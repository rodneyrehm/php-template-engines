<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run(1);
    }
    
    protected function run($factor)
    {
        $template = $this->twig->loadTemplate('loop-include.tpl');
        $data = array('values' => range(1, $factor));
        return $template->render($data);
    }

}
