<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run($factor);
    }
    
    protected function run($factor)
    {
        $this->tal->setTemplate('assign.' . $factor . '.tpl');
        for ($i=1; $i <= $factor; $i++) {
            $this->tal->{'value_'. $i} = $i;
        }
        return $this->tal->execute();
    }
}
