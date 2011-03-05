<?php
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

$test = &new GroupTest('All tests');
$test->addTestFile('testCustomer.php');
$test->addTestFile('testProduct.php');
$test->addTestFile('testCart.php');

$test->run(new HtmlReporter());
?>