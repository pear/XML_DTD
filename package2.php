<?php

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

$version = '0.5.0';
$apiver  = '0.5.0';
$state   = 'alpha';

$notes = <<<EOT
- switch to BSD license
- add package.xml v2 (while retaining package.xml v1)
- PEAR CS cleanup
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

if (PEAR::isError($result)) {
    echo $result->getMessage();
    die();
}

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
$package->setPhpDep('4.2.0');
$package->setPearinstallerDep('1.4.3');
$package->addPackageDepWithChannel('required', 'XML_Tree', 'pear.php.net', '2.0b1');
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
