#!/usr/bin/php -Cq
<?php
ob_implicit_flush(true);
$path = ini_get('include_path');
ini_set('include_path', realpath('..') . ":$path");
$argv = $_SERVER['argv'];
if (!is_file(@$argv[1])) {
	help();
}
$dtd = $argv[1];
if (isset($argv[2]) && !is_file($argv[2])) {
	help();
}
$xml = isset($argv[2]) ? $argv[2] : false;
if (!$xml) {
	include 'XML/DTD.php';
	$a = new XML_DTD_Parser;
	print_r($a->parse($dtd));
} else {
	include 'XML/DTD/XmlValidator.php';
	$a = new XML_DTD_XmlValidator;
	if (!$a->isValid($dtd, $xml)) {
		echo $a->getMessage();
	}
}
function help() {
	echo "Usage: test.php <dtd file> [<xml file>]\n";
	echo "Passing only <dtd file> will dump the parsed DTD Tree\n";
	echo "Passing both, will try to validate <xml file> with <dtd file>\n";
	exit;
}
?>