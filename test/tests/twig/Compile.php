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
        $this->twig->loadTemplate('assign.' . $factor . '.tpl');
        // file_get_contents($this->twig->getCacheFilename('assign.' . $factor . '.tpl'));
    }

}
