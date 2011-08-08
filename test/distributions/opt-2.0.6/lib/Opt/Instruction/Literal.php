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

	class Opt_Instruction_Literal extends Opt_Compiler_Processor
	{
		protected $_name = 'literal';
		
		public function configure()
		{
			$this->_addInstructions(array('opt:literal'));
		} // end configure();
	
		public function processNode(Opt_Xml_Node $node)
		{
			$params = array(
				'type' => array(0 => self::OPTIONAL, self::ID, 'cdata')	
			);
			$this->_extractAttributes($node, $params);
			
			// First, disable displaying CDATA around all CDATA text parts found
			$this->disableCDATA($node, true);
			
			// Define, what to display...
			$node->clear();
			$node->set('nophp', true);
			switch($params['type'])
			{
				case 'transparent';
					break;
				case 'comment':
					$node->set('commented', true);
					break;
				case 'comment_cdata':
					$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, '/* <![CDATA[ */');
					$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, '/* ]]> */');
					break;
				case 'cdata':
				default:
					$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, '<![CDATA[');
					$node->addAfter(Opt_Xml_Buffer::TAG_AFTER, ']]>');		
			}
			$this->_process($node);
		} // end processNode();

		/**
		 * Used on a node, it looks for the CDATA elements and disables the
		 * CDATA flag on them. Moreover, it allows to disable the text entitizing.
		 *
		 * @param Opt_Xml_Node $node The scanned node
		 * @param Boolean $noEntitize optional The entitizing flag.
		 */
		public function disableCDATA(Opt_Xml_Node $node, $noEntitize = false)
		{
			// Sets the CDATA attribute to all the $node descendants of
			// Opt_Xml_Cdata type. This is a non-recursive version using queue.
			$queue = new SplQueue;
			$queue->enqueue($node);
			do
			{
				$current = $queue->dequeue();

				if($current instanceof Opt_Xml_Cdata)
				{
					if($current->get('cdata') === true)
					{
						$current->set('cdata', false);
					}
					if($noEntitize)
					{
						$current->set('noEntitize', true);
					}
				}
				foreach($current as $subnode)
				{
					$queue->enqueue($subnode);
				}
			}
			while($queue->count() > 0);	
		} // end disableCdata();
	} // end Opt_Instruction_Literal;
