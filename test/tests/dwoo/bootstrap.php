<?php

require_once DISTRIBUTION_DIR . 'dwooAutoload.php';

abstract class BenchmarkBase extends Benchmarker
{
    protected $dwoo = null;
    
    public function __construct()
    {
        $this->dwoo = new Dwoo();
        $this->dwoo->setCompileDir(TMP_DIR . 'compiled');
        $this->dwoo->setCacheDir(TMP_DIR . 'compiled');
        // no templateDir? huh? supposed to go all absolute here?
    }
}