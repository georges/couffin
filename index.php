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
	$_SESSION['cart'] = new cart;
} 

$cart = $_SESSION['cart'];

$title = "Catalog";
include "header.php"; 
?>

<div id="navmenu">
	<ul>
		<li class="first">
			<a href="cart.php">View Cart (<?
		if ($cart->getTotalItems() >= 2) {
			echo $cart->getTotalItems() . " items";
		} else {
			echo $cart->getTotalItems() . " item";
		}
?>)</a>
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


<div id="catalog">
	<h2>Catalog</h2>
<?php
foreach ($products as $id => $product) { 
?>
	<div class="product">
		<a href="<?= $settings->siteUrl . $product->url?>?id=<?=$id?>">
			<img src="<?= $product->img?>" alt="<?=$product->getName()?>" />
		</a>
		<h3><a href="<?= $settings->siteUrl . $product->url?>?id=<?=$id?>"><?=$product->getName()?></a></h3>
		<p>
		<?=$product->description?>
			<span class="product-price">$ <?=$product->price?></span>
		</p>
		<form action="cart.php" method="post">
			<input type="hidden" name="action" value="add" />
			<input type="hidden" name="qty" value="1" />
			<input type="hidden" name="id" value="<?=$id?>" />
			<button type="submit" class="button">Add</button>
		</form>
		<hr />
	</div>
<? } ?>
	<hr />
</div>
<? 
include "footer.php";
?>
