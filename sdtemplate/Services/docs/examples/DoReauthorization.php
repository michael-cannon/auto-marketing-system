<?php

require_once './request_base.php';

$amount =& Services_PayPal::getType('BasicAmountType');
$amount->setval(100);
$amount->setattr('currencyID', 'USD');

$ra =& Services_PayPal::getType('DoReauthorizationRequestType');
$ra->setAuthorizationId('0123456789');
$ra->setAmount($amount);

$response = $caller->DoReauthorization($ra);
var_dump($response);
