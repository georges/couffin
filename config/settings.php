<?php
// Main url where the php files reside
$settings->siteUrl = "http://demo.auberger.com/couffin/";

// Email address used to send confirmation from
$settings->fromEmail = "Couffin Test <no-reply@demo.auberger.com>";

// Email address where the order is sent to for processing
$settings->orderProcessingEmail = "Couffin Test <couffin@demo.auberger.com>";

// Tax rates per states
$settings->taxRates = array("CA" => 0.0825, "TX" => 0.0625);

// Main url where the php files reside
$settings->downloadUrl = "http://demo.auberger.com/couffin/download.php";

// Shipping rates by weight. Only supports one shipping method for now.
// Prices are for "up to" the weight, i.e. $6.95 up to 1 lb
$settings->shippingRates = array(
	"UPS Ground" => array(
1 => 6.95,
2 => 8.25,
3 => 8.95,
4 => 9.95,
5 => 9.95,
6 => 10.95,
7 => 10.95,
8 => 11.95,
9 => 12.95,
10 => 13.95,
11 => 15.95	
	)
);

$settings->promoCodes = array(
	"PROMO2011" => array(
		"BRK01" => 0.2
	)
);

$settings->version = "1.1";
?>
