<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run($factor);
    }
    
    protected function run($factor)
    {
        $template = $this->twig->loadTemplate('assign.' . $factor . '.tpl');
        $data = array();
        for ($i=1; $i <= $factor; $i++) {
            $data['value_'. $i] = $i;
        }
        return $template->render($data);
    }

}
