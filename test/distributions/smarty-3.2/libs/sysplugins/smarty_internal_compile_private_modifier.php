<?php

/**
* Smarty Internal Plugin Compile Modifier
*
* Compiles code for modifier execution
*
* @package Smarty
* @subpackage Compiler
* @author Uwe Tews
*/

/**
* Smarty Internal Plugin Compile Modifier Class
*
* @package Smarty
* @subpackage Compiler
*/
class Smarty_Internal_Compile_Private_Modifier extends Smarty_Internal_CompileBase {

    /**
    * Compiles code for modifier execution
    *
    * @param array  $args      array with attributes from parser
    * @param object $compiler  compiler object
    * @param array  $parameter array with compilation parameter
    * @return string compiled code
    */
    public function compile($args, $compiler, $parameter)
    {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);
        $output = $parameter['value'];
        // loop over list of modifiers
        foreach ($parameter['modifierlist'] as $single_modifier) {
            $modifier = $single_modifier[0];
            $single_modifier[0] = $output;
            $params = implode(',', $single_modifier);
            // check for registered modifier
            if (isset($compiler->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER][$modifier])) {
                $function = $compiler->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER][$modifier][0];
                $object = $this->testParameter($modifier, $function, $single_modifier, $compiler);
                if (!is_array($function)) {
                    $output = "{$function}({$object}{$params})";
                } else {
                    if (is_object($function[0])) {
                        $output = '$_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER][\'' . $modifier . '\'][0][0]->' . $function[1] . '(' . $object . $params . ')';
                    } else {
                        $output = $function[0] . '::' . $function[1] . '(' . $object . $params . ')';
                    }
                }
            } else if (isset($compiler->smarty->registered_plugins[Smarty::PLUGIN_MODIFIERCOMPILER][$modifier][0])) {
                $output = call_user_func($compiler->smarty->registered_plugins[Smarty::PLUGIN_MODIFIERCOMPILER][$modifier][0], $single_modifier, $compiler->smarty);
                // check for plugin modifiercompiler
            } else if ($compiler->smarty->loadPlugin('smarty_modifiercompiler_' . $modifier)) {
                // check if modifier allowed
                if (!is_object($compiler->smarty->security_policy) || $compiler->smarty->security_policy->isTrustedModifier($modifier, $compiler)) {
                    $plugin = 'smarty_modifiercompiler_' . $modifier;
                    if ($this->getNoOfRequiredParameter($plugin) == 0) {
                        // NOTE: This covers the modifier like 'default' and 'cat' which can take any number of parameter
                        $output = call_user_func_array($plugin, $single_modifier);
                    } else {
                        $object = $this->testParameter($modifier, $plugin, $single_modifier, $compiler);
                        if ($object == '') {
                            $output = call_user_func_array($plugin, $single_modifier);
                        } else {
                            array_unshift($single_modifier, $object);
                            $output = call_user_func_array($plugin, $single_modifier);
                        }
                    }
                }
                // check for plugin modifier
            } else if ($function = $compiler->getPlugin($modifier, Smarty::PLUGIN_MODIFIER)) {
                $object = $this->testParameter($modifier, $function, $single_modifier, $compiler);
                // check if modifier allowed
                if (!is_object($compiler->smarty->security_policy) || $compiler->smarty->security_policy->isTrustedModifier($modifier, $compiler)) {
                    $output = "{$function}({$object}{$params})";
                }
                // check if trusted PHP function
            } else if (is_callable($modifier)) {
                // check if modifier allowed
                if (!is_object($compiler->smarty->security_policy) || $compiler->smarty->security_policy->isTrustedPhpModifier($modifier, $compiler)) {
                    $object = $this->testParameter($modifier, $modifier, $single_modifier, $compiler);
                    $output = "{$modifier}({$object}{$params})";
                }
            } else {
                $compiler->trigger_template_error("unknown modifier \"" . $modifier . "\"", $compiler->lex->taglineno);
            }
        }
        return $output;
    }

    /**
    * Check number of required modifer parameter oand optionally return context object
    *
    * @param string $modifier modifier name
    * @param callback $function modifier callback
    * @param array  $params parameter array
    * @param object $compiler  compiler object
    * @return string variable with context object or empty
    */
    private function testParameter($modifier, $callback, $params, $compiler) {
        $object = '';
        //NOTE: For some PHP functions in PHP < 5.3 getParameters() did not return a result
        if ($parameters = $this->buildReflection($callback)->getParameters()) {
            if ($result = $this->injectObject($callback, array('Smarty', 'Smarty_Internal_Template','Smarty_Internal_TemplateCompilerBase'),0)) {
                if ($result[0] == 'Smarty') {
                    $object = '$_smarty_tpl->smarty, ';
                } elseif  ($result[0] == 'Smarty_Internal_Template') {
                    $object = '$_smarty_tpl, ';
                } else {
                    $object = $compiler;
                }
                array_shift($parameters);
            }
            $no_supplied = count($params);
            if ($no_supplied > count($parameters)) {
                $compiler->trigger_template_error("Modifier '{$modifier}': Too many parameter", $compiler->lex->taglineno);
            }
            $i = 0;
            $names = array();
            foreach ($parameters as $parameter) {
                $i++;
                if ($i > $no_supplied && !$parameter->isOptional()) {
                    $names[] = $parameter->getName();
                }
            }
            if (!empty($names)) {
                $names = join(', ',$names);
                $compiler->trigger_template_error("Modifier '{$modifier}': Missing required parameter '{$names}'", $compiler->lex->taglineno);
            }
        }
        return $object;
    }
}
?>