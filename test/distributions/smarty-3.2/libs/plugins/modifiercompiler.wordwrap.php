<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty wordwrap modifier plugin
 *
 * Type:     modifier<br>
 * Name:     wordwrap<br>
 * Purpose:  wrap a string of text at a given length
 *
 * @link http://smarty.php.net/manual/en/language.modifier.wordwrap.php wordwrap (Smarty online manual)
 * @author Uwe Tews
 *
 * @param Smarty_Internal_TemplateCompilerBase $compiler compiler object
 * @param string $input input string
 * @param int $columns number of columns before wrap
 * @param string $wrap string to use to wrap
 * @param bool $cut if true wrap exact at column count
 * @return string with compiled code
 */
// NOTE: The parser does pass all parameter as strings which could be directly inserted into the compiled code string
function smarty_modifiercompiler_wordwrap(Smarty_Internal_TemplateCompilerBase $compiler, $input, $columns = 80, $wrap = '"\n"', $cut = 'false')
{
    $function = 'wordwrap';
    if (SMARTY_MBSTRING /* ^phpunit */&&empty($_SERVER['SMARTY_PHPUNIT_DISABLE_MBSTRING'])/* phpunit$ */) {
        if ($compiler->tag_nocache | $compiler->nocache) {
            $compiler->template->required_plugins['nocache']['wordwrap']['modifier']['file'] = SMARTY_PLUGINS_DIR .'shared.mb_wordwrap.php';
            $compiler->template->required_plugins['nocache']['wordwrap']['modifier']['function'] = 'smarty_mb_wordwrap';
        } else {
            $compiler->template->required_plugins['compiled']['wordwrap']['modifier']['file'] = SMARTY_PLUGINS_DIR .'shared.mb_wordwrap.php';
            $compiler->template->required_plugins['compiled']['wordwrap']['modifier']['function'] = 'smarty_mb_wordwrap';
        }
        $function = 'smarty_mb_wordwrap';
    }
    return $function . "({$input}, {$columns}, {$wrap}, {$cut})";
}

?>