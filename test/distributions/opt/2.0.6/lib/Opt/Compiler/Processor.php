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

	class Opt_Compiler_Processor
	{
		// Attribute types
		const STRING = 1;
		const HARD_STRING = 2;
		const NUMBER = 3;
		const EXPRESSION = 4;
		const ASSIGN_EXPR = 5;
		const ID = 6;
		const BOOL = 7;
		const ID_EMP = 8; // Same as "ID", but allows empty content.
		
		const REQUIRED = 1;
		const OPTIONAL = 2;
	
		// Class fields
		/**
		 * The compiler object.
		 *
		 * @var Opt_Compiler_Class
		 */
		protected $_compiler;
		/**
		 * The main class object.
		 *
		 * @var Opt_Class
		 */
		protected $_tpl;

		/**
		 * The processor name (see getName() description for details).
		 * @var String
		 */
		protected $_name;
		private $_queue = NULL;
		private $_instructions = array();
		private $_attributes = array();

		/**
		 * Creates a new instruction processor for the specified compiler.
		 * 
		 * @param Opt_Compiler_Class $compiler The compiler object.
		 */
		public function __construct(Opt_Compiler_Class $compiler)
		{
			$this->_compiler = $compiler;
			$this->_tpl = Opl_Registry::get('opt');
			
			$this->configure();
		} // end __construct();

		/**
		 * Called during the processor initialization. It allows to define
		 * the list of instructions and attributes supported by this processor.
		 */
		public function configure()
		{
			/* null */
		} // end configure();

		/**
		 * Resets the processor state after compiling the template. The default
		 * implementation is empty.
		 */
		public function reset()
		{
			/* null */
		} // end reset();

		/**
		 * This method is called automatically for each XML element that the
		 * processor has registered. It can handle many instructions tags, and
		 * the default implementation redirects the processing to the private
		 * user-created methods _processTagName for "opt:tagName".
		 *
		 * @param Opt_Xml_Node $node The node to be processed.
		 */
		public function processNode(Opt_Xml_Node $node)
		{
			$name = '_process'.ucfirst($node->getName());
			$this->$name($node);
		} // end processNode();

		/**
		 * This method is called automatically for each XML element that the
		 * processor has registered during the postprocessing, if the instruction
		 * requested this by setting the "postprocess" variable to "true" in the
		 * node. It can handle many instructions tags, and
		 * the default implementation redirects the processing to the private
		 * user-created methods _postprocessTagName for "opt:tagName".
		 *
		 * @param Opt_Xml_Node $node The node to be postprocessed.
		 */
		public function postprocessNode(Opt_Xml_Node $node)
		{
			$name = '_postprocess'.ucfirst($node->getName());
			$this->$name($node);
		} // end postprocessNode();

		/**
		 * This method is called automatically for each XML attribute that the
		 * processor has registered. It can handle many attributes, and the
		 * default implementation redirects the processing to the private
		 * user-created methods _processAttrName for "opt:name" attribute.
		 *
		 * @param Opt_Xml_Node $node The node that contains the attribute.
		 * @param Opt_Xml_Attribute $attr The attribute to be processed.
		 */
		public function processAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$name = '_processAttr'.ucfirst($attr->getName());
			$this->$name($node, $attr);
		} // end processAttribute();

		/**
		 * This method is called automatically for each XML attribute that the
		 * processor has registered during the postprocessing, if the instruction
		 * requested this by setting the "postprocess" variable to "true" in the
		 * attribute. It can handle many attributes, and the
		 * default implementation redirects the processing to the private
		 * user-created methods _postprocessAttrName for "opt:name" attribute.
		 *
		 * @param Opt_Xml_Node $node The node that contains the attribute.
		 * @param Opt_Xml_Attribute $attr The attribute to be processed.
		 */
		public function postprocessAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$name = '_postprocessAttr'.ucfirst($attr->getName());
			$this->$name($node, $attr);
		} // end postprocessAttribute();

		/**
		 * Processes the $system special variable call. OPT chooses the processor via
		 * the second part of the call. For example, if the processor's method getName()
		 * returns the name "foo", the variables "$system.foo.something" are be redirected
		 * to that processor. The method must return a valid PHP code that replaces the
		 * specified call.
		 *
		 * @param Array $opt The $system special variable already splitted into array.
		 * @return String The output PHP code
		 */
		public function processSystemVar($opt)
		{
			/* null */
		} // end processSystemVar();

		/**
		 * Returns the processor name. The name should be a valid identifier
		 * (the first character must be a letter or underline, the next ones
		 * may also contain numbers). The name is read from the protected
		 * property $_name;
		 *
		 * @final
		 * @internal
		 * @return String
		 */
		final public function getName()
		{
			return $this->_name;
		} // end getName();

		/**
		 * Returns the queue of children to be processed for the recently
		 * processed node/attribute.
		 *
		 * @internal
		 * @return SplQueue
		 */
		final public function getQueue()
		{
			$q = $this->_queue;
			$this->_queue = NULL;
			return $q;
		} // end getQueue();

		/**
		 * Returns the names of the XML instructions registered by this processor.
		 *
		 * @final
		 * @return Array
		 */
		final public function getInstructions()
		{
			return $this->_instructions;		
		} // end getInstructions();

		/**
		 * Returns the names of the XML attributes registered by this processor.
		 *
		 * @final
		 * @internal
		 * @return Array
		 */
		final public function getAttributes()
		{
			return $this->_attributes;
		} // end getAttributes();

		/**
		 * Adds the children of the specified node to the queue of the currently
		 * parsed element. It allows them to be processed.
		 *
		 * @final
		 * @internal
		 * @param Opt_Xml_Scannable $node
		 */
		final protected function _process(Opt_Xml_Scannable $node)
		{
			if($this->_queue === null)
			{
				$this->_queue = new SplQueue;
			}
			if($node->hasChildren())
			{
				foreach($node as $child)
				{
					$this->_queue->enqueue($child);		
				}
			}
		} // end _process();

		/**
		 * Directly enqueues the specified node in the queue of the elements
		 * waiting for parsing.
		 *
		 * @param Opt_Xml_Node $node The node to be enqueued.
		 */
		final protected function _enqueue(Opt_Xml_Node $node)
		{
			if($this->_queue === null)
			{
				$this->_queue = new SplQueue;
			}
			$this->_queue->enqueue($node);
		} // end _enqueue();
		
		final protected function _debugPrintQueue()
		{
			var_dump($this->_queue);
		} // end _debugPrintQueue();

		/**
		 * Allows to define the instructions parsed by this processor.
		 * It is intended to be used in configure() method.
		 *
		 * @final
		 * @param String|Array $list The name of a single instruction or list of instructions.
		 */
		final protected function _addInstructions($list)
		{
			if(is_array($list))
			{
				$this->_instructions = array_merge($this->_instructions, $list);
			}
			else
			{
				$this->_instructions[] = $list;
			}
		} // end _addInstructions();

		/**
		 * Allows to define the attributes parsed by this processor.
		 * It is intended to be used in configure() method.
		 *
		 * @final
		 * @param String|Array $list The name of a single attribute or list of attributes.
		 */
		final protected function _addAttributes($list)
		{
			if(is_array($list))
			{
				$this->_attributes = array_merge($this->_attributes, $list);
			}
			else
			{
				$this->_attributes[] = $list;
			}
		} // end _addAttributes();

		/**
		 * This helper method is the default instruction attribute handler in OPT.
		 * It allows to parse the list of attributes using the specified rules.
		 * The attribute configuration is passed as a second argument by reference,
		 * and OPT returns the compiled attribute values in the same way.
		 *
		 * If the attribute specification contains "__UNKNOWN__" element, the node
		 * may contain an undefined number of attributes. The undefined attributes
		 * must match to the rules in "__UNKNOWN__" element and are returned by the
		 * method as a separate array. For details, see the OPT user manual.
		 *
		 * @final
		 * @param Opt_Xml_Element $subitem The scanned XML element.
		 * @param Array &$config The reference to the attribute configuration
		 * @return Array|Null The list of undefined attributes, if "__UNKNOWN__" is set.
		 */
		final protected function _extractAttributes(Opt_Xml_Element $subitem, Array &$config)
		{
			$required = array();
			$optional = array();
			$unknown = null;
			// Decide, what is what.
			foreach($config as $name => &$data)
			{
				if($name == '__UNKNOWN__')
				{
					$unknown = &$data;					
				}
				elseif($data[0] == self::REQUIRED)
				{
					$required[$name] = &$data;
				}
				elseif($data[0] == self::OPTIONAL)
				{
					$optional[$name] = &$data;
				}
			}
			$config = array();
			$return = array();

			// Parse required attributes
			$attrList = $subitem->getAttributes(false);
			foreach($required as $name => &$data)
			{
				if(isset($attrList[$name]))
				{
					$aname = $name;
				}
				elseif(isset($attrList['str:'.$name]) && ($data[1] == self::EXPRESSION || $data[1] == self::ASSIGN_EXPR || $data[1] == self::STRING))
				{
					$data[1] = self::STRING;
					$aname = 'str:'.$name;
				}
				elseif(isset($attrList['parse:'.$name]))
				{
					if($data[1] == self::STRING)
					{
						$data[1] = self::EXPRESSION;
					}
					$aname = 'parse:'.$name;
				}
				else
				{
					throw new Opt_AttributeNotDefined_Exception($name, $subitem->getXmlName());
				}

				$config[$name] = $this->_extractAttribute($subitem, $attrList[$aname], $data[1]);
				unset($attrList[$aname]);
			}

			// Parse optional attributes
			foreach($optional as $name => &$data)
			{
				if(isset($attrList[$name]))
				{
					$aname = $name;
				}
				elseif(isset($attrList['str:'.$name]) && ($data[1] == self::EXPRESSION || $data[1] == self::ASSIGN_EXPR || $data[1] == self::STRING))
				{
					$data[1] = self::STRING;
					$aname = 'str:'.$name;
				}
				elseif(isset($attrList['parse:'.$name]) && ($data[1] == self::EXPRESSION || $data[1] == self::ASSIGN_EXPR || $data[1] == self::STRING))
				{
					if($data[1] == self::STRING)
					{
						$data[1] = self::EXPRESSION;
					}
					$aname = 'parse:'.$name;
				}
				else
				{
					// We can't use isset() because the default data might be "NULL"
					if(!array_key_exists(2, $data))
					{
						throw new Opt_APIMissingDefaultValue_Exception($name, $subitem->getXmlName());
					}
					$config[$name] = $data[2];
					continue;
				}
			
				$config[$name] = $this->_extractAttribute($subitem, $attrList[$aname], $data[1]);
				unset($attrList[$aname]);
			}
			// The remaining tags must be processed using $unknown rule, however it
			// must be defined.
			if(!is_null($unknown))
			{
				// TODO: Add here namespace check!
				foreach($attrList as $name => $attr)
				{
					$type = $unknown[1];
					if(strpos($name, 'str:') === 0 && ($type == self::STRING || $type == self::EXPRESSION || $type == self::ASSIGN_EXPR))
					{
						$type = self::STRING;
						$name = substr($name, 4, strlen($name) - 4);
					}
					elseif(strpos($name, 'parse:') === 0 && ($type == self::EXPRESSION || $type == self::ASSIGN_EXPR || $type == self::STRING))
					{
						if($type == self::STRING)
						{
							$type = self::EXPRESSION;
						}
						$name = substr($name, 6, strlen($name) - 6);
					}
					// Omit the special OPT namespaces...
					$nameItems = explode(':', $name);
					if(sizeof($nameItems) > 1)
					{
						if(!$this->_compiler->isNamespace($nameItems[0]))
						{
							$return[$name] = $this->_extractAttribute($subitem, $attr, $type);
						}
					}
					else
					{
						$return[$name] = $this->_extractAttribute($subitem, $attr, $type);
					}
					
				}
			}
			return $return;
		} // end _extractAttributes();

		/**
		 * Tries to extract a single attribute, using the specified value type.
		 *
		 * @final
		 * @internal
		 * @param Opt_Xml_Element $item The scanned XML element.
		 * @param Opt_Xml_Attribute $attr The parsed attribute
		 * @param Int $type The requested value type.
		 * @return Mixed The extracted attribute value
		 */
		final private function _extractAttribute(Opt_Xml_Element $item, Opt_Xml_Attribute $attr, $type)
		{
			$value = (string)$attr;
			switch($type)
			{
				// An identifier, but with empty values allowed.
				case self::ID_EMP:
					if($value == '')
					{
						return $value;
					}
				// An identifier
				case self::ID:
					if(!preg_match('/^[a-zA-Z0-9\_\.]+$/', $value))
					{
						throw new Opt_InvalidAttributeType_Exception($attr->getXmlName(), $item->getXmlName(), 'identifier');
					}
					return $value;
				// A number
				case self::NUMBER:
					if(!preg_match('/^\-?([0-9]+\.?[0-9]*)|(0[xX][0-9a-fA-F]+)$/', $value))
					{
						throw new Opt_InvalidAttributeType_Exception($attr->getXmlName(), $item->getXmlName(), 'number');
					}
					return $value;
				// Boolean value: "yes" or "no"
				case self::BOOL:
					if($value != 'yes' && $value != 'no')
					{
						throw new Opt_InvalidAttributeType_Exception($attr->getXmlName(), $item->getXmlName(), '"yes" or "no"');
					}
					return ($value == 'yes');
				// A string packed into PHP expression. Can be switched to EXPRESSION.
				case self::STRING:
					if($attr->getNamespace() == 'parse')
					{
						$result = $this->_compiler->compileExpression($value, false, false);
						return $result[0];
					}
					else
					{
						return '\''.$value.'\'';
					}
					break;
				// An OPT expression. Can be switched to STRING.
				case self::EXPRESSION:
					if($attr->getNamespace() == 'str')
					{
						return '\''.$value.'\'';
					}
					else
					{
						// Do not allow the empty strings to be evaluated!
						if(strlen(trim($value)) == 0)
						{
							throw new Opt_AttributeEmpty_Exception($attr->getXmlName(), $item->getXmlName());
						}
						$result = $this->_compiler->compileExpression($value, false, false);
						return $result[0];
					}
					break;
				// An OPT expression with assignment operators allowed.
				case self::ASSIGN_EXPR:
					if($attr->getNamespace() == 'str')
					{
						return '\''.$value.'\'';
					}
					else
					{
						// Do not allow the empty strings to be evaluated!
						if(strlen(trim($value)) == 0)
						{
							throw new Opt_AttributeEmpty_Exception($attr->getXmlName(), $item->getXmlName());
						}
						$result = $this->_compiler->compileExpression($value, true, false);
						return $result[0];
					}
					break;
				// So-called "hard" string, simply return the tag value and do not bother, what it is.
				case self::HARD_STRING:
					return $value;
					break;
			}
		} // end _extractAttribute();
	} // end Opt_Compiler_Processor;
