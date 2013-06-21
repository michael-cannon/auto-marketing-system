<? //cc_paypal.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

require_once 'Services/PayPal.php';
require_once 'Services/PayPal/Profile/Handler/Array.php';
require_once 'Services/PayPal/Profile/API.php';
require_once './classes/site_class.php';

$this->subscription_renewal = $subscription_renewal;
$this->account_balance = $account_balance;

$this->debug_email = "";
$email_msg = "";
$additional_msg = "";
$handler_error_response = "";

$info = Site::cc_pre_process($db,$cc);
$this->sql_query = "select * from ".$cc->CC_TABLE;

$cc_table_result = $db->Execute($this->sql_query);
if (strlen($this->debug_email) > 0)
{
	@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
}
if (!$cc_table_result)
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = $this->sql_query;
		@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);
	$this->display_page($db);
	$db->Close();
	exit;
}
$show_cc_paypal = $cc_table_result->FetchRow();
if (strlen($info["cc_exp_year"]) == 2)
	$info["cc_exp_year"] = "20".$info["cc_exp_year"];

if (strlen($this->debug_email) > 0)
{
	//TESTING DATA
	$environment = 'Sandbox';
	if (strpos(dirname(__FILE__),'/') !== FALSE)
		$certfile = dirname(__FILE__)."/Services/PayPal/cert/test/cert_key_pem.txt";
	else
		$certfile = dirname(__FILE__)."\Services\PayPal\cert\test\cert_key_pem.txt";
	$certfile = $show_cc_paypal["certfile"];
	$certpass = '';//NOT USED BY PAYPAL AT THIS TIME
	$apiusername = 'receiver_api1.geodesicsolutions.com';
	$apipassword = 'receiverreceiver';
	$cc_number = '4971517491601381';
	$cvv2_code = '000';
	$cc_exp_year = '2006';
	$cc_exp_month = '01';
}
else
{
	//LIVE DATA
	$environment = 'Live';
	$certfile = $show_cc_paypal["certfile"];
	$certpass = '';//NOT USED BY PAYPAL AT THIS TIME
	$apiusername = $show_cc_paypal["api_username"];
	$apipassword = $show_cc_paypal["api_password"];
	$cc_number = $info["cc_number"];
	$cvv2_code = $info["cvv2_code"];
	$cc_exp_year = $cc_exp_year;
	$cc_exp_month = $cc_exp_month;
}

$subject = null;//THIRD PARTY

$charset = $show_cc_paypal["charset"];

//*****************************************************************************
//SELLER'S DATA
$handler =& ProfileHandler_Array::getInstance(array(
	'username' => $apiusername,
	'certificateFile' => $certfile,
	'subject' => $subject,
	'environment' => $environment));
if (Services_PayPal::isError($handler))
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = Site::expand_array($handler);
		@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);
	$this->display_page($db);
	$db->Close();
	exit;
}
$profile =& APIProfile::getInstance($apiusername, $handler);
if (Services_PayPal::isError($profile))
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = Site::expand_array($profile);
		@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);
	$this->display_page($db);
	$db->Close();
	exit;
}
$profile->setAPIPassword($apipassword);
$caller =& Services_PayPal::getCallerServices($profile);
if (Services_PayPal::isError($caller))
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = Site::expand_array($caller);
		@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);
	$this->display_page($db);
	$db->Close();
	exit;
}
$Address =& Services_PayPal::getType('AddressType');
if (Services_PayPal::isError($Address))
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = Site::expand_array($Address);
		@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);
	$this->display_page($db);
	$db->Close();
	exit;
}

//*****************************************************************************
//BUYER'S DATA
$OrderTotal =& Services_PayPal::getType('BasicAmountType');
if (Services_PayPal::isError($OrderTotal))
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = Site::expand_array($OrderTotal);
		@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);
	$this->display_page($db);
	$db->Close();
	exit;
}
$OrderTotal->setattr('currencyID', $show_cc_paypal["currency_id"]);
$OrderTotal->setval($this->total, $charset);
$PaymentDetails =& Services_PayPal::getType('PaymentDetailsType');
if (Services_PayPal::isError($PaymentDetails))
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = Site::expand_array($PaymentDetails);
		@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);
	$this->display_page($db);
	$db->Close();
	exit;
}
$PaymentDetails->setOrderTotal($OrderTotal);
$PayerName =& Services_PayPal::getType('PersonNameType');
if (Services_PayPal::isError($PayerName))
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = Site::expand_array($PayerName);
		@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);
	$this->display_page($db);
	$db->Close();
	exit;
}
$PayerName->setLastName($user_data->LASTNAME, $charset);
$PayerName->setFirstName($user_data->FIRSTNAME, $charset);
$CardOwner =& Services_PayPal::getType('PayerInfoType');
if (Services_PayPal::isError($CardOwner))
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = Site::expand_array($CardOwner);
		@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);
	$this->display_page($db);
	$db->Close();
	exit;
}
$Address->setPostalCode($user_data->ZIP, $charset);
$Address->setCountry($info["user_country"]["abbreviation"], $charset);
$Address->setStateOrProvince($user_data->STATE, $charset);
$Address->setCityName($user_data->CITY, $charset);
$Address->setStreet1($user_data->ADDRESS, $charset);
$CardOwner->setAddress($Address);
$CardOwner->setPayerCountry($info["user_country"]["abbreviation"], $charset);
$CardOwner->setPayerName($PayerName);

$CreditCard =& Services_PayPal::getType('CreditCardDetailsType');
if (Services_PayPal::isError($CreditCard))
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = Site::expand_array($CreditCard);
		@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);
	$this->display_page($db);
	$db->Close();
	exit;
}
/*
DETERMINE CREDIT TYPE
PayPal Accepts the following Credit Cards:
-MasterCard: Must have a prefix of 51 to 55, and must be 16 digits in length.
-Visa: Must have a prefix of 4, and must be either 13 or 16 digits in length.
-American Express: Must have a prefix of 34 or 37, and must be 15 digits in length.
-Discover: Must have a prefix of 6011, and must be 16 digits in length.
*/
if (substr($cc_number,0,2)>="51" && substr($cc_number,0,2)<="55" && strlen($cc_number)=="16")
	$cc_type = 'MasterCard';
elseif (substr($cc_number,0,1)=="4" && ((strlen($cc_number)=="13" || strlen($cc_number)=="16")))
	$cc_type = 'Visa';
elseif ((substr($cc_number,0,2)=="34" || substr($cc_number,0,2)=="37") && strlen($cc_number)=="15")
	$cc_type = 'Amex';
elseif (substr($cc_number,0,4)=="6011" && strlen($cc_number)=="16")
	$cc_type = 'Discover';
else
{
	if (strlen($this->debug_email) > 0)
	{
		$handler_error_response = "Invalid Credit Card";
		@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__." - error",$handler_error_response."\n\n".$cc_number."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg,$handler_error_response);
	$this->display_page($db);
	$db->Close();
	exit;
}
$CreditCard->setCreditCardType($cc_type);
$CreditCard->setCardOwner($CardOwner);
$CreditCard->setExpYear($cc_exp_year, $charset);
$CreditCard->setExpMonth($cc_exp_month, $charset);
$CreditCard->setCreditCardNumber($cc_number, $charset);
$CreditCard->setCVV2($cvv2_code, $charset);

//******************************************************************************
//PROCESS DATA
$DoDirectPaymentRequestDetails =& Services_PayPal::getType('DoDirectPaymentRequestDetailsType');
if (Services_PayPal::isError($DoDirectPaymentRequestDetails))
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = Site::expand_array($DoDirectPaymentRequestDetails);
		@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);
	$this->display_page($db);
	$db->Close();
	exit;
}
$DoDirectPaymentRequestDetails->setIPAddress($info["ip"], $charset);
$DoDirectPaymentRequestDetails->setCreditCard($CreditCard);
$DoDirectPaymentRequestDetails->setPaymentDetails($PaymentDetails);
$DoDirectPaymentRequestDetails->setPaymentAction('Sale', $charset);
$DoDirectPayment =& Services_PayPal::getType('DoDirectPaymentRequestType');
if (Services_PayPal::isError($DoDirectPayment))
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = Site::expand_array($DoDirectPayment);
		@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);
	$this->display_page($db);
	$db->Close();
	exit;
}
$DoDirectPayment->setDoDirectPaymentRequestDetails($DoDirectPaymentRequestDetails);
$response = $caller->DoDirectPayment($DoDirectPayment);
if (Services_PayPal::isError($response))
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = Site::expand_array($DoDirectPayment);
		@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);
	$this->display_page($db);
	$db->Close();
	exit;
}
//SUCCESSFUL CONNECTION
if (is_array($response->Errors))
{
	foreach ($response->Errors as $value)
	{
		$error_short_msg .= $value->ShortMessage.", ";
		$error_long_msg .= $value->LongMessage.", ";
		$error_code .= $value->ErrorCode.", ";
		$severity_code .= $value->SeverityCode.", ";
	}
	$error_short_msg = rtrim($error_short_msg," ,");
	$error_long_msg = rtrim($error_short_msg," ,");
	$error_code = rtrim($error_short_msg," ,");
	$severity_code = rtrim($error_short_msg," ,");
}
else
{
	$error_short_msg = @$db->qstr($response->Errors->ShortMessage);
	$error_long_msg = @$db->qstr($response->Errors->LongMessage);
	$error_code = @$db->qstr($response->Errors->ErrorCode);
	$severity_code = @$db->qstr($response->Errors->SeverityCode);
}
$this->sql_query = "update ".$cc->CC_TRANSACTION_TABLE." set
	amount = ".$db->qstr($response->Amount->_value).",
	avs_code = ".$db->qstr($response->AVSCode).",
	cvv2_code = ".$db->qstr($response->CVV2Code).",
	trans_id = ".$db->qstr($response->TransactionID).",
	timestamp = ".$db->qstr($response->Timestamp).",
	ack = ".$db->qstr($response->Ack).",
	error_short_msg = ".$error_short_msg.",
	error_long_msg = ".$error_long_msg.",
	error_code = ".$error_code.",
	error_severity_code = ".$severity_code.",
	version = ".$db->qstr($response->Version).",
	build = ".$db->qstr($response->Build)."
	where transaction_id = ".$info["trans_id"];
$update_result = $db->Execute($this->sql_query);
if (strlen($this->debug_email) > 0)
{
	@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
}
if (!$update_result)
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = $this->sql_query;
		@mail($this->debug_email,"cc_initiate_paypal.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);
	$this->display_page($db);
	$db->Close();
	exit;
}
if ($response->Ack == 'Success')
	Site::cc_post_process($db,$cc,$info);
else
{
	if ($response->Ack || $error_long_msg)
		$handler_error_response = $response->Ack."<br>".$error_long_msg;
	else
		$handler_error_response =  "INTERNAL FAILURE";
 	Site::cc_post_process($db,$cc,$info,$handler_error_response);
}
?>
