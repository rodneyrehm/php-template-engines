<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run(1);
    }
    
    protected function run($factor)
    {
        $view = new Opt_View('loop.tpl');
        $view->values = range(1, $factor);

        $output = new Opt_Output_Return;
    	return $output->render($view);
    }
}
