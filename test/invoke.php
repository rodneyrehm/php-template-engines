<?php

define('INIT_MEMORY', memory_get_usage());

require_once dirname(__FILE__) . '/data.php';
$methods = array("prepare" => true, "evaluate" => true, "teardown" => true);

if (empty($_GET['distribution']) || empty($distributions[$_GET['distribution']])) {
    echo 'error: unknown distribution';
    return;
}

if (empty($_GET['test']) || empty($tests[$_GET['test']])) {
    echo 'error: unknown test';
    return;
}

if (empty($_GET['method']) || empty($methods[$_GET['method']])) {
    echo 'error: unknown method';
    return;
}

if (!empty($ignore[$_GET['test']][$_GET['distribution']])) {
    echo 'error: ignored test';
    return;
}

// define environment
define('BASE_DIR', dirname(__FILE__) . '/');
define('TEST_DIR', BASE_DIR . 'tests/' . $_GET['distribution'] . '/');
define('DISTRIBUTION_DIR', BASE_DIR . 'distributions/' . $_GET['distribution'] . '/');
define('TMP_DIR', BASE_DIR . 'tmp/');

// load Benchmarker
require_once BASE_DIR . 'tests/Benchmarker.php';

// load distribution
require_once TEST_DIR . 'bootstrap.php';

// load test
require_once TEST_DIR . $_GET['test'] . '.php';
$benchmark = new Benchmark();

// run test
$res = $benchmark->{$_GET['method']}(empty($_GET['factor']) ? 1 : $_GET['factor']);

if (!empty($_GET['output'])) {
    echo $res;
} else {
    // output results
    echo $benchmark->getMemory() . "\n" . $benchmark->getDuration();
}