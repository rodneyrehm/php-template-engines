<?php

/**
 * Smarty Internal Plugin Data
 *
 * This file contains the basic classes and methodes for template and variable creation
 *
 * @package Smarty
 * @subpackage Template
 * @author Uwe Tews
 */

/**
 * Base class with template and variable methodes
 *
 * @package Smarty
 * @subpackage Template
 */
class Smarty_Internal_Data {

    /**
     * name of class used for templates
     *
     * @var string
     */
    public $template_class = 'Smarty_Internal_Template';

    /**
     * template variables
     *
     * @var array
     */
    public $tpl_vars = null;

    /**
     * parent template (if any)
     *
     * @var Smarty_Internal_Template
     */
    public $parent = null;

    /**
     * set up variable containers
     */
    function __construct() {
        $this->tpl_vars = new Smarty_Variable_Container($this);
    }

    /**
     * assigns a Smarty variable
     *
     * @param array|string $tpl_var the template variable name(s)
     * @param mixed        $value   the value to assign
     * @param boolean      $nocache if true any output of this variable will be not cached
     * @param boolean $scope the scope the variable will have  (local,parent or root)
     * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for chaining
     */
    public function assign($tpl_var, $value = null, $nocache = false) {
        $data = array();
        if ($nocache) {
            $data['nocache'] = true;
        }
        if (is_array($tpl_var)) {
            foreach ($tpl_var as $varname => $value) {
                if ($varname != '') {
                    $data['value'] = $value;
                    $this->tpl_vars->$varname = $data;
                }
            }
        } else {
            if ($tpl_var != '') {
                $data['value'] = $value;
                $this->tpl_vars->$tpl_var = $data;
            }
        }
        return $this;
    }

    /**
     * assigns a global Smarty variable
     *
     * @param string $varname the global variable name
     * @param mixed  $value   the value to assign
     * @param boolean $nocache if true any output of this variable will be not cached
     * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for chaining
     */
    public function assignGlobal($varname, $value = null, $nocache = false) {
        $data = array();
        if ($nocache) {
            $data['nocache'] = true;
        }
        if ($varname != '') {
            $data['value'] = $value;
            Smarty::$global_tpl_vars->$varname = $data;
        }

        return $this;
    }

    /**
     * assigns values to template variables by reference
     *
     * @param string $tpl_var the template variable name
     * @param mixed $ &$value the referenced value to assign
     * @param boolean $nocache if true any output of this variable will be not cached
     * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for chaining
     */
    public function assignByRef($tpl_var, &$value, $nocache = false) {
        $data = array();
        if ($nocache) {
            $data['nocache'] = true;
        }
        if ($tpl_var != '') {
            $data['value'] = &$value;
            $this->tpl_vars->$tpl_var = $data;
        }

        return $this;
    }

    /**
     * appends values to template variables
     *
     * @param array|string $tpl_var the template variable name(s)
     * @param mixed        $value   the value to append
     * @param boolean      $merge   flag if array elements shall be merged
     * @param boolean $nocache if true any output of this variable will be not cached
     * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for chaining
     */
    public function append($tpl_var, $value = null, $merge = false, $nocache = false) {
        $data = array();
        if ($nocache) {
            $data['nocache'] = true;
        }
        if (is_array($tpl_var)) {
            // $tpl_var is an array, ignore $value
            foreach ($tpl_var as $varname => $value) {
                if ($varname != '') {
                    if (!isset($this->tpl_vars->$varname)) {
                        $this->tpl_vars->$varname = $this->getVariable($varname, null, true, false);
                        if ($this->tpl_vars->$varname === null) {
                            $data['value'] = array();
                            $this->tpl_vars->$varname = $data;
                        }
                    }
                    if (!(is_array($this->tpl_vars->{$varname}['value']) || $this->tpl_vars->{$varname}['value'] instanceof ArrayAccess)) {
                        settype($this->tpl_vars->{$varname}['value'], 'array');
                    }
                    if ($merge && is_array($value)) {
                        foreach ($value as $_mkey => $_mval) {
                            $this->tpl_vars->{$varname}['value'][$_mkey] = $_mval;
                        }
                    } else {
                        $this->tpl_vars->{$varname}['value'][] = $value;
                    }
                }
            }
        } else {
            if ($tpl_var != '' && isset($value)) {
                if (!isset($this->tpl_vars->$tpl_var)) {
                    $this->tpl_vars->$tpl_var = $this->getVariable($tpl_var, null, true, false);
                    if ($this->tpl_vars->$tpl_var === null) {
                        $data['value'] = array();
                        $this->tpl_vars->$tpl_var = $data;
                    }
                }
                if (!(is_array($this->tpl_vars->{$tpl_var}['value']) || $this->tpl_vars->{$tpl_var}['value'] instanceof ArrayAccess)) {
                    settype($this->tpl_vars->{$tpl_var}['value'], 'array');
                }
                if ($merge && is_array($value)) {
                    foreach ($value as $_mkey => $_mval) {
                        $this->tpl_vars->{$tpl_var}['value'][$_mkey] = $_mval;
                    }
                } else {
                    $this->tpl_vars->{$tpl_var}['value'][] = $value;
                }
            }
        }
        return $this;
    }

    /**
     * appends values to template variables by reference
     *
     * @param string $tpl_var the template variable name
     * @param mixed  &$value  the referenced value to append
     * @param boolean $merge  flag if array elements shall be merged
     * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for chaining
     */
    public function appendByRef($tpl_var, &$value, $merge = false) {
        if ($tpl_var != '' && isset($value)) {
            if (!isset($this->tpl_vars->$tpl_var)) {
                $this->tpl_vars->$tpl_var = array('value' => array());
            }
            if (!@is_array($this->tpl_vars->{$tpl_var}['value'])) {
                settype($this->tpl_vars->{$tpl_var}['value'], 'array');
            }
            if ($merge && is_array($value)) {
                foreach ($value as $_key => $_val) {
                    $this->tpl_vars->{$tpl_var}['value'][$_key] = &$value[$_key];
                }
            } else {
                $this->tpl_vars->{$tpl_var}['value'][] = &$value;
            }
        }
        return $this;
    }

    /**
     * clear the given assigned template variable.
     *
     * @param string|array $tpl_var the template variable(s) to clear
     * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for chaining
     */
    public function clearAssign($tpl_var) {
        if (is_array($tpl_var)) {
            foreach ($tpl_var as $curr_var) {
                unset($this->tpl_vars->$curr_var);
            }
        } else {
            unset($this->tpl_vars->$tpl_var);
        }

        return $this;
    }

    /**
     * clear all the assigned template variables.
     * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for chaining
     */
    public function clearAllAssign() {
        $this->tpl_vars = new Smarty_Variable_Container($this);
        return $this;
    }

    /**
     * Returns a single or all template variables
     *
     * @param string  $varname        variable name or null
     * @param string  $_ptr           optional pointer to data object
     * @param boolean $search_parents include parent templates?
     * @return string variable value or or array of variables
     */
    public function getTemplateVars($varname = null, $_ptr = null, $search_parents = true) {
        if (isset($varname)) {
            $result = $this->getVariable($varname, $_ptr, $search_parents, false);
            if ($result === null) {
                return false;
            } else {
                return $result['value'];
            }
        } else {
            $_result = array();
            if ($_ptr === null) {
                $_ptr = $this;
            } while ($_ptr !== null) {
                foreach ($_ptr->tpl_vars AS $varname => $data) {
                    if (strpos($varname, '___') !== 0 && !array_key_exists($varname, $_result)) {
                        $_result[$varname] = $data['value'];
                    }
                }
                // not found, try at parent
                if ($search_parents) {
                    $_ptr = $_ptr->parent;
                } else {
                    $_ptr = null;
                }
            }
            if ($search_parents && isset(Smarty::$global_tpl_vars)) {
                foreach (Smarty::$global_tpl_vars AS $varname => $data) {
                    if (strpos($varname, '___') !== 0 && !array_key_exists($varname, $_result)) {
                        $_result[$varname] = $data['value'];
                    }
                }
            }
            return $_result;
        }
    }

    /**
     * gets the object of a template variable
     *
     * @param string  $varname the name of the Smarty variable
     * @param object  $_ptr     optional pointer to data object
     * @param boolean $search_parents search also in parent data
     * @param boolean $error_enable enable error handling
     * @param string  $proptery requested variable property
     * @return mixed  variable array|variable property
     */
    public function getVariable($varname, $_ptr = null, $search_parents = true, $error_enable = true, $property = null) {
        if ($_ptr === null) {
            $_ptr = $this;
        }
        while ($_ptr !== null) {
            if (isset($_ptr->tpl_vars->$varname)) {
                // found it, return it
                if ($property === null) {
                    return $_ptr->tpl_vars->$varname;
                } else {
                    if (isset($_ptr->tpl_vars->{$varname}[$property])) {
                        return $_ptr->tpl_vars->{$varname}[$property];
                    } else {
                        return null;
                    }
                }
            }
            // not found, try at parent
            if ($search_parents) {
                $_ptr = $_ptr->parent;
            } else {
                $_ptr = null;
            }
        }
        if (isset(Smarty::$global_tpl_vars->$varname)) {
            // found it, return it
            return Smarty::$global_tpl_vars->$varname;
        }
        if (strpos($varname, '___config_var_') !== 0) {
            if (isset($this->smarty->default_variable_handler_func)) {
                $value = null;
                if (call_user_func_array($this->smarty->default_variable_handler_func, array($varname, &$value, $this))) {
                    return array('value' => $value);
                }
            }
            if ($this->smarty->error_unassigned != Smarty::UNASSIGNED_IGNORE && $error_enable) {
                $err_msg = "Unassigned template variable '{$varname}'";
                if ($this->smarty->error_unassigned == Smarty::UNASSIGNED_NOTICE) {
                    // force a notice
                    trigger_error($err_msg);
                } elseif ($this->smarty->error_unassigned == Smarty::UNASSIGNED_EXCEPTION) {
                    throw new SmartyRunTimeException($err_msg, $this);
                }
            }
        } else {
            $real_varname = substr($varname, 14);
            if (isset($this->smarty->default_config_variable_handler_func)) {
                $value = null;
                if (call_user_func_array($this->smarty->default_config_variable_handler_func, array($real_varname, &$value, $this))) {
                    return array('value' => $value);
                }
            }
            if ($this->smarty->error_unassigned != Smarty::UNASSIGNED_IGNORE && $error_enable) {
                $err_msg = "Unassigned config variable '{$real_varname}'";
                if ($this->smarty->error_unassigned == Smarty::UNASSIGNED_NOTICE) {
                    // force a notice
                    trigger_error($err_msg);
                } elseif ($this->smarty->error_unassigned == Smarty::UNASSIGNED_EXCEPTION) {
                    throw new SmartyRunTimeException($err_msg, $this);
                }
            }
        }
        // unassigned variable which shall be ignored
        return null;
    }

    /**
     * Returns a single or all config variables
     *
     * @param string $varname variable name or null
     * @return string variable value or or array of variables
     */
    public function getConfigVars($varname = null, $search_parents = true) {
        $_ptr = $this;
        if (isset($varname)) {
            $result = $this->getVariable('___config_var_' . $varname, $_ptr, $search_parents, false);
            if ($result === null) {
                return false;
            } else {
                return $result['value'];
            }
        } else {
            $_result = array();
            while ($_ptr !== null) {
                foreach ($_ptr->tpl_vars AS $varname => $data) {
                    if (strpos($varname, '___config_var_') === 0 && !array_key_exists($real_varname = substr($varname, 14), $_result)) {
                        $_result[$real_varname] = $data['value'];
                    }
                }
                // not found, try at parent
                if ($search_parents) {
                    $_ptr = $_ptr->parent;
                } else {
                    $_ptr = null;
                }
            }
            return $_result;
        }
    }

    /**
     * Deassigns a single or all config variables
     *
     * @param string $varname variable name or null
     * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for chaining
     */
    public function clearConfig($varname = null) {
        if (isset($varname)) {
            unset($this->tpl_vars->{'___config_var_' . $varname});
        } else {
            foreach ($this->tpl_vars as $key => $var) {
                if (strpos($key, '___config_var_') === 0) {
                    unset($this->tpl_vars->$key);
                }
            }
        }
        return $this;
    }

    /**
     * load a config file, optionally load just selected sections
     *
     * @param string $config_file filename
     * @param mixed  $sections    array of section names, single section or null
     * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for chaining
     */
    public function configLoad($config_file, $sections = null) {
        // load Config class
        $config = new Smarty_Internal_Config($config_file, $this->smarty, $this);
        $config->fetch($sections);
        return $this;
    }

    /**
     * gets  a stream variable
     *
     * @param string $variable the stream of the variable
     * @return mixed the value of the stream variable
     */
    public function getStreamVariable($variable) {
        $_result = '';
        $fp = fopen($variable, 'r+');
        if ($fp) {
            while (!feof($fp) && ($current_line = fgets($fp)) !== false) {
                $_result .= $current_line;
            }
            fclose($fp);
            return $_result;
        }

        if ($this->smarty->error_unassigned) {
            throw new SmartyException('Undefined stream variable "' . $variable . '"');
        } else {
            return null;
        }
    }

}

/**
 * class for the Smarty data object
 *
 * The Smarty data object will hold Smarty variables in the current scope
 *
 * @package Smarty
 * @subpackage Template
 */
class Smarty_Data extends Smarty_Internal_Data {

    /**
     * Smarty object
     *
     * @var Smarty
     */
    public $smarty = null;

    /**
     * create Smarty data object
     *
     * @param Smarty|array $_parent  parent template
     * @param Smarty       $smarty   global smarty instance
     */
    public function __construct($_parent = null, $smarty = null) {
        parent::__construct();
        $this->smarty = $smarty;
        if (is_object($_parent)) {
            // when object set up back pointer
            $this->parent = $_parent;
        } elseif (is_array($_parent)) {
            // set up variable values
            foreach ($_parent as $_key => $_val) {
                $this->tpl_vars->$_key = array('value' => $_val);
            }
        } elseif ($_parent != null) {
            throw new SmartyException("Wrong type for template variables");
        }
    }

}

/**
 * class for the Smarty variable container
 *
 * This class holds all assigned variables
 */
class Smarty_Variable_Container {

    /**
     * constructor to create backlink to Smarty|Smarty_Internal_Template|Smarty_Data
     *
     * @param  Smarty|Smarty_Internal_Template|Smarty_Data $object  object this instance belongs to
     */
    public function __construct($object = null) {
        $this->___smarty__data = $object;
    }

    /**
     * magic __get function called at access of unknown variable
     *
     * @param string $varname  name of variable
     * @returns array  wariable properties
     */
    public function __get($varname) {
        return $this->$varname = $this->___smarty__data->getVariable($varname, $this->___smarty__data->parent);
    }

}
