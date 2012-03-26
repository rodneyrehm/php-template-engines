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
	 * The class represents an XML tag.
	 */
	class Opt_Xml_Element extends Opt_Xml_Scannable
	{
		protected $_name;
		protected $_namespace;
		protected $_attributes;

		/**
		 * Creates a new XML tag with the specified name. The accepted
		 * name format is 'name' or 'namespace:name'.
		 *
		 * @param String $name The tag name.
		 */
		public function __construct($name)
		{
			parent::__construct();
			$this->setName($name);
		} // end __construct();

		/**
		 * Sets the name for the tag. The accepted format is 'name' or
		 * 'namespace:name'.
		 *
		 * @param String $name The tag name.
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
		 * Sets the namespace for the tag.
		 *
		 * @param String $namespace The namespace name.
		 */
		public function setNamespace($namespace)
		{
			$this->_namespace = $namespace;
		} // end setNamespace();

		/**
		 * Returns the tag name (without the namespace).
		 * @return String
		 */
		public function getName()
		{
			return $this->_name;
		} // end getName();

		/**
		 * Returns the tag namespace name.
		 * @return String
		 */
		public function getNamespace()
		{
			return $this->_namespace;
		} // end getNamespace();

		/**
		 * Returns the tag name (with the namespace, if possible)
		 *
		 * @return String
		 */
		public function getXmlName()
		{
			if(is_null($this->_namespace))
			{
				return $this->_name;
			}
			return $this->_namespace.':'.$this->_name;
		} // end getXmlName();

		/**
		 * Returns the list of attribute objects.
		 *
		 * @return Array
		 */
		public function getAttributes()
		{
			if(!is_array($this->_attributes))
			{
				return array();
			}
			return $this->_attributes;
		} // end getAttributes();

		/**
		 * Returns the attribute with the specified name.
		 *
		 * @param String $xmlName The XML name of the attribute (with the namespace)
		 * @return Opt_Xml_Attribute
		 */
		public function getAttribute($xmlName)
		{
			if(!is_array($this->_attributes))
			{
				return NULL;
			}
			if(!isset($this->_attributes[$xmlName]))
			{
				return NULL;
			}
			return $this->_attributes[$xmlName];
		} // end getAttribute();

		/**
		 * Adds a new attribute to the tag.
		 *
		 * @param Opt_Xml_Attribute $attribute The new attribute.
		 */
		public function addAttribute(Opt_Xml_Attribute $attribute)
		{
			if(!is_array($this->_attributes))
			{
				$this->_attributes = array();
			}
			$this->_attributes[$attribute->getXmlName()] = $attribute;
		} // end addAttribute();

		/**
		 * Removes the specified attribute identified either by the object
		 * or by the XML name.
		 *
		 * @param String|Opt_Xml_Attribute $refNode The attribute to be removed
		 * @return Boolean
		 */
		public function removeAttribute($refNode)
		{
			if(!is_array($this->_attributes))
			{
				return NULL;
			}
			if(is_object($refNode))
			{
				foreach($this->_attributes as $id => $node)
				{
					if($node === $refNode)
					{
						unset($this->_attributes[$id]);
						return true;
					}
				}
			}
			elseif(is_string($refNode))
			{
				if(isset($this->_attributes[$refNode]))
				{
					unset($this->_attributes[$refNode]);
					return true;
				}
			}
			return false;
		} // end removeAttribute();

		/**
		 * Clears the attribute list.
		 */
		public function removeAttributes()
		{
			$this->_attributes = array();
		} // end removeAttributes();

		/**
		 * Returns 'true', if the tag contains attributes.
		 *
		 * @return Boolean
		 */
		public function hasAttributes()
		{
			if(!is_array($this->_attributes))
			{
				return false;
			}
			return (sizeof($this->_attributes) > 0);
		} // end hasAttributes();

		/**
		 * Returns the XML tag name.
		 * @return String
		 */
		public function __toString()
		{
			return $this->getXmlName();
		} // end __toString();

		/**
		 * The method helps to clone the XML node by cloning
		 * its attributes.
		 *
		 * @internal
		 */
		protected function _cloneHandler()
		{
			if(is_array($this->_attributes))
			{
				foreach($this->_attributes as $name => $attribute)
				{
					$this->_attributes[$name] = clone $attribute;
				}
			}
		} // end _cloneHandler();

		/**
		 * Specifies, what node types can be children of XML tags.
		 *
		 * @internal
		 * @param Opt_Xml_Node $node
		 */
		protected function _testNode(Opt_Xml_Node $node)
		{
			if($node->getType() != 'Opt_Xml_Element' && $node->getType() != 'Opt_Xml_Text' && $node->getType() != 'Opt_Xml_Comment')
			{
				throw new Opt_APIInvalidNodeType_Exception('Opt_Xml_Element', $node->getType());
			}
		} // end _testNode();
	} // end Opt_Xml_Element;
