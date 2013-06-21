<? //browse_ads.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Browse_featured_pic_ads extends Site {
	var $subcategory_array = array();
	var $notify_data = array();

//########################################################################

	function Browse_featured_pic_ads($db,$classified_user_id,$language_id,$category_id=0,$page=0,$filter_id=0,$state_filter=0,$zip_filter=0,$zip_filter_distance=0,$product_configuration=0)
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
		$this->affiliate_group_id = $affiliate_group_id;
		$this->affiliate_id = 0;
		$this->state_filter = $state_filter;
		$this->zip_filter = $zip_filter;
		$this->zip_filter_distance = $zip_filter_distance;

	} //end of function Browse_ads

//###########################################################

	function browse($db)
	{
		$this->page_id = 62;
		$this->get_text($db);
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		//browse the auctions in this category that are open
		//$this->body .=$this->configuration_data['display_sub_category_ads']." display sub cats<Br>\n";

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
			if($zip_filter_in_statement)
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
		}
		
		//$this->sql_query = "select * from ".$this->classifieds_table." where
		//	category ".$this->in_statement." and live = 1 and featured_ad = 1 and image > 0 ";
		$this->sql_query = "select * from ".$this->classifieds_table.",".$this->images_urls_table." where
			".$this->classifieds_table.".id = ".$this->images_urls_table.".classified_id and ".$this->images_urls_table.".display_order = 1 and
			(featured_ad = 1 or featured_ad_2 = 1 or featured_ad_3 = 1 or featured_ad_4 = 1 or featured_ad_5 = 1) and image > 0 and live = 1 ".$this->sql_zip_filter_in_statement.$this->sql_state_filter_statement.$sql_filter_in_statement;
		if ($this->site_category)
			$this->sql_query .= " and category ".$this->in_statement;
		
		$orderByStatement = " order by ".$this->classifieds_table.".better_placement desc,date desc limit ".(($this->page_result -1) * $this->configuration_data['featured_ad_page_count']).",".$this->configuration_data['featured_ad_page_count'];
		
		$this->sql_query_count = "select count(id) as total from ".$this->classifieds_table." where
			live = 1 and (featured_ad = 1 or featured_ad_2 = 1 or featured_ad_3 = 1 or featured_ad_4 = 1 or featured_ad_5 = 1) and image > 0 ";
		if ($this->site_category)
			$this->sql_query_count .= " and category ".$this->in_statement;
		if ($this->filter_id)
			$this->sql_query_count .= $this->sql_filter_in_statement;
		if ($this->state_filter)
			$this->sql_query_count .= $this->sql_state_filter_statement;
		if (($this->zip_filter_distance) && ($this->zip_filter))
			$this->sql_query_count .= $this->sql_zip_filter_in_statement;
		$this->sql_query_count .= " group by item_type";
		//echo $this->sql_query."<br>\n";
		
		$this->sql_query_classifieds = $this->sql_query." and item_type = 1 ".$orderByStatement;
		$result = $db->Execute($this->sql_query_classifieds);
		$this->sql_query_auctions = $this->sql_query." and item_type = 2 ".$orderByStatement;
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
				$this->sql_query = "select * from ".$this->categories_table." where
					parent_id = ".$this->site_category." order by display_order,category_name";
			}
			else
			{
				$current_category_name = urldecode($this->messages[870]);

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
				if (($current_category_name->FEATURED_PIC_CACHE_EXPIRE > $this->shifted_time($db)) && ($this->configuration_data['use_category_cache']) && ($current_category_name->FEATURED_PIC_CACHE_EXPIRE != 0) && ($this->site_category))
				{
					//use the cache
					//echo "using category cache<br>\n";
					$this->body .= $current_category_name->FEATURED_PIC_CATEGORY_CACHE;
				}
				else
				{
					$this->category_cache = "";
					$this->category_cache .="<tr>\n\t<td valign=top class=back_to_normal_browsing>
						<a href=".$this->configuration_data['classifieds_url']."?a=5&b=".$this->site_category." class=back_to_normal_browsing>";
					$this->category_cache .= urldecode($this->messages[875])."</a></td>\n<tr>\n";

					if (($this->configuration_data['category_tree_display'] == 1) || ($this->configuration_data['category_tree_display'] == 2) || ($this->configuration_data['category_tree_display'] == 0))
					{
						$category_tree = $this->get_category_tree($db,$this->site_category);
						reset ($this->category_tree_array);
						if ($category_tree)
						{
							//category tree
							$this->current_category_tree .="<tr class=main_in_category_tree>\n\t<td valign=top height=20 class=browsing_category_tree>\n\t";
							$this->current_category_tree .=urldecode($this->messages[1367])." <a class=main_in_category_tree href=\"".$this->configuration_data['classifieds_file_name']."?a=5&c=".$browse_type."\" >".urldecode($this->messages[1368])."</a> > ";
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
										$this->current_category_tree .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->category_tree_array[$i]["category_id"]."&c=".$browse_type." class=browsing_category_tree>".$this->category_tree_array[$i]["category_name"]."</a> > ";
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
						$this->category_cache.= $this->current_category_tree;

					if ($this->configuration_data['display_category_navigation'])
					{
						if ($category_result->RecordCount() > 0)
						{
							if ($this->site_category)
								$category_columns = $this->configuration_data['number_of_browsing_subcategory_columns'];
							else
								$category_columns = $this->configuration_data['number_of_browsing_columns'];
							$this->category_cache .="<tr>\n\t<td valign=top height=20>\n\t<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
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
								$this->category_cache .="<tr><td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=8&b=".$show_category->CATEGORY_ID.">";
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
										$this->category_cache .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=8&b=".$show_category->CATEGORY_ID.">";
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
											$this->category_cache .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=8&b=".$show_category->CATEGORY_ID.">";
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
												$this->category_cache .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=8&b=".$show_category->CATEGORY_ID.">";
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
													$this->category_cache .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_url']."?a=8&b=".$show_category->CATEGORY_ID.">";
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
								$this->category_cache .="<tr class=no_subcategories_to>\n\t<td valign=top height=20>\n\t".urldecode($this->messages[869])." ".$category_name."\n\t</td>\n</tr>\n";
						}

						if ($this->configuration_data['use_category_cache'])
						{
							$recache_time = $this->shifted_time($db) + (3600 * $this->configuration_data['use_category_cache']);
							$this->sql_query = "update ".$this->categories_languages_table." set
								featured_pic_category_cache = \"".addslashes(urlencode($this->category_cache))."\",
								featured_pic_cache_expire = \"".$recache_time."\"
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
			$this->display_results($db,$result,0,$current_category_name);		
			
			//featured auctions
			$this->display_results($db,$result_auctions,1,$current_category_name);

			if ($this->configuration_data['featured_ad_page_count'] < $total_returned)
			{
				//display the link to the next 10
				$number_of_page_results = ceil($total_returned / $this->configuration_data['featured_ad_page_count']);
				$this->body .="<tr class=more_results>\n\t<td valign=top>".urldecode($this->messages[871])." ";
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
							$this->body .="<a href=".$this->configuration_data['classifieds_url']."?a=8&b=".$this->site_category."&page=".$i." class=browsing_result_page_links>".$i."</a> ";
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
									$this->body .="<a href=".$this->configuration_data['classifieds_url']."?a=8&b=".$this->site_category."&page=".$page." class=browsing_result_page_links>".$page."</a> ";
							}

						}
						else
						{
							//display the link to the section
							$this->body .="<a href=".$this->configuration_data['classifieds_url']."?a=8&b=".$this->site_category."&page=".(($section*10)+1)." class=browsing_result_page_links>".(($section*10)+1)."</a>";
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

	function display_results($db, $browse_results, $auction=0,$current_category_name=0)
	{
		if ($browse_results->RecordCount() > 0)
		{
			$this->body .="<tr>\n\t<td valign=top height=20>\n\t<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
			$this->body .="<tr><td valign=top colspan=".$this->configuration_data['featured_pic_ad_column_count']." class=featured_ad_pic_header>";
			if($auction)
				$this->body .=urldecode($this->messages[100874])." ".$current_category_name->CATEGORY_NAME."</td>\n</tr>\n";
			else
				$this->body .=urldecode($this->messages[874])." ".$current_category_name->CATEGORY_NAME."</td>\n</tr>\n";
			switch ($this->configuration_data['featured_pic_ad_column_count'])
			{
				case 1: $column_width = "100%";
				case 2: $column_width = "50%";
				case 3: $column_width = "33%";
				case 4: $column_width = "25%";
				case 5: $column_width = "20%";
			} //end of switch
			while ($show_classifieds = $browse_results->FetchNextObject())
			{
				//display the sub categories of this category
				$this->body .="<tr><td valign=top width=".$column_width." class=thumbnail_td>";
				$this->display_featured_thumbnail($db,$show_classifieds->ID);
				$this->body .= "<BR><font class=featured_ad_title_in_thumb>".urldecode($show_classifieds->TITLE);
				if (((strlen(trim(urldecode($show_classifieds->PRICE))) > 0)
					|| (strlen(trim(urldecode($show_classifieds->PRECURRENCY))) > 0)
					|| (strlen(trim(urldecode($show_classifieds->POSTCURRENCY))) > 0)) && ($show_classifieds->PRICE != 0))
				{
					$this->body .="<br>";
					if (floor($show_classifieds->PRICE) == $show_classifieds->PRICE)
					{
						$this->body .= stripslashes(urldecode($show_classifieds->PRECURRENCY))." ".
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
				$this->body .="</font></td>";
				if ($this->configuration_data['featured_pic_ad_column_count'] > 1)
				{
					if ($show_classifieds = $browse_results->FetchNextObject())
					{
						$this->body .="<td valign=top width=".$column_width." class=thumbnail_td>";
						$this->display_featured_thumbnail($db,$show_classifieds->ID);
						$this->body .= "<BR><font class=featured_ad_title_in_thumb>".urldecode($show_classifieds->TITLE);
						if (((strlen(trim(urldecode($show_classifieds->PRICE))) > 0)
							|| (strlen(trim(urldecode($show_classifieds->PRECURRENCY))) > 0)
							|| (strlen(trim(urldecode($show_classifieds->POSTCURRENCY))) > 0)) && ($show_classifieds->PRICE != 0))
						{
							$this->body .="<br>";
							if (floor($show_classifieds->PRICE) == $show_classifieds->PRICE)
							{
								$this->body .= stripslashes(urldecode($show_classifieds->PRECURRENCY))." ".
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
						$this->body .="</font></td>";
					}
					else
					{
						$this->body .="<td valign=top width=".$column_width.">&nbsp;</td>";
					}
					if ($this->configuration_data['featured_pic_ad_column_count'] > 2)
					{
						if ($show_classifieds = $browse_results->FetchNextObject())
						{
							$this->body .="<td valign=top width=".$column_width." class=thumbnail_td>";
							$this->display_featured_thumbnail($db,$show_classifieds->ID);
							$this->body .= "<BR><font class=featured_ad_title_in_thumb>".urldecode($show_classifieds->TITLE);
							if (((strlen(trim(urldecode($show_classifieds->PRICE))) > 0)
								|| (strlen(trim(urldecode($show_classifieds->PRECURRENCY))) > 0)
								|| (strlen(trim(urldecode($show_classifieds->POSTCURRENCY))) > 0)) && ($show_classifieds->PRICE != 0))
							{
								$this->body .="<br>";
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
							$this->body .="</font></td>";
						}
						else
						{
							$this->body .="<td valign=top width=".$column_width.">&nbsp;</td>";
						}
						if ($this->configuration_data['featured_pic_ad_column_count'] > 3)
						{
							if ($show_classifieds = $browse_results->FetchNextObject())
							{
								$this->body .="<td valign=top width=".$column_width." class=thumbnail_td>";
								$this->display_featured_thumbnail($db,$show_classifieds->ID);
								$this->body .= "<BR><font class=featured_ad_title_in_thumb>".urldecode($show_classifieds->TITLE);
								if (((strlen(trim(urldecode($show_classifieds->PRICE))) > 0)
									|| (strlen(trim(urldecode($show_classifieds->PRECURRENCY))) > 0)
									|| (strlen(trim(urldecode($show_classifieds->POSTCURRENCY))) > 0)) && ($show_classifieds->PRICE != 0))
								{
									$this->body .="<br>";
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
								$this->body .="</font></td>";
							}
							else
							{
								$this->body .="<td valign=top width=".$column_width.">&nbsp;</td>";
							}
							if ($this->configuration_data['featured_pic_ad_column_count'] > 4)
							{
								if ($show_classifieds = $browse_results->FetchNextObject())
								{
									$this->body .="<td valign=top width=".$column_width." class=thumbnail_td>";
									$this->display_featured_thumbnail($db,$show_classifieds->ID);
									$this->body .= "<BR><font class=featured_ad_title_in_thumb>".urldecode($show_classifieds->TITLE);
									if (((strlen(trim(urldecode($show_classifieds->PRICE))) > 0)
										|| (strlen(trim(urldecode($show_classifieds->PRECURRENCY))) > 0)
										|| (strlen(trim(urldecode($show_classifieds->POSTCURRENCY))) > 0)) && ($show_classifieds->PRICE != 0))
									{
										$this->body .="<br>";
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
									$this->body .="</font></td>";
								}
								else
								{
									$this->body .="<td valign=top width=".$column_width.">&nbsp;</td>";
								}
							}
						}
					}
				}
				$this->body .="</tr>";
			}
			$this->body .="</table>\n\t</td>\n</tr>\n";
		}
		else
		{
			if($auction)
				$this->body .="<tr class=no_featured_ads_message>\n\t<td valign=top height=20>\n\t".urldecode($this->messages[100868])."\n\t</td>\n</tr>\n";
			else
				$this->body .="<tr class=no_featured_ads_message>\n\t<td valign=top height=20>\n\t".urldecode($this->messages[868])."\n\t</td>\n</tr>\n";
		}
	}

//####################################################################################

	function display_featured_thumbnail($db,$classified_id)
	{
		if (($this->configuration_data['popup_while_browsing'])
			&& ($this->configuration_data['popup_while_browsing_width'])
			&& ($this->configuration_data['popup_while_browsing_height']))
		{
			$this->body .= "<a href=\"";
			$this->body .= $this->configuration_data['classifieds_file_name'];
			$this->body .= "?a=2&b=".$classified_id."\" ";
			$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
			//$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$classified_id."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
		}
		else
			$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$classified_id.">";
		$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$classified_id." and display_order = 1";
		$image_url_result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$image_url_result)
		{
			$this->error_message = urldecode($this->messages[81]);
			return false;
		}
		elseif ($image_url_result->RecordCount() == 1)
		{
			$show_image_url = $image_url_result->FetchNextObject();
			if (($show_image_url->IMAGE_WIDTH > $this->configuration_data['featured_thumbnail_max_width']) && ($show_image_url->IMAGE_HEIGHT > $this->configuration_data['featured_thumbnail_max_height']))
			{
				$imageprop = ($this->configuration_data['featured_thumbnail_max_width'] * 100) / $show_image_url->IMAGE_WIDTH;
				$imagevsize = ($show_image_url->IMAGE_HEIGHT * $imageprop) / 100 ;
				$final_image_width = $this->configuration_data['featured_thumbnail_max_width'];
				$final_image_height = ceil($imagevsize);
				if ($final_image_height > $this->configuration_data['featured_thumbnail_max_height'])
				{
					$imageprop = ($this->configuration_data['featured_thumbnail_max_height'] * 100) / $show_image_url->IMAGE_HEIGHT;
					$imagehsize = ($show_image_url->IMAGE_WIDTH * $imageprop) / 100 ;
					$final_image_height = $this->configuration_data['featured_thumbnail_max_height'];
					$final_image_width = ceil($imagehsize);
				}
			}
			elseif ($show_image_url->IMAGE_WIDTH > $this->configuration_data['featured_thumbnail_max_width'])
			{
				$imageprop = ($this->configuration_data['featured_thumbnail_max_width'] * 100) / $show_image_url->IMAGE_WIDTH;
				$imagevsize = ($show_image_url->IMAGE_HEIGHT * $imageprop) / 100 ;
				$final_image_width = $this->configuration_data['featured_thumbnail_max_width'];
				$final_image_height = ceil($imagevsize);
			}
			elseif ($show_image_url->IMAGE_HEIGHT > $this->configuration_data['featured_thumbnail_max_height'])
			{
				$imageprop = ($this->configuration_data['featured_thumbnail_max_height'] * 100) / $show_image_url->IMAGE_HEIGHT;
				$imagehsize = ($show_image_url->IMAGE_WIDTH * $imageprop) / 100 ;
				$final_image_height = $this->configuration_data['featured_thumbnail_max_height'];
				$final_image_width = ceil($imagehsize);
			}
			else
			{
				$final_image_width = $show_image_url->IMAGE_WIDTH;
				$final_image_height = $show_image_url->IMAGE_HEIGHT;
			}
			if ($show_image_url->THUMB_URL)
			{
				$this->body .="<img src=\"".$show_image_url->THUMB_URL."\" width=\"".$final_image_width."\" height=\"".$final_image_height."\" border=0>";
			}
			elseif ($show_image_url->IMAGE_URL)
			{
				$this->body .="<img src=\"".$show_image_url->IMAGE_URL."\" width=\"".$final_image_width."\" height=\"".$final_image_height."\" border=0>";
			}
			else
			{
				//display the photo icon
				$this->body .="<img src=".$this->configuration_data['photo_icon_url']." border=0>";
			}
		}
		else
		{
			$this->sql_query = "select image_id,thumb,image_width,image_height from ".$this->images_table." where classified_id = ".$classified_id." and display_order = 1";
			$image_db_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$image_db_result)
			{
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($image_db_result->RecordCount() == 1)
			{
				$show_image_url = $image_db_result->FetchNextObject();
				if ($this->configuration_data['photo_or_icon'] == 1)
				{
					if (($show_image_url->IMAGE_WIDTH > $this->configuration_data['featured_thumbnail_max_width']) && ($show_image_url->IMAGE_HEIGHT > $this->configuration_data['featured_thumbnail_max_height']))
					{
						$imageprop = ($this->configuration_data['featured_thumbnail_max_width'] * 100) / $show_image_url->IMAGE_WIDTH;
						$imagevsize = ($show_image_url->IMAGE_HEIGHT * $imageprop) / 100 ;
						$final_image_width = $this->configuration_data['featured_thumbnail_max_width'];
						$final_image_height = ceil($imagevsize);

						if ($final_image_height > $this->configuration_data['featured_thumbnail_max_height'])
						{
							$imageprop = ($this->configuration_data['featured_thumbnail_max_height'] * 100) / $show_image_url->IMAGE_HEIGHT;
							$imagehsize = ($show_image_url->IMAGE_WIDTH * $imageprop) / 100 ;
							$final_image_height = $this->configuration_data['featured_thumbnail_max_height'];
							$final_image_width = ceil($imagehsize);
						}
					}
					elseif ($show_image_url->IMAGE_WIDTH > $this->configuration_data['featured_thumbnail_max_width'])
					{
						$imageprop = ($this->configuration_data['featured_thumbnail_max_width'] * 100) / $show_image_url->IMAGE_WIDTH;
						$imagevsize = ($show_image_url->IMAGE_HEIGHT * $imageprop) / 100 ;
						$final_image_width = $this->configuration_data['featured_thumbnail_max_width'];
						$final_image_height = ceil($imagevsize);
					}
					elseif ($show_image_url->IMAGE_HEIGHT > $this->configuration_data['featured_thumbnail_max_height'])
					{
						$imageprop = ($this->configuration_data['featured_thumbnail_max_height'] * 100) / $show_image_url->IMAGE_HEIGHT;
						$imagehsize = ($show_image_url->IMAGE_WIDTH * $imageprop) / 100 ;
						$final_image_height = $this->configuration_data['featured_thumbnail_max_height'];
						$final_image_width = ceil($imagehsize);
					}
					else
					{
						$final_image_width = $show_image_url->IMAGE_WIDTH;
						$final_image_height = $show_image_url->IMAGE_HEIGHT;
					}
					//$this->body .=$final_image_width." is the width<BR>\n";
					//$this->body .=$final_image_height." is the height<br>\n";
					//$this->body .=$this->configuration_data['featured_thumbnail_max_width']." is max width<br>\n";
					//$this->body .=$this->configuration_data['featured_thumbnail_max_height']." is max height<br>\n";
					if ($show_image_url->THUMB)
					{
						$this->body .= "<img src=get_image.php?image=".$show_image_url->IMAGE_ID."&size=1 width=".$final_image_width." height=".$final_image_height." border=0>";
					}
					else
					{
						//$this->body .=$final_image_width." is the width<BR>\n";
						//$this->body .=$final_image_height." is the height<br>\n";
						$this->body .= "<img src=get_image.php?image=".$show_image_url->IMAGE_ID." width=".$final_image_width." height=".$final_image_height." border=0>";
					}
				}
				else
				{
					$this->body .="<font class=featured_ad_title_in_thumb>".$this->get_ad_title($db,$classified_id)."</font>";
				}
			}
			else
			{
				$this->body .="<font class=featured_ad_title_in_thumb>".$this->get_ad_title($db,$classified_id)."</font>";
			}
		}
		$this->body .="</a>";
		return true;
	} //end of function display_featured_thumbnail

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end of class Browse_featured_pic_ads

?>