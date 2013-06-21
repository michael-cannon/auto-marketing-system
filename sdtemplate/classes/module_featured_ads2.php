<? //module_featured_ads2.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$this->get_text($db,$show_module['page_id']);
$this->get_css($db,$show_module['page_id']);
$seed = rand();
$this->body = "";

if ($this->filter_id)
{
	$filter_in_statement = $this->get_sql_filter_in_statement($db);
	$sql_filter_in_statement = " and filter_id ".$filter_in_statement." ";
}

if ($this->state_filter)
{
	//add state to end of sql_query
	if (strlen(trim($this->sql_zip_filter_in_statement)) == 0)
		$this->sql_state_filter_statement = " and location_state = \"".$this->state_filter."\" ";
}
if (($this->zip_filter_distance) && ($this->zip_filter))
{
	//add zip code in statement to end of sql_query
	if (strlen(trim($this->sql_zip_filter_in_statement)) == 0)
	{
		$zip_filter_in_statement = $this->get_sql_zip_filter_in_statement($db);
		$this->sql_zip_filter_in_statement = " and ".$zip_filter_in_statement." ";
	}
}

//$show_module['module_display_type_listing'] WILL ALWAYS BE ZERO IF NOT CLASSAUCTIONS. THE SWITCH IN ADMIN IS NOT AVAILABLE TO CHOOSE.
if(($this->is_classifieds()) || (($this->is_class_auctions())&& ($show_module['module_display_type_listing']==2)))
{
	$type_in_statement = " and item_type=1 ";
}
if(($this->is_auctions()) || (($this->is_class_auctions())&& ($show_module['module_display_type_listing']==1)))
{
	$type_in_statement = " and item_type=2 ";
}
if(($this->is_class_auctions()) && ($show_module['module_display_type_listing']==0))
{
	$type_in_statement = "";
}

$this->sql_query = "select * from geodesic_classifieds where featured_ad = 1 and live = 1 ".$this->sql_zip_filter_in_statement.$this->sql_state_filter_statement.$sql_filter_in_statement.$type_in_statement;
$this->sql_query .= " order by rand(".$seed.") limit ".$show_module['module_number_of_ads_to_display'];
$featured_result = $db->Execute($this->sql_query);
//echo $this->sql_query."<br>\n";
if (!$featured_result)
{
	$this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
	return false;
}
elseif ($featured_result->RecordCount() > 0)
{
 	//$show_module['module_number_of_ads_to_display'];
	//$result->Move(0);
	//$this->display_featured_2_result($db,$featured_result);
	if ($featured_result->RecordCount() > 0)
	{
		$this->body ="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n\t";
		if ($show_module['module_display_header_row'])
		{
			$this->body .="<tr class=featured_2_title_row>\n\t\t";
			$colspan = 1;
			if ($show_module['module_display_photo_icon'])
				$colspan++;
			// 0 is default condition
			if (!$show_module['module_display_title'])
				$colspan++;
			if (($show_module['module_display_ad_description']) && (!$show_module['module_display_ad_description_where']))
				$colspan++;
			if ($show_module['module_display_optional_field_1'])
				$colspan++;
			if ($show_module['module_display_optional_field_2'])
				$colspan++;
			if ($show_module['module_display_optional_field_3'])
				$colspan++;
			if ($show_module['module_display_optional_field_4'])
				$colspan++;
			if ($show_module['module_display_optional_field_5'])
				$colspan++;
			if ($show_module['module_display_optional_field_6'])
				$colspan++;
			if ($show_module['module_display_optional_field_7'])
				$colspan++;
			if ($show_module['module_display_optional_field_8'])
				$colspan++;
			if ($show_module['module_display_optional_field_9'])
				$colspan++;
			if ($show_module['module_display_optional_field_10'])
				$colspan++;
			if ($show_module['module_display_optional_field_11'])
				$colspan++;
			if ($show_module['module_display_optional_field_12'])
				$colspan++;
			if ($show_module['module_display_optional_field_13'])
				$colspan++;
			if ($show_module['module_display_optional_field_14'])
				$colspan++;
			if ($show_module['module_display_optional_field_15'])
				$colspan++;
			if ($show_module['module_display_optional_field_16'])
				$colspan++;
			if ($show_module['module_display_optional_field_17'])
				$colspan++;
			if ($show_module['module_display_optional_field_18'])
				$colspan++;
			if ($show_module['module_display_optional_field_19'])
				$colspan++;
			if ($show_module['module_display_optional_field_20'])
				$colspan++;
			if ($show_module['module_display_city'])
				$colspan++;
			if ($show_module['module_display_state'])
				$colspan++;
			if ($show_module['module_display_country'])
				$colspan++;
			if ($show_module['module_display_zip'])
				$colspan++;
			if ($show_module['module_display_price'])
				$colspan++;
			if ($show_module['module_display_entry_date'])
				$colspan++;
			//CLASSAUCTIONS and AUCTIONS SPECIFIC
			if ($show_module['module_display_listing_column'])
				$colspan++;
			if ($show_module['module_display_number_bids'])
				$colspan++;
			if ($show_module['module_display_time_left'])
				$colspan++;
			$this->body .= "<td colspan=".$colspan.">".urldecode($this->messages[686])."</td>\n";
			$this->body .="</tr>\n\t";
		}
		$this->body .="<tr>\n\t\t";
		if ($show_module['module_display_header_row'])
		{
			//CLASSAUCTIONS and AUCTIONS SPECIFIC
			if ($show_module['module_display_listing_column'])
			{
				$this->body .="<td class=featured_2_type nowrap>".urldecode($this->messages[200052])."</td>\n\t";
			}
			if ($show_module['module_display_photo_icon'])
			{
				$this->body .="<td  class=featured_2_photo_td nowrap>".urldecode($this->messages[681])."</td>\n\t";
			}
			// 0 is default condition
			if (!$show_module['module_display_title'])
			{
				$this->body .="<td  class=featured_2_title_td ";
				if ((($show_module['module_display_ad_description'])&& ($show_module['module_display_ad_description_where'])) || (!$show_module['module_display_ad_description']))
					$this->body .= "width=100%";				
				$this->body .=">".urldecode($this->messages[682]);
				if (($show_module['module_display_ad_description']) && ($show_module['module_display_ad_description_where']))
				{
					$this->body .="<br>".urldecode($this->messages[683]);
				}
				$this->body .="</td>\n\t\t";
			}
			if (($show_module['module_display_ad_description']) && (!$show_module['module_display_ad_description_where']))
			{
				$this->body .="<td   class=featured_2_description_td>".urldecode($this->messages[683])."</td>\n\t";
			}
			if ($show_module['module_display_optional_field_1'])
				$this->body .="<td   class=featured_2_optional_header_1 nowrap>".urldecode($this->messages[989])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_2'])
				$this->body .="<td   class=featured_2_optional_header_2 nowrap>".urldecode($this->messages[989])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_3'])
				$this->body .="<td   class=featured_2_optional_header_3 nowrap>".urldecode($this->messages[991])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_4'])
				$this->body .="<td   class=featured_2_optional_header_4 nowrap>".urldecode($this->messages[992])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_5'])
				$this->body .="<td   class=featured_2_optional_header_5 nowrap>".urldecode($this->messages[993])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_6'])
				$this->body .="<td   class=featured_2_optional_header_6 nowrap>".urldecode($this->messages[994])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_7'])
				$this->body .="<td   class=featured_2_optional_header_7 nowrap>".urldecode($this->messages[995])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_8'])
				$this->body .="<td   class=featured_2_optional_header_8 nowrap>".urldecode($this->messages[996])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_9'])
				$this->body .="<td   class=featured_2_optional_header_9 nowrap>".urldecode($this->messages[997])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_10'])
				$this->body .="<td   class=featured_2_optional_header_10 nowrap>".urldecode($this->messages[998])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_11'])
				$this->body .="<td   class=featured_2_optional_header_11 nowrap>".urldecode($this->messages[1746])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_12'])
				$this->body .="<td   class=featured_2_optional_header_12 nowrap>".urldecode($this->messages[1747])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_13'])
				$this->body .="<td   class=featured_2_optional_header_13 nowrap>".urldecode($this->messages[1748])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_14'])
				$this->body .="<td   class=featured_2_optional_header_14 nowrap>".urldecode($this->messages[1749])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_15'])
				$this->body .="<td   class=featured_2_optional_header_15 nowrap>".urldecode($this->messages[1750])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_16'])
				$this->body .="<td   class=featured_2_optional_header_16 nowrap>".urldecode($this->messages[1751])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_17'])
				$this->body .="<td   class=featured_2_optional_header_17 nowrap>".urldecode($this->messages[1752])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_18'])
				$this->body .="<td   class=featured_2_optional_header_18 nowrap>".urldecode($this->messages[1753])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_19'])
				$this->body .="<td   class=featured_2_optional_header_19 nowrap>".urldecode($this->messages[1754])."</font></td>\n\t";
			if ($show_module['module_display_optional_field_20'])
				$this->body .="<td   class=featured_2_optional_header_20 nowrap>".urldecode($this->messages[1755])."</font></td>\n\t";
			if ($show_module['module_display_city'])
				$this->body .="<td  class=featured_2_city_header nowrap>".urldecode($this->messages[1374])."</td>\n\t";
			if ($show_module['module_display_state'])
				$this->body .="<td class=featured_2_state_header nowrap>".urldecode($this->messages[1375])."</td>\n\t";
			if ($show_module['module_display_country'])
				$this->body .="<td class=featured_2_country_header nowrap>".urldecode($this->messages[1376])."</td>\n\t";
			if ($show_module['module_display_zip'])
				$this->body .="<td class=featured_2_zip_header nowrap>".urldecode($this->messages[1377])."</td>\n\t";

			//CLASSAUCTIONS and AUCTIONS SPECIFIC
			if ($show_module['module_display_number_bids'])
			{
				$this->body .="<td class=featured_2_num_bids_td nowrap>".urldecode($this->messages[102553])."</td>\n\t";
			}

			if ($show_module['module_display_price'])
				$this->body .="<td  class=featured_2_price_td  nowrap>".urldecode($this->messages[684])."</td>\n\t";
			if ($show_module['module_display_entry_date'])
				$this->body .="<td  class=featured_2_entry_td nowrap>".urldecode($this->messages[685])."</td>\n\t";

			//CLASSAUCTIONS and AUCTIONS SPECIFIC
			if ($show_module['module_display_time_left'])
			{
				$this->body .="<td class=featured_2_time_left_td nowrap>".urldecode($this->messages[102554])."</td>\n\t";
			}

			if ($this->classified_user_id == 1)
			{
				//this is the admin
				$this->body .="<td>edit</td>\n\t";
				$this->body .="<td>delete</td>\n\t";
			}
			$this->body .="</tr>\n\t";
		}
		$this->row_count = 0;
		while ($show_classifieds = $featured_result->FetchRow())
		{
			if (($this->row_count % 2) == 0)
			{
				if ($show_classifieds['bolding'])
					$css_class_tag= "featured_2_result_table_body_even_bold";
				else
					$css_class_tag=  "featured_2_result_table_body_even ";
			}
			else
			{
				if ($show_classifieds['bolding'])
					$css_class_tag=  "featured_2_result_table_body_odd_bold";
				else
					$css_class_tag=  "featured_2_result_table_body_odd ";
			}
			$this->body .="<tr class=".$css_class_tag.">\n\t\t";

			//CLASSAUCTIONS and AUCTIONS SPECIFIC
			if ($show_module['module_display_listing_column'])
			{
				$this->body .="<td nowrap>";
				if ($show_classifieds['item_type'] == 1)
				{
					$this->body .=urldecode($this->messages[200053]);
				}
				if ($show_classifieds['item_type'] == 2)
				{
					$this->body .=urldecode($this->messages[200054]);
				}

				$this->body .="</td>\n\t";
			}

			if ($show_module['module_display_photo_icon'])
			{
				if($show_module['photo_or_icon'] == 1)
					$this->configuration_data['photo_or_icon'] = 1;
				if(($show_module['photo_or_icon'] == 2 && $this->configuration_data['photo_or_icon'] == 1) || $show_module['photo_or_icon'] == 1)
				{
					if ($this->configuration_data['photo_or_icon'] == 1)
					{
						if ($show_classifieds['image'] > 0)
						{
							if (($show_module['module_thumb_height'] > 0) || ($show_module['module_thumb_width'] > 0))
								$this->display_thumbnail($db,$show_classifieds['id'],$show_module['module_thumb_width'],$show_module['module_thumb_height']);
							else
								$this->display_thumbnail($db,$show_classifieds['id']);
						}
						elseif (($this->configuration_data['no_image_url']) && (!$show_classifieds['image']))
						{
							if (($this->configuration_data['popup_while_browsing'])
								&& ($this->configuration_data['popup_while_browsing_width'])
								&& ($this->configuration_data['popup_while_browsing_height']))
							{
								$this->body .= "<td align=center><a href=\"";
								$this->body .= $this->configuration_data['classifieds_file_name'];
								$this->body .= "?a=2&b=".$show_classifieds['id']."\" ";
								$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
							else
							{
								$this->body .="<td align=center><a href=".$this->configuration_data['classifieds_file_name'];
								$this->body .="?a=2&b=".$show_classifieds['id'].">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
						}
						else
							$this->body .="<td>&nbsp;</td>\n\t";
					}
					else
					{
						if ($show_classifieds['image'] > 0)
						{
							if (($this->configuration_data['popup_while_browsing'])
								&& ($this->configuration_data['popup_while_browsing_width'])
								&& ($this->configuration_data['popup_while_browsing_height']))
							{
								$this->body .= "<td><a href=\"";
								$this->body .= $this->configuration_data['classifieds_file_name'];
								$this->body .= "?a=2&b=".$show_classifieds['id']."\" ";
								$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
								$this->body .="<img src=".$this->configuration_data['photo_icon_url']." border=0></td>";
							}
							else
							{
								$this->body .="<td><a href=".$this->configuration_data['classifieds_file_name'];
								$this->body .="?a=2&b=".$show_classifieds['id'].">";
								$this->body .="<img src=".$this->configuration_data['photo_icon_url']." border=0></td>";
							}
						}
						elseif (($this->configuration_data['no_image_url']) && (!$show_classifieds['image']))
						{
							if (($this->configuration_data['popup_while_browsing'])
								&& ($this->configuration_data['popup_while_browsing_width'])
								&& ($this->configuration_data['popup_while_browsing_height']))
							{
								$this->body .= "<td align=center><a href=\"";
								$this->body .= $this->configuration_data['classifieds_file_name'];
								$this->body .= "?a=2&b=".$show_classifieds['id']."\" ";
								$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
							else
							{
								$this->body .="<td align=center><a href=".$this->configuration_data['classifieds_file_name'];
								$this->body .="?a=2&b=".$show_classifieds['id'].">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
						}
						else
							$this->body .="<td>&nbsp;</td>\n\t";
					}
				}
				else
				{
					$this->body .="<td align=center><a href=".$this->configuration_data['classifieds_file_name'];
					$this->body .="?a=2&b=".$show_classifieds['id'].">";
					$this->body .="<img src=".$this->configuration_data['photo_icon_url']." border=0></td>";
				}
			}

			/*
				Flag to hold whether a link has been done yet
				0 means it hasnt been
				1 means it has
			*/
			$link = 0;

			// 0 is default condition
			if (!$show_module['module_display_title'])
			{
				$this->body .="<td ";
				if ((($show_module['module_display_ad_description'])&& ($show_module['module_display_ad_description_where'])) || (!$show_module['module_display_ad_description']))
					$this->body .= "width=100%";				
				$this->body .=">";
				if ($show_classifieds['sold_displayed'])
					$this->body .= "<img src=".$this->configuration_data['sold_image']." border=0> ";
				if (($this->configuration_data['popup_while_browsing'])
					&& ($this->configuration_data['popup_while_browsing_width'])
					&& ($this->configuration_data['popup_while_browsing_height']))
					$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
				else
					$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				$this->body .= stripslashes(urldecode($show_classifieds['title']))."</a>\n\t\t";
				if ((strlen(trim($this->configuration_data['buy_now_image'])) >0) && ($show_classifieds['item_type'] == 2))
				{
					$current_bid = $show_classifieds['current_bid'];
					$number_of_bids = $this->get_number_of_bids($db,$show_classifieds['id']);
					if (($show_classifieds['buy_now']!= 0) && (($show_classifieds['current_bid'] == 0) || ($this->configuration_data['buy_now_reserve'] && $show_classifieds['current_bid'] < $show_classifieds['reserve_price'])))
					{
						$this->body .= "<img src=".stripslashes($this->configuration_data['buy_now_image'])." border=0 hspace=2>";
					}
				}								
				if ((strlen(trim($this->configuration_data['reserve_met_image'])) >0) && ($show_classifieds['item_type'] == 2))
				{
					if ( $show_classifieds['reserve_price'] != 0)
					{
						$current_bid = $show_classifieds['current_bid'];
						if ($current_bid >= $show_classifieds['reserve_price'])
						{
							$this->body .= "<img src=".stripslashes($this->configuration_data['reserve_met_image'])." border=0 hspace=2>";
						}
					}
				}				
				if ((strlen(trim($this->configuration_data['no_reserve_image'])) >0) && ($show_classifieds['item_type'] == 2))
				{
						if ($show_classifieds['reserve_price'] == 0.00)
						{
							$this->body .= "<img src=".stripslashes($this->configuration_data['no_reserve_image'])." border=0 hspace=2>";
						}
				}				
				if (($show_classifieds['attention_getter']) && ($show_module['module_display_attention_getter']))
				{
					$this->body .= "<img src=\"".$show_classifieds['attention_getter_url']."\" border=0 hspace=2>";
				}
				if (($show_module['module_display_ad_description']) && ($show_module['module_display_ad_description_where']))
				{
					$this->body .="<br>";
					if (strlen(urldecode($show_classifieds['description'])) > $show_module['length_of_description'])
					{
						$small_string = substr(trim(stripslashes(urldecode($show_classifieds['description']))),0,$show_module['length_of_description']);
						$position = strrpos($small_string," ");
						$smaller_string = substr($small_string,0,$position);
						$this->body .=$smaller_string."...";
					}
				else
					$this->body .=	stripslashes(urldecode($show_classifieds['description']));
				}
				$this->body .="</td>\n\t\t";

				$link = 1;
			}

			if (($show_module['module_display_ad_description']) && (!$show_module['module_display_ad_description_where']))
			{
				$this->body .="<td >";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (!$show_module['display_all_of_description'])
				{
					if (strlen(urldecode($show_classifieds['description'])) > $show_module['length_of_description'])
					{
						$small_string = substr(trim(stripslashes(urldecode($show_classifieds['description']))),0,$show_module['length_of_description']);
						$position = strrpos($small_string," ");
						$smaller_string = substr($small_string,0,$position);
						$this->body .=$smaller_string."...";
					}
					else
						$this->body .=	stripslashes(urldecode($show_classifieds['description']));
				}
				else
					$this->body .=	stripslashes(urldecode($show_classifieds['description']));

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</font></td>\n\t";
			}

			if ($show_module['module_display_optional_field_1'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_1']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_1']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</font></td>\n\t";
			}

			if ($show_module['module_display_optional_field_2'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_2']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_2']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</font></td>\n\t";
			}

			if ($show_module['module_display_optional_field_3'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_3']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_3']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</font></td>\n\t";
			}

			if ($show_module['module_display_optional_field_4'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_4']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_4']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</font></td>\n\t";
			}

			if ($show_module['module_display_optional_field_5'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_5']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_5']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</font></td>\n\t";
			}

			if ($show_module['module_display_optional_field_6'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_6']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_6']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</font></td>\n\t";
			}

			if ($show_module['module_display_optional_field_7'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_7']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_7']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</font></td>\n\t";
			}

			if ($show_module['module_display_optional_field_8'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_8']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_8']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</font></td>\n\t";
			}

			if ($show_module['module_display_optional_field_9'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_9']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_9']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</font></td>\n\t";
			}

			if ($show_module['module_display_optional_field_10'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_10']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_10']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</font></td>\n\t";
			}

			if ($show_module['module_display_optional_field_11'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_11']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_11']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}

			if ($show_module['module_display_optional_field_12'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_12']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_12']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}

			if ($show_module['module_display_optional_field_13'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_13']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_13']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}

			if ($show_module['module_display_optional_field_14'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_14']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_14']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}

			if ($show_module['module_display_optional_field_15'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_15']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_15']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}

			if ($show_module['module_display_optional_field_16'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_16']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_16']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}

			if ($show_module['module_display_optional_field_17'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_17']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_17']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}

			if ($show_module['module_display_optional_field_18'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_18']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_18']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}

			if ($show_module['module_display_optional_field_19'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_19']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_19']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}

			if ($show_module['module_display_optional_field_20'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['optional_field_20']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['optional_field_20']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}

			if ($show_module['module_display_city'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['location_city']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['location_city']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}

			if ($show_module['module_display_state'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['location_state']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['location_state']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}

			if ($show_module['module_display_country'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['location_country']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['location_country']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}

			if ($show_module['module_display_zip'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (strlen(trim(urldecode($show_classifieds['location_zip']))) > 0)
					$this->body .=stripslashes(urldecode($show_classifieds['location_zip']));
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}


			//CLASSAUCTIONS and AUCTIONS SPECIFIC
			if ($show_module['module_display_number_bids'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}
				if ($show_classifieds['item_type'] ==2)
					$this->body .=stripslashes(urldecode($this->get_number_of_bids($db,$show_classifieds['id'])));
				else
					$this->body .= " - ";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}


			if ($show_module['module_display_price'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				if (($show_classifieds[$this->item_price($show_classifieds['item_type'])] != 0)
					|| (strlen(trim(urldecode($show_classifieds['precurrency']))) > 0)
					|| (strlen(trim(urldecode($show_classifieds['postcurrency']))) > 0))
				{
					if (floor($show_classifieds[$this->item_price($show_classifieds['item_type'])]) == $show_classifieds[$this->item_price($show_classifieds['item_type'])])
					{
						$this->body .= stripslashes(urldecode($show_classifieds['precurrency'])). " ".
							number_format($show_classifieds[$this->item_price($show_classifieds['item_type'])])." ".
							stripslashes(urldecode($show_classifieds['postcurrency']));
					}
					else
					{
						$this->body .= stripslashes(urldecode($show_classifieds['precurrency'])). " ".
							number_format($show_classifieds[$this->item_price($show_classifieds['item_type'])],2,".",",")." ".
							stripslashes(urldecode($show_classifieds['postcurrency']));
					}
				}
				else
					$this->body .=	"-";

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</font></td>\n\t";
			}

			if ($show_module['module_display_entry_date'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				$this->body .= date(trim($this->configuration_data['entry_date_configuration']),$show_classifieds['date']);

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .= "</td>\n\t";
			}

			//CLASSAUCTIONS and AUCTIONS SPECIFIC
			if ($show_module['module_display_time_left'])
			{
				$this->body .="<td  nowrap>";

				if(!$link)
				{
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
				}

				$weeks = $this->DateDifference(w,$this->shifted_time($db),$show_classifieds["ends"]);
				$remaining_weeks = ($weeks * 604800);

				// Find days left
				$days = $this->DateDifference(d,($this->shifted_time($db)+$remaining_weeks),$show_classifieds["ends"]);
				$remaining_days = ($days * 86400);

				// Find hours left
				$hours = $this->DateDifference(h,($this->shifted_time($db)+$remaining_days),$show_classifieds["ends"]);
				$remaining_hours = ($hours * 3600);

				// Find minutes left
				$minutes = $this->DateDifference(m,($this->shifted_time($db)+$remaining_hours),$show_classifieds["ends"]);
				$remaining_minutes = ($minutes * 60);

				// Find seconds left
				$seconds = $this->DateDifference(s,($this->shifted_time($db)+$remaining_minutes),$show_classifieds["ends"]);
				if(($weeks <= 0) && ($days <= 0) && ($hours <= 0) && ($minutes <= 0) && ($seconds <= 0))
				{
					// If closed we want to display closed text
					$this->body .=  "<div nowrap class=auction_closed> - </div>\n\t";
				}
				else
				{
					$this->body .=  "";
				}
				if ($weeks > 0)
				{
					$this->body .= $weeks." ".stripslashes(urldecode($this->messages[102556])).", ".$days." ".stripslashes(urldecode($this->messages[102555]));
				}
				elseif ($days > 0)
				{
					$this->body .= $days." ".stripslashes(urldecode($this->messages[102555])).", ".$hours." ".stripslashes(urldecode($this->messages[102557]));
				}
				elseif ($hours > 0)
				{
					$this->body .= $hours." ".stripslashes(urldecode($this->messages[102557])).", ".$minutes." ".stripslashes(urldecode($this->messages[102558]));
				}
				elseif ($minutes > 0)
				{
					$this->body .= $minutes." ".stripslashes(urldecode($this->messages[102558])).", ".$seconds." ".stripslashes(urldecode($this->messages[102559]));
				}
				elseif ($seconds > 0)
				{
					$this->body .= $seconds." ".stripslashes(urldecode($this->messages[102559]));
				}

				if(!$link)
				{
					$this->body .= "</a>\n\t\t";
					$link = 1;
				}

				$this->body .="</td>\n\t";
			}

			if ($this->classified_user_id == 1)
			{
				//this is the admin
				$this->body .="<td >\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&e=".$show_classifieds['id']."&b=5><img src=images/btn_user_edit.gif border=0></a>\n\t\t</td>\n\t";
				$this->body .="<td >\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=99&b=".$show_classifieds['id']."&c=".$category."><img src=images/btn_user_remove.gif border=0></a>\n\t\t</td>\n\t";
			}
			$this->body .="</tr>\n\t";
			$this->row_count++;
		} //end of while
		$this->body .="</table>\n";
	}
	else
	{
		//no classifieds in this category
		$this->body ="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n\t";
		$this->body .="<tr class=browsing_category_name>\n\t<td >\n\t".urldecode($this->messages[687])."\n\t</td>\n</tr>\n";
		$this->body .="</table>\n";
	}

}

?>