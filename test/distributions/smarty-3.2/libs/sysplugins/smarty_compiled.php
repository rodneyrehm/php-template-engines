<?php
/**
* Smarty Compiled Resource Plugin
*
* @package Smarty
* @subpackage CompiledResources
* @author Uwe Tews
*/
/**
* Meta Data Container for Compiled Template Files
*
*
* @property string $content compiled content
*/
class Smarty_Compiled {

    /**
    * Compiled Filepath
    * @var string
    */
    public $filepath = null;

    /**
    * Compiled Timestamp
    * @var integer
    */
    public $timestamp = null;

    /**
    * Compiled Existance
    * @var boolean
    */
    public $exists = false;

    /**
    * Compiled Content Loaded
    * @var boolean
    */
    public $loaded = false;

    /**
    * Template was compiled
    * @var boolean
    */
    public $isCompiled = false;

    /**
    * Source Object
    * @var Smarty_Template_Source
    */
    public $source = null;

    /**
    * cache for Smarty_Compiled instances
    * @var array
    */
    public static $compileds = array();

    /**
    * Metadata properties
    *
    * populated by Smarty_Internal_Template::decodeProperties()
    * @var array
    */
    public $_properties = null;

    /**
    * create Compiled Object container
    *
    * @param Smarty__Internal_Template $_template template object this compiled object belongs to
    * @param Smarty_Template_Source   $source    source object
    */
    public function __construct(Smarty_Internal_Template $_template)
    {
        $this->source = $_template->source;
        $this->source->handler->populateCompiledFilepath($this, $_template);
        $this->timestamp = @filemtime($this->filepath);
        $this->exists = !!$this->timestamp;
    }

    /**
    * get rendered template output from compiled template
    *
    * @param Smarty__Internal_Template or Smarty $obj object of caller
    */
    public function getRenderedTemplate($obj, $_template) {
        $_template->cached_subtemplates = array();
        if (!$this->source->uncompiled) {
            $_smarty_tpl = $_template;
            if ($this->source->recompiled) {
                if ($obj->smarty->debugging) {
                    Smarty_Internal_Debug::start_compile($_template);
                }
                $code = $_template->compiler->compileTemplate($_template);
                if ($obj->smarty->debugging) {
                    Smarty_Internal_Debug::end_compile($_template);
                }
                if ($obj->smarty->debugging) {
                    Smarty_Internal_Debug::start_render($_template);
                }
                try {
                    ob_start();
                    eval("?>" . $code);
                    unset($code);
                } catch (Exception $e) {
                    ob_get_clean();
                    throw $e;
                }
            } else {
                if (!$this->exists || ($_template->smarty->force_compile && !$this->isCompiled)) {
                    $_template->compiler->compileTemplateSource($_template);
                    unset($_template->compiler);
                }
                if ($obj->smarty->debugging) {
                    Smarty_Internal_Debug::start_render($_template);
                }
                if (!$this->loaded) {
                    include($this->filepath);
                    if ($_template->mustCompile) {
                        // recompile and load again
                        $_template->compiler->compileTemplateSource($_template);
                        unset($_template->compiler);
                        include($this->filepath);
                    }
                    $this->loaded = true;
                } else {
                    $_template->decodeProperties($this->_properties, false);
                }
                try {
                    ob_start();
                    if (empty($_template->properties['unifunc']) || !is_callable($_template->properties['unifunc'])) {
                        throw new SmartyException("Invalid compiled template for '{$_template->template_resource}'");
                    }
                    $_template->properties['unifunc']($_template);
                    if (isset($_template->_capture_stack[0])) {
                        $_template->capture_error();
                    }
                } catch (Exception $e) {
                    ob_get_clean();
                    throw $e;
                }
            }
        } else {
            if ($this->source->uncompiled) {
                if ($obj->smarty->debugging) {
                    Smarty_Internal_Debug::start_render($_template);
                }
                try {
                    ob_start();
                    $this->source->renderUncompiled($_template);
                } catch (Exception $e) {
                    ob_get_clean();
                    throw $e;
                }
            } else {
                throw new SmartyException("Resource '$this->source->type' must have 'renderUncompiled' method");
            }
        }
        $output = ob_get_clean();
        if (!$this->source->recompiled && empty($_template->properties['file_dependency'][$this->source->uid])) {
            $_template->properties['file_dependency'][$this->source->uid] = array($this->source->filepath, $this->source->timestamp, $this->source->type);
        }
        if ($_template->parent instanceof Smarty_Internal_Template) {
            $_template->parent->properties['file_dependency'] = array_merge($_template->parent->properties['file_dependency'], $_template->properties['file_dependency']);
            foreach ($_template->required_plugins as $code => $tmp1) {
                foreach ($tmp1 as $name => $tmp) {
                    foreach ($tmp as $type => $data) {
                        $_template->parent->required_plugins[$code][$name][$type] = $data;
                    }
                }
            }
        }
        if ($_template->caching != Smarty::CACHING_LIFETIME_SAVED && $_template->caching != Smarty::CACHING_LIFETIME_CURRENT) {
            // var_dump('renderTemplate', $_template->has_nocache_code, $_template->template_resource, $_template->properties['nocache_hash'], $_template->parent->properties['nocache_hash'], $_output);
            if (!empty($_template->properties['nocache_hash']) && !empty($_template->parent->properties['nocache_hash'])) {
                // replace nocache_hash
                $output = preg_replace("/{$_template->properties['nocache_hash']}/", $_template->parent->properties['nocache_hash'], $output);
                $_template->parent->has_nocache_code = $_template->parent->has_nocache_code || $_template->has_nocache_code;
            }
        }
        if ($obj->smarty->debugging) {
            Smarty_Internal_Debug::end_render($_template);
        }
        return  $output;
    }

    /**
    * Delete compiled template file
    *
    * @param string  $resource_name template name
    * @param string  $compile_id    compile id
    * @param integer $exp_time      expiration time
    * @param Smarty  $smarty        Smarty instance
    * @return integer number of template files deleted
    */
    public static function clearCompiledTemplate($resource_name, $compile_id, $exp_time, Smarty $smarty)
    {
        $_compile_dir = $smarty->getCompileDir();
        $_compile_id = isset($compile_id) ? preg_replace('![^\w\|]+!', '_', $compile_id) : null;
        $_dir_sep = $smarty->use_sub_dirs ? DS : '^';
        if (isset($resource_name)) {
            $source = Smarty_Resource::source(null, $smarty, $resource_name);

            if ($source->exists) {
                // set basename if not specified
                $_basename = $source->handler->getBasename($source);
                if ($_basename === null) {
                    $_basename = basename( preg_replace('![^\w\/]+!', '_', $source->name) );
                }
                // separate (optional) basename by dot
                if ($_basename) {
                    $_basename = '.' . $_basename;
                }
                $_resource_part_1 = $source->uid . '.' . $source->type . $_basename . '.php';
                $_resource_part_1_length = strlen($_resource_part_1);
            } else {
                return 0;
            }

            $_resource_part_2 = str_replace('.php','.cache.php',$_resource_part_1);
            $_resource_part_2_length = strlen($_resource_part_2);
        } else {
            $_resource_part = '';
        }
        $_dir = $_compile_dir;
        if ($smarty->use_sub_dirs && isset($_compile_id)) {
            $_dir .= $_compile_id . $_dir_sep;
        }
        if (isset($_compile_id)) {
            $_compile_id_part = $_compile_dir . $_compile_id . $_dir_sep;
        }
        $_count = 0;
        try {
            $_compileDirs = new RecursiveDirectoryIterator($_dir);
            // NOTE: UnexpectedValueException thrown for PHP >= 5.3
        } catch (Exception $e) {
            return 0;
        }
        $_compile = new RecursiveIteratorIterator($_compileDirs, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($_compile as $_file) {
            if (substr($_file->getBasename(), 0, 1) == '.' || strpos($_file, '.svn') !== false)
            continue;

            $_filepath = (string) $_file;

            if ($_file->isDir()) {
                if (!$_compile->isDot()) {
                    // delete folder if empty
                    @rmdir($_file->getPathname());
                }
            } else {
                $unlink = false;
                if ((!isset($_compile_id) || strpos($_filepath, $_compile_id_part) === 0)
                && (!isset($resource_name)
                || (isset($_filepath[$_resource_part_1_length])
                && substr_compare($_filepath, $_resource_part_1, -$_resource_part_1_length, $_resource_part_1_length) == 0)
                || (isset($_filepath[$_resource_part_2_length])
                && substr_compare($_filepath, $_resource_part_2, -$_resource_part_2_length, $_resource_part_2_length) == 0))) {
                    if (isset($exp_time)) {
                        if (time() - @filemtime($_filepath) >= $exp_time) {
                            $unlink = true;
                        }
                    } else {
                        $unlink = true;
                    }
                }

                if ($unlink && @unlink($_filepath)) {
                    $_count++;
                }
            }
        }
        // clear compiled cache
        Smarty_Compiled::$compileds = array();
        return $_count;
    }
}
?>