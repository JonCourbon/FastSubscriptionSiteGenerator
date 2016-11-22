<?php
	function extractAllData($content)
	{
		$content=json_decode($content, true);
        if(!is_array($content))
        {
            echo "The JSON content is not well formatted, please check and try again!";
            exit();
        }
        
		$type=$content["type"];
		$steps=$content["steps"];
		
		$nbsteps=count($steps);
		$allfields=array();
		$allsubscriptionfields=array();
		$allauthentificationfields=array();
		$stepsdata=array();
		$fieldsbysteps=array();

		foreach($steps as $stepcontent)
		{
			$checkinform=true;
			if(isset($stepcontent["checkinform"]))
				$checkinform=$stepcontent['checkinform'];
			
			$stepsdata[]=array("name" =>$stepcontent["name"],"type" =>$stepcontent["type"], "checkinform"=>$checkinform);
				
			
					
			$fieldsbysteps[]=array();
			foreach($stepcontent["fields"] as $field)
			{
				$newfield=null;
				$required=false;
				$sortable=true;
				$filtering=true;
				$adminonly=false;
				if(isset($field['required']))
					$required=$field['required'];
				if(isset($field['sortable']))
					$sortable=$field['sortable'];
				if(isset($field['filtering']))
					$filtering=$field['filtering'];
				if(isset($field['adminonly']))
					$adminonly=$field['adminonly'];
					
				if(strcmp($stepcontent["type"],"authentification")==0)
					$required=true;
				if($adminonly)
					$required=false;


				if($field['type']=="string")
				{
					$newfield=new stringField($field['name'],$field['description'],$required,$sortable,$filtering,$adminonly);
				}
				elseif($field['type']=="number")
				{
					$newfield=new numberField($field['name'],$field['description'],$required,$sortable,$filtering,$adminonly);
				}
				elseif($field['type']=="radio")
				{
					$newfield=new radioField($field['name'],$field['options'],$required,$sortable,$filtering,$adminonly);
				}
				elseif($field['type']=="select")
				{
					$newfield=new selectField($field['name'],$field['options'],$required,$sortable,$filtering,$adminonly);
				}
				elseif($field['type']=="tel")
				{
					$newfield=new telField($field['name'],$field['description'],$required,$sortable,$filtering,$adminonly);
				}
				elseif($field['type']=="url")
				{
					$newfield=new urlField($field['name'],$field['description'],$required,$sortable,$filtering,$adminonly);
				}
				elseif($field['type']=="email")
				{
					$newfield=new emailField($field['name'],$field['description'],$required,$sortable,$adminonly);
				}
				elseif($field['type']=="date")
				{
					$newfield=new dateField($field['name'],$field['description'],$required,$sortable,$filtering,$adminonly);
				}
				elseif($field['type']=="file")
				{
					$extensions=[];
					if(isset($field['extensions']))
						$extensions=$field['extensions'];
					$newfield=new fileField($field['name'],$field['description'],$extensions,$required,$sortable,$filtering,$adminonly);
				}

				if($newfield)
				{
					$newfield=checkAlias($newfield,$allfields);
					$allfields[]=$newfield;
					
					$fieldsbysteps[count($fieldsbysteps)-1][]=$newfield;
					
					if(strcmp($stepcontent["type"],"subscription")==0)
						$allsubscriptionfields[]=$newfield;
					else
						$allauthentificationfields[]=$newfield;
				}
			}
		}
		
		

		$extracteddata=array();
		$extracteddata["type"]=$type;
		$extracteddata["steps"]=$steps;

		$extracteddata["allfields"]=$allfields;
		$extracteddata["nbsteps"]=$nbsteps;
		$extracteddata["stepsdata"]=$stepsdata;
		$extracteddata["fieldsbysteps"]=$fieldsbysteps;
		$extracteddata["allauthentificationfields"]=$allauthentificationfields;
		$extracteddata["allsubscriptionfields"]=$allsubscriptionfields;

		return $extracteddata;
	}
	
	function removeFrontBackFiles()
	{
		$files=array_merge(listFiles('html','../'),listFiles('php','../'),listFiles('php','../configuration'),listFiles('','../upload'),listFiles('php','../administration'));
		foreach($files as $filename)
		{
			unlink($filename);
		}
	}
	
	function listFiles($extension="php",$dossier)
	{
		$thelist=array();
		if ($handle = opendir($dossier)) {
			while (false !== ($file = readdir($handle)))
			{
				if ($file != "." && $file != ".." && ($extension=="" || ($extension!="" && strtolower(substr($file, strrpos($file, '.') + 1)) == $extension)))
				{
					$thelist[]=$dossier."/".$file;
				}
			}
			closedir($handle);
		}
		return $thelist;
	}


    function generateAlias($str)
    {
	// ajout d'une lettre au début si ça commence par un chiffre
	if(ctype_digit (substr($str,0,1)))
		$str="ABC_".$str;

	// on passe tout en minuscule
	$str = strtolower(trim($str));
	// transformer les caractères accentués en entités HTML
	$charset='utf-8'; // on a html en UTF8
	$str = htmlentities($str, ENT_NOQUOTES, $charset);
	    // remplacer les entités HTML pour avoir juste le premier caractères non accentués
	    // Exemple : "&ecute;" => "e", "&Ecute;" => "E", "Ã " => "a" ...
	    $str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);


	// Remplacer les ligatures tel que : Œ, Æ ...
	// Exemple "Å“" => "oe"
	$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
	// Supprimer tout le reste
	$str = preg_replace('#&[^;]+;#', '', $str);

	$str = preg_replace('/[^a-z0-9-]/', '-', $str);
	$str = preg_replace('/-+/', "-", $str);

	// pas de - en SQL
	$str = str_replace("/","_",$str);
	$str = str_replace("+","_",$str);
	$str = str_replace("*","_",$str);
	$str = str_replace("-","_",$str);

	// on coupe à 20 caracteres max (hors ajout plus tard)
	$str=substr ($str , 0 , 20);

    	return $str;
    }

    function checkAlias($field,$allfields)
    {
	$samealias=true;
	$num=1;
	while($samealias==true)
	{
		$alias=$field->getAlias();
		$samealias=false;
		foreach($allfields as $afield) // to be changed to while loop
		{
			if($afield->aliasIsEqual($alias))
				$samealias=true;
		}
		if($samealias)
		{
			$field->changeAlias($num);
			$num++;
		}
	}
	return $field;
    }


    function createForm($allfields,$noetape,$legend,$checkinform=true)
    {
	$form='<form action="processForm'.$noetape.'.php" method="post" enctype="multipart/form-data" >'."\n"; // enctype="multipart/form-data" for files
	$form=$form.'<fieldset>'."\n";
	$form=$form.'<legend>'.$legend.'</legend>'."\n";
	foreach ($allfields as &$field) {
	    if($field->isPublic())
	    {
			$form=$form.($field->toForm($checkinform));
		}
	}
	$form=$form.'<input type="submit" name="BtnSubmit" value="Send data">'."\n";
	$form=$form.'</fieldset>'."\n".'</form>';
	$form=$form.'<font color="red"> * </font>: Mandatory field';
	return $form;
    }

    function createJsScriptForm($allfields)
    {
	$form='';
	foreach ($allfields as &$field) {
	    if(strcmp(get_class($field),"dateField")==0)
	    {
			$form=$form.($field->getJsScriptForm())."\n";
		}
	}
	if(strcmp($form,"")!=0)
	{
		$formcomplete='<script type="text/javascript" charset="utf-8">'."\n";
		$formcomplete=$formcomplete.'$(function() {'."\n";
		$formcomplete=$formcomplete.$form."\n";
		$formcomplete=$formcomplete.'});'."\n";
		$formcomplete=$formcomplete.'</script>';
		$form=$formcomplete;
	}
	
	return $form;
    }

    function createActionScript($allfields)
    {
	$script='';
	foreach ($allfields as &$field) {
	    if($field->isPublic())
		$script=$script.($field->toScript())."\n";
	}

	$script=$script."\n";
	return $script;
    }

    function createSQLCreate($tname,$allfields)
    {
	$script='DROP TABLE IF EXISTS '.$tname.'; CREATE TABLE '.$tname.'(
		id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,';

	$count=count($allfields);
	for ($i = 0; $i < $count; $i++)
	{
	    $script=$script.($allfields[$i]->toSQLCreate());
 	    if($i<$count-1)
		$script=$script.",";
	    $script=$script."\n";
	}

	$script=$script.')'."\n";
	return $script;
    }
    
    
    function fromDBToSession($tname,$allfields)
    {
		$code='$requete="SELECT * FROM '.$tname.' WHERE id=".$_SESSION["id"];'."\n";
		$code=$code.'$resultats = $connection->query($requete);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$ligne=$resultats->fetch();'."\n";
		foreach ($allfields as &$field) {
			$code=$code.'$_SESSION["'.$field->getAlias().'"]=$ligne->'.$field->getAlias().";\n";
		}
		
		return $code;
	}


    function createBackTableRaw($allfields)
    {
	// columns
	$code='<tbody>'."\n";
	$code=$code.'<?php'."\n";
	$code=$code.'	foreach($tab as $row):'."\n";
	$code=$code.'?>'."\n";
	$code=$code.'		<tr id="<?php echo $row->id;?>">'."\n";
	foreach ($allfields as &$field) {
		$code=$code.'			<td>'.($field->toBackCell()).'</td>'."\n";
	}
	$code=$code.'		<td id="btn">Save</td>'."\n"; // for administrator
	$code=$code.'		</tr>'."\n";
	$code=$code.'<?php'."\n";
	$code=$code.'	endforeach;'."\n";
	$code=$code.'?>'."\n";
	$code=$code.'</tbody>'."\n";

	return $code;
     }
     
     
     function generateMenu($steps,$no,$verticalBar)
     {
		 if(!$verticalBar)
			$typemenu='-H';
		 else
			$typemenu='-V';
			
		 $code='<div class="checkout-wrap'.$typemenu.'">
				<ul class="checkout-bar'.$typemenu.'">';
		  $nbsteps=count($steps);
		  for($i=0;$i<$nbsteps;$i++)
		  {
				$class="";
				if($i==$no)
					$class="active";
				elseif($i==$no+1)
					$class="next";
				elseif($i<$no)
					$class="visited";
				$code=$code.'<li class="'.$class.'">'.$steps[$i].'</li>';
		 }
	$code=$code.'</ul>
		</div>';
	  
	  return $code;
	 }


    function createBackTable($allfields)
    {
	$tablecontent=createBackTableRaw($allfields);

	// columns
	$thfields='<tr>'."\n";
	foreach ($allfields as &$field) {
	    $thfields=$thfields.'<th>'.($field->getName()).'</th>'."\n";
	}	
	$thfields=$thfields.'<th></th>'."\n"; // for administrator

	$thfields=$thfields.'</tr>'."\n";


	$thetable='<table cellpadding="0" cellspacing="0" border="0" class="display" id="mytable">'."\n".
		'<thead>'."\n";
	$thetable=$thetable.$thfields;
	$thetable=$thetable.'</thead>'."\n";
	$thetable=$thetable.$tablecontent;
	$thetable=$thetable.'<tfoot>';
	$thetable=$thetable.$thfields;
	$thetable=$thetable.'</tfoot>'."\n"."<tbody>"."\n"."</tbody>"."\n"."</table>";

	return $thetable;
     }

function createBackTableScript($allfields)
{
	$script='var table=$("#mytable").dataTable({
        dom: \'Bfrtip\',
        buttons: [
            \'copyHtml5\',
            \'excelHtml5\',
            \'csvHtml5\',
            {
				extend: \'pdfHtml5\',
				orientation: \'landscape\',
				download: \'open\'
			}
        ],
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    })'."\n";
    
	$script=$script.'.columnFilter({
			sPlaceHolder: "footer:before",
			aoColumns: [';

	$count=count($allfields);
	for ($i = 0; $i < $count; $i++)
	{
	    $script=$script.($allfields[$i]->tobackFilter());
 	    if($i<$count-1)
			$script=$script.",";
	    $script=$script."\n";
	}
	$script=$script.','."\n".'null'; // for administrator
	$script=$script.']

		});';

	return $script;
}


   function createBackUpdateScript($allfields)
   {
	   $storescript="";
	   $jsondata="";
	   // get fields
	   $fieldno=0;
		foreach ($allfields as &$field) {
			if(!$field->isPublic())
			{
				$storescript=$storescript.'var col_'.($field->getAlias()).'='.$fieldno.';'."\n";
				$storescript=$storescript.'var '.($field->getAlias()).'=tr.find(\'td:eq(\'+col_'.($field->getAlias()).'+\')\').find("input").val();'."\n";
				if($jsondata!="")
					$jsondata=$jsondata.',';
				$jsondata=$jsondata.($field->getAlias()).':'.($field->getAlias());
			}
			$fieldno++;
		}
		$jsondata=$jsondata.',id:id';
		
		$storescript=$storescript.'$.post( "updateData.php", {'.$jsondata.' } )
			  .done(function( data ) {
				alert( "Data stored ! ");
			  });';	
		return $storescript;
		
   }


    function toSQLPrepareReq($tname,$allfields)
    {
	$startReq='$reqprepared = $connection->prepare ("INSERT INTO '.$tname.'(';
	$endReq=' VALUES(';

	$count=count($allfields);
	for ($i = 0; $i < $count; $i++)
	{
	    $startReq=$startReq.($allfields[$i]->getAlias());
 	    if($i<$count-1)
		$startReq=$startReq.",";

	    $endReq=$endReq.':'.($allfields[$i]->getAlias());
 	    if($i<$count-1)
		$endReq=$endReq.",";

	}

	$req=$startReq.') '.$endReq.')");';

	return $req;
    }
    
    function toSQLPrepareUpdateReq($tname,$updatefields)
    {
	$req='$reqprepared = $connection->prepare("UPDATE '.$tname.' SET ';

	$count=count($updatefields);
	for ($i = 0; $i < $count; $i++)
	{
	    $req=$req.($updatefields[$i]->getAlias()).'='.':'.($updatefields[$i]->getAlias());
 	    if($i<$count-1)
		$req=$req.",";
	}

	$req=$req.' WHERE id=:id");';

	return $req;
    }
    
    
    function toSQLPrepareAuthentifyReq($tname,$authfields)
    {
	$req=createActionScript($authfields)."\n";
	$req=$req.'$reqprepared = $connection->prepare("SELECT id FROM '.$tname.' WHERE ';

	$count=count($authfields);
	for ($i = 0; $i < $count; $i++)
	{
	    $req=$req.($authfields[$i]->getAlias()).'='.':'.($authfields[$i]->getAlias());
 	    if($i<$count-1)
		$req=$req." AND ";
	}

	$req=$req.'");';

	return $req;
    }

    function toSQLExecute($allfields)
    {
	$arr='';
	foreach ($allfields as &$field) {
	    $arr=$arr.($field->toSQLExecute())."\n";
	}
	return $arr;
    }
    
    function toSQLExecuteSource($allfields,$source)
    {
	$arr='';
	foreach ($allfields as &$field) {
	    $arr=$arr.($field->toSQLExecute($source))."\n";
	}
	return $arr;
    }
    

    function toSQLInsert($tname,$allfields)
    {
	$req=toSQLPrepareReq($tname,$allfields)."\n".toSQLExecute($allfields)."\n";
	
	$req=$req.'try{
		$reqprepared->execute();
		header("Location: validForm.php?id=".$connection->lastInsertId()); 
		exit();
		
	} catch (PDOException $erreur)
	{
		header("Location: errorForm.php?error=".$erreur->getMessage()); 
		exit();
	}';
	return $req;
    }	
    
    
    function toSQLUpdateReq($tname,$udpateFields)
    {
		$req=toSQLPrepareUpdateReq($tname,$udpateFields)."\n".toSQLExecute($udpateFields)."\n";
		$req=$req.'$reqprepared -> bindParam(":id", $_SESSION["id"], PDO::PARAM_INT);'."\n";
	
	$req=$req.'try{
		$reqprepared->execute();
		header("Location: validForm.php?id=".$_SESSION["id"]); 
		exit();
		
	} catch (PDOException $erreur)
	{
		header("Location: errorForm.php?error=".$erreur->getMessage()); 
		exit();
	}';
	return $req;
    }
    
    
    function toSQLAdminUpdateReq($tname,$adminFields)
    {
		$req=toSQLPrepareUpdateReq($tname,$adminFields)."\n".toSQLExecuteSource($adminFields,'$_POST')."\n";
		$req=$req.'$reqprepared -> bindParam(":id", $_POST["id"], PDO::PARAM_INT);'."\n";
	
	$req=$req.'try{
		$reqprepared->execute();
		echo 1;
		exit();
		
	} catch (PDOException $erreur)
	{
		echo 0;
	}';
	return $req;
    }
    
    
    function displayAuthenficationFields($authfields)
    {
		$msg="<div>Welcome ";
		$msg=$msg.'<?php'."\n";
		$count=count($authfields);
		for ($i = 0; $i < $count; $i++)
		{
			$msg=$msg.'echo $_SESSION["'.($authfields[$i]->getAlias()).'"];';
			if($i<$count-1)
			$msg=$msg.'echo ",";';
		}
		$msg=$msg.'?>'."\n";
		$msg=$msg."</div>";
		return $msg;
	}
    
    
    function toSQLAuthentifyReq($tname,$authfields,$nextStep,$allfields,$checkinform=true)
    {

	$msg=toSQLPrepareAuthentifyReq($tname,$authfields)."\n".toSQLExecute($authfields)."\n";
	
	$msg=$msg.'try{
		$reqprepared->execute();
		$ret=$reqprepared->fetch();
		if($ret)
		{
			$_SESSION["id"]=$ret[0];'."\n";
	if($checkinform)
	{
		// seek in database and peuplate SESSION
		$script=fromDBToSession($tname,$allfields);
		$msg=$msg.$script;
	}
	$msg=$msg.'header("Location: step'.$nextStep.'.php"); 
			exit();
		}
		else
		{
			$erreur="Authetification error, you are not in the database";
			header("Location: errorForm.php?error=".$erreur); 
			exit();
		}
		
	} catch (PDOException $erreur)
	{
		header("Location: errorForm.php?error=".$erreur->getMessage()); 
		exit();
	}';
	return $msg;
    }	
    	  

    function toRecap($allfields)
    {
	$arr='<table class="tableau_recap">'."\n";
	$arr=$arr.'<?php'."\n";
	$arr=$arr.'echo "<tr><th></th><th></th></tr>";';
	foreach ($allfields as &$field) {
	    if($field->isPublic())
			$arr=$arr.($field->toRecap());
	}
	$arr=$arr.'?>'."\n".'</table>'."\n";
	return $arr;
    }
    
    function getAdminFields($allfields)
    {
		$adminfields=array();
		foreach ($allfields as $field) {
			if(!$field->isPublic())
				$adminfields[]=$field;
		}
		return $adminfields;
    }


?>
