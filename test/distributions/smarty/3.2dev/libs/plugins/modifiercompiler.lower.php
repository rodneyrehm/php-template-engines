<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty lower modifier plugin
 *
 * Type:     modifier<br>
 * Name:     lower<br>
 * Purpose:  convert string to lowercase
 *
 * @link http://www.smarty.net/docs/en/language.modifier.lower.tpl lower (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 * @author Uwe Tews
 *
 * @param string $input input string
 * @return string with compiled code
 */
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_lower($input) {
    if (Smarty::$_MBSTRING) {
        return "mb_strtolower({$input}, '" . addslashes(Smarty::$_CHARSET) . "')";
    }
    // no MBString fallback
    return 'strtolower(' . $input . ')';
}
