<?php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

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

$debug_email = "";
//if (strlen($debug_email) > 0)
//	@mail($debug_email,"post - 1 - internet secure - referrer reported","referrer given by server: ".$_SERVER["HTTP_REFERER"]);
$referrer_address = $_SERVER["HTTP_REFERER"];

foreach ($_POST as $key => $value)
{
	$temp .= $key." = ".$value."\n";
}

//if (strlen($debug_email) > 0)
//	@mail($debug_email,"post - 2 - Internet Secure variables received by post",$temp);

foreach ($_REQUEST as $key => $value)
{
	$temp1 .= $key." = ".$value."\n";
}

if (strlen($debug_email) > 0)
	@mail($debug_email,"post - 2a - Internet Secure all variables recieved post and get",$temp1);

foreach ($_GET as $key => $value)
{
	$temp2 .= $key." = ".$value."\n";
}

//if (strlen($debug_email) > 0)
//	@mail($debug_email,"post - 2b - Internet Secure variables received by get",$temp2);

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
//if (strlen($debug_email) > 0)
//	@mail($debug_email,"post - 3 - Internet Secure","connected to db");

$site = new Site($db,2,$_POST["language_id"],$_COOKIE["classified_session"]);

$site->sql_query = "select * from geodesic_cc_internetsecure";
$cc_result = $db->Execute($site->sql_query);
//if (strlen($debug_email) > 0)
//	@mail($debug_email,"post - 4 - Internet Secure",$site->sql_query);
if (!$cc_result)
{
	//echo $sql_query." is the query <br>\n";
	$db->Close();
	exit;
}
elseif ($cc_result->RecordCount() == 1)
{
	//basic int
	$cc = $cc_result->FetchRow();

	$internetsecure_variables = $_REQUEST;

	//get transaction id from DoubleColonProducts variable
	list($price,$quantity,$transaction_id,$description,$flags) = explode("::",$internetsecure_variables["DoubleColonProducts"],5);

	if (strlen($debug_email) > 0)
		@mail($debug_email,"post - 4b - Internet Secure transaction id returned","transaction id: ".$transaction_id."\nprice: ".$price."\ndescription: ".$description."\ndoublecolonproduct: ".$internetsecure_variables["DoubleColonProducts"]);
	if ($transaction_id)
	{
		//check to see if transaction exists and has not been processed
		$site->sql_query = "select * from geodesic_cc_internetsecure_transactions
			where internetsecure_transaction_id = \"".$transaction_id."\"";
		$trans_result = $db->Execute($site->sql_query);
		if (strlen($debug_email) > 0)
			@mail($debug_email,"post - 5 - Internet Secure",$site->sql_query);
		if (!$trans_result)
		{
			//echo $sql_query." is the query <br>\n";
			$db->Close();
			exit;
		}
		elseif ($trans_result->RecordCount() == 1)
		{
			$show_cc_trans = $trans_result->FetchRow();

			if ($show_cc_trans["credit_card_processed"] == 0)
			{
				//this transaction has not taken place
				//update authorizenet table
				$site->sql_query = "update geodesic_cc_internetsecure_transactions set
					doublecolonproducts = \"".$internetsecure_variables["DoubleColonProducts"]."\",
					receipt_number = \"".$internetsecure_variables["receiptnumber"]."\",
					salesordernumber = \"".$internetsecure_variables["SalesOrderNumber"]."\",
					ipaddress = \"".$internetsecure_variables["ip_address"]."\",
					keysize = \"".$internetsecure_variables["KeySize"]."\",
					secretkeysize = \"".$internetsecure_variables["SecretKeySize"]."\",
					approvalcode = \"".$internetsecure_variables["ApprovalCode"]."\",
					returnurl =\"".$internetsecure_variables["ReturnURL"]."\",
					unixtimestamp =\"".$internetsecure_variables["UnixTimeStamp"]."\",
					verbage = \"".$internetsecure_variables["Verbage"]."\",
					niceverbage = \"".$internetsecure_variables["NiceVerbage"]."\",
					credit_card_processed = \"1\"
					where internetsecure_transaction_id = \"".$transaction_id."\"";
				$result = $db->Execute($site->sql_query);
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 6 - Internet Secure",$site->sql_query);
				if (!$result)
				{
					echo $result->ErrorMsg()." is the error<bR>\n";
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post - 6error - Internet Secure",$db->ErrorMsg());
					$db->Close();
					exit;
				}
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 6a - Internet Secure","Verbage = ".$internetsecure_variables["Verbage"] );
				if (($internetsecure_variables["Verbage"] == "Test Approved") || ($internetsecure_variables["Verbage"] == "Approved"))
				{
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post - 6a - Internet Secure type of trans",$show_cc_trans["ad_placement"]." is ad_placement\n\n".$show_cc_trans["subscription_renewal"]." is subscription_renewal");
					
					if ($show_cc_trans["pay_invoice"] > 0)
					{
						$site->sql_query = "update ".$site->invoices_table." set date_paid = ".$this->shifted_time($db)." where invoice_id = ".$show_cc_trans["pay_invoice"];
						if ($this->debug_invoices) echo $site->sql_query."<br>";
						$paid_result = $db->Execute($this->sql_query);
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
							$site->remove_account_session($db,$this->session_id);
							$site->display_page($db);
							$db->Close();
							exit;
						}
					}
					elseif ($show_cc_trans["account_balance"] > 0)
					{
						//update approval for this balance transaction
						$site->update_balance_approval($db,$show_cc_trans["account_balance"],$transaction_id);

						//add to the users balance
						//user data still exists
						$new_balance = $user_data["account_balance"] + $this->account_variables["price"];
						$sql_query = "update ".$this->userdata_table." set
							account_balance = ".$new_balance."
							where id = ".$show_cc_trans["user_id"];
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
							$site->remove_account_session($db,$this->session_id);
							$site->display_page($db);
							$db->Close();
							exit;
						}
					}
					elseif ($show_cc_trans["subscription_renewal"] == 1)
					{
						$site->sql_query = "select * from geodesic_classifieds_user_groups_price_plans where id = ".$show_cc_trans["user_id"];
						//echo $sql_query." is the query <br>\n";
						$price_plan_id_result = $db->Execute($site->sql_query);
						if ($price_plan_id_result->RecordCount() == 1)
						{
							$show_price_plan = $price_plan_id_result->FetchRow();
							$site->sql_query = "select * from geodesic_classifieds_subscription_choices where price_plan_id = ".$show_price_plan["price_plan_id"]."
								and period_id = ".$show_cc_trans["renewal_length"]." order by value asc";
							$choices_result = $db->Execute($site->sql_query);
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
									$site->sql_query = "select * from geodesic_classifieds_user_subscriptions where user_id = ".$show_cc_trans["user_id"];
									$check_subscriptions_results = $db->Execute($site->sql_query);
									if (!$check_subscriptions_results)
									{
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
										$site->sql_query = "update geodesic_classifieds_user_subscriptions
											set subscription_expire = ".$new_expire."
											where subscription_id = ".$show_subscription["subscription_id"];
										$update_subscriptions_results = $db->Execute($site->sql_query);
										if (!$update_subscriptions_results)
										{
											$db->Close();
											exit;
										}
									}
									else
									{
										//enter new subscription period
										$new_expire = ($this->shifted_time($db) + ($show_choice["value"] * 86400));
										$site->sql_query = "insert into geodesic_classifieds_user_subscriptions
											(user_id,subscription_expire)
											values
											(".$show_cc_trans["user_id"].",".$new_expire.")";
										$insert_subscriptions_results = $db->Execute($site->sql_query);
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
									$db->Close();
									exit;
								}
							}
						}
					}
					elseif ($show_cc_trans["ad_placement"] == 1)
					{
						//this is a new ad
						//approved
						//turn on classified ad
						//send to success page
						//send a success message

						include("classes/classified_sell_class.php");
						$sell = new Classified_sell($db,$show_cc_trans["user_id"],$language_id,$_COOKIE["classified_session"]);
						$sell->page_id = 14;
						$sell->get_text($db);

						$sell->sql_query = "select * from geodesic_classifieds where id = ".$show_cc_trans["classified_id"];
						$duration_result = $db->Execute($sell->sql_query);
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post - 7 - Internet Secure",$sell->sql_query);
						if (!$duration_result)
						{
							//echo $sql_query." is the query <br>\n";
							$db->Close();
							exit;
						}
						$show_duration = $duration_result->FetchRow();

						//when ad ends
						$length_of_ad = ($show_duration["duration"] * 86400);
						$current_time = $this->shifted_time($db);
						if  ($sell->configuration_data["admin_approves_all_ads"])
						{
							$sell->sql_query = "update geodesic_classifieds set
								date = ".$current_time.",
								ends = ".($current_time + $length_of_ad)."
								where id = ".$show_cc_trans["classified_id"];
						}
						else
						{
							$sell->sql_query = "update geodesic_classifieds set
								live = 1,
								date = ".$current_time.",
								ends = ".($current_time + $length_of_ad)."
								where id = ".$show_cc_trans["classified_id"];
							
							// IDev Affiliate payment
							if($sell->configuration_data["idevaffiliate"])
							{
								$idev_geoce_1 = $internetsecure_variables["xxxAmount"];
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
							@mail($debug_email,"post - 8 - Internet Secure",$sell->sql_query);
						$result = $db->Execute($sell->sql_query);
						if (!$result)
						{
							//echo $sql_query." is the query <br>\n";
							$db->Close();
							exit;
						}
						$sell->update_category_count($db,$show_duration["category"]);
						$sell->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
						$sell->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($sell->messages[1365])."</td>\n</tr>\n";
						$sell->body .="<tr class=page_title>\n\t<td>".urldecode($sell->messages[177])."</td>\n</tr>\n";
						$sell->body .="<tr class=page_description>\n\t<td>".urldecode($sell->messages[180])."</td>\n</tr>\n";
						$sell->body .="<tr class=page_description>\n\t<td>".urldecode($sell->messages[653])."</td>\n</tr>\n";
						$sell->body .="<tr class=view_ad_link >\n\t<td><A HREF=\"".trim($sell->configuration_data["classifieds_url"])."?a=2&b=".$show_cc_trans["classified_id"]."\" class=view_ad_link >";
						$sell->body .=urldecode($sell->messages[181])."</A>\n\t</td>\n</tr>\n";
						$sell->body .="</table>\n";
						$sell->check_user_subscription($db);
						$sell->check_subscriptions_and_credits($db);
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post - 8a - Internet Secure - remove sell session",$_COOKIE["classified_session"]);
						$sell->remove_sell_session($db,$_COOKIE["classified_session"]);

						//remove sell session
						//have to micromanage because of Internet Secure using a get instead of a post on the new system
						$sell->sql_query = "select * from geodesic_classifieds_sell_session where classified_id = ".$show_cc_trans["classified_id"];
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post - 8b - Internet Secure",$sell->sql_query);
						$get_sell_result = $db->Execute($sell->sql_query);
						if (!$get_sell_result)
						{
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 8bb - Internet Secure",$sell->sql_query);
							exit;
						}
						elseif ($get_sell_result->RecordCount() == 1)
						{
							$show_session = $get_sell_result->FetchRow();
							$sell->sql_query = "delete from geodesic_classifieds_sell_session where session = \"".$show_session["session"]."\"";
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 8c - Internet Secure",$sell->sql_query);
							$delete_sell_result = $db->Execute($sell->sql_query);
							if (!$delete_sell_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 8cc - Internet Secure",$sell->sql_query);
								exit;
							}

							//get the images captured so far
							$sell->sql_query = "delete from geodesic_classifieds_sell_session_images where session = \"".$show_session["session"]."\"";
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 8d - Internet Secure",$sell->sql_query);
							$delete_sell_image_result = $db->Execute($sell->sql_query);
							if (!$delete_sell_image_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 8dd - Internet Secure",$sell->sql_query);
								exit;
							}

							//get the category questions so far
							$sell->sql_query = "delete from geodesic_classifieds_sell_session_questions where session = \"".$show_session["session"]."\"";
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 8e - Internet Secure",$sell->sql_query);
							$delete_sell_question_result = $db->Execute($sell->sql_query);
							if (!$delete_sell_question_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 8ee - Internet Secure",$sell->sql_query);
								exit;
							}
						}
						$sell->sell_success_email($db,$show_cc_trans["classified_id"]);
						$sell->page_id = 14;
						$sell->display_page($db);
						$db->Close();
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post - 9 - Internet Secure","done");
						exit;
					}
					else
					{

						//do the renewal thing
						$site->page_id = 58;
						$site->get_text($db);
						$site->classified_user_id = $show_cc_trans["user_id"];

						$site->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
						$site->body .= "<tr class=page_title><td>".urldecode($site->messages[850])."</td>\n</tr>\n";
						$site->body .= "<tr class=page_description><td>".urldecode($site->messages[851])."</td>\n</tr>\n";

						$site->sql_query = "select * from geodesic_classifieds where id = ".$show_cc_trans["classified_id"];
						if (strlen($debug_email) > 0)
							@mail($debug_email,"post - 10 - Internet Secure",$site->sql_query);
						$classified_result = $db->Execute($site->sql_query);
						if (!$classified_result)
						{
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 10 - Internet Secure - error",$site->sql_query);
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
							if  ($site->configuration_data["admin_approves_all_ads"])
							{
								$site->sql_query = "update geodesic_classifieds set
									ends = \"".$new_expire."\",
									better_placement = \"0\",
									featured_ad = \"0\",
									bolding = \"0\",
									attention_getter = \"0\",
									featured_ad_2 = \"0\",
									featured_ad_3 = \"0\",
									featured_ad_4 = \"0\",
									featured_ad_5 = \"0\"
									where id = ".$show_cc_trans["classified_id"];
							}
							else
							{
								$site->sql_query = "update geodesic_classifieds set
									live = 1,
									expiration_notice = 0,
									date = \"".$this->shifted_time($db)."\",
									ends = \"".$new_expire."\",
									better_placement = \"0\",
									featured_ad = \"0\",
									bolding = \"0\",
									attention_getter = \"0\",
									featured_ad_2 = \"0\",
									featured_ad_3 = \"0\",
									featured_ad_4 = \"0\",
									featured_ad_5 = \"0\"
									where id = ".$show_cc_trans["classified_id"];
							}
							$renew_result = $db->Execute($site->sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 11 - Internet Secure",$site->sql_query);
							if (!$renew_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 11 - Internet Secure - error",$site->sql_query);
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
									@mail($debug_email,"post - 12 - Internet Secure",$site->sql_query);
								if (!$credits_results)
								{
									if (strlen($debug_email) > 0)
										@mail($debug_email,"post - 12 - Internet Secure - error",$site->sql_query);
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
											@mail($debug_email,"post - 13 - Internet Secure",$site->sql_query);
										if (!$remove_credits_results)
										{
											if (strlen($debug_email) > 0)
												@mail($debug_email,"post - 13 - Internet Secure - error",$site->sql_query);
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
											@mail($debug_email,"post - 14 - Internet Secure",$site->sql_query);
										if (!$remove_credit)
										{
											if (strlen($debug_email) > 0)
												@mail($debug_email,"post - 14 - Internet Secure - error",$site->sql_query);
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
									$idev_geoce_1 = $internetsecure_variables["xxxAmount"];
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
								$idev_geoce_1 = $internetsecure_variables["xxxAmount"];
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
										where id = ".$show_cc_trans["classified_id"];
								$end_result = $db->Execute($site->sql_query);
							}
						}

						if ($show_cc_trans["bolding"] == 1)
						{
							$site->sql_query = "update geodesic_classifieds set
								bolding = \"1\"
								where id = ".$show_cc_trans["classified_id"];
							$bolding_result = $db->Execute($site->sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 15 - Internet Secure",$site->sql_query);
							if (!$bolding_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 15 - Internet Secure - error",$site->sql_query);
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
								where id = ".$show_cc_trans["classified_id"];
							$better_placement_result = $db->Execute($site->sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 16 - Internet Secure",$site->sql_query);
							if (!$better_placement_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 16 - Internet Secure - error",$site->sql_query);
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
								where id = ".$show_cc_trans["classified_id"];
							$featured_ad_result = $db->Execute($site->sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 17 - Internet Secure",$site->sql_query);
							if (!$featured_ad_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 17 - Internet Secure - error",$site->sql_query);
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
								where id = ".$show_cc_trans["classified_id"];
							$featured_ad_result = $db->Execute($site->sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 18 - Internet Secure",$site->sql_query);
							if (!$featured_ad_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 18 - Internet Secure - error",$site->sql_query);
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
								where id = ".$show_cc_trans["classified_id"];
							$featured_ad_result = $db->Execute($site->sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 19 - Internet Secure",$site->sql_query);
							if (!$featured_ad_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 19 - Internet Secure - error",$site->sql_query);
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
								where id = ".$show_cc_trans["classified_id"];
							$featured_ad_result = $db->Execute($site->sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 20 - Internet Secure",$site->sql_query);
							if (!$featured_ad_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 20 - Internet Secure - error",$site->sql_query);
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
								where id = ".$show_cc_trans["classified_id"];
							$featured_ad_result = $db->Execute($site->sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 21 - Internet Secure",$site->sql_query);
							if (!$featured_ad_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 21 - Internet Secure - error",$site->sql_query);
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
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 22 - Internet Secure",$site->sql_query);
							if (!$attention_getter_result)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 22 - Internet Secure - error",$site->sql_query);
								$site->body .=$site->sql_query."<br>\n";
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
								where id = ".$show_cc_trans["classified_id"];
							$attention_getter_update = $db->Execute($site->sql_query);
							if (strlen($debug_email) > 0)
								@mail($debug_email,"post - 23 - Internet Secure",$site->sql_query);
							if (!$attention_getter_update)
							{
								if (strlen($debug_email) > 0)
									@mail($debug_email,"post - 23 - Internet Secure - error",$site->sql_query);
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
						$site->display_page($db);
						$db->Close();
						exit;
					}

				}
				elseif ($x_response_code == 2)
				{
					//declined
					//send to declined page
					//send a declined email
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post - 24 - Internet Secure - declined",$internetsecure_variables["x_response_reason_text"]);
					echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$site->configuration_data["classifieds_url"]."?a=1&credit_approval=2&declined=".urlencode($internetsecure_variables["x_response_reason_text"])."\">";
					$db->Close();
					exit;
				}
				else
				{
					//error in transaction
					//send to error page
					//send message to admin
					if (strlen($debug_email) > 0)
						@mail($debug_email,"post - 25 - Internet Secure - transaction error",$internetsecure_variables["x_response_reason_text"]);
					echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$site->configuration_data["classifieds_url"]."?a=1&credit_approval=3&declined=".urlencode($internetsecure_variables["x_response_reason_text"])."\">";
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
				if (strlen($debug_email) > 0)
					@mail($debug_email,"post - 26 - Internet Secure - already happened","this transaction has already taken place");
				echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$site->configuration_data["classifieds_url"]."?a=1&credit_approval=3\">";
				$db->Close();
				exit;
			}
		}
		else
		{
			//this transaction does not exist
			if (strlen($debug_email) > 0)
				@mail($debug_email,"post - 27 - Internet Secure - does not exist","this transaction does not exist");
			echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$site->configuration_data["classifieds_url"]."?a=1&credit_approval=3\">";
			$db->Close();
			exit;
		}
	}
	else
	{
		echo "nope...";
	}
}
?>
