<?php

/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty string_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     string_format<br>
 * Purpose:  format strings via sprintf
 *
 * @link http://www.smarty.net/docs/en/language.modifier.string.format.tpl string_format (Smarty online manual)
 * @author Uwe Tews
 *
 * @param mixed $input  input string to be formated
 * @param string $format format string
 * @return string with compiled code
 */
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_string_format($input, $format) {
    return "sprintf({$format}, {$input})";
}
