<?// admin_group_management_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Payment_management extends Admin_site {

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Payment_management($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_payment_accepted_list($db)
	{
		$this->get_configuration_data($db);

		$this->sql_query = "select * from ".$this->classified_payment_choices_table." order by name";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=39&b=2 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Payments > Payment Types > Payments Accepted";
			$this->description = "Choose which payment choices you will
				allow a registrant to use to pay for a listing. Make certain to click the \"save\" button below after making your selection.";
			$this->body .= "<tr>\n\t<td align=center>\n\t<table cellpadding=3 cellspacing=1 border=0 class=row_color2 width=100%>\n";
			$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light>\n\t<b>accept</b>\n\t</td>\n\t";
			$this->body .= "\n\t\t<td class=medium_font_light>\n\t<b>payment type and description</b>\n\t\t</td>\n\t\t";
			$this->body .= "<td class=medium_font_light>\n\t&nbsp; \n\t\t</td>\n\t</tr>\n\t";

			$this->row_count = 0;
			while ($show = $result->FetchRow())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t\t<td class=medium_font>\n\t
					<input type=checkbox name=d[".$show["payment_choice_id"]."] value=1 ";
				if ($show["accepted"])
					$this->body .= "checked";
				$this->body .= "> \n\t\t</td>\n\t\t";
				$this->body .= "<td class=medium_font>\n\t<b>".$show["name"]."</b><br>".$show["explanation"]." </td>\n\t";

				$this->body .= "<td>";
				switch ($show["payment_choice_id"])
				{
					case 1: //paypal
						$this->body .= "<a href=index.php?a=39&b=3><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></td>";
						break;
					case 5: //credit card
						$this->body .= "<a href=index.php?a=39&b=4><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></td>";
						break;
					case 7: //site account
						$this->body .= "<a href=index.php?a=39&b=10><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></td>";
						break;
					case 8: //NOCHEX
						$this->body .= "<a href=index.php?a=39&b=11><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></td>";
						break;
					default: //cash, money order, check
						$this->body .= "&nbsp;</td>";
				}
				$this->body .= "</tr>\n\t";
				$this->row_count++;
			}
			$this->body .= "</table>\n";
			$this->body .= "</td>\n</tr>\n";
			$this->body .= "<td class=medium_font><table border=0><tr><td width=10%><input type=radio name=d[override] value=1 ";
			if ($this->configuration_data["site_balance_override"] == 1)
			{
				$this->body .= " checked ";
			}
			$this->body .= "><span class=medium_font><b>yes</b><BR>
				<input type=radio name=d[override] value=0 ";
			if ($this->configuration_data["site_balance_override"] == 0)
			{
				$this->body .= " checked ";
			}
			$this->body .= "><b>no</b></span></td>";
			$this->body .= "<td width=90% class=medium_font><b>Site Balance Override Switch</b><br>
				<font class=medium_font>This switch allows you to force your users to use the site balance system to place listings. When placing
				a listing, the only payment option will be site balance/invoice if you check \"yes\".  When adding money to the site balance, your clients
				will see all other payment options you check above.</td></tr></table></td></tr>";
			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=3 class=medium_font align=center><input type=submit name=save value=\"Save\"></td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";

			return true;
		}

	} //end of function display_payment_accepted_list

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_payment_accepted($db,$accepted=0)
	{
		if ($accepted)
		{
			if ((is_array($accepted)) && (count($accepted) > 0))
			{
				$this->sql_query = "update ".$this->classified_payment_choices_table." set accepted = 0";
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					//echo $this->sql_query."<br>\n";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				else
				{
					//set the use of site balance within the site configuration table
					$this->sql_query = "update ".$this->site_configuration_table." set
						use_account_balance = 0";
					//echo $this->sql_query."<br>\n";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						//echo $this->sql_query."<br>\n";
						$this->error_message = $this->internal_error_message;
						return false;
					}
					foreach ($accepted as $key => $value)
					{
						if (($key == 7) && ($value == 1))
						{
							//set the use of site balance within the site configuration table
							$this->sql_query = "update ".$this->site_configuration_table." set
								use_account_balance = 1";
							//echo $this->sql_query."<br>\n";
							$result = $db->Execute($this->sql_query);
							if (!$result)
							{
								//echo $this->sql_query."<br>\n";
								$this->error_message = $this->internal_error_message;
								return false;
							}
						}

						if($key == "override")
						{
							$this->sql_query = "update ".$this->site_configuration_table." set site_balance_override = ".$value;
							$result = $db->Execute($this->sql_query);
							//echo $this->sql_query.'<br>';
							if(!$result)
							{
								return false;
							}
							else
							{
								continue;
							}
						}

						$this->sql_query = "update ".$this->classified_payment_choices_table." set
							accepted = ".$value."
							where payment_choice_id = ".$key;
						//echo $this->sql_query."<br>\n";
						$result = $db->Execute($this->sql_query);
						if (!$result)
						{
							//echo $this->sql_query."<br>\n";
							$this->error_message = $this->internal_error_message;
							return false;
						}
					}
				}
			}
			//none accepted
			return true;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_payment_accepted

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function free_or_pay_ads_form($db)
	{
		$this->sql_query = "select all_ads_are_free from ".$this->site_configuration_table;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			$show=$result->FetchRow();
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=39&b=1 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Payments > Charge for Listings?";
			$this->description = "Choose whether or not you wish to charge sellers to place listings on your site.
				If you select \"yes\" you must also specify which \"payment types\" you will accept by using the \"Payment
				Types\" menu to the left.";

			$this->body .= "<tr>\n\t<td width=50% valign=middle align=right class=medium_font><b>charge for listings?:</b> </td>\n\t";
			$this->body .= "<td width=50% class=medium_font>\n\t<input type=radio name=c value=yes ";
			if ($show["all_ads_are_free"] == 0)
				$this->body .= "checked";
			$this->body .= ">yes<br><input type=radio name=c value=no ";
			if ($show["all_ads_are_free"] == 1)
				$this->body .= "checked";
			$this->body .= ">no \n\t</td>\n</tr>\n";
			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center>\n\t<input type=submit name=save value=\"Save\">\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>";
			return true;
		}
	} //end of function free_or_pay_ads_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_free_or_pay_ads($db,$free_or_not_info=0)
	{
		if ($free_or_not_info)
		{
			if ($free_or_not_info == "no")
				$charge_for_ads = 1;
			elseif ($free_or_not_info == "yes")
				$charge_for_ads = 0;

			$this->sql_query = "update ".$this->site_configuration_table." set
				all_ads_are_free = ".$charge_for_ads;
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			else
			{
				return true;
			}

		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_free_or_pay_ads

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function waiting_period_form($db)
	{
		$this->sql_query = "select payment_waiting_period from ".$this->site_configuration_table;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			$show=$result->FetchRow();
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=39&b=7 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Payment Management > Waiting Period";
			$this->description = "Choose the length
				of the holding period on the listing before this application automatically deletes it.  This affects all listings
				where cash, money order or check is the form of payment chosen.  The listing will be placed on hold for the amount
				of days below.  Within that time you can approve the listing and it will be made active.  If PayPal or a credit card
				was the chosen form of payment the listing will be placed on hold for this amount of time awaiting approval and deleted
				if approval is never received.";

			$this->body .= "<tr>\n\t<td width=50% valign=top align=right class=medium_font>waiting period in days </td>\n\t";
			$this->body .= "<td width=50% class=medium_font>\n\t<select name=c[waiting_period]>\n\t\t";
			for ($i = 1; $i < 32;$i++ )
			{
				$this->body .= "<option ";
				if ($show["payment_waiting_period"] == $i)
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t \n\t</td>\n</tr>\n";
			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center>\n\t<input type=submit name=save value=\"Save\">\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>";
			return true;
		}
	} //end of function waiting_period_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_waiting_period($db,$waiting_period_info=0)
	{
		if ($waiting_period_info)
		{
			$this->sql_query = "update ".$this->site_configuration_table." set
				payment_waiting_period = ".$waiting_period_info["waiting_period"];
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			else
			{
				return true;
			}

		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_waiting_period

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_paypal_form($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"PayPal email address id\", \"This is the email address id of your PayPal business or Premier PayPal account.\"]\n
			Text[2] = [\"url of company logo\", \"This image will appear on the paypal payment form as your company logo. The url you enter must be an absolute https url address not a relative url address. ie https://www.yoursite.com/image/someimage.jpg. Make sure the url is https so your users will not receive the non-secure page error while at paypal.com.\"]\n
			Text[3] = [\"title of item sent to PayPal\", \"This is the email address id of your PayPal Business or Premier PayPal account.\"]\n
			Text[4] = [\"currency type you accept at PayPal\", \"This is the currency you accept at PayPal. PayPal only accepts 5 different currencies at this time. You can specify which one of those you accept here if that happens to be different from the currency specified on your site.\"]\n
			Text[5] = [\"currency multiplier\", \"This the multiplier your total will be multiplied by to get the PayPal Currency Total for the current transaction.  If your site accept Mexican Pesos but you can only accept US Dollars through your PayPal account you must enter a rate multiplier to find the cost of the listing in US dollars.  Carry the exchange multiplier to 4 decimal places (ie 11.1111)\"]\n";

		//".$this->show_tooltip(5,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$this->sql_query = "select * from ".$this->site_configuration_table;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//echo $this->sql_query."<br>\n";
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			$show=$result->FetchRow();
			$this->title = "Payments > Payment Types > PayPal Setup";
			$this->description = "If using this payment type, please enter the data for your existing PAYPAL account using this page.";

			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=39&b=3 method=post>";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>
					<tr class=".$this->get_row_color().">
						<td align=right width=50% class=medium_font>
							PayPal email address id:".$this->show_tooltip(1,1)."
						</td>
						<td width=50%>
							<input type=text name=f[paypal_id] value=\"".$show["paypal_id"]."\">
						</td>
					</tr>";$this->row_count++;

			$this->body .= "
					<tr class=".$this->get_row_color().">
						<td align=right width=50% class=medium_font>
							url of company logo:".$this->show_tooltip(2,1)."
						</td>
						<td width=50%>
							<input type=text name=f[paypal_image_url] value=\"".$show["paypal_image_url"]."\">
						</td>
					</tr>";$this->row_count++;

			$this->body .= "
					<tr class=".$this->get_row_color().">
						<td align=right width=50% class=medium_font>
							title of item sent to PayPal:".$this->show_tooltip(3,1)."
						</td>
						<td width=50%>
							<input type=text name=f[paypal_item_label] value=\"".$show["paypal_item_label"]."\">
						</td>
					</tr>";$this->row_count++;

			$this->body .= "
					<tr class=".$this->get_row_color().">
						<td align=right width=50% class=medium_font>
							currency type you accept at PayPal:".$this->show_tooltip(4,1)."
						</td>
						<td width=50%>
							<select name=f[currency_type]>
								<option ".(($show["paypal_currency"] == "USD") ? "selected" : "")." value=\"USD\">
									US Dollars
								</option>
								<option ".(($show["paypal_currency"] == "EUR") ? "selected" : "")." value=\"EUR\">
									Euro
								</option>
								<option ".(($show["paypal_currency"] == "CAD") ? "selected" : "")." value=\"CAD\">
									Canadian Dollar
								</option>
								<option ".(($show["paypal_currency"] == "GBP") ? "selected" : "")." value=\"GBP\">
									Pound Sterling
								</option>
								<option ".(($show["paypal_currency"] == "JPY") ? "selected" : "")." value=\"JPY\">
									Yen
								</option>
								<option ".(($show["paypal_currency"] == "AUD") ? "selected" : "")." value=\"AUD\">
									Australian Dollar
								</option>
							</select>
						</td>
					</tr>";$this->row_count++;

			$this->body .= "
					<tr class=".$this->get_row_color().">
						<td align=right width=50% class=medium_font>
							currency multiplier:".$this->show_tooltip(5,1)."
						</td>
						<td width=50%>
							<input type=text name=f[currency_rate] value=\"".$show["paypal_currency_rate"]."\">
						</td>
					</tr>";$this->row_count++;
			if (!$this->admin_demo())
			{
				$this->body .= "
						<tr class=".$this->get_row_color().">
							<td colspan=2 class=medium_font align=center>
								<input type=submit name=save value=\"Save\">
							</td>
						</tr>";$this->row_count++;
			}

			$this->body .= "
					<tr class=row_color_black>
						<td colspan=2 class=medium_font_light>
							<b>PayPal Setup Instructions</b><br>
							To set up PayPal as a payment option for use with this software follow the steps below:</b>
						</td>
					</tr>";$this->row_count++;

			$this->body .= "
					<tr>
						<td colspan=100%>
							<table>
								<tr class=".$this->get_row_color().">
									<td align=right width=10% valign=top class=medium_font>
										<b>1)</b>
									</td>
									<td class=medium_font>
										Log in to your Business or Premier PayPal account.  You must have a Business or Premier account with
										PayPal to accept payments through PayPal.
									</td>
								</tr>";$this->row_count++;

			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=right width=10% valign=top class=medium_font>
										<b>2)</b>
									</td>
									<td class=medium_font>
										Go to the 'Profile' subtab.
									</td>
								</tr>";$this->row_count++;

			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=right width=10% valign=top class=medium_font>
										<b>3)</b>
									</td>
									<td class=medium_font>
										Click on the 'Instant Payment Notification Preferences' link in the 'Selling Preferences' column.
									</td>
								</tr>";$this->row_count++;

			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=right width=10% valign=top class=medium_font>
										<b>4)</b>
									</td>
									<td class=medium_font>
										Click Edit.
									</td>
								</tr>";$this->row_count++;

			$ipn_url = str_replace($show["classifieds_file_name"], "paypalipn.php",$show["classifieds_url"]);
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=right width=10% valign=top class=medium_font>
										<b>5)</b>
									</td>
									<td class=medium_font>
										Check the box and enter the following URL which is where this application will receive you IPN
										Notifications:<br>
										<b>".$ipn_url."</b>
									</td>
								</tr>";$this->row_count++;

			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=right width=10% valign=top class=medium_font>
										<b>6)</b>
									</td>
									<td class=medium_font>
										Click Save
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
					<tr>
						<td colspan=2 class=medium_font>
							<b>The PayPal process to complete a transaction:</b><br>
							The registrant enters their listing through the \"listing\" process.  Approves their listing,
							adds any extra features, and chooses PayPal as their form of payment.  When they accept final
							approval of the listing costs they are taken to	PayPal with specific payment information for
							your site appearing in the form.  They complete the transaction through	PayPal.  Once the
							funds hit your account an \"IPN\" message is sent to your site at the above address.  This
							application verifies the transaction with PayPal and if verified the listing is made \"active\".
						</td>
					</tr>";
			return true;
		}

	} //end of function display_paypal_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_paypal($db,$paypal_data)
	{
		$this->sql_query = "update ".$this->site_configuration_table." set
			paypal_currency = \"".$paypal_data["currency_type"]."\",
			paypal_currency_rate = \"".$paypal_data["currency_rate"]."\",
			paypal_image_url = \"".$paypal_data["paypal_image_url"]."\",
			paypal_item_label = \"".$paypal_data["paypal_item_label"]."\",
			paypal_id = \"".$paypal_data["paypal_id"]."\"";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//echo $this->sql_query."<br>\n";
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			return true;
		}
	} //end of function update_paypal

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function admin_payments_home()
		{
			$this->title = "Payment Management";
			$this->description = "Below are the Payment Management admin tools.  You can control what payments and if you want to accept them for registrants to place listings on your site.";

			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";

			$this->body .= "<tr class=row_color2>\n\t<td>\n\t<a href=index.php?a=39&b=6><span class=medium_font><b>currency designation</b></span></a>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tSet the currency symbol and description used throughout the site. \n\t</td>\n</tr>\n";

			$this->body .= "<tr>\n\t<td>\n\t<a href=index.php?a=39&b=1><span class=medium_font><b>charge for listings?</b></span></a>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tchoose whether you wish accept payment to place listings or allow any registrant to
				place listings for free. \n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color2>\n\t<td>\n\t<a href=index.php?a=39&b=2><span class=medium_font><b>types of payments accepted</b></span></a>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tchoose which types of payments you wish to accept as payment for placing a listing.
				. \n\t</td>\n</tr>\n";

			$this->body .= "<tr>\n\t<td>\n\t<a href=index.php?a=39&b=3><span class=medium_font><b>PayPal payment setup</b></span></a>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tInstructions for setting up your PayPal account and this application to accept PayPal. \n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color2>\n\t<td>\n\t<a href=index.php?a=39&b=8><span class=medium_font><b>Worldpay payment setup</b></span></a>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tInstructions for setting up your Worldpay account and this application to accept Worldpay. \n\t</td>\n</tr>\n";

			$this->body .= "<tr>\n\t<td>\n\t<a href=index.php?a=39&b=5><span class=medium_font><b>credit card payment setup</b></span></a>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tInstructions for setting up this application to accept through your merchant account by way of Authorize.net. \n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color2>\n\t<td>\n\t<a href=index.php?a=39&b=10><span class=medium_font><b>site balance setup</b></span></a>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tAllows you to configure site balance use. \n\t</td>\n</tr>\n";


			//echo "<tr>\n\t<td>\n\t<a href=index.php?a=39&b=7><span class=medium_font><b>Payment Waiting Period</b></span></a>\n\t</td>\n\t";
			//echo "<td class=medium_font>\n\tSet the length of time to wait for a payment before a listing is deleted from hold. \n\t</td>\n</tr>\n";

			//echo "<tr class=row_color2>\n\t<td>\n\t<a href=index.php?a=39&b=4><span class=medium_font><b>credit card payment setup</b></span></a>\n\t</td>\n\t";
			//echo "<td class=medium_font>\n\tSetup this application to accept credit card payments. \n\t</td>\n</tr>\n";

			$this->body .= "</table>\n";
			return true;
	} //end of function admin_payments_home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function currency_designation_form($db)
	{
		$this->sql_query = "select precurrency,postcurrency from ".$this->site_configuration_table;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//echo $this->sql_query."<br>\n";
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			$show=$result->FetchRow();
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=39&b=6 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Payment Management > Currency Designation";
			$this->description = "Edit the currency symbol that comes before and currency type that comes after any price within your site.";

			$this->body .= "<tr>\n\t<td align=right width=50% class=medium_font>\n\t<b>symbol \"before\"</b> ($): \n\t
				</td>\n\t";
			$this->body .= "<td width=50%>\n\t<input type=text name=h[precurrency] value=\"".$show["precurrency"]."\"></td>\n\t</tr>\n";

			$this->body .= "<tr>\n\t<td align=right width=50% class=medium_font>\n\t<b>currency type \"after\"</b> (USD,DM,...): \n\t
				</td>\n\t";
			$this->body .= "<td width=50%>\n\t<input type=text name=h[postcurrency] value=\"".$show["postcurrency"]."\"></td>\n\t</tr>\n";

			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center><input type=submit name=save value=\"Save\"></td>\n</tr>\n";
			$this->body .= "</table>\n";
			return true;
		}

	} //end of function currency_designation_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_currency_designation($db,$currency_type_info=0)
	{
		if ($currency_type_info)
		{
			$this->sql_query = "update ".$this->site_configuration_table." set
				precurrency = \"".$currency_type_info["precurrency"]."\",
				postcurrency = \"".$currency_type_info["postcurrency"]."\"";
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	} //end of function update_currency_designation

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_credit_card_choice_form($db)
	{
		$this->sql_query = "select * from $this->cc_choices order by name";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//echo $this->sql_query."<br>\n";
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=39&b=4 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Payments > Payment Types > Credit Card Setup";
			$this->description = "Use the form below to choose the appropriate credit card processing system you will be using.  To edit each processor's preferences click the \"edit\" button.";

			$this->body .= "<tr class=row_color_black>\n\t
				<td align=center width=10% class=medium_font_light>\n\t<b>select</b>\n\t</td>\n\t";
			$this->body .= "<td width=80% class=medium_font_light>\n\t<b>credit card processor</b> \n\t</td>\n\t";
			$this->body .= "<td align=center width=10% class=medium_font_light>\n\t&nbsp; \n\t</td>\n\t</tr>\n";

			$this->row_count=0;
			while($show=$result->FetchRow())
			{
				//SKIP BITAL UNTIL IT IS WORKING
				if ($show["cc_id"] == 3) continue;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t
					<td align=center width=10% class=medium_font>\n\t
					<input type=radio name=f value=".$show["cc_id"]." ";
				if ($show["chosen_cc"] == 1)
					$this->body .= "checked";
				$this->body .= "> \n\t</td>\n\t";
				$this->body .= "<td width=80% class=medium_font>\n\t<b>".$show["name"]."</b><br>".$show["explanation"]." \n\t</td>\n\t";
				if (!($show["cc_id"] == 9))
				{
					$this->body .= "<td align=center width=10%>\n\t
						<a href=index.php?a=39&b=5&c=".$show["cc_id"]."><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></a>
						\n\t</td>\n\t</tr>\n";
				}
				else
				{
					$this->body .= "<td></td></tr>";
				}
				$this->row_count++;
			}

			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=3 class=medium_font align=center><br><br><input type=submit name=save value=\"Save\"></td>\n</tr>\n";
			$this->body .= "</table>\n";
			return true;
		}

	} //end of function display_credit_card_choice_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_credit_card_choice($db,$choice_id=0)
	{
		if ($choice_id)
		{
			$this->sql_query = "update ".$this->cc_choices." set chosen_cc = 0";
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			else
			{
				$this->sql_query = "update ".$this->cc_choices." set chosen_cc = 1 where cc_id = ".$choice_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					//echo $this->sql_query."<br>\n";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				else
				{
					return true;
				}
			}
		}
		else
		{
			return false;
		}
	} //end of function update_credit_card_choice

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_worldpay_form($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"Worldpay installation id\", \"This the id you were given by Worldpay. This id will identify a user you submit to Worldpay to collect funds from.\"]\n
			Text[2] = [\"currency type you will use when submitting a payment to Worldpay\", \"From the dropdown list select the type of currency you accept within your site.\"]\n
			Text[3] = [\"Worldpay test mode switch\", \"Put your Worldpay payment into test mode to make sure all your settings are correct. If using test mode use - credit card value: 4000 0000 0000 0002, expiration date: within seven years, security code: 123\"]\n
			Text[4] = [\"Worldpay callback password\", \"This is the password that you can optionally set at Worldpay to verify that Worldpay is the one returning an authorization. You can leave this field blank and the password will not be checked on any Worldpay callback procedures.\"]\n";

		//".$this->show_tooltip(4,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$this->sql_query = "select * from ".$this->worldpay_configuration_table;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//echo $this->sql_query."<br>\n";
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			$show=$result->FetchRow();

			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=39&b=8 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Payments > Payment Types > Credit Card Setup > WorldPay";
			$this->description = "If using this payment type, please enter the data for your existing WORLDPAY account using this page.";

			$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\tWorldpay installation id:".$this->show_tooltip(1,1)."</td>\n\t";
			$this->body .= "<td valign=top>\n\t<input type=text name=e[worldpay_installation_id] value=\"".$show["worldpay_installation_id"]."\"></td>\n\t</tr>\n";

			$this->body .= "<tr class=row_color2>\n\t<td align=right class=medium_font>\n\tcurrency type you will use when submitting a payment to Worldpay:".$this->show_tooltip(2,1)."</td>\n\t";
			$this->body .= "<td valign=top>\n\t<select name=e[currency_type]>";
			$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 11 order by display_value";
			$currency_result = $db->Execute($this->sql_query);
			if (!$currency_result)
			{
				//echo $this->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($currency_result->RecordCount() > 0)
			{
				while ($show_currency = $currency_result->FetchRow())
				{
					$this->body .= "<option value=".$show_currency["value"];
					if ($show_currency["value"] == $show["currency_type"])
						$this->body .= " selected";
					$this->body .= " >".$show_currency["display_value"]."</option>\n\t";
				}
			}
			$this->body .= "</td>\n\t</tr>\n";

			$this->body .= "<tr>\n\t<td align=right width=50% class=medium_font>\n\tWorldpay test mode switch:".$this->show_tooltip(3,1)."</td>\n\t";
			$this->body .= "<td valign=top>\n\t<input type=radio name=e[test_mode] value=1 ";
			if ($show["test_mode"] == 1)
				$this->body .= "checked";
			$this->body .= "><span class=medium_font>test mode<br><input type=radio name=e[test_mode] value=0 ";
			if ($show["test_mode"] == 0)
				$this->body .= "checked";
			$this->body .= ">live mode</span></td>\n\t</tr>\n";

			$this->body .= "<tr>\n\t<td align=right width=50% class=medium_font>\n\tWorldpay callback password:".$this->show_tooltip(4,1)."</td>\n\t";
			$this->body .= "<td valign=top>\n\t<input type=text name=e[callback_password] value=\"".$show["callback_password"]."\"></td>\n\t</tr>\n";


			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center><input type=submit name=save value=\"Save\"></td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td colspan=2><table width=100% align=center><tr class=row_color_black>
				<td colspan=2 class=medium_font_light><b>Worldpay Setup Instructions</b><br>To use Worldpay as a
				form of payment follow the steps below:</b></td></tr>";

			$this->body .= "<tr class=row_color2>\n<td colspan=2 valign=top class=medium_font><b>Country codes for Worldpay:</b><Br>
			Make sure you use the correct country abbreviations for the countries you enter into your this application.
			Worldpay uses the 1994 version of ISO-3166 along with the two letter codes used in that specification.
			Get the countrys two letter codes that you use within your site from the following Worldpay country codes chart.
			<a href=http://support.worldpay.com/kb/integration_guides/junior/integration/help/appendicies/sjig_10300.html>Worldpay Country Codes</a><br>
			Enter the two letter code for any specific country as the abbreviation for that country within this application's Country Admin. </td>\n</tr>\n";
			$this->body .= "<tr class=row_color2>\n<td align=right width=10% valign=top class=medium_font><b>1)</b> </td>";
			$this->body .= "<td class=medium_font>\n\tLog in to your Worldpay account. </td>\n</tr>\n";

			$this->body .= "</table></td>\n</tr>\n";

			$this->body .= "<tr>\n\t<td colspan=2 class=medium_font><b>The Worldpay process to complete a transaction:</b><br>
				The registrant enters their ad through the \"listing\" process.  Approves their listing, adds any extra features,
				and chooses Worldpay as their form of payment.  When they accept final approval of the listing costs they are taken to
				Worldpay with specific payment information for your site appearing in the form.  They complete the transaction through
				the Worldpay form.  Once the funds hit your account a \"callback\" message is sent to your site.
				This application verifies the transaction information internally and makes the listing is made \"active\" if the
				payment was approved through Worldpay. </td>\n</tr>\n";
			$this->body .= "</table>\n";
			return true;
		}

	} //end of function display_worldpay_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_worldpay($db,$info=0)
	{
		if ($info)
		{
			$this->sql_query = "update ".$this->worldpay_configuration_table." set
				test_mode = \"".$info["test_mode"]."\",
				currency_type = \"".$info["currency_type"]."\",
				callback_password = \"".$info["callback_password"]."\",
				worldpay_installation_id = \"".$info["worldpay_installation_id"]."\"";
			//echo $this->sql_query."<br>\n";
			$worldpay_result = $db->Execute($this->sql_query);
			if (!$worldpay_result)
			{
				//echo $this->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			else
			{
				return true;
			}
		}
	} //end of function update_worldpay

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_instant_renewal_form($db)
	{
		$this->sql_query = "select * from ".$this->cc_choices;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//echo $this->sql_query."<br>\n";
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=39&b=4 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title .= "Payments > Payment Types > Credit Card Setup";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=3 class=medium_font_light>\n\tChoose which credit card processing system you use below.  To edit each processors preferences click the edit button
				next to them. \n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color_black>\n\t
				<td align=center width=10% class=medium_font_light>\n\tchoice \n\t</td>\n\t";
			$this->body .= "<td width=80% class=medium_font_light>\n\tcredit card processor \n\t</td>\n\t";
			$this->body .= "<td align=center width=10% class=medium_font_light>\n\t&nbsp; \n\t</td>\n\t</tr>\n";

			$this->row_count=0;
			while($show=$result->FetchRow())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t
					<td align=center width=10% class=medium_font>\n\t
					<input type=radio name=f value=".$show["cc_id"]." ";
				if ($show["chosen_cc"] == 1)
					$this->body .= "checked";
				$this->body .= "> \n\t</td>\n\t";
				$this->body .= "<td width=80% class=medium_font>\n\t<b>".$show["name"]."</b><br>".$show["explanation"]." \n\t</td>\n\t";
				$this->body .= "<td align=center width=10%>\n\t
					<a href=index.php?a=39&b=5&c=".$show["cc_id"]."><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></a>
					\n\t</td>\n\t</tr>\n";
				$this->row_count++;
			}

			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center><input type=submit name=save value=\"Save\"></td>\n</tr>\n";
			$this->body .= "</table>\n";
			return true;
		}

	} //end of function display_instant_renewal_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_display_instant_renewal_choice ($db,$renewal_choice=0)
	{
		if ($renewal_choice)
		{
			$this->sql_query = "update ".$this->cc_choices." set chosen_cc = 0";
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			else
			{
				$this->sql_query = "update ".$this->cc_choices." set chosen_cc = 1 where cc_id = ".$choice_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					//echo $this->sql_query."<br>\n";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				else
				{
					return true;
				}
			}
		}
		else
		{
			return false;
		}
	} //end of function update_credit_card_choice

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_site_balance_form($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"site balance configuration\", \"ONLY POSITIVE BALANCES ALLOWED...Checking here will force the user to put enought money into their site balance to cover their transactions.  If their balance is not enough to cover the cost of their transaction they will be asked to add money to their site balance if they wish to use site balance as a method of payment.  Otherwise they can use another method of payment for their transaction.  If negative balances are allowed the user will be able to charge listing placement charges to their account without funds being within their account.  They will be able to build up a \\\"negative\\\" balance they can then pay off within the client side admin or you can roll those charges into an invoice that you send to them.  Once charges are rolled into an invoice those charges are removed from the site balance.\"]\n
			Text[2] = [\"invoice system configuration\", \"This choice will NOT use the site balance system.  The client will accrue charges by placing their placement, renewal and subscription costs within the invoicing system.  They will not be able to pay for their charges until they are invoiced using the invoicing system. When you invoice the client through the invoice admin they will be notified by email and they will be able to inspect their invoice details within the client side account management module.  They can then choose to pay for their invoice through the client side account management tool or send payment to you directly if you wish.  Within invoice management you can set the number of days after invoiced without paying for the invoice before the account cannot place any more listings.\"]\n";

		//".$this->show_tooltip(1,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$this->sql_query = "select * from ".$this->site_configuration_table;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//echo $this->sql_query."<br>\n";
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			$show= $result->FetchRow();

			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=39&b=10 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Payments > Payments Types > Site Balance Setup";
			$this->description = "Edit your Site Balance parameters below.  You have two options. You can either require your users to pay for each listing, renewal, etc.
			through a running account (site balance) that they have with your site, or they can pay for their accumulated listings, renewals, etc. through an invoice
			that is generated by this software when you manually activate the invoicing process. If used, this is a site wide setting and will be displayed as an option
			for payment to the seller when placing a listing on your site.";

			$this->body .= "<tr>\n\t<td align=right width=50% class=medium_font>\n\t<b>use site balance: </b>".$this->show_tooltip(1,1)."</td>\n\t";
			$this->body .= "<td width=50%>\n\t<input type=radio name=e[positive_balances_only] value=1 ";
			if ($show["positive_balances_only"] == 1)
				$this->body .= "checked";
			$this->body .= "><span class=medium_font>positive balances allowed only</span></td></tr>";
			$this->body .= "<tr  class=row_color_red><td colspan=2 align=center class=large_font_light>---OR---</td></tr>";

			$this->body .= "<tr  class=row_color2><td align=right class=medium_font><b>use invoice system: </b>".$this->show_tooltip(2,1)."</td>";
			$this->body .= "<td class=medium_font>\n\t<input type=radio name=e[positive_balances_only] value=0 ";
			if ($show["positive_balances_only"] == 0)
				$this->body .= "checked";
			$this->body .= ">negative balances allowed </td>\n\t</tr>\n";
			if (!$this->admin_demo()) $this->body .= "<tr><td colspan=2 class=medium_font align=center><input type=submit name=save value=Save></td></tr>";
			$this->body .= "</table>\n";
			return true;
		}

	} //end of function display_site_balance_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_site_balance($db,$info=0)
	{
		if ($info)
		{
			$this->sql_query = "update ".$this->site_configuration_table." set
				positive_balances_only = \"".$info["positive_balances_only"]."\"";
			//echo $this->sql_query."<br>\n";
			$worldpay_result = $db->Execute($this->sql_query);
			if (!$worldpay_result)
			{
				//echo $this->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			else
			{
				return true;
			}
		}
	} //end of function update_site_balance

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

function display_nochex_form($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "
			Text[1] = [\"Test Mode\", \"If you wish to test your NOCHEX account to make sure everything is working correctly click \\\"demo mode\\\" to the right.  If you wish to go live click \\\"live\\\" to the right.  Placing in \\\"demo mode\\\" sends the exact same data but to the NOCHEX test server.\"]\n
			Text[2] = [\"Logo Path\", \"Place the path to the logo you want NOCHEX to display when the customer is redirected to the NOCHEX website during their payment process.\"]\n
			Text[3] = [\"Email\", \"This must be the email on file with your NOCHEX account.\"]\n";

		//".$this->show_tooltip(1,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$this->sql_query = "select * from ".$this->nochex_settings_table;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			echo $this->sql_query."<br>\n";
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			$show=$result->FetchRow();
			$this->title = "Payments > Payment Types > NOCHEX Setup";
			$this->description = "If using this payment type, please enter the data for your existing NOCHEX account using this page.";

			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=39&b=11 method=post>";
					$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>
						<tr class=row_color2>
							<td width=30% align=right class=medium_font>Test Mode:".$this->show_tooltip(1,1)."</td>
							<td valign=top class=medium_font><input type=radio name=e[demo_mode] value=0 ";
		if ($show["demo_mode"] == 0)
			$this->body .= "checked";
		$this->body .= "> live<br><input type=radio name=e[demo_mode] value=1 ";
		if ($show["demo_mode"] == 1)
			$this->body .= "checked";
		$this->body .= ">demo mode</td>
						</tr>
						<tr>
							<td align=right width=30% class=medium_font>Logo Path:".$this->show_tooltip(2,1)."</td>
							<td align=left width=70%><input size=70 type=text name=e[logo_path] value=\"".$show["logo_path"]."\"></td>
						</tr>
						<tr class=row_color2>
							<td align=right width=30% class=medium_font>Path to this software:</td>
							<td align=left width=70% class=medium_font><b>".substr(dirname(__FILE__),0,-6)."</b></td>
						</tr>
						<tr class=row_color1>
							<td align=right width=30% class=medium_font>Email:".$this->show_tooltip(3,1)."</td>
							<td align=left width=70%><input size=40 type=text name=e[email] value=\"".$show["email"]."\"></td>
						</tr>
						<tr class=row_color2>";
		if (!$this->admin_demo())
			$this->body .= "<td colspan=2 class=medium_font align=center><input type=submit name=save value=\"Save\"></td>";
			
		$this->body .= "		</tr>
					</table>
				</form>";

			return true;
		}

	} //end of function display_nochex_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_nochex($db,$nochex_data)
	{
		$this->sql_query = "update ".$this->nochex_settings_table." set
			demo_mode = \"".addslashes($nochex_data["demo_mode"])."\",
			logo_path = \"".addslashes($nochex_data["logo_path"])."\",
			email = \"".addslashes($nochex_data["email"])."\"";
		//echo $this->sql_query."<br>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//echo $this->sql_query."<br>\n";
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			return true;
		}
	} //end of function update_nochex

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Payment_management
?>