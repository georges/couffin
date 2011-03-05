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

include_once "config/settings.php";
session_start();

// If no session exists, we have a problem
if (!session_is_registered('invoice')) {
	header("Location: " . $settings->siteUrl);
	exit;
} 	

$title = "Charge";
include "header.php"; 


?>
<div id="navmenu">
	<ul>
		<li class="first">
			<a href="failure.php">Failure</a>
		</li>
		<li class="last">
			<a href="success.php">Success</a>
		</li>
	</ul>
</div>
<p>
	This page should normaly be a form hosted by the financial institution that takes care of handling the charge securely. Once the payment is 
processed, it should return control to the <a href="success.php">success</a> or <a href="failure.php">failure</a> page to complete the processing of the order. Alternatively, you could alter this
page to capture the payement information and process it if you have a secure way to host it.
</p>
<p>
	You can continue to test the flow for a charge <a href="failure.php">failure</a> or <a href="success.php">success</a>.
</p>
<?
include "footer.php";
?>
