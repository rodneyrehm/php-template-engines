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
	 * This processor implements the opt:tree instruction.
	 */
	class Opt_Instruction_Tree extends Opt_Instruction_BaseSection
	{
		/**
		 * The processor name
		 * @var String
		 */
		protected $_name = 'tree';

		/**
		 * Configures the processor.
		 */
		public function configure()
		{
			$this->_addInstructions(array('opt:tree', 'opt:treeelse'));
		} // end configure();

		/**
		 * Processes the opt:tree node.
		 * @param Opt_Xml_Node $node The instruction node found by the compiler.
		 */
		protected function _processTree(Opt_Xml_Node $node)
		{
			// First, do the section stuff.
			$section = $this->_sectionCreate($node);
			$this->_sectionStart($section);

			// Check the tag structure and get the tags.
			$stList = $node->getElementsByTagNameNS('opt', 'list', false);
			$stNode = $node->getElementsByTagNameNS('opt', 'node', false);
			$stTreeElse = $node->getElementsByTagNameNS('opt', 'treeelse', false);
			if(sizeof($stList) != 1)
			{
				throw new Opt_InstructionTooManyItems_Exception('opt:list', 'opt:tree', 'Exactly one');
			}
			if(sizeof($stNode) != 1)
			{
				throw new Opt_InstructionTooManyItems_Exception('opt:node', 'opt:tree', 'Exactly one');
			}
			if(sizeof($stTreeElse) > 1)
			{
				throw new Opt_InstructionTooManyItems_Exception('opt:treeelse', 'opt:tree', 'Zero or one');
			}
			// Show "opt:list" and "opt:node" tags
			$stList = $stList[0];
			$stNode = $stNode[0];
			$stList->set('hidden', false);
			$stNode->set('hidden', false);

			// Reorganize the XML tree structure.
			$node->removeChildren();
			$node->appendChild($stList);
			$node->appendChild($stNode);
			if(isset($stTreeElse[0]))
			{
				$node->appendChild($stTreeElse[0]);
				$stTreeElse[0]->set('hidden', false);
			}
	
			$stListContent = $stList->getElementsByTagNameNS('opt', 'content');
			$stNodeContent = $stNode->getElementsByTagNameNS('opt', 'content');
			if(sizeof($stListContent) != 1)
			{
				throw new Opt_InstructionTooManyItems_Exception('opt:content', 'opt:list', 'Exactly one');
			}
			if(sizeof($stNodeContent) != 1)
			{
				throw new Opt_InstructionTooManyItems_Exception('opt:content', 'opt:node', 'Exactly one');
			}
			$content = array(
				'list' => $stListContent[0],
				'node' => $stNodeContent[0]
			);
					
			// Check the PHP buffers. Neither opt:list nor opt:node must have an instruction tag that
			// Is wrapped around opt:content, because this would certainly produce an invalid PHP code.
			// opt:content is used here as a separator in a big "switch" statement, so it must not be
			// enclosed in any PHP curly bracket block.
			$test = array(Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::TAG_AFTER, Opt_Xml_Buffer::TAG_CONTENT_BEFORE,
				Opt_Xml_Buffer::TAG_CONTENT_AFTER, Opt_Xml_Buffer::TAG_CONTENT);
			foreach($content as $id => $tag)
			{
				$tag->set('hidden', false);	
				$tag = $tag->getParent();
				while($tag->getName() != 'node' && $tag->getName() != 'list')
				{
					if($tag->getNamespace() == 'opt')
					{
						throw new Opt_TreeContent_Exception($tag->getXmlName(), 'opt:'.$id);
					}
					foreach($test as $buffer)
					{
						if($tag->bufferSize($buffer) > 0)
						{
							throw new Opt_TreeContent_Exception($tag->getXmlName(), 'opt:'.$id);
						}
					}
					$tag = $tag->getParent();
				}
			}
					
			// Now, generate the source code.
			/*
			 * Recursion is a native mechanism to process trees, and in fact - the template syntax suggest we use it here. However, it
			 * would be too expensive (functions, other stupidities). The trees are rendered in the imperative way. Both opt:list and opt:node
			 * are split with the opt:content tag and this way we have four rendering commands: beginning of the list, its end, beginning of the node
			 * and its end. The loop does not iterate through list items, but is a simple automata: if the rendering command queue is empty,
			 * we move to the next list item, decide what it is (leaf, subnode, etc.) and create a command chain that is necessary to render it.
			 * 
			 * If the chain is empty, we have to close the nodes that are still open and the main list itself. After processing this chain, we finish the job.
			 */
			$section['format']->action('section:forceItemVariables');
			$section['format']->assign('item', 'depth');
			$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $section['format']->get('section:loopBefore').'
			'.$section['format']->get('section:reset').'
$_'.$section['name'].'_depth = -1;
$_'.$section['name'].'_initDepth = null;
$_'.$section['name'].'_over = 0;
$_'.$section['name'].'_cmd = new SplQueue;
$_'.$section['name'].'_stack = new SplStack;
while(1)
{
	if($_'.$section['name'].'_cmd->count() == 0)
	{
		switch($_'.$section['name'].'_over)
		{
			case 0:
				$_'.$section['name'].'_over = 1;
				break;
			case 1:
				'.$section['format']->get('section:next').'
				break;
			case 2:
				break 2;
		}
		if(!'.$section['format']->get('section:valid').')
		{
			$_'.$section['name'].'_cmd->enqueue(array(3, $_'.$section['name'].'_stack->pop()));
			for($k = $_'.$section['name'].'_initDepth; $k < $_'.$section['name'].'_depth; $k++)
			{
				$_'.$section['name'].'_cmd->enqueue(array(4, null));
				$_'.$section['name'].'_cmd->enqueue(array(3, $_'.$section['name'].'_stack->pop()));
			}
			$_'.$section['name'].'_cmd->enqueue(array(4, null));
			$_'.$section['name'].'_over = 2;
		}
		else
		{
			'.$section['format']->get('section:populate').'
			if(is_null($_'.$section['name'].'_initDepth))
			{
				$_'.$section['name'].'_initDepth = '.$section['format']->get('section:variable').';
			}
			if($_'.$section['name'].'_initDepth > '.$section['format']->get('section:variable').')
			{
				throw new Opt_TreeInvalidDepth_Exception('.$section['format']->get('section:variable').', $_'.$section['name'].'_initDepth);
			}
			if($_'.$section['name'].'_depth < '.$section['format']->get('section:variable').')
			{
				$_'.$section['name'].'_cmd->enqueue(array(1, null));
				$_'.$section['name'].'_cmd->enqueue(array(2, $_sect'.$section['name'].'_v));
				$_'.$section['name'].'_stack->push($_sect'.$section['name'].'_v);
			}
			elseif($_'.$section['name'].'_depth > '.$section['format']->get('section:variable').')
			{
				$_'.$section['name'].'_cmd->enqueue(array(3, $_'.$section['name'].'_stack->pop()));
				for($k = '.$section['format']->get('section:variable').'; $k < $_'.$section['name'].'_depth; $k++)
				{
					$_'.$section['name'].'_cmd->enqueue(array(4, null));
					$_'.$section['name'].'_cmd->enqueue(array(3, $_'.$section['name'].'_stack->pop()));
				}
				$_'.$section['name'].'_cmd->enqueue(array(2, $_sect'.$section['name'].'_v));
				$_'.$section['name'].'_stack->push($_sect'.$section['name'].'_v);
			}
			else
			{
				$_'.$section['name'].'_cmd->enqueue(array(3, $_'.$section['name'].'_stack->pop()));
				$_'.$section['name'].'_cmd->enqueue(array(2, $_sect'.$section['name'].'_v));
				$_'.$section['name'].'_stack->push($_sect'.$section['name'].'_v);
			}
			$_'.$section['name'].'_depth = '.$section['format']->get('section:variable').';
		}
		
	}
	list($cmd, $_sect'.$section['name'].'_v) = $_'.$section['name'].'_cmd->dequeue();
	switch($cmd)
	{');
			// Add the four case code.
			$stList->addBefore(Opt_Xml_Buffer::TAG_BEFORE, 'case 1: ');
			$content['list']->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'break; ');
			$content['list']->addBefore(Opt_Xml_Buffer::TAG_AFTER, 'case 4: ');
			$stList->addAfter(Opt_Xml_Buffer::TAG_AFTER, ' break; ');
			$stNode->addBefore(Opt_Xml_Buffer::TAG_BEFORE, 'case 2: ');
			$content['node']->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'break; ');
			$content['node']->addBefore(Opt_Xml_Buffer::TAG_AFTER, 'case 3: ');
			$stNode->addAfter(Opt_Xml_Buffer::TAG_AFTER, 'break; } } unset($_'.$section['name'].'_stack); unset($_'.$section['name'].'_cmd); ');
			
			$this->processSeparator('$__sect_'.$section['name'], $section['separator'], $node);

			$node->set('postprocess', true);
			$this->_process($node);
			$this->_process($stList);
			$this->_process($stNode);
		} // end _processTree();

		/**
		 * Postprocesses the opt:tree node.
		 * @param Opt_Xml_Element $node The node found by the compiler.
		 */
		protected function _postprocessTree(Opt_Xml_Element $node)
		{
			$section = $this->getSection($node->get('sectionName'));
			if($node->hasAttributes())
			{
				if(!$node->get('sectionElse'))
				{
					$this->_sortSectionContents($node, 'opt', 'treeelse');
				}
			}
			$this->_sectionEnd($node);
		} // end _postprocessTree();

		/**
		 * Processes opt:treeelse node.
		 * @param Opt_Xml_Element $node The instruction node found by the compiler.
		 */
		protected function _processTreeelse(Opt_Xml_Element $node)
		{
			$parent = $node->getParent();
			if($parent instanceof Opt_Xml_Element && $parent->getXmlName() == 'opt:tree')
			{
				$parent->set('sectionElse', true);
				
				$section = $this->getSection($parent->get('sectionName'));
				$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' } else { ');
				$this->_process($node);
			}
		} // end _processTreeelse();
	} // end Opt_Instruction_Tree;