#!/usr/local/bin/php
<?php
/**
 * Just rebuilds API methods with no downloading, etc.
 *
 * $Id: buildMethods.php,v 1.1.1.1 2010/04/15 09:43:04 peimic.comprock Exp $
 *
 * @package Services_PayPal
 */

/**
 * Included libraries.
 */
require_once 'Services/PayPal.php';
require_once 'Services/PayPal/SDK.php';

$packageDir = Services_PayPal::getPackageRoot();
$sdk =& new PayPal_SDK();
var_dump($sdk->writeCallerServices($packageDir . '/CallerServices.php'));
