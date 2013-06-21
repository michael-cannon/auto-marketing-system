<? //admin_cc_authorizenet.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

function display_cc_admin_form($db,&$admin_payments,&$site)
{
	$site->body .= "<SCRIPT language=\"JavaScript1.2\">";
	// Set title and text for tooltip
	$site->body .= "Text[1] = [\"connection type\", \"SIM - This method can be used if cURL is not available on your server. SIM does require the installation of the \\\"mhash\\\" \\\"plugin\\\" or library installed and working on your server.  Connect to Authorize.net using one of the two methods.  AIM - This method is the most secure of all options but requires that your server have a non-standard PHP \\\"plugin\\\" or library installed on your server and configured within PHP. The library necessary is cURL. If you wish to use this method and have the cURL library installed and working properly on your server choose the AIM method.\"]\n
		Text[2] = [\"merchant login\", \"Merchant login id you were given to use within the ADC Relay Response message sent to Authorize.net in the live credit card transaction environment.\"]\n
		Text[3] = [\"merchant password\", \"<b>Only necessary if you are using the AIM method of connection.</b>  Password you use to log into your Merchant Account.\"]\n
		Text[4] = [\"transaction key\", \"<b>Only necessary if you are using the AIM method of connection.</b>  The gateway rejects all transactions that do not have a transaction key or that include an invalid key. The transaction key can be obtained from your Merchant Interface at Authorize.net. To obtain the transaction key from the Merchant Interface<br>1. Go to wwww.authorize.net and log into the Merchant Interface<br>2. Select Settings from the Main Menu<br>3. Click on Obtain Transaction Key in the Security section<br>4. Type in the answer to the secret question configured on setup<br>5. Click Submit.<br><br>It is strongly recommended that the merchant periodically change the transaction key. The merchant will have to disable the old key and generate a new key. The old key will be valid for 24 hours before it expires. To disable the old key on the Merchant Interface:<br>1. Log into the Merchant Interface<br>2. Select Settings from the Main Menu<br>3. Click on Obtain Transaction Key in the Security section<br>4. Type in the answer to the secret question configured on setup<br>5. Check the box that says Disable Old Key<br>6. Click Submit\"]\n
		Text[5] = [\"currency codes\", \"Below is a list of currency codes to send to Authorize.net. This is the currency you accept payments in.\"]\n
		Text[6] = [\"send authorize.net email to customer\", \"Choosing \\\"yes\\\" will have Authorize.net send the customer a receipt of the transaction.  You can customize the header and footer of that email in the customer email header and customer email footer sections below.\"]\n
		Text[7] = [\"send authorize.net email to admin\", \"Choosing \\\"yes\\\" will have Authorize.net send an email to the admin address set as the site email everytime a transaction is completed. RECOMMENDED \\\"YES\\\" AT FIRST TO MAKE SURE THAT YOUR TRANSACTIONS ARE COMPLETED CORRECTLY.\"]\n";

	//".$admin_payments->show_tooltip(7,1)."

	// Set style for tooltip
	//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
	$site->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
	$site->body .= "var TipId = \"tiplayer\"\n";
	$site->body .= "var FiltersEnabled = 1\n";
	$site->body .= "mig_clay()\n";
	$site->body .= "</script>";

	$admin_payments->sql_query = "select * from geodesic_cc_authorizenet";
	$authorize_result = $db->Execute($admin_payments->sql_query);
	//echo $admin_payments->sql_query."<br>\n";
	if (!$authorize_result)
	{
		//echo $admin_payments->sql_query."<br>\n";
		$site->error_message = $site->internal_error_message;
		return false;
	}
	elseif (($authorize_result->RecordCount() == 1) || ($authorize_result->RecordCount() == 0))
	{
		if ($authorize_result->RecordCount() == 0)
		{
			$admin_payments->sql_query = "insert into geodesic_cc_authorizenet
				(merchant_login,transaction_key,send_email_customer,send_email_merchant,header_email_receipt,footer_email_receipt)
				values
				(\"\",\"\",0,0,\"\",\"\")";
			$authorize_result = $db->Execute($admin_payments->sql_query);
		}
		$admin_payments->sql_query = "select transaction_key from geodesic_cc_authorizenet";
		$authorize_transaction_key_result = $db->Execute($admin_payments->sql_query);
		//echo $admin_payments->sql_query."<br>\n";
		if (!$authorize_transaction_key_result)
		{
			$admin_payments->sql_query = "ALTER TABLE geodesic_cc_authorizenet ADD transaction_key VARCHAR(50) NOT NULL AFTER merchant_login";
			$authorize_transaction_key_result = $db->Execute($admin_payments->sql_query);
			//echo $admin_payments->sql_query."<br>\n";
		}

		$admin_payments->sql_query = "select currency_code from geodesic_cc_authorizenet";
		$authorize_currency_code_result = $db->Execute($admin_payments->sql_query);
		//echo $admin_payments->sql_query."<br>\n";
		if (!$authorize_currency_code_result)
		{
			$admin_payments->sql_query = "ALTER TABLE geodesic_cc_authorizenet ADD currency_code VARCHAR(10) NOT NULL AFTER transaction_key";
			$authorize_currency_code_result = $db->Execute($admin_payments->sql_query);
			//echo $admin_payments->sql_query."<br>\n";
		}

		$admin_payments->sql_query = "select merchant_password from geodesic_cc_authorizenet";
		$authorize_password_result = $db->Execute($admin_payments->sql_query);
		//echo $admin_payments->sql_query."<br>\n";
		if (!$authorize_password_result)
		{
			$admin_payments->sql_query = "ALTER TABLE geodesic_cc_authorizenet ADD merchant_password tinytext NOT NULL AFTER currency_code";
			$authorize_password_result = $db->Execute($admin_payments->sql_query);
			//echo $admin_payments->sql_query."<br>\n";
		}

		$admin_payments->sql_query = "select merchant_type from geodesic_cc_authorizenet";
		$authorize_merchant_type_result = $db->Execute($admin_payments->sql_query);
		//echo $admin_payments->sql_query."<br>\n";
		if (!$authorize_merchant_type_result)
		{
			$admin_payments->sql_query = "ALTER TABLE geodesic_cc_authorizenet ADD merchant_type int(11) NOT NULL DEFAULT 1 AFTER merchant_password";
			$authorize_merchant_type_result = $db->Execute($admin_payments->sql_query);
			//echo $admin_payments->sql_query."<br>\n";
		}

		$admin_payments->sql_query = "select connection_type from geodesic_cc_authorizenet";
		$authorize_connection_type_result = $db->Execute($admin_payments->sql_query);
		//echo $admin_payments->sql_query."<br>\n";
		if (!$authorize_connection_type_result)
		{
			$admin_payments->sql_query = "ALTER TABLE geodesic_cc_authorizenet ADD connection_type int(11) NOT NULL DEFAULT 1 AFTER merchant_type";
			$authorize_connection_type_result = $db->Execute($admin_payments->sql_query);
			//echo $admin_payments->sql_query."<br>\n";
		}

		$admin_payments->sql_query = "select * from geodesic_classifieds_configuration";
		$site_result = $db->Execute($admin_payments->sql_query);
		//echo $admin_payments->sql_query."<br>\n";
		if (!$site_result)
		{
			//echo $admin_payments->sql_query."<br>\n";
			$site->error_message = $site->internal_error_message;
			return false;
		}
		elseif ($site_result->RecordCount() == 1)
		{
			$show_site = $site_result->FetchRow();
		}
		else
		{
			return false;
		}
		if (!$site->admin_demo())$site->body .= "<form action=index.php?a=39&b=5&c=1 method=post>\n";
		$site->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
		$site->title = "Payments > Payment Types > Credit Card Setup > Authorize.net";
		$site->description = "Change the necessary information to use Authorize.net credit card processing below.  You will also find instructions on
			what is needed to set up your merchant account with Authorize.net.";

		$show=$authorize_result->FetchRow();
		$site->body .= "<tr class=row_color1>\n\t
			<td width=60% align=right class=medium_font>\n\tconnection type:".$admin_payments->show_tooltip(1,1)."<br>
			<span class=small_font>For SIM, consult the <b><a href=http://mhash.sourceforge.net/>mhash</a></b> site and your internet host for more information.<br>
			For AIM, consult the <b><a href=http://curl.haxx.se>cURL</a></b> site and your internet host for more information.
			</span></td>\n\t";
		$site->body .= "<td valign=top class=medium_font><input type=radio name=g[connection_type] value=1 ";
		if ($show["connection_type"] == 1)
			$site->body .= "checked";
		$site->body .= "> SIM<br><input type=radio name=g[connection_type] value=2 ";
		if ($show["connection_type"] == 2)
			$site->body .= "checked";
		$site->body .= "> AIM</font>\n\t</td>\n</tr>\n";

		$site->body .= "<tr class=row_color2>\n\t
			<td width=50% align=right class=medium_font>\n\tmerchant login:".$admin_payments->show_tooltip(2,1)."</td>\n\t";
		$site->body .= "<td valign=top class=medium_font><input type=text name=g[merchant_login] value=\"".$show["merchant_login"]."\"></font>\n\t</td>\n</tr>\n";

		$site->body .= "<tr class=row_color1>\n\t
			<td width=50% align=right class=medium_font>\n\tmerchant password:".$admin_payments->show_tooltip(3,1)."</td>\n\t";
		$site->body .= "<td valign=top class=medium_font><input type=password name=g[merchant_password] value=\"".$show["merchant_password"]."\"></font>\n\t</td>\n</tr>\n";


		$site->body .= "<tr class=row_color2>\n\t
			<td width=50% align=right class=medium_font>\n\ttransaction key:".$admin_payments->show_tooltip(4,1)."</td>\n\t";
		$site->body .= "<td valign=top class=medium_font><input type=text name=g[transaction_key] value=\"".$show["transaction_key"]."\"></font>\n\t</td>\n</tr>\n";

		$site->body .= "<tr class=row_color1>\n\t
			<td width=50% align=right class=medium_font>\n\tcurrency codes:".$admin_payments->show_tooltip(5,1)."</td>\n\t";


		$site->body .= "<td valign=top class=medium_font>";
		$admin_payments->sql_query = "select * from geodesic_choices where type_of_choice = 75";
		$currency_result = $db->Execute($admin_payments->sql_query);
		//echo $admin_payments->sql_query."<br>\n";
		if (!$currency_result)
		{
			echo $admin_payments->sql_query."<br>\n";
			//return false;
		}
		elseif ($currency_result->RecordCount() > 0)
		{
			$site->body .= "<select name=g[currency_code]>\n\t\t";
			while ($show_currency = $currency_result->FetchRow())
			{
				$site->body .= "<option value=\"".$show_currency["value"]."\" ";
				if ($show["currency_code"] == $show_currency["value"])
					$site->body .= "selected";
				$site->body .= ">".$show_currency["display_value"]." - ".$show_currency["value"]."</option>\n\t\t";
			}
			$site->body .= "</select>";
		}
		$site->body .= "</font>\n\t</td>\n</tr>\n";

		$site->body .= "<tr class=row_color2>\n\t
			<td width=50% align=right class=medium_font>\n\tsend authorize.net email to customer:".$admin_payments->show_tooltip(6,1)."</td>\n\t";
		$site->body .= "<td valign=top class=medium_font><input type=radio name=g[send_email_customer] value=1 ";
		if ($show["send_email_customer"] == 1)
			$site->body .= "checked";
		$site->body .= ">yes<BR>
			<input type=radio name=g[send_email_customer] value=0 ";
		if ($show["send_email_customer"] == 0)
			$site->body .= "checked";
		$site->body .= ">no</font>\n\t</td>\n</tr>\n";

		$site->body .= "<tr class=row_color1>\n\t
			<td width=50% align=right class=medium_font>\n\tsend authorize.net email to admin:".$admin_payments->show_tooltip(7,1)."</td>\n\t";
		$site->body .= "<td valign=top class=medium_font><input type=radio name=g[send_email_merchant] value=1 ";
		if ($show["send_email_merchant"] == 1)
			$site->body .= "checked";
		$site->body .= ">yes<BR>
			<input type=radio name=g[send_email_merchant] value=0 ";
		if ($show["send_email_merchant"] == 0)
			$site->body .= "checked";
		$site->body .= ">no</font>\n\t</td>\n</tr>\n";
		//echo "<tr>\n\t
		//	<td width=50% align=right valign=top class=medium_font>customer email header</font><br>
		//	<span class=small_font>Whatever is entered at the right will appear at the top of the email sent to the
		//	customer from Authorize.net.  You can also set this in your Authorize.net merchant administration.</span>\n\t</td>\n\t";
		//echo "<td valign=top class=medium_font>
		//	<TEXTAREA name=g[header_email_receipt] cols=30 rows=5>".$show["header_email_receipt"]."</textarea></td>\n</tr>\n";

		//echo "<tr class=row_color2>\n\t
		//	<td width=50% align=right valign=top class=medium_font>customer email footer<br>
		//	<span class=small_font>Whatever is entered at the right will appear at the bottom of the email sent to the
		//	customer from Authorize.net.  You can also set this in your Authorize.net merchant administration.</span>\n\t</td>\n\t";
		//echo "<td valign=top class=medium_font>
		//	<TEXTAREA name=g[footer_email_receipt] cols=30 rows=5>".$show["footer_email_receipt"]."</textarea></td>\n</tr>\n";
		if (!$site->admin_demo())
			$site->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center><input type=submit name=save value=\"Save\"></td>\n</tr>\n";

		$site->body .= "<tr class=row_color_black>\n\t
			<td colspan=2 class=medium_font_light>\n\t<b>Authorize.net Setup Instructions</b><br>Configurations for use with <b>SIM</b> connection method.</font>\n\t</td>\n</tr>\n";
		$site->body .= "<tr>\n\t<td colspan=2 class=medium_font>\n\tWith the SIM integration you must first obtain a Transaction
			Key from your Authorize.net Merchant Interface following the steps below.<br><br>
			1) Log into the Merchant Interface<br><br>
			2) Select Settings from the Main Menu<br><br>
			3) Click on the Obtain Transaction Key in the Security section<br><br>
			4) Type in the answer to your secret question (The secret question and answer is setup
			during account activation. It is required to authenticate the merchant before the
			transaction key is generated.)<br><br>
			5) Click Submit<br><br>
			6) Insert the transaction key obtained into the transaction key space above.</font></td></tr>";
		$site->body .= "<tr>\n\t<td colspan=2 class=medium_font>\n\tBy completing the next set of instructions you are
			allowing GeoClassifieds to talk to your Authorize.net account asking for a transaction approval against a users account.<br><br>
			7) Log into the Merchant Interface (if you haven't already)<br><br>
			8) Select Settings from the Main Menu<br><br>
			9) Click on the Response /Receipt URL link in the Transaction Response section<br><br>
			10) Click on the Add URL link<br><br>
			11) Add the url on the next line<br>";
			$cc_url = str_replace($show_site["classifieds_file_name"], "cc_process_authorizenet.php",$show_site["classifieds_url"]);
		$site->body .= "<b>".$cc_url."</b><br><Br>
			12) Click Submit<br><br>";
		$site->body .= "<tr>\n\t<td colspan=2 class=medium_font>\n\tThe following setting allows Authorize.net to communicate
			results of the transaction back to GeoClassifieds with information approving or disproving the users transaction (thus turning it \"on\"
			automatically.<br><br>
			13) Insert your merchant login id in the field above.<Br><Br>\n
			14) Log into the Merchant Interface<br><br>
			15) Select Settings from the Main Menu<br><br>
			16) Click on Relay Response in the Transaction Response section<br><br>
			17) Enter the URL below so that Authorize.net will respond to GeoClassifieds when the transaction is approved.<br>";
			$cc_url = str_replace($show_site["classifieds_file_name"], "cc_process_authorizenet.php",$show_site["classifieds_url"]);
		$site->body .= "<b>".$cc_url."</b><br><Br>	";
		$site->body .= "18) You can test the process by placing your account in test mode.  In the \"General\" section after clicking the \"Settings\"
			menu link you will find a \"Test Mode\" link, click it.   Click the submit button until your account is in test mode.  You can
			then run test transactions to make sure everything works on your site.  Use the test credit card numbers below if you need:<Br>
			master card 5424000000000015<br>
			visa 4007000000027<br>
			discover 6011000000000012<br>
			amex 370000000000002<br>
			these card numbers will return declined if used in live mode.<br><br>
			19) <b>When you are satisfied that everything is working properly make sure to turn the test mode off in your Authorize.net
			account</b><br><br>";
		$site->body .= "</font>\n\t</td>\n</tr>\n";
		$site->body .= "<tr class=row_color_black>\n\t
			<td colspan=2 class=medium_font_light>\n\t<b>Authorize.net Setup Instructions</b><br>Configurations for use with <b>AIM</b> connection method.
			</font>\n\t</td>\n</tr>\n";

		$site->body .= "<tr>\n\t<td colspan=2 class=medium_font>\n\t1) If you had used another connection method before using
			the AIM method remove all of those settings from your Merchant Interface.  The AIM Method only needs one settings for it to work
			properly.  Remove all Relay Response, Response/Receipt URLS, Weblink (if visible) and Receipt Page settings.  These will cause conflicts.<br>
			2) If you are using the AIM method of connection click on the direct response and make sure \"Delimited Response\" is set to \"Yes\"
			and the \"Default Field Separator\" is set to \"| (pipe)\".  Leave the \"Field Encapsulation Character\" field empty.<br><Br>
			3) Click Submit<br><br>
			4) Make sure you enter your merchant account password in the proper field above.";

		$site->body .= "</table>\n";
		return true;
	}
	else
	{
		return false;
	}
} //end of function display_credit_card_choice_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_cc_admin($db,$admin_payments,$cc_info=0)
	{
		if ($cc_info)
		{
			$admin_payments->sql_query = "update geodesic_cc_authorizenet set
				merchant_login = \"".$cc_info["merchant_login"]."\",
				merchant_password = \"".$cc_info["merchant_password"]."\",
				merchant_type = \"".$cc_info["merchant_type"]."\",
				connection_type = \"".$cc_info["connection_type"]."\",
				transaction_key = \"".$cc_info["transaction_key"]."\",
				currency_code = \"".$cc_info["currency_code"]."\",
				header_email_receipt = \"".$cc_info["header_email_receipt"]."\",
				footer_email_receipt = \"".$cc_info["footer_email_receipt"]."\",
				send_email_merchant = \"".$cc_info["send_email_merchant"]."\",
				send_email_customer = \"".$cc_info["send_email_customer"]."\"";
			//echo $admin_payments->sql_query."<br>\n";
			$update_result = $db->Execute($admin_payments->sql_query);
			if (!$update_result)
			{
				//echo $admin_payments->sql_query."<br>\n";
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
	} //end of function update_credit_card_choice

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
?>