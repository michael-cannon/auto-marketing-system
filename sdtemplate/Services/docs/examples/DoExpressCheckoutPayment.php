<?php

require_once './request_base.php';

$amount =& Services_PayPal::getType('BasicAmountType');
$amount->setval(100);
$amount->setattr('currencyID', 'USD');

$pdt =& Services_PayPal::getType('PaymentDetailsType');
$pdt->setOrderTotal($amount);

$details =& Services_PayPal::getType('DoExpressCheckoutPaymentRequestDetailsType');
$details->setPaymentAction('Sale');
$details->setToken('checkouttoken');
$details->setPayerID('sdk-buyer@sdk.com');
$details->setPaymentDetails($pdt);

$ecprt =& Services_PayPal::getType('DoExpressCheckoutPaymentRequestType');
$ecprt->setDoExpressCheckoutPaymentRequestDetails($details);

$response = $caller->DoExpressCheckoutPayment($ecprt);
var_dump($response);
