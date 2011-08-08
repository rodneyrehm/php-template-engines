<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run($factor);
    }
    
    protected function run($factor)
    {
        $this->smarty->assign('values', range(1, $factor));
        return $this->smarty->fetch('loop-include.tpl');
    }
    
    /*
    public function teardown($factor)
    {
        // cleanup after test
        $this->smarty->clearCompiledTemplate('loop.tpl');
    }
    */
}
