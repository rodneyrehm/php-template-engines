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
 * This is a sample form processing tool.
 */
class Form
{
	const FORM_INITIAL = 0;
	const FORM_INVALID = 1;
	const FORM_VALID = 2;

	/**
	 * The list of form fields.
	 * @var Array
	 */
	private $_fields;
	/**
	 * The view that displays the form.
	 * @var Opt_View
	 */
	private $_view;
	/**
	 * The form action field
	 * @var String
	 */
	private $_action;

	/**
	 * The form status
	 * @var Integer
	 */
	private $_status = Form::FORM_INITIAL;

	/**
	 * The validation status of the fields.
	 * @var Array
	 */
	private $_validationStatus = array();

	/**
	 * The processed values
	 * @var Array
	 */
	private $_values = array();

	/**
	 * Creates a new form object for the specified view.
	 * @param Opt_View $view The view
	 */
	public function __construct(Opt_View $view)
	{
		$this->_view = $view;
		$view->form = $this;

		$this->_fields = array();
	} // end __construct();

	/**
	 * Sets the value of the "action" form field.
	 * @param String $action The action URL.
	 */
	public function setAction($action)
	{
		$this->_action = $action;
	} // end setAction();

	/**
	 * Returns the form parameters.
	 *
	 * @param String $name The parameter name
	 * @return Mixed
	 */
	public function __get($name)
	{
		if($name[0] == '_')
		{
			return null;
		}
		// Actually, we have only two parameters.
		switch($name)
		{
			case 'action':
				return $this->_action;
			case 'valid':
				return ($this->_status != self::FORM_INVALID ? true : false);
			default:

		}
	} // end __get();

	/**
	 * Returns the form status.
	 * @return Integer
	 */
	public function status()
	{
		return $this->_status;
	} // end valid();

	/**
	 * Adds a new field to the form.
	 *
	 * @param String $name The name of the field
	 * @param String $rules The validation rules for the field
	 * @param String $errorMessage The error message that must be displayed, if the field is filled with an incorrect value.
	 */
	public function addField($name, $rules, $errorMessage = null)
	{
		$this->_fields[$name] = array('rules' => explode(',',$rules), 'error' => $errorMessage);
	} // end addField();

	/**
	 * Validates the form and returns the result.
	 * @return Boolean
	 */
	public function validate()
	{
		if($_SERVER['REQUEST_METHOD'] != 'POST')
		{
			return false;
		}

		$this->_status = self::FORM_VALID;
		foreach($this->_fields as $name => $info)
		{
			// Check if the field is actually set, handling the "required" flag.
			if(empty($_POST[$name]))
			{                      
				if(in_array('required', $info['rules']))
				{
					$this->_validationStatus[$name] = $info['error'];
					$this->_status = self::FORM_INVALID;
				}
				else
				{
					// field is empty and not required
					$this->_values[$name] = null;
				}
				continue;
			}
			
			foreach($info['rules'] as $rule)
			{
				$data = explode('=', $rule);
				$ok = true;
				switch($data[0])
				{
					// The field must be an integer.
					case 'integer':
						$ok = ctype_digit($_POST[$name]);
						break;
					// The field must be URL
					case 'url':
						$test = filter_var($_POST[$name], FILTER_VALIDATE_URL, FILTER_SANITIZE_URL);
						if($test === false)
						{
							$ok = false;
						}
						break;
					// The field must be e-mail
					case 'email':
						$test = filter_var($_POST[$name], FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL);
						if($test === false)
						{
							$ok = false;
						}
						break;
					// Maximum field length
					case 'max_len':
						$ok = (strlen($_POST[$name]) <= $data[1]);
						break;
					// Minimum field length.
					case 'min_len':
						$ok = (strlen($_POST[$name]) >= $data[1]);
						break;
				}
				if(!$ok)
				{
					$this->_validationStatus[$name] = $info['error'];
					$this->_status = self::FORM_INVALID;
					break;
				}
			}
			if($ok)
			{
				$this->_values[$name] = $_POST[$name];
			}
		}
		return ($this->_status == self::FORM_VALID);
	} // end validate();

	/**
	 * Returns the validation status of a certain field.
	 *
	 * @param String $name The field name.
	 * @return Boolean
	 */
	public function getValidationStatus($name)
	{
		return !isset($this->_validationStatus[$name]);
	} // end getValidationStatus();
	
	/**
	 * Checks if given field is required
	 *
	 * @param String $name The field name.
	 * @return Boolean
	 */
	public function isRequired($name)
	{
		return in_array('required', $this->_fields[$name]['rules']);
	} // end isRequired();

	/**
	 * Returns the error message for a certain field or NULL
	 * if the field is filled correctly.
	 *
	 * @param String $name The field name.
	 * @return String|Null
	 */
	public function getErrorMessage($name)
	{
		if(!isset($this->_validationStatus[$name]))
		{
			return null;
		}
		return $this->_validationStatus[$name];
	} // end getErrorMessage();

	/**
	 * Returns the valid values.
	 * @return Array
	 */
	public function getValues()
	{
		return $this->_values;
	} // end getValues();
} // end Form;
