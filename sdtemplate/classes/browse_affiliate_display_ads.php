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
	var $debug_display_ad = 0;

//########################################################################

	function Display_ad($db,$affiliate_id,$language_id,$category_id=0,$page=0,$classified_id=0,$affiliate_group_id=0,$product_configuration=0)
	{
		if ($category_id)
			$this->site_category = $category_id;
		elseif ($classified_id)
		{
			$show = $this->get_classified_data($db,$classified_id);
			$this->site_category = $show->CATEGORY;
		}
		else
			$this->site_category = 0;
		if ($limit)
			$this->browse_limit = $limit;
		$this->Site($db,1,$language_id,0,$product_configuration);
		$this->get_ad_configuration($db);
		if ($page)
			$this->page_result = $page;
		else
			$this->page_result = 1;
		$this->affiliate_group_id = $affiliate_group_id;
		$this->affiliate_page_type = 1;

	} //end of function Display_ad

//###########################################################

	function display_classified($db,$id=0,$affiliate_id=0)
	{
		$this->page_id = 1;
		$this->get_text($db);
		if (($id) && ($affiliate_id))
		{
			$this->affiliate_id = $affiliate_id;
			$show = $this->get_classified_data($db,$id);
			$seller_data = $this->get_user_data($db,$show->SELLER);
			if ($show)
			{
				if ($show->LIVE == 1)
				{
					$this->sql_query = "select id from ".$this->classifieds_table." where
						seller = ".$affiliate_id." and live = 1 order by better_placement desc,date desc";
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
								$next_classified_id = $next_id->ID;
								break;
							}
							$last_classified_id = $next_id->ID;
						}
					}
					
					$this->site_category = $show->CATEGORY;
					
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
					$template_category = $show->CATEGORY;

					$this->sql_query = "select ad_display_template_id from ".$this->affiliate_templates_table."
						where group_id = ".$seller_data->GROUP_ID." and language_id = ".$this->language_id;

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
						$template_id = $show_detail_template->AD_DISPLAY_TEMPLATE_ID;
					}
					else
					{
						return false;
					}

					if (!$template_id)
						$template_id = $this->ad_configuration_data->USER_AD_TEMPLATE;

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

					$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$show->SELLER;
					$seller_group_result = $db->Execute($this->sql_query);
					if (!$seller_group_result)
					{
						return false;
					}
					elseif ($seller_group_result->RecordCount() ==1)
					{
						$show_group = $seller_group_result->FetchNextObject();
						$this->sql_query = "select sponsored_by_code from ".$this->groups_table." where group_id = ".$show_group->GROUP_ID;
						$sponsored_by_result = $db->Execute($this->sql_query);
						if (!$sponsored_by_result)
						{
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
						return false;

					$start_date = date(trim($this->configuration_data['entry_date_configuration']),$show->DATE);
					$seller_data = $this->get_user_data($db,$show->SELLER);

					$user_return_link = "<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id.">".urldecode($this->messages[726])." ".$seller_data->USERNAME."</a>";

					$image_block = $this->display_ad_images($db,$id);
					
					
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
					$extra_question_block = $this->get_ads_extra_values($db,$id,$seller_data->GROUP_ID);

					//get ads extra checkboxs
					$extra_checkbox_block = $this->get_ads_extra_checkboxs($db,$id,$seller_data->GROUP_ID);

					if ($show->SOLD_DISPLAYED)
						$title = "<img src=".$this->configuration_data['sold_image']." border=0> ".stripslashes(urldecode($show->TITLE));
					else
						$title = stripslashes(urldecode($show->TITLE));

					$template = str_replace("<<TITLE>>",$title,$template);

					if (((($this->browsing_configuration->USE_MAPPING_ADDRESS_FIELD) && (strlen(trim($show->MAPPING_ADDRESS)) > 0))
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
						$mapquest_link .= "','800','800')\" class=mapping_link>".urldecode($this->messages[1624])."</a>";
						$template = str_replace("<<MAPPING_LINK>>",$mapquest_link,$template);
					}
					else
						$template = str_replace("<<MAPPING_LINK>>","",$template);

					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_1)
					{
						$template = str_replace("<<OPTIONAL_FIELD_1_LABEL>>",urldecode($this->messages[912]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_1>>",stripslashes(urldecode($show->OPTIONAL_FIELD_1)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_2)
					{
						$template = str_replace("<<OPTIONAL_FIELD_2_LABEL>>",urldecode($this->messages[913]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_2>>",stripslashes(urldecode($show->OPTIONAL_FIELD_2)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_3)
					{
						$template = str_replace("<<OPTIONAL_FIELD_3_LABEL>>",urldecode($this->messages[914]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_3>>",stripslashes(urldecode($show->OPTIONAL_FIELD_3)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_4)
					{
						$template = str_replace("<<OPTIONAL_FIELD_4_LABEL>>",urldecode($this->messages[915]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_4>>",stripslashes(urldecode($show->OPTIONAL_FIELD_4)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_5)
					{
						$template = str_replace("<<OPTIONAL_FIELD_5_LABEL>>",urldecode($this->messages[916]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_5>>",stripslashes(urldecode($show->OPTIONAL_FIELD_5)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_6)
					{
						$template = str_replace("<<OPTIONAL_FIELD_6_LABEL>>",urldecode($this->messages[917]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_6>>",stripslashes(urldecode($show->OPTIONAL_FIELD_6)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_7)
					{
						$template = str_replace("<<OPTIONAL_FIELD_7_LABEL>>",urldecode($this->messages[918]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_7>>",stripslashes(urldecode($show->OPTIONAL_FIELD_7)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_8)
					{
						$template = str_replace("<<OPTIONAL_FIELD_8_LABEL>>",urldecode($this->messages[919]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_8>>",stripslashes(urldecode($show->OPTIONAL_FIELD_8)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_9)
					{
						$template = str_replace("<<OPTIONAL_FIELD_9_LABEL>>",urldecode($this->messages[920]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_9>>",stripslashes(urldecode($show->OPTIONAL_FIELD_9)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_10)
					{
						$template = str_replace("<<OPTIONAL_FIELD_10_LABEL>>",urldecode($this->messages[921]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_10>>",stripslashes(urldecode($show->OPTIONAL_FIELD_10)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_11)
					{
						$template = str_replace("<<OPTIONAL_FIELD_11_LABEL>>",urldecode($this->messages[1726]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_11>>",stripslashes(urldecode($show->OPTIONAL_FIELD_11)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_12)
					{
						$template = str_replace("<<OPTIONAL_FIELD_12_LABEL>>",urldecode($this->messages[1727]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_12>>",stripslashes(urldecode($show->OPTIONAL_FIELD_12)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_13)
					{
						$template = str_replace("<<OPTIONAL_FIELD_13_LABEL>>",urldecode($this->messages[1728]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_13>>",stripslashes(urldecode($show->OPTIONAL_FIELD_13)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_14)
					{
						$template = str_replace("<<OPTIONAL_FIELD_14_LABEL>>",urldecode($this->messages[1729]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_14>>",stripslashes(urldecode($show->OPTIONAL_FIELD_14)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_15)
					{
						$template = str_replace("<<OPTIONAL_FIELD_15_LABEL>>",urldecode($this->messages[1730]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_15>>",stripslashes(urldecode($show->OPTIONAL_FIELD_15)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_16)
					{
						$template = str_replace("<<OPTIONAL_FIELD_16_LABEL>>",urldecode($this->messages[1731]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_16>>",stripslashes(urldecode($show->OPTIONAL_FIELD_16)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_17)
					{
						$template = str_replace("<<OPTIONAL_FIELD_17_LABEL>>",urldecode($this->messages[1732]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_17>>",stripslashes(urldecode($show->OPTIONAL_FIELD_17)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_18)
					{
						$template = str_replace("<<OPTIONAL_FIELD_18_LABEL>>",urldecode($this->messages[1733]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_18>>",stripslashes(urldecode($show->OPTIONAL_FIELD_18)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_19)
					{
						$template = str_replace("<<OPTIONAL_FIELD_19_LABEL>>",urldecode($this->messages[1734]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_19>>",stripslashes(urldecode($show->OPTIONAL_FIELD_19)),$template);
					}
					if ($this->browsing_configuration->USE_OPTIONAL_FIELD_20)
					{
						$template = str_replace("<<OPTIONAL_FIELD_20_LABEL>>",urldecode($this->messages[1735]),$template);
						$template = str_replace("<<OPTIONAL_FIELD_20>>",stripslashes(urldecode($show->OPTIONAL_FIELD_20)),$template);
					}
					if ($this->browsing_configuration->USE_CITY_FIELD)
					{
						$template = str_replace("<<CITY_FIELD>>",urldecode($this->messages[1213]),$template);
						$template = str_replace("<<CITY_DATA>>",stripslashes(urldecode($show->LOCATION_CITY)),$template);
					}
					if ($this->browsing_configuration->USE_STATE_FIELD)
					{
						$template = str_replace("<<STATE_LABEL>>",urldecode($this->messages[1214]),$template);
						$template = str_replace("<<STATE_DATA>>",stripslashes(urldecode($show->LOCATION_STATE)),$template);
					}
					if ($this->browsing_configuration->USE_COUNTRY_FIELD)
					{
						$template = str_replace("<<COUNTRY_LABEL>>",urldecode($this->messages[1215]),$template);
						$template = str_replace("<<COUNTRY_DATA>>",stripslashes(urldecode($show->LOCATION_COUNTRY)),$template);
					}
					if ($this->browsing_configuration->USE_ZIP_FIELD)
					{
						$template = str_replace("<<ZIP_LABEL>>",urldecode($this->messages[1216]),$template);
						$template = str_replace("<<ZIP_DATA>>",stripslashes(urldecode($show->LOCATION_ZIP)),$template);
					}

					if ($this->browsing_configuration->USE_PRICE_FIELD)
					{
						$template = str_replace("<<PRICE_LABEL>>",urldecode($this->messages[15]),$template);
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

					$template = str_replace("<<CLASSIFIED_ID_LABEL>>",urldecode($this->messages[8]),$template);
					$template = str_replace("<<CLASSIFIED_ID>>",$show->ID,$template);
					$template = str_replace("<<VIEWED_COUNT_LABEL>>",urldecode($this->messages[10]),$template);
					$template = str_replace("<<VIEWED_COUNT>>",$show->VIEWED + 1,$template);
					$template = str_replace("<<SELLER_LABEL>>",urldecode($this->messages[3]),$template);
					$template = str_replace("<<SELLER>>","<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=13&b=".$id." class=display_ad_value>".$seller_data->USERNAME."</a>",$template);
					$template = str_replace("<<DATE_STARTED_LABEL>>",urldecode($this->messages[4]),$template);
					$template = str_replace("<<DATE_STARTED>>",$start_date,$template);
					$template = str_replace("<<EXTRA_QUESTION_BLOCK>>",$extra_question_block,$template);
					$template = str_replace("<<EXTRA_CHECKBOX_BLOCK>>",$extra_checkbox_block,$template);
					$template = str_replace("<<DESCRIPTION_LABEL>>",urldecode($this->messages[7]),$template);
					$template = str_replace("<<DESCRIPTION>>",stripslashes(urldecode($show->DESCRIPTION)),$template);
					$template = str_replace("<<IMAGE_BLOCK>>",$image_block,$template);

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
						$template = str_replace("<<PHONE_LABEL>>",stripslashes(urldecode($this->messages[1347])),$template);
						$template = str_replace("<<PHONE_DATA>>",stripslashes(urldecode($show->PHONE)),$template);
					}
					elseif ($this->browsing_configuration->USE_PHONE_1_OPTION_FIELD)
					{
						$template = str_replace("<<PHONE_LABEL>>","",$template);
						$template = str_replace("<<PHONE_DATA>>","",$template);
					}

					if (($this->browsing_configuration->USE_PHONE_2_OPTION_FIELD) && (strlen($show->PHONE2) > 0))
					{
						$template = str_replace("<<PHONE2_LABEL>>",stripslashes(urldecode($this->messages[1348])),$template);
						$template = str_replace("<<PHONE2_DATA>>",stripslashes(urldecode($show->PHONE2)),$template);
					}
					elseif ($this->browsing_configuration->USE_PHONE_2_OPTION_FIELD)
					{
						$template = str_replace("<<PHONE2_LABEL>>","",$template);
						$template = str_replace("<<PHONE2_DATA>>","",$template);
					}

					if (($this->browsing_configuration->USE_FAX_FIELD_OPTION) && (strlen($show->FAX) > 0))
					{
						$template = str_replace("<<FAX_LABEL>>",stripslashes(urldecode($this->messages[1349])),$template);
						$template = str_replace("<<FAX_DATA>>",stripslashes(urldecode($show->FAX)),$template);
					}
					elseif ($this->browsing_configuration->USE_FAX_FIELD_OPTION)
					{
						$template = str_replace("<<FAX_LABEL>>","",$template);
						$template = str_replace("<<FAX_DATA>>","",$template);
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


					//$add_to_favorite_link = "<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=20&b=".$id." class=favorites_link>".urldecode($this->messages[11])."</a>";
					//$template = str_replace("<<FAVORITES_LINK>>",$add_to_favorite_link,$template);

					if ($last_classified_id)
						$previous_ad_link = "<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=2&b=".$last_classified_id." class=previous_ad_link>".urldecode($this->messages[787])."</a>";
					else
						$previous_ad_link = "";
					$template = str_replace("<<PREVIOUS_AD_LINK>>",$previous_ad_link,$template);

					if ($next_classified_id)
						$next_ad_link = "<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=2&b=".$next_classified_id." class=next_ad_link>".urldecode($this->messages[786])."</a>";
					else
						$next_ad_link = "";
					$template = str_replace("<<NEXT_AD_LINK>>",$next_ad_link,$template);

					$notify_friend_link = "<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=12&b=".$id." class=notify_friend_link>".urldecode($this->messages[13])."</a>";
					$template = str_replace("<<NOTIFY_FRIEND_LINK>>",$notify_friend_link,$template);

					$message_to_seller_link = "<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=13&b=".$id." class=notify_seller_link>".urldecode($this->messages[14])."</a>";
					$template = str_replace("<<MESSAGE_TO_SELLER_LINK>>",$message_to_seller_link,$template);

					$print_friendly_link = "<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=14&b=".$id." class=print_friendly_link>".urldecode($this->messages[1473])."</a>";
					$template = str_replace("<<PRINT_FRIENDLY_LINK>>",$print_friendly_link,$template);

					if ($show->IMAGE)
					{
						$full_images_link = "<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=15&b=".$id." class=full_images_link>".urldecode($this->messages[1369])."</a>";
						$template = str_replace("<<FULL_IMAGES_LINK>>",$full_images_link,$template);
					}
					else
						$template = str_replace("<<FULL_IMAGES_LINK>>","",$template);

					$return_link = "<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id." class=return_link>".urldecode($this->messages[1527])."</a>";
					$template = str_replace("<<RETURN_TO_LIST_LINK>>",$return_link,$template);

					//increase view count
					$this->sql_query = "update ".$this->classifieds_table." set
						viewed = ".($show->VIEWED + 1)." where id = ".$id;
					$viewed_result = $db->Execute($this->sql_query);
					if (!$viewed_result)
					{
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}
					$this->body .= $template;
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

	function get_ads_extra_values($db,$classified_id=0,$group_id=0)
	{
		if (($group_id) && ($classified_id))
		{
			$this->sql_query = "select * from ".$this->classified_extra_table." where classified_id = ".$classified_id." and checkbox = 0 order by display_order asc";
			$special_result = $db->Execute($this->sql_query);
			if ($this->debug_display_ad) echo $this->sql_query."<Br>\n";
			if (!$special_result)
			{
				if ($this->debug_display_ad) echo $this->sql_query."<Br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($special_result->RecordCount() > 0 )
			{
				//get extra template

				//get category specific template (if any)
				$this->sql_query = "select ad_display_template_id from ".$this->affiliate_templates_table."
					where group_id = ".$group_id." and language_id = ".$this->language_id;

				if ($this->debug_display_ad) echo $this->sql_query."<Br>\n";
				$detail_template_result = $db->Execute($this->sql_query);
				if (!$detail_template_result)
				{
					if ($this->debug_display_ad) echo $this->sql_query."<Br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}
				elseif ($detail_template_result->RecordCount() == 1)
				{
					$show_detail_template = $detail_template_result->FetchNextObject();
					$template_id = $show_detail_template->EXTRA_QUESTION_TEMPLATE_ID;
				}
				else
				{
					return false;
				}
				if (!$template_id)
					$template_id = $this->ad_configuration_data->USER_EXTRA_TEMPLATE;

				//get template
				$this->sql_query = "select * from ".$this->templates_table." where template_id = ".$template_id;
				$template_result = $db->Execute($this->sql_query);
				if ($this->debug_display_ad) echo $this->sql_query."<Br>\n";
				if (!$template_result)
				{
					if ($this->debug_display_ad) echo $this->sql_query."<Br>\n";
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
				if (!$extra_template)
					$extra_template = stripslashes(urldecode($this->ad_configuration_data->USER_EXTRA_TEMPLATE));

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

	function get_ads_extra_checkboxs($db,$classified_id=0,$group_id=0)
	{
		if (($classified_id) && ($group_id))
		{
			$this->sql_query = "select * from ".$this->classified_extra_table." where classified_id = ".$classified_id." and checkbox = 1 order by display_order asc";
			$special_result = $db->Execute($this->sql_query);
			if ($this->debug_display_ad) echo $this->sql_query."<Br>\n";
			if (!$special_result)
			{
				if ($this->debug_display_ad) echo $this->sql_query."<Br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($special_result->RecordCount() > 0 )
			{
				//get extra checkbox template

				//get category specific template (if any)
				$this->sql_query = "select ad_display_template_id from ".$this->affiliate_templates_table."
					where group_id = ".$group_id." and language_id = ".$this->language_id;

				if ($this->debug_display_ad) echo $this->sql_query."<Br>\n";
				$detail_template_result = $db->Execute($this->sql_query);
				if (!$detail_template_result)
				{
					if ($this->debug_display_ad) echo $this->sql_query."<Br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}
				elseif ($detail_template_result->RecordCount() == 1)
				{
					$show_detail_template = $detail_template_result->FetchNextObject();
					$template_id = $show_detail_template->CHECKBOX_QUESTION_TEMPLATE_ID;
				}
				else
				{
					return false;
				}
				if (!$template_id)
					$template_id = $this->ad_configuration_data->USER_CHECKBOX_TEMPLATE;

				//get template
				$this->sql_query = "select * from ".$this->templates_table." where template_id = ".$template_id;
				$template_result = $db->Execute($this->sql_query);
				if ($this->debug_display_ad) echo $this->sql_query."<Br>\n";
				if (!$template_result)
				{
					if ($this->debug_display_ad) echo $this->sql_query."<Br>\n";
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
					//$current_line = str_replace("<<EXTRA_CHECKBOX_NAME>>",urldecode($show_special->NAME),$checkbox_template);
					
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
						$current_line .= str_replace("<<EXTRA_CHECKBOX_NAME>>", "<font class=display_ad_extra_checkbox_label>".stripslashes(urldecode($show_special->NAME))."</font>", $checkbox_template);
						$current_line .= $end;
					}
					else
					{
						$current_line = str_replace("<<EXTRA_CHECKBOX_NAME>>", "<font class=display_ad_extra_checkbox_label>".stripslashes(urldecode($show_special->NAME))."</font>",$checkbox_template);
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
			if ($this->debug_display_ad) echo $this->sql_query."<Br>\n";
			if (!$result)
			{
				if ($this->debug_display_ad) echo $this->sql_query."<Br>\n";
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
		//this->error_message is the class variable that will contain the error message
		$this->page_id = 1;
		$this->get_text($db);
		$this->body .="<table cellpadding=5 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr class=display_ad_label>\n\t<td>".urldecode($this->messages[600])."</td>\n</tr>\n";
		$this->body .="<tr class=display_ad_label>\n\t<td>".urldecode($this->messages[638])."</td>\n</tr>\n";
		$this->body .="<tr class=error_messages>\n\t<td>".urldecode($this->messages[64])."</td>\n</tr>\n";
		$this->body .="</table>\n";
		$this->display_page($db);
	 } //end of function browse_error

//####################################################################################

} // end of class Display_ad
?>