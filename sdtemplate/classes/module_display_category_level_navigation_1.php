<? //module_display_category_level_navigation_1.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$debug_category_navigation = 0;

$this->get_css($db,$show_module['page_id']);
$this->get_text($db,$show_module['page_id']);
$this->body = "";
if (strlen(trim($this->site_category)) == 0)
	$this->site_category = 0;
$current_category_name = $this->category_configuration->CATEGORY_NAME;
if (($show_module['cache_expire'] > $this->shifted_time($db)) && ($show_module['use_category_cache']) && ($show_module['cache_expire'] != 0) && (!$this->filter_id))
{
	//use the cache
	if ($debug_category_navigation) echo "using category cache<br>\n";
	$this->body .= $this->show_module->CATEGORY_CACHE;
}
else
{
	//get the categories inside of this category
	//echo $show_module['module_category_level_to_display'] ." is level to display<br>\n";
	if ($show_module['module_category_level_to_display'] == 0)
	{
		$this->sql_query = "select geodesic_categories.category_id,
			geodesic_categories.category_image,
			geodesic_categories.category_count,
			geodesic_categories.auction_category_count,
			geodesic_classifieds_categories_languages.category_name,
			geodesic_classifieds_categories_languages.description
			from geodesic_categories,geodesic_classifieds_categories_languages where
			parent_id = 0 and
			geodesic_categories.category_id = geodesic_classifieds_categories_languages.category_id and
			geodesic_classifieds_categories_languages.language_id = ".$this->language_id."
			order by geodesic_categories.display_order,geodesic_classifieds_categories_languages.category_name";
	}
	else
	{
		//get all second level categories
		$this->sql_query = "select geodesic_categories.category_id,
			geodesic_categories.category_image,
			geodesic_categories.category_count,
			geodesic_categories.auction_category_count,
			geodesic_classifieds_categories_languages.category_name,
			geodesic_classifieds_categories_languages.description
			from geodesic_categories,geodesic_classifieds_categories_languages where
			parent_id = 0 and
			geodesic_categories.category_id = geodesic_classifieds_categories_languages.category_id and
			geodesic_classifieds_categories_languages.language_id = ".$this->language_id."
			order by geodesic_categories.display_order,geodesic_classifieds_categories_languages.category_name";
		$level_category_result = $db->Execute($this->sql_query);
		if ($debug_category_navigation) echo $this->sql_query."<br>\n";
		if (!$level_category_result)
		{
			if ($debug_category_navigation) echo $this->sql_query."<br>\n";
			return false;
		}
		elseif ($level_category_result->RecordCount() > 0)
		{
			$level_in_statement = " in (";
			$level_started = 0;
			while ($show_level = $level_category_result->FetchRow())
			{
				if ($level_started == 0)
					$level_in_statement  .= $show_level["category_id"];
				else
					$level_in_statement  .= ",".$show_level["category_id"];
				$level_started = 1;
			}
			$level_in_statement  .= ")";
			$this->sql_query = "select geodesic_categories.category_id,
				geodesic_categories.category_image,
				geodesic_categories.category_count,
				geodesic_categories.auction_category_count,
				geodesic_classifieds_categories_languages.category_name,
				geodesic_classifieds_categories_languages.description
				from geodesic_categories,geodesic_classifieds_categories_languages where
				geodesic_categories.parent_id ".$level_in_statement." and
				geodesic_categories.category_id = geodesic_classifieds_categories_languages.category_id and
				geodesic_classifieds_categories_languages.language_id = ".$this->language_id."
				order by geodesic_categories.display_order,geodesic_classifieds_categories_languages.category_name";
		}
		else
			return false;
	}

	$category_result = $db->Execute($this->sql_query);
	if ($debug_category_navigation) echo $this->sql_query."<br>\n";
	if (!$category_result)
	{
		$this->error_message = "<span class=error_message>".urldecode($this->messages[1515])."</span>";
		return false;
	}
	else
	{
		if ($category_result->RecordCount() > 0)
		{
			$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
			switch ($show_module['number_of_browsing_columns'])
			{
				case 1: $column_width = "100%"; break;
				case 2: $column_width = "50%"; break;
				case 3: $column_width = "33%"; break;
				case 4: $column_width = "25%"; break;
				case 5: $column_width = "20%"; break;
			} //end of switch

			while ($show_category = $category_result->FetchRow())
			{
				//display the sub categories of this category
				$this->body .="<tr><td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id']."&c=".$browse_type.">";
				if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
				{
					$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
				}
				//$category_name = $this->get_category_name($db,$show_category['category_id']);
				$this->body .="<span class=navigation_1_name>".urldecode(stripslashes($show_category['category_name']));
				if ($show_module['module_display_new_ad_icon'])
					$this->check_category_new_ad_icon_use($db,$show_category['category_id']);				
				$this->body .= "</span>";
				if ($show_module['display_category_count'])
				{
					$category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
					$category_count['ad_count'] = $show_category['category_count'];
					$category_count['auction_count'] = $show_category['auction_category_count'];						
					$css = array("listing_count" => "class_navigation_1_count", "auction_count" => "class_navigation_1_auction_count", "ad_count" => "class_navigation_1_ad_count");
					$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'], 0, $css,$category_count);
				}
				if ($show_module['display_category_description'])
					$this->body .="</a><br><span class=navigation_1_description>".urldecode(stripslashes($show_category['description']))."</span>";
				$this->body .="</td>";
				if ($show_module['number_of_browsing_columns'] > 1)
				{
					if ($show_category = $category_result->FetchRow())
					{
						$this->body .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id']."&c=".$browse_type.">";
						if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
						{
							$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
						}
						//$category_name = $this->get_category_name($db,$show_category['category_id']);
						$this->body .="<span class=navigation_1_name>".urldecode(stripslashes($show_category['category_name']));
						if ($show_module['module_display_new_ad_icon'])
							$this->check_category_new_ad_icon_use($db,$show_category['category_id']);				
						$this->body .= "</span>";
						if ($show_module['display_category_count'])
						{
							$category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
							$category_count['ad_count'] = $show_category['category_count'];
							$category_count['auction_count'] = $show_category['auction_category_count'];							
							$css = array("listing_count" => "class_navigation_1_count", "auction_count" => "class_navigation_1_auction_count", "ad_count" => "class_navigation_1_ad_count");
							$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'], 0, $css,$category_count);
						}
						if ($show_module['display_category_description'])
							$this->body .="</a><br><span class=navigation_1_description>".urldecode(stripslashes($show_category['description']))."</span>";
						$this->body .="</td>";
					}
					else
					{
						$this->body .="<td  width=".$column_width.">&nbsp;</td>";
					}
					if ($show_module['number_of_browsing_columns'] > 2)
					{
						if ($show_category = $category_result->FetchRow())
						{
							$this->body .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id']."&c=".$browse_type.">";
							if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
							{
								$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
							}
							//$category_name = $this->get_category_name($db,$show_category['category_id']);
							$this->body .="<span class=navigation_1_name>".urldecode(stripslashes($show_category['category_name']));
							if ($show_module['module_display_new_ad_icon'])
								$this->check_category_new_ad_icon_use($db,$show_category['category_id']);				
							$this->body .= "</span>";
							if ($show_module['display_category_count'])
							{
								$category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
								$category_count['ad_count'] = $show_category['category_count'];
								$category_count['auction_count'] = $show_category['auction_category_count'];									
								$css = array("listing_count" => "class_navigation_1_count", "auction_count" => "class_navigation_1_auction_count", "ad_count" => "class_navigation_1_ad_count");
								$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'], 0, $css,$category_count);
							}
							if ($show_module['display_category_description'])
								$this->body .="</a><br><span class=navigation_1_description>".urldecode(stripslashes($show_category['description']))."</span>";
							$this->body .="</td>";
						}
						else
						{
							$this->body .="<td  width=".$column_width.">&nbsp;</td>";
						}
						if ($show_module['number_of_browsing_columns'] > 3)
						{
							if ($show_category = $category_result->FetchRow())
							{
								$this->body .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id']."&c=".$browse_type.">";
								if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
								{
									$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
								}
								//$category_name = $this->get_category_name($db,$show_category['category_id']);
								$this->body .="<span class=navigation_1_name>".urldecode(stripslashes($show_category['category_name']));
								if ($show_module['module_display_new_ad_icon'])
									$this->check_category_new_ad_icon_use($db,$show_category['category_id']);				
								$this->body .= "</span>";
								if ($show_module['display_category_count'])
								{
									$category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
									$category_count['ad_count'] = $show_category['category_count'];
									$category_count['auction_count'] = $show_category['auction_category_count'];										
									$css = array("listing_count" => "class_navigation_1_count", "auction_count" => "class_navigation_1_auction_count", "ad_count" => "class_navigation_1_ad_count");
									$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'], 0, $css,$category_count);
								}
								if ($show_module['display_category_description'])
									$this->body .="</a><br><span class=navigation_1_description>".urldecode(stripslashes($show_category['description']))."</span>";
								$this->body .="</td>";
							}
							else
							{
								$this->body .="<td  width=".$column_width.">&nbsp;</td>";
							}
							if ($show_module['number_of_browsing_columns'] > 4)
							{
								if ($show_category = $category_result->FetchRow())
								{
									$this->body .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id']."&c=".$browse_type.">";
									if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
									{
										$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
									}
									//$category_name = $this->get_category_name($db,$show_category['category_id']);
									$this->body .="<span class=navigation_1_name>".urldecode(stripslashes($show_category['category_name']));
									if ($show_module['module_display_new_ad_icon'])
										$this->check_category_new_ad_icon_use($db,$show_category['category_id']);				
									$this->body .= "</span>";
									if ($show_module['display_category_count'])
									{
										$category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
										$category_count['ad_count'] = $show_category['category_count'];
										$category_count['auction_count'] = $show_category['auction_category_count'];											
										$css = array("listing_count" => "class_navigation_1_count", "auction_count" => "class_navigation_1_auction_count", "ad_count" => "class_navigation_1_ad_count");
										$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'], 0, $css,$category_count);
									}
									if ($show_module['display_category_description'])
										$this->body .="</a><br><span class=navigation_1_description>".urldecode(stripslashes($show_category['description']))."</span>";
									$this->body .="</td>";
								}
								else
								{
									$this->body .="<td  width=".$column_width.">&nbsp;</td>";
								}
							}
						}
					}
				}
				$this->body .="</tr>";
				if (($show_module['module_display_ad_description']) && ($this->site_category))
					$this->body .="<tr class=navigation_3_subcategories_to>\n\t<td>\n\t".urldecode($this->messages[1890])." ".$current_category_name."\n\t</td>\n</tr>\n";

			}
			$this->body .="</table>";
		}
		else
		{
			if (($show_module['module_display_ad_description']) && ($this->site_category))
				$this->body .="<tr class=navigation_3_subcategories_to>\n\t<td>\n\t".urldecode($this->messages[1890])." ".$current_category_name."\n\t</td>\n</tr>\n";
			if ($show_module['display_no_subcategory_message'])
				$this->body .="<tr class=navigation_1_subcategories_to>\n\t<td>\n\t".urldecode($this->messages[1516])." ".$current_category_name."\n\t</td>\n</tr>\n";
		}

		if ($show_module['use_category_cache'])
		{
			$recache_time = $this->shifted_time($db) + (3600 * $show_module['use_category_cache']);
			$this->sql_query = "update geodesic_classifieds_categories_languages set
				category_cache = \"".addslashes($this->body)."\",
				cache_expire = \"".$recache_time."\"
				where category_id = ".$this->site_category." and language_id = ".$this->language_id;
			//echo $this->sql_query."<Br>\n";
			$cache_result = $db->Execute($this->sql_query);
			if (!$cache_result)
			{
				$this->error_message = "<span class=error_message>".urldecode($this->messages[1515])."</span>";
				return false;
			}
		}
	}
}

?>
