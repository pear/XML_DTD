#!/usr/bin/php -Cq
<?php
require 'XML/DTD_Validator.php';
$dtd = './package.dtd';
$xml = $argv[1];
$a = new DTD_Validator;
if (!$a->isValid($dtd, $xml)) {
	echo $a->getError();
}
?>