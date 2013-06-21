<? //admin_site_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Admin_site {

	var $admin_site_name = "GeoClassAuctions Administration";


	//tables within the database
	var $block_email_domains = "geodesic_email_domains";
	var $classifieds_table = "geodesic_classifieds";
	var $classifieds_expired_table = "geodesic_classifieds_expired";
	var $sell_questions_table = "geodesic_classifieds_sell_questions";
	var $sell_types_table = "geodesic_classifieds_sell_question_types";
	var $classified_extra_table = "geodesic_classifieds_ads_extra";
	var $classified_categories_table = "geodesic_categories";
	var $classified_filters_table = "geodesic_classifieds_filters";
	var $classified_categories_languages_table = "geodesic_classifieds_categories_languages";
	var $classified_filters_languages_table = "geodesic_classifieds_filters_languages";
	var $logins_table = "geodesic_logins";
	var $classified_sell_choices_table = "geodesic_classifieds_sell_question_choices";
	var $sell_choices_types_table = "geodesic_classifieds_sell_question_types";
	var $classified_questions_table = "geodesic_classifieds_sell_questions";
	var $questions_table = "geodesic_classifieds_sell_questions";
	var $states_table = "geodesic_states";
	var $text_message_table = "geodesic_text_messages";
	var $text_languages_table = "geodesic_text_languages";
	var $text_languages_messages_table = "geodesic_text_languages_messages";
	var $text_page_table = "geodesic_text_pages";
	var $text_subpages_table = "geodesic_text_subpages";
	var $confirm_table = "geodesic_confirm";
	var $confirm_email_table = "geodesic_confirm_email";
	var $userdata_table = "geodesic_userdata";
	var $userdata_history_table = "geodesic_userdata_history";
	var $badwords_table = "geodesic_text_badwords";
	var $countries_table = "geodesic_countries";
	var $ad_configuration_table = "geodesic_classifieds_ad_configuration";
	var $choices_table = "geodesic_choices";
	var $html_allowed_table = "geodesic_html_allowed";
	var $form_messages_table = "geodesic_classifieds_messages_form";
	var $past_messages_table = "geodesic_classifieds_messages_past";
	var $past_messages_recipients_table = "geodesic_classifieds_messages_past_recipients";
	var $site_configuration_table = "geodesic_classifieds_configuration";
	var $classified_ad_filter_table = "geodesic_ad_filter";
	var $classified_ad_filter_categories_table = "geodesic_ad_filter_categories";
	var $user_communications_table = "geodesic_user_communications";
	var $extra_pages_table = "geodesic_classifieds_extra_pages";
	var $file_types_table = "geodesic_file_types";
	var $images_table = "geodesic_classifieds_images";
	var $images_urls_table = "geodesic_classifieds_images_urls";
	var $classified_groups_table = "geodesic_groups";
	var $user_groups_price_plans_table = "geodesic_user_groups_price_plans";
	var $classified_expirations_table = "geodesic_classifieds_expirations";
	var $user_credits_table = "geodesic_classifieds_user_credits";
	var $credit_choices = "geodesic_classifieds_credit_choices";
	var $classified_payment_types_table = "geodesic_payment_choices";
	var $classified_user_subscriptions_table = "geodesic_classifieds_user_subscriptions";
	var $classified_subscription_choices_table = "geodesic_classifieds_subscription_choices";
	var $price_plan_table = "geodesic_classifieds_price_plans";
	var $classified_price_plans_categories_table = "geodesic_classifieds_price_plans_categories";
	var $price_plans_increments_table = "geodesic_classifieds_price_increments";
	var $price_plans_extras_table = "geodesic_classifieds_price_plans_extras";
	var $font_page_table = "geodesic_font_pages";
	var $font_sub_page_table = "geodesic_font_subpages";
	var $font_element_table = "geodesic_font_elements";
	var $classified_payment_choices_table = "geodesic_payment_choices";
	var $paypal_transaction_table = "geodesic_paypal_transactions";
	var $worldpay_configuration_table = "geodesic_worldpay_settings";
	var $worldpay_transaction_table = "geodesic_worldpay_transactions";
	var $cc_choices = "geodesic_credit_card_choices";
	var $banners_table = "geodesic_banners";
	var $banner_category_zones_table = "geodesic_banners_category_zones";
	var $registration_configuration_table = "geodesic_registration_configuration";
	var $registration_choices_table = "geodesic_registration_question_choices";
	var $registration_choices_types_table = "geodesic_registration_question_types";
	var $currency_types_table = "geodesic_currency_types";
	var $classified_price_plan_lengths_table = "geodesic_price_plan_ad_lengths";
	var $classified_subscription_holds_table = "geodesic_classifieds_user_subscriptions_holds";
	var $classified_discount_codes_table = "geodesic_classifieds_discount_codes";
	var $attached_price_plans = "geodesic_group_attached_price_plans";
	var $balance_transactions = "geodesic_balance_transactions";
	var $invoices_table = "geodesic_invoices";
	var $version_table = "geodesic_version";
	var $sessions_table = "geodesic_sessions";
	var $nochex_transaction_table = "geodesic_nochex_transactions";
	var $nochex_settings_table = "geodesic_nochex";
	var $auction_payment_types_table = "geodesic_payment_types";
	var $final_fee_table = "geodesic_auctions_final_fee_price_increments";

	var $pages_table = "geodesic_pages";
	var $pages_sections_table = "geodesic_pages_sections";
	var $pages_fonts_table = "geodesic_pages_fonts";
	var $pages_text_table = "geodesic_pages_messages";
	var $pages_text_languages_table = "geodesic_pages_messages_languages";
	var $pages_languages_table = "geodesic_pages_languages";
	var $templates_table = "geodesic_templates";
	var $templates_history_table = "geodesic_templates_history";
	var $pages_templates_table = "geodesic_pages_templates";
	var $affiliate_templates_table = "geodesic_pages_templates_affiliates";
	var $pages_modules_table = "geodesic_pages_modules";
	var $pages_modules_sections_table = "geodesic_pages_modules_sections";
	var $filters_table = "geodesic_classifieds_filters";
	var $filters_languages_table = "geodesic_classifieds_filters_languages";

	var $bid_table = "geodesic_auctions_bids";
	var $autobid_table = "geodesic_auctions_autobids";
	var $increments_table = "geodesic_auctions_increments";
	var $feedbacks_table = "geodesic_feedbacks";
	var $feedback_icons_table = "geodesic_auctions_feedback_icons";
	var $ip_ban_table = "geodesic_banned_ips";

	var $large_font;
	var $medium_font;
	var $small_font;
	var $font;
	var $font_color1;
	var $font_color2;

	var $extremely_large_font_tag = "<font face=arial,helvetica size=6 color=#000000>";
	var $very_large_font_tag = "<font face=arial,helvetica size=4 color=#000000>";
	var $large_font_tag = "<font face=arial,helvetica size=3 color=#000000>";
	var $medium_error_font_tag = "<font face=arial,helvetica size=2 color=#880000>";
	var $medium_font_tag = "<font face=arial,helvetica size=2 color=#000000>";
	var $small_font_tag = "<font face=arial,helvetica size=1 color=#000000>";
	var $very_large_font_tag_light = "<font face=arial,helvetica size=4 color=#FFFFFF>";
	var $large_font_tag_light = "<font face=arial,helvetica size=3 color=#FFFFFF>";
	var $medium_font_tag_light = "<font face=arial,helvetica size=2 color=#FFFFFF>";
	var $small_font_tag_light = "<font face=arial,helvetica size=1 color=#FFFFFF>";
	var $row_color_black = "#000000";
	var $row_color_red = "#000099";
	var $row_color1 = "#cccccc";
	var $row_color2 = "#dddddd";
	var $row_color3 = "#bbbbbb";
	//var $row_count;

	// Template data
	var $header_html = "";
	var $additional_header_html = "";
	var $additional_body_tag_attributes = " style='margin:0;'";
	var $footer_html = "";
	var $template = "";
	var $title = "";
	var $description = "";
	var $body = "";
	var $header_image = "";

	var $messages = array();
	var $data_missing_error_message = "Your request could not be completed: missing data";
	var $internal_error_message = "There was an internal error";
	var $data_error_message = "Not enough data to complete request";
	var $page_text_error_message = "No text connected to this page";
	var $no_pages_message = "No pages to list";
	var $row_count = 0;
	var $sql_query;
	var $category_dropdown_name_array = array();
	var $category_dropdown_id_array = array();
	var $page_id;

	var $error_message;

	var $page_widths = array(600,760,980,1110);

	var $debug = 0;
	var $debug_attach_modules = 0;

	var $category_tree_array = array();
	var $subcategory_array = array();
	var $images_to_display = array();
	var $dropdown_body;
	var $configuration_data;
	var $ad_configuration_data;
	var $stage = 0;
	var $time_shift = 0;
	var $time_shift_i = 0;

	var $product_configuration;

	var $admin_icon;


	//	*****************************************
	//	*****************************************
	//	*****************************************
	//	*****************************************
	//	TODO Fix this later
	var $auctions_table = "geodesic_classifieds";
	//	*****************************************
	//	*****************************************
	//	*****************************************


//########################################################################

	function Admin_site($db=0, $product_configuration=0)
	{
		if ($db)
		{
			$this->sql_query = "select * from ".$this->site_configuration_table;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$this->configuration_data = $result->FetchRow();
			}
		}

		if ($product_configuration)
			$this->product_configuration = $product_configuration;

		$this->set_shifted_time($db);
	} //end of function Site

//########################################################################

	function admin_header($db, $menu=0)
	{
		include_once("admin_header.php");
		return true;
	} //end of function admin_header

//########################################################################

	function admin_footer($db)
	{
		include_once("admin_footer.php");
		return true;
	} //end of function admin_footer

//########################################################################

	function admin_home($db)
	{
		include_once("admin_home.php");

		// Check for existence of sql folder
		if(is_dir("../sql/"))
		{
			echo "<center><font size=4 color=red>Security Alert.  Please delete the sql directory from your web server.</font></center>";
		}

		// Check for existence of setup folder
		if(is_dir("../setup/"))
		{
			echo "<center><font size=4 color=red>Security Alert.  Please delete the setup directory from your web server.</font></center>";
		}

		// Check for existence of setup folder
		if(is_dir("../upgrade/"))
		{
			echo "<center><font size=4 color=red>Security Alert.  Please delete the upgrade directory from your web server.</font></center>";
		}

		// Display it all
		echo $this->header_html.$this->footer_html;

		return true;
	} //end of function admin_home

//########################################################################

	function admin_menu($db,$menu_type=0)
	{
		if ($this->debug) echo $menu_type." is menu type<br>\n";
		$this->get_configuration_data($db);
		$this->body .= "<table cellspacing=1 cellpadding=1 border=0 align=center width=152>\n\t";
		if ($menu_type)
			$this->body .= "<tr align=center class=row_color_black>\n\t<td class=medium_font_light>\n\tAdministration Menu \n\t</td>\n</tr>\n";
		else
			$this->body .= "<tr align=center class=row_color_black>\n\t<td class=medium_font_light>\n\tAdministration Menu \n\t</td>\n</tr>\n";
		include("../config.php");
		if ($demo)
		{
			echo "<tr class=row_color_red>\n\t<td>\n\t<a href=\"../../enterprise/index.php\" target=_blank>view changes</a> \n\t</td></tr>\n\t";
		}
		else
		{
			if ($menu_type)
				echo "<tr class=row_color_red>\n\t<td>\n\t<a href=".$this->configuration_data["classifieds_url"]." target = _blank>view changes</a> \n\t</td></tr>\n\t";
			else
				echo "<tr class=row_color_red>\n\t<td>\n\t<a href=".$this->configuration_data["classifieds_url"]." target = _blank>view changes</a> \n\t</td></tr>\n\t";
		}
		if ($menu_type)
			$this->body .= "<tr class=row_color_red><td><a href=index.php>admin home</a></td></tr>\n\t";
		else
			$this->body .= "<tr class=row_color_red><td><a href=index.php>admin home</a></td></tr>\n\t";

		$this->body .= "<tr class=row_color_red><td><a href=http://www.geodesicsolutions.com/support target=new>help</a></td></tr>\n\t";

		if ($menu_type == 28)
		{
			$this->body .= "<tr class=row_color_red><td><a href=index.php?a=28>site configuration </a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=28&z=6>general</a></td>\n\t</tr>\n\t";
			//$this->body .= "<tr><td align=right><a href=index.php?a=28&z=1>header</a></td>\n\t</tr>\n\t";
			//$this->body .= "<tr><td align=right><a href=index.php?a=28&z=2>footer</a></td>\n\t</tr>\n\t";
			//$this->body .= "<tr><td align=right><a href=index.php?a=28&z=4>dimensions/colors</a></td>\n\t</tr>\n\t";
			//$this->body .= "<tr><td align=right><a href=index.php?a=28&z=5>menu bars</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=28&z=7>browsing</a></td>\n\t</tr>\n\t";
			//$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=28&z=9>user management template</a></td>\n\t</tr>\n\t";
			//$this->body .= "<tr><td align=right><a href=index.php?a=28&z=8>columns</a></td>\n\t</tr>\n\t";
		}
		else
			$this->body .= "<tr class=row_color_red><td><a href=index.php?a=28>site configuration</a></td>\n\t</tr>\n\t";


		if ($menu_type == 23)
		{
			$this->body .= "<tr class=row_color_red><td><a href=index.php?a=23>ad configuration</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=23&r=1>images configuration</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=23&r=4>fields to use</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=23&r=2>length of listings</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=23&r=3>image file types</a></td>\n\t</tr>\n\t";
			//$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=23&r=5>ad templates</a></td>\n\t</tr>\n\t";
			//$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=23&r=7>extra questions</a></td>\n\t</tr>\n\t";
			//$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=23&r=9>extra checkboxes</a></td>\n\t</tr>\n\t";
			//$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=23&r=15>full size images template</a></td>\n\t</tr>\n\t";
			//$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=23&r=19>popup image template</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=23&r=6>ad extras</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=66>attention getters</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=23&r=10>flyer forms</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=23&r=11>sign forms</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=23&r=14>currency types form</a></td>\n\t</tr>\n\t";
		}
		else
			$this->body .= "<tr class=row_color_red><td><a href=index.php?a=23>ad configuration</a></td>\n\t</tr>\n\t";

		if (($menu_type == 7) || ($menu_type == 32))
		{
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=7>categories </a></td>\n\t</tr>\n\t";
			//$this->body .= "<tr><td align=right><a href=index.php?a=32><span class=small_font>dropdown configuration</span></a></td>\n\t</tr>\n\t";
		}
		else
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=7>categories </a></td>\n\t</tr>\n\t";

		if ($menu_type == 68)
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=68>filters </a></td>\n\t</tr>\n\t";
		else
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=68>filters </a></td>\n\t</tr>\n\t";

		if ($menu_type == 26)
		{
			$this->body .= "<tr class=row_color_red><td><a href=index.php?a=26>registration configuration</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=26&z=1>generals</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=26&z=2>block email domains</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=26&b=1&z=4>confirmations</a></td>\n\t</tr>\n\t";
		}
		else
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=26>registration configuration</a></td>\n\t</tr>\n\t";

		if ($menu_type == 21)
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=21>edit states</a></td>\n\t</tr>\n\t";
		else
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=21>edit states</a></td>\n\t</tr>\n\t";

		if ($menu_type == 22)
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=22>edit countries</a></td>\n\t</tr>\n\t";
		else
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=22>edit countries</a></td>\n\t</tr>\n\t";

		if ($menu_type ==24)
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=24>allowed html</a></td>\n\t</tr>\n\t";
		else
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=24>allowed html</a></td>\n\t</tr>\n\t";

		if ($menu_type ==15)
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=15>badwords</a></td>\n\t</tr>\n\t";
		else
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=15>badwords</a></td>\n\t</tr>\n\t";

		if ($menu_type ==19)
		{
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=19>list users</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=19&b[order_by]=1>by username</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=19&b[order_by]=2>by lastname</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=19&b[order_by]=3>by latest joined</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=19&b[order_by]=4>by earliest joined</a></td>\n\t</tr>\n\t";

		}
		else
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=19>list users</a></td>\n\t</tr>\n\t";

		if ($menu_type ==16)
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=16>search users</a></td>\n\t</tr>\n\t";
		else
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=16>search users</a></td>\n\t</tr>\n\t";

		//$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=><span class=small_font>backup</span></a></td>\n\t</tr>\n\t";
		if ($menu_type ==51)
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=51>admin password</a></td>\n\t</tr>\n\t";
		else
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=51>admin password</a></td>\n\t</tr>\n\t";

		if ($menu_type == 25)
		{
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=25>admin messaging</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=25&x=1>send message</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=25&x=2>form messages</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=25&x=3>message history</a></td>\n\t</tr>\n\t";
		}
		else
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=25>admin messaging</a></td>\n\t</tr>\n\t";
		//if ($menu_type ==33)
			//$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=33>extra pages</a></td>\n\t</tr>\n\t";
		//else
			//$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=33>extra pages</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color_red><td ><a href=index.php?a=35>database backup</a></td>\n\t</tr>\n\t";
		if ($menu_type ==36)
		{
			$this->body .= "<tr class=row_color_red><td align=left><a href=index.php?a=36>user groups</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=36&b=1>add user group</a></td>\n\t</tr>\n\t";
		}
		else
			$this->body .= "<tr class=row_color_red><td align=left><a href=index.php?a=36>user groups</a></td>\n\t</tr>\n\t";
		if (($menu_type ==37) || ($menu_type == 75))
		{
			$this->body .= "<tr class=row_color_red><td align=left><a href=index.php?a=37>price plans</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=37&b=1>add price plan</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=75>discount codes</a></td>\n\t</tr>\n\t";
		}
		else
			$this->body .= "<tr class=row_color_red><td align=left><a href=index.php?a=37>price plans</a></td>\n\t</tr>\n\t";

		if ($menu_type ==39)
		{
			$this->body .= "<tr class=row_color_red><td><a href=index.php?a=39>payments</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=39&b=6>currency designation</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=39&b=1>charge?</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=39&b=2>payment types</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=39&b=3>paypal configuration</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=39&b=8>worldpay configuration</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=39&b=5>credit card configuration</a></td>\n\t</tr>\n\t";
		}
		else
			$this->body .= "<tr class=row_color_red><td><a href=index.php?a=39>payments</a></td>\n\t</tr>\n\t";
		$this->body .= "<tr class=row_color_red><td align=left><a href=index.php?a=40>transactions</a></td>\n\t</tr>\n\t";
		if (($this->configuration_data["use_account_balance"]) && (!$this->configuration_data["positive_balances_only"]))
			$this->body .= "<tr class=row_color_red><td align=left><a href=index.php?a=78>invoices</a></td>\n\t</tr>\n\t";
		$this->body .= "<tr class=row_color_red><td align=left><a href=index.php?a=40&z=1>unapproved listings</a></td>\n\t</tr>\n\t";

		if ($menu_type == 43)
		{
			$this->body .= "<tr class=row_color_red><td><a href=index.php?a=43>banner ads</a></td>\n\t</tr>\n\t";
			//$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=43&z=1>list banners</a></td>\n\t</tr>\n\t";
			//$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=43&z=4>add banner</a></td>\n\t</tr>\n\t";
		}
		else
			$this->body .= "<tr class=row_color_red><td><a href=index.php?a=43>banner ads</a></td>\n\t</tr>\n\t";

		$this->body .= "<tr class=row_color_red><td><a href=index.php?a=44>pages</a></td>\n\t</tr>\n\t";

		$this->body .= "<tr class=row_color_red><td><a href=index.php?a=109>text search</a></td>\n\t</tr>\n\t";

		//$this->body .= "<tr class=row_color_red><td><a href=index.php?a=74>page modules</a></td>\n\t</tr>\n\t";
		$this->body .= "<tr class=row_color_red><td><a href=index.php?a=79>page modules</a></td>\n\t</tr>\n\t";
		$this->body .= "<tr class=row_color_red><td><a href=index.php?a=45>templates</a></td>\n\t</tr>\n\t";
		$this->body .= "<tr class=row_color_red><td><a href=index.php?a=30>languages</a></td>\n\t</tr>\n\t";
		if($menu_type == 108)
		{
			$this->body .= "<tr class=row_color_red><td><a href=index.php?a=108>CSS management</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=108&b=5>Global CSS fonts</a></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color3><td align=right><a href=index.php?a=108&b=6>Global CSS colors</a></td>\n\t</tr>\n\t";
		}
		else
			$this->body .= "<tr class=row_color_red><td><a href=index.php?a=108>CSS management</a></td>\n\t</tr>\n\t";
		$this->body .= "<tr class=row_color_red><td><a href=index.php?a=100000>api integration</a></td>\n\t</tr>\n\t";
		$this->body .= "<tr>\n\t<td><a href=index.php?a=104><span class=medium_font><img src=admin_images/btn_admin_logout.gif alt=logout border=0></a></span></td>\n</tr>\n";
		$this->body .= "</table>\n\t";
		return true;
	} //end of function admin_menu

//########################################################################

	function badwords ($msg,$mod)
	{
		$msg=wordwrap_msg($msg);
    		$eachword = explode(" " , eregi_replace("<BR>"," ",$msg));		// temp remove <BR>
		$result = mysql_db_query($database, "SELECT * FROM badwords") or died("Query Error");
		while ($db = mysql_fetch_array($result)) {
			for ($i=0; $i<count($eachword); $i++) {
				if (is_int(strpos($eachword[$i],$db[badword]))) {
					if ($mod) {
		        			$msg = eregi_replace($eachword[$i], "<span class=\"censored\">".$eachword[$i]."</span>", $msg); // Badword
			    		}
			    		else {
						$msg = eregi_replace($eachword[$i], str_repeats("*", strlen($eachword[$i])), $msg); // Badword
		    			}
				}
	    		}
		}
	return $msg;
	}
//########################################################################

	function strip_tags ($info)
	{
		$info = str_replace("'", "''", $info);
		$info = stripslashes($info);
		$info = strip_tags($info);
		return $info;
	}

//########################################################################

	function push_messages_into_array($result)
	{
		//take the database message result and push the contents into an array
		while ($show = $result->FetchRow())
		{
			$this->messages[$show["message_id"]] = $show["display"];
		}
	} //end of function push_messages_into_array

//########################################################################

	function push_configuration_into_array($result)
	{
		//take the database message result and push the contents into an array
		while ($show = $result->FetchRow())
		{
			$configuration[$show["reference"]] = $show["value"];
		}
		return $configuration;
	} //end of function push_messages_into_array

//########################################################################

	function site_error($db_error=0,$file=0,$line=0)
	{
		//check to see if debugging
		if ($this->debug)
		{
			echo "<table cellpadding=3 cellspacing=1 border=0>
					<tr>
						<td class=very_large_font>
							There has been a database error
						</td>
					</tr>
					<tr>
						<td class=medium_error_font>";
			if ($db_error)
				echo "		With the following sql error:".$db_error."<br>";
			if ($file || $line)
				echo "		This <b>site_error()</b> was called from . . .
							".(($file) ? "<br>FILE = <b>$file</b>" : "")."
							".(($line) ? "<br>LINE = <b>$line</b>" : "");
			echo "		</td>
					</tr>
				</table>";
		}
		else
		{
			echo "<table cellpadding=3 cellspacing=1 border=0>\n";
			echo "<tr>\n\t<td class=very_large_font>There has been a error.<br>
				Please try again. \n\t</td>\n</tr>\n";
			echo "</table>\n";
		}
	} //end of function site_error

//#########################################################################

	function show_state_dropdown ($db,$state,$name)
	{
		$this->sql_query = "SELECT * FROM ".$this->states_table." order by display_order, name";
		$state_result = $db->Execute($this->sql_query);
		if (!$state_result)
		{
			return false;
		}
		else
		{
			$body .="
				<select name=".trim($name)." class=data_field_values>
					<option value=\"\">choose a state</option>";
			while ($show = $state_result->FetchRow())
			{
				$body .="
					<option value=\"".trim($show["abbreviation"])."\""
						.(($state==trim($show["abbreviation"]) || $state==trim($show["name"])) ? " selected" : "").">
						".trim($show["name"])."
					</option>";
			}
			$body .="
				</select>";
		}
		return $body;
	}// end of function show_state_dropdown

//#########################################################################

	function show_country_dropdown ($db,$country,$name)
	{
		$this->sql_query = "SELECT * FROM ".$this->countries_table." order by display_order, name";
		$country_result = $db->Execute($this->sql_query);
		if (!$country_result)
		{
			return false;
		}
		else
		{
			$body .="
				<select name=".trim($name)." class=data_field_values>
					<option value=\"\">choose a country</option>";
			while ($show = $country_result->FetchRow())
			{
				$body .="
					<option value=\"".trim($show["abbreviation"])."\""
						.(($country==trim($show["abbreviation"]) || trim($country==$show["name"])) ? " selected" : "").">
						".trim($show["name"])."
					</option>";
			}
			$body .="
				</select>";
		}
		return $body;
	}// end of function show_country_dropdown

//########################################################################

	function basic_input_box($input_title,$explanation,$input_name,$input_value="",$error="")
	{
		echo "<tr>\n\t<td valign=top align=right class=medium_font>".$input_title."<br>";
  		if (strlen(trim($error)) > 0) {
   			echo $error."<br>\n\t";
		}
   		echo "<span class=small_font>".$explanation."</span>\n\t</td>\n\t";
   		echo "<td valign=top class=medium_font>\n\t<input type=text name=".$input_name." length=30 ";
 		if (strlen(trim($input_value)) > 0)
   			echo "value=\"".$input_value."\"";
   		echo " maxlength=100>\n\t";
		echo "</td>\n</tr>\n";
	}

//########################################################################

	function get_category_tree($db,$category)
	{
		$i = 0;
		$category_next = $category;
		do
		{
			$this->sql_query = "select category_id,parent_id,category_name from ".$this->classified_categories_table."
				where category_id = ".$category_next;
			$category_result =  $db->Execute($this->sql_query);

			//$category = array();

			//echo $this->sql_query." is the query<br>\n";
			if (!$category_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3501];
				return false;
			}
			elseif ($category_result->RecordCount() == 1)
			{
				$show_category = $category_result->FetchRow();
				$this->sql_query = "select category_name from ".$this->classified_categories_languages_table."
					where category_id = ".$category_next." and language_id = 1";
				$category_name_result =  $db->Execute($this->sql_query);
				if (!$category_name_result)
				{
					return false;
				}
				elseif ($category_name_result->RecordCount() == 1)
				{
					$show_category_name = $category_name_result->FetchRow();
					//echo $i." is i before increment in get_category_tree<br>\n";
					//$category[$i] = array("parent_id"=>$show_category["parent_id"],"category_name"=>$show_category["category_name"],"category_id"=>$show_category["category_id"]);
					$this->category_tree_array[$i]["parent_id"]  = $show_category["parent_id"];
					$this->category_tree_array[$i]["category_name"] = urldecode(stripslashes($show_category_name["category_name"]));
					$this->category_tree_array[$i]["category_id"]   = $show_category["category_id"];
					//echo $this->category_tree_array[$i]["category_id"]." is the category id<br>\n";
					$i++;
					$category_next = $show_category["parent_id"];
				}
				else
					return false;
			}
			else
			{
				//echo "wrong return<Br>\n";
				return false;
			}

     		} while ( $show_category["parent_id"] != 0 );

     		return true;

	} // end of function get_category_tree($category)

//########################################################################

	function get_category_name($db,$category_id=0)
	{
		if ($category_id)
		{
			$this->sql_query = "select category_name from ".$this->classified_categories_languages_table." where language_id = 1 and category_id = ".$category_id;
			$category_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$category_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($category_result->RecordCount() == 1)
			{
				$show = $category_result->FetchRow();
				return urldecode(stripslashes($show["category_name"]));
			}
			else
			{
				//just display the user_id
				return false;
			}

		}
		else
		{
			return "Main";
		}
	} //end of function get_category_name

//########################################################################

	function get_category_description($db,$category_id=0)
	{
		if ($category_id)
		{
			$this->sql_query = "select description from ".$this->classified_categories_languages_table." where language_id = 1 and category_id = ".$category_id;
			$category_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$category_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($category_result->RecordCount() == 1)
			{
				$show = $category_result->FetchRow();
				return urldecode(stripslashes($show["description"]));
			}
			else
			{
				//just display the user_id
				return false;
			}

		}
		else
		{
			return "Main";
		}
	} //end of function get_category_description

//########################################################################

	function get_section($db,$section_id=0)
	{
		if ($section_id)
		{
			$this->sql_query = "select * from ".$this->pages_sections_table." where section_id = ".$section_id;
			$section_result = $db->Execute($this->sql_query);
			if (!$section_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($section_result->RecordCount() == 1)
			{
				$show = $section_result->FetchRow();
				return $show;
			}
			else
			{
				//just display the user_id
				return false;
			}

		}
		return true;
	} //end of function get_section

//########################################################################

	function get_page($db,$page_id=0)
	{
		if ($page_id)
		{
			$this->sql_query = "select * from ".$this->pages_table." where page_id = ".$page_id;
			$page_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$page_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($page_result->RecordCount() == 1)
			{
				$show = $page_result->FetchRow();
				return $show;
			}
			else
			{
				//just display the user_id
				return false;
			}

		}
		return true;
	} //end of function get_page

//########################################################################

	function get_template($db,$template_id=0)
	{
		if ($template_id)
		{
			$this->sql_query = "select * from ".$this->templates_table." where template_id = ".$template_id;
			$template_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$template_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($template_result->RecordCount() == 1)
			{
				$show = $template_result->FetchRow();
				return $show;
			}
			else
			{
				//just display the user_id
				return false;
			}

		}
		else
		{
			return false;
		}
	} //end of function get_template

//########################################################################

	function get_price_plan_name($db,$price_plan_id=0)
	{
		if ($price_plan_id)
		{
			$this->sql_query = "select name from ".$this->price_plan_table." where price_plan_id = ".$price_plan_id;
			$price_plan_result = $db->Execute($this->sql_query);
			if (!$price_plan_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($price_plan_result->RecordCount() == 1)
			{
				$show = $price_plan_result->FetchRow();
				return $show["name"];
			}
			else
			{
				//just display the user_id
				return false;
			}

		}
		else
		{
			return false;
		}
	} //end of function get_price_plan_name

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_price_plan($db,$price_plan_id=0)
	{
		if ($price_plan_id)
		{
			$this->sql_query = "select * from ".$this->price_plan_table." where price_plan_id = ".$price_plan_id;
			$price_plan_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$price_plan_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($price_plan_result->RecordCount() == 1)
			{
				$show = $price_plan_result->FetchRow();
				return $show;
			}
			else
			{
				//just display the user_id
				return false;
			}

		}
		else
		{
			return false;
		}
	} //end of function get_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_current_status($db,$user_id=0)
	{
		if ($user_id)
		{
			$this->sql_query = "select status from ".$this->logins_table." where id = ".$user_id;
			$result = $db->Execute($this->sql_query);

			if (!$result)
			{
				//echo $this->sql_query." is the state query<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_status = $result->FetchRow();
				return $show_status["status"];
			}
			else
			{
				$this->error_message = $this->data_error_message;
				return false;
			}
		}
		else
		{
			//no user id
			return false;
		}
	} //end of function get_current_status

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_user_data($db,$user_id=0)
	{
		if ($user_id)
		{
			$this->sql_query = "select * from ".$this->userdata_table." where id = ".$user_id;
			$result = $db->Execute($this->sql_query);

			if (!$result)
			{
				//echo $this->sql_query." is the state query<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_user = $result->FetchRow();
				return $show_user;
			}
			else
			{
				$this->error_message = $this->data_error_message;
				return false;
			}
		}
		else
		{
			//no user id
			return false;
		}
	} //end of function get_user_data

//########################################################################

	function display_user_data($db,$user_id=0)
	{
		if ($this->debug) 
		{
			echo "<br>TOP OF DISPLAY_USER_DATA<Br>\n";
			ECHO "<BR>USER ID - ".$user_id;
		}
		$this->sql_query = "SELECT * FROM ".$this->site_configuration_table;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		else
		{
			$this->configuration_data = $result->FetchRow();
		}

		if ($user_id)
		{
			$user_data = $this->get_user_data($db, $user_id);
			if ($user_data)
			{
				$this->title = "Users / User Groups > List Users > Userdata Display";
				//display this users information
				$this->body .= "
					<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>
						<tr class=row_color1>
							<td align=right class=medium_font>
								<b>user id: </b>
							</td>
							<td class=medium_font>
								".$user_data["id"]."
							</td>
						</tr>
						<tr class=row_color2>
							<td align=right class=medium_font>
								<b>username: </b>
							</td>
							<td class=medium_font>
								".$user_data["username"]."
							</td>
						</tr>
						<tr class=row_color1>
							<td align=right class=medium_font>
								<b>name: </b>
							</td>
							<td class=medium_font>
								".$user_data["firstname"]." ".$user_data["lastname"]."
							</td>
						</tr>
						<tr class=row_color2>
							<td align=right class=medium_font>
								<b>email: </b>
							</td>
							<td class=medium_font>
								".$user_data["email"]."
							</td>
						</tr>";

				if (strlen(trim($user_data["company_name"])) > 0)
				{
					$this->body .= "
						<tr class=row_color2>
							<td align=right class=medium_font>
								<b>company name: </b>
							</td>
							<td class=medium_font>
								".$user_data["company_name"]."
							</td>
						</tr>";
				}

				$this->body .= "
						<tr class=row_color1>
							<td align=right class=medium_font>
								<b>business type: </b>
							</td>
							<td class=medium_font>
								".(($user_data["business_type"] == 1) ? "individual" : "business")."
							</td>
						</tr>
						<tr class=row_color2>
							<td align=right valign=top class=medium_font>
								<b>address: </b>
							</td>
							<td class=medium_font>
								".((strlen(trim($user_data["address"])) > 0) ? $user_data["address"] : "").
								((strlen(trim($user_data["address_2"])) > 0) ? "&nbsp;(address 1)<BR>".$user_data["address_2"]."&nbsp;(address 2)": "").
								((strlen(trim($user_data["city"])) > 0) ? "<BR>".$user_data["city"] : "").
								((strlen(trim($user_data["state"])) > 0) ? ", ".$user_data["state"] : "").
								((strlen(trim($user_data["zip"])) > 0) ? " ".$user_data["zip"] : "").
								((strlen(trim($user_data["country"])) > 0) ? "<br>".$user_data["country"] : "")."
							</td>
						</tr>
						<tr class=row_color1>
							<td align=right valign=top class=medium_font>
								<b>phone contacts: </b>
							</td>
							<td class=medium_font>
								".$user_data["phone"].((strlen(trim($user_data["phone_2"])) > 0) ? "<br>".$user_data["phone_2"] : "").
								((strlen(trim($user_data["fax"])) > 0) ? "<br>fax:".$user_data["fax"] : "")."
							</td>
						</tr>";

				if (strlen(trim($user_data["url"])) > 0)
				{
					$this->body .= "
						<tr class=row_color1>
							<td align=right class=medium_font>
								<b>url: </b>
							</td>
							<td class=medium_font>
								".$user_data["url"]."
							</td>
						</tr>";
				}

				$sql_query = "select * from ".$this->registration_configuration_table;
				if ($this->debug) echo $sql_query."<br>\n";
				$result = $db->Execute($sql_query);
				if (!$result)
				{
					if ($this->debug) echo $sql_query."<br>\n";
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$registration_configuration = $result->FetchRow();
					$this->row_count = 1;
					for($i = 1; $i < 11; $i++)
					{
						if ($this->debug)
							echo $registration_configuration["registration_optional_".$i."_filter_association"]." is registration_optional_".$i."_filter_association<bR>\n";
						if (strlen(trim($user_data["optional_field_".$i])) > 0)
						{
							$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=right valign=top class=medium_font>
									<b>".$registration_configuration['registration_optional_'.$i.'_field_name'].": </b>
								</td>
								<td class=medium_font>";
							if ($registration_configuration["registration_optional_".$i."_filter_association"])
							{
								$this->sql_query = "select filter_name from ".$this->classified_filters_table." where filter_id = ".$user_data["optional_field_".$i];
								$result = $db->Execute($this->sql_query);
								if(!$result)
									return false;
								else
									$optional_field = $result->FetchRow();

								$this->body .= $optional_field["filter_name"];
								if ($this->debug)
									echo $user_data["optional_field_".$i]." is optional_field_".$i."<br>\n";
							}
							else 
							{
								$this->body .= $user_data["optional_field_".$i];
							}
							$this->body .= "
								</td>
							</tr>";
						}
						$this->row_count++;
					}
				}
				$this->body .= "
					<tr class=row_color2>
						<td align=right class=medium_font>
							<b>date registered: </b>
						</td>
						<td class=medium_font>
							".(($user_data["date_joined"] != 0) ? date("M d,Y G:i - l",$user_data["date_joined"]) : "not available")."
						</td>
					</tr>";

				$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$user_id;
				//$this->body .= $this->sql_query."<br>\n";
				$user_group_result = $db->Execute($this->sql_query);
				if (!$user_group_result)
				{
					return false;
				}
				elseif ($user_group_result->RecordCount() == 1)
				{
					$show_user_stuff = $user_group_result->FetchRow();
					$group_stuff = $this->get_group($db,$show_user_stuff["group_id"]);
					if ($group_stuff)
					{
						//current group
						$this->body .= "
						<tr class=row_color1>
							<td align=right class=medium_font>
								<b>group attached to: </b>
							</td>
							<td>
								<a href=index.php?a=36>
									<span class=medium_font>".$group_stuff["name"]."</span>
								</a>
							</td>
						</tr>";

						if ($group_stuff["affiliate"])
						{
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
								$this->body .= "
								<tr class=row_color1>
									<td align=right class=medium_font>
										<b>affiliate url link: </b>
									</td>
									<td>
										<a href=".$show_configuration["affiliate_url"]."?aff=".$user_id.">
											<span class=medium_font>".$show_configuration["affiliate_url"]."?aff=".$user_id."</span>
										</a>
									</td>
								</tr>";
							}
						}
					}

					//change expiration or credits
					if ($this->is_class_auctions() || $this->is_auctions())
						$auction_price_plan = $this->get_price_plan($db,$show_user_stuff["auction_price_plan_id"]);
					if ($this->is_class_auctions() || $this->is_classifieds())
						$classified_price_plan = $this->get_price_plan($db,$show_user_stuff["price_plan_id"]);
					if ($auction_price_plan || $classified_price_plan)
					{
						//current price plan
						if ($this->is_class_auctions() || $this->is_auctions())
							$this->body .= "
								<tr class=row_color1>
									<td align=right class=medium_font><b>auction</b> price plan attached to:</td>
									<td><a href=index.php?a=37&b=3&g=".$show_user_stuff["auction_price_plan_id"]."><span class=medium_font>".$auction_price_plan["name"]."</a></td>
								</tr>";
						if ($this->is_class_auctions() || $this->is_classifieds())
							$this->body .= "
								<tr class=row_color1>
									<td align=right class=medium_font><b>classified</b> price plan attached to:</td>
									<td><a href=index.php?a=37&b=3&g=".$show_user_stuff["price_plan_id"]."><span class=medium_font>".$classified_price_plan["name"]."</a></td>
								</tr>";
						if ($auction_price_plan["type_of_billing"]==1 || $classified_price_plan["type_of_billing"]==1)
						{
							//charged per listing -- check for credits
							$this->body .= "
										<tr class=row_color1>
											<td align=right class=medium_font><b>credits: </b></td>";
							$this->body .= "<td class=medium_font>";
							$this->sql_query = "select * from ".$this->user_credits_table." where user_id = ".$user_id;
							$get_credits_result = $db->Execute($this->sql_query);
							if ($debug) echo $sql_query." is the query<br>\n";
							if (!$get_credits_result)
							{
								return false;
							}
							elseif ($get_credits_result->RecordCount() > 0)
							{
								while ($show_credits = $get_credits_result->FetchRow())
								{
									$this->body .= $show_credits["credit_count"]." expire(s) on ".date("M d, Y H:i:s", $show_credits["credits_expire"]).
									" <a href=index.php?a=47&z=1&b=".$user_id."&c=".$show_credits["credits_id"].">delete</a><br>";
								}
							}
							else
								$this->body .= "0";
							$this->body .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href=index.php?a=47&z=2&b=".$user_id.">add credits</a><br>";
							$this->body .= "</td>\n</tr>\n";
						}
						elseif ($auction_price_plan["type_of_billing"]==2 || $classified_price_plan["type_of_billing"]==2)
						{
							//charge by subscription -- display when expire
							$this->body .= "<tr class=row_color1>\n\t<td align=right class=medium_font>subscription expires:</td>\n\t";
							$this->body .= "<td class=medium_font>\n\t";
							$this->sql_query = "select * from ".$this->classified_user_subscriptions_table." where user_id = ".$user_id;
							//echo $this->sql_query."<br>\n";
							$get_subscription_result = $db->Execute($this->sql_query);
							if ($debug) echo $sql_query." is the query<br>\n";
							if (!$get_subscription_result)
							{
								return false;
							}
							elseif ($get_subscription_result->RecordCount() == 1)
							{
								$show_subscription = $get_subscription_result->FetchRow();
								$this->body .= "expires on ".date("M d, Y H:i:s", $show_subscription["subscription_expire"]);
								$this->body .= " - - <a href=index.php?a=47&z=4&b=".$user_id.">delete subscription</a>";
							}
							else
								$this->body .= "expired";
							$this->body .= "<br><a href=index.php?a=47&z=3&b=".$user_id."&c=".$show_subscription["subscription_id"].">change expiration</a>";
							$this->body .= "</td></tr>\n";
						}
					}



					//check the use of account balance
					if ($this->configuration_data["use_account_balance"])
					{
						if ($this->configuration_data["positive_balances_only"])
						{
							$this->body .= "<tr class=row_color1>\n\t<td align=right class=medium_font>account balance: \n\t</td>\n\t";
							$this->body .= "<td>\n\t <span class=medium_font>".sprintf("%01.2f",$user_data["account_balance"])." <a href=index.php?a=76&b=".$user_id." >edit balance</a></span></td></tr>";
						}
						else
						{
							$this->body .= "<tr class=row_color1>\n\t<td align=center colspan=2><a href=index.php?a=78&b=".$user_id."&z=3><span class=medium_font><b>create invoice for this user</b></span></a>\n\t</td>\n\t</tr>";

							//present invoices unpaid
							//link to show ads and balance transactions attached
							//link to set invoice has been paid
							//transactions from date (earliest transaction in grouped transactions)
							//transactions to date (latest transaction is grouped transactions)
							//number of transactions within invoice
							//total of invoice
							$this->show_unpaid_invoices($db,$user_id);

							//past invoices paid
							//link to show ads and balance transaction attached
							//link to move paid invoice back to upaid invoice
							//transactions from date (earliest transaction in grouped transactions)
							//transactions to date (latest transaction is grouped transactions)
							//number of transactions within invoice
							//total of invoice
							$this->show_paid_invoices($db,$user_id);
						}
					}
				}

				$this->sql_query = "select classifieds_url from ".$this->site_configuration_table;
				//echo $this->sql_query."<br>";
				$site_result = $db->Execute($this->sql_query);
				if (!$site_result)
				{
					$this->debug_message = "no user data returned";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				elseif ($site_result->RecordCount() == 1)
				{
					$site_configuration = $site_result->FetchRow();
				}

				if($this->is_class_auctions() || $this->is_classifieds())
				{
					// Current Classifieds
					$this->sql_query = "select * from ".$this->classifieds_table." where seller = ".$user_id." and live = 1 and item_type = 1 order by date desc";
					//echo $this->sql_query."<br>";
					$current_result = $db->Execute($this->sql_query);
					if (!$current_result)
					{
						$this->debug_message = "no user data returned";
						$this->error_message = $this->internal_error_message;
						return false;
					}
					elseif ($current_result->RecordCount() > 0)
					{
						$this->display_current_item($current_result, "<b>current classifieds</b>");
					}
				}

				if($this->is_class_auctions() || $this->is_auctions())
				{
					// Current Auctions
					$this->sql_query = "select * from ".$this->classifieds_table." where seller = ".$user_id." and live = 1 and item_type = 2 order by date desc";
					//echo $this->sql_query."<br>";
					$current_result = $db->Execute($this->sql_query);
					if (!$current_result)
					{
						$this->debug_message = "no user data returned";
						$this->error_message = $this->internal_error_message;
						return false;
					}
					elseif ($current_result->RecordCount() > 0)
					{
						$this->display_current_item($current_result, "<b>current auctions</b>");
					}
				}

				if($this->is_class_auctions() || $this->is_classifieds())
				{
					// Expired ads
					$this->sql_query = "select * from ".$this->classifieds_table." where seller = ".$user_id." and live = 0 and ends < ".$this->shifted_time()." and item_type = 1 order by date desc";
					//echo $this->sql_query."<br>";
					$current_result = $db->Execute($this->sql_query);
					if (!$current_result)
					{
						$this->debug_message = "no user data returned";
						$this->error_message = $this->internal_error_message;
						return false;
					}
					elseif ($current_result->RecordCount() > 0)
					{
						$this->display_expired_item($current_result, "<b>classifieds recently expired</b>");
					}
				}

				if($this->is_class_auctions() || $this->is_auctions())
				{
					// Expired auctions
					$this->sql_query = "select * from ".$this->classifieds_table." where seller = ".$user_id." and live = 0 and ends < ".$this->shifted_time()." and item_type = 2 order by date desc";
					//echo $this->sql_query."<br>";
					$current_result = $db->Execute($this->sql_query);
					if (!$current_result)
					{
						$this->debug_message = "no user data returned";
						$this->error_message = $this->internal_error_message;
						return false;
					}
					elseif ($current_result->RecordCount() > 0)
					{
						$this->display_expired_item($current_result, "<b>auctions recently expired</b>");
					}
				}

				if($this->is_class_auctions() || $this->is_auctions())
				{
					// Display feedbacks
					$this->sql_query = "select * from ".$this->feedbacks_table.", ".$this->userdata_table." where rated_user_id = ".$user_id." and done = 1 and id = rater_user_id order by date desc";
					$current_result = $db->Execute($this->sql_query);
					if($this->configuration_data['debug_admin'])
					{
						$this->debug_display($db, $this->filename, $this->function_name, "auctions_feedbacks_table", "get feedback data");
					}
					if (!$current_result)
					{
						$this->debug_message = "no user data returned";
						$this->error_message = $this->internal_error_message;
						return false;
					}
					elseif ($current_result->RecordCount() > 0)
					{
						$this->body .= "
							<tr>
								<td colspan=2>
									<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>
										<tr  class=row_color_black>
											<td valign=top width=100% colspan=7 class=large_font_light>\n\tFeedbacks rating this user\n\t\t</td>\n\t</tr>\n\t";
						$this->body .= "<tr  class=row_color_black>\n\t\t<td class=small_font_light>\n\tID-Rater Name\n\t\t</td>\n\t\t";
						$this->body .= "<td class=small_font_light>\n\tDate\n\t\t</td>\n\t\t";
						$this->body .= "<td class=small_font_light>\n\tFeedback\n\t\t</td>\n\t\t";
						$this->body .= "<td class=small_font_light>\n\tRate\n\t\t</td>\n\t\t";
						$this->body .= "<td class=small_font_light>\n\tEdit Feedback\n\t\t</td>\n\t\t";
						$this->body .= "<td class=small_font_light>\n\tDelete Feedback\n\t\t</td>\n\t\t";
						$this->body .= "</tr>\n\t";

						$this->row_count = 0;
						while ($show = $current_result->FetchNextObject())
						{
							$this->body .= "<tr class=".$this->get_row_color().">\n\t\t";
							$this->body .= "<td>\n\t\t<a href=index.php?a=1017&b=".$show->RATER_USER_ID." class=small_font>\n\t".$show->RATER_USER_ID
								." - ".urldecode($show->USERNAME)."</a>\n\t\t</td>\n\t\t";
							$this->body .= "<td class=small_font>\n\t".date("M j, Y",$show->DATE)."\n\t\t</td>\n\t\t";
							$this->body .= "<td class=small_font>\n\t".stripslashes(urldecode($show->FEEDBACK))."</font>\n\t\t</td>\n\t\t";
							$this->body .= "<td class=small_font>\n\t".urldecode($show->RATE)."</font>\n\t\t</td>\n\t\t";
							$this->body .= "<td align=center>\n\t\t<a href=index.php?a=1106&b=".$show->AUCTION_ID."&c=".$show->RATER_USER_ID."><img src=admin_images/btn_admin_edit.gif border=0 alt=edit>\n\t\t</td>\n\t";
							$this->body .= "<td align=center>\n\t\t<a href=index.php?a=1106&b=".$show->AUCTION_ID."&c=".$show->RATER_USER_ID."&e=".$show->RATED_USER_ID."><img src=admin_images/btn_admin_delete.gif border=0 alt=edit>\n\t\t</td>\n\t";
							$this->body .= "</tr>\n\t";

							//renew/upgrade
							$this->row_count = !$this->row_count;
						}// end of while

						$this->body .= "</td>\n\t</tr>\n\t";
						$this->body .= "</table>\n\t";
						$this->body .= "</td></tr></table>";
					}

					// Display current bids
					$this->sql_query = "select * from ".$this->bid_table.", ".$this->auctions_table." where bidder = ".$user_id." and auction_id = id and ends > ".$this->shifted_time();
					$current_result = $db->Execute($this->sql_query);
					if($this->configuration_data['debug_admin'])
					{
						$this->debug_display($db, $this->filename, $this->function_name, "bid_table", "get bid data");
					}
					if (!$current_result)
					{
						$this->debug_message = "no bids returned";
						$this->error_message = $this->internal_error_message;
						return false;
					}
					elseif ($current_result->RecordCount() > 0)
					{
						$this->body .= "<tr><td colspan=2>";
						$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n\t";
						$this->body .= "<tr bgcolor=000066><td valign=top width=100% colspan=7 class=medium_font_light>\n\t<b>Current bids by this User</b>\n\t\t</td>\n\t</tr>\n\t";
						$this->body .= "<tr class=row_color_black>\n\t\t<td class=medium_font_light>\n\t<b>Auction ID - Title</b>\n\t\t</td>\n\t\t";
						$this->body .= "<td class=medium_font_light>\n\t<b>Date</b>\n\t\t</td>\n\t\t";
						$this->body .= "<td class=medium_font_light>\n\t<b>Bid</b>\n\t\t</td>\n\t\t";
						$this->body .= "<td class=medium_font_light align=center>\n\t<b>Quantity</b>\n\t\t</td>\n\t\t";
						$this->body .= "<td class=medium_font_light>\n\t&nbsp;\n\t\t</td>\n\t\t";
						$this->body .= "</tr>\n\t";

						$this->row_count = 0;
						while ($show = $current_result->FetchNextObject())
						{
							$this->body .= "<tr class=".$this->get_row_color().">\n\t\t";
							$this->body .= "<td>\n\t\t<a href=index.php?a=53&b=".$show->AUCTION_ID." class=small_font>\n\t".stripslashes(urldecode($show->AUCTION_ID))." - ".stripslashes(urldecode($show->TITLE))."</a>\n\t\t</td>\n\t\t";
							$this->body .= "<td class=small_font>\n\t".date("M j, Y",$show->TIME_OF_BID)."\n\t\t</td>\n\t\t";
							$this->body .= "<td class=small_font>\n\t".urldecode($show->BID)."</font>\n\t\t</td>\n\t\t";
							$this->body .= "<td class=small_font align=center>\n\t".urldecode($show->QUANTITY)."</font>\n\t\t</td>\n\t\t";
							$this->body .= "<td align=center>\n\t\t<a href=index.php?a=107&b=".$show->AUCTION_ID."&c=".$show->TIME_OF_BID."><img src=admin_images/btn_admin_edit.gif border=0 alt=edit>\n\t\t</td>\n\t";
							$this->body .= "</tr>\n\t";

							//renew/upgrade
							$this->row_count = !$this->row_count;
						}// end of while

						$this->body .= "</td>\n\t</tr>\n\t";
						$this->body .= "</table>\n\t";
						$this->body .= "</td></tr>";
					}
				}

				$this->body .= "
					<tr>
						<td colspan=2>
							<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>
								<tr class=row_color2>
									<td align=center colspan=2>
										<a href=index.php?a=18&b=".$user_data["id"].">
											<span class=medium_font><b>edit this users data</b></span>
										</a>
									</td>
								</tr>
								<tr class=row_color2>
									<td align=center colspan=2>
										<a href=index.php?a=41&b=".$user_data["id"].">
											<span class=medium_font><b>view users transactions</b></span>
										</a>
									</td>
								</tr>
							</table>
						</td>
					</tr></table>";
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			//no user id
			return false;
		}
	} //end of function display_user_data

//########################################################################

	function display_current_item($result, $title)
	{
		$this->body .= "
			<tr>
				<td colspan=100%>
					<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>
						<tr bgcolor=000066>
							<td valign=top width=100% colspan=8 class=medium_font_light align=center>
								<b>".$title."</b>
							</td>
						</tr>
						<tr class=row_color_black>
							<td class=medium_font_light>
								<b>id - title</b>
							</td>
							<td class=medium_font_light>
								<b>started</b>
							</td>
							<td class=medium_font_light>
								<b>ends</b>
							</td>
							<td class=medium_font_light align=center>
								<b>forwarded</b>
							</td>
							<td class=medium_font_light align=center>
								<b>responded</b>
							</td>
							<td class=medium_font_light align=center>
								<b>viewed</b>
							</td>
							<td class=medium_font_light align=center>
								<b>upgrade/extend</b>
							</td>
							<td class=medium_font_light align=center>
								<b>image(s)</b>
							</td>
						</tr>";

		$this->row_count = 0;
		while ($show = $result ->FetchRow())
		{
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td>
								<a href=index.php?a=53&b=".$show["id"].">
									<span class=small_font>".$show["id"]." - ".urldecode($show["title"])."</span>
								</a>
							</td>
							<td class=small_font>
								".date("M j, Y",$show["date"])."
							</td>
							<td class=small_font>
								".date("M j, Y",$show["ends"])."
							</td>
							<td class=small_font align=center>
								".$show["forwarded"]."
							</td>
							<td class=small_font align=center>
								".$show["responded"]."
							</td>
							<td class=small_font align=center>
								".$show["viewed"]."
							</td>
							<td align=center>
								<a href=index.php?a=54&b=".$show["id"].">
									<span class=small_font>change</span>
								</a>
							</td>
							<td class=small_font align=center>
								".$show["image"]." -
								<a href=index.php?a=55&b=".$show["id"]."&c=".$show["seller"].">
									<span class=small_font>increase</span>
								</a>
							</td>
						</tr>";$this->row_count++;
		}// end of while
		$this->body .= "
					</table>
				</td>
			</tr>";
	}

//########################################################################

	function display_expired_item($result, $title)
	{
		$this->body .= "
			<tr>
				<td colspan=100%>
					<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>
						<tr bgcolor=000066>
							<td valign=top width=100% colspan=7 class=medium_font_light align=center>
								".$title."
							</td>
						</tr>
						<tr class=row_color_black>
							<td class=medium_font_light>
								<b>id - title</b>
							</td>
							<td class=medium_font_light>
								<b>started</b>
							</td>
							<td class=medium_font_light>
								<b>ends</b>
							</td>
							<td align=center class=medium_font_light>
								<b>forwarded</b>
							</td>
							<td align=center class=medium_font_light>
								<b>responded</b>
							</td>
							<td align=center class=medium_font_light>
								<b>viewed</b>
							</td>
							<td align=center class=medium_font_light>
								<b>restart</b>
							</td>
						</tr>";

		$this->row_count = 0;
		while ($show = $result->FetchRow())
		{
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td>
								<a href=index.php?a=53&b=".$show["id"].">
									<span class=small_font>".$show["id"]." - ".urldecode($show["title"])."</span>
								</a>
							</td>
							<td class=small_font>
								".date("M j, Y",$show["date"])."
							</td>
							<td class=small_font>
								".date("M j, Y",$show["ends"])."
							</td>
							<td align=center class=small_font>
								".$show["forwarded"]."
							</td>
							<td align=center class=small_font>
								".$show["responded"]."
							</td>
							<td align=center class=small_font>
								".$show["viewed"]."
							</td>
							<td align=center>
								<a href=index.php?a=54&b=".$show["id"].">
									<span class=small_font>restart</span>
								</a>
							</td>
						</tr>";$this->row_count++;
		}// end of while
		$this->body .= "
					</table>
				</td>
			</tr>";
	}

//########################################################################

	function get_row_color()
	{
		if (($this->row_count % 2) == 0)
			$row_color = "row_color1";
		else
			$row_color = "row_color2";
		return $row_color;
	} //end of function get_row_color

//########################################################################

	function display_font_type_select($db,$name,$current_value)
	{
		$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 3 order by display_order";
		$result = $db->Execute($this->sql_query);
		if ($this->debug) echo $this->sql_query."<br>\n";
		if (!$result)
		{
			if ($this->debug)
			{
				echo $this->sql_query."<br>\n";
				echo $db->ErrorMsg()." is the error<BR>\n";
			}
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->body .= "<select name=".$name.">\n\t\t";
			while ($show_style = $result->FetchNextObject())
			{
				$this->body .= "<option ";
				if ($show_style->VALUE == $current_value)
					$this->body .= "selected";
				$this->body .= ">".$show_style->VALUE."</option>\n\t\t";
			} //end of while
			$this->body .= "</select>\n\t";
		}
		else
		{
			$this->error_message = $this->data_error_message;
			return false;
		}

	} //end of function display_font_type_select

//########################################################################


	function display_font_style_select($db,$name,$current_value)
	{
		$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 5 order by display_order";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->body .= "<select name=".$name.">\n\t\t";
			while ($show_style = $result->FetchRow())
			{
				$this->body .= "<option ";
				if ($show_style["value"] == $current_value)
					$this->body .= "selected";
				$this->body .= ">".$show_style["value"]."</option>\n\t\t";
			} //end of while
			$this->body .= "</select>\n\t";
		}
		else
		{
			$this->error_message = $this->data_error_message;
			return false;
		}


	} //end of function display_font_style_select

//########################################################################

	function display_font_size_select($db,$name,$current_value)
	{
		$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 4 order by display_value";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->body .= "<select name=".$name.">\n\t\t";
			while ($show_size = $result->FetchRow())
			{
				$this->body .= "<option ";
				if ($show_size["value"] == $current_value)
					$this->body .= "selected";
				$this->body .= ">".$show_size["value"]."</option>\n\t\t";
			} //end of while
			$this->body .= "</select>\n\t";
		}
		else
		{
			$this->error_message = $this->data_error_message;
			return false;
		}

	} //end of function display_font_size_select

//########################################################################

	function display_font_weight_select($db,$name,$current_value)
	{
		$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 6 order by display_order";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->body .= "<select name=".$name.">";
			while ($show_weight = $result->FetchRow())
			{
				$this->body .= "<option ";
				if ($show_weight["value"] == $current_value)
					$this->body .= "selected";
				$this->body .= ">".$show_weight["value"]."</option>";
			} //end of while
			$this->body .= "</select>";
		}
		else
		{
			$this->error_message = $this->data_error_message;
			return false;
		}

	} //end of function display_font_weight_select

//########################################################################

	function display_font_decoration_select($db,$name,$current_value)
	{
		$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 7 order by display_order";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->body .= "<select name=".$name.">\n\t\t";
			$this->body .= "<option></option>\n\t\t";
			while ($show_decoration = $result->FetchRow())
			{
				$this->body .= "<option ";
				if ($show_decoration["value"] == $current_value)
					$this->body .= "selected";
				$this->body .= ">".$show_decoration["value"]."</option>\n\t\t";
			} //end of while
			$this->body .= "</select>\n\t";
		}
		else
		{
			$this->error_message = $this->data_error_message;
			return false;
		}

	} //end of function display_font_decoration_select

//########################################################################

	function display_text_align_select($db,$name,$current_value)
	{
		$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 8 order by display_order";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->body .= "<select name=".$name.">\n\t\t";
			$this->body .= "<option></option>\n\t\t";
			while ($show_text_align = $result->FetchRow())
			{
				$this->body .= "<option ";
				if ($show_text_align["value"] == $current_value)
					$this->body .= "selected";
				$this->body .= ">".$show_text_align["value"]."</option>\n\t\t";
			} //end of while
			$this->body .= "</select>\n\t";
		}
		else
		{
			$this->error_message = $this->data_error_message;
			return false;
		}

	} //end of function display_text_align_select

//########################################################################

	function display_text_vertical_align_select($db,$name,$current_value)
	{
		$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 20 order by display_order,display_value";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->body .= "<select name=".$name.">\n\t\t";
			$this->body .= "<option></option>\n\t\t";
			while ($show_text_align = $result->FetchRow())
			{
				$this->body .= "<option ";
				if ($show_text_align["value"] == $current_value)
					$this->body .= "selected";
				$this->body .= ">".$show_text_align["value"]."</option>\n\t\t";
			} //end of while
			$this->body .= "</select>\n\t";
		}
		else
		{
			$this->error_message = $this->data_error_message;
			return false;
		}

	} //end of function display_text_align_select

//########################################################################

	function get_sql_in_statement($db,$category_id)
	{
		if ($category_id)
		{
			$this->sql_query = "SELECT in_statement FROM ".$this->classified_categories_table." WHERE category_id = ".$category_id;
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_in_statement = $result->FetchRow();
				$current_in_statement = $show_in_statement["in_statement"];
				if (strlen(trim($current_in_statement)) > 0)
				{
					$this->in_statement = $current_in_statement;
					return $current_in_statement;
				}
				else
				{
					$this->get_sql_in_array($db,$category_id);
					if (count($this->subcategory_array) > 0)
					{
						$this->in_statement .= "in (";
						while (list($key,$value) = each($this->subcategory_array))
						{
							if ($key == 0)
								$this->in_statement .= $value;
							else
								$this->in_statement .= ",".$value;
						}
						$this->in_statement .= ")";
						$this->sql_query = "update ".$this->classified_categories_table." set
							in_statement =\"".$this->in_statement."\"
							WHERE category_id = ".$category_id;
						//echo $this->sql_query."<br>\n";
						$result = $db->Execute($this->sql_query);
						if (!$result)
						{
							return false;
						}
						return true;
					}
					else
					{
						return false;
					}
				}
			}
			else
			{
				$this->get_sql_in_array($db,$category_id);
				if (count($this->subcategory_array) > 0)
				{
					$this->in_statement .= "in (";
					while (list($key,$value) = each($this->subcategory_array))
					{
						if ($key == 0)
							$this->in_statement .= $value;
						else
							$this->in_statement .= ",".$value;
					}
					$this->in_statement .= ")";
					return $this->in_statement;
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			//category_id is missing
			return false;
		}

	} //end of get_sql_in_statement

//####################################################################################

	function get_sql_in_array($db,$category_id)
	{
		if ($category_id)
		{
			//get the count for this category
			$count = 0;

			$this->sql_query = "select category_id from ".$this->classified_categories_table." where parent_id = ".$category_id;
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[2524];
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				while ($show_category = $result->FetchRow())
				{
					$this->get_sql_in_array($db,$show_category["category_id"]);
				}
			}

			array_push ($this->subcategory_array, $category_id);

			return true;
		}
		else
		{
			//category_id is missing
			return false;
		}

	} //end of get_sql_in_array

//##################################################################################

	function get_language_name($db,$language_id=0)
	{
		if ($language_id)
		{
			$this->sql_query = "select language from ".$this->pages_languages_table." where language_id = ".$language_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
				return $show["language"];
			}
			else
			{
				return "no name";
			}
		}
		else
		{
			return "no name";
		}
	} //end of function get_language_name

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_group_name($db,$group_id=0)
	{
		if ($group_id)
		{
			$this->sql_query = "select name from ".$this->classified_groups_table." where group_id = ".$group_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
				return $show["name"];
			}
			else
			{
				return "no name";
			}
		}
		else
		{
			return "no name";
		}
	} //end of function get_group_name

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_group($db,$group_id=0)
	{
		if ($group_id)
		{
			$this->sql_query = "select * from ".$this->classified_groups_table." where group_id = ".$group_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
				return $show;
			}
			else
			{
				return "no name";
			}
		}
		else
		{
			return "no name";
		}
	} //end of function get_group

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_date_select($year_name,$month_name,$day_name,$year=0,$month=0,$day=0)
	{
		$date = "";

		$time = time() + $this->time_shift;
		if (!$year)
			$year = date("Y",$time);
		if (!$month)
			$month = date("n",$time);
		if (!$day)
			$day = date("j",$time);

		$date .= "Month <select name=".$month_name.">\n\t\t";
		for ($i=1;$i<13;$i++)
		{
			$date .= "<option";
			if ($month == $i)
				$date .= " selected";
			$date .= ">".$i."</option>\n\t\t";
		}
		$date .= "</select>\n\t\t";

		$date .= "Day <select name=".$day_name.">\n\t\t";
		for ($i=1;$i<32;$i++)
		{
			$date .= "<option";
			if ($day == $i)
				$date .= " selected";
			$date .= ">".$i."</option>\n\t\t";
		}
		$date .= "</select>\n\t\t";
		$date .= "Year <select name=".$year_name.">\n\t\t";
		for ($i=$year;$i<=(5+$year);$i++)
		{
			$date .= "<option";
			if ($year == $i)
				$date .= " selected";
			$date .= ">".$i."</option>\n\t\t";
		}
		$date .= "</select>\n\t\t";

		return $date;
	} //end of function get_date_select

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_fine_date_select($year_name,$month_name,$day_name,$hour_name,$minute_name,
		$year_value=0,$month_value=0,$day_value=0,$hour_value=0,$minute_value=0)
	{
		$time = time() + $this->time_shift;
		if (!$year_value)
			$year_value = date("Y",$time);
		if (!$month_value)
			$month_value = date("n",$time);
		if (!$day_value)
			$day_value = date("j",$time);
		if (!$hour_value)
			$hour_value = date("G",$time);
		if (!$minute_value)
			$minute_value = date("i",$time);

		$this->body .= "minute <select name=".$minute_name.">\n\t\t";
		for ($i=0;$i<=59;$i++)
		{
			$this->body .= "<option";
			if ($minute_value == $i)
				$this->body .= " selected";
			$this->body .= ">".sprintf("%02d",$i)."</option>\n\t\t";
		}
		$this->body .= "</select>\n\t\t";

		$this->body .= "hour <select name=".$hour_name.">\n\t\t";
		for ($i=0;$i<=23;$i++)
		{
			$this->body .= "<option";
			if ($hour_value == $i)
				$this->body .= " selected";
			$this->body .= ">".sprintf("%02d",$i)."</option>\n\t\t";
		}
		$this->body .= "</select>\n\t\t";

		$this->body .= "day <select name=".$day_name.">\n\t\t";
		for ($i=1;$i<32;$i++)
		{
			$this->body .= "<option";
			if ($day_value == $i)
				$this->body .= " selected";
			$this->body .= ">".$i."</option>\n\t\t";
		}
		$this->body .= "</select>\n\t\t";

		$this->body .= "month <select name=".$month_name.">\n\t\t";
		for ($i=1;$i<13;$i++)
		{
			$this->body .= "<option";
			if ($month_value == $i)
				$this->body .= " selected";
			$this->body .= ">".$i."</option>\n\t\t";
		}
		$this->body .= "</select>\n\t\t";

		$this->body .= "year <select name=".$year_name.">\n\t\t";
		for ($i=($year_value-2);$i<=($year_value+2);$i++)
		{
			$this->body .= "<option";
			if ($year_value == $i)
				$this->body .= " selected";
			$this->body .= ">".$i."</option>\n\t\t";
		}
		$this->body .= "</select>\n\t\t";

	} //end of function get_date_select

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_basic_classified_data($db,$classified_id=0,$display=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"HTTPS Required\", \"To view the entire credit card number, you must view this page using HTTPS.  This is for the protection of the user's credit card information.  Click the \\\"View Full Credit Card Number\\\" link to the right to view the full credit card number.\"]\n";
		//".$this->show_tooltip(1,1)."
		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";
		if ($classified_id)
		{
			$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$classified_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				if(!$this->configuration_data)
					$this->get_configuration_data($db);

				$show = $result->FetchRow();
				$user = $this->get_user_data($db,$show["seller"]);
				$category_name = $this->get_category_name($db,$show["category"]);
				$category_tree = $this->get_category_tree($db,$show["category"]);
				reset ($this->category_tree_array);
				if ($category_tree)
				{
					//category tree
					$current_category_tree .= " Main ";
					if (is_array($this->category_tree_array))
					{
						$i = 0;
						//$categories = array_reverse($this->category_tree_array);
						$i = count($this->category_tree_array);
						while ($i > 0 )
						{
							//display all the categories
							$i--;
							$current_category_tree .=  " > ".$this->category_tree_array[$i]["category_name"];
						}
					}
					else
					{
						$current_category_tree .=$category_tree;
					}
				}

				$this->row_count = 0;

				if (!$this->admin_demo())$ad = "<form action=index.php?a=25&x=1 method=post>\n";
				$ad .= "<table cellpadding=2 cellspacing=1 border=0 class=row_color2 width=100%>\n";
				$ad .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light><b>listing specifics</b> </td></tr>\n\t";
				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=35% class=medium_font>\n\t<b>listing type:</b></font></td>\n\t";
				if($show['item_type'] == 2)
				{
					//type of auction
					if ($show['auction_type'] == 1)
					{
						$ad .= "<td colspan=2 class=medium_font>\n\tstandard auction</font></td>\n</tr>\n";
					}
					elseif ($show['auction_type'] == 2)
					{
						$ad .= "<td colspan=2 class=medium_font>\n\tdutch auction</font></td>\n</tr>\n";
					}
				}
				elseif($show['item_type'] == 1)
				{
					$ad .= "<td colspan=2 class=medium_font>\n\tClassified ad</font></td>\n</tr>\n";
				}
				$this->row_count++;

				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>category: </b></td>\n\t";
				$ad .= "<td class=medium_font>".$current_category_tree." </td>\n</tr>\n";
				$this->row_count++;
				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>title: </b></td>\n\t";
				$ad .= "<td class=medium_font>\n\t".stripslashes(urldecode($show["title"]))." </td>\n</tr>\n";
				$this->row_count++;
				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=35% class=medium_font>\n\t<b>listing id: </b></font></td>\n\t";
				$ad .= "<td colspan=2 class=medium_font>\n\t".stripslashes(urldecode($show["id"]))."</font></td>\n</tr>\n";
				$this->row_count++;
				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>seller: </td>\n\t";
				$ad .= "<td>"."<a href=index.php?a=17&b=".$show["seller"]."><span class=medium_font>".$user["username"]." ( ".$show["email"]." )</span></a>";
				if (!$this->admin_demo()) $ad .= "\n\t<input type=hidden name=b[".$show["seller"]."] value=\"".$user["username"]."\"><input type=submit value=contact></td>\n</tr>\n";
				$this->row_count++;
				if (strlen(trim($show["phone"])) > 0 || strlen(trim($show["phone2"])) > 0 || (strlen(trim($show["fax"])) > 0))
				{
					$ad .= "<tr  class=".$this->get_row_color().">\n\t\n\t<td align=right valign=top class=medium_font>\n\t<b>phone contact: </b>\n\t</td>\n\t";
					//echo $show["phone"]." is user phone<br>\n";
					$ad .= "<td class=medium_font>\n\t".$show["phone"];
					if (strlen(trim($show["phone2"])) > 0)
						$ad .= "<br>".$show["phone2"];
					if (strlen(trim($show["fax"])) > 0)
						$ad .= "<br>fax:".$show["fax"];
					$ad .= "</font>\n\t</td>\n</tr>\n";
					$this->row_count++;
				}
				if (strlen(trim($show["mapping_address"])) > 0 || strlen(trim($show["mapping_zip"])) > 0
					|| strlen(trim($show["mapping_state"])) > 0 || strlen(trim($show["mapping_city"])) > 0
					|| strlen(trim($show["mapping_country"])) > 0 )
				{
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=35% class=medium_font>\n\t<b>mapping location: </b></font></td>\n\t";
					$ad .= "<td colspan=2 class=medium_font>\n\t".stripslashes(urldecode($show["mapping_address"]))."<br>".stripslashes(urldecode($show["mapping_city"])).", ".stripslashes(urldecode($show["mapping_state"]));
					$ad .= "\t".stripslashes(urldecode($show["mapping_zip"]))."<br>".stripslashes(urldecode($show["mapping_country"]))."</font></td>\n</tr>\n";
					$this->row_count++;
				}
				if ($show["discount_id"] > 0)
				{
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>discount id: </b></td>\n\t";
					$ad .= "<td colspan=2 class=medium_font>\n\t".$show["discount_id"]."</td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>discount percentage: </b></td>\n\t";
					$ad .= "<td colspan=2 class=medium_font>\n\t".$show["discount_percentage"]." %</td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>discount amount:</td>\n\t";
					$ad .= "<td colspan=2 class=medium_font>\n\t".urldecode($show["precurrency"])." ".$show["discount_amount"]." ".urldecode($show["postcurrency"])."</td>\n</tr>\n";
					$this->row_count++;
				}
				if($show['duration'])
				{
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=35% class=medium_font>\n\t<b>duration: </b></font></td>\n\t";
					$ad .= "<td colspan=2 class=medium_font>\n\t".$show["duration"]." day</font></td>\n</tr>\n";
					$this->row_count++;
				}
				if(($show['start_time'] > 0) || ($show['end_time'] > 0))
				{
					if($show['start_time'] > 0)
					{
						$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>listing start date: </b></td>\n\t";
						$ad .= "<td class=medium_font>".date("M d, Y H:i:s", $show["start_time"])." </td>\n</tr>\n";
						$this->row_count++;
					}

					if($show['end_time'] > 0)
					{
						$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>listing end date: </b></td>\n\t";
						$ad .= "<td class=medium_font>".date("M d, Y H:i:s", $show["end_time"])." </td>\n</tr>\n";
						$this->row_count++;
					}
				}
				else
				{
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>date listing entered: </b></td>\n\t";
					$ad .= "<td class=medium_font>".date("M d, Y H:i:s", $show["date"])." </td>\n</tr>\n";
					$this->row_count++;
				}
				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>date listing ends: </b></td>\n\t";
				$ad .= "<td colspan=2 class=medium_font>\n\t";
				if (($show["live"] == 1) || ($show["live"] == 0))
				{
					$ad .= date("M d, Y H:i:s", $show["ends"]);
				}
				elseif ($show["live"] == 2)
				{
					$ad .= date("M d, Y H:i:s", (($show["duration"] * 86400) + time()));
				}
				$ad .= "</td>\n</tr>\n";
				//$ad .= "<td colspan=2 class=medium_font>\n\t".date("M d, Y H:i:s", $show["ends"])."</td>\n</tr>\n";
				$this->row_count++;
				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>location: </b></td>\n\t";
				$ad .= "<td class=medium_font>".$show["location_city"]." ".$show["location_state"]." ".$show["location_zip"]." ".$show["location_country"]." </td>\n</tr>\n";
				$this->row_count++;
				if($show['item_type'] == 1)
				{
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>price: </b></td>";
					$ad .= "<td class=medium_font>\n\t".urldecode($show["precurrency"])." ".urldecode($show["price"])." ".urldecode($show["postcurrency"])." </td>\n</tr>\n";
					$this->row_count++;
				}
				elseif($show['item_type'] == 2)
				{
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>starting price: </b></td>";
					$ad .= "<td colspan=2 class=medium_font>\n\t".urldecode($show["precurrency"])." ".urldecode($show['starting_bid'])." ".urldecode($show["postcurrency"])."</td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>reserve price: </b></td>";
					$ad .= "<td colspan=2 class=medium_font>\n\t".urldecode($show["precurrency"])." ".urldecode($show['reserve_price'])." ".urldecode($show["postcurrency"])."</td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>buy now price: </b></td>";
					$ad .= "<td colspan=2 class=medium_font>\n\t".urldecode($show["precurrency"])." ".urldecode($show['buy_now'])." ".urldecode($show["postcurrency"])."</td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr  class=".$this->get_row_color().">\n\t\n\t<td align=right class=medium_font>\n<b>final price: </b></td>";
					$ad .= "<td colspan=2 class=medium_font>\n\t".urldecode($show["precurrency"])." ".urldecode($show['final_price'])." ".urldecode($show["postcurrency"])."</td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=35% class=medium_font>\n\t<b>quantity: </b></td>\n\t";
					$ad .= "<td colspan=2 class=medium_font>\n\t".stripslashes(urldecode($show['quantity']))."</td>\n</tr>\n";
					$this->row_count++;
				}

				if (strlen(trim($show['payment_options'])) > 0)
				{
					$show['payment_options'] = urldecode($show['payment_options']);
					$payment_options = str_replace("||",", ",$show['payment_options']);
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t<b>payment types accepted:</b></td>\n\t";
					$ad .= "<td colspan=2 class=medium_font>\n\t".$payment_options."</td>\n</tr>\n";
					$this->row_count++;
				}

				// Check for price plan id being default
				if($show['price_plan_id'] == 0)
				{
					$this->sql_query = "select price_plan_id, auction_price_plan_id from ".
						$this->user_groups_price_plans_table." where id = ".$show['seller'];
					$result = $db->Execute($this->sql_query);
					if(!$result)
					{
						return false;
					}

					$price_plan = $result->FetchRow();

					if($show['item_type'] == 1)
					{
						$show['price_plan_id'] = $price_plan['price_plan_id'];
					}
					elseif($show['item_type'] == 2)
					{
						$show['price_plan_id'] = $price_plan['auction_price_plan_id'];
					}
				}

				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=35% class=medium_font>\n\t<b>price plan:</b></font></td>\n\t";
				$price_plan_name = $this->get_price_plan_name($db,$show["price_plan_id"]);
				$ad .= "<td colspan=2 class=medium_font>\n\t".$price_plan_name."</td>\n</tr>\n";
				$this->row_count++;

				//status of ad
				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=35% class=medium_font>\n\t<b>status:</b></font></td>\n\t";
				if (!$show["live"])
				{
					$ad .= "<td colspan=2 class=medium_font>\n\texpired</font></td>\n</tr>\n";
				}
				else
				{
					$ad .= "<td colspan=2 class=medium_font>\n\tlive</font></td>\n</tr>\n";
				}
				$this->row_count++;

				//# of times viewed
				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=35% class=medium_font>\n\t<b>number of times viewed:</b></font></td>\n\t";
				if (!$show["viewed"])
				{
					$ad .= "<td colspan=2 class=medium_font>\n\tzero</font></td>\n</tr>\n";
				}
				else
				{
					$ad .= "<td colspan=2 class=medium_font>\n\t".$show["viewed"]."</font></td>\n</tr>\n";
				}
				$this->row_count++;

				//# of times responded
				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=35% class=medium_font>\n\t<b>number of times responded:</b></font></td>\n\t";
				if (!$show["responded"])
				{
					$ad .= "<td colspan=2 class=medium_font>\n\tzero</font></td>\n</tr>\n";
				}
				else
				{
					$ad .= "<td colspan=2 class=medium_font>\n\t".$show["responded"]."</font></td>\n</tr>\n";
				}
				$this->row_count++;

				//# of times forwarded
				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=35% class=medium_font>\n\t<b>number of times forwarded:</b></font></td>\n\t";
				if (!$show["forwarded"])
				{
					$ad .= "<td colspan=2 class=medium_font>\n\tzero</font></td>\n</tr>\n";
				}
				else
				{
					$ad .= "<td colspan=2 class=medium_font>\n\t".$show["forwarded"]."</font></td>\n</tr>\n";
				}
				$this->row_count++;

				//expiration notice sent or not
				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=35% class=medium_font>\n\t<b>expiration notice sent:</b></font></td>\n\t";
				if (!$show["expiration_notice"])
					$ad .= "<td colspan=2 class=medium_font>\n\tno</font></td>\n</tr>\n";
				else
					$ad .= "<td colspan=2 class=medium_font>\n\tyes</font></td>\n</tr>\n";
				$this->row_count++;

				//ad description
				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>description: </b></td>";
				$ad .= "<td class=medium_font>\n\t".stripslashes(urldecode($show["description"]))." </td>\n</tr>\n";
				$this->row_count++;

				for($i = 1; $i < 21; $i++)
				{
					if (strlen(trim($show["optional_field_".$i])) > 0)
					{
						$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>optional field ".$i.": </b></td>";
						$ad .= "<td class=medium_font>\n\t".stripslashes(urldecode($show["optional_field_".$i]))." </td>\n</tr>\n";
					}
					$this->row_count++;
				}
				$ad .= "</form>\n\t";

				$this->sql_query = "select * from ".$this->classified_extra_table." where classified_id = ".$classified_id." order by display_order";
				$extra_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$extra_result)
				{
					$this->error_message = $this->internal_error_message;
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($extra_result->RecordCount() > 0)
				{
					while ($show_extra = $extra_result->FetchRow())
					{
						$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>".stripslashes(urldecode($show_extra["name"])).":</td>";
						$ad .= "<td class=medium_font>\n\t".stripslashes(urldecode($show_extra["value"]))."</td>\n</tr>\n";
						$this->row_count++;
					}
				}

				if ($show["image"] > 0)
				{
					$ad .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light><b>listing images</b></td></tr>\n\t";
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=center colspan=2>";
					$ad .= $this->display_ad_images ($db,$classified_id);
					$ad .= "</td></tr>";
					$this->row_count++;
				}


				if($show['item_type'] == 1)
				{
					// Display winner details
					if($show['final_price'] > 0.00)
					{
						if($show['auction_type'] == 1)
						{
							// Standard auction
							$ad .= "<tr>\n\t<td colspan=3 class=medium_font_light>";
							$ad .= "<table width=100%>";
							$ad .= "<tr  class=row_color_black>\n\t<td colspan=3 class=medium_font_light>\n\t<b>Standard Auction Winner</b></font></td></tr>\n\t";
							$ad .= "<tr  class=row_color_black>\n\t";
							$ad .= "<td width=30% class=small_font_light>\n\tWinner</font></td>\n\t";
							$ad .= "<td class=small_font_light>\n\tWinning bid price</font></td>\n\t";
							$ad .= "<td class=small_font_light>\n\tLink to auction</font></td>\n\t</tr>\n\t";

							$this->sql_query = "select * from ".$this->bid_table." where auction_id =".$show['id']." order by bid desc,time_of_bid asc limit 1";
							$result = $db->Execute($this->sql_query);
							if ($this->debug) echo $this->sql_query."<br>\n";
							if($result->RecordCount() == 1)
							{
								$bid_result = $result->FetchRow();

								// Get user that won auction
								$this->sql_query = "select username from ".$this->userdata_table." where id = ".$bid_result['bidder'];
								$result = $db->Execute($this->sql_query);
								if(!$result)
								{
									if ($this->debug) echo $this->sql_query."<br>\n";
									return false;
								}
								else
									$user_result = $result->FetchRow();

								$ad .= "<tr class=".$this->get_row_color().">\n\t\n\t<td class=small_font>".$user_result['username']."</td>\n\t";
								$ad .= "<td class=small_font>".$show['final_price']."</td>\n\t";
								$ad .= "<td class=small_font><a href=".$this->configuration_data['classifieds_url']."?a=4&b=2&c=".$show['id'].">".stripslashes(urldecode($show['title']))."</a></td><tr>";
								$this->row_count++;

								if (($show['final_fee'] == 1) && ($this->configuration_data['use_account_balance']) &&
									($show['current_bid'] != 0) && ($show['current_bid'] >= $show['reserve_price']))
								{
									$current_account_balance = $seller_info['account_balance'];
									//get the final_fee charge
									if ($show['price_plan_id'])
										$auction_price_plan_id = $show['price_plan_id'];
									else
									{
										//get the price plan attached to this seller
										$this->sql_query = "select auction_price_plan_id from ".$this->user_groups_price_plans_table." where id = ".$show['seller'];
										$price_plan_result = $db->Execute($this->sql_query);
										if ($this->debug) echo $this->sql_query." 2<BR>\n";
										if (!$price_plan_result)
										{
											if ($this->debug) echo $this->sql_query."<BR>\n";
											return false;
										}
										elseif  ($price_plan_result->RecordCount() == 1)
										{
											$show_price_plan = $price_plan_result->FetchRow();
											$auction_price_plan_id = $show_price_plan['auction_price_plan_id'];
										}
										else
										{
											if ($this->debug) echo $this->sql_query." - returned the wrong count<BR>\n";
											return false;
										}

									}

									$this->sql_query = "select charge from ".$this->final_fee_table." where".
										"(low<=".$show['current_bid']." AND high>=".$show['current_bid'].")
										and price_plan_id = ".$auction_price_plan_id." ORDER BY charge DESC limit 1";
									//echo $this->sql_query."<br>\n";
									$increment_result = $db->Execute($this->sql_query);
									if ($this->debug) echo $this->sql_query."<BR>\n";
									if (!$increment_result)
									{
										if ($this->debug) echo $this->sql_query."<BR>\n";
										return false;
									}
									elseif  ($increment_result->RecordCount() == 1)
									{
										$show_increment = $increment_result->FetchRow();
										$final_fee_percentage = $show_increment['charge'];
										if ($final_fee_percentage > 0)
										{

											$final_fee_charge = sprintf("%01.2f",(($final_fee_percentage * $show['current_bid']) / 100));

											$ad .= "<tr><td colspan=4 class=medium_error_font_tag>final fees due for this auction: ".urldecode($show['precurrency'])." ".$final_fee_charge." ".urldecode($show['postcurrency']);
											$ad .= "<br>final fee %: ".$final_fee_percentage."%";
											//$ad .= "total final fees to billed or have been billed: ".urldecode($show->PRECURRENCY)." ".((($total_quatity_bidders_receiving * $final_dutch_bid) * $final_fee_percentage) /100)." ".urldecode($show->POSTCURRENCY)." </td></tr>";
											$ad .= "</td></tr>";
										}
									}
									elseif ($increment_result->RecordCount() == 0)
									{
										$price_plan_name = $this->get_price_plan_name($db, $show['price_plan_id']);
										$ad .= "<tr><td colspan=4 class=medium_error_font_tag>";
										$ad .= "<br><b>ERROR: There are no final fees set for the ".$price_plan_name." price plan - 1</b><br>";
										$ad .= "</td></tr>";
									}
									else
									{
										return false;
									}
								}
								$ad .= "</table></td></tr>";
							}
							else
							{
								if ($this->debug) echo $this->sql_query."<br>\n";
								return false;
							}
						}
						else
						{
							// Dutch auction

							$this->sql_query = "select * from ".$this->bid_table." where auction_id=".$show->ID." order by bid desc,time_of_bid asc";
							$bid_result = $db->Execute($this->sql_query);
							if ($this->debug) echo $this->sql_query."<br>\n";
							if(!$bid_result)
							{
								if ($this->debug) echo $this->sql_query."<br>\n";
								return false;
							}
							elseif ($bid_result->RecordCount() > 0)
							{
								$ad .= "<tr>\n\t<td colspan=3 class=medium_font_light>";

								$ad .= "<table width=100%>";
								$ad .= "<tr class=row_color_black><td colspan=4 class=medium_font_light>\n\t<b>Dutch Auction Bidder(s)</b></font></td></tr>\n\t";
								$ad .= "<tr class=row_color_black>\n\t";

								$ad .= "<td width=30% class=small_font_light>\n\tBidder</font></td>\n\t";
								$ad .= "<td class=small_font_light>\n\tbidders bid price</font></td>\n\t";
								$ad .= "<td class=small_font_light>\n\tquantity bid on</font></td>\n\t\n\t";
								$ad .= "<td class=small_font_light>\n\tquantity won</font></td>\n\t</tr>\n\t";
								$total_quantity = $show->QUANTITY;
								$show_bidder = $bid_result->FetchNextObject();

								$final_dutch_bid = 0;
								do
								{
									$quantity_bidder_receiving = 0;
									if ($this->debug)
									{
										echo "TOP OF BIDDER LOOP<br>\n";
										echo $show->QUANTITY." is auction total starting quantity<bR>\n";
										echo $show_bidder->QUANTITY." is quantity of bid<Br>\n";
										echo $show_bidder->BIDDER." is bidder<bR>\n";
										echo $show_bidder->BID." is bid<br>\n";
										echo $total_quantity." is total_quantity<Br>\n";
									}
									if ( $show_bidder->QUANTITY <= $total_quantity )
									{
										if ($this->debug) echo "bidder quantity is less than or equal to the total_quantity<BR>\n";
										$quantity_bidder_receiving = $show_bidder->QUANTITY ;
										if ( $show_bidder->QUANTITY == $total_quantity )
										{
											$final_dutch_bid = $show_bidder->BID;
											if ($this->debug) echo $final_dutch_bid." is final bid after total = bid quantity<bR>\n";
										}
										$total_quantity = $total_quantity - $quantity_bidder_receiving;
									}
									else
									{
										if ($this->debug) echo "bidder quantity is greater than the total_quantity<BR>\n";
										$quantity_bidder_receiving = $total_quantity;
										$total_quantity = 0;
										$final_dutch_bid = $show_bidder->BID;
										if ($this->debug) echo $final_dutch_bid." is final bid after total < bid quantity<bR>\n";
									}

									if ($this->debug) echo $quantity_bidder_receiving." is quantity_bidder_receiving<BR>";
									if ($quantity_bidder_receiving)
									{
										$local_key = count($this->dutch_bidders);
										$this->dutch_bidders[$local_key]["bidder"] = $show_bidder->BIDDER;
										$this->dutch_bidders[$local_key]["quantity"] = $quantity_bidder_receiving;
										$this->dutch_bidders[$local_key]["bid"] = $show_bidder->BID;
										$bidder_info = $this->get_user_data($db,$show_bidder->BIDDER);
										$total_quatity_bidders_receiving = $total_quatity_bidders_receiving + $quantity_bidder_receiving;

										$ad .= "<tr class=".$this->get_row_color().">\n\t\n\t<td class=medium_font>".$bidder_info->USERNAME."</td>\n\t";
										$ad .= "<td class=medium_font>".urldecode($show->PRECURRENCY)." ".$show_bidder->BID." ".urldecode($show->POSTCURRENCY);
										if ($show_bidder->BID < $show->RESERVE_PRICE)
											$ad .= " (did not bid above the reserve)";
										$ad .= "</td>\n\t";
										$ad .= "<td class=medium_font>".$show_bidder->QUANTITY."</td>";
										$ad .= "<td class=medium_font>";
										if ($show_bidder->BID < $show->RESERVE_PRICE)
											$ad .= "0";
										else
										{
											$lowest_winning_bid = $show_bidder->BID;
											$ad .= $quantity_bidder_receiving;
										}
										$ad .= "</td><tr>";
										$this->row_count++;
										//echo $seller_report."<br><br>";
									}
								} while (($show_bidder = $bid_result->FetchNextObject()) && ($total_quantity != 0) && ($final_dutch_bid == 0));
								if ($this->debug)
								{
									echo $final_dutch_bid." is final dutch bid after resolution loop<bR>\n";
									echo $this->dutch_bidders[$local_key]["bid"]." is the last bid<BR>\n";
								}

								//if ($final_dutch_bid == 0)
								$final_dutch_bid = $lowest_winning_bid;

								if (!$final_dutch_bid)
									$final_dutch_bid = 0;

								$ad .= "<tr class=row_color_black><td colspan=4 class=medium_font_light>final dutch bid: ".urldecode($show->PRECURRENCY)." ".$final_dutch_bid." ".urldecode($show->POSTCURRENCY);
								if ($final_dutch_bid < $show->RESERVE_PRICE)
									$ad .= " (no dutch bidders bid enough to win any items) ";
								$ad .= "</td></tr>";

								//figure final fees for this auction
								if (($show->FINAL_FEE == 1) && ($this->configuration_data->USE_ACCOUNT_BALANCE) &&
									($show->CURRENT_BID != 0) && ($show->CURRENT_BID >= $show->RESERVE_PRICE))
								{
									$current_account_balance = $seller_info->ACCOUNT_BALANCE;
									//get the final_fee charge
									if ($show->PRICE_PLAN_ID)
										$auction_price_plan_id = $show->PRICE_PLAN_ID;
									else
									{
										//get the price plan attached to this seller
										$this->sql_query = "select auction_price_plan_id from ".$this->user_groups_price_plans_table." where
											id = ".$show->SELLER;
										$price_plan_result = $db->Execute($this->sql_query);
										if ($this->debug) echo $this->sql_query."<BR>\n";
										if (!$price_plan_result)
										{
											if ($this->debug) echo $this->sql_query."<BR>\n";
											return false;
										}
										elseif  ($price_plan_result->RecordCount() == 1)
										{
											$show_price_plan = $price_plan_result->FetchNextObject();
											$auction_price_plan_id = $show_price_plan->AUCTION_PRICE_PLAN_ID;
										}
										else
										{
											if ($this->debug) echo $this->sql_query." - returned the wrong count<BR>\n";
											return false;
										}

									}

									$this->sql_query = "select charge from ".$this->final_fee_table." where".
										"(low<=".$show->CURRENT_BID." AND high>=".$show->CURRENT_BID.")
										and price_plan_id = ".$auction_price_plan_id." ORDER BY charge DESC limit 1";
									//echo $this->sql_query."<br>\n";
									$increment_result = $db->Execute($this->sql_query);
									if ($this->debug) echo $this->sql_query."<BR>\n";
									if (!$increment_result)
									{
										if ($this->debug) echo $this->sql_query."<BR>\n";
										return false;
									}
									elseif  ($increment_result->RecordCount() == 1)
									{
										$show_increment = $increment_result->FetchNextObject();
										$final_fee_percentage = $show_increment->CHARGE;
										if ($final_fee_percentage > 0)
										{
											//this is a dutch auction type
											if (count($this->dutch_bidders))
											{
												reset ($this->dutch_bidders);
												//get total amount of final fees to charge
												//and test to see if above current account balance.
												//if not above the current account balance leave for the next
												//auction placement or until balance is above final fee costs
												$total_final_fee = 0;
												foreach ($this->dutch_bidders as $key => $value)
												{
													$final_fee_charge = sprintf("%01.2f",(($final_fee_percentage * ($show->CURRENT_BID * $this->dutch_bidders[$key]["quantity"])) / 100));
													$this->dutch_bidders[$key]["final_fee"] = $final_fee_charge;
													if ($show->RESERVE_PRICE <= $this->dutch_bidders[$key]["bid"])
														$total_final_fee = $total_final_fee + $final_fee_charge;
												}
											}
										} //end of if ($final_fee_percentage > 0)
									}
									elseif ($increment_result->RecordCount() == 0)
									{
										$price_plan_name = $this->get_price_plan_name($db,$auction_price_plan_id);
										$ad .= "<tr><td colspan=4 class=medium_error_font_tag>";
										$ad .= "<br><b>ERROR: There are no final fees set for the ".$price_plan_name." price plan - 2</b><br>";
										$ad .= "</td></tr>";
									}
									else
									{
										return false;
									}

									$ad .= "<tr class=row_color_black><td colspan=4 class=medium_font_light>final fees due for this auction: ".urldecode($show->PRECURRENCY)." ".$total_final_fee." ".urldecode($show->POSTCURRENCY);
									$ad .= "<br>final fee %: ".$final_fee_percentage."%<br>";
									$ad .= "total items won by dutch bidder(s) above: ".$total_quatity_bidders_receiving;
									//$ad .= "total final fees to billed or have been billed: ".urldecode($show->PRECURRENCY)." ".((($total_quatity_bidders_receiving * $final_dutch_bid) * $final_fee_percentage) /100)." ".urldecode($show->POSTCURRENCY)." </td></tr>";
									$ad .= "</td></tr>";
								}
								$ad .= "</table></td></tr>";

							}
						}
					}
				}

				$ad .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light><b>transaction specifics</b></td></tr>\n\t";

				if ($show["renewal_payment_expected"])
				{
					if ($show["renewal_payment_expected"] == 1)
					{
						$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>expecting renewal payment (by date): </b></b></td>";
						$ad .= "<td class=medium_font>";

						$ad .= "yes";
						$ad .= " ( ".date("M d, Y H:i:s", $show["renewal_payment_expected_by"])." )";
						$ad .= " </td>\n</tr>\n";
						$this->row_count++;
					}
					elseif ($show["renewal_payment_expected"] == 2)
					{
						$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>expecting upgrade payment (by date): </b></td>";
						$ad .= "<td class=medium_font>";

						$ad .= "yes";
						$ad .= " ( ".date("M d, Y H:i:s", $show["renewal_payment_expected_by"])." )";
						$ad .= " </td>\n</tr>\n";
						$this->row_count++;
					}

					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>bolding: </b></td>";
					$ad .= "<td class=medium_font>";
					if (($show["renewal_bolding"]) || ($show["bolding_upgrade"]))
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>better placement: </b></td>";
					$ad .= "<td class=medium_font>";
					if (($show["renewal_better_placement"]) || ($show["better_placement_upgrade"]))
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>featured listing: </b></td>";
					$ad .= "<td class=medium_font>";
					if (($show["renewal_featured_ad"]) || ($show["featured_ad_upgrade"]))
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>featured listing level 2: </b></td>";
					$ad .= "<td class=medium_font>";
					if (($show["renewal_featured_ad_2"]) || ($show["featured_ad_2_upgrade"]))
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>featured listing level 3: </b></td>";
					$ad .= "<td class=medium_font>";
					if (($show["renewal_featured_ad_3"]) || ($show["featured_ad_3_upgrade"]))
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>featured listing level 4: </b></td>";
					$ad .= "<td class=medium_font>";
					if (($show["renewal_featured_ad_4"]) || ($show["featured_ad_4_upgrade"]))
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>featured listing level 5: </b></td>";
					$ad .= "<td class=medium_font>";
					if (($show["renewal_featured_ad_5"]) || ($show["featured_ad_5_upgrade"]))
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>attention getter: </b></td>";
					$ad .= "<td class=medium_font>";
					if (($show["renewal_attention_getter"]) || ($show["attention_getter_upgrade"]))
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
				}
				else
				{
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>bolding: </b></td>";
					$ad .= "<td class=medium_font>";
					if ($show["bolding"])
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>better placement: </b></td>";
					$ad .= "<td class=medium_font>";
					if ($show["better_placement"])
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>featured listing: </b></td>";
					$ad .= "<td class=medium_font>";
					if ($show["featured_ad"])
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>featured listing level 2: </b></td>";
					$ad .= "<td class=medium_font>";
					if ($show["featured_ad_2"])
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>featured listing level 3: </b></td>";
					$ad .= "<td class=medium_font>";
					if ($show["featured_ad_3"])
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>featured listing level 4: </b></td>";
					$ad .= "<td class=medium_font>";
					if ($show["featured_ad_4"])
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>featured listing level 5: </b></td>";
					$ad .= "<td class=medium_font>";
					if ($show["featured_ad_5"])
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= " </td>\n</tr>\n";
					$this->row_count++;
					$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>attention getter: </b></td>";
					$ad .= "<td class=medium_font>";
					if ($show["attention_getter"])
						$ad .= "yes";
					else
						$ad .= "no";
					$ad .= "</td>\n</tr>\n";
					$this->row_count++;
				}
				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>transaction type: </b></td>\n\t";
				$ad .= "<td class=medium_font>";
				switch ($show["transaction_type"])
				{
					case 1:
						$ad .= "cash";
						break;
					case 2:
						$this->sql_query = "SELECT cc_id,name,cc_transaction_table FROM $this->cc_choices WHERE chosen_cc = 1";
						$cc_result = $db->Execute($this->sql_query);
						if ($cc_result->RecordCount() == 1) $chosen_cc = $cc_result->FetchRow();
						else return false;
						switch ($chosen_cc["cc_id"])
						{
							case 1:
								//authorize.net
								$trans_table_key = "authorizenet_transaction_id";
								break;
							case 2:
								//2checkout
								$trans_table_key = "transaction_id";
								break;
							case 3:
								//bitel
								$trans_table_key = "bitel_transaction_id";
							break;
							case 4:
								//linkpoint
								$trans_table_key = "linkpoint_transaction_id";
								break;
							case 5:
								//internetsecure
								$trans_table_key = "internetsecure_transaction_id";
								break;
							case 6:
								//payflow pro
								$trans_table_key = "payflow_pro_transaction_id";
								break;
							case 7:
								//paypal pro
								$trans_table_key = "transaction_id";
								break;
							case 8:
								//add new cc payment handler here
							break;
							case 9:
								//manual processing
								$trans_table_key = "manual_transaction_id";
							break;
						}
						$this->sql_query = "SELECT * FROM ".$chosen_cc["cc_transaction_table"]." WHERE classified_id = ".$show["id"]." ORDER BY $trans_table_key DESC LIMIT 1";
						$card_data = $db->Execute($this->sql_query);
						if($card_data !== false)
						{
							if ($card_data->RecordCount() == 1)
							{
								$card = $card_data->FetchRow();
								require_once '../classes/site_class.php';
								$cc_number = Site::decrypt($card["card_num"], $card["decryption_key"]);
								$cvv2 = $card["cvv2_code"];
								if ($_SERVER["HTTPS"] != "on")
								{
									$cc_num_Xs = '';$cvv2_Xs = '';
									for ($i=0;$i<strlen($cc_number);$i++)
										$cc_num_Xs .= 'X';
									for ($i=0;$i<strlen($cvv2);$i++)
										$cvv2_Xs .= 'X';
									$cc_number = substr_replace($cc_number,$cc_num_Xs,0,-4);
									$cvv2 = $cvv2_Xs;
								}
								$ad .= $chosen_cc["name"].$this->show_tooltip(1,1)."&nbsp;<a href=https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]."><span class=medium_font>View Full Credit Card Number</span></a>
										<br>Credit Card Number - ".chunk_split($cc_number,4,' ')."
										<br>Expiration Date - ".$card["exp_date"];
								if ($chosen_cc["cc_transaction_table"]=='geodesic_cc_paypal_transactions' ||
									$chosen_cc["cc_transaction_table"]=='geodesic_cc_manual_transactions')
									$ad .= "<br>Card Verification Code - $cvv2";
							}
						}
						else $ad .= "<i>Information no longer available.  Transaction may have been deleted from '".$chosen_cc["cc_transaction_table"]."' table</i>";
						break;
					case 3:
						$ad .= "paypal - may not be completed through paypal";
						break;
					case 4:
						$ad .= "money order";
						break;
					case 5:
						$ad .= "check";
						break;
					case 6:
						$ad .= "worldpay - may not be completed through worldpay";
						break;
					default:
						$ad .= "none";
				}
				$ad .= " </td>\n</tr>\n";
				$this->row_count++;

				if($show['item_type'] == 2)
				{
					$this->sql_query = "SELECT * FROM ".$this->price_plan_table." where price_plan_id = ".$show['price_plan_id'];
					if ($this->debug) echo $this->sql_query."<br>\n";
					$price_plan_result = $db->Execute($this->sql_query);
					if (!$price_plan_result)
					{
						if ($this->debug) echo $this->sql_query."<br>\n";
						return false;
					}
					elseif ($price_plan_result->RecordCount() == 1)
					{
						$price_plan = $price_plan_result->FetchRow();
						if (($price_plan['charge_percentage_at_auction_end'])  && ($price_plan['roll_final_fee_into_future']))
						{
							$this->sql_query = "SELECT * FROM ".$this->classifieds_table." where final_fee = 1 and final_fee_transaction_number = 0 and ends < ".time()." and seller = ".$show['seller'];
							if ($this->debug) echo $this->sql_query."<br>\n";
							$final_fee_result = $db->Execute($this->sql_query);
							if (!$final_fee_result)
							{
								if ($this->debug) echo $this->sql_query."<br>\n";
								return false;
							}
							elseif ($final_fee_result->RecordCount() > 0)
							{
								$pending_final_fee_transactions = "";
								while ($show_final_fee = $final_fee_result->FetchRow())
								{
									//clear open final fee transactions
									$this->sql_query = "select * from ".$this->bid_table." where auction_id=".$show_final_fee['id']." order by bid desc,time_of_bid asc";
									$bid_result = $db->Execute($this->sql_query);
									if ($this->debug) echo $this->sql_query."<br>\n";
									if (!$bid_result)
									{
										if ($this->debug) echo $this->sql_query."<br>\n";
										return false;
									}
									elseif ($bid_result->RecordCount() > 0)
									{
										$total_quantity = $show_final_fee['quantity'];
										//echo "total items sold - ".$total_quantity."<br>\n";
										$final_dutch_bid = 0;
										$total_quantity_sold = 0;
										$show_bidder = $bid_result->FetchRow();
										do
										{
											$quantity_bidder_receiving = 0;
											if ( $show_bidder['quantity'] <= $total_quantity )
											{
												$quantity_bidder_receiving = $show_bidder['quantity'];
												if ($show_bidder['quantity'] == $total_quantity )
												{
													$final_dutch_bid = $show_bidder['bid'];
													//echo $final_dutch_bid." is final bid after quantity_bidder_receiving <= bid quantity<bR>\n";
												}
												$total_quantity = $total_quantity - $quantity_bidder_receiving;
											}
											else
											{
												$quantity_bidder_receiving = $total_quantity;
												$total_quantity = 0;
												$final_dutch_bid = $show_bidder['bid'];
												//echo $final_dutch_bid." is final bid after total < bid quantity<bR>\n";
											}
											if ($quantity_bidder_receiving)
											{
												$dutch_bidder_bid = $show_bidder['bid'];
												//echo $dutch_bidder_bid." is final bid in quantity_bidder_receiving<bR>\n";
											}
											//echo $total_quantity." is total quantity after bidder - ".$show_bidder->BIDDER."<br>";
											$total_quantity_sold = $total_quantity_sold + $quantity_bidder_receiving;
										} while (($show_bidder = $bid_result->FetchRow()) && ($total_quantity != 0) && ($final_dutch_bid == 0));

										if ($final_dutch_bid == 0)
											$final_dutch_bid = $dutch_bidder_bid;
										//echo $final_dutch_bid." is the final_dutch_bid<br>\n";
										//echo $show_final_fee->RESERVE_PRICE." is the reserve<Br>\n";
										//echo $total_quantity_sold." is total_quantity_sold<Br>\n";
										if (($total_quantity_sold) && ($final_dutch_bid) && ($final_dutch_bid >= $show_final_fee['reserve_price']))
										{
											//determine total cost
											$this->sql_query = "select charge from ".$this->final_fee_table." where ".
												"(low<=".$final_dutch_bid." AND high>=".$final_dutch_bid.") and price_plan_id = ".$price_plan['price_plan_id']." ORDER BY charge DESC limit 1";
											if ($this->debug) echo $this->sql_query."<br>\n";
											$increment_result = $db->Execute($this->sql_query);
											if (!$increment_result)
											{
												if ($this->debug) echo $this->sql_query."<br>\n";
												return false;
											}
											elseif  ($increment_result->RecordCount() == 1)
											{
												$show_increment = $increment_result->FetchRow();
												$final_fee_percentage = $show_increment['charge'];
												if ($final_fee_percentage > 0)
												{
													$final_fee_charge = ((($final_fee_percentage/100) * $final_dutch_bid) * $total_quantity_sold);
												}
												if ($this->debug) echo $final_fee_charge." is final fee charge for ".$show_final_fee['id']."<br>\n";
												$this->final_fee_total = $this->final_fee_total + $final_fee_charge;
												if ($this->debug) echo $final_fee_charge." was just added to final fee total<br>\n";
												$pending_auction_data = $this->get_auction_data($db,$show_final_fee['id']);
												if (strlen(trim($pending_final_fee_transactions)) == 0)
													$pending_final_fee_transactions = "<table>";
												$pending_final_fee_transactions .= "<tr><td class=medium_font>".urldecode($pending_auction_data['title'])."
													(".$show_final_fee['id']." )</td><td class=medium_font> ".
													$this->configuration_data['precurrency']." ".$final_fee_charge." ".$this->configuration_data['postcurrency']."</td>
													</tr>";
											}
											elseif ($increment_result->RecordCount() == 0)
											{
												$price_plan_name = $this->get_price_plan_name($db, $price_plan['price_plan_id']);
												$pending_final_fee_transactions .= "<table><tr><td colspan=4 class=medium_error_font_tag>";
												$pending_final_fee_transactions .= "<br><b>ERROR: There are no final fees set for the ".$price_plan_name." price plan - 3</b><br>";
												$pending_final_fee_transactions .= "</td></tr>";
											}
											else
											{
												return false;
											}
											$this->row_count++;
										}
									}
									else
									{
										//no bids for this dutch auction
										//no final fees
									}
								}
								$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\tfinal fees pending:</font></td>\n\t";
								$ad .= "<td class=medium_font>\n\t".$this->configuration_data['precurrency']." ".
									sprintf("%01.2f",$this->final_fee_total)." ".$this->configuration_data['postcurrency'].
									" - these will be cleared if approved, moved to an invoice or taken from the site balance </font></td>\n</tr>\n";
								$this->row_count++;
								if (strlen(trim($pending_final_fee_transactions)) > 0)
								{
									$pending_final_fee_transactions .= "</table>";
									$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\tindividual auctions and final fees pending for each<Br></td>\n\t";
									$ad .= "<td class=medium_font>\n\t".$pending_final_fee_transactions."</td>\n</tr>\n";
								}
							}
						}
					}
				}

				$ad .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>total:</b></td>\n\t";
				if ($show["renewal_payment_expected"])
					$ad .= "<td class=medium_font>".$this->configuration_data["precurrency"]." ".sprintf("%01.2f",$show["renewal_total"])." ".$this->configuration_data["postcurrency"]."</td>\n</tr>\n";
				else
					$ad .= "<td class=medium_font>".$this->configuration_data["precurrency"]." ".sprintf("%01.2f",$show["total"])." ".$this->configuration_data["postcurrency"]." </td>\n</tr>\n";
				$this->row_count++;


				if ($display)
				{
					$ad .= "<tr class=row_color_black>\n\t<td colspan=2><a href=index.php?a=17&b=".$show["seller"]."><span class=medium_font_light><b>sellers data</b></span></a></td></tr>\n\t";
					$ad .= "<tr class=row_color_black>\n\t<td colspan=2><a href=index.php?a=19><span class=medium_font_light><b>list users</b></span></a></td></tr>\n\t";
					$ad .= "</table>\n";
					echo $ad;
					return true;
				}
				else
				{
					$ad .= "</table>\n";
					return $ad;
				}
			}
			else
			{
				return "no name";
			}
		}
		else
		{
			return false;
		}
	} //end of function display_basic_classified_data

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_classified_ad($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$this->sql_query = "delete from ".$this->classifieds_table." where id = ".$classified_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				//echo $this->sql_query."<br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}

			//delete url images
			$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$classified_id;
			$get_url_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$get_url_result)
			{
				$this->body .=$this->sql_query."<br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($get_url_result->RecordCount())
			{
				while ($show_url = $get_url_result->FetchRow())
				{
					if ($show_url["full_filename"])
						unlink($show_url["file_path"].$show_url["full_filename"]);
					if ($show_url["thumb_filename"])
						unlink($show_url["file_path"].$show_url["thumb_filename"]);
				}
				$this->sql_query = "delete from ".$this->images_urls_table." where classified_id = ".$classified_id;
				$delete_url_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$delete_url_result)
				{
					$this->body .=$this->sql_query."<br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}
			}

			//delete db images
			$this->sql_query = "delete from ".$this->images_table." where classified_id = ".$classified_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				//echo $this->sql_query."<br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}

			//delete from auctions extra questions
			$this->sql_query = "delete from ".$this->classified_extra_table." where classified_id = ".$classified_id;
			$remove_extra_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>";
			if (!$remove_extra_result)
			{
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	} //end of function delete_classified_ad

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_ad_count_for_category($db,$category_id=0)
	{
		if ($category_id)
		{
			//get the count for this category
			$count = 0;

			$this->sql_query = "select category_id from ".$this->classified_categories_table." where parent_id = ".$category_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>";
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[2524];
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				while ($show_category = $result->FetchRow())
				{
					$returned_count = $this->get_ad_count_for_category($db,$show_category["category_id"]);
					if ($returned_count)
						$count += $returned_count;

					//echo $count." is count returned for category ".$category_id."<br>\n";
				}
			}

			$count += $this->get_ad_count_this_category($db,$category_id);
			return $count;
		}
		else
		{
			//category_id is missing
			return false;
		}

	} //end of function get_ad_count_for_category

//##################################################################################

	function get_ad_count_this_category($db,$category_id=0)
	{
		if ($category_id)
		{
			//get the count for this category
			$count = 0;

			$this->sql_query = "select count(*) as total from ".$this->classifieds_table." where live = 1 and category = ".$category_id;
			$count_result = $db->Execute($this->sql_query);
			if (!$count_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[2524];
				return false;
			}
			elseif ($count_result->RecordCount() == 1)
			{
				$show = $count_result->FetchRow();
				return $show["total"];
			}
			else
			{
				return 0;
			}
		}
		else
		{
			//category_id is missing
			return false;
		}

	} //end of function get_ad_count_for_category

//##################################################################################

	function update_category_count($db,$category_id=0)
	{
		if ($category_id)
		{
			$category_tree = $this->get_category_tree($db,$category_id);
			reset ($this->category_tree_array);
			if ($category_tree)
			{
				if (is_array($this->category_tree_array))
				{
					$i = count($this->category_tree_array);
					while ($i > 0 )
					{
						//display all the categories
						$i--;

						if ($this->category_tree_array[$i]["category_id"] != 0)
						{
							//$category_count = $this->get_ad_count_for_category($db,$this->category_tree_array[$i]["category_id"]);
							$this->sql_query = "select in_statement from ".$this->classified_categories_table." where
								category_id = ".$this->category_tree_array[$i]["category_id"];
							$in_category_result = $db->Execute($this->sql_query);
							//echo $this->sql_query."<br>\n";
							if (!$in_category_result)
							{
								return false;
							}
							if ($in_category_result->RecordCount() == 1)
							{
								$show_in_statement = $in_category_result->FetchRow();

								// Count ads
								$this->sql_query = "select count(*) as total from ".$this->classifieds_table." where live = 1 and category ".$show_in_statement["in_statement"]." and item_type = 1";
								$count_result = $db->Execute($this->sql_query);
								//echo $this->sql_query."<br>\n";

								// Count Auctions
								$this->sql_query = "select count(*) as total from ".$this->classifieds_table." where live = 1 and category ".$show_in_statement["in_statement"]." and item_type = 2";
								$auction_count_result = $db->Execute($this->sql_query);
								//echo $this->sql_query."<br>\n";

								if (!$count_result || !$auction_count_result)
								{
									//echo $this->sql_query." is the query<br>\n";
									$this->error_message = $this->messages[2524];
									return false;
								}
								elseif ($count_result->RecordCount() == 1)
								{
									$show = $count_result->FetchRow();
									$auction_show = $auction_count_result->FetchRow();

									$this->sql_query = "update ".$this->classified_categories_table." set
										category_count = ".$show["total"].",
										auction_category_count = ".$auction_show["total"]."
										where category_id = ".$this->category_tree_array[$i]["category_id"];
									$update_count_result = $db->Execute($this->sql_query);
									//echo $this->sql_query."<br>\n";
									if (!$update_count_result)
									{
										return false;
									}
								}
								else
								{
									return false;
								}
							}
							else
							{
								return false;
							}
						}
					}
				}
				else
				{
					$category_tree_fields = $category_tree;
				}
			}
		}
	} //end of function update_category_count

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_category_dropdown($db,$name,$category_id=0,$no_main=0,$dropdown_limit=0)
	{
		if ($this->debug)
		{
			echo "TOP OF GET_CATEGORY_DROPDOWN<br>\n";
			echo $dropdown_limit." is dropdown_limit inside get_category_dropdown<bR>\n";
			echo $category_id." is the category id<BR>\n";
		}

		if (count($this->category_dropdown_name_array) == 0)
		{
			if (!$no_main)
			{
				array_push($this->category_dropdown_name_array, "All Categories");
				array_push($this->category_dropdown_id_array,0);
			}

			$this->get_subcategories_for_dropdown($db,0,$dropdown_limit);
		}
		else
		{
			reset($this->category_dropdown_name_array);
		}

		//build the select statement
		//array_reverse($this->category_dropdown_name_array);
		//array_reverse($this->category_dropdown_id_array);
		$this->dropdown_body = "<select name=".$name.">\n\t\t";
		foreach($this->category_dropdown_name_array as $key => $value)
		{
			$this->dropdown_body .= "<option ";
			if ($this->category_dropdown_id_array[$key] == $category_id)
				$this->dropdown_body .= "selected";
			$this->dropdown_body .= " value=".$this->category_dropdown_id_array[$key].">".$this->category_dropdown_name_array[$key]."</option>\n\t\t";
		}
		$this->dropdown_body .= "</select>\n\t";
		if ($this->debug)
		{
			echo "END OF GET_CATEGORY_DROPDOWN<br>\n";
		}
     		return true;

	} //end of function get_category_dropdown

//##################################################################################

	function get_subcategories_for_dropdown($db,$category_id=0,$dropdown_limit=0)
	{
		//$stage++;
		//$this->sql_query = "select category_id,parent_id,category_name from ".$this->classified_categories_table."
		//	where parent_id = ".$category_id;
		if ($this->debug)
		{
			echo "TOP OF GET_SUBCATEGORIES_FOR_DROPDOWN<Br>\n";
			echo $dropdown_limit." is dropdown limit before check<BR>\n";
			echo $this->stage." is this->stage<Br>\n";
		}
		if ($dropdown_limit == 0)
			$dropdown_limit = 2;

		if ($this->debug) echo $dropdown_limit." is dropdown_limit after check<BR>\n";

		if (($this->stage + 1) <= $dropdown_limit)
		{
			$this->sql_query = "select ".$this->classified_categories_table.".category_id as category_id,
				".$this->classified_categories_table.".parent_id as parent_id,".$this->classified_categories_languages_table.".category_name as category_name
				from ".$this->classified_categories_table.",".$this->classified_categories_languages_table."
				where ".$this->classified_categories_table.".category_id = ".$this->classified_categories_languages_table.".category_id
				and ".$this->classified_categories_table.".parent_id = ".$category_id."
				and ".$this->classified_categories_languages_table.".language_id = 1 order by ".$this->classified_categories_table.".display_order,".$this->classified_categories_languages_table.".category_name";
			$category_result =  $db->Execute($this->sql_query);
			//if ($this->debug) echo $this->sql_query." is the query<br>\n";
			if (!$category_result)
			{
				if ($this->debug) echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[2052];
				return false;
			}
			elseif ($category_result->RecordCount() > 0)
			{
				$this->stage++;
				while ($show_category = $category_result->FetchRow())
				{
					$pre_stage = "";
					for ($i=1;$i<=$this->stage;$i++)
					{
						$pre_stage .= "&nbsp;&nbsp;&nbsp;";
					}
					if ($category_id != 0)
					{
						array_push($this->category_dropdown_name_array, $pre_stage.urldecode(stripslashes($show_category["category_name"])));
						array_push($this->category_dropdown_id_array,$show_category["category_id"]);
					}
					else
					{
						array_push($this->category_dropdown_name_array, urldecode(stripslashes($show_category["category_name"])));
						array_push($this->category_dropdown_id_array, $show_category["category_id"]);
					}
					$this->get_subcategories_for_dropdown($db,$show_category["category_id"],$dropdown_limit);
				}
				$this->stage--;
			}
		}
		if ($this->debug) echo "BOTTOM OF GET_SUBCATEGORIES_FOR_DROPDOWN<bR>\n";
		return;
	} //end of function get_subcategories_for_dropdown

//##################################################################################

	function check_subscriptions_and_credits($db,$transaction_id)
	{
		if ($transaction_id)
		{
			$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$transaction_id;
			//echo $this->sql_query." is the query <br>\n";
			$ad_result = $db->Execute($this->sql_query);
			if (!$ad_result)
			{
				//echo $this->sql_query." is the query <br>\n";
				exit;
			}
			if ($ad_result->RecordCount() == 1)
			{
				$show_classifieds = $ad_result->FetchRow();
				$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$show_classifieds["seller"];
				//echo $this->sql_query." is the query <br>\n";
				$price_plan_id_result = $db->Execute($this->sql_query);
				if (!$price_plan_id_result)
				{
					//echo $this->sql_query." is the query <br>\n";
					exit;
				}
				elseif ($price_plan_id_result->RecordCount() == 1)
				{
					$show_price_plan_id = $price_plan_id_result->FetchRow();

					$price_plan = $this->get_price_plan($db,$show_price_plan_id["price_plan_id"]);
					if ($price_plan["type_of_billing"] == 2)
					{
						if (!$this->user_currently_subscribed($db,$show_classifieds["seller"]))
						{
							//push the subscription up
							$this->sql_query = "delete from ".$this->classified_user_subscriptions_table." where
								user_id = ".$show_classifieds["seller"];
							//echo $this->sql_query." is the query<br>\n";
							$free_subscription_result = $db->Execute($this->sql_query);
							if (!$free_subscription_result)
							{
								$this->site_error($db->ErrorMsg());
								//echo $this->sql_query." is the query<br>\n";
								$this->error["confirm"] =urldecode($this->messages[229]);
								return false;
							}

							//get subscription choice
							$this->sql_query = "select * from ".$this->classified_subscription_choices_table." where period_id = ".$show_classifieds["subscription_choice"];
							$choice_result = $db->Execute($this->sql_query);
							//echo $this->sql_query."<br>\n";
							if (!$choice_result)
							{
								return false;
							}
							elseif ($choice_result->RecordCount() == 1 )
							{
								$show_subscription_choice = $choice_result->FetchRow();
								//build expiration from subscription choice
								$expiration = (($show_subscription_choice["value"] * 86400) + $this->shifted_time());
								$this->sql_query = "insert into ".$this->classified_user_subscriptions_table."
									(user_id,subscription_expire)
									values
									(".$show_classifieds["seller"].",".$expiration.")";
								//echo $this->sql_query." is the query<br>\n";
								$free_subscription_result = $db->Execute($this->sql_query);
								if (!$free_subscription_result)
								{
									$this->site_error($db->ErrorMsg());
									//echo $this->sql_query." is the query<br>\n";
									$this->error["confirm"] =urldecode($this->messages[229]);
									return false;
								}
							}
						}
						//else
						//	echo "user is currently subscribed<Br>\n";
					}
					elseif ($price_plan["type_of_billing"] == 1)
					{
						//check to see if this was a credit
						if (strlen(trim($show_classifieds["discount_id"])) > 0)
						{
							$this->sql_query = "select * from ".$this->classified_discount_codes_table." where
								discount_id = \"".urlencode(trim($show_classifieds["discount_id"]))."\"
								and active = 1";
							$discount_check_result =  $db->Execute($this->sql_query);
							//echo $this->sql_query." is the query<br>\n";
							if (!$discount_check_result)
							{
								echo $this->sql_query." is the query<br>\n";
								$this->error_message = $this->messages[3501];
								return false;
							}
							elseif ($discount_check_result->RecordCount() == 1)
							{
								$discount_code = $discount_check_result->FetchRow();
								if ($discount_code["user_id"])
								{
									//there is a user id
									//check to see if the user_id has credits attached
									$this->sql_query = "select * from ".$this->user_credits_table." where
										user_id = ".$discount_code["user_id"];
									//echo $this->sql_query." is the query<br>\n";
									$credit_result =  $db->Execute($this->sql_query);
									if (!$credit_result)
									{
										//echo $this->sql_query." is the query<br>\n";
										$this->error_message = $this->messages[3501];
										return false;
									}
									elseif ($credit_result->RecordCount() > 0)
									{
										//if credits attached the cost of the ad is 0
										//subtract the cost of the ad from the subtotal
										$this->remove_a_users_credit($db,$discount_code["user_id"]);
										//discount applies to extra features
									}
									elseif ($credit_result->RecordCount() == 0)
									{
										//there are no credits attached to the discount code user id
										//discount code is the only discount that applies
									}
								}
							}
							else
							{
								//discount code does not match any discount code
							}
						}
						elseif (!$this->get_user_credits($db,$show_classifieds["seller"]))
						{
							//remove a credit
							$this->remove_a_users_credit($db,$show_classifieds["seller"]);
						}
					}
				}
				else
					return false;
			}
			else
				return false;
		}
		else
			return false;
		return true;
	} //end of function check_subscriptions_and_credits

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function remove_a_users_credit($db,$discount_code_user_id=0)
	{
		if ($discount_code_user_id)
			$user_id_removed_from = $discount_code_user_id;
		else
			$user_id_removed_from = $this->classified_user_id;
		$this->sql_query = "select * from ".$this->user_credits_table." where user_id = ".$user_id_removed_from." order by credits_expire asc limit 1";
		$credits_results = $db->Execute($this->sql_query);
		echo $this->sql_query."<br>\n";
		if (!$credits_results)
		{
			return false;
		}
		elseif ($credits_results->RecordCount() == 1)
		{
			//remove one of these credits
			$show_credits = $credits_results->FetchRow();
			if ($show_credits["credit_count"] == 1)
			{
				//delete from the table
				$this->sql_query = "delete from ".$this->user_credits_table." where
					credits_id = ".$show_credits["credits_id"]."
					and user_id = ".$user_id_removed_from;
				$remove_credits_results = $db->Execute($this->sql_query);
				echo $this->sql_query."<br>\n";
				if (!$remove_credits_results)
				{
					return false;
				}
			}
			else
			{
				//remove one from the credit count
				$this->sql_query = "update ".$this->user_credits_table." set
					credit_count = ".($show_credits["credit_count"] - 1)."
					where credits_id = ".$show_credits["credits_id"]."
					and user_id = ".$user_id_removed_from;
				$remove_credit = $db->Execute($this->sql_query);
				echo $this->sql_query."<br>\n";
				if (!$remove_credit)
				{
					return false;
				}
			}
		}
		return true;

	} //end of function remove_a_users_credit

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_user_credits($db,$user_id)
	{
		if ($user_id)
		{
			//expire user credits
			$this->sql_query = "select * from ".$this->user_credits_table." where user_id = ".$user_id." order by credits_expire asc limit 1";
			$expire_results = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$expire_results)
				return false;
			elseif ($expire_results->RecordCount() == 1)
				return 1;
			else
				return 0;
		}
		else
			return false;
	} //end of function get_user_credits

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function user_currently_subscribed($db,$user_id=0)
	{
		if ($user_id)
		{
			$this->sql_query = "select * from ".$this->classified_user_subscriptions_table." where subscription_expire > ".$this->shifted_time()." and user_id = ".$user_id;
			$get_subscriptions_results = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$get_subscriptions_results)
				return false;
			elseif ($get_subscriptions_results->RecordCount() == 0)
				return 0;
			elseif ($get_subscriptions_results->RecordCount() > 0)
				return 1;
		}
		else
			return false;
	} // end of function check_user_subscription

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_page_language_template($db,$page_id=0,$language_id=0)
	{
		if (($page_id) && ($language_id))
		{
			if ($page_id == 69)
			{

				$sql_query = "select * from ".$this->ad_configuration_table;
				$result = $db->Execute($sql_query);
				if (!$result)
				{
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show = $result->FetchRow();
					return $show["ad_detail_print_friendly_template"];
				}
				else
					return false;
			}
			elseif ($page_id == 157)
			{

				$sql_query = "select * from ".$this->ad_configuration_table;
				$result = $db->Execute($sql_query);
				if (!$result)
				{
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show = $result->FetchRow();
					return $show["popup_image_template_id"];
				}
				else
					return false;
			}
			else
			{
				$this->sql_query = "select template_id from ".$this->pages_templates_table." where page_id = ".$page_id." and language_id = ".$language_id;
				$template_results = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$template_results)
				{
						return false;
				}
				elseif ($template_results->RecordCount() == 1)
				{
					$show = $template_results->FetchRow();
					return $show["template_id"];
				}
				else
					return false;
			}

		}
		else
		{
			return false;
		}
	} //end of function get_page_language_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_text($db,$current_page_id=0)
	{
		//get default language
		$this->sql_query = "select language_id from ".$this->pages_languages_table." where default_language = 1";
		$language_result = $db->Execute($this->sql_query);
		if (!$language_result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($language_result->RecordCount() == 1)
		{
			$show_language = $language_result->FetchRow();
			$language_id = $show_language["language_id"];
		}
		else
		{
			$language_id = 1;
		}
		if ($current_page_id)
			$this->sql_query = "select text_id,text from ".$this->pages_text_languages_table." where page_id = ".$current_page_id." and language_id = ".$language_id;
		else
			$this->sql_query = "select text_id,text from ".$this->pages_text_languages_table." where page_id = ".$this->page_id." and language_id = ".$language_id;
		//echo $this->sql_query."<br>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			//take the database message result and push the contents into an array
			while ($show = $result->FetchRow())
			{
				$this->messages[$show["text_id"]] = $show["text"];
				//echo $show["text_id"]." - ".$show["text"]."<br>\n";
			}
		}
	} // end of function get_text

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_classified_data($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$classified_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				return false;
			}
			elseif ($result->RecordCount() > 1 )
			{
				//more than one auction matches
				//$this->body .=$this->sql_query." is the query<br>\n";
				return false;
			}
			elseif ($result->RecordCount() <= 0)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				return false;
			}
			$show = $result->FetchRow();
			return $show;
		}
		else
		{
			return false;
		}

	} //end of function get_classified_data

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_expired_classified_data($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$this->sql_query = "select * from ".$this->classifieds_expired_table." where id = ".$classified_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				return false;
			}
			elseif ($result->RecordCount() > 1 )
			{
				//more than one auction matches
				//$this->body .=$this->sql_query." is the query<br>\n";
				return false;
			}
			elseif ($result->RecordCount() <= 0)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				return false;
			}
			$show = $result->FetchRow();
			return $show;
		}
		else
		{
			return false;
		}

	} //end of function get_classified_data

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_configuration_data($db)
	{
		if(!$this->configuration_data)
		{
			$this->sql_query = "SELECT * FROM ".$this->site_configuration_table;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			else
			{
				$this->configuration_data = $result->FetchRow();
			}
		}

		return true;
	} //end of function get_configuration_data

//########################################################################

	function get_image_data($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$images_to_display = array();

			$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$classified_id;
			$result = $db->Execute($this->sql_query);

			if (!$result)
			{
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				while ($show_urls = $result->FetchRow())
				{
					$this->images_to_display[$show_urls["display_order"]]["type"] = 1;
					$this->images_to_display[$show_urls["display_order"]]["id"] = $show_urls["image_id"];
					$this->images_to_display[$show_urls["display_order"]]["image_width"] = $show_urls["image_width"];
					$this->images_to_display[$show_urls["display_order"]]["image_height"] = $show_urls["image_height"];
					$this->images_to_display[$show_urls["display_order"]]["original_image_width"] = $show_urls["original_image_width"];
					$this->images_to_display[$show_urls["display_order"]]["original_image_height"] = $show_urls["original_image_height"];
					$this->images_to_display[$show_urls["display_order"]]["url"] = $show_urls["image_url"];
					$this->images_to_display[$show_urls["display_order"]]["classified_id"] = $show_urls["classified_id"];
					$this->images_to_display[$show_urls["display_order"]]["icon"] = $show_urls["icon"];
					$this->images_to_display[$show_urls["display_order"]]["mime_type"] = $show_urls["mime_type"];
				}
			}

			$this->sql_query = "select * from ".$this->images_table." where classified_id = ".$classified_id;
			$result = $db->Execute($this->sql_query);

			if (!$result)
			{
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				while ($show_uploaded = $result->FetchRow())
				{
					$this->images_to_display[$show_uploaded["display_order"]]["type"] = 2;
					$this->images_to_display[$show_uploaded["display_order"]]["id"] = $show_uploaded["image_id"];
					$this->images_to_display[$show_uploaded["display_order"]]["image_width"] = $show_uploaded["image_width"];
					$this->images_to_display[$show_uploaded["display_order"]]["image_height"] = $show_uploaded["image_height"];
					$this->images_to_display[$show_uploaded["display_order"]]["original_image_width"] = $show_uploaded["original_image_width"];
					$this->images_to_display[$show_uploaded["display_order"]]["original_image_height"] = $show_uploaded["original_image_height"];
					$this->images_to_display[$show_uploaded["display_order"]]["image_file"] = $show_uploaded["image_file"];
					$this->images_to_display[$show_uploaded["display_order"]]["classified_id"] = $show_uploaded["classified_id"];
					$this->images_to_display[$show_uploaded["display_order"]]["mime_type"] = $show_urls["mime_type"];
				}
			}
		}
		else
		{
			return false;
		}
	} //end of function get_image_data

//##########################################################################################

	function display_ad_images ($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$this->get_image_data($db,$classified_id);
			$count_images = count($this->images_to_display);
			if ((is_array($this->images_to_display)) && (count($this->images_to_display) > 0))
			{
				reset($this->images_to_display);
				$image_table =  "<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>";
				$value = current($this->images_to_display);

				do {
					$image_table .= "<tr><td align=center valign=top width=".$width_tag.">";
					$image_table .= $this->display_image_tag($value);
					$image_table .= "</td>";
					$image_table .= "</tr>";
				} while ($value = next($this->images_to_display));

				$image_table .= "</table>\n";
			}
			return $image_table;
		}
		else
		{
			//no auction id to check
			$this->error_message = urldecode($this->messages[81]);
			return false;
		}
	 } //end of function display_ad_images

//####################################################################################

	function display_image_tag($value)
	{
		if ($value["type"] == 1)
		{
		//display the url
			if (strlen(trim($value["icon"])) > 0)
			{
				$tag = "<a href=\"".$value["url"]."\">";
				$tag .=  "<img src=\"".$value["icon"]."\" border=0></a>";
			}
			else
			{
				if ($value["image_width"] != $value["original_image_width"])
					$tag = "<a href=\"javascript:winimage('../".$value["url"]."','".($value["original_image_width"]+40)."','".($value["original_image_height"]+40)."')\" class=browsing_image_links>";
				$tag .=  "<img src=../".$value["url"]." width=".$value["image_width"]." height=".$value["image_height"]." border=0>";
				if ($value["image_width"] != $value["original_image_width"])
					$tag .= "</a><br><a href=\"javascript:winimage('../".$value["url"]."','".($value["original_image_width"]+40)."','".($value["original_image_height"]+40)."')\" class=browsing_image_links>".urldecode($this->messages[339])."</a>";
			}
		}
		elseif ($value["type"] == 2)
		{
			//display the uploaded image
			if ($value["image_width"] != $value["original_image_width"])
				$tag = "<a href=\"javascript:winimage('../get_image.php?image=".$value["id"]."','".($value["original_image_width"]+40)."','".($value["original_image_height"]+40)."')\" class=browsing_image_links>";
			$tag .=  "<img src=../get_image.php?image=".$value["id"]." width=".$value["image_width"]." height=".$value["image_height"]." border=0>";
			if ($value["image_width"] != $value["original_image_width"])
				$tag .= "</a><br><a href=\"javascript:winimage('../get_image.php?image=".$value["id"]."','".($value["original_image_width"]+40)."','".($value["original_image_height"]+40)."')\" class=browsing_image_links>".urldecode($this->messages[339])."</a>";
		}
		return $tag;

	} //end of function display_image_tag


//####################################################################################

	function show_paid_invoices($db,$user_id)
	{
		//link to show ads and balance transaction attached
		//link to move paid invoice back to upaid invoice
		//transactions from date (earliest transaction in grouped transactions)
		//transactions to date (latest transaction is grouped transactions)
		//number of transactions within invoice
		//total of invoice
		if ($user_id)
		{
			//get list of current paid invoices
			$this->sql_query = "select * from ".$this->invoices_table." where user_id = ".$user_id." and date_paid != 0 order by invoice_date asc";
			//$this->body .= $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				//display table
				$this->body .= "<table width=100% cellpadding=1 cellspacing=1 border=0>";
				$this->body .= "<tr class=row_color_black>\n\t<td colspan=9 class=medium_font_light>Paid Invoices</td></tr>";
				$this->body .= "<tr class=row_color_black><td class=small_font_light>invoice # </td>";
				$this->body .= "<td class=small_font_light>invoice date </td>";
				$this->body .= "<td class=small_font_light>date paid </td>";
				$this->body .= "<td class=small_font_light>transactions from </td>";
				$this->body .= "<td class=small_font_light>transactions to </td>";
				$this->body .= "<td class=small_font_light># of transactions </td>";
				$this->body .= "<td class=small_font_light>total </td>";
				$this->body .= "<td class=small_font_light> &nbsp; </td>";
				$this->body .= "<td class=small_font_light> &nbsp; </td>";
				$this->body .= "</tr>";

				$this->row_count = 0;
				while ($show = $result->FetchRow())
				{
					$this->body .= "<tr class=".$this->get_row_color()."><td class=small_font>\n\t".$show["invoice_id"]."</td>";
					$this->body .= "<td class=small_font>\n\t".date("M d,Y G:i - l",$show["invoice_date"])."</td>";
					$this->body .= "<td class=small_font>\n\t".date("M d,Y G:i - l",$show["date_paid"])."</td>";
					$this->sql_query = "select * from ".$this->balance_transactions.",".$this->classifieds_table." where
						".$this->balance_transactions.".ad_id = ".$this->classifieds_table.".id and
						".$this->balance_transactions.".invoice_id = ".$show["invoice_id"]." order by ".$this->classifieds_table.".date asc limit 1";
					$earliest_result = $db->Execute($this->sql_query);
					if (!$earliest_result)
					{
						$this->body .= $this->sql_query."<br>";
						$this->body .= "error getting earliest transaction 1";
						return false;
					}
					elseif ($earliest_result->RecordCount() == 1)
					{
						$show_earliest = $earliest_result->FetchRow();
						$earliest_date = $show_earliest["date"];
					}
					else
					{
						//$this->body .= $this->sql_query."<br>";
						//$this->body .= "error getting earliest transaction";

						if ($this->debug)
						{
							$this->body .= $this->sql_query."<br>";
							$this->body .= "error getting earliest transaction 2";
						}
						$this->sql_query = "select * from ".$this->balance_transactions.",".$this->classifieds_expired_table." where
							".$this->balance_transactions.".ad_id = ".$this->classifieds_expired_table.".id and
							".$this->balance_transactions.".invoice_id = ".$show["invoice_id"]." order by ".$this->classifieds_expired_table.".date asc limit 1";
						$expired_earliest_result = $db->Execute($this->sql_query);
						if (!$expired_earliest_result)
						{
							if ($this->debug)
							{
								$this->body .= $this->sql_query."<br>";
								$this->body .= "error getting earliest transaction 3";
							}
							$earliest_date = 0;
						}
						elseif ($expired_earliest_result->RecordCount() == 1)
						{
							$show_earliest = $expired_earliest_result->FetchRow();
							$earliest_date = $show_earliest["date"];
						}
						else
						{
							//cannot find auction anywhere...in current or archived auctions
							//manually deleted from the database
							if ($this->debug)
							{
								$this->body .= $this->sql_query."<br>";
								$this->body .= "error getting earliest transaction 4";
							}
							$earliest_date = 0;
						}

					}
					if ($earliest_date)
						$body .= "<td class=small_font>".date("M d,Y G:i - l",$earliest_date)."</td>";
					else
						$body .= "<td class=small_font>not found</td>";

					$this->sql_query = "select * from ".$this->balance_transactions.",".$this->classifieds_table." where
						".$this->balance_transactions.".ad_id = ".$this->classifieds_table.".id and
						".$this->balance_transactions.".invoice_id = ".$show["invoice_id"]." order by ".$this->classifieds_table.".date desc limit 1";
					$latest_result = $db->Execute($this->sql_query);
					if (!$latest_result)
					{
						$this->body .= $this->sql_query."<br>";
						$this->body .= "error getting earliest transaction 5";
						return false;
					}
					elseif ($latest_result->RecordCount() == 1)
					{
						$show_latest = $latest_result->FetchRow();
						$latest_date = $show_latest["date"];
					}
					else
					{
						//$this->body .= $this->sql_query."<br>";
						//$this->body .= "error getting earliest transaction";

						$this->sql_query = "select * from ".$this->balance_transactions.",".$this->classifieds_expired_table." where
							".$this->balance_transactions.".ad_id = ".$this->classifieds_expired_table.".id and
							".$this->balance_transactions.".invoice_id = ".$show["invoice_id"]." order by ".$this->classifieds_expired_table.".date desc limit 1";
						$expired_latest_result = $db->Execute($this->sql_query);
						if (!$expired_latest_result)
						{
							if ($this->debug)
							{
								$this->body .= $this->sql_query."<br>";
								$this->body .= "error getting earliest transaction 6";
							}
							return false;
						}
						elseif ($expired_latest_result->RecordCount() == 1)
						{
							$show_latest = $expired_latest_result->FetchRow();
							$latest_date = $show_latest["date"];
						}
						elseif ($expired_latest_result->RecordCount() == 0 )
						{
							$latest_date = 0;
						}
						else
						{
							$latest_date = 0;
						}

					}
					if ($latest_date)
						$body .= "<td class=small_font>".date("M d,Y G:i - l",$latest_date)."</td>";
					else
						$body .= "<td class=small_font>not found</td>";

					$this->sql_query = "select count(*) as total from ".$this->balance_transactions." where invoice_id = ".$show["invoice_id"];
					$count_result = $db->Execute($this->sql_query);
					if (!$count_result)
					{
						$this->body .= $this->sql_query."<br>";
						$this->body .= "error getting earliest transaction 7";
						return false;
					}
					elseif ($count_result->RecordCount() == 1)
					{
						$show_count = $count_result->FetchRow();
						$transaction_count = $show_count["total"];
					}
					else
					{
						$this->body .= $this->sql_query."<br>";
						$this->body .= "error getting number of  transaction 8";
					}
					$this->body .= "<td class=small_font>\n\t".$transaction_count."</td>";

					$this->sql_query = "select sum(amount) as total_of_invoice from ".$this->balance_transactions." where invoice_id = ".$show["invoice_id"];
					if ($this->debug) $this->body .= $this->sql_query."<br>";
					$total_result = $db->Execute($this->sql_query);
					if (!$total_result)
					{
						$this->body .= $this->sql_query."<br>";
						$this->body .= "error getting earliest transaction 9";
						return false;
					}
					elseif ($total_result->RecordCount() == 1)
					{
						$show_total = $total_result->FetchRow();
						$invoice_total = $show_total["total_of_invoice"];
					}
					else
					{
						$this->body .= $this->sql_query."<br>";
						$this->body .= "error getting number of  transaction 10";
					}
					$this->body .= "<td><div nowrap><span class=small_font>".$this->configuration_data["precurrency"]." ".sprintf("%01.2f",$invoice_total)." ".$this->configuration_data["postcurrency"]."</span></div></td>";
					$this->body .= "<td><a href=index.php?a=78&z=1&b=".$show["invoice_id"]."&c=".$user_id."><span class=small_font>make unpaid</a></span</td>";
					$this->body .= "<td><a href=index.php?a=78&z=8&b=".$show["invoice_id"]."&c=".$user_id."><span class=small_font>details</a></span></td>";
					$this->body .= "</tr>";

					$this->row_count++;
				} //end of while

				$this->body .= "</table>";
			}
			else
			{
				//display nothing
			}
		}
		else
		{

		}
	} //end of function show_paid_invoices

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_unpaid_invoices($db,$user_id)
	{
		//link to show ads and balance transactions attached
		//link to set invoice has been paid
		//transactions from date (earliest transaction in grouped transactions)
		//transactions to date (latest transaction is grouped transactions)
		//number of transactions within invoice
		//total of invoice
		if ($user_id)
		{
			//get list of current unpaid invoices
			$this->sql_query = "select * from ".$this->invoices_table." where user_id = ".$user_id." and date_paid = 0 order by invoice_date asc";
			$result = $db->Execute($this->sql_query);
			//$this->body .= $this->sql_query."<br>\n";
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				//display table
				$this->body .= "<table width=100% cellpadding=1 cellspacing=1 border=0>";
				$this->body .= "<tr class=row_color_black>\n\t<td colspan=8 class=medium_font_light>Unpaid Invoices</td></tr>";
				$this->body .= "<tr class=row_color_black><td class=small_font_light> invoice # </td>";
				$this->body .= "<td class=small_font_light> invoice date </td>";
				$this->body .= "<td class=small_font_light> transactions from </td>";
				$this->body .= "<td class=small_font_light> transactions to </td>";
				$this->body .= "<td class=small_font_light> # of transactions  </td>";
				$this->body .= "<td class=small_font_light> total </td>";
				$this->body .= "<td class=small_font_light> &nbsp; </td>";
				$this->body .= "<td class=small_font_light> &nbsp; </td>";
				$this->body .= "</tr>";

				$this->row_count = 0;
				while ($show = $result->FetchRow())
				{
					$this->body .= "<tr class=".$this->get_row_color()."><td class=small_font>\n\t".$show["invoice_id"]."</td>";
					$this->body .= "<td class=small_font>\n\t".date("M d,Y G:i - l",$show["invoice_date"])."</td>";
					$this->sql_query = "select * from ".$this->balance_transactions.",".$this->classifieds_table." where
						".$this->balance_transactions.".ad_id = ".$this->classifieds_table.".id and
						".$this->balance_transactions.".invoice_id = ".$show["invoice_id"]." order by ".$this->classifieds_table.".date asc limit 1";
					$earliest_result = $db->Execute($this->sql_query);
					if (!$earliest_result)
					{
						$this->body .= $this->sql_query."<br>";
						$this->body .= "error getting earliest transaction 11";
						return false;
					}
					elseif ($earliest_result->RecordCount() == 1)
					{
						$show_earliest = $earliest_result->FetchRow();
						$earliest_date = $show_earliest["date"];
					}
					else
					{
						if ($this->debug)
						{
							$this->body .= $this->sql_query."<br>";
							$this->body .= "error getting earliest transaction 12";
						}
						$earliest_date = 0;
					}
					if ($earliest_date)
						$this->body .= "<td class=small_font>".date("M d,Y G:i - l",$earliest_date)."</td>";
					else
						$this->body .= "<td class=small_font>not found</td>";
					//$this->body .= "<td class=small_font>\n\t".date("M d,Y G:i - l",$earliest_date)."</td>";

					$this->sql_query = "select * from ".$this->balance_transactions.",".$this->classifieds_table." where
						".$this->balance_transactions.".ad_id = ".$this->classifieds_table.".id and
						".$this->balance_transactions.".invoice_id = ".$show["invoice_id"]." order by ".$this->classifieds_table.".date desc limit 1";
					$latest_result = $db->Execute($this->sql_query);
					if (!$latest_result)
					{
						$this->body .= $this->sql_query."<br>";
						$this->body .= "error getting earliest transaction 13";
						return false;
					}
					elseif ($latest_result->RecordCount() == 1)
					{
						$show_latest = $latest_result->FetchRow();
						$latest_date = $show_latest["date"];
					}
					else
					{
						if ($this->debug)
						{
							$this->body .= $this->sql_query."<br>";
							$this->body .= "error getting earliest transaction 14";
						}
						$latest_date = 0;
					}
					if ($latest_date)
						$this->body .= "<td class=small_font>".date("M d,Y G:i - l",$latest_date)."</td>";
					else
						$this->body .= "<td class=small_font>not found</td>";
					//$this->body .= "<td class=small_font>\n\t".date("M d,Y G:i - l",$latest_date)."</td>";

					$this->sql_query = "select count(*) as total from ".$this->balance_transactions." where invoice_id = ".$show["invoice_id"];
					$count_result = $db->Execute($this->sql_query);
					if (!$count_result)
					{
						$this->body .= $this->sql_query."<br>";
						$this->body .= "error getting earliest transaction 15";
						return false;
					}
					elseif ($count_result->RecordCount() == 1)
					{
						$show_count = $count_result->FetchRow();
						$transaction_count = $show_count["total"];
					}
					else
					{
						$this->body .= $this->sql_query."<br>";
						$this->body .= "error getting number of  transaction 16";
					}
					$this->body .= "<td class=small_font>\n\t".$transaction_count."</td>";

					$this->sql_query = "select sum(amount) as total_of_invoice from ".$this->balance_transactions." where invoice_id = ".$show["invoice_id"];
					//$this->body .= $this->sql_query."<br>";
					$total_result = $db->Execute($this->sql_query);
					if (!$total_result)
					{
						$this->body .= $this->sql_query."<br>";
						$this->body .= "error getting earliest transaction 17";
						return false;
					}
					elseif ($total_result->RecordCount() == 1)
					{
						$show_total = $total_result->FetchRow();
						$invoice_total = $show_total["total_of_invoice"];
					}
					else
					{
						$this->body .= $this->sql_query."<br>";
						$this->body .= "error getting number of  transaction 18";
					}
					$this->body .= "<td><div nowrap><span class=small_font>".$this->configuration_data["precurrency"]." ".sprintf("%01.2f",$invoice_total)." ".$this->configuration_data["postcurrency"]."</span></div></td>";
					$this->body .= "<td><a href=index.php?a=78&z=2&b=".$show["invoice_id"]."&c=".$user_id."><span class=small_font>make paid</a></td>";
					$this->body .= "<td><a href=index.php?a=78&z=8&b=".$show["invoice_id"]."&c=".$user_id."><span class=small_font>details</a></td>";

					$this->body .= "</tr>";

					$this->row_count++;
				} //end of while

				$this->body .= "</table>";
			}
			else
			{
				//display nothing
			}
		}
		else
		{

		}
	} //end of function show_unpaid_invoices

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_tooltip($text, $style)
	{
		//return "<a href=\"javascript:void(0)\" onClick=\"stm(Text[".$text."],Style[".$style."])\" onMouseOut=\"htm()\"><img border=0 src=admin_images/help.gif></a>";
		return "<a href=\"javascript:void(0)\" onMouseOver=\"stm(Text[".$text."],Style[".$style."])\" onMouseOut=\"mig_hide(1)\"><img border=0 src=admin_images/help.gif></a>";

	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function search_text_form($skip=0)
	{
		if (!$skip)
		{
			//skip this if the javascript is already on the page (don't want to duplicate it)
			$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
			// Set title and text for tooltip
			$this->body .= "Text[1] = [\"search for text\", \"Search for text within your database here.  Choose whether to display partial text showing only up to 50 characters surrounding the first occurrence of your key word or phrase, or to display the entire text (template, html file,page module, etc.) highlighting all occurrences of the key word or phrase. Check <b>exact phrase match?</b> to the right if you want to search for text exactly as you type it.  You must surround word(s) with spaces yourself when performing an exact phrase match. To perform sub-word searches, simply enclose the word(s) in quotes <b>individually</b>. YOU MUST SURROUND WORDS THAT HAVE SPECIAL CHARACTERS WITH QUOTES SUCH AS UNDERSCORES AND ANGLE BRACKETS<BR>(i.e.&lt;MODULE_TITLE&gt;)\"]\n";
			//".$this->show_tooltip(1,1)."

			// Set style for tooltip
			//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
			$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
			$this->body .= "var TipId = \"tiplayer\"\n";
			$this->body .= "var FiltersEnabled = 1\n";
			$this->body .= "mig_clay()\n";
			$this->body .= "</script>";
		}
		$this->body .= "
			<script type=\"text/javascript\">
				function make_exact_".$skip."()
				{
					check_element = document.getElementById('exact_".$skip."');
					input_element = document.getElementById('inbox_".$skip."');
					check = (check_element.checked == true) ? 0 : 1;
					if (check==0)
					{
						check_element.checked = true;
						re = /([\"'`])(.+?)\\1/i;
						phraseList = input_element.value.split(re)
						new_phrase = new Array
						for(i=0;i<phraseList.length;i++)
						{
							match = re.exec(phraseList[i])
							new_phrase[i] = RegExp.$2;
							input_element.value = input_element.value.replace(re,new_phrase[i]);
						}
						input_element.value = '\"'+input_element.value+'\"';
					}
					else
					{
						check_element.checked = false;
					}
					document.getElementById('exact_".!$skip."').checked = false;
					document.getElementById('inbox_".!$skip."').value = '';
				}
			</script>";

		$this->body .= "

<tr height=12><td>&nbsp;</td></tr>

			<tr>
				<td colspan=100%>
					<table width=100% border=0>
						<tr>
							<td colspan=100%>";
		if (!$this->admin_demo())
			$this->body .= "	<form method=post action=\"index.php?a=14&y=1\">";
		$this->body .= "			<table width=100% border=0>
										<tr class=row_color1>
											<td class=medium_font align=center>
												<table cellpadding=0 cellspacing=0 border=0 width=100%>
													<tr>
														<td class=medium_font align=right>
															<b>search for text:</b>".$this->show_tooltip(1,1)."
														</td>
														<td class=medium_font align=left width=45%>
															<input id=inbox_".$skip." onchange=\"javascript:make_exact_".$skip."();\" size=50 type=text name=search_text>
														</td>
														<td valign=top class=medium_font align=left>
															<input id=exact_".$skip." onclick=\"javascript:make_exact_".$skip."();\" type=checkbox value=1>
																<b>exact phrase match?</b>
														</td>
													</tr>
													<tr>
														<td>&nbsp;</td>
														<td class=medium_font align=left colspan=2>
															<input type=radio name=search_type value=0 checked>
																show the first occurrence only
															<input type=radio name=search_type value=1>
																show all occurrences
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr class=row_color1>
											<td align=center><br><input type=submit value=\"Search\"><br><br></td>
										</tr>
									</table>
								</form>
							</td>
						</tr>
					</table>
				</td>
			</tr>";
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 * This function looks inside of $content whether it came from a template or a module
	 * It finds all the tags in it and all of the tags inside of those tags if it is an HTML module
	 * and continues to do this recursively until all embedded HTML has been examined.
	 * Once found, the function then attaches all of these tags to the page the original module is
	 * attached to or to the page that the template is attached to (if it is attached to one).
	 *
	 * @param ADODB $db
	 * @param int $id
	 * @param int $is_template
	 * @param string $content (optional, but will run faster if passed in)
	 * 			template_code | module_logged_in_html/module_logged_out_html
	 *
	 * @return false='error' | true='no problems' (may or may not have attached anything)
	 */

	function AttachEmbeddedModules($db, $id, $is_template=0 ,$content='')
	{
		if ($this->debug_attach_modules)
		{
			echo (($is_template) ? 'template_id = ' : 'module_id = ').$id.'<br>';
		}
		if (!isset($this->all_modules))
		{
			//get all module tags BUT NOT MORE THAN ONCE
			$this->sql_query = "SELECT module_replace_tag,name,page_id FROM ".$this->pages_table." WHERE module = 1";
			$result = $db->Execute($this->sql_query);
			if(!$result)
				return false;
			$this->all_modules = $result->GetArray();
		}
		if (isset($this->all_modules))
		{
			//reset the pointer to the beginning of the array
			reset($this->all_modules);
			if ($is_template)
			{
				//content is a template
				$this->sql_query = "select page_id from ".$this->pages_templates_table." where template_id = ".$id;
				$templates_result = $db->Execute($this->sql_query);
				if(!$templates_result)
					return false;
				$attached_page_ids = $templates_result->GetArray();
				if (strlen($content)<=0)
				{
					$template_info = $this->get_template($db, $id);
					$content = $template_info['template_code'];
				}
			}
			else
			{
				//content is a module
				//get all page ids that this module is attached to
				$this->sql_query = "SELECT page_id FROM ".$this->pages_modules_table." WHERE module_id = ".$id;
				$modules_result = $db->Execute($this->sql_query);
				if(!$modules_result)
					return false;
				$attached_page_ids = $modules_result->GetArray();
				if (strlen($content)<=0)
				{
					$module_info = $this->get_page($db, $id);
					$content = $module_info['module_logged_in_html'].$module_info['module_logged_out_html'];
				}
			}
			$found_tag_ids = array();
			foreach($this->all_modules as $key => $value)
			{
				//Check to see if tag is in the $content
				if(strpos($content, $value['module_replace_tag']) !== false)
					$found_tag_ids[] .= $value['page_id'];
			}
			if (isset($found_tag_ids))
			{
				foreach ($found_tag_ids as $embedded_mod_id)
				{
					if (isset($attached_page_ids))
					{
						$module_page = array();
						foreach ($attached_page_ids as $key => $value)
						{
							// Module found so lets attach it and display it
							$this->sql_query = "SELECT * FROM ".$this->pages_modules_table." WHERE
								module_id = $embedded_mod_id AND
								page_id = ".$value['page_id'];
							$result_check = $db->Execute($this->sql_query);
							if(!$result_check)
							{
								return false;
							}
							if($result_check->RecordCount() == 0)
							{
								$module_page[$value['page_id']] = $embedded_mod_id;
							}
						}
					}
				}
				if (isset($module_page))
				{
					asort($module_page);
					foreach ($module_page as $page_id => $module_id)
					{
						$time = $this->shifted_time() + $this->time_shift_i++;
						$this->sql_query = "INSERT INTO ".$this->pages_modules_table." (module_id, page_id, time)
							VALUES ($module_id, $page_id, $time)";
						$result_check = $db->Execute($this->sql_query);
						if(!$result_check)
							return false;
						$this->sql_query = "SELECT module_logged_in_html,module_logged_out_html FROM ".$this->pages_table." WHERE
							module = 1 AND
							page_id = $module_id";
						$result = $db->Execute($this->sql_query);
						if(!$result)
							return false;
						elseif ($result->RecordCount()==1)
						{
							$sub_content = $result->FetchRow();
							$sub_content = $sub_content['module_logged_in_html'].$sub_content['module_logged_out_html'];
							//attach embedded modules RECURSIVELY
							if (strlen($sub_content)>0)
								$this->AttachEmbeddedModules($db, $module_id, 0, $sub_content);
						}
					}
				}
			}
		}
		return true;
	} // End of function AttachEmbeddedModules

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_page($db)
	{
		$this->template = file_get_contents("template.html");

		$this->template = str_replace("<<TITLE>>", $this->title, $this->template);
		$this->template = str_replace("<<DESCRIPTION>>", $this->description, $this->template);
		$this->template = str_replace("<<MAINBODY>>", $this->body, $this->template);
		$this->template = str_replace("<<IMAGE>>", $this->choose_header_image(), $this->template);

		// Build the footer
		$this->admin_header($db, 1);
		$this->admin_footer($db);

		if($this->debug)
		{
			echo strlen($this->header_html).' is header<br>';
			echo strlen($this->template).' is the template<br>';
			echo strlen($this->footer_html).' is the footer<br>';
		}

		echo $this->header_html.$this->template.$this->footer_html;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function special_chars($t=0)
	{
		$t = preg_replace("/&(?!#[0-9]+;)/s", '&amp;', $t );
		$t = str_replace( "<", "&lt;"  , $t );
		$t = str_replace( ">", "&gt;"  , $t );
		$t = str_replace( '"', "&quot;", $t );
		return $t;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function choose_header_image()
	{
		/*
		 *	To Add extra images:
		 *	Add a new array below and make the first element of it
		 *	the complete relative path to the image.  Then for the rest
		 *	of the elements simply put all the $_REQUEST["a"] values.
		 *	Then replace the name of the array with the template below
		 *	and copy and paste it at the end of the list below.
		 *
		 *	To Add extra pages:
		 *	Add the $_REQUEST["a"] value of the page to the correct
		 *	array below.  Make sure it is at the end of the array list,
		 *	NOT at the beginning.
		 */
		$site_configuration = array("admin_images/menu_siteconfig.gif", 28, 100000, 24, 15, 68);
		$registration_configuration = array("admin_images/menu_regconfig.gif", 26, 67, 34);
		$listing_configuration = array("admin_images/menu_config.gif", 23, 32, 66);
		$categories = array("admin_images/menu_cats.gif", 1, 2, 4, 5, 6, 7, 8, 9, 12, 13, 72, 73, 10, 11, 70);
		$geographic = array("admin_images/menu_geo.gif", 21, 22);
		$users = array("admin_images/menu_users.gif", 36, 19, 16, 17, 47, 55, 60, 61, 62, 63, 64, 69, 54, 18, 82, 53, 111);
		$auction_pricing = array("admin_images/menu_aucpricing.gif", 37, 75);
		$payments = array("admin_images/menu_payments.gif", 39);
		$transactions = array("admin_images/menu_trans.gif", 40, 71, 77, 80, 41, 42, 78);
		$pages_management = array("admin_images/menu_pages.gif", 44, 38, 14, 109, 99);
		$pages_modules = array("admin_images/menu_pagemod.gif", 74, 79, 76, 74);
		$templates = array("admin_images/menu_temp.gif", 45);
		$languages = array("admin_images/menu_lang.gif", 30, 29, 31);
		$admin_tools = array("admin_images/menu_admintools.gif", 25, 108, 35, 51);
		$feedback = array("admin_images/menu_feedback.gif", 110);

		// Special cases that use the "r" variable
		$auction_configuration = array("admin_images/menu_aucconfig.gif", 20, 21);

		// Check for external modules setting their admin_icon variable
		if($this->admin_icon)
			return $this->admin_icon;

		/*
		 	Template for regular cases:
			if(in_array($_REQUEST["a"], $array_name))
				return $array_name[0];

			Template for special cases:
			NOTE: Please replace X with the correct request variable
			if(in_array($_REQUEST["X"], $array_name))
				return $array_name[0];
		 */

		// First check for special cases of "r" variable
		if(in_array($_REQUEST["r"], $auction_configuration))
			return $auction_configuration[0];

		// Regular cases
		if(in_array($_REQUEST["a"], $site_configuration))
			return $site_configuration[0];
		if(in_array($_REQUEST["a"], $registration_configuration))
			return $registration_configuration[0];
		if(in_array($_REQUEST["a"], $listing_configuration))
			return $listing_configuration[0];
		if(in_array($_REQUEST["a"], $categories))
			return $categories[0];
		if(in_array($_REQUEST["a"], $users))
			return $users[0];
		if(in_array($_REQUEST["a"], $auction_pricing))
			return $auction_pricing[0];
		if(in_array($_REQUEST["a"], $payments))
			return $payments[0];
		if(in_array($_REQUEST["a"], $transactions))
			return $transactions[0];
		if(in_array($_REQUEST["a"], $pages_management))
			return $pages_management[0];
		if(in_array($_REQUEST["a"], $pages_modules))
			return $pages_modules[0];
		if(in_array($_REQUEST["a"], $templates))
			return $templates[0];
		if(in_array($_REQUEST["a"], $languages))
			return $languages[0];
		if(in_array($_REQUEST["a"], $admin_tools))
			return $admin_tools[0];
		if(in_array($_REQUEST["a"], $geographic))
			return $geographic[0];
		if(in_array($_REQUEST["a"], $feedback))
			return $feedback[0];
	}

//#######################################################################

	function switch_product()
	{
		$this->product_configuration->switch_product();
	} // end function switch_product

//########################################################################

	function is_class_auctions()
	{
		return $this->product_configuration->is_class_auctions();
	} // end function is_class_auctions

//########################################################################

	function is_auctions()
	{
		return $this->product_configuration->is_auctions();
	}

//########################################################################

	function is_classifieds()
	{
		return $this->product_configuration->is_classifieds();
	}

//########################################################################

	function set_type($type)
	{
		$this->product_configuration->set_type($type);
	}

//########################################################################

	function get_current_table()
	{
		return $this->product_configuration->current_table;
	}

//########################################################################

	function get_current_url()
	{
		return $this->product_configuration->current_url;
	}

//########################################################################

	function get_current_file_name()
	{
		return $this->product_configuration->current_file_name;
	}

//########################################################################

	function get_current_ssl_url()
	{
		return $this->product_configuration->current_ssl_url;
	}

//#######################################################################

	function get_configuration_table()
	{
		return $this->product_configuration->current_configuration_table;
	}

//#######################################################################

	function set_shifted_time($db)
	{
		$this->sql_query = "select time_shift from ".$this->site_configuration_table;
		$result = $db->Execute($this->sql_query);

		if(!$result)
		{
			return false;
		}
		else
		{
			$time = $result->FetchRow();
			$this->time_shift = $time['time_shift'];
		}

		return true;
	}

//#######################################################################

	function shifted_time($current_time=0)
	{
		if ($current_time)
		{
			$time = $current_time + (3600 * $this->time_shift);
		}
		else
		{
			$time = time() + (3600 * $this->time_shift);
		}
		return $time;
	}

//#######################################################################

	function subscription_period_dropdown($db,$present_value=0,$name=0)
	{
		if ($name)
		{
			$this->sql_query = "select * from  ".$this->choices_table." where type_of_choice = 9 order by display_order";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() ==0)
			{
				$query = array();
				$query[0] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '4 days', '4', 4, 4)";
				$query[1] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '1 day', '1', 1, 1)";
				$query[2] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '2 days', '2', 2, 2)";
				$query[3] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '3 days', '3', 3, 3)";
				$query[4] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '5 days', '5', 5, 5)";
				$query[5] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '7 days', '7', 7, 7)";
				$query[6] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '10 days', '10', 10, 10)";
				$query[7] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '14 days', '14', 14, 14)";
				$query[8] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '28 days', '28', 28, 28)";
				$query[9] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '30 days', '30', 30, 30)";
				$query[10] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '45 days', '45', 45, 45)";
				$query[11] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '60 days', '60', 60, 60)";
				$query[12] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '90 days', '90', 90, 90)";
				$query[13] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '120 days', '120', 120, 120)";
				$query[14] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '150 days', '150', 150, 127)";
				$query[15] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '180 days', '180', 180, 127)";
				$query[16] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '270 days', '270', 270, 127)";
				$query[17] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '365 days', '365', 365, 127)";
				$query[18] = "INSERT INTO ".$this->choices_table." ( type_of_choice, display_value, value, numeric_value, display_order) VALUES ( 9, '0 days', '0', 0, 0)";

				foreach ($query as $value)
				{
					$result = $db->Execute($value);
					//echo $value."<bR>\n";
					if (!$result)
					{
						//echo $value."<bR>\n";
						$this->error_message = $this->internal_error_message;
						return false;
					}
				}

				$this->sql_query = "select * from  ".$this->choices_table." where type_of_choice = 9 order by display_order";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}
				$this->body .= "<select name=\"".$name."\">\n\t\t";
				$this->body .= "<option value=0>None</option>";
				while ($show = $result->FetchRow())
				{
					$this->body .= "<option value=\"".$show["value"]."\" ";
					if ($show["value"] == $present_value)
						$this->body .= "selected";
					$this->body .= ">".$show["display_value"]."</option>\n\t\t";
				}
				$this->body .= "</select>\n\t";
				return true;
			}
			elseif ($result->RecordCount() > 0)
			{
				$this->body .= "<select name=\"".$name."\">\n\t\t";

				while ($show = $result->FetchRow())
				{
					$this->body .= "<option value=\"".$show["value"]."\" ";
					if ($show["value"] == $present_value)
						$this->body .= "selected";
					$this->body .= ">".$show["display_value"]."</option>\n\t\t";
				}
				$this->body .= "</select>\n\t";
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	} //end of function subscription_period_dropdown

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function charge_select_box($present_value=0,$name=0)
	{
		if ($name)
		{
			if (strchr($present_value,"."))
			{
				$split_value = explode(".",$present_value);
				$dollars = $split_value[0];
				$cents = $split_value[1];
			}
			else
			{
				$dollars = $present_value;
				$cents = 0;
			}

			$this->body .= "$<input align=right type=text name=\"".$name."[0]\" size=4 maxsize=4 value=".$dollars.">\n\t\t";
			$this->body .= ".<select name=\"".$name."[1]\">\n\t\t";
			for ($i=0;$i<100;$i++)
			{
				$this->body .= "<option ";
				if ($i == $cents)
					$this->body .= "selected";
				$this->body .= ">".sprintf("%02d",$i)."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t";
			return true;
		}
		else
		{
			return false;
		}
	} //end of function charge_select_box

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function date_dropdown ($current_date,$name=0)
	{
		if ($name)
		{
			//echo $name." is the name<br>\n";
			// Take care of the case where $current_date isnt set
			if($current_date > 0)
			{
				$date = getdate($current_date);
			}
			else
			{
				$date = getdate();
			}

			//get the current year
			$current_year = getdate();

			$this->body .= "<select name=".$name."[month]>\n\t\t";
			for ($i=1;$i < 13;$i++)
			{
				$this->body .= "<option ";
				if ($date["mon"] == $i)
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select><select name=".$name."[day]>\n\t\t";
			for ($i=1;$i < 32;$i++)
			{
				$this->body .= "<option ";
				if ($date["mday"] == $i)
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t";
			$this->body .= "<select name=".$name."[year]>\n\t\t";
			for ($i=$current_year["year"];$i < $date["year"]+50;$i++)
			{
				$this->body .= "<option ";
				if ($date["year"] == $i)
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>";

			return true;
		}
		else
		{
			return false;
		}
	} //end of function date_dropdown

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function admin_demo()
	{
		include("../config.php");
		if ($demo == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_check($data, $ad_template_code=0, $auction_template_code=0)
	{
		$this->row_count = 0;
		foreach($data as $key => $value)
		{
			// Check for tags that shouldnt be
			if($this->is_auctions())
			{
				if(stristr($key, "classified") !== false)
					continue;
			}
			elseif($this->is_classifieds() !== false)
			{
				if(stristr($key, "auction") !== false)
					continue;
			}

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;".$key."&gt;&gt;<br>";
			$this->body .= $value."\n\t</td>\n";
			if($ad_template_code)
			{
				$this->body .= "<td align=center valign=middle width=25%>";
				if(strstr($ad_template_code, "<<".$key.">>"))
					$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
				else
					$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
				$this->body .= "</td>\n\t";
			}
			if($auction_template_code)
			{
				$this->body .= "<td align=center valign=middle width=25%>";
				if(strstr($auction_template_code, "<<".$key.">>"))
					$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
				else
					$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
				$this->body .= "</td>\n\t";
			}
			$this->body .= "</tr>\n";
			$this->row_count++;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_for_any_tax($db)
	{
		$sql_query = "SELECT s.tax,c.tax FROM ".$this->states_table." AS s,".$this->countries_table." AS c
			WHERE s.tax > 0 OR
			c.tax > 0";
		$result = $db->Execute($sql_query);
		if (!$result)
			return false;
		if ($result->RecordCount()>0)
		{
			$sql_query = "UPDATE ".$this->site_configuration_table." SET charge_tax_by = 1";
			$result = $db->Execute($sql_query);
			if (!$result)
				return false;
		}
		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function include_ajax()
	{
		// Gives you some bathroom cleaner
		$this->extra_header_html .= "<script type=\"text/javascript\" src=\"ajax.js\"></script>\n";
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_ad_configuration($db)
	{
		if(!$this->ad_configuration_data)
		{
			$this->sql_query = "select * from ".$this->ad_configuration_table;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->messages[57];
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				if ($by_array == 0)
				{
					$this->ad_configuration_data = $result->FetchNextObject();
				}
				else
				{
					$this->ad_configuration_data = $result->FetchRow();
				}
				return true;
			}
			else
			{
				$this->html_disallowed_list = 0;
				return true;
			}
		}
	} //function get_ad_configuration

//########################################################################
} //end of class Site
?>
