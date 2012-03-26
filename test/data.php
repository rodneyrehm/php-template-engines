<?php

/*
 * List of distributions to benchmark
 */
$distributions = array(
    // 'dwoo-1.1.1' => true,
    // 'opt-2.0.6' => true,
    // 'phptal-1.2.2' => true,
    // 'raintpl-2.6.4' => true,
    // 'savant-3.0.1' => true,
    'smarty-2.6' => true,
    'smarty-3.0' => true,
    'smarty-3.1' => true,
    'smarty-3.1.6' => true,
    'smarty-3.2' => true,
    // 'twig-1.1.1' => true,
    // 'twig-1.2.0' => true,
);

/*
 * List of runnable tests,
 * NameOfTest => factors
 * Factor can be (int) 1 or an array of integers.
 * If multiple factors are supplied, the test is run for each factor.
 * This allows to visualize a performance curve.
 * Systems may do very well on small factors (say handle 10 variables),
 * but do extremely miserable for higher factors (say handle 1000 variables)
 */
$tests = array(
    'Loop' => array(10, 100, 1000, 10000),
    'Assign' => array(10, 100, 1000, 10000),
    'LoopInclude' => array(1, 10, 100),
    //'TemplateInheritance' => array(1,2,3),
);

/*
 * Exclude tests from distributions, 
 * if distribution is incapable of handling the test
 */
$ignore = array(
    'TemplateInheritance' => array(
        'smarty-2.6' => true,
    ),
);