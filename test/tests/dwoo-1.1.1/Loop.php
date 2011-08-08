<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run(1);
    }
    
    protected function run($factor)
    {
        $data = new Dwoo_Data();
        $data->assign('values', range(1, $factor));
        $tpl = new Dwoo_Template_File(TEST_DIR . 'templates/loop.tpl');
        return $this->dwoo->get($tpl, $data);
    }
}
