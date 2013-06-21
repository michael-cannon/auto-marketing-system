<? //admin_cc_internetsecure.php
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
	$site->body .= "Text[1] = [\"demo mode\", \"If you wish to test your Internet Secure account to make sure everything is working correctly, choose \\\"demo mode\\\". If you wish to go live, choose \\\"live\\\".\"]\n
		Text[2] = [\"canadian tax method\", \"Choose which, if any, type of Canadian tax you want to charge.\"]\n
		Text[3] = [\"language to display\", \"This will determine the language that is displayed on the Internet Secure website as the user continues their transaction at the Internet Secure website.\"]\n";

	//".$admin_payments->show_tooltip(3,1)."

	// Set style for tooltip
	//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
	$site->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
	$site->body .= "var TipId = \"tiplayer\"\n";
	$site->body .= "var FiltersEnabled = 1\n";
	$site->body .= "mig_clay()\n";
	$site->body .= "</script>";

	$admin_payments->sql_query = "select * from geodesic_cc_internetsecure";
	$internetsecure_result = $db->Execute($admin_payments->sql_query);
	//echo $admin_payments->sql_query."<br>\n";
	if (!$internetsecure_result)
	{
		echo $admin_payments->sql_query."<br>\n";
		$this->error_message = $this->internal_error_message;
		return false;
	}
	elseif (($internetsecure_result->RecordCount() == 1) || ($internetsecure_result->RecordCount() == 0))
	{
		if ($internetsecure_result->RecordCount() == 0)
		{
			$admin_payments->sql_query = "insert into geodesic_cc_internetsecure
				(sid,demo_mode)
				values
				(\"\",0)";
			$checkout_result = $db->Execute($admin_payments->sql_query);

			$admin_payments->sql_query = "select * from geodesic_cc_internetsecure";
			$checkout_result = $db->Execute($admin_payments->sql_query);
			//echo $admin_payments->sql_query."<br>\n";
			if (!$checkout_result)
			{
				echo $admin_payments->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
		}

		$admin_payments->sql_query = "select * from geodesic_classifieds_configuration";
		$site_result = $db->Execute($admin_payments->sql_query);
		//echo $admin_payments->sql_query."<br>\n";
		if (!$site_result)
		{
			echo $admin_payments->sql_query."<br>\n";
			$this->error_message = $this->internal_error_message;
			return false;
		}
		elseif ($site_result->RecordCount() == 1)
		{
			$show_site = $site_result->FetchRow();
		}
		else
			return false;
		if (!$site->admin_demo())$site->body .= "<form action=index.php?a=39&b=5&c=5 method=post>\n";
		$site->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
		$site->title = "Payments > Payment Types > Credit Card Setup > Internet Secure";
		$site->description = "Change the necessary information to use Internet Secure credit card processing below.  You will also find instructions on
			what is needed to set up your merchant account with Internet Secure.";

		$show=$internetsecure_result->FetchRow();
		$site->body .= "<tr class=row_color2>\n\t
			<td width=45% align=right class=medium_font>\n\tinternet secure merchant account number:</td>\n\t";
		$site->body .= "<td valign=top class=medium_font><input type=text name=g[merchantnumber] value=\"".$show["merchantnumber"]."\"></td>\n</tr>\n";

		$site->body .= "<tr class=row_color1>\n\t
			<td align=right class=medium_font>\n\tdemo mode:".$admin_payments->show_tooltip(1,1)."</td>\n\t";
		$site->body .= "<td valign=top class=medium_font><input type=radio name=g[demo_mode] value=0 ";
		if ($show["demo_mode"] == 0)
			$site->body .= "checked";
		$site->body .= "> live<br><input type=radio name=g[demo_mode] value=1 ";
		if ($show["demo_mode"] == 1)
			$site->body .= "checked";
		$site->body .= "> demo mode\n\t</td>\n</tr>\n";

		$site->body .= "<tr class=row_color2>\n\t
			<td align=right class=medium_font>\n\tcanadian tax method:".$admin_payments->show_tooltip(2,1)."</td>\n\t";
		$site->body .= "<td valign=top class=medium_font><input type=radio name=g[canadian_tax_method] value=\"0\" ";
		if ($show["canadian_tax_method"] == "0")
			$site->body .= "checked";
		$site->body .= "> none<br><input type=radio name=g[canadian_tax_method] value=\"{PST}\" ";
		if ($show["canadian_tax_method"] == "{PST}")
			$site->body .= "checked";
		$site->body .= "> Provincial Sales Tax - current tax rates for each of the Canadian Provinces and Territories
			<br><input type=radio name=g[canadian_tax_method] value=\"{GST}\" ";
		if ($show["canadian_tax_method"] == "{GST}")
			$site->body .= "checked";
		$site->body .= "> Goods and Service Tax<br><input type=radio name=g[canadian_tax_method] value=\"{HST}\" ";
		if ($show["canadian_tax_method"] == "{HST}")
			$site->body .= "checked";
		$site->body .= "> Harmonized Sales Tax\n\t</td>\n</tr>\n";

		$site->body .= "<tr class=row_color1>\n\t
			<td align=right class=medium_font>\n\tlanguage to display:".$admin_payments->show_tooltip(3,1)."</td>\n\t";
		$site->body .= "<td valign=top class=medium_font><input type=radio name=g[language] value=\"English\" ";
		if ($show["language"] == "English")
			$site->body .= "checked";
		$site->body .= "> English<br><input type=radio name=g[language] value=\"French\" ";
		if ($show["language"] == "French")
			$site->body .= "checked";
		$site->body .= "> French	<br><input type=radio name=g[language] value=\"Spanish\" ";
		if ($show["language"] == "Spanish")
			$site->body .= "checked";
		$site->body .= "> Spanish<br><input type=radio name=g[language] value=\"Portuguese\" ";
		if ($show["language"] == "Portuguese")
			$site->body .= "checked";
		$site->body .= "> Portuguese <br><input type=radio name=g[language] value=\"Japanese\" ";
		if ($show["language"] == "Japanese")
			$site->body .= "checked";
		$site->body .= ">Japanese\n\t</td>\n</tr>\n";
		if (!$site->admin_demo())
			$site->body .= "<tr class=row_color2>\n\t<td colspan=2 class=medium_font align=center><input type=submit name=save value=\"Save\"></td>\n</tr>\n";

		$site->body .= "<tr class=row_color_black>\n\t
			<td colspan=2 class=medium_font_light>\n\t<b>Internet Secure Setup Instructions</b><BR>
			You will be using the Internet Secure Export Script to return variables to your site and turn on the classified ad once the
			transaction has been approved.  \n\t</td>\n</tr>\n";
		$cc_url = str_replace($show_site["classifieds_file_name"], "cc_process_internetsecure.php",$show_site["classifieds_url"]);
		$site->body .= "<tr>\n\t<td colspan=2 class=medium_font>\n\t
			1) Log into the Merchant Reporting Area of your Internet Secure Account Management Tool.  Within the top menu bar select
			\"Export Scripts\" and then \"Export Script Options\".  Please note that your account must have the \"Export Scripts\" feature to
			work seemlessly with GeoClassifieds.<br><br>
			2) Within the field called \"Server Domain Name\" you must enter the following the base URL of your site (ie www.mysite.com):<bR><br>
			3) Within the \"Web Page\" field you must place the url path to the page that will be processing the Internet Secure transaction.
			The path is everything in the url below immediately following the domain name of the url and starts with the <b>single</b> \"/\ (slash)\".
			In a url like <br>http://www.mysite.com/classifieds/cc_process_internetsecure.php<br>The base url would
			be <bR>http://www.mysite.com<br> and this would be placed within the \"Server Domain Name\" field and
			<bR>/classifieds/cc_process_internetsecure.php<bR>would be placed within the \"Web Page\" field.<br>You just need to find
			the full url path to the \"cc_process_internetsecure.php\" and split the url between the two fields within the Internet Secure
			Account Management page specified.<br><br>
			4) On the same page place a check next to \"Send Approvals Only\"<br><BR>";
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
			$admin_payments->sql_query = "update geodesic_cc_internetsecure set
				merchantnumber = \"".$cc_info["merchantnumber"]."\",
				canadian_tax_method = \"".$cc_info["canadian_tax_method"]."\",
				language = \"".$cc_info["language"]."\",
				demo_mode = \"".$cc_info["demo_mode"]."\"";
			//echo $admin_payments->sql_query."<br>\n";
			$update_result = $db->Execute($admin_payments->sql_query);
			if (!$update_result)
			{
				echo $admin_payments->sql_query."<br>\n";
				echo $db->ErrorMsg()." is the error<br>\n";
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