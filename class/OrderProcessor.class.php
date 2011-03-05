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
class OrderProcessor {
	var $from;
	var $orderProcessing;

	function OrderProcessor($fromEmail, $orderProcessingEmail, $downloadUrl) {
		$this->from = $fromEmail;	
		$this->orderProcessing = $orderProcessingEmail;
		$this->downloadUrl = $downloadUrl;
	}
	
	function formatBody($invoice) {
		$downloadableProducts = $invoice->getDownloadableProducts();

?>		
Date: <?=$invoice->getDateCreated()?>

Order Number: <?=$invoice->orderNumber?>


Bill To:
========		
<?=$invoice->customer->billingAddress->name?>

<?=$invoice->customer->billingAddress->street1?>

<?
if (strlen($invoice->customer->billingAddress->street2) > 0) {
	echo $invoice->customer->billingAddress->street2 . "\n";
}
?>
<?=$invoice->customer->billingAddress->city . ", " . $invoice->customer->billingAddress->state . " " . $invoice->customer->billingAddress->zip ?>

<?=$invoice->customer->phone ?>

<?=$invoice->customer->email ?>


Ship To:
========	
<?=$invoice->shippingAddress->name ?>

<?=$invoice->shippingAddress->street1 ?>

<?
if (strlen($invoice->shippingAddress->street2) > 0) {		
	echo $invoice->shippingAddress->street2 . "\n";
}
?>
<?=$invoice->shippingAddress->city .", ". $invoice->shippingAddress->state ." ". $invoice->shippingAddress->zip ?>


Ship Via: <?=$invoice->getShippingMethod()?>

<?= $invoice->cart->hasDiscount() ? "Promo code: " . $invoice->promoCode :  "" ?>


Items:
<?	
foreach ($invoice->cart->items as $id => $item) { 
	if ($item->discount > 0) {
		echo "$id  " . str_replace("&#8482;", "", $item->getName()) . "  $item->qty x $".number_format($item->getPrice(), 2). " - $" . number_format($item->discountedAmount(), 2) . " = $".number_format($item->extendedPrice(), 2) ."\n";
	} else {
		echo "$id  " . str_replace("&#8482;", "", $item->getName()) . "  $item->qty x $".number_format($item->getPrice(), 2)." = $".number_format($item->extendedPrice(), 2) ."\n";
	}
}
?>

Sub-total: $ <?=number_format($invoice->getSubTotal(), 2)?>

Tax (<?=number_format($invoice->getTaxRate()*100, 2)?> %): $ <?=number_format($invoice->getTaxAmount(), 2)?>

Shipping (<?=number_format($invoice->cart->getTotalWeight(), 2)?> lb): $ <?=number_format($invoice->getShippingAmount(), 2)?>

TOTAL: $ <?=number_format($invoice->getTotal(), 2)?>

<?
		if (sizeof($downloadableProducts) > 0) {
?>		

Download your products now!
===========================
Click on each link below and select the location where you want to save the file on your computer. 
Links are only valid for 24 hours. Make sure to download each file.

<?
			foreach ($downloadableProducts as $id => $downloadableProduct) {
?>
<?=str_replace("&#8482;", "", $downloadableProduct->getName())?>

<?
				foreach ($downloadableProduct->secureAssets as $id2 => $secureAsset) {
?>

<?=$secureAsset->getFileName() . " (" . $secureAsset->getFileSizeMB() . " MB)"?>

<?=$this->downloadUrl?>?<?=$secureAsset->toSecureString()?>

<?			
				}	
			}
		}
	}
			
	function sendMailToCustomer($invoice) {		
		$to = $invoice->customer->billingAddress->name . " <" . $invoice->customer->email . ">";			
		$headers = "From: ". $this->from ."\n";
	
		ob_start();
?>
Dear <?=$invoice->customer->billingAddress->name?>,

Thank you for your order. This automated e-mail serves as your receipt. Your order is summarized below.

<?$this->formatBody($invoice)?>

Payment Type: Credit Card
Your credit card will not be charged until your order is shipped.
<?
		$body = ob_get_clean();
		
		$body2 = str_replace("\n", "\r\n", $body);

		return 	mail($to, "Your order #$invoice->orderNumber", $body2, $headers);
	}

	function sendMailToWarehouse($invoice) {
		$headers = "From: ". $this->from ."\n";
		
		$forwarded_ip = $_SERVER["HTTP_X_FORWARDED_FOR"]; 
		$remote_addr = $_SERVER["REMOTE_ADDR"]; 
	
		ob_start();
		$this->formatBody($invoice);
		$body = ob_get_clean();

		// Proxy user?
		if (strlen($forwarded_ip) > 0) {
			$body = $body . "User IP: $forwarded_ip (".gethostbyaddr($forwarded_ip).") via proxy $remote_addr (".gethostbyaddr($remote_addr).")\r\n";
		} else {
			$body = $body . "User IP: $remote_addr (".gethostbyaddr($remote_addr).")\r\n";
		}
		$body = $body . "User Agent: ". $_SERVER['HTTP_USER_AGENT']. "\r\n";
		
		return 	mail($this->orderProcessing, "Order #$invoice->orderNumber", $body, $headers);
	}
}
?>
