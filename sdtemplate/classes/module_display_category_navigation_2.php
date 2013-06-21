<? //module_display_category_navigation_2.php	
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$this->get_css($db,$show_module['page_id']);
$this->get_text($db,$show_module['page_id']);
$this->body = "";

if (strlen(trim($this->site_category)) == 0) 
	$this->site_category = 0;

$current_category_name = $this->category_configuration->CATEGORY_NAME;
if (($show_module['cache_expire'] > $this->shifted_time($db)) && ($show_module['use_category_cache']) && ($show_module['cache_expire'] != 0) && (!$this->filter_id))
{
	//use the cache
	//echo "using category cache<br>\n";
	$this->body .= $this->show_module->CATEGORY_CACHE;
}
else
{
	//get the categories inside of this category
	//$this->sql_query = "select * from geodesic_categories where
	//	parent_id = ".$this->site_category." order by display_order,category_name";
	$this->sql_query = "select geodesic_categories.category_id,
		geodesic_categories.category_image,
		geodesic_categories.category_count,
		geodesic_classifieds_categories_languages.category_name,
		geodesic_classifieds_categories_languages.description
		from geodesic_categories,geodesic_classifieds_categories_languages where
		parent_id = ".$this->site_category." and
		geodesic_categories.category_id = geodesic_classifieds_categories_languages.category_id and
		geodesic_classifieds_categories_languages.language_id = ".$this->language_id." 
		order by geodesic_categories.display_order,geodesic_classifieds_categories_languages.category_name";		
	$category_result = $db->Execute($this->sql_query);
	//echo $this->sql_query." is the query<br>\n";
	if (!$category_result)
	{
		$this->error_message = "<font class=error_message>".urldecode($this->messages[1517])."</font>";
		return false;
	}
	else
	{
		// get parent category name
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
				$link = "<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id'];
				if ($browse_type) $link .= "&c=".$browse_type;
				$link .= ">";

				//display the sub categories of this category
				$this->body .="<tr><td  width=".$column_width.">";
				if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
				{
					$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
				}
				//$category_name = $this->get_category_name($db,$show_category['category_id']);
				$this->body .= str_replace(">", " class=navigation_2_name>", $link).urldecode(stripslashes($show_category['category_name']));
				if ($show_module['module_display_new_ad_icon'])
					$this->check_category_new_ad_icon_use($db,$show_category['category_id']);				
				$this->body .= "</a>";
				if ($show_module['display_category_count']) 
				{
					$css = array("listing_count" => "navigation_2_count", "auction_count" => "navigation_2_auction_count", "ad_count" => "navigation_2_ad_count");
					$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'], $link, $css);
				}
				if ($show_module['display_category_description'])
					$this->body .="</a><br><font class=navigation_2_description>".urldecode(stripslashes($show_category['description']))."</font>";
				$this->body .="</td>";
				if ($show_module['number_of_browsing_columns'] > 1)
				{
					if ($show_category = $category_result->FetchRow())
					{
						$this->body .="<td  width=".$column_width.">";
						if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
						{
							$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
						}
						//$category_name = $this->get_category_name($db,$show_category['category_id']);
						$this->body .= str_replace(">", " class=navigation_2_name>", $link).urldecode(stripslashes($show_category['category_name']));
						if ($show_module['module_display_new_ad_icon'])
							$this->check_category_new_ad_icon_use($db,$show_category['category_id']);				
						$this->body .= "</a>";
						if ($show_module['display_category_count']) 
						{
							$css = array("listing_count" => "navigation_2_count", "auction_count" => "navigation_2_auction_count", "ad_count" => "navigation_2_ad_count");
							$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'], $link, $css);
						}
						if ($show_module['display_category_description'])
							$this->body .="</a><br><font class=navigation_2_description>".urldecode(stripslashes($show_category['description']))."</font>";
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
							$this->body .="<td  width=".$column_width.">";
							if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
							{
								$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
							}
							//$category_name = $this->get_category_name($db,$show_category['category_id']);
							$this->body .= str_replace(">", " class=navigation_2_name>", $link).urldecode(stripslashes($show_category['category_name']));
							if ($show_module['module_display_new_ad_icon'])
								$this->check_category_new_ad_icon_use($db,$show_category['category_id']);				
							$this->body .= "</a>";
							if ($show_module['display_category_count']) 
							{
								$css = array("listing_count" => "navigation_2_count", "auction_count" => "navigation_2_auction_count", "ad_count" => "navigation_2_ad_count");
								$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'], $link, $css);
							}
							if ($show_module['display_category_description'])
								$this->body .="</a><br><font class=navigation_2_description>".urldecode(stripslashes($show_category['description']))."</font>";
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
								$this->body .="<td  width=".$column_width.">";
								if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
								{
									$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
								}
								//$category_name = $this->get_category_name($db,$show_category['category_id']);
								$this->body .= str_replace(">", " class=navigation_2_name>", $link).urldecode(stripslashes($show_category['category_name']));
								if ($show_module['module_display_new_ad_icon'])
									$this->check_category_new_ad_icon_use($db,$show_category['category_id']);				
								$this->body .= "</a>";
								if ($show_module['display_category_count']) 
								{
									$css = array("listing_count" => "navigation_2_count", "auction_count" => "navigation_2_auction_count", "ad_count" => "navigation_2_ad_count");
									$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'], $link, $css);
								}
								if ($show_module['display_category_description'])
									$this->body .="</a><br><font class=navigation_2_description>".urldecode(stripslashes($show_category['description']))."</font>";
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
									$this->body .="<td  width=".$column_width.">";
									if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
									{
										$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
									}
									//$category_name = $this->get_category_name($db,$show_category['category_id']);
									$this->body .= str_replace(">", " class=navigation_2_name>", $link).urldecode(stripslashes($show_category['category_name']));
									if ($show_module['module_display_new_ad_icon'])
										$this->check_category_new_ad_icon_use($db,$show_category['category_id']);				
									$this->body .= "</a>";
									if ($show_module['display_category_count']) 
									{
										$css = array("listing_count" => "navigation_2_count", "auction_count" => "navigation_2_auction_count", "ad_count" => "navigation_2_ad_count");
										$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'], $link, $css);
									}
									if ($show_module['display_category_description'])
										$this->body .="</a><br><font class=navigation_2_description>".urldecode(stripslashes($show_category['description']))."</font>";
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
					$this->body .="<tr class=navigation_3_subcategories_to>\n\t<td>\n\t".urldecode($this->messages[1891])." ".$current_category_name."\n\t</td>\n</tr>\n";
				
			}
			$this->body .="</table>";
		}
		else
		{
			if (($show_module['module_display_ad_description']) && ($this->site_category))
				$this->body .="<tr class=navigation_3_subcategories_to>\n\t<td>\n\t".urldecode($this->messages[1891])." ".$current_category_name."\n\t</td>\n</tr>\n";
			if ($show_module['display_no_subcategory_message'])
				$this->body .="<tr class=navigation_2_subcategories_to>\n\t<td>\n\t".urldecode($this->messages[1518])." ".$current_category_name."\n\t</td>\n</tr>\n";
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
				$this->error_message = "<font class=error_message>".urldecode($this->messages[1517])."</font>";
				return false;
			}
		}
	}
}

?>
