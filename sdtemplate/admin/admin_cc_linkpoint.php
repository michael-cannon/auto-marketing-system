<? //admin_cc_linkpoint.php
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
	$site->body .= "Text[1] = [\"path to the \\\"pem\\\" file linkpoint has you create\", \"Enter the server root path to the \\\"storenumber.pem\\\" that Linkpoint sends to you to verify your communications with them.They provide this file to you, request that you rename it with your storenumber and upload to your site.\"]\n
		Text[2] = [\"demo mode\", \"If you wish to test your Linkpoint account to make sure everything is working correctly, choose \\\"demo mode\\\".  If you wish to go live, choose \\\"live\\\".\"]\n";

	//".$admin_payments->show_tooltip(1,1)."

	// Set style for tooltip
	//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
	$site->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
	$site->body .= "var TipId = \"tiplayer\"\n";
	$site->body .= "var FiltersEnabled = 1\n";
	$site->body .= "mig_clay()\n";
	$site->body .= "</script>";

	$admin_payments->sql_query = "select * from geodesic_cc_linkpoint";
	$linkpoint_result = $db->Execute($admin_payments->sql_query);
	//echo $admin_payments->sql_query."<br>\n";
	if (!$linkpoint_result)
	{
		//echo $admin_payments->sql_query."<br>\n";
		$this->error_message = $this->internal_error_message;
		return false;
	}
	elseif ($linkpoint_result->RecordCount() == 1)
	{
		$admin_payments->sql_query = "select * from geodesic_classifieds_configuration";
		$site_result = $db->Execute($admin_payments->sql_query);
		//echo $admin_payments->sql_query."<br>\n";
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
		if (!$site->admin_demo())$site->body .= "<form action=index.php?a=39&b=5&c=4 method=post>\n";
		$site->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
		$site->title = "Payments > Payment Types > Credit Card Setup > Linkpoint";
		$site->description = "Change the necessary information to use linkpoint credit card processing below.<br><br>PLEASE NOTE: You must have an SSL
			certificate installed on your server to communicate to Linkpoint.  They will only accept communications through an SSL connection.";

		$show=$linkpoint_result->FetchRow();

		$site->body .= "<tr class=row_color2>\n\t
			<td width=50% align=right class=medium_font>\n\tstore number:</td>\n\t";
		$site->body .= "<td valign=top class=medium_font>\n\t<input type=text name=g[store_number] value=\"".$show["store_number"]."\">\n\t</td>\n</tr>\n";

		$site->body .= "<tr class=row_color1>\n\t
			<td width=50% align=right class=medium_font>\n\tpath to the \"pem\" file linkpoint has you create:".$admin_payments->show_tooltip(1,1)."</td>\n\t";
		$site->body .= "<td valign=top class=medium_font>\n\t<input type=text name=g[ssl_path] value=\"".$show["ssl_path"]."\">\n\t</td>\n</tr>\n";


		$site->body .= "<tr class=row_color2>\n\t
			<td width=50% align=right class=medium_font>\n\tdemo mode:".$admin_payments->show_tooltip(2,1)."</td>\n\t";
		$site->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=g[demo_mode] value=0 ";
		if ($show["demo_mode"] == 0)
			$site->body .= "checked";
		$site->body .= "> live<br><input type=radio name=g[demo_mode] value=1 ";
		if ($show["demo_mode"] == 1)
			$site->body .= "checked";
		$site->body .= "> demo mode\n\t</td>\n</tr>\n";
		if (!$site->admin_demo())
			$site->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center><input type=submit name=save value=\"Save\"></td>\n</tr>\n";
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
		$admin_payments->sql_query = "update geodesic_cc_linkpoint set
			store_number = \"".$cc_info["store_number"]."\",
			demo_mode = \"".$cc_info["demo_mode"]."\",
			ssl_path = \"".$cc_info["ssl_path"]."\"";
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