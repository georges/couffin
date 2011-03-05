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

// Line item present in the cart
class Item {
		var $id;
		var $qty = 0;
	var $discount = 0;
	var $product = null;
	
	function Item($qty, $product) {
		$this->qty = $qty;
		$this->product = $product;
	}
	
	function getPrice() {
		return $this->product->price;
	}

	function getWeight() {
		return $this->product->weight;
	}

	function getUrl() {
		return $this->product->url;
	}
	
	function getQty() {
		return $this->qty;
	}
	
	function getName() {
		return $this->product->getName();
	}
	
	function getProduct() {
		return $this->product;	
	}
	
		function extendedPrice() {
				return ($this->qty * ($this->getPrice() * (1-$this->discount)));
		}
		
		function discountedAmount() {
				return ($this->getPrice() * $this->discount);
		}
		
}

// Main class containing items
class Cart {
		var $items = array();
	
	function Cart() {
	}
	
		function getNbLineItems() {
				return count($this->items);
		}

	function getTotalWeight() {
		$totalWeight=0;
				foreach ($this->items as $id => $item) {			
						$totalWeight = $totalWeight + ($item->qty * $item->getWeight());
				}
		return $totalWeight;		
	}

		function getTotalItems() {
				$totalItems=0;
				foreach ($this->items as $id => $item) {
						$totalItems = $totalItems + $item->qty;
				}
				return $totalItems;
		}

		function getTotalPrice() {
				$totalPrice=0;
				foreach ($this->items as $id => $item) {
			$totalPrice = $totalPrice + $item->extendedPrice();
				}
				return $totalPrice;
		}
		
		function isEmpty() {
				return ($this->getNbLineItems()==0);
		}
		
		function getItemQty($id) {
				return $this->items[$id]->qty;
		}
		
		function setItemQty($id, $qty) {
		if ($qty == 0) {
			$this->removeItem($id);
		} else {
			$this->items[$id]->qty = $qty;
		}
		}
		
		function addItem($id, $qty, $product) {
				// If item exists, simply add more to it
				if ($this->getItemQty($id) > 0) {
						$this->setItemQty($id, $this->getItemQty($id) + $qty);
				}
				else {
			$this->items[$id] = new Item($qty, $product);
				}
		}
		
		function removeItem($id) {
				if (!$this->isEmpty())	{
						$tmp=array();
			foreach ($this->items as $idList => $item) {
								if ($idList != $id) {
										$tmp[$idList] = $item;
								}
						};
						$this->items=$tmp;
				};
		}
	
	function hasDiscount() {
		foreach ($this->items as $id => $item) {
			if ($item->discount > 0) {
				return true;
			}
		} 
		return false;
	}
		
	function removeAll() {
		$this->items=array();
	}
}
?>
