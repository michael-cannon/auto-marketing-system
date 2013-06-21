<? //browse_sellers.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Browse_sellers extends Site {
	var $subcategory_array = array();
	var $notify_data = array();
	var $seller_configuration_data;
	var $debug_sellers = 0;

//########################################################################

	function Browse_sellers($db,$classified_user_id,$language_id,$category_id=0,$page=0,$classified_id=0,$product_configuration=0)
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

	} //end of function Browse_ads

//###########################################################

	function browse($db,$browse_type=0)
	{
		$this->browse_type = $browse_type;
		$this->page_id = 113;
		$this->get_text($db);

		$this->get_seller_configuration_data($db);
		if(!$this->seller_configuration_data)
			return false;

		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";

		if ($this->site_category)
		{
			if ($this->seller_configuration_data->DISPLAY_NO_SUBCATEGORY_MESSAGE)
			{
				$this->get_sql_in_statement($db,$this->site_category);
			}
			else
			{
				$this->in_statement = " in (".$this->site_category.") ";
			}
		}

		$this->sql_query = "select distinct(seller) from ".$this->classifieds_table." where
			category ".$this->in_statement." and live = 1 ";
		switch ($browse_type)
		{
			case 0: //normal
 				$this->sql_query .= " order by seller desc ";
				break;
			case 1: //seller asc
				$this->sql_query .= " order by seller asc ";
				break;
			default:
				$this->sql_query .= " order by seller desc ";
				break;
		}

		$this->sql_query .= " limit ".(($this->page_result -1) * $this->seller_configuration_data->MODULE_NUMBER_OF_ADS_TO_DISPLAY).",".$this->seller_configuration_data->MODULE_NUMBER_OF_ADS_TO_DISPLAY;

		//echo $this->sql_query." is the query<br>\n";
		$this->sql_query_count = "select count(distinct(seller)) as total from ".$this->classifieds_table." where
			category ".$this->in_statement." and live = 1";
		if ($this->debug_sellers) echo $this->sql_query_count." at top<br>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			if ($this->debug_sellers) echo $this->sql_query_count."<br>\n";
			$this->error_message = "<font class=error_message>".urldecode($this->messages[10364])."</font>";
			return false;
		}
		else
		{
			if ($this->sql_query_count)
			{
				$total_count_result = $db->Execute($this->sql_query_count);
				//echo $this->sql_query_count." is the query<br>\n";
				if ($total_count_result)
				{
					$show_total = $total_count_result->FetchNextObject();
					$total_returned = $show_total->TOTAL;
					//$this->body .=$total_returned." is the total returned<br>\n";
				}
			}
			//get this categories name
			if ($this->site_category)
			{
				$this->body .="<tr>\n\t<td valign=top class=back_to_normal_browsing>
					<a href=".$this->configuration_data['classifieds_url']."?a=5&b=".$this->site_category." class=back_to_normal_browsing>";
				$this->body .= urldecode($this->messages[1952])."</a></td>\n<tr>\n";
				if ($this->browse_type)
				{
					$this->body .="<tr>\n\t<td valign=top class=back_to_normal_browsing>
						<a href=".$this->configuration_data['classifieds_url']."?a=25&b=".$this->site_category." class=back_to_normal_browsing>";
					$this->body .= urldecode($this->messages[1953])."</a></td>\n<tr>\n";
				}
				$current_category_name = $this->get_category_name($db,$this->site_category);
				if (($current_category_name->SELLER_CACHE_EXPIRE > $this->shifted_time($db)) && ($this->configuration_data['use_category_cache']) && ($current_category_name->SELLER_CACHE_EXPIRE != 0))
				{
					//use the cache
					//echo "using category cache<br>\n";
					$this->body .= $current_category_name->SELLER_CATEGORY_CACHE;
				}
				else
				{
					//get the categories inside of this category
					$this->sql_query = "select * from ".$this->categories_table." where
						parent_id = ".$this->site_category." order by display_order,category_name";
					$category_result = $db->Execute($this->sql_query);
					if ($this->debug_sellers) echo $this->sql_query."<br>\n";
					if (!$category_result)
					{
						if ($this->debug_sellers) echo $this->sql_query."<br>\n";
						$this->error_message = "<font class=error_message>".urldecode($this->messages[10364])."</font>";
						return false;
					}
					else
					{
						if ($category_result->RecordCount() > 0)
						{
							$this->category_cache .="<tr>\n\t<td valign=top height=20>\n\t<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
							switch ($this->seller_configuration_data->MODULE_NUMBER_OF_COLUMNS)
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
								$this->category_cache .="<tr><td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$show_category->CATEGORY_ID."&c=".$browse_type.">";
								if ((strlen(trim($show_category->CATEGORY_IMAGE)) > 0) && ($this->seller_configuration_data->DISPLAY_CATEGORY_IMAGE))
								{
									$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
								}
								$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
								$this->category_cache .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME."</font>";
								if ($this->seller_configuration_data->DISPLAY_CATEGORY_COUNT)
									$this->category_cache .="<font class=browsing_subcategory_count>(".$this->get_seller_category_count($db,$show_category->CATEGORY_ID).")</font>";
								if ($this->seller_configuration_data->DISPLAY_CATEGORY_DESCRIPTION)
									$this->category_cache .="</a><br><font class=browsing_subcategory_description>".$category_name->DESCRIPTION."</font>";
								$this->category_cache .="</td>";
								if ($this->seller_configuration_data->MODULE_NUMBER_OF_COLUMNS > 1)
								{
									if ($show_category = $category_result->FetchNextObject())
									{
										$this->category_cache .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$show_category->CATEGORY_ID."&c=".$browse_type.">";
										if ((strlen(trim($show_category->CATEGORY_IMAGE)) > 0) && ($this->seller_configuration_data->DISPLAY_CATEGORY_IMAGE))
										{
											$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
										}
										$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
										$this->category_cache .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME."</font>";
										if ($this->seller_configuration_data->DISPLAY_CATEGORY_COUNT)
											$this->category_cache .="<font class=browsing_subcategory_count>(".$this->get_seller_category_count($db,$show_category->CATEGORY_ID).")</font>";
										if ($this->seller_configuration_data->DISPLAY_CATEGORY_DESCRIPTION)
											$this->category_cache .="</a><br><font class=browsing_subcategory_description>".$category_name->DESCRIPTION."</font>";
										$this->category_cache .="</td>";
									}
									else
									{
										$this->category_cache .="<td valign=top width=".$column_width.">&nbsp;</td>";
									}
									if ($this->seller_configuration_data->MODULE_NUMBER_OF_COLUMNS > 2)
									{
										if ($show_category = $category_result->FetchNextObject())
										{
											$this->category_cache .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$show_category->CATEGORY_ID."&c=".$browse_type.">";
											if ((strlen(trim($show_category->CATEGORY_IMAGE)) > 0) && ($this->seller_configuration_data->DISPLAY_CATEGORY_IMAGE))
											{
												$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
											}
											$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
											$this->category_cache .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME."</font>";
											if ($this->seller_configuration_data->DISPLAY_CATEGORY_COUNT)
												$this->category_cache .="<font class=browsing_subcategory_count>(".$this->get_seller_category_count($db,$show_category->CATEGORY_ID).")</font>";
											if ($this->seller_configuration_data->DISPLAY_CATEGORY_DESCRIPTION)
												$this->category_cache .="</a><br><font class=browsing_subcategory_description>".$category_name->DESCRIPTION."</font>";
											$this->category_cache .="</td>";
										}
										else
										{
											$this->category_cache .="<td valign=top width=".$column_width.">&nbsp;</td>";
										}
										if ($this->seller_configuration_data->MODULE_NUMBER_OF_COLUMNS > 3)
										{
											if ($show_category = $category_result->FetchNextObject())
											{
												$this->category_cache .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$show_category->CATEGORY_ID."&c=".$browse_type.">";
												if ((strlen(trim($show_category->CATEGORY_IMAGE)) > 0) && ($this->seller_configuration_data->DISPLAY_CATEGORY_IMAGE))
												{
													$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
												}
												$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
												$this->category_cache .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME."</font>";
												if ($this->seller_configuration_data->DISPLAY_CATEGORY_COUNT)
													$this->category_cache .="<font class=browsing_subcategory_count>(".$this->get_seller_category_count($db,$show_category->CATEGORY_ID).")</font>";
												if ($this->seller_configuration_data->DISPLAY_CATEGORY_DESCRIPTION)
													$this->category_cache .="</a><br><font class=browsing_subcategory_description>".$category_name->DESCRIPTION."</font>";
												$this->category_cache .="</td>";
											}
											else
											{
												$this->category_cache .="<td valign=top width=".$column_width.">&nbsp;</td>";
											}
											if ($this->seller_configuration_data->MODULE_NUMBER_OF_COLUMNS > 4)
											{
												if ($show_category = $category_result->FetchNextObject())
												{
													$this->category_cache .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$show_category->CATEGORY_ID."&c=".$browse_type.">";
													if ((strlen(trim($show_category->CATEGORY_IMAGE)) > 0) && ($this->seller_configuration_data->DISPLAY_CATEGORY_IMAGE))
													{
														$this->category_cache .="<img src=\"".$show_category->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
													}
													$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
													$this->category_cache .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME."</font>";
													if ($this->seller_configuration_data->DISPLAY_CATEGORY_COUNT)
														$this->category_cache .="<font class=browsing_subcategory_count>(".$this->get_seller_category_count($db,$show_category->CATEGORY_ID).")</font>";
													if ($this->seller_configuration_data->DISPLAY_CATEGORY_DESCRIPTION)
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
							//if ($this->seller_configuration_data->DISPLAY_NO_SUBCATEGORY_MESSAGE)
								//$this->category_cache .="<tr class=no_subcategories_to>\n\t<td valign=top height=20>\n\t".urldecode($this->messages[1962])." ".$current_category_name->CATEGORY_NAME."\n\t</td>\n</tr>\n";
						}
					}

					$category_tree = $this->get_category_tree($db,$this->site_category);
					reset ($this->category_tree_array);

					if ($category_tree)
					{
						//category tree
						$this->category_cache .="<tr class=main>\n\t<td valign=top height=20 class=browsing_category_tree>\n\t";
						$this->category_cache .=urldecode($this->messages[2452])." <a href=".$this->configuration_data['classifieds_file_name']."?a=25&c=".$browse_type." class=main>".$this->messages[2453]."</a> > ";
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
									$this->category_cache .=$this->category_tree_array[$i]["category_name"];
								else
									$this->category_cache .="<a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$this->category_tree_array[$i]["category_id"]."&c=".$browse_type." class=browsing_category_tree>".$this->category_tree_array[$i]["category_name"]."</a> > ";
							}
						}
						else
						{
							$this->category_cache .=$category_tree;
						}
						$this->category_cache .="\n\t</td>\n</tr>\n";
					}
					if ($this->configuration_data['use_category_cache'])
					{
						$recache_time = $this->shifted_time($db) + (3600 * $this->configuration_data['use_category_cache']);
						$this->sql_query = "update ".$this->categories_languages_table." set
							seller_category_cache = \"".addslashes(urlencode($this->category_cache))."\",
							seller_cache_expire = \"".$recache_time."\"
							where category_id = ".$this->site_category." and language_id = ".$this->language_id;
						if ($this->debug_sellers) echo $this->sql_query."<br>\n";
						$cache_result = $db->Execute($this->sql_query);
						if (!$cache_result)
						{
							if ($this->debug_sellers) echo $this->sql_query."<br>\n";
							$this->error_message = "<font class=error_message>".urldecode($this->messages[10364])."</font>";
							return false;
						}
					}
					$this->body .= urldecode(stripslashes($this->category_cache));
				}
				if ($this->debug_sellers) 
				{
					echo $total_returned." is total_returned<br>\n";
					echo $this->seller_configuration_data->MODULE_NUMBER_OF_ADS_TO_DISPLAY." is MODULE_NUMBER_OF_ADS_TO_DISPLAY<Br>\n";
				}
				if ($this->seller_configuration_data->MODULE_NUMBER_OF_ADS_TO_DISPLAY < $total_returned)
				{
					//display the link to the next 10
					$number_of_page_results = ceil($total_returned / $this->seller_configuration_data->MODULE_NUMBER_OF_ADS_TO_DISPLAY);
					$this->body .="<tr class=browsing_result_page_links>\n\t<td valign=top><font class=more_results>".urldecode($this->messages[2454])." ".$this->page_result."</font><font class=page_of>".urldecode($this->messages[10368]).ceil($total_returned / $this->seller_configuration_data->MODULE_NUMBER_OF_ADS_TO_DISPLAY)."</font></td>\n</tr>\n";
				}

				$result->Move(0);
				$this->display_browse_result($db,$result,"browsing_result_table_header");
				if ($this->seller_configuration_data->MODULE_NUMBER_OF_ADS_TO_DISPLAY < $total_returned)
				{
					//display the link to the next 10
					$number_of_page_results = ceil($total_returned / $this->seller_configuration_data->MODULE_NUMBER_OF_ADS_TO_DISPLAY);
					$this->body .="<tr class=more_results>\n\t<td valign=top>".urldecode($this->messages[2454])." ";
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
								$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$this->site_category."&page=".$i."&c=".$browse_type." class=browsing_result_page_links>".$i."</a> ";
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
										$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$this->site_category."&page=".$page."&c=".$browse_type." class=browsing_result_page_links>".$page."</a> ";
								}

							}
							else
							{
								//display the link to the section
								$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$this->site_category."&page=".(($section*10)+1)."&c=".$browse_type." class=browsing_result_page_links>".(($section*10)+1)."</a>";
							}
							if (($section+1) < $number_of_sections)
								$this->body .= "<font class=browsing_result_page_links>..</font>";
						}
					}
					$this->body .="</td>\n</tr>\n";
				}
			}
			else
			{
				if (!$this->browse_main($db))
				{
					$this->error_message = "<font class=error_message>".urldecode($this->messages[10364])."</font>";
					return false;
				}
				else
				{
					return true;
				}
			}
		}
		$this->body .="</table>\n";
		$this->display_page($db);
		return true;
	} //end of function browse

//####################################################################################

	function display_browse_result($db,$browse_result)
	{
					if ($browse_result->RecordCount() > 0)
					{
						$browse_result->Move(0);
						$link_text = "<a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$this->site_category."&c=";

						//display the ads inside of this category
						$this->body .="<tr>\n\t<td  height=20>\n\t";
						$this->body .="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n\t";
						$this->body .="<tr class=column_headers>\n\t\t";
						if ($this->seller_configuration_data->MODULE_DISPLAY_BUSINESS_TYPE)
						{
							$this->body .="<td>".$link_text;
							if ($this->browse_type == 43) $this->body .= "44";
							elseif ($this->browse_type == 44) $this->body .= "0";
							else $this->body .= "43";
							$this->body .= " class=column_headers>".urldecode($this->messages[1954])."</a></td>\n\t";
						}
						$this->body .="<td>".$this->messages[1963]."</td>\n\t";
						if ($this->seller_configuration_data->MODULE_DISPLAY_NAME)
							$this->body .="<td>".$this->messages[1955]."</td>\n\t";
						if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_1)
							$this->body .="<td >".$this->messages[1965]."</td>\n\t";
						if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_2)
							$this->body .="<td >".$this->messages[1966]."</td>\n\t";

						if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_3)
							$this->body .="<td >".$this->messages[1967]."</td>\n\t";

						if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_4)
							$this->body .="<td >".$this->messages[1968]."</td>\n\t";

						if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_5)
							$this->body .="<td >".$this->messages[1969]."</td>\n\t";

						if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_6)
							$this->body .="<td >".$this->messages[1970]."</td>\n\t";

						if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_7)
							$this->body .="<td >".$this->messages[1971]."</td>\n\t";

						if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_8)
							$this->body .="<td >".$this->messages[1972]."</td>\n\t";

						if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_9)
							$this->body .="<td >".$this->messages[1973]."</td>\n\t";

						if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_10)
							$this->body .="<td >".$this->messages[1974]."</td>\n\t";
						if ($this->seller_configuration_data->MODULE_DISPLAY_ADDRESS)
							$this->body .="<td >".$this->messages[1964]."</td>\n\t";
						if ($this->seller_configuration_data->MODULE_DISPLAY_CITY)
							$this->body .="<td>".$this->messages[1956]."</td>\n\t";
						if ($this->seller_configuration_data->MODULE_DISPLAY_STATE)
							$this->body .="<td>".$this->messages[1957]."</td>\n\t";
						if ($this->seller_configuration_data->MODULE_DISPLAY_COUNTRY)
							$this->body .="<td>".$this->messages[1958]."</td>\n\t";
						if ($this->seller_configuration_data->MODULE_DISPLAY_ZIP)
							$this->body .="<td>".$this->messages[1959]."</td>\n\t";
						if ($this->seller_configuration_data->MODULE_DISPLAY_PHONE)
							$this->body .="<td >".$this->messages[1975]."</td>\n\t";
						if ($this->seller_configuration_data->MODULE_DISPLAY_PHONE2)
							$this->body .="<td >".$this->messages[1976]."</td>\n\t";

						$this->body .="</tr>\n\t";

						$this->row_count = 0;
						while ($show_classifieds = $browse_result->FetchNextObject())
						{
							if (($this->row_count % 2) == 0)
								$css_class_tag=  "browsing_result_table_body_even ";
							else
								$css_class_tag=  "browsing_result_table_body_odd ";
							$this->body .="<tr class=".$css_class_tag.">\n\t\t";
							$seller_data = $this->get_user_data($db,$show_classifieds->SELLER);
							if ($this->seller_configuration_data->MODULE_DISPLAY_BUSINESS_TYPE)
							{
								$this->body .="<td >";
								if ($seller_data->BUSINESS_TYPE == 1)
									$this->body .= urldecode($this->messages[10010]);
								elseif ($seller_data->BUSINESS_TYPE == 2)
									$this->body .= urldecode($this->messages[10009]);
								else
									$this->body .= "&nbsp;";
								$this->body .= "</td>\n\t";
							}
							//display username
							$this->body .="<td ><a href=".$this->configuration_data['classifieds_file_name']."?a=6&b=".$show_classifieds->SELLER.">".$seller_data->USERNAME."</a></td>\n\t";

							//display name
							if ($this->seller_configuration_data->MODULE_DISPLAY_NAME)
								$this->body .="<td >".$seller_data->FIRSTNAME." ".$seller_data->LASTNAME."</td>\n\t";

							if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_1)
								$this->body .="<td >".$seller_data->OPTIONAL_FIELD_1."</td>\n\t";

							if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_2)
								$this->body .="<td >".$seller_data->OPTIONAL_FIELD_2."</td>\n\t";

							if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_3)
								$this->body .="<td >".$seller_data->OPTIONAL_FIELD_3."</td>\n\t";

							if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_4)
								$this->body .="<td >".$seller_data->OPTIONAL_FIELD_4."</td>\n\t";

							if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_5)
								$this->body .="<td >".$seller_data->OPTIONAL_FIELD_5."</td>\n\t";

							if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_6)
								$this->body .="<td >".$seller_data->OPTIONAL_FIELD_6."</td>\n\t";

							if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_7)
								$this->body .="<td >".$seller_data->OPTIONAL_FIELD_7."</td>\n\t";

							if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_8)
								$this->body .="<td >".$seller_data->OPTIONAL_FIELD_8."</td>\n\t";

							if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_9)
								$this->body .="<td >".$seller_data->OPTIONAL_FIELD_9."</td>\n\t";

							if ($this->seller_configuration_data->MODULE_DISPLAY_OPTIONAL_FIELD_10)
								$this->body .="<td >".$seller_data->OPTIONAL_FIELD_10."</td>\n\t";

							//display address
							if ($this->seller_configuration_data->MODULE_DISPLAY_ADDRESS)
								$this->body .="<td >".$seller_data->ADDRESS." ".$seller_data->ADDRESS_2."</td>\n\t";

							//display city
							if ($this->seller_configuration_data->MODULE_DISPLAY_CITY)
								$this->body .="<td >".$seller_data->CITY."</td>\n\t";

							//display state
							if ($this->seller_configuration_data->MODULE_DISPLAY_STATE)
								$this->body .="<td >".$seller_data->STATE."</td>\n\t";

							//display country
							if ($this->seller_configuration_data->MODULE_DISPLAY_COUNTRY)
								$this->body .="<td >".$seller_data->COUNTRY."</td>\n\t";

							//display zip
							if ($this->seller_configuration_data->MODULE_DISPLAY_ZIP)
								$this->body .="<td >".$seller_data->ZIP."</td>\n\t";

							//display phone
							if ($this->seller_configuration_data->MODULE_DISPLAY_PHONE)
								$this->body .="<td >".$seller_data->PHONE."</td>\n\t";

							//display phone2
							if ($this->seller_configuration_data->MODULE_DISPLAY_PHONE2)
								$this->body .="<td >".$seller_data->PHONE2."</td>\n\t";

							$this->body .="</tr>\n\t";
							$this->row_count++;
						} //end of while
						$this->body .="</table>\n\t</td>\n</tr>\n";
					}
					else
					{
						//no classifieds in this category
						$this->body .="<tr class=no_sellers_in_category>\n\t<td >\n\t".urldecode($this->messages[1962])."\n\t</td>\n</tr>\n";

					}
		return;
	} //end of function display_browse_result

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function DateDifference ($interval, $date1,$date2)
	{
		$difference =  $date2 - $date1;
		switch ($interval)
		{
			case "w":
				$returnvalue  =$difference/604800;
				break;
			case "d":
				$returnvalue  = $difference/86400;
				break;
			case "h":
				$returnvalue = $difference/3600;
				break;
			case "m":
				$returnvalue  = $difference/60;
				break;
			case "s":
				$returnvalue  = $difference;
				break;
	    	}
	    	return $returnvalue;
	} //end of function DateDifference

//####################################################################################

	function browse_error ()
	{
		//this->error_message is the class variable that will contain the error message
		$this->body .="<table cellpadding=5 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr>\n\t<td>".urldecode($this->messages[10364])."</td>\n</tr>\n";
				if ($this->error_message)
			$this->body .="<tr>\n\t<td>".$this->error_message."</td>\n</tr>\n";
		$this->body .="</table>\n";
	 } //end of function browse_error

//####################################################################################

	function main($db)
	{
		$this->page_id = 113;
		$this->get_text($db);
		$this->body .="<table cellpadding=5 cellspacing=1 border=0 width=100%>\n";
		if (strlen(trim($this->messages[10369])) > 0)
			$this->body .="<tr class=browse_sellers_main_page_title>\n\t<td valign=top height=20>".urldecode($this->messages[10369])."</td>\n</tr>\n";
		if (strlen(trim($this->messages[10370])) > 0)
			$this->body .="<tr class=browse_sellers_main_page_message>\n\t<td valign=top height=20>".urldecode($this->messages[10370])."</td>\n</tr>\n";
		$this->body .="<tr>\n\t<td valign=top>\n\t";
		if (!$this->browse_main($db))
			$this->browse_error();
		$this->body .="</td>\n</tr>\n";
		$this->body .="</table>\n";
		$this->display_page($db);
		return true;
	 } //end of function main

//####################################################################################

	function browse_main($db)
	{
		if(!$this->seller_configuration_data)
			if(!$this->get_seller_configuration_data($db))
				return false;

		$this->sql_query = "select * from ".$this->categories_table." where parent_id = 0 order by display_order,category_name";
		$result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$result)
		{
			//$this->body .=$this->sql_query." is the query<br>\n";
			$this->error_message = "<font class=error_message>".urldecode($this->messages[10364])."</font>";
			return false;
		}
		elseif  ($result->RecordCount() > 0)
		{
			$this->body .="<table cellpadding=3 cellspacing=1 border=0 width=100% valign=top>\n\t";
			$this->body .="<tr>\n\t<td valign=top class=back_to_normal_browsing>
				<a href=".$this->configuration_data['classifieds_url']."?a=5&b=".$this->site_category." class=back_to_normal_browsing>";
			$this->body .= urldecode($this->messages[10001])."</a></td>\n<tr>\n";

			switch ($this->seller_configuration_data->MODULE_NUMBER_OF_COLUMNS)
			{
				case 1: $column_width = "100%"; break;
				case 2: $column_width = "50%"; break;
				case 3: $column_width = "33%"; break;
				case 4: $column_width = "25%"; break;
				case 5: $column_width = "20%"; break;
			} //end of switch
			while ($show = $result->FetchNextObject())
			{
				$this->body .="<tr>\n\t\t<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$show->CATEGORY_ID.">";
				if ((strlen(trim($show->CATEGORY_IMAGE)) > 0) && ($this->seller_configuration_data->DISPLAY_CATEGORY_IMAGE))
				{
					$this->body .="<img src=\"".$show->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
				}
				$category_name = $this->get_category_name($db,$show->CATEGORY_ID);
				$this->body .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME."</font>";
				if ($this->seller_configuration_data->DISPLAY_CATEGORY_COUNT)
					$this->body .="<font class=browsing_subcategory_count >(".$this->get_seller_category_count($db,$show->CATEGORY_ID).")</font>";
				if ($this->seller_configuration_data->DISPLAY_CATEGORY_DESCRIPTION)
					$this->body .="</a><br><font class=browsing_subcategory_description>".$category_name->DESCRIPTION."</font>";
				$this->body .="</td>\n\t";
				if ($this->seller_configuration_data->MODULE_NUMBER_OF_COLUMNS > 1)
				{
					if ($show = $result->FetchNextObject())
					{
						$category_name = $this->get_category_name($db,$show->CATEGORY_ID);
						$this->body .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$show->CATEGORY_ID.">";
						if ((strlen(trim($show->CATEGORY_IMAGE)) > 0) && ($this->seller_configuration_data->DISPLAY_CATEGORY_IMAGE))
						{
							$this->body .="<img src=\"".$show->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
						}
						$this->body .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME."</font>";
						if ($this->seller_configuration_data->DISPLAY_CATEGORY_COUNT)
							$this->body .="<font class=browsing_subcategory_count >(".$this->get_seller_category_count($db,$show->CATEGORY_ID).")</font>";
						if ($this->seller_configuration_data->DISPLAY_CATEGORY_DESCRIPTION)
							$this->body .="</a><br><font class=browsing_subcategory_description>".$category_name->DESCRIPTION."</font>";
						$this->body .="</td>\n\t";
					}
					else
					{
						$this->body .="<td valign=top width=".$column_width.">&nbsp;</td>\n\t";
					}
					if ($this->seller_configuration_data->MODULE_NUMBER_OF_COLUMNS > 2)
					{
						if ($show = $result->FetchNextObject())
						{
							$category_name = $this->get_category_name($db,$show->CATEGORY_ID);
							$this->body .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$show->CATEGORY_ID.">";
							if ((strlen(trim($show->CATEGORY_IMAGE)) > 0) && ($this->seller_configuration_data->DISPLAY_CATEGORY_IMAGE))
							{
								$this->body .="<img src=\"".$show->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
							}
							$this->body .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME."</font>";
							if ($this->seller_configuration_data->DISPLAY_CATEGORY_COUNT)
								$this->body .="<font class=browsing_subcategory_count >(".$this->get_seller_category_count($db,$show->CATEGORY_ID).")</font>";
							if ($this->seller_configuration_data->DISPLAY_CATEGORY_DESCRIPTION)
								$this->body .="</a><br><font class=browsing_subcategory_description>".$category_name->DESCRIPTION."</font>";
							$this->body .="</td>\n\t";
						}
						else
						{
							$this->body .="<td valign=top width=".$column_width.">&nbsp;</td>\n\t";
						}
						if ($this->seller_configuration_data->MODULE_NUMBER_OF_COLUMNS > 3)
						{
							if ($show = $result->FetchNextObject())
							{
								$category_name = $this->get_category_name($db,$show->CATEGORY_ID);
								$this->body .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$show->CATEGORY_ID.">";
								if ((strlen(trim($show->CATEGORY_IMAGE)) > 0) && ($this->seller_configuration_data->DISPLAY_CATEGORY_IMAGE))
								{
									$this->body .="<img src=\"".$show->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
								}
								$this->body .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME."</font>";
								if ($this->seller_configuration_data->DISPLAY_CATEGORY_COUNT)
									$this->body .="<font class=browsing_subcategory_count >(".$this->get_seller_category_count($db,$show->CATEGORY_ID).")</font>";
								if ($this->seller_configuration_data->DISPLAY_CATEGORY_DESCRIPTION)
									$this->body .="</a><br><font class=browsing_subcategory_description>".$category_name->DESCRIPTION."</font>";
								$this->body .="</td>\n\t";
							}
							else
							{
								$this->body .="<td valign=top width=".$column_width.">&nbsp;</td>\n\t";
							}
							if ($this->seller_configuration_data->MODULE_NUMBER_OF_COLUMNS > 4)
							{
								if ($show = $result->FetchNextObject())
								{
									$category_name = $this->get_category_name($db,$show->CATEGORY_ID);
									$this->body .="<td valign=top width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=25&b=".$show->CATEGORY_ID.">";
									if ((strlen(trim($show->CATEGORY_IMAGE)) > 0) && ($this->seller_configuration_data->DISPLAY_CATEGORY_IMAGE))
									{
										$this->body .="<img src=\"".$show->CATEGORY_IMAGE."\" hspace=2 vspace=0 border=0 align=left>";
									}
									$this->body .="<font class=browsing_subcategory_name>".$category_name->CATEGORY_NAME."</font>";
									if ($this->seller_configuration_data->DISPLAY_CATEGORY_COUNT)
										$this->body .="<font class=browsing_subcategory_count >(".$this->get_seller_category_count($db,$show->CATEGORY_ID).")</font>";
									if ($this->seller_configuration_data->DISPLAY_CATEGORY_DESCRIPTION)
										$this->body .="</a><br><font class=browsing_subcategory_description>".$category_name->DESCRIPTION."</font>";
									$this->body .="</td>\n\t";
								}
								else
								{
									$this->body .="<td valign=top width=".$column_width.">&nbsp;</td>\n\t";
								}
							}
						}
					}
				}

				$this->body .="</tr>\n\t";
			}
			$this->body .="</table>\n";
			return true;
		}
		else
		{
			$this->body .="<table cellpadding=5 cellspacing=1 border=0 valign=top>\n\t";
			$this->body .="<tr class=no_categories_yet>\n\t<td valign=top>".urldecode($this->messages[10371])."</td>\n</tr>\n";
			$this->body .="</table>\n";
			return true;
		}


	 } //end of function main

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_seller_category_count($db,$category_id=0)
	{
		if ($category_id)
		{
			$in_statement = $this->get_sql_in_statement($db,$category_id);
			$this->sql_query = "select count(distinct(seller)) as seller_count from ".$this->classifieds_table."
				where category ".$in_statement." and live =1";
			$count_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<BR>\n";
			if (!$count_result)
			{
				return false;
			}
			elseif ($count_result->RecordCount() == 1)
			{
				$show = $count_result->FetchNextObject();
				return $show->SELLER_COUNT;
			}
			else
				return false;
		}
		else
			return false;
	} //end of function get_seller_category_count

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_seller_configuration_data($db)
	{
		$this->sql_query = "select * from ".$this->pages_table." where page_id = 113";
		//echo $this->sql_query." at top<br>\n";
		$page_result = $db->Execute($this->sql_query);
		if (!$page_result)
		{
			$this->error_message = "<font class=error_message>".urldecode($this->messages[10364])."</font>";
			return false;
		}
		elseif ($page_result->RecordCount() == 1)
		{
			$this->seller_configuration_data = $page_result->FetchNextObject();
		}
		else
			return false;

		return true;
	}
} // end of class Browse_ads

?>