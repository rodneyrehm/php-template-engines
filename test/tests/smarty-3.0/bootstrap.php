<?php

require_once DISTRIBUTION_DIR . 'libs/Smarty.class.php';

abstract class BenchmarkBase extends Benchmarker
{
    protected $smarty = null;
    
    public function __construct()
    {
        $this->smarty = new Smarty();
        $this->smarty->template_dir = TEST_DIR . 'templates/';
        $this->smarty->compile_dir = TMP_DIR . 'compiled/';
        $this->smarty->cache_dir = TMP_DIR . 'cached/';
        $this->smarty->caching = 0;
    }
}