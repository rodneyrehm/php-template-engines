<?php

require_once DISTRIBUTION_DIR . 'classes/PHPTAL.php';

abstract class BenchmarkBase extends Benchmarker
{
    protected $tal = null;
    
    public function __construct()
    {
        $this->tal = new PHPTAL(); 
        $this->tal->setTemplateRepository(TEST_DIR . 'templates/');
        $this->tal->setPhpCodeDestination(TMP_DIR . 'compiled/');
    }
}