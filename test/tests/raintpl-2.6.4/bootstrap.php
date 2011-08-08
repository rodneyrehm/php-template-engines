<?php

require_once DISTRIBUTION_DIR . 'inc/rain.tpl.class.php';
raintpl::configure("base_url", null );
raintpl::configure("tpl_dir", TEST_DIR . "templates/" );
raintpl::configure("cache_dir", TMP_DIR . "compiled/" );

abstract class BenchmarkBase extends Benchmarker
{
    protected $tpl = null;
    
    public function __construct()
    {
    	$this->tpl = new RainTPL;
    }
}