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
 * The SPL datastructure format.
 *
 * @author Amadeusz "megawebmaster" Starzykiewicz
 * @copyright (c) Invenzzia Group 2010
 */

class Opt_Format_SplDatastructure extends Opt_Format_SingleArray
{
	protected $_supports = array(
		'section'
	);

	protected $_properties = array(
		'section:useReference' => true,
		'section:anyRequests' => null,
		'section:itemAssign' => false,
		'section:variableAssign' => false,
	);

	protected $_vals;

	/**
		* Build a PHP code for the specified hook name.
		*
		* @param String $hookName The hook name
		* @return String The output PHP code
		*/
	protected function _build($hookName)
	{
		switch($hookName)
		{
			// Initializes the section by obtaining the list of items to display
			case 'section:init':
				$section = $this->_getVar('section');

				if(($section['parent']) !== null)
				{
					$parent = Opt_Instruction_BaseSection::getSection($section['parent']);
					$parent['format']->assign('item', $section['name']);
					return '$_sect'.$section['name'].'_vals = '.$parent['format']->get('section:variable').'; ';
				}
				elseif(($section['datasource']) !== null)
				{
					return '$_sect'.$section['name'].'_vals = '.$section['datasource'].'; ';
				}
				else
				{
					if($this->_getVar('access') == Opt_Class::ACCESS_LOCAL)
					{
						return '$_sect'.$section['name'].'_vals = $this->_data[\''.$section['name'].'\']; ';
					}
					else
					{
						return '$_sect'.$section['name'].'_vals = self::$_global[\''.$section['name'].'\']; ';
					}
				}
			// The end of the section loop.
			case 'section:endLoop':
				$section = $this->_getVar('section');
				return ' $_sect'.$section['name'].'_i++; } ';
			// The condition that should test if the section is not empty.
			case 'section:isNotEmpty':
				// SPL structures compatible with format: SplDoublyLinkedList, SplStack, SplQueue
				$section = $this->_getVar('section');
				return '($_sect'.$section['name'].'_vals instanceof SplDoublyLinkedList) && ($_sect'.$section['name'].'_cnt = $_sect'.$section['name'].'_vals->count())>0';
			// The code block after the condition
			case 'section:started':
				$section = $this->_getVar('section');
				return '';
				//return 'if($_sect'.$section['name'].'_vals instanceof IteratorAggregate){ $_sect'.$section['name'].'_vals = $_sect'.$section['name'].'_vals->getIterator(); }';
			// The code block before the end of the conditional block.
			case 'section:finished':
				$section = $this->_getVar('section');
				if($section['order'] == 'desc')
				{
					return ' $_sect'.$section['name'].'_vals->setIteratorMode($_sect'.$section['name'].'_default_order); ';
				}
				return '';
			// The code block after the conditional block
			case 'section:done':
				return '';
			// The code block before entering the loop.
			case 'section:loopBefore':
				$section = $this->_getVar('section');
				$this->_vals = '$_sect'.$section['name'].'_vals';
				if($section['order'] == 'desc')
				{
					return 'try	{ $_sect'.$section['name'].'_default_order = $_sect'.$section['name'].'_vals->getIteratorMode(); $_sect'.$section['name'].'_vals->setIteratorMode((~$_sect'.$section['name'].'_default_order & SplDoublyLinkedList::IT_MODE_LIFO) | ($_sect'.$section['name'].'_default_order & ~SplDoublyLinkedList::IT_MODE_LIFO)); }
						catch(RuntimeException $_sect'.$section['name'].'_exception){
							$_sect'.$section['name'].'_tmp = new SplDoublyLinkedList; foreach($_sect'.$section['name'].'_vals as $v){ $_sect'.$section['name'].'_tmp->push($v); } $_sect'.$section['name'].'_vals = $_sect'.$section['name'].'_tmp;
							$_sect'.$section['name'].'_vals->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO); unset($_sect'.$section['name'].'_tmp);
						} $_sect'.$section['name'].'_i = 0; ';
				}
				return '';
			// The default loop for the ascending order.
			case 'section:startAscLoop':
			// The default loop for the descending order.
			case 'section:startDescLoop':
				$section = $this->_getVar('section');
				return 'foreach($_sect'.$section['name'].'_vals as $_sect'.$section['name'].'_v){ ';
			// Retrieving the whole section item.
			case 'section:item':
				$section = $this->_getVar('section');
				return '$_sect'.$section['name'].'_v';
			// Retrieving a variable from a section item.
			case 'section:variable':
				$section = $this->_getVar('section');
				if($this->isDecorating())
				{
					return '$_sect'.$section['name'].'_v'.$this->_decorated->get('item:item');
				}
				return '$_sect'.$section['name'].'_v['.$this->_getVar('item').']';
			// Resetting the section to the first element.
			case 'section:reset':
				return $this->_vals.'->rewind();';
			// Moving to the next element.
			case 'section:next':
				return $this->_vals.'->next();';
			// Checking whether the iterator is valid.
			case 'section:valid':
				return $this->_vals.'->valid()';
			// Populate the current element
			case 'section:populate':
				$section = $this->_getVar('section');
				return '$_sect'.$section['name'].'_v = '.$this->_vals.'->current(); $_sect'.$section['name'].'_i = '.$this->_vals.'->key();';
			// The code that returns the number of items in the section;
			case 'section:count':
				$section = $this->_getVar('section');
				return '$_sect'.$section['name'].'_cnt';
			// Section item size.
			case 'section:size':
				$section = $this->_getVar('section');
				return 'count($_sect'.$section['name'].'_v)';
			// Section iterator.
			case 'section:iterator':
				$section = $this->_getVar('section');
				return '$_sect'.$section['name'].'_i';
			// Testing the first element.
			case 'section:isFirst':
				$section = $this->_getVar('section');
				if($section['order'] == 'asc')
				{
					return '($_sect'.$section['name'].'_i == 0)';
				}
				else
				{
					return '($_sect'.$section['name'].'_i == ($_sect'.$section['name'].'_cnt-1))';
				}
			// Testing the last element.
			case 'section:isLast':
				$section = $this->_getVar('section');
				if($section['order'] == 'asc')
				{
					return '($_sect'.$section['name'].'_i == ($_sect'.$section['name'].'_cnt-1))';
				}
				else
				{
					return '($_sect'.$section['name'].'_i == 0)';
				}
			// Testing the extreme element.
			case 'section:isExtreme':
				$section = $this->_getVar('section');
				return '(($_sect'.$section['name'].'_i == ($_sect'.$section['name'].'_cnt-1)) || ($_sect'.$section['name'].'_i == 0))';
			default:
				return NULL;
		}
	} // end _build();
} // end Opt_Format_Spldatastructure;
