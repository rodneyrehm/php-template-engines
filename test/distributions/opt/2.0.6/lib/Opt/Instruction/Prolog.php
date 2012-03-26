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

	class Opt_Instruction_Prolog extends Opt_Compiler_Processor
	{
		protected $_name = 'prolog';
		
		public function configure()
		{
			$this->_addInstructions(array('opt:prolog'));
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			$params = array(
				'version' => array(0 => self::OPTIONAL, self::STRING, null),
				'encoding' => array(0 => self::OPTIONAL, self::STRING, null),
				'standalone' => array(0 => self::OPTIONAL, self::STRING, null)
			);
			$this->_extractAttributes($node, $params);
			
			$root = $node;
			while(is_object($tmp = $root->getParent()))
			{
				$root = $tmp;
			}

			if(is_null($params['version']))
			{
				$params['version'] = '\'1.0\'';
			}
			if(is_null($params['standalone']))
			{
				$params['standalone'] = '\'yes\'';
			}
			if(is_null($params['encoding']))
			{
				unset($params['encoding']);
			}

			$root->setProlog($prolog = new Opt_Xml_Prolog($params));
			$prolog->setDynamic('version', true);
			$prolog->setDynamic('standalone', true);
			$prolog->setDynamic('encoding', true);
		} // end processNode();
	} // end Opt_Instruction_Prolog;