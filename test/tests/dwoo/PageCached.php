<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run($factor);
    }
    
    protected function run($factor)
    {
        $this->dwoo->setCacheTime(3600);
        $data = new Dwoo_Data();
        $tpl = new Dwoo_Template_File(TEST_DIR . 'templates/page/page.tpl');
        if (!$this->dwoo->isCached($tpl)) {
            include BASE_DIR .'tests/_data/Page/data.php';
        
            $keys = array(
                'navigation',
                'articles',
                'tags',
                'tweets',
                'connect_links',
                'footer_resources',
            );
        
            foreach ($keys as $key) {
                $data->assign($key, $$key);
            }
        }
        return $this->dwoo->get($tpl, $data);
    }
}
