<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty count_words modifier plugin
 *
 * Type:     modifier<br>
 * Name:     count_words<br>
 * Purpose:  count the number of words in a text
 *
 * @link http://www.smarty.net/manual/en/language.modifier.count.words.php count_words (Smarty online manual)
 * @author Uwe Tews
 *
 * @param string $input input string
 * @return string with compiled code
*/
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_count_words($input)
{
    if (SMARTY_MBSTRING /* ^phpunit */&&empty($_SERVER['SMARTY_PHPUNIT_DISABLE_MBSTRING'])/* phpunit$ */) {
        // expression taken from http://de.php.net/manual/en/function.str-word-count.php#85592
        return 'preg_match_all(\'/\p{L}[\p{L}\p{Mn}\p{Pd}\\\'\x{2019}]*/u\', ' . $input . ', $tmp)';
    }
    // no MBString fallback
    return "str_word_count({$input})";
}
?>