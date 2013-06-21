<?php

require_once './request_base.php';

$bau =& Services_PayPal::getType('BAUpdateRequestType');
$bau->setDesc('123');
$bau->setMpID('merchantPullId');

$response = $caller->BillAgreementUpdate($bau);
var_dump($response);
