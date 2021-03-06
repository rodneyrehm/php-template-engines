<?php

/*
 * List of distributions to benchmark
 */
$distributions = array(
    'dwoo' => array(
        '1.1.1' => 'dwoo',
    ),
    // 'opt' => array(
    //     '2.0.6' => 'opt',
    // ),
    'phptal' => array(
        '1.2.2' => 'phptal',
    ),
    'raintpl' => array(
        //'2.6.4' => 'raintpl', // Page will break here
        '2.7.2' => 'raintpl',
    ),
    'savant' => array(
        '3.0.1' => 'savant',
    ),
    'smarty' => array(
        '2.6' => 'smarty2', 
        '3.0' => 'smarty3', 
        '3.1.8' => 'smarty3', 
        '3.2dev' => 'smarty3',
    ),
    'twig' => array(
        '1.1.1' => 'twig', 
        // '1.2.0' => 'twig', 
        // '1.3.0' => 'twig', 
        // '1.4.0' => 'twig', 
        '1.5.1' => 'twig', 
        '1.6.3' => 'twig',
        '1.6.5' => 'twig',
        '1.7.0' => 'twig',
    ),
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
    'Page' => array(1),
    'PageCached' => array(1),
    'Assign' => array(10, 100, 1000), // 10000
    'Loop' => array(10, 100, 1000), // 10000
    'Compile' => array(10, 100, 1000), // 10000
);

$charttypes = array(
    'Loop' => 'bar',
    'Assign' => 'bar',
    'Compile' => 'bar',
    'Page' => 'column',
    'PageCached' => 'column',
);

/*
 * Exclude tests from distributions, 
 * if distribution is incapable of handling the test
 */
$ignore = array(
    'TemplateInheritance' => array(
        'smarty-2.6' => true,
    ),
    'Compile' => array(
        'savant' => true,
    ),
    'PageCached' => array(
        'phptal' => true,
        'savant' => true,
        'twig' => true,
    ),
);