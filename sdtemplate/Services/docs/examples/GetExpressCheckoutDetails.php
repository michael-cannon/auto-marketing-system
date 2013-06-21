<?php

require_once './request_base.php';

$ecd =& Services_PayPal::getType('GetExpressCheckoutDetailsRequestType');
$ecd->setToken('0123456789');

$response = $caller->GetExpressCheckoutDetails($ecd);
var_dump($response);
