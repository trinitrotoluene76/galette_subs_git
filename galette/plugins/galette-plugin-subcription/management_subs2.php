<?php

/**
 * management subs 2 for Subscribtion plugin
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


	
//var_dump($_GET);
if(isset($_GET['id_abn']))
	{
	$id_adh2=$_GET['id_adh'];
	$id_abn2=$_GET['id_abn'];
	$id_act2=$_GET['id_act'];
	}

//enregistrement des données
//var_dump($_POST);
$valid=0;
if(isset($_POST['valid']))
	{
	$followup4= new Followup;
	$followup4->id_act=$id_act2;
	$followup4->id_abn=$id_abn2;
	$followup4->id_adh=$id_adh2;
	$followup4->getFollowup($followup4);
	$followup4->feedback_act=$_POST['feedback_act'];
	$followup4->feedback_act_off=$_POST['feedback_act_off'];
	$followup4->statut_act=$_POST['statut_act'];
	$followup4->store($followup4);
	//si le statut est payé=2 ou validé=1 on ajoute le membre dans le group correspondant
	if($followup4->statut_act == 1 || $followup4->statut_act == 2)
		{
		$valid=$followup4->put_into_group($followup4);
		}
	//var_dump($followup4);
	
	//si l'activité est validé, payé ou refusé, on envoi un mail à l'adhérent
	if($followup4->statut_act == 1|| $followup4->statut_act == 2 || $followup4->statut_act == 3)
		{
		//---------------------------------------------------------------->ENVOI de l'email
			if($preferences->pref_mail_method != GaletteMail::METHOD_DISABLED)
				{
				//Pour chaque activité concernée, on récupère les managers avec id_act et on leur envoi un mail
				$group = new Group();
				$group->load($id_act2);//value = id_group
				
				//récupération des infos de l'adhérent à valider
				$adherent=new Adherent();
				$adherent->load($id_adh2);
				//var_dump($adherent->surname." ".$adherent->name);
				$sname1=$adherent->surname." ".$adherent->name;
				
				//les managers sont cités en signature du mail
				$managers=array();
				$managers=$group->getManagers();
				$sname="";
				foreach ( $managers as  $key => $manager ) 
				{
				$sname=$sname.$manager->surname." ".$manager->name." mail: ".$manager->email."\r\n";
				}
									
					if ( GaletteMail::isValidEmail($adherent->email) ) 
						{
						$mail = new GaletteMail();
						if($followup4->statut_act == 1)
							{
							$mail->setSubject("[".$preferences->pref_nom."] inscription pour la section ".$group->getName()." validé");
							}
						if($followup4->statut_act == 2)
							{
							$mail->setSubject("[".$preferences->pref_nom."] inscription pour la section ".$group->getName()." payé");
							}
						if($followup4->statut_act == 3)
							{
							$mail->setSubject("[".$preferences->pref_nom."] inscription pour la section ".$group->getName()." refusé");
							}
						$mail->setRecipients(
							array(
								$adherent->email => $sname1
							)
						);
						$proto = 'http';
						if ( isset($_SERVER['HTTPS']) ) {
							$proto = 'https';
						}
						
						$mail->html=true;
						if($followup4->statut_act == 1)
							{
							$mail->setMessage("Bonjour,\r\n\r\n"."Votre inscription pour la section ".$group->getName()." est maintenant validée.\r\n"."L'abonnement concerné est le N°".$followup4->id_abn.".\r\n"."Vous pouvez payer la section concernée".".\r\n"."Pour voir le suivi de vos abonnements et les montants à payer, connectez vous à l'adresse suivante (Abonnement/suivi): ".$proto . '://' . $_SERVER['SERVER_NAME'] .dirname($_SERVER['REQUEST_URI'])."/follow_up_subs.php\r\n\r\n"."Pour les questions ou réclamations, ne répondez pas à ce mail mais contactez vos responsables de section:\r\n".$sname."\r\n\r\n"."Cordialement,\r\n"."le bureau.");
							}
						if($followup4->statut_act == 2)
							{
							$mail->setMessage("Bonjour,\r\n\r\n"."Votre inscription pour la section ".$group->getName()." est maintenant payée.\r\n"."L'abonnement concerné est le N°".$followup4->id_abn.".\r\n\r\n"."Pour voir le suivi de vos abonnements et les montants à payer, connectez vous à l'adresse suivante (Abonnement/suivi): ".$proto . '://' . $_SERVER['SERVER_NAME'] .dirname($_SERVER['REQUEST_URI'])."/follow_up_subs.php\r\n\r\n"."Pour les questions ou réclamations, ne répondez pas à ce mail mais contactez vos responsables de section:\r\n".$sname."\r\n\r\n"."Cordialement,\r\n"."le bureau.");
							}
						if($followup4->statut_act == 3)
							{
							$mail->setMessage("Bonjour,\r\n\r\n"."Votre inscription pour la section ".$group->getName()." a été refusée par le responsable de section ou le bureau.\r\n"."L'abonnement concerné est le N°".$followup4->id_abn.".\r\n\r\n"."Pour voir le suivi de vos abonnements et pour connaitre le motif du refus, connectez vous à l'adresse suivante (Abonnement/suivi): ".$proto . '://' . $_SERVER['SERVER_NAME'] .dirname($_SERVER['REQUEST_URI'])."/follow_up_subs.php\r\n\r\n"."Pour les questions ou réclamations, ne répondez pas à ce mail mais contactez vos responsables de section:\r\n".$sname."\r\n\r\n"."Cordialement,\r\n"."le bureau.");
							}
							
						//var_dump($mail);
						//envoi de l'email:
						$sent = $mail->send();
						//var_dump($preferences->pref_mail_method);
											
						}//fin du if	
				 }//fin du if 
					
			//------------------------------------------------------------------>Fin envoi email
		}
	
	}//fin du isset valid
	
$member = new Adherent();
//on rempli l'Adhérent par ses caractéristiques à l'aide de son id
$member->load($id_adh2);
//var_dump($member);

//check si l'adhérent a une photo (0/1)
//var_dump($member->picture->hasPicture());
$picture=$member->picture->hasPicture();


require_once 'includes/tarif.php';


$subscription= new Subscription;
$subscription->id_abn=$id_abn2;
$subscription->getSubscription($subscription);
//formatage de la date pour affichage
$date=DateTime::createFromFormat('Y-m-d', $subscription->date_demande);
$subscription->date_demande=$date->format('d-m-Y');

$followup= new Followup();
$followup->id_act=$id_act2;
$followup->id_abn=$id_abn2;
$followup->id_adh=$id_adh2;
$followup->getFollowup($followup);

//retourner la liste des activités d'un abn
	$activities=array();
	$followups=array();
	$followup2=new Followup;
	$followup2->id_abn=$id_abn2;
	$res=$followup2->getFollowupAct($followup2);
	$total_statut_ref=array();//sert à calculer le statut abn si refus
	$statut_abn=0; //0 orange, 1 vert, 2 rouge
	//var_dump($res);
	//pour chaque suivi d'activité
	foreach ( $res as  $key => $value ) 
		{
		$activity2=new Activity;
		$activity2->id_group=$value;
		$activity2->getActivity($activity2);
		$activities[$value]=$activity2;
		//var_dump($activity2);
		
		$followup3=new Followup;
		$followup3->id_act=$value;
		$followup3->id_adh=$id_adh2;
		$followup3->id_abn=$id_abn2;
		$followup3->getFollowup($followup3);
		$followups[$value]=$followup3;
		
		//4 statuts act : en cours, validé, payé, refusé -> 3 couleurs statut_abn (orange, rouge, vert)
					//0=en cours
					//1=validé
					//2=payé
					//3=refusé
					
			//si tout est payé statut_abn=vert, 
			//si au moins 1 est en cours ou validé et aucun refus statut_abn=orange
			//si au moins 1 est refusé statut_abn=rouge
			//0 orange, 1 vert, 2 rouge
			$total_statut=0;
			foreach ( $followups as  $key => $followup4 ) 
				{
				$total_statut=$total_statut+$followup4->statut_act;
				$total_statut_ref[]=$followup4->statut_act;
				}
			//var_dump(2*count($followups));
			//var_dump($total_statut);
			
			//si toutes les activités sont payées	
			if($total_statut==2*count($followups))
				{
				$statut_abn=1;
				}
			
			//si il y a au moins un refus	
			if(max($total_statut_ref)==3)
				{
				$statut_abn=2;
				}
			if(max($total_statut_ref)!=3 && $total_statut!=2*count($followups))
				{
				$statut_abn=0;
				}
		}//fin du foreach activity

		
$tpl->assign('page_title', _T("Management of Subscribers"));

$tpl->assign('subscription',$subscription);
//var_dump ($subscription);

$tpl->assign('followup',$followup);
//var_dump($followup);

//non utilisé pour l'instant
$tpl->assign('category',$category);
//echo ('category: '.$category);

$tpl->assign('member',$member);
$tpl->assign('statut',$statut);
$tpl->assign('age',$age);

$tpl->assign('picture',$picture);
//var_dump($picture);

$tpl->assign('activities',$activities);
//var_dump($activities);
$tpl->assign('followups',$followups);
//var_dump($followups);
$tpl->assign('statut_abn',$statut_abn);
//var_dump($statut_abn);

$tpl->assign('valid',$valid);

//Set the path to the current plugin's templates,
//but backup main Galette's template path before
$orig_template_path = $tpl->template_dir;
$tpl->template_dir = 'templates/' . $preferences->pref_theme;

$content = $tpl->fetch('management_subs2.tpl', SUBSCRIPTION_SMARTY_PREFIX);
$tpl->assign('content', $content);
//Set path to main Galette's template
$tpl->template_dir = $orig_template_path;
$tpl->display('page.tpl', SUBSCRIPTION_SMARTY_PREFIX);
?>
