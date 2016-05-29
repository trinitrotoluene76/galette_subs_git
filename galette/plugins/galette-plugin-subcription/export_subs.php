<?php

/**
 * Export for Subscribtion plugin
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
use Galette\IO\Csv;
use Galette\IO\CsvOut;
use Galette\Entity\FieldsConfig;
use Galette\Repository\Members;

$csv = new CsvOut();
$written = array();
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

//début création du fichier d'export
//récupération du id_act
if(isset($_GET['id_act']))
{
$id_act=$_GET['id_act'];
//var_dump($id_act);
}
else
{
$id_act=0;
}

//Liste des abonnements de la muscu (avec l'appartenance et le statut de l'abn):
$select = new Zend_Db_Select($zdb->db);
        $select->from(array('f2' => 'galette_subscription_followup'),array('f2.*'))
		->join(array('d' => 'galette_dynamic_fields'), 'd.item_id=f2.id_adh',array())
		->join(array('f' => 'galette_field_contents_5'), 'f.id=d.field_val' ,array('f.val'))
		->join(array('a' => 'galette_adherents'), 'a.id_adh=f2.id_adh' ,array('a.*'))
		->where('f2.id_act = ?', $id_act)
		->where('d.field_id = ?', '5')
		->group('f2.id_abn');
    $result = $select->query()->fetchAll(Zend_Db::FETCH_ASSOC);
 
        if ( count($result) > 0 ) {
            $filename ='liste_abn_appartenance_statut_'.$id_act.'.csv';
            $filepath = CsvOut::DEFAULT_DIRECTORY . $filename;
            $fp = fopen($filepath, 'w');
            if ( $fp ) {
                $res = $csv->export(
                    $result,
                    Csv::DEFAULT_SEPARATOR,
                    Csv::DEFAULT_QUOTE,
                    true,
                    $fp
                );
                fclose($fp);
                $written[] = array(
                    'name' => $filename,
                    'file' => $filepath
                );
            }
		}

//Liste des adhérents de la section (avec l'appartenance et le statut de l'abn):
$select = new \Zend_Db_Select($zdb->db);
        $select->from(array('a' => 'galette_adherents'),array('a.*'))
		->join(array('g' => 'galette_groups_members'), 'g.id_adh=a.id_adh',array())
		->join(array('d' => 'galette_dynamic_fields'), 'd.item_id=a.id_adh',array())
		->join(array('f' => 'galette_field_contents_5'), 'f.id=d.field_val' ,array('f.val'))
		->where('g.id_group = ?', $id_act)
		->where('d.field_id = ?', '5')
		->group('a.nom_adh');
    $result = $select->query()->fetchAll(Zend_Db::FETCH_ASSOC);
 
        if ( count($result) > 0 ) {
            $filename ='liste_adherents_appartenance_'.$id_act.'.csv';
            $filepath = CsvOut::DEFAULT_DIRECTORY . $filename;
            $fp = fopen($filepath, 'w');
            if ( $fp ) {
                $res = $csv->export(
                    $result,
                    Csv::DEFAULT_SEPARATOR,
                    Csv::DEFAULT_QUOTE,
                    true,
                    $fp
                );
                fclose($fp);
                $written[] = array(
                    'name' => $filename,
                    'file' => $filepath
                );
            }
		}
//-------->fin création du fichier


$tpl->assign('page_title', _T("Export"));

$tpl->assign('id_act',$id_act);
		
//Set the path to the current plugin's templates,
//but backup main Galette's template path before
$orig_template_path = $tpl->template_dir;
$tpl->template_dir = 'templates/' . $preferences->pref_theme;

$content = $tpl->fetch('export_subs.tpl', SUBSCRIPTION_SMARTY_PREFIX);
$tpl->assign('content', $content);
//Set path to main Galette's template
$tpl->template_dir = $orig_template_path;
$tpl->display('page.tpl', SUBSCRIPTION_SMARTY_PREFIX);
?>