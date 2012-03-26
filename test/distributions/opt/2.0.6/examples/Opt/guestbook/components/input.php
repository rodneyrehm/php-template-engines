<?php
/*
 *  OPEN POWER LIBS EXAMPLES <http://www.invenzzia.org>
 *  ===================================================
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) 2008 Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 */

/**
 * The class represents a simple INPUT field as a component.
 */
class InputComponent extends BaseComponent
{
	/**
	 * Processes the component displaying.
	 *
	 * @param Array $attributes The attributes of opt:display tag.
	 */
	public function display($attributes = array())
	{
		$attributes['name'] = $this->_params['name'];
		$attributes['class'] = 'inputText';
		$attributes['id'] = 'form_'.$this->_params['name'].'_id';

		if($this->_form->status() == Form::FORM_INVALID)
		{
			$attributes['value'] = htmlspecialchars($_POST[$this->_params['name']]);
		}
		elseif(isset($this->_params['value']))
		{
			$attributes['value'] = htmlspecialchars($this->_params['value']);
		}

		echo '<input';
		foreach($attributes as $name => $value)
		{
			echo ' '.$name.'="'.$value.'"';
		}
		echo ' />';
	} // end display();
} // end InputComponent;
