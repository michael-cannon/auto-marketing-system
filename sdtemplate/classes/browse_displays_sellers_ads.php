<? //browse_displays_sellers_ads.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Browse_display_sellers_ads extends Site {
	var $subcategory_array = array();
	var $notify_data = array();
	var $seller_id = 0;

//########################################################################

	function Browse_display_sellers_ads($db,$classified_user_id,$language_id,$category_id=0,$page=0,$seller_id=0,$product_configuration=0)
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
		$this->Site($db,1,$language_id,$classified_user_id,$product_configuration);
		$this->get_ad_configuration($db);
		if ($page)
			$this->page_result = $page;
		else
			$this->page_result = 1;
		$this->seller_id = $seller_id;
	} //end of function Browse_display_sellers_ads

//###########################################################

	function browse($db)
	{
		$this->page_id = 55;
		$this->get_text($db);
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		//browse the auctions in this category that are open
		//$this->body .=$this->configuration_data['display_sub_category_ads']." display sub cats<Br>\n";

		$seller_data = $this->get_user_data($db,$this->seller_id);

		$this->body .= "<tr class=sellers_name>\n\t<td >".urldecode($this->messages[760])." ".$seller_data->USERNAME."</td>\n</tr>\n";

		$this->sql_query_count = "select count(id) as total from ".$this->classifieds_table." where
			seller = ".$this->seller_id." and live = 1";
		$this->sql_query = "select * from ".$this->classifieds_table." where
			seller = ".$this->seller_id." and live = 1 order by better_placement desc,date desc limit ".(($this->page_result -1) * $this->configuration_data['number_of_ads_to_display']).",".$this->configuration_data['number_of_ads_to_display'];
		//$this->body .=$this->sql_query."<br>\n";
		if (($seller_data->EXPOSE_EMAIL) ||
			($seller_data->EXPOSE_COMPANY_NAME) ||
			($seller_data->EXPOSE_FIRSTNAME) ||
			($seller_data->EXPOSE_LASTNAME) ||
			($seller_data->EXPOSE_ADDRESS) ||
			($seller_data->EXPOSE_CITY) ||
			($seller_data->EXPOSE_STATE) ||
			($seller_data->EXPOSE_COUNTRY) ||
			($seller_data->EXPOSE_ZIP) ||
			($seller_data->EXPOSE_PHONE) ||
			($seller_data->EXPOSE_PHONE2) ||
			($seller_data->EXPOSE_FAX) ||
			($seller_data->EXPOSE_URL) ||
			($seller_data->EXPOSE_OPTIONAL_1) ||
			($seller_data->EXPOSE_OPTIONAL_2) ||
			($seller_data->EXPOSE_OPTIONAL_3) ||
			($seller_data->EXPOSE_OPTIONAL_4) ||
			($seller_data->EXPOSE_OPTIONAL_5) ||
			($seller_data->EXPOSE_OPTIONAL_6) ||
			($seller_data->EXPOSE_OPTIONAL_7) ||
			($seller_data->EXPOSE_OPTIONAL_8) ||
			($seller_data->EXPOSE_OPTIONAL_9) ||
			($seller_data->EXPOSE_OPTIONAL_10))
		{
			//expose the chosen data
			$this->body .= "<tr><td><table width=100% cellspacing=1 cellpadding=2 border=0>";
			if ($seller_data->EXPOSE_EMAIL)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1575])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->EMAIL."</td></tr>\n";
			}
			if (($seller_data->EXPOSE_COMPANY_NAME) && (strlen(trim($seller_data->COMPANY_NAME)) > 0))
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1576])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->COMPANY_NAME."</td></tr>\n";
			}
			if (($seller_data->EXPOSE_FIRSTNAME) || ($seller_data->EXPOSE_LASTNAME))
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1577])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->FIRSTNAME." ".$seller_data->LASTNAME."</td></tr>\n";
			}

			if ($seller_data->EXPOSE_ADDRESS)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1579])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->ADDRESS."<br>".$seller_data->ADDRESS_2."</td></tr>\n";
			}

			if (($seller_data->EXPOSE_CITY) || ($seller_data->EXPOSE_STATE) || ($seller_data->EXPOSE_ZIP))
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1580])."</td>";
				$this->body .= "<td class=seller_info_data>";
				if ($seller_data->EXPOSE_CITY)
					$this->body .= $seller_data->CITY;
				if ($seller_data->EXPOSE_STATE)
					$this->body .= " ".$seller_data->STATE;
				if ($seller_data->EXPOSE_ZIP)
					$this->body .= " ".$seller_data->ZIP;
				$this->body .= "</td></tr>\n";
			}

			if ($seller_data->EXPOSE_COUNTRY)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1582])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->COUNTRY."</td></tr>\n";
			}

			if ($seller_data->EXPOSE_PHONE)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1584])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->PHONE."</td></tr>\n";
			}
			if ($seller_data->EXPOSE_PHONE2)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1585])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->PHONE2."</td></tr>\n";
			}
			if ($seller_data->EXPOSE_FAX)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1586])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->FAX."</td></tr>\n";
			}

			if ($seller_data->EXPOSE_URL)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1587])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->URL."</td></tr>\n";
			}

			if ($seller_data->EXPOSE_OPTIONAL_1)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1588])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->OPTIONAL_FIELD_1."</td></tr>\n";
			}

			if ($seller_data->EXPOSE_OPTIONAL_2)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1589])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->OPTIONAL_FIELD_2."</td></tr>\n";
			}

			if ($seller_data->EXPOSE_OPTIONAL_3)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1590])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->OPTIONAL_FIELD_3."</td></tr>\n";
			}

			if ($seller_data->EXPOSE_OPTIONAL_4)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1591])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->OPTIONAL_FIELD_4."</td></tr>\n";
			}

			if ($seller_data->EXPOSE_OPTIONAL_5)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1592])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->OPTIONAL_FIELD_5."</td></tr>\n";
			}

			if ($seller_data->EXPOSE_OPTIONAL_6)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1593])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->OPTIONAL_FIELD_6."</td></tr>\n";
			}

			if ($seller_data->EXPOSE_OPTIONAL_7)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1594])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->OPTIONAL_FIELD_7."</td></tr>\n";
			}

			if ($seller_data->EXPOSE_OPTIONAL_8)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1595])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->OPTIONAL_FIELD_8."</td></tr>\n";
			}

			if ($seller_data->EXPOSE_OPTIONAL_9)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1596])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->OPTIONAL_FIELD_9."</td></tr>\n";
			}

			if ($seller_data->EXPOSE_OPTIONAL_10)
			{
				$this->body .= "<tr><td class=seller_info_fields>".urldecode($this->messages[1597])."</td>";
				$this->body .= "<td class=seller_info_data>".$seller_data->OPTIONAL_FIELD_10."</td></tr>\n";
			}
			$this->body .= "</table></td></tr>";
		}
		$result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$result)
		{
			$this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
			return false;
		}
		else
		{
			if ($this->sql_query_count)
			{
				$total_count_result = $db->Execute($this->sql_query_count);
				//$this->body .=$this->sql_query_count." is the query<br>\n";
				if ($total_count_result)
				{
					$show_total = $total_count_result->FetchNextObject();
					$total_returned = $show_total->TOTAL;
					//$this->body .=$total_returned." is the total returned<br>\n";
				}
			}

			if ($this->configuration_data['number_of_ads_to_display'] < $total_returned)
			{
				//display the link to the next 10
				$number_of_page_results = ceil($total_returned / $this->configuration_data['number_of_ads_to_display']);
				$this->body .="<tr class=sellers_ads_result_page_links>\n\t<td ><font class=more_results>".urldecode($this->messages[758])." ".$this->page_result."</font><font class=page_of>".urldecode($this->messages[759]).ceil($total_returned / $this->configuration_data['number_of_ads_to_display'])."</font></td>\n</tr>\n";
			}

			$result->Move(0);
			$this->display_browse_result($db,$result,"seller_result_table_header");
			if ($this->configuration_data['number_of_ads_to_display'] < $total_returned)
			{
				//display the link to the next 10
				$number_of_page_results = ceil($total_returned / $this->configuration_data['number_of_ads_to_display']);
				$this->body .="<tr class=sellers_ads_more_results>\n\t<td >".urldecode($this->messages[757])." ";
				if ($number_of_page_results < 10)
				{
					for ($i = 1;$i <= $number_of_page_results;$i++)
					{
						if ($this->page_result == $i)
						{
							$this->body .=" <b>".$i."</b> ";
						}
						else
						{
							$this->body .="<a href=".$this->configuration_data['classifieds_url']."?a=6&b=".$this->seller_id."&page=".$i." class=sellers_ads_result_page_links>".$i."</a> ";
						}
					}
				}
				else
				{
					$number_of_sections =  ceil($number_of_page_results/10);
					for ($section = 0;$section < $number_of_sections;$section++)
					{
						if (($this->page_result > ($section * 10)) && ($this->page_result <= (($section+1) * 10)))
						{
							//display the individual pages within this section
							for ($page = (($section * 10) + 1);$page <= (($section+1) * 10);$page++)
							{
								if ($page <= $number_of_page_results)
									$this->body .="<a href=".$this->configuration_data['classifieds_url']."?a=6&b=".$this->seller_id."&page=".$page." class=sellers_ads_result_page_links>".$page."</a> ";
							}
						}
						else
						{
							//display the link to the section
							$this->body .="<a href=".$this->configuration_data['classifieds_url']."?a=6&b=".$this->seller_id."&page=".(($section*10)+1)." class=sellers_ads_result_page_links>".(($section*10)+1)."</a>";
						}
						if (($section+1) < $number_of_sections)
							$this->body .= "<font class=sellers_ads_result_page_links>..</font>";
					}
				}
				$this->body .="</td>\n</tr>\n";
			}
		}
		$this->body .="</table>\n";
		$this->display_page($db);
		return true;
	} //end of function browse

//####################################################################################

	function display_browse_result($db,$browse_result,$header_css)
	{
					if ($browse_result->RecordCount() > 0)
					{
						$browse_result->Move(0);
						//display the ads inside of this category
						$this->body .="<tr>\n\t<td valign=top height=20>\n\t";
						$this->body .="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n\t";
						$this->body .="<tr class=".$header_css.">\n\t\t";
						
						if(false && $this->is_class_auctions())
							$this->body .="<td  class=seller_title_column_header nowrap>".urldecode($this->messages[200008])."</td>\n\t";
						
						if ($this->configuration_data['display_photo_icon'])
							$this->body .="<td  class=seller_photo_column_header nowrap>".urldecode($this->messages[753])."</td>\n\t";
						if ($this->configuration_data['display_ad_title'])
						{
							$this->body .="<td  class=seller_title_column_header ";
							if ((($this->configuration_data['display_ad_description'])&& ($this->$this->configuration_data['display_ad_description_where'])) || (!$this->browsing_configuration['display_ad_description']))
								$this->body .= "width=100%";							
							$this->body .=">".urldecode($this->messages[752]);
							if (($this->configuration_data['display_ad_description']) && ($this->configuration_data['display_ad_description_where']))
								$this->body .="<br>".urldecode($this->messages[21]);

							$this->body .="</td>\n\t\t";
						}

						if (($this->configuration_data['display_ad_description']) && (!$this->configuration_data['display_ad_description_where']))
							$this->body .="<td class=seller_description_column_header>".urldecode($this->messages[754])."</td>\n\t";
						if ($this->configuration_data['display_optional_field_1'])
							$this->body .="<td class=optional_field_header_1 nowrap>".urldecode($this->messages[1049])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_2'])
							$this->body .="<td class=optional_field_header_2 nowrap >".urldecode($this->messages[1050])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_3'])
							$this->body .="<td class=optional_field_header_3 nowrap >".urldecode($this->messages[1051])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_4'])
							$this->body .="<td class=optional_field_header_4  nowrap>".urldecode($this->messages[1052])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_5'])
							$this->body .="<td class=optional_field_header_5 nowrap >".urldecode($this->messages[1053])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_6'])
							$this->body .="<td class=optional_field_header_6 nowrap >".urldecode($this->messages[1054])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_7'])
							$this->body .="<td class=optional_field_header_7 nowrap >".urldecode($this->messages[1055])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_8'])
							$this->body .="<td class=optional_field_header_8  nowrap>".urldecode($this->messages[1056])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_9'])
							$this->body .="<td class=optional_field_header_9  nowrap>".urldecode($this->messages[1057])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_10'])
							$this->body .="<td class=optional_field_header_10 nowrap >".urldecode($this->messages[1058])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_11'])
							$this->body .="<td class=optional_field_header_11  nowrap>".urldecode($this->messages[1716])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_12'])
							$this->body .="<td class=optional_field_header_12 nowrap >".urldecode($this->messages[1717])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_13'])
							$this->body .="<td class=optional_field_header_13  nowrap>".urldecode($this->messages[1718])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_14'])
							$this->body .="<td class=optional_field_header_14  nowrap>".urldecode($this->messages[1719])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_15'])
							$this->body .="<td class=optional_field_header_15  nowrap>".urldecode($this->messages[1720])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_16'])
							$this->body .="<td class=optional_field_header_16  nowrap>".urldecode($this->messages[1721])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_17'])
							$this->body .="<td class=optional_field_header_17 nowrap >".urldecode($this->messages[1722])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_18'])
							$this->body .="<td class=optional_field_header_18 nowrap >".urldecode($this->messages[1723])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_19'])
							$this->body .="<td class=optional_field_header_19  nowrap>".urldecode($this->messages[1724])."</font></td>\n\t";
						if ($this->configuration_data['display_optional_field_20'])
							$this->body .="<td class=optional_field_header_20  nowrap>".urldecode($this->messages[1725])."</font></td>\n\t";

						if ($this->configuration_data['display_browsing_city_field'])
							$this->body .="<td class=city_column_header nowrap>".urldecode($this->messages[1415])."</a></td>\n\t";
						if ($this->configuration_data['display_browsing_state_field'])
							$this->body .="<td class=state_column_header nowrap>".urldecode($this->messages[1416])."</td>\n\t";
						if ($this->configuration_data['display_browsing_country_field'])
							$this->body .="<td class=country_column_header nowrap>".urldecode($this->messages[1417])."</td>\n\t";
						if ($this->configuration_data['display_browsing_zip_field'])
							$this->body .="<td class=zip_column_header nowrap>".urldecode($this->messages[1418])."</td>\n\t";
						if ($this->configuration_data['display_price'])
							$this->body .="<td  class=seller_price_column_header nowrap >".urldecode($this->messages[755])."</td>\n\t";
						if ($this->configuration_data['display_entry_date'])
							$this->body .="<td  class=seller_entry_date_column_header nowrap>".urldecode($this->messages[756])."</td>\n\t";
						if ($this->configuration_data['display_time_left'])
							$this->body .="<td  class=seller_time_left_column_header nowrap>".urldecode($this->messages[102546])."</td>\n\t";

						if ($this->classified_user_id == 1)
						{
							//this is the admin
							$this->body .="<td>edit</td>\n\t";
							$this->body .="<td>delete</td>\n\t";
						}

						$this->body .="</tr>\n\t";
						$this->row_count = 0;
						while ($show_classifieds = $browse_result->FetchNextObject())
						{
							if (($this->row_count % 2) == 0)
							{
								if ($show_classifieds->BOLDING)
									$css_class_tag= "seller_result_table_body_even_bold";
								else
									$css_class_tag=  "seller_result_table_body_even ";
							}
							else
							{
								if ($show_classifieds->BOLDING)
									$css_class_tag=  "seller_result_table_body_odd_bold";
								else
									$css_class_tag=  "seller_result_table_body_odd ";
							}
							$this->body .="<tr class=".$css_class_tag.">\n\t\t";

							if(false && $this->is_class_auctions())
							{
								if($show_classifieds->ITEM_TYPE == 1)
								{
									$this->body .="<td align=center nowrap>".urldecode($this->messages[200010])."</td>";
								}
								elseif($show_classifieds->ITEM_TYPE == 2)
								{
									$auction = 1;
									$this->body .="<td align=center nowrap>".urldecode($this->messages[200009])."</td>";
								}
							}

							if ($this->configuration_data['display_photo_icon'])
							{
								if ($this->configuration_data['photo_or_icon'] == 1)
								{
									if ($show_classifieds->IMAGE > 0)
									{
										if ($featured)
											$this->display_thumbnail($db,$show_classifieds->ID,$this->configuration_data['featured_thumbnail_max_width'],$this->configuration_data['featured_thumbnail_max_height']);
										else
											$this->display_thumbnail($db,$show_classifieds->ID);
									}
									elseif (($this->configuration_data['no_image_url']) && (!$show_classifieds->IMAGE))
									{
										if (($this->configuration_data['popup_while_browsing'])
											&& ($this->configuration_data['popup_while_browsing_width'])
											&& ($this->configuration_data['popup_while_browsing_height']))
										{
											$this->body .= "<td align=center><a href=\"";
											$this->body .= $this->configuration_data['classifieds_file_name'];
											$this->body .= "?a=2&b=".$show_classifieds->ID."\" ";
											$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
											$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
										}
										else
										{
											$this->body .="<td align=center><a href=".$this->configuration_data['classifieds_file_name'];
											$this->body .="?a=2&b=".$show_classifieds->ID.">";
											$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
										}
									}
									else
										$this->body .="<td>&nbsp;</td>\n\t";
								}
								else
								{
									if ($show_classifieds->IMAGE > 0)
									{
										if (($this->configuration_data['popup_while_browsing'])
											&& ($this->configuration_data['popup_while_browsing_width'])
											&& ($this->configuration_data['popup_while_browsing_height']))
										{
											$this->body .= "<td><a href=\"";
											$this->body .= $this->configuration_data['classifieds_file_name'];
											$this->body .= "?a=2&b=".$show_classifieds->ID."\" ";
											$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
											$this->body .="<img src=".$this->configuration_data['photo_icon_url']." border=0></td>";
										}
										else
										{
											$this->body .="<td><a href=".$this->configuration_data['classifieds_file_name'];
											$this->body .="?a=2&b=".$show_classifieds->ID.">";
											$this->body .="<img src=".$this->configuration_data['photo_icon_url']." border=0></td>";
										}
									}
									elseif (($this->configuration_data['no_image_url']) && (!$show_classifieds->IMAGE))
									{
										if (($this->configuration_data['popup_while_browsing'])
											&& ($this->configuration_data['popup_while_browsing_width'])
											&& ($this->configuration_data['popup_while_browsing_height']))
										{
											$this->body .= "<td align=center><a href=\"";
											$this->body .= $this->configuration_data['classifieds_file_name'];
											$this->body .= "?a=2&b=".$show_classifieds->ID."\" ";
											$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
											$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
										}
										else
										{
											$this->body .="<td align=center><a href=".$this->configuration_data['classifieds_file_name'];
											$this->body .="?a=2&b=".$show_classifieds->ID.">";
											$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
										}
									}
									else
										$this->body .="<td>&nbsp;</td>\n\t";
								}
							}
							if ($this->configuration_data['display_ad_title'])
							{
								$this->body .="<td ";
								if ((($this->configuration_data['display_ad_description'])&& ($this->$this->configuration_data['display_ad_description_where'])) || (!$this->browsing_configuration['display_ad_description']))
									$this->body .= "width=100%";								
								$this->body .=">\n\t\t";
								if ($show_classifieds->SOLD_DISPLAYED)
									$this->body .= "<img src=".$this->configuration_data['sold_image']." border=0> ";
								$this->body .= "<a href=".$this->configuration_data['classifieds_url']."?a=2&b=".$show_classifieds->ID." class=".$css_class_tag.">".stripslashes(urldecode($show_classifieds->TITLE))."</a>\n\t\t";
								if ($show_classifieds->ATTENTION_GETTER)
								{
									$this->body .= "<img src=\"".$show_classifieds->ATTENTION_GETTER_URL."\" border=0 hspace=2>";
								}
								if (($this->configuration_data['display_ad_description']) && ($this->configuration_data['display_ad_description_where']))
								{
									$this->body .="<br>";
									if (strlen(urldecode($show_classifieds->DESCRIPTION)) > $this->configuration_data['length_of_description'])
									{
										$small_string = substr(trim(strip_tags(stripslashes(urldecode($show_classifieds->DESCRIPTION)))),0,$this->configuration_data['length_of_description']);
										$position = strrpos($small_string," ");
										$smaller_string = substr($small_string,0,$position);
										$this->body .=$smaller_string."...";
									}
									else
										$this->body .=	stripslashes(urldecode($show_classifieds->DESCRIPTION));
								}
								$this->body .="</td>\n\t\t";
							}

							if (($this->configuration_data['display_ad_description']) && (!$this->configuration_data['display_ad_description_where']))
							{
								$this->body .="<td >";
								if (!$this->configuration_data['display_all_of_description'])
								{
									if (strlen(urldecode($show_classifieds->DESCRIPTION)) > $this->configuration_data['length_of_description'])
									{
										$small_string = substr(trim(strip_tags(stripslashes(urldecode($show_classifieds->DESCRIPTION)))),0,$this->configuration_data['length_of_description']);
										$position = strrpos($small_string," ");
										$smaller_string = substr($small_string,0,$position);
										$this->body .=$smaller_string."...";
									}
									else
										$this->body .=	stripslashes(urldecode($show_classifieds->DESCRIPTION));
								}
								else
									$this->body .=	stripslashes(urldecode($show_classifieds->DESCRIPTION));
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_1'])
							{
								$this->body .="<td   nowrap align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_1))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_1));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_2'])
							{
								$this->body .="<td   nowrap align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_2))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_2));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_3'])
							{
								$this->body .="<td   nowrap align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_3))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_3));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_4'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_4))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_4));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_5'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_5))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_5));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_6'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_6))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_6));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_7'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_7))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_7));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_8'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_8))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_8));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_9'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_9))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_9));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_10'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_10))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_10));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_11'])
							{
								$this->body .="<td   nowrap align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_11))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_11));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_12'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_12))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_12));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_13'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_13))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_13));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_14'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_14))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_14));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_15'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_15))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_15));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_16'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_16))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_16));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_17'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_17))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_17));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_18'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_18))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_18));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_19'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_19))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_19));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_20'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_20))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_20));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_browsing_city_field'])
							{
								$this->body .="<td   nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->LOCATION_CITY))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_CITY));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_browsing_state_field'])
							{
								$this->body .="<td   nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->LOCATION_STATE))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_STATE));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_browsing_country_field'])
							{
								$this->body .="<td   nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->LOCATION_COUNTRY))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_COUNTRY));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_browsing_zip_field'])
							{
								$this->body .="<td  nowrap align=center >";
									if (strlen(trim(urldecode($show_classifieds->LOCATION_ZIP))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_ZIP));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_price'])
							{
								$this->body .="<td  nowrap align=center >";
								if (((strlen(trim(urldecode($show_classifieds->PRICE))) > 0)
									|| (strlen(trim(urldecode($show_classifieds->PRECURRENCY))) > 0)
									|| (strlen(trim(urldecode($show_classifieds->POSTCURRENCY))) > 0)) && ($show_classifieds->PRICE != 0))
								{
									if (floor($show_classifieds->PRICE) == $show_classifieds->PRICE)
									{
										$this->body .= stripslashes(urldecode($show_classifieds->PRECURRENCY)). " ".
											number_format($show_classifieds->PRICE)." ".
											stripslashes(urldecode($show_classifieds->POSTCURRENCY));
									}
									else
									{
										$this->body .= stripslashes(urldecode($show_classifieds->PRECURRENCY)). " ".
											number_format($show_classifieds->PRICE,2,".",",")." ".
											stripslashes(urldecode($show_classifieds->POSTCURRENCY));
									}
								}
								elseif (((strlen(trim(urldecode($show_classifieds->MINIMUM_BID))) > 0)
									|| (strlen(trim(urldecode($show_classifieds->PRECURRENCY))) > 0)
									|| (strlen(trim(urldecode($show_classifieds->POSTCURRENCY))) > 0)) && ($show_classifieds->MINIMUM_BID != 0))
								{
									if (floor($show_classifieds->MINIMUM_BID) == $show_classifieds->MINIMUM_BID)
									{
										$this->body .= stripslashes(urldecode($show_classifieds->PRECURRENCY)). " ".
											number_format($show_classifieds->MINIMUM_BID)." ".
											stripslashes(urldecode($show_classifieds->POSTCURRENCY));
									}
									else
									{
										$this->body .= stripslashes(urldecode($show_classifieds->PRECURRENCY)). " ".
											number_format($show_classifieds->MINIMUM_BID,2,".",",")." ".
											stripslashes(urldecode($show_classifieds->POSTCURRENCY));
									}
								}
								else
									$this->body .=	stripslashes(urldecode($show_classifieds->PRECURRENCY))." - ".stripslashes(urldecode($show_classifieds->POSTCURRENCY));
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_entry_date'])
							{
								//&& ($show_classifieds->ITEM_TYPE == 1
									//|| ($this->configuration_data['auction_entry_date'] 
										//&& $show_classifieds->ITEM_TYPE == 2)))
								if($show_classifieds->ITEM_TYPE == 1
									|| ($this->configuration_data['auction_entry_date'] 
										&& $show_classifieds->ITEM_TYPE == 2))
								{
									$this->body .="<td  nowrap align=center >".date(trim($this->configuration_data['entry_date_configuration']),$show_classifieds->DATE)."</td>\n\t";
								}
								else
								{
									$this->body .="<td  nowrap align=center >-</td>\n\t";
								}
							}

							if($this->configuration_data['display_time_left'])
							{
								$weeks = $this->DateDifference(w,$this->shifted_time($db),$show_classifieds->ENDS);
								$remaining_weeks = ($weeks * 604800);

								// Find days left
								$days = $this->DateDifference(d,($this->shifted_time($db)+$remaining_weeks),$show_classifieds->ENDS);
								$remaining_days = ($days * 86400);

								// Find hours left
								$hours = $this->DateDifference(h,($this->shifted_time($db)+$remaining_days),$show_classifieds->ENDS);
								$remaining_hours = ($hours * 3600);

								// Find minutes left
								$minutes = $this->DateDifference(m,($this->shifted_time($db)+$remaining_hours),$show_classifieds->ENDS);
								$remaining_minutes = ($minutes * 60);

								// Find seconds left
								$seconds = $this->DateDifference(s,($this->shifted_time($db)+$remaining_minutes),$show_classifieds->ENDS);
								if(($weeks <= 0) && ($days <= 0) && ($hours <= 0) && ($minutes <= 0) && ($seconds <= 0))
								{
									// If closed we want to display closed text
									$this->body .=  "<td align=center><div nowrap class=auction_closed>";
									$this->body .= urldecode($this->messages[100051]);
								}
								else
								{
									$this->body .=  "<td align=center><div nowrap>";
								}

								if((($show_classifieds->ITEM_TYPE == 2) 
									|| ($this->configuration_data['classified_time_left'] && ($show_classifieds->ITEM_TYPE == 1))))
								{
									if ($weeks > 0)
									{
										$this->body .= $weeks." ".stripslashes(urldecode($this->messages[3284])).", ".$days." ".stripslashes(urldecode($this->messages[103004]));
									}
									elseif ($days > 0)
									{
										$this->body .= $days." ".stripslashes(urldecode($this->messages[3285])).", ".$hours." ".stripslashes(urldecode($this->messages[103005]));
									}
									elseif ($hours > 0)
									{
										$this->body .= $hours." ".stripslashes(urldecode($this->messages[3286])).", ".$minutes." ".stripslashes(urldecode($this->messages[103006]));
									}
									elseif ($minutes > 0)
									{
										$this->body .= $minutes." ".stripslashes(urldecode($this->messages[3287])).", ".$seconds." ".stripslashes(urldecode($this->messages[103007]));
									}
									elseif ($seconds > 0)
									{
										$this->body .= $seconds." ".stripslashes(urldecode($this->messages[3288]));
									}
								}
								else
								{
									$this->body .= "-";
								}
								$this->body .= "</div></td>\n\t";
							}
							elseif($this->configuration_data['display_time_left'])
							{
								$this->body .= "<td  nowrap align=center>-</td>\n\t";
							}

							if ($this->classified_user_id == 1)
							{
								//this is the admin
								$this->body .="<td >\n\t\t<a href=".$this->configuration_data['classifieds_url']."?a=4&e=".$show_classifieds->ID."&b=5>edit</a>\n\t\t</td>\n\t";
								$this->body .="<td >\n\t\t<a href=".$this->configuration_data['classifieds_url']."?a=99&b=".$show_classifieds->ID."&c=".$category.">delete</a>\n\t\t</td>\n\t";

							}
							$this->body .="</tr>\n\t";
							$this->row_count++;
						} //end of while
						$this->body .="</table>\n\t</td>\n</tr>\n";
					}
					else
					{
						//no classifieds in this category
						$this->body .="<tr class=no_ads_in_category>\n\t<td >\n\t".urldecode($this->messages[17])."\n\t</td>\n</tr>\n";

					}
		return;
	} //end of function display_browse_result

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

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
		exit;
	 } //end of function browse_error

//####################################################################################

} // end of class Browse_display_sellers_ads

?>