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

	class Opt_Instruction_Block extends Opt_Compiler_Processor
	{
		protected $_name = 'block';
		// The counter used to generate unique variable names for defined blocks
		protected $_unique = 0;

		// The stack is required by the processSystemVar() method to determine, which component
		// the call refers to.
		protected $_stack;
		
		public function configure()
		{
			$this->_addInstructions('opt:block');
			$this->_stack = new SplStack;
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			$node->set('block', true);
			// Undefined block processing
			$params = array(
				'from' => array(self::REQUIRED, self::EXPRESSION, null),
				'__UNKNOWN__' => array(self::OPTIONAL, self::EXPRESSION, null)
			);
			$vars = $this->_extractAttributes($node, $params);
			$this->_stack->push($params['from']);
					
			$mainCode = ' if(is_object('.$params['from'].') && '.$params['from'].' instanceof Opt_Block_Interface){ '.$params['from'].'->setView($this); ';
			$mainCode .= $this->_commonProcessing($node, $params['from'], $vars);
		
			$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE,  $mainCode);
			$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');
			$node->set('postprocess', true);
		} // end processNode();
		
		public function postprocessNode(Opt_Xml_Node $node)
		{
			$this->_stack->pop();
		} // end postprocessNode();

		public function processBlock(Opt_Xml_Element $node)
		{
			// Defined block processing
			$params = array(
				'__UNKNOWN__' => array(self::OPTIONAL, self::EXPRESSION, null)
			);

			$vars = $this->_extractAttributes($node, $params);
			// Get the real class name
			$cn = '$_block_'.($this->_unique++);

			$this->_stack->push($cn);
			
			$class = $this->_compiler->block($node->getXmlName());
			// Check, if there are any conversions that may take control over initializing
			// the component object. We are allowed to capture only particular component
			// creation or all of them.
			if((($to = $this->_compiler->convert('##block_'.$class)) != '##block_'.$class))
			{
				$ccode = str_replace(array('%CLASS%', '%TAG%'), array($class, $node->getXmlName()), $to);
			}
			elseif((($to = $this->_compiler->convert('##block')) != '##block'))
			{
				$ccode = str_replace(array('%CLASS%', '%TAG%'), array($class, $node->getXmlName()), $to);
			}
			else
			{
				$ccode = 'new '.$class;
			}

			$mainCode = $cn.' = '.$ccode.'; '.$cn.'->setView($this); ';

			$this->_commonProcessing($node, $cn, $vars);
			$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE,  $mainCode);
		} // end processBlock();
		
		public function postprocessBlock(Opt_Xml_Node $node)
		{
			$this->_stack->pop();
		} // end postprocessBlock();

		private function _commonProcessing($node, $cn, $args)
		{
			// Common part of the component processing
			$argList = 'array( ';
			foreach($args as $name=>$value)
			{
				$argList .= '\''.$name.'\' => '.$value.', ';	
			}
			$argList .= ')';		
		
			if($node->get('single'))
			{
				$node->addAfter(Opt_Xml_Buffer::TAG_SINGLE_BEFORE, $cn.'->onSingle('.$argList.'); ');
			}
			else
			{
				$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, ' if('.$cn.'->onOpen('.$argList.')){ ');
				$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, ' } '.$cn.'->onClose(); ');
			}
		
			$this->_process($node);
		} // end _commonProcessing();

		public function processSystemVar($opt)
		{
			if($this->_stack->count() == 0)
			{
				throw new Opt_SysVariableInvalidUse_Exception('$'.implode('.',$opt), 'blocks');
			}
			return $this->_stack->top().'->get(\''.$opt[2].'\')';
		} // end processSystemVar();
	} // end Opt_Instruction_Component;