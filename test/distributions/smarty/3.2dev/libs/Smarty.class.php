<?php

/**
 * Project:     Smarty: the PHP compiling template engine
 * File:        Smarty.class.php
 * SVN:         $Id: Smarty.class.php 4572 2012-03-04 13:36:15Z uwe.tews@googlemail.com $
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * For questions, help, comments, discussion, etc., please join the
 * Smarty mailing list. Send a blank e-mail to
 * smarty-discussion-subscribe@googlegroups.com
 *
 * @link http://www.smarty.net/
 * @copyright 2008 New Digital Group, Inc.
 * @author Monte Ohrt <monte at ohrt dot com>
 * @author Uwe Tews
 * @author Rodney Rehm
 * @package Smarty
 * @version 3.2-DEV
 */
/**
 * define shorthand directory separator constant
 */
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * set SMARTY_DIR to absolute path to Smarty library files.
 * Sets SMARTY_DIR only if user application has not already defined it.
 */
if (!defined('SMARTY_DIR')) {
    define('SMARTY_DIR', dirname(__FILE__) . DS);
}

/**
 * set SMARTY_SYSPLUGINS_DIR to absolute path to Smarty internal plugins.
 * Sets SMARTY_SYSPLUGINS_DIR only if user application has not already defined it.
 */
if (!defined('SMARTY_SYSPLUGINS_DIR')) {
    define('SMARTY_SYSPLUGINS_DIR', SMARTY_DIR . 'sysplugins' . DS);
}
if (!defined('SMARTY_PLUGINS_DIR')) {
    define('SMARTY_PLUGINS_DIR', SMARTY_DIR . 'plugins' . DS);
}
if (!defined('SMARTY_MBSTRING')) {
    define('SMARTY_MBSTRING', function_exists('mb_strlen'));
}
if (!defined('SMARTY_RESOURCE_CHAR_SET')) {
    // UTF-8 can only be done properly when mbstring is available!
    /**
     * @deprecated in favor of Smarty::$_CHARSET
     */
    define('SMARTY_RESOURCE_CHAR_SET', SMARTY_MBSTRING ? 'UTF-8' : 'ISO-8859-1');
}
if (!defined('SMARTY_RESOURCE_DATE_FORMAT')) {
    /**
     * @deprecated in favor of Smarty::$_DATE_FORMAT
     */
    define('SMARTY_RESOURCE_DATE_FORMAT', '%b %e, %Y');
}

/**
 * register the class autoloader
 */
if (!defined('SMARTY_SPL_AUTOLOAD')) {
    define('SMARTY_SPL_AUTOLOAD', 0);
}

if (SMARTY_SPL_AUTOLOAD && set_include_path(get_include_path() . PATH_SEPARATOR . SMARTY_SYSPLUGINS_DIR) !== false) {
    $registeredAutoLoadFunctions = spl_autoload_functions();
    if (!isset($registeredAutoLoadFunctions['spl_autoload'])) {
        spl_autoload_register();
    }
} else {
    spl_autoload_register('smartyAutoload');
}

/**
 * Load always needed external class files
 */
include_once SMARTY_SYSPLUGINS_DIR . 'smarty_internal_data.php';
include_once SMARTY_SYSPLUGINS_DIR . 'smarty_internal_templatebase.php';
include_once SMARTY_SYSPLUGINS_DIR . 'smarty_internal_template.php';
include_once SMARTY_SYSPLUGINS_DIR . 'smarty_resource.php';
include_once SMARTY_SYSPLUGINS_DIR . 'smarty_internal_resource_file.php';

/**
 * This is the main Smarty class
 * @package Smarty
 */
class Smarty extends Smarty_Internal_TemplateBase {
    /*     * #@+
     * constant definitions
     */

    /**
     * smarty version
     */
    const SMARTY_VERSION = 'Smarty 3.2-DEV';

    /**
     * define variable scopes
     */
    const SCOPE_LOCAL = 0;
    const SCOPE_PARENT = 1;
    const SCOPE_ROOT = 2;
    const SCOPE_GLOBAL = 3;
    /**
     * define caching modes
     */
    const CACHING_OFF = 0;
    const CACHING_LIFETIME_CURRENT = 1;
    const CACHING_LIFETIME_SAVED = 2;
    /**
     * define compile check modes
     */
    const COMPILECHECK_OFF = 0;
    const COMPILECHECK_ON = 1;
    const COMPILECHECK_CACHEMISS = 2;
    /**
     * modes for handling of "<?php ... ?>" tags in templates.
     */
    const PHP_PASSTHRU = 0; //-> print tags as plain text
    const PHP_QUOTE = 1; //-> escape tags as entities
    const PHP_REMOVE = 2; //-> escape tags as entities
    const PHP_ALLOW = 3; //-> escape tags as entities
    /**
     * filter types
     */
    const FILTER_POST = 'post';
    const FILTER_PRE = 'pre';
    const FILTER_OUTPUT = 'output';
    const FILTER_VARIABLE = 'variable';
    /**
     * plugin types
     */
    const PLUGIN_FUNCTION = 'function';
    const PLUGIN_BLOCK = 'block';
    const PLUGIN_COMPILER = 'compiler';
    const PLUGIN_MODIFIER = 'modifier';
    const PLUGIN_MODIFIERCOMPILER = 'modifiercompiler';
    /**
     * unassigend template variable handling
     */
    const UNASSIGNED_IGNORE = 0;
    const UNASSIGNED_NOTICE = 1;
    const UNASSIGNED_EXCEPTION = 2;

    /*     * #@- */

    /**
     * assigned global tpl vars
     * @internal
     */
    public static $global_tpl_vars = null;

    /**
     * error handler returned by set_error_hanlder() in Smarty::muteExpectedErrors()
     * @internal
     */
    public static $_previous_error_handler = null;

    /**
     * contains directories outside of SMARTY_DIR that are to be muted by muteExpectedErrors()
     * @internal
     */
    public static $_muted_directories = array();

    /**
     * Flag denoting if Multibyte String functions are available
     */
    public static $_MBSTRING = SMARTY_MBSTRING;

    /**
     * The character set to adhere to (e.g. "UTF-8")
     */
    public static $_CHARSET = SMARTY_RESOURCE_CHAR_SET;

    /**
     * The date format to be used internally
     * (accepts date() and strftime())
     */
    public static $_DATE_FORMAT = SMARTY_RESOURCE_DATE_FORMAT;

    /**
     * Flag denoting if PCRE should run in UTF-8 mode
     */
    public static $_UTF8_MODIFIER = 'u';

    /*     * #@+
     * variables
     */

    /**
     * auto literal on delimiters with whitspace
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.auto.literal.tpl
     */
    public $auto_literal = true;

    /**
     * display error on not assigned variables
     * @var integer
     * @link <missing>
     * @uses UNASSIGNED_IGNORE as possible value
     * @uses UNASSIGNED_NOTICE as possible value
     * @uses UNASSIGNED_EXCEPTION as possible value
     */
    public $error_unassigned = self::UNASSIGNED_IGNORE;

    /**
     * look up relative filepaths in include_path
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.use.include.path.tpl
     */
    public $use_include_path = false;

    /**
     * enable source code tracback for runtime exceptions
     * @var boolean
     */
    public $enable_traceback = true;

    /**
     * template directory
     * @var array
     * @link http://www.smarty.net/docs/en/variable.template.dir.tpl
     */
    private $template_dir = array();

    /**
     * joined template directory string used in cache keys
     * @var string
     * @internal
     */
    public $joined_template_dir = null;

    /**
     * joined config directory string used in cache keys
     * @var string
     * @internal
     */
    public $joined_config_dir = null;

    /**
     * default template handler
     * @var callable
     * @link http://www.smarty.net/docs/en/variable.default.template.handler.func.tpl
     */
    public $default_template_handler_func = null;

    /**
     * default config handler
     * @var callable
     * @link http://www.smarty.net/docs/en/variable.default.config.handler.func.tpl
     */
    public $default_config_handler_func = null;

    /**
     * default plugin handler
     * @var callable
     * @link <missing>
     */
    public $default_plugin_handler_func = null;

    /**
     * default variable handler
     * @var callable
     * @link <missing>
     */
    public $default_variable_handler_func = null;

    /**
     * default config variable handler
     * @var callable
     * @link <missing>
     */
    public $default_config_variable_handler_func = null;

    /**
     * compile directory
     * @var string
     * @link http://www.smarty.net/docs/en/variable.compile.dir.tpl
     */
    private $compile_dir = null;

    /**
     * plugins directory
     * @var array
     * @link http://www.smarty.net/docs/en/variable.plugins.dir.tpl
     */
    private $plugins_dir = array();

    /**
     * cache directory
     * @var string
     * @link http://www.smarty.net/docs/en/variable.cache.dir.tpl
     */
    private $cache_dir = null;

    /**
     * config directory
     * @var array
     * @link http://www.smarty.net/docs/en/variable.fooobar.tpl
     */
    private $config_dir = array();

    /**
     * disable core plugins in {@link loadPlugin()}
     * @var boolean
     * @link <missing>
     */
    public $disable_core_plugins = false;

    /**
     * force template compiling?
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.force.compile.tpl
     */
    public $force_compile = false;

    /**
     * check template for modifications?
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.compile.check.tpl
     * @uses COMPILECHECK_OFF as possible value
     * @uses COMPILECHECK_ON as possible value
     * @uses COMPILECHECK_CACHEMISS as possible value
     */
    public $compile_check = true;

    /**
     * use sub dirs for compiled/cached files?
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.use.sub.dirs.tpl
     */
    public $use_sub_dirs = false;

    /**
     * allow ambiguous resources (that are made unique by the resource handler)
     * @var boolean
     */
    public $allow_ambiguous_resources = false;
    /*
     * caching enabled
     * @var integer
     * @link http://www.smarty.net/docs/en/variable.caching.tpl
     * @uses CACHING_OFF as possible value
     * @uses CACHING_LIFETIME_CURRENT as possible value
     * @uses CACHING_LIFETIME_SAVED as possible value
     */
    public $caching = false;

    /**
     * merge compiled includes
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.merge.compiled.includes.tpl
     */
    public $merge_compiled_includes = true;

    /**
     * cache lifetime in seconds
     * @var integer
     * @link http://www.smarty.net/docs/en/variable.cache.lifetime.tpl
     */
    public $cache_lifetime = 3600;

    /**
     * force cache file creation
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.force.cache.tpl
     */
    public $force_cache = false;

    /**
     * Set this if you want different sets of cache files for the same
     * templates.
     * @var string
     * @link http://www.smarty.net/docs/en/variable.cache.id.tpl
     */
    public $cache_id = null;

    /**
     * Set this if you want different sets of compiled files for the same
     * templates.
     * @var string
     * @link http://www.smarty.net/docs/en/variable.compile.id.tpl
     */
    public $compile_id = null;

    /**
     * template left-delimiter
     * @var string
     * @link http://www.smarty.net/docs/en/variable.left.delimiter.tpl
     */
    public $left_delimiter = "{";

    /**
     * template right-delimiter
     * @var string
     * @link http://www.smarty.net/docs/en/variable.right.delimiter.tpl
     */
    public $right_delimiter = "}";
    /*     * #@+
     * security
     */

    /**
     * class name
     *
     * This should be instance of Smarty_Security.
     * @var string
     * @see Smarty_Security
     * @link <missing>
     */
    public $security_class = 'Smarty_Security';

    /**
     * implementation of security class
     * @var Smarty_Security
     * @see Smarty_Security
     * @link <missing>
     */
    public $security_policy = null;

    /**
     * controls handling of PHP-blocks
     * @var integer
     * @link http://www.smarty.net/docs/en/variable.php.handling.tpl
     * @uses PHP_PASSTHRU as possible value
     * @uses PHP_QUOTE as possible value
     * @uses PHP_REMOVE as possible value
     * @uses PHP_ALLOW as possible value
     */
    public $php_handling = self::PHP_PASSTHRU;

    /**
     * controls if the php template file resource is allowed
     * @var boolean
     * @link http://www.smarty.net/docs/en/api.variables.tpl#variable.allow.php.templates
     */
    public $allow_php_templates = false;

    /**
     * Should compiled-templates be prevented from being called directly?
     *
     * {@internal
     * Currently used by Smarty_Internal_Template only.
     * }}
     *
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.direct.access.security.tpl
     */
    public $direct_access_security = true;
    /*     * #@- */

    /**
     * debug mode
     *
     * Setting this to true enables the debug-console.
     *
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.debugging.tpl
     */
    public $debugging = false;

    /**
     * This determines if debugging is enable-able from the browser.
     * <ul>
     *  <li>NONE => no debugging control allowed</li>
     *  <li>URL => enable debugging when SMARTY_DEBUG is found in the URL.</li>
     * </ul>
     * @var string
     * @link http://www.smarty.net/docs/en/variable.debugging.ctrl.tpl
     */
    public $debugging_ctrl = 'NONE';

    /**
     * Name of debugging URL-param.
     * Only used when $debugging_ctrl is set to 'URL'.
     * The name of the URL-parameter that activates debugging.
     * @var string
     * @link http://www.smarty.net/docs/en/variable.smarty.debug.id.tpl
     */
    public $smarty_debug_id = 'SMARTY_DEBUG';

    /**
     * Path of debug template.
     * @var string
     * @link http://www.smarty.net/docs/en/variable.debug_template.tpl
     */
    public $debug_tpl = null;

    /**
     * When set, smarty uses this value as error_reporting-level.
     * @var integer
     * @link http://www.smarty.net/docs/en/variable.error.reporting.tpl
     */
    public $error_reporting = null;

    /**
     * Internal flag for getTags()
     * @var boolean
     * @internal
     */
    public $get_used_tags = false;

    /*     * #@+
     * config var settings
     */

    /**
     * Controls whether variables with the same name overwrite each other.
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.config.overwrite.tpl
     */
    public $config_overwrite = true;

    /**
     * Controls whether config values of on/true/yes and off/false/no get converted to boolean.
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.config.booleanize.tpl
     */
    public $config_booleanize = true;

    /**
     * Controls whether hidden config sections/vars are read from the file.
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.config.read.hidden.tpl
     */
    public $config_read_hidden = false;

    /*     * #@- */

    /*     * #@+
     * resource locking
     */

    /**
     * locking concurrent compiles
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.compile.locking.tpl
     */
    public $compile_locking = true;

    /**
     * Controls whether cache resources should emply locking mechanism
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.cache.locking.tpl
     */
    public $cache_locking = false;

    /**
     * seconds to wait for acquiring a lock before ignoring the write lock
     * @var float
     * @link http://www.smarty.net/docs/en/variable.locking.timeout.tpl
     */
    public $locking_timeout = 10;

    /*     * #@- */

    /**
     * global template functions
     * @var array
     * @internal
     */
    public $template_functions = array();

    /**
     * resource type used if none given
     * Must be an valid key of $registered_resources.
     * @var string
     * @link http://www.smarty.net/docs/en/variable.default.resource.type.tpl
     */
    public $default_resource_type = 'file';

    /**
     * caching type
     * Must be an element of $cache_resource_types.
     * @var string
     * @link http://www.smarty.net/docs/en/variable.caching.type.tpl
     */
    public $caching_type = 'file';

    /**
     * internal config properties
     * @var array
     * @internal
     */
    public $properties = array();

    /**
     * config type
     * @var string
     * @link http://www.smarty.net/docs/en/variable.default.config.type.tpl
     */
    public $default_config_type = 'file';

    /**
     * cached template objects
     * @var array
     * @internal
     */
    public $template_objects = array();

    /**
     * check If-Modified-Since headers
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.cache.modified.check.tpl
     */
    public $cache_modified_check = false;

    /**
     * registered plugins
     * @var array
     * @internal
     */
    public $registered_plugins = array();

    /**
     * plugin search order
     * @var array
     * @link <missing>
     */
    public $plugin_search_order = array('function', 'block', 'compiler', 'class');

    /**
     * registered objects
     * @var array
     * @internal
     */
    public $registered_objects = array();

    /**
     * registered classes
     * @var array
     * @internal
     */
    public $registered_classes = array();

    /**
     * registered filters
     * @var array
     * @internal
     */
    public $registered_filters = array();

    /**
     * registered resources
     * @var array
     * @internal
     */
    public $registered_resources = array();

    /**
     * resource handler cache
     * @var array
     * @internal
     */
    public $_resource_handlers = array();

    /**
     * registered cache resources
     * @var array
     * @internal
     */
    public $registered_cache_resources = array();

    /**
     * cache resource handler cache
     * @var array
     * @internal
     */
    public $_cacheresource_handlers = array();

    /**
     * autoload filter
     * @var array
     * @link http://www.smarty.net/docs/en/variable.autoload.filters.tpl
     */
    public $autoload_filters = array();

    /**
     * default modifier
     * @var array
     * @link http://www.smarty.net/docs/en/variable.default.modifiers.tpl
     */
    public $default_modifiers = array();

    /**
     * autoescape variable output
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.escape.html.tpl
     */
    public $escape_html = false;

    /**
     * global internal smarty vars
     * @var array
     */
    public static $_smarty_vars = array();

    /**
     * start time for execution time calculation
     * @var integer
     * @internal
     */
    public $start_time = 0;

    /**
     * default file permissions (octal)
     * @var integer
     * @internal
     */
    public $_file_perms = 0644;

    /**
     * default dir permissions (octal)
     * @var integer
     * @internal
     */
    public $_dir_perms = 0771;

    /**
     * block tag hierarchy
     * @var array
     * @internal
     */
    public $_tag_stack = array();

    /**
     * self pointer to Smarty object
     * @var Smarty
     * @internal
     */
    public $smarty;

    /**
     * required by the compiler for BC
     * @var string
     * @internal
     */
    public $_current_file = null;

    /**
     * internal flag to enable parser debugging
     * @var boolean
     * @internal
     */
    public $_parserdebug = false;

    /**
     * Saved parameter of merged templates during compilation
     *
     * @var array
     * @internal
     */
    public $merged_templates_func = array();
    /*     * #@- */

    /**
     * Initialize new Smarty object
     *
     */
    public function __construct() {
        // create variabale container
        parent::__construct();
        if (!isset(self::$global_tpl_vars)) {
            self::$global_tpl_vars = new Smarty_Variable_Container();
        }
        // selfpointer needed by some other class methods
        $this->smarty = $this;
        // PHP options
        if (is_callable('mb_internal_encoding')) {
            mb_internal_encoding(Smarty::$_CHARSET);
        }
        $this->start_time = microtime(true);
        // set default dirs
        $this->setTemplateDir('.' . DS . 'templates' . DS)
                ->setCompileDir('.' . DS . 'templates_c' . DS)
                ->setCacheDir('.' . DS . 'cache' . DS)
                ->setConfigDir('.' . DS . 'configs' . DS);

        $this->debug_tpl = 'file:' . dirname(__FILE__) . '/debug.tpl';
        if (isset($_SERVER['SCRIPT_NAME'])) {
            $this->assignGlobal('SCRIPT_NAME', $_SERVER['SCRIPT_NAME']);
        }
    }

    /**
     * Class destructor
     */
    public function __destruct() {
        // intentionally left blank
    }

    /**
     * <<magic>> set selfpointer on cloned object
     */
    public function __clone() {
        $this->smarty = $this;
    }

    /**
     * <<magic>> Generic getter.
     *
     * Calls the appropriate getter function.
     * Issues an E_USER_NOTICE if no valid getter is found.
     *
     * @param string $name property name
     * @return mixed
     */
    public function __get($name) {
        $allowed = array(
            'template_dir' => 'getTemplateDir',
            'config_dir' => 'getConfigDir',
            'plugins_dir' => 'getPluginsDir',
            'compile_dir' => 'getCompileDir',
            'cache_dir' => 'getCacheDir',
        );

        if (isset($allowed[$name])) {
            return $this->{$allowed[$name]}();
        } else {
            trigger_error('Undefined property: ' . get_class($this) . '::$' . $name, E_USER_NOTICE);
        }
    }

    /**
     * <<magic>> Generic setter.
     *
     * Calls the appropriate setter function.
     * Issues an E_USER_NOTICE if no valid setter is found.
     *
     * @param string $name  property name
     * @param mixed  $value parameter passed to setter
     */
    public function __set($name, $value) {
        $allowed = array(
            'template_dir' => 'setTemplateDir',
            'config_dir' => 'setConfigDir',
            'plugins_dir' => 'setPluginsDir',
            'compile_dir' => 'setCompileDir',
            'cache_dir' => 'setCacheDir',
        );

        if (isset($allowed[$name])) {
            $this->{$allowed[$name]}($value);
        } else {
            trigger_error('Undefined property: ' . get_class($this) . '::$' . $name, E_USER_NOTICE);
        }
    }

    /**
     * Check if a template resource exists
     *
     * @param string $resource_name template name
     * @return boolean status
     */
    public function templateExists($resource_name) {
        $source = Smarty_Resource::source(null, $this, $resource_name);
        return $source->exists;
    }

    /**
     * Returns a single or all global  variables
     *
     * @param object $smarty
     * @param string $varname variable name or null
     * @return string variable value or or array of variables
     */
    public function getGlobal($varname = null) {
        if (isset($varname)) {
            if (isset(self::$global_tpl_vars->{$varname}['value'])) {
                return self::$global_tpl_vars->{$varname}['value'];
            } else {
                return '';
            }
        } else {
            $_result = array();
            foreach (self::$global_tpl_vars AS $key => $var) {
                if (strpos($key, '___') !== 0) {
                    $_result[$key] = $var['value'];
                }
            }
            return $_result;
        }
    }

    /**
     * Empty cache folder
     *
     * @param integer $exp_time expiration time
     * @param string  $type     resource type
     * @return integer number of cache files deleted
     */
    function clearAllCache($exp_time = null, $type = null) {
        // load cache resource and call clearAll
        $_cache_resource = Smarty_CacheResource::load($this, $type);
        Smarty_CacheResource::invalidLoadedCache($this);
        return $_cache_resource->clearAll($this, $exp_time);
    }

    /**
     * Empty cache for a specific template
     *
     * @param string  $template_name template name
     * @param string  $cache_id      cache id
     * @param string  $compile_id    compile id
     * @param integer $exp_time      expiration time
     * @param string  $type          resource type
     * @return integer number of cache files deleted
     */
    public function clearCache($template_name, $cache_id = null, $compile_id = null, $exp_time = null, $type = null) {
        // load cache resource and call clear
        $_cache_resource = Smarty_CacheResource::load($this, $type);
        Smarty_CacheResource::invalidLoadedCache($this);
        return $_cache_resource->clear($this, $template_name, $cache_id, $compile_id, $exp_time);
    }

    /**
     * Delete compiled template file
     *
     * @param string $resource_name template name
     * @param string $compile_id compile id
     * @param integer $exp_time expiration time
     * @return integer number of template files deleted
     */
    public function clearCompiledTemplate($resource_name = null, $compile_id = null, $exp_time = null) {
        return Smarty_Compiled::clearCompiledTemplate($resource_name, $compile_id, $exp_time, $this);
    }

    /**
     * Loads security class and enables security
     *
     * @param string|Smarty_Security $security_class if a string is used, it must be class-name
     * @return Smarty current Smarty instance for chaining
     * @throws SmartyException when an invalid class name is provided
     */
    public function enableSecurity($security_class = null) {
        if ($security_class instanceof Smarty_Security) {
            $this->security_policy = $security_class;
            return $this;
        } elseif (is_object($security_class)) {
            throw new SmartyException("enableSecurity(): Class '" . get_class($security_class) . "' must extend Smarty_Security.");
        }
        if ($security_class == null) {
            $security_class = $this->security_class;
        }
        if (!class_exists($security_class)) {
            throw new SmartyException("enableSecurity(): Security class '$security_class' is not defined");
        } elseif ($security_class !== 'Smarty_Security' && !is_subclass_of($security_class, 'Smarty_Security')) {
            throw new SmartyException("enableSecurity(): Class '$security_class' must extend Smarty_Security.");
        } else {
            $this->security_policy = new $security_class($this);
        }

        return $this;
    }

    /**
     * Disable security
     * @return Smarty current Smarty instance for chaining
     */
    public function disableSecurity() {
        $this->security_policy = null;

        return $this;
    }

    /**
     * Set template directory
     *
     * @param string|array $template_dir directory(s) of template sources
     * @return Smarty current Smarty instance for chaining
     */
    public function setTemplateDir($template_dir) {
        $this->template_dir = array();
        foreach ((array) $template_dir as $k => $v) {
            $this->template_dir[$k] = rtrim($v, '/\\') . DS;
        }

        $this->joined_template_dir = join(DIRECTORY_SEPARATOR, $this->template_dir);
        return $this;
    }

    /**
     * Add template directory(s)
     *
     * @param string|array $template_dir directory(s) of template sources
     * @param string       $key          of the array element to assign the template dir to
     * @return Smarty current Smarty instance for chaining
     */
    public function addTemplateDir($template_dir, $key=null) {
        // make sure we're dealing with an array
        $this->template_dir = (array) $this->template_dir;

        if (is_array($template_dir)) {
            foreach ($template_dir as $k => $v) {
                if (is_int($k)) {
                    // indexes are not merged but appended
                    $this->template_dir[] = rtrim($v, '/\\') . DS;
                } else {
                    // string indexes are overridden
                    $this->template_dir[$k] = rtrim($v, '/\\') . DS;
                }
            }
        } elseif ($key !== null) {
            // override directory at specified index
            $this->template_dir[$key] = rtrim($template_dir, '/\\') . DS;
        } else {
            // append new directory
            $this->template_dir[] = rtrim($template_dir, '/\\') . DS;
        }
        $this->joined_template_dir = join(DIRECTORY_SEPARATOR, $this->template_dir);
        return $this;
    }

    /**
     * Get template directories
     *
     * @param mixed index of directory to get, null to get all
     * @return array|string list of template directories, or directory of $index
     */
    public function getTemplateDir($index=null) {
        if ($index !== null) {
            return isset($this->template_dir[$index]) ? $this->template_dir[$index] : null;
        }

        return (array) $this->template_dir;
    }

    /**
     * Set config directory
     *
     * @param string|array $template_dir directory(s) of configuration sources
     * @return Smarty current Smarty instance for chaining
     */
    public function setConfigDir($config_dir) {
        $this->config_dir = array();
        foreach ((array) $config_dir as $k => $v) {
            $this->config_dir[$k] = rtrim($v, '/\\') . DS;
        }

        $this->joined_config_dir = join(DIRECTORY_SEPARATOR, $this->config_dir);
        return $this;
    }

    /**
     * Add config directory(s)
     *
     * @param string|array $config_dir directory(s) of config sources
     * @param string key of the array element to assign the config dir to
     * @return Smarty current Smarty instance for chaining
     */
    public function addConfigDir($config_dir, $key=null) {
        // make sure we're dealing with an array
        $this->config_dir = (array) $this->config_dir;

        if (is_array($config_dir)) {
            foreach ($config_dir as $k => $v) {
                if (is_int($k)) {
                    // indexes are not merged but appended
                    $this->config_dir[] = rtrim($v, '/\\') . DS;
                } else {
                    // string indexes are overridden
                    $this->config_dir[$k] = rtrim($v, '/\\') . DS;
                }
            }
        } elseif ($key !== null) {
            // override directory at specified index
            $this->config_dir[$key] = rtrim($config_dir, '/\\') . DS;
        } else {
            // append new directory
            $this->config_dir[] = rtrim($config_dir, '/\\') . DS;
        }

        $this->joined_config_dir = join(DIRECTORY_SEPARATOR, $this->config_dir);
        return $this;
    }

    /**
     * Get config directory
     *
     * @param mixed index of directory to get, null to get all
     * @return array|string configuration directory
     */
    public function getConfigDir($index=null) {
        if ($index !== null) {
            return isset($this->config_dir[$index]) ? $this->config_dir[$index] : null;
        }

        return (array) $this->config_dir;
    }

    /**
     * Set plugins directory
     *
     * Adds {@link SMARTY_PLUGINS_DIR} if not specified
     * @param string|array $plugins_dir directory(s) of plugins
     * @return Smarty current Smarty instance for chaining
     */
    public function setPluginsDir($plugins_dir) {
        $this->plugins_dir = array();
        foreach ((array) $plugins_dir as $k => $v) {
            $this->plugins_dir[$k] = rtrim($v, '/\\') . DS;
        }

        return $this;
    }

    /**
     * Adds directory of plugin files
     *
     * @param object $smarty
     * @param string $ |array $ plugins folder
     * @return Smarty current Smarty instance for chaining
     */
    public function addPluginsDir($plugins_dir) {
        // make sure we're dealing with an array
        $this->plugins_dir = (array) $this->plugins_dir;

        if (is_array($plugins_dir)) {
            foreach ($plugins_dir as $k => $v) {
                if (is_int($k)) {
                    // indexes are not merged but appended
                    $this->plugins_dir[] = rtrim($v, '/\\') . DS;
                } else {
                    // string indexes are overridden
                    $this->plugins_dir[$k] = rtrim($v, '/\\') . DS;
                }
            }
        } else {
            // append new directory
            $this->plugins_dir[] = rtrim($plugins_dir, '/\\') . DS;
        }

        $this->plugins_dir = array_unique($this->plugins_dir);
        return $this;
    }

    /**
     * Get plugin directories
     *
     * @return array list of plugin directories
     */
    public function getPluginsDir() {
        return (array) $this->plugins_dir;
    }

    /**
     * Set compile directory
     *
     * @param string $compile_dir directory to store compiled templates in
     * @return Smarty current Smarty instance for chaining
     */
    public function setCompileDir($compile_dir) {
        $this->compile_dir = rtrim($compile_dir, '/\\') . DS;
        if (!isset(Smarty::$_muted_directories[$this->compile_dir])) {
            Smarty::$_muted_directories[$this->compile_dir] = null;
        }
        return $this;
    }

    /**
     * Get compiled directory
     *
     * @return string path to compiled templates
     */
    public function getCompileDir() {
        return $this->compile_dir;
    }

    /**
     * Set cache directory
     *
     * @param string $cache_dir directory to store cached templates in
     * @return Smarty current Smarty instance for chaining
     */
    public function setCacheDir($cache_dir) {
        $this->cache_dir = rtrim($cache_dir, '/\\') . DS;
        if (!isset(Smarty::$_muted_directories[$this->cache_dir])) {
            Smarty::$_muted_directories[$this->cache_dir] = null;
        }
        return $this;
    }

    /**
     * Get cache directory
     *
     * @return string path of cache directory
     */
    public function getCacheDir() {
        return $this->cache_dir;
    }

    /**
     * Set default modifiers
     *
     * @param array|string $modifiers modifier or list of modifiers to set
     * @return Smarty current Smarty instance for chaining
     */
    public function setDefaultModifiers($modifiers) {
        $this->default_modifiers = (array) $modifiers;
        return $this;
    }

    /**
     * Add default modifiers
     *
     * @param array|string $modifiers modifier or list of modifiers to add
     * @return Smarty current Smarty instance for chaining
     */
    public function addDefaultModifiers($modifiers) {
        if (is_array($modifiers)) {
            $this->default_modifiers = array_merge($this->default_modifiers, $modifiers);
        } else {
            $this->default_modifiers[] = $modifiers;
        }

        return $this;
    }

    /**
     * Get default modifiers
     *
     * @return array list of default modifiers
     */
    public function getDefaultModifiers() {
        return $this->default_modifiers;
    }

    /**
     * Set autoload filters
     *
     * @param array $filters filters to load automatically
     * @param string $type "pre", "output", … specify the filter type to set. Defaults to none treating $filters' keys as the appropriate types
     * @return Smarty current Smarty instance for chaining
     */
    public function setAutoloadFilters($filters, $type=null) {
        if ($type !== null) {
            $this->autoload_filters[$type] = (array) $filters;
        } else {
            $this->autoload_filters = (array) $filters;
        }

        return $this;
    }

    /**
     * Add autoload filters
     *
     * @param array $filters filters to load automatically
     * @param string $type "pre", "output", … specify the filter type to set. Defaults to none treating $filters' keys as the appropriate types
     * @return Smarty current Smarty instance for chaining
     */
    public function addAutoloadFilters($filters, $type=null) {
        if ($type !== null) {
            if (!empty($this->autoload_filters[$type])) {
                $this->autoload_filters[$type] = array_merge($this->autoload_filters[$type], (array) $filters);
            } else {
                $this->autoload_filters[$type] = (array) $filters;
            }
        } else {
            foreach ((array) $filters as $key => $value) {
                if (!empty($this->autoload_filters[$key])) {
                    $this->autoload_filters[$key] = array_merge($this->autoload_filters[$key], (array) $value);
                } else {
                    $this->autoload_filters[$key] = (array) $value;
                }
            }
        }

        return $this;
    }

    /**
     * Get autoload filters
     *
     * @param string $type type of filter to get autoloads for. Defaults to all autoload filters
     * @return array array( 'type1' => array( 'filter1', 'filter2', … ) ) or array( 'filter1', 'filter2', …) if $type was specified
     */
    public function getAutoloadFilters($type=null) {
        if ($type !== null) {
            return isset($this->autoload_filters[$type]) ? $this->autoload_filters[$type] : array();
        }

        return $this->autoload_filters;
    }

    /**
     * return name of debugging template
     *
     * @return string
     */
    public function getDebugTemplate() {
        return $this->debug_tpl;
    }

    /**
     * set the debug template
     *
     * @param string $tpl_name
     * @return Smarty current Smarty instance for chaining
     * @throws SmartyException if file is not readable
     */
    public function setDebugTemplate($tpl_name) {
        if (!is_readable($tpl_name)) {
            throw new SmartyException("setDebugTemplate(): Unknown file '{$tpl_name}'");
        }
        $this->debug_tpl = $tpl_name;

        return $this;
    }

    /**
     * creates a template object
     *
     * @param string $template the resource handle of the template file
     * @param mixed $cache_id cache id to be used with this template
     * @param mixed $compile_id compile id to be used with this template
     * @param object $parent next higher level of Smarty variables
     * @param boolean $do_clone flag is Smarty object shall be cloned
     * @return object template object
     */
    public function createTemplate($template, $cache_id = null, $compile_id = null, $parent = null, $do_clone = true) {
        if (!empty($cache_id) && (is_object($cache_id) || is_array($cache_id))) {
            $parent = $cache_id;
            $cache_id = null;
        }
        if (!empty($parent) && is_array($parent)) {
            $data = $parent;
            $parent = null;
        } else {
            $data = null;
        }
        // default to cache_id and compile_id of Smarty object
        $cache_id = $cache_id === null ? $this->cache_id : $cache_id;
        $compile_id = $compile_id === null ? $this->compile_id : $compile_id;
        // already in template cache?
        if ($this->allow_ambiguous_resources) {
            $_templateId = Smarty_Resource::getUniqueTemplateName($this, $template) . $cache_id . $compile_id;
        } else {
            $_templateId = $this->joined_template_dir . '#' . $template . $cache_id . $compile_id;
        }
        if (isset($_templateId[150])) {
            $_templateId = sha1($_templateId);
        }
        if ($do_clone) {
            if (isset($this->template_objects[$_templateId])) {
                // return cached template object
                $tpl = clone $this->template_objects[$_templateId];
                $tpl->smarty = clone $tpl->smarty;
                $tpl->parent = $parent;
            } else {
                $tpl = new $this->template_class($template, clone $this, $parent, $cache_id, $compile_id, null, null);
            }
        } else {
            if (isset($this->template_objects[$_templateId])) {
                // return cached template object
                $tpl = $this->template_objects[$_templateId];
                $tpl->parent = $parent;
            } else {
                $tpl = new $this->template_class($template, $this, $parent, $cache_id, $compile_id, null, null);
            }
        }
        // fill data if present
        if (!empty($data) && is_array($data)) {
            // set up variable values
            foreach ($data as $varname => $value) {
                $tpl->tpl_vars->$varname = array('value' => $value);
            }
        }
        return $tpl;
    }

    /**
     * Takes unknown classes and loads plugin files for them
     * class name format: Smarty_PluginType_PluginName
     * plugin filename format: plugintype.pluginname.php
     *
     * @param string $plugin_name    class plugin name to load
     * @param bool   $check          check if already loaded
     * @return string |boolean filepath of loaded file or false
     */
    public function loadPlugin($plugin_name, $check = true) {
        // if function or class exists, exit silently (already loaded)
        if ($check && (is_callable($plugin_name) || class_exists($plugin_name, false))) {
            return true;
        }
        // Plugin name is expected to be: Smarty_[Type]_[Name]
        $_name_parts = explode('_', $plugin_name, 3);
        // class name must have three parts to be valid plugin
        // count($_name_parts) < 3 === !isset($_name_parts[2])
        if (!isset($_name_parts[2]) || strtolower($_name_parts[0]) !== 'smarty') {
            throw new SmartyException("loadPlugin(): Plugin {$plugin_name} is not a valid name format");
            return false;
        }
        // if type is "internal", get plugin from sysplugins
        if (strtolower($_name_parts[1]) == 'internal') {
            $file = SMARTY_SYSPLUGINS_DIR . strtolower($plugin_name) . '.php';
            if (file_exists($file)) {
                require_once($file);
                return $file;
            } else {
                return false;
            }
        }
        // plugin filename is expected to be: [type].[name].php
        $_plugin_filename = "{$_name_parts[1]}.{$_name_parts[2]}.php";


        $_stream_resolve_include_path = function_exists('stream_resolve_include_path');
        // add SMARTY_PLUGINS_DIR if not present
        $_plugins_dir = $this->getPluginsDir();
        if (!$this->disable_core_plugins) {
            $_plugins_dir[] = SMARTY_PLUGINS_DIR;
        }

        // loop through plugin dirs and find the plugin
        foreach ($_plugins_dir as $_plugin_dir) {
            $names = array(
                $_plugin_dir . $_plugin_filename,
                $_plugin_dir . strtolower($_plugin_filename),
            );
            foreach ($names as $file) {
                if (file_exists($file)) {
                    require_once($file);
                    return $file;
                }
                if ($this->use_include_path && !preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_plugin_dir)) {
                    // try PHP include_path
                    if ($_stream_resolve_include_path) {
                        $file = stream_resolve_include_path($file);
                    } else {
                        $file = Smarty_Internal_Get_Include_Path::getIncludePath($file);
                    }
                    if ($file !== false) {
                        require_once($file);
                        return $file;
                    }
                }
            }
        }

        // no plugin loaded
        return false;
    }

    /**
     * Compile all template files
     *
     * @param string $extension file extension
     * @param bool $force_compile force all to recompile
     * @param int $time_limit
     * @param int $max_errors
     * @return integer number of template files recompiled
     */
    public function compileAllTemplates($extention = '.tpl', $force_compile = false, $time_limit = 0, $max_errors = null) {
        return Smarty_Internal_Utility::compileAllTemplates($extention, $force_compile, $time_limit, $max_errors, $this);
    }

    /**
     * Compile all config files
     *
     * @param string $extension file extension
     * @param bool $force_compile force all to recompile
     * @param int $time_limit
     * @param int $max_errors
     * @return integer number of template files recompiled
     */
    public function compileAllConfig($extention = '.conf', $force_compile = false, $time_limit = 0, $max_errors = null) {
        return Smarty_Internal_Utility::compileAllConfig($extention, $force_compile, $time_limit, $max_errors, $this);
    }

    /**
     * Return array of tag/attributes of all tags used by an template
     *
     * @param object $templae template object
     * @return array of tag/attributes
     */
    public function getTags(Smarty_Internal_Template $template) {
        return Smarty_Internal_Utility::getTags($template);
    }

    /**
     * Run installation test
     *
     * @param array $errors Array to write errors into, rather than outputting them
     * @return boolean true if setup is fine, false if something is wrong
     */
    public function testInstall(&$errors=null) {
        return Smarty_Internal_Utility::testInstall($this, $errors);
    }

    /**
     * Get Smarty Configuration Information
     *
     * @param boolean $html return formatted HTML, array else
     * @param integer $flags see Smarty_Internal_Info constants
     * @return string|array configuration information
     */
    public function info($html=true, $flags=0) {
        $info = new Smarty_Internal_Info($this);
        return $html ? $info->getHtml($flags) : $info->getArray($flags);
    }

    /**
     * Error Handler to mute expected messages
     *
     * @link http://php.net/set_error_handler
     * @param integer $errno Error level
     * @return boolean
     */
    public static function mutingErrorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
        $_is_muted_directory = false;

        // add the SMARTY_DIR to the list of muted directories
        if (!isset(Smarty::$_muted_directories[SMARTY_DIR])) {
            $smarty_dir = realpath(SMARTY_DIR);
            Smarty::$_muted_directories[SMARTY_DIR] = array(
                'file' => $smarty_dir,
                'length' => strlen($smarty_dir),
            );
        }

        // walk the muted directories and test against $errfile
        foreach (Smarty::$_muted_directories as $key => &$dir) {
            if (!$dir) {
                // resolve directory and length for speedy comparisons
                $file = realpath($key);
                $dir = array(
                    'file' => $file,
                    'length' => strlen($file),
                );
            }
            if (strpos($errfile, $dir['file']) === 0) {
                $_is_muted_directory = true;
                break;
            }
        }

        // pass to next error handler if this error did not occur inside SMARTY_DIR
        // or the error was within smarty but masked to be ignored
        if (!$_is_muted_directory || ($errno && $errno & error_reporting())) {
            if (Smarty::$_previous_error_handler) {
                return call_user_func(Smarty::$_previous_error_handler, $errno, $errstr, $errfile, $errline, $errcontext);
            } else {
                return false;
            }
        }
    }

    /**
     * Enable error handler to mute expected messages
     *
     * @return void
     */
    public static function muteExpectedErrors() {
        /*
          error muting is done because some people implemented custom error_handlers using
          http://php.net/set_error_handler and for some reason did not understand the following paragraph:

          It is important to remember that the standard PHP error handler is completely bypassed for the
          error types specified by error_types unless the callback function returns FALSE.
          error_reporting() settings will have no effect and your error handler will be called regardless -
          however you are still able to read the current value of error_reporting and act appropriately.
          Of particular note is that this value will be 0 if the statement that caused the error was
          prepended by the @ error-control operator.

          Smarty deliberately uses @filemtime() over file_exists() and filemtime() in some places. Reasons include
          - @filemtime() is almost twice as fast as using an additional file_exists()
          - between file_exists() and filemtime() a possible race condition is opened,
          which does not exist using the simple @filemtime() approach.
         */
        $error_handler = array('Smarty', 'mutingErrorHandler');
        $previous = set_error_handler($error_handler);

        // avoid dead loops
        if ($previous !== $error_handler) {
            Smarty::$_previous_error_handler = $previous;
        }
    }

    /**
     * Disable error handler muting expected messages
     *
     * @return void
     */
    public static function unmuteExpectedErrors() {
        restore_error_handler();
    }

}

// let PCRE (preg_*) treat strings as ISO-8859-1 if we're not dealing with UTF-8
if (Smarty::$_CHARSET !== 'UTF-8') {
    Smarty::$_UTF8_MODIFIER = '';
}

/**
 * Smarty exception class
 * @package Smarty
 */
class SmartyException extends Exception {

    public function __toString() {
        return "Smarty error: {$this->message}\n";
    }

}

/**
 * Smarty compiler exception class
 * @package Smarty
 */
class SmartyCompilerException extends SmartyException {

    public function __toString() {
        // TODO
        // NOTE: PHP does escape \n and HTML tags on return. For this reasion we echo the message.
        // This needs to be investigated later.
        echo "Compiler: {$this->message}";
        return '';
    }

}

/**
 * Smarty runtime exception class
 * loads template source and displays line where error did occur
 *
 * @package Smarty
 */
class SmartyRuntimeException extends SmartyException {

    protected $object = null;
    protected $line = null;

    public function __construct($message, $object = null) {
        $this->message = $message;
        $this->object = $object;
        if ($object->smarty->enable_traceback) {
            $this->line = $object->trace_call_stack[0][1];
        }
    }

    public function __toString() {
        $source = '';
        $source_trace = $this->object->smarty->enable_traceback;
        if ($source_trace) {
            if ($this->object->trace_call_stack[0][2] == 'eval' || $this->object->trace_call_stack[0][2] == 'string') {
                $this->file = $this->object->trace_call_stack[0][2] . ':';
                $source_trace = false;
            } else {
                $ptr = Smarty_Resource::source(null, $this->object->smarty, $this->object->trace_call_stack[0][0]);
                // make sure we reload source content
                unset($ptr->content);
                $this->file = $ptr->filepath;
                if (!$ptr->exists) {
                    $source_trace = false;
                }
            }
        }
        if ($source_trace == true) {
            preg_match_all("/\n/", $ptr->content, $match, PREG_OFFSET_CAPTURE);
            $start_line = max(1, $this->line - 2);
            $end_line = min($this->line + 2, count($match[0]) + 1);
            $source = "<br>";
            for ($i = $start_line; $i <= $end_line; $i++) {
                $from = 0;
                $to = 99999999;
                if (isset($match[0][$i - 2])) {
                    $from = $match[0][$i - 2][1];
                }
                if (isset($match[0][$i - 1])) {
                    $to = $match[0][$i - 1][1] - $from;
                }
                $substr = substr($ptr->content, $from, $to);
                $source .= sprintf('%4d : ', $i) . htmlspecialchars(trim(preg_replace('![\t\r\n]+!', ' ', $substr))) . "<br>";
            }
        }
        $msg = "<br>Smarty runtime exception: <b>{$this->message}</b> in <b>{$this->file}</b> line <b>{$this->line}</b>{$source}<br><br>";
        $stack = $this->object->trace_call_stack;
        array_shift($stack);
        foreach ($stack as $info) {
            $msg .= "<b>called by {$info[0]} in line {$info[1]}</b><br>";
        }
        $ptr = $this->object;
        while ($ptr->parent instanceof Smarty_Internal_Template) {
            $ptr = $ptr->parent;
            foreach ($ptr->trace_call_stack as $info) {
                $msg .= "<b>called by {$info[0]} in line {$info[1]}</b><br>";
            }
        }
        // TODO
        // NOTE: PHP does escape \n and HTML tags on return. For this reasion we echo the message.
        // This needs to be investigated later.
        echo $msg;
        return $this->message;
    }

}

/**
 * Autoloader
 */
function smartyAutoload($class) {
    $_class = strtolower($class);
    static $_classes = array(
'smarty_config_source' => true,
 'smarty_config_compiled' => true,
 'smarty_security' => true,
 'smarty_cacheresource' => true,
 'smarty_cacheresource_custom' => true,
 'smarty_cacheresource_keyvaluestore' => true,
 'smarty_resource' => true,
 'smarty_resource_custom' => true,
 'smarty_resource_uncompiled' => true,
 'smarty_resource_recompiled' => true,
 'smarty_compiled' => true,
 'smarty_template_cached' => 'smarty_cacheresource'
    );

    if (strpos($_class, 'smarty_internal_') === 0 || (isset($_classes[$_class]) && $_classes[$_class] === true)) {
        include SMARTY_SYSPLUGINS_DIR . $_class . '.php';
    } elseif (isset($_classes[$_class])) {
        include SMARTY_SYSPLUGINS_DIR . $_classes[$_class] . '.php';
    }
}
