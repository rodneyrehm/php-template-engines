<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 * 
 */

	class Opt_Xml_Prolog
	{
		private $_attributes;
		private $_dynamic = array('version' => false, 'standalone' => false, 'encoding' => false);

		/**
		 * Constructs the prolog from the specified attribute list. The unknown prolog
		 * attributes are ignored.
		 *
		 * @param Array $attributes The attribute list.
		 */
		public function __construct($attributes = null)
		{
			if(is_null($attributes))
			{
				$this->_attributes = array(
					'version' => '1.0',
					'standalone' => 'yes'
				);
			}
			elseif(is_array($attributes))
			{
				foreach($attributes as $name => $value)
				{
					$this->setAttribute($name, $value);
				}
			}
		} // end __construct();

		/**
		 * Sets the prolog attribute value.
		 *
		 * @param String $name The attribute name
		 * @param String $value The attribute value
		 * @param Boolean $dynamic Is the attribute value dynamic?
		 */
		public function setAttribute($name, $value, $dynamic = false)
		{
			if($name == 'version' || $name == 'standalone' || $name == 'encoding')
			{
				$this->_attributes[$name] = $value;
				$this->_dynamic[$name] = $dynamic;
			}
		} // end setAttribute();

		/**
		 * Returns the attribute value.
		 *
		 * @param String $name The attribute name.
		 * @return String
		 */
		public function getAttribute($name)
		{
			if(!isset($this->_attributes[$name]))
			{
				return NULL;
			}
			return $this->_attributes[$name];
		} // end getAttribute();

		/**
		 * Sets the dynamic flag state to the attribute.
		 * @param String $name The attribute name
		 * @param Boolean $state The new state
		 */
		public function setDynamic($name, $state)
		{
			if($name == 'version' || $name == 'standalone' || $name == 'encoding')
			{
				$this->_dynamic[$name] = $state;
			}
		} // end setDynamic();

		/**
		 * Returns true, if the attribute value is dynamic.
		 * @param String $name The attribute name.
		 * @return Boolean
		 */
		public function isDynamic($name)
		{
			if(!isset($this->_dynamic[$name]))
			{
				return NULL;
			}
			return $this->_dynamic[$name];
		} // end isDynamic();

		/**
		 * Returns the attribute list.
		 * @return Array
		 */
		public function getAttributes()
		{
			return $this->_attributes;
		} // end getAttributes();

		/**
		 * Constructs an XML prolog from the attributes.
		 * @return String
		 */
		public function getProlog()
		{
			$code = '<?xml ';
			foreach($this->_attributes as $name => $value)
			{
				if($this->_dynamic[$name])
				{
					$code .= $name.'="<?php echo '.$value.'; ?>" ';
				}
				else
				{
					$code .= $name.'="'.$value.'" ';
				}
			}
			return $code.'?>';
		} // end getProlog();
	} // end Opt_Xml_Prolog;