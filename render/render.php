<?php
$base = dirname(__FILE__) . '/../';
require_once $base . 'test/distributions/smarty/3.1.8/libs/Smarty.class.php';

$smarty = new Smarty();
$smarty
    ->setTemplateDir( $base . 'render/templates/' )
    ->setCompileDir( $base . 'test/tmp/compiled/' );

require_once $base .'test/data.php';
require_once $base .'test/tmp/results.php';

$smarty->assign('charttypes', $charttypes);

// clean output directory
exec('rm -rf ' . $base . 'htdocs/distribution/*');
exec('rm -rf ' . $base . 'htdocs/*.html');

$__tests = array();
$__distributions = array();

foreach ($tests as $test => $t) {
    $__tests[$test] = array(
        'test' => $test,
        'data' => array(
            'memory' => array(),
            'duration' => array(),
        ),
    );
    
    foreach ($totals[$test] as $distribution => $versions) {
        foreach ($versions as $version => $factors) {
            $_data = array(
                'memory' => array(),
                'duration' => array(),
                'factor' => array(),
            );

            foreach ($factors as $factor => $data) {
                $_data['memory'][$factor] = $data['memory'];
                $_data['duration'][$factor] = $data['duration'];
            }
            
            $__distributions[$distribution][$version][] = array(
                'test' => $test,
                'distribution' => $distribution,
                'version' => $version,
                'data' => $_data,
            );
        }
        
        foreach ($__distributions as &$__versions) {
            krsort($__versions);
        }
    }
}

ksort($__distributions);

$_tests_current = $__tests;
foreach ($__distributions as $distribution => $t) {
    $versions = array_keys($t);
    
    $_hastest = false;
    $_tests = $__tests;
    
    foreach ($t as $version => $data) {
        // generate distribution/version
        $smarty->assign('tests', $data);
        $smarty->assign('version', $version);
        $smarty->assign('distribution', $distribution);
        $smarty->assign('versions', $versions);
        $t = $smarty->fetch('distribution.tpl');
        $path = $base . 'htdocs/distribution/' . $distribution . '/';
        @mkdir($path, 0777, true);
        file_put_contents($path . $version  . '.html', $t);
        
        // generate distribution/index
        foreach ($data as $t) {
            $_tests[$t['test']]['data']['memory'][$version] = $t['data']['memory'];
            $_tests[$t['test']]['data']['duration'][$version] = $t['data']['duration'];
            if (!$_hastest) {
                $_tests_current[$t['test']]['data']['memory'][$distribution] = $t['data']['memory'];
                $_tests_current[$t['test']]['data']['duration'][$distribution] = $t['data']['duration'];
            }
        }
        
        $_hastest = true;
    }
      
    $smarty->assign('tests', $_tests);
    $t = $smarty->fetch('test.tpl');
    file_put_contents($base . 'htdocs/distribution/' . $distribution . '/index.html', $t);
}



$smarty->assign('tests', $_tests_current);
$t = $smarty->fetch('test.tpl');
file_put_contents($base . 'htdocs/tests.html', $t);


foreach ($distributions as &$versions) {
    krsort($versions);
}

$smarty->assign('distributions', $distributions);
$t = $smarty->fetch('index.tpl');
file_put_contents($base . 'htdocs/index.html', $t);
