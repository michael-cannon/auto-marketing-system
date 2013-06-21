<? //browse_ads.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Browse_newest_ads extends Site {
	var $subcategory_array = array();
	var $notify_data = array();
	var $sort_type;

//########################################################################

	function Browse_newest_ads($db,$classified_user_id,$language_id,$category_id=0,$page=0,$browse_type=0,$filter_id=0,$state_filter=0,$zip_filter=0,$zip_filter_distance=0,$product_configuration=0)
	{
		if ($category_id)
			$this->site_category = $category_id;
		else
			$this->site_category = 0;

		$this->Site($db,1,$language_id,$classified_user_id,$product_configuration);
		$this->get_ad_configuration($db);
		if ($page)
			$this->page_result = $page;
		else
			$this->page_result = 1;

		$this->filter_id = $filter_id;
		$this->browse_type = $browse_type;
		$this->state_filter = $state_filter;
		$this->zip_filter = $zip_filter;
		$this->zip_filter_distance = $zip_filter_distance;
	} //end of function Browse_newest_ads

//###########################################################

	function browse($db,$sort_type=0)
	{
		$this->sort_type = $sort_type;
		$this->page_id = 64;
		$this->get_text($db);
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		//browse the auctions in this category that are open
		//$this->body .=$this->configuration_data['display_sub_category_ads']." display sub cats<Br>\n";

		if ($this->configuration_data['display_sub_category_ads'])
		{
			$this->get_sql_in_statement($db,$this->site_category);
		}
		else
		{
			$this->in_statement = " in (".$this->site_category.") ";
		}

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
			$this->sql_zip_filter_in_statement = " and ".$zip_filter_in_statement." ";
		}

		switch ($this->browse_type)
		{
			case 1:
				//last 1 week
				$cutoff_time = ($this->shifted_time($db) - (86400 * 7));
				$header_display = $this->messages[901];
				break;
			case 2:
				//last 2 weeks
				$cutoff_time = ($this->shifted_time($db) - (86400 * 14));
				$header_display = $this->messages[902];
				break;
			case 3:
				//last 3 weeks
				$cutoff_time = ($this->shifted_time($db) - (86400 * 21));
				$header_display = $this->messages[903];
				break;
			case 4:
				//last 24 hours
				$cutoff_time = ($this->shifted_time($db) - 86400);
				$header_display = $this->messages[900];
				break;
			default:
				//last 1 week
				$cutoff_time = ($this->shifted_time($db) - (86400 * 7));
				$header_display = $this->messages[901];
		} //end of switch

		if (($this->sort_type == 0) && ($this->configuration_data['default_display_order_while_browsing']))
			$this->sort_type = $this->configuration_data['default_display_order_while_browsing'];

		switch ($this->sort_type)
		{
			case 0: //nothing
 				$order_by = "order by better_placement desc ";
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
				$order_by = "order by better_placement desc ";
				break;
		}
		$this->sql_query = "select * from ".$this->classifieds_table." where
			category ".$this->in_statement." and live = 1 ";
		$this->sql_query_classifieds = $this->sql_query." and item_type = 1 ";
		$this->sql_query_auctions = $this->sql_query." and item_type = 2 ";
		if ($this->filter_id)
		{
			$this->sql_query_classifieds .= $this->sql_filter_in_statement;
			$this->sql_query_auctions .= $this->sql_filter_in_statement;
		}
		if ($this->state_filter)
		{
			$this->sql_query_classifieds .= $this->sql_state_filter_statement;
			$this->sql_query_auctions .= $this->sql_state_filter_statement;
		}
		if (($this->zip_filter_distance) && ($this->zip_filter))
		{
			$this->sql_query_classifieds .= $this->sql_zip_filter_in_statement;
			$this->sql_query_auctions .= $this->sql_zip_filter_in_statement;
		}
		$this->sql_query_classifieds .= "and date > ".$cutoff_time." ".$order_by." ,date desc limit ".(($this->page_result -1) * $this->configuration_data['number_of_ads_to_display']).",".$this->configuration_data['number_of_ads_to_display'];
		$this->sql_query_auctions .= "and date > ".$cutoff_time." ".$order_by." ,date desc limit ".(($this->page_result -1) * $this->configuration_data['number_of_ads_to_display']).",".$this->configuration_data['number_of_ads_to_display'];

		$this->sql_query_count = "select count(id) as total from ".$this->classifieds_table." where
			category ".$this->in_statement." and live = 1 ";
		if ($this->filter_id)
			$this->sql_query_count .= $sql_filter_in_statement;
		if ($this->state_filter)
			$this->sql_query_count .= $this->sql_state_filter_statement;
		if (($this->zip_filter_distance) && ($this->zip_filter))
			$this->sql_query_count .= $this->sql_zip_filter_in_statement;
		$this->sql_query_count .= "and date > ".$cutoff_time;
		$this->sql_query_count .= " group by item_type";
		//echo $this->sql_query."<br>\n";

		$result = $db->Execute($this->sql_query_classifieds);
		$result_auctions = $db->Execute($this->sql_query_auctions);
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
					$total_returned_ads = $show_total->TOTAL;
					$show_total = $total_count_result->FetchNextObject();
					$total_returned_auctions = $show_total->TOTAL;
					if($total_returned_ads>$total_returned_auctions)
						$total_returned = $total_returned_ads;
					else
						$total_returned = $total_returned_ads;
					//$this->body .=$total_returned." is the total returned<br>\n";
				}
			}
			//get this categories name
			if ($this->site_category)
			{
				$current_category_name = $this->get_category_name($db,$this->site_category);

				//get the categories inside of this category
				//$this->sql_query = "select * from ".$this->categories_table." where
				//	parent_id = ".$this->site_category." order by display_order,category_name";
				$this->sql_query = "select ".$this->categories_table.".category_id,
					".$this->categories_table.".category_image,
					".$this->categories_table.".category_count,
					".$this->categories_languages_table.".category_name,
					".$this->categories_languages_table.".description
					from ".$this->categories_table.",".$this->categories_languages_table." where
					parent_id = ".$this->site_category." and
					".$this->categories_table.".category_id = ".$this->categories_languages_table.".category_id and
					".$this->categories_languages_table.".language_id = ".$this->language_id."
					order by ".$this->categories_table.".display_order,".$this->categories_languages_table.".category_name";


				$this->category_sql_query = "select * from ".$this->categories_languages_table." where
					category_id = ".$this->site_category." and language_id = ".$this->language_id;
				//echo $this->category_sql_query." is category<bR>\n";

				$current_category_result = $db->Execute($this->category_sql_query);
				if (!$current_category_result)
				{
					//echo $this->category_sql_query." is category<bR>\n";
					return false;
				}
				elseif ($current_category_result->RecordCount() == 1)
				{
					$cache_info = $current_category_result->FetchNextObject();
				}
				else
				{
					$cache_info = $current_category_name;
				}
			}
			else
			{
				$current_category_name = urldecode($this->messages[904]);

				//get the categories inside of this category
				//$this->sql_query = "select * from ".$this->categories_table." where
				//	parent_id = 0 order by display_order,category_name";

				$this->sql_query = "select ".$this->categories_table.".category_id,
					".$this->categories_table.".category_image,
					".$this->categories_table.".category_count,
					".$this->categories_languages_table.".category_name,
					".$this->categories_languages_table.".description
					from ".$this->categories_table.",".$this->categories_languages_table." where
					parent_id = 0 and
					".$this->categories_table.".category_id = ".$this->categories_languages_table.".category_id and
					".$this->categories_languages_table.".language_id = ".$this->language_id."
					order by ".$this->categories_table.".display_order,".$this->categories_languages_table.".category_name";


				$cache_info = $current_category_name;

			}
			$category_result = $db->Execute($this->sql_query);
			if (!$category_result)
			{
				$this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
				return false;
			}
			else
			{
				
				if ($this->get_category_configuration($db,$this->site_category))
				{
					if (!$this->category_configuration['use_site_default'])
					{
						$this->browsing_configuration = $this->configuration_data;
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
					$this->browsing_configuration = $this->configuration_data;
				}
				if (($current_category_name->NEWEST_CACHE_EXPIRE > $this->shifted_time($db)) && ($this->configuration_data['use_category_cache']) && ($current_category_name->NEWEST_CACHE_EXPIRE != 0) && ($this->site_category))
				{
					//use the cache
					echo "using category cache<br>\n";
					$this->body .= $current_category_name->NEWEST_CATEGORY_CACHE;
				}
				else
				{
					//$this->body .=$this->sql_query." is the query<br>\n";
					$this->category_cache = "";
					$this->category_cache .="<tr>\n\t<td valign=top class=back_to_normal_browsing>
						<a href=".$this->configuration_data['classifieds_url']."?a=5&b=".$this->site_category." class=back_to_normal_browsing>";
					$this->category_cache .= urldecode($this->messages[888])."</a></td>\n</tr>\n";

					if (($this->configuration_data['category_tree_display'] == 1) || ($this->configuration_data['category_tree_display'] == 2) || ($this->configuration_data['category_tree_display'] == 0))
					{
						$category_tree = $this->get_category_tree($db,$this->site_category);
						reset ($this->category_tree_array);

						if ($category_tree)
						{
							//category tree
							$this->current_category_tree .="<tr class=main>\n\t<td valign=top height=20 class=browsing_category_tree>\n\t";
							$this->current_category_tree .=urldecode($this->messages[890])." <a href=".$this->configuration_data['classifieds_file_name']."?a=11&b=0&c=".$this->browse_type." class=main>".$this->messages[891]."</a> > ";
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
										$this->current_category_tree .=urldecode(stripslashes($this->category_tree_array[$i]["category_name"]));
									else
										$this->current_category_tree .="<a href=".$this->configuration_data['classifieds_file_name']."?a=11&b=".$this->category_tree_array[$i]["category_id"]."&c=".$this->browse_type." class=browsing_category_tree>".urldecode(stripslashes($this->category_tree_array[$i]["category_name"]))."</a> > ";
								}
							}
							else
							{
								$this->current_category_tree .=$category_tree;
							}
							$this->current_category_tree .="\n\t</td>\n</tr>\n";
						}
					}
					if (($this->configuration_data['category_tree_display'] == 1) || ($this->configuration_data['category_tree_display'] == 2))
						$this->category_cache .= $this->current_category_tree;

					if ($this->configuration_data['display_category_navigation'])
					{
						if ($category_result->RecordCount() > 0)
						{
							if ($this->site_category)
								$category_columns = $this->configuration_data['number_of_browsing_subcategory_columns'];
							else
								$category_columns = $this->configuration_data['number_of_browsing_columns'];
							$this->category_cache .="<tr>\n\t<td valign=top>\n\t<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
							switch ($category_columns)
							{
								case 1: $column_width = "100%";
								case 2: $column_width = "50%";
								case 3: $column_width = "33%";
								case 4: $column_width = "25%";
								case 5: $column_width = "20%";
							} //end of switch
							while ($show_category = $category_result->FetchNextObject())
							{
								//display the sub categories of this category
								$this->category_cache .="<tr><td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=11&b=".$show_category->CATEGORY_ID."&c=".$this->browse_type.">";
								if (strlen(trim($show_category->CATEGORY_IMAGE)) > 0)
								{
									$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
								}
								//$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
								$this->category_cache .="<font class=browsing_subcategory_name>".urldecode(stripslashes($show_category->CATEGORY_NAME))."</font>";
								if ($this->configuration_data['display_category_description'])
									$this->category_cache .="</a><br><font class=browsing_subcategory_description>".urldecode(stripslashes($show_category->DESCRIPTION))."</font>";
								$this->category_cache .="</td>";
								if ($category_columns > 1)
								{
									if ($show_category = $category_result->FetchNextObject())
									{
										$this->category_cache .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=11&b=".$show_category->CATEGORY_ID."&c=".$this->browse_type.">";
										if (strlen(trim($show_category->CATEGORY_IMAGE)) > 0)
										{
											$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
										}
										//$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
										$this->category_cache .="<font class=browsing_subcategory_name>".urldecode(stripslashes($show_category->CATEGORY_NAME))."</font>";
										if ($this->configuration_data['display_category_description'])
											$this->category_cache .="</a><br><font class=browsing_subcategory_description>".urldecode(stripslashes($show_category->DESCRIPTION))."</font>";
										$this->category_cache .="</td>";
									}
									else
									{
										$this->category_cache .="<td valign=top width=".$column_width.">&nbsp;</td>";
									}
									if ($category_columns > 2)
									{
										if ($show_category = $category_result->FetchNextObject())
										{
											$this->category_cache .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=11&b=".$show_category->CATEGORY_ID."&c=".$this->browse_type.">";
											if (strlen(trim($show_category->CATEGORY_IMAGE)) > 0)
											{
												$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
											}
											//$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
											$this->category_cache .="<font class=browsing_subcategory_name>".urldecode(stripslashes($show_category->CATEGORY_NAME))."</font>";
											if ($this->configuration_data['display_category_description'])
												$this->category_cache .="</a><br><font class=browsing_subcategory_description>".urldecode(stripslashes($show_category->DESCRIPTION))."</font>";
											$this->category_cache .="</td>";
										}
										else
										{
											$this->category_cache .="<td valign=top width=".$column_width.">&nbsp;</td>";
										}
										if ($category_columns > 3)
										{
											if ($show_category = $category_result->FetchNextObject())
											{
												$this->category_cache .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=11&b=".$show_category->CATEGORY_ID."&c=".$this->browse_type.">";
												if (strlen(trim($show_category->CATEGORY_IMAGE)) > 0)
												{
													$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
												}
												//$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
												$this->category_cache .="<font class=browsing_subcategory_name>".urldecode(stripslashes($show_category->CATEGORY_NAME))."</font>";
												if ($this->configuration_data['display_category_description'])
													$this->category_cache .="</a><br><font class=browsing_subcategory_description>".urldecode(stripslashes($show_category->DESCRIPTION))."</font>";
												$this->category_cache .="</td>";
											}
											else
											{
												$this->category_cache .="<td valign=top width=".$column_width.">&nbsp;</td>";
											}
											if ($category_columns > 4)
											{
												if ($show_category = $category_result->FetchNextObject())
												{
													$this->category_cache .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=11&b=".$show_category->CATEGORY_ID."&c=".$this->browse_type.">";
													if (strlen(trim($show_category->CATEGORY_IMAGE)) > 0)
													{
														$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
													}
													//$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
													$this->category_cache .="<font class=browsing_subcategory_name>".urldecode(stripslashes($show_category->CATEGORY_NAME))."</font>";
													if ($this->configuration_data['display_category_description'])
														$this->category_cache .="</a><br><font class=browsing_subcategory_description>".urldecode(stripslashes($show_category->DESCRIPTION))."</font>";
													$this->category_cache .="</td>";
												}
												else
												{
													$this->category_cache .="<td  width=".$column_width.">&nbsp;</td>";
												}
											}
										}
									}
								}
								$this->category_cache .="</tr>";
							}
							$this->category_cache .="</table>\n\t</td>\n</tr>\n";
						}
						else
						{
							if ($this->configuration_data['display_no_subcategory_message'])
								$this->category_cache .="<tr class=no_subcategories_to>\n\t<td  height=20>\n\t".urldecode($this->messages[889])." ".$category_name."\n\t</td>\n</tr>\n";
						}

						if ($this->configuration_data['use_category_cache'])
						{
							$recache_time = $this->shifted_time($db) + (3600 * $this->configuration_data['use_category_cache']);
							$this->sql_query = "update ".$this->categories_languages_table." set
								newest_category_cache = \"".addslashes(urlencode($this->category_cache))."\",
								newest_cache_expire = \"".$recache_time."\"
								where category_id = ".$this->site_category." and language_id = ".$this->language_id;
							//echo $this->sql_query."<Br>\n";
							$cache_result = $db->Execute($this->sql_query);
							if (!$cache_result)
							{
								$this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
								return false;
							}
						}
					}
				}
				if (($this->configuration_data['category_tree_display'] == 0) || ($this->configuration_data['category_tree_display'] == 2))
					$this->category_cache .= $this->current_category_tree;
				$this->body .= urldecode(stripslashes($this->category_cache));
			}

			// MLC turn off classifeds
			if( false && $this->is_class_auctions()||$this->is_classifieds())
			{
				//featured ads
				$result->Move(0);
				$this->body .="<tr><td  class=newest_ad_header >".urldecode($this->messages[899])." ";
				if ($this->site_category)
					$this->body .= $current_category_name->CATEGORY_NAME;
				else
					$this->body .= $current_category_name;
				$this->body .= " ".urldecode($header_display)." </td>\n</tr>\n";
				$this->body .="<tr>\n\t<td >\n\t";
				$this->body .="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n\t";
	
				$this->display_browse_result($db,$result,"browsing_result_table_header");
			}
			
			if($this->is_class_auctions()||$this->is_auctions())
			{
				//auctions
				$result->Move(0);
				$this->body .="<tr><td  class=newest_ad_header >".urldecode($this->messages[100899])." ";
				if ($this->site_category)
					$this->body .= $current_category_name->CATEGORY_NAME;
				else
					$this->body .= $current_category_name;
				$this->body .= " ".urldecode($header_display)." </td>\n</tr>\n";
				$this->body .="<tr>\n\t<td >\n\t";
				$this->body .="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n\t";
	
				$this->display_browse_result($db,$result_auctions,"browsing_result_table_header",1);
				$this->body .= "</table></td></tr>";
			}

			if ($this->configuration_data['number_of_ads_to_display'] < $total_returned)
			{
				//display the link to the next 10
				$number_of_page_results = ceil($total_returned / $this->configuration_data['number_of_ads_to_display']);
				$this->body .="<tr class=more_results>\n\t<td >".urldecode($this->messages[892])." ";
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
							$this->body .="<a href=".$this->configuration_data['classifieds_url']."?a=11&b=".$this->site_category."&c=".$this->browse_type."&page=".$i." class=browsing_result_page_links>".$i."</a> ";
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
									$this->body .="<a href=".$this->configuration_data['classifieds_url']."?a=11&b=".$this->site_category."&c=".$this->browse_type."&page=".$page." class=browsing_result_page_links>".$page."</a> ";
							}

						}
						else
						{
							//display the link to the section
							$this->body .="<a href=".$this->configuration_data['classifieds_url']."?a=11&b=".$this->site_category."&c=".$this->browse_type."&page=".(($section*10)+1)." class=browsing_result_page_links>".(($section*10)+1)."</a>";
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

	function display_browse_result($db,$browse_result,$header_css, $auction=0)
	{
					if ($browse_result->RecordCount() > 0)
					{
						$browse_result->Move(0);
						//display the ads inside of this category
						$link_text = "<a href=".$this->configuration_data['classifieds_file_name']."?a=11&b=".$this->site_category."&c=".$this->browse_type."&d=";
						//display the ads inside of this category
						$this->body .="<tr>\n\t<td  height=20>\n\t";
						$this->body .="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n\t";
						$this->body .="<tr class=".$header_css.">\n\t\t";
						if ($this->browsing_configuration['display_business_type'])
						{
							$this->body .="<td class=business_type_column_header>".$link_text;
							if ($this->sort_type == 43) $this->body .= "44";
							elseif ($this->sort_type == 44) $this->body .= "0";
							else $this->body .= "43";
							$this->body .= " class=business_type_column_header>".urldecode($this->messages[1404])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_photo_icon'])
							$this->body .= "<td  class=photo_column_header>".urldecode($this->messages[893])."</td>\n\t";
						if ($this->browsing_configuration['display_ad_title'])
						{
							$this->body .="<td  class=title_column_header>".$link_text;
							if ($this->sort_type == 5) $this->body .= "6";
							elseif ($this->sort_type == 6) $this->body .= "0";
							else $this->body .= "5";
							$this->body .= " class=title_column_header>".urldecode($this->messages[894])."</a>";
							if (($this->browsing_configuration['display_ad_description']) && ($this->browsing_configuration['display_ad_description_where']))
								$this->body .="<br>".urldecode($this->messages[895]);

							$this->body .="</td>\n\t\t";
						}
						if (($this->browsing_configuration['display_ad_description']) && (!$this->browsing_configuration['display_ad_description_where']))
							$this->body .="<td   class=description_column_header>".urldecode($this->messages[895])."</td>\n\t";

						if ($this->browsing_configuration['display_optional_field_1'])
						{
							$this->body .="<td class=optional_field_header_1>".$link_text;
							if ($this->sort_type == 15) $this->body .= "16";
							elseif ($this->sort_type == 16) $this->body .= "0";
							else $this->body .= "15";
							$this->body .= " class=optional_field_header_1>".urldecode($this->messages[969])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_2'])
						{
							$this->body .="<td class=optional_field_header_2>".$link_text;
							if ($this->sort_type == 17) $this->body .= "18";
							elseif ($this->sort_type == 18) $this->body .= "0";
							else $this->body .= "17";
							$this->body .= " class=optional_field_header_2>".urldecode($this->messages[970])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_3'])
						{
							$this->body .="<td class=optional_field_header_3>".$link_text;
							if ($this->sort_type == 19) $this->body .= "20";
							elseif ($this->sort_type == 20) $this->body .= "0";
							else $this->body .= "19";
							$this->body .= " class=optional_field_header_3>".urldecode($this->messages[971])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_4'])
						{
							$this->body .="<td class=optional_field_header_4>".$link_text;
							if ($this->sort_type == 21) $this->body .= "22";
							elseif ($this->sort_type == 22) $this->body .= "0";
							else $this->body .= "21";
							$this->body .= " class=optional_field_header_4>".urldecode($this->messages[972])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_5'])
						{
							$this->body .="<td class=optional_field_header_5>".$link_text;
							if ($this->sort_type == 23) $this->body .= "24";
							elseif ($this->sort_type == 24) $this->body .= "0";
							else $this->body .= "23";
							$this->body .= " class=optional_field_header_5>".urldecode($this->messages[973])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_6'])
						{
							$this->body .="<td class=optional_field_header_6>".$link_text;
							if ($this->sort_type == 25) $this->body .= "26";
							elseif ($this->sort_type == 26) $this->body .= "0";
							else $this->body .= "25";
							$this->body .= " class=optional_field_header_6>".urldecode($this->messages[974])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_7'])
						{
							$this->body .="<td class=optional_field_header_7>".$link_text;
							if ($this->sort_type == 27) $this->body .= "28";
							elseif ($this->sort_type == 28) $this->body .= "0";
							else $this->body .= "27";
							$this->body .= " class=optional_field_header_7>".urldecode($this->messages[975])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_8'])
						{
							$this->body .="<td class=optional_field_header_8>".$link_text;
							if ($this->sort_type == 29) $this->body .= "30";
							elseif ($this->sort_type == 30) $this->body .= "0";
							else $this->body .= "29";
							$this->body .= " class=optional_field_header_8>".urldecode($this->messages[976])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_9'])
						{
							$this->body .="<td class=optional_field_header_9>".$link_text;
							if ($this->sort_type == 31) $this->body .= "32";
							elseif ($this->sort_type == 32) $this->body .= "0";
							else $this->body .= "31";
							$this->body .= " class=optional_field_header_9>".urldecode($this->messages[977])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_10'])
						{
							$this->body .="<td class=optional_field_header_10>".$link_text;
							if ($this->sort_type == 33) $this->body .= "34";
							elseif ($this->sort_type == 34) $this->body .= "0";
							else $this->body .= "33";
							$this->body .= " class=optional_field_header_10>".urldecode($this->messages[978])."</a></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_11'])
						{
							$this->body .="<td class=optional_field_header_11>".$link_text;
							if ($this->browse_type == 45) $this->body .= "46";
							elseif ($this->browse_type == 46) $this->body .= "0";
							else $this->body .= "45";
							$this->body .= " class=optional_field_header_11>".urldecode($this->messages[2291])."</a></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_12'])
						{
							$this->body .="<td class=optional_field_header_12>".$link_text;
							if ($this->browse_type == 47) $this->body .= "48";
							elseif ($this->browse_type == 48) $this->body .= "0";
							else $this->body .= "47";
							$this->body .= " class=optional_field_header_12>".urldecode($this->messages[2292])."</a></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_13'])
						{
							$this->body .="<td class=optional_field_header_13>".$link_text;
							if ($this->browse_type == 49) $this->body .= "50";
							elseif ($this->browse_type == 50) $this->body .= "0";
							else $this->body .= "49";
							$this->body .= " class=optional_field_header_13>".urldecode($this->messages[2293])."</a></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_14'])
						{
							$this->body .="<td class=optional_field_header_14>".$link_text;
							if ($this->browse_type == 51) $this->body .= "52";
							elseif ($this->browse_type == 52) $this->body .= "0";
							else $this->body .= "51";
							$this->body .= " class=optional_field_header_14>".urldecode($this->messages[2294])."</a></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_15'])
						{
							$this->body .="<td class=optional_field_header_15>".$link_text;
							if ($this->browse_type == 53) $this->body .= "54";
							elseif ($this->browse_type == 54) $this->body .= "0";
							else $this->body .= "53";
							$this->body .= " class=optional_field_header_15>".urldecode($this->messages[2295])."</a></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_16'])
						{
							$this->body .="<td class=optional_field_header_16>".$link_text;
							if ($this->browse_type == 55) $this->body .= "56";
							elseif ($this->browse_type == 56) $this->body .= "0";
							else $this->body .= "55";
							$this->body .= " class=optional_field_header_16>".urldecode($this->messages[2296])."</a></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_17'])
						{
							$this->body .="<td class=optional_field_header_17>".$link_text;
							if ($this->browse_type == 57) $this->body .= "58";
							elseif ($this->browse_type == 58) $this->body .= "0";
							else $this->body .= "57";
							$this->body .= " class=optional_field_header_17>".urldecode($this->messages[2297])."</a></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_18'])
						{
							$this->body .="<td class=optional_field_header_18>".$link_text;
							if ($this->browse_type == 59) $this->body .= "60";
							elseif ($this->browse_type == 60) $this->body .= "0";
							else $this->body .= "59";
							$this->body .= " class=optional_field_header_18>".urldecode($this->messages[2298])."</a></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_19'])
						{
							$this->body .="<td class=optional_field_header_19>".$link_text;
							if ($this->browse_type == 61) $this->body .= "62";
							elseif ($this->browse_type == 62) $this->body .= "0";
							else $this->body .= "61";
							$this->body .= " class=optional_field_header_19>".urldecode($this->messages[2299])."</a></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_20'])
						{
							$this->body .="<td class=optional_field_header_20>".$link_text;
							if ($this->browse_type == 63) $this->body .= "64";
							elseif ($this->browse_type == 64) $this->body .= "0";
							else $this->body .= "63";
							$this->body .= " class=optional_field_header_20>".urldecode($this->messages[2300])."</a></td>\n\t";
						}

						if ($this->browsing_configuration['display_browsing_city_field'])
						{
							$this->body .="<td class=city_column_header>".$link_text;
							if ($this->sort_type == 35) $this->body .= "36";
							elseif ($this->sort_type == 36) $this->body .= "0";
							else $this->body .= "35";
							$this->body .= " class=city_column_header>".urldecode($this->messages[1335])."</a></td>\n\t";
						}

						if ($this->browsing_configuration['display_browsing_state_field'])
						{
							$this->body .="<td class=state_column_header>".$link_text;
							if ($this->sort_type == 37) $this->body .= "38";
							elseif ($this->sort_type == 38) $this->body .= "0";
							else $this->body .= "37";
							$this->body .= " class=state_column_header>".urldecode($this->messages[1336])."</td>\n\t";
						}
						if ($this->browsing_configuration['display_browsing_country_field'])
						{
							$this->body .="<td class=country_column_header>".$link_text;
							if ($this->sort_type == 39) $this->body .= "40";
							elseif ($this->sort_type == 40) $this->body .= "0";
							else $this->body .= "39";
							$this->body .= " class=country_column_header>".urldecode($this->messages[1337])."</td>\n\t";
						}
						if ($this->browsing_configuration['display_browsing_zip_field'])
						{
							$this->body .="<td class=zip_column_header>".$link_text;
							if ($this->sort_type == 41) $this->body .= "42";
							elseif ($this->sort_type == 42) $this->body .= "0";
							else $this->body .= "41";
							$this->body .= " class=zip_column_header>".urldecode($this->messages[1338])."</td>\n\t";
						}

						if ($this->browsing_configuration['display_price'])
						{
							$this->body .="<td  class=price_column_header >".$link_text;
							if ($this->sort_type == 1) $this->body .= "2";
							elseif ($this->sort_type == 2) $this->body .= "0";
							else $this->body .= "1";
							$this->body .= " class=price_column_header>".urldecode($this->messages[896])."</td>\n\t";
						}

						if ($this->browsing_configuration['display_entry_date']
							&& (!$auction || ($this->browsing_configuration['auction_entry_date'] && $auction)))
							$this->body .="<td  class=entry_date_column_header>".urldecode($this->messages[897])."</td>\n\t";

						if ($auction && $this->browsing_configuration['display_number_bids'])
						{
							$this->body .= "<td class=newest_bids_column_header>".urldecode($this->messages[102537])."</td>\n\t";
						}
						
						if ($this->browsing_configuration['display_time_left']
							&& ($auction || ($this->browsing_configuration['classified_time_left'] && !$auction)))
						{
							$this->body .=  "<td class=newest_time_left_column_header><div nowrap>".urldecode($this->messages[102538])."</div></td>\n\t";
						}
						
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
							if ($this->browsing_configuration['display_business_type'])
							{
								$this->body .="<td >";
								if ($show_classifieds->BUSINESS_TYPE == 1)
									$this->body .= urldecode($this->messages[1405]);
								elseif ($show_classifieds->BUSINESS_TYPE == 2)
									$this->body .= urldecode($this->messages[1406]);
								else
									$this->body .= "&nbsp;";
								$this->body .= "</td>\n\t";
							}
							if ($this->browsing_configuration['display_photo_icon'])
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
							if ($this->browsing_configuration['display_ad_title'])
							{
								$this->body .="<td >\n\t\t";
								if ($show_classifieds->SOLD_DISPLAYED)
									$this->body .= "<img src=".$this->configuration_data['sold_image']." border=0> ";
								if (($this->configuration_data['popup_while_browsing'])
									&& ($this->configuration_data['popup_while_browsing_width'])
									&& ($this->configuration_data['popup_while_browsing_height']))
									$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds->ID."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
								else
									$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds->ID." class=".$css_class_tag.">";
								$this->body .= stripslashes(urldecode($show_classifieds->TITLE))."</a>\n\t\t";

								if ($show_classifieds->ATTENTION_GETTER)
								{
									$this->body .= "<img src=\"".$show_classifieds->ATTENTION_GETTER_URL."\" border=0 hspace=2>";
								}
								if (($this->browsing_configuration['display_ad_description']) && ($this->configuration_data['display_ad_description_where']))
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
							if (($this->browsing_configuration['display_ad_description']) && (!$this->configuration_data['display_ad_description_where']))
							{
								$this->body .="<td >";
								if (!$this->browsing_configuration['display_all_of_description'])
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
							if ($this->browsing_configuration['display_optional_field_1'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_1))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_1));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_2'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_2))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_2));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_3'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_3))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_3));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_4'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_4))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_4));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_5'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_5))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_5));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_6'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_6))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_6));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_7'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_7))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_7));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_8'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_8))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_8));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_9'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_9))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_9));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_10'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_10))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_10));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_11'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_11))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_11));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_12'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_12))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_12));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_13'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_13))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_13));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_14'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_14))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_14));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_15'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_15))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_15));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_16'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_16))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_16));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_17'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_17))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_17));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_18'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_18))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_18));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_19'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_19))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_19));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_20'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_20))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_20));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}


							if ($this->browsing_configuration['display_browsing_city_field'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->LOCATION_CITY))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_CITY));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->browsing_configuration['display_browsing_state_field'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->LOCATION_STATE))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_STATE));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->browsing_configuration['display_browsing_country_field'])
							{
								$this->body .="<td  >";
									if (strlen(trim(urldecode($show_classifieds->LOCATION_COUNTRY))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_COUNTRY));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if ($this->browsing_configuration['display_browsing_zip_field'])
							{
								$this->body .="<td >";
									if (strlen(trim(urldecode($show_classifieds->LOCATION_ZIP))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_ZIP));
									else
										$this->body .=	"-";
								$this->body .="</td>\n\t";
							}

							if (($show_classifieds->ITEM_TYPE == 1) && $this->browsing_configuration['display_price'])
							{
								$this->body .="<td >";
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
								else
									$this->body .=	"-";
								$this->body .="</td>\n\t";
							}
							elseif (($show_classifieds->ITEM_TYPE == 2) && $this->browsing_configuration['display_price'])
							{
								$this->body .="<td >";
								if (((strlen(trim(urldecode($show_classifieds->MINIMUM_BID))) > 0)
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

							if ($this->browsing_configuration['display_entry_date']
								&& ($show_classifieds->ITEM_TYPE == 1
									|| ($this->browsing_configuration['auction_entry_date'] 
										&& $show_classifieds->ITEM_TYPE == 2)))
								$this->body .="<td >".date(trim($this->configuration_data['entry_date_configuration']),$show_classifieds->DATE)."</td>\n\t";

							if (($show_classifieds->ITEM_TYPE == 2) && $this->browsing_configuration['display_number_bids'])
							{
								$this->body .= "<td ><div nowrap>".$this->get_number_of_bids($db,$show_classifieds->ID)." ".urldecode($this->messages[103042])."</div></td>";
							}
							if($this->browsing_configuration['display_time_left']
								&& (($show_classifieds->ITEM_TYPE == 2) 
									|| ($this->browsing_configuration['classified_time_left'] && ($show_classifieds->ITEM_TYPE == 1))))
							{
									$weeks = $this->DateDifference(w,$this->shifted_time($db),$show_classifieds->ENDS);
									$remaining_weeks = ($weeks * 604800);
									$days = $this->DateDifference(d,($this->shifted_time($db)+$remaining_weeks),$show_classifieds->ENDS);
									$remaining_days = ($days * 86400);
									$hours = $this->DateDifference(h,($this->shifted_time($db)+$remaining_days),$show_classifieds->ENDS);
									$remaining_hours = ($hours * 3600);
									$minutes = $this->DateDifference(m,($this->shifted_time($db)+$remaining_hours),$show_classifieds->ENDS);
									$remaining_minutes = ($minutes * 60);
									$seconds = $this->DateDifference(s,($this->shifted_time($db)+$remaining_minutes),$show_classifieds->ENDS);
								if(($weeks <= 0) && ($days <= 0) && ($hours <= 0) && ($minutes <= 0) && ($seconds <= 0))
								{
									// If closed we want to display closed text
									$this->body .=  "<td ><div nowrap class=auction_closed>";
									$this->body .= urldecode($this->messages[100051])."</div></td>\n\t";
								}
								else
								{
									$this->body .=  "<td ><div nowrap>";
								}
								if ($weeks > 0)
								{
									$this->body .=  $weeks." ".urldecode($this->messages[102539]).", ".$days." ".urldecode($this->messages[102540])."</div></td>\n\t";
								}
								elseif ($days > 0)
								{
									$this->body .=  $days." ".urldecode($this->messages[102540]).", ".$hours." ".urldecode($this->messages[102541])."</div></td>\n\t";
								}
								elseif ($hours > 0)
								{
									$this->body .=  $hours." ".urldecode($this->messages[102541]).", ".$minutes." ".urldecode($this->messages[102542])."</div></td>\n\t";
								}
								elseif ($minutes > 0)
								{
									$this->body .=  $minutes." ".urldecode($this->messages[102542]).", ".$seconds." ".urldecode($this->messages[102543])."</div></td>\n\t";
								}
								elseif ($seconds > 0)
								{
									$this->body .=  $seconds." ".urldecode($this->messages[102543])."</div></td>\n\t";
								}
							}
							
							if ($this->classified_user_id == 1)
							{
								//this is the admin
								$this->body .="<td >\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&e=".$show_classifieds->ID."&b=5>edit</a>\n\t\t</td>\n\t";
								$this->body .="<td >\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=99&b=".$show_classifieds->ID."&c=".$this->site_category.">delete</a>\n\t\t</td>\n\t";

							}
							$this->body .="</tr>\n\t";
							$this->row_count++;
						} //end of while
						$this->body .="</table>\n\t</td>\n</tr>\n";
					}
					else
					{
						//no classifieds in this category
						if($auction)
							$this->body .="<tr class=no_ads_in_category>\n\t<td >\n\t".urldecode($this->messages[100898])."\n\t</td>\n</tr>\n";
						else
							$this->body .="<tr class=no_ads_in_category>\n\t<td >\n\t".urldecode($this->messages[898])."\n\t</td>\n</tr>\n";
						
					}
		return;
	} //end of function display_browse_result

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end of class Browse_newest_ads

?>
