<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run($factor);
    }
    
    protected function run($factor)
    {
        for ($i=1; $i <= $factor; $i++) {
            $this->smarty->assign('value_'. $i, $i);
        }
        return $this->smarty->fetch('assign.' . $factor . '.tpl');
    }
    
    /*
    public function teardown($factor)
    {
        // cleanup after test
        $this->smarty->clearCompiledTemplate('loop.tpl');
    }
    */
}
