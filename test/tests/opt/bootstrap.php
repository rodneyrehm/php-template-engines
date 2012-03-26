<?php

require_once DISTRIBUTION_DIR . 'lib/Opl/Base.php';
Opl_Loader::setDirectory(DISTRIBUTION_DIR . 'lib');
Opl_Loader::register();

abstract class BenchmarkBase extends Benchmarker
{
    protected $opt = null;
    
    public function __construct()
    {
    	$this->opt = new Opt_Class;
    	$this->opt->sourceDir = TEST_DIR . 'templates/';
    	$this->opt->compileDir = TMP_DIR . 'compiled/';
    	$tpl->contentType = Opt_Output_Http::XHTML;
        $tpl->charset = 'utf-8';
    	$this->opt->setup();
    }
}
