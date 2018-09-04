<?php
	include_once("Field.php");

	class radioField extends Field
	{
		private $_options='';

		public function __construct($name,$options,$required=false,$sortable=true,$filtering=true,$adminonly=false)
		{
			$this->_name=$name;
			$this->_alias0=generateAlias($name);
			$this->_alias=$this->_alias0;
			$this->_options=$options;
			$this->_required=$required;
			$this->_sortable=$sortable;
			$this->_filtering=$filtering;
			$this->_adminonly=$adminonly;
		}

		public function toForm($checkinform=true)
		{
			$formfield='<p><label for="'.$this->_alias.'">'.$this->_name;
			if($this->_required)
				$formfield=$formfield.'<font color="red"> * </font>';
			$formfield=$formfield.'</label>';
			foreach($this->_options as $option)
			{
				$requ='';
				if($this->_required)
					$requ=' required="required"';
					
				if($checkinform)
				{
					$formfield=$formfield.'<input type="radio" name="'.$this->_alias.'" <?php if(isset($_SESSION["'.$this->_alias.'"]) && !empty($_SESSION["'.$this->_alias.'"])) if($_SESSION["'.$this->_alias.'"]=="'.$option.'") echo "check=\"checked\""; ?>'.$requ.' value="'.$option.'"/>'.$option.'</p>';
				}
				else
					$formfield=$formfield.'<input type="radio" name="'.$this->_alias.'"'.$requ.' value="'.$option.'"/><p>'.$option.'</p>';
	
				
			}
			$formfield=$formfield.'</p>'."\n";		
			return $formfield;
		}

		public function toScript()
		{
			$scriptfield='if(isset($_POST["'.$this->_alias.'"]) && !empty($_POST["'.$this->_alias.'"]))'."\n".'$_SESSION["'.$this->_alias.'"]=$_POST["'.$this->_alias.'"];'."\n";
					
			return $scriptfield;
		}

		public function toSQLCreate()
		{
			$sqlcreate=$this->_alias.' ENUM(';

			$count=count($this->_options);
			for($i=0;$i<$count;$i++)
			{
				$sqlcreate=$sqlcreate.'"'.($this->_options[$i]).'"';
				if($i<$count-1)
					$sqlcreate=$sqlcreate.',';
			}


			$sqlcreate=$sqlcreate.')';

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
				$code='{ type: "select", names: [';

				$count=count($this->_options);
				for($i=0;$i<$count;$i++)
				{
					$code=$code.'"'.($this->_options[$i]).'"';
					if($i<$count-1)
						$code=$code.',';
				}

				$code=$code.']  }';
				return $code;
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
				$texte='<?php echo $row->'.($this->getAlias()).';?>';
			}
			else
			{
				foreach($this->_options as $option)
				{
					$texte=$texte.'<input type="radio" name="'.$this->_alias.'" value="'.$option.'" >'.$option;
				}
			}
			return $texte;
		}

		public function toRecap()
		{
			$scriptfield='';
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
