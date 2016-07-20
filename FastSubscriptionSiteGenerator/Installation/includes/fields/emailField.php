<?php
	include_once("functions.php");
	include_once("Field.php");

	class emailField extends Field
	{
		private $_description='';

		public function __construct($name,$description,$required=false,$sortable=true,$adminonly=false)
		{
			$this->_name=$name;
			$this->_alias0=generateAlias($name);
			$this->_alias=$this->_alias0;
			$this->_description=$description;
			$this->_required=$required;
			$this->_sortable=$sortable;
			$this->_adminonly=$adminonly;
		}

		public function toForm($checkinform=true)
		{
			$formfield='<p><label for="'.$this->_alias.'">
			    '.$this->_name;
			if($this->_required)
				$formfield=$formfield.'<font color="red"> * </font>';
			$formfield=$formfield.'</label>
			    <input name="'.$this->_alias.'" type="email" placeholder="'.$this->_description.'"';
			if($this->_required)
			{
				$formfield=$formfield.' required="required" ';
			}
			
			if($checkinform)
			{
				$formfield=$formfield.'<?php if(isset($_SESSION["'.$this->_alias.'"]) && !empty($_SESSION["'.$this->_alias.'"])) echo "value=".$_SESSION["'.$this->_alias.'"]; ?>';
			}
			$formfield=$formfield.' />
			</p>';		
			return $formfield;
		}

		public function toScript()
		{
			$scriptfield='if(isset($_POST["'.$this->_alias.'"]) && !empty($_POST["'.$this->_alias.'"]))'."\n".'$_SESSION["'.$this->_alias.'"]=$_POST["'.$this->_alias.'"];'."\n";
					
			return $scriptfield;
		}

		public function toSQLCreate()
		{
			$sqlcreate=$this->_alias." varchar(255) DEFAULT ''";
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
			// filter disable
			return 'null';
		}
		
		public function toBackCell()
		{
			$texte="";
			if(!$this->_adminonly)
			{
				$texte='<?php echo $row->'.($this->getAlias()).';?>';
			}
			else
			{
				$texte='<input type="email" value="<?php echo $row->'.($this->getAlias()).';?>"/>';
			}
			return $texte;
		}

		public function toRecap()
		{
			$scriptfield=$scriptfield.'if(isset($_SESSION["'.$this->_alias.'"])&& !empty($_SESSION["'.$this->_alias.'"])){'."\n";
			$scriptfield=$scriptfield.'echo "<tr>";'."\n";
			$scriptfield=$scriptfield.'echo "<td>'.$this->_name.':</td>";'."\n";
			$scriptfield=$scriptfield.'echo "<td>".$_SESSION["'.$this->_alias.'"]."</td>";'."\n";
			$scriptfield=$scriptfield.'echo "</tr>";'."\n";
			$scriptfield=$scriptfield.'}'."\n";
			return $scriptfield;
		}
	}
?>
