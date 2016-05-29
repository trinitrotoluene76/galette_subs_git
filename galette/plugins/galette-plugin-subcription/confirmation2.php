<?php

/**
 * Confirmation 2 for Subscribtion plugin
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

//var_dump($_POST);

require_once 'includes/tarif.php';

$subscription= new Subscription;

if(isset($_POST['id_abn']))
	{
	$subscription->id_abn=$_POST['id_abn'];
	}
if(isset($_GET['id_abn']))
	{
	$subscription->id_abn=$_GET['id_abn'];
	}
$subscription->getSubscription($subscription);
//formatage de la date pour affichage
$date=DateTime::createFromFormat('Y-m-d', $subscription->date_demande);
$subscription->date_demande=$date->format('d-m-Y');

//retourner la liste des activités d'un abn
$followup=new Followup;
$followup->id_abn=$subscription->id_abn;
//$followup->id_abn=71;
//var_dump($followup->getFollowupAct($followup));

$res=array();
$res=$followup->getFollowupAct($followup);
$activities=array();

//calcul du total due:
$total=0.00;
//var_dump($category);

foreach ( $res as  $key => $value ) 
				{
				$activity=new Activity;
				$activity->id_group=$value;
				$activity->getActivity($activity);
				$activities[]=$activity;
				//var_dump($activity);
				
				$followup=new Followup;
				$followup->id_act=$activity->id_group;
				$followup->id_adh=$member->id;
				$followup->id_abn=$subscription->id_abn;
				$followup->getFollowup($followup);
				$followups[]=$followup;
				
				//si l'adhésion à m'activité est validé par le responsable de group statut_act == 1, alors on met à jour le total due
				if(	$followup->statut_act == 1)
					{
					switch ($category)
						{
						case 0:
						$total=$total+$activity->floatprice($activity->price1); break;
						case 1:
						$total=$total+$activity->floatprice($activity->price2); break;
						case 2:
						$total=$total+$activity->floatprice($activity->price3); break;
						case 3:
						$total=$total+$activity->floatprice($activity->price4); break;
						default:
						$total=$total+$activity->floatprice($activity->price4); break;
						}
					//var_dump($total);
					}//fin du if
				}//fin du foreach


$tpl->assign('page_title', _T("Follow up & Payment"));

//$tpl->assign('activities',$activities);
//var_dump ($activities);
		
$tpl->assign('subscription',$subscription);
//var_dump ($subscription);

$tpl->assign('activities',$activities);
//var_dump($activities);

$tpl->assign('followups',$followups);
//var_dump($followups);

$tpl->assign('category',$category);
//echo ('category: '.$category);

$tpl->assign('total',$total);
//echo ('total: '.$total);

//Set the path to the current plugin's templates,
//but backup main Galette's template path before
$orig_template_path = $tpl->template_dir;
$tpl->template_dir = 'templates/' . $preferences->pref_theme;

$content = $tpl->fetch('confirmation2.tpl', SUBSCRIPTION_SMARTY_PREFIX);
$tpl->assign('content', $content);
//Set path to main Galette's template
$tpl->template_dir = $orig_template_path;
$tpl->display('page.tpl', SUBSCRIPTION_SMARTY_PREFIX);
?>
