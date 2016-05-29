<?php
/**
 * Upload process for Subscribtion plugin
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
require_once '_config.inc.php';

//upload du fichier dans le répertoire définir dans l'animation flash
if (isset($_FILES['Filedata'])) 
   { 
	$chemin=$_GET['chemin'];
      $path_parts = pathinfo($chemin);

        $uploadfile = './'.$path_parts['dirname']. '/'.basename( $_FILES['Filedata']['name'] ); 
      
        if ( move_uploaded_file( $_FILES['Filedata']['tmp_name'], $uploadfile ) )  
                { 
                     rename($uploadfile, $chemin);
					 
                     return true ; 
                } 
                else  return false ; 
    }  
      //else return false ; 
	  
//supression du fichier après avoir cliqué sur "supprimer"	dans l'animation flash  
if(isset($_POST['supp']))
	{
	if($_POST['supp']==1)
		{
		$nom=utf8_decode($_POST['id_name']);
		$path="./upload/files/".$nom;
		if(file_exists($path))
			{
			$file= new File();
			$res=0;
			$res=$file->remove($path,$nom);
			if($res==1)
				{
				echo("&res=".$res."&");
				}
			
			}
		}
	
	}

//enregistrement du fichier dans la bdd
if(isset($_POST['store']))
	{
	if($_POST['store']==1)
		{
		$file= new File();
		$file->doc_name=utf8_decode($_POST['doc_name']);
		$file->doc_name=$_POST['doc_name'];
		//$file->doc_name=utf8_encode($_POST['doc_name']); ne fonctionne pas car rajoute un Ãƒ avant l'encodage
		//$file->doc_name=utf8_encode("Copie de Main courante Procédure Gestion des Faits Technique");
		
		//$file->doc_name=htmlentities(utf8_decode($_POST['doc_name'])); ne fonctionne pas dans la bdd il y a un blanc
		$file->emplacement=$_POST['id_name'];
		//on a besoin des id pour la méthode getFileDesc
		$file->id_act=$_POST['id_act'];
		$file->id_adh=$_POST['id_adh'];
		$today= new DateTime("now");
		//var_dump($today->format('d-m-Y'));
		$file->date_record=$today->format('Y-m-d');
		$file->store();
		echo("&monStore=".utf8_encode(1)."&");
		}
		
	}
	// $file= new File();
	//var_dump($file);
	//$file->doc_name="doc.txt";
	//$file->emplacement="187162.txt";
	/* $today= new DateTime("now");
		//var_dump($today->format('d-m-Y'));
	$file->date_record=$today->format('Y-m-d');
	$file->store();  */
	//$file->remove(utf8_encode('186202'));
	
//var_dump($file);	

?> 
