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

 // The format class, where sub-sections are parts of the upper-level section array.
 
	class Opt_Format_SingleArray extends Opt_Format_Array
	{
		protected $_properties = array(
			'section:useReference' => true,
			'section:anyRequests' => null,
			'variable:assign' => true,
			'variable:useReference' => true,
			'item:assign' => true,
			'item:useReference' => true,
			'section:itemAssign' => false,
			'section:variableAssign' => true
		);

		protected function _build($hookName)
		{
			if($hookName == 'section:init')
			{
				$section = $this->_getVar('section');

				if(!is_null($section['parent']))
				{
					$parent = Opt_Instruction_BaseSection::getSection($section['parent']);
					$parent['format']->assign('item', $section['name']);
					if($parent['format']->property('section:useReference'))
					{
						return '$_sect'.$section['name'].'_vals = &'.$parent['format']->get('section:variable').'; ';
					}
					return '$_sect'.$section['name'].'_vals = '.$parent['format']->get('section:variable').'; ';
				}
				elseif(!is_null($section['datasource']))
				{
					return '$_sect'.$section['name'].'_vals = '.$section['datasource'].'; ';
				}
				else
				{
					$this->assign('item', $section['name']);
					return '$_sect'.$section['name'].'_vals = &'.$this->get('variable:main').'; ';
				}
			}
			else
			{
				return parent::_build($hookName);
			}
			return NULL;
		} // end _build();
	} // end Opt_Format_SingleArray;
