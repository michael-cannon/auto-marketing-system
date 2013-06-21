<?php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

require_once './classes/site_class.php';
//NOCHEX PAYMENT HANDLER
/*** cURL-enabled PHP Script ***/

if ($subscription_renewal)
{
	$this->price_plan_id = $this->renewStorefrontSubscription ? '-1' : $this->price_plan_id;
	$this->classified_id = $this->classified_user_id;
	$this->sql_query = "insert into ".$this->nochex_transaction_table."
		(classified_id,user_id,first_name,last_name,address,city,state,country,zip,email,tax,amount,
		fax,company,description,renewal_length,subscription_renewal,price_plan_id)
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
	if ($this->renew_upgrade == 1)
	{
		$ad_description = "classified ad renewal";
		$ad_type = "classified ad renewal";
		$renewing = 1;
	}
	else
	{
		$ad_description = "classified ad uprgrade";
		$ad_type = "classified ad upgrade";
		$renewing = 0;
	}
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
	if (($this->renew_upgrade_variables["attention_getter"]) || ($this->renew_upgrade_variables["attention_getter_upgrade"])){
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
	$this->sql_query = "insert into ".$this->nochex_transaction_table."
		(classified_id,user_id,first_name,last_name,address,city,state,country,zip,email,tax,amount,
		fax,company,description,renew,bolding,better_placement,featured_ad,featured_ad_2,featured_ad_3,featured_ad_4,featured_ad_5,
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
		\"".$this->tax."\",
		\"".$this->total."\",
		\"".$user_data["fax"]."\",
		\"".$user_data["company_name"]."\",
		\"".$ad_description."\",
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
	$this->sql_query = "insert into ".$this->nochex_transaction_table."
		(user_id,first_name,last_name,address,city,state,country,zip,email,tax,amount,
		fax,company,description,account_balance)
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
		\"".$user_data["fax"]."\",
		\"".$user_data["company_name"]."\",
		\"balance purchase\",
		\"".$account_balance."\")";

	$this->total = $this->account_variables["price"];
	$ad_type = "classified subscription renewal";
}
elseif
($this->invoice_id)
{
	$this->sql_query = "insert into ".$this->nochex_transaction_table."
		(user_id,first_name,last_name,address,city,state,country,zip,email,tax,amount,
		fax,company,description,pay_invoice)
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
		\"".$user_data["fax"]."\",
		\"".$user_data["company_name"]."\",
		\"invoice payment\",
		\".$this->invoice_id.\")";

	$this->total = $this->invoice_total;
	$ad_type = "invoice payment";
}
else
{
	$this->sql_query = "insert into ".$this->nochex_transaction_table."
		(classified_id,user_id,first_name,last_name,address,city,state,country,zip,email,tax,amount,
		fax,company,description,ad_placement,price_plan_id)
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
		\"".$this->tax."\",
		\"".$this->total."\",
		\"".$user_data["fax"]."\",
		\"".$user_data["company_name"]."\",
		\"classified ad placement\",
		\"1\",
		\"".$this->users_price_plan."\")";

	$ad_type = "classified ad placement";
}
$trans_result = $db->Execute($this->sql_query);
$debug_email = "";
if (strlen($debug_email) > 0)
	mail($debug_email,"pre - 1 - NOCHEX",$this->sql_query);
if (!$trans_result)
{
	if (strlen($debug_email) > 0)
		@mail($debug_email,"pre 2 - NOCHEX - error",$nochex_url);
	Site::site_error($db);
	exit;
}

$trans_id = $db->Insert_ID();

$return_url = str_replace($this->configuration_data["classifieds_file_name"], "nochex_apc.php",$this->configuration_data["classifieds_url"]);
$nochex_url = "https://www.nochex.com/nochex.dll/checkout?";
$nochex_url .= "&receiver_email=".urlencode($this->configuration_data["nochex_id"]);
if ($account_balance)
{
	$nochex_url .= "&returnurl=".urlencode($this->configuration_data["classifieds_url"]."?a=4&b=18");
}
elseif ($this->invoice_id)
{
	$nochex_url .= "&returnurl=".urlencode($this->configuration_data["classifieds_url"]."?a=4&b=15");
}
elseif ($this->configuration_data["admin_approves_all_ads"])
{
	$nochex_url .= "&returnurl=".urlencode($this->configuration_data["classifieds_url"]);
}
else
{
	$nochex_url .= "&returnurl=".urlencode($this->configuration_data["classifieds_url"]."?a=2&b=".$this->classified_id);
}
$nochex_url .= "&responderurl=".urlencode($return_url);
$nochex_url .= "&firstname=".urlencode($user_data["firstname"]);
$nochex_url .= "&lastname=".urlencode($user_data["lastname"]);
$nochex_url .= "&firstline=".urlencode($user_data["address"]." ".$user_data["address_2"]);
$nochex_url .= "&town=".urlencode($user_data["city"]);
$nochex_url .= "&county=".urlencode($user_data["state"]);
$nochex_url .= "&postcode=".urlencode($user_data["zip"]);
if (strlen(trim($this->classified_variables["discount_code"])) > 0)
{
	$this->sql_query = "select * from ".$this->discount_codes_table." where
		discount_code = \"".urlencode(trim($this->classified_variables["discount_code"]))."\"
		and active = 1";
	$discount_check_result =  $db->Execute($this->sql_query);
	if (!$discount_check_result)
	{
		if (strlen($debug_email) > 0)
			@mail($debug_email,"pre 3 - NOCHEX - error",$nochex_url);
		$this->error_message = $this->messages[3501];
		return false;
	}
	elseif ($discount_check_result->RecordCount() == 1)
	{
		$show_email = $discount_check_result->FetchRow();
		if (strlen(trim($show_email["discount_email"])) > 0)
			$nochex_url .= "&email_address_sender=".urlencode($show_email["discount_email"]);
		else
			$nochex_url .= "&email_address_sender=".urlencode($user_data["email"]);
	}
	else
		$nochex_url .= "&email_address_sender=".urlencode($user_data["email"]);
}
else
	$nochex_url .= "&email_address_sender=".urlencode($user_data["email"]);
$nochex_url .= "&email_address_sender=".urlencode($this->configuration_data["site_email"]);
$nochex_url .= "&description=".urlencode($ad_type);
if ($this->invoice_id)
	$nochex_url .= "&ordernumber=".$this->invoice_id;
elseif ($account_balance)
	$nochex_url .= "&ordernumber=".$account_balance;
elseif ($this->subscription_renewal)
	$nochex_url .= "&ordernumber=".$this->subscription_renewal;
else
	$nochex_url .= "&ordernumber=".$this->classified_id;
$nochex_url .= "&amount=".$this->total;

$this->sql_query = "select * from $this->nochex_settings_table";
$merchant_settings_result =  $db->Execute($this->sql_query);
if (!$merchant_settings_result)
{
	if (strlen($debug_email) > 0)
		@mail($debug_email,"pre 4 - NOCHEX - error",$nochex_url);
	$this->error_message = $this->messages[3501];
	return false;
}
elseif ($merchant_settings_result->RecordCount() == 1)
{
	$merchant = $merchant_settings_result->FetchRow();
	$nochex_url .= "&logo=".$merchant["logo_path"];
	$nochex_url .= "&email=".$merchant["email"];
}

if ($this->subscription_renewal)
	$this->remove_renew_subscription_session($db,$this->session_id);
elseif ($this->renew_upgrade)
	$this->remove_renew_upgrade_session($db,$this->session_id);
elseif ($this->invoice_id)
	$this->remove_invoice_session($db,$this->session_id);
elseif ($account_balance)
	$this->remove_account_session($db,$this->session_id);
else
	$this->remove_sell_session($db,$this->session_id);

if (strlen($debug_email) > 0)
	@mail($debug_email,"pre - 5 - NOCHEX",$nochex_url);

header("Location: ".$nochex_url);
$db->Close();
exit;




?>
