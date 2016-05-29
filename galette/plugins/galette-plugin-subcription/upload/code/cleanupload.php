	<?php
	//Maintenant, on se connecte à la base de données (php situé à la racine)
	include("../../identifiants/identifiants.php");
	mysql_connect($host, $user, $password) or die ("impossible de se connecter au serveur");
	mysql_select_db($bdd) or die ("impossible de se connecter a la base de donnees"); 
	//Initialisation de deux variables
	?>

	
<?php
//supprime les fichiers en trop après l'appel de nettoyer le repertoire
if(isset($_POST['cleanrep']))
{
	//trasforme le string en tableau
	$rep=explode(",",$_POST['cleanrep']);
	$page=$_POST['nompageclean'];
	for($i=0;$i<sizeof($rep);$i++)
		{
		$path="../".$page."/upload/".$rep[$i];
		unlink($path);
		}
}

if(isset($_POST['cleanbdd']))
{
	$lien=explode(",",$_POST['cleanbdd']);
	$page=$_POST['nompageclean'];
	for($i=0;$i<sizeof($lien);$i++)
			{
			mysql_query("DELETE FROM jdb_upload WHERE nompage='".$page."' AND lien='".$lien[$i]."'");
			}
}

//recupere le nom de toutes les pages
$requetelist=mysql_query("SELECT * FROM jdblist ORDER BY id ASC") or die ("Une erreur de purge table jdblist");
							if (mysql_num_rows($requetelist) < 1)
							{
							$nompage[]=0;
							}
							else
							{
								$i=0;
								while($datalist = mysql_fetch_assoc($requetelist))
									{
										$nompage[$i]=$datalist['nompage'];
										$i++;
									}
							}
							
//FONCTIONS							
//list les fichiers dans le repertoire upload

function listupload($nompage)
{
$repUpload = '../'.$nompage.'/upload';
	if ($rep = opendir($repUpload)) 
	{
		while ($element = readdir($rep))
		{

		   $ext=strrchr($element,'.');
		   if (!is_dir($repUpload.'/'.$element) && $ext)
			{
			$tri[$element] = $element;
			}

		}
		closedir($rep);
		if(isset($tri))
		{
		 //Tri du tableau sur le nom des images.
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

	  echo("Répertoire non accessible !");
	}
return $res;
}
 //recupere tous les fichiers de la page dans la bdd
 function listbdd($nompage)
 {
	 $requeteupload=mysql_query("SELECT * FROM jdb_upload WHERE nompage='".$nompage."' ORDER BY lien ASC") or die ("Une erreur de purge table jdb_upload");
								if (mysql_num_rows($requeteupload) < 1)
								{
								$lien[0]='bdd vide';
								}
								else
								{
									$i=0;
									while($dataupload = mysql_fetch_assoc($requeteupload))
										{
											$lien[$i]=$dataupload['lien'];
											$i++;
										}
								}
	return $lien;
}
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="fr" lang="fr"><head>

<link rel="icon" type="image/png" href="../css/favicon.ico " />
<!--[if IE]><link rel="shortcut icon" type="image/x-icon" href="../css/favicon.ico" /><![endif]-->
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta http-equiv="content-language" content="fr">
<meta http-equiv="content-style-type" content="text/css">
<meta http-equiv="imagetoolbar" content="no">
<meta name="resource-type" content="document">
<meta name="distribution" content="global">
<meta name="copyright" content="Amaury Froment">
<meta name="keywords" content="">
<meta name="description" content="">
<title>Nettoyage du dossier upload</title>

<style type="text/css">
<!--
@import url("../css/design.css");
-->
</style>
</head>
<body bgcolor="#000000">
<table id="maintable" align="center" border="0" cellpadding="0" cellspacing="0" width="800">
						<tbody><td id="contentrow">
									<table class="tablebg" cellspacing="0" width="100%">
										<caption><div class="cap-left">
													<div class="cap-right">contenu du dossier upload
													</div>
												</div>
										</caption>
						
						
										<tbody>
											<tr>
												<td class="row1"><div style="overflow: auto; width: 100%;" >

													<table class="tablebg" cellspacing="0" width="100%">
														<tbody>
															<?php
															echo '
																		<tr class="row1" align="center">
																		 <td class="row" align="center" valign="top">
																		 <form ACTION="./menu.php">
																			<LABEL >Grand purgatoire du repertoire upload </LABEL>
																			
																			<input class="btnmain" type="submit" value="Retour" ">
																		 </form>
																		 </td>
																			
																		</tr>
																	
																		';//fin du echo
																		
																		
																	?>
															
															<td class="cat" colspan="3" align="center" width="100%">
															</td>
															
														</tbody>
													</table>
																					
												</td>
											</tr>
										</tbody>
									</table>
								</td>
						</tbody>
</table>

<table id="maintable" align="center" border="0" cellpadding="0" cellspacing="0" width="800">
						<tr>
							<th>page</th>
							<th>bdd</th>
							<th>repertoire upload</th>
						</tr>
					
					<?php
					
					for($l=0;$l<sizeof($nompage);$l++)
					{
						$page=$nompage[$l];
						$res= listupload($page);
						$lien=listbdd($page);
						
						echo('
						<tr class="row1">
							<td class="row" align="center" valign="top">
							'.$page.'
							</td>
							<td class="row" align="center" valign="top">
						');
						
						//affiche  les liens de la bdd en trop
							$resultbdd = array_diff($lien, $res);
							sort($resultbdd);
							if(sizeof($resultbdd)>0)
							{
							if(sizeof($resultbdd)>0 && $resultbdd[0]!=="bdd vide")
								{
								//transfome le tableau en string pour transmission
								$resultbdd2=implode(",",$resultbdd);
								echo '
								<FORM METHOD=post ACTION="cleanupload.php" ENCTYPE="multipart/form-data">
									<INPUT TYPE=hidden NAME=cleanbdd VALUE="'.$resultbdd2.'">
									<INPUT TYPE=hidden NAME=nompageclean VALUE="'.$page.'">
									<INPUT class="btnlite" TYPE=submit VALUE="nettoyer la bdd">
								</FORM>
								';
								}
								
							for($i=0;$i<sizeof($resultbdd);$i++)
								{
								echo($resultbdd[$i].'<br/>');
								}
							}
							else
							{
							echo('ok');
							}
							
						echo('
							</td>
							<td class="row" align="center" valign="top">
							'); 
							$resultrep = array_diff($res, $lien);
							sort($resultrep);
							if(sizeof($resultrep)>0)
							{
								if(sizeof($resultrep)>0 && $resultrep[0]!=="repertoire vide")
								{
								//transfome le tableau en string pour transmission
								$resultrep2=implode(",",$resultrep);
								echo '
								<FORM METHOD=post ACTION="cleanupload.php" ENCTYPE="multipart/form-data">
									<INPUT TYPE=hidden NAME=cleanrep VALUE="'.$resultrep2.'">
									<INPUT TYPE=hidden NAME=nompageclean VALUE="'.$page.'">
									<INPUT class="btnlite" TYPE=submit VALUE="nettoyer le repertoire">
								</FORM>
								';
								}
								for($i=0;$i<sizeof($resultrep);$i++)
									{
									echo($resultrep[$i].'<br/>');
									}
								}
								else
								{
								echo('ok');
							}
							
							echo('
						</td>
						</tr>
						
							');
							
					}
					?>
					
		
</table>
 
</body>
</html>
<?php
mysql_close();
?>