<?php
	class translationInterface implements Opl_Translation_Interface
	{
		private $_original = array(
			'foo' => array('bar' => 'Value 1', 'joe' => 'Value 2'),
			'goo' => array('bar' => 'Modificable value: %s'),
		);

		private $_modified = array();

		public function _($group, $id)
		{
			if(isset($this->_modified[$group][$id]))
			{
				return $this->_modified[$group][$id];
			}
			if(isset($this->_original[$group][$id]))
			{
				return $this->_original[$group][$id];
			}
			return '';
		} // end _();
		public function assign($group, $id)
		{
			$args = func_get_args();
			unset($args[0]);
			unset($args[1]);
			if(isset($this->_original[$group][$id]))
			{
				if(!isset($this->_modified[$group]))
				{
					$this->_modified[$group] = array();
				}
				$this->_modified[$group][$id] = vsprintf($this->_original[$group][$id], $args);
			}
		} // end assign();
	} // end translationInterface;