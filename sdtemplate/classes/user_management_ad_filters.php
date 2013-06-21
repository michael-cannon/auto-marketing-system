<? //user_management_ad_filters.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class User_management_ad_filters extends Site
{
	var $debug_filters = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function User_management_ad_filters ($db,$language_id,$classified_user_id=0,$product_configuration=0)
	{
		$this->Site($db,6,$language_id,$classified_user_id,$product_configuration);
	} //end of function User_management

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_all_ad_filters($db)
	{
		$this->page_id = 27;
		$this->get_text($db);
		if ($this->classified_user_id)
		{
			$this->sql_query = "select * from ".$this->ad_filter_table." where user_id = ".$this->classified_user_id." order by date_started desc";
			$result = $db->Execute($this->sql_query);
			if ($this->debug_filters) echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug_filters)
				{
					echo $this->sql_query."<br>\n";
					echo $db->ErrorMsg()." is the sql error<bR>\n";
				}
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
				$this->body .="<tr class=user_management_page_title><td colspan=4 valign=top width=100%>\n\t\t".urldecode($this->messages[627])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=my_ad_filters_title>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[374]);
				$this->body .=$this->display_help_link(376);
				$this->body .="\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=table_description>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[375])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr  class=table_column_headers>\n\t\t<td>\n\t\t".urldecode($this->messages[378])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[379])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[380])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t"."&nbsp;\n\t\t</td>\n\t</tr>\n\t";

				$this->row_count = 0;
				while ($show = $result->FetchNextObject())
				{
					if (($this->row_count % 2) == 0)
						$css_tag = "result_set_even_rows";
					else
						$css_tag = "result_set_odd_rows";
					if (!$show->CATEGORY_ID)
						$category_name = urldecode($this->messages[2313]);
					else
					{
						$name = $this->get_category_name($db,$show->CATEGORY_ID);
						$category_name = $name->CATEGORY_NAME;
					}
					$this->body .="<tr class=".$css_tag.">\n\t\t<td>\n\t\t".$category_name." ";
					if ($show->SUB_CATEGORY_CHECK)
						$this->body .=urldecode($this->messages[382]);
					$this->body .="\n\t\t</td>\n\t\t";
					$this->body .="<td>\n\t\t".$show->SEARCH_TERMS."\n\t\t</td>\n\t\t";
					$this->body .="<td>\n\t\t".date("M j, Y - h:i - l",$show->DATE_STARTED)."\n\t\t</td>\n\t\t";
					$this->body .="<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=9&c=2&d=".$show->FILTER_ID.">".urldecode($this->messages[381])."\n\t\t</td>\n\t</tr>\n\t";
					$this->row_count++;
				}

				$this->body .="<tr class=add_remove_ad_filter_links>\n\t\t<td colspan=4>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=9&c=1 class=add_remove_ad_filter_links >".urldecode($this->messages[384])."</a>\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=add_remove_ad_filter_links>\n\t<td colspan=4>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=9&c=3 class=add_remove_ad_filter_links >".urldecode($this->messages[383])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=user_management_home_link>\n\t<td colspan=4>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[385])."</a>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
			}
			else
			{
				//there are no ad filters for this user
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
				$this->body .="<tr class=user_management_page_title><td colspan=4 valign=top width=100%>\n\t\t".urldecode($this->messages[627])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=my_ad_filters_title>\n\t\t<td>\n\t\t".urldecode($this->messages[374]);
				$this->body .=$this->display_help_link(376);
				$this->body .="\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=table_description>\n\t\t<td>".urldecode($this->messages[377])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=add_remove_ad_filter_links>\n\t\t<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=9&c=1 class=add_remove_ad_filter_links>".urldecode($this->messages[384])."</a>\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=user_management_home_link>\n\t<td>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[385])."</a>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
			}
			$this->display_page($db);
			return true;

		}
		else
		{
			return false;
		}

	} //end of function display_all_ad_filters

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_ad_filter($db,$filter_id=0)
	{
		if ($this->classified_user_id)
		{
			if ($filter_id)
			{
				$this->sql_query = "delete from ".$this->ad_filter_table." where filter_id = ".$filter_id;
				$result = $db->Execute($this->sql_query);
				if ($this->debug_filters) echo $this->sql_query."<br>\n";
				if (!$result)
				{
					if ($this->debug_filters) echo $this->sql_query."<br>\n";
					$this->site_error($db->ErrorMsg());
					return false;
				}

				$this->sql_query = "delete from ".$this->ad_filter_categories_table." where filter_id = ".$filter_id;
				$result = $db->Execute($this->sql_query);
				if ($this->debug_filters) echo $this->sql_query."<br>\n";
				if (!$result)
				{
					if ($this->debug_filters) echo $this->sql_query."<br>\n";
					$this->site_error($db->ErrorMsg());
					return false;
				}
				return true;
			}
			else
			{
				//no filter id
				$this->error_message = $this->data_missing_error_message;
				return false;
			}
		}
		else
			return false;
	} //end of function delete_ad_filter

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function add_new_filter_form($db)
	{
		$this->page_id = 28;
		$this->get_text($db);
		if ($this->classified_user_id)
		{
			$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=4&b=9&c=4 method=post>\n";
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
			$this->body .="<tr class=user_management_page_title><td colspan=4 width=100%>\n\t\t".urldecode($this->messages[626])."\n\t\t</td>\n\t</tr>\n\t";

			$this->body .="<tr class=add_new_filter_title>\n\t<td colspan=2>\n\t".urldecode($this->messages[429])."\n\t</td>\n</tr>\n";
			$this->body .="<tr class=table_description>\n\t<td colspan=2>\n\t".urldecode($this->messages[430])."\n\t</td>\n</tr>\n";

			$this->body .="<tr class=filter_body>\n\t<td align=right>".urldecode($this->messages[431])."\n\t</td>\n\t";
			$this->body .="<td>\n\t";
			$this->get_category_dropdown($db,"d[category_id]");
			$this->body .="\n\t</td>\n</tr>\n";

			$this->body .="<tr class=filter_body>\n\t<td align=right>".urldecode($this->messages[432])."\n\t</td>\n\t";
			$this->body .="<td>\n\t<input type=radio name=d[subcategories_also] value=1 checked>".urldecode($this->messages[3271])."<br><input type=radio name=d[subcategories_also] value=0>".urldecode($this->messages[3272])."\n\t</td>\n</tr>\n";

			$this->body .="<tr class=filter_body>\n\t<td align=right>".urldecode($this->messages[433])."<br>".urldecode($this->messages[434])."\n\t</td>\n\t";
			$this->body .="<td>\n\t<input type=text name=d[search_terms] size=50 maxsize=50>\n\t</td>\n</tr>\n";

			$this->body .="<tr class=submit_button>\n\t<td colspan=2>\n\t"."<input type=submit value=\"".urldecode($this->messages[3273])."\">\n\t</td>\n</tr>\n";
			$this->body .="<tr class=user_management_home_link>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[436])."</a>\n\t</td>\n</tr>\n";
			$this->body .="</table>\n";
			$this->body .="</form>\n";
			$this->display_page($db);
			return true;
		}
		else
			return false;

	} //end of function add_new_filter_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_new_filter($db,$filter_info=0)
	{
		if ($this->classified_user_id)
		{
			if ($filter_info)
			{
				if (strlen(trim($filter_info["search_terms"])) > 0)
				{
					$current_time = $this->shifted_time($db);
					$search_terms_array = explode(",",$filter_info["search_terms"]);
					foreach ($search_terms_array as $value)
					{

						$this->sql_query = "select filter_id from ".$this->ad_filter_table." where user_id = ".$this->classified_user_id."
							and search_terms = \"".$value."\"";
						$filter_id_result = $db->Execute($this->sql_query);
						if ($this->debug_filters) echo $this->sql_query."<br>\n";
						if (!$filter_id_result)
						{
							if ($this->debug_filters) echo $this->sql_query."<br>\n";
							return false;
						}
						elseif ($filter_id_result->RecordCount() > 0)
						{
							while ($show_filter = $filter_id_result->FetchNextObject())
							{
								$this->sql_query = "delete from ".$this->ad_filter_categories_table." where filter_id = ".$show_filter->FILTER_ID;
								$delete_categories_result = $db->Execute($this->sql_query);
								if ($this->debug_filters) echo $this->sql_query."<br>\n";
								if (!$delete_categories_result)
								{
									if ($this->debug_filters) echo $this->sql_query."<br>\n";
									return false;
								}
							}

							$this->sql_query = "delete from ".$this->ad_filter_table." where user_id = ".$this->classified_user_id."
								and search_terms = \"".$value."\"";
							$delete_result = $db->Execute($this->sql_query);
							if ($this->debug_filters) echo $this->sql_query."<br>\n";
							if (!$delete_result)
							{
								if ($this->debug_filters) echo $this->sql_query."<br>\n";
								$this->error_message = $this->internal_error_message;
								return false;
							}
						}
						$this->sql_query = "insert into ".$this->ad_filter_table."
							(user_id,search_terms,date_started,category_id,sub_category_check)
							values
							(".$this->classified_user_id.",\"".$value."\",".$current_time.",".$filter_info["category_id"].",".$filter_info["subcategories_also"].")";
						$insert_filter_result = $db->Execute($this->sql_query);
						if ($this->debug_filters) echo $this->sql_query."<br>\n";
						if (!$insert_filter_result)
						{
							if ($this->debug_filters) echo $this->sql_query."<br>\n";
							$this->error_message = $this->internal_error_message;
							return false;
						}

						$filter_id = $db->Insert_ID();

						$this->sql_query = "insert into ".$this->ad_filter_categories_table."
							(filter_id, category_id)
							values
							(".$filter_id.",".$filter_info["category_id"].")";
						$insert_category_result = $db->Execute($this->sql_query);
						if ($this->debug_filters) echo $this->sql_query."<br>\n";
						if (!$insert_category_result)
						{
							if ($this->debug_filters) echo $this->sql_query."<br>\n";
							$this->error_message = $this->internal_error_message;
							return false;
						}

						if ($filter_info["subcategories_also"] == 1)
						{
							//get subcategories and insert them into the db
							$this->get_subcategories_for_dropdown($db,$filter_info["category_id"]);

							//use class variable to insert category_ids
							reset($this->category_dropdown_id_array);
							foreach ($this->category_dropdown_id_array as $value)
							{
								$this->sql_query = "insert into ".$this->ad_filter_categories_table."
									(filter_id, category_id)
									values
									(".$filter_id.",".$value.")";
								$result = $db->Execute($this->sql_query);
								if ($this->debug_filters) echo $this->sql_query."<br>\n";
								if (!$result)
								{
									if ($this->debug_filters) echo $this->sql_query."<br>\n";
									$this->error_message = $this->internal_error_message;
									return false;
								}
							}

						}
					}
				}
				else
				{
					//no terms entered
					return true;
				}
				return true;
			}
			else
			{
				//no filter_info
				$this->error_message = $this->data_missing_error_message;
				return false;
			}
		}
		else
			return false;
	} //end of function add_new_filter

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function clear_ad_filters($db)
	{
		if ($this->classified_user_id)
		{
			$this->sql_query = "select * from ".$this->ad_filter_table." where user_id = ".$this->classified_user_id;
			$filter_result = $db->Execute($this->sql_query);
			if ($this->debug_filters) echo $this->sql_query."<br>\n";
			if (!$filter_result)
			{
				if ($this->debug_filters) echo $this->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($filter_result->RecordCount() > 0)
			{
				while ($show = $filter_result->FetchNextObject())
				{
					$this->sql_query = "delete from ".$this->ad_filter_categories_table." where filter_id = ".$show->FILTER_ID;
					$result = $db->Execute($this->sql_query);
					if ($this->debug_filters) echo $this->sql_query."<br>\n";
					if (!$result)
					{
						if ($this->debug_filters) echo $this->sql_query."<br>\n";
						$this->error_message = $this->internal_error_message;
						return false;
					}
				}
			}

			$this->sql_query = "delete from ".$this->ad_filter_table." where user_id = ".$this->classified_user_id;
			$result = $db->Execute($this->sql_query);
			if ($this->debug_filters) echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug_filters) echo $this->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			return true;
		}
		else
			return false;

	} //end of function clear_ad_filters

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_ad_filters ($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$classified = $this->get_classified_data($db,$classified_id);
			if ($classified->LIVE == 1)
			{
				$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$classified_id;
				$result = $db->Execute($this->sql_query);
				if ($this->debug_filters) echo $this->sql_query."<br>\n";
				if (!$result)
				{
					if ($this->debug_filters) echo $this->sql_query."<br>\n";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show = $result->FetchNextObject();
					$this->sql_query = "select * from ".$this->ad_filter_categories_table." where category_id = ".$show->CATEGORY;
					$filter_category_result = $db->Execute($this->sql_query);
					if ($this->debug_filters) echo $this->sql_query."<br>\n";
					if (!$filter_category_result)
					{
						if ($this->debug_filters) echo $this->sql_query."<br>\n";
						$this->error_message = $this->internal_error_message;
						return false;
					}
					elseif ($filter_category_result->RecordCount() > 0)
					{
						$this->page_id = 29;
						$this->get_text($db);
						$to_search = urldecode($show->SEARCH_TEXT)." ".urldecode($show->TITLE)." ".urldecode($show->DESCRIPTION)
							." ".urldecode($show->OPTIONAL_FIELD_1)
							." ".urldecode($show->OPTIONAL_FIELD_2)
							." ".urldecode($show->OPTIONAL_FIELD_3)
							." ".urldecode($show->OPTIONAL_FIELD_4)
							." ".urldecode($show->OPTIONAL_FIELD_5)
							." ".urldecode($show->OPTIONAL_FIELD_6)
							." ".urldecode($show->OPTIONAL_FIELD_7)
							." ".urldecode($show->OPTIONAL_FIELD_8)
							." ".urldecode($show->OPTIONAL_FIELD_9)
							." ".urldecode($show->OPTIONAL_FIELD_10)
							." ".urldecode($show->OPTIONAL_FIELD_11)
							." ".urldecode($show->OPTIONAL_FIELD_12)
							." ".urldecode($show->OPTIONAL_FIELD_13)
							." ".urldecode($show->OPTIONAL_FIELD_14)
							." ".urldecode($show->OPTIONAL_FIELD_15)
							." ".urldecode($show->OPTIONAL_FIELD_16)
							." ".urldecode($show->OPTIONAL_FIELD_17)
							." ".urldecode($show->OPTIONAL_FIELD_18)
							." ".urldecode($show->OPTIONAL_FIELD_19)
							." ".urldecode($show->OPTIONAL_FIELD_20)
							." ".urldecode($show->LOCATION_CITY)
							." ".urldecode($show->LOCATION_STATE)
							." ".urldecode($show->LOCATION_COUNTRY);
						$to_search = strtoupper($to_search);
						while ($show_filter_id = $filter_category_result->FetchNextObject())
						{
							$this->sql_query = "select * from ".$this->ad_filter_table." where filter_id = ".$show_filter_id->FILTER_ID;
							$filter_term_result = $db->Execute($this->sql_query);
							if ($this->debug_filters) echo $this->sql_query."<br>\n";
							if (!$filter_term_result)
							{
								if ($this->debug_filters) echo $this->sql_query."<br>\n";
								$this->error_message = $this->internal_error_message;
								return false;
							}
							elseif ($filter_term_result->RecordCount() == 1)
							{
								$show_filter_term = $filter_term_result->FetchNextObject();
								$term_list_to_search_with = array();
								if ($this->classified_user_id != $show_filter_term->USER_ID)
								{
									$term_to_search_with = strtoupper(urldecode($show_filter_term->SEARCH_TERMS));

									if (strstr(trim($term_to_search_with)," "))
									{
										//break out filter into separate terms to search with
										$term_list_to_search_with = explode(" ",$term_to_search_with);
										if ($this->debug_filters)
										{
											echo "exploding: ".$term_to_search_with."<bR>\n";
											echo count($term_list_to_search_with)." is the count of term_list_to_search_with<bR>\n";
										}
									}
									elseif (strstr(trim($term_to_search_with),","))
									{
										//break out filter into separate terms to search with
										$term_list_to_search_with = explode(" ",$term_to_search_with);
										if ($this->debug_filters)
										{
											echo "exploding: ".$term_to_search_with."<bR>\n";
											echo count($term_list_to_search_with)." is the count of term_list_to_search_with<bR>\n";
										}
									}										
									else 
									{
										array_push($term_list_to_search_with,$term_to_search_with);
										if ($this->debug_filters)
										{
											echo "pushing: ".$term_to_search_with." onto term_list_to_search_with<bR>\n";
											echo count($term_list_to_search_with)." is the count of term_list_to_search_with<bR>\n";
										}												
									}									
									if (count($term_list_to_search_with) > 0)
									{
										reset($term_list_to_search_with);
										//$this->body .=$term_to_search_with." is term_to_search_with<Br>\n";
										//$this->body .=$to_search." is to_search<br>\n";
										$is_in_all = 1;
										foreach ($term_list_to_search_with as $key => $current_item_to_search_with)
										{
											if ($this->debug_filters)
											{
												echo "<br>searching using this term: ".$current_item_to_search_with."<br>\n";	
											}
											if (strlen(trim($current_item_to_search_with)) > 0)
											{
												if (!strstr($to_search,$current_item_to_search_with))
												{
													$is_in_all = 0;
												}
											}
										}										
										
										if ($is_in_all == 1)
										{										
											$email_address = $this->get_user_email($db,$show_filter_term->USER_ID);
											$user_data = $this->get_user_data($db,$show_filter_term->USER_ID);
											$seller_email_address = $this->get_user_email($db,$classified->SELLER);
											$seller_data = $this->get_user_data($db,$classified->SELLER);
	
											//send a message to this user
											$message["message"] = urldecode($this->messages[1319])."\n\n";
											$message["message"] .= urldecode($this->messages[1320])." ".date("M j, Y - h:i - l",$show_filter_term->DATE_STARTED)."\n";
											$message["message"] .= urldecode($this->messages[1321])." ".$show_filter_term->SEARCH_TERMS."\n\n";
											$message["message"] .= urldecode($this->messages[1322])."\n\n";
											$message["message"] .= $this->configuration_data['classifieds_url']."?a=2&b=".$classified_id."\n\n";
	
											$message["classified_id"] = $classified_id;
											$message["regarding_ad"] = $classified_id;
											$message["subject"] = urldecode($this->messages[1318])."\n\n";
	
											if ($this->configuration_data['email_header_break'])
												$separator = "\n";
											else
												$separator = "\r\n";
	
											if ($seller_data->COMMUNICATION_TYPE == 1)
											{
												$from = "From: ".$seller_email_address.$separator."Reply-to: ".$seller_email_address.$separator;
												$additional = "-f".$seller_email_address;
											}
											else
											{
												$from = "From: ".$this->configuration_data['site_email'].$separator."Reply-to: ".$seller_email_address.$separator;
												$additional = "-f".$this->configuration_data['site_email'];
											}
	
											if ($this->configuration_data['email_configuration'] == 1)
												mail($email_address, $message["subject"], $message["message"], $from,$additional);
											elseif ($this->configuration_data['email_configuration'] == 2)
												mail($email_address, $message["subject"], $message["message"], $from);
											else
												mail($email_address, $message["subject"], $message["message"]);
	
											if ($this->debug_filters)
											{
												echo "sent to: ".$email_address."<bR>\n";	
											}												
											//$additional = "-f ".$this->configuration_data['site_email'];
											//@mail($email_address, $subject, $message, $from,$additional);
											//mail($email_address,$subject,$message,$from,$additional);
											if ($user_data->COMMUNICATION_TYPE != 1)
											{
												//save the communication within the communication queue also
												$this->sql_query = "insert into ".$this->user_communications_table."
													(message_to,message_from_non_user,regarding_ad,date_sent,message)
													values
													(".$show_filter_term->USER_ID.",\"".$classified->SELLER."\",".$classified_id.",".$this->shifted_time($db).",\"".urlencode($message["message"])."\")";
												$save_communication_result = $db->Execute($this->sql_query);
												if ($this->debug_filters) echo $this->sql_query."<br>\n";
												if (!$save_communication_result)
												{
													if ($this->debug_filters) echo $this->sql_query."<br>\n";
													return false;
												}
											}
											break;
											//$this->send_communication($db,$show_filter_term->USER_ID,$message);
										}
									}
								}
							}
						}
					}
				}
			}
			return true;
		}
		else
		{
			//no filter id
			$this->error_message = $this->data_missing_error_message;
			return false;
		}
	} //end of function check_ad_filters

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function user_management_footer($db)
	{
		$this->body .="</td>\n</tr>\n";
		$this->body .="</table>\n";

	} //end of function user_management_footer

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function user_management_header($db,$switch=0)
	{

		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr>\n\t<td width=100% valign=top>\n\t";

	} //end of function user_management_header

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

}
?>