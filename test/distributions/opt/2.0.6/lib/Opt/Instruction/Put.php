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

	class Opt_Instruction_Put extends Opt_Compiler_Processor
	{
		protected $_name = 'put';
		protected $_nesting = 0;
		
		public function configure()
		{
			$this->_addInstructions(array('opt:put'));
			$this->_addAttributes(array('opt:content'));
		} // end configure();
		
		public function processNode(Opt_Xml_Node $node)
		{
			$params = array(
				'value' => array(0 => self::REQUIRED, self::ASSIGN_EXPR)		
			);
			$this->_extractAttributes($node, $params);

			$node->set('single', false);
			$node->addAfter(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, ' echo '.$params['value'].'; ');
		} // end processNode();
		
		public function processAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$result = $this->_compiler->compileExpression($attr->getValue(), false, Opt_Compiler_Class::ESCAPE_BOTH);
			if($result[2] == true)
			{
				// The expression is a single variable that can be handled in a simple way.
				$node->addAfter(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, 'if(empty('.$result[3].')){ ');
				$node->addAfter(Opt_Xml_Buffer::TAG_CONTENT_AFTER, '} else { echo '.$result[0].'; } ');
			}
			else
			{
				// In more complex expressions, we store the result to a temporary variable.
				$node->addAfter(Opt_Xml_Buffer::TAG_CONTENT_BEFORE, ' $_cont'.$this->_nesting.' = '.$result[0].'; if(empty($_cont'.$this->_nesting.')){ ');
				$node->addAfter(Opt_Xml_Buffer::TAG_CONTENT_AFTER, '} else { echo $_cont'.$this->_nesting.'; } ');
			}
			$this->_nesting++;
			$attr->set('postprocess', true);
		} // end processAttribute();

		public function postprocessAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$this->_nesting--;
		} // end postprocessAttribute();
	} // end Opt_Instruction_Put;