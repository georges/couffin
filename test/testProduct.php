<?php
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('../class/Product.class.php');

class TestProduct extends UnitTestCase {
    
	function setUp() {
	}

    function tearDown() {
    }
	
    function testCreation() {
		$product = new Product("", "", "", 0, 0, "");
        $this->assertIsA($product, "Product");
    }

    function testName() {
		$product = new Product("myname", "", "", 0, 0, "");
        $this->assertTrue($product->getName(), "myname");
    }

}
?>