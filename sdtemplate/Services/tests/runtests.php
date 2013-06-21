#!/usr/local/bin/php
<?php

require_once 'PHPUnit.php';

$harness = &new PHPUnit_TestSuite();

if (!empty($argv[1]) && file_exists(dirname(__FILE__) . '/' . basename($argv[1]))) {
    $classname = str_replace('.phput', '', basename($argv[1])) . 'Test';
    include_once basename($argv[1]);
    if (!class_exists($classname)) {
        trigger_error("Test Suite file '$test' didn't define the $classname class!");
    }
    $harness->addTestSuite($classname);
} else {
    $tests = glob(dirname(__FILE__) . '/*.phput');
    foreach ($tests as $test) {
        list($classname) = explode('.', $test);
        $classname = basename($classname) . 'Test';
        if (class_exists($classname)) {
            trigger_error("Class '$classname' already exists!\n");
        } else {
            include_once $test;
            if (!class_exists($classname)) {
                trigger_error("Test Suite file '$test' didn't define the $classname class!");
            }
        }
        $harness->addTestSuite($classname);
    }
}

$results = PHPUnit::run($harness);
print $results->toString();
