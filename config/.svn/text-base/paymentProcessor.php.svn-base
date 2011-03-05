<!-- 
	This form can be used to delegate the payment to a 3rd party service
	You can change the various input fields name and action url for the POST. 
	Do not change the form name as it is used by a button to perform the POST.
-->
<form name="pay" action="charge.php" method="post">
	<!-- Billing -->
	<input type="hidden" name="bname" value="<?= $invoice->customer->billingAddress->name ?>">
	<input type="hidden" name="baddr2" value="<?= $invoice->customer->billingAddress->street2 ?>">
	<input type="hidden" name="baddr1" value="<?= $invoice->customer->billingAddress->street1 ?>">
	<input type="hidden" name="bcity" value="<?= $invoice->customer->billingAddress->city ?>">
	<input type="hidden" name="bstate" value="<?= $invoice->customer->billingAddress->state ?>">
	<input type="hidden" name="bcountry" value="<?= $invoice->customer->billingAddress->country ?>">
	<input type="hidden" name="bzip" value="<?= $invoice->customer->billingAddress->zip ?>">
	<input type="hidden" name="phone" value="<?= $invoice->customer->phone ?>">
	<input type="hidden" name="email" value="<?= $invoice->customer->email ?>">
	<!-- Shipping -->
	<input type="hidden" name="sname" value="<?= $invoice->shippingAddress->name ?>">
	<input type="hidden" name="saddr1" value="<?= $invoice->shippingAddress->street1 ?>">
	<input type="hidden" name="saddr2" value="<?= $invoice->shippingAddress->street2 ?>">
	<input type="hidden" name="scity" value="<?= $invoice->shippingAddress->city ?>">
	<input type="hidden" name="sstate" value="<?= $invoice->shippingAddress->state ?>">
	<input type="hidden" name="szip" value="<?= $invoice->shippingAddress->zip ?>">
	<input type="hidden" name="scountry" value="<?= $invoice->shippingAddress->country ?>">
	<!-- Order info -->
	<input type="hidden" name="oid" value="<?= $invoice->orderNumber ?>">
	<input type="hidden" name="subtotal" value="<?= $invoice->getSubTotal() ?>">
	<input type="hidden" name="shipping" value="<?= $invoice->getShippingAmount() ?>">
	<input type="hidden" name="tax" value="<?= number_format($invoice->getTaxAmount(), 2) ?>">
	<input type="hidden" name="total" value="<?= number_format($invoice->getTotal(), 2) ?>">
</form>
