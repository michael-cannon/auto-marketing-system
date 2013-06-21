<?// admin_site_configuration_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
\**************************************************************************/

class Site_configuration extends Admin_site {

	var $internal_error_message = "There was an internal error";
	var $data_error_message = "Not enough data to complete request";
	var $page_text_error_message = "No text connected to this page";
	var $no_pages_message = "No pages to list";

	var $site_configuration_message;

	var $debug_site = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Site_configuration($db=0, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	} //end of function Site_configuration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_general_configuration_form($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"site url\", \"Enter the url to link directly to index.php (http://www.somesite.com/index.php).\"]\n
			Text[2] = [\"file name\", \"Enter the file name of the base file.  This file was sent to you as index.php but you have the ability to rename this file if you like.  Just make sure you put the name of that file here.\"]\n
			Text[3] = [\"secure ssl site url\", \"Enter the ssl url to link directly to index.php (http://www.somesite.com/index.php).\"]\n
			Text[4] = [\"use ssl connection for 'listing' process\", \"If you are planning on accepting credit cards to place listings on your site this is STRONGLY RECOMMENDED (WE RECOMMEND THIS SO STRONGLY THAT WE SUGGEST YOU DO NOT ACCEPT CREDIT CARDS WITHOUT THIS PROTECTION FOR YOUR SITE). We also recommend a ssl certificate for your domain. Geodesic Solutions sells ssl certificates if you require one.  This protects the credit card information from getting stolen between the clients browser and your site.\"]\n
			Text[5] = [\"affiliate url\", \"Enter the url to link directly to aff.php (http://www.somesite.com/aff.php) - this is the original name of the file.  You should change here accordingly.\"]\n
			Text[6] = [\"email configuration\", \"There are three different configurations to send email.  Your host determines which setting you need.  There are different levels of spam protection and then just different configurations on smtp servers that determine which setting is necessary.  Start with setting 1.  Try sending emails from anywhere within the site.  If no emails are sent go down to the next setting and try again until the email are being sent.  Configuration number 3 will be necessary for Yahoo hosting clients.  Other hosts very in their configuration.\"]\n
			Text[7] = [\"email header break configuration\", \"There are two different possibilities for the header \\\"divider\\\".  The official PHP/RFC position is to separate your email header with a return and newline characters.  But several servers do not recognize these very well.  The next configuration is to separate the email headers with just a newline character.\"]\n
			Text[8] = [\"BCC admin on user communication\", \"Enter an email address here to have a blind copy of all notify friend and notify seller email sent.  If this address is left empty no email will be sent.  This does not affect communications sent from within communication section.\"]\n
			Text[9] = [\"admin approves all listings\", \"Set this control to yes and the admin will have to approve every listing before it will be exposed on the client side.\"]\n
			Text[10] = [\"notify admin when an listing is edited\", \"Set this control to yes and the admin will be notified by email of a listing that has been modified by a user.\"]\n
			Text[11] = [\"number of levels of categories to display in dropdown\", \" If you have a large number of categories you may experience page load problems on pages where the category dropdown appears (such as the search page or pages where the search module appears).  This makes for a very long dropdown and long page load times.  To alleviate these issues you can choose to display a certain number of levels of categories within the dropdown. This will shorten the page load times and shrink the dropdown to a manageable size.  If set to 0 all levels of categories will be displayed.\"]\n
			Text[12] = [\"administration reply email\", \"This used in all email communications as the reply to email address.\"]\n
			Text[13] = [\"use API to sync user information\", \"Choosing \\\"yes\\\" will use the automatic program interface to sync user information between multiple installations of Geodesic Solutions software. The \\\"api integration\\\" link will provide more instructions.  To sync your installations together requires that you purchase the API software tools from Geodesic Solutions.\"]\n
			Text[14] = [\"switch for use of built-in CSS\", \"Choose whether to use side wide built in css or use your own.  This will most of the time be set to yes unless you use your own CSS files.\"]\n
			Text[15] = [\"Activate IDevAffiliate Integration\", \"Turn on the IDevAffiliate software integration package.\"]\n
			Text[16] = [\"Use IDevAffiliate for Renewing of Listings\", \"Pay commissions for the renewing of listings.\"]\n
			Text[17] = [\"Use IDevAffiliate for Upgrading of Listings\", \"Pay commissions for the upgrading of listings.\"]\n
			Text[18] = [\"Absolute Path to IDevAffiliate sale.php with trailing slash\", \"The absolute path to sale.php included with IDevAffiliate.\"]\n
			Text[19] = [\"Character Set Used\", \"Choose the same character set that you wish to use on the front side here.  This will set the character set on the admin side.  Then place the correct character set for the type of text used on the site in the templates on the front side.\"]\n
			Text[20] = [\"Site On/Off Switch\", \"Checking the \\\"Off\\\" switch will re-direct anyone other than the administrator to a page saying that the site is \\\"Under Construction\\\" (or one of your choosing).  WARNING: IF THE URL BELOW DOES NOT CONTAIN A LOGGED IN/OUT MODULE THE ADMIN USER WILL ONLY BE ABLE TO LOG IN WHILE THE SWITCH IS \\\"On\\\".\"]\n
			Text[21] = [\"Disable Site Url\", \"Set the url to a page that will display a message that the site is disabled. The url set here must be to a file outside of the software(i.e. an external html file). The admin can still browse the site when it is disabled but must be logged into the client side as the admin prior to disabling. \"]\n
			Text[22] = [\"Time offset\", \"Set the offset from the server clock that the software will use.\"]\n
			Text[23] = [\"Listing type allowed\", \"Control what type of listing is allowed to be placed on your site. NOTE: This will not change the product from its intention of being a combined venue but will stop the actual placement of an auction or an ad. This allows for the switch to be used upon initial site launch or later in a live site without losing the software's complete functionality\"]\n
			Text[24] = [\"Storefront Url\", \"Enter the url to link directly to stores.php (http://www.somesite.com/stores.php) - this is the original name of the file.  You should change here accordingly.\"]\n
			"
			;

		//".$this->show_tooltip(14,1)."

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
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show_configuration = $result->FetchRow();
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=28&z=6 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100%>\n";
			if ($this->site_configuration_message)
				$this->body .= "<tr>\n\t<td colspan=2 class=medium_error_font>\n\t".$this->site_configuration_message." \n\t</td>\n</tr>\n";

			$this->title .= "Site Setup > General Settings";
			$this->description .= "Control various general aspects of your site through the form below.";

			$this->row_count = 0;

			$this->body .= "<tr  class=row_color_red>\n\t<td colspan=2 class=medium_font_light>\n\t <b>Miscellaneous Url Settings</b></font>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t<b>site url:</b>".$this->show_tooltip(1,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t\n\t
				<input type=text name=b[classifieds_url] size=60 maxsize=100 value=\"".$show_configuration["classifieds_url"]."\"> \n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t<b>file name:</b>".$this->show_tooltip(2,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t\n\t
				<input type=text name=b[classifieds_file_name] size=60 maxsize=100 value=\"".$show_configuration["classifieds_file_name"]."\"> \n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t<b>secure ssl site url:</b>".$this->show_tooltip(3,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t\n\t
				<input type=text name=b[classifieds_ssl_url] size=60 maxsize=100 value=\"".$show_configuration["classifieds_ssl_url"]."\"> \n\t</td>\n</tr>\n";
			$this->row_count++;

			//use ssl connection for the listing process
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>use ssl connection for the 'listing' process:</b>".$this->show_tooltip(4,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[use_ssl_in_sell_process] value=0 ";
			if ($show_configuration["use_ssl_in_sell_process"] == 0)
				$this->body .= "checked";
			$this->body .= "> NO<br><input type=radio name=b[use_ssl_in_sell_process] value=1 ";
			if ($show_configuration["use_ssl_in_sell_process"] == 1)
				$this->body .= "checked";
			$this->body .= "> YES </td></tr>";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t<b>affiliate url:</b>".$this->show_tooltip(5,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t\n\t
				<input type=text name=b[affiliate_url] size=60 maxsize=100 value=\"".$show_configuration["affiliate_url"]."\"> \n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>site on/off switch:</b>".$this->show_tooltip(20,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[site_on_off] value=0 ";
			if ($show_configuration["site_on_off"] == 0)
				$this->body .= "checked";
			$this->body .= ">On<br><input type=radio name=b[site_on_off] value=1 ";
			if ($show_configuration["site_on_off"] == 1)
				$this->body .= "checked";
			$this->body .= ">Off</td></tr>";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t<b>disable site url:</b>".$this->show_tooltip(21,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t\n\t
				<input type=text name=b[disable_site_url] size=60 maxsize=100 value=\"".$show_configuration["disable_site_url"]."\"> \n\t</td>\n</tr>\n";
			$this->row_count++;
			if($this->is_class_auctions())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>listing types allowed:</b>".$this->show_tooltip(23,1)."</td>\n\t";
				$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[listing_type_allowed] value=0 ";
				if ($show_configuration["listing_type_allowed"] == 0)
					$this->body .= "checked";
				$this->body .= "> 1 - allow classifieds and auctions<br><input type=radio name=b[listing_type_allowed] value=1 ";
				if ($show_configuration["listing_type_allowed"] == 1)
					$this->body .= "checked";
				$this->body .= "> 2 - allow classifieds only<bR><input type=radio name=b[listing_type_allowed] value=2 ";
				if ($show_configuration["listing_type_allowed"] == 2)
					$this->body .= "checked";
				$this->body .= "> 3 - allow auctions only</td></tr>";
			}

			//STOREFRONT CODE
			if(file_exists('storefront/admin_store.php'))
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t<b>storefront url:</b>".$this->show_tooltip(24,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t\n\t
					<a name='storefrontUrl'></a><input type=text name=b[storefront_url] size=60 maxsize=100 value=\"".$show_configuration["storefront_url"]."\"> \n\t</td>\n</tr>\n";
				$this->row_count++;
			}
			//STOREFRONT CODE

			$this->body .= "<tr  class=row_color_red>\n\t<td colspan=2 class=medium_font_light>\n\t <b>Miscellaneous Site Settings</b></font>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t<b>server time offset:</b>".$this->show_tooltip(22,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<select name=b[time_shift]>";
			for ($i=-23;$i<24;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_configuration["time_shift"])
					$this->body .= "selected";
				$this->body .= " value=".$i.">".$i."</option>\n\t\t";
			}
			$this->body .= "</select> Hours\n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>email configuration:</b>".$this->show_tooltip(6,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[email_configuration] value=1 ";
			if ($show_configuration["email_configuration"] == 1)
				$this->body .= "checked";
			$this->body .= "> 1 - allows the setting of additional headers (reply-to,...etc)<br><input type=radio name=b[email_configuration] value=2 ";
			if ($show_configuration["email_configuration"] == 2)
				$this->body .= "checked";
			$this->body .= "> 2 - allows only \"from\" header to be set<bR><input type=radio name=b[email_configuration] value=3 ";
			if ($show_configuration["email_configuration"] == 3)
				$this->body .= "checked";
			$this->body .= "> 3 - allows no headers or \"from\" to be set (for Yahoo hosting the lead email on the account is used as
				the return email address)  </td></tr>";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>email header break configuration:</b>".$this->show_tooltip(7,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[email_header_break] value=0 ";
			if ($show_configuration["email_header_break"] == 0)
				$this->body .= "checked";
			$this->body .= "> Standard return and newline character<br><input type=radio name=b[email_header_break] value=1 ";
			if ($show_configuration["email_header_break"] == 1)
				$this->body .= "checked";
			$this->body .= "> Simple newline only character </td></tr>";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t<b>BCC admin on user communication:</b>".$this->show_tooltip(8,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t\n\t
				<input type=text name=b[admin_email_bcc] size=60 maxsize=100 value=\"".$show_configuration["admin_email_bcc"]."\"> \n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>admin approves all listings:</b>".$this->show_tooltip(9,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[admin_approves_all_ads] value=1 ";
			if ($show_configuration["admin_approves_all_ads"] == 1)
				$this->body .= "checked";
			$this->body .= "> Yes<br><input type=radio name=b[admin_approves_all_ads] value=0 ";
			if ($show_configuration["admin_approves_all_ads"] == 0)
				$this->body .= "checked";
			$this->body .= "> No </td></tr>";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>notify admin when a listing is edited:</b>".$this->show_tooltip(10,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[admin_email_edit] value=1 ";
			if ($show_configuration["admin_email_edit"] == 1)
				$this->body .= "checked";
			$this->body .= "> Yes<br><input type=radio name=b[admin_email_edit] value=0 ";
			if ($show_configuration["admin_email_edit"] == 0)
				$this->body .= "checked";
			$this->body .= "> No </td></tr>";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>number of levels of categories to display in category dropdown (client side):</b>".$this->show_tooltip(11,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<select name=b[levels_of_categories_displayed]>";
			for ($i=0;$i<50;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_configuration["levels_of_categories_displayed"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>number of levels of categories to display in category dropdown in admin only:</b>".$this->show_tooltip(11,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<select name=b[levels_of_categories_displayed_admin]>";
			for ($i=0;$i<10;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_configuration["levels_of_categories_displayed_admin"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t<b>administration reply email:</b>".$this->show_tooltip(12,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t\n\t
				<input type=text name=b[site_email] size=60 maxsize=100 value=\"".$show_configuration["site_email"]."\"> \n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>use API to sync user information:</b>".$this->show_tooltip(13,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[use_api] value=1 ";
			if ($show_configuration["use_api"] == 1)
				$this->body .= "checked";
			$this->body .= "> Yes<br><input type=radio name=b[use_api] value=0 ";
			if ($show_configuration["use_api"] == 0)
				$this->body .= "checked";
			$this->body .= "> No </td></tr>";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>switch for use of built-in CSS:</b>".$this->show_tooltip(14,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t
			<input type=radio name=b[use_css] value=1 ";
			if ($show_configuration["use_css"] == 1)
				$this->body .= "checked ";
			$this->body .= ">Yes<br><input type=radio name=b[use_css] value=0 ";
			if ($show_configuration["use_css"] == 0)
				$this->body .= "checked";
			$this->body .= "> No \n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr  class=row_color_red>\n\t<td colspan=2 class=medium_font_light>\n\t <b>IDevAffiliate Integration</b></font>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>Activate IDevAffiliate Integration:</b>".$this->show_tooltip(15,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[idevaffiliate] value=1 ";
			if ($show_configuration["idevaffiliate"] == 1)
				$this->body .= "checked";
			$this->body .= "> Yes<br><input type=radio name=b[idevaffiliate] value=0 ";
			if ($show_configuration["idevaffiliate"] == 0)
				$this->body .= "checked";
			$this->body .= "> No </td></tr>";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t <b>Use IDevAffiliate for Renewing of Listings:</b>".$this->show_tooltip(16,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[idev_renewal] value=1 ";
			if ($show_configuration["idev_renewal"] == 1)
				$this->body .= "checked";
			$this->body .= "> Yes<br><input type=radio name=b[idev_renewal] value=0 ";
			if ($show_configuration["idev_renewal"] == 0)
				$this->body .= "checked";
			$this->body .= "> No </td></tr>";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t <b>Use IDevAffiliate for Upgrading of Listings:</b>".$this->show_tooltip(17,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[idev_upgrade] value=1 ";
			if ($show_configuration["idev_upgrade"] == 1)
				$this->body .= "checked";
			$this->body .= "> Yes<br><input type=radio name=b[idev_upgrade] value=0 ";
			if ($show_configuration["idev_upgrade"] == 0)
				$this->body .= "checked";
			$this->body .= "> No </td></tr>";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t <b>Absolute Path to IDevAffiliate sales.php with trailing slash:</b>".$this->show_tooltip(18,1)."<br>i.e. C:/Apache2/htdocs/IdevAffiliate/ or /var/www/idevaffiliate/</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t\n\t
				<input type=text name=b[idev_path] size=60 maxsize=100 value=\"".$show_configuration["idev_path"]."\"></font>\n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=medium_font_light>\n\t<b>Character Encoding</b></font>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>Choose the charset that your set will run:</b>".$this->show_tooltip(19,1)."</td>\n\t";
			$this->body .= "<td>\n\t";
			$this->body .= "<select name=b[charset]>\n\t";
			$this->body .= "<option value=\"utf-8\"";
			if($show_configuration["charset"] == "utf-8")
				$this->body .= " selected";
			$this->body .= ">utf-8</option>\n\t";
			$this->body .= "<option value=\"iso-8859-1\"";
			if($show_configuration["charset"] == "iso-8859-1")
				$this->body .= " selected";
			$this->body .= ">iso-8859-1 (latin 1)</option>\n\t";
			$this->body .= "<option value=\"iso-2022-jp\"";
			if($show_configuration["charset"] == "iso-2022-jp")
				$this->body .= " selected";
			$this->body .= ">iso-2022-jp</option>\n\t";
			$this->body .= "<option value=\"euc-kr\"";
			if($show_configuration["charset"] == "euc-kr")
				$this->body .= " selected";
			$this->body .= ">euc-kr</option>\n\t";
			$this->body .= "<option value=\"windows-1251\"";
			if($show_configuration["charset"] == "windows-1251")
				$this->body .= " selected";
			$this->body .= ">windows-1251</option>\n\t";
			$this->body .= "<option value=\"EUC-JP\"";
			if($show_configuration["charset"] == "EUC-JP")
				$this->body .= " selected";
			$this->body .= ">EUC-JP</option>\n\t";
			$this->body .= "<option value=\"EUC-JP\"";
			if($show_configuration["charset"] == "x-mac-arabic")
				$this->body .= " selected";
			$this->body .= ">x-mac-arabic</option>\n\t";
			$this->body .= "<option value=\"windows-1256\"";
			if($show_configuration["charset"] == "windows-1256")
				$this->body .= " selected";
			$this->body .= ">windows-1256</option>\n\t";
			$this->body .= "<option value=\"iso-8859-4\"";
			if($show_configuration["charset"] == "iso-8859-4")
				$this->body .= " selected";
			$this->body .= ">iso-8859-4</option>\n\t";
			$this->body .= "<option value=\"windows-1250\"";
			if($show_configuration["charset"] == "windows-1250")
				$this->body .= " selected";
			$this->body .= ">windows-1250</option>\n\t";
			$this->body .= "<option value=\"shift_jis euc-kr\"";
			if($show_configuration["charset"] == "shift_jis euc-kr")
				$this->body .= " selected";
			$this->body .= ">shift_jis euc-kr</option>\n\t";
			$this->body .= "</select>\n\t</td>\n\t</tr>\n\t";
			$this->row_count++;

			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 align=center class=medium_font>\n\t<input type=submit value=\"Save\" name=submit> \n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";

			return true;
		}
		else
		{
			$this->site_configuration_message = $this->internal_error_message;
			return false;
		}

	} //end of function display_general_configuration_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_general_configuration($db,$configuration_info=0)
	{
		if ($configuration_info)
		{
			$this->sql_query = "update ".$this->site_configuration_table." set
				site_on_off = \"".trim($configuration_info["site_on_off"])."\",
				disable_site_url = \"".trim($configuration_info["disable_site_url"])."\",
				classifieds_url = \"".trim($configuration_info["classifieds_url"])."\",
				use_api = \"".$configuration_info["use_api"]."\",
				classifieds_file_name = \"".trim($configuration_info["classifieds_file_name"])."\",
				affiliate_url = \"".trim($configuration_info["affiliate_url"])."\",
				classifieds_ssl_url = \"".trim($configuration_info["classifieds_ssl_url"])."\",
				email_configuration = \"".trim($configuration_info["email_configuration"])."\",
				levels_of_categories_displayed = \"".trim($configuration_info["levels_of_categories_displayed"])."\",
				levels_of_categories_displayed_admin = \"".trim($configuration_info["levels_of_categories_displayed_admin"])."\",
				admin_approves_all_ads = \"".trim($configuration_info["admin_approves_all_ads"])."\",
				admin_email_edit = \"".$configuration_info["admin_email_edit"]."\",
				use_ssl_in_sell_process = \"".$configuration_info["use_ssl_in_sell_process"]."\",
				admin_email_bcc = \"".$configuration_info["admin_email_bcc"]."\",
				email_header_break = \"".$configuration_info["email_header_break"]."\",
				site_email = \"".trim($configuration_info["site_email"])."\",
				idevaffiliate = \"".$configuration_info["idevaffiliate"]."\",
				idev_renewal = \"".$configuration_info["idev_renewal"]."\",
				idev_upgrade = \"".$configuration_info["idev_upgrade"]."\",
				idev_path = \"".trim($configuration_info["idev_path"])."\",
				charset = \"".trim($configuration_info["charset"])."\",
				time_shift = \"".$configuration_info["time_shift"]."\",
				listing_type_allowed = \"".$configuration_info["listing_type_allowed"]."\",
				use_css = \"".trim($configuration_info["use_css"])."\"";
			$result = $db->Execute($this->sql_query);
			if ($this->debug_site) echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug_site) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
			{
				//STOREFRONT CODE
				if($configuration_info["storefront_url"])
				{
					$this->sql_query = "update ".$this->site_configuration_table." set
					storefront_url = \"".trim($configuration_info["storefront_url"])."\"";
					$result = $db->Execute($this->sql_query);
					if ($this->debug_site) echo $this->sql_query."<br>\n";
					if (!$result)
					{
						if ($this->debug_site) echo $this->sql_query."<br>\n";
						$this->site_error($db->ErrorMsg());
						return false;
					}
				}
				//STOREFRONT CODE
				return true;
			}
		}
		else
		{
			$this->site_configuration_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_general_configuration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_browse_configuration_form($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"display category navigation\", \"Choosing \\\"yes\\\" will display the category navigation above the browsing results (according to the settings configured below). Choosing no will not display category navigation above the browsing results. The category navigation can then be displayed through any one of the category navigation display modules available.\"]\n
			Text[2] = [\"display category count\", \"Choosing \\\"yes\\\" will display the number of listings a category has in it next to that categories name while browsing.\"]\n
			Text[3] = [\"display sub-category listings\", \"Choosing \\\"yes\\\" will display the listings of a current categories sub-categories within the current category listings. Choosing \\\"yes\\\" will make your site appear more full.\"]\n
			Text[4] = [\"number of columns for browsing categories on home page\", \"Set the number of columns to display the categories in while on the home page that displays the main categories.\"]\n
			Text[5] = [\"number of columns for browsing subcategories\", \"Set the number of columns to display the categories in while browsing the listings.\"]\n
			Text[6] = [\"display the category description while browsing\", \"Choosing \\\"yes\\\" will display the category description below the category name while browsing.\"]\n
			Text[7] = [\"display the \\\"no subcategory\\\" message\", \"Choosing \\\"yes\\\" will display the \\\"There are no subcategories to...\\\" message when you are browsing a category that has no subcategories to enter.\"]\n
			Text[8] = [\"display sub-category tree above normal results\", \"Choosing \\\"yes\\\" will display the category tree above the normal browsing results (according to the configuration setting below). Choosing no will not display the category tree above the normal browsing results. The category tree can then be displayed using one of the category tree display modules available.\"]\n
			Text[9] = [\"where and if you want to display the category tree\", \"This control allows you to control if you want and where the category tree is to be displayed when users are browsing the categories.\"]\n
			Text[10] = [\"category new listing time limit\", \"This the time limit on whether to display the \\\"new listing\\\" icon or not. If a category has a new listing placed within it within the time limit you set below the \\\"new listing\\\" icon will appear next to that category's name when it appears within category navigation. If you do not want to display any new listings on the home page set this to 0\"]\n
			Text[11] = [\"\\\"new listing\\\" in category icon to use\", \"This is the url of the \\\"new listing\\\" icon used within the category navigation to indicate if a category has a new listing within it. The icon will appear immediately next to the category name within the category navigation around the site. This is the icon that will be used within all category navigation whether it is within a module or within normal category navigation display.\"]\n
			Text[12] = [\"post login page\", \"From the list below choose the page you would like to appear once a user has successfully logged in.\"]\n
			Text[13] = [\"display the thumbnail photo or photo icon\", \"If you choose to display the photo column while browsing you can choose to display the photo icon or a thumbnail of the first image connected to the listing.\"]\n
			Text[14] = [\"image to use if no image uploaded by user\", \"If you choose to set an image location here that image will be shown in the photo column when browsing listings if the user did not upload an image for their listing.\"]\n
			Text[15] = [\"photo icon to use\", \"This is the url of the photo icon used in the photo icon column. The photo icon will be displayed if you choose to display the photo icon only or when the thumbnail is chosen to be displayed and there is a problem displaying it.\"]\n
			Text[16] = [\"browsing thumbnail max size\", \"Set the max width and height of the thumbnail photo displayed in the browsing results.\"]\n
			Text[17] = [\"browsing featured thumbnail max size\", \"Set the max width and height of the featured thumbnail photo displayed in the browsing results. This also controls the size of the thumbnails in the browsing featured by picture only page.\"]\n
			Text[18] = [\"help icon to use\", \"This is the url of the help icon used around the site to link to help popups.\"]\n
			Text[19] = [\"sold icon to use\", \"This is the url of the sold icon used when user chooses to display the sold icon next to their listing while browsing.\"]\n
			Text[20] = [\"number of listings to display on a page\", \"Set the number of listings to display on any result page.  This will also affect the Active and Expired Listings pages also.\"]\n
			Text[21] = [\"number of featured listings to display on the category home pages\", \"Set the number of featured listings you wish to display on each category's home page. The featured listings will only display on the first (home) page of every category and only shows the featured listings within that category. If you do not want to display any featured listings on these pages set this to 0.\"]\n
			Text[22] = [\"time to cache category listings while browsing\", \"Set the length of time to cache the category listing when browsing. This is a time saving feature that could help the speed of . If you do not want to display any new listings on the home page set this to 0.\"]\n
			Text[23] = [\"use the zip code distance calculator\", \"Choosing \\\"yes\\\" will allow your clients to search for listings within a certain distance from any zip code they enter.\"]\n
			Text[24] = [\"use preconfigured search form within search page\", \"You can choose to use the default search form within the search page or choose to create one of your own within the your own template if you wish. If you choose to use your own search form it will be used across all categories. The default form can contain different search fields based on the current category the client came from.\"]\n
			Text[25] = [\"user must be logged in to contact seller\", \"Choosing \\\"yes\\\" will require the users to register and log in to contact the seller.\"]\n
			Text[26] = [\"user must be logged in to view listings\", \"Choosing \\\"yes\\\" will require the users to register and log in to view any listings.\"]\n
			Text[27] = [\"Choose how display category count displays\", \"This decides how the category count will display.  Note that if you select no to display category count this setting has no effect.\"]\n
			Text[28] = [\"buy now image\", \"By referencing an image here you will have this image display next to the title of an auction when the auction has a buy now price available. Reference the image relatively (ex:images/nameofyourimage.jpg).\"]\n
			Text[29] = [\"reserve met image\", \"By referencing an image here you will have this image display next to the title of an auction when the auction has met the reserve price. Reference the image relatively (ex:images/nameofyourimage.jpg).\"]\n
			Text[30] = [\"no reserve image\", \"By referencing an image here you will have this image display next to the title of an auction when the auction has NO reserve price. Reference the image relatively (ex:images/nameofyourimage.jpg).\"]\n
			Text[31] = [\"default order of ads while browsing\", \"Choosing no order will let the ads display by newest first.  You can choose any of the other values in the dropdown to determine the default order of ads displayed while browsing.\"]\n
			Text[32] = [\"Rewrite URLs\", \"Rewrite URLs to make them look static (ex:index_a-28_b-141.htm). To use this function, make sure you have your .htaccess file set up correctly.\"]\n";


		//".$this->show_tooltip(27,1)."

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
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show_configuration = $result->FetchRow();
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=28&z=7 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=".$this->get_row_color()." width=100%>\n";
			if ($this->site_configuration_message)
				$this->body .= "<tr>\n\t<td colspan=2 class=medium_error_font>\n\t".$this->site_configuration_message." \n\t</td>\n</tr>\n";

			$this->title = "Site Setup > Browsing Settings";
			$this->description = "Control the features available when browsing the pages of your site.";

			$this->row_count = 0;

			//display category navigation above browsing results
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>display category navigation:</b>".$this->show_tooltip(1,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[display_category_navigation] value=1 ";
			if ($show_configuration["display_category_navigation"] == 1)
				$this->body .= "checked";
			$this->body .= "> yes<br><input type=radio name=b[display_category_navigation] value=0 ";
			if ($show_configuration["display_category_navigation"] == 0)
				$this->body .= "checked";
			$this->body .= "> no </td>\n</tr>\n";
			$this->row_count++;

			//display subcategory contents while browsing categories
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>display category count:</b>".$this->show_tooltip(2,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[display_category_count] value=1 ";
			if ($show_configuration["display_category_count"] == 1)
				$this->body .= "checked";
			$this->body .= "> yes<br><input type=radio name=b[display_category_count] value=0 ";
			if ($show_configuration["display_category_count"] == 0)
				$this->body .= "checked";
			$this->body .= "> no </td>\n</tr>\n";
			$this->row_count++;

			// Choose how to display the category counts
			if($this->is_class_auctions())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>choose how to display category counts:</b>".$this->show_tooltip(27,1)."</td>\n\t";
				$this->body .= "<td width=50% valign=top class=medium_font>\n\t";
				$this->body .= "<select name=b[browsing_count_format]>\n\t\t";
				$this->body .= "<option ";
				if ($show_configuration["browsing_count_format"] == 1)
					$this->body .= "selected";
				$this->body .= " value=1>Display Auction Count Only</option>\n\t\t";
				$this->body .= "<option ";
				if ($show_configuration["browsing_count_format"] == 2)
					$this->body .= "selected";
				$this->body .= " value=2>Display Classified Count Only</option>\n\t\t";
				$this->body .= "<option ";
				if ($show_configuration["browsing_count_format"] == 3)
					$this->body .= "selected";
				$this->body .= " value=3>Display Auction then Classified Count</option>\n\t\t";
				$this->body .= "<option ";
				if ($show_configuration["browsing_count_format"] == 4)
					$this->body .= "selected";
				$this->body .= " value=4>Display Classified then Auction Count</option>\n\t\t";
				$this->body .= "<option ";
				if ($show_configuration["browsing_count_format"] == 5)
					$this->body .= "selected";
				$this->body .= " value=5>Combined Count</option>\n\t\t";
				$this->body .= "</td>\n</tr>\n";
				$this->row_count++;
			}

			//display category count next to category while browsing
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>display sub-category listings:</b>".$this->show_tooltip(3,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[display_sub_category_ads] value=1 ";
			if ($show_configuration["display_sub_category_ads"] == 1)
				$this->body .= "checked";
			$this->body .= "> yes<br><input type=radio name=b[display_sub_category_ads] value=0 ";
			if ($show_configuration["display_sub_category_ads"] == 0)
				$this->body .= "checked";
			$this->body .= "> no </td>\n</tr>\n";
			$this->row_count++;

			//number of columns for browsing categories
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=50% class=medium_font>\n\t<b>number of columns for browsing categories on home page:</b>".$this->show_tooltip(4,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<select name=b[number_of_browsing_columns]>\n\t\t";
			for ($i=1;$i<6;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_configuration["number_of_browsing_columns"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";
			$this->row_count++;

			//number of columns for browsing subcategories
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=50% class=medium_font>\n\t<b>number of columns for browsing subcategories:</b>".$this->show_tooltip(5,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<select name=b[number_of_browsing_subcategory_columns]>\n\t\t";
			for ($i=1;$i<6;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_configuration["number_of_browsing_subcategory_columns"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";
			$this->row_count++;

			//display listing descriptions when browsing categories
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>display the category description while browsing:</b>".$this->show_tooltip(6,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[display_category_description] value=1 ";
			if ($show_configuration["display_category_description"] == 1)
				$this->body .= "checked";
			$this->body .= "> yes<br><input type=radio name=b[display_category_description] value=0 ";
			if ($show_configuration["display_category_description"] == 0)
				$this->body .= "checked";
			$this->body .= "> no </td>\n</tr>\n";
			$this->row_count++;

			//display no subcategories message
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>display the \"no subcategory\" message:</b>".$this->show_tooltip(7,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[display_no_subcategory_message] value=1 ";
			if ($show_configuration["display_no_subcategory_message"] == 1)
				$this->body .= "checked";
			$this->body .= "> yes<br><input type=radio name=b[display_no_subcategory_message] value=0 ";
			if ($show_configuration["display_no_subcategory_message"] == 0)
				$this->body .= "checked";
			$this->body .= "> no </td>\n</tr>\n";
			$this->row_count++;

			//display category tree at top of browsing results
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>display sub-category tree above normal results:</b>".$this->show_tooltip(8,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[display_category_tree] value=1 ";
			if ($show_configuration["display_category_tree"] == 1)
				$this->body .= "checked";
			$this->body .= "> yes<br><input type=radio name=b[display_category_tree] value=0 ";
			if ($show_configuration["display_category_tree"] == 0)
				$this->body .= "checked";
			$this->body .= "> no </td>\n</tr>\n";
			$this->row_count++;

			//display of category tree
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=50% class=medium_font>\n\t<b>where and if you want to display the category tree:</b>".$this->show_tooltip(9,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<select name=b[category_tree_display]>\n\t\t";
			$this->body .= "<option value=0 ";
			if ($show_configuration["category_tree_display"] == 0)
				$this->body .= "selected";
			$this->body .= ">below subcategory listing only</option>\n\t\t";
			$this->body .= "<option value=1 ";
			if ($show_configuration["category_tree_display"] == 1)
				$this->body .= "selected";
			$this->body .= ">above subcategory listing only</option>\n\t\t";
			$this->body .= "<option value=2 ";
			if ($show_configuration["category_tree_display"] == 2)
				$this->body .= "selected";
			$this->body .= ">above and below subcategory listing</option>\n\t\t";
			$this->body .= "<option value=3 ";
			if ($show_configuration["category_tree_display"] == 3)
				$this->body .= "selected";
			$this->body .= ">do not display category tree</option>\n\t\t";
			$this->body .= "</select>\n\t</td>\n</tr>\n";
			$this->row_count++;

			// New listing limit
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=50% class=medium_font>\n\t<b>category new listing time limit:</b>".$this->show_tooltip(10,1)."</td>\n\t";
			$value_lengths = array(1,2,3,4,5,6,12,18,24,48,72,96,120,240);
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<select name=b[category_new_ad_limit]>\n\t\t";
			$this->body .= "<option value=0>no display of new listing icon</option>";
			reset($value_lengths);
			foreach ($value_lengths as $limit_time)
			{
				$this->body .= "<option value=".$limit_time;
				if ($show_configuration["category_new_ad_limit"] == $limit_time)
					$this->body .= " selected";
				$this->body .= ">".$limit_time." hour(s)</option>";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";
			$this->row_count++;

			//new listing icon url
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>new listing in category icon to use:</b>".$this->show_tooltip(11,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=text name=b[category_new_ad_image] size=60 value=\"".$show_configuration["category_new_ad_image"]."\">\n\t\t";
			$this->body .= "\n\t</td>\n</tr>\n";

			//post login page
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>post login page:</b>".$this->show_tooltip(12,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<select name=b[post_login_page]>\n\t\t";
			$this->body .= "<option value=0 ";
			if ($show_configuration["post_login_page"] == 0)
				$this->body .= "selected";
			$this->body .= ">user management home page</option>\n\t\t";
			$this->body .= "<option value=1 ";
			if ($show_configuration["post_login_page"] == 1)
				$this->body .= "selected";
			$this->body .= ">browsing home page - main categories</option>\n\t\t";
			$this->body .= "</select>\n\t</td>\n</tr>\n";
			$this->row_count++;

			//photo or icon
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>display the thumbnail photo or photo icon:</b>".$this->show_tooltip(13,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=b[photo_or_icon] value=1 ";
			if ($show_configuration["photo_or_icon"] == 1)
				$this->body .= "checked";
			$this->body .= ">photo<br><input type=radio name=b[photo_or_icon] value=2 ";
			if ($show_configuration["photo_or_icon"] == 2)
				$this->body .= "checked";
			$this->body .=">icon\n\t</td>\n</tr>\n";
			$this->row_count++;

			//no image available image
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>image to use if no image uploaded by user:</b>".$this->show_tooltip(14,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=text name=b[no_image_url] size=60 value=\"".$show_configuration["no_image_url"]."\">\n\t\t";
			$this->body .= "\n\t</td>\n</tr>\n";
			$this->row_count++;

			//photo icon url
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>photo icon to use:</b>".$this->show_tooltip(15,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=text name=b[photo_icon_url] size=60 value=\"".$show_configuration["photo_icon_url"]."\">\n\t\t";
			$this->body .= "\n\t</td>\n</tr>\n";
			$this->row_count++;

			//browsing photo thumbnail width and height
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>browsing thumbnail max size:</b>".$this->show_tooltip(16,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=text name=b[thumbnail_max_width] size=5 value=\"".$show_configuration["thumbnail_max_width"]."\">width<bR>
				<input type=text name=b[thumbnail_max_height] size=5 value=\"".$show_configuration["thumbnail_max_height"]."\">height\n\t\t";
			$this->body .= "\n\t</td>\n</tr>\n";
			$this->row_count++;

			//browsing featured photo thumbnail width and height
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>browsing featured thumbnail max size:</b>".$this->show_tooltip(17,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=text name=b[featured_thumbnail_max_width] size=5 value=\"".$show_configuration["featured_thumbnail_max_width"]."\">width<bR>
				<input type=text name=b[featured_thumbnail_max_height] size=5 value=\"".$show_configuration["featured_thumbnail_max_height"]."\">height\n\t\t";
			$this->body .= "\n\t</td>\n</tr>\n";
			$this->row_count++;

			//help icon url
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>help icon to use:</b>".$this->show_tooltip(18,1)."</td>\n\t";
			$this->body .= "<td class=medium_font>\n\t<input type=text name=b[help_image] size=60 value=\"".urldecode($show_configuration["help_image"])."\">\n\t\t";
			$this->body .= "\n\t</td>\n</tr>\n";
			$this->row_count++;

			//sold icon url
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>sold icon to use:</b>".$this->show_tooltip(19,1)."</td>\n\t";
			$this->body .= "<td class=medium_font>\n\t<input type=text name=b[sold_image] size=60 value=\"".urldecode($show_configuration["sold_image"])."\">\n\t\t";
			$this->body .= "\n\t</td>\n</tr>\n";
			$this->row_count++;

			if(($this->is_class_auctions()) || ($this->is_auctions()))
			{
				//buy now image display
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>buy now image:</b>".$this->show_tooltip(28,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<input type=text name=b[buy_now_image] size=60 value=\"".$show_configuration['buy_now_image']."\"></font>\n\t</td>\n</tr>\n";
				$this->row_count++;

				//reserve image display
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>reserve met image:</b>".$this->show_tooltip(29,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<input type=text name=b[reserve_met_image] size=60 value=\"".$show_configuration['reserve_met_image']."\"></font>\n\t</td>\n</tr>\n";
				$this->row_count++;

				//no reserve image display
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>no reserve image:</b>".$this->show_tooltip(30,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<input type=text name=b[no_reserve_image] size=60 value=\"".$show_configuration['no_reserve_image']."\"></font>\n\t</td>\n</tr>\n";
				$this->row_count++;
			}

			//number of listings or search returns to display on a page
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=50% class=medium_font>\n\t<b>number of listings to display on a page:</b>".$this->show_tooltip(20,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<select name=b[number_of_ads_to_display]>\n\t\t";
			for ($i=1;$i<100;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_configuration["number_of_ads_to_display"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";
			$this->row_count++;

			//number of featured listings to display on the category home page
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=50% class=medium_font>\n\t<b>number of featured listings to display on the category home pages:</b>".$this->show_tooltip(21,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<select name=b[number_of_featured_ads_to_display]>\n\t\t";
			for ($i=0;$i<=20;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_configuration["number_of_featured_ads_to_display"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";
			$this->row_count++;

			//category caching
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=50% class=medium_font>\n\t<b>time to cache category listings while browsing:</b>".$this->show_tooltip(22,1)."</td>\n\t";
			$value_lengths = array(1,2,3,4,5,6,12,18,24);
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<select name=b[use_category_cache]>\n\t\t";
			$this->body .= "<option value=0>no caching</option>";
			reset($value_lengths);
			foreach ($value_lengths as $cache_time)
			{
				$this->body .= "<option value=".$cache_time;
				if ($show_configuration["use_category_cache"] == $cache_time)
					$this->body .= " selected";
				$this->body .= ">".$cache_time." hour(s)</option>";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";
			$this->row_count++;

			//use zip code distance calculator
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>use the zip code distance calculator:</b>".$this->show_tooltip(23,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t";
			$this->body .= "<input type=radio name=b[use_zip_distance_calculator] value=1 ";
			if ($show_configuration["use_zip_distance_calculator"] == 1)
				$this->body .= "checked";
			$this->body .= "> yes (use United States Zip Codes) THIS REQUIRES A ZIP-DISTANCE UPGRADE.  YOU MUST CONTACT GEODESIC SOLUTIONS TO ACQUIRE IT.  CURRENTLY
				AVAILABLE FOR UNITED STATES.<br>";
			$this->body .= "<input type=radio name=b[use_zip_distance_calculator] value=2 ";
			if ($show_configuration["use_zip_distance_calculator"] == 2)
				$this->body .= "checked";
			$this->body .= "> yes (use United Kingdom Postal Codes) THIS REQUIRES ZIP-DISTANCE DATA THAT MUST BE PURCHASED FROM A SEPARATE
				COMPANY.  THE COMPANY IS CGI INTERACTIVE BASED IN THE UK.  THEIR WEB ADDRESS IS http://www.cgi-interactive.com AND YOU
				MUST CONTACT THEM TO MAKE ARRANGEMENTS TO PURCHASE THIS DATA.  ONCE THIS DATA IS ACQUIRED AND INSTALLED BY YOU
				THE SOFTWARE WILL NEED NOTHING ELSE.<br>";
			$this->body .= "<input type=radio name=b[use_zip_distance_calculator] value=3 ";
			if ($show_configuration["use_zip_distance_calculator"] == 3)
				$this->body .= "checked";
			$this->body .= "> yes (use 3 letter abbreviation of zip codes) THIS REQUIRES ZIP-DISTANCE DATA THAT MUST BE PURCHASED FROM A SEPARATE
				COMPANY. THEN THE DATA MUST BE MODIFIED TO CONTAIN ONLY THE FIRST THREE LETTERS OF THE POSTAL CODE WITH THE THREE
				LETTER POSTAL CODE BEING THE AVERAGE OF ALL POSTAL CODES CONTAINING THOSE 3 LETTERS IN THE FIRST 3 POSITIONS.<br>";
			$this->body .= "<input type=radio name=b[use_zip_distance_calculator] value=0 ";
			if ($show_configuration["use_zip_distance_calculator"] == 0)
				$this->body .= "checked";
			$this->body .= "> no </td>\n</tr>\n";
			$this->row_count++;

			//turn off use of mainbody tag within search page
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>use preconfigured search form within search page:</b>".$this->show_tooltip(24,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=b[use_search_form] value=0 ";
			if ($show_configuration["use_search_form"] == 0)
				$this->body .= "checked";
			$this->body .= "> use default search form<br><input type=radio name=b[use_search_form] value=1 ";
			if ($show_configuration["use_search_form"] == 1)
				$this->body .= "checked";
			$this->body .="> create my own search form\n\t</td>\n</tr>\n";
			$this->row_count++;

			//log in to contact seller switch
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>user must be logged in to contact seller:</b>".$this->show_tooltip(25,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[seller_contact] value=1 ";
			if ($show_configuration["seller_contact"] == 1)
				$this->body .= "checked";
			$this->body .= "> yes<br><input type=radio name=b[seller_contact] value=0 ";
			if ($show_configuration["seller_contact"] == 0)
				$this->body .= "checked";
			$this->body .= "> no </td>\n</tr>\n";
			$this->row_count++;

			//log in to view listing switch
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>user must be logged in to view listings:</b>".$this->show_tooltip(26,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[subscription_to_view_or_bid_ads] value=1 ";
			if ($show_configuration["subscription_to_view_or_bid_ads"] == 1)
				$this->body .= "checked";
			$this->body .= "> yes<br><input type=radio name=b[subscription_to_view_or_bid_ads] value=0 ";
			if ($show_configuration["subscription_to_view_or_bid_ads"] == 0)
				$this->body .= "checked";
			$this->body .= "> no </td>\n</tr>\n";
			$this->row_count++;

			//default order by while browsing normally

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=50% class=medium_font>\n\t<b>default order of listings while browsing:</b>".$this->show_tooltip(31,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<select name=b[default_display_order_while_browsing]>\n\t\t";
			$order_by_array = array();
			$order_by_array[0] = "default";
			$order_by_array[1] = "price ascending";
			$order_by_array[2] = "price descending";
			//$order_by_array[3] = "placement date ascending";
			$order_by_array[4] = "placement date descending";
			$order_by_array[5] = "title ascending (alphabetical)";
			$order_by_array[6] = "title descending";
			$order_by_array[7] = "city ascending (alphabetical)";
			$order_by_array[8] = "city descending";
			$order_by_array[9] = "state as";
			$order_by_array[10] = "state d";
			$order_by_array[11] = "country as";
			$order_by_array[12] = "country de";
			$order_by_array[13] = "zip ascending";
			$order_by_array[14] = "zip descending";
			$order_by_array[15] = "optional field 1 ascending";
			$order_by_array[16] = "optional field 1 de";
			$order_by_array[17] = "optional field 2 ascending";
			$order_by_array[18] = "optional field 2 de";
			$order_by_array[19] = "optional field 3 ascending";
			$order_by_array[20] = "optional field 3 de";
			$order_by_array[21] = "optional field 4 ascending";
			$order_by_array[22] = "optional field 4 de";
			$order_by_array[23] = "optional field 5 ascending";
			$order_by_array[24] = "optional field 5 de";
			$order_by_array[25] = "optional field 6 ascending";
			$order_by_array[26] = "optional field 6 de";
			$order_by_array[27] = "optional field 7 ascending";
			$order_by_array[28] = "optional field 7 de";
			$order_by_array[29] = "optional field 8 ascending";
			$order_by_array[30] = "optional field 8 de";
			$order_by_array[31] = "optional field 9 ascending";
			$order_by_array[32] = "optional field 9 de";
			$order_by_array[33] = "optional field 10 ascending";
			$order_by_array[34] = "optional field 10 de";

			$order_by_array[45] = "optional field 11 ascending";
			$order_by_array[46] = "optional field 11 de";
			$order_by_array[47] = "optional field 12 ascending";
			$order_by_array[48] = "optional field 12 de";
			$order_by_array[49] = "optional field 13 ascending";
			$order_by_array[50] = "optional field 13 de";
			$order_by_array[51] = "optional field 14 ascending";
			$order_by_array[52] = "optional field 14 de";
			$order_by_array[53] = "optional field 15 ascending";
			$order_by_array[54] = "optional field 15 de";
			$order_by_array[55] = "optional field 16 ascending";
			$order_by_array[56] = "optional field 16 de";
			$order_by_array[57] = "optional field 17 ascending";
			$order_by_array[58] = "optional field 17 de";
			$order_by_array[59] = "optional field 18 ascending";
			$order_by_array[60] = "optional field 18 de";
			$order_by_array[61] = "optional field 19 ascending";
			$order_by_array[62] = "optional field 19 de";
			$order_by_array[63] = "optional field 20 ascending";
			$order_by_array[64] = "optional field 20 de";
			$order_by_array[43] = "business type ascending";
			$order_by_array[44] = "business type de";

			reset ($order_by_array);
			foreach ($order_by_array as $key => $value)
			{
				$this->body .= "<option value=".$key." ";
				if ($key == $show_configuration['default_display_order_while_browsing'])
					$this->body .= "selected";
				$this->body .= ">".$value."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";
			$this->row_count++;

			//url rewrite switch
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>Rewrite URLs:</b>".$this->show_tooltip(32,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<input type=radio name=b[url_rewrite] value=1 ";
			if ($show_configuration["url_rewrite"] == 1)
				$this->body .= "checked";
			$this->body .= "> yes<br><input type=radio name=b[url_rewrite] value=0 ";
			if ($show_configuration["url_rewrite"] == 0)
				$this->body .= "checked";
			$this->body .= "> no </td>\n</tr>\n";
			$this->row_count++;

			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 align=center class=medium_font>\n\t<input type=submit value=\"Save\" name=submit> \n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";

			return true;
		}
		else
		{
			$this->site_configuration_message = $this->internal_error_message;
			return false;
		}

	} //end of function display_browse_configuration_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_browse_configuration($db,$configuration_info=0)
	{
		if ($this->debug_site) echo "hello from the top of update_browse_configuration<br>\n";
		if ($configuration_info)
		{
			$this->sql_query = "update ".$this->site_configuration_table." set
				number_of_browsing_columns = ".$configuration_info["number_of_browsing_columns"].",
				number_of_browsing_subcategory_columns = ".$configuration_info["number_of_browsing_subcategory_columns"].",
				category_tree_display = ".$configuration_info["category_tree_display"].",
				display_category_navigation = ".$configuration_info["display_category_navigation"].",
				display_category_description = ".$configuration_info["display_category_description"].",
				display_category_tree = ".$configuration_info["display_category_tree"].",
				post_login_page = ".$configuration_info["post_login_page"].",
				display_no_subcategory_message = ".$configuration_info["display_no_subcategory_message"].",
				no_image_url = \"".trim($configuration_info["no_image_url"])."\",
				photo_icon_url = \"".$configuration_info["photo_icon_url"]."\",
				sold_image = \"".$configuration_info["sold_image"]."\",
				photo_or_icon = \"".$configuration_info["photo_or_icon"]."\",
				thumbnail_max_height = \"".$configuration_info["thumbnail_max_height"]."\",
				thumbnail_max_width = \"".$configuration_info["thumbnail_max_width"]."\",
				featured_thumbnail_max_height = \"".$configuration_info["featured_thumbnail_max_height"]."\",
				featured_thumbnail_max_width = \"".$configuration_info["featured_thumbnail_max_width"]."\",
				help_image = \"".urlencode($configuration_info["help_image"])."\",
				display_sub_category_ads = \"".$configuration_info["display_sub_category_ads"]."\",
				display_category_count = \"".$configuration_info["display_category_count"]."\",
				browsing_count_format = \"".$configuration_info["browsing_count_format"]."\",
				number_of_ads_to_display = \"".$configuration_info["number_of_ads_to_display"]."\",
				number_of_featured_ads_to_display = \"".$configuration_info["number_of_featured_ads_to_display"]."\",
				number_of_new_ads_to_display = \"".$configuration_info["number_of_new_ads_to_display"]."\",
				use_zip_distance_calculator = \"".$configuration_info["use_zip_distance_calculator"]."\",
				use_search_form = \"".$configuration_info["use_search_form"]."\",
				seller_contact = \"".$configuration_info["seller_contact"]."\",
				category_new_ad_limit = \"".$configuration_info["category_new_ad_limit"]."\",
				category_new_ad_image = \"".$configuration_info["category_new_ad_image"]."\",
				use_category_cache = \"".$configuration_info["use_category_cache"]."\",
				buy_now_image = \"".$configuration_info["buy_now_image"]."\",
				reserve_met_image = \"".$configuration_info["reserve_met_image"]."\",
				no_reserve_image = \"".$configuration_info["no_reserve_image"]."\",
				subscription_to_view_or_bid_ads = \"".$configuration_info["subscription_to_view_or_bid_ads"]."\",
				default_display_order_while_browsing = \"".$configuration_info["default_display_order_while_browsing"]."\",
				url_rewrite = \"".$configuration_info["url_rewrite"]."\"";

			$result = $db->Execute($this->sql_query);
			if ($this->debug_site)
				echo $this->sql_query."<bR>\n";
			if (!$result)
			{
				if ($this->debug_site) echo $this->sql_query."<bR>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			$this->site_configuration_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_browse_configuration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function configuration_home()
	{
				$this->body .= "<table cellpadding=3 cellspacing=0 width=100% border=0 align=center class=".$this->get_row_color().">\n";
					$this->body .= "<tr class=row_color_red>\n\t\t<td colspan=2 class=very_large_font_light><b>Site Setup</b> \n\t\t</td>\n\t</tr>\n\t";
					$this->body .= "<tr class=row_color_red>\n\t\t<td colspan=2 class=medium_font_light>In this section you will configure the necessary functional elements
							as well as the applicable browsing settings of your site. \n\t\t</td>\n\t</tr>\n\t";
					$this->body .= "<tr>\n\t\t<td align=right valign=top><a href=index.php?a=28&z=6><span class=medium_font><b>general</b></span></a>\n\t\t</td>\n\t\t
						<td class=medium_font>general site settings </a>\n\t\t</td>\n\t</tr>\n\t";
					$this->body .= "<tr class=".$this->get_row_color().">\n\t\t<td align=right valign=top><a href=index.php?a=28&z=7><span class=medium_font><b>browsing</b></span></a>\n\t\t</td>\n\t\t
						<td class=medium_font>configure the settings for your user's browsing </a>\n\t\t</td></tr>\n\t";
					//echo "<tr class=".$this->get_row_color().">\n\t\t<td align=right valign=top><a href=index.php?a=28&z=9><span class=medium_font><b>user management template</b></span></a>\n\t\t</td>\n\t\t
					//	<td class=medium_font>set the template for the user management page </a>\n\t\t</td></tr>\n\t";
					$this->body .= "</table>\n";
			return true;
	} //end of function configuration_home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function home_template_form($db)
	{
		$sql_query = "select * from ".$this->site_configuration_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->site_error($sql_query,$db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show = $result->FetchRow();
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=".$this->get_row_color().">\n";
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=28&z=9 method=post>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=very_large_font_light>\n\t<b>User Management Home Template Configuration</b> \n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=medium_font_light>\n\tAdd a template displaying
				the user management (My Account) Home Page on the front side.  This template only controls the placement of label and data fields within the table you provide in the
				box above.  You can control the actual text placed within labels through the \"Pages Management\" menu. You can override the label text by typing text directly into the template but that text will
				always appear no matter the language choice (if you only have one language then this point is useless).  Please
				look closely at the default template to understand how you should also use the font css tags wrapped around
				each label and data field.  Use the css style tag admin, also in the Pages Management menu, to adjust
				css display properties for the listing text and text throughout the site.   \n\t</td>\n</tr>\n";

			$this->body .= "<tr class=".$this->get_row_color()."><td align=right class=medium_font>User Management Home Template </td>";
			$sql_query = "select name,template_id from ".$this->templates_table." order by name";
			//echo $sql_query.'<br>';
			$this->body .= "<td valign=top>\n\t<select name=b[template]>\n\t\t";
			$category_template_result = $db->Execute($sql_query);
			 if (!$category_template_result)
			 {
				$this->error_message = $this->messages[3501];
				return false;
			 }
			 elseif ($category_template_result->RecordCount() > 0)
			 {
			 	$this->body .= "<option value=0";
			 	if ($show_template["template_id"] == $show["home_template"])
					$this->body .= " selected ";
				$this->body .= ">None</option>\n\t";
				while ($show_template = $category_template_result->FetchRow())
				{
					$this->body .= "<option value=".$show_template["template_id"];
					if ($show_template["template_id"] == $show["home_template"])
						$this->body .= " selected ";
					$this->body .= ">".$show_template["name"]."</option>\n\t";
				}
			 }
			$this->body .= "</select></td></tr>";
			if (!$this->admin_demo())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top colspan=2 class=medium_font>\n\t
					<input type=submit name=submit value=\"Save\"> \n\t</td>\n</tr>\n";
			}

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top colspan=2 class=medium_font>\n\t
				Below is the list of template fields possible for the user management home html table.  Place the fields below within the table
				where you would like them to appear.  Next to each field is an explanation of what that field represents.
				The data will be replaced on the fly by this Application. \n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;SECTION_TITLE&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tTitle for this section \n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;PAGE_TITLE&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe title of the page. \n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;DESCRIPTION&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tDescription for this page. \n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;ACTIVE_ADS&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tLink to the users currently active listings. \n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;EXPIRED_ADS&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tLink to the users recently expired listings. \n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;CURRENT_INFO&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tLink to the users information. \n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;PLACE_AD&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tLink to listing. \n\t</td>\n</tr>\n";
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;FAVORITES&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tLink to users favorite listings. \n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;COMMUNICATIONS&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tLink to user's communications. \n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;COMMUNICATIONS_CONFIG&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tLink to user's communication configuration. \n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;SIGNS_AND_FLYERS&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tLink to signs and flyers page. \n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;RENEW_EXTEND_SUBSCRIPTION&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tLink to renew/extend subscription.<br>
				<b>Will only appear if user is a member of a subscription-based price plan</b>. \n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;ADD_MONEY_WITH_BALANCE&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tLink to add money to your account.  <b>This will also display the account balance beside it.</b> \n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;ADD_MONEY&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tLink to add money to your account. \n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr  class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;BALANCE_TRANSACTIONS&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tLink to display all balance transactions that have happened to the clients site balance.</font>\n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr  class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;PAID_INVOICES&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tLink to display all paid invoices if the invoice system is used.</font>\n\t</td>\n</tr>\n";
			$this->row_count++;
			$this->body .= "<tr  class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;UNPAID_INVOICES&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tLink to display all unpaid invoices if the invoice system is used.</font>\n\t</td>\n</tr>\n";
			$this->row_count++;


			//echo "<tr class=row_color_black>\n\t<td colspan=2 align=center class=large_font_light>\n\t
			//	<b> denotes fields used within the affiliate listing display template that is set within the group administration</b></td></tr>\n";
			$this->body .= "</form>\n";
			$this->body .= "</table>\n";
			return true;
		}
		else
		{
			return false;
		}

	} //end of function user_management_template_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_home_template($db,$template_info=0)
	{
		if ($template_info)
		{
			$sql_query = "update ".$this->site_configuration_table." set
				home_template = \"".$template_info["template"]."\"";
			$result = $db->Execute($sql_query);
			//echo $sql_query."<br>\n";
			if (!$result)
			{
				$this->site_error($sql_query,$db->ErrorMsg());
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
	} //end of function update_home_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%


	function ban_ip_form($db)
	{
		$sql_query = "select * from ".$this->site_configuration_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			if ($this->debug_site)
			{
				echo $sql_query."<br>\n";
				echo $db->ErrorMsg()."<br>\n";
			}
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show = $result->FetchNextObject();
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1>\n";
			$this->body .= "<form action=index.php?a=80 method=post>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=very_large_font_light>\n\t<b>IP Ban List Management</b> \n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=medium_font_light>\n\tYou can ban ip from browsing your ad details or using the
				   contact seller feature\n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color1><td align=right valign=top class=medium_font>banned ips</td>";
			$this->sql_query = "select * from ".$this->ip_ban_table;
			//echo $this->sql_query.'<br>';
			$this->body .= "<td valign=top class=medium_font>\n\t";

			$ip_result = $db->Execute($this->sql_query);
			 if (!$ip_result)
			 {
				if ($this->debug_site)
				{
					echo $this->sql_query."<br>\n";
					echo $db->ErrorMsg()."<br>\n";
				}
				return false;
			 }
			 elseif ($ip_result->RecordCount() > 0)
			 {
			 	$this->body .= "<table width=100%>";
				while ($show_ip = $ip_result->FetchNextObject())
				{
					$this->body .= "<tr><td class=medium_font>".$show_ip->IP."</td>\n";
					$this->body .= "<td class=medium_font><a href=index.php?a=80&c=".$show_ip->IP_ID." class=medium_font><img src=./admin_images/btn_admin_delete.gif border=0></a></td>\n</tr>";
				}
				 $this->body .= "&nbsp;</table>";
			 }
			 elseif ($ip_result->RecordCount() == 0)
			 {
			 	$this->body .= "there are currently no banned ips";
			 }

			$this->body .= "&nbsp;</td></tr>";

			$this->body .= "<tr class=row_color2>\n\t<td class=medium_font align=right>\n\t
				ip to ad </td><td><input type=text name=b ><input type=submit name=insert value=\"insert ip\"> \n\t</td>\n</tr>\n";

			$this->body .= "</form>\n";
			$this->body .= "</table>\n";
			return true;
		}
		else
		{
			if ($this->debug_site) echo "no site configuration result<Br>\n";		
			return false;
		}

	} //end of function ban_ip_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_ip_from_list($db,$ip_id=0)
	{
		if ($ip_id)
		{
			$sql_query = "delete from  ".$this->ip_ban_table." where ip_id = ".$ip_id;
			$result = $db->Execute($sql_query);
			if ($this->debug_site) echo $sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug_site)
				{
					echo $sql_query."<br>\n";
					echo $db->ErrorMsg()."<br>\n";
				}
				return false;
			}
			else
			{
				$this->update_ip_ban_check($db);
				return true;
			}
		}
		else
		{
			return false;
		}
	} //end of function delete_ip_from_list

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_ip_to_ban_list($db,$ip=0)
	{
		if ($ip)
		{
			//check ip format
			$sql_query = "insert into ".$this->ip_ban_table." (ip) values (\"".$ip."\")";
			$result = $db->Execute($sql_query);
			if ($this->debug_site) echo $sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug_site)
				{
					echo $sql_query."<br>\n";
					echo $db->ErrorMsg()."<br>\n";
				}
				return false;
			}
			else
			{
				$this->update_ip_ban_check($db);
				return true;
			}
		}
		else
		{
			if ($this->debug_site) echo "no ip to inser<BR>\n";
			return false;
		}
	} //end of function insert_ip_to_ban_list

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_ip_ban_check($db)
	{
		//check the total number of ips in the ban table
		//if there are more than 0 set the site configuration variable
		//took check the ip ban table
		$sql_query = "select * from  ".$this->ip_ban_table;
		$count_result = $db->Execute($sql_query);
		if ($this->debug_site) echo $sql_query."<br>\n";
		if (!$count_result)	
		{			
			if ($this->debug_site)
			{
				echo $sql_query."<br>\n";
				echo $db->ErrorMsg()."<br>\n";
			}
			return false;
		}
		else
		{	
			if ($count_result->RecordCount() > 0)
			{
				$use_ip_ban = 1;	
			}
			else 
			{
				$use_ip_ban = 0;
			}
			$sql_query = "update ".$this->site_configuration_table." set ip_ban_check = ".$use_ip_ban;
			$update_result = $db->Execute($sql_query);
			if ($this->debug_site) echo $sql_query."<br>\n";
			if (!$update_result)	
			{				
				if ($this->debug_site)
				{
					echo $sql_query."<br>\n";
					echo $db->ErrorMsg()."<br>\n";
				}	
				return false;
			}
		}		
	} //end of function update_ip_ban_check
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Site_configuration
?>