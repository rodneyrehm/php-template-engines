<?php

/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {exception} plugin
 *
 * Type:     function<br>
 * Name:     exception<br>
 * Purpose:  throw a SnartyRunTimeException
 *
 * @link http://www.smarty.net/docs/en/language.function.exception.tpl {exception}
 *       (Smarty online manual)
 * @author Uwe Tews
 * @param Smarty_Internal_Template $template template object
 * @param string  $message   exception messsage
 */
function smarty_function_exception(Smarty_Internal_Template $template, $message = 'User Exception') {
    throw new SmartyRunTimeException($message, $template);
    return;
}
