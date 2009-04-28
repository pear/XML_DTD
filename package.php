<?php

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
