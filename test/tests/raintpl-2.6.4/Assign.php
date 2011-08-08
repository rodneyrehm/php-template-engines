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
            $this->tpl->assign('value_'. $i, $i);
        }
        return $this->tpl->draw('assign.' . $factor, true);
    }
}
