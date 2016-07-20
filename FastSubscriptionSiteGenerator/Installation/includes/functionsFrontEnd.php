<?php
    include_once('functions.php');


    function createFrontEndIndexFile($title,$description,$footer)
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
	PAGE D'INDEX
	------------------------- */
	// on charge notre modèle
	$template = $twig->loadTemplate('FrontEnd/index.html.twig');
	// on stocke le contenu rendu avec certains paramètres

	$contenuIndex= $template->render(array(
		'nom_site' => $title,
		'descriptif' => $description,
		'footer' => $footer
	)); 

	// on ouvre le fichier index.html (page d'acceuil qui sera affichée pour l'administrateur)
	$myfile = fopen("../index.html", "w") or die("Unable to open file!");

	// on y met le contenu généré avec Twig
	fwrite($myfile, $contenuIndex);
	fclose($myfile);

    }

    function createFrontEndStepFile($title,$description,$footer,$fields,$etapes,$noetape,$nbsteps,$authentified=false,$authentifyfields=array(),$checkinform=true,$verticalBar=false)
    {
	$formContent=createForm($fields,$noetape,$checkinform);

	$identity="";
	if($authentified)
	{
		$identity=displayAuthenficationFields($authentifyfields);
	}

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
	PAGE D'INDEX
	------------------------- */
	// on charge notre modèle
	if(!$verticalBar)
		$template = $twig->loadTemplate('FrontEnd/step.php.twig');
	else
		$template = $twig->loadTemplate('FrontEnd/stepV.php.twig');
	// on stocke le contenu rendu avec certains paramètres
	
	$menu=generateMenu($etapes,$noetape,$verticalBar);

	$jsfunctions=createJsScriptForm($fields);

	$contenuIndex= $template->render(array(
		'nom_site' => $title,
		'descriptif' => $description,
		'menu' => $menu,
		'form' => $formContent,
		'footer' => $footer,
		'jsfunctions' => $jsfunctions,
		'identity' => $identity
	)); 

	// on ouvre le fichier index.html (page d'acceuil qui sera affichée pour l'administrateur)
	$myfile = fopen("../step".$noetape.".php", "w") or die("Unable to open file!");

	// on y met le contenu généré avec Twig
	fwrite($myfile, $contenuIndex);
	fclose($myfile);

    }

    function createFrontEndProcessFile($allfields,$noetape,$nbsteps)
    {
	$script=createActionScript($allfields);			
	
	if($noetape==$nbsteps-1)
		$destination='recapitulatif.php';
	else
		$destination='step'.($noetape+1).'.php';

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
	PAGE D'INDEX
	------------------------- */
	// on charge notre modèle
	$template = $twig->loadTemplate('FrontEnd/processForm.php.twig');
	// on stocke le contenu rendu avec certains paramètres

	$contenuIndex= $template->render(array(
		'script' => $script,
		'destination' => $destination
	)); 

	// on ouvre le fichier index.html (page d'acceuil qui sera affichée pour l'administrateur)
	$myfile = fopen("../processForm".$noetape.".php", "w") or die("Unable to open file!");

	// on y met le contenu généré avec Twig
	fwrite($myfile, $contenuIndex);
	fclose($myfile);

    }

   function createFrontEndAuthentifyFile($tname,$authentifyfields,$noetape,$nbsteps,$allfields,$checkinform=true)
    {
	$script=toSQLAuthentifyReq($tname,$authentifyfields,$noetape+1,$allfields,$checkinform);			

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
	PAGE D'INDEX
	------------------------- */
	// on charge notre modèle
	$template = $twig->loadTemplate('FrontEnd/authentifyForm.php.twig');
	// on stocke le contenu rendu avec certains paramètres

	$contenuIndex= $template->render(array(
		'script' => $script
	)); 

	// on ouvre le fichier index.html (page d'acceuil qui sera affichée pour l'administrateur)
	$myfile = fopen("../processForm".$noetape.".php", "w") or die("Unable to open file!");

	// on y met le contenu généré avec Twig
	fwrite($myfile, $contenuIndex);
	fclose($myfile);

    }
    
    
    function createFrontEndRecapFile($nom_site,$footer,$allfields,$etapes,$nostep=2,$verticalBar=false)
    {
	$recapitulatif=toRecap($allfields);


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
	PAGE D'INDEX
	------------------------- */
	// on charge notre modèle
	if(!$verticalBar)
		$template = $twig->loadTemplate('FrontEnd/recapitulatif.php.twig');
	else
		$template = $twig->loadTemplate('FrontEnd/recapitulatifV.php.twig');
		
	// on stocke le contenu rendu avec certains paramètres
	
	$menu=generateMenu($etapes,$nostep,$verticalBar);

	$contenuIndex= $template->render(array(
		'nom_site' => $nom_site,
		'step' => $nostep,
		'recapitulatif' => $recapitulatif,
		'menu' => $menu,
		'footer' => $footer
	)); 

	// on ouvre le fichier index.html (page d'acceuil qui sera affichée pour l'administrateur)
	$myfile = fopen("../recapitulatif.php", "w") or die("Unable to open file!");

	// on y met le contenu généré avec Twig
	fwrite($myfile, $contenuIndex);
	fclose($myfile);

    }


    function createFrontEndSaveFile($tname,$allfields,$authentified)
    {
		
		if(!$authentified)
		{
			$script=toSQLInsert($tname,$allfields);
		}
		else
		{
			$script=toSQLUpdateReq($tname,$allfields);
		}


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
	PAGE D'INDEX
	------------------------- */
	// on charge notre modèle
	$template = $twig->loadTemplate('FrontEnd/saveData.php.twig');
	// on stocke le contenu rendu avec certains paramètres

	$contenuIndex= $template->render(array(
		'code' => $script
	)); 

	// on ouvre le fichier index.html (page d'acceuil qui sera affichée pour l'administrateur)
	$myfile = fopen("../saveData.php", "w") or die("Unable to open file!");

	// on y met le contenu généré avec Twig
	fwrite($myfile, $contenuIndex);
	fclose($myfile);

    }


function createFrontEndValidErrorFile($title,$valid_msg,$error_msg,$etapes,$noetape=4,$verticalBar=false)
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
	PAGE D'INDEX
	------------------------- */
	// on charge notre modèle
	if(!$verticalBar)
		$template = $twig->loadTemplate('FrontEnd/validForm.php.twig');
	else
		$template = $twig->loadTemplate('FrontEnd/validFormV.php.twig');

	// on stocke le contenu rendu avec certains paramètres

	$menu=generateMenu($etapes,$noetape,$verticalBar);
	
	$contenuIndex= $template->render(array(
		'nom_site' => $title,
		'valid_msg' => $valid_msg,
		'menu' => $menu
	)); 

	// on ouvre le fichier index.html (page d'acceuil qui sera affichée pour l'administrateur)
	$myfile = fopen("../validForm.php", "w") or die("Unable to open file!");

	// on y met le contenu généré avec Twig
	fwrite($myfile, $contenuIndex);
	fclose($myfile);
	
	/* ------------------------- 
	PAGE D'INDEX
	------------------------- */
	// on charge notre modèle
	$template = $twig->loadTemplate('FrontEnd/errorForm.php.twig');
	// on stocke le contenu rendu avec certains paramètres

	$menu="";
	
	$contenuIndex= $template->render(array(
		'nom_site' => $title,
		'error_msg' => $error_msg,
		'menu' => $menu
	)); 

	// on ouvre le fichier index.html (page d'acceuil qui sera affichée pour l'administrateur)
	$myfile = fopen("../errorForm.php", "w") or die("Unable to open file!");

	// on y met le contenu généré avec Twig
	fwrite($myfile, $contenuIndex);
	fclose($myfile);

    }





?>
