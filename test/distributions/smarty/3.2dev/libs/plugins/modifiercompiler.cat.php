<?php

/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty cat modifier plugin
 *
 * Type:     modifier<br>
 * Name:     cat<br>
 * Date:     Feb 24, 2003<br>
 * Purpose:  catenate a value to a variable<br>
 * Input:    string to catenate<br>
 * Example:  {$var|cat:"foo"}
 *
 * @link http://www.smarty.net/docs/en/language.modifier.cat.tpl cat
 *          (Smarty online manual)
 * @author   Uwe Tews
 *
 * @param n x mixed $params any number of parameter                    }
 * @return string with compiled code
 */
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_cat() {
    return '(' . implode(').(', func_get_args()) . ')';
}
