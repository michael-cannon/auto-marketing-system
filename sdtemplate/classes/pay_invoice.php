<? //pay_invoice.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Pay_invoice extends Site {

	var $classified_user_id;
	var $user_data;
	var $session_id;
	var $invoice_variables = array();
	var $invoice_id = 0;
	var $invoice_info;
	var $invoice_total;

	var $debug_invoice = 0;

//########################################################################

	function Pay_invoice($db,$classified_user_id,$language_id,$session_id=0,$invoice_id=0,$product_configuration=0)
	{
		$this->Site($db,1,$language_id,$classified_user_id,$product_configuration);
		$this->classified_user_id = $classified_user_id;
		$this->session_id = $session_id;
		$this->invoice_id = $invoice_id;
		$this->user_data = $this->get_user_data($db,$this->classified_user_id);

		if (($this->user_data) && ($this->classified_user_id)
			&& ($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only']))
		{
			//this is an old session...restart it
			$this->sql_query = "select * from ".$this->sell_table." where session = \"".$this->session_id."\" and pay_invoice != 0";
			$setup_invoice_result = $db->Execute($this->sql_query);
			if ($this->debug_invoice) echo $this->sql_query."<br>\n";
			if (!$setup_invoice_result)
			{
				if ($this->debug_invoice) echo $this->sql_query."<br>\n";
				$this->setup_error = $this->messages[453];
				return false;
			}
			elseif ($setup_invoice_result->RecordCount() == 1)
			{
				$show = $setup_invoice_result->FetchNextObject();
				//highlight_string(print_r($show,1));
				$this->invoice_id = $show->PAY_INVOICE;
				$this->invoice_variables["price"] = $show->PRICE;
				$this->invoice_variables["payment_type"] = $show->PAYMENT_TYPE;
				$this->invoice_variables["cc_number"] = Site::decrypt($show->CC_NUMBER, $show->DECRYPTION_KEY);
				$this->invoice_variables["decryption_key"] = 0;
				$this->invoice_variables["cc_exp_year"] = $show->CC_EXP_YEAR;
				$this->invoice_variables["cc_exp_month"] = $show->CC_EXP_MONTH;
				$this->invoice_variables["cvv2_code"] = $show->CVV2_CODE;

				$this->get_invoice_info($db);
				$this->get_invoice_total($db);
				if ($this->debug_invoice)
				{
					echo "pulling variables from db<BR>\n";
					echo $show->PAY_INVOICE." is the invoice id from the database<br>\n";
					echo $this->invoice_variables["price"]." is price<br>\n";
				}
			}
			else
			{
				//this is a new session
				//insert the invoice id to be paid into the session
				if ($invoice_id)
				{
					//check the existence of the invoice connected to this user
					$this->sql_query = "select * from ".$this->invoices_table."
						where invoice_id = ".$invoice_id." and user_id = ".$this->classified_user_id." and date_paid = 0";
					$check_invoice_result = $db->Execute($this->sql_query);
					if ($this->debug_invoice)
						echo $this->sql_query."<br>\n";
					if (!$check_invoice_result)
					{
						if ($this->debug_invoice)
							echo $this->sql_query."<br>\n";
						return false;
					}
					elseif ($check_invoice_result->RecordCount() != 1)
					{
						if ($this->debug_invoice)
							echo $this->sql_query."<br>\n";
						return false;
					}
					$this->invoice_id = $invoice_id;

					//get total for invoice
					$this->get_invoice_total($db);

					$this->sql_query = "insert into ".$this->sell_table."
						(session,time_started,pay_invoice,price)
						values
						(\"".$this->session_id."\",".$this->shifted_time($db).",".$invoice_id.",".$this->invoice_total.")";
					$insert_sell_result = $db->Execute($this->sql_query);
					if ($this->debug_invoice)
						echo $this->sql_query."<br>\n";
					if (!$insert_sell_result)
					{
						if ($this->debug_invoice)
							echo $this->sql_query."<br>\n";
						return false;
					}
				}
				else
				{
					//there is no invoice id to pay
					return false;
				}
			}
		}
		else
		{
			//this user does not exist
			return false;
		}

		if ($this->debug_invoice)
		{
			echo $this->classified_user_id." is classified_user_id in Pay_invoice<br>\n";
		}
	} //end of function Pay_invoice

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_invoice_variables($info)
	{
		if (is_array($info))
		{
			reset ($info);
			foreach ($info as $key => $value)
			{
				$this->invoice_variables[$key] = $value;
				if ($this->debug_invoice)
					echo $key." is the key to - ".$value."<br>\n";
			}
		}

	} //end of get_invoice_variables

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function save_invoice_variables ($db)
	{
		$unique_key = substr(md5(uniqid(rand(),1)), 0,strlen($this->invoice_variables["cc_number"]));
		$encrypted_cc_num = Site::encrypt($this->invoice_variables["cc_number"], $unique_key);
		$this->sql_query = "update ".$this->sell_table." set
			payment_type = \"".$this->invoice_variables["payment_type"]."\",
			cc_number = \"$encrypted_cc_num\",
			decryption_key = \"$unique_key\",
			cc_exp_year = \"".$this->invoice_variables["cc_exp_year"]."\",
			cc_exp_month = \"".$this->invoice_variables["cc_exp_month"]."\",
			cvv2_code = \"".$this->invoice_variables["cvv2_code"]."\"
			where session = \"".$this->session_id."\" and pay_invoice = ".$this->invoice_id;
		$save_variable_result = $db->Execute($this->sql_query);
		if ($this->debug_invoice)
			echo $this->sql_query."<br>\n";
		if (!$save_variable_result)
		{
			//$this->body .=$this->sql_query."<br>\n";
			return false;
		}
	} //end of function save_invoice_variables

//####################################################################

	function remove_invoice_session($db,$account_session=0)
	{
		if ($account_session)
		{
			$this->sql_query = "delete from ".$this->sell_table." where session = \"".$account_session."\" and pay_invoice = ".$this->invoice_id;
			if ($this->debug_invoice)
				echo $this->sql_query."<Br>\n";
			$delete_account_result = $db->Execute($this->sql_query);
			if (!$delete_account_result)
			{
				return false;
			}
		}
	} //end of funciton remove_invoice_session

//####################################################################

	function invoice_form($db)
	{
		$this->page_id = 178;
		$this->get_text($db);
		//echo "hello from almost top of upgrade form<Br>\n";
		//get this users price plan specifics
		if ($this->debug_invoice)
		{
			echo $this->classified_user_id." is classified_user_id in invoice_form<br>\n";
			echo $this->user_data->ID." is the user id <BR>\n";
			echo $this->user_data->FIRSTNAME." is the user firstname <BR>\n";
		}
		if (($this->classified_user_id) && ($this->user_data) && ($this->get_invoice_info($db)))
		{
			if ($this->debug_invoice)
				echo "building form<bR>\n";
			$payment_types_accepted = $this->get_payment_types_accepted($db);
			if ($this->debug_invoice)
				echo $payment_types_accepted->RecordCount()." is the number of accepted payment types<BR>\n";
			if (($this->user_data) && ($payment_types_accepted))
			{
				$renewable = 0;
				$upgradeable = 0;
				$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=30 method=post>\n";
				$this->body .="<table cellpadding=0 cellspacing=1 border=0 align=center width=100%>\n";
				$this->body .="<tr class=page_title>\n\t<td  colspan=2>".urldecode($this->messages[3104])."</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t<td colspan=2>".urldecode($this->messages[3105])."</td>\n</tr>\n";

				$this->body .="<tr>\n\t<td colspan=2>\n\t";
				$this->body .="<table cellpadding=3 cellspacing=0 border=1 align=center width=100%>\n\t";

				//invoice number
				$this->body .="<tr>\n\t\t<td width=50% class=cost_field_labels >".urldecode($this->messages[3106])."</td>\n\t";
				$this->body .= "<td class=cost_data_values>".$this->invoice_id."</td></tr>";

				//invoice date
				$this->body .="<tr>\n\t\t<td width=50% class=cost_field_labels >".urldecode($this->messages[3107])."</td>\n\t";
				$this->body .= "<td class=cost_data_values>".date("M d,Y",$this->invoice_info->INVOICE_DATE)."</td></tr>";

				//invoice amount
				$this->body .="<tr>\n\t\t<td width=50% class=cost_field_labels >".urldecode($this->messages[3241])."</td>\n\t";
				$this->body .= "<td class=cost_data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->invoice_total)
					." ".$this->configuration_data['postcurrency']."</td></tr>";

				//??get all ads associated with invoice
				//??display dates of transaction

				$this->body .="</table>\n\t</td>\n</tr>\n";

				if (strlen($this->error_variables["choose"]) > 0)
					$this->body .="<tr>\n\t<td class=error_message colspan=3>".$this->error_variables["choose"]."</td>\n</tr>\n";

				//PAY CHOICE(S)
				$this->body .="<tr class=payment_choice_section_title>\n\t<td colspan=3>".urldecode($this->messages[3109])."</td>\n</tr>\n";
				$this->body .= "<tr><td colspan=2>\n\t";
				$this->body .="<table cellpadding=3 cellspacing=0 border=1 align=center width=100%>\n\t";
				$this->body .="<tr class=page_description >\n\t<td colspan=3>".urldecode($this->messages[3108]);
				if ($this->error_variables["payment_type"])
					$this->body .="<font class=error_message>".urldecode($this->messages[3110])."</font>";
				$this->body .="</td>\n</tr>\n";

				while ($show_payment = $payment_types_accepted->FetchNextObject())
				{
					switch ($show_payment->TYPE)
					{
						case 1:
						{
							//cash
							$this->body .="<tr>\n\t\t<td width=50%><font class=payment_choices_labels>".urldecode($this->messages[3111])."</font><br>
								<font class=payment_choices_descriptions>".urldecode($this->messages[3112])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] checked value=1>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=1 ";
								if ($this->invoice_variables["payment_type"] == 1) $this->body .="checked";
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
							$this->body .="<tr>\n\t\t<td  width=50%><font class=payment_choices_labels>".urldecode($this->messages[3113])."</font><br>
								<font class=payment_choices_descriptions>".urldecode($this->messages[3114])."</td>\n\t";
							if ($this->debug_invoice)
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
									if ($this->invoice_variables["payment_type"] == 2) $this->body .="checked";
									$this->body .="></td>\n\t<td width=40% align=center class=payment_choices_cc_number_values>&nbsp;\n\t";
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
								$this->body .= urldecode($this->messages[3115]);
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
												urldecode($this->messages[200106])."</a>";
									$this->body .= "
											</td>
											<td width=50% align=left>
												<input type=text name=c[cvv2_code] size=4>
											</td>
										</tr>";
								}
								$this->body .="
										<tr>
											<td width=50% align=right>".urldecode($this->messages[3116])."</td>
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
							$this->body .="<tr>\n\t\t<td  width=50%><font class=payment_choices_labels>".urldecode($this->messages[3117])."</font><br>
								<font class=payment_choices_descriptions>".urldecode($this->messages[3118])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=3>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=3 ";
								if ($this->invoice_variables["payment_type"] == 3) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}
						}
						break;
						case 4:
						{
							//money order
							$this->body .="<tr>\n\t\t<td width=50%><font class=payment_choices_labels>".urldecode($this->messages[3119])."</font><br>
								<font class=payment_choices_descriptions>".urldecode($this->messages[3120])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=4>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=4 ";
								if ($this->invoice_variables["payment_type"] == 4) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}
						}
						break;
						case 5:
						{
							//check
							$this->body .="<tr>\n\t\t<td width=50%><font class=payment_choices_labels>".urldecode($this->messages[3121])."</font><br>
								<font class=payment_choices_descriptions>".urldecode($this->messages[3122])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td  width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=5>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td  width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=5 ";
								if ($this->invoice_variables["payment_type"] == 5) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}
						}
						break;
						case 6:
						{
							//worldpay
							$this->body .="<tr>\n\t\t<td  width=50%><font class=payment_choices_labels>".urldecode($this->messages[3123])."</font><br>
								<font class=payment_choices_descriptions>".urldecode($this->messages[3124])."</td>\n\t";
							if ($payment_types_accepted->RecordCount() == 1)
							{
								//this is the only choice so has hidden variable saying this is the type requested
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=6>&nbsp;</td>\n\t</tr>\n\t";
							}
							else
							{
								//this is a choice among many
								$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=6 ";
								if ($this->invoice_variables["payment_type"] == 6) $this->body .="checked";
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
								if ($this->invoice_variables["payment_type"] == 8) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}*/
						}
						break;

					}
				}
				$this->body .="</table>\n\t</td>\n</tr>\n";

				$this->body .="<tr>\n\t<td align=center colspan=2>\n\t<br><input type=submit name=z class=submit_button value=\"".urldecode($this->messages[3125])."\">\n\t</td>\n</tr>\n";
				$this->body .="<tr>\n\t<td colspan=2 class=back_to_my_account_link><a href=".$this->configuration_data['classifieds_url']."?a=4&b=3>".urldecode($this->messages[3126])."</a></td>\n</tr>\n";
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

	} //end of function invoice_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_transaction_variables($db)
	{
		if ($this->debug_invoice) echo "checking transaction variables<Br>\n";
		$this->page_id = 178;
		$this->get_text($db);
		$this->error = 0;
		unset($this->error_variables);
		$this->error_variables = array();

		if ($this->debug_invoice) ECHO "<BR>PAYMENT TYPE IS -".$this->invoice_variables["payment_type"]."<BR>";
		if ($this->invoice_variables["payment_type"] == 2)
		{
			//cc_number
			//put verification script in
			$this->sql_query = "select cc_id from ".$this->cc_choices." where chosen_cc = 1";
			if ($this->debug_invoice)
				echo $this->sql_query." is the query <br>\n";
			$cc_choice_result = $db->Execute($this->sql_query);
			if (!$cc_choice_result)
			{
				if ($this->debug_invoice) echo "error checking cc configuration info 2<Br>\n";
				return false;
			}
			elseif ($cc_choice_result->RecordCount() == 1)
			{
				$show_cc_choice = $cc_choice_result->FetchNextObject();
				if (($show_cc_choice->CC_ID == 1) || ($show_cc_choice->CC_ID == 3) || ($show_cc_choice->CC_ID == 4) || ($show_cc_choice->CC_ID >= 6))
				{
					if (strlen(trim($this->invoice_variables["cc_number"])) == 0)
					{
						$this->error++;
						$this->error_variables["cc_number"] = urldecode($this->messages[3127]);
					}

					//check date of expiration
					$current_year = date("y");
					if (($this->invoice_variables["cc_exp_year"] < $current_year) || (($this->invoice_variables["cc_exp_year"] == $current_year)
						&& ($this->invoice_variables["cc_exp_month"] < date("m"))))
					{
						$this->error++;
						$this->error_variables["cc_expiration"] = urldecode($this->messages[3128]);
					}
				}
			}
			else
			{
				if ($this->debug_invoice) echo "error checking cc configuration info 2<Br>\n";
				return false;
			}
		}
		elseif (($this->invoice_variables["payment_type"] == 0) || (strlen(trim($this->invoice_variables["payment_type"])) == 0))
		{
			//some action must be taken
			$this->error++;
			$this->error_variables["choose"] = urldecode($this->messages[3110]);
		}

		if ($this->debug_invoice)
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
		$this->page_id = 179;
		$this->get_text($db);

		//save transaction data
		//save the transaction data overwriting
		//update transaction data into the classifieds table


		//get totals and taxes if any
		$this->body .= "<script language=\"javascript\">function formSubmit(fObj)
	{
	// ** validate form
	return true; // if form needs to be submitted. Otherwise: return false.
	}
	</script>";
		$this->body .= "<form onSubmit=\"if(formSubmit(this)) this.z.disabled=true; else return false;\" action=".$this->configuration_data['classifieds_file_name']."?a=30&d=final_accepted method=post>\n";
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[3129])."</td>\n</tr>\n";
		$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[3130])."</td>\n</tr>\n";
		$this->body .="<tr class=page_description>\n\t<td>";
		$user_data = $this->user_data;
		//check pay choice information
		switch ($this->invoice_variables["payment_type"])
		{
			case 1: //cash
						$this->body .= urldecode($this->messages[3131]);
					break;
					case 2: //credit card
						$this->body .= urldecode($this->messages[3132]);
					break;
					case 3: //paypal
						$this->body .= urldecode($this->messages[3133]);
					break;
					case 4: //money order
						$this->body .= urldecode($this->messages[3134]);
					break;
					case 5: //check
						$this->body .= urldecode($this->messages[3135]);
					break;
					case 6: //worldpay
						$this->body .= urldecode($this->messages[3136]);
					break;
					case 8: //NOCHEX
						//$this->body .= urldecode($this->messages[]);
					break;
					default:
						return false;
		} //end of switch ($this->invoice_variables["payment_type"])

		$this->body .= $payment_type_message."</td>\n</tr>\n";
		$this->body .="<tr>\n\t<td align=center>\n\t<table cellpadding=3 cellspacing=1>\n\t";


		//invoice number
		$this->body .="<tr>\n\t\t<td width=50% class=cost_field_labels >".urldecode($this->messages[3137])."</td>\n\t";
		$this->body .= "<td class=cost_data_values>".$this->invoice_id."</td></tr>";

		//invoice date
		$this->body .="<tr>\n\t\t<td width=50% class=cost_field_labels >".urldecode($this->messages[3138])."</td>\n\t";
		$this->body .= "<td class=cost_data_values>".date("M d,Y",$this->invoice_info->INVOICE_DATE)."</td></tr>";

		//invoice amount
		$this->body .="<tr>\n\t\t<td width=50% class=cost_field_labels >".urldecode($this->messages[3139])."</td>\n\t";
		$this->body .= "<td class=cost_data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->invoice_variables["price"])
			." ".$this->configuration_data['postcurrency']."</td></tr>";

		$this->body .="<tr>\n\t<td align=center class=complete_transaction_button><input type=submit name=z value=\"".urldecode($this->messages[3140])."\" class=complete_transaction_button>\n\t</td>\n</tr>\n";
		$this->body .="<tr>\n\t<td align=center><a href=".trim($this->configuration_data['classifieds_url'])."?a=4&b=3 class=back_to_my_currents_ads_link>".urldecode($this->messages[3141])."</a><br>";
		$this->body .="</td>\n</tr>\n";

		$this->body .="</table>\n";
		$this->body .="</form>\n";
		$this->display_page($db);
		return true;
	} //end of function final_approval_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function transaction_approved($db)
	{
		if ($this->debug_invoice)
			echo "hello from top of transaction_approved<br>\n";

		if ($this->debug_invoice)
		{
			echo $this->invoice_variables["price"]." is amount to charge in transaction_approved<br>\n";
		}

		switch ($this->invoice_variables["payment_type"])
		{
			case 1:
			{
				//cash
				//administrator must approve transaction
				//place in renewal transactions expecting funds or on hold
				$this->page_id = 180;
				$this->get_text($db);
				$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
				$this->body .= "<tr class=page_title><td>".urldecode($this->messages[3142])."</td>\n</tr>\n";
				$this->body .= "<tr class=page_description><td>".urldecode($this->messages[3143])."</td>\n</tr>\n";

				//display message saying the ad has not been renewed until payment
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[3144])." ".
				$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->invoice_variables["price"]).
					" ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[3145])." ".$this->invoice_id."</td>\n</tr>\n";

				$this->remove_invoice_session($db,$this->session_id);
				$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_url']."?a=4\" class=my_account_link >";
				$this->body .=urldecode($this->messages[3169])."</A>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
				$this->display_page($db);
				exit;
			}
			break;
			case 2:
			{
				//credit card
				//each credit card processor will have its own transaction handler
				//find the right credit card processor
				if ($this->debug_invoice)
					echo "hello from credit card<bR>\n";
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
				$this->page_id = 180;
				$this->get_text($db);
				$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
				$this->body .= "<tr class=page_title><td>".urldecode($this->messages[3142])."</td>\n</tr>\n";
				$this->body .= "<tr class=page_description><td>".urldecode($this->messages[3143])."</td>\n</tr>\n";
				//display message saying the ad has not been renewed until payment
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[3146])." ".
					$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->invoice_variables["price"]).
					" ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[3145])." ".$this->invoice_id."</td>\n</tr>\n";

				$this->remove_invoice_session($db,$this->session_id);
				$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_url']."?a=4\" class=my_account_link >";
				$this->body .=urldecode($this->messages[3169])."</A>\n\t</td>\n</tr>\n";
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
				$this->page_id = 180;
				$this->get_text($db);
				$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
				$this->body .= "<tr class=page_title><td>".urldecode($this->messages[3142])."</td>\n</tr>\n";
				$this->body .= "<tr class=page_description><td>".urldecode($this->messages[3143])."</td>\n</tr>\n";
				//display message saying the ad has not been renewed until payment
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[3147])." ".
					$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->invoice_variables["price"]).
					" ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
				$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[3145])." ".$this->invoice_id."</td>\n</tr>\n";
				$this->remove_invoice_session($db,$this->session_id);
				$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_url']."?a=4\" class=my_account_link >";
				$this->body .=urldecode($this->messages[3169])."</A>\n\t</td>\n</tr>\n";
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
		} //end of switch ($this->invoice_variables["payment_type"])
		return true;
	} //end of function transaction_approved

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_cc($db)
	{
		$this->sql_query = "select * from ".$this->cc_choices." where chosen_cc = 1";

		$cc_result = $db->Execute($this->sql_query);
		if ($this->debug_invoice)
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

	function get_invoice_info($db)
	{
		if ($this->debug_invoice) echo $this->invoice_id." is the invoice_id<bR>\n";
		if ($this->invoice_id)
		{
			$this->sql_query = "select * from ".$this->invoices_table." where invoice_id = ".$this->invoice_id;
			$invoice_result = $db->Execute($this->sql_query);
			if ($this->debug_invoice) echo $this->sql_query."<br>\n";
			if (!$invoice_result)
			{
				$this->body .=$this->sql_query."<br>\n";
				return false;
			}
			elseif ($invoice_result->RecordCount() == 1)
			{
				$this->invoice_info = $invoice_result->FetchNextObject();
				return true;;
			}
			else
			{
				return false;
			}
		}
		else
		{
			//there is no invoice id to check
			return false;
		}
	} //end of function get_invoice_info

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_invoice_total($db)
	{
		if ($this->invoice_id)
		{
			//get total for invoice
			$this->sql_query = "select * from  ".$this->balance_transactions."  where invoice_id = ".$this->invoice_id;
			if ($this->debug_invoices) echo $this->sql_query."<br>";
			$invoice_items_result = $db->Execute($this->sql_query);
			if (!$invoice_items_result)
			{
				return false;
			}
			elseif ($invoice_items_result->RecordCount() > 0)
			{
				$this->invoice_total = 0;
				while ($show_item = $invoice_items_result->FetchNextObject())
				{
					$this->invoice_total = $this->invoice_total + $show_item->AMOUNT;
				}
				return true;
			}
			else
			{
				//there is no items/ads attached to this invoice
				return false;
			}
		}
		else
			return false;
	} //end of function get_invoice_total

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Pay_invoice
?>
