<? //browse_ads.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Browse_ads extends Site {
	var $subcategory_array = array();
	var $notify_data = array();
	var $debug_affiliate_browse = 0;

//########################################################################

	function Browse_ads($db,$affiliate_id,$language_id,$category_id=0,$page=0,$classified_id=0,$affiliate_group_id=0, $product_configuration=0)
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
		$this->Site($db,1,$language_id,0, $product_configuration);
		$this->get_ad_configuration($db);
		if ($page)
		{
			$this->page_result = $page;
			$this->affiliate_page_type = $page;
		}
		else
		{
			$this->affiliate_page_type = 1;
			$this->page_result = 1;
		}
		$this->affiliate_group_id = $affiliate_group_id;
		$this->affiliate_id = $affiliate_id;
	} //end of function Browse_ads

//###########################################################

	function browse($db,$category=0,$browse_type=0)
	{
		$this->page_id = 3;
		$this->get_text($db);
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		//browse the auctions in this category that are open
		//$this->body .=$this->configuration_data['display_sub_category_ads']." display sub cats<Br>\n";
		
		if (($browse_type == 0) && ($this->configuration_data['default_display_order_while_browsing']))
		{
			$browse_type = $this->configuration_data['default_display_order_while_browsing'];
		}
		
		$this->browse_type = $browse_type;
		
		switch ($browse_type)
		{
			case 0: //nothing
 				$order_by = "order by better_placement desc,date desc ";
				break;
			case 1: //price desc
				$order_by = " order by price desc, current_bid desc, better_placement desc ";
				break;
			case 2: //price asc
				$order_by = " order by price asc, current_bid asc, better_placement desc ";
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

		$this->sql_query_count = "select count(id) as total from ".$this->classifieds_table." where seller = ".$this->affiliate_id." and live = 1";
		$this->sql_query = "select * from ".$this->classifieds_table." where
			live = 1 and seller = ".$this->affiliate_id." ".$order_by." limit ".(($this->page_result -1) * $this->configuration_data['number_of_ads_to_display']).",".$this->configuration_data['number_of_ads_to_display'];
		if ($this->debug_affiliate_browse) echo $this->sql_query."<br>\n";

		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			if ($this->debug_affiliate_browse) echo $this->sql_query."<br>\n";
			$this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
			return false;
		}
		else
		{
			$total_count_result = $db->Execute($this->sql_query_count);
			if ($this->debug_affiliate_browse) echo $this->sql_query_count."<br>\n";
			if ($total_count_result)
			{
				$show_total = $total_count_result->FetchNextObject();
				$total_returned = $show_total->TOTAL;
				//$this->body .=$total_returned." is the total returned<br>\n";
			}

			if ($this->configuration_data['number_of_ads_to_display'] < $total_returned)
			{
				//display the link to the next 10
				$number_of_page_results = ceil($total_returned / $this->configuration_data['number_of_ads_to_display']);
				$this->body .="<tr class=browsing_result_page_links>\n\t<td ><font class=more_results>".urldecode($this->messages[25])." ".$this->page_result."</font><font class=page_of>".urldecode($this->messages[26]).ceil($total_returned / $this->configuration_data['number_of_ads_to_display'])."</font></td>\n</tr>\n";
			}

			$result->Move(0);
			$this->display_browse_result($db,$result,"browsing_result_table_header");
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
							$this->body .="<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=5&b=".$category."&page=".$i;
							if ($browse_type) $this->body .= "&c=".$browse_type;
							$this->body .= " class=browsing_result_page_links>".$i."</a> ";
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
								{
									$this->body .="<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=5&b=".$category."&page=".$page;
									if ($browse_type) $this->body .= "&c=".$browse_type;
									$this->body .= " class=browsing_result_page_links>".$page."</a> ";
								}
							}

						}
						else
						{
							//display the link to the section
							$this->body .="<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=5&b=".$category."&page=".(($section*10)+1);
							if ($browse_type) $this->body .= "&c=".$browse_type;
							$this->body .= " class=browsing_result_page_links>".(($section*10)+1)."</a>";
						}
						if (($section+1) < $number_of_sections)
							$this->body .= "<font class=browsing_result_page_links>..</font>";
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
						$link_text = "<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=5&b=".$this->site_category."&page=".$this->page_result."&c=";						
						
						$this->body .="<tr>\n\t<td  height=20>\n\t";
						$this->body .="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n\t";
						$this->body .="<tr class=".$header_css.">\n\t\t";
						if ($this->configuration_data['display_photo_icon'])
							$this->body .="<td  class=title_column_header>".urldecode($this->messages[23])."</td>\n\t";
						if ($this->configuration_data['display_ad_title'])
						{
							$this->body .="<td  class=title_column_header>".$link_text;
							if ($this->browse_type == 5) $this->body .= "6";
							elseif ($this->browse_type == 6) $this->body .= "0";
							else $this->body .= "5";
							$this->body .= " class=title_column_header >".urldecode($this->messages[19])."</a>";
							if (($this->configuration_data['display_ad_description']) && ($this->configuration_data['display_ad_description_where']))
								$this->body .="<br>".urldecode($this->messages[21]);
	
							$this->body .="</td>\n\t\t";
						}

						if (($this->configuration_data['display_ad_description']) && (!$this->configuration_data['display_ad_description_where']))
							$this->body .="<td   class=description_column_header>".urldecode($this->messages[21])."</td>\n\t";
						if ($this->configuration_data['display_optional_field_1'])
						{
							$this->body .="<td class=optional_field_header_1>".$link_text;
							if ($this->browse_type == 15) $this->body .= "16";
							elseif ($this->browse_type == 16) $this->body .= "0";
							else $this->body .= "15";
							$this->body .= " class=optional_field_header_1>".urldecode($this->messages[922])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_2'])
						{
							$this->body .="<td class=optional_field_header_2>".$link_text;
							if ($this->browse_type == 17) $this->body .= "18";
							elseif ($this->browse_type == 18) $this->body .= "0";
							else $this->body .= "17";
							$this->body .= " class=optional_field_header_2>".urldecode($this->messages[923])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_3'])
						{
							$this->body .="<td class=optional_field_header_3>".$link_text;
							if ($this->browse_type == 19) $this->body .= "20";
							elseif ($this->browse_type == 20) $this->body .= "0";
							else $this->body .= "19";
							$this->body .= " class=optional_field_header_3>".urldecode($this->messages[924])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_4'])
						{
							$this->body .="<td class=optional_field_header_4>".$link_text;
							if ($this->browse_type == 21) $this->body .= "22";
							elseif ($this->browse_type == 22) $this->body .= "0";
							else $this->body .= "21";
							$this->body .= " class=optional_field_header_4>".urldecode($this->messages[925])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_5'])
						{
							$this->body .="<td class=optional_field_header_5>".$link_text;
							if ($this->browse_type == 23) $this->body .= "24";
							elseif ($this->browse_type == 24) $this->body .= "0";
							else $this->body .= "23";
							$this->body .= " class=optional_field_header_5>".urldecode($this->messages[926])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_6'])
						{
							$this->body .="<td class=optional_field_header_6>".$link_text;
							if ($this->browse_type == 25) $this->body .= "26";
							elseif ($this->browse_type == 26) $this->body .= "0";
							else $this->body .= "25";
							$this->body .= " class=optional_field_header_6>".urldecode($this->messages[927])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_7'])
						{
							$this->body .="<td class=optional_field_header_7>".$link_text;
							if ($this->browse_type == 27) $this->body .= "28";
							elseif ($this->browse_type == 28) $this->body .= "0";
							else $this->body .= "27";
							$this->body .= " class=optional_field_header_7>".urldecode($this->messages[928])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_8'])
						{
							$this->body .="<td class=optional_field_header_8>".$link_text;
							if ($this->browse_type == 29) $this->body .= "30";
							elseif ($this->browse_type == 30) $this->body .= "0";
							else $this->body .= "29";
							$this->body .= " class=optional_field_header_8>".urldecode($this->messages[929])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_9'])
						{
							$this->body .="<td class=optional_field_header_9>".$link_text;
							if ($this->browse_type == 31) $this->body .= "32";
							elseif ($this->browse_type == 32) $this->body .= "0";
							else $this->body .= "31";
							$this->body .= " class=optional_field_header_9>".urldecode($this->messages[930])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_10'])
						{
							$this->body .="<td class=optional_field_header_10>".$link_text;
							if ($this->browse_type == 33) $this->body .= "34";
							elseif ($this->browse_type == 34) $this->body .= "0";
							else $this->body .= "33";
							$this->body .= " class=optional_field_header_10>".urldecode($this->messages[931])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_11'])
						{
							$this->body .="<td class=optional_field_header_11>".$link_text;
							if ($this->browse_type == 45) $this->body .= "46";
							elseif ($this->browse_type == 46) $this->body .= "0";
							else $this->body .= "45";
							$this->body .= " class=optional_field_header_11>".urldecode($this->messages[1696])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_12'])
						{
							$this->body .="<td class=optional_field_header_12>".$link_text;
							if ($this->browse_type == 47) $this->body .= "48";
							elseif ($this->browse_type == 48) $this->body .= "0";
							else $this->body .= "47";
							$this->body .= " class=optional_field_header_12>".urldecode($this->messages[1697])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_13'])
						{
							$this->body .="<td class=optional_field_header_13>".$link_text;
							if ($this->browse_type == 49) $this->body .= "50";
							elseif ($this->browse_type == 50) $this->body .= "0";
							else $this->body .= "49";
							$this->body .= " class=optional_field_header_13>".urldecode($this->messages[1698])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_14'])
						{
							$this->body .="<td class=optional_field_header_14>".$link_text;
							if ($this->browse_type == 51) $this->body .= "52";
							elseif ($this->browse_type == 52) $this->body .= "0";
							else $this->body .= "51";
							$this->body .= " class=optional_field_header_14>".urldecode($this->messages[1699])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_15'])
						{
							$this->body .="<td class=optional_field_header_15>".$link_text;
							if ($this->browse_type == 53) $this->body .= "54";
							elseif ($this->browse_type == 54) $this->body .= "0";
							else $this->body .= "53";
							$this->body .= " class=optional_field_header_15>".urldecode($this->messages[1700])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_16'])
						{
							$this->body .="<td class=optional_field_header_16>".$link_text;
							if ($this->browse_type == 55) $this->body .= "56";
							elseif ($this->browse_type == 56) $this->body .= "0";
							else $this->body .= "55";
							$this->body .= " class=optional_field_header_16>".urldecode($this->messages[1701])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_17'])
						{
							$this->body .="<td class=optional_field_header_17>".$link_text;
							if ($this->browse_type == 57) $this->body .= "58";
							elseif ($this->browse_type == 58) $this->body .= "0";
							else $this->body .= "57";
							$this->body .= " class=optional_field_header_17>".urldecode($this->messages[1702])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_18'])
						{
							$this->body .="<td class=optional_field_header_18>".$link_text;
							if ($this->browse_type == 59) $this->body .= "60";
							elseif ($this->browse_type == 60) $this->body .= "0";
							else $this->body .= "59";
							$this->body .= " class=optional_field_header_18>".urldecode($this->messages[1703])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_19'])
						{
							$this->body .="<td class=optional_field_header_19>".$link_text;
							if ($this->browse_type == 61) $this->body .= "62";
							elseif ($this->browse_type == 62) $this->body .= "0";
							else $this->body .= "61";
							$this->body .= " class=optional_field_header_19>".urldecode($this->messages[1704])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_optional_field_20'])
						{
							$this->body .="<td class=optional_field_header_20>".$link_text;
							if ($this->browse_type == 63) $this->body .= "64";
							elseif ($this->browse_type == 64) $this->body .= "0";
							else $this->body .= "63";
							$this->body .= " class=optional_field_header_20>".urldecode($this->messages[1705])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_browsing_city_field'])
						{
							$this->body .="<td class=city_column_header>".$link_text;
							if ($this->browse_type == 7) $this->body .= "8";
							elseif ($this->browse_type == 8) $this->body .= "0";
							else $this->body .= "7";
							$this->body .= " class=city_column_header>".urldecode($this->messages[1199])."</a></td>\n\t";
						}

						if ($this->configuration_data['display_browsing_state_field'])
						{
							$this->body .="<td class=state_column_header>".$link_text;
							if ($this->browse_type == 37) $this->body .= "38";
							elseif ($this->browse_type == 38) $this->body .= "0";
							else $this->body .= "37";
							$this->body .= " class=state_column_header>".urldecode($this->messages[1200])."</a></td>\n\t";
						}
						if ($this->configuration_data['display_browsing_country_field'])
						{
							$this->body .="<td class=country_column_header>".$link_text;
							if ($this->browse_type == 39) $this->body .= "40";
							elseif ($this->browse_type == 40) $this->body .= "0";
							else $this->body .= "39";
							$this->body .= " class=country_column_header>".urldecode($this->messages[1201])."</a></td>\n\t";
						}
						if ($this->configuration_data['display_browsing_zip_field'])
						{
							$this->body .="<td class=zip_column_header>".$link_text;
							if ($this->browse_type == 41) $this->body .= "42";
							elseif ($this->browse_type == 42) $this->body .= "0";
							else $this->body .= "41";
							$this->body .= " class=zip_column_header>".urldecode($this->messages[1202])."</a></td>\n\t";
						}
						if ($this->configuration_data['display_price'])
							$this->body .="<td  class=price_column_header >".$link_text;
							if ($this->browse_type == 1) $this->body .= "2";
							elseif ($this->browse_type == 2) $this->body .= "0";
							else $this->body .= "1";
							$this->body .= " class=price_column_header>".urldecode($this->messages[27])."</a></td>\n\t";

						if ($this->configuration_data['display_entry_date'])
							$this->body .="<td  class=entry_date_column_header>".urldecode($this->messages[22])."</td>\n\t";

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
									$css_class_tag= "browsing_result_table_body_even_bold";
								else
									$css_class_tag=  "browsing_result_table_body_even ";
							}
							else
							{
								if ($show_classifieds->BOLDING)
									$css_class_tag=  "browsing_result_table_body_odd_bold";
								else
									$css_class_tag=  "browsing_result_table_body_odd ";
							}
							$this->body .="<tr class=".$css_class_tag.">\n\t\t";
							
							
							if ($this->configuration_data['display_photo_icon'])
							{
								if ($this->configuration_data['photo_or_icon'] == 1)
								{
									if ($show_classifieds->IMAGE > 0)
										$this->display_thumbnail($db,$show_classifieds->ID,0,0,0,$this->affiliate_id);
									elseif (($this->configuration_data['no_image_url']) && (!$show_classifieds->IMAGE))
									{
										if (($this->configuration_data['popup_while_browsing'])
											&& ($this->configuration_data['popup_while_browsing_width'])
											&& ($this->configuration_data['popup_while_browsing_height']))
										{
											$this->body .= "<td align=center><a href=\"";
											$this->body .= $this->configuration_data['affiliate_url'];
											$this->body .= "?a=2&b=".$show_classifieds->ID."\" ";
											$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
											$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
										}
										else
										{
											$this->body .="<td align=center><a href=".$this->configuration_data['affiliate_url'];
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
										$this->body .="<td valign=middle align=center><img src=".$this->configuration_data['photo_icon_url']."></td>";
									}
									elseif (($this->configuration_data['no_image_url']) && (!$show_classifieds->IMAGE))
									{
										if (($this->configuration_data['popup_while_browsing'])
											&& ($this->configuration_data['popup_while_browsing_width'])
											&& ($this->configuration_data['popup_while_browsing_height']))
										{
											$this->body .= "<td align=center><a href=\"";
											$this->body .= $this->configuration_data['affiliate_url'];
											$this->body .= "?a=2&b=".$show_classifieds->ID."\" ";
											$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
											$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
										}
										else
										{
											$this->body .="<td align=center><a href=".$this->configuration_data['affiliate_url'];
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
								$this->body .="<td >\n\t\t<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=2&b=".$show_classifieds->ID." class=".$css_class_tag.">".stripslashes(urldecode($show_classifieds->TITLE))."</a>\n\t\t";
								if ($show_classifieds->ATTENTION_GETTER)
								{
									$this->body .= "<img src=\"".$show_classifieds->ATTENTION_GETTER_URL."\" border=0 hspace=2>";
								}
								if (($this->configuration_data['display_ad_description']) && ($this->configuration_data['display_ad_description_where']))
								{
									$this->body .="<br>";
									if (strlen(urldecode($show_classifieds->DESCRIPTION)) > $this->configuration_data['length_of_description'])
									{
										$small_string = substr(trim(stripslashes(urldecode($show_classifieds->DESCRIPTION))),0,$this->configuration_data['length_of_description']);
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
										$small_string = substr(trim(stripslashes(urldecode($show_classifieds->DESCRIPTION))),0,$this->configuration_data['length_of_description']);
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
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_1))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_1));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_2'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_2))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_2));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_3'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_3))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_3));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_4'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_4))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_4));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_5'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_5))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_5));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_6'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_6))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_6));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_7'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_7))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_7));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_8'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_8))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_8));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_9'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_9))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_9));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_10'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_10))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_10));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_11'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_11))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_11));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_12'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_12))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_12));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_13'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_13))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_13));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_14'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_14))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_14));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_15'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_15))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_15));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_16'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_16))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_16));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_17'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_17))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_17));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_18'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_18))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_18));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_19'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_19))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_19));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_optional_field_20'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_20))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_20));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_browsing_city_field'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->LOCATION_CITY))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_CITY));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_browsing_state_field'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->LOCATION_STATE))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_STATE));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_browsing_country_field'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->LOCATION_COUNTRY))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_COUNTRY));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_browsing_zip_field'])
							{
								$this->body .="<td >";
									if (strlen(trim(urldecode($show_classifieds->LOCATION_ZIP))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_ZIP));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->configuration_data['display_price'])
							{
								$this->body .="<td  align=center>";
								if (((strlen(trim(urldecode($show_classifieds->PRICE))) > 0)
									|| (strlen(trim(urldecode($show_classifieds->PRECURRENCY))) > 0)
									|| (strlen(trim(urldecode($show_classifieds->POSTCURRENCY))) > 0)) && ($show_classifieds->PRICE != 0))

									$this->body .=stripslashes(urldecode($show_classifieds->PRECURRENCY." ".number_format($show_classifieds->PRICE,2,".",",")." ".$show_classifieds->POSTCURRENCY));
								else
									$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->configuration_data['display_entry_date'])
								$this->body .="<td >".date("M d-G:i",$show_classifieds->DATE)."</td>\n\t";

							if ($this->classified_user_id == 1)
							{
								//this is the admin
								$this->body .="<td >\n\t\t<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=4&e=".$show_classifieds->ID."&b=5>edit</a>\n\t\t</td>\n\t";
								$this->body .="<td >\n\t\t<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=99&b=".$show_classifieds->ID."&c=".$category.">delete</a>\n\t\t</td>\n\t";

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

	function browse_error ()
	{
		//this->error_message is the class variable that will contain the error message
		$this->body .="<table cellpadding=5 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr>\n\t<td>".urldecode($this->messages[80])."</td>\n</tr>\n";
				if ($this->error_message)
			$this->body .="<tr>\n\t<td>".$this->error_message."</td>\n</tr>\n";
		$this->body .="</table>\n";
	 } //end of function browse_error

//####################################################################################

} // end of class Browse_ads

?>