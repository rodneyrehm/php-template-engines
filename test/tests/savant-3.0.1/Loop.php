<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run(1);
    }
    
    protected function run($factor)
    {
        $this->savant->assign('values', range(1, $factor));
        return $this->savant->fetch('loop.tpl'); 
    }
}
