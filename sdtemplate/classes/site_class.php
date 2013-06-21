<? //site_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Site
{
	//tables within the database
	var $classifieds_table = "geodesic_classifieds";
	var $classifieds_expired_table = "geodesic_classifieds_expired";
	var $classified_sell_questions_table = "geodesic_classifieds_sell_questions";
	var $classified_extra_table = "geodesic_classifieds_ads_extra";
	var $categories_table = "geodesic_categories";
	var $filters_table = "geodesic_classifieds_filters";
	var $categories_languages_table = "geodesic_classifieds_categories_languages";
	var $filters_languages_table = "geodesic_classifieds_filters_languages";
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
	var $ad_filter_table = "geodesic_ad_filter";
	var $ad_filter_categories_table = "geodesic_ad_filter_categories";
	var $user_communications_table = "geodesic_user_communications";
	var $site_configuration_table = "geodesic_classifieds_configuration";
	var $site_auction_configuration_table = "geodesic_auctions_configuration";
	var $choices_table = "geodesic_choices";
	var $images_table = "geodesic_classifieds_images";
	var $images_urls_table = "geodesic_classifieds_images_urls";
	var $favorites_table = "geodesic_favorites";
	var $extra_pages_table = "geodesic_classifieds_extra_pages";
	var $file_types_table = "geodesic_file_types";
	var $groups_table = "geodesic_groups";
	var $group_questions_table = "geodesic_classifieds_group_questions";
	var $price_plans_table = "geodesic_classifieds_price_plans";
	var $price_plans_categories_table = "geodesic_classifieds_price_plans_categories";
	var $price_plans_increments_table = "geodesic_classifieds_price_increments";
	var $user_groups_price_plans_table = "geodesic_user_groups_price_plans";
	var $expirations_table = "geodesic_classifieds_expirations";
	var $user_credits_table = "geodesic_classifieds_user_credits";
	var $credit_choices = "geodesic_classifieds_credit_choices";
	var $payment_types_table = "geodesic_payment_choices";
	var $user_subscriptions_table = "geodesic_classifieds_user_subscriptions";
	var $subscription_choices = "geodesic_classifieds_subscription_choices";
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
	var $currency_types_table = "geodesic_currency_types";
	var $worldpay_configuration_table = "geodesic_worldpay_settings";
	var $worldpay_transaction_table = "geodesic_worldpay_transactions";
	var $registration_configuration_table = "geodesic_registration_configuration";
	var $registration_choices_table = "geodesic_registration_question_choices";
	var $registration_choices_types_table = "geodesic_registration_question_types";
	var $price_plan_lengths_table = "geodesic_price_plan_ad_lengths";
	var $subscription_holds_table = "geodesic_classifieds_user_subscriptions_holds";
	var $voting_table = "geodesic_classifieds_votes";
	var $discount_codes_table = "geodesic_classifieds_discount_codes";
	var $attached_price_plans = "geodesic_group_attached_price_plans";
	var $balance_transactions = "geodesic_balance_transactions";
	var $balance_transactions_items = "geodesic_balance_transactions_items";
	var $invoices_table = "geodesic_invoices";
	var $nochex_transaction_table = "geodesic_nochex_transactions";
	var $nochex_settings_table = "geodesic_nochex";
	var $auction_payment_types_table = "geodesic_payment_types";
    var $auctions_expired_table = "geodesic_auctions_expired";

    var $subscription_renewal = 0;
    var $account_balance = 0;

	var $pages_table = "geodesic_pages";
	var $pages_sections_table = "geodesic_pages_sections";
	var $pages_fonts_table = "geodesic_pages_fonts";
	var $pages_text_table = "geodesic_pages_messages";
	var $pages_text_languages_table = "geodesic_pages_messages_languages";
	var $pages_languages_table = "geodesic_pages_languages";
	var $templates_table = "geodesic_templates";
	var $pages_templates_table = "geodesic_pages_templates";
	var $pages_modules_table = "geodesic_pages_modules";
	var $affiliate_templates_table = "geodesic_pages_templates_affiliates";
	var $block_email_domains = "geodesic_email_domains";

	var $final_fee_table = "geodesic_auctions_final_fee_price_increments";
	var $bid_table = "geodesic_auctions_bids";
	var $autobid_table = "geodesic_auctions_autobids";
	var $increments_table = "geodesic_auctions_increments";
	var $auctions_feedbacks_table = "geodesic_auctions_feedbacks";
	var $auctions_feedback_icons_table = "geodesic_auctions_feedback_icons";
	var $blacklist_table = "geodesic_auctions_blacklisted_users";
	var $invitedlist_table = "geodesic_auctions_invited_users";

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
	var $debug_affiliate = 0;

	var $site_error_message;
	var $sql_query;
	var $row_count;
	var $configuration_data;
	var $ad_configuration_data;
	var $category_configuration;
	var $field_configuration_data;
	var $classified_user_id;
	var $stage;
	var $language_id;
	var $classified_variables;
	var $site_category = 0;
	var $page_result = 1;
	var $page_id;
	var $module_id;
	var $body;
	var $module_body;
	var $font_stuff;
	var $template;
	var $product;
	var $in_statement;
	var $count_images;
	var $images_captured;
	var $images_error;
	var $first_image_filled = 0;

	var $messages = array();
	var $category_tree_array = array();
	var $category_dropdown_name_array = array();
	var $category_dropdown_id_array = array();
	var $subcategory_array = array();
	var $images_to_display = array();
	var $image_file_types_icon = array();
	var $image_file_types_extension = array();

	var $category_questions = array();
	var $category_explanation = array();
	var $category_choices = array();
	var $category_other_box = array();
	var $category_display_order = array();
	var $category_dropdown_array = array();

	var $image_file_types = array();

	var $site_name;

	var $message_category;
	var $multiple_languages;

	var $affiliate_id = 0;
	var $affiliate_page_type = 0;
	var $affiliate_group_id = 0;

	var $filter_id = 0;
	var $state_filter = "";
	var $zip_filter = "";
	var $zip_filter_distance = "";
	var $max_latitude;
	var $min_latitude;
	var $max_longitude;
	var $min_longitude;
	var $postal_code_table = "geodesic_zip_codes";
	var $sql_filter_in_statement;
	var $sql_state_filter_statement;
	var $sql_zip_filter_in_statement;

	var $uk_postcodes = array();

	var $product_configuration = 0;
	var $auction_configuration_data;
	var $popup_image_debug = 0;

	var $sell_type = 0;
	var $debug_detail_check = 0;
	var $debug_sell = 0;
	var $debug_email = "";
	var $withAjax = false;

	var $header_font_stuff;
    var $db = NULL; //BCS-IT

//########################################################################

	function Site ($db,$message_category=0,$language_id=0,$classified_user_id=0,$product_configuration=0)
	{
    	$this->db = $db;
		if ($message_category)
			$this->message_category = $message_category;
		if ($language_id)
		{
			//check language existence
			$this->sql_query = "SELECT * FROM ".$this->pages_languages_table." where language_id = ".$language_id;
			//echo $this->sql_query." is the messages query<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $db->ErrorMsg()."<Br>\n";
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

		$this->classified_user_id = $classified_user_id;

		//get configuration data
		$this->get_configuration_data($db);
		if ($this->configuration_data["site_on_off"] && $this->classified_user_id != 1)
			ob_start();

		$this->product_configuration = $product_configuration;

		$this->expire_groups_and_plans($db);
		$this->get_type();

	} //end of function Site

//########################################################################

	function display_page ($db)
	{

		//if (($this->page_id < 135) || ($this->page_id > 154))
		// if configuration_data isnt there yet well then lets get it.
		if(!$this->configuration_data)
			$this->get_configuration_data($db);
		if ($this->configuration_data["site_on_off"] && $this->classified_user_id != 1)
		{
			ob_clean();
			header("Location: ".$this->configuration_data["disable_site_url"]);
		}
		// if the user wants to use their own css this lets them
		if($this->configuration_data['use_css'])
			$css = $this->get_css($db);
		else
			$css = 0;

		if ($this->debug) echo "css retrieved<br>\n";

        if ($this->page_id != 69)
			$this->get_template($db);

		if ($this->debug) echo "before template and extra page check for page_id = ".$this->page_id."<br>\n";
		if (($this->template) || (($this->page_id >= 135) && ($this->page_id <= 154)))
		{
			if ($this->debug) echo "after template and extra page check for page_id = ".$this->page_id."<br>\n";

			//$this->display_menu_bar();
			//echo "hello 3<Br>";
			if ($this->page_id != 69)
				$this->template = str_replace("<<MAINBODY>>",$this->body,$this->template);

			if ($this->debug)
			{
				if (($this->page_id != 69) && (($this->page_id < 135) || ($this->page_id > 154)))
					echo "mainbody was replaced<bR>\n";
				else
					echo "mainbody was NOT replaced<bR>\n";
			}

			//get any modules attached to this page
			//echo $this->affiliate_id." is the affiliate_id<Br>\n";
			//if ($this->affiliate_id == 0)
			$this->get_page_modules($db);

			$this->header_font_stuff .= "<script language=\"JavaScript\" type=\"text/javascript\">
<!--
function win(fileName) {
	myFloater = window.open('','myWindow','scrollbars=yes,status=no,width=300,height=300')
	myFloater.location.href = fileName;
}
function winimage(fileName,width,height) {
	myFloater = window.open('','myWindow','scrollbars=yes,resizable=yes,status=no,width=' + width + ',height=' + height)
	myFloater.location.href = fileName;
}
--></script>";
			if ($css)
			{
				$this->header_font_stuff .= "\n<style type=\"text/css\">\n<!--\n".$this->font_stuff."-->\n</style>\n";
			}
			$this->includeAjax();
			$this->header_font_stuff .= "
				<script language=\"JavaScript\" type=\"text/javascript\">

					window.onload = function(){
						sendReq('close', '');
					}

				</script>";
			$this->template = str_replace("<<CSSSTYLESHEET>>",$this->header_font_stuff,$this->template);
			if (!$this->debug)
			{
				if($this->page_id == 1 || $this->page_id == 69 || $this->page_id == 70 || $this->page_id == 71)
				{
					// Get rid of excess tags on auction display page
					$this->template = eregi_replace("<<[a-z0-9_]*>>", "", $this->template);
				}
			}

			if($this->configuration_data["url_rewrite"])
			{
				$this->template = preg_replace('/href=(.+?)[\s|>]/ie', "\$this->formatUrls('\\1','\\0')", $this->template);
			}
			echo $this->template;
			//echo "hello 4<Br>";
			return true;
		}
		else
			return false;
	} //end of function display_page

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_template($db)
	{
		if ($this->debug_affiliate)
		{
			echo $this->affiliate_id." is the id<br>\n";
			echo $this->affiliate_group_id." is the group<Br>\n";
		}
		if (($this->affiliate_id) && ($this->affiliate_group_id))
		{
			if ($this->debug_affiliate)
			{
				echo $this->affiliate_id." is the id<br>\n";
				echo $this->affiliate_group_id." is the group<Br>\n";
			}
			$this->sql_query = "select * from ".$this->affiliate_templates_table." where group_id = ".$this->affiliate_group_id." and language_id = ".$this->language_id;
			$result = $db->Execute($this->sql_query);
			if (($this->debug) || ($this->debug_affiliate)) echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if (($this->debug) || ($this->debug_affiliate))  echo $this->sql_query."<br>\n";
				echo "template failed 1<br>";
				return false;
			}
			elseif ($result->RecordCount() ==1)
			{
				$show_page = $result->FetchNextObject();
				if (($this->debug) || ($this->debug_affiliate)) echo $this->affiliate_page_type." is affiliate_page_type<Br>\n";
				if (($this->affiliate_page_type > 1) || ($this->page_id == 1))
					$this->sql_query = "select template_code,location from ".$this->templates_table." where template_id = ".$show_page->SECONDARY_TEMPLATE_ID;
				else
					$this->sql_query = "select template_code,location from ".$this->templates_table." where template_id = ".$show_page->TEMPLATE_ID;
				$template_result = $db->Execute($this->sql_query);
				if (($this->debug) || ($this->debug_affiliate))  echo $this->sql_query."<br>\n";
				if (!$template_result)
				{
					if (($this->debug) || ($this->debug_affiliate))  echo $this->sql_query."<br>\n";
					echo "template failed 2<br>";
					return false;
				}
				elseif ($template_result->RecordCount() ==1)
				{
					$show_template = $template_result->FetchNextObject();
					$this->template = stripslashes($show_template->TEMPLATE_CODE);

					//get this affiliates personal html
					$affiliate_html = $this->get_user_data($db,$this->affiliate_id);
					if (($this->debug) || ($this->debug_affiliate))
					{
						echo $affiliate_html->AFFILIATE_HTML." is this affiliates AFFILIATE_HTML<br>\n";
					}
					if (strlen(trim($affiliate_html->AFFILIATE_HTML)) > 0)
						$this->template = str_replace("<AFFILIATE_INFO>",stripslashes($affiliate_html->AFFILIATE_HTML),$this->template);
					else
						$this->template = str_replace("<AFFILIATE_INFO>","",$this->template);

					return true;
				}
				else
				{
					if (($this->debug) || ($this->debug_affiliate))
					{
						echo "the template count is wrong - ".$template_result->RecordCount()."<br>\n";
					}
					echo "template failed 3<br>";
					return false;
				}
			}
			else
			{
				echo "template configuration issue - check template assignment on this page 1<br>";
				return false;
			}
		}
		if (($this->page_id == 3) && ($this->page_result == 1) && ($this->site_category))
		{
        	//see if there is a category home page template
			$this->sql_query = "select template_id from ".$this->categories_languages_table." where category_id = ".$this->site_category." and language_id = ".$this->language_id;
            $cat_temp_result = $db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$cat_temp_result)
			{
				if ($this->debug) echo $this->sql_query."<br>\n";
				echo "template failed 4<br>";
				return false;
			}
			elseif ($cat_temp_result->RecordCount() ==1)
			{
				$show_category_template = $cat_temp_result->FetchNextObject();
				if ($show_category_template->TEMPLATE_ID)
				{
					$this->sql_query = "select template_code,location from ".$this->templates_table." where template_id = ".$show_category_template->TEMPLATE_ID;
					$template_result = $db->Execute($this->sql_query);
					if ($this->debug) echo $this->sql_query."<br>\n";
					if (!$template_result)
					{
						if ($this->debug) echo $this->sql_query."<br>\n";
						echo "template failed 5<br>";
						return false;
					}
					elseif ($template_result->RecordCount() ==1)
					{
						$show_template = $template_result->FetchNextObject();
						$this->template = stripslashes($show_template->TEMPLATE_CODE);
						return;
					}
					else
					{
						echo "template failed 6<br>";
						return false;
					}
					return true;
				}
			}
			else
			{
				echo "template configuration issue - check template assignment on this page 2<br>";
				return false;
			}
		}
		if (($this->page_id == 3) && ($this->page_result > 1) && ($this->site_category))
		{
			//see if there is a category secondary page template
			$this->sql_query = "select secondary_template_id from ".$this->categories_languages_table." where category_id = ".$this->site_category." and language_id = ".$this->language_id;
			$cat_temp_result = $db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$cat_temp_result)
			{
				if ($this->debug) echo $this->sql_query."<br>\n";
				echo "template failed 7<br>";
				return false;
			}
			elseif ($cat_temp_result->RecordCount() ==1)
			{
				$show_category_template = $cat_temp_result->FetchNextObject();
				if ($show_category_template->SECONDARY_TEMPLATE_ID)
				{
					$this->sql_query = "select template_code,location from ".$this->templates_table." where template_id = ".$show_category_template->SECONDARY_TEMPLATE_ID;
					$template_result = $db->Execute($this->sql_query);
					if ($this->debug) echo $this->sql_query."<br>\n";
					if (!$template_result)
					{
						if ($this->debug) echo $this->sql_query."<br>\n";
						echo "template failed 8<br>";
						return false;
					}
					elseif ($template_result->RecordCount() ==1)
					{
						$show_template = $template_result->FetchNextObject();
						$this->template = stripslashes($show_template->TEMPLATE_CODE);
						return;
					}
					else
					{
						echo "template failed 9<br>";
						return false;
					}
					return true;
				}
			}
			else
			{
				echo "template configuration issue - check template assignment on this page 3<br>";
				return false;
			}
		}
		if (($this->page_id == 1) && ($this->site_category))
		{
			//see if there is a category home page template
			$this->sql_query = "select ad_display_template_id from ".$this->categories_languages_table." where category_id = ".$this->site_category." and language_id = ".$this->language_id;
			$cat_temp_result = $db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$cat_temp_result)
			{
				if ($this->debug) echo $this->sql_query."<br>\n";
				echo "template failed 10<br>";
				return false;
			}
			elseif ($cat_temp_result->RecordCount() ==1)
			{
				$show_category_template = $cat_temp_result->FetchNextObject();
				if ($show_category_template->AD_DISPLAY_TEMPLATE_ID)
				{
					$this->sql_query = "select template_code,location from ".$this->templates_table." where template_id = ".$show_category_template->AD_DISPLAY_TEMPLATE_ID;
					$template_result = $db->Execute($this->sql_query);
					if ($this->debug) echo $this->sql_query."<br>\n";
					if (!$template_result)
					{
						if ($this->debug) echo $this->sql_query."<br>\n";
						echo "template failed 11<br>";
						return false;
					}
					elseif ($template_result->RecordCount() ==1)
					{
						$show_template = $template_result->FetchNextObject();
						$this->template = stripslashes($show_template->TEMPLATE_CODE);
						return;
					}
					else
					{
						echo "template failed 12<br>";
						return false;
					}
					return true;
				}
			}
			else
			{
				echo "template configuration issue - check template assignment on this page 4<br>";
				return false;
			}
		}
		$this->sql_query = "select * from ".$this->pages_templates_table." where page_id = ".$this->page_id." and language_id = ".$this->language_id;
		$result = $db->Execute($this->sql_query);
		if ($this->debug) echo $this->sql_query."<br>\n";
		if (!$result)
		{
			if ($this->debug) echo $this->sql_query."<br>\n";
			echo "template failed 13<br>";
			echo $db->ErrorMsg()." is the error<br>\n";
			if ($this->debug) echo $this->sql_query."<br>\n";
			return false;
		}
		elseif ($result->RecordCount() ==1)
		{
			$show_page = $result->FetchNextObject();
			$this->sql_query = "select template_code,location from ".$this->templates_table." where template_id = ".$show_page->TEMPLATE_ID;
			$template_result = $db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$template_result)
			{
				if ($this->debug) echo $this->sql_query."<br>\n";
				echo "template failed 14<br>";
				return false;
			}
			elseif ($template_result->RecordCount() ==1)
			{
				$show_template = $template_result->FetchNextObject();
				$this->template = stripslashes($show_template->TEMPLATE_CODE);
				return;
			}
			else
			{
				echo "template failed 15<br>";
				return false;
			}
		}
		else
		{
			echo "template configuration issue - check template assignment on this page 5<br>";
			return false;
		}

	} // end of function get_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_page_modules($db)
	{
		// Flag if any HTML modules are used
		$html = false;

		$this->sql_query = "select * from ".$this->pages_modules_table." where page_id = ".$this->page_id." order by time asc";
		$result = $db->Execute($this->sql_query);
		if ($this->debug) echo $this->sql_query."<br>\n";
		if (!$result)
		{
			if ($this->debug) echo $this->sql_query."<br>\n";
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			while ($show = $result->FetchRow())
			{
				$this->sql_query = "select * from ".$this->pages_table." where page_id = ".$show['module_id'];
				$module_result = $db->Execute($this->sql_query);
				if ($this->debug) echo $this->sql_query."<br>\n";
				if (!$module_result)
				{
					if ($this->debug) echo $this->sql_query."<br>\n";
					//continue with next module...instead of erroring out.
					continue;
				}
				elseif ($module_result->RecordCount() == 1)
				{
					$show_module = $module_result->FetchRow();
					if (strlen($show_module['module_file_name']) > 0)
					{
						include("classes/".$show_module['module_file_name']);
						$this->template = str_replace($show_module['module_replace_tag'],$this->body,$this->template);
					}

					// Check if HTML is in the module name
					if(strpos($show_module['name'], "HTML") != false)
					{
						$html = true;
					}
				}
			}
		}

		// If we there were any HTML modules lets go over the modules again so that there are no embedded ones
		/*if($html)
		{
			$this->sql_query = "select * from ".$this->pages_table." where module = 1";
			$module_result = $db->Execute($this->sql_query);

			$modules = $module_result->GetArray();
			foreach($modules as $array => $key)
			{
				//include("classes/".$key['module_file_name']);
				$this->template = str_replace($key['module_replace_tag'], $this->body, $this->template);
			}
		}
		*/

		return true;
	} // end of function get_page_modules

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_text($db,$current_page_id=0)
	{
		if ($current_page_id)
			$this->sql_query = "select text_id,text from ".$this->pages_text_languages_table." where page_id = ".$current_page_id;
		else
			$this->sql_query = "select text_id,text from ".$this->pages_text_languages_table." where page_id = ".$this->page_id;
		$numberOfExtraPages = func_num_args();
		if($numberOfExtraPages>=3)
		{
			$args_list = func_get_args();
			for($lcv=2; $lcv<$numberOfExtraPages; $lcv++)
				$this->sql_query .= " or page_id = ".$args_list[$lcv];
		}
		$this->sql_query .= " and language_id = ".$this->language_id;
		//echo $this->sql_query."<br>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//echo "bad get_text query<bR>\n";
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
			return false;
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
					$this->font_stuff .= "background-image: url(".$show['background_image']."); ";
				if (strlen(trim($show['text_align'])) > 0)
					$this->font_stuff .= "text-align: ".$show['text_align']."; ";
				if (strlen(trim($show['text_vertical_align'])) > 0)
					$this->font_stuff .= "vertical-align: ".$show['text_vertical_align'].";";
				$this->font_stuff .= " } \n";
			}
		}
		return true;
	} // end of function get_css

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

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

	function get_configuration_data($db)
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
		return true;
	} //end of function get_configuration_data

//########################################################################

	//used within classauctions only
	function get_auction_configuration_data($db)
	{
		$this->sql_query = "SELECT * FROM ".$this->site_auction_configuration_table;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		else
		{
			$this->auction_configuration_data = $result->FetchNextObject();
		}
		return true;
	}

//##########################################################################

	function site_error($db)
	{
		$this->page_id = 59;
		$this->get_text($db);
		//check to see if debugging
		$this->body ="<table cellpadding=3 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr>\n\t<td class=site_error_page_title>\n\t".urldecode($this->messages[908])." \n\t</td>\n</tr>\n";
		$this->body .="<tr>\n\t<td class=site_error_page_description>\n\t".urldecode($this->messages[908])." \n\t</td>\n</tr>\n";
		$this->body .="<tr>\n\t<td class=site_error_page_description>\n\t".$this->site_error_message." \n\t</td>\n</tr>\n";
		if ($this->debug)
		{
			$this->body .="<tr>\n\t<td class=site_error_page_description>\n\tWith the following query:<br>".$this->sql_query." \n\t</td>\n</tr>\n";
		}
		$this->body .="</table>\n";
		$this->display_page($db);
		exit;

	} //end of function site_error

//#########################################################################

	function show_state_dropdown ($db,$state,$name)
	{
		$this->sql_query = "SELECT * FROM ".$this->states_table." order by display_order, name";
		$result = $db->Execute($this->sql_query);
		if (!$result)
			return false;
		else
		{
			$this->body .="<select name=".$name.">\n\t\t";
			$this->body .="<option value=none>Choose a state\n\t\t";
			while ($show = $result->FetchNextObject()) {
				//spit out the state list
				$this->body .="<option value=\"".$show->ABBREVIATION."\"";
				if ($state == $show->ABBREVIATION)
				$this->body .="selected";
				$this->body .=">".$show->NAME."\n\t\t";
			}

			$this->body .="</select>\n\t";
		}
		return true;
	}// end of function show_state_dropdown

//#########################################################################

	function show_country_dropdown ($db,$country,$name)
	{
		$this->sql_query = "SELECT * FROM ".$this->countries_table." order by display_order, name";
		$result = $db->Execute($this->sql_query);
		if (!$result)
			return false;
		else
		{
			$this->body .="<select name=".$name.">\n\t\t";
			$this->body .="<option value=none>Choose a country\n\t\t";
			while ($show = $result->FetchNextObject()) {
				//spit out the country list
				$this->body .="<option ";
				if ((urldecode($country) == $show->ABBREVIATION) || (urldecode($country) == $show->NAME))
				$this->body .="selected";
				$this->body .=">".$show->NAME."\n\t\t";
			}

			$this->body .="</select>\n\t";
		}
		return true;
	}// end of function show_country_dropdown

//########################################################################

	function get_category_tree($db,$category)
	{
		$i = 0;
		$category_next = $category;
		$this->category_tree_array = 0;
		$this->category_tree_array = array();
		do
		{
			$this->sql_query = "select parent_id from ".$this->categories_table." where category_id = ".$category_next;
			$category_result =  $db->Execute($this->sql_query);

			if (!$category_result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[2052];
				return false;
			}
			elseif ($category_result->RecordCount() == 1)
			{
				$show_category = $category_result->FetchNextObject();
				$this->sql_query = "select category_name from ".$this->categories_languages_table."
					where category_id = ".$category_next." and language_id =".$this->language_id;
				$category_name_result =  $db->Execute($this->sql_query);

				if (!$category_name_result)
				{
					//$this->body .=$this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[2052];
					return false;
				}
				elseif ($category_name_result->RecordCount() == 1)
				{
					$show_name = $category_name_result->FetchNextObject();
					$this->category_tree_array[$i]["parent_id"]  = $show_category->PARENT_ID;
					$this->category_tree_array[$i]["category_name"] = urldecode(stripslashes($show_name->CATEGORY_NAME));
					$this->category_tree_array[$i]["category_id"]   = $category_next;
					$i++;
					$category_next = $show_category->PARENT_ID;
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

     		} while ( $show_category->PARENT_ID != 0 );

     		return true;

	} // end of function get_category_tree($category)

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_user_name($db,$some_user_id=0)
	{
		if ($some_user_id)
		{
			$this->sql_query = "select username from ".$this->userdata_table." where id = ".$some_user_id;
			$username_result = $db->Execute($this->sql_query);
			if (!$username_result)
				return false;
			elseif ($username_result->RecordCount() == 1)
			{
				$show_username = $username_result->FetchNextObject();
				return $show_username->USERNAME;
			}
			else
			{
				//just display the user_id
				return $some_user_id;
			}
		}
		else
		{
			return false;
		}
	} //end of function get_user_name

//########################################################################

	function get_user_email($db,$some_user_id=0)
	{
		if ($some_user_id)
		{
			$this->sql_query = "select email from ".$this->userdata_table." where id = ".$some_user_id;
			$username_result = $db->Execute($this->sql_query);
			if (!$username_result)
				return false;
			elseif ($username_result->RecordCount() == 1)
			{
				$show_username = $username_result->FetchNextObject();
				return $show_username->EMAIL;
			}
			else
			{
				//just display the user_id
				return $some_user_id;
			}
		}
		else
		{
			return false;
		}
	} //end of function get_user_email

//########################################################################

	function get_ad_title($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$this->sql_query = "select title from ".$this->classifieds_table." where id = ".$classified_id;
			$ad_result = $db->Execute($this->sql_query);
			//$this->body .=$this->sql_query."<br>\n";
			if (!$ad_result)
			{
				return false;
			}
			elseif ($ad_result->RecordCount() == 1)
			{
				$show_title = $ad_result->FetchNextObject();
				return urldecode($show_title->TITLE);
			}
			else
			{
				//just display the user_id
				return $classified_id;
			}
		}
		else
		{
			return false;
		}
	} //end of function get_ad_title

//########################################################################

	function get_user_communication_level($db,$user_id=0)
	{
		if ($user_id)
		{
			$this->sql_query = "select communication_type from ".$this->userdata_table." where id = ".$user_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_type = $result->FetchNextObject();
				return $show_type->COMMUNICATION_TYPE;
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
	} //end of function get_ad_title

//########################################################################

	function get_row_color($special=0)
	{
		if (($this->row_count % 2) == 0)
		{
			switch ($page_id)
			{


				case 2:
				//search page results
					if ($special)
						return "main_result_table_body_even_bold";
					else
						return "main_result_table_body_even";
					break;

				case 3:
				//search page results
					if ($special)
						return "browsing_result_table_body_even_bold";
					else
						return "browsing_result_table_body_even";
					break;
			}
		}
		else
		{
			switch ($page_id)
			{
				case 2:
				//search page results
					if ($special)
						return "main_result_table_body_odd_bold";
					else
						return "main_result_table_body_odd";
					break;

				case 3:
				//search page results
					if ($special)
						return "browsing_result_table_body_odd_bold";
					else
						return "browsing_result_table_body_edd";
					break;
			}
		}
		return $row_color;
	} //end of function get_row_color

//##################################################################################

	function get_category_name($db,$category_id=0)
	{
		if ($category_id)
		{
			$this->sql_query = "select category_name,category_cache,cache_expire,description from ".$this->categories_languages_table." where category_id = ".$category_id." and language_id = ".$this->language_id;
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchNextObject();
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
			return "Main";
		}
	} //end of function get_category_name

//##################################################################################

	function get_category_configuration($db,$category_id=0,$get_array=1)
	{
		if ($category_id)
		{
			$this->sql_query = "select * from ".$this->categories_table." where category_id = ".$category_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<bR>\n";
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				if($get_array)
					$this->category_configuration = $result->FetchRow();
				else
					$this->category_configuration = $result->FetchNextObject();
				return true;
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
	} //end of function get_category_configuration

//##################################################################################

	function get_category_dropdown($db,$name,$category_id=0,$no_main=0,$css_control=0)
	{
		if (!$no_main)
		{
			array_push($this->category_dropdown_name_array, "All Categories");
			array_push($this->category_dropdown_id_array,0);
		}
		$this->get_subcategories_for_dropdown($db,0,0);

		//build the select statement
		//array_reverse($this->category_dropdown_name_array);
		//array_reverse($this->category_dropdown_id_array);
		$this->body .="<select name=".$name." ";
		if ($css_control)
			$this->body .= "class=".$css_control;
		$this->body .= ">\n\t\t";
		foreach($this->category_dropdown_name_array as $key => $value)
		{
			$this->body .="<option ";
			if ($this->category_dropdown_id_array[$key] == $category_id)
				$this->body .="selected";
			$this->body .=" value=".$this->category_dropdown_id_array[$key].">".urldecode($this->category_dropdown_name_array[$key])."</option>\n\t\t";
		}
		$this->body .="</select>\n\t";

     		return true;

	} //end of function get_category_dropdown

//##################################################################################

	function get_subcategories_for_dropdown($db,$category_id=0)
	{
		if ((($this->stage + 1) <= $this->configuration_data['levels_of_categories_displayed'])
			|| ($this->configuration_data['levels_of_categories_displayed'] == 0))
		{
			//$stage++;
			//$this->sql_query = "select ".$this->categories_table.".category_id as category_id,".$this->categories_table.".parent_id as parent_id,".$this->categories_languages_table.".category_name as category_name
			//	from ".$this->categories_table.",".$this->categories_languages_table."
			//	where ".$this->categories_table.".category_id =".$this->categories_languages_table.".category_id and
			//	".$this->categories_table.".parent_id = ".$category_id." order by display_order,category_name";
			$this->sql_query = "select ".$this->categories_table.".category_id as category_id,
				".$this->categories_table.".parent_id as parent_id,".$this->categories_languages_table.".category_name as category_name
				from ".$this->categories_table.",".$this->categories_languages_table."
				where ".$this->categories_table.".category_id = ".$this->categories_languages_table.".category_id
				and ".$this->categories_table.".parent_id = ".$category_id."
				and ".$this->categories_languages_table.".language_id = ".$this->language_id." order by ".$this->categories_table.".display_order,".$this->categories_languages_table.".category_name";
			$category_result =  $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$category_result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[2052];
				return false;
			}
			elseif ($category_result->RecordCount() > 0)
			{
				$this->stage++;
				while ($show_category = $category_result->FetchNextObject())
				{
					$pre_stage = "";
					for ($i=1;$i<=$this->stage;$i++)
					{
						$pre_stage .= "&nbsp;&nbsp;&nbsp;";
					}
					if ($category_id != 0)
					{
						array_push($this->category_dropdown_name_array, $pre_stage.urldecode(stripslashes($show_category->CATEGORY_NAME)));
						array_push($this->category_dropdown_id_array,$show_category->CATEGORY_ID);
					}
					else
					{
						array_push($this->category_dropdown_name_array, urldecode(stripslashes($show_category->CATEGORY_NAME)));
						array_push($this->category_dropdown_id_array,$show_category->CATEGORY_ID);
					}
					$this->get_subcategories_for_dropdown($db,$show_category->CATEGORY_ID);
				}
				$this->stage--;
			}
		}
		return;
	} //end of function get_subcategories_for_dropdown

//##################################################################################

	function get_category_questions($db,$category_id=0)
	{
		//get sell questions specific to this category
		//echo $category_id." is category_id in get_category_questions<BR>\n";
		while ($category_id != 0)
		{
			//get the questions for this category
			$this->sql_query = "SELECT * FROM ".$this->classified_sell_questions_table." WHERE category_id = ".$category_id." ORDER BY display_order";
			//$this->sql_query = "SELECT * FROM ".$this->classified_sell_questions_table." WHERE category_id = ".$category_id." ORDER BY display_order desc";
			//$this->body .=$this->sql_query." is the query<br>\n";
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}

			if ($result->RecordCount() > 0)
			{
				//$this->body .="hello from inside a positive results<br>\n";
				while ($get_questions = $result->FetchNextObject())
				{
					//get all the questions for this category and store them in the auction_questions variable
					//$this->body .=$get_questions["question_key"]." is the question key<br>\n";
					$this->category_questions[$get_questions->QUESTION_ID] = $get_questions->NAME;
					$this->category_explanation[$get_questions->QUESTION_ID] = $get_questions->EXPLANATION;
					$this->category_choices[$get_questions->QUESTION_ID] = $get_questions->CHOICES;
					$this->category_other_box[$get_questions->QUESTION_ID] = $get_questions->OTHER_INPUT;
					$this->category_display_order[$get_questions->QUESTION_ID] = $get_questions->DISPLAY_ORDER;
					$this->category_url_icon[$get_questions->QUESTION_ID] = $get_questions->URL_ICON;

					//$this->body .=$get_questions->CHOICES." is the choices for ".$get_questions->QUESTION_ID."<br>\n\t";
				} //end of while $get_questions = mysql_fetch_array($result)
			} //end of if ($result)

			//get this_cat_id parent category
			$this->sql_query = "SELECT parent_id FROM ".$this->categories_table." WHERE category_id = ".$category_id;
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			//$this->body .=$this->sql_query." is the query<br>\n";
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_category = $result->FetchNextObject();
				$category_id = $show_category->PARENT_ID;
			}
			else
			{
				//$this->body .=$this->sql_query." is the query where count is not 1<br>\n";
				return false;
			}

		} //end of if ($category_id != 0)

	} //end of function get_category_questions

//##################################################################################

	function get_ad_configuration($db,$by_array=0)
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

	} //function get_ad_configuration

//#########################################################################

	function get_sql_in_statement($db,$category_id)
	{
		if ($category_id)
		{
			$this->sql_query = "SELECT in_statement FROM ".$this->categories_table." WHERE category_id = ".$category_id;
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_in_statement = $result->FetchRow();
				$current_in_statement = $show_in_statement['in_statement'];
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
						$this->in_statement = "";
						$this->in_statement .= "in (";
						while (list($key,$value) = each($this->subcategory_array))
						{
							if ($key == 0)
								$this->in_statement .= $value;
							else
								$this->in_statement .= ",".$value;
						}
						$this->in_statement .= ")";
						$this->sql_query = "update ".$this->categories_table." set
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

			$this->sql_query = "select category_id from ".$this->categories_table." where parent_id = ".$category_id;
			//$this->body .=$this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[2524];
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				while ($show_category = $result->FetchRow())
				{
					$this->get_sql_in_array($db,$show_category['category_id']);
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

	function get_user_data($db,$user_id=0)
	{
		if (!$user_id)
		{
			if ($this->classified_user_id)
				$user_id = $this->classified_user_id;
		}

		if ($user_id)
		{
			if ($this->configuration_data['use_mambo'])
			{
				//get user data from mambo installation instead

			}
			else
			{
				$this->sql_query = "select * from ".$this->userdata_table.",".$this->user_groups_price_plans_table." where
					".$this->userdata_table.".id = ".$this->user_groups_price_plans_table.".id and ".$this->userdata_table.".id = ".$user_id;
				$user_data_result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is the get_user_data query<br>\n";
				if (!$user_data_result)
				{
					//$this->body .=$this->sql_query." is the state query<br>\n";
					//echo $db->ErrorMsg()." is the error in get_user_data<br>\n";
					//echo "bad get_user_data query<bR>\n";
					return false;
				}
				elseif ($user_data_result->RecordCount() == 1)
				{
					$show_user = $user_data_result->FetchNextObject();
					return $show_user;
				}
				else
				{
					$this->error_message = $this->data_error_message;
					return false;
				}
			}
		}
		else
		{
			//no user id
			return false;
		}
	} //end of function get_user_data

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_type()
	{
		if (strpos(dirname(__FILE__),'/') !== FALSE)
			require_once(dirname(__FILE__)."/info.php");
		else
			require_once(dirname(__FILE__)."\info.php");
		$this->product = new product();
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_classified_data($db,$classified_id=0,$array=0)
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
			if($array)
			{
				$show = $result->FetchRow();
				return $show;
			}
			else
			{
				$show = $result->FetchNextObject();
				return $show;
			}
		}
		else
		{
			return false;
		}

	} //end of function get_classified_data

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function choose_language_form($db)
	{
		$this->sql_query = "select * from ".$this->pages_languages_table." where active = 1";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//$this->body .=$this->sql_query." is the state query<br>\n";
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->page_id = 42;
			$this->get_text($db);
			//display the language choice form
			$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=21 method=post>\n";
			$this->body .="<table cellpadding=3 cellspacing=1 border=0 width=100%>\n";
			$this->body .="<tr class=language_page_title>\n\t<td colspan=2>\n\t".urldecode($this->messages[327])."\n\t</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td colspan=2>\n\t".urldecode($this->messages[328])."\n\t</td>\n</tr>\n";
			$this->body .="<tr class=field_label>\n\t<td align=right width=50% class=medium_font>\n\t".urldecode($this->messages[329])." \n\t</td>\n\t";
			$this->body .="<td width=50%>\n\t<select name=set_language_cookie>\n\t\t";
			while ($show = $result->FetchNExtObject())
			{
				$this->body .="<option value=".$show->LANGUAGE_ID;
				if($show->LANGUAGE_ID == $language_id)
					$this->body .= " selected";
				$this->body .= ">".urldecode($show->LANGUAGE)."</option>\n\t\t";
			}
			$this->body .="</select>\n\t</td>\n</tr>\n";
			$this->body .="<tr class=save_language_choice_button>\n\t<td colspan=2 align=center class=medium_font>\n\t<input type=submit name=submit value=\"".urldecode($this->messages[330])."\"> \n\t</td>\n</tr>\n";
			$this->body .="</table>\n";
			$this->body .="</form>\n";
			$this->display_page($db);
			return true;
		}
		else
		{
			$this->error_message = $this->data_error_message;
			return false;
		}
	} //end of function choose_language_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_help_link($help_id=0,$type=0,$link_text=0,$question_id=0)
	{
		if ($question_id)
		{
			$help_link =  "<a href=\"javascript:win('show_help.php?a=1&c=".$question_id."');\"><img src=".urldecode($this->configuration_data['help_image'])." hspace=2 vspace=0 border=0></a>";
		}
		elseif (($help_id) &&($link_text))
		{
			$help_link =  "<a href=\"javascript:win('show_help.php?a=".$help_id."&l=".$this->language_id."');\"><span class=medium_font>".urldecode($this->messages[$link_text])."</span></a>";
		}
		elseif ($help_id)
		{
			if ($type == 1)
			{
				$help_link =  "<a href=\"javascript:win('show_help.php?a=".$help_id."&b=1&l=".$this->language_id."');\"><img src=".urldecode($this->configuration_data['help_image'])." hspace=2 vspace=0 border=0></a>";
			}
			else
			{
				$help_link =  "<a href=\"javascript:win('show_help.php?a=".$help_id."&l=".$this->language_id."');\"><img src=".urldecode($this->configuration_data['help_image'])." hspace=2 vspace=0 border=0></a>";
			}
		}
		else
		{
			//no user id
			return false;
		}
		return $help_link;
	} //end of function display_help_link

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_ad_images ($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$this->get_ad_configuration($db);
			$this->get_image_data($db,$classified_id);
			$this->count_images = count($this->images_to_display);

			if ($this->count_images >= $this->ad_configuration_data->PHOTO_COLUMNS)
			{
				$use_count = 0;
				switch ($this->ad_configuration_data->PHOTO_COLUMNS)
				{
					case 1:
						$width_tag = "100%";
						break;
					case 2:
						$width_tag = "50%";
						break;
					case 3:
						$width_tag = "33%";
						break;
					case 4:
						$width_tag = "25%";
						break;
					case 5:
						$width_tag = "20%";
						break;
					case 6:
						$width_tag = "16%";
						break;
					case 7:
						$width_tag = "14%";
						break;
					case 8:
						$width_tag = "12%";
						break;
					default:

				}
			}
			else {
				$use_count = 1;
				switch ($this->count_images)
				{
					case 1:
						$width_tag = "100%";
						break;
					case 2:
						$width_tag = "50%";
						break;
					case 3:
						$width_tag = "33%";
						break;
					case 4:
						$width_tag = "25%";
						break;
					case 5:
						$width_tag = "20%";
						break;
					case 6:
						$width_tag = "16%";
						break;
					case 7:
						$width_tag = "14%";
						break;
					case 8:
						$width_tag = "12%";
						break;
					default:

				}
			}
			if ((is_array($this->images_to_display)) && (count($this->images_to_display) > 0))
			{
				ksort($this->images_to_display);
				reset($this->images_to_display);
				$image_table =  "<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>";
				$value = current($this->images_to_display);

				do {
					$image_table .= "<tr><td align=center valign=top width=".$width_tag.">";
					$image_table .= $this->display_image_tag($db, $value);
					$image_table .= "</td>";
					if ((($this->ad_configuration_data->PHOTO_COLUMNS > 1) && ($use_count == 0)) || (($use_count == 1) && ($this->count_images > 1)))
					{
						$value = next($this->images_to_display);
						if ($value)
						{
							$image_table .= "<td align=center valign=top width=".$width_tag.">";
							$image_table .= $this->display_image_tag($db, $value);
							$image_table .= "</td>";
							if ((($this->ad_configuration_data->PHOTO_COLUMNS > 2) && ($use_count == 0)) || (($use_count == 1) && ($this->count_images > 2)))
							{
								$value = next($this->images_to_display);
								if ($value)
								{
									$image_table .= "<td align=center valign=top width=".$width_tag.">";
									$image_table .= $this->display_image_tag($db, $value);
									$image_table .= "</td>";

									if ((($this->ad_configuration_data->PHOTO_COLUMNS > 3) && ($use_count == 0)) || (($use_count == 1) && ($this->count_images > 3)))
									{
										$value = next($this->images_to_display);
										if ($value)
										{
											$image_table .= "<td align=center valign=top width=".$width_tag.">";
											$image_table .= $this->display_image_tag($db, $value);
											$image_table .= "</td>";

											if ((($this->ad_configuration_data->PHOTO_COLUMNS > 4) && ($use_count == 0)) || (($use_count == 1) && ($this->count_images > 4)))
											{
												$value = next($this->images_to_display);
												if ($value)
												{
													$image_table .= "<td align=center valign=top width=".$width_tag.">";
													$image_table .= $this->display_image_tag($db, $value);
													$image_table .= "</td>";

													if ((($this->ad_configuration_data->PHOTO_COLUMNS > 5) && ($use_count == 0)) || (($use_count == 1) && ($this->count_images > 5)))
													{
														$value = next($this->images_to_display);
														if ($value)
														{
															$image_table .= "<td align=center valign=top width=".$width_tag.">";
															$image_table .= $this->display_image_tag($db, $value);
															$image_table .= "</td>";

															if ((($this->ad_configuration_data->PHOTO_COLUMNS > 6) && ($use_count == 0)) || (($use_count == 1) && ($this->count_images > 6)))
															{
																$value = next($this->images_to_display);
																if ($value)
																{
																	$image_table .= "<td align=center valign=top width=".$width_tag.">";
																	$image_table .= $this->display_image_tag($db, $value);
																	$image_table .= "</td>";

																	if ((($this->ad_configuration_data->PHOTO_COLUMNS > 7) && ($use_count == 0)) || (($use_count == 1) && ($this->count_images > 7)))
																	{
																		$value = next($this->images_to_display);
																		if ($value)
																		{
																			$image_table .= "<td align=center valign=top width=".$width_tag.">";
																			$image_table .= $this->display_image_tag($db, $value);
																			$image_table .= "</td>";
																		}
																	}

																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
					$image_table .= "</tr>";
				} while ($value = next($this->images_to_display));

				$image_table .= "</table>\n";
			}
			return $image_table;
		}
		else
		{
			//no auction id to check
			return false;
		}
	 } //end of function display_ad_images

//####################################################################################

	function display_image_tag($db,$value)
	{
		if (($value["image_width"] > $this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH) && ($value["image_height"] > $this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT))
		{
			$imageprop = ($this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH * 100) / $value["image_width"];
			$imagevsize = ($value["image_height"] * $imageprop) / 100 ;
			$final_image_width = $this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH;
			$final_image_height = ceil($imagevsize);

			if ($final_image_height > $this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT)
			{
				$imageprop = ($this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT * 100) / $value["image_height"];
				$imagehsize = ($value["image_width"] * $imageprop) / 100 ;
				$final_image_height = $this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT;
				$final_image_width = ceil($imagehsize);
			}
		}
		elseif ($value["image_width"] > $this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH)
		{
			$imageprop = ($this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH * 100) / $value["image_width"];
			$imagevsize = ($value["image_height"] * $imageprop) / 100 ;
			$final_image_width = $this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH;
			$final_image_height = ceil($imagevsize);
		}
		elseif ($value["image_height"] > $this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT)
		{
			$imageprop = ($this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT * 100) / $value["image_height"];
			$imagehsize = ($value["image_width"] * $imageprop) / 100 ;
			$final_image_height = $this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT;
			$final_image_width = ceil($imagehsize);
		}
		else
		{
			$final_image_width = $value["image_width"];
			$final_image_height = $value["image_height"];
		}

		//echo $value["image_text"]." is image text2<br>\n";
		if ($value["type"] == 1)
		{
			//display the url
			if (strlen(trim($value["icon"])) > 0)
			{
				$tag = "<a href=\"".$value["url"]."\" target=\"new\">";
				$tag .=  "<img src=\"".$value["icon"]."\" border=0></a>";
			}
			else
			{
				if ($final_image_width != $value["original_image_width"])
				{
					if ($this->configuration_data['image_link_destination_type'])
					{
						if ($this->affiliate_id)
							$tag = "<a href=".$this->configuration_data['affiliate_url']."?a=15&b=".$value["classified_id"].">";
						else
							$tag = "<a href=".$this->configuration_data['classifieds_url']."?a=15&b=".$value["classified_id"].">";
					}
					else
					{
						$this->sql_query = "select * from ".$this->ad_configuration_table;
						//echo $db." is the db<br>\n";
						$extra_result = $db->Execute($this->sql_query);
						//echo $this->sql_query." is the query<br>\n";
						$show = $extra_result->FetchNextObject();
						if ($this->ad_configuration_data->POPUP_IMAGE_TEMPLATE_ID)
						{
							if($this->popup_image_debug)
							{
								echo $this->ad_configuration_data->POPUP_IMAGE_EXTRA_WIDTH." is the extra width<br>\n";
								echo $this->ad_configuration_data->POPUP_IMAGE_EXTRA_HEIGHT." is the extra height<br>\n";
								echo $show->MAXIMUM_FULL_IMAGE_WIDTH." is the image width<br>\n";
								echo $show->MAXIMUM_FULL_IMAGE_HEIGHT." is the image height<br>\n";
							}
							$tag = "<a href=\"javascript:winimage('get_image.php?popupimage=".$value["id"]."','".($show->MAXIMUM_FULL_IMAGE_WIDTH + $show->POPUP_IMAGE_EXTRA_WIDTH)."','".($show->MAXIMUM_FULL_IMAGE_HEIGHT + $show->POPUP_IMAGE_EXTRA_HEIGHT)."')\" class=zoom_linkmage_links>";
						}
						else
						{
							$tag = "<a href=\"javascript:winimage('".$value["url"]."','".($show->MAXIMUM_FULL_IMAGE_WIDTH+40)."','".($show->MAXIMUM_FULL_IMAGE_HEIGHT+40)."')\" class=zoom_link>";
						}
					}
				}
				if ($value["thumb_url"])
				{
					$url = $value["thumb_url"];
					$width = $final_image_width;
					$height = $final_image_height;
				}
				else
				{
					$url = $value["url"];
					$width = $final_image_width;
					$height = $final_image_height;
				}

				$tag .= $this->display_image($db, $url, $width, $height, $value['mime_type']);
			}
		}
		elseif ($value["type"] == 2)
		{
			//display the uploaded image
			if ($final_image_width != $value["original_image_width"])
			{
				if ($this->configuration_data['image_link_destination_type'])
					$tag = "<a href=".$this->configuration_data['classifieds_url']."?a=15&b=".$value["classified_id"].">";
				else
					$tag = "<a href=\"javascript:winimage('get_image.php?image=".$value["id"]."','".($value["original_image_width"]+40)."','".($value["original_image_height"]+40)."')\" class=zoom_link>";
			}
			$tag .=  "<img src=get_image.php?image=".$value["id"]." width=".$final_image_width." height=".$final_image_height." border=0>";
		}

		if ((strlen($value["image_text"]) > 0) && ($this->ad_configuration_data->MAXIMUM_IMAGE_DESCRIPTION))
		{
			if (strlen($value["image_text"]) <= $this->ad_configuration_data->MAXIMUM_IMAGE_DESCRIPTION)
				$tag .= "<br><font class=zoom_link>".$value["image_text"]."</font>";
			else
			{
				$small_string = substr($value["image_text"],0,$this->ad_configuration_data->MAXIMUM_IMAGE_DESCRIPTION);
				$position = strrpos($small_string," ");
				$smaller_string = substr($small_string,0,$position);
				$tag .= "<br><font class=\"zoom_link\">".$smaller_string."...</font>";
			}
		}
		if ($final_image_width != $value["original_image_width"])
			$tag .= "<br><font class=zoom_link>".urldecode($this->messages[339])."</font><font class=zoom_link>".urldecode($this->messages[12])."</font>";
		$tag .= "</a>";

		return $tag;

	} //end of function display_image_tag

//####################################################################################

	function get_image_data($db,$classified_id=0,$large=0)
	{
		if ($classified_id)
		{
			if (($this->ad_configuration_data->NUMBER_OF_PHOTOS_IN_DETAIL) && (!$large))
				$photo_limit = " order by display_order limit ".$this->ad_configuration_data->NUMBER_OF_PHOTOS_IN_DETAIL;
			else
				$photo_limit = " order by display_order ";  

			$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$classified_id.$photo_limit;
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);

			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				while ($show_urls = $result->FetchRow())
				{
					$this->images_to_display[$show_urls['display_order']]["type"] = 1;
					$this->images_to_display[$show_urls['display_order']]["id"] = $show_urls['image_id'];
					$this->images_to_display[$show_urls['display_order']]["image_width"] = $show_urls['image_width'];
					$this->images_to_display[$show_urls['display_order']]["image_height"] = $show_urls['image_height'];
					$this->images_to_display[$show_urls['display_order']]["original_image_width"] = $show_urls['original_image_width'];
					$this->images_to_display[$show_urls['display_order']]["original_image_height"] = $show_urls['original_image_height'];
					$this->images_to_display[$show_urls['display_order']]["url"] = $show_urls['image_url'];
					$this->images_to_display[$show_urls['display_order']]["classified_id"] = $show_urls['classified_id'];
					$this->images_to_display[$show_urls['display_order']]["image_text"] = $show_urls['image_text'];
					$this->images_to_display[$show_urls['display_order']]["thumb_url"] = $show_urls['thumb_url'];
					$this->images_to_display[$show_urls['display_order']]["icon"] = $show_urls['icon'];
					$this->images_to_display[$show_urls["display_order"]]["mime_type"] = $show_urls['mime_type'];
				}
			}

			$this->sql_query = "select * from ".$this->images_table." where classified_id = ".$classified_id.$photo_limit;
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);

			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				while ($show_uploaded = $result->FetchRow())
				{
					$this->images_to_display[$show_uploaded['display_order']]["type"] = 2;
					$this->images_to_display[$show_uploaded['display_order']]["id"] = $show_uploaded['image_id'];
					$this->images_to_display[$show_uploaded['display_order']]["image_width"] = $show_uploaded['image_width'];
					$this->images_to_display[$show_uploaded['display_order']]["image_height"] = $show_uploaded['image_height'];
					$this->images_to_display[$show_uploaded['display_order']]["original_image_width"] = $show_uploaded['original_image_width'];
					$this->images_to_display[$show_uploaded['display_order']]["original_image_height"] = $show_uploaded['original_image_height'];
					$this->images_to_display[$show_uploaded['display_order']]["image_file"] = $show_uploaded['image_file'];
					$this->images_to_display[$show_uploaded['display_order']]["classified_id"] = $show_uploaded['classified_id'];
					$this->images_to_display[$show_uploaded['display_order']]["image_text"] = $show_uploaded['image_text'];
					$this->images_to_display[$show_uploaded['display_order']]["thumb_file"] = $show_uploaded['thumb_file'];
					$this->images_to_display[$show_uploaded["display_order"]]["mime_type"] = $show_uploaded['mime_type'];
				}
			}
		}
		else
		{
			return false;
		}
	} //end of function get_image_data

//####################################################################################

	function get_form_variables ($info)
	{
		//get the variables from the form and save them
		if (is_array($info))
		{
			reset ($info);
			foreach ($info as $key => $value)
			//while (list($key,$value) = each($info))
			{
				if ($value != "none")
				{
					if (!is_array($value))
					{
						$this->classified_variables[$key] = stripslashes($value);
					}
					else
					{
						// NOTE:
						// Below is a work around to what we believe is a bug in PHP
						// If we assigned a value to any element in a sub-array of
						// the $this->classified_variables variable it would just grab the
						// first character of the string or number
						$this->temp_array = array();
						foreach ($value as $category_specific_key => $category_specific_value)
						{
							$this->temp_array[$category_specific_key] = stripslashes($category_specific_value);
							//highlight_string(print_r($this->temp_array, 1));
							//echo $key." is the category_specific_key - ".$category_specific_value."<br>\n";
						}
						$this->classified_variables[$key] = $this->temp_array;
						//highlight_string(print_r($this->classified_variables[$key], 1));
					}
					//echo $key." is the key and this is the value - ".$this->classified_variables[$key]."<br>\n";
				}
				elseif (($key == "state") || ($key == "country") || ($key == "mapping_state") || ($key == "mapping_country"))
				{
					$this->classified_variables[$key] = stripslashes($value);
					//echo $key." is the key and this is the value - ".$this->classified_variables[$key]."<br>\n";
				}
			}

			// highlight_string(print_r($this->classified_variables, 1));

			if ($this->classified_variables["auction_minimum"])
				$this->classified_variables["auction_minimum"] = str_replace(",","",$this->classified_variables["auction_minimum"]);
			if ($this->classified_variables["auction_buy_now"])
				$this->classified_variables["auction_buy_now"] = str_replace(",","",$this->classified_variables["auction_buy_now"]);
			if ($this->classified_variables["auction_reserve"])
				$this->classified_variables["auction_reserve"] = str_replace(",","",$this->classified_variables["auction_reserve"]);

			if(count($this->classified_variables["payment_options_from_form"]) > 0)
			{
				$count = 0;
				reset($this->classified_variables["payment_options_from_form"]);
				foreach ($this->classified_variables["payment_options_from_form"] as $key => $value)
				{
					//echo $key." is ".$value." while saving payment values from form<br>\n";
					if($count == 0 )
					{
						$this->classified_variables["payment_options"] = urldecode($value);
					}
					else
					{
						$this->classified_variables["payment_options"] .= "||".urldecode($value);
					}
					$count++;
				}
			}
		}
	} //end of function get_form_variables ($info)

//#####################################################################

	function classified_detail_check($db,$category_id=0)
	{
		if ($debug_detail_check)
		{
			echo "<br>TOP OF CLASSIFIED_DETAIL_CHECK<Br>\n";
		}
		$current_time = $this->shifted_time($db);
		if ($category_id)
			$this->get_category_configuration($db,$category_id);
		else
			$this->get_category_configuration($db,$this->terminal_category);
		$this->get_ad_configuration($db,1);
		//echo $this->category_configuration["use_site_default"]." is use site default<bR>\n";
		if (!$this->category_configuration["use_site_default"])
		{
			//echo "using site settings<br>\n";
			$this->field_configuration_data = $this->ad_configuration_data;
			$this->field_configuration_data["use_optional_field_1"] = $this->configuration_data['use_optional_field_1'];
			$this->field_configuration_data["use_optional_field_2"] = $this->configuration_data['use_optional_field_2'];
			$this->field_configuration_data["use_optional_field_3"] = $this->configuration_data['use_optional_field_3'];
			$this->field_configuration_data["use_optional_field_4"] = $this->configuration_data['use_optional_field_4'];
			$this->field_configuration_data["use_optional_field_5"] = $this->configuration_data['use_optional_field_5'];
			$this->field_configuration_data["use_optional_field_6"] = $this->configuration_data['use_optional_field_6'];
			$this->field_configuration_data["use_optional_field_7"] = $this->configuration_data['use_optional_field_7'];
			$this->field_configuration_data["use_optional_field_8"] = $this->configuration_data['use_optional_field_8'];
			$this->field_configuration_data["use_optional_field_9"] = $this->configuration_data['use_optional_field_9'];
			$this->field_configuration_data["use_optional_field_10"] = $this->configuration_data['use_optional_field_10'];
			$this->field_configuration_data["use_optional_field_11"] = $this->configuration_data['use_optional_field_11'];
			$this->field_configuration_data["use_optional_field_12"] = $this->configuration_data['use_optional_field_12'];
			$this->field_configuration_data["use_optional_field_13"] = $this->configuration_data['use_optional_field_13'];
			$this->field_configuration_data["use_optional_field_14"] = $this->configuration_data['use_optional_field_14'];
			$this->field_configuration_data["use_optional_field_15"] = $this->configuration_data['use_optional_field_15'];
			$this->field_configuration_data["use_optional_field_16"] = $this->configuration_data['use_optional_field_16'];
			$this->field_configuration_data["use_optional_field_17"] = $this->configuration_data['use_optional_field_17'];
			$this->field_configuration_data["use_optional_field_18"] = $this->configuration_data['use_optional_field_18'];
			$this->field_configuration_data["use_optional_field_19"] = $this->configuration_data['use_optional_field_19'];
			$this->field_configuration_data["use_optional_field_20"] = $this->configuration_data['use_optional_field_20'];
		}
		else
		{
			//echo "using category settings<br>\n";
			$this->field_configuration_data = $this->category_configuration;
		}

		$this->error = 0;
		unset($this->error_variables);
		$this->error_variables = array();

		//echo "about to check for badwords<Br>\n";
		$this->classified_variables["classified_title"] = strtr(stripslashes(urldecode($this->classified_variables["classified_title"])),"\"","'");
		//$this->classified_variables["classified_title"] = stripslashes(urldecode($this->classified_variables["classified_title"]));
		if ($this->debug) echo $this->classified_variables["classified_title"]." after stripslashes<Br>\n";
		$this->classified_variables["classified_title"] = substr($this->classified_variables["classified_title"],0,$this->ad_configuration_data["title_length"]);
		if ($this->debug) echo $this->classified_variables["classified_title"]." after maxlength<Br>\n";
		$this->classified_variables["classified_title"] = wordwrap($this->classified_variables["classified_title"],$this->configuration_data['max_word_width'], " \n",1);
		if ($this->debug) echo $this->classified_variables["classified_title"]." after wordwrap<Br>\n";
		$this->classified_variables["classified_title"] = $this->replace_disallowed_html($db,$this->classified_variables["classified_title"],0);
		if ($this->debug) echo $this->classified_variables["classified_title"]." after disallowed<Br>\n";
		$this->classified_variables["classified_title"] = $this->check_for_badwords($this->classified_variables["classified_title"]);
		if ($this->debug) echo $this->classified_variables["classified_title"]." after check for badwords<Br>\n";
		if (strlen(trim($this->classified_variables["classified_title"])) ==0)
		{
			//error in classified_title - was not entered
			$this->error++;
			$this->error_variables["classified_title"] = "error";
		}

		if ($this->field_configuration_data["use_state_field"])
		{
			if (($this->classified_variables["state"] == "none") || (strlen(trim($this->classified_variables["state"])) == 0))
			{
				//no state chosen
				$this->error++;
				$this->error_variables["state"] = "error";
			}
		}

		if ($this->field_configuration_data["use_country_field"])
		{
			if (($this->classified_variables["country"] == "none") || (strlen(trim($this->classified_variables["country"])) == 0))
			{
				//no country chosen
				//echo $this->classified_variables["country"]." is country check<br>\n";
				//echo $this->messages[232]." is the error message<br>\n";
				$this->error++;
				$this->error_variables["country"] = "error";
			}
		}

		if ($this->field_configuration_data["use_zip_field"])
		{
			$this->classified_variables["zip_code"] = stripslashes(urldecode($this->classified_variables["zip_code"]));
			$this->classified_variables["zip_code"] = wordwrap($this->classified_variables["zip_code"],$this->ad_configuration_data["zip_length"], " \n",1);
			$this->classified_variables["zip_code"] = $this->replace_disallowed_html($db,$this->classified_variables["zip_code"],1);
			$this->classified_variables["zip_code"] = $this->check_for_badwords($this->classified_variables["zip_code"]);
			if ($this->configuration_data['require_zip_field'])
			{
				if (strlen(trim($this->classified_variables["zip_code"])) == 0)
				{
					//error in classified_zip - was not entered
					$this->error++;
					$this->error_variables["zip_code"] = "error";

				}
				else
				{
					//check that zip is regulation size
					if (strlen(trim($this->classified_variables["zip_code"])) > 15)
					{
						//zip not long enough
						$this->error++;
						$this->error_variables["zip_code"] = "error";
					}
				}
			}
		}


		// Make sure minimum bid isnt 0.00
		if ($debug_detail_check)
		{
			echo $this->classified_variables["auction_minimum"]." is the auction_minimum<bR>\n";
			echo $this->classified_variables["auction_reserve"]." is the auction_reserve<bR>\n";
			echo $this->classified_variables["auction_buy_now"]." is the auction_buy_now<bR>\n";
		}

		if ($this->sell_type == 2)
			{
			if (!$this->classified_variables["auction_reserve"] || !ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["auction_reserve"]))
			{
				settype($this->classified_variables["auction_reserve"], "float");
				$this->classified_variables["auction_reserve"] = 0.00;
			}
			else
			{
				settype($this->classified_variables["auction_reserve"], "float");
			}

			if (!$this->classified_variables["auction_buy_now"] || !ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["auction_buy_now"]))
			{
				settype($this->classified_variables["auction_buy_now"], "float");
				$this->classified_variables["auction_buy_now"] = 0.00;
			}
			else
			{
				settype($this->classified_variables["auction_buy_now"], "float");
			}

			if (!$this->classified_variables["auction_minimum"] || !ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["auction_minimum"]))
			{
				settype($this->classified_variables["auction_minimum"], "float");
				$this->classified_variables["auction_minimum"] = 0.00;
			}
			else
			{
				settype($this->classified_variables["auction_minimum"], "float");
			}

			if (!$this->classified_variables["auction_minimum"] || $this->classified_variables["auction_minimum"] == 0.00)
			{
				$this->classified_variables["auction_minimum"] = 0.01;
			}
			elseif (strlen(trim($this->classified_variables["auction_minimum"])) > 0)
			{
				if (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["auction_minimum"]))
				{
					$this->classified_variables["auction_minimum"] = 0.01;
				}
			}
		}

		if(!$live)
		{
			// Find if buy_now is set for this user's price plan
			$buy_now = 0;
			$this->sql_query = "select buy_now_only from geodesic_sessions, ".$this->user_groups_price_plans_table." as p, ".$this->price_plans_table." as pp where classified_session like \"".$this->session_id."\" AND id = user_id AND p.price_plan_id = pp.price_plan_id";
			$result = $db->Execute($this->sql_query);
			if(!$result)
			{
				return false;
			}
			else
			{
				$buy_now_result = $result->FetchRow();
				$buy_now = $buy_now_result["buy_now_only"];
			}

			// If buy now show no errors unless buy now is 0.0
			if($buy_now)
			{
				$this->classified_variables["auction_reserve"] = 0.0;

				if($this->classified_variables["auction_buy_now"] <= 0.0)
				{
					$this->error++;
					$this->error_variables["auction_buy_now"] = "error";
				}
			}
			elseif ($this->classified_variables["auction_buy_now"] > 0)
			{
				if ($this->classified_variables["auction_reserve"] != 0.00)
				{
					if($this->classified_variables["auction_reserve"] < $this->classified_variables["auction_minimum"])
					{
						if ($debug_detail_check)
						{
							echo "the auction_reserve does not equal 0.00 and is smaller than the auction_minimum and buy now larger than 0<br>\n";
						}
						$this->error++;
						$this->error_variables["auction_reserve"] = "error";
					}
					elseif($this->classified_variables["auction_reserve"] >= $this->classified_variables["auction_minimum"])
					{
						if($this->classified_variables["auction_buy_now"] < $this->classified_variables["auction_reserve"])
						{
							$this->error++;
							$this->error_variables["auction_buy_now"] = "error";
						}
					}
				}

				if ($this->classified_variables["auction_buy_now"] < $this->classified_variables["auction_minimum"])
				{
					if ($debug_detail_check)
					{
						echo "the auction_buy_now is smaller than the auction_minimum yet greater than 0<br>\n";
					}
					$this->error++;
					$this->error_variables["auction_buy_now"] = "error";
				}
			}
			else
			{
				if (($this->classified_variables["auction_reserve"] < $this->classified_variables["auction_minimum"]) && ($this->classified_variables["auction_reserve"] != 0.00))
				{
					if ($debug_detail_check)
					{
						echo "the auction_reserve does not equal 0.00 and is smaller than the auction_minimum<br>\n";
					}
					$this->error++;
					$this->error_variables["auction_reserve"] = "error";
				}
			}
		}

		//payment type
		if ($this->configuration_data['payment_types'] && $this->sell_type==2)
		{
			if($this->debug_sell)
			{
				echo $this->classified_variables["payment_options"]." is the payment_options in detail_check<bR>";
			}
			if((strlen(trim($this->classified_variables["payment_options"])) == 0) && ($this->configuration_data['payment_types_use']))
			{
				$this->error++;
				$this->error_variables["payment_options"] = "error";
			}
		}

		if ($this->field_configuration_data["use_optional_field_1"])
		{
			if (($this->ad_configuration_data["optional_1_other_box"]) && (strlen(trim($this->classified_variables["optional_field_1_other"])) > 0))
				$this->classified_variables["optional_field_1"] = $this->classified_variables["optional_field_1_other"];
			if ($this->ad_configuration_data["optional_1_length"] > 0)
				$this->classified_variables["optional_field_1"] = substr($this->classified_variables["optional_field_1"],0,$this->ad_configuration_data["optional_1_length"]);
			if (($this->ad_configuration_data["optional_1_other_box"]) || (!$this->ad_configuration_data["optional_1_field_type"]))
			{
				$this->classified_variables["optional_field_1"] = stripslashes(urldecode($this->classified_variables["optional_field_1"]));
				$this->classified_variables["optional_field_1"] = wordwrap($this->classified_variables["optional_field_1"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_1"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_1"],0);
				$this->classified_variables["optional_field_1"] = $this->check_for_badwords($this->classified_variables["optional_field_1"]);
			}
			if ($this->ad_configuration_data["optional_1_number_only"])
			{
				$this->classified_variables["optional_field_1"] = str_replace(",","",$this->classified_variables["optional_field_1"]);
				if ((strlen(trim($this->classified_variables["optional_field_1"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_1"])))
				{
					//echo "error in number only optional 1<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_1"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_1'])
			{
				if (strlen(trim($this->classified_variables["optional_field_1"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_1"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_2"])
		{
			if (($this->ad_configuration_data["optional_2_other_box"]) && (strlen(trim($this->classified_variables["optional_field_2_other"])) > 0))
				$this->classified_variables["optional_field_2"] = $this->classified_variables["optional_field_2_other"];
			if ($this->ad_configuration_data["optional_2_length"] > 0)
				$this->classified_variables["optional_field_2"] = substr($this->classified_variables["optional_field_2"],0,$this->ad_configuration_data["optional_2_length"]);

			if (($this->ad_configuration_data["optional_2_other_box"]) || (!$this->ad_configuration_data["optional_2_field_type"]))
			{
				$this->classified_variables["optional_field_2"] = stripslashes(urldecode($this->classified_variables["optional_field_2"]));
				$this->classified_variables["optional_field_2"] = wordwrap($this->classified_variables["optional_field_2"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_2"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_2"],0);
				$this->classified_variables["optional_field_2"] = $this->check_for_badwords($this->classified_variables["optional_field_2"]);
			}
			if ($this->ad_configuration_data["optional_2_number_only"])
			{
				$this->classified_variables["optional_field_2"] = str_replace(",","",$this->classified_variables["optional_field_2"]);
				if ((strlen(trim($this->classified_variables["optional_field_2"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_2"])))
				{
					//echo "error in number only optional 2<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_2"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_2'])
			{
				if (strlen(trim($this->classified_variables["optional_field_2"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_2"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_3"])
		{
			if (($this->ad_configuration_data["optional_3_other_box"]) && (strlen(trim($this->classified_variables["optional_field_3_other"])) > 0))
				$this->classified_variables["optional_field_3"] = $this->classified_variables["optional_field_3_other"];
			if ($this->ad_configuration_data["optional_3_length"] > 0)
				$this->classified_variables["optional_field_3"] = substr($this->classified_variables["optional_field_3"],0,$this->ad_configuration_data["optional_3_length"]);

			if (($this->ad_configuration_data["optional_3_other_box"]) || (!$this->ad_configuration_data["optional_3_field_type"]))
			{
				$this->classified_variables["optional_field_3"] = stripslashes(urldecode($this->classified_variables["optional_field_3"]));
				$this->classified_variables["optional_field_3"] = wordwrap($this->classified_variables["optional_field_3"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_3"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_3"],0);
				$this->classified_variables["optional_field_3"] = $this->check_for_badwords($this->classified_variables["optional_field_3"]);
			}
			if ($this->ad_configuration_data["optional_3_number_only"])
			{
				$this->classified_variables["optional_field_3"] = str_replace(",","",$this->classified_variables["optional_field_3"]);
				if ((strlen(trim($this->classified_variables["optional_field_3"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_3"])))
				{
					//echo "error in number only optional 3<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_3"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_3'])
			{
				if (strlen(trim($this->classified_variables["optional_field_3"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_3"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_4"])
		{
			if (($this->ad_configuration_data["optional_4_other_box"]) && (strlen(trim($this->classified_variables["optional_field_4_other"])) > 0))
				$this->classified_variables["optional_field_4"] = $this->classified_variables["optional_field_4_other"];
			if ($this->ad_configuration_data["optional_4_length"] > 0)
				$this->classified_variables["optional_field_4"] = substr($this->classified_variables["optional_field_4"],0,$this->ad_configuration_data["optional_4_length"]);

			if (($this->ad_configuration_data["optional_4_other_box"]) || (!$this->ad_configuration_data["optional_4_field_type"]))
			{
				$this->classified_variables["optional_field_4"] = stripslashes(urldecode($this->classified_variables["optional_field_4"]));
				$this->classified_variables["optional_field_4"] = wordwrap($this->classified_variables["optional_field_4"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_4"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_4"],0);
				$this->classified_variables["optional_field_4"] = $this->check_for_badwords($this->classified_variables["optional_field_4"]);
			}
			if ($this->ad_configuration_data["optional_4_number_only"])
			{
				$this->classified_variables["optional_field_4"] = str_replace(",","",$this->classified_variables["optional_field_4"]);
				if ((strlen(trim($this->classified_variables["optional_field_4"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_4"])))
				{
					//echo "error in number only optional 4<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_4"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_4'])
			{
				if (strlen(trim($this->classified_variables["optional_field_4"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_4"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_5"])
		{
			if (($this->ad_configuration_data["optional_5_other_box"]) && (strlen(trim($this->classified_variables["optional_field_5_other"])) > 0))
				$this->classified_variables["optional_field_5"] = $this->classified_variables["optional_field_5_other"];
			if ($this->ad_configuration_data["optional_5_length"] > 0)
				$this->classified_variables["optional_field_5"] = substr($this->classified_variables["optional_field_5"],0,$this->ad_configuration_data["optional_5_length"]);

			if (($this->ad_configuration_data["optional_5_other_box"]) || (!$this->ad_configuration_data["optional_5_field_type"]))
			{
				$this->classified_variables["optional_field_5"] = stripslashes(urldecode($this->classified_variables["optional_field_5"]));
				$this->classified_variables["optional_field_5"] = wordwrap($this->classified_variables["optional_field_5"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_5"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_5"],0);
				$this->classified_variables["optional_field_5"] = $this->check_for_badwords($this->classified_variables["optional_field_5"]);
			}
			if ($this->ad_configuration_data["optional_5_number_only"])
			{
				$this->classified_variables["optional_field_5"] = str_replace(",","",$this->classified_variables["optional_field_5"]);
				if ((strlen(trim($this->classified_variables["optional_field_5"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_5"])))
				{
					//echo "error in number only optional 5<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_5"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_5'])
			{
				if (strlen(trim($this->classified_variables["optional_field_5"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_5"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_6"])
		{
			if (($this->ad_configuration_data["optional_6_other_box"]) && (strlen(trim($this->classified_variables["optional_field_6_other"])) > 0))
				$this->classified_variables["optional_field_6"] = $this->classified_variables["optional_field_6_other"];
			if ($this->ad_configuration_data["optional_6_length"] > 0)
				$this->classified_variables["optional_field_6"] = substr($this->classified_variables["optional_field_6"],0,$this->ad_configuration_data["optional_6_length"]);

			if (($this->ad_configuration_data["optional_6_other_box"]) || (!$this->ad_configuration_data["optional_6_field_type"]))
			{
				$this->classified_variables["optional_field_6"] = stripslashes(urldecode($this->classified_variables["optional_field_6"]));
				$this->classified_variables["optional_field_6"] = wordwrap($this->classified_variables["optional_field_6"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_6"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_6"],0);
				$this->classified_variables["optional_field_6"] = $this->check_for_badwords($this->classified_variables["optional_field_6"]);
			}
			if ($this->ad_configuration_data["optional_6_number_only"])
			{
				$this->classified_variables["optional_field_6"] = str_replace(",","",$this->classified_variables["optional_field_6"]);
				if ((strlen(trim($this->classified_variables["optional_field_6"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_6"])))
				{
					//echo "error in number only optional 6<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_6"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_6'])
			{
				if (strlen(trim($this->classified_variables["optional_field_6"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_6"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_7"])
		{
			if (($this->ad_configuration_data["optional_7_other_box"]) && (strlen(trim($this->classified_variables["optional_field_7_other"])) > 0))
				$this->classified_variables["optional_field_7"] = $this->classified_variables["optional_field_7_other"];
			if ($this->ad_configuration_data["optional_7_length"] > 0)
				$this->classified_variables["optional_field_7"] = substr($this->classified_variables["optional_field_7"],0,$this->ad_configuration_data["optional_7_length"]);

			if (($this->ad_configuration_data["optional_7_other_box"]) || (!$this->ad_configuration_data["optional_7_field_type"]))
			{
				$this->classified_variables["optional_field_7"] = stripslashes(urldecode($this->classified_variables["optional_field_7"]));
				$this->classified_variables["optional_field_7"] = wordwrap($this->classified_variables["optional_field_7"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_7"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_7"],0);
				$this->classified_variables["optional_field_7"] = $this->check_for_badwords($this->classified_variables["optional_field_7"]);
			}
			if ($this->ad_configuration_data["optional_7_number_only"])
			{
				$this->classified_variables["optional_field_7"] = str_replace(",","",$this->classified_variables["optional_field_7"]);
				if ((strlen(trim($this->classified_variables["optional_field_7"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_7"])))
				{
					//echo "error in number only optional 7<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_7"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_7'])
			{
				if (strlen(trim($this->classified_variables["optional_field_7"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_7"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_8"])
		{
			if (($this->ad_configuration_data["optional_8_other_box"]) && (strlen(trim($this->classified_variables["optional_field_8_other"])) > 0))
				$this->classified_variables["optional_field_8"] = $this->classified_variables["optional_field_8_other"];
			if ($this->ad_configuration_data["optional_8_length"] > 0)
				$this->classified_variables["optional_field_8"] = substr($this->classified_variables["optional_field_8"],0,$this->ad_configuration_data["optional_8_length"]);

			if (($this->ad_configuration_data["optional_8_other_box"]) || (!$this->ad_configuration_data["optional_8_field_type"]))
			{
				$this->classified_variables["optional_field_8"] = stripslashes(urldecode($this->classified_variables["optional_field_8"]));
				$this->classified_variables["optional_field_8"] = wordwrap($this->classified_variables["optional_field_8"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_8"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_8"],0);
				$this->classified_variables["optional_field_8"] = $this->check_for_badwords($this->classified_variables["optional_field_8"]);
			}
			if ($this->ad_configuration_data["optional_8_number_only"])
			{
				$this->classified_variables["optional_field_8"] = str_replace(",","",$this->classified_variables["optional_field_8"]);
				if ((strlen(trim($this->classified_variables["optional_field_8"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_8"])))
				{
					//echo "error in number only optional 8<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_8"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_8'])
			{
				if (strlen(trim($this->classified_variables["optional_field_8"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_8"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_9"])
		{
			if (($this->ad_configuration_data["optional_9_other_box"]) && (strlen(trim($this->classified_variables["optional_field_9_other"])) > 0))
				$this->classified_variables["optional_field_9"] = $this->classified_variables["optional_field_9_other"];
			if ($this->ad_configuration_data["optional_9_length"] > 0)
				$this->classified_variables["optional_field_9"] = substr($this->classified_variables["optional_field_9"],0,$this->ad_configuration_data["optional_9_length"]);

			if (($this->ad_configuration_data["optional_9_other_box"]) || (!$this->ad_configuration_data["optional_9_field_type"]))
			{
				$this->classified_variables["optional_field_9"] = stripslashes(urldecode($this->classified_variables["optional_field_9"]));
				$this->classified_variables["optional_field_9"] = wordwrap($this->classified_variables["optional_field_9"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_9"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_9"],0);
				$this->classified_variables["optional_field_9"] = $this->check_for_badwords($this->classified_variables["optional_field_9"]);
			}
			if ($this->ad_configuration_data["optional_9_number_only"])
			{
				$this->classified_variables["optional_field_9"] = str_replace(",","",$this->classified_variables["optional_field_9"]);
				if ((strlen(trim($this->classified_variables["optional_field_9"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_9"])))
				{
					//echo "error in number only optional 9<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_9"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_9'])
			{
				if (strlen(trim($this->classified_variables["optional_field_9"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_9"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_10"])
		{
			if (($this->ad_configuration_data["optional_10_other_box"]) && (strlen(trim($this->classified_variables["optional_field_10_other"])) > 0))
				$this->classified_variables["optional_field_10"] = $this->classified_variables["optional_field_10_other"];
			if ($this->ad_configuration_data["optional_10_length"] > 0)
				$this->classified_variables["optional_field_10"] = substr($this->classified_variables["optional_field_10"],0,$this->ad_configuration_data["optional_10_length"]);

			if (($this->ad_configuration_data["optional_10_other_box"]) || (!$this->ad_configuration_data["optional_10_field_type"]))
			{
				$this->classified_variables["optional_field_10"] = stripslashes(urldecode($this->classified_variables["optional_field_10"]));
				$this->classified_variables["optional_field_10"] = wordwrap($this->classified_variables["optional_field_10"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_10"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_10"],0);
				$this->classified_variables["optional_field_10"] = $this->check_for_badwords($this->classified_variables["optional_field_10"]);
			}
			if ($this->ad_configuration_data["optional_10_number_only"])
			{
				$this->classified_variables["optional_field_10"] = str_replace(",","",$this->classified_variables["optional_field_10"]);
				if ((strlen(trim($this->classified_variables["optional_field_10"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_10"])))
				{
					//echo "error in number only optional 10<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_10"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_10'])
			{
				if (strlen(trim($this->classified_variables["optional_field_10"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_10"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_11"])
		{
			if (($this->ad_configuration_data["optional_11_other_box"]) && (strlen(trim($this->classified_variables["optional_field_11_other"])) > 0))
				$this->classified_variables["optional_field_11"] = $this->classified_variables["optional_field_11_other"];
			if ($this->ad_configuration_data['optional_11_length'] > 0)
				$this->classified_variables["optional_field_11"] = substr($this->classified_variables["optional_field_11"],0,$this->ad_configuration_data["optional_11_length"]);
			if (($this->ad_configuration_data["optional_11_other_box"]) || (!$this->ad_configuration_data["optional_11_field_type"]))
			{
				$this->classified_variables["optional_field_11"] = stripslashes(urldecode($this->classified_variables["optional_field_11"]));
				$this->classified_variables["optional_field_11"] = wordwrap($this->classified_variables["optional_field_11"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_11"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_11"],0);
				$this->classified_variables["optional_field_11"] = $this->check_for_badwords($this->classified_variables["optional_field_11"]);
			}
			if ($this->ad_configuration_data["optional_11_number_only"])
			{
				$this->classified_variables["optional_field_11"] = str_replace(",","",$this->classified_variables["optional_field_11"]);
				if ((strlen(trim($this->classified_variables["optional_field_11"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_11"])))
				{
					//echo "error in number only optional 1<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_11"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_11'])
			{
				if (strlen(trim($this->classified_variables["optional_field_11"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_11"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_12"])
		{
			if (($this->ad_configuration_data["optional_12_other_box"]) && (strlen(trim($this->classified_variables["optional_field_12_other"])) > 0))
				$this->classified_variables["optional_field_12"] = $this->classified_variables["optional_field_12_other"];
			if ($this->ad_configuration_data['optional_12_length'] > 0)
				$this->classified_variables["optional_field_12"] = substr($this->classified_variables["optional_field_12"],0,$this->ad_configuration_data["optional_12_length"]);
			if (($this->ad_configuration_data["optional_12_other_box"]) || (!$this->ad_configuration_data["optional_12_field_type"]))
			{
				$this->classified_variables["optional_field_12"] = stripslashes(urldecode($this->classified_variables["optional_field_12"]));
				$this->classified_variables["optional_field_12"] = wordwrap($this->classified_variables["optional_field_12"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_12"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_12"],0);
				$this->classified_variables["optional_field_12"] = $this->check_for_badwords($this->classified_variables["optional_field_12"]);
			}
			if ($this->ad_configuration_data["optional_12_number_only"])
			{
				$this->classified_variables["optional_field_12"] = str_replace(",","",$this->classified_variables["optional_field_12"]);
				if ((strlen(trim($this->classified_variables["optional_field_12"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_12"])))
				{
					//echo "error in number only optional 1<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_12"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_12'])
			{
				if (strlen(trim($this->classified_variables["optional_field_12"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_12"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_13"])
		{
			if (($this->ad_configuration_data["optional_13_other_box"]) && (strlen(trim($this->classified_variables["optional_field_13_other"])) > 0))
				$this->classified_variables["optional_field_13"] = $this->classified_variables["optional_field_13_other"];
			if ($this->ad_configuration_data['optional_13_length'] > 0)
				$this->classified_variables["optional_field_13"] = substr($this->classified_variables["optional_field_13"],0,$this->ad_configuration_data["optional_13_length"]);
			if (($this->ad_configuration_data["optional_13_other_box"]) || (!$this->ad_configuration_data["optional_13_field_type"]))
			{
				$this->classified_variables["optional_field_13"] = stripslashes(urldecode($this->classified_variables["optional_field_13"]));
				$this->classified_variables["optional_field_13"] = wordwrap($this->classified_variables["optional_field_13"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_13"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_13"],0);
				$this->classified_variables["optional_field_13"] = $this->check_for_badwords($this->classified_variables["optional_field_13"]);
			}
			if ($this->ad_configuration_data["optional_13_number_only"])
			{
				$this->classified_variables["optional_field_13"] = str_replace(",","",$this->classified_variables["optional_field_13"]);
				if ((strlen(trim($this->classified_variables["optional_field_13"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_13"])))
				{
					//echo "error in number only optional 1<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_13"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_13'])
			{
				if (strlen(trim($this->classified_variables["optional_field_13"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_13"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_14"])
		{
			if (($this->ad_configuration_data["optional_14_other_box"]) && (strlen(trim($this->classified_variables["optional_field_14_other"])) > 0))
				$this->classified_variables["optional_field_14"] = $this->classified_variables["optional_field_14_other"];
			if ($this->ad_configuration_data['optional_14_length'] > 0)
				$this->classified_variables["optional_field_14"] = substr($this->classified_variables["optional_field_14"],0,$this->ad_configuration_data["optional_14_length"]);
			if (($this->ad_configuration_data["optional_14_other_box"]) || (!$this->ad_configuration_data["optional_14_field_type"]))
			{
				$this->classified_variables["optional_field_14"] = stripslashes(urldecode($this->classified_variables["optional_field_14"]));
				$this->classified_variables["optional_field_14"] = wordwrap($this->classified_variables["optional_field_14"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_14"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_14"],0);
				$this->classified_variables["optional_field_14"] = $this->check_for_badwords($this->classified_variables["optional_field_14"]);
			}
			if ($this->ad_configuration_data["optional_14_number_only"])
			{
				$this->classified_variables["optional_field_14"] = str_replace(",","",$this->classified_variables["optional_field_14"]);
				if ((strlen(trim($this->classified_variables["optional_field_14"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_14"])))
				{
					//echo "error in number only optional 1<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_14"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_14'])
			{
				if (strlen(trim($this->classified_variables["optional_field_14"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_14"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_15"])
		{
			if (($this->ad_configuration_data["optional_15_other_box"]) && (strlen(trim($this->classified_variables["optional_field_15_other"])) > 0))
				$this->classified_variables["optional_field_15"] = $this->classified_variables["optional_field_15_other"];
			if ($this->ad_configuration_data['optional_15_length'] > 0)
				$this->classified_variables["optional_field_15"] = substr($this->classified_variables["optional_field_15"],0,$this->ad_configuration_data["optional_15_length"]);
			if (($this->ad_configuration_data["optional_15_other_box"]) || (!$this->ad_configuration_data["optional_15_field_type"]))
			{
				$this->classified_variables["optional_field_15"] = stripslashes(urldecode($this->classified_variables["optional_field_15"]));
				$this->classified_variables["optional_field_15"] = wordwrap($this->classified_variables["optional_field_15"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_15"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_15"],0);
				$this->classified_variables["optional_field_15"] = $this->check_for_badwords($this->classified_variables["optional_field_15"]);
			}
			if ($this->ad_configuration_data["optional_15_number_only"])
			{
				$this->classified_variables["optional_field_15"] = str_replace(",","",$this->classified_variables["optional_field_15"]);
				if ((strlen(trim($this->classified_variables["optional_field_15"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_15"])))
				{
					//echo "error in number only optional 1<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_15"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_15'])
			{
				if (strlen(trim($this->classified_variables["optional_field_15"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_15"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_16"])
		{
			if (($this->ad_configuration_data["optional_16_other_box"]) && (strlen(trim($this->classified_variables["optional_field_16_other"])) > 0))
				$this->classified_variables["optional_field_16"] = $this->classified_variables["optional_field_16_other"];
			if ($this->ad_configuration_data['optional_16_length'] > 0)
				$this->classified_variables["optional_field_16"] = substr($this->classified_variables["optional_field_16"],0,$this->ad_configuration_data["optional_16_length"]);
			if (($this->ad_configuration_data["optional_16_other_box"]) || (!$this->ad_configuration_data["optional_16_field_type"]))
			{
				$this->classified_variables["optional_field_16"] = stripslashes(urldecode($this->classified_variables["optional_field_16"]));
				$this->classified_variables["optional_field_16"] = wordwrap($this->classified_variables["optional_field_16"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_16"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_16"],0);
				$this->classified_variables["optional_field_16"] = $this->check_for_badwords($this->classified_variables["optional_field_16"]);
			}
			if ($this->ad_configuration_data["optional_16_number_only"])
			{
				$this->classified_variables["optional_field_16"] = str_replace(",","",$this->classified_variables["optional_field_16"]);
				if ((strlen(trim($this->classified_variables["optional_field_16"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_16"])))
				{
					//echo "error in number only optional 1<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_16"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_16'])
			{
				if (strlen(trim($this->classified_variables["optional_field_16"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_16"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_17"])
		{
			if (($this->ad_configuration_data["optional_17_other_box"]) && (strlen(trim($this->classified_variables["optional_field_17_other"])) > 0))
				$this->classified_variables["optional_field_17"] = $this->classified_variables["optional_field_17_other"];
			if ($this->ad_configuration_data['optional_17_length'] > 0)
				$this->classified_variables["optional_field_17"] = substr($this->classified_variables["optional_field_17"],0,$this->ad_configuration_data["optional_17_length"]);
			if (($this->ad_configuration_data["optional_17_other_box"]) || (!$this->ad_configuration_data["optional_17_field_type"]))
			{
				$this->classified_variables["optional_field_17"] = stripslashes(urldecode($this->classified_variables["optional_field_17"]));
				$this->classified_variables["optional_field_17"] = wordwrap($this->classified_variables["optional_field_17"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_17"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_17"],0);
				$this->classified_variables["optional_field_17"] = $this->check_for_badwords($this->classified_variables["optional_field_17"]);
			}
			if ($this->ad_configuration_data["optional_17_number_only"])
			{
				$this->classified_variables["optional_field_17"] = str_replace(",","",$this->classified_variables["optional_field_17"]);
				if ((strlen(trim($this->classified_variables["optional_field_17"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_17"])))
				{
					//echo "error in number only optional 1<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_17"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_17'])
			{
				if (strlen(trim($this->classified_variables["optional_field_17"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_17"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_18"])
		{
			if (($this->ad_configuration_data["optional_18_other_box"]) && (strlen(trim($this->classified_variables["optional_field_18_other"])) > 0))
				$this->classified_variables["optional_field_18"] = $this->classified_variables["optional_field_18_other"];
			if ($this->ad_configuration_data['optional_18_length'] > 0)
				$this->classified_variables["optional_field_18"] = substr($this->classified_variables["optional_field_18"],0,$this->ad_configuration_data["optional_18_length"]);
			if (($this->ad_configuration_data["optional_18_other_box"]) || (!$this->ad_configuration_data["optional_18_field_type"]))
			{
				$this->classified_variables["optional_field_18"] = stripslashes(urldecode($this->classified_variables["optional_field_18"]));
				$this->classified_variables["optional_field_18"] = wordwrap($this->classified_variables["optional_field_18"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_18"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_18"],0);
				$this->classified_variables["optional_field_18"] = $this->check_for_badwords($this->classified_variables["optional_field_18"]);
			}
			if ($this->ad_configuration_data["optional_18_number_only"])
			{
				$this->classified_variables["optional_field_18"] = str_replace(",","",$this->classified_variables["optional_field_18"]);
				if ((strlen(trim($this->classified_variables["optional_field_18"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_18"])))
				{
					//echo "error in number only optional 1<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_18"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_18'])
			{
				if (strlen(trim($this->classified_variables["optional_field_18"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_18"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_19"])
		{
			if (($this->ad_configuration_data["optional_19_other_box"]) && (strlen(trim($this->classified_variables["optional_field_19_other"])) > 0))
				$this->classified_variables["optional_field_19"] = $this->classified_variables["optional_field_19_other"];
			if ($this->ad_configuration_data['optional_19_length'] > 0)
				$this->classified_variables["optional_field_19"] = substr($this->classified_variables["optional_field_19"],0,$this->ad_configuration_data["optional_19_length"]);
			if (($this->ad_configuration_data["optional_19_other_box"]) || (!$this->ad_configuration_data["optional_19_field_type"]))
			{
				$this->classified_variables["optional_field_19"] = stripslashes(urldecode($this->classified_variables["optional_field_19"]));
				$this->classified_variables["optional_field_19"] = wordwrap($this->classified_variables["optional_field_19"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_19"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_19"],0);
				$this->classified_variables["optional_field_19"] = $this->check_for_badwords($this->classified_variables["optional_field_19"]);
			}
			if ($this->ad_configuration_data["optional_19_number_only"])
			{
				$this->classified_variables["optional_field_19"] = str_replace(",","",$this->classified_variables["optional_field_19"]);
				if ((strlen(trim($this->classified_variables["optional_field_19"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_19"])))
				{
					//echo "error in number only optional 1<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_19"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_19'])
			{
				if (strlen(trim($this->classified_variables["optional_field_19"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_19"] = "error";
				}
			}
		}

		if ($this->field_configuration_data["use_optional_field_20"])
		{
			if (($this->ad_configuration_data["optional_20_other_box"]) && (strlen(trim($this->classified_variables["optional_field_20_other"])) > 0))
				$this->classified_variables["optional_field_20"] = $this->classified_variables["optional_field_20_other"];
			if ($this->ad_configuration_data['optional_20_length'] > 0)
				$this->classified_variables["optional_field_20"] = substr($this->classified_variables["optional_field_20"],0,$this->ad_configuration_data["optional_20_length"]);
			if (($this->ad_configuration_data["optional_20_other_box"]) || (!$this->ad_configuration_data["optional_20_field_type"]))
			{
				$this->classified_variables["optional_field_20"] = stripslashes(urldecode($this->classified_variables["optional_field_20"]));
				$this->classified_variables["optional_field_20"] = wordwrap($this->classified_variables["optional_field_20"],$this->configuration_data['max_word_width'], " \n",1);
				$this->classified_variables["optional_field_20"] = $this->replace_disallowed_html($db,$this->classified_variables["optional_field_20"],0);
				$this->classified_variables["optional_field_20"] = $this->check_for_badwords($this->classified_variables["optional_field_20"]);
			}
			if ($this->ad_configuration_data["optional_20_number_only"])
			{
				$this->classified_variables["optional_field_20"] = str_replace(",","",$this->classified_variables["optional_field_20"]);
				if ((strlen(trim($this->classified_variables["optional_field_20"])) > 0) && (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["optional_field_20"])))
				{
					//echo "error in number only optional 1<Br>\n";
					$this->error++;
					$this->error_variables["optional_field_20"] = "error_number";
				}
			}
			if ($this->configuration_data['require_optional_field_20'])
			{
				if (strlen(trim($this->classified_variables["optional_field_20"])) == 0)
				{
					$this->error++;
					$this->error_variables["optional_field_20"] = "error";
				}
			}
		}

		if ($this->field_configuration_data->USE_URL_LINK_1)
		{
			if ($this->ad_configuration_data["url_link_1_length"] > 0)
				$this->classified_variables["url_link_1"] = substr($this->classified_variables["url_link_1"],0,$this->ad_configuration_data["url_link_1_length"]);
			$this->classified_variables["url_link_1"] = stripslashes(urldecode($this->classified_variables["url_link_1"]));
			$this->classified_variables["url_link_1"] = $this->replace_disallowed_html($db,$this->classified_variables["url_link_1"],1);
			$this->classified_variables["url_link_1"] = $this->check_for_badwords($this->classified_variables["url_link_1"]);

			if ($this->configuration_data['require_url_link_1'])
			{
				if (strlen(trim($this->classified_variables["url_link_1"])) == 0)
				{
					$this->error++;
					$this->error_variables["url_link_1"] = "error";
				}
			}
		}

		if ($this->field_configuration_data->USE_URL_LINK_2)
		{
			if ($this->ad_configuration_data["url_link_2_length"] > 0)
				$this->classified_variables["url_link_2"] = substr($this->classified_variables["url_link_2"],0,$this->ad_configuration_data["url_link_2_length"]);
			$this->classified_variables["url_link_2"] = stripslashes(urldecode($this->classified_variables["url_link_2"]));
			$this->classified_variables["url_link_2"] = $this->replace_disallowed_html($db,$this->classified_variables["url_link_2"],1);
			$this->classified_variables["url_link_2"] = $this->check_for_badwords($this->classified_variables["url_link_2"]);

			if ($this->configuration_data['require_url_link_2'])
			{
				if (strlen(trim($this->classified_variables["url_link_2"])) == 0)
				{
					$this->error++;
					$this->error_variables["url_link_2"] = "error";
				}
			}
		}

		if ($this->field_configuration_data->USE_URL_LINK_3)
		{
			if ($this->ad_configuration_data["url_link_3_length"] > 0)
				$this->classified_variables["url_link_3"] = substr($this->classified_variables["url_link_3"],0,$this->ad_configuration_data["url_link_3_length"]);
			$this->classified_variables["url_link_3"] = stripslashes(urldecode($this->classified_variables["url_link_3"]));
			$this->classified_variables["url_link_3"] = $this->replace_disallowed_html($db,$this->classified_variables["url_link_3"],1);
			$this->classified_variables["url_link_3"] = $this->check_for_badwords($this->classified_variables["url_link_3"]);

			if ($this->configuration_data['require_url_link_3'])
			{
				if (strlen(trim($this->classified_variables["url_link_3"])) == 0)
				{
					$this->error++;
					$this->error_variables["url_link_3"] = "error";
				}
			}
		}
		if ($this->field_configuration_data->USE_BUY_NOW && $this->field_configuration_data->REQUIRE_BUY_NOW && !strlen($this->classified_variables["paypal_id"]))
		{
			$this->error++;
			$this->error_variables["paypal_id"] = "error";
		}
		if ($this->field_configuration_data->USE_EMAIL_OPTION_FIELD)
		{
			if ((!eregi("^([a-z0-9]+)(([a-z0-9._-]+))*[@]([a-z0-9]+)([._-]([a-z0-9]+))*[.]([a-z0-9]){2,3}([a-z0-9])?$", $this->classified_variables[email_option])) ||
				(strlen(trim($this->classified_variables[email_option])) == 0))
			{
				$this->error_variables["email_option"] ="error1";
				$this->error++;
			}
		}

		if ($this->field_configuration_data->USE_PRICE_FIELD)
		{
			if ($this->classified_user_id)
			{
				$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
				$plan_result = $db->Execute($this->sql_query);
				if (!$plan_result)
				{
					//echo $this->sql_query."<br>\n";
					return false;
				}
				elseif ($plan_result->RecordCount() == 1)
				{
					$show_price_plan_id = $plan_result->FetchNextObject();
					$this->sql_query = "select type_of_billing, charge_per_ad_type from ".$this->price_plans_table." where price_plan_id = ".$show_price_plan_id->PRICE_PLAN_ID;
					$price_plan_result = $db->Execute($this->sql_query);
					if (!$price_plan_result)
					{
						//echo $this->sql_query."<br>\n";
						return false;
					}
					elseif ($price_plan_result->RecordCount() == 1)
					{
						$show_price_plan = $price_plan_result->FetchNextObject();
						if (($show_price_plan->TYPE_OF_BILLING == 1) && ($show_price_plan->CHARGE_PER_AD_TYPE == 1))
						{
							$this->classified_variables["price"] = trim(str_replace(",","",$this->classified_variables["price"]));
							//update price
							if ($this->session_id)
							{
								$this->sql_query = "update ".$this->sell_table." set
									price = \"".addslashes($this->classified_variables["price"])."\"
									where session = \"".$this->session_id."\"";
								$save_price_result = $db->Execute($this->sql_query);
								//echo $this->sql_query."<br>\n";
								if (!$save_price_result)
								{
									//echo $this->sql_query."<br>\n";
								}
							}
							if (strlen($this->classified_variables["price"]) == 0)
							{
								$this->error++;
								$this->error_variables["price"] = "error";
							}


						}
						else
						{
							if (strlen($this->classified_variables["price"]) == 0)
							{
								$this->error++;
								$this->error_variables["price"] = "error";
							}
						}
					}
				}
			}
			$this->classified_variables["price"] = trim(str_replace(",","",$this->classified_variables["price"]));
			$this->classified_variables["price"] = substr($this->classified_variables["price"],0,$this->ad_configuration_data["price_length"]);
			if ((!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $this->classified_variables["price"])))
			{
				//error in classified_zip - was not entered
				$this->error++;
				$this->error_variables["price"] = "error";
			}
		}

		$this->classified_variables["start_time"] = $this->get_time($this->classified_variables["start_time"]["start_hour"],$this->classified_variables["start_time"]["start_minute"],$this->classified_variables["start_time"]["start_month"],$this->classified_variables["start_time"]["start_day"],$this->classified_variables["start_time"]["start_year"]);
		if ($this->classified_variables["start_time"] <  $current_time)
			$this->classified_variables["start_time"] = $current_time;
		$this->classified_variables["end_time"] = $this->get_time($this->classified_variables["end_time"]["end_hour"],$this->classified_variables["end_time"]["end_minute"],$this->classified_variables["end_time"]["end_month"],$this->classified_variables["end_time"]["end_day"],$this->classified_variables["end_time"]["end_year"]);

		if ($debug_detail_check)
		{
			echo $this->classified_variables["start_time"]." before start time <bR>";
			echo $this->classified_variables["end_time"]." before end time <bR>";
			echo $this->classified_variables["classified_length"]." before auction length <bR>";
			echo $current_time." is current_time 2<br>\n";
		}

		if ($this->sell_type == 2)
		{
			if(!$live)
			{

				if ($debug_detail_check)
				{
					echo "<BR>CHECKING START AND END TIME<br>";
					echo $current_time." is current_time<br>\n";
				}
				if($this->configuration_data['user_set_auction_start_times'] && $this->configuration_data['user_set_auction_end_times'])
				{
					if ($debug_detail_check) echo "USE START AND USE END<br>\n";
					if(($this->classified_variables["end_time"] <= $current_time) && ($this->classified_variables["start_time"] <= $current_time))
					{
						if ($debug_detail_check) echo "start and end time less than current time<BR>\n";
						if($this->classified_variables["classified_length"] == 0)
						{
							if ($debug_detail_check) echo "auction_length length is 0<Br>";
							$this->error++;
							$this->error_variables["classified_length"] = "error";
						}
						else
						{
							if ($debug_detail_check) echo "auction_length > 0...set start and end to 0<bR>";
							$this->classified_variables["start_time"] = 0;
							$this->classified_variables["end_time"] = 0;
						}
					}
					elseif($this->classified_variables["end_time"] <= $current_time)
					{
						if ($debug_detail_check) echo "start time > current time while end is less than current time<BR>\n";
						if($this->classified_variables["classified_length"] == 0)
						{
							if ($debug_detail_check) echo "auction length is 0 and must be set<bR>";
							$this->error++;
							$this->error_variables["end_time"] = "error";
						}
						else
						{
							if ($debug_detail_check) echo "end time is less than current time and start time is greater than current time but duration is provided <bR>";
							$this->classified_variables["end_time"] = 0;
						}
					}
					else
					{
						if ($debug_detail_check) echo "start time and end time is greater than current time<br>\n";
						if($this->classified_variables["start_time"] >= $this->classified_variables["end_time"])
						{
							if ($debug_detail_check) echo "start time is greater than end time ...error<br>\n";
							$this->error++;
							$this->error_variables["end_time"] = "error";
						}
						else
						{
							//$this->classified_variables["start_time"] = 0;
							$this->classified_variables["classified_length"] = 0;
							if ($debug_detail_check) echo "start time is less than end time...correct<bR>";
						}
					}
				}
				elseif($this->configuration_data['user_set_auction_start_times'])
				{
					//auction is live and 'switch for use of auction start times' is 'yes'
					if ($debug_detail_check) echo "USE START ONLY<br>\n";
					$this->classified_variables["end_time"] = 0;
					if ($debug_detail_check)
						echo $current_time." is current_time<br>\n";

					if($this->classified_variables["classified_length"] == 0)
					{
						if ($debug_detail_check)
							echo "duration should be provided 11 <bR>";
						$this->error++;
						$this->error_variables["duration"] = "error";
					}
					else
					{
						if ($debug_detail_check)
							echo "start time is greater than current time and duration is provided <bR>";
					}
					if ($this->classified_variables["start_time"] < $current_time)
					{
						if ($debug_detail_check) echo "start time is less than current_time ... setting to 0<br>\n";
						$this->classified_variables["start_time"] = 0;
					}
				}
				elseif($this->configuration_data['user_set_auction_end_times'])
				{
					if ($debug_detail_check) echo "USE END ONLY<br>\n";
					$this->classified_variables["start_time"] = 0;
					if($this->classified_variables["end_time"] <= $current_time)
					{
						$this->classified_variables["end_time"] = 0;
						if ($debug_detail_check) echo "end_time is less than current_time...setting to 0 <bR>";
						if($this->classified_variables["classified_length"] == 0)
						{
							if ($debug_detail_check) echo "Either end_time should be greater than current_time or duration should be provided <bR>";
							$this->error++;
							$this->error_variables["duration"] = "error";
						}
					}
					else
					{
						if ($debug_detail_check) echo "end time is greater than current time...setting length to 0 <bR>";
						$this->classified_variables["classified_length"] = 0;
					}
				}
				else
				{
					if ($debug_detail_check) echo "end time and start time are not in use--setting both to 0...only check duration<br>\n";
					$this->classified_variables["end_time"] = 0;
					$this->classified_variables["start_time"] = 0;
					if($this->classified_variables["classified_length"] == 0)
					{
						if ($debug_detail_check) echo "duration missing<bR>";
						$this->error++;
						$this->error_variables["duration"] = "error";
					}
					else
					{
						if ($debug_detail_check) echo "No start time or end time but duration is provided <bR>";
					}
				}
			}
			if ($debug_detail_check)
			{
				echo "<BR>ENDING START AND END TIME<br>";
				echo $current_time." is current_time<br>\n";
			}
			if ($debug_detail_check)
			{
				echo "here<br>\n";
				echo $this->classified_variables["start_time"]." after start time <bR>";
				echo $this->classified_variables["end_time"]." after end time <bR>";
				echo $this->classified_variables["classified_length"]." after auction length <bR>";
			}
		}

		if ($this->field_configuration_data->USE_PHONE_1_OPTION_FIELD)
		{
			$this->classified_variables["phone_1_option"] = trim(str_replace(",","",$this->classified_variables["phone_1_option"]));
			$this->classified_variables["phone_1_option"] = substr($this->classified_variables["phone_1_option"],0,$this->ad_configuration_data["phone_1_length"]);
		}

		if ($this->field_configuration_data->USE_PHONE_2_OPTION_FIELD)
		{
			$this->classified_variables["phone_2_option"] = trim(str_replace(",","",$this->classified_variables["phone_2_option"]));
			$this->classified_variables["phone_2_option"] = substr($this->classified_variables["phone_2_option"],0,$this->ad_configuration_data["phone_2_length"]);
		}

		if ($this->field_configuration_data->USE_FAX_FIELD_OPTION)
		{
			$this->classified_variables["fax_option"] = trim(str_replace(",","",$this->classified_variables["fax_option"]));
			$this->classified_variables["fax_option"] = substr($this->classified_variables["fax_option"],0,$this->ad_configuration_data["fax_length"]);
		}
		if ($this->ad_configuration_data["editable_description_field"])
		{
			if ($this->debug) echo $this->classified_variables["description"]." is description before anything<bR>\n";
			$this->classified_variables["description"] = stripslashes(urldecode($this->classified_variables["description"]));
			//$this->classified_variables["description"] = ereg_replace("\"", "'", $this->classified_variables["description"]);
			if ($this->debug) echo $this->classified_variables["description"]." is description before max length<br>\n";
			$this->classified_variables["description"] = substr($this->classified_variables["description"],0,$this->ad_configuration_data["maximum_description_length"]);
			if ($this->debug) echo $this->classified_variables["description"]." is description before wordwrap<br>\n";
			//$this->classified_variables["description"] = wordwrap($this->classified_variables["description"],$this->configuration_data['max_word_width'], "\n",1);
			if ($this->debug) echo $this->classified_variables["description"]." is description before disallowed<br>\n";
			$this->classified_variables["description"] = $this->replace_disallowed_html($db,$this->classified_variables["description"]);
			if ($this->debug) echo $this->classified_variables["description"]." is description before badword<br>\n";
			$this->classified_variables["description"] = $this->check_for_badwords($this->classified_variables["description"]);
			//$this->classified_variables["description"] = nl2br($this->classified_variables["description"]);
			//$this->classified_variables["description"] = ereg_replace("(\r\n|\n|\r)", "<br>", $this->classified_variables["description"]);
			if ($this->debug) echo $this->classified_variables["description"]." after<br>\n";
			if (strlen(trim($this->classified_variables["description"])) == 0)
			{
				$this->error++;
				$this->error_variables["description"] = "error";
			}
		}

		if ($this->field_configuration_data->USE_CITY_FIELD)
		{
			$this->classified_variables["city"] = $this->replace_disallowed_html($db,$this->classified_variables["city"],1);
			$this->classified_variables["city"] = $this->check_for_badwords($this->classified_variables["city"]);
		}


		if ($this->debug)
		{
			echo $this->error." is the error count<br>\n";
			reset($this->error_variables);
			foreach ($this->error_variables as $key => $value)
				echo $key." is the key to ".$value."<br>\n";

			echo "END OF CLASSIFIED_DETAIL_CHECK<Br>\n";
		}

		if ($this->error == 0)
		{
			//echo "details checked ok<br>\n";
			return true;
		}
		else
			return false;

	} //end of function classified_detail_check()

//############################################################################

	function get_html_disallowed_array($db)
	{
		$this->sql_query = "select * from ".$this->html_allowed_table." where tag_status = 1";
		$html_result = $db->Execute($this->sql_query);
		//$this->body .=$this->sql_query."<br>\n";
		if (!$html_result)
		{
			return false;
		}
		elseif ($html_result->RecordCount() > 0)
		{
			$this->row_count = 0;
			while ($show_html = $html_result->FetchNextObject())
			{
				//$this->body .=$show_html->TAG_NAME." is the tag name<br>\n";
				if ($show_html->USE_SEARCH_STRING)
				{
					//$this->html_open_disallowed_list[$this->row_count] = str_replace("+++++",$show_html->TAG_NAME,$this->html_disallowed_string);
					//$this->html_closed_disallowed_list[$this->row_count] = str_replace("+++++",$show_html->TAG_NAME,$this->html_disallowed_string);
					$this->html_disallowed_list[$this->row_count] = str_replace("+++++",$show_html->TAG_NAME,$this->html_disallowed_string);
					//$this->body .=$this->html_disallowed_list[$this->row_count]." is html disallowed ".$this->row_count."<Br>\n";
				}
				//if ($show_html->REGULAR_EXPRESSION)
				//	$this->html_disallowed_list[$this->row_count] = $show_html->REGULAR_EXPRESSION;


				else
					$this->html_disallowed_list[$this->row_count] = "'".$show_html->TAG_NAME."'i";
				$this->html_disallowed_replacement[$this->row_count] = $show_html->REPLACE_WITH;
				//$this->body .=$this->html_disallowed_list[$this->row_count]." is html disallowed ".$this->row_count."<Br>\n";
				$this->row_count++;
			}
			return true;
		}
		else
		{
			$this->html_disallowed_list = 0;
			return true;
		}
	} //end of function get_html_disallowed_array

//#########################################################################

	function replace_disallowed_html($db,$text,$remove_all=0)
	{
		$text = preg_replace ("/(<\/?)(\w+)([^>]*>)/e", "'\\1'.strtoupper('\\2').'\\3'", $text);
		if ($remove_all)
			$this->sql_query = "select * from ".$this->html_allowed_table;
		else
			$this->sql_query = "select * from ".$this->html_allowed_table." where tag_status = 1";
		$html_result = $db->Execute($this->sql_query);
		//$this->body .=$this->sql_query."<br>\n";
		if (!$html_result)
		{
			return false;
		}
		elseif ($html_result->RecordCount() > 0)
		{
			$this->row_count = 0;
			while ($show_html = $html_result->FetchNextObject())
			{
				//$this->body .=$show_html->TAG_NAME." is the tag name<br>\n";
				if (strlen(trim($show_html->REPLACE_WITH)) == 0)
					$replace = " ";
				else
					$replace = $show_html->REPLACE_WITH;
				if ($show_html->USE_SEARCH_STRING)
				{
					$expression = "/(<\/?)".$show_html->TAG_NAME."([^>]*>)/e";
					$text = preg_replace ($expression, $replace, $text);

				}
				else
				{
					$expression = "'".$show_html->TAG_NAME."'";
					$text = str_replace ($show_html->TAG_NAME, $replace, $text);
				}
				//$this->html_disallowed_replacement[$this->row_count] = $show_html->REPLACE_WITH;
				//$this->body .=$this->html_disallowed_list[$this->row_count]." is html disallowed ".$this->row_count."<Br>\n";
				//$this->row_count++;
			}
			return $text;
		}
		else
		{
			$this->html_disallowed_list = 0;
			return $text;
		}
		//$this->body .=$text." after upper<bR>\n";
		//$this->body .=$text." before cleaning<bR>\n";
		//$text = preg_replace ("/(<\/?)APPLET([^>]*>)/e", "appletsomething", $text);
		//$text = preg_replace ("/(<\/?)HTML([^>]*>)/e", "something", $text);
		//$text = preg_replace ("/(<\/?)TABLE([^>]*>)/e", "table", $text);
		//$this->body .=$text." after cleaning<bR>\n";
		return $text;

	} //function replace_disallowed_html

//#########################################################################

	function get_badword_array($db)
	{
		$this->sql_query = "select * from ".$this->badwords_table." order by badword_id";
		$result = $db->Execute($this->sql_query);

		if (!$result)
		{
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->row_count = 0;
			while ($show = $result->FetchNextObject())
			{
				$this->badword_list[$this->row_count] = $show->BADWORD;
				$this->badword_replacement[$this->row_count] = $show->BADWORD_REPLACEMENT;
				$this->row_count++;
			}
			return true;
		}
		else
		{
			$this->badword_list = 0;
			return true;
		}
	} //end of function get_badword_array

//#########################################################################

	function check_for_badwords($text)
	{
		if ($this->badword_list)
		{
			reset($this->badword_list);
			while (list($key,$badword) = each($this->badword_list))
			{
				if (($this->badword_replacement[$key] != $badword) && (strlen(trim($text)) > 0) && (strlen(trim($badword)) > 0))
				{
					if(stristr($text, $badword))
					{
						if(strtoupper($text)==strtoupper($badword))
							$text = $this->badword_replacement[$key];
						$text = eregi_replace("^$badword([^a-zA-Z])*", $this->badword_replacement[$key], $text);
						$text = eregi_replace("([^a-zA-Z])*$badword$", $this->badword_replacement[$key], $text);
						while(eregi("([^a-zA-Z ])*($badword)([^a-zA-Z ])*", $text)){
							//$text = eregi_replace("([^a-zA-Z])*($badword)([^a-zA-Z])*", "\\1".$this->badword_replacement[$key]."\\3", $text);
							$text = eregi_replace("([^a-zA-Z])*($badword)([^a-zA-Z])*", $this->badword_replacement[$key], $text);
						}
					}
				}
			}
        	}
        	return $text;
        } //end of function check_for_badwords

//#########################################################################

	function extra_page($db,$page_id=0)
	{
		if ($page_id)
		{
			$this->sql_query = "select * from ".$this->extra_pages_table." where page_id = ".$page_id;
			$result = $db->Execute($this->sql_query);

			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchNextObject();
				$this->body .=stripslashes(urldecode($show->PAGE_CONTENT));
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

        } //end of function check_for_badwords

//#########################################################################

	function get_image_file_types_array($db)
	{
		$this->sql_query = "select * from ".$this->file_types_table." where accept = 1";
		$type_result = $db->Execute($this->sql_query);
		if (!$type_result)
		{
			return false;
		}
		elseif ($type_result->RecordCount() > 0)
		{

			while ($show = $type_result->FetchNextObject())
			{
				array_push($this->image_file_types,$show->MIME_TYPE);
				array_push($this->image_file_types_icon,$show->ICON_TO_USE);
				array_push($this->image_file_types_extension,$show->EXTENSION);
			}
		}
		return true;
	} //end of get_image_file_types_array

//#########################################################################

	function image_accepted_type($type)
	{
		reset($this->image_file_types);
		foreach ($this->image_file_types as $key => $value)
		{
			if (strstr($type,$value))
			{
				$image_accepted_type = 1;
				$this->current_file_type_icon = $this->image_file_types_icon[$key];
				$this->current_file_type_extension = $this->image_file_types_extension[$key];
				return true;
			}
		}
		return false;
	} //end of function image_accepted_type

//#########################################################################

	function get_category_string($db,$category)
	{
		$category_tree = $this->get_category_tree($db,$category);
		reset ($this->category_tree_array);

		if ($category_tree)
		{
			//category tree
			$category_string = urldecode($this->messages[79])." > ";
			if (is_array($this->category_tree_array))
			{
				$i = 0;
				//$categories = array_reverse($this->category_tree_array);
				$i = count($this->category_tree_array);
				while ($i > 0 )
				{
					//display all the categories
					$i--;
					if ($i == 0)
						$category_string .= $this->category_tree_array[$i]["category_name"];
					else
						$category_string .= $this->category_tree_array[$i]["category_name"]." > ";
				}
			}
			else
			{
				$this->body .=$category_tree;
			}
		}
	}

//##################################################################################

	function check_extra_questions($db)
	{
		$num_questions = count($this->classified_variables["question_value"]);
		//$this->body .=$num_questions." is the num of questions remembered<Br>\n";
		if ($num_questions > 0 )
		{
			while (list($key,$value) = each($this->classified_variables["question_value"]))
			{
				 if (strlen(trim($value)) > 0)
				 {
					if (strlen(trim($this->classified_variables["question_value_other"][$key])) > 0)
					{
						//check other value
						//wordrap
						$this->classified_variables["question_value_other"][$key] = wordwrap($this->classified_variables["question_value_other"][$key],$this->configuration_data['max_word_width'], " \n",1);
						//check the value for badwords
						$this->classified_variables["question_value_other"][$key] = $this->check_for_badwords($this->classified_variables["question_value_other"][$key]);
						//check the value for disallowed html
						$this->classified_variables["question_value_other"][$key] = $this->replace_disallowed_html($db,$this->classified_variables["question_value_other"][$key],0);

					}
					else
					{
						//check dropdown or input box value
						//wordrap
						$this->classified_variables["question_value"][$key] = wordwrap($this->classified_variables["question_value"][$key],$this->configuration_data['max_word_width'], " \n",1);
						//check the value for badwords
						$this->classified_variables["question_value"][$key] = $this->check_for_badwords($this->classified_variables["question_value"][$key]);
						//check the value for disallowed html
						$this->classified_variables["question_value"][$key] = $this->replace_disallowed_html($db,$this->classified_variables["question_value"][$key],0);
					}
				} // end of if
			}//end of while
		}// end of if num_questions > 0


	} //end of function check_extra_questions

//################################################################################

	function in_array_key($key, $array, $value = false)
	{
		if (is_array($array))
		{
			while(list($k, $v) = each($array))
			{
				if($key == $k)
				{
					if($value && $value == $v)
						return true;
					elseif($value && $value != $v)
						return false;
					else
						return true;
				}
			}
		}
		return false;
	} //end of function in_array_key

//#################################################################################

	function expire_groups_and_plans($db)
	{
		$this->sql_query = "select * from ".$this->expirations_table." where expires < ".$this->shifted_time($db);
		$type_result = $db->Execute($this->sql_query);
		if (!$type_result)
		{
			return false;
		}
		elseif ($type_result->RecordCount() > 0)
		{
			while ($show = $type_result->FetchNextObject())
			{
				if ($show->TYPE == 1)
				{
					$this->sql_query = "select group_expires_into from ".$this->groups_table." where group_id = ".$show->TYPE_ID;
					$expire_into_result = $db->Execute($this->sql_query);
					if (!$expire_into_result)
					{
						return false;
					}
					elseif ($expire_into_result->RecordCount() == 1)
					{
						$show_expire_into = $expire_into_result->FetchNextObject();
						//expire group
						if ($show->TYPE_ID_EXPIRES_TO)
						{
							$this->sql_query = "update ".$this->user_groups_price_plans_table." set
								group_id = ".$show_expire_into->GROUP_EXPIRES_INTO."
								where group_id = ".$show->TYPE_ID;
							$update_group_result = $db->Execute($this->sql_query);
							if (!$update_group_result)
							{
								return false;
							}
						}
					}
				}
				elseif ($show->TYPE == 2)
				{
					//expire price plans
					$this->sql_query = "select * from ".$this->price_plans_table." where price_plan_id = ".$show->TYPE_ID;
					$price_plan_result = $db->Execute($this->sql_query);
					if (!$price_plan_result)
					{
						return false;
					}
					elseif ($price_plan_result->RecordCount() == 1)
					{
						if ($show->USER_ID)
						{
							//expires this specific users price plan
							$show_price_plan = $price_plan_result->FetchNextObject();

							//check to see if ads expire with price plan
							if ($show_price_plan->AD_AND_SUBSCRIPTION_EXPIRATION == 1)
							{
								$this->sql_query = "update ".$this->classifieds_table." set
									live = 0
									where seller = ".$show->USER_ID;
								$update_live_result = $db->Execute($this->sql_query);
								//$this->body .=$this->sql_query."<br>\n";
								if (!$update_live_result)
								{
									return false;
								}
							}
							if ($show_price_plan->PRICE_PLAN_EXPIRES_INTO)
							{
								$this->sql_query = "update ".$this->user_groups_price_plans_table." set
									price_plan_id = ".$show_price_plan->PRICE_PLAN_EXPIRES_INTO."
									where id = ".$show->USER_ID;
								$update_price_plan_result = $db->Execute($this->sql_query);
								if (!$update_price_plan_result)
								{
									return false;
								}
							}
						}
						else
						{
							//expires this price plan for every user
							$show_price_plan = $price_plan_result->FetchNextObject();
							if ($show_price_plan->AD_AND_SUBSCRIPTION_EXPIRATION == 1)
							{

								$this->sql_query = "select * ".$this->user_groups_price_plans_table."
									where price_plan_id = ".$show->TYPE_ID;
								$select_users_result = $db->Execute($this->sql_query);
								//$this->body .=$this->sql_query."<br>\n";
								if (!$select_users_result)
								{
									return false;
								}
								elseif ($select_users_result->RecordCount() > 0)
								{
									while ($show_users = $select_users_result->FetchNextObject())
									{
										$this->sql_query = "update ".$this->classifieds_table." set
											live = 0
											where seller = ".$show_users->ID;
										$update_live_result = $db->Execute($this->sql_query);
										$this->body .=$this->sql_query."<br>\n";
										if (!$update_live_result)
										{
											return false;
										}
									}
								}
							}
							if ($show_price_plan->PRICE_PLAN_EXPIRES_INTO)
							{
								$this->sql_query = "update ".$this->user_groups_price_plans_table." set
									price_plan_id = ".$show_price_plan->PRICE_PLAN_EXPIRES_INTO."
									where price_plan_id = ".$show->TYPE_ID;
								$update_price_plan_result = $db->Execute($this->sql_query);
								//$this->body .=$this->sql_query."<br>\n";
								if (!$update_price_plan_result)
								{
									return false;
								}
							}
						}
					}
				}
				//delete the expiration
				$this->sql_query = "delete from ".$this->expirations_table." where expiration_id = ".$show->EXPIRATION_ID;
				$delete_result = $db->Execute($this->sql_query);
				//$this->body .=$this->sql_query."<br>\n";
				if (!$delete_result)
				{
					return false;
				}
			}
		}
		return true;

	} //end of function expire_groups_and_plans

//#################################################################################

	function get_state_name($db,$state_abbreviation=0)
	{
		if ($state_abbreviation)
		{
			$this->sql_query = "select name from ".$this->states_table." where abbreviation = \"".$state_abbreviation."\"";
			$name_result = $db->Execute($this->sql_query);
			if (!$name_result)
			{
				return false;
			}
			elseif ($name_result->RecordCount() == 1)
			{
				$show_name = $name_result->FetchNextObject();
				return $show_name->NAME;
			}
			else
			{
				//just display the user_id
				return $state_abbreviation;
			}
		}
		else
		{
			return false;
		}
	} //end of function get_state_name

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_ad_count_for_category($db,$category_id=0)
	{
		if ($category_id)
		{
			//get the count for this category
			$count = 0;

			$this->sql_query = "select category_id from ".$this->categories_table." where parent_id = ".$category_id;
			$category_id_result = $db->Execute($this->sql_query);
			//$this->body .=$this->sql_query."<br>";
			if (!$category_id_result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[2524];
				return false;
			}
			elseif ($category_id_result->RecordCount() > 0)
			{
				while ($show_category = $category_id_result->FetchNextObject())
				{
					$returned_count = $this->get_ad_count_for_category($db,$show_category->CATEGORY_ID);
					if ($returned_count)
						$count += $returned_count;

					//$this->body .=$count." is count returned for category ".$category_id."<br>\n";
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
			//$this->body .=$this->sql_query."<br>\n";
			if (!$count_result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[2524];
				return false;
			}
			elseif ($count_result->RecordCount() == 1)
			{
				$show = $count_result->FetchNextObject();
				return $show->TOTAL;
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

	} //end of function get_ad_count_this_category

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
							$this->sql_query = "select in_statement from ".$this->categories_table." where
								category_id = ".$this->category_tree_array[$i]["category_id"];
							$in_category_result = $db->Execute($this->sql_query);
							//echo $this->sql_query."<br>\n";
							if (!$in_category_result)
							{
								return false;
							}
							if ($in_category_result->RecordCount() == 1)
							{
								$show_in_statement = $in_category_result->FetchNextObject();

								$this->sql_query = "select count(*) as total from ".$this->classifieds_table." where live = 1 and category ".$show_in_statement->IN_STATEMENT." and item_type = 1";
								$count_result = $db->Execute($this->sql_query);
								//echo $this->sql_query."<br>\n";

								$this->sql_query = "select count(*) as total from ".$this->classifieds_table." where live = 1 and category ".$show_in_statement->IN_STATEMENT." and item_type = 2";
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
									$show = $count_result->FetchNextObject();
									$auction_show = $auction_count_result->FetchRow();

									$this->sql_query = "update ".$this->categories_table." set
										category_count = ".$show->TOTAL.",
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

	function get_category_count($db,$category_id=0)
	{
		//echo $category_id." is category id<br>";
		if (($category_id) && (!$this->filter_id) && (!$this->state_filter) && (!$this->zip_filter_distance) && (!$this->zip_filter))
		{
			if($this->is_class_auctions())
				$this->sql_query = "select category_count, auction_category_count ";
			elseif($this->is_auctions())
				$this->sql_query = "select auction_category_count ";
			elseif($this->is_classifieds())
				$this->sql_query = "select category_count ";
			$this->sql_query .= "from ".$this->categories_table." where category_id = ".$category_id;
			$count_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<bR>\n";
			if (!$count_result)
			{
				return false;
			}
			elseif ($count_result->RecordCount() == 1)
			{
				$show = $count_result->FetchRow();
				$array = array("auction_count" => $show['auction_category_count'], "ad_count" => $show['category_count'], "listing_count" => ($show['category_count']+$show['auction_category_count']));
				return $array;
			}
			else
				return false;
		}
		elseif ($category_id)
		{
			if ($this->filter_id)
			{
				$filter_in_statement = $this->get_sql_filter_in_statement($db);
				$sql_filter_in_statement = " and filter_id ".$filter_in_statement." ";
			}
			if ($this->state_filter)
			{
				//add state to end of sql_query
				$sql_state_filter_statement = " and location_state = \"".$this->state_filter."\" ";

			}
			if (($this->zip_filter_distance) && ($this->zip_filter))
			{
				//add zip code in statement to end of sql_query
				if (strlen(trim($this->sql_zip_filter_in_statement)) == 0)
				{
					$zip_filter_in_statement = $this->get_sql_zip_filter_in_statement($db);
					$this->sql_zip_filter_in_statement = " and  ".$zip_filter_in_statement." ";
				}
			}

			//get category in statement
			$this->sql_query = "select in_statement from ".$this->categories_table." where category_id = ".$category_id;
			$in_result = $db->Execute($this->sql_query);
			if (!$in_result)
			{
				return false;
			}
			elseif ($in_result->RecordCount() == 1)
			{
				$show_in_statement = $in_result->FetchRow();
				$sql_category = " category ".$show_in_statement['in_statement'];
			}

			if($this->is_class_auctions())
			{
				// Get listing count
				$this->sql_query = "select count(*) as category_count from ".$this->classifieds_table." where live = 1 and ".$sql_category.$sql_filter_in_statement.$sql_state_filter_statement.$sql_zip_filter_in_statement;
				$count_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<bR>\n";
				if (!$count_result)
				{
					return false;
				}
				elseif ($count_result->RecordCount() == 1)
					$show_count = $count_result->FetchRow();
			}

			if($this->is_class_auctions() || $this->is_classifieds())
			{
				// Get ad count
				$this->sql_query = "select count(*) as category_count from ".$this->classifieds_table." where live = 1 and ".$sql_category.$sql_filter_in_statement.$sql_state_filter_statement.$sql_zip_filter_in_statement." and item_type = 1";
				$ad_count_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<bR>\n";
				if (!$ad_count_result)
				{
					return false;
				}
				elseif ($ad_count_result->RecordCount() == 1)
					$show_ad_count = $ad_count_result->FetchRow();
			}

			if($this->is_class_auctions() || $this->is_auctions())
			{
				// Get auction count
				$this->sql_query = "select count(*) as category_count from ".$this->classifieds_table." where live = 1 and ".$sql_category.$sql_filter_in_statement.$sql_state_filter_statement.$sql_zip_filter_in_statement." and item_type = 2";
				$auction_count_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<bR>\n";
				if (!$auction_count_result)
				{
					return false;
				}
				elseif ($auction_count_result->RecordCount() == 1)
					$show_auction_count = $auction_count_result->FetchRow();
			}

			return array("auction_count" => $show_auction_count['category_count'], "ad_count" => $show_ad_count['category_count'], "listing_count" => ($show_ad_count['category_count']+$show_auction_count['category_count']));
		}
		else
			return false;
	} //end of function get_category_count

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 *	Displays the formatted category count
	 *	@param $db
	 *	@param int $category_id
	 *	@param int $browsing_count_format format that the count will be printed in
	 *	@param string $link the link for all the pages
	 *	@param array $css the css tags that will be used for each part of the count keyed by listing_count, ad_count, and auction_count
	 *	@return string text that contains the count or counts
	 */
	function display_category_count($db, $category_id=0, $browsing_count_format=-1, $link=0, $css=0, $category_count=0)
	{
		if((!is_array($category_count)) || ($this->filter_id) || ($this->state_filter) || ($this->zip_filter_distance) || ($this->zip_filter))
			$category_count = $this->get_category_count($db, $category_id);

		// Check for css and build the link tag from there
		if($link)
		{
			if($css)
			{
				$link_css["listing_count"] = str_replace(">", " class=".$css['listing_count'].">", $link);
				$link_css["ad_count"] = str_replace(">", " class=".$css['ad_count'].">", $link);
				$link_css["auction_count"] = str_replace(">", " class=".$css['auction_count'].">", $link);
			}
			else
			{
				$link_css["listing_count"] = $link;
				$link_css["ad_count"] = $link;
				$link_css["auction_count"] = $link;
			}
		}
		elseif($css && is_array($css))
		{
			$link_css["listing_count"] = "<font class=".$css['listing_count'].">";
			$link_css["ad_count"] = "<font class=".$css['ad_count'].">";
			$link_css["auction_count"] = "<font class=".$css['auction_count'].">";
		}

		// It will only use the passed in variable when called from a module
		if($browsing_count_format == -1)
			$browsing_count_format = $this->configuration_data['browsing_count_format'];
		switch ($browsing_count_format)
		{
			case -1:
				if($link)
					return $link_css['listing_count']."(".$category_count['listing_count'].")</a>";
				elseif($css)
					return $link_css['listing_count']."(".$category_count['listing_count'].")</font>";
				else
					return "(".$category_count['listing_count'].")";
				break;
			case 0:
				if($link)
					return $link_css['listing_count']."(".$category_count['listing_count'].")</a>";
				elseif($css)
					return $link_css['listing_count']."(".$category_count['listing_count'].")</font>";
				else
					return "(".$category_count['listing_count'].")";
				break;
			case 1:
				if($link)
					return $link_css['auction_count']."(".$category_count['auction_count'].")</a>";
				elseif($css)
					return $link_css['auction_count']."(".$category_count['auction_count'].")</font>";
				else
					return "(".$category_count['auction_count'].")";
				break;
			case 2:
				if($link)
					return $link_css['ad_count']."(".$category_count['ad_count'].")</a>";
				elseif($css)
					return $link_css['ad_count']."(".$category_count['ad_count'].")</font>";
				else
					return "(".$category_count['ad_count'].")";
				break;
			case 3:
				if($link)
					return $link_css['auction_count']."(".$category_count['auction_count'].")</a>".$link_css['ad_count']."(".$category_count['ad_count'].")</a>";
				elseif($css)
					return $link_css['auction_count']."(".$category_count['auction_count'].")</font>".$link_css['ad_count']."(".$category_count['ad_count'].")</font>";
				else
					return "(".$category_count['auction_count'].")(".$category_count['ad_count'].")";
				break;
			case 4:
				if($link)
					return $link_css['ad_count']."(".$category_count['ad_count'].")</a>".$link_css['auction_count']."(".$category_count['auction_count'].")</a>";
				elseif($css)
					return $link_css['ad_count']."(".$category_count['ad_count'].")</font>".$link_css['auction_count']."(".$category_count['auction_count'].")</font>";
				else
					return "(".$category_count['ad_count'].")(".$category_count['auction_count'].")";
				break;
			case 5:
				if($link)
					return $link_css['listing_count']."(".$category_count['listing_count'].")</a>";
				elseif($css)
					return $link_css['listing_count']."(".$category_count['listing_count'].")</font>";
				else
					return "(".$category_count['listing_count'].")";
				break;
			default:
				if($link)
					return $link_css['listing_count']."(".$category_count['listing_count'].")</a>";
				elseif($css)
					return $link_css['listing_count']."(".$category_count['listing_count'].")</font>";
				else
					return "(".$category_count['listing_count'].")";
				break;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_sql_filter_in_statement($db)
	{
		if ($this->filter_id)
		{
			$this->sql_query = "SELECT in_statement FROM ".$this->filters_table." WHERE filter_id = ".$this->filter_id;
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_filter_in = $result->FetchNextObject();
				return $show_filter_in->IN_STATEMENT;
			}
		}
		else
		{
			//category_id is missing
			return false;
		}

	} //end of get_sql_filter_in_statement

//####################################################################################

	function get_banner($db,$zone=0)
	{
		//category set already
		if ($zone)
		{
			$this->sql_query = "select * from ".$this->banners_table." where category_id = \"".$this->site_category."\" and zone = ".$zone;
			//$this->body .=$this->sql_query."<bR>\n";
			$banner_result = $db->Execute($this->sql_query);
			if (!$banner_result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				return false;
			}
			elseif ($banner_result->RecordCount() == 0)
			{
				$this->get_category_tree($db,$this->site_category);
				$i = count($this->category_tree_array);
				while ($i >= 0 )
				{
					//display all the categories
					$i--;
					$this->sql_query = "select * from ".$this->banners_table." where category_id = \"".$this->category_tree_array[$i]["category_id"]."\" and zone = ".$zone;
					//$this->body .=$this->sql_query." is the parent sql<bR>\n";
					$parent_banner_result = $db->Execute($this->sql_query);
					if (!$parent_banner_result)
					{
						//$this->body .=$this->sql_query." is the query<br>\n";
						return false;
					}
					elseif ($parent_banner_result->RecordCount() == 1)
					{
						$show = $parent_banner_result->FetchNextObject();
						if (strlen(trim($show->EXTERNAL_CODE_USE)) > 0)
						{
							return stripslashes($show->EXTERNAL_CODE_USE);
						}
						else
						{
							$tag = "<a href=".$this->configuration_data['classifieds_file_name']."?a=23&banner_id=".$show->BANNER_ID.">
								<img src=".$show->BANNER_URL." border=0></a>";
							$this->update_banner_impression($db,$show->BANNER_ID);
							return $tag;
						}
					}
					elseif ($parent_banner_result->RecordCount() > 1)
					{
						srand(microtime()*1000000);
						$move_to = rand(1,$parent_banner_result->RecordCount());
						$parent_banner_result->Move($move_to-1);
						$show = $parent_banner_result->FetchNextObject();

						if (strlen(trim($show->EXTERNAL_CODE_USE)) > 0)
						{
							return stripslashes($show->EXTERNAL_CODE_USE);
						}
						else
						{
							$tag = "<a href=".$this->configuration_data['classifieds_file_name']."?a=23&banner_id=".$show->BANNER_ID.">
								<img src=".$show->BANNER_URL." border=0></a>";
							$this->update_banner_impression($db,$show->BANNER_ID);
							return $tag;
						}
					}
				}
			}
			elseif ($banner_result->RecordCount() == 1)
			{
				$show = $banner_result->FetchNextObject();
				if (strlen(trim($show->EXTERNAL_CODE_USE)) > 0)
				{
					return stripslashes($show->EXTERNAL_CODE_USE);
				}
				else
				{
					$tag = "<a href=".$this->configuration_data['classifieds_file_name']."?a=23&banner_id=".$show->BANNER_ID.">
						<img src=".$show->BANNER_URL." border=0></a>";
					$this->update_banner_impression($db,$show->BANNER_ID);
					return $tag;
				}
			}
			elseif ($banner_result->RecordCount() > 1)
			{
				srand(microtime()*1000000);
				$move_to = rand(1,$banner_result->RecordCount());
				$banner_result->Move($move_to-1);
				$show = $banner_result->FetchNextObject();
				if (strlen(trim($show->EXTERNAL_CODE_USE)) > 0)
				{
					return stripslashes($show->EXTERNAL_CODE_USE);
				}
				else
				{
					$tag = "<a href=".$this->configuration_data['classifieds_file_name']."?a=23&banner_id=".$show->BANNER_ID.">
						<img src=".$show->BANNER_URL." border=0></a>";
					$this->update_banner_impression($db,$show->BANNER_ID);
					return $tag;
				}
			}
			else
			{
				return false;
			}
		}
	} //end of function get_banner

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_banner_impression ($db,$banner_id=0)
	{
		if ($banner_id)
		{
			$this->sql_query = "select impressions from ".$this->banners_table." where banner_id = ".$banner_id;
			$banner_result = $db->Execute($this->sql_query);
			if (!$banner_result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				return false;
			}
			elseif ($banner_result->RecordCount() == 1)
			{
				$show = $banner_result->FetchNextObject();
				$impressions = $show->IMPRESSIONS + 1;
				$this->sql_query = "update ".$this->banners_table." set
					impressions = \"".$impressions."\"
					where banner_id = ".$banner_id;
				$update_result = $db->Execute($this->sql_query);
				if (!$update_result)
				{
					//$this->body .=$this->sql_query." is the query<br>\n";
					return false;
				}
			}
		}
	} //end of function update_banner_impression

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_banner_click ($db,$banner_id=0)
	{
		if ($banner_id)
		{
			$this->sql_query = "select clicks,link_url from ".$this->banners_table." where banner_id = ".$banner_id;
			$banner_result = $db->Execute($this->sql_query);
			if (!$banner_result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				return false;
			}
			elseif ($banner_result->RecordCount() == 1)
			{
				$show = $banner_result->FetchNextObject();
				$clicks = $show->CLICKS + 1;
				$this->sql_query = "update ".$this->banners_table." set
					clicks = \"".$clicks."\"
					where banner_id = ".$banner_id;
				$update_result = $db->Execute($this->sql_query);
				if (!$update_result)
				{
					//$this->body .=$this->sql_query." is the query<br>\n";
					return false;
				}

				return $show->LINK_URL;
			}
		}
		else
			return false;
	} //end of function update_banner_impression

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function send_communication($db,$to=0,$message_data=0)
	{
		$debug_comm = 0;
		if ($this->configuration_data['email_header_break'])
			$separator = "\n";
		else
			$separator = "\r\n";

		if ($debug_comm)
		{
			echo $message_data["from"]." is from<BR>\n";
			echo $message_data["subject"]." is subject<br>\n";
			echo $to." is to<BR>\n";

		}
		if ($message_data)
		{
			if (($to) && ($to != "reply"))
			{
				if ((strlen(trim($message_data["message"])) > 0) && (strlen(trim($message_data["from"])) > 0) && (strlen(trim($message_data["subject"])) > 0) )
				{
					$to_data = $this->get_user_data($db,$to);
					if ($to_data)
					{
						if (strlen(trim($message_data["subject"])) == 0)
							$message_data["subject"] == "no subject";
						if (($to_data->COMMUNICATION_TYPE == 1) || ($to_data->COMMUNICATION_TYPE == 2))
						{
							//send an email
							if (($this->classified_user_id) && (!$system))
							{
								$from_data = $this->get_user_data($db,$this->classified_user_id);
								if ($from_data)
								{
									if ($from_data->COMMUNICATION_TYPE == 1)
									{
										$message_from = "Reply-to: ".$from_data->EMAIL.$separator."From: ".$from_data->EMAIL.$separator;
										$message_data["message"] .= "From: ".$from_data->USERNAME."\n".$from_data->EMAIL;
									}
									else
									{
										$message_from = "Reply-to: ".$this->configuration_data['site_email'].$separator."From: ".$this->configuration_data['site_email'].$separator;
										$message_data["message"] .= "From: ".$from_data->USERNAME."\n\n";
										$message_data["message"] .= urldecode($this->messages[249])."\n\n";
										$message_data["message"] .= "\n".$this->configuration_data['classifieds_url']."?a=3&b=".$from_data->ID;
										if ($message_data["auction_id"])
											$message_data["message"] .= "&c=".$message_data["auction_id"];
									}
								}
							}
							else
							{
								if ($system)
								{
									$message_from = "From: ".$this->configuration_data['site_email'].$separator."Reply-to: ".$this->configuration_data['site_email'].$separator;
								}
								else
								{
									$email_address = str_replace("Reply-to: ","",$message_data["from"]);
									$email_address = str_replace("From: ","",$email_address);
									$message_from = "Reply-to: ".$message_data["from"].$separator.$message_data["from"].$separator;
									$message_data["message"] .= "\n\n".$message_data["from"]."\n\n";
								}
							}

							//if ($to_data->COMMUNICATION_TYPE == 2)
							//	$message_data["message"] .= "\n\n".urldecode($this->messages[248])."\n\n";
							//send this email

							if (strlen(trim($message_data["classified_id"])) > 0)
								$regarding_ad = $message_data["classified_id"];
							else
								$regarding_ad = 0;
							$additional = "-f".$this->configuration_data['site_email'];

							$ip = $_SERVER['REMOTE_ADDR'];
							$host = @gethostbyaddr($ip);
							//$host = preg_replace("/^[^.]+./", "*.", $host);
							$message_data["message"] .= "\n".$_SERVER["REMOTE_ADDR"]." : ".$host;

							if ($this->configuration_data['email_configuration_type'] == 1)
								$message_data["message"] = str_replace("\n\n","\n",$message_data["message"]);

							if ($this->configuration_data['email_configuration'] == 1)
								mail($to_data->EMAIL, $message_data["subject"], $message_data["message"], $message_from,$additional);
							elseif ($this->configuration_data['email_configuration'] == 2)
								mail($to_data->EMAIL, $message_data["subject"], $message_data["message"], $message_from);
							else
								mail($to_data->EMAIL, $message_data["subject"], $message_data["message"]);
							if ($this->classified_user_id)
							{
								$this->sql_query = "insert into ".$this->user_communications_table."
									(message_to,message_from,regarding_ad,date_sent,message)
									values
									(".$to.",".$this->classified_user_id.",".$regarding_ad.",".$this->shifted_time($db).",\"".urlencode($message_data["message"])."\")";
							}
							else
							{
								$email_address = str_replace("Reply-to: ","",$message_data["from"]);
								$email_address = str_replace("From: ","",$email_address);
								$this->sql_query = "insert into ".$this->user_communications_table."
									(message_to,message_from_non_user,regarding_ad,date_sent,message)
									values
									(".$to.",\"".$email_address."\",".$regarding_ad.",".$this->shifted_time($db).",\"".urlencode($message_data["message"])."\")";
							}
							if ($debug_comm)
								echo $this->sql_query." has an error<Br>\n";
							$result = $db->Execute($this->sql_query);
							if (!$result)
							{
								if ($debug_comm)
									echo $this->sql_query." has an error<Br>\n";
								$this->error_message = $this->internal_error_message;
								return false;
							}
						}
						else
						{
							//send a private message
							if (strlen(trim($message_data["classified_id"])) > 0)
								$regarding_ad = $message_data["classified_id"];
							else
								$regarding_ad = 0;

							$ip = $_SERVER['REMOTE_ADDR'];
							$host = @gethostbyaddr($ip);
							//$host = preg_replace("/^[^.]+./", "*.", $host);
							$message_data["message"] .= "\n".$_SERVER["REMOTE_ADDR"]." : ".$host;


							if ($this->classified_user_id)
							{
								$this->sql_query = "insert into ".$this->user_communications_table."
									(message_to,message_from,regarding_ad,date_sent,message)
									values
									(".$to.",".$this->classified_user_id.",".$regarding_ad.",".$this->shifted_time($db).",\"".urlencode($message_data["message"])."\")";
							}
							else
							{
								$email_address = str_replace("Reply-to: ","",$message_data["from"]);
								$email_address = str_replace("From: ","",$email_address);
								$this->sql_query = "insert into ".$this->user_communications_table."
									(message_to,message_from_non_user,regarding_ad,date_sent,message)
									values
									(".$to.",\"".$message_data["from"]."\",".$regarding_ad.",".$this->shifted_time($db).",\"".urlencode($message_data["message"])."\")";
							}
							if ($debug_comm) echo $this->sql_query."<br>\n";
							$result = $db->Execute($this->sql_query);
							if (!$result)
							{
								if ($debug_comm) echo $this->sql_query."<br>\n";
								return false;
							}
						}
						return true;
					}
					else
					{
						return false;
					}
				}
				else
					return false;
			}
			elseif (($message_data["replied_to_this_messages"]) && ($to == "reply"))
			{
				$this->page_id = 25;
				$this->get_text($db);
				//send a reply using this data
				$this->sql_query = "select * from ".$this->user_communications_table." where message_id = ".$message_data["replied_to_this_messages"];
				$result = $db->Execute($this->sql_query);
				if ($debug_comm) echo $this->sql_query."<br>\n";
				if (!$result)
				{
					if ($debug_comm) echo $this->sql_query." - has an error<br>\n";
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show_message = $result->FetchNextObject();
					if ($show_message->MESSAGE_FROM)
					{
						//this is a local user send them the reply
						$to_data = $this->get_user_data($db,$show_message->MESSAGE_FROM);
						$from_data = $this->get_user_data($db,$this->classified_user_id);
						$subject = $this->get_ad_title($db,$message_data["regarding_ad"]);

						if ($from_data->COMMUNICATION_TYPE == 1)
						{
							$message_from = "From: ".$from_data->EMAIL.$separator."Reply-to: ".$from_data->EMAIL.$separator;
						}
						else
						{
							$message_from = "From: ".$from_data->EMAIL.$separator."Reply-to: ".$from_data->EMAIL.$separator;
							$message_data["message"] .= "\n\n".urldecode($this->messages[249])."\n\n";
							$message_data["message"] .= "\n".$this->configuration_data['classifieds_url']."?a=3&b=".$from_data->ID."&c=".$message_data["classified_id"];
						}

						if (($to_data->COMMUNICATION_TYPE == 1) || ($to_data->COMMUNICATION_TYPE == 2))
						{
							$additional = "-f".$this->configuration_data['site_email'];

							$ip = $_SERVER['REMOTE_ADDR'];
							$host = @gethostbyaddr($ip);
							//$host = preg_replace("/^[^.]+./", "*.", $host);
							$message_data["message"] .= "\n".$_SERVER["REMOTE_ADDR"]." : ".$host;


							if ($this->configuration_data['email_configuration_type'] == 1)
								$message_data["message"] = str_replace("\n\n","\n",$message_data["message"]);
							if ($this->configuration_data['email_configuration'] == 1)
								mail($to_data->EMAIL, $subject, $message_data["message"], $message_from,$additional);
							elseif ($this->configuration_data['email_configuration'] == 2)
								mail($to_data->EMAIL, $subject, $message_data["message"], $message_from);
							else
								mail($to_data->EMAIL, $subject, $message_data["message"]);
							$this->sql_query = "insert into ".$this->user_communications_table."
								(message_to,message_from,regarding_ad,date_sent,message,replied_to_this_message)
								values
								(".$show_message->MESSAGE_FROM.",".$this->classified_user_id.",".$message_data["regarding_ad"].",".$this->shifted_time($db).",\"".urlencode($message_data["message"])."\",".$message_data["regarding_ad"].")";
						}
						else
						{
							$ip = $_SERVER['REMOTE_ADDR'];
							$host = @gethostbyaddr($ip);
							//$host = preg_replace("/^[^.]+./", "*.", $host);
							$message_data["message"] .= "\n".$_SERVER["REMOTE_ADDR"]." : ".$host;

							$this->sql_query = "insert into ".$this->user_communications_table."
								(message_to,message_from,regarding_ad,date_sent,message,replied_to_this_message)
								values
								(".$show_message->MESSAGE_FROM.",".$this->classified_user_id.",".$message_data["regarding_ad"].",".$this->shifted_time($db).",\"".urlencode($message_data["message"])."\",".$message_data["regarding_ad"].")";
						}
						$result = $db->Execute($this->sql_query);
						if ($debug_comm) echo $this->sql_query."<br>\n";
						if (!$result)
						{
							if ($debug_comm) echo $this->sql_query." - has an error<br>\n";
							return false;
						}
					}
					else
					{
						//this is a non registered user
						//check the validity of the email address
						if ($debug_comm)
						{
							echo "this is a non registered user<Br>\n";
							echo $show_message->MESSAGE_FROM_NON_USER." is show_message->MESSAGE_FROM_NON_USER<Br>\n";
							echo $message_data["classified_id"]." is message_data[classified_id]<bR>\n";
							echo $message_data["regarding_ad"]." is message_data[regarding_ad]<BR>\n";
							echo $this->classified_user_id." is classified_user_id<BR>\n";
							echo $message_data["message"]." is the message<BR>\n";
							echo $this->page_id." is page_id<br>\n";

						}
						$email_address = str_replace("Reply-to: ","",$show_message->MESSAGE_FROM_NON_USER);
						$email_address = str_replace("From: ","",$email_address);
						if ($debug_comm)
						{
							echo $email_address." is the email_address after cleaning<BR>\n";
						}
						if (eregi("^([a-z0-9]+)(([a-z0-9._-]+))*[@]([a-z0-9]+)([._-]([a-z0-9]+))*[.]([a-z0-9]){2}([a-z0-9])?$", $email_address))
						{
							//this is a valid email address
							//send the reply
							$from_data = $this->get_user_data($db,$this->classified_user_id);
							$subject = $this->get_ad_title($db,$message_data["regarding_ad"]);
							$message_data["classified_id"] = $message_data["regarding_ad"];
							if ($debug_comm)
							{
								echo $from_data->COMMUNICATION_TYPE." is communication_type<BR>\n";
							}

							$message_top = urldecode($this->messages[412])."\n".$email_address."\n\n";
							$message_top .= urldecode($this->messages[414])."\n";
							if ($from_data->COMMUNICATION_TYPE == 1)
							{
								$message_from = "From: ".$from_data->EMAIL.$separator."Reply-to: ".$from_data->EMAIL.$separator;
							}
							else
							{
								$message_from = "From: ".$this->configuration_data['site_email'].$separator."Reply-to: ".$this->configuration_data['site_email'].$separator;
								$message_data["message"] .= "\n\n".$this->configuration_data['classifieds_url']."?a=3&b=".$from_data->ID."&c=".$message_data["classified_id"];
							}
							$additional = "-f".$this->configuration_data['site_email'];
							$ip = $_SERVER['REMOTE_ADDR'];
							$host = @gethostbyaddr($ip);
							//$host = preg_replace("/^[^.]+./", "*.", $host);
							$message_data["message"] = $message_top.$message_data["message"];
							$message_data["message"] .= "\n\n".$_SERVER["REMOTE_ADDR"]." : ".$host;

							if ($this->configuration_data['email_configuration_type'] == 1)
								$message_data["message"] = str_replace("\n\n","\n",$message_data["message"]);
							if ($this->configuration_data['email_configuration'] == 1)
								mail($email_address, $subject, $message_data["message"], $message_from,$additional);
							elseif ($this->configuration_data['email_configuration'] == 2)
								mail($email_address, $subject, $message_data["message"], $message_from);
							else
								mail($email_address, $subject, $message_data["message"]);
						}
						else
						{
							//this is an invalid email address
							if ($debug_comm)
							{
								echo "invalid email address - ".$email_address." - <br>\n";
							}
							return false;
						}
					}
					if ($debug_comm) echo "sent successfully<br>\n";
					return true;
				}
				else
				{
					if ($debug_comm) echo "error 1<br>\n";
					return false;
				}
			}
			else
			{
				if ($debug_comm) echo "error 2<br>\n";
				return false;
			}
		}
		else
		{
			//no communication info
			if ($debug_comm) echo "no message data passed in<br>\n";
			return false;
		}
	} //end of function send_communication

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_thumbnail($db,$classified_id,$max_width=0,$max_height=0,$display_table_data=0,$affiliate=0)
	{
		$debug_thumbnail = 0;

		if($debug_thumbnail)
		{
			echo "<br>TOP OF DISPLAY_THUMBNAIL<Br>\n";
			echo $max_height." is max_height<bR>\n";
			echo $max_width." is max_width<BR>\n";
		}

		if (!$display_table_data)
		{
			$this->body .="<td align=center valign=middle ";
			if ($this->configuration_data['thumbnail_max_width'])
				$this->body .="width=".$this->configuration_data['thumbnail_max_width'];
			$this->body .=" class=thumbnail_td>";
		}
		if (($this->configuration_data['popup_while_browsing'])
			&& ($this->configuration_data['popup_while_browsing_width'])
			&& ($this->configuration_data['popup_while_browsing_height']))
		{
			$this->body .= "<a href=\"";
			if ($affiliate)
				$this->body .= $this->configuration_data['affiliate_url']."?aff=".$affiliate."&";
			else
				$this->body .= $this->configuration_data['classifieds_file_name']."?";
			$this->body .= "a=2&b=".$classified_id."\" ";
			$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
		}
		else
		{
			$this->body .="<a href=";
			if ($affiliate)
				$this->body .= $this->configuration_data['affiliate_url']."?aff=".$affiliate."&";
			else
				$this->body .= $this->configuration_data['classifieds_file_name']."?";
			$this->body .= "a=2&b=".$classified_id.">";
		}

		$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$classified_id." and display_order = 1";
		$image_url_result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$image_url_result)
		{
			$this->error_message = urldecode($this->messages[81]);
			return false;
		}
		else
			$show_image_url = $image_url_result->FetchNextObject();

		// Setup the thumbnail size for below
		if ($max_width != 0)
			$current_max_width = $max_width;
		else
			$current_max_width = $this->configuration_data['thumbnail_max_width'];

		if ($max_height != 0)
			$current_max_height = $max_height;
		else
			$current_max_height = $this->configuration_data['thumbnail_max_height'];
		if (($show_image_url->IMAGE_WIDTH > $current_max_width) && ($show_image_url->IMAGE_HEIGHT > $current_max_height))
		{
			$imageprop = ($current_max_width * 100) / $show_image_url->IMAGE_WIDTH;
			$imagevsize = ($show_image_url->IMAGE_HEIGHT * $imageprop) / 100 ;
			$final_image_width = $current_max_width;
			$final_image_height = ceil($imagevsize);

			if ($final_image_height > $current_max_height)
			{
				$imageprop = ($current_max_height * 100) / $show_image_url->IMAGE_HEIGHT;
				$imagehsize = ($show_image_url->IMAGE_WIDTH * $imageprop) / 100 ;
				$final_image_height = $current_max_height;
				$final_image_width = ceil($imagehsize);
			}
		}
		elseif ($show_image_url->IMAGE_WIDTH > $current_max_width)
		{
			$imageprop = ($current_max_width * 100) / $show_image_url->IMAGE_WIDTH;
			$imagevsize = ($show_image_url->IMAGE_HEIGHT * $imageprop) / 100 ;
			$final_image_width = $current_max_width;
			$final_image_height = ceil($imagevsize);
		}
		elseif ($show_image_url->IMAGE_HEIGHT > $current_max_height)
		{
			$imageprop = ($current_max_height * 100) / $show_image_url->IMAGE_HEIGHT;
			$imagehsize = ($show_image_url->IMAGE_WIDTH * $imageprop) / 100 ;
			$final_image_height = $current_max_height;
			$final_image_width = ceil($imagehsize);
		}
		else
		{
			$final_image_width = $show_image_url->IMAGE_WIDTH;
			$final_image_height = $show_image_url->IMAGE_HEIGHT;
		}

		if($debug_thumbnail)
		{
			echo $final_image_width." is the width<BR>\n";
			echo $final_image_height." is the height<br>\n";
			echo $this->configuration_data['thumbnail_max_width']." is max width<br>\n";
			echo $this->configuration_data['thumbnail_max_height']." is max height<br>\n";
		}

		$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$classified_id." and display_order = 1";
		$image_url_result = $db->Execute($this->sql_query);
		if($debug_thumbnail)
			echo $this->sql_query." is the query<br>\n";
		if (!$image_url_result)
		{
			return false;
		}
		elseif ($image_url_result->RecordCount() == 1)
		{
			$show_image_url = $image_url_result->FetchNextObject();

			if($debug_thumbnail)
			{
				echo $show_image_ur->MIME_TYPE." is mime_type<br>";
				echo $final_image_height." is final_image_height<BR>\n";
				echo $final_image_width." is final_image_width<bR>\n";
			}

			if (($this->configuration_data['photo_or_icon'] == 1) || (($final_image_width) && ($final_image_height)))
			{
				if($debug_thumbnail)
				{
					echo $show_image_url->THUMB_URL." is thumb url<br>\n";
					echo $show_image_url->IMAGE_URL." is image url<br>\n";
				}

				if (($show_image_url->THUMB_URL) && ($show_image_url->THUMB_URL != "0"))
				{
					$width = $final_image_width;
					$height = $final_image_height;
					$url = $show_image_url->THUMB_URL;
				}
				elseif ($show_image_url->IMAGE_URL)
				{
					$width = $final_image_width;
					$height = $final_image_height;
					$url = $show_image_url->IMAGE_URL;
				}
				else
				{
					//display the photo icon
					$url = $this->configuration_data['photo_icon_url'];
				}
			}
			else
			{
				//echo $this->configuration_data['photo_icon_url']." is the photo icon 1<Br>";
				$url = $this->configuration_data['photo_icon_url'];
			}

			if($debug_thumbnail)
			{
				echo $url.' is the url<br>';
				echo $width." is width<BR>\n";
				echo $height." is height<BR>\n";
			}
			$this->body .= $this->display_image($db, $url, $width, $height,$show_image_url->MIME_TYPE,1);
		}
		else
		{
			$this->sql_query = "select image_id,thumb,image_width,image_height,mime_type from ".$this->images_table." where classified_id = ".$classified_id." and display_order = 1";
			$image_db_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$image_db_result)
			{
				return false;
			}
			elseif ($image_db_result->RecordCount() == 1)
			{
				$show_image_url = $image_db_result->FetchNextObject();
				if ($this->configuration_data['photo_or_icon'] == 1)
				{
					if ($show_image_url->THUMB)
					{
						$this->body .= "<img src=get_image.php?image=".$show_image_url->IMAGE_ID."&size=1 width=".$final_image_width." height=".$final_image_height." border=0>";
					}
					else
					{
						//$this->body .=$final_image_width." is the width<BR>\n";
						//$this->body .=$final_image_height." is the height<br>\n";
						$this->body .= "<img src=get_image.php?image=".$show_image_url->IMAGE_ID." width=".$final_image_width." height=".$final_image_height." border=0>";
					}
				}
				else
				{
					$this->body .= $this->display_image($db, $this->configuration_data['photo_icon_url'], $width, $height,$show_image_url->MIME_TYPE,1);
				}
			}
			else
			{
				$this->body .="<font class=featured_ad_title_in_thumb>".$this->get_ad_title($db,$classified_id)." ";
			}
		}
		$this->body .="</a>";

		if (!$display_table_data)
			$this->body .= "</td>";

		return true;
	} //end of function display_thumbnail

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function sell_success_email($db,$classified_id=0)
	{
		if (($classified_id) && ($this->configuration_data['send_successful_placement_email']))
		{
			$this->page_id = 51;
			$this->get_text($db);
			$ad_data = $this->get_classified_data($db,$classified_id);
			$user_data = $this->get_user_data($db,$ad_data->SELLER);
			if (($ad_data) && ($user_data))
			{

				$subject = urldecode($this->messages[712]);
				$message = urldecode($this->messages[713])." ".$user_data->FIRSTNAME.",\n";
				$message .= urldecode($this->messages[714])."\n\n";
				if (!$this->configuration_data['admin_approves_all_ads'])
					$message .= $this->configuration_data['classifieds_url']."?a=2&b=".$classified_id;

				if ($this->configuration_data['email_header_break'])
					$separator = "\n";
				else
					$separator = "\r\n";

				$from = "From: ".$this->configuration_data['site_email'].$separator."Reply-to: ".$this->configuration_data['site_email'].$separator;
				$additional = "-f".$this->configuration_data['site_email'];

				if ($this->configuration_data['email_configuration_type'] == 1)
					$message = str_replace("\n\n","\n",$message);

				if ($this->configuration_data['email_configuration'] == 1)
					mail($user_data->EMAIL, $subject, $message, $from,$additional);
				elseif ($this->configuration_data['email_configuration'] == 2)
					mail($user_data->EMAIL, $subject, $message, $from);
				else
					mail($user_data->EMAIL, $subject, $message);
				//mail($user_data->EMAIL,$subject,$message,$from);

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

	} //end of function sell_success_email

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_payment_types_accepted($db)
	{
		//expire user credits
		$this->sql_query = "select * from ".$this->payment_types_table." where accepted = 1";
		$payment_type_results = $db->Execute($this->sql_query);
		if (!$payment_type_results)
		{
			return false;
		}
		elseif ($payment_type_results->RecordCount() > 0)
		{
			return $payment_type_results;
		}
		else
		{
			return false;
		}
	} //end of function get_payment_types_accepted

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_tax($db,$user_data=0)
	{
		if ($this->subtotal && $this->configuration_data['charge_tax_by'] && $user_data)
		{
			//charge by state/province
			if ($user_data->STATE != "none")
			{
				$this->sql_query = "SELECT tax,tax_type FROM ".$this->states_table." WHERE abbreviation = '".$user_data->STATE."'";
				$get_tax_results = $db->Execute($this->sql_query);
				if (!$get_tax_results)
				{
					return false;
				}
				elseif ($get_tax_results->RecordCount() == 1)
				{
					$show_tax = $get_tax_results->FetchRow();
					$state_tax = (($show_tax['tax_type']==1) ? $show_tax['tax'] : ($show_tax['tax']/100 * $this->subtotal));
				}
				else
				{
					$state_tax = 0;
				}
			}
			else
			{
				$state_tax = 0;
			}

			//charge by country
			if ($user_data->COUNTRY != "none")
			{
				$this->sql_query = "SELECT tax,tax_type FROM ".$this->countries_table." WHERE abbreviation = '".$user_data->COUNTRY."'";
				$get_tax_results = $db->Execute($this->sql_query);
				if (!$get_tax_results)
				{
					return false;
				}
				elseif ($get_tax_results->RecordCount() == 1)
				{
					$show_tax = $get_tax_results->FetchRow();
					$country_tax = (($show_tax['tax_type']==1) ? $show_tax['tax'] : ($show_tax['tax']/100 * $this->subtotal));
				}
				else
				{
					$country_tax = 0;
				}
			}
			else
			{
				$country_tax = 0;
			}
			$total_tax = $state_tax + $country_tax;
			return sprintf("%01.2f",$total_tax);
		}
		else
		{
			return 0;
		}

	} //end of function get_tax

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function expire_subscriptions($db)
	{
		//echo $this->configuration_data['subscription_expire_period_notice']." sub exp notice<Br>\n";
		if ( $this->configuration_data['subscription_expire_period_notice'])
		{
			$old_page_id = $this->page_id;
			$this->page_id = 87;
			$this->get_text($db);
			$this->page_id = $old_page_id;
			$notice_time = ($this->shifted_time($db) + (86400 * $this->configuration_data['subscription_expire_period_notice']));
			$this->sql_query = "select * from ".$this->user_subscriptions_table." where subscription_expire < ".$notice_time." and notice_sent = 0";
			//echo $this->sql_query."<br>";
			$subscription_notice_results = $db->Execute($this->sql_query);
			if (!$subscription_notice_results)
			{
				return false;
			}
			elseif ($subscription_notice_results->RecordCount())
			{
				while ($show = $subscription_notice_results->FetchNextObject())
				{
					if($this->configuration_data['send_admin_end_email'])
					{
						$user_data = $this->get_user_data($db,$show->USER_ID);
						$message_data["subject"] =  urldecode($this->messages[1435]);
						$message_data["message"] = $user_data->USERNAME."\n\n";
						$message_data["message"] .= urldecode($this->messages[1436])."\n\n";
						$message_data["message"] .= date("M d, Y H:i", $show->SUBSCRIPTION_EXPIRE)."\n\n";
						$message_data["message"] .= $this->configuration_data['classifieds_url']."\n\n";

						if ($this->configuration_data['email_header_break'])
							$separator = "\n";
						else
							$separator = "\r\n";

						$message_data["from"] = "From: ".$this->configuration_data['site_email'].$separator."Reply-to: ".$this->configuration_data['site_email'].$separator;

						if ($this->configuration_data['email_configuration_type'] == 1)
							$message_data["message"] = str_replace("\n\n","\n",$message_data["message"]);

						$additional = "-f".$this->configuration_data['site_email'];

						if ($this->configuration_data['email_configuration'] == 1)
							mail($user_data->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"],$additional);
						elseif ($this->configuration_data['email_configuration'] == 2)
							mail($user_data->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"]);
						else
							mail($user_data->EMAIL, $message_data["subject"], $message_data["message"]);
						//@mail($user_data->EMAIL,urldecode($this->messages[558]),$message,$message_from);
					}

					$this->sql_query = "update ".$this->user_subscriptions_table." set
						notice_sent = 1
						where subscription_id = ".$show->SUBSCRIPTION_ID;
					//echo $this->sql_query."<br>";
					$update_result = $db->Execute($this->sql_query);
					if (!$update_result)
					{
						return false;
					}
				}
			}
		}

		$this->sql_query = "select * from ".$this->user_subscriptions_table." where subscription_expire < ".$this->shifted_time($db);
		$expire_subscriptions_results = $db->Execute($this->sql_query);
		//echo $this->sql_query."<bR>\n";
		if (!$expire_subscriptions_results)
		{
			return false;
		}
		else
		{
			//check to see if ads are expired also
			if($expire_subscriptions_results->RecordCount() > 0)
			{
				while ($show_user = $expire_subscriptions_results->FetchNextObject())
				{
					$this->sql_query = "select price_plan_id from ".$this->user_groups_price_plans_table." where id = ".$show_user->USER_ID;
					$user_price_plan_results = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$user_price_plan_results)
					{
						return false;
					}
					elseif ($user_price_plan_results->RecordCount() == 1)
					{
						$show_price_plan = $user_price_plan_results->FetchNextObject();
						$this->sql_query = "select ad_and_subscription_expiration from ".$this->price_plans_table." where price_plan_id = ".$show_price_plan->PRICE_PLAN_ID;
						//echo $this->sql_query."<br>\n";
						$user_expire_ads_result = $db->Execute($this->sql_query);
						if (!$user_expire_ads_result)
						{
							return false;
						}
						elseif ($user_expire_ads_result->RecordCount() == 1)
						{
							$show_expired_ads_also = $user_expire_ads_result->FetchNextObject();
							if ($show_expired_ads_also->AD_AND_SUBSCRIPTION_EXPIRATION)
							{
								//expire the ads also
								$this->sql_query = "update ".$this->classifieds_table." set
									live = 0,
									ends = ".$this->shifted_time($db)."
									where seller = ".$show_user->USER_ID;
								$expire_ads_also_result = $db->Execute($this->sql_query);
								//echo $this->sql_query."<br>\n";
								if (!$expire_ads_also_result)
								{
									return false;
								}
							}
						}
						else
							return false;
					}
					else
						return false;
				}

				$this->sql_query = "delete from ".$this->user_subscriptions_table." where subscription_expire < ".$this->shifted_time($db);
				$expire_subscriptions_results = $db->Execute($this->sql_query);
				//echo $this->sql_query."<bR>\n";

				if (!$expire_subscriptions_results)
					return false;
				else
					return true;
			}
			else
				return true;
		}

	}// end of function expire_subscriptions

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function LoadRTE($sFieldName, $sHTMLContent, $iWidth, $iHeight, $bButtons, $secure = 0)
	{

		$this->body .=  "<!-- Start Rich Text Box //-->\n\t";
		include_once 'classes/AplosRTE/rte.php';

		// Generate URL for AplosRTE
		if($secure)
		{
			if($this->configuration_data['classifieds_ssl_url'] && $this->configuration_data['use_ssl_in_sell_process'])
			{
				/*
				$parsed_url = parse_url($this->configuration_data['classifieds_ssl_url']);
				$url = $parsed_url['scheme']."://".$parsed_url['host'].$parsed_url['path'];
				$url = str_replace(basename($url), "", $url);
				*/
				$url = 'classes/AplosRTE/';
			}
			else
			{
				//$url = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/'.'classes/AplosRTE/';
				$url = 'classes/AplosRTE/';
			}
		}
		else
		{
			//echo "both switches not yes but secure=0<br>";
			//$url = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/'.'classes/AplosRTE/';
			$url = 'classes/AplosRTE/';
		}

		$editor = new rteEditor( 	$rtePath      = $url, //full URL to AplosRTE dir - use trailing slash
									$imageGallery = true,
									$rteTheme     = '' ); // default,blue,green,silver

		$this->body .= $editor->initRTE( $fieldname    = $sFieldName, //fieldname
		                       $content      = $sHTMLContent, //default content
		                       $rteWidth     = $iWidth,
    		                   $rteheight    = $iHeight,
    		                   $showEditor   = true, //show toolbar
    		                   $readonly     = false, //textarea readonly
    		                   '' ); // CSS style for textarea if not Gecko or IE
		$this->body .=  "<!-- End Rich Text Box //-->\n";
		return true;

	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_sql_zip_filter_in_statement($db)
	{
		if (($this->zip_filter) && ($this->zip_filter_distance))
		{
			if ($this->configuration_data['use_zip_distance_calculator'] == 2)
			{
				if ($this->debug)
				{
					echo "using the uk zip codes<br>\n";
					echo $db_host." is db_host<br>\n";
					echo $db_username." is db_username<br>\n";
					echo $db_password." is db_password<br>\n";
					echo $database." is database<br>\n";
					echo $this->zip_filter." is zip filter<bR>";
					echo $this->zip_filter_distance." is zip filter distance<bR>";
				}

				$postcode_list = array();

				$postcode = trim($options['postcode_from']);
				$this->sql_query = "SELECT postcode, x, y FROM cgi_postcodes WHERE postcode = ' " . trim($this->zip_filter) . "'";
				if ($this->debug) echo $this->sql_query."<bR>\n";
				$postcode_data_result = $db->Execute($this->sql_query);
				$result = mysql_query($sql);
				if($postcode_data_result->RecordCount() <> 1)
				{
					return false;
				}
				while ($row = $postcode_data_result->FetchNextObject())
				{
					$user_x = $row->X;
					$user_y = $row->Y;
				}
				//$_radius = $this->zip_filter_distance + 5;
				$_radius = $this->zip_filter_distance;
				$_radius = $_radius / 0.621;
				$_radius = sprintf("%.0f", $_radius * 1000);
				$_radius = $_radius / 2;
				$_lowerx = $user_x - $_radius;
				$_lowery = $user_y - $_radius;
				$_upperx = $user_x + $_radius;
				$_uppery = $user_y + $_radius;
				$this->sql_query = "SELECT x, y, postcode FROM cgi_postcodes WHERE (x >= $_lowerx and y >= $_lowery) AND (x <= $_upperx and y <= $_uppery)";
				if ($this->debug) echo $this->sql_query."<bR>\n";
				$postcode_result = $db->Execute($this->sql_query);
				if((!$postcode_result) || ($postcode_result->RecordCount() == 0))
				{
					if ($this->debug) echo $this->sql_query."<bR>\n";
					return false;
				}
				$zip_in_statement .= "location_zip in (";
				$zip_count = 0;
				while ($row = $postcode_result->FetchNextObject())
				{

					$distance = sqrt((($row->X -$user_x) * ($row->X -$user_x)) + (($row->Y - $user_y) * ($row->Y - $user_y)));
					$km = sprintf("%.2f", $distance / 1000);
					$miles = sprintf("%.2f", $km * 0.621);
					$this->uk_postcodes[urlencode($row->POSTCODE)] = $miles;
					if ($this->debug) echo $row->POSTCODE." has a distance of: ".$miles."<br>";
					if ($zip_count == 0)
					{
						$zip_in_statement .= "'".urlencode($row->POSTCODE)."'";
					}
					else
					{
						$zip_in_statement .= ",'".urlencode($row->POSTCODE)."'";
					}
					$zip_count++;
				}
				$zip_in_statement .= ")";
				if ($this->debug) echo $zip_in_statement." is zip in statement within site_class<bR>\n";
				return $zip_in_statement;
			}
			elseif ($this->configuration_data['use_zip_distance_calculator'] == 1)
			{
				//get the longitude and latitude of the zip code entered
				$this->sql_query = "select * from ".$this->postal_code_table." where zipcode = ".$this->zip_filter." limit 1";
				//echo $this->sql_query."<br>\n";
				$zip_result = $db->Execute($this->sql_query);
				if (!$zip_result)
				{
					return false;
				}
				elseif ($zip_result->RecordCount() == 1)
				{
					//zip code data found...continue
					$show_zip_data = $zip_result->FetchNextObject();

					//get the minimum and maximum longitude and latitude
					$this->RadiusAssistant($show_zip_data->LATITUDE, $show_zip_data->LONGITUDE, $this->zip_filter_distance);

					$this->search_zip_latitude = $show_zip_data->LATITUDE;
					$this->search_zip_longitude = $show_zip_data->LONGITUDE;

					//get the zip codes within distance using min and max longitude and latitude
					$this->sql_query = "select distinct(zipcode) from ".$this->postal_code_table." where
						((latitude >= ".$this->min_latitude." and
						latitude <= ".$this->max_latitude.") and
						(longitude >= ".$this->min_longitude." and
						longitude <= ".$this->max_longitude."))";
					//echo $this->sql_query."<br>\n";
					$range_zip_result = $db->Execute($this->sql_query);
					if (!$range_zip_result)
					{
						return false;
					}
					elseif ($range_zip_result->RecordCount() > 0)
					{
						$zip_in_statement .= "location_zip in (";
						$zip_count = 0;
						while ($show_zip_in = $range_zip_result->FetchNextObject())
						{
							if ($zip_count == 0)
								$zip_in_statement .= $show_zip_in->ZIPCODE;
							else
								$zip_in_statement .= ",".$show_zip_in->ZIPCODE;
							$zip_count++;
						}
						$zip_in_statement .= ")";
					}
					else
					{
						//no results
						$zip_in_statement .= "location_zip in ()";
					}
					return $zip_in_statement;
				}
				else
				{
					//category_id is missing
					return false;
				}
			}
			elseif ($this->configuration_data['use_zip_distance_calculator'] == 3)
			{
				//get the longitude and latitude of the zip code entered
				//echo $this->zip_filter." is zip_filter in zip distance<bR>\n";
				$this->sql_query = "select * from ".$this->postal_code_table." where zipcode = \"".substr($this->zip_filter,0,3)."\" limit 1";
				//echo $this->sql_query."<br>\n";
				$zip_result = $db->Execute($this->sql_query);
				if (!$zip_result)
				{
					//echo $this->sql_query."<br>\n";
					//echo $db->ErrorMsg()."<bR>\n";
					return false;
				}
				elseif ($zip_result->RecordCount() == 1)
				{
					//zip code data found...continue
					$show_zip_data = $zip_result->FetchNextObject();

					//get the minimum and maximum longitude and latitude
					$this->RadiusAssistant($show_zip_data->LATITUDE, $show_zip_data->LONGITUDE, $this->zip_filter_distance);

					$this->search_zip_latitude = $show_zip_data->LATITUDE;
					$this->search_zip_longitude = $show_zip_data->LONGITUDE;

					//get the zip codes within distance using min and max longitude and latitude
					$this->sql_query = "select distinct(zipcode) from ".$this->postal_code_table." where
						((latitude >= ".$this->min_latitude." and
						latitude <= ".$this->max_latitude.") and
						(longitude >= ".$this->min_longitude." and
						longitude <= ".$this->max_longitude."))";
					//echo $this->sql_query."<br>\n";
					$range_zip_result = $db->Execute($this->sql_query);
					if (!$range_zip_result)
					{
						return false;
					}
					elseif ($range_zip_result->RecordCount() > 0)
					{
						$zip_in_statement .= "location_zip in (";
						$zip_count = 0;
						while ($show_zip_in = $range_zip_result->FetchNextObject())
						{
							if ($zip_count == 0)
								$zip_in_statement .= "\"".$show_zip_in->ZIPCODE."\"";
							else
								$zip_in_statement .= ",\"".$show_zip_in->ZIPCODE."\"";
							$zip_count++;
						}
						$zip_in_statement .= ")";
					}
					else
					{
						//no results
						$zip_in_statement .= "location_zip in ()";
					}
					return $zip_in_statement;
				}
				else
				{
					//category_id is missing
					return false;
				}
			}
		}
		else
		{
			//category_id is missing
			return false;
		}

	} //end of get_sql_zip_filter_in_statement

//####################################################################################

	function RadiusAssistant($Latitude, $Longitude, $Miles)
	{
		$EQUATOR_LAT_MILE = 69.172;
		$this->max_latitude = $Latitude + $Miles / $EQUATOR_LAT_MILE;
		$this->min_latitude = $Latitude - ($this->max_latitude - $Latitude);
		$this->max_longitude = $Longitude + $Miles / (cos($this->min_latitude * M_PI / 180) * $EQUATOR_LAT_MILE);
		$this->min_longitude = $Longitude - ($this->max_longitude - $Longitude);
	} //end of function RadiusAssistant

//##################################################################################

	function calculate_distance_from_zip($dblLat2,$dblLong2)
	{
		$EARTH_RADIUS_MILES = 3963;
		$dist = 0;
		//convert degrees to radians
		$current_latitude = $this->search_zip_latitude * M_PI / 180;
		$current_longitude = $this->search_zip_longitude * M_PI / 180;
		$dblLat2 = $dblLat2 * M_PI / 180;
		$dblLong2 = $dblLong2 * M_PI / 180;
		if ($current_latitude != $dblLat2 || $current_longitude != $dblLong2)
		{
			//the two points are not the same
			$dist =
				sin($current_latitude) * sin($dblLat2)
				+ cos($current_latitude) * cos($dblLat2)
				* cos($dblLong2 - $current_longitude);

			$dist =
				$EARTH_RADIUS_MILES
				* (-1 * atan($dist / sqrt(1 - $dist * $dist)) + M_PI / 2);
		}
		return $dist;
	} //end of function Calculate_distance_from_zip

//##################################################################################

	function check_category_new_ad_icon_use($db,$category_id=0,$category_cache_instead=0)
	{
		//echo $this->configuration_data['category_new_ad_image']." is CATEGORY_NEW_AD_IMAGE<br>\n";
		//echo $this->configuration_data['category_new_ad_limit']." is CATEGORY_NEW_AD_LIMIT<br>\n";
		if ((strlen(trim($this->configuration_data['category_new_ad_image'])) > 0)
			&& ($this->configuration_data['category_new_ad_limit'])
			&& ($category_id))
		{
			$date_limit = ($this->shifted_time($db) - ($this->configuration_data['category_new_ad_limit'] * 3600));
			$in_statement = $this->get_sql_in_statement($db,$category_id);
			$this->sql_query = "select count(id) as count from ".$this->classifieds_table."	where
				live = 1 and
				ends > ".$this->shifted_time($db)." and
				date > ".$date_limit." and
				category ".$in_statement;
			//echo $this->sql_query."<br>\n";
			$new_ad_result = $db->Execute($this->sql_query);
			if (!$new_ad_result)
			{
				return false;
			}
			elseif ($new_ad_result->RecordCount() == 1)
			{
				$show_count = $new_ad_result->FetchRow();
				if ($show_count['count'] > 0)
				{
					if ($category_cache_instead)
						$this->category_cache .= "<img src=\"".$this->configuration_data['category_new_ad_image']."\" border=0>";
					else
						$this->body .= "<img src=\"".$this->configuration_data['category_new_ad_image']."\" border=0>";
				}
			}
		}
	} //end of function check_category_new_ad_icon_use

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_user_subscription($db)
	{
		$this->function_name = "check_user_subscription";

		$this->sql_query = "select * from ".$this->user_subscriptions_table." where subscription_expire > ".$this->shifted_time($db)." and user_id = ".$this->auction_user_id;
		$get_subscriptions_results = $db->Execute($this->sql_query);
		if($this->configuration_data['debug_sell'])
		{
			$this->debug_display($db, $this->filename, $this->function_name, "user_subscriptions_table", "get data from user subscriptions table by subscription expire and user id");
		}
		if (!$get_subscriptions_results)
		{
			return false;
		}
		elseif ($get_subscriptions_results->RecordCount() == 0)
		{
			return false;
		}
		elseif ($get_subscriptions_results->RecordCount() > 0)
		{
			return true;
		}
	} // end of function check_user_subscription

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_balance_approval($db,$transaction_id=0,$cc_transaction_id=0)
	{
		if ($transaction_id)
		{
			$this->sql_query = "update ".$this->balance_transactions." set
				approved = 1,
				cc_transaction_id = ".$cc_transaction_id."
				where transaction_id = ".$transaction_id;
			$update_balance_transaction_result = $db->Execute($this->sql_query);
			if (!$update_balance_transaction_result)
				return false;
			else
				return true;
		}
		else
			return false;
	} //end of function update_balance_approval

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_price_plan_from_group($db,$group_id=0,$auctions=0)
	{
		if ($group_id)
		{
			$this->sql_query = "select * from ".$this->groups_table." where group_id = ".$group_id;
			$group_price_plan_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			//$this->body .=$this->sql_query." is get_price_plan query<br>\n";
			if (!$group_price_plan_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($this->sql_query,$db->ErrorMsg());
				return false;
			}
			elseif ($group_price_plan_result->RecordCount() == 1)
			{
				$show_group_price_plan = $group_price_plan_result->FetchNextObject();

				if($auctions)
					$this->sql_query = "select * from ".$this->price_plans_table." where price_plan_id = ".$show_group_price_plan->AUCTION_PRICE_PLAN_ID;
				else
					$this->sql_query = "select * from ".$this->price_plans_table." where price_plan_id = ".$show_group_price_plan->PRICE_PLAN_ID;
				$price_plan_result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is the query<br>\n";
				//$this->body .=$this->sql_query." is get_price_plan query<br>\n";
				if (!$price_plan_result)
				{
					$this->error_message = $this->internal_error_message;
					$this->site_error($this->sql_query,$db->ErrorMsg());
					return false;
				}
				elseif ($price_plan_result->RecordCount() == 1)
				{
					$show_price_plan = $price_plan_result->FetchNextObject();
					return $show_price_plan;
				}
				else
				{
					return false;
				}
			}
			else
			{
				//just display the user_id
				return false;
			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function get_price_plan_from_group

//########################################################################

	function get_auctions_price_plan_from_group($db,$group_id=0)
	{
		if ($group_id)
		{
			$this->sql_query = "select * from ".$this->groups_table." where group_id = ".$group_id;
			$group_price_plan_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			//$this->body .=$this->sql_query." is get_price_plan query<br>\n";
			if (!$group_price_plan_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($this->sql_query,$db->ErrorMsg());
				return false;
			}
			elseif ($group_price_plan_result->RecordCount() == 1)
			{
				$show_group_price_plan = $group_price_plan_result->FetchNextObject();
				$this->sql_query = "select * from ".$this->auctions_price_plans_table." where price_plan_id = ".$show_group_price_plan->AUCTION_PRICE_PLAN_ID;
				$price_plan_result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is the query<br>\n";
				//$this->body .=$this->sql_query." is get_price_plan query<br>\n";
				if (!$price_plan_result)
				{
					$this->error_message = $this->internal_error_message;
					$this->site_error($this->sql_query,$db->ErrorMsg());
					return false;
				}
				elseif ($price_plan_result->RecordCount() == 1)
				{
					$show_price_plan = $price_plan_result->FetchNextObject();
					return $show_price_plan;
				}
				else
				{
					return false;
				}
			}
			else
			{
				//just display the user_id
				return false;
			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function get_auctions_price_plan_from_group

//########################################################################

	function Reconnect()
	{
		include("config.php");
		include_once("adodb.inc.php");

		$db = &ADONewConnection('mysql');

		if (!$db->Connect($db_host, $db_username, $db_password, $database))
		{
			echo "Could not reconnect to database";
			exit;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function expand_array($a)
	{
		ob_start();
		print_r($a);
		$t= ob_get_contents();
		ob_end_clean();
		for($c=10;$c>=1;$c--)
		{
			$search="\n ".str_repeat(" ",4*$c-1);
			$replace="<br>\n".str_repeat("&nbsp;",8*$c);
			$t= str_replace($search,$replace,$t);
		}
		//Final adjustment which takes care of the single last closing parenthesis
		$t= str_replace("\n\n)","<br>\n)",$t);

		return $t;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function getip() {
	   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
	   $ip = getenv("HTTP_CLIENT_IP");

	   else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
	   $ip = getenv("HTTP_X_FORWARDED_FOR");

	   else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
	   $ip = getenv("REMOTE_ADDR");

	   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
	   $ip = $_SERVER['REMOTE_ADDR'];

	   else
	   $ip = "unknown";

	   return($ip);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function encrypt($string, $key) {
   $result = '';
   for($i=0; $i<strlen($string); $i++) {
     $char = substr($string, $i, 1);
     $keychar = substr($key, ($i % strlen($key))-1, 1);
     $char = chr(ord($char)+ord($keychar));
     $result.=$char;
   }

   return base64_encode($result);
  }

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function decrypt($string, $key) {
   $result = '';
   $string = base64_decode($string);

   for($i=0; $i<strlen($string); $i++) {
     $char = substr($string, $i, 1);
     $keychar = substr($key, ($i % strlen($key))-1, 1);
     $char = chr(ord($char)-ord($keychar));
     $result.=$char;
   }

   return $result;
  }

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_number_of_bids ($db,$auction_id)
	{
		$this->function_name = "get_number_of_bids";
		if ($auction_id)
		{
			$this->sql_query = "select count(*) as total_bids from ".$this->bid_table." where auction_id =".$auction_id;
			$bid_count_result = $db->Execute($this->sql_query);
			if (!$bid_count_result)
			{
				//echo $this->sql_query." is the state query<br>\n";
				if($this->configuration_data['debug_browse']){
					$this->debug_display($this->sql_query, $db, $this->filename,$this->function_name,"bid_table","");
				}
				$this->error_message = $this->messages[100080];
				return false;
			}
			elseif ($bid_count_result->RecordCount() == 1)
			{
				$show = $bid_count_result->FetchNextObject();
				return $show->TOTAL_BIDS;
			}
			else
			{
				return false;
			}
		}
		return false;
	} //end of function get_number_of_bids
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

function DateDifference ($interval, $date1,$date2)
	{
		$difference =  $date2 - $date1;
		switch ($interval)
		{
			case "w":
				$returnvalue  =$difference/604800;
				break;
			case "d":
				$returnvalue  = $difference/86400;
				break;
			case "h":
				$returnvalue = $difference/3600;
				break;
			case "m":
				$returnvalue  = $difference/60;
				break;
			case "s":
				$returnvalue  = $difference;
				break;
	    	}
	    	return intval($returnvalue);
	} //end of function DateDifference

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function print_number($number=0)
	{
		// Get number format from database
		if($this->configuration_data['number_format'] == 0)
		{
			// American
			return number_format($number, 2, '.', ',');
		}
		elseif($this->configuration_data['number_format'] == 1)
		{
			// European
			return number_format($number, 2, ',', '.');
		}
		elseif($this->configuration_data['number_format'] == 2)
		{
			// Japanese...no decimal point
			return number_format($number, 0, '.', ',');
		}
		else
		{
			// Shouldnt get here
			return $number;
		}
	} //end of print_number

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

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

	function get_time($hour,$min,$month,$day,$year)
	{
		$time = mktime($hour, $min, 0, $month, $day, $year);
		if($this->configuration_data['debug_sell'])
		{
			echo $time . ' is $time<br>';
			echo $min . ' is end minute.<br>';
			echo $hour . ' is end hour.<br>';
			echo $month . ' is end month.<br>';
			echo $day . ' is end day.<br>';
			echo $year . ' is end year.<br>';
			echo '<br>';
		}
		return $time;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_high_bidder_username($db,$auction_id)
	{
		if ($this->debug)
		{
			echo "<br>TOP OF GET_HIGH_BIDDER_USERNAME<Br>\n";
		}
		if ($auction_id)
		{
			$this->sql_query = "select bidder from ".$this->bid_table." where auction_id =".$auction_id." order by bid desc,time_of_bid asc limit 1";
			$high_bidder_result = $db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$high_bidder_result)
			{
				if ($this->debug) echo $this->sql_query."<br>\n";
				$this->error_message = $this->messages[80];
				return false;
			}
			elseif ($high_bidder_result->RecordCount() == 1)
			{
				$show = $high_bidder_result->FetchRow();
				$username = $this->get_user_name($db,$show['bidder']);
				if ($this->debug) echo $username." is the high bidder username to return<br>\n";
				return $username;
			}
			else
			{
				return false;
			}
		}
		return false;
	} //end of function get_high_bidder_username

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_high_bidder($db,$auction_id=0)
	{
		if ($this->debug)
		{
			echo "<br>TOP OF GET_HIGH_BIDDER<Br>\n";
		}
		$this->sql_query = "select * from ".$this->bid_table." where auction_id=".$auction_id." order by bid desc,time_of_bid asc limit 1";
		$high_bid_result = $db->Execute($this->sql_query);
		if ($this->debug) echo $this->sql_query."<br>\n";
		if (!$high_bid_result)
		{
			if ($this->debug) echo $this->sql_query."<br>\n";
			return false;
		}
		elseif ($high_bid_result->RecordCount() == 1)
		{
			$show_high_bidder = $high_bid_result->FetchRow();
			return $show_high_bidder;
		}
		else
		{
			return 0;
		}
	}

//#######################################################################

	function item_price($item_type_passed=1)
	{
		if($item_type_passed == 2)
		{
			return "minimum_bid";
		}
		else
		{
			return "price";
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_blacklist($db,$seller_id,$auction_user_id)
	{
		if (($seller_id) && ($auction_user_id))
		{
			$this->sql_query = "select * from ".$this->blacklist_table." where seller_id =".$seller_id." and user_id =".$auction_user_id." ";
			if ($this->debug_display_auction)
				echo $this->sql_query." is query 2 <bR>";
			$blacklist_result = $db->Execute($this->sql_query);
			if(!$blacklist_result){
				return false;
			}
			else if($blacklist_result->RecordCount() > 0 )
			{
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
	} //end of function check_blacklist

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_invitedlist($db,$seller_id=0,$auction_user_id=0)
	{
		if (($seller_id) && ($auction_user_id))
		{
			//check to see if there are any in the invited table for this seller
			$this->sql_query = "select * from ".$this->invitedlist_table." where seller_id =".$seller_id;
			if ($this->debug_display_auction) echo $this->sql_query." is query checking if any invited list <bR>";
			$any_invitedlist_result = $db->Execute($this->sql_query);
			if (!$any_invitedlist_result)
			{
				return 0;
			}
			elseif ($any_invitedlist_result->RecordCount() > 0)
			{
				//check to see if this auction_user_id in invited list attached with this seller
				$this->sql_query = "select * from ".$this->invitedlist_table." where seller_id =".$seller_id." and user_id =".$auction_user_id." ";
				if ($this->debug_display_auction)
					echo $this->sql_query." is <bR>";
				$invitedlist_result = $db->Execute($this->sql_query);
				if(!$invitedlist_result)
				{
					return 0;
				}
				else if($invitedlist_result->RecordCount() > 0 )
				{
					return 1;
				}
				else
				{
					return 0;
				}
			}
			else
			{
				//there are no invited buyers in this sellers list
				//this is treated as if all buyers are invited
				return 2;
			}
		}
	} //end of function check_invitedlist

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function shifted_time($db)
	{
		$sql_query = "select time_shift from ".$this->site_configuration_table;
		$result = $db->Execute($sql_query);

		if(!$result)
		{
			//echo $this->sql_query.'<Br>';
			return false;
		}
		else
			$time = $result->FetchRow();

		return time() + (3600 * $time['time_shift']);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	* Displays the appropriate page for credit card processing failure
	* Page is determined by $this->page_id set in payment handler
	*
	* @param 	integer	$success 					1=success | 0=failure
	* @param 	string	$additional_msg 			Message set in payment handler
	* @param 	string	$handler_error_response 	cc company error
	* @param 	string	link						Link to: try again(failure) | to view listing,etc(success)
	*
	* @return 	string	$body						Call site error if incorrect page id, else return $body
	*/
	function cc_success_failure($db,$success,$additional_msg="",$handler_error_response="")
	{
		$section_title = "";
		$page_title = "";
		$page_description_1 = "";
		$page_description_2 = "";
		$internal_msg_1 = "";
		$internal_msg_2 = "";
		$link = "";
		switch ($this->page_id)
		{
			case 109://subscription renewal
				$css_Section_Title = "section_title";
				$css_Page_Title = "page_title";
				$css_Page_Description = "page_description";
				$css_Success_Failure_Message = "success_failure_message";
				$css_Link = "back_to_my_info_link";

				$this->get_text($db);
				$section_title = "";
				$page_title = urldecode($this->messages[1669]);
				$page_description_1 = urldecode($this->messages[1670]);
				$page_description_2 = "";
				$internal_msg_1 = ($success) ? urldecode($this->messages[1679]) : urldecode($this->messages[200111]);
				$internal_msg_2 = "";
				$link = "<A HREF=\"".trim($this->configuration_data["classifieds_url"])."?a=4&b=3\" class=$css_Link>"
					.urldecode($this->messages[1672])."</A>";
				if ($success)
					$this->remove_renew_subscription_session($db,$this->session_id);
			break;
			case 175://adding to an account balance
				$css_Section_Title = "user_management_page_title";
				$css_Page_Title = "page_title";
				$css_Page_Description = "page_description";
				$css_Success_Failure_Message = "success_failure_message";
				$css_Link = "my_account_link";

				$this->get_text($db);
				$section_title = "";
				$page_title = urldecode($this->messages[2531]);
				$page_description_1 = urldecode($this->messages[2532]);
				$page_description_2 = "";
				$internal_msg_1 = "";
				$internal_msg_2 = ($success) ? urldecode($this->messages[2537]) : urldecode($this->messages[200112]);
				$link = "<A HREF=\"".$this->configuration_data["classifieds_url"]."?a=4&b=3\" class=$css_Link>"
					.urldecode($this->messages[2536])."</A>";
				if ($success)
					$this->remove_account_session($db,$this->session_id);
			break;
			case 180://paying for an invoice
				$css_Section_Title = "user_management_page_title";
				$css_Page_Title = "page_title";
				$css_Page_Description = "page_description";
				$css_Success_Failure_Message = "success_failure_message";
				$css_Link = "my_account_link";

				$this->get_text($db);
				$section_title = "";
				$page_title = urldecode($this->messages[3142]);
				$page_description_1 = urldecode($this->messages[3143]);
				$page_description_2 = "";
				$internal_msg_1 = ($success) ? urldecode($this->messages[3167]) : urldecode($this->messages[3168]);
				$internal_msg_2 = "";
				$link = "<A HREF=\"".$this->configuration_data["classifieds_url"]."?a=4&b=3\" class=$css_Link>"
					.urldecode($this->messages[3169])."</A>";
				if ($success)
					$this->remove_invoice_session($db,$this->session_id);
			break;
			case 58://renewing or upgrading a listing
				$css_Section_Title = "page_title";
				$css_Page_Title = "page_title";
				$css_Page_Description = "page_description";
				$css_Success_Failure_Message = "error_message";
				$css_Link = "my_account_link";

				$this->get_text($db);
				$section_title = "";
				$page_title = urldecode($this->messages[850]);
				$page_description_1 = urldecode($this->messages[851]);
				$page_description_2 = "";
				$internal_msg_1 = ($success) ? urldecode($this->messages[852]) : urldecode($this->messages[857]);
				$internal_msg_2 = "";
				$link = "<A HREF=\"".$this->configuration_data["classifieds_file_name"]."?a=4&b=1\" class=my_account_link>"
					.urldecode($this->messages[860])."</A>";
				if ($success)
					$this->remove_renew_upgrade_session($db,$this->session_id);
			break;
			case 14://placing a listing
				$css_Section_Title = "place_ad_section_title";
				$css_Page_Title = "page_title";
				$css_Page_Description = "page_description";
				$css_Success_Failure_Message = "error_message";
				$css_Link = "view_ad_link";

				$this->get_text($db);
				$section_title = urldecode($this->messages[1365]);
				$page_title = urldecode($this->messages[177]);
				$page_description_1 = ($success) ? urldecode($this->messages[180]) : urldecode($this->messages[178]);
				if (!($handler_error_response || $success))
					$page_description_2 = urldecode($this->messages[179]);
				$internal_msg_1 = ($success) ? urldecode($this->messages[653]) : urldecode($this->messages[654]);
				$internal_msg_2 = ($success) ? "" : urldecode($this->messages[655]);
				$link = ($success)
					? "<A HREF=\"".trim($this->configuration_data["classifieds_url"])."?a=2&b=".$this->classified_id."\" class=$css_Link>"
						.urldecode($this->messages[181])."</A>"
					: "<A HREF=\"".$this->configuration_data["classifieds_file_name"]."?a=1\" class=$css_Link>"
						.urldecode($this->messages[861])."</A>";
				$this->update_final_approval($db,0);
				$this->update_billing_approved($db,0);
				if ($success)
					$this->remove_sell_session($db,$this->session_id);
			break;
			default:
				$this->site_error($db);
			break;
		}
		$body = "
			<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>
				<tr class=$css_Section_Title>
					<td>".$section_title."</td>
				</tr>
				<tr class=$css_Page_Title>
					<td>".$page_title."</td>
				</tr>
				<tr class=$css_Page_Description>
					<td>".$page_description_1."</td>
				</tr>
				<tr class=$css_Page_Description>
					<td>".$page_description_2."</td>
				</tr>
				<tr class=$css_Page_Description>
					<td>".$internal_msg_1."</td>
				</tr>
				<tr class=$css_Page_Description>
					<td>".$internal_msg_2."</td>
				</tr>
				<tr class=$css_Success_Failure_Message>
					<td>".$additional_msg."</td>
				</tr>
				<tr class=$css_Success_Failure_Message>
					<td>".$handler_error_response."</td>
				</tr>
				<tr>
					<td>".$link."</td>
				</tr>
			</table>";

		return $body;
	}//end cc_display_process_failure()

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function cc_pre_process($db,$cc)
	{
		$user_data = $this->get_user_data($db,$this->classified_user_id);
		$this->sql_query = "select * from ".$this->cc_choices." where chosen_cc = 1";
		$cc_result = $db->Execute($this->sql_query);
		if (!$cc_result)
		{
			$this->setup_error = $this->messages[453];
			return false;
		}
		elseif ($cc_result->RecordCount() == 1)
		{
			$show_cc_choice = $cc_result->FetchRow();
			$cvv2_insert = "";
			switch ($show_cc_choice["cc_id"])
			{
				case 1:
					//authorize.net
					$info["trans_table_key"] = "authorizenet_transaction_id";
					break;
				case 2:
					//2checkout
					$info["trans_table_key"] = "transaction_id";
					break;
				case 3:
					//bitel
					$info["trans_table_key"] = "bitel_transaction_id";
				break;
				case 4:
					//linkpoint
					$info["trans_table_key"] = "linkpoint_transaction_id";
					break;
				case 5:
					//internetsecure
					$info["trans_table_key"] = "internetsecure_transaction_id";
					break;
				case 6:
					//payflow pro
					$info["trans_table_key"] = "payflow_pro_transaction_id";
					break;
				case 7:
					//paypal pro
					$info["trans_table_key"] = "transaction_id";
					$cvv2_insert = ",cvv2_code";
					break;
				case 8:
					//add new cc payment handler here
				break;
				case 9:
					//manual processing
					$info["trans_table_key"] = "manual_transaction_id";
					$cvv2_insert = ",cvv2_code";
				break;
			}
		}
		if ($this->subscription_renewal)
		{
			require_once './classes/site_class.php';
			$unique_key = substr(md5(uniqid(rand(),1)), 0,strlen($this->renew_subscription_variables["cc_number"]));
			$encrypted_card_num = Site::encrypt($this->renew_subscription_variables["cc_number"], $unique_key);
			$this->page_id = 109;
			$this->classified_id = $this->classified_user_id;
			$cvv2_code = ($show_cc_choice["cc_id"]==7 || $show_cc_choice["cc_id"]==9) ? "\"".$this->renew_subscription_variables["cvv2_code"]."\"," : "";
			$this->price_plan_id = $this->renewStorefrontSubscription ? '-1' : $this->price_plan_id;
			
			if ($show_cc_choice["cc_id"] == 2)
			{
				//2checkout custom
				$this->sql_query = "insert into ".$cc->CC_TRANSACTION_TABLE."
					(classified_id,user_id,card_holder_name,street_address,city,state,country,zip,email,
					tax,total,product_description,renewal_length,subscription_renewal,price_plan_id)
					values
					(".$this->classified_id.",
					".$this->classified_user_id.",
					\"".$user_data->FIRSTNAME." ".$user_data->LASTNAME."\",
					\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
					\"".$user_data->CITY."\",
					\"".$user_data->STATE."\",
					\"".$user_data->COUNTRY."\",
					\"".$user_data->ZIP."\",
					\"".$user_data->EMAIL."\",
					\"".$this->tax."\",
					\"".$this->total."\",
					\"".$user_data->FAX."\",
					\"classified subscription renewal\",
					\"".$this->renew_subscription_variables["subscription_choice"]."\",
					\"1\",
					\"".$this->price_plan_id."\")";				
			}
			else 
			{
				//all other cc payment gateways
				$this->sql_query = "insert into ".$cc->CC_TRANSACTION_TABLE."
					(classified_id,user_id,first_name,last_name,address,city,state,country,zip,email,
					card_num,decryption_key,exp_date".$cvv2_insert.",tax,amount,
					fax,company,description,renewal_length,subscription_renewal,price_plan_id)
					values
					(".$this->classified_id.",
					".$this->classified_user_id.",
					\"".$user_data->FIRSTNAME."\",
					\"".$user_data->LASTNAME."\",
					\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
					\"".$user_data->CITY."\",
					\"".$user_data->STATE."\",
					\"".$user_data->COUNTRY."\",
					\"".$user_data->ZIP."\",
					\"".$user_data->EMAIL."\",
					\"$encrypted_card_num\",
					\"$unique_key\",
					\"".$this->renew_subscription_variables["cc_exp_month"]."/".$this->renew_subscription_variables["cc_exp_year"]."\",
					$cvv2_code
					\"".$this->tax."\",
					\"".$this->total."\",
					\"".$user_data->FAX."\",
					\"".$user_data->COMPANY_NAME."\",
					\"classified subscription renewal\",
					\"".$this->renew_subscription_variables["subscription_choice"]."\",
					\"1\",
					\"".$this->price_plan_id."\")";
			}

			//move decryption to within setting up renewal session in the top of renew_upgrade_sellers_ads.php
			//$info["cc_number"] = Site::decrypt($this->renew_subscription_variables["cc_number"], $this->renew_subscription_variables["decryption_key"]);
			$info["cc_number"] = $this->renew_subscription_variables["cc_number"];
			$info["cc_exp_month"] = $this->renew_subscription_variables["cc_exp_month"];
			$info["cc_exp_year"] = $this->renew_subscription_variables["cc_exp_year"];
			$info["cvv2_code"] = $this->renew_subscription_variables["cvv2_code"];
			$info["ad_type"] = "listing subscription renewal";
		}
		elseif ($this->account_balance)
		{
			$this->page_id = 175;

			require_once './classes/site_class.php';
			$unique_key = substr(md5(uniqid(rand(),1)), 0,strlen($this->account_variables["cc_number"]));
			$encrypted_card_num = Site::encrypt($this->account_variables["cc_number"], $unique_key);
			$cvv2_code = ($show_cc_choice["cc_id"]==7 || $show_cc_choice["cc_id"]==9) ? "\"".$this->account_variables["cvv2_code"]."\"," : "";
			
			if ($show_cc_choice["cc_id"] == 2)
			{			
				$this->sql_query = "insert into ".$cc->CC_TRANSACTION_TABLE."
					(user_id,card_holder_name,street_address,city,state,country,zip,email,
					tax,total,product_description,account_balance)
					values
					(".$this->classified_user_id.",
					\"".$user_data->FIRSTNAME." ".$user_data->LASTNAME."\",
					\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
					\"".$user_data->CITY."\",
					\"".$user_data->STATE."\",
					\"".$user_data->COUNTRY."\",
					\"".$user_data->ZIP."\",
					\"".$user_data->EMAIL."\",
					\"0\",
					\"".$this->account_variables["price"]."\",
					\"balance purchase\",
					\"".$this->account_balance."\")";
			}
			else 
			{
				$this->sql_query = "insert into ".$cc->CC_TRANSACTION_TABLE."
					(user_id,first_name,last_name,address,city,state,country,zip,email,
					card_num,decryption_key,exp_date".$cvv2_insert.",tax,amount,
					fax,company,description,account_balance)
					values
					(".$this->classified_user_id.",
					\"".$user_data->FIRSTNAME."\",
					\"".$user_data->LASTNAME."\",
					\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
					\"".$user_data->CITY."\",
					\"".$user_data->STATE."\",
					\"".$user_data->COUNTRY."\",
					\"".$user_data->ZIP."\",
					\"".$user_data->EMAIL."\",
					\"$encrypted_card_num\",
					\"$unique_key\",
					\"".$this->account_variables["cc_exp_month"]."/".$this->account_variables["cc_exp_year"]."\",
					$cvv2_code
					\"0\",
					\"".$this->account_variables["price"]."\",
					\"".$user_data->FAX."\",
					\"".$user_data->COMPANY_NAME."\",
					\"balance purchase\",
					\"".$this->account_balance."\")";				
			}

			$this->total = $this->account_variables["price"];
			//decryption moved to session setup in renew_subscriptions.php
			//$info["cc_number"] = Site::decrypt($this->account_variables["cc_number"], $this->account_variables["decryption_key"]);
			$info["cc_number"] = $this->account_variables["cc_number"];
			$info["cc_exp_month"] = $this->account_variables["cc_exp_month"];
			$info["cc_exp_year"] = $this->account_variables["cc_exp_year"];
			$info["cvv2_code"] = $this->account_variables["cvv2_code"];
			$info["ad_type"] = "account balance deposit";
		}
		elseif ($this->invoice_id)
		{
			$this->page_id = 180;
			$unique_key = substr(md5(uniqid(rand(),1)), 0,strlen($this->invoice_variables["cc_number"]));
			$encrypted_card_num = Site::encrypt($this->invoice_variables["cc_number"], $unique_key);
			$cvv2_code = ($show_cc_choice["cc_id"]==7 || $show_cc_choice["cc_id"]==9) ? "\"".$this->invoice_variables["cvv2_code"]."\"," : "";
			
			if ($show_cc_choice["cc_id"] == 2)
			{
				$this->sql_query = "insert into ".$cc->CC_TRANSACTION_TABLE."
					(user_id,card_holder_name,street_address,city,state,country,zip,email,
					tax,total,product_description,pay_invoice)
					values
					(".$this->classified_user_id.",
					\"".$user_data->FIRSTNAME." ".$user_data->LASTNAME."\",
					\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
					\"".$user_data->CITY."\",
					\"".$user_data->STATE."\",
					\"".$user_data->COUNTRY."\",
					\"".$user_data->ZIP."\",
					\"".$user_data->EMAIL."\",
					\"0\",
					\"".$this->invoice_total."\",
					\"invoice payment\",
					\"".$this->invoice_id."\")";
			}
			else 
			{
				$this->sql_query = "insert into ".$cc->CC_TRANSACTION_TABLE."
					(user_id,first_name,last_name,address,city,state,country,zip,email,
					card_num,decryption_key,exp_date".$cvv2_insert.",tax,amount,
					fax,company,description,pay_invoice)
					values
					(".$this->classified_user_id.",
					\"".$user_data->FIRSTNAME."\",
					\"".$user_data->LASTNAME."\",
					\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
					\"".$user_data->CITY."\",
					\"".$user_data->STATE."\",
					\"".$user_data->COUNTRY."\",
					\"".$user_data->ZIP."\",
					\"".$user_data->EMAIL."\",
					\"$encrypted_card_num\",
					\"$unique_key\",
					\"".$this->invoice_variables["cc_exp_month"]."/".$this->invoice_variables["cc_exp_year"]."\",
					$cvv2_code
					\"0\",
					\"".$this->invoice_total."\",
					\"".$user_data->FAX."\",
					\"".$user_data->COMPANY_NAME."\",
					\"invoice payment\",
					\"".$this->invoice_id."\")";				
			}

			$this->total = $this->invoice_total;
			//move decryption to session creation in the top of pay_invoice.php file
			//$info["cc_number"] = Site::decrypt($this->invoice_variables["cc_number"], $this->invoice_variables["decryption_key"]);
			$info["cc_number"] = $this->invoice_variables["cc_number"];
			$info["cc_exp_month"] = $this->invoice_variables["cc_exp_month"];
			$info["cc_exp_year"] = $this->invoice_variables["cc_exp_year"];
			$info["cvv2_code"] = $this->invoice_variables["cvv2_code"];
			$info["ad_type"] = "invoice payment";
		}
		elseif ($this->renew_upgrade)
		{
			$this->page_id = 58;
			$unique_key = substr(md5(uniqid(rand(),1)), 0,strlen($this->renew_upgrade_variables["cc_number"]));
			$encrypted_card_num = Site::encrypt($this->renew_upgrade_variables["cc_number"], $unique_key);
			if ($this->renew_upgrade == 1)
			{
				$ad_description = "classified ad renewal";
				$info["ad_type"] = "classified ad renewal";
				$renewing = 1;
			}
			else
			{
				$ad_description = "classified ad upgrade";
				$info["ad_type"] = "classified ad upgrade";
				$renewing = 0;
			}
			$bolding = ($this->renew_upgrade_variables["bolding"] || $this->renew_upgrade_variables["bolding_upgrade"]) ? 1 : 0;
			$better_placement = ($this->renew_upgrade_variables["better_placement"] || $this->renew_upgrade_variables["better_placement_upgrade"]) ? 1 : 0;
			$featured_ad = ($this->renew_upgrade_variables["featured_ad"] || $this->renew_upgrade_variables["featured_ad_upgrade"]) ? 1 : 0;
			$featured_ad_2 = ($this->renew_upgrade_variables["featured_ad_2"] || $this->renew_upgrade_variables["featured_ad_2_upgrade"]) ? 1 : 0;
			$featured_ad_3 = ($this->renew_upgrade_variables["featured_ad_3"] || $this->renew_upgrade_variables["featured_ad_3_upgrade"]) ? 1 : 0;
			$featured_ad_4 = ($this->renew_upgrade_variables["featured_ad_4"] || $this->renew_upgrade_variables["featured_ad_4_upgrade"]) ? 1 : 0;
			$featured_ad_5 = ($this->renew_upgrade_variables["featured_ad_5"] || $this->renew_upgrade_variables["featured_ad_5_upgrade"]) ? 1 : 0;
			if ($this->renew_upgrade_variables["attention_getter"] || $this->renew_upgrade_variables["attention_getter_upgrade"])
			{
				$attention_getter = 1;
				if ($this->renew_upgrade_variables["attention_getter_choice"])
					$attention_getter_choice = $this->renew_upgrade_variables["attention_getter_choice"];
				else
					$attention_getter_choice = $this->renew_upgrade_variables["attention_getter_choice_upgrade"];
			}
			else
			{
				$attention_getter = 0;
				$attention_getter_choice = 0;
			}
			$cvv2_code = ($show_cc_choice["cc_id"]==7 || $show_cc_choice["cc_id"]==9) ? "\"".$this->renew_upgrade_variables["cvv2_code"]."\"," : "";
			
			if ($show_cc_choice["cc_id"] == 2)
			{
				$this->sql_query = "insert into ".$cc->CC_TRANSACTION_TABLE."
					(classified_id,user_id,card_holder_name,street_address,city,state,country,zip,email,
					tax,total,product_description,renew,bolding,better_placement,featured_ad,featured_ad_2,
					featured_ad_3,featured_ad_4,featured_ad_5,
					attention_getter,attention_getter_choice,renewal_length,
					use_credit_for_renewal,price_plan_id)
					values
					(".$this->classified_id.",
					".$this->classified_user_id.",
					\"".$user_data->FIRSTNAME." ".$user_data->LASTNAME."\",
					\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
					\"".$user_data->CITY."\",
					\"".$user_data->STATE."\",
					\"".$user_data->COUNTRY."\",
					\"".$user_data->ZIP."\",
					\"".$user_data->EMAIL."\",
					\"".$this->tax."\",
					\"".$this->total."\",
					\"".$ad_description."\",
					\"".$renewing."\",
					\"".$bolding."\",
					\"".$better_placement."\",
					\"".$featured_ad."\",
					\"".$featured_ad_2."\",
					\"".$featured_ad_3."\",
					\"".$featured_ad_4."\",
					\"".$featured_ad_5."\",
					\"".$attention_getter."\",
					\"".$attention_getter_choice."\",
					\"".$this->renew_upgrade_variables["renewal_length"]."\",
					\"".$this->renew_upgrade_variables["use_credit_for_renewal"]."\",
					\"".$this->price_plan_id."\")";
			}
			else 
			{
				$this->sql_query = "insert into ".$cc->CC_TRANSACTION_TABLE."
					(classified_id,user_id,first_name,last_name,address,city,state,country,zip,email,
					card_num,decryption_key,exp_date".$cvv2_insert.",tax,amount,fax,company,description,
					renew,bolding,better_placement,featured_ad,featured_ad_2,featured_ad_3,featured_ad_4,featured_ad_5,
					attention_getter,attention_getter_choice,renewal_length,
					use_credit_for_renewal,price_plan_id)
					values
					(".$this->classified_id.",
					".$this->classified_user_id.",
					\"".$user_data->FIRSTNAME."\",
					\"".$user_data->LASTNAME."\",
					\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
					\"".$user_data->CITY."\",
					\"".$user_data->STATE."\",
					\"".$user_data->COUNTRY."\",
					\"".$user_data->ZIP."\",
					\"".$user_data->EMAIL."\",
					\"$encrypted_card_num\",
					\"$unique_key\",
					\"".$this->renew_upgrade_variables["cc_exp_month"]."/".$this->renew_upgrade_variables["cc_exp_year"]."\",
					$cvv2_code
					\"".$this->tax."\",
					\"".$this->total."\",
					\"".$user_data->FAX."\",
					\"".$user_data->COMPANY_NAME."\",
					\"".$ad_description."\",
					\"".$renewing."\",
					\"".$bolding."\",
					\"".$better_placement."\",
					\"".$featured_ad."\",
					\"".$featured_ad_2."\",
					\"".$featured_ad_3."\",
					\"".$featured_ad_4."\",
					\"".$featured_ad_5."\",
					\"".$attention_getter."\",
					\"".$attention_getter_choice."\",
					\"".$this->renew_upgrade_variables["renewal_length"]."\",
					\"".$this->renew_upgrade_variables["use_credit_for_renewal"]."\",
					\"".$this->price_plan_id."\")";				
			}

			$info["cc_number"] = Site::decrypt($this->renew_upgrade_variables["cc_number"], $this->renew_upgrade_variables["decryption_key"]);
			$info["cc_exp_month"] = $this->renew_upgrade_variables["cc_exp_month"];
			$info["cc_exp_year"] = $this->renew_upgrade_variables["cc_exp_year"];
			$info["cvv2_code"] = $this->renew_upgrade_variables["cvv2_code"];
			$info["ad_type"] = "listing upgrade/renewal";
		}
		else
		{
			$this->page_id = 14;
			$unique_key = substr(md5(uniqid(rand(),1)), 0,strlen($this->classified_variables["cc_number"]));
			$encrypted_card_num = Site::encrypt($this->classified_variables["cc_number"], $unique_key);
			$cvv2_code = ($show_cc_choice["cc_id"]==7 || $show_cc_choice["cc_id"]==9) ? "\"".$this->classified_variables["cvv2_code"]."\"," : "";
			if ($show_cc_choice["cc_id"] == 2)
			{
				$this->sql_query = "insert into ".$cc->CC_TRANSACTION_TABLE."
					(classified_id,user_id,card_holder_name,street_address,city,state,country,zip,email,
					tax,total,product_description,ad_placement,price_plan_id)
					values
					(".$this->classified_id.",
					".$this->classified_user_id.",
					\"".$user_data->FIRSTNAME." ".$user_data->LASTNAME."\",
					\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
					\"".$user_data->CITY."\",
					\"".$user_data->STATE."\",
					\"".$user_data->COUNTRY."\",
					\"".$user_data->ZIP."\",
					\"".$user_data->EMAIL."\",
					\"".$this->tax."\",
					\"".$this->total."\",
					\"classified ad placement\",
					\"1\",
					\"".$this->users_price_plan."\")";
			}
			else 
			{
				$this->sql_query = "insert into ".$cc->CC_TRANSACTION_TABLE."
					(classified_id,user_id,first_name,last_name,address,city,state,country,zip,email,
					card_num,decryption_key,exp_date".$cvv2_insert.",tax,amount,
					fax,company,description,ad_placement,price_plan_id)
					values
					(".$this->classified_id.",
					".$this->classified_user_id.",
					\"".$user_data->FIRSTNAME."\",
					\"".$user_data->LASTNAME."\",
					\"".$user_data->ADDRESS." ".$user_data->ADDRESS_2."\",
					\"".$user_data->CITY."\",
					\"".$user_data->STATE."\",
					\"".$user_data->COUNTRY."\",
					\"".$user_data->ZIP."\",
					\"".$user_data->EMAIL."\",
					\"$encrypted_card_num\",
					\"$unique_key\",
					\"".$this->classified_variables["cc_exp_month"]."/".$this->classified_variables["cc_exp_year"]."\",
					$cvv2_code
					\"".$this->tax."\",
					\"".$this->total."\",
					\"".$user_data->FAX."\",
					\"".$user_data->COMPANY_NAME."\",
					\"classified ad placement\",
					\"1\",
					\"".$this->users_price_plan."\")";				
			}

			$info["cc_number"] = Site::decrypt($this->classified_variables["cc_number"], $this->classified_variables["decryption_key"]);
			$info["cc_exp_month"] = $this->classified_variables["cc_exp_month"];
			$info["cc_exp_year"] = $this->classified_variables["cc_exp_year"];
			$info["cvv2_code"] = $this->classified_variables["cvv2_code"];
			$info["ad_type"] = "listing placement";
		}
		$cc_trans_result = $db->Execute($this->sql_query);
		if (strlen($this->debug_email) > 0)
		{
			@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\nunencrypted cc: ".$info["cc_number"]."\n\n".date("l dS of F Y h:i:s A"));
		}
		if (!$cc_trans_result)
		{
			if (strlen($this->debug_email) > 0)
			{
				$email_msg = $this->sql_query;
				@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
			}
			//DISPLAY ERROR PAGE
			$this->body .= Site::cc_success_failure($db,0,$additional_msg);
			$this->display_page($db);
			$db->Close();
			exit;
		}
		if ($show_cc_choice["cc_id"]==9) return '';
		$info["trans_id"] = $db->Insert_ID();
		$this->get_text($db);
		$this->sql_query = "select * from ".$cc->CC_TABLE;
		$cc_table_result = $db->Execute($this->sql_query);
		if (strlen($this->debug_email) > 0)
		{
			@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
		}
		if (!$cc_table_result)
		{
			if (strlen($this->debug_email) > 0)
			{
				$email_msg = $this->sql_query;
				@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
			}
			//DISPLAY ERROR PAGE
			$this->body .= Site::cc_success_failure($db,0,$additional_msg);
			$this->display_page($db);
			$db->Close();
			exit;
		}
		//$show_cc = $cc_table_result->FetchRow();
		if ((!$this->subscription_renewal) && (!$this->invoice_id) && (!$this->account_balance))
		{
			$this->sql_query = "update ".$this->classifieds_table." set cc_transaction_type = ".$cc->CC_ID." where id = ".$this->classified_id;
			$update_cc_result = $db->Execute($this->sql_query);
			if (strlen($this->debug_email) > 0)
			{
				@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
			}
			if (!$update_cc_result)
			{
				if (strlen($this->debug_email) > 0)
				{
					$email_msg = $this->sql_query;
					@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
				}
				//DISPLAY ERROR PAGE
				$this->body .= Site::cc_success_failure($db,0,$additional_msg);
				$this->display_page($db);
				$db->Close();
				exit;
			}
		}

		$this->sql_query = "SELECT abbreviation FROM $this->countries_table WHERE
			name = \"".$user_data->COUNTRY."\" OR
			abbreviation = \"".$user_data->COUNTRY."\"";

		$country_result = $db->Execute($this->sql_query);
		$this->sql_query = "select * from ".$this->site_configuration_table;
		$config_result = $db->Execute($this->sql_query);
		if (strlen($this->debug_email) > 0)
		{
			@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
		}
		if (!$config_result)
		{
			if (strlen($this->debug_email) > 0)
			{
				$email_msg = $this->sql_query;
				@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
			}
			//DISPLAY ERROR PAGE
			$this->body .= Site::cc_success_failure($db,0,$additional_msg);
			$this->display_page($db);
			$db->Close();
			exit;
		}
		elseif (!$country_result)
		{
			if (strlen($this->debug_email) > 0)
			{
				$email_msg = $this->sql_query;
				@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
			}
			//DISPLAY ERROR PAGE
			$this->body .= Site::cc_success_failure($db,0,$additional_msg);
			$this->display_page($db);
			$db->Close();
			exit;
		}
		else
		{
			$info["ip"] = Site::getip();
			$info["user_country"] = $country_result->FetchRow();
			$info["show"] = $config_result->FetchRow();

			if (strlen($this->debug_email) > 0)
			{
				$before = "\nCC # BEFORE DECRYPTION - ".$this->classified_variables["cc_number"];
				$before .= "\nDECRYPTION KEY - ".$this->classified_variables["decryption_key"];
				$before .= "\nCC # - ".$info["cc_number"];
				$before .= "\nEXP DATE - ".$info["cc_exp_month"]." / ".$info["cc_exp_year"];
				$before .= "\nCVV2 NUM - ".$info["cvv2_code"];
				$before .= "\nTOTAL - ".$this->total;
				$before .= "\nFIRST NAME - ".$user_data->FIRSTNAME;
				$before .= "\nLAST NAME - ".$user_data->LASTNAME;
				$before .= "\nADDRESS - ".$user_data->ADDRESS;
				$before .= "\nCITY - ".$user_data->CITY;
				$before .= "\nSTATE - ".$user_data->STATE;
				$before .= "\nZIP - ".$user_data->ZIP;
				$before .= "\nCOUNTRY - ".$info["user_country"]["abbreviation"];
				$before .= "\nIP  -  ".$info["ip"];
				@mail($this->debug_email,"site_class.php LINE ".__LINE__,$before."\n\n".date("l dS of F Y h:i:s A"));
			}
		}
		return $info;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function cc_post_process($db,$cc,$info,$handler_error_response="")
	{
		if ($handler_error_response == "")
		{
			$user_data = $this->get_user_data($db,$this->classified_user_id);
			//check to see if transaction exists
			$this->sql_query = "select * from ".$cc->CC_TRANSACTION_TABLE." where ".$info["trans_table_key"]." = ".$info["trans_id"];
			$trans_result = $db->Execute($this->sql_query);
			if (strlen($this->debug_email) > 0)
			{
				@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
			}
			if (!$trans_result)
			{
				if (strlen($this->debug_email) > 0)
				{
					$email_msg = $this->sql_query;
					@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
				}
				//DISPLAY ERROR PAGE
				$this->body .= Site::cc_success_failure($db,0,$additional_msg);
				$this->display_page($db);
				$db->Close();
				exit;
			}
			elseif ($trans_result->RecordCount() == 1)
			{
				$show_cc_trans = $trans_result->FetchRow();
				if ($show_cc_trans["pay_invoice"] > 0)
				{
					$this->sql_query = "update ".$this->invoices_table." set date_paid = ".$this->shifted_time($db)." where invoice_id = ".$show_cc_trans["pay_invoice"];
					$paid_result = $db->Execute($this->sql_query);
					if (strlen($this->debug_email) > 0)
					{
						@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
					}
					if (!$paid_result)
					{
						if (strlen($this->debug_email) > 0)
						{
							$email_msg = $this->sql_query;
							@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
						}
						//DISPLAY ERROR PAGE
						$this->body .= Site::cc_success_failure($db,0,$additional_msg);
						$this->display_page($db);
						$db->Close();
						exit;
					}
					else
					{
						//DISPLAY SUCCESS PAGE
						$this->body .= Site::cc_success_failure($db,1,$additional_msg);
						$this->display_page($db);
						$db->Close();
						exit;
					}
				}
				elseif ($show_cc_trans["account_balance"] > 0)
				{
					//update approval for this balance transaction
					$this->update_balance_approval($db,$show_cc_trans["account_balance"],$info["trans_id"]);

					//add to the users balance
					//user data still exists
					$new_balance = $user_data->ACCOUNT_BALANCE + $this->account_variables["price"];
					$this->sql_query = "update ".$this->userdata_table." set
						account_balance = ".$new_balance."
						where id = ".$this->classified_user_id;
					$update_balance_results = $db->Execute($this->sql_query);
					if (strlen($this->debug_email) > 0)
					{
						@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
					}
					if (!$update_balance_results)
					{
						if (strlen($this->debug_email) > 0)
						{
							$email_msg = $this->sql_query;
							@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
						}
						//DISPLAY ERROR PAGE
						$this->body .= Site::cc_success_failure($db,0,$additional_msg);
						$this->display_page($db);
						$db->Close();
						exit;
					}
					else
					{
						//DISPLAY SUCCESS PAGE
						$additional_msg = $this->configuration_data['precurrency']." ".sprintf("%01.2f",$new_balance).
							" ".$this->configuration_data['postcurrency'];
						$this->body .= Site::cc_success_failure($db,1,$additional_msg);
						$this->display_page($db);
						$db->Close();
						exit;
					}
				}
				//STOREFRONT CODE
				elseif($show_cc_trans["subscription_renewal"] == 1 && $show_cc_trans["price_plan_id"] == -1)
				{
					$this->sql_query = "select * from geodesic_storefront_subscriptions_choices
						where period_id = ".$show_cc_trans["renewal_length"]."";
					$choices_result = $db->Execute($this->sql_query);
					if (!$choices_result)
					{
						if (strlen($this->debug_email) > 0)
						{
							$email_msg = $this->sql_query;
							@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
						}
						//DISPLAY ERROR PAGE
						$this->body .= Site::cc_success_failure($db,0,$additional_msg);
						$this->display_page($db);
						$db->Close();
						exit;
					}
					elseif ($choices_result->RecordCount() == 1 )
					{
						$show_choice = $choices_result->FetchNextObject();
						if ($show_choice->VALUE !=0)
						{
							//check to see if currently subscribed
							$this->sql_query = "select * from geodesic_storefront_subscriptions where user_id = ".$show_cc_trans["user_id"];
							$check_subscriptions_results = $db->Execute($this->sql_query);
							if (!$check_subscriptions_results)
							{
								if (strlen($this->debug_email) > 0)
								{
									$email_msg = $this->sql_query;
									@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
								}
								//DISPLAY ERROR PAGE
								$this->body .= Site::cc_success_failure($db,0,$additional_msg);
								$this->display_page($db);
								$db->Close();
								exit;
							}
							elseif ($check_subscriptions_results->RecordCount() > 0)
							{
								//extend subscription period
								$show_subscription = $check_subscriptions_results->FetchNextObject();
								if ($show_subscription->EXPIRATION > $this->shifted_time($db))
									$new_expire = ($show_subscription->EXPIRATION + ($show_choice->VALUE * 86400));
								else
									$new_expire = ($this->shifted_time($db) + ($show_choice->VALUE * 86400));
								$this->sql_query = "update geodesic_storefront_subscriptions
									set expiration = ".$new_expire."
									where subscription_id = ".$show_subscription->SUBSCRIPTION_ID;
								$update_subscriptions_results = $db->Execute($this->sql_query);
								if (!$update_subscriptions_results)
								{
									if (strlen($this->debug_email) > 0)
									{
										$email_msg = $this->sql_query;
										@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
									}
									//DISPLAY ERROR PAGE
									$this->body .= Site::cc_success_failure($db,0,$additional_msg);
									$this->display_page($db);
									$db->Close();
									exit;
								}

							}
							else
							{
								//enter new subscription period
								$new_expire = ($this->shifted_time($db) + ($show_choice->VALUE * 86400));
								$this->sql_query = "insert into geodesic_storefront_subscriptions
									(user_id,expiration)
									values
									(".$show_cc_trans["user_id"].",".$new_expire.")";
								$insert_subscriptions_results = $db->Execute($this->sql_query);
								if (!$insert_subscriptions_results)
								{
									if (strlen($this->debug_email) > 0)
									{
										$email_msg = $this->sql_query;
										@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
									}
									//DISPLAY ERROR PAGE
									$this->body .= Site::cc_success_failure($db,0,$additional_msg);
									$this->display_page($db);
									$db->Close();
									exit;
								}
							}
						}
					}
				}
				//STOREFRONT CODE
				elseif ($show_cc_trans["subscription_renewal"] == 1)
				{
					//check to see if currently subscribed
					$this->sql_query = "select * from ".$this->user_subscriptions_table." where user_id = ".$this->classified_user_id;
					$check_subscriptions_results = $db->Execute($this->sql_query);
					if (strlen($this->debug_email) > 0)
					{
						@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
					}
					$price_plan_id_result = $db->Execute($this->sql_query);
					if (!$check_subscriptions_results)
					{
						if (strlen($this->debug_email) > 0)
						{
							$email_msg = $this->sql_query;
							@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
						}
						//DISPLAY ERROR PAGE
						$this->body .= Site::cc_success_failure($db,0,$additional_msg);
						$this->display_page($db);
						$db->Close();
						exit;
					}
					elseif ($check_subscriptions_results->RecordCount() > 0)
					{
						//extend subscription period
						$show_subscription = $check_subscriptions_results->FetchRow();
						//check to see if price plan attached to subscription
						if ($show_subscription["price_plan_id"])
						{
							$this->sql_query = "select * from ".$this->subscription_choices." where price_plan_id = ".$show_subscription["price_plan_id"]."
								and period_id = ".$show_cc_trans["renewal_length"]." order by value asc";
							if (strlen($this->debug_email) > 0)
							{
								@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
							}
						}
						else
						{
							if ($show_cc_trans["price_plan_id"])
							{
								$this->sql_query = "select * from ".$this->subscription_choices." where price_plan_id = ".$show_cc_trans["price_plan_id"]."
									and period_id = ".$show_cc_trans["renewal_length"]." order by value asc";
								if (strlen($this->debug_email) > 0)
								{
									@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
								}
							}
							else
							{
								$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->transaction_results["x_cust_id"];
								$price_plan_id_result = $db->Execute($this->sql_query);
								if ($price_plan_id_result->RecordCount() == 1)
								{
									$show_price_plan = $price_plan_id_result->FetchRow();
									$this->sql_query = "select * from ".$this->subscription_choices." where price_plan_id = ".$show_price_plan["price_plan_id"]."
										and period_id = ".$show_cc_trans["renewal_length"]." order by value asc";
								}
								else
								{
									//cannot get default price plan id from user id
									if (strlen($this->debug_email) > 0)
									{
										$email_msg = $this->sql_query;
										@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
									}
									//DISPLAY ERROR PAGE
									$this->body .= Site::cc_success_failure($db,0,$additional_msg);
									$this->display_page($db);
									$db->Close();
									exit;
								}
							}
						}
						$choices_result = $db->Execute($this->sql_query);
						if (strlen($this->debug_email) > 0)
						{
							@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
						}
						if (!$choices_result)
						{
							if (strlen($this->debug_email) > 0)
							{
								$email_msg = $this->sql_query;
								@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
							}
							//DISPLAY ERROR PAGE
							$this->body .= Site::cc_success_failure($db,0,$additional_msg);
							$this->display_page($db);
							$db->Close();
							exit;
						}
						elseif ($choices_result->RecordCount() == 1 )
						{
							$show_choice = $choices_result->FetchRow();
						}
						else
						{
							if (strlen($this->debug_email) > 0)
							{
								$email_msg = $this->sql_query;
								@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
							}
							//DISPLAY ERROR PAGE
							$this->body .= Site::cc_success_failure($db,0,$additional_msg);
							$this->display_page($db);
							$db->Close();
							exit;
						}
						if ($show_choice["value"] !=0)
						{
							if ($show_subscription["subscription_expire"] > $this->shifted_time($db))
								$new_expire = ($show_subscription["subscription_expire"] + ($show_choice["value"] * 86400));
							else
								$new_expire = ($this->shifted_time($db) + ($show_choice["value"] * 86400));
							$this->sql_query = "update ".$this->user_subscriptions_table."
								set subscription_expire = ".$new_expire."
								where subscription_id = ".$show_subscription["subscription_id"];
							if (strlen($this->debug_email) > 0)
							{
								@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
							}
							$update_subscriptions_results = $db->Execute($this->sql_query);
							if (!$update_subscriptions_results)
							{
								if (strlen($this->debug_email) > 0)
								{
									$email_msg = $this->sql_query;
									@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
								}
								//DISPLAY ERROR PAGE
								$this->body .= Site::cc_success_failure($db,0,$additional_msg);
								$this->display_page($db);
								$db->Close();
								exit;
							}
							//DISPLAY SUCCESS PAGE
							$this->body .= Site::cc_success_failure($db,1,$additional_msg);
							$this->display_page($db);
							$db->Close();
							exit;
						}
						else
						{
							//no value to extend the subscription by
							if (strlen($this->debug_email) > 0)
							{
								$email_msg = $this->sql_query;
								@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
							}
							//DISPLAY ERROR PAGE
							$this->body .= Site::cc_success_failure($db,0,$additional_msg);
							$this->display_page($db);
							$db->Close();
							exit;
						}
					}
				}
				elseif ($show_cc_trans["ad_placement"] == 1)
				{
					$this->page_id = 14;
					$this->get_text($db);
					//approved
					//turn on classified ad
					//send to success page
					//send a success message
					$this->sql_query = "select * from ".$this->classifieds_table."
						where id = ".$this->classified_id;
					if (strlen($this->debug_email) > 0)
					{
						@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
					}
					$duration_result = $db->Execute($this->sql_query);
					if (!$duration_result)
					{
						if (strlen($this->debug_email) > 0)
						{
							$email_msg = $this->sql_query;
							@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
						}
						//DISPLAY ERROR PAGE
						$this->body .= Site::cc_success_failure($db,0,$additional_msg);
						$this->display_page($db);
						$db->Close();
						exit;
					}
					$show_duration = $duration_result->FetchRow();
					$length_of_ad = ($show_duration["duration"] * 86400);
					//this is a new ad
					//when ad ends
					$current_time = $this->shifted_time($db);
					if  ($this->configuration_data['admin_approves_all_ads'])
					{
						$this->sql_query = "update ".$this->classifieds_table." set
							date = ".$current_time.",
							ends = ".($current_time + $length_of_ad)."
							where id = ".$this->classified_id;
					}
					else
					{
						$this->sql_query = "update ".$this->classifieds_table." set
							live = 1,
							date = ".$current_time.",
							ends = ".($current_time + $length_of_ad)."
							where id = ".$this->classified_id;
					if (strlen($this->debug_email) > 0)
					{
						@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
					}
					// IDev Affiliate payment
					if($this->configuration_data['idevaffiliate'])
					{
						$idev_geoce_1 = $this->total;
						$idev_geoce_2 = "ad-".$this->classified_id;
						include($this->configuration_data['idev_path'].'sale.php');

						include("config.php");
						include("classes/adodb.inc.php");

						$db = &ADONewConnection('mysql');

						if (!$db->Connect($db_host, $db_username, $db_password, $database))
						{
							echo "Could not reconnect to database";
							exit;
						}
					}
				}
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					if (strlen($this->debug_email) > 0)
					{
						$email_msg = $this->sql_query;
						@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
					}
					//DISPLAY ERROR PAGE
					$this->body .= Site::cc_success_failure($db,0,$additional_msg);
					$this->display_page($db);
					$db->Close();
					exit;
				}
					$this->update_category_count($db,$show_duration["category"]);
					$this->check_subscriptions_and_credits($db);
					//DISPLAY SUCCESS PAGE
					$this->body .= Site::cc_success_failure($db,1,$additional_msg);
					$this->display_page($db);
					if ($this->configuration_data['send_successful_placement_email'])
						$this->sell_success_email($db,$this->classified_id);
					$db->Close();
					exit;
				}
				else
				{
					$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$this->classified_id;
					if (strlen($this->debug_email) > 0)
					{
						@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
					}
					$classified_result = $db->Execute($this->sql_query);
					if (!$classified_result)
					{
						if (strlen($this->debug_email) > 0)
						{
							$email_msg = $this->sql_query;
							@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
						}
						//DISPLAY ERROR PAGE
						$this->body .= Site::cc_success_failure($db,0,$additional_msg);
						$this->display_page($db);
						$db->Close();
						exit;
					}
					$show_classified = $classified_result->FetchRow();

					if (($this->renew_upgrade == 1) && ($this->renew_upgrade_variables["renewal_length"] > 0))
					{
						if ($show_classified["ends"] > $this->shifted_time($db))
							$new_expire = ($show_classified["ends"] + ($this->renew_upgrade_variables["renewal_length"] * 86400));
						else
							$new_expire = ($this->shifted_time($db) + ($this->renew_upgrade_variables["renewal_length"] * 86400));
						if  ($this->configuration_data['admin_approves_all_ads'])
						{
							$this->sql_query = "update geodesic_classifieds set
								date = \"".$this->shifted_time($db)."\",
								ends = \"".$new_expire."\",
								renewal_payment_expected = 0,
								better_placement = \"0\",
								featured_ad = \"0\",
								bolding = \"0\",
								attention_getter = \"0\",
								featured_ad_2 = \"0\",
								featured_ad_3 = \"0\",
								featured_ad_4 = \"0\",
								featured_ad_5 = \"0\"
								where id = ".$this->classified_id;
						}
						else
						{
							$this->sql_query = "update geodesic_classifieds set
								ends = \"".$new_expire."\",
								date = \"".$this->shifted_time($db)."\",
								renewal_payment_expected = 0,
								better_placement = \"0\",
								featured_ad = \"0\",
								bolding = \"0\",
								attention_getter = \"0\",
								featured_ad_2 = \"0\",
								featured_ad_3 = \"0\",
								featured_ad_4 = \"0\",
								featured_ad_5 = \"0\",
								live = 1
								where id = ".$this->classified_id;
						}
						if (strlen($this->debug_email) > 0)
						{
							@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
						}
						$renew_result = $db->Execute($this->sql_query);
						if (!$renew_result)
						{
							if (strlen($this->debug_email) > 0)
							{
								$email_msg = $this->sql_query;
								@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
							}
							//DISPLAY ERROR PAGE
							$this->body .= Site::cc_success_failure($db,0,$additional_msg);
							$this->display_page($db);
							$db->Close();
							exit;
						}
						if ($show_cc_trans["use_credit_for_renewal"])
						{
							$this->sql_query = "select * from geodesic_classifieds_user_credits where user_id = ".$show_cc_trans["user_id"]." order by credits_expire asc limit 1";
							if (strlen($this->debug_email) > 0)
							{
								@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
							}
							$credits_results = $db->Execute($this->sql_query);
							if (!$credits_results)
							{
								if (strlen($this->debug_email) > 0)
								{
									$email_msg = $this->sql_query;
									@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
								}
								//DISPLAY ERROR PAGE
								$this->body .= Site::cc_success_failure($db,0,$additional_msg);
								$this->display_page($db);
								$db->Close();
								exit;
							}
							elseif ($credits_results->RecordCount() == 1)
							{
								//remove one of these credits
								$show_credits = $credits_results->FetchRow();
								if ($show_credits["credit_count"] == 1)
								{
									//delete from the table
									$this->sql_query = "delete from geodesic_classifieds_user_credits where
										credits_id = ".$show_credits["credits_id"]."
										and user_id = ".$show_cc_trans["user_id"];
									if (strlen($this->debug_email) > 0)
									{
										@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
									}
									$remove_credits_results = $db->Execute($this->sql_query);
									if (!$remove_credits_results)
									{
										if (strlen($this->debug_email) > 0)
										{
											$email_msg = $this->sql_query;
											@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
										}
										//DISPLAY ERROR PAGE
										$this->body .= Site::cc_success_failure($db,0,$additional_msg);
										$this->display_page($db);
										$db->Close();
										exit;
									}
								}
								else
								{
									//remove one from the credit count
									$this->sql_query = "update geodesic_classifieds_user_credits set
										credit_count = ".($show_credits["credit_count"] - 1)."
										where credits_id = ".$show_credits["credits_id"]."
										and user_id = ".$show_cc_trans["user_id"];
									if (strlen($this->debug_email) > 0)
									{
										@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
									}
									$remove_credit = $db->Execute($this->sql_query);
									if (!$remove_credit)
									{
										if (strlen($this->debug_email) > 0)
										{
											$email_msg = $this->sql_query;
											@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
										}
										//DISPLAY ERROR PAGE
										$this->body .= Site::cc_success_failure($db,0,$additional_msg);
										$this->display_page($db);
										$db->Close();
										exit;
									}
								}
							}
						}
						else
						{
							// If not using credits to pay for renewal then send it to IDev
							// IDev Affiliate payment
							if($this->configuration_data['idevaffiliate'] && $this->configuration_data['idev_renewal'])
							{
								$idev_geoce_1 = $response->Amount->_value;
								$idev_geoce_2 = "ad-renew-".$show_duration["id"];
								include($this->configuration_data['idev_path'].'sale.php');

								include("config.php");
								include("classes/adodb.inc.php");

								$db = &ADONewConnection('mysql');

								if (!$db->Connect($db_host, $db_username, $db_password, $database))
								{
									echo "Could not reconnect to database";
									exit;
								}
							}
						}
					}
					else
					{
						// Upgrade auction
						// IDev Affiliate payment
						if($this->configuration_data['idevaffiliate'] && $this->configuration_data['idev_upgrade'])
						{
							$idev_geoce_1 = $this->total;
							$idev_geoce_2 = "ad-upgrade-".$show_duration["id"];
							include($this->configuration_data['idev_path'].'sale.php');

							include("config.php");
							include("classes/adodb.inc.php");

							$db = &ADONewConnection('mysql');

							if (!$db->Connect($db_host, $db_username, $db_password, $database))
							{
								echo "Could not reconnect to database";
								exit;
							}
						}
					}
					if ($show_cc_trans["bolding"] == 1)
					{
						$this->sql_query = "update geodesic_classifieds set bolding = \"1\" where id = ".$this->classified_id;
						if (strlen($this->debug_email) > 0)
						{
							@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
						}
						$bolding_result = $db->Execute($this->sql_query);
						if (!$bolding_result)
						{
							if (strlen($this->debug_email) > 0)
							{
								$email_msg = $this->sql_query;
								@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
							}
							//DISPLAY ERROR PAGE
							$this->body .= Site::cc_success_failure($db,0,$additional_msg);
							$this->display_page($db);
							$db->Close();
							exit;
						}
						$additional_msg .= "<tr class=success_failure_message><td>".urldecode($this->messages[853])."</td>\n</tr>\n";
					}

					if ($show_cc_trans["better_placement"] == 1)
					{
						$this->sql_query = "update geodesic_classifieds set better_placement = \"1\" where id = ".$this->classified_id;
						if (strlen($this->debug_email) > 0)
						{
							@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
						}
						$better_placement_result = $db->Execute($this->sql_query);
						if (!$better_placement_result)
						{
							if (strlen($this->debug_email) > 0)
							{
								$email_msg = $this->sql_query;
								@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
							}
							//DISPLAY ERROR PAGE
							$this->body .= Site::cc_success_failure($db,0,$additional_msg);
							$this->display_page($db);
							$db->Close();
							exit;
						}
						$additional_msg .= "<tr class=success_failure_message><td>".urldecode($this->messages[854])."</td>\n</tr>\n";
					}

					if ($show_cc_trans["featured_ad"] == 1)
					{
						$this->sql_query = "update geodesic_classifieds set	featured_ad = \"1\"	where id = ".$this->classified_id;
						if (strlen($this->debug_email) > 0)
						{
							@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
						}
						$featured_ad_result = $db->Execute($this->sql_query);
						if (!$featured_ad_result)
						{
							if (strlen($this->debug_email) > 0)
							{
								$email_msg = $this->sql_query;
								@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
							}
							//DISPLAY ERROR PAGE
							$this->body .= Site::cc_success_failure($db,0,$additional_msg);
							$this->display_page($db);
							$db->Close();
							exit;
						}
						$additional_msg .= "<tr class=success_failure_message><td>".urldecode($this->messages[855])."</td>\n</tr>\n";
					}

					if ($show_cc_trans["featured_ad_2"] == 1)
					{
						$this->sql_query = "update geodesic_classifieds set	featured_ad_2 = \"1\" where id = ".$this->classified_id;
						if (strlen($this->debug_email) > 0)
						{
							@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
						}
						$featured_ad_result = $db->Execute($this->sql_query);
						if (!$featured_ad_result)
						{
							if (strlen($this->debug_email) > 0)
							{
								$email_msg = $this->sql_query;
								@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
							}
							//DISPLAY ERROR PAGE
							$this->body .= Site::cc_success_failure($db,0,$additional_msg);
							$this->display_page($db);
							$db->Close();
							exit;
						}
						$additional_msg .= "<tr class=success_failure_message><td>".urldecode($this->messages[2284])."</td>\n</tr>\n";
					}

					if ($show_cc_trans["featured_ad_3"] == 1)
					{
						$this->sql_query = "update geodesic_classifieds set	featured_ad_3 = \"1\" where id = ".$this->classified_id;
						if (strlen($this->debug_email) > 0)
						{
							@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
						}
						$featured_ad_result = $db->Execute($this->sql_query);
						if (!$featured_ad_result)
						{
							if (strlen($this->debug_email) > 0)
							{
								$email_msg = $this->sql_query;
								@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
							}
							//DISPLAY ERROR PAGE
							$this->body .= Site::cc_success_failure($db,0,$additional_msg);
							$this->display_page($db);
							$db->Close();
							exit;
						}
						$additional_msg .= "<tr class=success_failure_message><td>".urldecode($this->messages[2285])."</td>\n</tr>\n";
					}

					if ($show_cc_trans["featured_ad_4"] == 1)
					{
						$this->sql_query = "update geodesic_classifieds set	featured_ad_4 = \"1\" where id = ".$this->classified_id;
						if (strlen($this->debug_email) > 0)
						{
							@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
						}
						$featured_ad_result = $db->Execute($this->sql_query);
						if (!$featured_ad_result)
						{
							if (strlen($this->debug_email) > 0)
							{
								$email_msg = $this->sql_query;
								@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
							}
							//DISPLAY ERROR PAGE
							$this->body .= Site::cc_success_failure($db,0,$additional_msg);
							$this->display_page($db);
							$db->Close();
							exit;
						}
						$additional_msg .= "<tr class=success_failure_message><td>".urldecode($this->messages[2286])."</td>\n</tr>\n";
					}

					if ($show_cc_trans["featured_ad_5"] == 1)
					{
						$this->sql_query = "update geodesic_classifieds set	featured_ad_5 = \"1\" where id = ".$this->classified_id;
						if (strlen($this->debug_email) > 0)
						{
							@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
						}
						$featured_ad_result = $db->Execute($this->sql_query);
						if (!$featured_ad_result)
						{
							if (strlen($this->debug_email) > 0)
							{
								$email_msg = $this->sql_query;
								@mail($this->debug_email,"site_class.php LINE ".__LINE__,$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
							}
							//DISPLAY ERROR PAGE
							$this->body .= Site::cc_success_failure($db,0,$additional_msg);
							$this->display_page($db);
							$db->Close();
							exit;
						}
						$additional_msg .= "<tr class=success_failure_message><td>".urldecode($this->messages[2287])."</td>\n</tr>\n";
					}

					if ($show_cc_trans["attention_getter"] == 1)
					{
						$this->sql_query = "select * from geodesic_choices where choice_id = ".$show_cc_trans["attention_getter_choice"];
						$attention_getter_result = $db->Execute($this->sql_query);
						if (strlen($this->debug_email) > 0)
						{
							@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
						}
						if (!$attention_getter_result)
						{
							if (strlen($this->debug_email) > 0)
							{
								$email_msg = $this->sql_query;
								@mail($this->debug_email,"site_class.php LINE ".__LINE__,$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
							}
							//DISPLAY ERROR PAGE
							$this->body .= Site::cc_success_failure($db,0,$additional_msg);
							$this->display_page($db);
							$db->Close();
							exit;
						}
						elseif ($attention_getter_result->RecordCount() == 1)
						{
							$show_attention_getter = $attention_getter_result->FetchRow();
							$attention_getter_url = $show_attention_getter["value"];
						}
						else
						{
							$this->classified_variables["attention_getter"] = 0;
							$attention_getter_url = "";
						}

						$this->sql_query = "update geodesic_classifieds set	attention_getter = \"1\",
							attention_getter_url = \"".$attention_getter_url."\" where id = ".$this->classified_id;
						if (strlen($this->debug_email) > 0)
						{
							@mail($this->debug_email,"site_class.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
						}
						$attention_getter_update = $db->Execute($this->sql_query);
						if (!$attention_getter_update)
						{
							if (strlen($this->debug_email) > 0)
							{
								$email_msg = $this->sql_query;
								@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
							}
							//DISPLAY ERROR PAGE
							$this->body .= Site::cc_success_failure($db,0,$additional_msg);
							$this->display_page($db);
							$db->Close();
							exit;
						}
						$additional_msg .= "<tr class=success_failure_message><td>".urldecode($this->messages[856])."</td>\n</tr>\n";
					}
					//DISPLAY SUCCESS PAGE
					$this->body .= Site::cc_success_failure($db,1,$additional_msg);
					$this->display_page($db);
					$db->Close();
					exit;
				}
			}
			else
			{
				if (strlen($this->debug_email) > 0)
				{
					$email_msg = $this->sql_query;
					@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
				}
				//DISPLAY ERROR PAGE
				$this->body .= Site::cc_success_failure($db,0,$additional_msg);
				$this->display_page($db);
				$db->Close();
				exit;
			}
		}
		else
		{
			//FAILURE
			if (strlen($this->debug_email) > 0)
			{
				$email_msg = $handler_error_response;
				@mail($this->debug_email,"site_class.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
			}
			//DISPLAY ERROR PAGE
			$this->body .= Site::cc_success_failure($db,0,$additional_msg,$handler_error_response);
			$this->display_page($db);
			$db->Close();
			exit;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_year_dropdown($variable)
	{
		$date = getdate();

		$this->body .= "<select name=".$variable.">";
		for ($i=0;$i<20;$i++)
		{
			$this->body .= "<option value=".sprintf("%02d",($date['year']+$i)).">".sprintf("%02d",($date['year']+$i))."</option>";
		}
		$this->body .= "</select>";
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function includeAjax() {
		if(false == $this->withAjax) {
			$this->header_font_stuff .= "<script type='text/javascript'>
				var debug = 0;
				var status = '';

				function createRequestObject() {
				    var ro;
				    var browser = navigator.appName;
				    if(browser == 'Microsoft Internet Explorer'){
				        ro = new ActiveXObject('Microsoft.XMLHTTP');
				    }else{
				        ro = new XMLHttpRequest();
				    }
				    return ro;
				}

				var http = createRequestObject();

				function sendReq(action, b) {
					url = 'index.php?a=ajax&action='+action+'&b='+b;
					if(debug) status += 'URL: '+url+'\\n';
					try {
					    http.open('get', url);
						if(debug) status += 'Request sent\\n\\n';
					    http.onreadystatechange = handleResponse;
					    http.send(null);
					} catch(exception) {}
				}

				function handleResponse() {
				    if(http.readyState == 4) {
				        var response = http.responseText;
				        var update = new Array();

				        if(response.indexOf('|') != -1) {
				            update = response.split('|');
				   			if(debug) {
				   				if(debug) alert('Debug: \\n\\t'+status+'\\n\\nupdate[0] = '+update[0]+'\\n\\nupdate[1] = '+update[1]);
				   				status = '';
				   			}
				            document.getElementById(update[0]).innerHTML = update[1];
				        } else if(debug)
				        	alert ('Debug: \\n\\t'+status+'\\n\\n'+response);
				    }
				}
			</script>";
			$this->withAjax = true;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function formatUrls($string,$entireString)
	{
		$string = stripslashes($string);
		$endUrl = strstr($entireString, ">") ? ">" : "";
		$string = trim($string,"\"");
		$string = trim($string,"'");
		$newUrl = "href=\"";
		if(strstr($string, "#")&&strpos($string, "#")==0)
		{
			return $newUrl."http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].$string."\" ".$endUrl;
		}
		if(stristr($string,"javascript")!==FALSE&&stristr($string, "http")==FALSE)
		{
			$baseHREF = str_replace($this->configuration_data["classifieds_file_name"],"",$this->configuration_data["classifieds_url"]);

			if(stristr($string,"win('"))
				$string = str_replace("win('", "win('".$baseHREF, $string);
			if(stristr($string,"winimage('"))
				$string = str_replace("winimage('", "winimage('".$baseHREF, $string);

			return $newUrl.$string."\" ".$endUrl;
		}
		if(!stristr($string,$this->configuration_data["classifieds_file_name"]."?"))
			return $newUrl.$string."\" ".$endUrl;
		$urlParts = explode(".php?", $string);
		$urlGetVariables = preg_split("/[&]/", $urlParts[1]);
		$newUrl .= $urlParts[0];
		$a=0;
		for($lcv=0;$lcv<count($urlGetVariables);$lcv++)
		{
			$urlGetVariableValues = explode("=", $urlGetVariables[$lcv]);
			if(!$urlGetVariableValues[1])
				$urlGetVariableValues[1] = "0";

			switch($urlGetVariableValues[0])
			{
				case "a";
					$a = $urlGetVariableValues[1];
					switch($urlGetVariableValues[1])
					{
						case 2:
							$newUrl .= "/vehicle-view";
						break;
						case 5:
							if(count($urlGetVariables)==1)
								return "href=\"".$string."\" ".$endUrl;
							$newUrl .= "/listings";
						break;
						case 6:
							$newUrl .= "/other";
						break;
						case 8:
							$newUrl .= "/featured";
						break;
						case 11:
							$newUrl .= "/listings";
						break;
						case 15:
							$newUrl .= "/images";
						break;
						default:
							return "href=\"".$string."\" ".$endUrl;
						break;
					}
				break;
				case "b":
					switch($a)
					{
						case 2:
                        	$classifiedId = $urlGetVariableValues[1];
							$newUrl .= "/".$urlGetVariableValues[1];
						break;
						case 5:
							$newUrl .= "/category".$urlGetVariableValues[1];
						break;
						case 6:
							$newUrl .= "/seller".$urlGetVariableValues[1];
						break;
						case 8:
							$newUrl .= "/category".$urlGetVariableValues[1];
						break;
						case 11:
							$categoryId = $urlGetVariableValues[1];
						break;
						case 15:
							$newUrl .= "/item".$urlGetVariableValues[1];
						break;
					}
				break;
				case "c":
					switch($a)
					{
						case 11:
							switch($urlGetVariableValues[1])
							{
								case 1:
									$newUrl .= "/1week".$categoryId;
								break;
								case 2:
									$newUrl .= "/2weeks".$categoryId;
								break;
								case 3:
									$newUrl .= "/3weeks".$categoryId;
								break;
								case 4:
									$newUrl .= "/1day".$categoryId;
								break;
							}
						break;
						case 5:
							return "href=\"".$string."\" ".$endUrl;
						break;
					}
				break;
				case "page":
					$newUrl .= "/page".$urlGetVariableValues[1];
				break;
				default:
					return "href=\"".$string."\" ".$endUrl;
				break;
			}
		}
        if($a != 2)
        {
			return $newUrl.".htm\" ".$endUrl;
        }
        else
        {
        	$classified_data = $this->get_classified_data($this->db,$classifiedId, 1);
            $newUrl .= "/".$classified_data['title']."/\" ";
            $newUrl = str_replace("index/", "", $newUrl);
			// MLC remove /
            $newUrl = preg_replace("#\%2F#", "", $newUrl);
        	return $newUrl.$endUrl;
        }
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_image($db, $url, $width=0, $height=0, $mime_type=0, $icon=0)
	{
		$debug_display_image = 0;

		if($debug_display_image)
		{
			echo "<Br>TOP OF DISPLAY_IMAGE<bR>";
			echo $mime_type." is mime_type<br>\n";
			echo $width.' is the width<Br>';
			echo $height.' is the height<br>';
		}

		// Check if no mime type
		if(!$mime_type)
		{
			// If no icon lets either display the icon or the image

			$image = @getimagesize($url);
			$mime_type = $image['mime'];
			if(strpos($mime_type, "image/") === false)
			{
				// Get the icon out of the database
				$sql_query = "select file.mime_type, icon_to_use from ".$this->file_types_table." as file, ".$this->images_urls_table." as image where file.mime_type = image.mime_type and image_url like \"".$url."\" limit 1";
				$result = $db->Execute($sql_query);
				if($debug_display_image)
					echo $sql_query." is sql_query<Br>\n";
				if(!$result)
				{
					echo $db->ErrorMsg()."<bR>\n";
					return false;
				}

				$icon = $result->FetchRow();

				if(strpos($mime_type, "image/") === false)
				{
					$size = @getimagesize($icon['icon_to_use']);

					$return = "<img src=\"".$icon['icon_to_use']."\"";
					if($width)
						$return .= " width=\"".$size[0]."\"";
					if($height)
						$return .= " height=\"".$size[1]."\"";
					$return .= " border=0>";

					return $return;
				}
			}
			else
			{
				$return = "<img src=\"".$url."\"";
				if($width)
					$return .= " width=\"".$width."\"";
				if($height)
					$return .= " height=\"".$height."\"";
				$return .= " border=0>";

				return $return;
			}
		}

		// Check if it is an image
		if(strpos($mime_type, "image/") !== false)
		{
			$return = "<img src=\"".$url."\"";
			if($width)
				$return .= " width=\"".$width."\"";
			if($height)
				$return .= " height=\"".$height."\"";
			$return .= " border=0>";

			return $return;
		}

		$mime_type = eregi_replace("(text|application|video|audio|music|www|x-world|multipart|xgl|chemical){1}\/", "", $mime_type);
		switch ($mime_type)
		{
			case 'x-shockwave-flash':
				if(!$icon)
					return "<OBJECT classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"
						codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0\"
						WIDTH=\"".$width."\" HEIGHT=\"".$height."\" id=\"myMovieName\">
						<PARAM NAME=movie VALUE=\"".$url."\">
						<PARAM NAME=quality VALUE=high>
						<PARAM NAME=bgcolor VALUE=#FFFFFF>
						<EMBED src=\"".$url."\" quality=high bgcolor=#FFFFFF WIDTH=\"".$width."\" HEIGHT=\"".$height."\"
						NAME=\"myMovieName\" TYPE=\"application/x-shockwave-flash\"
						PLUGINSPAGE=\"http://www.macromedia.com/go/getflashplayer\">
						</EMBED>
						</OBJECT>";
				else
				{
					// Get the icon out of the database
					$sql_query = "select file.mime_type, icon_to_use from ".$this->file_types_table." as file, ".$this->images_urls_table." as image where file.mime_type = image.mime_type and image_url like \"".$url."\" limit 1";
					$result = $db->Execute($sql_query);
					if($debug_display_image)
						echo $sql_query." is sql_query<Br>\n";
					if(!$result)
					{
						echo $db->ErrorMsg()."<bR>\n";
						return false;
					}

					$icon = $result->FetchRow();

					if(strpos($mime_type, "image/") === false)
					{
						$size = @getimagesize($icon['icon_to_use']);

						$return = "<img src=\"".$icon['icon_to_use']."\"";
						if($width)
							$return .= " width=\"".$size[0]."\"";
						if($height)
							$return .= " height=\"".$size[1]."\"";
						$return .= " border=0>";

						return $return;
					}
				}
				break;

			default:
				$return = "<img src=\"".$url."\"";
				if($width)
					$return .= " width=\"".$width."\"";
				if($height)
					$return .= " height=\"".$height."\"";
				$return .= " border=0>";

				return $return;
				break;
		}
	}	// end of function display_image

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function featured_ads_pic_level_1_display($db, $show, $show_module, $class_name)
	{
		$debug_module = 0;

		if (($this->configuration_data['popup_while_browsing'])
			&& ($this->configuration_data['popup_while_browsing_width'])
			&& ($this->configuration_data['popup_while_browsing_height']))
		{
			$this->body .= "<a href=\"".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show['id']."\" ";
			$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
		}
		else
		{
			$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show['id'].">";
		}
		$current_max_width = $show_module['module_thumb_width'];
		$current_max_height = $show_module['module_thumb_height'];

		if (($show['image_width'] > $current_max_width) && ($show['image_height'] > $current_max_height))
		{
			$imageprop = ($current_max_width * 100) / $show['image_width'];
			$imagevsize = ($show['image_height'] * $imageprop) / 100 ;
			$final_image_width = $current_max_width;
			$final_image_height = ceil($imagevsize);

			if ($final_image_height > $current_max_height)
			{
				$imageprop = ($current_max_height * 100) / $show['image_height'];
				$imagehsize = ($show['image_width'] * $imageprop) / 100 ;
				$final_image_height = $current_max_height;
				$final_image_width = ceil($imagehsize);
			}
		}
		elseif ($show['image_width'] > $current_max_width)
		{
			$imageprop = ($current_max_width * 100) / $show['image_width'];
			$imagevsize = ($show['image_height'] * $imageprop) / 100 ;
			$final_image_width = $current_max_width;
			$final_image_height = ceil($imagevsize);
		}
		elseif ($show['image_height'] > $current_max_height)
		{
			$imageprop = ($current_max_height * 100) / $show['image_height'];
			$imagehsize = ($show['image_width'] * $imageprop) / 100 ;
			$final_image_height = $current_max_height;
			$final_image_width = ceil($imagehsize);
		}
		else
		{
			$final_image_width = $show['image_width'];
			$final_image_height = $show['image_height'];
		}

		if($debug_module)
		{
			echo $show["thumb_url"]." is show[thumb_url] in featured_ads_pic_level_1_display<Br>\n";
			echo $show["mime_type"]." is show[mime_type]<br>\n";
		}

		if (($show['thumb_url']) && ($show['thumb_url'] != "0"))
			$this->body .= $this->display_image($db, $show['thumb_url'], $final_image_width, $final_image_height,$show["mime_type"], 1);
			//$this->body .="<img src=\"".$show['thumb_url']."\" width=\"".$final_image_width."\" height=\"".$final_image_height."\" border=0>";
		else
			$this->body .= $this->display_image($db, $show['image_url'], $final_image_width, $final_image_height,$show["mime_type"], 1);

		if (($show_module['module_display_title']) || ($show_module['module_display_price']) || ($show_module['module_display_type_text']))
		{
			$this->body .= "</td></tr>";
			$this->body .= "<tr><td class=".$class_name.">";
		}
		if ($show_module['module_display_title'])
			$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show['id'].">".substr(urldecode($show[$show_module['module_text_type']]),0,$show_module['length_of_description'])."</a>";
		if (($show_module['module_display_price']) && (($show[$this->item_price($show['item_type'])] != 0)
			|| (strlen(trim(urldecode($show['precurrency']))) > 0)
			|| (strlen(trim(urldecode($show['postcurrency']))) > 0)))
		{
			if ($show_module['module_display_title'])
				$this->body .= "<br>";
		    
			$p = $show[$this->item_price($show['item_type'])];
			
		    if($show_module['use_current'])
			   $p = $show['current_bid'];
			   
			if (floor($show[$this->item_price($show['item_type'])]) == $show[$this->item_price($show['item_type'])])
			{    			    
				$this->body .= "<nobr>".stripslashes(urldecode($show['precurrency'])). " ".
					number_format($p)." ".
					stripslashes(urldecode($show['postcurrency']))."</nobr>";
			}
			else
			{
				$this->body .= "<nobr>".stripslashes(urldecode($show['precurrency'])). " ".
					number_format($p,2,".",",")." ".
					stripslashes(urldecode($show['postcurrency']))."</nobr>";
			}
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_ip($db)
	{
		if ($this->debug)
		{
			echo "<br>TOP OF CHECK_IP<BR>\n";
		}
		//code to check the current ip against a banned list in the database
		$ip_to_check = $_SERVER['REMOTE_ADDR'];
				
		$this->sql_query = "select * from ".$this->ip_ban_table." where ip = \"".$ip_to_check."\"";
		$ip_result = $db->Execute($this->sql_query);
		if ($this->debug) echo $this->sql_query."<br>\n";
		if (!$ip_result)
		{
			//do nothing until verified	
			if ($this->debug)
			{
				echo $db->ErrorMsg()." is the error<br>\n";
				echo $this->sql_query."<br>\n";	
			}
		}
		elseif ($ip_result->RecordCount() > 0)
		{
			//this ip exists in the ip ban list
			//do not allow to communicate
			if ($this->debug_notify)
			{
				echo "this ip is banned<br>\n";	
			}					
			header("Location: ".$this->configuration_data->CLASSIFIEDS_URL);
			exit;
		}
		return true;
	} //end of function check_ip
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Site
?>
