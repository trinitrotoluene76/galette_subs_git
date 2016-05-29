<?php
  /***************************************************************************/
  // Parse les var. pour flash afin de lui envoyer dans le bon format 
  function parse($variable,$valeur)
  {
    echo "&$variable=$valeur";
  }
  /***************************************************************************/


//Maintenant, on se connecte à la base de données
include("../identifiants/identifiants.php");
mysql_connect($host, $user, $password) or die ("impossible de se connecter au serveur");
mysql_select_db($bdd) or die ("impossible de se connecter a la base de donnees"); 
//Initialisation de deux variables



$madate=date('d/m/Y');
$lien=$_POST[nomfichieretendu];
// Connexion
mysql_connect($host, $user, $password) or die ("impossible de se connecter au serveur");
mysql_select_db($bdd) or die ("impossible de se connecter a la base de donnees"); 
$SQLQuery = "INSERT INTO tb_upload (id, auteur, date, nom, description,lien) VALUES ('','$_POST[auteur]', '".$madate."','$_POST[url]','$_POST[description]','$lien') ";
$result = mysql_query($SQLQuery);

mysql_close();             
 // ----- traitement termine
  parse("done",1);
    
                  ?>