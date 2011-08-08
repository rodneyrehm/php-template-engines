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
 * Represents a textarea as a component.
 */
class TextareaComponent extends BaseComponent
{
	/**
	 * Displays the textarea.
	 *
	 * @param Array $attributes The attributes of "opt:display"
	 */
	public function display($attributes = array())
	{
		$attributes['name'] = $this->_params['name'];
		$attributes['id'] = 'form_'.$this->_params['name'].'_id';

		echo '<textarea';
		foreach($attributes as $name => $value)
		{
			echo ' '.$name.'="'.$value.'"';
		}
		echo '>';
		
		if($this->_form->status() == Form::FORM_INVALID)
		{
			echo htmlspecialchars($_POST[$this->_params['name']]);
		}
		elseif(isset($this->_params['value']))
		{
			echo htmlspecialchars($this->_params['value']);
		}
		echo '</textarea>';
	} // end display();
} // end TextareaComponent;
