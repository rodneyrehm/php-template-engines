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
        $tpl = $this->smarty->createTemplate('assign.' . $factor . '.tpl');
        $tpl->compileTemplateSource();
        // return file_get_contents($tpl->compiled->filepath);
    }

}
