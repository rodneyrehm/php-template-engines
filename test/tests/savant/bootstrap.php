<?php

require_once DISTRIBUTION_DIR . 'Savant3.php';

abstract class BenchmarkBase extends Benchmarker
{
    protected $savant = null;
    
    public function __construct()
    {
        $this->savant = new Savant3();
        $this->savant->setPath('template', TEST_DIR . 'templates/');
        // no compile dir?
    }
}