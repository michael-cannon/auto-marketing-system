<? //browse_display_ad_full_images.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Display_ad_full_images extends Site {
	var $subcategory_array = array();
	var $notify_data = array();

//########################################################################

	function Display_ad_full_images ($db,$classified_user_id,$language_id,$category_id=0,$page=0,$classified_id=0,$affiliate_id=0,$product_configuration=0)
	{
		if ($limit)
			$this->browse_limit = $limit;
		$this->Site($db,1,$language_id,$classified_user_id,$product_configuration);
		if ($category_id)
			$this->site_category = $category_id;
		elseif ($classified_id)
		{
			$show = $this->get_classified_data($db,$classified_id);
			$this->site_category = $show->CATEGORY;
		}
		else
			$this->site_category = 0;
		$this->get_ad_configuration($db);
		if ($page)
			$this->page_result = $page;
		else
			$this->page_result = 1;
		$this->affiliate_id = $affiliate_id;
	} //end of function Display_ad

//###########################################################

	function display_classified_full_images($db,$id=0)
	{
		$this->page_id = 84;
		$this->get_text($db);
		if ($id)
		{
			$show = $this->get_classified_data($db,$id);
			if ($show)
			{
				if ($show->LIVE == 1)
				{
					$this->body .="<table cellpadding=3 cellspacing=0 border=0 width=100%>\n";
					$this->body .= "<tr class=full_images_title><td>".urldecode($show->TITLE)."</td></tr>\n";

					$image_block = $this->display_full_image_template($db,$id);
					
					$this->body .= "<tr class=full_images_description><td colspan=2>".stripslashes(urldecode($show->DESCRIPTION))."</td></tr>\n";

					//if ($this->affiliate_id)
					//	$this->body .= "<tr class=back_to_current_ad_link><td colspan=2><a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id." class=back_to_current_ad_link>".urldecode($this->messages[1357])."</a></td></tr>\n";
					//else
					//	$this->body .= "<tr class=back_to_current_ad_link><td colspan=2><a href=".$this->configuration_data['classifieds_url']."?a=2&b=".$id." class=back_to_current_ad_link>".urldecode($this->messages[1357])."</a></td></tr>\n";

					$this->body .= "<tr width=100%><td colspan=2>";

					$this->body .= "<table width=100%>";
					$this->body .= "<tr align=center>";
					if ($this->affiliate_id)
					{
						$this->body .= "<td class=full_images_sellers_other_link><a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id." class=full_images_sellers_other_link>".urldecode($this->messages[1361])."</a></td>\n";
					}
					else
					{
						$this->body .= "<td class=full_images_favorite_link><a href=".$this->configuration_data['classifieds_url']."?a=20&b=".$id." class=full_images_favorite_link>".urldecode($this->messages[1358])."</a></td>";
						$this->body .= "<td class=full_images_notify_friend_link><a href=".$this->configuration_data['classifieds_url']."?a=12&b=".$id." class=full_images_notify_friend_link>".urldecode($this->messages[1359])."</a></td>\n";
						$this->body .= "<td class=full_images_seller_link><a href=".$this->configuration_data['classifieds_url']."?a=13&b=".$id." class=full_images_seller_link>".urldecode($this->messages[1360])."</a></td>\n";
						$this->body .= "<td class=full_images_sellers_other_link><a href=".$this->configuration_data['classifieds_url']."?a=6&b=".$show->SELLER." class=full_images_sellers_other_link>".urldecode($this->messages[1361])."</a></td>\n";
					}
						
					$this->body .= "</tr></table></td></tr>";

					$this->body .= "<tr class=full_images_title><td align=center colspan=2><a href=";
					if ($this->affiliate_id)
						$this->body .= $this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id;
					else 
						$this->body .= $this->configuration_data['classifieds_url']."?a=2&b=".$id;
					$this->body.=" class=top_back_to_current_ad_link>".urldecode($this->messages[1357])."</a></td></tr>\n";
					$this->body .= "</table>\n";
					$this->display_page($db);
					return true;
				}
				elseif($show->LIVE == 0)
                {
                    $this->body .="<table cellpadding=3 cellspacing=0 border=0 width=100%>\n";
                    $this->body .= "<tr class=full_images_title><td>".urldecode($show->TITLE)."&nbsp;"."<font color=#ff0000>[Closed]</font></td></tr>\n";

                    $image_block = $this->display_full_image_template($db,$id);

                    $this->body .= "<tr class=full_images_description><td colspan=2>".stripslashes(urldecode($show->DESCRIPTION))."</td></tr>\n";

                    //if ($this->affiliate_id)
                    //    $this->body .= "<tr class=back_to_current_ad_link><td colspan=2><a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id." class=back_to_current_ad_link>".urldecode($this->messages[1357])."</a></td></tr>\n";
                    //else
                    //    $this->body .= "<tr class=back_to_current_ad_link><td colspan=2><a href=".$this->configuration_data['classifieds_url']."?a=2&b=".$id." class=back_to_current_ad_link>".urldecode($this->messages[1357])."</a></td></tr>\n";

                    $this->body .= "<tr width=100%><td colspan=2>";

                    $this->body .= "<table width=100%>";
                    $this->body .= "<tr align=center>";
                    if ($this->affiliate_id)
                    {
                        $this->body .= "<td class=full_images_sellers_other_link><a href=".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id." class=full_images_sellers_other_link>".urldecode($this->messages[1361])."</a></td>\n";
                    }
                    else
                    {
                        $this->body .= "<td class=full_images_favorite_link><a href=".$this->configuration_data['classifieds_url']."?a=20&b=".$id." class=full_images_favorite_link>".urldecode($this->messages[1358])."</a></td>";
                        $this->body .= "<td class=full_images_notify_friend_link><a href=".$this->configuration_data['classifieds_url']."?a=12&b=".$id." class=full_images_notify_friend_link>".urldecode($this->messages[1359])."</a></td>\n";
                        $this->body .= "<td class=full_images_seller_link><a href=".$this->configuration_data['classifieds_url']."?a=13&b=".$id." class=full_images_seller_link>".urldecode($this->messages[1360])."</a></td>\n";
                        $this->body .= "<td class=full_images_sellers_other_link><a href=".$this->configuration_data['classifieds_url']."?a=6&b=".$show->SELLER." class=full_images_sellers_other_link>".urldecode($this->messages[1361])."</a></td>\n";
                    }

                    $this->body .= "</tr></table></td></tr>";

                    $this->body .= "<tr class=full_images_title><td align=center colspan=2><a href=";
                    if ($this->affiliate_id)
                        $this->body .= $this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id;
                    else
                        $this->body .= $this->configuration_data['classifieds_url']."?a=2&b=".$id;
                    $this->body.=" class=top_back_to_current_ad_link>".urldecode($this->messages[1357])."</a></td></tr>\n";
                    $this->body .= "</table>\n";
                    $this->display_page($db);
                    return true;
                }
			}
			else
			{
				return false;
			}
		}
		else
		{
			//no id to display
			//$this->body .="no id<br>\n";
			return false;
		} //end of else
	} //end of function display_classifed

//####################################################################################

	function display_full_image_template($db,$classified_id)
	{
		if ($classified_id)
		{
			//get full-size image template
			$template_id = $this->ad_configuration_data->FULL_SIZE_IMAGE_TEMPLATE;
			$this->sql_query = "select template_code,location from ".$this->templates_table." where template_id = ".$template_id;
			$template_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$template_result)
			{
				return false;
			}
			elseif ($template_result->RecordCount() ==1)
			{
				$show_template = $template_result->FetchNextObject();
				$full_image_template_base = stripslashes($show_template->TEMPLATE_CODE);
			}
			else
			{
				return false;
			}
						
			$this->get_image_data($db,$classified_id,1);
			reset ($this->images_to_display);
			foreach ($this->images_to_display as $value)
			{
				$this->body .= "<tr><td colspan=2 align=center>";
				$image_template = $full_image_template_base;
				
				//check any full sized image limits
				if (($value["original_image_width"] > $this->ad_configuration_data->MAXIMUM_FULL_IMAGE_WIDTH) || 
					($value["original_image_height"] > $this->ad_configuration_data->MAXIMUM_FULL_IMAGE_HEIGHT))
				{
					if (($value["original_image_width"] > $this->ad_configuration_data->MAXIMUM_FULL_IMAGE_WIDTH) && ($value["original_image_height"] > $this->ad_configuration_data->MAXIMUM_FULL_IMAGE_HEIGHT))
					{
						$imageprop = ($this->ad_configuration_data->MAXIMUM_FULL_IMAGE_WIDTH * 100) / $value["original_image_width"];
						$imagevsize = ($value["original_image_height"] * $imageprop) / 100 ;
						$image_width = $this->ad_configuration_data->MAXIMUM_FULL_IMAGE_WIDTH;
						$image_height = ceil($imagevsize);

						if ($image_height > $this->ad_configuration_data->MAXIMUM_FULL_IMAGE_HEIGHT)
						{
							$imageprop = ($this->ad_configuration_data->MAXIMUM_FULL_IMAGE_HEIGHT * 100) / $value["original_image_height"];
							$imagehsize = ($value["original_image_width"] * $imageprop) / 100 ;
							$image_height = $this->ad_configuration_data->MAXIMUM_FULL_IMAGE_HEIGHT;
							$image_width = ceil($imagehsize);
						}
					}
					elseif ($value["original_image_width"] > $this->ad_configuration_data->MAXIMUM_FULL_IMAGE_WIDTH)
					{
						$imageprop = ($this->ad_configuration_data->MAXIMUM_FULL_IMAGE_WIDTH * 100) / $value["original_image_width"];
						$imagevsize = ($value["original_image_height"] * $imageprop) / 100 ;
						$image_width = $this->ad_configuration_data->MAXIMUM_FULL_IMAGE_WIDTH;
						$image_height = ceil($imagevsize);
					}
					elseif ($value["original_image_height"] > $this->ad_configuration_data->MAXIMUM_FULL_IMAGE_HEIGHT)
					{
						$imageprop = ($this->ad_configuration_data->MAXIMUM_FULL_IMAGE_HEIGHT * 100) / $value["original_image_height"];
						$imagehsize = ($value["original_image_width"] * $imageprop) / 100 ;
						$image_height = $this->ad_configuration_data->MAXIMUM_FULL_IMAGE_HEIGHT;
						$image_width = ceil($imagehsize);
					}
					else
					{
						$image_width = $value["original_image_width"];
						$image_height = $value["original_image_height"];
					}
					
				}
				else
				{
					$image_width = $value["original_image_width"];
					$image_height = $value["original_image_height"];
				}

				if ($value["type"] == 1)
                {
					//$image =  "<img src=".$value["url"]." width=".$image_width." height=".$image_height." border=0>";
                    $solt = rand(1,10000);
                    $image =  "<img src=imagewm.php?image=".$value["id"]."&st=".$solt." width=".$image_width." height=".$image_height." border=0>";
                }
                elseif ($value["type"] == 2)
                {
					$image =  "<img src=get_image.php?image=".$value["id"]." width=".$image_width." height=".$image_height." border=0>";
                }

				$image_template = str_replace("<<FULL_SIZE_IMAGE>>",$image,$image_template);
				if (strlen($value["image_text"]) > 0)
				{
					$text = "<br>".$value["image_text"];	
					$image_template = str_replace("<<FULL_SIZE_TEXT>>",$text,$image_template);
				}
				else
					$image_template = str_replace("<<FULL_SIZE_TEXT>>","",$image_template);
				$this->body .= $image_template."</td></tr>";
			}
			return true;
		}
		else
			return false;

	} //end of function display_full_image_template

//####################################################################################

	function classified_exists ($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$this->sql_query = "select id from ".$this->classifieds_table." where id = ".$classified_id;
			$result = $db->Execute($this->sql_query);

			if (!$result)
			{
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				return true;
			}
			else
			{
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
		}
		else
		{
			//no auction id to check
			$this->error_message = urldecode($this->messages[81]);
			return false;
		}
	 } //end of function classified_exists

//####################################################################################

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

} // end of class Display_ad

?>
