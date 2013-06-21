<?php

require_once './request_base.php';

$amount =& Services_PayPal::getType('BasicAmountType');
$amount->setval(100);
$amount->setattr('currencyID', 'USD');

$pd =& Services_PayPal::getType('MerchantPullPaymentType');
$pd->setAmount($amount);
$pd->setMpID('merchantPullId');
$b =& Services_PayPal::getType('BillUserRequestType');
$b->setMerchantPullPaymentDetails($pd);

$response = $caller->BillUser($b);
var_dump($response);
