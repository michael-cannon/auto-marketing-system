<? //browse_ads.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Browse_featured_text_ads extends Site {
	var $subcategory_array = array();
	var $notify_data = array();
	
	var $debug_featured_text = 0;

//########################################################################

	function Browse_featured_text_ads($db,$classified_user_id,$language_id,$category_id=0,$page=0,$filter_id=0,$state_filter=0,$zip_filter=0,$zip_filter_distance=0,$product_configuration=0)
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
		$this->affiliate_group_id = $affiliate_group_id;
		$this->affiliate_id = 0;
		$this->state_filter = $state_filter;
		$this->zip_filter = $zip_filter;
		$this->zip_filter_distance = $zip_filter_distance;

	} //end of function Browse_ads

//###########################################################

	function browse($db)
	{
		$this->page_id = 63;
		$this->get_text($db);
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		//browse the auctions in this category that are open
		//$this->body .=$this->configuration_data['display_sub_category_ads']." display sub cats<Br>\n";

		$this->get_ad_configuration($db);
		if ($this->get_category_configuration($db,$this->site_category))
		{
			if ($this->debug_featured_text) echo "just got category settings<bR>\n";
			if (!$this->category_configuration['use_site_default'])
			{
				if ($this->debug_featured_text) echo "not using the site defaults<bR>\n";
				$this->browsing_configuration = $this->configuration_data;
			}
			else
			{
				if ($this->debug_featured_text) echo "using the site defaults<bR>\n";
				$this->browsing_configuration = $this->category_configuration;
			}			
			
		}
		else
		{
			if ($this->debug_featured_text) echo "no category<bR>\n";
			$this->browsing_configuration = $this->configuration_data;
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
			$this->sql_zip_filter_in_statement = "and ".$zip_filter_in_statement." ";
		}

		if ($this->site_category)
		{
			if ($this->configuration_data['display_sub_category_ads'])
			{
				$this->get_sql_in_statement($db,$this->site_category);
			}
			else
			{
				$this->in_statement = " in (".$this->site_category.") ";
			}


			$this->sql_query = "select * from ".$this->classifieds_table." where
				category ".$this->in_statement." and live = 1 and (featured_ad = 1 or featured_ad_2 = 1 or featured_ad_3 = 1 or featured_ad_4 = 1 or featured_ad_5 = 1) ";

			$this->sql_query_count = "select count(id) as total from ".$this->classifieds_table." where
				category ".$this->in_statement." and live = 1 and (featured_ad = 1 or featured_ad_2 = 1 or featured_ad_3 = 1 or featured_ad_4 = 1 or featured_ad_5 = 1) ";

			//$this->body .=$this->sql_query."<br>\n";
		}
		else
		{
			$this->sql_query = "select * from ".$this->classifieds_table." where
				live = 1 and (featured_ad = 1 or featured_ad_2 = 1 or featured_ad_3 = 1 or featured_ad_4 = 1 or featured_ad_5 = 1) ";

			$this->sql_query_count = "select count(id) as total from ".$this->classifieds_table." where
				live = 1 and (featured_ad = 1 or featured_ad_2 = 1 or featured_ad_3 = 1 or featured_ad_4 = 1 or featured_ad_5 = 1) ";
		}
		if ($this->filter_id)
			$this->sql_query .= $this->sql_filter_in_statement;
		if ($this->state_filter)
			$this->sql_query .= $this->sql_state_filter_statement;
		if (($this->zip_filter_distance) && ($this->zip_filter))
			$this->sql_query .= $this->sql_zip_filter_in_statement;
		$orderByStatement .= " order by better_placement desc,date desc limit ".(($this->page_result -1) * $this->configuration_data['featured_ad_page_count']).",".$this->configuration_data['featured_ad_page_count'];

		if ($this->filter_id)
			$this->sql_query_count .= $this->sql_filter_in_statement;
		if ($this->state_filter)
			$this->sql_query_count .= $this->sql_state_filter_statement;
		if (($this->zip_filter_distance) && ($this->zip_filter))
			$this->sql_query_count .= $this->sql_zip_filter_in_statement;
		$this->sql_query_count .= " group by item_type";

		$this->sql_query_classifieds = $this->sql_query." and item_type = 1 ".$orderByStatement;
		$result = $db->Execute($this->sql_query_classifieds);
		$this->sql_query_auctions = $this->sql_query." and item_type = 2 ".$orderByStatement;
		$result_auctions = $db->Execute($this->sql_query_auctions);
		
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
				$this->sql_query = "select * from ".$this->categories_table." where
					parent_id = ".$this->site_category." order by display_order,category_name";
			}
			else
			{
				$current_category_name = urldecode($this->messages[1334]);

				//get the categories inside of this category
				$this->sql_query = "select * from ".$this->categories_table." where
					parent_id = 0 order by display_order,category_name";
			}
			$category_result = $db->Execute($this->sql_query);
			if (!$category_result)
			{
				$this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
				return false;
			}
			else
			{

				if (($current_category_name->FEATURED_TEXT_CACHE_EXPIRE > $this->shifted_time($db)) && ($this->configuration_data['use_category_cache']) && ($current_category_name->FEATURED_TEXT_CACHE_EXPIRE != 0) && ($this->site_category))
				{
					//use the cache
					//echo "using category cache<br>\n";
					$this->body .= $current_category_name->FEATURED_TEXT_CATEGORY_CACHE;
				}
				else
				{
					$this->category_cache = "";
					$this->category_cache .="<tr>\n\t<td valign=top class=back_to_normal_browsing>
						<a href=".$this->configuration_data['classifieds_url']."?a=5&b=".$this->site_category." class=back_to_normal_browsing>";
					$this->category_cache .= urldecode($this->messages[876])."</a></td>\n<tr>\n";

					if (($this->configuration_data['category_tree_display'] == 1) || ($this->configuration_data['category_tree_display'] == 2) || ($this->configuration_data['category_tree_display'] == 0))
					{
						$category_tree = $this->get_category_tree($db,$this->site_category);
						reset ($this->category_tree_array);

						if ($category_tree)
						{
							//category tree
							$this->current_category_tree .="<tr class=main>\n\t<td valign=top height=20 class=browsing_category_tree>\n\t";
							$this->current_category_tree .=urldecode($this->messages[878])." <a href=".$this->configuration_data['classifieds_file_name']."?a=9&c=".$browse_type." class=main>".urldecode($this->messages[879])."</a> > ";
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
										$this->current_category_tree .=$this->category_tree_array[$i]["category_name"];
									else
										$this->current_category_tree .="<a href=".$this->configuration_data['classifieds_file_name']."?a=9&b=".$this->category_tree_array[$i]["category_id"]."&c=".$browse_type." class=browsing_category_tree>".$this->category_tree_array[$i]["category_name"]."</a> > ";
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


					//$this->body .=$this->sql_query." is the query<br>\n";

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
								$this->category_cache .="<tr><td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=9&b=".$show_category->CATEGORY_ID.">";
								if (strlen(trim($show_category->CATEGORY_IMAGE)) > 0)
								{
									$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
								}
								$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
								$this->category_cache .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME;
								if ($this->configuration_data['category_new_ad_limit'])
									$this->check_category_new_ad_icon_use($db,$show_category->CATEGORY_ID,1);
								$this->category_cache .= "</font>";
								if ($this->configuration_data['display_category_description'])
									$this->category_cache .="</a><br><font class=browsing_subcategory_description>".$category_name->DESCRIPTION."</font>";
								$this->category_cache .="</td>";
								if ($category_columns > 1)
								{
									if ($show_category = $category_result->FetchNextObject())
									{
										$this->category_cache .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=9&b=".$show_category->CATEGORY_ID.">";
										if (strlen(trim($show_category->CATEGORY_IMAGE)) > 0)
										{
											$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
										}
										$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
										$this->category_cache .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME;
										if ($this->configuration_data['category_new_ad_limit'])
											$this->check_category_new_ad_icon_use($db,$show_category->CATEGORY_ID,1);
										$this->category_cache .= "</font>";
										if ($this->configuration_data['display_category_description'])
											$this->category_cache .="</a><br><font class=browsing_subcategory_description>".$category_name->DESCRIPTION."</font>";
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
											$this->category_cache .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=9&b=".$show_category->CATEGORY_ID.">";
											if (strlen(trim($show_category->CATEGORY_IMAGE)) > 0)
											{
												$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
											}
											$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
											$this->category_cache .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME;
											if ($this->configuration_data['category_new_ad_limit'])
												$this->check_category_new_ad_icon_use($db,$show_category->CATEGORY_ID,1);
											$this->category_cache .= "</font>";
											if ($this->configuration_data['display_category_description'])
												$this->category_cache .="</a><br><font class=browsing_subcategory_description>".$category_name->DESCRIPTION."</font>";
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
												$this->category_cache .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=9&b=".$show_category->CATEGORY_ID.">";
												if (strlen(trim($show_category->CATEGORY_IMAGE)) > 0)
												{
													$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
												}
												$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
												$this->category_cache .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME;
												if ($this->configuration_data['category_new_ad_limit'])
													$this->check_category_new_ad_icon_use($db,$show_category->CATEGORY_ID,1);
												$this->category_cache .= "</font>";
												if ($this->configuration_data['display_category_description'])
													$this->category_cache .="</a><br><font class=browsing_subcategory_description>".$category_name->DESCRIPTION."</font>";
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
													$this->category_cache .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=9&b=".$show_category->CATEGORY_ID.">";
													if (strlen(trim($show_category->CATEGORY_IMAGE)) > 0)
													{
														$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
													}
													$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
													$this->category_cache .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME;
													if ($this->configuration_data['category_new_ad_limit'])
														$this->check_category_new_ad_icon_use($db,$show_category->CATEGORY_ID,1);
													$this->category_cache .= "</font>";
													if ($this->configuration_data['display_category_description'])
														$this->category_cache .="</a><br><font class=browsing_subcategory_description>".$category_name->DESCRIPTION."</font>";
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
								$this->category_cache .="<tr class=browsing_text_featured_no_subcategories>\n\t<td  height=20>\n\t".urldecode($this->messages[877])." ".$current_category_name->CATEGORY_NAME."\n\t</td>\n</tr>\n";
						}

						if ($this->configuration_data['use_category_cache'])
						{
							$recache_time = $this->shifted_time($db) + (3600 * $this->configuration_data['use_category_cache']);
							$this->sql_query = "update ".$this->categories_languages_table." set
								featured_text_category_cache = \"".addslashes(urlencode($this->category_cache))."\",
								featured_text_cache_expire = \"".$recache_time."\"
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

			//featured ads
			$result->Move(0);
			$this->body .="<tr><td  colspan=".$this->configuration_data['featured_pic_ad_column_count']." class=featured_ad_pic_header>";
			$this->body .=urldecode($this->messages[886])." ";
			if ($this->site_category != 0)
				$this->body .= $current_category_name->CATEGORY_NAME;
			else
				$this->body .= $current_category_name;
			$this->body .= "</td>\n</tr>\n";
			$this->display_browse_result($db,$result,"browsing_result_table_header");

			//featured auctions
			$result->Move(0);
			$this->body .="<tr><td  colspan=".$this->configuration_data['featured_pic_ad_column_count']." class=featured_ad_pic_header>";
			$this->body .=urldecode($this->messages[100886])." ";
			if ($this->site_category != 0)
				$this->body .= $current_category_name->CATEGORY_NAME;
			else
				$this->body .= $current_category_name;
			$this->body .= "</td>\n</tr>\n";
			$this->display_browse_result($db,$result_auctions,"browsing_result_table_header", 1);
			
			
			if ($this->configuration_data['featured_ad_page_count'] < $total_returned)
			{
				//display the link to the next 10
				$number_of_page_results = ceil($total_returned / $this->configuration_data['featured_ad_page_count']);
				$this->body .="<tr class=more_results>\n\t<td >".urldecode($this->messages[880])." ";
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
							$this->body .="<a href=".$this->configuration_data['classifieds_url']."?a=9&b=".$this->site_category."&page=".$i." class=browsing_result_page_links>".$i."</a> ";
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
									$this->body .="<a href=".$this->configuration_data['classifieds_url']."?a=9&b=".$this->site_category."&page=".$page." class=browsing_result_page_links>".$page."</a> ";
							}

						}
						else
						{
							//display the link to the section
							$this->body .="<a href=".$this->configuration_data['classifieds_url']."?a=9&b=".$this->site_category."&page=".(($section*10)+1)." class=browsing_result_page_links>".(($section*10)+1)."</a>";
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

	function display_browse_result($db,$browse_result,$header_css,$auction=0)
	{
					if ($browse_result->RecordCount() > 0)
					{
						$browse_result->Move(0);
						$link_text = "<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category."&c=";
						//display the ads inside of this category
						$this->body .="<tr>\n\t<td  height=20>\n\t";
						$this->body .="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n\t";
						$this->body .="<tr class=".$header_css.">\n\t\t";
						//if ($this->browsing_configuration['display_business_type'])
						//{
						//	$this->body .="<td class=business_type_column_header>".urldecode($this->messages[1262])."</a></td>\n\t";
						//}
						
						if($auction)
							$this->body .="<td  class=title_column_header>".urldecode($this->messages[100881])."</a>";
						else
							$this->body .="<td  class=title_column_header>".urldecode($this->messages[881])."</a>";
						if (($this->browsing_configuration['display_ad_description']) && ($this->browsing_configuration['display_ad_description_where']))
							$this->body .="<br>".urldecode($this->messages[882]);

						$this->body .="</td>\n\t\t";

						if (($this->browsing_configuration['display_ad_description']) && (!$this->browsing_configuration['display_ad_description_where']))
							$this->body .="<td   class=description_column_header>".urldecode($this->messages[882])."</td>\n\t";

						if ($this->browsing_configuration['display_optional_field_1'])
						{
							$this->body .="<td class=optional_field_header_1>".urldecode($this->messages[959])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_2'])
						{
							$this->body .="<td class=optional_field_header_2>".urldecode($this->messages[960])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_3'])
						{
							$this->body .="<td class=optional_field_header_3>".urldecode($this->messages[961])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_4'])
						{
							$this->body .="<td class=optional_field_header_4>".urldecode($this->messages[962])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_5'])
						{
							$this->body .="<td class=optional_field_header_5>".urldecode($this->messages[963])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_6'])
						{
							$this->body .="<td class=optional_field_header_6>".urldecode($this->messages[964])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_7'])
						{
							$this->body .="<td class=optional_field_header_7>".urldecode($this->messages[965])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_8'])
						{
							$this->body .="<td class=optional_field_header_8>".urldecode($this->messages[966])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_9'])
						{
							$this->body .="<td class=optional_field_header_9>".urldecode($this->messages[967])."</a></td>\n\t";
						}
						if ($this->browsing_configuration['display_optional_field_10'])
						{
							$this->body .="<td class=optional_field_header_10>".urldecode($this->messages[968])."</a></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_11'])
						{
							$this->body .="<td class=optional_field_header_11>".urldecode($this->messages[1857])."</font></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_12'])
						{
							$this->body .="<td class=optional_field_header_12>".urldecode($this->messages[1858])."</font></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_13'])
						{
							$this->body .="<td class=optional_field_header_13>".urldecode($this->messages[1859])."</font></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_14'])
						{
							$this->body .="<td class=optional_field_header_14>".urldecode($this->messages[1860])."</font></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_15'])
						{
							$this->body .="<td class=optional_field_header_15>".urldecode($this->messages[1861])."</font></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_16'])
						{
							$this->body .="<td class=optional_field_header_16>".urldecode($this->messages[1862])."</font></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_17'])
						{
							$this->body .="<td class=optional_field_header_17>".urldecode($this->messages[1863])."</font></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_18'])
						{
							$this->body .="<td class=optional_field_header_18>".urldecode($this->messages[1864])."</font></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_19'])
						{
							$this->body .="<td class=optional_field_header_19>".urldecode($this->messages[1865])."</font></td>\n\t";
						}

						if ($this->browsing_configuration['display_optional_field_20'])
						{
							$this->body .="<td class=optional_field_header_20>".urldecode($this->messages[1866])."</font></td>\n\t";
						}

						if ($this->browsing_configuration['display_browsing_city_field'])
						{
							$this->body .="<td class=city_column_header>".urldecode($this->messages[1407])."</a></td>\n\t";
						}

						if ($this->browsing_configuration['display_browsing_state_field'])
						{
							$this->body .="<td class=state_column_header>".urldecode($this->messages[1408])."</td>\n\t";
						}
						if ($this->browsing_configuration['display_browsing_country_field'])
						{
							$this->body .="<td class=country_column_header>".urldecode($this->messages[1409])."</td>\n\t";
						}
						if ($this->browsing_configuration['display_browsing_zip_field'])
						{
							$this->body .="<td class=zip_column_header>".urldecode($this->messages[1410])."</td>\n\t";
						}

						if ($this->browsing_configuration['display_price'])
						{
							$this->body .="<td  class=price_column_header >".urldecode($this->messages[883])."</td>\n\t";
						}

						if ($this->browsing_configuration['display_entry_date']
							&& (!$auction || ($this->browsing_configuration['auction_entry_date'] && $auction)))
							$this->body .="<td  class=entry_date_column_header>".urldecode($this->messages[884])."</td>\n\t";
							
						if ($auction&&$this->browsing_configuration['display_number_bids'])
						{
							$this->body .= "<td class=number_bids_header>".urldecode($this->messages[102529])."</td>\n\t";
						}

						if ($this->browsing_configuration['display_time_left']
							&& ($auction || ($this->browsing_configuration['classified_time_left'] && !$auction)))
						{
							$this->body .= "<td class=time_left_header><div nowrap>".urldecode($this->messages[102530])."</div></td>\n\t";
						}

						if ($this->classified_user_id == 1)
						{
							//this is the admin
							$this->body .="<td>edit</td>\n\t";
							$this->body .="<td >delete</td>\n\t";
						}

						$this->body .="</tr>\n\t";

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

							$this->body .="<td >\n\t\t";
							if ($show_classifieds->SOLD_DISPLAYED)
								$this->body .= "<img src=".$this->configuration_data['sold_image']." border=0> ";
							if (($this->configuration_data['popup_while_browsing'])
								&& ($this->configuration_data['popup_while_browsing_width'])
								&& ($this->configuration_data['popup_while_browsing_height']))
								$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds->ID."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
							else
								$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds->ID." class=".$css_class_tag.">".stripslashes(urldecode($show_classifieds->TITLE))."</a>\n\t\t";
							if ($show_classifieds->ATTENTION_GETTER)
							{
								$this->body .= "<img src=\"".$show_classifieds->ATTENTION_GETTER_URL."\" border=0 hspace=2>";
							}
							if (($this->browsing_configuration['display_ad_description']) && ($this->browsing_configuration['display_ad_description_where']))
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

							if (($this->browsing_configuration['display_ad_description']) && (!$this->browsing_configuration['display_ad_description_where']))
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

							if ($this->browsing_configuration['display_optional_field_1'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_1))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_1));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_2'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_2))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_2));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_3'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_3))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_3));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_4'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_4))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_4));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_5'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_5))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_5));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_6'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_6))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_6));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_7'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_7))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_7));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_8'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_8))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_8));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_9'])
							{
								$this->body .="<td  align=center>";
									if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_9))) > 0)
										$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_9));
									else
										$this->body .=	"-";
								$this->body .="</font></td>\n\t";
							}

							if ($this->browsing_configuration['display_optional_field_10'])
							{
								$this->body .="<td  align=center>";
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


							if (!$auction&&$this->browsing_configuration['display_price'])
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
							elseif ($auction&&$this->browsing_configuration['display_price'])
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

							if ($auction&&$this->browsing_configuration['display_number_bids'])
							{
								$this->body .="<td ><div nowrap>".$this->get_number_of_bids($db,$show_classifieds->ID)." ".urldecode($this->messages[102531])."</div></td>";
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
									$this->body .= urldecode($this->messages[100053])."</div></td>\n\t";
								}
								else
								{
									$this->body .=  "<td ><div nowrap>";
								}
								if ($weeks > 0)
								{
									$this->body .=$weeks." ".urldecode($this->messages[102532]).", ".$days." ".urldecode($this->messages[102533])."</div></td>\n\t";
								}
								elseif ($days > 0)
								{
									$this->body .=$days." ".urldecode($this->messages[102533]).", ".$hours." ".urldecode($this->messages[102534])."</div></td>\n\t";
								}
								elseif ($hours > 0)
								{
									$this->body .=$hours." ".urldecode($this->messages[102534]).", ".$minutes." ".urldecode($this->messages[102535])."</div></td>\n\t";
								}
								elseif ($minutes > 0)
								{
									$this->body .=$minutes." ".urldecode($this->messages[102535]).", ".$seconds." ".urldecode($this->messages[102536])."</div></td>\n\t";
								}
								elseif ($seconds > 0)
								{
									$this->body .=$seconds." ".urldecode($this->messages[102536])."</div></td>\n\t";
								}
							}
								
							if ($this->classified_user_id == 1)
							{
								//this is the admin
								$this->body .="<td >\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&e=".$show_classifieds->ID."&b=9>edit</a>\n\t\t</td>\n\t";
								$this->body .="<td >\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=99&b=".$show_classifieds->ID."&c=".$category.">delete</a>\n\t\t</td>\n\t";

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
							$this->body .="<tr class=no_ads_in_category>\n\t<td >\n\t".urldecode($this->messages[100885])."\n\t</td>\n</tr>\n";
						else
							$this->body .="<tr class=no_ads_in_category>\n\t<td >\n\t".urldecode($this->messages[885])."\n\t</td>\n</tr>\n";

					}
		return;
	} //end of function display_browse_result

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end of class Browse_featured_text_ads

?>