<?php
/**
 * Smarty Internal Plugin Template
 *
 * This file contains the Smarty template engine
 *
 * @package Smarty
 * @subpackage Template
 * @author Uwe Tews
 */

/**
 * Main class with template data structures and methods
 *
 * @package Smarty
 * @subpackage Template
 *
 * @property Smarty_Template_Source                 $source
 * @property Smarty_Compiled                        $compiled
 * @property Smarty_Template_Cached                 $cached
 * @property Smarty_Internal_SmartyTemplateCompiler $compiler
 */
class Smarty_Internal_Template extends Smarty_Internal_TemplateBase {

    /**
     * cache_id
     * @var string
     * @link http://www.smarty.net/docs/en/variable.cache.id.tpl
     */
    public $cache_id = null;
    /**
     * $compile_id
     * @var string
     * @link http://www.smarty.net/docs/en/variable.compile.id.tpl
     */
    public $compile_id = null;
    /**
     * caching enabled
     * @var boolean
     * @link http://www.smarty.net/docs/en/variable.caching.tpl
     */
    public $caching = null;
    /**
     * cache lifetime in seconds
     * @var integer
     * @link http://www.smarty.net/docs/en/variable.cache.lifetime.tpl
     */
    public $cache_lifetime = null;
    /**
     * individually cached subtemplates
     * @var array
     */
    public $cached_subtemplates = array();
    /**
     * Template resource
     * @var string
     * @internal
     */
    public $template_resource = null;
    /**
     * flag if compiled template is invalid and must be (re)compiled
     * @var boolean
     * @internal
     */
    public $mustCompile = null;
    /**
     * flag if template does contain nocache code sections
     * @var boolean
     * @internal
     */
    public $has_nocache_code = false;
    /**
     * special compiled and cached template properties
     * @var array
     * @internal
     */
    public $properties = array('file_dependency' => array(),
        'nocache_hash' => '',
        'function' => array());
    /**
     * required plugins
     * @var array
     * @internal
     */
    public $required_plugins = array('compiled' => array(), 'nocache' => array());
    /**
     * Global smarty instance
     * @var Smarty
     * @internal
     */
    public $smarty = null;
    /**
     * blocks for template inheritance
     * @var array
     * @internal
     */
    public $block_data = array();
    /**
     * variable filters
     * @var array
     * @internal
     */
    public $variable_filters = array();
    /**
     * optional log of tag/attributes
     * @var array
     * @internal
     */
    public $used_tags = array();
    /**
     * internal flag to allow relative path in child template blocks
     * @var boolean
     * @internal
     */
    public $allow_relative_path = false;
    /**
     * internal capture runtime stack
     * @var array
     * @internal
     */
    public $_capture_stack = array();
    /**
     * template template call stack for traceback
     * @var array
     * @internal
     */
    public $trace_call_stack = array();

    /**
     * Create template data object
     *
     * Some of the global Smarty settings copied to template scope
     * It load the required template resources and cacher plugins
     *
     * @param string                   $template_resource template resource string
     * @param Smarty                   $smarty            Smarty instance
     * @param Smarty_Internal_Template $_parent           back pointer to parent object with variables or null
     * @param mixed                    $_cache_id cache   id or null
     * @param mixed                    $_compile_id       compile id or null
     * @param int                      $_caching          use caching?
     * @param int                      $_cache_lifetime   cache life-time in seconds
     * @param boolean                  $no_var_container  flag to suppress generation of variable container
     */
    public function __construct($template_resource, $smarty, $_parent = null, $_cache_id = null, $_compile_id = null, $_caching = null, $_cache_lifetime = null, $no_var_container = false)
    {
        // set up local variable scope
        parent::__construct();

        $this->smarty = &$smarty;
        // Smarty parameter
        $this->cache_id = $_cache_id === null ? $this->smarty->cache_id : $_cache_id;
        $this->compile_id = $_compile_id === null ? $this->smarty->compile_id : $_compile_id;
        $this->caching = $_caching === null ? $this->smarty->caching : $_caching;
        if ($this->caching === true)
            $this->caching = Smarty::CACHING_LIFETIME_CURRENT;
        $this->cache_lifetime = $_cache_lifetime === null ? $this->smarty->cache_lifetime : $_cache_lifetime;
        $this->parent = $_parent;
        // Template resource
        $this->template_resource = $template_resource;
        // copy block data of template inheritance
        if ($this->parent instanceof Smarty_Internal_Template) {
            $this->block_data = $this->parent->block_data;
        }
    }

    /**
     * Returns if the current template must be compiled by the Smarty compiler
     *
     * It does compare the timestamps of template source and the compiled templates and checks the force compile configuration
     *
     * @return boolean true if the template must be compiled
     */
    public function mustCompile()
    {
        if (!$this->source->exists) {
            if ($this->parent instanceof Smarty_Internal_Template) {
                $parent_resource = " in '$this->parent->template_resource}'";
            } else {
                $parent_resource = '';
            }
            throw new SmartyException("Unable to load template {$this->source->type} '{$this->source->name}'{$parent_resource}");
        }
        if ($this->mustCompile === null) {
            $this->mustCompile = (!$this->source->uncompiled && ($this->smarty->force_compile || $this->source->recompiled || $this->compiled->timestamp === false ||
                    ($this->smarty->compile_check && $this->compiled->timestamp < $this->source->timestamp)));
        }
        return $this->mustCompile;
    }


    /**
     * Writes the cached template output
     *
     * @return bool
     */
    public function writeCachedContent($content)
    {
        if ($this->source->recompiled || !($this->caching == Smarty::CACHING_LIFETIME_CURRENT || $this->caching == Smarty::CACHING_LIFETIME_SAVED)) {
            // don't write cache file
            return false;
        }
        $this->properties['cache_lifetime'] = $this->cache_lifetime;
        $this->properties['unifunc'] = 'content_' . uniqid('', false);
        $content = $this->createTemplateCodeFrame($content, true);
        $_smarty_tpl = $this;
        eval("?>" . $content);
        $this->cached->valid = true;
        $this->cached->processed = true;
        return $this->cached->write($this, $content);
    }

    /**
     * Template code runtime function to get subtemplate content
     *
     * @param string  $template       the resource handle of the template file
     * @param mixed   $cache_id       cache id to be used with this template
     * @param mixed   $compile_id     compile id to be used with this template
     * @param integer $caching        cache mode
     * @param integer $cache_lifetime life time of cache data
     * @param array   $vars optional  variables to assign
     * @param int     $parent_scope   scope in which {include} should execute
     * @returns string template content
     */
    public function getSubTemplate($template, $cache_id, $compile_id, $caching, $cache_lifetime, $data, $parent_scope)
    {
        $cloned = false;
        // already in template cache?
        if ($this->smarty->allow_ambiguous_resources) {
            $_templateId = Smarty_Resource::getUniqueTemplateName($this->smarty, $template) . $cache_id . $compile_id;
        } else {
            $_templateId = $this->smarty->joined_template_dir . '#' . $template . $cache_id . $compile_id;
        }

        if (isset($_templateId[150])) {
            $_templateId = sha1($_templateId);
        }
        if ($this->caching && $caching && $caching != 9999) {
            $this->cached_subtemplates[$_templateId] = array($template, $cache_id, $compile_id, $caching, $cache_lifetime);
        }
        if (isset($this->smarty->template_objects[$_templateId])) {
            // clone cached template object because of possible recursive call
            $tpl = clone $this->smarty->template_objects[$_templateId];
            $cloned = true;
            $tpl->parent = $this;
            $tpl->caching = $caching;
            $tpl->cache_lifetime = $cache_lifetime;
        } else {
            $tpl = new $this->smarty->template_class($template, $this->smarty, $this, $cache_id, $compile_id, $caching, $cache_lifetime, true);
        }
        if ($parent_scope == Smarty::SCOPE_LOCAL) {
            $tpl->tpl_vars = clone $this->tpl_vars;
            $tpl->tpl_vars->___smarty__data = $tpl;
            $tpl->config_vars =  clone $this->config_vars;
            $tpl->config_vars->___smarty__data = $tpl;
        } elseif ($parent_scope == Smarty::SCOPE_PARENT) {
            $tpl->tpl_vars = $this->tpl_vars;
            $tpl->config_vars = $this->config_vars;
        } elseif ($parent_scope == Smarty::SCOPE_GLOBAL) {
            $tpl->tpl_vars = Smarty::$global_tpl_vars;
        } elseif ($parent_scope == Smarty::SCOPE_ROOT) {
            if (($scope_ptr = $this->getScopePointer($parent_scope)) != null) {
                $tpl->tpl_vars = $scope_ptr->tpl_vars;
                $tpl->config_vars = $scope_ptr->config_vars;
            } else {
                $tpl->tpl_vars = new Smarty_Variable_Container($tpl);
                $tpl->config_vars = new Smarty_Config_Variable_Container($pl);
            }
        }
        if (!empty($data)) {
            // set up variable values
            foreach ($data as $_key => $_val) {
                $tpl->tpl_vars->$_key = $_val;
            }
        }
        $result = $tpl->fetch(null, null, null, null, false, true, false);
        if ($cloned) {
            unset($tpl->tpl_vars, $tpl->config_vars, $tpl);
        }
        return $result;
    }

    /**
     * Template code runtime function to set up an subtemplate
     *
     * @param string  $template       the resource handle of the template file
     * @param mixed   $cache_id       cache id to be used with this template
     * @param mixed   $compile_id     compile id to be used with this template
     * @param integer $caching        cache mode
     * @param integer $cache_lifetime life time of cache data
     * @param array   $vars optional  variables to assign
     * @param int     $parent_scope   scope in which {include} should execute
     * @param string  $hash           nocache hash code if inline template
     * @returns string template content
     */
    public function setupInlineSubTemplate($template, $cache_id, $compile_id, $caching, $cache_lifetime, $data, $parent_scope, $hash)
    {
        $tpl = new $this->smarty->template_class($template, $this->smarty, $this, $cache_id, $compile_id, $caching, $cache_lifetime, true);
        $tpl->properties['nocache_hash']  = $hash;
        // get variables from calling scope
        if ($parent_scope == Smarty::SCOPE_LOCAL) {
            $tpl->tpl_vars = clone $this->tpl_vars;
            $tpl->tpl_vars->___smarty__data = $tpl;
            $tpl->config_vars =  clone $this->config_vars;
            $tpl->config_vars->___smarty__data = $tpl;
        } elseif ($parent_scope == Smarty::SCOPE_PARENT) {
            $tpl->tpl_vars = $this->tpl_vars;
            $tpl->config_vars = $this->config_vars;
        } elseif ($parent_scope == Smarty::SCOPE_GLOBAL) {
            $tpl->tpl_vars = Smarty::$global_tpl_vars;
        } elseif ($parent_scope == Smarty::SCOPE_ROOT) {
            if (($scope_ptr = $this->getScopePointer($parent_scope)) != null) {
                $tpl->tpl_vars = $scope_ptr->tpl_vars;
                $tpl->config_vars = $scope_ptr->config_vars;
            } else {
                $tpl->tpl_vars = new Smarty_Variable_Container($tpl);
                $tpl->config_vars = new Smarty_Config_Variable_Container($pl);
            }
        }
        if (!empty($data)) {
            // set up variable values
            foreach ($data as $_key => $_val) {
                $tpl->tpl_vars->$_key = $_val;
            }
        }
        return $tpl;
    }


    /**
     * Create code frame for compiled and cached templates
     *
     * @param string $content   optional template content
     * @param bool   $cache     flag for cache file
     * @return string
     */
    public function createTemplateCodeFrame($content = '', $cache = false)
    {
        $plugins_string = '';
        // include code for plugins
        if (!$cache) {
            if (!empty($this->required_plugins['compiled'])) {
                $plugins_string = '<?php ';
                foreach ($this->required_plugins['compiled'] as $tmp) {
                    foreach ($tmp as $data) {
                        $plugins_string .= "if (!is_callable('{$data['function']}')) include '{$data['file']}';\n";
                    }
                }
                $plugins_string .= '?>';
            }
            if (!empty($this->required_plugins['nocache'])) {
                $this->has_nocache_code = true;
                $plugins_string .= "<?php echo '/*%%SmartyNocache:{$this->properties['nocache_hash']}%%*/<?php \$_smarty = \$_smarty_tpl->smarty; ";
                foreach ($this->required_plugins['nocache'] as $tmp) {
                    foreach ($tmp as $data) {
                        $plugins_string .= addslashes("if (!is_callable('{$data['function']}')) include '{$data['file']}';\n");
                    }
                }
                $plugins_string .= "?>/*/%%SmartyNocache:{$this->properties['nocache_hash']}%%*/';?>\n";
            }
        }
        // build property code
        $this->properties['has_nocache_code'] = $this->has_nocache_code;
        $output = '';
        if (!$this->source->recompiled) {
            $output = "<?php /*%%SmartyHeaderCode:{$this->properties['nocache_hash']}%%*/";
            if ($this->smarty->direct_access_security) {
                $output .= "if(!defined('SMARTY_DIR')) exit('no direct access allowed');\n";
            }
        }
        if ($cache) {
            // remove compiled code of{function} definition
            unset($this->properties['function']);
            if (!empty($this->smarty->template_functions)) {
                // copy code of {function} tags called in nocache mode
                foreach ($this->smarty->template_functions as $name => $function_data) {
                    if (isset($function_data['called_nocache'])) {
                        foreach ($function_data['called_functions'] as $func_name) {
                            $this->smarty->template_functions[$func_name]['called_nocache'] = true;
                        }
                    }
                }
                 foreach ($this->smarty->template_functions as $name => $function_data) {
                    if (isset($function_data['called_nocache'])) {
                        unset($function_data['called_nocache'], $function_data['called_functions'], $this->smarty->template_functions[$name]['called_nocache']);
                        $this->properties['function'][$name] = $function_data;
                    }
                }
            }
        }
        $this->properties['version'] = Smarty::SMARTY_VERSION;
        if (!isset($this->properties['unifunc'])) {
            $this->properties['unifunc'] = 'content_' . uniqid('', false);
        }
        $this->properties['cachedsubtemplates'] = $this->cached_subtemplates;
        if (!$this->source->recompiled) {
            $output .= "\$_valid = \$_smarty_tpl->decodeProperties(" . var_export($this->properties, true) . ',' . ($cache ? 'true' : 'false') . "); /*/%%SmartyHeaderCode%%*/?>\n";
        }
        if (!$this->source->recompiled) {
            $output .= '<?php if ($_valid && !is_callable(\'' . $this->properties['unifunc'] . '\')) {function ' . $this->properties['unifunc'] . "(\$_smarty_tpl) {\n?>";
        }
        $output .= $plugins_string;
        $output .= $content;
        if (!$this->source->recompiled) {
            $output .= '<?php }} ?>';
        }
        // make sure that we don't run into backtrack limit errors
        ini_set('pcre.backtrack_limit', -1);
        $output = preg_replace_callback('/(\?>)([\n]?)(<\?php )|(\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\';|"[^"\\\\]*(?:\\\\.[^"\\\\]*)*";)/',array($this,'removePhpTags'), $output);
        return $output;
    }

    /**
     * preg_replace callback to remove unneeded  ?><?php tags
     *
     * @param string $match match string
     * @return string  replacemant
     */
    function removePhpTags($match) {
        if (isset($match[4])) {
            return $match[4];
        }
        if (isset($match[2])) {
            return $match[2];
        }
        return '';
    }

    /**
     * This function is executed automatically when a compiled or cached template file is included
     *
     * - Decode saved properties from compiled template and cache files
     * - Check if compiled or cache file is valid
     *
     * @param array $properties     special template properties
     * @param bool  $cache          flag if called from cache file
     * @return bool                 flag if compiled or cache file is valid
     */
    public function decodeProperties($properties, $cache = false)
    {
        $this->has_nocache_code = $properties['has_nocache_code'];
        $this->properties['nocache_hash'] = $properties['nocache_hash'];
        if (isset($properties['cache_lifetime'])) {
            $this->properties['cache_lifetime'] = $properties['cache_lifetime'];
        }
        if (isset($properties['file_dependency'])) {
            $this->properties['file_dependency'] = array_merge($this->properties['file_dependency'], $properties['file_dependency']);
        }
        if (!empty($properties['function'])) {
            $this->properties['function'] = array_merge($this->properties['function'], $properties['function']);
            $this->smarty->template_functions = array_merge($this->smarty->template_functions, $properties['function']);
        }
        if (isset($properties['cachedsubtemplates'])) {
            $this->cached_subtemplates = $properties['cachedsubtemplates'];
        }
         $this->properties['version'] = (isset($properties['version'])) ? $properties['version'] : '';
        $this->properties['unifunc'] = $properties['unifunc'];
        // check file dependencies at compiled code
        $is_valid = true;
        if ($this->properties['version'] != Smarty::SMARTY_VERSION) {
            $is_valid = false;
        } else if (((!$cache && $this->smarty->compile_check && empty($this->compiled->_properties) && !$this->compiled->isCompiled) || $cache && ($this->smarty->compile_check === true || $this->smarty->compile_check === Smarty::COMPILECHECK_ON)) && !empty($this->properties['file_dependency'])) {
            foreach ($this->properties['file_dependency'] as $_file_to_check) {
                if ($_file_to_check[2] == 'file' || $_file_to_check[2] == 'php') {
                    if ($this->source->filepath == $_file_to_check[0] && isset($this->source->timestamp)) {
                        // do not recheck current template
                        $mtime = $this->source->timestamp;
                    } else {
                        // file and php types can be checked without loading the respective resource handlers
                        $mtime = filemtime($_file_to_check[0]);
                    }
                } elseif ($_file_to_check[2] == 'string') {
                    continue;
                } else {
                    $source = Smarty_Resource::source(null, $this->smarty, $_file_to_check[0]);
                    $mtime = $source->timestamp;
                }
                if ($mtime > $_file_to_check[1]) {
                    $is_valid = false;
                    break;
                }
            }
        }
        if ($cache) {
            $this->cached->valid = $is_valid;
        } else {
            $this->mustCompile = !$is_valid;
        }
        // store data in reusable Smarty_Compiled
        if (!$cache) {
            $this->compiled->_properties = $properties;
        }
        return $is_valid;
    }

    /**
     * Template code runtime function to create a local Smarty variable for array assignments
     *
     * @param string $tpl_var   tempate variable name
     * @param bool   $nocache   cache mode of variable
     */
    public function createLocalArrayVariable($tpl_var, $nocache = false)
    {
            $this->tpl_vars->$tpl_var = $this->getVariable($tpl_var, null, true, false);
                    $key = '___nocache_'.$tpl_var;
                    $this->tpl_vars->$key = $nocache;
        if (!(is_array($this->tpl_vars->$tpl_var) || $this->tpl_vars->$tpl_var instanceof ArrayAccess)) {
            settype($this->tpl_vars->$tpl_var, 'array');
        }
    }

    /**
     * Get parent or root of template parent chain
     *
     * @param int $scope    parent or root scope
     * @return mixed object
     */
    public function getScopePointer($scope)
    {
        if ($scope == Smarty::SCOPE_PARENT && !empty($this->parent)) {
            return $this->parent;
        } elseif ($scope == Smarty::SCOPE_ROOT && !empty($this->parent)) {
            $ptr = $this->parent;
            while (!empty($ptr->parent)) {
                $ptr = $ptr->parent;
            }
            return $ptr;
        }
        return null;
    }

    /**
     * [util function] counts an array, arrayaccess/traversable or PDOStatement object
     *
     * @param mixed $value
     * @return int the count for arrays and objects that implement countable, 1 for other objects that don't, and 0 for empty elements
     */
    public function _count($value)
    {
        if (is_array($value) === true || $value instanceof Countable) {
            return count($value);
        } elseif ($value instanceof IteratorAggregate) {
            // Note: getIterator() returns a Traversable, not an Iterator
            // thus rewind() and valid() methods may not be present
            return iterator_count($value->getIterator());
        } elseif ($value instanceof Iterator) {
            return iterator_count($value);
        } elseif ($value instanceof PDOStatement) {
            return $value->rowCount();
        } elseif ($value instanceof Traversable) {
            return iterator_count($value);
        } elseif ($value instanceof ArrayAccess) {
            if ($value->offsetExists(0)) {
                return 1;
            }
        } elseif (is_object($value)) {
            return count($value);
        }
        return 0;
    }

    /**
     * runtime error for not matching capture tags
     *
     */
    public function capture_error()
    {
        throw new SmartyRuntimeException("Not matching {capture} open/close", $this);
    }

    /**
     * Get Template Configuration Information
     *
     * @param boolean $html  return formatted HTML, array else
     * @param integer $flags see Smarty_Internal_Info constants
     * @return string|array configuration information
     */
    public function info($html=true, $flags=0)
    {
        $info = new Smarty_Internal_Info($this->smarty, $this);
        return $html ? $info->getHtml($flags) : $info->getArray($flags);
    }

    /**
    * Empty cache for this template
    *
    * @param integer $exp_time      expiration time
    * @return integer number of cache files deleted
    */
    public function clearCache($exp_time=null)
    {
        Smarty_CacheResource::invalidLoadedCache($this->smarty);
        return $this->cached->handler->clear($this->smarty, $this->template_name, $this->cache_id, $this->compile_id, $exp_time);
    }

    /**
     * [util function] to use either var_export or unserialize/serialize to generate code for the
     * cachevalue optionflag of {assign} tag
     *
     * @param mixed $var Smarty variable value
     * @return string PHP inline code
     */
    public function _export_cache_value($var) {
        if (is_int($var) || is_float($var) || is_bool($var) || is_string($var) || (is_array($var) && !is_object($var) && !array_reduce($var, array($this,'_check_array_callback')))) { return var_export($var,true); }
        if (is_resource($var)) { throw new SmartyException('Cannot serialize resource'); }
        return 'unserialize(\'' . serialize($var) . '\')';
    }

    /**
     * callback used by _export_cache_value to check arrays recursively
     *
     * @param bool $flag status of previous elements
     * @param mixed $element array element to check
     * @return bool status
     */
    private function _check_array_callback($flag, $element) {
        if (is_resource($element)) { throw new SmartyException('Cannot serialize resource'); }
        $flag = $flag || is_object($element) || (!is_int($element) && !is_float($element) && !is_bool($element) && !is_string($element) && (is_array($element) && array_reduce($element, array($this,'_check_array_callback'))));
        return $flag;
    }

    /**
     * set Smarty property in template context
     *
     * @param string $property_name property name
     * @param mixed  $value         value
     */
    public function __set($property_name, $value)
    {
        switch ($property_name) {
            case 'source':
            case 'compiled':
            case 'cached':
            case 'compiler':
                $this->$property_name = $value;
                return;

            // FIXME: routing of template -> smarty attributes
            default:
                if (property_exists($this->smarty, $property_name)) {
                    $this->smarty->$property_name = $value;
                    return;
                }
        }

        throw new SmartyException("invalid template property '$property_name'.");
    }

    /**
     * get Smarty property in template context
     *
     * @param string $property_name property name
     */
    public function __get($property_name)
    {
        switch ($property_name) {
            case 'source':
                if (empty($this->template_resource)) {
                    throw new SmartyException("Unable to parse resource name \"{$this->template_resource}\"");
                }
                $this->source = Smarty_Resource::source($this);
                // cache template object under a unique ID
                // do not cache eval resources
                if ($this->source->type != 'eval') {
                    if ($this->smarty->allow_ambiguous_resources) {
                        $_templateId = $this->source->unique_resource . $this->cache_id . $this->compile_id;
                    } else {
                        $_templateId = $this->smarty->joined_template_dir . '#' . $this->template_resource . $this->cache_id . $this->compile_id;
                    }

                    if (isset($_templateId[150])) {
                        $_templateId = sha1($_templateId);
                    }
                    $this->smarty->template_objects[$_templateId] = $this;
                }
                return $this->source;

            case 'compiled':
                // check runtime cache
                $_cache_key = $this->source->unique_resource . '#' . $this->compile_id;
                if (isset(Smarty_Compiled::$compileds[$_cache_key])) {
                    $this->compiled =  Smarty_Compiled::$compileds[$_cache_key];
                } else {
                    $this->compiled = Smarty_Compiled::$compileds[$_cache_key] = new Smarty_Compiled($this);
                }
                return $this->compiled;

            case 'cached':
                $this->cached = new Smarty_Template_Cached($this);
                return $this->cached;

            case 'compiler':
                $this->smarty->loadPlugin($this->source->compiler_class);
                $this->compiler = new $this->source->compiler_class($this->source->template_lexer_class, $this->source->template_parser_class, $this->smarty);
                return $this->compiler;

            // FIXME: routing of template -> smarty attributes
            default:
                if (property_exists($this->smarty, $property_name)) {
                    return $this->smarty->$property_name;
                }
        }

        throw new SmartyException("template property '$property_name' does not exist.");
    }

    /**
     * Template data object destrutor
     *
     */
    public function __destruct()
    {
        if ($this->smarty->cache_locking && isset($this->cached) && $this->cached->is_locked) {
            $this->cached->handler->releaseLock($this->smarty, $this->cached);
        }
    }

}

?>