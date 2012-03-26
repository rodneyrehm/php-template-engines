<?php

/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty upper modifier plugin
 *
 * Type:     modifier<br>
 * Name:     lower<br>
 * Purpose:  convert string to uppercase
 *
 * @link http://www.smarty.net/docs/en/language.modifier.upper.tpl lower (Smarty online manual)
 * @author Uwe Tews
 *
 * @param string $input input string
 * @return string with compiled code
 */
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_upper($input) {
    if (Smarty::$_MBSTRING) {
        return "mb_strtoupper({$input}, '" . addslashes(Smarty::$_CHARSET) . "')";
    }
    // no MBString fallback
    return "strtoupper({$input})";
}
