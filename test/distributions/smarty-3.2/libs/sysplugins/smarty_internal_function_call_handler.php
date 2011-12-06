<?php
/**
 * Smarty Internal Plugin Function Call Handler
 *
 * @package Smarty
 * @subpackage PluginsInternal
 * @author Uwe Tews
 */

/**
 * This class does call function defined with the {function} tag
 *
 * @package Smarty
 * @subpackage PluginsInternal
 */
class Smarty_Internal_Function_Call_Handler {

    /**
     * This function handles calls to template functions defined by {function}
     * It does create a PHP function at the first call
     *
     * @param string                   $_name       template function name
     * @param Smarty_Internal_Template $_template   template object
     * @param array                    $_params     Smarty variables passed as call parameter
     * @param string                   $_hash       nocache hash value
     * @param bool                     $_nocache    nocache flag
     */
    public static function call($_name, Smarty_Internal_Template $_template, $_params, $_hash, $_nocache)
    {
        if ($_nocache) {
            $_function = "smarty_template_function_{$_name}_nocache";
        } else {
            $_function = "smarty_template_function_{$_hash}_{$_name}";
        }
        if (!is_callable($_function)) {
            $_code = "function {$_function}(\$_smarty_tpl,\$params) {
    \$saved_tpl_vars = clone \$_smarty_tpl->tpl_vars;
    foreach (\$_smarty_tpl->smarty->template_functions['{$_name}']['parameter'] as \$key => \$value) {\$_smarty_tpl->tpl_vars->\$key = \$value;};
    foreach (\$params as \$key => \$value) {\$_smarty_tpl->tpl_vars->\$key = \$value;}?>";
            if ($_nocache) {
                $echo = "echo \\'/\*%%SmartyNocache:{$_template->smarty->template_functions[$_name]['nocache_hash']}%%\*/";
                $end = "/\*/%%SmartyNocache:{$_template->smarty->template_functions[$_name]['nocache_hash']}%%\*/\\';";
                $_code .= preg_replace(array("!<\?php {$echo}|{$echo}<\?php|{$end}\?>|\?>\s*{$end}!",
                        "!\\\'!"), array('', "'"), $_template->smarty->template_functions[$_name]['compiled']);
                $_template->smarty->template_functions[$_name]['called_nocache'] = true;
            } else {
                $_code .= "<?php echo '/*%%SmartyNocache:{$_template->properties['nocache_hash']}%%*/" . $_template->smarty->template_functions[$_name]['traceback'] . "/*/%%SmartyNocache:{$_template->properties['nocache_hash']}%%*/';?>\n";
                $_code .= preg_replace("/{$_template->smarty->template_functions[$_name]['nocache_hash']}/", $_template->properties['nocache_hash'], $_template->smarty->template_functions[$_name]['compiled']);
                $_code .= "<?php echo '/*%%SmartyNocache:{$_template->properties['nocache_hash']}%%*/<?php array_shift(\$_smarty_tpl->trace_call_stack);?>/*/%%SmartyNocache:{$_template->properties['nocache_hash']}%%*/';?>\n";
            }
            $_code .= "<?php \$_smarty_tpl->tpl_vars = \$saved_tpl_vars;}";
            eval($_code);
        }
        $_function($_template, $_params);
    }

}

?>
