<?php

require_once './request_base.php';

$name =& Services_PayPal::getType('PersonNameType');
$name->setFirstName('SDK');
$name->setLastName('Buyer');

$address =& Services_PayPal::getType('AddressType');
$address->setStreet1('123 Main');
$address->setCityName('San Jose');
$address->setStateOrProvince('CA');
$address->setCountry('US');
$address->setPostalCode('95135');

$payer =& Services_PayPal::getType('PayerInfoType');
$payer->setPayer('sdk-buyer@sdk.com');
$payer->setPayerID('sdk-buyer@sdk.com');
$payer->setPayerStatus('verified');
$payer->setPayerName($name);
$payer->setPayerCountry('US');
$payer->setAddress($address);

$cc =& Services_PayPal::getType('CreditCardDetailsType');
$cc->setCreditCardType('Visa');
$cc->setCreditCardNumber('4083838966326212');
$cc->setExpMonth(6);
$cc->setExpYear(2006);
$cc->setCardOwner($payer);

$amount =& Services_PayPal::getType('BasicAmountType');
$amount->setval(100);
$amount->setattr('currencyID', 'USD');

$pdt =& Services_PayPal::getType('PaymentDetailsType');
$pdt->setOrderTotal($amount);

$details =& Services_PayPal::getType('DoDirectPaymentRequestDetailsType');
$details->setPaymentAction('Authorization');
$details->setPaymentDetails($pdt);
$details->setCreditCard($cc);
$details->setIPAddress('127.0.0.1');
$details->setMerchantSessionId('merchantId');

$ddp =& Services_PayPal::getType('DoDirectPaymentRequestType');
$ddp->setDoDirectPaymentRequestDetails($details);

$response = $caller->DoDirectPayment($ddp);
var_dump($response);
