<? //worldpay_process.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

//transId
//transStatus
//	Y - successful
// 	C - cancelled
//transTime
//authAmount
//authCurrency
//authAmountString
//rawAuthMessage
//rawAuthCode
//callbackPW
//cardType
//countryString
//countryMatch
//	Y - match
//	N - no match
//	B - comparison not available
//	I - contact country not supplied
//	S - card issue country not available
//AVS
//	1234
//	1 - card verification
//	2 - postcode AVS check
//	3 - address AVS check
//	4 - country comparison check
//	values
//		0 - not supported
//		1 - not checked
//		2 - matched
//		4 - not matched
//		8 - partially matched
//cartId
//M_sessionId
//M_customerId
//name
//address
//postcode
//country
//tel
//fax
//email
//amount
//currency
//description

//mail("james@geodesicsolutions.com","worldpay test 1a",$_SERVER["REMOTE_ADDR"]."\n".$_SERVER["QUERY_STRING"]."is the query string");

include("config.php");
include("classes/adodb.inc.php");
include("classes/site_class.php");

//mail("james@geodesicsolutions.com","worldpay test 1",$_SERVER["REMOTE_ADDR"]."\n".$cartId." is cart id\n".$M_customerId." is customer id\n");

if (($_REQUEST["cartId"]) && ($_REQUEST["M_customerId"]))
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

	$site = new Site($db,2,$language_id,$_REQUEST["M_customerId"]);

	//check to see if transaction exists
	$sql_query = "select * from ".$site->worldpay_configuration_table;
	//echo $sql_query."<br>";
	$configuration_result = $db->Execute($sql_query);
	if (!$configuration_result)
	{
		//echo $sql_query." is the query <br>\n";
		$db->Close();
		exit;
	}
	elseif ($configuration_result->RecordCount() == 1)
	{
		$worldpay_configuration = $configuration_result->FetchRow();
		if (strlen(trim($worldpay_configuration["callback_password"])) > 0)
		{
			if ($worldpay_configuration["callback_password"] != $_POST["callbackPW"])
			{
				//password does not match
				$db->Close();
				exit;
			}
		}

		$sql_query = "select * from ".$site->worldpay_transaction_table."
			where transaction_id = \"".$_REQUEST["cartId"]."\" and user_id = \"".$_POST["M_customerId"]."\" and session_id = \"".$_POST["M_sessionId"]."\"";
		$trans_result = $db->Execute($sql_query);
		if (!$trans_result)
		{
			$db->Close();
			exit;
		}
		elseif ($trans_result->RecordCount() == 1)
		{
			$show_worldpay_trans = $trans_result->FetchRow();
			if (($show_worldpay_trans["amount"] == $_REQUEST["authAmount"]) && ($_REQUEST["transStatus"] == "Y") && ($show_worldpay_trans["transstatus"] == ""))
			{
				//this transaction has not taken place
				//update worldpay transaction table
				$sql_query = "update ".$site->worldpay_transaction_table." set
					transStatus = \"".$_POST["transStatus"]."\",
					transTime = \"".$_POST["transTime"]."\",
					authAmount = \"".$_POST["authAmount"]."\",
					authCurrency = \"".$_POST["authCurrency"]."\",
					authAmountString = \"".$_POST["authAmountString"]."\",
					rawAuthMessage = \"".$_POST["rawAuthMessage"]."\",
					rawAuthCode = \"".$_POST["rawAuthCode"]."\",
					countryString = \"".$_POST["countryString"]."\",
					countryMatch = \"".$_POST["countryMatch"]."\",
					AVS = \"".$_POST["AVS"]."\"
					where transaction_id = \"".$_REQUEST["cartId"]."\" and user_id = \"".$_POST["M_customerId"]."\"";
				$update_result = $db->Execute($sql_query);
				if (!$update_result)
				{
					$site->display_page($db);
					$db->Close();
					exit;
				}
				if ($_POST["transStatus"] == "Y")
				{
					//approved
					//turn on classified ad
					//send to success page
					//send a success message
					if ($show_worldpay_trans["pay_invoice"] > 0)
					{
						$site->sql_query = "update ".$site->invoices_table." set date_paid = ".time()." where invoice_id = ".$show_worldpay_trans["pay_invoice"];
						if ($this->shifted_time($db)->shifted_time($db)->debug_invoices) echo $site->sql_query."<br>";
						$paid_result = $db->Execute($site->sql_query);
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post - 11 - authorize.net - aim",$site->sql_query."\n\n".date("l dS of F Y h:i:s A"));
						if (!$paid_result)
						{
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 11a - authorize.net - aim - error",$site->sql_query."\n\n".date("l dS of F Y h:i:s A"));
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
							$site->remove_account_session($db,$this->shifted_time($db)->session_id);
							$site->display_page($db);
							$db->Close();
							exit;
						}
					}
					elseif ($show_worldpay_trans["account_balance"] > 0)
					{
						//update approval for this balance transaction
						$this->shifted_time($db)->update_balance_approval($db,$show_worldpay_trans["account_balance"],$_REQUEST["cartId"]);

						//add to the users balance
						//user data still exists
						$new_balance = $user_data["account_balance"] + $this->shifted_time($db)->account_variables["price"];
						$sql_query = "update ".$this->shifted_time($db)->userdata_table." set
							account_balance = ".$new_balance."
							where id = ".$show_worldpay_trans["user_id"];
						$update_balance_results = $db->Execute($sql_query);
						if (!$update_balance_results)
						{
							return false;
						}
						else
						{
							$site->page_id = 175;
							$site->get_text($db);
							$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
							$site->body .= "<tr class=page_title><td>".urldecode($site->messages[2531])."</td>\n</tr>\n";
							$site->body .= "<tr class=page_description><td>".urldecode($site->messages[2532])."</td>\n</tr>\n";
							$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[2537])." ".
								$site->configuration_data["precurrency"]." ".sprintf("%01.2f",$new_balance).
								" ".$site->configuration_data["postcurrency"]."</td>\n</tr>\n";
							$site->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$site->configuration_data["classifieds_url"]."?a=4&b=3\" class=my_account_link >";
							$site->body .=urldecode($site->messages[2536])."</A>\n\t</td>\n</tr>\n";
							$site->body .= "</table>\n";
							$site->remove_account_session($db,$this->shifted_time($db)->session_id);
							$site->display_page($db);
							$db->Close();
							exit;
						}
					}
					elseif ($show_worldpay_trans["subscription_renewal"] == 1)
					{
						$site->page_id = 109;
						$site->get_text($db);

						$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
						$site->body .= "<tr class=page_title><td>".urldecode($site->messages[1669])."</td>\n</tr>\n";
						$site->body .= "<tr class=page_description><td>".urldecode($site->messages[1670])."</td>\n</tr>\n";

						$sql_query = "select * from ".$site->user_groups_price_plans_table." where id = ".$show_worldpay_trans["user_id"];
						//echo $sql_query." is the query <br>\n";
						$price_plan_id_result = $db->Execute($sql_query);
						if ($price_plan_id_result->RecordCount() == 1)
						{
							$show_price_plan = $price_plan_id_result->FetchRow();
							$sql_query = "select * from ".$site->subscription_choices." where price_plan_id = ".$show_price_plan["price_plan_id"]."
								and period_id = ".$show_worldpay_trans["renewal_length"]." order by value asc";
							$choices_result = $db->Execute($sql_query);
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
									$sql_query = "select * from ".$site->user_subscriptions_table." where user_id = ".$show_worldpay_trans["user_id"];
									$check_subscriptions_results = $db->Execute($sql_query);
									if (!$check_subscriptions_results)
									{
										$db->Close();
										exit;
									}
									elseif ($check_subscriptions_results->RecordCount() > 0)
									{
										//extend subscription period
										$show_subscription = $check_subscriptions_results->FetchRow();
										if ($show_subscription["subscription_expire"] > time())
											$new_expire = ($show_subscription["subscription_expire"] + ($show_choice["value"] * 86400));
										else
											$new_expire = (time() + ($show_choice["value"] * 86400));
										$sql_query = "update ".$site->user_subscriptions_table."
											set subscription_expire = ".$new_expire."
											where subscription_id = ".$show_subscription["subscription_id"];
										$update_subscriptions_results = $db->Execute($sql_query);
										if (!$update_subscriptions_results)
										{
											$db->Close();
											exit;
										}
									}
									else
									{
										//enter new subscription period
										$new_expire = (time() + ($show_choice["value"] * 86400));
										$sql_query = "insert into ".$site->user_subscriptions_table."
											(user_id,subscription_expire)
											values
											(".$show_worldpay_trans["user_id"].",".$new_expire.")";
										$insert_subscriptions_results = $db->Execute($sql_query);
										if (!$insert_subscriptions_results)
										{
											$db->Close();
											exit;
										}
									}
								}
							}
						}
						$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[1679])."</td>\n</tr>\n";
						$site->body .= "<tr><td><WPDISPLAY ITEM=banner></td></tr>\n";
						$site->body .= "<tr class=view_ad_link><td><a href=".$site->configuration_data["classifieds_url"]."?a=4&b=3 class=view_ad_link>".urldecode($site->messages[1672])."</a></td>\n</tr>\n";
						$site->body .= "</table>\n";
						$site->display_page($db);
					}
					elseif ($show_worldpay_trans["ad_placement"] == 1)
					{
						$site->page_id = 14;
						$site->get_text($db);

						$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
						$site->body .= "<tr class=place_ad_section_title><td>".urldecode($site->messages[1365])."</td>\n</tr>\n";
						$site->body .= "<tr class=page_title><td>".urldecode($site->messages[177])."</td>\n</tr>\n";
						//$site->body .= "<tr class=page_description><td>".urldecode($site->messages[851])."</td>\n</tr>\n";

						$sql_query = "select duration,category from ".$site->classifieds_table." where id = ".$show_worldpay_trans["classified_id"];
						$duration_result = $db->Execute($sql_query);
						if (!$duration_result)
						{
							$site->body .= "<tr class=error_message><td>".urldecode($site->messages[655])."</td>\n</tr>\n";
							$site->body .= "</table>\n";
							$site->display_page($db);
							$db->Close();
							exit;
						}
						$show_duration = $duration_result->FetchRow();

						//when ad ends
						$length_of_ad = ($show_duration["duration"] * 86400);
						$current_time = time();
						if  ($site->configuration_data["admin_approves_all_ads"])
						{
							$sql_query = "update ".$site->classifieds_table." set
								date = ".$current_time.",
								ends = ".($current_time + $length_of_ad)."
								where id = ".$show_worldpay_trans["classified_id"];
						}
						else
						{
							$sql_query = "update ".$site->classifieds_table." set
								live = 1,
								date = ".$current_time.",
								ends = ".($current_time + $length_of_ad)."
								where id = ".$show_worldpay_trans["classified_id"];
							
							// IDev Affiliate payment
							if($site->configuration_data["idevaffiliate"])
							{
								$idev_geoce_1 = $_REQUEST["amount"];
								$idev_geoce_2 = "auction-".$show_worldpay_trans["classified_id"];
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
						$classifieds_result = $db->Execute($sql_query);
						if (!$classifieds_result)
						{
							$site->body .= "<tr class=error_message><td>".urldecode($site->messages[655])."</td>\n</tr>\n";
							$site->body .= "</table>\n";
							$site->display_page($db);
							$db->Close();
							exit;
						}
						$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[180])."</td>\n</tr>\n";
						$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[858])."</td>\n</tr>\n";
						$site->body .= "<tr class=view_ad_link><td><a href=".$site->configuration_data["classifieds_url"]."?a=2&b=".$show_worldpay_trans["classified_id"]." class=view_ad_link>".urldecode($site->messages[181])."</a></td>\n</tr>\n";
						$site->body .= "<tr class=view_ad_link><td><a href=".$site->configuration_data["classifieds_url"]."?a=1 class=view_ad_link>".urldecode($site->messages[182])."</a></td>\n</tr>\n";
						$site->body .= "<tr><td><WPDISPLAY ITEM=banner></td></tr>\n";
						$site->body .= "</table>\n";
						$site->display_page($db);
						$site->update_category_count($db,$show_duration["category"]);
						include("classes/classified_sell_class.php");
						$sell = new Classified_sell($db,$_POST["M_customerId"],$language_id,$_POST["M_sessionId"]);
						$sell->check_user_subscription($db);
						$sell->check_subscriptions_and_credits($db);
						$sell->sell_success_email($db,$show_worldpay_trans["classified_id"]);
						$sell->remove_sell_session($db,$_POST["M_sessionId"]);
						$db->Close();
						exit;
					}
					else
					{
						$site->page_id = 58;
						$site->get_text($db);

						$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
						$site->body .= "<tr class=page_title><td>".urldecode($site->messages[850])."</td>\n</tr>\n";
						$site->body .= "<tr class=page_description><td>".urldecode($site->messages[851])."</td>\n</tr>\n";

						//renew/upgrade an ad
						$sql_query = "select * from ".$site->classifieds_table." where id = ".$show_worldpay_trans["classified_id"];
						$classified_result = $db->Execute($sql_query);
						if (!$classified_result)
						{
							$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
							$site->body .= "</table>\n";
							$site->display_page($db);
							$db->Close();
							exit;
						}
						$show_classified = $classified_result->FetchRow();

						if (($show_worldpay_trans["renew"] == 1) && ($show_worldpay_trans["renewal_length"] > 0))
						{
							if ($show_classifieds["ends"] > time())
								$new_expire = ($show_classified["ends"] + ($show_worldpay_trans["renewal_length"] * 86400));
							else
								$new_expire = (time() + ($show_worldpay_trans["renewal_length"] * 86400));
							if  ($site->configuration_data["admin_approves_all_ads"])
							{
								$sql_query = "update geodesic_classifieds set
									ends = \"".$new_expire."\",
									better_placement = \"0\",
									featured_ad = \"0\",
									bolding = \"0\",
									attention_getter = \"0\",
									featured_ad_2 = \"0\",
									featured_ad_3 = \"0\",
									featured_ad_4 = \"0\",
									featured_ad_5 = \"0\"
									where id = ".$show_worldpay_trans["classified_id"];
							}
							else
							{
								$sql_query = "update geodesic_classifieds set
									ends = \"".$new_expire."\",
									expiration_notice = 0,
									date = \"".time()."\",
									better_placement = \"0\",
									featured_ad = \"0\",
									bolding = \"0\",
									attention_getter = \"0\",
									featured_ad_2 = \"0\",
									featured_ad_3 = \"0\",
									featured_ad_4 = \"0\",
									featured_ad_5 = \"0\",
									live = 1
									where id = ".$show_worldpay_trans["classified_id"];
							}
							$renew_result = $db->Execute($sql_query);
							if (!$renew_result)
							{
								$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
								$site->body .= "</table>\n";
								$site->display_page($db);
								$db->Close();
								exit;
							}
							$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[852])."</td>\n</tr>\n";

							if ($show_worldpay_trans["use_credit_for_renewal"])
							{
								$site->sql_query = "select * from geodesic_classifieds_user_credits where user_id = ".$show_worldpay_trans["user_id"]." order by credits_expire asc limit 1";
								$credits_results = $db->Execute($site->sql_query);
								//$this->shifted_time($db)->body .=$this->shifted_time($db)->sql_query."<br>\n";
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
										$site->sql_query = "delete from geodesic_classifieds_user_credits where
											credits_id = ".$show_credits["credits_id"]."
											and user_id = ".$show_worldpay_trans["user_id"];
										$remove_credits_results = $db->Execute($site->sql_query);
										//$this->shifted_time($db)->body .=$this->shifted_time($db)->sql_query."<br>\n";
										if (!$remove_credits_results)
										{
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
											and user_id = ".$show_worldpay_trans["user_id"];
										$remove_credit = $db->Execute($site->sql_query);
										//$this->shifted_time($db)->body .=$this->shifted_time($db)->sql_query."<br>\n";
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
								// If not using credit then pass it to idev
								// IDev Affiliate payment
								if($site->configuration_data["idevaffiliate"] && $site->configuration_data["idev_renewal"])
								{
									$idev_geoce_1 = $_REQUEST["amount"];
									$idev_geoce_2 = "auction-renewal-".$show_worldpay_trans["classified_id"];
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
								$idev_geoce_1 = $_REQUEST["amount"];
								$idev_geoce_2 = "auction-upgrade-".$show_worldpay_trans["classified_id"];
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
							
							// Change end time
							if($sell->configuration_data["upgrade_time"])
							{
								$end_time = ($show_classified["ends"] - $show_classified["date"]) + time();
								$site->sql_query = "update geodesic_classifieds set
									ends = ".$end_time." 
									where id = ".$show_worldpay_trans["classified_id"];
								$end_result = $db->Execute($site->sql_query);
							}
						}

						if ($show_worldpay_trans["bolding"] == 1)
						{
							$sql_query = "update geodesic_classifieds set
								bolding = \"1\"
								where id = ".$show_worldpay_trans["classified_id"];
							$bolding_result = $db->Execute($sql_query);
							if (!$bolding_result)
							{
								$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
								$site->body .= "</table>\n";
								$site->display_page($db);
								$db->Close();
								exit;
							}
							$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[853])."</td>\n</tr>\n";
						}

						if ($show_worldpay_trans["better_placement"] == 1)
						{
							$sql_query = "update geodesic_classifieds set
								better_placement = \"1\"
								where id = ".$show_worldpay_trans["classified_id"];
							$better_placement_result = $db->Execute($sql_query);
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

						if ($show_worldpay_trans["featured_ad"] == 1)
						{
							$sql_query = "update geodesic_classifieds set
								featured_ad = \"1\"
								where id = ".$show_worldpay_trans["classified_id"];
							$featured_ad_result = $db->Execute($sql_query);
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

						if ($show_worldpay_trans["featured_ad_2"] == 1)
						{
							$sql_query = "update geodesic_classifieds set
								featured_ad_2 = \"1\"
								where id = ".$show_worldpay_trans["classified_id"];
							$featured_ad_result = $db->Execute($sql_query);
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

						if ($show_worldpay_trans["featured_ad_3"] == 1)
						{
							$sql_query = "update geodesic_classifieds set
								featured_ad_3 = \"1\"
								where id = ".$show_worldpay_trans["classified_id"];
							$featured_ad_result = $db->Execute($sql_query);
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

						if ($show_worldpay_trans["featured_ad_4"] == 1)
						{
							$sql_query = "update geodesic_classifieds set
								featured_ad_4 = \"1\"
								where id = ".$show_worldpay_trans["classified_id"];
							$featured_ad_result = $db->Execute($sql_query);
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

						if ($show_worldpay_trans["featured_ad_5"] == 1)
						{
							$sql_query = "update geodesic_classifieds set
								featured_ad_5 = \"1\"
								where id = ".$show_worldpay_trans["classified_id"];
							$featured_ad_result = $db->Execute($sql_query);
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

						if ($show_worldpay_trans["attention_getter"] == 1)
						{
							$site->sql_query = "select * from geodesic_choices where choice_id = ".$show_worldpay_trans["attention_getter_choice"];
							$attention_getter_result = $db->Execute($site->sql_query);
							//$site->body .=$site->sql_query."<br>\n";
							if (!$attention_getter_result)
							{
								$site->body .= "<tr class=error_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
								$site->body .= "</table>\n";
								$site->display_page($db);
								$db->Close();
								return false;
							}
							elseif ($attention_getter_result->RecordCount() == 1)
							{
								$show_attention_getter = $attention_getter_result->FetchRow();
								$attention_getter_url = $show_attention_getter["value"];
							}
							else
							{
								$site->classified_variables["attention_getter"] = 0;
								$attention_getter_url = "";
							}

							$sql_query = "update geodesic_classifieds set
								attention_getter = \"1\",
								attention_getter_url = \"".$attention_getter_url."\"
								where id = ".$show_worldpay_trans["classified_id"];
							$attention_getter_update = $db->Execute($sql_query);
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
						$site->body .= "<tr><td><WPDISPLAY ITEM=banner></td></tr>\n";
						$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[1977])."</td>\n</tr>\n";
						$site->body .= "</table>\n";
						$site->display_page($db);
						$db->Close();
						exit;
					}
				}
				elseif ($_POST["transStatus"] == "C")
				{
					//declined - give another choice
					if ($show_worldpay_trans["ad_placement"] == 1)
					{
						$site->page_id = 14;
						$site->get_text($db);
						$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
						$site->body .= "<tr class=page_title><td>".urldecode($site->messages[177])."</td>\n</tr>\n";
						$site->body .= "<tr class=page_description><td>".urldecode($site->messages[655])."</td>\n</tr>\n";
						$site->body .= "<tr class=error_message><td>".urldecode($site->messages[654])."</td>\n</tr>\n";
						$site->body .= "<tr class=view_ad_link><td><a href=".$site->configuration_data["classifieds_url"]."?a=1 class=view_ad_link>".urldecode($site->messages[861])."</a></td>\n</tr>\n";
						$site->body .= "</table>\n";

					}
					else
					{
						$site->page_id = 58;
						$site->get_text($db);

						$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
						$site->body .= "<tr class=page_title><td>".urldecode($site->messages[850])."</td>\n</tr>\n";
						$site->body .= "<tr class=page_description><td>".urldecode($site->messages[851])."</td>\n</tr>\n";
						$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
						$site->body .= "<tr class=my_account_link><td><a href=".$site->configuration_data["classifieds_url"]."?a=4&b=1 class=my_account_link>".urldecode($site->messages[860])."</a></td>\n</tr>\n";
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
					if ($show_worldpay_trans["ad_placement"] == 1)
					{
						$site->page_id = 14;
						$site->get_text($db);
						$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
						$site->body .= "<tr class=page_title><td>".urldecode($site->messages[177])."</td>\n</tr>\n";
						$site->body .= "<tr class=page_description><td>".urldecode($site->messages[655])."</td>\n</tr>\n";
						$site->body .= "<tr class=error_message><td>".urldecode($site->messages[654])."</td>\n</tr>\n";
						$site->body .= "<tr class=view_ad_link><td><a href=".$site->configuration_data["classifieds_url"]."?a=1 class=view_ad_link>".urldecode($site->messages[861])."</a></td>\n</tr>\n";
						$site->body .= "</table>\n";
					}
					else
					{
						$site->page_id = 58;
						$site->get_text($db);

						$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
						$site->body .= "<tr class=page_title><td>".urldecode($site->messages[850])."</td>\n</tr>\n";
						$site->body .= "<tr class=page_description><td>".urldecode($site->messages[851])."</td>\n</tr>\n";
						$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
						$site->body .= "</table>\n";
					}

					$site->display_page($db);
					$db->Close();
					exit;
				}
			}
			else
			{
				//bad transaction data
				if ($show_worldpay_trans["ad_placement"] == 1)
				{
					$site->page_id = 14;
					$site->get_text($db);
					$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
					$site->body .= "<tr class=page_title><td>".urldecode($site->messages[177])."</td>\n</tr>\n";
					$site->body .= "<tr class=page_description><td>".urldecode($site->messages[655])."</td>\n</tr>\n";
					$site->body .= "<tr class=error_message><td>".urldecode($site->messages[654])."</td>\n</tr>\n";
					$site->body .= "<tr class=view_ad_link><td><a href=".$site->configuration_data["classifieds_url"]."?a=1 class=view_ad_link>".urldecode($site->messages[861])."</a></td>\n</tr>\n";
					$site->body .= "</table>\n";
				}
				else
				{
					$site->page_id = 58;
					$site->get_text($db);
					$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
					$site->body .= "<tr class=page_title><td>".urldecode($site->messages[850])."</td>\n</tr>\n";
					$site->body .= "<tr class=page_description><td>".urldecode($site->messages[851])."</td>\n</tr>\n";
					$site->body .= "<tr class=success_failure_message><td>".urldecode($site->messages[857])."</td>\n</tr>\n";
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
			$site->page_id = 59;
			$site->get_text($db);
			$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
			$site->body .= "<tr class=error_message><td>".urldecode($site->messages[859])."</td>\n</tr>\n";
			$site->body .= "</table>\n";
			$site->display_page($db);
			$db->Close();
			exit;
		}
	}
	else
	{
		//cannot get worldpay configuration
		$site->page_id = 59;
		$site->get_text($db);
		$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
		$site->body .= "<tr class=error_message><td>".urldecode($site->messages[859])."</td>\n</tr>\n";
		$site->body .= "</table>\n";
		$site->display_page($db);
		$db->Close();
		exit;
	}
}
else
{
	exit;
}

?>
