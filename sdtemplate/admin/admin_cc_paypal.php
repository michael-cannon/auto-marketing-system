<? //admin_cc_paypal.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

function display_cc_admin_form($db,&$admin_payments,&$site)
{
	$debug_cc_paypal = 0;
	if ($debug_cc_paypal)
	{
		echo "<font color=red>TO TEST USING A TEST ACCOUNT SET UP DURING DEVELOPMENT<br>
			WITH PAYPAL YOU CAN USE THE FOLLOWING INFORMATION<br>
			(THIS TEST ACCOUNT WAS STILL ACTIVE WITH PAYPAL ON 8/18/2005)<br><br>
			INSIDE <b>cc_initiate_paypal.php</b>
			<ul>
				<li>SET \$environment = 'Sandbox' INSTEAD OF 'Live'</li>
			</ul>";

	}
	$site->body .= "<SCRIPT language=\"JavaScript1.2\">";
	// Set title and text for tooltip
	$site->body .= "Text[1] = [\"PayPal API Username\", \"Your API username, which is auto-generated by PayPal when you apply for a digital certificate to use the PayPal Web Services API. You can see this value on https://www.paypal.com/ in your Profile under <b>API Access > API Certificate Information</b>.\"]\n,
		Text[2] = [\"PayPal API Password\", \"Your API password, which you specify when you apply for a digital certificate to use the PayPal Web Services API.\"]\n,
		Text[3] = [\"Path to Certificate\", \"The FULL path (including the file name) to your PayPal-issued digital certificate. To ensure security for your customers and your business, a public certificate and private key issued by PayPal are required for use of the PayPal Web Services API. The certificate file is in PEM format and contains both your private key and your public certificate. To obtain a PayPal Web Services API username, password, and digital certificate, you must first create a Business or Premier account and apply online at <b>https://www.paypal.com.</b>\"]\n,
		Text[4] = [\"Recommended Path\", \"For convenience, a directory is already set aside for your PayPal-issued digital certificate.  After placing the certificate in the recommended directory listed to the right, copy and paste the file path in the text box above and make sure the name of the file at the end of the file path matches the name of your PayPal-issued digital certificate.\"]\n,
		Text[5] = [\"PayPal Currency Codes\", \"PayPal-Supported Currencies and their Maximum Transaction Amounts<br><b>AUD</b> Australian Dollar 12,500 AUD<br><b>CAD</b> Canadian Dollar 12,500 CAD<br><b>EUR</b> Euro 8,000 EUR<br><b>GBP</b> Pound Sterling 5,500 GBP<br><b>JPY</b> Japanese Yen 1,000,000 JPY<br><b>USD</b> U.S. Dollar 10,000 USD<br>You must manually set any \"]\n,
		Text[6] = [\"Character Set\", \"A character set is a computer representation of all the individual possible letterforms or word symbols of a language. Listed are PayPal-Supported character sets:<br><b>ISO 8859-1</b> - West European languages (Latin-1) ISO 8859-1 is currently the most widely used.<br><b>US ASCII</b> - This set of 128 English characters were established by ANSI X3.4-1986 and is slowly being phased out due to it's limitations to the English language.<br><b>UTF-8</b> - Unicode Transformation Format-8. It is an octet (8-bit) lossless encoding of Unicode characters.\"]\n,
		Text[7] = [\"Required Fields\", \"Paypal requires these fields to be sent along with the credit card number and expiration date.  Therefore you must require these variables during the registration process.\"]\n
		Text[8] = [\"Enabling Extensions\", \"If you are unsure how to enable any of these extensions, see you server administrator.\"]\n
		Text[9] = [\"Installing PEAR packages\", \"If you are unsure how to install any of these packages, see you server administrator.\"]\n";



	//".$admin_payments->show_tooltip(4,1)."

	// Set style for tooltip
	//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
	$site->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
	$site->body .= "var TipId = \"tiplayer\"\n";
	$site->body .= "var FiltersEnabled = 1\n";
	$site->body .= "mig_clay()\n";
	$site->body .= "</script>";

	$admin_payments->sql_query = "select * from geodesic_cc_paypal";
	$cc_paypal_result = $db->Execute($admin_payments->sql_query);
	//echo $admin_payments->sql_query."<br>";
	if ($cc_paypal_result->RecordCount() > 1) {
		//echo $admin_payments->sql_query."<br>";
		$site->error_message = $site->internal_error_message;
		return false;
	} elseif ($cc_paypal_result->RecordCount() == 1) {
		$show = $cc_paypal_result->FetchRow();
		$currency_id = $show["currency_id"];
		$charset = $show["charset"];
	} else {
		//SET DEFAULT VALUES
		$currency_id = "USD";
		$charset = "iso-8859-1";
	}

	$site->title = "Payments > Payment Types > Credit Card Setup > PayPal Website Payments Pro";
	$site->description = "Change the necessary information to use PayPal credit card processing below.  You will also find instructions on
		what is needed to set up a Website Payments Pro Account and API Profile with PayPal.  For server setup procedures, see the instructions
		below or contact your server administrator.  PayPal Pro only accepts Master Card, Visa, American Express, and Discover.";

	if (!$site->admin_demo())$site->body .= "<form action=index.php?a=39&b=5&c=7 method=post>";
			$site->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>
				<tr class=row_color2>
					<td width=30% align=right class=medium_font>PayPal API Username:".$admin_payments->show_tooltip(1,1)."</td>
					<td valign=top class=medium_font><input size=30 type=text name=g[api_username] value=\"".@$show["api_username"]."\"></td>
				</tr>
				<tr class=row_color1>
					<td width=30% align=right class=medium_font>PayPal API Password:".$admin_payments->show_tooltip(2,1)."</td>
					<td valign=top class=medium_font><input size=30 type=password name=g[api_password] value=\"".@$show["api_password"]."\"></td>
				</tr>
				<tr class=row_color2>
					<td width=30% align=right class=medium_font>Path to Certificate:".$admin_payments->show_tooltip(3,1)."</td>
					<td valign=top class=medium_font><input size=80 type=text name=g[certfile] value=\"".@$show["certfile"]."\"></td>
				</tr>
				<tr class=row_color2>
					<td width=30% align=right class=medium_font><i>Recommended Path</i>:".$admin_payments->show_tooltip(4,1)."<br>";
	if (strpos(dirname(__FILE__),'/') !== FALSE)
		$site->body .= "
					<td valign=top class=medium_font><b>".substr(dirname(__FILE__),0,-6)."/Services/PayPal/cert/cert_key_pem.txt</b>";
	else
		$site->body .= "
					<td valign=top class=medium_font><b>".substr(dirname(__FILE__),0,-6)."\Services\PayPal\cert\cert_key_pem.txt</b>";
	$site->body .= "
					</td>
				</tr>";

	$site->body .= "
				<tr class=row_color1>
					<td width=30% align=right class=medium_font>PayPal Currency Codes:".$admin_payments->show_tooltip(5,1)."</td>
					<td valign=top class=medium_font>
						<select name=g[currency_id]>
							<option value=AUD ";
	if($currency_id == 'AUD')
		$site->body .= "selected";
	$site->body .= ">AUD - Australian Dollar</option>
							<option value=CAD ";
	if($currency_id == 'CAD')
		$site->body .= "selected";
	$site->body .= ">CAD - Canadian Dollar
							<option value=EUR ";
	if($currency_id == 'EUR')
		$site->body .= "selected";
	$site->body .= ">EUR - Euro
							<option value=GBP ";
	if($currency_id == 'GBP')
		$site->body .= "selected";
	$site->body .= ">GBP - Pound Sterling
							<option value=JPY ";
	if($currency_id == 'JPY')
		$site->body .= "selected";
	$site->body .= ">JPY - Japanese Yen
							<option value=USD ";
	if($currency_id == 'USD')
		$site->body .= "selected";
	$site->body .= ">USD - U.S. Dollar</option>
						</select>
					</td>
				</tr>
				<tr class=row_color2>
					<td width=30% align=right class=medium_font>Character Set:".$admin_payments->show_tooltip(6,1)."</td>
					<td valign=top class=medium_font>
						<select name=g[charset]>
							<option value='us-ascii' ";
	if($charset == 'us-ascii')
		$site->body .= "selected";
	$site->body .= ">US-ASCII</option>
							<option value='utf-8' ";
	if($charset == 'utf-8')
		$site->body .= "selected";
	$site->body .= ">UTF-8</option>
							<option value='iso-8859-1' ";
	if($charset == 'iso-8859-1')
		$site->body .= "selected";
	$site->body .= ">ISO-8859-1</option>
						</select>
					</td>
				</tr>
				<tr class=row_color_black>
					<td width=100% colspan=100% align=left class=medium_font_light>
						User Registration Requirements&nbsp;&nbsp;&nbsp;<a href=index.php?a=26&z=1>Click here to edit user registration requirements.</a>
					</td>
				</tr>
				<tr class=row_color2>
					<td width=100% colspan=100% align=left class=medium_font>
						Information required by PayPal Pro:".$admin_payments->show_tooltip(7,1)."
						<ul>
							<li>First Name</li>
							<li>Last Name</li>
							<li>Street Address</li>
							<li>City</li>
							<li>State/Province</li>
							<li>Country</li>
							<li>Zip</li>
						</ul>
					</td>
				</tr>
				<tr class=row_color_black>
					<td width=100% colspan=100% align=left class=medium_font_light>Server Setup Procedures</td>
				</tr>
				<tr class=row_color2>
					<td width=100% colspan=100% align=left class=medium_font>
						1.Enable the following extensions:".$admin_payments->show_tooltip(8,1)."
						<ul>
							<li>PHP Perl Compatible Regular Expressions extension for PHP 4.3.0+ and higher</li>
							<li>PHP cURL extension for PHP 4.3.0+ and higher with SSL support</li>
							<li>PHP OpenSSL extension for PHP 4.3.0+ and higher (for digital certificate transcoding)</li>
						</ul>
						2.Ensure that PEAR has the following required packages:".$admin_payments->show_tooltip(9,1)."
						<ul>
							<li>Net_URL</li>
							<li>Net_Socket</li>
							<li>HTTP_Request</li>
							<li>Log</li>
						</ul>
					</td>
				</tr>
				<tr>";
	if (!$site->admin_demo())
		$site->body .= "<td colspan=2 class=medium_font align=center><input type=submit name=save value=\"Save\"></td>";
	$site->body .= "	</tr>
			</table>
		</form>";
	return true;

} //end of function display_credit_card_choice_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_cc_admin($db,$admin_payments,$cc_info=0)
	{
		if ($cc_info)
		{
			$admin_payments->sql_query = "update geodesic_cc_paypal set
				api_username = ".$db->qstr($cc_info["api_username"]).",
				api_password = ".$db->qstr($cc_info["api_password"]).",
				certfile = ".$db->qstr($cc_info["certfile"]).",
				currency_id = ".$db->qstr($cc_info["currency_id"]).",
				charset = ".$db->qstr($cc_info["charset"])."";
			//echo $admin_payments->sql_query."<br>";
			$update_result = $db->Execute($admin_payments->sql_query);
			if (!$update_result)
			{
				//echo $admin_payments->sql_query."<br>";
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
