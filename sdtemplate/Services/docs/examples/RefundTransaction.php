<?php

require_once './request_base.php';

$rtrt =& Services_PayPal::getType('RefundTransactionRequestType');
$rtrt->setTransactionId('0123456789');

$response = $caller->RefundTransaction($rtrt);
var_dump($response);
