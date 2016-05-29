<?php

/**
 * File class for Subscribtion plugin
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
 
class File {

    const TABLE = 'files';
    //clé primaire de la table files
	const PK = 'id_doc';

	//champs de la table files de la bdd
    private $_fields = array(
        '_id_doc' => 'integer',
        '_id_act' => 'integer',
        '_id_adh' => 'integer',
        '_id_abn' => 'integer',
        '_doc_name' => 'varchar(200)',
        '_description' => 'varchar(200)',
        '_emplacement' => 'varchar(200)',
        '_date_record' => 'datetime',
		'_vierge' => 'integer',
		'_return_file' => 'integer'
    );
	
	//variables le l'objet
    private $_id_doc;
	private $_id_act;
	private $_id_adh;
	private $_id_abn;
	private $_doc_name='';
	private $_description='';
	private $_emplacement='';
	private $_date_record='';
	private $_vierge=0;//0= document quelconque / 1=document provenant de la page de gestion des groupes
	private $_return_file=0;//0= document informatif / 1=formulaire à télécharger, remplir et à retourner
	    
    

    /**
     * Construit une nouvellle activité à partir de la BDD (à partir de son ID) ou vierge
     * 
     * @param int|object $args Peut être null, un ID ou une ligne de la BDD
     */
    public function __construct($args = null) {
        global $zdb;

        if (is_int($args)) {
            try {
                $select = new Zend_Db_Select($zdb->db);
                $select->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
                        ->where(self::PK . ' = ' . $args);
                if ($select->query()->rowCount() == 1) {
                    $this->_loadFromRS($select->query()->fetch());
                }
                
            } catch (Exception $e) {
                Analog\Analog::log(
                        'Something went wrong :\'( | ' . $e->getMessage() . "\n" .
                        $e->getTraceAsString(), Analog\Analog::ERROR
                );
            }
        } else if (is_object($args)) {
            $this->_loadFromRS($args);
        }
    }

     /**
     * Populate object from a resultset row
     *
     * @param ResultSet $r the resultset row
     *
     * @return void
     */
    private function _loadFromRS($r)
    {
        $this->_id_doc = $r->id_doc;
		$this->_id_act= $r->id_act;
		$this->_id_adh= $r->id_adh;
		$this->_id_abn= $r->id_abn;
		$this->_doc_name=$r->doc_name;
		$this->_description= $r->description;
		$this->_emplacement= $r->emplacement;
		$this->_date_record= $r->date_record;
		$this->_vierge= $r->vierge;
		$this->_return_file= $r->return_file;
        
    }

    /**
     * Protège les guillemets et apostrophes en les transformant en caractères qui ne gênent pas en HTML
     * 
     * @param string $str Chaîne à transformée
     * 
     * @return string Chaîne protégée
     */
    private static function protectQuote($str) {
        return str_replace(array('\'', '"'), array('’', '“'), $str);
    }

  
    /**
	 * Exécute une requête SQL pour savoir si l'id_doc à créer est valide (est présent dans la table file)
     * Retourne 1 si l’id existe dans la table file->update
	 * 0 s’il n’y est pas
	 * $result=0
	 *
     * @param 
     */
    public function is_id_doc() 
		{
		global $zdb;
		$result=0;
		if($this->_id_doc!=NULL)
			{
			//var_dump($this->_id_doc);
			$req1 = new Zend_Db_Select($zdb->db);
			$req1->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
					->where('id_doc = ?', $this->_id_doc)
					->limit(1, 0);
					
			if ($req1->query()->rowCount() == 1) 
				{
				$result=1;
				}//fin du 1er if
			}
		return $result;
		}//fin de la fonction
	


  /** 
     * Enregistre l'élément en cours que ce soit en insert ou update
     * 
     * @return bool False si l'enregistrement a échoué, true si aucune erreur
     */
    public function store() {
        global $zdb;

        try {
            $values = array();

            foreach ($this->_fields as $k => $v) {
                $values[substr($k, 1)] = $this->$k;
            }
			$res=$this->is_id_doc();
            if ($res=='0') 
				{
				$add = $zdb->db->insert(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE, $values);
				//Analog\Analog::log('insert file');
				if ($add > 0) 
					{
						$this->_id_doc = $zdb->db->lastInsertId();
					} else {
							throw new Exception(_T("file.AJOUT ECHEC"));
							}
				} 
			if ($res=='1') 
				{
				
				//Analog\Analog::log('update file');
				$edit = $zdb->db->update(
				PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE, $values, self::PK . '=' . $this->_id_doc
										);
				}else {
								throw new Exception(_T("file.AJOUT ECHEC"));
								}
            return true;
			} catch (Exception $e) {
					Analog\Analog::log(
                    'Something went wrong :\'( | ' . $e->getMessage() . "\n" .
                    $e->getTraceAsString(), Analog\Analog::ERROR
					);
            return false;
        }
    }

  /**
     * supprime le doc de la table file et de son emplacement sur le serveur
     *
     * @param path le chemin + nom du fichier à supprimer
	 *		  nom le nom du fichier en question
	 *		  clean si on souhaite nettoyer le répertoire et la bdd (à réserver pour le staff pour éviter les requetes inutiles)
	 * Return 1 si ok, 0 sinon
	 * exemple $file_del->remove("./upload/files/".$file_del->emplacement,$file_del->emplacement);
     */
	 
	 static function remove($path,$nom,$clean) {
	 
        global $zdb;
		 $where=array(
					"emplacement='".$nom."'"
					); 
					
        $res2=$zdb->db->delete(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE, $where);
        
		if(file_exists($path))
			{
			$res1=unlink($path);
			$res=0;
			if($res1==1 && $res2==1)
				{
				$res=1;
				//echo("&res=".$res."&");
				}
			
			}
		if($clean == 1)
			{
			$file=new File();
			$file->clean_file();
			}
		
		return $res;
    }
  /**
     * Exactement la même fonction que "remove" sans l'appel de cleanfile ni listrep pour éviter les boucles infinies
     * supprime le doc de la table file et de son emplacement sur le serveur
     *
     * @param path le chemin + nom du fichier à supprimer
	 *		  nom le nom du fichier en question
	 * Return 1 si ok, 0 sinon
	 * exemple $file_del->remove("./upload/files/".$file_del->emplacement,$file_del->emplacement);
     */
	 
	 static function remove2($path,$nom) {
        global $zdb;
		 $where=array(
					"emplacement='".$nom."'"
					); 
					
        $res2=$zdb->db->delete(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE, $where);
        
		if(file_exists($path))
			{
			$res1=unlink($path);
			$res=0;
			if($res1==1 && $res2==1)
				{
				$res=1;
				//echo("&res=".$res."&");
				}
			
			}
		
		return $res;
    }
	
	/**
     * Liste les fichiers d'un répertoire et supprime le fichier s'il n'est pas dans la bdd (ne supprime pas les répertoires)
     *
     * @param $pathrep emplacement du répertoire
	 * Return un tableau contenant les emplacements des fichiers supprimés (123587_5_1.jpg, mother.pdf...)
     */
	 

 static function listrep($pathrep)
{
//var_dump($pathrep);
//$pathrep = './upload/files';
global $zdb;

	//si le répertoire existe, on l'ouvre
	if ($rep = opendir($pathrep)) 
	{
		//tant q'il y a des fichiers
		while ($element = readdir($rep))
		{
		   $ext=strrchr($element,'.');
		   //si le fichier n'est pas un répertoire
		   if (!is_dir($pathrep.'/'.$element) && $ext)
			{
			
			//on execute une requete SQL pour savoir s'il est ds la bdd sinon on le supprime
			 $select_empl = new Zend_Db_Select($zdb->db);
			 $select_empl->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
					->where('emplacement = ?', $element)
					->limit(1, 0);
					
				if ($select_empl->query()->rowCount() < 1) 
					{
					$file1=new File();
					$file1->remove2($pathrep."/".$element,"");
					
					//on stocke son emplacement dans un tableau
					$tri[$element] = $element;
					}
			}//fin du if
		}//fin du while
		closedir($rep);
		
		//si le tableau contient des fichiers
		if(isset($tri))
			{
			//Tri du tableau sur le nom.
			sort($tri);
			$res = $tri;
			}
		else
			{
			$res[0] = "repertoire vide";
			}
	}
	else
		{
		var_dump("Répertoire non accessible !");
		}

//var_dump($res);
return $res;
}

	/**
     * Script de nettoyage
	 * recherche dans la bdd si le fichier est dans la bdd + non vierge && date_record >2ans && fichier existe->delete + delete bdd
	 *	ou si le fichier n'est pas dans la bdd mais est présent dans le répertoire-> delete
	 *  ou si fichier n'existe pas dans le répertoire mais présent dans la bdd, on supprime juste sa ligne ds la bdd
	 *	script appelé à chaque suppression d'un fichier par qqn du staff.
     *
     * @param rien
	 * Return rien
     */
	 
	 static function clean_file() {
        //paramètres
		$duree1=2;//en année
				
		global $zdb;
		
		$select = new Zend_Db_Select($zdb->db);
        $select->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
                ->where('vierge = ?', '0')
				->where('YEAR(date_record) <= ?', date('Y', strtotime('-'.$duree1.' years')));
            //exemple de requete: ->where('MONTH(date) = ?', date('n', strtotime('last month')))
							   // ->where('YEAR(date) = ?', date('Y'))
							   // ->where('DAY(date_record) < ?', date('d', strtotime('-11 days')));
       
        if ($select->query()->rowCount() > 0) 
			{
			//echo('res>0');
			 $res = $select->query()->fetchAll();
            foreach ( $res as $row ) 
				{
					$file = new File($row);
					$path="./upload/files/".$file->emplacement;
					$file->remove2($path,$file->emplacement);
					//var_dump($file->emplacement);
				}
           
			}//fin du if
		
		//supprime tous les fichiers non présent dans la bdd mais présent dans le répertoire
		$file2=new File();
		$pathrep="./upload/files/";
		$file2->listrep($pathrep);
		
		//supprime tous les fichiers de la bdd qui sont inexistant dans le répertoire
		$file2->clean_file_bdd();
		
    }
	
	/**
     * Script de nettoyage
	 * si fichier n'existe pas sur le serveur, on supprime juste sa ligne ds la bdd
	 *	script appelé à chaque suppression d'un fichier.
     *
     * @param 
	 *
	 * Return rien
     */
	 
	 static function clean_file_bdd() {
       	global $zdb;
		
		$select = new Zend_Db_Select($zdb->db);
        $select->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE);
       
        if ($select->query()->rowCount() > 0) 
			{
			$res = $select->query()->fetchAll();
            foreach ( $res as $row ) 
				{
					$file = new File($row);
					$path="./upload/files/".$file->emplacement;
					if (file_exists($path)) 
						{
							//var_dump ("Le fichier $file->emplacement existe.");
						} else {
							//var_dump ("Le fichier $file->emplacement n'existe pas.");
							$file->remove2("",$file->emplacement);
						}
					//var_dump($file->emplacement);
				}
           
			}//fin du if
		
    }
	
    /**
	*
     * Exécute une requête SQL pour récupérer les données d'un fichier à partir de son id_doc (fichier non vierge)
     * 
     * Ne retourne rien.
     * 
     * @param File $object Le fichier à hydrater
     */
    static function getFile($object) {
        global $zdb;

        // Statut
        $select_id_doc = new Zend_Db_Select($zdb->db);
        $select_id_doc->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
                ->where('id_doc = ?', $object->id_doc)
                ->limit(1, 0);
                
        if ($select_id_doc->query()->rowCount() == 1) {
            $file = $select_id_doc->query()->fetch();
            $object->_id_act = $file->id_act;
            $object->_id_adh = $file->id_adh;
            $object->_id_abn = $file->id_abn;
            $object->_doc_name = $file->doc_name;
            $object->_description = $file->description;
            $object->_emplacement = $file->emplacement;
            $object->_date_record = $file->date_record;
            $object->_vierge = $file->vierge;
            $object->_return_file = $file->return_file;
            
        }
    }
	
	/**
	*
     * Exécute une requête SQL pour récupérer la liste des fichiers à partir de l'id_adh
     * 
     * Retourne un tableau de files si ils existent, 0 sinon
     * 
     * @param id_adh
     */
    public function getFileList() {
        global $zdb;
		$result=0;
		
		if($this->id_adh != NULL)
			{
			$id_adh1='id_adh = ?';
			$id_adh2=$this->id_adh;
			}
		else
			{
			$id_adh1='id_adh IS NULL';
			$id_adh2='';
			}
		
			$vierge1='vierge = ?';
			$vierge2=0;
		
        $select = new Zend_Db_Select($zdb->db);
        $select->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
                ->where($vierge1, $vierge2)
				->where($id_adh1, $id_adh2)
				->order('id_act');
                
        if ($select->query()->rowCount() > 0) 
			{
			//echo('res>0');
			$files = array();
            $res = $select->query()->fetchAll();
            foreach ( $res as $row ) 
				{
					$files[] = new File($row);
					//var_dump($row);
					
				}
            $result=$files;
			
			}//fin du if
			
		return $result;
    }
	
	/**
	*
     * Exécute une requête SQL pour récupérer la liste des fichiers à partir de l'id_adh et id_act
     * 
     * Retourne un tableau de files si ils existent, 0 sinon
     * 
     * @param id_act, id_adh
     */
    public function getFileListAdh() {
        global $zdb;
		$result=0;
        
		if($this->id_act != NULL)
			{
			$id_act1='id_act = ?';
			$id_act2=$this->id_act;
			}
		else
			{
			$id_act1='id_act IS NULL';
			$id_act2='';
			}
		
		if($this->id_adh != NULL)
			{
			$id_adh1='id_adh = ?';
			$id_adh2=$this->id_adh;
			}
		else
			{
			$id_adh1='id_adh IS NULL';
			$id_adh2='';
			}
		
			$vierge1='vierge = ?';
			$vierge2=0;
		
		//var_dump("vierge=");
		//var_dump($vierge2);
		// var_dump("id_act=");
		// var_dump($id_act2);
		// var_dump("id_adh=");
		// var_dump($id_adh2);
		
        $select = new Zend_Db_Select($zdb->db);
        $select->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
                ->where($vierge1, $vierge2)
				->where($id_act1, $id_act2)
				->where($id_adh1, $id_adh2)
				->order('id_doc');
                
        if ($select->query()->rowCount() > 0) 
			{
			//echo('res>0');
			$files = array();
            $res = $select->query()->fetchAll();
            foreach ( $res as $row ) 
				{
					$files[] = new File($row);
					//var_dump($row);
					
				}
            $result=$files;
			
			}//fin du if
			
		return $result;
    }
	
	/**
	*
     * Exécute une requête SQL pour récupérer la liste des fichiers vierges (=provenant de la page mangement_group)
     * 
     * Retourne un tableau de files si ils existent, 0 sinon
     * 
     * @param id_act, id_abn, vierge optionnels
     */
    public function getFileListVierge() {
        global $zdb;
		$result=0;
        
		if($this->id_act != NULL)
			{
			$id_act1='id_act = ?';
			$id_act2=$this->id_act;
			}
		else
			{
			$id_act1='id_act IS NULL';
			$id_act2='';
			}
			
			
		
			$vierge1='vierge = ?';
			$vierge2=1;
			
		
		 //var_dump("vierge=");
		 //var_dump($vierge2);
		// var_dump("id_act=");
		// var_dump($id_act2);
		
        $select = new Zend_Db_Select($zdb->db);
        $select->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
                ->where($vierge1, $vierge2)
				->where($id_act1, $id_act2)
				->order('id_doc');
                
        if ($select->query()->rowCount() > 0) 
			{
			//echo('res>0');
			$files = array();
            $res = $select->query()->fetchAll();
            foreach ( $res as $row ) 
				{
					$files[] = new File($row);
					//var_dump($row);
					
				}
            $result=$files;
			
			}//fin du if
			
		return $result;
    }
	

	/**
	*
     * Exécute une requête SQL pour récupérer les données d'un fichier à partir de son timestamp réduit (sans) extension
     * 
     * Retourne 1 si le fichier a été trouvé 0 sinon.
     * 
     * @param File $object Le fichier à hydrater contenant le timestamp réduit nombre.(extension)
     */
    static function getFileDesc($object) {
        global $zdb;
		$result=0;
		//var_dump("emplacement");
        //var_dump($object->emplacement);
        $select_timestamp = new Zend_Db_Select($zdb->db);
        $select_timestamp->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
				//emplacement timestamp_idact_idfile.extension
                ->where('emplacement LIKE ?', $object->emplacement.'%.%')
				->limit(1, 0);
                
        if ($select_timestamp->query()->rowCount() == 1) {
            $file = $select_timestamp->query()->fetch();
            $object->_id_doc = $file->id_doc;
            $object->_id_act = $file->id_act;
            $object->_id_adh = $file->id_adh;
            $object->_doc_name = $file->doc_name;
            $object->_emplacement = $file->emplacement;
            $object->_date_record = $file->date_record;
            $result=1;
        }
		return $result;
    }
	    /**
     * Retrieve fields from database
     *
     * @return array
     */
    public static function getDbFields()
    {
        global $zdb;
        return array_keys($zdb->db->describeTable(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE));
    }
	
	
	 /**
	 * Check posted values validity
	 *
	 * @param array $values   All values to check, basically the $_POST array
	 *                        after sending the form
	 *
	 * @return true|array
	 */
	public function check($values)
	{
		global $zdb;
		$valid = '1';
		$fields = self::getDbFields();
		//var_dump ($fields);
	 foreach ( $fields as $key ) {
				//first of all, let's sanitize values
				$key = strtolower($key);
				if ( isset($values[$key]) ) {
					$value = stripslashes(trim($values[$key]));
					$this->$key=$value;
					
				} 
					//echo ('affichage de $key et $value');
					//echo ($key);
					//var_dump ($value);			
			}
			
			return $valid;
	}			
 
    
  
    /**
     * Global getter method
     *
     * @param string $name name of the property we want to retrive
     *
     * @return false|object the called property
     */
    public function __get($name) {
        $rname = '_' . $name;
        if (substr($rname, 0, 3) == '___') {
            return false;
        }
        switch ($name) {
            case 'doc_name':
                return str_replace('\'', '’', $this->_doc_name);
            case 'decsription':
                return str_replace('\'', '’', $this->_decsription);
            case 'emplacement':
                return str_replace('\'', '’', $this->_emplacement);
            default:
                return $this->$rname;
        }
    }

    /**
     * Global setter method
     *
     * @param string $name  name of the property we want to assign a value to
     * @param object $value a relevant value for the property
     *
     * @return void
     */
    public function __set($name, $value) {
        $rname = '_' . $name;
        $this->$rname = $value;
    }

}

?>
