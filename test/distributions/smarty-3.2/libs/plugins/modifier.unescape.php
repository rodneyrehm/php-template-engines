<?php
/**
* Smarty plugin
*
* @package Smarty
* @subpackage PluginsModifier
*/

/**
* Smarty unescape modifier plugin
* NOTE: This plugin is called only when smarty_modifercompiler_escape() is not able to produce inline code
*
* Type:     modifier<br>
* Name:     unescape<br>
* Purpose:  unescape html entities
*
* @author Uwe Tews
*
* @param Smarty_Internal_Template $template template object
* @param string $input     output string
* @param string $esc_type  escape type
* @param string  $char_set      character set
* @return string with compiled code
*/
function smarty_modifier_unescape(Smarty_Internal_Template $template, $input, $esc_type = 'html', $char_set = null)
{
    if (!$char_set) {
        $char_set = SMARTY_RESOURCE_CHAR_SET;
    }
    switch ($esc_type) {
        case 'entity':
        return mb_convert_encoding($input, $char_set, 'HTML-ENTITIES');

        case 'htmlall':
        if (SMARTY_MBSTRING /* ^phpunit */&&empty($_SERVER['SMARTY_PHPUNIT_DISABLE_MBSTRING'])/* phpunit$ */) {
            return mb_convert_encoding($input, $char_set, 'HTML-ENTITIES');
        }
        return html_entity_decode($input, ENT_QUOTES, $char_set);

        case 'html':
        return htmlspecialchars_decode($input, ENT_QUOTES);

        default:
        throw new SmartyRuntimeException("Modifier unescape: Illegal unescape type '{$esc_type}'", $template);
    }
}
?>