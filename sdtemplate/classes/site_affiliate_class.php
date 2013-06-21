<?//site_affiliate_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Site_affiliate
{
	//tables within the database
	var $classifieds_table = "geodesic_classifieds";
	var $classifieds_expired_table = "geodesic_classifieds_expired";
	var $classified_sell_questions_table = "geodesic_classifieds_sell_questions";
	var $classified_extra_table = "geodesic_classifieds_ads_extra";
	var $categories_table = "geodesic_categories";
	var $categories_languages_table = "geodesic_classifieds_categories_languages";
	var $logins_table = "geodesic_logins";
	var $configuration_table = "geodesic_configuration";
	var $sell_choices_table = "geodesic_classifieds_sell_question_choices";
	var $sell_choices_types_table = "geodesic_classifieds_sell_question_types";
	var $questions_table = "geodesic_classifieds_sell_questions";
	var $states_table = "geodesic_states";
	var $countries_table = "geodesic_countries";
	var $text_message_table = "geodesic_text_messages";
	var $text_languages_table = "geodesic_text_languages";
	var $text_languages_messages_table = "geodesic_text_languages_messages";
	var $text_page_table = "geodesic_text_pages";
	var $text_subpages_table = "geodesic_text_subpages";
	var $confirm_table = "geodesic_confirm";
	var $confirm_email_table = "geodesic_confirm_email";
	var $userdata_table = "geodesic_userdata";
	var $badwords_table = "geodesic_text_badwords";
	var $ad_configuration_table = "geodesic_classifieds_ad_configuration";
	var $userdata_history_table = "geodesic_userdata_history";
	var $html_allowed_table = "geodesic_html_allowed";
	var $ad_filter_table = "geodesic_classifieds_ad_filter";
	var $ad_filter_categories_table = "geodesic_classifieds_ad_filter_categories";
	var $user_communications_table = "geodesic_user_communications";
	var $site_configuration_table = "geodesic_classifieds_configuration";
	var $choices_table = "geodesic_choices";
	var $images_table = "geodesic_classifieds_images";
	var $images_urls_table = "geodesic_classifieds_images_urls";
	var $favorites_table = "geodesic_classifieds_favorites";
	var $extra_pages_table = "geodesic_classifieds_extra_pages";
	var $file_types_table = "geodesic_file_types";
	var $groups_table = "geodesic_groups";
	var $group_questions_table = "geodesic_classifieds_group_questions";
	var $price_plans_table = "geodesic_classifieds_price_plans";
	var $price_plans_categories_table = "geodesic_classifieds_price_plans_categories";
	var $user_groups_price_plans_table = "geodesic_user_groups_price_plans";
	var $expirations_table = "geodesic_classifieds_expirations";
	var $user_credits_table = "geodesic_classifieds_user_credits";
	var $payment_types_table = "geodesic_payment_choices";
	var $user_subscriptions_table = "geodesic_classifieds_user_subscriptions";
	var $font_page_table = "geodesic_font_pages";
	var $font_sub_page_table = "geodesic_font_subpages";
	var $font_element_table = "geodesic_font_elements";
	var $paypal_transaction_table = "geodesic_paypal_transactions";
	var $cc_choices = "geodesic_credit_card_choices";
	var $sell_table = "geodesic_classifieds_sell_session";
	var $sell_images_table = "geodesic_classifieds_sell_session_images";
	var $sell_questions_table = "geodesic_classifieds_sell_session_questions";
	var $registration_table = "geodesic_registration_session";
	var $banners_table = "geodesic_banners";
	var $worldpay_configuration_table = "geodesic_worldpay_settings";
	var $worldpay_transaction_table = "geodesic_worldpay_transactions";

	var $pages_table = "geodesic_pages";
	var $pages_sections_table = "geodesic_pages_sections";
	var $pages_fonts_table = "geodesic_pages_fonts";
	var $pages_text_table = "geodesic_pages_messages";
	var $pages_text_languages_table = "geodesic_pages_messages_languages";
	var $pages_languages_table = "geodesic_pages_languages";
	var $templates_table = "geodesic_templates";
	var $pages_templates_table = "geodesic_pages_templates";
	var $pages_modules_table = "geodesic_pages_modules";

	var $very_large_font_tag;
	var $large_font_tag;
	var $medium_error_font_tag;
	var $medium_font_tag;
	var $small_font_tag;
	var $row_color_black = "#000000";
	var $row_color1;
	var $row_color2;
	var $menu_bar_font_tag;

	var $background_color_light = "#eeeeee";
	var $background_color_dark = "#dddddd";

	var $data_missing_error_message = "Your request could not be completed: missing data";
	var $internal_error_message = "There was an internal error";
	var $data_error_message = "Not enough data to complete request";
	var $page_text_error_message = "No text connected to this page";
	var $no_pages_message = "No pages to list";
	var $basic_error_message = "There has been a error processing your request.<br>Please try again.";

	var $error_message;
	var $debug = 0;
	var $debug_error_message;
	var $sql_query;
	var $row_count;
	var $configuration_data;
	var $ad_configuration_data;
	var $classified_user_id;
	var $stage;
	var $language_id;
	var $classified_variables;
	var $site_category;
	var $page_result = 1;
	var $page_id;
	var $module_id;
	var $body;
	var $module_body;
	var $font_stuff;
	var $template;
	var $in_statement;

	var $messages = array();
	var $category_tree_array = array();
	var $category_dropdown_name_array = array();
	var $category_dropdown_id_array = array();
	var $subcategory_array = array();
	var $images_to_display = array();

	var $category_questions = array();
	var $category_explanation = array();
	var $category_choices = array();
	var $category_other_box = array();
	var $category_dropdown_array = array();

	var $image_file_types = array();

	var $site_name;

	var $message_category;
	var $multiple_languages;

//########################################################################

	function Site ($db,$message_category=0,$language_id=0,$classified_user_id=0)
	{
		if ($message_category)
			$this->message_category = $message_category;
		if ($language_id)
		{
			//check language existence
			$this->sql_query = "SELECT * FROM ".$this->text_languages_table." where language_id = ".$language_id;
			// echo $this->sql_query." is the messages query<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$this->language_id = $language_id;
			}
			else
			{
				$this->language_id = 1;
			}
		}
		else
			$this->language_id = 1;

		$this->set_language($db);

		$this->classified_user_id = $classified_user_id;

		//get configuration data
		$this->get_configuration_data($db);

		$this->expire_groups_and_plans($db);

	} //end of function Site

//########################################################################

	function display_page ($db)
	{
		$this->get_template($db);
		$css = $this->get_css($db);
		//echo "hello 1<Br>";
		if (($this->template) && ($css))
		{
			//echo "hello 2<Br>";

			//$this->display_menu_bar();
			//echo "hello 3<Br>";
			$this->template = str_replace("<<MAINBODY>>",$this->body,$this->template);

			//get any modules attached to this page
			$this->get_page_modules($db);
			if (strlen(trim($css)) > 0)
			{
				//echo "hello css<bR>\n";
				$this->font_stuff = "<style type=\"text/css\">\n<!--\n".$this->font_stuff."-->\n</style>\n";
				$this->template = str_replace("<<CSSSTYLESHEET>>",$this->font_stuff,$this->template);
			}
			echo $this->template;
			//echo "hello 4<Br>";
			return true;
		}
		else
		{
			return false;
		}
	} //end of function display_page

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%


	function get_text($db,$current_page_id=0)
	{
		if ($current_page_id)
			$this->sql_query = "select text_id,text from ".$this->pages_text_languages_table." where page_id = ".$current_page_id." and language_id = ".$this->language_id;
		else
			$this->sql_query = "select text_id,text from ".$this->pages_text_languages_table." where page_id = ".$this->page_id." and language_id = ".$this->language_id;
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
			while ($show = $result->FetchNextObject())
			{
				$this->messages[$show->TEXT_ID] = $show->TEXT;
				//echo $show->TEXT_ID." - ".$show->TEXT."<br>\n";
			}
		}
	} // end of function get_text

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_css($db,$current_page_id=0)
	{
		if ($current_page_id)
			$this->sql_query = "select * from ".$this->pages_fonts_table." where page_id = ".$current_page_id;
		else
			$this->sql_query = "SELECT * FROM ".$this->pages_fonts_table." where page_id = ".$this->page_id;
		$result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			while ($show = $result->FetchRow())
			{
				$this->font_stuff .= ".".$show['element']." { ";
				$this->font_stuff .= "font-family: ".$show['font_family']."; ";
				$this->font_stuff .= "font-size: ".$show['font_size']."; ";
				$this->font_stuff .= "font-style: ".$show['font_style']."; ";
				$this->font_stuff .= "font-weight: ".$show['font_weight']."; ";
				if (strlen(trim($show['color'])) > 0)
					$this->font_stuff .= "color: ".$show['color']."; ";
				if (strlen(trim($show['text_decoration'])) > 0)
					$this->font_stuff .= "text-decoration: ".$show['text_decoration']."; ";
				if (strlen(trim($show['background_color'])) > 0)
					$this->font_stuff .= "background-color: ".$show['background_color']."; ";
				if (strlen(trim($show['background_image'])) > 0)
					$this->font_stuff .= "background-image: url(".$show['background_image'].")";
				if (strlen(trim($show['text_align'])) > 0)
					$this->font_stuff .= "text-align: ".$show['text_align'].";";
				$this->font_stuff .= " } \n";
			}
		}
		return true;
	} // end of function get_css

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
}//end of class Site Affiliate
?>