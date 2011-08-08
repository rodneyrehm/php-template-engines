<?php
$base = dirname(__FILE__) . '/../';
require_once $base . 'test/distributions/smarty-3.1/libs/Smarty.class.php';

$smarty = new Smarty();
$smarty
    ->setTemplateDir( $base . 'render/templates/' )
    ->setCompileDir( $base . 'test/tmp/compiled/' );

require_once $base .'test/data.php';
require_once $base .'test/tmp/results.php';

$__tests = array();
$__distributions = array();

foreach ($tests as $test => $t) {
    $_tests = array(
        'test' => $test,
        'data' => array(
            'memory' => array(),
            'duration' => array(),
        ),
    );
    foreach ($totals[$test] as $distribution => $factors) {
        $_distribution = array(
            'memory' => array(),
            'duration' => array(),
        );
        $data = array();
        foreach ($factors as $factor => $data) {
            if (empty($_tests['data']['memory'][$distribution])) {
                $_tests['data']['memory'][$distribution] = array();
                $_tests['data']['duration'][$distribution] = array();
            }
            $_tests['data']['memory'][$distribution][$factor] = $data['memory'];
            $_tests['data']['duration'][$distribution][$factor] = $data['duration'];
            
            $_distribution['memory'][$factor] = $data['memory'];
            $_distribution['duration'][$factor] = $data['duration'];
        }
        
        if (empty($__distributions[$distribution])) {
            $__distributions[$distribution] = array();
        }
        
        $__distributions[$distribution][] = array(
            'test' => $test,
            'distribution' => $distribution,
            'data' => $_distribution,
        );
    }

    $__tests[] = $_tests;
}

foreach ($__distributions as $distribution => $data) {
    $smarty->assign('tests', $data);
    $t = $smarty->fetch('distribution.tpl');
    file_put_contents($base . 'htdocs/' . $distribution . '.html', $t);
}

$smarty->assign('tests', $__tests);
$t = $smarty->fetch('test.tpl');
file_put_contents($base . 'htdocs/tests.html', $t);
