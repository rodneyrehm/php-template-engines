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
 * @link http://smarty.php.net/manual/en/language.modifier.upper.php lower (Smarty online manual)
 * @author Uwe Tews
 *
 * @param string $input input string
 * @return string with compiled code
 */
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_upper($input)
{
    if (SMARTY_MBSTRING /* ^phpunit */&&empty($_SERVER['SMARTY_PHPUNIT_DISABLE_MBSTRING'])/* phpunit$ */) {
        return "mb_strtoupper({$input}, SMARTY_RESOURCE_CHAR_SET)";
    }
    // no MBString fallback
    return "strtoupper({$input})";
}

?>