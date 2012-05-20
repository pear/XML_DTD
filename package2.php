<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PEAR package v2 generator for XML_DTD 
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
 * @author    Igor Feghali <ifeghali@php.net>
 * @copyright 2003-2012 The PHP Group
 * @license   http://opensource.org/licenses/bsd-license New BSD License
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/XML_DTD
 */

require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$desc = 
    "Parsing of DTD files and DTD validation of XML files.  "
    . "The XML validation is done with the php sax parser, "
    . "the xml extension, it does not use the domxml extension.  " 
    . "Currently supports most of the current XML spec, including entities, "
    . "elements and attributes. Some uncommon parts of the spec "
    . "may still be unsupported."
;

$version = '0.5.2';
$apiver  = '0.5.0';
$state   = 'alpha';

$notes = <<<EOT
- Fixed circular reference which was leaking memory
- Due to PHP 4 and 5 differences in object handling, the XML Parser had to be rewritten to be PHP 4 compatible again.
- Error when parsing empty XML
EOT;

$package = PEAR_PackageFileManager2::importOptions(
    'package2.xml',
    array(
    'filelistgenerator' => 'cvs',
    'changelogoldtonew' => false,
    'simpleoutput'	=> true,
    'baseinstalldir'    => 'XML',
    'packagefile'       => 'package2.xml',
    'packagedirectory'  => '.'));

$package->clearDeps();

$package->setPackage('XML_DTD');
$package->setPackageType('php');
$package->setSummary('Parsing of DTD files and DTD validation of XML files');
$package->setDescription($desc);
$package->setChannel('pear.php.net');
$package->setLicense('BSD License', 'http://opensource.org/licenses/bsd-license');
$package->setAPIVersion($apiver);
$package->setAPIStability($state);
$package->setReleaseVersion($version);
$package->setReleaseStability($state);
$package->setNotes($notes);
$package->setPhpDep('4.3.0');
$package->setPearinstallerDep('1.5.4');
$package->addPackageDepWithChannel('required', 'XML_Parser', 'pear.php.net', '1.3.1');
$package->addIgnore(array('package.php', 'package2.php', 'package.xml', 'package2.xml'));
$package->addReplacement('DTD.php', 'package-info', '@package_version@', 'version');
$package->addReplacement('DTD/XmlValidator.php', 'package-info', '@package_version@', 'version');
$package->generateContents();

if ($_SERVER['argv'][1] == 'make') {
    $result = $package->writePackageFile();
} else {
    $result = $package->debugPackageFile();
}

if (PEAR::isError($result)) {
    echo $result->getMessage();
    die();
}
