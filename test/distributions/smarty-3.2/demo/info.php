<?php

require_once dirname(__FILE__) . '/../libs/Smarty.class.php';

ini_set('pcre.backtrack_limit', -1);

$smarty = new Smarty();
$smarty->setTemplateDir(array(
    'mine' => '../../my-templates',
    'default' => './templates',
));
$smarty->addPluginsDir('./plugins');
$smarty->registerPlugin('modifier', 'strlen', 'strlen');
$smarty->registerPlugin('modifier', 'strlen2', array('InfoFoo', 'strlen'));
$smarty->registerPlugin('modifier', 'strlen3', function($string){ return strlen($string); });

$smarty->autoload_filters['output'] = array('trimwhitespace');

$smarty->registerFilter('pre', array('InfoFoo', 'prefilterThingie'));
//$smarty->registerFilter('pre', function(){ });
//$smarty->registerFilter('pre', function(){ });

class InfoFoo {
    public static function strlen($string)
    {
        return strlen($string);
    }
    public static function prefilterThingie()
    {
        
    }
}

$smarty->right_delimiter = $smarty->left_delimiter;
$smarty->caching = 1;
$smarty->cache_lifetime = 336699;

$tpl = $smarty->createTemplate('eval:foobar');
$tpl->caching = 2;
echo $tpl->info();