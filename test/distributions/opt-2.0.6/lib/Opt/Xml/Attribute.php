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
 */

	/**
	 * XML Attribute implementation for OPT compiler.
	 */
	class Opt_Xml_Attribute extends Opt_Xml_Buffer
	{
		protected $_namespace;
		protected $_name;
		protected $_value;

		/**
		 * Constructs a new attribute object with the specified name and value.
		 *
		 * @param String $name Attribute name (with optional namespace in XML notation)
		 * @param String $value Attribute value.
		 */
		public function __construct($name, $value)
		{
			$this->setName($name);
			$this->_value = $value;
		} // end __construct();

		/**
		 * Sets the specified attribute name, with optional namespace
		 * in XML notation included, i.e. "ns:name".
		 *
		 * @param String $name New attribute name
		 */
		public function setName($name)
		{
			if(strpos($name, ':') !== false)
			{
				$data = explode(':', $name);
				$this->_name = $data[1];
				$this->_namespace = $data[0];
			}
			else
			{
				$this->_name = $name;
			}
		} // end setName();

		/**
		 * Sets the namespace.
		 *
		 * @param String $namespace Namespace
		 */
		public function setNamespace($namespace)
		{
			$this->_namespace = $namespace;
		} // end setNamespace();

		/**
		 * Sets the attribute value. Note that the attributes hold the values
		 * with the entities replaced to the corresponding characters.
		 *
		 * @param String $value The new value.
		 */
		public function setValue($value)
		{
			$this->_value = $value;
		} // end setValue();

		/**
		 * Returns the attribute name, without the namespace.
		 *
		 * @return String The attribute name (without namespace)
		 */
		public function getName()
		{
			return $this->_name;
		} // end getName();

		/**
		 * Returns the attribute namespace.
		 *
		 * @return String The attribute namespace
		 */
		public function getNamespace()
		{
			return $this->_namespace;
		} // end getNamespace();

		/**
		 * Returns the attribute name with the namespace prepended, if possible.
		 *
		 * @return String The attribute name with namespace.
		 */
		public function getXmlName()
		{
			if(is_null($this->_namespace))
			{
				return $this->_name;
			}
			else
			{
				return $this->_namespace.':'.$this->_name;
			}
		} // end getXmlName();

		/**
		 * Returns the current attribute value. It does not replace
		 * the characters with entities.
		 *
		 * @return String The value
		 */
		public function getValue()
		{
			return $this->_value;
		} // end getValue();

		/**
		 * The same effect, as getValue().
		 *
		 * @return String The value
		 */
		public function __toString()
		{
			return $this->_value;
		} // end __toString();		
	} // end Opt_Xml_Attribute;
