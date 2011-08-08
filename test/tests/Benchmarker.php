<?php

abstract class Benchmarker
{
    protected $_memory = null;
    protected $_duration = null;
    
    protected abstract function run($factor);
    
    public function evaluate($factor)
    {
        $this->prepareRun($factor);
        
        //$memory = memory_get_usage();
        $start = microtime(true);

        $t = $this->run($factor);

        $this->_duration = microtime(true) - $start;
        $this->_memory = memory_get_usage() - INIT_MEMORY;
        
        $this->teardownRun($factor);
        return $t;
    }

    protected function prepareRun($factor)
    {
        // run before each iteration
    }
    
    protected function teardownRun($factor)
    {
        // run after each iteration
    }
        
    public function prepare($factor)
    {
        // run before test iterations
    }
    
    public function teardown($factor)
    {
        // run after test iterations
        exec('rm -rf ' . TMP_DIR .'compiled/*');
        exec('rm -rf ' . TMP_DIR .'cached/*');
    }
    
    public function getMemory()
    {
        return $this->_memory;
    }
    
    public function getDuration()
    {
        return $this->_duration;
    }
}
