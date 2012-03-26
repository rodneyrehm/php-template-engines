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

	class Opt_Instruction_Extend extends Opt_Compiler_Processor
	{
		protected $_name = 'extend';
		
		public function configure()
		{
			/* IMPORTANT NOTICE: This instruction is partially hard-coded inside the compiler, because
			 * 	it would be to hard to do what it is intented to do from this level. See: _generateExtend()
			 * in "Internal tools and utilities" of /opt/compiler/class.php
			 */
		
			$this->_addInstructions(array('opt:extend'));
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			if($node->getParent()->getType() != 'Opt_Xml_Root')
			{
				throw new Opt_InstructionInvalidParent_Exception($node->getXmlName(), 'ROOT');
			}

			$params = array(
				'file' => array(0 => self::REQUIRED, self::HARD_STRING),
				'escaping' => array(0 => self::OPTIONAL, self::BOOL, NULL),
				'dynamic' => array(0 => self::OPTIONAL, self::BOOL, false),
				'__UNKNOWN__' => array(0 => self::OPTIONAL, self::HARD_STRING, null),
			);

			$branches = $this->_extractAttributes($node, $params);
			
			if(!is_null($params['escaping']))
			{
				$this->_compiler->set('escaping', $params['escaping']);
			}

			if($params['dynamic'] && !is_null($branch = $this->_compiler->inherits($this->_compiler->get('currentTemplate'))))
			{
			}
			elseif(isset($branches[$this->_compiler->get('branch')]))
			{
				$branch = $branches[$this->_compiler->get('branch')];
			}
			else
			{
				$branch = $params['file'];
			}

			$node->set('branch', $branch);
			$node->set('postprocess', true);
			$this->_process($node);
		} // end processNode();
		
		public function postprocessNode(Opt_Xml_Node $node)
		{
			if($this->_compiler->processor('snippet')->isSnippet($node->get('branch')))
			{				
				$node->getParent()->set('snippet', $node->get('branch'));
			}
			else
			{
				$node->getParent()->set('extend', $node->get('branch'));
			}
		} // end postprocessNode();
	} // end Opt_Instruction_Extends;