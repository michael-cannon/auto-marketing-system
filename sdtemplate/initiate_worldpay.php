<? //initiate_worldpay.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$user_data = $this->get_user_data($db,$this->classified_user_id);
if ($subscription_renewal)
{
	$this->classified_id = $this->classified_user_id;
	$this->sql_query = "insert into ".$this->worldpay_transaction_table." 
		(classified_id,session_id,user_id,first_name,last_name,address,city,state,country,
		zip,email,tax,amount,fax,phone,company,transaction_description,renewal_length,subscription_renewal,price_plan_id)
		values 
		(".$this->classified_id.",
		\"".$this->session_id."\",
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
		\"".$user_data["phone"]."\",
		\"".$user_data["company_name"]."\",
		\"classified ad\",
		\"".$this->renew_subscription_variables["subscription_choice"]."\",
		\"1\",
		\"".$this->price_plan_id."\")";
	$ad_type = "classified subscription renewal";
}
elseif ($renew_upgrade)
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
		
	if (($this->renew_upgrade_variables["featured_ad_2"]) || ($this->renew_upgrade_variables["featured_ad_upgrade_2"]))
		$featured_ad_2 = 1;
	else
		$featured_ad_2 = 0;		

	if (($this->renew_upgrade_variables["featured_ad_3"]) || ($this->renew_upgrade_variables["featured_ad_upgrade_3"]))
		$featured_ad_3 = 1;
	else
		$featured_ad_3 = 0;	
		
	if (($this->renew_upgrade_variables["featured_ad_4"]) || ($this->renew_upgrade_variables["featured_ad_upgrade_4"]))
		$featured_ad_4 = 1;
	else
		$featured_ad_4 = 0;	
		
	if (($this->renew_upgrade_variables["featured_ad_5"]) || ($this->renew_upgrade_variables["featured_ad_upgrade_5"]))
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
	
	$this->sql_query = "insert into ".$this->worldpay_transaction_table." 
		(classified_id,session_id,user_id,first_name,last_name,address,city,state,country,
		zip,email,tax,amount,fax,phone,company,transaction_description,renew,bolding,better_placement,featured_ad,
		featured_ad_2,featured_ad_3,featured_ad_4,featured_ad_5,attention_getter,
		attention_getter_choice,renewal_length,use_credit_for_renewal,price_plan_id)
		values 
		(".$this->classified_id.",
		\"".$this->session_id."\",
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
		\"".$user_data["phone"]."\",
		\"".$user_data["company_name"]."\",
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
	$ad_type = "classified ad renewal";
}
elseif ($account_balance)
{
	$sql_query = "insert into geodesic_cc_linkpoint_transactions
		(user_id,first_name,last_name,address,city,state,country,zip,email,tax,amount,
		fax,phone,company,transaction_description,account_balance)
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
		\"".$user_data["phone"]."\",
		\"".$user_data["company_name"]."\",
		\"balance purchase\",
		\".$account_balance.\")";

	$this->total = $this->account_variables["price"];
	$ad_type = "account balance purchase";
}
elseif ($this->invoice_id)
{
	$sql_query = "insert into geodesic_cc_linkpoint_transactions
		(user_id,first_name,last_name,address,city,state,country,zip,email,tax,amount,
		fax,phone,company,transaction_description,pay_invoice)
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
		\"".$user_data["phone"]."\",
		\"".$user_data["company_name"]."\",
		\"invoice payment\",
		\".$this->invoice_id.\")";

	$this->total = $this->invoice_total;
	$ad_type = "invoice payment";
}
else
{
	$this->sql_query = "insert into ".$this->worldpay_transaction_table." 
		(classified_id,session_id,user_id,first_name,last_name,address,city,state,country,zip,email,tax,amount,fax,
		phone,company,transaction_description,ad_placement,price_plan_id)
		values 
		(".$this->classified_id.",
		\"".$this->session_id."\",
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
		\"".$user_data["phone"]."\",
		\"".$user_data["company_name"]."\",
		\"classified ad\",
		\"1\",
		\"".$this->users_price_plan."\")";
	$ad_type = "classified ad";
}
$worldpay_transaction_result = $db->Execute($this->sql_query);

if (!$worldpay_transaction_result)
{
	//echo $this->sql_query." is the query <br>\n";
	$db->Close();
	exit;
}

$trans_id = $db->Insert_ID();

$this->sql_query = "select * from ".$this->worldpay_configuration_table;
$worldpay_data_result = $db->Execute($this->sql_query);

if (!$worldpay_data_result)
{
	//echo $this->sql_query." is the query <br>\n";
	$db->Close();
	exit;
}
$show_worldpay = $worldpay_data_result->FetchRow();

if ((!$this->subscription_renewal) && (!$this->invoice_id) && (!$account_balance))
{
	$sql_query = "update ".$this->classifieds_table." set cc_transaction_type = 6 where id = ".$this->classified_id;
	$update_cc_result = $db->Execute($sql_query);
	if (strlen($debug_email) > 0)
		@mail($debug_email,"pre - 3 - worlpay",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
	if (!$update_cc_result)
	{
	//echo $sql_query." is the query <br>\n";
		$db->Close();
		exit;
	}
}

$return_url = str_replace($this->configuration_data["classifieds_file_name"], "worldpay_process.php",$this->configuration_data["classifieds_url"]);
$return_url = str_replace("http://", "",$return_url);

$worldpay_url = "https://select.worldpay.com/wcc/purchase?";
$worldpay_url .=  "instId=".$show_worldpay["worldpay_installation_id"];
$worldpay_url .=  "&cartId=".$trans_id;
$worldpay_url .=  "&M_sessionId=".$this->session_id;
$worldpay_url .=  "&M_customerId=".$this->classified_user_id;
$worldpay_url .=  "&MC_callback=".$return_url;
$worldpay_url .=  "&name=".urlencode($user_data["firstname"]." ".$user_data["lastname"]);
if (strlen(trim($user_data["address"])) > 0)
	$worldpay_url .=  "&address=".urlencode($user_data["address"]." ".$user_data["address_2"]);
if (strlen(trim($user_data["country"])) > 0)
	$worldpay_url .=  "&country=".urlencode($user_data["country"]);
if (strlen(trim($user_data["zip"])) > 0)
	$worldpay_url .=  "&postcode=".urlencode($user_data["zip"]);
if (strlen(trim($user_data["phone"])) > 0)
	$worldpay_url .=  "&tel=".urlencode($user_data["phone"]);
if (strlen(trim($user_data["email"])) > 0)
	$worldpay_url .=  "&email=".urlencode($user_data["email"]);
$worldpay_url .=  "&amount=".$this->total;
$worldpay_url .=  "&currency=".urlencode($show_worldpay["currency_type"]);
$worldpay_url .=  "&desc=".urlencode($ad_type);
if ($show_worldpay["test_mode"])
	$worldpay_url .=  "&testMode=100";
header("Location: ".$worldpay_url);
$db->Close();
exit;
?>