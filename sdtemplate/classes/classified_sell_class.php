<? //classified_sell_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Classified_sell extends Site {

	var $terminal_category = 0;

	var $classified_details_collected = 0;
	var $classified_images_collected = 0;
	var $billing_approved = 0;
	var $final_approval = 0;
	var $check_splash= 0;
	var $classified_id;

	var $setup_error = "";

	var $error=0;
	var $images_received = "";
	var $images_error;

	var $badword_list;
	var $badword_replacement;

	var $accepted_file_types;

	var $images_captured = array();
	var $first_image_filled = 0;
	var $html_disallowed_list = array();
	var $html_disallowed_string = "/(<\/?)+++++([^>]*>)/e";

	var $users_group;
	var $users_price_plan;
	var $user_credits = 0;
	var $user_currently_subscribed = 0;

	var $price_plan;
	var $subtotal;
	var $discount;
	var $cost_of_ad;
	var $final_fee;
	var $final_fee_total;
	var $total;
	var $tax;
	var $discount_percentage = 0;
	var $amount_to_charge_balance = 0;

	var $copy_id = 0;
	var $session_id;

	var $debug_sell = 0;

    var $user_data;

//#####################################################################

	function Classified_sell ($db,$classified_user_id,$language_id,$session_id,$copy_id=0,$product_configuration=0)
	{
		 //constructor
		  $this->Site($db,2,$language_id,$classified_user_id,$product_configuration);

		  if($copy_id)
		  	$this->copy_id = $copy_id;

		 //delete expired sell sessions (after 24 hours)
		 $this->remove_old_sell_sessions($db);

		  $this->session_id = $session_id;
		  $this->setup_sell_session($db);

		 //delete expired images
		 $this->delete_expired_images($db);

		 $this->check_invoice_cutoff($db);

         if($classified_user_id) $this->user_data = $this->get_user_data($db,$classified_user_id);

		 //delete expired ads on hold
		 //taken care of in browse class
		 //$this->delete_expired_ads_on_hold($db);

		 return true;

	} //end of function Classified_sell

//####################################################################

	function setup_sell_session($db)
	{
		if ($this->debug_sell)
		{
			echo "<BR>TOP OF SETUP_SELL_SESSION<Br>\n";
		}
		if ($this->session_id)
		{
			$this->sql_query = "select * from ".$this->sell_table." where session = \"".$this->session_id."\" and renew_upgrade = 0";
			$setup_sell_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query."<br>\n";
			if (!$setup_sell_result)
			{
				if ($this->debug_sell) echo $this->sql_query."<br>\n";
				$this->setup_error = $this->messages[453];
				return false;
			}
			elseif ($setup_sell_result->RecordCount() == 1)
			{
				//get variables from db and save in local variables
				$this->set_sell_variables($db, $setup_sell_result);

				//get the images captured so far
				$this->sql_query = "select * from ".$this->sell_images_table." where session = \"".$this->session_id."\" order by display_order";
				$setup_sell_image_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<br>\n";
				if (!$setup_sell_image_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					$this->setup_error = $this->messages[453];
					return false;
				}
				elseif ($setup_sell_image_result->RecordCount() > 0)
				{
					while ($show_image = $setup_sell_image_result->FetchNextObject())
					{
						$this->images_captured[$show_image->DISPLAY_ORDER]["type"] = $show_image->IMAGE_TYPE;
						$this->images_captured[$show_image->DISPLAY_ORDER]["id"] = $show_image->IMAGE_ID;
					}
				}
				if (is_array($this->images_captured))
					ksort($this->images_captured);

				//get the category questions so far
				$this->sql_query = "select * from ".$this->sell_questions_table." where session = \"".$this->session_id."\" and group_id = 0 order by display_order";
				$setup_sell_question_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<br>\n";
				if (!$setup_sell_question_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					$this->setup_error = $this->messages[453];
					return false;
				}
				elseif ($setup_sell_question_result->RecordCount() > 0)
				{
					while ($show_question = $setup_sell_question_result->FetchNextObject())
					{
						$this->classified_variables["question_display_order"][$show_question->TYPE_ID] = $show_question->DISPLAY_ORDER;
						$this->classified_variables["question_value"][$show_question->TYPE_ID] = urldecode($show_question->QUESTION_VALUE);
						$this->classified_variables["question_value_other"][$show_question->TYPE_ID] = urldecode($show_question->QUESTION_VALUE_OTHER);
					}
				}

				//get the group questions so far
				$this->sql_query = "select * from ".$this->sell_questions_table." where session = \"".$this->session_id."\" and group_id != 0 order by display_order";
				$setup_sell_question_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<br>\n";
				if (!$setup_sell_question_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					$this->setup_error = $this->messages[453];
					return false;
				}
				elseif ($setup_sell_question_result->RecordCount() > 0)
				{
					while ($show_question = $setup_sell_question_result->FetchNextObject())
					{
						$this->classified_variables["group_display_order"][$show_question->TYPE_ID] = $show_question->DISPLAY_ORDER;
						$this->classified_variables["group_value"][$show_question->TYPE_ID] = urldecode($show_question->QUESTION_VALUE);
						$this->classified_variables["group_value_other"][$show_question->TYPE_ID] = urldecode($show_question->QUESTION_VALUE_OTHER);
					}
				}

				 //see if this user has reached their maximum allowed ads
				 $this->check_maximum_ad_limit($db);

				 // Set the price plan
				 if ($this->debug_sell)
				 {
				 	echo "about to call get_this_price_plan<Br>\n";
				 	echo $_REQUEST["set_cat"]." is request[set_cat]<bR>\n";
				 	echo $_REQUEST["b"]." is request[b]<bR>\n";
				 	echo $this->users_price_plan." is this users price plan<BR>\n";
				 }
				 if($this->users_price_plan)
				 	$this->get_this_price_plan($db);

				 if ($this->sell_type == 2)
				 {
					 if (strlen(trim($this->setup_error)) == 0)
					 {
					 	return true;
					 }
					 else
					 {
						$this->remove_sell_session($db,$this->session_id);
						$this->setup_sell_error_display($db);
					 	exit;
					 }
				 }
			}
			elseif($this->copy_id)
			{
				// The array all the data will be put into
				$session_data = array();

				// The fields that will be skipped
				$skip_list = array(
						'id' => 1,
						'seller' => 1,
						'live' => 1,
						'date' => 1,
						'precurrency' => 1,
						'postcurrency' => 1,
						'image' => 1,
						'duration' => 1,
						'ends' => 1,
						'search_text' => 1,
						'viewed' => 1,
						'responded' => 1,
						'forwarded' => 1,
						'transaction_type' => 1,
						'cc_transaction_type' => 1,
						'subtotal' => 1,
						'tax' => 1,
						'total' => 1,
						'customer_approved' => 1,
						'expiration_notice' => 1,
						'expiration_last_sent' => 1,
						'sold_displayed' => 1,
						'one_votes' => 1,
						'two_votes' => 1,
						'three_votes' => 1,
						'vote_total' => 1,
						'business_type' => 1,
						'email' => 1,
						'renewal_payment_expected' => 1,
						'renewal_payment_expected_by' => 1,
						'renewal_length' => 1,
						'renewal_featured_ad' => 1,
						'renewal_featured_ad_2' => 1,
						'renewal_featured_ad_3' => 1,
						'renewal_featured_ad_4' => 1,
						'renewal_featured_ad_5' => 1,
						'renewal_bolding' => 1,
						'renewal_better_placement' => 1,
						'renewal_attention_getter' => 1,
						'renewal_attention_getter_choice' => 1,
						'renewal_subtotal' => 1,
						'renewal_tax' => 1,
						'renewal_total' => 1,
						'discount_id' => 1,
						'discount_amount' => 1,
						'discount_percentage' => 1,
						'price_plan_id' => 1,
						'auction_length' => 1,
						'final_fee_transaction_number' => 1,
						'starting_bid' => 1,
						'current_bid' => 1,
						'final_price' => 1,
						'high_bidder' => 1,
						'start_time' => 1,
						'end_time' => 1,
						'buy_now_only' => 1,
						'type' => 1,

						'session'  => 1,
						'time_started'  => 1,
						'classified_id'  => 1,
						'classified_details_collected'  => 1,
						'classified_images_collected'  => 1,
						'classified_approved'  => 1,
						'billing_approved'  => 1,
						'final_approval'  => 1,
						'users_group'  => 1,
						'users_price_plan'  => 1,
						'user_credits'  => 1,
						'user_currently_subscribed'  => 1,
						'payment_type'  => 1,
						'cc_number'  => 1,
						'decryption_key'  => 1,
						'cc_exp_year'  => 1,
						'cc_exp_month'  => 1,
						'subscription_choice'  => 1,
						'check_splash'  => 1,
						'renew_upgrade'  => 1,
						'ad_renewal'  => 1,
						'bolding_upgrade'  => 1,
						'better_placement_upgrade'  => 1,
						'featured_ad_upgrade'  => 1,
						'featured_ad_2_upgrade'  => 1,
						'featured_ad_3_upgrade'  => 1,
						'featured_ad_4_upgrade'  => 1,
						'featured_ad_5_upgrade'  => 1,
						'attention_getter_upgrade'  => 1,
						'attention_getter_choice_upgrade'  => 1,
						'paypal_id'  => 1,
						'user_credit_for_renewal'  => 1,
						'discount_code'  => 1,
						'account_balance'  => 1,
						'print'  => 1,
						'print_description'  => 1,
						'print_web_approved' => 1,
						'cvv2_code' => 1,
						'final_fee' => 1
				);

				// The fields that will have a different name
				// key is value in listing
				// value is key in session
				$diff_list = array();
				$diff_list['category'] = 'terminal_category';
				$diff_list['title'] = 'classified_title';
				$diff_list['location_city'] = 'city';
				$diff_list['location_state'] = 'state';
				$diff_list['location_country'] = 'country';
				$diff_list['location_zip'] = 'zip_code';
				$diff_list['attention_getter_url'] = 'attention_getter_choice';
				$diff_list['phone'] = 'phone_1_option';
				$diff_list['phone2'] = 'phone_2_option';
				$diff_list['fax'] = 'fax_option';
				$diff_list['quantity'] = 'auction_quantity';
				$diff_list['minimum_bid'] = 'auction_minimum';
				$diff_list['reserve_price'] = 'auction_reserve';
				$diff_list['buy_now'] = 'auction_buy_now';
				$diff_list['item_type'] = 'type';

				// Set some needed variables
				$session_data['classified_details_collected'] = 1;
				$session_data['classified_images_collected'] = 1;
				$session_data['time_started'] = time();
				$session_data['start_time'] = time();

				// Get all data from the copied from listing
				$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$this->copy_id;
				$listing_result = $db->Execute($this->sql_query);
				if(!$listing_result)
					return false;
				else
					$listing = $listing_result->FetchRow();

				// Copy from current listing into new sell session
				$user_data = $this->get_user_data($db,$this->classified_user_id);

				$this->sql_query = "insert into ".$this->sell_table."
					(session,time_started,phone_1_option,phone_2_option,fax_option,city,state,country,zip_code,
					mapping_address,mapping_city,mapping_state,mapping_country,mapping_zip)
					values
					(\"".$this->session_id."\",".$this->shifted_time($db).",\"".$user_data->PHONE."\",\"".$user_data->PHONE2."\",\"".$user_data->FAX."\",
					\"".urlencode($user_data->CITY)."\",\"".urlencode($user_data->STATE)."\",\"".urlencode($user_data->COUNTRY)."\",\"".urlencode($user_data->ZIP)."\",
					\"".urlencode($user_data->ADDRESS." ".$user_data->ADDRESS2)."\",\"".urlencode($user_data->CITY)."\",\"".urlencode($user_data->STATE)."\",
					\"".urlencode($user_data->COUNTRY)."\",\"".urlencode($user_data->ZIP)."\")";
				$insert_sell_result = $db->Execute($this->sql_query);
				if ($this->debug_sell)
					echo $this->sql_query."<br>\n";
				if (!$insert_sell_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					$this->setup_error = $this->messages[453];
					return false;
				}

				// Set the group and price plan data
				$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
				$group_result = $db->Execute($this->sql_query);
				if(!$group_result)
					return false;
				else
					$group = $group_result->FetchRow();

				$session_data['users_group'] = $group['group_id'];
				$session_data['users_price_plan'] = ($listing['item_type'] == 1) ? $group['price_plan_id'] : $group['auction_price_plan_id'];

				// Set the correct currency type
				$this->sql_query = "select type_id from ".$this->currency_types_table." where precurrency =\"".urldecode($listing['precurrency'])."\" and postcurrency =\"".urldecode($listing['postcurrency'])."\"";
				$currency_result = $db->Execute($this->sql_query);
				if(!$currency_result)
					return false;
				else
					$currency = $currency_result->FetchRow();

				$session_data['currency_type'] = $currency['type_id'];

				$this->sql_query = "update ".$this->sell_table." set ";
				foreach($listing as $key => $value)
				{
					// If we hit a non-string key lets skip it
					if(is_int($key))
						continue;

					// Main loop that runs through all the elements
					// Check if we need to skip this one
					if($skip_list[$key])
						continue;

					// Check if it needs to be renamed
					if($diff_list[$key])
						$key = $diff_list[$key];

					$this->sql_query .= $key." = \"".$value."\", ";
				}

				// Set the end time on the listing
				if($listing['duration'])
					$session_data['end_time'] = time() + $listing['duration']*86400;
				else
				{
					$delta = $listing['end_time'] - $listing['date'];
					$session_data['end_time'] = time() + $delta;
				}

				// Append the session_data array onto it also
				foreach ($session_data as $key => $value)
				{
					$this->sql_query .= $key." = \"".$value."\", ";
				}
				$this->sql_query = rtrim($this->sql_query, " ,");

				$this->sql_query .= " where session = \"".$this->session_id."\"";
				$update_result = $db->Execute($this->sql_query);
				//echo $this->sql_query.'<Br>';
				if(!$update_result)
					return false;

				// Handle the images
				if($listing['image'])
				{
					$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$listing['id'];
					//echo $this->sql_query.'<Br>';
					$image_urls_result = $db->Execute($this->sql_query);
					if(!$image_urls_result)
						return false;

					if(!$this->ad_configuration_data)
							$this->get_ad_configuration($db);

					while($image_urls = $image_urls_result->FetchRow())
					{
						// Check if it is a URL-referenced or an uploaded image
						if($image_urls['full_filename'])
						{
							$this->sql_query = "select extension from ".$this->file_types_table." where mime_type = \"".$image_urls['mime_type']."\"";
							$extension_result = $db->Execute($this->sql_query);
							if(!$extension_result)
								return false;
							else
								$extension = $extension_result->FetchRow();

							// Set the file extension
							$this->current_file_type_extension = $extension['extension'];

							// Uploaded image
							do
							{
								srand((double)microtime()*1000000);
								$filename_root = rand(1000000,9999999);
								$filepath = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename_root.".".$this->current_file_type_extension;
							} while (file_exists($filepath));

							$full_filename = $filename_root.".".$this->current_file_type_extension;
							$full_url = $this->ad_configuration_data->URL_IMAGE_DIRECTORY.$full_filename;

							if(!copy($image_urls['image_url'], $full_url))
							{
								echo 'Errro copying<Br>';
								return false;
							}

							$image_urls['full_filename'] = $full_filename;
							$image_urls['image_url'] = $full_url;
						}

						$first_part = "";
						$second_part = "";
						foreach($image_urls as $key => $value)
						{
							if(is_int($key))
								continue;
							elseif($key == "classified_id")
								continue;
							elseif($key == "image_id")
								continue;

							$first_part .= $key.", ";
							$second_part .= "\"".$value."\", ";
						}
						$first_part = rtrim($first_part, " ,");
						$second_part = rtrim($second_part, " ,");

						$this->sql_query = "insert into ".$this->images_urls_table." (".$first_part.") values (".$second_part.")";
						//echo $this->sql_query.'<Br>';
						$image_insert_result = $db->Execute($this->sql_query);
						if(!$image_insert_result)
							return false;

						$insert_id = $db->Insert_ID();

						$this->sql_query = "insert into ".$this->sell_images_table."
							(session,display_order,image_type,image_id)
							values
							(\"".$this->session_id."\",".$image_urls['display_order'].",1,".$insert_id.")";
						$image_insert_result = $db->Execute($this->sql_query);
						//echo $this->sql_query."<br>\n";
						if (!$image_insert_result)
						{
							$this->error_message = urldecode($this->messages[57]);
							return false;
						}

						// Now add it to the images captured array
						$this->images_captured[$image_urls['display_order']]['type'] = 1;
						$this->images_captured[$image_urls['display_order']]['id'] = $insert_id;
					}
				}

				// Set some final data
				$this->set_group_and_price_plan($db);
				$this->set_terminal_category($db, $listing['category']);
				$this->set_filter_id($db);
				$this->set_sell_type($db, $listing['item_type']);

				// Set the images as updated
				$this->update_images_collected($db, 1);

				// Now set all the sell session variables
				$this->sql_query = "select * from ".$this->sell_table." where session = \"".$this->session_id."\" and renew_upgrade = 0";
				$setup_sell_result = $db->Execute($this->sql_query);
				if(!$setup_sell_result)
					return false;
				else
					$this->set_sell_variables($db, $setup_sell_result);

				// Display the approval form
				$this->classified_approval_display($db);
				exit;
			}
			else
			{
				//create new sell session
				$user_data = $this->get_user_data($db,$this->classified_user_id);
				$this->sql_query = "insert into ".$this->sell_table."
					(session,time_started,phone_1_option,phone_2_option,fax_option,city,state,country,zip_code,
					mapping_address,mapping_city,mapping_state,mapping_country,mapping_zip)
					values
					(\"".$this->session_id."\",".$this->shifted_time($db).",\"".$user_data->PHONE."\",\"".$user_data->PHONE2."\",\"".$user_data->FAX."\",
					\"".urlencode($user_data->CITY)."\",\"".urlencode($user_data->STATE)."\",\"".urlencode($user_data->COUNTRY)."\",\"".urlencode($user_data->ZIP)."\",
					\"".urlencode($user_data->ADDRESS." ".$user_data->ADDRESS2)."\",\"".urlencode($user_data->CITY)."\",\"".urlencode($user_data->STATE)."\",
					\"".urlencode($user_data->COUNTRY)."\",\"".urlencode($user_data->ZIP)."\")";
				$insert_sell_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<br>\n";
				if (!$insert_sell_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					$this->setup_error = $this->messages[453];
					return false;
				}

				if ($this->configuration_data['use_filters'])
				{
					 //set filter
					$this->set_filter_id($db);

					for($i = 1; $i < 21; $i++)
					{
						$this->sql_query = "update ".$this->sell_table." set ";
						if ($this->configuration_data['optional_'.$i.'_filter_association'])
						{
							$this->classified_variables["optional_field_".$i] = $this->get_filter_value($db,$this->configuration_data['optional_'.$i.'_filter_association']);
							if ($this->debug_sell)
								echo $this->classified_variables["optional_field_".$i]." is optional ".$i." after get_filter_value<BR>\n";
							$this->sql_query .= "optional_field_".$i." = \"".$this->classified_variables["optional_field_".$i]."\", ";
							$update_filter_result = $db->Execute($this->sql_query);
							if ($this->debug_sell)
								echo $this->sql_query."<bR>\n";
						}
						$this->sql_query = rtrim($this->sql_query, " ,");
						$this->sql_query .= " where session =\"".$this->session_id."\"";
					}
					//$this-> save_form_variables($db);
				}

				 //set users_group and users_price_plan
				$this->set_group_and_price_plan($db);

				 //see if this user has reached their maximum allowed ads
				 $this->check_maximum_ad_limit($db);

				 if ($this->sell_type == 2)
				 {
					 if (strlen(trim($this->setup_error)) == 0)
					 {
					 	return true;
					 }
					 else
					 {
						$this->remove_sell_session($db,$this->session_id);
						$this->setup_sell_error_display($db);
					 	exit;
					 }
				 }
			}
		}
	} //end of funciton setup_sell_session

//####################################################################

	function set_sell_variables($db, $result)
	{
		$show = $result->FetchNextObject();
		$this->check_splash = $show->CHECK_SPLASH;
		$this->terminal_category = $show->TERMINAL_CATEGORY;
		$this->classified_details_collected = $show->CLASSIFIED_DETAILS_COLLECTED;
		$this->classified_images_collected = $show->CLASSIFIED_IMAGES_COLLECTED;
		$this->classified_approved = $show->CLASSIFIED_APPROVED;
		$this->billing_approved = $show->BILLING_APPROVED;
		$this->final_approval = $show->FINAL_APPROVED;
		$this->users_group = $show->USERS_GROUP;
		$this->users_price_plan = $show->USERS_PRICE_PLAN;
		$this->user_credits = $show->USER_CREDITS;
		$this->user_currently_subscribed = $show->USER_CURRENTLY_SUBSCRIBED;
		$this->classified_id = $show->CLASSIFIED_ID;
		$this->filter_id = $show->FILTER_ID;
		$this->sell_type = $show->TYPE;
		$this->final_fee = $show->FINAL_FEE;

		if ($this->debug_sell)
		{
			echo $this->final_fee." is final_fee<BR>\n";
			echo $this->sell_type." is sell_type<BR>\n";
			echo $show->AUCTION_MINIMUM." is AUCTION_MINIMUM<bR>\n";
			echo $show->PRICE." is PRICE<bR>\n";
		}
		if (($this->sell_type == 2) && ($this->final_fee != 1))
		{
			//check to see if final fee charged
			$this->check_final_fee($db);

			// Set price to minimum bid
			$show->PRICE = $show->AUCTION_MINIMUM;
		}

		$this->classified_variables["classified_length"] = $show->CLASSIFIED_LENGTH;
		$this->classified_variables["classified_title"] = urldecode($show->CLASSIFIED_TITLE);
		$this->classified_variables["description"] = urldecode($show->DESCRIPTION);
		$this->classified_variables["price"] = $show->PRICE;
		$this->classified_variables["currency_type"] = $show->CURRENCY_TYPE;
		$this->classified_variables["city"] = urldecode($show->CITY);
		$this->classified_variables["state"] = $show->STATE;
		$this->classified_variables["country"] = $show->COUNTRY;
		$this->classified_variables["zip_code"] = urldecode($show->ZIP_CODE);
		$this->classified_variables["payment_type"] = $show->PAYMENT_TYPE;
		$this->classified_variables["cc_number"] = Site::decrypt($show->CC_NUMBER, $show->DECRYPTION_KEY);
		$this->classified_variables["decryption_key"] = 0;
		$this->classified_variables["cc_exp_month"] = $show->CC_EXP_MONTH;
		$this->classified_variables["cc_exp_year"] = $show->CC_EXP_YEAR;
		$this->classified_variables["cvv2_code"] = $show->CVV2_CODE;
		$this->classified_variables["featured_ad"] = $show->FEATURED_AD;
		$this->classified_variables["featured_ad_2"] = $show->FEATURED_AD_2;
		$this->classified_variables["featured_ad_3"] = $show->FEATURED_AD_3;
		$this->classified_variables["featured_ad_4"] = $show->FEATURED_AD_4;
		$this->classified_variables["featured_ad_5"] = $show->FEATURED_AD_5;
		$this->classified_variables["attention_getter"] = $show->ATTENTION_GETTER;
		$this->classified_variables["attention_getter_choice"] = $show->ATTENTION_GETTER_CHOICE;
		$this->classified_variables["bolding"] = $show->BOLDING;
		$this->classified_variables["better_placement"] = $show->BETTER_PLACEMENT;
		$this->classified_variables["subscription_choice"] = $show->SUBSCRIPTION_CHOICE;
		$this->classified_variables["credit_choice"] = $show->CREDIT_CHOICE;
		$this->classified_variables["email_option"] = $show->EMAIL_OPTION;
		$this->classified_variables["expose_email"] = $show->EXPOSE_EMAIL;
		$this->classified_variables["phone_1_option"] = $show->PHONE_1_OPTION;
		$this->classified_variables["phone_2_option"] = $show->PHONE_2_OPTION;
		$this->classified_variables["fax_option"] = $show->FAX_OPTION;
		$this->classified_variables["url_link_1"] = stripslashes($show->URL_LINK_1);
		$this->classified_variables["url_link_2"] = stripslashes($show->URL_LINK_2);
		$this->classified_variables["url_link_3"] = stripslashes($show->URL_LINK_3);
		$this->classified_variables["optional_field_1"] = urldecode($show->OPTIONAL_FIELD_1);
		$this->classified_variables["optional_field_2"] = urldecode($show->OPTIONAL_FIELD_2);
		$this->classified_variables["optional_field_3"] = urldecode($show->OPTIONAL_FIELD_3);
		$this->classified_variables["optional_field_4"] = urldecode($show->OPTIONAL_FIELD_4);
		$this->classified_variables["optional_field_5"] = urldecode($show->OPTIONAL_FIELD_5);
		$this->classified_variables["optional_field_6"] = urldecode($show->OPTIONAL_FIELD_6);
		$this->classified_variables["optional_field_7"] = urldecode($show->OPTIONAL_FIELD_7);
		$this->classified_variables["optional_field_8"] = urldecode($show->OPTIONAL_FIELD_8);
		$this->classified_variables["optional_field_9"] = urldecode($show->OPTIONAL_FIELD_9);
		$this->classified_variables["optional_field_10"] = urldecode($show->OPTIONAL_FIELD_10);
		$this->classified_variables["optional_field_11"] = urldecode($show->OPTIONAL_FIELD_11);
		$this->classified_variables["optional_field_12"] = urldecode($show->OPTIONAL_FIELD_12);
		$this->classified_variables["optional_field_13"] = urldecode($show->OPTIONAL_FIELD_13);
		$this->classified_variables["optional_field_14"] = urldecode($show->OPTIONAL_FIELD_14);
		$this->classified_variables["optional_field_15"] = urldecode($show->OPTIONAL_FIELD_15);
		$this->classified_variables["optional_field_16"] = urldecode($show->OPTIONAL_FIELD_16);
		$this->classified_variables["optional_field_17"] = urldecode($show->OPTIONAL_FIELD_17);
		$this->classified_variables["optional_field_18"] = urldecode($show->OPTIONAL_FIELD_18);
		$this->classified_variables["optional_field_19"] = urldecode($show->OPTIONAL_FIELD_19);
		$this->classified_variables["optional_field_20"] = urldecode($show->OPTIONAL_FIELD_20);
		$this->classified_variables["mapping_address"] = urldecode($show->MAPPING_ADDRESS);
		$this->classified_variables["mapping_city"] = urldecode($show->MAPPING_CITY);
		$this->classified_variables["mapping_state"] = urldecode($show->MAPPING_STATE);
		$this->classified_variables["mapping_country"] = urldecode($show->MAPPING_COUNTRY);
		$this->classified_variables["mapping_zip"] = urldecode($show->MAPPING_ZIP);
		$this->classified_variables["discount_code"] = urldecode($show->DISCOUNT_CODE);
		$this->classified_variables["paypal_id"] = urldecode($show->PAYPAL_ID);
		$this->classified_variables["auction_type"] = urldecode($show->AUCTION_TYPE);
		$this->classified_variables["auction_quantity"] = urldecode($show->AUCTION_QUANTITY);
		$this->classified_variables["auction_minimum"] = urldecode($show->AUCTION_MINIMUM);
		$this->classified_variables["auction_reserve"] = urldecode($show->AUCTION_RESERVE);
		$this->classified_variables["auction_buy_now"] = urldecode($show->AUCTION_BUY_NOW);
		$this->classified_variables["payment_options"] = urldecode($show->PAYMENT_OPTIONS);
		$this->classified_variables["sell_type"] = urldecode($show->TYPE);
		$this->classified_variables["start_time"] = $show->START_TIME;
		$this->classified_variables["end_time"] = $show->END_TIME;

		//STOREFRONT CODE
		if($show->STOREFRONT_CATEGORY)
		{
			$this->classified_variables["storefront_category"] = $show->STOREFRONT_CATEGORY;
		}
		//STOREFRONT CODE

		if ($this->debug_sell)
		{
			echo $this->users_price_plan." is users_price_plan<bR>\n";
			echo $this->classified_variables["auction_minimum"]." is auction_minimum<bR>\n";
			echo $this->terminal_category." is the terminal_category<BR>\n";

		}
	}

//####################################################################

	function remove_sell_session($db,$sell_session=0,$classified_id=0)
	{
		if ($sell_session)
		{
			$this->function_name = "remove_sell_session";
			$this->sql_query = "delete from ".$this->sell_table." where session = \"".$sell_session."\"";
			$delete_sell_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query."<Br>\n";
			if (!$delete_sell_result)
			{
				if ($this->debug_sell) echo $this->sql_query."<Br>\n";
				return false;
			}

			$this->sql_query = "delete from ".$this->sell_images_table." where session = \"".$sell_session."\"";
			$delete_sell_image_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query."<Br>\n";
			if (!$delete_sell_image_result)
			{
				if ($this->debug_sell) echo $this->sql_query."<Br>\n";
				return false;
			}

			//get the category questions so far
			$this->sql_query = "delete from ".$this->sell_questions_table." where session = \"".$sell_session."\"";
			$delete_sell_question_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query."<Br>\n";
			if (!$delete_sell_question_result)
			{
				if ($this->debug_sell) echo $this->sql_query."<Br>\n";
				return false;
			}
		}
		elseif ($classified_id)
		{
			$this->sql_query = "select * from ".$this->sell_table." where classified_id = \"".$classified_id."\"";
			if ($this->debug_sell) echo $this->sql_query."<Br>\n";
			$select_sell_result = $db->Execute($this->sql_query);
			if (!$select_sell_result)
			{
				if ($this->debug_sell) echo $this->sql_query."<Br>\n";
				return false;
			}
			elseif ($select_sell_result->RecordCount() == 1)
			{
				$show_sell_session = $select_sell_result->FetchNextObject();
				$this->sql_query = "delete from ".$this->sell_table." where session = \"".$show_sell_session->SESSION."\"";
				if ($this->debug_sell) echo $this->sql_query."<Br>\n";
				$delete_sell_result = $db->Execute($this->sql_query);
				if (!$delete_sell_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<Br>\n";
					return false;
				}

				//get the images captured so far
				$this->sql_query = "select * from ".$this->sell_images_table." where session = \"".$show_sell_session->SESSION."\"";
				$select_sell_image_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<Br>\n";
				if ($select_sell_image_result->RecordCount() > 0)
				{
					while ($show_image = $select_sell_image_result->FetchNextObject())
					{
						if ($show_image->IMAGE_TYPE == 1)
						{
							//delete url images
							//get image urls to
							$this->sql_query = "select * from ".$this->images_urls_table." where image_id = ".$show_image->IMAGE_ID;
							$get_url_result = $db->Execute($this->sql_query);
							if ($this->debug_sell) echo $this->sql_query."<Br>\n";
							if (!$get_url_result)
							{
								if ($this->debug_sell) echo $this->sql_query."<Br>\n";
								return false;
							}
							elseif ($get_url_result->RecordCount() == 1)
							{
								$show_url = $get_url_result->FetchNextObject();
								if ($show_url->FULL_FILENAME)
									unlink($show_url->FILE_PATH.$show_url->FULL_FILENAME);
								if ($show_url->THUMB_FILENAME)
									unlink($show_url->FILE_PATH.$show_url->THUMB_FILENAME);

								$this->sql_query = "delete from ".$this->images_urls_table." where image_id = ".$show_image->IMAGE_ID;
								$delete_url_result = $db->Execute($this->sql_query);
								if ($this->debug_sell) echo $this->sql_query."<Br>\n";
								if (!$delete_url_result)
								{
									if ($this->debug_sell) echo $this->sql_query."<Br>\n";
									return false;
								}
							}
						}
						elseif ($show_image->IMAGE_TYPE == 2)
						{
							$this->sql_query = "delete from ".$this->images_table." where image_id = ".$show_image->IMAGE_ID;
							$delete_image_result = $db->Execute($this->sql_query);
							if ($this->debug_sell) echo $this->sql_query."<Br>\n";
							if (!$delete_image_result)
							{
								if ($this->debug_sell) echo $this->sql_query."<Br>\n";
								return false;
							}
						}
					}
				}
				//get the images captured so far
				$this->sql_query = "delete from ".$this->sell_images_table." where session = \"".$show_sell_session->SESSION."\"";
				$delete_sell_image_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<Br>\n";
				if (!$delete_sell_image_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<Br>\n";
					return false;
				}

				//get the category questions so far
				$this->sql_query = "delete from ".$this->sell_questions_table." where session = \"".$show_sell_session->SESSION."\"";
				$delete_sell_question_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<Br>\n";
				if (!$delete_sell_question_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<Br>\n";
					return false;
				}
			}
		}
	} //end of funciton remove_sell_session

//####################################################################

	function remove_old_sell_sessions($db)
	{
		$this->sql_query = "select * from ".$this->sell_table." where time_started < ".($this->shifted_time($db) - (2 * 60 * 60));
		$get_old_sell_result = $db->Execute($this->sql_query);
		if ($this->debug_sell)
		{
			echo "<bR>TOP OF REMOVE_OLD_SELL_SESSIONS<br>\n";
			echo $this->sql_query."<bR>\n";
			echo $show_old->CLASSIFIED_ID." is classified_id<br>\n";
		}
		if (!$get_old_sell_result)
		{
			if ($this->debug_sell) echo $this->sql_query."<bR>\n";
			return false;
		}
		elseif ($get_old_sell_result->RecordCount() > 0)
		{
			while ($show_old = $get_old_sell_result->FetchNextObject())
			{
				if ($show_old->CLASSIFIED_ID)
				{
					$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$show_old->CLASSIFIED_ID;
					$check_live_result = $db->Execute($this->sql_query);
					if ($this->debug_sell) echo $this->sql_query."<bR>\n";
					if (!$check_live_result)
					{
						if ($this->debug_sell) echo $this->sql_query."<bR>\n";
						return false;
					}
					elseif ($check_live_result->RecordCount() == 1)
					{
						$show_removed = $check_live_result->FetchNextObject();
						if ($show_removed->LIVE == 0)
						{
							//delete from classifieds extra questions
							$this->sql_query = "delete from ".$this->classified_extra_table." where classified_id = ".$show_old->CLASSIFIED_ID;
							$remove_extra_result = $db->Execute($this->sql_query);
							if ($this->debug_sell) echo $this->sql_query."<bR>\n";
							if (!$remove_extra_result)
							{
								if ($this->debug_sell) echo $this->sql_query."<bR>\n";
								return false;
							}

							//delete url images
							//get image urls to
							$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$show_old->CLASSIFIED_ID;
							$get_url_result = $db->Execute($this->sql_query);
							if ($this->debug_sell) echo $this->sql_query."<bR>\n";
							if (!$get_url_result)
							{
								if ($this->debug_sell) echo $this->sql_query."<bR>\n";
								return false;
							}
							elseif ($get_url_result->RecordCount())
							{
								while ($show_url = $get_url_result->FetchNextObject())
								{
									if ($show_url->FULL_FILENAME)
										unlink($show_url->FILE_PATH.$show_url->FULL_FILENAME);
									if ($show_url->THUMB_FILENAME)
										unlink($show_url->FILE_PATH.$show_url->THUMB_FILENAME);
								}
								$this->sql_query = "delete from ".$this->images_urls_table." where classified_id = ".$show_old->CLASSIFIED_ID;
								$delete_url_result = $db->Execute($this->sql_query);
								if ($this->debug_sell) echo $this->sql_query."<bR>\n";
								if (!$delete_url_result)
								{
									if ($this->debug_sell) echo $this->sql_query."<bR>\n";
									return false;
								}
							}

							//delete db images
							$this->sql_query = "delete from ".$this->images_table." where classified_id = ".$show_old->CLASSIFIED_ID;
							$result = $db->Execute($this->sql_query);
							//must delete old image files also
							if ($this->debug_sell) echo $this->sql_query."<bR>\n";
							if (!$result)
							{
								if ($this->debug_sell) echo $this->sql_query."<bR>\n";
								return false;
							}

							//delete classified data
							$this->sql_query = "delete from ".$this->classifieds_table." where id = ".$show_old->CLASSIFIED_ID;
							$result = $db->Execute($this->sql_query);
							if ($this->debug_sell) echo $this->sql_query."<bR>\n";
							if (!$result)
							{
								if ($this->debug_sell) echo $this->sql_query."<bR>\n";
								return false;
							}
						}
					}
				}
				$this->remove_sell_session($db,$show_old->SESSION);
			}
		}
	} //end of function remove_old_sell_sessions

//####################################################################

	function save_form_variables ($db)
	{
		if ($this->debug_sell)
		{
			echo "TOP OF SAVE_FORM_VARIABLES<br>\n";
			echo $this->classified_variables["start_time"] . ' is the start time.save_form<br>';
			echo $this->classified_variables["end_time"] . ' is the end time.save_form<br>';
		}

		require_once './classes/site_class.php';
		$unique_key = substr(md5(uniqid(rand(),1)), 0,strlen($this->classified_variables["cc_number"]));
		$encrypted_card_num = Site::encrypt($this->classified_variables["cc_number"], $unique_key);

		if ($this->ad_configuration_data->TEXTAREA_WRAP)
			$this->classified_variables["description"] = (urldecode(nl2br($this->classified_variables["description"])));
		else
			$this->classified_variables["description"] = (urldecode($this->classified_variables["description"]));
		$this->classified_variables["classified_title"] = urldecode($this->classified_variables["classified_title"]);

		//dutch auctions cannot have a buy now option
		if($this->classified_variables["auction_type"] == 2) $this->classified_variables["auction_buy_now"] = 0;

		$this->sql_query = "update ".$this->sell_table." set
			classified_length = \"".$this->classified_variables["classified_length"]."\",
			classified_title = \"".urlencode($this->classified_variables["classified_title"])."\",
			description = \"".urlencode($this->classified_variables["description"])."\",
			price = \"".trim(str_replace(",","",$this->classified_variables["price"]))."\",
			currency_type = \"".$this->classified_variables["currency_type"]."\",
			city = \"".$this->classified_variables["city"]."\",
			state = \"".$this->classified_variables["state"]."\",
			country = \"".$this->classified_variables["country"]."\",
			payment_type = \"".$this->classified_variables["payment_type"]."\",
			cc_number = \"$encrypted_card_num\",
			decryption_key = \"$unique_key\",
			cc_exp_year = \"".$this->classified_variables["cc_exp_year"]."\",
			cc_exp_month = \"".$this->classified_variables["cc_exp_month"]."\",
			cvv2_code = \"".$this->classified_variables["cvv2_code"]."\",
			featured_ad = \"".$this->classified_variables["featured_ad"]."\",
			featured_ad_2 = \"".$this->classified_variables["featured_ad_2"]."\",
			featured_ad_3 = \"".$this->classified_variables["featured_ad_3"]."\",
			featured_ad_4 = \"".$this->classified_variables["featured_ad_4"]."\",
			featured_ad_5 = \"".$this->classified_variables["featured_ad_5"]."\",
			attention_getter = \"".$this->classified_variables["attention_getter"]."\",
			attention_getter_choice = \"".$this->classified_variables["attention_getter_choice"]."\",
			bolding = \"".$this->classified_variables["bolding"]."\",
			better_placement = \"".$this->classified_variables["better_placement"]."\",
			subscription_choice = \"".$this->classified_variables["subscription_choice"]."\",
			credit_choice = \"".$this->classified_variables["credit_choice"]."\",
			zip_code = \"".addslashes($this->classified_variables["zip_code"])."\",
			phone_1_option = \"".addslashes($this->classified_variables["phone_1_option"])."\",
			phone_2_option = \"".addslashes($this->classified_variables["phone_2_option"])."\",
			email_option = \"".addslashes($this->classified_variables["email_option"])."\",
			fax_option = \"".addslashes($this->classified_variables["fax_option"])."\",
			expose_email = \"".$this->classified_variables["expose_email"]."\",
			url_link_1 = \"".addslashes($this->classified_variables["url_link_1"])."\",
			url_link_2 = \"".addslashes($this->classified_variables["url_link_2"])."\",
			url_link_3 = \"".addslashes($this->classified_variables["url_link_3"])."\",
			start_time = \"".$this->classified_variables["start_time"]."\",
			end_time = \"".$this->classified_variables["end_time"]."\",
			optional_field_1 = \"".urlencode($this->classified_variables["optional_field_1"])."\",
			optional_field_2 = \"".urlencode($this->classified_variables["optional_field_2"])."\",
			optional_field_3 = \"".urlencode($this->classified_variables["optional_field_3"])."\",
			optional_field_4 = \"".urlencode($this->classified_variables["optional_field_4"])."\",
			optional_field_5 = \"".urlencode($this->classified_variables["optional_field_5"])."\",
			optional_field_6 = \"".urlencode($this->classified_variables["optional_field_6"])."\",
			optional_field_7 = \"".urlencode($this->classified_variables["optional_field_7"])."\",
			optional_field_8 = \"".urlencode($this->classified_variables["optional_field_8"])."\",
			optional_field_9 = \"".urlencode($this->classified_variables["optional_field_9"])."\",
			optional_field_10 = \"".urlencode($this->classified_variables["optional_field_10"])."\",
			optional_field_11 = \"".urlencode($this->classified_variables["optional_field_11"])."\",
			optional_field_12 = \"".urlencode($this->classified_variables["optional_field_12"])."\",
			optional_field_13 = \"".urlencode($this->classified_variables["optional_field_13"])."\",
			optional_field_14 = \"".urlencode($this->classified_variables["optional_field_14"])."\",
			optional_field_15 = \"".urlencode($this->classified_variables["optional_field_15"])."\",
			optional_field_16 = \"".urlencode($this->classified_variables["optional_field_16"])."\",
			optional_field_17 = \"".urlencode($this->classified_variables["optional_field_17"])."\",
			optional_field_18 = \"".urlencode($this->classified_variables["optional_field_18"])."\",
			optional_field_19 = \"".urlencode($this->classified_variables["optional_field_19"])."\",
			optional_field_20 = \"".urlencode($this->classified_variables["optional_field_20"])."\",
			discount_code = \"".addslashes($this->classified_variables["discount_code"])."\",
			mapping_address = \"".addslashes($this->classified_variables["mapping_address"])."\",
			mapping_city = \"".addslashes($this->classified_variables["mapping_city"])."\",
			mapping_state = \"".addslashes($this->classified_variables["mapping_state"])."\",
			mapping_country = \"".addslashes($this->classified_variables["mapping_country"])."\",
			mapping_zip = \"".addslashes($this->classified_variables["mapping_zip"])."\",
			paypal_id = \"".addslashes($this->classified_variables["paypal_id"])."\",
			auction_type = \"".addslashes($this->classified_variables["auction_type"])."\",
			auction_quantity = \"".addslashes($this->classified_variables["auction_quantity"])."\",
			auction_minimum = \"".addslashes($this->classified_variables["auction_minimum"])."\",
			auction_reserve = \"".addslashes($this->classified_variables["auction_reserve"])."\",
			auction_buy_now = \"".addslashes($this->classified_variables["auction_buy_now"])."\",
			payment_options = \"".addslashes($this->classified_variables["payment_options"])."\"
			where session = \"".$this->session_id."\"";

		$save_variable_result = $db->Execute($this->sql_query);
		if ($this->debug_sell) echo $this->sql_query."<br>\n";
		if (!$save_variable_result)
		{
			if ($this->debug_sell)
			{
				echo $db->ErrorMsg()."<bR>\n";
				echo $this->sql_query."<br>\n";
			}
			return false;
		}

		//STOREFRONT CODE
		if($this->classified_variables["storefront_category"])
		{
			$this->sql_query = "update ".$this->sell_table." set
			storefront_category = \"".$this->classified_variables["storefront_category"]."\"
			where session = \"".$this->session_id."\"";
			$save_variable_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query."<br>\n";
			if (!$save_variable_result)
			{
				if ($this->debug_sell)
				{
					echo $db->ErrorMsg()."<br>\n";
					echo $this->sql_query."<br>\n";
				}
				return false;
			}
		}
		//STOREFRONT CODE

		//delete current questions values in db
		$this->sql_query = "delete from ".$this->sell_questions_table." where session = \"".$this->session_id."\"";
		$delete_sell_question_result = $db->Execute($this->sql_query);
		if ($this->debug_sell) echo $this->sql_query."<br>\n";
		if (!$delete_sell_question_result)
		{
			if ($this->debug_sell) echo $this->sql_query."<br>\n";
			return false;
		}

		if ((count($this->classified_variables["question_value"]) > 0) && (is_array($this->classified_variables["question_value"])))
		{
			reset ($this->classified_variables["question_value"]);
			while (list($key,$value) = each($this->classified_variables["question_value"]))
			//foreach ($this->classified_variables["question_value"] as $key => $value)
			{
				 if ((strlen(trim($this->classified_variables["question_value"][$key])) > 0) || (strlen(trim($this->classified_variables["question_value_other"][$key])) > 0))
				 {
				 	if ($this->ad_configuration_data->TEXTAREA_WRAP)
				 	{
						$this->sql_query = "insert into ".$this->sell_questions_table."
							(session,type_id,question_value,question_value_other,display_order)
							values
							(\"".$this->session_id."\",".$key.",\"".urlencode(nl2br($this->classified_variables["question_value"][$key]))."\",
							\"".urlencode($this->classified_variables["question_value_other"][$key])."\",\"".$this->classified_variables["question_display_order"][$key]."\")";
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						$insert_sell_question_result = $db->Execute($this->sql_query);
					}
					else
					{
						$this->sql_query = "insert into ".$this->sell_questions_table."
							(session,type_id,question_value,question_value_other,display_order)
							values
							(\"".$this->session_id."\",".$key.",\"".urlencode($this->classified_variables["question_value"][$key])."\",
							\"".urlencode($this->classified_variables["question_value_other"][$key])."\",\"".$this->classified_variables["question_display_order"][$key]."\")";
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						$insert_sell_question_result = $db->Execute($this->sql_query);
					}
					if (!$insert_sell_question_result)
					{
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						return false;
					}
				} // end of if
			} // end of while
		} //end of if (count($this->classified_variables["question_value"] > 0)

		if ((count($this->classified_variables["group_value"]) > 0) && (is_array($this->classified_variables["group_value"])))
		{
			reset ($this->classified_variables["group_value"]);
			//while (list($key,$value) = each($this->classified_variables["question_value"]))
			foreach ($this->classified_variables["group_value"] as $key => $value)
			{
				 if ((strlen(trim($this->classified_variables["group_value"][$key])) > 0) || (strlen(trim($this->classified_variables["group_value_other"][$key])) > 0))
				 {
					$this->sql_query = "insert into ".$this->sell_questions_table."
						(session,type_id,question_value,question_value_other,group_id,display_order)
						values
						(\"".$this->session_id."\",".$key.",\"".urlencode($this->classified_variables["group_value"][$key])."\",
						\"".urlencode($this->classified_variables["group_value_other"][$key])."\",".$this->users_group.",\"".$this->classified_variables["group_value_order"][$key]."\")";
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					$insert_sell_question_result = $db->Execute($this->sql_query);
					if (!$insert_sell_question_result)
					{
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						return false;
					}
				} // end of if
			} // end of while
		} //end of if (count($this->classified_variables["question_value"] > 0)
	} //end of function save_form_variables

//####################################################################

	function check_if_category($db,$category=0)
	{
		if ($category)
		{
			//check to see if this number is even a category
			$this->sql_query = "select * from ".$this->categories_table." where category_id = ".$category;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = urldecode($this->messages[57]);
				return false;
			}
			else
			{
				if ($result->RecordCount() == 1)
					return true;
				else
				{
					//$this->body .=$this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[57]);
					return false;
				}
			}
		}
		else
		{
			$this->error_message = urldecode($this->messages[57]);
			return false;
		}
	} //end of function check_if_category

//#####################################################################

	function choose_category($db,$parent_category=0)
	{
		$this->page_id = 8;
		$this->get_text($db);
		if ($this->classified_user_id)
		{
			if ($parent_category)
			{
				$parent_name = $this->get_category_name($db,$parent_category);
				if ($parent_name)
				{
					//echo $this->sql_query." is the query<br>\n";
					if ($this->configuration_data['order_choose_category_by_alpha'])
					{
						$this->sql_query = "select ".$this->categories_table.".category_id,
							".$this->categories_languages_table.".category_name
							from ".$this->categories_table.",".$this->categories_languages_table." where
							parent_id = ".$parent_category." and
							".$this->categories_table.".category_id = ".$this->categories_languages_table.".category_id and
							".$this->categories_languages_table.".language_id = ".$this->language_id."
							order by ".$this->categories_languages_table.".category_name";
					}
					else
					{
						$this->sql_query = "select ".$this->categories_table.".category_id,
							".$this->categories_languages_table.".category_name
							from ".$this->categories_table.",".$this->categories_languages_table." where
							parent_id = ".$parent_category." and
							".$this->categories_table.".category_id = ".$this->categories_languages_table.".category_id and
							".$this->categories_languages_table.".language_id = ".$this->language_id."
							order by ".$this->categories_table.".display_order,".$this->categories_languages_table.".category_name";
					}
					$sub_result = $db->Execute($this->sql_query);
					if (!$sub_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = urldecode($this->messages[57]);
						return false;
					}
					elseif ($sub_result->RecordCount() > 0)
					{
						switch ($this->configuration_data['sell_category_column_count'])
						{
							case 0: $column_width = "100%"; break;
							case 1: $column_width = "100%"; break;
							case 2: $column_width = "50%"; break;
							case 3: $column_width = "33%"; break;
							case 4: $column_width = "25%"; break;
							case 5: $column_width = "20%"; break;
							default: $column_width = "100%"; break;
						} //end of switch

						if (($this->configuration_data['sell_category_column_count'] == 0) || (!$this->configuration_data['sell_category_column_count']))
							$colspan = 1;
						else
							$colspan = $this->configuration_data['sell_category_column_count'];

						$number_of_sub_cats = $sub_result->RecordCount();
						//do this if there are 1 or more subcategory to choose from
						$this->body .=  "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
						$this->body .=  "<tr class=place_ad_section_title>\n\t<td>\n\t".urldecode($this->messages[606]);
						$this->body .=  "<tr class=page_title>\n\t<td>\n\t".urldecode($this->messages[83]);
						$this->display_help_link(85);
						$this->body .=  "\n\t</td>\n</tr>\n";
						$this->body .=  "<tr class=page_description>\n\t
							<td>\n\t".urldecode($this->messages[79])."
							<font class=place_an_ad_choose_category_bold>".
							$parent_name->CATEGORY_NAME."</font> ".urldecode($this->messages[77]).
							$number_of_sub_cats.urldecode($this->messages[80])." \n\t</td>\n</tr>\n";
						$this->body .=  "<tr>\n\t<td align=center>\n\t<table cellpadding=3 cellspacing=1 border=0 width=100%>\n\t";
						$this->body .=  "<tr class=page_description>\n\t\t<td colspan=".$colspan.">\n\t\t".urldecode($this->messages[78])."</font>\n\t\t</td>\n\t</tr>\n\t";
						 while ($show_sub_cats = $sub_result->FetchNextObject())
						 {
							//display the subcategories of this parent_category
							//$category_name = $this->get_category_name($db,$show_sub_cats->CATEGORY_ID);
							$this->body .=  "<tr class=category_links>\n\t\n\t\t";
							$this->body .= "<td width=".$column_width.">\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=".$show_sub_cats->CATEGORY_ID."&c=category&set_cat=1  class=category_links>".
							urldecode(stripslashes($show_sub_cats->CATEGORY_NAME))."</a>\n\t\t</td>\n\t";
							if ($this->configuration_data['sell_category_column_count'] > 1)
							{
								if ($show_sub_cats = $sub_result->FetchNextObject())
								{
									$this->body .= "<td width=".$column_width.">\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=".$show_sub_cats->CATEGORY_ID."&c=category&set_cat=1  class=category_links>".
									urldecode(stripslashes($show_sub_cats->CATEGORY_NAME))."</a>\n\t\t</td>\n\t";
								}
								else
								{
									$this->body .="<td  width=".$column_width.">&nbsp;</td>";
								}
								if ($this->configuration_data['sell_category_column_count'] > 2)
								{
									if ($show_sub_cats = $sub_result->FetchNextObject())
									{
										$this->body .= "<td width=".$column_width.">\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=".$show_sub_cats->CATEGORY_ID."&c=category&set_cat=1  class=category_links>".
										urldecode(stripslashes($show_sub_cats->CATEGORY_NAME))."</a>\n\t\t</td>\n\t";
									}
									else
									{
										$this->body .="<td  width=".$column_width.">&nbsp;</td>";
									}
									if ($this->configuration_data['sell_category_column_count'] > 3)
									{
										if ($show_sub_cats = $sub_result->FetchNextObject())
										{
											$this->body .= "<td width=".$column_width.">\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=".$show_sub_cats->CATEGORY_ID."&c=category&set_cat=1  class=category_links>".
											urldecode(stripslashes($show_sub_cats->CATEGORY_NAME))."</a>\n\t\t</td>\n\t";
										}
										else
										{
											$this->body .="<td  width=".$column_width.">&nbsp;</td>";
										}

										if ($this->configuration_data['sell_category_column_count'] > 4)
										{
											if ($show_sub_cats = $sub_result->FetchNextObject())
											{
												$this->body .= "<td width=".$column_width.">\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=".$show_sub_cats->CATEGORY_ID."&c=category&set_cat=1  class=category_links>".
												urldecode(stripslashes($show_sub_cats->CATEGORY_NAME))."</a>\n\t\t</td>\n\t";
											}
											else
											{
												$this->body .="<td  width=".$column_width.">&nbsp;</td>";
											}
										}
									}
								}
							}

							$this->body .= "</tr>\n\t";
						}
						//need to display this category as a choice here
						if (!$this->configuration_data['place_ads_only_in_terminal_categories'])
						{
							$this->body .=  "<tr class=place_an_ad_choose_category_bold>\n\t\t<td colspan=".$colspan.">\n\t\t".urldecode($this->messages[82])."
								<a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=".$parent_category."&c=terminal&set_cat=1 class=place_an_ad_choose_category_bold>".$parent_name->CATEGORY_NAME."</a> ".urldecode($this->messages[31])."\n\t\t</td>\n\t</tr>\n\t";
						}

						$this->body .=  "\n\t</table>\n\t</td>\n</tr>\n";
						$this->body .=  "<tr class=end_sell_process_link>\n\t<td>\n\t<br><br><a href=".trim($this->configuration_data['classifieds_url'])."?a=98 class=end_sell_process_link>".urldecode($this->messages[74])."</a>\n\t</td>\n</tr>\n";
						$this->body .=  "</table>\n";
						$this->display_page($db);
						return true;
					}
					else
					{
						//this is the terminal category
						//there are no sub categories underneath it
						//check this is really a category
						if ($this->check_if_category($db,$parent_category))
						{
							//this is a true category id make it the terminal one
							if (!$this->set_terminal_category($db,$parent_category))
							{
								$this->error_message = urldecode($this->messages[57]);
								return false;
							}
							else
							{
								$this->get_this_price_plan($db);
								if (!$this->classified_details_collected || $_REQUEST["set_cat"])
								{
									$this->display_classified_detail_form($db);
									return true;
								}
								elseif (!$this->classified_images_collected)
								{
									$this->display_classified_image_form($db);
									return true;
								}
								else
								{
									$this->classified_approval_display($db);
									return true;
								}
							}
						}
						else
						{
							$this->error_message = urldecode($this->messages[57]);
							return false;
						}
					}
					return true;
				}
				else
				{
					//$this->body .=  $this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[57]);
					return false;
				}
			}
			else
			{
				//choose the main category
				if ($this->configuration_data['order_choose_category_by_alpha'])
				{
					$this->sql_query = "select ".$this->categories_table.".category_id,
						".$this->categories_languages_table.".category_name
						from ".$this->categories_table.",".$this->categories_languages_table." where
						parent_id = 0 and
						".$this->categories_table.".category_id = ".$this->categories_languages_table.".category_id and
						".$this->categories_languages_table.".language_id = ".$this->language_id;
					switch($this->classified_variables['sell_type'])
					{
						case 1:
							$this->sql_query .= " AND (".$this->categories_table.".listing_types_allowed = 0 OR ".$this->categories_table.".listing_types_allowed = 1)";
							break;
						case 2:
							$this->sql_query .= " AND (".$this->categories_table.".listing_types_allowed = 0 OR ".$this->categories_table.".listing_types_allowed = 2)";
							break;
						default:
							$this->sql_query .= " AND ".$this->categories_table.".listing_types_allowed = 0";
							break;
					}
					$this->sql_query .= " order by ".$this->categories_languages_table.".category_name";
				}
				else
				{
					$this->sql_query = "select ".$this->categories_table.".category_id,
						".$this->categories_languages_table.".category_name
						from ".$this->categories_table.",".$this->categories_languages_table." where
						parent_id = 0 and
						".$this->categories_table.".category_id = ".$this->categories_languages_table.".category_id and
						".$this->categories_languages_table.".language_id = ".$this->language_id;
					switch($this->classified_variables['sell_type'])
					{
						case 1:
							$this->sql_query .= " AND (".$this->categories_table.".listing_types_allowed = 0 OR ".$this->categories_table.".listing_types_allowed = 1)";
							break;
						case 2:
							$this->sql_query .= " AND (".$this->categories_table.".listing_types_allowed = 0 OR ".$this->categories_table.".listing_types_allowed = 2)";
							break;
						default:
							$this->sql_query .= " AND ".$this->categories_table.".listing_types_allowed = 0";
							break;
					}
					$this->sql_query .= " order by ".$this->categories_table.".display_order,".$this->categories_languages_table.".category_name";
				}

				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					echo $this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[57]);
					return false;
				}

				switch ($this->configuration_data['sell_category_column_count'])
				{
					case 0: $column_width = "100%"; break;
					case 1: $column_width = "100%"; break;
					case 2: $column_width = "50%"; break;
					case 3: $column_width = "33%"; break;
					case 4: $column_width = "25%"; break;
					case 5: $column_width = "20%"; break;
					default: $column_width = "100%"; break;
				} //end of switch

				$this->body .=  "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
				$this->body .=  "<tr class=place_ad_section_title>\n\t<td>\n\t".urldecode($this->messages[606]);
				$this->body .=  "<tr class=page_title>\n\t<td>\n\t".urldecode($this->messages[83]).$this->display_help_link(84)."\n\t</td>\n</tr>\n";
				$this->body .=  "<tr class=page_description>\n\t<td>\n\t".urldecode($this->messages[76])."\n\t</td>\n</tr>\n";
				$this->body .= "<tr><td><table width=100%>";
				if ($result->RecordCount() > 0)
				{
					while ($show = $result->FetchNextObject())
					{
						//show all the categories in the option list
						//$category_name = $this->get_category_name($db,$show->CATEGORY_ID);
						$this->body .=  "<tr class=category_links>\n\t";
						$this->body .= "<td width=".$column_width.">\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=".$show->CATEGORY_ID."&set_cat=1 class=category_links>".urldecode(stripslashes($show->CATEGORY_NAME))."</a>\n\t</td>\n";
						if ($this->configuration_data['sell_category_column_count'] > 1)
						{
							if ($show = $result->FetchNextObject())
							{
								$this->body .= "<td width=".$column_width.">\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=".$show->CATEGORY_ID."&set_cat=1 class=category_links>".urldecode(stripslashes($show->CATEGORY_NAME))."</a>\n\t</td>\n";
							}
							else
							{
								$this->body .="<td  width=".$column_width.">&nbsp;</td>";
							}
							if ($this->configuration_data['sell_category_column_count'] > 2)
							{
								if ($show = $result->FetchNextObject())
								{
									$this->body .= "<td width=".$column_width.">\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=".$show->CATEGORY_ID."&set_cat=1 class=category_links>".urldecode(stripslashes($show->CATEGORY_NAME))."</a>\n\t</td>\n";
								}
								else
								{
									$this->body .="<td  width=".$column_width.">&nbsp;</td>";
								}
								if ($this->configuration_data['sell_category_column_count'] > 3)
								{
									if ($show = $result->FetchNextObject())
									{
										$this->body .= "<td width=".$column_width.">\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=".$show->CATEGORY_ID."&set_cat=1 class=category_links>".urldecode(stripslashes($show->CATEGORY_NAME))."</a>\n\t</td>\n";
									}
									else
									{
										$this->body .="<td  width=".$column_width.">&nbsp;</td>";
									}

									if ($this->configuration_data['sell_category_column_count'] > 4)
									{
										if ($show = $result->FetchNextObject())
										{
											$this->body .= "<td width=".$column_width.">\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=".$show->CATEGORY_ID."&set_cat=1 class=category_links>".urldecode(stripslashes($show->CATEGORY_NAME))."</a>\n\t</td>\n";
										}
										else
										{
											$this->body .="<td  width=".$column_width.">&nbsp;</td>";
										}
									}
								}
							}
						}
						$this->body .= "</tr>\n";
					} //end of while
				}
				else
				{
					$this->body .=  "<tr class=error_message>\n\t<td width=".$column_width.">\n\t".urldecode($this->messages[58])."\n\t</td>\n</tr>\n";
				}
				$this->body .= "</table></td></tr>";
				$this->body .=  "<tr class=end_sell_process_link>\n\t<td>\n\t<br><br><a href=".trim($this->configuration_data['classifieds_url'])."?a=98 class=end_sell_process_link>".urldecode($this->messages[74])."</a>\n\t</td>\n</tr>\n";
				$this->body .=  "</table>\n";
				$this->display_page($db);
				return true;
			}

		}
		else
		{
			//no user_id
			return false;
		}
	} //end of function choose_category

//#####################################################################

	function insert_classified ($db)
	{
		if ($this->debug_sell) echo "<br>TOP OF INSERT_CLASSIFIED<bR>\n";
		$current_time = $this->shifted_time($db);
		if ($this->classified_user_id)
		{
			$user_data = $this->get_user_data($db,$this->classified_user_id);
			$this->get_ad_configuration($db);
			$listing_starts = $this->shifted_time($db);

			$user_data = $this->get_user_data($db,$this->classified_user_id);
			//set this to minutes by using the first line
			//$listing_ends =  $this->DateAdd("d",$listing_starts,$this->classified_variables["classified_length"]);

			if ($this->classified_variables["sell_type"] == 1)
			{
				//this is a classified ad
				//set the expiration of the ad to number of days from now
				//will use the duration to set the life of the ad once the ad is approved by admin or credit card purchase
				$listing_ends =  $this->DateAdd("d",$listing_starts,$this->configuration_data['expire_unfinished_period']);

				//if all ads are free make the ad 'live'
				if  ($this->configuration_data['admin_approves_all_ads'])
				{
					$live = 2;
					$listing_ends =  $this->DateAdd("d",$listing_starts,$this->configuration_data['expire_unfinished_period']);
				}
				elseif (($this->configuration_data['all_ads_are_free']) && (!$this->configuration_data['admin_approves_all_ads']))
				{
					$live = 1;
					$listing_ends =  $this->DateAdd("d",$listing_starts,$this->classified_variables["classified_length"]);
				}
				else
				{
					$live = 0;
					//set the expiration of the ad to number of days from now
					//will use the duration to set the life of the ad once the ad is approved by admin or credit card purchase
					$listing_ends =  $this->DateAdd("d",$listing_starts,$this->configuration_data['expire_unfinished_period']);
				}
			}
			elseif ($this->classified_variables["sell_type"] == 2)
			{
				//Finding auction_ends based on start_time, end_time and classified_length
				if ($this->debug_sell)
				{
					echo $this->classified_variables["end_time"]." is incoming classified_variables[end_time]<bR>\n";
					echo $this->classified_variables["start_time"]." is incoming classified_variables[start_time]<bR>\n";
					echo $this->classified_variables["classified_length"]." is incoming classified_variables[classified_length]<bR>\n";
				}
				if($this->classified_variables["end_time"] == 0)
				{
					if($this->classified_variables["start_time"] == 0)
					{
						$listing_ends = $this->DateAdd("d",$current_time,$this->classified_variables["classified_length"]);
					}
					else
					{
						$listing_ends = $this->DateAdd("d",$this->classified_variables["start_time"],$this->classified_variables["classified_length"]);
					}
				}
				else
				{
					$listing_ends = $this->classified_variables["end_time"];
				}

				$listing_starts = $current_time;

				if  ($this->configuration_data['admin_approves_all_ads'])
				{
					$live = 2;
				}
				elseif (($this->configuration_data['all_ads_are_free']) && (!$this->configuration_data['admin_approves_all_ads']))
				{
					$live = 1;
				}
				else
				{
					$live = 0;
				}

			}

			if ($this->debug_sell)
			{
				echo $listing_ends." is listing_ends<bR>\n";
				echo $listing_starts." is listing_starts<BR>\n";
				echo $live." is live<BR>\n";
			}

			if ($this->classified_variables["currency_type"])
			{
				$this->sql_query = "select precurrency,postcurrency from ".$this->currency_types_table." where type_id = ".$this->classified_variables["currency_type"];
				$currency_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<br>\n";
				if (!$currency_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					$this->error_message = urldecode($this->messages[57]);
					return false;
				}
				elseif ($currency_result->RecordCount() == 1)
				{
					$show_currency = $currency_result->FetchNextObject();
				}
			}

			if (strlen(trim($this->classified_variables["email_option"])) == 0)
			{
				//get the sellers default email address
				$this->classified_variables["email_option"] = $user_data->EMAIL;
			}

			if ($this->classified_id)
			{
				//THIS IS AN UPDATE OF PREVIOUSLY ENTERED DATA

				$this->sql_query = "UPDATE ".$this->classifieds_table." set
					title = \"".urlencode(urldecode($this->classified_variables["classified_title"]))."\",
					date = \"".$listing_starts."\",
					description = \"".urlencode(urldecode($this->classified_variables["description"]))."\",
					price = \"".$this->classified_variables["price"]."\",
					precurrency = \"".urlencode($show_currency->PRECURRENCY)."\",
					postcurrency = \"".urlencode($show_currency->POSTCURRENCY)."\",
					image = \"".count($this->images_captured)."\",
					category = ".$this->terminal_category.",
					duration = ".$this->classified_variables["classified_length"].",
					location_city = \"".urlencode($this->classified_variables["city"])."\",
					location_state = \"".$this->classified_variables["state"]."\",
					location_country = \"".$this->classified_variables["country"]."\",
					location_zip = \"".urlencode($this->classified_variables["zip_code"])."\",
					email = \"".$this->classified_variables["email_option"]."\",
					phone = \"".urlencode($this->classified_variables["phone_1_option"])."\",
					phone2 = \"".urlencode($this->classified_variables["phone_2_option"])."\",
					fax = \"".urlencode($this->classified_variables["fax"])."\",
					expose_email = \"".$this->classified_variables["expose_email"]."\",
					ends = \"".$listing_ends."\",
					url_link_1 = \"".addslashes($this->classified_variables["url_link_1"])."\",
					url_link_2 = \"".addslashes($this->classified_variables["url_link_2"])."\",
					url_link_3 = \"".addslashes($this->classified_variables["url_link_3"])."\",
					subscription_choice = \"".$this->classified_variables["subscription_choice"]."\",
					optional_field_1 = \"".urlencode($this->classified_variables["optional_field_1"])."\",
					optional_field_2 = \"".urlencode($this->classified_variables["optional_field_2"])."\",
					optional_field_3 = \"".urlencode($this->classified_variables["optional_field_3"])."\",
					optional_field_4 = \"".urlencode($this->classified_variables["optional_field_4"])."\",
					optional_field_5 = \"".urlencode($this->classified_variables["optional_field_5"])."\",
					optional_field_6 = \"".urlencode($this->classified_variables["optional_field_6"])."\",
					optional_field_7 = \"".urlencode($this->classified_variables["optional_field_7"])."\",
					optional_field_8 = \"".urlencode($this->classified_variables["optional_field_8"])."\",
					optional_field_9 = \"".urlencode($this->classified_variables["optional_field_9"])."\",
					optional_field_10 = \"".urlencode($this->classified_variables["optional_field_10"])."\",
					optional_field_11 = \"".urlencode($this->classified_variables["optional_field_11"])."\",
					optional_field_12 = \"".urlencode($this->classified_variables["optional_field_12"])."\",
					optional_field_13 = \"".urlencode($this->classified_variables["optional_field_13"])."\",
					optional_field_14 = \"".urlencode($this->classified_variables["optional_field_14"])."\",
					optional_field_15 = \"".urlencode($this->classified_variables["optional_field_15"])."\",
					optional_field_16 = \"".urlencode($this->classified_variables["optional_field_16"])."\",
					optional_field_17 = \"".urlencode($this->classified_variables["optional_field_17"])."\",
					optional_field_18 = \"".urlencode($this->classified_variables["optional_field_18"])."\",
					optional_field_19 = \"".urlencode($this->classified_variables["optional_field_19"])."\",
					optional_field_20 = \"".urlencode($this->classified_variables["optional_field_20"])."\",
					mapping_address = \"".addslashes($this->classified_variables["mapping_address"])."\",
					mapping_city = \"".addslashes($this->classified_variables["mapping_city"])."\",
					mapping_state = \"".addslashes($this->classified_variables["mapping_state"])."\",
					mapping_country = \"".addslashes($this->classified_variables["mapping_country"])."\",
					mapping_zip = \"".addslashes($this->classified_variables["mapping_zip"])."\",
					paypal_id = \"".addslashes($this->classified_variables["paypal_id"])."\",
					auction_type = \"".addslashes($this->classified_variables["auction_type"])."\",
					quantity = \"".addslashes($this->classified_variables["auction_quantity"])."\",
					starting_bid = \"".addslashes($this->classified_variables["auction_minimum"])."\",
					minimum_bid = \"".addslashes($this->classified_variables["auction_minimum"])."\",
					reserve_price = \"".addslashes($this->classified_variables["auction_reserve"])."\",
					buy_now = \"".addslashes($this->classified_variables["auction_buy_now"])."\",
					payment_options = \"".addslashes($this->classified_variables["payment_options"])."\",
					start_time = \"".$listing_starts."\",
					end_time = \"".$listing_ends."\",
					buy_now_only = \"".$this->classified_variables["buy_now_only"]."\",
					item_type = \"".$this->classified_variables["sell_type"]."\"
					where id = ".$this->classified_id;
				$result = $db->Execute($this->sql_query);

                //echo $this->sql_query." is the query<br>\n";
				if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
				if (!$result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[57]);
					return false;
				}

				//STOREFRONT CODE
				if($this->classified_variables["storefront_category"])
				{
					$this->sql_query = "update ".$this->classifieds_table." set
					storefront_category = \"".$this->classified_variables["storefront_category"]."\"
					where id = \"".$this->classified_id."\"";
					$save_variable_result = $db->Execute($this->sql_query);
					if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
					if (!$save_variable_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = urldecode($this->messages[57]);
						return false;
					}
				}
				//STOREFRONT CODE

				$this->delete_current_category_questions($db);
				if (!$this->insert_category_questions($db))
					return false;

				if (!$this->insert_classified_images($db))
					return false;

				return true;
			}
			else
			{
				//THIS IS NOT AN UPDATE OF A PREVIOUSLY TRIED CLASSIFIED AD
				//set this to minutes by using the first line
				//$listing_ends =  $this->DateAdd("d",$listing_starts,$this->classified_variables["classified_length"]);

				//$this->body .=$listing_ends." is $listing_ends<br>\n";
				//$this->body .=$listing_starts." is listing_starts<br>\n";

				$this->sql_query = "INSERT INTO ".$this->classifieds_table."
					(seller,live,title,date,description,precurrency,price,postcurrency,image,category,
					duration,location_city,location_state,location_country,location_zip,ends,business_type,
					optional_field_1,optional_field_2,optional_field_3,optional_field_4,
					optional_field_5,optional_field_6,optional_field_7,optional_field_8,
					optional_field_9,optional_field_10,
					optional_field_11,optional_field_12,optional_field_13,optional_field_14,
					optional_field_15,optional_field_16,optional_field_17,optional_field_18,
					optional_field_19,optional_field_20,email,expose_email,phone,phone2,fax,filter_id,
					mapping_address,mapping_city,mapping_state,mapping_country,mapping_zip,subscription_choice,
					url_link_1,url_link_2,url_link_3,price_plan_id,paypal_id,auction_type,
					quantity,starting_bid,minimum_bid,reserve_price,buy_now,payment_options,item_type,
					end_time,start_time,buy_now_only,final_fee,longitude,latitude)
					VALUES
					(\"".$this->classified_user_id."\",
					\"".$live."\",
					\"".urlencode(urldecode($this->classified_variables["classified_title"]))."\",
					\"".$listing_starts."\",
					\"".urlencode(urldecode($this->classified_variables["description"]))."\",
					\"".urlencode($show_currency->PRECURRENCY)."\",
					\"".$this->classified_variables["price"]."\",
					\"".urlencode($show_currency->POSTCURRENCY)."\",
					\"".count($this->images_captured)."\",
					".$this->terminal_category.",
					".$this->classified_variables["classified_length"].",
					\"".$this->classified_variables["city"]."\",
					\"".$this->classified_variables["state"]."\",
					\"".$this->classified_variables["country"]."\",
					\"".urlencode($this->classified_variables["zip_code"])."\",
					\"".$listing_ends."\",
					\"".$user_data->BUSINESS_TYPE."\",
					\"".urlencode($this->classified_variables["optional_field_1"])."\",
					\"".urlencode($this->classified_variables["optional_field_2"])."\",
					\"".urlencode($this->classified_variables["optional_field_3"])."\",
					\"".urlencode($this->classified_variables["optional_field_4"])."\",
					\"".urlencode($this->classified_variables["optional_field_5"])."\",
					\"".urlencode($this->classified_variables["optional_field_6"])."\",
					\"".urlencode($this->classified_variables["optional_field_7"])."\",
					\"".urlencode($this->classified_variables["optional_field_8"])."\",
					\"".urlencode($this->classified_variables["optional_field_9"])."\",
					\"".urlencode($this->classified_variables["optional_field_10"])."\",
					\"".urlencode($this->classified_variables["optional_field_11"])."\",
					\"".urlencode($this->classified_variables["optional_field_12"])."\",
					\"".urlencode($this->classified_variables["optional_field_13"])."\",
					\"".urlencode($this->classified_variables["optional_field_14"])."\",
					\"".urlencode($this->classified_variables["optional_field_15"])."\",
					\"".urlencode($this->classified_variables["optional_field_16"])."\",
					\"".urlencode($this->classified_variables["optional_field_17"])."\",
					\"".urlencode($this->classified_variables["optional_field_18"])."\",
					\"".urlencode($this->classified_variables["optional_field_19"])."\",
					\"".urlencode($this->classified_variables["optional_field_20"])."\",
					\"".$this->classified_variables["email_option"]."\",
					\"".$this->classified_variables["expose_email"]."\",
					\"".$this->classified_variables["phone_1_option"]."\",
					\"".$this->classified_variables["phone_2_option"]."\",
					\"".$this->classified_variables["fax_option"]."\",
					\"".$this->filter_id."\",
					\"".$this->classified_variables["mapping_address"]."\",
					\"".$this->classified_variables["mapping_city"]."\",
					\"".$this->classified_variables["mapping_state"]."\",
					\"".$this->classified_variables["mapping_country"]."\",
					\"".$this->classified_variables["mapping_zip"]."\",
					\"".$this->classified_variables["subscription_choice"]."\",
					\"".addslashes($this->classified_variables["url_link_1"])."\",
					\"".addslashes($this->classified_variables["url_link_2"])."\",
					\"".addslashes($this->classified_variables["url_link_3"])."\",
					\"".$this->users_price_plan."\",
					\"".$this->classified_variables["paypal_id"]."\",
					\"".addslashes($this->classified_variables["auction_type"])."\",
					\"".addslashes($this->classified_variables["auction_quantity"])."\",
					\"".addslashes($this->classified_variables["auction_minimum"])."\",
					\"".addslashes($this->classified_variables["auction_minimum"])."\",
					\"".addslashes($this->classified_variables["auction_reserve"])."\",
					\"".addslashes($this->classified_variables["auction_buy_now"])."\",
					\"".addslashes($this->classified_variables["payment_options"])."\",
					\"".$this->classified_variables["sell_type"]."\",
					\"".addslashes($listing_ends)."\",
					\"".$listing_starts."\",
					\"".$this->classified_variables["buy_now_only"]."\",
					\"".$this->final_fee."\",
                    \"".$this->user_data->LONGITUDE."\",
                    \"".$this->user_data->LATITUDE."\"
                    )";
                //echo $this->sql_query." is the query<br>\n";
				$result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
				if (!$result)
				{
					if ($this->debug_sell)
					{
						echo $this->sql_query." is the query<br>\n";
						echo $db->ErrorMsg()." is the error<br>\n";
					}
					$this->error_message = urldecode($this->messages[57]);
					return false;
				}
				else
				{
					$this->update_classified_id($db,$db->Insert_ID());

					//STOREFRONT CODE
					if($this->classified_variables["storefront_category"])
					{
						$this->sql_query = "update ".$this->classifieds_table." set
						storefront_category = \"".$this->classified_variables["storefront_category"]."\"
						where id = \"".$db->Insert_ID()."\"";
						$save_variable_result = $db->Execute($this->sql_query);
						if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
					}
					//STOREFRONT CODE

					if (!$this->insert_category_questions($db))
					{
						//cannot insert category questions
						//so remove what was entered so far
						$this->remove_classified_insert_failed($db);
						return false;
					}

					if (!$this->insert_classified_images($db))
					{
						$this->remove_classified_insert_failed($db);
						$this->delete_current_category_questions($db);
						return false;
					}
					return true;
				}
			}
		}
		else
		{
			//no user id
			return false;
		}

	} //end of function insert_classified

//#####################################################################

	function remove_classified_insert_failed($db)
	{
		$this->sql_query = "delete from ".$this->classifieds_table." where
			id = ".$this->classified_id;
		$delete_extra_result = $db->Execute($this->sql_query);
		if (!$delete_extra_result)
		{
			//$this->body .=$this->sql_query." is the query<br>\n";
			$this->error_message = urldecode($this->messages[57]);
			return false;
		}
		return true;
	} //end of function remove_classified_inser_failed

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_current_category_questions($db)
	{
		$this->sql_query = "delete from ".$this->classified_extra_table." where
			classified_id = ".$this->classified_id;
		$delete_extra_result = $db->Execute($this->sql_query);
		//$this->body .=$this->sql_query."<br>\n";
		if (!$delete_extra_result)
		{
			//$this->body .=$this->sql_query." is the query<br>\n";
			$this->error_message = urldecode($this->messages[57]);
			return false;
		}
		return true;
	} //end of function delete_current_category_questions

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_category_questions($db)
	{
		$num_questions = count($this->classified_variables["question_value"]);
		//echo $num_questions." is the num of questions remembered<Br>\n";
		if ($num_questions > 0 )
		{
			while (list($key,$value) = each($this->classified_variables["question_value"]))
			{
				 if ((strlen(trim($this->classified_variables["question_value"][$key])) > 0) || (strlen(trim($this->classified_variables["question_value_other"][$key])) > 0))
				 {
					//there is a value in this questions so put it in the db
					$this->sql_query = "SELECT * FROM ".$this->classified_sell_questions_table." WHERE question_id = \"".$key."\"";
					$question_result = $db->Execute($this->sql_query);
					//echo $this->sql_query." is the query<br>\n";
					if (!$question_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = urldecode($this->messages[57]);
						return false;
					}
					elseif ($question_result->RecordCount() == 1)
					{
						$show = $question_result->FetchNextObject();
						if (strlen(trim($this->classified_variables["question_value_other"][$key])) > 0)
						{
							//$this->body .="use the other value for ---".$key."<br>\n";
							$use_this_value = urldecode($this->classified_variables["question_value_other"][$key]);
						}
						else
							$use_this_value = urldecode($this->classified_variables["question_value"][$key]);
						if ($show->CHOICES == "check")
							$checkbox = 1;
						elseif ($show->CHOICES == "url")
							$checkbox = 2;
						else
							$checkbox = 0;
						$use_this_value = str_replace("\n"," ",$use_this_value);
						$this->sql_query = "insert into ".$this->classified_extra_table."
							(classified_id,name,question_id,value,explanation,checkbox,display_order)
							values
							(".$this->classified_id.",\"".urlencode($show->NAME)."\",\"".$key."\",\"".urlencode($use_this_value)."\",
							\"".addslashes($show->EXPLANATION)."\",".$checkbox.",".$show->DISPLAY_ORDER.")";
						$current_insert_result = $db->Execute($this->sql_query);
						//echo $this->sql_query." is the query<br>\n";
						if (!$current_insert_result)
						{
							//echo $this->sql_query." is the bad query<br>\n";
							$this->error_message = urldecode($this->messages[57]);
							return false;
						}
						$search_text .= $use_this_value." - ";
					}
				} // end of if
			} // end of for $i

		}// end of if num_questions > 0

		$num_group_questions = count($this->classified_variables["group_value"]);
		//echo $num_group_questions." is the num of group questions remembered<Br>\n";
		if ($num_group_questions > 0 )
		{
			reset($this->classified_variables["group_value"]);
			while (list($key,$value) = each($this->classified_variables["group_value"]))
			{
				if ((strlen(trim($this->classified_variables["group_value"][$key])) > 0) || (strlen(trim($this->classified_variables["group_value_other"][$key])) > 0))
				 {
					//there is a value in this questions so put it in the db
					$this->sql_query = "SELECT * FROM ".$this->classified_sell_questions_table." WHERE question_id = \"".$key."\"";
					$question_result = $db->Execute($this->sql_query);
					//echo $this->sql_query." is the query<br>\n";
					if (!$question_result)
					{
						//$this->body .=$this->sql_query." is the query<br>\n";
						$this->error_message = urldecode($this->messages[57]);
						return false;
					}
					elseif ($question_result->RecordCount() == 1)
					{
						$show = $question_result->FetchNextObject();
						if (strlen(trim($this->classified_variables["group_value_other"][$key])) > 0)
						{
							//$this->body .="use the other value for ---".$key."<br>\n";
							$use_this_value = urldeocde($this->classified_variables["group_value_other"][$key]);
						}
						else
							$use_this_value = urldecode($this->classified_variables["group_value"][$key]);
						if ($show->CHOICES == "check")
							$checkbox = 1;
						elseif ($show->CHOICES == "url")
							$checkbox = 2;
						else
							$checkbox = 0;
						$this->sql_query = "insert into ".$this->classified_extra_table."
							(classified_id,name,question_id,value,explanation,checkbox,group_id)
							values
							(".$this->classified_id.",\"".urlencode($show->NAME)."\",\"".$key."\",\"".urlencode($use_this_value)."\",
							\"".addslashes($show->EXPLANATION)."\",".$checkbox.",".$this->users_group.")";
						$insert_result = $db->Execute($this->sql_query);
						//echo $this->sql_query." is the query<br>\n";
						if (!$insert_result)
						{
							//$this->body .=$this->sql_query." is the query<br>\n";
							$this->error_message = urldecode($this->messages[57]);
							return false;
						}
						$search_text .= urlencode($use_this_value)." - ";
					}
				} // end of if
			} // end of for $i
		}


		 if (strlen(trim($this->classified_variables["optional_field_1"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_1"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_2"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_2"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_3"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_3"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_4"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_4"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_5"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_5"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_6"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_6"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_7"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_7"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_8"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_8"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_9"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_9"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_10"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_10"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_11"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_11"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_12"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_12"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_13"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_13"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_14"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_14"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_15"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_15"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_16"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_16"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_17"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_17"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_18"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_18"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_19"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_19"])." - ";
		 if (strlen(trim($this->classified_variables["optional_field_20"])) > 0)
		 	$search_text .= urlencode($this->classified_variables["optional_field_20"])." - ";
		$search_text = urldecode($search_text);
		$this->sql_query = "update ".$this->classifieds_table." set
			search_text = \"".urlencode($search_text)."\"
			where id = ".$this->classified_id;
		//echo $this->sql_query." is the query<br>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//$this->body .=$this->sql_query." is the query<br>\n";
			$this->error_message = urldecode($this->messages[57]);
			return false;
		}
		return true;
	} //end of function insert_category_questions

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_classified_images($db)
	{
		//tie the images in the two tables to this classifieds
		if (count($this->images_captured) > 0)
		{
			//images were captured
			//display them
			foreach ($this->images_captured as $key => $value)
			{
				if ($value["type"] == 1)
				{
					$this->sql_query = "update ".$this->images_urls_table." set
						classified_id = ".$this->classified_id."
						WHERE image_id = ".$value["id"];
					//$this->body .=$this->sql_query." is the query<br>\n";
					$image_result = $db->Execute($this->sql_query);
					if (!$image_result)
					{
						//$this->body .=$this->sql_query." is the query<br>\n";
						$this->error_message = urldecode($this->messages[57]);
						return false;
					}
				}
				elseif ($value["type"] == 2)
				{
					$this->sql_query = "update ".$this->images_table." set
						classified_id = ".$this->classified_id."
						WHERE image_id = ".$value["id"];
					//$this->body .=$this->sql_query." is the query<br>\n";
					$image_result = $db->Execute($this->sql_query);
					if (!$image_result)
					{
						//$this->body .=$this->sql_query." is the query<br>\n";
						$this->error_message = urldecode($this->messages[57]);
						return false;
					}
				}
			}
		}
		return true;
	} //end of function insert_classified_images

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_classified_detail_form ($db)
	{
		$this->page_id = 9;
		$this->get_text($db);
		if ($this->classified_user_id)
		{
			$this->update_classified_details_collected($db,0);
			$this->get_category_configuration($db,$this->terminal_category,0);
			$this->get_ad_configuration($db);
			//echo $this->category_configuration->USE_SITE_DEFAULT." is use site default<bR>\n";
			if (!$this->category_configuration->USE_SITE_DEFAULT)
			{
				//echo "using site settings<br>\n";
				$this->field_configuration_data = $this->ad_configuration_data;
				$this->field_configuration_data->USE_OPTIONAL_FIELD_1 = $this->configuration_data['use_optional_field_1'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_2 = $this->configuration_data['use_optional_field_2'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_3 = $this->configuration_data['use_optional_field_3'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_4 = $this->configuration_data['use_optional_field_4'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_5 = $this->configuration_data['use_optional_field_5'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_6 = $this->configuration_data['use_optional_field_6'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_7 = $this->configuration_data['use_optional_field_7'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_8 = $this->configuration_data['use_optional_field_8'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_9 = $this->configuration_data['use_optional_field_9'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_10 = $this->configuration_data['use_optional_field_10'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_11 = $this->configuration_data['use_optional_field_11'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_12 = $this->configuration_data['use_optional_field_12'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_13 = $this->configuration_data['use_optional_field_13'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_14 = $this->configuration_data['use_optional_field_14'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_15 = $this->configuration_data['use_optional_field_15'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_16 = $this->configuration_data['use_optional_field_16'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_17 = $this->configuration_data['use_optional_field_17'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_18 = $this->configuration_data['use_optional_field_18'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_19 = $this->configuration_data['use_optional_field_19'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_20 = $this->configuration_data['use_optional_field_20'];
			}
			else
			{
				//echo "using category settings<br>\n";
				$this->field_configuration_data = $this->category_configuration;
			}

			$user_data = $this->get_user_data($db,$this->classified_user_id);
			$this->body .="<form name=classified_details_form action=".$this->configuration_data['classifieds_file_name']."?a=1&set_details=1 method=post ";
			if ($this->configuration_data['use_rte'])
			{
				$this->body .= "onsubmit=\"return submitForm();\"";
			}
			$this->body .=">\n";
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
			$this->body .="<tr class=place_ad_section_title>\n\t<td colspan=2>\n\t";
			$this->body .= urldecode($this->messages[608]);
			$this->body .="</td></tr><tr class=page_title>\n\t<td colspan=2>\n\t".urldecode($this->messages[108]);
			$this->body .= "\n\t</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td colspan=2>".urldecode($this->messages[111])."\n\t</td>\n</tr>\n";

			//classified category
			$this->body .="<tr class=place_an_ad_details_category_tree>\n\t<td colspan=2>\n\t";
			$this->body .=urldecode($this->messages[640])."\n\t";
			$this->body .=urldecode($this->messages[639])." >";

			$category_tree = $this->get_category_tree($db,$this->terminal_category);
			reset ($this->category_tree_array);

			if ($category_tree)
			{
				//category tree
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
							$this->body .=" ".$this->category_tree_array[$i]["category_name"]." ";
						else
							$this->body .=" ".$this->category_tree_array[$i]["category_name"]." > ";
					}
				}
				else
				{
					$this->body .=$category_tree;
				}
				$this->body .="\n\t</td>\n</tr>\n";
			}

			if ($this->debug_sell) echo "before classified duration dropdown<Br>\n";
			if ($this->sell_type == 1)
			{
				if ($this->debug_sell)
				{
					echo "displaying classified duration dropdown<bR>\n";
					echo $this->price_plan->CHARGE_PER_AD_TYPE." is charge per ad type<br>\n";
				}

				//classified duration
				$this->body .="<tr>\n\t<td width=35% class=place_an_ad_details_fields>".urldecode($this->messages[125])."<br>".urldecode($this->messages[126])."\n\t</td>\n\t";
				$this->body .="<td width=65%  class=place_an_ad_details_data>\n\t<select  class=place_an_ad_details_data id=classified_length name=b[classified_length]>\n\t\t";

				if ($this->price_plan->CHARGE_PER_AD_TYPE == 2)
				{
					//pull price plan specific
					if ($this->price_plan->CATEGORY_ID)
						$this->sql_query = "select * from ".$this->price_plan_lengths_table." where
							price_plan_id = ".$this->users_price_plan." and category_id = ".$this->price_plan->CATEGORY_ID." order by length_of_ad asc";
					else
						$this->sql_query = "select * from ".$this->price_plan_lengths_table." where
							price_plan_id = ".$this->users_price_plan." and category_id = 0 order by length_of_ad asc";
					$length_result = $db->Execute($this->sql_query);
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					if (!$length_result)
					{
						if ($this->debug_sell)
						{
							echo $db->ErrorMsg()."<Br>\n";
							echo $this->sql_query."<br>\n";
						}
						$this->display_basic_duration_dropdown($db);
					}
					elseif ($length_result->RecordCount() > 0)
					{
						while ($show_lengths = $length_result->FetchRow())
						{
							$this->body .= "<option value=".$show_lengths["length_of_ad"];
							if ($this->classified_variables["classified_length"] == $show_lengths["length_of_ad"])
								   $this->body .= " selected";
							$this->body .= ">".$show_lengths["display_length_of_ad"]."</option>";
						}
					}
					else
					{
						if ($this->debug_sell) echo "dropdown 1<bR>\n";
						$this->display_basic_duration_dropdown($db);
					}
				}
				else
				{
					if ($this->debug_sell) echo "dropdown 2<bR>\n";
					$this->display_basic_duration_dropdown($db);
				}
				$this->body .= "</select>\n\t</td>\n</tr>\n";
			}
			if ($this->debug_sell)
			{
				echo "after classified duration dropdown<Br>\n";
			}

			//classified title
			$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[123]);
			if ($this->error_variables["classified_title"])
				$this->body .="<br><font class=error_message>".urldecode($this->messages[116])."</font>";
			$this->body .="</td>\n\t";
			$this->body .="<td class=place_an_ad_details_data>\n\t<input type=text id=classified_title class=place_an_ad_details_data name=b[classified_title]
				value=\"".stripslashes(urldecode($this->classified_variables["classified_title"]))."\" ";
			if ($this->ad_configuration_data->TITLE_LENGTH > 50)
				$this->body .= "size=50 maxlength=".$this->ad_configuration_data->TITLE_LENGTH;
			else
				$this->body .= "size=".$this->ad_configuration_data->TITLE_LENGTH." maxlength=".$this->ad_configuration_data->TITLE_LENGTH;
			$this->body .= ">\n\t";
			$this->body .="</td>\n</tr>\n";
			if ($this->field_configuration_data->USE_PRICE_FIELD && $this->sell_type == 1)
			{
				//classified price
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[134]);
				if ($this->error_variables["price"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[642])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=place_an_ad_details_data>\n\t<input type=text  id=price class=place_an_ad_details_data name=b[price] ";
				if ($this->ad_configuration_data->PRICE_LENGTH > 12)
					$this->body .= "size=12 maxlength=".$this->ad_configuration_data->PRICE_LENGTH;
				else
					$this->body .= "size=".$this->ad_configuration_data->PRICE_LENGTH." maxlength=".$this->ad_configuration_data->PRICE_LENGTH;
				$this->body .= " value=\"".stripslashes(urldecode($this->classified_variables["price"]))."\">\n\t";
				//display currency type
				$this->sql_query = "SELECT * FROM ".$this->currency_types_table." order by display_order";
				$currency_result = $db->Execute($this->sql_query);
				if (!$currency_result)
					$this->site_error($db->ErrorMsg());
				elseif ($currency_result->RecordCount() > 0)
				{
					if ($currency_result->RecordCount() > 1)
					{
						$this->body .= "<select class=place_an_ad_details_data name=b[currency_type] id=currency_type>\n\t";
						//$this->body .=  "<option value=0>".urldecode($this->messages[580])."</option>\n\t";
						while ($show_currency_types = $currency_result->FetchNextObject())
						{
							$this->body .=  "<option value=".$show_currency_types->TYPE_ID;
							if ($show_currency_types->TYPE_ID == $this->classified_variables["currency_type"])
								$this->body .=  " selected";
							$this->body .=  ">".$show_currency_types->TYPE_NAME."</option>\n\t\t";
						}
						$this->body .=  "</select>";
					}
					else
					{
						//there is only one choice so display as a hardcoded...no choice
						$show_currency_types = $currency_result->FetchNextObject();
						$this->body .= " ".$show_currency_types->TYPE_NAME." <input type=hidden name=b[currency_type] id=currency_type value=\"".$show_currency_types->TYPE_ID."\">";
					}
				}

				$this->body .="</td>\n</tr>\n";
			}
			elseif ($this->sell_type == 2)
			{
				$this->body .= "<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[102690]);
				if ($this->error_variables["auction_quantity"])
					$this->body .= "<br><font class=error_message>".urldecode($this->error_variables["auction_quantity"])."</font>";
				$this->body .= "</td>\n\t";
				$this->body .= "<td valign=top  class=place_an_ad_details_data>\n\t<input type=text name=b[auction_quantity] value=\"";
				if (!$this->classified_variables["auction_quantity"])
					$this->classified_variables["auction_quantity"] = 1;
				$this->body .= $this->classified_variables["auction_quantity"]."\" class=place_an_ad_details_data>\n\t";
				$this->body .= "</td>\n</tr>\n";

				if (!$this->classified_variables["auction_type"])
					$this->classified_variables["auction_type"] == 1;
				$this->body .= "<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[102689])."\n\t";
				if ($this->error_variables["auction_type"])
					$this->body .= "<br><font class=error_message>".urldecode($this->error_variables["auction_type"])."</font>";
				$this->body .= "</td>\n\t";
				$this->body .= "<td class=place_an_ad_details_data>\n\t";

				if ($this->configuration_data['allow_standard'] && $this->configuration_data['allow_dutch'])
				{
					$this->body .="<select class=place_an_ad_details_data name=b[auction_type]>\n\t\t";

					$this->body .= "<option value=1 ";
					if ($this->classified_variables["auction_type"] == 1)
						$this->body .= " selected";
					$this->body .= "> ".urldecode($this->messages[102837])."</option>";
					$this->body .= "<option value=2 ";
					if ($this->classified_variables["auction_type"] == 2)
						$this->body .= " selected";
					$this->body .= ">".urldecode($this->messages[102838])."</option>";
					$this->body .= "</select>";
				}

				elseif ($this->configuration_data['allow_standard'])
				{
					$this->body .="<input type=hidden class=place_an_ad_details_data name=b[auction_type] value=1>".urldecode($this->messages[102837])."</input>\n\t\t";
				}

				elseif ($this->configuration_data['allow_dutch'])
				{
					$this->body .="<input type=hidden class=place_an_ad_details_data name=b[auction_type] value=2>".urldecode($this->messages[102838])."</input>\n\t\t";
				}
				else
				{
					$this->body .="<select class=place_an_ad_details_data name=b[auction_type]>\n\t\t";

					$this->body .= "<option value=1 ";
					if ($this->classified_variables["auction_type"] == 1)
						$this->body .= " selected";
					$this->body .= "> ".urldecode($this->messages[102837])."</option>";
					$this->body .= "<option value=2 ";
					if ($this->classified_variables["auction_type"] == 2)
						$this->body .= " selected";
					$this->body .= ">".urldecode($this->messages[102838])."</option>";
					$this->body .= "</select>";
				}
				$this->body .= $this->display_help_link(200172);
				$current_time = $this->shifted_time($db);

				if($this->configuration_data['user_set_auction_start_times'])
				{
					$this->body .="<tr>\n\t<td width=35% class=place_an_ad_details_fields>".urldecode($this->messages[102816]);
					if ($this->error_variables["start_time"])
						$this->body .= "<br><font class=error_message>".urldecode($this->messages[103356])."</font>";
					$this->body .= "\n\t</td>\n\t";
					$this->body .="<td width=65% class=place_an_ad_details_data>";
					if ($this->classified_variables["start_time"] < $current_time)
						$current_start_time = $current_time;
					else
						$current_start_time = $this->classified_variables["start_time"];
					$this->get_date_select("b[start_time][start_year]","b[start_time][start_month]","b[start_time][start_day]","b[start_time][start_hour]","b[start_time][start_minute]",$current_start_time);
					$this->body .="</td>\n</tr>\n";
				}

				// auction end time
				if($this->configuration_data['user_set_auction_end_times'])
				{
					if ($this->classified_variables["end_time"] < $current_time)
						$current_end_time = $current_time;
					else
						$current_end_time = $this->classified_variables["end_time"];
					$this->body .="<tr>\n\t<td width=35% class=place_an_ad_details_fields>".urldecode($this->messages[102820]);
					if ($this->error_variables["end_time"])
						$this->body .= "<br><font class=error_message>".urldecode($this->messages[103357])."</font>";
					$this->body .= "\n\t</td>\n\t";
					$this->body .="<td width=65% class=place_an_ad_details_data>";
					$this->get_date_select("b[end_time][end_year]","b[end_time][end_month]","b[end_time][end_day]","b[end_time][end_hour]","b[end_time][end_minute]",$current_end_time);
					$this->body .="</td>\n</tr>\n";
				}

				//auction duration
				$this->body .="<tr>\n\t<td width=35% class=place_an_ad_details_fields>".urldecode($this->messages[125])."<br>".urldecode($this->messages[100126])."\n\t</td>\n\t";
				$this->body .= "<td width=65% class=place_an_ad_details_data>\n\t<select class=place_an_ad_details_data id=classified_length name=b[classified_length]>\n\t\t";
				$this->display_basic_duration_dropdown($db);
				$this->body .= "</select></font>\n\t</td>\n\t</tr>\n\t";
				if (($this->error_variables["duration"]) || ($this->error_variables["classified_length"]))
					$this->body .= "<tr>\n\t<td><font class=error_message>".urldecode($this->messages[103358])."</font></td>\n\t</tr>\n\t";

				// currency type
				$this->body .= "<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[100134])."</td>";
				$this->body .= "<td class=place_an_ad_details_data>";

				$this->sql_query = "SELECT * FROM ".$this->currency_types_table." order by display_order";
				$currency_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<br>\n";
				if($this->configuration_data['debug_sell'])
				{
					$this->debug_display($this->sql_query, $db, $filename, $this->function_name, "currency_types_table", "get data from currency types table");
				}
				if (!$currency_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					$this->site_error($db->ErrorMsg());
				}
				elseif ($currency_result->RecordCount() > 0)
				{
					if ($currency_result->RecordCount() > 1)
					{
						$this->body .= "<select class=place_an_ad_details_data name=b[currency_type] id=currency_type>\n\t";
						//$this->body .=  "<option value=0>".urldecode($this->messages[580])."</option>\n\t";
						while ($show_currency_types = $currency_result->FetchNextObject())
						{
							$this->body .=  "<option value=".$show_currency_types->TYPE_ID;
							if ($show_currency_types->TYPE_ID == $this->classified_variables["currency_type"])
								$this->body .=  " selected";
							$this->body .=  ">".$show_currency_types->TYPE_NAME."</option>\n\t\t";
						}
						$this->body .=  "</select>";
					}
					else
					{
						//there is only one choice so display as a hardcoded...no choice
						$show_currency_types = $currency_result->FetchNextObject();
						$this->body .= " ".$show_currency_types->TYPE_NAME." <input type=hidden name=b[currency_type] id=currency_type value=\"".$show_currency_types->TYPE_ID."\">";
					}
				}
				$this->body .= "</td></tr>";

				// bidding

				//minimum bid
				$this->body .= "<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[102691]);
				if ($this->error_variables["auction_minimum"])
					$this->body .= "<br><font class=error_message>".urldecode($this->error_variables["auction_minimum"])."</font>";
				$this->body .= "</td>\n\t";
				$this->body .= "<td valign=top  class=place_an_ad_details_data>\n\t<input class=place_an_ad_details_data type=text name=b[auction_minimum] value=\"".stripslashes(urldecode($this->classified_variables["auction_minimum"]))."\">\n\t";
				$this->body .= "</td>\n</tr>\n";

				//reserve price
				$this->body .= "<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[102692]);
				if ($this->error_variables["auction_reserve"])
					$this->body .= "<br><font class=error_message>".urldecode($this->messages[102731])."</font>";
				$this->body .= "</td>\n\t";
				$this->body .= "<td valign=top  class=place_an_ad_details_data>\n\t<input class=place_an_ad_details_data type=text name=b[auction_reserve] value=\"".stripslashes(urldecode($this->classified_variables["auction_reserve"]))."\">\n\t";
				$this->body .= "</td>\n</tr>\n";

				//buy now price
				$this->body .= "<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[102693]);
				if ($this->error_variables["auction_buy_now"])
				{
					if(!$buy_now_only)
						$this->body .= "<br><font class=error_message>".urldecode($this->messages[102732])."</font>";
					else
						$this->body .= "<br><font class=error_message>".urldecode($this->messages[103373])."</font>";
				}
				$this->body .= "</td>\n\t";
				$this->body .= "<td valign=top  class=place_an_ad_details_data>\n\t<input type=text name=b[auction_buy_now] value=\"".stripslashes(urldecode($this->classified_variables["auction_buy_now"]))."\">\n\t";
				$this->body .= "</td>\n</tr>\n";

				// payment types
				if($this->configuration_data['payment_types'])
				{
					$this->body .= "<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[102850]);
					if ($this->error_variables["payment_options"])
						$this->body .= "<br><font class=error_message>".urldecode($this->messages[102851])."</font>";
					$this->body .= "</td>\n\t";
					$this->body .= "<td valign=top  class=place_an_ad_details_data>";

					$this->body .="<table cellpadding=1 cellspacing=1 border=1 width=100%>\n";
					$this->body .="<tr>\n\t<td colspan=1 class=place_an_ad_details_data align=center>".urldecode($this->messages[102867])."</td><tr>\n\t";
					$this->sql_query = "SELECT * FROM ".$this->auction_payment_types_table." order by display_order";
					$payment_result = $db->Execute($this->sql_query);
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					if($this->configuration_data['debug_sell'])
					{
						$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, $this->payment_types_table, "get payment types options");
					}
					if (!$payment_result)
					{
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						$this->error_message = urldecode($this->messages[100057]);
						return false;
					}
					elseif ($payment_result->RecordCount() > 0)
					{
						if(!is_array($this->classified_variables["payment_options"]))
						{
							$this->classified_variables["payment_options"] = explode("||",$this->classified_variables["payment_options"]);
						}

						$this->body .="<tr>\n\t<td colspan=1><table cellpadding=2 cellspacing=1 border=0 width=100%><tr>";
						$count = 0;
						while($show_payment = $payment_result->FetchNextObject())
						{
							if($count >= 3)
							{
								$count = $count % 3;
								if(in_array($show_payment->TYPE_NAME,$this->classified_variables["payment_options"])){
									$this->body .="\n\t</tr>\n\t<tr>\n\t<td class=place_an_ad_details_data><input type=checkbox name=b[payment_options_from_form][] value=\"".urlencode($show_payment->TYPE_NAME)."\" checked >".$show_payment->TYPE_NAME."</td>\n\t";
								}else{
									$this->body .="\n\t</tr>\n\t<tr>\n\t<td class=place_an_ad_details_data><input type=checkbox name=b[payment_options_from_form][] value=\"".urlencode($show_payment->TYPE_NAME)."\" >".$show_payment->TYPE_NAME."</td>\n\t";
								}
							}
							else
							{
								if(in_array($show_payment->TYPE_NAME,$this->classified_variables["payment_options"])){
									$this->body .="<td class=place_an_ad_details_data><input type=checkbox name=b[payment_options_from_form][] value=\"".urlencode($show_payment->TYPE_NAME)."\" checked >".$show_payment->TYPE_NAME."</td>\n\t";
								}else{
									$this->body .="<td class=place_an_ad_details_data><input type=checkbox name=b[payment_options_from_form][] value=\"".urlencode($show_payment->TYPE_NAME)."\">".$show_payment->TYPE_NAME."</td>\n\t";
								}
							}
							$count++;
						}
						$this->body .= "</tr></table></td></tr>";
					}
					else
					{
						$this->body .=" <tr>\n\t<td colspan = 1> No Choices to Display <input type=hidden name=b[payment_type_count] value=\"".$payment_result->RecordCount()."\"></td>\n\t</tr>";
					}
					$this->body .= "</tr></table>\n";
				}
			}

			if ($this->field_configuration_data->USE_EMAIL_OPTION_FIELD)
			{
				//display the email that will be used to communicate
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1339]);
				if ($this->error_variables["email_option"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1343])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=place_an_ad_details_data>\n\t";
				if (strlen($this->classified_variables["email_option"]) > 0)
					$current_email = urldecode($this->classified_variables["email_option"]);
				else
					$current_email = $user_data->EMAIL;
				if ($this->ad_configuration_data->USE_EMAIL_OVERRIDE)
				{
					$this->body .= "<input type=text name=b[email_option] value=\"".$current_email."\" class=place_an_ad_details_data>";
				}
				else
				{
					$this->body .= $user_data->EMAIL;
					$this->body .= "<input type=hidden name=b[email_option] value=\"".$current_email."\">";
				}
				if ($this->field_configuration_data->PUBLICALLY_EXPOSE_EMAIL)
				{
					$this->body .= urldecode($this->messages[1340]);
					$this->body .= urldecode($this->messages[1341])." <input type=radio name=b[expose_email] value=1 ";
					if ($this->classified_variables["expose_email"] == 1)
						$this->body .= "checked";
					$this->body .= "> ".urldecode($this->messages[1342])." <input type=radio name=b[expose_email] value=0 ";
					if (($this->classified_variables["expose_email"] == 0) || (strlen(trim($this->classified_variables["expose_email"])) == 0))
						$this->body .= "checked";
					$this->body .= ">";
				}

				$this->body .="</td>\n</tr>\n";
			}
			else
			{
				$this->body .= "<input type=hidden name=b[email_option] value=\"".$user_data->EMAIL."\">";
			}

			if ($this->field_configuration_data->USE_PHONE_1_OPTION_FIELD)
			{
				//display the email that will be used to communicate
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1345]);
				$this->body .="</td>\n\t";
				$this->body .="<td class=place_an_ad_details_data>\n\t";
				//if (strlen($this->classified_variables["phone_1_option"]) > 0)
				if ($this->debug_sell) echo $this->classified_variables["phone_1_option"]." is phone_1_option<Br>\n";
				$current_phone_1 = $this->classified_variables["phone_1_option"];
				//else
					//$current_phone_1 = $user_data->PHONE;

				if ($this->field_configuration_data->ALLOW_PHONE_1_OVERRIDE)
				{
					$this->body .= "<input type=text name=b[phone_1_option] value=\"".$current_phone_1."\" ";
					if ($this->ad_configuration_data->PHONE_1_LENGTH > 18)
						$this->body .= "size=18 maxlength=".$this->ad_configuration_data->PHONE_1_LENGTH;
					else
						$this->body .= "size=".$this->ad_configuration_data->PHONE_1_LENGTH." maxlength=".$this->ad_configuration_data->PHONE_1_LENGTH;

					$this->body .= " class=place_an_ad_details_data>";
				}
				else
				{
					$this->body .= $user_data->PHONE;
					$this->body .= "<input type=hidden name=b[phone_1_option] value=\"".$user_data->PHONE."\">";
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_PHONE_2_OPTION_FIELD)
			{
				//display the email that will be used to communicate
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1346]);
				$this->body .="</td>\n\t";
				$this->body .="<td class=place_an_ad_details_data>\n\t";
				//if (strlen($this->classified_variables["phone_2_option"]) > 0)
					$current_phone_2 = $this->classified_variables["phone_2_option"];
				//else
					//$current_phone_2 = $user_data->PHONE2;
				if ($this->field_configuration_data->ALLOW_PHONE_2_OVERRIDE)
				{
					$this->body .= "<input type=text name=b[phone_2_option] value=\"".$current_phone_2."\" ";
					if ($this->ad_configuration_data->PHONE_2_LENGTH > 18)
						$this->body .= "size=18 maxlength=".$this->ad_configuration_data->PHONE_2_LENGTH;
					else
						$this->body .= "size=".$this->ad_configuration_data->PHONE_2_LENGTH." maxlength=".$this->ad_configuration_data->PHONE_2_LENGTH;

					$this->body .= " class=place_an_ad_details_data>";
				}
				else
				{
					$this->body .= $user_data->PHONE2;
					$this->body .= "<input type=hidden name=b[phone_2_option] value=\"".$user_data->PHONE2."\">";
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_FAX_FIELD_OPTION)
			{
				//display the email that will be used to communicate
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1355]);
				$this->body .="</td>\n\t";
				$this->body .="<td class=place_an_ad_details_data>\n\t";
				//if (strlen($this->classified_variables["fax_option"]) > 0)
					$current_fax = $this->classified_variables["fax_option"];
				//else
				//	$current_fax = $user_data->FAX;
				if ($this->field_configuration_data->ALLOW_FAX_OVERRIDE)
				{
					$this->body .= "<input type=text name=b[fax_option] value=\"".$current_fax."\" ";
					if ($this->ad_configuration_data->FAX_LENGTH > 18)
						$this->body .= "size=18 maxlength=".$this->ad_configuration_data->FAX_LENGTH;
					else
						$this->body .= "size=".$this->ad_configuration_data->FAX_LENGTH." maxlength=".$this->ad_configuration_data->FAX_LENGTH;

					$this->body .= " class=place_an_ad_details_data>";
				}
				else
				{
					$this->body .= $user_data->FAX;
					$this->body .= "<input type=hidden name=b[fax_option] value=\"".$user_data->FAX."\">";
				}
				$this->body .="</td>\n</tr>\n";
			}

			//city location
			if ($this->field_configuration_data->USE_CITY_FIELD)
			{
				//if (strlen($this->classified_variables["city"]) == 0)
					//$city_data = $user_data->CITY;
				//else
					$city_data = $this->classified_variables["city"];
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1129]);
				if ($this->error_variables["city"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1130])."</font>";
				$this->body .="<td class=place_an_ad_details_data>\n\t<input type=text name=b[city] ";
				if ($this->ad_configuration_data->CITY_LENGTH > 20)
					$this->body .= "size=20 maxlength=".$this->ad_configuration_data->CITY_LENGTH;
				else
					$this->body .= "size=".$this->ad_configuration_data->CITY_LENGTH." maxlength=".$this->ad_configuration_data->CITY_LENGTH;
				$this->body .= " value=\"".$city_data."\" class=place_an_ad_details_data>\n\t";
				$this->body .="</td>\n</tr>\n";
			}

			//state
			if ($this->field_configuration_data->USE_STATE_FIELD)
			{
				//if (strlen($this->classified_variables["state"]) == 0)
					//$state_data = $user_data->STATE;
				//else
					$state_data = $this->classified_variables["state"];
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[127]);
				if ($this->error_variables["state"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[117])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				$this->sql_query = "SELECT * FROM ".$this->states_table." order by display_order, name";
				$state_result = $db->Execute($this->sql_query);
				if (!$state_result)
					return false;
				else
				{
					$this->body .="<select name=b[state] class=place_an_ad_details_data>\n\t\t";
					$this->body .="<option value=none>".urldecode($this->messages[1208])."</option>\n\t\t";
					while ($show = $state_result->FetchNextObject()) {
						//spit out the state list
						$this->body .="<option value=\"".$show->ABBREVIATION."\"";
						if (trim(urldecode($state_data)) == trim($show->ABBREVIATION))
						$this->body .="selected";
						$this->body .=">".$show->NAME."\n\t\t";
					}

					$this->body .="</select>\n\t";
				}
				$this->body .="</td>\n</tr>\n";
			}

			//country
			if ($this->field_configuration_data->USE_COUNTRY_FIELD)
			{
				//if (strlen($this->classified_variables["country"]) == 0)
					//$country_data = $user_data->COUNTRY;
				//else
					$country_data = $this->classified_variables["country"];
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[115]);
				if ($this->error_variables["country"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[128])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				$this->sql_query = "SELECT * FROM ".$this->countries_table." order by display_order, name";
				$country_result = $db->Execute($this->sql_query);
				if (!$country_result)
					return false;
				else
				{
					$this->body .="<select name=b[country] class=place_an_ad_details_data>\n\t\t";
					$this->body .="<option value=none>".urldecode($this->messages[1209])."</option>\n\t\t";
					while ($show = $country_result->FetchNextObject()) {
						//spit out the country list
						$this->body .="<option ";
						if ((urldecode($country_data) == $show->ABBREVIATION) || (urldecode($country_data) == $show->NAME))
							$this->body .="selected";
						$this->body .=">".$show->NAME."</option>\n\t\t";
					}

					$this->body .="</select>\n\t";
				}
				$this->body .="</td>\n</tr>\n";
			}

			//zip location
			if ($this->field_configuration_data->USE_ZIP_FIELD)
			{
				//if (strlen($this->classified_variables["zip_code"]) == 0)
					//$zip_code_data = $user_data->ZIP;
				//else
					$zip_code_data = $this->classified_variables["zip_code"];
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[121]);
				if ($this->error_variables["zip_code"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[118])."</font>";
				if (strlen($this->messages[119]) > 0)
					$this->body .="<bR>".urldecode($this->messages[119])."</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t<input type=text name=b[zip_code] value=\"".$zip_code_data."\" ";
				if ($this->ad_configuration_data->ZIP_LENGTH > 10)
					$this->body .= "size=10 maxlength=".$this->ad_configuration_data->ZIP_LENGTH;
				else
					$this->body .= "size=".$this->ad_configuration_data->ZIP_LENGTH." maxlength=".$this->ad_configuration_data->ZIP_LENGTH;
				$this->body .= " class=place_an_ad_details_data>\n\t";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_URL_LINK_1)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[2434]);
				if ($this->error_variables["url_link_1"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[2437])."</font>";
				$this->body .="<td class=place_an_ad_details_data>\n\t<input type=text name=b[url_link_1] ";
				if ($this->ad_configuration_data->URL_LINK_1_LENGTH > 30)
					$this->body .= "size=30 maxlength=".$this->ad_configuration_data->URL_LINK_1_LENGTH;
				else
					$this->body .= "size=".$this->ad_configuration_data->URL_LINK_1_LENGTH." maxlength=".$this->ad_configuration_data->URL_LINK_1_LENGTH;
				$this->body .= " value=\"".$this->classified_variables["url_link_1"]."\" class=place_an_ad_details_data>\n\t";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_URL_LINK_2)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[2435]);
				if ($this->error_variables["url_link_2"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[2438])."</font>";
				$this->body .="<td class=place_an_ad_details_data>\n\t<input type=text name=b[url_link_2] ";
				if ($this->ad_configuration_data->URL_LINK_2_LENGTH > 30)
					$this->body .= "size=30 maxlength=".$this->ad_configuration_data->URL_LINK_2_LENGTH;
				else
					$this->body .= "size=".$this->ad_configuration_data->URL_LINK_2_LENGTH." maxlength=".$this->ad_configuration_data->URL_LINK_2_LENGTH;
				$this->body .= " value=\"".$this->classified_variables["url_link_2"]."\" class=place_an_ad_details_data>\n\t";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_URL_LINK_3)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[2436]);
				if ($this->error_variables["url_link_3"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[2439])."</font>";
				$this->body .="<td class=place_an_ad_details_data>\n\t<input type=text name=b[url_link_3] ";
				if ($this->ad_configuration_data->URL_LINK_3_LENGTH > 30)
					$this->body .= "size=30 maxlength=".$this->ad_configuration_data->URL_LINK_3_LENGTH;
				else
					$this->body .= "size=".$this->ad_configuration_data->URL_LINK_3_LENGTH." maxlength=".$this->ad_configuration_data->URL_LINK_3_LENGTH;
				$this->body .= " value=\"".$this->classified_variables["url_link_3"]."\" class=place_an_ad_details_data>\n\t";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->debug_sell)
			{
				echo $this->ad_configuration_data->OPTIONAL_1_FIELD_TYPE." is type of 1<Br>\n";
				echo $this->field_configuration_data->USE_OPTIONAL_FIELD_1." is use field of 1<bR>\n";
				echo $this->configuration_data['optional_1_filter_association']." is filter_assoc of 1<bR>\n";
				echo $this->ad_configuration_data->OPTIONAL_1_LENGTH." is length of 1<bR>\n";
				echo $this->ad_configuration_data->OPTIONAL_1_OTHER_BOX." is other box of 1<bR>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_1)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[909]);
				if ($this->error_variables["optional_field_1"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[910])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_1_filter_association']))
				//(!$this->configuration_data['optional_1_filter_association'])
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_1"]));
				}
				else
				{

					if (!$this->ad_configuration_data->OPTIONAL_1_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_1] id=optional_field_1 ";
						if ($this->ad_configuration_data->OPTIONAL_1_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_1_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_1_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_1_LENGTH;
						$this->body .= " class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_1"]))."\">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_1_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						//if ($this->debug_sell)
							//echo $this->sql_query."<bR>\n";
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_1] id=optional_field_1 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_1"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_1] id=optional_field_1 class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_1"]))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_1_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_1_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_1 name=b[optional_field_1_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_1"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_1_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_1_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_1_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_1_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_2)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[941]);
				if ($this->error_variables["optional_field_2"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[942])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_2_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_2"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_2_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_2] id=optional_field_2 ";
						if ($this->ad_configuration_data->OPTIONAL_2_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_2_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_2_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_2_LENGTH;
						$this->body .= " class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_2"]))."\">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_2_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_2] id=optional_field_2 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_2"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_2] id=optional_field_2 class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_2"]))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_2_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_2_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_2 name=b[optional_field_2_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_2"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_2_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_2_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_2_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_2_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_3)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[943]);
				if ($this->error_variables["optional_field_3"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[944])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_3_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_3"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_3_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_3] id=optional_field_3 ";
						if ($this->ad_configuration_data->OPTIONAL_3_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_3_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_3_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_3_LENGTH;
						$this->body .= " class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_3"]))."\">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_3_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_3] id=optional_field_3 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_3"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_3] id=optional_field_3 class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_3"]))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_3_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_3_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_3 name=b[optional_field_3_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_3"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_3_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_3_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_3_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_3_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_4)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[945]);
				if ($this->error_variables["optional_field_4"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[946])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_4_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_4"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_4_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_4] id=optional_field_4 ";
						if ($this->ad_configuration_data->OPTIONAL_4_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_4_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_4_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_4_LENGTH;
						$this->body .= " class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_4"]))."\">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_4_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_4] id=optional_field_4 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_4"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_4] id=optional_field_4 class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_4"]))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_4_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_4_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_4 name=b[optional_field_4_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_4"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_4_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_4_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_4_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_4_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_5)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[947]);
				if ($this->error_variables["optional_field_5"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[948])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_5_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_5"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_5_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_5] id=optional_field_5 ";
						if ($this->ad_configuration_data->OPTIONAL_5_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_5_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_5_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_5_LENGTH;
						$this->body .= " class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_5"]))."\">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_5_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_5] id=optional_field_5 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_5"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_5] id=optional_field_5 class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_5"]))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_5_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_5_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_5 name=b[optional_field_5_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_5"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_5_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_5_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_5_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_5_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_6)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[949]);
				if ($this->error_variables["optional_field_6"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[950])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_6_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_6"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_6_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_6] id=optional_field_6 ";
						if ($this->ad_configuration_data->OPTIONAL_6_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_6_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_6_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_6_LENGTH;
						$this->body .= " class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_6"]))."\">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_6_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_6] id=optional_field_6 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_6"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_6] id=optional_field_6 class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_6"]))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_6_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_6_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_6 name=b[optional_field_6_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_6"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_6_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_6_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_6_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_6_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_7)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[951]);
				if ($this->error_variables["optional_field_7"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[952])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_7_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_7"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_7_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_7] id=optional_field_7 ";
						if ($this->ad_configuration_data->OPTIONAL_7_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_7_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_7_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_7_LENGTH;
						$this->body .= " class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_7"]))."\">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_7_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_7] id=optional_field_7 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_7"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_7] id=optional_field_7 value=\"".stripslashes(urldecode($this->classified_variables["optional_field_7"]))."\" class=place_an_ad_details_data>\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_7_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_7_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_7 name=b[optional_field_7_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_7"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_7_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_7_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_7_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_7_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_8)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[953]);
				if ($this->error_variables["optional_field_8"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[954])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_8_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_8"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_8_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_8] id=optional_field_8 ";
						if ($this->ad_configuration_data->OPTIONAL_8_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_8_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_8_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_8_LENGTH;
						$this->body .= " value=\"".stripslashes(urldecode($this->classified_variables["optional_field_8"]))."\" class=place_an_ad_details_data>\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_8_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_8] id=optional_field_8 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_8"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_8] id=optional_field_8 value=\"".stripslashes(urldecode($this->classified_variables["optional_field_8"]))."\" class=place_an_ad_details_data>\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_8_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_8_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_8 name=b[optional_field_8_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_8"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_8_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_8_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_8_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_8_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_9)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[955]);
				if ($this->error_variables["optional_field_9"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[956])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_9_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_9"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_9_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_9] id=optional_field_9 ";
						if ($this->ad_configuration_data->OPTIONAL_9_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_9_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_9_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_9_LENGTH;
						$this->body .= " value=\"".stripslashes(urldecode($this->classified_variables["optional_field_9"]))."\" class=place_an_ad_details_data>\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_9_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_9] id=optional_field_9 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_9"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_9] id=optional_field_9 value=\"".stripslashes(urldecode($this->classified_variables["optional_field_9"]))."\" class=place_an_ad_details_data>\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_9_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_9_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_9 name=b[optional_field_9_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_9"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_9_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_9_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_9_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_9_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_10)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[957]);
				if ($this->error_variables["optional_field_10"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[958])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_10_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_10"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_10_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_10] id=optional_field_10 ";
						if ($this->ad_configuration_data->OPTIONAL_10_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_10_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_10_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_10_LENGTH;
						$this->body .= " value=\"".stripslashes(urldecode($this->classified_variables["optional_field_10"]))."\" class=place_an_ad_details_data>\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_10_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_10] id=optional_field_10 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_10"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_10] id=optional_field_10 value=\"".stripslashes(urldecode($this->classified_variables["optional_field_10"]))."\" class=place_an_ad_details_data>\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_10_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_10_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_10 name=b[optional_field_10_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_10"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_10_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_10_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_10_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_10_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_11)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1903]);
				if ($this->error_variables["optional_field_11"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1904])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_11_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_11"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_11_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_11] id=optional_field_11 ";
						if ($this->ad_configuration_data->OPTIONAL_11_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_11_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_11_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_11_LENGTH;
						$this->body .= " class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_11"]))."\">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_11_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_11] id=optional_field_1 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_11"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_11] id=optional_field_1 class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_11"]))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_11_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_11_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_11 name=b[optional_field_11_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_11"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_11_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_11_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_11_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_11_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_12)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1905]);
				if ($this->error_variables["optional_field_12"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1906])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_12_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_12"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_12_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_12] id=optional_field_12 ";
						if ($this->ad_configuration_data->OPTIONAL_12_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_12_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_12_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_12_LENGTH;
						$this->body .= " class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_12"]))."\">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_12_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_12] id=optional_field_12 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_12"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_12] id=optional_field_2 class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_12"]))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_12_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_12_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_12 name=b[optional_field_12_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_12"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_12_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_12_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_12_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_12_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_13)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1907]);
				if ($this->error_variables["optional_field_13"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1908])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_13_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_13"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_13_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_13] id=optional_field_13 ";
						if ($this->ad_configuration_data->OPTIONAL_13_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_13_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_13_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_13_LENGTH;
						$this->body .= " class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_13"]))."\">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_13_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_13] id=optional_field_13 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_13"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_13] id=optional_field_13 class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_13"]))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_13_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_13_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_3 name=b[optional_field_13_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_13"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_13_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_13_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_13_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_13_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_14)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1909]);
				if ($this->error_variables["optional_field_14"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1910])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_14_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_14"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_14_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_14] id=optional_field_14 ";
						if ($this->ad_configuration_data->OPTIONAL_14_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_14_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_14_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_14_LENGTH;
						$this->body .= " class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_14"]))."\">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_14_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_14] id=optional_field_14 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_14"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_14] id=optional_field_14 class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_14"]))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_14_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_14_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_14 name=b[optional_field_14_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_14"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_14_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_14_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_14_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_14_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_15)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1911]);
				if ($this->error_variables["optional_field_15"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1912])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_15_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_15"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_15_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_15] id=optional_field_15 ";
						if ($this->ad_configuration_data->OPTIONAL_15_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_15_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_15_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_15_LENGTH;
						$this->body .= " class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_15"]))."\">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_15_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_15] id=optional_field_15 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_15"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_15] id=optional_field_15 class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_15"]))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_15_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_15_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_15 name=b[optional_field15_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_15"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_15_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_15_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_15_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_15_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_16)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1913]);
				if ($this->error_variables["optional_field_16"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1914])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_16_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_16"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_16_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_16] id=optional_field_16 ";
						if ($this->ad_configuration_data->OPTIONAL_16_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_16_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_16_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_16_LENGTH;
						$this->body .= " class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_16"]))."\">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_16_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_16] id=optional_field_16 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_16"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_16] id=optional_field_16 class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_16"]))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_16_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_16_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_16 name=b[optional_field_16_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_16"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_16_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_16_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_16_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_16_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_17)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1915]);
				if ($this->error_variables["optional_field_17"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1916])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_17_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_17"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_17_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_17] id=optional_field_17 ";
						if ($this->ad_configuration_data->OPTIONAL_17_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_17_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_17_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_17_LENGTH;
						$this->body .= " class=place_an_ad_details_data value=\"".stripslashes(urldecode($this->classified_variables["optional_field_17"]))."\">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_17_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_17] id=optional_field_17 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_17"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_17] id=optional_field_7 value=\"".stripslashes(urldecode($this->classified_variables["optional_field_17"]))."\" class=place_an_ad_details_data>\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_17_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_17_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_17 name=b[optional_field_17_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_17"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_17_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_17_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_17_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_17_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_18)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1917]);
				if ($this->error_variables["optional_field_18"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1918])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_18_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_18"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_18_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_18] id=optional_field_18 ";
						if ($this->ad_configuration_data->OPTIONAL_18_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_18_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_18_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_18_LENGTH;
						$this->body .= " value=\"".stripslashes(urldecode($this->classified_variables["optional_field_18"]))."\" class=place_an_ad_details_data>\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_18_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_18] id=optional_field_18 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_18"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_18] id=optional_field_18 value=\"".stripslashes(urldecode($this->classified_variables["optional_field_18"]))."\" class=place_an_ad_details_data>\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_18_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_18_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_18 name=b[optional_field_18_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_18"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_18_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_18_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_18_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_18_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_19)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1919]);
				if ($this->error_variables["optional_field_19"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1920])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_19_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_19"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_19_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_19] id=optional_field_19 ";
						if ($this->ad_configuration_data->OPTIONAL_19_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_19_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_19_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_19_LENGTH;
						$this->body .= " value=\"".stripslashes(urldecode($this->classified_variables["optional_field_19"]))."\" class=place_an_ad_details_data>\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_19_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_19] id=optional_field_19 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_19"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_19] id=optional_field_19 value=\"".stripslashes(urldecode($this->classified_variables["optional_field_19"]))."\" class=place_an_ad_details_data>\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_19_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_19_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_19 name=b[optional_field_19_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_19"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_19_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_19_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_19_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_19_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_20)
			{
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1921]);
				if ($this->error_variables["optional_field_20"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1922])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_20_filter_association']))
				{
					$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_20"]));
				}
				else
				{
					if (!$this->ad_configuration_data->OPTIONAL_20_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=b[optional_field_20] id=optional_field_20 ";
						if ($this->ad_configuration_data->OPTIONAL_20_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_20_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_20_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_20_LENGTH;
						$this->body .= " value=\"".stripslashes(urldecode($this->classified_variables["optional_field_20"]))."\" class=place_an_ad_details_data>\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_20_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=b[optional_field_20] id=optional_field_20 class=place_an_ad_details_data>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if ($this->classified_variables["optional_field_20"] == $show_dropdown->VALUE)
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_20] id=optional_field_20 value=\"".stripslashes(urldecode($this->classified_variables["optional_field_20"]))."\" class=place_an_ad_details_data>\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_20_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_20_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_20 name=b[optional_field_20_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($this->classified_variables["optional_field_20"]));
						$this->body .= "\" class=place_an_ad_details_data ";
						if ($this->ad_configuration_data->OPTIONAL_20_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_20_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_20_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_20_LENGTH;
						$this->body .= ">\n\t";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}


			//get and display category questions
			$this->get_category_questions($db,$this->terminal_category);
			$this->display_category_questions($db);

			//get and display group questions
			$this->get_group_questions($db,$this->users_group);
			$this->display_group_questions($db);

			if (($this->field_configuration_data->USE_MAPPING_ADDRESS_FIELD) ||
				($this->field_configuration_data->USE_MAPPING_CITY_FIELD) ||
				($this->field_configuration_data->USE_MAPPING_STATE_FIELD) ||
				($this->field_configuration_data->USE_MAPPING_COUNTRY_FIELD) ||
				($this->field_configuration_data->USE_MAPPING_ZIP_FIELD))
			{
				$this->body .="<tr>\n\t<td td class=place_an_ad_details_fields colspan=2>".urldecode($this->messages[1622])."</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t<td colspan=2>".urldecode($this->messages[1623])."\n\t</td>\n</tr>\n";
			}

			//mapping address location
			if ($this->field_configuration_data->USE_MAPPING_ADDRESS_FIELD)
			{
				//if (strlen($this->classified_variables["mapping_address"]) == 0)
					//$mapping_address_data = urldecode($user_data->ADDRESS." ".$user_data->ADDRESS2);
				//else
					$mapping_address_data = $this->classified_variables["mapping_address"];
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1617])."</td>";
				$this->body .="<td class=place_an_ad_details_data>\n\t<input type=text name=b[mapping_address] value=\"".$mapping_address_data."\" class=place_an_ad_details_data>\n\t";
				$this->body .="</td>\n</tr>\n";
			}

			//mapping city location
			if ($this->field_configuration_data->USE_MAPPING_CITY_FIELD)
			{
				//if (strlen($this->classified_variables["mapping_city"]) == 0)
					//$mapping_city_data = urldecode($user_data->CITY);
				//else
					$mapping_city_data = $this->classified_variables["mapping_city"];
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1618])."</td>";
				$this->body .="<td class=place_an_ad_details_data>\n\t<input type=text name=b[mapping_city] value=\"".$mapping_city_data."\" class=place_an_ad_details_data>\n\t";
				$this->body .="</td>\n</tr>\n";
			}

			//mapping state
			if ($this->field_configuration_data->USE_MAPPING_STATE_FIELD)
			{
				//if (strlen($this->classified_variables["mapping_state"]) == 0)
					//$state_data = $user_data->STATE;
				//else
					$state_data = $this->classified_variables["mapping_state"];
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1619])."</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				$this->sql_query = "SELECT * FROM ".$this->states_table." order by name";
				$state_result = $db->Execute($this->sql_query);
				if (!$state_result)
					return false;
				else
				{
					$this->body .="<select name=b[mapping_state] class=place_an_ad_details_data>\n\t\t";
					$this->body .="<option value=none>".urldecode($this->messages[117])."</option>\n\t\t";
					while ($show = $state_result->FetchNextObject()) {
						//spit out the state list
						$this->body .="<option value=\"".$show->ABBREVIATION."\"";
						if (trim(urldecode($state_data)) == trim($show->ABBREVIATION))
						$this->body .="selected";
						$this->body .=">".$show->NAME."\n\t\t";
					}

					$this->body .="</select>\n\t";
				}
				$this->body .="</td>\n</tr>\n";
			}

			//mapping country
			if ($this->field_configuration_data->USE_MAPPING_COUNTRY_FIELD)
			{
				//if (strlen($this->classified_variables["mapping_country"]) == 0)
					//$country_data = $user_data->COUNTRY;
				//else
					$country_data = $this->classified_variables["mapping_country"];
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1620])."</td>\n\t";
				$this->body .="<td  class=place_an_ad_details_data>\n\t";
				$this->sql_query = "SELECT * FROM ".$this->countries_table." order by display_order, name";
				$country_result = $db->Execute($this->sql_query);
				if (!$country_result)
					return false;
				else
				{
					$this->body .="<select name=b[mapping_country] class=place_an_ad_details_data>\n\t\t";
					$this->body .="<option value=none>".urldecode($this->messages[1209])."</option>\n\t\t";
					while ($show = $country_result->FetchNextObject()) {
						//spit out the country list
						$this->body .="<option ";
						if ((urldecode($country_data) == $show->ABBREVIATION) || (urldecode($country_data) == $show->NAME))
							$this->body .="selected";
						$this->body .=">".$show->NAME."</option>\n\t\t";
					}

					$this->body .="</select>\n\t";
				}
				$this->body .="</td>\n</tr>\n";
			}

			//mapping zip location
			if ($this->field_configuration_data->USE_MAPPING_ZIP_FIELD)
			{
				//if (strlen($this->classified_variables["mapping_zip"]) == 0)
					//$mapping_zip_code_data = urldecode($user_data->ZIP);
				//else
					$mapping_zip_code_data = $this->classified_variables["mapping_zip"];
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[1621])."</td>";
				$this->body .="<td  class=place_an_ad_details_data>\n\t<input type=text name=b[mapping_zip] value=\"".$mapping_zip_code_data."\" class=place_an_ad_details_data>\n\t";
				$this->body .="</td>\n</tr>\n";
			}

			$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[114])."\n\t";
			if (strlen(trim($this->error_variables["description"])) > 0)
				$this->body .="<br><font class=error_message>".urldecode($this->messages[120])."</font>\n\t";
			$this->body .="</td>\n";
			$this->body .="\n\t<td class=place_an_ad_details_data>";
			if ($this->configuration_data['use_rte'])
			{
				$rte_description = str_replace(chr(13), "", chr(13).$this->classified_variables["description"]);
				//$rte_description = str_replace(chr(34), "", $this->classified_variables["description"]);
				$this->LoadRTE("b[description]", urldecode($rte_description), 405, 300, 1, 1);

			}
			else
			{
				$this->body .="\n\t<textarea name=b[description] cols=60 rows=15 class=place_an_ad_details_data ";
				if ($this->ad_configuration_data->TEXTAREA_WRAP)
				{
					$this->body .= "wrap=virtual>";
					if (strlen(trim($this->classified_variables["description"])) > 0)
						$this->body .= eregi_replace('<BR[[:space:]]*/?[[:space:]]*>',"",stripslashes(urldecode($this->classified_variables["description"])));
				}
				else
				{
					$this->body .= "wrap=soft>";
					if (strlen(trim($this->classified_variables["description"])) > 0)
						$this->body .= stripslashes(urldecode($this->classified_variables["description"]));
				}
				$this->body .=" </textarea>";
			}
			$this->body .= "</font>\n\t</td>\n</tr>\n";
			if($this->field_configuration_data->USE_BUY_NOW)
			{
				$this->body .= "
					<tr>
						<td class=place_an_ad_details_fields>".urldecode($this->messages[3279])."<br>
							<div style='width:85%;padding-left:2%;'><font color=#999999 face='Arial, Helvetica, sans-serif' size=2>".urldecode($this->messages[3280])."</font></div>";
				if($this->error_variables["paypal_id"])
					$this->body .= "<br><font class=error_message>".$this->messages[3281]."</font>";
				$this->body .= "</td>
						<td><input type=text class=place_an_ad_details_data name=b[paypal_id] value='{$this->classified_variables["paypal_id"]}'></td>
					</tr>";
			}

			//STOREFRONT CODE
			if(file_exists('classes/storefront/store_class.php'))
			{
				include_once('classes/storefront/store_class.php');

				$this->sql_query = "select * from ".Store::get('storefront_subscriptions_table')." where user_id = ".$this->classified_user_id;
				$subscriptionResult = $db->Execute($this->sql_query);

				$this->sql_query = "select * from ".Store::get('storefront_categories_table')." where user_id = '".$this->classified_user_id."' order by display_order asc";
				$categoryResults = $db->Execute($this->sql_query);

				if($subscriptionResult->RecordCount()==1&&$categoryResults->RecordCount())
				{
					$subscriptionInfo = $subscriptionResult->FetchRow();
					$expiresAt = $this->shifted_time($db) + $subscriptionInfo["expiration"];
					if(time()<=$expiresAt)
					{

						$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".urldecode($this->messages[500002])."</td>\n\t";
						$this->body .="<td  class=place_an_ad_details_data>\n\t";
						$this->body .="<select name=b[storefront_category] class=place_an_ad_details_data>\n\t\t";
						while ($show = $categoryResults->FetchRow())
						{
							$this->body .="<option value=\"".$show["category_id"]."\"";
							$this->body .=">".stripslashes($show["category_name"])."\n\t\t";
						}

						$this->body .="</select>\n\t";
						$this->body .="</td>\n</tr>\n";

					}
				}
			}
			//STOREFRONT CODE

			$this->body .="<tr>\n\t<td colspan=2 class=submit_button><input type=submit name=submit value=\"".urldecode($this->messages[641])."\"class=submit_button>\n\t</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td class=end_sell_process_link colspan=2>\n\t<br><br><a href=".trim($this->configuration_data['classifieds_url'])."?a=98 class=end_sell_process_link>".urldecode($this->messages[113])."</a>\n\t</td>\n</tr>\n";
			$this->body .="</table>\n</form>";

			$this->display_page($db);
			return true;
		}
		else
		{
			//no user id
			$this->display_page($db);
			return false;
		}
	} //end of function display_classified_detail_form ()

//#####################################################################

	function get_this_price_plan($db)
	{
		$base_price_plan = $this->get_price_plan($db,$this->users_price_plan);
		if ($base_price_plan->TYPE_OF_BILLING == 1)
		{
			if ($this->terminal_category)
			{
				$category_id = $this->terminal_category;
				do {
					$this->sql_query = "select * from ".$this->price_plans_categories_table." where
						price_plan_id = ".$this->users_price_plan." and category_id = ".$category_id;
					$category_price_plan_result = $db->Execute($this->sql_query);
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					if (!$category_price_plan_result)
					{
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						return false;
					}
					elseif ($category_price_plan_result->RecordCount() == 1)
					{
						$show_price_plan = $category_price_plan_result->FetchNextObject();
						break;
					}
					else
						$show_price_plan = 0;
					if (!$show_price_plan)
					{
						//get category parent
						$this->sql_query = "select parent_id from ".$this->categories_table." where category_id = ".$category_id;
						$category_result = $db->Execute($this->sql_query);
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						if (!$category_result)
						{
							if ($this->debug_sell) echo $this->sql_query."<br>\n";
							return false;
						}
						elseif ($category_result->RecordCount() == 1)
						{
							$show_category = $category_result->FetchNextObject();
							$category_id = $show_category->PARENT_ID;
						}
						else
						{
							if ($this->debug_sell) echo $this->sql_query."<br>\n";
							return false;
						}
					}
					//check all the way to the main category
				} while ($category_id != 0);

				if (!$show_price_plan)
				{
					$this->price_plan = $base_price_plan;
					if ($this->debug_sell)
					{
						echo "using base price plan<bR>\n";
						echo $base_price_plan->USE_FEATURED_ADS." is USE_FEATURED_ADS<br>";
						echo $base_price_plan->USE_FEATURED_ADS_LEVEL_2." is USE_FEATURED_ADS_LEVEL_2<br>";
						echo $base_price_plan->USE_FEATURED_ADS_LEVEL_3." is USE_FEATURED_ADS_LEVEL_3<br>";
						echo $base_price_plan->USE_FEATURED_ADS_LEVEL_4." is USE_FEATURED_ADS_LEVEL_4<br>";
						echo $base_price_plan->USE_FEATURED_ADS_LEVEL_5." is USE_FEATURED_ADS_LEVEL_5<br>";
					}
					return $base_price_plan;
				}
				else
				{
					$show_price_plan->TYPE_OF_BILLING = $base_price_plan->TYPE_OF_BILLING;
					$show_price_plan->NUM_FREE_PICS = $base_price_plan->NUM_FREE_PICS;
					if ($this->debug_sell)
					{
						echo "using base price plan<bR>\n";
						echo $show_price_plan->TYPE_OF_BILLING." is TYPE_OF_BILLING<br>";
						echo $show_price_plan->NUM_FREE_PICS." is NUM_FREE_PICS<br>";
					}

				}
				$this->price_plan = $show_price_plan;
				return $show_price_plan;
			}
		}
		elseif ($base_price_plan->TYPE_OF_BILLING == 2)
		{
			//subscription based
			$this->price_plan = $base_price_plan;
			return $base_price_plan;
		}
		else
		{
			if ($this->debug_sell) echo "returning false at the bottom of get_this_price_plan<br>\n";
			return false;
		}
	} //end of function get_this_price_plan

//#####################################################################

	function display_basic_duration_dropdown($db)
	{
		//check for category specific dropdown lengths first
		$current_category = $this->terminal_category;
		do {
			$this->sql_query = "select * from ".$this->price_plan_lengths_table." where category_id = ".$current_category."
				and price_plan_id = 0 order by length_of_ad asc";
			$category_duration_result = $db->Execute($this->sql_query);
			IF ($this->debug_sell) echo $this->sql_query."<br>";
			if (!$category_duration_result)
			{
				IF ($this->debug_sell) echo $this->sql_query."<br>";
				return false;
			}
			elseif ($category_duration_result->RecordCount() == 0)
			{
				//get parent category
				$this->sql_query = "select parent_id from ".$this->categories_table." where category_id = ".$current_category;
				$parent_result = $db->Execute($this->sql_query);
				IF ($this->debug_sell) echo $this->sql_query."<br>";
				if (!$parent_result)
				{
					IF ($this->debug_sell) echo $this->sql_query."<br>";
					return false;
				}
				elseif ($parent_result->RecordCount() == 1)
				{
					$show_parent = $parent_result->FetchNextObject();
					$current_category = $show_parent->PARENT_ID;
				}
				else
					return false;
			}
		} while (($current_category != 0) && ($category_duration_result->RecordCount() == 0));
		if ($category_duration_result->RecordCount() > 0)
		{
			while ($show_durations = $category_duration_result->FetchRow())
			{
				$this->body .= "<option value=".$show_durations["length_of_ad"];
				if ($this->classified_variables["classified_length"] == $show_durations["length_of_ad"])
					   $this->body .= " selected";
				$this->body .= ">".$show_durations["display_length_of_ad"]."</option>";
			}
			return true;
		}
		else
		{
			$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 1 order by numeric_value";
			$duration_result = $db->Execute($this->sql_query);
			IF ($this->debug_sell) echo $this->sql_query."<br>";
			if (!$duration_result)
			{
				IF ($this->debug_sell) echo $this->sql_query."<br>";
				return false;
			}
			elseif ($duration_result->RecordCount() > 0)
			{
				while ($show_durations = $duration_result->FetchRow())
				{
					$this->body .= "<option value=".$show_durations["numeric_value"];
					if ($this->classified_variables["classified_length"] == $show_durations["numeric_value"])
						   $this->body .= " selected";
					$this->body .= ">".$show_durations["display_value"];
					$this->body .= " ".urldecode($this->messages[546])."</option>";
				}
				return true;
			}
			else
			{
				return false;
			}
		}
	} //end of function display_basic_duration_dropdown

//#####################################################################

	function display_classified_image_form($db)
	{
		if ($this->classified_user_id)
		{
			$this->get_ad_configuration($db);
			if (($this->ad_configuration_data->MAXIMUM_PHOTOS > 0)
				&& ($this->ad_configuration_data->ALLOW_URL_REFERENCED
				|| $this->ad_configuration_data->ALLOW_UPLOAD_IMAGES))
			{
				$this->page_id = 10;
				$this->get_text($db);
				$this->body ="<form action=".$this->configuration_data['classifieds_file_name']."?a=1&set_images=1 method=post enctype=multipart/form-data name=imageForm>\n";
				//$this->body .="<input type=hidden name=MAX_FILE_SIZE value=\"".$this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE."\">\n";
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
				$this->body .="<tr class=place_ad_section_title>\n\t<td>\n\t".urldecode($this->messages[610])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_title>\n\t<td>\n\t".urldecode($this->messages[161])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[167])."\n\t</td>\n</tr>\n";
				
				if (strlen($this->images_error) > 0)
					$this->body .="<tr class=error_message>\n\t<td>".$this->images_error."\n\t</td>\n</tr>\n";
				
				$not_keys_yet = array();
				for ($n=1;$n<=$this->ad_configuration_data->MAXIMUM_PHOTOS;$n++)
				{
					if (!$this->in_array_key($n, $this->images_captured))
					{
						array_push($not_keys_yet,$n);
					}
				}
				
				if ((is_array($this->images_captured)) && (count($this->images_captured) > 0))
				{
					$this->body .="<tr class=place_an_ad_instructions>\n\t<td>\n\t".urldecode($this->messages[407])."<br>";
					$this->show_sell_images($db,1);
					$this->body .="</td>\n</tr>\n";
				}
				reset($not_keys_yet);
				
				if(is_dir('classes/aurigma/'))
				{
					if(!isset($_COOKIE["useAdvancedCookie"]))
					{
						$defaultUploader = $this->ad_configuration_data->IMAGE_UPLOADER_DEFAULT;
						setcookie('useAdvancedCookie',$this->ad_configuration_data->IMAGE_UPLOADER_DEFAULT,time()+60*60*24*120);
					}else{
						$defaultUploader = $_COOKIE["useAdvancedCookie"];
						if($_REQUEST["useSimple"]==1)
						{
							$defaultUploader = 0;
							setcookie('useAdvancedCookie',0,time()+60*60*24*120);
						}elseif($_REQUEST["useAdvanced"]==1)
						{
							$defaultUploader = 1;
							setcookie('useAdvancedCookie',1,time()+60*60*24*120);
						}
					}
				}else{
					$this->ad_configuration_data->CLIENTSIDE_IMAGE_UPLOADER = false;
				}
				
				$this->body .="<tr>\n\t<td>\n\t<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
				if (($this->ad_configuration_data->ALLOW_UPLOAD_IMAGES) && ($this->ad_configuration_data->ALLOW_URL_REFERENCED))
				{
					if($this->ad_configuration_data->CLIENTSIDE_IMAGE_UPLOADER&&$defaultUploader)
					{
						$this->body .="<tr><td class=image_upload_size>".urldecode($this->messages[643])." ".$this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE."\n\t</td></tr>";						
						$this->body .="<tr><td align=center class=image_upload_field_labels id=imageUploaderContainer>";
						include_once('classes/aurigma/include_aurigma.php');
						$this->body .= "</td></tr>";
					}else
					//show the url or image upload boxes
					if ($this->ad_configuration_data->MAXIMUM_PHOTOS > count($this->images_captured))
					{
						$this->body .="<tr class=url_upload_field_labels>\n\t<td>".urldecode($this->messages[166])."\n\t</td>\n\t";
						$this->body .="<td class=image_upload_field_labels >".urldecode($this->messages[169])."\n\t</td>\n</tr>\n";
						$this->body .="<tr class=image_upload_size>\n\t<td>&nbsp;</td><td class=image_upload_size>".urldecode($this->messages[643])." ".$this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE."\n\t</td>\n</tr>\n";
						foreach ($not_keys_yet as $value)
						//for ($i=1;$i <= $this->ad_configuration_data->MAXIMUM_PHOTOS - count($this->images_captured);$i++)
						{
							$this->body .="<tr>\n\t<td class=url_upload_field_labels>".$value.") <input type=text name=c[".$value."][url][location] size=35 maxlength=100></font>";
							if ($value == 1)
								$this->body .= "*";
							$this->body .= "\n\t</td>\n\t";
							$this->body .="<td class=image_upload_field_labels >".$value.") <input type=file name=d[".$value."]>";
							if ($value == 1)
								$this->body .= "*";
							$this->body .= "</td>\n</tr>\n";
							$this->body .="<tr>\n\t<td class=url_upload_field_labels><input type=text name=c[".$value."][url][text] size=35 maxlength=100></font>\n\t</td>\n\t";
							$this->body .="<td class=image_upload_field_labels ><input type=text name=c[".$value."][text] size=35 maxlength=100></td>\n</tr>\n";

						}
					}
				}
				elseif (($this->ad_configuration_data->ALLOW_URL_REFERENCED) && (!$this->ad_configuration_data->ALLOW_UPLOAD_IMAGES))
				{
					if ($this->ad_configuration_data->MAXIMUM_PHOTOS > count($this->images_captured))
					{
						//show only the image url choices
						$this->body .="<tr  class=url_upload_field_labels>\n\t<td>".urldecode($this->messages[166])."\n\t</td>\n</tr>\n";
						//for ($i=1;$i <= $this->ad_configuration_data->MAXIMUM_PHOTOS - count($this->images_captured);$i++)
						foreach ($not_keys_yet as $value)
						{
							$this->body .="<tr>\n\t<td  class=url_upload_field_labels>".$value.") <input type=text name=c[".$value."][url][location] size=35 maxlength=100>";
							if ($value == 1)
								$this->body .= "*";
							$this->body .= " <input type=text name=c[".$value."][url][text] size=35 maxlength=100></font>\n\t</td>\n</tr>\n";
						}
					}
				}
				elseif (($this->ad_configuration_data->ALLOW_UPLOAD_IMAGES) && (!$this->ad_configuration_data->ALLOW_URL_REFERENCED))
				{
					if($this->ad_configuration_data->CLIENTSIDE_IMAGE_UPLOADER&&$defaultUploader)
					{
						$this->body .="<tr><td class=image_upload_size>".urldecode($this->messages[643])." ".$this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE."\n\t</td></tr>";						
						$this->body .="<tr><td align=center class=image_upload_field_labels id=imageUploaderContainer>";
						include_once('classes/aurigma/include_aurigma.php');
						$this->body .= "</td></tr>";
					}else
					if ($this->ad_configuration_data->MAXIMUM_PHOTOS > count($this->images_captured))
					{
						
						$this->body .="<tr><td class=image_upload_size>".urldecode($this->messages[643])." ".$this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE."\n\t</td></tr>";
						$this->body .="<tr>\n\t<td class=image_upload_field_labels >".urldecode($this->messages[169])."\n\t</td>\n</tr>\n";
						foreach ($not_keys_yet as $value)
						//for ($i=1;$i <= $this->ad_configuration_data->MAXIMUM_PHOTOS - count($this->images_captured);$i++)
						{
							$this->body .="<tr>\n\t<td align=center class=image_upload_field_labels>\n\t".$value.") <input type=file name=d[".$value."]>";
							if ($value == 1)
								$this->body .= "*";
							$this->body .= " <input type=text name=c[".$value."][text] size=35 maxlength=100></td>\n</tr>\n";
						}
					}
				}
				$this->body .="</table>\n\t</td>\n</tr>\n";
				
				if($this->ad_configuration_data->CLIENTSIDE_IMAGE_UPLOADER&&$defaultUploader)
				{
					$this->body .="<tr>\n\t<td class=enter_images_button><a href='".$this->configuration_data['classifieds_file_name']."?a=1&set_images=1&useSimple=1'>".urldecode($this->messages[200173])."</a>\n\t</td>\n</tr>\n";
					$this->body .="<tr>\n\t<td class=enter_images_button><input type=button name=submit value=\"".urldecode($this->messages[170])."\" class=enter_images_button onclick='javascript: getImageUploader(\"ImageUploader\").Send(); this.disabled = true;'>\n\t</td>\n</tr>\n";
				} else {
					if(is_dir('classes/aurigma/')&&$this->ad_configuration_data->CLIENTSIDE_IMAGE_UPLOADER)
					{
						$this->body .="<tr>\n\t<td class=enter_images_button><a href='".$this->configuration_data['classifieds_file_name']."?a=1&set_images=1&useAdvanced=1'>".urldecode($this->messages[200174])."</a>\n\t</td>\n</tr>\n";
					}
					$this->body .="<tr>\n\t<td class=enter_images_button><input type=submit name=submit value=\"".urldecode($this->messages[170])."\" class=enter_images_button>\n\t</td>\n</tr>\n";
				}
				$this->body .="<tr>\n\t<td class=enter_no_images_button><input type=submit name=c[no_images] value=\"".urldecode($this->messages[171])."\" class=enter_no_images_button>\n\t</td>\n</tr>\n";
				if (count($this->images_captured) > 0)
					$this->body .="<tr>\n\t<td class=enter_no_images_button><input type=submit name=c[no_images] value=\"".urldecode($this->messages[174])."\" class=enter_no_images_button>\n\t</td>\n</tr>\n";
				$this->body .="<tr>\n\t<td class=end_sell_process_link colspan=2>\n\t<br><br><a href=".trim($this->configuration_data['classifieds_url'])."?a=98 class=end_sell_process_link>".urldecode($this->messages[165])."</a>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n</form>";

			}
			else
			{

				//echo  "images done<Br>\n";
				$this->update_images_collected($db,1);
				$this->classified_approval_display($db);
				return true;

			}
		}
		else
		{
			//no user id
			return false;
		}

		$this->display_page($db);
		return true;

	} //end of function display_classified_image_form ()

//#####################################################################

	function update_images_collected($db,$classified_images_collected)
	{
		$this->classified_images_collected = $classified_images_collected;
		$this->sql_query = "update ".$this->sell_table." set
			classified_images_collected = ".$classified_images_collected."
			where session=\"".$this->session_id."\"";
		$image_collected_result = $db->Execute($this->sql_query);
		if ($this->debug_sell) echo $this->sql_query."<bR>\n";
		if (!$image_collected_result)
		{
			if ($this->debug_sell)
			{
				echo $db->ErrorMsg()." is errormsg<BR>\n";
				echo $this->sql_query."<bR>\n";
			}
			return false;
		}
		return true;
	} // end of function update_images_collected

//#####################################################################

	function update_check_splash($db)
	{
		$this->check_splash = 1;
		$this->sql_query = "update ".$this->sell_table." set
			check_splash = 1
			where session=\"".$this->session_id."\"";
		$splash_checked_result = $db->Execute($this->sql_query);
		if ($this->debug_sell) echo $this->sql_query."<bR>\n";
		if (!$splash_checked_result)
		{
			if ($this->debug_sell)
			{
				echo $db->ErrorMsg()." is errormsg<BR>\n";
				echo $this->sql_query."<bR>\n";
			}
			return false;
		}
		return true;
	} // end of function update_check_splash

//#####################################################################

	function update_classified_details_collected($db,$classified_details_collected)
	{
		$this->classified_details_collected = $classified_details_collected;
		$this->sql_query = "update ".$this->sell_table." set
			classified_details_collected = ".$classified_details_collected."
			where session=\"".$this->session_id."\"";
		$detail_collected_result = $db->Execute($this->sql_query);
		if ($this->debug_sell) echo $this->sql_query."<bR>\n";
		if (!$detail_collected_result)
		{
			if ($this->debug_sell)
			{
				echo $db->ErrorMsg()." is errormsg<BR>\n";
				echo $this->sql_query."<bR>\n";
			}
			return false;
		}
		return true;
	} // end of function update_classified_details_collected

//#####################################################################

	function update_billing_approved($db,$billing_approved)
	{
		$this->billing_approved = $billing_approved;
		$this->sql_query = "update ".$this->sell_table." set
			billing_approved = ".$billing_approved."
			where session=\"".$this->session_id."\"";
		$billing_approved_result = $db->Execute($this->sql_query);
		if ($this->debug_sell) echo $this->sql_query."<bR>\n";
		if (!$billing_approved_result)
		{
			if ($this->debug_sell)
			{
				echo $db->ErrorMsg()." is errormsg<BR>\n";
				echo $this->sql_query."<bR>\n";
			}
			return false;
		}
		return true;
	} // end of function update_billing_approved

//#####################################################################

	function update_final_approval($db,$final_approval)
	{
		if ($this->debug_sell) echo "TOP OF UPDATE_FINAL_APPROVAL<br>\n";
		$this->final_approval = $final_approval;
		$this->sql_query = "update ".$this->sell_table." set
			final_approval = ".$final_approval."
			where session=\"".$this->session_id."\"";
		$final_approval_result = $db->Execute($this->sql_query);
		if ($this->debug_sell) echo $this->sql_query."<bR>\n";
		if (!$final_approval_result)
		{
			if ($this->debug_sell)
			{
				echo $db->ErrorMsg()." is errormsg<BR>\n";
				echo $this->sql_query."<bR>\n";
			}
			return false;
		}
		return true;
	} // end of function update_final_approval

//#####################################################################

	function update_classified_id($db,$classified_id)
	{
		$this->classified_id = $classified_id;
		$this->sql_query = "update ".$this->sell_table." set
			classified_id = ".$classified_id."
			where session=\"".$this->session_id."\"";
		$classified_id_result = $db->Execute($this->sql_query);
		if ($this->debug_sell) echo $this->sql_query."<bR>\n";
		if (!$classified_id_result)
		{
			if ($this->debug_sell)
			{
				echo $db->ErrorMsg()." is errormsg<BR>\n";
				echo $this->sql_query."<bR>\n";
			}
			return false;
		}
		return true;
	} // end of function update_classified_id

//#####################################################################

	function update_terminal_category($db,$terminal_category)
	{
		$this->terminal_category = $terminal_category;
		$this->sql_query = "update ".$this->sell_table." set
			terminal_category = ".$terminal_category."
			where session=\"".$this->session_id."\"";
		$terminal_category_result = $db->Execute($this->sql_query);
		if ($this->debug_sell) echo $this->sql_query."<bR>\n";
		if (!$terminal_category_result)
		{
			if ($this->debug_sell)
			{
				echo $db->ErrorMsg()." is errormsg<BR>\n";
				echo $this->sql_query."<bR>\n";
			}
			return false;
		}
		return true;
	} // end of function update_terminal_category

//#####################################################################

	function update_classified_approved($db,$classified_approved)
	{
		if ($this->debug_sell) echo "TOP OF UPDATE_CLASSIFIED_APPROVED<br>\n";
		$this->classified_approved = $classified_approved;
		$this->sql_query = "update ".$this->sell_table." set
			classified_approved = ".$classified_approved."
			where session=\"".$this->session_id."\"";
		$classified_approved_result = $db->Execute($this->sql_query);
		if ($this->debug_sell) echo $this->sql_query."<bR>\n";
		if (!$classified_approved_result)
		{
			if ($this->debug_sell)
			{
				echo $db->ErrorMsg()." is errormsg<BR>\n";
				echo $this->sql_query."<bR>\n";
			}
			return false;
		}
		return true;
	} // end of function update_classified_approved

//#####################################################################

	function process_images($db,$url_info=0,$post_files)
	{
		$this->page_id = 10;
		$this->get_text($db);
		$sell_debug_images = 0;
		$this->get_ad_configuration($db);
		$image_height = ($this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT > $this->ad_configuration_data->LEAD_PICTURE_HEIGHT) ?
			$this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT : $this->ad_configuration_data->LEAD_PICTURE_HEIGHT;
		$image_width = ($this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH > $this->ad_configuration_data->LEAD_PICTURE_WIDTH) ?
			$this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH : $this->ad_configuration_data->LEAD_PICTURE_WIDTH;
		if ($sell_debug_images)
		{
			echo "hello from process_images top<bR>\n";
			echo $url_info["no_images"]." is no images<br>\n";
		}
		//echo $url_info["no_images"]." is no images<br>\n";
		if ($this->classified_user_id)
		{
			if ((strlen(trim($url_info["no_images"])) > 0)&&!$url_info["imageUploader"])
			{
				//no images will be collected for this classified ad
				$this->update_images_collected($db,1);
				return true;
			}elseif ($url_info["imageUploader"]) {
				//aurigma
				$not_keys_yet = array();
				for ($n=1;$n<=$this->ad_configuration_data->MAXIMUM_PHOTOS;$n++)
				{
					if (!$this->in_array_key($n, $this->images_captured))
					{
						array_push($not_keys_yet,$n);
					}
				}
				reset($not_keys_yet);
				for($i=1;$i<=$_POST["FileCount"];$i++)
				{
					$image_position = current($not_keys_yet);
					
					$description = $_POST ['Description_' . $i];
					$thumbnailName1 = "Thumbnail1_" . $i;
					$thumbnailName2 = "Thumbnail2_" . $i;
					
					$size1=$post_files[$thumbnailName1][size];
					if($size1) 
					{ 	
						$fileName1 = $post_files[$thumbnailName1][name];
						$tempName1 = $post_files[$thumbnailName1][tmp_name];
						$type1 = $post_files[$thumbnailName1][type];
						$imageProperties1 = @getimagesize($tempName1);
						$width1 = $imageProperties1[0];
						$height1 = $imageProperties1[1];
					}
					$size2=$post_files[$thumbnailName2][size];
					if($size2) 
					{ 	
						$fileName2 = $post_files[$thumbnailName2][name];
						$tempName2 = $post_files[$thumbnailName2][tmp_name];
						$type2 = $post_files[$thumbnailName2][type];
						$imageProperties2 = @getimagesize($tempName2);
						$width2 = $imageProperties2[0];
						$height2 = $imageProperties2[1];
					}
					if ($this->ad_configuration_data->IMAGE_UPLOAD_SAVE_TYPE == 1)
					{
						//SAVE TO THE DATABASE
						//SHOULDN'T BE USED
						$fp =fopen($tempName2, "r");
						if ($fp)
						{
							$data = fread($fp, $size2);
							$data = addslashes($data);

							if ($size1)
							{
								$thumb_fp =fopen($tempName1, "r");
								if ($thumb_fp)
								{
									$thumb_data = fread($thumb_fp, filesize($tempName1));
									$thumb_data = addslashes($thumb_data);
									$thumb = 1;
								}
								else
								{
									$thumb = 0;
									$thumb_data = 0;
								}
							}
							else
							{
								$thumb = 0;
								$thumb_data = 0;
							}
							
							$this->sql_query = "insert into ".$this->images_table."
								(filesize,filetype,filename,image_text,date_entered,image_width,image_height,original_image_width,original_image_height,thumb,thumb_file,image_file,display_order)
								values
								(".$size1.",\"".$type1."\",\"".$fileName1."\",\"".$description."\",".$this->shifted_time($db).",".$width2.",".$height2.",".$width2.",".$height2.",\"".$thumb."\",\"".$thumb_data."\",\"".$data."\",".$image_position.")";
							$result = $db->Execute($this->sql_query);
							if ($sell_debug_images)
							{
								echo $this->sql_query."<br>\n";
							}
							if (!$result)
							{
								$this->error_message = urldecode($this->messages[57]);
								return false;
							}	
							$this->images_captured[$image_position]["type"] = 2;
							$this->images_captured[$image_position]["id"] = $db->Insert_ID();
							ksort($this->images_captured);
							$this->update_images_collected($db,1);

							$this->sql_query = "insert into ".$this->sell_images_table."
								(session,display_order,image_type,image_id)
								values
								(\"".$this->session_id."\",".$image_position.",2,".$this->images_captured[$image_position]["id"].")";
							$image_insert_result = $db->Execute($this->sql_query);
							if ($sell_debug_images)
							{
								echo $this->sql_query."<br>\n";
							}
							if (!$image_insert_result)
							{
								$this->error_message = urldecode($this->messages[57]);
								return false;
							}
							$this->first_image_filled = 1;
						}
						else
						{
							$this->images_error = urldecode($this->messages[1147]);
						}
					}
					else
					{
						//SAVE IMAGE TO SERVER
						//do thumb first
						//get extension
						switch ($imageProperties1[2])
						{
							case 1: //gif
								$extension = ".gif";
							break;
							case 2: //jpg
								$extension = ".jpg";
							break;
							case 3: //png
								$extension = ".png";
							break;
							case 6: //bmp
								$extension = ".bmp";
							break;
							case 7: //tiff (intel)
								$extension = ".tif";
							break;
							default:
								// Check for accepted types in the database
								$this->sql_query = "select extension from ".$this->file_types_table." where mime_type like \"".$image_dimensions['mime']."\" and accept = 1";
								$result = $db->Execute($this->sql_query);
								if(!$result)
								{
									$extension = 0;
									break;
								}

								if($result->RecordCount() == 0)
								{
									$extension = 0;
									break;
								}
								else
									$file_type = $result->FetchRow();

								$extension = ".".$file_type['extension'];
							break;
						}
						if ($sell_debug_images)
						{
							echo $extension." is the extension<BR>\n";
						}
						if ($extension)
						{
							if ($size1)
							{
								do {
									srand((double)microtime()*1000000);
									$thumb_filename_root = rand(1000000,9999999);
									$thumb_filepath = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$thumb_filename_root.".jpg";
								} while (file_exists($thumb_filepath));
								if ($sell_debug_images)
									echo  $this->ad_configuration_data->PHOTO_QUALITY." is the photo quality<br>\n";
								$image_done = copy($tempName1, $thumb_filepath);
								if ($image_done)
								{
									$thumb_url = $this->ad_configuration_data->URL_IMAGE_DIRECTORY.$thumb_filename_root.".jpg";
									$thumb_filename = $thumb_filename_root.".jpg";
								}
								else
								{
									if ($sell_debug_images)
										echo "image NOT created with imagejpeg<br>\n";
									$thumb_url = 0;
									$thumb_filename = 0;
								}
								if ($sell_debug_images)
								{
									echo $thumb_url." is the thumb url<BR>\n";
									echo $thumb_filename." is the thumb filename<BR>\n";
								}
							}
							else
							{
								$thumb_url = 0;
								$thumb_filename = 0;
							}
							//do full size image
							do {
								srand((double)microtime()*1000000);
								$filename_root = rand(1000000,9999999);
								$filepath = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename_root.$extension;
							} while (file_exists($filepath));
							$full_filename = $filename_root.$extension;
							$full_url = $this->ad_configuration_data->URL_IMAGE_DIRECTORY.$full_filename;
							if ($sell_debug_images)
							{
								echo $filepath." is the filepath within full size image<Br>\n";
								echo $full_url." is the full_url within full size image<Br>\n";
								echo $full_filename." is the full_filename within full size image<Br>\n";
								echo $filepath." is the filepath within full size image<Br>\n";
								echo $filename." is the filename within full size image<Br>\n";
							}
							 if (copy ($tempName2,$filepath))
							 {
								$this->sql_query = "insert into ".$this->images_urls_table."
									(image_url,full_filename,image_text,thumb_url,thumb_filename,file_path,date_entered,image_width,image_height,original_image_width,original_image_height,display_order,filesize,mime_type)
									values
									(\"".$full_url."\",\"".$full_filename."\",\"".$description."\",\"".$thumb_url."\",\"".$thumb_filename."\",\"".$this->ad_configuration_data->IMAGE_UPLOAD_PATH."\",".$this->shifted_time($db).",".$width1.",".$height1.",".$width2.",".$height2.",".$image_position.",".$size2.",\"".$imageProperties1['mime']."\")";
								if ($sell_debug_images)
								{
									echo $this->sql_query."<br>\n";
								}
								$result = $db->Execute($this->sql_query);
								if (!$result)
								{
									$this->error_message = urldecode($this->messages[57]);
									if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
										@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
									return false;
								}
								$this->images_captured[$image_position]["type"] = 1;
								$this->images_captured[$image_position]["id"] = $db->Insert_ID();
								ksort($this->images_captured);
								$this->update_images_collected($db,1);
								$this->sql_query = "insert into ".$this->sell_images_table."
									(session,display_order,image_type,image_id)
									values
									(\"".$this->session_id."\",".$image_position.",1,".$this->images_captured[$image_position]["id"].")";
								$image_insert_result = $db->Execute($this->sql_query);
								if ($sell_debug_images)
								{
									echo $this->sql_query."<br>\n";
								}
								$this->first_image_filled = 1;
								if (!$image_insert_result)
								{
									$this->error_message = urldecode($this->messages[57]);
									if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
										@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
									return false;
								}

							}
							if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
								@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
						} // if ($extension)
						if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
							@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
					}
					next($not_keys_yet);
				}
			}
			else
			{
				$this->get_image_file_types_array($db);
				//process the images entered by the ad poster
				if ($sell_debug_images)
				{
					echo "hello from top of image process loop<br>\n";
					echo $this->ad_configuration_data->MAXIMUM_PHOTOS." is max number of pics<bR>\n";
					echo count($_FILES['d'])." is the _FILES array count of d<bR>\n";
				}
				for ($i = 1;$i <= $this->ad_configuration_data->MAXIMUM_PHOTOS;$i++)
				{
					if ($sell_debug_images)
					{
						echo $i." is the iteration<bR>\n";
						echo $post_files[d]['size'][$i]." is the upload size of ".$i." inside of i loop<br>\n";
						echo $_FILES['d']['size'][$i]." is the _FILES size<bR>\n";
						echo $HTTP_POST_FILES['d']['size'][$i]." is the HTTP_POST_FILES size<bR>\n";
						echo $url_info[$i]." is the url_info of ".$i."<br>\n";
					}
					if (strlen(trim($url_info[$i]["url"]["location"])) > 0)
					{
						//insert the url
						$image_dimensions = @getimagesize($url_info[$i]["url"]["location"]);
						if ($sell_debug_images)
						{
							echo "hello from url_info loop where i is ".$i."<br>\n";
							echo $url_info[$i]." is the url<br>\n";
							echo $image_dimensions[0]." is the width in url<br>\n";
						}
						if ($image_dimensions)
						{
							if (($image_dimensions[0] > $image_width) && ($image_dimensions[1] > $image_height))
							{
								$imageprop = ($image_width * 100) / $image_dimensions[0];
								$imagevsize = ($image_dimensions[1] * $imageprop) / 100 ;
								$final_image_width = $image_width;
								$final_image_height = ceil($imagevsize);

								if ($final_image_height > $image_height)
								{
									$imageprop = ($image_height * 100) / $image_dimensions[1];
									$imagehsize = ($image_dimensions[0] * $imageprop) / 100 ;
									$final_image_height = $image_height;
									$final_image_width = ceil($imagehsize);
								}
							}
							elseif ($image_dimensions[0] > $image_width)
							{
								$imageprop = ($image_width * 100) / $image_dimensions[0];
								$imagevsize = ($image_dimensions[1] * $imageprop) / 100 ;
								$final_image_width = $image_width;
								$final_image_height = ceil($imagevsize);
							}
							elseif ($image_dimensions[1] > $image_height)
							{
								$imageprop = ($image_height * 100) / $image_dimensions[1];
								$imagehsize = ($image_dimensions[0] * $imageprop) / 100 ;
								$final_image_height = $image_height;
								$final_image_width = ceil($imagehsize);
							}
							else
							{
								$final_image_width = $image_dimensions[0];
								$final_image_height = $image_dimensions[1];
							}

							if ((!$this->first_image_filled) && ($i > 1) && (count($this->images_captured) == 0))
								$image_position = 1;
							else
								$image_position = $i;

							if($this->image_accepted_type($image_dimensions['mime']))
							{
								if(!$this->insert_image($db, $image_position, $url_info[$image_position]["url"]["location"], $url_info[$image_position]["url"]["text"], $final_image_width, $final_image_height, $image_dimensions))
									return false;
							}
							else
							{
								//wrong image file type
								$this->images_error = urldecode($this->messages[1150]);
								return false;
							}
						}
						else
						{
							//could not find url image
						}
					}
					elseif (($post_files[d]['size'][$i] > 0) && ($post_files[d]['size'][$i] < $this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE))
					{
						//insert the image
						$size = $post_files[d]['size'][$i];
						$name = $post_files[d]['name'][$i];
						$type = $post_files[d]['type'][$i];
						$tmp_file = $post_files[d]['tmp_name'][$i];
						if ($sell_debug_images)
						{
							echo $type." is the filetype uploaded<br>\n";
							echo $size." is the file size uploaded<bR>\n";
							echo $tmp_file." is the tmp_file<br>\n";
							echo $name." is the name<Br>\n";
							echo $_SERVER["DOCUMENT_ROOT"]." is the doc root<br>\n";
							echo stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH)." is the upload path<br>\n";
							echo $this->ad_configuration_data->IMAGE_UPLOAD_TYPE." is image_upload_type<br>\n";
						}
						if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
						{
							$filename = strrchr($tmp_file,"/");
							$filename = str_replace("/","",$filename);
							if (!move_uploaded_file($tmp_file, stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename))
							{
								$filename = 0;
								if ($sell_debug_images)
								{
									echo "uploaded file NOT moved because of  error<BR>\n";
									echo $filename." is the filename before image type check<bR>\n";
								}
							}
							else
							{
								$filename = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename;
								if ($sell_debug_images)
								{
									echo "uploaded file moved successfully<BR>\n";
									echo $filename." is the filename before image type check<bR>\n";
								}
							}
						}
						else
						{
							$filename = $tmp_file;
							if ($sell_debug_images)
							{
								echo "uploaded file is set not to be moved<BR>\n";
								echo $tmp_file." is the tmp_file before image type check<bR>\n";
								echo $filename." is the filename before image type check<bR>\n";
							}
						}
						if ($this->image_accepted_type($type))
						{
							if ($sell_debug_images)
							{
								echo strlen(trim($this->current_file_type_icon))." is the strlen of the file type icon<br>\n";
								echo $this->current_file_type_icon." is the icon to use<br>\n";
								echo $type." is the type<Br\n";
							}
							if (strlen(trim($this->current_file_type_icon)) > 0)
							{
								//upload file and reference using icon
								do {
									srand((double)microtime()*1000000);
									$filename_root = rand(1000000,9999999);
									$filepath = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename_root.".".$this->current_file_type_extension;
								} while (file_exists($filepath));

								$full_filename = $filename_root.".".$this->current_file_type_extension;
								$full_url = $this->ad_configuration_data->URL_IMAGE_DIRECTORY.$full_filename;
								if ($sell_debug_images)
								{
									echo $filepath." is the filepath within icon use<Br>\n";
									echo $full_url." is the full_url within icon use<Br>\n";
									echo $full_filename." is the full_filename within icon use<Br>\n";
								}

								$a = array("B", "KB", "MB", "GB", "TB", "PB");

								$pos = 0;
								while ($size >= 1024)
								{
									$size /= 1024;
									$pos++;
								}
								$displayed_filesize = round($size,2)." ".$a[$pos];

								 if (copy ($filename,$filepath))
								 {
								 	if(!$image_dimensions)
								 	{
								 		$image_dimensions = @getimagesize($full_url);
								 		if($image_dimensions)
								 		{
								 			if (($image_dimensions[0] > $image_width) && ($image_dimensions[1] > $image_height))
								 			{
								 				$imageprop = ($image_width * 100) / $image_dimensions[0];
								 				$imagevsize = ($image_dimensions[1] * $imageprop) / 100 ;
								 				$final_image_width = $image_width;
								 				$final_image_height = ceil($imagevsize);

								 				if ($final_image_height > $image_height)
								 				{
								 					$imageprop = ($image_height * 100) / $image_dimensions[1];
								 					$imagehsize = ($image_dimensions[0] * $imageprop) / 100 ;
								 					$final_image_height = $image_height;
								 					$final_image_width = ceil($imagehsize);
								 				}
								 			}
								 			elseif ($image_dimensions[0] > $image_width)
								 			{
								 				$imageprop = ($image_width * 100) / $image_dimensions[0];
								 				$imagevsize = ($image_dimensions[1] * $imageprop) / 100 ;
								 				$final_image_width = $image_width;
								 				$final_image_height = ceil($imagevsize);
								 			}
								 			elseif ($image_dimensions[1] > $image_height)
								 			{
								 				$imageprop = ($image_height * 100) / $image_dimensions[1];
								 				$imagehsize = ($image_dimensions[0] * $imageprop) / 100 ;
								 				$final_image_height = $image_height;
								 				$final_image_width = ceil($imagehsize);
								 			}
								 			else
								 			{
								 				$final_image_width = $image_dimensions[0];
								 				$final_image_height = $image_dimensions[1];
								 			}
								 		}
								 	}

								 	if($this->image_accepted_type($image_dimensions['mime']))
								 	{
								 		if(!$this->insert_image($db, $i, $full_url, "", $final_image_width, $final_image_height, $image_dimensions))
								 			return false;
								 	}
								}
							}
							else
							{
								$image_dimensions = @getimagesize($filename);
								if ($sell_debug_images)
								{
									echo $image_dimensions[0]." is the width in upload<br>\n";
									echo $image_dimensions[1]." is the height in upload<br>\n";
									reset ($image_dimensions);
									foreach ($image_dimensions as $key => $value)
										echo $key." is the key and ".$value." is the value after getimagesize<BR>\n";
								}
								if ($image_dimensions)
								{
									if (($image_dimensions[0] > $image_width) && ($image_dimensions[1] > $image_height))
									{
										$imageprop = ($image_width * 100) / $image_dimensions[0];
										$imagevsize = ($image_dimensions[1] * $imageprop) / 100 ;
										$final_image_width = $image_width;
										$final_image_height = ceil($imagevsize);

										if ($final_image_height > $image_height)
										{
											$imageprop = ($image_height * 100) / $image_dimensions[1];
											$imagehsize = ($image_dimensions[0] * $imageprop) / 100 ;
											$final_image_height = $image_height;
											$final_image_width = ceil($imagehsize);
										}
									}
									elseif ($image_dimensions[0] > $image_width)
									{
										$imageprop = ($image_width * 100) / $image_dimensions[0];
										$imagevsize = ($image_dimensions[1] * $imageprop) / 100 ;
										$final_image_width = $image_width;
										$final_image_height = ceil($imagevsize);
									}
									elseif ($image_dimensions[1] > $image_height)
									{
										$imageprop = ($image_height * 100) / $image_dimensions[1];
										$imagehsize = ($image_dimensions[0] * $imageprop) / 100 ;
										$final_image_height = $image_height;
										$final_image_width = ceil($imagehsize);
									}
									else
									{
										$final_image_width = $image_dimensions[0];
										$final_image_height = $image_dimensions[1];
									}

									if ($sell_debug_images)
									{
										echo $final_image_width." is final width and ".$image_dimensions[0]." is orig width<BR>\n";
										echo $final_image_height." is final height and ".$image_dimensions[1]." is orig wiheightdth<BR>\n";
									}
									if (($final_image_width != $image_dimensions[0]) || ($final_image_height != $image_dimensions[1]))
									{
										//create thumbnail
										$copied = 0;
										switch ($image_dimensions[2])
										{
											case 1: //gif
												//no gif support to open and rewrite
												$extension = ".gif";
											break;
											case 2: //jpg
												if (function_exists("imagecreatefromjpeg"))
												{
													if ($sell_debug_images)
														echo "imagecreatefromjpeg is reported to exist<BR>\n";
													$src_image = @imagecreatefromjpeg($filename);
													if (($sell_debug_images) && ($src_image))
														echo "imagecreatefromjpeg worked<BR>\n";
													if (!$src_image)
													{
														$copied =0;
													}
													else
													{
														if (function_exists("imagecreatetruecolor"))
														{
															if ($sell_debug_images)
																echo "imagecreatetruecolor is reported to exist<BR>\n";
															if (!$this->ad_configuration_data->IMAGECREATETRUECOLOR_SWITCH)
															{
																$dest_image = @imagecreatetruecolor($final_image_width,$final_image_height);
																if (($sell_debug_images) && ($dest_image))
																	echo "imagecreatetruecolor worked<BR>\n";
																if (!$dest_image)
																{
																	if ($sell_debug_images)
																		echo "using imagecreate to make thumb<BR>\n";
																	$dest_image = imagecreate($final_image_width,$final_image_height);
																	if (($sell_debug_images) && ($dest_image))
																		echo "imagecreate worked<BR>\n";
																}
															}
															else
																$dest_image = imagecreate($final_image_width,$final_image_height);
														}
														else
															$dest_image = imagecreate($final_image_width,$final_image_height);
														if (($src_image)  && ($dest_image))
														{
															if (!$this->ad_configuration_data->IMAGECREATETRUECOLOR_SWITCH)
															{
																if ($sell_debug_images)
																	echo "using imagecopyresampled<br>\n";
																$copied = imagecopyresampled($dest_image,$src_image,0,0,0,0,$final_image_width,$final_image_height,$image_dimensions[0],$image_dimensions[1]);
															}
															else
															{
																if ($sell_debug_images)
																	echo "using imagecopyresized<br>\n";
																$copied = imagecopyresized($dest_image,$src_image,0,0,0,0,$final_image_width,$final_image_height,$image_dimensions[0],$image_dimensions[1]);
															}
														}
														 else
															$copied = 0;
													}

												 }
												 else
												 {
												 	if ($sell_debug_images)
												 		echo "imagecreatefromjpeg does not exist<Br>\n";
													$copied = 0;
												 }
												 $extension = ".jpg";
												if ($sell_debug_images)
												{
													echo $copied." is copied in jpg case<br>\n";
													echo $filename." is filename in jpg case<br>\n";
												}
											break;
											case 3: //png
												if (function_exists("imagecreatefrompng"))
												{
													$src_image = @imagecreatefrompng ($filename);
													if (function_exists("imagecreatetruecolor"))
													{
														if (!$this->ad_configuration_data->IMAGECREATETRUECOLOR_SWITCH)
														{
															$dest_image = @imagecreatetruecolor($final_image_width,$final_image_height);
															if (($sell_debug_images) && ($dest_image))
																echo "imagecreatetruecolor worked<BR>\n";
															if (!$dest_image)
																$dest_image = imagecreate($final_image_width,$final_image_height);
														}
														else
															$dest_image = imagecreate($final_image_width,$final_image_height);
													}
													else
														$dest_image = imagecreate($final_image_width,$final_image_height);
													if (($src_image)  && ($dest_image))
													{
														if (!$this->ad_configuration_data->IMAGECREATETRUECOLOR_SWITCH)
														{
															$copied = imagecopyresampled($dest_image,$src_image,0,0,0,0,$final_image_width,$final_image_height,$image_dimensions[0],$image_dimensions[1]);
														}
														else
															$copied = imagecopyresized($dest_image,$src_image,0,0,0,0,$final_image_width,$final_image_height,$image_dimensions[0],$image_dimensions[1]);
													}
													 else
														$copied = 0;
												}
												else
													$copied = 0;
												$extension = ".png";
											break;
											case 6: //bmp
												//no bmp support to open and rewrite
												$extension = ".bmp";
											break;
											case 7: //tiff (intel)
												//no tiff support to open and rewrite
												$extension = ".tif";
											break;
										}
									}
									else
									{
										//no thumbnail
										//picture is small enough
										$copied = 0;
										if ($sell_debug_images)
										{
											echo "no thumb copied<br>\n";
										}
										$extension = "";
									}
									if ($this->ad_configuration_data->IMAGE_UPLOAD_SAVE_TYPE == 1)
									{
										//SAVE TO THE DATABASE
										$fp =fopen($filename, "r");
										if ($fp)
										{
											$data = fread($fp, $size);
											$data = addslashes($data);

											if ($copied)
											{
												do {
													srand((double)microtime()*1000000);
													$filename_root = rand(1000000,9999999);
													$filepath = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename_root.".jpg";
												} while (file_exists($filepath));
												$image_done = imagejpeg($dest_image, $filepath, $this->ad_configuration_data->PHOTO_QUALITY);
												if ($image_done)
												{
													$thumb_fp =fopen($filepath, "r");
													if ($thumb_fp)
													{
														$thumb_data = fread($thumb_fp, filesize($filepath));
														$thumb_data = addslashes($thumb_data);
														$thumb = 1;
													}
													else
													{
														$thumb = 0;
														$thumb_data = 0;
													}
												}
												else
												{
													$thumb = 0;
													$thumb_data = 0;
												}
												unlink($filepath);
											}
											else
											{
												$thumb = 0;
												$thumb_data = 0;
											}
											if ((!$this->first_image_filled) && ($i > 1) && (count($this->images_captured) == 0))
												$image_position = 1;
											else
												$image_position = $i;
											$this->sql_query = "insert into ".$this->images_table."
												(filesize,filetype,filename,image_text,date_entered,image_width,image_height,original_image_width,original_image_height,thumb,thumb_file,image_file,display_order)
												values
												(".$size.",\"".$type."\",\"".$name."\",\"".$url_info[$image_position]["text"]."\",".$this->shifted_time($db).",".$final_image_width.",".$final_image_height.",".$image_dimensions[0].",".$image_dimensions[1].",".$thumb.",\"".$thumb_data."\",\"".$data."\",".$image_position.")";
											$result = $db->Execute($this->sql_query);
											if ($sell_debug_images)
											{
												echo $this->sql_query."<br>\n";
											}
											if (!$result)
											{
												$this->error_message = urldecode($this->messages[57]);
												if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
													@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
												return false;
											}
											$this->images_captured[$image_position]["type"] = 2;
											$this->images_captured[$image_position]["id"] = $db->Insert_ID();
											ksort($this->images_captured);
											$this->update_images_collected($db,1);

											$this->sql_query = "insert into ".$this->sell_images_table."
												(session,display_order,image_type,image_id)
												values
												(\"".$this->session_id."\",".$image_position.",2,".$this->images_captured[$image_position]["id"].")";
											$image_insert_result = $db->Execute($this->sql_query);
											if ($sell_debug_images)
											{
												echo $this->sql_query."<br>\n";
											}
											if (!$image_insert_result)
											{
												$this->error_message = urldecode($this->messages[57]);
												if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
													@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
												return false;
											}
											$this->first_image_filled = 1;
											if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
												@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
										}
										else
										{
											$this->images_error = urldecode($this->messages[1147]);
											if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
												@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
										}
									}
									else
									{
										//SAVE IMAGE TO SERVER
										//do thumb first
										//get extension
										switch ($image_dimensions[2])
										{
											case 1: //gif
												$extension = ".gif";
											break;
											case 2: //jpg
												$extension = ".jpg";
											break;
											case 3: //png
												$extension = ".png";
											break;
											case 6: //bmp
												$extension = ".bmp";
											break;
											case 7: //tiff (intel)
												$extension = ".tif";
											break;
											default:
												// Check for accepted types in the database
												$this->sql_query = "select extension from ".$this->file_types_table." where mime_type like \"".$image_dimensions['mime']."\" and accept = 1";
												$result = $db->Execute($this->sql_query);
												if(!$result)
												{
													$extension = 0;
													break;
												}

												if($result->RecordCount() == 0)
												{
													$extension = 0;
													break;
												}
												else
													$file_type = $result->FetchRow();

												$extension = ".".$file_type['extension'];
											break;
										}
										if ($sell_debug_images)
										{
											echo $extension." is the extension<BR>\n";
										}
										if ($extension)
										{
											if ($copied)
											{
												do {
													srand((double)microtime()*1000000);
													$thumb_filename_root = rand(1000000,9999999);
													$thumb_filepath = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$thumb_filename_root.".jpg";
												} while (file_exists($thumb_filepath));
												if ($sell_debug_images)
													echo  $this->ad_configuration_data->PHOTO_QUALITY." is the photo quality<br>\n";
												$image_done = imagejpeg($dest_image, $thumb_filepath, $this->ad_configuration_data->PHOTO_QUALITY);
												if ($image_done)
												{
													//get url of thumb
													if ($sell_debug_images)
														echo "image created with imagejpeg<br>\n";
													$thumb_url = $this->ad_configuration_data->URL_IMAGE_DIRECTORY.$thumb_filename_root.".jpg";
													$thumb_filename = $thumb_filename_root.".jpg";
												}
												else
												{
													if ($sell_debug_images)
														echo "image NOT created with imagejpeg<br>\n";
													$thumb_url = 0;
													$thumb_filename = 0;
												}
												if ($sell_debug_images)
												{
													echo $thumb_url." is the thumb url<BR>\n";
													echo $thumb_filename." is the thumb filename<BR>\n";
												}
											}
											else
											{
												$thumb_url = 0;
												$thumb_filename = 0;
											}
											//do full size image
											do {
												srand((double)microtime()*1000000);
												$filename_root = rand(1000000,9999999);
												$filepath = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename_root.$extension;
											} while (file_exists($filepath));
											$full_filename = $filename_root.$extension;
											$full_url = $this->ad_configuration_data->URL_IMAGE_DIRECTORY.$full_filename;
											if ($sell_debug_images)
											{
												echo $filepath." is the filepath within full size image<Br>\n";
												echo $full_url." is the full_url within full size image<Br>\n";
												echo $full_filename." is the full_filename within full size image<Br>\n";
												echo $filepath." is the filepath within full size image<Br>\n";
												echo $filename." is the filename within full size image<Br>\n";
											}
											 if (copy ($filename,$filepath))
											 {
												if ((!$this->first_image_filled) && ($i > 1) && (count($this->images_captured) == 0))
													$image_position = 1;
												else
													$image_position = $i;
												$this->sql_query = "insert into ".$this->images_urls_table."
													(image_url,full_filename,image_text,thumb_url,thumb_filename,file_path,date_entered,image_width,image_height,original_image_width,original_image_height,display_order, mime_type)
													values
													(\"".$full_url."\",\"".$full_filename."\",\"".$url_info[$image_position]["text"]."\",\"".$thumb_url."\",\"".$thumb_filename."\",\"".$this->ad_configuration_data->IMAGE_UPLOAD_PATH."\",".$this->shifted_time($db).",".$final_image_width.",".$final_image_height.",".$image_dimensions[0].",".$image_dimensions[1].",".$image_position.", \"".$image_dimensions['mime']."\")";
												if ($sell_debug_images)
												{
													echo $this->sql_query."<br>\n";
												}
												$result = $db->Execute($this->sql_query);
												if (!$result)
												{
													$this->error_message = urldecode($this->messages[57]);
													if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
														@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
													return false;
												}
												$this->images_captured[$image_position]["type"] = 1;
												$this->images_captured[$image_position]["id"] = $db->Insert_ID();
												ksort($this->images_captured);
												$this->update_images_collected($db,1);
												$this->sql_query = "insert into ".$this->sell_images_table."
													(session,display_order,image_type,image_id)
													values
													(\"".$this->session_id."\",".$image_position.",1,".$this->images_captured[$image_position]["id"].")";
												$image_insert_result = $db->Execute($this->sql_query);
												if ($sell_debug_images)
												{
													echo $this->sql_query."<br>\n";
												}
												$this->first_image_filled = 1;
												if (!$image_insert_result)
												{
													$this->error_message = urldecode($this->messages[57]);
													if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
														@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
													return false;
												}

											}
											if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
												@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
										} // if ($extension)
										if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
											@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
									}
								} //if ($image_dimensions)
								else
								{
									//internal error could not process your image
									if ($post_files[d]['size'][$i] == 0)
									{
										$this->images_error = urldecode($this->messages[1148]);
										return false;
									}
									elseif ($post_files[d]['size'][$i] > $this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE)
									{
										$this->images_error =  urldecode($this->messages[1149]);
										return false;
									}
									if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
										@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
								}
							}
						} //if ($this->image_accepted_type($type))
						else
						{
							//wrong image file type
							$this->images_error = urldecode($this->messages[1150]);
							return false;
						}
						if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
							@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
					}
					else
					{
					//	echo $post_files[d]['size'][$i]." is size of ".$i." in else<bR>\n";
					//	if ($post_files[d]['size'][$i] == 0)
					//	{
					//		$this->images_error = urldecode($this->messages[1148]);
					//		return false;
					//	}
						//echo $post_files[d]['size'][$i]." is the size<Br>\n";
						//echo $this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE." is max size <br>\n";
						if ($post_files[d]['size'][$i] > $this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE)
						{
							$this->images_error =  urldecode($this->messages[1148]);
							return false;
						}
					}
				}
				if ((count($this->images_captured) == 0) || (count($post_files[d]) == 0))
				{
					$this->images_error =  urldecode($this->messages[1148]);
				}

				return true;
			}
		}
		else
		{
			//no user id or image_info
			return false;
		}

	} //end of function process_images

//#####################################################################

	function insert_image($db, $image_position, $url, $text, $final_image_width, $final_image_height, $image_dimensions)
	{
		$this->sql_query = "insert into ".$this->images_urls_table."
							(image_url,date_entered,image_text,image_width,image_height,original_image_width,original_image_height,display_order, icon, mime_type)
							values
							(\"".$url."\",".$this->shifted_time($db).",\"".$text."\",".$final_image_width.",".$final_image_height.",".$image_dimensions[0].",".$image_dimensions[1].",".$image_position.", \"".$this->current_file_type_icon."\", \"".$image_dimensions['mime']."\")";
		if ($sell_debug_images)
		{
			echo $image_position." is image_position<bR>\n";
			echo $this->first_image_filled." is first_image_filled<BR>\n";
			echo $this->sql_query."<br>\n";
		}
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			if ($sell_debug_images)
				echo $this->sql_query." is url<br>\n";
			$this->error_message = urldecode($this->messages[57]);
			return false;
		}

		$this->first_image_filled = 1;
		$this->images_captured[$image_position]["type"] = 1;
		$this->images_captured[$image_position]["id"] = $db->Insert_ID();
		ksort($this->images_captured);
		$this->update_images_collected($db,1);

		$this->sql_query = "insert into ".$this->sell_images_table."
							(session,display_order,image_type,image_id)
							values
							(\"".$this->session_id."\",".$image_position.",1,".$this->images_captured[$image_position]["id"].")";
		$image_insert_result = $db->Execute($this->sql_query);
		if ($sell_debug_images)
		{
			echo $this->sql_query."<br>\n";
		}
		if (!$image_insert_result)
		{
			$this->error_message = urldecode($this->messages[57]);
			return false;
		}
	}

//#####################################################################

	function sell_success($db,$credit_approval=0,$declined=0)
	{
		if ($this->debug_sell) echo "TOP OF SELL_SUCCESS<br>\n";
		$this->page_id = 14;
		$this->get_text($db);
		//$this->body .=$credit_approval." is the credit approval passed<BR>\n";
		//$this->body .=$this->classified_variables["payment_type"]." is the payment type<br>\n";
		$this->get_cost_of_ad($db);
		$user_data = $this->get_user_data($db);
		$this->tax = $this->get_tax($db,$user_data);
		$this->total = ($this->subtotal - $this->discount - $this->amount_to_charge_account) + $this->tax;
		//$this->body .=$this->total." is the total<Br>\n";

		if ($this->classified_user_id)
		{
			//display the pertinent success messages according to payment type if any
			if (($this->subtotal != 0) && ($this->discount_percentage < 100) && (($this->subtotal - $this->discount) != $this->amount_to_charge_balance))
			{
				switch ($this->classified_variables["payment_type"])
				{
					case 1: //cash
						//$this->body .="hello from cash<bR>\n";
						$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
						$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[1365])."</td>\n</tr>\n";
						$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[177])."</td>\n</tr>\n";
						$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[180])."</td>\n</tr>\n";
						$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[656]).$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->total).
						" ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
						$this->body .="</table>\n";
						$this->admin_sell_success_email($db);
						$this->remove_sell_session($db,$this->session_id);
						break;

					case 2: //credit card
						//$this->body .="hello from credit card<bR>\n";
						switch ($credit_approval)
						{
							case 1: //approved
								$this->update_category_count($db,$this->terminal_category);
								$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
								$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[1365])."</td>\n</tr>\n";
								$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[177])."</td>\n</tr>\n";
								$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[180])."</td>\n</tr>\n";
								$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[653])."</td>\n</tr>\n";
								if  (!$this->configuration_data['admin_approves_all_ads'])
								{
									$this->body .="<tr class=view_ad_link >\n\t<td><A HREF=\"".trim($this->configuration_data['classifieds_url'])."?a=2&b=".$this->classified_id."\" class=view_ad_link >";
									$this->body .=urldecode($this->messages[181])."</A>\n\t</td>\n</tr>\n";
								}
								$this->body .="</table>\n";
								$this->check_subscriptions_and_credits($db);
								$this->remove_sell_session($db,$this->session_id);
								$this->sell_success_email($db,$this->classified_id);
								break;
							case 2: //declined
								//$this->body .="hello from error 2<br>\n";
								$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
								$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[1365])."</td>\n</tr>\n";
								$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[177])."</td>\n</tr>\n";
								$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[655])."</td>\n</tr>\n";
								$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[654])."</td>\n</tr>\n";
								$this->body .="<tr class=page_description >\n\t<td>";
								$this->body .=urldecode($declined)."\n\t</td>\n</tr>";
								$this->body .="<tr class=view_ad_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_file_name']."?a=1\" class=view_ad_link >";
								$this->body .=urldecode($this->messages[861])."</A>\n\t</td>\n</tr>\n";
								$this->body .="</table>\n";
								$this->update_final_approval($db,0);
								$this->update_billing_approved($db,0);
								break;
							case 3: //error
								//$this->body .="hello from error 3<br>\n";
								$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
								$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[1365])."</td>\n</tr>\n";
								$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[177])."</td>\n</tr>\n";
								$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[655])."</td>\n</tr>\n";
								$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[654])."</td>\n</tr>\n";
								$this->body .="<tr class=page_description >\n\t<td><font>";
								$this->body .=urldecode($declined)."</font>\n\t</td>\n</tr>";
								$this->body .="<tr class=view_ad_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_file_name']."?a=1\" class=view_ad_link >";
								$this->body .=urldecode($this->messages[861])."</A>\n\t</td>\n</tr>\n";
								$this->body .="</table>\n";
								$this->update_final_approval($db,0);
								$this->update_billing_approved($db,0);
								break;
							default: //error
								//$this->body .="hello from error none<br>\n";
								$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
								$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[1365])."</td>\n</tr>\n";
								$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[177])."</td>\n</tr>\n";
								$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[655])."</td>\n</tr>\n";
								$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[654])."</td>\n</tr>\n";
								$this->body .="<tr class=view_ad_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_file_name']."?a=1\" class=view_ad_link >";
								$this->body .=urldecode($this->messages[861])."</A>\n\t</td>\n</tr>\n";
								$this->body .="</table>\n";
								$this->update_final_approval($db,0);
								$this->update_billing_approved($db,0);
								break;
						}
						break;
					case 3: //paypal
						//$this->body .="hello from paypal<bR>\n";
						$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
						$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[1365])."</td>\n</tr>\n";
						$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[177])."</td>\n</tr>\n";
						$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[180])."</td>\n</tr>\n";
						$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[505])."</td>\n</tr>\n";
						if  (!$this->configuration_data['admin_approves_all_ads'])
							$this->body .="<tr class=page_description>\n\t<td><A HREF=\"".trim($this->configuration_data['classifieds_url'])."?a=2&b=".$this->classified_id."\" class=view_ad_link >".urldecode($this->messages[181])."</A>\n\t</td>\n</tr>\n";
						$this->body .="</table>\n";
						$this->remove_sell_session($db,$this->session_id);
						$this->check_subscriptions_and_credits($db);
						$this->sell_success_email($db,$this->classified_id);
						break;
					case 4: //money order
						//$this->body .="hello from money order<bR>\n";
						$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
						$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[1365])."</td>\n</tr>\n";
						$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[177])."</td>\n</tr>\n";
						$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[180])."</td>\n</tr>\n";
						$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[657]).$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->total).
						" ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
						$this->body .="</table>\n";
						$this->admin_sell_success_email($db);
						$this->remove_sell_session($db,$this->session_id);
						break;
					case 5: //check
						//$this->body .="hello from check<bR>\n";
						$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
						$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[1365])."</td>\n</tr>\n";
						$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[177])."</td>\n</tr>\n";
						$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[180])."</td>\n</tr>\n";
						$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[658]).$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->total).
						" ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
						$this->body .="</table>\n";
						$this->admin_sell_success_email($db);
						$this->remove_sell_session($db,$this->session_id);
						break;
					case 6: //worldpay
						//$this->body .="hello from worldpay<bR>\n";
						break;
					case 7: //account balance
						//$this->body .="hello from account balance<bR>\n";
						$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
						$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[1365])."</td>\n</tr>\n";
						$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[177])."</td>\n</tr>\n";
						$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[3170])."</td>\n</tr>\n";
						if (!$this->configuration_data['admin_approves_all_ads'])
						{
							$this->body .="<tr class=page_description>\n\t<td><A HREF=\"".trim($this->configuration_data['classifieds_url'])."?a=2&b=".$this->classified_id."\" class=view_ad_link >".urldecode($this->messages[181])."</A>\n\t</td>\n</tr>\n";
						}
						$this->body .="</table>\n";
						$this->check_subscriptions_and_credits($db);
						$this->remove_sell_session($db,$this->session_id);
						$this->sell_success_email($db,$this->classified_id);
						break;
					case 8: //NOCHEX
						//$this->body .="hello from NOCHEX<bR>\n";
					break;
					default: //got here because ad was free
						//$this->body .="because there was nothing<br>\n";
						$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
						$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[1365])."</td>\n</tr>\n";
						$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[177])."</td>\n</tr>\n";
						$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[180])."</td>\n</tr>\n";
						if (!$this->configuration_data['admin_approves_all_ads'])
						{
							$this->body .="<tr class=page_description>\n\t<td><A HREF=\"".trim($this->configuration_data['classifieds_url'])."?a=2&b=".$this->classified_id."\" class=view_ad_link >".urldecode($this->messages[181])."</A>\n\t</td>\n</tr>\n";
						}
						$this->body .="</table>\n";
						$this->update_category_count($db,$this->terminal_category);
						$this->check_subscriptions_and_credits($db);
						$this->remove_sell_session($db,$this->session_id);
						$this->sell_success_email($db,$this->classified_id);
						break;
				}
			}
			else
			{
				//update transaction type to be 0
				//$this->body .="hello from total is 0<bR>\n";
				$this->update_category_count($db,$this->terminal_category);
				$this->classified_variables["payment_type"] = 0;
				$this->set_transaction_choices($db);
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
				$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[1365])."</td>\n</tr>\n";
				$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[177])."</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[180])."</td>\n</tr>\n";
				if  (!$this->configuration_data['admin_approves_all_ads'])
					$this->body .="<tr class=page_description>\n\t<td><A HREF=\"".trim($this->configuration_data['classifieds_url'])."?a=2&b=".$this->classified_id."\" class=view_ad_link >".urldecode($this->messages[181])."</A>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n";
				$this->check_subscriptions_and_credits($db);
				$this->remove_sell_session($db,$this->session_id);
				$this->sell_success_email($db,$this->classified_id);
			}
			$this->page_id = 14;
			$this->display_page($db);
			return true;
		}
		else
		{
			//no user id
			return false;
		}

	} //end of function sell_success

//########################################################################

	function classified_approval_display($db)
	{
		if ($this->debug_sell) echo "TOP OF CLASSIFIED_APPROVAL_DISPLAY<br>\n";
		if ($this->classified_user_id)
		{
			$this->page_id = 11;
			$this->get_text($db);
			$this->get_category_configuration($db,$this->terminal_category,0);
			$this->get_ad_configuration($db);
			//echo $this->category_configuration->USE_SITE_DEFAULT." is use site default<bR>\n";
			if (!$this->category_configuration->USE_SITE_DEFAULT)
			{
				//echo "using site settings<br>\n";
				$this->field_configuration_data = $this->ad_configuration_data;
				$this->field_configuration_data->USE_OPTIONAL_FIELD_1 = $this->configuration_data['use_optional_field_1'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_2 = $this->configuration_data['use_optional_field_2'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_3 = $this->configuration_data['use_optional_field_3'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_4 = $this->configuration_data['use_optional_field_4'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_5 = $this->configuration_data['use_optional_field_5'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_6 = $this->configuration_data['use_optional_field_6'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_7 = $this->configuration_data['use_optional_field_7'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_8 = $this->configuration_data['use_optional_field_8'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_9 = $this->configuration_data['use_optional_field_9'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_10 = $this->configuration_data['use_optional_field_10'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_11 = $this->configuration_data['use_optional_field_11'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_12 = $this->configuration_data['use_optional_field_12'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_13 = $this->configuration_data['use_optional_field_13'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_14 = $this->configuration_data['use_optional_field_14'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_15 = $this->configuration_data['use_optional_field_15'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_16 = $this->configuration_data['use_optional_field_16'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_17 = $this->configuration_data['use_optional_field_17'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_18 = $this->configuration_data['use_optional_field_18'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_19 = $this->configuration_data['use_optional_field_19'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_20 = $this->configuration_data['use_optional_field_20'];
			}
			else
			{
				//echo "using category settings<br>\n";
				$this->field_configuration_data = $this->category_configuration;
			}

			$this->body ="<table border=0 cellpadding=2 cellspacing=1 width=100%>\n";
			$this->body .="<tr class=place_ad_section_title>\n\t<td>\n\t".urldecode($this->messages[607])."\n\t</td>\n</tr>\n";
			$this->body .="<tr class=page_title>\n\t<td>\n\t".urldecode($this->messages[87])."\n\t</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td>\n\t".urldecode($this->messages[105])."\n\t</td>\n</tr>\n";

			//category name
			$this->body .="<tr class=category_tree >\n\t<td>\n\t";
			$this->body .=urldecode($this->messages[92])." : \n\t ".urldecode($this->messages[101])." >";

			$category_tree = $this->get_category_tree($db,$this->terminal_category);
			reset ($this->category_tree_array);

			if ($category_tree)
			{
				//category tree
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
						{
							$this->body .=" ".$this->category_tree_array[$i]["category_name"]." ";
							break;
						}
						else
							$this->body .=$this->category_tree_array[$i]["category_name"]." > ";
					}
				}
				else
				{
					$this->body .=$category_tree;
				}
				$this->body .="\n\t</td>\n</tr>\n";
			}

			//detail box
			$this->body .="<tr>\n\t<td>\n\t";
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";

			//classified title
			$this->body .="<tr class=ad_title>\n\t\t<td colspan=2>".stripslashes(urldecode($this->classified_variables["classified_title"]))."</td>\n\t</tr>\n\t";

			//seller id
			$name = $this->get_user_name($db,$this->classified_user_id);
			$this->body .="<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[93])."</td>\n\t";
			$this->body .="<td width=65% class=data_values>".$name;
			if ($this->ad_configuration_data->USE_EMAIL_OPTION_FIELD)
			{
				$this->body .= " ( ".$this->classified_variables["email_option"]." )";
			}
			$this->body .= "</td>\n\t</tr>\n\t";

			//phone 1 option
			if ($this->field_configuration_data->USE_PHONE_1_OPTION_FIELD)
			{
				$this->body .="<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[1350])."</td>\n\t";
				$this->body .="<td width=65% class=data_values>".$this->classified_variables["phone_1_option"]."</td>\n\t</tr>\n\t";
			}

			//phone 2 option
			if ($this->field_configuration_data->USE_PHONE_2_OPTION_FIELD)
			{
				$this->body .="<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[1351])."</td>\n\t";
				$this->body .="<td width=65% class=data_values>".$this->classified_variables["phone_2_option"]."</td>\n\t</tr>\n\t";
			}

			//fax option
			if ($this->field_configuration_data->USE_FAX_FIELD_OPTION)
			{
				$this->body .="<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[1352])."</td>\n\t";
				$this->body .="<td width=65% class=data_values>".$this->classified_variables["fax_option"]."</td>\n\t</tr>\n\t";
			}

			if($this->sell_type == 1)
			{
				// Listing Type
				$this->body .= "<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[200113])."</td>\n\t";
				$this->body .="<td width=65% class=data_values>".urldecode($this->messages[200114])."</td>";

				// Price field
				if ($this->field_configuration_data->USE_PRICE_FIELD)
				{
					$this->body .= "<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[106])."</td>\n\t";
					if ($this->classified_variables["currency_type"])
					{
						$this->sql_query = "SELECT * FROM ".$this->currency_types_table." WHERE type_id = ".$this->classified_variables["currency_type"];
						$currency_result = $db->Execute($this->sql_query);

						if (!$currency_result)
						{
							//echo $this->sql_query." is the query<br>\n";
							$this->error_message = urldecode($this->messages[57]);
							return false;
						}
						elseif ($currency_result->RecordCount() == 1)
						{
							$show_currency = $currency_result->FetchNextObject();
						}
					}
					$this->body .="<td width=65% class=data_values>";
					if (((strlen(trim(urldecode($this->classified_variables["price"]))) > 0)
						|| (strlen(trim(urldecode($show_currency->PRECURRENCY))) > 0)
						|| (strlen(trim(urldecode($show_currency->POSTCURRENCY))) > 0)) && ($this->classified_variables["price"] != 0))
					{
						if (floor($this->classified_variables["price"]) == $this->classified_variables["price"])
						{
							$this->body .= stripslashes(urldecode($show_currency->PRECURRENCY)). " ".
								number_format($this->classified_variables["price"])." ".
								stripslashes(urldecode($show_currency->POSTCURRENCY));
						}
						else
						{
							$this->body .= stripslashes(urldecode($show_currency->PRECURRENCY)). " ".
								number_format($this->classified_variables["price"],2,".",",")." ".
								stripslashes(urldecode($show_currency->POSTCURRENCY));
						}
					}
					else
						$this->body .=	stripslashes(urldecode($show_currency->PRECURRENCY))." - ".stripslashes(urldecode($show_currency->POSTCURRENCY));
					$this->body .="</td>\n\t";
					//$this->body .="<td width=65% class=data_values>".$show_currency->PRECURRENCY." ".stripslashes(urldecode($this->classified_variables["price"]))." ".$show_currency->POSTCURRENCY."</td>\n\t</tr>\n\t";
				}

				/*	TODO Remove commenting when payment types are added to classifieds
				// payment types
				if($this->classified_variables['payment_options'])
				{
					// Payment Types
					$this->body .= "<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[200117])."</td>\n\t";
					// Taking into account either one or two pipes for separation
					$payment_types = preg_split("/[|]+/", $this->classified_variables['payment_options']);
					$this->body .= "<td width=65% class=data_values>";
					foreach($payment_types as $value)
					{
						$this->body .= $value.', ';
					}
					$this->body = rtrim($this->body, ' ,');
					$this->body .= "
								</td>
									</tr>";
				}
				*/

				//classified ends
				$this->body .="<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[95])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t";
				if ($this->price_plan->CHARGE_PER_AD_TYPE == 2)
				{
					//pull price plan specific
					if ($this->price_plan->CATEGORY_ID)
						$this->sql_query = "select * from ".$this->price_plan_lengths_table." where
							price_plan_id = ".$this->users_price_plan." and category_id = ".$this->price_plan->CATEGORY_ID."
							and length_of_ad = ".intval($this->classified_variables["classified_length"]);
					else
						$this->sql_query = "select * from ".$this->price_plan_lengths_table." where
							price_plan_id = ".$this->users_price_plan." and length_of_ad = ".intval($this->classified_variables["classified_length"])."
							and category_id = 0";
					$length_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$length_result)
					{
						$this->display_basic_duration_value($db);
					}
					elseif ($length_result->RecordCount() == 1)
					{
						$show_length = $length_result->FetchNextObject();
						$this->body .= $show_length["display_length_of_ad"];
					}
					else
						$this->display_basic_duration_value($db);
				}
				else
				{
					$this->display_basic_duration_value($db);
				}

				$this->body .= "</td>\n\t</tr>\n\t";
			}
			elseif ($this->sell_type == 2)
			{
				// Listing Type
				$this->body .= "<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[200113])."</td>\n\t";
				if($this->classified_variables['auction_type'] == 1)
					$this->body .="<td width=65% class=data_values>".urldecode($this->messages[200115])."</td>";
				elseif($this->classified_variables['auction_type'] == 2)
					$this->body .="<td width=65% class=data_values>".urldecode($this->messages[200116])."</td>";

				// Quantity
				$this->body .= "<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[200118])."</td>\n\t";
				$this->body .="<td width=65% class=data_values>".$this->classified_variables['auction_quantity']."</td>";

				if ($this->classified_variables["currency_type"])
				{
					$this->sql_query = "SELECT * FROM ".$this->currency_types_table." WHERE type_id = ".$this->classified_variables["currency_type"];
					$currency_result = $db->Execute($this->sql_query);

					if (!$currency_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = urldecode($this->messages[57]);
						return false;
					}
					elseif ($currency_result->RecordCount() == 1)
					{
						$show_currency = $currency_result->FetchNextObject();
					}
				}
				$this->body .="
					<tr>
						<td width=35% class=field_labels>".urldecode($this->messages[102725])."</td>
						<td width=65% class=data_values>";
				if (((strlen(trim(urldecode($this->classified_variables["auction_minimum"]))) > 0) || (strlen(trim(urldecode($show_currency->PRECURRENCY))) > 0) || (strlen(trim(urldecode($show_currency->POSTCURRENCY))) > 0))
						&& ($this->classified_variables["auction_minimum"] != 0))
				{
					if (floor($this->classified_variables["auction_minimum"]) == $this->classified_variables["auction_minimum"])
					{
						$this->body .= stripslashes(urldecode($show_currency->PRECURRENCY)). " ".
							number_format($this->classified_variables["auction_minimum"])." ".
							stripslashes(urldecode($show_currency->POSTCURRENCY));
					}
					else
					{
						$this->body .= stripslashes(urldecode($show_currency->PRECURRENCY)). " ".
							number_format($this->classified_variables["auction_minimum"],2,".",",")." ".
							stripslashes(urldecode($show_currency->POSTCURRENCY));
					}
				}
				else
					$this->body .=	stripslashes(urldecode($show_currency->PRECURRENCY))." - ".stripslashes(urldecode($show_currency->POSTCURRENCY));
				$this->body .= "</td>\n\t</tr>\n\t";

				$this->body .= "<tr>\n\t<td width=35% class=field_labels>".urldecode($this->messages[102726])."</td>";
				$this->body .= "<td width=65% class=data_values>";
				if (((strlen(trim(urldecode($this->classified_variables["auction_reserve"]))) > 0)
					|| (strlen(trim(urldecode($show_currency->PRECURRENCY))) > 0)
					|| (strlen(trim(urldecode($show_currency->POSTCURRENCY))) > 0))
					&& ($this->classified_variables["auction_reserve"] != 0))
				{
					if (floor($this->classified_variables["auction_reserve"]) == $this->classified_variables["auction_reserve"])
					{
						$this->body .= stripslashes(urldecode($show_currency->PRECURRENCY)). " ".
							number_format($this->classified_variables["auction_reserve"])." ".
							stripslashes(urldecode($show_currency->POSTCURRENCY));
					}
					else
					{
						$this->body .= stripslashes(urldecode($show_currency->PRECURRENCY)). " ".
							number_format($this->classified_variables["auction_reserve"],2,".",",")." ".
							stripslashes(urldecode($show_currency->POSTCURRENCY));
					}
				}
				else
					$this->body .=	stripslashes(urldecode($show_currency->PRECURRENCY))." 0.00 ".stripslashes(urldecode($show_currency->POSTCURRENCY));
				$this->body .= "</td>\n\t</tr>\n\t";

				if($this->classified_variables["auction_buy_now"] > 0 && strlen($this->classified_variables["auction_buy_now"]) > 0)
				{
					$this->body .= "
						<tr>
							<td width=35% class=field_labels>".urldecode($this->messages[102727])."</td>
							<td width=65% class=data_values>";
					if (((strlen(trim(urldecode($this->classified_variables["auction_buy_now"]))) > 0) || (strlen(trim(urldecode($show_currency->PRECURRENCY))) > 0) || (strlen(trim(urldecode($show_currency->POSTCURRENCY))) > 0))
							&& ($this->classified_variables["auction_buy_now"] != 0))
					{
						if (floor($this->classified_variables["auction_buy_now"]) == $this->classified_variables["auction_buy_now"])
						{
							$this->body .= stripslashes(urldecode($show_currency->PRECURRENCY)). " ".
								number_format($this->classified_variables["auction_buy_now"])." ".
								stripslashes(urldecode($show_currency->POSTCURRENCY));
						}
						else
						{
							$this->body .= stripslashes(urldecode($show_currency->PRECURRENCY)). " ".
								number_format($this->classified_variables["auction_buy_now"],2,".",",")." ".
								stripslashes(urldecode($show_currency->POSTCURRENCY));
						}
					}
					else
						$this->body .=	stripslashes(urldecode($show_currency->PRECURRENCY))." - ".stripslashes(urldecode($show_currency->POSTCURRENCY));
					$this->body .= "
							</td>
						</tr>";
				}

				// payment types
				if($this->classified_variables['payment_options'])
				{
					// Payment Types
					$this->body .= "<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[200117])."</td>\n\t";
					// Taking into account either one or two pipes for separation
					$payment_types = preg_split("/[|]+/", $this->classified_variables['payment_options']);
					$this->body .= "<td width=65% class=data_values>";
					foreach($payment_types as $value)
					{
						$this->body .= $value.', ';
					}
					$this->body = rtrim($this->body, ' ,');
					$this->body .= "
								</td>
									</tr>";
				}

				if ($this->debug_sell)
				{
					echo $this->classified_variables["auction_length"]." is the auction length  <bR>";
					echo $this->classified_variables["start_time"]." is the start time <bR>";
					echo $this->classified_variables["end_time"]." is the end time <bR>";
				}

				if($this->classified_variables["end_time"] == 0)
				{
					if($this->classified_variables["start_time"] == 0)
					{
						$auction_ends = $this->shifted_time($db) + ($this->classified_variables["classified_length"] * 86400);
					}
					else
					{
						$auction_ends = $this->classified_variables["start_time"] + ($this->classified_variables["classified_length"] * 86400);
					}
				}
				else
				{
					$auction_ends = $this->classified_variables["end_time"];
				}

				if ($this->debug_sell) echo $auction_ends." is the ends time ###<bR>";
				//auction start and end
				if($this->configuration_data["user_set_auction_start_times"] && $this->classified_variables["start_time"])
				{
					$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[103355])."</td>\n\t ";
					$this->body .="<td class=data_values>\n\t".date("M j, Y \a\\t h:i A",$this->classified_variables["start_time"])."</td>\n\t</tr>\n\t";
				}
				if($this->configuration_data["user_set_auction_end_times"])
				{
					$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[100095])."</td>\n\t ";
					$this->body .="<td class=data_values>\n\t".date("M j, Y \a\\t h:i A",$auction_ends)."</td>\n\t</tr>\n\t";
				}

				// duration
				if(!$this->classified_variables["end_time"])
				{
					$this->body .="<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[95])."</td>\n\t ";
					$this->body .="<td class=data_values>\n\t";
					if ($this->price_plan->CHARGE_PER_AD_TYPE == 2)
					{
						//pull price plan specific
						if ($this->price_plan->CATEGORY_ID)
							$this->sql_query = "select * from ".$this->price_plan_lengths_table." where price_plan_id = ".$this->users_price_plan." and category_id = ".$this->price_plan->CATEGORY_ID." and length_of_ad = ".intval($this->classified_variables["classified_length"]);
						else
							$this->sql_query = "select * from ".$this->price_plan_lengths_table." where price_plan_id = ".$this->users_price_plan." and length_of_ad = ".intval($this->classified_variables["classified_length"]);
						$length_result = $db->Execute($this->sql_query);
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						if($this->configuration_data->DEBUG_SELL)
						{
							$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "price_plan_lengths_table", "get data from price plan lengths table by length of ad");
						}
						if (!$length_result)
						{
							if ($this->debug_sell) echo $this->sql_query."<br>\n";
							$this->display_basic_duration_value($db);
						}
						elseif ($length_result->RecordCount() == 1)
						{
							$show_length = $length_result->FetchNextObject();
							$this->body .= $show_length["display_length_of_ad"];
						}
						else
						{
							$this->display_basic_duration_value($db);
						}
					}
					else
					{
						$this->display_basic_duration_value($db);
					}
					$this->body .= "</td>\n\t</tr>\n\t";
				}
			}
			//location information
			if (($this->field_configuration_data->USE_ZIP_FIELD)
				|| ($this->field_configuration_data->USE_STATE_FIELD)
				|| ($this->field_configuration_data->USE_COUNTRY_FIELD)
				|| ($this->field_configuration_data->USE_CITY_FIELD))
			{
				$this->body .="<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[644])."\n\t</td>\n\t";
				$this->body .="<td width=65% class=data_values>";
				if ($this->field_configuration_data->USE_CITY_FIELD)
					$this->body .=$this->classified_variables["city"]." ";
				if ($this->field_configuration_data->USE_STATE_FIELD)
					$this->body .=$this->classified_variables["state"]." ";
				if ($this->field_configuration_data->USE_ZIP_FIELD)
					$this->body .=stripslashes(urldecode($this->classified_variables["zip_code"]))." ";
				if ($this->field_configuration_data->USE_COUNTRY_FIELD)
					$this->body .=$this->classified_variables["country"]." ";

				$this->body .="</td>\n\t</tr>\n\t";
			}

			//mapping location information
			if (($this->field_configuration_data->USE_MAPPING_ZIP_FIELD)
				|| ($this->field_configuration_data->USE_MAPPING_STATE_FIELD)
				|| ($this->field_configuration_data->USE_MAPPING_COUNTRY_FIELD)
				|| ($this->field_configuration_data->USE_MAPPING_CITY_FIELD)
				|| ($this->field_configuration_data->USE_MAPPING_ADDRESS_FIELD))
			{
				$this->body .="<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[94])."\n\t</td>\n\t";
				$this->body .="<td width=65% class=data_values>";
				if ($this->field_configuration_data->USE_MAPPING_ADDRESS_FIELD)
					$this->body .= stripslashes(urldecode($this->classified_variables["mapping_address"]))."<br>";
				if ($this->field_configuration_data->USE_MAPPING_CITY_FIELD)
					$this->body .= stripslashes(urldecode($this->classified_variables["mapping_city"]))." ";
				if ($this->field_configuration_data->USE_MAPPING_STATE_FIELD)
					$this->body .= stripslashes(urldecode($this->classified_variables["mapping_state"]))." ";
				if ($this->field_configuration_data->USE_MAPPING_ZIP_FIELD)
					$this->body .= stripslashes(urldecode($this->classified_variables["mapping_zip"]))." ";
				if ($this->field_configuration_data->USE_MAPPING_COUNTRY_FIELD)
					$this->body .= stripslashes(urldecode($this->classified_variables["mapping_country"]))." ";

				$this->body .="</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_URL_LINK_1)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[2455])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["url_link_1"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_URL_LINK_2)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[2456])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["url_link_2"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_URL_LINK_3)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[2457])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["url_link_3"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_1)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[911])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_1"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_2)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[932])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_2"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_3)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[933])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_3"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_4)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[934])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_4"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_5)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[935])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_5"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_6)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[936])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_6"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_7)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[937])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_7"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_8)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[938])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_8"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_9)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[939])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_9"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_10)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[940])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_10"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_11)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[1893])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_11"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_12)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[1894])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_12"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_13)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[1895])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_13"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_14)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[1896])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_14"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_15)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[1897])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_15"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_16)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[1898])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_16"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_17)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[1899])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_17"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_18)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[1900])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_18"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_19)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[1901])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_19"])."</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_20)
			{
				$this->body .="<tr>\n\t\t<td width=35%  class=field_labels>".urldecode($this->messages[1902])."</td>\n\t ";
				$this->body .="<td class=data_values>\n\t".urldecode($this->classified_variables["optional_field_20"])."</td>\n\t</tr>\n\t";
			}

			$num_questions = count($this->classified_variables["question_value"]);
			if ($num_questions > 0 )
			{
				reset ($this->classified_variables["question_value"]);
				while (list($key,$value) = each($this->classified_variables["question_value"]))
				{
					if ((strlen(trim($value)) > 0) || (strlen(trim($this->classified_variables["question_value_other"][$key])) > 0))
					 {
					 	//there is a value in this questions so put it in the db
						$this->sql_query = "SELECT * FROM ".$this->classified_sell_questions_table." WHERE question_id = \"".$key."\"";
						$result = $db->Execute($this->sql_query);

						if (!$result)
						{
							//$this->body .=$this->sql_query." is the query<br>\n";
							$this->error_message = urldecode($this->messages[57]);
							//return false;
						}
						elseif ($result->RecordCount() == 1)
						{
							$show = $result->FetchNextObject();
							if ($show->CHOICES == "check")
							{
								$this->body .="<tr>\n\t\t<td class=field_labels>\n\t\t&nbsp;\n\t\t</td>\n\t\t";
								$this->body .="<td class=data_values>\n\t\t".urldecode($value)."\n\t\t</td>\n\t</tr>\n\t";
							}
							else
							{
								if (strlen(trim($this->classified_variables["question_value_other"][$key])) > 0)
								{
									//$this->body .="use the other value for ---".$key."<br>\n";
									$use_this_value = urldecode($this->classified_variables["question_value_other"][$key]);
								}
								else
									$use_this_value = urldecode($value);
								$this->body .="<tr>\n\t\t<td class=field_labels>\n\t\t".urldecode($show->NAME)."\n\t\t</td>\n\t\t";
								$this->body .="<td class=data_values>\n\t\t".urldecode($use_this_value)."\n\t\t</td>\n\t</tr>\n\t";
							}
						}
					} // end of if
				} // end of for $i
			}// end of if num >0

			$num_questions = count($this->classified_variables["group_value"]);
			if ($num_questions > 0 )
			{
				reset ($this->classified_variables["group_value"]);
				while (list($key,$value) = each($this->classified_variables["group_value"]))
				{
					if ((strlen(trim($value)) > 0) || (strlen(trim($this->classified_variables["group_value_other"][$key])) > 0))
					 {
					 	//there is a value in this questions so put it in the db
						$this->sql_query = "SELECT * FROM ".$this->classified_sell_questions_table." WHERE question_id = \"".$key."\"";
						$result = $db->Execute($this->sql_query);

						if (!$result)
						{
							//$this->body .=$this->sql_query." is the query<br>\n";
							$this->error_message = urldecode($this->messages[57]);
							//return false;
						}
						elseif ($result->RecordCount() == 1)
						{
							$show = $result->FetchNextObject();
							if ($show->CHOICES == "check")
							{
								$this->body .="<tr>\n\t\t<td class=field_labels>\n\t\t&nbsp;\n\t\t</td>\n\t\t";
								$this->body .="<td class=data_values>\n\t\t".urldecode($value)."\n\t\t</td>\n\t</tr>\n\t";
							}
							else
							{
								if (strlen(trim($this->classified_variables["group_value_other"][$key])) > 0)
								{
									//$this->body .="use the other value for ---".$key."<br>\n";
									$use_this_value = $this->classified_variables["group_value_other"][$key];
								}
								else
									$use_this_value = $value;
								$this->body .="<tr>\n\t\t<td class=field_labels>\n\t\t".urldecode($show->NAME)."\n\t\t</td>\n\t\t";
								$this->body .="<td class=data_values>\n\t\t".urldecode($use_this_value)."\n\t\t</td>\n\t</tr>\n\t";
							}
						}
					} // end of if
				} // end of for $i
			}// end of if num >0

			//description
			$this->body .="<tr>\n\t<td class=field_labels>\n\t".stripslashes(urldecode($this->messages[645]))."</td>\n\t";
			$this->body .="<td class=data_values>".stripslashes(urldecode($this->classified_variables["description"]))."\n\t</td>\n</tr>\n";

			$this->body .="</table>\n\t</td>\n</tr>\n";
			//$this->body .="<tr>\n\t<td colspan=2>\n\t<hr width=100% size=2 >\n\t</td>\n</tr>\n";

			//images
			if ((is_array($this->images_captured)) && (count($this->images_captured) > 0))
			{
				$this->body .="<tr>\n\t<td colspan=2>\n\t";
				$this->show_sell_images($db,0);
				$this->body .="</td>\n</tr>\n";
			}

			//$this->body .="<tr>\n\t<td colspan=2>\n\t<hr width=100% size=2 >\n\t</td>\n</tr>\n";

			$this->body .="<tr class=edit_approval_links>\n\t<td colspan=2 >\n\t";
			$this->body .="<a href=";
			if ($this->debug_sell) echo $this->configuration_data['use_ssl_in_sell_process']." is USE_SSL_IN_SELL_PROCESS<Br>\n";
			if ($this->configuration_data['use_ssl_in_sell_process'])
				$this->body .= trim($this->configuration_data['classifieds_ssl_url']);
			else
				$this->body .= trim($this->configuration_data['classifieds_file_name']);
			$this->body .= "?a=1&b=ad_accepted class=edit_approval_links >".urldecode($this->messages[97])."</a><br>\n\t</td>\n</tr>\n";
			$this->body .="<tr class=edit_approval_links>\n\t<td colspan=2 >\n\t";
			$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=edit_category class=edit_approval_links >".urldecode($this->messages[98])."</a><br>\n\t</td>\n</tr>\n";
			$this->body .="<tr class=edit_approval_links>\n\t<td colspan=2 >\n\t";
			$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=edit_details class=edit_approval_links >".urldecode($this->messages[100])."</a><br>\n\t</td>\n</tr>\n";
			$this->body .="<tr class=edit_approval_links>\n\t<td colspan=2 >\n\t";
			$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=edit_image class=edit_approval_links >".urldecode($this->messages[99])."</a>\n\t</td>\n</tr>\n";
			$this->body .="<tr class=end_sell_process_link>\n\t<td colspan=2>\n\t<a href=".trim($this->configuration_data['classifieds_url'])."?a=98 class=end_sell_process_link>".urldecode($this->messages[91])."</a>\n\t</td>\n</tr>\n";
			$this->body .="</table>\n";

			$this->display_page($db);
			return true;
		}
		else
		{
			//no user id
			return false;
		}

	} //end of function classified_approval_display

//####################################################################################

	function display_basic_duration_value($db)
	{
		//check for category specific dropdown lengths first
		$current_category = $this->terminal_category;
		do {
			$this->sql_query = "select display_length_of_ad from ".$this->price_plan_lengths_table." where category_id = ".$current_category."
				and price_plan_id = 0 and length_of_ad = ".intval($this->classified_variables["classified_length"]);
			$category_duration_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>";
			if (!$category_duration_result)
			{
				return false;
			}
			elseif ($category_duration_result->RecordCount() != 1)
			{
				//get parent category
				$this->sql_query = "select parent_id from ".$this->categories_table." where category_id = ".$current_category;
				$parent_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>";
				if (!$parent_result)
				{
					return false;
				}
				elseif ($parent_result->RecordCount() == 1)
				{
					$show_parent = $parent_result->FetchNextObject();
					$current_category = $show_parent->PARENT_ID;
				}
				else
					return false;
			}
			else
			{
				$show_length = $category_duration_result->FetchNextObject();
				$this->body .= $show_length["display_length_of_ad"];
				return true;
			}
		} while (($current_category != 0) && ($category_duration_result->RecordCount() != 1));
		if ($category_duration_result->RecordCount() == 1)
		{
			$show_length = $category_duration_result->FetchNextObject();
			$this->body .= $show_length["display_length_of_ad"];
			return true;
		}
		else
		{
			$this->sql_query = "select display_value from ".$this->choices_table." where type_of_choice = 1 and
				numeric_value = ".intval($this->classified_variables["classified_length"]);
			$duration_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>";
			if (!$duration_result)
			{
				return false;
			}
			elseif ($duration_result->RecordCount() == 1)
			{
				$show_duration = $duration_result->FetchNextObject();
				$this->body .= $show_duration->DISPLAY_VALUE;
				return true;
			}
			else
			{
				return false;
			}
		}
	} //end of function display_basic_duration_value

//#####################################################################

	function DateAdd ($interval, $date,$quantity)
	{
		$difference =  $date2 - $date1;
		switch ($interval)
		{
			case "w":
				$timevalue  = 604800;
				break;
			case "d":
				$timevalue  = 86400;
				break;
			case "h":
				$timevalue = 3600;
				break;
			case "m":
				$timevalue  = 60;
				break;
		}

		$returnvalue = $date +($quantity * $timevalue);
	    	return $returnvalue;
	} //end of function DateDifference

//#########################################################################

	function sell_error()
	{
		$this->body .="<table cellpadding=5 cellspacing=1 border=0>\n";
		$this->body .="<tr class=place_an_ad_error>\n\t<td>".urldecode($this->messages[56])."</td>\n</tr>\n";
		if ($this->error_message)
			$this->body .="<tr class=place_an_ad_error>\n\t<td>".$this->error_message."</td>\n</tr>\n";
		$this->body .="</table>\n";

	} //end of function sell_error

//#########################################################################

	function set_terminal_category($db,$category_id)
	{
		//set the category name and category variables
		$this->sql_query = "select category_name from ".$this->categories_table." where category_id = ".$category_id;
		$result = $db->Execute($this->sql_query);

		if (!$result)
		{
			//$this->body .=$this->sql_query." is the query<br>\n";
			$this->error_message = urldecode($this->messages[57]);
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			if ($this->classified_id)
			{
				//delete the current category questions because the category has changed
				$this->delete_current_category_questions($db);

				//and unset any current category questions in classified_variables
				unset ($this->classified_variables["question_value"]);
				unset ($this->category_questions);
				unset ($this->category_explanation);
				unset ($this->category_choices);
				unset ($this->category_other_box);
			}
			$show = $result->FetchNextObject();
			$this->terminal_category_name = $show->CATEGORY_NAME;
			$this->update_terminal_category($db,$category_id);
			return true;
		}
		else
		{
			$this->error_message = urldecode($this->messages[57]);
			return false;
		}
	} //end of function set_terminal_category($category)

//#########################################################################

	function end_sell_process($db)
	{
		if ($this->debug_sell) echo " TOP OF END_SELL_PROCESS<br>\n";
		$this->page_id = 65;
		$this->get_text($db);
		$this->body .="<table width=100% cellpadding=5 border=0 cellspacing=1>\n<tr>\n\t<td align=center class=end_place_an_ad_title >";
		$this->body .=urldecode($this->messages[906])."\n\t<BR><BR></td>\n</tr>\n";
		$this->body .="<tr>\n\t<td align=center>\n\t<a href=";
		if ($this->configuration_data['use_ssl_in_sell_process'])
			$this->body .=trim($this->configuration_data['classifieds_ssl_url']);
		else
			$this->body .=trim($this->configuration_data['classifieds_url']);
		$this->body .="?a=1 class=end_place_an_ad_link>";
		$this->body .=urldecode($this->messages[907])."</A><br><br>\n\t</td>\n</tr>\n</table>\n";

		$this->remove_sell_session($db,$this->session_id);
		if ($this->debug_sell) echo $this->classified_id." is classified_id<br>\n";
		if ($this->classified_id)
		{
			//remove
			//delete the images
			$this->sql_query = "delete from ".$this->images_table." where classified_id = ".$this->classified_id;
			$delete_image_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
			//$this->body .=$this->sql_query."<br>";
			if (!$delete_image_result)
			{
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			//delete from classifieds table
			$this->sql_query = "delete from ".$this->classifieds_table." where id = ".$this->classified_id;
			$remove_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
			//$this->body .=$this->sql_query."<br>";
			if (!$remove_result)
			{
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}

			//delete from auctions extra questions
			$this->sql_query = "delete from ".$this->classified_extra_table." where classified_id = ".$this->classified_id;
			$remove_extra_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
			//$this->body .=$this->sql_query."<br>";
			if (!$remove_extra_result)
			{
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}

			//delete url images
			//get image urls to
			$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$this->classified_id;
			$get_url_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
			//$this->body .=$this->sql_query."<br>\n";
			if (!$get_url_result)
			{
				$this->body .=$this->sql_query."<br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($get_url_result->RecordCount())
			{
				while ($show_url = $get_url_result->FetchNextObject())
				{
					if ($show_url->FULL_FILENAME)
						unlink($show_url->FILE_PATH.$show_url->FULL_FILENAME);
					if ($show_url->THUMB_FILENAME)
						unlink($show_url->FILE_PATH.$show_url->THUMB_FILENAME);
				}
				$this->sql_query = "delete from ".$this->images_urls_table." where classified_id = ".$this->classified_id;
				$delete_url_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
				//$this->body .=$this->sql_query."<br>\n";
				if (!$delete_url_result)
				{
					$this->body .=$this->sql_query."<br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}
			}
		}
		else
		{
			$this->sql_query = "delete from ".$this->sell_table." where session = \"".$this->session_id."\"";
			if ($this->debug_sell) echo $this->sql_query."is the query<Br>\n";
			$delete_sell_result = $db->Execute($this->sql_query);
			if (!$delete_sell_result)
			{
				if ($this->debug_sell) echo $this->sql_query."<Br>\n";
				return false;
			}

			//get the category questions so far
			$this->sql_query = "delete from ".$this->sell_questions_table." where session = \"".$this->session_id."\"";
			$delete_sell_question_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
			if (!$delete_sell_question_result)
			{
				if ($this->debug_sell) echo $this->sql_query."<Br>\n";
				return false;
			}

			//get the images captured so far
			$this->sql_query = "select * from ".$this->sell_images_table." where session = \"".$this->session_id."\"";
			$select_sell_image_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
			if ($select_sell_image_result->RecordCount() > 0)
			{
				while ($show_image = $select_sell_image_result->FetchNextObject())
				{
					if ($show_image->IMAGE_TYPE == 1)
					{
						//delete url images
						//get image urls to
						$this->sql_query = "select * from ".$this->images_urls_table." where image_id = ".$show_image->IMAGE_ID;
						$get_url_result = $db->Execute($this->sql_query);
						if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
						if (!$get_url_result)
						{
							if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
							return false;
						}
						elseif ($get_url_result->RecordCount() == 1)
						{
							$show_url = $get_url_result->FetchNextObject();
							if ($this->debug_sell)
							{
								echo $show_url->FILE_PATH." is the FILE_PATH<BR>\n";
								echo $show_url->FULL_FILENAME." is the FULL_FILENAME<BR>\n";
								echo $show_url->THUMB_FILENAME." is the THUMB_FILENAME<BR>\n";
							}
							if ($show_url->FULL_FILENAME)
							{
								unlink($show_url->FILE_PATH.$show_url->FULL_FILENAME);
								if ($this->debug_sell) echo "deleting full image: ".$show_url->FILE_PATH.$show_url->FULL_FILENAME."<br>\n";
							}
							if ($show_url->THUMB_FILENAME)
							{
								unlink($show_url->FILE_PATH.$show_url->THUMB_FILENAME);
								if ($this->debug_sell) echo "deleting thumb image: ".$show_url->FILE_PATH.$show_url->THUMB_FILENAME."<br>\n";
							}

							$this->sql_query = "delete from ".$this->images_urls_table." where image_id = ".$show_image->IMAGE_ID;
							$delete_url_result = $db->Execute($this->sql_query);
							if ($this->debug_sell) echo $this->sql_query."<Br>\n";
							if (!$delete_url_result)
							{
								if ($this->debug_sell) echo $this->sql_query."<Br>\n";
								return false;
							}
						}
					}
					elseif ($show_image->IMAGE_TYPE == 2)
					{
						$this->sql_query = "delete from ".$this->images_table." where image_id = ".$show_image->IMAGE_ID;
						$delete_image_result = $db->Execute($this->sql_query);
						if ($this->debug_sell) echo $this->sql_query."<Br>\n";
						if (!$delete_image_result)
						{
							if ($this->debug_sell) echo $this->sql_query."<Br>\n";
							return false;
						}
					}
				}
			}

			$this->sql_query = "delete from ".$this->sell_images_table." where session = \"".$this->session_id."\"";
			$delete_sell_image_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
			if (!$delete_sell_image_result)
			{
				if ($this->debug_sell) echo $this->sql_query."<Br>\n";
				return false;
			}
		}
		$this->display_page($db);
		exit;

	} //end of function end_sell_process

//#########################################################################

	function make_html_uppercase($text)
	{
		$text = preg_replace ("/(<\/?)(\w+)([^>]*>)/e", "'\\1'.strtoupper('\\2').'\\3'", $text);
		return $text;

	} //function make_html_uppercase

//#########################################################################

	function display_category_questions($db)
	{
		//echo count($this->category_questions)." is the count of category questions<br>\n";
		if (count($this->category_questions) > 0)
		{
			//$category_questions = array_reverse($category_questions);  //puts question in order of general to specific
			if (strlen(urldecode($this->messages[131])))
				$this->body .="<tr class=place_an_ad_details_fields>\n\t<td colspan=2 >".urldecode($this->messages[131])."</td>\n</tr>\n";
			if (strlen(urldecode($this->messages[132])))
				$this->body .="<tr class=place_an_ad_details_fields>\n\t<td colspan=2 >".urldecode($this->messages[132])."</td>\n</tr>\n";
			//asort($this->category_questions); //crutch
			foreach ($this->category_questions as $key => $value)
			{
				//spit out the questions
				$this->body .="<tr>\n\t<td class=place_an_ad_details_fields>".$this->category_questions[$key]."\n\t</td>\n\t";
				$this->body .="<td class=place_an_ad_details_data>\n\t";
				//$this->body .=$this->category_choices[$key]." is category choices ".$key." <br>\n\t";
				if (($this->category_choices[$key] == "none") || ($this->category_choices[$key] == "url"))
				{
					//spit out the normal input tag if there are no choices for this question
					$this->body .="<input class=place_an_ad_details_data type=text name=b[question_value][".$key."] value=\"".urldecode($this->classified_variables["question_value"][$key])."\" length=30 maxlength=255>\n\t";
				}
				elseif ($this->category_choices[$key] == "textarea")
				{
					$this->body .="\n\t<textarea name=b[question_value][".$key."] cols=60 rows=15 class=place_an_ad_details_data ";
					if ($this->ad_configuration_data->TEXTAREA_WRAP)
					{
						$this->body .= "wrap=virtual>";
						$this->body .= eregi_replace('<BR[[:space:]]*/?[[:space:]]*>',"",stripslashes(urldecode($this->classified_variables["question_value"][$key])));
					}
					else
					{
						$this->body .= "wrap=soft>";
						$this->body .= stripslashes(urldecode($this->classified_variables["question_value"][$key]));
					}
					$this->body .=" </textarea>";
				}
				elseif ($this->category_choices[$key] == "check")
				{
					//display a checkbox
					$this->body .= "<input class=place_an_ad_details_data type=checkbox name=b[question_value][".$key."] value=\"".urldecode($this->category_questions[$key])."\" ";
					$this->classified_variables["question_value"][$key] = str_replace("\n"," ",$this->classified_variables["question_value"][$key]);
					$this->category_questions[$key] = str_replace("\n"," ",$this->category_questions[$key]);
					//if (strcmp(trim($this->classified_variables["question_value"][$key]),trim($this->category_questions[$key])) == 0)
					if ($this->classified_variables["question_value"][$key] == $this->category_questions[$key])
						$this->body .= "checked";
					$this->body .= ">".$show_choices->VALUE;
					if ($this->debug_sell)
					{
						echo $this->classified_variables["question_value"][$key]." is 1<bR>\n";
						echo $this->category_questions[$key]." is 2<bR>\n";
					}
				}
				else
				{
					//get the list of choices for this question
					$this->sql_query = "SELECT * FROM ".$this->sell_choices_table." WHERE type_id = \"".$this->category_choices[$key]."\" ORDER BY display_order,value";
					//$this->body .=$this->sql_query." is the query to get sell_choices<br>\n";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						return false;
					}
					elseif ($result->RecordCount() > 0)
					{
						$this->body .="<select class=place_an_ad_details_data name=b[question_value][".$key."]>\n\t\t";
						$this->body .="<option></option>\n\t\t";
						while ($show_choices = $result->FetchNextObject())
						{
							//put choices in options of this select statement
							$this->body .="<option ";
							$this->classified_variables["question_value"][$key] = str_replace("\n"," ",$this->classified_variables["question_value"][$key]);
							$value = str_replace("\n"," ",$show_choices->VALUE);
							if (strcmp(trim($this->classified_variables["question_value"][$key]),trim($value)) == 0)
								$this->body .= "selected";
							$this->body .=">".$show_choices->VALUE."</option>\n\t\t";
						}
						$this->body .="</select>\n\t";
					}
					if ($this->category_other_box[$key] == 1)
						$this->body .=urldecode($this->messages[406])."<input class=place_an_ad_details_data type=text size=12 maxlength=50 name=b[question_value_other][".$key."] value=\"".$this->classified_variables["question_value_other"][$key]."\">";
				} //end of if $category_questions[$i]["choices"] != "none"
				//$this->body .= "<a href=\"message.php\" onmouseover=\"window.status='explanation to ".$category_questions[$key]."';  return true;\" onmouseout=\"window.status=''; return true;\" onClick='enterWindow=window.open(\"message.php?msg=".urlencode($category_explanation[$key])."&msgheader=Explanation\",\"Explanation to ".$category_questions[$key]."\",\"width=300,height=150,top=50,left=100,resizeable=no\"); return false'>explanation</a>";
				if (strlen(trim($this->category_explanation[$key])) > 0)
				{
					$this->body .= $this->display_help_link(0,0,0,$key);
				}
				$this->body .= "<input type=hidden name=b[question_display_order][".$key."] value=\"".urldecode($this->category_display_order[$key])."\">";
				$this->body .="</td>\n</tr>\n";

			} // end of while
		} //end of if (count($category_questions) > 0)
	} //end of function display_category_questions

//##################################################################################

	function display_group_questions($db)
	{
		//$this->body .=count($this->category_questions)." is the count of category questions<br>\n";
		if (count($this->group_questions) > 0)
		{
			//$category_questions = array_reverse($category_questions);  //puts question in order of general to specific
			//if (strlen(urldecode($this->messages[404])))
			//	$this->body .="<tr class=place_an_ad_details_fields>\n\t<td colspan=2 >".urldecode($this->messages[404])."</td>\n</tr>\n";
			//if (strlen(urldecode($this->messages[405])))
			//	$this->body .="<tr class=place_an_ad_details_fields>\n\t<td colspan=2 >".urldecode($this->messages[405])."</td>\n</tr>\n";
			//asort($this->category_questions); //crutch

			foreach ($this->group_questions as $key => $value)
			{
				//spit out the questions
				$this->body .="<tr>\n\t<td  class=place_an_ad_details_fields>".$this->group_questions[$key]."\n\t</td>\n\t";
				$this->body .="<td class=place_an_ad_details_data>\n\t";
				//$this->body .=$this->category_choices[$key]." is category choices ".$key." <br>\n\t";
				//if (($this->category_choices[$key] == "none") || ($this->category_choices[$key] == "url"))
				if ((strcmp(trim($this->group_choices[$key]), "none") == 0) || (strcmp(trim($this->group_choices[$key]), "url") == 0))
				{
					//spit out the normal input tag if there are no choices for this question
					$this->body .="<input class=data_fields type=text name=b[group_value][".$key."] value=\"".urldecode($this->classified_variables["group_value"][$key])."\" length=30 maxlength=256>\n\t";
				}
				elseif (strcmp(trim($this->group_choices[$key]), "check") == 0)
				{
					//display a checkbox
					$this->body .= "<input  class=data_fields type=checkbox name=b[group_value][".$key."] value=\"".urldecode($this->classified_variables["group_value"][$key])."\" ";
					if ($this->classified_variables["group_value"][$key] == $this->group_questions[$key])
						$this->body .= "checked";
					$this->body .= ">".$show_choices->VALUE;
				}
				elseif (strcmp(trim($this->group_choices[$key]), "none") != 0)
				{
					//get the list of choices for this question
					$this->sql_query = "SELECT * FROM ".$this->sell_choices_table." WHERE type_id = \"".$this->group_choices[$key]."\" ORDER BY display_order,value";
					//$this->body .=$this->sql_query." is the query to get sell_choices<br>\n";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						return false;
					}
					elseif ($result->RecordCount() > 0)
					{
						$this->body .="<select class=place_an_ad_details_data name=b[group_value][".$key."]>\n\t\t";
						$this->body .="<option></option>\n\t\t";
						while ($show_choices = $result->FetchNextObject())
						{
							//put choices in options of this select statement
							$this->body .="<option ";
							if ($this->classified_variables["group_value"][$key] == $show_choices->VALUE)
								$this->body .="selected";
							$this->body .=">".$show_choices->VALUE."</option>\n\t\t";
						}
						$this->body .="</select>\n\t";
					}
					if ($this->group_other_box[$key] == 1)
						$this->body .=urldecode($this->messages[406])."<input type=text size=12 maxlength=50 name=b[group_value_other][".$key."] value=\"".$this->classified_variables["group_value_other"][$key]."\">";
				} //end of if $category_questions[$i]["choices"] != "none"
				else
				{
					//spit out the normal input tag if there are no choices for this question
					$this->body .="<input class=place_an_ad_details_data type=text name=b[group_value][".$key."] value=\"".$this->classified_variables["question_value"][$key]."\" length=30 maxlength=30>\n\t";
				}
				$this->body .="</td>\n</tr>\n";

			} // end of while
		} //end of if (count($category_questions) > 0)
	} //end of function display_group_questions

//##################################################################################

	function get_group_questions($db,$group_id=0)
	{
		//get sell questions specific to this category
		if ($group_id != 0)
		{
			//get the questions for this category
			$this->sql_query = "SELECT * FROM ".$this->classified_sell_questions_table." WHERE group_id = ".$group_id." ORDER BY display_order";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			if ($result->RecordCount() > 0)
			{
				//$this->body .="hello from inside a positive results<br>\n";
				while ($get_questions = $result->FetchNextObject())
				{
					//get all the questions for this category and store them in the auction_questions variable
					//$this->body .=$get_questions["question_key"]." is the question key<br>\n";
					$this->group_questions[$get_questions->QUESTION_ID] = $get_questions->NAME;
					$this->group_explanation[$get_questions->QUESTION_ID] = $get_questions->EXPLANATION;
					$this->group_choices[$get_questions->QUESTION_ID] = $get_questions->CHOICES;
					$this->group_other_box[$get_questions->QUESTION_ID] = $get_questions->OTHER_INPUT;

					//$this->body .=$get_questions->CHOICES." is the choices for ".$get_questions->QUESTION_ID."<br>\n\t";
				} //end of while $get_questions = mysql_fetch_array($result)
			} //end of if ($result)

		} //end of if ($group_id != 0)

	} //end of function get_group_questions

//##################################################################################

	function show_sell_images($db,$edit=0)
	{
		$this->get_ad_configuration($db);
		//images were captured
		//display them
		reset($this->images_captured);
		//echo count($this->images_captured)." is the number of image to display<Br>\n";
		//echo $this->ad_configuration_data->MAXIMUM_PHOTOS." is the MAX number of photos allowed<br>\n";
		$local_image_counter = 0;
		$this->body .="<tr>\n\t<td colspan=2>\n\t<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n\t";

		do {
			if ($local_image_counter == 0)
				$value = current($this->images_captured);
			else
				$value = next($this->images_captured);
			$this->body .="<tr>\n\t\t<td align=center valign=top>\n\t\t";
			$position = key($this->images_captured);
			if ($value)
				$this->display_image_tag($db,$value,$position,$edit);
			else
				$this->body .= "&nbsp;";
			$this->body .="\n\t\t</td>";
			$local_image_counter++;
			if ($this->ad_configuration_data->PHOTO_COLUMNS > 1)
			{
				$value = next($this->images_captured);
				$this->body .="<td align=center valign=top>\n\t\t";
				if ($value)
					$this->display_image_tag($db,$value,$position,$edit);
				else
					$this->body .= "&nbsp;";
				$this->body .="\n\t\t</td>";
				$local_image_counter++;
				if ($this->ad_configuration_data->PHOTO_COLUMNS > 2)
				{
					$value = next($this->images_captured);
					$this->body .="<td align=center valign=top>\n\t\t";
					if ($value)
						$this->display_image_tag($db,$value,$position,$edit);
					else
						$this->body .= "&nbsp;";
					$this->body .="\n\t\t</td>";
					$local_image_counter++;
					if ($this->ad_configuration_data->PHOTO_COLUMNS > 3)
					{
						$value = next($this->images_captured);
						$this->body .="<td align=center valign=top>\n\t\t";
						if ($value)
							$this->display_image_tag($db,$value,$position,$edit);
						else
							$this->body .= "&nbsp;";
						$this->body .="\n\t\t</td>";
						$local_image_counter++;
					}
				}
			}
			$this->body .="\n\t</tr>\n\t";
		} while ($local_image_counter <  $this->ad_configuration_data->MAXIMUM_PHOTOS);
			//while ($value = next($this->images_captured));
			$this->body .="</table>\n\t";
		return true;
	} //end of function show_sell_images

//##################################################################################

	function display_image_tag($db,$value,$position,$edit=0)
	{
		if ($value["type"] == 1)
		{
			$this->sql_query = "SELECT * FROM ".$this->images_urls_table." WHERE image_id = ".$value["id"];
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
		}
		elseif ($value["type"] == 2)
		{
			$this->sql_query = "SELECT * FROM ".$this->images_table." WHERE image_id = ".$value["id"];
			$result = $db->Execute($this->sql_query);
			//$this->body .=$this->sql_query." is the query<br>\n";
		}
		if (!$result)
		{
			//$this->body .=$this->sql_query." is the query<br>\n";
			$this->error_message = urldecode($this->messages[57]);
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show = $result->FetchNextObject();
			if (($show->IMAGE_WIDTH > $this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH) && ($show->IMAGE_HEIGHT > $this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT))
			{
				$imageprop = ($this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH * 100) / $show->IMAGE_WIDTH;
				$imagevsize = ($show->IMAGE_HEIGHT * $imageprop) / 100 ;
				$final_image_width = $this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH;
				$final_image_height = ceil($imagevsize);

				if ($final_image_height > $this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT)
				{
					$imageprop = ($this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT * 100) / $show->IMAGE_HEIGHT;
					$imagehsize = ($show->IMAGE_WIDTH * $imageprop) / 100 ;
					$final_image_height = $this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT;
					$final_image_width = ceil($imagehsize);
				}
			}
			elseif ($show->IMAGE_WIDTH > $this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH)
			{
				$imageprop = ($this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH * 100) / $show->IMAGE_WIDTH;
				$imagevsize = ($show->IMAGE_HEIGHT * $imageprop) / 100 ;
				$final_image_width = $this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH;
				$final_image_height = ceil($imagevsize);
			}
			elseif ($show->IMAGE_HEIGHT > $this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT)
			{
				$imageprop = ($this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT * 100) / $show->IMAGE_HEIGHT;
				$imagehsize = ($show->IMAGE_WIDTH * $imageprop) / 100 ;
				$final_image_height = $this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT;
				$final_image_width = ceil($imagehsize);
			}
			else
			{
				$final_image_width = $show->IMAGE_WIDTH;
				$final_image_height = $show->IMAGE_HEIGHT;
			}
			if ($value["type"] == 1)
			{
				if ($show->THUMB_URL)
					$url = $show->THUMB_URL;
				else
					$url = $show->IMAGE_URL;

				$this->body .= $this->display_image($db, $url, $final_image_width, $final_image_height, $show->MIME_TYPE);

				if (strlen($show->IMAGE_TEXT) > 0)
					$this->body .= "<br><font class=image_text>".$show->IMAGE_TEXT."</font>";
				if ($final_image_width != $show->ORIGINAL_IMAGE_WIDTH)
					$this->body .="<br><a href=\"javascript:winimage('".$show->IMAGE_URL."','".($show->ORIGINAL_IMAGE_WIDTH+40)."','".($show->ORIGINAL_IMAGE_HEIGHTH+40)."')\" class=place_an_ad_image_links>".urldecode($this->messages[246])."</a>";
				if (($edit) && (!$_REQUEST["set_details"]))
					$this->body .="<br><a href=".$this->configuration_data['classifieds_file_name']."?a=1&f=".$value["id"]."&g=".$position." class=delete_image_links>".urldecode($this->messages[408]).urldecode($this->messages[173])."</a>";
			}
			elseif($value["type"] == 2)
			{
				if (strlen($show->IMAGE_TEXT) > 0)
					$this->body .= "<br><font class=image_text>".$show->IMAGE_TEXT."</font>";
				$this->body .="\n\t<img src=get_image.php?image=".$show->IMAGE_ID."  width=".$final_image_width." height=".$final_image_height." >";
				if ($final_image_width != $show->ORIGINAL_IMAGE_WIDTH)
					$this->body .="<br><a href=\"javascript:winimage('get_image.php?image=".$show->IMAGE_ID."','".($show->ORIGINAL_IMAGE_WIDTH+40)."','".($show->ORIGINAL_IMAGE_HEIGHT+40)."')\" class=place_an_ad_image_links>".urldecode($this->messages[246])."</a>";
				if ($edit)
					$this->body .="<br><a href=".$this->configuration_data['classifieds_file_name']."?a=1&f=".$value["id"]."&g=".$position." class=delete_image_links>".urldecode($this->messages[408]).urldecode($this->messages[173])."</a>";
			}
		}
	} //end of function display_this_image

//##################################################################################

	function remove_image($db,$image_id=0,$image_key=0)
	{
		if ($image_id)
		{
			if ($this->images_captured[$image_key]["type"] == 1)
			{
				//delete url images
				//get image urls to
				$this->sql_query = "select * from ".$this->images_urls_table." where image_id = ".$image_id;
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
					while ($show_url = $get_url_result->FetchNextObject())
					{
						if ($show_url->FULL_FILENAME)
							unlink($show_url->FILE_PATH.$show_url->FULL_FILENAME);
						if ($show_url->THUMB_FILENAME)
							unlink($show_url->FILE_PATH.$show_url->THUMB_FILENAME);
					}
					$this->sql_query = "delete from ".$this->images_urls_table." where image_id = ".$image_id;
					$delete_url_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$delete_url_result)
					{
						$this->body .=$this->sql_query."<br>\n";
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}
				}
			}
			elseif ($this->images_captured[$image_key]["type"] == 2)
				$this->sql_query = "delete from ".$this->images_table." WHERE image_id = ".$image_id;
			else
			{
				//$this->body .=$image_id." is the image id<br>\n";
				return false;
			}
			$delete_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$delete_result)
			{
				return false;
			}

			$this->sql_query = "delete from ".$this->sell_images_table." WHERE image_id = ".$image_id." and session = \"".$this->session_id."\"";
			$delete_session_image_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$delete_session_image_result)
			{
				return false;
			}
			unset ($this->images_captured);
			$this->images_captured = array();
			//get the images captured so far
			$this->sql_query = "select * from ".$this->sell_images_table." where session = \"".$this->session_id."\" order by display_order";
			$setup_sell_image_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<bR>\n";
			if (!$setup_sell_image_result)
			{
				//$this->body .=$this->sql_query."<bR>\n";
				$this->setup_error = $this->messages[453];
				return false;
			}
			elseif ($setup_sell_image_result->RecordCount() > 0)
			{
				while ($show_image = $setup_sell_image_result->FetchNextObject())
				{
					$this->images_captured[$show_image->DISPLAY_ORDER]["type"] = $show_image->IMAGE_TYPE;
					$this->images_captured[$show_image->DISPLAY_ORDER]["id"] = $show_image->IMAGE_ID;
				}
			}
			ksort($this->images_captured);
			return true;
		}
		else
		{
			return false;
		}

	} //end of function remove_image

//##################################################################################

	function delete_expired_images($db)
	{
		$expiration_time = ($this->shifted_time($db) - (60 * 60 * 24));
		$this->sql_query = "delete from ".$this->images_table." WHERE classified_id = 0 and date_entered < ".$expiration_time;
		//$this->body .=$this->sql_query."<br>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		$this->sql_query = "delete from ".$this->images_urls_table." WHERE classified_id = 0 and date_entered < ".$expiration_time;
		//$this->body .=$this->sql_query."<br>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		return true;

	} //end of function delete_expired_images

//##################################################################################

	function delete_expired_ads_on_hold($db)
	{
		$expiration_time = ($this->shifted_time($db) - (60 * 60 * 24 * $this->configuration_data['payment_waiting_period']));
		$this->sql_query = "delete from ".$this->classifieds_table." WHERE live = 0 and date < ".$expiration_time;
		if ($this->debug_sell) echo $this->sql_query."<br>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		return true;

	} //end of function delete_expired_ads_on_hold

//##################################################################################

	function set_group_and_price_plan($db)
	{
		if ($this->debug_sell)
		{
			echo "<BR>TOP OF SET_GROUP_AND_PRICE_PLAN<br>\n";
		}
		if ($this->classified_user_id)
		{
			//set the category name and category variables
			$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
			$group_and_price_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query."<br>\n";
			if (!$group_and_price_result)
			{
				if ($this->debug_sell)
				{
					echo $this->sql_query."<br>\n";
					echo $db->ErrorMSg()."<br>\n";
				}
				$this->setup_error = $this->messages[453];
				return false;
			}
			elseif ($group_and_price_result->RecordCount() == 1)
			{
				$show = $group_and_price_result->FetchNextObject();
				$this->users_group = $show->GROUP_ID;
				$this->sql_query = "update ".$this->sell_table." set
					users_group = ".$show->GROUP_ID."
					where session = \"".$this->session_id."\"";
				$update_group_and_price_result = $db->Execute($this->sql_query);
				if (!$update_group_and_price_result)
				{
					if ($this->debug_sell)
					{
						echo $this->sql_query."<br>\n";
						echo $db->ErrorMSg()."<br>\n";
					}
					return false;
				}
				$this->check_user_subscription($db,1);

				//check for multiple price plans
				$this->sql_query = "select * from ".$this->attached_price_plans." where group_id = ".$show->GROUP_ID." and price_plan_id > 0";
				$multiple_price_plan_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<br>\n";
				if (!$multiple_price_plan_result)
				{
					if ($this->debug_sell)
					{
						echo $this->sql_query."<br>\n";
						echo $db->ErrorMSg()."<br>\n";
					}
					$this->setup_error = $this->messages[453];
					return false;
				}

				if (!$this->user_currently_subscribed && $this->sell_type)
				{
					if ($multiple_price_plan_result->RecordCount() > 0)
					{
						$this->display_price_plan_choice_form($db);
					}
					else
					{
						$this->users_price_plan = $show->PRICE_PLAN_ID;
						$this->sql_query = "update ".$this->sell_table." set
							users_price_plan = ".$show->PRICE_PLAN_ID."
							where session = \"".$this->session_id."\"";
						$update_group_and_price_result = $db->Execute($this->sql_query);
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						if (!$update_group_and_price_result)
						{
							if ($this->debug_sell)
							{
								echo $this->sql_query."<br>\n";
								echo $db->ErrorMSg()."<br>\n";
							}
							$this->setup_error = $this->messages[453];
							return false;
						}
					}
				}
				elseif($this->sell_type)
				{
					if ($multiple_price_plan_result->RecordCount() > 0)
					{
						//get price plan the current subscription is under
						$this->sql_query = "select * from ".$this->user_subscriptions_table."
							where user_id = ".$this->classified_user_id." order by subscription_expire desc";
						$get_subscription_result = $db->Execute($this->sql_query);
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						if (!$get_subscription_result)
						{
							if ($this->debug_sell)
							{
								echo $this->sql_query."<br>\n";
								echo $db->ErrorMSg()."<br>\n";
							}
							$this->setup_error = $this->messages[453];
							return false;
						}
						elseif ($get_subscription_result->RecordCount() > 0)
						{
							//see if there is a price plan attached to the subscription
							//if there is a subscription attached set it as the current price plan
							//if no subscription attached use the default price plan
							$show_subscription = $get_subscription_result->FetchNextObject();
							if ($show_subscription->PRICE_PLAN > 0)
							{
								$this->users_price_plan = $show_subscription->PRICE_PLAN;
								$this->sql_query = "update ".$this->sell_table." set
									users_price_plan = ".$show_subscription->PRICE_PLAN."
									where session = \"".$this->session_id."\"";
								$update_group_and_price_result = $db->Execute($this->sql_query);
								if ($this->debug_sell) echo $this->sql_query."<br>\n";
								if (!$update_group_and_price_result)
								{
									if ($this->debug_sell)
									{
										echo $this->sql_query."<br>\n";
										echo $db->ErrorMSg()."<br>\n";
									}
									$this->setup_error = $this->messages[453];
									return false;
								}
							}
							else
							{
								$this->users_price_plan = $show->PRICE_PLAN_ID;
								$this->sql_query = "update ".$this->sell_table." set
									users_price_plan = ".$show->PRICE_PLAN_ID."
									where session = \"".$this->session_id."\"";
								$update_group_and_price_result = $db->Execute($this->sql_query);
								if ($this->debug_sell) echo $this->sql_query."<br>\n";
								if (!$update_group_and_price_result)
								{
									if ($this->debug_sell)
									{
										echo $this->sql_query."<br>\n";
										echo $db->ErrorMSg()."<br>\n";
									}
									$this->setup_error = $this->messages[453];
									return false;
								}
							}
						}
						else
						{
							//no current subscription found...use the default
							$this->users_price_plan = $show->PRICE_PLAN_ID;
							$this->sql_query = "update ".$this->sell_table." set
								users_price_plan = ".$show->PRICE_PLAN_ID."
								where session = \"".$this->session_id."\"";
							$update_group_and_price_result = $db->Execute($this->sql_query);
							if ($this->debug_sell) echo $this->sql_query."<br>\n";
							if (!$update_group_and_price_result)
							{
								if ($this->debug_sell)
								{
									echo $this->sql_query."<br>\n";
									echo $db->ErrorMSg()."<br>\n";
								}
								$this->setup_error = $this->messages[453];
								return false;
							}
						}
					}
					else
					{
						$this->users_price_plan = $show->PRICE_PLAN_ID;
						$this->sql_query = "update ".$this->sell_table." set
							users_price_plan = ".$show->PRICE_PLAN_ID."
							where session = \"".$this->session_id."\"";
						$update_group_and_price_result = $db->Execute($this->sql_query);
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						if (!$update_group_and_price_result)
						{
							if ($this->debug_sell)
							{
								echo $this->sql_query."<br>\n";
								echo $db->ErrorMSg()."<br>\n";
							}
							$this->setup_error = $this->messages[453];
							return false;
						}
					}

				}
				return true;
			}
			else
			{
				if ($this->debug_sell)
				{
					echo $this->sql_query."<br>\n";
					echo $db->ErrorMSg()."<br>\n";
				}
				$this->setup_error = $this->messages[453];
				return false;
			}
		}
		else
		{
			$this->setup_error = $this->messages[453];
			return false;
		}
	} //end of function set_group_and_price_plan

//#########################################################################

	function check_maximum_ad_limit($db)
	{
		if ($this->users_price_plan)
		{
			$this->page_id=8;
			$this->get_text($db);
			if ($this->classified_user_id)
			{
				//check to see if this user has reached their maximum ad count
				$this->sql_query = "select count(*) as total_ads from ".$this->classifieds_table." where seller = ".$this->classified_user_id." and live = 1";
				$total_ads_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<br>\n";
				if (!$total_ads_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					$this->setup_error = $this->messages[86];
					return false;
				}
				elseif ($total_ads_result->RecordCount() == 1)
				{
					$show_total_ads = $total_ads_result->FetchNextObject();
					$this->sql_query = "select max_ads_allowed from ".$this->price_plans_table." where price_plan_id = ".$this->users_price_plan;
					$price_plan_result = $db->Execute($this->sql_query);
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					if (!$price_plan_result)
					{
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						$this->setup_error = $this->messages[86];
						return false;
					}
					elseif ($price_plan_result->RecordCount() == 1)
					{
						$show_price_plan = $price_plan_result->FetchNextObject();
						if ($show_total_ads->TOTAL_ADS < $show_price_plan->MAX_ADS_ALLOWED)
						{
							if ($this->debug_sell)
							{
								echo "total ads(".$show_total_ads->TOTAL_ADS.") is less then max ads (".$show_price_plan->MAX_ADS_ALLOWED.") for price plan - ".$this->users_price_plan."<BR>\n";
							}
							return true;
						}
						else
						{
							$this->setup_error = $this->messages[75];
							$this->remove_sell_session($db,$this->session_id);
							if ($this->debug_sell)
							{
								echo "total ads(".$show_total_ads->TOTAL_ADS.") is GREATER THAN max ads (".$show_price_plan->MAX_ADS_ALLOWED.")  for price plan - ".$this->users_price_plan."<BR>\n";
							}
							return false;
						}
					}
					else
					{
						$this->setup_error = $this->messages[86];
						return false;
					}

					return true;
				}
				else
				{
					$this->setup_error = $this->messages[86];
					return false;
				}
			}
			else
			{
				$this->setup_error = $this->messages[86];
				return false;
			}
		}
		else
		{
			return true;
		}
	} //end of function check_maximum_ad_limit

//#########################################################################

	function classified_billing_form($db)
	{
		// Check for SSL first
		if($this->configuration_data['use_ssl_in_sell_process'])
		{
			if(!$_SERVER['HTTPS'])
				$this->redirect($_SERVER['QUERY_STRING']);
		}

		$this->page_id = 12;
		$this->get_text($db);

		if ($this->debug_sell)
		{
			echo "<BR>TOP OF CLASSIFIED_BILLING_FORM<br>\n";
			echo $this->price_plan->USE_FEATURED_ADS." is price plan USE_FEATURED_ADS at top of classified_billing_form<BR>\n";
			echo $this->price_plan->APPLIES_TO." is price_plan->applies_to<bR>\n";
		}
		$user_data = $this->get_user_data($db,$this->classified_user_id);
		$payment_types_accepted = $this->get_payment_types_accepted($db);
		if ($this->debug_sell)
		{
			echo $user_data." is user_data<br>\n";
			echo $this->price_plan." is price_plan<br>\n";
			echo $payment_types_accepted." is payment_types_accepted<br>\n";
		}

		if (($this->price_plan) && ($user_data) && ($payment_types_accepted))
		{
			$this->update_classified_approved($db,1);
			$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=1&b=billing_accepted method=post>\n";
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
			$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[612])."</td>\n</tr>\n";
			$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[184])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[189])."</td>\n</tr>\n";

			//SHOW USER DATA INFORMATION
			$this->body .="<tr class=personal_info_header>\n\t<td>".urldecode($this->messages[190])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td>\n\t";
			$this->body .="<table cellpadding=3 cellspacing=0 border=1 align=center width=100%>\n\t";

			//name
			$this->body .="<tr >\n\t\t<td class=personal_info_field_labels width=50%>".urldecode($this->messages[191])."</td>\n\t\t";
			$this->body .="<td width=50% class=personal_info_data_values >\n\t".$user_data->FIRSTNAME." ".$user_data->LASTNAME."\n\t\t</td>\n\t</tr>\n\t";

			//email
			$this->body .="<tr >\n\t\t<td class=personal_info_field_labels>".urldecode($this->messages[220])."</td>\n\t\t";
			$this->body .="<td class=personal_info_data_values >\n\t".$user_data->EMAIL."\n\t\t</td>\n\t</tr>\n\t";

			//company name
			if (($this->configuration_data['use_company_name_field']) && (strlen(trim($user_data->COMPANY_NAME)) > 0))
			{
				$this->body .="<tr>\n\t\t<td class=personal_info_field_labels>".urldecode($this->messages[193])."</td>\n\t\t";
				$this->body .="<td class=personal_info_data_values >\n\t".$user_data->COMPANY_NAME."\n\t\t</td>\n\t</tr>\n\t";
			}

			//address
			$this->body .="<tr>\n\t\t<td class=personal_info_field_labels>".urldecode($this->messages[192])."</td>\n\t\t";
			$this->body .="<td class=personal_info_data_values >\n\t".$user_data->ADDRESS;
			if (strlen(trim($user_data->ADDRESS_2)) > 0)
				$this->body .="<br>".$user_data->ADDRESS_2;
			if (strlen(trim($user_data->CITY)) > 0)
				$this->body .="<br>".$user_data->CITY;
			if ($this->configuration_data['use_state_field'])
			{
				if (strlen(trim($user_data->STATE)) > 0)
					$this->body .=", ".$user_data->STATE;
			}
			if ($this->configuration_data['use_country_field'])
			{
				if (strlen(trim($user_data->COUNTRY)) > 0)
					$this->body .="<br>".$user_data->COUNTRY;
			}
			if (strlen(trim($user_data->ZIP)) > 0)
				$this->body .="<br>".$user_data->ZIP;
			$this->body .="\n\t\t</td>\n\t</tr>\n\t";
			$this->body .="<tr>\n\t<td colspan=2 align=center>
				<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=3 class=edit_personal_info_link>".urldecode($this->messages[221])."</a>\n\t</td>\n</tr>\n";

			$this->body .="</table>\n\t</td>\n</tr>\n";

			//SHOW AD COST AND ADDITIONAL FEATURES
			$this->body .="<tr class=ad_cost_features_header>\n\t<td>".urldecode($this->messages[194])."</td>\n</tr>\n";
			$this->body .="<tr class=ad_cost_features_description >\n\t<td>".urldecode($this->messages[195])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td>\n\t";
			$this->body .="<table cellpadding=3 cellspacing=0 border=1 align=center width=100%>\n\t";
			//get price plan specifics
			if ($this->debug_sell)
			{
				echo $this->price_plan->TYPE_OF_BILLING." is type of billing<Br>\n";
			}
			if ($this->price_plan->TYPE_OF_BILLING == 1)
			{
				//fee based billing
				//display the ad cost for placing an ad
				//check if there are credits for this user waiting
				//if there is a credit display that there is a credit and the ad placement cost will be free
				$this->expire_credits($db);
				$this->get_user_credits($db);

				if ($this->user_credits)
				{
					//display that credit will be used
					$this->body .="<tr>\n\t\t<td width=50% class=ad_cost_features_field_labels >".urldecode($this->messages[196])."</td>\n\t";
					$this->body .="<td width=50% class=ad_cost_features_data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",0).
						" ".$this->configuration_data['postcurrency'];
				}
				else
				{
					$this->get_cost_of_ad($db);
					//display the cost of the ad from this price plan
					$this->body .="<tr>\n\t\t<td width=50% class=ad_cost_features_field_labels >".urldecode($this->messages[197])."</td>\n\t";
					$this->body .="<td width=50% class=ad_cost_features_data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->cost_of_ad).
						" ".$this->configuration_data['postcurrency'];
					//echo "<tr>\n\t\t<td colspan=2>".$subscription_choices_table."</td>\n</tr>\n";
				}

			}
			elseif ($this->price_plan->TYPE_OF_BILLING == 2)
			{
				//subscription based billing
				//check to see if subscription has expired
				//if expired get the subscription fee and for how long
				//if not expired show that the ad placement fee is free
				$this->expire_subscriptions($db);
				$this->check_user_subscription($db);
				$subscription_choices_table = $this->show_subscription_choices_table($db);
				if ($this->user_currently_subscribed)
				{
					//display the free message
					$this->body .="<tr>\n\t\t<td width=50% class=ad_cost_features_field_labels >".urldecode($this->messages[198])."</td>\n\t";
					$this->body .="<td width=50% class=ad_cost_features_data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",0).
						" ".$this->configuration_data['postcurrency']."</td></tr>\n\t";
				}
				else
				{
					if ($subscription_choices_table)
					{
						$this->body .="<tr>\n\t\t<td class=ad_cost_features_field_labels colspan=2>".urldecode($this->messages[1651])."</td>\n\t</tr>";
						if (strlen($this->error_variables["subscription_choice"]) > 0)
							$this->body .= "<tr>\n\t\t<td class=error_message colspan=2>".$this->error_variables["subscription_choice"]."</td></tr>";
						$this->body .="<tr>\n\t\t<td align=center colspan=2><table>";
						$this->body .= $subscription_choices_table;
						$this->body .= "</table>";

					}
					else
					{
						//display the subscription cost and for how long
						$billing_period = $this->get_billing_period($db,$this->price_plan->SUBSCRIPTION_BILLING_PERIOD);
						$this->body .="<tr>\n\t\t<td width=50% class=ad_cost_features_field_labels >".urldecode($this->messages[199])." ".$billing_period;
						if (strlen($this->error_variables["subscription_choice"]) > 0)
							$this->body .= "<br><font class=error_message>".$this->error_variables["subscription_choice"]."</font>";
						$this->body .= "</td>\n\t";
						$this->body .="<td width=50% class=ad_cost_features_data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->SUBSCRIPTION_BILLING_CHARGE_PER_PERIOD).
							" ".$this->configuration_data['postcurrency'];
					}
				}

			}

			if ($this->sell_type != 2)
			{
				$this->sql_query = "select auction_price_plan_id from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
				$price_plan_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<br>\n";
				if(!$price_plan_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					return false;
				}
				$price_plan_id = $price_plan_result->FetchNextObject();
				$this->row_count = 0;
				if ($price_plan_id->AUCTION_PRICE_PLAN_ID)
				{
					$this->sql_query = "select * from ".$this->price_plans_table." where price_plan_id = ".$price_plan_id->AUCTION_PRICE_PLAN_ID;

					$price_plan_result = $db->Execute($this->sql_query);
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					if(!$price_plan_result)
					{
						$this->site_error($db->ErrorMsg());
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						return false;
					}
					$price_plan = $price_plan_result->FetchNextObject();
				}
				else
				{
					$auction_price_plan = $this->price_plan;
				}
			}
			else
			{
				$auction_price_plan = $this->price_plan;
			}

			if ($this->debug_sell)
			{
				echo $auction_price_plan." is auction_price_plan<BR>\n";
				echo $auction_price_plan->CHARGE_PERCENTAGE_AT_AUCTION_END." is CHARGE_PERCENTAGE_AT_AUCTION_END<br>\n";
				echo $auction_price_plan->ROLL_FINAL_FEE_INTO_FUTURE." is ROLL_FINAL_FEE_INTO_FUTURE<br>\n";
			}

			if (($auction_price_plan->CHARGE_PERCENTAGE_AT_AUCTION_END)  && ($auction_price_plan->ROLL_FINAL_FEE_INTO_FUTURE))
			{
				//display the current totals for final fee and auction to total cost of this auction
				$this->sql_query = "select * from ".$this->classifieds_table." where
					seller = ".$this->classified_user_id."
					and final_fee = 1
					and final_fee_transaction_number = 0
					and final_price >= reserve_price
					and final_price > 0.00
					and end_time < ".time();
				$seller_auction_result = $db->Execute($this->sql_query);
				if ($this->debug_sell)
				{
					echo $this->sql_query."<br>\n";
					echo $seller_auction_result->RecordCount()." is seller_auction_result->RecordCount<BR>\n";
				}
				if (!$seller_auction_result)
				{
					$this->site_error($db->ErrorMsg());
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					return false;
				}
				elseif ($seller_auction_result->RecordCount() > 0)
				{
					$this->body .="<table cellpadding=2 cellspacing=1 border=1 align=center width=100%>\n\t";
					$this->body .="<tr class=final_fee_header>\n\t\t<td colspan=4>\n\t\t".stripslashes(urldecode($this->messages[103064]))."\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=final_fee_table_header>\n\t\t<td>".stripslashes(urldecode($this->messages[103065]))."</td>\n\t<td>".stripslashes(urldecode($this->messages[103066]))."</td>\n\t<td>\n\t".stripslashes(urldecode($this->messages[103067]))."</td>\n\t<td>\n\t".stripslashes(urldecode($this->messages[103068]))."</td>\n\t</td>\n\t";
					while($show_final_fee = $seller_auction_result->FetchRow())
					{
						//check to see that final fee should be charged
						if (($show_final_fee["auction_type"] == 1) && ($show_final_fee["item_type"] == 2))
						{
							//regular auction with only one winner
							$this->sql_query = "select * from ".$this->bid_table." where auction_id = ".$show_final_fee["id"]." order by bid desc limit 1";
							$bid_count_result = $db->Execute($this->sql_query);
							if ($this->debug_sell) echo $this->sql_query."<br>\n";
							if (!$bid_count_result)
							{
								if ($this->debug_sell) echo $this->sql_query."<br>\n";
								$this->error_message = urldecode($this->messages[100081]);
								return false;
							}
							elseif ($bid_count_result->RecordCount() == 1)
							{
								if ($this->debug_sell)
								{
									echo $bid_count_result->RecordCount()." is bid count<BR>\n";
									echo $show_final_fee["final_price"]." > ".$show_final_fee["reserve_price"]."<br>\n";
								}
								if ($show_final_fee["final_price"] >= $show_final_fee["reserve_price"])
								{
									//get final fee percentage
									$this->sql_query = "select charge from ".$this->final_fee_table." where ".
										"low<=".$show_final_fee["final_price"]." AND high>=".$show_final_fee["final_price"]." and price_plan_id = ".$auction_price_plan->PRICE_PLAN_ID." ORDER BY charge DESC limit 1";
									$increment_result = $db->Execute($this->sql_query);
									if ($this->debug_sell) echo $this->sql_query." 1<br>\n";
									if (!$increment_result)
									{
										if ($this->debug_sell) echo $this->sql_query."<br>\n";
										return false;
									}
									elseif($increment_result->RecordCount() == 1)
									{
										$show_increment = $increment_result->FetchNextObject();
										$final_fee_percentage = $show_increment->CHARGE;
									}
									else
									{
										return false;
									}
									if ($final_fee_percentage > 0)
									{
										$final_fee_charge = (($final_fee_percentage/100) * $show_final_fee["final_price"]);
									}
									if($this->debug_sell)
									{
										echo $final_fee_charge." is final fee charge for ".$show_final_fee["id"]."<br>\n";
										echo $final_fee_percentage.' is the final fee percentage<br>';
									}
									$final_fee_total += $final_fee_charge;
									$this->final_fee_total = $final_fee_total;

									if ($this->debug_sell)
									{
										echo $final_fee_charge." was just added to final fee total<br>\n";
										echo $final_fee_total." is the final fee total<br>\n";
									}
								}
							}

							// legend row


							if ($final_fee_total > 0)
							{
								// data rows
								if (($this->row_count % 2) == 0)
									$css_tag = "final_fee_result_set_even_rows";
								else
									$css_tag = "final_fee_result_set_odd_rows";
								$this->body .="<tr class=".$css_row_tag.">\n\t\t
									<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2&c=".$show_final_fee["id"]." class=".$css_row_tag.">".urldecode($show_final_fee["title"])."</a>\n\t\t</td>\n\t\t";
								$this->body .="<td>\n\t\t".date("M j, Y - h:i - l",$show_final_fee["date"])."\n\t\t</td>\n\t\t";
								$this->body .="<td>\n\t\t".date("M j, Y - h:i - l",$show_final_fee["ends"])."\n\t\t</td>\n\t\t";
								$this->body .= "<td>".urldecode($show_final_fee["precurrency"])." ".sprintf("%0.02f",$final_fee_total)." ".urldecode($show_final_fee["postcurrency"])."</td></tr>";

								$this->row_count++;
							}
						}
					}
					$this->body .="</table>\n\t";
				}

			}
			$this->body .= "</td></tr>";

			if ($this->debug_sell)
			{
				echo "here are the extras<BR>\n";
			}

			/*if ($this->price_plan->CHARGE_PER_PICTURE > 0)
			{
				$number_of_images = count($this->images_captured);
				//charge per picture
				$this->body .="<tr>\n\t\t<td class=ad_cost_features_field_labels width=50%><font class=ad_cost_features_field_labels >".urldecode($this->messages[225])."</font><br>
					<font class=ad_cost_features_description>".urldecode($this->messages[226])."</font></td>\n\t";
				$this->body .="<td class=ad_cost_features_data_values width=50%>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",($this->price_plan->CHARGE_PER_PICTURE * $number_of_images)).
					" ".$this->configuration_data['postcurrency']." ( ".$number_of_images." X ".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->CHARGE_PER_PICTURE)." ".$this->configuration_data['postcurrency'].")
					</td>\n\t</tr>\n\t";
			}*/

			if ($this->price_plan->CHARGE_PER_PICTURE > 0)
			{
				$number_of_images = count($this->images_captured);
				//charge per picture
				$quantity = $number_of_images-$this->price_plan->NUM_FREE_PICS;
				if($quantity < 0)
					$quantity = 0;
				$this->body .="<tr>\n\t\t<td width=50%><font class=ad_cost_features_field_labels >".urldecode($this->messages[225])."</font><br>
					<font class=ad_cost_features_description>".urldecode($this->messages[226])."</font></td>\n\t";
				$this->body .="<td class=ad_cost_features_data_values width=50%>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",($this->price_plan->CHARGE_PER_PICTURE * $quantity)).
					" ".$this->configuration_data['postcurrency']." ( ".$quantity." X ".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->CHARGE_PER_PICTURE)." ".$this->configuration_data['postcurrency'].") ";

				if($this->price_plan->NUM_FREE_PICS)
				{
					$this->body .= urldecode($this->messages[3211]) ." = ". $this->price_plan->NUM_FREE_PICS;
				}

				$this->body .= "</td>\n\t</tr>\n\t";
			}

			if ($this->configuration_data['use_bolding_feature'])
			{
				if($this->price_plan->USE_BOLDING)
				{
					//bolding
					$this->body .="<tr>\n\t\t<td width=50%><font class=ad_cost_features_field_labels >".urldecode($this->messages[200])."</font><br>
						<font class=ad_cost_features_description>".urldecode($this->messages[201])."</font></td>\n\t";
					$this->body .="<td class=ad_cost_features_data_values width=50%>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->BOLDING_PRICE).
						" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[718])."
						<input type=radio name=c[bolding] value=1 ";
					if ($this->classified_variables["bolding"]) $this->body .="checked";
					$this->body .="> ".urldecode($this->messages[719])."<input type=radio name=c[bolding] value=0 ";
					if (!$this->classified_variables["bolding"]) $this->body .="checked";
					$this->body .="></td>\n\t</tr>\n\t";
				}
			}

			if ($this->configuration_data['use_better_placement_feature'])
			{
				if($this->price_plan->USE_BETTER_PLACEMENT)
				{
					//better placement
					$this->body .="<tr>\n\t\t<td width=50%><font class=ad_cost_features_field_labels >".urldecode($this->messages[215])."</font><br>
						<font class=ad_cost_features_description>".urldecode($this->messages[216])."</td>\n\t";
					$this->body .="<td class=ad_cost_features_data_values width=50%>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->BETTER_PLACEMENT_CHARGE).
						" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[718])."
						<input type=radio name=c[better_placement] value=1 ";
					if ($this->classified_variables["better_placement"]) $this->body .="checked";
					$this->body .="> ".urldecode($this->messages[719])."<input type=radio name=c[better_placement] value=0 ";
					if (!$this->classified_variables["better_placement"]) $this->body .="checked";
					$this->body .="></td>\n\t</tr>\n\t";
				}
			}

			if ($this->debug_sell)
			{
				echo $this->configuration_data['use_featured_feature']." is site USE_FEATURED_FEATURE<BR>\n";
				echo $this->price_plan->USE_FEATURED_ADS." is price plan USE_FEATURED_ADS<bR>\n";
			}
			if ($this->configuration_data['use_featured_feature'])
			{
				if($this->price_plan->USE_FEATURED_ADS)
				{
					//featured ad
					$this->body .="<tr>\n\t\t<td width=50%><font class=ad_cost_features_field_labels >".urldecode($this->messages[217])."</font><br>
						<font class=ad_cost_features_description>".urldecode($this->messages[218])."</font></td>\n\t";
					$this->body .="<td class=ad_cost_features_data_values width=50%>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE).
						" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[718])."
						<input type=radio name=c[featured_ad] value=1 ";
					if ($this->classified_variables["featured_ad"]) $this->body .="checked";
					$this->body .="> ".urldecode($this->messages[719])."<input type=radio name=c[featured_ad] value=0 ";
					if (!$this->classified_variables["featured_ad"]) $this->body .="checked";
					$this->body .="></td>\n\t</tr>\n\t";
				}
			}

			if ($this->configuration_data['use_featured_feature_2'])
			{
				if($this->price_plan->USE_FEATURED_ADS_LEVEL_2)
				{
					//featured ad
					$this->body .="<tr>\n\t\t<td width=50%><font class=ad_cost_features_field_labels >".urldecode($this->messages[2260])."</font><br>
						<font class=ad_cost_features_description>".urldecode($this->messages[2261])."</font></td>\n\t";
					$this->body .="<td class=ad_cost_features_data_values width=50%>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE_2).
						" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[718])."
						<input type=radio name=c[featured_ad_2] value=1 ";
					if ($this->classified_variables["featured_ad_2"]) $this->body .="checked";
					$this->body .="> ".urldecode($this->messages[719])."<input type=radio name=c[featured_ad_2] value=0 ";
					if (!$this->classified_variables["featured_ad_2"]) $this->body .="checked";
					$this->body .="></td>\n\t</tr>\n\t";
				}
			}

			if ($this->configuration_data['use_featured_feature_3'])
			{
				if($this->price_plan->USE_FEATURED_ADS_LEVEL_3)
				{
					//featured ad
					$this->body .="<tr>\n\t\t<td width=50%><font class=ad_cost_features_field_labels >".urldecode($this->messages[2262])."</font><br>
						<font class=ad_cost_features_description>".urldecode($this->messages[2263])."</font></td>\n\t";
					$this->body .="<td class=ad_cost_features_data_values width=50%>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE_3).
						" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[718])."
						<input type=radio name=c[featured_ad_3] value=1 ";
					if ($this->classified_variables["featured_ad_3"]) $this->body .="checked";
					$this->body .="> ".urldecode($this->messages[719])."<input type=radio name=c[featured_ad_3] value=0 ";
					if (!$this->classified_variables["featured_ad_3"]) $this->body .="checked";
					$this->body .="></td>\n\t</tr>\n\t";
				}
			}

			if ($this->configuration_data['use_featured_feature_4'])
			{
				if($this->price_plan->USE_FEATURED_ADS_LEVEL_4)
				{
					//featured ad
					$this->body .="<tr>\n\t\t<td width=50%><font class=ad_cost_features_field_labels >".urldecode($this->messages[2264])."</font><br>
						<font class=ad_cost_features_description>".urldecode($this->messages[2265])."</font></td>\n\t";
					$this->body .="<td class=ad_cost_features_data_values width=50%>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE_4).
						" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[718])."
						<input type=radio name=c[featured_ad_4] value=1 ";
					if ($this->classified_variables["featured_ad_4"]) $this->body .="checked";
					$this->body .="> ".urldecode($this->messages[719])."<input type=radio name=c[featured_ad_4] value=0 ";
					if (!$this->classified_variables["featured_ad_4"]) $this->body .="checked";
					$this->body .="></td>\n\t</tr>\n\t";
				}
			}

			if ($this->configuration_data['use_featured_feature_5'])
			{
				if($this->price_plan->USE_FEATURED_ADS_LEVEL_5)
				{
					//featured ad
					$this->body .="<tr>\n\t\t<td width=50%><font class=ad_cost_features_field_labels >".urldecode($this->messages[2266])."</font><br>
						<font class=ad_cost_features_description>".urldecode($this->messages[2267])."</font></td>\n\t";
					$this->body .="<td class=ad_cost_features_data_values width=50%>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE_5).
						" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[718])."
						<input type=radio name=c[featured_ad_5] value=1 ";
					if ($this->classified_variables["featured_ad_5"]) $this->body .="checked";
					$this->body .="> ".urldecode($this->messages[719])."<input type=radio name=c[featured_ad_5] value=0 ";
					if (!$this->classified_variables["featured_ad_5"]) $this->body .="checked";
					$this->body .="></td>\n\t</tr>\n\t";
				}
			}

			if ($this->configuration_data['use_attention_getters'])
			{
				if($this->price_plan->USE_ATTENTION_GETTERS)
				{
					$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 10";
					$attention_getters_result = $db->Execute($this->sql_query);
					if (!$attention_getters_result)
					{
						//$this->body .=$this->sql_query."<br>\n";
						$this->setup_error = $this->messages[453];
						return false;
					}
					elseif ($attention_getters_result->RecordCount() > 0)
					{
						//attention getters
						$this->body .="<tr>\n\t\t<td width=50%><font class=ad_cost_features_field_labels >".urldecode($this->messages[1362])."</font><br>
							<font class=ad_cost_features_description>".urldecode($this->messages[1363])."</font>";
						if (strlen($this->error_variables["attention_getter"]) > 0)
							$this->body .= "<br><font class=error_message>".$this->error_variables["attention_getter"]."</font>";
						$this->body .= "</td>\n\t";
						$this->body .="<td class=ad_cost_features_data_values width=50%>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->ATTENTION_GETTER_PRICE).
							" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[718])."
							<input type=radio name=c[attention_getter] value=1 ";
						if ($this->classified_variables["attention_getter"]) $this->body .="checked";
						$this->body .="> ".urldecode($this->messages[719])."<input type=radio name=c[attention_getter] value=0 ";
						if (!$this->classified_variables["attention_getter"]) $this->body .="checked";
						$this->body .="><br><table cellpadding=1 cellspacing=1 border=0 align=center>";
						while ($show_attention_getter = $attention_getters_result->FetchNextObject())
						{
							$this->body .= "<tr>\n\t<td valign=middle class=attention_getter_labels ><img src=\"".$show_attention_getter->VALUE."\" border=0 hspace=2>
								<input type=radio name=c[attention_getter_choice] value=".$show_attention_getter->CHOICE_ID;
							if ($this->classified_variables["attention_getter_choice"] == $show_attention_getter->CHOICE_ID)
								$this->body .= " checked";
							$this->body .= "></td>\n\t</tr>\n";
						}
						$this->body .= "</table></td>\n\t</tr>\n\t";
					}
				}
			}

			$this->body .="</table>\n\t</td>\n</tr>\n";

			if ($this->price_plan->CHARGE_PERCENTAGE_AT_AUCTION_END)
			{
				//display the final fee charge
				$this->body .="<tr class=final_fee_header>\n\t<td>".urldecode($this->messages[102808])."</td>\n</tr>\n";
				$this->body .= "<tr><td>";
				$this->body .=  "<table cellpadding=2 cellspacing=1 border=1 bordercolor=dddddd align=center width=100%>\n\t";
				$this->body .=  "<tr>\n\t\t<td width=50%>
					<font class=final_fee_field_labels>".urldecode($this->messages[102808])."</font>
					<br><font class=final_fee_field_labels>".urldecode($this->messages[102806])."</font></td>\n\t";
				$this->body .=  "<td width=50%>";

				$this->sql_query = "select * from ".$this->final_fee_table." where price_plan_id = ".$this->users_price_plan." order by low asc";
				$final_fee_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<br>\n";
				if($this->configuration_data->DEBUG_SELL)
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "final_fee_table", "get data from final fee table by price plan id");
				}
				if (!$final_fee_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					return false;
				}
				elseif ($final_fee_result->RecordCount() > 0)
				{
					$this->body .=  "<table cellpadding=2 cellspacing=1 border=0 width=100%  class=row_color2>\n";
					$this->body .=  "<tr class=final_fee_table_header>\n\t
						<td>".urldecode($this->messages[102809])."</td>\n\t
						<td>".urldecode($this->messages[102810])."</td>\n\t
						<td>".urldecode($this->messages[102813])."</td>\n</tr>\n";
					$this->row_count = 0;
					while ($show = $final_fee_result->FetchNextObject())
					{
						//if (($this->row_count % 2) == 0)
						//	$css_tag = "final_fee_result_set_even_rows";
						//else
						//	$css_tag = "final_fee_result_set_odd_rows";
						$this->body .="<tr class=final_fee_table_row>\n\t\t
							<td>\n\t".$show->LOW." ";
						$this->body .=  "\n\t</td>\n\t
							<td>\n\t";
						if ($show->HIGH == 100000000)
							$this->body .=  urldecode($this->messages[102815]);
						else
							$this->body .=  $show->HIGH."\n\t</td>\n\t";
						$this->body .=  "<td>\n\t".$show->CHARGE." %\n\t</td>\n</tr>\n";
						$this->row_count++;
					} //end of while
					$this->body .=  "</table>\n";
				}
				$this->body .=  "</td>\n\t</tr>\n\t</table></td></tr>";
			}

			if ($this->debug_sell)
			{
				echo "here are the payment choices<BR>\n";
			}

			//PAY CHOICE(S)
			$this->body .="<tr class=payment_choices_header >\n\t<td>".urldecode($this->messages[202]);
			$this->body .="</td>\n</tr>\n";
			if ($this->error_variables["payment_type"])
				$this->body .="<tr class=error_message>\n\t<td>".urldecode($this->messages[222]);
				$this->body .="</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td>\n\t";
			$this->body .="<table cellpadding=3 cellspacing=1 border=1 align=center width=100%>\n\t";
			if ($this->configuration_data['all_ads_are_free'])
			{
				//shows that all ads free
				// ***
				$this->body .= "<tr><td align=center>FREE!!!</td></tr>";
			}
			else
			{
				//check to see if account balance feature is in use for this site
				while ($show_payment = $payment_types_accepted->FetchNextObject())
				{
					switch ($show_payment->TYPE)
					{
						case 1:
						{
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)) ||
									(!$this->configuration_data['use_account_balance']) ||
									(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)))
							{
								//cash
								$this->body .="<tr>\n\t\t<td class=payment_choices_field_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[203])."</font><br>
								<font class=payment_choices_field_descriptions>".urldecode($this->messages[204])."</td>\n\t";
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] checked value=1>&nbsp;</td>\n\t</tr>\n\t";
								}
								else
								{
									//this is a choice among many
									$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=1 ";
									if ($this->classified_variables["payment_type"] == 1) $this->body .="checked";
									$this->body .="></td>\n\t</tr>\n\t";
								}
							}
						}
						break;
						case 2:
						{
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)) ||
									(!$this->configuration_data['use_account_balance']) ||
									(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)))
							{
								//credit card
								//check to see if this needs to collect cc info here
								//2checkout collects at their site
								//can collect cc for authorizenet here
								$this->sql_query = "select * from ".$this->cc_choices." where chosen_cc = 1";
								$cc_result = $db->Execute($this->sql_query);
								if (!$cc_result)
								{
									$this->setup_error = $this->messages[453];
									return false;
								}
								elseif ($cc_result->RecordCount() == 1)
								{
									$show_cc_choice = $cc_result->FetchNextObject();

								}
								$this->body .="<tr>\n\t\t<td class=payment_choices_field_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[209])."</font><br>
								<font class=payment_choices_field_descriptions>".urldecode($this->messages[210])."</td>\n\t";
								if ($this->debug_sell)
									echo $show_cc_choice->CC_ID." is the cc id<br>\n";
								if (($show_cc_choice->CC_ID == 1) || ($show_cc_choice->CC_ID == 3) || ($show_cc_choice->CC_ID == 4) || ($show_cc_choice->CC_ID >= 6))
								{
									if ($payment_types_accepted->RecordCount() == 1)
									{
										//this is the only choice so has hidden variable saying this is the type requested
										$this->body .="<td width=10% valign=top><input type=radio name=c[payment_type] checked value=2>&nbsp;</td>\n\t<td width=40% align=center>\n\t";
									}
									else
									{
										//this is a choice among many
										$this->body .="<td width=10% valign=top><input type=radio name=c[payment_type] value=2 ";
										if (($this->classified_variables["payment_type"] == 2) || ($this->classified_variables["payment_type"] = 0)) $this->body .="checked";
										$this->body .="></td>\n\t<td width=40% align=center class=payment_choices_cc_number_data_values>\n\t";
									}
									$this->body .="
										<table cellpadding=3 cellspacing=1 border=0 align=center width=100% class=payment_choices_cc_number_data_values>
											<tr>
												<td width=50% align=right>";
									if ($this->error_variables["cc_number"])
										$this->body .="
													<font class=error_message>".$this->error_variables["cc_number"]."</font>";
									if ($this->error_variables["cc_expiration"])
										$this->body .="
													<font class=error_message>".$this->error_variables["cc_expiration"]."</font>";
									$this->body .= urldecode($this->messages[213]);
									$this->body .="
												</td>
												<td width=50% align=left>
													<input type=text name=c[cc_number] size=20><br>
												</td>
											</tr>";
									if ($show_cc_choice->CC_ID==7 || $show_cc_choice->CC_ID==9)
									{
										$this->body .="
											<tr>
												<td width=50% align=right>";
										$this->body .= "
													<a href=\"javascript:winimage('./images/cvv2_code.gif',500,200)\">".
													urldecode($this->messages[3278])."</a>";
										$this->body .= "
												</td>
												<td width=50% align=left>
													<input type=text name=c[cvv2_code] size=4>
												</td>
											</tr>";
									}
									$this->body .="
											<tr>
												<td width=50% align=right>".urldecode($this->messages[214])."</td>
												<td width=50% align=left>";
									$this->body .= "<select name=c[cc_exp_month]>";
									for ($i=1;$i<13;$i++)
									{
										$this->body .="
														<option>".sprintf("%02d",$i)."</option>";
									}
									$this->body .= "</select>";
									$this->display_year_dropdown("c[cc_exp_year]");
									$this->body .= "
												</td>
											</tr>
										</table>";
								}
								elseif (($show_cc_choice->CC_ID == 2) ||  ($show_cc_choice->CC_ID == 5))
								{
									if ($payment_types_accepted->RecordCount() == 1)
									{
										//this is the only choice so has hidden variable saying this is the type requested
										$this->body .= "<td colspan=2 width=50% valign=top><input type=radio name=c[payment_type] checked value=2>&nbsp;";
									}
									else
									{
										//this is a choice among many
										$this->body .= "<td colspan=2 width=50% valign=top><input type=radio name=c[payment_type] value=2 ";
										if ($this->classified_variables["payment_type"] == 2) $this->body .= "checked";
											$this->body .= "checked>";
									}
								}
								$this->body .="</td>\n\t</tr>\n\t";
							}
						}
						break;
						case 3:
						{
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)) ||
									(!$this->configuration_data['use_account_balance']) ||
									(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)))
							{
								//paypal
								$this->body .="<tr>\n\t\t<td class=payment_choices_field_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[211])."</font><br>
									<font class=payment_choices_field_descriptions>".urldecode($this->messages[212])."</td>\n\t";
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .="<td width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=3>&nbsp;</td>\n\t</tr>\n\t";
								}
								else
								{
									//this is a choice among many
									$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=3 ";
									if ($this->classified_variables["payment_type"] == 3) $this->body .="checked";
									$this->body .="></td>\n\t</tr>\n\t";
								}
							}
						}
						break;
						case 4:
						{
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)) ||
									(!$this->configuration_data['use_account_balance']) ||
									(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)))
							{
								//money order
								$this->body .="<tr>\n\t\t<td class=payment_choices_field_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[205])."</font><br>
								<font class=payment_choices_field_descriptions>".urldecode($this->messages[206])."</td>\n\t";
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .="<td width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=4>&nbsp;</td>\n\t</tr>\n\t";
								}
								else
								{
									//this is a choice among many
									$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=4 ";
									if ($this->classified_variables["payment_type"] == 4) $this->body .="checked";
									$this->body .="></td>\n\t</tr>\n\t";
								}
							}
						}
						break;
						case 5:
						{
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)) ||
									(!$this->configuration_data['use_account_balance']) ||
									(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)))
							{
								//check
								$this->body .="<tr>\n\t\t<td class=payment_choices_field_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[207])."</font><br>
								<font class=payment_choices_field_descriptions>".urldecode($this->messages[208])."</td>\n\t";
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .="<td  width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=5>&nbsp;</td>\n\t</tr>\n\t";
								}
								else
								{
									//this is a choice among many
									$this->body .="<td  width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=5 ";
									if ($this->classified_variables["payment_type"] == 5) $this->body .="checked";
									$this->body .="></td>\n\t</tr>\n\t";
								}
							}
						}
						break;
						case 6:
						{
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)) ||
									(!$this->configuration_data['use_account_balance']) ||
									(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)))
							{
								//worldpay
								$this->body .="<tr>\n\t\t<td class=payment_choices_field_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[864])."</font><br>
									<font class=payment_choices_field_descriptions>".urldecode($this->messages[865])."</td>\n\t";
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .="<td width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=6>&nbsp;</td>\n\t</tr>\n\t";
								}
								else
								{
									//this is a choice among many
									$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=6 ";
									if ($this->renew_upgrade_variables["payment_type"] == 3) $this->body .="checked";
									$this->body .="></td>\n\t</tr>\n\t";
								}
							}
						}
						break;
						case 7: //site balance
						{
							if ($this->debug_sell)
							{
								echo $this->configuration_data['use_account_balance']." is USE_ACCOUNT_BALANCE<bR>\n";
								echo $this->configuration_data['positive_balances_only']." is POSITIVE_BALANCES_ONLY<bR>\n";
								echo $user_data->ACCOUNT_BALANCE." is ACCOUNT_BALANCE<bR>\n";
							}
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) && ($user_data->ACCOUNT_BALANCE > 0))
								|| (($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
							{
								//will check to see if balance is enough to pay for within transaction detail check
								if ($this->debug_sell) echo $this->configuration_data['positive_balances_only']." is POSITIVE_BALANCES_ONLY<br>\n";
								if (!$this->configuration_data['positive_balances_only'])
								{
									if ($this->debug_sell) echo "display invoicing choice<bR>\n";
									//use the invoicing system
									$this->body .="<tr>\n\t\t<td class=payment_choices_field_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[3237])."</font><br>
										<span class=payment_choices_field_descriptions>";
									$this->body .= urldecode($this->messages[3238]);

									//get current accumulated but unbilled charges
									$this->sql_query = "select * from ".$this->balance_transactions." where (auction_id != 0 or ad_id != 0 or subscription_renewal != 0) and cc_transaction_id = 0 and invoice_id = 0 and user_id = ".$this->classified_user_id;
									if ($this->debug_sell) echo $this->sql_query."<br>";
									$invoice_total_result = $db->Execute($this->sql_query);
									if (!$invoice_total_result)
									{
										return false;
									}
									elseif ($invoice_total_result->RecordCount() > 0)
									{
										$to_be_invoiced = 0;
										while ($show_invoices = $invoice_total_result->FetchNextObject())
										{
											$to_be_invoiced = $to_be_invoiced + $show_invoices->AMOUNT;
										}
										$this->body .= " ".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$to_be_invoiced)." ".$this->configuration_data['postcurrency'];
									}
									else
									{
										$this->body .= " ".$this->configuration_data['precurrency']." ".sprintf("%0.02f",0)." ".$this->configuration_data['postcurrency'];
									}
								}
								else
								{
									if ($this->debug_sell) echo "display site balance choice<bR>\n";
									//display site balance system choice
									$this->body .="<tr>\n\t\t<td class=payment_choices_field_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[3239])."</font><br>
										<span class=payment_choices_field_descriptions>";
									$this->body .= urldecode($this->messages[3240])."<br><br>".urldecode($this->messages[2546]).$this->configuration_data['precurrency']." ".sprintf("%0.02f",$user_data->ACCOUNT_BALANCE)." ".$this->configuration_data['postcurrency']."<br>\n\t";

									if ($this->debug_sell) echo $user_data->ACCOUNT_BALANCE." is the user account balance<br>\n";
									if ($user_data->ACCOUNT_BALANCE >= 0)
									{
										//display positve balance
										//$this->body .= urldecode($this->messages[2546]).$this->configuration_data['precurrency']." ".sprintf("%0.02f",$user_data->ACCOUNT_BALANCE)." ".$this->configuration_data['postcurrency']."<br></td>\n\t";
									}
									else
									{
										//display accumulated charges
										//possibly create function to total uninvoiced charges
										//possibly create function to display unpaid invoice total
										//$this->body .= urldecode($this->messages[2547]).$this->configuration_data['precurrency']." ".sprintf("%0.02f",abs($user_data->ACCOUNT_BALANCE))." ".$this->configuration_data['postcurrency']."<br>";
									}
								}
								$this->body .="</span></td>\n\t<td width=50% colspan=2 valign=top>";
								if  ($user_data->ACCOUNT_BALANCE > 0)
								{
									if ($payment_types_accepted->RecordCount() == 1)
									{
										//this is the only choice so has hidden variable saying this is the type requested
										$this->body .="<input type=radio checked name=c[payment_type] value=7>&nbsp;";
									}
									else
									{
										$this->body .= "<input type=radio name=c[payment_type] ";
										if ($this->classified_variables["payment_type"] == 7) $this->body .="checked";
										elseif 	(($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
												($this->configuration_data['site_balance_override'] == 1))
												$this->body .="checked";
										$this->body .= " value=7>&nbsp;";
									}
								}
								if (strlen(trim($this->error_variables["account_balance"])) > 0)
									$this->body .= "<br><span class=error_message>".$this->error_variables["account_balance"]."</span>";
								$this->body .= "</td>\n\t</tr>\n\t";
							}
						}
						break;
						case 8:
						{
							//NOCHEX
							if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)) ||
									(!$this->configuration_data['use_account_balance']) ||
									(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)))
							{
								$this->body .="
									<tr>
										<td class=payment_choices_field_labels width=50%>
											<font class=payment_choices_field_labels>".urldecode($this->messages[3275])."</font><br>
											<font class=payment_choices_field_descriptions>".urldecode($this->messages[3276])."
										</td>";
								if ($payment_types_accepted->RecordCount() == 1)
								{
									//this is the only choice so has hidden variable saying this is the type requested
									$this->body .="
										<td width=50% colspan=2 valign=top>
											<input type=radio checked name=c[payment_type] value=8>&nbsp;</td>
									</tr>";
								}
								else
								{
									//this is a choice among many
									$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=8 ";
									if ($this->renew_upgrade_variables["payment_type"] == 8) $this->body .="checked";
									$this->body .="></td>\n\t</tr>\n\t";
								}
							}
						}
						break;
					}
				}
			}
			$this->body .="</table>\n\t</td>\n</tr>\n";

			//display discount code functionality
			if ($this->check_discount_code_use($db))
			{
				//display discount code acceptance box
				$this->body .="<tr>\n\t<td><table width=100%><tr><td width=50% class=discount_code_row_left>".urldecode($this->messages[2301]);
				if ($this->error_variables["discount_code_error"])
					$this->body .="<br><font class=discount_code_error_message>".urldecode($this->messages[2302])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td width=50% class=discount_code_row_right>\n\t
					<input type=text class=discount_code_row name=c[discount_code] value=\"".stripslashes(urldecode($this->classified_variables["discount_code"]))."\">\n\t";
				$this->body .="</td>\n</tr>\n</table></td></tr>";
			}

			$this->body .="<tr class=save_choices_button>\n\t<td colspan=2>\n\t<br><input type=submit name=z value=\"".urldecode($this->messages[219])."\" class=save_choices_button>\n\t</td>\n</tr>\n";
			$this->body .="<tr class=edit_ad_links><td colspan=2><a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=edit_category class=edit_ad_links>".urldecode($this->messages[646])."</a></td>\n</tr>\n";
			$this->body .="<tr class=edit_ad_links><td colspan=2><a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=edit_details class=edit_ad_links>".urldecode($this->messages[647])."</a></td>\n</tr>\n";
			$this->body .="<tr class=edit_ad_links>\n\t<td colspan=2><a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=edit_image class=edit_ad_links>".urldecode($this->messages[648])."</a></td>\n</tr>\n";
			$this->body .="<tr class=end_sell_process_link>\n\t<td colspan=2><a href=".trim($this->configuration_data['classifieds_url'])."?a=98 class=end_sell_process_link >".urldecode($this->messages[188])."</a></td>\n</tr>\n";
			$this->body .="</table>\n";
			$this->body .="</form>\n";

			$this->display_page($db);
			return true;
		}
		else
		{
			return false;
		}

	} //end of function classified_billing_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_transaction_variables($db)
	{
		if ($this->debug_sell) echo "TOP OF CHECK_TRANSACTIONS_VARIABLES<br>\n";
		$this->page_id = 12;
		$this->get_text($db);
		$this->error = 0;
		unset($this->error_variables);
		$this->error_variables = array();
		$this->get_cost_of_ad($db);
		//$this->body .=$this->cost_of_ad." is the total<br>\n";
		if ($this->subtotal != 0)
		{
			if (!$this->classified_variables["payment_type"])
			{
				//error in classified_title - was not entered
				$this->error++;
				$this->error_variables["payment_type"] = urldecode($this->messages[222]);
				//echo "bad transaction type<br>\n";
			}


			if ($this->classified_variables["payment_type"] == 2)
			{
				//cc_number
				//put verification script in
				//will not get cc information for 2checkout - bypass
				$this->sql_query = "select cc_id from ".$this->cc_choices." where chosen_cc = 1";
				//echo $this->sql_query." is the query <br>\n";
				$cc_choice_result = $db->Execute($this->sql_query);
				if (!$cc_choice_result)
				{
					return false;
				}
				elseif ($cc_choice_result->RecordCount() == 1)
				{
					$show_cc_choice = $cc_choice_result->FetchNextObject();
					if (($show_cc_choice->CC_ID == 1) || ($show_cc_choice->CC_ID == 3) || ($show_cc_choice->CC_ID == 4) || ($show_cc_choice->CC_ID >= 6))
					{
						if (strlen(trim($this->classified_variables["cc_number"])) == 0)
						{
							$this->error++;
							$this->error_variables["cc_number"] = urldecode($this->messages[223]);
						}

						//check date of expiration
						$current_year = date("y");
						if (($this->classified_variables["cc_exp_year"] < $current_year) || (($this->classified_variables["cc_exp_year"] == $current_year)
							&& ($this->classified_variables["cc_exp_month"] < date("m"))))
						{
							$this->error++;
							$this->error_variables["cc_expiration"] = urldecode($this->messages[224]);
						}
					}
					elseif ($show_cc_choice->CC_ID == 9)
					{
						//manual cc transaction
						//commented code to force the requirement of cvv code for manual transaction
						//if (strlen(trim($this->classified_variables["cvv2_code"])) == 0)
						//{
						//	$this->error++;
						//	$this->error_variables["cc_number"] = urldecode($this->messages[223]);
						//}
					}
				}
				else
					return false;
			}
			elseif ($this->classified_variables["payment_type"] == 7)
			{
				//account balance payment type
				if (($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']))
				{
					$user_data = $this->get_user_data($db,$this->classified_user_id);
					if ($this->debug_sell) echo $user_data->ACCOUNT_BALANCE." is account_balance<Br>\n";
					if ($this->subtotal > $user_data->ACCOUNT_BALANCE)
					{
						$this->error++;
						$this->error_variables["account_balance"] = urldecode($this->messages[2543]);
					}
				}
			}
		}

		if ($this->classified_variables["attention_getter"])
		{
			//check attention getter choice
			if ($this->classified_variables["attention_getter_choice"] == 0)
			{
				$this->error++;
				$this->error_variables["attention_getter"] = urldecode($this->messages[721]);
				//echo $this->error_variables["attention_getter"]." is the attention error message<Br>\n";
			}
		}

		if (strlen(trim($this->classified_variables["discount_code"])) > 0)
		{
			$this->sql_query = "select * from ".$this->discount_codes_table." where
				discount_code = \"".urlencode(trim($this->classified_variables["discount_code"]))."\"
				and active = 1";
			$discount_check_result =  $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$discount_check_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3501];
				return false;
			}
			elseif ($discount_check_result->RecordCount() != 1)
			{
				$this->error++;
				$this->error_variables["discount_code_error"] = 1;
			}
		}

		if (($this->price_plan->TYPE_OF_BILLING == 2) && (!$this->user_currently_subscribed))
		{
			if (!$this->classified_variables["subscription_choice"])
			{
				//error in classified_title - was not entered
				$this->error++;
				$this->error_variables["subscription_choice"] = urldecode($this->messages[3097]);
				if ($this->debug_sell)
				{
					echo "bad subscription choice<br>\n";
					echo $this->error_variables["subscription_choice"]." is error in sub choice<BR>\n";
				}
			}
		}

		//echo $this->error." is the error in check_transaction<br>\n";
		if ($this->error == 0)
			return true;
		else
			return false;
	} //end of function check_transaction_variables

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function final_approval_form($db)
	{
		// Check for SSL first
		if($this->configuration_data['use_ssl_in_sell_process'])
		{
			if(!$_SERVER['HTTPS'])
				$this->redirect($_SERVER['QUERY_STRING']);
		}

		if ($this->debug_sell) echo "TOP OF FINAL_APPROVAL_FORM<br>\n";
		$this->page_id = 13;
		$this->get_text($db);
		$this->update_billing_approved($db,1);

		//save transaction data
		//save the transaction data overwriting
		//update transaction data into the classifieds table

		//echo $this->classified_variables["payment_type"]." is payment_type<br>\n";


		//get totals and taxes if any
		$payment_types_accepted = $this->get_payment_types_accepted($db);

		if (($this->price_plan) && ($payment_types_accepted))
		{
			$this->get_cost_of_ad($db);
			//$this->body .=$this->cost_of_ad." is subtotal cost of ad in final_approval_form<br>\n";
			//$this->body .=$this->subtotal." is subtotal returned in final_approval_form<br>\n";
			if ($this->debug_sell)
			{
				echo $this->amount_to_charge_balance." is amount_to_charge_balance in final_approval_form<BR>\n";
				echo $this->classified_variables["payment_type"]." is payment_type in final_approval_form<BR>\n";
			}
			$user_data = $this->get_user_data($db);
			$this->tax = $this->get_tax($db,$user_data);
			$this->total = ($this->subtotal - $this->discount - $this->amount_to_charge_balance) + $this->tax;
			$this->set_transaction_choices($db);
			$this->body .= "<form onSubmit=\"this.z.disabled=true\" action=";
			$this->body .= $this->configuration_data['classifieds_file_name']."?a=1&b=final_accepted method=post>\n";
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
			$this->body .="<tr class=place_ad_section_title>\n\t<td>".urldecode($this->messages[609])."</td>\n</tr>\n";
			$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[136])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[141])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t<td>";
			//echo $this->subtotal." is subtotal<br>\n";
			//echo $this->cost_of_ad." is cost of ad<Br>\n";
			//echo $this->total." is the total<br>";
			if ($this->total != 0)
			{
				//$this->body .="<tr class=page_description>\n\t<td>";
				//check pay choice information
				switch ($this->classified_variables["payment_type"])
				{
					case 1: //cash
						$this->body .= urldecode($this->messages[149]);
					break;
					case 2: //credit card
						$this->body .= urldecode($this->messages[150]);
					break;
					case 3: //paypal
						$this->body .= urldecode($this->messages[151]);
					break;
					case 4: //money order
						$this->body .= urldecode($this->messages[152]);
					break;
					case 5: //check
						$this->body .= urldecode($this->messages[153]);
					break;
					case 6: //worldpay
						$this->body .= urldecode($this->messages[2545]);
					break;
					case 7: //from your account balance/invoice system
						if (($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']))
							$this->body .= urldecode($this->messages[2544]);
						elseif (($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only']))
							$this->body .= urldecode($this->messages[3265]);
						else
							$this->body .= "";
					break;
					case 8: //NOCHEX
						$this->body .= urldecode($this->messages[3277]);
					break;
					default:
						return false;
				} //end of switch ($this->classified_variables["payment_type"])
			}

			$this->body .= "</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td align=center>\n\t<table cellpadding=3 cellspacing=1>\n\t";
			$this->body .="<tr class=transaction_totals_header>\n\t<td colspan=2>".urldecode($this->messages[142])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td class=ad_features_column_header>".urldecode($this->messages[158])."</td>\n\t";
			$this->body .="<td class=cost_column_header>".urldecode($this->messages[159])."</td>\n</tr>\n";
			//show transaction totals
			//cost of ad
			$this->body .="<tr>\n\t<td width=50% class=ad_cost_field_labels>";
			if ($this->price_plan->TYPE_OF_BILLING == 1)
				$this->body .=urldecode($this->messages[143]);
			elseif ($this->price_plan->TYPE_OF_BILLING == 2)
				$this->body .=urldecode($this->messages[144]);
			$this->body .="</td>\n\t<td width=50% class=ad_cost_data_values>".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->cost_of_ad)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
			if ($this->price_plan->CHARGE_PER_PICTURE > 0)
			{
				$number_of_images = count($this->images_captured);
				//charge per picture
				$quantity = $number_of_images-$this->price_plan->NUM_FREE_PICS;
				if($quantity < 0)
					$quantity = 0;
				$this->body .="<tr>\n\t\t<td class=ad_cost_field_labels>".urldecode($this->messages[649])."</td>\n\t";
				$this->body .="<td class=ad_cost_data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",($this->price_plan->CHARGE_PER_PICTURE * $quantity)).
					" ".$this->configuration_data['postcurrency']." ( ".$quantity." X ".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->CHARGE_PER_PICTURE)." ".$this->configuration_data['postcurrency'].") ";

				if($this->price_plan->NUM_FREE_PICS)
				{
					$this->body .= urldecode($this->messages[3212]) ." = ". $this->price_plan->NUM_FREE_PICS;
				}


				$this->body .= "</td>\n\t</tr>\n\t";

			}

			if (($this->configuration_data['use_bolding_feature']) && ($this->classified_variables["bolding"]))
				$this->body .="<tr>\n\t<td class=ad_cost_field_labels>
					".urldecode($this->messages[145])."</td>\n\t
					<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->BOLDING_PRICE)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			if (($this->configuration_data['use_better_placement_feature']) && ($this->classified_variables["better_placement"]))
				$this->body .="<tr>\n\t<td class=ad_cost_field_labels>
					".urldecode($this->messages[146])."</td>\n\t
					<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->BETTER_PLACEMENT_CHARGE)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			if (($this->configuration_data['use_featured_feature']) && ($this->classified_variables["featured_ad"]))
				$this->body .="<tr>\n\t<td class=ad_cost_field_labels>
					".urldecode($this->messages[147])."</td>\n\t
					<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->FEATURED_AD_PRICE)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			if (($this->configuration_data['use_featured_feature_2']) && ($this->classified_variables["featured_ad_2"]))
				$this->body .="<tr>\n\t<td class=ad_cost_field_labels>
					".urldecode($this->messages[2268])."</td>\n\t
					<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->FEATURED_AD_PRICE_2)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			if (($this->configuration_data['use_featured_feature_3']) && ($this->classified_variables["featured_ad_3"]))
				$this->body .="<tr>\n\t<td class=ad_cost_field_labels>
					".urldecode($this->messages[2269])."</td>\n\t
					<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->FEATURED_AD_PRICE_3)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			if (($this->configuration_data['use_featured_feature_4']) && ($this->classified_variables["featured_ad_4"]))
				$this->body .="<tr>\n\t<td class=ad_cost_field_labels>
					".urldecode($this->messages[2270])."</td>\n\t
					<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->FEATURED_AD_PRICE_4)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			if (($this->configuration_data['use_featured_feature_5']) && ($this->classified_variables["featured_ad_5"]))
				$this->body .="<tr>\n\t<td class=ad_cost_field_labels>
					".urldecode($this->messages[2271])."</td>\n\t
					<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->FEATURED_AD_PRICE_5)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			if (($this->configuration_data['use_attention_getters']) && ($this->classified_variables["attention_getter"]))
				$this->body .="<tr>\n\t<td class=ad_cost_field_labels>
					".urldecode($this->messages[720])."</td>\n\t
					<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->ATTENTION_GETTER_PRICE)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			if (($this->price_plan->CHARGE_PERCENTAGE_AT_AUCTION_END)  && ($this->price_plan->ROLL_FINAL_FEE_INTO_FUTURE))
			{
				if (($this->classified_variables["payment_type"] != 7) || (($this->classified_variables["payment_type"] == 7) && ($this->configuration_data['positive_balances_only'])))
				{
					//display the current totals for final fee and listing to total cost of this listing
					$this->sql_query = "select * from ".$this->classifieds_table." where seller = ".$this->classified_user_id." and final_fee = 1 and final_fee_transaction_number = 0 and ends < ".time();
					$seller_auction_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<bR>\n";
					if (!$seller_auction_result)
					{
						$this->site_error($db->ErrorMsg());
						return false;
					}
					elseif ($seller_auction_result->RecordCount() > 0)
					{
						$this->body .= "<tr>\n\t<td  class=ad_cost_field_labels>
							".urldecode($this->messages[102805])."</td>\n\t
							<td class=ad_cost_data_values>".$this->configuration_data['precurrency']." ".sprintf("%0.02f", $this->final_fee_total)." ".$this->configuration_data['postcurrency']."</td></tr>";
					}
					else
					{
						//there are no final fee costs awaiting charges
						$this->body .= "<tr>\n\t<td  class=ad_cost_field_labels>
							".urldecode($this->messages[102805])."</td>\n\t
							<td class=ad_cost_data_values>".$this->configuration_data['precurrency']." ".sprintf("%0.02f",0)." ".$this->configuration_data['postcurrency']."</td></tr>";
					}
				}
			}

			//subtotal
			$this->body .="<tr>\n\t<td class=subtotal_field_label>".urldecode($this->messages[148])."</td>\n\t";
			$this->body .="<td class=subtotal_data_value>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->subtotal)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			if (strlen(trim($this->classified_variables["discount_code"])) > 0)
			{
				//discount
				$this->body .="<tr>\n\t<td class=subtotal_field_label>".urldecode($this->messages[2303])."</td>\n\t";
				$this->body .="<td class=subtotal_data_value>\n\t ".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->discount)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
			}

			if ($this->amount_to_charge_balance > 0)
			{
				//discount
				$this->body .="<tr>\n\t<td class=subtotal_field_label>amount discount from other account</td>\n\t";
				$this->body .="<td class=subtotal_data_value>\n\t - ".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->amount_to_charge_balance)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
			}


			//tax
			$this->body .="<tr>\n\t<td class=tax_field_label>".urldecode($this->messages[155])."</td>\n\t";
			$this->body .= "<td class=tax_data_value>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->tax)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			//total
			$this->body .="<tr>\n\t<td class=total_field_label>".urldecode($this->messages[156])."</td>\n\t";
			$this->body .= "<td class=total_data_value>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->total)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

			$this->body .="</table>\n\t</td>\n</tr>\n";
			//$this->body .="<tr class=complete_transaction_button>\n\t<td ><br><br><br><input type=submit name=z onclick=\"disableButton(this);\" value=\"".urldecode($this->messages[154])."\" class=complete_transaction_button>\n\t<br><br><br><br><br></td>\n</tr>\n";
			$this->body .="<tr class=complete_transaction_button>\n\t<td ><br><br><br><font class=error_message>".urldecode($this->messages[2410])."</font>\n";
			$this->body .="<br><input type=submit name=z ";
			//$this->body .= "onClick=\"return checksubmit(this)\"";
			$this->body .= " value=\"".urldecode($this->messages[154])."\" class=complete_transaction_button>\n\t<br><br><br><br><br></td>\n</tr>\n";
			$this->body .="<tr class=edit_links>\n\t<td colspan=2><a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=edit_category class=edit_links>".urldecode($this->messages[651])."</a></td>\n</tr>\n";
			$this->body .="<tr class=edit_links>\n\t<td colspan=2><a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=edit_details class=edit_links>".urldecode($this->messages[652])."</a></td>\n</tr>\n";
			$this->body .="<tr class=edit_links>\n\t<td colspan=2><a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=edit_image class=edit_links>".urldecode($this->messages[650])."</a></td>\n</tr>\n";
			$this->body .="<tr class=edit_links>\n\t<td colspan=2><a href=".$this->configuration_data['classifieds_file_name']."?a=1&b=edit_transaction class=edit_links>".urldecode($this->messages[157])."</a></td>\n</tr>\n";
			$this->body .="<tr class=end_sell_process_link>\n\t<td colspan=2><a href=".trim($this->configuration_data['classifieds_url'])."?a=98 class=end_sell_process_link>".urldecode($this->messages[140])."</a></td>\n</tr>\n";

			$this->body .="</table>\n";
			$this->body .="</form>\n";

			$this->display_page($db);
			return true;
		}
		else
		{
			//echo "bad return<BR>\n";
			return false;
		}

	} //end of function final_approval_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function transaction_approved($db)
	{
		if ($this->debug_sell) echo "TOP OF TRANSACTION_APPROVED<br>\n";
		$this->get_cost_of_ad($db);
		$user_data = $this->get_user_data($db);
		$this->tax = $this->get_tax($db,$user_data);
		$this->total = ($this->subtotal - $this->discount - $this->amount_to_charge_balance)+ $this->tax;
		$this->set_transaction_choices($db);
		$this->update_final_approval($db,1);
		$current_time = $this->shifted_time($db);

		$this->sql_query = "update ".$this->classifieds_table." set
			customer_approved = 1
			where id = ".$this->classified_id;
		$transaction_result = $db->Execute($this->sql_query);
		if (!$transaction_result)
		{
			//echo $this->sql_query."<br>\n";
			return false;
		}
		if ($this->debug_sell)
		{
			echo $this->sql_query."<br>\n";
			echo $this->total." is total<br>\n";
			echo $this->subtotal." is subtotal<br>\n";
			echo $this->discount." is discount<br>\n";
			echo $this->discount_percentage." is discount_percentage<br>\n";
			echo $this->amount_to_charge_balance." is amount_to_charge_account<Br>\n";
		}
		if (($this->total == 0) || ($this->subtotal == $this->discount)
			|| ($this->discount_percentage == 100) || ($this->subtotal == $this->amount_to_charge_balance))
		{
			if ($this->debug_sell)
			{
				echo "this has no charge on it<bR>\n";
			}
			if ($this->sell_type == 1)
			{
				//there is no charge to place this ad
				//make it live now
				$show_ad = $this->get_classified_data($db,$this->classified_id);
				$length_of_ad = ($show_ad->DURATION * 86400);
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
				}
				if ($this->debug_sell) echo $this->sql_query."<br>\n";
				$live_result = $db->Execute($this->sql_query);
				if (!$live_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					return false;
				}
			}
			elseif ($this->sell_type == 2)
			{

				//sunit
				if($this->classified_variables["end_time"] == 0)
				{
					if($this->classified_variables["start_time"] == 0)
					{
						$ends = $this->DateAdd("d",$current_time,$this->classified_variables["classified_length"]);
					}
					else
					{
						$ends = $this->DateAdd("d",$this->classified_variables["start_time"],$this->classified_variables["classified_length"]);
					}
				}
				else
				{
					$ends = $this->classified_variables["end_time"];
				}
				//sunit

				if($this->classified_variables["start_time"] == 0)
				{
					$listing_starts = $this->shifted_time($db);
				}
				else
				{
					$listing_starts = $this->classified_variables["start_time"];
				}

				if ($this->debug_sell)
				{
					echo $this->final_fee." is final_fee<BR>\n";
					echo $this->classified_variables["payment_type"]." is classified_variables[payment_type]<Br>\n";
					echo $this->price_plan->ROLL_FINAL_FEE_INTO_FUTURE." is price_plan->ROLL_FINAL_FEE_INTO_FUTURE<BR>\n";
					echo $this->classified_variables["end_time"]." is classified_variables[end_time]<bR>\n";
					echo $this->classified_variables["start_time"]." is classified_variables[start_time]<bR>\n";
					echo $ends." is ends<BR>\n";
					echo $listing_starts." is listing_starts<bR>\n";
				}

				if (($this->final_fee) && ($this->classified_variables["payment_type"] == 2) && (!$this->price_plan->ROLL_FINAL_FEE_INTO_FUTURE))
				{
					if ($this->debug_sell) echo "doing the final fee loop<bR>\n";
					//get a auth_only approval from authorize.net
					$auth_approval_only = 1;
					$user_data = $this->get_user_data($db,$this->classified_user_id);
					$cc = $this->get_cc($db);

					if ($cc)
					{
						include_once($cc->CC_INITIATE_FILE);
						if ($auth_only_approved)
						{
							//there is no charge to place this ad
							//make it live now
							//Finding auction_ends based on start_time, end_time and classified_length


							$this->sql_query = "update ".$this->classifieds_table." set
								live = 1,
								date = ".$listing_starts.",
								ends =".$ends."
								where id = ".$this->classified_id;
							$live_result = $db->Execute($this->sql_query);
							if ($this->debug_sell) echo $this->sql_query."<br>\n";
							if($this->configuration_data->DEBUG_SELL)
							{
								$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_table", "update live, date and ends in auctions table");
							}
							if (!$live_result)
							{
								if ($this->debug_sell) echo $this->sql_query."<br>\n";
								$this->final_fee_approval = 3;
								return true;
							}
							else
							{
								$this->final_fee_approval = 1;
								return true;
							}
						}
						else
						{
							$this->final_fee_approval = 2;
							$this->declined = urlencode($x_response_reason_text);
							return true;
						}
					}
					else
					{
						$this->final_fee_approval = 3;
						return true;
					}
				}
				else
				{
					if ($this->debug_sell) echo "setting date and ends for auction<br>\n";
					//there is no charge to place this auction
					//make it live now
					//need to setup auction_en
					$current_time = $this->shifted_time($db);
					//Finding auction_ends based on start_time, end_time and classified_length

					if  ($this->configuration_data["admin_approves_all_ads"])
					{
						$this->sql_query = "update ".$this->classifieds_table." set
							date = ".$listing_starts.",
							ends = ".$ends."
							where id = ".$this->classified_id;
					}
					else
					{
						$this->sql_query = "update ".$this->classifieds_table." set
							live = 1,
							date = ".$listing_starts.",
							ends =".$ends."
							where id = ".$this->classified_id;
					}
					$live_result = $db->Execute($this->sql_query);
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					if($this->configuration_data->DEBUG_SELL)
					{
						$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_table", "update live, date and ends in auctions table");
					}
					if (!$live_result)
					{
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						return false;
					}
				}
			}
		}
		else
		{
			switch ($this->classified_variables["payment_type"])
			{
				case 1:
				{
					//cash
					//administrator must open classified ad
					return true;

				}
				break;
				case 2:
				{
					//credit card
					//each credit card processor will have its own transaction handler
					//find the right credit card processor
					//echo "hello from credit card<bR>\n";
					$user_data = $this->get_user_data($db,$this->classified_user_id);
					$cc = $this->get_cc($db);
					if ($cc)
					{
						include($cc->CC_INITIATE_FILE);
						return false;
						exit;
					}
					else
					{
						return true;
					}
				}
				break;
				case 3:
				{
					//paypal
					//paypal will have a separate final transaction handler that opens the classified ad
					include("paypal_initiate.php");
					return false;
					exit;
				}
				break;
				case 4:
				{
					//money order
					//administrator must open classified ad
					//$this->body .="hello from money order<bR>\n";
					return true;
				}
				break;
				case 5:
				{
					//check
					//administrator must open classified ad
					//$this->body .="hello from check<bR>\n";
					return true;
				}
				case 6:
				{
					//worldpay
					include("initiate_worldpay.php");
					return true;
				}
				break;
				case 7:
				{
					//use site account balance to pay for ad
					//deduct cost of ad for account balance
					$user_data = $this->get_user_data($db,$this->classified_user_id);
					if ($this->debug_sell)
					{
						echo $this->configuration_data['positive_balances_only']." is POSITIVE_BALANCES_ONLY in transaction_approved<br>\n";
						echo $this->configuration_data['use_account_balance']." is USE_ACCOUNT_BALANCE in transaction_approved<bR>\n";
					}
					if (($this->configuration_data['positive_balances_only']) && ($this->configuration_data['use_account_balance']))
					{
						$new_balance = $user_data->ACCOUNT_BALANCE - $this->total;
						$this->sql_query = "update ".$this->userdata_table." set
							account_balance = ".$new_balance."
							where id = ".$this->classified_user_id;
						if ($this->debug_sell) echo $this->sql_query."<bR>\n";
						$update_balance_results = $db->Execute($this->sql_query);
						if (!$update_balance_results)
						{
							if ($this->debug_sell) echo $this->sql_query."<bR>\n";
							return false;
						}

						$this->sql_query = "insert into ".$this->balance_transactions."
							(user_id,ad_id,amount,date,cc_transaction_id,invoice_id,bolding,better_placement,featured_ad,featured_ad_2,featured_ad_3,
							featured_ad_4,featured_ad_5,attention_getter,approved)
							values
							(".$this->classified_user_id.",".$this->classified_id.",".$this->total.",".$this->shifted_time($db).",999999999,999999999,".
							$this->classified_variables["bolding"].",".$this->classified_variables["better_placement"].",".
							$this->classified_variables["featured_ad"].",".$this->classified_variables["featured_ad_2"].",".
							$this->classified_variables["featured_ad_3"].",".$this->classified_variables["featured_ad_4"].",".
							$this->classified_variables["featured_ad_5"].",".$this->classified_variables["attention_getter"].",\"1\")";
						if ($this->debug_sell) echo $this->sql_query."<bR>\n";
						$insert_invoice_item_result = $db->Execute($this->sql_query);
						if (!$insert_invoice_item_result)
						{
							if ($this->debug_sell) echo $this->sql_query." has an error<bR>\n";
							return false;
						}
					}
					elseif ((!$this->configuration_data['positive_balances_only']) && ($this->configuration_data['use_account_balance']))
					{
						$this->sql_query = "insert into ".$this->balance_transactions."
							(user_id,ad_id,amount,date,bolding,better_placement,featured_ad,featured_ad_2,featured_ad_3,
							featured_ad_4,featured_ad_5,attention_getter,approved)
							values
							(".$this->classified_user_id.",".$this->classified_id.",".$this->total.",".$this->shifted_time($db).",".
							$this->classified_variables["bolding"].",".$this->classified_variables["better_placement"].",".
							$this->classified_variables["featured_ad"].",".$this->classified_variables["featured_ad_2"].",".
							$this->classified_variables["featured_ad_3"].",".$this->classified_variables["featured_ad_4"].",".
							$this->classified_variables["featured_ad_5"].",".$this->classified_variables["attention_getter"].",\"1\")";
						if ($this->debug_sell) echo $this->sql_query."<bR>\n";
						$insert_invoice_item_result = $db->Execute($this->sql_query);
						if (!$insert_invoice_item_result)
						{
							if ($this->debug_sell)
								echo $this->sql_query." has an error<bR>\n";
							return false;
						}
					}

					if ($this->sell_type == 1)
					{
						//and turn on the ad if admin doesn't want to approve all of them
						if (!$this->configuration_data['admin_approves_all_ads'])
						{
							$show_ad = $this->get_classified_data($db,$this->classified_id);
							$length_of_ad = ($show_ad->DURATION * 86400);
							$current_time = $this->shifted_time($db);
							$this->sql_query = "update ".$this->classifieds_table." set
								live = 1,
								date = ".$current_time.",
								ends = ".($current_time + $length_of_ad)."
								where id = ".$this->classified_id;
							if ($this->debug_sell) echo $this->sql_query."<br>\n";
							$live_result = $db->Execute($this->sql_query);
							if (!$live_result)
							{
								if ($this->debug_sell) echo $this->sql_query." has an error<bR>\n";
								return false;
							}
						}
					}
					elseif ($this->sell_type == 2)
					{
						//move final fees into balance transactions table
						if (($this->configuration_data["positive_balances_only"]) && ($this->configuration_data["use_account_balance"]))
						{
							//get all final fees charged within this transaction
							if (($this->price_plan->CHARGE_PERCENTAGE_AT_AUCTION_END) && ($this->price_plan->ROLL_FINAL_FEE_INTO_FUTURE))
							{
								$this->sql_query = "SELECT * FROM ".$this->classifieds_table." where final_fee = 1 and live = 0 and final_fee_transaction_number = 0 and ends < ".$this->shifted_time($db)." and seller = ".$this->classified_user_id;
								$final_fee_result = $db->Execute($this->sql_query);
								if ($this->debug_sell) echo $this->sql_query."<br>\n";
								if($this->configuration_data->DEBUG_SELL)
								{
									$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_table", "get data from auctions table by final fee and being live");
								}
								if (!$final_fee_result)
								{
									if ($this->debug_sell) echo $this->sql_query."<br>\n";
									$this->site_error($db->ErrorMsg());
									return false;
								}
								elseif ($final_fee_result->RecordCount() > 0)
								{
									//calculate the total final fees from the open transactions
									$this->final_fee_total = 0;
									while ($show_final_fee = $final_fee_result->FetchNextObject())
									{
										//check to see that final fee should be charged
										if ($show_final_fee->AUCTION_TYPE == 1)
										{
											//regular auction with only one winner
											$this->sql_query = "select * from ".$this->bid_table." where auction_id = ".$show_final_fee->ID." order by bid desc limit 1";
											$bid_count_result = $db->Execute($this->sql_query);
											if ($this->debug_sell) echo $this->sql_query."<br>\n";
											if($this->configuration_data->DEBUG_SELL)
											{
												$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "bid_table", "get data from bid table by auction id");
											}
											if (!$bid_count_result)
											{
												if ($this->debug_sell) echo $this->sql_query."<br>\n";
												$this->error_message = urldecode($this->messages[81]);
												return false;
											}
											elseif ($bid_count_result->RecordCount() == 1)
											{
												if ($this->debug_sell)
												{
													echo $bid_count_result->RecordCount()." is bid count<BR>\n";
													echo $show_final_fee->FINAL_PRICE." > ".$show_final_fee->RESERVE_PRICE."<br>\n";
												}
												if ($show_final_fee->FINAL_PRICE >= $show_final_fee->RESERVE_PRICE)
												{
													//get final fee percentage
													$this->sql_query = "select charge from ".$this->final_fee_table." where ".
														"(low<=".$show_final_fee->FINAL_PRICE." AND high>=".$show_final_fee->FINAL_PRICE.") and price_plan_id = ".$this->price_plan_id." ORDER BY charge DESC limit 1";
													$increment_result = $db->Execute($this->sql_query);
													if ($this->debug_sell) echo $this->sql_query." 2<br>\n";
													if($this->configuration_data->DEBUG_SELL)
													{
														$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "final_fee_table", "get charge from final fee table by price plan id");
													}
													if (!$increment_result)
													{
														if ($this->debug_sell) echo $this->sql_query."<br>\n";
														return false;
													}
													elseif  ($increment_result->RecordCount() == 1)
													{
														$show_increment = $increment_result->FetchNextObject();
														$final_fee_percentage = $show_increment->CHARGE;
													}
													else
													{
														return false;
													}
													if ($final_fee_percentage > 0)
													{
														$final_fee_charge = (($final_fee_percentage/100) * $show_final_fee->FINAL_PRICE);
														$this->sql_query = "insert into ".$this->balance_transactions."
															(user_id,auction_id,amount,date,final_fee,cc_transaction_id,invoice_id)
															values
															(".$this->classified_user_id.",".$show_final_fee->ID.",".$final_fee_charge.",".$this->shifted_time($db).",1,999999999,999999999)";
														if ($this->debug_sell) echo $this->sql_query."<br>\n";
														$insert_invoice_item_result = $db->Execute($this->sql_query);
														if (!$insert_invoice_item_result)
														{
															if ($this->debug_sell) echo $this->sql_query."<br>\n";
															return false;
														}

														//get balance transaction id
														$balance_transaction_id = $db->Insert_ID();

														//show that final fee has been paid by the site balance
														$this->sql_query = "update ".$this->classifieds_table." set
															final_fee_transaction_number = ".$balance_transaction_id."
															where id = ".$show_final_fee->ID;
														if ($this->debug_sell) echo $this->sql_query."<br>\n";
														$update_final_fee_transaction_result = $db->Execute($this->sql_query);
														if (!$update_final_fee_transaction_result)
														{
															if ($this->debug_sell) echo $this->sql_query."<br>\n";
															return false;
														}

													}
													if (($this->configuration_data->DEBUG_SELL) || ($this->debug_sell)) echo $final_fee_charge." is final fee charge for ".$show_final_fee->ID."<br>\n";
													$this->final_fee_total = $this->final_fee_total + $final_fee_charge;
													if ($this->debug_sell) echo $final_fee_charge." was just added to final fee total<br>\n";
												}
											}
										}
										else
										{
											//dutch auction with multiple winners
											$this->sql_query = "select * from ".$this->bid_table." where auction_id=".$show_final_fee->ID." order by bid desc,time_of_bid asc";
											$bid_result = $db->Execute($this->sql_query);
											if ($this->debug_sell) echo $this->sql_query."<br>\n";
											if($this->configuration_data->DEBUG_SELL)
											{
												$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "bid_table", "get data from bid table by auction id");
											}
											if (!$bid_result)
											{
												if ($this->debug_sell) echo $this->sql_query."<br>\n";
												return false;
											}
											elseif ($bid_result->RecordCount() > 0)
											{
												$total_quantity = $show_final_fee->QUANTITY;
												//echo "total items sold - ".$total_quantity."<br>\n";
												$final_dutch_bid = 0;
												$total_quantity_sold = 0;
												$show_bidder = $bid_result->FetchNextObject();
												do
												{
													$quantity_bidder_receiving = 0;
													if ( $show_bidder->QUANTITY <= $total_quantity )
													{
														$quantity_bidder_receiving = $show_bidder->QUANTITY ;
														if ( $show_bidder->QUANTITY == $total_quantity )
														{
															$final_dutch_bid = $show_bidder->BID;
															//echo $final_dutch_bid." is final bid after quantity_bidder_receiving <= bid quantity<bR>\n";
														}
														$total_quantity = $total_quantity - $quantity_bidder_receiving;
													}
													else
													{
														$quantity_bidder_receiving = $total_quantity;
														$total_quantity = 0;
														$final_dutch_bid = $show_bidder->BID;
														//echo $final_dutch_bid." is final bid after total < bid quantity<bR>\n";
													}
													if ($quantity_bidder_receiving)
													{
														$dutch_bidder_bid = $show_bidder->BID;
														//echo $dutch_bidder_bid." is final bid in quantity_bidder_receiving<bR>\n";
													}
													//echo $total_quantity." is total quantity after bidder - ".$show_bidder->BIDDER."<br>";
													$total_quantity_sold = $total_quantity_sold + $quantity_bidder_receiving;
												} while (($show_bidder = $bid_result->FetchNextObject()) && ($total_quantity != 0) && ($final_dutch_bid == 0));
												if ($final_dutch_bid == 0)
													$final_dutch_bid = $dutch_bidder_bid;
												//echo $final_dutch_bid." is the final_dutch_bid<br>\n";
												///echo $show_final_fee->RESERVE_PRICE." is the reserve<Br>\n";
												//echo $total_quantity_sold." is total_quantity_sold<Br>\n";
												if (($total_quantity_sold) && ($final_dutch_bid) && ($final_dutch_bid >= $show_final_fee->RESERVE_PRICE))
												{
													//determine total cost
													$this->sql_query = "select charge from ".$this->final_fee_table." where ".
														"(low<=".$final_dutch_bid." AND high>=".$final_dutch_bid.") and price_plan_id = ".$this->price_plan->PRICE_PLAN_ID." ORDER BY charge DESC limit 1";
													$increment_result = $db->Execute($this->sql_query);
													if ($this->debug_sell) echo $this->sql_query." 3<br>\n";
													if($this->configuration_data->DEBUG_SELL)
													{
														$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "final_fee_table", "get charge from final fee table by price plan id");
													}
													if (!$increment_result)
													{
														if ($this->debug_sell) echo $this->sql_query."<br>\n";
														return false;
													}
													elseif  ($increment_result->RecordCount() == 1)
													{
														$show_increment = $increment_result->FetchNextObject();
														$final_fee_percentage = $show_increment->CHARGE;
													}
													else
													{
														if ($this->debug_sell) echo $this->sql_query."<br>\n";
														return false;
													}
													if ($final_fee_percentage > 0)
													{
														$final_fee_charge = ((($final_fee_percentage/100) * $final_dutch_bid) * $total_quantity_sold);
														$this->sql_query = "insert into ".$this->balance_transactions."
															(user_id,auction_id,amount,date,final_fee,cc_transaction_id,invoice_id)
															values
															(".$this->classified_user_id.",".$show_final_fee->ID.",".$final_fee_charge.",".$this->shifted_time($db).",1,999999999,999999999)";
														if ($this->debug_sell) echo $this->sql_query."<br>\n";
														$insert_invoice_item_result = $db->Execute($this->sql_query);
														if (!$insert_invoice_item_result)
														{
															if ($this->debug_sell) echo $this->sql_query."<br>\n";
															return false;
														}
													}
													//echo $final_fee_charge." is final fee charge for ".$show_final_fee->ID."<br>\n";
													$this->final_fee_total = $this->final_fee_total + $final_fee_charge;
													//echo $final_fee_charge." was just added to final fee total<br>\n";
												}
											}
											else
											{
												//no bids for this dutch auction
												//no final fees
											}
										}
									}
									//$this->final_fee_total contains the total final fees to be charged within this transaction
								}
								else
								{
									//$this->final_fee_total will be 0 as there are no final fee transactions to be charged
									$this->final_fee_total = 0;
								}
							}
						}
						elseif ((!$this->configuration_data["positive_balances_only"]) && ($this->configuration_data["use_account_balance"]))
						{
							//this is the invoicing choice
							//final fees are not within this "cost of ad" computation
						}

						if ($this->debug_sell)
						{
							echo "---turning on ad within site balance/invoice payment choice<BR>\n";
							echo $this->price_plan->TYPE_OF_BILLING." is the price plan type of billing<bR>\n";
						}
						if ($this->price_plan->TYPE_OF_BILLING == 2)
						{
							$this->sql_query = "select * from ".$this->user_subscriptions_table." where
								subscription_expire > ".$this->shifted_time($db)." and user_id = ".$this->classified_user_id;
							$get_subscriptions_results = $db->Execute($this->sql_query);
							if ($this->debug_sell) echo $this->sql_query."<br>\n";
							if (!$get_subscriptions_results)
							{
								if ($this->debug_sell)
								{
									echo $db->ErrorMsg()." is the mysql error<Br>\n";
									echo $this->sql_query."<br>\n";
								}
								return false;
							}
							elseif ($get_subscriptions_results->RecordCount() == 0)
							{
								//push the subscription up
								$this->sql_query = "select * from ".$this->subscription_choices." where
									period_id = ".$this->classified_variables["subscription_choice"];
								$choice_result = $db->Execute($this->sql_query);
								if ($this->debug_sell) echo $this->sql_query."<br>\n";
								if (!$choice_result)
								{
									if ($this->debug_sell)
									{
										echo $db->ErrorMsg()." is the mysql error<Br>\n";
										echo $this->sql_query." <br>\n";
									}
									return false;
								}
								elseif ($choice_result->RecordCount() == 1 )
								{
									$show_subscription_choice = $choice_result->FetchNextObject();
									$expiration = (($show_subscription_choice->VALUE * 86400) + $this->shifted_time($db));
									$this->sql_query = "insert into ".$this->user_subscriptions_table."
										(user_id,subscription_expire)
										values
										(".$this->classified_user_id.",".$expiration.")";
									$free_subscription_result = $db->Execute($this->sql_query);
									if ($this->debug_sell) echo $this->sql_query."<br>\n";
									if (!$free_subscription_result)
									{
										if ($this->debug_sell) echo $this->sql_query."<br>\n";
										return false;
									}
								}
							}
							else
							{
								if ($this->debug_sell)
								{
									echo "subscription already exists...do not change current subscription<Br>\n";
								}
							}
						}
						elseif ($this->price_plan->TYPE_OF_BILLING == 1)
						{
							//check to see if this was a credit
							$this->sql_query = "select * from ".$this->user_credits_table."
								where user_id = ".$this->classified_user_id." order by credits_expire asc limit 1";
							$credits_results = $db->Execute($this->sql_query);
							if ($this->debug_sell) echo $this->sql_query."<br>\n";
							if (!$credits_results)
							{
								if ($this->debug_sell) echo $this->sql_query."<br>\n";
								return false;
							}
							elseif ($credits_results->RecordCount() == 1)
							{
								//remove one of these credits
								$show_credits = $credits_results->FetchNextObject();
								if ($show_credits->CREDIT_COUNT == 1)
								{
									//delete from the table
									$this->sql_query = "delete from ".$this->user_credits_table." where
										credits_id = ".$show_credits->CREDITS_ID."
										and user_id = ".$this->classified_user_id;
									$remove_credits_results = $db->Execute($this->sql_query);
									if ($this->debug_sell) echo $this->sql_query."<br>\n";
									if (!$remove_credits_results)
									{
										if ($this->debug_sell) echo $this->sql_query."<br>\n";
										return false;
									}
								}
								else
								{
									//remove one from the credit count
									$this->sql_query = "update ".$this->user_credits_table." set
										credit_count = ".($show_credits->CREDIT_COUNT - 1)."
										where credits_id = ".$show_credits->CREDITS_ID."
										and user_id = ".$this->classified_user_id;
									$remove_credit = $db->Execute($this->sql_query);
									if ($this->debug_sell) echo $this->sql_query."<br>\n";
									if (!$remove_credit)
									{
										if ($this->debug_sell) echo $this->sql_query."<br>\n";
										return false;
									}
								}
							}
						}


						//and turn on the auction
						$show_auction = $this->get_classified_data($db,$this->classified_id);
						$current_time = $this->shifted_time($db);
						//Finding auction_ends based on start_time, end_time and classified_length
						//sunit
						if ($this->debug_sell)
						{
							echo $show_auction->END_TIME." is end time from auction data<bR>\n";
							echo $show_auction->START_TIME." is start time from auction data<bR>\n";
							echo $this->classified_variables["start_time"]." is start_time from session<BR>\n";
							echo $this->classified_variables["end_time"]." is end_time from session<BR>\n";
						}
						if($this->classified_variables["end_time"] == 0)
						{
							if($this->classified_variables["start_time"] == 0)
							{
								$ends = $this->DateAdd("d",$current_time,$this->classified_variables["classified_length"]);
								if ($this->debug_sell) echo "ENDS: current time plus duration * 86400:".$ends."<Br>\n";
							}
							else
							{
								$ends = $this->DateAdd("d",$this->classified_variables["start_time"],$this->classified_variables["classified_length"]);
								if ($this->debug_sell) echo "ENDS: start_time: ".$this->classified_variables["start_time"]." plus duration * 86400:".$ends."<Br>\n";
							}
						}
						else
						{
							$ends = $this->classified_variables["end_time"];
							if ($this->debug_sell) echo "ENDS: end time set: ".$this->classified_variables["end_time"]." : ".$ends."<Br>\n";
						}

						if($this->classified_variables["start_time"] == 0)
						{
							$listing_starts = $this->shifted_time($db);
						}
						else
						{
							$listing_starts = $this->classified_variables["start_time"];
						}

						if  ($this->configuration_data->ADMIN_APPROVES_ALL_AUCTIONS)
						{
							$this->sql_query = "update ".$this->classifieds_table." set
								date = ".$listing_starts.",
								ends = ".$ends."
								where id = ".$this->classified_id;
						}
						else
						{
							$this->sql_query = "update ".$this->classifieds_table." set
								live = 1,
								date = ".$listing_starts.",
								ends =".$ends."
								where id = ".$this->classified_id;
						}

						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						$live_result = $db->Execute($this->sql_query);
						if (!$live_result)
						{
							if ($this->debug_sell) echo $this->sql_query." has an error<bR>\n";
							return false;
						}
					}
					$this->update_category_count($db,$this->terminal_category);
					return true;
				}
				break;
				case 8:
				{
					//NOCHEX
					include("nochex_initiate.php");
					return true;
				}
				break;
				default:
					//$this->body .="got to default<br>";
					return false;
			} //end of switch ($this->classified_variables["payment_type"])
		}
		return true;
	} //end of function transaction_approved

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_cost_of_ad($db)
	{
		if ($this->debug_sell)
		{
			echo "<BR>TOP OF GET_COST_OF_AD<bR>\n";
		}
		$this->price_plan = $this->price_plan;
		if ($this->price_plan)
		{
			if ($this->price_plan->TYPE_OF_BILLING == 1)
			{
				//fee based billing
				$this->get_user_credits($db);
				if ($this->user_credits)
				{
					$this->cost_of_ad = 0;
					//$this->body .="subtotal cost of ad in get_cost_of_ad is 0 with billing type 1<br>";
				}
				else
				{
					switch ($this->price_plan->CHARGE_PER_AD_TYPE)
					{
						case 1: //get the charge based on the price field
							if ($this->price_plan->CATEGORY_ID)
								$this->sql_query = "select charge from ".$this->price_plans_increments_table." where".
									"((low<=\"".$this->classified_variables["price"]."\" AND high>=\"".$this->classified_variables["price"]."\") OR".
									"(low<\"".$this->classified_variables["price"]."\" AND high<\"".$this->classified_variables["price"]."\")) and
									price_plan_id = ".$this->users_price_plan." and category_id = ".$this->price_plan->CATEGORY_ID." ORDER BY charge DESC limit 1";
							else
								$this->sql_query = "select charge from ".$this->price_plans_increments_table." where".
									"((low<=\"".$this->classified_variables["price"]."\" AND high>=\"".$this->classified_variables["price"]."\") OR".
									"(low<\"".$this->classified_variables["price"]."\" AND high<\"".$this->classified_variables["price"]."\"))
									and price_plan_id = ".$this->users_price_plan." and category_id = 0 ORDER BY charge DESC limit 1";

							if ($this->debug_sell) echo $this->sql_query."<br>\n";
							$increment_result = $db->Execute($this->sql_query);
							if (!$increment_result)
							{
								$this->cost_of_ad = $this->price_plan->CHARGE_PER_AD;
							}
							elseif  ($increment_result->RecordCount() == 1)
							{
								$show_increment = $increment_result->FetchNextObject();
								$this->cost_of_ad = $show_increment->CHARGE;
							}
							else
							{
								$this->cost_of_ad = $this->price_plan->CHARGE_PER_AD;
							}
							break;

						case 2: //get the charge based on price range charge
							if ($this->price_plan->CATEGORY_ID)
								$this->sql_query = "select length_charge from ".$this->price_plan_lengths_table."
									where length_of_ad = ".$this->classified_variables["classified_length"]."
									and price_plan_id = ".$this->users_price_plan." and category_id = ".$this->price_plan->CATEGORY_ID;
							else
								$this->sql_query = "select length_charge from ".$this->price_plan_lengths_table."
									where length_of_ad = ".$this->classified_variables["classified_length"]."
									and price_plan_id = ".$this->users_price_plan." and category_id = 0";

							if ($this->debug_sell) echo $this->sql_query."<br>\n";
							$length_result = $db->Execute($this->sql_query);
							if (!$length_result)
							{
								//echo $this->sql_query." is the query<br>\n";
								$this->cost_of_ad = $this->price_plan->CHARGE_PER_AD;
							}
							elseif  ($length_result->RecordCount() == 1)
							{
								$show_length_cost = $length_result->FetchNextObject();
								$this->cost_of_ad = $show_length_cost->LENGTH_CHARGE;
							}
							else
							{
								$this->cost_of_ad = $this->price_plan->CHARGE_PER_AD;
							}
							break;

						default:
							$this->cost_of_ad = $this->price_plan->CHARGE_PER_AD;

					} //end of switch
				}
			}
			elseif ($this->price_plan->TYPE_OF_BILLING == 2)
			{
				$this->check_user_subscription($db);
				if ($this->user_currently_subscribed)
				{
					//display the free message
					$this->cost_of_ad = 0;
					//$this->body .="subtotal cost of ad in get_cost_of_ad is 0 with billing type 2<br>";
				}
				else
				{
					//display the subscription cost and for how long
					$this->sql_query = "select * from ".$this->subscription_choices." where period_id = ".$this->classified_variables["subscription_choice"];
					$choice_result = $db->Execute($this->sql_query);
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					if (!$choice_result)
					{
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						return false;
					}
					elseif ($choice_result->RecordCount() == 1 )
					{
						$show_choice = $choice_result->FetchNextObject();
						$this->cost_of_ad = $show_choice->AMOUNT;
					}
					else
					{
						$this->cost_of_ad = 0;
					}
					//$this->cost_of_ad = $this->price_plan->SUBSCRIPTION_BILLING_CHARGE_PER_PERIOD;
					//echo $this->price_plan->SUBSCRIPTION_BILLING_CHARGE_PER_PERIOD." is the SUBSCRIPTION_BILLING_CHARGE_PER_PERIOD<Br>\n";
					//$this->body .="subtotal cost of ad in get_cost_of_ad is ".$this->price_plan->SUBSCRIPTION_BILLING_CHARGE_PER_PERIOD."<br>";
				}
			}
			else
			{
				return false;
			}
			//$this->body .=$this->cost_of_ad." is the subtotal cost of ad in get cost of ad<Br>\n";
			$this->subtotal = $this->cost_of_ad;

			if ($this->debug_sell)
			{
				echo $this->classified_variables["payment_type"]." is payment_type<BR>\n";
				echo $this->configuration_data["positive_balances_only"]." is configuration_data[positive_balances_only]<bR>\n";
				echo $this->price_plan->CHARGE_PERCENTAGE_AT_AUCTION_END." is CHARGE_PERCENTAGE_AT_AUCTION_END<bR>\n";
				echo $this->price_plan->ROLL_FINAL_FEE_INTO_FUTURE." is ROLL_FINAL_FEE_INTO_FUTURE<bR>\n";
			}
			if (($this->classified_variables["payment_type"] != 7) || (($this->classified_variables["payment_type"] == 7) && ($this->configuration_data["positive_balances_only"])))
			{
				if ($this->debug_sell)
				{
					echo $this->sell_type." is sell_type<BR>\n";
				}
				if ($this->sell_type == 2)
				{
					if ($this->debug_sell) echo "setting auction_price_plan from current price plan<BR>\n";
					$auction_price_plan = $this->price_plan;
				}
				else
				{
					if ($this->debug_sell) echo "getting auction_price_plan from database<BR>\n";
					//get the auction price plan attached to this user so that the current final fees can be calculated
					$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
					$group_and_price_result = $db->Execute($this->sql_query);
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					if (!$group_and_price_result)
					{
						if ($this->debug_sell)
						{
							echo $this->sql_query."<br>\n";
							echo $db->ErrorMSg()."<br>\n";
						}
						$this->setup_error = $this->messages[453];
						return false;
					}
					elseif ($group_and_price_result->RecordCount() == 1)
					{
						$show = $group_and_price_result->FetchRow();
						//$show["auction_price_plan_id"];
						$this->sql_query = "select * from ".$this->price_plans_table." where price_plan_id = ".$show["auction_price_plan_id"];
						$price_plan_result = $db->Execute($this->sql_query);
						if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";

						if (!$price_plan_result)
						{
							if ($this->debug_sell)
							{
								echo $this->sql_query." is the query<br>\n";
								echo $db->ErrorMsg()."<br>\n";
							}
							return false;
						}
						elseif ($price_plan_result->RecordCount() == 1)
						{
							$auction_price_plan = $price_plan_result->FetchNextObject();
						}
						else
						{
							$auction_price_plan = 0;
						}

					}
					else
					{
						$auction_price_plan = 0;
					}

				}
				if ($this->debug_sell)
				{
					echo $auction_price_plan->CHARGE_PERCENTAGE_AT_AUCTION_END." is CHARGE_PERCENTAGE_AT_AUCTION_END for auctions<br>\n";
					echo $auction_price_plan->ROLL_FINAL_FEE_INTO_FUTURE." is ROLL_FINAL_FEE_INTO_FUTURE for auctions<br>\n";
				}
				if (($auction_price_plan->CHARGE_PERCENTAGE_AT_AUCTION_END)  && ($auction_price_plan->ROLL_FINAL_FEE_INTO_FUTURE))
				{
					$this->sql_query = "SELECT * FROM ".$this->classifieds_table." where final_fee = 1 and live = 0 and final_fee_transaction_number = 0 and ends < ".$this->shifted_time($db)." and seller = ".$this->classified_user_id;
					$final_fee_result = $db->Execute($this->sql_query);
					if ($this->debug_sell)
					{
						echo $this->sql_query."<br>\n";
						echo $final_fee_result->RecordCount()." is the final_fee_result->RecordCount<br>\n";
					}
					if($this->configuration_data->DEBUG_SELL)
					{
						$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_table", "get data from auctions table by final fee and being live");
					}
					if (!$final_fee_result)
					{
						if ($this->debug_sell) echo $this->sql_query."<br>\n";
						$this->site_error($db->ErrorMsg());
						return false;
					}
					elseif ($final_fee_result->RecordCount() > 0)
					{
						//calculate the total final fees from the open transactions
						$this->final_fee_total = 0;
						//echo "final fee total is now 0<Br>\n";
						while ($show_final_fee = $final_fee_result->FetchNextObject())
						{
							if($show_final_fee->ITEM_TYPE == 1)
							{
								// If classified then lets skip it completely
								continue;
							}

							//check to see that final fee should be charged
							if ($show_final_fee->AUCTION_TYPE == 1)
							{
								//regular auction with only one winner
								$this->sql_query = "select * from ".$this->bid_table." where auction_id = ".$show_final_fee->ID." order by bid desc limit 1";
								$bid_count_result = $db->Execute($this->sql_query);
								if ($this->debug_sell) echo $this->sql_query."<br>\n";
								if($this->configuration_data->DEBUG_SELL)
								{
									$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "bid_table", "get data from bid table by auction id");
								}
								if (!$bid_count_result)
								{
									if ($this->debug_sell) echo $this->sql_query."<br>\n";
									$this->error_message = urldecode($this->messages[81]);
									return false;
								}
								elseif ($bid_count_result->RecordCount() == 1)
								{
									if($this->debug_sell)
									{
										echo $bid_count_result->RecordCount()." is bid count<BR>\n";
										echo $show_final_fee->FINAL_PRICE." > ".$show_final_fee->RESERVE_PRICE."<br>\n";
									}
									if ($show_final_fee->FINAL_PRICE >= $show_final_fee->RESERVE_PRICE)
									{
										//get final fee percentage
										$this->sql_query = "select charge from ".$this->final_fee_table." where ".
											"(low<=".$show_final_fee->FINAL_PRICE." AND high>=".$show_final_fee->FINAL_PRICE.") and price_plan_id = ".$auction_price_plan->PRICE_PLAN_ID." ORDER BY charge DESC limit 1";
										$increment_result = $db->Execute($this->sql_query);
										if ($this->debug_sell) echo $this->sql_query."<br>\n";
										if($this->configuration_data->DEBUG_SELL)
										{

											$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "final_fee_table", "get charge from final fee table by price plan id");
										}
										if (!$increment_result)
										{
											if ($this->debug_sell)
											{
												echo $this->sql_query."<br>\n";
												echo $db->ErrorMsg()."<bR>\n";
											}
											return false;
										}
										elseif  ($increment_result->RecordCount() == 1)
										{
											$show_increment = $increment_result->FetchNextObject();
											$final_fee_percentage = $show_increment->CHARGE;
										}
										else
										{
											if ($this->debug_sell) echo "returning false while getting final_fee total<Br>\n";
											return false;
										}
										if ($final_fee_percentage > 0)
										{
											$final_fee_charge = sprintf("%01.2f",(($final_fee_percentage/100) * $show_final_fee->FINAL_PRICE));
										}
										$this->final_fee_total = $this->final_fee_total + $final_fee_charge;
										if($this->debug_sell)
										{
											echo $final_fee_charge." was just added to final fee total<br>\n";
											echo $final_fee_charge." is final fee charge for ".$show_final_fee->ID." with final fee % of ".$final_fee_percentage."<br>\n";
										}

									}
								}
							}
							else
							{
								//dutch auction with multiple winners
								$this->sql_query = "select * from ".$this->bid_table." where auction_id=".$show_final_fee->ID." order by bid desc,time_of_bid asc";
								$bid_result = $db->Execute($this->sql_query);
								if ($this->debug_sell) echo $this->sql_query."<br>\n";
								if($this->configuration_data->DEBUG_SELL)
								{
									$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "bid_table", "get data from bid table by auction id");
								}
								if (!$bid_result)
								{
									if ($this->debug_sell) echo $this->sql_query."<br>\n";
									return false;
								}
								elseif ($bid_result->RecordCount() > 0)
								{
									$total_quantity = $show_final_fee->QUANTITY;
									//echo "total items sold - ".$total_quantity."<br>\n";
									$final_dutch_bid = 0;
									$total_quantity_sold = 0;
									$show_bidder = $bid_result->FetchNextObject();
									do
									{
										$quantity_bidder_receiving = 0;
										if ( $show_bidder->QUANTITY <= $total_quantity )
										{
											$quantity_bidder_receiving = $show_bidder->QUANTITY ;
											if ( $show_bidder->QUANTITY == $total_quantity )
											{
												$final_dutch_bid = $show_bidder->BID;
												if ($this->debug_sell) echo $final_dutch_bid." is final bid after quantity_bidder_receiving <= bid quantity<bR>\n";
											}
											$total_quantity = $total_quantity - $quantity_bidder_receiving;
										}
										else
										{
											$quantity_bidder_receiving = $total_quantity;
											$total_quantity = 0;
											$final_dutch_bid = $show_bidder->BID;
											if ($this->debug_sell) echo $final_dutch_bid." is final bid after total < bid quantity<bR>\n";
										}
										if ($quantity_bidder_receiving)
										{
											$dutch_bidder_bid = $show_bidder->BID;
											if ($this->debug_sell) echo $dutch_bidder_bid." is final bid in quantity_bidder_receiving<bR>\n";
										}
										if ($this->debug_sell) echo $total_quantity." is total quantity after bidder - ".$show_bidder->BIDDER."<br>";
										$total_quantity_sold = $total_quantity_sold + $quantity_bidder_receiving;
									} while (($show_bidder = $bid_result->FetchNextObject()) && ($total_quantity != 0) && ($final_dutch_bid == 0));
									if ($final_dutch_bid == 0)
										$final_dutch_bid = $dutch_bidder_bid;
									if ($this->debug_sell)
									{
										echo $final_dutch_bid." is the final_dutch_bid<br>\n";
										echo $show_final_fee->RESERVE_PRICE." is the reserve<Br>\n";
										echo $total_quantity_sold." is total_quantity_sold<Br>\n";
									}
									if (($total_quantity_sold) && ($final_dutch_bid) && ($final_dutch_bid >= $show_final_fee->RESERVE_PRICE))
									{
										//determine total cost
										$this->sql_query = "select charge from ".$this->final_fee_table." where ".
											"(low<=".$final_dutch_bid." AND high>=".$final_dutch_bid.") and price_plan_id = ".$this->users_price_plan." ORDER BY charge DESC limit 1";
										$increment_result = $db->Execute($this->sql_query);
										if ($this->debug_sell) echo $this->sql_query."<br>\n";
										if($this->configuration_data->DEBUG_SELL)
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "final_fee_table", "get charge from final fee table by price plan id");
										}
										if (!$increment_result)
										{
											if ($this->debug_sell) echo $this->sql_query."<br>\n";
											return false;
										}
										elseif  ($increment_result->RecordCount() == 1)
										{
											$show_increment = $increment_result->FetchNextObject();
											$final_fee_percentage = $show_increment->CHARGE;
										}
										else
										{
											if ($this->debug_sell) echo $this->sql_query."<br>\n";
											return false;
										}
										if ($final_fee_percentage > 0)
										{

											$final_fee_charge = sprintf("%01.2f",((($final_fee_percentage/100) * $final_dutch_bid) * $total_quantity_sold));

										}
										if ($this->debug_sell) echo $final_fee_charge." is final fee charge for ".$show_final_fee->ID."<br>\n";
										$this->final_fee_total = $this->final_fee_total + $final_fee_charge;
										if ($this->debug_sell) echo $final_fee_charge." was just added to final fee total<br>\n";
									}
								}
								else
								{
									//no bids for this dutch auction
									//no final fees
								}
							}
						}
					}
					$this->subtotal = $this->subtotal + $this->final_fee_total;
					if ($this->debug_sell)
					{
						echo $this->subtotal." is subtotal after adding final fees of: ". $this->final_fee_total."<bR>\n";
					}
				}
			}

			if ($this->price_plan->CHARGE_PER_PICTURE > 0)
			{
				$number_of_images = count($this->images_captured);
				$quantity = $number_of_images-$this->price_plan->NUM_FREE_PICS;
				if($quantity < 0)
					$quantity = 0;
				$this->subtotal  = $this->subtotal + ($this->price_plan->CHARGE_PER_PICTURE * $quantity);
			}
			if (($this->configuration_data['use_bolding_feature']) && ($this->classified_variables["bolding"]))
				$this->subtotal  = $this->subtotal + $this->price_plan->BOLDING_PRICE;

			if (($this->configuration_data['use_better_placement_feature']) && ($this->classified_variables["better_placement"]))
				$this->subtotal  = $this->subtotal  + $this->price_plan->BETTER_PLACEMENT_CHARGE;

			if (($this->configuration_data['use_featured_feature']) && ($this->classified_variables["featured_ad"]))
				$this->subtotal  = $this->subtotal  + $this->price_plan->FEATURED_AD_PRICE;

			if (($this->configuration_data['use_featured_feature_2']) && ($this->classified_variables["featured_ad_2"]))
				$this->subtotal  = $this->subtotal  + $this->price_plan->FEATURED_AD_PRICE_2;

			if (($this->configuration_data['use_featured_feature_3']) && ($this->classified_variables["featured_ad_3"]))
				$this->subtotal  = $this->subtotal  + $this->price_plan->FEATURED_AD_PRICE_3;

			if (($this->configuration_data['use_featured_feature_4']) && ($this->classified_variables["featured_ad_4"]))
				$this->subtotal  = $this->subtotal  + $this->price_plan->FEATURED_AD_PRICE_4;

			if (($this->configuration_data['use_featured_feature_5']) && ($this->classified_variables["featured_ad_5"]))
				$this->subtotal  = $this->subtotal  + $this->price_plan->FEATURED_AD_PRICE_5;

			if (($this->configuration_data['use_attention_getters']) && ($this->classified_variables["attention_getter"]))
				$this->subtotal  = $this->subtotal  + $this->price_plan->ATTENTION_GETTER_PRICE;

			//check for discount percentage
			if (strlen(trim($this->classified_variables["discount_code"])) > 0)
			{
				$this->sql_query = "select * from ".$this->discount_codes_table." where
					discount_code = \"".urlencode(trim($this->classified_variables["discount_code"]))."\"
					and active = 1";
				$discount_check_result =  $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
				if (!$discount_check_result)
				{
					if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[3501];
					return false;
				}
				elseif ($discount_check_result->RecordCount() == 1)
				{
					$discount_code = $discount_check_result->FetchNextObject();
					if ($discount_code->USER_ID)
					{
						//there is a user id
						//check to see if the user_id has credits attached
						$this->sql_query = "select * from ".$this->user_credits_table." where
							user_id = ".$discount_code->USER_ID;
						$credit_result =  $db->Execute($this->sql_query);
						if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
						if (!$credit_result)
						{
							if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
							$this->error_message = $this->messages[3501];
							return false;
						}
						elseif ($credit_result->RecordCount() > 0)
						{
							//if credits attached the cost of the ad is 0
							//subtract the cost of the ad from the subtotal
							$this->subtotal = ($this->subtotal - $this->cost_of_ad);
							$this->cost_of_ad = 0;
							//discount applies to extra features
						}
						elseif ($credit_result->RecordCount() == 0)
						{
							//there are no credits attached to the discount code user id
							//discount code is the only discount that applies
						}
					}
					$this->discount = round(($this->subtotal * ($discount_code->DISCOUNT_PERCENTAGE / 100)),2);

					if (($this->subtotal - $this->discount) > 0)
					{
						//check to see if there is a site balance to take remaining costs from
						$discount_user_data = $this->get_user_data($db,$discount_code->USER_ID);
						if ($discount_user_data->ACCOUNT_BALANCE > ($this->subtotal - $this->discount))
						{
							$this->amount_to_charge_balance = ($this->subtotal - $this->discount);
							if ($this->debug_sell)
							{
								echo $this->amount_to_charge_balance." is amount_to_charge_balance in get_cost_of_ad<BR>\n";
							}
						}
					}

					//save discount id, discount percentage and amount discounted
					$this->sql_query = "update ".$this->classifieds_table." set
						discount_id = \"".$discount_code->DISCOUNT_ID."\",
						discount_amount = \"".$this->discount."\",
						discount_percentage = \"".$discount_code->DISCOUNT_PERCENTAGE."\"
						where id = ".$this->classified_id;
					$update_discount_result =  $db->Execute($this->sql_query);
					if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
					if (!$update_discount_result)
					{
						if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[3501];
						return false;
					}
				}
				else
				{
					//discount code does not match any discount code
				}
			}
			//echo $this->subtotal." is subtotal in get cost of ad<br>\n";
			if ($this->debug_sell)
			{
				echo $this->amount_to_charge_balance." is amount_to_charge_balance in bottom of get_cost_of_ad<BR>\n";
			}
			return true;
		}
		else
		{
			return false;
		}
	} // end of function get_cost_of_ad

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_price_plan($db)
	{
		//get price plan specifics
		if ($this->debug_sell)
		{
			echo "TOP OF GET_PRICE_PLAN<br>\n";
			echo $this->users_price_plan." is users_price_plan<bR>\n";
			echo $this->terminal_category." is terminal_category<BR>\n";
		}
		if ($this->users_price_plan)
		{
			$this->sql_query = "select * from ".$this->price_plans_table." where price_plan_id = ".$this->users_price_plan;
			$price_plan_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";

			if (!$price_plan_result)
			{
				if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
				if ($this->debug_sell) echo "BOTTOM OF GET_PRICE_PLAN9<BR>\n";
				return false;
			}
			elseif ($price_plan_result->RecordCount() == 1)
			{
				$show = $price_plan_result->FetchNextObject();
				if ($this->terminal_category)
				{
					$category_next = $this->terminal_category;
					$overriding_category = 0;
					do
					{
						$this->sql_query = "select category_id,parent_id from ".$this->categories_table."
							where category_id = ".$category_next;
						$category_result =  $db->Execute($this->sql_query);
						if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
						if (!$category_result)
						{
							if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
							$this->error_message = $this->messages[3501];
							if ($this->debug_sell) echo "BOTTOM OF GET_PRICE_PLAN8<BR>\n";
							return false;
						}
						elseif ($category_result->RecordCount() == 1)
						{
							$show_category = $category_result->FetchNextObject();
							$this->sql_query = "select * from ".$this->price_plans_categories_table."
								where category_id = ".$show_category->CATEGORY_ID." and price_plan_id = ".$this->users_price_plan;
							$category_price_plan_result =  $db->Execute($this->sql_query);
							if ($this->debug_sell) echo $this->sql_query." is the query<br>\n";
							if ($category_price_plan_result->RecordCount() == 1)
							{
								$overriding_category = $show_category->CATEGORY_ID;
								$show_category_price_plan = $category_price_plan_result->FetchNextObject();
							}
							$category_next = $show_category->PARENT_ID;
						}
						else
						{
							if ($this->debug_sell)
							{
								echo "error getting category specific price plan in GET_PRICE_PLAN<br>\n";
								if ($this->debug_sell) echo "BOTTOM OF GET_PRICE_PLAN7<BR>\n";
							}
							return false;
						}
					} while (($show_category->PARENT_ID != 0 ) && ($overriding_category== 0));

					if ( $overriding_category != 0 )
					{
						if ($show->TYPE_OF_BILLING == 2)
						{
							//there is an overriding category specific price plan
							//overwrite the returns from the base price plan with these
							$show->FEATURED_AD_PRICE = $show_category_price_plan->FEATURED_AD_PRICE;
							$show->FEATURED_AD_PRICE_2 = $show_category_price_plan->FEATURED_AD_PRICE_2;
							$show->FEATURED_AD_PRICE_3 = $show_category_price_plan->FEATURED_AD_PRICE_3;
							$show->FEATURED_AD_PRICE_4 = $show_category_price_plan->FEATURED_AD_PRICE_4;
							$show->FEATURED_AD_PRICE_5 = $show_category_price_plan->FEATURED_AD_PRICE_5;
							$show->BOLDING_PRICE = $show_category_price_plan->BOLDING_PRICE;
							$show->ATTENTION_GETTER_PRICE = $show_category_price_plan->ATTENTION_GETTER_PRICE;
							$show->CHARGE_PER_PICTURE = $show_category_price_plan->CHARGE_PER_PICTURE;
							$show->BETTER_PLACEMENT_CHARGE = $show_category_price_plan->BETTER_PLACEMENT_CHARGE;
							if ($this->debug_sell) echo "BOTTOM OF GET_PRICE_PLAN1<BR>\n";
							return $show;
						}
						else
						{
							//this is a fee type
							$show_category_price_plan->TYPE_OF_BILLING = 1;
							$show_category_price_plan->NUM_FREE_PICS = $show->NUM_FREE_PICS;
							if ($this->debug_sell) echo "BOTTOM OF GET_PRICE_PLAN2<BR>\n";
							return $show_category_price_plan;
						}
					}
					else
					{
						if ($this->debug_sell) echo "BOTTOM OF GET_PRICE_PLAN3<BR>\n";
						return $show;
					}
				}
				else
				{
					if ($this->debug_sell) echo "BOTTOM OF GET_PRICE_PLAN4<BR>\n";
					return $show;
				}
			}
			else
			{
				if ($this->debug_sell) echo "no price return<br>\n";
				$this->price_plan = 0;
				if ($this->debug_sell)
				{
					if ($this->debug_sell) echo "BOTTOM OF GET_PRICE_PLAN5<BR>\n";
				}
				return false;
			}
		}
		else
		{
			if ($this->debug_sell) echo "no price plan id<Br>\n";
			$this->price_plan = 0;
			if ($this->debug_sell)
			{
				if ($this->debug_sell) echo "BOTTOM OF GET_PRICE_PLAN6<BR>\n";
			}
			return false;
		}

	} //end of function get_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function expire_credits($db)
	{
		//expire user credits
		$this->sql_query = "delete from ".$this->user_credits_table." where credits_expire < ".$this->shifted_time($db)." or credit_count = 0";
		$expire_results = $db->Execute($this->sql_query);

		if (!$expire_results)
		{
			return false;
		}
		else
		{
			return true;
		}
	} //end of function expire_credits

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_user_credits($db)
	{
		//expire user credits
		$this->sql_query = "select * from ".$this->user_credits_table." where user_id = ".$this->classified_user_id." order by credits_expire asc limit 1";
		$credits_results = $db->Execute($this->sql_query);

		if (!$credits_results)
		{
			return false;
		}
		elseif ($credits_results->RecordCount() == 1)
		{
			$this->user_credits = 1;
		}
		else
		{
			$this->user_credits = 0;
		}
	} //end of function get_user_credits

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function remove_a_users_credit($db,$discount_code_user_id=0)
	{
		if ($discount_code_user_id)
			$user_id_removed_from = $discount_code_user_id;
		else
			$user_id_removed_from = $this->classified_user_id;
		$this->sql_query = "select * from ".$this->user_credits_table." where user_id = ".$user_id_removed_from." order by credits_expire asc limit 1";
		$credits_results = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$credits_results)
		{
			return false;
		}
		elseif ($credits_results->RecordCount() == 1)
		{
			//remove one of these credits
			$show_credits = $credits_results->FetchNextObject();
			if ($show_credits->CREDIT_COUNT == 1)
			{
				//delete from the table
				$this->sql_query = "delete from ".$this->user_credits_table." where
					credits_id = ".$show_credits->CREDITS_ID."
					and user_id = ".$user_id_removed_from;
				$remove_credits_results = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$remove_credits_results)
				{
					return false;
				}
			}
			else
			{
				//remove one from the credit count
				$this->sql_query = "update ".$this->user_credits_table." set
					credit_count = ".($show_credits->CREDIT_COUNT - 1)."
					where credits_id = ".$show_credits->CREDITS_ID."
					and user_id = ".$user_id_removed_from;
				$remove_credit = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$remove_credit)
				{
					return false;
				}
			}
		}
		return true;

	} //end of function remove_a_users_credit

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_billing_period($db,$billing_period)
	{
		//expire user credits
		$this->sql_query = "select display_value from ".$this->choices_table." where type_of_choice = 2 and value = ".$billing_period;
		$billing_period_results = $db->Execute($this->sql_query);

		if (!$billing_period_results)
		{
			return false;
		}
		elseif ($billing_period_results->RecordCount() == 1)
		{
			$show_billing_period = $billing_period_results->FetchNextObject();
			return $show_billing_period->DISPLAY_VALUE;
		}
	} //end of function get_billing_period

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function expire_subscriptions_old($db)
	{
		$this->sql_query = "select * from ".$this->user_subscriptions_table." where subscription_expire < ".$this->shifted_time($db);
		$subscriptions_to_expire_results = $db->Execute($this->sql_query);

		if (!$subscriptions_to_expire_results)
		{
			return false;
		}
		elseif ($subscriptions_to_expire_results->RecordCount() > 0)
		{
			$this->sql_query = "delete from ".$this->user_subscriptions_table." where subscription_expire < ".$this->shifted_time($db);
			$expire_subscriptions_results = $db->Execute($this->sql_query);

			if (!$expire_subscriptions_results)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		return true;

	}// end of function expire_subscriptions_old

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_user_subscription($db,$initialize=0)
	{
		if ($this->debug_sell)
		{
			echo "<BR>TOP OF CHECK_USER_SUBSCRIPTION<Br>\n";
		}
		$this->sql_query = "select * from ".$this->user_subscriptions_table." where subscription_expire > ".$this->shifted_time($db)." and user_id = ".$this->classified_user_id;
		$get_subscriptions_results = $db->Execute($this->sql_query);
		if ($this->debug_sell) echo $this->sql_query."<br>\n";
		if (!$get_subscriptions_results)
		{
			if ($this->debug_sell) echo $this->sql_query."<br>\n";
			return false;
		}
		elseif ($get_subscriptions_results->RecordCount() == 0)
		{
			$this->user_currently_subscribed = 0;
			return true;
		}
		elseif ($get_subscriptions_results->RecordCount() > 0)
		{
			if ($initialize)
			{
				//set the current price plan to the subscription price plan
				$current_subscription = $get_subscriptions_results->FetchNextObject();
				if ($current_subscripiton->PRICE_PLAN_ID)
				{
					$this->users_price_plan = $current_subscripiton->PRICE_PLAN_ID;
				}
				$this->sql_query = "update ".$this->sell_table." set
					users_price_plan = ".$current_subscription->PRICE_PLAN_ID."
					where session = \"".$this->session_id."\"";
				$update_group_and_price_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<br>\n";
				if (!$update_group_and_price_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					$this->setup_error = $this->messages[453];
					return false;
				}
			}
			$this->user_currently_subscribed = 1;
			return true;
		}
	} // end of function check_user_subscription

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_subscriptions_and_credits($db)
	{
		if ($this->price_plan)
		{
			if ($this->price_plan->TYPE_OF_BILLING == 2)
			{
				if (!$this->user_currently_subscribed)
				{
					//push the subscription up
					$this->sql_query = "delete from ".$this->user_subscriptions_table." where
						user_id = ".$this->classified_user_id;
					//echo $this->sql_query." is the query<br>\n";
					$free_subscription_result = $db->Execute($this->sql_query);
					if (!$free_subscription_result)
					{
						$this->site_error($this->sql_query,$db->ErrorMsg());
						//$this->body .=$this->sql_query." is the query<br>\n";
						$this->error["confirm"] =urldecode($this->messages[229]);
						return false;
					}

					//get subscription period chosen
					$this->sql_query = "select * from ".$this->subscription_choices." where period_id = ".$this->classified_variables["subscription_choice"];
					$choice_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$choice_result)
					{
						return false;
					}
					elseif ($choice_result->RecordCount() == 1 )
					{
						$show_subscription_choice = $choice_result->FetchNextObject();
						//build expiration from subscription choice
						$expiration = (($show_subscription_choice->VALUE * 86400) + $this->shifted_time($db));
						$this->sql_query = "insert into ".$this->user_subscriptions_table."
							(user_id,subscription_expire,price_plan_id)
							values
							(".$this->classified_user_id.",".$expiration.",".$this->users_price_plan.")";
						//echo $this->sql_query." is the query<br>\n";
						$free_subscription_result = $db->Execute($this->sql_query);
						if (!$free_subscription_result)
						{
							$this->site_error($this->sql_query,$db->ErrorMsg());
							//$this->body .=$this->sql_query." is the query<br>\n";
							$this->error["confirm"] =urldecode($this->messages[229]);
							return false;
						}
					}
				}
				//else
				//	$this->body .="user is currently subscribed<Br>\n";
			}
			elseif ($this->price_plan->TYPE_OF_BILLING == 1)
			{
				//check to see if this was a credit
				if (strlen(trim($this->classified_variables["discount_code"])) > 0)
				{
					$this->sql_query = "select * from ".$this->discount_codes_table." where
						discount_code = \"".urlencode(trim($this->classified_variables["discount_code"]))."\"
						and active = 1";
					$discount_check_result =  $db->Execute($this->sql_query);
					//echo $this->sql_query." is the query<br>\n";
					if (!$discount_check_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[3501];
						return false;
					}
					elseif ($discount_check_result->RecordCount() == 1)
					{
						$discount_code = $discount_check_result->FetchNextObject();
						if ($discount_code->USER_ID)
						{
							//there is a user id
							//check to see if the user_id has account balance attached
							$this->remove_from_users_account($db,$discount_code->USER_ID);
							//check to see if the user_id has credits attached
							$this->remove_a_users_credit($db,$discount_code->USER_ID);
						}
					}
					else
					{
						//discount code does not match any discount code
					}
				}
				elseif ($this->user_credits)
				{
					//remove a credit
					$this->remove_a_users_credit($db);
					$this->remove_from_users_account($db);
				}
				else
				{
					$this->remove_from_users_account($db);
				}

			}
		}
		else
		{
			return false;
		}
	} //end of function check_subscriptions_and_credits

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function set_transaction_choices($db)
	{
		if ($this->classified_variables["attention_getter"])
		{
			//get the attention getter url
			$this->sql_query = "select * from ".$this->choices_table." where choice_id = ".$this->classified_variables["attention_getter_choice"];
			$attention_getter_result = $db->Execute($this->sql_query);
			//$this->body .=$this->sql_query."<br>\n";
			if (!$attention_getter_result)
			{
				$this->body .=$this->sql_query."<br>\n";
				return false;
			}
			elseif ($attention_getter_result->RecordCount() == 1)
			{
				$show_attention_getter = $attention_getter_result->FetchNextObject();
				$attention_getter_url = $show_attention_getter->VALUE;
			}
			else
			{
				$this->classified_variables["attention_getter"] = 0;
				$attention_getter_url = "";
			}
		}
		else
			$attention_getter_url = "";

		$this->sql_query = "update ".$this->classifieds_table." set
			transaction_type = \"".$this->classified_variables["payment_type"]."\",
			bolding = \"".$this->classified_variables["bolding"]."\",
			better_placement = \"".$this->classified_variables["better_placement"]."\",
			featured_ad = \"".$this->classified_variables["featured_ad"]."\",
			featured_ad_2 = \"".$this->classified_variables["featured_ad_2"]."\",
			featured_ad_3 = \"".$this->classified_variables["featured_ad_3"]."\",
			featured_ad_4 = \"".$this->classified_variables["featured_ad_4"]."\",
			featured_ad_5 = \"".$this->classified_variables["featured_ad_5"]."\",
			attention_getter = \"".$this->classified_variables["attention_getter"]."\",
			attention_getter_url = \"".$attention_getter_url."\",
			subscription_choice = \"".$this->classified_variables["subscription_choice"]."\",
			total = \"".$this->total."\",
			subtotal = \"".$this->subtotal."\",
			tax = \"".$this->tax."\"
			where id = ".$this->classified_id;

		$transaction_result = $db->Execute($this->sql_query);
		//$this->body .=$this->sql_query."<br>\n";
		if (!$transaction_result)
		{
			//echo $this->sql_query."<br>\n";
			return false;
		}
		return true;
	} //end of function set_transaction_choices

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function setup_sell_error_display($db)
	{
		$this->page_id = 8;
		$this->get_text($db);
		$this->body .="<table width=100% cellpadding=2 cellspacing=1>\n<tr>\n\t<td align=center><br><br>\n\t";
		$this->body .="<font class=page_title>".urldecode($this->messages[70])."\n\t<BR><BR>\n\t";
		$this->body .="<font class=error_message>".urldecode($this->setup_error)."<BR><BR>\n\t</td>\n</tr>\n</table>\n";
		$this->display_page($db);

	} //end of function setup_sell_error_display

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_cc($db)
	{
		$this->sql_query = "select * from ".$this->cc_choices." where chosen_cc = 1";

		$cc_result = $db->Execute($this->sql_query);
		//$this->body .=$this->sql_query."<br>\n";
		if (!$cc_result)
		{
			$this->body .=$this->sql_query."<br>\n";
			return false;
		}
		elseif ($cc_result->RecordCount() == 1)
		{
			$show = $cc_result->FetchNextObject();
			return $show;
		}
		else
		{
			return false;
		}

	} //end of function get_cc

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_subscription_choices_table($db)
	{
		$this->sql_query = "select * from ".$this->subscription_choices." where price_plan_id = ".$this->users_price_plan." order by value asc";
		$choices_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$choices_result)
		{
			return false;
		}
		elseif ($choices_result->RecordCount() > 0 )
		{
			while ($show_choices = $choices_result->FetchNextObject())
			{
				$choices_table .= "<tr>\n\t<td align=right width=50% class=ad_cost_features_field_labels>".$show_choices->DISPLAY_VALUE."
					<input type=radio name=c[subscription_choice] value=".$show_choices->PERIOD_ID;
				if (($choices_result->RecordCount() == 1) || ($this->classified_variables["subscription_choice"] == $show_choices->PERIOD_ID))
					$choices_table .= " checked";
				$choices_table .= "></td>\n\t";
				$choices_table .= "<td width=50% class=ad_cost_features_data_values>".$this->configuration_data['precurrency']." ".
					sprintf("%0.2f",$show_choices->AMOUNT)." ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
			}
			return $choices_table;
		}
		else
		{
			return false;
		}
	} //end of function show_subscription_choices_table

//#######################################################################################

	function show_credit_choices_table($db,$current_choice=0)
	{
		$this->sql_query = "select * from ".$this->credit_choices." where price_plan_id = ".$this->users_price_plan." order by value asc";
		$choices_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$choices_result)
		{
			return false;
		}
		elseif ($choices_result->RecordCount() > 0 )
		{
			while ($show_choices = $choices_result->FetchNextObject())
			{
				$choices_table .= "<tr>\n\t<td align=right width=50% class=place_an_ad_transaction_detail_feature_field>".$show_choices->DISPLAY_VALUE."
					<input type=radio name=c[credit_choice] value=".$show_choices->CREDIT_ID;
				if (($choices_result->RecordCount() == 1) || ($current_choice == $show_choices->CREDIT_ID))
					$choices_table .= " checked";
				$choices_table .= "></td>\n\t";
				$choices_table .= "<td width=50% class=place_an_ad_transaction_detail_feature_data>".$this->configuration_data['precurrency']." ".
					sprintf("%0.2f",$show_choices->AMOUNT)." ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
			}
			return $choices_table;
		}
		else
		{
			return false;
		}
	} //end of function show_credit_choices_table

//#######################################################################################

	function display_splash_page($db)
	{
		$this->sql_query = "select place_an_ad_splash_code from ".$this->groups_table." where group_id = ".$this->users_group;
		$splash_result = $db->Execute($this->sql_query);
		if ($this->debug_sell) echo $this->sql_query."<br>\n";
		if (!$splash_result)
		{
			return false;
		}
		elseif ($splash_result->RecordCount() ==1)
		{
			$show = $splash_result->FetchNextObject();
			if (strlen($show->PLACE_AN_AD_SPLASH_CODE) > 0)
			{
				//display the splash code

				$this->page_id = 8;
				$this->get_text($db);
				$this->body = "<table><tr><td>";
				$this->body .= stripslashes($show->PLACE_AN_AD_SPLASH_CODE);
				$this->body .= "</td></tr>";
				$this->body .= "<tr><td><a href=".$this->configuration_data['classifieds_file_name']."?a=1 class=continue_place_an_ad_link>".
					urldecode($this->messages[905])."</a></td></tr></table>";
				$this->update_check_splash($db);
				$this->display_page($db);
				exit;
			}
			else
			{
				//no splash code there-- move on
				$this->update_check_splash($db);
			}
		}
		else
			return true;
	} //end of function display_splash_page

//#######################################################################################

	function display_price_plan_choice_form($db)
	{
		if ($this->debug_sell)
		{
			echo "<BR>TOP OF DISPLAY_PRICE_PLAN_CHOICE_FORM<Br>\n";
		}
		$this->sql_query = "select * from ".$this->attached_price_plans." where group_id = ".$this->users_group." and price_plan_id > 0 and applies_to = ".$this->sell_type;
		$attached_result = $db->Execute($this->sql_query);
		if ($this->debug_sell) echo $this->sql_query."<br>\n";
		if (!$attached_result)
		{
			if ($this->debug_sell)
			{
				echo $db->ErrorMsg()."<bR>\n";
				echo $this->sql_query."<br>\n";
			}
			$this->setup_error = 1;
			return false;
		}
		elseif ($attached_result->RecordCount() > 0)
		{
			$this->check_user_subscription($db,1);

			if (!$this->users_price_plan)
			{
				//the user does not currently have a subscription...and the price plan has not been set
				$this->page_id = 8;
				$this->get_text($db);
				$this->body = "<table cellpadding=2 cellspacing=1 border=0 width=100%><tr class=place_ad_section_title><td colspan=2>".urldecode($this->messages[606])."</td></tr>";
				$this->body .= "<tr class=page_title><td colspan=2>".urldecode($this->messages[2464])."</td></tr>";
				$this->body .= "<tr class=page_description><td colspan=2>".urldecode($this->messages[2463])."<br><br></td></tr>";
				//display the price plan choices
				while ($show_price_plans = $attached_result->FetchNextObject())
				{
					$this->body .= "<tr><td class=plan_choice_links><a href=".$this->configuration_data['classifieds_file_name']."?a=1&price_plan=".$show_price_plans->PRICE_PLAN_ID." class=plan_choice_links>
						".$show_price_plans->NAME."<br><br></td>";
					$this->body .= "<td class=plan_description valign=top>".$show_price_plans->DESCRIPTION."</td></tr>";
				}
				$this->body .= "</table>";
				$this->display_page($db);
				exit;
			}
			else
			{
				//this user has a current subscription and the price plan attached to that subscription is now the current price plan.
			}
		}
		else
		{
			//no attached price plans set the default price plan
			$this->sql_query = "select * from ".$this->groups_table." where group_id = ".$this->users_group;
			$group_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query."<bR>\n";
			if (!$group_result)
			{
				if ($this->debug_sell)
				{
					echo $db->ErrorMsg()."<br>\n";
					echo  $this->sql_query."<bR>\n";
				}
				$this->setup_error = $this->messages[453];
				return false;
			}
			elseif ($group_result->RecordCount() == 1)
			{
				$show_group = $group_result->FetchNextObject();

				$use_this_price_plan = 0;

				if ($this->sell_type == 1)
				{
					//set a classified price plan
					if ($this->debug_sell) echo "setting a CLASSIFIED price plan<bR>\n";
					$use_this_price_plan = $show_group->PRICE_PLAN_ID;
				}
				elseif ($this->sell_type == 2)
				{
					//set an auction price plan
					if ($this->debug_sell) echo "setting a AUCTION price plan<bR>\n";
					$use_this_price_plan = $show_group->AUCTION_PRICE_PLAN_ID;
				}
				else
				{
					$this->setup_error = $this->messages[453];
					return false;
				}
				if ($use_this_price_plan)
				{
					$this->sql_query = "update ".$this->sell_table." set
						users_price_plan = ".$use_this_price_plan."
						where session = \"".$this->session_id."\"";
					$update_price_result = $db->Execute($this->sql_query);
					if ($this->debug_sell) echo $this->sql_query."<bR>\n";
					if (!$update_price_result)
					{
						if ($this->debug_sell)
						{
							echo $db->ErrorMsg()."<br>\n";
							echo  $this->sql_query." error<bR>\n";
						}
						$this->setup_error = $this->messages[453];
						return false;
					}
				}
				else
				{
					if ($this->debug_sell) echo "ERROR SETTING PRICE PLAN<bR>\n";
					$this->setup_error = $this->messages[453];
					return false;
				}
			}
			else
			{
				$this->setup_error = $this->messages[453];
				return false;
			}
		}
		return true;
	} //end of function display_price_plan_choice_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function set_price_plan($db,$price_plan=0)
	{
		if ($this->debug_sell)
		{
			echo "<BR>TOP OF SET_PRICE_PLAN<BR>\n";
		}
		if ($price_plan)
		{
			//check that price plan exists and attached to this group

			$this->sql_query = "select * from ".$this->attached_price_plans." where group_id = ".$this->users_group." and price_plan_id = ".$price_plan." and applies_to = ".$this->sell_type;
			$check_price_plan_result = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query."<bR>\n";
			if (!$check_price_plan_result)
			{
				if ($this->debug_sell)
				{
					echo $db->ErrorMsg()."<Br>\n";
					echo $this->sql_query."<bR>\n";
				}
				$this->setup_error = $this->messages[453];
				return false;
			}
			elseif ($check_price_plan_result->RecordCount() == 1)
			{
				$this->users_price_plan = $price_plan;
				$this->sql_query = "update ".$this->sell_table." set
					users_price_plan = ".$price_plan."
					where session = \"".$this->session_id."\"";
				$update_price_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<bR>\n";
				if (!$update_group_and_price_result)
				{
					if ($this->debug_sell)
					{
						echo $db->ErrorMsg()."<bR>\n";
						echo $this->sql_query."<bR>\n";
					}
					$this->setup_error = $this->messages[453];
					return false;
				}
				$this->check_maximum_ad_limit($db);
				return true;
			}
			else
			{
				if ($this->debug_sell) echo "displaying display_price_plan_choice_form from within set_price_plan 1<br>\n";
				$this->display_price_plan_choice_form($db);
			}
		}
		else
		{
			//echo "no price plan entered<br>\n";
			if ($this->debug_sell) echo "displaying display_price_plan_choice_form from within set_price_plan 2<br>\n";
			$this->display_price_plan_choice_form($db);
		}
	} //end of function set_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function sell_success_email($db,$classified_id=0)
	{
		if ($classified_id)
		{
			if (($this->configuration_data['send_successful_placement_email']) && (!$this->configuration_data['admin_approves_all_ads']))
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
					$message .= $this->configuration_data['classifieds_url']."?a=2&b=".$classified_id;

                    // echo $message."<br>";
                    // echo $subject."<br>";

					if ($this->configuration_data['email_configuration_type'] == 1)
						$message = str_replace("\n\n","\n",$message);

					if ($this->configuration_data['email_header_break'])
						$separator = "\n";
					else
						$separator = "\r\n";
					$from = "From: ".$this->configuration_data['site_email'].$separator."Reply-to: ".$this->configuration_data['site_email'].$separator;
                    // echo "From: $from<br>";
					$additional = "-f".$this->configuration_data['site_email'];
                    // echo "Aditional: $aditional<br>";
					if ($this->configuration_data['email_configuration'] == 1)
						mail($user_data->EMAIL,$subject,$message,$from,$additional);
					elseif ($this->configuration_data['email_configuration'] == 2)
						mail($user_data->EMAIL,$subject,$message,$from);
					else
						mail($user_data->EMAIL,$subject,$message);
				}
			}

			if ($this->configuration_data['send_admin_placement_email'])
			{
				//echo "email should be sent to ad for ad success!!<br>";
				$subject = "An Ad has been placed!!";
				$message = urldecode($this->messages[713])." Admin,\n";
				$message .= "An ad has been placed and is live on your site. See the link below for the ad details.\n\n";
				$message .= $this->configuration_data['classifieds_url']."?a=2&b=".$classified_id;
				if ($this->configuration_data['email_configuration_type'] == 1)
					$message = str_replace("\n\n","\n",$message);

				if ($this->configuration_data['email_header_break'])
					$separator = "\n";
				else
					$separator = "\r\n";
				$from = "From: ".$this->configuration_data['site_email'].$separator."Reply-to: ".$this->configuration_data['site_email'].$separator;

				$additional = "-f".$this->configuration_data['site_email'];

				if ($this->configuration_data['email_configuration'] == 1)
					mail($this->configuration_data['site_email'],$subject,$message,$from,$additional);
				elseif ($this->configuration_data['email_configuration'] == 2)
					mail($this->configuration_data['site_email'],$subject,$message,$from);
				else
					mail($this->configuration_data['site_email'],$subject,$message);
			}
			return true;
		}
		else
		{
			return false;
		}

	} //end of function sell_success_email

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function set_filter_id($db)
	{
		$user_data = $this->get_user_data($db,$this->classified_user_id);
		if (($this->configuration_data['use_filters']) && ($user_data->FILTER_ID) && (
			($this->configuration_data['optional_1_filter_association']) ||
			($this->configuration_data['optional_2_filter_association']) ||
			($this->configuration_data['optional_3_filter_association']) ||
			($this->configuration_data['optional_4_filter_association']) ||
			($this->configuration_data['optional_5_filter_association']) ||
			($this->configuration_data['optional_6_filter_association']) ||
			($this->configuration_data['optional_7_filter_association']) ||
			($this->configuration_data['optional_8_filter_association']) ||
			($this->configuration_data['optional_9_filter_association']) ||
			($this->configuration_data['optional_10_filter_association'])))
		{
			$this->filter_id = $user_data->FILTER_ID;
			$this->sql_query = "update ".$this->sell_table." set
				filter_id = ".$this->filter_id."
				where session=\"".$this->session_id."\"";
			if ($this->debug_sell)
				echo $this->sql_query."<bR>\n";
			$filter_id_result = $db->Execute($this->sql_query);
			if (!$filter_id_result)
			{
				return false;
			}
			return true;
		}
		else
		{
			//not using filters - leave filter_id at 0
			return true;
		}
	} //end of function set_filter_id

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_filter_value($db,$association=0)
	{
		if ($association)
		{
			//association is the filter level this value is associated with
			$this->sql_query = "select count(distinct(filter_level)) as level_count from ".$this->filters_table;
			$level_count_result = $db->Execute($this->sql_query);
			if ($this->debug_sell)
				echo $this->sql_query."<br>\n";
			if (!$level_count_result)
			{
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($level_count_result->RecordCount() == 1)
			{
				$level_count = $level_count_result->FetchNextObject();
				if ($level_count->LEVEL_COUNT == $association)
				{
					//get current filter id filter name
					$this->sql_query = "select ".$this->filters_languages_table.".filter_name
						from ".$this->filters_languages_table."
						where ".$this->filters_languages_table.".language_id = ".$this->language_id."
						and ".$this->filters_languages_table.".filter_id = ".$this->filter_id;
					$filter_result =  $db->Execute($this->sql_query);
					if ($this->debug_sell)
						echo $this->sql_query."<br>\n";
					if (!$filter_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[3501];
						return false;
					}
					elseif ($filter_result->RecordCount() == 1)
					{
						$show_filter_name = $filter_result->FetchNextObject();
						return $show_filter_name->FILTER_NAME;
					}
					else
						return false;

				}
				else
				{
					$filter_name = $this->get_filter_level($db,$this->filter_id,$association);
					return $filter_name;
				}
			}
			else
			{
				return false;
			}
		}
		else
			return false;
	}//end of function get_filter_value

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_filter_level($db,$filter=0,$level_result=0)
	{
		if ($filter)
		{
			$i = 0;
			$filter_next = $filter;
			do
			{
				$this->sql_query = "select ".$this->filters_table.".filter_id,".$this->filters_table.".parent_id,
					".$this->filters_languages_table.".filter_name, ".$this->filters_table.".filter_level
					from ".$this->filters_table.",".$this->filters_languages_table."
					where ".$this->filters_table.".filter_id = ".$this->filters_languages_table.".filter_id
					and ".$this->filters_languages_table.".language_id = ".$this->language_id."
					and ".$this->filters_table.".filter_id = ".$filter_next;
				$filter_result =  $db->Execute($this->sql_query);
				if ($this->debug_sell)
					echo $this->sql_query." is the query<br>\n";
				if (!$filter_result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[3501];
					return false;
				}
				elseif ($filter_result->RecordCount() == 1)
				{
					$show_filter = $filter_result->FetchNextObject();
					$this->filter_level_array[$i]["parent_id"]  = $show_filter->PARENT_ID;
					$this->filter_level_array[$i]["filter_name"] = $show_filter->FILTER_NAME;
					$this->filter_level_array[$i]["filter_id"]   = $show_filter->FILTER_ID;
					$this->filter_level_array[$i]["filter_level"]   = $show_filter->FILTER_LEVEL;
					if (($level_result) && ($level_result == $show_filter->FILTER_LEVEL))
						return $show_filter->FILTER_NAME;
					$i++;
					$filter_next = $show_filter->PARENT_ID;
				}
				else
				{
					//echo "wrong return<Br>\n";
					return false;
				}

			} while ( $show_filter->PARENT_ID != 0 );

			return $i;
		}
		else
			return false;

	} // end of function get_filter_level

//########################################################################

	function check_discount_code_use($db)
	{
		$this->sql_query = "select * from ".$this->discount_codes_table."
			where active = 1";
		$discount_check_result =  $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$discount_check_result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[3501];
			return false;
		}
		elseif ($discount_check_result->RecordCount() > 0)
		{
			$this->discount_codes = $discount_check_result;
			return true;
		}
		else
			return false;
	} //end of function check_discount_code_use

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function  admin_sell_success_email($db)
	{
		$this->page_id = 51;
		$this->get_text($db);
		//echo $this->configuration_data['user_set_hold_email']." is the setting for hold email<br>";

		if($this->configuration_data['user_set_hold_email'])
		{
			//echo $this->configuration_data['user_set_hold_email']." is the setting for hold email<br>";
			//echo "email should be sent for ad on hold<br>";
			$subject = "An Ad has been placed!!";
			$message = "Admin,\n\n";
			$message .= "An ad has been placed and is on hold because a manual payment type was chosen. See the unapproved ads section of the admin.\n\n";
			$message .= "Additional ads may be in the unapproved ads section that you were not sent an email. These will be failed auto pay attempts  or if you are approving all ads.\n\n";
			if ($this->configuration_data['email_configuration_type'] == 1)
				$message = str_replace("\n\n","\n",$message);

			if ($this->configuration_data['email_header_break'])
				$separator = "\n";
			else
				$separator = "\r\n";

			$from = "From: ".$this->configuration_data['site_email'].$separator."Reply-to: ".$this->configuration_data['site_email'].$separator;
			$additional = "-f".$this->configuration_data['site_email'];

			if ($this->configuration_data['email_configuration'] == 1)
				mail($this->configuration_data['site_email'],$subject,$message,$from,$additional);
			elseif ($this->configuration_data['email_configuration'] == 2)
				mail($this->configuration_data['site_email'],$subject,$message,$from);
			else
				mail($this->configuration_data['site_email'],$subject,$message);

			return true;
		}
	} //end of function admin_sell_success_email

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function remove_from_users_account($db,$discount_code_user_id=0)
	{
		if ($this->debug_sell) echo $this->amount_to_charge_balance." is amount_to_charge_balance in remove_from_users_account<br>";
		if ($this->amount_to_charge_balance > 0)
		{
			if ($discount_code_user_id)
				$user_id_removed_from = $discount_code_user_id;
			else
				$user_id_removed_from = $this->classified_user_id;
			if ($this->debug_sell) echo $user_id_removed_from." is user_id_removed_from in remove_from_users_account<br>";

			$user_data = $this->get_user_data($db,$user_id_removed_from);

			//update user account balance
			$this->sql_query = "update ".$this->userdata_table." set
				account_balance = ".($user_data->ACCOUNT_BALANCE - $this->amount_to_charge_balance)."
				where id = ".$user_id_removed_from;
			$update_balance_results = $db->Execute($this->sql_query);
			if ($this->debug_sell) echo $this->sql_query."<br>\n";
			if (!$update_balance_results)
			{
				return false;
			}
			else
			{
				//add to account balance transactions table
				$this->sql_query = "insert into ".$this->balance_transactions_items."
					(item_id,amount) values (".$this->classified_id.",".$this->amount_to_charge_balance.")";
				$insert_balance_subtraction_result = $db->Execute($this->sql_query);
			}
		}
		return true;

	} //end of function remove_from_users_account

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_invoice_cutoff($db)
	{
		 if (($this->configuration_data['invoice_cutoff']) &&
		 	((!$this->configuration_data['positive_balances_only']) && ($this->configuration_data['use_account_balance'])))
		 {
			$this->page_id=8;
			$this->get_text($db);
			$this->sql_query = "select * from ".$this->payment_types_table." where payment_choice_id = 7";
			if ($this->debug_sell) echo $this->sql_query."<br>";
			$site_balance_result = $db->Execute($this->sql_query);
			if (!$site_balance_result)
			{
				return false;
			}
			elseif ($site_balance_result->RecordCount() == 1)
			{
				$show_site_balance = $site_balance_result->FetchNextObject();
				if ($show_site_balance->ACCEPTED)
				{
					//this is an accepted payment choice
					//check for unpaid invoices
					//check to see if unpaid invoices are too old
					//get invoice cutoff date
					$invoice_cutoff_date = ($this->shifted_time($db) - ($this->configuration_data['invoice_cutoff'] * 86400));
					$this->sql_query = "select * from ".$this->invoices_table." where user_id = ".$this->classified_user_id." and
						date_paid = 0 and invoice_date < ".$invoice_cutoff_date;
					if ($this->debug_sell) echo $this->sql_query."<br>";
					$invoice_cutoff_result = $db->Execute($this->sql_query);
					if (!$invoice_cutoff_result)
					{
						return false;
					}
					elseif ($invoice_cutoff_result->RecordCount() > 0)
					{
						$this->setup_error = $this->messages[3103];
						return false;
					}
				}
			}
		 }
		 if((!$this->configuration_data['positive_balances_only']) && ($this->configuration_data['use_account_balance']))
		 {
		 	$this->sql_query = "select * from ".$this->payment_types_table." where payment_choice_id = 7";
			$site_balance_result = $db->Execute($this->sql_query);
			if(!$site_balance_result)
			{
				echo $this->sql_query.'<br>';
				return false;
			}
			else
			{
				$show_site_balance = $site_balance_result->FetchNextObject();
			}

			if($show_site_balance->ACCEPTED)
			{
				$this->sql_query = "select sum(amount) as to_be_invoiced from ".$this->balance_transactions."  where
					cc_transaction_id = 0 and invoice_id = 0 and user_id = ".$this->classified_user_id."
					and (auction_id != 0 or ad_id != 0 or subscription_renewal != 0)";
				$result = $db->Execute($this->sql_query);
				if(!$result)
				{
					return false;
				}
				else
				{
					$sum = $result->FetchNextObject();
					$to_be_invoiced = $sum->TO_BE_INVOICED;
				}

				// Check this amount versus the limit
				$this->sql_query = "select invoice_max from ".$this->user_groups_price_plans_table." as u, ".$this->classified_price_plans_table." as p where id = ".$this->classified_user_id." and u.price_plan_id = p.price_plan_id";
				//echo $this->sql_query.'<Br>';
				$result = $db->Execute($this->sql_query);
				if(!$result)
				{
					return false;
				}
				else
				{
					$invoice_max = $result->FetchNextObject();
				}

				// Check for database returning NULL
				if(!$to_be_invoiced)
				{
					$to_be_invoiced = 0.00;
				}
				if ($invoice_max->INVOICE_MAX > 0)
				{
					if(($to_be_invoiced >= $invoice_max->INVOICE_MAX) && $to_be_invoiced != 0)
					{
						$this->page_id = 8;
						$this->get_text($db);
						$this->setup_error = $this->messages[3269];
						if ($this->debug_sell) echo $this->setup_error." is the setup error within check_invoice_cutoff<BR>\n";
						return false;
					}
				}
			}
		 }
	} //end of function check_invoice_cutoff

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function set_sell_type($db, $type)
	{
		$this->sql_query = "update {$this->sell_table} set type = $type where session = '{$this->session_id}'";
		if ($this->debug_sell) echo $this->sql_query."<bR>\n";
		$this->sell_type = $type;
		$set_type_result = $db->Execute($this->sql_query) or die("<br>".__LINE__.mysql_error());
		if (!$set_type_result)
		{
			if ($this->debug_sell)
			{
				echo $db->ErrorMsg();
				echo $this->sql_query."<bR>\n";
			}
			return false;
		}

		// Set the classified_variable value
		$this->classified_variables['sell_type'] = $type;

		//check/set final_fee switch if this is an auction type
		//if ($type == 2)
		//{
		//	$this->check_final_fee($db);
		//}
		return true;

	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function choose_sell_type($db)
	{
		if ($this->debug_sell)
		{
			echo "<br>TOP OF CHOOSE_SELL_TYPE<BR>\n";
		}
		$this->page_id = 199;
		$this->get_text($db);
		// ***

		$this->body .=  "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .=  "<tr class=section_title>\n\t<td>\n\t".urldecode($this->messages[200011]);
		$this->body .=  "\n\t</td>\n</tr>\n";
		$this->body .=  "<tr class=page_title>\n\t<td>\n\t".urldecode($this->messages[200012]);
		$this->body .=  "\n\t</td>\n</tr>\n";
		$this->body .=  "<tr class=page_description>\n\t<td>\n\t".urldecode($this->messages[200013])."\n\t</td>\n</tr>\n";

		$this->body .=  "<tr class=classified_link>\n\t<td>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=1&set_type=1 class=classified_link>".urldecode($this->messages[200014])."</a>\n\t</td>\n</tr>\n";
		$this->body .=  "<tr class=auction_link>\n\t<td>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=1&set_type=2 class=auction_link>".urldecode($this->messages[200015])."</a>\n\t</td>\n</tr>\n";

		$this->body .=  "<tr class=end_sell_process_link>\n\t<td>\n\t<a href=".trim($this->configuration_data['classifieds_url'])."?a=98 class=end_sell_process_link>".urldecode($this->messages[200016])."</a>\n\t</td>\n</tr>\n";
		$this->body .=  "</table>\n";
		$this->display_page($db);
		return true;
		if ($this->debug_sell)
		{
			echo "BOTTOM OF CHOOSE_SELL_TYPE<BR>\n";
		}
/*
		$this->body = urldecode($this->messages[103380])."<br>
			<a href=".$this->configuration_data['classifieds_file_name']."?a=1&set_type=1>".urldecode($this->messages[103381])."</a>&nbsp;&nbsp;&nbsp;
			<a href=".$this->configuration_data['classifieds_file_name']."?a=1&set_type=2>".urldecode($this->messages[103382])."</a>";

		$this->display_page($db);

		return true;
*/
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_date_select($year_name,$month_name,$day_name,$hour_name,$minute_name,
		$timestamp = 0,$year_value=0,$month_value=0,$day_value=0,$hour_value=0,$minute_value=0)
	{
		if($timestamp == 0){
			$time = $this->shifted_time($db);
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
		}else{
			$year_value = date("Y",$timestamp);
			$month_value = date("n",$timestamp);
			$day_value = date("j",$timestamp);
			$hour_value = date("G",$timestamp);
			$minute_value = date("i",$timestamp);
		}

		$this->body .= "<font class=place_an_ad_details_data>".urldecode($this->messages[103058])." <select name=".$day_name.">\n\t\t";
		for ($i=1;$i<32;$i++)
		{
			$this->body .="<option";
			if ($day_value == $i)
				$this->body .= " selected";
			$this->body .=">".$i."</option>\n\t\t";
		}
		$this->body .="</select>\n\t\t";

		$this->body .= urldecode($this->messages[103059])." <select name=".$month_name.">\n\t\t";
		for ($i=1;$i<13;$i++)
		{
			$this->body .="<option";
			if ($month_value == $i)
				$this->body .= " selected";
			$this->body .=">".$i."</option>\n\t\t";
		}
		$this->body .="</select>\n\t\t";

		$this->body .= urldecode($this->messages[103060])." <select name=".$year_name.">\n\t\t";
		for ($i=2004;$i<=($year_value+2);$i++)
		{
			$this->body .="<option";
			if ($year_value == $i)
				$this->body .= " selected";
			$this->body .=">".$i."</option>\n\t\t";
		}
		$this->body .="</select>\n\t\t";

		$this->body .= urldecode($this->messages[103061])." <select name=".$hour_name.">\n\t\t";
		for ($i=0;$i<=23;$i++)
		{
			$this->body .="<option";
			if ($hour_value == $i)
				$this->body .= " selected";
			$this->body .=">".sprintf("%02d",$i)."</option>\n\t\t";
		}
		$this->body .="</select>\n\t\t";

		$this->body .= urldecode($this->messages[103062])." <select name=".$minute_name.">\n\t\t";
		for ($i=0;$i<=59;$i++)
		{
			$this->body .="<option";
			if ($minute_value == $i)
				$this->body .= " selected";
			$this->body .=">".sprintf("%02d",$i)."</option>\n\t\t";
		}
		$this->body .="</select>\n\t\t</font>";

	} //end of function get_fine_date_select

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_final_fee($db)
	{
		if ($this->debug_sell)
		{
			echo "<br>TOP OF CHECK_FINAL_FEE<BR>\n";
			echo $this->users_price_plan." is users_price_plan<BR>\n";
		}
		$this->function_name = "check_final_fee";

		//check to see if there is a final fee to this auction
		$this->price_plan = $this->get_price_plan($db,$this->users_price_plan);
		$this->final_fee = $this->price_plan->CHARGE_PERCENTAGE_AT_AUCTION_END;

		if ($this->debug_sell)
		{
			echo $this->price_plan." is price_plan<BR>\n";
			echo $this->price_plan->CHARGE_PERCENTAGE_AT_AUCTION_END." is CHARGE_PERCENTAGE_AT_AUCTION_END<Br>\n";
		}

		//save final fee settings
		$this->sql_query = "update ".$this->sell_table." set
			final_fee = ".$this->final_fee."
			where session=\"".$this->session_id."\"";
		$auction_final_fee_result = $db->Execute($this->sql_query);
		if ($this->debug_sell) echo $this->sql_query."<br>\n";
		if($this->configuration_data->DEBUG_SELL)
		{
			$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "sell_table", "update final fee in sell table by session id");
		}
		if (!$auction_final_fee_result)
		{
			if ($this->debug_sell)
			{
				echo $db->ErrorMsg()."<br>\n";
				echo $this->sql_query."<br>\n";
			}
			return false;
		}
		if ($this->debug_sell) echo "<br>BOTTOM OF CHECK_FINAL_FEE<BR><BR>\n";
		return true;

	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function close_final_fee_transactions($db,$transaction_number=0)
	{
		if ($transaction_number)
		{
			$this->price_plan = $this->get_this_price_plan($db);
			if (($this->price_plan->CHARGE_PERCENTAGE_AT_AUCTION_END)  && ($this->price_plan->ROLL_FINAL_FEE_INTO_FUTURE))
			{
				$this->sql_query = "update ".$this->classifieds_table." set
					final_fee_transaction_number = \"".$transaction_number."\"
					where final_fee = 1 and final_fee_transaction_number = 0 and ends < ".time()." and seller = ".$this->classified_user_id;
				$final_fee_result = $db->Execute($this->sql_query);
				if ($this->debug_sell) echo $this->sql_query."<br>\n";
				if (!$final_fee_result)
				{
					if ($this->debug_sell) echo $this->sql_query."<br>\n";
					$this->site_error($db->ErrorMsg());
					return false;
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	} //end of function close_final_fee_transactions

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function redirect($query_string)
	{
		header("Location: ".$this->configuration_data['classifieds_ssl_url'].$query_string);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Classified_sell
?>