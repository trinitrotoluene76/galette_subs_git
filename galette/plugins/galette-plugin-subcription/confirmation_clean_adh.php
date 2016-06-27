<?php

/**
 * Confirmation clean adherent for Subscribtion plugin
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
use Galette\Filters\MembersList as MembersList;//#evol 55
use Galette\Repository\Members as Members;//evol #55
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


		//evol#55 affichage des adherents non abonnés depuis 2 ans (staff et managers de group exclus)
	
		 $members=array();
		 $filters = new MembersList();
		 $members0 = new Members($filters);
		 $adherent_del=new Adherent();
		 $members_list = $members0->getMembersList(1,null,0,0,0,0,0);
		//var_dump($members_list);
		 //creation d'une boucle
		 foreach ( $members_list as  $keydel => $valuedel ) 
			{
			 $adherent_del=$members_list[$keydel];
				 
			if($adherent_del->isStaff2()==false)
				{
				//var_dump($adherent_del->modification_date);
				 
				$lastsubsdate= \DateTime::createFromFormat('j/m/Y',$adherent_del->modification_date);
				$today= new \DateTime("now");
				//echo('diff:');
				$elapse=$lastsubsdate->diff($today);
				//var_dump($elapse->format('%R%Y'));
				$elapse=$elapse->format('%Y');
				if( $elapse>=1)
				//if( $elapse>=2)
					{
					$members[$keydel]=$adherent_del;
					}
				}//fin if staff
			}//fin foreach
				

$tpl->assign('page_title', _T("Confirmation to clean members"));
$tpl->assign('members', $members);
//var_dump($members);

//Set the path to the current plugin's templates,
//but backup main Galette's template path before
$orig_template_path = $tpl->template_dir;
$tpl->template_dir = 'templates/' . $preferences->pref_theme;

$content = $tpl->fetch('confirmation_clean_adh.tpl', SUBSCRIPTION_SMARTY_PREFIX);
$tpl->assign('content', $content);
//Set path to main Galette's template
$tpl->template_dir = $orig_template_path;
$tpl->display('page.tpl', SUBSCRIPTION_SMARTY_PREFIX);
?>
