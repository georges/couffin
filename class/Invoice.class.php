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

class Invoice {
	var $customer;
	var $shippingAddress;	
	var $cart;
	var $shipToBillingAddress;
	var $dateCreated;
	var $orderNumber;
	var $promoCode;
	
	var $subscribe = true;
	var $htmlemail = true;
	
	var $taxManager;
	var $shippingManager;
	var $promoManager;

	function Invoice($taxManager, $shippingManager, $promoManager) {
		$this->customer = new Customer();
		$this->cart = new Cart();
		$this->shippingAddress = new Address();
		$this->dateCreated = time();
		$this->newOrderNumber();
		$this->shipToBillingAddress=true;
		$this->shippingAddress->country="USA";
		$this->taxManager = $taxManager;
		$this->shippingManager = $shippingManager;
		$this->promoManager = $promoManager;
	}

	function generateOrderNumber() {
		$filename = "invoice-sequence";
		$handle=fopen($filename,'r');
		$sequence=fgets($handle);
		$sequence = ($sequence == 0) ? 100 : $sequence+1;
		fclose($handle);
		$handle=fopen($filename,'w');
		fwrite($handle,$sequence);
		fclose($handle);
		return $sequence;
	}
	
	function getDateCreated() {
		return date("m/d/Y", $this->dateCreated);
	}

	function getSubTotal() {
		return $this->cart->getTotalPrice();
	}
	
	function getTotal() {
		return $this->cart->getTotalPrice() + $this->getTaxAmount() + $this->getShippingAmount();
	}
	
	function getTaxAmount() {
		return $this->cart->getTotalPrice() * $this->getTaxRate();	
	}

	function getShippingAmount() {
		return $this->shippingManager->computeShipping($this->cart->getTotalWeight());
	}
	
	function getTaxRate() {
		return $this->taxManager->getTaxRate($this->customer->billingAddress->state);
	}
	
	function getShippingMethod() {
		return $this->shippingManager->getShippingMethod();
	}

	function processDiscount() {
		// Compute discount if any
		foreach ($this->cart->items as $id => $item) {
			$this->cart->items[$id]->discount = $this->promoManager->getDiscount($this->promoCode, $id);
		}	
	}

	function getDownloadableProducts() {
		// Retrieve downloadable products from cart if any
		$downloadableProducts = array();
		foreach ($this->cart->items as $id => $item) {
			if (strtolower(get_class($item->getProduct())) == "downloadableproduct") {
				array_push($downloadableProducts, $item->getProduct());
			}
		}	
		return $downloadableProducts;
	}
	
	function processSubscription() {
		// Add code to add the user to your email list provider
		return true;
	}
	
	function validate() {
		return $this->customer->validate() && $this->shippingAddress->validate() && $this->promoManager->isValid($this->promoCode);
	}
	
	function newOrderNumber() {
		$this->orderNumber = $this->generateOrderNumber();	
	}
	
}
?>
