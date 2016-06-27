<?php

/**
 * Subs confirmation for Subscribtion plugin
 *
 * PHP version 5
 *
 * Copyright © 2013 The Galette Team
 *
 * This file is part of Galette (http://galette.eu).
 *
 * Galette is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Galette is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Galette. If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Plugins
 * @package   GaletteSubscribtion
 *
 * @author    Amaury FROMENT <amaury.froment@gmail.com>
 * @copyright 2011-2013 The Galette Team
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @version   0.7.8
 * @link      http://galette.tuxfamily.org
 * @since     Available since 0.7.8
 */
 
define('GALETTE_BASE_PATH', '../../');
require_once GALETTE_BASE_PATH . 'includes/galette.inc.php';
use Galette\Entity\Adherent as Adherent;
use Galette\Entity\Group as Group;
use Galette\Repository\Groups as Groups;
use Galette\Entity\DynamicFields as DynamicFields;
use Galette\DynamicFieldsTypes\DynamicFieldType as DynamicFieldType;
use Galette\Core\GaletteMail as GaletteMail;
use Galette\Entity\Texts;


if (!$login->isLogged()) {
    header('location: ' . GALETTE_BASE_PATH . 'index.php');
    die();
}
$id_adh = get_numeric_form_value('id_adh', '');

if ( !$login->isSuperAdmin() ) {
    if ( !$login->isAdmin() && !$login->isStaff() && !$login->isGroupManager()
        || $login->isAdmin() && $id_adh == ''
        || $login->isStaff() && $id_adh == ''
        || $login->isGroupManager() && $id_adh == ''
    ) {
        $id_adh = $login->id;
    }
}
require_once '_config.inc.php';

$member = new Adherent();
//on rempli l'Adhérent par ses caractéristiques à l'aide de son id
$member->load($id_adh);
//var_dump($member);
//début evol #55 MAJ date dernière connexion
$member->updateModificationDate();
//fin evol #55
//Fonction de traitement d'un string
//---------------------------------------------------------------------------------->
//between ('@', '.', 'biohazard@online.ge');
    function after ($this, $inthat)
    {
        if (!is_bool(strpos($inthat, $this)))
        return substr($inthat, strpos($inthat,$this)+strlen($this));
    };

    function after_last ($this, $inthat)
    {
        if (!is_bool(strrevpos($inthat, $this)))
        return substr($inthat, strrevpos($inthat, $this)+strlen($this));
    };

    function before ($this, $inthat)
    {
        return substr($inthat, 0, strpos($inthat, $this));
    };

    function before_last ($this, $inthat)
    {
        return substr($inthat, 0, strrevpos($inthat, $this));
    };

    function between ($this, $that, $inthat)
    {
        return before ($that, after($this, $inthat));
    };

    function between_last ($this, $that, $inthat)
    {
     return after_last($this, before_last($that, $inthat));
    };

// use strrevpos function in case your php version does not include it
function strrevpos($instr, $needle)
{
    $rev_pos = strpos (strrev($instr), strrev($needle));
    if ($rev_pos===false) return false;
    else return strlen($instr) - $rev_pos - strlen($needle);
};
//---------------------------------------------------------------------------------->Fin fonction traitement string
//var_dump(between("_", "_", "timestamp_5_1")); //5=id_group
//var_dump(after_last( "_", "timestamp_5_1"));//1=doc N°1

//var_dump($_POST);
//traitement des données envoyées en POST
//---------------------------------------------------------------------------------->
$total=$_POST['total_estimme'];
//var_dump($_POST);
$subscription= new Subscription;
$today= new DateTime("now");
//var_dump($today->format('d-m-Y'));
$subscription->date_demande=$today->format('Y-m-d');
$subscription->total_estimme=$total;
$subscription->id_adh=$id_adh;
$subscription->message_abn=$_POST['message_abn'];
$valid = $subscription->check($_POST);

	//var_dump ($valid);
				
//si les données sont valides on les enregistres -> continuer à bosser cette partie
	if ( $valid == true )
		{
		$subscription->store();
		//echo('$subscription->store():');	
		//var_dump($subscription->store());	
		//echo('$subscription->is_id_abn($subscription)');	
		//var_dump($subscription->is_id_abn($subscription));
		}
$files=array ();
// $file[id_group][$k]
foreach ($_POST as $key => $value) 
		{
		//var_dump($key);
		//si id_group(0, 1, 2)=id_group alors $value=id_group
		if(substr($key,0,8)=='id_group')
			{
			//var_dump(substr($key,0,8));
			$followups[$value]=new Followup;
			$followups[$value]->id_act=$value;
			$followups[$value]->id_adh=$id_adh;
			$followups[$value]->id_abn=$subscription->id_abn;
			
			
			//création d'une activité pour évaluer son autovalidation
			$activity= new Activity;
			$activity->id_group=$value;
			//hydrate l'activité
			$activity->getActivity($activity);
			if($activity->autovalidation == 1)
						{
						//var_dump ($activity);
			
						//statut activity = Validé (1)
						$followups[$value]->statut_act=1;
						//enregistrement afin d'utiliser getStatusSub
						$followups[$value]->store();
			
						//recherche et calcul du statut abn
						$statut_abn=$followups[$value]->getStatusSub($subscription->id_abn);
						//var_dump ($statut_abn);
						}
					else
						{//Statut abn = en cours
						$statut_abn=0;
						}
			
			
			//---------------------------------------------------------------->ENVOI de l'email de confirmation
			if($preferences->pref_mail_method != GaletteMail::METHOD_DISABLED)
				{
				//Pour chaque activité concernée, on récupère les managers avec id_act et on leur envoi un mail
				$group = new Group();
				$group->load($value);//value = id_group
				//affiche le nom,tel,mail du manager de group + pour changer la manager de group, allez dans la page gestion group
				$managers=array();
				$managers=$group->getManagers();
				//var_dump($managers);
				
				//récupération des infos de l'adhérent à valider
				$adherent=new Adherent();
				$adherent->load($id_adh);
				//var_dump($adherent->surname." ".$adherent->name);
				
				foreach ($managers as $key => $manager) 
					{
					//var_dump($manager->email);
					//var_dump($manager->surname." ".$manager->name);
					$sname=$manager->surname." ".$manager->name;
					
					if ( GaletteMail::isValidEmail($manager->email) ) 
						{
						$mail = new GaletteMail();
						$mail->setSubject("[".$preferences->pref_nom."] Nouvelle inscription pour la section ".$group->getName());
						$mail->setRecipients(
							array(
								$manager->email => $sname
							)
						);
						$proto = 'http';
						if ( isset($_SERVER['HTTPS']) ) {
							$proto = 'https';
						}
						
						$mail->html=true;
						$mail->setMessage("Bonjour,\r\n\r\n"."connectez vous sur Galette pour valider la nouvelle inscription de ".$adherent->surname." ".$adherent->name." pour la section ".$group->getName().".\r\n"."Allez dans l'onglet Abonnement/Gestion des abonnés. L'abonnement concerné est le N°".$subscription->id_abn.".\r\n".$proto . '://' . $_SERVER['SERVER_NAME'] .dirname($_SERVER['REQUEST_URI'])."/management_subs.php\r\n\r\n"."Cordialement,\r\n"."le bureau.");
						//var_dump($mail);
						//envoi de l'email:
						$sent = $mail->send();
						//var_dump($preferences->pref_mail_method);
											
						}//fin du if
					}//fin pour chaque manager	
				 }//fin du if 
					
			//------------------------------------------------------------------>Fin envoi email


			}//fin du if id group
			
		//si message_adh_act(id_group)=message_adh_act
		if(substr($key,0,15)=='message_adh_act')
			{
			//var_dump(substr($key,15,3));
			//substr($key,15,3)=id_group
			$followups[substr($key,15,3)]->message_adh_act=$value;
			$followups[substr($key,15,3)]->store();
			}//fin du if message
		
		//si POST['description'_id_act_N°]=description_5_1
		if(substr($key,0,11)=='description')
			{
			$id_group=between("_", "_", $key); //5=id_group
			$number=after_last( "_", $key);//1=doc N°1

			//création de l'objet file pour description
			$files[$id_group][$number]= new File();
			//hydratation (les id_act et _id_adh son remplis par l'upload_process.php)
			$files[$id_group][$number]->id_abn=$subscription->id_abn;
			
			
			$files[$id_group][$number]->description=$value;
			}//fin du if description
			
		//si POST['timestamp'_id_act_N°]=timestamp
		if(substr($key,0,9)=='timestamp')
			{
			$files[$id_group][$number]->emplacement=$value;
			//var_dump($files[substr($key,10,3)]);
			$res=$files[$id_group][$number]->getFileDesc($files[$id_group][$number]);
			if($res == 1)
				{
				$files[$id_group][$number]->store();
				}
			}
		
		}//fin du foreach
		//var_dump($followups);
//------------------------------------------------------------------------------------>FIN	du traitement du $_POST

$tpl->assign('page_title', _T("Validation"));

//var_dump ($activity);
		
$tpl->assign('subscription',$subscription);
//var_dump ($subscription);

$tpl->assign('statut_abn',$statut_abn);
//var_dump ($statut_abn);

//Set the path to the current plugin's templates,
//but backup main Galette's template path before
$orig_template_path = $tpl->template_dir;
$tpl->template_dir = 'templates/' . $preferences->pref_theme;

$content = $tpl->fetch('subs_confirmation.tpl', SUBSCRIPTION_SMARTY_PREFIX);
$tpl->assign('content', $content);
//Set path to main Galette's template
$tpl->template_dir = $orig_template_path;
$tpl->display('page.tpl', SUBSCRIPTION_SMARTY_PREFIX);
?>
