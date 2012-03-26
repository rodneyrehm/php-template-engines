<?php
/**
 * Test format for the "custom assign" tests with accompanying test items.
 */

	function variableHandler($item)
	{
		$data = Opl_Registry::get('data');

		return $data[$item];
	} // end variableHandler();

	function modifyVariable($item, $value)
	{
		$data = Opl_Registry::get('data');
		$data[$item] = $value;
		Opl_Registry::register('data', $data);
	} // end modifyVariable();

	class smallClass
	{
		private $_data = array('foo' => 'bar');

		public function readItem($item)
		{
			return $this->_data[$item];
		} // end readItem();

		public function saveItem($item, $value)
		{
			$this->_data[$item] = $value;
		} // end saveItem();
	} // end smallClass;

    class generator implements Opt_Generator_Interface
    {
    	private $_parent;

    	public function __construct($parent = '')
    	{
    		$this->_parent = $parent;
    	} // end __construct();

    	public function generate($name)
    	{
    		$result = array();
    		for($i = 1; $i <= 3; $i++)
    		{
    			$val = $this->_parent.(string)$i;
    			switch($name)
    			{
    				case 'sect1':
    					$result[] = array('val' => $val, 'sect2' => new generator($val.'.'));
    					break;
    				case 'sect2':
    					$result[] = array('val' => $val, 'sect3' => new generator($val.'.'));
    					break;
    				case 'sect3':
    					$result[] = array('val' => $val);
    			}
    		}
    		return $result;
    	} // end generate();
    } // end Opt_Generator_Interface;

	class Opt_Format_Test extends Opt_Compiler_Format
	{
		protected $_supports = array(
			'section', 'variable', 'item'
		);

		protected $_properties = array(
			'section:useReference' => false,
			'section:anyRequests' => null,
			'variable:assign' => true,
			'item:assign' => true,
			'section:itemAssign' => false
		);

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

					if(!is_null($section['parent']))
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
					return 'is_object($_sect'.$section['name'].'_vals) && ($_sect'.$section['name'].'_vals instanceof Traversable) && ($_sect'.$section['name'].'_vals instanceof Countable) && ($_sect'.$section['name'].'_vals->count() > 0)';
				// The code block after the condition
				case 'section:started':
				// The code block before the end of the conditional block.
				case 'section:finished':
				// The code block after the conditional block
				case 'section:done':
				// The code block before entering the loop.
				case 'section:loopBefore':
					$section = $this->_getVar('section');
					if($section['order'] == 'desc')
					{
						return ' $tmp = array(); foreach($_sect'.$section['name'].'_vals as $i => $v){ $tmp[$i] = $v; } $_sect'.$section['name'].'_vals = &$tmp; ';
					}
					return '';
				// The default loop for the ascending order.
				case 'section:startAscLoop':
					$section = $this->_getVar('section');
					return 'foreach($_sect'.$section['name'].'_vals as $_sect'.$section['name'].'_i => $_sect'.$section['name'].'_v){ ';
				// The default loop for the descending order.
				case 'section:startDescLoop':
					$section = $this->_getVar('section');
					return 'foreach($_sect'.$section['name'].'_vals as $_sect'.$section['name'].'_i => $_sect'.$section['name'].'_v){ ';
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
				// Resetting the section to the first element.
				case 'section:reset':
					$section = $this->_getVar('section');
					if($section['order'] == 'asc')
					{
						return '$_sect'.$section['name'].'_vals->reset();';
					}
					else
					{
						return 'end($_sect'.$section['name'].'_vals); $_sect'.$section['name'].'_v = current($_sect'.$section['name'].'_vals); $_sect'.$section['name'].'_i = key($_sect'.$section['name'].'_vals); ';
					}
					break;
				// Moving to the next element.
				case 'section:next':
					$section = $this->_getVar('section');
					if($section['order'] == 'asc')
					{
						return '$_sect'.$section['name'].'_vals->next();';
					}
					else
					{
						return 'prev($_sect'.$section['name'].'_vals);';
					}
					break;
				// Checking whether the iterator is valid.
				case 'section:valid':
					$section = $this->_getVar('section');
					return 'isset($_sect'.$section['name'].'_vals[$_sect'.$section['nesting'].'_i])';
				// Populate the current element
				case 'section:populate':
					$section = $this->_getVar('section');
					return '$_sect'.$section['name'].'_v = current($_sect'.$section['name'].'_vals); $_sect'.$section['name'].'_i = key($_sect'.$section['name'].'_vals);';
				// The code that returns the number of items in the section;
				case 'section:count':
					$section = $this->_getVar('section');
					return '$_sect'.$section['name'].'_cnt';
				// Section item size.
				case 'section:size':
					$section = $this->_getVar('section');
					return '($_sect'.$section['name'].'_v instanceof Countable ? $_sect'.$section['name'].'_v->count() : -1)';
				// Section iterator.
				case 'section:iterator':
					$section = $this->_getVar('section');
					return '$_sect'.$section['name'].'_i';
				// Testing the first element.
				case 'section:isFirst':
					$section = $this->_getVar('section');
					if($section['order'] == 'asc')
					{
						return '$_sect'.$section['nesting'].'_i == 0';
					}
					else
					{
						return '$_sect'.$section['nesting'].'_i == ($_sect'.$section['name'].'_cnt-1)';
					}
				// Testing the last element.
				case 'section:isLast':
					$section = $this->_getVar('section');
					if($section['order'] == 'asc')
					{
						return '$_sect'.$section['nesting'].'_i == ($_sect'.$section['name'].'_cnt-1)';
					}
					else
					{
						return '$_sect'.$section['nesting'].'_i == 0';
					}
				// Testing the extreme element.
				case 'section:isExtreme':
					$section = $this->_getVar('section');
					return '(($_sect'.$section['nesting'].'_i == ($_sect'.$section['name'].'_cnt-1)) || ($_sect'.$section['nesting'].'_i == 0))';
				// The variable access.
				case 'variable:main':
					$this->_applyVars = false;
					$item = $this->_getVar('item');
					return 'variableHandler(\''.$item.'\')';
				case 'variable:assign':
					$this->_applyVars = false;
					$item = $this->_getVar('item');
					return 'modifyVariable(\''.$item.'\', '.$this->_getVar('value').')';
				case 'item:item':
					return '->readItem(\''.$this->_getVar('item').'\')';
				case 'item:assign':
					return '->saveItem(\''.$this->_getVar('item').'\','.$this->_getVar('value').')';
				default:
					return NULL;
			}
		} // end _build();
	} // end Opt_Format_Test;
