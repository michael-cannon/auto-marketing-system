<? //module_display_main_category_navigation_1.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$this->get_css($db,$show_module['page_id']);
$this->get_text($db,$show_module['page_id']);

$sql_query_classifieds_count = "select * from ".$this->classifieds_table." where live = 1 ";
$result_count = $db->Execute($sql_query_classifieds_count);
$total_count_returned_result = $result_count->RecordCount();
$this->body = "<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=ViewAll> <b>View All ($total_count_returned_result)</b></a>";

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
	$this->sql_query = "select * from geodesic_categories where
		parent_id = 0 order by display_order,category_name";
	$category_result = $db->Execute($this->sql_query);
	//echo $this->sql_query." is the query<br>\n";
	if (!$category_result)
	{
		$this->error_message = "<font class=error_message>".urldecode($this->messages[1515])."</font>";
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
				$link = "<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id'];
				if ($browse_type) $link .= "&c=".$browse_type;
				$link .= ">";

				//display the sub categories of this category
				$this->body .="<tr><td  width=".$column_width.">";
				if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
				{
					$this->body .= $link;
					$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
					$this->body .= "</a>";
				}
				$category_name = $this->get_category_name($db,$show_category['category_id']);
				$this->body .= str_replace(">", " class=main_navigation_1_name>", $link).urldecode(stripslashes($category_name->CATEGORY_NAME));
				$this->body .= "</a>";
				if ($show_module['display_category_count'])
				{
					$category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
					$category_count['ad_count'] = $show_category['category_count'];
					$category_count['auction_count'] = $show_category['auction_category_count'];
					$css = array("listing_count" => "main_navigation_1_count", "auction_count" => "main_navigation_1_auction_count", "ad_count" => "main_navigation_1_ad_count");
					$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'], $link, $css,$category_count);
				}
				if ($show_module['module_display_new_ad_icon'])
				{
					$this->check_category_new_ad_icon_use($db,$show_category['category_id']);
				}
				if ($show_module['display_category_description'])
					$this->body .= "<br><font class=main_navigation_1_description>".urldecode(stripslashes($category_name->DESCRIPTION))."</font>";
				$this->body .="</td>";
				if ($show_module['number_of_browsing_columns'] > 1)
				{
					if ($show_category = $category_result->FetchRow())
					{
						$this->body .="<td width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id'];
						if ($browse_type) $this->body .= "&c=".$browse_type;
						$this->body .= ">";
						if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
						{
							$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
						}
						$category_name = $this->get_category_name($db,$show_category['category_id']);
						$this->body .= str_replace(">", " class=main_navigation_1_name>", $link).urldecode(stripslashes($category_name->CATEGORY_NAME))."</a>";
						if ($show_module['display_category_count'])
						{
							$category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
							$category_count['ad_count'] = $show_category['category_count'];
							$category_count['auction_count'] = $show_category['auction_category_count'];
							$css = array("listing_count" => "main_navigation_1_count", "auction_count" => "main_navigation_1_auction_count", "ad_count" => "main_navigation_1_ad_count");
							$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'],$link, $css,$category_count);
						}
						if ($show_module['display_category_description'])
							$this->body .="<br><font class=main_navigation_1_description>".urldecode(stripslashes($category_name->DESCRIPTION))."</font>";
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
							$this->body .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id'];
							if ($browse_type) $this->body .= "&c=".$browse_type;
							$this->body .= ">";
							if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
							{
								$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
							}
							$category_name = $this->get_category_name($db,$show_category['category_id']);
							$this->body .= str_replace(">", " class=main_navigation_1_name>", $link).urldecode(stripslashes($category_name->CATEGORY_NAME))."</a>";
							if ($show_module['display_category_count'])
							{
								$category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
								$category_count['ad_count'] = $show_category['category_count'];
								$category_count['auction_count'] = $show_category['auction_category_count'];
								$css = array("listing_count" => "main_navigation_1_count", "auction_count" => "main_navigation_1_auction_count", "ad_count" => "main_navigation_1_ad_count");
								$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'],$link, $css,$category_count);
							}
							if ($show_module['module_display_new_ad_icon'])
							{
								$this->check_category_new_ad_icon_use($db,$show_category['category_id']);
								$this->body .= "</font>";
							}
							if ($show_module['display_category_description'])
								$this->body .="<br><font class=main_navigation_1_description>".urldecode(stripslashes($category_name->DESCRIPTION))."</font>";
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
								$this->body .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id'];
								if ($browse_type) $this->body .= "&c=".$browse_type;
								$this->body .= ">";
								if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
								{
									$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
								}
								$category_name = $this->get_category_name($db,$show_category['category_id']);
								$this->body .= str_replace(">", " class=main_navigation_1_name>", $link).urldecode(stripslashes($category_name->CATEGORY_NAME))."</a>";
								if ($show_module['display_category_count'])
								{
									$category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
									$category_count['ad_count'] = $show_category['category_count'];
									$category_count['auction_count'] = $show_category['auction_category_count'];
									$css = array("listing_count" => "main_navigation_1_count", "auction_count" => "main_navigation_1_auction_count", "ad_count" => "main_navigation_1_ad_count");
									$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'], $link, $css,$category_count);
								}
								if ($show_module['module_display_new_ad_icon'])
								{
									$this->check_category_new_ad_icon_use($db,$show_category['category_id']);
									$this->body .= "</font>";
								}
								if ($show_module['display_category_description'])
									$this->body .="<br><font class=main_navigation_1_description>".urldecode(stripslashes($category_name->DESCRIPTION))."</font>";
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
									$this->body .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id'];
									if ($browse_type) $this->body .= "&c=".$browse_type;
									$this->body .= ">";
									if ((strlen(trim($show_category['category_image'])) > 0) && ($show_module['display_category_image']))
									{
										$this->body .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
									}
									$category_name = $this->get_category_name($db,$show_category['category_id']);
									$this->body .= str_replace(">", " class=main_navigation_1_name>", $link).urldecode(stripslashes($category_name->CATEGORY_NAME))."</a>";
									if ($show_module['display_category_count'])
									{
										$category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
										$category_count['ad_count'] = $show_category['category_count'];
										$category_count['auction_count'] = $show_category['auction_category_count'];
										$css = array("listing_count" => "main_navigation_1_count", "auction_count" => "main_navigation_1_auction_count", "ad_count" => "main_navigation_1_ad_count");
										$this->body .= $this->display_category_count($db,$show_category['category_id'], $show_module['browsing_count_format'], $link, $css,$category_count);
									}
									if ($show_module['module_display_new_ad_icon'])
									{
										$this->check_category_new_ad_icon_use($db,$show_category['category_id']);
										$this->body .= "</font>";
									}
									if ($show_module['display_category_description'])
										$this->body .="<br><font class=main_navigation_1_description>".urldecode(stripslashes($category_name->DESCRIPTION))."</font>";
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
			}
			$this->body .="</table>";
		}
		else
		{
			if ($show_module['display_no_subcategory_message'])
				$this->body .="<tr class=main_navigation_1_subcategories_to>\n\t<td>\n\t".urldecode($this->messages[1516])." ".$current_category_name."\n\t</td>\n</tr>\n";
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
				$this->error_message = "<font class=error_message>".urldecode($this->messages[1515])."</font>";
				return false;
			}
		}
	}
}

?>
