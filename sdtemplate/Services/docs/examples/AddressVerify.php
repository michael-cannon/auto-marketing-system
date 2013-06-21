<?php

require_once './request_base.php';

$avrt =& Services_PayPal::getType('AddressVerifyRequestType');
$avrt->setEmail('sdk-buyer@sdk.com');
$avrt->setZip('95100');
$avrt->setStreet('123 Main St');

$response = $caller->AddressVerify($avrt);
var_dump($response);
