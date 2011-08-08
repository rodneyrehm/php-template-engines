<?php
/**
 * Test component class for the instruction unit testing purposes.
 *
 * @copyright Invenzzia Group 2009
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

	class myComponent implements Opt_Component_Interface
	{
		private $params = array();

		public function __construct($name = '')
		{
			/* null */
		} // end __construct();

		public function setView(Opt_View $view)
		{
			echo "VIEW PASSED\r\n";
		} // end setView();

		public function setDatasource($data)
		{
			if(is_array($data))
			{
				echo "DATASOURCE PASSED\r\n";
			}
		} // end setDatasource();

		public function set($name, $value)
		{
			$this->params[$name] = $value;
			echo "PARAM ".$name." PASSED\r\n";
		} // end set();

		public function get($name)
		{
			echo "PARAM ".$name." RETURNED\r\n";
			return $this->params[$name];
		} // end set();

		public function defined($name)
		{
			/* null */
		} // end defined();

		public function display($attributes = array())
		{
			if(sizeof($attributes) == 0)
			{
				echo "COMPONENT DISPLAYED\r\n";
			}
			else
			{
				echo "COMPONENT DISPLAYED WITH:\r\n";
			}
			foreach($attributes as $name => $value)
			{
				echo $name.': '.$value."\r\n";
			}
		} // end display();

		public function processEvent($name)
		{
			if($name == 'falseEvent')
			{
				echo "FALSE EVENT CHECKED\r\n";
				return false;
			}
			echo "TRUE EVENT LAUNCHED\r\n";
			return true;
		} // end processEvent();

		public function manageAttributes($tagName, Array $attributes)
		{
			echo "ATTRIBUTE MANAGEMENT FOR: ".$tagName."\r\n";
			foreach($attributes as $name => $value)
			{
				echo $name.': '.$value."\r\n";
			}
			return $attributes;
		} // end manageAttributes();
	} // end myComponent;
