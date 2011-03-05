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
include_once "class/Product.class.php";
include_once "class/Cart.class.php";
include_once "class/Customer.class.php";
include_once "class/Invoice.class.php";
include_once "class/States.class.php";
include_once "class/OrderProcessor.class.php";
include_once "class/Product.class.php";
include_once "class/PromoManager.class.php";


session_start();

// If no session exists, we have a problem
if (!session_is_registered('invoice')) {
	header("Location: " . $settings->siteUrl);
	exit;
} 	

$title = "Error";
include "header.php"; 

$invoice = $_SESSION['invoice'];

if ($invoice->validate()) {		
	// Insure a unique order number for each submission
	$invoice->newOrderNumber();
	$_SESSION['invoice'] = $invoice;
?>

<div id="navmenu">
&nbsp;
</div>

<p class="error">
	We are sorry, we are unable to process your order.<br/><br/>
	Please try to <a href='invoice.php'>check out</a> again.
</p>

<?
} else {
	header("Location: invoice.php");
	exit;
}	
include "footer.php"; 
?>