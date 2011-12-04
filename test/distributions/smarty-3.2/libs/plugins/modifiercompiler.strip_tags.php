<?php
/**
* Smarty plugin
*
* @package Smarty
* @subpackage PluginsModifierCompiler
*/

/**
* Smarty strip_tags modifier plugin
*
* Type:     modifier<br>
* Name:     strip_tags<br>
* Purpose:  strip html tags from text
*
* @link http://www.smarty.net/manual/en/language.modifier.strip.tags.php strip_tags (Smarty online manual)
* @author Uwe Tews
*
* @param Smarty_Internal_TemplateCompilerBase $compiler compiler object
* @param string $input  input string
* @param bool $replace  if true replace tag by ' '
* @return string with compiled code
*/
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_strip_tags(Smarty_Internal_TemplateCompilerBase $compiler, $input, $replace = 'true')
{
    if (preg_match('/^([\'"]?)[a-zA-Z0-9_]+(\\1)$/', $replace)) {
        $replace = trim($replace, "'\"");
        if ($replace == 'true') {
            return "preg_replace('!<[^>]*?>!', ' ', {$input})";
        }
        if ($replace == 'false') {
            return "strip_tags({$input})";
        }
    }
    // could not optimize |strip_tags, so fallback to regular plugin
    if ($compiler->tag_nocache | $compiler->nocache) {
        $compiler->template->required_plugins['nocache']['escape']['modifier']['file'] = SMARTY_PLUGINS_DIR .'modifier.strip_tags';
        $compiler->template->required_plugins['nocache']['escape']['modifier']['function'] = 'smarty_modifier_strip_tags';
    } else {
        $compiler->template->required_plugins['compiled']['escape']['modifier']['file'] = SMARTY_PLUGINS_DIR .'modifier.strip_tags.php';
        $compiler->template->required_plugins['compiled']['escape']['modifier']['function'] = 'smarty_modifier_strip_tags';
    }
    return "smarty_modifier_strip_tags({$input}, {$replace})";

}
?>