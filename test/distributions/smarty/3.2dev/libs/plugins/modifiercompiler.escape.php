<?php

/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */
/**
 * @ignore
 */

/**
 * Smarty escape modifier plugin
 *
 * Type:     modifier<br>
 * Name:     escape<br>
 * Purpose:  escape string for output
 *
 * @link http://www.smarty.net/docs/en/language.modifier.escape.tpl (Smarty online manual)
 * @author Rodney Rehm
 *
 * @param Smarty_Internal_TemplateCompilerBase $compiler compiler object
 * @param string  $input         input string
 * @param string  $esc_type      escape type
 * @param string  $char_set      character set, used for htmlspecialchars() or htmlentities()
 * @param boolean $double_encode encode already encoded entitites again, used for htmlspecialchars() or htmlentities()
 * @return string with compiled code
 */
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_escape(Smarty_Internal_TemplateCompilerBase $compiler, $input, $esc_type = 'html', $char_set = 'null', $double_encode = 'true') {
    if (trim($char_set, "'\"") == 'null') {
        $char_set = '\'' . Smarty::$_CHARSET . '\'';
    }
    if (preg_match('/^([\'"]?)[a-zA-Z0-9_]+(\\1)$/', $esc_type)) {
        // $esc_type is litteral so we can produce compiled code
        $esc = trim($esc_type, "'\"");
        switch ($esc) {
            case 'html':
                return "htmlspecialchars({$input}, ENT_QUOTES, {$char_set}, {$double_encode})";

            case 'htmlall':
                if (Smarty::$_MBSTRING) {
                    return "mb_convert_encoding(htmlspecialchars({$input}, ENT_QUOTES, {$char_set}, {$double_encode}), 'HTML-ENTITIES', {$char_set})";
                }

                // no MBString fallback
                return "htmlentities({$input}, ENT_QUOTES, {$char_set}, {$double_encode})";

            case 'url':
                return "rawurlencode({$input})";

            case 'urlpathinfo':
                return "str_replace('%2F', '/', rawurlencode({$input}))";

            case 'quotes':
                // escape unescaped single quotes
                return 'preg_replace("%(?<!\\\\\\\\)\'%", "\\\'",' . $input . ')';

            case 'javascript':
                // escape quotes and backslashes, newlines, etc.
                return 'strtr(' . $input . ', array("\\\\" => "\\\\\\\\", "\'" => "\\\\\'", "\"" => "\\\\\"", "\\r" => "\\\\r", "\\n" => "\\\n", "</" => "<\/" ))';
        }
    }

    // could not optimize |escape call, so fallback to regular plugin
    if ($compiler->tag_nocache | $compiler->nocache) {
        $compiler->template->required_plugins['nocache']['escape']['modifier']['file'] = SMARTY_PLUGINS_DIR . 'modifier.escape.php';
        $compiler->template->required_plugins['nocache']['escape']['modifier']['function'] = 'smarty_modifier_escape';
    } else {
        $compiler->template->required_plugins['compiled']['escape']['modifier']['file'] = SMARTY_PLUGINS_DIR . 'modifier.escape.php';
        $compiler->template->required_plugins['compiled']['escape']['modifier']['function'] = 'smarty_modifier_escape';
    }
    return "smarty_modifier_escape(\$_smarty_tpl, {$input}, {$esc_type}, {$char_set}, {$double_encode})";
}
