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
include_once "config/products.php";

session_start();

// If no session exists, create one
if (!session_is_registered('cart')) {
	$_SESSION['cart'] = new Cart();
} 

$cart = $_SESSION['cart'];

// Figure out how we got here. Only post are supported to alter cart
// A GET request will simply display the content
if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {
	$id = $_POST['id'];
		
	switch ($_POST['action']) {
		case 'add':
			$cart->addItem($id, $_POST['qty'], $products[$id]);
			break;
				
		case 'remove':
			$cart->removeItem($id);
			break;
	
		case 'empty':
			$cart->removeAll();
			session_destroy();
			break;
	}		
}

// This is needed because the cart variable is a copy of the object, not a reference to it
$_SESSION['cart'] = $cart;	

$title = "Cart";
include "header.php";
?>
<div id="navmenu">
	<ul>
		<li class="first">
			<a href="index.php">View Catalog</a>
		</li>
<?
	if (!$cart->isEmpty()) {
?>
		<li class="last">
			<a href="invoice.php">Check Out</a>
		</li>
<?
	}
?>
	</ul>
</div>

<hr />

<?
if (!$cart->isEmpty()) {
?>
<div id="cart">
	<fieldset>
		<legend>Shopping Cart</legend>
		<table>
			<tr>
				<th>Sku</th>
				<th>Product</th>
				<th>Qty</th>
				<th>Price</th>
				<th>Total</th>
				<th></th>
			</tr>
<?php
	$alternate = false;
	foreach ($cart->items as $id => $item) { 
		$alternate = !$alternate;
?>
			<tr class="<?= $alternate ? "a" : "b" ?>">
				<td><?=$id?></td>
				<td><a href="<?= $settings->siteUrl . $item->getUrl()?>?id=<?=$id?>"><?=$item->getName()?></a></td>
				<td align="center">
					<form action="<?=$PHP_SELF?>" method="post">
						<input type="hidden" name="action" value="add" />
						<input type="hidden" name="qty" value="-1" />
						<input type="hidden" name="id" value="<?=$id?>" />
						<button type="submit" class="button">-</button>
					</form>
					<?=$item->qty?>
					<form action="<?=$PHP_SELF?>" method="post">
						<input type="hidden" name="action" value="add" />
						<input type="hidden" name="qty" value="1" />
						<input type="hidden" name="id" value="<?=$id?>" />
						<button type="submit" class="button">+</button>
					</form>
				</td>
				<td class="currency">$<?=number_format($item->getPrice(), 2)?></td>
				<td class="currency">$<?=number_format($item->extendedPrice(), 2)?></td>
				<td align="right" >
					<form action="<?=$PHP_SELF?>" method="post">
						<input type="hidden" name="action" value="remove" />
						<input type="hidden" name="id" value="<?=$id?>" />
						<button type="submit" class="button">Remove</button>
					</form>
				</td>
			</tr>
	<?php 	} 	?>
			<tr>
				<td align="right" colspan="4"><strong>TOTAL</strong></td>
				<td class="currency"><strong>$<?=number_format($cart->getTotalPrice(), 2)?></strong></td>
				<td align="right" >
				</td>
			</tr>
		</table>
	</fieldset>
</div>
<?	
} else {
	echo "<p class='info'>Your cart is empty</p>";
}
?>
<? 
include "footer.php";
?>

