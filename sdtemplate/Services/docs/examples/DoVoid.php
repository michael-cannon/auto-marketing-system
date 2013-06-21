<?php

require_once './request_base.php';

$v =& Services_PayPal::getType('DoVoidRequestType');
$v->setAuthorizationId('0123456789');

$response = $caller->DoVoid($v);
var_dump($response);
