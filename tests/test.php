#!/usr/bin/php -Cq
<?php
$dtd = $argv[1];
$xml = isset($argv[2]) ? $argv[2] : false;
if (!$xml) {
	include 'XML/DTD_Parser.php';
	$a = new XML_DTD_Parser;
	print_r($a->parse($dtd));
} else {
	include 'XML/DTD_Validator.php';
	$a = new XML_DTD_Validator;
	if (!$a->isValid($dtd, $xml)) {
		echo $a->getMessage();
	}
}
?>