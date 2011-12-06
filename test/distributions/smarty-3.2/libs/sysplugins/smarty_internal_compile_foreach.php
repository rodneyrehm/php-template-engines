<?php
/**
 * Smarty Internal Plugin Compile Foreach
 *
 * Compiles the {foreach} {foreachelse} {/foreach} tags
 *
 * @package Smarty
 * @subpackage Compiler
 * @author Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Foreach Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Foreach extends Smarty_Internal_CompileBase {
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array('from', 'item');
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array('name', 'key');
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array('from','item','key','name');

    /**
     * Compiles code for the {foreach} tag
     *
     * @param array  $args      array with attributes from parser
     * @param object $compiler  compiler object
     * @param array  $parameter array with compilation parameter
     * @return string compiled code
     */
    public function compile($args, $compiler, $parameter)
    {
        $tpl = $compiler->template;
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);

        $from = $_attr['from'];
        $item = trim($_attr['item'],'\'"');
        if ("\$_smarty_tpl->tpl_vars->{$item}" == $from) {
            $compiler->trigger_template_error("item variable {$item} may not be the same variable as at 'from'", $compiler->lex->taglineno);
        }

        if (isset($_attr['key'])) {
            $key =trim($_attr['key'],'\'"');
        } else {
            $key = null;
        }

        $this->openTag($compiler, 'foreach', array('foreach', $compiler->nocache, $item, $key));
        // maybe nocache because of nocache variables
        $compiler->nocache = $compiler->nocache | $compiler->tag_nocache;

        if (isset($_attr['name'])) {
            $name = $_attr['name'];
            $has_name = true;
            $SmartyVarName = '$smarty.foreach.' . trim($name, '\'"') . '.';
        } else {
            $name = null;
            $has_name = false;
        }
        $ItemVarName = '$' . $item . '@';
        // evaluates which Smarty variables and properties have to be computed
        if ($has_name) {
            $usesSmartyFirst = strpos($tpl->source->content, $SmartyVarName . 'first') !== false;
            $usesSmartyLast = strpos($tpl->source->content, $SmartyVarName . 'last') !== false;
            $usesSmartyIndex = strpos($tpl->source->content, $SmartyVarName . 'index') !== false;
            $usesSmartyIteration = strpos($tpl->source->content, $SmartyVarName . 'iteration') !== false;
            $usesSmartyShow = strpos($tpl->source->content, $SmartyVarName . 'show') !== false;
            $usesSmartyTotal = strpos($tpl->source->content, $SmartyVarName . 'total') !== false;
        } else {
            $usesSmartyFirst = false;
            $usesSmartyLast = false;
            $usesSmartyTotal = false;
            $usesSmartyShow = false;
        }

        $usesPropFirst = $usesSmartyFirst || strpos($tpl->source->content, $ItemVarName . 'first') !== false;
        $usesPropLast = $usesSmartyLast || strpos($tpl->source->content, $ItemVarName . 'last') !== false;
        $usesPropIndex = $usesPropFirst || strpos($tpl->source->content, $ItemVarName . 'index') !== false;
        $usesPropIteration = $usesPropLast || strpos($tpl->source->content, $ItemVarName . 'iteration') !== false;
        $usesPropShow = strpos($tpl->source->content, $ItemVarName . 'show') !== false;
        $usesPropTotal = $usesSmartyTotal || $usesSmartyShow || $usesPropShow || $usesPropLast || strpos($tpl->source->content, $ItemVarName . 'total') !== false;
        // generate output code
        $output = "<?php ";
        $output .= " \$_smarty_tpl->tpl_vars->___loop_{$item} = false;\n";
        $output .= " \$_from = $from; if (!is_array(\$_from) && !is_object(\$_from)) { settype(\$_from, 'array');}\n";
        if ($usesPropTotal) {
            $output .= " \$_smarty_tpl->tpl_vars->___total_{$item} = \$_smarty_tpl->_count(\$_from);\n";
        }
        if ($usesPropIteration) {
            $output .= " \$_smarty_tpl->tpl_vars->___iteration_{$item} = 0;\n";
        }
        if ($usesPropIndex) {
            $output .= " \$_smarty_tpl->tpl_vars->___index_{$item} = -1;\n";
        }
        if ($usesPropShow) {
            $output .= " \$_smarty_tpl->tpl_vars->___show_{$item} = (\$_smarty_tpl->tpl_vars->___total_{$item} > 0);\n";
        }
        if ($has_name) {
            if ($usesSmartyTotal) {
                $output .= " \$_smarty_tpl->tpl_vars->smarty['foreach'][{$name}]['total'] = \$_smarty_tpl->tpl_vars->___total_{$item};\n";
            }
            if ($usesSmartyIteration) {
                $output .= " \$_smarty_tpl->tpl_vars->smarty['foreach'][{$name}]['iteration'] = 0;\n";
            }
            if ($usesSmartyIndex) {
                $output .= " \$_smarty_tpl->tpl_vars->smarty['foreach'][{$name}]['index'] = -1;\n";
            }
            if ($usesSmartyShow) {
                $output .= " \$_smarty_tpl->tpl_vars->smarty['foreach'][{$name}]['show']=(\$_smarty_tpl->tpl_vars->___total_{$item} > 0);\n";
            }
        }
        $output .= "foreach (\$_from as \$_smarty_tpl->tpl_vars->___key_{$item} => \$_smarty_tpl->tpl_vars->{$item}){\n\$_smarty_tpl->tpl_vars->___loop_{$item} = true;\n";
        if ($key != null) {
            $output .= " \$_smarty_tpl->tpl_vars->{$key} = \$_smarty_tpl->tpl_vars->___key_{$item};\n";
        }
        if ($usesPropIteration) {
            $output .= " \$_smarty_tpl->tpl_vars->___iteration_{$item}++;\n";
        }
        if ($usesPropIndex) {
            $output .= " \$_smarty_tpl->tpl_vars->___index_{$item}++;\n";
        }
        if ($usesPropFirst) {
            $output .= " \$_smarty_tpl->tpl_vars->___first_{$item} = \$_smarty_tpl->tpl_vars->___index_{$item} === 0;\n";
        }
        if ($usesPropLast) {
            $output .= " \$_smarty_tpl->tpl_vars->___last_{$item} = \$_smarty_tpl->tpl_vars->___iteration_{$item} === \$_smarty_tpl->tpl_vars->___total_{$item};\n";
        }
        if ($has_name) {
            if ($usesSmartyFirst) {
                $output .= " \$_smarty_tpl->tpl_vars->smarty['foreach'][{$name}]['first'] = \$_smarty_tpl->tpl_vars->___first_{$item};\n";
            }
            if ($usesSmartyIteration) {
                $output .= " \$_smarty_tpl->tpl_vars->smarty['foreach'][{$name}]['iteration']++;\n";
            }
            if ($usesSmartyIndex) {
                $output .= " \$_smarty_tpl->tpl_vars->smarty['foreach'][{$name}]['index']++;\n";
            }
            if ($usesSmartyLast) {
                $output .= " \$_smarty_tpl->tpl_vars->smarty['foreach'][{$name}]['last'] = \$_smarty_tpl->tpl_vars->___last_{$item};\n";
            }
        }
        $output .= "?>";

        return $output;
    }
}

/**
 * Smarty Internal Plugin Compile Foreachelse Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Foreachelse extends Smarty_Internal_CompileBase {

    /**
     * Compiles code for the {foreachelse} tag
     *
     * @param array  $args array with attributes from parser
     * @param object $compiler compiler object
     * @param array  $parameter array with compilation parameter
     * @return string compiled code
     */
    public function compile($args, $compiler, $parameter)
    {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);

        list($openTag, $nocache, $item, $key) = $this->closeTag($compiler, array('foreach'));
        $this->openTag($compiler, 'foreachelse', array('foreachelse', $nocache, $item, $key));

        return "<?php }\nif (!\$_smarty_tpl->tpl_vars->___loop_{$item}) {\n?>";
    }

}

/**
 * Smarty Internal Plugin Compile Foreachclose Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Foreachclose extends Smarty_Internal_CompileBase {

    /**
     * Compiles code for the {/foreach} tag
     *
     * @param array  $args      array with attributes from parser
     * @param object $compiler  compiler object
     * @param array  $parameter array with compilation parameter
     * @return string compiled code
     */
    public function compile($args, $compiler, $parameter)
    {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);
        // must endblock be nocache?
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }

        list($openTag, $compiler->nocache, $item, $key) = $this->closeTag($compiler, array('foreach', 'foreachelse'));

        return "<?php } ?>";
    }

}

?>
