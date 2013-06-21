<?php

require_once './request_base.php';

$amount =& Services_PayPal::getType('BasicAmountType');
$amount->setval(100);
$amount->setattr('currencyID', 'USD');

$dcrt =& Services_PayPal::getType('DoCaptureRequestType');
$dcrt->setAuthorizationID('authorizationid001');
$dcrt->setAmount($amount);
$dcrt->setCompleteType('Complete');

$response = $caller->DoCapture($dcrt);
var_dump($response);
