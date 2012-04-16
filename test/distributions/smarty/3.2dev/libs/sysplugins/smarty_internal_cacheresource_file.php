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
    public function populate(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template) {
        $_source_file_path = str_replace(':', '.', $_template->source->filepath);
        $_cache_id = isset($_template->cache_id) ? preg_replace('![^\w\|]+!', '_', $_template->cache_id) : null;
        $_compile_id = isset($_template->compile_id) ? preg_replace('![^\w\|]+!', '_', $_template->compile_id) : null;
        // if use_sub_dirs build subfolders
        if ($_template->smarty->use_sub_dirs) {
            $_filepath = substr($_template->source->uid, 0, 2) . DS . $_template->source->uid . DS;
            if (isset($_cache_id)) {
                $_cache_id_parts = explode('|', $_cache_id);
                $_cache_id_last = count($_cache_id_parts) - 1;
                $_cache_id_hash = md5($_cache_id_parts[$_cache_id_last]);
                if ($_cache_id_last > 0) {
                    for ($i = 0; $i < $_cache_id_last; $i++) {
                        $_filepath .= $_cache_id_parts[$i] . DS;
                    }
                }
                $_filepath .= substr($_cache_id_hash, 0, 2) . DS
                        . substr($_cache_id_hash, 2, 2) . DS
                        . substr($_cache_id_hash, 4, 2) . DS;
                $_filepath .= $_cache_id_parts[$_cache_id_last];
            }
            $_filepath .= '^' . $_compile_id . '^';
        } else {
            $_filepath = str_replace('|', '.', $_cache_id) . '^' . $_compile_id . '^' . $_template->source->uid . '.';
        }
        $_cache_dir = $_template->smarty->getCacheDir();
        if ($_template->smarty->cache_locking) {
            // create locking file name
            // relative file name?
            if (!preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_cache_dir)) {
                $_lock_dir = rtrim(getcwd(), '/\\') . DS . $_cache_dir;
            } else {
                $_lock_dir = $_cache_dir;
            }
            $cached->lock_id = $_lock_dir . sha1($_cache_id . $_compile_id . $_template->source->uid) . '.lock';
        }
        $cached->filepath = $_cache_dir . $_filepath . basename($_source_file_path) . '.php';
        $cached->timestamp = @filemtime($cached->filepath);
        $cached->exists = !!$cached->timestamp;
    }

    /**
     * populate Cached Object with timestamp and exists from Resource
     *
     * @param Smarty_Template_Cached $cached cached object
     * @return void
     */
    public function populateTimestamp(Smarty_Template_Cached $cached) {
        $cached->timestamp = @filemtime($cached->filepath);
        $cached->exists = !!$cached->timestamp;
    }

    /**
     * Read the cached template and process its header
     *
     * @param Smarty_Internal_Template $_template template object
     * @param Smarty_Template_Cached $cached cached object
     * @return booelan true or false if the cached content does not exist
     */
    public function process(Smarty_Internal_Template $_template, Smarty_Template_Cached $cached=null) {
        $_smarty_tpl = $_template;
        return @include $_template->cached->filepath;
    }

    /**
     * Write the rendered template output to cache
     *
     * @param Smarty_Internal_Template $_template template object
     * @param string                   $content   content to cache
     * @return boolean success
     */
    public function writeCachedContent(Smarty_Internal_Template $_template, $content) {
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
    public function clearAll(Smarty $smarty, $exp_time = null) {
        $save_use_sub_dirs = $smarty->use_sub_dirs;
        $smarty->use_sub_dirs = false;
        $count = $this->clear($smarty, null, null, null, $exp_time);
        $smarty->use_sub_dirs = true;
        $count += $this->clear($smarty, null, null, null, $exp_time);
        $smarty->use_sub_dirs = $save_use_sub_dirs;
        return $count;
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
    public function clear(Smarty $smarty, $resource_name, $cache_id, $compile_id, $exp_time) {
        $_cache_id = isset($cache_id) ? preg_replace('![^\w\|]+!', '_', $cache_id) : null;
        $_compile_id = isset($compile_id) ? preg_replace('![^\w\|]+!', '_', $compile_id) : null;
        $_preg_compile_id = ($_compile_id == null) ? '(.*)?' : preg_quote($_compile_id);
        $_preg_cache_id = '(.*)?';
        $_preg_file = '(.*)?';
        $_cache_dir = $smarty->getCacheDir();
        $_count = 0;
        $_time = time();

        if (isset($resource_name)) {
            $source = Smarty_Resource::source(null, $smarty, $resource_name);
            if ($source->exists) {
                // set basename if not specified
                $_basename = $source->handler->getBasename($source);
                if ($_basename === null) {
                    $_basename = basename(preg_replace('![^\w\/]+!', '_', $source->name));
                }
                // separate (optional) basename by dot
                //                if ($_basename) {
                //                    $_basename = '.' . $_basename;
                //                }
                if ($smarty->use_sub_dirs) {
                    $_preg_file = preg_quote($_basename);
                    $_dir_template = $_cache_dir . substr($source->uid, 0, 2) . DS . $source->uid . DS;
                    // does subdir for template exits?
                    if (!is_dir($_dir_template)) {
                        return 0;
                    }
                    // use template subdir as top level
                    $_dir_array = array($_dir_template);
                } else {
                    $_preg_file = preg_quote($source->uid . '.' . $_basename);
                }
            } else {
                // template does not exist
                return 0;
            }
        }
        // if use_sub_dirs iterate over folder
        if ($smarty->use_sub_dirs) {
            // if no template was specified build top level array for all templates
            if (!isset($resource_name)) {
                $_dir_array = array();
                $_dir_it1 = new DirectoryIterator($_cache_dir);
                foreach ($_dir_it1 as $_dir1) {
                    if (!$_dir1->isDir() || $_dir1->isDot() || substr($_dir1->getBasename(), 0, 1) == '.') {
                        continue;
                    }
                    $_dir_it2 = new DirectoryIterator($_dir1->getPathname());
                    foreach ($_dir_it2 as $_dir2) {
                        if (!$_dir2->isDir() || $_dir2->isDot() || substr($_dir2->getBasename(), 0, 1) == '.') {
                            continue;
                        }
                        $_dir_array[] = $_dir2->getPathname() . DS;
                    }
                }
            }
            $_dir_cache_id = '';
            // build subfolders by cache_id
            if (isset($_cache_id)) {
                $_cache_id_parts = explode('|', $_cache_id);
                $_cache_id_last = count($_cache_id_parts) - 1;
                $_cache_id_hash = md5($_cache_id_parts[$_cache_id_last]);
                // lower levels of structured cache_id
                if ($_cache_id_last > 0) {
                    for ($i = 0; $i < $_cache_id_last; $i++) {
                        $_dir_cache_id .= $_cache_id_parts[$i] . DS;
                    }
                }
                // hash for highest level of cache_id
                $_dir_cache_id2 = $_dir_cache_id . substr($_cache_id_hash, 0, 2) . DS
                        . substr($_cache_id_hash, 2, 2) . DS
                        . substr($_cache_id_hash, 4, 2) . DS;
                $_preg_cache_id2 = preg_quote($_cache_id_parts[$_cache_id_last]);
                // add highest level
                $_dir_cache_id .= $_cache_id_parts[$_cache_id_last] . DS;
            }
            // loop over templates
            foreach ($_dir_array as $dir) {
                $_dirs = array($dir . $_dir_cache_id, isset($_cache_id) ? $dir . $_dir_cache_id2 : null);
                $_deleted = array(false, false);
                for ($i = 0; $i < 2; $i++) {
                    if ($i == 0) {
                        if (!is_dir($_dirs[$i])) {
                            continue;
                        }
                        // folder for lower levels is present or no cache_id specified
                        $_cacheDirs1 = new RecursiveDirectoryIterator($_dirs[$i]);
                        $_cacheDirs = new RecursiveIteratorIterator($_cacheDirs1, RecursiveIteratorIterator::CHILD_FIRST);
                        $_preg_cache_id = '(.*)?';
                    } else if (isset($_cache_id)) {
                        if (!is_dir($_dirs[$i])) {
                            continue;
                        }
                        // folder with highest level hash is present
                        $_cacheDirs = new DirectoryIterator($_dirs[$i]);
                        $_preg_cache_id = $_preg_cache_id2;
                    }
                    if ($i == 0 || isset($_cache_id)) {
                        $regex = "/^{$_preg_cache_id}\^{$_preg_compile_id}\^{$_preg_file}\.php\$/i";
                        foreach ($_cacheDirs as $_file) {
                            $filename = $_file->getBasename();
                            $path = $_file->getPathname();
                            if (substr($filename, 0, 1) == '.' || strpos($_file, '.svn') !== false)
                                continue;
                            // directory ?
                            if ($_file->isDir()) {
                                if (!$_cacheDirs->isDot()) {
                                    // delete folder if empty
                                    @rmdir($_file->getPathname());
                                    continue;
                                }
                            }
                            // does file match selections?
                            if (!preg_match($regex, $filename, $matches)) {
                                continue;
                            }
                            // expired ?
                            if (isset($exp_time) && $_time - @filemtime($path) < $exp_time) {
                                continue;
                            }
                            $_count += @unlink($path) ? 1 : 0;
                            $_deleted[$i] = true;
                        }
                    }
                    unset($_cacheDirs, $_cacheDirs1);
                    if ($_deleted[$i]) {
                        $_dir = $_dirs[$i];
                        while ($_dir != $_cache_dir) {
                            if (@rmdir($_dir) === false) {
                                break;
                            }
                            $_dir = substr($_dir, 0, strrpos(substr($_dir, 0, -1), DS) + 1);
                        }
                    }
                }
            }
        } else {
            if (isset($_cache_id)) {
                $_preg_cache_id = preg_quote(str_replace('|', '.', $_cache_id)) . '(?=[\^\.])(.*)?';
            }
            $regex = "/^{$_preg_cache_id}\^{$_preg_compile_id}\^{$_preg_file}\.php\$/i";
            $_cacheDirs = new DirectoryIterator($_cache_dir);
            foreach ($_cacheDirs as $_file) {
                $filename = $_file->getBasename();
                $path = $_file->getPathname();
                // directory ?
                if ($_file->isDir()) {
                    continue;
                }
                // does file match selections?
                if (!preg_match($regex, $filename, $matches)) {
                    continue;
                }
                // expired ?
                if (isset($exp_time) && $_time - @filemtime($path) < $exp_time) {
                    continue;
                }
                $_count += @unlink($path) ? 1 : 0;
            }
        }
        return $_count;
    }

    /**
     * Check is cache is locked for this template
     *
     * @param Smarty $smarty Smarty object
     * @param Smarty_Template_Cached $cached cached object
     * @return booelan true or false if cache is locked
     */
    public function hasLock(Smarty $smarty, Smarty_Template_Cached $cached) {
        if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
            clearstatcache(true, $cached->lock_id);
        } else {
            clearstatcache();
        }
        $t = @filemtime($cached->lock_id);
        return $t && (time() - $t < $smarty->locking_timeout);
    }

    /**
     * Lock cache for this template
     *
     * @param Smarty $smarty Smarty object
     * @param Smarty_Template_Cached $cached cached object
     */
    public function acquireLock(Smarty $smarty, Smarty_Template_Cached $cached) {
        $cached->is_locked = true;
        touch($cached->lock_id);
    }

    /**
     * Unlock cache for this template
     *
     * @param Smarty $smarty Smarty object
     * @param Smarty_Template_Cached $cached cached object
     */
    public function releaseLock(Smarty $smarty, Smarty_Template_Cached $cached) {
        $cached->is_locked = false;
        @unlink($cached->lock_id);
    }

}
