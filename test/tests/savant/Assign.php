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
            $this->savant->{'value_'. $i} = $i;
        }
        return $this->savant->fetch('assign.' . $factor . '.tpl'); 
    }
}
