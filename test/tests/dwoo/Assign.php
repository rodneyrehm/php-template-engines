<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run($factor);
    }
    
    protected function run($factor)
    {
        $data = new Dwoo_Data();
        for ($i=1; $i <= $factor; $i++) {
            $data->assign('value_'. $i, $i);
        }
        $tpl = new Dwoo_Template_File(TEST_DIR . 'templates/assign.' . $factor . '.tpl');
        return $this->dwoo->get($tpl, $data);
    }
}
