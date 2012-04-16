<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run($factor);
    }
    
    protected function run($factor)
    {
        include BASE_DIR .'tests/smarty3/Page/data.php';
        
        $keys = array(
            'navigation',
            'articles',
            'tags',
            'tweets',
            'connect_links',
            'footer_resources',
        );
        
        foreach ($keys as $key) {
            $this->savant->$key = $$key;
        }
        
        return $this->savant->fetch('page/page.tpl'); 
    }
}
