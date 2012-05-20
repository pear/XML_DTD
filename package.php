<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PEAR package generator for XML_DTD 
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
 * @author    Stephan Schmidt <schst@php.net>
 * @author    Igor Feghali <ifeghali@php.net>
 * @copyright 2003-2012 The PHP Group
 * @license   http://opensource.org/licenses/bsd-license New BSD License
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/XML_DTD
 */

require_once 'PEAR/PackageFileManager.php';

$version = '0.5.2';
$state = 'alpha';
$notes = <<<EOT
- Fixed circular reference which was leaking memory
- Due to PHP 4 and 5 differences in object handling, the XML Parser had to be rewritten to be PHP 4 compatible again.
- Error when parsing empty XML
EOT;

$description = <<<EOT
Parsing of DTD files and DTD validation of XML files.
The XML validation is done with the php sax parser, the xml extension, it does not use the domxml extension.

Currently supports most of the current XML spec, including entities, elements and attributes. Some uncommon parts of the spec may still be unsupported.
EOT;

$package = new PEAR_PackageFileManager();
$result = $package->setOptions(array(
    'package'           => 'XML_DTD',
    'summary'           => 'Parsing of DTD files and DTD validation of XML files',
    'description'       => $description,
    'version'           => $version,
    'state'             => $state,
    'license'           => 'BSD License',
    'filelistgenerator' => 'cvs',
    'ignore'            => array('package.php', 'package2.php', 'package.xml', 'package2.xml'),
    'notes'             => $notes,
    'simpleoutput'      => true,
    'cleardependencies' => true,
    'baseinstalldir'    => 'XML',
    'packagedirectory'  => './',
    'dir_roles'         => array('docs' => 'doc',
                                 'examples' => 'doc',
                                 'tests' => 'test',
                                 )
    ));

if (PEAR::isError($result)) {
    echo $result->getMessage();
    die();
}

$package->addRole('txt', 'doc');

//$package->addMaintainer('cox', 'lead', 'Tomas V.V.Cox', 'cox@php.net');
//$package->addMaintainer('schst', 'lead', 'Stephan Schmidt', 'schst@php-tools.net');
//$package->addMaintainer('ashnazg', 'lead', 'Chuck Burgess', 'ashnazg@php.net');
$package->addMaintainer('ifeghali', 'lead', 'Igor Feghali', 'ifeghali@php.net');

$package->addDependency('php',        '4.3.0', 'ge', 'php', false);
$package->addDependency('PEAR',       '1.5.4', 'ge', 'pkg', false);
$package->addDependency('XML_Parser', '1.3.1', 'ge', 'pkg', false);

if (isset($_GET['make']) || (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'make')) {
    $result = $package->writePackageFile();
} else {
    $result = $package->debugPackageFile();
}

if (PEAR::isError($result)) {
    echo $result->getMessage();
    die();
}
?>
