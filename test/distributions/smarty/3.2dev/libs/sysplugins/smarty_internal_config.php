<?php

/**
 * Smarty Internal Plugin Config
 *
 * @package Smarty
 * @subpackage Config
 * @author Uwe Tews
 */

/**
 * Smarty Internal Plugin Config
 *
 * Main class for config variables
 *
 * @package Smarty
 * @subpackage Config
 *
 * @property Smarty_Config_Source   $source
 * @property Smarty_Config_Compiled $compiled
 * @ignore
 */
class Smarty_Internal_Config {

    /**
     * Samrty instance
     *
     * @var Smarty object
     */
    public $smarty = null;

    /**
     * Object of config var storage
     *
     * @var object
     */
    public $parent = null;

    /**
     * Config resource
     * @var string
     */
    public $config_resource = null;

    /**
     * Compiled config file
     *
     * @var string
     */
    public $compiled_config = null;

    /**
     * filepath of compiled config file
     *
     * @var string
     */
    public $compiled_filepath = null;

    /**
     * Filemtime of compiled config Filemtime
     *
     * @var int
     */
    public $compiled_timestamp = null;

    /**
     * flag if compiled config file is invalid and must be (re)compiled
     * @var bool
     */
    public $mustCompile = null;

    /**
     * Config file compiler object
     *
     * @var Smarty_Internal_Config_File_Compiler object
     */
    public $compiler_object = null;

    /**
     * $compile_id
     * @var string
     * @link http://www.smarty.net/docs/en/variable.compile.id.tpl
     */
    public $compile_id = null;

    /**
     * $compiletime_options
     * value is computed of the compiletime options relevant for config files
     *      $config_read_hidden
     *      $config_booleanize
     *      $config_overwrite
     *
     * @var int
     */
    public $compiletime_options = 0;

    /**
     * Constructor of config file object
     *
     * @param string $config_resource config file resource name
     * @param Smarty $smarty Smarty instance
     * @param object $parent object where config vars should be stored
     */
    public function __construct($config_resource, $smarty, $parent = null) {
        $this->parent = $parent;
        $this->smarty = $smarty;
        $this->compile_id = $smarty->compile_id;
        $this->config_resource = $config_resource;
        $this->compiletime_options = (int) $this->smarty->config_read_hidden + (int) $this->smarty->config_booleanize * 2
                + (int) $this->smarty->config_overwrite * 4;
    }

    /**
     * Returns if the current config file must be compiled
     *
     * It does compare the timestamps of config source and the compiled config and checks the force compile configuration
     *
     * @return boolean true if the file must be compiled
     */
    public function mustCompile() {
        if (!$this->source->exists) {
            $msg = "Unable to load config file '{$this->source->type}:{$this->source->name}'";
            if ($this->parent instanceof Smarty_Internal_Template) {
                throw new SmartyRunTimeException($msg, $this->parent);
            } else {
                throw new SmartyException($msg);
            }
        }
        if ($this->mustCompile === null) {
            $this->mustCompile = ($this->smarty->force_compile || $this->compiled->timestamp === false || $this->smarty->compile_check && $this->compiled->timestamp < $this->source->timestamp);
        }
        return $this->mustCompile;
    }

    /**
     * Returns the compiled config file
     *
     * It checks if the config file must be compiled or just read the compiled version
     *
     * @return string the compiled config file
     */
    public function getCompiledConfig() {
        if ($this->compiled_config === null) {
            // see if template needs compiling.
            if ($this->mustCompile()) {
                $this->compileConfigSource();
            } else {
                $this->compiled_config = file_get_contents($this->compiled->filepath);
            }
        }
        return $this->compiled_config;
    }

    /**
     * Compiles the config files
     *
     * @throws Exception
     */
    public function compileConfigSource() {
        // compile template
        if (!is_object($this->compiler_object)) {
            // load compiler
            $this->compiler_object = new Smarty_Internal_Config_File_Compiler($this->smarty);
        }
        // compile locking
        if ($this->smarty->compile_locking) {
            if ($saved_timestamp = $this->compiled->timestamp) {
                touch($this->compiled->filepath);
            }
        }
        // call compiler
        try {
            $this->compiler_object->compileSource($this);
        } catch (Exception $e) {
            // restore old timestamp in case of error
            if ($this->smarty->compile_locking && $saved_timestamp) {
                touch($this->compiled->filepath, $saved_timestamp);
            }
            throw $e;
        }
        // compiling succeded
        // write compiled template
        Smarty_Internal_Write_File::writeFile($this->compiled->filepath, $this->getCompiledConfig(), $this->smarty);
    }

    /**
     * fetch config variables
     *
     * @param mixed $sections array of section names, single section or null
     * @param object $scope global,parent or local
     */
    public function fetch($sections = null, $scope = 'local') {
        if ($this->parent instanceof Smarty_Internal_Template) {
            $this->parent->properties['file_dependency'][sha1($this->source->filepath)] = array($this->source->filepath, $this->source->timestamp, 'file');
        }
        if ($this->mustCompile()) {
            $this->compileConfigSource();
        }
        // pointer to scope
        if ($scope == 'local') {
            $scope_ptr = $this->parent;
        } elseif ($scope == 'parent') {
            if (isset($this->parent->parent)) {
                $scope_ptr = $this->parent->parent;
            } else {
                $scope_ptr = $this->parent;
            }
        } elseif ($scope == 'root' || $scope == 'global') {
            $scope_ptr = $this->parent;
            while (isset($scope_ptr->parent)) {
                $scope_ptr = $scope_ptr->parent;
            }
        }
        $_config_vars = array();
        include($this->compiled->filepath);
        // copy global config vars
        foreach ($_config_vars['vars'] as $variable => $value) {
            if ($this->smarty->config_overwrite || !isset($scope_ptr->tpl_vars->$variable)) {
                $scope_ptr->tpl_vars->{$variable} = array('value' => $value);
            } else {
                $scope_ptr->tpl_vars->{$variable} = array('value' => array_merge((array) $scope_ptr->tpl_vars->{$variable}['value'], (array) $value));
            }
        }
        // scan sections
        if (!empty($sections)) {
            $sections = array_flip((array) $sections);
            foreach ($_config_vars['sections'] as $this_section => $dummy) {
                if (isset($sections[$this_section])) {
                    foreach ($_config_vars['sections'][$this_section]['vars'] as $variable => $value) {
                        if ($this->smarty->config_overwrite || !isset($scope_ptr->tpl_vars->$variable)) {
                            $scope_ptr->tpl_vars->{$variable} = array('value' => $value);
                        } else {
                            $scope_ptr->tpl_vars->{$variable}['value'] = array('value' => array_merge((array) $scope_ptr->tpl_vars->{$variable}['value'], (array) $value));
                        }
                    }
                }
            }
        }
    }

    /**
     * set Smarty property in template context
     *
     * @param string $property_name property name
     * @param mixed  $value         value
     * @throws SmartyException if $property_name is not valid
     */
    public function __set($property_name, $value) {
        switch ($property_name) {
            case 'source':
            case 'compiled':
                $this->$property_name = $value;
                return;
        }
    }

    /**
     * get Smarty property in template context
     *
     * @param string $property_name property name
     * @throws SmartyException if $property_name is not valid
     */
    public function __get($property_name) {
        switch ($property_name) {
            case 'source':
                if (empty($this->config_resource)) {
                    throw new SmartyException("Unable to parse resource name \"{$this->config_resource}\"");
                }
                $this->source = Smarty_Resource::config($this);
                return $this->source;

            case 'compiled':
                // check runtime cache
                $_cache_key = $this->source->unique_resource . '#' . $this->compiletime_options;
                if (isset(Smarty_Compiled::$compileds[$_cache_key])) {
                    $this->compiled = Smarty_Compiled::$compileds[$_cache_key];
                } else {
                    $this->compiled = Smarty_Compiled::$compileds[$_cache_key] = new Smarty_Compiled($this);
                }
                return $this->compiled;
        }
        // we should never come here
        return null;
    }

}
