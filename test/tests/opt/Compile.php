<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->teardown(1);
    }
    
    protected function run($factor)
    {
        $this->teardown(1);
        $view = new Opt_View('assign.' . $factor . '.tpl');
        // had to be made public in lib/Opt/Class.php
        $t = $view->_preprocess();
        //return file_get_contents( $this->opt->compileDir . $t[0]);
    }
}
