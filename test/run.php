<?php

define('HTTP_BASE_PATH', 'test.dev/php-template-engines/');
define('ITERATIONS', 100);
define('QUANTILE', (int) ITERATIONS / 4);
define('ELEMENTS', QUANTILE * 2);

ini_set( 'allow_url_fopen', true );

require_once dirname(__FILE__) . '/data.php';

function invoke($distribution, $test, $factor, $method)
{
    $url = 'http://' . HTTP_BASE_PATH 
        . 'test/invoke.php?distribution=' . $distribution 
        . '&test=' . $test 
        . '&factor=' . $factor 
        . '&method=' . $method;

    $t = file_get_contents($url);
    
    if (!$t || !strncmp($t, 'error:', 6)) {
        throw new Exception("Failed " . $url);
    }
    
    if ($method != 'evaluate') {
        return null;
    }
    
    $t = explode("\n", $t);
    return array(
        'memory' => (double) $t[0] / 1024 / 1024,
        'duration' => (double) $t[1],
    );
}

$totals = array();

foreach ($distributions as $distribution => $versions) {
    foreach ($versions as $version => $case) {
        $dv = $distribution . '-' . $version;
        foreach ($tests as $test => $factors) {
            if (!empty($ignore[$test][$distribution])) {
                continue;
            }
        
            foreach ((array)$factors as $factor) {
                if (!$factor) {
                    $factor = 1;
                }
            
                $results = array(
                    'memory' => array(),
                    'duration' => array(),
                );
            
                invoke($dv, $test, $factor, 'prepare');
            
                for ($i=0; $i < ITERATIONS; $i++) {
                    $t = invoke($dv, $test, $factor, 'evaluate');
                    $results['memory'][] = $t['memory'];
                    $results['duration'][] = $t['duration'];
                }
            
                invoke($dv, $test, $factor, 'teardown');
            
                // ignore first and last quantile to reduce impact of performance spikes in testing
                $averages = array();
                foreach (array('memory', 'duration') as $k) {
                    sort($results[$k]);
                    $results[$k] = array_slice($results[$k], QUANTILE, ELEMENTS);
                    $averages[$k] = array_sum($results[$k]) / ELEMENTS;
                }
            
                printf("%-20s %-15s %0.4fs   %0.4fMB\n", $test . '['. $factor .']', $dv, $averages['duration'], $averages['memory']);
            
                if (empty($totals[$test])) {
                    $totals[$test] = array();
                }
            
                if (empty($totals[$test][$distribution])) {
                    $totals[$test][$distribution] = array();
                }
                
                if (empty($totals[$test][$distribution][$version])) {
                    $totals[$test][$distribution][$version] = array();
                }
            
                $totals[$test][$distribution][$version][$factor] = $averages;
            }
        }
    }
}

file_put_contents(dirname(__FILE__) .'/tmp/results.php', "<?php\n\$time = " . time() . ";\n\$totals = " . var_export($totals, true) .';');
