<?php

/**
 * Send files for Subscribtion plugin
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

//check si l'adhérent a une photo (0/1)
//var_dump($member->picture->hasPicture());
$picture=$member->picture->hasPicture();

//on charge tous les groupes. (j'ai une erreur qui n'empeche pas le fonctionnement mais qui apparait dans le log "Non-static method Activity::getList() should not be called statically"
	$group=Activity::getList(true);
	//var_dump ($group);
	
	$activities=array();//=$activity0, $activity1, $activity2...
	$files_vierges=array();//files vierges
	foreach ($group as $key => $value) 
		{
			//echo('echo_POST:');
			if(isset($_POST['total']))
				{
				//var_dump($_POST);
				//parsage de tout ce qui est POSTé
				foreach ( $_POST as $k_post=>$val_post ) 
					{
					//echo('k:');
					//si case coché=id_group,on créé une nouvelle activity, ou si l'activité est une activité parente
					if($k_post==$group[$key]->getId() || ($k_post=="id_parent_group_checked" && $val_post==$group[$key]->getId()))
						{
						//création de l'objet activité
						$activity = new Activity();
						//récupération de l'id_group ainsi que le nom du groupe managé
						$activity->id_group = $group[$key]->getId();
						//$activity->group_name = $group[$key]->getName();

						//hydrate l'activité avec les données de la bdd. L'objet passé en paramètre doit être une activité avec un id_group valide
						$activity->getActivity($activity);
						//var_dump ($activity);
						$activities[$key]=$activity;
						
						//chaque activité comporte 0 ou plusieurs fichiers vierges
						$file=new File();
						$file->id_act=$activity->id_group;
						//var_dump($file);
						$files_vierges[$activity->id_group]=$file->getFileListVierge();
						}
						
					}//fin du foreach post
						
				$total=$_POST['total'];
				//var_dump($total);
				}//fin du isset	
			
		}//fin du foreach
//var_dump($_POST);
//var_dump ($activities);
$subscription= new Subscription;
$subscription->total_estimme=$total;
$subscription->id_adh=$id_adh;

//2 utilisateurs du formulaire doivent avoir 0.1s minimum de différence 
//entre le chargement de leur pagepour avoir un nom de fichier différent
//temps en s*10 (centieme de s)
$timestamp=intval(microtime(true)*10-13992330840);
		
$tpl->assign('page_title', _T("Send Files"));

$tpl->assign('activities',$activities);
//var_dump ($activities);
		
$tpl->assign('picture',$picture);
$tpl->assign('timestamp',$timestamp);
$tpl->assign('subscription',$subscription);
//var_dump ($subscription);
$tpl->assign('files_vierges',$files_vierges);
//var_dump ($files_vierges);

//Set the path to the current plugin's templates,
//but backup main Galette's template path before
$orig_template_path = $tpl->template_dir;
$tpl->template_dir = 'templates/' . $preferences->pref_theme;

$content = $tpl->fetch('send_files.tpl', SUBSCRIPTION_SMARTY_PREFIX);
$tpl->assign('content', $content);
//Set path to main Galette's template
$tpl->template_dir = $orig_template_path;
$tpl->display('page.tpl', SUBSCRIPTION_SMARTY_PREFIX);
?>
