<?php

/**
 * Activity class for Subscribtion plugin
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
 
use Galette\Entity\Group as Group;
 
class Activity {

    const TABLE = 'activities';
    //clé primaire de la table activities
	const PK = 'id_group';

	//champs de la table activities de la bdd
    private $_fields = array(
        '_id_group' => 'integer',
        '_price1' => 'decimal',
        '_price2' => 'decimal',
        '_price3' => 'decimal',
        '_price4' => 'decimal',
        '_lieu' => 'varchar(200)',
        '_jours' => 'varchar(200)',
        '_horaires' => 'varchar(200)',
        '_renseignements' => 'varchar(200)',
		'_complet' => 'integer',
		'_autovalidation' => 'integer'
    );
	
	//variables le l'objet
    private $_id_group;
    private $_group_name = '';
    private $_price1 = 0.0;
    private $_price2 = 0.0;
    private $_price3 = 0.0;
    private $_price4 = 0.0;
    private $_lieu = '';
    private $_jours = '';
    private $_horaires = '';
    private $_renseignements = '';
	private $_complet=0;
    private $_autovalidation=0;
    
    
    

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
	 * Exécute une requête SQL pour savoir si l'id_group à créer est valide (est présent dans la table group)
     * Retourne 1 si l’id existe dans la table group et activity ->update
	 * 0 s’il n’est dans aucune table
	 * 2 si s’il existe dans la table group mais pas dans la table activity ->insert
	 * $result=0
	 *
     * @param Activity $object L'objet dont on cherche l'id group
     */
    static function is_id_group($object) 
		{
		global $zdb;
		$result=0;
		// Statut
		$req1 = new Zend_Db_Select($zdb->db);
		$req1->from(PREFIX_DB . Galette\Entity\Group::TABLE)
				->where('id_group = ?', $object->id_group)
				->limit(1, 0);
				
		if ($req1->query()->rowCount() == 1) 
			{
			$result=2;
			$req2 = new Zend_Db_Select($zdb->db);
			$req2->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
				->where('id_group = ?', $object->id_group)
				->limit(1, 0);
			if ($req2->query()->rowCount() == 1) 
				{
				$result=1;
				}//fin du 2eme if
			}//fin du 1er if
		return $result;
		}//fin de la fonction
	
    /**
	 * Exécute une requête SQL pour savoir si l'activité est au complet
     * Retourne 1 si oui
	 * 0 sinon
	 * $result
	 *
     * @param 
     */
    public function is_full() 
		{
		global $zdb;
		$result=0;
			
		$req2 = new Zend_Db_Select($zdb->db);
		$req2->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
			->where('id_group = ?', $this->_id_group)
			->where('complet = ?', '1')
			->limit(1, 0);
		if ($req2->query()->rowCount() == 1) 
			{
			$result=1;
			}//fin du 2eme if
			
		return $result;
		}//fin de la fonction
    
	/**
	 * Exécute une requête SQL pour avoir la liste des groupes parents
     * Retourne un tableau de groups si il existe, 0 sinon
	 * 
     * @param void
     */
    public function get_parentgrouplist() 
		{
		global $zdb;
		$result=0;
			
		$select = new Zend_Db_Select($zdb->db);
		$select->from(PREFIX_DB . Group::TABLE)
			->where('parent_group IS NULL');
		if ($select->query()->rowCount() > 0) 
			{
			//echo('res>0');
			$groups = array();
            $res = $select->query()->fetchAll();
            foreach ( $res as $row ) 
				{
					$groups[] = new Group($row);
				}
            $result=$groups;
			
			}//fin du if
			
		return $result;
		}//fin de la fonction

	/**
	 * Exécute une requête SQL pour savoir si l'activité est parente ou non
     * Retourne 1 si elle est parente, 0 sinon
	 * 
     * @param void
     */
    public function is_parent_group() 
		{
		global $zdb;
		$result=0;
			
		$select = new Zend_Db_Select($zdb->db);
		$select->from(PREFIX_DB . Group::TABLE)
			->where('parent_group IS NULL')
			->where('id_group = ?', $this->_id_group);
		if ($select->query()->rowCount() > 0) 
			{
			//echo('res>0');
			$result=1;
			}//fin du if
			
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
			$res=$this->is_id_group($this);
			//echo ('res');
			//var_dump ($res);
            
			if ($res=='2') 
				{
				$add = $zdb->db->insert(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE, $values);
				//Analog\Analog::log('insert activity');
				if ($add > 0) 
					{
						$this->_id_group = $zdb->db->lastInsertId();
						//echo ('insertion réussie');
						
					} else {
							throw new Exception(_T("ACTIVITY.AJOUT ECHEC res=2"));
							//echo ('insertion non réussie');
							}
				}//fin du if res==2 
			if ($res=='1') 
				{
				//Analog\Analog::log('update activity');
				$edit = $zdb->db->update(
				PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE, $values, self::PK . '=' . $this->_id_group
										);
				}//fin du if res==1
			else {
					//echo ('res=0');
				 }
            return true;
			}//fin du try
			catch (Exception $e) 
				{
					Analog\Analog::log(
                    'Something went wrong :\'( | ' . $e->getMessage() . "\n" .
                    $e->getTraceAsString(), Analog\Analog::ERROR
					);
					echo ('catch exeption');
					return false;
				}	
    }//fin de la fonction

	/**
     * remove all adherent from Group
     *
     * @param id_group, l'id du groupe contenant les adhérents
	 * Return 1 si ok, 0 sinon
     */
	 
	 static function remove($id_group) {
        global $zdb;
		$where=array(
					"id_group=".$id_group
					);
					
        $rem=$zdb->db->delete(PREFIX_DB . Group::GROUPSUSERS_TABLE, $where);
        
		return $rem;
    }
	
	/**
     * Get groups list
     *
     * @param boolean $full Return full list
     *
     * @return Zend_Db_RowSet
     */
    public function getList($full = true)
    {
        global $zdb, $login;
        try {
            $select = new \Zend_Db_Select($zdb->db);
            $select->from(
                array('a' => PREFIX_DB . Group::TABLE)
            )->joinLeft(
                array('b' => PREFIX_DB . Group::GROUPSUSERS_TABLE),
                'a.' . Group::PK . '=b.' . Group::PK,
                array('members' => new \Zend_Db_Expr('count(b.' . Group::PK . ')'))
            );

            /* if ( !$login->isAdmin() && !$login->isStaff() && $full === true ) {
                $select->join(
                    array('c' => PREFIX_DB . Group::GROUPSMANAGERS_TABLE),
                    'a.' . Group::PK . '=c.' . Group::PK,
                    array()
                )->where('c.' . Adherent::PK . ' = ?', $login->id);
            } */

            if ( $full !== true ) {
                $select->where('parent_group IS NULL');
            }

            $select->group('a.' . Group::PK)
                ->group('a.group_name')
                ->group('a.creation_date')
                ->group('a.parent_group')
                ->order('a.group_name ASC');

            $groups = array();
            $res = $select->query()->fetchAll();
            foreach ( $res as $row ) {
                $groups[] = new Group($row);
            }
            return $groups;
        } catch (\Exception $e) {
            Analog::log(
                'Cannot list groups | ' . $e->getMessage(),
                Analog::WARNING
            );
            Analog::log(
                'Query was: ' . $select->__toString() . ' ' . $e->getTraceAsString(),
                Analog::ERROR
            );
        }
    }//fin de la fonction
  
    /**
	*
     * Exécute une requête SQL pour récupérer les données d'une activité
     * 
     * Ne retourne rien.
     * 
     * @param Activity $object L'activité cherchée contenant l'id_group
     */
    static function getActivity($object) {
        global $zdb;

        // Statut
        $select_id_grp = new Zend_Db_Select($zdb->db);
        $select_id_grp->from(PREFIX_DB . SUBSCRIPTION_PREFIX . Activity::TABLE)
                ->join(PREFIX_DB . Galette\Entity\Group::TABLE, PREFIX_DB . Galette\Entity\Group::TABLE . '.id_group = ' . PREFIX_DB . SUBSCRIPTION_PREFIX . Activity::TABLE . '.id_group')
                ->where(PREFIX_DB . Galette\Entity\Group::TABLE.'.id_group = ?', $object->id_group)
                ->limit(1, 0);
                
        //Analog\Analog::log('test de load activity');

        if ($select_id_grp->query()->rowCount() == 1) {
            $activity = $select_id_grp->query()->fetch();
            $object->_id_group = $activity->id_group;
            $object->_group_name = $activity->group_name;
            $object->_price1 = $activity->price1;
            $object->_price2 = $activity->price2;
            $object->_price3 = $activity->price3;
            $object->_price4 = $activity->price4;
            $object->_lieu = $activity->lieu;
            $object->_jours = $activity->jours;
            $object->_horaires = $activity->horaires;
            $object->_renseignements = $activity->renseignements;
            $object->_complet = $activity->complet;
            $object->_autovalidation = $activity->autovalidation;
            
        }
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
	 * modifi le type des prix de string à float
	 *
	 * @param Price
	 *
	 * @return price en float
	 */
	public function floatprice($price)
	{
		$number=str_replace(",", ".", $price);
		$number=floatval ($number);
		$number=number_format($number, 2, '.', '');
		$price2=round($number,2);
		return $price2;
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
					
					switch($key) 
						{
						//vérification des prix et formatage (remplacement , par . conversion en float limitation à 2 décimales et arrondi)
						case 'price1':
						case 'price2':
						case 'price3':
						case 'price4':
            
						if ( $value<0 ) 
							{
							$valid = '0';	
							}
						$number=str_replace(",", ".", $value);
						$number=floatval ($number);
						$number=number_format($number, 2, '.', '');
						$this->$key=round($number,2);
					
						}
			
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
            case 'name':
                return str_replace('\'', '’', $this->_group_name);
            case 'price1':
                return number_format($this->_price1, 2, ',', ' ');
            case 'price2':
                return number_format($this->_price2, 2, ',', ' ');
            case 'price3':
                return number_format($this->_price3, 2, ',', ' ');
            case 'price4':
                return number_format($this->_price4, 2, ',', ' ');
            case 'jours':
                return str_replace('\'', '’', $this->_jours);
            case 'horaires':
                return str_replace('\'', '’', $this->_horaires);
            case 'renseignements':
                return str_replace('\'', '’', $this->_renseignements);
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
