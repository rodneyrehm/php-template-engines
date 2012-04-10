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
        
        $data = new Dwoo_Data();
        foreach ($keys as $key) {
            $data->assign($key, $$key);
        }
 
        $tpl = new Dwoo_Template_File(TEST_DIR . 'templates/page/page.tpl');
        return $this->dwoo->get($tpl, $data);
    }
}
