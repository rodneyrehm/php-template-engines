<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run(1);
    }
    
    protected function run($factor)
    {
        $this->tal->setTemplate('loop.tpl');
        $this->tal->values = range(1, $factor);
        return $this->tal->execute();
    }
}
