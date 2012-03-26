<?php

require_once DISTRIBUTION_DIR . 'lib/Twig/Autoloader.php';
Twig_Autoloader::register();

abstract class BenchmarkBase extends Benchmarker
{
    protected $twig = null;
    
    public function __construct()
    {
        $loader = new Twig_Loader_Filesystem(TEST_DIR . 'templates/');
        $this->twig = new Twig_Environment($loader, array(
          'cache' => TMP_DIR . 'compiled/',
        ));
    }
}