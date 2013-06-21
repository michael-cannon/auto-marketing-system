<? //user_management_seller_flyer.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Display_seller_flyer extends Site {

//########################################################################

	function Display_seller_sign($db,$classified_user_id,$language_id,$category_id=0,$page=0,$classified_id=0,$product_configuration=0)
	{
		$this->Site(&$db,1,$language_id,$classified_user_id,$product_configuration);
		if ($category_id)
			$this->site_category = $category_id;
		elseif ($classified_id)
		{
			$show = $this->get_classified_data($db,$classified_id);
			$this->site_category = $show->CATEGORY;
		}
		else
			$this->site_category = 0;
		$this->get_ad_configuration(&$db);
		if ($page)
			$this->page_result = $page;
		else
			$this->page_result = 1;
	} //end of function Display_ad

//###########################################################

	function display_classified($db,$id=0)
	{
		$this->page_id = 70;
		$this->get_text(&$db);
		if ($id)
		{
			
			$show = $this->get_classified_data($db,$id);
			if ($show)
			{
				if ($show->LIVE == 1)
				{
					//get the template from the database
					$this->sql_query = "select template_id from ".$this->pages_templates_table." where language_id = ".$this->language_id." and page_id = 70";
					$template_id_result = &$db->Execute($this->sql_query);
					if (!$template_id_result)
					{
						return false;
					}	
					elseif ($template_id_result->RecordCount() == 1)
					{
						$show_template_id = $template_id_result->FetchNextObject();						
						$this->sql_query = "select template_code from ".$this->templates_table." where template_id = ".$show_template_id->TEMPLATE_ID;
						$template_result = &$db->Execute($this->sql_query);
						if (!$template_result)
						{
							return false;
						}	
						elseif ($template_result->RecordCount() == 1)	
						{
							$show_template = $template_result->FetchNextObject();
							$template = $show_template->TEMPLATE_CODE;
						}
						else
						{
							return false;
						}
					}
					else
					{
						return false;
					}
					
					$this->body .="<table cellpadding=3 cellspacing=0 border=1 width=100%>\n";

					$seller_data = $this->get_user_data(&$db,$show->SELLER);

					$template = str_replace("<<CLASSIFIED_ID>>",$show->ID,$template);
					$template = str_replace("<<TITLE>>",stripslashes($sign_info["title"]),$template);
					$template = str_replace("<<CONTACT>>",stripslashes($sign_info["contact_name"]),$template);
					$template = str_replace("<<DESCRIPTION>>",stripslashes($sign_info["description"]),$template);
					$template = str_replace("<<PHONE>>",$sign_info["phone_1"],$template);
					if($sign_info["item_type"]==1)
						$template = str_replace("<<PRICE>>",$sign_info["price"],$template);
					if($sign_info["item_type"]==2)
						$template = str_replace("<<PRICE>>",$sign_info["minimum_bid"],$template);
					
					$css = $this->get_css(&$db);
					if (strlen(trim($css)) > 0)
					{
						//echo "hello css<bR>\n";
						$this->font_stuff = "<style type=\"text/css\">\n<!--\n".$this->font_stuff."-->\n</style>\n";
						$template = str_replace("<<CSSSTYLESHEET>>",$this->font_stuff,$template);
					}
					echo $template;
					return true;
				}
				else
				{
					return false;
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
			$this->body .="no id<br>\n";
			return false;
		} //end of else
	} //end of function display_classifed

//####################################################################################

	function get_ads_extra_values($db,$classified_id)
	{
		$this->sql_query = "select * from ".$this->classified_extra_table." where classified_id = ".$classified_id." and checkbox = 0";
		$special_result = &$db->Execute($this->sql_query);
		//echo $this->sql_query."<Br>\n";
		if (!$special_result)
		{
			//$this->body .=$this->sql_query." is the query<br>\n";
			$this->error_message = urldecode($this->messages[81]);
			return false;
		}
		elseif ($special_result->RecordCount() > 0 )
		{
			$extra_template = stripslashes(urldecode($this->ad_configuration_data->USER_EXTRA_TEMPLATE));

			//$this->body .="<tr>\n\t<td>\n\t<table cellpadding=2 cellspacing=1 border=0>\n\t";
			//list the special description fields
			while ($show_special = $special_result->FetchNextObject())
			{
				$current_line = str_replace("<<EXTRA_QUESTION_NAME>>",stripslashes(urldecode($show_special->NAME)),$extra_template);
				$current_line = str_replace("<<EXTRA_QUESTION_VALUE>>",stripslashes(urldecode($show_special->VALUE)),$current_line);
				$question_block .= $current_line;
			}
			//$this->body .="</table>\n\t</td>\n</tr>\n";
		}
		return $question_block;
	} //end of function get_ads_extra_questions

//#################################################################################

	function get_ads_extra_checkboxs($db,$classified_id)
	{
		$this->sql_query = "select * from ".$this->classified_extra_table." where classified_id = ".$classified_id." and checkbox = 1";
		$special_result = &$db->Execute($this->sql_query);
		//echo $this->sql_query." inside of checkboxes<Br>\n";
		if (!$special_result)
		{
			//$this->body .=$this->sql_query." is the query<br>\n";
			$this->error_message = urldecode($this->messages[81]);
			return false;
		}
		elseif ($special_result->RecordCount() > 0 )
		{
			$extra_template = stripslashes(urldecode($this->ad_configuration_data->USER_CHECKBOX_TEMPLATE));

			//$this->body .="<tr>\n\t<td>\n\t<table cellpadding=2 cellspacing=1 border=0>\n\t";
			//list the special description fields
			while ($show_special = $special_result->FetchNextObject())
			{
				$current_line = str_replace("<<EXTRA_CHECKBOX_NAME>>", stripslashes(urldecode($show_special->NAME)),$extra_template);
				$question_block .= $current_line;
			}
			//$this->body .="</table>\n\t</td>\n</tr>\n";
		}
		return $question_block;
	} //end of function get_ads_extra_checkboxs

//#################################################################################

	function classified_exists ($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$this->sql_query = "select id from ".$this->classifieds_table." where id = ".$classified_id;
			$result = &$db->Execute($this->sql_query);

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

} // end of class Display_seller_flyer

?>
