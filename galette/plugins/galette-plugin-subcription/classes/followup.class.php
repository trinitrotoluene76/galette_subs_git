<?php

/**
 * Follow up class for Subscribtion plugin
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
use Galette\Entity\Adherent as Adherent;

class Followup {

    const TABLE = 'followup';
    //clé primaire de la table followup
	//const PK = 'id_act,id_adh,id_abn';

	//champs de la table followup de la bdd
    private $_fields = array(
        '_id_act' => 'integer',
        '_id_adh' => 'integer',
        '_id_abn' => 'integer',
        '_statut_act' => 'varchar(200)',
		'_feedback_act' => 'varchar(200)',
		'_message_adh_act' => 'varchar(200)',
		'_feedback_act_off' => 'varchar(200)'
    );
	
	//variables le l'objet
    private $_id_act;
    private $_id_adh;
    private $_id_abn;
    private $_statut_act=''; //0=en cours de validation/ 1= refusé / 2=validé / 3= payé 
    private $_feedback_act='';
    private $_message_adh_act ='';
    private $_feedback_act_off = '';
    
    

    /**
     * Construit unnouveau suivi à partir de la BDD (à partir de son ID) ou vierge
     * 
     * @param int|object $args Peut être null, un tableau d'ID ou une ligne de la BDD
     */
    public function __construct($args = null) {
        global $zdb;

        if (is_int($args[0]) && is_int($args[1]) && is_int($args[2])) {
            try {
                $select = new Zend_Db_Select($zdb->db);
                $select->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
                        ->where('id_act = ' . $args[0])
						->where('id_adh = ' . $args[1])
						->where('id_abn = ' . $args[2]);
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
	 * Exécute une requête SQL pour savoir si la clé primaire existe (id_act, id_adh, id_abn) existe
     * Retourne 1 si l’id existe dans la table->update
	 * 0 s’il n’existe pas dans la table->insert
	 * $result=0
	 *
     * @param Subcription $object L'abonnement dont on cherche l'id abn
     */
    static function is_id($object) 
		{
		global $zdb;
		$result=0;
		// Statut
		$req1 = new Zend_Db_Select($zdb->db);
		$req1->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
				->where('id_act = ?', $object->id_act)
				->where('id_adh = ?', $object->id_adh)
				->where('id_abn = ?', $object->id_abn)
				->limit(1, 0);
				
		if ($req1->query()->rowCount() == 1) 
			{
			$result=1;
			}//fin du 1er if
		return $result;
		}//fin de la fonction
	
   /**
	 * Exécute une requête SQL enregistrer l'adhérent dans le group/l'activité. Cette méthode est appelée si le statut_act>="validé"
     * Retourne 1 si l'adhérent a été inséré dans la table group
	 * 0 l'adhérent existe déjà
	 * $result=0
	 *
     * @param 
     */
    static function put_into_group($object) 
		{
		global $zdb;
		$result=0;
		// Statut
		$req2 = new Zend_Db_Select($zdb->db);
		$req2->from(PREFIX_DB . Group::GROUPSUSERS_TABLE)
				->where('id_group = ?', $object->id_act)
				->where('id_adh = ?', $object->id_adh)
				->limit(1, 0);
				
		if ($req2->query()->rowCount() == 1) 
			{
			$result=0;
			}//fin du 1er if
		else
			{
				$values= array(
				Group::PK => $object->id_act,
				Adherent::PK => $object->id_adh
				);
				$add = $zdb->db->insert(PREFIX_DB .Group::GROUPSUSERS_TABLE, $values);
				//Analog\Analog::log('insert followup');
				if ($add > 0) 
					{	//lastInsertId n'est valable que pour les clés autoincrémentés
						//$object->_id_abn = $zdb->db->lastInsertId();
					} else {
							throw new Exception(_T("followup.group.AJOUT ECHEC"));
							}
				$result=1;
			}
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
			$res=$this->is_id($this);
            if ($res=='0') 
				{
				$add = $zdb->db->insert(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE, $values);
				//Analog\Analog::log('insert followup');
				if ($add > 0) 
					{	//lastInsertId n'est valable que pour les clés autoincrémentés
						//$this->_id_abn = $zdb->db->lastInsertId();
					} else {
							throw new Exception(_T("followup.insert ECHEC"));
							}
				} 
			if ($res=='1') 
				{
				//Analog\Analog::log('update followup');
				$where['id_act = ?'] = $this->_id_act;
				$where['id_adh = ?'] = $this->_id_adh;
				$where['id_abn = ?'] = $this->_id_abn;
				$edit = $zdb->db->update(
				PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE, $values, $where
										);
				}else {
								throw new Exception(_T("followup.update ECHEC"));
								}
            return true;
			} catch (Exception $e) {
					Analog\Analog::log(
                    'Something went wrong : ' . $e->getMessage() . "\n" .
                    $e->getTraceAsString(), Analog\Analog::ERROR
					);
            return false;
			}
			
			//Si le statut est validé ou payé on met l'adhérent dans le group
			if(($this->_satut_act)>=2)
				{
				put_into_group();
				}
    }//fin du store

    /**
	 *
     * Exécute une requête SQL pour supprimer un suivi
     * Retourne 1 si le suivi est supprimé, 0 sinon.
     * 
     * @param Subscription $object Le suivi à supprimer contenant l'id_act.
     */
    static function remove($object) {
        global $zdb;
		$where=array(
					"id_act=".$object->id_act
					);
					//var_dump($where);
        $rem=$zdb->db->delete(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE, $where);
        
		return $rem;
    }
	
    /**
	 *
     * Exécute une requête SQL pour récupérer les infos d'un suivi
     * Ne retourne rien.
     * 
     * @param followup $object Le suivi à hydrater.
     */
    static function getFollowup($object) {
        global $zdb;

        // Statut
        $select_id = new Zend_Db_Select($zdb->db);
        $select_id->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
                ->where('id_act = ?', $object->id_act)
                ->where('id_adh = ?', $object->id_adh)
                ->where('id_abn = ?', $object->id_abn)
                ->limit(1, 0);
                
        //Analog\Analog::log('test de load followup');

        if ($select_id->query()->rowCount() == 1) {
            $followup = $select_id->query()->fetch();
            $object->_statut_act = $followup->statut_act;
            $object->_feedback_act = $followup->feedback_act;
            $object->_message_adh_act = $followup->message_adh_act;
            $object->_feedback_act_off = $followup->feedback_act_off;
            
        }
    }
	
	 /**
	 *
     * Exécute une requête SQL pour récupérer l'id_adh d'un abonnement
     * Ne retourne rien.
     * 
     * @param followup $object Le suivi contenant l'id_abn.
     */
    static function getAdh($object) {
        global $zdb;

        // Statut
        $select_id = new Zend_Db_Select($zdb->db);
        $select_id->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
                ->where('id_abn = ?', $object->id_abn)
                ->limit(1, 0);
                
        //Analog\Analog::log('test de load followup');

        if ($select_id->query()->rowCount() == 1) {
            $followup = $select_id->query()->fetch();
            $object->_id_adh = $followup->id_adh;
        }
    }
	
   /**
	 *
     * Exécute une requête SQL pour récupérer les abonnements
     * Retourne un tableau d'id_abn.
     * 
     * @param followup $object Le suivi contenant l'id_act.
     */
    static function getFollowupSub($object) {
        global $zdb;

        // Statut
        $select_id = new Zend_Db_Select($zdb->db);
        $select_id->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
                ->where('id_act = ?', $object->id_act)
				->order('id_abn');
        //var_dump($select_id->query()->rowCount());
			        
        //Analog\Analog::log('test de load followup');
		$result=array();
        if ($select_id->query()->rowCount() > 0) {
			$followups = $select_id->query()->fetchAll();
            //var_dump($followups);
			foreach ( $followups as  $key => $value ) 
				{
				//var_dump($key);
				$result[]=$followups[$key]->id_abn;
				}
            
        }//fin du if
		//var_dump($result);
		return $result;
    }
	
	/**
	 *
     * Exécute une requête SQL pour récupérer les activités d'un abn
     * Retourne un tableau d'id_act.
     * 
     * @param followup $object Le suivi contenant l'id_abn.
     */
    static function getFollowupAct($object) {
        global $zdb;

        // Statut
        $select_acts = new Zend_Db_Select($zdb->db);
        $select_acts->from(PREFIX_DB . SUBSCRIPTION_PREFIX . self::TABLE)
                ->where('id_abn = ?', $object->id_abn);
        //var_dump($select_acts->query()->rowCount());
			        
        //Analog\Analog::log('test de load followup');
		$result2=array();
        if ($select_acts->query()->rowCount() > 0) {
			$followups2 = $select_acts->query()->fetchAll();
            //var_dump($followups2);
			foreach ( $followups2 as  $key2 => $value2 ) 
				{
				//var_dump($key2);
				$result2[]=$followups2[$key2]->id_act;
				}
            
        }//fin du if
		//var_dump($select_acts->query()->rowCount());
		return $result2;
    }
	
  /**
	 *
     * Exécute une requête SQL pour reconstituer le statut d'un abonnement à partir du statut des activités
     * Retourne un statut_abn (0=orange, 2=rouge, 1=vert)
						4 statuts act : en cours, validé, payé, refusé -> 3 couleurs statut_abn (orange, rouge, vert)
						//0=en cours
						//1=validé
						//2=payé
						//3=refusé.
     * 
     * @param id_abn $id_abn La clé primaire d'un abonnement.
     */
    static function getStatusSub($id_abn) {
		
		//initialisation des variables
		$followups=array();
		$total_statut_ref=array();//sert à calculer le statut abn si refus
		$statut_abn=0; //0 orange, 1 vert, 2 rouge

		$followup=new Followup;
		$followup->id_abn=$id_abn;
		
		//retourner la liste des activités d'un abn (pour peu qu'il soit enregistré dans la bdd)
		$activities=$followup->getFollowupAct($followup);
		//var_dump($activities);
		//var_dump($followup);
		
		//pour chaque suivi d'activité
		foreach ( $activities as  $key => $id_act ) 
			{
			$followup2=new Followup;
			$followup2->id_act=$id_act;
			$followup2->id_abn=$id_abn;
			$followup2->getAdh($followup2);
			//var_dump($followup2);
			$followup2->getFollowup($followup2);
			$followups[]=$followup2;
			}//fin du foreach act
			
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
		//var_dump($followups);
		//pour chaque suivi d'activité, on additionne les statuts de chaque activité
		foreach ( $followups as  $key => $followup3 ) 
			{
			$total_statut=$total_statut+$followup3->statut_act;
			//var_dump($total_statut);
			$total_statut_ref[]=$followup3->statut_act;
			}
		//var_dump(2*count($followups));
		//var_dump($followup3->statut_act);
		//var_dump($total_statut);
		
		//si toutes les activités sont payées (alors la somme des statuts= 2x nb d'activités dans l'abn)
		if($total_statut==2*count($followups))
			{
			$statut_abn=1;
			}
		
		//si il y a au moins un refus (alors le max du tableau des statuts de l'abn = 3)
		if(max($total_statut_ref)==3)
			{
			$statut_abn=2;
			}
		//si les statuts des activités sont différentes de refusées ou payées, alors on est en cours
		if(max($total_statut_ref)!=3 && $total_statut!=2*count($followups))
			{
			$statut_abn=0;
			}
			
		return $statut_abn;
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
            case 'feedback_act':
				return str_replace('\'', '’', $this->_feedback_act);
				break;
            case 'message_adh_act':
				return str_replace('\'', '’', $this->_message_adh_act);
				break;
            case 'feedback_act_off':
                return str_replace('\'', '’', $this->_feedback_act_off);
				break;
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
