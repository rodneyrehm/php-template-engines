<?php
/**
 * Test block class for the instruction unit testing purposes.
 *
 * @copyright Invenzzia Group 2009
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

class myBlock implements Opt_Block_Interface
{
	public function setView(Opt_View $view)
	{
		echo "VIEW PASSED\r\n";
	} // end setView();

	public function onOpen(Array $attributes)
	{
		echo "ON OPEN: ".sizeof($attributes)."\r\n";
		if(isset($attributes['hide']))
		{
			echo "HIDING\r\n";
			return false;
		}
		return true;
	} // end onOpen();

	public function onClose()
	{
		echo "ON CLOSE\r\n";

	} // end onClose();

	public function onSingle(Array $attributes)
	{
		echo "ON SINGLE: ".sizeof($attributes)."\r\n";
	} // end onSingle();
} // end myBlock;

