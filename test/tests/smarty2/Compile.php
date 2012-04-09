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
        $template = 'assign.' . $factor . '.tpl';
        $path = $this->smarty->_get_compile_path($template);
        $this->smarty->_compile_resource($template, $path);
        //return file_get_contents($path);
    }

}
