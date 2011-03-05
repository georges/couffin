<?php
/*
 Each product needs to be added in the array.
*/
$products = array(

// Product key
"SKU001" =>
new product(
// Name
"Rubber Ducky&#8482;",
// Product page. Can be a custom page or the generic product.php page.
"product.php", 
// Product image
"images/ducky.jpg",
// Price 
2.99,
// Weight
0.25,
// Extended description
"A nice bubble bath companion."
),

"SKU002" =>
new product(
"Compass",
"product.php", 
"images/compass.jpg", 
9.95,
0.75,
"Always know where north is."
),

"SKU003" =>
new product(
"Gold fish & Tank",
"product.php", 
"images/fish.jpg", 
24.95,
2,
"Circle around and around."
),

"SKU004" =>
new product(
"Mailbox",
"product.php", 
"images/mailbox.jpg", 
12.99,
1,
"You've got mail."
),

"SKU005" =>
new DownloadableProduct(
"David Byrne - My Fair Lady - MP3 file download",
"product.php",
"images/album_cover.gif",
4.99, 
0,   
"",
array(
// The assets should be located in a folder that is outside of your web server so that they can't be 
// downloaded directly
new SecureAsset("../couffin_assets/David Byrne - My Fair Lady.mp3")
)    
),

);

?>
