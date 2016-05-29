SET FOREIGN_KEY_CHECKS=0;

-- Table structure for table galette_subscription_subscriptions
DROP TABLE IF EXISTS galette_subscription_subscriptions;
CREATE TABLE galette_subscription_subscriptions (
  id_abn int(10) unsigned NOT NULL AUTO_INCREMENT,
  id_adh int(10) unsigned NOT NULL,
  date_demande date NOT NULL default '1901-01-01',
  total_estimme decimal(15,2) unsigned default '0',
  message_abn text,
  PRIMARY KEY (id_abn),
  FOREIGN KEY(id_adh) REFERENCES galette_adherents (id_adh) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 
-- Table structure for table galette_subscription_activities
DROP TABLE IF EXISTS galette_subscription_activities;
CREATE TABLE galette_subscription_activities (
  id_group int(10),
  price1 decimal(15,2) unsigned default '0',
  price2 decimal(15,2) unsigned default '0',
  price3 decimal(15,2) unsigned default '0',
  price4 decimal(15,2) unsigned default '0',
  lieu text,
  jours text,
  horaires text,
  renseignements text,
  complet int(10),
  autovalidation int(10),
  PRIMARY KEY (id_group),
  FOREIGN KEY(id_group) REFERENCES galette_groups (id_group) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

  
-- Table structure for table galette_subscription_files
DROP TABLE IF EXISTS galette_subscription_files;
CREATE TABLE galette_subscription_files (
  id_doc int(10) unsigned NOT NULL AUTO_INCREMENT,
  id_act int(10)  NULL,
  id_adh int(10) unsigned  NULL,
  id_abn int(10) unsigned NULL,
  doc_name varchar(200) NOT NULL default '',
  description text,
  emplacement varchar(200) default '',
  date_record date NOT NULL default '1901-01-01',  
  vierge int(10),
  return_file int(10) DEFAULT NULL,
  PRIMARY KEY (id_doc),
  FOREIGN KEY(id_act) REFERENCES galette_groups (id_group) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(id_adh) REFERENCES galette_adherents (id_adh) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(id_abn) REFERENCES galette_subscription_subscriptions (id_abn) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
  
-- Table structure for table galette_subscription_followup
DROP TABLE IF EXISTS galette_subscription_followup;
CREATE TABLE galette_subscription_followup (
  id_act int(10) NOT NULL,
  id_adh int(10) unsigned NOT NULL,
  id_abn int(10) unsigned NOT NULL,
  statut_act int(10) unsigned default '0',
  feedback_act text,
  message_adh_act text,
  feedback_act_off text,
  PRIMARY KEY (id_act,id_adh,id_abn),
  FOREIGN KEY(id_act) REFERENCES galette_groups (id_group) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(id_adh) REFERENCES galette_adherents (id_adh) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(id_abn) REFERENCES galette_subscription_subscriptions (id_abn) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
	
SET FOREIGN_KEY_CHECKS=1;
