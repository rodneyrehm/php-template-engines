<?php

/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty default modifier plugin
 *
 * Type:     modifier<br>
 * Name:     default<br>
 * Purpose:  designate default value for empty variables
 *
 * @link http://www.smarty.net/docs/en/language.modifier.default.tpl default (Smarty online manual)
 * @author Uwe Tews
 *
 * @param n x mixed $params any number of parameter                    }
 * @return string with compiled code
 */
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_default() {
    $params = func_get_args();
    $output = $params[0];
    if (!isset($params[1])) {
        $params[1] = "''";
    }
    $first = true;
    array_shift($params);
    foreach ($params as $param) {
        $postfix = '';
        $at = '';
        if ($first) {
            preg_match('/(\$_smarty_tpl->tpl_vars->)([0-9]*[a-zA-Z_]\w*)(.*)/', $output, $match);
            if (isset($match[1])) {
                $output = "isset({$match[1]}{$match[2]}) ? {$match[1]}{$match[2]} : \$_smarty_tpl->getVariable('{$match[2]}', null, true, false)";
                $postfix = $match[3];
            }
            $first = false;
        } else {
            $at = '@';
        }
        $output = "((\$tmp = {$at}{$output})===null||\$tmp{$postfix}==='' ? {$param} : \$tmp{$postfix})";
    }
    return $output;
}
