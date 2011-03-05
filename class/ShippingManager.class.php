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

class shippingManager {

	var $shippingRates;

	function shippingManager($shippingRates) {
		$this->shippingRates = $shippingRates;
	}
	
	function computeShipping($weight) {
		// Only first shipping method supported
		reset($this->shippingRates);
		$shippingRate = current($this->shippingRates);

		if ($weight == 0) {
			return 0;
		} else {
			$rate = $shippingRate[ceil($weight)];
			if ($rate > 0) {
				return $rate;
			} else {
				reset($shippingRate);
				for ($i=1;$i<count($shippingRate);$i++) {
     				next($shippingRate);
				}
				// Flat rate for heavier stuff. Returns the last price.
				return current($shippingRate);
			}
		}
	}

	function getShippingMethod() {
		// Only first shipping method supported
		reset($this->shippingRates);
		return key($this->shippingRates);	
	}
}
?>
