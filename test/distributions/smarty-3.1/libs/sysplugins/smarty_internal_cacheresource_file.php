<?php
/**
 * Smarty Internal Plugin CacheResource File
 *
 * @package Smarty
 * @subpackage Cacher
 * @author Uwe Tews
 * @author Rodney Rehm
 */

/**
 * This class does contain all necessary methods for the HTML cache on file system
 *
 * Implements the file system as resource for the HTML cache Version ussing nocache inserts.
 *
 * @package Smarty
 * @subpackage Cacher
 */
class Smarty_Internal_CacheResource_File extends Smarty_CacheResource {

    /**
     * populate Cached Object with meta data from Resource
     *
     * @param Smarty_Template_Cached   $cached    cached object
     * @param Smarty_Internal_Template $_template template object
     * @return void
     */
    public function populate(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template)
    {
        $_source_file_path = str_replace(':', '.', $_template->source->filepath);
        $_cache_id = isset($_template->cache_id) ? preg_replace('![^\w\|]+!', '_', $_template->cache_id) : null;
        $_compile_id = isset($_template->compile_id) ? preg_replace('![^\w\|]+!', '_', $_template->compile_id) : null;
        $_filepath = $_template->source->uid;
        // if use_sub_dirs, break file into directories
        if ($_template->smarty->use_sub_dirs) {
            $_filepath = substr($_filepath, 0, 2) . DS
                . substr($_filepath, 2, 2) . DS
                . substr($_filepath, 4, 2) . DS
                . $_filepath;
        }
        $_compile_dir_sep = $_template->smarty->use_sub_dirs ? DS : '^';
        if (isset($_cache_id)) {
            $_cache_id = str_replace('|', $_compile_dir_sep, $_cache_id) . $_compile_dir_sep;
        } else {
            $_cache_id = '';
        }
        if (isset($_compile_id)) {
            $_compile_id = $_compile_id . $_compile_dir_sep;
        } else {
            $_compile_id = '';
        }
        $_cache_dir = $_template->smarty->getCacheDir();
        $cached->filepath = $_cache_dir . $_cache_id . $_compile_id . $_filepath . '.' . basename($_source_file_path) . '.php';
        $cached->timestamp = @filemtime($cached->filepath);
        $cached->exists = !!$cached->timestamp;
    }

    /**
     * populate Cached Object with timestamp and exists from Resource
     *
     * @param Smarty_Template_Cached $cached cached object
     * @return void
     */
    public function populateTimestamp(Smarty_Template_Cached $cached)
    {
        $cached->timestamp = @filemtime($cached->filepath);
        $cached->exists = !!$cached->timestamp;
    }

    /**
     * Read the cached template and process its header
     *
     * @param Smarty_Internal_Template $_template template object
     */
    public function process(Smarty_Internal_Template $_template)
    {
        $_smarty_tpl = $_template;
        include $_template->cached->filepath;
    }

    /**
     * Write the rendered template output to cache
     *
     * @param Smarty_Internal_Template $_template template object
     * @param string                   $content   content to cache
     * @return boolean success
     */
    public function writeCachedContent(Smarty_Internal_Template $_template, $content)
    {
        if (Smarty_Internal_Write_File::writeFile($_template->cached->filepath, $content, $_template->smarty) === true) {
            $_template->cached->timestamp = filemtime($_template->cached->filepath);
            $_template->cached->exists = !!$_template->cached->timestamp;
            return true;
        }
        return false;
    }

    /**
     * Empty cache
     *
     * @param Smarty_Internal_Template $_template template object
     * @param integer                  $exp_time  expiration time (number of seconds, not timestamp)
     * @return integer number of cache files deleted
     */
    public function clearAll(Smarty $smarty, $exp_time = null)
    {
        return $this->clear($smarty, null, null, null, $exp_time);
    }

    /**
     * Empty cache for a specific template
     *
     * @param Smarty  $_template     template object
     * @param string  $resource_name template name
     * @param string  $cache_id      cache id
     * @param string  $compile_id    compile id
     * @param integer $exp_time      expiration time (number of seconds, not timestamp)
     * @return integer number of cache files deleted
    */
    public function clear(Smarty $smarty, $resource_name, $cache_id, $compile_id, $exp_time)
    {
        $_cache_id = isset($cache_id) ? preg_replace('![^\w\|]+!', '_', $cache_id) : null;
        $_compile_id = isset($compile_id) ? preg_replace('![^\w\|]+!', '_', $compile_id) : null;
        $_dir_sep = $smarty->use_sub_dirs ? '/' : '^';
        $_compile_id_offset = $smarty->use_sub_dirs ? 3 : 0;
        $_dir = $smarty->getCacheDir();
        $_dir_length = strlen($_dir);
        if (isset($_cache_id)) {
            $_cache_id_parts = explode('|', $_cache_id);
            $_cache_id_parts_count = count($_cache_id_parts);
            if ($smarty->use_sub_dirs) {
                foreach ($_cache_id_parts as $id_part) {
                    $_dir .= $id_part . DS;
                }
            }
        }
        if (isset($resource_name)) {
            $_save_stat = $smarty->caching;
            $smarty->caching = true;
            $tpl = new $smarty->template_class($resource_name, $smarty);
            $smarty->caching = $_save_stat;
            if ($tpl->source->exists) {
                $_resourcename_parts = basename(str_replace('^', '/', $tpl->cached->filepath));
                // remove from template cache
                unset($smarty->template_objects[sha1($tpl->template_resource . $tpl->cache_id . $tpl->compile_id)]);
            } else {
                // remove from template cache
                unset($smarty->template_objects[sha1($tpl->template_resource . $tpl->cache_id . $tpl->compile_id)]);
                return 0;
            }
        }
        $_count = 0;
        $_time = time();
        if (file_exists($_dir)) {
            $_cacheDirs = new RecursiveDirectoryIterator($_dir);
            $_cache = new RecursiveIteratorIterator($_cacheDirs, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($_cache as $_file) {
                if (substr($_file->getBasename(),0,1) == '.' || strpos($_file, '.svn') !== false) continue;
                // directory ?
                if ($_file->isDir()) {
                    if (!$_cache->isDot()) {
                        // delete folder if empty
                        @rmdir($_file->getPathname());
                    }
                } else {
                    $_parts = explode($_dir_sep, str_replace('\\', '/', substr((string)$_file, $_dir_length)));
                    $_parts_count = count($_parts);
                    // check name
                    if (isset($resource_name)) {
                        if ($_parts[$_parts_count-1] != $_resourcename_parts) {
                            continue;
                        }
                    }
                    // check compile id
                    if (isset($_compile_id) && (!isset($_parts[$_parts_count-2 - $_compile_id_offset]) || $_parts[$_parts_count-2 - $_compile_id_offset] != $_compile_id)) {
                        continue;
                    }
                    // check cache id
                    if (isset($_cache_id)) {
                        // count of cache id parts
                        $_parts_count = (isset($_compile_id)) ? $_parts_count - 2 - $_compile_id_offset : $_parts_count - 1 - $_compile_id_offset;
                        if ($_parts_count < $_cache_id_parts_count) {
                            continue;
                        }
                        for ($i = 0; $i < $_cache_id_parts_count; $i++) {
                            if ($_parts[$i] != $_cache_id_parts[$i]) continue 2;
                        }
                    }
                    // expired ?
                    if (isset($exp_time) && $_time - @filemtime($_file) < $exp_time) {
                        continue;
                    }
                    $_count += @unlink((string) $_file) ? 1 : 0;
                }
            }
        }
        return $_count;
    }

}

?>