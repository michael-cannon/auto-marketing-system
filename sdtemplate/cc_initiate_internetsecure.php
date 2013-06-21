<? //cc_initiate_internetsecure.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

//VARIABLES TO SEND
//merchantnumber
//language
//ReturnURL
//Products = Price::Qty::Code::Description::Flags
//----Products breaks
//----##.##::1::$this->classified_id::classified ad::$ad_type::

//taxes
//{PST} - Provincial Sales Tax
//{GST} - Goods and Service Tax
//{HST} - Harmonized Sales Tax

//test flags
//{TEST} - produces test that is approved

/*
VARIABLES RECEIVED
//variables passed back from Internet Secure Export Script
xxxName
xxxCompany
xxxAddress
xxxCity
xxxProvince
xxxCountry
xxxPostal
xxxEmail
xxxPhone
xxxcard_name
xxxCCType
xxxAmount
CustomerName
CustomerCompany
CustomerAddress
CustomerCity
CustomerProvince
CustomerCountry
CustomerPostalCode
CustomerEmail
CustomerPhone
Cardholder
MerchantNumber
Currency
Amount
SalesOrderNumber
receiptnumber
ApprovalCode
Verbage
NiceVerbage
CVV2Result
AVSResponseCode
Products
DoubleColonProducts
Language
KeySize
SecretKeySize
UserAgent
EntryTimeStamp
UnixTimeStamp
TimeStamp
Live
RefererURL
ip_address
ReturnURL
ReturnCGI
xxxVar1
xxxVar2
xxxVar3
xxxVar4
xxxVar5
*/

if ($this->subscription_renewal)
{
	$this->classified_id = $this->classified_user_id;
	$this->sql_query = "insert into ".$cc["cc_transaction_table"]."
		(classified_id,user_id,first_name,last_name,address,city,state,country,zip,phone,email,tax,amount,
		fax,company,description,renewal_length,subscription_renewal,price_plan_id)
		values
		(".$this->classified_user_id.",
		".$this->classified_user_id.",
		\"".$user_data["firstname"]."\",
		\"".$user_data["lastname"]."\",
		\"".$user_data["address"]." ".$user_data["address_2"]."\",
		\"".$user_data["city"]."\",
		\"".$user_data["state"]."\",
		\"".$user_data["country"]."\",
		\"".$user_data["zip"]."\",
		\"".$user_data["phone"]."\",
		\"".$user_data["email"]."\",
		\"".$this->tax."\",
		\"".$this->total."\",
		\"".$user_data["fax"]."\",
		\"".$user_data["company_name"]."\",
		\"classified subscription renewal\",
		\"".$this->renew_subscription_variables["subscription_choice"]."\",
		\"1\",
		\"".$this->price_plan_id."\")";

	$ad_type = "classified subscription renewal";
}
elseif ($this->renew_upgrade)
{
	if (($this->renew_upgrade_variables["ad_renewal"]))
		$renewing = 1;
	else
		$renewing = 0;

	if (($this->renew_upgrade_variables["bolding"]) || ($this->renew_upgrade_variables["bolding_upgrade"]))
		$bolding = 1;
	else
		$bolding = 0;

	if (($this->renew_upgrade_variables["better_placement"]) || ($this->renew_upgrade_variables["better_placement_upgrade"]))
		$better_placement = 1;
	else
		$better_placement = 0;

	if (($this->renew_upgrade_variables["featured_ad"]) || ($this->renew_upgrade_variables["featured_ad_upgrade"]))
		$featured_ad = 1;
	else
		$featured_ad = 0;

	if (($this->renew_upgrade_variables["featured_ad_2"]) || ($this->renew_upgrade_variables["featured_ad_2_upgrade"]))
		$featured_ad_2 = 1;
	else
		$featured_ad_2 = 0;

	if (($this->renew_upgrade_variables["featured_ad_3"]) || ($this->renew_upgrade_variables["featured_ad_3_upgrade"]))
		$featured_ad_3 = 1;
	else
		$featured_ad_3 = 0;

	if (($this->renew_upgrade_variables["featured_ad_4"]) || ($this->renew_upgrade_variables["featured_ad_4_upgrade"]))
		$featured_ad_4 = 1;
	else
		$featured_ad_4 = 0;

	if (($this->renew_upgrade_variables["featured_ad_5"]) || ($this->renew_upgrade_variables["featured_ad_5_upgrade"]))
		$featured_ad_5 = 1;
	else
		$featured_ad_5 = 0;

	if (($this->renew_upgrade_variables["attention_getter"]) || ($this->renew_upgrade_variables["attention_getter_upgrade"]))
	{
		$attention_getter = 1;
		if ($this->renew_upgrade_variables["attention_getter_choice"])
			$attention_getter_choice = $this->renew_upgrade_variables["attention_getter_choice"];
		else
			$attention_getter_choice = $this->renew_upgrade_variables["attention_getter_choice_upgrade"];
	}
	else
	{
		$attention_getter = 0;
		$attention_getter_choice = 0;
	}

	$this->sql_query = "insert into ".$cc["cc_transaction_table"]."
		(classified_id,user_id,first_name,last_name,address,city,state,country,zip,email,phone,tax,amount,description,
		renew,bolding,better_placement,featured_ad,featured_ad_2,featured_ad_3,featured_ad_4,featured_ad_5,
		attention_getter,attention_getter_choice,renewal_length,
		use_credit_for_renewal,price_plan_id)
		values
		(".$this->classified_id.",
		".$this->classified_user_id.",
		\"".$user_data["firstname"]."\",
		\"".$user_data["lastname"]."\",
		\"".$user_data["address"]." ".$user_data["address_2"]."\",
		\"".$user_data["city"]."\",
		\"".$user_data["state"]."\",
		\"".$user_data["country"]."\",
		\"".$user_data["zip"]."\",
		\"".$user_data["email"]."\",
		\"".$user_data["phone"]."\",
		\"".$this->tax."\",
		\"".$this->total."\",
		\"classified ad\",
		\"".$renewing."\",
		\"".$bolding."\",
		\"".$better_placement."\",
		\"".$featured_ad."\",
		\"".$featured_ad_2."\",
		\"".$featured_ad_3."\",
		\"".$featured_ad_4."\",
		\"".$featured_ad_5."\",
		\"".$attention_getter."\",
		\"".$attention_getter_choice."\",
		\"".$this->renew_upgrade_variables["renewal_length"]."\",
		\"".$this->renew_upgrade_variables["use_credit_for_renewal"]."\",
		\"".$this->price_plan_id."\")";
}
elseif ($account_balance)
{
	$sql_query = "insert into ".$cc["cc_transaction_table"]."
		(user_id,first_name,last_name,address,city,state,country,zip,email,tax,amount,
		description,account_balance)
		values
		(".$this->classified_user_id.",
		\"".$user_data["firstname"]."\",
		\"".$user_data["lastname"]."\",
		\"".$user_data["address"]." ".$user_data["address_2"]."\",
		\"".$user_data["city"]."\",
		\"".$user_data["state"]."\",
		\"".$user_data["country"]."\",
		\"".$user_data["zip"]."\",
		\"".$user_data["email"]."\",
		\"0\",
		\"".$this->account_variables["price"]."\",
		\"balance purchase\",
		\".$account_balance.\")";

	$this->total = $this->account_variables["price"];
	$ad_type = "account balance purchase";
}
elseif ($this->invoice_id)
{
	$sql_query = "insert into ".$cc["cc_transaction_table"]."
		(user_id,first_name,last_name,address,city,state,country,zip,email,tax,amount,
		description,pay_invoice)
		values
		(".$this->classified_user_id.",
		\"".$user_data["firstname"]."\",
		\"".$user_data["lastname"]."\",
		\"".$user_data["address"]." ".$user_data["address_2"]."\",
		\"".$user_data["city"]."\",
		\"".$user_data["state"]."\",
		\"".$user_data["country"]."\",
		\"".$user_data["zip"]."\",
		\"".$user_data["email"]."\",
		\"0\",
		\"".$this->invoice_total."\",
		\"invoice payment\",
		\".$this->invoice_id.\")";

	$this->total = $this->invoice_total;
	$ad_type = "invoice payment";
}
else
{
	$this->sql_query = "insert into ".$cc["cc_transaction_table"]."
		(classified_id,user_id,first_name,last_name,address,city,state,country,zip,email,phone,tax,amount,description,ad_placement,
		price_plan_id)
		values
		(".$this->classified_id.",
		".$this->classified_user_id.",
		\"".$user_data["firstname"]."\",
		\"".$user_data["lastname"]."\",
		\"".$user_data["address"]." ".$user_data["address_2"]."\",
		\"".$user_data["city"]."\",
		\"".$user_data["state"]."\",
		\"".$user_data["country"]."\",
		\"".$user_data["zip"]."\",
		\"".$user_data["email"]."\",
		\"".$user_data["phone"]."\",
		\"".$this->tax."\",
		\"".$this->total."\",
		\"classified ad\",
		\"1\",
		\"".$this->users_price_plan."\")";
}

$debug_email = "";
if (strlen($debug_email) > 0)
	@mail($debug_email,"pre - 1",$this->sql_query);
$cc_transaction_result = $db->Execute($this->sql_query);
if (!$cc_transaction_result)
{
	//echo $this->sql_query." is the query <br>\n";
	if (strlen($debug_email) > 0)
		@mail($debug_email,"pre - 1a",$db->ErrorMsg()." is the sql error");
	$db->Close();
	exit;
}
$trans_id = $db->Insert_ID();
$this->sql_query = "select * from ".$cc["cc_table"];
$cc_table_result = $db->Execute($this->sql_query);
if (strlen($debug_email) > 0)
	@mail($debug_email,"pre - 2",$this->sql_query);
if (!$cc_table_result)
{
	//echo $this->sql_query." is the query <br>\n";
	$db->Close();
	exit;
}
$show_internetsecure = $cc_table_result->FetchRow();

if ((!$this->subscription_renewal) && (!$this->invoice_id) && (!$account_balance))
{
	$this->sql_query = "update ".$this->classifieds_table." set cc_transaction_type = ".$cc["cc_id"]." where id = ".$this->classified_id;
	$update_cc_result = $db->Execute($this->sql_query);
	if (strlen($debug_email) > 0)
		@mail($debug_email,"pre - 3",$this->sql_query);
	if (!$update_cc_result)
	{
		//echo $this->sql_query." is the query <br>\n";
		$db->Close();
		exit;
	}
}

if ($this->subscription_renewal)
	$this->remove_renew_subscription_session($db,$this->session_id);
elseif ($this->renew_upgrade)
	$this->remove_renew_upgrade_session($db,$this->session_id);

//build redirect
$cc_url = "https://secure.internetsecure.com/process.cgi?";
$cc_url .= "MerchantNumber=".$show_internetsecure["merchantnumber"];
$cc_url .= "&language=".$show_internetsecure["language"];
$cc_url .= "&ReturnURL=".urlencode($this->configuration_data["classifieds_url"]."?a=2&b=".$this->classified_id);
$cc_url .= "&Products=".urlencode("Price::Qty::Code::Description::Flags");
$cc_url .= urlencode("|".$this->total."::1::".$trans_id."::classified ad::");
if ($show_internetsecure["demo_mode"])
{
	$cc_url .= urlencode("{TEST}");
}
if ($show_internetsecure["canadian_tax_method"] != "0")
{
	$cc_url .= urlencode(trim($show_internetsecure["canadian_tax_method"]));
}
$cc_url .= "&xxxName=".urlencode($user_data["firstname"]." ".$user_data["lastname"]);
$cc_url .= "&xxxAddress=".urlencode($user_data["address"]." ".$user_data["address_2"]);
$cc_url .= "&xxxCity=".urlencode($user_data["city"]);
$cc_url .= "&xxxProvince=".urlencode($user_data["state"]);
$cc_url .= "&xxxPostal=".urlencode($user_data["zip"]);
$cc_url .= "&xxxCountry=".urlencode($user_data["country"]);
$cc_url .= "&xxxEmail=".urlencode($user_data["email"]);
$cc_url .= "&xxxPhone=".urlencode($user_data["phone"]);

if (strlen($debug_email) > 0)
	@mail($debug_email,"pre - 4",$cc_url);
header("Location: ".$cc_url);
exit;
?>
