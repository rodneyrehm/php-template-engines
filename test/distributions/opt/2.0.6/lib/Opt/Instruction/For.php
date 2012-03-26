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
	 * This processor implements the opt:for instruction.
	 */
	class Opt_Instruction_For extends Opt_Instruction_Loop
	{
		/**
		 * The processor name
		 * @var String
		 */
		protected $_name = 'for';

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
			$this->_addInstructions(array('opt:for'));
		} // end configure();

		/**
		 * Processes the "opt:for" node.
		 * @param Opt_Xml_Node $node The node found by the compiler
		 */
		public function processNode(Opt_Xml_Node $node)
		{
			$params = array(
				'begin' => array(0 => self::REQUIRED, self::ASSIGN_EXPR),
				'while' => array(0 => self::REQUIRED, self::ASSIGN_EXPR),
				'iterate' => array(0 => self::REQUIRED, self::ASSIGN_EXPR),
				'separator' => $this->getSeparatorConfig()
			);
			$this->_extractAttributes($node, $params);
			$this->_nesting++;
			
			$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' for('.$params['begin'].'; '.$params['while'].'; '.$params['iterate'].'){ ');
			$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' } ');
			
			$this->processSeparator('$__for'.$this->_nesting, $params['separator'], $node);
			
			$node->set('postprocess', true);
			$this->_process($node);
		} // end processNode();

		/**
		 * In the postprocessing, we decrement the nesting level.
		 * @param Opt_Xml_Node $node The node found by the compiler.
		 */
		public function postprocessNode(Opt_Xml_Node $node)
		{
			$this->_nesting--;
		} // end postprocessNode();
	} // end Opt_Instruction_For;