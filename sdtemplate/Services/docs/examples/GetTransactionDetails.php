<?php

require_once './request_base.php';

$d =& Services_PayPal::getType('GetTransactionDetailsRequestType');
$d->setTransactionId('16Y927061V203442U');

$response = $caller->GetTransactionDetails($d);
var_dump($response);
