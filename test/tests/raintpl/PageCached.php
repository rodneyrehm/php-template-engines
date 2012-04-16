<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run($factor);
    }
    
    protected function run($factor)
    {
        $cache = $this->tpl->cache('page/page');
        if ($cache) {
            return $cache;
        }
        
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
            $this->tpl->assign($key, $$key);
        }

        return $this->tpl->draw('page/page', true);
    }
}

function escape($string) {
    return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
}
