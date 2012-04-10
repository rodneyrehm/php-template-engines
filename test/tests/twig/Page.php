<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run($factor);
    }
    
    protected function run($factor)
    {
        // wow… even need to write a plugin for is_array() - wtf?
        require_once TEST_DIR . 'page/Twig_Extension_PHP.php';
        $this->twig->addExtension(new Twig_Extension_PHP());
        
        include BASE_DIR .'tests/smarty3/Page/data.php';
        
        $keys = array(
            'navigation',
            'articles',
            'tags',
            'tweets',
            'connect_links',
            'footer_resources',
        );
        
        $data = array();
        foreach ($keys as $key) {
            $data[$key] = $$key;
        }
        
        $template = $this->twig->loadTemplate('/page/page.tpl');
        return $template->render($data);
    }

}
