<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run($factor);
    }
    
    protected function run($factor)
    {
        $this->smarty->assign('title', "hello world");
        return $this->smarty->fetch('ti.' . $factor . '.tpl');
    }
    
    /*
    public function teardown($factor)
    {
        // cleanup after test
        $this->smarty->clearCompiledTemplate('loop.tpl');
    }
    */
}
