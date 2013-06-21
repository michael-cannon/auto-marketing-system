<? //renew_subscriptions.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Renew_subscriptions extends Site {

	var $classified_user_id;
	var $classified_id;
	var $classified_data;
	var $category_id;
	var $price_plan_id = 0;
	var $price_plan;
	var $user_data;
	var $renew_upgrade_variables;
	var $session_id;
	var $user_credits;
	var $renew_upgrade;
	var $max_ads_reached;
	var $user_currently_subscribed;
	var $cost_of_subscription;
	var $subscription_renewal = 1;

	var $debug_subscription = 0;

//########################################################################

	function Renew_subscriptions($db,$classified_user_id,$language_id,$session_id=0,$product_configuration=0)
	{
		$this->Site($db,1,$language_id,$classified_user_id,$product_configuration);
		$this->classified_user_id = $classified_user_id;
		$this->session_id = $session_id;
		if ($this->classified_user_id)
		{
			$this->user_data = $this->get_user_data($db,$this->classified_user_id);
			$this->check_user_subscription($db);
			if (!$this->price_plan_id)
			{
				$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
				if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
				$price_plan_id_result = $db->Execute($this->sql_query);
				if ($price_plan_id_result->RecordCount() == 1)
				{
					$show_price_plan = $price_plan_id_result->FetchRow();
					$this->price_plan_id = $show_price_plan["price_plan_id"];
				}
			}
		}
		else
		{
			return false;
		}
		$this->get_price_plan($db);

		//this is an old session...restart it
		$this->sql_query = "select * from ".$this->sell_table." where session = \"".$this->session_id."\" and renew_upgrade = 1";
		$setup_sell_result = $db->Execute($this->sql_query);
		if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
		if (!$setup_sell_result)
		{
			if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
			$this->setup_error = $this->messages[453];
			return false;
		}
		elseif ($setup_sell_result->RecordCount() == 1)
		{
			$show = $setup_sell_result->FetchNextObject();
			$this->renew_subscription_variables["subscription_choice"] = $show->SUBSCRIPTION_CHOICE;
			$this->renew_subscription_variables["payment_type"] = $show->PAYMENT_TYPE;
			$this->renew_subscription_variables["cc_number"] = Site::decrypt($show->CC_NUMBER, $show->DECRYPTION_KEY);
			$this->renew_subscription_variables["decryption_key"] = 0;
			$this->renew_subscription_variables["cc_exp_year"] = $show->CC_EXP_YEAR;
			$this->renew_subscription_variables["cc_exp_month"] = $show->CC_EXP_MONTH;
			$this->renew_subscription_variables["cvv2_code"] = $show->CVV2_CODE;
		}
		else
		{
			//incase there are more than one
			//$this->sql_query = "delete from ".$this->sell_table." where session = \"".$this->session_id."\" and renew_upgrade = 1";
			//$setup_sell_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			//if (!$setup_sell_result)
			//{
			//	//$this->body .=$this->sql_query."<bR>\n";
			//	$this->setup_error = $this->messages[453];
			//	return false;
			//}
			//sell session data not there...start over
			//start the new sell session
			$this->sql_query = "insert into ".$this->sell_table."
				(session,time_started,renew_upgrade)
				values
				(\"".$this->session_id."\",".$this->shifted_time($db).",1)";
			$insert_sell_result = $db->Execute($this->sql_query);
			if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
			if (!$insert_sell_result)
			{
				if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
				return false;
			}

		}

		//echo $this->renew_upgrade." is renew_upgrade<br>\n";
		//echo $this->price_plan." is price_plan<br>\n";
		//echo $this->price_plan_id." is price_plan_id<br>\n";
		//echo $this->classified_data." is classified_data<br>\n";
		//echo $this->category_id." is category_id<br>\n";
		//echo $this->classified_id." is classified_id<br>\n";
		//echo $this->classified_user_id." is classified_user_id<br>\n";
	} //end of function Renew_upgrade_sellers_ads

//###########################################################

	function get_renew_subscription_variables($info)
	{
		if (is_array($info))
		{
			reset ($info);
			foreach ($info as $key => $value)
			{
				$this->renew_subscription_variables[$key] = $value;
				//echo $key." is to ".$value."<bR>\n";
			}
		}

	} //end of get_renew_subscription_variables

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function save_renew_subscription_variables ($db)
	{
		$unique_key = substr(md5(uniqid(rand(),1)), 0,strlen($this->renew_subscription_variables["cc_number"]));
		$encrypted_cc_num = Site::encrypt($this->renew_subscription_variables["cc_number"], $unique_key);
		$this->sql_query = "update ".$this->sell_table." set
			payment_type = \"".$this->renew_subscription_variables["payment_type"]."\",
			cc_number = \"$encrypted_cc_num\",
			decryption_key = \"$unique_key\",
			cc_exp_year = \"".$this->renew_subscription_variables["cc_exp_year"]."\",
			cc_exp_month = \"".$this->renew_subscription_variables["cc_exp_month"]."\",
			cvv2_code = \"".$this->renew_subscription_variables["cvv2_code"]."\",
			subscription_choice = \"".$this->renew_subscription_variables["subscription_choice"]."\"
			where session = \"".$this->session_id."\"";
		$save_variable_result = $db->Execute($this->sql_query);
		if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";

		//STOREFRONT CODE
		if($this->renewStorefrontSubscription)
		{
			$this->sql_query = "update ".$this->sell_table." set
			storefront = 1
			where session = \"".$this->session_id."\"";
			$save_variable_result = $db->Execute($this->sql_query);
		}
		//STOREFRONT CODE

		if (!$save_variable_result)
		{
			if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
			return false;
		}
	} //end of function save_renew_subscription_variables

//####################################################################

	function remove_renew_subscription_session($db,$sell_session=0)
	{
		if ($sell_session)
		{
			$this->sql_query = "delete from ".$this->sell_table." where session = \"".$sell_session."\" and renew_upgrade = 1";
			if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
			$delete_sell_result = $db->Execute($this->sql_query);
			if (!$delete_sell_result)
			{
				if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
				return false;
			}
			return true;
		}
		else
			return false;
	} //end of funciton remove_sell_session

//####################################################################

	function subscription_renewal_form($db)
	{
		$this->page_id = 107;
		$this->get_text($db);
		//echo "hello from almost top of upgrade form<Br>\n";
		//get this users price plan specifics
		//echo $this->price_plan_id." is price_plan_id<br>\n";
		//echo $this->price_plan->TYPE_OF_BILLING." is price_plan type in renewal form<br>\n";
		//echo $this->classified_user_id." is classified_user_id in renewal form<br>\n";
		if ($this->price_plan->TYPE_OF_BILLING == 1)
		{
			$this->body .="<table cellpadding=0 cellspacing=1 border=0 align=center width=100%>\n";
			$this->body .="<tr class=page_title>\n\t<td  colspan=3>".urldecode($this->messages[792])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description  colspan=3>\n\t<td colspan=3>".urldecode($this->messages[793])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td class=error_message colspan=3>".urldecode($this->messages[1680])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td class=back_to_my_info_link><a href=".$this->configuration_data['classifieds_url']."?a=4&b=3>".urldecode($this->messages[1648])."</a></td>\n</tr>\n";
			$this->body .="</table>\n";
			$this->display_page($db);
			return true;

		}
		elseif (($this->price_plan->TYPE_OF_BILLING == 2) && ($this->classified_user_id))
		{
			$this->body .="<table cellpadding=0 cellspacing=1 border=0 align=center width=100%>\n";
			$this->body .="<tr class=page_title>\n\t<td  colspan=3>".urldecode($this->messages[792])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description  colspan=3>\n\t<td colspan=3>".urldecode($this->messages[793])."</td>\n</tr>\n";

			if (!$this->user_currently_subscribed)
			{
				$this->body .="<tr>\n\t<td class=error_message colspan=3>".urldecode($this->messages[1625])."</td>\n</tr>\n";
			}
			else
			{
				$this->body .="<tr>\n\t<td class=error_message colspan=3>".urldecode($this->messages[1625])."</td>\n</tr>\n";
			}

			$payment_types_accepted = $this->get_payment_types_accepted($db);
			if (($this->price_plan) && ($this->user_data) && ($payment_types_accepted))
			{
				$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=24 method=post>\n";
				$this->body .="<table cellpadding=0 cellspacing=1 border=0 align=center width=100%>\n";
				$this->body .="<tr class=page_title>\n\t<td  colspan=3>".urldecode($this->messages[1628])."</td>\n</tr>\n";
				$this->body .="<tr class=page_description  colspan=3>\n\t<td colspan=3>".urldecode($this->messages[1629])."</td>\n</tr>\n";
				if (strlen($this->error_variables["choose"]) > 0)
					$this->body .="<tr>\n\t<td class=error_message colspan=3>".$this->error_variables["choose"]."</td>\n</tr>\n";

				//get subscription choices
				$this->body .="<tr class=subscription_renewal_section_title  colspan=3>\n\t<td colspan=3>".urldecode($this->messages[1630])."</td>\n</tr>\n";

				$this->body .= $this->show_subscription_choices_table($db);

				//PAY CHOICE(S)
				$this->body .="<tr class=subscription_renewal_section_title>\n\t<td colspan=3>".urldecode($this->messages[1631])."</td>\n</tr>\n";
				$this->body .= "<tr><td colspan=3>\n\t";
				$this->body .="<table cellpadding=3 cellspacing=0 border=1 align=center width=100%>\n\t";
				if ($this->error_variables["payment_type"])
				{
					$this->body .="<tr class=page_description >\n\t<td colspan=3>";
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1632])."</font>";
				}
				$this->body .="</td>\n</tr>\n";
				while ($show_payment = $payment_types_accepted->FetchNextObject())
				{
					switch ($show_payment->TYPE)
					{
						case 1:
						{
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
								($this->configuration_data['site_balance_override'] == 0)) ||
								(!$this->configuration_data['use_account_balance']) ||
								(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
							{
								//cash
								$this->body .="<tr>\n\t\t<td width=50%><font class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[1633])."</font><br>
								<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[1634])."</td>\n\t";
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .="<td width=50% colspan=2 valign=top class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] checked value=1>&nbsp;</td>\n\t</tr>\n\t";
								}
								else
								{
									//this is a choice among many
									$this->body .="<td width=50% colspan=2 valign=top class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] value=1 ";
									if ($this->renew_subscription_variables["payment_type"] == 1) $this->body .="checked";
									$this->body .="></td>\n\t</tr>\n\t";
								}
							}
						}
						break;
						case 2:
						{
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
								($this->configuration_data['site_balance_override'] == 0)) ||
								(!$this->configuration_data['use_account_balance']) ||
								(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
							{
								//credit card
								$this->sql_query = "select * from ".$this->cc_choices." where chosen_cc = 1";
								$cc_result = $db->Execute($this->sql_query);
								if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
								if (!$cc_result)
								{
									if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
									$this->setup_error = $this->messages[453];
									return false;
								}
								elseif ($cc_result->RecordCount() == 1)
								{
									$show_cc_choice = $cc_result->FetchNextObject();
								}
								$this->body .="<tr>\n\t\t<td  width=50%><font class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[1635])."</font><br>
								<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[1636])."</td>\n\t";
								if (($show_cc_choice->CC_ID == 1) || ($show_cc_choice->CC_ID == 3) || ($show_cc_choice->CC_ID == 4) || ($show_cc_choice->CC_ID >= 6))
								{
									if ($payment_types_accepted->RecordCount() == 1)
									{
										//this is the only choice so has hidden variable saying this is the type requested
										$this->body .="<td width=10% class=subscription_renewal_payment_choices_labels ><input type=radio name=c[payment_type] checked value=2>&nbsp;</td>\n\t<td width=40% align=center>\n\t";
									}
									else
									{
										//this is a choice among many
										$this->body .="<td width=10% class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] value=2 ";
										if ($this->renew_subscription_variables["payment_type"] == 2) $this->body .="checked";
										$this->body .="></td>\n\t<td width=40% align=center class=subscription_renewal_payment_choices_cc_number_val>\n\t";
									}
									$this->body .="
										<table cellpadding=3 cellspacing=1 border=0 align=center width=100% class=payment_choices_cc_number_data_values>
											<tr>
												<td width=50% align=right>";
									if ($this->error_variables["cc_number"])
										$this->body .="
													<font class=error_message>".$this->error_variables["cc_number"]."</font>";
									if ($this->error_variables["cc_expiration"])
										$this->body .="
													<font class=error_message>".$this->error_variables["cc_expiration"]."</font>";
									$this->body .= urldecode($this->messages[1637]);
									$this->body .="
												</td>
												<td width=50% align=left>
													<input type=text name=c[cc_number] size=20><br>
												</td>
											</tr>";
									if ($show_cc_choice->CC_ID==7  || $show_cc_choice->CC_ID==9)
									{
										$this->body .="
											<tr>
												<td width=50% align=right>";
										$this->body .= "
													<a href=\"javascript:winimage('./images/cvv2_code.gif',500,200)\">".
													urldecode($this->messages[200107])."</a>";
										$this->body .= "
												</td>
												<td width=50% align=left>
													<input type=text name=c[cvv2_code] size=4>
												</td>
											</tr>";
									}
									$this->body .="
											<tr>
												<td width=50% align=right>".urldecode($this->messages[1638])."</td>
												<td width=50% align=left>";
									$this->body .= "<select name=c[cc_exp_month]>";
									for ($i=1;$i<13;$i++)
									{
										$this->body .="
														<option>".sprintf("%02d",$i)."</option>";
									}
									$this->body .= "</select>";
									$this->display_year_dropdown("c[cc_exp_year]");
									$this->body .= "
												</td>
											</tr>
										</table>";
								}
								elseif ($show_cc_choice->CC_ID == 2)
								{
									if ($payment_types_accepted->RecordCount() == 1)
									{
										//this is the only choice so has hidden variable saying this is the type requested
										$this->body .= "<td colspan=2 width=50% valign=top  class=subscription_renewal_payment_choices_labels>
										<input type=radio name=c[payment_type] checked value=2>&nbsp;";
									}
									else
									{
										//this is a choice among many
										$this->body .= "<td colspan=2 width=50% valign=top class=subscription_renewal_payment_choices_labels>
										<input type=radio name=c[payment_type] value=2 ";
										if ($this->classified_variables["payment_type"] == 2) $this->body .= "checked";
										$this->body .= ">";
									}
								}
								$this->body .="</td>\n\t</tr>\n\t";
							}
						}
						break;
						case 3:
						{
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
								($this->configuration_data['site_balance_override'] == 0)) ||
								(!$this->configuration_data['use_account_balance']) ||
								(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
							{
								//paypal
								$this->body .="<tr>\n\t\t<td  width=50%><font class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[1639])."</font><br>
								<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[1640])."</td>\n\t";
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .="<td width=50% colspan=2 class=subscription_renewal_payment_choices_labels><input type=radio checked name=c[payment_type] value=3>&nbsp;</td>\n\t</tr>\n\t";
								}
								else
								{
									//this is a choice among many
									$this->body .="<td width=50% colspan=2 class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] value=3 ";
									if ($this->renew_subscription_variables["payment_type"] == 3) $this->body .="checked";
									$this->body .="></td>\n\t</tr>\n\t";
								}
							}
						}
						break;
						case 4:
						{
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
								($this->configuration_data['site_balance_override'] == 0)) ||
								(!$this->configuration_data['use_account_balance']) ||
								(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
							{
								//money order
								$this->body .="<tr>\n\t\t<td width=50%><font class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[1641])."</font><br>
								<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[1642])."</td>\n\t";
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .="<td width=50% colspan=2 valign=top class=subscription_renewal_payment_choices_labels><input type=radio checked name=c[payment_type] value=4>&nbsp;</td>\n\t</tr>\n\t";
								}
								else
								{
									//this is a choice among many
									$this->body .="<td width=50% colspan=2 valign=top class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] value=4 ";
									if ($this->renew_subscription_variables["payment_type"] == 4) $this->body .="checked";
									$this->body .="></td>\n\t</tr>\n\t";
								}
							}
						}
						break;
						case 5:
						{
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
								($this->configuration_data['site_balance_override'] == 0)) ||
								(!$this->configuration_data['use_account_balance']) ||
								(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
							{
								//check
								$this->body .="<tr>\n\t\t<td width=50%><font class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[1643])."</font><br>
								<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[1644])."</td>\n\t";
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .="<td  width=50% colspan=2 valign=top class=subscription_renewal_payment_choices_labels><input type=radio checked name=c[payment_type] value=5>&nbsp;</td>\n\t</tr>\n\t";
								}
								else
								{
									//this is a choice among many
									$this->body .="<td  width=50% colspan=2 valign=top class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] value=5 ";
									if ($this->renew_subscription_variables["payment_type"] == 5) $this->body .="checked";
									$this->body .="></td>\n\t</tr>\n\t";
								}
							}
						}
						break;
						case 6:
						{
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
								($this->configuration_data['site_balance_override'] == 0)) ||
								(!$this->configuration_data['use_account_balance']) ||
								(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
							{
								//worldpay
								$this->body .="<tr>\n\t\t<td  width=50%><font class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[1645])."</font><br>
								<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[1646])."</td>\n\t";
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .="<td width=50% colspan=2 class=subscription_renewal_payment_choices_labels><input type=radio checked name=c[payment_type] value=6>&nbsp;</td>\n\t</tr>\n\t";
								}
								else
								{
									//this is a choice among many
									$this->body .="<td width=50% colspan=2 class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] value=6 ";
									if ($this->renew_subscription_variables["payment_type"] == 6) $this->body .="checked";
									$this->body .="></td>\n\t</tr>\n\t";
								}
							}
						}
						break;

						case 7: //site balance
						{
							if ($this->debug_subscription)
							{
								echo $this->configuration_data['use_account_balance']." is USE_ACCOUNT_BALANCE<br>\n";
								echo $this->configuration_data['positive_balances_only']." is POSITIVE_BALANCES_ONLY<br>\n";
								echo $this->user_data->ACCOUNT_BALANCE." is the ACCOUNT_BALANCE<br>\n";
							}
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) && ($this->user_data->ACCOUNT_BALANCE > 0))
								|| (($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
							{
								if ($this->debug_subscription) echo "displaying account balance option<br>\n";
								//will check to see if balance is enough to pay for within transaction detail check

								if (!$this->configuration_data['positive_balances_only'])
								{
									$this->body .="<tr>\n\t\t<td class=subscription_renewal_payment_choices_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[3247])."</font><br>
										<font class=subscription_renewal_payment_choices_descriptions>";
									$this->body .= urldecode($this->messages[3248])."<br>".urldecode($this->messages[3249]);

									//get current accumulated but unbilled charges
									$this->sql_query = "select * from ".$this->balance_transactions." where (auction_id != 0 or ad_id != 0 or subscription_renewal != 0) and cc_transaction_id = 0 and invoice_id = 0 and user_id = ".$this->classified_user_id;
									if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
									$invoice_total_result = $db->Execute($this->sql_query);
									if (!$invoice_total_result)
									{
										if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
										return false;
									}
									elseif ($invoice_total_result->RecordCount() > 0)
									{
										$to_be_invoiced = 0;
										while ($show_invoices = $invoice_total_result->FetchNextObject())
										{
											$to_be_invoiced = $to_be_invoiced + $show_invoices->AMOUNT;
										}
										$this->body .= " ".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$to_be_invoiced)." ".$this->configuration_data['postcurrency'];
									}
									else
									{
										$this->body .= " ".$this->configuration_data['precurrency']." ".sprintf("%0.02f",0)." ".$this->configuration_data['postcurrency'];
									}
									$this->body .= "</td>\n\t";
								}
								else
								{
									$this->body .="<tr>\n\t\t<td class=subscription_renewal_payment_choices_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[3250])."</font><br>
										<font class=subscription_renewal_payment_choices_descriptions><br>".
										urldecode($this->messages[3251])."<br><br>".urldecode($this->messages[3252])." ".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->user_data->ACCOUNT_BALANCE)." ".$this->configuration_data['postcurrency']."<br>\n\t";
									$this->body .= "</td>\n\t";
								}
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] ";
								if ($this->renew_upgrade_variables["payment_type"] == 7)
									$this->body .="checked";
								elseif ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
								($this->configuration_data['site_balance_override'] == 0)) ||
								(!$this->configuration_data['use_account_balance']) ||
								(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
									$this->body .= "checked";
								$this->body .= " value=7>&nbsp;</td>\n\t</tr>\n\t";
							}
							elseif (($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) && ($this->user_data->ACCOUNT_BALANCE == 0))
							{
								//the client does not have enough in their account to pay for the subscription
								$this->body .="<tr>\n\t\t<td class=subscription_renewal_payment_choices_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[3250])."</font><br>
									<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[3251])."</td>";
								$this->body .="<td width=50% colspan=2 valign=top class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[3253])."</td></tr>";
							}
						}
						break;
						case 8:
						{
							//NOCHEX
							/*if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
								($this->configuration_data['site_balance_override'] == 0)) ||
								(!$this->configuration_data['use_account_balance']) ||
								(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
							{
								//worldpay
								$this->body .="<tr>\n\t\t<td  width=50%><font class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[])."</font><br>
								<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[])."</td>\n\t";
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .="<td width=50% colspan=2 class=subscription_renewal_payment_choices_labels><input type=radio checked name=c[payment_type] value=6>&nbsp;</td>\n\t</tr>\n\t";
								}
								else
								{
									//this is a choice among many
									$this->body .="<td width=50% colspan=2 class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] value=8 ";
									if ($this->renew_subscription_variables["payment_type"] == 8) $this->body .="checked";
									$this->body .="></td>\n\t</tr>\n\t";
								}
							}*/
						}
						break;
					}
				}
				$this->body .="</table>\n\t</td>\n</tr>\n";
				//$this->body .="\n\t</td>\n</tr>\n";
			}
			$this->body .="<tr>\n\t<td align=center colspan=2 class=sumbit_button>\n\t<br><input type=submit name=z class=submit_button value=\"".urldecode($this->messages[1647])."\">\n\t</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td class=back_to_my_info_link><a href=".$this->configuration_data['classifieds_url']."?a=4&b=3>".urldecode($this->messages[1648])."</a></td>\n</tr>\n";
			$this->body .="</table>\n";
			$this->body .="</form>\n";

			$this->display_page($db);
			return true;
		}
		else
		{
			//this is not a subscription based plan
			return false;
		}

	} //end of function classified_upgrade_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_transaction_variables($db)
	{
		$this->page_id = 107;
		$this->get_text($db);
		$this->error = 0;
		unset($this->error_variables);
		$this->error_variables = array();
		//check to see if chosen and cost of renewal

		$this->get_cost_of_subscription($db);
		//$this->body .=$this->cost_of_subscription." is the total<br>\n";
		//echo $this->subtotal." is the subtotal in check<br>\n";
		//echo $this->renew_subscription_variables["subscription_choice"]." is sub choice in check<BR>\n";
		if ($this->renew_subscription_variables["subscription_choice"] == 0)
		{
			//some action must be taken
			$this->error++;
			$this->error_variables["choose"] = urldecode($this->messages[1675]);
		}

		if ($this->subtotal != 0)
		{
			if (!$this->renew_subscription_variables["payment_type"])
			{
				$this->error++;
				$this->error_variables["payment_type"] = urldecode($this->messages[1676]);
			}
			if ($this->renew_subscription_variables["payment_type"] == 2)
			{
				//cc_number
				//put verification script in
				$this->sql_query = "select cc_id from ".$this->cc_choices." where chosen_cc = 1";
				if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
				$cc_choice_result = $db->Execute($this->sql_query);
				if (!$cc_choice_result)
				{
					if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
					return false;
				}
				elseif ($cc_choice_result->RecordCount() == 1)
				{
					$show_cc_choice = $cc_choice_result->FetchNextObject();
					if (($show_cc_choice->CC_ID == 1) || ($show_cc_choice->CC_ID == 3) || ($show_cc_choice->CC_ID == 4) || ($show_cc_choice->CC_ID >= 6))
					{
						if (strlen(trim($this->renew_subscription_variables["cc_number"])) == 0)
						{
							$this->error++;
							$this->error_variables["cc_number"] = urldecode($this->messages[1677]);
						}

						//check date of expiration
						$current_year = date("y");
						if (($this->renew_subscription_variables["cc_exp_year"] < $current_year) || (($this->renew_subscription_variables["cc_exp_year"] == $current_year)
							&& ($this->renew_subscription_variables["cc_exp_month"] < date("m"))))
						{
							$this->error++;
							$this->error_variables["cc_expiration"] = urldecode($this->messages[1678]);
						}
					}
				}
				else
					return false;
			}

			if ($this->renew_subscription_variables["payment_type"] == 7)
			{
				if (($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']))
				{
					if ($this->debug_subscription)
					{
						echo "checking account type payment choice<bR>\n";
						echo $this->subtotal." is the subtotal<bR>\n";
						echo $this->classified_user_id." is the classified_user_id used<Br>\n";
						echo $this->user_data->ACCOUNT_BALANCE." is the account_balance<bR>\n";
					}
					//account balance payment type

					if ($this->subtotal > $this->user_data->ACCOUNT_BALANCE)
					{
						$this->error++;
						$this->error_variables["account_balance"] = urldecode($this->messages[3253]);
					}
				}
				else
				{
					//this is an invoice transaction
					if ($this->debug_subscription)
					{
						echo "this transaction will be invoiced<bR>\n";
					}
				}
			}
		}

		if ($this->error == 0)
			return true;
		else
			return false;
	} //end of function check_transaction_variables

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function final_approval_form($db)
	{
		$this->page_id = 108;
		$this->get_text($db);

		//save transaction data
		//save the transaction data overwriting
		//update transaction data into the classifieds table


		//get totals and taxes if any
		if (($this->price_plan->TYPE_OF_BILLING == 2) && ($this->classified_user_id))
		{
			$user_data = $this->get_user_data($db);
			$this->tax = $this->get_tax($db,$user_data);
			$this->total = $this->subtotal + $this->tax;

			//check to see if the early days of the ad so can upgrade
			$this->body .= "<form action=".$this->configuration_data['classifieds_file_name']."?a=24&d=final_accepted method=post>\n";
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
			$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[1652])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[1653])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td>";
			if ($this->total != 0)
			{
				$user_data = $this->user_data;
				//check pay choice information
				switch ($this->renew_subscription_variables["payment_type"])
				{
					case 1: //cash
						$this->body .= urldecode($this->messages[1654]);
					break;
					case 2: //credit card
						$this->body .= urldecode($this->messages[1655]);
					break;
					case 3: //paypal
						$this->body .= urldecode($this->messages[1656]);
					break;
					case 4: //money order
						$this->body .= urldecode($this->messages[1657]);
					break;
					case 5: //check
						$this->body .= urldecode($this->messages[1658]);
					break;
					case 6: //worldpay
						$this->body .= urldecode($this->messages[1659]);
					break;
					case 7: //invoicing/site balance
						$this->body .= urldecode($this->messages[3246]);
					break;
					case 8: //invoicing/NOCHEX
						//$this->body .= urldecode($this->messages[3246]);
					break;
					default:
						return false;
				} //end of switch ($this->renew_subscription_variables["payment_type"])
			}
			$this->body .= $payment_type_message."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td align=center>\n\t<table cellpadding=3 cellspacing=1>\n\t";
			$this->body .="<tr class=subscription_renewal_transaction_totals_header>\n\t<td colspan=2>".urldecode($this->messages[1660])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td  class=subscription_renewal_left_column_header>".urldecode($this->messages[1661])."</td>\n\t";
			$this->body .="<td class=subscription_renewal_right_column_header>".urldecode($this->messages[1662])."</td>\n</tr>\n";

			//show transaction totals
			//cost of subscription
			$this->body .="<tr>\n\t<td  width=50% class=subscription_renewal_left_column>";
			$this->body .=urldecode($this->messages[1663])."</td>\n\t";
			$this->body .="<td class=subscription_renewal_right_column>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->cost_of_subscription)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			//tax
			$this->body .="<tr class=subscription_renewal_left_column >\n\t<td >
				".urldecode($this->messages[1664])."</td>\n\t
				<td class=subscription_renewal_right_column>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->tax)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
			//total
			$this->body .="<tr class=subscription_renewal_left_column>\n\t<td >
				".urldecode($this->messages[1665])."</td>\n\t
				<td class=subscription_renewal_right_column>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->total)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			$this->body .="</table>\n\t</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td align=center class=submit_button><input type=submit name=z value=\"".urldecode($this->messages[1666])."\" class=submit_button>\n\t</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td class=back_to_my_info_link colspan=2><a href=".trim($this->configuration_data['classifieds_url'])."?a=24>".urldecode($this->messages[1667])."</a><br>";
			$this->body .="<tr>\n\t<td class=back_to_my_info_link colspan=2><a href=".trim($this->configuration_data['classifieds_url'])."?a=4&b=3>".urldecode($this->messages[1668])."</a><br>";
			$this->body .="</td>\n</tr>\n";

			$this->body .="</table>\n";
			$this->body .="</form>\n";
			$this->display_page($db);
			return true;
		}
		else
		{
			//cannot upgrade or renew now
			return false;
		}
	} //end of function final_approval_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function transaction_approved($db)
	{
		$this->get_cost_of_subscription($db);
		$user_data = $this->get_user_data($db);
		$this->tax = $this->get_tax($db,$user_data);
		$this->total = $this->subtotal + $this->tax;
		if ($this->total == 0)
		{
			//there is no charge to place this ad
			//make it live now
			$this->do_free_subscription_renewals($db);
		}
		else
		{
			switch ($this->renew_subscription_variables["payment_type"])
			{
				case 1:
				{
					//cash
					//administrator must open classified ad
					//check if instant renewal or placed on hold
					//place in renewal transactions expecting funds or on hold
					$this->page_id = 109;
					$this->get_text($db);
					$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
					$this->body .= "<tr class=page_title><td>".urldecode($this->messages[1669])."</td>\n</tr>\n";
					$this->body .= "<tr class=page_description><td>".urldecode($this->messages[1670])."</td>\n</tr>\n";

					//display message saying the ad has not been renewed until payment
					$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[1671])."</td>\n</tr>\n";

					$this->remove_renew_subscription_session($db,$this->session_id);
					$this->body .="<tr class=back_to_my_info_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_file_name']."?a=4&b=3\">";
					$this->body .=urldecode($this->messages[1672])."</A>\n\t</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->update_subscription_renewal_payment_expected($db);
					$this->display_page($db);
					exit;

				}
				break;
				case 2:
				{
					//credit card
					//each credit card processor will have its own transaction handler
					//find the right credit card processor
					$user_data = $this->get_user_data($db,$this->classified_user_id);
					$subscription_renewal = 1;
					$cc = $this->get_cc($db);
					if ($cc)
					{
						include($cc->CC_INITIATE_FILE);
						return false;
						exit;
					}
					else
					{
						return true;
					}
				}
				break;
				case 3:
				{
					//paypal
					//paypal will have a separate final transaction handler that opens the classified ad
					//get unique verifier for paypal 'custom' field
					$subscription_renewal = 1;
					include("paypal_initiate.php");
				}
				break;
				case 4:
				{
					//money order
					//administrator must open classified ad
					//check if instant renewal or placed on hold
					$this->page_id = 109;
					$this->get_text($db);
					$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
					$this->body .= "<tr class=page_title><td>".urldecode($this->messages[1669])."</td>\n</tr>\n";
					$this->body .= "<tr class=page_description><td>".urldecode($this->messages[1670])."</td>\n</tr>\n";

					//display message saying the ad has not been renewed until payment
					$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[1673])."</td>\n</tr>\n";

					$this->remove_renew_subscription_session($db,$this->session_id);
					$this->body .="<tr class=back_to_my_info_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_file_name']."?a=4&b=3\">";
					$this->body .=urldecode($this->messages[1672])."</A>\n\t</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->update_subscription_renewal_payment_expected($db);
					$this->display_page($db);
					exit;
				}
				break;
				case 5:
				{
					//check
					//administrator must open classified ad
					//check if instant renewal or placed on hold
					$this->page_id = 109;
					$this->get_text($db);
					$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
					$this->body .= "<tr class=page_title><td>".urldecode($this->messages[1669])."</td>\n</tr>\n";
					$this->body .= "<tr class=page_description><td>".urldecode($this->messages[1670])."</td>\n</tr>\n";

					//display message saying the ad has not been renewed until payment
					$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[1674])."</td>\n</tr>\n";

					$this->remove_renew_subscription_session($db,$this->session_id);
					$this->body .="<tr class=back_to_my_info_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_file_name']."?a=4&b=3\">";
					$this->body .=urldecode($this->messages[1672])."</A>\n\t</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->update_subscription_renewal_payment_expected($db);
					$this->display_page($db);
					exit;
				}
				case 6:
				{
					//worldpay
					$subscription_renewal = 1;
					include("initiate_worldpay.php");
					return true;
				}
				break;
				case 7:
				{
					//site balance / invoice payment choice
					$this->page_id = 109;
					$this->get_text($db);
					$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
					$this->body .= "<tr class=page_title><td>".urldecode($this->messages[1669])."</td>\n</tr>\n";
					$this->body .= "<tr class=page_description><td>".urldecode($this->messages[1670])."</td>\n</tr>\n";

					if (($this->configuration_data['positive_balances_only']) && ($this->configuration_data['use_account_balance']))
					{
						$new_balance = $this->user_data->ACCOUNT_BALANCE - $this->total;
						$this->sql_query = "update ".$this->userdata_table." set
							account_balance = ".$new_balance."
							where id = ".$this->classified_user_id;
						if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
						$update_balance_results = $db->Execute($this->sql_query);
						if (!$update_balance_results)
						{
							if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
							return false;
						}

						$this->sql_query = "insert into ".$this->balance_transactions."
							(user_id,subscription_renewal,amount,date,cc_transaction_id,invoice_id)
							values
							(".$this->classified_user_id.",1,".$this->total.",".$this->shifted_time($db).",999999999,999999999)";
						if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
						$insert_invoice_item_result = $db->Execute($this->sql_query);
						if (!$insert_invoice_item_result)
						{
							if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
							return false;
						}
						$this->body .="<tr>\n\t<td class=success_failure_message >".urldecode($this->messages[3254])."</td>\n</tr>\n";
					}
					elseif ((!$this->configuration_data['positive_balances_only']) && ($this->configuration_data['use_account_balance']))
					{
						$this->sql_query = "insert into ".$this->balance_transactions."
							(user_id,subscription_renewal,amount,date)
							values
							(".$this->classified_user_id.",1,".$this->total.",".$this->shifted_time($db).")";
						if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
						$insert_invoice_item_result = $db->Execute($this->sql_query);
						if (!$insert_invoice_item_result)
						{
							if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
							return false;
						}
						$this->body .="<tr>\n\t<td class=success_failure_message >".urldecode($this->messages[3255])."</td>\n</tr>\n";
					}

					$this->do_subscription_renewal($db);
					//display message that ad has been renewed and will be billed to account balance or invoiced

					$this->remove_renew_subscription_session($db,$this->session_id);
					$this->body .="<tr class=back_to_my_info_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_file_name']."?a=4&b=3\" class=back_to_my_info_link >";
					$this->body .=urldecode($this->messages[1672])."</A>\n\t</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->display_page($db);
					exit;
				}
				break;
				case 8:
				{
					//NOCHEX
					include("nochex_initiate.php");
					return true;
				}
				break;
				default:
					//$this->body .="got to default<br>";
					return false;
			} //end of switch ($this->renew_subscription_variables["payment_type"])
		}
		return true;
	} //end of function transaction_approved

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_price_plan($db)
	{
		//get price plan specifics
		if ($this->price_plan_id)
		{
			$this->sql_query = "select * from ".$this->price_plans_table." where price_plan_id = ".$this->price_plan_id;
			$price_plan_result = $db->Execute($this->sql_query);
			if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";

			if (!$price_plan_result)
			{
				if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
				return false;
			}
			elseif ($price_plan_result->RecordCount() == 1)
			{
				$show = $price_plan_result->FetchNextObject();
				$this->price_plan = $show;
				return $show;
			}
			else
			{
				if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
				return false;
			}
		}
		else
		{
			//echo "no price plan id<Br>\n";
			return false;
		}

	} //end of function get_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_cost_of_subscription($db)
	{
		//STOREFRONT CODE
		if($this->renewStorefrontSubscription)
		{
			include_once('storefront/store_class.php');
			$this->sql_query = "select * from ".Store::get("storefront_subscriptions_choices_table")."
				where period_id = ".$this->renew_subscription_variables["subscription_choice"]."";
			$choices_result = $db->Execute($this->sql_query);
			if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
			if (!$choices_result)
			{
				if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
				return false;
			}
			elseif ($choices_result->RecordCount() == 1 )
			{
				$show_choice = $choices_result->FetchNextObject();
				$this->cost_of_subscription = $show_choice->AMOUNT;
			}
			$this->subtotal = $this->cost_of_subscription;
			return true;
		}
		//STOREFRONT CODE

		$price_plan = $this->get_price_plan($db);
		if ($price_plan)
		{
			if ($price_plan->TYPE_OF_BILLING == 1)
			{
				//there is no cost of subscription
				return false;
			}
			elseif ($price_plan->TYPE_OF_BILLING == 2)
			{
				$this->sql_query = "select * from ".$this->subscription_choices." where price_plan_id = ".$this->price_plan_id." and period_id = ".$this->renew_subscription_variables["subscription_choice"]." order by value asc";
				$choices_result = $db->Execute($this->sql_query);
				if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
				if (!$choices_result)
				{
					if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
					return false;
				}
				elseif ($choices_result->RecordCount() == 1 )
				{
					$show_choice = $choices_result->FetchNextObject();
					$this->cost_of_subscription = $show_choice->AMOUNT;

				}
			}
			else
			{
				if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
				return false;
			}
			$this->subtotal = $this->cost_of_subscription;

			return true;
		}
		else
		{
			return false;
		}
	} // end of function get_cost_of_subscription

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function do_free_subscription_renewals($db)
	{
		if ($this->debug_subscription)
		{
			echo "<br>TOP OF DO_FREE_SUBSCRIPTION_RENEWALS<br>\n";
		}
		$this->page_id = 109;
		$this->get_text($db);
		$this->get_cost_of_subscription($db);
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[1669])."</td>\n</tr>\n";
		$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[1670])."</td>\n</tr>\n";
		if (($this->classified_user_id) && ($this->cost_of_subscription == 0) && ($this->renew_subscription_variables["subscription_choice"]))
		{

			$this->do_subscription_renewal($db);

			$this->body .="<tr>\n\t<td class=success_failure_message >".urldecode($this->messages[1679])."</td>\n</tr>\n";
			//$this->body .="<tr>\n\t<td class=success_failure_message>".urldecode($this->messages[855])."</td>\n\t</tr>\n";

		}
		else
		{
			$this->body .="<tr>\n\t<td class=success_failure_message>
				".urldecode($this->messages[857])."</td>\n\</tr>\n";
		}
		$this->remove_renew_subscription_session($db,$this->session_id);
		$this->body .="<tr class=back_to_my_info_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_file_name']."?a=4&b=3\" class=back_to_my_info_link >";
		$this->body .=urldecode($this->messages[1672])."</A>\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		if ($this->debug_subscription)
		{
			echo $this->body." is the current body<bR>\n";
			echo "BOTTOM OF DO_FREE_SUBSCRIPTION_RENEWALS<br>\n";
		}

		$this->display_page($db);
		return true;
	} //end of function do_free_subscription_renewals

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_cc($db)
	{
		$this->sql_query = "select * from ".$this->cc_choices." where chosen_cc = 1";

		$cc_result = $db->Execute($this->sql_query);
		if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
		if (!$cc_result)
		{
			if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
			return false;
		}
		elseif ($cc_result->RecordCount() == 1)
		{
			$show = $cc_result->FetchNextObject();
			return $show;
		}
		else
		{
			return false;
		}

	} //end of function get_cc

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_subscription_renewal_payment_expected($db)
	{
		$this->sql_query = "insert into ".$this->subscription_holds_table."
			(user_id, subscription_choice,date)
			values
			(".$this->classified_user_id.",".$this->renew_subscription_variables["subscription_choice"].",".$this->shifted_time($db).")";

		if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
		$update_subscription_renewal_payment_expected_result = $db->Execute($this->sql_query);
		if (!$update_subscription_renewal_payment_expected_result)
		{
			if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
			return false;
		}
		//STOREFRONT CODE
		if($this->renewStorefrontSubscription)
		{
			$renewal_id = $db->Insert_Id();
			$this->sql_query = "update ".$this->subscription_holds_table." set
			storefront = 1
			where renewal_id = $renewal_id";
			$update_subscription_renewal_payment_expected_result = $db->Execute($this->sql_query);
			if (!$update_subscription_renewal_payment_expected_result)
			{
				if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
				return false;
			}
		}
		//STOREFRONT CODE
		return true;
	} //end of function update_subscription_renewal_payment_expected

//####################################################################

	function check_user_subscription($db)
	{
		$this->sql_query = "select * from ".$this->user_subscriptions_table." where subscription_expire > ".$this->shifted_time($db)." and user_id = ".$this->classified_user_id;
		$get_subscriptions_results = $db->Execute($this->sql_query);
		if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";

		if (!$get_subscriptions_results)
		{
			if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
			return false;
		}
		elseif ($get_subscriptions_results->RecordCount() == 0)
		{
			$this->user_currently_subscribed = 0;
			return true;
		}
		elseif ($get_subscriptions_results->RecordCount() > 0)
		{
			//set the current price plan to the subscription price plan
			$current_subscription = $get_subscriptions_results->FetchNextObject();
			if ($current_subscripiton->PRICE_PLAN_ID)
			{
				$this->price_plan_id = $current_subscripiton->PRICE_PLAN_ID;
			}
			else
			{
				//there is no price plan attached to this subscription
				//do not set the price plan here
				//let the code after this function call get the price plan current attached to this users group
			}
			$this->user_currently_subscribed = 1;
			return true;
		}
	} // end of function check_user_subscription

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_subscription_choices_table($db)
	{
		$this->sql_query = "select * from ".$this->subscription_choices." where price_plan_id = ".$this->price_plan_id." order by value asc";
		$choices_result = $db->Execute($this->sql_query);
		if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
		if (!$choices_result)
		{
			if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
			return false;
		}
		elseif ($choices_result->RecordCount() > 0 )
		{
			while ($show_choices = $choices_result->FetchNextObject())
			{
				$choices_table .= "<tr>\n\t<td width=50% class=subscription_choices_label>".$show_choices->DISPLAY_VALUE."</td>";
				$choices_table .= "<td width=50% class=subscription_choices_value>
					<input type=radio name=c[subscription_choice] value=".$show_choices->PERIOD_ID;
				if (($choices_result->RecordCount() == 1) || ($this->renew_subscription_variables["subscription_choice"] == $show_choices->PERIOD_ID))
					$choices_table .= " checked";
				$choices_table .= " class=subscription_choices_value>".$this->configuration_data['precurrency']." ".
					sprintf("%0.2f",$show_choices->AMOUNT)." ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
			}
			return $choices_table;
		}
		else
		{
			return false;
		}
	} //end of function show_subscription_choices_table

//#######################################################################################

	function do_subscription_renewal($db)
	{
		//STOREFRONT CODE
		if($this->renewStorefrontSubscription)
		{
			include_once('storefront/store_class.php');
			$this->sql_query = "select * from ".Store::get("storefront_subscriptions_choices_table")."
				where period_id = ".$this->renew_subscription_variables["subscription_choice"]."";
			$choices_result = $db->Execute($this->sql_query);
			if (!$choices_result)
			{
				if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
				return false;
			}
			elseif ($choices_result->RecordCount() == 1 )
			{
				$show_choice = $choices_result->FetchNextObject();
				if ($show_choice->VALUE !=0)
				{
					//check to see if currently subscribed
					$this->sql_query = "select * from ".Store::get("storefront_subscriptions_table")." where user_id = ".$this->classified_user_id;
					$check_subscriptions_results = $db->Execute($this->sql_query);
					if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
					if (!$check_subscriptions_results)
					{
						if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
						return false;
					}
					elseif ($check_subscriptions_results->RecordCount() > 0)
					{
						//extend subscription period
						$show_subscription = $check_subscriptions_results->FetchNextObject();
						if ($show_subscription->EXPIRATION > $this->shifted_time($db))
							$new_expire = ($show_subscription->EXPIRATION + ($show_choice->VALUE * 86400));
						else
							$new_expire = ($this->shifted_time($db) + ($show_choice->VALUE * 86400));
						$this->sql_query = "update ".Store::get("storefront_subscriptions_table")."
							set expiration = ".$new_expire."
							where subscription_id = ".$show_subscription->SUBSCRIPTION_ID;
						$update_subscriptions_results = $db->Execute($this->sql_query);
						if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
						if (!$update_subscriptions_results)
						{
							if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
							return false;
						}

					}
					else
					{
						//enter new subscription period
						$new_expire = ($this->shifted_time($db) + ($show_choice->VALUE * 86400));
						$this->sql_query = "insert into ".Store::get("storefront_subscriptions_table")."
							(user_id,expiration)
							values
							(".$this->classified_user_id.",".$new_expire.")";
						$insert_subscriptions_results = $db->Execute($this->sql_query);
						if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
						if (!$insert_subscriptions_results)
						{
							if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
							return false;
						}
					}
				}
			}
			return true;
		}
		//STOREFRONT CODE
		//get length of subscription choice
		$this->sql_query = "select * from ".$this->subscription_choices." where price_plan_id = ".$this->price_plan_id."
			and period_id = ".$this->renew_subscription_variables["subscription_choice"]." order by value asc";
		$choices_result = $db->Execute($this->sql_query);
		if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
		if (!$choices_result)
		{
			if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
			return false;
		}
		elseif ($choices_result->RecordCount() == 1 )
		{
			$show_choice = $choices_result->FetchNextObject();
			if ($show_choice->VALUE !=0)
			{
				//check to see if currently subscribed
				$this->sql_query = "select * from ".$this->user_subscriptions_table." where user_id = ".$this->classified_user_id;
				$check_subscriptions_results = $db->Execute($this->sql_query);
				if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
				if (!$check_subscriptions_results)
				{
					if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
					return false;
				}
				elseif ($check_subscriptions_results->RecordCount() > 0)
				{
					//extend subscription period
					$show_subscription = $check_subscriptions_results->FetchNextObject();
					if ($show_subscription->SUBSCRIPTION_EXPIRE > $this->shifted_time($db))
						$new_expire = ($show_subscription->SUBSCRIPTION_EXPIRE + ($show_choice->VALUE * 86400));
					else
						$new_expire = ($this->shifted_time($db) + ($show_choice->VALUE * 86400));
					$this->sql_query = "update ".$this->user_subscriptions_table."
						set subscription_expire = ".$new_expire."
						where subscription_id = ".$show_subscription->SUBSCRIPTION_ID;
					$update_subscriptions_results = $db->Execute($this->sql_query);
					if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
					if (!$update_subscriptions_results)
					{
						if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
						return false;
					}

				}
				else
				{
					//enter new subscription period
					$new_expire = ($this->shifted_time($db) + ($show_choice->VALUE * 86400));
					$this->sql_query = "insert into ".$this->user_subscriptions_table."
						(user_id,subscription_expire)
						values
						(".$this->classified_user_id.",".$new_expire.")";
					$insert_subscriptions_results = $db->Execute($this->sql_query);
					if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
					if (!$insert_subscriptions_results)
					{
						if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
						return false;
					}
				}
			}
		}
		return true;
	} //end of function do_subscription_renewal

//STOREFRONT CODE
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function storefront_subscription_renewal_form($db)
	{
		$this->page_id = 107;
		$this->get_text($db);
		//echo "hello from almost top of upgrade form<Br>\n";
		//get this users price plan specifics
		//echo $this->price_plan_id." is price_plan_id<br>\n";
		//echo $this->price_plan->TYPE_OF_BILLING." is price_plan type in renewal form<br>\n";
		//echo $this->classified_user_id." is classified_user_id in renewal form<br>\n";

		$payment_types_accepted = $this->get_payment_types_accepted($db);
		if (($this->user_data) && ($payment_types_accepted))
		{
			$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=200 method=post>\n";
			$this->body .="<table cellpadding=0 cellspacing=1 border=0 align=center width=100%>\n";
			$this->body .="<tr class=page_title>\n\t<td  colspan=3>".urldecode($this->messages[1628])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description  colspan=3>\n\t<td colspan=3>".urldecode($this->messages[1629])."</td>\n</tr>\n";
			if (strlen($this->error_variables["choose"]) > 0)
				$this->body .="<tr>\n\t<td class=error_message colspan=3>".$this->error_variables["choose"]."</td>\n</tr>\n";

			//get subscription choices
			$this->body .="<tr class=subscription_renewal_section_title  colspan=3>\n\t<td colspan=3>".urldecode($this->messages[1630])."</td>\n</tr>\n";

			include_once('storefront/store_class.php');
			$this->sql_query = "select storefront from $this->groups_table where group_id = ".$this->user_data->GROUP_ID;
			$result = $db->Execute($this->sql_query);
			if(!$result)
				return false;
			$result = $result->FetchRow();
			if($result["storefront"]==1)
			{
				$this->sql_query = "select * from ".Store::get("storefront_group_subscriptions_choices_table")." as choices, ".Store::get("storefront_subscriptions_choices_table")." as choiceData
				where choiceData.period_id = choices.choice_id
				and choices.group_id = {$this->user_data->GROUP_ID}
				order by choiceData.amount asc";
				$choices_result = $db->Execute($this->sql_query);
				if (!$choices_result)
					return false;

				while ($show_choices = $choices_result->FetchRow())
				{
					$this->body .= "<tr>\n\t<td width=50% class=subscription_choices_label>".$show_choices["display_value"]."</td>";
					$this->body .= "<td width=50% class=subscription_choices_value>
						<input type=radio name=c[subscription_choice] value=".$show_choices["period_id"];
					if (($choices_result->RecordCount() == 1) || ($this->renew_subscription_variables["subscription_choice"] == $show_choices["period_id"]))
						$this->body .= " checked";
					$this->body .= " class=subscription_choices_value>".$this->configuration_data['precurrency']." ".
						sprintf("%0.2f",$show_choices["amount"])." ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
				}
			}


			//PAY CHOICE(S)
			$this->body .="<tr class=subscription_renewal_section_title>\n\t<td colspan=3>".urldecode($this->messages[1631])."</td>\n</tr>\n";
			$this->body .= "<tr><td colspan=3>\n\t";
			$this->body .="<table cellpadding=3 cellspacing=0 border=1 align=center width=100%>\n\t";
			if ($this->error_variables["payment_type"])
			{
				$this->body .="<tr class=page_description >\n\t<td colspan=3>";
				$this->body .="<br><font class=error_message>".urldecode($this->messages[1632])."</font>";
			}
			$this->body .="</td>\n</tr>\n";
			while ($show_payment = $payment_types_accepted->FetchNextObject())
			{
				switch ($show_payment->TYPE)
				{
					case 1:
					{
						if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
							($this->configuration_data['site_balance_override'] == 0)) ||
							(!$this->configuration_data['use_account_balance']) ||
							(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
						{
							//cash
							$this->body .="<tr>\n\t\t<td width=50%><font class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[1633])."</font><br>
							<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[1634])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td width=50% colspan=2 valign=top class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] checked value=1>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td width=50% colspan=2 valign=top class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] value=1 ";
								if ($this->renew_subscription_variables["payment_type"] == 1) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}
						}
					}
					break;
					case 2:
					{
						if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
							($this->configuration_data['site_balance_override'] == 0)) ||
							(!$this->configuration_data['use_account_balance']) ||
							(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
						{
							//credit card
							$this->sql_query = "select * from ".$this->cc_choices." where chosen_cc = 1";
							$cc_result = $db->Execute($this->sql_query);
							if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
							if (!$cc_result)
							{
								if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
								$this->setup_error = $this->messages[453];
								return false;
							}
							elseif ($cc_result->RecordCount() == 1)
							{
								$show_cc_choice = $cc_result->FetchNextObject();
							}
							$this->body .="<tr>\n\t\t<td  width=50%><font class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[1635])."</font><br>
							<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[1636])."</td>\n\t";
							if (($show_cc_choice->CC_ID == 1) || ($show_cc_choice->CC_ID == 3) || ($show_cc_choice->CC_ID == 4) || ($show_cc_choice->CC_ID >= 6))
							{
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .="<td width=10% class=subscription_renewal_payment_choices_labels ><input type=radio name=c[payment_type] checked value=2>&nbsp;</td>\n\t<td width=40% align=center>\n\t";
								}
								else
								{
									//this is a choice among many
									$this->body .="<td width=10% class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] value=2 ";
									if ($this->renew_subscription_variables["payment_type"] == 2) $this->body .="checked";
									$this->body .="></td>\n\t<td width=40% align=center class=subscription_renewal_payment_choices_cc_number_val>\n\t";
								}
								$this->body .="
									<table cellpadding=3 cellspacing=1 border=0 align=center width=100% class=subscription_renewal_payment_choices_cc_number_val>
										<tr>
											<td width=50% align=right>";
								if ($this->error_variables["cc_number"])
									$this->body .="
												<font class=error_message>".$this->error_variables["cc_number"]."</font>";
								if ($this->error_variables["cc_expiration"])
									$this->body .="
												<font class=error_message>".$this->error_variables["cc_expiration"]."</font>";
								$this->body .= urldecode($this->messages[1637]);
								$this->body .="
											</td>
											<td width=50% align=left>
												<input type=text name=c[cc_number] size=20><br>
											</td>
										</tr>";
								if ($show_cc_choice->CC_ID==7  || $show_cc_choice->CC_ID==9)
								{
									$this->body .="
										<tr>
											<td width=50% align=right>";
									$this->body .= "
												<a href=\"javascript:winimage('./images/cvv2_code.gif',500,200)\">".
												urldecode($this->messages[200107])."</a>";
									$this->body .= "
											</td>
											<td width=50% align=left>
												<input type=text name=c[cvv2_code] size=4>
											</td>
										</tr>";
								}
								$this->body .="
										<tr>
											<td width=50% align=right>".urldecode($this->messages[1638])."</td>
											<td width=50% align=left>";
								$this->body .= "<select name=c[cc_exp_month]>";
								for ($i=1;$i<13;$i++)
								{
									$this->body .="
													<option>".sprintf("%02d",$i)."</option>";
								}
								$this->body .= "</select>";
								$this->display_year_dropdown("c[cc_exp_year]");
								$this->body .= "
											</td>
										</tr>
									</table>";
							}
							elseif ($show_cc_choice->CC_ID == 2)
							{
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .= "<td colspan=2 width=50% valign=top  class=subscription_renewal_payment_choices_labels>
									<input type=radio name=c[payment_type] checked value=2>&nbsp;";
								}
								else
								{
									//this is a choice among many
									$this->body .= "<td colspan=2 width=50% valign=top class=subscription_renewal_payment_choices_labels>
									<input type=radio name=c[payment_type] value=2 ";
									if ($this->classified_variables["payment_type"] == 2) $this->body .= "checked";
									$this->body .= ">";
								}
							}
							$this->body .="</td>\n\t</tr>\n\t";
						}
					}
					break;
					case 3:
					{
						if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
							($this->configuration_data['site_balance_override'] == 0)) ||
							(!$this->configuration_data['use_account_balance']) ||
							(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
						{
							//paypal
							$this->body .="<tr>\n\t\t<td  width=50%><font class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[1639])."</font><br>
							<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[1640])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td width=50% colspan=2 class=subscription_renewal_payment_choices_labels><input type=radio checked name=c[payment_type] value=3>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td width=50% colspan=2 class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] value=3 ";
								if ($this->renew_subscription_variables["payment_type"] == 3) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}
						}
					}
					break;
					case 4:
					{
						if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
							($this->configuration_data['site_balance_override'] == 0)) ||
							(!$this->configuration_data['use_account_balance']) ||
							(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
						{
							//money order
							$this->body .="<tr>\n\t\t<td width=50%><font class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[1641])."</font><br>
							<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[1642])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td width=50% colspan=2 valign=top class=subscription_renewal_payment_choices_labels><input type=radio checked name=c[payment_type] value=4>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td width=50% colspan=2 valign=top class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] value=4 ";
								if ($this->renew_subscription_variables["payment_type"] == 4) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}
						}
					}
					break;
					case 5:
					{
						if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
							($this->configuration_data['site_balance_override'] == 0)) ||
							(!$this->configuration_data['use_account_balance']) ||
							(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
						{
							//check
							$this->body .="<tr>\n\t\t<td width=50%><font class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[1643])."</font><br>
							<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[1644])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td  width=50% colspan=2 valign=top class=subscription_renewal_payment_choices_labels><input type=radio checked name=c[payment_type] value=5>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td  width=50% colspan=2 valign=top class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] value=5 ";
								if ($this->renew_subscription_variables["payment_type"] == 5) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}
						}
					}
					break;
					case 6:
					{
						if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
							($this->configuration_data['site_balance_override'] == 0)) ||
							(!$this->configuration_data['use_account_balance']) ||
							(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
						{
							//worldpay
							$this->body .="<tr>\n\t\t<td  width=50%><font class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[1645])."</font><br>
							<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[1646])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td width=50% colspan=2 class=subscription_renewal_payment_choices_labels><input type=radio checked name=c[payment_type] value=6>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td width=50% colspan=2 class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] value=6 ";
								if ($this->renew_subscription_variables["payment_type"] == 6) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}
						}
					}
					break;

					case 7: //site balance
					{
						if ($this->debug_subscription)
						{
							echo $this->configuration_data['use_account_balance']." is USE_ACCOUNT_BALANCE<br>\n";
							echo $this->configuration_data['positive_balances_only']." is POSITIVE_BALANCES_ONLY<br>\n";
							echo $this->user_data->ACCOUNT_BALANCE." is the ACCOUNT_BALANCE<br>\n";
						}
						if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) && ($this->user_data->ACCOUNT_BALANCE > 0))
							|| (($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
						{
							if ($this->debug_subscription) echo "displaying account balance option<br>\n";
							//will check to see if balance is enough to pay for within transaction detail check

							if (!$this->configuration_data['positive_balances_only'])
							{
								$this->body .="<tr>\n\t\t<td class=subscription_renewal_payment_choices_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[3247])."</font><br>
									<font class=subscription_renewal_payment_choices_descriptions>";
								$this->body .= urldecode($this->messages[3248])."<br>".urldecode($this->messages[3249]);

								//get current accumulated but unbilled charges
								$this->sql_query = "select * from ".$this->balance_transactions." where (auction_id != 0 or ad_id != 0 or subscription_renewal != 0) and cc_transaction_id = 0 and invoice_id = 0 and user_id = ".$this->classified_user_id;
								if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
								$invoice_total_result = $db->Execute($this->sql_query);
								if (!$invoice_total_result)
								{
									if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
									return false;
								}
								elseif ($invoice_total_result->RecordCount() > 0)
								{
									$to_be_invoiced = 0;
									while ($show_invoices = $invoice_total_result->FetchNextObject())
									{
										$to_be_invoiced = $to_be_invoiced + $show_invoices->AMOUNT;
									}
									$this->body .= " ".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$to_be_invoiced)." ".$this->configuration_data['postcurrency'];
								}
								else
								{
									$this->body .= " ".$this->configuration_data['precurrency']." ".sprintf("%0.02f",0)." ".$this->configuration_data['postcurrency'];
								}
								$this->body .= "</td>\n\t";
							}
							else
							{
								$this->body .="<tr>\n\t\t<td class=subscription_renewal_payment_choices_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[3250])."</font><br>
									<font class=subscription_renewal_payment_choices_descriptions><br>".
									urldecode($this->messages[3251])."<br><br>".urldecode($this->messages[3252])." ".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->user_data->ACCOUNT_BALANCE)." ".$this->configuration_data['postcurrency']."<br>\n\t";
								$this->body .= "</td>\n\t";
							}
							$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] ";
							if ($this->renew_upgrade_variables["payment_type"] == 7)
								$this->body .="checked";
							elseif ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
							($this->configuration_data['site_balance_override'] == 0)) ||
							(!$this->configuration_data['use_account_balance']) ||
							(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
								$this->body .= "checked";
							$this->body .= " value=7>&nbsp;</td>\n\t</tr>\n\t";
						}
					}
					break;
					case 8:
					{
						//NOCHEX
						/*if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
							($this->configuration_data['site_balance_override'] == 0)) ||
							(!$this->configuration_data['use_account_balance']) ||
							(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
						{
							//worldpay
							$this->body .="<tr>\n\t\t<td  width=50%><font class=subscription_renewal_payment_choices_labels>".urldecode($this->messages[])."</font><br>
							<font class=subscription_renewal_payment_choices_descriptions>".urldecode($this->messages[])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td width=50% colspan=2 class=subscription_renewal_payment_choices_labels><input type=radio checked name=c[payment_type] value=6>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td width=50% colspan=2 class=subscription_renewal_payment_choices_labels><input type=radio name=c[payment_type] value=8 ";
								if ($this->renew_subscription_variables["payment_type"] == 8) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}
						}*/
					}
					break;
				}
			}
			$this->body .="</table>\n\t</td>\n</tr>\n";
			//$this->body .="\n\t</td>\n</tr>\n";
		}
		$this->body .="<tr>\n\t<td align=center colspan=2 class=sumbit_button>\n\t<br><input type=submit name=z class=submit_button value=\"".urldecode($this->messages[1647])."\">\n\t</td>\n</tr>\n";
		$this->body .="<tr>\n\t<td colspan=2 class=back_to_my_info_link><a href=".$this->configuration_data['classifieds_url']."?a=4&b=3>".urldecode($this->messages[1648])."</a></td>\n</tr>\n";
		$this->body .="</table>\n";
		$this->body .="</form>\n";

		$this->display_page($db);
		return true;

	} //end of function storefront_subscription_renewal_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function final_approval_storefront_form($db)
	{
		$this->page_id = 108;
		$this->get_text($db);

		//save transaction data
		//save the transaction data overwriting
		//update transaction data into the classifieds table

		//get totals and taxes if any
		if ($this->classified_user_id)
		{
			$user_data = $this->get_user_data($db);
			$this->tax = $this->get_tax($db,$user_data);
			$this->total = $this->subtotal + $this->tax;

			//check to see if the early days of the ad so can upgrade
			$this->body .= "<form action=".$this->configuration_data['classifieds_file_name']."?a=200&d=final_accepted method=post>\n";
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
			$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[1652])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[1653])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td>";
			if ($this->total != 0)
			{
				$user_data = $this->user_data;
				//check pay choice information
				switch ($this->renew_subscription_variables["payment_type"])
				{
					case 1: //cash
						$this->body .= urldecode($this->messages[1654]);
					break;
					case 2: //credit card
						$this->body .= urldecode($this->messages[1655]);
					break;
					case 3: //paypal
						$this->body .= urldecode($this->messages[1656]);
					break;
					case 4: //money order
						$this->body .= urldecode($this->messages[1657]);
					break;
					case 5: //check
						$this->body .= urldecode($this->messages[1658]);
					break;
					case 6: //worldpay
						$this->body .= urldecode($this->messages[1659]);
					break;
					case 7: //invoicing/site balance
						$this->body .= urldecode($this->messages[3246]);
					break;
					case 8: //invoicing/NOCHEX
						//$this->body .= urldecode($this->messages[3246]);
					break;
					default:
						return false;
				} //end of switch ($this->renew_subscription_variables["payment_type"])
			}
			$this->body .= $payment_type_message."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td align=center>\n\t<table cellpadding=3 cellspacing=1>\n\t";
			$this->body .="<tr class=subscription_renewal_transaction_totals_header>\n\t<td colspan=2>".urldecode($this->messages[1660])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td  class=subscription_renewal_left_column_header>".urldecode($this->messages[1661])."</td>\n\t";
			$this->body .="<td class=subscription_renewal_right_column_header>".urldecode($this->messages[1662])."</td>\n</tr>\n";

			//show transaction totals
			//cost of subscription
			$this->body .="<tr>\n\t<td  width=50% class=subscription_renewal_left_column>";
			$this->body .=urldecode($this->messages[1663])."</td>\n\t";
			$this->body .="<td class=subscription_renewal_right_column>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->cost_of_subscription)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			//tax
			$this->body .="<tr class=subscription_renewal_left_column >\n\t<td >
				".urldecode($this->messages[1664])."</td>\n\t
				<td class=subscription_renewal_right_column>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->tax)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
			//total
			$this->body .="<tr class=subscription_renewal_left_column>\n\t<td >
				".urldecode($this->messages[1665])."</td>\n\t
				<td class=subscription_renewal_right_column>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->total)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			$this->body .="</table>\n\t</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td align=center class=submit_button><input type=submit name=z value=\"".urldecode($this->messages[1666])."\" class=submit_button>\n\t</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td class=back_to_my_info_link colspan=2><a href=".trim($this->configuration_data['classifieds_url'])."?a=200>".urldecode($this->messages[1667])."</a><br>";
			$this->body .="<tr>\n\t<td class=back_to_my_info_link colspan=2><a href=".trim($this->configuration_data['classifieds_url'])."?a=4&b=3>".urldecode($this->messages[1668])."</a><br>";
			$this->body .="</td>\n</tr>\n";

			$this->body .="</table>\n";
			$this->body .="</form>\n";
			$this->display_page($db);
			return true;
		}
		else
		{
			//cannot upgrade or renew now
			return false;
		}
	} //end of function final_approval_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

//STOREFRONT CODE

} //end of class Renew_upgrade_sellers_ads
?>