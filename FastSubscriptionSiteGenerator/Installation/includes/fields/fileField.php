<?php
	include_once("functions.php");
	include_once("Field.php");

	class fileField extends Field
	{
		private $_description='';
		private $_extensions=[];

		public function __construct($name,$description,$extensions,$required=false,$sortable=true,$filtering=true,$adminonly=false)
		{
			$this->_name=$name;
			$this->_alias0=generateAlias($name);
			$this->_alias=$this->_alias0;
			$this->_description=$description;
			$this->_required=$required;
			$this->_sortable=$sortable;
			$this->_filtering=$filtering;
			$this->_adminonly=$adminonly;
			$this->_extensions=$extensions;
		}

		public function toForm($checkinform=true)
		{
			$formfield='<p><label for="'.$this->_alias.'">
			    '.$this->_name;
			if($this->_required)
				$formfield=$formfield.'<font color="red"> * </font>';
			$formfield=$formfield.'</label>
			    <input type="file" name="'.$this->_alias.'"';
			if($this->_required)
			{
				$formfield=$formfield.' required="required" ';
			}
			$formfield=$formfield.' />';
			if(!empty($this->_extensions))
			{
				$formfield=$formfield.'(<i>';
				foreach($this->_extensions as $ext)
					$formfield=$formfield.' *.'.$ext;
				$formfield=$formfield.'</i>)';
			}
			$formfield=$formfield.'</p>'."\n";		
			return $formfield;
		}

		public function toScript()
		{
			$scriptfield='
			// taille autorisées (min & max -- en octets)
			$file_min_size = 0;
			$file_max_size = 10000000;
			// On vérifie la présence d"un fichier à uploader
			if (($_FILES["'.$this->_alias.'"]["size"] > $file_min_size) && ($_FILES["'.$this->_alias.'"]["size"] < $file_max_size)) :
			  // dossier où sera déplacé le fichier
			  $content_dir = "upload/";
			 $tmp_file = $_FILES["'.$this->_alias.'"]["tmp_name"];
			  if( !is_uploaded_file($tmp_file) ){
			   // $errors ["'.$this->_alias.'"] = "File not found";
			  }
			  // on vérifie l"extension
			  $path = $_FILES["'.$this->_alias.'"]["name"];
			  $ext = pathinfo($path, PATHINFO_EXTENSION); // on récupère l"extension'."\n";

			if(!empty($this->_extensions))
			{
			  $scriptfield=$scriptfield.'if(';

		          $nbext=count($this->_extensions);
			  $scriptfield=$scriptfield.'!strstr($ext, "'.$this->_extensions[0].'")';

			  for($i=1;$i<$nbext;$i++)
				$scriptfield=$scriptfield.'&& !strstr($ext, "'.$this->_extensions[$i].'")';


			  $scriptfield=$scriptfield.'){
			  $errors ["upfiles"] = "EXTENSION ".$ext." NOT ALLOWED";'."\n".'}';
			}
			  
			$scriptfield=$scriptfield.'
			  // Si le formulaire est validé, on copie le fichier dans le dossier de destination
			  if(empty($errors)){
			  $name_file = md5(uniqid(rand(), true)).".".$ext; // on crée un nom unique en conservant l"extension
			  if( !move_uploaded_file($tmp_file, $content_dir . $name_file) ){
			  $errors ["'.$this->_alias.'"] = "Il y a des erreurs! Impossible de copier le fichier dans le dossier cible";
			  }
			  } 
			// On récupère l"url du fichier envoyé
			  $get_the_file = $content_dir.$name_file;
			$_SESSION["'.$this->_alias.'"]=$get_the_file;

			elseif($_FILES["upfiles"]["size"] > $file_max_size):
			  $errors ["'.$this->_alias.'"] = "le fichier dépasse la limite autorisée";
			  $get_the_file = "Pas de fichier joint";
			  else: 
			  $get_the_file = "Pas de fichier joint";
			  endif;
';

	
			return $scriptfield;
		}

		public function toSQLCreate()
		{
			$sqlcreate=$this->_alias." varchar(255)";
			// do not set not null if partial data are added by the administrator
					
			return $sqlcreate;
		}

		public function toSQLExecute($source='$_SESSION')
		{
			$req='if(isset('.$source.'["'.$this->_alias.'"]))'."\n".' $reqprepared -> bindParam(":'.$this->_alias.'", '.$source.'["'.$this->_alias.'"], PDO::PARAM_STR);'."\n".' else'."\n".' $reqprepared -> bindParam(":'.$this->_alias.'",$myNull, PDO::PARAM_NULL);';
			return $req;
		}

		public function tobackFilter()
		{
			if($this->_filtering)
			{
				return '{ type: "text" }';
			}
			else
			{
				return 'null';
			}
		}
		
		public function toBackCell()
		{
			$texte="";
			if(!$this->_adminonly)
			{
				$texte='<a href="../<?php echo $row->'.($this->getAlias()).';?>" target="_new"><?php echo $row->'.($this->getAlias()).';?></a>';
			}
			else
			{
				// input fichier not managed
				$texte='<a href="../<?php echo $row->'.($this->getAlias()).';?>" target="_new"><?php echo $row->'.($this->getAlias()).';?></a>';
			}
			return $texte;
		}
		




		public function toRecap()
		{
			$scriptfield=$scriptfield.'if(isset($_SESSION["'.$this->_alias.'"])&& !empty($_SESSION["'.$this->_alias.'"])){'."\n";
			$scriptfield=$scriptfield.'echo "<tr>";'."\n";
			$scriptfield=$scriptfield.'echo "<td>'.$this->_name.':</td>";'."\n";
			$scriptfield=$scriptfield.'echo "<td><a href=\" ".$_SESSION["'.($this->_alias).'"]." \" target=\"_new\" >".$_SESSION["'.($this->_alias).'"]."</a></td>";';
			$scriptfield=$scriptfield.'echo "</tr>";'."\n";
			$scriptfield=$scriptfield.'}'."\n";
			return $scriptfield;
		}
	}
?>
