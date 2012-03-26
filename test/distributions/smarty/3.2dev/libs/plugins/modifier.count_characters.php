<?php

/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty count_characters modifier plugin
 * NOTE: This plugin is called only when smarty_modifercompiler_count_characters() is not able to produce inline code
 *
 * Type:     modifier<br>
 * Name:     count_characteres<br>
 * Purpose:  count the number of characters in a text
 *
 * @link http://www.smarty.net/docs/en/language.modifier.count.characters.tpl count_characters (Smarty online manual)
 * @author Uwe Tews
 *
 * @param string $input  input string
 * @param bool   $whitespace   flag count whitespaces
 * @param array $params parameters
 * @return string with compiled code
 */
function smarty_modifier_count_characters($input, $whitespace = false) {
    if (!$whitespace) {
        return preg_match_all('/[^\s]/u', $input, $tmp);
    }
    if (SMARTY_MBSTRING /* ^phpunit */ && empty($_SERVER['SMARTY_PHPUNIT_DISABLE_MBSTRING'])/* phpunit$ */) {
        return mb_strlen($input, SMARTY_RESOURCE_CHAR_SET);
    }
    // no MBString fallback
    return strlen($input);
}
