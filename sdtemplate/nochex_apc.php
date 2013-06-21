<?php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

//nochex_apc.php
/*** cURL-enabled Sample PHP Script ***/

// VARIABLES
$debug_email = "";
if (strlen($debug_email) > 0)
	@mail($debug_email,"post 1 - NOCHEX","");
if (!isset($_POST)) $_POST = &$HTTP_POST_VARS;
  foreach ($_POST AS $key => $value) {
  $values[] = $key."=".urlencode($value);
}
$work_string = @implode("&", $values);
if (strlen($debug_email) > 0)
	@mail($debug_email,"post 2 - NOCHEX",$work_string);
include_once("config.php");
include_once("classes/adodb.inc.php");
include_once("classes/site_class.php");
$db = &ADONewConnection('mysql');
if($persistent_connections)
{
	//echo " Persistent Connection <bR>";
	if (!$db->PConnect($db_host, $db_username, $db_password, $database))
	{
		echo "could not connect to database";
		exit;
	}
}
else
{
	//echo " No Persistent Connection <bR>";
	if (!$db->Connect($db_host, $db_username, $db_password, $database))
	{
		echo "could not connect to database";
		exit;
	}
}
$transaction_id = $_POST["transaction_id"];
$transaction_date = $_POST["transaction_date"];
$from_email = $_POST["from_email"];
$to_email = $_POST["to_email"];
$order_id = $_POST["order_id"];
$amount = $_POST["amount"];
$security_key = $_POST["security_key"];
$this->sql_query = "select * from $this->nochex_settings_table";
$nochex_result = $db->Execute($this->sql_query);
$nochex_settings = $nochex_result->FetchRow();
$url = ($nochex_settings["demo_mode"]) ? "https://www.nochex.com/nochex.dll/apc/testapc" : "https://www.nochex.com/nochex.dll/apc/apc";
$ch = curl_init ();
curl_setopt ($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_POST, true);
curl_setopt ($ch, CURLOPT_POSTFIELDSIZE, 0);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $work_string);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
curl_setopt ($ch, CURLOPT_SSLVERSION, 3);
curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
$output = curl_exec ($ch);
curl_close ($ch);
$response = preg_replace ("'Content-type: text/plain'si","",$output);
if ($response=="AUTHORISED")
{
	$subject = "NOCHEX ".$response;
	$msg = "NOCHEX RESPONSE: {$response} ";
	$msg.= "transaction id: {$transaction_id} ";
	$msg.= "transaction date: {$transaction_date} ";
	$msg.= "from email: {$from_email} ";
	$msg.= "to email: {$to_email} ";
	$msg.= "order id: {$order_id} ";
	$msg.= "amount: {$amount} ";
	$msg.= "security key: {$security_key} ";
	if (strlen($debug_email) > 0)
		@mail ($debug_email, "post 3 - ".$subject, $msg);

	$sql_query = "select * from geodesic_nochex_transactions where nochex_transaction_id = \"".$order_id."\"";
	$result = $db->Execute($sql_query);
	if (strlen($debug_email) > 0)
		@mail($debug_email,"post 4 - NOCHEX",$sql_query);
	if (!$result)
	{
		$db->Close();
		exit;
	}
	else
	{
		if ($result->RecordCount() == 1)
		{
			$show_transaction = $result->FetchRow();
			//this transaction exists
			//complete it
			$sql_query = "update geodesic_nochex_transactions set
				response = \"".$response."\",
				security_key = \"".$security_key."\",
				transaction_date = \"".$transaction_date."\",
				amount = \"".$amount."\",
				where nochex_transaction_id = ".$order_id;
			$result = $db->Execute($sql_query);
			if (strlen($debug_email) > 0)
				@mail($debug_email,"post 5 - NOCHEX",$sql_query);
			if (!$result)
			{
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post 6 - NOCHEX - ERROR",$sql_query);
				$db->Close();
				exit;
			}

			if (strlen($debug_email) > 0)
				@mail($debug_email,"post 7 - NOCHEX",$show_transaction["pay_invoice"]." is pay_invoice\n".$show_transaction["account_balance"]." is account_balance\n".
					$show_transaction["subscription_renewal"]." is subscription_renewal\n".$show_transaction["ad_placement"]." is ad placement\n\n");
			if ($show_transaction["pay_invoice"] > 0)
			{
				$sql_query = "update geodesic_invoices set date_paid = $transaction_date where invoice_id = ".$show_transaction["pay_invoice"];
				$paid_result = $db->Execute($sql_query);
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 8 - NOCHEX",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
				if (!$paid_result)
				{
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post - 9 - NOCHEX - ERROR",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
					$db->Close();
					exit;
				}
				else
				{
					//invoice payment successful
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post - 10 - NOCHEX",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
					exit;
				}
			}
			elseif ($show_transaction["account_balance"] > 0)
			{
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 11 - NOCHEX",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
				//update approval for this balance transaction
				$sql_query = "update geodesic_balance_transactions set
					approved = 1,
					cc_transaction_id = \"".$order_id."\"
					where transaction_id = ".$show_transaction["account_balance"];
				$update_balance_transaction_result = $db->Execute($sql_query);
				if (!$update_balance_transaction_result)
				{
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post - 12 account balance - B - NOCHEX",$show_transaction["account_balance"]." is account balance\n\n".$sql_query."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"),$debug_from,$debug_additional);
				}
				else
				{

				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 13 account balance - C - NOCHEX",$show_transaction["account_balance"]." is account balance\n\n".$sql_query."\n\n".date("l dS of F Y h:i:s A"),$debug_from,$debug_additional);
				//add to the users balance
				//user data still exists
				$sql_query = "select * from geodesic_userdata where id = ".$show_transaction["user_id"];
				$user_data_result = $db->Execute($sql_query);
				if (!$user_data_result)
				{
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post - 14 account balance - D - NOCHEX",$show_transaction["account_balance"]." is account balance\n\n".$sql_query."\n\n".date("l dS of F Y h:i:s A"),$debug_from,$debug_additional);
				}
				else
				{
					//add to the users balance
					//user data still exists
					$user_data = $user_data_result->FetchRow();
					$new_balance = $user_data["account_balance"] + $amount;
					$sql_query = "update geodesic_userdata set
						account_balance = ".$new_balance."
						where id = ".$show_transaction["user_id"];
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post - 15 - NOCHEX",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
					$update_balance_results = $db->Execute($sql_query);
					if (!$update_balance_results)
					{
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post - 16 - NOCHEX - ERROR",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
						return false;
					}
					else
					{
						//account balance transaction completed successfully
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post - 17 - NOCHEX - SUCCESSFUL",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
						exit;
					}
				}
			}
		}
		//STOREFRONT CODE
		elseif($show_transaction["subscription_renewal"] == 1 && $show_transaction["price_plan_id"] == -1)
		{
			$this->sql_query = "select * from geodesic_storefront_subscriptions_choices
				where period_id = ".$show_transaction["renewal_length"]."";
			$choices_result = $db->Execute($this->sql_query);
			if (!$choices_result)
			{
				$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
				if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
				$db->Close();
				exit;
			}
			elseif ($choices_result->RecordCount() == 1 )
			{
				$show_choice = $choices_result->FetchNextObject();
				if ($show_choice->VALUE !=0)
				{
					//check to see if currently subscribed
					$this->sql_query = "select * from geodesic_storefront_subscriptions where user_id = ".$show_transaction["user_id"];
					$check_subscriptions_results = $db->Execute($this->sql_query);
					if (!$check_subscriptions_results)
					{
						$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
						if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
						$db->Close();
						exit;
					}
					elseif ($check_subscriptions_results->RecordCount() > 0)
					{
						//extend subscription period
						$show_subscription = $check_subscriptions_results->FetchNextObject();
						if ($show_subscription->EXPIRATION > $this->shifted_time($db))
							$new_expire = ($show_subscription->EXPIRATION + ($show_choice->VALUE * 86400));
						else
							$new_expire = ($this->shifted_time($db) + ($show_choice->VALUE * 86400));
						$this->sql_query = "update geodesic_storefront_subscriptions
							set expiration = ".$new_expire."
							where subscription_id = ".$show_subscription->SUBSCRIPTION_ID;
						$update_subscriptions_results = $db->Execute($this->sql_query);
						if (!$update_subscriptions_results)
						{
							$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
							if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
							$db->Close();
							exit;
						}

					}
					else
					{
						//enter new subscription period
						$new_expire = ($this->shifted_time($db) + ($show_choice->VALUE * 86400));
						$this->sql_query = "insert into geodesic_storefront_subscriptions
							(user_id,expiration)
							values
							(".$show_transaction["user_id"].",".$new_expire.")";
						$insert_subscriptions_results = $db->Execute($this->sql_query);
						if (!$insert_subscriptions_results)
						{
							$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
							if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
							$db->Close();
							exit;
						}
					}
				}
			}
		}
		//STOREFRONT CODE
		elseif ($show_transaction["subscription_renewal"] == 1)
		{
			$sql_query = "select * from geodesic_user_groups_price_plans where id = ".$show_transaction["user_id"];
			//echo $sql_query." is the query <br>\n";
			if (strlen($debug_email) > 0)
				@mail($debug_email,"post 18 - NOCHEX",$sql_query);
			$price_plan_id_result = $db->Execute($sql_query);
			if ($price_plan_id_result->RecordCount() == 1)
			{
				$show_price_plan = $price_plan_id_result->FetchRow();
				$sql_query = "select * from geodesic_classifieds_subscription_choices where price_plan_id = ".$show_price_plan["price_plan_id"]."
					and period_id = ".$show_transaction["renewal_choice"]." order by value asc";
				$choices_result = $db->Execute($sql_query);
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post 19 - NOCHEX",$sql_query);
				if (!$choices_result)
				{
					$db->Close();
					exit;
				}
				elseif ($choices_result->RecordCount() == 1 )
				{
					$show_choice = $choices_result->FetchRow();
					if ($show_choice["value"] !=0)
					{
						//check to see if currently subscribed
						$sql_query = "select * from geodesic_classifieds_user_subscriptions where user_id = ".$show_transaction["user_id"];
						$check_subscriptions_results = $db->Execute($sql_query);
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post 20 - NOCHEX",$sql_query);
						if (!$check_subscriptions_results)
						{
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post 21 - NOCHEX - ERROR",$db->ErrorMsg());
							$db->Close();
							exit;
						}
						elseif ($check_subscriptions_results->RecordCount() > 0)
						{
							//extend subscription period
							$show_subscription = $check_subscriptions_results->FetchRow();
							if ($show_subscription["subscription_expire"] > $transaction_date)
								$new_expire = ($show_subscription["subscription_expire"] + ($show_choice["value"] * 86400));
							else
								$new_expire = ($transaction_date + ($show_choice["value"] * 86400));
							$sql_query = "update geodesic_classifieds_user_subscriptions
								set subscription_expire = ".$new_expire."
								where subscription_id = ".$show_subscription["subscription_id"];
							$update_subscriptions_results = $db->Execute($sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post 22 - NOCHEX",$sql_query);
							if (!$update_subscriptions_results)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post 23 - NOCHEX - ERROR",$db->ErrorMsg());
								$db->Close();
								exit;
							}
						}
						else
						{
							//enter new subscription period
							$new_expire = ($transaction_date + ($show_choice["value"] * 86400));
							$sql_query = "insert into geodesic_classifieds_user_subscriptions
								(user_id,subscription_expire)
								values
								(".$show_transaction["user_id"].",".$new_expire.")";
							$insert_subscriptions_results = $db->Execute($sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post 24 - NOCHEX",$sql_query);
							if (!$insert_subscriptions_results)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post 25 - NOCHEX",$sql_query);
								$db->Close();
								exit;
							}
						}
					}
				}
			}
		}
		elseif ($show_transaction["ad_placement"] == 1)
		{
			$sql_query = "select * from geodesic_classifieds where id = ".$show_transaction["classified_id"];
			$duration_result = $db->Execute($sql_query);
			if (strlen($debug_email) > 0)
				@mail($debug_email,"post 26 - NOCHEX",$sql_query);
			if (!$duration_result)
			{
				$db->Close();
				exit;
			}
			$show_duration = $duration_result->FetchRow();

			//when ad ends
			$length_of_ad = ($show_duration["duration"] * 86400);
			$current_time = $transaction_date;
			include_once("classes/site_class.php");
			$site = new Site($db,0,0,0);
			if  ($site->configuration_data["admin_approves_all_ads"])
			{
				$sql_query = "update geodesic_classifieds set
					date = ".$current_time.",
					ends = ".($current_time + $length_of_ad)."
					where id = ".$show_transaction["classified_id"];
			}
			else
			{
				$sql_query = "update geodesic_classifieds set
					live = 1,
					date = ".$current_time.",
					ends = ".($current_time + $length_of_ad)."
					where id = ".$show_transaction["classified_id"];

				// IDev Affiliate payment
				if($site->configuration_data["idevaffiliate"] && $site->configuration_data["idev_upgrade"])
				{
					$idev_geoce_1 = $payment_gross;
					$idev_geoce_2 = "ad-".$show_transaction["classified_id"];
					include($site->configuration_data["idev_path"].'sale.php');

					include("config.php");
					include("classes/adodb.inc.php");

					$db = &ADONewConnection('mysql');

					if (!$db->Connect($db_host, $db_username, $db_password, $database))
					{
						echo "Could not reconnect to database";
						exit;
					}
				}
			}
			$set_live_result = $db->Execute($sql_query);
			if (strlen($debug_email) > 0)
				@mail($debug_email,"post 27 - NOCHEX",$sql_query);
			if (!$set_live_result)
			{
				$db->Close();
				exit;
			}

			$site->update_category_count($db,$show_duration["category"]);

			$site->sell_success_email($db,$show_transaction["classified_id"]);

			//check to see if need to update subscription expirations
			$sql_query = "select * from geodesic_user_groups_price_plans
				where id = ".$show_duration["seller"];
			if (strlen($debug_email) > 0)
				@mail($debug_email,"post 28 - NOCHEX",$sql_query);
			$get_group_result = $db->Execute($sql_query);
			if (!$get_group_result)
			{
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post 29 - NOCHEX - wrong",$db->ErrorMsg()."\n".sql_query);
				$db->Close();
				exit;
			}
			elseif ($get_group_result->RecordCount() == 1)
			{
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post 30 - NOCHEX",$get_group_result->RecordCount()." is recordcount<BR>\n");

				$show_group = $get_group_result->FetchRow();
				$price_plan = $site->get_price_plan_from_group($db,$show_group["group_id"]);
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post 31 - NOCHEX",$price_plan["type_of_billing"]." is price plan type of billing\n".$show_group["group_id"]." is group id<Br>");
				if ($price_plan)
				{
					if ($price_plan["type_of_billing"] == 2)
					{
						$sql_query = "select * from geodesic_classifieds_user_subscriptions where subscription_expire > ".$transaction_date." and user_id = ".$show_duration["seller"];
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post 32 - NOCHEX",$sql_query);
						$get_subscriptions_results = $db->Execute($sql_query);
						if (!$get_subscriptions_results)
						{
							$db->Close();
							exit;
						}
						elseif ($get_subscriptions_results->RecordCount() == 0)
						{
							//push the subscription up
							$sql_query = "select * from geodesic_classifieds_subscription_choices where period_id = ".$show_duration["subscription_choice"];
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post 33 - NOCHEX",$sql_query);
							$choice_result = $db->Execute($sql_query);
							if (!$choice_result)
							{
								$db->Close();
								exit;
							}
							elseif ($choice_result->RecordCount() == 1 )
							{
								$show_subscription_choice = $choice_result->FetchRow();
								$expiration = (($show_subscription_choice["value"] * 86400) + $transaction_date);
								$sql_query = "insert into geodesic_classifieds_user_subscriptions
									(user_id,subscription_expire)
									values
									(".$show_duration["seller"].",".$expiration.")";
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post 34 - NOCHEX",$sql_query);
								$free_subscription_result = $db->Execute($sql_query);
								if (!$free_subscription_result)
								{
									$db->Close();
									exit;
								}
							}
						}
					}
					elseif ($price_plan["type_of_billing"] == 1)
					{
						//check to see if this was a credit
						$sql_query = "select * from geodesic_classifieds_user_credits
							where user_id = ".$show_duration["seller"]." order by credits_expire asc limit 1";
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post 35 - NOCHEX",$sql_query);
						$credits_results = $db->Execute($sql_query);
						if (!$credits_results)
						{
							$db->Close();
							exit;
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
									and user_id = ".$show_duration["seller"];
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post 36 - NOCHEX",$sql_query);
								$remove_credits_results = $db->Execute($sql_query);
								if (!$remove_credits_results)
								{
									$db->Close();
									exit;
								}
							}
							else
							{
								//remove one from the credit count
								$sql_query = "update geodesic_classifieds_user_credits set
									credit_count = ".($show_credits["credit_count"] - 1)."
									where credits_id = ".$show_credits["credits_id"]."
									and user_id = ".$show_duration["seller"];
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post 37 - NOCHEX",$sql_query);
								$remove_credit = $db->Execute($sql_query);
								if (!$remove_credit)
								{
									$db->Close();
									exit;
								}
							}
						}
					}
				}
				else
				{
					$db->Close();
					exit;
				}
			}
			else
			{
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post 38 - NOCHEX","wrong group count result");
			}
		}
		else
		{
			//do the renewal thing
			include_once("classes/site_class.php");
			$site = new Site($db,0,0,0);
			$sql_query = "select * from geodesic_classifieds where id = ".$show_transaction["classified_id"];
			$classified_result = $db->Execute($sql_query);
			if (strlen($debug_email) > 0)
				@mail($debug_email,"post 39 - NOCHEX",$sql_query);
			if (!$classified_result)
			{
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post 40 - NOCHEX - ERROR",$db-ErrorMsg());
				$db->Close();
				exit;
			}
			$show_classified = $classified_result->FetchRow();
			if (strlen($debug_email) > 0)
				@mail($debug_email,"post 41 - NOCHEX",$show_transaction["renew"]." is renew\n\n".$show_transaction["renewal_length"]." is renewal_length\n\n".$show_classified["ends"]." is ends and time is ".$transaction_date);
			if ($show_transaction["renewal_length"] > 0)
			{
				if ($show_classified["ends"] > $transaction_date)
					$new_expire = ($show_classified["ends"] + ($show_transaction["renewal_length"] * 86400));
				else
					$new_expire = ($transaction_date + ($show_transaction["renewal_length"] * 86400));
			}
			else
			{
				$length_of_ad = ($show_classified["duration"] * 86400);
				$new_expire = ($transaction_date + $length_of_ad);
			}
			if (($show_transaction["renew"] == 1) && ($show_transaction["renewal_length"] > 0))
			{
				if  ($site->configuration_data["admin_approves_all_ads"])
				{
						$sql_query = "update geodesic_classifieds set
							ends = \"$new_expire\",
							renewal_payment_expected = 0,
							date = \"$transaction_date\",
							better_placement = \"0\",
							featured_ad = \"0\",
							bolding = \"0\",
							attention_getter = \"0\",
							featured_ad_2 = \"0\",
							featured_ad_3 = \"0\",
							featured_ad_4 = \"0\",
							featured_ad_5 = \"0\",
							expiration_notice = 0
							where id = ".$show_transaction["classified_id"];
					}
					else
					{
						$sql_query = "update geodesic_classifieds set
							ends = \"$new_expire\",
							live = 1,
							renewal_payment_expected = 0,
							date = \"$transaction_date\",
							better_placement = \"0\",
							featured_ad = \"0\",
							bolding = \"0\",
							attention_getter = \"0\",
							featured_ad_2 = \"0\",
							featured_ad_3 = \"0\",
							featured_ad_4 = \"0\",
							featured_ad_5 = \"0\",
							expiration_notice = 0
							where id = ".$show_transaction["classified_id"];
					}
					$renew_result = $db->Execute($sql_query);
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post 42 - NOCHEX",$sql_query);
					if (!$renew_result)
					{
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post 43 - NOCHEX - ERROR",$db->ErrorMsg());
						$db->Close();
						exit;
					}
					$site->update_category_count($db,$show_classified["category"]);

					if ($show_transaction["use_credit_for_renewal"])
					{
						$sql_query = "select * from geodesic_classifieds_user_credits where user_id = ".$show_transaction["user_id"]." order by credits_expire asc limit 1";
						$credits_results = $db->Execute($sql_query);
						if (!$credits_results)
						{
							$db->Close();
							exit;
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
									and user_id = ".$show_transaction["user_id"];
								$remove_credits_results = $db->Execute($sql_query);
								if (!$remove_credits_results)
								{
									$db->Close();
									exit;
								}
							}
							else
							{
								//remove one from the credit count
								$sql_query = "update geodesic_classifieds_user_credits set
									credit_count = ".($show_credits["credit_count"] - 1)."
									where credits_id = ".$show_credits["credits_id"]."
									and user_id = ".$show_transaction["user_id"];
								$remove_credit = $db->Execute($sql_query);

								if (!$remove_credit)
								{
									$db->Close();
									exit;
								}
							}
						}
					}
					else
					{
						// If not using credit then send to IDev
						// IDev Affiliate payment
						if($site->configuration_data["idevaffiliate"] && $site->configuration_data["idev_upgrade"])
						{
							$idev_geoce_1 = $payment_gross;
							$idev_geoce_2 = "ad-renewal-".$show_transaction["classified_id"];
							include($site->configuration_data["idev_path"].'sale.php');

							include("config.php");
							include("classes/adodb.inc.php");

							$db = &ADONewConnection('mysql');

							if (!$db->Connect($db_host, $db_username, $db_password, $database))
							{
								echo "Could not reconnect to database";
								exit;
							}
						}
					}
				}
				else
				{
					// IDev Affiliate payment
					if($site->configuration_data["idevaffiliate"] && $site->configuration_data["idev_upgrade"])
					{
						$idev_geoce_1 = $payment_gross;
						$idev_geoce_2 = "ad-upgrade-".$show_transaction["classified_id"];
						include($site->configuration_data["idev_path"].'sale.php');

						include("config.php");
						include("classes/adodb.inc.php");

						$db = &ADONewConnection('mysql');

						if (!$db->Connect($db_host, $db_username, $db_password, $database))
						{
							echo "Could not reconnect to database";
							exit;
						}
					}
				}

				if ($show_transaction["bolding"] == 1)
				{
					$sql_query = "update geodesic_classifieds set
						renewal_payment_expected = 0,
						ends = ".$new_expire.",
						bolding = 1
						where id = ".$show_transaction["classified_id"];
					$renew_result = $db->Execute($sql_query);
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post 44 - NOCHEX",$sql_query);
					if (!$renew_result)
					{
						$db->Close();
						exit;
					}
				}

				if ($show_transaction["better_placement"] == 1)
				{
					$sql_query = "update geodesic_classifieds set
						renewal_payment_expected = 0,
						ends = ".$new_expire.",
						better_placement = 1
						where id = ".$show_transaction["classified_id"];
					$renew_result = $db->Execute($sql_query);
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post 45 - NOCHEX",$sql_query);
					if (!$renew_result)
					{
						$db->Close();
						exit;
					}
				}

				if ($show_transaction["featured_ad"] == 1)
				{
					$sql_query = "update geodesic_classifieds set
						renewal_payment_expected = 0,
						ends = ".$new_expire.",
						featured_ad = 1
						where id = ".$show_transaction["classified_id"];
					$renew_result = $db->Execute($sql_query);
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post 46 - NOCHEX",$sql_query);
					if (!$renew_result)
					{
						$db->Close();
						exit;
					}
				}


				if ($show_transaction["featured_ad_2"] == 1)
				{
					$sql_query = "update geodesic_classifieds set
						renewal_payment_expected = 0,
						ends = ".$new_expire.",
						featured_ad_2 = 1
						where id = ".$show_transaction["classified_id"];
					$renew_result = $db->Execute($sql_query);
					if (!$renew_result)
					{
						$db->Close();
						exit;
					}
				}


				if ($show_transaction["featured_ad_3"] == 1)
				{
					$sql_query = "update geodesic_classifieds set
						renewal_payment_expected = 0,
						ends = ".$new_expire.",
						featured_ad_3 = 1
						where id = ".$show_transaction["classified_id"];
					$renew_result = $db->Execute($sql_query);
					if (!$renew_result)
					{
						$db->Close();
						exit;
					}
				}


				if ($show_transaction["featured_ad_4"] == 1)
				{
					$sql_query = "update geodesic_classifieds set
						renewal_payment_expected = 0,
						ends = ".$new_expire.",
						featured_ad_4 = 1
						where id = ".$show_transaction["classified_id"];
					$renew_result = $db->Execute($sql_query);
					if (!$renew_result)
					{
						$db->Close();
						exit;
					}
				}


				if ($show_transaction["featured_ad_5"] == 1)
				{
					$sql_query = "update geodesic_classifieds set
						renewal_payment_expected = 0,
						ends = ".$new_expire.",
						featured_ad_5 = 1
						where id = ".$show_transaction["classified_id"];
					$renew_result = $db->Execute($sql_query);
					if (!$renew_result)
					{
						$db->Close();
						exit;
					}
				}


				if ($show_transaction["attention_getter"] == 1)
				{
					$sql_query = "select * from geodesic_choices where choice_id = ".$show_transaction["attention_getter_choice"];
					$attention_getter_result = $db->Execute($sql_query);
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post 17 - NOCHEX",$sql_query);
					if (!$attention_getter_result)
					{
						$db->Close();
						exit;
					}
					elseif ($attention_getter_result->RecordCount() == 1)
					{
						$show_attention_getter = $attention_getter_result->FetchRow();
						$attention_getter_url = $show_attention_getter["value"];

						$sql_query = "update geodesic_classifieds set
							renewal_payment_expected = 0,
						.	ends = ".$new_expire.",
							attention_getter = 1,
							attention_getter_url = \"".$attention_getter_url."\"
							where id = ".$show_transaction["classified_id"];
						$renew_result = $db->Execute($sql_query);
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post 18 - NOCHEX",$sql_query);
						if (!$renew_result)
						{
							$db->Close();
							exit;
						}
					}
					else
					{
						$db->Close();
						exit;
					}
				}
				//mail($debug_email,"6",$req);
			}
		}
		else
		{
			//echo $sql_query."<Br>\n";
			$db->Close();
			exit;
		}
	}
}
elseif ($response=="DECLINED")
{
	$subject = "NOCHEX ".$response;
	$msg = "NOCHEX RESPONSE: {$response} ";
	$msg.= "transaction id: {$transaction_id} ";
	$msg.= "transaction date: {$transaction_date} ";
	$msg.= "from email: {$from_email} ";
	$msg.= "to email: {$to_email} ";
	$msg.= "order id: {$order_id} ";
	$msg.= "amount: {$amount} ";
	$msg.= "security key: {$security_key} ";
	if (strlen($debug_email) > 0)
		@mail ($admin_email, $subject, $msg);
}
else
{
	$subject = "NOCHEX VALIDITY RESPONSE: INVALID RESPONSE";
	$msg = "RESPONSE FROM NOCHEX WAS NEITHER AUTHORISED OR DECLINED? Looks like a NOCHEX problem. Probably best to ask NOCHEX Support ";
	$msg.= "Response was \"{$response}\" ";
	$msg.= print_r ($_POST, true);
	if (strlen($debug_email) > 0)
		@mail ($debug_email, $subject, $msg);
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
exit;
?>