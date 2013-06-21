<? //cc_initiate_authorizenet.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

if ($subscription_renewal)
{
	$this->classified_id = $this->classified_user_id;
	$sql_query = "insert into geodesic_cc_linkpoint_transaction
		(classified_id,user_id,first_name,last_name,address,city,state,country,zip,email,card_num,decryption_key,exp_date,tax,amount,
		fax,company,description,renewal_length,subscription_renewal)
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
		\"".$this->renew_subscription_variables["cc_number"]."\",
		\"".$this->renew_subscription_variables["decryption_key"]."\",
		\"".$this->renew_subscription_variables["cc_exp_month"]."/".$this->renew_subscription_variables["cc_exp_year"]."\",
		\"".$this->tax."\",
		\"".$this->total."\",
		\"".$user_data["fax"]."\",
		\"".$user_data["company_name"]."\",
		\"classified subscription renewal\",
		\"".$this->renew_subscription_variables["subscription_choice"]."\",
		\"1\",
		\"".$this->price_plan_id."\")";

	$cc_number = Site::decrypt($this->renew_subscription_variables["cc_number"], $this->renew_subscription_variables["decryption_key"]);
	$cc_exp_month = $this->renew_subscription_variables["cc_exp_month"];
	$cc_exp_year = $this->renew_subscription_variables["cc_exp_year"];
	$ad_type = "classified subscription renewal";
	$ad_placement = 0;
}
elseif ($this->renew_upgrade)
{
	if (($this->renew_upgrade_variables["ad_renewal"]))
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

	$sql_query = "insert into geodesic_cc_linkpoint_transactions
		(classified_id,user_id,first_name,last_name,address,city,state,country,zip,email,card_num,decryption_key,exp_date,tax,amount,
		fax,company,description,renew,bolding,better_placement,featured_ad,featured_ad_2,featured_ad_3,featured_ad_4,featured_ad_5,
		attention_getter,attention_getter_choice,renewal_length,
		use_credit_for_renewal)
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
		\"".$this->renew_upgrade_variables["cc_number"]."\",
		\"".$this->renew_upgrade_variables["decryption_key"]."\",
		\"".$this->renew_upgrade_variables["cc_exp_month"]."/".$this->renew_upgrade_variables["cc_exp_year"]."\",
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

	$cc_number = Site::decrypt($this->renew_upgrade_variables["cc_number"], $this->renew_upgrade_variables["decryption_key"]);
	$cc_exp_month = $this->renew_upgrade_variables["cc_exp_month"];
	$cc_exp_year = $this->renew_upgrade_variables["cc_exp_year"];
	$ad_placement = 0;
}
elseif ($account_balance)
{
	$sql_query = "insert into geodesic_cc_linkpoint_transactions
		(user_id,first_name,last_name,address,city,state,country,zip,email,card_num,decryption_key,exp_date,tax,amount,
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
		\"".$this->account_variables["cc_number"]."\",
		\"".$this->account_variables["decryption_key"]."\",
		\"".$this->account_variables["cc_exp_month"]."/".$this->account_variables["cc_exp_year"]."\",
		\"0\",
		\"".$this->account_variables["price"]."\",
		\"".$user_data["fax"]."\",
		\"".$user_data["company_name"]."\",
		\"balance purchase\",
		\".$account_balance.\")";

	$this->total = $this->account_variables["price"];
	$cc_number = Site::decrypt($this->account_variables["cc_number"], $this->account_variables["decryption_key"]);
	$cc_exp_month = $this->account_variables["cc_exp_month"];
	$cc_exp_year = $this->account_variables["cc_exp_year"];
	$ad_type = "account balance purchase";
}
elseif ($this->invoice_id)
{
	$sql_query = "insert into geodesic_cc_linkpoint_transactions
		(user_id,first_name,last_name,address,city,state,country,zip,email,card_num,decryption_key,exp_date,tax,amount,
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
		\"".$this->invoice_variables["cc_number"]."\",
		\"".$this->invoice_variables["decryption_key"]."\",
		\"".$this->invoice_variables["cc_exp_month"]."/".$this->invoice_variables["cc_exp_year"]."\",
		\"0\",
		\"".$this->invoice_total."\",
		\"".$user_data["fax"]."\",
		\"".$user_data["company_name"]."\",
		\"invoice payment\",
		\".$this->invoice_id.\")";

	$this->total = $this->invoice_total;
	$cc_number = Site::decrypt($this->account_variables["cc_number"], $this->account_variables["decryption_key"]);
	$cc_exp_month = $this->account_variables["cc_exp_month"];
	$cc_exp_year = $this->account_variables["cc_exp_year"];
	$ad_type = "invoice payment";
}
else
{
	$sql_query = "insert into geodesic_cc_linkpoint_transactions
		(classified_id,user_id,first_name,last_name,address,city,state,country,zip,email,card_num,decryption_key,exp_date,tax,amount,
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
		\"".$this->classified_variables["cc_number"]."\",
		\"".$this->classified_variables["decryption_key"]."\",
		\"".$this->classified_variables["cc_exp_month"]."/".$this->classified_variables["cc_exp_year"]."\",
		\"".$this->tax."\",
		\"".$this->total."\",
		\"".$user_data["fax"]."\",
		\"".$user_data["company_name"]."\",
		\"classified ad placement\",
		\"1\",
		\"".$this->users_price_plan."\")";

	$cc_number = $this->classified_variables["cc_number"];
	$cc_number = Site::decrypt($this->classified_variables["cc_number"], $this->classified_variables["decryption_key"]);
	$cc_exp_month = $this->classified_variables["cc_exp_month"];
	$cc_exp_year = $this->classified_variables["cc_exp_year"];
	$ad_type = "classified ad placement";
	$ad_placement = 1;
}

$debug_email = "james@geodesicsolutions.com";
if (strlen($debug_email) > 0)
	mail($debug_email,"pre - 1 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
$cc_transaction_result = $db->Execute($sql_query);
if (!$cc_transaction_result)
{
	//echo $sql_query." is the query <br>\n";
	$db->Close();
	exit;
}

$trans_id = $db->Insert_ID();

$sql_query = "select * from geodesic_cc_linkpoint";
$cc_table_result = $db->Execute($sql_query);
if (strlen($debug_email) > 0)
	@mail($debug_email,"pre - 2 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
if (!$cc_table_result)
{
	//echo $sql_query." is the query <br>\n";
	$db->Close();
	exit;
}

$show_linkpoint = $cc_table_result->FetchRow();
if ((!$this->subscription_renewal) && (!$this->invoice_id) && (!$account_balance))
{
	$sql_query = "update ".$this->classifieds_table." set cc_transaction_type = ".$cc["cc_id"]." where id = ".$this->classified_id;
	$update_cc_result = $db->Execute($sql_query);
	if (strlen($debug_email) > 0)
		@mail($debug_email,"pre - 3 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
	if (!$update_cc_result)
	{
	//echo $sql_query." is the query <br>\n";
		$db->Close();
		exit;
	}
}

if ($show_linkpoint["demo_mode"])
	$result_setting = "GOOD";
else
	$result_setting = "LIVE";

$xml = "
<order>
	<billing>
		<name>".$user_data["firstname"]." ".$user_data["lastname"]."</name>
		<address1>".$user_data["address"]."</address1>
		<address2>".$user_data["address_2"]."</address2>
		<company>".$user_data["company_name"]."</company>
		<city>".$user_data["city"]."</city>
		<state>".$user_data["state"]."</state>
		<zip>".$user_data["zip"]."</zip>
		<country>".$user_data["country"]."</country>
		<email>".$user_data["email"]."</email>
		<phone>".$user_data["phone"]."</phone>
	</billing>
	<orderoptions>
		<result>".$result_setting."</result>
		<ordertype>SALE</ordertype>
	</orderoptions>
	<merchantinfo>
		<configfile>".$show_linkpoint["store_number"]."</configfile>
	</merchantinfo>
	<creditcard>
		<cardnumber>".$cc_number."</cardnumber>
		<cardexpmonth>".$cc_exp_month."</cardexpmonth>
		<cardexpyear>".$cc_exp_year."</cardexpyear>
	</creditcard>
	<payment>
		<chargetotal>".$this->total."</chargetotal>
	</payment>
</order>";
if (strlen($debug_email) > 0)
	@mail($debug_email,"pre - 3a - linkpoint - xml",$xml."\n\n".date("l dS of F Y h:i:s A"));

if ($show_linkpoint["demo_mode"])
	$hoststring = "https://staging.linkpt.net:1129/lpc/servlet/lppay";
else
	$hoststring = "https://secure.linkpt.net:1129/";

$cert	= $show_linkpoint["ssl_path"];

if (strlen($debug_email) > 0)
	@mail($debug_email,"pre - 3b - linkpoint - cert & https",$cert."\n".$hoststring."\n".date("l dS of F Y h:i:s A"));

# use PHP built-in curl functions
$ch = curl_init ();
curl_setopt ($ch, CURLOPT_URL,$hoststring);
curl_setopt ($ch, CURLOPT_POST, 1);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $xml); # the string we built above
curl_setopt ($ch, CURLOPT_SSLCERT, $cert);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
//if (strlen($debug_email) > 0)
//	curl_setopt ($ch, CURLOPT_VERBOSE, 1);	//// optional - verbose debug output
											// not for production use
$linkpoint_result = curl_exec ($ch);
curl_close ($ch);

if (!$linkpoint_result)
{
	//bad or no return
	if (strlen($debug_email) > 0)
		@mail($debug_email,"post - 4 - linkpoint - no return","there was an error in talking to linkpoint.\n\nnothing was returned from linkpoint"."\n\n".date("l dS of F Y h:i:s A"));
	echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$this->configuration_data["classifieds_url"]."?a=1&credit_approval=2&declined=no+communication+response\">";
	exit;
}
else
{
	if (strlen($debug_email))
		@mail($debug_email,"post - 5 - linkpoint result xml returned",$linkpoint_result."\n\n".date("l dS of F Y h:i:s A"));

	//convert XML to an array
	preg_match_all ("/<(.*?)>(.*?)\</", $linkpoint_result, $outarr, PREG_SET_ORDER);

	$n = 0;
	$temp = "";
	while (isset($outarr[$n]))
	{
		$linkpoint[$outarr[$n][1]] = strip_tags($outarr[$n][0]);
		$temp .= $linkpoint[$outarr[$n][1]]." - ".$outarr[$n][1]." = ".strip_tags($outarr[$n][0])."\n\n";
		$n++;
	}
	if (strlen($debug_email) > 0)
		@mail($debug_email,"post - 6 - linkpoint - returned variables",$temp."\n\n".date("l dS of F Y h:i:s A"));

	$sql_query = "update geodesic_cc_linkpoint_transactions set
		r_avs = \"".$linkpoint["r_avs"]."\",
		r_ordernum = \"".$linkpoint["r_ordernum"]."\",
		r_error = \"".$linkpoint["r_error"]."\",
		r_approved = \"".$linkpoint["r_approved"]."\",
		r_code = \"".$linkpoint["r_code"]."\",
		r_message = \"".$linkpoint["r_message"]."\",
		r_time = \"".$linkpoint["r_time"]."\",
		r_ref = \"".$linkpoint["r_ref"]."\",
		r_tdate = \"".$linkpoint["r_tdate"]."\",
		r_tax = \"".$linkpoint["r_tax"]."\",
		r_authresponse = \"".$linkpoint["r_authresponse"]."\",
		r_csp = \"".$linkpoint["r_csp"]."\",
		r_vpasresponse = \"".$linkpoint["r_vpasresponse"]."\"
		where linkpoint_transaction_id = ".$trans_id;
	$result = $db->Execute($sql_query);
	if (strlen($debug_email) > 0)
		@mail($debug_email,"post - 7 - linkpoint ",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
	if (!$result)
	{
		//echo $sql_query." is the query <br>\n";
		$db->Close();
		exit;
	}

	if ($linkpoint["r_approved"] == "APPROVED")
	{
		if ($this->invoice_id > 0)
		{
			$this->sql_query = "update ".$this->invoices_table." set date_paid = ".$this->shifted_time($db)." where invoice_id = ".$this->invoice_id;
			if ($this->debug_invoices) echo $this->sql_query."<br>";
			$paid_result = $db->Execute($this->sql_query);
			if (strlen($debug_email) > 0)
				@mail($debug_email,"post - 11 - authorize.net - aim",$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
			if (!$paid_result)
			{
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 11a - authorize.net - aim - error",$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
				$db->Close();
				exit;
			}
			else
			{
				$this->page_id = 180;
				$this->get_text($db);
				$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
				$this->body .= "<tr class=page_title><td>".urldecode($this->messages[3142])."</td>\n</tr>\n";
				$this->body .= "<tr class=page_description><td>".urldecode($this->messages[3143])."</td>\n</tr>\n";
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[3167])."</td>\n</tr>\n";
				$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data["classifieds_url"]."?a=4&b=3\" class=my_account_link >";
				$this->body .=urldecode($this->messages[2536])."</A>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
				$this->remove_account_session($db,$this->session_id);
				$this->display_page($db);
				$db->Close();
				exit;
			}
		}
		elseif ($account_balance > 0)
		{
			//update approval for this balance transaction
			$this->update_balance_approval($db,$account_balance,$trans_id);

			//add to the users balance
			//user data still exists
			$new_balance = $user_data["account_balance"] + $this->account_variables["price"];
			$sql_query = "update ".$this->userdata_table." set
				account_balance = ".$new_balance."
				where id = ".$this->classified_user_id;
			$update_balance_results = $db->Execute($sql_query);
			if (!$update_balance_results)
			{
				return false;
			}
			else
			{
				$this->page_id = 175;
				$this->get_text($db);
				$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
				$this->body .= "<tr class=page_title><td>".urldecode($this->messages[2531])."</td>\n</tr>\n";
				$this->body .= "<tr class=page_description><td>".urldecode($this->messages[2532])."</td>\n</tr>\n";
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[2537])." ".
					$this->configuration_data["precurrency"]." ".sprintf("%01.2f",$new_balance).
					" ".$this->configuration_data["postcurrency"]."</td>\n</tr>\n";
				$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data["classifieds_url"]."?a=4&b=3\" class=my_account_link >";
				$this->body .=urldecode($this->messages[2536])."</A>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
				$this->remove_account_session($db,$this->session_id);
				$this->display_page($db);
				$db->Close();
				exit;
			}
		}
		elseif ($subscription_renewal)
		{
			$sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
			if (strlen($debug_email) > 0)
				@mail($debug_email,"post - 8 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
			$price_plan_id_result = $db->Execute($sql_query);
			if ($price_plan_id_result->RecordCount() == 1)
			{
				$show_price_plan = $price_plan_id_result->FetchRow();
				$sql_query = "select * from ".$this->subscription_choices." where price_plan_id = ".$show_price_plan["price_plan_id"]."
					and period_id = ".$this->renew_subscription_variables["subscription_choice"]." order by value asc";
				$choices_result = $db->Execute($sql_query);
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 9 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));

				if (!$choices_result)
				{
					return false;
				}
				elseif ($choices_result->RecordCount() == 1 )
				{
					$show_choice = $choices_result->FetchRow();
					if ($show_choice["value"] !=0)
					{
						//check to see if currently subscribed
						$sql_query = "select * from ".$this->user_subscriptions_table." where user_id = ".$this->classified_user_id;
						$check_subscriptions_results = $db->Execute($sql_query);
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post - 10 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
						if (!$check_subscriptions_results)
						{
							return false;
						}
						elseif ($check_subscriptions_results->RecordCount() > 0)
						{
							//extend subscription period
							$show_subscription = $check_subscriptions_results->FetchRow();
							if ($show_subscription["subscription_expire"] > $this->shifted_time($db))
								$new_expire = ($show_subscription["subscription_expire"] + ($show_choice["value"] * 86400));
							else
								$new_expire = ($this->shifted_time($db) + ($show_choice["value"] * 86400));
							$sql_query = "update ".$this->user_subscriptions_table."
								set subscription_expire = ".$new_expire."
								where subscription_id = ".$show_subscription["subscription_id"];
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 11 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));

							$update_subscriptions_results = $db->Execute($sql_query);
							if (!$update_subscriptions_results)
							{
								return false;
							}
						}
						else
						{
							//enter new subscription period
							$new_expire = ($this->shifted_time($db) + ($show_choice["value"] * 86400));
							$sql_query = "insert into ".$this->user_subscriptions_table."
								(user_id,subscription_expire)
								values
								(".$this->classified_user_id.",".$new_expire.")";
							$insert_subscriptions_results = $db->Execute($sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 12 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));

							if (!$insert_subscriptions_results)
							{
								return false;
							}
						}
						$this->page_id = 109;
						$this->get_text($db);
						$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
						$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[1669])."</td>\n</tr>\n";
						$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[1670])."</td>\n</tr>\n";
						$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[1679])."</td>\n</tr>\n";
						$this->body .="<tr class=view_ad_link >\n\t<td><A HREF=\"".trim($this->configuration_data["classifieds_url"])."?a=4&b=3\" class=view_ad_link >";
						$this->body .=urldecode($this->messages[1672])."</A>\n\t</td>\n</tr>\n";
						$this->body .="</table>\n";
						$this->display_page($db);
						$db->Close();
						exit;
					}
				}
			}
		}
		elseif ($ad_placement == 1)
		{
			$this->page_id = 14;
			$this->get_text($db);
			//approved
			//turn on classified ad
			//send to success page
			//send a success message
			$sql_query = "select * from ".$this->classifieds_table."
				where id = ".$this->classified_id;
			if (strlen($debug_email) > 0)
				@mail($debug_email,"post - 13 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));

			$duration_result = $db->Execute($sql_query);
			if (!$duration_result)
			{
				//echo $sql_query." is the query <br>\n";
				$db->Close();
				exit;
			}
			$show_duration = $duration_result->FetchRow();
			$length_of_ad = ($show_duration["duration"] * 86400);

			//this is a new ad
			//when ad ends
			$current_time = $this->shifted_time($db);
			if  ($this->configuration_data["admin_approves_all_ads"])
			{
				$sql_query = "update ".$this->classifieds_table." set
					date = ".$current_time.",
					ends = ".($current_time + $length_of_ad)."
					where id = ".$this->classified_id;
			}
			else
			{
				$sql_query = "update ".$this->classifieds_table." set
					live = 1,
					date = ".$current_time.",
					ends = ".($current_time + $length_of_ad)."
					where id = ".$this->classified_id;
				
				// IDev Affiliate payment
				if($this->configuration_data["idevaffiliate"])
				{
					$idev_geoce_1 = $this->total;
					$idev_geoce_2 = "ad-".$show_duration["id"];
					include($this->configuration_data["idev_path"].'sale.php');
				}
			}
			if (strlen($debug_email) > 0)
				@mail($debug_email,"post - 14 - linkpoint",$sql_query."\n\nduration: ".$show_duration["duration"]."\n\ncurrent_time: ".$current_time."\n\n".date("l dS of F Y h:i:s A"));
			$result = $db->Execute($sql_query);
			if (!$result)
			{
				//echo $sql_query." is the query <br>\n";
				$db->Close();
				exit;
			}

			$this->update_category_count($db,$show_duration["category"]);
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
			$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[1365])."</td>\n</tr>\n";
			$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[177])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[180])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[653])."</td>\n</tr>\n";
			$this->body .="<tr class=view_ad_link >\n\t<td><A HREF=\"".trim($this->configuration_data["classifieds_url"])."?a=2&b=".$this->classified_id."\" class=view_ad_link >";
			$this->body .=urldecode($this->messages[181])."</A>\n\t</td>\n</tr>\n";
			$this->body .="</table>\n";
			$this->check_subscriptions_and_credits($db);
			if (!$this->renew_upgrade)
				$this->remove_sell_session($db,$this->session_id);
			if ($this->configuration_data["send_successful_placement_email"])
				$this->sell_success_email($db,$this->classified_id);
			$this->page_id = 14;
			$this->display_page($db);
			$db->Close();
			exit;
			//echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$this->configuration_data["classifieds_url"]."?a=1&credit_approval=1\">";
		}
		else
		{
			$this->page_id = 58;
			$this->get_text($db);

			$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
			$this->body .= "<tr class=page_title><td>".urldecode($this->messages[850])."</td>\n</tr>\n";
			$this->body .= "<tr class=page_description><td>".urldecode($this->messages[851])."</td>\n</tr>\n";

			$sql_query = "select * from ".$this->classifieds_table." where id = ".$this->classified_id;
			if (strlen($debug_email) > 0)
				@mail($debug_email,"post - 15 - linkpoint",$sql_query."\n\nrenew_upgrade: ".$this->renew_upgrade."\n\n".date("l dS of F Y h:i:s A"));

			$classified_result = $db->Execute($sql_query);
			if (!$classified_result)
			{
				//echo $sql_query." is the query <br>\n";
				$this->remove_renew_upgrade_session($db,$this->session_id);
				$db->Close();
				exit;
			}
			$show_classified = $classified_result->FetchRow();

			if (($this->renew_upgrade == 1) && ($this->renew_upgrade_variables["renewal_length"] > 0))
			{
				if ($show_classified["ends"] > $this->shifted_time($db))
					$new_expire = ($show_classified["ends"] + ($this->renew_upgrade_variables["renewal_length"] * 86400));
				else
					$new_expire = ($this->shifted_time($db) + ($this->renew_upgrade_variables["renewal_length"] * 86400));
				if  ($this->configuration_data["admin_approves_all_ads"])
				{
					$sql_query = "update geodesic_classifieds set
						date = \"".$this->shifted_time($db)."\",
						ends = \"".$new_expire."\",
						renewal_payment_expected = 0,
						better_placement = \"0\",
						featured_ad = \"0\",
						bolding = \"0\",
						attention_getter = \"0\",
						featured_ad_2 = \"0\",
						featured_ad_3 = \"0\",
						featured_ad_4 = \"0\",
						featured_ad_5 = \"0\"
						where id = ".$this->classified_id;
				}
				else
				{
					$sql_query = "update geodesic_classifieds set
						ends = \"".$new_expire."\",
						date = \"".$this->shifted_time($db)."\",
						renewal_payment_expected = 0,
						better_placement = \"0\",
						featured_ad = \"0\",
						bolding = \"0\",
						attention_getter = \"0\",
						featured_ad_2 = \"0\",
						featured_ad_3 = \"0\",
						featured_ad_4 = \"0\",
						featured_ad_5 = \"0\",
						live = 1
						where id = ".$this->classified_id;
				}
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 16 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
				$renew_result = $db->Execute($sql_query);
				if (!$renew_result)
				{
					$this->body .= "<tr class=error_message><td>".urldecode($this->messages[857])."</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->display_page($db);
					$this->remove_renew_upgrade_session($db,$this->session_id);
					$db->Close();
					exit;
				}
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[852])."</td>\n</tr>\n";

				if ($this->renew_upgrade_variables["use_credit_for_renewal"])
				{
					$sql_query = "select * from geodesic_classifieds_user_credits where user_id = ".$this->classified_user_id." order by credits_expire asc limit 1";
					$credits_results = $db->Execute($sql_query);
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post - 17 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
					if (!$credits_results)
					{
						return false;
					}
					elseif ($credits_results->RecordCount() == 1)
					{
						//remove one of these credits
						$show_credits = $credits_results->FetchRow();
						if ($show_credits["credit_count"] == 1)
						{
							//delete from the table
							$sql_query = "delete from geodesic_classifieds_user_credits where
								credits_id = ".$show_credits["credits_id"]."
								and user_id = ".$this->classified_user_id;
							$remove_credits_results = $db->Execute($sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 18 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
							if (!$remove_credits_results)
							{
								return false;
							}
						}
						else
						{
							//remove one from the credit count
							$sql_query = "update geodesic_classifieds_user_credits set
								credit_count = ".($show_credits["credit_count"] - 1)."
								where credits_id = ".$show_credits["credits_id"]."
								and user_id = ".$this->classified_user_id;
							$remove_credit = $db->Execute($sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 19 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));

							if (!$remove_credit)

							{

								return false;

							}
						}
					}
				}
				else
				{
					// If not using credits to pay for renewal then send it to IDev
					// IDev Affiliate payment
					if($this->configuration_data["idevaffiliate"] && $this->configuration_data["idev_renewal"])
					{
						$idev_geoce_1 = $this->total;
						$idev_geoce_2 = "ad-renew-".$show_duration["id"];
						include($this->configuration_data["idev_path"].'sale.php');
					}
				}
			}
			else
			{
				// Upgrade auction
				// IDev Affiliate payment
				if($this->configuration_data["idevaffiliate"] && $this->configuration_data["idev_upgrade"])
				{
					$idev_geoce_1 = $this->total;
					$idev_geoce_2 = "ad-upgrade-".$show_duration["id"];
					include($this->configuration_data["idev_path"].'sale.php');
				}
			}

			if ($bolding)
			{
				$sql_query = "update geodesic_classifieds set bolding = 1 where id = ".$this->classified_id;
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 20 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
				$bolding_result = $db->Execute($sql_query);
				if (!$bolding_result)
				{
					$this->body .= "<tr class=error_message><td>".urldecode($this->messages[857])."</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->display_page($db);
					$this->remove_renew_upgrade_session($db,$this->session_id);
					$db->Close();
					exit;
				}
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[853])."</td>\n</tr>\n";
			}

			if ($better_placement)
			{
				$sql_query = "update geodesic_classifieds set better_placement = 1 where id = ".$this->classified_id;

				if (strlen($debug_email) > 0)

					@mail($debug_email,"post - 21 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));

				$better_placement_result = $db->Execute($sql_query);

				if (!$better_placement_result)

				{

					$this->body .= "<tr class=error_message><td>".urldecode($this->messages[857])."</td>\n</tr>\n";

					$this->body .= "</table>\n";

					$this->display_page($db);

					$this->remove_renew_upgrade_session($db,$this->session_id);

					$db->Close();

					exit;

				}

				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[854])."</td>\n</tr>\n";

			}



			if ($featured_ad)
			{
				$sql_query = "update geodesic_classifieds set featured_ad = 1 where id = ".$this->classified_id;
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 22 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
				$featured_ad_result = $db->Execute($sql_query);
				if (!$featured_ad_result)
				{
					$this->body .= "<tr class=error_message><td>".urldecode($this->messages[857])."</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->display_page($db);
					$this->remove_renew_upgrade_session($db,$this->session_id);
					$db->Close();
					exit;
				}
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[855])."</td>\n</tr>\n";
			}

			if ($featured_ad_2)
			{
				$sql_query = "update geodesic_classifieds set featured_ad_2 = 1 where id = ".$this->classified_id;
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 23 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
				$featured_ad_result = $db->Execute($sql_query);
				if (!$featured_ad_result)
				{
					$this->body .= "<tr class=error_message><td>".urldecode($this->messages[857])."</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->display_page($db);
					$this->remove_renew_upgrade_session($db,$this->session_id);
					$db->Close();
					exit;
				}
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[855])."</td>\n</tr>\n";
			}

			if ($featured_ad_3)

			{

				$sql_query = "update geodesic_classifieds set featured_ad_3 = 1 where id = ".$this->classified_id;
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 24 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
				$featured_ad_result = $db->Execute($sql_query);
				if (!$featured_ad_result)
				{
					$this->body .= "<tr class=error_message><td>".urldecode($this->messages[857])."</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->display_page($db);
					$this->remove_renew_upgrade_session($db,$this->session_id);
					$db->Close();
					exit;
				}
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[855])."</td>\n</tr>\n";
			}

			if ($featured_ad_4)
			{
				$sql_query = "update geodesic_classifieds set featured_ad_4 = 1 where id = ".$this->classified_id;
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 25 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
				$featured_ad_result = $db->Execute($sql_query);
				if (!$featured_ad_result)
				{
					$this->body .= "<tr class=error_message><td>".urldecode($this->messages[857])."</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->display_page($db);
					$this->remove_renew_upgrade_session($db,$this->session_id);
					$db->Close();
					exit;
				}
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[855])."</td>\n</tr>\n";
			}

			if ($featured_ad_5)
			{
				$sql_query = "update geodesic_classifieds set featured_ad_5 = 1 where id = ".$this->classified_id;
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 26 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
				$featured_ad_result = $db->Execute($sql_query);
				if (!$featured_ad_result)
				{
					$this->body .= "<tr class=error_message><td>".urldecode($this->messages[857])."</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->display_page($db);
					$this->remove_renew_upgrade_session($db,$this->session_id);
					$db->Close();
					exit;
				}
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[855])."</td>\n</tr>\n";
			}

			if ($attention_getter)
			{
				$sql_query = "select * from geodesic_choices where choice_id = ".$attention_getter;
				$attention_getter_result = $db->Execute($sql_query);
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 27 - linkpoint",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
				if (!$attention_getter_result)
				{
					$this->body .=$sql_query."<br>\n";
					$this->remove_renew_upgrade_session($db,$this->session_id);
					return false;
				}
				elseif ($attention_getter_result->RecordCount() == 1)
				{
					$show_attention_getter = $attention_getter_result->FetchRow();
					$attention_getter_url = $show_attention_getter["value"];
				}
				else
				{
					$this->classified_variables["attention_getter"] = 0;
					$attention_getter_url = "";
				}

				$sql_query = "update geodesic_classifieds set
					attention_getter = 1,
					attention_getter_url = \"".$attention_getter_url."\"
					where id = ".$this->classified_id;
				$attention_getter_update = $db->Execute($sql_query);
				//echo $sql_query."<br>\n";
				if (!$attention_getter_update)
				{
					$this->body .= "<tr class=error_message><td>".urldecode($this->messages[857])."</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->display_page($db);
					$this->remove_renew_upgrade_session($db,$this->session_id);
					$db->Close();
					exit;
				}
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[856])."</td>\n</tr>\n";
			}
			$this->remove_renew_upgrade_session($db,$this->session_id);
			$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data["classifieds_file_name"]."?a=4&b=1\" class=my_account_link >";
			$this->body .=urldecode($this->messages[860])."</A>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
		}
		$this->display_page($db);
		$db->Close();
		exit;
	}
	elseif (strlen($linkpoint["r_approved"]) > 0)
	{
		//declined
		//send to declined page
		//send a declined email
		if ($ad_placement == 1)
		{
			$this->page_id = 14;
			$this->get_text($db);
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
			$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[1365])."</td>\n</tr>\n";
			$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[177])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[655])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[654])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description >\n\t<td>";
			$this->body .=urldecode($linkpoint["x_response_reason_text"])."\n\t</td>\n</tr>";
			$this->body .="<tr class=view_ad_link >\n\t<td><A HREF=\"".$this->configuration_data["classifieds_file_name"]."?a=1\" class=view_ad_link >";
			$this->body .=urldecode($this->messages[861])."</A>\n\t</td>\n</tr>\n";
			$this->body .="</table>\n";
			$this->update_final_approval($db,0);
			$this->update_billing_approved($db,0);
		}
		else
		{
			$this->page_id = 58;
			$this->get_text($db);
			$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
			$this->body .= "<tr class=page_title><td>".urldecode($this->messages[850])."</td>\n</tr>\n";
			$this->body .= "<tr class=page_description><td>".urldecode($this->messages[851])."</td>\n</tr>\n";
			$this->body .= "<tr class=error_message><td>".urldecode($this->messages[857])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description >\n\t<td>";
			$this->body .=urldecode($linkpoint["x_response_reason_text"])."\n\t</td>\n</tr>";
			$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data["classifieds_file_name"]."?a=4&b=1\" class=my_account_link >";
			$this->body .=urldecode($this->messages[860])."</A>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->remove_renew_upgrade_session($db,$this->session_id);
		}
		$this->display_page($db);
		$db->Close();
		exit;
		//echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$this->configuration_data["classifieds_url"]."?a=1&credit_approval=2&declined=".urlencode($x_response_reason_text)."\">";
	}
	else
	{
		//error in transaction
		//send to error page
		//send message to admin
		if ($ad_placement)
		{
			$this->page_id = 14;
			$this->get_text($db);
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
			$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[1365])."</td>\n</tr>\n";
			$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[177])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[655])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[654])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description >\n\t<td>";
			$this->body .=urldecode($linkpoint["x_response_reason_text"])."\n\t</td>\n</tr>";
			$this->body .="<tr class=view_ad_link >\n\t<td><A HREF=\"".$this->configuration_data["classifieds_file_name"]."?a=1\" class=view_ad_link >";
			$this->body .=urldecode($this->messages[861])."</A>\n\t</td>\n</tr>\n";
			$this->body .="</table>\n";
			$this->update_final_approval($db,0);
			$this->update_billing_approved($db,0);
		}
		else
		{
			$this->page_id = 58;
			$this->get_text($db);
			$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
			$this->body .= "<tr class=page_title><td>".urldecode($this->messages[850])."</td>\n</tr>\n";
			$this->body .= "<tr class=page_description><td>".urldecode($this->messages[851])."</td>\n</tr>\n";
			$this->body .= "<tr class=error_message><td>".urldecode($this->messages[857])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description >\n\t<td>";
			$this->body .=urldecode($linkpoint["x_response_reason_text"])."\n\t</td>\n</tr>";
			$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data["classifieds_file_name"]."?a=4&b=1\" class=my_account_link >";
			$this->body .=urldecode($this->messages[860])."</A>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->remove_renew_upgrade_session($db,$this->session_id);
		}
		$this->display_page($db);
		$db->Close();
		exit;
		//echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$this->configuration_data["classifieds_url"]."?a=1&credit_approval=3&declined=".urlencode($x_response_reason_text)."\">";
	}
}
?>