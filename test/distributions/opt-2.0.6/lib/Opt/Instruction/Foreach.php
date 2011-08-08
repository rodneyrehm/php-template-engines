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
	 * This processor implements the opt:foreach instruction.
	 */
	class Opt_Instruction_Foreach extends Opt_Instruction_Loop
	{
		/**
		 * The processor name
		 * @var String
		 */
		protected $_name = 'foreach';

		/**
		 * The current nesting level of "opt:for"
		 * @var Integer
		 */
		protected $_nesting = 0;

		/**
		 * Configures the processor.
		 */
		public function configure()
		{
			$this->_addInstructions(array('opt:foreach', 'opt:foreachelse'));
		} // end configure();

		/**
		 * Processes the "opt:foreach" node.
		 * @param Opt_Xml_Node $node The node found by the compiler
		 */
		public function processNode(Opt_Xml_Node $node)
		{
			switch($node->getName())
			{
				case 'foreach':
					$params = array(
						'array' => array(0 => self::REQUIRED, self::EXPRESSION),
						'value' => array(0 => self::REQUIRED, self::ID),
						'index' => array(0 => self::OPTIONAL, self::ID, null),
						'separator' => array(0 => self::OPTIONAL, self::EXPRESSION, null)
					);
					
					$this->_extractAttributes($node, $params);
					$this->_nesting++;
					
					$node->sort(array('*' => 0, 'opt:foreachelse' => 1));
					$list = $node->getElementsByTagNameNS('opt', 'foreachelse', false);

					// Determine, if we are using opt:foreachelse, because it requires a bit different code from us.
					$codeBegin = ' foreach('.$params['array'].' as '.(!is_null($params['index']) ? '$__fe'.$this->_nesting.'_idx => ' : '').'$__fe'.$this->_nesting.'_val){ ';
					switch(sizeof($list))
					{
						case 0:
							break;
						case 1:
							$codeBegin = 'if(sizeof('.$params['array'].') > 0){ '.$codeBegin;
							break;
						default:
							throw new Opt_InstructionTooManyItems_Exception('opt:foreachelse', $node->getXmlName());
					}					

					// Register everything in the buffer.
					$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, $codeBegin);
					$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');
					$this->_compiler->setConversion('##var_'.$params['value'], '$__fe'.$this->_nesting.'_val');
					if(!is_null($params['index']))
					{
						$this->_compiler->setConversion('##var_'.$params['index'], '$__fe'.$this->_nesting.'_idx');
					}
					// This instruction supports separators.
					$this->processSeparator('$__foreach_'.$this->_nesting, $params['separator'], $node);
					
					$node->set('postprocess', true);
					$this->_process($node);
					$node->set('params', $params);

					break;
				case 'foreachelse':
					if($node->getParent()->getName() != 'foreach')
					{
						throw new Opt_InstructionInvalidParent_Exception($node->getXmlName(), 'opt:foreach');
					}
					$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, '} } else { ');
					$this->_process($node);
					break;
			}
		} // end processNode();

		/**
		 * In the postprocessing, we decrement the nesting level and unregister
		 * the conversions set in the processing stage.
		 *
		 * @param Opt_Xml_Node $node The node found by the compiler.
		 */
		public function postprocessNode(Opt_Xml_Node $node)
		{
			$params = $node->get('params');
			$this->_compiler->unsetConversion('##var_'.$params['value']);
			$this->_compiler->unsetConversion('##var_'.$params['index']);
			$this->_nesting--;
		} // end postprocessNode();
	} // end Opt_Instruction_Foreach;