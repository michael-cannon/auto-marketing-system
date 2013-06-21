<? //browse_display_ad_print_friendly.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Display_ad_print_friendly extends Site {
	var $subcategory_array = array();
	var $notify_data = array();

//########################################################################

	function Display_ad_print_friendly($db,$classified_user_id,$language_id,$category_id=0,$page=0,$classified_id=0,$affiliate_id=0,$product_configuration=0)
	{
		if ($limit)
			$this->browse_limit = $limit;
		$this->Site($db,1,$language_id,$classified_user_id,$product_configuration);
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
		$this->affiliate_id = $affiliate_id;
	} //end of function Display_ad

//###########################################################

	function display_classified($db,$id=0)
	{
		$this->page_id = 69;
		$this->get_text($db);
		if ($id)
		{
			$show = $this->get_classified_data($db, $id);
			if ($show)
			{
				if ($show->LIVE == 1)
				{
					$template_category = $show->CATEGORY;
					do {
						//get category specific template (if any)
						$this->sql_query = "select ".$this->categories_table.".parent_id,
							".$this->categories_languages_table.".ad_detail_print_friendly_template,
							".$this->categories_languages_table.".auction_detail_print_friendly_template
							from ".$this->categories_table.", ".$this->categories_languages_table." where
							".$this->categories_languages_table.".category_id = ".$template_category." and
							".$this->categories_table.".category_id = ".$template_category." and ".$this->categories_languages_table.".language_id = ".$this->language_id;

						//echo $this->sql_query."<bR>\n";
						$detail_template_result = $db->Execute($this->sql_query);
						if (!$detail_template_result)
						{
							//echo $this->sql_query." is the query<br>\n";
							$this->error_message = urldecode($this->messages[81]);
							return false;
						}
						elseif ($detail_template_result->RecordCount() == 1)
						{
							$show_detail_template = $detail_template_result->FetchNextObject();
							if($show->ITEM_TYPE == 1)
								$template_id = $show_detail_template->AD_DETAIL_PRINT_FRIENDLY_TEMPLATE;
							elseif($show->ITEM_TYPE == 2)
								$template_id = $show_detail_template->AUCTION_DETAIL_PRINT_FRIENDLY_TEMPLATE;
							$template_category = $show_detail_template->PARENT_ID;
						}
						else
						{
							return false;
						}
					} while (($template_category) && (!$template_id));

					if (!$template_id)
					{
						if($show->ITEM_TYPE == 1)
							$template_id = $this->ad_configuration_data->AD_DETAIL_PRINT_FRIENDLY_TEMPLATE;
						elseif($show->ITEM_TYPE == 2)
							$template_id = $this->ad_configuration_data->AUCTION_DETAIL_PRINT_FRIENDLY_TEMPLATE;
					}

					//get template
					$this->sql_query = "select * from ".$this->templates_table." where template_id = ".$template_id;
					$template_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$template_result)
					{
						echo "template failed 4<br>";
						return false;
					}
					elseif ($template_result->RecordCount() ==1)
					{
						$show_template = $template_result->FetchNextObject();
						$template = stripslashes($show_template->TEMPLATE_CODE);
					}
					else
						return false;

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
					//echo $this->sql_query."<bR>\n";
					$next_id_result = $db->Execute($this->sql_query);
					if (!$next_id_result)
					{
						$this->body .=$this->sql_query." is the query<br>\n";
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}
					elseif ($next_id_result->RecordCount() > 1)
					{
						while ($next_id = $next_id_result->FetchNextObject())
						{
							if ($next_id->ID == $id)
							{
								$next_id = $next_id_result->FetchNextObject();
								$next_item_id = $next_id->ID;
								break;
							}
							$last_item_id = $next_id->ID;
						}
					}
					//echo $last_classified_id." is last<Br>\n";
					//echo $next_classified_id." is next<br>\n";

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

							//echo "using site defaults<br>\n";
						}
						else
						{
							$this->browsing_configuration = $this->category_configuration;
							//echo "using category specific settings<br>\n";
						}
					}
					else
					{
						//echo "no category<br>\n";
						$this->browsing_configuration = $this->ad_configuration_data;
					}

					if($this->is_class_auctions() || $this->is_auctions())
					{
						if($this->configuration_data['black_list_of_buyers'] && $this->configuration_data['invited_list_of_buyers'])
						{
							//check blacklist and invited list
							$invited = $this->check_invitedlist($db,$show->SELLER,$this->auction_user_id);
							$banned = $this->check_blacklist($db,$show->SELLER,$this->auction_user_id);
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
							if ($this->check_invitedlist($db,$show->SELLER,$this->auction_user_id))
							{
								if ($this->debug_display_auction)
									echo "this buyer is on the invited list 2<br>";
								//this user is on the invited list
								$can_bid = 1;
							}
						}
						elseif ($this->configuration_data['black_list_of_buyers'])
						{
							//check black list only
							if ($this->debug_display_auction)
								echo "checking only black list of buyers<BR>\n";
							if ($this->check_blacklist($db,$show->SELLER,$this->auction_user_id))
							{
								if ($this->debug_display_auction)
									echo "this buyer is on the blacklist 2<br>";
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
							$can_bid = 1;
						}
					}

					$this->body .="<table cellpadding=3 cellspacing=0 border=1 width=100%>\n";

					$category_tree = $this->get_category_tree($db,$show->CATEGORY);
					reset ($this->category_tree_array);

					if ($category_tree)
					{
						//category tree
						$category_tree_fields = "".urldecode($this->messages[1101])." ". urldecode($this->messages[1100])." > ";
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
									$category_tree_fields .= $this->category_tree_array[$i]["category_name"];
								else
									$category_tree_fields .= $this->category_tree_array[$i]["category_name"]." > ";
							}
						}
						else
						{
							$category_tree_fields = $category_tree;
						}
						$template = str_replace("<<CATEGORY_TREE>>",$category_tree_fields,$template);
					}

					$start_date = date(trim($this->configuration_data['entry_date_configuration']),$show->DATE);
					$end_date = date(trim($this->configuration_data['entry_date_configuration']), $show->ENDS);
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
						elseif ($seller_group_result->RecordCount() ==1)
						{
							$show_group = $seller_group_result->FetchRow();
							$this->sql_query = "select sponsored_by_code from ".$this->groups_table." where group_id = ".$show_group['group_id'];
							$sponsored_by_result = $db->Execute($this->sql_query);
							if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
							if (!$sponsored_by_result)
							{
								if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
								return false;
							}
							elseif ($sponsored_by_result->RecordCount() == 1)
							{
								$sponsored_by_html = $sponsored_by_result->FetchRow();
								if (strlen(trim($sponsored_by_html['sponsored_by_code'])) > 0)
									$template = str_replace("<<SPONSORED_BY>>",stripslashes($sponsored_by_html['sponsored_by_code']),$template);
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
					$template = str_replace("<<IMAGE_BLOCK>>",$image_block,$template);

   					$salt = rand(1,10000);
                    $this->get_image_data($db,$id=0,$large=0);
                    $image_big_black =  "<img src=imagewm.php?image=".$this->images_to_display[1]["id"]."&st=".$salt." width=".$image_width." height=".$image_height." border=0><center><p><font class=display_ad_value>".$this->images_to_display[1]["image_text"]."</font></p></center>";
                    $template = str_replace("<<IMAGE_BIG_BLOCK>>", $image_big_black, $template);



					if (($this->ad_configuration_data->LEAD_PICTURE_WIDTH) && ($this->ad_configuration_data->LEAD_PICTURE_HEIGHT))
					{
						if ($this->images_to_display[1]["type"])
						{
							//an image is within the first (lead) image slot
							if ($this->debug_ad_display)
							{
								echo $this->ad_configuration_data->LEAD_PICTURE_WIDTH." is the lead image width<bR>\n";
								echo $this->ad_configuration_data->LEAD_PICTURE_HEIGHT." is the lead image height<bR>\n";
								echo $this->images_to_display[1]["type"]." is the lead image type<br>\n";
								echo $this->images_to_display[1]["thumb_url"]." is the thumb url<Br>\n";
								echo $this->images_to_display[1]["url"]." is the url<Br>\n";
								echo $this->images_to_display[1]["original_image_width"]." is original_image_width<br>\n";
								echo $this->images_to_display[1]["original_image_height"]." is original_image_height<br>\n";
								echo $this->images_to_display[1]["image_width"]." is image_width<br>\n";
								echo $this->images_to_display[1]["image_height"]." is image_height<br>\n";
								echo $this->images_to_display[1]["url"]." is the url<br>\n";
								echo $this->images_to_display[1]["thumb_url"]." is the thumb_url<br>\n";
								echo $this->images_to_display[1]["image_height"]." is image_height2<Br>\n";
							}

							if ($this->debug_ad_display) echo $lead_image_tag." is the image tag at the top<bR>\n";
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
										if 	($this->images_to_display[1]["type"] == 2)
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
											if ($this->debug_ad_display)
											echo "width of thumbnail larger than lead pic dimensions<br>\n";
											$imageprop = ($this->ad_configuration_data->LEAD_PICTURE_WIDTH * 100) / $this->images_to_display[1]["image_width"];
											$imagevsize = ($this->images_to_display[1]["image_height"] * $imageprop) / 100 ;
											$lead_image_width = $this->ad_configuration_data->LEAD_PICTURE_WIDTH;
											$lead_image_height = ceil($imagevsize);
										}
										elseif ($this->images_to_display[1]["image_height"] > $this->ad_configuration_data->LEAD_PICTURE_HEIGHT)
										{
											if ($this->debug_ad_display)
											echo "height of thumbnail larger than lead pic dimensions<br>\n";
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
											echo $lead_image_width." is the resized thumbnail width<Br>";
											echo $lead_image_height." is the resized thumbnail height<Br>";
										}
										if 	($this->images_to_display[1]["type"] == 2)
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

										if ($this->debug_display_auction)
										{
											echo $lead_image_width." is the lead_image_width for the original image where original width is: ".$this->images_to_display[1]["original_image_width"]."<bR>\n";
											echo $lead_image_height." is the lead_image_height for the original image where original height is: ".$this->images_to_display[1]["original_image_height"]."<bR>\n";
										}
										if 	($this->images_to_display[1]["type"] == 2)
										$lead_image_tag .=  "<img src=get_image.php?image=".$this->images_to_display[1]["id"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";
										else
										$lead_image_tag .=  "<img src=".$this->images_to_display[1]["url"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";

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
										if 	($this->images_to_display[1]["type"] == 2)
										$lead_image_tag .=  "<img src=get_image.php?image=".$this->images_to_display[1]["id"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";
										else
										$lead_image_tag .=  "<img src=".$this->images_to_display[1]["url"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";
									}
								}
							}

							//$lead_image_tag .= "</a>";
							if ($this->debug_ad_display) echo $lead_image_tag." is the lead image tag<br>\n";
							$template = str_replace("<<LEAD_PICTURE>>",$lead_image_tag,$template);
						}
						else
						{
							if ($this->debug_ad_display) echo "there is no image to display in the lead image spot<br>\n";
							$template = str_replace("<<LEAD_PICTURE>>","",$template);
						}
					} //end of if lead picture

					//get ads extra questions
					$extra_question_block = $this->get_ads_extra_values($db,$id,$show->CATEGORY);

					//get ads extra checkboxs
					$extra_checkbox_block = $this->get_ads_extra_checkboxs($db,$id,$show->CATEGORY);

					$template = str_replace("<<EXTRA_QUESTION_BLOCK>>",$extra_question_block,$template);
					$template = str_replace("<<EXTRA_CHECKBOX_BLOCK>>",$extra_checkbox_block,$template);

					if ($show->SOLD_DISPLAYED)
						$title = "<img src=".$this->configuration_data['sold_image']." border=0> ".stripslashes(urldecode($show->TITLE));
					else
						$title = stripslashes(urldecode($show->TITLE));

					$template = str_replace("<<TITLE>>",$title,$template);
                    $template = str_replace("<MODULE_TITLE>", $title, $template);

/*					if (((($this->browsing_configuration->USE_MAPPING_ADDRESS_FIELD) && (strlen(trim($show->MAPPING_ADDRESS)) > 0))
						|| (($this->browsing_configuration->USE_MAPPING_ZIP_FIELD) && (strlen(trim($show->MAPPING_ZIP)) > 0)))
						&& ($this->browsing_configuration->USE_MAPPING_STATE_FIELD) && (strlen(trim($show->MAPPING_STATE)) > 0)
						&& ($this->browsing_configuration->USE_MAPPING_CITY_FIELD) && (strlen(trim($show->MAPPING_CITY)) > 0))
					{
						//build mapquest link
						$mapquest_link = "<a href=\"javascript:winimage('http://www.mapquest.com/maps/map.adp?homesubmit=Get+Map";
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
						$template = str_replace("<<MAPPING_LINK>>","",$template);*/

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
						$time_left .= $weeks." ".urldecode($this->messages[103327]).", ".$days." ".urldecode($this->messages[103328]);
					}
					elseif ($days > 0)
					{
						$time_left .= $days." ".urldecode($this->messages[103328]).", ".$hours." ".urldecode($this->messages[103329]);
					}
					elseif ($hours > 0)
					{
						$time_left .= $hours." ".urldecode($this->messages[103329]).", ".$minutes." ".urldecode($this->messages[103323]);
					}
					elseif ($minutes > 0)
					{
						$time_left .= $minutes." ".urldecode($this->messages[103323]).", ".$seconds." ".urldecode($this->messages[103324]);
					}
					elseif ($seconds > 0)
					{
						$time_left .= $seconds." ".urldecode($this->messages[103324]);
					}
					$template = str_replace("<<TIME_REMAINING_LABEL>>", urldecode($this->messages[103325]),$template);
					$template = str_replace("<<TIME_REMAINING>>", $time_left, $template);

					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_1)
					{
						$template = str_replace("<<OPTIONAL_FIELD_1_LABEL>>",urldecode($this->messages[1090]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_1>>",stripslashes(urldecode($show->OPTIONAL_FIELD_1)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_2)
					{
						$template = str_replace("<<OPTIONAL_FIELD_2_LABEL>>",urldecode($this->messages[1091]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_2>>",stripslashes(urldecode($show->OPTIONAL_FIELD_2)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_3)
					{
						$template = str_replace("<<OPTIONAL_FIELD_3_LABEL>>",urldecode($this->messages[1092]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_3>>",stripslashes(urldecode($show->OPTIONAL_FIELD_3)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_4)
					{
						$template = str_replace("<<OPTIONAL_FIELD_4_LABEL>>",urldecode($this->messages[1093]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_4>>",stripslashes(urldecode($show->OPTIONAL_FIELD_4)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_5)
					{
						$template = str_replace("<<OPTIONAL_FIELD_5_LABEL>>",urldecode($this->messages[1094]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_5>>",stripslashes(urldecode($show->OPTIONAL_FIELD_5)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_6)
					{
						$template = str_replace("<<OPTIONAL_FIELD_6_LABEL>>",urldecode($this->messages[1095]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_6>>",stripslashes(urldecode($show->OPTIONAL_FIELD_6)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_7)
					{
						$template = str_replace("<<OPTIONAL_FIELD_7_LABEL>>",urldecode($this->messages[1096]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_7>>",stripslashes(urldecode($show->OPTIONAL_FIELD_7)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_8)
					{
						$template = str_replace("<<OPTIONAL_FIELD_8_LABEL>>",urldecode($this->messages[1097]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_8>>",stripslashes(urldecode($show->OPTIONAL_FIELD_8)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_9)
					{
						$template = str_replace("<<OPTIONAL_FIELD_9_LABEL>>",urldecode($this->messages[1098]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_9>>",stripslashes(urldecode($show->OPTIONAL_FIELD_9)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_10)
					{
						$template = str_replace("<<OPTIONAL_FIELD_10_LABEL>>",urldecode($this->messages[1099]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_10>>",stripslashes(urldecode($show->OPTIONAL_FIELD_10)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_11)
					{
						$template = str_replace("<<OPTIONAL_FIELD_11_LABEL>>",urldecode($this->messages[1706]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_11>>",stripslashes(urldecode($show->OPTIONAL_FIELD_11)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_12)
					{
						$template = str_replace("<<OPTIONAL_FIELD_12_LABEL>>",urldecode($this->messages[1707]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_12>>",stripslashes(urldecode($show->OPTIONAL_FIELD_12)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_13)
					{
						$template = str_replace("<<OPTIONAL_FIELD_13_LABEL>>",urldecode($this->messages[1708]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_13>>",stripslashes(urldecode($show->OPTIONAL_FIELD_13)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_14)
					{
						$template = str_replace("<<OPTIONAL_FIELD_14_LABEL>>",urldecode($this->messages[1709]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_14>>",stripslashes(urldecode($show->OPTIONAL_FIELD_14)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_15)
					{
						$template = str_replace("<<OPTIONAL_FIELD_15_LABEL>>",urldecode($this->messages[1710]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_15>>",stripslashes(urldecode($show->OPTIONAL_FIELD_15)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_16)
					{
						$template = str_replace("<<OPTIONAL_FIELD_16_LABEL>>",urldecode($this->messages[1711]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_16>>",stripslashes(urldecode($show->OPTIONAL_FIELD_16)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_17)
					{
						$template = str_replace("<<OPTIONAL_FIELD_17_LABEL>>",urldecode($this->messages[1712]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_17>>",stripslashes(urldecode($show->OPTIONAL_FIELD_17)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_18)
					{
						$template = str_replace("<<OPTIONAL_FIELD_18_LABEL>>",urldecode($this->messages[1713]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_18>>",stripslashes(urldecode($show->OPTIONAL_FIELD_18)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_19)
					{
						$template = str_replace("<<OPTIONAL_FIELD_19_LABEL>>",urldecode($this->messages[1714]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_19>>",stripslashes(urldecode($show->OPTIONAL_FIELD_19)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_20)
					{
						$template = str_replace("<<OPTIONAL_FIELD_20_LABEL>>",urldecode($this->messages[1715]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_20>>",stripslashes(urldecode($show->OPTIONAL_FIELD_20)),$template);
					}

					if (($this->browsing_configuration->PUBLICALLY_EXPOSE_EMAIL) && ($show->EXPOSE_EMAIL))
					{
						$template = str_replace("<<PUBLIC_EMAIL_LABEL>>",stripslashes(urldecode($this->messages[1474])),$template);
						$template = str_replace("<<PUBLIC_EMAIL>>",stripslashes(urldecode($show->EMAIL)),$template);
					}
					else
					{
						$template = str_replace("<<PUBLIC_EMAIL_LABEL>>","",$template);
						$template = str_replace("<<PUBLIC_EMAIL>>","",$template);
					}

					if (($this->browsing_configuration->USE_PHONE_1_OPTION_FIELD) && (strlen($show->PHONE) > 0))
					{
						$template = str_replace("<<PHONE_LABEL>>",stripslashes(urldecode($this->messages[1475])),$template);
						$template = str_replace("<<PHONE_DATA>>",stripslashes(urldecode($show->PHONE)),$template);
					}
					else
					{
						$template = str_replace("<<PHONE_LABEL>>","",$template);
						$template = str_replace("<<PHONE_DATA>>","",$template);
					}

					if (($this->browsing_configuration->USE_PHONE_2_OPTION_FIELD) && (strlen($show->PHONE2) > 0))
					{
						$template = str_replace("<<PHONE2_LABEL>>",stripslashes(urldecode($this->messages[1476])),$template);
						$template = str_replace("<<PHONE2_DATA>>",stripslashes(urldecode($show->PHONE2)),$template);
					}
					else
					{
						$template = str_replace("<<PHONE2_LABEL>>","",$template);
						$template = str_replace("<<PHONE2_DATA>>","",$template);
					}

					if (($this->browsing_configuration->USE_FAX_FIELD_OPTION) && (strlen($show->FAX) > 0))
					{
						$template = str_replace("<<FAX_LABEL>>",stripslashes(urldecode($this->messages[1477])),$template);
						$template = str_replace("<<FAX_DATA>>",stripslashes(urldecode($show->FAX)),$template);
					}
					else
					{
						$template = str_replace("<<FAX_LABEL>>","",$template);
						$template = str_replace("<<FAX_DATA>>","",$template);
					}

					if ($this->browsing_configuration->USE_URL_LINK_1)
					{
						if (strlen(trim($show->URL_LINK_1)) > 0)
						{
							if (stristr(stripslashes(urldecode($show->URL_LINK_1)),"http://"))
								$url_link_1 = stripslashes(urldecode($this->messages[2440]));
							else
								$url_link_1 = stripslashes(urldecode($this->messages[2440]));
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
								$url_link_2 = stripslashes(urldecode($this->messages[2441]));
							else
								$url_link_2 = stripslashes(urldecode($this->messages[2441]));

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
								$url_link_3 = stripslashes(urldecode($this->messages[2442]));
							else
								$url_link_3 = stripslashes(urldecode($this->messages[2442]));

							$template = str_replace("<<URL_LINK_3>>",$url_link_3,$template);
						}
						else
							$template = str_replace("<<URL_LINK_3>>","",$template);
					}

					if ($this->browsing_configuration->USE_CITY_FIELD)
					{
						$template = str_replace("<<CITY_LABEL>>",urldecode($this->messages[1468]),$template);
						$template = str_replace("<<CITY_DATA>>",stripslashes(urldecode($show->LOCATION_CITY)),$template);
					}
					if ($this->browsing_configuration->USE_STATE_FIELD)
					{
						$template = str_replace("<<STATE_LABEL>>",urldecode($this->messages[1478]),$template);
						$template = str_replace("<<STATE_DATA>>",stripslashes(urldecode($show->LOCATION_STATE)),$template);
					}
					if ($this->browsing_configuration->USE_COUNTRY_FIELD)
					{
						$template = str_replace("<<COUNTRY_LABEL>>",urldecode($this->messages[1080]),$template);
						$template = str_replace("<<COUNTRY_DATA>>",stripslashes(urldecode($show->LOCATION_COUNTRY)),$template);
					}
					if ($this->browsing_configuration->USE_ZIP_FIELD)
					{
						$template = str_replace("<<ZIP_LABEL>>",urldecode($this->messages[1479]),$template);
						$template = str_replace("<<ZIP_DATA>>",stripslashes(urldecode($show->LOCATION_ZIP)),$template);
					}


					$template = str_replace("<<CLASSIFIED_ID_LABEL>>",urldecode($this->messages[1082]),$template);
					$template = str_replace("<<CLASSIFIED_ID>>",$show->ID,$template);

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
							$template = str_replace("<<BID_START_DATE_LABEL>>",urldecode($this->messages[103326]), $template);
							$template = str_replace("<<BID_START_DATE>>",$bid_start_date,$template);
						}
						else
						{
							$template = str_replace("<<BID_START_DATE_LABEL>>","", $template);
							$template = str_replace("<<BID_START_DATE>>","",$template);
						}
					}

					$template = str_replace("<<VIEWED_COUNT_LABEL>>",urldecode($this->messages[1084]),$template);
					$template = str_replace("<<VIEWED_COUNT>>",$show->VIEWED,$template);

					$template = str_replace("<<SELLER_LABEL>>",urldecode($this->messages[1078]),$template);
					$template = str_replace("<<SELLER>>",$seller_data->USERNAME,$template);

					$template = str_replace("<<DATE_STARTED_LABEL>>",urldecode($this->messages[1079]),$template);
					$template = str_replace("<<DATE_STARTED>>",$start_date,$template);
					//$template = str_replace("<<LOCATION_LABEL>>",urldecode($this->messages[6]),$template);
					//$template = str_replace("<<LOCATION>>",$location_string,$template);

					$template = str_replace("<<DESCRIPTION_LABEL>>",urldecode($this->messages[1081]),$template);
					$template = str_replace("<<DESCRIPTION>>",stripslashes(urldecode($show->DESCRIPTION)),$template);

					//classauctions details
					if ($show->ITEM_TYPE==1)
					{
						if ($this->browsing_configuration->USE_PRICE_FIELD)
						{
							$template = str_replace("<<PRICE_LABEL>>",stripslashes(urldecode($this->messages[1085])),$template);
							if (((strlen(trim(urldecode($show->PRICE))) > 0)
								|| (strlen(trim(urldecode($show->PRECURRENCY))) > 0)
								|| (strlen(trim(urldecode($show->POSTCURRENCY))) > 0)) && ($show->PRICE != 0))
							{
								if (floor($show->PRICE) == $show->PRICE)
								{
									$template = str_replace("<<PRICE>>",stripslashes(urldecode($show->PRECURRENCY))." ".number_format(stripslashes(urldecode($show->PRICE)))." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
								}
								else
								{
									$template = str_replace("<<PRICE>>",stripslashes(urldecode($show->PRECURRENCY))." ".number_format(stripslashes(urldecode($show->PRICE)),2,".",",")." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
								}
							}
							else
								$template = str_replace("<<PRICE>>",stripslashes(urldecode($show->PRECURRENCY))." - ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
						}
					}

					if($this->is_class_auctions() || $this->is_auctions())
					{
						// Feedback
						// Will display on both the auctions and classifieds
						$template = str_replace("<<SELLER_RATING_LABEL>>",stripslashes(urldecode($this->messages[103092])),$template);

						if ($this->debug_ad_display)
						{
							echo $seller_data->FEEDBACK_COUNT." is the FEEDBACK_COUNT<br>\n";
							echo $seller_data->FEEDBACK_SCORE." is FEEDBACK_SCORE<br>\n";
						}
						if (($seller_data->FEEDBACK_SCORE > 0) && ($seller_data->FEEDBACK_COUNT != 0))
						{
							$this->sql_query = "select filename from ".$this->auctions_feedback_icons_table." where begin <= ".$seller_data->FEEDBACK_SCORE." AND end >= ".$seller_data->FEEDBACK_SCORE;
							if (($this->configuration_data['debug_feedback']) ||  ($this->debug_ad_display))
							{
								$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "auctions_feedback_icons_table", "get the icon filename");
							}
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
							if($this->configuration_data['debug_feedback'])
							{
								$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "auctions_feedback_icons_table", "get the 0 icon filename");
							}
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
							if($this->configuration_data['debug_feedback'])
							{
								$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "auctions_feedback_icons_table", "get the negative icon filename");
							}
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
							$template = str_replace("<<SELLER_RATING>>",stripslashes(urldecode($this->messages[103110])),$template);

						//$template = str_replace("<<FEEDBACK_LINK>>","<a href=".$this->configuration_data['classifieds_file_name']."?a=1030&b=".$id."&d=".$show->SELLER." class=display_auction_value>".stripslashes(urldecode($this->messages[102717]))."</a>",$template);
						$template = str_replace("<<SELLER_NUMBER_RATES_LABEL>>",stripslashes(urldecode($this->messages[103093])),$template);
						$template = str_replace("<<SELLER_NUMBER_RATES>>",$seller_data->FEEDBACK_COUNT,$template);
						//$template = str_replace("<<SELLER_RATING_SCALE_EXPLANATION>>",$this->display_help_link(102826),$template);
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
								$reserve = stripslashes(urldecode($this->messages[103072]));
							}
							else
							{
								//reserve not yer met
								$reserve = stripslashes(urldecode($this->messages[103073]));
							}
						}
						else
							$reserve = "";

						if(!$show->BUY_NOW_ONLY)
						{
							$template = str_replace("<<RESERVE>>",$reserve,$template);
						}
						else
						{
							$template = str_replace("<<RESERVE>>","",$template);
						}

						//$template = str_replace("<<AUCTION_TYPE_HELP>>", $this->display_help_link(103056), $template);

						if ($show->AUCTION_TYPE == 1)
							$type_of_auction = stripslashes(urldecode($this->messages[200130]));
						else
							$type_of_auction = stripslashes(urldecode($this->messages[200131]));
						if ($show->AUCTION_TYPE == 1)
						{
							$template = str_replace("<<WINNING_DUTCH_BIDDERS>>", "",$template);
							$template = str_replace("<<WINNING_DUTCH_BIDDERS_LABEL>>", "",$template);
							$template = str_replace("<<HIGH_BIDDER_LABEL>>",stripslashes(urldecode($this->messages[103112])),$template);
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
										$template = str_replace("<<BUY_NOW_LABEL>>",stripslashes(urldecode($this->messages[200133])),$template);
										if($can_bid)
										{
											$template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
											if ($show->LIVE)
											{
												if ($this->classified_user_id)
													$template = str_replace("<<BUY_NOW_LINK>>",stripslashes(urldecode($this->messages[103078])),$template);
												else
													$template = str_replace("<<BUY_NOW_LINK>>",stripslashes(urldecode($this->messages[103078])),$template);
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
									elseif(($this->configuration_data['buy_now_reserve'] == 0) && ($show->CURRENT_BID == 0.00))
									{
										//Check for black list and invited list of buyers
										//Show only when not in blacklist but in invited list
										$template = str_replace("<<BUY_NOW_LABEL>>",stripslashes(urldecode($this->messages[200133])),$template);
										if($can_bid)
										{
											$template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
											if ($show->LIVE)
											{
												if ($this->classified_user_id)
													$template = str_replace("<<BUY_NOW_LINK>>",stripslashes(urldecode($this->messages[103078])),$template);
												else
													$template = str_replace("<<BUY_NOW_LINK>>",stripslashes(urldecode($this->messages[103078])),$template);
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
									//Check for black list and invited list of buyers
									//Show only when not in blacklist but in invited list
									if($can_bid)
									{
										$template = str_replace("<<BUY_NOW_LABEL>>",stripslashes(urldecode($this->messages[200133])),$template);
										$template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
										if ($this->classified_user_id)
											$template = str_replace("<<BUY_NOW_LINK>>",stripslashes(urldecode($this->messages[103078])),$template);
										else
											$template = str_replace("<<BUY_NOW_LINK>>",stripslashes(urldecode($this->messages[103078])),$template);
									}
									else
									{
										$template = str_replace("<<BUY_NOW_LABEL>>",stripslashes(urldecode($this->messages[200133])),$template);
										$template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".number_format($show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
										$template = str_replace("<<BUY_NOW_LINK>>","",$template);
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
								$dutch_bidders_table .= "<tr class=current_dutch_bidders_header><td>".stripslashes(urldecode($this->messages[103366]))."</td>\n\t";
								$dutch_bidders_table .= "<td>".stripslashes(urldecode($this->messages[103079]))."</td>\n\t";
								$dutch_bidders_table .= "<td>".stripslashes(urldecode($this->messages[103080]))."</td>\n</tr>\n";

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
								$template = str_replace("<<WINNING_DUTCH_BIDDERS_LABEL>>",stripslashes(urldecode($this->messages[103081])),$template);
							}
							else
							{
								$template = str_replace("<<WINNING_DUTCH_BIDDERS>>",stripslashes(urldecode($this->messages[103366])),$template);
								$template = str_replace("<<WINNING_DUTCH_BIDDERS_LABEL>>",stripslashes(urldecode($this->messages[103082])),$template);
							}

							$template = str_replace("<<HIGH_BIDDER_LABEL>>",stripslashes(urldecode($this->messages[103112])),$template);
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
							//$template = str_replace("<<HIGH_BIDDER>>",stripslashes(urldecode($this->messages[200132])),$template);
						}

						$template = str_replace("<<NUM_BIDS_LABEL>>",stripslashes(urldecode($this->messages[103083])),$template);
						$template = str_replace("<<QUANTITY_LABEL>>",stripslashes(urldecode($this->messages[103084])),$template);
						$template = str_replace("<<QUANTITY>>",$show->QUANTITY,$template);
						//$template = str_replace("<<AUCTION_ID_LABEL>>",stripslashes(urldecode($this->messages[100008])),$template);
						//$template = str_replace("<<AUCTION_ID>>",$show->ID,$template);

/*						if($this->configuration_data['bid_history_link_live'] == 1 && !$show->BUY_NOW_ONLY)
						{
							if ($this->debug_ad_display) echo "BID_HISTORY_LINK replace 1<BR>\n";
							$template = str_replace("<<BID_HISTORY_LINK>>",stripslashes(urldecode($this->messages[102706])),$template);
						}*/
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
							$template = str_replace("<<PAYMENT_OPTIONS_LABEL>>",stripslashes(urldecode($this->messages[103086])),$template);
							$template = str_replace("<<PAYMENT_OPTIONS>>",$payment_options,$template);
						}
						else
						{
							$template = str_replace("<<PAYMENT_OPTIONS_LABEL>>","",$template);
							$template = str_replace("<<PAYMENT_OPTIONS>>","",$template);
						}
						$template = str_replace("<<AUCTION_TYPE_LABEL>>",stripslashes(urldecode($this->messages[103087])),$template);
						$template = str_replace("<<AUCTION_TYPE_DATA>>",$type_of_auction,$template);
						$template = str_replace("<<DATE_ENDED>>",$end_date,$template);

						$member_since = date(trim($this->configuration_data['entry_date_configuration']), $seller_data->DATE_JOINED);
						$template = str_replace("<<MEMBER_SINCE>>", $member_since, $template);

						if ($show->LIVE == 1)
						{
							$template = str_replace("<<DATE_ENDED_LABEL>>",stripslashes(urldecode($this->messages[103088])),$template);
						}
						else
						{
							$template = str_replace("<<DATE_ENDED_LABEL>>",stripslashes(urldecode($this->messages[103088])),$template);
						}

						if(!$show->BUY_NOW_ONLY)
						{
							$template = str_replace("<<MINIMUM_LABEL>>",stripslashes(urldecode($this->messages[103089])),$template);
							$template = str_replace("<<MINIMUM_BID>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $minimum_bid)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);

							if($this->classified_user_id == $show->SELLER)
							{
								$template = str_replace("<<RESERVE_LABEL>>",stripslashes(urldecode($this->messages[103090])),$template);
								$template = str_replace("<<RESERVE_BID>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->RESERVE_PRICE)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
							}
							$template = str_replace("<<STARTING_LABEL>>",stripslashes(urldecode($this->messages[103091])),$template);
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
							if($can_bid)
							{
								if ($this->classified_user_id)
									$make_bid_link = stripslashes(urldecode($this->messages[103095]));
								else
									$make_bid_link = stripslashes(urldecode($this->messages[103095]));

									if(!$show->BUY_NOW_ONLY)
									{
										$template = str_replace("<<MAKE_BID_LINK>>",$make_bid_link,$template);
									}
									else
									{
										$template = str_replace("<<MAKE_BID_LINK>>","",$template);
									}
							}
							elseif (!$this->classified_user_id)
							{
								$make_bid_link = stripslashes(urldecode($this->messages[103095]));
								$template = str_replace("<<MAKE_BID_LINK>>",$make_bid_link,$template);
							}
							else
							{
								$make_bid_link = stripslashes(urldecode($this->messages[103095]));
								$template = str_replace("<<MAKE_BID_LINK>>",$make_bid_link,$template);
							}

/*							if($this->configuration_data['bid_history_link_live'] == 1)
							{
								if ($this->debug_ad_display) echo "BID_HISTORY_LINK replace 3<BR>\n";
								$template = str_replace("<<BID_HISTORY_LINK>>",stripslashes(urldecode($this->messages[102706])),$template);
							}
							else
							{
								if ($this->debug_ad_display) echo "BID_HISTORY_LINK replace 4<BR>\n";
								$template = str_replace("<<BID_HISTORY_LINK>>","&nbsp;",$template);
							}*/

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
							//$template = str_replace("<<BID_HISTORY_LINK>>","<a href=".$this->configuration_data['classifieds_file_name']."?a=1031&b=".$id." class=bid_history_link>".stripslashes(urldecode($this->messages[102706]))."</a>",$template);
						}
					}

					$template = str_replace("<<SELLER_FIRST_NAME>>",stripslashes(urldecode($seller_data->FIRSTNAME)),$template);
					$template = str_replace("<<SELLER_LAST_NAME>>",stripslashes(urldecode($seller_data->LASTNAME)),$template);
					$template = str_replace("<<SELLER_URL>>",stripslashes(urldecode($seller_data->URL)),$template);
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


					$css = $this->get_css($db);
					if (strlen(trim($css)) > 0)
					{
						//echo "hello css<bR>\n";
						$this->font_stuff = "<style type=\"text/css\">\n<!--\n".$this->font_stuff."-->\n</style>\n";
						$template = str_replace("<<CSSSTYLESHEET>>",$this->font_stuff,$template);
					}
					//echo $template;
					$this->template = $template;
					$this->display_page($db);
					return true;
				}
				elseif($show->LIVE == 0)
                {
                    $template_category = $show->CATEGORY;
                    do {
                        //get category specific template (if any)
                        $this->sql_query = "select ".$this->categories_table.".parent_id,
                            ".$this->categories_languages_table.".ad_detail_print_friendly_template,
                            ".$this->categories_languages_table.".auction_detail_print_friendly_template
                            from ".$this->categories_table.", ".$this->categories_languages_table." where
                            ".$this->categories_languages_table.".category_id = ".$template_category." and
                            ".$this->categories_table.".category_id = ".$template_category." and ".$this->categories_languages_table.".language_id = ".$this->language_id;

                        //echo $this->sql_query."<bR>\n";
                        $detail_template_result = $db->Execute($this->sql_query);
                        if (!$detail_template_result)
                        {
                            //echo $this->sql_query." is the query<br>\n";
                            $this->error_message = urldecode($this->messages[81]);
                            return false;
                        }
                        elseif ($detail_template_result->RecordCount() == 1)
                        {
                            $show_detail_template = $detail_template_result->FetchNextObject();
                            if($show->ITEM_TYPE == 1)
                                $template_id = $show_detail_template->AD_DETAIL_PRINT_FRIENDLY_TEMPLATE;
                            elseif($show->ITEM_TYPE == 2)
                                $template_id = $show_detail_template->AUCTION_DETAIL_PRINT_FRIENDLY_TEMPLATE;
                            $template_category = $show_detail_template->PARENT_ID;
                        }
                        else
                        {
                            return false;
                        }
                    } while (($template_category) && (!$template_id));

                    if (!$template_id)
                    {
                        if($show->ITEM_TYPE == 1)
                            $template_id = $this->ad_configuration_data->AD_DETAIL_PRINT_FRIENDLY_TEMPLATE;
                        elseif($show->ITEM_TYPE == 2)
                            $template_id = $this->ad_configuration_data->AUCTION_DETAIL_PRINT_FRIENDLY_TEMPLATE;
                    }

                    //get template
                    $this->sql_query = "select * from ".$this->templates_table." where template_id = ".$template_id;
                    $template_result = $db->Execute($this->sql_query);
                    //echo $this->sql_query."<br>\n";
                    if (!$template_result)
                    {
                        echo "template failed 4<br>";
                        return false;
                    }
                    elseif ($template_result->RecordCount() ==1)
                    {
                        $show_template = $template_result->FetchNextObject();
                        $template = stripslashes($show_template->TEMPLATE_CODE);
                    }
                    else
                        return false;

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
                    //echo $this->sql_query."<bR>\n";
                    $next_id_result = $db->Execute($this->sql_query);
                    if (!$next_id_result)
                    {
                        $this->body .=$this->sql_query." is the query<br>\n";
                        $this->error_message = urldecode($this->messages[81]);
                        return false;
                    }
                    elseif ($next_id_result->RecordCount() > 1)
                    {
                        while ($next_id = $next_id_result->FetchNextObject())
                        {
                            if ($next_id->ID == $id)
                            {
                                $next_id = $next_id_result->FetchNextObject();
                                $next_item_id = $next_id->ID;
                                break;
                            }
                            $last_item_id = $next_id->ID;
                        }
                    }
                    //echo $last_classified_id." is last<Br>\n";
                    //echo $next_classified_id." is next<br>\n";

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

                            //echo "using site defaults<br>\n";
                        }
                        else
                        {
                            $this->browsing_configuration = $this->category_configuration;
                            //echo "using category specific settings<br>\n";
                        }
                    }
                    else
                    {
                        //echo "no category<br>\n";
                        $this->browsing_configuration = $this->ad_configuration_data;
                    }

                    if($this->is_class_auctions() || $this->is_auctions())
                    {
                        if($this->configuration_data['black_list_of_buyers'] && $this->configuration_data['invited_list_of_buyers'])
                        {
                            //check blacklist and invited list
                            $invited = $this->check_invitedlist($db,$show->SELLER,$this->auction_user_id);
                            $banned = $this->check_blacklist($db,$show->SELLER,$this->auction_user_id);
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
                            if ($this->check_invitedlist($db,$show->SELLER,$this->auction_user_id))
                            {
                                if ($this->debug_display_auction)
                                    echo "this buyer is on the invited list 2<br>";
                                //this user is on the invited list
                                $can_bid = 1;
                            }
                        }
                        elseif ($this->configuration_data['black_list_of_buyers'])
                        {
                            //check black list only
                            if ($this->debug_display_auction)
                                echo "checking only black list of buyers<BR>\n";
                            if ($this->check_blacklist($db,$show->SELLER,$this->auction_user_id))
                            {
                                if ($this->debug_display_auction)
                                    echo "this buyer is on the blacklist 2<br>";
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
                            $can_bid = 1;
                        }
                    }

                    $this->body .="<table cellpadding=3 cellspacing=0 border=1 width=100%>\n";

                    $category_tree = $this->get_category_tree($db,$show->CATEGORY);
                    reset ($this->category_tree_array);

                    if ($category_tree)
                    {
                        //category tree
                        $category_tree_fields = "".urldecode($this->messages[1101])." ". urldecode($this->messages[1100])." > ";
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
                                    $category_tree_fields .= $this->category_tree_array[$i]["category_name"];
                                else
                                    $category_tree_fields .= $this->category_tree_array[$i]["category_name"]." > ";
                            }
                        }
                        else
                        {
                            $category_tree_fields = $category_tree;
                        }
                        $template = str_replace("<<CATEGORY_TREE>>",$category_tree_fields,$template);
                    }

                    $start_date = date(trim($this->configuration_data['entry_date_configuration']),$show->DATE);
                    $end_date = date(trim($this->configuration_data['entry_date_configuration']), $show->ENDS);
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
                        elseif ($seller_group_result->RecordCount() ==1)
                        {
                            $show_group = $seller_group_result->FetchRow();
                            $this->sql_query = "select sponsored_by_code from ".$this->groups_table." where group_id = ".$show_group['group_id'];
                            $sponsored_by_result = $db->Execute($this->sql_query);
                            if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
                            if (!$sponsored_by_result)
                            {
                                if ($this->debug_ad_display) echo $this->sql_query."<bR>\n";
                                return false;
                            }
                            elseif ($sponsored_by_result->RecordCount() == 1)
                            {
                                $sponsored_by_html = $sponsored_by_result->FetchRow();
                                if (strlen(trim($sponsored_by_html['sponsored_by_code'])) > 0)
                                    $template = str_replace("<<SPONSORED_BY>>",stripslashes($sponsored_by_html['sponsored_by_code']),$template);
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
                    $template = str_replace("<<IMAGE_BLOCK>>",$image_block,$template);

                    $salt = rand(1,10000);
                    $this->get_image_data($db,$id=0,$large=0);
                    $image_big_black =  "<img src=imagewm.php?image=".$this->images_to_display[1]["id"]."&st=".$salt." width=".$image_width." height=".$image_height." border=0><center><p><font class=display_ad_value>".$this->images_to_display[1]["image_text"]."</font></p></center>";
                    $template = str_replace("<<IMAGE_BIG_BLOCK>>", $image_big_black, $template);

                    if (($this->ad_configuration_data->LEAD_PICTURE_WIDTH) && ($this->ad_configuration_data->LEAD_PICTURE_HEIGHT))
                    {
                        if ($this->images_to_display[1]["type"])
                        {
                            //an image is within the first (lead) image slot
                            if ($this->debug_ad_display)
                            {
                                echo $this->ad_configuration_data->LEAD_PICTURE_WIDTH." is the lead image width<bR>\n";
                                echo $this->ad_configuration_data->LEAD_PICTURE_HEIGHT." is the lead image height<bR>\n";
                                echo $this->images_to_display[1]["type"]." is the lead image type<br>\n";
                                echo $this->images_to_display[1]["thumb_url"]." is the thumb url<Br>\n";
                                echo $this->images_to_display[1]["url"]." is the url<Br>\n";
                                echo $this->images_to_display[1]["original_image_width"]." is original_image_width<br>\n";
                                echo $this->images_to_display[1]["original_image_height"]." is original_image_height<br>\n";
                                echo $this->images_to_display[1]["image_width"]." is image_width<br>\n";
                                echo $this->images_to_display[1]["image_height"]." is image_height<br>\n";
                                echo $this->images_to_display[1]["url"]." is the url<br>\n";
                                echo $this->images_to_display[1]["thumb_url"]." is the thumb_url<br>\n";
                                echo $this->images_to_display[1]["image_height"]." is image_height2<Br>\n";
                            }

                            if ($this->debug_ad_display) echo $lead_image_tag." is the image tag at the top<bR>\n";
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
                                        if     ($this->images_to_display[1]["type"] == 2)
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
                                            if ($this->debug_ad_display)
                                            echo "width of thumbnail larger than lead pic dimensions<br>\n";
                                            $imageprop = ($this->ad_configuration_data->LEAD_PICTURE_WIDTH * 100) / $this->images_to_display[1]["image_width"];
                                            $imagevsize = ($this->images_to_display[1]["image_height"] * $imageprop) / 100 ;
                                            $lead_image_width = $this->ad_configuration_data->LEAD_PICTURE_WIDTH;
                                            $lead_image_height = ceil($imagevsize);
                                        }
                                        elseif ($this->images_to_display[1]["image_height"] > $this->ad_configuration_data->LEAD_PICTURE_HEIGHT)
                                        {
                                            if ($this->debug_ad_display)
                                            echo "height of thumbnail larger than lead pic dimensions<br>\n";
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
                                            echo $lead_image_width." is the resized thumbnail width<Br>";
                                            echo $lead_image_height." is the resized thumbnail height<Br>";
                                        }
                                        if     ($this->images_to_display[1]["type"] == 2)
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

                                        if ($this->debug_display_auction)
                                        {
                                            echo $lead_image_width." is the lead_image_width for the original image where original width is: ".$this->images_to_display[1]["original_image_width"]."<bR>\n";
                                            echo $lead_image_height." is the lead_image_height for the original image where original height is: ".$this->images_to_display[1]["original_image_height"]."<bR>\n";
                                        }
                                        if     ($this->images_to_display[1]["type"] == 2)
                                        $lead_image_tag .=  "<img src=get_image.php?image=".$this->images_to_display[1]["id"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";
                                        else
                                        $lead_image_tag .=  "<img src=".$this->images_to_display[1]["url"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";

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
                                        if     ($this->images_to_display[1]["type"] == 2)
                                        $lead_image_tag .=  "<img src=get_image.php?image=".$this->images_to_display[1]["id"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";
                                        else
                                        $lead_image_tag .=  "<img src=".$this->images_to_display[1]["url"]." width=".$lead_image_width." height=".$lead_image_height." border=0>";
                                    }
                                }
                            }

                            //$lead_image_tag .= "</a>";
                            if ($this->debug_ad_display) echo $lead_image_tag." is the lead image tag<br>\n";
                            $template = str_replace("<<LEAD_PICTURE>>",$lead_image_tag,$template);
                        }
                        else
                        {
                            if ($this->debug_ad_display) echo "there is no image to display in the lead image spot<br>\n";
                            $template = str_replace("<<LEAD_PICTURE>>","",$template);
                        }
                    } //end of if lead picture

                    //get ads extra questions
                    $extra_question_block = $this->get_ads_extra_values($db,$id,$show->CATEGORY);

                    //get ads extra checkboxs
                    $extra_checkbox_block = $this->get_ads_extra_checkboxs($db,$id,$show->CATEGORY);

                    $template = str_replace("<<EXTRA_QUESTION_BLOCK>>",$extra_question_block,$template);
                    $template = str_replace("<<EXTRA_CHECKBOX_BLOCK>>",$extra_checkbox_block,$template);

                    if ($show->SOLD_DISPLAYED)
                        $title = "<img src=".$this->configuration_data['sold_image']." border=0> ".stripslashes(urldecode($show->TITLE));
                    else
                        $title = stripslashes(urldecode($show->TITLE));

                    $template = str_replace("<<TITLE>>",$title." [CLOSED]",$template);
                    $template = str_replace("<MODULE_TITLE>", $title, $template);

/*                    if (((($this->browsing_configuration->USE_MAPPING_ADDRESS_FIELD) && (strlen(trim($show->MAPPING_ADDRESS)) > 0))
                        || (($this->browsing_configuration->USE_MAPPING_ZIP_FIELD) && (strlen(trim($show->MAPPING_ZIP)) > 0)))
                        && ($this->browsing_configuration->USE_MAPPING_STATE_FIELD) && (strlen(trim($show->MAPPING_STATE)) > 0)
                        && ($this->browsing_configuration->USE_MAPPING_CITY_FIELD) && (strlen(trim($show->MAPPING_CITY)) > 0))
                    {
                        //build mapquest link
                        $mapquest_link = "<a href=\"javascript:winimage('http://www.mapquest.com/maps/map.adp?homesubmit=Get+Map";
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
                        $template = str_replace("<<MAPPING_LINK>>","",$template);*/

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
                        $time_left .= $weeks." ".urldecode($this->messages[103327]).", ".$days." ".urldecode($this->messages[103328]);
                    }
                    elseif ($days > 0)
                    {
                        $time_left .= $days." ".urldecode($this->messages[103328]).", ".$hours." ".urldecode($this->messages[103329]);
                    }
                    elseif ($hours > 0)
                    {
                        $time_left .= $hours." ".urldecode($this->messages[103329]).", ".$minutes." ".urldecode($this->messages[103323]);
                    }
                    elseif ($minutes > 0)
                    {
                        $time_left .= $minutes." ".urldecode($this->messages[103323]).", ".$seconds." ".urldecode($this->messages[103324]);
                    }
                    elseif ($seconds > 0)
                    {
                        $time_left .= $seconds." ".urldecode($this->messages[103324]);
                    }
                    $template = str_replace("<<TIME_REMAINING_LABEL>>", urldecode($this->messages[103325]),$template);
                    $template = str_replace("<<TIME_REMAINING>>", $time_left, $template);

                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_1)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_1_LABEL>>",urldecode($this->messages[1090]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_1>>",stripslashes(urldecode($show->OPTIONAL_FIELD_1)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_2)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_2_LABEL>>",urldecode($this->messages[1091]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_2>>",stripslashes(urldecode($show->OPTIONAL_FIELD_2)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_3)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_3_LABEL>>",urldecode($this->messages[1092]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_3>>",stripslashes(urldecode($show->OPTIONAL_FIELD_3)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_4)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_4_LABEL>>",urldecode($this->messages[1093]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_4>>",stripslashes(urldecode($show->OPTIONAL_FIELD_4)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_5)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_5_LABEL>>",urldecode($this->messages[1094]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_5>>",stripslashes(urldecode($show->OPTIONAL_FIELD_5)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_6)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_6_LABEL>>",urldecode($this->messages[1095]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_6>>",stripslashes(urldecode($show->OPTIONAL_FIELD_6)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_7)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_7_LABEL>>",urldecode($this->messages[1096]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_7>>",stripslashes(urldecode($show->OPTIONAL_FIELD_7)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_8)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_8_LABEL>>",urldecode($this->messages[1097]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_8>>",stripslashes(urldecode($show->OPTIONAL_FIELD_8)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_9)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_9_LABEL>>",urldecode($this->messages[1098]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_9>>",stripslashes(urldecode($show->OPTIONAL_FIELD_9)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_10)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_10_LABEL>>",urldecode($this->messages[1099]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_10>>",stripslashes(urldecode($show->OPTIONAL_FIELD_10)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_11)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_11_LABEL>>",urldecode($this->messages[1706]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_11>>",stripslashes(urldecode($show->OPTIONAL_FIELD_11)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_12)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_12_LABEL>>",urldecode($this->messages[1707]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_12>>",stripslashes(urldecode($show->OPTIONAL_FIELD_12)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_13)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_13_LABEL>>",urldecode($this->messages[1708]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_13>>",stripslashes(urldecode($show->OPTIONAL_FIELD_13)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_14)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_14_LABEL>>",urldecode($this->messages[1709]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_14>>",stripslashes(urldecode($show->OPTIONAL_FIELD_14)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_15)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_15_LABEL>>",urldecode($this->messages[1710]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_15>>",stripslashes(urldecode($show->OPTIONAL_FIELD_15)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_16)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_16_LABEL>>",urldecode($this->messages[1711]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_16>>",stripslashes(urldecode($show->OPTIONAL_FIELD_16)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_17)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_17_LABEL>>",urldecode($this->messages[1712]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_17>>",stripslashes(urldecode($show->OPTIONAL_FIELD_17)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_18)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_18_LABEL>>",urldecode($this->messages[1713]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_18>>",stripslashes(urldecode($show->OPTIONAL_FIELD_18)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_19)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_19_LABEL>>",urldecode($this->messages[1714]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_19>>",stripslashes(urldecode($show->OPTIONAL_FIELD_19)),$template);
                    }
                    if ($this->browsing_configuration->USE_OPTIONAL_FIELD_20)
                    {
                        $template = str_replace("<<OPTIONAL_FIELD_20_LABEL>>",urldecode($this->messages[1715]),$template);
                        $template = str_replace("<<OPTIONAL_FIELD_20>>",stripslashes(urldecode($show->OPTIONAL_FIELD_20)),$template);
                    }

                    if (($this->browsing_configuration->PUBLICALLY_EXPOSE_EMAIL) && ($show->EXPOSE_EMAIL))
                    {
                        $template = str_replace("<<PUBLIC_EMAIL_LABEL>>",stripslashes(urldecode($this->messages[1474])),$template);
                        $template = str_replace("<<PUBLIC_EMAIL>>",stripslashes(urldecode($show->EMAIL)),$template);
                    }
                    else
                    {
                        $template = str_replace("<<PUBLIC_EMAIL_LABEL>>","",$template);
                        $template = str_replace("<<PUBLIC_EMAIL>>","",$template);
                    }

                    if (($this->browsing_configuration->USE_PHONE_1_OPTION_FIELD) && (strlen($show->PHONE) > 0))
                    {
                        $template = str_replace("<<PHONE_LABEL>>",stripslashes(urldecode($this->messages[1475])),$template);
                        $template = str_replace("<<PHONE_DATA>>",stripslashes(urldecode($show->PHONE)),$template);
                    }
                    else
                    {
                        $template = str_replace("<<PHONE_LABEL>>","",$template);
                        $template = str_replace("<<PHONE_DATA>>","",$template);
                    }

                    if (($this->browsing_configuration->USE_PHONE_2_OPTION_FIELD) && (strlen($show->PHONE2) > 0))
                    {
                        $template = str_replace("<<PHONE2_LABEL>>",stripslashes(urldecode($this->messages[1476])),$template);
                        $template = str_replace("<<PHONE2_DATA>>",stripslashes(urldecode($show->PHONE2)),$template);
                    }
                    else
                    {
                        $template = str_replace("<<PHONE2_LABEL>>","",$template);
                        $template = str_replace("<<PHONE2_DATA>>","",$template);
                    }

                    if (($this->browsing_configuration->USE_FAX_FIELD_OPTION) && (strlen($show->FAX) > 0))
                    {
                        $template = str_replace("<<FAX_LABEL>>",stripslashes(urldecode($this->messages[1477])),$template);
                        $template = str_replace("<<FAX_DATA>>",stripslashes(urldecode($show->FAX)),$template);
                    }
                    else
                    {
                        $template = str_replace("<<FAX_LABEL>>","",$template);
                        $template = str_replace("<<FAX_DATA>>","",$template);
                    }

                    if ($this->browsing_configuration->USE_URL_LINK_1)
                    {
                        if (strlen(trim($show->URL_LINK_1)) > 0)
                        {
                            if (stristr(stripslashes(urldecode($show->URL_LINK_1)),"http://"))
                                $url_link_1 = stripslashes(urldecode($this->messages[2440]));
                            else
                                $url_link_1 = stripslashes(urldecode($this->messages[2440]));
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
                                $url_link_2 = stripslashes(urldecode($this->messages[2441]));
                            else
                                $url_link_2 = stripslashes(urldecode($this->messages[2441]));

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
                                $url_link_3 = stripslashes(urldecode($this->messages[2442]));
                            else
                                $url_link_3 = stripslashes(urldecode($this->messages[2442]));

                            $template = str_replace("<<URL_LINK_3>>",$url_link_3,$template);
                        }
                        else
                            $template = str_replace("<<URL_LINK_3>>","",$template);
                    }

                    if ($this->browsing_configuration->USE_CITY_FIELD)
                    {
                        $template = str_replace("<<CITY_LABEL>>",urldecode($this->messages[1468]),$template);
                        $template = str_replace("<<CITY_DATA>>",stripslashes(urldecode($show->LOCATION_CITY)),$template);
                    }
                    if ($this->browsing_configuration->USE_STATE_FIELD)
                    {
                        $template = str_replace("<<STATE_LABEL>>",urldecode($this->messages[1478]),$template);
                        $template = str_replace("<<STATE_DATA>>",stripslashes(urldecode($show->LOCATION_STATE)),$template);
                    }
                    if ($this->browsing_configuration->USE_COUNTRY_FIELD)
                    {
                        $template = str_replace("<<COUNTRY_LABEL>>",urldecode($this->messages[1080]),$template);
                        $template = str_replace("<<COUNTRY_DATA>>",stripslashes(urldecode($show->LOCATION_COUNTRY)),$template);
                    }
                    if ($this->browsing_configuration->USE_ZIP_FIELD)
                    {
                        $template = str_replace("<<ZIP_LABEL>>",urldecode($this->messages[1479]),$template);
                        $template = str_replace("<<ZIP_DATA>>",stripslashes(urldecode($show->LOCATION_ZIP)),$template);
                    }


                    $template = str_replace("<<CLASSIFIED_ID_LABEL>>",urldecode($this->messages[1082]),$template);
                    $template = str_replace("<<CLASSIFIED_ID>>",$show->ID,$template);

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
                            $template = str_replace("<<BID_START_DATE_LABEL>>",urldecode($this->messages[103326]), $template);
                            $template = str_replace("<<BID_START_DATE>>",$bid_start_date,$template);
                        }
                        else
                        {
                            $template = str_replace("<<BID_START_DATE_LABEL>>","", $template);
                            $template = str_replace("<<BID_START_DATE>>","",$template);
                        }
                    }

                    $template = str_replace("<<VIEWED_COUNT_LABEL>>",urldecode($this->messages[1084]),$template);
                    $template = str_replace("<<VIEWED_COUNT>>",$show->VIEWED,$template);

                    $template = str_replace("<<SELLER_LABEL>>",urldecode($this->messages[1078]),$template);
                    $template = str_replace("<<SELLER>>",$seller_data->USERNAME,$template);

                    $template = str_replace("<<DATE_STARTED_LABEL>>",urldecode($this->messages[1079]),$template);
                    $template = str_replace("<<DATE_STARTED>>",$start_date,$template);
                    //$template = str_replace("<<LOCATION_LABEL>>",urldecode($this->messages[6]),$template);
                    //$template = str_replace("<<LOCATION>>",$location_string,$template);

                    $template = str_replace("<<DESCRIPTION_LABEL>>",urldecode($this->messages[1081]),$template);
                    $template = str_replace("<<DESCRIPTION>>",stripslashes(urldecode($show->DESCRIPTION)),$template);

                    //classauctions details
                    if ($show->ITEM_TYPE==1)
                    {
                        if ($this->browsing_configuration->USE_PRICE_FIELD)
                        {
                            $template = str_replace("<<PRICE_LABEL>>",stripslashes(urldecode($this->messages[1085])),$template);
                            if (((strlen(trim(urldecode($show->PRICE))) > 0)
                                || (strlen(trim(urldecode($show->PRECURRENCY))) > 0)
                                || (strlen(trim(urldecode($show->POSTCURRENCY))) > 0)) && ($show->PRICE != 0))
                            {
                                if (floor($show->PRICE) == $show->PRICE)
                                {
                                    $template = str_replace("<<PRICE>>",stripslashes(urldecode($show->PRECURRENCY))." ".number_format(stripslashes(urldecode($show->PRICE)))." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
                                }
                                else
                                {
                                    $template = str_replace("<<PRICE>>",stripslashes(urldecode($show->PRECURRENCY))." ".number_format(stripslashes(urldecode($show->PRICE)),2,".",",")." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
                                }
                            }
                            else
                                $template = str_replace("<<PRICE>>",stripslashes(urldecode($show->PRECURRENCY))." - ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
                        }
                    }

                    if($this->is_class_auctions() || $this->is_auctions())
                    {
                        // Feedback
                        // Will display on both the auctions and classifieds
                        $template = str_replace("<<SELLER_RATING_LABEL>>",stripslashes(urldecode($this->messages[103092])),$template);

                        if ($this->debug_ad_display)
                        {
                            echo $seller_data->FEEDBACK_COUNT." is the FEEDBACK_COUNT<br>\n";
                            echo $seller_data->FEEDBACK_SCORE." is FEEDBACK_SCORE<br>\n";
                        }
                        if (($seller_data->FEEDBACK_SCORE > 0) && ($seller_data->FEEDBACK_COUNT != 0))
                        {
                            $this->sql_query = "select filename from ".$this->auctions_feedback_icons_table." where begin <= ".$seller_data->FEEDBACK_SCORE." AND end >= ".$seller_data->FEEDBACK_SCORE;
                            if (($this->configuration_data['debug_feedback']) ||  ($this->debug_ad_display))
                            {
                                $this->debug_display($this->sql_query, $db, $this->filename, $function_name, "auctions_feedback_icons_table", "get the icon filename");
                            }
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
                            if($this->configuration_data['debug_feedback'])
                            {
                                $this->debug_display($this->sql_query, $db, $this->filename, $function_name, "auctions_feedback_icons_table", "get the 0 icon filename");
                            }
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
                            if($this->configuration_data['debug_feedback'])
                            {
                                $this->debug_display($this->sql_query, $db, $this->filename, $function_name, "auctions_feedback_icons_table", "get the negative icon filename");
                            }
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
                            $template = str_replace("<<SELLER_RATING>>",stripslashes(urldecode($this->messages[103110])),$template);

                        //$template = str_replace("<<FEEDBACK_LINK>>","<a href=".$this->configuration_data['classifieds_file_name']."?a=1030&b=".$id."&d=".$show->SELLER." class=display_auction_value>".stripslashes(urldecode($this->messages[102717]))."</a>",$template);
                        $template = str_replace("<<SELLER_NUMBER_RATES_LABEL>>",stripslashes(urldecode($this->messages[103093])),$template);
                        $template = str_replace("<<SELLER_NUMBER_RATES>>",$seller_data->FEEDBACK_COUNT,$template);
                        //$template = str_replace("<<SELLER_RATING_SCALE_EXPLANATION>>",$this->display_help_link(102826),$template);
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
                                $reserve = stripslashes(urldecode($this->messages[103072]));
                            }
                            else
                            {
                                //reserve not yer met
                                $reserve = stripslashes(urldecode($this->messages[103073]));
                            }
                        }
                        else
                            $reserve = "";

                        if(!$show->BUY_NOW_ONLY)
                        {
                            $template = str_replace("<<RESERVE>>",$reserve,$template);
                        }
                        else
                        {
                            $template = str_replace("<<RESERVE>>","",$template);
                        }

                        //$template = str_replace("<<AUCTION_TYPE_HELP>>", $this->display_help_link(103056), $template);

                        if ($show->AUCTION_TYPE == 1)
                            $type_of_auction = stripslashes(urldecode($this->messages[200130]));
                        else
                            $type_of_auction = stripslashes(urldecode($this->messages[200131]));
                        if ($show->AUCTION_TYPE == 1)
                        {
                            $template = str_replace("<<WINNING_DUTCH_BIDDERS>>", "",$template);
                            $template = str_replace("<<WINNING_DUTCH_BIDDERS_LABEL>>", "",$template);
                            $template = str_replace("<<HIGH_BIDDER_LABEL>>",stripslashes(urldecode($this->messages[103112])),$template);
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
                                        $template = str_replace("<<BUY_NOW_LABEL>>",stripslashes(urldecode($this->messages[200133])),$template);
                                        if($can_bid)
                                        {
                                            $template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
                                            if ($show->LIVE)
                                            {
                                                if ($this->classified_user_id)
                                                    $template = str_replace("<<BUY_NOW_LINK>>",stripslashes(urldecode($this->messages[103078])),$template);
                                                else
                                                    $template = str_replace("<<BUY_NOW_LINK>>",stripslashes(urldecode($this->messages[103078])),$template);
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
                                    elseif(($this->configuration_data['buy_now_reserve'] == 0) && ($show->CURRENT_BID == 0.00))
                                    {
                                        //Check for black list and invited list of buyers
                                        //Show only when not in blacklist but in invited list
                                        $template = str_replace("<<BUY_NOW_LABEL>>",stripslashes(urldecode($this->messages[200133])),$template);
                                        if($can_bid)
                                        {
                                            $template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
                                            if ($show->LIVE)
                                            {
                                                if ($this->classified_user_id)
                                                    $template = str_replace("<<BUY_NOW_LINK>>",stripslashes(urldecode($this->messages[103078])),$template);
                                                else
                                                    $template = str_replace("<<BUY_NOW_LINK>>",stripslashes(urldecode($this->messages[103078])),$template);
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
                                    //Check for black list and invited list of buyers
                                    //Show only when not in blacklist but in invited list
                                    if($can_bid)
                                    {
                                        $template = str_replace("<<BUY_NOW_LABEL>>",stripslashes(urldecode($this->messages[200133])),$template);
                                        $template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
                                        if ($this->classified_user_id)
                                            $template = str_replace("<<BUY_NOW_LINK>>",stripslashes(urldecode($this->messages[103078])),$template);
                                        else
                                            $template = str_replace("<<BUY_NOW_LINK>>",stripslashes(urldecode($this->messages[103078])),$template);
                                    }
                                    else
                                    {
                                        $template = str_replace("<<BUY_NOW_LABEL>>",stripslashes(urldecode($this->messages[200133])),$template);
                                        $template = str_replace("<<BUY_NOW_DATA>>",stripslashes(urldecode($show->PRECURRENCY))." ".number_format($show->BUY_NOW)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
                                        $template = str_replace("<<BUY_NOW_LINK>>","",$template);
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
                                $dutch_bidders_table .= "<tr class=current_dutch_bidders_header><td>".stripslashes(urldecode($this->messages[103366]))."</td>\n\t";
                                $dutch_bidders_table .= "<td>".stripslashes(urldecode($this->messages[103079]))."</td>\n\t";
                                $dutch_bidders_table .= "<td>".stripslashes(urldecode($this->messages[103080]))."</td>\n</tr>\n";

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
                                $template = str_replace("<<WINNING_DUTCH_BIDDERS_LABEL>>",stripslashes(urldecode($this->messages[103081])),$template);
                            }
                            else
                            {
                                $template = str_replace("<<WINNING_DUTCH_BIDDERS>>",stripslashes(urldecode($this->messages[103366])),$template);
                                $template = str_replace("<<WINNING_DUTCH_BIDDERS_LABEL>>",stripslashes(urldecode($this->messages[103082])),$template);
                            }

                            $template = str_replace("<<HIGH_BIDDER_LABEL>>",stripslashes(urldecode($this->messages[103112])),$template);
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
                            //$template = str_replace("<<HIGH_BIDDER>>",stripslashes(urldecode($this->messages[200132])),$template);
                        }

                        $template = str_replace("<<NUM_BIDS_LABEL>>",stripslashes(urldecode($this->messages[103083])),$template);
                        $template = str_replace("<<QUANTITY_LABEL>>",stripslashes(urldecode($this->messages[103084])),$template);
                        $template = str_replace("<<QUANTITY>>",$show->QUANTITY,$template);
                        //$template = str_replace("<<AUCTION_ID_LABEL>>",stripslashes(urldecode($this->messages[100008])),$template);
                        //$template = str_replace("<<AUCTION_ID>>",$show->ID,$template);

/*                        if($this->configuration_data['bid_history_link_live'] == 1 && !$show->BUY_NOW_ONLY)
                        {
                            if ($this->debug_ad_display) echo "BID_HISTORY_LINK replace 1<BR>\n";
                            $template = str_replace("<<BID_HISTORY_LINK>>",stripslashes(urldecode($this->messages[102706])),$template);
                        }*/
                        //else
                        //{
                        //    if ($this->debug_ad_display) echo "BID_HISTORY_LINK replace 2<BR>\n";
                        //    $template = str_replace("<<BID_HISTORY_LINK>>","&nbsp;",$template);
                        //}

                        //payment_types
                        if($this->configuration_data['payment_types'])
                        {
                            $show->PAYMENT_OPTIONS = stripslashes(urldecode($show->PAYMENT_OPTIONS));
                            $payment_options = str_replace("||",", ",$show->PAYMENT_OPTIONS);
                            $template = str_replace("<<PAYMENT_OPTIONS_LABEL>>",stripslashes(urldecode($this->messages[103086])),$template);
                            $template = str_replace("<<PAYMENT_OPTIONS>>",$payment_options,$template);
                        }
                        else
                        {
                            $template = str_replace("<<PAYMENT_OPTIONS_LABEL>>","",$template);
                            $template = str_replace("<<PAYMENT_OPTIONS>>","",$template);
                        }
                        $template = str_replace("<<AUCTION_TYPE_LABEL>>",stripslashes(urldecode($this->messages[103087])),$template);
                        $template = str_replace("<<AUCTION_TYPE_DATA>>",$type_of_auction,$template);
                        $template = str_replace("<<DATE_ENDED>>",$end_date,$template);

                        $member_since = date(trim($this->configuration_data['entry_date_configuration']), $seller_data->DATE_JOINED);
                        $template = str_replace("<<MEMBER_SINCE>>", $member_since, $template);

                        if ($show->LIVE == 1)
                        {
                            $template = str_replace("<<DATE_ENDED_LABEL>>",stripslashes(urldecode($this->messages[103088])),$template);
                        }
                        else
                        {
                            $template = str_replace("<<DATE_ENDED_LABEL>>",stripslashes(urldecode($this->messages[103088])),$template);
                        }

                        if(!$show->BUY_NOW_ONLY)
                        {
                            $template = str_replace("<<MINIMUM_LABEL>>",stripslashes(urldecode($this->messages[103089])),$template);
                            $template = str_replace("<<MINIMUM_BID>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $minimum_bid)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);

                            if($this->classified_user_id == $show->SELLER)
                            {
                                $template = str_replace("<<RESERVE_LABEL>>",stripslashes(urldecode($this->messages[103090])),$template);
                                $template = str_replace("<<RESERVE_BID>>",stripslashes(urldecode($show->PRECURRENCY))." ".$this->print_number( $show->RESERVE_PRICE)." ".stripslashes(urldecode($show->POSTCURRENCY)),$template);
                            }
                            $template = str_replace("<<STARTING_LABEL>>",stripslashes(urldecode($this->messages[103091])),$template);
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
                            if($can_bid)
                            {
                                if ($this->classified_user_id)
                                    $make_bid_link = stripslashes(urldecode($this->messages[103095]));
                                else
                                    $make_bid_link = stripslashes(urldecode($this->messages[103095]));

                                    if(!$show->BUY_NOW_ONLY)
                                    {
                                        $template = str_replace("<<MAKE_BID_LINK>>",$make_bid_link,$template);
                                    }
                                    else
                                    {
                                        $template = str_replace("<<MAKE_BID_LINK>>","",$template);
                                    }
                            }
                            elseif (!$this->classified_user_id)
                            {
                                $make_bid_link = stripslashes(urldecode($this->messages[103095]));
                                $template = str_replace("<<MAKE_BID_LINK>>",$make_bid_link,$template);
                            }
                            else
                            {
                                $make_bid_link = stripslashes(urldecode($this->messages[103095]));
                                $template = str_replace("<<MAKE_BID_LINK>>",$make_bid_link,$template);
                            }

/*                            if($this->configuration_data['bid_history_link_live'] == 1)
                            {
                                if ($this->debug_ad_display) echo "BID_HISTORY_LINK replace 3<BR>\n";
                                $template = str_replace("<<BID_HISTORY_LINK>>",stripslashes(urldecode($this->messages[102706])),$template);
                            }
                            else
                            {
                                if ($this->debug_ad_display) echo "BID_HISTORY_LINK replace 4<BR>\n";
                                $template = str_replace("<<BID_HISTORY_LINK>>","&nbsp;",$template);
                            }*/

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
                            //$template = str_replace("<<BID_HISTORY_LINK>>","<a href=".$this->configuration_data['classifieds_file_name']."?a=1031&b=".$id." class=bid_history_link>".stripslashes(urldecode($this->messages[102706]))."</a>",$template);
                        }
                    }

                    $template = str_replace("<<SELLER_FIRST_NAME>>",stripslashes(urldecode($seller_data->FIRSTNAME)),$template);
                    $template = str_replace("<<SELLER_LAST_NAME>>",stripslashes(urldecode($seller_data->LASTNAME)),$template);
                    $template = str_replace("<<SELLER_URL>>",stripslashes(urldecode($seller_data->URL)),$template);
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


                    $css = $this->get_css($db);
                    if (strlen(trim($css)) > 0)
                    {
                        //echo "hello css<bR>\n";
                        $this->font_stuff = "<style type=\"text/css\">\n<!--\n".$this->font_stuff."-->\n</style>\n";
                        $template = str_replace("<<CSSSTYLESHEET>>",$this->font_stuff,$template);
                    }
                    //echo $template;
                    $this->template = $template;
                    $this->display_page($db);
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
		}
		else
		{
			//no id to display
			$this->body .="no id<br>\n";
			return false;
		} //end of else
	} //end of function display_classifed

//####################################################################################

	function get_ads_extra_values($db,$classified_id,$category_id=0)
	{
		if (($category_id) && ($classified_id))
		{
			$this->sql_query = "select * from ".$this->classified_extra_table." where classified_id = ".$classified_id." and checkbox = 0 order by display_order asc";
			$special_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<Br>\n";
			if (!$special_result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($special_result->RecordCount() > 0 )
			{
				//get extra template

				do {
					//get category specific template (if any)
					$this->sql_query = "select ".$this->categories_table.".parent_id, 
						".$this->categories_languages_table.".ad_detail_extra_display_template_id
						from ".$this->categories_table.", ".$this->categories_languages_table." where
						".$this->categories_languages_table.".category_id = ".$category_id." and
						".$this->categories_table.".category_id = ".$category_id." and 
						".$this->categories_languages_table.".language_id = ".$this->language_id;

					//echo $this->sql_query."<bR>\n";
					$detail_template_result = $db->Execute($this->sql_query);
					if (!$detail_template_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}
					elseif ($detail_template_result->RecordCount() == 1)
					{
						$show_detail_template = $detail_template_result->FetchNextObject();
						$template_id = $show_detail_template->AD_DETAIL_EXTRA_DISPLAY_TEMPLATE_ID;
						$category_id = $show_detail_template->PARENT_ID;
					}
					else
					{
						return false;
					}						
				} while (($category_id) && (!$template_id));

				if (!$template_id)
					$template_id = $this->ad_configuration_data->USER_EXTRA_TEMPLATE;				

				//get template
				$this->sql_query = "select * from ".$this->templates_table." where template_id = ".$template_id;
				$template_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$template_result)
				{
					echo "template failed 4<br>";
					return false;
				}
				elseif ($template_result->RecordCount() ==1)
				{
					$show_template = $template_result->FetchNextObject();
					$extra_template = stripslashes($show_template->TEMPLATE_CODE);
				}
				else
					return false;

				//$this->body .="<tr>\n\t<td>\n\t<table cellpadding=2 cellspacing=1 border=0>\n\t";
				//list the special description fields
				while ($show_special = $special_result->FetchNextObject())
				{
					$current_line = str_replace("<<EXTRA_QUESTION_NAME>>",urldecode($show_special->NAME),$extra_template);
					$current_line = str_replace("<<EXTRA_QUESTION_VALUE>>",urldecode($show_special->VALUE),$current_line);
					$question_block .= $current_line;
				}
				//$this->body .="</table>\n\t</td>\n</tr>\n";
			}
			return $question_block;
		}
		else
			return false;
	} //end of function get_ads_extra_questions

//#################################################################################

	function get_ads_extra_checkboxs($db,$classified_id=0,$category_id=0)
	{
		if (($classified_id) && ($category_id))
		{
			$this->sql_query = "select * from ".$this->classified_extra_table." where classified_id = ".$classified_id." and checkbox = 1";
			$special_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." inside of checkboxes<Br>\n";
			if (!$special_result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($special_result->RecordCount() > 0 )
			{
				//get extra checkbox template
				do {
					//get category specific template (if any)
					$this->sql_query = "select ".$this->categories_table.".parent_id,
						".$this->categories_languages_table.".ad_detail_checkbox_display_template_id
						from ".$this->categories_table.", ".$this->categories_languages_table." where 
						".$this->categories_languages_table.".category_id = ".$category_id." and
						".$this->categories_table.".category_id = ".$category_id." and 
						".$this->categories_languages_table.".language_id = ".$this->language_id;;

					//echo $this->sql_query."<bR>\n";
					$detail_template_result = $db->Execute($this->sql_query);
					if (!$detail_template_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}
					elseif ($detail_template_result->RecordCount() == 1)
					{
						$show_detail_template = $detail_template_result->FetchNextObject();
						$template_id = $show_detail_template->AD_DETAIL_CHECKBOX_DISPLAY_TEMPLATE_ID;
						$category_id = $show_detail_template->PARENT_ID;
					}
					else
					{
						return false;
					}
				} while (($category_id) && (!$template_id));

				if (!$template_id)
					$template_id = $this->ad_configuration_data->USER_CHECKBOX_TEMPLATE;

				//get template
				$this->sql_query = "select * from ".$this->templates_table." where template_id = ".$template_id;
				$template_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$template_result)
				{
					echo "template failed 4<br>";
					return false;
				}
				elseif ($template_result->RecordCount() ==1)
				{
					$show_template = $template_result->FetchNextObject();
					$checkbox_template = stripslashes($show_template->TEMPLATE_CODE);
				}
				else
					return false;

				//$this->body .="<tr>\n\t<td>\n\t<table cellpadding=2 cellspacing=1 border=0>\n\t";
				//list the special description fields
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
						$current_line = str_replace("<<EXTRA_CHECKBOX_NAME>>",stripslashes(urldecode($show_special->NAME)),$checkbox_template);
					$question_block .= $current_line;
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
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				return true;
			}
			else
			{
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
		}
		else
		{
			//no auction id to check
			$this->error_message = urldecode($this->messages[81]);
			return false;
		}
	 } //end of function classified_exists

//####################################################################################

	function browse_error ($db) 
	{
		$this->page_id = 1;
		$this->get_text($db);		
		$this->body .="<table cellpadding=5 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr class=display_ad_label>\n\t<td>".urldecode($this->messages[600])."</td>\n</tr>\n";
		$this->body .="<tr class=display_ad_label>\n\t<td>".urldecode($this->messages[638])."</td>\n</tr>\n";
		$this->body .="<tr class=error_messages>\n\t<td>".urldecode($this->messages[64])."</td>\n</tr>\n";
		$this->body .="</table>\n";  
		$this->display_page($db);
		exit; 
	 } //end of function browse_error	 
	
//####################################################################################

} // end of class Display_ad_print_friendly

?>
