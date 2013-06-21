<? //paypalipn.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$debug_email = "";
$email_msg = "LINE ".__LINE__."\n";
if (strlen($debug_email)>0) @mail($debug_email,"paypal_initiate.php",$email_msg);
//receiver_email
//item_name
//item_number
//quantity
//invoice = id
//custom
//option_name1
//option_selection1
//option_name2
//option_selection2
//num_cart_items
//**payment_status - "Pending","Completed","Failed","Denied"
//**pending_reason - "echeck","intl","verify", "address","upgrade","unilateral","other"
//**payment_date - "18:30:30 Jan 1,2000 PST"
//**payment_gross
//**payment_fee
//**txn_id
//**txn_type - "web_accept","cart","send_money"
//first_name
//last_name
//address_street
//address_city
//address_zip
//address_country
//address_status - "confirmed","unconfirmed"
//payer_email
//payer_id
//payer_status - "verified","unverified","intl_verified"
//**payment_type - "echeck","instant"
//**notify_version - "1.3"
//**verify_sign
//mc_gross ---------- currently unused
//mc_fee ---------- currently unused
//mc_currency ------- currently unused

//**supplied by paypal

// read the post from PayPal system and add 'cmd'
$email_msg = "LINE ".__LINE__."\n";
if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
$req = 'cmd=_notify-validate';
if ($_POST)
{
	foreach ($_POST as $key => $value)
	{
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}
}
else
{
	foreach ($HTTP_POST_VARS as $key => $value)
	{
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}
}
$email_msg = "LINE ".__LINE__."\n";
if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
include_once("config.php");
include_once("classes/adodb.inc.php");
include_once("classes/site_class.php");
$db = &ADONewConnection('mysql');
$site = new Site($db,0,0,0);
if($persistent_connections)
{
	//echo " Persistent Connection <bR>";
	if (!$db->PConnect($db_host, $db_username, $db_password, $database))
	{
		$email_msg = "LINE ".__LINE__."\t"."could not connect to database\n";
		if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
		exit;
	}
}
else
{
	//echo " No Persistent Connection <bR>";
	if (!$db->Connect($db_host, $db_username, $db_password, $database))
	{
		$email_msg = "LINE ".__LINE__."\t"."could not connect to database\n";
		if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
		exit;
	}
}
// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= 'Content-Length: ' . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

// assign posted variables to local variables
// note: additional IPN variables also available -- see IPN documentation
if ($_POST)
{
	$item_name = $_POST['item_name'];
	$receiver_email = $_POST['receiver_email'];
	$item_number = $_POST['item_number'];
	$invoice = $_POST['invoice'];
	$payment_status = $_POST['payment_status'];
	$payment_gross = $_POST['payment_gross'];
	$txn_id = $_POST['txn_id'];
	$payer_email = $_POST['payer_email'];
	$payer_id = $_POST['payer_id'];
	$pending_reason = $_POST['pending_reason'];
	$payment_date = $_POST['payment_date'];
	$payment_fee = $_POST['payment_fee'];
	$payer_status = $_POST['payer_status'];
	$payment_type = $_POST['payment_type'];
	$notify_version = $_POST['notify_version'];
	$verify_sign = $_POST['verify_sign'];
	$custom = $_POST['custom'];
	$email_msg = "LINE ".__LINE__."\n";
	foreach ($_POST as $key => $value)
		$email_msg .= "\n".$key." = ".$value;
	if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
}
else
{
	$item_name = $HTTP_POST_VARS['item_name'];
	$receiver_email = $HTTP_POST_VARS['receiver_email'];
	$item_number = $HTTP_POST_VARS['item_number'];
	$invoice = $HTTP_POST_VARS['invoice'];
	$payment_status = $HTTP_POST_VARS['payment_status'];
	$payment_gross = $HTTP_POST_VARS['payment_gross'];
	$txn_id = $HTTP_POST_VARS['txn_id'];
	$payer_email = $HTTP_POST_VARS['payer_email'];
	$payer_id = $HTTP_POST_VARS['payer_id'];
	$pending_reason = $HTTP_POST_VARS['pending_reason'];
	$payment_date = $HTTP_POST_VARS['payment_date'];
	$payment_fee = $HTTP_POST_VARS['payment_fee'];
	$payer_status = $HTTP_POST_VARS['payer_status'];
	$payment_type = $HTTP_POST_VARS['payment_type'];
	$notify_version = $HTTP_POST_VARS['notify_version'];
	$verify_sign = $HTTP_POST_VARS['verify_sign'];
	$custom = $HTTP_POST_VARS['custom'];
	$email_msg = "LINE ".__LINE__."\n";
	foreach ($HTTP_POST_VARS as $key => $value)
		$email_msg .= "\n".$key." = ".$value;
	if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
}
if (!$fp)
{
	// ERROR
	echo "$errstr ($errno)";
}
else
{
	$email_msg = "LINE ".__LINE__."\n";
	if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
	fputs ($fp, $header . $req);
	$all_res = "";
	/*$email_msg = "";
	while (!feof($fp))
		$email_msg .= fgets ($fp, 1024);
	if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}*/
	while (!feof($fp))
	{
		$res = fgets ($fp, 1024);
		/*$email_msg = "LINE ".__LINE__."\t\$res = ".$res."\t\$fp = ".$fp."\n";
		if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}*/
		//$res = trim($res);
		//$all_res .= $res;
		if (strcmp($res, "VERIFIED") == 0)
		{
			// check the payment_status is Completed
  			// check that txn_id has not been previously processed
  			// check that receiver_email is an email address in your PayPal account
  			// process payment
			$email_msg = "LINE ".__LINE__."\n\$payment_status = ".$payment_status."\nstrlen = ".strlen($payment_status)."\n";
			if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
  			if ($payment_status == "Completed")
  			{
  				$email_msg = "LINE ".__LINE__."\n";
				if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
  				$sql_query = "select id from geodesic_paypal_transactions where txn_id = \"".$txn_id."\"";
				$result = $db->Execute($sql_query);
				if (!$result)
				{
					$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
					if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
					$db->Close();
					exit;
				}
				else
				{
					/*if ($result->RecordCount() <= 0)
					{
						$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
						if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
						$db->Close();
						exit;
					}*/

					$sql_query = "select * from geodesic_paypal_transactions where transaction_id = \"".$invoice."\" and custom = \"".$custom."\"";
					$result = $db->Execute($sql_query);
					$email_msg = "LINE ".__LINE__."\n";
					if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
					if (!$result)
					{
						$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
						if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
							$sql_query = "update geodesic_paypal_transactions set
								address_status = \"".$address_status."\",
								payment_status = \"".$payment_status."\",
								pending_reason = \"".$pending_reason."\",
								payment_date = \"".$payment_date."\",
								payment_fee = \"".$payment_fee."\",
								txn_id = \"".$txn_id."\",
								txn_type = \"".$txn_type."\",
								payer_status = \"".$payer_status."\",
								payment_type = \"".$payment_type."\",
								notify_version = \"".$notify_version."\",
								verify_sign = \"".$verify_sign."\"
								where transaction_id = ".$invoice;
							$result = $db->Execute($sql_query);
							$email_msg = "LINE ".__LINE__."\n";
							if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
							if (!$result)
							{
								$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
								if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
								$db->Close();
								exit;
							}

							$email_msg = "LINE ".__LINE__."\n";
							if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
							if ($show_transaction["pay_invoice"] > 0)
							{
								$sql_query = "update geodesic_invoices set date_paid = ".$site->shifted_time($db)." where invoice_id = ".$show_transaction["pay_invoice"];
								$paid_result = $db->Execute($sql_query);
								$email_msg = "LINE ".__LINE__."\n";
								if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
								if (!$paid_result)
								{
									$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									$db->Close();
									exit;
								}
								else
								{
									//invoice payment successful
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									exit;
								}
							}
							elseif ($show_transaction["account_balance"] > 0)
							{
								$email_msg = "LINE ".__LINE__."\n";
								if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
								//update approval for this balance transaction
								$sql_query = "update geodesic_balance_transactions set 
									approved = 1,
									cc_transaction_id = \"".$invoice."\"
									where transaction_id = ".$show_transaction["account_balance"];
								$update_balance_transaction_result = $db->Execute($sql_query);
								if (!$update_balance_transaction_result)
								{
									$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									$db->Close();
									exit;
								}
								else
								{

									$email_msg = "LINE ".__LINE__."\n";
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									//add to the users balance
									//user data still exists
									$sql_query = "select * from geodesic_userdata where id = ".$show_transaction["user_id"];
									$user_data_result = $db->Execute($sql_query);
									if (!$user_data_result)								
									{
										$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
										$db->Close();
										exit;
									}
									else 
									{									
										//add to the users balance
										//user data still exists
										$user_data = $user_data_result->FetchRow();
										$new_balance = $user_data["account_balance"] + $show_transaction["payment_gross"];
										$sql_query = "update geodesic_userdata set
											account_balance = ".$new_balance."
											where id = ".$show_transaction["user_id"];
										$email_msg = "LINE ".__LINE__."\n";
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
										$update_balance_results = $db->Execute($sql_query);
										if (!$update_balance_results)
										{
											$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
											if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
											$db->Close();
											exit;
										}
										else
										{
											//account balance transaction completed successfully
											$email_msg = "LINE ".__LINE__."\n";
											if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
											$db->Close();
											exit;
										}
									}
								}
							}
							//STOREFRONT CODE
							elseif($show_transaction["subscription_renewal"] == 1 && $show_transaction["price_plan_id"] == -1)
							{
								$this->sql_query = "select * from geodesic_storefront_subscriptions_choices  
									where period_id = ".$show_transaction["renewal_choice"]."";
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
								$email_msg = "LINE ".__LINE__."\n";
								if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
								$price_plan_id_result = $db->Execute($sql_query);
								if ($price_plan_id_result->RecordCount() == 1)
								{
									$show_price_plan = $price_plan_id_result->FetchRow();
									$sql_query = "select * from geodesic_classifieds_subscription_choices where price_plan_id = ".$show_price_plan["price_plan_id"]."
										and period_id = ".$show_transaction["renewal_choice"]." order by value asc";
									$choices_result = $db->Execute($sql_query);
									$email_msg = "LINE ".__LINE__."\n";
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									if (!$choices_result)
									{
										$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
											$email_msg = "LINE ".__LINE__."\n";	
											if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}								
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
												$show_subscription = $check_subscriptions_results->FetchRow();
												if ($show_subscription["subscription_expire"] > $site->shifted_time($db))
													$new_expire = ($show_subscription["subscription_expire"] + ($show_choice["value"] * 86400));
												else
													$new_expire = ($site->shifted_time($db) + ($show_choice["value"] * 86400));
												$sql_query = "update geodesic_classifieds_user_subscriptions
													set subscription_expire = ".$new_expire."
													where subscription_id = ".$show_subscription["subscription_id"];
												$update_subscriptions_results = $db->Execute($sql_query);
												$email_msg = "LINE ".__LINE__."\n";	
												if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}											
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
												$new_expire = ($site->shifted_time($db) + ($show_choice["value"] * 86400));
												$sql_query = "insert into geodesic_classifieds_user_subscriptions
													(user_id,subscription_expire)
													values
													(".$show_transaction["user_id"].",".$new_expire.")";
												$insert_subscriptions_results = $db->Execute($sql_query);
												$email_msg = "LINE ".__LINE__."\n";	
												if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}										
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
							}
							elseif ($show_transaction["ad_placement"] == 1)
							{
								$sql_query = "select * from geodesic_classifieds where id = ".$show_transaction["id"];
								$duration_result = $db->Execute($sql_query);
								$email_msg = "LINE ".__LINE__."\n".$sql_query;
								if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
								if (!$duration_result)
								{
									$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									$db->Close();
									exit;
								}
								$show_duration = $duration_result->FetchRow();

								//when ad ends
								$length_of_ad = ($show_duration["duration"] * 86400);
								$current_time = $site->shifted_time($db);
								$email_msg = "LINE ".__LINE__."\n".$sql_query;
								if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
								if  ($site->configuration_data["admin_approves_all_ads"])
								{
									$sql_query = "update geodesic_classifieds set
										date = ".$current_time.",
										ends = ".($current_time + $length_of_ad)."
										where id = ".$show_transaction["id"];
								}
								else
								{
									$sql_query = "update geodesic_classifieds set
										live = 1,
										date = ".$current_time.",
										ends = ".($current_time + $length_of_ad)."
										where id = ".$show_transaction["id"];
									
									// IDev Affiliate payment
									if($site->configuration_data["idevaffiliate"] && $site->configuration_data["idev_upgrade"])
									{
										$idev_geoce_1 = $payment_gross;
										$idev_geoce_2 = "ad-".$show_transaction["id"];
										include($site->configuration_data["idev_path"].'sale.php');
										
										include("config.php");
										include("classes/adodb.inc.php");
								
										$db = &ADONewConnection('mysql');
								
										if (!$db->Connect($db_host, $db_username, $db_password, $database))
										{
											$email_msg = "LINE ".__LINE__."\t"."Could not reconnect to database\n";
											if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
											$db->Close();
											exit;
										}											
									}
								}
								$set_live_result = $db->Execute($sql_query);
								$email_msg = "LINE ".__LINE__."\n";
								if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
								if (!$set_live_result)
								{
									$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									$db->Close();
									exit;
								}

								$site->update_category_count($db,$show_duration["category"]);

								$site->sell_success_email($db,$show_transaction["id"]);

								//check to see if need to update subscription expirations
								$sql_query = "select * from geodesic_user_groups_price_plans
									where id = ".$show_duration["seller"];
								$email_msg = "LINE ".__LINE__."\n";
								if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
								$get_group_result = $db->Execute($sql_query);
								if (!$get_group_result)
								{
									$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									$db->Close();
									exit;
								}
								elseif ($get_group_result->RecordCount() == 1)
								{
									$email_msg = "LINE ".__LINE__."\n";
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									$show_group = $get_group_result->FetchRow();
									$price_plan = $site->get_price_plan_from_group($db,$show_group["group_id"]);
									if ($price_plan)
									{
										if ($price_plan["type_of_billing"] == 2)
										{
											$sql_query = "select * from geodesic_classifieds_user_subscriptions where subscription_expire > ".$site->shifted_time($db)." and user_id = ".$show_duration["seller"];
											$email_msg = "LINE ".__LINE__."\n";
											if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
											$get_subscriptions_results = $db->Execute($sql_query);
											if (!$get_subscriptions_results)
											{
												$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
												if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
												$db->Close();
												exit;
											}
											elseif ($get_subscriptions_results->RecordCount() == 0)
											{
												//push the subscription up
												$sql_query = "select * from geodesic_classifieds_subscription_choices where period_id = ".$show_duration["subscription_choice"];
												$email_msg = "LINE ".__LINE__."\n";
												if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
												$choice_result = $db->Execute($sql_query);
												if (!$choice_result)
												{
													$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
													if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
													$db->Close();
													exit;
												}
												elseif ($choice_result->RecordCount() == 1 )
												{
													$show_subscription_choice = $choice_result->FetchRow();
													$expiration = (($show_subscription_choice["value"] * 86400) + $site->shifted_time($db));
													$sql_query = "insert into geodesic_classifieds_user_subscriptions
														(user_id,subscription_expire)
														values
														(".$show_duration["seller"].",".$expiration.")";
													$email_msg = "LINE ".__LINE__."\n";
													if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
													$free_subscription_result = $db->Execute($sql_query);
													if (!$free_subscription_result)
													{
														$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
														if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
											$email_msg = "LINE ".__LINE__."\n";
											if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
											$credits_results = $db->Execute($sql_query);
											if (!$credits_results)
											{
												$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
												if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
													$email_msg = "LINE ".__LINE__."\n";
													if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
													$remove_credits_results = $db->Execute($sql_query);
													if (!$remove_credits_results)
													{
														$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
														if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
													$email_msg = "LINE ".__LINE__."\n";
													if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
													$remove_credit = $db->Execute($sql_query);
													if (!$remove_credit)
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
									else
									{
										$email_msg = "LINE ".__LINE__."\terror - no price plan\n";
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
										$db->Close();
										exit;
									}
								}
								else
								{
									$email_msg = "LINE ".__LINE__."\terror - wrong group count result\n";
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									$db->Close();
									exit;
								}
							}
							else
							{
								//do the renewal thing
								include_once("classes/site_class.php");
								$site = new Site($db,0,0,0);
								$sql_query = "select * from geodesic_classifieds where id = ".$show_transaction["id"];
								$classified_result = $db->Execute($sql_query);
								$email_msg = "LINE ".__LINE__."\n";
								if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
								if (!$classified_result)
								{
									$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									$db->Close();
									exit;
								}
								$show_classified = $classified_result->FetchRow();
								$email_msg = "LINE ".__LINE__."\n";
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
								if ($show_transaction["renewal_length"] > 0)
								{
									if ($show_classified["ends"] > $site->shifted_time($db))
										$new_expire = ($show_classified["ends"] + ($show_transaction["renewal_length"] * 86400));
									else
										$new_expire = ($site->shifted_time($db) + ($show_transaction["renewal_length"] * 86400));									
								}
								else 
								{
									$length_of_ad = ($show_classified["duration"] * 86400);
									$new_expire = ($site->shifted_time($db) + $length_of_ad);
								}
								if (($show_transaction["renew"] == 1) && ($show_transaction["renewal_length"] > 0))
								{
									if  ($site->configuration_data["admin_approves_all_ads"])
									{
										$sql_query = "update geodesic_classifieds set
											ends = \"".$new_expire."\",
											renewal_payment_expected = 0,
											date = \"".$site->shifted_time($db)."\",
											better_placement = \"0\",
											featured_ad = \"0\",
											bolding = \"0\",
											attention_getter = \"0\",
											featured_ad_2 = \"0\",
											featured_ad_3 = \"0\",
											featured_ad_4 = \"0\",
											featured_ad_5 = \"0\",
											expiration_notice = 0
											where id = ".$show_transaction["id"];
									}
									else 
									{
										$sql_query = "update geodesic_classifieds set
											ends = \"".$new_expire."\",
											live = 1,
											renewal_payment_expected = 0,
											date = \"".$site->shifted_time($db)."\",
											better_placement = \"0\",
											featured_ad = \"0\",
											bolding = \"0\",
											attention_getter = \"0\",
											featured_ad_2 = \"0\",
											featured_ad_3 = \"0\",
											featured_ad_4 = \"0\",
											featured_ad_5 = \"0\",
											expiration_notice = 0
											where id = ".$show_transaction["id"];										
									}
									$renew_result = $db->Execute($sql_query);
									$email_msg = "LINE ".__LINE__."\n";
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									if (!$renew_result)
									{
										$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}										
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
											$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
											if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
													$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
													if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
													$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
													if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
											$idev_geoce_2 = "ad-renewal-".$show_transaction["id"];
											include($site->configuration_data["idev_path"].'sale.php');
											
											include("config.php");
											include("classes/adodb.inc.php");
									
											$db = &ADONewConnection('mysql');
									
											if (!$db->Connect($db_host, $db_username, $db_password, $database))
											{
												$email_msg = "LINE ".__LINE__."\t"."Could not reconnect to database\n";
												if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
												$db->Close();
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
										$idev_geoce_2 = "ad-upgrade-".$show_transaction["id"];
										include($site->configuration_data["idev_path"].'sale.php');
										
										include("config.php");
										include("classes/adodb.inc.php");
								
										$db = &ADONewConnection('mysql');
								
										if (!$db->Connect($db_host, $db_username, $db_password, $database))
										{
											$email_msg = "LINE ".__LINE__."\t"."Could not reconnect to database\n";
											if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
											exit;
										}											
									}

									// Change end time
									if($sell->configuration_data["upgrade_time"])
									{
										$end_time = ($show_classified["ends"] - $show_classified["date"]) + $site->shifted_time($db);
										$site->sql_query = "update geodesic_classifieds set
												ends = ".$end_time." 
												where id = ".$show_transaction["id"];
										$end_result = $db->Execute($site->sql_query);
									}
								}

								if ($show_transaction["bolding"] == 1)
								{
									$sql_query = "update geodesic_classifieds set
										renewal_payment_expected = 0,
										ends = ".$new_expire.",
										bolding = 1
										where id = ".$show_transaction["id"];
									$renew_result = $db->Execute($sql_query);
									if (strlen($debug_email) > 0)
										@mail($debug_email,"post 14 - paypal",$sql_query);
									if (!$renew_result)
									{
										$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
										where id = ".$show_transaction["id"];
									$renew_result = $db->Execute($sql_query);
									$email_msg = "LINE ".__LINE__."\n";
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									if (!$renew_result)
									{
										$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
										where id = ".$show_transaction["id"];
									$renew_result = $db->Execute($sql_query);
									$email_msg = "LINE ".__LINE__."\n";
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									if (!$renew_result)
									{
										$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
										where id = ".$show_transaction["id"];
									$renew_result = $db->Execute($sql_query);
									if (!$renew_result)
									{
										$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
										where id = ".$show_transaction["id"];
									$renew_result = $db->Execute($sql_query);
									if (!$renew_result)
									{
										$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
										where id = ".$show_transaction["id"];
									$renew_result = $db->Execute($sql_query);
									if (!$renew_result)
									{
										$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
										where id = ".$show_transaction["id"];
									$renew_result = $db->Execute($sql_query);
									if (!$renew_result)
									{
										$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
										$db->Close();
										exit;
									}
								}


								if ($show_transaction["attention_getter"] == 1)
								{
									$sql_query = "select * from geodesic_choices where choice_id = ".$show_transaction["attention_getter_choice"];
									$attention_getter_result = $db->Execute($sql_query);
									$email_msg = "LINE ".__LINE__."\n";
									if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
									if (!$attention_getter_result)
									{
										$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
											where id = ".$show_transaction["id"];
										$renew_result = $db->Execute($sql_query);
										$email_msg = "LINE ".__LINE__."\n";
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
										if (!$renew_result)
										{
											$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
											if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
											$db->Close();
											exit;
										}
									}
									else
									{
										$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
										if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
										$db->Close();
										exit;
									}
								}
							}
						}
						else
						{
							$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
							if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
							$db->Close();
							exit;
						}
					}
  				}
  			}
  			else
  			{
  				//mail($debug_email,"7",$req);
  			}
  		}
  		elseif (strcmp($res, "INVALID") == 0)
  		{
			$email_msg = "LINE ".__LINE__."\tINVALID\n";
			if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
			// log for manual investigation
			include_once("config.php");
			include_once("classes/adodb.inc.php");
			$db = &ADONewConnection('mysql');
			if($persistent_connections)
			{
				//echo " Persistent Connection <bR>";
				if (!$db->PConnect($db_host, $db_username, $db_password, $database))
				{
					$email_msg = "LINE ".__LINE__."\t"."could not connect to database\n";
					if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
					$db->Close();
					exit;
				}
			}
			else
			{
				//echo " No Persistent Connection <bR>";
				if (!$db->Connect($db_host, $db_username, $db_password, $database))
				{
					$email_msg = "LINE ".__LINE__."\t"."could not connect to database\n";
					if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
					$db->Close();
					exit;
				}
			}
  			$sql_query = "select * from geodesic_paypal_transactions where id = \"".$invoice."\" and custom = \"".$custom."\"";
			$result = $db->Execute($sql_query);
			if (!$result)
			{
				$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
				if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
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
					$sql_query = "update geodesic_paypal_transactions set
						address_status = \"".$address_status."\",
						payment_status = \"".$payment_status."\",
						pending_reason = \"".$pending_reason."\",
						payment_date = \"".$payment_date."\",
						payment_fee = \"".$payment_fee."\",
						txn_id = \"".$txn_id."\",
						txn_type = \"".$txn_type."\",
						payer_status = \"".$payer_status."\",
						payment_type = \"".$payment_type."\",
						notify_version = \"".$notify_version."\",
						verify_sign = \"".$verify_sign."\"
						where id = ".$invoice;
					$result = $db->Execute($sql_query);
					if (!$result)
					{
						$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
						if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
						$db->Close();
						exit;
					}

					//send email to admin
					$sql_query = "select site_email from geodesic_classifieds_configuration";
					$configuration_result = $db->Execute($sql_query);
					if (!$configuration_result)
					{
						$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
						if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
						$db->Close();
						exit;
					}
					elseif ($configuration_result->RecordCount() == 1)
					{
						$show_configuration = $configuration_result->FetchRow();
						$subject = "paypal invalid transaction response";
						$body = "Bad paypal transaction information below:\n\r";

						$sql_query = "select * from geodesic_userdata where email = \"".$payer_email."\"";
						$user_result = $db->Execute($sql_query);
						if (!$user_result)
						{
							$email_msg = "LINE ".__LINE__."\nerror in query\n".$sql_query;
							if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
							$db->Close();
							exit;
						}
						elseif ($user_result->RecordCount() == 1)
						{
							$show_user_data = $user_result->FetchRow();
							$body .= "username: ".$show_user_data["username"]."\n\r";
						}
						$body .= "transaction_id: ".$invoice."\n\r";
						$body .= "classified_id: ".$show_transaction["id"]."\n\r";
						$body .= "address_status: ".$address_status."\n\r";
						$body .= "payment_status: ".$payment_status."\n\r";
						$body .= "pending_reason: ".$pending_reason."\n\r";
						$body .= "payment_date: ".$payment_date."\n\r";
						$body .= "payment_fee: ".$payment_fee."\n\r";
						$body .= "txn_id: ".$txn_id."\n\r";
						$body .= "txn_type: ".$txn_type."\n\r";
						$body .= "payer_status:".$payer_status."\n\r";
						$body .= "payment_type: ".$payment_type."\n\r";
						$body .= "notify_version: ".$notify_version."\n\r";
						$body .=  "verify_sign: ".$verify_sign."\n\r";

						$body .= "receiver_email: ".$show_transaction["receiver_email"]."\n\r";
						$body .= "item_name: ".$show_transaction["item_name"]."\n\r";
						$body .= "item_number: ".$show_transaction["item_number"]."\n\r";
						$body .= "quantity: ".$show_transaction["quantity"]."\n\r";
						$body .= "num_cart_items: ".$show_transaction["num_cart_items"]."\n\r";
						$body .= "first_name: ".$show_transaction["first_name"]."\n\r";
						$body .= "last_name: ".$show_transaction["last_name"]."\n\r";
						$body .= "address_street: ".$show_transaction["address_street"]."\n\r";
						$body .= "address_city:".$show_transaction["address_city"]."\n\r";
						$body .= "address_state: ".$show_transaction["address_state"]."\n\r";
						$body .= "address_zip: ".$show_transaction["address_zip"]."\n\r";
						$body .= "payer_email: ".$show_transaction["payer_email"]."\n\r";
						$body .= "payer_paypal_email: ".$show_transaction["payer_email_id"]."\n\r";
						$body .= "payer_id: ".$show_transaction["payer_id"]."\n\r";

						//mail($show_configuration["site_email"],$subject,$body);

					}
				}
   			}
  		}
  		/*$email_msg = "LINE ".__LINE__."\tnot VERIFIED nor INVALID\n";
		if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}*/
	}
	$email_msg = "LINE ".__LINE__."\n";
	if (strlen($debug_email)>0) {@mail($debug_email,"paypalipn.php",$email_msg);}
	fclose ($fp);
}

?>