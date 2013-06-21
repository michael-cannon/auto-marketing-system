<? //admin_cc_payflow_pro.php
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
	$site->body .= "Text[1] = [\"Partner\", \"This is the Verisign partner. The default value (VeriSign) should be used for the test account, or if you signed up yourself for your account.\"]\n
		Text[2] = [\"Vendor\", \"This is your vendor name, defined at registration time at Verisign.\"]\n
		Text[3] = [\"User\", \"This is your user name, defined at registration time at Verisign.  If you do not place anything in this box, then the Vendor name will be used.\"]\n
		Text[4] = [\"Password\", \"This is your password, defined at registration time at Verisign.\"]\n
		Text[5] = [\"Test Mode\", \"If you wish to test your payflow_pro account to make sure everything is working correctly click \\\"demo mode\\\" to the right.  If you wish to go live click \\\"live\\\" to the right.  Placing in \\\"demo mode\\\" sends the exact same data but to the payflow pro test server.\"]\n
		Text[6] = [\"Test Credit Card Numbers\", \"Use these cc numbers to test transactions within the demo mode.\"]\n
		Text[7] = [\"Test Credit Card Reactions\", \"If you want to test the credit card transaction results use the amount testing mechanism listed here.\"]\n";


	//".$admin_payments->show_tooltip(5,1)."

	// Set style for tooltip
	//$site->body .= "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
	$site->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
	$site->body .= "var TipId = \"tiplayer\"\n";
	$site->body .= "var FiltersEnabled = 1\n";
	$site->body .= "mig_clay()\n";
	$site->body .= "</script>";

	$admin_payments->sql_query = "select * from geodesic_cc_payflow_pro";
	$payflow_pro_result = $db->Execute($admin_payments->sql_query);
	//echo $admin_payments->sql_query."<br>\n";
	if (!$payflow_pro_result)
	{
		echo $admin_payments->sql_query."<br>\n";
		$this->error_message = $this->internal_error_message;
		return false;
	}
	elseif ($payflow_pro_result->RecordCount() == 1)
	{
		$admin_payments->sql_query = "select * from geodesic_classifieds_configuration";
		$site_result = $db->Execute($admin_payments->sql_query);
		$admin_payments->sql_query."<br>\n";
		if (!$site_result)
		{
			//echo $admin_payments->sql_query."<br>\n";
			$this->error_message = $this->internal_error_message;
			return false;
		}
		elseif ($site_result->RecordCount() == 1)
		{
			$show_site = $site_result->FetchRow();
		}
		else
			return false;
		$site->title = "Payments > Payment Types > Credit Card Setup > Verisign Payflow Pro";
		$site->description = "Change the necessary information to use Payflow Pro credit card processing below.  You must sign up for a merchant account with Verisign in order to use Payflow Pro";
		$show=$payflow_pro_result->FetchRow();

		$site->body .= "<tr><td>";
			if (!$site->admin_demo())$site->body .= "<form action=index.php?a=39&b=5&c=6 method=post>";
				$site->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>
					<tr class=row_color1>\n\t
						<td width=30% align=right class=medium_font>Partner:".$admin_payments->show_tooltip(1,1)."</td>
						<td valign=top class=medium_font><input type=text name=g[partner] value=\"".$show["partner"]."\"></td>
					</tr>
					<tr class=row_color2>
						<td width=30% align=right class=medium_font>Vendor:".$admin_payments->show_tooltip(2,1)."</td>
						<td valign=top class=medium_font><input type=text name=g[vendor] value=\"".$show["vendor"]."\"></td>
					</tr>
					<tr class=row_color1>
						<td width=30% align=right class=medium_font>User:".$admin_payments->show_tooltip(3,1)."</td>
						<td valign=top class=medium_font><input type=text name=g[user] value=\"".$show["user"]."\"></td>
					</tr>
					<tr class=row_color2>
						<td width=30% align=right class=medium_font>Password:".$admin_payments->show_tooltip(4,1)."</td>
						<td valign=top class=medium_font><input type=password name=g[password] value=\"".$show["password"]."\"></td>
					</tr>
					<tr class=row_color1>
						<td width=30% align=right class=medium_font>Test Mode:".$admin_payments->show_tooltip(5,1)."</td>
						<td valign=top class=medium_font><input type=radio name=g[demo_mode] value=0 ";
		if ($show["demo_mode"] == 0)
			$site->body .= "checked";
		$site->body .= "> live<br><input type=radio name=g[demo_mode] value=1 ";
		if ($show["demo_mode"] == 1)
			$site->body .= "checked";
		$site->body .= ">demo mode</td>
					</tr>
					<tr class=row_color2>
						<td width=30% align=right valign=top class=medium_font>Test Credit Card Numbers:".$admin_payments->show_tooltip(6,1)."</td>
						<td valign=top class=medium_font>
							<ul>
								<li><b>American Express</b> 378282246310005</li>
								<li><b>Amex Corporate</b> 378734493671000</li>
								<li><b>Australian BankCard</b> 5610591081018250</li>
								<li><b>Diners Club</b> 30569309025904</li>
								<li><b>Discover</b> 6011111111111117</li>
								<li><b>JCB</b> 3530111333300000</li>
								<li><b>MasterCard</b> 5555555555554444</li>
								<li><b>Visa</b> 4111111111111111</li>
								<li>Switch/Solo (Paymentech) 6331101999990016</li>
							</ul>
						</td>
					</tr>
					<tr class=row_color1>
						<td width=30% align=right valign=top class=medium_font>Test Credit Card Reactions:".$admin_payments->show_tooltip(7,1)."</td>\n\t
						<td valign=top class=medium_font>
							<table cellpadding=3 cellspacing=1 border=1>
								<tr>
									<td>amount of transaction</td><td>type of result testing for</td>
								</tr>
								<tr><td>$0-$1000</td><td>Approved</td></tr>
								<tr>
								</tr>
								<tr>
									<td>$2001+</td><td>Declined</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>\n\t";
		if (!$site->admin_demo())
			$site->body .= "<td colspan=2 class=medium_font align=center><input type=submit name=save value=\"Save\"></td>";
		$site->body .= "	</tr>
				</table>
			</form></td></tr>";
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
		$admin_payments->sql_query = "update geodesic_cc_payflow_pro set
			partner = \"".$cc_info["partner"]."\",
			vendor = \"".$cc_info["vendor"]."\",
			user = \"".$cc_info["user"]."\",
			demo_mode = ".$cc_info["demo_mode"].",
			password = \"".$cc_info["password"]."\"";
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