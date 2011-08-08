<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 */

// Objective format for OPT

	class Opt_Format_Objective extends Opt_Compiler_Format
	{
		protected $_supports = array(
			'section', 'variable', 'item'
		);

		protected $_properties = array(
			'section:useReference' => true,
			'section:anyRequests' => null,
			'section:itemAssign' => false,
			'section:variableAssign' => true,
			'variable:useReference' => true,
			'variable:assign' => true,
			'item:assign' => true,
			'item:useReference' => true,
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

					if($section['parent'] !== null)
					{
						$parent = Opt_Instruction_BaseSection::getSection($section['parent']);
						$parent['format']->assign('item', $section['name']);
						return '$_sect'.$section['name'].'_vals = '.$parent['format']->get('section:variable').'; ';
					}
					elseif(!is_null($section['datasource']))
					{
						return '$_sect'.$section['name'].'_vals = '.$section['datasource'].'; ';
					}
					else
					{
						$this->assign('item', $section['name']);
						return '$_sect'.$section['name'].'_vals = '.$this->get('variable:main').'; ';
					}
				// The end of the section loop.
				case 'section:endLoop':
					return ' } ';
				// The condition that should test if the section is not empty.
				case 'section:isNotEmpty':
					$section = $this->_getVar('section');
					return 'is_object($_sect'.$section['name'].'_vals) && ($_sect'.$section['name'].'_vals instanceof Traversable) && ($_sect'.$section['name'].'_vals instanceof Countable) && (($_sect'.$section['name'].'_cnt = $_sect'.$section['name'].'_vals->count()) > 0)';
				// The code block after the condition
				case 'section:started':
					$section = $this->_getVar('section');
					return 'if($_sect'.$section['name'].'_vals instanceof IteratorAggregate){ $_sect'.$section['name'].'_vals = $_sect'.$section['name'].'_vals->getIterator(); }';
				// The code block before the end of the conditional block.
				case 'section:finished':
					return '';
				// The code block after the conditional block
				case 'section:done':
					return '';
				// The code block before entering the loop.
				case 'section:loopBefore':
					$section = $this->_getVar('section');
					if($section['order'] == 'desc')
					{
						$this->_vals = '$_sect'.$section['name'].'_tmp';
						return ' $_sect'.$section['name'].'_tmp = array(); foreach($_sect'.$section['name'].'_vals as $i => $v){ $_sect'.$section['name'].'_tmp[$i] = $v; } ';
					}
					else
					{
						$this->_vals = '$_sect'.$section['name'].'_vals';
					}
					return '';
				// The default loop for the ascending order.
				case 'section:startAscLoop':
					$section = $this->_getVar('section');
					return 'foreach($_sect'.$section['name'].'_vals as $_sect'.$section['name'].'_i => $_sect'.$section['name'].'_v){ ';
				// The default loop for the descending order.
				case 'section:startDescLoop':
					$section = $this->_getVar('section');
					return 'for($_sect'.$section['nesting'].'_i = $_sect'.$section['name'].'_cnt-1; $_sect'.$section['nesting'].'_i >= 0; $_sect'.$section['nesting'].'_i--){ $_sect'.$section['name'].'_v = '.$this->_vals.'[$_sect'.$section['nesting'].'_i]; ';
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
					return '$_sect'.$section['name'].'_v->'.$this->_getVar('item');
				case 'section:variableAssign':
					$section = $this->_getVar('section');
					if($this->isDecorating())
					{
						return '$_sect'.$section['name'].'_v'.$this->_decorated->get('item:assign');
					}
					return '$_sect'.$section['name'].'_v->'.$this->_getVar('item').'='.$this->_getVar('value');
				// Resetting the section to the first element.
				case 'section:reset':
					$section = $this->_getVar('section');
					if($section['order'] == 'asc')
					{
						return $this->_vals.'->rewind();';
					}
					else
					{
						return 'end('.$this->_vals.'); $_sect'.$section['name'].'_v = current('.$this->_vals.'); $_sect'.$section['name'].'_i = key('.$this->_vals.'); ';
					}
					break;
				// Moving to the next element.
				case 'section:next':
					$section = $this->_getVar('section');
					if($section['order'] == 'asc')
					{
						return $this->_vals.'->next();';
					}
					else
					{
						return 'prev('.$this->_vals.'); $_sect'.$section['name'].'_i = key('.$this->_vals.');';
					}
					break;
				// Checking whether the iterator is valid.
				case 'section:valid':
					$section = $this->_getVar('section');
					if($section['order'] == 'asc')
					{
						return $this->_vals.'->valid()';
					}
					else
					{
						return 'isset('.$this->_vals.'[$_sect'.$section['name'].'_i])';
					}
				// Populate the current element
				case 'section:populate':
					$section = $this->_getVar('section');
					if($section['order'] == 'asc')
					{
						return '$_sect'.$section['name'].'_v = '.$this->_vals.'->current(); $_sect'.$section['name'].'_i = '.$this->_vals.'->key();';
					}
					else
					{
						return '$_sect'.$section['name'].'_v = current('.$this->_vals.'); $_sect'.$section['name'].'_i = key('.$this->_vals.');';
					}
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
				// The variable access.
				case 'variable:main':
					$this->_applyVars = false;
					$item = $this->_getVar('item');
					if($this->_getVar('access') == Opt_Class::ACCESS_LOCAL)
					{
						return '$this->_data[\''.$item.'\']';
					}
					else
					{
						return 'self::$_global[\''.$item.'\']';
					}
				case 'variable:assign':
					$this->_applyVars = false;
					$item = $this->_getVar('item');
					if($this->_getVar('access') == Opt_Class::ACCESS_LOCAL)
					{
						return '$this->_data[\''.$item.'\']='.$this->_getVar('value');
					}
					else
					{
						return 'self::$_global[\''.$item.'\']='.$this->_getVar('value');
					}
				case 'item:item':
					return '->'.$this->_getVar('item');
				case 'item:assign':
					return '->'.$this->_getVar('item').'='.$this->_getVar('value');
				default:
					return NULL;
			}
		} // end _build();
	} // end Opt_Format_Objective;
