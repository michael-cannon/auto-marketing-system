<?php

error_reporting(E_ALL);
header('Content-Type: text/plain');

require_once 'Services/PayPal.php';
require_once 'Services/PayPal/Profile/Handler/Array.php';
require_once 'Services/PayPal/Profile/API.php';

// Settings.
$certfile = dirname(__FILE__) . '/sdk-seller_cert.pem';
$certpass = '';
$apiusername = 'sdk-seller_api1.sdk.com';
$apipassword = '12345678';
$subject = null;
$environment = 'Sandbox';

$handler =& ProfileHandler_Array::getInstance(array(
    'username' => $apiusername,
    'certificateFile' => $certfile,
    'subject' => $subject,
    'environment' => $environment));

$profile =& APIProfile::getInstance($apiusername, $handler);
$profile->setAPIPassword($apipassword);

$caller =& Services_PayPal::getCallerServices($profile);

if(Services_PayPal::isError($caller))
{
    print "Could not create CallerServices instance: ". $caller->getMessage();
    exit;
}

