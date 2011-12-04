<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty strip_tags modifier plugin
 * NOTE: This plugin is called only when smarty_modifercompiler_strip_tags() is not able to produce inline code
 *
 * Type:     modifier<br>
 * Name:     strip_tags<br>
 * Purpose:  strip html tags from text
 *
 * @link http://www.smarty.net/manual/en/language.modifier.strip.tags.php strip_tags (Smarty online manual)
 * @author Uwe Tews
 *
 * @param string $input  input string
 * @param bool $replace  if true replace tag by ' '
 * @return string with compiled code
 */
function smarty_modifiercompiler_strip_tags($input, $replace = true)
{
    if ($replace === true) {
        return preg_replace('!<[^>]*?>!', ' ', $input);
    } else {
        return strip_tags($input);
    }
}
?>