<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run(1);
    }
    
    protected function run($factor)
    {
        $this->tpl->assign('values', range(1, $factor));
        return $this->tpl->draw('loop', true);
    }
}
