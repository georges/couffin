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
include_once "class/DownloadableProduct.class.php";
include_once "class/SecureAsset.class.php";
include_once "class/Cart.class.php";
include_once "class/Customer.class.php";
include_once "class/Invoice.class.php";
include_once "class/States.class.php";
include_once "class/TaxManager.class.php";
include_once "class/ShippingManager.class.php";
include_once "class/OrderProcessor.class.php";
include_once "config/products.php";
include_once "class/PromoManager.class.php";

session_start();

// If no session exists, we can't send success email
if (!session_is_registered('invoice')) {
	error_log("No invoice, redirecting to home");
	header("Location: " . $settings->siteUrl);
	exit;
} 	

$title = "Congratulations";

$invoice = $_SESSION['invoice'];
$orderOk=false;

// This is needed so phpmesh does not pick up this
ob_end_clean();
if ($invoice->validate()) {	
	$orderProcessor = new OrderProcessor(	$settings->fromEmail, 
											$settings->orderProcessingEmail,
											$settings->downloadUrl);
		
	if ($orderProcessor->sendMailToWarehouse($invoice) && $orderProcessor->sendMailToCustomer($invoice)) {
		$orderOk = true;
		$invoice->processSubscription();
	} else {
		$orderOk = false;
		error_log("Order processing error: " . $invoice->customer->email);
	}
} else {
	// Invoice not valid. This can happen if manually forwarded to this page
	header("Location: " . "invoice.php");
	exit;
}
ob_start();

// Check if there are soft goods
$downloadableProducts = $invoice->getDownloadableProducts();

include "header.php"; 
?>

<div id="navmenu">
&nbsp;
</div>

<?php
if ($orderOk) {
?>


<p class='info'>
	Thank you, your order has been completed. A confirmation email has been sent to <strong><?=$invoice->customer->email?></strong>.
	You will receive another email notification when your order has shipped.
</p>

<?php
if (sizeof($downloadableProducts) > 0) {
	echo "<h2>Download your products now!</h2>";
	echo "<p>Click on each link below and select the location where you want to save the file on your computer. <b>Links are only valid for 24 hours</b>. Make sure to download each file.</p>";
	foreach ($downloadableProducts as $id => $downloadableProduct) {
		echo "<h3>" . $downloadableProduct->name . " (" . $downloadableProduct->getTotalFileSizeMB() ." MB)</h3>";
		echo "<ul>";
		foreach ($downloadableProduct->secureAssets as $id2 => $secureAsset) {
			echo "<li><a href=\"" . $settings->downloadUrl . "?". $secureAsset->toSecureString() . "\">" . $secureAsset->getFileName() . "</a>";
			echo " (" . $secureAsset->getFileSizeMB() . " MB)</li>";
		}	
		echo "</ul>";
	}
}

// You may want to do that to clear the user session
//session_destroy();

} else { ?>

<p class='error'>
	There has been a problem with your order.
</p>


<?php
}
include "footer.php"; 
?>
