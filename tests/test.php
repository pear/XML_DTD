#!/usr/bin/php -Cq
<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Unit test for PEAR package XML_DTD
 *
 * PHP versions 4 and 5
 *
 * LICENSE:
 *
 * Copyright (c) 2003-2012 The PHP Group
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * The name of the author may not be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  XML
 * @package   XML_DTD
 * @author    Tomas V.V.Cox <cox@idecnet.com> 
 * @copyright 2003-2012 The PHP Group
 * @license   http://opensource.org/licenses/bsd-license New BSD License
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/XML_DTD
 */

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
