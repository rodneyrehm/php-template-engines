<?php

class Benchmark extends BenchmarkBase
{
    public function prepare($factor)
    {
        $this->run($factor);
    }
    
    protected function run($factor)
    {
        $this->tal->setTemplate('page/page.tpl');

        include BASE_DIR .'tests/_data/Page/data.php';
        
        // compensate for inability to loop in an attribute-value
        foreach ($articles as &$article) {
            $article['_hentry_class'] = $article['hentry'] ? 'hentry' : '';

            $article['_categories_class'] = array_keys($article['categories']);
            if ($article['_categories_class']) {
                $article['_categories_class'] = 'category-' . join(" category-", $article['_categories_class']);
            } else {
                $article['_categories_class'] = '';
            }
            
            $article['_tags_class'] = array_keys($article['tags']);
            if ($article['_tags_class']) {
                $article['_tags_class'] = 'tag-' . join(" tag-", $article['_tags_class']);
            } else {
                $article['_tags_class'] = '';
            }
        }
        
        foreach ($footer_resources as &$resource) {
            $resource['_our_content'] = $resource['is_our_content'] ? 'our-content' : '';
        }

        $keys = array(
            'navigation',
            'articles',
            'tags',
            'tweets',
            'connect_links',
            'footer_resources',
        );
        
        foreach ($keys as $key) {
            $this->tal->$key = $$key;
        }
        
        $this->tal->ficken = "lalalalalalala";
        return $this->tal->execute();
    }
    
}
