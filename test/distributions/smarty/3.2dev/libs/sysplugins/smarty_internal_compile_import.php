<?php

/**
 * Smarty Internal Plugin Compile Import
 *
 * Compiles the {import} tag
 *
 * @package Smarty
 * @subpackage Compiler
 * @author Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Import Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Import extends Smarty_Internal_CompileBase {

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array('file');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array('file');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $option_flags = array();

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array();

    /**
     * Compiles code for the {import} tag
     *
     * @param array $args array with attributes from parser
     * @param object $compiler compiler object
     * @param array $parameter array with compilation parameter
     * @return string compiled code
     */
    public function compile($args, $compiler, $parameter) {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);

        $include_file = $_attr['file'];
        if ($compiler->has_variable_string && substr_count($include_file, "'") != 2) {
            $compiler->trigger_template_error('illegal variable template name', $compiler->lex->taglineno);
        }

        eval("\$tpl_name = $include_file;");
        if ($compiler->nocache || !$compiler->template->caching) {
            $caching = 0;
        } else {
            $caching = 1;
        }
        $tpl = new $compiler->smarty->template_class($tpl_name, $compiler->smarty, $compiler->template, null, null, $caching, null, true);
        // get compiled code
        $tpl->compiler->suppressHeader = true;
        $tpl->compiler->suppressTemplatePropertyHeader = true;
        $compiled_code = $tpl->compiler->compileTemplate($tpl);
        // release compiler object to free memory
        unset($tpl->compiler);
        // merge compiled code for {function} tags
        $compiler->template->properties['function'] = array_merge($compiler->template->properties['function'], $tpl->properties['function']);
        // merge filedependency
        $tpl->properties['file_dependency'][$tpl->source->uid] = array($tpl->source->filepath, $tpl->source->timestamp, $tpl->source->type);
        $compiler->template->properties['file_dependency'] = array_merge($compiler->template->properties['file_dependency'], $tpl->properties['file_dependency']);
        // remove header code
        $compiled_code = preg_replace("/(\/\*%%SmartyHeaderCode:{$tpl->properties['nocache_hash']}%%\*\/(.+?)\/\*\/%%SmartyHeaderCode%%\*\/)/s", '', $compiled_code);
        // replace nocache_hash
        $compiled_code = preg_replace("/{$tpl->properties['nocache_hash']}/", $compiler->template->properties['nocache_hash'], $compiled_code);
        $compiler->template->has_nocache_code = true;

        $save = $compiler->nocache_nolog;
        // update nocache line number trace back
        $compiler->parser->updateNocacheLineTrace();
        $compiler->nocache_nolog = $save;
        // output compiled code
        $_output = "<?php \n/*  Imported template \"" . $tpl_name . "\" */\n?>";
        $_output .= $compiled_code;
        $_output .= "\n<?php /*  End of imported template \"" . $tpl_name . "\" */\n?>";
        return $_output;
    }

}
