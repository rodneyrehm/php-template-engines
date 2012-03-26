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
 * $Id$
 */

	/**
	 * Some debugging utilities for the OPL projects.
	 */
	class Opl_Debug
	{

		/**
		 * Displays a variable dump with the title.
		 * @param mixed $variable The variable to dump
		 * @param string $desc The dump description.
		 */
		static public function dump($variable, $desc = NULL)
		{
			if(!is_null($desc))
			{
				echo '<h3>'.$desc.'</h3>';
			}
			if(extension_loaded('xdebug'))
			{
				var_dump($variable);
				return;
			}
			
			echo '<pre>'.htmlspecialchars(var_export($variable, true)).'</pre>';
		} // end dump();

		/**
		 * Writes a message to the output system.
		 *
		 * @param string $message The message to write
		 * @param boolean $console Printing on console?
		 */
		static public function write($message, $console = false)
		{
			if($console)
			{
				echo $message.PHP_EOL;
				return;
			}
			echo '<div>'.$message.'</div>'.PHP_EOL;
		} // end write();

		/**
		 * Prints a debug backtrace.
		 */
		static public function backtrace()
		{
			echo '<pre>';
			debug_print_backtrace();
			echo '</pre>';
		} // end backtrace();

		/**
		 * Prints a message onto the standard error output.
		 * @param string $message The message
		 */
		static public function writeErr($message)
		{
			$fp = fopen('php://stderr', 'w');
			fwrite($fp, $message."\r\n");
			fclose($fp);
		} // end writeErr();

		/**
		 * Prints information on the binary flags set in an integer.
		 *
		 * @param integer $int The flag integer
		 * @param boolean $console Print on console?
		 */
		static public function printFlags($int, $console = false)
		{
			$i = 1;
			$j = 1;
			if($console)
			{
				echo "Set flags:\r\n";
			}
			else
			{
				echo '<div>Set flags: <br/>';
			}
			for($j = 0; $j < 32; $j++)
			{
				$i = $i << 2;
				if($int & $i)
				{
					if($console)
					{
						echo $i."\r\n"; 
					}
					else
					{
						echo $i.'<br/>';
					}
				}
			}
			if(!$console)
			{
				echo '</div>';
			}
		} // end printFlags();
	} // end Opl_Debug;
