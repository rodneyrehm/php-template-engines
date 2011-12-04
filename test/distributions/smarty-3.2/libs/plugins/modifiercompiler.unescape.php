<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty unescape modifier plugin
 *
 * Type:     modifier<br>
 * Name:     unescape<br>
 * Purpose:  unescape html entities
 *
 * @author Rodney Rehm
 *
 * @param string $input     output string
 * @param string $esc_type  escape type
 * @param string  $char_set      character set
 * @return string with compiled code
 */
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_unescape($input, $esc_type = "'html'", $char_set = "SMARTY_RESOURCE_CHAR_SET")
{
    if (preg_match('/^([\'"]?)[a-zA-Z0-9_]+(\\1)$/', $esc_type)) {
        // $esc_type is litteral so we can produce compiled code
        $esc = trim($esc_type, "'\"");
        switch ($esc) {
        case 'entity':
            return "mb_convert_encoding({$input}, {$char_set}, 'HTML-ENTITIES')";
        case 'htmlall':
            if (SMARTY_MBSTRING /* ^phpunit */&&empty($_SERVER['SMARTY_PHPUNIT_DISABLE_MBSTRING'])/* phpunit$ */) {
                return "mb_convert_encoding({$input}, {$char_set}, 'HTML-ENTITIES')";
            }
            return "html_entity_decode({$input}, ENT_QUOTES, {$char_set})";

        case 'html':
            return "htmlspecialchars_decode({$input}, ENT_QUOTES)";

    }
}
    // could not optimize |escape call, so fallback to regular plugin
    if ($compiler->tag_nocache | $compiler->nocache) {
        $compiler->template->required_plugins['nocache']['escape']['modifier']['file'] = SMARTY_PLUGINS_DIR .'modifier.unescape.php';
        $compiler->template->required_plugins['nocache']['escape']['modifier']['function'] = 'smarty_modifier_unescape';
    } else {
        $compiler->template->required_plugins['compiled']['escape']['modifier']['file'] = SMARTY_PLUGINS_DIR .'modifier.unescape.php';
        $compiler->template->required_plugins['compiled']['escape']['modifier']['function'] = 'smarty_modifier_unescape';
    }
    return "smarty_modifier_unescape(\$_smarty_tpl, {$input}, {$esc_type}, {$char_set})";
}
?>