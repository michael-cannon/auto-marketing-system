<? //browse_display_ad.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Display_ad extends Site {
	var $subcategory_array = array();
	var $notify_data = array();

	var $ad_id;

	var $debug_ad_display = 0;

//########################################################################

	function Display_ad($db, $classified_user_id, $language_id, $category_id=0, $page=0, $classified_id=0, $group_id=0, $product_configuration=0)
	{
		if ($limit)
			$this->browse_limit = $limit;
		$this->Site($db,1,$language_id,$classified_user_id, $product_configuration);
		if ($category_id)
			$this->site_category = $category_id;
		elseif ($classified_id)
		{
			$show = $this->get_classified_data($db,$classified_id);
			$this->site_category = $show->CATEGORY;
		}
		else
			$this->site_category = 0;
		$this->get_ad_configuration($db);
		if ($page)
			$this->page_result = $page;
		else
			$this->page_result = 1;

		$this->ad_id = $classified_id;
	} //end of function Display_ad

//###########################################################

	function display_classified($db,$id=0)
	{
		if ($this->debug_ad_display)
		{
			echo $id." is id<bR>\n";
			echo $this->classified_user_id." is classified_user_id<bR>\n";
			echo $this->configuration_data['subscription_to_view_or_bid_ads']." is SUBSCRIPTION_TO_VIEW_OR_BID_ADS<Br>\n";
		}
		$this->page_id = 1;
		$this->get_text($db);
		if ($id)
		{
			if($this->configuration_data['subscription_to_view_or_bid_ads'] && !$this->classified_user_id)
			{
				include_once("authenticate_class.php");
				$auth = new Auth($db,$language_id);
  				$auth->login_form($db, "", "", "a*is*2&b*is*".$id, 3);
				return true;
			}

			//this is beta code that will only be used if set to be used within the config.php file
			if (!defined('MUST_HAVE_SUBSCRIPTION_TO_VIEW_AD_DETAIL'))
				$must_have_subscription_to_view_ad_detail = 0;
			else
				$must_have_subscription_to_view_ad_detail = MUST_HAVE_SUBSCRIPTION_TO_VIEW_AD_DETAIL;
			if ($must_have_subscription_to_view_ad_detail)
			{
				if (!$this->check_user_subscription($db))
				{
					include_once("authenticate_class.php");
					$auth = new Auth($db,$language_id);
	  				$auth->login_form($db, "", "", "a*is*2&b*is*".$id, 3);
					return true;
				}
			}

			$show = $this->get_classified_data($db,$id);
			if ($this->debug_ad_display)
			{
				echo $show->TITLE." is the title<BR>\n";
				echo $show->LIVE." is live<bR>\n";
				echo $show->ENDS." is when ad ends and this is current time: ".time()."<br>\n";
			}
			if ($show)
			{
				if ((($show->LIVE == 1) && ($show->ITEM_TYPE ==1)) || ($show->ITEM_TYPE == 2))
				{
					//get the next ad id within this category
					if ($this->configuration_data['display_sub_category_ads'])
					{
						$this->get_sql_in_statement($db,$show->CATEGORY);
					}
					else
					{
						$this->in_statement = " in (".$show->CATEGORY.") ";
					}
					$this->sql_query = "select id from ".$this->classifieds_table." where
						category ".$this->in_statement." and live = 1 order by better_placement desc,date desc";
					if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
					$next_id_result = $db->Execute($this->sql_query);
					if (!$next_id_result)
					{
						if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
						$this->error_message = stripslashes(urldecode($this->messages[81]));
						return false;
					}
					elseif ($next_id_result->RecordCount() > 1)
					{
						while ($next_id = $next_id_result->FetchNextObject())
						{
							if ($next_id->ID == $id)
							{
								$next_id = $next_id_result->FetchNextObject();
								$next_classified_id = $next_id->ID;
								break;
							}
							$last_classified_id = $next_id->ID;
						}
					}
					if ($this->debug_ad_display)
					{
						echo $last_classified_id." is last<Br>\n";
						echo $next_classified_id." is next<br>\n";
					}

					if ($this->get_category_configuration($db,$this->site_category,0))
					{
						if (!$this->category_configuration->USE_SITE_DEFAULT)
						{
							$this->browsing_configuration = $this->ad_configuration_data;
							$this->browsing_configuration->USE_OPTIONAL_FIELD_1 = $this->configuration_data['use_optional_field_1'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_2 = $this->configuration_data['use_optional_field_2'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_3 = $this->configuration_data['use_optional_field_3'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_4 = $this->configuration_data['use_optional_field_4'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_5 = $this->configuration_data['use_optional_field_5'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_6 = $this->configuration_data['use_optional_field_6'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_7 = $this->configuration_data['use_optional_field_7'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_8 = $this->configuration_data['use_optional_field_8'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_9 = $this->configuration_data['use_optional_field_9'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_10 = $this->configuration_data['use_optional_field_10'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_11 = $this->configuration_data['use_optional_field_11'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_12 = $this->configuration_data['use_optional_field_12'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_13 = $this->configuration_data['use_optional_field_13'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_14 = $this->configuration_data['use_optional_field_14'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_15 = $this->configuration_data['use_optional_field_15'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_16 = $this->configuration_data['use_optional_field_16'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_17 = $this->configuration_data['use_optional_field_17'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_18 = $this->configuration_data['use_optional_field_18'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_19 = $this->configuration_data['use_optional_field_19'];
							$this->browsing_configuration->USE_OPTIONAL_FIELD_20 = $this->configuration_data['use_optional_field_20'];

						}
						else
						{
							$this->browsing_configuration = $this->category_configuration;
						}
					}
					else
					{
						//echo "no category<br>\n";
						$this->browsing_configuration = $this->ad_configuration_data;
					}



					$category_tree = $this->get_category_tree($db,$show->CATEGORY);
					reset ($this->category_tree_array);

					if ($category_tree)
					{
						//category tree
						$category_tree_fields = "".stripslashes(urldecode($this->messages[2]))." <a href=".$this->configuration_data['classifieds_url']."?a=5 class=display_ad_category_tree>".stripslashes(urldecode($this->messages[5]))."</a> > ";
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
									$category_tree_fields .= "<a href=".$this->configuration_data['classifieds_url']."?a=5&b=".$this->category_tree_array[$i]["category_id"]."&page=".$this->page_result." class=display_ad_category_tree>".$this->category_tree_array[$i]["category_name"]."</a>";
								else
									$category_tree_fields .= "<a href=".$this->configuration_data['classifieds_url']."?a=5&b=".$this->category_tree_array[$i]["category_id"]." class=display_ad_category_tree>".$this->category_tree_array[$i]["category_name"]."</a> > ";
							}
						}
						else
						{
							$category_tree_fields = $category_tree;
						}
					}

					$template_category = $show->CATEGORY;
					do {
						//get category specific template (if any)
						$this->sql_query = "select ".$this->categories_table.".parent_id, ";
						if($show->ITEM_TYPE==1)
							$this->sql_query .=	$this->categories_languages_table.".ad_detail_display_template_id ";
						if($show->ITEM_TYPE==2)
							$this->sql_query .=	$this->categories_languages_table.".auction_detail_display_template_id ";
						$this->sql_query .=	"from ".$this->categories_table.", ".$this->categories_languages_table." where
							".$this->categories_languages_table.".category_id = ".$template_category." and
							".$this->categories_table.".category_id = ".$template_category." and ".$this->categories_languages_table.".language_id = ".$this->language_id;

						if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
						$detail_template_result = $db->Execute($this->sql_query);
						if (!$detail_template_result)
						{
							if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
							$this->error_message = stripslashes(urldecode($this->messages[81]));
							return false;
						}
						elseif ($detail_template_result->RecordCount() == 1)
						{
							$show_detail_template = $detail_template_result->FetchNextObject();
							if($show->ITEM_TYPE==1)
								$template_id = $show_detail_template->AD_DETAIL_DISPLAY_TEMPLATE_ID;
							if($show->ITEM_TYPE==2)
								$template_id = $show_detail_template->AUCTION_DETAIL_DISPLAY_TEMPLATE_ID;
							$template_category = $show_detail_template->PARENT_ID;
						}
						else
						{
							if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
							return false;
						}
					} while (($template_category) && (!$template_id));

					if (!$template_id)
					{
						if($show->ITEM_TYPE==1)
                        //CHANGED ON 04.04.2006 BCS-IT
							$template_id = $this->ad_configuration_data->USER_AD_TEMPLATE;
						if($show->ITEM_TYPE==2)
							$template_id = $this->ad_configuration_data->AUCTIONS_USER_AD_TEMPLATE;
					}

					//get template
					$this->sql_query = "select * from ".$this->templates_table." where template_id = ".$template_id;
					$template_result = $db->Execute($this->sql_query);
					if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
					if (!$template_result)
					{
						if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
						echo "template failed 4<br>";
						return false;
					}
					elseif ($template_result->RecordCount() ==1)
					{
						$show_template = $template_result->FetchNextObject();
						$template = stripslashes($show_template->TEMPLATE_CODE);
					}
					else
					{
						if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
						return false;
					}


					if (($show->LIVE) && ($this->classified_user_id))
					{
						//set single $can_bid variable here instead of checking invited and blacklist several times in function
						if ($this->debug_ad_display)
						{
							echo $this->configuration_data['black_list_of_buyers']." is to check blacklist<br>\n";
							echo $this->configuration_data['invited_list_of_buyers']." is to check invited<br>\n";
						}

						if ($this->configuration_data['invited_list_of_buyers'])
						{
							//check invited only
							if ($this->check_invitedlist($db,$show->SELLER,$this->classified_user_id))
							{
								if ($this->debug_ad_display)
									echo "this buyer is on the invited list 2<br>";
								//this user is on the invited list
								$can_bid = 1;
							}
							else
							{
								if ($this->debug_ad_display)
									echo "not on the invited list so cannot bid<BR>\n";
								$error_msg = stripslashes(urldecode($this->messages[102862]));
								$can_bid = 0;
							}
						}
						elseif ($this->configuration_data['black_list_of_buyers'])
						{
							//check black list only
							if ($this->debug_ad_display)
								echo "checking only black list of buyers<BR>\n";
							if ($this->check_blacklist($db,$show->SELLER,$this->classified_user_id))
							{
								if ($this->debug_ad_display)
									echo "this buyer is on the blacklist 2<br>";
								$error_msg = stripslashes(urldecode($this->messages[102861]));
								$can_bid = 0;
							}
							else
							{
								if ($this->debug_ad_display)
									echo "not on the blacklist so can bid<BR>\n";
								$can_bid = 1;
							}
						}
						else
						{
							//there are no restrictions on
							if ($this->debug_ad_display)
								echo "there are no lists to check<br>";

							if(!$show->BUY_NOW_ONLY)
							{
								$can_bid = 1;
							}
							else
							{
								$can_bid = 0;
							}
						}
						if (($show->LIVE) && ($this->classified_user_id))
						{
							//set single $can_bid variable here instead of checking invited and blacklist several times in function
							if ($this->debug_display_auction)
							{
								echo $this->configuration_data['black_list_of_buyers']." is to check blacklist<br>\n";
								echo $this->configuration_data['invited_list_of_buyers']." is to check invited<br>\n";
							}
							if($this->configuration_data['black_list_of_buyers'] && $this->configuration_data['invited_list_of_buyers'])
							{
								//check blacklist and invited list
								$invited = $this->check_invitedlist($db,$show->SELLER,$this->classified_user_id);
								$banned = $this->check_blacklist($db,$show->SELLER,$this->classified_user_id);
								if ($this->debug_display_auction)
								{
									echo $banned." is banned<bR>\n";
									echo $invited." is invited<bR>\n";
								}
								if ($invited == 1)
								{
									if ($this->debug_display_auction)
										echo "this buyer is on the invited list<br>";
									//this user is on the invited list
									$can_bid = 1;
								}
								else
								{
									if ($this->debug_display_auction)
										echo "this user is not on the invitedlist<bR>";
									if ($banned)
									{
										if ($this->debug_display_auction)
											echo "this buyer is on the blacklist<br>";
										$error_msg = stripslashes(urldecode($this->messages[102861]));
										$can_bid = 0;
									}
									else
									{
										//this user is not on the invited or
										if ($this->debug_display_auction)
										{
											echo "this user is not on the blacklist<bR>\n";
											echo "this buyer can bid -1<br>\n";
										}
										if ($invited == 2)
											$can_bid = 1;
									}
								}
							}
							elseif ($this->configuration_data['invited_list_of_buyers'])
							{
								//check invited only
								if ($this->check_invitedlist($db,$show->SELLER,$this->classified_user_id))
								{
									if ($this->debug_display_auction)
										echo "this buyer is on the invited list 2<br>";
									//this user is on the invited list
									$can_bid = 1;
								}
								else
								{
									if ($this->debug_ad_display)
										echo "not on the invited list so cannot bid<BR>\n";
									$error_msg = stripslashes(urldecode($this->messages[102862]));
									$can_bid = 0;
								}
							}
							elseif ($this->configuration_data['black_list_of_buyers'])
							{
								//check black list only
								if ($this->debug_display_auction)
									echo "checking only black list of buyers<BR>\n";
								if ($this->check_blacklist($db,$show->SELLER,$this->classified_user_id))
								{
									if ($this->debug_display_auction)
										echo "this buyer is on the blacklist 2<br>";
									$error_msg = stripslashes(urldecode($this->messages[102861]));
									$can_bid = 0;
								}
								else
								{
									if ($this->debug_display_auction)
										echo "not on the blacklist so can bid<BR>\n";
									$can_bid = 1;
								}
							}
							else
							{
								//there are no restrictions on
								if ($this->debug_display_auction)
									echo "there are no lists to check<br>";

								if(!$show->BUY_NOW_ONLY)
								{
									$can_bid = 1;
								}
								else
								{
									$can_bid = 0;
								}
							}
						}
					}
					else
					{
						if ($this->debug_ad_display)
						{
							echo "false for checking blacklist and invited list<br>";
							echo "---".$show->LIVE." is live<BR>\n";
							echo "---".$this->classified_user_id." is classified_user_id<BR>\n";
						}
						$can_bid = 0;
					}
					$end_date = date(trim($this->configuration_data['entry_date_configuration']), $show->ENDS);
					$start_date = date(trim($this->configuration_data['entry_date_configuration']), $show->DATE);
					$seller_data = $this->get_user_data($db,$show->SELLER);

					if (($seller_data) && ($show->SELLER))
					{
						$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$show->SELLER;
						$seller_group_result = $db->Execute($this->sql_query);
						if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
						if (!$seller_group_result)
						{
							if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
							return false;
						}
						elseif ($seller_group_result->RecordCount() == 1)
						{
							$show_group = $seller_group_result->FetchNextObject();
							$this->sql_query = "select sponsored_by_code from ".$this->groups_table." where group_id = ".$show_group->GROUP_ID;
							$sponsored_by_result = $db->Execute($this->sql_query);
							if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
							if (!$sponsored_by_result)
							{
								if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
								return false;
							}
							elseif ($sponsored_by_result->RecordCount() ==1)
							{
								$sponsored_by_html = $sponsored_by_result->FetchNextObject();
								if (strlen(trim($sponsored_by_html->SPONSORED_BY_CODE)) > 0)
									$template = str_replace("<<SPONSORED_BY>>",stripslashes($sponsored_by_html->SPONSORED_BY_CODE),$template);
								else
									$template = str_replace("<<SPONSORED_BY>>","",$template);
							}
						}
						else
						{
							if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
							return false;
						}
					}
					else
					{
						$template = str_replace("<<SPONSORED_BY>>","",$template);
					}

					$image_block = $this->display_ad_images($db,$id);


    					if (($this->ad_configuration_data->LEAD_PICTURE_WIDTH) && ($this->ad_configuration_data->LEAD_PICTURE_HEIGHT))
					{
						if ($this->debug_ad_display) echo "<br>TOP OF SECTION TO PLACE LEAD IMAGE TAG<br>\n";
						if ($this->images_to_display[1]["type"])
						{
							//an image is within the first (lead) image slot
							if ($this->debug_ad_display)
							{
								echo $this->ad_configuration_data->LEAD_PICTURE_WIDTH." is the lead image width - LEAD_PICTURE_WIDTH<bR>\n";
								echo $this->ad_configuration_data->LEAD_PICTURE_HEIGHT." is the lead image height - LEAD_PICTURE_HEIGHT<bR>\n";
								echo $this->images_to_display[1]["type"]." is the lead image type[type]<br>\n";
								echo $this->images_to_display[1]["thumb_url"]." is the thumb url[thumb_url]<Br>\n";
								echo $this->images_to_display[1]["url"]." is the url[url]<Br>\n";
								echo $this->images_to_display[1]["original_image_width"]." is original_image_width[original_image_width]<br>\n";
								echo $this->images_to_display[1]["original_image_height"]." is original_image_height[original_image_height]<br>\n";
								echo $this->images_to_display[1]["image_width"]." is image_width[image_width]<br>\n";
								echo $this->images_to_display[1]["image_height"]." is image_height[image_height]<br>\n";
							}
							/*if ($this->configuration_data['image_link_destination_type'])
								$lead_image_tag = "<a href=".$this->configuration_data['classifieds_url']."?a=15&b=".$id." class=full_images_link>";
							else
							{
								if ($this->debug_ad_display)
								{
									echo $this->images_to_display[1]["id"]." is image id<br>\n";
								}
								if ($this->ad_configuration_data->POPUP_IMAGE_TEMPLATE_ID)
								{
									if ($this->images_to_display[1]["type"] == 2)
										$lead_image_tag = "<a href=\"javascript:winimage('get_image.php?popupimage=".$this->images_to_display[1]["id"]."','".($this->images_to_display[1]["image_width"] + $this->ad_configuration_data->POPUP_IMAGE_EXTRA_WIDTH)."','".($this->images_to_display[1]["image_height"] + $this->ad_configuration_data->POPUP_IMAGE_EXTRA_HEIGHT)."')\" class=full_images_link>";
									else
										$lead_image_tag = "<a href=\"javascript:winimage('".$this->images_to_display[1]["url"]."','".($this->images_to_display[1]["image_width"] + $this->ad_configuration_data->POPUP_IMAGE_EXTRA_WIDTH)."','".($this->images_to_display[1]["image_height"] + $this->ad_configuration_data->POPUP_IMAGE_EXTRA_HEIGHT)."')\" class=full_images_link>";
								}
								else
								{
									if ($this->images_to_display[1]["type"] == 2)
										$lead_image_tag = "<a href=\"javascript:winimage('get_image.php?popupimage=".$this->images_to_display[1]["id"]."','".($this->images_to_display[1]["image_width"] + 40)."','".($this->images_to_display[1]["image_height"] + 40)."')\" class=full_images_link>";
									else
										$lead_image_tag = "<a href=\"javascript:winimage('".$this->images_to_display[1]["url"]."','".($this->images_to_display[1]["image_width"]+40)."','".($this->images_to_display[1]["image_height"]+40)."')\" class=full_images_link>";
								}
							}	*/
							if ($this->debug_ad_display) echo $lead_image_tag." is the image tag at the top----------------------------<bR>\n";
							if ($this->images_to_display[1]["icon"] > 0)
							{
								if ($this->debug_ad_display) echo " displaying the icon for the lead image<bR>\n";
								//$lead_image_tag = "<a href=\"".$this->images_to_display[1]["url"]."\">";
								$lead_image_tag .=  "<img src=\"".$this->images_to_display[1]["icon"]."\" border=0>";
								//$lead_image_tag .= "</a>";
							}
							else
							{
								if ((strlen($this->images_to_display[1]["thumb_url"]) > 0) && ($this->images_to_display[1]["thumb_url"] != "0"))
								{
									if ($this->debug_ad_display) echo "there is a thumbnail<BR>\n";
									//check the width and height of the original image
									if (($this->images_to_display[1]["original_image_width"] < $this->ad_configuration_data->LEAD_PICTURE_WIDTH) &&
										($this->images_to_display[1]["original_image_height"] < $this->ad_configuration_data->LEAD_PICTURE_HEIGHT))
									{
										//use the original image to display as the lead image
										if ($this->debug_ad_display) echo "using the original image (there is a thumbnail) because the original dimensions are smaller than the max dimensions for the lead image<bR>\n ";

										$lead_image_width = $this->images_to_display[1]["original_image_width"];
										$lead_image_height = $this->images_to_display[1]["original_image_height"];
										$lead_image_tag .=  "<img src=".$this->images_to_display[1]["url"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";
									}
									elseif (($this->images_to_display[1]["image_width"] < $this->ad_configuration_data->LEAD_PICTURE_WIDTH) &&
										($this->images_to_display[1]["image_height"] < $this->ad_configuration_data->LEAD_PICTURE_HEIGHT))
									{
										//use the thumbnail to display as the lead image
										if ($this->debug_ad_display) echo "using the thumbnail image because the original dimensions are smaller than the max dimensions for the lead image<bR>\n ";

										$lead_image_width = $this->images_to_display[1]["image_width"];
										$lead_image_height = $this->images_to_display[1]["image_height"];

										//resize up the lead image
										$width_imageprop = ($this->ad_configuration_data->LEAD_PICTURE_WIDTH * 100) / $this->images_to_display[1]["image_width"];
										$height_imageprop = ($this->ad_configuration_data->LEAD_PICTURE_HEIGHT * 100) / $this->images_to_display[1]["image_height"];
										if ($width_imageprop >= $height_imageprop)
										{
											//width relationship is greater that height
											$imageprop = $height_imageprop;
											$lead_image_height = $this->ad_configuration_data->LEAD_PICTURE_HEIGHT;
											$imagevsize = ($this->images_to_display[1]["image_width"] * $imageprop) / 100 ;
											$lead_image_width = ceil($imagevsize);

										}
										else
										{
											//height relationship is greater that width
											$imageprop = $width_imageprop;
											$lead_image_width = $this->ad_configuration_data->LEAD_PICTURE_WIDTH;
											$imagehsize = ($this->images_to_display[1]["image_height"] * $imageprop) / 100 ;
											$lead_image_height = ceil($imagehsize);
										}
										if ($this->images_to_display[1]["type"] == 2)
											$lead_image_tag .=  "<img src=get_image.php?image=".$this->images_to_display[1]["id"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";
										else
											$lead_image_tag .=  "<img src=".$this->images_to_display[1]["thumb_url"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";
									}
									else
									{
										//resize the thumbnail
										if ($this->debug_ad_display) echo "using the thumbnail image but must check to resize it<bR>\n ";
										if (($this->images_to_display[1]["image_width"] > $this->ad_configuration_data->LEAD_PICTURE_WIDTH) && ($this->images_to_display[1]["image_height"] > $this->ad_configuration_data->LEAD_PICTURE_HEIGHT))
										{
											if ($this->debug_ad_display) echo "width and height of thumbnail larger than lead pic dimensions<br>\n";
											$imageprop = ($this->ad_configuration_data->LEAD_PICTURE_WIDTH * 100) / $this->images_to_display[1]["image_width"];
											$imagevsize = ($this->images_to_display[1]["image_height"] * $imageprop) / 100 ;
											$lead_image_width = $this->ad_configuration_data->LEAD_PICTURE_WIDTH;
											$lead_image_height = ceil($imagevsize);

											if ($lead_image_height > $this->ad_configuration_data->LEAD_PICTURE_HEIGHT)
											{
												$imageprop = ($this->ad_configuration_data->LEAD_PICTURE_HEIGHT * 100) / $this->images_to_display[1]["image_height"];
												$imagehsize = ($this->images_to_display[1]["image_width"] * $imageprop) / 100 ;
												$lead_image_height = $this->ad_configuration_data->LEAD_PICTURE_HEIGHT;
												$lead_image_width = ceil($imagehsize);
											}
										}
										elseif ($this->images_to_display[1]["image_width"] > $this->ad_configuration_data->LEAD_PICTURE_WIDTH)
										{
											if ($this->debug_ad_display) echo "width of thumbnail larger than lead pic dimensions<br>\n";
											$imageprop = ($this->ad_configuration_data->LEAD_PICTURE_WIDTH * 100) / $this->images_to_display[1]["image_width"];
											$imagevsize = ($this->images_to_display[1]["image_height"] * $imageprop) / 100 ;
											$lead_image_width = $this->ad_configuration_data->LEAD_PICTURE_WIDTH;
											$lead_image_height = ceil($imagevsize);
										}
										elseif ($this->images_to_display[1]["image_height"] > $this->ad_configuration_data->LEAD_PICTURE_HEIGHT)
										{
											if ($this->debug_ad_display) echo "height of thumbnail larger than lead pic dimensions<br>\n";
											$imageprop = ($this->ad_configuration_data->LEAD_PICTURE_HEIGHT * 100) / $this->images_to_display[1]["image_height"];
											$imagehsize = ($this->images_to_display[1]["image_width"] * $imageprop) / 100 ;
											$lead_image_height = $this->ad_configuration_data->LEAD_PICTURE_HEIGHT;
											$lead_image_width = ceil($imagehsize);
										}
										else
										{
											$lead_image_width = $this->images_to_display[1]["image_width"];
											$lead_image_height = $this->images_to_display[1]["image_height"];
										}
										if ($this->debug_ad_display)
										{
											echo "using the thumbnail image and has been resized to:<bR>\n ";
											echo "&nbsp;&nbsp;&nbsp;&nbsp;".$lead_image_width." is the resized thumbnail width<Br>";
											echo "&nbsp;&nbsp;&nbsp;&nbsp;".$lead_image_height." is the resized thumbnail height<Br>";
										}
										if ($this->images_to_display[1]["type"] == 2)
											$lead_image_tag .=  "<img src=get_image.php?image=".$this->images_to_display[1]["id"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";
										else
											$lead_image_tag .=  "<img src=".$this->images_to_display[1]["thumb_url"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";

									}
								}
								elseif ((strlen($this->images_to_display[1]["url"]) > 0) && ($this->images_to_display[1]["url"] != "0"))
								{
									//no thumbnail to display use the original image
									if ($this->debug_ad_display) echo "no thumbnail so must use the original image<br>\n";
									if (($this->images_to_display[1]["original_image_width"] < $this->ad_configuration_data->LEAD_PICTURE_WIDTH) &&
										($this->images_to_display[1]["original_image_height"] < $this->ad_configuration_data->LEAD_PICTURE_HEIGHT))
									{
										//use the original image as is for width and height
										if ($this->debug_ad_display) echo "width and height of thumbnail smaller than lead pic dimensions<br>\n";
										$lead_image_height = $this->images_to_display[1]["original_image_height"];
										$lead_image_width = $this->images_to_display[1]["original_image_width"];

										if ($this->debug_ad_display)
										{
											echo $lead_image_width." is the lead_image_width for the original image where original width is: ".$this->images_to_display[1]["original_image_width"]."<bR>\n";
											echo $lead_image_height." is the lead_image_height for the original image where original height is: ".$this->images_to_display[1]["original_image_height"]."<bR>\n";
										}
									}
									else
									{
										//resize the original image
										if (($this->images_to_display[1]["original_image_width"] > $this->ad_configuration_data->LEAD_PICTURE_WIDTH) && ($this->images_to_display[1]["original_image_height"] > $this->ad_configuration_data->LEAD_PICTURE_HEIGHT))
										{
											if ($this->debug_ad_display) echo "width and height of original larger than lead pic dimensions<br>\n";
											$imageprop = ($this->ad_configuration_data->LEAD_PICTURE_WIDTH * 100) / $this->images_to_display[1]["original_image_width"];
											$imagevsize = ($this->images_to_display[1]["original_image_height"] * $imageprop) / 100 ;
											$lead_image_width = $this->ad_configuration_data->LEAD_PICTURE_WIDTH;
											$lead_image_height = ceil($imagevsize);

											if ($lead_image_height > $this->ad_configuration_data->LEAD_PICTURE_HEIGHT)
											{
												$imageprop = ($this->ad_configuration_data->LEAD_PICTURE_HEIGHT * 100) / $this->images_to_display[1]["original_image_height"];
												$imagehsize = ($this->images_to_display[1]["original_image_width"] * $imageprop) / 100 ;
												$lead_image_height = $this->ad_configuration_data->LEAD_PICTURE_HEIGHT;
												$lead_image_width = ceil($imagehsize);
											}
										}
										elseif ($this->images_to_display[1]["original_image_width"] > $this->ad_configuration_data->LEAD_PICTURE_WIDTH)
										{
											if ($this->debug_ad_display)
												echo "width of original larger than lead pic dimensions<br>\n";
											$imageprop = ($this->ad_configuration_data->LEAD_PICTURE_WIDTH * 100) / $this->images_to_display[1]["original_image_width"];
											$imagevsize = ($this->images_to_display[1]["original_image_height"] * $imageprop) / 100 ;
											$lead_image_width = $this->ad_configuration_data->LEAD_PICTURE_WIDTH;
											$lead_image_height = ceil($imagevsize);
										}
										elseif ($this->images_to_display[1]["original_image_height"] > $this->ad_configuration_data->LEAD_PICTURE_HEIGHT)
										{
											if ($this->debug_ad_display)
												echo "height of original larger than lead pic dimensions<br>\n";
											$imageprop = ($this->ad_configuration_data->LEAD_PICTURE_HEIGHT * 100) / $this->images_to_display[1]["original_image_height"];
											$imagehsize = ($this->images_to_display[1]["original_image_width"] * $imageprop) / 100 ;
											$lead_image_height = $this->ad_configuration_data->LEAD_PICTURE_HEIGHT;
											$lead_image_width = ceil($imagehsize);
										}
										else
										{
											if ($this->debug_ad_display)
												echo "width and height of original smaller than lead pic dimensions<br>\n";
											$lead_image_width = $this->images_to_display[1]["original_image_width"];
											$lead_image_height = $this->images_to_display[1]["original_image_height"];
										}
										if ($this->debug_ad_display)
										{
											echo $lead_image_width." is the lead_image_width for the original image where original width is: ".$this->images_to_display[1]["original_image_width"]."<bR>\n";
											echo $lead_image_height." is the lead_image_height for the original image where original height is: ".$this->images_to_display[1]["original_image_height"]."<bR>\n";
										}
									}
									if ($this->images_to_display[1]["type"] == 2)
										$lead_image_tag = $this->display_image($db, "get_image.php?image=".$this->images_to_display[1]["id"], $lead_image_width, $lead_image_height, $show->MIME_TYPE);
										//$lead_image_tag .=  "<img src=get_image.php?image=".$this->images_to_display[1]["id"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";
									else
										$lead_image_tag .= $this->display_image($db, $this->images_to_display[1]["url"], $lead_image_width, $lead_image_height, $show->MIME_TYPE);
								}
								if 	($this->images_to_display[1]["type"] == 2)
									$lead_image_tag = $this->display_image($db, "get_image.php?image=".$this->images_to_display[1]["id"], $lead_image_width, $lead_image_height, $show->MIME_TYPE);
										//$lead_image_tag .=  "<img src=get_image.php?image=".$this->images_to_display[1]["id"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";
								else
									$lead_image_tag = $this->display_image($db, $this->images_to_display[1]["url"], $lead_image_width, $lead_image_height, $show->MIME_TYPE);
							}
                            //BCS-IT 01.05.2006
							//$lead_image_tag .= "</a>";
							if ($this->debug_ad_display) echo $lead_image_tag." is the lead image tag<br><Br>\n";
                            $img_url = "<img src=imagewm.php?image=".$this->images_to_display[1]["id"]." width=$lead_image_width height=$lead_image_height>"; //$this->images_to_display[1]["id"];
							//$template = str_replace("<<LEAD_PICTURE>>",$lead_image_tag,$template);

                            //BCS-IT 26.06.2006
                            if(SHOW_VIDEO_DEFAULT)
							{
								$video_object = <<<EOD
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
        codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0"
        width="322"
        height="272"
        id="amplayer"
        align="middle">
   <param name="movie"
value="http://www.auctionmercial.com/singleVideo.swf?id=<<OPTIONAL_FIELD_16>>&auto_play=true"
/>
   <param name="allowScriptAccess" value="sameDomain" />
   <param name="allowFullScreen" value="false" />
   <param name="quality" value="high" />
   <param name="play" value="true" />
   <param name="bgcolor" value="#ffffff" />
   <embed
src="http://www.auctionmercial.com/singleVideo.swf?id=<<OPTIONAL_FIELD_16>>&auto_play=true"
          wmode="transparent"
          quality="high"
          width="322"
          height="272"
          name="amplayer"
          align="middle"
          allowScriptAccess="sameDomain"
          allowFullScreen="false"
          type="application/x-shockwave-flash"
          pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
EOD;
								$template = str_replace("<<LEAD_PICTURE>>", $video_object,$template);
							}
                            else $template = str_replace("<<LEAD_PICTURE>>", $img_url,$template);
						}
						else
						{
							if ($this->debug_ad_display) echo "there is no image to display in the lead image spot<BR><br>\n";
							$template = str_replace("<<LEAD_PICTURE>>","",$template);
						}
					} //end of if lead picture

					//get ads extra questions
					$extra_question_block = $this->get_ads_extra_values($db,$id,$show->CATEGORY);

					//get ads extra checkboxes
					$extra_checkbox_block = $this->get_ads_extra_checkboxes($db,$id,$show->CATEGORY);

					if (strlen(trim($this->configuration_data['sold_image'])) >0)
					{
						if ($show->SOLD_DISPLAYED)
						{
							$title = "<img src=".$this->configuration_data['sold_image']." border=0> ".stripslashes(urldecode($show->TITLE));
						}
						else
						{
							$title = stripslashes(urldecode($show->TITLE));
						}
					}
					else
					{
						$title = stripslashes(urldecode($show->TITLE));
					}

					if($show->ENDS <= $this->shifted_time($db))
					{
						$template = str_replace("<<TITLE>>",$title."&nbsp;"."<font class=closed_label>".$this->messages[103369]."</font>",$template);
					}
					else
					{
						$template = str_replace("<<TITLE>>",$title,$template);
					}

/*
					//$template = str_replace("<<TITLE>>",$title,$template);

					if (((($this->browsing_configuration->USE_MAPPING_ADDRESS_FIELD) && (strlen(trim($show->MAPPING_ADDRESS)) > 0))
						|| (($this->browsing_configuration->USE_MAPPING_ZIP_FIELD) && (strlen(trim($show->MAPPING_ZIP)) > 0)))
						&& ($this->browsing_configuration->USE_MAPPING_STATE_FIELD) && (strlen(trim($show->MAPPING_STATE)) > 0)
						&& ($this->browsing_configuration->USE_MAPPING_CITY_FIELD) && (strlen(trim($show->MAPPING_CITY)) > 0))

					{
						//build mapquest link
						$mapquest_link = "<a href=\"javascript:winimage('http://maps.google.com/maps?f=q&hl=en&q=";
						//$mapquest_link = "<a href=http://www.mapquest.com/maps/map.adp?homesubmit=Get+Map";
						$mapquest_link .= "&address=".urlencode(trim($show->MAPPING_ADDRESS));
						$mapquest_link .= "&city=".urlencode(trim($show->MAPPING_CITY));
						$mapquest_link .= "&state=".urlencode(trim($show->MAPPING_STATE));
						if (($this->browsing_configuration->USE_MAPPING_COUNTRY_FIELD) && (strlen(trim($show->MAPPING_COUNTRY)) > 0))
							$mapquest_link .= "&country=".urlencode(trim($show->MAPPING_COUNTRY));
						if (($this->browsing_configuration->USE_MAPPING_ZIP_FIELD) && (strlen(trim($show->MAPPING_ZIP)) > 0))
							$mapquest_link .= "&zipcode=".urlencode(trim($show->MAPPING_ZIP));
						$mapquest_link .= "','800','800')\" class=mapping_link>".stripslashes(urldecode($this->messages[1624]))."</a>";
						$template = str_replace("<<MAPPING_LINK>>",$mapquest_link,$template);
					}
					else
						$template = str_replace("<<MAPPING_LINK>>","",$template);

					if ($this->browsing_configuration->USE_MAPPING_ADDRESS_FIELD)
					{
						$template = str_replace("<<MAPPING_ADDRESS>>",stripslashes(urldecode($show->MAPPING_ADDRESS)),$template);
					}

					if ($this->browsing_configuration->USE_MAPPING_ZIP_FIELD)
					{
						$template = str_replace("<<MAPPING_ZIP>>",stripslashes(urldecode($show->MAPPING_ZIP)),$template);
					}

					if ($this->browsing_configuration->USE_MAPPING_STATE_FIELD)
					{
						$template = str_replace("<<MAPPING_STATE>>",stripslashes(urldecode($show->MAPPING_STATE)),$template);
					}

					if ($this->browsing_configuration->USE_MAPPING_CITY_FIELD)
					{
						$template = str_replace("<<MAPPING_CITY>>",stripslashes(urldecode($show->MAPPING_CITY)),$template);
					}

					if ($this->browsing_configuration->USE_MAPPING_COUNTRY_FIELD)
					{
						$template = str_replace("<<MAPPING_COUNTRY>>",stripslashes(urldecode($show->MAPPING_COUNTRY)),$template);
					}
*/





                    //$template = str_replace("<<TITLE>>",$title,$template);

                    if (((($this->browsing_configuration->USE_MAPPING_ADDRESS_FIELD) && (strlen(trim($show->MAPPING_ADDRESS)) > 0))
                        || (($this->browsing_configuration->USE_MAPPING_ZIP_FIELD) && (strlen(trim($show->MAPPING_ZIP)) > 0)))
                        && ($this->browsing_configuration->USE_MAPPING_STATE_FIELD) && (strlen(trim($show->MAPPING_STATE)) > 0)
                        && ($this->browsing_configuration->USE_MAPPING_CITY_FIELD) && (strlen(trim($show->MAPPING_CITY)) > 0))

                    {
                        //build mapquest link
                        //$mapquest_link = "<a href=http://www.mapquest.com/maps/map.adp?homesubmit=Get+Map";
                    	if ($this->browsing_configuration->USE_MAPPING_ADDRESS_FIELD)
                    	{
                        	$link .= '+'.stripslashes(urldecode($show->MAPPING_ADDRESS));
                    	}

                        if ($this->browsing_configuration->USE_MAPPING_CITY_FIELD)
                        {
                            $link .= '+'.stripslashes(urldecode($show->MAPPING_CITY));
                        }

                        if ($this->browsing_configuration->USE_MAPPING_STATE_FIELD)
                        {
                            $link .= '+'.stripslashes(urldecode($show->MAPPING_STATE));
                        }

                    	if ($this->browsing_configuration->USE_MAPPING_ZIP_FIELD)
                    	{
                        	$link .= '+'.stripslashes(urldecode($show->MAPPING_ZIP));
                    	}

                    	if ($this->browsing_configuration->USE_MAPPING_COUNTRY_FIELD)
                    	{
                        	$link .= '+'.stripslashes(urldecode($show->MAPPING_COUNTRY));
                    	}

                        $mapquest_link = "<a href=\"javascript:winimage('http://maps.google.com/maps?f=q&hl=en&q=<<QUERY>>";
                        $link = str_replace(' ', '+', $link);
                        $mapquest_link = str_replace("<<QUERY>>", "$link", $mapquest_link);
                        $mapquest_link .= "','800','800')\" class=mapping_link>".stripslashes(urldecode($this->messages[1624]))."</a>";
                        $template = str_replace("<<MAPPING_LINK>>",$mapquest_link,$template);
                    }
                    else
                        $template = str_replace("<<MAPPING_LINK>>","",$template);
                    //END GOOGLE SEARCH

					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_1)
					{
						$template = str_replace("<<OPTIONAL_FIELD_1_LABEL>>",stripslashes(urldecode($this->messages[912])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_1>>",stripslashes(urldecode($show->OPTIONAL_FIELD_1)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_2)
					{
						$template = str_replace("<<OPTIONAL_FIELD_2_LABEL>>",stripslashes(urldecode($this->messages[913])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_2>>",stripslashes(urldecode($show->OPTIONAL_FIELD_2)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_3)
					{
						$template = str_replace("<<OPTIONAL_FIELD_3_LABEL>>",stripslashes(urldecode($this->messages[914])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_3>>",stripslashes(urldecode($show->OPTIONAL_FIELD_3)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_4)
					{
						$template = str_replace("<<OPTIONAL_FIELD_4_LABEL>>",stripslashes(urldecode($this->messages[915])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_4>>",stripslashes(urldecode($show->OPTIONAL_FIELD_4)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_5)
					{
						$template = str_replace("<<OPTIONAL_FIELD_5_LABEL>>",stripslashes(urldecode($this->messages[916])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_5>>",stripslashes(urldecode($show->OPTIONAL_FIELD_5)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_6)
					{
						$template = str_replace("<<OPTIONAL_FIELD_6_LABEL>>",stripslashes(urldecode($this->messages[917])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_6>>",stripslashes(urldecode($show->OPTIONAL_FIELD_6)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_7)
					{
						$template = str_replace("<<OPTIONAL_FIELD_7_LABEL>>",stripslashes(urldecode($this->messages[918])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_7>>",stripslashes(urldecode($show->OPTIONAL_FIELD_7)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_8)
					{
						$template = str_replace("<<OPTIONAL_FIELD_8_LABEL>>",stripslashes(urldecode($this->messages[919])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_8>>",stripslashes(urldecode($show->OPTIONAL_FIELD_8)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_9)
					{
						$template = str_replace("<<OPTIONAL_FIELD_9_LABEL>>",stripslashes(urldecode($this->messages[920])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_9>>",stripslashes(urldecode($show->OPTIONAL_FIELD_9)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_10)
					{
						$template = str_replace("<<OPTIONAL_FIELD_10_LABEL>>",stripslashes(urldecode($this->messages[921])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_10>>",stripslashes(urldecode($show->OPTIONAL_FIELD_10)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_11)
					{
						$template = str_replace("<<OPTIONAL_FIELD_11_LABEL>>",stripslashes(urldecode($this->messages[1726])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_11>>",stripslashes(urldecode($show->OPTIONAL_FIELD_11)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_12)
					{
						$template = str_replace("<<OPTIONAL_FIELD_12_LABEL>>",stripslashes(urldecode($this->messages[1727])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_12>>",stripslashes(urldecode($show->OPTIONAL_FIELD_12)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_13)
					{
						$template = str_replace("<<OPTIONAL_FIELD_13_LABEL>>",stripslashes(urldecode($this->messages[1728])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_13>>",stripslashes(urldecode($show->OPTIONAL_FIELD_13)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_14)
					{
						$template = str_replace("<<OPTIONAL_FIELD_14_LABEL>>",stripslashes(urldecode($this->messages[1729])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_14>>",stripslashes(urldecode($show->OPTIONAL_FIELD_14)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_15)
					{
						$template = str_replace("<<OPTIONAL_FIELD_15_LABEL>>",stripslashes(urldecode($this->messages[1730])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_15>>",stripslashes(urldecode($show->OPTIONAL_FIELD_15)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_16)
					{
						$template = str_replace("<<OPTIONAL_FIELD_16_LABEL>>",stripslashes(urldecode($this->messages[1731])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_16>>",stripslashes(urldecode($show->OPTIONAL_FIELD_16)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_17)
					{
						$template = str_replace("<<OPTIONAL_FIELD_17_LABEL>>",stripslashes(urldecode($this->messages[1732])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_17>>",stripslashes(urldecode($show->OPTIONAL_FIELD_17)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_18)
					{
						$template = str_replace("<<OPTIONAL_FIELD_18_LABEL>>",stripslashes(urldecode($this->messages[1733])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_18>>",stripslashes(urldecode($show->OPTIONAL_FIELD_18)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_19)
					{
						$template = str_replace("<<OPTIONAL_FIELD_19_LABEL>>",stripslashes(urldecode($this->messages[1734])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_19>>",stripslashes(urldecode($show->OPTIONAL_FIELD_19)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_20)
					{
						$template = str_replace("<<OPTIONAL_FIELD_20_LABEL>>",stripslashes(urldecode($this->messages[1735])),$template);
						$template = str_replace("<<OPTIONAL_FIELD_20>>",stripslashes(urldecode($show->OPTIONAL_FIELD_20)),$template);
					}
					if ($this->browsing_configuration->USE_CITY_FIELD)
					{
						$template = str_replace("<<CITY_LABEL>>",stripslashes(urldecode($this->messages[1213])),$template);
						$template = str_replace("<<CITY_DATA>>",ucwords(stripslashes(urldecode($show->LOCATION_CITY))),$template);
					}
					if ($this->browsing_configuration->USE_STATE_FIELD)
					{
						$template = str_replace("<<STATE_LABEL>>",stripslashes(urldecode($this->messages[1214])),$template);
						$template = str_replace("<<STATE_DATA>>",stripslashes(urldecode($show->LOCATION_STATE)),$template);
					}
					if ($this->browsing_configuration->USE_COUNTRY_FIELD)
					{
						$template = str_replace("<<COUNTRY_LABEL>>",stripslashes(urldecode($this->messages[1215])),$template);
						$template = str_replace("<<COUNTRY_DATA>>",stripslashes(urldecode($show->LOCATION_COUNTRY)),$template);
					}
					if ($this->browsing_configuration->USE_ZIP_FIELD)
					{
						$template = str_replace("<<ZIP_LABEL>>",stripslashes(urldecode($this->messages[1216])),$template);
						$template = str_replace("<<ZIP_DATA>>",stripslashes(urldecode($show->LOCATION_ZIP)),$template);
					}

					//if($show->ITEM_TYPE==1)
					$template = str_replace("<<CLASSIFIED_ID_LABEL>>",stripslashes(urldecode($this->messages[8])),$template);
					//if($show->ITEM_TYPE==1)
					$template = str_replace("<<CLASSIFIED_ID>>",$show->ID,$template);
					$template = str_replace("<<VIEWED_COUNT_LABEL>>",stripslashes(urldecode($this->messages[10])),$template);
					$template = str_replace("<<VIEWED_COUNT>>",$show->VIEWED + 1,$template);
					if ($show->SELLER != 0)
					{
						$template = str_replace("<<SELLER_LABEL>>",stripslashes(urldecode($this->messages[3])),$template);
						$template = str_replace("<<SELLER>>","<a href=".$this->configuration_data['classifieds_url']."?a=13&b=".$id." class=display_ad_value>".$seller_data->USERNAME."</a>",$template);
					}
					else
					{
						$template = str_replace("<<SELLER_LABEL>>","",$template);
						$template = str_replace("<<SELLER>>","",$template);
					}
					if($this->is_class_auctions() || $this->is_auctions())
					{
						if ($show->START_TIME)
						{
							$bid_start_date = date(trim($this->configuration_data['entry_date_configuration']),$show->START_TIME);
							$time = $show->START_TIME;
						}
						else
						{
							$bid_start_date = date(trim($this->configuration_data['entry_date_configuration']),$show->DATE);
							$time = $show->DATE;
						}
						if ($time!=0 && $time<time())
						{
							$template = str_replace("<<BID_START_DATE_LABEL>>",urldecode($this->messages[102819]), $template);
							$template = str_replace("<<BID_START_DATE>>",$bid_start_date,$template);
						}
						else
						{
							$template = str_replace("<<BID_START_DATE_LABEL>>","", $template);
							$template = str_replace("<<BID_START_DATE>>","",$template);
						}
					}
					$template = str_replace("<<DATE_STARTED_LABEL>>",stripslashes(urldecode($this->messages[4])),$template);
					$template = str_replace("<<DATE_STARTED>>",$start_date,$template);
//					$template = str_replace("<<LOCATION_LABEL>>",stripslashes(urldecode($this->messages[6])),$template);
//					$template = str_replace("<<LOCATION>>",$location_string,$template);
					$template = str_replace("<<EXTRA_QUESTION_BLOCK>>",$extra_question_block,$template);
					$template = str_replace("<<EXTRA_CHECKBOX_BLOCK>>",$extra_checkbox_block,$template);
					$template = str_replace("<<DESCRIPTION_LABEL>>",stripslashes(urldecode($this->messages[7])),$template);
					$template = str_replace("<<DESCRIPTION>>",stripslashes(urldecode($show->DESCRIPTION)),$template);
                    $member_since = date(trim($this->configuration_data['entry_date_configuration']), $seller_data->DATE_JOINED);
                    $template = str_replace("<<MEMBER_SINCE>>", $member_since, $template);

					//classauctions details
					if ($show->ITEM_TYPE==1)
					{
						if ($this->browsing_configuration->USE_PRICE_FIELD)
						{
							$template = str_replace("<<PRICE_LABEL>>",stripslashes(urldecode($this->messages[15])),$template);
							if (((strlen(trim(urldecode($show->PRICE))) > 0)
								|| (strlen(trim(urldecode($show->PRECURRENCY))) > 0)
								|| (strlen(trim(urldecode($show->POSTCURRENCY))) > 0)) && ($show->PRICE != 0))
							{
                                $template = str_replace("<<OFFER>>", stripslashes(urldecode("<a href=".$this->configuration_data['classifieds_file_name']."?a=1313&b=".$id." class=display_auction_value>Make Offer</a>")),$template);
								if (floor($show->PRICE) == $show->PRICE)
								{
									$template = str_replace("<<PRICE>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number(stripslashes(urldecode($show->PRICE)))." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
								}
								else
								{
									$template = str_replace("<<PRICE>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number(stripslashes(urldecode($show->PRICE)))." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
								}
							}
							else
								$template = str_replace("<<PRICE>>",stripslashes(urldecode($show->PRECURRENCY))." - ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
						}
					}

					if($this->is_class_auctions())
					{
						// Feedback
						// Will display on both the auctions and classifieds
						$template = str_replace("<<SELLER_RATING_LABEL>>",stripslashes(urldecode($this->messages[102704])),$template);

						if($this->debug_ad_display)
						{
							echo $seller_data->FEEDBACK_COUNT." is the FEEDBACK_COUNT<br>\n";
							echo $seller_data->FEEDBACK_SCORE." is FEEDBACK_SCORE<br>\n";
						}
						if (($seller_data->FEEDBACK_SCORE > 0) && ($seller_data->FEEDBACK_COUNT != 0))
						{
							$this->sql_query = "select filename from ".$this->auctions_feedback_icons_table." where begin <= ".$seller_data->FEEDBACK_SCORE." AND end >= ".$seller_data->FEEDBACK_SCORE;
							$result = $db->Execute($this->sql_query);
							if(!$result)
								$image = "";
							else
							{
								$result_img = $result->FetchNextObject();
								$image = "<img src=\"".$result_img->FILENAME."\" align=middle>";
							}
							if ($this->debug_ad_display)
							{
								echo $result->RecordCount()." is the record count<BR>\n";
							}
							$template = str_replace("<<SELLER_RATING>>",$seller_data->FEEDBACK_SCORE.$image,$template);
						}
						elseif (($seller_data->FEEDBACK_SCORE == 0) || ($seller_data->FEEDBACK_COUNT == 0))
						{
							$this->sql_query = "select filename from ".$this->auctions_feedback_icons_table." where begin = 0";
							$result = $db->Execute($this->sql_query);
							if(!$result)
								$image = "";
							else
							{
								$result_img = $result->FetchNextObject();
								$image = "<img src=\"".$result_img->FILENAME."\" align=middle>";
							}
							$template = str_replace("<<SELLER_RATING>>",$seller_data->FEEDBACK_SCORE.$image,$template);
						}
						elseif ($seller_data->FEEDBACK_SCORE < 0)
						{
							$this->sql_query = "select filename from ".$this->auctions_feedback_icons_table." where begin = -1";
							$result = $db->Execute($this->sql_query);
							if(!$result)
								$image = "";
							else
							{
								$result_img = $result->FetchNextObject();
								$image = "<img src=\"".$result_img->FILENAME."\" align=middle>";
							}
							$template = str_replace("<<SELLER_RATING>>",$seller_data->FEEDBACK_SCORE.$image,$template);
						}
						else
							$template = str_replace("<<SELLER_RATING>>",stripslashes(urldecode($this->messages[102716])),$template);

						$template = str_replace("<<FEEDBACK_LINK>>","<LI><a href=".$this->configuration_data['classifieds_file_name']."?a=1030&b=".$id."&d=".$show->SELLER." class=display_auction_value>".stripslashes(urldecode($this->messages[102717]))."</a></LI>",$template);
						$template = str_replace("<<SELLER_NUMBER_RATES_LABEL>>",stripslashes(urldecode($this->messages[102714])),$template);
						$template = str_replace("<<SELLER_NUMBER_RATES>>",$seller_data->FEEDBACK_COUNT,$template);
						$template = str_replace("<<SELLER_RATING_SCALE_EXPLANATION>>",$this->display_help_link(102826),$template);
					}

					//auctions details
					if ($show->ITEM_TYPE == 2)
					{
						$minimum_bid = $show->MINIMUM_BID;

						//check reserve price
						if ($show->RESERVE_PRICE != 0.00)
						{
							if ($show->CURRENT_BID >= $show->RESERVE_PRICE)
							{
								//reserve met
								$reserve = stripslashes(urldecode($this->messages[102694]));
							}
							else
							{
								//reserve not yer met
								$reserve = stripslashes(urldecode($this->messages[102695]));
							}
						}
						else
							$reserve = "";

						if(!$show->BUY_NOW_ONLY && $show->LIVE)
						{
							$template = str_replace("<<RESERVE>>",$reserve,$template);
						}
						else
						{
							$template = str_replace("<<RESERVE>>","",$template);
						}

						$template = str_replace("<<AUCTION_TYPE_HELP>>", $this->display_help_link(103056), $template);

						// Time remaining
						// Find weeks left
						$weeks = $this->DateDifference(w,$this->shifted_time($db),$show->ENDS);
						$remaining_weeks = ($weeks * 604800);

						// Find days left
						$days = $this->DateDifference(d,($this->shifted_time($db)+$remaining_weeks),$show->ENDS);
						$remaining_days = ($days * 86400);

						// Find hours left
						$hours = $this->DateDifference(h,($this->shifted_time($db)+$remaining_days),$show->ENDS);
						$remaining_hours = ($hours * 3600);

						// Find minutes left
						$minutes = $this->DateDifference(m,($this->shifted_time($db)+$remaining_hours),$show->ENDS);
						$remaining_minutes = ($minutes * 60);

						// Find seconds left
						$seconds = $this->DateDifference(s,($this->shifted_time($db)+$remaining_minutes),$show->ENDS);

						if ($weeks > 0)
						{
							$time_left .= $weeks." ".stripslashes(urldecode($this->messages[103191])).", ".$days." ".stripslashes(urldecode($this->messages[103192]));
						}
						elseif ($days > 0)
						{
							$time_left .= $days." ".stripslashes(urldecode($this->messages[103192])).", ".$hours." ".stripslashes(urldecode($this->messages[103193]));
						}
						elseif ($hours > 0)
						{
							$time_left .= $hours." ".stripslashes(urldecode($this->messages[103193])).", ".$minutes." ".stripslashes(urldecode($this->messages[103194]));
						}
						elseif ($minutes > 0)
						{
							$time_left .= $minutes." ".stripslashes(urldecode($this->messages[103194])).", ".$seconds." ".stripslashes(urldecode($this->messages[103195]));
						}
						elseif ($seconds > 0)
						{
							$time_left .= $seconds." ".stripslashes(urldecode($this->messages[103195]));
						}
						$template = str_replace("<<TIME_REMAINING_LABEL>>", stripslashes(urldecode($this->messages[102705])),$template);
						$template = str_replace("<<TIME_REMAINING>>", "<div nowrap>".$time_left."</div>", $template);

						if ($show->AUCTION_TYPE == 1)
							$type_of_auction = stripslashes(urldecode($this->messages[102707]));
						else
							$type_of_auction = stripslashes(urldecode($this->messages[102708]));
						if ($show->AUCTION_TYPE == 1)
						{
							$template = str_replace("<<WINNING_DUTCH_BIDDERS>>", "",$template);
							$template = str_replace("<<WINNING_DUTCH_BIDDERS_LABEL>>", "",$template);
							$template = str_replace("<<HIGH_BIDDER_LABEL>>",stripslashes(urldecode($this->messages[102697])),$template);
							if ($show->CURRENT_BID != 0)
							{
								//echo "When number of bids is not equal to zero<bR>\n";
								$high_bidder = $this->get_high_bidder_username($db,$id);
								$template = str_replace("<<HIGH_BIDDER>>",$high_bidder,$template);

								//get number of bids
								$number_of_bids = $this->get_number_of_bids($db,$id);
								$template = str_replace("<<NUM_BIDS>>",$number_of_bids,$template);

								if ($show->BUY_NOW != 0)
								{
									if(($this->configuration_data['buy_now_reserve'] != 0) && ($show->CURRENT_BID < $show->RESERVE_PRICE))
									{
										//Check for black list and invited list of buyers
										//Show only when not in blacklist but in invited list
										$template = str_replace("<<BUY_NOW_LABEL>>",stripslashes(urldecode($this->messages[102698])),$template);
										if ($this->classified_user_id)
										{
											if($can_bid)
											{
												$template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
												if ($show->LIVE)
												{
													$template = str_replace("<<BUY_NOW_LINK>>","<a href=".$this->configuration_data['classifieds_file_name']."?a=1029&b=".$show->ID."&d=1 class=buy_now_link>".stripslashes(urldecode($this->messages[102718]))."</a>",$template);
												}
												else
													$template = str_replace("<<BUY_NOW_LINK>>","",$template);
											}
											else
											{
												$template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
												$template = str_replace("<<BUY_NOW_LINK>>","",$template);
											}
										}
										else
										{
											$template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
											if ($show->LIVE)
												$template = str_replace("<<BUY_NOW_LINK>>","<a href=".$this->configuration_data['classifieds_file_name']."?a=10&c=".urlencode("a*is*1029&b*is*".$show->ID."&d*is*1")." class=buy_now_link>".stripslashes(urldecode($this->messages[102718]))."</a>",$template);
											else
												$template = str_replace("<<BUY_NOW_LINK>>","",$template);
										}
									}
									elseif(($this->configuration_data['buy_now_reserve'] == 0) && ($show->CURRENT_BID == 0.00))
									{
										//Check for black list and invited list of buyers
										//Show only when not in blacklist but in invited list
										$template = str_replace("<<BUY_NOW_LABEL>>",stripslashes(urldecode($this->messages[102698])),$template);
										if ($this->classified_user_id)
										{
											if($can_bid)
											{
												$template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
												if ($show->LIVE)
												{
													$template = str_replace("<<BUY_NOW_LINK>>","<a href=".$this->configuration_data['classifieds_file_name']."?a=1029&b=".$show->ID."&d=1 class=buy_now_link>".stripslashes(urldecode($this->messages[102718]))."</a>",$template);
												}
												else
													$template = str_replace("<<BUY_NOW_LINK>>","",$template);
											}
											else
											{
												$template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
												$template = str_replace("<<BUY_NOW_LINK>>","",$template);
											}
										}
										else
										{
											$template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
											if ($show->LIVE)
												$template = str_replace("<<BUY_NOW_LINK>>","<a href=".$this->configuration_data['classifieds_file_name']."?a=10&c=".urlencode("a*is*1029&b*is*".$show->ID."&d*is*1")." class=buy_now_link>".stripslashes(urldecode($this->messages[102718]))."</a>",$template);
											else
												$template = str_replace("<<BUY_NOW_LINK>>","",$template);
										}
									}
									else
									{
										//remove buy now as there is a malfunction
										$template = str_replace("<<BUY_NOW_LABEL>>","",$template);
										$template = str_replace("<<BUY_NOW_DATA>>","",$template);
										$template = str_replace("<<BUY_NOW_LINK>>","",$template);
									}
								}
								else
								{
									//remove buy now because there is no buy now on this auction
									$template = str_replace("<<BUY_NOW_LABEL>>","",$template);
									$template = str_replace("<<BUY_NOW_DATA>>","",$template);
									$template = str_replace("<<BUY_NOW_LINK>>","",$template);
								}
							}
							else
							{
								//no bids recieved
								$template = str_replace("<<NUM_BIDS>>",stripslashes(urldecode($this->messages[103002])),$template);
								//no high bidder
								$template = str_replace("<<HIGH_BIDDER>>",stripslashes(urldecode($this->messages[103002])),$template);

								//display buy now if not 0
								if (($show->BUY_NOW != 0) && ($show->AUCTION_TYPE == 1) && ($show->LIVE == 1))
								{
									//sunit
									//Check for black list and invited list of buyers
									//Show only when not in blacklist but in invited list
									$template = str_replace("<<BUY_NOW_LABEL>>",stripslashes(urldecode($this->messages[102698])),$template);
									$template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);

									if ($this->classified_user_id)
									{
										if($can_bid)
										{
											$template = str_replace("<<BUY_NOW_LINK>>","<a href=".$this->configuration_data['classifieds_file_name']."?a=1029&b=".$show->ID."&d=1 class=buy_now_link>".stripslashes(urldecode($this->messages[102718]))."</a>",$template);
										}
										else
										{
											$template = str_replace("<<BUY_NOW_LINK>>","",$template);
										}
									}
									else
									{
										$template = str_replace("<<BUY_NOW_LINK>>","<a href=".$this->configuration_data['classifieds_file_name']."?a=10&c=".urlencode("a*is*1029&b*is*".$show->ID."&d*is*1")." class=buy_now_link>".stripslashes(urldecode($this->messages[102718]))."</a>",$template);
									}
								}
								else
								{
									//remove buy now
									$template = str_replace("<<BUY_NOW_LABEL>>","",$template);
									$template = str_replace("<<BUY_NOW_DATA>>","",$template);
									$template = str_replace("<<BUY_NOW_LINK>>","",$template);
								}
							}
						}
						else
						{
							 //Get dutch winners
							$this->sql_query = "select * from ".$this->bid_table." where auction_id=".$show->ID." order by bid desc,time_of_bid asc";
							$bid_result = $db->Execute($this->sql_query);
							if (!$bid_result)
							{
								echo $this->sql_query;
								return false;
							}
							elseif ($bid_result->RecordCount() > 0)
							{
								$total_quantity = $show->QUANTITY;
								//echo "total items sold - ".$total_quantity."<br>\n";
								$final_dutch_bid = 0;
								$seller_report = "";
								$show_bidder = $bid_result->FetchNextObject();
								$quantity_bidder_receiving = 0;
								do
								{
									if ( $show_bidder->QUANTITY <= $total_quantity )
									{
										$quantity_bidder_receiving = $show_bidder->QUANTITY ;
										if ( $show_bidder->QUANTITY == $total_quantity )
										{
											$final_dutch_bid = $show_bidder->BID;
											//echo $final_dutch_bid." is final bid after total = bid quantity<bR>\n";
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

									$local_key = count($this->dutch_bidders);
									$this->dutch_bidders[$local_key]["bidder"] = $show_bidder->BIDDER;
									$this->dutch_bidders[$local_key]["quantity"] = $quantity_bidder_receiving;
									$this->dutch_bidders[$local_key]["bid"] = $show_bidder->BID;
									$bidder_info = $this->get_user_data($db,$show_bidder->BIDDER);
								} while (($show_bidder = $bid_result->FetchNextObject()) && ($total_quantity != 0) && ($final_dutch_bid == 0));
							}

							if (count($this->dutch_bidders) > 0)
							{
								//display the current winning bidders
								$dutch_bidders_table = "<table cellspacing=1 cellpadding=2 border=0>\n";
								$dutch_bidders_table .= "<tr class=current_dutch_bidders_header><td>".stripslashes(urldecode($this->messages[102711]))."</td>\n\t";
								$dutch_bidders_table .= "<td>".stripslashes(urldecode($this->messages[102712]))."</td>\n\t";
								$dutch_bidders_table .= "<td>".stripslashes(urldecode($this->messages[102713]))."</td>\n</tr>\n";

								reset ($this->dutch_bidders);
								foreach ($this->dutch_bidders as $key => $value)
								{
									$bidder_info = $this->get_user_data($db,$this->dutch_bidders[$key]["bidder"]);
									$dutch_bidders_table .= "<tr class=current_dutch_bidders_body><td>".$bidder_info->USERNAME."</td>";
									$dutch_bidders_table .= "<td>".stripslashes(urldecode($show->PRECURRENCY))." ".$this->dutch_bidders[$key]["bid"]." ".stripslashes(urldecode($show->POSTCURRENCY))."</td>";
									$dutch_bidders_table .= "<td>".$this->dutch_bidders[$key]["quantity"]."</td>\n</tr>\n";
								}
								$dutch_bidders_table .= "</table>";
								$template = str_replace("<<WINNING_DUTCH_BIDDERS>>",$dutch_bidders_table,$template);
								$template = str_replace("<<WINNING_DUTCH_BIDDERS_LABEL>>",stripslashes(urldecode($this->messages[102709])),$template);
							}
							else
							{
								$template = str_replace("<<WINNING_DUTCH_BIDDERS>>",stripslashes(urldecode($this->messages[102710])),$template);
								$template = str_replace("<<WINNING_DUTCH_BIDDERS_LABEL>>",stripslashes(urldecode($this->messages[102709])),$template);
							}

							$template = str_replace("<<HIGH_BIDDER_LABEL>>",stripslashes(urldecode($this->messages[102697])),$template);
							$number_of_bids = $this->get_number_of_bids($db,$id);
							if(!$show->BUY_NOW_ONLY)
							{
								$template = str_replace("<<NUM_BIDS>>",$number_of_bids,$template);
							}
							else
							{
								$template = str_replace("<<NUM_BIDS>>","",$template);
							}
							$template = str_replace("<<BUY_NOW_LABEL>>","",$template);
							$template = str_replace("<<BUY_NOW_DATA>>","",$template);
							$template = str_replace("<<BUY_NOW_LINK>>","",$template);
							$template = str_replace("<<HIGH_BIDDER>>",stripslashes(urldecode($this->messages[103069])),$template);
						}

						$template = str_replace("<<NUM_BIDS_LABEL>>",stripslashes(urldecode($this->messages[102696])),$template);
						$template = str_replace("<<QUANTITY_LABEL>>",stripslashes(urldecode($this->messages[102699])),$template);
						$template = str_replace("<<QUANTITY>>",$show->QUANTITY,$template);
						//$template = str_replace("<<AUCTION_ID_LABEL>>",stripslashes(urldecode($this->messages[100008])),$template);
						//$template = str_replace("<<AUCTION_ID>>",$show->ID,$template);

						if($this->configuration_data['bid_history_link_live'] == 1 && !$show->BUY_NOW_ONLY)
						{
							if ($this->debug_ad_display) echo "BID_HISTORY_LINK replace 1<BR>\n";
							$template = str_replace("<<BID_HISTORY_LINK>>","<a href=".$this->configuration_data['classifieds_file_name']."?a=1031&b=".$id." class=bid_history_link>".stripslashes(urldecode($this->messages[102706]))."</a>",$template);
						}
						//else
						//{
						//	if ($this->debug_ad_display) echo "BID_HISTORY_LINK replace 2<BR>\n";
						//	$template = str_replace("<<BID_HISTORY_LINK>>","&nbsp;",$template);
						//}

						//payment_types
						if($this->configuration_data['payment_types'])
						{
							$show->PAYMENT_OPTIONS = stripslashes(urldecode($show->PAYMENT_OPTIONS));
							$payment_options = str_replace("||",", ",$show->PAYMENT_OPTIONS);
							$template = str_replace("<<PAYMENT_OPTIONS_LABEL>>",stripslashes(urldecode($this->messages[102853])),$template);
							$template = str_replace("<<PAYMENT_OPTIONS>>",$payment_options,$template);
						}
						else
						{
							$template = str_replace("<<PAYMENT_OPTIONS_LABEL>>","",$template);
							$template = str_replace("<<PAYMENT_OPTIONS>>","",$template);
						}
						$template = str_replace("<<AUCTION_TYPE_LABEL>>",stripslashes(urldecode($this->messages[102700])),$template);
						$template = str_replace("<<AUCTION_TYPE_DATA>>",$type_of_auction,$template);
						$template = str_replace("<<DATE_ENDED>>",$end_date,$template);

						$member_since = date(trim($this->configuration_data['entry_date_configuration']), $seller_data->DATE_JOINED);
						$template = str_replace("<<MEMBER_SINCE>>", $member_since, $template);

						if ($show->LIVE == 1)
						{
							$template = str_replace("<<DATE_ENDED_LABEL>>",stripslashes(urldecode($this->messages[102701])),$template);
						}
						else
						{
							$template = str_replace("<<DATE_ENDED_LABEL>>",stripslashes(urldecode($this->messages[102701])),$template);
						}

						if(!$show->BUY_NOW_ONLY)
						{
                        	if($show->LIVE)
                            {
								$template = str_replace("<<MINIMUM_LABEL>>",stripslashes(urldecode($this->messages[102702])),$template);
								$template = str_replace("<<MINIMUM_BID>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $minimum_bid)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
                            }
                            else
                            {
                                $sellers_other_ads_link = "<a href=".$this->configuration_data['classifieds_url']."?a=6&b=".$show->SELLER." class=sellers_other_ads_link>".stripslashes(urldecode($this->messages[750]))."</a>";
                                $template = str_replace("<<MINIMUM_LABEL>>", "",$template);
                                $template = str_replace("<<MINIMUM_BID>>", "<font color=#ff0000><strong>Auction closed</strong></font>, see $sellers_other_ads_link",$template);
                            }

							if($this->classified_user_id == $show->SELLER)
							{
								$template = str_replace("<<RESERVE_LABEL>>",stripslashes(urldecode($this->messages[102966])),$template);
								$template = str_replace("<<RESERVE_BID>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->RESERVE_PRICE)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
							}
							$template = str_replace("<<STARTING_LABEL>>",stripslashes(urldecode($this->messages[102703])),$template);
							$template = str_replace("<<STARTING_BID>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number($show->STARTING_BID)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
						}
						else
						{
							$template = str_replace("<<MINIMUM_LABEL>>","",$template);
							$template = str_replace("<<MINIMUM_BID>>","",$template);

							$template = str_replace("<<RESERVE_LABEL>>","",$template);
							$template = str_replace("<<RESERVE_BID>>","",$template);

							$template = str_replace("<<STARTING_LABEL>>","",$template);
							$template = str_replace("<<STARTING_BID>>","",$template);
						}

						if ($show->LIVE == 1)
						{
							if ($this->debug_ad_display)
							{
								echo "checking live to display bid and history links<br>";
								echo "---".$show->LIVE." is live<BR>\n";
								echo "---".$this->classified_user_id." is classified_user_id<BR>\n";
							}
							//Check for black list and invited list of buyers
							if ($this->classified_user_id)
							{
								if($can_bid)
								{
									$make_bid_link = "<a href=".$this->configuration_data['classifieds_file_name']."?a=1029&b=".$id." class=make_bid_link>".stripslashes(urldecode($this->messages[102719]))."</a>";

									if(!$show->BUY_NOW_ONLY)
									{
										$template = str_replace("<<MAKE_BID_LINK>>",$make_bid_link,$template);
									}
									else
									{
										$template = str_replace("<<MAKE_BID_LINK>>","",$template);
									}
								}
								else
								{
									$template = str_replace("<<MAKE_BID_LINK>>",$error_msg."&nbsp;".stripslashes(urldecode($this->messages[102863])),$template);
								}
							}
							else
							{
								if ($show->LIVE)
								{
									$make_bid_link = "<a href=".$this->configuration_data['classifieds_file_name']."?a=10&c=".urlencode("a*is*1029&b*is*".$id)." class=make_bid_link>".stripslashes(urldecode($this->messages[102719]))."</a>";
								}
								else
								{
									$make_bid_link = "";
								}
								$template = str_replace("<<MAKE_BID_LINK>>",$make_bid_link,$template);

							}

							if($this->configuration_data['bid_history_link_live'] == 1)
							{
								if ($this->debug_ad_display) echo "BID_HISTORY_LINK replace 3<BR>\n";
								$template = str_replace("<<BID_HISTORY_LINK>>","<a href=".$this->configuration_data['classifieds_file_name']."?a=1031&b=".$id." class=bid_history_link>".stripslashes(urldecode($this->messages[102706]))."</a>",$template);
							}
							else
							{
								if ($this->debug_ad_display) echo "BID_HISTORY_LINK replace 4<BR>\n";
								$template = str_replace("<<BID_HISTORY_LINK>>","&nbsp;",$template);
							}
							//sunit
						}
						else
						{
							if ($this->debug_ad_display)
							{
								echo "not live for checking to display bid and history links<br>";
								echo "---".$show->LIVE." is live<BR>\n";
								echo "---".$this->classified_user_id." is classified_user_id<BR>\n";
							}
							$template = str_replace("<<MAKE_BID_LINK>>","",$template);
							//$template = str_replace("<<MAKE_BID_LINK>>","&nbsp;",$template);
							$template = str_replace("<<FAVORITES_LINK>>","&nbsp;",$template);
							$template = str_replace("<<NOTIFY_FRIEND_LINK>>","&nbsp;",$template);
							if ($this->debug_ad_display)
							{
								echo "BID_HISTORY_LINK replace 5<BR>\n";
								echo strpos($template, "<<BID_HISTORY_LINK>>")." is the position of BID_HISTORY_LINK<bR>\n";
							}
							$template = str_replace("<<BID_HISTORY_LINK>>","<a href=".$this->configuration_data['classifieds_file_name']."?a=1031&b=".$id." class=bid_history_link>".stripslashes(urldecode($this->messages[102706]))."</a>",$template);
						}

					}


					$template = str_replace("<<IMAGE_BLOCK>>",$image_block,$template);
					$template = str_replace("<<CATEGORY_TREE>>",$category_tree_fields,$template);

					if (($this->browsing_configuration->PUBLICALLY_EXPOSE_EMAIL) && ($show->EXPOSE_EMAIL))
					{
						$template = str_replace("<<PUBLIC_EMAIL_LABEL>>",stripslashes(urldecode($this->messages[1344])),$template);
						$template = str_replace("<<PUBLIC_EMAIL>>",stripslashes(urldecode($show->EMAIL)),$template);
					}
					elseif ($this->browsing_configuration->PUBLICALLY_EXPOSE_EMAIL)
					{
						$template = str_replace("<<PUBLIC_EMAIL_LABEL>>","",$template);
						$template = str_replace("<<PUBLIC_EMAIL>>","",$template);
					}

					if (($this->browsing_configuration->USE_PHONE_1_OPTION_FIELD) && (strlen($show->PHONE) > 0))
					{
						$template = str_replace("<<PHONE_LABEL>>","<LI>".stripslashes(urldecode($this->messages[1347])),$template);
						$formatted_phone_number = $this->format_phone_data(stripslashes(urldecode($show->PHONE)));
						$template = str_replace("<<PHONE_DATA>>",$formatted_phone_number."</LI>",$template);
					}
					elseif ($this->browsing_configuration->USE_PHONE_1_OPTION_FIELD)
					{
						$template = str_replace("<<PHONE_LABEL>>","",$template);
						$template = str_replace("<<PHONE_DATA>>","",$template);
					}

					if (($this->browsing_configuration->USE_PHONE_2_OPTION_FIELD) && (strlen($show->PHONE2) > 0))
					{
						$template = str_replace("<<PHONE2_LABEL>>",stripslashes(urldecode($this->messages[1348])),$template);
						$formatted_phone_number = $this->format_phone_data(stripslashes(urldecode($show->PHONE2)));
						$template = str_replace("<<PHONE2_DATA>>",$formatted_phone_number,$template);
					}
					elseif ($this->browsing_configuration->USE_PHONE_2_OPTION_FIELD)
					{
						$template = str_replace("<<PHONE2_LABEL>>","",$template);
						$template = str_replace("<<PHONE2_DATA>>","",$template);
					}

					if (($this->browsing_configuration->USE_FAX_FIELD_OPTION) && (strlen($show->FAX) > 0))
					{
						$template = str_replace("<<FAX_LABEL>>","<LI>".stripslashes(urldecode($this->messages[1349])),$template);
						$formatted_phone_number = $this->format_phone_data(stripslashes(urldecode($show->FAX)));
						$template = str_replace("<<FAX_DATA>>",$formatted_phone_number."</LI>",$template);
					}
					elseif ($this->browsing_configuration->USE_FAX_FIELD_OPTION)
					{
						$template = str_replace("<<FAX_LABEL>>","",$template);
						$template = str_replace("<<FAX_DATA>>","",$template);
					}

					if ($this->browsing_configuration->USE_URL_LINK_1)
					{
						if (strlen(trim($show->URL_LINK_1)) > 0)
						{
							if (stristr(stripslashes(urldecode($show->URL_LINK_1)),"http://"))
								$url_link_1 = "<a class=url_link_1 href=\"javascript:void(null)\" onClick=\"window.open('".stripslashes(urldecode($show->URL_LINK_1))."','mywindow','toolbar=yes,location=yes,menubar=yes,scrollbars=yes,resizable=yes');\">".stripslashes(urldecode($this->messages[2440]))."</a>";
							else
								$url_link_1 = "<a class=url_link_1 href=\"javascript:void(null)\" onClick=\"window.open('http://".stripslashes(urldecode($show->URL_LINK_1))."','mywindow','toolbar=yes,location=yes,menubar=yes,scrollbars=yes,resizable=yes');\">".stripslashes(urldecode($this->messages[2440]))."</a>";
							$template = str_replace("<<URL_LINK_1>>",$url_link_1,$template);
						}
						else
							$template = str_replace("<<URL_LINK_1>>","",$template);

					}

					if ($this->browsing_configuration->USE_URL_LINK_2)
					{
						if (strlen(trim($show->URL_LINK_2)) > 0)
						{
							if (stristr(stripslashes(urldecode($show->URL_LINK_2)),"http://"))
								$url_link_2 = "<a class=url_link_2 href=\"javascript:void(null)\" onClick=\"window.open('".stripslashes(urldecode($show->URL_LINK_2))."','mywindow','toolbar=yes,location=yes,menubar=yes,scrollbars=yes,resizable=yes');\">".stripslashes(urldecode($this->messages[2441]))."</a>";
							else
								$url_link_2 = "<a class=url_link_2 href=\"javascript:void(null)\" onClick=\"window.open('http://".stripslashes(urldecode($show->URL_LINK_2))."','mywindow','toolbar=yes,location=yes,menubar=yes,scrollbars=yes,resizable=yes');\">".stripslashes(urldecode($this->messages[2441]))."</a>";

							$template = str_replace("<<URL_LINK_2>>",$url_link_2,$template);
						}
						else
							$template = str_replace("<<URL_LINK_2>>","",$template);
					}

					if ($this->browsing_configuration->USE_URL_LINK_3)
					{
						if (strlen(trim($show->URL_LINK_3)) > 0)
						{
							if (stristr(stripslashes(urldecode($show->URL_LINK_3)),"http://"))
								$url_link_3 = "<a class=url_link_3 href=\"javascript:void(null)\" onClick=\"window.open('".stripslashes(urldecode($show->URL_LINK_3))."','mywindow','toolbar=yes,location=yes,menubar=yes,scrollbars=yes,resizable=yes');\">".stripslashes(urldecode($this->messages[2442]))."</a>";
							else
								$url_link_3 = "<a class=url_link_3 href=\"javascript:void(null)\" onClick=\"window.open('http://".stripslashes(urldecode($show->URL_LINK_3))."','mywindow','toolbar=yes,location=yes,menubar=yes,scrollbars=yes,resizable=yes');\">".stripslashes(urldecode($this->messages[2442]))."</a>";

							$template = str_replace("<<URL_LINK_3>>",$url_link_3,$template);
						}
						else
							$template = str_replace("<<URL_LINK_3>>","",$template);
					}

					if (($seller_data) && ($show->SELLER))
					{
						$template = str_replace("<<SELLER_FIRST_NAME>>",stripslashes(urldecode($seller_data->FIRSTNAME)),$template);
						$template = str_replace("<<SELLER_LAST_NAME>>",stripslashes(urldecode($seller_data->LASTNAME)),$template);
						if (stristr(stripslashes($seller_data->URL), urldecode("http://")))
							$url_current_line = "<a href=".stripslashes(urldecode($seller_data->URL))." target=_blank>".stripslashes(urldecode($seller_data->URL))."</a>";
						else
							$url_current_line = "<a href=http://".stripslashes(urldecode($seller_data->URL))." target=_blank>".stripslashes(urldecode($seller_data->URL))."</a>";
						$template = str_replace("<<SELLER_URL>>",$url_current_line,$template);
						//$template = str_replace("<<SELLER_URL>>",stripslashes(urldecode($seller_data->URL)),$template);
						$template = str_replace("<<SELLER_ADDRESS>>",stripslashes(urldecode($seller_data->ADDRESS." ".$seller_data->ADDRESS_2)),$template);
						$template = str_replace("<<SELLER_CITY>>",stripslashes(urldecode($seller_data->CITY)),$template);
						$template = str_replace("<<SELLER_STATE>>",stripslashes(urldecode($seller_data->STATE)),$template);
						$template = str_replace("<<SELLER_COUNTRY>>",stripslashes(urldecode($seller_data->COUNTRY)),$template);
						$template = str_replace("<<SELLER_ZIP>>",stripslashes(urldecode($seller_data->ZIP)),$template);
						$template = str_replace("<<SELLER_PHONE>>",stripslashes(urldecode($seller_data->PHONE)),$template);
						$template = str_replace("<<SELLER_PHONE2>>",stripslashes(urldecode($seller_data->PHONE2)),$template);
						$template = str_replace("<<SELLER_FAX>>",stripslashes(urldecode($seller_data->FAX)),$template);
						$template = str_replace("<<SELLER_COMPANY_NAME>>",stripslashes(urldecode($seller_data->COMPANY_NAME)),$template);
						$template = str_replace("<<SELLER_OPTIONAL_1>>",stripslashes(urldecode($seller_data->OPTIONAL_FIELD_1)),$template);
						$template = str_replace("<<SELLER_OPTIONAL_2>>",stripslashes(urldecode($seller_data->OPTIONAL_FIELD_2)),$template);
						$template = str_replace("<<SELLER_OPTIONAL_3>>",stripslashes(urldecode($seller_data->OPTIONAL_FIELD_3)),$template);
						$template = str_replace("<<SELLER_OPTIONAL_4>>",stripslashes(urldecode($seller_data->OPTIONAL_FIELD_4)),$template);
						$template = str_replace("<<SELLER_OPTIONAL_5>>",stripslashes(urldecode($seller_data->OPTIONAL_FIELD_5)),$template);
						$template = str_replace("<<SELLER_OPTIONAL_6>>",stripslashes(urldecode($seller_data->OPTIONAL_FIELD_6)),$template);
						$template = str_replace("<<SELLER_OPTIONAL_7>>",stripslashes(urldecode($seller_data->OPTIONAL_FIELD_7)),$template);
						$template = str_replace("<<SELLER_OPTIONAL_8>>",stripslashes(urldecode($seller_data->OPTIONAL_FIELD_8)),$template);
						$template = str_replace("<<SELLER_OPTIONAL_9>>",stripslashes(urldecode($seller_data->OPTIONAL_FIELD_9)),$template);
						$template = str_replace("<<SELLER_OPTIONAL_10>>",stripslashes(urldecode($seller_data->OPTIONAL_FIELD_10)),$template);
					}

					// Do PayPal buy it now link if the seller has entered a paypal id and the ad hasn't been sold
					if ($show->PAYPAL_ID != "" && $show->SOLD_DISPLAYED != 1 && $this->browsing_configuration->USE_BUY_NOW)
					{
					  $buy_it_now_link = "<a href=" .$this->configuration_data['classifieds_url']. "?a=31&b=" .$id. "><img src='images/btn_buyitnow.gif' alt='Buy this item now through PayPal' border='0'></a>";
					  $template = str_replace("<<BUY_IT_NOW_LINK>>",$buy_it_now_link,$template);
					}

					if ($last_classified_id)
						$previous_ad_link = "<a href=".$this->configuration_data['classifieds_url']."?a=2&b=".$last_classified_id." class=previous_ad_link>".stripslashes(urldecode($this->messages[787]))."</a>";
					else
						$previous_ad_link = "";
					$template = str_replace("<<PREVIOUS_AD_LINK>>",$previous_ad_link,$template);
					$template = str_replace("<<PREVIOUS_AUCTION_LINK>>",$previous_ad_link,$template);

					if ($next_classified_id)
						$next_ad_link = "<a href=".$this->configuration_data['classifieds_url']."?a=2&b=".$next_classified_id." class=next_ad_link>".stripslashes(urldecode($this->messages[786]))."</a>";
					else
						$next_ad_link = "";
					$template = str_replace("<<NEXT_AD_LINK>>",$next_ad_link,$template);
					$template = str_replace("<<NEXT_AUCTION_LINK>>","<LI>".$next_ad_link."</LI>",$template);

					$print_friendly_link = "<a href=".$this->configuration_data['classifieds_url']."?a=14&b=".$id." class=print_friendly_link>".stripslashes(urldecode($this->messages[1473]))."</a>";
					$template = str_replace("<<PRINT_FRIENDLY_LINK>>","<LI>".$print_friendly_link."</LI>",$template);

					$add_to_favorite_link = "<a href=".$this->configuration_data['classifieds_url']."?a=20&b=".$id." class=favorites_link>".stripslashes(urldecode($this->messages[11]))."</a>";
					$template = str_replace("<<FAVORITES_LINK>>","<LI>".$add_to_favorite_link."</LI>",$template);

					$notify_friend_link = "<a href=".$this->configuration_data['classifieds_url']."?a=12&b=".$id." class=notify_friend_link>".stripslashes(urldecode($this->messages[13]))."</a>";
					$template = str_replace("<<NOTIFY_FRIEND_LINK>>","<LI>".$notify_friend_link."</LI>",$template);

					$vote_on_ad_link = "<a href=".$this->configuration_data['classifieds_url']."?a=26&b=".$show->ID." class=vote_on_ad_link>".stripslashes(urldecode($this->messages[2289]))."</a>";
					$template = str_replace("<<VOTE_ON_AD_LINK>>",$vote_on_ad_link,$template);

					$show_ad_vote_comments_link = "<a href=".$this->configuration_data['classifieds_url']."?a=27&b=".$show->ID." class=show_votes_on_ad_link>".stripslashes(urldecode($this->messages[2290]))."</a>";
					$template = str_replace("<<SHOW_AD_VOTE_COMMENTS_LINK>>",$show_ad_vote_comments_link,$template);

					if (($seller_data) && ($show->SELLER))
					{
						$message_to_seller_link = "<a href=".$this->configuration_data['classifieds_url']."?a=13&b=".$id." class=notify_seller_link>".stripslashes(urldecode($this->messages[14]))."</a>";
						$template = str_replace("<<MESSAGE_TO_SELLER_LINK>>","<LI>".$message_to_seller_link."</LI>",$template);

						$sellers_other_ads_link = "<a href=".$this->configuration_data['classifieds_url']."?a=6&b=".$show->SELLER." class=sellers_other_ads_link>".stripslashes(urldecode($this->messages[750]))."</a>";
						if($show->ITEM_TYPE==1)
							$template = str_replace("<<SELLERS_OTHER_ADS_LINK>>","<LI>".$sellers_other_ads_link."</LI>",$template);
						if($show->ITEM_TYPE==2)
							$template = str_replace("<<SELLERS_OTHER_AUCTIONS_LINK>>","<LI>".$sellers_other_ads_link."</LI>",$template);
					}
					else
					{
						if($show->ITEM_TYPE==1)
							$template = str_replace("<<SELLERS_OTHER_ADS_LINK>>",$sellers_other_ads_link,$template);
						if($show->ITEM_TYPE==2)
							$template = str_replace("<<SELLERS_OTHER_AUCTIONS_LINK>>",$sellers_other_ads_link,$template);
						$template = str_replace("<<MESSAGE_TO_SELLER_LINK>>","",$template);
					}

					if ($show->IMAGE)
					{
						$full_images_link = "<a href=".$this->configuration_data['classifieds_url']."?a=15&b=".$id." class=full_images_link>".stripslashes(urldecode($this->messages[1369]))."</a>";
						$template = str_replace("<<FULL_IMAGES_LINK>>",$full_images_link,$template);
					}
					else
						$template = str_replace("<<FULL_IMAGES_LINK>>","",$template);

					//STOREFRONT CODE
					$storefront_link = "";
					if(file_exists('classes/storefront/store_class.php'))
					{
						include_once('classes/storefront/store_class.php');

						$this->sql_query = "select * from ".Store::get('storefront_subscriptions_table')." where user_id = ".$this->classified_user_id;
						$subscriptionResult = $db->Execute($this->sql_query);

						if($subscriptionResult->RecordCount()==1)
						{
							$subscriptionInfo = $subscriptionResult->FetchRow();
							$expiresAt = $this->shifted_time($db) + $subscriptionInfo["expiration"];
							if(time()<=$expiresAt)
							{
								$storefront_link = "<a href=stores.php?store=".$show->SELLER." class=notify_seller_link>".stripslashes(urldecode($this->messages[500005]))."</a>";
							}
						}
					}
					$template = str_replace("<<STOREFRONT_LINK>>",$storefront_link,$template);
					//STOREFRONT CODE

					//increase view count
					$this->sql_query = "update ".$this->classifieds_table." set
						viewed = ".($show->VIEWED + 1)." where id = ".$id;
					$viewed_result = $db->Execute($this->sql_query);
					if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
					if (!$viewed_result)
					{
						if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
						$this->error_message = stripslashes(urldecode($this->messages[81]));
						return false;
					}
					$this->body .= $template;
					$this->display_page($db);
					return true;
				}
				else
				{
					$this->browse_error($db);
					return true;
				}
			}
			else
			{
				$this->browse_error($db);
				return true;
			}
		}
		else
		{
			//no id to display
			$this->browse_error($db);
			return true;
		} //end of else
	} //end of function display_classifed

//####################################################################################

	function get_ads_extra_values($db,$classified_id=0,$category_id=0)
	{
		if (($category_id) && ($classified_id))
		{
			$this->sql_query = "select * from ".$this->classified_extra_table." where classified_id = ".$classified_id." and checkbox !=1 order by display_order asc";
			$special_result = $db->Execute($this->sql_query);
			if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
			if (!$special_result)
			{
				if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
				$this->error_message = stripslashes(urldecode($this->messages[81]));
				return false;
			}
			elseif ($special_result->RecordCount() > 0 )
			{
				//get extra template

				do {
					//get category specific template (if any)
					$this->sql_query = "select ".$this->categories_table.".parent_id,
						".$this->categories_languages_table.".ad_detail_extra_display_template_id,
						".$this->categories_languages_table.".auction_detail_extra_display_template_id
						from ".$this->categories_table.", ".$this->categories_languages_table." where
						".$this->categories_languages_table.".category_id = ".$category_id." and
						".$this->categories_table.".category_id = ".$category_id." and
						".$this->categories_languages_table.".language_id = ".$this->language_id;

					if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
					$detail_template_result = $db->Execute($this->sql_query);
					if (!$detail_template_result)
					{
						if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
						$this->error_message = stripslashes(urldecode($this->messages[81]));
						return false;
					}
					elseif ($detail_template_result->RecordCount() == 1)
					{
						// Check for item_type
						$this->sql_query = "select item_type from ".$this->classifieds_table." where id =".$classified_id;
						$item_type_result = $db->Execute($this->sql_query);
						if(!$item_type_result)
							return false;
						else
							$item_type = $item_type_result->FetchRow();

						$show_detail_template = $detail_template_result->FetchNextObject();
						if($item_type['item_type'] == 1)
							$template_id = $show_detail_template->AD_DETAIL_EXTRA_DISPLAY_TEMPLATE_ID;
						elseif($item_type['item_type'] == 2)
							$template_id = $show_detail_template->AUCTION_DETAIL_EXTRA_DISPLAY_TEMPLATE_ID;
						$category_id = $show_detail_template->PARENT_ID;
					}
					else
					{
						if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
						return false;
					}
				} while (($category_id) && (!$template_id));

				if (!$template_id)
				{
					if($item_type['item_type'] == 1)
						$template_id = $this->ad_configuration_data->USER_EXTRA_TEMPLATE;
					if($item_type['item_type'] == 2)
						$template_id = $this->ad_configuration_data->AUCTIONS_USER_EXTRA_TEMPLATE;
				}

				//get template
				$this->sql_query = "select * from ".$this->templates_table." where template_id = ".$template_id;
				$template_result = $db->Execute($this->sql_query);
				if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
				if (!$template_result)
				{
					if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
					echo "template failed 4<br>";
					return false;
				}
				elseif ($template_result->RecordCount() ==1)
				{
					$show_template = $template_result->FetchNextObject();
					$extra_template = stripslashes($show_template->TEMPLATE_CODE);
				}
				else
				{
					if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
					return false;
				}
				if (!$extra_template)
					$extra_template = stripslashes(urldecode($this->ad_configuration_data->USER_EXTRA_TEMPLATE));

				//$this->body .="<tr>\n\t<td>\n\t<table cellpadding=2 cellspacing=1 border=0>\n\t";
				//list the special description fields
				//echo "here<br>";
				while ($show_special = $special_result->FetchNextObject())
				{
					if ($show_special->CHECKBOX == 1)
						$current_line = str_replace("<<EXTRA_QUESTION_NAME>>","",$extra_template);
					else
						$current_line = str_replace("<<EXTRA_QUESTION_NAME>>",stripslashes(urldecode($show_special->NAME)),$extra_template);
					if ((strlen(trim($show_special->URL_ICON)) > 0) && ($show_special->CHECKBOX == 2))
					{
						if (stristr(stripslashes($show_special->VALUE), urlencode("http://")))
							$url_link = "<a href=".stripslashes(urldecode($show_special->VALUE))." target=_blank><img src=".$show_special->URL_ICON." border=0></a>";
						else
							$url_link = "<a href=http://".stripslashes(urldecode($show_special->VALUE))." target=_blank><img src=".$show_special->URL_ICON." border=0></a>";
						$current_line = str_replace("<<EXTRA_QUESTION_VALUE>>",$url_link,$current_line);
					}
					elseif ($show_special->CHECKBOX == 2)
					{
						if (stristr(stripslashes($show_special->VALUE), urlencode("http://")))
							$url_current_line = "<a href=".stripslashes(urldecode($show_special->VALUE))." target=_blank class=display_ad_extra_question_value>".stripslashes(urldecode($show_special->VALUE))."</a>";
						else
							$url_current_line = "<a href=http://".stripslashes(urldecode($show_special->VALUE))." target=_blank class=display_ad_extra_question_value>".stripslashes(urldecode($show_special->VALUE))."</a>";
						//echo $current_line."<br>\n";
						$current_line = str_replace("<<EXTRA_QUESTION_VALUE>>",$url_current_line,$current_line);

					}
					else
					{
						$current_line = str_replace("<<EXTRA_QUESTION_VALUE>>",stripslashes(urldecode($show_special->VALUE)),$current_line);
					}
					$question_block .= $current_line;
				}


				//{
				//	$current_line = str_replace("<<EXTRA_QUESTION_NAME>>",stripslashes(urldecode($show_special->NAME)),$extra_template);
				//	$current_line = str_replace("<<EXTRA_QUESTION_VALUE>>",stripslashes(urldecode($show_special->VALUE)),$current_line);
				//	$question_block .= $current_line;
				//}
				//$this->body .="</table>\n\t</td>\n</tr>\n";
			}
			return $question_block;
		}
		else
			return false;
	} //end of function get_ads_extra_questions

//#################################################################################

	function get_ads_extra_checkboxes($db,$classified_id=0,$category_id=0)
	{
		if (($classified_id) && ($category_id))
		{
			$this->sql_query = "select * from ".$this->classified_extra_table." where classified_id = ".$classified_id." and checkbox = 1 order by display_order asc";
			$special_result = $db->Execute($this->sql_query);
			if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
			if (!$special_result)
			{
				if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
				$this->error_message = stripslashes(urldecode($this->messages[81]));
				return false;
			}
			elseif ($special_result->RecordCount() > 0 )
			{
				//get extra checkbox template
				do {
					//get category specific template (if any)
					$this->sql_query = "select ".$this->categories_table.".parent_id,
						".$this->categories_languages_table.".ad_detail_checkbox_display_template_id,
						".$this->categories_languages_table.".auction_detail_checkbox_display_template_id
						from ".$this->categories_table.", ".$this->categories_languages_table." where
						".$this->categories_languages_table.".category_id = ".$category_id." and
						".$this->categories_table.".category_id = ".$category_id." and
						".$this->categories_languages_table.".language_id = ".$this->language_id;;

					if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
					$detail_template_result = $db->Execute($this->sql_query);
					if (!$detail_template_result)
					{
						if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
						$this->error_message = stripslashes(urldecode($this->messages[81]));
						return false;
					}
					elseif ($detail_template_result->RecordCount() == 1)
					{
						// Check for item_type
						$this->sql_query = "select item_type from ".$this->classifieds_table." where id =".$classified_id;
						$item_type_result = $db->Execute($this->sql_query);
						if(!$item_type_result)
							return false;
						else
							$item_type = $item_type_result->FetchRow();

						$show_detail_template = $detail_template_result->FetchNextObject();
						if($item_type['item_type'] == 1)
							$template_id = $show_detail_template->AD_DETAIL_CHECKBOX_DISPLAY_TEMPLATE_ID;
						elseif($item_type['item_type'] == 2)
							$template_id = $show_detail_template->AUCTION_DETAIL_CHECKBOX_DISPLAY_TEMPLATE_ID;
						$category_id = $show_detail_template->PARENT_ID;
					}
					else
					{
						if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
						return false;
					}
				} while (($category_id) && (!$template_id));

				if (!$template_id)
				{
					if($item_type['item_type'] == 1)
						$template_id = $this->ad_configuration_data->USER_CHECKBOX_TEMPLATE;
					elseif($item_type['item_type'] == 2)
						$template_id = $this->ad_configuration_data->AUCTIONS_USER_CHECKBOX_TEMPLATE;
				}

				//get template
				$this->sql_query = "select * from ".$this->templates_table." where template_id = ".$template_id;
				$template_result = $db->Execute($this->sql_query);
				if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
				if (!$template_result)
				{
					if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
					echo "template failed 4<br>";
					return false;
				}
				elseif ($template_result->RecordCount() ==1)
				{
					$show_template = $template_result->FetchNextObject();
					$checkbox_template = stripslashes($show_template->TEMPLATE_CODE);
				}
				else
				{
					if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
					return false;
				}

				$counter = 1;
				while ($show_special = $special_result->FetchNextObject())
				{
					if($this->configuration_data['checkbox_columns'] > 1)
					{
						if($counter == $this->configuration_data['checkbox_columns'])
						{
							$start = "";
							$end = "</tr>";
							$counter = 1;
						}
						elseif($counter == 1)
						{
							$start = "<tr>";
							$end = "";
							$counter++;
						}
						else
						{
							$start = "";
							$end = "";
							$counter++;
						}

						$current_line = $start;
						$current_line .= str_replace("<<EXTRA_CHECKBOX_NAME>>", stripslashes(urldecode($show_special->NAME)), $checkbox_template);
						$current_line .= $end;
					}
					else
					{
						$current_line = str_replace("<<EXTRA_CHECKBOX_NAME>>", stripslashes(urldecode($show_special->NAME)),$checkbox_template);
					}

					$question_block .= $current_line;
				}

				if($counter != $this->configuration_data['checkbox_columns'])
				{
					$question_block .= "</tr>";
				}
				//$this->body .="</table>\n\t</td>\n</tr>\n";
			}
			return $question_block;
		}
		else
			return false;
	} //end of function get_ads_extra_checkboxs

//#################################################################################

	function classified_exists ($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$this->sql_query = "select id from ".$this->classifieds_table." where id = ".$classified_id;
			$result = $db->Execute($this->sql_query);

			if (!$result)
			{
				$this->error_message = stripslashes(urldecode($this->messages[81]));
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				return true;
			}
			else
			{
				$this->error_message = stripslashes(urldecode($this->messages[81]));
				return false;
			}
		}
		else
		{
			//no auction id to check
			$this->error_message = stripslashes(urldecode($this->messages[81]));
			return false;
		}
	 } //end of function classified_exists

//####################################################################################

	function browse_error ($db)
	{
		//this->error_message is the class variable that will contain the error message
		$this->page_id = 1;
		$this->get_text($db);
		$this->body .="<table cellpadding=5 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr class=display_ad_label>\n\t<td>".stripslashes(urldecode($this->messages[600]))."</td>\n</tr>\n";
		$this->body .="<tr class=display_ad_label>\n\t<td>".stripslashes(urldecode($this->messages[638]))."</td>\n</tr>\n";
		if($this->error_message)
			$this->body .="<tr class=error_messages>\n\t<td>".stripslashes(urldecode($this->error_message))."</td>\n</tr>\n";
		else
			$this->body .="<tr class=error_messages>\n\t<td>".stripslashes(urldecode($this->messages[64]))."</td>\n</tr>\n";
		$this->body .="</table>\n";
		$this->display_page($db);
		exit;
	 } //end of function browse_error

//####################################################################################

	function get_filter_value($db,$association=0)
	{
		if ($association)
		{
			//association is the filter level this value is associated with
			$this->sql_query = "select count(distinct(filter_level)) as level_count from ".$this->filters_table;
			$level_count_result = $db->Execute($this->sql_query);
			if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
			if (!$level_count_result)
			{
				if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
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
					if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
					if (!$filter_result)
					{
						if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
						$this->error_message = $this->messages[3501];
						return false;
					}
					elseif ($filter_result->RecordCount() == 1)
					{
						$show_filter_name = $filter_result->FetchNextObject();
						return $show_filter_name->FILTER_NAME;
					}
					else
					{
						if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
						return false;
					}

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
	}

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
				if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
				if (!$filter_result)
				{
					if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
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

	function format_phone_data($phone_number=0)
	{
		if ($phone_number)
		{
			$PhoneNumber = ereg_replace( "[^0-9]", "", $phone_number ); // Strip out non-numerics
			if( ereg( "^([0-9]{3})([0-9]{3})([0-9]{4})$", $PhoneNumber, $NumberParts ) )
                			return "(" . $NumberParts[1] . ") " . $NumberParts[2] . "-" . $NumberParts[3];
        			else
        		       		return $phone_number;
		}
		else
			return $phone_number;
	} //end of function format_phone_data

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end of class Display_ad

?>