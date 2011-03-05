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

define("DELIMITER", ":");
define("SECRET", "<-- PLACE SOME VERY LONG RANDOM STRING HERE -->");

class SecureAsset {
	var $file;
	var $expires;
	var $hash;
		
	function SecureAsset($file) {
		$this->file = $file;
		$this->expires = time() + (24 * 60 * 60); // links are good for 24 hours
		$this->hash = null;
	}

	function toString() {
		// Order matters
		return implode(DELIMITER, array($this->file, $this->expires));
	}
	
	function computeHash() {
		return md5($this->toString() . SECRET);
	}
	
	function toSecureString() {
		return urlencode(base64_encode($this->toString() . DELIMITER . $this->computeHash()));
	}

	function fromSecureString($string) {
		// Order matters
		$this->file = null;
		$this->expires = time()-1;
		$this->hash = null;
		list($this->file, $this->expires, $this->hash) = split(DELIMITER, base64_decode(urldecode($string)), 3);
	}

	function hasBeenTampered() {
		return ($this->hash != $this->computeHash());
	}

	function hasExpired() {
		return ($this->expires < time());
	}
	
	function isValid() {
		return !$this->hasExpired() && !$this->hasBeenTampered();
	}
	
	function getExtension() {
		$path_parts = pathinfo($this->file);
		return $path_parts['extension'];
	}

	function getFileName() {
		$path_parts = pathinfo($this->file);
		return $path_parts['basename'];
	}

	function getFileSizeMB() {
		return number_format(filesize($this->file)/1024/1024, 1);
	}
}
?>