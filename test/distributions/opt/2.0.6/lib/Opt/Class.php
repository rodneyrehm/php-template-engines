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
 */

	/*
	 * Interface definitions
	 */
	interface Opt_Component_Interface
	{
		public function __construct($name = '');
		public function setView(Opt_View $view);
		public function setDatasource($data);

		public function set($name, $value);
		public function get($name);
		public function defined($name);

		public function display($attributes = array());
		public function processEvent($name);
		public function manageAttributes($tagName, Array $attributes);
	} // end Opt_Component_Interface;
	
	interface Opt_Block_Interface
	{
		public function setView(Opt_View $view);
		public function onOpen(Array $attributes);
		public function onClose();
		public function onSingle(Array $attributes);
	} // end Opt_Block_Interface;
	
	interface Opt_Caching_Interface
	{
		public function templateCacheStart(Opt_View $view);
		public function templateCacheStop(Opt_View $view);
	} // end Opt_Caching_Interface;
	
	interface Opt_Output_Interface
	{
		public function getName();
		public function render(Opt_View $view);
	} // end Opt_Output_Interface;

	interface Opt_Generator_Interface
	{
		public function generate($what);
	} // end Opt_Generator_Interface;
	
	/*
	 * Class definitions
	 */

	class Opt_Class extends Opl_Class
	{
		// Constants
		const CM_DEFAULT = 0;
		const CM_REBUILD = 1;
		const CM_PERFORMANCE = 2;
		
		const ACCESS_LOCAL = 0;
		const ACCESS_GLOBAL = 1;
		
		const CHOOSE_MODE = 0;
		const XML_MODE = 1;
		const QUIRKS_MODE = 2;

		const OPT_INSTRUCTION = 1;
		const OPT_NAMESPACE = 2;
		const OPT_FORMAT = 3;
		const OPT_COMPONENT = 4;
		const OPT_BLOCK = 5;
		const PHP_FUNCTION = 6;
		const PHP_CLASS = 7;
		const XML_ENTITY = 8;
	
		const VERSION = '2.0.6';
		const ERR_STANDARD = 6135; // E_ALL^E_NOTICE
	
		// Directory configuration
		public $sourceDir = NULL;
		public $compileDir = NULL;
		public $cacheDir = NULL;

		// Template configuration
		public $compileId = NULL;

		// Front-end configuration
		public $compileMode = self::CM_DEFAULT;
		public $charset = 'utf-8';
		public $contentType = 0;
		public $gzipCompression = true;
		public $headerBuffering = false;
		public $contentNegotiation = false;
		public $errorReporting = self::ERR_STANDARD;
		public $stdStream = 'file';
		public $debugConsole = false;
		public $allowRelativePaths = false;

		// Function configuration
		public $moneyFormat;
		public $numberDecimals;
		public $numberDecPoint;
		public $numberThousandSep;
		public $pluralForms = array(
			'%d == 1' => 0,
			'%d' => 1
		);

		// Compiler configuration
		public $mode = self::XML_MODE;
		public $unicodeNames = false;
		public $htmlAttributes = false;
		public $printComments = false;
		public $prologRequired = true;
		public $stripWhitespaces = true;
		public $singleRootNode = true;
		public $basicOOP = true;
		public $advancedOOP = true;
		public $backticks = null;
		public $translate = null;
		public $strictCallbacks = true;
		public $htmlEntities = true;
		public $escape = true;
		public $variableAccess = self::ACCESS_LOCAL;
		public $defaultFormat = 'Array';

		// Data
		protected $_tf = NULL;	// translation interface
		
		// Add-ons
		protected $_cache;

		protected $_instructions = array('Opt_Instruction_Section', 'Opt_Instruction_Tree',
			'Opt_Instruction_Grid', 'Opt_Instruction_Selector', 'Opt_Instruction_Repeat',
			'Opt_Instruction_Snippet', 'Opt_Instruction_Extend',
			'Opt_Instruction_For', 'Opt_Instruction_Foreach', 'Opt_Instruction_If',
			'Opt_Instruction_Put', 'Opt_Instruction_Capture', 'Opt_Instruction_Attribute',
			'Opt_Instruction_Tag', 'Opt_Instruction_Root', 'Opt_Instruction_Prolog',
			'Opt_Instruction_Dtd', 'Opt_Instruction_Literal', 'Opt_Instruction_Include',
			'Opt_Instruction_Dynamic', 'Opt_Instruction_Component', 'Opt_Instruction_Block');
		protected $_functions = array(
			'money' => 'Opt_Function::money', 'number' => 'Opt_Function::number', 'spacify' => 'Opt_Function::spacify',
			'firstof' => 'Opt_Function::firstof', 'indent' => 'Opt_Function::indent', 'strip' => 'Opt_Function::strip',
			'stripTags' => 'Opt_Function::stripTags', 'upper' => 'Opt_Function::upper', 'lower' => 'Opt_Function::lower',
			'capitalize' => 'Opt_Function::capitalize', 'countWords' => 'str_word_count', 'countChars' => 'strlen',
			'replace' => '#3,1,2#str_replace', 'repeat' => 'str_repeat', 'nl2br' => 'Opt_Function::nl2br', 'date' => 'date',
			'regexReplace' => '#3,1,2#preg_replace', 'truncate' => 'Opt_Function::truncate', 'wordWrap' => 'Opt_Function::wordwrap',
			'contains' => 'Opt_Function::contains', 'count' => 'sizeof', 'sum' => 'Opt_Function::sum', 'average' => 'Opt_Function::average',
			'absolute' => 'Opt_Function::absolute', 'stddev' => 'Opt_Function::stddev', 'range' => 'Opt_Function::range',
			'isUrl' => 'Opt_Function::isUrl', 'isImage' => 'Opt_Function::isImage', 'stddev' => 'Opt_Function::stddev',
			'entity' => 'Opt_Function::entity', 'scalar' => 'is_scalar', 'containsKey' => 'Opt_Function::containsKey',
			'cycle' => 'Opt_Function::cycle', 'autoLink' => 'Opt_Function::autoLink', 'pluralize' => 'Opt_Function::pluralize',
			'countSubstring' => 'Opt_Function::countSubstring', 'pad' => 'Opt_Function::pad', 'autoLink' => 'Opt_Function::autoLink',
			'position' => 'strpos'
		);
		protected $_classes = array();
		protected $_components = array();
		protected $_blocks = array();
		protected $_namespaces = array(1 => 'opt', 'com', 'parse');
		protected $_formats = array(
			'Array' => 'Opt_Format_Array',
			'SingleArray' => 'Opt_Format_SingleArray',
			'StaticGenerator' => 'Opt_Format_StaticGenerator',
			'RuntimeGenerator' => 'Opt_Format_RuntimeGenerator',
			'Objective' => 'Opt_Format_Objective',
			'SplDatastructure' => 'Opt_Format_SplDatastructure');
		protected $_entities = array('lb' => '{', 'rb' => '}');
		protected $_buffers = array();

		// Status
		protected $_init = false;

		// Other
		protected $_compiler;
		
		/*
		 * Template parsing
		 */

		/**
		 * Returns the compiler object and optionally loads the necessary classes. Unless
		 * you develop instructions or reimplement various core features you do not have
		 * to use this method.
		 *
		 * @return Opt_Compiler_Class The compiler
		 */
		public function getCompiler()
		{
			if(!is_object($this->_compiler))
			{
				$this->_compiler = new Opt_Compiler_Class($this);
			}
			return $this->_compiler;
		} // end getCompiler();

		/*
		 * Extensions and configuration
		 */

		/**
		 * Performs the main initialization of OPT. If the optional argument `$config` is
		 * specified, it is transparently sent to Opt_Class::loadConfig(). Before using this
		 * method, we are obligated to configure the library and load the necessary extensions.
		 *
		 * @param mixed $config = null The optional configuration to be loaded
		 */
		public function setup($config = null)
		{
			if(is_array($config))
			{
				$this->loadConfig($config);
			}
			if(!is_null($this->pluginDir))
			{
				$this->loadPlugins();
			}

			if(Opl_Registry::exists('opl_translate'))
			{
				$this->setTranslationInterface(Opl_Registry::get('opl_translate'));
			}
			if(Opl_Registry::getState('opl_debug_console') || $this->debugConsole)
			{
				$this->debugConsole = true;				
				Opt_Support::initDebugConsole($this);
			}
			
			// Check paths etc.
			if(is_string($this->sourceDir))
			{
				$this->sourceDir = array('file' => $this->sourceDir);
			}
			foreach($this->sourceDir as &$path)
			{
				$this->_securePath($path);
			}
			$this->_securePath($this->compileDir);
			$this->_init = true;
		} // end setup();

		/**
		 * Registers a new add-on in OPT identified by `$type`. The type is identified
		 * by the appropriate Opt_Class constant. The semantics of the next arguments
		 * depends on the registered add-on.
		 *
		 * Note that you may register several add-ons at the same time by passing an
		 * array as the second argument.
		 *
		 * @param int $type The type of registered item(s).
		 * @param mixed $item The item or a list of items to be registered
		 * @param mixed $addon = null Used in several types of add-ons
		 * @return void
		 */
		public function register($type, $item, $addon = null)
		{
			if($this->_init)
			{
				throw new Opt_Initialization_Exception($this->_init, 'register an item');
			}
			
			$map = array(1 => '_instructions', '_namespaces', '_formats', '_components', '_blocks', '_functions', '_classes', '_entities');
			$whereto = $map[$type];
			// Massive registration
			if(is_array($item))
			{
				$this->$whereto = array_merge($this->$whereto, $item);
				return;
			}
			switch($type)
			{
				case self::OPT_FORMAT:
					if($addon === null)
					{
						$addon = 'Opt_Format_'.$item;
					}
					$a = &$this->$whereto;
					$a[$item] = $addon;
					break;
				case self::OPT_INSTRUCTION:
					if($addon === null)
					{
						$addon = 'Opt_Instruction_'.$item;
					}
					$a = &$this->$whereto;
					$a[$item] = $addon;
					break;
				case self::OPT_NAMESPACE:
					$a = &$this->$whereto;
					$a[] = $item;
					break;
				default:
					if($addon === null)
					{
						throw new BadMethodCallException('Missing argument 3 for Opt_Class::register()');
					}
					$a = &$this->$whereto;
					$a[$item] = $addon;
			}
		} // end register();

		/**
		 * Registers a new translation interface to be used in templates. The translation
		 * interface must implement Opl_Translation_Interface. If the specified parameter
		 * is not a valid translation interface, the method unregisters the already set one
		 * and returns false.
		 *
		 * @param Opl_Translation_Interface $tf  The translation interface or "null".
		 * @return boolean True, if the translation interface was properly set.
		 */
		public function setTranslationInterface($tf)
		{
			if(!$tf instanceof Opl_Translation_Interface)
			{
				$this->_tf = null;
				return false;
			}
			$this->_tf = $tf;
			return true;
		} // end setTranslationInterface();

		/**
		 * Returns the current translation interface assigned to OPT.
		 *
		 * @return Opl_Translation_Interface The translation interface.
		 */
		public function getTranslationInterface()
		{
			return $this->_tf;
		} // end getTranslationInterface();

		/**
		 * Sets the global caching system to use in all the views.
		 *
		 * @param Opt_Caching_Interface $cache=null The caching interface
		 */
		public function setCache(Opt_Caching_Interface $cache = null)
		{
			$this->_cache = $cache;
		} // end setCache();

		/**
		 * Returns the current global caching system.
		 *
		 * @return Opt_Caching_Interface
		 */
		public function getCache()
		{
			return $this->_cache;
		} // end getCache();

		/**
		 * An implementation of advisory output buffering which allows us
		 * to tell us, whether another part of the script opened the requested
		 * buffer.
		 *
		 * @param string $buffer The buffer name
		 * @param boolean $state The new buffer state: true to open, false to close.
		 */
		public function setBufferState($buffer, $state)
		{
			if($state)
			{
				if(!isset($this->_buffers[$buffer]))
				{
					$this->_buffers[$buffer] = 1;
				}
				else
				{
					$this->_buffers[$buffer]++;
				}
			}
			else
			{
				if(isset($this->_buffers[$buffer]) && $this->_buffers[$buffer] > 0)
				{
					$this->_buffers[$buffer]--;
				}
			}
		} // end setBufferState();

		/**
		 * Returns the state of the specified output buffer.
		 *
		 * @param String $buffer Buffer name
		 * @return Boolean
		 */
		public function getBufferState($buffer)
		{
			if(!isset($this->_buffers[$buffer]))
			{
				return false;
			}
			return ($this->_buffers[$buffer] > 0);
		} // end getBufferState();

		/*
		 * Internal use
		 */

		/**
		 * Allows the read access to some of the internal structures for the
		 * template compiler.
		 *
		 * @internal
		 * @param string $name The structure to be returned.
		 * @return array The returned structure.
		 */
		public function _getList($name)
		{
			static $list;
			if(is_null($list))
			{
				$list = array('_instructions', '_namespaces', '_formats', '_components', '_blocks', '_functions', '_classes', '_tf', '_entities');
			}
			if(in_array($name, $list))
			{
				return $this->$name;
			}
			return NULL;
		} // end _getList();

		/**
		 * The helper function for the plugin subsystem. It returns the
		 * PHP code that loads the specified plugin.
		 *
		 * @internal
		 * @param String $directory The plugin directory
		 * @param SplFileInfo $file The loaded file
		 * @return String
		 */
		protected function _pluginLoader($directory, SplFileInfo $file)
		{
			$ns = explode('.', $file->getFilename());
			if(end($ns) == 'php')
			{
				switch($ns[0])
				{
					case 'instruction':
						return 'Opl_Loader::mapAbsolute(\'Opt_Instruction_'.$ns[1].'\', \''.$directory.$file->getFilename().'\'); $this->register(Opt_Class::OPT_INSTRUCTION, \''.$ns[1].'\'); ';
					case 'format':
						return 'Opl_Loader::mapAbsolute(\'Opt_Format_'.$ns[1].'\', \''.$directory.$file->getFilename().'\'); $this->register(Opt_Class::OPT_FORMAT, \''.$ns[1].'\'); ';
					default:
						return ' require(\''.$directory.$file->getFilename().'\'); ';
				}
			}
		} // end _pluginLoader();

		/**
		 * Parses the stream in the template path name and returns
		 * the real path.
		 *
		 * @internal
		 * @param String $name Template filename
		 * @return String
		 */
		public function _stream($name)
		{
			if(strpos($name, ':') !== FALSE)
			{
				// We get the stream ID from the given filename.
				$data = explode(':', $name);
				if(!isset($this->sourceDir[$data[0]]))
				{
					throw new Opt_ObjectNotExists_Exception('resource', $data[0]);
				}
				if(!$this->allowRelativePaths && strpos($data[1], '../') !== false)
				{
					throw new Opt_NotSupported_Exception('relative paths', $data[1]);
				}
				return $this->sourceDir[$data[0]].$data[1];
			}
			// Here, the standard stream is used.
			if(!isset($this->sourceDir[$this->stdStream]))
			{
				throw new Opt_ObjectNotExists_Exception('resource', $this->stdStream);
			}
			if(!$this->allowRelativePaths && strpos($name, '../') !== false)
			{
				throw new Opt_NotSupported_Exception('relative paths', $name);
			}
			return $this->sourceDir[$this->stdStream].$name;
		} // end _stream();

		/**
		 * Loads the template source code. Returns the template body or
		 * the array with two (false) values in case of problems.
		 *
		 * @internal
		 * @param String $filename The template filename
		 * @param Boolean $exception Do we inform about the problems with exception?
		 * @return String|Array
		 */
		public function _getSource($filename, $exception = true)
		{
			$item = $this->_stream($filename);
			if(!file_exists($item))
			{
				if(!$exception)
				{
					return array(false, false);
				}
				throw new Opt_TemplateNotFound_Exception($item);
			}
			return file_get_contents($item);
		} // end _getSource();

		/**
		 * The class constructor - registers the main object in the
		 * OPL registry.
		 */
		public function __construct()
		{
			Opl_Registry::register('opt', $this);
		} // end __construct();

		/**
		 * The destructor. Clears the output buffers and optionally
		 * displays the debug console.
		 */
		public function __destruct()
		{
			if($this->debugConsole)
			{
				try
				{
					Opt_Support::updateTimers();
					Opl_Debug_Console::display();
				}
				catch(Opl_Exception $e)
				{
					die('<div style="background: #f77777;">Opt_Class destructor exception: '.$e->getMessage().'</div>');
				}
			}
		} // end __destruct();
	} // end Opt_Class;

	/**
	 * The main view class.
	 */
	class Opt_View
	{
		const VAR_LOCAL = false;
		const VAR_GLOBAL = true;

		/**
		 * A reference to the main class object.
		 * @var Opt_Class
		 */
		private $_tpl;

		/**
		 * The template name
		 * @var string
		 */
		private $_template;

		/**
		 * Data format information storage
		 * @var array
		 */
		private $_formatInfo = array();

		/**
		 * Template inheritance storage for the inflectors
		 * @var array
		 */
		private $_inheritance = array();

		/**
		 * Template inheritance storage for the compiler.
		 * @var array
		 */
		private $_cplInheritance = array();

		/**
		 * View data
		 * @var array
		 */
		private $_data = array();

		/**
		 * Translation interface
		 * @var Opl_Translation_Interface
		 */
		private $_tf;

		/**
		 * The information for the debugger: processing time
		 * @var integer
		 */
		private $_processingTime = null;

		/**
		 * The branch name for the template inheritance.
		 * @var string
		 */
		private $_branch = null;

		/**
		 * The caching system used in the view.
		 * @var Opt_Caching_Interface
		 */
		private $_cache = null;

		/**
		 * The compiler mode (XML/HTML or quirks).
		 * @var integer
		 */
		private $_mode;

		/**
		 * Part of the caching system to integrate with opt:dynamic instruction.
		 * @var array
		 */
		private $_outputBuffer = array();

		/**
		 * The template variable storage
		 * @static
		 * @var array
		 */
		static private $_vars = array();

		/**
		 * The list of the captured content.
		 * @static
		 * @var array
		 */
		static private $_capture = array();

		/**
		 * The global template data
		 * @static
		 * @var array
		 */
		static private $_global = array();

		/**
		 * The global data format information
		 * @static
		 * @var array
		 */
		static private $_globalFormatInfo = array();

		/**
		 * Creates a new view object. The optional argument, $template
		 * may specify the template to be associated with this view.
		 * Please note that if you do not specify the template here,
		 * you have to do this manually later using Opt_View::setTemplate()
		 * method.
		 *
		 * @param string $template The template file.
		 */
		public function __construct($template = '')
		{
			$this->_tpl = Opl_Registry::get('opt');
			$this->_template = $template;
			$this->_mode = $this->_tpl->mode;
			$this->_cache = $this->_tpl->getCache();
		} // end __construct();

		/**
		 * Associates a template file to the view.
		 *
		 * @param string $file The template file.
		 */
		public function setTemplate($file)
		{
			$this->_template = $file;
		} // end setTemplate();

		/**
		 * Returns a template associated with this view.
		 *
		 * @return string The template filename.
		 */
		public function getTemplate()
		{
			return $this->_template;
		} // end getTemplate();

		/**
		 * Sets the template mode (XML, Quirks, etc...)
		 *
		 * @param Int $mode The new mode
		 */
		public function setMode($mode)
		{
			$this->_mode = $mode;
		} // end setMode();

		/**
		 * Gets the current template mode.
		 *
		 * @return Int
		 */
		public function getMode()
		{
			return $this->_mode;
		} // end getMode();

		/**
		 * Sets a template inheritance branch that will be used
		 * in this view. If you want to disable branching, set
		 * the argument to NULL.
		 *
		 * @param string $branch The branch name.
		 */
		public function setBranch($branch)
		{
			$this->_branch = $branch;
		} // end setBranch();

		/**
		 * Returns a branch used in the template inheritance.
		 *
		 * @return string The branch name.
		 */
		public function getBranch()
		{
			return $this->_branch;
		} // end getBranch();

		/**
		 * Returns the view processing time for the debug purposes.
		 * The processing time is calculated only if the debug mode
		 * is enabled.
		 *
		 * @return float The processing time.
		 */
		public function getTime()
		{
			return $this->_processingTime;
		} // end getTime();

		/*
		 * Data management
		 */

		/**
		 * Creates a new local template variable.
		 *
		 * @param string $name The variable name.
		 * @param mixed $value The variable value.
		 */
		public function __set($name, $value)
		{
			$this->_data[$name] = $value;
		} // end __set();

		/**
		 * Creates a new local template variable.
		 *
		 * @param string $name The variable name.
		 * @param mixed $value The variable value.
		 */
		public function assign($name, $value)
		{
			$this->_data[$name] = $value;
		} // end assign();

		/**
		 * Creates a group of local template variables
		 * using an associative array, where the keys are
		 * the variable names.
		 *
		 * @param array $vars A list of variables.
		 */
		public function assignGroup($values)
		{
			$this->_data = array_merge($this->_data, $values);
		} // end assignGroup();

		/**
		 * Creates a new local template variable with
		 * the value assigned by reference.
		 *
		 * @param string $name The variable name.
		 * @param mixed &$value The variable value.
		 */
		public function assignRef($name, &$value)
		{
			$this->_data[$name] = &$value;
		} // end assignRef();

		/**
		 * Returns the value of a template variable or
		 * null, if the variable does not exist.
		 *
		 * @param string $name The variable name.
		 * @return mixed The variable value or NULL.
		 */
		public function get($name)
		{
			if(!isset($this->_data[$name]))
			{
				return null;
			}
			return $this->_data[$name];
		} // end read();

		/**
		 * Returns the value of a local template variable or
		 * null, if the variable does not exist.
		 *
		 * @param string $name The variable name.
		 * @return mixed The variable value or NULL.
		 */
		public function &__get($name)
		{
			if(!isset($this->_data[$name]))
			{
				// For returning by reference...
				$empty = null;
				return $empty;
			}
			return $this->_data[$name];
		} // end __get();

		/**
		 * Returns TRUE, if the local template variable with the
		 * specified name is defined.
		 *
		 * @param string $name The variable name.
		 * @return boolean True, if the variable is defined.
		 */
		public function defined($name)
		{
			return isset($this->_data[$name]);
		} // end defined();

		/**
		 * Returns TRUE, if the local template variable with the
		 * specified name is defined.
		 *
		 * @param string $name The variable name.
		 * @return boolean True, if the variable is defined.
		 */
		public function __isset($name)
		{
			return isset($this->_data[$name]);
		} // end __isset();

		/**
		 * Removes a local template variable with the specified name.
		 *
		 * @param string $name The variable name.
		 * @return boolean True, if the variable has been removed.
		 */
		public function remove($name)
		{
			if(isset($this->_data[$name]))
			{
				unset($this->_data[$name]);
				if(isset($this->_formatInfo[$name]))
				{
					unset($this->_formatInfo[$name]);
				}
				return true;
			}
			return false;
		} // end remove();

		/**
		 * Removes a local template variable with the specified name.
		 *
		 * @param string $name The variable name.
		 * @return boolean True, if the variable has been removed.
		 */
		public function __unset($name)
		{
			return $this->remove($name);
		} // end __unset();

		/**
		 * Creates a new global template variable.
		 *
		 * @static
		 * @param string $name The variable name.
		 * @param mixed $value The variable value.
		 */
		static public function assignGlobal($name, $value)
		{
			self::$_global[$name] = $value;
		} // end assignGlobal();

		/**
		 * Creates a group of global template variables
		 * using an associative array, where the keys are
		 * the variable names.
		 *
		 * @static
		 * @param array $vars A list of variables.
		 */
		static public function assignGroupGlobal($values)
		{
			self::$_global = array_merge(self::$_global, $values);
		} // end assignGroupGlobal();

		/**
		 * Creates a new global template variable with
		 * the value assigned by reference.
		 *
		 * @static
		 * @param string $name The variable name.
		 * @param mixed &$value The variable value.
		 */
		static public function assignRefGlobal($name, &$value)
		{
			self::$_global[$name] = &$value;
		} // end assignRefGlobal();

		/**
		 * Returns TRUE, if the global template variable with the
		 * specified name is defined.
		 *
		 * @static
		 * @param string $name The variable name.
		 * @return boolean True, if the variable is defined.
		 */
		static public function definedGlobal($name)
		{
			return isset(self::$_global[$name]);
		} // end definedGlobal();

		/**
		 * Returns the value of a global template variable or
		 * null, if the variable does not exist.
		 *
		 * @static
		 * @param string $name The variable name.
		 * @return mixed The variable value or NULL.
		 */
		static public function getGlobal($name)
		{
			if(!isset(self::$_global[$name]))
			{
				return null;
			}
			return self::$_global[$name];
		} // end getGlobal();

		/**
		 * Removes a global template variable with the specified name.
		 *
		 * @static
		 * @param string $name The variable name.
		 * @return boolean True, if the variable has been removed.
		 */
		static public function removeGlobal($name)
		{
			if(isset(self::$_global[$name]))
			{
				unset(self::$_global[$name]);
				return true;
			}
			return false;
		} // end removeGlobal();

		/**
		 * Clears all the possible static private buffers.
		 */
		static public function clear()
		{
			self::$_vars = array();
			self::$_capture = array();
			self::$_global = array();
			self::$_globalFormatInfo = array();
		} // end clear();

		/**
		 * Returns the value of the internal template variable or
		 * NULL if it does not exist.
		 *
		 * @param string $name The internal variable name.
		 * @return mixed The variable value or NULL.
		 */
		public function getTemplateVar($name)
		{
			if(!isset(self::$_vars[$name]))
			{
				return null;
			}
			return self::$_vars[$name];
		} // end getTemplateVar();

		/**
		 * Sets the specified data format for the identifier that may
		 * identify a template variable or some other things. The details
		 * are explained in the OPT user manual.
		 *
		 * @param string $item The item name
		 * @param string $format The format to be used for the specified item.
		 */
		public function setFormat($item, $format)
		{
			$this->_formatInfo[$item] = $format;
		} // end setFormat();
		
		/**
		 * Sets the specified data format for the identifier that may
		 * identify a global template variable or some other things. The details
		 * are explained in the OPT user manual.
		 *
		 * @static
		 * @param string $item The item name
		 * @param string $format The format to be used for the specified item.
		 * @param boolean $global Does it register the item in the "global." group?
		 */
		static public function setFormatGlobal($item, $format, $global = true)
		{
			if($global)
			{
				self::$_globalFormatInfo['global.'.$item] = $format;
			}
			else
			{
				self::$_globalFormatInfo[$item] = $format;
			}
		} // end setFormatGlobal();

		/**
		 * Sets the caching interface that should be used with this view.
		 *
		 * @param Opt_Caching_Interface $iface The caching interface
		 */
		public function setCache(Opt_Caching_Interface $iface = null)
		{
			$this->_cache = $iface;
		} // end setCache();

		/**
		 * Returns the caching interface used with this view
		 *
		 * @return Opt_Caching_Interface
		 */
		public function getCache()
		{
			return $this->_cache;
		} // end getCache();

		/**
		 * A method for caching systems that tells, whether there is some
		 * dynamic content available in the captured part.
		 *
		 * @return Boolean
		 */
		public function hasDynamicContent()
		{
			return sizeof($this->_outputBuffer) > 0;
		} // end hasDynamicContent();

		/**
		 * Returns the static parts of the cached template, if the opt:dynamic
		 * is used. Please note that the returned array does not contain the
		 * last buffer, which must be closed and retrieved manually with
		 * ob_get_flush().
		 *
		 * @return Array
		 */
		public function getOutputBuffers()
		{
			return $this->_outputBuffer;
		} // end getBuffers();
		
		/*
		 * Dynamic inheritance
		 */

		/**
		 * Creates a dynamic template inheritance between the templates in the view.
		 * There are two possible uses of the method. If you specify only the one
		 * argument, the method will extend the main view template with the specified
		 * template.
		 *
		 * The two arguments can be used to extend other templates in the inheritance
		 * chain. In this case the first argument specifies the template that is going
		 * to extend something, and the second one - the extended template.
		 *
		 * @param string $source The extending template or the extended template in case of one-argument call.
		 * @param string $destination The extended template.
		 */
		public function inherit($source, $destination = null)
		{
			if($destination === null)
			{
				$this->_inheritance[$this->_template] = str_replace(array('/', ':', '\\'), '__', $source);
				$this->_cplInheritance[$this->_template] = $source;
				return;
			}
			$this->_inheritance[$source] = str_replace(array('/', ':', '\\'), '__',$destination);
			$this->_cplInheritance[$source] = $destination;
		} // end inherit();
		
		/*
		 * Internal use
		 */

		/**
		 * Executes, and optionally compiles the template represented by the view.
		 * Returns true, if the template was found and successfully executed.
		 *
		 * @param Opt_Output_Interface $output The output interface.
		 * @param Boolean $exception Should the exceptions be thrown if the template does not exist?
		 * @return Boolean
		 */
		public function _parse(Opt_Output_Interface $output, $exception = true)
		{
			if($this->_tpl->debugConsole)
			{
				$time = microtime(true);
			}
			$cached = false;
			if($this->_cache !== null)
			{
				$result = $this->_cache->templateCacheStart($this);
				if($result !== false)
				{
					// For dynamic cache...
					if(is_string($result))
					{
						include($result);
					}
					return true;
				}
				$cached = true;
			}
			$this->_tf = $this->_tpl->getTranslationInterface();
			if($this->_tpl->compileMode != Opt_Class::CM_PERFORMANCE)
			{
				list($compileName, $compileTime) = $this->_preprocess($exception);	
				if(is_null($compileName))
				{
					return false;
				}
			}
			else
			{
				$compileName = $this->_convert($this->_template);
				$compileTime = null;
				if(!$exception && !file_exists($compileName))
				{
					return false;
				}
			}
			
			$old = error_reporting($this->_tpl->errorReporting);
			require($this->_tpl->compileDir.$compileName);
			error_reporting($old);

			// The counter stops, if the time counting has been enabled for the debug console purposes
			if($this->_cache !== null)
			{
				$this->_cache->templateCacheStop($this);
			}
			if(isset($time))
			{
				Opt_Support::addView($this->_template, $output->getName(), $this->_processingTime = microtime(true) - $time, $cached);
			}
			return true;
		} // end _parse();

		/**
		 * The method checks whether the template exists and if it was modified by
		 * the template designer. In the second case, it loads and runs the template
		 * compiler to produce a new version. Returns an array with the template data:
		 *  - Compiled template name
		 *  - Compilation time
		 * They are needed by the template execution system or template inheritance. In
		 * case of problems, the array contains two NULL values.
		 *
		 * @internal
		 * @param Boolean $exception Do we inform about unexisting template with exceptions?
		 * @return Array
		 */
		protected function _preprocess($exception = true)
		{
			$item = $this->_tpl->_stream($this->_template);
			$compiled = $this->_convert($this->_template);
			$compileTime = @filemtime($this->_tpl->compileDir.$compiled);
			$result = NULL;
			
			// Here the "rebuild" compilation mode is processed
			if($this->_tpl->compileMode == Opt_Class::CM_REBUILD)
			{
				if(!file_exists($item))
				{
					if(!$exception)
					{
						return array(NULL, NULL);
					}
					throw new Opt_TemplateNotFound_Exception($item);
				}
				$result = file_get_contents($item);
			}
			else
			{				
				// Otherwise, we perform a modification test.
				$rootTime = @filemtime($item);
				if($rootTime === false)
				{
					if(!$exception)
					{
						return array(NULL, NULL);
					}
					throw new Opt_TemplateNotFound_Exception($item);
				}
				if($compileTime === false || $compileTime < $rootTime)
				{
					$result = file_get_contents($item);
				}
			}

			if($result === null)
			{
				return array($compiled, $compileTime);
			}

			$compiler = $this->_tpl->getCompiler();
			$compiler->setInheritance($this->_cplInheritance);
			$compiler->setFormatList(array_merge($this->_formatInfo, self::$_globalFormatInfo));
			$compiler->set('branch', $this->_branch);
			$compiler->compile($result, $this->_template, $compiled, $this->_mode);
			return array($compiled, $compileTime);
		} // end _preprocess();

		/**
		 * This method is used by the template with the template inheritance. It
		 * allows to check, whether one of the templates on the dependency list
		 * has been modified. The method takes the compilation time of the compiled
		 * template and the list of the source template names that it depends on.
		 *
		 * Returns true, if one if the templates is newer than the compilation time.
		 *
		 * @param Int $compileTime Compiled template creation time.
		 * @param Array $templates The list of dependencies
		 * @return Boolean
		 */
		protected function _massPreprocess($compileTime, $templates)
		{
			// We do not check CM_REBUILD, because the compilation has already been done in _parse()
			if($this->_tpl->compileMode == Opt_Class::CM_DEFAULT)
			{
				$cnt = sizeof($templates);
					
				// TODO: Check whether the object as array key works :P
				for($i = 0; $i < $cnt; $i++)
				{
					$templates[$i] = $this->_tpl->_stream($templates[$i]);
					$time = @filemtime($templates[$i]);
					if($time === null)
					{
						throw new Opt_TemplateNotFound_Exception($templates[$i]);
					}
					if($time >= $compileTime)
					{
						return true;
					}
				}
			}
			return false;
		} // end _massPreprocess();

		/**
		 * Converts the source template file name to the compiled
		 * template file name.
		 *
		 * @internal
		 * @param String $filename The source file name
		 * @return String
		 */
		public function _convert($filename)
		{
			$list = array();
			if(sizeof($this->_inheritance) > 0)
			{
				$list = $this->_inheritance;
				sort($list);
			}
			$list[] = str_replace(array('/', ':', '\\'), '__', $filename);
			if($this->_tpl->compileId !== null)
			{
				return $this->_tpl->compileId.'_'.implode('/', $list).'.php';
			}
			return implode('/', $list).'.php';
		} // end _convert();

		/**
		 * Compiles the specified template and returns the current
		 * time.
		 *
		 * @internal
		 * @param String $filename The file name.
		 * @return Integer
		 */
		public function _compile($filename)
		{
			$compiled = $this->_convert($filename);
			$compiler = $this->_tpl->getCompiler();
			$compiler->setInheritance($this->_cplInheritance);
			$compiler->setFormatList(array_merge($this->_formatInfo, self::$_globalFormatInfo));
			$compiler->set('branch', $this->_branch);
			$compiler->compile($this->_tpl->_getSource($filename), $filename, $compiled, $this->_mode);
			return time();
		} // end _compile();
	} // end Opt_View;
