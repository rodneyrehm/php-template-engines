<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty count_paragraphs modifier plugin
 *
 * Type:     modifier<br>
 * Name:     count_paragraphs<br>
 * Purpose:  count the number of paragraphs in a text
 *
 * @link http://www.smarty.net/manual/en/language.modifier.count.paragraphs.php
 *          count_paragraphs (Smarty online manual)
 * @author Uwe Tews
 *
 * @param string $input input string
 * @return string with compiled code
 */
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_count_paragraphs($input)
{
    // count \r or \n characters
    return '(preg_match_all(\'#[\r\n]+#\', ' . $input . ', $tmp)+1)';
}
?>