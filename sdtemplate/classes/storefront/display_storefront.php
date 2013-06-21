<?php
class Display_Storefront extends Store
{
	var $storefrontTemplate = '';
	var $displayPage = false;
	var $pageInfo = Array();
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function Display_Storefront($db,$language_id,$classified_user_id=0,$product_configuration=0,$storefront_id=0)
	{
		$this->Store($db,6,$language_id,$classified_user_id,$product_configuration,$storefront_id);
	} //end of function User_management_storefront
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function generateListings($db,$classified_user_id,$language_id,$category_id=0,$page=0,$classified_id=0,$filter_id=0,$state_filter=0,$zip_filter=0,$zip_filter_distance=0,$product_configuration=0,$browse_type=0)
	{
		$this->body = '';
		if ($category_id)
			$this->site_category = $category_id;
		elseif ($classified_id)
		{
			$show = $this->get_classified_data($db,$classified_id);
			$this->site_category = $show['category'];
		}
		else
			$this->site_category = 0;
		if ($limit)
			$this->browse_limit = $limit;
		$this->Site($db,1,$language_id,$classified_user_id, $product_configuration);
		if ($page)
			$this->page_result = $page;
		else
			$this->page_result = 1;

		$this->filter_id = $filter_id;
		$this->state_filter = $state_filter;
		$this->zip_filter = $zip_filter;
		$this->zip_filter_distance = $zip_filter_distance;
		
		$this->browse_type = $browse_type;
		$this->page_id = 3;
		$this->get_text($db, 0, 10003);
		if ($this->filter_id)
		{
			//add filter association to end of sql_query
			$filter_in_statement = $this->get_sql_filter_in_statement($db);
			$this->sql_filter_in_statement = " and filter_id ".$filter_in_statement." ";
		}
		if ($this->state_filter)
		{
			//add state to end of sql_query
			$this->sql_state_filter_statement = " and location_state = \"".$this->state_filter."\" ";
		}
		if (($this->zip_filter_distance) && ($this->zip_filter))
		{
			//add zip code in statement to end of sql_query
			$zip_filter_in_statement = $this->get_sql_zip_filter_in_statement($db);
			$this->sql_zip_filter_in_statement = "and ".$zip_filter_in_statement." ";
			if ($this->debug_browse) $this->sql_zip_filter_in_statement." is the zip filter in statement<bR>\n";
		}
		$this->body .="<table class=body_table id=body_table cellpadding=0 cellspacing=0 border=0 width=100%>\n";
		
		$sqlCategoryCondition = ($this->site_category) ? "storefront_category = ".$this->site_category." and " : "";

		$this->sql_query = "select * from ".$this->classifieds_table."
			where $sqlCategoryCondition live = 1 and seller = ".$this->storefront_id." ";

		if ($this->filter_id)
			$this->sql_query .= $this->sql_filter_in_statement;
		if ($this->state_filter)
			$this->sql_query .= $this->sql_state_filter_statement;
		if (($this->zip_filter_distance) && ($this->zip_filter))
			$this->sql_query .= $this->sql_zip_filter_in_statement;
		if ($this->debug_browse) echo $this->sql_query." at first<bR>\n";
		switch ($browse_type)
		{
			case 0: //nothing
 				$order_by = "order by better_placement desc,date desc ";
				break;
			case 1: //price desc
				$order_by = " order by price desc, better_placement desc ";
				break;
			case 2: //price asc
				$order_by = " order by price asc, better_placement desc ";
				break;
			case 3: //date earliest to latest
				$order_by = " order by date asc, better_placement desc ";
				break;
			case 4: //date latest to earliest
				$order_by = " order by date desc, better_placement desc ";
				break;
			case 5: //title asc
				$order_by = " order by title asc, better_placement desc ";
				break;
			case 6: //title desc
				$order_by = " order by title desc, better_placement desc ";
				break;
			case 7: //city asc
				$order_by = " order by location_city asc, better_placement desc ";
				break;
			case 8: //city desc
				$order_by = " order by location_city desc, better_placement desc ";
				break;
			case 9: //state asc
				$order_by = " order by location_state asc, better_placement desc ";
				break;
			case 10: //state desc
				$order_by = " order by location_state desc, better_placement desc ";
				break;
			case 11: //country asc
				$order_by = " order by location_country asc, better_placement desc ";
				break;
			case 12: //country desc
				$order_by = " order by location_country desc, better_placement desc ";
				break;
			case 13: //zip asc
				$order_by = " order by location_zip asc, better_placement desc ";
				break;
			case 14: //zip desc
				$order_by = " order by location_zip desc, better_placement desc ";
				break;
			case 15: //optional field 1 asc
				$order_by = " order by optional_field_1 asc, better_placement desc ";
				break;
			case 16: //optional field 1 desc
				$order_by = " order by optional_field_1 desc, better_placement desc ";
				break;
			case 17: //optional field 2 asc
				$order_by = " order by optional_field_2 asc, better_placement desc ";
				break;
			case 18: //optional field 2 desc
				$order_by = " order by optional_field_2 desc, better_placement desc ";
				break;
			case 19: //optional field 3 asc
				$order_by = " order by optional_field_3 asc, better_placement desc ";
				break;
			case 20: //optional field 3 desc
				$order_by = " order by optional_field_3 desc, better_placement desc ";
				break;
			case 21: //optional field 4 asc
				$order_by = " order by optional_field_4 asc, better_placement desc ";
				break;
			case 22: //optional field 4 desc
				$order_by = " order by optional_field_4 desc, better_placement desc ";
				break;
			case 23: //optional field 5 asc
				$order_by = " order by optional_field_5 asc, better_placement desc ";
				break;
			case 24: //optional field 5 desc
				$order_by = " order by optional_field_5 desc, better_placement desc ";
				break;
			case 25: //optional field 6 asc
				$order_by = " order by optional_field_6 asc, better_placement desc ";
				break;
			case 26: //optional field 6 desc
				$order_by = " order by optional_field_6 desc, better_placement desc ";
				break;
			case 27: //optional field 7 asc
				$order_by = " order by optional_field_7 asc, better_placement desc ";
				break;
			case 28: //optional field 7 desc
				$order_by = " order by optional_field_7 desc, better_placement desc ";
				break;
			case 29: //optional field 8 asc
				$order_by = " order by optional_field_8 asc, better_placement desc ";
				break;
			case 30: //optional field 8 desc
				$order_by = " order by optional_field_8 desc, better_placement desc ";
				break;
			case 31: //optional field 9 asc
				$order_by = " order by optional_field_9 asc, better_placement desc ";
				break;
			case 32: //optional field 9 desc
				$order_by = " order by optional_field_9 desc, better_placement desc ";
				break;
			case 33: //optional field 10 asc
				$order_by = " order by optional_field_10 asc, better_placement desc ";
				break;
			case 34: //optional field 10 desc
				$order_by = " order by optional_field_10 desc, better_placement desc ";
				break;
			case 35: //city asc
				$order_by = " order by location_city asc, better_placement desc ";
				break;
			case 36: //city desc
				$order_by = " order by location_city desc, better_placement desc ";
				break;
			case 37: //state asc
				$order_by = " order by location_state asc, better_placement desc ";
				break;
			case 38: //state desc
				$order_by = " order by location_state desc, better_placement desc ";
				break;
			case 39: //country asc
				$order_by = " order by location_country asc, better_placement desc ";
				break;
			case 40: //country desc
				$order_by = " order by location_country desc, better_placement desc ";
				break;
			case 41: //zip asc
				$order_by = " order by location_zip asc, better_placement desc ";
				break;
			case 42: //zip desc
				$order_by = " order by location_zip desc, better_placement desc ";
				break;
			case 43: //business_type asc
				$order_by = " order by business_type asc, better_placement desc ";
				break;
			case 44: //business_type desc
				$order_by = " order by business_type desc, better_placement desc ";
				break;
			case 45: //optional field 11 asc
				$order_by = " order by optional_field_11 asc, better_placement desc ";
				break;
			case 46: //optional field 11 desc
				$order_by = " order by optional_field_11 desc, better_placement desc ";
				break;
			case 47: //optional field 12 asc
				$order_by = " order by optional_field_12 asc, better_placement desc ";
				break;
			case 48: //optional field 12 desc
				$order_by = " order by optional_field_12 desc, better_placement desc ";
				break;
			case 49: //optional field 13 asc
				$order_by = " order by optional_field_13 asc, better_placement desc ";
				break;
			case 50: //optional field 13 desc
				$order_by = " order by optional_field_13 desc, better_placement desc ";
				break;
			case 51: //optional field 14 asc
				$order_by = " order by optional_field_14 asc, better_placement desc ";
				break;
			case 52: //optional field 14 desc
				$order_by = " order by optional_field_14 desc, better_placement desc ";
				break;
			case 53: //optional field 15 asc
				$order_by = " order by optional_field_15 asc, better_placement desc ";
				break;
			case 54: //optional field 15 desc
				$order_by = " order by optional_field_15 desc, better_placement desc ";
				break;
			case 55: //optional field 16 asc
				$order_by = " order by optional_field_16 asc, better_placement desc ";
				break;
			case 56: //optional field 16 desc
				$order_by = " order by optional_field_16 desc, better_placement desc ";
				break;
			case 57: //optional field 17 asc
				$order_by = " order by optional_field_17 asc, better_placement desc ";
				break;
			case 58: //optional field 17 desc
				$order_by = " order by optional_field_17 desc, better_placement desc ";
				break;
			case 59: //optional field 18 asc
				$order_by = " order by optional_field_18 asc, better_placement desc ";
				break;
			case 60: //optional field 18 desc
				$order_by = " order by optional_field_18 desc, better_placement desc ";
				break;
			case 61: //optional field 19 asc
				$order_by = " order by optional_field_19 asc, better_placement desc ";
				break;
			case 62: //optional field 19 desc
				$order_by = " order by optional_field_19 desc, better_placement desc ";
				break;
			case 63: //optional field 20 asc
				$order_by = " order by optional_field_20 asc, better_placement desc ";
				break;
			case 64: //optional field 20 desc
				$order_by = " order by optional_field_20 desc, better_placement desc ";
				break;
			default:
				$order_by = "order by better_placement desc,date desc ";
				break;
		}
		$this->sql_query_classifieds = $this->sql_query." and item_type = 1 ".$order_by." limit ".(($this->page_result -1) * $this->configuration_data['number_of_ads_to_display']).",".$this->configuration_data['number_of_ads_to_display'];
		$result = $db->Execute($this->sql_query_classifieds);
		$this->sql_query_auctions = $this->sql_query." and item_type = 2 ".$order_by." limit ".(($this->page_result -1) * $this->configuration_data['number_of_ads_to_display']).",".$this->configuration_data['number_of_ads_to_display'];
		$result_auctions = $db->Execute($this->sql_query_auctions);

		//echo $this->sql_query." is the query<br>\n";
		$this->sql_query_count = "select count(id) as total from ".$this->classifieds_table." where
			where $sqlCategoryCondition live = 1 and seller = ".$this->storefront_id." ";
		if ($this->filter_id)
			$this->sql_query_count .= $this->sql_filter_in_statement;
		if ($this->state_filter)
			$this->sql_query_count .= $this->sql_state_filter_statement;
		if (($this->zip_filter_distance) && ($this->zip_filter))
			$this->sql_query_count .= $this->sql_zip_filter_in_statement;
		$this->sql_query_count .= " group by item_type";
		if ($this->debug_browse)  echo $this->sql_query_count." at top<br>\n";
		if ($this->debug_browse) echo $this->sql_query."<Br>\n";
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
				if ($this->debug_browse)  echo $this->sql_query_count." is the query<br>\n";
				if ($total_count_result)
				{
					$show_total = $total_count_result->FetchRow();
					$total_returned_ads = $show_total['total'];
					$show_total = $total_count_result->FetchRow();
					$total_returned_auctions = $show_total['total'];
					if($total_returned_ads>$total_returned_auctions)
						$total_returned = $total_returned_ads;
					else
						$total_returned = $total_returned_auctions;	
					if ($this->debug_browse) $total_returned." is the total returned<br>\n";
				}
			}
			if ($this->get_storefront_configuration($db))
			{
				$this->browsing_configuration = $this->storefront_configuration;
				if ($this->debug_browse) echo "using category specific settings<br>\n";
			}
			
			//get category name
			if($this->site_category)
				$current_category_name = $this->getStorefrontCategoryName($db, $this->site_category);
			else
				$current_category_name = $this->get_user_name($db, $this->storefront_id);
			
			if($this->is_classifieds()||$this->is_class_auctions())
			{
				if ($result->RecordCount() > 0)
					$this->body .="<tr class=normal_results_header id=normal_results_header>\n\t<td class=normal_result_colum id=normal_result_colum>".urldecode($this->messages[200109])." ".$current_category_name."</td></tr>";
				$result->Move(0);
				$this->display_browse_result($db,$result,"browsing_result_table_header");
			}
				
			if($this->is_auctions()||$this->is_class_auctions())
			{
				if ($result_auctions->RecordCount() > 0)
					$this->body .="<tr class=normal_results_header id=normal_results_header>\n\t<td class=normal_result_colum id=normal_result_colum>".urldecode($this->messages[200110])." ".$current_category_name."</td></tr>";
				$this->display_browse_result($db,$result_auctions,"browsing_result_table_header",0,1);
			}

			if ($this->configuration_data['number_of_ads_to_display'] < $total_returned)
			{
				//display the link to the next 10
				$number_of_page_results = ceil($total_returned / $this->configuration_data['number_of_ads_to_display']);
				$this->body .="<tr class=browsing_result_page_links>\n\t<td ><font class=more_results>".urldecode($this->messages[25])." ".$this->page_result."</font><font class=page_of>".urldecode($this->messages[26])."</font> <font class=more_results>".ceil($total_returned / $this->configuration_data['number_of_ads_to_display'])."</font></td>\n</tr>\n";
			}

			if ($this->configuration_data['number_of_ads_to_display'] < $total_returned)
			{
				//display the link to the next 10
				$number_of_page_results = ceil($total_returned / $this->configuration_data['number_of_ads_to_display']);
				$this->body .="<tr class=more_results>\n\t<td >".urldecode($this->messages[24])." ";
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
							$this->body .="<a href=".$this->configuration_data['storefront_url']."?store=".$this->storefront_id."&b=".$this->site_category."&page=".$i;
							if ($browse_type) $this->body .= "&c=".$browse_type;
							$this->body .=" class=browsing_result_page_links>".$i."</a> ";
						}
					}
				}
				elseif($number_of_page_results < 100)
				{
					$number_of_sections =  ceil($number_of_page_results/10);
					for ($section = 0;$section < $number_of_sections;$section++)
					{
						if (($this->page_result > ($section * 10)) && ($this->page_result <= (($section+1) * 10)))
						{
							//display the individual pages within this section
							for ($page = (($section * 10) + 1);$page <= (($section+1) * 10);$page++)
							{
								if($page == $this->page_result)
								{
									$this->body .=" <b>".$page."</b> ";
									continue;
								}
								if ($page <= $number_of_page_results)
								{
									$this->body .="<a href=".$this->configuration_data['storefront_url']."?store=".$this->storefront_id."&b=".$this->site_category."&page=".$page;
									if ($browse_type) $this->body .= "&c=".$browse_type;
									$this->body .= " class=browsing_result_page_links>".$page."</a> ";
								}
							}

						}
						else
						{
							//display the link to the section
							$this->body .="<a href=".$this->configuration_data['storefront_url']."?store=".$this->storefront_id."&b=".$this->site_category."&page=".(($section*10)+1);
							if ($browse_type) $this->body .= "&c=".$browse_type;
							$this->body .= " class=browsing_result_page_links>".(($section*10)+1)."</a>";
						}
						if (($section+1) < $number_of_sections)
							$this->body .= " <font class=browsing_result_page_links>..</font> ";
					}
				}
				else
				{
					$number_of_sections =  ceil($number_of_page_results/100);
					for ($section = 0;$section < $number_of_sections;$section++)
					{
						if (($this->page_result > ($section * 100)) && ($this->page_result <= (($section+1) * 100)))
						{
							//display tens
							for ($page = (($section * 100) + 1);$page <= (($section+1) * 100);$page+=10)
							{

								if (($this->page_result >= $page) && ($this->page_result <= ($page+9)))
								{
									//display ones
									for ($page_link = $page;$page_link <= ($page+9);$page_link++)
									{
										if($page_link == $this->page_result)
										{
											$this->body .=" <b>".$page_link."</b> ";
											continue;
										}
										if ($page_link <= $number_of_page_results)
										{
											$this->body .="<a href=".$this->configuration_data['storefront_url']."?store=".$this->storefront_id."&b=".$this->site_category."&page=".$page_link;
											if ($browse_type) $this->body .= "&c=".$browse_type;
											$this->body .= " class=browsing_result_page_links>".$page_link."</a> ";
										}
									}

								}else
								{
									if($page == $this->page_result)
									{
										$this->body .=" <b>".$page."</b> ";
										continue;
									}
									if ($page <= $number_of_page_results)
									{
										$this->body .="<a href=".$this->configuration_data['storefront_url']."?store=".$this->storefront_id."&b=".$this->site_category."&page=".$page;
										if ($browse_type) $this->body .= "&c=".$browse_type;
										$this->body .= " class=browsing_result_page_links>".$page."</a> ";
										if (($section+1) < $number_of_sections)
											$this->body .= " <font class=browsing_result_page_links>..</font> ";
									}
								}
							}

						}
						else
						{
							//display hundreds
							$this->body .="<a href=".$this->configuration_data['storefront_url']."?store=".$this->storefront_id."&b=".$this->site_category."&page=".(($section*100)+1);
							if ($browse_type) $this->body .= "&c=".$browse_type;
								$this->body .= " class=browsing_result_page_links>".(($section*100)+1)."</a>";
							if (($section+1) < $number_of_sections)
								$this->body .= " <font class=browsing_result_page_links>..</font> ";
						}
					}
				}
				$this->body .="</td>\n</tr>\n";
			}
		}
		$this->body .="</table>\n";
		$this->storefrontTemplate = str_replace('(!STOREFRONT_LISTINGS!)',$this->body, $this->storefrontTemplate);
		
	} //end of function browse

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_browse_result($db,$browse_result,$header_css,$featured=0,$auction=0)
	{
		if ($browse_result->RecordCount() > 0)
		{
			$browse_result->Move(0);
			$link_text = "<a href=".$this->configuration_data['storefront_url']."?store=".$this->storefront_id."&b=".$this->site_category."&c=";
			//display the ads inside of this category
			$this->body .="<tr class=listings_row id=listings_row>\n\t<td class=listings_column id=listings_column height=20>\n\t";
			$this->body .="<table id=listings_table class=listings_table cellpadding=0 cellspacing=0 border=0 align=center width=100%>\n\t";
			$this->body .="<tr class=header_row id=header_row>\n\t\t";
			if ($this->browsing_configuration['display_business_type'])
			{
				$this->body .="<td class=business_type_column_header id=header_column>".$link_text;
				if ($this->browse_type == 43) $this->body .= "44";
				elseif ($this->browse_type == 44) $this->body .= "0";
				else $this->body .= "43";
				$this->body .= " class=business_type_column_header>".urldecode($this->messages[1262])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_photo_icon'])
				$this->body .= "<td  class=photo_column_header id=header_column>".urldecode($this->messages[23])."</td>\n\t";
			if ($this->browsing_configuration['display_ad_title'])
			{
				$this->body .="<td  class=title_column_header id=header_column>".$link_text;
				if ($this->browse_type == 5) $this->body .= "6";
				elseif ($this->browse_type == 6) $this->body .= "0";
				else $this->body .= "5";
				$this->body .= " class=title_column_header>".urldecode($this->messages[19])."</a>";
				if (($this->browsing_configuration['display_ad_description'])&& ($this->browsing_configuration['display_ad_description_where']))
					$this->body .="<br>".urldecode($this->messages[21]);
	
				$this->body .="</td>\n\t\t";
			}
	
			if (($this->browsing_configuration['display_ad_description'])&& (!$this->browsing_configuration['display_ad_description_where']))
				$this->body .="<td   class=description_column_header id=header_column>".urldecode($this->messages[21])."</td>\n\t";
	
			if ($this->browsing_configuration['display_optional_field_1'])
			{
				$this->body .="<td class=optional_field_header_1 id=header_column>".$link_text;
				if ($this->browse_type == 15) $this->body .= "16";
				elseif ($this->browse_type == 16) $this->body .= "0";
				else $this->body .= "15";
				$this->body .= " class=optional_field_header_1>".urldecode($this->messages[922])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_2'])
			{
				$this->body .="<td class=optional_field_header_2 id=header_column>".$link_text;
				if ($this->browse_type == 17) $this->body .= "18";
				elseif ($this->browse_type == 18) $this->body .= "0";
				else $this->body .= "17";
				$this->body .= " class=optional_field_header_2>".urldecode($this->messages[923])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_3'])
			{
				$this->body .="<td class=optional_field_header_3 id=header_column>".$link_text;
				if ($this->browse_type == 19) $this->body .= "20";
				elseif ($this->browse_type == 20) $this->body .= "0";
				else $this->body .= "19";
				$this->body .= " class=optional_field_header_3>".urldecode($this->messages[924])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_4'])
			{
				$this->body .="<td class=optional_field_header_4 id=header_column>".$link_text;
				if ($this->browse_type == 21) $this->body .= "22";
				elseif ($this->browse_type == 22) $this->body .= "0";
				else $this->body .= "21";
				$this->body .= " class=optional_field_header_4>".urldecode($this->messages[925])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_5'])
			{
				$this->body .="<td class=optional_field_header_5 id=header_column>".$link_text;
				if ($this->browse_type == 23) $this->body .= "24";
				elseif ($this->browse_type == 24) $this->body .= "0";
				else $this->body .= "23";
				$this->body .= " class=optional_field_header_5>".urldecode($this->messages[926])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_6'])
			{
				$this->body .="<td class=optional_field_header_6 id=header_column>".$link_text;
				if ($this->browse_type == 25) $this->body .= "26";
				elseif ($this->browse_type == 26) $this->body .= "0";
				else $this->body .= "25";
				$this->body .= " class=optional_field_header_6>".urldecode($this->messages[927])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_7'])
			{
				$this->body .="<td class=optional_field_header_7 id=header_column>".$link_text;
				if ($this->browse_type == 27) $this->body .= "28";
				elseif ($this->browse_type == 28) $this->body .= "0";
				else $this->body .= "27";
				$this->body .= " class=optional_field_header_7>".urldecode($this->messages[928])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_8'])
			{
				$this->body .="<td class=optional_field_header_8 id=header_column>".$link_text;
				if ($this->browse_type == 29) $this->body .= "30";
				elseif ($this->browse_type == 30) $this->body .= "0";
				else $this->body .= "29";
				$this->body .= " class=optional_field_header_8>".urldecode($this->messages[929])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_9'])
			{
				$this->body .="<td class=optional_field_header_9 id=header_column>".$link_text;
				if ($this->browse_type == 31) $this->body .= "32";
				elseif ($this->browse_type == 32) $this->body .= "0";
				else $this->body .= "31";
				$this->body .= " class=optional_field_header_9>".urldecode($this->messages[930])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_10'])
			{
				$this->body .="<td class=optional_field_header_10 id=header_column>".$link_text;
				if ($this->browse_type == 33) $this->body .= "34";
				elseif ($this->browse_type == 34) $this->body .= "0";
				else $this->body .= "33";
				$this->body .= " class=optional_field_header_10>".urldecode($this->messages[931])."</a></td>\n\t";
			}
	
			if ($this->browsing_configuration['display_optional_field_11'])
			{
				$this->body .="<td class=optional_field_header_11 id=header_column>".$link_text;
				if ($this->browse_type == 45) $this->body .= "46";
				elseif ($this->browse_type == 46) $this->body .= "0";
				else $this->body .= "45";
				$this->body .= " class=optional_field_header_11>".urldecode($this->messages[1696])."</a></td>\n\t";
			}
	
			if ($this->browsing_configuration['display_optional_field_12'])
			{
				$this->body .="<td class=optional_field_header_12 id=header_column>".$link_text;
				if ($this->browse_type == 47) $this->body .= "48";
				elseif ($this->browse_type == 48) $this->body .= "0";
				else $this->body .= "47";
				$this->body .= " class=optional_field_header_12>".urldecode($this->messages[1697])."</a></td>\n\t";
			}
	
			if ($this->browsing_configuration['display_optional_field_13'])
			{
				$this->body .="<td class=optional_field_header_13 id=header_column>".$link_text;
				if ($this->browse_type == 49) $this->body .= "50";
				elseif ($this->browse_type == 50) $this->body .= "0";
				else $this->body .= "49";
				$this->body .= " class=optional_field_header_13>".urldecode($this->messages[1698])."</a></td>\n\t";
			}
	
			if ($this->browsing_configuration['display_optional_field_14'])
			{
				$this->body .="<td class=optional_field_header_14 id=header_column>".$link_text;
				if ($this->browse_type == 51) $this->body .= "52";
				elseif ($this->browse_type == 52) $this->body .= "0";
				else $this->body .= "51";
				$this->body .= " class=optional_field_header_14>".urldecode($this->messages[1699])."</a></td>\n\t";
			}
	
			if ($this->browsing_configuration['display_optional_field_15'])
			{
				$this->body .="<td class=optional_field_header_15 id=header_column>".$link_text;
				if ($this->browse_type == 53) $this->body .= "54";
				elseif ($this->browse_type == 54) $this->body .= "0";
				else $this->body .= "53";
				$this->body .= " class=optional_field_header_15>".urldecode($this->messages[1700])."</a></td>\n\t";
			}
	
			if ($this->browsing_configuration['display_optional_field_16'])
			{
				$this->body .="<td class=optional_field_header_16 id=header_column>".$link_text;
				if ($this->browse_type == 55) $this->body .= "56";
				elseif ($this->browse_type == 56) $this->body .= "0";
				else $this->body .= "55";
				$this->body .= " class=optional_field_header_16>".urldecode($this->messages[1701])."</a></td>\n\t";
			}
	
			if ($this->browsing_configuration['display_optional_field_17'])
			{
				$this->body .="<td class=optional_field_header_17 id=header_column>".$link_text;
				if ($this->browse_type == 57) $this->body .= "58";
				elseif ($this->browse_type == 58) $this->body .= "0";
				else $this->body .= "57";
				$this->body .= " class=optional_field_header_17>".urldecode($this->messages[1702])."</a></td>\n\t";
			}
	
			if ($this->browsing_configuration['display_optional_field_18'])
			{
				$this->body .="<td class=optional_field_header_18 id=header_column>".$link_text;
				if ($this->browse_type == 59) $this->body .= "60";
				elseif ($this->browse_type == 60) $this->body .= "0";
				else $this->body .= "59";
				$this->body .= " class=optional_field_header_18>".urldecode($this->messages[1703])."</a></td>\n\t";
			}
	
			if ($this->browsing_configuration['display_optional_field_19'])
			{
				$this->body .="<td class=optional_field_header_19 id=header_column>".$link_text;
				if ($this->browse_type == 61) $this->body .= "62";
				elseif ($this->browse_type == 62) $this->body .= "0";
				else $this->body .= "61";
				$this->body .= " class=optional_field_header_19>".urldecode($this->messages[1704])."</a></td>\n\t";
			}
	
			if ($this->browsing_configuration['display_optional_field_20'])
			{
				$this->body .="<td class=optional_field_header_20 id=header_column>".$link_text;
				if ($this->browse_type == 63) $this->body .= "64";
				elseif ($this->browse_type == 64) $this->body .= "0";
				else $this->body .= "63";
				$this->body .= " class=optional_field_header_20>".urldecode($this->messages[1705])."</a></td>\n\t";
			}
	
			if ($this->browsing_configuration['display_browsing_city_field'])
			{
				$this->body .="<td class=city_column_header id=header_column>".$link_text;
				if ($this->browse_type == 7) $this->body .= "8";
				elseif ($this->browse_type == 8) $this->body .= "0";
				else $this->body .= "7";
				$this->body .= " class=city_column_header>".urldecode($this->messages[1199])."</a></td>\n\t";
			}
	
			if ($this->browsing_configuration['display_browsing_state_field'])
			{
				$this->body .="<td class=state_column_header id=header_column>".$link_text;
				if ($this->browse_type == 37) $this->body .= "38";
				elseif ($this->browse_type == 38) $this->body .= "0";
				else $this->body .= "37";
				$this->body .= " class=state_column_header>".urldecode($this->messages[1200])."</td>\n\t";
			}
			if ($this->browsing_configuration['display_browsing_country_field'])
			{
				$this->body .="<td class=country_column_header id=header_column>".$link_text;
				if ($this->browse_type == 39) $this->body .= "40";
				elseif ($this->browse_type == 40) $this->body .= "0";
				else $this->body .= "39";
				$this->body .= " class=country_column_header>".urldecode($this->messages[1201])."</td>\n\t";
			}
			if ($this->browsing_configuration['display_browsing_zip_field'])
			{
				$this->body .="<td class=zip_column_header id=header_column>".$link_text;
				if ($this->browse_type == 41) $this->body .= "42";
				elseif ($this->browse_type == 42) $this->body .= "0";
				else $this->body .= "41";
				$this->body .= " class=zip_column_header>".urldecode($this->messages[1202])."</td>\n\t";
			}
	
			if ($auction && $this->browsing_configuration['display_number_bids'])
			{
				$this->body .= "<td class=number_bids_header id=header_column>".urldecode($this->messages[103041])."</td>\n\t";
			}
	
			if ($this->browsing_configuration['display_price'])
			{
				$this->body .="<td  class=price_column_header id=header_column>".$link_text;
				//$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category;
				if ($this->browse_type == 1) $this->body .= "2";
				elseif ($this->browse_type == 2) $this->body .= "0";
				else $this->body .= "1";
				$this->body .= " class=price_column_header>".urldecode($this->messages[27])."</td>\n\t";
			}
	
			if ($this->browsing_configuration['display_entry_date']
				&& (!$auction || ($this->browsing_configuration['auction_entry_date'] && $auction)))
				$this->body .="<td  class=entry_date_column_header id=header_column>".urldecode($this->messages[22])."</td>\n\t";
	
			if ($this->browsing_configuration['display_time_left']
				&& ($auction || ($this->browsing_configuration['classified_time_left'] && !$auction)))
			{
				$this->body .=  "<td class=time_left_header id=header_column><div nowrap>".urldecode($this->messages[103008])."</div></td>\n\t";
			}
	
			if ($this->classified_user_id == 1)
			{
				//this is the admin
				$this->body .="<td>edit</td>\n\t";
				$this->body .="<td >delete</td>\n\t";
			}
	
			$this->body .="</tr>\n\t";
	
			//Display Data
			$this->row_count = 0;
			while ($show_classifieds = $browse_result->FetchRow())
			{
				if (($this->row_count % 2) == 0)
				{
					if ($show_classifieds['bolding'])
						$css_class_tag= "listing_even_bold";
					else
						$css_class_tag=  "listing_even ";
				}
				else
				{
					if ($show_classifieds['bolding'])
						$css_class_tag=  "listing_odd_bold";
					else
						$css_class_tag=  "listing_odd ";
				}
				$this->body .="<tr class=".$css_class_tag." id=listing_row>\n\t\t";
				if ($this->browsing_configuration['display_business_type'])
				{
					$this->body .="<td id=listing_column>";
					if ($show_classifieds['business_type'] == 1)
						$this->body .= urldecode($this->messages[1263]);
					elseif ($show_classifieds['business_type'] == 2)
						$this->body .= urldecode($this->messages[1263]);
					else
						$this->body .= "&nbsp;";
					$this->body .= "</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_photo_icon'])
				{
					if ($this->configuration_data['photo_or_icon'] == 1)
					{
						if ($show_classifieds['image'] > 0)
						{
							if ($featured)
								$this->display_thumbnail($db,$show_classifieds['id'],$this->configuration_data['featured_thumbnail_max_width'],$this->configuration_data['featured_thumbnail_max_height']);
							else
								$this->display_thumbnail($db,$show_classifieds['id']);
						}
						elseif (($this->configuration_data['no_image_url']) && (!$show_classifieds['image']))
						{
							if (($this->configuration_data['popup_while_browsing'])
								&& ($this->configuration_data['popup_while_browsing_width'])
								&& ($this->configuration_data['popup_while_browsing_height']))
							{
								$this->body .= "<td align=center id=listing_column><a href=\"";
								$this->body .= $this->configuration_data['classifieds_file_name'];
								$this->body .= "?a=2&b=".$show_classifieds['id']."\" ";
								$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
							else
							{
								$this->body .="<td align=center id=listing_column><a href=".$this->configuration_data['classifieds_file_name'];
								$this->body .="?a=2&b=".$show_classifieds['id'].">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
						}
						else
							$this->body .="<td id=listing_column>&nbsp;</td>\n\t";
					}
					else
					{
						if ($show_classifieds['image'] > 0)
						{
							if (($this->configuration_data['popup_while_browsing'])
								&& ($this->configuration_data['popup_while_browsing_width'])
								&& ($this->configuration_data['popup_while_browsing_height']))
							{
								$this->body .= "<td id=listing_column><a href=\"";
								$this->body .= $this->configuration_data['classifieds_file_name'];
								$this->body .= "?a=2&b=".$show_classifieds['id']."\" ";
								$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
								$this->body .="<img src=".$this->configuration_data['photo_icon_url']." border=0></td>";
							}
							else
							{
								$this->body .="<td id=listing_column><a href=".$this->configuration_data['classifieds_file_name'];
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
								$this->body .= "<td align=center id=listing_column><a href=\"";
								$this->body .= $this->configuration_data['classifieds_file_name'];
								$this->body .= "?a=2&b=".$show_classifieds['id']."\" ";
								$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
							else
							{
								$this->body .="<td align=center id=listing_column><a href=".$this->configuration_data['classifieds_file_name'];
								$this->body .="?a=2&b=".$show_classifieds['id'].">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
						}
						else
							$this->body .="<td  id=listing_column>&nbsp;</td>\n\t";
					}
				}
				if ($this->browsing_configuration['display_ad_title'])
				{
					$this->body .="<td id=listing_column>\n\t\t";
					if ($show_classifieds['sold_displayed'])
						$this->body .= "<img src=".$this->configuration_data['sold_image']." border=0> ";
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
					$this->body .= stripslashes(urldecode($show_classifieds['title']))."</a>\n\t\t";
					if($show_classifieds['item_type'] == 2)
					{
						if ((strlen(trim($this->configuration_data['buy_now_image'])) >0))
						{
							$current_bid = $show_classifieds['current_bid'];
							$number_of_bids = $this->get_number_of_bids($db,$show_classifieds['id']);
							if (($show_classifieds['buy_now']!= 0) && (($show_classifieds['current_bid'] == 0) || ($this->configuration_data['buy_now_reserve'] && $show_classifieds['current_bid'] < $show_classifieds['reserve_price'])))
							{
								$this->body .= "<img src=".stripslashes($this->configuration_data['buy_now_image'])." border=0 hspace=2>";
							}
						}								
						if (strlen(trim($this->configuration_data['reserve_met_image'])) >0)
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
						if (strlen(trim($this->configuration_data['no_reserve_image'])) >0)
						{
							if ($show_classifieds['reserve_price'] == 0.00)
							{
								$this->body .= "<img src=".stripslashes($this->configuration_data['no_reserve_image'])." border=0 hspace=2>";
							}
						}
					}													
					if ($show_classifieds['attention_getter'])
					{
						$this->body .= "<img src=\"".$show_classifieds['attention_getter_url']."\" border=0 hspace=2>";
					}
					if (($this->browsing_configuration['display_ad_description'])&& ($this->browsing_configuration['display_ad_description_where']))
					{
						$this->body .="<br>";
						if (!$this->browsing_configuration['display_all_of_description'] ||!$this->browsing_configuration['auctions_display_all_of_description'])
						{
							if (strlen(urldecode($show_classifieds['description'])) > $this->configuration_data['length_of_description'])
							{
								$small_string = substr(trim(stripslashes(urldecode($show_classifieds['description']))),0,$this->configuration_data['length_of_description']);
								$position = strrpos($small_string," ");
								$smaller_string = substr($small_string,0,$position);
								$this->body .= $smaller_string."...";
							}
							else
								$this->body .=	stripslashes(urldecode($show_classifieds['description']));
						}
						else
							$this->body .=	stripslashes(urldecode($show_classifieds['description']));
					}
					$this->body .="</td>\n\t\t";
				}
	
				if (($this->browsing_configuration['display_ad_description'])&& (!$this->browsing_configuration['display_ad_description_where']))
				{
					$this->body .="<td id=listing_column>";
					if (!$this->browsing_configuration['display_all_of_description'])
					{
						if (strlen(urldecode($show_classifieds['description'])) > $this->configuration_data['length_of_description'])
						{
							$small_string = substr(trim(stripslashes(urldecode($show_classifieds['description']))),0,$this->configuration_data['length_of_description']);
							$position = strrpos($small_string," ");
							$smaller_string = substr($small_string,0,$position);
							$this->body .=$smaller_string."...";
						}
						else
							$this->body .=	stripslashes(urldecode($show_classifieds['description']));
					}
					else
						$this->body .=	stripslashes(urldecode($show_classifieds['description']));
					$this->body .="&nbsp;</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_1'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_1']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_1']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_2'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_2']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_2']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_3'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_3']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_3']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_4'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_4']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_4']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_5'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_5']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_5']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_6'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_6']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_6']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_7'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_7']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_7']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_8'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_8']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_8']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_9'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_9']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_9']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_10'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_10']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_10']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_11'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_11']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_11']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_12'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_12']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_12']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_13'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_13']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_13']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_14'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_14']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_14']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_15'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_15']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_15']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_16'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_16']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_16']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_17'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_17']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_17']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_18'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_18']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_18']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_19'])
				{
					$this->body .="<td id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_19']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_19']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_optional_field_20'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_20']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_20']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_browsing_city_field'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['location_city']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['location_city']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_browsing_state_field'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['location_state']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['location_state']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_browsing_country_field'])
				{
					$this->body .="<td  id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['location_country']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['location_country']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if ($this->browsing_configuration['display_browsing_zip_field'])
				{
					$this->body .="<td id=listing_column>";
						if (strlen(trim(urldecode($show_classifieds['location_zip']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['location_zip']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}
	
				if($show_classifieds['item_type'] == 2)
				{
					if ($this->browsing_configuration['display_number_bids'])
					{
						$this->body .= "<td id=listing_column><div nowrap>".$this->get_number_of_bids($db,$show_classifieds['id'])." ".urldecode($this->messages[103042])."</div></td>";
					}
				}
	
				if (($show_classifieds['item_type'] == 1) && $this->browsing_configuration['display_price'])
				{
					$this->body .="<td id=listing_column>";
					if (((strlen(trim(urldecode($show_classifieds['price']))) > 0)
						|| (strlen(trim(urldecode($show_classifieds['precurrency']))) > 0)
						|| (strlen(trim(urldecode($show_classifieds['postcurrency']))) > 0)) && ($show_classifieds['price'] != 0))
					{
						if (floor($show_classifieds['price']) == $show_classifieds['price'])
						{
							$this->body .= stripslashes(urldecode($show_classifieds['precurrency'])). " ".
								number_format($show_classifieds['price'])." ".
								stripslashes(urldecode($show_classifieds['postcurrency']));
						}
						else
						{
							$this->body .= stripslashes(urldecode($show_classifieds['precurrency'])). " ".
								number_format($show_classifieds['price'],2,".",",")." ".
								stripslashes(urldecode($show_classifieds['postcurrency']));
						}
					}
					else
						$this->body .=	stripslashes(urldecode($show_classifieds['precurrency']))." - ".stripslashes(urldecode($show_classifieds['postcurrency']));
					$this->body .="</td>\n\t";
				}
				elseif (($show_classifieds['item_type'] == 2) && $this->browsing_configuration['display_price'])
				{
					$this->body .="<td id=listing_column>";
					if (((strlen(trim(urldecode($show_classifieds['minimum_bid']))) > 0)
						|| (strlen(trim(urldecode($show_classifieds['precurrency']))) > 0)
						|| (strlen(trim(urldecode($show_classifieds['postcurrency']))) > 0)) && ($show_classifieds['minimum_bid'] != 0))
					{
						if (floor($show_classifieds['minimum_bid']) == $show_classifieds['minimum_bid'])
						{
							$this->body .= stripslashes(urldecode($show_classifieds['precurrency'])). " ".
								number_format($show_classifieds['minimum_bid'])." ".
								stripslashes(urldecode($show_classifieds['postcurrency']));
						}
						else
						{
							$this->body .= stripslashes(urldecode($show_classifieds['precurrency'])). " ".
								number_format($show_classifieds['minimum_bid'],2,".",",")." ".
								stripslashes(urldecode($show_classifieds['postcurrency']));
						}
					}
					else
						$this->body .=	stripslashes(urldecode($show_classifieds['precurrency']))." - ".stripslashes(urldecode($show_classifieds['postcurrency']));
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_entry_date']
					&& ($show_classifieds['item_type'] == 1
						|| ($this->browsing_configuration['auction_entry_date'] 
							&& $show_classifieds['item_type'] == 2)))
					$this->body .="<td id=listing_column>".date(trim($this->configuration_data['entry_date_configuration']),$show_classifieds['date'])."</td>\n\t";
	
				if($this->browsing_configuration['display_time_left']
					&& (($show_classifieds['item_type'] == 2) 
						|| ($this->browsing_configuration['classified_time_left'] && ($show_classifieds['item_type'] == 1))))
				{
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
						$this->body .=  "<td id=listing_column><div nowrap class=auction_closed>";
						$this->body .= urldecode($this->messages[100051])."</div>";
					}
					else
					{
						$this->body .=  "<td id=listing_column><div nowrap>";
					}
					if ($weeks > 0)
					{
						$this->body .= $weeks." ".stripslashes(urldecode($this->messages[103003])).", ".$days." ".stripslashes(urldecode($this->messages[103004]));
					}
					elseif ($days > 0)
					{
						$this->body .= $days." ".stripslashes(urldecode($this->messages[103004])).", ".$hours." ".stripslashes(urldecode($this->messages[103005]));
					}
					elseif ($hours > 0)
					{
						$this->body .= $hours." ".stripslashes(urldecode($this->messages[103005])).", ".$minutes." ".stripslashes(urldecode($this->messages[103006]));
					}
					elseif ($minutes > 0)
					{
						$this->body .= $minutes." ".stripslashes(urldecode($this->messages[103006])).", ".$seconds." ".stripslashes(urldecode($this->messages[103007]));
					}
					elseif ($seconds > 0)
					{
						$this->body .= $seconds." ".stripslashes(urldecode($this->messages[103007]));
					}
					$this->body .= "</td>\n\t";
				}

				if ($this->classified_user_id == 1)
				{
					//this is the admin
					$this->body .="<td >\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&e=".$show_classifieds['id']."&b=5><img src=images/btn_user_edit.gif border=0></a>\n\t\t</td>\n\t";
					$this->body .="<td >\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=99&b=".$show_classifieds['id']."&c=".$this->site_category."><img src=images/btn_user_remove.gif border=0></a>\n\t\t</td>\n\t";
	
				}
				$this->body .="</tr>\n\t";
				$this->row_count++;
			} //end of while
			$this->body .="</table>\n\t</td>\n</tr>\n";
		}
		else
		{
			//no classifieds in this category
			if(!$auction)
				$this->body .="<tr class=no_ads_in_category>\n\t<td >\n\t".urldecode($this->messages[17])."\n\t</td>\n</tr>\n";
			else
				$this->body .="<tr class=no_ads_in_category>\n\t<td >\n\t".urldecode($this->messages[100017])."\n\t</td>\n</tr>\n";
	
		}
	} //end of function display_browse_result

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayCategories($db, $menuTitle)
	{
		$this->getStorefrontCategories($db);
		$this->category0ut = "\n<ul id=categories>";
		$this->category0ut .= "\n<li id=menu_title class='category_menu_title menu_item'>".$menuTitle."</li>";
		$homeLink = $this->storefrontUserData["storefront_home_link"] ? stripslashes($this->storefrontUserData["storefront_home_link"]) : $this->storefrontUserData["username"];
		$this->category0ut .= "\n<li id=home class=menu_item><a href='".$this->configuration_data['storefront_url']."?store=".$this->storefront_id."'>".$homeLink."</a></li>";
		foreach($this->storefrontCategories as $storefrontCategoryIndex => $storefrontCategoryDetails)
		{
			$this->category0ut .= "\n<li id=category class=menu_item>&nbsp;&nbsp;<a href='".$this->configuration_data['storefront_url']."?store=".$this->storefront_id."&b=".$storefrontCategoryDetails["category_id"]."'>".stripslashes($storefrontCategoryDetails["category_name"])."</a></li>";
		}
		$this->category0ut .= "\n</ul>";
		if($this->storefrontIsEditable)
		{			
		}
		return $this->category0ut;
		// onClick='javascript: sendUserData(\"addCategory\",\"categoryAdd\",\"adding\",\"list-style:none outside;margin-left:-30px;\");'
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayLogo($db)
	{
		if($this->storefrontUserData["storefront_header"])
		{
			$this->sql_query = "select url_image_directory from ".$this->ad_configuration_table;
			$configurationResults = $db->Execute($this->sql_query);
			$configurationInfo = $configurationResults->FetchRow();
			$this->logoOut = "\n<img ";
			$this->logoOut .= "src='".$configurationInfo["url_image_directory"]."storefrontImages/".$this->storefrontUserData["storefront_header"]."'";
			$this->logoOut .= " id=logo>";
		}else{$this->logoOut = '';}
		return $this->logoOut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayWelcomeMessage($db)
	{
		if($this->storefrontUserData["storefront_welcome_message"])
		{
			$this->welcomeMessageOut .= $this->storefrontUserData["storefront_welcome_message"];
		}else{$this->welcomeMessageOut .= '';}
		return $this->welcomeMessageOut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayPages($db, $menuTitle)
	{
		$this->getStorefrontPages($db);
		$this->pages0ut = "\n<ul id=pages>";
		$this->pages0ut .= "\n<li id=menu_title class='page_menu_title menu_item'>".$menuTitle."</li>";
		foreach($this->storefrontPage as $storefrontPageIndex => $storefrontPageDetails)
		{
			$this->pages0ut .= "\n<li id=page class=menu_item><a href='".$this->configuration_data['storefront_url']."?store=".$this->storefront_id."&p=".$storefrontPageDetails["page_id"]."'>".stripslashes(urldecode($storefrontPageDetails["page_link_text"]))."</a></li>";
		}
		$this->pages0ut .= "\n</ul>";
		return $this->pages0ut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayManagerLogo($db,$buttonText)
	{
		$this->logoOut ='';
		if($this->storefrontIsEditable)
		{						
			//storefront logo
			$this->logoOut .= "\n<form action='".$this->configuration_data['storefront_url']."?update&store=".$this->storefront_id."' name=updateLogo id=updateLogo enctype='multipart/form-data' method='post'>";
			$this->logoOut .= "\n<input class=file type=file name=storefrontLogo id=storefrontLogo size=20>";
			$this->logoOut .= "\n<input class=button type=submit name=storefrontUpdateLogo id=storefrontUpdateLogo value='$buttonText'>";
			$this->logoOut .= "\n</ul>";
			$this->logoOut .= "\n</form>";
		}
		return $this->logoOut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayManagerWelcome($db,$buttonText)
	{
		$this->welcomeMessageOut ='';
		if($this->storefrontIsEditable)
		{			
			//storefront welcome note
			$this->welcomeMessageOut .= "\n<form action='".$this->configuration_data['storefront_url']."?update&store=".$this->storefront_id."' name=updateMessage id=updateMessage method='post'>";
			$this->welcomeMessageOut .= "\n<input type=text name=storefrontNote id=storefrontNote value='".stripslashes($this->storefrontUserData["storefront_welcome_message"])."' size=20>";
			$this->welcomeMessageOut .= "\n<input class=button type=submit name=storefrontUpdateNote id=storefrontUpdateNote value='$buttonText'>";
			$this->welcomeMessageOut .= "\n</form>";
		}
		return $this->welcomeMessageOut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayManagerHomeLink($db,$buttonText)
	{
		$this->category0ut ='';
		if($this->storefrontIsEditable)
		{			
			//storefront category
			$homeLink = $this->storefrontUserData["storefront_home_link"] ? stripslashes($this->storefrontUserData["storefront_home_link"]) : $this->storefrontUserData["username"];
			$this->category0ut .= "\n<form action='".$this->configuration_data['storefront_url']."?update&store=".$this->storefront_id."' name=addHomeLink id=addHomeLink method=post>";
			$this->category0ut .= "\n<input type=text name=homeLink id=homeLink size=20 value='".$homeLink."'>";
			$this->category0ut .= "\n<input class=button type=submit name=homeLinkAdd id=homeLinkAdd value='$buttonText'>";
			$this->category0ut .= "\n</form>";
		}
		return $this->category0ut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayManagerCategoryAdd($db,$buttonText)
	{
		$this->category0ut ='';
		if($this->storefrontIsEditable)
		{			
			//storefront category
			$this->category0ut .= "\n<form action='".$this->configuration_data['storefront_url']."?update&store=".$this->storefront_id."' name=addCategory id=addCategory method=post>";
			$this->category0ut .= "\n<input type=text name=categoryName id=categoryName size=20>";
			$this->category0ut .= "\n<input class=button type=submit name=categoryAdd id=categoryAdd value='$buttonText'>";
			$this->category0ut .= "\n</form>";
		}
		return $this->category0ut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayManagerCategoryEdit($db,$buttonText)
	{
		$this->category0ut ='';
		if($this->storefrontIsEditable)
		{
			$this->getStorefrontCategories($db);
			//storefront category
			$this->category0ut .= "\n<form action='".$this->configuration_data['storefront_url']."?update&store=".$this->storefront_id."' name=editCategory id=editCategory method=post>";
			$this->category0ut .= "\n<select class=select name=categoryId id=categoryId size=1>";
			foreach($this->storefrontCategories as $storefrontCategoryIndex => $storefrontCategoryDetails)
				$this->category0ut .= "\n<option value='".$storefrontCategoryDetails["category_id"]."'>".stripslashes($storefrontCategoryDetails["category_name"])."</option>";
			$this->category0ut .= "\n</select>";
			$this->category0ut .= "\n<input type=text name=categoryName id=categoryName size=20>";
			$this->category0ut .= "\n<input class=button type=submit name=categoryEdit id=categoryEdit value='$buttonText'>";
			$this->category0ut .= "\n</form>";
		}
		return $this->category0ut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayManagerCategoryDelete($db,$buttonText)
	{
		$this->category0ut ='';
		if($this->storefrontIsEditable)
		{
			$this->getStorefrontCategories($db);
			//storefront category
			$this->category0ut .= "\n<form action='".$this->configuration_data['storefront_url']."?update&store=".$this->storefront_id."' name=editCategory id=editCategory method=post>";
			$this->category0ut .= "\n<select class=select name=categoryId1 id=categoryId1 size=1>";
			foreach($this->storefrontCategories as $storefrontCategoryIndex => $storefrontCategoryDetails)
				$this->category0ut .= "\n<option value='".$storefrontCategoryDetails["category_id"]."'>".stripslashes($storefrontCategoryDetails["category_name"])."</option>";
			$this->category0ut .= "\n</select>&nbsp;";
			$this->category0ut .= "\n<select class=select name=categoryId2 id=categoryId2 size=1>";
			foreach($this->storefrontCategories as $storefrontCategoryIndex => $storefrontCategoryDetails)
				$this->category0ut .= "\n<option value='".$storefrontCategoryDetails["category_id"]."'>".stripslashes($storefrontCategoryDetails["category_name"])."</option>";
			$this->category0ut .= "\n</select>";
			$this->category0ut .= "\n<input class=button type=submit name=categoryDelete id=categoryDelete value='$buttonText'>";
			$this->category0ut .= "\n</form>";
		}
		return $this->category0ut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayManagerCategorySort($db,$buttonText)
	{
		$this->category0ut ='';
		if($this->storefrontIsEditable)
		{
			$this->getStorefrontCategories($db);
			$storefrontCategoryCount = count($this->storefrontCategories);
			
			//storefront category
			$this->category0ut .= "\n<form action='".$this->configuration_data['storefront_url']."?update&store=".$this->storefront_id."' name=sortCategory id=sortCategory method=post>";
			$lcv=1;
			foreach($this->storefrontCategories as $storefrontCategoryIndex => $storefrontCategoryDetails)
			{
				$this->category0ut .= stripslashes($storefrontCategoryDetails["category_name"]);
				$this->category0ut .= "\n&nbsp;<select class=select name='categoryOrder[".$storefrontCategoryDetails["category_id"]."]' id=order_$lcv size=1 onFocus=\"javascript: prev_order=document.getElementById('order_$lcv').value;\" onChange=\"javascript: switch_order(prev_order, document.getElementById('order_$lcv').value, $storefrontCategoryCount, 'order_$lcv');\">";
				for($lcv2=1;$lcv2<=$storefrontCategoryCount;$lcv2++)
				{
					$this->category0ut .= "\n<option value='".$lcv2."' ";
					if($lcv==$lcv2)
						$this->category0ut .= "selected";
					$this->category0ut .= ">".$lcv2."</option>";
				}
				$this->category0ut .= "\n</select><br>";
				$lcv++;
			}
			$this->category0ut .= "\n<input class=button type=submit name=categorySort id=categorySort value='$buttonText'>";
			$this->category0ut .= "\n</form>";
		}
		return $this->category0ut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayManagerSwitchTemplate($db,$buttonText)
	{
		$this->templateSwitchOut ='';
		if($this->storefrontIsEditable)
		{
			$this->sql_query = "select * from ".$this->templates_table." where storefront_template = 1 order by storefront_template_default desc";
			$storefrontTemplates = $db->Execute($this->sql_query);
			
			$this->templateSwitchOut .= "\n<form action='".$this->configuration_data['storefront_url']."?update&store=".$this->storefront_id."' name=editCategory id=editCategory method=post>";
			$this->templateSwitchOut .= "\n<select name=storefrontTemplate id=storefrontTemplate size=1>";
			while($storefrontTemplate = $storefrontTemplates->FetchRow())
			{
				$selected = ($storefrontTemplate["template_id"]==$this->storefrontUserData["storefront_template_id"]) ? "selected" : "";
				$this->templateSwitchOut .= "\n<option value='".$storefrontTemplate["template_id"]."' $selected>".stripslashes($storefrontTemplate["name"])."</option>";
			}
			$this->templateSwitchOut .= "\n</select>&nbsp;";
			$this->templateSwitchOut .= "\n<input class=button type=submit name=templateSwitch id=templateSwitch value='$buttonText'>";
			$this->templateSwitchOut .= "\n</form>";
		}
		return $this->templateSwitchOut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayManagerToggleActivity($db,$buttonText1,$buttonText2)
	{
		$this->toggleOut ='';
		if($this->storefrontIsEditable)
		{
			$buttonText = ($this->storefrontUserData["storefront_on_hold"]==0) ? $buttonText1 : $buttonText2;
			$this->toggleOut .= "\n<form action='".$this->configuration_data['storefront_url']."?update&store=".$this->storefront_id."' name=toggleStorefront id=toggleStorefront method=post>";
			$this->toggleOut .= "\n<input class=button type=submit name=storefrontToggle id=storefrontToggle value='$buttonText'>";
			$this->toggleOut .= "\n</form>";
		}
		return $this->toggleOut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayManagerPageAdd($db,$buttonText)
	{
		$this->page0ut ='';
		if($this->storefrontIsEditable)
		{			
			//storefront extra page
			$this->page0ut .= "\n<form action='".$this->configuration_data['storefront_url']."?update&store=".$this->storefront_id."' name=addPage id=addPage method=post>";
			$this->page0ut .= "\n<input type=text name=pageName id=pageName size=20>";
			$this->page0ut .= "\n<input class=button type=submit name=pageAdd id=pageAdd value='$buttonText'>";
			$this->page0ut .= "\n</form>";
		}
		return $this->page0ut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayManagerPageBodyEdit($db,$buttonText,$cols=40,$rows=20,$use_rte=false)
	{
		$this->page0ut = '';
		if($this->storefrontIsEditable)
		{			
			//storefront extra page
			$this->page0ut .= "\n<form action='".$this->configuration_data['storefront_url']."?update&store=".$this->storefront_id."&p=".$this->pageId."' name=editPageBody id=editPageBody method=post";
			if ($use_rte===true||
				strtolower($use_rte)==="true"||
				$use_rte==="1")
			{
				$this->page0ut .= " onsubmit=\"return submitForm();\"";
				$this->page0ut .= ">\n";
				$this->LoadRTE("pageBody", urldecode($this->pageInfo["page_body"]), ($cols*6), ($rows*10), 1, 1);
				$this->page0ut .= $this->body;

			}
			else
			{
				$this->page0ut .=">\n";
				$this->page0ut .= "\n<textarea name=pageBody id=pageBody cols=$cols rows=$rows>".urldecode($this->pageInfo["page_body"])."</textarea>";
			}
			$this->page0ut .= "\n<input class=button type=submit name=pageBodyEdit id=pageBodyEdit value='$buttonText'>";
			$this->page0ut .= "\n</form>";
		}
		return $this->page0ut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayManagerPageNameEdit($db,$buttonText)
	{
		$this->page0ut ='';
		if($this->storefrontIsEditable)
		{			
			//storefront extra page
			$this->page0ut .= "\n<form action='".$this->configuration_data['storefront_url']."?update&store=".$this->storefront_id."&p=".$this->pageId."' name=editPageName id=editPageName method=post>";
			$this->page0ut .= "\n<input type=text name=pageName id=pageName size=20 value=\"".urldecode($this->pageInfo["page_name"])."\">";
			$this->page0ut .= "\n<input class=button type=submit name=pageNameEdit id=pageNameEdit value='$buttonText'>";
			$this->page0ut .= "\n</form>";
		}
		return $this->page0ut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayManagerPageLinkTextEdit($db,$buttonText)
	{
		$this->page0ut ='';
		if($this->storefrontIsEditable)
		{			
			//storefront extra page
			$this->page0ut .= "\n<form action='".$this->configuration_data['storefront_url']."?update&store=".$this->storefront_id."&p=".$this->pageId."' name=editPageLinkText id=editPageLinkText method=post>";
			$this->page0ut .= "\n<input type=text name=pageLinkText id=pageLinkText size=20 value=\"".urldecode($this->pageInfo["page_link_text"])."\">";
			$this->page0ut .= "\n<input class=button type=submit name=pageLinkTextEdit id=pageLinkTextEdit value='$buttonText'>";
			$this->page0ut .= "\n</form>";
		}
		return $this->page0ut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayManagerPageDelete($db,$buttonText)
	{
		$this->page0ut ='';
		if($this->storefrontIsEditable)
		{			
			//storefront extra page
			$this->page0ut .= "\n<form action='".$this->configuration_data['storefront_url']."?update&store=".$this->storefront_id."&p=".$this->pageId."' name=deletePage id=deletePage method=post>";
			$this->page0ut .= "\n<input class=button type=submit name=pageDelete id=pageDelete value='$buttonText'>";
			$this->page0ut .= "\n</form>";
		}
		return $this->page0ut;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function generatePage($db,$pageId)
	{
		$this->pageId = $pageId;
		$this->sql_query = "select * from $this->storefront_pages_table where user_id = '$this->storefront_id' and page_id = '$pageId'";
		$pageResults = $db->Execute($this->sql_query);
		if($pageResults->RecordCount()!=1)
			return false;
		$this->pageInfo = $pageResults->FetchRow();
		$this->storefrontTemplate = str_replace('(!STOREFRONT_LISTINGS!)',urldecode($this->pageInfo["page_body"]), $this->storefrontTemplate);
		return true;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function displayScripts()
	{
		if($this->storefrontIsEditable)
		{
			$this->headScriptOut .= "
	        var prev_order = null;
	        
	        function switch_order(original_order, new_order, element_length, subject_name)
	        {
	            for(x = 0; x < element_length; x++)
	            {
	            	var element_id = 'order_'+(x+1);
	                if(document.getElementById(element_id).value == new_order)
	                {
		                document.getElementById(element_id).value = original_order;
	                }
	            }
		        document.getElementById(subject_name).value = new_order;
		        prev_order = new_order;
	        }
			";
		}
		return "<script language='Javascript'>".$this->headScriptOut."</script>";
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function renderTemplateModules($db)
	{
		$this->sql_query = "select * from ".$this->storefront_template_modules_table." where template_id = '".$this->storefrontTemplateId."'";
		
		$result = $db->Execute($this->sql_query);
		if (!$result)
			return false;
		elseif ($result->RecordCount() > 0)
		{
			while ($show = $result->FetchRow())
			{
				$this->sql_query = "select * from ".$this->pages_table." where page_id = ".$show['module_id'];
				$module_result = $db->Execute($this->sql_query);
				if ($module_result->RecordCount() == 1)
				{
					$show_module = $module_result->FetchRow();
					if (strlen($show_module['module_file_name']) > 0)
					{
						include("classes/".$show_module['module_file_name']);
						$this->storefrontTemplate = str_replace($show_module['module_replace_tag'],$this->body,$this->storefrontTemplate);
					}

				}
			}
		}

		return true;
	} // end of function get_page_modules

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function getTemplate($db)
	{
		$templateSelectCondition = (!$this->storefrontUserData["storefront_template_id"]) ? "storefront_template_default = 1" : "template_id = {$this->storefrontUserData["storefront_template_id"]}";
		$this->sql_query = "select * from ".$this->templates_table." where ".$templateSelectCondition." and language_id = '".$this->language_id."' limit 1";
		if($templateResults = $db->Execute($this->sql_query))
		{
			$templateInfo = $templateResults->FetchRow();
			$this->storefrontTemplateId = $templateInfo["template_id"]; 
			$this->storefrontTemplate = stripslashes($templateInfo["template_code"]); 
		}else{
			return false;
		}
		return true;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function renderTemplate($db)
	{
		if($this->storefrontIsEditable)
		{
			if(!$this->displayPage)
			{
				$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER!\)|\(!END_STOREFRONT_MANAGER!\)/','', $this->storefrontTemplate);
				$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_EXTRA_PAGE!\)([a-zA-Z0-9\w\W\d\D]*)\(!END_STOREFRONT_MANAGER_EXTRA_PAGE!\)/','', $this->storefrontTemplate);
			}else{
				$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_EXTRA_PAGE!\)|\(!END_STOREFRONT_MANAGER_EXTRA_PAGE!\)/','', $this->storefrontTemplate);
				$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER!\)([a-zA-Z0-9\w\W\d\D]*)\(!END_STOREFRONT_MANAGER!\)/','', $this->storefrontTemplate);
			}
		}else{
			$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER!\)([a-zA-Z0-9\w\W\d\D]*)\(!END_STOREFRONT_MANAGER!\)/','', $this->storefrontTemplate);
			$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_EXTRA_PAGE!\)([a-zA-Z0-9\w\W\d\D]*)\(!END_STOREFRONT_MANAGER_EXTRA_PAGE!\)/','', $this->storefrontTemplate);
		}
		$this->storefrontTemplate = str_replace('(!STOREFRONT_HEAD!)',$this->displayScripts(), $this->storefrontTemplate);
		$this->storefrontTemplate = str_replace('(!STOREFRONT_LOGO!)',$this->displayLogo($db), $this->storefrontTemplate);
		$this->storefrontTemplate = str_replace('(!STOREFRONT_WELCOME_NOTE!)',$this->displayWelcomeMessage($db), $this->storefrontTemplate);
		$this->storefrontTemplate = preg_replace('@\(!STOREFRONT_CATEGORIES[\s]*?(MENU_TITLE="([^"]*)")?[\s]*?!\)@',$this->displayCategories($db,'\\2'), $this->storefrontTemplate);
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_PAGES[\s]*?(MENU_TITLE="([^"]*)")?[\s]*?!\)/',$this->displayPages($db,'\\2'), $this->storefrontTemplate);
		
		//standard controls
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_LOGO[\s]?(BUTTON_TEXT="([\w\s]*)")?[\s]*?!\)/',$this->displayManagerLogo($db, '\\2'), $this->storefrontTemplate);
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_WELCOME_NOTE[\s]*?(BUTTON_TEXT="([\w\s]*)")?[\s]*?!\)/',$this->displayManagerWelcome($db, '\\2'), $this->storefrontTemplate);
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_CATEGORIES_ADD[\s]*?(BUTTON_TEXT="([\w\s]*)")?[\s]*?!\)/',$this->displayManagerCategoryAdd($db, '\\2'), $this->storefrontTemplate);
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_HOME_LINK[\s]*?(BUTTON_TEXT="([\w\s]*)")?[\s]*?!\)/',$this->displayManagerHomeLink($db, '\\2'), $this->storefrontTemplate);
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_CATEGORIES_EDIT[\s]*?(BUTTON_TEXT="([\w\s]*)")?[\s]*?!\)/',$this->displayManagerCategoryEdit($db, '\\2'), $this->storefrontTemplate);
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_CATEGORIES_DELETE[\s]*?(BUTTON_TEXT="([\w\s]*)")?[\s]*?!\)/',$this->displayManagerCategoryDelete($db, '\\2'), $this->storefrontTemplate);
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_CATEGORIES_SORT[\s]*?(BUTTON_TEXT="([\w\s]*)")?[\s]*?!\)/',$this->displayManagerCategorySort($db, '\\2'), $this->storefrontTemplate);
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_TEMPLATE[\s]*?(BUTTON_TEXT="([\w\s]*)")?[\s]*?!\)/',$this->displayManagerSwitchTemplate($db, '\\2'), $this->storefrontTemplate);
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_HOLD[\s]*?(ON_BUTTON_TEXT="([\w\s]*)")?[\s]*?(OFF_BUTTON_TEXT="([\w\s]*)")?[\s]*?!\)/',$this->displayManagerToggleActivity($db, '\\2', '\\4'), $this->storefrontTemplate);
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_PAGE_ADD[\s]*?(BUTTON_TEXT="([\w\s]*)")?[\s]*?!\)/',$this->displayManagerPageAdd($db, '\\2'), $this->storefrontTemplate);
		
		//extra page controls
		preg_match('/\(!STOREFRONT_MANAGER_PAGE_BODY[\s]?(BUTTON_TEXT="([\w\s]*)")?[\s]?(COLUMNS="([0-9]*)")?[\s]?(ROWS="([0-9]*)")?[\s]?(USE_RTE="([\w\s]*)")?[\s]?!\)/',$this->storefrontTemplate,$matches);
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_PAGE_BODY[\s]?(BUTTON_TEXT="([\w\s]*)")?[\s]*?(COLUMNS="([0-9]*)")?[\s]*?(ROWS="([0-9]*)")?[\s]*?(USE_RTE="([\w\s]*)")?[\s]?!\)/',$this->displayManagerPageBodyEdit($db, '\\2', $matches[4], $matches[6], $matches[8]), $this->storefrontTemplate);
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_PAGE_NAME[\s]?(BUTTON_TEXT="([\w\s]*)")?[\s]*?!\)/',$this->displayManagerPageNameEdit($db, '\\2'), $this->storefrontTemplate);
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_PAGE_LINK_TEXT[\s]?(BUTTON_TEXT="([\w\s]*)")?[\s]*?!\)/',$this->displayManagerPageLinkTextEdit($db, '\\2'), $this->storefrontTemplate);
		$this->storefrontTemplate = preg_replace('/\(!STOREFRONT_MANAGER_PAGE_DELETE[\s]?(BUTTON_TEXT="([\w\s]*)")?[\s]*?!\)/',$this->displayManagerPageDelete($db, '\\2'), $this->storefrontTemplate);
		
		$this->storefrontTemplate = str_replace('(!STOREFRONT_MANAGER_ERROR!)',$this->storefrontManagementError, $this->storefrontTemplate);
		
		return true;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
}
	
?>