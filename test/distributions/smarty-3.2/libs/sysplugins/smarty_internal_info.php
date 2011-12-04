<?php

class Smarty_Internal_Info
{
    const ALL = 255;
    const PHP = 1;
    const PROPERTIES = 2;
    const FILESYSTEM = 4;
    const PLUGINS = 8;
    const REGISTERED = 16;
    const DEFAULTS = 32;
    const SECURITY = 64;
    const CONSTANTS = 128;

    const NOT_AVAILABLE = '#!$#notappliccable#$!#';

    protected static $ignored_properties = array(
        'smarty' => true,
        'parent' => true,
        'source' => true,
        'compiled' => true,
        'compiler' => true,
        'cached' => true,
        'tpl_vars' => true,
        'config_vars' => true,

        // working these extra
        'default_modifiers' => true,
        'autoload_filters' => true,

        // working these extra
        'template_dir' => true,
        'compile_dir' => true,
        'config_dir' => true,
        'cache_dir' => true,
        'plugins_dir' => true,
    );
    protected static $constructed = array(
        'debug_tpl' => true,
        'template_dir' => true,
        'compile_dir' => true,
        'config_dir' => true,
        'cache_dir' => true,
        'plugins_dir' => true,
    );
    protected static $flags = array(
        'caching' => array(
            'CACHING_OFF',
            'CACHING_LIFETIME_CURRENT',
            'CACHING_LIFETIME_SAVED',
        ),
        'compile_check' => array(
            'COMPILECHECK_OFF',
            'COMPILECHECK_ON',
            'COMPILECHECK_CACHEMISS',
        ),
        'php_handling' => array(
            'PHP_PASSTHRU',
            'PHP_QUOTE',
            'PHP_REMOVE',
            'PHP_ALLOW',
        ),
        'error_unassigned' => array(
            'UNASSIGNED_IGNORE',
            'UNASSIGNED_NOTICE',
            'UNASSIGNED_EXCEPTION',
        ),
    );

    protected $smarty = null;
    protected $template = null;

    protected $data = null;
    protected $errors = array();
    protected $warnings = array();

    protected $php = array();
    protected $properties = array();
    protected $plugins = array();
    protected $registered = array();
    protected $defaults = array();
    protected $security = array();
    protected $filesystem = array();
    protected $constants = array();

    public function __construct(Smarty $smarty, Smarty_Internal_Template $template = null)
    {
        $this->smarty = $smarty;
        $this->template = $template;
    }

    public function getArray($flags=0)
    {
        $this->analyze($flags);
        return $this->data;
    }

    public function getHtml($flags=0)
    {
        $this->analyze($flags);

        $tpl = new Smarty();
        $tpl->assign('data', $this->data);
        $tpl->assign('info', $this);
        $tpl->assign('_smarty', $this->smarty);
        $tpl->assign('_template', $this->template);
        // don't litter any template_c around
        $template = file_get_contents(SMARTY_DIR . 'info.tpl');
        return $tpl->fetch('eval:' . $template);
    }

    protected function analyze($flags=0)
    {
        $this->data = array(
            'na' => self::NOT_AVAILABLE,
            'version' => Smarty::SMARTY_VERSION,
            'bc' => $this->smarty instanceof SmartyBC,
        );

        if (!$flags || $flags & self::PHP) {
            $this->analyzeEnvironment();
            $this->data['php'] = $this->php;
        }
        if (!$flags || $flags & self::PROPERTIES) {
            $this->analyzeProperties();
            $this->data['properties'] = $this->properties;
        }
        if (!$flags || $flags & self::FILESYSTEM) {
            $this->analyzeFilesystem();
            $this->data['filesystem'] = $this->filesystem;
        }
        if (!$flags || $flags & self::PLUGINS) {
            $this->analyzePlugins();
            $this->data['plugins'] = $this->plugins;
        }
        if (!$flags || $flags & self::REGISTERED) {
            $this->analyzeRegistered();
            $this->data['registered'] = $this->registered;
        }
        if (!$flags || $flags & self::DEFAULTS) {
            $this->analyzeDefaults();
            $this->data['defaults'] = $this->defaults;
        }
        if (!$flags || $flags & self::SECURITY) {
            $this->analyzeSecurity();
            $this->data['security'] = $this->security;
        }
        if (!$flags || $flags & self::CONSTANTS) {
            $this->analyzeConstants();
            $this->data['constants'] = $this->constants;
        }

        // purge plugins_dir if loaded by other test
        if ($flags && !($flags & self::FILESYSTEM)) {
            $this->data['filesystem'] = array();
        }

        $this->data['errors'] = $this->errors;
        $this->data['warnings'] = $this->warnings;
    }

    protected function analyzeTemplates()
    {
        // run through ALL templates,
        //  test if they compile
        //  test if they contain deprecated plugins
        //  show inheritance paths
        //  show include paths
    }

    protected function analyzeEnvironment()
    {
        if ($this->php) {
            return;
        }

        $this->php = array(
            'version' => phpversion(),
            'modules' => array(
                'mbstring' => array(
                    'name' => 'Multibyte String',
                    'href' => 'http://php.net/mbstring',
                    'available' => function_exists('mb_substr'),
                    'enabled' => SMARTY_MBSTRING,
                    'version' => phpversion('mbstring'),
                    'options' => array(
                        'func_overload' => array(
                            'name' => 'Function overload',
                            'is_value' => ini_get('mbstring.func_overload') & 2,
                            'need_value' => false,
                        ),
                    ),
                ),
                'pcre' => array(
                    'name' => 'Perl Compatible Regular Expressions',
                    'href' => 'http://php.net/pcre',
                    'available' => function_exists('preg_replace'),
                    'enabled' => true,
                    'version' => phpversion('pcre'),
                    'options' => array(
                        'backtrack_limit' => array(
                            'name' => 'Backtrack Limit',
                            'is_value' => ini_get('pcre.backtrack_limit'),
                            'need_value' => -1,
                        ),
                    ),
                )
            )
        );
    }

    protected function analyzeConstants()
    {
        if ($this->constants) {
            return;
        }

        $constants = array(
            'SMARTY_DIR' => realpath(dirname(__FILE__) .'/..') . DS,
            'SMARTY_SYSPLUGINS_DIR' => dirname(__FILE__) . DS,
            'SMARTY_PLUGINS_DIR' => realpath(dirname(__FILE__) .'/../plugins') . DS,
            'SMARTY_MBSTRING' => function_exists('mb_strlen'),
            'SMARTY_RESOURCE_CHAR_SET' => function_exists('mb_strlen') ? 'UTF-8' : 'ISO-8859-1',
            'SMARTY_RESOURCE_DATE_FORMAT' => '%b %e, %Y',
            'SMARTY_SPL_AUTOLOAD' => 0,
        );

        foreach ($constants as $key => $default) {
            $value = constant($key);
            $constants[$key] = array(
                'name' => $key,
                'value' => $value,
                'default' => $default,
                'diff' => $default === $value,
                'error' => null,
                'warning' => null,
            );
        }

        $this->constants = $constants;
    }

    protected function analyzeProperties()
    {
        if ($this->properties) {
            return;
        }

        $_clean_smarty = new Smarty();
        $template = null;
        $smarty = new ReflectionClass($this->smarty);
        if ($this->template) {
            $template = new ReflectionClass($this->template);
        }

        foreach ($smarty->getDefaultProperties() as $name => $value) {
            if ($name[0] == '_' || isset(self::$ignored_properties[$name])) {
                continue;
            }

            $property = $smarty->getProperty($name);
            if ($property->isStatic()) {
                continue;
            }

            $doc = $property->getDocComment();

            if (preg_match('#\* @internal\s#i', $doc, $matches)) {
                continue;
            }

            $type = null;
            if (preg_match('#\* @var (?<type>[a-zA-Z0-9_]+)\s#i', $doc, $matches)) {
                $type = $matches['type'];
            }

            $link = null;
            if (preg_match('#\* @link (?<link>[^\s]+)\s#i', $doc, $matches)) {
                $link = $matches['link'];
            }

            $_name = null;
            if (preg_match('#/\*{2}\s+\* (?<name>)\s\*#i', $doc, $matches)) {
                $_name = trim($matches['name']);
            }

            try {
                $_smarty_value = $this->sanitizeValue($name, $property->getValue($this->smarty), $type);
            } catch (ReflectionException $e) {
                $_smarty_value = self::NOT_AVAILABLE;
            }

            if ($template) {
                try {
                    $_property = $template->getProperty($name);
                    $_template_value = $this->sanitizeValue($name, $_property->getValue($this->template), $type);
                } catch (ReflectionException $e) {
                    $_template_value = self::NOT_AVAILABLE;
                }
            } else {
                $_template_value = self::NOT_AVAILABLE;
            }

            if (isset(self::$constructed[$name])) {
                $value = $_clean_smarty->$name;
            }
            $_default_value = $this->sanitizeValue($name, $value, $type);
            $this->properties[$name] = array(
                'name' => $_name ? $_name : $name,
                'type' => $type,
                'link' => $link,
                'default' => $_default_value,
                'flag' => isset(self::$flags[$name]),
                'smarty' => $_smarty_value,
                'template' => $_template_value,
                'error' => null,
                'warning' => null,
            );

            $this->properties[$name]['template_diff'] = $_smarty_value !== $_template_value;
            $this->properties[$name]['smarty_diff'] = $_smarty_value !== $_default_value;
        }

        ksort($this->properties);
        $this->analyzePropertiesPlausibility();
    }

    protected function analyzePropertiesPlausibility()
    {
        // maybe these checks should be performed by the compiler?

        if ($this->properties['left_delimiter']['smarty'] === $this->properties['right_delimiter']['smarty']) {
            $message = "Left and Right Delimiters are equal";
            $this->errors['properties-left_delimiter'] = $message;
            $this->properties['left_delimiter']['error'] = $message;
            $this->properties['right_delimiter']['error'] = $message;
        }

        if ($this->smarty->security_policy && $this->smarty->security_policy->php_handling != $this->smarty->php_handling) {
            $message = 'Smarty_Security::$php_handling superseeds Smarty::$php_handling';
            $this->warnings['properties-php_handling'] = $message;
            $this->properties['php_handling']['warning'] = $message;
        }
    }

    protected function analyzeFilesystem()
    {
        if ($this->filesystem) {
            return;
        }

        $this->filesystem = array(
            'template_dir' => array(),
            'config_dir' => array(),
            'cache_dir' => array(),
            'compile_dir' => array(),
            'plugins_dir' => array(),
        );

        foreach ($this->smarty->getTemplateDir() as $key => $path) {
            $t = $this->analyzeDirectory($path, false, $this->smarty->use_include_path);
            $t['key'] = $key;
            $this->filesystem['template_dir'][] = $t;
            if ($t['error']) {
                $this->errors['filesystem-template_dir'] = "Template Directories";
            }
        }

        foreach ($this->smarty->getConfigDir() as $key => $path) {
            $t = $this->analyzeDirectory($path, false, $this->smarty->use_include_path);
            $t['key'] = $key;
            $this->filesystem['config_dir'][] = $t;
            if ($t['error']) {
                $this->errors['filesystem-config_dir'] = "Config Directories";
            }
        }

        $_plugins_dir = $this->smarty->getPluginsDir();
        if (!$this->smarty->disable_core_plugins) {
            $_plugins_dir[] = SMARTY_PLUGINS_DIR;
        }

        foreach ($_plugins_dir as $key => $path) {
            $t = $this->analyzeDirectory($path, false, $this->smarty->use_include_path);
            $this->filesystem['plugins_dir'][] = $t;
            if ($t['error']) {
                $this->errors['filesystem-plugins_dir'] = "Plugin Directories";
            }
        }

        $path = $this->smarty->getCompileDir();
        $t = $this->analyzeDirectory($path, true);
        $this->filesystem['compile_dir'][] = $t;
        if ($t['error']) {
            $this->errors['filesystem-compile_dir'] = "Compile Directory";
        }

        $path = $this->smarty->getCacheDir();
        $t = $this->analyzeDirectory($path, true);
        $this->filesystem['cache_dir'][] = $t;
        if ($t['error']) {
            $this->errors['filesystem-cache_dir'] = "Cache Directory";
        }

        ksort($this->filesystem);
    }

    protected function analyzeDirectory($path, $expect_writable=false, $use_include_path=false)
    {
        $includepath = null;
        $realpath = realpath($path);
        if (!$realpath && $use_include_path && !preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $path)) {
            $includepath = Smarty_Internal_Get_Include_Path::getIncludePath($path);
            if ($includepath) {
                $realpath = realpath($includepath);
            }
        }

        $_is_directory = $realpath ? is_dir($realpath) : null;
        $_is_readable = $_is_directory && $realpath ? is_readable($realpath) : null;
        $_is_writable = $_is_directory && $realpath ? is_writable($realpath) : null;

        if (!$realpath) {
            $error = 'Not Found';
        } elseif (!$_is_directory) {
            $error = 'Not a Directory';
        } elseif (!$_is_readable) {
            $error = 'Not Readable';
        } elseif (!$_is_writable && $expect_writable) {
            $error = 'Not Writable';
        } else {
            $error = null;
        }

        return array(
            'key' => null,
            'path' => $path,
            'realpath' => $realpath,
            'includepath' => $includepath,
            'is_dir' => $_is_directory,
            'readable' => $_is_readable,
            'writable' => $_is_writable,
            'error' => $error,
            'warning' => null,
        );
    }

    protected function analyzePlugins()
    {
        if ($this->plugins) {
            return;
        }

        $this->plugins = array(
            'function' => array(),
            'modifier' => array(),
            'modifercompiler' => array(),
            'block' => array(),
            'compiler' => array(),
            'prefilter' => array(),
            'postfilter' => array(),
            'outputfilter' => array(),
            'variablefilter' => array(),
            'insert' => array(),
            'resource' => array(),
            'cacheresource' => array(),
        );

        // import plugins_dir
        if (!$this->filesystem) {
            $this->analyzeFilesystem();
        }

        $directories = $this->filesystem['plugins_dir'];

        // reverse so first element is actually shown in output
        $directories = array_reverse($directories);

        // scan plugins_dir
        foreach ($directories as $dir) {
            if (!$dir['realpath']) {
                continue;
            }

            $iterator = new DirectoryIterator($dir['realpath']);
            foreach ($iterator as $file) {
                if ($file->isDot()) {
                    continue;
                }

                $parts = explode('.', $file->getFilename());
                $type = strtolower($parts[0]);
                if (count($parts) != 3 || !isset($this->plugins[$type])) {
                    continue;
                }

                $name = $parts[1];
                $_name = "smarty_{$type}_{$name}";
                $realpath = $dir['realpath'] . DS . $file->getFilename();
                $function = $this->reflectedFunctionFromFile($_name, $realpath);

                $autoload = false;
                // autoload_filters
                if (isset($type[6]) && substr($type, -6) == 'filter' && $this->smarty->autoload_filters) {
                    $filter = substr($type, 0, -6);
                    if (isset($this->smarty->autoload_filters[$filter]) && in_array($name, $this->smarty->autoload_filters[$filter])) {
                        $autoload = true;
                    }
                }

                // default_modifiers
                if ($type == 'modifier' && $this->smarty->default_modifiers &&  in_array($name, $this->smarty->default_modifiers)) {
                    $autoload = true;
                }

                if (isset($this->plugins[$type][$name])) {
                    $this->warnings['plugins-' . $type . '-' . $name] = "Plugin '{$name}' found in at least 2 directories";
                }

                $signature = null;
                $nocache = null;
                $cache_attr = null;
                if ($function instanceof ReflectionFunction) {
                    $signature = $this->reflectedSignature($function);
                    list($nocache, $cache_attr) = $this->reflectedAnnotations($function);
                }

                $this->plugins[$type][$name] = array(
                    'name' => $name,
                    'type' => $type,
                    'file' => $file->getFilename(),
                    'realpath' => $realpath,

                    'function' => $_name,
                    'line' => $function instanceof Reflector ? $function->getStartLine() : null,
                    'signature' => $signature,
                    'nocache' => $nocache,
                    'cache_attr' => $cache_attr,

                    'registered' => null,
                    'lazyloaded' => true,
                    'autoload' => $autoload,
                    'error' => is_string($function) ? $function : null,
                    'warning' => null,
                );
            }
        }

        // scan registered_plugins
        foreach ($this->smarty->registered_plugins as $type => $plugins) {
            if (!isset($this->plugins[$type])) {
                continue;
            }
            foreach ($plugins as $name => $_plugin) {
                list($callback, $nocache, $attributes) = $_plugin;
                $function = $this->reflectedFunctionOfCallable($callback);

                if (isset($this->plugins[$type][$name])) {
                    $this->warnings['plugins-' . $type . '-' . $name] = "Plugin '{$name}' found in at least 2 directories";
                }

                $this->plugins[$type][$name] = array(
                    'name' => $name,
                    'type' => $type,
                    'file' => $function ? basename($function->getFileName()) : null,
                    'realpath' => $function ? $function->getFileName() : null,

                    'function' => $function ? $function->getName() : null,
                    'line' => $function ? $function->getStartLine() : null,
                    'signature' => $function ? $this->reflectedSignature($function) : null,
                    'nocache' => $nocache,
                    'cache_attr' => $attributes,

                    'registered' => true,
                    'lazyloaded' => false,
                    'autoload' => false,
                    'error' => null,
                    'warning' => null,
                );
            }
        }

        // scan registered_filters
        foreach ($this->smarty->registered_filters as $type => $filters) {
            $type .= 'filter';
            if (!isset($this->plugins[$type])) {
                continue;
            }
            foreach ($filters as $callback) {
                $function = $this->reflectedFunctionOfCallable($callback);

                if (isset($this->plugins[$type][$name])) {
                    $this->warnings['plugins-' . $type . '-' . $name] = "Plugin '{$name}' found in at least 2 directories";
                }

                $this->plugins[$type][$name] = array(
                    'name' => $name,
                    'type' => $type,
                    'file' => $function ? basename($function->getFileName()) : null,
                    'realpath' => $function ? $function->getFileName() : null,

                    'function' => $function ? $function->getName() : null,
                    'line' => $function ? $function->getStartLine() : null,
                    'signature' => $function ? $this->reflectedSignature($function) : null,
                    'nocache' => null,
                    'cache_attr' => null,

                    'registered' => true,
                    'lazyloaded' => false,
                    'autoload' => false,
                    'error' => null,
                    'warning' => null,
                );
            }
        }

        ksort($this->plugins);
        foreach ($this->plugins as &$plugins) {
            ksort($plugins);
        }
    }

    protected function analyzeRegistered()
    {
        if ($this->registered) {
            return;
        }

        // analyze registered_objects
        foreach ($this->smarty->registered_objects as $name => $_object) {
            list($object, $allowed, $args, $blocked) = $object;

        }

        // analyze registered_classes
        foreach ($this->smarty->registered_classes as $name => $class) {

        }

        // Smarty::$registered_filters might be getting an overhaul

        // analyze registered_resources
        foreach ($this->smarty->registered_resources as $name => $handler) {

        }

        // analyze registered_cacheresources
        foreach ($this->smarty->registered_cache_resources as $name => $handler) {

        }
    }

    protected function analyzeDefaults()
    {
        if ($this->defaults) {
            return;
        }

        foreach ($this->smarty->default_modifiers as $callback) {
            $function = $this->reflectedFunctionOfCallable($callback);

        }

        // Smarty::$autoload_filters might be getting an overhaul
    }

    protected function analyzeSecurity()
    {
        if ($this->security) {
            return;
        }

        /*
            php_handling (wtf?)
            secure_dir (template_dir config_dir) -> also in {fetch}
            trusted_dir
            static_classes (registered_classes?)
            php_functions
            php_modifiers
            allowed_tags, disabled_tags
            allowed_modifiers, disabled_modifiers
            streams
            allow_constants
            allow_super_globals
        */
    }

    protected function reflectedAnnotations($function) {
        $attributes = array(null, null);

        /*
        * @smarty_nocache
        * @smarty_cache_attr param1, param2
        */

        // get PHPdoc data
        $doc = $function->getDocComment();

        if ($doc && preg_match_all('#@(?<name>smarty_nocache|smarty_cache_attr)(?<value>.*)$#m', $doc, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $m = explode(',', $match['value']);
                $m = array_map('trim', $m);
                if (count($m) === 1 && $m[0] === '') {
                    $attributes[0] = true;
                } else {
                    $attributes[1] = $m;
                }
            }
        }

        return $attributes;
    }

    protected function reflectedSignature(ReflectionFunctionAbstract $function)
    {
        $params = array();
        foreach ($function->getParameters() as $p) {
            $param = '';
            if ($c = $p->getClass()) {
                $param .= $c->getName() .' ';
            }

            $param .= '$' . $p->getName();
            try {
                $param .= '='. var_export($p->getDefaultValue(), true);
            } catch (ReflectionException $e) {}

            $params[] = $param;
        }

        $signature = $function->getName() .'(' . join(', ', $params) . ')';
        return $signature;
    }

    protected function reflectedFunctionFromFile($function_name, $filepath)
    {
        // Not sure if this is really such a great idea…

        // make sure we're not running into syntax errors
        $last = exec('php -l ' . escapeshellarg($filepath));
        if (!$last) {
            return null;
        }
        if (strpos($last, 'Errors parsing') !== false) {
            return 'syntax';
        }

        ob_start();
        include_once $filepath;
        ob_end_clean();
        if (function_exists($function_name)) {
            return new ReflectionFunction($function_name);
        } elseif (class_exists($function_name)) {
            return new ReflectionClass($function_name);
        }

        return null;
    }

    protected function reflectedFunctionOfCallable($callback)
    {
        try {
            if (is_string($callback) || $callback instanceof Closure) {
                return new ReflectionFunction($callback);
            } elseif (is_array($callback)) {
                $cn = is_object($callback[0]) ? 'ReflectionObject' : 'ReflectionClass';
                $reflection = new $cn($callback[0]);
                return $reflection->getMethod($callback[1]);
            } elseif (is_object($callback) && method_exists($callback, '__invoke')) {
                $reflection = new ReflectionObject($callback);
                return $reflection->getMethod('__invoke');
            }
        } catch(ReflectionException $e) {}

        return null;
    }

    protected function sanitizeValue($name, $value, $type)
    {
        switch ($type) {
            case 'boolean':
                $value = (boolean) $value;
                break;

            case 'integer':
                $value = (int) $value;
                break;

            case 'float':
                $value = (int) $value;
                break;

            case 'array':
                $value = (array) $value;
                break;

            case 'string':
                $value = (string) $value;
                break;

            case 'callable':
                $value = $value ? '(function)' : null;
                break;
        }

        if (isset(self::$flags[$name])) {
            $value = isset(self::$flags[$name][$value]) ? self::$flags[$name][$value] : $value;
        }

        return $value;
    }

}