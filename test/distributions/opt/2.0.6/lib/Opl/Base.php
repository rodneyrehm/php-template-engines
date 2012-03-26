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
	 * Interface definitions
	 */

	interface Opl_Translation_Interface
	{
		public function _($group, $id);
		public function assign($group, $id);
	} // end Opl_Translation_Interface;

	/*
	 * Class definitions
	 */

	/**
	 * The generic class autoloader.
	 */
	class Opl_Loader
	{	
		/**
		 * The default library handler
		 * @var Callback
		 */
		static private $_handler = array('Opl_Loader', 'oplHandler');

		/**
		 * The main directory used by autoloader
		 * @var String
		 */
		static private $_directory = '';
		/**
		 * The library-specific configuration.
		 * @var Array
		 */
		static private $_libraries = array();

		/**
		 * The list of manually mapped files.
		 * @var Array
		 */
		static private $_mappedFiles = array();

		/**
		 * The list of initialized OPL libraries.
		 * @var Array
		 */
		static private $_initialized = array();

		/**
		 * If OPL has been loaded?
		 * @var Boolean
		 */
		static private $_loaded = false;

		/**
		 * Checking if the file exists status.
		 * @var Boolean
		 */
		static private $_fileCheck = false;

		/**
		 * Checking if loader should handle not known libraries.
		 * @var Boolean
		 */
		static private $_handleUnknownLibraries = true;

		/**
		 * Specifies a directory path to the OPL libraries.
		 *
		 * @param string $name The directory name where the OPL libraries are kept.
		 */
		static public function setDirectory($name)
		{
			if($name != '')
			{
				if($name[strlen($name)-1] != DIRECTORY_SEPARATOR)
				{
					$name .= DIRECTORY_SEPARATOR;
				}
			}
			// Prevention against current directory changes in Apache
			// which affects destructors. We avoid it by switching to the
			// absolute path.
			
			if(isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false)
			{
				$name = realpath($name).DIRECTORY_SEPARATOR;
			}
			self::$_directory = $name;
		} // end setDirectory();

		/**
		 * Sets the new default handler that will capture all the file calls to
		 * support library-specific settings. The 'null' value removes the current
		 * handler.
		 *
		 * @param Callback|Null $handler The new library handler.
		 */
		static public function setDefaultHandler($handler)
		{
			if(is_null($handler))
			{
				self::$_handler = null;
				return;
			}
			if(!is_callable($handler, true))
			{
				throw new Opl_InvalidCallback_Exception();
			}
			self::$_handler = $handler;
		} // end setDefaultHandler();

		/**
		 * Sets state for handling unknown, not registered by addLibrary libraries.
		 *
		 * @param Boolean $state State for handling unknown libraries
		 */
		static public function setHandleUnknownLibraries($state)
		{
			self::$_handleUnknownLibraries = (boolean)$state;
		} // end setHandleUnknownLibraries();

		/**
		 * Registers the autoloader.
		 */
		static public function register()
		{
			spl_autoload_register(array('Opl_Loader', 'autoload'));
		} // end register();

		/**
		 * Returns the OPL libraries directory.
		 *
		 * @return string The OPL libraries directory.
		 */
		static public function getDirectory()
		{
			return self::$_directory;
		} // end getDirectory();

		/**
		 * Allows to enable or disable the file existence checking by
		 * the autoloader. Note that for the performance reasons, the
		 * checking should be disabled in the production environment.
		 *
		 * @param Boolean $status The new status
		 */
		static public function setCheckFileExists($status)
		{
			self::$_fileCheck = (bool)$status;
		} // end setCheckFileExists();

		/**
		 * Allows to load the path list for the libraries either from an
		 * array or from an INI file.
		 *
		 * @param string|array $config The path list to the OPL libraries.
		 */
		static public function loadPaths($config)
		{
			if(!is_array($config))
			{
				if(!file_exists($config))
				{
					throw new Opl_FileNotExists_Exception('file', $config);
				}
				$config = parse_ini_file($config, true);
			}

			if(isset($config['directory']))
			{
				self::setDirectory($config['directory']);
			}
			if(isset($config['libraries']) && is_array($config['libraries']))
			{
				foreach($config['libraries'] as $lib => $path)
				{
					self::mapLibrary($lib, $path);
				}
			}
			if(isset($config['classes']) && is_array($config['classes']))
			{
				foreach($config['classes'] as $class => $path)
				{
					if(strpos($path, ':') !== false)
					{
						$data = explode(':', $path);
						self::map($class, $data[1], $data[0]);
					}
					else
					{
						self::map($class, $path);
					}
				}
			}
		} // end loadPaths();

		/**
		 * Configures the autoloader settings for the specific library.
		 *
		 * @param String $prefix The library prefix used by the classes.
		 * @param Array $config The library configuration
		 */
		static public function addLibrary($prefix, Array $config)
		{
			if(isset($config['directory']))
			{
				if($config['directory'] != '')
				{
					if($config['directory'][strlen($config['directory'])-1] != DIRECTORY_SEPARATOR)
					{
						$config['directory'] .= DIRECTORY_SEPARATOR;
					}
				}
			}
			if(isset($config['basePath']))
			{
				if($config['basePath'] != '')
				{
					if($config['basePath'][strlen($config['basePath'])-1] != DIRECTORY_SEPARATOR)
					{
						$config['basePath'] .= DIRECTORY_SEPARATOR;
					}
				}
			}
			self::$_libraries[$prefix] = $config;
		} // end addLibrary();

		/**
		 * Removes the library-specific settings for the library.
		 *
		 * @param String $prefix The library prefix used by the classes
		 */
		static public function removeLibrary($prefix)
		{
			if(isset(self::$_libraries[$prefix]))
			{
				unset(self::$_libraries[$prefix]);
			}
		} // end removeLibrary();

		/**
		 * Allows to specify a directory for a single OPL library.
		 *
		 * @param string $libraryName The three-letter library code.
		 * @param string $directory The directory, where the library is located.
		 */
		static public function mapLibrary($libraryName, $directory)
		{
			self::addLibrary($libraryName, array('directory' => $directory));
		} // end mapLibrary();

		/**
		 * Allows to specify a path for one of the classes manually.
		 * However, the path must be located within the class library
		 * directory.
		 *
		 * @param string $className The class name
		 * @param string $directory The directory, where the library is located.
		 * @param string|null $library If not specified, the library name is taken from the class name.
		 */
		static public function map($className, $file, $library = NULL)
		{
			if(is_null($library))
			{
				// Determine the library name according to the class name.
				$id = strpos($className, '_');
				if($id === false)
				{
					throw new Opl_InvalidClass_Exception($className);
				}
				$library = substr($className, 0, $id);
			}

			self::$_mappedFiles[$className] = self::getLibraryPath($library).$file;
		} // end map();

		/**
		 * Allows to specify an absolute path to the class.
		 *
		 * @param string $className The class name.
		 * @param string $file The absolute path to the class file.
		 */
		static public function mapAbsolute($className, $file)
		{
			self::$_mappedFiles[$className] = $file;
		} // end mapAbsolute();

		/**
		 * Loads the class.
		 *
		 * @param string $name The class name.
		 * @return boolean True, if the class was successfully loaded.
		 */
		static public function load($name)
		{
			return self::autoload($name);
		} // end load();

		/**
		 * An autoloader method.
		 *
		 * @param string $className The class name.
		 * @return boolean True, if the class was successfully loaded.
		 */
		static public function autoload($className)
		{
			// Manually mapped files support
			if(isset(self::$_mappedFiles[$className]))
			{
				require(self::$_mappedFiles[$className]);
				return true;
			}

			$id = strpos($className, '_');
			$wholeName = false;
			// Handle the situation if there is no "_" in the class name
			if($id === false)
			{
				$id = strlen($className);
				$wholeName = true;
			}
			$library = substr($className, 0, $id);

			// Check if autoloader have to handle not registered libraries
			if(!self::$_handleUnknownLibraries)
			{
				if(!in_array($library, array_keys(self::$_libraries)))
				{
					return false;
				}
			}

			// If the handler is configured, allow the handler to do something.
			$handler = self::_getLibraryHandler($library);
			$ok = true;
			if($handler !== null)
			{
				$ok = call_user_func($handler, $library, $className);
			}

			// Load the file.
			if($ok)
			{
				if($wholeName)
				{
					$path = '..'.DIRECTORY_SEPARATOR.$className;
				}
				else
				{
					$path = str_replace('_', DIRECTORY_SEPARATOR, substr($className, $id+1, strlen($className) - $id - 1));
				}
				$file = self::getLibraryPath($library).$path.'.php';
				if(self::$_fileCheck == true && !file_exists($file))
				{
						return true;
				}

				require($file);
				return true;
			}
			return false;
		} // end autoload();

		/**
		 * The OPL libraries handler that provides the OPL-specific autoloading
		 * issues.
		 *
		 * @param Array $item The requested item.
		 * @param Boolean
		 */
		static public function oplHandler($library, $className)
		{
			// Backward compatibility to PHP 5.2
			// This allows to load the compatibility classes even if some parts of OPL are
			// loaded by different autoloader.
			if(!self::$_loaded)
			{
				if(version_compare(phpversion(), '5.3.0-dev', '<'))
				{
					require(self::getLibraryPath('Opl').'Php52.php');
				}
				self::$_loaded = true;
				if(class_exists($className, false) || interface_exists($className, false))
				{
					return false;
				}
			}
			if($className == $library.'_Class')
			{
				self::$_initialized[$library] = true;
				return true;
			}
			$base = self::getLibraryPath($library);
			// Load the base library file, if not loaded yet.
			if(!isset(self::$_initialized[$library]))
			{
				if($library != 'Opc' && $library != 'Opl')
				{
					require($base.'Class.php');
				}
				self::$_initialized[$library] = true;
				if(class_exists($className, false) || interface_exists($className, false))
				{
					return false;
				}
			}

			// This is the exception.
			$id = strrpos($className, '_');
			if(substr($className, $id+1, 9) == 'Exception')
			{
				require($base.'Exception.php');
				return false;
			}
			// Everything is done.
			return true;
		} // end oplHandler();

		/**
		 * This method handles PHAR-s. Their initialization procedure is a bit
		 * different, so we do not need so much code.
		 *
		 * @param String $library The library name
		 * @param String $className The class name
		 * @return Boolean
		 */
		static public function pharHandler($library, $className)
		{
			// This is the exception.
			$id = strrpos($className, '_');
			if(substr($className, $id+1, 9) == 'Exception')
			{
				require($base.'Exception.php');
				return false;
			}
			// Everything is done.
			return true;
		} // end pharHandler();

		/**
		 * Returns the path for the specified library.
		 *
		 * @param String $library The library
		 * @return String
		 */
		static public function getLibraryPath($library)
		{
			if(isset(self::$_libraries[$library]))
			{
				if(isset(self::$_libraries[$library]['directory']))
				{
					return self::$_libraries[$library]['directory'];
				}
				if(isset(self::$_libraries[$library]['basePath']))
				{
					return self::$_libraries[$library]['basePath'].$library.DIRECTORY_SEPARATOR;
				}
			}
			return self::$_directory.$library.DIRECTORY_SEPARATOR;
		} // end getLibraryPath();

		/**
		 * Returns the handler for the specified library.
		 *
		 * @internal
		 * @param String $library The library
		 * @return Callback
		 */
		static protected function _getLibraryHandler($library)
		{
			if(isset(self::$_libraries[$library]))
			{
				if(array_key_exists('handler', self::$_libraries[$library]))
				{
					return self::$_libraries[$library]['handler'];
				}
			}
			return self::$_handler;
		} // end _getLibraryPath();
	} // end Opl_Loader;
	
	class Opl_Registry
	{
		static private $_objects = array();
		static private $_states = array();		

		/**
		 * Registers a new object in the registry.
		 *
		 * @param String $name The object key
		 * @param Object $object The registered object
		 */
		static public function register($name, $object)
		{
			self::$_objects[$name] = $object;
		} // end register();

		/**
		 * Returns the previously registered object. If the object does not
		 * exist, it throws an exception.
		 *
		 * @param String $name The registered object key.
		 * @return Object
		 */
		static public function get($name)
		{
			if(!isset(self::$_objects[$name]))
			{
				throw new Opl_Debug_ItemNotExists_Exception('object', $name);
			}
			return self::$_objects[$name];
		} // end get();

		/**
		 * Check whether there is an object registered under a specified key.
		 *
		 * @param String $name The object key
		 * @return Boolean
		 */
		static public function exists($name)
		{
			return !empty(self::$_objects[$name]);
		} // end exists();

		/**
		 * Sets the state variable in the registry
		 *
		 * @param String $name The variable name
		 * @param Mixed $value The variable value
		 */
		static public function setState($name, $value)
		{
			self::$_states[$name] = $value;
		} // end setState();

		/**
		 * Returns the state variable from the registry. If the
		 * variable does not exist, it returns NULL.
		 *
		 * @param String $name The variable name
		 * @return Mixed
		 */
		static public function getState($name)
		{
			if(!isset(self::$_states[$name]))
			{
				return NULL;
			}
			return self::$_states[$name];
		} // end getState();
	} // end Opl_Registry;

	class Opl_Class
	{
		// Plugin support
		public $pluginDir = NULL;
		public $pluginDataDir = NULL;
		public $pluginAutoload = true;
	
		// The rest of the configuration
		protected $_config = array();

		/**
		 * Returns the specified configuration property value.
		 *
		 * @param String $name The property name
		 * @return Mixed
		 */
		public function __get($name)
		{
			if($name[0] == '_')
			{
				return NULL;
			}
			if(!isset($this->_config[$name]))
			{
				throw new Opl_OptionNotExists_Exception($name, get_class($this));
			}
			return $this->_config[$name];
		} // __get();

		/**
		 * Sets the custom configuration property value.
		 *
		 * @param String $name The property name
		 * @param Mixed $value The property value
		 * @return Mixed
		 */
		public function __set($name, $value)
		{
			if($name[0] == '_')
			{
				return NULL;
			}
			
			$this->_config[$name] = $value;
		} // end __set();

		/**
		 * Loads the configuration from external array or INI file.
		 *
		 * @param String|Array $config The configuration option values or the INI filename.
		 * @return Boolean
		 */
		public function loadConfig($config)
		{
			if(is_string($config))
			{
				$config = @parse_ini_file($config);
			}
			
			if(!is_array($config))
			{
				return false;
			}
			foreach($config as $name => $value)
			{
				if($name[0] == '_')
				{
					continue;
				}
				if(property_exists($this, $name))
				{
					$this->$name = $value;
				}
				else
				{
					$this->_config[$name] = $value;
				}
			}
			return true;
		} // end loadConfig();

		/**
		 * Returns the configuration as an array.
		 *
		 * @return Array
		 */
		public function getConfig()
		{
			$vars = $this->_config;
			$internal = get_object_vars($this);
			foreach($internal as $id=>$var)
			{
				if($id[0] != '_')
				{
					$vars[$id] = $var;
				}
			}
			return $vars;
		} // end getConfig();

		/**
		 * Loads the plugins from the directories specified in the class configuration.
		 */
		public function loadPlugins()
		{
			if(is_string($this->pluginDir))
			{
				$dirs[] = &$this->pluginDir;
			}
			elseif(is_array($this->pluginDir))
			{
				$dirs = &$this->pluginDir;
			}
			else
			{
				throw new Opl_InvalidType_Exception(get_class($this).'::pluginDir', 'string or array');
			}
			
			$dataFile = $this->pluginDataDir.get_class($this).'_Plugins.php';
			$cplTime = @filemtime($dataFile);
			$rebuild = false;
			if($this->pluginAutoload)
			{
				
				if($cplTime !== false)
				{
					// The plugin data file exists, but we have to check
					// whether there are some new plugins or not.
					$mode = 0;
					foreach($dirs as &$dir)
					{
						if($mode == 0)
						{
							$dirTime = @filemtime($dir);
							if($dirTime === false)
							{
								throw new Opl_FileNotExists_Exception('directory', $dir);
							}
							
							// Some new plugins have been added to this directory
							if($dirTime > $cplTime)
							{
								$rebuild = true;
								$mode = 1;
							}
						}
						// Now, we know that one of the dirs has a new plugin
						// We just have to check if all the directories exist
						elseif(!is_dir($dir))
						{
							throw new Opl_FileNotExists_Exception('directory', $dir);
						}
					}
				}
			}
			if($cplTime === false)
			{
				// No plugin data file, 
				foreach($dirs as &$dir)
				{
					if(!is_dir($dir))
					{
						throw new Opl_FileNotExists_Exception('directory', $dir);
					}
				}
				$rebuild = true;
			}
			// We have to rebuild the file
			if($rebuild)
			{
				$src = '<'.'?php ';
				foreach($dirs as &$dir)
				{
					$this->_securePath($dir);
					foreach(new DirectoryIterator($dir) as $file)
					{
						if($file->isFile())
						{
							$src .= $this->_pluginLoader($dir, $file);
						}
					}
				}
				if(is_writeable($this->pluginDataDir))
				{
					file_put_contents($dataFile, $src);
				}
				else
				{
					throw new Opl_NotWriteable_Exception('directory', $this->pluginDataDir);
				}
			}
			
			require($dataFile);
		} // end loadPlugins();

		/**
		 * The method allows to define the specific plugin loading settings for the
		 * library. Because the results are cached in order not to exhaust the server
		 * resources, the method must return a PHP code that loads the specified plugin.
		 *
		 * @internal
		 * @param String $directory The plugin location
		 * @param SplFileInfo $file The plugin file information
		 * @return String
		 */
		protected function _pluginLoader($directory, SplFileInfo $file)
		{
			return '';
		} // end _pluginLoader();

		/**
		 * The method allows to secure the path by adding an ending slash, if
		 * it is not specified.
		 *
		 * @internal
		 * @param String &$path The path to secure.
		 */
		public function _securePath(&$path)
		{
			if($path[strlen($path)-1] != '/')
			{
				$path .= '/';
			}
		} // end _securePath();
	} // end Opl_Class;
	
	class Opl_Goto_Exception extends Exception
	{
	} // end Opl_Goto_Exception;
