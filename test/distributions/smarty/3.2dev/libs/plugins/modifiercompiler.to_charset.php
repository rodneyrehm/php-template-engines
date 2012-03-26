<?php

/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty to_charset modifier plugin
 *
 * Type:     modifier<br>
 * Name:     to_charset<br>
 * Purpose:  convert character encoding from internal encoding to $charset
 *
 * @link http://www.smarty.net/docs/en/language.modifier.to_charset.tpl
 *          regex_replace (Smarty online manual)
 * @author Rodney Rehm
 *
 * @param string $input input string
 * @param string $charset target charset
 * @return string with compiled code
 */
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_to_charset($input, $charset = '\'ISO-8859-1\'') {
    if (!Smarty::$_MBSTRING) {
        // FIXME: (rodneyrehm) shouldn't this throw an error?
        return $input;
    }
    return "mb_convert_encoding({$input}, {$charset}, '" . addslashes(Smarty::$_CHARSET) . "')";
}
