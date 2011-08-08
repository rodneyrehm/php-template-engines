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

	class Opt_Instruction_Component extends Opt_Compiler_Processor
	{
		protected $_name = 'component';
		// The counter used to generate unique variable names for defined components
		protected $_unique = 0;

		// The stack is required by the processSystemVar() method to determine, which component
		// the call refers to.
		protected $_stack;
		
		public function configure()
		{
			$this->_addInstructions(array('opt:component', 'opt:onEvent', 'opt:display', ));
			$this->_stack = new SplStack;
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			switch($node->getName())
			{
				case 'component':
					$node->set('component', true);
					// Undefined component processing
					$params = array(
						'from' => array(self::REQUIRED, self::EXPRESSION, null),
						'datasource' => array(self::OPTIONAL, self::EXPRESSION, null),
						'template' => array(self::OPTIONAL, self::ID, null),
						'__UNKNOWN__' => array(self::OPTIONAL, self::EXPRESSION, null)
					);
					$vars = $this->_extractAttributes($node, $params);
					$this->_stack->push($params['from']);
					
					$mainCode = ' if(is_object('.$params['from'].') && '.$params['from'].' instanceof Opt_Component_Interface){ '.$params['from'].'->setView($this); ';
					if(!is_null($params['datasource']))
					{
						$mainCode .= $params['from'].'->setDatasource('.$params['datasource'].'); ';
					}			
					
					$mainCode .= $this->_commonProcessing($node, $params['from'], $params, $vars);
		
					$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE,  $mainCode);
					$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');
					break;
				case 'onEvent':
					if($this->_stack->count() == 0)
					{
						throw new Opt_ComponentNotActive_Exception($node->getXmlName());
					}
				
					$tagParams = array(
						'name' => array(self::REQUIRED, self::EXPRESSION)
					);

					$this->_extractAttributes($node, $tagParams);
					$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, ' if('.$this->_stack->top().'->processEvent('.$tagParams['name'].')){ ');
					$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');
					$this->_process($node);
					break;
					
				case 'display':
					if($this->_stack->count() == 0)
					{
						throw new Opt_ComponentNotActive_Exception($node->getXmlName());
					}
					$node->set('hidden', false);
					$node->removeChildren();
					// The opt:display attributes must be packed into array and sent
					// to Opt_Component_Interface::display()
					$subCode = '';
					if($node->hasAttributes())
					{
						$params = array(
							'__UNKNOWN__' => array(self::OPTIONAL, self::EXPRESSION, null)
						);
						$vars = $this->_extractAttributes($node, $params);
						$subCode = 'array(';
						foreach($vars as $name => $value)
						{
							$subCode .= '\''.$name.'\' => '.$value.',';
						}
						$subCode .= ')';
					}
					$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $this->_stack->top().'->display('.$subCode.'); ');
					break;
			}			
		} // end processNode();
		
		public function postprocessNode(Opt_Xml_Node $node)
		{
			if(!is_null($attribute = $node->get('_componentTemplate')))
			{
				$this->_compiler->processor('snippet')->postprocessAttribute($node, $attribute);
			}
			$this->_stack->pop();
		} // end postprocessNode();

		public function processComponent(Opt_Xml_Element $node)
		{
			// Defined component processing
			$params = array(
				'datasource' => array(self::OPTIONAL, self::EXPRESSION, null),
				'template' => array(self::OPTIONAL, self::ID, null),
				'__UNKNOWN__' => array(self::OPTIONAL, self::EXPRESSION, null)
			);

			$vars = $this->_extractAttributes($node, $params);
			// Get the real class name
			$cn = '$_component_'.($this->_unique++);

			$this->_stack->push($cn);

			$class = $this->_compiler->component($node->getXmlName());

			// Check, if there are any conversions that may take control over initializing
			// the component object. We are allowed to capture only particular component
			// creation or all of them.
			if((($to = $this->_compiler->convert('##component_'.$class)) != '##component_'.$class))
			{
				$attributes = 'array(';
				foreach($vars as $name => $value)
				{
					$attributes .= '\''.$name.'\' => '.$value.', ';
				}
				$attributes .= ')';
				$ccode = str_replace(array('%CLASS%', '%TAG%', '%ATTRIBUTES%'), array($class, $node->getXmlName(), $attributes), $to);
			}
			elseif((($to = $this->_compiler->convert('##component')) != '##component'))
			{
				$attributes = 'array(';
				foreach($vars as $name => $value)
				{
					$attributes .= '\''.$name.'\' => '.$value.', ';
				}
				$attributes .= ')';
				$ccode = str_replace(array('%CLASS%', '%TAG%', '%ATTRIBUTES%'), array($class, $node->getXmlName(), $attributes), $to);
			}
			else
			{
				$ccode = 'new '.$class;
			}

			// Generate the initialization code
			$mainCode = $cn.' = '.$ccode.'; '.$cn.'->setView($this); ';
			if(!is_null($params['datasource']))
			{
				$mainCode .= $cn.'->setDatasource('.$params['datasource'].'); ';
			}	

			$mainCode .= $this->_commonProcessing($node, $cn, $params, $vars);
			$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE,  $mainCode);
		} // end processComponent();
		
		public function postprocessComponent(Opt_Xml_Node $node)
		{			
			if(!is_null($attribute = $node->get('_componentTemplate')))
			{
				$this->_compiler->processor('snippet')->postprocessAttribute($node, $attribute);
				$node->set('_componentTemplate', NULL);
			}
			$this->_stack->pop();
		} // end postprocessComponent();

		private function _commonProcessing($node, $cn, $params, $args)
		{
			// Common part of the component processing
			$set2 = array();
			if(!is_null($params['template']))
			{
				// Scan for opt:set tags - they may contain some custom arguments.
				$set2 = $node->getElementsByTagNameNS('opt', 'set');
				
				// Now a little trick - how to cheat the opt:insert instruction
				$attribute = new Opt_Xml_Attribute('opt:use', $params['template']);
				$this->_compiler->processor('snippet')->processAttribute($node, $attribute);		
			}

			// Find all the important component elements
			// Remember that some opt:set tags may have been found above and are located in $set2 array.
			$everything = $this->_find($node);
			$everything[0] = array_merge($everything[0], $set2);
			
			$code = '';
			// opt:set
			foreach($everything[0] as $set)
			{
				$tagParams = array(
					'name' => array(self::REQUIRED, self::EXPRESSION),
					'value' => array(self::REQUIRED, self::EXPRESSION)
				);

				$this->_extractAttributes($set, $tagParams);
				$code .= $cn.'->set('.$tagParams['name'].', '.$tagParams['value'].'); ';
			}
			foreach($args as $name => $value)
			{
				$code .= $cn.'->set(\''.$name.'\', '.$value.'); ';
			}
			// com:* and opt:component-attributes
			foreach($everything[1] as $wtf)
			{
				$id = null;
				if($wtf->getNamespace() == 'com')
				{
					$wtf->setNamespace(NULL);
					$subCode = ' $out = '.$cn.'->manageAttributes(\''.$wtf->getName().'\', array(';
				}
				else
				{
					$id = $wtf->getAttribute('opt:component-attributes')->getValue();
					$subCode = ' $out = '.$cn.'->manageAttributes(\''.$wtf->getName().'#'.$id.'\', array(';
				}

				
				foreach($wtf->getAttributes() as $attribute)
				{
					$params = array(
						'__UNKNOWN__' => array(self::OPTIONAL, self::STRING, null)
					);
					$vars = $this->_extractAttributes($wtf, $params);
					foreach($vars as $name => $value)
					{
						$subCode .= '\''.$name.'\' => '.$value.',';
					}
				}
				$wtf->removeAttributes();
				$wtf->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $subCode.')); ');
				$wtf->addAfter(Opt_Xml_Buffer::TAG_ENDING_ATTRIBUTES, ' if(is_array($out)){ foreach($out as $name=>$value){ echo \' \'.$name.\'="\'.$value.\'"\'; } } ');
			}	
			
			$node->set('postprocess', true);
			if(isset($attribute))
			{
				$node->set('_componentTemplate', $attribute);
			}
			$this->_process($node);
			return $code;
		} // end _commonProcessing();

		public function processSystemVar($opt)
		{
			if($this->_stack->count() == 0)
			{
				throw new Opt_SysVariableInvalidUse_Exception('$'.implode('.',$opt), 'components');
			}
			return $this->_stack->top().'->get(\''.$opt[2].'\')';
		} // end processSystemVar();

		private function _find($node)
		{
			// We have so many recursions... let's do it in the imperative way.
			$queue = new SplQueue;
			foreach($node as $subnode)
			{
				$queue->enqueue($subnode);
			}
			$result = array(
				0 => array(),	// opt:set
				1 => array(),	// com:*
			);
			$map = array('opt:set' => 0);
			
			do
			{
				$current = $queue->dequeue();
				
				if($current instanceof Opt_Xml_Element)
				{
					if(isset($map[$current->getXmlName()]))
					{
						$result[$map[$current->getXmlName()]][] = $current;
					}
					elseif($current->getNamespace() == 'com' || $current->getAttribute('opt:component-attributes') !== null)
					{
						$result[1][] = $current;
					}
					
					// Do not visit the nested components
					if($current->getXmlName() == 'opt:component' || $this->_compiler->isComponent($current->getXmlName()))
					{
						if($queue->count() == 0)
						{
							break;
						}
						continue;
					}
				}
				foreach($current as $subnode)
				{
					$queue->enqueue($subnode);
				}				
			}
			while($queue->count() > 0);
			return $result;
		} // end _find();
	} // end Opt_Instruction_Component;
