<? //add_to_account_balance.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Account_balance extends Site {

	var $classified_user_id;
	var $user_data;
	var $session_id;
	var $balance_variables;

	var $debug_balance = 0;

//########################################################################

	function Account_balance($db,$classified_user_id,$language_id,$session_id=0,$product_configuration=0)
	{
		$this->Site($db,1,$language_id,$classified_user_id,$product_configuration);
		$this->classified_user_id = $classified_user_id;
		$this->session_id = $session_id;
		$this->user_data = $this->get_user_data($db,$this->classified_user_id);

		if (($this->user_data) && ($this->classified_user_id)
			&& ($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']))
		{
			//this is an old session...restart it
			$this->sql_query = "select * from ".$this->sell_table." where session = \"".$this->session_id."\" and account_balance = 1";
			$setup_balance_result = $db->Execute($this->sql_query);
			if ($this->debug_balance)
				echo $this->sql_query."<br>\n";
			if (!$setup_balance_result)
			{
				if ($this->debug_balance)
					echo $this->sql_query."<br>\n";
				$this->setup_error = $this->messages[453];
				return false;
			}
			elseif ($setup_balance_result->RecordCount() == 1)
			{
				$show = $setup_balance_result->FetchNextObject();
				$this->account_variables["price"] = $show->PRICE;
				$this->account_variables["payment_type"] = $show->PAYMENT_TYPE;
				$this->account_variables["cc_number"] = Site::decrypt($show->CC_NUMBER, $show->DECRYPTION_KEY);
				$this->account_variables["decryption_key"] = 0;
				$this->account_variables["cc_exp_year"] = $show->CC_EXP_YEAR;
				$this->account_variables["cc_exp_month"] = $show->CC_EXP_MONTH;
				$this->account_variables["cvv2_code"] = $show->CVV2_CODE;
				if ($this->debug_balance)
				{
					echo "pulling variables from db<BR>\n";
					echo $this->account_variables["price"]." is price<br>\n";
				}
			}
			else
			{
				$this->sql_query = "insert into ".$this->sell_table."
					(session,time_started,account_balance)
					values
					(\"".$this->session_id."\",".$this->shifted_time($db).",1)";
				$insert_sell_result = $db->Execute($this->sql_query);
				if ($this->debug_balance)
					echo $this->sql_query."<br>\n";
				if (!$insert_sell_result)
				{
					if ($this->debug_balance)
						echo $this->sql_query."<br>\n";
					return false;
				}
			}
		}
		else
		{
			//this user does not exist
			return false;
		}

		if ($this->debug_balance)
		{
			echo $this->classified_user_id." is classified_user_id<br>\n";
		}
	} //end of function Account_balance

//###########################################################

	function get_account_variables($info)
	{
		if (is_array($info))
		{
			reset ($info);
			foreach ($info as $key => $value)
			{
				$this->account_variables[$key] = $value;
				if ($this->debug_balance)
					echo $key." is the key to - ".$value."<br>\n";
			}
		}

	} //end of get_account_variables

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function save_account_variables ($db)
	{
		$unique_key = substr(md5(uniqid(rand(),1)), 0,strlen($this->account_variables["cc_number"]));
		$encrypted_cc_num = Site::encrypt($this->account_variables["cc_number"], $unique_key);
		$this->sql_query = "update ".$this->sell_table." set
			price = \"".$this->account_variables["price"]."\",
			payment_type = \"".$this->account_variables["payment_type"]."\",
			cc_number = \"$encrypted_cc_num\",
			decryption_key = \"$unique_key\",
			cc_exp_year = \"".$this->account_variables["cc_exp_year"]."\",
			cc_exp_month = \"".$this->account_variables["cc_exp_month"]."\",
			cvv2_code = \"".$this->account_variables["cvv2_code"]."\"
			where session = \"".$this->session_id."\" and account_balance = 1";
		$save_variable_result = $db->Execute($this->sql_query);
		if ($this->debug_balance)
			echo $this->sql_query."<br>\n";
		if (!$save_variable_result)
		{
			//$this->body .=$this->sql_query."<br>\n";
			return false;
		}
	} //end of function save_account_variables

//####################################################################

	function remove_account_session($db,$account_session=0)
	{
		if ($account_session)
		{
			$this->sql_query = "delete from ".$this->sell_table." where session = \"".$account_session."\" and account_balance = 1";
			if ($this->debug_balance)
				echo $this->sql_query."<Br>\n";
			$delete_account_result = $db->Execute($this->sql_query);
			if (!$delete_account_result)
			{
				return false;
			}
		}
	} //end of funciton remove_account_session

//####################################################################

	function account_form($db)
	{
		$this->page_id = 173;
		$this->get_text($db);
		//echo "hello from almost top of upgrade form<Br>\n";
		//get this users price plan specifics
		if ($this->debug_balance)
		{
			echo $this->classified_user_id." is classified_user_id in account_form<br>\n";
			echo $this->user_data->ID." is the user id <BR>\n";
			echo $this->user_data->FIRSTNAME." is the user firstname <BR>\n";
		}
		if (($this->classified_user_id) && ($this->user_data))
		{
			if ($this->debug_balance)
				echo "building form<bR>\n";
			$payment_types_accepted = $this->get_payment_types_accepted($db);
			if ($this->debug_balance)
				echo $payment_types_accepted->RecordCount()." is the number of accepted payment types<BR>\n";
			if (($this->user_data) && ($payment_types_accepted))
			{
				$renewable = 0;
				$upgradeable = 0;
				$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=29 method=post>\n";
				$this->body .="<table cellpadding=0 cellspacing=1 border=0 align=center width=100%>\n";
				$this->body .="<tr class=page_title>\n\t<td  colspan=2>".urldecode($this->messages[2497])."</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t<td colspan=2>".urldecode($this->messages[2498])."</td>\n</tr>\n";

				$this->body .="<tr>\n\t<td colspan=2>\n\t";
				$this->body .="<table cellpadding=3 cellspacing=0 border=1 align=center width=100%>\n\t";

				if (strlen($this->error_variables["balance"]) > 0)
					$this->body .="<tr>\n\t<td class=error_message colspan=3>".$this->error_variables["balance"]."</td>\n</tr>\n";

				$this->body .="<tr>\n\t\t<td width=50% class=cost_field_labels >".urldecode($this->messages[2503])."<Br>";
				$this->body .="<font class=cost_field_descriptions>".urldecode($this->messages[2505])."</td>\n\t";
				$this->body .= "<td class=cost_data_values><input type=text value=\"".$this->account_variables["price"]."\" name=c[price] class=cost_data_values></td></tr>";

				$this->body .="<tr>\n\t\t<td width=50% class=cost_field_labels >".urldecode($this->messages[2504])."</td>";
				$this->body .= "<td class=cost_data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->user_data->ACCOUNT_BALANCE).
					" ".$this->configuration_data['postcurrency']."</td></tr>";

				$this->body .="</table>\n\t</td>\n</tr>\n";

				if (strlen($this->error_variables["choose"]) > 0)
					$this->body .="<tr>\n\t<td class=error_message colspan=3>".$this->error_variables["choose"]."</td>\n</tr>\n";

				//PAY CHOICE(S)
				$this->body .="<tr class=payment_choice_section_title>\n\t<td colspan=3>".urldecode($this->messages[1414])."</td>\n</tr>\n";
				$this->body .= "<tr><td colspan=2>\n\t";
				$this->body .="<table cellpadding=3 cellspacing=0 border=1 align=center width=100%>\n\t";
				$this->body .="<tr class=page_description >\n\t<td colspan=3>".urldecode($this->messages[809]);
				if ($this->error_variables["payment_type"])
					$this->body .="<font class=error_message>".urldecode($this->messages[810])."</font>";
				$this->body .="</td>\n</tr>\n";

				while ($show_payment = $payment_types_accepted->FetchNextObject())
				{
					switch ($show_payment->TYPE)
					{
						case 1:
						{
							//cash
							$this->body .="<tr>\n\t\t<td width=50%><font class=payment_choices_labels>".urldecode($this->messages[2506])."</font><br>
								<font class=payment_choices_descriptions>".urldecode($this->messages[2507])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] checked value=1>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=1 ";
								if ($this->account_variables["payment_type"] == 1) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}
						}
						break;
						case 2:
						{
							//credit card
							$this->sql_query = "select * from ".$this->cc_choices." where chosen_cc = 1";
							$cc_result = $db->Execute($this->sql_query);
							if (!$cc_result)
							{
								$this->setup_error = $this->messages[453];
								return false;
							}
							elseif ($cc_result->RecordCount() == 1)
							{
								$show_cc_choice = $cc_result->FetchNextObject();
							}
							$this->body .="<tr>\n\t\t<td  width=50%><font class=payment_choices_labels>".urldecode($this->messages[2508])."</font><br>
								<font class=payment_choices_descriptions>".urldecode($this->messages[2509])."</td>\n\t";
							if ($this->debug_balance)
								echo $show_cc_choice->CC_ID." is cc_id using<bR>\n";
							if (($show_cc_choice->CC_ID == 1) || ($show_cc_choice->CC_ID == 3) || ($show_cc_choice->CC_ID == 4) || ($show_cc_choice->CC_ID >= 6))
							{
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .="<td width=10% ><input type=radio name=c[payment_type] checked value=2>&nbsp;</td>\n\t<td width=40% align=center>\n\t";
								}
								else
								{
									//this is a choice among many
									$this->body .="<td width=10% ><input type=radio name=c[payment_type] value=2 ";
									if ($this->account_variables["payment_type"] == 2) $this->body .="checked";
									$this->body .="></td>\n\t<td width=40% align=center class=payment_choices_cc_number_values>&nbsp;\n\t";
								}
								$this->body .="
									<table cellpadding=3 cellspacing=1 border=0 align=center width=100% class=payment_choices_cc_number_data_values>
										<tr>
											<td width=50% class=cc_number>";
								if ($this->error_variables["cc_number"])
									$this->body .="
												<font class=error_message>".$this->error_variables["cc_number"]."</font>";
								if ($this->error_variables["cc_expiration"])
									$this->body .="
												<font class=error_message>".$this->error_variables["cc_expiration"]."</font>";
								$this->body .= urldecode($this->messages[2510]);
								$this->body .="
											</td>
											<td width=50% align=left>
												<input type=text name=c[cc_number] size=20><br>
											</td>
										</tr>";
								if ($show_cc_choice->CC_ID==7 || $show_cc_choice->CC_ID==9)
								{
									$this->body .="
										<tr>
											<td width=50% align=right>";
									$this->body .= "
												<a href=\"javascript:winimage('./images/cvv2_code.gif',500,200)\">".
												urldecode($this->messages[200105])."</a>";
									$this->body .= "
											</td>
											<td width=50% align=left>
												<input type=text name=c[cvv2_code] size=4>
											</td>
										</tr>";
								}
								$this->body .="
										<tr>
											<td width=50% class=cc_expiration>".urldecode($this->messages[2511])."</td>
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
							elseif (($show_cc_choice->CC_ID == 2) || ($show_cc_choice->CC_ID == 5))
							{
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .= "<td colspan=2 width=50% valign=top><input type=radio name=c[payment_type] checked value=2>&nbsp;";
								}
								else
								{
									//this is a choice among many
									$this->body .= "<td colspan=2 width=50% valign=top><input type=radio name=c[payment_type] value=2 ";
									if ($this->classified_variables["payment_type"] == 2) $this->body .= "checked";
									$this->body .= ">";
								}
							}
							$this->body .="</td>\n\t</tr>\n\t";
						}
						break;
						case 3:
						{
							//paypal
							$this->body .="<tr>\n\t\t<td  width=50%><font class=payment_choices_labels>".urldecode($this->messages[2512])."</font><br>
								<font class=payment_choices_descriptions>".urldecode($this->messages[2513])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=3>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=3 ";
								if ($this->account_variables["payment_type"] == 3) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}
						}
						break;
						case 4:
						{
							//money order
							$this->body .="<tr>\n\t\t<td width=50%><font class=payment_choices_labels>".urldecode($this->messages[2514])."</font><br>
								<font class=payment_choices_descriptions>".urldecode($this->messages[2515])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=4>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=4 ";
								if ($this->account_variables["payment_type"] == 4) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}
						}
						break;
						case 5:
						{
							//check
							$this->body .="<tr>\n\t\t<td width=50%><font class=payment_choices_labels>".urldecode($this->messages[2516])."</font><br>
								<font class=payment_choices_descriptions>".urldecode($this->messages[2517])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td  width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=5>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td  width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=5 ";
								if ($this->account_variables["payment_type"] == 5) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}
						}
						break;
						case 6:
						{
							//worldpay
							$this->body .="<tr>\n\t\t<td  width=50%><font class=payment_choices_labels>".urldecode($this->messages[2518	])."</font><br>
								<font class=payment_choices_descriptions>".urldecode($this->messages[2519])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=6>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=6 ";
								if ($this->account_variables["payment_type"] == 6) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}
						}
						break;
						case 8:
						{
							//NOCHEX
							/*$this->body .="<tr>\n\t\t<td  width=50%><font class=payment_choices_labels>".urldecode($this->messages[])."</font><br>
								<font class=payment_choices_descriptions>".urldecode($this->messages[])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=6>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=6 ";
								if ($this->account_variables["payment_type"] == 8) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}*/
						}
						break;
					}
				}
				$this->body .="</table>\n\t</td>\n</tr>\n";

				$this->body .="<tr>\n\t<td align=center colspan=2>\n\t<br><input type=submit name=z class=submit_button value=\"".urldecode($this->messages[2520])."\">\n\t</td>\n</tr>\n";
				$this->body .="<tr>\n\t<td colspan=2><a href=".$this->configuration_data['classifieds_url']."?a=4&b=3 class=back_to_my_account_link>".urldecode($this->messages[2521])."</a></td>\n</tr>\n";
				$this->body .="</table>\n";
				$this->body .="</form>\n";

				$this->display_page($db);
				return true;
			}
			else
			{
				//echo "something wrong 2<br>\n";
				return false;
			}
		}
		else
		{
			//echo "something wrong<br>\n";
			return false;
		}

	} //end of function account_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_transaction_variables($db)
	{
		$this->page_id = 173;
		$this->get_text($db);
		$this->error = 0;
		unset($this->error_variables);
		$this->error_variables = array();

		if ($this->account_variables["price"] != 0)
		{
			if (!$this->account_variables["payment_type"])
			{
				//error in classified_title - was not entered
				$this->error++;
				$this->error_variables["payment_type"] = urldecode($this->messages[2500]);
				if ($this->debug_balance)
					echo "bad transaction type<br>\n";
			}
		}
		elseif ($this->account_variables["price"] == 0)
		{
			$this->error++;
			$this->error_variables["balance"] = urldecode($this->messages[2499]);
			if ($this->debug_balance)
				echo "bad transaction type<br>\n";
		}

		if (($this->account_variables["payment_type"] == 2) && ($this->account_variables["price"] != 0))
		{
			//cc_number
			//put verification script in
			$this->sql_query = "select cc_id from ".$this->cc_choices." where chosen_cc = 1";
			if ($this->debug_balance)
				echo $this->sql_query." is the query <br>\n";
			$cc_choice_result = $db->Execute($this->sql_query);
			if (!$cc_choice_result)
			{
				return false;
			}
			elseif ($cc_choice_result->RecordCount() == 1)
			{
				$show_cc_choice = $cc_choice_result->FetchNextObject();
				if (($show_cc_choice->CC_ID == 1) || ($show_cc_choice->CC_ID == 3) || ($show_cc_choice->CC_ID == 4) || ($show_cc_choice->CC_ID >= 6))
				{
					if (strlen(trim($this->account_variables["cc_number"])) == 0)
					{
						$this->error++;
						$this->error_variables["cc_number"] = urldecode($this->messages[2501]);
					}

					//check date of expiration
					$current_year = date("y");
					if (($this->account_variables["cc_exp_year"] < $current_year) || (($this->account_variables["cc_exp_year"] == $current_year)
						&& ($this->account_variables["cc_exp_month"] < date("m"))))
					{
						$this->error++;
						$this->error_variables["cc_expiration"] = urldecode($this->messages[2502]);
					}
				}
			}
			else
				return false;
		}
		elseif (($this->account_variables["payment_type"] == 0) || (strlen(trim($this->account_variables["payment_type"])) == 0))
		{
			//some action must be taken
			$this->error++;
			$this->error_variables["choose"] = urldecode($this->messages[2500]);
		}

		if ($this->debug_balance)
		{
			echo $this->error." is the error count<br>\n";
			reset($this->error_variables);
			foreach ($this->error_variables as $key => $value)
				echo $key." is the key to ".$value."<br>\n";
		}

		if ($this->error == 0)
			return true;
		else
			return false;
	} //end of function check_transaction_variables

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function final_approval_form($db)
	{
		$this->page_id = 174;
		$this->get_text($db);

		//save transaction data
		//save the transaction data overwriting
		//update transaction data into the classifieds table


		//get totals and taxes if any

		$this->body .= "<form onSubmit=\"this.z.disabled=true\" action=".$this->configuration_data['classifieds_file_name']."?a=29&d=final_accepted method=post>\n";
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[2522])."</td>\n</tr>\n";
		$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[2523])."</td>\n</tr>\n";
		$this->body .="<tr class=page_description>\n\t<td>";
		$user_data = $this->user_data;
		//check pay choice information
		switch ($this->account_variables["payment_type"])
		{
			case 1: //cash
						$this->body .= urldecode($this->messages[2524]);
					break;
					case 2: //credit card
						$this->body .= urldecode($this->messages[2525]);
					break;
					case 3: //paypal
						$this->body .= urldecode($this->messages[2526]);
					break;
					case 4: //money order
						$this->body .= urldecode($this->messages[2527]);
					break;
					case 5: //check
						$this->body .= urldecode($this->messages[2528]);
					break;
					case 6: //worldpay
						$this->body .= urldecode($this->messages[2529]);
					break;
					case 8: //NOCHEX
						//$this->body .= urldecode($this->messages[]);
					break;
					default:
						return false;
		} //end of switch ($this->account_variables["payment_type"])

		$this->body .= $payment_type_message."</td>\n</tr>\n";
		$this->body .="<tr>\n\t<td align=center>\n\t<table cellpadding=3 cellspacing=1>\n\t";
		$this->body .="<tr>\n\t<td  class=add_to_balance_label>".urldecode($this->messages[2530])."</td>\n\t";
		$this->body .="<td class=add_to_balance_data>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->account_variables["price"]).
			" ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
		$this->body .="</table>\n\t</td>\n</tr>\n";
		$this->body .="<tr>\n\t<td align=center class=complete_transaction_button><input type=submit name=z value=\"".urldecode($this->messages[2540])."\">\n\t</td>\n</tr>\n";
		$this->body .="<tr>\n\t<td align=center><a href=".trim($this->configuration_data['classifieds_url'])."?a=4&b=3 class=back_to_my_currents_ads_link>".urldecode($this->messages[3244])."</a><br>";
		$this->body .="</td>\n</tr>\n";

		$this->body .="</table>\n";
		$this->body .="</form>\n";
		$this->display_page($db);
		return true;
	} //end of function final_approval_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function transaction_approved($db)
	{
		if ($this->debug_balance)
			echo "hello from top of transaction_approved<br>\n";

		//insert transaction into balance transactions table
		$this->sql_query = "insert into ".$this->balance_transactions."
			(user_id,amount,date,payment_type)
			values
			(".$this->classified_user_id.",".$this->account_variables["price"].",".$this->shifted_time($db).",".$this->account_variables["payment_type"].")";
		$balance_transaction_result = $db->Execute($this->sql_query);
		if (!$balance_transaction_result)
		{
			return false;
		}
		$account_balance = $db->Insert_ID();

		if ($this->debug_balance)
		{
			echo $this->account_variables["price"]." is amount to charge in transaction_approved<br>\n";
		}

		$this->page_id = 175;
		$this->get_text($db);

		switch ($this->account_variables["payment_type"])
		{
			case 1:
			{
				//cash
				//administrator must approve transaction
				//place in renewal transactions expecting funds or on hold
				$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
				$this->body .= "<tr class=page_title><td>".urldecode($this->messages[2531])."</td>\n</tr>\n";
				$this->body .= "<tr class=page_description><td>".urldecode($this->messages[2532])."</td>\n</tr>\n";

				//display message saying the ad has not been renewed until payment
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[2533])." - ".
				$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->account_variables["price"]).
					" ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
				$this->remove_account_session($db,$this->session_id);
				$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_url']."?a=4&b=3\" class=my_account_link >";
				$this->body .=urldecode($this->messages[2536])."</A>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
				$this->display_page($db);
				exit;
			}
			break;
			case 2:
			{
				if ($this->debug_balance)
				{
					echo $this->page_id." is the page_id just before calling cc<br>\n";
				}
				//credit card
				//each credit card processor will have its own transaction handler
				//find the right credit card processor
				if ($this->debug_balance)
					echo "hello from credit card<bR>\n";
				$this->account_balance = 1;
				$user_data = $this->user_data;
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
				include("paypal_initiate.php");
			}
			break;
			case 4:
			{
				//money order
				//administrator must open classified ad
				//check if instant renewal or placed on hold
				$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
				$this->body .= "<tr class=page_title><td>".urldecode($this->messages[2531])."</td>\n</tr>\n";
				$this->body .= "<tr class=page_description><td>".urldecode($this->messages[2532])."</td>\n</tr>\n";
				//display message saying the ad has not been renewed until payment
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[2534])." - ".
					$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->account_variables["price"]).
					" ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
				$this->remove_account_session($db,$this->session_id);
				$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_url']."?a=4&b=3\" class=my_account_link >";
				$this->body .=urldecode($this->messages[2536])."</A>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
				$this->display_page($db);
				exit;
			}
			break;
			case 5:
			{
				//check
				//administrator must open classified ad
				//check if instant renewal or placed on hold
				$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
				$this->body .= "<tr class=page_title><td>".urldecode($this->messages[2531])."</td>\n</tr>\n";
				$this->body .= "<tr class=page_description><td>".urldecode($this->messages[2532])."</td>\n</tr>\n";
				//display message saying the ad has not been renewed until payment
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[2535])." - ".
					$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->account_variables["price"]).
					" ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
				$this->remove_account_session($db,$this->session_id);
				$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_url']."?a=4&b=3\" class=my_account_link >";
				$this->body .=urldecode($this->messages[2536])."</A>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
				$this->display_page($db);
				exit;
			}
			case 6:
			{
				//worldpay
				include("initiate_worldpay.php");
				return true;
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
		} //end of switch ($this->account_variables["payment_type"])
		return true;
	} //end of function transaction_approved

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_cc($db)
	{
		$this->sql_query = "select * from ".$this->cc_choices." where chosen_cc = 1";

		$cc_result = $db->Execute($this->sql_query);
		if ($this->debug_balance)
			echo $this->sql_query."<br>\n";
		if (!$cc_result)
		{
			$this->body .=$this->sql_query."<br>\n";
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

} //end of class Account_balance
?>
