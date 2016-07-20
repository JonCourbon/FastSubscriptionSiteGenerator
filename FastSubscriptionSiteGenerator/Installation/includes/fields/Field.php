<?php
	class Field {
		protected $_required=false;
		protected $_sortable=true;
		protected $_name='';
		protected $_alias;
		protected $_alias0;
		protected $_filtering=true;
		protected $_adminonly=false;

		public function getName()
		{
			return $this->_name;
		}


		public function getAlias()
		{
			return $this->_alias;
		}

		public function aliasIsEqual($alias)
		{
			if($this->_alias==$alias)
				return true;
			else
				return false;
		}

		public function changeAlias($num)
		{
			$this->_alias=$this->_alias0.$num;
		}

		public function isPublic()
		{
			return !($this->_adminonly);
		}

	}
?>
