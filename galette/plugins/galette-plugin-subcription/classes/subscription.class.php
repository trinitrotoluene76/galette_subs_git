<?php

/**
 * Subscription class for Subscribtion plugin
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
 
class Subscription {

    const TABLE = 'subscriptions';
    //clé primaire de la table subscriptions
	const PK = 'id_abn';

	//champs de la table subscriptions de la bdd
    private $_fields = array(
        '_id_abn' => 'integer',
        '_id_adh' => 'integer',
        '_date_demande' => 'datetime',
		'_total_estimme' => 'decimal',
        '_message_abn' => 'varchar(200)'
    );
	
	//variables le l'objet
    private $_id_abn;
    private $_id_adh;
    private $_date_demande;
    private $_total_estimme = 0.0;
    private $_message_abn = '';
    
    

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
	 * Exécute une requête SQL pour savoir si l'id_abn existe
     * Retourne 1 si l’id existe dans la table->update
	 * 0 s’il n’existe pas dans la table->insert
	 * $result=0
	 *
     * @param Subcription $object L'abonnement dont on cherche l'id abn
     */
    public function is_id_abn($object) 
		{
		global $zdb;
		$result=0;
		if($object->id_abn!=null)
			{		
			//echo('on rentre dasn is_id_abn, $object->id_abn');
			//var_dump($object->id_abn);
			$req1 = new Zend_Db_Select($zdb->db);
			$req1->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
					->where('id_abn = ?', $object->id_abn)
					->limit(1, 0);
					
			if ($req1->query()->rowCount() == 1) 
				{
				//echo('on rentre dans is_id_abn, result=1');
			
				$result=1;
				}//fin du 1er if
			}//fin du 1er if
		return $result;
		}//fin de la fonction
	
 

  /** Enregistre l'élément en cours que ce soit en insert ou update
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
			$res=$this->is_id_abn($this);
            //echo('$this->is_id_abn($this) dans store');
			//var_dump($res);
			//var_dump($this);
			if ($res=='0') 
				{
				//var_dump('insert');
				$add = $zdb->db->insert(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE, $values);
				//Analog\Analog::log('insert Subscription');
				if ($add > 0) 
					{
						$this->_id_abn = $zdb->db->lastInsertId();
					} else {
							throw new Exception(_T("Subscription.AJOUT insert ECHEC"));
							}
				} 
			if ($res=='1') 
				{
				//Analog\Analog::log('update Subscription');
				//echo('update id abn:');
				//var_dump($this->_id_abn);
				$edit = $zdb->db->update(
				PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE, $values, self::PK . '=' . $this->_id_abn
										);
				if ($edit > 0) 
					{
					}else {
								throw new Exception(_T("Subscription.AJOUT update ECHEC"));
								}
				}
				
            return true;
			} catch (Exception $e) {
					Analog\Analog::log(
                    'Something went wrong :' . $e->getMessage() . "\n" .
                    $e->getTraceAsString(), Analog\Analog::ERROR
					);
            return false;
        }
    }

   /**
	 *
     * Exécute une requête SQL pour supprimer un abonnement
     * Retourne 1 si l'abonnement est supprimé, 0 sinon.
     * 
     * @param Subscription $object La subscription supprimer contenant l'id_abn.
     */
    static function remove($object) {
        global $zdb;

        $rem=$zdb->db->delete(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE, self::PK . '=' . $object->id_abn);
        
		return $rem;
    }
 
    /**
	 *
     * Exécute une requête SQL pour récupérer les infos d'une subsription
     * Ne retourne rien.
     * 
     * @param Subscription $object La subscription à hydrater contenant l'id_abn.
     */
    static function getSubscription($object) {
        global $zdb;

        // Statut
        $select_id_abn = new Zend_Db_Select($zdb->db);
        $select_id_abn->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
                ->where('id_abn = ?', $object->id_abn)
                ->limit(1, 0);
        //Analog\Analog::log('test de load subscription');

        if ($select_id_abn->query()->rowCount() == 1) {
            $subscription = $select_id_abn->query()->fetch();
            $object->_id_adh = $subscription->id_adh;
            $object->_date_demande = $subscription->date_demande;
            $object->_total_estimme = $subscription->total_estimme;
            $object->_message_abn = $subscription->message_abn;
            //var_dump($object); 
        }
    }
 
 /**
	 *
     * Exécute une requête SQL pour récupérer la liste des subsriptions d'un adhérent
     * Retourne un tableau d'id_abn
     * 
     * @param Subscription $object La subscription à hydrater contenant l'id_adh.
     */
    static function getSubscriptionList($object) {
        global $zdb;

        // Statut
        $select_id_abn = new Zend_Db_Select($zdb->db);
        $select_id_abn->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
                ->where('id_adh = ?', $object->id_adh);
        //var_dump($select_id_abn);
		$result=0;
        if ($select_id_abn->query()->rowCount() > 0) 
			{
				$result=array();
				$subscriptions = $select_id_abn->query()->fetchAll();
				
				foreach ( $subscriptions as  $key => $value ) 
					{
					//var_dump($key);
					$result[]=$subscriptions[$key]->id_abn;
					}
					
			}//fin du if
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
            case 'message_abn':
                return str_replace('\'', '’', $this->_message_abn);
            case 'total_estimme':
               // return number_format($this->_total_estimme, 2, ',', ' ');
            
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
