<?php

require_once './request_base.php';

$amount =& Services_PayPal::getType('BasicAmountType');
$amount->setval(0.5);
$amount->setattr('currencyID', 'USD');

$ecd =& Services_PayPal::getType('SetExpressCheckoutRequestDetailsType');
$ecd->setOrderTotal($amount);
$ecd->setReturnURL('http://www.example.com/return');
$ecd->setCancelURL('http://www.example.com/cancel');

$ec =& Services_PayPal::getType('SetExpressCheckoutRequestType');
$ec->setSetExpressCheckoutRequestDetails($ecd);

$response = $caller->SetExpressCheckout($ec);
var_dump($response);
