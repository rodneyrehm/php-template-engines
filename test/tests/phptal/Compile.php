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
        $this->tal->setTemplate('assign.' . $factor . '.tpl');
        $this->tal->prepare();
        //return file_get_contents($this->tal->getCodePath());
    }
}
