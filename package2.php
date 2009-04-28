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
