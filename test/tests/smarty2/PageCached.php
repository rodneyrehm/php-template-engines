<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run($factor);
    }
    
    protected function run($factor)
    {
        $this->smarty->caching = true;

        if (!$this->smarty->is_cached('page/page.tpl')) {
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
                $this->smarty->assign($key, $$key);
            }
        } 

        return $this->smarty->fetch('page/page.tpl');
    }
}
