<?php
/*
 * buildWebsite.php
 * 
 * Part of the FastSubscriptionSiteGenerator library
 * 
 * Copyright 2016 JCourbon <jonathan.courbon@udamail.fr>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Installation</title>
	<meta charset="utf-8"/>
<body>
	<h1>Installation</h1>
<?php
// functions
include_once("includes/functions.php");
include_once("includes/functionsBackEnd.php");
include_once("includes/functionsFrontEnd.php");
include_once("includes/functionsInstallation.php");

// fields type
include_once('includes/fields/stringField.php');
include_once('includes/fields/numberField.php');
include_once('includes/fields/radioField.php');
include_once('includes/fields/selectField.php');
include_once('includes/fields/urlField.php');
include_once('includes/fields/telField.php');
include_once('includes/fields/emailField.php');
include_once('includes/fields/dateField.php');
include_once('includes/fields/fileField.php');


/*********************************/
/*   FORM */
/*********************************/
// Informations
$title=$_POST['title'];
$description=$_POST['description'];
$footer=$_POST['footer'];

// Database
$host=$_POST['host'];
$port=$_POST['port'];
$login=$_POST['login'];
$password=$_POST['password'];
$dbname=$_POST['dbname'];
$tname=$_POST['tname'];

// back-end
$loginbe=$_POST['loginbe'];

// content
$content=$_POST['content'];

if(!empty(trim($content)))
{
	
	// personalisation
	$valid_msg=$_POST['valid_msg'];
	$error_msg=$_POST['error_msg'];
	
	
	/*********************************/
	/*   PARSE CONTENT */
	/*********************************/
	
	$extracteddata=extractAllData($content);
	$steps=$extracteddata["steps"];
	$allfields=$extracteddata["allfields"];
	$nbsteps=$extracteddata["nbsteps"];
	$stepsdata=$extracteddata["stepsdata"];
	$fieldsbysteps=$extracteddata["fieldsbysteps"];
	$allauthentificationfields=$extracteddata["allauthentificationfields"];
	$allsubscriptionfields=$extracteddata["allsubscriptionfields"];

	/*********************************/
	/*   BUILD DATABASE */
	/*********************************/

	// test database connection
	$res=testDBAccess($host,$port,$dbname,$login,$password);
	if($res==0)
	{
		echo "enable to access database with the provided parameters"."<br/>";
		exit();
	}

	// create database
	echo "1) Database generation"."<br/>";
	// database connection file
	createConfigFile($host,$port,$dbname,$login,$password);
	echo " ... configuration/config.php done"."<br/>";
	// create table
	createTables($tname,$allfields);
	echo " ... SQL database created"."<br/>";


	// front-end !!!
    $etapes=array();
    for($i=0;$i<$nbsteps;$i++)
		$etapes[]=$stepsdata[$i]["name"];
    $etapes[]="Summary";
    $etapes[]="Validation";
    $etapes[]="Complete";
    
    $verticalBar=false;
    if($nbsteps>=3)
		$verticalBar=true;
		
	echo "2) Front-end generation"."<br/>";
	createFrontEndIndexFile($title,$description,$footer);
	echo " ... index.html created"."<br/>";
	echo $nbsteps." steps<br/>";
	 // pour chaque étape
	$authentified=false;
	for($noetape=0;$noetape<$nbsteps;$noetape++)
	{
		createFrontEndStepFile($title,$description,$footer,$fieldsbysteps[$noetape],$etapes,$noetape,$nbsteps,$authentified,$allauthentificationfields,$stepsdata[$noetape]["checkinform"],$verticalBar);
		echo " ... step".$noetape.".html created"."<br/>";
		if(strcmp($stepsdata[$noetape]["type"],"subscription")==0)
		{
			createFrontEndProcessFile($fieldsbysteps[$noetape],$noetape,$nbsteps);
			echo " ... processForm".$noetape.".php created"."<br/>";
		}
		else
		{
			createFrontEndAuthentifyFile($tname,$fieldsbysteps[$noetape],$noetape,$nbsteps,$allfields,$stepsdata[$noetape]["checkinform"]);
			echo " ... processForm".$noetape.".php created (authentification!!)"."<br/>";
			$authentified=true;
		}
		
	}
	createFrontEndRecapFile($title,$footer,$allfields,$etapes,2,$verticalBar);
	echo " ... recapitulatif.php created"."<br/>";
	createFrontEndSaveFile($tname,$allsubscriptionfields,$authentified);
	echo " ... saveData.php created"."<br/>";
	createFrontEndValidErrorFile($title,$valid_msg,$error_msg,$etapes,2+$nbsteps,$verticalBar);
	echo " ... errorForm.php and validForm.php files created"."<br/>";

	// back-end !!!
	echo "3) Back-end generation"."<br/>";
	createBackEndFiles($title,$tname,$allfields,$loginbe);
	echo " ... done"."<br/>";
	
	echo "Visit:<br/>";
	echo "<ul>";
	
	$pageURL = 'http';
	if(!isset($_SERVER["HTTPS"]))
		$_SERVER["HTTPS"]="off";
		
    if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") 
    {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
    } 
    else 
    {
        $pageURL .= $_SERVER["SERVER_NAME"];
    }
    $pageURL .= dirname($_SERVER['REQUEST_URI']);

	$url =  $pageURL ."/../";
	$escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
	echo '<li>Front-end: <a href="' . $escaped_url . '">' . $escaped_url . '</a></li>';
	$url =  $pageURL ."/../administration";
	$escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
	echo '<li>Back-end: <a href="' . $escaped_url . '">' . $escaped_url . '</a></li>';
	echo "</ul>";

	// save configuration
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('UTC');
	$today = date("Y-m-d H:i:s");

	$configfile = fopen("configuration.php", "w") or die("Unable to open file!");
	fwrite($configfile, "Site generated using FastSubscriptionSiteGenerator the ".$today."\n");
	fwrite($configfile, "Title: ".$title."\n");
	fwrite($configfile, "Description: ".$description."\n");
	fwrite($configfile, "Footer: ".$footer."\n");
	fwrite($configfile, "Database: "."\n");
	fwrite($configfile, "   Host: ".$host."\n");
	fwrite($configfile, "   Port: ".$port."\n");
	fwrite($configfile, "   Host: ".$host."\n");
	fwrite($configfile, "   Login: ".$login."\n");
	fwrite($configfile, "   Password: ".$password."\n");
	fwrite($configfile, "   Db name: ".$dbname."\n");
	fwrite($configfile, "   Table name: ".$tname."\n");
	fwrite($configfile, "Login backend: ".$loginbe."\n");
	fwrite($configfile, "Content: ".$content."\n");
	fclose($configfile);


	// rename installation folder
	rename("../Installation","../Installationpack");
	
}
else
{
	echo "Empty content, not able to generate the website";
}
	
	
?>

</body>
</html>
