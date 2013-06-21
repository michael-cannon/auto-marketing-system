<?php

require_once './request_base.php';

$ts =& Services_PayPal::getType('TransactionSearchRequestType');
$ts->setStartDate(date('Y-m-d') . 'T00:00:00-0700');

$response = $caller->TransactionSearch($ts);
var_dump($response);
