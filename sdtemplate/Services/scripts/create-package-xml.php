#!/usr/local/bin/php
<?php
/**
 * Update the package.xml file. Usage:
 *
 * ./create-package-xml.php
 *     Display updated package.xml
 *
 * ./create-package.xml.php make
 *     Write updated package.xml
 */

require_once 'PEAR/PackageFileManager.php';

$packager =& new PEAR_PackageFileManager();

// Change options for new releases.
$e = $packager->setOptions(array(
    'baseinstalldir' => 'Services',
    'version' => '0.0.1',
    'packagedirectory' => dirname(__FILE__) . '/../',
    'state' => 'beta',
    'filelistgenerator' => 'cvs',
    'notes' => 'Initial SDK release',
    'ignore' => array('tests/',
                      'scripts/',
                      'SOAP/example/',
                      'SOAP/test/',
                      'errors.html'),
    'dir_roles' => array('docs' => 'doc'),
    'exceptions' => array('PayPal/build/README' => 'php',
                          'paypal-sdk-update' => 'script',
                          'paypal-sdk-update.bat' => 'script'),
    'installexceptions' => array('paypal-sdk-update' => '/',
                                 'paypal-sdk-update.bat' => '/'),
    'simpleoutput' => true,
    ));
if (PEAR::isError($e)) {
    die(__LINE__ . ': ' . $e->getMessage() . "\n");
}

// Add custom roles.
$packager->addRole('crt', 'php');
$packager->addRole('dist', 'php');
$packager->addRole('in', 'php');
$packager->addRole('wsdl', 'php');
$packager->addRole('xml', 'php');
$packager->addRole('xsd', 'php');
$packager->addRole('css', 'doc');

// Don't install the .bat file except on Windows.
$packager->addPlatformException('paypal-sdk-update.bat', 'windows');

// Replace @PHP-BIN@ in scripts with the path to the PHP executable,
// and @BIN-DIR@ with the bin (script install) directory.
$packager->addReplacement('paypal-sdk-update', 'pear-config', '@PHP-BIN@', 'php_bin');
$packager->addReplacement('paypal-sdk-update.bat', 'pear-config', '@PHP-BIN@', 'php_bin');
$packager->addReplacement('paypal-sdk-update.bat', 'pear-config', '@BIN-DIR@', 'bin_dir');

// Replace @package_version@ in PayPal.php with the version of the
// SDK.
$packager->addReplacement('PayPal.php', 'package-info', '@package_version@', 'version');

// Output or write the new package file depending on command line
// args.
if (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'make') {
    $e = $packager->writePackageFile();
} else {
    $e = $packager->debugPackageFile();
}
if (PEAR::isError($e)) {
    die(__LINE__ . ': ' . $e->getMessage() . "\n");
}
