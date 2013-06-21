<? //paypal_initiate.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$debug_email = "";
$email_msg = "LINE ".__LINE__."\n";
if (strlen($debug_email)>0) @mail($debug_email,"paypal_initiate.php",$email_msg);
do {
	$custom_id = md5(uniqid(rand()));
	$custom_id = substr( $custom_id, 0,16);
	$sql_query = "SELECT custom FROM ".$this->paypal_transaction_table." WHERE custom = \"".$custom_id."\"";
	//$this->body .=$this->sql_query." is the query<br>\n";
	$custom_id_result = $db->Execute($sql_query);
	if (!$custom_id_result)
	{
		$email_msg = "LINE ".__LINE__."\t"."error in query".$sql_query."\n";
		if (strlen($debug_email)>0) @mail($debug_email,"paypal_initiate.php",$email_msg);
		$db->Close();
		exit;
	}
} while ($custom_id_result->RecordCount() > 0);

$user_data = $this->get_user_data($db,$this->classified_user_id);
//insert transaction into paypal transaction table
if ($subscription_renewal)
{
	$this->price_plan_id = $this->renewStorefrontSubscription ? '-1' : $this->price_plan_id;
	$this->sql_query = "insert into ".$this->paypal_transaction_table."
	(user_id,receiver_email,item_name,item_number,quantity,
	num_cart_items,first_name,last_name,address_street,
	address_city,address_state,address_zip,payer_email,payer_id,custom,payment_gross,
	subscription_renewal,renewal_choice,price_plan_id)
	values
	(".$this->classified_user_id.",
	\"".$this->configuration_data["paypal_id"]."\",
	\"classified ad renewal\",
	\"1\",
	\"1\",
	\"1\",
	\"".$user_data->FIRSTNAME."\",
	\"".$user_data->LASTNAME."\",
	\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
	\"".$user_data->CITY."\",
	\"".$user_data->STATE."\",
	\"".$user_data->ZIP."\",
	\"".$user_data->EMAIL."\",
	\"".$user_data->ID."\",
	\"".$custom_id."\",
	\"".$this->total."\",
	\"1\",
	\"".$this->renew_subscription_variables["subscription_choice"]."\",
	\"".$this->price_plan_id."\")";

	$ad_type = "subscription renewal";
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
		$ad_description = "classified ad upgrade";
		$ad_type = "classified ad upgrade";
		$renewing = 0;
	}
	$bolding = ($this->renew_upgrade_variables["bolding"] || $this->renew_upgrade_variables["bolding_upgrade"]) ? 1 : 0;
	$better_placement = ($this->renew_upgrade_variables["better_placement"] || $this->renew_upgrade_variables["better_placement_upgrade"]) ? 1 : 0;
	$featured_ad = ($this->renew_upgrade_variables["featured_ad"] || $this->renew_upgrade_variables["featured_ad_upgrade"]) ? 1 : 0;
	$featured_ad_2 = ($this->renew_upgrade_variables["featured_ad_2"] || $this->renew_upgrade_variables["featured_ad_2_upgrade"]) ? 1 : 0;
	$featured_ad_3 = ($this->renew_upgrade_variables["featured_ad_3"] || $this->renew_upgrade_variables["featured_ad_3_upgrade"]) ? 1 : 0;
	$featured_ad_4 = ($this->renew_upgrade_variables["featured_ad_4"] || $this->renew_upgrade_variables["featured_ad_4_upgrade"]) ? 1 : 0;
	$featured_ad_5 = ($this->renew_upgrade_variables["featured_ad_5"] || $this->renew_upgrade_variables["featured_ad_5_upgrade"]) ? 1 : 0;
	if ($this->renew_upgrade_variables["attention_getter"] || $this->renew_upgrade_variables["attention_getter_upgrade"])
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
	if (($this->renew_upgrade_variables["ad_renewal"]))
	{
		$renewing = 1;
		$ad_type = "classified ad renewal";
	}
	else
	{
		$renewing = 0;
		$ad_type = "classified ad upgrade";
	}
	
	$this->sql_query = "insert into ".$this->paypal_transaction_table."
	(id,user_id,receiver_email,item_name,item_number,quantity,
	num_cart_items,first_name,last_name,address_street,
	address_city,address_state,address_zip,payer_email,payer_id,custom,payment_gross,
	renew,bolding,better_placement,featured_ad,featured_ad_2,featured_ad_3,featured_ad_4,featured_ad_5,
	attention_getter,attention_getter_choice,renewal_choice,renewal_length,use_credit_for_renewal,price_plan_id)
	values
	(".$this->classified_id.",
	".$this->classified_user_id.",
	\"".$this->configuration_data["paypal_id"]."\",
	\"".$ad_type."\",
	\"1\",
	\"1\",
	\"1\",
	\"".$user_data->FIRSTNAME."\",
	\"".$user_data->LASTNAME."\",
	\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
	\"".$user_data->CITY."\",
	\"".$user_data->STATE."\",
	\"".$user_data->ZIP."\",
	\"".$user_data->EMAIL."\",
	\"".$user_data->ID."\",
	\"".$custom_id."\",
	\"".$this->total."\",
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
	\"".$this->renew_upgrade_variables["attention_getter_choice_upgrade"]."\",
	\"".$this->renew_upgrade_variables["renewal_length"]."\",
	\"".$this->renew_upgrade_variables["use_credit_for_renewal"]."\",
	\"".$this->price_plan_id."\")";

	$ad_type = "classified ad renewal";

}
elseif ($account_balance)
{
	$this->sql_query = "insert into ".$this->paypal_transaction_table."
		(user_id,receiver_email,item_name,item_number, quantity,
		first_name,last_name,address_street,address_city,address_state,address_zip,
		payer_email,payer_id,custom,payment_gross,account_balance)
		values
		(".$this->classified_user_id.",
		\"".$this->configuration_data["paypal_id"]."\",
		\"balance purchase\",
		\"1\",
		\"1\",
		\"".$user_data->FIRSTNAME."\",
		\"".$user_data->LASTNAME."\",
		\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
		\"".$user_data->CITY."\",
		\"".$user_data->STATE."\",
		\"".$user_data->ZIP."\",
		\"".$user_data->EMAIL."\",
		\"".$user_data->ID."\",
		\"".$custom_id."\",
		\"".$this->account_variables["price"]."\",
		\"".$account_balance."\")";

	$this->total = $this->account_variables["price"];
	$ad_type = "account balance";
}
elseif ($this->invoice_id)
{
	$this->sql_query = "insert into ".$this->paypal_transaction_table."
		(user_id,receiver_email,item_name,item_number,quantity,
		first_name,last_name,address_street,address_city,address_state,address_zip,
		payer_email,payer_id,custom,payment_gross,pay_invoice)
		values
		(".$this->classified_user_id.",
		\"".$this->configuration_data["paypal_id"]."\",
		\"invoice payment for invoice id: ".$this->invoice_id."\",
		\"1\",
		\"1\",
		\"".$user_data->FIRSTNAME."\",
		\"".$user_data->LASTNAME."\",
		\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
		\"".$user_data->CITY."\",
		\"".$user_data->STATE."\",
		\"".$user_data->ZIP."\",
		\"".$user_data->EMAIL."\",
		\"".$user_data->ID."\",
		\"".$custom_id."\",
		\"".$this->invoice_total."\",
		\"".$this->invoice_id."\")";

	$this->total = $this->invoice_total;
	$ad_type = "invoice payment";

}
else
{
	$this->sql_query = "insert into ".$this->paypal_transaction_table."
	(id,user_id,receiver_email,item_name,item_number,quantity,
	num_cart_items,first_name,last_name,address_street,
	address_city,address_state,address_zip,payer_email,payer_id,custom,payment_gross,ad_placement,price_plan_id)
	values
	(".$this->classified_id.",
	".$this->classified_user_id.",
	\"".$this->configuration_data["paypal_id"]."\",
	\"classified ad\",
	\"1\",
	\"1\",
	\"1\",
	\"".$user_data->FIRSTNAME."\",
	\"".$user_data->LASTNAME."\",
	\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
	\"".$user_data->CITY."\",
	\"".$user_data->STATE."\",
	\"".$user_data->ZIP."\",
	\"".$user_data->EMAIL."\",
	\"".$user_data->ID."\",
	\"".$custom_id."\",
	\"".$this->total."\",
	\"1\",
	\"".$this->users_price_plan."\")";

	$ad_type = "classified ad purchase";
}

if (strlen(trim($this->configuration_data["paypal_item_label"])) > 0)
	$ad_type = $this->configuration_data["paypal_item_label"];
$email_msg = "LINE ".__LINE__."\n";
if (strlen($debug_email)>0) @mail($debug_email,"paypal_initiate.php",$email_msg);
$paypal_result = $db->Execute($this->sql_query);
//echo $this->sql_query."<br>\n";
if (!$paypal_result)
{
	$email_msg = "LINE ".__LINE__."\t"."error in query".$sql_query."\n";
	if (strlen($debug_email)>0) @mail($debug_email,"paypal_initiate.php",$email_msg);
	$db->Close();
	exit;
}

$trans_id = $db->Insert_ID();

//$paypal_url = "http://www.geodesicsolutions.com/products/classifieds/testing/ecom/paypal_test.php?";
$return_url = str_replace($this->configuration_data["classifieds_file_name"], "paypalipn.php",$this->configuration_data["classifieds_url"]);
$paypal_url = "https://www.paypal.com/cgi-bin/webscr?";
$paypal_url .= "&receiver_email=".urlencode($this->configuration_data["paypal_id"]);
$paypal_url .= "&return=".urlencode($this->configuration_data["classifieds_url"]);
$paypal_url .= "&notify_url=".urlencode($return_url);
$paypal_url .= "&business=".urlencode($this->configuration_data["paypal_id"]);
$paypal_url .= "&cmd=_xclick";
$paypal_url .= "&item_name=".urlencode($ad_type);
$paypal_url .= "&image_url=".urlencode($this->configuration_data["paypal_image_url"]);
$paypal_url .= "&item_number=1";
$paypal_url .= "&quantity=1";
$paypal_url .= "&shipping_amount=0";
$paypal_url .= "&currency_code=".trim($this->configuration_data["paypal_currency"]);
if ($this->configuration_data["paypal_currency_rate"] != 0)
{
	$paypal_url .= "&amount=".sprintf("%01.2f",(round(($this->configuration_data["paypal_currency_rate"] * $this->total) , 2)));
}
else
	$paypal_url .= "&amount=".sprintf("%01.2f",$this->total);
$paypal_url .= "&invoice=".$trans_id;
$paypal_url .= "&num_cart_items=1";
$paypal_url .= "&first_name=".urlencode($user_data->FIRSTNAME);
$paypal_url .= "&last_name=".urlencode($user_data->LASTNAME);
$paypal_url .= "&address_street=".urlencode($user_data->ADDRESS." ".$user_data->ADDRESS_2);
$paypal_url .= "&address_city=".urlencode($user_data->CITY);
$paypal_url .= "&address_state=".urlencode($user_data->STATE);
$paypal_url .= "&address_zip=".urlencode($user_data->ZIP);
$paypal_url .= "&payer_email=".urlencode($user_data->EMAIL);
$paypal_url .= "&payer_id=".urlencode($this->classified_user_id);
$paypal_url .= "&custom=".urlencode($custom_id);

$email_msg = "LINE ".__LINE__."\n".$paypal_url;
if (strlen($debug_email)>0) @mail($debug_email,"paypal_initiate.php",$email_msg);

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

$email_msg = "LINE ".__LINE__."\n";
if (strlen($debug_email)>0) @mail($debug_email,"paypal_initiate.php",$email_msg);

header("Location: ".$paypal_url);
$db->Close();
exit;
?>