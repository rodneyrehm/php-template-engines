<?php

/**
 * Smarty Internal Plugin Compile While
 *
 * Compiles the {while} tag
 *
 * @package Smarty
 * @subpackage Compiler
 * @author Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile While Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_While extends Smarty_Internal_CompileBase {

    /**
     * Compiles code for the {while} tag
     *
     * @param array  $args      array with attributes from parser
     * @param object $compiler  compiler object
     * @param array  $parameter array with compilation parameter
     * @return string compiled code
     */
    public function compile($args, $compiler, $parameter) {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);
        $this->openTag($compiler, 'while', $compiler->nocache);

        if (!array_key_exists("if condition", $parameter)) {
            $compiler->trigger_template_error("missing while condition", $compiler->lex->taglineno);
        }

        // maybe nocache because of nocache variables
        $compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
        if (is_array($parameter['if condition'])) {
            if (is_array($parameter['if condition']['var'])) {
                $var = trim($parameter['if condition']['var']['var'], "'");
            } else {
                $var = trim($parameter['if condition']['var'], "'");
            }
            if ($compiler->nocache) {
                $_nocache = 'true';
                // create nocache var to make it know for further compiling
                $compiler->template->tpl_vars->$var = array('value' => null, 'nocache' => true);
            } else {
                $_nocache = 'false';
            }
            if (is_array($parameter['if condition']['var'])) {
                $_output = "<?php if (!isset(\$_smarty_tpl->tpl_vars->{$var}) || !is_array(\$_smarty_tpl->tpl_vars->{$var}['value'])) \$_smarty_tpl->_createLocalArrayVariable(" . $parameter['if condition']['var']['var'] . ",{$_nocache});\n";
                $_output .= "while (\$_smarty_tpl->tpl_vars->{$var}['value']" . $parameter['if condition']['var']['smarty_internal_index'] . " = " . $parameter['if condition']['value'] . "){?>";
            } else {
                $_output = "<?php if (!isset(\$_smarty_tpl->tpl_vars->{$var})) \$_smarty_tpl->tpl_vars->{$var} = array('value' => null, 'nocache' => {$_nocache});\n";
                $_output .= "while (\$_smarty_tpl->tpl_vars->{$var}['value'] = " . $parameter['if condition']['value'] . "){?>";
            }
            return $_output;
        } else {
            return "<?php while ({$parameter['if condition']}){?>";
        }
    }

}

/**
 * Smarty Internal Plugin Compile Whileclose Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Whileclose extends Smarty_Internal_CompileBase {

    /**
     * Compiles code for the {/while} tag
     *
     * @param array  $args     array with attributes from parser
     * @param object $compiler compiler object
     * @return string compiled code
     */
    public function compile($args, $compiler) {
        // must endblock be nocache?
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }
        $compiler->nocache = $this->closeTag($compiler, array('while'));
        return "<?php }?>";
    }

}
