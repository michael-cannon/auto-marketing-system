<?php

require_once './request_base.php';

$amount =& Services_PayPal::getType('BasicAmountType');
$amount->setval(0.5);
$amount->setattr('currencyID', 'USD');

$mprit =& Services_PayPal::getType('MassPayRequestItemType');
$mprit->setReceiverEmail('sdk-buyer@sdk.com');
$mprit->setAmount($amount);

$mp =& Services_PayPal::getType('MassPayRequestType');
$mp->setMassPayItem($mprit);
$mp->setEmailSubject('test payment');

$response = $caller->MassPay($mp);
var_dump($response);
