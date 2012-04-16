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
        // had to be made public in inc/rain.tpl.class.php
        $this->tpl->check_template('assign.' . $factor);
        //return file_get_contents($this->tpl->tpl['compiled_filename']);
    }
}

