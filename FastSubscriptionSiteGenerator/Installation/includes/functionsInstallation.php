<?php

function createConfigFile($hote,$port,$nom_bd,$id,$mdp)
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
	PAGE DE CONFIG
	------------------------- */
	// on charge notre modèle
	$template = $twig->loadTemplate('All/config.php.twig');
	// on stocke le contenu rendu avec certains paramètres

	$contenuIndex= $template->render(array(
		'hote=' => $hote,
		'port' => $port,
		'nom_bd' => $nom_bd,
		'identifiant' => $id,
		'mot_de_passe' => $mdp
	)); 

	// on ouvre le fichier accueil.html (page d'acceuil qui sera affichée pour l'utilisateur
	$myfile = fopen("../configuration/config.php", "w") or die("Unable to open file!");

	// on y met le contenu généré avec Twig
	fwrite($myfile, $contenuIndex);
	fclose($myfile);
}


function createTables($tname,$allfields)
{
	include('../configuration/config.php');
	include('../includes/connection.php');
	$requete=createSQLCreate($tname,$allfields);

	try
	{
		$connection->exec($requete);
		
	} catch (PDOException $erreur)
	{
		echo "Erruer!!!;: ".$erreur->getMessage();
	}

	
}

?>
