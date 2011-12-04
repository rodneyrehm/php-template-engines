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
    * configuration settings
    *
    * @var array
    */
    public $config_vars = array();

    /**
    * set up variable containers
    */
    function __construct ()
    {
        $this->tpl_vars = new Smarty_Variable_Container();
        $this->tpl_vars->__smarty__data = $this;
        $this->config_vars = new Smarty_Config_Variable_Container();
        $this->config_vars->__smarty__data = $this;
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
    public function assign($tpl_var, $value = null, $nocache = false)
    {
        if (is_array($tpl_var)) {
            foreach ($tpl_var as $_key => $_val) {
                if ($_key != '') {
                    $this->tpl_vars->$_key = new Smarty_variable($_val, $nocache);
                }
            }
        } else {
            if ($tpl_var != '') {
                $this->tpl_vars->$tpl_var = new Smarty_variable($value, $nocache);
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
    public function assignGlobal($varname, $value = null, $nocache = false)
    {
        if ($varname != '') {
            Smarty::$global_tpl_vars->$varname = new Smarty_variable($value, $nocache);
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
    public function assignByRef($tpl_var, &$value, $nocache = false)
    {
        if ($tpl_var != '') {
            $this->tpl_vars->$tpl_var = new Smarty_variable(null, $nocache);
            $this->tpl_vars->$tpl_var->value = &$value;
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
    public function append($tpl_var, $value = null, $merge = false, $nocache = false)
    {
        if (is_array($tpl_var)) {
            // $tpl_var is an array, ignore $value
            foreach ($tpl_var as $_key => $_val) {
                if ($_key != '') {
                    if (!isset($this->tpl_vars->$_key)) {
                        $tpl_var_inst = $this->getVariable($_key, null, true, false);
                        if ($tpl_var_inst instanceof Undefined_Smarty_Variable) {
                            $this->tpl_vars->$_key = new Smarty_variable(null, $nocache);
                        } else {
                            $this->tpl_vars->$_key = clone $tpl_var_inst;
                        }
                    }
                    if (!(is_array($this->tpl_vars->$_key->value) || $this->tpl_vars->$_key->value instanceof ArrayAccess)) {
                        settype($this->tpl_vars->$_key->value, 'array');
                    }
                    if ($merge && is_array($_val)) {
                        foreach($_val as $_mkey => $_mval) {
                            $this->tpl_vars->$_key->value[$_mkey] = $_mval;
                        }
                    } else {
                        $this->tpl_vars->$_key->value[] = $_val;
                    }
                }
            }
        } else {
            if ($tpl_var != '' && isset($value)) {
                if (!isset($this->tpl_vars->$tpl_var)) {
                    $tpl_var_inst = $this->getVariable($tpl_var, null, true, false);
                    if ($tpl_var_inst instanceof Undefined_Smarty_Variable) {
                        $this->tpl_vars->$tpl_var = new Smarty_variable(null, $nocache);
                    } else {
                        $this->tpl_vars->$tpl_var = clone $tpl_var_inst;
                    }
                }
                if (!(is_array($this->tpl_vars->$tpl_var->value) || $this->tpl_vars->$tpl_var->value instanceof ArrayAccess)) {
                    settype($this->tpl_vars->$tpl_var->value, 'array');
                }
                if ($merge && is_array($value)) {
                    foreach($value as $_mkey => $_mval) {
                        $this->tpl_vars->$tpl_var->value[$_mkey] = $_mval;
                    }
                } else {
                    $this->tpl_vars->$tpl_var->value[] = $value;
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
    public function appendByRef($tpl_var, &$value, $merge = false)
    {
        if ($tpl_var != '' && isset($value)) {
            if (!isset($this->tpl_vars->$tpl_var)) {
                $this->tpl_vars->$tpl_var = new Smarty_variable();
            }
            if (!@is_array($this->tpl_vars->$tpl_var->value)) {
                settype($this->tpl_vars->$tpl_var->value, 'array');
            }
            if ($merge && is_array($value)) {
                foreach($value as $_key => $_val) {
                    $this->tpl_vars->$tpl_var->value[$_key] = &$value[$_key];
                }
            } else {
                $this->tpl_vars->$tpl_var->value[] = &$value;
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
    public function clearAssign($tpl_var)
    {
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
    public function clearAllAssign()
    {
        $this->tpl_vars = new Smarty_Variable_Container();
        $this->tpl_vars->__smarty__data = $this;
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
    public function getTemplateVars($varname = null, $_ptr = null, $search_parents = true)
    {
        if (isset($varname)) {
            $_var = $this->getVariable($varname, $_ptr, $search_parents, false);
            if (is_object($_var)) {
                return $_var->value;
            } else {
                return null;
            }
        } else {
            $_result = array();
            if ($_ptr === null) {
                $_ptr = $this;
            } while ($_ptr !== null) {
                foreach ($_ptr->tpl_vars AS $key => $var) {
                    if ($key != '__smarty__data' && !array_key_exists($key, $_result)) {
                        $_result[$key] = $var->value;
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
                foreach (Smarty::$global_tpl_vars AS $key => $var) {
                    if ($key != '__smarty__data' && !array_key_exists($key, $_result)) {
                        $_result[$key] = $var->value;
                    }
                }
            }
            return $_result;
        }
    }
    /**
    * gets the object of a template variable
    *
    * @param string  $variable the name of the Smarty variable
    * @param object  $_ptr     optional pointer to data object
    * @param boolean $search_parents search also in parent data
    * @param boolean $error_enable enable error handling
    * @return object the object of the variable
    */
    public function getVariable($_variable, $_ptr = null, $search_parents = true, $error_enable = true)
    {
        if ($_ptr === null) {
            $_ptr = $this;
        }
        while ($_ptr !== null) {
            if (isset($_ptr->tpl_vars->$_variable)) {
                // found it, return it
                return $_ptr->tpl_vars->$_variable;
            }
            // not found, try at parent
            if ($search_parents) {
                $_ptr = $_ptr->parent;
            } else {
                $_ptr = null;
            }
        }
        if (isset(Smarty::$global_tpl_vars->$_variable)) {
            // found it, return it
            return Smarty::$global_tpl_vars->$_variable;
        }
        if (isset($this->smarty->default_variable_handler_func)) {
            $value = null;
            if (call_user_func_array($this->smarty->default_variable_handler_func,array($_variable, &$value, $this))) {
                return new Smarty_Variable($value);
            }
        }
        if ($this->smarty->error_unassigned != Smarty::UNASSIGNED_IGNORE && $error_enable) {
            $err_msg = "Unassigned template variable '{$_variable}'";
            if ($this->smarty->error_unassigned == Smarty::UNASSIGNED_NOTICE) {
                // force a notice
                trigger_error($err_msg);
            } elseif ($this->smarty->error_unassigned == Smarty::UNASSIGNED_EXCEPTION) {
                throw new SmartyRunTimeException($err_msg, $this);
            }
        }
        return new Undefined_Smarty_Variable;
    }
    /**
    * Returns a single or all config variables
    *
    * @param string $varname variable name or null
    * @return string variable value or or array of variables
    */
    public function getConfigVars($varname = null, $search_parents = true)
    {
        if (isset($varname)) {
            return $this->getConfigVariable($varname, $this, $search_parents, false);
        } else {
            $_result = array();
            $_ptr = $this;
            while ($_ptr !== null) {
                foreach ($_ptr->config_vars AS $key => $value) {
                    if ($key != '__smarty__data' && !array_key_exists($key, $_result)) {
                        $_result[$key] = $value;
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
    * gets value of config variable
    *
    * @param string  $variable the name of the Smarty variable
    * @param object  $_ptr     optional pointer to data object
    * @param boolean $search_parents search also in parent data
    * @param boolean $error_enable enable error handling
    * @return object the object of the variable
    */
    public function getConfigVariable($_variable, $_ptr = null, $search_parents = true, $error_enable = true)
    {
        if ($_ptr === null) {
            $_ptr = $this;
        }
        while ($_ptr !== null) {
            if (isset($_ptr->config_vars->$_variable)) {
                // found it, return it
                return $_ptr->config_vars->$_variable;
            }
            // not found, try at parent
            if ($search_parents) {
                $_ptr = $_ptr->parent;
            } else {
                $_ptr = null;
            }
        }
        if (isset($this->smarty->default_config_variable_handler_func)) {
            $value = null;
            if (call_user_func_array($this->smarty->default_config_variable_handler_func,array($_variable, &$value, $this))) {
                return $value;
            }
        }
        if ($this->smarty->error_unassigned != Smarty::UNASSIGNED_IGNORE && $error_enable) {
            $err_msg = "Unassigned config variable '{$_variable}'";
            if ($this->smarty->error_unassigned == Smarty::UNASSIGNED_NOTICE) {
                // force a notice
                trigger_error($err_msg);
            } elseif ($this->smarty->error_unassigned == Smarty::UNASSIGNED_EXCEPTION) {
                throw new SmartyRunTimeException($err_msg, $this);
            }
        }
        return null;
    }

    /**
    * Deassigns a single or all config variables
    *
    * @param string $varname variable name or null
    * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for chaining
    */
    public function clearConfig($varname = null)
    {
        if (isset($varname)) {
            unset($this->config_vars->$varname);
        } else {
            foreach($this->config_vars as $key => $var) {
                if ($key != '__smarty__data') {
                    unset($this->config_vars->$key);
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
    public function configLoad($config_file, $sections = null)
    {
        // load Config class
        $config = new Smarty_Internal_Config($config_file, $this->smarty, $this);
        $config->loadConfigVars($sections);
        return $this;
    }

    /**
    * gets  a stream variable
    *
    * @param string $variable the stream of the variable
    * @return mixed the value of the stream variable
    */
    public function getStreamVariable($variable)
    {
        $_result = '';
        $fp = fopen($variable, 'r+');
        if ($fp) {
            while (!feof($fp) && ($current_line = fgets($fp)) !== false ) {
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
    public function __construct ($_parent = null, $smarty = null)
    {
        parent::__construct();
        $this->smarty = $smarty;
        if (is_object($_parent)) {
            // when object set up back pointer
            $this->parent = $_parent;
        } elseif (is_array($_parent)) {
            // set up variable values
            foreach ($_parent as $_key => $_val) {
                $this->tpl_vars->$_key = new Smarty_variable($_val);
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
    // pointer to parent object
    public $__smarty__data = null;

    /**
    *
    * magic __get function to get variable not know in current scope
    */
    public function __get($_variable)
    {
        return $this->$_variable = $this->__smarty__data->getVariable($_variable, $this->__smarty__data->parent);
    }
}

/**
* class for the Smarty config variable container
*
* This class holds all loaded config variables
*/
class Smarty_Config_Variable_Container {
    // pointer to parent object
    public $__smarty__data = null;

    /**
    *
    * magic __get function to get variable not know in current scope
    */
    public function __get($_variable)
    {
        return $this->$_variable = $this->__smarty__data->getConfigVariable($_variable, $this->__smarty__data->parent);
    }
}

/**
* class for the Smarty variable object
*
* This class defines the Smarty variable object
*
* @package Smarty
* @subpackage Template
*/
class Smarty_Variable {

    /**
    * template variable
    *
    * @var mixed
    */
    public $value = null;
    /**
    * if true any output of this variable will be not cached
    *
    * @var boolean
    */
    public $nocache = false;

    /**
    * create Smarty variable object
    *
    * @param mixed   $value   the value to assign
    * @param boolean $nocache if true any output of this variable will be not cached
    */
    public function __construct($value = null, $nocache = false)
    {
        $this->value = $value;
        $this->nocache = $nocache;
    }

    /**
    * <<magic>> String conversion
    *
    * @return string
    */
    public function __toString()
    {
        return (string) $this->value;
    }

}

/**
* class for undefined variable object
*
* This class defines an object for undefined variable handling
*
* @package Smarty
* @subpackage Template
*/
class Undefined_Smarty_Variable {
    public $value = null;
    public $nocache = false;
}

?>