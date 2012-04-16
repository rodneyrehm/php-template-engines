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
        if (method_exists($tpl, 'compileTemplateSource')) {
            $tpl->compileTemplateSource();
        } else {
            $tpl->compiler->compileTemplateSource($tpl);
        }
        // return file_get_contents($tpl->compiled->filepath);
    }

}
