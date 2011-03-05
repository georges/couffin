<?php

include_once "config/settings.php";
include_once "class/SecureAsset.class.php";

set_time_limit(0);

function mimeTypes($file) {
	if (!is_file($file) || !is_readable($file)) return false;
	$types = array();
	$fp = fopen($file,"r");
	while (false != ($line = fgets($fp,4096))) {
	if (!preg_match("/^\s*(?!#)\s*(\S+)\s+(?=\S)(.+)/",$line,$match)) continue;
	$tmp = preg_split("/\s/",trim($match[2]));
	foreach($tmp as $type) $types[strtolower($type)] = $match[1];
	}
	fclose ($fp);	
	return $types;
}

$s = new SecureAsset(null);

$s->fromSecureString($_SERVER['QUERY_STRING']);

if ($s->isValid()) {
	# read the mime-types
	$mimes = mimeTypes('mime.types');	

	if (ob_get_level() == 0) {
	   ob_start();
	}

	if (isset($mimes[$s->getExtension()])) {
		header("Content-Type: ".$mimes[$s->getExtension()]);
	}
	header("Content-Disposition: attachment; filename=\"" . $s->getFileName() . "\"");   
	header("Content-Length: " . @filesize($s->file));

	$fp = fopen($s->file, 'rb');
	if (!$fp) {
		error_log("Can't open file: " . $s->file);
	}
 	while(!feof($fp) && $fp) {
       	echo fread($fp, 4096);
       	ob_flush();
		flush();
   	}	
   	
   	fclose($fp);
   	exit;
} else {
	error_log("Invalid link: " . $s->file);
	if ($s->hasExpired()) {
		error_log("Link has expired");
	}
	if ($s->hasBeenTampered()) {
		error_log("Link has been tampered with");
	}

?>
<h1>Error</h1>
<p>This download link is invalid or has expired.</p>
<?
}
?>
