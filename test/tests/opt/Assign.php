<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run($factor);
    }
    
    protected function run($factor)
    {
        $view = new Opt_View('assign.' . $factor . '.tpl');
        for ($i=1; $i <= $factor; $i++) {
            $view->{'value_'. $i} = $i;
        }
        $output = new Opt_Output_Return;
    	return $output->render($view);
    }
}
