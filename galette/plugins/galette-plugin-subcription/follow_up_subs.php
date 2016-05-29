<?php

/**
 * Follow up subs for Subscribtion plugin
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

$subscription= new Subscription;
$subscription->id_adh=$id_adh;

//recherche de la liste des abonnements de l'adhérent
$result=array();
$result=$subscription->getSubscriptionList($subscription);
//var_dump($result);

$subscriptions=array();
$activities=array();
$res=array();
$followups=array();
$total_statut_ref=array();//sert à calculer le statut abn si refus
$statut_abn=0; //0 orange, 1 vert, 2 rouge
$statut_abns=array();//$statut_abns[id_abn]=$statut_abn

//pour chaque abonnement
foreach ( $result as  $key => $value ) 
	{
	$subscription= new Subscription;
	$subscription->id_abn=$value;
	$subscription->getSubscription($subscription);
	//formatage de la date pour affichage
	$date=DateTime::createFromFormat('Y-m-d', $subscription->date_demande);
	$subscription->date_demande=$date->format('d-m-Y');
	
	//retourner la liste des activités d'un abn
	$followup=new Followup;
	$followup->id_abn=$subscription->id_abn;
	$res=$followup->getFollowupAct($followup);
	//pour chaque suivi d'activité
	foreach ( $res as  $key => $value2 ) 
		{
		$activity=new Activity;
		$activity->id_group=$value2;
		$activity->getActivity($activity);
		$activities[$subscription->id_abn][]=$activity;
		//var_dump($activity);
		
		$followup=new Followup;
		$followup->id_act=$activity->id_group;
		$followup->id_adh=$member->id;
		$followup->id_abn=$subscription->id_abn;
		$followup->getFollowup($followup);
		$followups[$subscription->id_abn][]=$followup;
			
		}//fin du foreach activity
		
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
		unset($total_statut_ref);
		foreach ( $followups[$subscription->id_abn] as  $key => $followup ) 
			{
			$total_statut=$total_statut+$followup->statut_act;
			$total_statut_ref[]=$followup->statut_act;
			}
		//var_dump(2*count($followups));
		//var_dump($followup->statut_act);
		//var_dump($total_statut);
		
		//si toutes les activités sont payées (alors la somme des statuts= 2x nb d'activités dans l'abn)
		if($total_statut==2*count($followups[$subscription->id_abn]))
			{
			$statut_abns[$subscription->id_abn]=1;
			}
		
		//si il y a au moins un refus (alors le max du tableau des statuts de l'abn = 3)
		if(max($total_statut_ref)==3)
			{
			$statut_abns[$subscription->id_abn]=2;
			}
		if(max($total_statut_ref)!=3 && $total_statut!=2*count($followups[$subscription->id_abn]))
			{
			$statut_abns[$subscription->id_abn]=0;
			}
	//var_dump(count($followups));
	$subscriptions[]=$subscription;
	}//fin du foreach subscription

//var_dump($statut_abns);

$tpl->assign('page_title', _T("Follow up Subscriptions"));
		
$tpl->assign('subscriptions',$subscriptions);
//var_dump ($subscriptions);

$tpl->assign('activities',$activities);
//var_dump($activities);

$tpl->assign('statut_abns',$statut_abns);
//var_dump($statut_abns[127]);

//Set the path to the current plugin's templates,
//but backup main Galette's template path before
$orig_template_path = $tpl->template_dir;
$tpl->template_dir = 'templates/' . $preferences->pref_theme;

$content = $tpl->fetch('follow_up_subs.tpl', SUBSCRIPTION_SMARTY_PREFIX);
$tpl->assign('content', $content);
//Set path to main Galette's template
$tpl->template_dir = $orig_template_path;
$tpl->display('page.tpl', SUBSCRIPTION_SMARTY_PREFIX);
?>
