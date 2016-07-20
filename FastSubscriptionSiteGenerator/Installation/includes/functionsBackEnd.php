<?php
include_once('functions.php');

function createBackEndFiles($nom_site,$tname,$allfields,$login)
{

	// on inclue la librairie Twig
	require_once("vendor/twig/twig/lib/Twig/Autoloader.php");
	Twig_Autoloader::register();
	// les templates sont dans le dossier "templates"
	$loader = new Twig_Loader_Filesystem("templates");
	// on charge les éléments
	$twig = new Twig_Environment($loader, array(
	"cache" => false
	));


	/* ------------------------- 
	PAGE INDEX
	------------------------- */
	// on charge notre modèle
	$template = $twig->loadTemplate('BackEnd/index.html.twig');
	// on stocke le contenu rendu avec certains paramètres

	$contenuIndex= $template->render(array(
		'nom_site' => $nom_site
	)); 

	// on ouvre le fichier index.html (page d'acceuil qui sera affichée pour l'administrateur)
	$myfile = fopen("../administration/index.php", "w") or die("Unable to open file!");

	// on y met le contenu généré avec Twig
	fwrite($myfile, $contenuIndex);
	fclose($myfile);

	/* ------------------------- 
	PAGE LOGIN
	------------------------- */
	// on charge notre modèle
	$template = $twig->loadTemplate('BackEnd/sauthentifier.php.twig');
	// on stocke le contenu rendu avec certains paramètres

	$contenuIndex= $template->render(array(
		'login' => $login
	)); 

	// on ouvre le fichier index.html (page d'acceuil qui sera affichée pour l'administrateur)
	$myfile = fopen("../administration/sauthentifier.php", "w") or die("Unable to open file!");

	// on y met le contenu généré avec Twig
	fwrite($myfile, $contenuIndex);
	fclose($myfile);

	
	/* ------------------------- 
	PAGE ADMIN
	------------------------- */
	$tableBackEnd=createBackTable($allfields);
	$scriptBackTable=createBackTableScript($allfields);
	$storescript=createBackUpdateScript($allfields);
	
	// on charge notre modèle
	$template = $twig->loadTemplate('BackEnd/adminPage.php.twig');
	// on stocke le contenu rendu avec certains paramètres

	$contenuIndex= $template->render(array(
		'nom_site' => $nom_site,
		'tname' => $tname,
		'tablesortscript' => $scriptBackTable,
		'tablesort' => $tableBackEnd,
		'storescript' => $storescript
	)); 

	// on ouvre le fichier index.html (page d'acceuil qui sera affichée pour l'administrateur)
	$myfile = fopen("../administration/adminPage.php", "w") or die("Unable to open file!");

	// on y met le contenu généré avec Twig
	fwrite($myfile, $contenuIndex);
	fclose($myfile);
	
	
	/* ------------------------- 
	PAGE UPDATE
	------------------------- */
	$adminfields=getAdminFields($allfields);
	$updateScript=toSQLAdminUpdateReq($tname,$adminfields);
	
	// on charge notre modèle
	$template = $twig->loadTemplate('BackEnd/updateData.php.twig');
	// on stocke le contenu rendu avec certains paramètres

	$contenuIndex= $template->render(array(
		'tname' => $tname,
		'updateScript' => $updateScript
	)); 

	// on ouvre le fichier index.html (page d'acceuil qui sera affichée pour l'administrateur)
	$myfile = fopen("../administration/updateData.php", "w") or die("Unable to open file!");

	// on y met le contenu généré avec Twig
	fwrite($myfile, $contenuIndex);
	fclose($myfile);

}


?>
