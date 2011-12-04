<?php
/**
* Smarty Internal Plugin Compile Function Plugin
*
* Compiles code for the execution of function plugin
*
* @package Smarty
* @subpackage Compiler
* @author Uwe Tews
*/

/**
* Smarty Internal Plugin Compile Function Plugin Class
*
* @package Smarty
* @subpackage Compiler
*/
class Smarty_Internal_Compile_Private_Function_Plugin extends Smarty_Internal_CompileBase {

    /**
    * Attribute definition: Overwrites base class.
    *
    * @var array
    * @see Smarty_Internal_CompileBase
    */
    public $required_attributes = array();
    /**
    * Attribute definition: Overwrites base class.
    *
    * @var array
    * @see Smarty_Internal_CompileBase
    */
    public $optional_attributes = array('_any');

    /**
    * Compiles code for the execution of function plugin
    *
    * @param array $args array with attributes from parser
    * @param object $compiler compiler object
    * @param array $parameter array with compilation parameter
    * @param string $tag name of function plugin
    * @param string $function PHP function name
    * @return string compiled code
    */
    public function compile($args, $compiler, $parameter, $tag, $function)
    {
        // This tag does create output
        $compiler->has_output = true;

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
        $result = $this->getPluginParameterString($function,$_attr, $compiler, false, $cache_attr);
        // compile code
        $output = "<?php echo {$function}({$result});?>\n";
        return $output;
    }
}
?>