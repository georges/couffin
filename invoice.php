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
include_once "class/PromoManager.class.php";

session_start();

// If no cart exists we can't check out
if (!session_is_registered('cart')) {
	// Redirect to root of site
	header("Location: $settings->siteUrl");
	exit;
} 	

$cart = $_SESSION['cart'];

// If no session exists, create one
if (!session_is_registered('invoice')) {
	$_SESSION['invoice'] = new Invoice(	new TaxManager($settings->taxRates), 
										new ShippingManager($settings->shippingRates),
										new PromoManager($settings->promoCodes));
} 

$invoice = $_SESSION['invoice'];
$invoice->cart = $cart;

$displayInvoice = false;
$noValidation=true;
$errorMessage="";
	
// Figure out how we got here. Only post are supported to alter this
// A GET request will simply display the content
if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {

	$invoice->customer->billingAddress->name = $_POST['name'];		
	$invoice->customer->billingAddress->street1 = $_POST['street1'];
	$invoice->customer->billingAddress->street2 = $_POST['street2'];
	$invoice->customer->billingAddress->city = $_POST['city'];
	$invoice->customer->billingAddress->zip = $_POST['zip'];
	$invoice->customer->billingAddress->state = $_POST['state'];
	$invoice->customer->email = $_POST['email'];
	$invoice->customer->phone = $_POST['phone'];	
		
	$invoice->shipToBillingAddress = (strlen($_POST['shipToBillingAddress']) > 0);
		
	if ($invoice->shipToBillingAddress) {
		$invoice->shippingAddress->name = $_POST['name'];
		$invoice->shippingAddress->street1 = $_POST['street1'];
		$invoice->shippingAddress->street2 = $_POST['street2'];
		$invoice->shippingAddress->city = $_POST['city'];
		$invoice->shippingAddress->zip = $_POST['zip'];
		$invoice->shippingAddress->state = $_POST['state'];			
	} else {
		$invoice->shippingAddress->name = $_POST['shipToName'];
		$invoice->shippingAddress->street1 = $_POST['shipTostreet1'];
		$invoice->shippingAddress->street2 = $_POST['shipTostreet2'];
		$invoice->shippingAddress->city = $_POST['shipTocity'];
		$invoice->shippingAddress->zip = $_POST['shipTozip'];
		$invoice->shippingAddress->state = $_POST['shipTostate'];			
	}
	
	$invoice->subscribe = (strlen($_POST['subscribe']) > 0);
	$invoice->htmlemail = (strlen($_POST['htmlemail']) > 0);

	$invoice->promoCode = strtoupper($_POST['promoCode']);	
	
	
	if ($invoice->validate()) {
		// Process discount and email registration
		$invoice->processDiscount();

		$displayInvoice=true;
	} else {
		$errorMessage="<p class='warning'>Please correct the errors in the <span class='validation-error'>highlighted fields</span> below.</p>";
		$noValidation=false;
	}
}

// This is needed because the variable is a copy of the object, not a reference to it
$_SESSION['invoice'] = $invoice;	
$_SESSION['cart'] = $invoice->cart;

$title = "Invoice";
include "header.php"; 

?>
<div id="invoice">

<? if ($displayInvoice) { ?>
	<div id="navmenu">
		<ul>
			<li class="first">
				<a href="invoice.php">&laquo; Back</a>
			</li>
			<li class="last">
				<a href="#" onclick="document.pay.submit();">Proceed to Secure Payment &raquo;</a>
			</li>
		</ul>
	</div>
<hr />
			<p class="info">Please verify your information before <a href="#" onclick="document.pay.submit();">proceeding to the secure payment page</a>.</p>
	<div id="billing">
		<fieldset>
			<legend>Billing Information (<a href="invoice.php">edit</a>)</legend>
			<div>
				<label>Order #</label>
				<?= $invoice->orderNumber ?>
			</div>
			<div>
				<label>Name</label>
				<?=$invoice->customer->billingAddress->name?>
			</div>
			<div>
				<label>Address</label>
				<?= $invoice->customer->billingAddress->street1 ?>
			</div>
	<? if (strlen($invoice->customer->billingAddress->street2) > 0) { ?>
			<div>
				<label>&nbsp;</label>
				<?= $invoice->customer->billingAddress->street2 ?>
			</div>
	<? } ?>
			<div>
				<label>City</label>
				<?=$invoice->customer->billingAddress->city?>
			</div>
			<div>
				<label>ZIP</label>
				<?=$invoice->customer->billingAddress->zip?>
			</div>
			<div>
				<label>State</label>
				<?=$invoice->customer->billingAddress->state?>
			</div>
			<div>
				<label>Email</label>
				<?=$invoice->customer->email?>
			</div>
			<div>
				<label>Phone</label>
				<?=$invoice->customer->phone?>
			</div>
		</fieldset>
	</div>
	<div id="shipping">
		<fieldset>
			<legend>Shipping Information (<a href="invoice.php">edit</a>)</legend>
			<div>
				<label>Name</label>
				<?=$invoice->shippingAddress->name?>
			</div>
			<div>
				<label>Address</label>
				<?=$invoice->shippingAddress->street1?>
			</div>
	<? if (strlen($invoice->shippingAddress->street2) > 0) { ?>
			<div>
				<label>&nbsp;</label>
				<?=$invoice->shippingAddress->street2?>
			</div>
	<? } ?>
			<div>
				<label>City</label>
				<?=$invoice->shippingAddress->city?>
			</div>
			<div>
				<label>ZIP</label>
				<?=$invoice->shippingAddress->zip?>
			</div>
			<div>
				<label>State</label>
				<?=$invoice->shippingAddress->state?>
			</div>
		</fieldset>
	</div>
	<hr />
	<? $hasDiscount = $invoice->cart->hasDiscount(); 
	?>
	<div id="items">
		<fieldset>
			<legend>Items (<a href="cart.php">edit</a>)</legend>
			<table>
				<tr>
					<th>Sku</th>
					<th>Product</th>
					<th>Qty</th>
					<th>Price</th>
					<th><?= $hasDiscount ? "Discount" : "" ?></th>
					<th>Total</th>
				</tr>
	
<? 
	$alternate = false;
	foreach ($invoice->cart->items as $id => $item) { 
		$alternate = !$alternate;
?>
				<tr class="<?= $alternate ? "a" : "b" ?>">
					<td><?=$id?></td>
					<td><?=$item->getName()?></td>
					<td align="center"><?=$item->qty?></td>
					<td class="currency">$<?=number_format($item->getPrice(), 2)?></td>
					<?= $hasDiscount ? "<td class='currency'>$" . number_format($item->discountedAmount(), 2) . "</td>" : "<td></td>" ?>
					<td class="currency">$<?=number_format($item->extendedPrice(), 2)?></td>
				</tr>
<? 	} ?>
				<tr class="b">
					<td colspan="5" align="right"><strong>Tax (<?=number_format($invoice->getTaxRate()*100, 2)?> %)</strong></td>
					<td class="currency">$<?=number_format($invoice->getTaxAmount(), 2)?></td>
				</tr>
				<tr class="b">
					<td colspan="5" align="right"><strong><?=$invoice->getShippingMethod()?> Shipping (<?=number_format($invoice->cart->getTotalWeight(), 2)?> lb)</strong></td>
					<td class="currency">$<?=number_format($invoice->getShippingAmount(), 2)?></td>
				</tr>
				<tr class="b">
					<td colspan="5" align="right"><strong>TOTAL</strong></td>
					<td class="currency">$<?=number_format($invoice->getTotal(), 2)?></td>
				</tr>
			</table>
		</fieldset>
	</div>
<?	
	include "config/paymentProcessor.php";

	} else { 
?>
	<script language="JavaScript" type="text/javascript"><!--
function toggle() {
	document.invoice.shipToName.readOnly = document.invoice.shipToBillingAddress.checked;
	document.invoice.shipTostreet1.readOnly = document.invoice.shipToBillingAddress.checked;
	document.invoice.shipTostreet2.readOnly = document.invoice.shipToBillingAddress.checked;
	document.invoice.shipTozip.readOnly = document.invoice.shipToBillingAddress.checked;
	document.invoice.shipTocity.readOnly = document.invoice.shipToBillingAddress.checked;
	document.invoice.shipTostate.readOnly = document.invoice.shipToBillingAddress.checked;			
}
//-->
	</script>
	<div id="navmenu">
		<ul>
			<li class="first">
				<a href="cart.php">View Cart (<?
		if ($cart->getTotalItems() >= 2) {
			echo $cart->getTotalItems() . " items";
		} else {
			echo $cart->getTotalItems() . " item";
		}
?>)
				</a>
			</li>
			<li 	class="last">
				<a href="#" onclick="document.invoice.submit();">Next &raquo;</a>
			</li>
		</ul>
	</div>

<hr />
	<?= $errorMessage ?>
	<form name="invoice" action="<?=$PHP_SELF?>" method="post" enctype="application/x-www-form-urlencoded">
		<div id="billing">
			<fieldset>
				<legend>Billing Information</legend>
				<div>
					<label for="name" class="req">Name</label>
					<input type="text" name="name" id="name" tabindex="1" value="<?=$invoice->customer->billingAddress->name?>" class="<?=$noValidation || $invoice->customer->billingAddress->isNameValid() ? '' : 'validation-error' ?>" />
				</div>
				<div>
					<label for="street1" class="req">Address</label>
					<input type="text" name="street1" id="street1" tabindex="2" value="<?=$invoice->customer->billingAddress->street1?>" class="<?=$noValidation || $invoice->customer->billingAddress->isStreet1Valid() ? '' : 'validation-error' ?>" />
				</div>
				<div>
					<label>&nbsp;</label>
					<input type="text" name="street2" id="street2" tabindex="3" value="<?=$invoice->customer->billingAddress->street2?>" />
				</div>
				<div>
					<label for="city" class="req">City</label>
					<input type="text" name="city" id="city" tabindex="4" value="<?=$invoice->customer->billingAddress->city?>" class="<?=$noValidation || $invoice->customer->billingAddress->isCityValid() ? '' : 'validation-error' ?>"/>
				</div>
				<div>
					<label for="zip" class="req">ZIP</label>
					<input type="text" name="zip" id="zip" tabindex="5" value="<?=$invoice->customer->billingAddress->zip?>" class="<?=$noValidation || $invoice->customer->billingAddress->isZipValid() ? '' : 'validation-error' ?>"/>
				</div>
				<div>
					<label for="state" class="req">State</label>
					<select name="state" id="state" tabindex="6" class="<?=$noValidation || $invoice->customer->billingAddress->isStateValid() ? '' : 'validation-error' ?>">
<?
        while (list($abbrev, $name)=each($states)) {
                printf("<option %s value=\"%s\">%s</option>\n", ($invoice->customer->billingAddress->state==$abbrev) ? 'selected="selected"' : '', $abbrev, $name); 
        }
?>
					</select>
				</div>
				<div>
					<label for="email" class="req">Email</label>
					<input type="text" name="email" id="email" tabindex="7" value="<?=$invoice->customer->email?>" class="<?=$noValidation || $invoice->customer->isEmailValid() ? '' : 'validation-error' ?>"/>
				</div>
				<div>
					<label for="phone" class="req">Phone</label>
					<input type="text" name="phone" id="phone" tabindex="8" value="<?=$invoice->customer->phone?>" class="<?=$noValidation || $invoice->customer->isPhoneValid() ? '' : 'validation-error' ?>"/>
				</div>
			</fieldset>
		</div>
		<div id="shipping">
			<fieldset>
				<legend>Shipping Information</legend>
				<div>
					<label class="checkbox">
						<input onclick="toggle();" class="checkbox" type="checkbox" tabindex="9" name="shipToBillingAddress" <?= ($invoice->shipToBillingAddress ? 'checked="checked"' : '')?> />
						Same as billing address
					</label>
				</div>
				<hr />
				<div>
					<label for="shipToName" class="req">Name</label>
					<input type="text" name="shipToName" id="shipToName" tabindex="10" value="<?=$invoice->shippingAddress->name?>" class="<?=$noValidation || $invoice->shipToBillingAddress || $invoice->shippingAddress->isNameValid() ? '' : 'validation-error' ?>"/>
				</div>
				<div>
					<label for="shipTostreet1" class="req">Address</label>
					<input type="text" name="shipTostreet1" id="shipTostreet1" tabindex="11" value="<?=$invoice->shippingAddress->street1?>" class="<?=$noValidation || $invoice->shipToBillingAddress  || $invoice->shippingAddress->isStreet1Valid() ? '' : 'validation-error' ?>"/>
				</div>
				<div>
					<label>&nbsp;</label>
					<input type="text" name="shipTostreet2" id="shipTostreet2" tabindex="12" value="<?=$invoice->shippingAddress->street2?>" />
				</div>
				<div>
					<label for="shipTocity" class="req">City</label>
					<input type="text" name="shipTocity" id="shipTocity" tabindex="13" value="<?=$invoice->shippingAddress->city?>" class="<?=$noValidation || $invoice->shipToBillingAddress  || $invoice->shippingAddress->isCityValid() ? '' : 'validation-error' ?>"/>
				</div>
				<div>
					<label for="shipTozip" class="req">ZIP</label>
					<input type="text" name="shipTozip" id="shipTozip" tabindex="14" value="<?=$invoice->shippingAddress->zip?>" class="<?=$noValidation || $invoice->shipToBillingAddress  || $invoice->shippingAddress->isZipValid() ? '' : 'validation-error' ?>"/>
				</div>
				<div>
					<label for="shipTostate" class="req">State</label>
					<select name="shipTostate" id="shipTostate" tabindex="15" class="<?=$noValidation || $invoice->shipToBillingAddress  || $invoice->shippingAddress->isStateValid() ? '' : 'validation-error' ?>">
<?
	reset($states);
    while (list($abbrev, $name)=each($states)) {
    		printf("<option %s value=\"%s\">%s</option>\n", ($invoice->shippingAddress->state==$abbrev) ? 'selected="selected"' : '', $abbrev, $name); 
    }
?>
					</select>
				</div>
			</fieldset>
		</div>
		<hr />
		<fieldset>
				<div>
					<label for="promoCode" >Promo Code</label>
					<input type="text" name="promoCode" id="promoCode"  value="<?=$invoice->promoCode?>" class="<?=$noValidation || $invoice->promoManager->isValid($invoice->promoCode) ? '' : 'validation-error' ?>"/>
					<br />
					<br />
					<input class="checkbox" type="checkbox"  name="subscribe" <?= ($invoice->subscribe ? 'checked="checked"' : '')?> >
						Yes, I want to receive emails about news and promotional offers.
					</input>
					<br />
					<div class="indent">
					<input class="checkbox" type="checkbox" name="htmlemail" <?= ($invoice->htmlemail ? 'checked="checked"' : '')?> />
						I prefer to receive emails in HTML format
					</input>
					</div>
				</div>
		</fieldset>
	</form>
	<script language="JavaScript" type="text/javascript"><!--
toggle();
-->
	</script>
<? } ?>
<hr />
</div>

<? include "footer.php" ?>
