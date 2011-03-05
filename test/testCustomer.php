<?php
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('../class/Customer.class.php');

class TestCustomer extends UnitTestCase {
    
	function setUp() {
	}

    function tearDown() {
    }
	
    function testCreation() {
		$customer = new Customer();		
        $this->assertIsA($customer, "Customer");
		$this->assertIsA($customer->billingAddress, "Address");
    }

	function testValidEmail() {
		$customer = new Customer();		
		$this->assertTrue($customer->validateEmail("a@a.com"));
		$this->assertTrue($customer->validateEmail("a@a.org"));
		$this->assertTrue($customer->validateEmail("a@a.cc"));
		$this->assertTrue($customer->validateEmail("1@a.com"));
		$this->assertTrue($customer->validateEmail("this.is@trois.blah.com"));
		$this->assertTrue($customer->validateEmail("ab_cde@toto.com"));
		$this->assertTrue($customer->validateEmail("ab-cde@toto.com"));
		$this->assertTrue($customer->validateEmail("ab-cde@toto-ar-us.com"));
		$this->assertTrue($customer->validateEmail("ab!@toto.com"));
		$this->assertTrue($customer->validateEmail("customer/department=shipping@example.com"));
		$this->assertTrue($customer->validateEmail("_somename@example.com"));
		$this->assertTrue($customer->validateEmail("user+mailbox@example.com"));
	}

	function testInvalidEmail() {
		$customer = new Customer();				
		$this->assertFalse($customer->validateEmail("a"));
		$this->assertFalse($customer->validateEmail("a@b"));
		$this->assertFalse($customer->validateEmail("a@ksjdf"));
		$this->assertFalse($customer->validateEmail("a.com"));
		$this->assertFalse($customer->validateEmail("abc@def@example.com"));
		$this->assertFalse($customer->validateEmail("@example.com"));
		$this->assertFalse($customer->validateEmail("bob@"));
		$this->assertFalse($customer->validateEmail("a.com"));
	}

	function testValidPhone() {
		$customer = new Customer();		
		$this->assertTrue($customer->validatePhone("650-555-1212"));		
		$this->assertTrue($customer->validatePhone("650 555 1212"));		
		$this->assertTrue($customer->validatePhone("650.555.1212"));		
		$this->assertTrue($customer->validatePhone("650.555-1212"));		
		$this->assertTrue($customer->validatePhone("(650) 555-1212"));		
		$this->assertTrue($customer->validatePhone("(650) 555 1212"));		
		$this->assertTrue($customer->validatePhone("(650) 555.1212"));		
		$this->assertTrue($customer->validatePhone("6505551212"));		
	}

	function testInvalidPhone() {
		$customer = new Customer();		
		$this->assertFalse($customer->validatePhone(""));		
		$this->assertFalse($customer->validatePhone("6"));		
		$this->assertFalse($customer->validatePhone("00011122223"));		
		$this->assertFalse($customer->validatePhone("650 555 12 12"));		
		$this->assertFalse($customer->validatePhone("650/555/1212"));		
	}

}
?>