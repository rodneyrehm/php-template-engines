<?php

/**
 * Smarty write file plugin
 *
 * @package Smarty
 * @subpackage PluginsInternal
 * @author Monte Ohrt
 */

/**
 * Smarty Internal Write File Class
 *
 * @package Smarty
 * @subpackage PluginsInternal
 */
class Smarty_Internal_Write_File {

    /**
     * Writes file in a safe way to disk
     *
     * @param string $_filepath complete filepath
     * @param string $_contents file content
     * @param Smarty $smarty    smarty instance
     * @return boolean true
     */
    public static function writeFile($_filepath, $_contents, Smarty $smarty) {
        $_error_reporting = error_reporting();
        error_reporting($_error_reporting & ~E_NOTICE & ~E_WARNING);
        if ($smarty->_file_perms !== null) {
            $old_umask = umask(0);
        }

        $_dirpath = dirname($_filepath);
        // if subdirs, create dir structure
        if ($_dirpath !== '.' && !file_exists($_dirpath)) {
            mkdir($_dirpath, $smarty->_dir_perms === null ? 0777 : $smarty->_dir_perms, true);
        }

        // write to tmp file, then move to overt file lock race condition
        $_tmp_file = $_dirpath . DS . uniqid('wrt', true);
        if (!file_put_contents($_tmp_file, $_contents)) {
            error_reporting($_error_reporting);
            throw new SmartyException("unable to write file {$_tmp_file}");
            return false;
        }

        // remove original file
        @unlink($_filepath);

        // rename tmp file
        $success = rename($_tmp_file, $_filepath);
        if (!$success) {
            error_reporting($_error_reporting);
            throw new SmartyException("unable to write file {$_filepath}");
            return false;
        }

        if ($smarty->_file_perms !== null) {
            // set file permissions
            chmod($_filepath, $smarty->_file_perms);
            umask($old_umask);
        }
        error_reporting($_error_reporting);
        return true;
    }

    /**
     * Create code frame for compiled and cached templates
     *
     * @param Smarty_Internal_Template $_teplate   template object
     * @param string $content   optional template content
     * @param bool   $cache     flag for cache file
     * @return string
     */
    public static function createTemplateCodeFrame(Smarty_Internal_Template $_template, $content = '', $cache = false) {
        $plugins_string = '';
        // include code for plugins
        if (!$cache) {
            if (!empty($_template->required_plugins['compiled'])) {
                $plugins_string = '<?php ';
                foreach ($_template->required_plugins['compiled'] as $tmp) {
                    foreach ($tmp as $data) {
                        $plugins_string .= "if (!is_callable('{$data['function']}')) include '{$data['file']}';\n";
                    }
                }
                $plugins_string .= '?>';
            }
            if (!empty($_template->required_plugins['nocache'])) {
                $_template->has_nocache_code = true;
                $plugins_string .= "<?php echo '/*%%SmartyNocache:{$_template->properties['nocache_hash']}%%*/<?php \$_smarty = \$_smarty_tpl->smarty; ";
                foreach ($_template->required_plugins['nocache'] as $tmp) {
                    foreach ($tmp as $data) {
                        $plugins_string .= addslashes("if (!is_callable('{$data['function']}')) include '{$data['file']}';\n");
                    }
                }
                $plugins_string .= "?>/*/%%SmartyNocache:{$_template->properties['nocache_hash']}%%*/';?>\n";
            }
        }
        // build property code
        $_template->properties['has_nocache_code'] = $_template->has_nocache_code;
        $output = '';
        if (!$_template->source->recompiled) {
            $output = "<?php /*%%SmartyHeaderCode:{$_template->properties['nocache_hash']}%%*/";
            if ($_template->smarty->direct_access_security) {
                $output .= "if(!defined('SMARTY_DIR')) exit('no direct access allowed');\n";
            }
        }
        if ($cache) {
            // remove compiled code of{function} definition
            unset($_template->properties['function']);
            if (!empty($_template->smarty->template_functions)) {
                // copy code of {function} tags called in nocache mode
                foreach ($_template->smarty->template_functions as $name => $function_data) {
                    if (isset($function_data['called_nocache'])) {
                        foreach ($function_data['called_functions'] as $func_name) {
                            $_template->smarty->template_functions[$func_name]['called_nocache'] = true;
                        }
                    }
                }
                foreach ($_template->smarty->template_functions as $name => $function_data) {
                    if (isset($function_data['called_nocache'])) {
                        unset($function_data['called_nocache'], $function_data['called_functions'], $_template->smarty->template_functions[$name]['called_nocache']);
                        $_template->properties['function'][$name] = $function_data;
                    }
                }
            }
        }
        $_template->properties['version'] = Smarty::SMARTY_VERSION;
        if (!isset($_template->properties['unifunc'])) {
            $_template->properties['unifunc'] = 'content_' . str_replace('.', '_', uniqid('', true));
        }
        $_template->properties['cachedsubtemplates'] = $_template->cached_subtemplates;
        if (!$_template->source->recompiled) {
            $output .= "\$_valid = \$_smarty_tpl->_decodeProperties(" . var_export($_template->properties, true) . ',' . ($cache ? 'true' : 'false') . "); /*/%%SmartyHeaderCode%%*/?>\n";
        }
        if (!$_template->source->recompiled) {
            $output .= '<?php if ($_valid && !is_callable(\'' . $_template->properties['unifunc'] . '\')) {function ' . $_template->properties['unifunc'] . "(\$_smarty_tpl) {\n?>";
        }
        $output .= $plugins_string;
        $output .= $content;
        if (!$_template->source->recompiled) {
            $output .= '<?php }} ?>';
        }
        // make sure that we don't run into backtrack limit errors
        ini_set('pcre.backtrack_limit', -1);
        $output = preg_replace_callback('/(\?>)([\n]?)(<\?php )|(\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\';|"[^"\\\\]*(?:\\\\.[^"\\\\]*)*";)/', '_smartyRemovePhpTags', $output);
        return $output;
    }

}

/**
 * preg_replace callback to remove unneeded  ?><?php tags
 *
 * @param string $match match string
 * @return string  replacemant
 */
function _smartyRemovePhpTags($match) {
    if (isset($match[4])) {
        return $match[4];
    }
    if (isset($match[2])) {
        return $match[2];
    }
    return '';
}
