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
        $tpl = new Dwoo_Template_File(TEST_DIR . 'templates/assign.' . $factor . '.tpl');
        $t = $tpl->getCompiledTemplate($this->dwoo);
        //return file_get_contents($t);
    }
}
