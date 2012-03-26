<?php

define('INIT_MEMORY', memory_get_usage());

set_time_limit(0);
// need to up the memory limit…

require_once dirname(__FILE__) . '/data.php';
$methods = array("prepare" => true, "evaluate" => true, "teardown" => true);

if (empty($_GET['distribution'])) {
    echo 'error: unknown distribution';
    return;
}

$t = explode('-', $_GET['distribution'], 2);
if (empty($t[1]) || empty($distributions[$t[0]][$t[1]])) {
    echo 'error: unknown version';
    return;
} else {
    $distribution = $t[0];
    $version = $t[1];
    $case = $distributions[$distribution][$version];
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
define('TEST_DIR', BASE_DIR . 'tests/' . $case . '/');
define('DISTRIBUTION_DIR', BASE_DIR . 'distributions/' . $distribution . '/' . $version . '/');
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