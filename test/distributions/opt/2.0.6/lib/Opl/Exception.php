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

	/*
	 * Function definitions
	 */

	function Opl_Error_Handler(Opl_Exception $exception)
	{
		$handler = new Opl_ErrorHandler;
		$handler->display($exception);
	} // end Opl_Error_Handler();

	/*
	 * Class definitions
	 */

	class Opl_Exception extends Exception
	{
		protected $_message = '%s';

		public function __construct()
		{
			$args = func_get_args();
			$this->message = vsprintf($this->_message, $args);
			$this->clean();
		} // end __construct();

		public function getLibrary()
		{
			$tokens = explode('_', get_class_name($this));
			return Opl_Registry::get(strtolower($tokens[0]));
		} // end getLibrary();
		
		public function clean()
		{
			/* null */
		} // end clean();
	} // end Opl_Exception;
	
	class Opl_NoTranslationInterface_Exception extends Opl_Exception
	{
		protected $_message = '%s can\'t complete its job: no translation interface defined.';
	} // end Opl_NoTranslationInterface_Exception;
	
	class Opl_InvalidType_Exception extends Opl_Exception
	{
		protected $_message = 'Invalid type of %s: %s required.';
	} // end Opl_InvalidType_Exception;
	
	/*
	 * Filesystem exceptions
	 */
	
	class Opl_Filesystem_Exception extends Opl_Exception{}
	
	class Opl_NotReadable_Exception extends Opl_Filesystem_Exception
	{
		protected $_message = 'The %s "%s" is not readable by PHP.';
	} // end Opl_NotReadable_Exception;
	
	class Opl_NotWriteable_Exception extends Opl_Filesystem_Exception
	{
		protected $_message = 'The %s "%s" is not writeable by PHP.';
	} // end Opl_Not_Writeable_Exception;
	
	class Opl_FileNotExists_Exception extends Opl_Filesystem_Exception
	{
		protected $_message = 'The %s "%s" does not exist in the filesystem.';
	} // end Opl_Not_Writeable_Exception;

	class Opl_InvalidClass_Exception extends Opl_Filesystem_Exception
	{
		protected $_message = '"%s" is not a valid class name.';
	} // end Opl_Not_Writeable_Exception;

	class Opl_InvalidCallback_Exception extends Opl_Exception
	{
		protected $_message = 'The specified library handler is not a valid PHP callback.';
	} // end Opl_InvalidCallback_Exception;
	
	/*
	 * Debugger exceptions
	 */
	
	class Opl_Debug_Exception extends Opl_Exception
	{
		protected $_message = 'This exception should not happen and may be caused by the bug in the OPL library. Please run the script in the debug environment and contact the Invenzzia Team, providing all the information on this exception.';
	} // end Opl_Debug_Exception;
	
	class Opl_OptionNotExists_Exception extends Opl_Debug_Exception
	{
		protected $_message = 'The option "%s" does not exist in %s.';
	} // end Opl_OptionNotExists_Exception;
	
	class Opl_Debug_ItemExists_Exception extends Opl_Debug_Exception
	{
		protected $_message = 'The %s: "%s" already exists.';
	} // end Opl_Debug_ItemExists_Exception;
	
	class Opl_Debug_ItemNotExists_Exception extends Opl_Debug_Exception
	{
		protected $_message = 'The %s: "%s" does not exist.';
	} // end Opl_Debug_ItemNotExists_Exception;
	
	class Opl_Debug_Generic_Exception extends Opl_Debug_Exception
	{
		public function __construct($message)
		{
			$this->message = $message;
		} // end __construct();
	} // end Opl_Debug_Generic_Exception;
