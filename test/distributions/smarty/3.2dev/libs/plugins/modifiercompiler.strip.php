<?php

/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty strip modifier plugin
 *
 * Type:     modifier<br>
 * Name:     strip<br>
 * Purpose:  Replace all repeated spaces, newlines, tabs
 *              with a single space or supplied replacement string.<br>
 * Example:  {$var|strip} {$var|strip:"&nbsp;"}<br>
 * Date:     September 25th, 2002
 *
 * @link http://www.smarty.net/docs/en/language.modifier.strip.tpl strip (Smarty online manual)
 * @author Uwe Tews
 *
 * @params string $input input string
 * @params string $replacement replacement string
 * @return string with compiled code
 */
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_strip($input, $replacement = "' '") {
    return "preg_replace('!\s+!" . Smarty::$_UTF8_MODIFIER . "', {$replacement},{$input})";
}
