<?php
/*                                                                              
    Couffin - A simple PHP shopping basket.                                    
	http://auberger.com/couffin
                                                                                                                                                           
	(c) Copyright 2005-2011 Georges Auberger
	All Rights Reserved

	Permission is hereby granted, free of charge, to any person
	obtaining a copy of this software and associated documentation
	files (the "Software"), to deal in the Software without
	restriction, including without limitation the rights to use,
	copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the
	Software is furnished to do so, subject to the following
	conditions:

	The above copyright notice and this permission notice shall be
	included in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
	OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
	HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
	OTHER DEALINGS IN THE SOFTWARE.                                 
*/

class Address {
	var $name;
	var $street1;
	var $street2;
	var $city;
	var $zip;
	var $state;
	var $country;

	function isNameValid() {
		return strlen($this->name) > 0;
	}

	function isStreet1Valid() {
		return strlen($this->street1) > 0;
	}

	function isCityValid() {
		return strlen($this->city) > 0;
	}

	function isZipValid() {
		return 	strlen($this->zip) == 5;
	}

	function isStateValid() {
		return	strlen($this->state) == 2;
	}

	function isCountryValid() {
		return	strlen($this->country) > 0;
	}

	function validate() {
		return $this->isNameValid() &&	
			$this->isStreet1Valid() &&	
			$this->isCityValid() &&	
			$this->isZipValid() &&	
			$this->isStateValid() && 
			$this->isCountryValid();
	}

}

class Customer {
    var $billingAddress;
    var $email;
    var $phone;
	
	function Customer() {
		$this->billingAddress = new address;
		// Only expect US customers for now
		$this->billingAddress->country = "USA";
	}
	
	function getName() {
		return $this->billingAddress->name;
	}
	
	function validateEmail($emailToValidate) {
		return ereg('^[_A-Za-z0-9\.!#%&=\/+-]+@[A-Za-z0-9-]+\.[A-Za-z0-9\.-]+$', $emailToValidate);
	}

	function isEmailValid() {
		return $this->validateEmail($this->email);
	}

	function validatePhone($phone) {
       return ereg('^\(?[0-9]{3}\)?-?\.? ?[0-9]{3}-?\.? ?[0-9]{4}$', $phone);
	}
	
	function isPhoneValid() {
       return $this->validatePhone($this->phone);
	}

	function validate() {
		return $this->billingAddress->validate() && $this->isEmailValid() && $this->isPhoneValid();
	}
}

?>
