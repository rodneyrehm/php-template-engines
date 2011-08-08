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
 * This is an implementation of a sample guestbook written with
 * Open Power Template 2.
 */

/**
 * An exception class.
 */
class Opl_NoFormDefined_Exception extends Opl_Exception
{
	protected $_message = 'No form defined for the component %s.';
} // end Opl_NoFormDefined_Exception;

/**
 * The class represents a base component. The other components simply
 * overwrite one method: display() according to their needs, whereas
 * the logic is common.
 *
 * @abstract
 */
abstract class BaseComponent implements Opt_Component_Interface
{
	/**
	 * The component parameter list
	 * @var Array
	 */
	protected $_params = array();

	/**
	 * The view object that deploys the component
	 * @var Opt_View
	 */
	protected $_view;

	/**
	 * The form object.
	 * @var form
	 */
	protected $_form;

	/**
	 * Constructs the component with the specified name.
	 *
	 * @param String $name The name of the newly created component.
	 */
	public function __construct($name = '')
	{
		$this->_params['name'] = $name;
	} // end __construct();

	/**
	 * The deployment method. It is automatically called by OPT if
	 * the component is deployed in the template. We can perform some
	 * actions then.
	 *
	 * @param Opt_View $view The view that deploys the component.
	 */
	public function setView(Opt_View $view)
	{
		$this->_view = $view;
		$this->_form = $view->get('form');

		if(!is_object($this->_form))
		{
			throw new Opl_NoFormDefined_Exception(get_class($this).':'.$this->_params['name']);
		}
	} // end setView();

	/**
	 * This is unused.
	 *
	 * @param Mixed &$data The datasource
	 */
	public function setDatasource($data)
	{
		/* null */
	} // end setDatasource();

	/**
	 * Sets the value of a component parameter.
	 *
	 * @param String $name The parameter name
	 * @param Mixed $value The parameter value
	 */
	public function set($name, $value)
	{
		$this->_params[$name] = $value;
	} // end set();

	/**
	 * Returns the component parameter value.
	 * @param String $name The parameter name.
	 * @return Mixed
	 */
	public function get($name)
	{
		if($name == 'id')
		{
			return $this->_params['name'].'_id';
		}
		if(!isset($this->_params[$name]))
		{
			return null;
		}
		return $this->_params[$name];
	} // end get();

	/**
	 * Checks if the parameter is defined.
	 *
	 * @param String $name The parameter name.
	 * @return Boolean
	 */
	public function defined($name)
	{
		return isset($this->_params[$name]);
	} // end defined();

	/**
	 * Allows to perform the attribute modifications to certain HTML
	 * tags (that are in the "com" namespace), so that we can add there
	 * extra CSS classes depending on the component state.
	 *
	 * @param String $nodeName The tag name.
	 * @param Array $attributes The source attribute list
	 * @return Array
	 */
	public function manageAttributes($nodeName, Array $attributes)
	{
		if($this->_form->status() != Form::FORM_INVALID)
		{
			return $attributes;
		}
		if($nodeName == 'div' && $this->_form->getValidationStatus($this->_params['name']))
		{
			$attributes['class'] = $attributes['class'].' '.$this->_view->getTemplateVar('formInvalidFieldRowClass');
		}
		return $attributes;
	} // end manageAttributes();

	/**
	 * Checks if a component event occurs.
	 *
	 * @param String $event The event name
	 * @return Boolean
	 */
	public function processEvent($event)
	{
		$ok = $this->_form->getValidationStatus($this->_params['name']);
		switch($event)
		{
			case 'isRequired':
				if($this->_form->isRequired($this->_params['name']))
				{
					return true;
				}
				break;
			case 'error':
				if(!$ok)
				{
					$this->_view->errorMessage = $this->_form->getErrorMessage($this->_params['name']);
					return true;
				}
		}
		return false;
	} // end processEvent();
} // end BaseComponent;
