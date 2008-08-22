<?php

require_once 'PEAR/PackageFileManager.php';

$version = '0.5.0';
$state = 'alpha';
$notes = <<<EOT
- switch to BSD license
- add package.xml v2 (while retaining package.xml v1)
- PEAR CS cleanup
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
$package->addMaintainer('ashnazg', 'lead', 'Chuck Burgess', 'ashnazg@php.net');

$package->addDependency('XML_Tree', '2.0b1', 'ge', 'pkg', false);
$package->addDependency('php', '4.2.0', 'ge', 'php', false);

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
