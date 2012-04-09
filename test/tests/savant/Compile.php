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

        $this->savant->fetch('assign.' . $factor . '.tpl'); 
    }
}
