<? //admin_cc_2checkout.php
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
	$site->body .= "Text[1] = [\"demo mode\", \"If you wish to test your 2Checkout account to make sure everything is working correctly, choose \\\"demo mode\\\".  If you wish to go live, choose \\\"live\\\".\"]\n";

	//".$admin_payments->show_tooltip(1,1)."

	// Set style for tooltip
	//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
	$site->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
	$site->body .= "var TipId = \"tiplayer\"\n";
	$site->body .= "var FiltersEnabled = 1\n";
	$site->body .= "mig_clay()\n";
	$site->body .= "</script>";

	$admin_payments->sql_query = "select * from geodesic_cc_twocheckout";
	$checkout_result = $db->Execute($admin_payments->sql_query);
	//echo $admin_payments->sql_query."<br>\n";
	if (!$checkout_result)
	{
		echo $admin_payments->sql_query."<br>\n";
		$site->error_message = $site->internal_error_message;
		return false;
	}
	elseif (($checkout_result->RecordCount() == 1) || ($checkout_result->RecordCount() == 0))
	{
		if ($checkout_result->RecordCount() == 0)
		{
			$admin_payments->sql_query = "insert into geodesic_cc_twocheckout
				(sid,demo_mode)
				values
				(\"\",0)";
			$checkout_result = $db->Execute($admin_payments->sql_query);

			$admin_payments->sql_query = "select * from geodesic_cc_twocheckout";
			$checkout_result = $db->Execute($admin_payments->sql_query);
			//echo $admin_payments->sql_query."<br>\n";
			if (!$checkout_result)
			{
				echo $admin_payments->sql_query."<br>\n";
				$site->error_message = $site->internal_error_message;
				return false;
			}
		}

		$admin_payments->sql_query = "select * from geodesic_classifieds_configuration";
		$site_result = $db->Execute($admin_payments->sql_query);
		//echo $admin_payments->sql_query."<br>\n";
		if (!$site_result)
		{
			echo $admin_payments->sql_query."<br>\n";
			$site->error_message = $site->internal_error_message;
			return false;
		}
		elseif ($site_result->RecordCount() == 1)
		{
			$show_site = $site_result->FetchRow();
		}
		else
			return false;
		if (!$site->admin_demo())$site->body .= "<form action=index.php?a=39&b=5&c=2 method=post>\n";
		$site->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
		$site->title = "Payments > Payment Types > Credit Card Setup > 2Checkout";
		$site->description = "If using this payment type, please enter the data for your existing 2CHECKOUT account using this page.";

		$show=$checkout_result->FetchRow();
		$site->body .= "<tr class=row_color2>\n\t
			<td width=50% align=right class=medium_font>\n\t<b>2Checkout Account Number:</b></font></font></td>\n\t";
		$site->body .= "<td valign=top class=medium_font><input type=text name=g[sid] value=\"".$show["sid"]."\"></font>\n\t</td>\n</tr>\n";

		/*
		DEPRECATED - NOW THERE'S ONLY ONE URL TO USE
		$site->body .= "<tr class=row_color1>\n\t
			<td width=50% align=right class=medium_font>\n\t<b>Account type:</b></font><br></td>\n\t";
		$site->body .= "<td valign=top class=medium_font><input type=radio name=g[account_type] value=0 ";
		if ($show["account_type"] == 0)
			$site->body .= "checked";
		$site->body .= "> old<br><input type=radio name=g[account_type] value=1 ";
		if ($show["account_type"] == 1)
			$site->body .= "checked";
		$site->body .= "> new</font>\n\t</td>\n</tr>\n";*/

		$site->body .= "<tr class=row_color2>\n\t
			<td width=50% align=right class=medium_font>\n\t<b>Demo mode:</b>".$admin_payments->show_tooltip(1,1)."</td>\n\t";
		$site->body .= "<td valign=top class=medium_font><input type=radio name=g[demo_mode] value=0 ";
		if ($show["demo_mode"] == 0)
			$site->body .= "checked";
		$site->body .= "> live<br><input type=radio name=g[demo_mode] value=1 ";
		if ($show["demo_mode"] == 1)
			$site->body .= "checked";
		$site->body .= "> demo mode</font>\n\t</td>\n</tr>\n";
		if (!$site->admin_demo())
			$site->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center><input type=submit name=save value=\"Save\"></td>\n</tr>\n";

		$site->body .= "<tr class=row_color_black>\n\t
			<td colspan=2 class=medium_font_light>\n\t<b>Settings to make to your 2Checkout account:</b></font>\n\t</td>\n</tr>\n";
		$cc_url = str_replace($show_site["classifieds_file_name"], "cc_process_2checkout.php",$show_site["classifieds_url"]);
		$site->body .= "<tr>\n\t<td colspan=2 class=medium_font>
			1) Turn on the appropriate switch to send \"passback posts\" to this software.  Enter \"Yes\" in the \"Return to a routine
			on your site after credit card processor?\"<br><br>
			2) Enter the url below into the \"Return URL\" field on the same page<bR><b>".$cc_url."</b>";
		$site->body .= "</td></tr>";
		$site->body .= "<tr class=row_color_black height=1><td colspan=2></td></tr>";
		$site->body .= "<tr>\n\t
			<td colspan=2 class=medium_font>\n\tNOTE: 2CHECKOUT IS IN THE PROCESS OF CHANGING THEIR ACCOUNT PREFERENCES AND SETTINGS.
			THE FOLLOWING INSTRUCTIONS MAY BE MORE APPLICABLE TO YOUR PARTICULAR ACCOUNT:<br><br>
			Necessary changes to make your 2Checkout account communicate with this software:</font>\n\t</td>\n</tr>\n";
		$cc_url = str_replace($show_site["classifieds_file_name"], "cc_process_2checkout.php",$show_site["classifieds_url"]);
		$site->body .= "<tr>\n\t<td colspan=2 class=medium_font>
			1) From the account home page click the \"Setting Up Your Site\" link.<br><br>
			2) Click the \"Click Here\" next to the sentence \"If you use a shopping cart please Click Here\".<bR><br>
			3) Click \"You can do so here\" next to the sentence \"To recieve these parameters, you must specify where they are sent.<br><br>
			4) Insert the following link into the text boxes labelled: <br>
			\"Input a url for your customers to be sent to on a successful purchase\"<br>or<br>
			\"Input a url for your customers to be sent to when a purchase cannot be determined verified immediately\"<br><BR>
			<b>".$cc_url."</b>";
		$site->body .= "</td></tr>";

		$site->body .= "</table>\n";
		return $site->body;
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
			$admin_payments->sql_query = "update geodesic_cc_twocheckout set
				sid = \"".$cc_info["sid"]."\",
				account_type = \"".$cc_info["account_type"]."\",
				demo_mode = \"".$cc_info["demo_mode"]."\"";
			//echo $admin_payments->sql_query."<br>\n";
			$update_result = $db->Execute($admin_payments->sql_query);
			if (!$update_result)
			{
				echo $admin_payments->sql_query."<br>\n";
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
	} //end of function update_cc_admin

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
?>