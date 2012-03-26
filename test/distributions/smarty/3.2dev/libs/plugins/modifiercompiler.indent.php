<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty indent modifier plugin
 *
 * Type:     modifier<br>
 * Name:     indent<br>
 * Purpose:  indent lines of text
 *
 * @link http://www.smarty.net/docs/en/language.modifier.indent.tpl indent (Smarty online manual)
 * @author Uwe Tews
 *
 * @param string $input input string
 * @param int $count number of posizions to indent
 * @param char $char character to use for indention
 * @return string with compiled code
 */
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_indent($input, $count = 4, $char = "' '") {
    return "preg_replace('!^!m',str_repeat({$char}, {$count}), {$input})";
}
