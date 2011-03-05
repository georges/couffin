<?php
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('../class/Cart.class.php');
require_once('simpletest/mock_objects.php');
require_once('../class/Product.class.php');

Mock::generate('Product');

class TestCart extends UnitTestCase {
    
	function setUp() {
	}

    function tearDown() {
    }
	
	function assertEmptyCart($cart) {
        $this->assertTrue($cart->isEmpty());
		$this->assertTrue($cart->getNbLineItems() == 0);
		$this->assertTrue($cart->getTotalItems() == 0);
		$this->assertTrue($cart->getTotalWeight() == 0);
		$this->assertTrue($cart->getTotalPrice() == 0);
		$this->assertFalse($cart->hasDiscount());		
	}
	
    function testCreation() {
		$cart = new Cart();
        $this->assertIsA($cart, "Cart");
    }

    function testEmptiness() {
		$cart = new Cart();
		$this->assertEmptyCart($cart);
    }

    function testAddition() {
		$cart = new Cart();
		$product = new MockProduct();
		$cart->addItem(1, 1, $product);

        $this->assertFalse($cart->isEmpty());
		$this->assertTrue($cart->getNbLineItems() == 1);
		$this->assertTrue($cart->getTotalItems() == 1);
		$this->assertTrue($cart->getTotalWeight() == 0);
		$this->assertTrue($cart->getTotalPrice() == 0);
		$this->assertFalse($cart->hasDiscount());
    }

    function testRemoval() {
		$cart = new Cart();
		$product = new MockProduct();
		$cart->addItem(1, 1, $product);
        $this->assertFalse($cart->isEmpty());
		$cart->removeItem(1);
		$this->assertEmptyCart($cart);
    }

    function testRemoveAll() {
		$cart = new Cart();
		$product = new MockProduct();
		$cart->addItem(1, 1, $product);
		$cart->addItem(2, 1, $product);
        $this->assertFalse($cart->isEmpty());
		$this->assertTrue($cart->getNbLineItems() == 2);
		$cart->removeAll();
		$this->assertEmptyCart($cart);
    }

}
?>