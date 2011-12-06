<?php
/**
* Smarty Internal Plugin Compile Function
*
* Compiles the {function} {/function} tags
*
* @package Smarty
* @subpackage Compiler
* @author Uwe Tews
*/

/**
* Smarty Internal Plugin Compile Function Class
*
* @package Smarty
* @subpackage Compiler
*/
class Smarty_Internal_Compile_Function extends Smarty_Internal_CompileBase {

    /**
    * Attribute definition: Overwrites base class.
    *
    * @var array
    * @see Smarty_Internal_CompileBase
    */
    public $required_attributes = array('name');
    /**
    * Attribute definition: Overwrites base class.
    *
    * @var array
    * @see Smarty_Internal_CompileBase
    */
    public $shorttag_order = array('name');
    /**
    * Attribute definition: Overwrites base class.
    *
    * @var array
    * @see Smarty_Internal_CompileBase
    */
    public $optional_attributes = array('_any');

    /**
    * Compiles code for the {function} tag
    *
    * @param array $args array with attributes from parser
    * @param object $compiler compiler object
    * @param array $parameter array with compilation parameter
    * @return boolean true
    */
    public function compile($args, $compiler, $parameter)
    {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);

        if ($_attr['nocache'] === true) {
            $compiler->trigger_template_error('nocache option not allowed', $compiler->lex->taglineno);
        }
        unset($_attr['nocache']);
        if ($compiler->template->source->type == 'eval' || $compiler->template->source->type == 'string') {
            $resource = $compiler->template->source->type;
        } else {
            $resource = $compiler->template->template_resource;
            // santitize extends resource
            if (strpos($resource,'extends:') !==false) {
                $start = strpos($resource,':');
                $end = strpos($resource,'|');
                $resource = substr($resource,$start+1,$end-$start-1);
            }
        }
        $save = array($_attr, $compiler->parser->current_buffer,
        $compiler->template->has_nocache_code, $compiler->template->required_plugins, $resource);
        $this->openTag($compiler, 'function', $save);
        $_name = trim($_attr['name'], "'\"");
        unset($_attr['name']);
        // set flag that we are compiling a template function
        $compiler->compiles_template_function = true;
        $compiler->template->properties['function'][$_name]['parameter'] = array();
        $_smarty_tpl = $compiler->template;
        foreach ($_attr as $_key => $_data) {
            eval ('$tmp='.$_data.';');
            $compiler->template->properties['function'][$_name]['parameter'][$_key] = $tmp;
        }
        $compiler->smarty->template_functions[$_name]['parameter'] = $compiler->template->properties['function'][$_name]['parameter'];
        if ($compiler->template->caching) {
            $output = '';
        } else {
            $output = "<?php if (!function_exists('smarty_template_function_{$_name}')) {
            function smarty_template_function_{$_name}(\$_smarty_tpl,\$params) {
            array_unshift(\$_smarty_tpl->trace_call_stack, array('{$resource}', 0, '{$compiler->template->source->type}'));
            \$saved_tpl_vars = clone \$_smarty_tpl->tpl_vars;
            foreach (\$_smarty_tpl->smarty->template_functions['{$_name}']['parameter'] as \$key => \$value) {\$_smarty_tpl->tpl_vars->\$key = \$value;};
            foreach (\$params as \$key => \$value) {\$_smarty_tpl->tpl_vars->\$key = \$value;}?>";
        }
        // Init temporay context
        $compiler->template->required_plugins = array('compiled' => array(), 'nocache' => array());
        $compiler->parser->current_buffer = new _smarty_template_buffer($compiler->parser);
        $compiler->parser->current_buffer->append_subtree(new _smarty_tag($compiler->parser, $output));
        $compiler->template->has_nocache_code = false;
        $compiler->has_code = false;
        $compiler->template->properties['function'][$_name]['compiled'] = '';
        return true;
    }

}

/**
* Smarty Internal Plugin Compile Functionclose Class
*
* @package Smarty
* @subpackage Compiler
*/
class Smarty_Internal_Compile_Functionclose extends Smarty_Internal_CompileBase {

    /**
    * Compiles code for the {/function} tag
    *
    * @param array $args array with attributes from parser
    * @param object $compiler compiler object
    * @param array $parameter array with compilation parameter
    * @return boolean true
    */
    public function compile($args, $compiler, $parameter)
    {
        $_attr = $this->getAttributes($compiler, $args);
        $saved_data = $this->closeTag($compiler, array('function'));
        $_name = trim($saved_data[0]['name'], "'\"");
        $nocache_start_string = "<?php echo '/*%%SmartyNocache:{$compiler->template->properties['nocache_hash']}%%*/<?php ";
        $nocache_end_string = "?>/*/%%SmartyNocache:{$compiler->template->properties['nocache_hash']}%%*/';?>\n";
        // build plugin include code
        $plugins_string = '';
        if (!empty($compiler->template->required_plugins['compiled'])) {
            $plugins_string = '<?php ';
            foreach($compiler->template->required_plugins['compiled'] as $tmp) {
                foreach($tmp as $data) {
                    $plugins_string .= "if (!is_callable('{$data['function']}')) include '{$data['file']}';\n";
                }
            }
            $plugins_string .= '?>';
        }
        if (!empty($compiler->template->required_plugins['nocache'])) {
            $plugins_string .= $nocache_start_string;
            foreach($compiler->template->required_plugins['nocache'] as $tmp) {
                foreach($tmp as $data) {
                    $plugins_string .= "if (!is_callable(\'{$data['function']}\')) include \'{$data['file']}\';\n";
                }
            }
            $plugins_string .= $nocache_end_string;
        }
        // remove last line break from function definition
        $last = count($compiler->parser->current_buffer->subtrees) - 1;
        if ($compiler->parser->current_buffer->subtrees[$last] instanceof _smarty_linebreak) {
            unset($compiler->parser->current_buffer->subtrees[$last]);
        }
        // if caching save template function for possible nocache call
        if ($compiler->template->caching) {
            $compiler->template->properties['function'][$_name]['compiled'] .= $plugins_string
//            . $nocache_start_string . "array_unshift(\$_smarty_tpl->trace_call_stack, array(\'{$saved_data[4]}\', 0, \'{$compiler->template->source->type}\'));\n" . $nocache_end_string
            . "<?php array_unshift(\$_smarty_tpl->trace_call_stack, array('{$saved_data[4]}', 0, '{$compiler->template->source->type}'));?>\n"
            . $compiler->parser->current_buffer->to_smarty_php()
//            . $nocache_start_string . "array_shift(\$_smarty_tpl->trace_call_stack);\n" . $nocache_end_string
            . "<?php\narray_shift(\$_smarty_tpl->trace_call_stack);?>";
            $compiler->template->properties['function'][$_name]['nocache_hash'] = $compiler->template->properties['nocache_hash'];
            $compiler->template->properties['function'][$_name]['has_nocache_code'] = $compiler->template->has_nocache_code;
            $compiler->template->properties['function'][$_name]['called_functions'] = $compiler->called_functions;
            $compiler->template->properties['function'][$_name]['traceback'] = "<?php array_unshift(\$_smarty_tpl->trace_call_stack, array(\'{$saved_data[4]}\', 0, \'{$compiler->template->source->type}\'));?>";

            $compiler->called_functions = array();
            $compiler->smarty->template_functions[$_name] = $compiler->template->properties['function'][$_name];
            $compiler->has_code = false;
            $output = true;
        } else {
            $output = $plugins_string . $compiler->parser->current_buffer->to_smarty_php() . "<?php \$_smarty_tpl->tpl_vars = \$saved_tpl_vars;\narray_shift(\$_smarty_tpl->trace_call_stack);}}?>\n";
        }
        // reset flag that we are compiling a template function
        $compiler->compiles_template_function = false;
        // restore old compiler status
        $compiler->parser->current_buffer = $saved_data[1];
        $compiler->template->has_nocache_code = $compiler->template->has_nocache_code | $saved_data[2];
        $compiler->template->required_plugins = $saved_data[3];
        return $output;
    }

}

?>