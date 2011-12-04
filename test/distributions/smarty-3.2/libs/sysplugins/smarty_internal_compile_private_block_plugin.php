<?php
/**
* Smarty Internal Plugin Compile Block Plugin
*
* Compiles code for the execution of block plugin
*
* @package Smarty
* @subpackage Compiler
* @author Uwe Tews
*/

/**
* Smarty Internal Plugin Compile Block Plugin Class
*
* @package Smarty
* @subpackage Compiler
*/
class Smarty_Internal_Compile_Private_Block_Plugin extends Smarty_Internal_CompileBase {

    /**
    * Attribute definition: Overwrites base class.
    *
    * @var array
    * @see Smarty_Internal_CompileBase
    */
    public $optional_attributes = array('_any');

    /**
    * Compiles code for the execution of block plugin
    *
    * @param array  $args      array with attributes from parser
    * @param object $compiler  compiler object
    * @param array  $parameter array with compilation parameter
    * @param string $tag       name of block plugin
    * @param string $function  PHP function name
    * @return string compiled code
    */
    public function compile($args, $compiler, $parameter, $tag, $function)
    {
        if (!isset($tag[5]) || substr($tag, -5) != 'close') {
            // opening tag of block plugin
            // check and get attributes
            $_attr = $this->getAttributes($compiler, $args);
            if ($_attr['nocache'] === true) {
                $compiler->tag_nocache = true;
            }
            unset($_attr['nocache']);
            $cache_attr = null;
            if ($compiler->template->caching) {
                $result = $this->getAnnotation($function, 'smarty_nocache');
                if ($result) {
                    $compiler->tag_nocache = $compiler->tag_nocache || $result;
                    $compiler->getPlugin(substr($function,16), Smarty::PLUGIN_FUNCTION);
                }
                if ($compiler->tag_nocache || $compiler->nocache) {
                    $cache_attr = $this->getAnnotation($function, 'smarty_cache_attr');
                }
            }
            // convert attributes into parameter string
            $par_string = $this->getPluginParameterString($function,$_attr, $compiler, true, $cache_attr);

            $this->openTag($compiler, $tag, array($par_string, $compiler->nocache));
            // maybe nocache because of nocache variables or nocache plugin
            $compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
            // compile code
            if (is_array($par_string)) {
                // old style with params array
                $output = "<?php \$_smarty_tpl->smarty->_tag_stack[] = array('{$tag}', {$par_string['par']}); \$_block_repeat=true; echo {$function}({$par_string['par']}, null, {$par_string['obj']}, \$_block_repeat);while (\$_block_repeat) { ob_start();?>";
            } else {
                // new style with real parameter
                $par_string = str_replace('__content__', 'null', $par_string);
                $output = "<?php \$_block_repeat=true; \$_smarty_tpl->smarty->_tag_stack[] = array('{$tag}', {$par_string}); echo {$function}({$par_string});while (\$_block_repeat) { ob_start();?>";
            }
        } else {
            // must endblock be nocache?
            if ($compiler->nocache) {
                $compiler->tag_nocache = true;
            }
            // closing tag of block plugin, restore nocache
            list($par_string, $compiler->nocache) = $this->closeTag($compiler, substr($tag, 0, -5));
            // This tag does create output
            $compiler->has_output = true;
            // compile code
            if (!isset($parameter['modifier_list'])) {
                $mod_pre = $mod_post ='';
            } else {
                $mod_pre = ' ob_start(); ';
                $mod_post = 'echo '.$compiler->compileTag('private_modifier',array(),array('modifierlist'=>$parameter['modifier_list'],'value'=>'ob_get_clean()')).';';
            }
            if (is_array($par_string)) {
                // old style with params array
                $output = "<?php \$_block_content = ob_get_clean(); \$_block_repeat=false;".$mod_pre." echo {$function}({$par_string['par']}, \$_block_content, {$par_string['obj']}, \$_block_repeat); ".$mod_post." } array_pop(\$_smarty_tpl->smarty->_tag_stack);?>";
            } else {
                // new style with real parameter
                $par_string = str_replace('__content__', '$_block_content', $par_string);
                $output = "<?php \$_block_content = ob_get_clean(); \$_block_repeat=false;".$mod_pre." echo {$function}({$par_string}); ".$mod_post." } array_pop(\$_smarty_tpl->smarty->_tag_stack);?>";
            }
        }
        return $output . "\n";
    }

}

?>