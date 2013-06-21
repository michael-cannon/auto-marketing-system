<? //cc_process_authorizenet.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

error_reporting  (E_ERROR | E_WARNING | E_PARSE);
//x_response_code
//x_response_subcode
//x_response_reason_code
//x_response_reason_text
//x_auth_code
//x_avs_code
//x_trans_id
//x_invoice_num - classified_id
//x_amount
//x_method
//x_description
//x_type
//x_cust_id - user_id
//x_first_name
//x_last_name
//x_company
//x_address
//x_city
//x_state
//x_zip
//x_country
//x_phone
//x_fax
//x_email
//x_ship_to_first_name
//x_ship_to_last_name
//x_ship_to_company
//x_ship_to_address
//x_ship_to_city
//x_ship_to_state
//x_ship_to_zip
//x_ship_to_country
//x_tax
//x_duty
//x_freight
//x_tax_exempt
//x_po_num - authorizenet_transaction_id
//x_md5_hash

include("config.php");
include("classes/adodb.inc.php");
include("classes/site_class.php");

$debug_email = "";

if ($_POST)
{
	foreach ($_POST as $key => $value)
	{
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}
}

if (strlen($debug_email) > 0)
	@mail($debug_email,"process - 1 - authorize.net",date("l dS of F Y h:i:s A")."\n\n".$req);

if (($_REQUEST["x_invoice_num"]) && ($_REQUEST["x_po_num"]) && ($_REQUEST["x_cust_id"]) && ($_REQUEST["x_email"]))
{
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
	if (strlen($debug_email) > 0)
		@mail($debug_email,"process - 2 - authorize.net",date("l dS of F Y h:i:s A"));

	$site = new Site($db,2,$language_id,$_REQUEST["x_cust_id"]);

	$sql_query = "select * from geodesic_credit_card_choices where cc_id = 1";
	$cc_result = $db->Execute($sql_query);
	if (!$cc_result)
	{
		//echo $sql_query." is the query <br>\n";
		$db->Close();
		exit;
	}
	elseif ($cc_result->RecordCount() == 1)
	{
		$cc = $cc_result->FetchRow();
		//check to see if transaction exists
		$sql_query = "select * from ".$cc["cc_transaction_table"]."
			where classified_id = \"".$_REQUEST["x_invoice_num"]."\" and authorizenet_transaction_id = \"".$_REQUEST["x_po_num"]."\" and user_id = ".$_REQUEST["x_cust_id"]." and email = \"".$_REQUEST["x_email"]."\"";
		$trans_result = $db->Execute($sql_query);
		if (!$trans_result)
		{
			//echo $sql_query." is the query <br>\n";
			$db->Close();
			exit;
		}
		elseif ($trans_result->RecordCount() == 1)
		{
			$show_cc_trans = $trans_result->FetchRow();
			if (strlen($debug_email) > 0)
				@mail($debug_email,"process - 3 - authorize.net",$sql_query."\n\n".date("l dS of F Y h:i:s A"));

			if ($show_cc_trans["response_code"] == 0)
			{
				//this transaction has not taken place
				//update authorizenet table
				$sql_query = "update ".$cc["cc_transaction_table"]." set
					response_code = \"".$_REQUEST["x_response_code"]."\",
					response_subcode = \"".$_REQUEST["x_response_subcode"]."\",
					response_reason_code = \"".$_REQUEST["x_response_reason_code"]."\",
					response_reason_text = \"".$_REQUEST["x_response_reason_text"]."\",
					auth_code = \"".$_REQUEST["x_auth_code"]."\",
					avs_code = \"".$_REQUEST["x_avs_code"]."\",
					trans_id = \"".$_REQUEST["x_trans_id"]."\",
					md5_hash = \"".$_REQUEST["x_MD5_Hash"]."\"
					where classified_id = ".$_REQUEST["x_invoice_num"]." and authorizenet_transaction_id = ".$_REQUEST["x_po_num"]." and user_id = ".$_REQUEST["x_cust_id"]." and email = \"".$_REQUEST["x_email"]."\"";
				$result = $db->Execute($sql_query);
				if (!$result)
				{
					if (strlen($debug_email) > 0)
						@mail($debug_email,"process - 3a - authorize.net",$sql_query."\n\n".date("l dS of F Y h:i:s A"));
					$db->Close();
					exit;
				}
				if (strlen($debug_email) > 0)
					@mail($debug_email,"process - 4 - authorize.net",$_REQUEST["x_response_code"]."is response code\n\n".$sql_query."\n\n".date("l dS of F Y h:i:s A"));
				if ($_REQUEST["x_response_code"] == 1)
				{
					if ($show_cc_trans["pay_invoice"] > 0)
					{
						$site->sql_query = "update ".$site->invoices_table." set date_paid = ".$this->shifted_time($db)." where invoice_id = ".$show_cc_trans["pay_invoice"];
						$paid_result = $db->Execute($site->sql_query);
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post - 4a - authorize.net - sim",$site->sql_query."\n\n".date("l dS of F Y h:i:s A"));
						if (!$paid_result)
						{
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 4b - authorize.net - sim - error",$site->sql_query."\n\n".date("l dS of F Y h:i:s A"));
							$db->Close();
							exit;
						}
						else
						{
							$site->page_id = 180;
							$site->get_text($db);
							$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
							$site->body .= "<tr class=page_title><td>".urldecode($site->messages[3142])."</td>\n</tr>\n";
							$site->body .= "<tr class=page_description><td>".urldecode($site->messages[3143])."</td>\n</tr>\n";
							$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[3167])."</td>\n</tr>\n";
							$site->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$site->configuration_data["classifieds_url"]."?a=4&b=3\" class=my_account_link >";
							$site->body .=urldecode($site->messages[2536])."</A>\n\t</td>\n</tr>\n";
							$site->body .= "</table>\n";
							$site->display_page($db);
							$db->Close();
							exit;
						}
					}
					elseif ($show_cc_trans["account_balance"] > 0)
					{
						//update approval for this balance transaction
						$site->update_balance_approval($db,$show_cc_trans["account_balance"],$_REQUEST["x_po_num"]);						
						
						//add to the users balance
						//get the users current balance
						$sql_query = "select * from  ".$site->userdata_table." where id = ".$_REQUEST["x_cust_id"];
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post - 5 - authorize.net - sim",$site->sql_query."\n\n".date("l dS of F Y h:i:s A"));						
						$userdata_results = $db->Execute($sql_query);
						if (!$userdata_results)
						{
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 5a - authorize.net - sim - error",$site->sql_query."\n\n".date("l dS of F Y h:i:s A"));
							$db->Close();							
						}
						elseif ($userdata_results->RecordCount() == 1)
						{
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 6 - authorize.net - sim",$site->sql_query."\n\n".date("l dS of F Y h:i:s A"));							
							$user_data = $userdata_results->FetchRow();
							$new_balance = $user_data["account_balance"] + $show_cc_trans["amount"];
							$sql_query = "update ".$site->userdata_table." set
								account_balance = ".$new_balance." 
								where id = ".$_REQUEST["x_cust_id"];
							$update_balance_results = $db->Execute($sql_query);
							if (!$update_balance_results)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 6 - authorize.net - sim - error",$site->sql_query."\n\n".date("l dS of F Y h:i:s A"));									
								return false;	
							}
							else
							{
								$site->page_id = 175;
								$site->get_text($db);
								$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
								$site->body .= "<tr class=page_title><td>".urldecode($site->messages[2531])."</td>\n</tr>\n";
								$site->body .= "<tr class=page_description><td>".urldecode($site->messages[2532])."</td>\n</tr>\n";
								$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[2537])." - ".
									$site->configuration_data["precurrency"]." ".sprintf("%01.2f",$new_balance).
									" ".$site->configuration_data["postcurrency"]."</td>\n</tr>\n";
								$site->body .="<tr class=my_account_link>\n\t<td><a href=\"".$this->configuration_data["classifieds_file_name"]."?a=4&b=3\" class=my_account_link >";
								$site->body .=urldecode($site->messages[2536])."</a>\n\t</td>\n</tr>\n";
								$site->body .= "</table>\n";
								$site->remove_account_session($db,$_COOKIE["classified_session"]);
								$site->display_page($db);
								$db->Close();				
								exit;					
							}											
						}
						else
						{
							
						}
					}
					elseif ($show_cc_trans["subscription_renewal"] == 1)
					{
						$sql_query = "select * from ".$site->user_groups_price_plans_table." where id = ".$_REQUEST["x_cust_id"];
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post - 7 - authorize.net - sim",$site->sql_query."\n\n".date("l dS of F Y h:i:s A"));	
						$price_plan_id_result = $db->Execute($sql_query);
						if ($price_plan_id_result->RecordCount() == 1)
						{
							$show_price_plan = $price_plan_id_result->FetchRow();
							$sql_query = "select * from ".$site->subscription_choices." where price_plan_id = ".$show_price_plan["price_plan_id"]."
								and period_id = ".$show_cc_trans["renewal_length"]." order by value asc";
							$choices_result = $db->Execute($sql_query);
							if (!$choices_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 7 - authorize.net - sim - error",$site->sql_query."\n\n".date("l dS of F Y h:i:s A"));									
								$db->Close();
								exit;
							}
							elseif ($choices_result->RecordCount() == 1 )
							{
								$show_choice = $choices_result->FetchRow();
								if ($show_choice["value"] !=0)
								{
									//check to see if currently subscribed
									
									$sql_query = "select * from ".$site->user_subscriptions_table." where user_id = ".$_REQUEST["x_cust_id"];
									$check_subscriptions_results = $db->Execute($sql_query);
									if (strlen($debug_email) > 0)
										@mail($debug_email,"post - 8 - authorize.net - sim",$site->sql_query."\n\n".date("l dS of F Y h:i:s A"));									
									
									if (!$check_subscriptions_results)
									{
										if (strlen($debug_email) > 0)
											@mail($debug_email,"post - 8 - authorize.net - sim",$site->sql_query."\n\n".date("l dS of F Y h:i:s A"));																			
										$db->Close();
										exit;
									}
									elseif ($check_subscriptions_results->RecordCount() > 0)
									{
										//extend subscription period
										$show_subscription = $check_subscriptions_results->FetchRow();
										if ($show_subscription["subscription_expire"] > $this->shifted_time($db))
											$new_expire = ($show_subscription["subscription_expire"] + ($show_choice["value"] * 86400));
										else
											$new_expire = ($this->shifted_time($db) + ($show_choice["value"] * 86400));
										$sql_query = "update ".$site->user_subscriptions_table."
											set subscription_expire = ".$new_expire."
											where subscription_id = ".$show_subscription["subscription_id"];
										if (strlen($debug_email) > 0)
											@mail($debug_email,"post - 8 - authorize.net - sim",$site->sql_query."\n\n".date("l dS of F Y h:i:s A"));									
										
										$update_subscriptions_results = $db->Execute($sql_query);
										if (!$update_subscriptions_results)
										{
											if (strlen($debug_email) > 0)
												@mail($debug_email,"post - 8 - authorize.net - sim",$site->sql_query."\n\n".date("l dS of F Y h:i:s A"));									
											$db->Close();
											exit;
										}
									}
									else
									{
										//enter new subscription period
										$new_expire = ($this->shifted_time($db) + ($show_choice["value"] * 86400));
										$sql_query = "insert into ".$site->user_subscriptions_table."
											(user_id,subscription_expire)
											values
											(".$_REQUEST["x_cust_id"].",".$new_expire.")";
										$insert_subscriptions_results = $db->Execute($sql_query);
										if (!$insert_subscriptions_results)
										{
											$db->Close();
											exit;
										}
									}
									$site->page_id = 109;
									$site->get_text($db);
									$site->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
									$site->body .="<tr class=page_title>\n\t<td>".urldecode($site->messages[1669])."</td>\n</tr>\n";
									$site->body .="<tr class=page_description>\n\t<td>".urldecode($site->messages[1670])."</td>\n</tr>\n";
									$site->body .="<tr class=page_description>\n\t<td>".urldecode($site->messages[1679])."</td>\n</tr>\n";
									$site->body .="<tr class=view_ad_link >\n\t<td><A HREF=\"".trim($site->configuration_data["classifieds_url"])."?a=4&b=3\" class=view_ad_link >";
									$site->body .=urldecode($site->messages[1672])."</A>\n\t</td>\n</tr>\n";
									$site->body .="</table>\n";
									$site->display_page($db);
								}
							}
						}
					}
					elseif ($show_cc_trans["ad_placement"] == 1)
					{
						include("classes/classified_sell_class.php");
						$sell = new Classified_sell($db,$_REQUEST["x_cust_id"],$language_id,$HTTP_COOKIE_VARS["classified_session"]);
						$sell->page_id = 14;
						$sell->get_text($db);
						//approved
						//turn on classified ad
						//send to success page
						//send a success message
						$sell->sql_query = "select * from ".$site->classifieds_table." where id = ".$_REQUEST["x_invoice_num"];
						$duration_result = $db->Execute($sell->sql_query);
						if (strlen($debug_email) > 0)
							@mail($debug_email,"process - 9 - authorize.net",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));

						if (!$duration_result)
						{
							if (strlen($debug_email) > 0)
								@mail($debug_email,"process - 9a - authorize.net - error",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));
							$db->Close();
							exit;
						}
						$show_duration = $duration_result->FetchRow();
						$length_of_ad = ($show_duration["duration"] * 86400);
						//this is a new ad
						//when ad ends
						$current_time = $this->shifted_time($db);
						if  ($sell->configuration_data["admin_approves_all_ads"])
						{
							$sell->sql_query = "update ".$sell->classifieds_table." set
								date = ".$current_time.",
								ends = ".($current_time + $length_of_ad)."
								where id = ".$_REQUEST["x_invoice_num"];
						}
						else
						{
							$sell->sql_query = "update ".$sell->classifieds_table." set
								live = 1,
								date = ".$current_time.",
								ends = ".($current_time + $length_of_ad)."
								where id = ".$_REQUEST["x_invoice_num"];
							
							// IDev Affiliate payment
							if($sell->configuration_data["idevaffiliate"])
							{
								$idev_geoce_1 = $_REQUEST["x_amount"];
								$idev_geoce_2 = "ad-".$show_duration["id"];
								include($sell->configuration_data["idev_path"].'sale.php');
								
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
						if (strlen($debug_email) > 0)
							@mail($debug_email,"process - 10 - authorize.net",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));

						$result = $db->Execute($sell->sql_query);
						if (!$result)
						{
							if (strlen($debug_email) > 0)
								@mail($debug_email,"process - 10 - authorize.net - error",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));
							$db->Close();
							exit;
						}

						$sell->update_category_count($db,$show_duration["category"]);
						$sell->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
						$sell->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($sell->messages[1365])."</td>\n</tr>\n";
						$sell->body .="<tr class=page_title>\n\t<td>".urldecode($sell->messages[177])."</td>\n</tr>\n";
						$sell->body .="<tr class=page_description>\n\t<td>".urldecode($sell->messages[180])."</td>\n</tr>\n";
						$sell->body .="<tr class=page_description>\n\t<td>".urldecode($sell->messages[653])."</td>\n</tr>\n";
						$sell->body .="<tr class=view_ad_link >\n\t<td><A HREF=\"".trim($sell->configuration_data["classifieds_url"])."?a=2&b=".$_REQUEST["x_invoice_num"]."\" class=view_ad_link >";
						$sell->body .=urldecode($this->messages[181])."</A>\n\t</td>\n</tr>\n";
						$sell->body .="</table>\n";
						$sell->check_user_subscription($db);
						$sell->check_subscriptions_and_credits($db);
						$sell->remove_sell_session($db,$_COOKIE["classified_session"],$_REQUEST["x_invoice_num"]);
						if (strlen($debug_email) > 0)
							@mail($debug_email,"process - 11 - authorize.net",$_REQUEST["x_invoice_num"]."is id\n\n".date("l dS of F Y h:i:s A"));
						$sell->sell_success_email($db,$_REQUEST["x_invoice_num"]);
						$sell->page_id = 14;
						$sell->display_page($db);
						if (strlen($debug_email) > 0)
							@mail($debug_email,"process - 12 - authorize.net",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));

						$db->Close();
						exit;
						//echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$this->configuration_data["classifieds_url"]."?a=1&credit_approval=1\">";
					}
					else
					{
						$site->page_id = 58;
						$site->get_text($db);
						if (strlen($debug_email) > 0)
							@mail($debug_email,"process - 12 - authorize.net",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));						

						$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
						$site->body .= "<tr class=page_title><td>".urldecode($site->messages[850])."</td>\n</tr>\n";
						$site->body .= "<tr class=page_description><td>".urldecode($site->messages[851])."</td>\n</tr>\n";

						$sql_query = "select * from ".$site->classifieds_table." where id = ".$_REQUEST["x_invoice_num"];
						$classified_result = $db->Execute($sql_query);
						if (strlen($debug_email) > 0)
							@mail($debug_email,"process - 13 - authorize.net",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));						
						if (!$classified_result)
						{
							if (strlen($debug_email) > 0)
								@mail($debug_email,"process - 13 - authorize.net - error",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));
							$db->Close();
							exit;
						}
						$show_classified = $classified_result->FetchRow();

						if (($show_cc_trans["renew"] == 1) && ($show_cc_trans["renewal_length"] > 0))
						{
							if ($show_classified["ends"] > $this->shifted_time($db))
								$new_expire = ($show_classified["ends"] + ($show_cc_trans["renewal_length"] * 86400));
							else
								$new_expire = ($this->shifted_time($db) + ($show_cc_trans["renewal_length"] * 86400));
							$site->sql_query = "update geodesic_classifieds set
								ends = \"".$new_expire."\",
								date = \"".$this->shifted_time($db)."\",
								expiration_notice = 0,
								better_placement = \"0\",
								featured_ad = \"0\",
								bolding = \"0\",
								attention_getter = \"0\",
								featured_ad_2 = \"0\",
								featured_ad_3 = \"0\",
								featured_ad_4 = \"0\",
								featured_ad_5 = \"0\",
								where id = ".$_REQUEST["x_invoice_num"];
							if (strlen($debug_email) > 0)
								@mail($debug_email,"process - 14 - authorize.net",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));
							$renew_result = $db->Execute($site->sql_query);
							if (!$renew_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"process - 14a - authorize.net - error",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));								
								$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
								$site->body .= "</table>\n";
								$site->display_page($db);
								$db->Close();
								exit;
							}
							$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[852])."</td>\n</tr>\n";

							if ($show_cc_trans["use_credit_for_renewal"])
							{
								$site->sql_query = "select * from geodesic_classifieds_user_credits where user_id = ".$show_cc_trans["user_id"]." order by credits_expire asc limit 1";
								$credits_results = $db->Execute($site->sql_query);
								if (strlen($debug_email) > 0)
									@mail($debug_email,"process - 15 - authorize.net",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));
								if (!$credits_results)
								{
									if (strlen($debug_email) > 0)
										@mail($debug_email,"process - 15a - authorize.net",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));									
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
										$site->sql_query = "delete from geodesic_classifieds_user_credits where
											credits_id = ".$show_credits["credits_id"]."
											and user_id = ".$show_cc_trans["user_id"];
										$remove_credits_results = $db->Execute($site->sql_query);
										if (strlen($debug_email) > 0)
											@mail($debug_email,"process - 16 - authorize.net",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));
										if (!$remove_credits_results)
										{
											if (strlen($debug_email) > 0)
												@mail($debug_email,"process - 16a - authorize.net - error",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));											
											$db->Close();
											exit;
										}
									}
									else
									{
										//remove one from the credit count
										$site->sql_query = "update geodesic_classifieds_user_credits set
											credit_count = ".($show_credits["credit_count"] - 1)."
											where credits_id = ".$show_credits["credits_id"]."
											and user_id = ".$show_cc_trans["user_id"];
										$remove_credit = $db->Execute($site->sql_query);
										if (strlen($debug_email) > 0)
											@mail($debug_email,"process - 17 - authorize.net",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));
										if (!$remove_credit)
										{
											if (strlen($debug_email) > 0)
												@mail($debug_email,"process - 17a - authorize.net",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));											
											$db->Close();
											exit;
										}
									}
								}
							}
							else
							{
								// If not using credits to pay for renewal then send it to IDev
								// IDev Affiliate payment
								if($sell->configuration_data["idevaffiliate"] && $sell->configuration_data["idev_renewal"])
								{
									$idev_geoce_1 = $_REQUEST["x_amount"];
									$idev_geoce_2 = "ad-renew-".$show_duration["id"];
									include($sell->configuration_data["idev_path"].'sale.php');
									
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
							// Upgrade auction
							// IDev Affiliate payment
							if($sell->configuration_data["idevaffiliate"] && $sell->configuration_data["idev_upgrade"])
							{
								$idev_geoce_1 = $_REQUEST["x_amount"];
								$idev_geoce_2 = "ad-upgrade-".$show_duration["id"];
								include($sell->configuration_data["idev_path"].'sale.php');
								
								include("config.php");
								include("classes/adodb.inc.php");
								
								$db = &ADONewConnection('mysql');
								
								if (!$db->Connect($db_host, $db_username, $db_password, $database))
								{
									echo "Could not reconnect to database";
									exit;
								}									
							}
							
							// Change end time
							if($sell->configuration_data["upgrade_time"])
							{
								$end_time = ($show_classified["ends"] - $show_classified["date"]) + $this->shifted_time($db);
								$site->sql_query = "update geodesic_classifieds set
										ends = ".$end_time." 
										where id = ".$_REQUEST["x_invoice_num"];
								$end_result = $db->Execute($site->sql_query);
							}
						}

						if ($show_cc_trans["bolding"] == 1)
						{
							$site->sql_query = "update geodesic_classifieds set
								bolding = \"1\"
								where id = ".$_REQUEST["x_invoice_num"];
							$bolding_result = $db->Execute($site->sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"process - 18 - authorize.net",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));							
							if (!$bolding_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"process - 18a - authorize.net - error",$sell->sql_query."\n\n".date("l dS of F Y h:i:s A"));								
								$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
								$site->body .= "</table>\n";
								$site->display_page($db);
								$db->Close();
								exit;
							}
							$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[853])."</td>\n</tr>\n";
						}

						if ($show_cc_trans["better_placement"] == 1)
						{
							$site->sql_query = "update geodesic_classifieds set
								better_placement = \"1\"
								where id = ".$_REQUEST["x_invoice_num"];
							$better_placement_result = $db->Execute($site->sql_query);
							if (!$better_placement_result)
							{
								$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
								$site->body .= "</table>\n";
								$site->display_page($db);
								$db->Close();
								exit;
							}
							$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[854])."</td>\n</tr>\n";
						}

						if ($show_cc_trans["featured_ad"] == 1)
						{
							$site->sql_query = "update geodesic_classifieds set
								featured_ad = \"1\"
								where id = ".$_REQUEST["x_invoice_num"];
							$featured_ad_result = $db->Execute($site->sql_query);
							if (!$featured_ad_result)
							{
								$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
								$site->body .= "</table>\n";
								$site->display_page($db);
								$db->Close();
								exit;
							}
							$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[855])."</td>\n</tr>\n";
						}

						if ($show_cc_trans["featured_ad_2"] == 1)
						{
							$site->sql_query = "update geodesic_classifieds set
								featured_ad_2 = \"1\"
								where id = ".$_REQUEST["x_invoice_num"];
							$featured_ad_result = $db->Execute($site->sql_query);
							if (!$featured_ad_result)
							{
								$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
								$site->body .= "</table>\n";
								$site->display_page($db);
								$db->Close();
								exit;
							}
							$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[855])."</td>\n</tr>\n";
						}

						if ($show_cc_trans["featured_ad_3"] == 1)
						{
							$site->sql_query = "update geodesic_classifieds set
								featured_ad_3 = \"1\"
								where id = ".$_REQUEST["x_invoice_num"];
							$featured_ad_result = $db->Execute($site->sql_query);
							if (!$featured_ad_result)
							{
								$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
								$site->body .= "</table>\n";
								$site->display_page($db);
								$db->Close();
								exit;
							}
							$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[855])."</td>\n</tr>\n";
						}

						if ($show_cc_trans["featured_ad_4"] == 1)
						{
							$site->sql_query = "update geodesic_classifieds set
								featured_ad_4 = \"1\"
								where id = ".$_REQUEST["x_invoice_num"];
							$featured_ad_result = $db->Execute($site->sql_query);
							if (!$featured_ad_result)
							{
								$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
								$site->body .= "</table>\n";
								$site->display_page($db);
								$db->Close();
								exit;
							}
							$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[855])."</td>\n</tr>\n";
						}

						if ($show_cc_trans["featured_ad_5"] == 1)
						{
							$site->sql_query = "update geodesic_classifieds set
								featured_ad_5 = \"1\"
								where id = ".$_REQUEST["x_invoice_num"];
							$featured_ad_result = $db->Execute($site->sql_query);
							if (!$featured_ad_result)
							{
								$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
								$site->body .= "</table>\n";
								$site->display_page($db);
								$db->Close();
								exit;
							}
							$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[855])."</td>\n</tr>\n";
						}

						if ($show_cc_trans["attention_getter"] == 1)
						{
							$site->sql_query = "select * from geodesic_classifieds_choices where choice_id = ".$show_cc_trans["attention_getter_choice"];
							$attention_getter_result = $db->Execute($site->sql_query);
							if (!$attention_getter_result)
							{
								$site->body .=$this->sql_query."<br>\n";
								$db->Close();
								exit;
							}
							elseif ($attention_getter_result->RecordCount() == 1)
							{
								$show_attention_getter = $attention_getter_result->FetchRow();
								$attention_getter_url = $show_attention_getter["value"];
							}
							else
							{
								//
							}

							$site->sql_query = "update geodesic_classifieds set
								attention_getter = \"1\",
								attention_getter_url = \"".$attention_getter_url."\"
								where id = ".$_REQUEST["x_invoice_num"];
							$attention_getter_update = $db->Execute($site->sql_query);

							if (!$attention_getter_update)
							{
								$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
								$site->body .= "</table>\n";
								$site->display_page($db);
								$db->Close();
								exit;
							}
							$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[856])."</td>\n</tr>\n";
						}
						$site->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$site->configuration_data["classifieds_file_name"]."?a=4&b=1\" class=my_account_link >";
						$site->body .=urldecode($site->messages[860])."</A>\n\t</td>\n</tr>\n";
						$site->body .= "</table>\n";
					}
					$site->display_page($db);
					exit;
				}
				elseif ($this->transaction_results["x_response_code"] == 2)
				{
					//declined
					//send to declined page
					//send a declined email
					if (!$renew_upgrade)
					{
						$site->page_id = 14;
						$site->get_text($db);
						$site->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
						$site->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($site->messages[1365])."</td>\n</tr>\n";
						$site->body .="<tr class=page_title>\n\t<td>".urldecode($site->messages[177])."</td>\n</tr>\n";
						$site->body .="<tr class=page_description>\n\t<td>".urldecode($site->messages[655])."</td>\n</tr>\n";
						$site->body .="<tr class=page_description>\n\t<td>".urldecode($site->messages[654])."</td>\n</tr>\n";
						$site->body .="<tr class=page_description >\n\t<td>";
						$site->body .=urldecode($_REQUEST["x_response_reason_text"])."\n\t</td>\n</tr>";
						$site->body .="<tr class=view_ad_link >\n\t<td><A HREF=\"".$site->configuration_data["classifieds_file_name"]."?a=1\" class=view_ad_link >";
						$site->body .=urldecode($site->messages[523])."</A>\n\t</td>\n</tr>\n";
						$site->body .="</table>\n";
						$site->update_final_approval($db,0);
						$site->update_billing_approved($db,0);
					}
					else
					{
						$site->page_id = 58;
						$site->get_text($db);
						$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
						$site->body .= "<tr class=page_title><td>".urldecode($site->messages[850])."</td>\n</tr>\n";
						$site->body .= "<tr class=page_description><td>".urldecode($site->messages[851])."</td>\n</tr>\n";
						$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
						$site->body .="<tr class=page_description >\n\t<td>";
						$site->body .=urldecode($_REQUEST["x_response_reason_text"])."\n\t</td>\n</tr>";
						$site->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$site->configuration_data["classifieds_file_name"]."?a=4&b=1\" class=my_account_link >";
						$site->body .=urldecode($site->messages[860])."</A>\n\t</td>\n</tr>\n";
						$site->body .= "</table>\n";
					}
					$site->display_page($db);
					$db->Close();
					exit;
				}
				else
				{
					//error in transaction
					//send to error page
					//send message to admin
					if ($show_cc_trans["ad_placement"] == 1)
					{
						$site->page_id = 14;
						$site->get_text($db);
						$site->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
						$site->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($site->messages[1365])."</td>\n</tr>\n";
						$site->body .="<tr class=page_title>\n\t<td>".urldecode($site->messages[177])."</td>\n</tr>\n";
						$site->body .="<tr class=page_description>\n\t<td>".urldecode($site->messages[655])."</td>\n</tr>\n";
						$site->body .="<tr class=page_description>\n\t<td>".urldecode($site->messages[654])."</td>\n</tr>\n";
						$site->body .="<tr class=page_description >\n\t<td>";
						$site->body .=urldecode($_REQUEST["x_response_reason_text"])."\n\t</td>\n</tr>";
						$site->body .="<tr class=view_ad_link >\n\t<td><A HREF=\"".$site->configuration_data["classifieds_file_name"]."?a=1\" class=view_ad_link >";
						$site->body .=urldecode($site->messages[523])."</A>\n\t</td>\n</tr>\n";
						$site->body .="</table>\n";
						include("classes/classified_sell_class.php");
						$sell = new Classified_sell($db,$_REQUEST["x_cust_id"],$language_id,$HTTP_COOKIE_VARS["classified_session"]);
						$sell->update_final_approval($db,0);
						$sell->update_billing_approved($db,0);
					}
					else
					{
						$site->page_id = 58;
						$site->get_text($db);
						$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
						$site->body .= "<tr class=page_title><td>".urldecode($site->messages[850])."</td>\n</tr>\n";
						$site->body .= "<tr class=page_description><td>".urldecode($site->messages[851])."</td>\n</tr>\n";
						$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
						$site->body .="<tr class=page_description >\n\t<td>";
						$site->body .=urldecode($_REQUEST["x_response_reason_text"])."\n\t</td>\n</tr>";
						$site->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$site->configuration_data["classifieds_file_name"]."?a=4&b=1\" class=my_account_link >";
						$site->body .=urldecode($site->messages[860])."</A>\n\t</td>\n</tr>\n";
						$site->body .= "</table>\n";
					}
					$site->display_page($db);
					$db->Close();
					exit;
				}
			}
			else
			{
				//this transaction has already taken place and cannot be changed
				//send to error page

				//send message to admin
				//second transaction attempt
				if ($show_cc_trans["ad_placement"] == 1)
				{
					$site->page_id = 14;
					$site->get_text($db);
					$site->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
					$site->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($site->messages[1365])."</td>\n</tr>\n";
					$site->body .="<tr class=page_title>\n\t<td>".urldecode($site->messages[177])."</td>\n</tr>\n";
					$site->body .="<tr class=page_description>\n\t<td>".urldecode($site->messages[655])."</td>\n</tr>\n";
					$site->body .="<tr class=page_description>\n\t<td>".urldecode($site->messages[654])."</td>\n</tr>\n";
					$site->body .="<tr class=page_description >\n\t<td>";
					$site->body .=urldecode($_REQUEST["x_response_reason_text"])."\n\t</td>\n</tr>";
					$site->body .="<tr class=view_ad_link >\n\t<td><A HREF=\"".$site->configuration_data["classifieds_file_name"]."?a=1\" class=view_ad_link >";
					$site->body .=urldecode($site->messages[523])."</A>\n\t</td>\n</tr>\n";
					$site->body .="</table>\n";
					include("classes/classified_sell_class.php");
					$sell = new Classified_sell($db,$_REQUEST["x_cust_id"],$language_id,$HTTP_COOKIE_VARS["classified_session"]);
					
					$sell->update_final_approval($db,0);
					$sell->update_billing_approved($db,0);
				}
				else
				{
					$site->page_id = 58;
					$site->get_text($db);
					$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
					$site->body .= "<tr class=page_title><td>".urldecode($site->messages[850])."</td>\n</tr>\n";
					$site->body .= "<tr class=page_description><td>".urldecode($site->messages[851])."</td>\n</tr>\n";
					$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
					$site->body .="<tr class=page_description >\n\t<td>";
					$site->body .=urldecode($_REQUEST["x_response_reason_text"])."\n\t</td>\n</tr>";
					$site->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$site->configuration_data["classifieds_file_name"]."?a=4&b=1\" class=my_account_link >";
					$site->body .=urldecode($site->messages[860])."</A>\n\t</td>\n</tr>\n";
					$site->body .= "</table>\n";
				}
				$site->display_page($db);
				$db->Close();
				exit;
			}

		}
		else
		{
			//this transaction does not exist
			echo "nope";
		}
	}
	else
		echo "nope";
}
else
	echo "nope";
?>