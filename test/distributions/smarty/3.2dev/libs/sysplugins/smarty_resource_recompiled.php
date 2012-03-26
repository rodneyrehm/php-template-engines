<?php

/**
 * Smarty Resource Plugin
 *
 * @package Smarty
 * @subpackage TemplateResources
 * @author Rodney Rehm
 */

/**
 * Smarty Resource Plugin
 *
 * Base implementation for resource plugins that don't compile cache
 *
 * @package Smarty
 * @subpackage TemplateResources
 */
abstract class Smarty_Resource_Recompiled extends Smarty_Resource {

    /**
     * populate Compiled Object with compiled filepath
     *
     * @param Smarty_Compiled $compiled compiled object
     * @param Smarty_Internal_Template|Smarty_Internal_Cache $_object template or cache object object
     * @return void
     */
    public function populateCompiledFilepath(Smarty_Compiled $compiled, $_object) {
        $compiled->filepath = false;
        $compiled->timestamp = false;
        $compiled->exists = false;
    }

}
