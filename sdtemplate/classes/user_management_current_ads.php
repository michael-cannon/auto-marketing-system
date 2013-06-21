<? //user_management_current_ads.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class User_management_current_ads extends Site
{
	var $debug_remove_ad = 0;
	var $debug_current = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function User_management_current_ads ($db,$language_id, $classified_user_id=0, $page=0, $product_configuration=0)
	{
		$this->Site($db,6,$language_id,$classified_user_id, $product_configuration);

		if(!$page)
			$this->page_result = 1;
		else
			$this->page_result = $page;
	} //end of function User_management
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function list_current_ads ($db)
	{
		$this->page_id = 22;
		$this->get_text($db);
		if ($this->classified_user_id)
		{
			$this->sql_query = "select * from ".$this->classifieds_table." where seller = ".$this->classified_user_id." and live = 1 order by date desc ";
			if($this->page_result != 1)
				$this->sql_query .= "limit ".(($this->page_result-1) * $this->configuration_data['number_of_ads_to_display']).", ".$this->configuration_data['number_of_ads_to_display'];
			else
				$this->sql_query .= "limit ".$this->configuration_data['number_of_ads_to_display'];
			//echo $this->sql_query."<br>";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->debug_message = "no user data returned";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				if($this->is_class_auctions())
					$colspan = 12;
				else
					$colspan = 10;

				if ((strlen(trim($this->configuration_data["sold_image"])) == 0 ) && (($this->is_class_auctions()) || ($this->is_classifieds())))
					$colspan = $colspan -1;	
					
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n\t";
				$this->body .="<tr class=user_management_page_title>\n\t";
				$this->body .="<td valign=top width=100% colspan=".$colspan.">\n\t\t".urldecode($this->messages[634])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=my_current_ads_title>\n\t\t<td colspan=".$colspan.">\n\t\t".urldecode($this->messages[504])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=table_description>\n\t\t<td colspan=".$colspan.">\n\t\t".urldecode($this->messages[505])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=table_column_headers>\n\t\t";
				if($this->is_class_auctions())
					$this->body .="<td nowrap>\n\t\t".urldecode($this->messages[200000])."\n\t\t</td>\n\t\t";
				$this->body .="<td width=100%>\n\t\t".urldecode($this->messages[506])."\n\t\t</td>\n\t\t";
				$this->body .="<td nowrap>\n\t\t".urldecode($this->messages[507])."\n\t\t</td>\n\t\t";
				$this->body .="<td nowrap>\n\t\t".urldecode($this->messages[508])."\n\t\t</td>\n\t\t";
				if($this->is_class_auctions() || $this->is_auctions())
					$this->body .="<td nowrap>\n\t\t".urldecode($this->messages[102865])."\n\t\t</td>\n\t\t";
				$this->body .="<td nowrap>\n\t\t".urldecode($this->messages[715])."\n\t\t</td>\n\t\t";
				$this->body .="<td nowrap>\n\t\t".urldecode($this->messages[783])."\n\t\t</td>\n\t\t";
				$this->body .="<td nowrap>\n\t\t".urldecode($this->messages[784])."\n\t\t</td>\n\t\t";
				$this->body .="<td nowrap>\n\t\t".urldecode($this->messages[785])."\n\t\t</td>\n\t\t";
				$this->body .="<td nowrap>\n\t\t".urldecode($this->messages[833])."\n\t\t</td>\n\t\t";
				$this->body .="<td nowrap>\n\t\t".urldecode($this->messages[3205])."\n\t\t</td>\n\t\t";
				$this->body .="<td nowrap>\n\t\t".urldecode($this->messages[3206])."\n\t\t</td>\n\t\t";
				if (!defined('ALLOW_COPYING_NEW_LISTING'))
					$allow_copying_new_listing = 0;
				else
					$allow_copying_new_listing = ALLOW_COPYING_NEW_LISTING;
				if($allow_copying_new_listing)
					$this->body .="<td nowrap>\n\t\t&nbsp;\n\t\t</td>\n\t\t";
				$this->body .="</tr>\n\t";

				$this->row_count = 0;
				while ($show = $result->FetchNextObject())
				{
					$css_tag = $this->get_row_color(2);
					if (($this->row_count % 2) == 0)
						$css_tag = "result_set_even_rows";
					else
						$css_tag = "result_set_odd_rows";
					$this->body .="<tr class=".$css_tag.">\n\t\t";
					if($this->is_class_auctions())
					{
						if($show->ITEM_TYPE == 1)
							$this->body .="<td nowrap>\n\t\t".urldecode($this->messages[200002])."\n\t\t</td>\n\t\t";
						elseif($show->ITEM_TYPE == 2)
							$this->body .="<td nowrap>\n\t\t".urldecode($this->messages[200001])."\n\t\t</td>\n\t\t";
					}
					$this->body .="<td width=100%>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show->ID." class=".$css_tag.">".urldecode($show->TITLE)." (".$show->ID.")</a>\n\t\t</td>\n\t\t";
					$this->body .="<td nowrap>\n\t\t".date("M j, Y",$show->DATE)."\n\t\t</td>\n\t\t";
					$this->body .="<td nowrap>\n\t\t".date("M j, Y",$show->ENDS)."\n\t\t</td>\n\t\t";

					// Current Bid
					if($this->is_class_auctions() || $this->is_auctions())
					{
						if($show->CURRENT_BID > 0)
						{
							$this->body .="<td nowrap>\n\t\t".$show->CURRENT_BID."\n\t\t</td>\n\t\t";
						}
						elseif($show->ITEM_TYPE == 1)
						{
							$this->body .="<td nowrap>\n\t\t-\n\t\t</td>\n\t\t";
						}
						else
						{
							$this->body .="<td nowrap>\n\t\t".urldecode($this->messages[102864])."\n\t\t</td>\n\t\t";
						}
					}

					if($show->ITEM_TYPE == 1)
					{
						if (strlen(trim($this->configuration_data["sold_image"])) >0)
						{
							if ($show->SOLD_DISPLAYED)
								$this->body .="<td nowrap>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=11&c=".$show->ID." class=action_links>".urldecode($this->messages[717])."</a>\n\t\t</td>\n\t\t";
							else
								$this->body .="<td nowrap>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=11&c=".$show->ID." class=action_links>".urldecode($this->messages[716])."</a>\n\t\t</td>\n\t\t";
						}
						
					}
					elseif($show->ITEM_TYPE == 2)
					{
						$this->body .="<td nowrap>\n\t\t&nbsp;\n\t\t</td>\n\t\t";
					}
					$this->body .="<td nowrap>\n\t\t".$show->FORWARDED."\n\t\t</td>\n\t\t";
					$this->body .="<td nowrap>\n\t\t".$show->RESPONDED."\n\t\t</td>\n\t\t";
					$this->body .="<td nowrap>\n\t\t".$show->VIEWED."\n\t\t</td>\n\t\t";

					//renew/upgrade
					$this->body .="<td nowrap>\n\t\t";
					if($show->ITEM_TYPE == 1)
					{
						$renew_cutoff = ($show->ENDS - ($this->configuration_data['days_to_renew'] * 86400));
						$renew_postcutoff = ($show->ENDS + ($this->configuration_data['days_to_renew'] * 86400));
						if (($this->configuration_data['days_to_renew']) && ($this->shifted_time($db) > $renew_cutoff) && ($this->shifted_time($db) < $renew_postcutoff))
						{
							$this->body .= "<a href=";
							if ($this->configuration_data['use_ssl_in_sell_process'])
								$this->body .= trim($this->configuration_data['classifieds_ssl_url']);
							else
								$this->body .= trim($this->configuration_data['classifieds_file_name']);
							$this->body .= "?a=7&b=".$show->ID."&r=1 class=action_links>".urldecode($this->messages[834])."</a>";
						}
					}

					//check to see if any extra features are in use before displaying upgrade button
					if ($this->debug_current)
					{
						echo $this->configuration_data['use_bolding_feature']." is USE_BOLDING_FEATURE<Br>\n";
						echo $show->BOLDING." is classified BOLDING<Br>\n";
						echo $this->configuration_data['use_better_placement_feature']." is USE_BETTER_PLACEMENT_FEATURE<Br>\n";
						echo $show->BETTER_PLACEMENT." is classified BETTER_PLACEMENT<Br>\n";
						echo $this->configuration_data['use_featured_feature']." is USE_FEATURED_FEATURE<Br>\n";
						echo $show->FEATURED_AD." is classified FEATURED_AD<Br>\n";
						echo $this->configuration_data['use_featured_feature_2']." is USE_FEATURED_FEATURE_2<Br>\n";
						echo $show->FEATURED_AD_2." is classified FEATURED_AD_2<Br>\n";
						echo $this->configuration_data['use_featured_feature_3']." is USE_FEATURED_FEATURE_3<Br>\n";
						echo $show->FEATURED_AD_3." is classified FEATURED_AD_3<Br>\n";
						echo $this->configuration_data['use_featured_feature_4']." is USE_FEATURED_FEATURE_4<Br>\n";
						echo $show->FEATURED_AD_4." is classified FEATURED_AD_4<Br>\n";
						echo $this->configuration_data['use_featured_feature_5']." is USE_FEATURED_FEATURE_5<Br>\n";
						echo $show->FEATURED_AD_5." is classified FEATURED_AD_5<Br>\n";
						echo $this->configuration_data['use_attention_getters']." is USE_ATTENTION_GETTERS<Br>\n";
						echo $show->ATTENTION_GETTER." is classified ATTENTION_GETTER<Br>\n";
					}

					if($this->configuration_data['days_can_upgrade'])
					{
						$upgrade_cutoff = ($show->DATE + ($this->configuration_data['days_can_upgrade'] * 86400));
						if (($this->configuration_data['use_bolding_feature']) ||
						($this->configuration_data['use_better_placement_feature']) ||
						($this->configuration_data['use_featured_feature']) ||
						($this->configuration_data['use_featured_feature_2']) ||
						($this->configuration_data['use_featured_feature_3']) ||
						($this->configuration_data['use_featured_feature_4']) ||
						($this->configuration_data['use_featured_feature_5']) ||
						($this->configuration_data['use_attention_getters']))
						{
							if (($this->configuration_data['days_can_upgrade']) && ($this->shifted_time($db) < $upgrade_cutoff))
							{
								if ((($this->configuration_data['use_bolding_feature']) && ($show->BOLDING == 0)) ||
									(($this->configuration_data['use_better_placement_feature']) && ($show->BETTER_PLACEMENT == 0)) ||
									(($this->configuration_data['use_featured_feature']) && ($show->FEATURED_AD == 0)) ||
									(($this->configuration_data['use_featured_feature_2']) && ($show->FEATURED_AD_2 == 0)) ||
									(($this->configuration_data['use_featured_feature_3']) && ($show->FEATURED_AD_3 == 0)) ||
									(($this->configuration_data['use_featured_feature_4']) && ($show->FEATURED_AD_4 == 0)) ||
									(($this->configuration_data['use_featured_feature_5']) && ($show->FEATURED_AD_5 == 0)) ||
									(($this->configuration_data['use_attention_getters']) && ($show->ATTENTION_GETTER == 0)))
								{
									$this->body .= " <a href=";
									if ($this->configuration_data['use_ssl_in_sell_process'])
										$this->body .= trim($this->configuration_data['classifieds_ssl_url']);
									else
										$this->body .= trim($this->configuration_data['classifieds_file_name']);
									$this->body .= "?a=7&b=".$show->ID."&r=2 class=action_links>".urldecode($this->messages[835])."</a>";
								}
							}
						}
					}
					$this->body .= "</td>\n\t\t";

					if($show->ITEM_TYPE == 2)
					{
						// Edit auction
						if(($this->configuration_data['edit_begin'] == 0) && ($show->CURRENT_BID == 0.00))
							$this->body .="<td nowrap>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&e=".$show->ID." class=action_links>".urldecode($this->messages[509])."</a>\n\t\t</td>\n\t\t";
						else
							$this->body .="<td nowrap>\n\t\t&nbsp;\n\t\t</td>\n\t";

						if($this->configuration_data['admin_only_removes_auctions'] == 0)
							$this->body .="<td nowrap>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=6&c=".$show->ID." class=action_links>".urldecode($this->messages[510])."</a>\n\t\t</td>\n\t";
						else
							$this->body .="<td nowrap>\n\t\t&nbsp;\n\t\t</td>\n\t</tr>\n\t";
					}
					else
					{
						$this->body .="<td nowrap>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&e=".$show->ID." class=action_links>".urldecode($this->messages[509])."</a>\n\t\t</td>\n\t\t";
						$this->body .="<td nowrap>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=6&c=".$show->ID." class=action_links>".urldecode($this->messages[510])."</a>\n\t\t</td>\n\t\t";
					}

					if (!defined('ALLOW_COPYING_NEW_LISTING'))
						$allow_copying_new_listing = 0;
					else
						$allow_copying_new_listing = ALLOW_COPYING_NEW_LISTING;
					if($allow_copying_new_listing)
						$this->body .= "<td nowrap>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=1&copy_id=".$show->ID.">".$this->messages[200176]."</a></td>\n\t";
					$this->body .= "</tr>\n\t";

					$this->row_count++;

					if($this->configuration_data['number_of_ads_to_display'] && ($this->configuration_data['number_of_ads_to_display'] == $this->row_count))
						break;
				} // end of while

				$this->body .="</td>\n\t</tr>\n\t";

				// Get the number of ads
				$this->sql_query = "select count(id) as number_listings from ".$this->classifieds_table." where seller = ".$this->classified_user_id." and live = 1";
				$result = $db->Execute($this->sql_query);
				if(!$result)
					return false;
				else
					$total = $result->FetchRow();

				$total_returned = $total['number_listings'];

				if ($this->configuration_data['number_of_ads_to_display'] < $total_returned)
				{
					//display the link to the next 10
					$number_of_page_results = ceil($total_returned / $this->configuration_data['number_of_ads_to_display']);
					$this->body .="<tr class=\"more_results\">\n\t<td colspan=100%>".urldecode($this->messages[200173])." ";
					if ($number_of_page_results < 10)
					{
						for ($i = 1; $i <= $number_of_page_results; $i++)
						{
							if ($this->page_result == $i)
							{
								$this->body .=" <b>".$i."</b> ";
							}
							else
							{
								$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=1&page=".$i." class=\"browsing_result_page_links\">".$i."</a> ";
							}
						}
					}
					elseif($number_of_page_results < 100)
					{
						$number_of_sections =  ceil($number_of_page_results/10);
						for ($section = 0;$section < $number_of_sections;$section++)
						{
							if (($this->page_result > ($section * 10)) && ($this->page_result <= (($section+1) * 10)))
							{
								//display the individual pages within this section
								for ($page = (($section * 10) + 1);$page <= (($section+1) * 10);$page++)
								{
									if($page == $this->page_result)
									{
										$this->body .=" <b>".$page."</b> ";
										continue;
									}
									if ($page <= $number_of_page_results)
									{
										$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=1&page=".$page;
										$this->body .= " class=\"browsing_result_page_links\">".$page."</a> ";
									}
								}

							}
							else
							{
								//display the link to the section
								$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=1&page=".(($section*10)+1);
								if ($browse_type) $this->body .= "&c=".$browse_type;
								$this->body .= " class=\"browsing_result_page_links\">".(($section*10)+1)."</a>";
							}
							if (($section+1) < $number_of_sections)
							$this->body .= " <font class=\"browsing_result_page_links\">...</font> ";
						}
					}
					else
					{
						$number_of_sections =  ceil($number_of_page_results/100);
						for ($section = 0;$section < $number_of_sections;$section++)
						{
							if (($this->page_result > ($section * 100)) && ($this->page_result <= (($section+1) * 100)))
							{
								//display tens
								for ($page = (($section * 100) + 1);$page <= (($section+1) * 100);$page+=10)
								{

									if (($this->page_result >= $page) && ($this->page_result <= ($page+9)))
									{
										//display ones
										for ($page_link = $page;$page_link <= ($page+9);$page_link++)
										{
											if($page_link == $this->page_result)
											{
												$this->body .=" <b>".$page_link."</b> ";
												continue;
											}
											if ($page_link <= $number_of_page_results)
											{
												$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=1&page=".$page_link;
												$this->body .= " class=\"browsing_result_page_links\">".$page_link."</a> ";
											}
										}

									}else
									{
										if($page == $this->page_result)
										{
											$this->body .=" <b>".$page."</b> ";
											continue;
										}
										if ($page <= $number_of_page_results)
										{
											$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=1&page=".$page;
											$this->body .= " class=\"browsing_result_page_links\">".$page."</a> ";
											if (($section+1) < $number_of_sections)
												$this->body .= " <font class=\"browsing_result_page_links\">...</font> ";
										}
									}
								}

							}
							else
							{
								//display hundreds
								$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=1&page=".(($section*100)+1);
								$this->body .= " class=\"browsing_result_page_links\">".(($section*100)+1)."</a>";
								if (($section+1) < $number_of_sections)
									$this->body .= " <font class=\"browsing_result_page_links\">...</font> ";
							}
						}
					}
					$this->body .="</td>\n</tr>\n";
				}

				$this->body .="<tr class=user_management_home_link>\n\t<td colspan=".$colspan.">\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[512])."</a>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";

			}
			else
			{
				//there are no current ads for this user
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
				$this->body .="<tr class=user_management_page_title><td valign=top width=100%>\n\t\t".urldecode($this->messages[634])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=my_current_ads_title>\n\t\t<td colspan=5>\n\t\t".urldecode($this->messages[504])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=error_message>\n\t<td>\n\t".urldecode($this->messages[511])."</td>\n\t</tr>\n\t";
				$this->body .="<tr class=user_management_home_link>\n\t<td>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[512])."</a>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
			}

			$this->sql_query = "select * from ".$this->classifieds_table." where (live = 0 or renewal_payment_expected = 1 or renewal_payment_expected = 2) and ends > ".$this->shifted_time($db)." and customer_approved = 1 and seller = ".$this->classified_user_id." order by date asc";
			//echo $this->sql_query."<br>";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->debug_message = "no user data returned";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n\t";
				$this->body .="<tr class=my_current_ads_title>\n\t\t<td colspan=3>\n\t\t".urldecode($this->messages[1433])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=table_column_headers>\n\t\t<td>\n\t\t".urldecode($this->messages[506])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[1434])."\n\t\t</td>\n\t";
				$this->body .="<td>\n\t\t&nbsp;\n\t\t</td>\n\t</tr>\n\t";
				while ($show = $result->FetchNextObject())
				{
					if ($this->debug_current)
					{
						echo $show->LIVE." is live<Br>\n";
						echo $show->RENEWAL_PAYMENT_EXPECTED." is RENEWAL_PAYMENT_EXPECTED<Br>\n";
						echo $show->ENDS." is ends<Br>\n";
						echo $show->CUSTOMER_APPROVED." is CUSTOMER_APPROVED<Br>\n";
						echo $show->SELLER." is seller<Br>\n";
					}
					//display the ads awaiting approval
					$css_tag = $this->get_row_color(2);
					if (($this->row_count % 2) == 0)
						$css_tag = "result_set_even_rows";
					else
						$css_tag = "result_set_odd_rows";
					$this->body .="<tr class=".$css_tag.">\n\t\t<td>\n\t\t".urldecode($show->TITLE)."  (".$show->ID.") ";
					if ($show->RENEWAL_LENGTH > 0)
					{
						$this->body .= urldecode($this->messages[834]);
					}
					elseif (($show->FEATURED_AD_UPGRADE) || ($show->BOLDING_UPGRADE) || ($show->ATTENTION_GETTER_UPGRADE) || ($show->BETTER_PLACEMENT_UPGRADE))
					{
						$this->body .= urldecode($this->messages[835]);
					}
					$this->body .= "\n\t\t</td>\n\t\t";
					$this->body .="<td>\n\t\t".urldecode($show->DESCRIPTION)."\n\t\t</td>\n\t";
					if (!$show->DISCOUNT_ID)
					{
						if ($show->RENEWAL_PAYMENT_EXPECTED)
							$this->body .="<td>\n\t\t".$this->configuration_data['precurrency']." ".$this->print_number($show->RENEWAL_TOTAL).
								" ".$this->configuration_data['postcurrency']."\n\t\t</td>";
						else
							$this->body .="<td>\n\t\t".$this->configuration_data['precurrency']." ".$this->print_number($show->TOTAL).
								" ".$this->configuration_data['postcurrency']."\n\t\t</td>";
					}
					else
						$this->body .= "<td>-</td>";
					$this->body .= "\n\t</tr>";
					$this->row_count++;

				}
				$this->body .= "</table>\n\t";
			}


			$this->display_page($db);
			return true;
		}
		else
		{
			//no user id
			$this->error_message = $this->data_missing_error_message;
			return false;
		}
	} //end of function list_current_ads

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function remove_current_ad($db,$classified_info=0)
	{
		if (($classified_info) && ($this->classified_user_id) && ($classified_info["id"] != "") && ($classified_info["id"] != 0))
		{
			$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$classified_info["id"]." and seller = ".$this->classified_user_id;
			$remove_result = $db->Execute($this->sql_query);
			if ($this->debug_remove_ad) echo $this->sql_query." is the query<br>\n";
			if (!$remove_result)
			{
				//echo $this->sql_query."<br>";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($remove_result->RecordCount() == 1)
			{
				$show = $remove_result->FetchNextObject();
				$category_string = $this->get_category_string($db,$show->CATEGORY);
				$this->sql_query = "REPLACE ".$this->classifieds_expired_table."
					(id,seller,title,date,description,category,
					duration,location_state,location_zip,location_country,ends,search_text,ad_ended,reason_ad_ended,viewed,
					transaction_type,bolding,better_placement,featured_ad,subtotal,tax,total,precurrency,price,postcurrency,
					business_type,optional_field_1,optional_field_2,optional_field_3,optional_field_4,optional_field_5,
					optional_field_6,optional_field_7,optional_field_8,optional_field_9,optional_field_10,
					optional_field_11,optional_field_12,optional_field_13,optional_field_14,optional_field_15,
					optional_field_16,optional_field_17,optional_field_18,optional_field_19,optional_field_20,phone,phone2,fax,email,
					url_link_1,url_link_2,url_link_3,item_type";
				if($show->ITEM_TYPE==2)
				{
					$this->sql_query .= ",auction_type,final_fee,final_fee_transaction_number,final_price,high_bidder";
				}
				$this->sql_query .= ")
					VALUES
					(".$show->ID.",
					\"".$show->SELLER."\",
					\"".$show->TITLE."\",
					\"".$show->DATE."\",
					\"".$show->DESCRIPTION."\",
					\"".$category_string."\",
					\"".$show->DURATION."\",
					\"".$show->LOCATION_STATE."\",
					\"".$show->LOCATION_ZIP."\",
					\"".$show->LOCATION_COUNTRY."\",
					\"".$show->ENDS."\",
					\"".urlencode($show->SEARCH_TEXT)."\",
					".$this->shifted_time($db).",
					\"user choice - ".$classified_info["reason_for_removal"]."\",
					".$show->VIEWED.",
					\"".$show->TRANSACTION_TYPE."\",
					\"".$show->BOLDING."\",
					\"".$show->BETTER_PLACEMENT."\",
					\"".$show->FEATURED_AD."\",
					\"".$show->SUBTOTAL."\",
					\"".$show->TAX."\",
					\"".$show->TOTAL."\",
					\"".$show->PRECURRENCY."\",
					\"".$show->PRICE."\",
					\"".$show->POSTCURRENCY."\",
					\"".$show->BUSINESS_TYPE."\",
					\"".$show->OPTIONAL_FIELD_1."\",
					\"".$show->OPTIONAL_FIELD_2."\",
					\"".$show->OPTIONAL_FIELD_3."\",
					\"".$show->OPTIONAL_FIELD_4."\",
					\"".$show->OPTIONAL_FIELD_5."\",
					\"".$show->OPTIONAL_FIELD_6."\",
					\"".$show->OPTIONAL_FIELD_7."\",
					\"".$show->OPTIONAL_FIELD_8."\",
					\"".$show->OPTIONAL_FIELD_9."\",
					\"".$show->OPTIONAL_FIELD_10."\",
					\"".$show->OPTIONAL_FIELD_11."\",
					\"".$show->OPTIONAL_FIELD_12."\",
					\"".$show->OPTIONAL_FIELD_13."\",
					\"".$show->OPTIONAL_FIELD_14."\",
					\"".$show->OPTIONAL_FIELD_15."\",
					\"".$show->OPTIONAL_FIELD_16."\",
					\"".$show->OPTIONAL_FIELD_17."\",
					\"".$show->OPTIONAL_FIELD_18."\",
					\"".$show->OPTIONAL_FIELD_19."\",
					\"".$show->OPTIONAL_FIELD_20."\",
					\"".$show->PHONE."\",
					\"".$show->PHONE2."\",
					\"".$show->FAX."\",
					\"".$show->EMAIL."\",
					\"".$show->URL_LINK_1."\",
					\"".$show->URL_LINK_2."\",
					\"".$show->URL_LINK_3."\",
					\"".$show->ITEM_TYPE."\"";
				if($show->ITEM_TYPE==2)
				{
					$this->sql_query .= ",
					\"".$show->AUCTION_TYPE."\",
					\"".$show->FINAL_FEE."\",
					\"".$show->FINAL_FEE_TRANSACTION_NUMBER."\",
					\"".$show->FINAL_PRICE."\",
					\"".$show->HIGH_BIDDER."\"";
				}
					$this->sql_query .= ")";

				$insert_expired_result = $db->Execute($this->sql_query);
				if ($this->debug_remove_ad) echo $this->sql_query." is the query<br>\n";
				if (!$insert_expired_result)
				{
					if ($this->debug_remove_ad) echo $this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}
				//delete the images
				$this->sql_query = "delete from ".$this->images_table." where classified_id = ".$show->ID;
				$delete_image_result = $db->Execute($this->sql_query);
				if ($this->debug_remove_ad) echo $this->sql_query." is the query<br>\n";
				if (!$delete_image_result)
				{
					if ($this->debug_remove_ad) echo $this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}
				//delete from classifieds table
				$this->sql_query = "delete from ".$this->classifieds_table." where id = ".$show->ID;
				$remove_result = $db->Execute($this->sql_query);
				if ($this->debug_remove_ad) echo $this->sql_query." is the query<br>\n";
				if (!$remove_result)
				{
					if ($this->debug_remove_ad) echo $this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}

				//delete from classifieds extra questions
				$this->sql_query = "delete from ".$this->classified_extra_table." where classified_id = ".$show->ID;
				$remove_extra_result = $db->Execute($this->sql_query);
				if ($this->debug_remove_ad) echo $this->sql_query." is the query<br>\n";
				if (!$remove_extra_result)
				{
					if ($this->debug_remove_ad) echo $this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}

				//delete url images
				//get image urls to
				$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$show->ID;
				$get_url_result = $db->Execute($this->sql_query);
				if ($this->debug_remove_ad) echo $this->sql_query." is the query<br>\n";
				if (!$get_url_result)
				{
					if ($this->debug_remove_ad) echo $this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}
				elseif ($get_url_result->RecordCount())
				{
					while ($show_url = $get_url_result->FetchNextObject())
					{
						if ($show_url->FULL_FILENAME)
							unlink($show_url->FILE_PATH.$show_url->FULL_FILENAME);

						if ($show_url->THUMB_FILENAME)
							unlink($show_url->FILE_PATH.$show_url->THUMB_FILENAME);
					}
					$this->sql_query = "delete from ".$this->images_urls_table." where classified_id = ".$show->ID;
					$delete_url_result = $db->Execute($this->sql_query);
					if ($this->debug_remove_ad) echo $this->sql_query." is the query<br>\n";
					if (!$delete_url_result)
					{
						if ($this->debug_remove_ad) echo $this->sql_query." is the query<br>\n";
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}
				}

				$this->update_category_count($db,$show->CATEGORY);
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
	} //end of function remove_current_ad

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function verify_remove_current_ad($db,$classified_id=0)
	{
		$this->page_id = 36;
		$this->get_text($db);
		if (($classified_id) && ($this->classified_user_id))
		{
			$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$classified_id." and seller = ".$this->classified_user_id;
			$remove_result = $db->Execute($this->sql_query);
			if ($this->debug_remove_ad) echo $this->sql_query."<br>";
			if (!$remove_result)
			{
				//echo "no result<br>";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($remove_result->RecordCount() == 1)
			{
				//show the form to verify removal of this users classifieds ad
				$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=4&b=6 method=post>\n";
				$this->body .= "<input type=hidden name=c[id] value=".$classified_id.">";
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
				$this->body .="<tr class=user_management_page_title><td colspan=2 valign=top width=100%>\n\t\t".urldecode($this->messages[635])."\n\t\t</td>\n\t</tr>\n\t";

				$this->body .="<tr class=ad_removal_title>\n\t\t<td colspan=2>\n\t\t".urldecode($this->messages[475])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=table_description>\n\t\t<td colspan=2>\n\t\t".urldecode($this->messages[476])."\n\t</td>\n</tr>\n";

				$this->body .="<tr class=form_label>\n\t\t<td  valign=top>\n\t".urldecode($this->messages[477])."</td>\n\t\t";
				$this->body .="<td>\n\t\t<textarea name=c[reason_for_removal] cols=30 rows=2></textarea>\n\t\t</td>\n\t</tr>\n\t";

				$this->body .="<tr class=ad_removal_button>\n\t\t<td colspan=2>\n\t\t
					<input type=submit name=z[remove] value=\"".urldecode($this->messages[478])."\" class=ad_removal_button>\n\t</td>\n</tr>\n";
				$this->body .= "<tr class=back_to_current_ad_links>\n\t\t<td colspan=2>\n\t\t
					<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=1>".stripslashes(urldecode($this->messages[480]))."</a>\n\t</td>\n</tr>\n";

				$this->body .="</table>\n";
				$this->body .="</form>\n";
				$this->display_page($db);
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
	} //end of function verify_remove_current_ad

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function verify_remove_success($db)
	{
		$this->page_id = 36;
		$this->get_text($db);
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr class=user_management_page_title><td valign=top width=100%>\n\t\t".urldecode($this->messages[635])."\n\t\t</td>\n\t</tr>\n\t";
		$this->body .="<tr class=ad_removal_title>\n\t\t<td colspan=2>\n\t\t".urldecode($this->messages[475])."\n\t</td>\n</tr>\n";
		$this->body .="<tr class=ad_removal_success_message>\n\t<td>\n\t<br><br>".urldecode($this->messages[479])."\n\t<br>
			<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=1 class=back_to_current_ad_links>".urldecode($this->messages[480])."</a>\n\t</td>\n</tr>\n";
		$this->body .="</table>\n";
		$this->display_page($db);
		return true;
	} //end of function verify_remove_success

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function classified_ad_detail($db,$show)
	{
		if ($show)
		{
			$this->get_ad_configuration($db);
			$this->get_category_configuration($db,$show->CATEGORY, 0);
			//if (!$this->category_configuration['use_site_default'])
			if (!$this->category_configuration->USE_SITE_DEFAULT)
			{
				$this->field_configuration_data = $this->ad_configuration_data;
				$this->field_configuration_data->USE_OPTIONAL_FIELD_1 = $this->configuration_data['use_optional_field_1'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_2 = $this->configuration_data['use_optional_field_2'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_3 = $this->configuration_data['use_optional_field_3'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_4 = $this->configuration_data['use_optional_field_4'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_5 = $this->configuration_data['use_optional_field_5'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_6 = $this->configuration_data['use_optional_field_6'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_7 = $this->configuration_data['use_optional_field_7'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_8 = $this->configuration_data['use_optional_field_8'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_9 = $this->configuration_data['use_optional_field_9'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_10 = $this->configuration_data['use_optional_field_10'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_11 = $this->configuration_data['use_optional_field_11'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_12 = $this->configuration_data['use_optional_field_12'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_13 = $this->configuration_data['use_optional_field_13'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_14 = $this->configuration_data['use_optional_field_14'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_15 = $this->configuration_data['use_optional_field_15'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_16 = $this->configuration_data['use_optional_field_16'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_17 = $this->configuration_data['use_optional_field_17'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_18 = $this->configuration_data['use_optional_field_18'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_19 = $this->configuration_data['use_optional_field_19'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_20 = $this->configuration_data['use_optional_field_20'];
				//echo "using site defaults<br>\n";
			}
			else
			{
				//category specific setting\
				//echo "using category defaults to check<br>\n";
				$this->field_configuration_data = $this->category_configuration;
			}

			if (is_numeric($show->CATEGORY))
			{
				$category_tree = $this->get_category_tree($db,$show->CATEGORY);
				reset ($this->category_tree_array);

				if ($category_tree)
				{
					//category tree
					$this->body .="<tr>\n\t<td colspan=2 class=category_tree>\n\t";
					$this->body .=urldecode($this->messages[484]).": <a href=".$this->configuration_data['classifieds_file_name']."?a=5 class=category_tree>".urldecode($this->messages[1573])."</a> > ";
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
								$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->category_tree_array[$i]["category_id"]." class=category_tree>".$this->category_tree_array[$i]["category_name"]."</a>";
							else
								$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->category_tree_array[$i]["category_id"]." class=category_tree>".$this->category_tree_array[$i]["category_name"]."</a> > ";
						}
					}
					else
					{
						$this->body .=$category_tree;
					}
					$this->body .="\n\t</td>\n</tr>\n";
				}
			}
			else
				$this->body .=$show->CATEGORY;

			//classified title
			$this->body .="<tr class=ad_title><td class=field_labels>\n\t\t".urldecode($this->messages[485])."\n\t\t</td>\n\t";
			$this->body .="\n\t<td class=data_values>".stripslashes(urldecode($show->TITLE))."\n\t</td>\n</tr>\n";

			//classified id
			$this->body .="<tr>\n\t<td width=35% class=field_labels>".urldecode($this->messages[486])."</td>\n\t";
			$this->body .="<td valign=top width=65% class=data_values>\n\t".$show->ID."\n\t</td>\n</tr>\n";

			//viewed count
			$this->body .="<tr>\n\t<td width=35% class=field_labels>".urldecode($this->messages[1102])."</td>\n\t";
			$this->body .="<td valign=top width=65% class=data_values>\n\t".($show->VIEWED + 1)."\n\t</td>\n</tr>\n";

			//start date
			$start_date = date("M d, Y H:i:s", $show->DATE);
			$this->body .="<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[487])." </td>\n\t";
			$this->body .="<td valign=top width=65% class=data_values>\n\t".$start_date."</td>\n\t</tr>\n\t";


			if ($this->field_configuration_data->USE_EMAIL_OPTION_FIELD)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1429])."</td>\n\t";
				$this->body .="<td class=data_values>\n\t".urldecode($show->EMAIL)."</td>\n</tr>\n";
			}

			if (($show->ITEM_TYPE == 1) && $this->field_configuration_data->USE_PRICE_FIELD)
			{
				//classified price
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[489])."</td>\n\t";
				//$this->body .="<td valign=top  class=data_values>\n\t".urldecode($show->PRECURRENCY)." ".stripslashes(urldecode($show->PRICE))." ".urldecode($show->POSTCURRENCY)."</td>\n</tr>\n";
				$this->body .="<td valign=top  class=data_values>";
				if (((strlen(trim(urldecode($show->PRICE))) > 0)
					|| (strlen(trim(urldecode($show->PRECURRENCY))) > 0)
					|| (strlen(trim(urldecode($show->POSTCURRENCY))) > 0)) && ($show->PRICE != 0))
				{
					if (floor($show->PRICE) == $show_classifieds->PRICE)
					{
						$this->body .= stripslashes(urldecode($show->PRECURRENCY)). " ".
							number_format($show->PRICE)." ".
							stripslashes(urldecode($show->POSTCURRENCY));
					}
					else
					{
						$this->body .= stripslashes(urldecode($show->PRECURRENCY)). " ".
							number_format($show->PRICE,2,".",",")." ".
							stripslashes(urldecode($show->POSTCURRENCY));
					}
				}
				else
					$this->body .=	stripslashes(urldecode($show->PRECURRENCY))." - ".stripslashes(urldecode($show->POSTCURRENCY));
				$this->body .="</td>\n\t";
			}

			if ($this->field_configuration_data->USE_PHONE_1_OPTION_FIELD)
			{
				//classified price
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1430])."</td>\n\t";
				$this->body .="<td  class=data_values>\n\t".stripslashes(urldecode($show->PHONE))."</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_PHONE_2_OPTION_FIELD)
			{
				//classified price
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1431])."</td>\n\t";
				$this->body .="<td  class=data_values>\n\t".stripslashes(urldecode($show->PHONE2))."</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_FAX_FIELD_OPTION)
			{
				//classified price
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1432])."</td>\n\t";
				$this->body .="<td  class=data_values>\n\t".stripslashes(urldecode($show->FAX))."</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_CITY_FIELD)
			{
				//classified price
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1943])."</td>\n\t";
				$this->body .="<td  class=data_values>\n\t".stripslashes(urldecode($show->LOCATION_CITY))."</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_STATE_FIELD)
			{
				//classified price
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1944])."</td>\n\t";
				$this->body .="<td  class=data_values>\n\t".stripslashes(urldecode($show->LOCATION_STATE))."</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_COUNTRY_FIELD)
			{
				//classified price
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1945])."</td>\n\t";
				$this->body .="<td  class=data_values>\n\t".stripslashes(urldecode($show->LOCATION_COUNTRY))."</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_ZIP_FIELD)
			{
				//classified price
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1946])."</td>\n\t";
				$this->body .="<td  class=data_values>\n\t".stripslashes(urldecode($show->LOCATION_ZIP))."</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_URL_LINK_1)
			{
				//classified price
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[2449])."</td>\n\t";
				$this->body .="<td  class=data_values>\n\t".stripslashes($show->URL_LINK_1)."</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_URL_LINK_2)
			{
				//classified price
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[2450])."</td>\n\t";
				$this->body .="<td  class=data_values>\n\t".stripslashes($show->URL_LINK_2)."</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_URL_LINK_3)
			{
				//classified price
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[2451])."</td>\n\t";
				$this->body .="<td  class=data_values>\n\t".stripslashes($show->URL_LINK_3)."</td>\n</tr>\n";
			}

			//location
			//$this->body .="<tr>\n\t\t<td width=35% class=field_labels>".urldecode($this->messages[488])." </td>\n\t";
			//$this->body .="<td width=65% class=data_values>\n\t".urldecode($show->LOCATION_COUNTRY)." ".urldecode($show->LOCATION_STATE)." ".$show->LOCATION_ZIP."</td>\n\t</tr>\n\t";

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_1)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1104])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_1)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_2)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1105])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_2)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_3)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1106])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_3)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_4)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1107])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_4)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_5)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1108])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_5)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_6)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1109])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_6)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_7)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1110])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_7)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_8)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1111])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_8)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_9)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1112])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_9)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_10)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1113])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_10)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_11)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1806])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_11)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_12)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1807])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_12)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_13)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1808])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_13)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_14)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1809])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_14)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_15)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1810])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_15)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_16)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1811])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_16)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_17)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1812])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_17)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_18)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1814])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_18)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_19)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1815])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_19)."</td>\n\t</tr>\n";
			}
			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_20)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1816])."</td>\n\t";
				$this->body .="<td class=data_values>".urldecode($show->OPTIONAL_FIELD_20)."</td>\n\t</tr>\n";
			}


			//get extra questions
			$this->get_ads_extra_values($db,$show->ID);

			$this->get_ads_extra_checkboxes($db,$show->ID);

			//mapping address location
			if ($this->field_configuration_data->USE_MAPPING_ADDRESS_FIELD)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1681])."</td>";
				$this->body .="<td class=data_values>\n\t".urldecode($show->MAPPING_ADDRESS)."</td>\n</tr>\n";
			}

			//mapping city location
			if ($this->field_configuration_data->USE_MAPPING_CITY_FIELD)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1682])."</td>";
				$this->body .="<td class=data_values>\n\t".urldecode($show->MAPPING_CITY)."</td>\n</tr>\n";
			}

			//mapping state
			if ($this->field_configuration_data->USE_MAPPING_STATE_FIELD)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1683])."</td>\n\t";
				$this->body .="<td  class=data_values>\n\t".urldecode($show->MAPPING_STATE)."</td>\n</tr>\n";
			}

			//mapping country
			if ($this->field_configuration_data->USE_MAPPING_COUNTRY_FIELD)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1684])."</td>\n\t";
				$this->body .="<td  class=data_values>\n\t".urldecode($show->MAPPING_COUNTRY)."</td>\n</tr>\n";
			}

			//mapping zip location
			if ($this->field_configuration_data->USE_MAPPING_ZIP_FIELD)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1685])."</td>";
				$this->body .="<td  class=data_values>\n\t".urldecode($show->MAPPING_ZIP)."</td>\n</tr>\n";
			}

			// PayPal ID
			if ($show->PAYPAL_ID != "")
			{
			  $this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[3279])."</td>";
			  $this->body .="\n\t<td class=data_values>\n\t" .$show->PAYPAL_ID. "<td>\n</tr>\n";
			}

			//description
			$this->body .="<tr>\n\t\t<td class=ad_description_label>".urldecode($this->messages[490])."</td>";
			$this->body .= "<td class=ad_description>".stripslashes(urldecode($show->DESCRIPTION))."</td>\n\t</tr>\n\t";
			return true;

		}
		else
		{
			return false;
		}

	} //end of function classified_ad_detail

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function edit_classified_ad_home($db,$classified_id=0)
	{
		$this->page_id = 31;
		$this->get_text($db);
		if ($classified_id)
		{

			$this->get_ad_configuration($db);
			$show = $this->get_classified_data($db,$classified_id);
			if (($show->SELLER == $this->classified_user_id) || ($this->classified_user_id == 1))
			{
				if ($show)
				{
					//show the form to begin editing this classified ad
					$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
					$this->body .="<tr class=user_management_page_title><td colspan=2 valign=top width=100%>\n\t\t".urldecode($this->messages[632])."\n\t\t</td>\n\t</tr>\n\t";

					$this->body .="<tr class=edit_ad_title>\n\t<td colspan=2 >\n\t".urldecode($this->messages[482])."\n\t</td>\n</tr>\n";
					$this->body .="<tr class=page_description>\n\t<td colspan=2>\n\t".urldecode($this->messages[483])."\n\t</td>\n</tr>\n";

					$this->classified_ad_detail($db,$show);

					$this->body .="<tr>\n\t<td colspan=2>\n\t";
					$this->body .=$this->current_display_ad_images($db,$classified_id);
					$this->body .="</td>\n</tr>\n";

					$this->body .="<tr class=edit_ad_links>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=1&e=".$classified_id." class=edit_ad_links>".urldecode($this->messages[491])."</a>\n\t</td>\n</tr>\n";
					if ($this->ad_configuration_data->MAXIMUM_PHOTOS > 0)
						$this->body .="<tr class=edit_ad_links>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=2&e=".$classified_id." class=edit_ad_links>".urldecode($this->messages[492])."</a>\n\t</td>\n</tr>\n";
					//edit category
					if (($this->check_no_category_specific_plan($db)) || ($this->classified_user_id == 1))
						$this->body .="<tr class=edit_ad_links>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=3&e=".$classified_id." class=edit_ad_links>".urldecode($this->messages[493])."</a>\n\t</td>\n</tr>\n";
					if ($this->classified_user_id != 1)
						$this->body .="<tr class=back_to_my_currents_ads_link>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=1 class=back_to_my_currents_ads_link>".urldecode($this->messages[494])."</a>\n\t</td>\n</tr>\n";
					$this->body .="</table>\n\t";
					$this->display_page($db);
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
				$this->body .="<tr class=user_management_page_title><td valign=top  colspan=2 width=100%>\n\t\t".urldecode($this->messages[634])."\n\t\t</td>\n\t</tr>\n\t";

				$this->body .="<tr class=edit_ad_links>\n\t<td colspan=2 >\n\t".urldecode($this->messages[401])."\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
				$this->display_page($db);
				return true;
			}

		}
		else
		{
			return false;
		}

	} //end of function edit_classified_ad_home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%.

	function edit_classified_ad_detail($db,$classified_id=0)
	{
		$this->page_id = 32;
		$this->get_text($db);
		if ($classified_id)
		{
			$show = $this->get_classified_data($db,$classified_id);
			if (($show->SELLER == $this->classified_user_id) || ($this->classified_user_id == 1))
			{
				if ($show)
				{
					//show the form to begin editing this classified ad details
					$this->body .="<form name=edit_ad_details_form action=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=1&e=".$classified_id." method=post ";
					if ($this->configuration_data['use_rte'])
						$this->body .= "onsubmit=\"return submitForm();\"";
					$this->body .= ">\n";
					$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
					$this->body .="<tr class=section_title><td colspan=2 valign=top width=100%>\n\t\t".urldecode($this->messages[631])."\n\t\t</td>\n\t</tr>\n\t";
					$this->body .="<tr class=user_management_page_title>\n\t<td colspan=2 >\n\t".urldecode($this->messages[466])."\n\t</td>\n</tr>\n";
					$this->body .="<tr class=page_description>\n\t<td colspan=2>\n\t".urldecode($this->messages[467])."\n\t</td>\n</tr>\n";

					$this->classified_ad_detail_form($db,$show);

					$this->body .="<tr class=save_changes_button>\n\t<td colspan=2>\n\t<input type=submit name=submit value=\"".urldecode($this->messages[472])."\" class=save_changes_button>\n\t</td>\n</tr>\n";
					$this->body .="<tr class=edit_ad_home_link>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&e=".$classified_id." class=edit_ad_home_link>
						".urldecode($this->messages[473])."</a>\n\t</td>\n</tr>\n";
					$this->body .="</table>\n\t";
					$this->body .="</form>\n";
					$this->display_page($db);
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
				$this->body .="<tr class=user_management_page_title><td colspan=2 valign=top width=100%>\n\t\t".urldecode($this->messages[466])."\n\t\t</td>\n\t</tr>\n\t";

				$this->body .="<tr class=edit_ad_home_link>\n\t<td colspan=2 >\n\t".urldecode($this->messages[473])."\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
				$this->display_page($db);
				return true;
			}
		}
		else
		{
			return false;
		}

	} //end of function edit_classified_ad_detail

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function classified_ad_detail_form($db,$show=0)
	{
		if ($show)
		{
			$this->get_category_configuration($db,$show->CATEGORY, 0);
			$this->get_ad_configuration($db);
			//echo $this->category_configuration->USE_SITE_DEFAULT." is use site default<bR>\n";
			if (!$this->category_configuration->USE_SITE_DEFAULT)
			{
				//echo "using site settings - detail form<br>\n";
				$this->field_configuration_data = $this->ad_configuration_data;
				$this->field_configuration_data->USE_OPTIONAL_FIELD_1 = $this->configuration_data['use_optional_field_1'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_2 = $this->configuration_data['use_optional_field_2'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_3 = $this->configuration_data['use_optional_field_3'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_4 = $this->configuration_data['use_optional_field_4'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_5 = $this->configuration_data['use_optional_field_5'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_6 = $this->configuration_data['use_optional_field_6'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_7 = $this->configuration_data['use_optional_field_7'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_8 = $this->configuration_data['use_optional_field_8'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_9 = $this->configuration_data['use_optional_field_9'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_10 = $this->configuration_data['use_optional_field_10'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_11 = $this->configuration_data['use_optional_field_11'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_12 = $this->configuration_data['use_optional_field_12'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_13 = $this->configuration_data['use_optional_field_13'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_14 = $this->configuration_data['use_optional_field_14'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_15 = $this->configuration_data['use_optional_field_15'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_16 = $this->configuration_data['use_optional_field_16'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_17 = $this->configuration_data['use_optional_field_17'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_18 = $this->configuration_data['use_optional_field_18'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_19 = $this->configuration_data['use_optional_field_19'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_20 = $this->configuration_data['use_optional_field_20'];
			}
			else
			{
				//echo "using category settings - detail form<br>\n";
				$this->field_configuration_data = $this->category_configuration;
			}
			//classified title
			$this->body .="<tr class=field_labels><td >\n\t\t".urldecode($this->messages[468])."\n\t\t";
			if ($this->error_variables["classified_title"])
				$this->body .="<br><font class=error_message>".urldecode($this->messages[1136])."</font>";
			$this->body .= "</td>\n\t";
			if ($this->ad_configuration_data->EDITABLE_TITLE_FIELD)
			{
				$this->body .="\n\t<td class=data_fields><input type=text name=d[classified_title] value=\"".stripslashes(urldecode($show->TITLE))."\"  class=data_fields ";
				if ($this->ad_configuration_data->TITLE_LENGTH > 50)
					$this->body .= "size=50 maxlength=".$this->ad_configuration_data->TITLE_LENGTH;
				else
					$this->body .= "size=".$this->ad_configuration_data->TITLE_LENGTH." maxlength=".$this->ad_configuration_data->TITLE_LENGTH;
				$this->body .= ">\n\t";
			}
			else
				$this->body .="\n\t<td class=data_fields>".stripslashes(urldecode($show->TITLE))."<input type=hidden name=d[classified_title] value=\"".stripslashes(urldecode($show->TITLE))."\">\n\t";
			$this->body .= "</td>\n</tr>\n";

			if ($this->field_configuration_data->USE_EMAIL_OPTION_FIELD)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1420]);
				if ($this->error_variables["email_option"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1424])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				if ($this->field_configuration_data->USE_EMAIL_OVERRIDE)
				{
					$this->body .= "<input type=text name=d[email_option] value=\"".urldecode($show->EMAIL)."\" class=data_fields>";
				}
				else
				{
					$this->body .= $show->EMAIL;
					$this->body .= " <input type=hidden name=d[email_option] value=\"".urldecode($show->EMAIL)."\"> ";
				}
				if ($this->field_configuration_data->PUBLICALLY_EXPOSE_EMAIL)
				{
					$this->body .= urldecode($this->messages[1425]);
					$this->body .= urldecode($this->messages[1427])." <input type=radio name=d[expose_email] value=1 ";
					if ($show->EXPOSE_EMAIL == 1)
						$this->body .= "checked";
					$this->body .= " class=data_fields> ".urldecode($this->messages[1428])." <input type=radio name=d[expose_email] value=0 ";
					if ($show->EXPOSE_EMAIL == 0)
						$this->body .= "checked";
					$this->body .= " class=data_fields>";
				}

				$this->body .="</td>\n</tr>\n";
			}


			//$this->body .=$this->ad_configuration_data->USE_PRICE_FIELD." is use price field<br>\n";
			if (($show->ITEM_TYPE!=2)&&$this->field_configuration_data->USE_PRICE_FIELD)
			{
				//classified price
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[470])."</td>\n\t";
				if ($this->ad_configuration_data->EDITABLE_PRICE_FIELD)
				{
					$this->body .="<td  class=data_fields>\n\t<input type=text name=d[precurrency] size=3 class=data_fields value=\"".stripslashes(urldecode($show->PRECURRENCY))."\"> <input type=text name=d[price] class=data_fields value=\"".stripslashes(urldecode($show->PRICE))."\" ";
					if ($this->ad_configuration_data->PRICE_LENGTH > 12)
						$this->body .= "size=12 maxlength=".$this->ad_configuration_data->PRICE_LENGTH;
					else
						$this->body .= "size=".$this->ad_configuration_data->PRICE_LENGTH." maxlength=".$this->ad_configuration_data->PRICE_LENGTH;
					$this->body .= ">\n\t";
					//$this->body .= "<input type=text name=d[postcurrency] size=10 class=data_fields value=\"".urldecode($show->POSTCURRENCY)."\">\n\t";
					
					
					$this->sql_query = "select postcurrency from ".$this->currency_types_table;
					$result = $db->Execute($this->sql_query);
					if(!$result)
					{
						return false;
					}
					if ($result->RecordCount() > 1)
					{
						$this->body .= "<select name=d[postcurrency]>\n\t";
						while($currencies = $result->FetchRow())
						{
							$this->body .= "<option value=".$currencies['postcurrency'];
							if($currencies['postcurrency'] === $show->POSTCURRENCY)
								$this->body .= " selected";
							$this->body .= ">".urldecode($currencies['postcurrency'])."</option>\n";
						}
						$this->body .= "</select>";
					}
					elseif ($result->RecordCount() == 1)
					{
						$currencies = $result->FetchRow();
						$this->body .="<input type=hidden name=d[precurrency] value=\"".urldecode($currencies['postcurrency'])."\">".urldecode($show->POSTCURRENCY)."\n\t";
						
					}
					else 
					{
						$this->body .="<input type=hidden name=d[precurrency] value=\"\">\n\t";
						
					}
				}
				else
				{
					$this->body .="<td  class=data_fields>\n\t".stripslashes(urldecode($show->PRECURRENCY))." ".stripslashes(urldecode($show->PRICE))." ".stripslashes(urldecode($show->POSTCURRENCY))."\n\t";
					$this->body .="<input type=hidden name=d[precurrency] value=\"".urldecode($show->PRECURRENCY)."\"> <input type=hidden name=d[price] value=\"".stripslashes(urldecode($show->PRICE))."\"> <input type=hidden name=d[postcurrency] value=\"".urldecode($show->POSTCURRENCY)."\">\n\t";
				}
				$this->body .="</td>\n</tr>\n";
			}
			else
			{
				$this->body .="<input type=hidden name=d[precurrency] value=\"".urldecode($show->PRECURRENCY)."\"> <input type=hidden name=d[price] value=\"".stripslashes(urldecode($show->PRICE))."\"> <input type=hidden name=d[postcurrency] value=\"".urldecode($show->POSTCURRENCY)."\">\n\t";
			}

			if ($this->field_configuration_data->USE_PHONE_1_OPTION_FIELD)
			{
				//classified price
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1421])."</td>\n\t";
				if ($this->ad_configuration_data->ALLOW_PHONE_1_OVERRIDE)
				{
					$this->body .="<td  class=data_fields>\n\t<input type=text name=d[phone] class=data_fields value=\"".stripslashes(urldecode($show->PHONE))."\" ";
					if ($this->ad_configuration_data->PHONE_1_LENGTH > 10)
						$this->body .= "size=10 maxlength=".$this->ad_configuration_data->PHONE_1_LENGTH;
					else
						$this->body .= "size=".$this->ad_configuration_data->PHONE_1_LENGTH." maxlength=".$this->ad_configuration_data->PHONE_1_LENGTH;
					$this->body .= ">\n\t";
				}
				else
					$this->body .="<td  class=data_fields>\n\t".stripslashes(urldecode($show->PHONE))."<input type=hidden name=d[phone] value=\"".stripslashes(urldecode($show->PHONE))."\">\n\t";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_PHONE_2_OPTION_FIELD)
			{
				//classified price
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1422])."</td>\n\t";
				if ($this->ad_configuration_data->ALLOW_PHONE_2_OVERRIDE)
				{
					$this->body .="<td  class=data_fields>\n\t<input type=text name=d[phone2] class=data_fields value=\"".stripslashes(urldecode($show->PHONE2))."\" ";
					if ($this->ad_configuration_data->PHONE_2_LENGTH > 10)
						$this->body .= "size=10 maxlength=".$this->ad_configuration_data->PHONE_2_LENGTH;
					else
						$this->body .= "size=".$this->ad_configuration_data->PHONE_2_LENGTH." maxlength=".$this->ad_configuration_data->PHONE_2_LENGTH;
					$this->body .= ">\n\t";
				}
				else
					$this->body .="<td  class=data_fields>\n\t".stripslashes(urldecode($show->PHONE2))."<input type=hidden name=d[phone2] value=\"".stripslashes(urldecode($show->PHONE2))."\">\n\t";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_FAX_FIELD_OPTION)
			{
				//classified price
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1423])."</td>\n\t";
				if ($this->ad_configuration_data->ALLOW_FAX_OVERRIDE)
				{
					$this->body .="<td  class=data_fields>\n\t<input type=text name=d[fax] class=data_fields value=\"".stripslashes(urldecode($show->FAX))."\" ";
					if ($this->ad_configuration_data->FAX_LENGTH > 10)
						$this->body .= "size=10 maxlength=".$this->ad_configuration_data->FAX_LENGTH;
					else
						$this->body .= "size=".$this->ad_configuration_data->FAX_LENGTH." maxlength=".$this->ad_configuration_data->FAX_LENGTH;
					$this->body .= ">\n\t";
				}
				else
					$this->body .="<td  class=data_fields>\n\t".stripslashes(urldecode($show->FAX))."<input type=hidden name=d[fax] value=\"".stripslashes(urldecode($show->FAX))."\">\n\t";
				$this->body .="</td>\n</tr>\n";
			}

			//location
			if ($this->field_configuration_data->USE_CITY_FIELD)
			{
				$this->body .="<tr class=field_labels>\n\t\t<td  width=25%>".urldecode($this->messages[1133])."\n\t";
				$this->body .= "</td>\n\t";
				$this->body .="<td width=75% class=data_fields>\n\t";
				if ($this->ad_configuration_data->EDITABLE_CITY_FIELD)
				{
					$this->body .="<input type=text name=d[city] value=\"".stripslashes(urldecode($show->LOCATION_CITY))."\" class=data_fields ";
					if ($this->ad_configuration_data->CITY_LENGTH > 20)
						$this->body .= "size=20 maxlength=".$this->ad_configuration_data->CITY_LENGTH;
					else
						$this->body .= "size=".$this->ad_configuration_data->CITY_LENGTH." maxlength=".$this->ad_configuration_data->CITY_LENGTH;
					$this->body .= ">";
				}
				else
					$this->body .= stripslashes(urldecode($show->LOCATION_CITY))."<input type=hidden name=d[city] value=\"".stripslashes(urldecode($show->LOCATION_CITY))."\">";
				$this->body .= "</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_STATE_FIELD)
			{
				$this->body .="<tr class=field_labels>\n\t\t<td  width=25%>".urldecode($this->messages[1132]);
				if ($this->error_variables["state"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1206])."</font>";
				$this->body .= "</td>\n\t";
				$this->body .="<td width=75% class=data_fields>\n\t";
				if ($this->ad_configuration_data->EDITABLE_STATE_FIELD)
				{
					$this->sql_query = "SELECT * FROM ".$this->states_table." order by display_order, name";
					$result = $db->Execute($this->sql_query);
					if (!$result)
						return false;
					else
					{
						$this->body .="<select name=d[state] class=data_fields>\n\t\t";
						$this->body .="<option value=none>".urldecode($this->messages[1206])."</option>\n\t\t";
						while ($show_state = $result->FetchNextObject()) {
							//spit out the state list
							$this->body .="<option value=\"".$show_state->ABBREVIATION."\"";
							if ((urldecode($show->LOCATION_STATE) == $show_state->ABBREVIATION) || (urldecode(trim($show->LOCATION_STATE)) == trim($show_state->ABBREVIATION)))
							$this->body .="selected";
							$this->body .=">".$show_state->NAME."\n\t\t";
						}

						$this->body .="</select>\n\t";
					}
				}
				else
					$this->body .= $show->LOCATION_STATE."<input type=hidden name=d[state] value=\"".$show->LOCATION_STATE."\">";
				$this->body .="</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_ZIP_FIELD)
			{
				$this->body .="<tr class=field_labels>\n\t\t<td  width=25%>".urldecode($this->messages[1134]);
				if ($this->error_variables["zip_code"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1947])."</font>";
				$this->body .= "</td>\n\t";
				$this->body .="<td width=75% class=data_fields>\n\t";
				if ($this->ad_configuration_data->EDITABLE_ZIP_FIELD)
				{
					$this->body .="<input type=text name=d[zip_code] value=\"".stripslashes(urldecode($show->LOCATION_ZIP))."\" class=data_fields ";
					if ($this->ad_configuration_data->ZIP_LENGTH > 10)
						$this->body .= "size=10 maxlength=".$this->ad_configuration_data->ZIP_LENGTH;
					else
						$this->body .= "size=".$this->ad_configuration_data->ZIP_LENGTH." maxlength=".$this->ad_configuration_data->ZIP_LENGTH;
					$this->body .= "></td>\n\t</tr>\n\t";
				}
				else
					$this->body .= stripslashes(urldecode($show->LOCATION_ZIP))."<input type=hidden name=d[zip_code] value=\"".stripslashes(urldecode($show->LOCATION_ZIP))."\"></td>\n\t</tr>\n\t";
				$this->body .="</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_COUNTRY_FIELD)
			{
				$this->body .="<tr class=field_labels>\n\t\t<td  width=25%>".urldecode($this->messages[469]);
				if ($this->error_variables["country"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1207])."</font>";
				$this->body .= "</td>\n\t";
				$this->body .="<td width=75% class=data_fields>\n\t";
				if ($this->ad_configuration_data->EDITABLE_COUNTRY_FIELD)
				{
					$this->sql_query = "SELECT * FROM ".$this->countries_table." order by display_order, name";
					$result = $db->Execute($this->sql_query);
					if (!$result)
						return false;
					else
					{
						$this->body .="<select name=d[country] class=data_fields>\n\t\t";
						$this->body .="<option value=none>".urldecode($this->messages[1207])."</option>\n\t\t";
						while ($show_country = $result->FetchNextObject()) {
							//spit out the country list
							$this->body .="<option ";
							if ((urldecode(trim($show->LOCATION_COUNTRY)) == trim($show_country->ABBREVIATION)) || (urldecode(trim($show->LOCATION_COUNTRY)) == trim($show_country->NAME)))
							$this->body .="selected";
							$this->body .=">".$show_country->NAME."\n\t\t";
						}

						$this->body .="</select>\n\t";
					}
				}
				else
					$this->body .= $show->LOCATION_COUNTRY."<input type=hidden name=d[country] value=\"".$show->LOCATION_COUNTRY."\">";
				$this->body .="</td>\n\t</tr>\n\t";
			}

			if ($this->field_configuration_data->USE_URL_LINK_1)
			{
				$this->body .="<tr class=field_labels>\n\t<td>".urldecode($this->messages[2443]);
				if ($this->error_variables["url_link_1"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[2446])."</font>";
				$this->body .="<td class=data_fields>\n\t";
				if ($this->ad_configuration_data->EDITABLE_URL_LINK_1)
				{
					$this->body .= "<input type=text name=d[url_link_1] ";
					if ($this->ad_configuration_data->URL_LINK_1_LENGTH > 30)
						$this->body .= "size=30 maxlength=".$this->ad_configuration_data->URL_LINK_1_LENGTH;
					else
						$this->body .= "size=".$this->ad_configuration_data->URL_LINK_1_LENGTH." maxlength=".$this->ad_configuration_data->URL_LINK_1_LENGTH;
					$this->body .= " value=\"".$show->URL_LINK_1."\" class=data_fields>\n\t";
				}
				else
					$this->body .= $show->URL_LINK_1."<input type=hidden name=d[url_link_1] value=\"".$show->URL_LINK_1."\">";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_URL_LINK_2)
			{
				$this->body .="<tr class=field_labels>\n\t<td>".urldecode($this->messages[2444]);
				if ($this->error_variables["url_link_2"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[2447])."</font>";
				$this->body .="<td class=data_fields>\n\t";
				if ($this->ad_configuration_data->EDITABLE_URL_LINK_2)
				{
					$this->body .= "<input type=text name=d[url_link_2] ";
					if ($this->ad_configuration_data->URL_LINK_2_LENGTH > 30)
						$this->body .= "size=30 maxlength=".$this->ad_configuration_data->URL_LINK_2_LENGTH;
					else
						$this->body .= "size=".$this->ad_configuration_data->URL_LINK_2_LENGTH." maxlength=".$this->ad_configuration_data->URL_LINK_2_LENGTH;
					$this->body .= " value=\"".$show->URL_LINK_2."\" class=data_fields>\n\t";
				}
				else
					$this->body .= $show->URL_LINK_2."<input type=hidden name=d[url_link_2] value=\"".$show->URL_LINK_2."\">";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_URL_LINK_3)
			{
				$this->body .="<tr class=field_labels>\n\t<td>".urldecode($this->messages[2445]);
				if ($this->error_variables["url_link_3"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[2448])."</font>";
				$this->body .="<td class=data_fields>\n\t";
				if ($this->ad_configuration_data->EDITABLE_URL_LINK_3)
				{
					$this->body .= "<input type=text name=d[url_link_3] ";
					if ($this->ad_configuration_data->URL_LINK_3_LENGTH > 30)
						$this->body .= "size=30 maxlength=".$this->ad_configuration_data->URL_LINK_3_LENGTH;
					else
						$this->body .= "size=".$this->ad_configuration_data->URL_LINK_3_LENGTH." maxlength=".$this->ad_configuration_data->URL_LINK_3_LENGTH;
					$this->body .= " value=\"".$show->URL_LINK_3."\" class=data_fields>\n\t";
				}
				else
					$this->body .= $show->URL_LINK_3."<input type=hidden name=d[url_link_3] value=\"".$show->URL_LINK_3."\">";
				$this->body .="</td>\n</tr>\n";
			}
			
			if (($show->ITEM_TYPE ==2) && ($this->configuration_data->PAYMENT_TYPES_USE))
			{
				$this->body .= "<tr>\n\t<td class=field_labels>".urldecode($this->messages[103115]);
				$this->body .= "</td>\n\t";
				$this->body .= "<td valign=top class=data_fields>";

				$this->body .="<table cellpadding=1 cellspacing=1 border=1 width=100%>\n";
				$this->sql_query = "SELECT * FROM ".$this->auction_payment_types_table." order by display_order";
				$payment_result = $db->Execute($this->sql_query);
				if (!$payment_result)
				{
					$this->error_message = urldecode($this->messages[100057]);
					return false;
				}
				elseif ($payment_result->RecordCount() > 0)
				{
					$this->body .="<tr>\n\t<td colspan=1><table cellpadding=2 cellspacing=1 border=0 width=100%><tr>";
					$count = 0;
					while($show_payment = $payment_result->FetchNextObject())
					{
						if(!is_array($payment_options))
						{
							$payment_options = explode("||",$show_payment->PAYMENT_OPTIONS);
							$payments = explode("||", $show->PAYMENT_OPTIONS);
						}

						if($count >= 3)
						{
							$count = $count % 3;
							if(in_array($show_payment->TYPE_NAME, $payments))
							{
								$this->body .= "\n\t</tr>\n\t<tr>\n\t<td class=data_fields>".$show_payment->TYPE_NAME."<input type=checkbox name=d[payment_options_from_form][] value=\"".$show_payment->TYPE_NAME."\" checked ></td>\n\t";
							}
							else
							{
								$this->body .="\n\t</tr>\n\t<tr>\n\t<td class=data_fields>".$show_payment->TYPE_NAME."<input type=checkbox name=d[payment_options_from_form][] value=\"".$show_payment->TYPE_NAME."\" ></td>\n\t";
							}
						}
						else
						{
							if(in_array($show_payment->TYPE_NAME,$payments))
							{
								$this->body .="<td class=data_fields>".$show_payment->TYPE_NAME."<input type=checkbox name=d[payment_options_from_form][] value=\"".$show_payment->TYPE_NAME."\" checked ></td>\n\t";
							}
							else
							{
								$this->body .="<td class=data_fields>".$show_payment->TYPE_NAME."<input type=checkbox name=d[payment_options_from_form][] value=\"".$show_payment->TYPE_NAME."\" ></td>\n\t";
							}
						}
						$count++;
					}
					$this->body .= "</tr></table></td></tr>";
				}
				else
				{
					$this->body .=" <tr>\n\t<td colspan = 1> No Choices to Display <input type=hidden name=d[payment_type_count] value=\"".$payment_result->RecordCount()."\"></td>\n\t</tr>";
				}
				$this->body .= "</tr></table>\n";
			}
			
			if ($this->debug_current)
			{
				echo $this->field_configuration_data->USE_OPTIONAL_FIELD_1." is field_configuration_data->USE_OPTIONAL_FIELD_1<br>\n";
				echo $this->ad_configuration_data->OPTIONAL_1_FIELD_EDITABLE." is ad_configuration_data->OPTIONAL_1_FIELD_EDITABLE<br>\n";
				echo $this->ad_configuration_data->OPTIONAL_1_FIELD_TYPE." is ad_configuration_data->OPTIONAL_1_FIELD_TYPE<br>\n";
				echo $this->configuration_data['optional_1_filter_association']." is configuration_data->OPTIONAL_1_FILTER_ASSOCIATION<br>\n";
				echo $show->OPTIONAL_FIELD_1." is show->OPTIONAL_FIELD_1<BR>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_1)
			{
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1114]);
				if ($this->error_variables["optional_field_1"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1137])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_1_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_1_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_1_FIELD_TYPE)
					{
						$this->body .= "<input class=data_fields id=optional_field_1 type=text name=d[optional_field_1] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_1))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_1_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_1_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_1_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_1_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_1_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select id=optional_field_1 name=d[optional_field_1] class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_1) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input id=optional_field_1 class=data_fields type=text name=d[optional_field_1] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_1))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_1_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_1_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[1135])." <input class=data_fields type=text name=d[optional_field_1_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_1));
						$this->body .= "\">\n\t";
					}
				}
				else
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_1))."<input type=hidden name=d[optional_field_1] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_1))."\">";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_2)
			{
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1115]);
				if ($this->error_variables["optional_field_2"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1138])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				//echo $this->ad_configuration_data->OPTIONAL_2_FIELD_EDITABLE." is the editable 2<br>";
				//echo $this->configuration_data['optional_2_filter_association']." is the association<br>";
				if (($this->ad_configuration_data->OPTIONAL_2_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_2_filter_association']))
				{
					//echo $this->ad_configuration_data->OPTIONAL_2_FIELD_TYPE." is the field type<br>";
					if (!$this->ad_configuration_data->OPTIONAL_2_FIELD_TYPE)
					{
						$this->body .= "<input id=optional_field_2 class=data_fields type=text name=d[optional_field_2] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_2))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_2_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_2_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_2_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_2_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_2_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select id=optional_field_2 name=d[optional_field_2] class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_2) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input id=optional_field_2 class=data_fields type=text name=d[optional_field_2] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_2))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_2_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_2_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[1135])." <input class=data_fields type=text name=d[optional_field_2_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_2));
						$this->body .= "\">\n\t";
					}
				}
				else
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_2))."<input type=hidden name=d[optional_field_2] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_2))."\">";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_3)
			{
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1116]);
				if ($this->error_variables["optional_field_3"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1139])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				//echo $this->ad_configuration_data->OPTIONAL_3_FIELD_EDITABLE." is the editable 3<br>";
				//echo $this->configuration_data['optional_3_filter_association']." is the association 3<br>";
				if (($this->ad_configuration_data->OPTIONAL_3_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_3_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_3_FIELD_TYPE)
					{
						$this->body .= "<input id=optional_field_3 class=data_fields type=text name=d[optional_field_3] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_3))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_3_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_3_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_3_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_3_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_3_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select id=optional_field_3 name=d[optional_field_3] class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_3) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input id=optional_field_3 class=data_fields type=text name=d[optional_field_3] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_3))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_3_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_3_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[1135])." <input class=data_fields type=text name=d[optional_field_3_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_3));
						$this->body .= "\">\n\t";
					}
				}
				else
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_3))."<input type=hidden name=d[optional_field_3] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_3))."\">";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_4)
			{
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1117]);
				if ($this->error_variables["optional_field_4"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1140])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_4_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_4_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_4_FIELD_TYPE)
					{
						$this->body .= "<input id=optional_field_3 class=data_fields type=text name=d[optional_field_4] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_4))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_4_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_4_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_4_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_4_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_4_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select id=optional_field_4 name=d[optional_field_4] class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_4) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input id=optional_field_4 class=data_fields type=text name=d[optional_field_4] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_4))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_4_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_4_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[1135])." <input class=data_fields type=text name=d[optional_field_4_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_4));
						$this->body .= "\">\n\t";
					}
				}
				else
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_4))."<input type=hidden name=d[optional_field_4] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_4))."\">";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_5)
			{
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1118]);
				if ($this->error_variables["optional_field_5"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1141])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_5_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_5_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_5_FIELD_TYPE)
					{
						$this->body .= "<input id=optional_field_5 class=data_fields type=text name=d[optional_field_5] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_5))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_5_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_5_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_5_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_5_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_5_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select id=optional_field_5 name=d[optional_field_5] class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_5) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input id=optional_field_5 class=data_fields type=text name=d[optional_field_5] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_5))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_5_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_5_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[1135])." <input class=data_fields type=text name=d[optional_field_5_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_5));
						$this->body .= "\">\n\t";
					}
				}
				else
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_5))."<input type=hidden name=d[optional_field_5] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_5))."\">";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_6)
			{
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1119]);
				if ($this->error_variables["optional_field_6"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1142])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_6_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_6_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_6_FIELD_TYPE)
					{
						$this->body .= "<input id=optional_field_6 class=data_fields type=text name=d[optional_field_6] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_6))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_6_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_6_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_6_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_6_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_6_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select id=optional_field_6 name=d[optional_field_6] class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_6) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input id=optional_field_6 class=data_fields type=text name=d[optional_field_6] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_6))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_6_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_6_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[1135])." <input class=data_fields type=text name=d[optional_field_6_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_6));
						$this->body .= "\">\n\t";
					}
				}
				else
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_6))."<input type=hidden name=d[optional_field_6] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_6))."\">";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_7)
			{
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1120]);
				if ($this->error_variables["optional_field_7"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1144])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_7_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_7_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_7_FIELD_TYPE)
					{
						$this->body .= "<input id=optional_field_7 class=data_fields type=text name=d[optional_field_7] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_7))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_7_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_7_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_7_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_7_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_7_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select id=optional_field_7 name=d[optional_field_7] class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_7) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input id=optional_field_7 class=data_fields type=text name=d[optional_field_7] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_7))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_7_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_7_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[1135])." <input class=data_fields type=text name=d[optional_field_7_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_7));
						$this->body .= "\">\n\t";
					}
				}
				else
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_7))."<input type=hidden name=d[optional_field_7] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_7))."\">";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_8)
			{
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1121]);
				if ($this->error_variables["optional_field_8"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1143])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_8_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_8_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_8_FIELD_TYPE)
					{
						$this->body .= "<input id=optional_field_8 class=data_fields type=text name=d[optional_field_8] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_8))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_8_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_8_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_8_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_8_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_8_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select id=optional_field_8 name=d[optional_field_8] class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_8) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input id=optional_field_8 class=data_fields type=text name=d[optional_field_8] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_8))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_8_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_8_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[1135])." <input class=data_fields type=text name=d[optional_field_8_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_8));
						$this->body .= "\">\n\t";
					}
				}
				else
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_8))."<input type=hidden name=d[optional_field_8] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_8))."\">";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_9)
			{
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1122]);
				if ($this->error_variables["optional_field_9"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1145])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_9_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_9_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_9_FIELD_TYPE)
					{
						$this->body .= "<input id=optional_field_9 class=data_fields type=text name=d[optional_field_9] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_9))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_9_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_9_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_9_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_9_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_9_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select id=optional_field_9 name=d[optional_field_9] class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_9) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input id=optional_field_9 class=data_fields type=text name=d[optional_field_9] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_9))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_9_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_9_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[1135])." <input class=data_fields type=text name=d[optional_field_9_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_9));
						$this->body .= "\">\n\t";
					}
				}
				else
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_9))."<input type=hidden name=d[optional_field_9] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_9))."\">";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_10)
			{
				$this->body .="<tr>\n\t<td  class=field_labels>".urldecode($this->messages[1123]);
				if ($this->error_variables["optional_field_10"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1146])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_10_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_10_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_10_FIELD_TYPE)
					{
						$this->body .= "<input id=optional_field_10 class=data_fields type=text name=d[optional_field_10] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_10))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_10_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_10_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_10_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_10_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_10_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select id=optional_field_10 name=d[optional_field_10] class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_10) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input id=optional_field_10 class=data_fields type=text name=d[optional_field_10] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_10))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_10_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_10_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[1135])." <input class=data_fields type=text name=d[optional_field_10_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_10));
						$this->body .= "\">\n\t";
					}
				}
				else
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_10))."<input type=hidden name=d[optional_field_10] value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_10))."\">";
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_11)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1817]);
				if ($this->error_variables["optional_field_11"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1837])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_11_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_11_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_11_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=d[optional_field_11] id=optional_field_11 class=data_fields value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_11))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_11_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_11_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_11_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_11_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_11_FIELD_TYPE." order by display_order,value";
						//echo $this->sql_query."<br>\n";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=d[optional_field_11] id=optional_field_11 class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_11) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=d[optional_field_11] id=optional_field_11 class=data_fields value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_11))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_11_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_11_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_11 name=d[optional_field_11_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_11));
						$this->body .= "\" class=data_fields>\n\t";
					}
				}
				else
				{
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_11));
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_12)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1818]);
				if ($this->error_variables["optional_field_12"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1838])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_12_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_12_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_12_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=d[optional_field_12] id=optional_field_12 class=data_fields value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_12))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_12_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_12_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_12_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_12_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_12_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						//echo $this->sql_query."<br>\n";
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=d[optional_field_12] id=optional_field_12 class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_12) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=d[optional_field_12] id=optional_field_12 class=data_fields value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_12))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_12_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_12_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_12 name=d[optional_field_12_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_12));
						$this->body .= "\" class=data_fields>\n\t";
					}
				}
				else
				{
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_12));
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_13)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1819]);
				if ($this->error_variables["optional_field_13"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1839])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_13_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_13_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_13_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=d[optional_field_13] id=optional_field_13 class=data_fields value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_13))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_13_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_13_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_13_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_13_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_13_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=d[optional_field_13] id=optional_field_13 class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_13) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=d[optional_field_13] id=optional_field_13 class=data_fields value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_13))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_13_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_13_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_3 name=d[optional_field_13_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_13));
						$this->body .= "\" class=data_fields>\n\t";
					}
				}
				else
				{
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_13));
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_14)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1820]);
				if ($this->error_variables["optional_field_14"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1840])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_14_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_14_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_14_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=d[optional_field_14] id=optional_field_14 class=data_fields value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_14))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_14_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_14_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_14_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_14_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_14_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=d[optional_field_14] id=optional_field_14 class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_14) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=d[optional_field_14] id=optional_field_14 class=data_fields value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_14))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_14_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_14_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_14 name=d[optional_field_14_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_14));
						$this->body .= "\" class=data_fields>\n\t";
					}
				}
				else
				{
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_14));
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_15)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1821]);
				if ($this->error_variables["optional_field_15"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1841])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_15_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_15_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_15_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=d[optional_field_15] id=optional_field_15 class=data_fields value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_15))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_15_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_15_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_15_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_15_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_15_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=d[optional_field_15] id=optional_field_15 class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_15) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=d[optional_field_15] id=optional_field_15 class=data_fields value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_15))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_15_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_15_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_15 name=d[optional_field15_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_15));
						$this->body .= "\" class=data_fields>\n\t";
					}
				}
				else
				{
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_15));
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_16)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1822]);
				if ($this->error_variables["optional_field_16"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1842])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_16_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_16_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_16_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=d[optional_field_16] id=optional_field_16 class=data_fields value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_16))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_16_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_16_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_16_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_16_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_16_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=d[optional_field_16] id=optional_field_16 class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_16) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=d[optional_field_16] id=optional_field_16 class=data_fields value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_16))."\">\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_16_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_16_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_16 name=d[optional_field_16_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_16));
						$this->body .= "\" class=data_fields>\n\t";
					}
				}
				else
				{
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_16));
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_17)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1823]);
				if ($this->error_variables["optional_field_17"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1843])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_17_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_17_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_17_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=d[optional_field_17] id=optional_field_17 class=data_fields value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_17))."\" ";
						if ($this->ad_configuration_data->OPTIONAL_17_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_17_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_17_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_17_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_17_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=d[optional_field_17] id=optional_field_7 class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_17) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=d[optional_field_17] id=optional_field_17 value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_17))."\" class=data_fields>\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_17_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_17_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_17 name=d[optional_field_17_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_17));
						$this->body .= "\" class=data_fields>\n\t";
					}
				}
				else
				{
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_17));
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_18)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1824]);
				if ($this->error_variables["optional_field_18"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1844])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_18_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_18_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_18_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=d[optional_field_18] id=optional_field_18 value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_18))."\" class=data_fields ";
						if ($this->ad_configuration_data->OPTIONAL_18_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_18_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_18_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_18_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_18_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=d[optional_field_18] id=optional_field_18 class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_18) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=d[optional_field_18] id=optional_field_18 value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_18))."\" class=data_fields>\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_18_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_18_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_18 name=d[optional_field_18_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_18));
						$this->body .= "\" class=data_fields>\n\t";
					}
				}
				else
				{
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_18));
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_19)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1825]);
				if ($this->error_variables["optional_field_19"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1845])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_19_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_19_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_19_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=d[optional_field_19] id=optional_field_19 value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_19))."\" class=data_fields ";
						if ($this->ad_configuration_data->OPTIONAL_19_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_19_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_19_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_19_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_19_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=d[optional_field_19] id=optional_field_19 class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_19) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=d[optional_field_19] id=optional_field_19 value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_19))."\" class=data_fields>\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_19_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_19_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_19 name=d[optional_field_19_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_19));
						$this->body .= "\" class=data_fields>\n\t";
					}
				}
				else
				{
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_19));
				}
				$this->body .="</td>\n</tr>\n";
			}

			if ($this->field_configuration_data->USE_OPTIONAL_FIELD_20)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1826]);
				if ($this->error_variables["optional_field_20"])
					$this->body .="<br><font class=error_message>".urldecode($this->messages[1846])."</font>";
				$this->body .="</td>\n\t";
				$this->body .="<td  class=data_fields>\n\t";
				if (($this->ad_configuration_data->OPTIONAL_20_FIELD_EDITABLE)
					&& (!$this->configuration_data['optional_20_filter_association']))
				{
					if (!$this->ad_configuration_data->OPTIONAL_20_FIELD_TYPE)
					{
						$this->body .= "<input type=text name=d[optional_field_20] id=optional_field_20 value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_20))."\" class=data_fields ";
						if ($this->ad_configuration_data->OPTIONAL_20_LENGTH > 30)
							$this->body .= "size=30 maxlength=".$this->ad_configuration_data->OPTIONAL_20_LENGTH;
						else
							$this->body .= "size=".$this->ad_configuration_data->OPTIONAL_20_LENGTH." maxlength=".$this->ad_configuration_data->OPTIONAL_20_LENGTH;
						$this->body .= ">\n\t";
					}
					else
					{
						$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$this->ad_configuration_data->OPTIONAL_20_FIELD_TYPE." order by display_order,value";
						$type_result = $db->Execute($this->sql_query);
						if (!$type_result)
						{
							return false;
						}
						elseif ($type_result->RecordCount() > 0)
						{
							$this->body .= "<select name=d[optional_field_20] id=optional_field_20 class=data_fields>";
							$matched = 0;
							while ($show_dropdown = $type_result->FetchNextObject())
							{
								$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
								if (urldecode($show->OPTIONAL_FIELD_20) == urldecode($show_dropdown->VALUE))
								{
									$this->body .= "selected";
									$matched = 1;
								}
								$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
							}
							$this->body .= "</select>";
						}
						else
							//blank text box
							$this->body .= "<input type=text name=d[optional_field_20] id=optional_field_20 value=\"".stripslashes(urldecode($show->OPTIONAL_FIELD_20))."\" class=data_fields>\n\t";
					}
					if (($this->ad_configuration_data->OPTIONAL_20_OTHER_BOX) && ($this->ad_configuration_data->OPTIONAL_20_FIELD_TYPE))
					{
						$this->body .= " ".urldecode($this->messages[133])." <input type=text id=optional_field_20 name=d[optional_field_20_other] value=\"";
						if (!$matched)
							$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_20));
						$this->body .= "\" class=data_fields>\n\t";
					}
				}
				else
				{
					$this->body .= stripslashes(urldecode($show->OPTIONAL_FIELD_20));
				}
				$this->body .="</td>\n</tr>\n";
			}


			if ($this->ad_configuration_data->EDITABLE_CATEGORY_SPECIFIC)
			{
				$this->get_category_questions($db,$show->CATEGORY);
				$this->display_category_questions($db,$show->ID);

				//get and display group questions
				$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
				$group_and_price_result = $db->Execute($this->sql_query);

				if (!$group_and_price_result)
				{
					return false;
				}
				elseif ($group_and_price_result->RecordCount() == 1)
				{
					$show_group = $group_and_price_result->FetchNextObject();
					$this->get_group_questions($db,$show_group->GROUP_ID);
					$this->display_group_questions($db,$show->ID);
				}
			}
			else
			{
				//get extra questions
				$this->get_ads_extra_values($db,$show->ID);
				$this->get_ads_extra_checkboxes($db,$show->ID);
			}

			//mapping fields
			if (($this->field_configuration_data->USE_MAPPING_ADDRESS_FIELD) ||
				($this->field_configuration_data->USE_MAPPING_CITY_FIELD) ||
				($this->field_configuration_data->USE_MAPPING_STATE_FIELD) ||
				($this->field_configuration_data->USE_MAPPING_COUNTRY_FIELD) ||
				($this->field_configuration_data->USE_MAPPING_ZIP_FIELD))
			{
				$this->body .="<tr class=page_description>\n\t<td colspan=2>".urldecode($this->messages[1691])."\n\t</td>\n</tr>\n";
			}

			//mapping address location
			if ($this->field_configuration_data->USE_MAPPING_ADDRESS_FIELD)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1686])."</td>";
				$this->body .="<td class=data_fields>\n\t<input type=text name=d[mapping_address] value=\"".urldecode($show->MAPPING_ADDRESS)."\" class=data_fields>\n\t";
				$this->body .="</td>\n</tr>\n";
			}

			//mapping city location
			if ($this->field_configuration_data->USE_MAPPING_CITY_FIELD)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1687])."</td>";
				$this->body .="<td class=data_fields>\n\t<input type=text name=d[mapping_city] value=\"".urldecode($show->MAPPING_CITY)."\" class=data_fields>\n\t";
				$this->body .="</td>\n</tr>\n";
			}

			//mapping state
			if ($this->field_configuration_data->USE_MAPPING_STATE_FIELD)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1688])."</td>\n\t";
				$this->body .="<td  class=data_fields>\n\t";
				$this->sql_query = "SELECT * FROM ".$this->states_table." order by display_order,name";
				$state_result = $db->Execute($this->sql_query);
				if (!$state_result)
					return false;
				else
				{
					$this->body .="<select name=d[mapping_state] class=data_fields>\n\t\t";
					$this->body .="<option value=none>".urldecode($this->messages[1206])."</option>\n\t\t";
					while ($show_state = $state_result->FetchNextObject()) {
						//spit out the state list
						$this->body .="<option value=\"".$show_state->ABBREVIATION."\"";
						if (urldecode($show->MAPPING_STATE) == $show_state->ABBREVIATION)
						$this->body .="selected";
						$this->body .=">".$show_state->NAME."\n\t\t";
					}

					$this->body .="</select>\n\t";
				}
				$this->body .="</td>\n</tr>\n";
			}

			//mapping country
			if ($this->field_configuration_data->USE_MAPPING_COUNTRY_FIELD)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1689])."</td>\n\t";
				$this->body .="<td  class=data_fields>\n\t";
				$this->sql_query = "SELECT * FROM ".$this->countries_table." order by name";
				$country_result = $db->Execute($this->sql_query);
				if (!$country_result)
					return false;
				else
				{
					$this->body .="<select name=d[mapping_country] class=data_fields>\n\t\t";
					$this->body .="<option value=none>".urldecode($this->messages[1207])."</option>\n\t\t";
					while ($show_country = $country_result->FetchNextObject()) {
						//spit out the country list
						$this->body .="<option ";
						if ((urldecode($show->MAPPING_COUNTRY) == $show_country->ABBREVIATION) || (urldecode($show->MAPPING_COUNTRY) == $show_country->NAME))
							$this->body .="selected";
						$this->body .=">".$show_country->NAME."</option>\n\t\t";
					}

					$this->body .="</select>\n\t";
				}
				$this->body .="</td>\n</tr>\n";
			}

			//mapping zip location
			if ($this->field_configuration_data->USE_MAPPING_ZIP_FIELD)
			{
				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1690])."</td>";
				$this->body .="<td  class=data_fields>\n\t<input type=text name=d[mapping_zip] value=\"".urldecode($show->MAPPING_ZIP)."\" class=data_fields>\n\t";
				$this->body .="</td>\n</tr>\n";
			}

			//description
			//$this->body .="<tr>\n\t\t<td colspan=2 class=field_labels>".urldecode($this->messages[471])."<br>";

			$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[471])."\n\t";
			if (strlen(trim($this->error_variables["description"])) > 0)
				$this->body .="<br><font class=error_message>".urldecode($this->messages[120])."</font>\n\t";
			$this->body .="</td>\n";
			$this->body .="\n\t<td class=data_fields>";


			if ($this->ad_configuration_data->EDITABLE_DESCRIPTION_FIELD)
			{
				if ($this->configuration_data['use_rte'])
				{
					$rte_description = str_replace(chr(13), "", chr(13).urldecode($show->DESCRIPTION));
					//$rte_description = str_replace(chr(34), "", $this->classified_variables["description"]);
					$this->LoadRTE("d[description]", $rte_description, 500, 300, 1);

				}
				else
				{
					$this->body .="\n\t<textarea name=d[description] cols=60 rows=15 class=data_fields ";
					if ($this->ad_configuration_data->TEXTAREA_WRAP)
					{
						$this->body .= "wrap=virtual>";
						$this->body .= eregi_replace('<BR[[:space:]]*/?[[:space:]]*>',"",stripslashes(urldecode($show->DESCRIPTION)));
					}
					else
					{
						$this->body .= "wrap=soft>";
						$this->body .= stripslashes(urldecode($show->DESCRIPTION));
					}
					$this->body .=" </textarea>";
				}
			}
			else
			{
				$this->body .= stripslashes(urldecode($show->DESCRIPTION));
				$this->body .= "<input type=hidden name=d[description] cols=60 rows=15 class=data_fields value=\"".stripslashes(urldecode($show->DESCRIPTION))."\">";
			}
			if($this->ad_configuration_data->EDITABLE_BUY_NOW && $this->ad_configuration_data->USE_BUY_NOW)
			{
				$this->body .= "
					<tr>
						<td class=field_labels>".urldecode($this->messages[3282])."<br>";
				if($this->error_variables["paypal_id"])
					$this->body .= "<font class=error_message>".$this->messages[3283]."</font>";
				$this->body .= "</td>
						<td><input type=text class=place_an_ad_details_data name=b[paypal_id] value='{$show->PAYPAL_ID}'></td>
					</tr>";
			}
			
			//STOREFRONT CODE
			if(file_exists('classes/storefront/store_class.php'))
			{
				include_once('classes/storefront/store_class.php');
				
				$this->sql_query = "select * from ".Store::get('storefront_subscriptions_table')." where user_id = ".$this->classified_user_id;
				$subscriptionResult = $db->Execute($this->sql_query);
				
				$this->sql_query = "select * from ".Store::get('storefront_categories_table')." where user_id = '".$this->classified_user_id."' order by display_order asc";
				$categoryResults = $db->Execute($this->sql_query);
				
				if($subscriptionResult->RecordCount()==1&&$categoryResults->RecordCount())
				{
					$subscriptionInfo = $subscriptionResult->FetchRow();
					$expiresAt = $this->shifted_time($db) + $subscriptionInfo["expiration"];
					if(time()<=$expiresAt)
					{
						if($this->page_id == 32);
							$this->messages[500002] = $this->messages[500007];
						$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[500002])."</td>\n\t";
						$this->body .="<td  class=place_an_ad_details_data>\n\t";
						$this->body .="<select name=d[storefront_category] class=place_an_ad_details_data>\n\t\t";
						while ($showCats = $categoryResults->FetchRow())
						{
							$selected = ($show->STOREFRONT_CATEGORY==$showCats["category_id"]) ? "selected" : "";
							$this->body .="<option value=\"".$showCats["category_id"]."\"";
							$this->body .=" $selected>".stripslashes($showCats["category_name"])."\n\t\t";
						}
	
						$this->body .="</select>\n\t";
						$this->body .="</td>\n</tr>\n";
						
					}
				}
			}
			//STOREFRONT CODE
			
			return true;
		}
		else
		{
			return false;
		}

	} //end of function classified_ads_detail_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_classified_ad_details($db,$classified_id=0)
	{
		//echo "hello from update<br>\n";
		//echo $this->classified_user_id." is the user id<br>\n";
		//echo $classified_id." is the id<br>\n";
		$show = $this->get_classified_data($db,$classified_id);
		if (($show->SELLER == $this->classified_user_id) || ($this->classified_user_id == 1))
		{
			if ($classified_id)
			{
				if ($this->ad_configuration_data->TEXTAREA_WRAP)
					$this->classified_variables["description"] = (urldecode(nl2br($this->classified_variables["description"])));
				else
					$this->classified_variables["description"] = (urldecode($this->classified_variables["description"]));
				$this->sql_query = "update ".$this->classifieds_table." set
					title = \"".urlencode($this->classified_variables["classified_title"])."\",
					optional_field_1 = \"".urlencode($this->classified_variables["optional_field_1"])."\",
					optional_field_2 = \"".urlencode($this->classified_variables["optional_field_2"])."\",
					optional_field_3 = \"".urlencode($this->classified_variables["optional_field_3"])."\",
					optional_field_4 = \"".urlencode($this->classified_variables["optional_field_4"])."\",
					optional_field_5 = \"".urlencode($this->classified_variables["optional_field_5"])."\",
					optional_field_6 = \"".urlencode($this->classified_variables["optional_field_6"])."\",
					optional_field_7 = \"".urlencode($this->classified_variables["optional_field_7"])."\",
					optional_field_8 = \"".urlencode($this->classified_variables["optional_field_8"])."\",
					optional_field_9 = \"".urlencode($this->classified_variables["optional_field_9"])."\",
					optional_field_10 = \"".urlencode($this->classified_variables["optional_field_10"])."\",
					optional_field_11 = \"".urlencode($this->classified_variables["optional_field_11"])."\",
					optional_field_12 = \"".urlencode($this->classified_variables["optional_field_12"])."\",
					optional_field_13 = \"".urlencode($this->classified_variables["optional_field_13"])."\",
					optional_field_14 = \"".urlencode($this->classified_variables["optional_field_14"])."\",
					optional_field_15 = \"".urlencode($this->classified_variables["optional_field_15"])."\",
					optional_field_16 = \"".urlencode($this->classified_variables["optional_field_16"])."\",
					optional_field_17 = \"".urlencode($this->classified_variables["optional_field_17"])."\",
					optional_field_18 = \"".urlencode($this->classified_variables["optional_field_18"])."\",
					optional_field_19 = \"".urlencode($this->classified_variables["optional_field_19"])."\",
					optional_field_20 = \"".urlencode($this->classified_variables["optional_field_20"])."\",
					url_link_1 = \"".stripslashes($this->classified_variables["url_link_1"])."\",
					url_link_2 = \"".stripslashes($this->classified_variables["url_link_2"])."\",
					url_link_3 = \"".stripslashes($this->classified_variables["url_link_3"])."\",
					precurrency = \"".urlencode($this->classified_variables["precurrency"])."\",
					price = \"".$this->classified_variables["price"]."\",
					email = \"".$this->classified_variables["email_option"]."\",
					expose_email = \"".$this->classified_variables["expose_email"]."\",
					phone = \"".urlencode($this->classified_variables["phone"])."\",
					phone2 = \"".urlencode($this->classified_variables["phone2"])."\",
					fax = \"".urlencode($this->classified_variables["fax"])."\",
					postcurrency = \"".urlencode($this->classified_variables["postcurrency"])."\",
					description = \"".urlencode($this->classified_variables["description"])."\",
					location_city = \"".urlencode($this->classified_variables["city"])."\",
					location_country = \"".$this->classified_variables["country"]."\",
					location_state = \"".$this->classified_variables["state"]."\",
					location_zip = \"".urlencode($this->classified_variables["zip_code"])."\",
					mapping_address = \"".urlencode($this->classified_variables["mapping_address"])."\",
					mapping_city = \"".urlencode($this->classified_variables["mapping_city"])."\",
					mapping_state = \"".urlencode($this->classified_variables["mapping_state"])."\",
					mapping_country = \"".urlencode($this->classified_variables["mapping_country"])."\",
					mapping_zip = \"".urlencode($this->classified_variables["mapping_zip"])."\",
					paypal_id = \"".$this->classified_variables["paypal_id"]."\"
					where id = ".$classified_id;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					echo $this->sql_query."<br>\n";
					return false;
				}
				
				//STOREFRONT CODE
				if($this->classified_variables["storefront_category"])
				{
					$this->sql_query = "update ".$this->classifieds_table." set 
					storefront_category = \"".$this->classified_variables["storefront_category"]."\"
					where id = \"".$classified_id."\"";
					$save_variable_result = $db->Execute($this->sql_query);
					if (!$save_variable_result)
					{
						return false;
					}
				}
				//STOREFRONT CODE
				
				if ($this->configuration_data['admin_email_edit'])
				{
					$subject = "An ad's details have been edited - #".$classified_id;
					$message = "The below ad has been edited:\n\n";
					$message .= $this->configuration_data['classifieds_url']."?a=2&b=".$classified_id;
					if ($this->configuration_data['email_configuration_type'] == 1)
						$message = str_replace("\n\n","\n",$message);

					if ($this->configuration_data['email_header_break'])
						$separator = "\n";
					else
						$separator = "\r\n";
					$from = "From: ".$this->configuration_data['site_email'].$separator."Reply-to: ".$this->configuration_data['site_email'].$separator;

					$additional = "-f".$this->configuration_data['site_email'];

					if ($this->configuration_data['email_configuration'] == 1)
						mail($this->configuration_data['site_email'],$subject,$message,$from,$additional);
					elseif ($this->configuration_data['email_configuration'] == 2)
						mail($this->configuration_data['site_email'],$subject,$message,$from);
					else
						mail($this->configuration_data['site_email'],$subject,$message);
				}

				$this->delete_current_category_questions($db,$classified_id);
				if (!$this->insert_category_questions($db,$classified_id))
					return false;

				return true;
			}
			else
			{
				return false;
			}
		}
		else
			return false;

	} //end of function update_classified_ad_details

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_current_category_questions($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$this->sql_query = "delete from ".$this->classified_extra_table." where
				classified_id = ".$classified_id;
			$delete_extra_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$delete_extra_result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = urldecode($this->messages[57]);
				return false;
			}
			return true;
		}
		else
			return false;
	} //end of function delete_current_category_questions

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_category_questions($db,$classified_id=0)
	{
		$num_questions = count($this->classified_variables["question_value"]);
		$search_text = "";
		if (($num_questions > 0 ) && ($classified_id))
		{
			reset($this->classified_variables["question_value"]);
			while (list($key,$value) = each($this->classified_variables["question_value"]))
			{
				 if ((strlen(trim($this->classified_variables["question_value"][$key])) > 0) || (strlen(trim($this->classified_variables["question_value_other"][$key])) > 0))
				 {
					//there is a value in this questions so put it in the db
					$this->sql_query = "SELECT * FROM ".$this->classified_sell_questions_table." WHERE question_id = \"".$key."\"";
					$question_result = $db->Execute($this->sql_query);
					//echo $this->sql_query." is the query<br>\n";
					if (!$question_result)
					{
						//$this->body .=$this->sql_query." is the query<br>\n";
						$this->error_message = urldecode($this->messages[57]);
						return false;
					}
					elseif ($question_result->RecordCount() == 1)
					{
						$show = $question_result->FetchNextObject();
						if (strlen(trim($this->classified_variables["question_value_other"][$key])) > 0)
						{
							//$this->body .="use the other value for ---".$key."<br>\n";
							$use_this_value = urldecode($this->classified_variables["question_value_other"][$key]);
						}
						else
						{
							if ($this->ad_configuration_data->TEXTAREA_WRAP)
								$use_this_value = urldecode(nl2br($this->classified_variables["question_value"][$key]));
							else
								$use_this_value = urldecode($this->classified_variables["question_value"][$key]);
						}
						if ($show->CHOICES == "check")
							$checkbox = 1;
						elseif ($show->CHOICES == "url")
							$checkbox = 2;
						else
							$checkbox = 0;
						$use_this_value = str_replace("\n"," ",$use_this_value);
						$this->sql_query = "insert into ".$this->classified_extra_table."
							(classified_id,name,question_id,value,explanation,checkbox,display_order)
							values
							(".$classified_id.",\"".urlencode($show->NAME)."\",\"".$key."\",\"".urlencode($use_this_value)."\",
							\"".addslashes($show->EXPLANATION)."\",".$checkbox.",".$show->DISPLAY_ORDER.")";
						$current_insert_result = $db->Execute($this->sql_query);
						//echo $this->sql_query." is the query<br>\n";
						if (!$current_insert_result)
						{
							//$this->body .=$this->sql_query." is the query<br>\n";
							$this->error_message = urldecode($this->messages[57]);
							return false;
						}
						$search_text .= urlencode($use_this_value)." - ";
					}
				} // end of if
			} // end of for $i
		}

		$num_group_questions = count($this->classified_variables["group_value"]);
		//echo $num_group_questions." is the num of group questions remembered<Br>\n";
		if (($num_group_questions > 0 ) && ($classified_id))
		{
			$this->sql_query = "select group_id from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
			$group_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$group_result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = urldecode($this->messages[57]);
				return false;
			}
			elseif ($group_result->RecordCount() == 1)
			{
				$show_group = $group_result->FetchNextObject();
				reset($this->classified_variables["group_value"]);
				while (list($key,$value) = each($this->classified_variables["group_value"]))
				{
					if ((strlen(trim($this->classified_variables["group_value"][$key])) > 0) || (strlen(trim($this->classified_variables["group_value_other"][$key])) > 0))
					 {
						//there is a value in this questions so put it in the db
						$this->sql_query = "SELECT * FROM ".$this->classified_sell_questions_table." WHERE question_id = \"".$key."\"";
						$question_result = $db->Execute($this->sql_query);
						//echo $this->sql_query." is the query<br>\n";
						if (!$question_result)
						{
							//$this->body .=$this->sql_query." is the query<br>\n";
							$this->error_message = urldecode($this->messages[57]);
							return false;
						}
						elseif ($question_result->RecordCount() == 1)
						{
							$show = $question_result->FetchNextObject();
							if (strlen(trim($this->classified_variables["group_value_other"][$key])) > 0)
							{
								//$this->body .="use the other value for ---".$key."<br>\n";
								$use_this_value = urldeocde($this->classified_variables["group_value_other"][$key]);
							}
							else
								$use_this_value = urldecode($this->classified_variables["group_value"][$key]);
							if ($show->CHOICES == "check")
								$checkbox = 1;
							elseif ($show->CHOICES == "url")
								$checkbox = 2;
							else
								$checkbox = 0;
							$this->sql_query = "insert into ".$this->classified_extra_table."
								(classified_id,name,question_id,value,explanation,checkbox,group_id)
								values
								(".$classified_id.",\"".urlencode($show->NAME)."\",\"".$key."\",\"".urlencode($use_this_value)."\",
								\"".addslashes($show->EXPLANATION)."\",".$checkbox.",".$show_group->GROUP_ID.")";
							$insert_result = $db->Execute($this->sql_query);
							//echo $this->sql_query." is the query<br>\n";
							if (!$insert_result)
							{
								//$this->body .=$this->sql_query." is the query<br>\n";
								$this->error_message = urldecode($this->messages[57]);
								return false;
							}
							$search_text .= urlencode($use_this_value)." - ";
						}
					} // end of if
				} // end of for $i
			}
		}

		if ((($num_questions > 0) && ($classified_id)) || (($num_group_questions > 0) && ($classified_id)))
		{
			 if (strlen(trim($this->classified_variables["optional_field_1"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_1"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_2"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_2"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_3"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_3"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_4"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_4"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_5"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_5"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_6"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_6"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_7"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_7"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_8"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_8"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_9"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_9"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_10"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_10"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_11"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_11"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_12"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_12"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_13"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_13"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_14"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_14"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_15"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_15"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_16"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_16"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_17"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_17"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_18"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_18"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_19"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_19"]." - ";
			 if (strlen(trim($this->classified_variables["optional_field_20"])) > 0)
			 	$search_text .= $this->classified_variables["optional_field_20"]." - ";
			$search_text = urldecode($search_text);
			$this->sql_query = "update ".$this->classifieds_table." set
				search_text = \"".urlencode($search_text)."\"
				where id = ".$classified_id;
			//echo $this->sql_query." is the query<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = urldecode($this->messages[57]);
				return false;
			}
		}// end of if num_questions > 0
		return true;
	} //end of function insert_category_questions

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function current_display_image_tag($value)
	{
		//overrides display_image_tag in site_class
		if ($value["type"] == 1)
		{
//			$this->sql_query = "SELECT * FROM ".$this->images_urls_table." WHERE image_id = ".$value["id"];
//			$image_result = $db->Execute($this->sql_query);
//			//echo $this->sql_query." is the query<br>\n";
//			if (!$image_result)
//			{
//
//				$this->error_message = urldecode($this->messages[57]);
//				return false;
//			}
//			elseif ($image_result->RecordCount() == 1)
//			{
//				//show the http saved
//				$show = $image_result->FetchNextObject();
				$image_tag .="<img src=";
				if ($value["thumb_url"])
					$image_tag .= $value["thumb_url"];
				else
					$image_tag .= $value["url"];
				$image_tag .= " width=".$value["image_width"]." height=".$value["image_height"]." >";
				if (strlen($value["image_text"]) > 0)
					$image_tag .= "<br><font class=image_field_labels>".$value["image_text"]."</font>";
				if ($value["image_width"] != $value["original_image_width"])
					$image_tag .="<br><a href=\"javascript:winimage('".$value["url"]."','".($value["original_image_width"]+40)."','".($value["original_image_height"]+40)."')\" class=edit_ads_image_text>".urldecode($this->messages[1124])."</a>";
				$image_tag .="<br><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=4&e=".$value["classified_id"]."&d[type]=1&d[image_id]=".$value["id"]." class=delete_image_links>".urldecode($this->messages[1125])."</a>";
			//}
		}
		elseif ($value["type"] == 2)
		{
//			$this->sql_query = "SELECT * FROM ".$this->images_table." WHERE image_id = ".$value["id"];
//			$result = $db->Execute($this->sql_query);
//			//echo $this->sql_query." is the query<br>\n";
//			if (!$result)
//			{
//				//$this->body .=$this->sql_query." is the query<br>\n";
//				$this->error_message = urldecode($this->messages[57]);
//				return false;
//			}
//			elseif ($result->RecordCount() == 1)
//			{
//				$show = $result->FetchNextObject();
				$image_tag .="\n\t<img src=get_image.php?image=".$value["image_id"]."  width=".$value["image_width"]." height=".$value["image_height"]." >";
				if (strlen($value["image_text"]) > 0)
					$image_tag .= "<br><font class=image_field_labels>".$value["image_text"]."</font>";
				if ($value["image_width"] != $value["original_image_width"])
					$image_tag .="<br><a href=\"javascript:winimage('get_image.php?image=".$value["image_id"]."','".($value["original_image_width"]+40)."','".($value["original_image_height"]+40)."')\" class=edit_ads_image_text>".urldecode($this->messages[1124])."</a>";
				$image_tag .="<br><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=4&e=".$value["classified_id"]."&d[type]=2&d[image_id]=".$value["id"]." class=delete_image_links>".urldecode($this->messages[1125])."</a>";

//			}
		}
		return $image_tag;
	} //end of function current_display_image_tag

//##################################################################################

	function edit_classified_ad_images($db,$classified_id=0)
	{
		$this->page_id = 33;
		$this->get_text($db);
		$this->get_ad_configuration($db);
		if ($this->debug_current)
		{
			echo $this->ad_configuration_data->MAXIMUM_PHOTOS." is MAXIMUM_PHOTOS<Br>\n";
			echo $this->classified_id." is classified_id<BR>\n";
		}

		if (($classified_id) && ($this->ad_configuration_data->MAXIMUM_PHOTOS))
		{
			$show = $this->get_classified_data($db,$classified_id);
			//get the cost of placing images within this ad
			$price_plan = $this->get_price_plan($db,$show->CATEGORY,$show->SELLER);
			if ($this->debug_current)
			{
				echo $show->SELLER." is SELLER<Br>\n";
				echo $this->classified_user_id." is classified_user_id<br>\n";
				echo $price_plan->CHARGE_PER_PICTURE." is CHARGE_PER_PICTURE<Br>\n";
				echo $show->IMAGE." is show->image<Br>\n";
				echo $price_plan->NUM_FREE_PICS." is NUM_FREE_PICS<bR>\n";
				echo $show->CATEGORY." is the category<br>\n";
			}
			if (($show->SELLER == $this->classified_user_id) || ($this->classified_user_id == 1))
			{
				if ($show)
				{
					//show the form to begin editing this classified ad details
					$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=5&e=".$classified_id." method=post enctype=multipart/form-data>\n";
					//$this->body .="<input type=hidden name=MAX_FILE_SIZE value=\"".$this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE."\">\n";
					$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
					$this->body .="<tr class=section_title>\n\t<td colspan=2 >\n\t".urldecode($this->messages[633])."\n\t</td>\n</tr>\n";
					$this->body .="<tr class=user_management_page_title><td colspan=2 valign=top width=100%>\n\t\t".urldecode($this->messages[496])."\n\t\t</td>\n\t</tr>\n\t";


					$this->body .="<tr class=page_description>\n\t<td colspan=2>\n\t".urldecode($this->messages[497])."\n\t</td>\n</tr>\n";
					$this->body .="<tr class=page_description>\n\t<td colspan=2>\n\t".urldecode($this->messages[1353])." ".$this->get_ad_title($db,$classified_id)."\n\t</td>\n</tr>\n";
					$this->body .="<tr>\n\t<td colspan=2>\n\t";
					$this->body .= $this->current_display_ad_images($db,$classified_id);
					$this->body .="</td>\n</tr>\n";
					if (strlen($this->images_error) > 0)
						$this->body .="<tr class=error_message>\n\t<td colspan=2>\n\t".$this->images_error."\n\t</td>\n</tr>\n";
					//get an array of open keys
					//echo $price_plan->CHARGE_PER_PICTURE." is charge per pic<br>\n";
					//echo $maximum_number_images." is maximum_number_images<br>\n";
					//echo $this->ad_configuration_data->MAXIMUM_PHOTOS." is the MAXIMUM_PHOTOS<br>\n";
					if ($price_plan->CHARGE_PER_PICTURE > 0)
					{
						if ($price_plan->NUM_FREE_PICS >= $show->IMAGE)
						{
							if ($this->debug_current)
							{

							}
							$maximum_number_images = ($price_plan->NUM_FREE_PICS);
						}
						else
						{
							$maximum_number_images = ($show->IMAGE);
						}
					}
					else
						$maximum_number_images = $this->ad_configuration_data->MAXIMUM_PHOTOS;

					if ($this->debug_current)
					{
						echo $maximum_number_images." is the maximum_number_images<Br>\n";
					}

					if (($maximum_number_images == 0) && ($price_plan->CHARGE_PER_PICTURE != 0))
					{
						$this->body .="<tr class=error_message>\n\t<td colspan=2>\n\t".urldecode($this->messages[1354])."\n\t</td>\n</tr>\n";
					}
					else
					{
						if ($this->debug_current)
						{
							echo  "about to display the form<BR>\n";
							echo $maximum_number_images." is maximum_number_images<bR>";
							echo $price_plan->CHARGE_PER_PICTURE." is price_plan->CHARGE_PER_PICTURE<bR>";
						}
						$not_keys_yet = array();
						for ($n=1;$n<=$maximum_number_images;$n++)
						{
							if ($this->debug_current) echo $n." is the n<BR>\n";
							if (!$this->in_array_key($n, $this->images_to_display))
							{
								if ($this->debug_current) echo "putting n: ".$n." in not_keys_yet<BR>\n";
								array_push($not_keys_yet,$n);
							}
						}

						if ($this->debug_current)
						{
							reset ($not_keys_yet);
							foreach ($not_keys_yet as $key => $value)
								echo $value." is value in not_keys_yet-".$key."<br>\n";
						}
						reset ($not_keys_yet);

						//add image form

						
						if(is_dir('classes/aurigma/'))
						{
							if(!isset($_COOKIE["useAdvancedCookie"]))
							{
								$defaultUploader = $this->ad_configuration_data->IMAGE_UPLOADER_DEFAULT;
								setcookie('useAdvancedCookie',$this->ad_configuration_data->IMAGE_UPLOADER_DEFAULT,time()+60*60*24*120);
							}else{
								$defaultUploader = $_COOKIE["useAdvancedCookie"];
								if($_REQUEST["useSimple"]==1)
								{
									$defaultUploader = 0;
									setcookie('useAdvancedCookie',0,time()+60*60*24*120);
								}elseif($_REQUEST["useAdvanced"]==1)
								{
									$defaultUploader = 1;
									setcookie('useAdvancedCookie',1,time()+60*60*24*120);
								}
							}
						}else{
							$this->ad_configuration_data->CLIENTSIDE_IMAGE_UPLOADER = false;
						}
						
						if ((count($this->images_to_display) < $maximum_number_images) &&($maximum_number_images > 0))
						{
							if (($this->ad_configuration_data->ALLOW_UPLOAD_IMAGES) && ($this->ad_configuration_data->ALLOW_URL_REFERENCED))
							{
								//show the url or image upload boxes
								if ($maximum_number_images > count($this->images_to_display))
								{
									if($this->ad_configuration_data->CLIENTSIDE_IMAGE_UPLOADER&&$defaultUploader)
									{
										$this->body .="<tr><td align=center class=image_field_labels id=imageUploaderContainer>";
										include_once('classes/aurigma/include_aurigma.php');
										$this->body .= "</td></tr>";
									}else{
										$this->body .="<tr class=column_headers>\n\t<td valign=top>".urldecode($this->messages[499])."\n\t</td>\n\t";
										$this->body .="<td valign=top>".urldecode($this->messages[500])."<br>
											".urldecode($this->messages[1103]).": ".$this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE."\n\t</td>\n</tr>\n";
										foreach ($not_keys_yet as $value)
										//for ($i=1;$i <= $this->ad_configuration_data->MAXIMUM_PHOTOS - count($this->images_captured);$i++)
										{
											$this->body .="<tr>\n\t<td class=image_field_labels>".$value.") <input type=text name=f[".$value."][url][location] size=35 maxlength=100></font>\n\t</td>\n\t";
											$this->body .="<td class=image_field_labels >".$value.") <input type=file name=d[".$value."]></td>\n</tr>\n";
											$this->body .="<tr>\n\t<td class=image_field_labels><input type=text name=f[".$value."][url][text] size=35 maxlength=100></font>\n\t</td>\n\t";
											$this->body .="<td class=image_field_labels ><input type=text name=f[".$value."][text] size=35 maxlength=100></td>\n</tr>\n";
										}
									}
								}
							}
							elseif (($this->ad_configuration_data->ALLOW_URL_REFERENCED) && (!$this->ad_configuration_data->ALLOW_UPLOAD_IMAGES))
							{
								if ($maximum_number_images > count($this->images_captured))
								{
									if($this->ad_configuration_data->CLIENTSIDE_IMAGE_UPLOADER&&$defaultUploader)
									{
										$this->body .="<tr><td align=center class=image_field_labels id=imageUploaderContainer>";
										include_once('classes/aurigma/include_aurigma.php');
										$this->body .= "</td></tr>";
									}else{
										//show only the image url choices
										$this->body .="<tr  class=image_field_labels>\n\t<td>".urldecode($this->messages[499])."\n\t</td>\n</tr>\n";
										foreach ($not_keys_yet as $value)
										//for ($i=1;$i <= $this->ad_configuration_data->MAXIMUM_PHOTOS - count($this->images_captured);$i++)
										{
											$this->body .="<tr>\n\t<td  class=image_field_labels>".$value.") <input type=text name=f[".$value."][urll][location] size=35 maxlength=100>
												<input type=text name=f[".$value."][url][text] size=35 maxlength=100></font>\n\t</td>\n</tr>\n";
										}
									}
								}
							}
							elseif (($this->ad_configuration_data->ALLOW_UPLOAD_IMAGES) && (!$this->ad_configuration_data->ALLOW_URL_REFERENCED))
							{
								if ($maximum_number_images > count($this->images_captured))
								{
									if($this->ad_configuration_data->CLIENTSIDE_IMAGE_UPLOADER&&$defaultUploader)
									{	
										$this->body .="<tr><td align=center class=image_field_labels id=imageUploaderContainer>";
										include_once('classes/aurigma/include_aurigma.php');
										$this->body .= "</td></tr>";
									}else{
										$this->body .="<tr><td class=column_headers>".urldecode($this->messages[500])."<br>
											".urldecode($this->messages[1103]).": ".$this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE."\n\t</td></tr>";
										foreach ($not_keys_yet as $value)
										//for ($i=1;$i <= $this->ad_configuration_data->MAXIMUM_PHOTOS - count($this->images_captured);$i++)
										{
											$this->body .="<tr>\n\t<td align=center class=image_field_labels>\n\t".$value.") <input type=file name=d[".$value."]>
												<input type=text name=f[".$value."][text] size=35 maxlength=100></td>\n</tr>\n";
										}
									}
								}
							}
							if($this->ad_configuration_data->CLIENTSIDE_IMAGE_UPLOADER&&$defaultUploader)
							{
								$this->body .="<tr class=add_images_button>\n\t<td colspan=2><a href='".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=2&e=".$classified_id."&useSimple=1'>".urldecode($this->messages[200180])."</a>\n\t</td>\n</tr>\n";
								$this->body .="<tr class=add_images_button><td colspan=2><input type=button name=submit value=\"".urldecode($this->messages[501])."\" class=add_images_button onclick='javascript: getImageUploader(\"ImageUploader\").Send(); this.disabled = true;'>\n\t</td>\n</tr>\n";
							} else {
								if(is_dir('classes/aurigma/')&&$this->ad_configuration_data->CLIENTSIDE_IMAGE_UPLOADER)
								{
									$this->body .="<tr class=add_images_button>\n\t<td colspan=2><a href='".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=2&e=".$classified_id."&useAdvanced=1'>".urldecode($this->messages[200181])."</a>\n\t</td>\n</tr>\n";
								}
								$this->body .="<tr class=add_images_button><td colspan=2><input type=submit name=submit value=\"".urldecode($this->messages[501])."\" class=add_images_button>\n\t</td>\n</tr>\n";
							}
							//$this->body .="<tr class=add_images_button>\n\t<td align=center colspan=2><input type=hidden name=MAX_FILE_SIZE value=\"".$this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE."\">
							//$this->body .="<tr>\n\t<td align=center colspan=2><input type=submit name=c[no_images] value=\"".urldecode($this->messages[244])."\">\n\t</td>\n</tr>\n";
							$this->body .="\n\t</td>\n</tr>\n";
							//$this->body .="<tr>\n\t<td colspan=2>\n\t"."<input type=submit name=submit value=\"".urldecode($this->messages[375])."\">\n\t</td>\n</tr>\n";
						}
					}
					$this->body .="<tr class=edit_ad_home_button>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&e=".$classified_id." class=edit_ad_home_button>
						".urldecode($this->messages[502])."</a>\n\t</td>\n</tr>\n";
					$this->body .="</table>\n\t";
					$this->body .="</form>\n";
					$this->display_page($db);
					return true;

				}
				else
				{
					return false;
				}
			}
			else
			{
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
				$this->body .="<tr class=section_title><td colspan=2 valign=top width=100%>\n\t\t".urldecode($this->messages[633])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=user_management_page_title><td colspan=2 valign=top width=100%>\n\t\t".urldecode($this->messages[496])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=edit_ad_home_button>\n\t<td colspan=2 >\n\t".urldecode($this->messages[502])."\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
				$this->display_page($db);
				return true;
			}

		}
		else
		{
			return false;
		}

	} //end of function edit_classified_ad_images

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function current_display_ad_images ($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$this->get_image_data($db,$classified_id,1);
			$this->count_images = count($this->images_to_display);
			$this->get_ad_configuration($db);

			if ($this->count_images >= $this->ad_configuration_data->PHOTO_COLUMNS)
			{
				$use_count = 0;
				switch ($this->ad_configuration_data->PHOTO_COLUMNS)
				{
					case 1:
						$width_tag = "100%";
						break;
					case 2:
						$width_tag = "50%";
						break;
					case 3:
						$width_tag = "33%";
						break;
					case 4:
						$width_tag = "25%";
						break;
					case 5:
						$width_tag = "20%";
						break;
					case 6:
						$width_tag = "16%";
						break;
					case 7:
						$width_tag = "14%";
						break;
					case 8:
						$width_tag = "12%";
						break;
					default:

				}
			}
			else {
				$use_count = 1;
				switch ($this->count_images)
				{
					case 1:
						$width_tag = "100%";
						break;
					case 2:
						$width_tag = "50%";
						break;
					case 3:
						$width_tag = "33%";
						break;
					case 4:
						$width_tag = "25%";
						break;
					case 5:
						$width_tag = "20%";
						break;
					case 6:
						$width_tag = "16%";
						break;
					case 7:
						$width_tag = "14%";
						break;
					case 8:
						$width_tag = "12%";
						break;
					default:

				}
			}
			if ((is_array($this->images_to_display)) && (count($this->images_to_display) > 0))
			{
				reset($this->images_to_display);
				$image_table =  "<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>";
				$value = current($this->images_to_display);

				do {
					$image_table .= "<tr><td align=center valign=top width=".$width_tag." class=image_field_labels>";
					$image_table .= $this->current_display_image_tag($value);
					$image_table .= "</td>";
					if ((($this->ad_configuration_data->PHOTO_COLUMNS > 1) && ($use_count == 0)) || (($use_count == 1) && ($this->count_images > 1)))
					{
						$value = next($this->images_to_display);
						if ($value)
						{
							$image_table .= "<td align=center valign=top width=".$width_tag." class=image_field_labels>";
							$image_table .= $this->current_display_image_tag($value);
							$image_table .= "</td>";
							if ((($this->ad_configuration_data->PHOTO_COLUMNS > 2) && ($use_count == 0)) || (($use_count == 1) && ($this->count_images > 2)))
							{
								$value = next($this->images_to_display);
								if ($value)
								{
									$image_table .= "<td align=center valign=top width=".$width_tag." class=image_field_labels>";
									$image_table .= $this->current_display_image_tag($value);
									$image_table .= "</td>";

									if ((($this->ad_configuration_data->PHOTO_COLUMNS > 3) && ($use_count == 0)) || (($use_count == 1) && ($this->count_images > 3)))
									{
										$value = next($this->images_to_display);
										if ($value)
										{
											$image_table .= "<td align=center valign=top width=".$width_tag." class=image_field_labels>";
											$image_table .= $this->current_display_image_tag($value);
											$image_table .= "</td>";

											if ((($this->ad_configuration_data->PHOTO_COLUMNS > 4) && ($use_count == 0)) || (($use_count == 1) && ($this->count_images > 4)))
											{
												$value = next($this->images_to_display);
												if ($value)
												{
													$image_table .= "<td align=center valign=top width=".$width_tag." class=image_field_labels>";
													$image_table .= $this->current_display_image_tag($value);
													$image_table .= "</td>";

													if ((($this->ad_configuration_data->PHOTO_COLUMNS > 5) && ($use_count == 0)) || (($use_count == 1) && ($this->count_images > 5)))
													{
														$value = next($this->images_to_display);
														if ($value)
														{
															$image_table .= "<td align=center valign=top width=".$width_tag." class=image_field_labels>";
															$image_table .= $this->current_display_image_tag($value);
															$image_table .= "</td>";

															if ((($this->ad_configuration_data->PHOTO_COLUMNS > 6) && ($use_count == 0)) || (($use_count == 1) && ($this->count_images > 6)))
															{
																$value = next($this->images_to_display);
																if ($value)
																{
																	$image_table .= "<td align=center valign=top width=".$width_tag." class=image_field_labels>";
																	$image_table .= $this->current_display_image_tag($value);
																	$image_table .= "</td>";

																	if ((($this->ad_configuration_data->PHOTO_COLUMNS > 7) && ($use_count == 0)) || (($use_count == 1) && ($this->count_images > 7)))
																	{
																		$value = next($this->images_to_display);
																		if ($value)
																		{
																			$image_table .= "<td align=center valign=top width=".$width_tag." class=image_field_labels>";
																			$image_table .= $this->current_display_image_tag($value);
																			$image_table .= "</td>";
																		}
																	}

																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
					$image_table .= "</tr>";
				} while ($value = next($this->images_to_display));

				$image_table .= "</table>\n";
			}
			return $image_table;
		}
		else
		{
			//no auction id to check
			return false;
		}
	 } //end of function current_display_ad_images

//####################################################################################

	function delete_classified_ad_image($db,$image_info=0,$classified_id=0)
	{
		//echo "hello from delete_classified_ad_image<br>\n";
		$show = $this->get_classified_data($db,$classified_id);
		if (($show->SELLER == $this->classified_user_id) || ($this->classified_user_id == 1))
		{
			if (($classified_id) && ($image_info))
			{
				if ($image_info["type"] == 1)
				{
					//type is url
					//delete url images
					//get image urls to
					$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$classified_id." and image_id = ".$image_info["image_id"];
					$get_url_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$get_url_result)
					{
						$this->body .=$this->sql_query."<br>\n";
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}
					elseif ($get_url_result->RecordCount())
					{
						while ($show_url = $get_url_result->FetchNextObject())
						{
							if ($show_url->FULL_FILENAME)
								unlink($show_url->FILE_PATH.$show_url->FULL_FILENAME);
							if ($show_url->THUMB_FILENAME)
								unlink($show_url->FILE_PATH.$show_url->THUMB_FILENAME);
						}
						$this->sql_query = "delete from ".$this->images_urls_table." where classified_id = ".$classified_id." and image_id = ".$image_info["image_id"];
						$delete_url_result = $db->Execute($this->sql_query);
						//echo $this->sql_query."<br>\n";
						if (!$delete_url_result)
						{
							$this->body .=$this->sql_query."<br>\n";
							$this->error_message = urldecode($this->messages[81]);
							return false;
						}
					}
					$this->update_images_or_not($db,$classified_id);
					return true;
				}
				elseif ($image_info["type"] == 2)
				{
					//type is uploaded
					$this->sql_query = "delete from ".$this->images_table." where classified_id = ".$classified_id." and image_id = ".$image_info["image_id"];
					$result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$result)
					{
						return false;
					}
					$this->update_images_or_not($db,$classified_id);
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
			//echo "no user info<br>\n";
			return false;
		}

	} //end of function delete_classified_ad_image

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_images_or_not($db,$classified_id=0)
	{
		$show_classified = $this->get_classified_data($db,$classified_id);

		$price_plan = $this->get_price_plan($db,$show_classified->CATEGORY,$this->classified_user_id);

		//echo $price_plan->CHARGE_PER_PICTURE." is charge per picture in update_images_or_not<br>\n";
		if ($price_plan->CHARGE_PER_PICTURE == 0)
		{
			$this->sql_query = "select image_id from ".$this->images_urls_table." where classified_id = ".$classified_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$this->get_image_data($db,$classified_id);
				$count_images = count($this->images_to_display);
				//this ad still has images
				$this->sql_query = "update ".$this->classifieds_table." set
					image = ".($count_images + 1)."
					where id = ".$classified_id;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					return false;
				}
				return true;
			}
			else
			{
				$this->sql_query = "select image_id from ".$this->images_table." where classified_id = ".$classified_id;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					return false;
				}
				elseif ($result->RecordCount() > 0)
				{
					$this->get_image_data($db,$classified_id);
					$count_images = count($this->images_to_display);
					$this->sql_query = "update ".$this->classifieds_table." set
						image = ".($count_images + 1)."
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$result)
					{
						return false;
					}
				}
				else
				{
					$this->sql_query = "update ".$this->classifieds_table."
						set image = 0
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$result)
					{
						return false;
					}
				}
				return true;
			}
		}
		else
			return true;
	} //end of function update_images_or_not

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//
//	function display_image_tag($value,$edit=0)
//	{
//		if ($value["type"] == 1)
//		{
//		//display the url
//			$tag = "<a href=\"javascript:winimage('".$value["url"]."','".($value["original_image_width"]+40)."','".($value["original_image_height"]+40)."')\"><img src=".$value["url"]." width=".$value["image_width"]." height=".$value["image_height"]." border=0>";
//			//if ($value["image_width"] != $value["original_image_width"])
//			$tag .= "<br><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=4&d[type]=".$value["type"]."&d[image_id]=".$value["id"]."&e=".$value["classified_id"]."><font class=user_management_detail_image_links>".urldecode($this->messages[381])."</font></a>";
//		}
//		elseif ($value["type"] == 2)
//		{
//			//display the uploaded image
//			$tag = "<a href=\"javascript:winimage('get_image.php?image=".$value["id"]."','".($value["original_image_width"]+40)."','".($value["original_image_height"]+40)."')\">
//				<img src=get_image.php?image=".$value["id"]." width=".$value["image_width"]." height=".$value["image_height"]." border=0></a>";
//			$tag .=  "<br><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=4&d[type]=".$value["type"]."&d[image_id]=".$value["id"]."&e=".$value["classified_id"]."><font class=user_management_detail_image_links>".urldecode($this->messages[381])."</font></a>";
//		}
//		return $tag;
//	} //end of function display_image_tag

//####################################################################################

	function process_images($db,$url_info=0,$classified_id=0,$post_files)
	{
		$debug_image = 0;
		$this->page_id = 33;
		$this->get_text($db);
		$this->get_ad_configuration($db);
		$classified_data = $this->get_classified_data($db,$classified_id);
		$this->get_image_data($db,$classified_id,1);
		$price_plan = $this->get_price_plan($db,$classified_data->CATEGORY,$classified_data->SELLER);
		if ($debug_image)
		{
			echo $price_plan." is price_plan<bR>\n";
			echo $classified_data." is classified_data<BR>\n";
			echo $classified_id." is classified_id<BR>\n";
			echo count($this->images_to_display)." is the count of this->images_to_display<bR>\n";
		}

		if ($price_plan->CHARGE_PER_PICTURE > 0)
		{
			if ($price_plan->NUM_FREE_PICS >= $classified_data->IMAGE)
			{
				$maximum_number_images = ($price_plan->NUM_FREE_PICS);
			}
			else
			{
				$maximum_number_images = ($classified_data->IMAGE);
			}
		}
		else
			$maximum_number_images = $this->ad_configuration_data->MAXIMUM_PHOTOS;

		if ($debug_image)
		{
			echo $price_plan->NUM_FREE_PICS." is price_plan->NUM_FREE_PICS<BR>\n";
			echo $classified_data->IMAGE." is classified_data->IMAGE<bR>\n";
			echo $maximum_number_images." is maximum_number_images<bR>\n";
			echo $this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT." is MAXIMUM_IMAGE_HEIGHT<br>\n";
			echo $this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH." is MAXIMUM_IMAGE_WIDTH<br>\n";
		}

		$image_height = ($this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT > $this->ad_configuration_data->LEAD_PICTURE_HEIGHT) ?
			$this->ad_configuration_data->MAXIMUM_IMAGE_HEIGHT : $this->ad_configuration_data->LEAD_PICTURE_HEIGHT;
		$image_width = ($this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH > $this->ad_configuration_data->LEAD_PICTURE_WIDTH) ?
			$this->ad_configuration_data->MAXIMUM_IMAGE_WIDTH : $this->ad_configuration_data->LEAD_PICTURE_WIDTH;

		//if ($price_plan->CHARGE_PER_PICTURE == 0)
		//	$maximum_number_images = $this->ad_configuration_data->MAXIMUM_PHOTOS;
		//else
		//	$maximum_number_images = $classified_data->IMAGE;
		//echo $maximum_number_images." is maximum_number_images in process images<Br>\n";
		//echo $this->ad_configuration_data->MAXIMUM_PHOTOS." is MAXIMUM_PHOTOS in process images<Br>\n";
		if ($classified_id)
		{
			if ($this->configuration_data['admin_email_edit'])
			{
				$subject = "An ad's images have been edited - #".$classified_id;
				$message = "The below ad has been edited:\n\n";
				$message .= $this->configuration_data['classifieds_url']."?a=2&b=".$classified_id;
				if ($this->configuration_data['email_configuration_type'] == 1)
					$message = str_replace("\n\n","\n",$message);
				if ($this->configuration_data['email_header_break'])
					$separator = "\n";
				else
					$separator = "\r\n";
				$from = "From: ".$this->configuration_data['site_email'].$separator."Reply-to: ".$this->configuration_data['site_email'].$separator;
				$additional = "-f".$this->configuration_data['site_email'];
				if ($this->configuration_data['email_configuration'] == 1)
					mail($this->configuration_data['site_email'],$subject,$message,$from,$additional);
				elseif ($this->configuration_data['email_configuration'] == 2)
					mail($this->configuration_data['site_email'],$subject,$message,$from);
				else
					mail($this->configuration_data['site_email'],$subject,$message);
			}
			//echo $url_info." is url info<Br>\n";
			//echo $maximum_number_images." is max num im<Br>\n";

			$this->get_image_file_types_array($db);
			//foreach ($url_info as $key => $value)
				//echo $value." is the value to the key-".$key."<br>\n";

			//process the images entered by the ad poster
			//$this->accepted_file_types = $this->get_accepted_file_types($db);
			//echo "hello from top of image process loop<br>\n";
			//echo $this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE." is max upload size<bR>\n";
			if ($url_info["imageUploader"]) {
				//aurigma
				$not_keys_yet = array();
				for ($n=1;$n<=$this->ad_configuration_data->MAXIMUM_PHOTOS;$n++)
				{
					if (!$this->in_array_key($n, $this->images_to_display))
					{
						array_push($not_keys_yet,$n);
					}
				}
				reset($not_keys_yet);
				for($i=1;$i<=$_POST["FileCount"];$i++)
				{
					$image_position = current($not_keys_yet);
					
					$description = $_POST ['Description_' . $i];
					$thumbnailName1 = "Thumbnail1_" . $i;
					$thumbnailName2 = "Thumbnail2_" . $i;
					
					$size1=$post_files[$thumbnailName1][size];
					if($size1) 
					{ 	
						$fileName1 = $post_files[$thumbnailName1][name];
						$tempName1 = $post_files[$thumbnailName1][tmp_name];
						$imageProperties1 = @getimagesize($tempName1);
						$width1 = $imageProperties1[0];
						$height1 = $imageProperties1[1];
					}
					$size2=$post_files[$thumbnailName2][size];
					if($size2) 
					{ 	
						$fileName2 = $post_files[$thumbnailName2][name];
						$tempName2 = $post_files[$thumbnailName2][tmp_name];
						$imageProperties2 = @getimagesize($tempName2);
						$width2 = $imageProperties2[0];
						$height2 = $imageProperties2[1];
					}
					
					if ($this->ad_configuration_data->IMAGE_UPLOAD_SAVE_TYPE == 1)
					{
						//SAVE TO THE DATABASE
						//SHOULDN'T BE USED
						$fp =fopen($tempName2, "r");
						if ($fp)
						{
							$data = fread($fp, $size2);
							$data = addslashes($data);

							if ($size1)
							{
								$thumb_fp =fopen($tempName1, "r");
								if ($thumb_fp)
								{
									$thumb_data = fread($thumb_fp, filesize($tempName1));
									$thumb_data = addslashes($thumb_data);
									$thumb = 1;
								}
								else
								{
									$thumb = 0;
									$thumb_data = 0;
								}
							}
							else
							{
								$thumb = 0;
								$thumb_data = 0;
							}
							
							$this->sql_query = "insert into ".$this->images_table."
								(classified_id,filesize,filetype,filename,image_text,date_entered,image_width,image_height,original_image_width,original_image_height,thumb,thumb_file,image_file,display_order)
								values
								(".$classified_id.",\"".$size1."\",\"".$type1."\",\"".$fileName1."\",\"".$description."\",".$this->shifted_time($db).",".$width2.",".$height2.",".$width2.",".$height2.",\"".$thumb."\",\"".$thumb_data."\",\"".$data."\",".$image_position.")";
							$result = $db->Execute($this->sql_query);
							if ($sell_debug_images)
							{
								echo $this->sql_query."<br>\n";
							}
							if (!$result)
							{
								$this->error_message = urldecode($this->messages[57]);
								return false;
							}
							$this->images_captured[$image_position]["id"] = $db->Insert_ID();
							$this->first_image_filled = 1;
						}
						else
						{
							$this->images_error = urldecode($this->messages[1147]);
						}
					}
					else
					{
						//SAVE IMAGE TO SERVER
						//do thumb first
						//get extension
						switch ($imageProperties1[2])
						{
							case 1: //gif
								$extension = ".gif";
							break;
							case 2: //jpg
								$extension = ".jpg";
							break;
							case 3: //png
								$extension = ".png";
							break;
							case 6: //bmp
								$extension = ".bmp";
							break;
							case 7: //tiff (intel)
								$extension = ".tif";
							break;
							default:
								// Check for accepted types in the database
								$this->sql_query = "select extension from ".$this->file_types_table." where mime_type like \"".$image_dimensions['mime']."\" and accept = 1";
								$result = $db->Execute($this->sql_query);
								if(!$result)
								{
									$extension = 0;
									break;
								}

								if($result->RecordCount() == 0)
								{
									$extension = 0;
									break;
								}
								else
									$file_type = $result->FetchRow();

								$extension = ".".$file_type['extension'];
							break;
						}
						if ($sell_debug_images)
						{
							echo $extension." is the extension<BR>\n";
						}
						if ($extension)
						{
							if ($size1)
							{
								do {
									srand((double)microtime()*1000000);
									$thumb_filename_root = rand(1000000,9999999);
									$thumb_filepath = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$thumb_filename_root.".jpg";
								} while (file_exists($thumb_filepath));
								if ($sell_debug_images)
									echo  $this->ad_configuration_data->PHOTO_QUALITY." is the photo quality<br>\n";
								$image_done = copy($tempName1, $thumb_filepath);
								if ($image_done)
								{
									$thumb_url = $this->ad_configuration_data->URL_IMAGE_DIRECTORY.$thumb_filename_root.".jpg";
									$thumb_filename = $thumb_filename_root.".jpg";
								}
								else
								{
									if ($sell_debug_images)
										echo "image NOT created with imagejpeg<br>\n";
									$thumb_url = 0;
									$thumb_filename = 0;
								}
								if ($sell_debug_images)
								{
									echo $thumb_url." is the thumb url<BR>\n";
									echo $thumb_filename." is the thumb filename<BR>\n";
								}
							}
							else
							{
								$thumb_url = 0;
								$thumb_filename = 0;
							}
							//do full size image
							do {
								srand((double)microtime()*1000000);
								$filename_root = rand(1000000,9999999);
								$filepath = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename_root.$extension;
							} while (file_exists($filepath));
							$full_filename = $filename_root.$extension;
							$full_url = $this->ad_configuration_data->URL_IMAGE_DIRECTORY.$full_filename;
							if ($sell_debug_images)
							{
								echo $filepath." is the filepath within full size image<Br>\n";
								echo $full_url." is the full_url within full size image<Br>\n";
								echo $full_filename." is the full_filename within full size image<Br>\n";
								echo $filepath." is the filepath within full size image<Br>\n";
								echo $filename." is the filename within full size image<Br>\n";
							}
							 if (copy ($tempName2,$filepath))
							 {
								$this->sql_query = "insert into ".$this->images_urls_table."
									(classified_id,image_url,full_filename,image_text,thumb_url,thumb_filename,file_path,date_entered,image_width,image_height,original_image_width,original_image_height,display_order,filesize,mime_type)
									values
									(".$classified_id.",\"".$full_url."\",\"".$full_filename."\",\"".$description."\",\"".$thumb_url."\",\"".$thumb_filename."\",\"".$this->ad_configuration_data->IMAGE_UPLOAD_PATH."\",".$this->shifted_time($db).",".$width1.",".$height1.",".$width2.",".$height2.",".$image_position.",".$size2.",\"".$imageProperties1['mime']."\")";
								if ($sell_debug_images)
								{
									echo $this->sql_query."<br>\n";
								}
								$result = $db->Execute($this->sql_query);
								if (!$result)
								{
									$this->error_message = urldecode($this->messages[57]);
									if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
										@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
									return false;
								}
								$this->images_captured[$image_position]["id"] = $db->Insert_ID();
							}
							if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
								@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
						} // if ($extension)
						if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
							@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
					}
					next($not_keys_yet);
				}
			}else{
				for ($i = 0;$i <= $maximum_number_images;$i++)
				{
					//echo $i." is the iteration<bR>\n";
					//echo $post_files[d]['size'][$i]." is the upload size of ".$i." inside of i loop<br>\n";
					//echo $post_files[d]['size'][1]." is the upload size of 1 inside of loop<br>\n";
					//echo $post_files[d]['size'][2]." is the upload size of 2 inside of loop<br>\n";
					//echo $url_info[$i]["url"]["location"]." is the url_info of ".$i."<br>\n";
					
					if (strlen(trim($url_info[$i]["url"]["location"])) > 0)
					{
						//insert the url
						//echo "hello from url_info loop where i is ".$i."<br>\n";
						//echo $url_info[$i]["url"]["location"]." is the url of ".$i." - 2<br>\n";
						$image_dimensions = @getimagesize($url_info[$i]["url"]["location"]);
						//echo $image_dimensions[0]." is the width in url<br>\n";
						if ($image_dimensions)
						{
							if (($image_dimensions[0] > $image_width) && ($image_dimensions[1] > $image_height))
							{
								$imageprop = ($image_width * 100) / $image_dimensions[0];
								$imagevsize = ($image_dimensions[1] * $imageprop) / 100 ;
								$final_image_width = $image_width;
								$final_image_height = ceil($imagevsize);
	
								if ($final_image_height > $image_height)
								{
									$imageprop = ($image_height * 100) / $image_dimensions[1];
									$imagehsize = ($image_dimensions[0] * $imageprop) / 100 ;
									$final_image_height = $image_height;
									$final_image_width = ceil($imagehsize);
								}
							}
							elseif ($image_dimensions[0] > $image_width)
							{
								$imageprop = ($image_width * 100) / $image_dimensions[0];
								$imagevsize = ($image_dimensions[1] * $imageprop) / 100 ;
								$final_image_width = $image_width;
								$final_image_height = ceil($imagevsize);
							}
							elseif ($image_dimensions[1] > $image_height)
							{
								$imageprop = ($image_height * 100) / $image_dimensions[1];
								$imagehsize = ($image_dimensions[0] * $imageprop) / 100 ;
								$final_image_height = $image_height;
								$final_image_width = ceil($imagehsize);
							}
							else
							{
								$final_image_width = $image_dimensions[0];
								$final_image_height = $image_dimensions[1];
							}
	
							if ((!$this->first_image_filled) && ($i > 1) && (count($this->images_to_display) == 0))
								$image_position = 1;
							else
								$image_position = $i;
	
							$this->sql_query = "insert into ".$this->images_urls_table."
								(classified_id,image_url,date_entered,image_text,image_width,image_height,original_image_width,original_image_height,display_order)
								values
								(".$classified_id.",\"".$url_info[$i]["url"]["location"]."\",".$this->shifted_time($db).",\"".$url_info[$i]["url"]["text"]."\",".$final_image_width.",".$final_image_height.",".$image_dimensions[0].",".$image_dimensions[1].",".$image_position.")";
							//echo $this->sql_query."<br>\n";
							$this->first_image_filled = 1;
							$this->images_captured[$i]["id"] = $db->Insert_ID();
							$result = $db->Execute($this->sql_query);
							if (!$result)
							{
								$this->error_message = urldecode($this->messages[57]);
								return false;
							}
							if ($debug_image)
								echo $this->sql_query." - 1<br>\n";
						}
						else
						{
							//could not find url image
							//$this->images_error = urldecode($this->messages[1126]);
							echo "could not find image<Br>\n";
						}
					}
					elseif (($post_files[d]['size'][$i] > 0) && ($post_files[d]['size'][$i] < $this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE))
					{
	
						//insert the image
						$size = $post_files[d]['size'][$i];
						$name = $post_files[d]['name'][$i];
						$type = $post_files[d]['type'][$i];
						$tmp_file = $post_files[d]['tmp_name'][$i];
						if ($debug_image)
						{
							echo "hello from image upload loop where i is ".$i."<br>\n";
							echo $url_info[$i]["text"]." is text<Br>\n";
							echo $tmp_file." is the tmp_file<br>\n";
							echo $_SERVER["DOCUMENT_ROOT"]." is the doc root<br>\n";
							echo stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH)." is the upload path<br>\n";
						}
						if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
						{
							$filename = strrchr($tmp_file,"/");
							$filename = str_replace("/","",$filename);
							if (!move_uploaded_file($tmp_file, stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename))
							{
								$filename = 0;
							}
							else
							{
								$filename = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename;
							}
						}
						else
						{
							$filename = $tmp_file;
						}
	
						if ($this->image_accepted_type($type))
						{
							if (strlen(trim($this->current_file_type_icon)) > 0)
							{
								//upload file and reference using icon
								do {
									srand((double)microtime()*1000000);
									$filename_root = rand(1000000,9999999);
									$filepath = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename_root.".".$this->current_file_type_extension;
								} while (file_exists($filepath));
	
								$full_filename = $filename_root.".".$this->current_file_type_extension;
								$full_url = $this->ad_configuration_data->URL_IMAGE_DIRECTORY.$full_filename;
								if ($debug_images)
								{
									echo $filepath." is the filepath within icon use<Br>\n";
									echo $full_url." is the full_url within icon use<Br>\n";
									echo $full_filename." is the full_filename within icon use<Br>\n";
								}
	
								$a = array("B", "KB", "MB", "GB", "TB", "PB");
	
								$pos = 0;
								while ($size >= 1024)
								{
									$size /= 1024;
									$pos++;
								}
								$displayed_filesize = round($size,2)." ".$a[$pos];
	
								 if (copy ($filename,$filepath))
								 {
									if ((!$this->first_image_filled) && ($i > 1)&& (count($this->images_to_display) == 0))
										$image_position = 1;
									else
										$image_position = $i;
									$this->sql_query = "insert into ".$this->images_urls_table."
										(classified_id,image_url,full_filename,file_path,date_entered,display_order,filesize,filesize_displayed,icon)
										values
										(".$classified_id.",\"".$full_url."\",\"".$full_filename."\",
										\"".$this->ad_configuration_data->IMAGE_UPLOAD_PATH."\",
										".$this->shifted_time($db).",".$image_position.",\"".$size."\",\"".$displayed_filesize."\",\"".$this->current_file_type_icon."\")";
									if ($debug_images)
									{
										echo $this->sql_query." 2<br>\n";
									}
									$this->first_image_filled = 1;
									$result = $db->Execute($this->sql_query);
									if (!$result)
									{
										$this->error_message = urldecode($this->messages[57]);
										return false;
									}
								}
							}
							else
							{
								$image_dimensions = @getimagesize($filename);
								//echo $image_dimensions[0]." is the width in upload<br>\n";
								if ($image_dimensions)
								{
									if (($image_dimensions[0] > $image_width) && ($image_dimensions[1] > $image_height))
									{
										$imageprop = ($image_width * 100) / $image_dimensions[0];
										$imagevsize = ($image_dimensions[1] * $imageprop) / 100 ;
										$final_image_width = $image_width;
										$final_image_height = ceil($imagevsize);
	
										if ($final_image_height > $image_height)
										{
											$imageprop = ($image_height * 100) / $image_dimensions[1];
											$imagehsize = ($image_dimensions[0] * $imageprop) / 100 ;
											$final_image_height = $image_height;
											$final_image_width = ceil($imagehsize);
										}
									}
									elseif ($image_dimensions[0] > $image_width)
									{
										$imageprop = ($image_width * 100) / $image_dimensions[0];
										$imagevsize = ($image_dimensions[1] * $imageprop) / 100 ;
										$final_image_width = $image_width;
										$final_image_height = ceil($imagevsize);
									}
									elseif ($image_dimensions[1] > $image_height)
									{
										$imageprop = ($image_height * 100) / $image_dimensions[1];
										$imagehsize = ($image_dimensions[0] * $imageprop) / 100 ;
										$final_image_height = $image_height;
										$final_image_width = ceil($imagehsize);
									}
									else
									{
										$final_image_width = $image_dimensions[0];
										$final_image_height = $image_dimensions[1];
									}
	
									if ($debug_image)
									{
										echo $final_image_width." is final width and ".$image_dimensions[0]." is orig width<BR>\n";
										echo $final_image_height." is final height and ".$image_dimensions[1]." is orig wiheightdth<BR>\n";
									}
	
									if (($final_image_width != $image_dimensions[0]) || ($final_image_height != $image_dimensions[1]))
									{
										//create thumbnail
										$copied = 0;
										switch ($image_dimensions[2])
										{
											case 1: //gif
												//no gif support to open and rewrite
												$extension = ".gif";
											break;
											case 2: //jpg
												if (function_exists("imagecreatefromjpeg"))
												{
													$src_image = @imagecreatefromjpeg($filename);
													if (function_exists("imagecreatetruecolor"))
													{
														if (!$this->ad_configuration_data->IMAGECREATETRUECOLOR_SWITCH)
														{
															$dest_image = @imagecreatetruecolor($final_image_width,$final_image_height);
															if ($debug_images)
																echo "imagecreatetruecolor worked<BR>\n";
															if (!$dest_image)
																$dest_image = imagecreate($final_image_width,$final_image_height);
														}
														else
															$dest_image = imagecreate($final_image_width,$final_image_height);
													}
		    											else
		    												$dest_image = imagecreate($final_image_width,$final_image_height);
													if (($src_image)  && ($dest_image))
													{
														if (!$this->ad_configuration_data->IMAGECREATETRUECOLOR_SWITCH)
														{
															if ($debug_images)
																echo "using imagecopyresampled<br>\n";
															$copied = imagecopyresampled($dest_image,$src_image,0,0,0,0,$final_image_width,$final_image_height,$image_dimensions[0],$image_dimensions[1]);
														}
														else
														{
															if ($debug_images)
																echo "using imagecopyresized<br>\n";
															$copied = imagecopyresized($dest_image,$src_image,0,0,0,0,$final_image_width,$final_image_height,$image_dimensions[0],$image_dimensions[1]);
														}
													}
													 else
														$copied = 0;
												 }
												 else
												 	$copied = 0;
												 $extension = ".jpg";
											break;
											case 3: //png
												if (function_exists("imagecreatefrompng"))
												{
													$src_image = @imagecreatefrompng ($filename);
													if (function_exists("imagecreatetruecolor"))
													{
														if (!$this->ad_configuration_data->IMAGECREATETRUECOLOR_SWITCH)
														{
															$dest_image = @imagecreatetruecolor($final_image_width,$final_image_height);
															if ($debug_images)
																echo "imagecreatetruecolor worked<BR>\n";
															if (!$dest_image)
																$dest_image = imagecreate($final_image_width,$final_image_height);
														}
														else
															$dest_image = imagecreate($final_image_width,$final_image_height);
													}
		    											else
		    												$dest_image = imagecreate($final_image_width,$final_image_height);
	    												if (($src_image)  && ($dest_image))
	    													//if (function_exists("imagecopyresampled"))
	    													//	$copied = imagecopyresampled($dest_image,$src_image,0,0,0,0,$final_image_width,$final_image_height,$image_dimensions[0],$image_dimensions[1]);
	    													//else
	    													$copied = imagecopyresized($dest_image,$src_image,0,0,0,0,$final_image_width,$final_image_height,$image_dimensions[0],$image_dimensions[1]);
													 else
													  	$copied = 0;
												}
												else
													$copied = 0;
												$extension = ".png";
											break;
											case 6: //bmp
												//no bmp support to open and rewrite
												$extension = ".bmp";
											break;
											case 7: //tiff (intel)
												//no tiff support to open and rewrite
												$extension = ".tif";
											break;
										}
									}
									else
									{
										//no thumbnail
										//picture is small enough
										$copied = 0;
										$extension = "";
									}
									if ($this->ad_configuration_data->IMAGE_UPLOAD_SAVE_TYPE == 1)
									{
										//SAVE TO THE DATABASE
										$fp =fopen($filename, "r");
										if ($fp)
										{
											$data = fread($fp, $size);
											$data = addslashes($data);
	
											if ($copied)
											{
												do {
													srand((double)microtime()*1000000);
													$filename_root = rand(1000000,9999999);
													$filepath = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename_root.".jpg";
												} while (file_exists($filepath));
												$image_done = imagejpeg($dest_image, $filepath, $this->ad_configuration_data->PHOTO_QUALITY);
												if ($image_done)
												{
													$thumb_fp =fopen($filepath, "r");
													if ($thumb_fp)
													{
														$thumb_data = fread($thumb_fp, filesize($filepath));
														$thumb_data = addslashes($thumb_data);
														$thumb = 1;
													}
													else
													{
														$thumb = 0;
														$thumb_data = 0;
													}
												}
												else
												{
													$thumb = 0;
													$thumb_data = 0;
												}
												unlink($filepath);
											}
											else
											{
												$thumb = 0;
												$thumb_data = 0;
											}
	
											if ((!$this->first_image_filled) && ($i > 1)&& (count($this->images_to_display) == 0))
												$image_position = 1;
											else
												$image_position = $i;
	
											$this->sql_query = "insert into ".$this->images_table."
												(classified_id,filesize,filetype,filename,image_text,date_entered,image_width,image_height,original_image_width,original_image_height,thumb,thumb_file,image_file,display_order)
												values
												(".$classified_id.",".$size.",\"".$type."\",\"".$name."\",\"".$url_info[$i]["text"]."\",".$this->shifted_time($db).",".$final_image_width.",".$final_image_height.",".$image_dimensions[0].",".$image_dimensions[1].",".$thumb.",\"".$thumb_data."\",\"".$data."\",".$image_position.")";
											$image_insert_result = $db->Execute($this->sql_query);
											$this->first_image_filled = 1;
											//echo $this->sql_query."<br>\n";
											if (!$image_insert_result)
											{
												$this->error_message = urldecode($this->messages[57]);
												if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
													@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
												return false;
											}
											$this->images_captured[$i]["id"] = $db->Insert_ID();
											if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
												@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
										}
										else
										{
											$this->images_error = urldecode($this->messages[1126]);
											if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
												@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
										}
									}
									else
									{
										//SAVE IMAGE TO SERVER
										//do thumb first
										//get extension
										switch ($image_dimensions[2])
										{
											case 1: //gif
												$extension = ".gif";
											break;
											case 2: //jpg
												$extension = ".jpg";
											break;
											case 3: //png
												$extension = ".png";
											break;
											case 6: //bmp
												$extension = ".bmp";
											break;
											case 7: //tiff (intel)
												$extension = ".tif";
											break;
											default:
												$extension = 0;
										}
	
										if ($extension)
										{
											if ($copied)
											{
												do {
													srand((double)microtime()*1000000);
													$thumb_filename_root = rand(1000000,9999999);
													$thumb_filepath = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$thumb_filename_root.".jpg";
												} while (file_exists($thumb_filepath));
												$image_done = imagejpeg($dest_image, $thumb_filepath, $this->ad_configuration_data->PHOTO_QUALITY);
												if ($image_done)
												{
													//get url of thumb
													$thumb_url = $this->ad_configuration_data->URL_IMAGE_DIRECTORY.$thumb_filename_root.".jpg";
													$thumb_filename = $thumb_filename_root.".jpg";
												}
												else
												{
													$thumb_url = 0;
													$thumb_filename = 0;
												}
											}
											else
											{
												$thumb_url = 0;
												$thumb_filename = 0;
											}
											//do full size image
											do {
												srand((double)microtime()*1000000);
												$filename_root = rand(1000000,9999999);
												$filepath = stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename_root.$extension;
											} while (file_exists($filepath));
	
											$full_filename = $filename_root.$extension;
											$full_url = $this->ad_configuration_data->URL_IMAGE_DIRECTORY.$full_filename;
											//echo $filepath." is the filepath<Br>\n";
											if ($debug_image)
											{
												echo $this->first_image_filled." is first_image_filled<bR>\n";
												echo $i." is the iteration<br>\n";
												echo count($this->images_captured)." is images_captured count<bR>\n";
											}
	
											if ((!$this->first_image_filled) && ($i > 1) && (count($this->images_to_display) == 0))
												$image_position = 1;
											else
												$image_position = $i;
	
											 if (copy ($filename,$filepath))
											 {
												$this->sql_query = "insert into ".$this->images_urls_table."
													(classified_id,image_url,full_filename,image_text,thumb_url,thumb_filename,file_path,date_entered,image_width,image_height,original_image_width,original_image_height,display_order)
													values
													(".$classified_id.",\"".$full_url."\",\"".$full_filename."\",\"".$url_info[$i]["text"]."\",\"".$thumb_url."\",\"".$thumb_filename."\",\"".$this->ad_configuration_data->IMAGE_UPLOAD_PATH."\",".$this->shifted_time($db).",".$final_image_width.",".$final_image_height.",".$image_dimensions[0].",".$image_dimensions[1].",".$image_position.")";
												if ($debug_image)
												{
													echo $this->sql_query." 3<br>\n";
												}
												$this->first_image_filled = 1;
												$result = $db->Execute($this->sql_query);
												if (!$result)
												{
													$this->error_message = urldecode($this->messages[57]);
													if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
														@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
													return false;
												}
												$this->images_captured[$i]["id"] = $db->Insert_ID();
												$this->first_image_filled = 1;
											}
											if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
												@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
										} // if ($extension)
										if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
											@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
									}
								} //if ($image_dimensions)
								else
								{
									//internal error could not process your image
									if ($post_files[d]['size'][$i] == 0)
									{
										$this->images_error = urldecode($this->messages[1126]);
										if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
											@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
										return false;
									}
									elseif ($post_files[d]['size'][$i] > $this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE)
									{
										$this->images_error =  urldecode($this->messages[1126]);
										if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
											@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
										return false;
									}
	
								}
							}
						} //if ($this->image_accepted_type($type))
						else
						{
							//wrong image file type
							$this->images_error = urldecode($this->messages[1131]);
						}
						if ($this->ad_configuration_data->IMAGE_UPLOAD_TYPE)
							@unlink(stripslashes($this->ad_configuration_data->IMAGE_UPLOAD_PATH).$filename);
					}
					else
					{
						//if ($post_files[d]['size'][$i] == 0)
						//{
						//	echo "error 4<BR>";
						//	$this->images_error = urldecode($this->messages[1126]);
						//}
						//else
						if ($post_files[d]['size'][$i] > $this->ad_configuration_data->MAXIMUM_UPLOAD_SIZE)
						{
							$this->images_error =  urldecode($this->messages[1126]);
						}
					}
				}
			}
			//get the number of images
			$this->get_image_data($db,$classified_id);

			$this->update_images_or_not($db,$classified_id);
			//$this->sql_query = "UPDATE ".$this->classifieds_table." set
			//	image = ".count($this->images_to_display)."
			//	where id = ".$classified_id;
			//echo $this->sql_query."<bR>\n";
			//$image_report_result = $db->Execute($this->sql_query);
			//if (!$image_report_result)
			//{
			//	return false;
			//}
			return true;
		}
		else
		{
			//no user id or image_info
			return false;
		}

	} //end of function process_images

//####################################################################################

	function change_sold_sign_status($db,$classified_id)
	{
		if (($classified_id) && ($this->classified_user_id))
		{
			$classified_ad = $this->get_classified_data($db,$classified_id);

			// Check for it not being a classified ad
			if($classified_ad->ITEM_TYPE != 1)
				return false;

			if (($classified_ad->SELLER == $this->classified_user_id) && (strlen(trim($this->configuration_data["sold_image"])) >0))
			{
				if ($classified_ad->SOLD_DISPLAYED == 1)
					$display = 0;
				else
					$display = 1;
				$this->sql_query = "update ".$this->classifieds_table." set sold_displayed = ".$display." where id = ".$classified_id;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					$this->error_message = urldecode($this->messages[57]);
					return false;
				}
				return true;
			}
			else
				return false;
		}
		else
			return false;
	} //end of function change_sold_sign_status

//####################################################################################

	function check_no_category_specific_plan($db)
	{
		if ($this->classified_user_id)
		{
			$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				$this->error_message = urldecode($this->messages[57]);
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchNextObject();
				if ($show->PRICE_PLAN_ID)
				{
					$this->sql_query = "select * from ".$this->price_plans_categories_table."
						where price_plan_id = ".$show->PRICE_PLAN_ID;
					$category_price_plan_result =  $db->Execute($this->sql_query);
					//echo $this->sql_query." is the query<br>\n";
					if ($category_price_plan_result->RecordCount() > 0)
					{
						//there are category specific price plans...cannot change category
						return false;
					}
					else
						return true;
				}
				else
					return false;
			}
			else
				return false;
		}
		else
			return false;
	} //end of function check_no_category_specific_plan

//####################################################################################
	function get_price_plan($db,$category=0,$user_id=0)
	{
		//get price plan specifics
		if (($user_id) && ($category))
		{
			$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$user_id;
			$user_price_plan_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";

			if (!$user_price_plan_result)
			{
				return false;
			}
			elseif ($user_price_plan_result->RecordCount() == 1)
			{
				$show = $user_price_plan_result->FetchNextObject();
				$this->sql_query = "select * from ".$this->price_plans_table." where price_plan_id = ".$show->PRICE_PLAN_ID;
				$current_price_plan_result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is the query<br>\n";

				if (!$current_price_plan_result)
				{
					return false;
				}
				elseif ($current_price_plan_result->RecordCount() == 1)
				{
					$show = $current_price_plan_result->FetchNextObject();

					$category_next = $category;
					$overriding_category = 0;
					do
					{
						$this->sql_query = "select category_id,parent_id from ".$this->categories_table."
							where category_id = ".$category_next;
						$category_result =  $db->Execute($this->sql_query);
						//echo $this->sql_query." is the query<br>\n";
						if (!$category_result)
						{
							//echo $this->sql_query." is the query<br>\n";
							$this->error_message = $this->messages[3501];
							return false;
						}
						elseif ($category_result->RecordCount() == 1)
						{
							$show_category = $category_result->FetchNextObject();
							$this->sql_query = "select * from ".$this->price_plans_categories_table."
								where category_id = ".$show_category->CATEGORY_ID." and price_plan_id = ".$show->PRICE_PLAN_ID;
							$category_price_plan_result =  $db->Execute($this->sql_query);
							//echo $this->sql_query." is the query<br>\n";
							if ($category_price_plan_result->RecordCount() == 1)
							{
								$overriding_category = $show_category->CATEGORY_ID;
								$show_category_price_plan = $category_price_plan_result->FetchNextObject();
							}
							$category_next = $show_category->PARENT_ID;
						}
						else
						{
							return false;
						}
					} while (($show_category->PARENT_ID != 0 ) && ($overriding_category== 0));

					if ( $overriding_category != 0 )
					{
						if ($show->TYPE_OF_BILLING == 2)
						{
							//there is an overriding category specific price plan
							//overwrite the returns from the base price plan with these
							$show->FEATURED_AD_PRICE = $show_category_price_plan->FEATURED_AD_PRICE;
							$show->BOLDING_PRICE = $show_category_price_plan->BOLDING_PRICE;
							$show->ATTENTION_GETTER_PRICE = $show_category_price_plan->ATTENTION_GETTER_PRICE;
							$show->CHARGE_PER_PICTURE = $show_category_price_plan->CHARGE_PER_PICTURE;
							$show->BETTER_PLACEMENT_CHARGE = $show_category_price_plan->BETTER_PLACEMENT_CHARGE;
							//echo $show->NUM_FREE_PICS." is NUM_FREE_PICS 1 <Br>\n";
							return $show;
						}
						else
						{
							//this is a fee type
							$show_category_price_plan->TYPE_OF_BILLING = 1;
							//echo $show->NUM_FREE_PICS." is NUM_FREE_PICS 2 <Br>\n";
							return $show_category_price_plan;
						}
					}
					else
					{
						//echo $show->NUM_FREE_PICS." is NUM_FREE_PICS 3 <Br>\n";
						return $show;
					}
				}
				else
				{
					//echo $show->NUM_FREE_PICS." is NUM_FREE_PICS 4 <Br>\n";
					return $show;
				}
			}
			else
			{
				//echo "no price return<br>\n";
				$this->price_plan = 0;
				return false;
			}
		}
		else
		{
			//echo "no price plan id<Br>\n";
			$this->price_plan = 0;
			return false;
		}

	} //end of function get_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function sellers_sign_form($db,$classified_id)
	{
		//title,phone_1,phone_2,classified_id,description
		$this->page_id = 71;
		$this->get_text($db);
		$classified_ad = $this->get_classified_data($db,$classified_id);
		$user = $this->get_user_data($db,$this->classified_user_id);
		if (($classified_ad) && ($user))
		{
			$this->sql_query = "select * from ".$this->pages_table." where page_id = 71";
			$sign_page_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";

			if (!$sign_page_result)
			{
				return false;
			}
			elseif ($sign_page_result->RecordCount() == 1)
			{
				$show_page = $sign_page_result->FetchNextObject();
				//show the form to begin editing this classified ad details
				$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=4&b=12&d=".$classified_id." method=post>\n";
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
				$this->body .="<tr class=section_title><td colspan=2 valign=top width=100%>\n\t\t".urldecode($this->messages[1153])."\n\t\t</td>\n\t</tr>\n\t";

				$this->body .="<tr class=page_title>\n\t<td colspan=2 >\n\t".urldecode($this->messages[1152])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t<td colspan=2>\n\t".urldecode($this->messages[1154])."\n\t</td>\n</tr>\n";
				$this->body .= "<input type=hidden name=c[classified_id] value=".$classified_ad->ID.">";

				if ($show_page->MODULE_USE_IMAGE)
				{
					$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 14";
					$sign_image_result = $db->Execute($this->sql_query);
					//echo $this->sql_query." is the query<br>\n";
					if (!$sign_image_result)
					{
						return false;
					}
					elseif ($sign_image_result->RecordCount() > 0)
					{
						$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1312])."\n\t</td>\n\t";
						$this->body .="<td class=page_field_data><select name=c[image]>\n\t";
						$this->body .= "<option value=0>".urldecode($this->messages[1313])."</option>\n\t";
						while ($show_sign = $sign_image_result->FetchNextObject())
						{
							$this->body .= "<option value=".$show_sign->CHOICE_ID.">".$show_sign->DISPLAY_VALUE."</option>\n\t\t";
						}
						$this->body .= "</select></td></tr>\n";
					}
					else
					{
						$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1312])."\n\t</td>\n\t";
						$this->body .="<td class=page_field_data>".urldecode($this->messages[1313])."<input type=hidden name=c[image] value=\"0\"></td></tr>\n";
					}
				}

				if ($show_page->MODULE_DISPLAY_TITLE)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1151])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[title] class=page_field_data value=\"".urldecode($classified_ad->TITLE)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_PRICE)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1171])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[price] class=page_field_data value=\"".$this->configuration_data['precurrency']." ".urldecode($classified_ad->PRICE)." ".$this->configuration_data['postcurrency']."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_CONTACT)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1159])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[contact] class=page_field_data value=\"".$user->FIRSTNAME." ".$user->LASTNAME."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_PHONE1)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1155])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[phone_1] class=page_field_data value=\"".urldecode($classified_ad->PHONE)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_PHONE2)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1156])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[phone_2] class=page_field_data value=\"".urldecode($classified_ad->PHONE2)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_ADDRESS)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1284])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[address] class=page_field_data value=\"".$user->ADDRESS." ".$user->ADDRESS_2."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_CITY)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1285])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[city] class=page_field_data value=\"".urldecode($classified_ad->LOCATION_CITY)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_STATE)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1286])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[state] class=page_field_data value=\"".urldecode($classified_ad->LOCATION_STATE)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_ZIP)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1287])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[zip] class=page_field_data value=\"".urldecode($classified_ad->LOCATION_ZIP)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_AD_DESCRIPTION)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1157])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><textarea name=c[description] class=page_field_data cols=45 rows=10>".urldecode($classified_ad->DESCRIPTION)."</textarea></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_1)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1288])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_1] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_1)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_2)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1289])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_2] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_2)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_3)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1290])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_3] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_3)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_4)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1291])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_4] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_4)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_5)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1292])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_5] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_5)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_6)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1293])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_6] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_6)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_7)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1294])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_7] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_7)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_8)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1295])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_8] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_8)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_9)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1296])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_9] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_9)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_10)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1297])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_10] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_10)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_11)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1847])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_11] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_11)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_12)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1848])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_12] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_12)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_13)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1849])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_13] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_13)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_14)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1850])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_14] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_14)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_15)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1851])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_15] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_15)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_16)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1852])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_16] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_16)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_17)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1853])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_17] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_17)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_18)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1854])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_18] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_18)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_19)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1855])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_19] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_19)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_20)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1856])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_20] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_20)."\"></td></tr>\n";
				}

				$this->body .="<tr class=show_sign_button >\n\t<td colspan=2>\n\t"."<input type=submit name=submit value=\"".urldecode($this->messages[1183])."\" class=show_sign_button >\n\t</td>\n</tr>\n";
				$this->body .="<tr class=back_to_current_ad_link>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=back_to_current_ad_link>
					".urldecode($this->messages[1158])."</a>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
				$this->body .="</form>\n";
			}
			else
				return false;
		}
		else
		{
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
			$this->body .="<tr class=section_title><td width=100%>\n\t\t".urldecode($this->messages[1151])."\n\t\t</td>\n\t</tr>\n\t";
			$this->body .="<tr class=page_title>\n\t<td colspan=2 >\n\t".urldecode($this->messages[1152])."\n\t</td>\n</tr>\n";
			$this->body .="<tr class=back_to_current_ad_link>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=back_to_current_ad_link>
				".urldecode($this->messages[1158])."</a>\n\t</td>\n</tr>\n";
			$this->body .="</table>\n\t";
		}
		$this->display_page($db);
		return true;
	} //end of function sellers_sign_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_sellers_sign($db,$sign_info)
	{
		$this->page_id = 74;
		$this->get_text($db);
		$this->get_template($db);

		if ($this->template)
		{
			$this->sql_query = "select * from ".$this->pages_table." where page_id = 71";
			$sign_page_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";

			if (!$sign_page_result)
			{
				return false;
			}
			elseif ($sign_page_result->RecordCount() == 1)
			{
				$show_page = $sign_page_result->FetchNextObject();
				if ($show_page->MODULE_USE_IMAGE)
				{
					if ($sign_info["image"] != 0)
					{
						$this->sql_query = "select * from ".$this->choices_table." where choice_id = ".$sign_info["image"];
						$sign_images_result = $db->Execute($this->sql_query);
						//echo $this->sql_query." is the query<br>\n";
						if (!$sign_images_result)
						{
							return false;
						}
						elseif ($sign_images_result->RecordCount() == 1)
						{
							$show_sign_image = $sign_images_result->FetchNextObject();
							$image_tag = "<img src=".$show_sign_image->VALUE.">";
						}
						else
						{
							$image_tag = $this->get_signs_and_flyers_user_image($db,$sign_info["classified_id"],"sign");
						}
					}
					else
					{
						$image_tag = $this->get_signs_and_flyers_user_image($db,$sign_info["classified_id"],"sign");
					}
					$this->template = str_replace("<<IMAGE>>",$image_tag,$this->template);
				}
			}

			$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$sign_info["classified_id"];
			$result = $db->Execute($this->sql_query);
			if(!$result)
			{
				return false;
			}
			else
				$listing = $result->FetchRow();

			if ($show_page->MODULE_DISPLAY_TITLE)
				$this->template = str_replace("<<TITLE>>",$sign_info["title"],$this->template);
			if ($show_page->MODULE_DISPLAY_ADDRESS)
				$this->template = str_replace("<<ADDRESS>>",$sign_info["address"],$this->template);
			if ($show_page->MODULE_DISPLAY_CITY)
				$this->template = str_replace("<<CITY>>",$sign_info["city"],$this->template);
			if ($show_page->MODULE_DISPLAY_STATE)
				$this->template = str_replace("<<STATE>>",$sign_info["state"],$this->template);
			if ($show_page->MODULE_DISPLAY_ZIP)
				$this->template = str_replace("<<ZIP>>",$sign_info["zip"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_1)
				$this->template = str_replace("<<OPTIONAL_FIELD_1>>",$sign_info["optional_field_1"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_2)
				$this->template = str_replace("<<OPTIONAL_FIELD_2>>",$sign_info["optional_field_2"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_3)
				$this->template = str_replace("<<OPTIONAL_FIELD_3>>",$sign_info["optional_field_3"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_4)
				$this->template = str_replace("<<OPTIONAL_FIELD_4>>",$sign_info["optional_field_4"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_5)
				$this->template = str_replace("<<OPTIONAL_FIELD_5>>",$sign_info["optional_field_5"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_6)
				$this->template = str_replace("<<OPTIONAL_FIELD_6>>",$sign_info["optional_field_6"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_7)
				$this->template = str_replace("<<OPTIONAL_FIELD_7>>",$sign_info["optional_field_7"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_8)
				$this->template = str_replace("<<OPTIONAL_FIELD_8>>",$sign_info["optional_field_8"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_9)
				$this->template = str_replace("<<OPTIONAL_FIELD_9>>",$sign_info["optional_field_9"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_10)
				$this->template = str_replace("<<OPTIONAL_FIELD_10>>",$sign_info["optional_field_10"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_11)
				$this->template = str_replace("<<OPTIONAL_FIELD_11>>",$sign_info["optional_field_11"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_12)
				$this->template = str_replace("<<OPTIONAL_FIELD_12>>",$sign_info["optional_field_12"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_13)
				$this->template = str_replace("<<OPTIONAL_FIELD_13>>",$sign_info["optional_field_13"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_14)
				$this->template = str_replace("<<OPTIONAL_FIELD_14>>",$sign_info["optional_field_14"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_15)
				$this->template = str_replace("<<OPTIONAL_FIELD_15>>",$sign_info["optional_field_15"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_16)
				$this->template = str_replace("<<OPTIONAL_FIELD_16>>",$sign_info["optional_field_16"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_17)
				$this->template = str_replace("<<OPTIONAL_FIELD_17>>",$sign_info["optional_field_17"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_18)
				$this->template = str_replace("<<OPTIONAL_FIELD_18>>",$sign_info["optional_field_18"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_19)
				$this->template = str_replace("<<OPTIONAL_FIELD_19>>",$sign_info["optional_field_19"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_20)
				$this->template = str_replace("<<OPTIONAL_FIELD_20>>",$sign_info["optional_field_20"],$this->template);
			if ($show_page->MODULE_DISPLAY_PRICE)
				$this->template = str_replace("<<PRICE>>",$sign_info["price"],$this->template);
			if ($show_page->MODULE_DISPLAY_CONTACT)
				$this->template = str_replace("<<CONTACT>>",$sign_info["contact"],$this->template);
			if ($show_page->MODULE_DISPLAY_PHONE1)
				$this->template = str_replace("<<PHONE_1>>",$sign_info["phone_1"],$this->template);
			if ($show_page->MODULE_DISPLAY_PHONE2)
				$this->template = str_replace("<<PHONE_2>>",$sign_info["phone_2"],$this->template);
			if ($show_page->MODULE_DISPLAY_CLASSIFIED_ID)
				$this->template = str_replace("<<CLASSIFIED_ID>>",$sign_info["classified_id"],$this->template);
			if ($show_page->MODULE_DISPLAY_AUCTION_ID)
				$this->template = str_replace("<<AUCTION_ID>>",$sign_info["classified_id"],$this->template);
			if ($show_page->MODULE_DISPLAY_AD_DESCRIPTION)
				$this->template = str_replace("<<DESCRIPTION>>",stripslashes(stripslashes(urldecode($sign_info["description"]))),$this->template);
			if(($listing->BUY_NOW != 0.00) && ($listing['item_type'] == 2))
				$this->template = str_replace("<<BUY_NOW_PRICE>>", urldecode($auction->BUY_NOW),$this->template);
			else
				$this->template = str_replace("<<BUY_NOW_PRICE>>", "",$this->template);
			if($listing['item_type'] == 2)
				$this->template = str_replace("<<STARTING_BID>>", urldecode($listing->STARTING_BID),$this->template);
			else
				$this->template = str_replace("<<STARTING_BID>>", "",$this->template);
			echo $this->template;
			exit;
			return true;
		}
		else
			return false;
	} //end of function display_sellers_sign

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function sellers_flyer_form($db,$classified_id)
	{
		//title,phone,classified_id,description,price,contact
		$this->page_id = 70;
		$this->get_text($db);
		$classified_ad = $this->get_classified_data($db,$classified_id);
		$user = $this->get_user_data($db,$this->classified_user_id);
		if (($classified_ad) && ($user))
		{
			$this->sql_query = "select * from ".$this->pages_table." where page_id = 70";
			$sign_page_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$sign_page_result)
			{
				return false;
			}
			elseif ($sign_page_result->RecordCount() == 1)
			{
				$show_page = $sign_page_result->FetchNextObject();
				//show the form to begin editing this classified ad details
				$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=4&b=13&d=".$classified_id." method=post>\n";
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
				$this->body .="<tr class=section_title><td colspan=2 valign=top width=100%>\n\t\t".urldecode($this->messages[1160])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=page_title>\n\t<td colspan=2 >\n\t".urldecode($this->messages[1161])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t<td colspan=2>\n\t".urldecode($this->messages[1162])."\n\t</td>\n</tr>\n";
				$this->body .= "<input type=hidden name=c[classified_id] value=".$classified_ad->ID.">";

				if ($show_page->MODULE_USE_IMAGE)
				{
					$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 13";
					$sign_image_result = $db->Execute($this->sql_query);
					//echo $this->sql_query." is the query<br>\n";
					if (!$sign_image_result)
					{
						return false;
					}
					elseif ($sign_image_result->RecordCount() > 0)
					{
						$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1314])."\n\t</td>\n\t";
						$this->body .="<td class=page_field_data><select name=c[image]>\n\t";
						$this->body .= "<option value=0>".urldecode($this->messages[1315])."</option>\n\t";
						while ($show_sign = $sign_image_result->FetchNextObject())
						{
							$this->body .= "<option value=".$show_sign->CHOICE_ID.">".$show_sign->DISPLAY_VALUE."</option>\n\t\t";
						}
						$this->body .= "</select></td></tr>\n";
					}
					else
					{
						$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1314])."\n\t</td>\n\t";
						$this->body .="<td class=page_field_data>".urldecode($this->messages[1315])."<input type=hidden name=c[image] value=\"0\"></td></tr>\n";
					}
				}

				if ($show_page->MODULE_DISPLAY_TITLE)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1163])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[title] class=page_field_data value=\"".urldecode($classified_ad->TITLE)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_PRICE)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1170])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[price] class=page_field_data value=\"".$this->configuration_data['precurrency']." ".urldecode($classified_ad->PRICE)." ".$this->configuration_data['postcurrency']."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_CONTACT)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1164])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[contact] class=page_field_data value=\"".$user->FIRSTNAME." ".$user->LASTNAME."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_PHONE1)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1165])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[phone_1] class=page_field_data value=\"".urldecode($classified_ad->PHONE)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_PHONE2)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1166])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[phone_2] class=page_field_data value=\"".urldecode($classified_ad->PHONE2)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_ADDRESS)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1298])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[address] class=page_field_data value=\"".$user->ADDRESS." ".$user->ADDRESS_2."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_CITY)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1299])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[city] class=page_field_data value=\"".urldecode($classified_ad->LOCATION_CITY)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_STATE)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1300])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[state] class=page_field_data value=\"".urldecode($classified_ad->LOCATION_STATE)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_ZIP)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1301])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[zip] class=page_field_data value=\"".urldecode($classified_ad->LOCATION_ZIP)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_AD_DESCRIPTION)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1167])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><textarea name=c[description] class=page_field_data cols=45 rows=10>".urldecode($classified_ad->DESCRIPTION)."</textarea></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_1)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1302])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_1] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_1)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_2)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1303])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_2] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_2)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_3)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1304])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_3] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_3)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_4)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1305])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_4] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_4)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_5)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1306])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_5] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_5)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_6)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1307])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_6] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_6)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_7)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1308])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_7] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_7)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_8)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1309])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_8] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_8)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_9)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1310])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_9] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_9)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_10)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1311])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_10] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_10)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_11)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1827])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_11] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_11)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_12)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1828])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_12] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_12)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_13)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1829])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_13] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_13)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_14)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1830])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_14] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_14)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_15)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1831])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_15] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_15)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_16)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1832])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_16] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_16)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_17)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1833])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_17] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_17)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_18)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1834])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_18] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_18)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_19)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1835])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_19] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_19)."\"></td></tr>\n";
				}
				if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_20)
				{
					$this->body .="<tr class=page_field>\n\t<td >\n\t".urldecode($this->messages[1836])."\n\t</td>\n\t";
					$this->body .="<td class=page_field_data><input type=text name=c[optional_field_20] class=page_field_data value=\"".urldecode($classified_ad->OPTIONAL_FIELD_20)."\"></td></tr>\n";
				}

				$this->body .="<tr class=show_flyer_button >\n\t<td colspan=2>\n\t<input type=submit name=submit value=\"".urldecode($this->messages[1168])."\" class=show_flyer_button >\n\t</td>\n</tr>\n";
				$this->body .="<tr class=back_to_current_ad_link>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=back_to_current_ad_link>
				".urldecode($this->messages[1169])."</a>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
				$this->body .="</form>\n";
			}
		}
		else
		{
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
			$this->body .="<tr class=section_title><td width=100%>\n\t\t".urldecode($this->messages[1160])."\n\t\t</td>\n\t</tr>\n\t";
			$this->body .="<tr class=page_title>\n\t<td colspan=2 >\n\t".urldecode($this->messages[1161])."\n\t</td>\n</tr>\n";
			$this->body .="<tr class=back_to_current_ad_link>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=back_to_current_ad_link>
				".urldecode($this->messages[1169])."</a>\n\t</td>\n</tr>\n";
			$this->body .="</table>\n\t";
		}
		$this->display_page($db);
		return true;
	} //end of function sellers_flyer_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_sellers_flyer($db,$flyer_info=0)
	{
		$this->page_id = 73;
		$this->get_text($db);
		$this->get_template($db);

		//$this->sql_query = "select flyer_template from ".$this->ad_configuration_table;
		//$flyer_template_result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		//if (!$flyer_template_result)
		//{
		//	echo $this->sql_query." is the query<br>\n";
		//	return false;
		//}
		//elseif ($flyer_template_result->RecordCount() == 1)
		//{
		//	$flyer_template = $flyer_template_result->FetchNextObject();
		//	$this->template = $flyer_template->FLYER_TEMPLATE;
		//}
		//echo "hello 1<Br>";
		if ($this->template)
		{
			$this->sql_query = "select * from ".$this->pages_table." where page_id = 70";
			$sign_page_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";

			if (!$sign_page_result)
			{
				return false;
			}
			elseif ($sign_page_result->RecordCount() == 1)
			{
				$show_page = $sign_page_result->FetchNextObject();
				if ($show_page->MODULE_USE_IMAGE)
				{
					if ($flyer_info["image"] != 0)
					{
						$this->sql_query = "select * from ".$this->choices_table." where choice_id = ".$flyer_info["image"];
						$sign_images_result = $db->Execute($this->sql_query);
						//echo $this->sql_query." is the query<br>\n";
						if (!$sign_images_result)
						{
							return false;
						}
						elseif ($sign_images_result->RecordCount() == 1)
						{
							$show_sign_image = $sign_images_result->FetchNextObject();
							$image_tag = "<img src=".$show_sign_image->VALUE.">";
						}
						else
						{
							$image_tag = $this->get_signs_and_flyers_user_image($db,$flyer_info["classified_id"],"flyer");
						}
					}
					else
					{
						$image_tag = $this->get_signs_and_flyers_user_image($db,$flyer_info["classified_id"],"flyer");
					}
					$this->template = str_replace("<<IMAGE>>",$image_tag,$this->template);
				}
			}
			if ($show_page->MODULE_DISPLAY_TITLE)
				$this->template = str_replace("<<TITLE>>",$flyer_info["title"],$this->template);
			if ($show_page->MODULE_DISPLAY_ADDRESS)
				$this->template = str_replace("<<ADDRESS>>",$flyer_info["address"],$this->template);
			if ($show_page->MODULE_DISPLAY_CITY)
				$this->template = str_replace("<<CITY>>",$flyer_info["city"],$this->template);
			if ($show_page->MODULE_DISPLAY_STATE)
				$this->template = str_replace("<<STATE>>",$flyer_info["state"],$this->template);
			if ($show_page->MODULE_DISPLAY_ZIP)
				$this->template = str_replace("<<ZIP>>",$flyer_info["zip"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_1)
				$this->template = str_replace("<<OPTIONAL_FIELD_1>>",$flyer_info["optional_field_1"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_2)
				$this->template = str_replace("<<OPTIONAL_FIELD_2>>",$flyer_info["optional_field_2"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_3)
				$this->template = str_replace("<<OPTIONAL_FIELD_3>>",$flyer_info["optional_field_3"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_4)
				$this->template = str_replace("<<OPTIONAL_FIELD_4>>",$flyer_info["optional_field_4"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_5)
				$this->template = str_replace("<<OPTIONAL_FIELD_5>>",$flyer_info["optional_field_5"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_6)
				$this->template = str_replace("<<OPTIONAL_FIELD_6>>",$flyer_info["optional_field_6"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_7)
				$this->template = str_replace("<<OPTIONAL_FIELD_7>>",$flyer_info["optional_field_7"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_8)
				$this->template = str_replace("<<OPTIONAL_FIELD_8>>",$flyer_info["optional_field_8"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_9)
				$this->template = str_replace("<<OPTIONAL_FIELD_9>>",$flyer_info["optional_field_9"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_10)
				$this->template = str_replace("<<OPTIONAL_FIELD_10>>",$flyer_info["optional_field_10"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_11)
				$this->template = str_replace("<<OPTIONAL_FIELD_11>>",$flyer_info["optional_field_11"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_12)
				$this->template = str_replace("<<OPTIONAL_FIELD_12>>",$flyer_info["optional_field_12"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_13)
				$this->template = str_replace("<<OPTIONAL_FIELD_13>>",$flyer_info["optional_field_13"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_14)
				$this->template = str_replace("<<OPTIONAL_FIELD_14>>",$flyer_info["optional_field_14"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_15)
				$this->template = str_replace("<<OPTIONAL_FIELD_15>>",$flyer_info["optional_field_15"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_16)
				$this->template = str_replace("<<OPTIONAL_FIELD_16>>",$flyer_info["optional_field_16"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_17)
				$this->template = str_replace("<<OPTIONAL_FIELD_17>>",$flyer_info["optional_field_17"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_18)
				$this->template = str_replace("<<OPTIONAL_FIELD_18>>",$flyer_info["optional_field_18"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_19)
				$this->template = str_replace("<<OPTIONAL_FIELD_19>>",$flyer_info["optional_field_19"],$this->template);
			if ($show_page->MODULE_DISPLAY_OPTIONAL_FIELD_20)
				$this->template = str_replace("<<OPTIONAL_FIELD_20>>",$flyer_info["optional_field_20"],$this->template);
			if ($show_page->MODULE_DISPLAY_PRICE)
				$this->template = str_replace("<<PRICE>>",$flyer_info["price"],$this->template);
			if ($show_page->MODULE_DISPLAY_CONTACT)
				$this->template = str_replace("<<CONTACT>>",$flyer_info["contact"],$this->template);
			if ($show_page->MODULE_DISPLAY_PHONE1)
				$this->template = str_replace("<<PHONE_1>>",$flyer_info["phone_1"],$this->template);
			if ($show_page->MODULE_DISPLAY_PHONE2)
				$this->template = str_replace("<<PHONE_2>>",$flyer_info["phone_2"],$this->template);
			if ($show_page->MODULE_DISPLAY_CLASSIFIED_ID)
				$this->template = str_replace("<<CLASSIFIED_ID>>",$flyer_info["classified_id"],$this->template);
			if ($show_page->MODULE_DISPLAY_AD_DESCRIPTION)
				$this->template = str_replace("<<DESCRIPTION>>",stripslashes(stripslashes(urldecode($flyer_info["description"]))),$this->template);
			echo $this->template;
			exit;
			return true;
		}
		else
			return false;

	} //end of function display_sellers_flyer

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_signs_and_flyers_user_image($db,$classified_id=0,$type=0)
	{
		//echo $classified_id." is id<br>\n";
		//echo $type." is type<br>\n";
		if (($classified_id) && ($type))
		{
			$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$classified_id." and display_order = 1";
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);

			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() ==1)
			{
				$show_urls = $result->FetchNextObject();
				$this->get_ad_configuration($db);
				if ($type == "sign")
				{
					if (($this->ad_configuration->SIGN_MAXIMUM_IMAGE_WIDTH >= $show_urls->ORIGINAL_IMAGE_WIDTH) &&
					($this->ad_configuration->SIGN_MAXIMUM_IMAGE_HEIGHT >= $show_urls->ORIGINAL_IMAGE_HEIGHT))
					{
						$image_tag = "<img src=".$show_urls->IMAGE_URL." width=".$show_urls->ORIGINAL_IMAGE_WIDTH." height=".$show_urls->ORIGINAL_IMAGE_HEIGHT.">";
					}
					else
					{
						if (($show_urls->ORIGINAL_IMAGE_WIDTH > $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_WIDTH) &&
						($show_urls->ORIGINAL_IMAGE_HEIGHT > $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_HEIGHT))
						{
							$imageprop = ($this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_WIDTH * 100) / $show_urls->ORIGINAL_IMAGE_WIDTH;
							$imagevsize = ($show_urls->ORIGINAL_IMAGE_HEIGHT * $imageprop) / 100 ;
							$final_image_width = $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_WIDTH;
							$final_image_height = ceil($imagevsize);

							if ($final_image_height > $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_HEIGHT)
							{
								$imageprop = ($this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_HEIGHT * 100) / $show_urls->ORIGINAL_IMAGE_HEIGHT;
								$imagehsize = ($show_urls->ORIGINAL_IMAGE_WIDTH * $imageprop) / 100 ;
								$final_image_height = $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_HEIGHT;
								$final_image_width = ceil($imagehsize);
							}
						}
						elseif ($show_urls->ORIGINAL_IMAGE_WIDTH > $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_WIDTH)
						{
							$imageprop = ($this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_WIDTH * 100) / $show_urls->ORIGINAL_IMAGE_WIDTH;
							$imagevsize = ($show_urls->ORIGINAL_IMAGE_HEIGHT * $imageprop) / 100 ;
							$final_image_width = $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_WIDTH;
							$final_image_height = ceil($imagevsize);
						}
						elseif ($show_urls->ORIGINAL_IMAGE_HEIGHT > $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_HEIGHT)
						{
							$imageprop = ($this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_HEIGHT * 100) / $show_urls->ORIGINAL_IMAGE_HEIGHT;
							$imagehsize = ($show_urls->ORIGINAL_IMAGE_WIDTH * $imageprop) / 100 ;
							$final_image_height = $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_HEIGHT;
							$final_image_width = ceil($imagehsize);
						}
						else
						{
							$final_image_width = $show_urls->ORIGINAL_IMAGE_WIDTH;
							$final_image_height = $show_urls->ORIGINAL_IMAGE_HEIGHT;
						}
						$image_tag = "<img src=".$show_urls->IMAGE_URL." width=".$final_image_width." height=".$final_image_height.">";
					}
				}
				elseif ($type == "flyer")
				{
					if (($this->ad_configuration->FLYER_MAXIMUM_IMAGE_WIDTH >= $show_urls->ORIGINAL_IMAGE_WIDTH) &&
					($this->ad_configuration->FLYER_MAXIMUM_IMAGE_HEIGHT >= $show_urls->ORIGINAL_IMAGE_HEIGHT))
					{
						$image_tag = "<img src=".$show_urls->IMAGE_URL." width=".$show_urls->ORIGINAL_IMAGE_WIDTH." height=".$show_urls->ORIGINAL_IMAGE_HEIGHT.">";
					}
					else
					{
						if (($show_urls->ORIGINAL_IMAGE_WIDTH > $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_WIDTH) &&
						($show_urls->ORIGINAL_IMAGE_HEIGHT > $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_HEIGHT))
						{
							$imageprop = ($this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_WIDTH * 100) / $show_urls->ORIGINAL_IMAGE_WIDTH;
							$imagevsize = ($show_urls->ORIGINAL_IMAGE_HEIGHT * $imageprop) / 100 ;
							$final_image_width = $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_WIDTH;
							$final_image_height = ceil($imagevsize);

							if ($final_image_height > $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_HEIGHT)
							{
								$imageprop = ($this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_HEIGHT * 100) / $show_urls->ORIGINAL_IMAGE_HEIGHT;
								$imagehsize = ($show_urls->ORIGINAL_IMAGE_WIDTH * $imageprop) / 100 ;
								$final_image_height = $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_HEIGHT;
								$final_image_width = ceil($imagehsize);
							}
						}
						elseif ($show_urls->ORIGINAL_IMAGE_WIDTH > $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_WIDTH)
						{
							$imageprop = ($this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_WIDTH * 100) / $show_urls->ORIGINAL_IMAGE_WIDTH;
							$imagevsize = ($show_urls->ORIGINAL_IMAGE_HEIGHT * $imageprop) / 100 ;
							$final_image_width = $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_WIDTH;
							$final_image_height = ceil($imagevsize);
						}
						elseif ($show_urls->ORIGINAL_IMAGE_HEIGHT > $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_HEIGHT)
						{
							$imageprop = ($this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_HEIGHT * 100) / $show_urls->ORIGINAL_IMAGE_HEIGHT;
							$imagehsize = ($show_urls->ORIGINAL_IMAGE_WIDTH * $imageprop) / 100 ;
							$final_image_height = $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_HEIGHT;
							$final_image_width = ceil($imagehsize);
						}
						else
						{
							$final_image_width = $show_urls->ORIGINAL_IMAGE_WIDTH;
							$final_image_height = $show_urls->ORIGINAL_IMAGE_HEIGHT;
						}
						$image_tag = "<img src=".$show_urls->IMAGE_URL." width=".$final_image_width." height=".$final_image_height.">";
					}
				}
				else
					return false;
			}
			else
			{
				$this->sql_query = "select * from ".$this->images_table." where classified_id = ".$classified_id." and display_order = 1";
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);

				if (!$result)
				{
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show_uploaded = $result->FetchNextObject();
					$this->images_to_display[1]["type"] = 2;
					$this->images_to_display[1]["id"] = $show_uploaded->IMAGE_ID;
					$this->images_to_display[1]["original_image_width"] = $show_uploaded->ORIGINAL_IMAGE_WIDTH;
					$this->images_to_display[1]["original_image_height"] = $show_uploaded->ORIGINAL_IMAGE_HEIGHT;
					$this->images_to_display[1]["image_file"] = $show_uploaded->IMAGE_FILE;
					$this->images_to_display[1]["classified_id"] = $show_uploaded->CLASSIFIED_ID;
					$this->get_ad_configuration($db);

					if ($type == "sign")
					{
						if (($this->ad_configuration->SIGN_MAXIMUM_IMAGE_WIDTH >= $show_uploaded->ORIGINAL_IMAGE_WIDTH) &&
						($this->ad_configuration->SIGN_MAXIMUM_IMAGE_HEIGHT >= $show_uploaded->ORIGINAL_IMAGE_HEIGHT))
						{
							$image_tag = "<img src=get_image.php?image=".$show_uploaded->IMAGE_ID." width=".$show_uploaded->ORIGINAL_IMAGE_WIDTH." height=".$show_uploaded->ORIGINAL_IMAGE_HEIGHT.">";
						}
						else
						{
							if (($show_uploaded->ORIGINAL_IMAGE_WIDTH > $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_WIDTH) &&
							($show_uploaded->ORIGINAL_IMAGE_HEIGHT > $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_HEIGHT))
							{
								$imageprop = ($this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_WIDTH * 100) / $show_uploaded->ORIGINAL_IMAGE_WIDTH;
								$imagevsize = ($show_uploaded->ORIGINAL_IMAGE_HEIGHT * $imageprop) / 100 ;
								$final_image_width = $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_WIDTH;
								$final_image_height = ceil($imagevsize);

								if ($final_image_height > $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_HEIGHT)
								{
									$imageprop = ($this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_HEIGHT * 100) / $show_uploaded->ORIGINAL_IMAGE_HEIGHT;
									$imagehsize = ($show_uploaded->ORIGINAL_IMAGE_WIDTH * $imageprop) / 100 ;
									$final_image_height = $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_HEIGHT;
									$final_image_width = ceil($imagehsize);
								}
							}
							elseif ($show_uploaded->ORIGINAL_IMAGE_WIDTH > $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_WIDTH)
							{
								$imageprop = ($this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_WIDTH * 100) / $show_uploaded->ORIGINAL_IMAGE_WIDTH;
								$imagevsize = ($show_uploaded->ORIGINAL_IMAGE_HEIGHT * $imageprop) / 100 ;
								$final_image_width = $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_WIDTH;
								$final_image_height = ceil($imagevsize);
							}
							elseif ($show_uploaded->ORIGINAL_IMAGE_HEIGHT > $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_HEIGHT)
							{
								$imageprop = ($this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_HEIGHT * 100) / $show_uploaded->ORIGINAL_IMAGE_HEIGHT;
								$imagehsize = ($show_uploaded->ORIGINAL_IMAGE_WIDTH * $imageprop) / 100 ;
								$final_image_height = $this->ad_configuration_data->SIGN_MAXIMUM_IMAGE_HEIGHT;
								$final_image_width = ceil($imagehsize);
							}
							else
							{
								$final_image_width = $show_uploaded->ORIGINAL_IMAGE_WIDTH;
								$final_image_height = $show_uploaded->ORIGINAL_IMAGE_HEIGHT;
							}
							$image_tag = "<img src=get_image.php?image=".$show_uploaded->IMAGE_ID." width=".$final_image_width." height=".$final_image_height.">";
						}
					}
					elseif ($type == "flyer")
					{
						if (($this->ad_configuration->FLYER_MAXIMUM_IMAGE_WIDTH >= $show_uploaded->ORIGINAL_IMAGE_WIDTH) &&
						($this->ad_configuration->FLYER_MAXIMUM_IMAGE_HEIGHT >= $show_uploaded->ORIGINAL_IMAGE_HEIGHT))
						{
							$image_tag = "<img src=get_image.php?image=".$show_uploaded->IMAGE_ID." width=".$show_uploaded->ORIGINAL_IMAGE_WIDTH." height=".$show_uploaded->ORIGINAL_IMAGE_HEIGHT.">";
						}
						else
						{
							if (($show_uploaded->ORIGINAL_IMAGE_WIDTH > $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_WIDTH) &&
							($show_uploaded->ORIGINAL_IMAGE_HEIGHT > $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_HEIGHT))
							{
								$imageprop = ($this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_WIDTH * 100) / $show_uploaded->ORIGINAL_IMAGE_WIDTH;
								$imagevsize = ($show_uploaded->ORIGINAL_IMAGE_HEIGHT * $imageprop) / 100 ;
								$final_image_width = $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_WIDTH;
								$final_image_height = ceil($imagevsize);

								if ($final_image_height > $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_HEIGHT)
								{
									$imageprop = ($this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_HEIGHT * 100) / $show_uploaded->ORIGINAL_IMAGE_HEIGHT;
									$imagehsize = ($show_uploaded->ORIGINAL_IMAGE_WIDTH * $imageprop) / 100 ;
									$final_image_height = $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_HEIGHT;
									$final_image_width = ceil($imagehsize);
								}
							}
							elseif ($show_uploaded->ORIGINAL_IMAGE_WIDTH > $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_WIDTH)
							{
								$imageprop = ($this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_WIDTH * 100) / $show_uploaded->ORIGINAL_IMAGE_WIDTH;
								$imagevsize = ($show_uploaded->ORIGINAL_IMAGE_HEIGHT * $imageprop) / 100 ;
								$final_image_width = $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_WIDTH;
								$final_image_height = ceil($imagevsize);
							}
							elseif ($show_uploaded->ORIGINAL_IMAGE_HEIGHT > $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_HEIGHT)
							{
								$imageprop = ($this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_HEIGHT * 100) / $show_uploaded->ORIGINAL_IMAGE_HEIGHT;
								$imagehsize = ($show_uploaded->ORIGINAL_IMAGE_WIDTH * $imageprop) / 100 ;
								$final_image_height = $this->ad_configuration_data->FLYER_MAXIMUM_IMAGE_HEIGHT;
								$final_image_width = ceil($imagehsize);
							}
							else
							{
								$final_image_width = $show_uploaded->ORIGINAL_IMAGE_WIDTH;
								$final_image_height = $show_uploaded->ORIGINAL_IMAGE_HEIGHT;
							}
							$image_tag = "<img src=get_image.php?image=".$show_uploaded->IMAGE_ID." width=".$final_image_width." height=".$final_image_height.">";
						}
					}
					else
						return false;

				}
			}
		}
		else
		{
			return false;
		}
		return $image_tag;
	} //end of function get_signs_and_flyers_user_image

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function signs_and_flyers_list($db)
	{
		$this->page_id = 72;
		$this->get_text($db);
		if ($this->classified_user_id)
		{
			$this->sql_query = "select * from ".$this->classifieds_table." where seller = ".$this->classified_user_id." and live = 1 order by date desc";
			//$this->body .=$this->sql_query."<br>";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n\t";
				$this->body .="<tr class=section_title><td valign=top width=100% colspan=9>\n\t\t".urldecode($this->messages[1172])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=page_title>\n\t\t<td colspan=9>\n\t\t".urldecode($this->messages[1173])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t\t<td colspan=9>\n\t\t".urldecode($this->messages[1174])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=table_column_header>\n\t\t<td>\n\t\t".urldecode($this->messages[1175])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[1176])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[1177])."\n\t\t</td>\n\t\t";
				$this->body .="\n\t</tr>\n\t";

				$this->row_count = 0;
				while ($show = $result ->FetchNextObject())
				{
					$css_tag = $this->get_row_color(2);
					if (($this->row_count % 2) == 0)
						$css_tag = "result_set_even_rows";
					else
						$css_tag = "result_set_odd_rows";
					$this->body .="<tr class=".$css_tag.">\n\t\t<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show->ID." class=".$css_tag.">".urldecode($show->TITLE)."</a>\n\t\t</td>\n\t\t";
					$this->body .="<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=12&c=".$show->ID." class=action_links>".urldecode($this->messages[1178])."</a>\n\t\t</td>\n\t\t";
					$this->body .="<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=13&c=".$show->ID." class=action_links>".urldecode($this->messages[1179])."</a>\n\t\t</td>\n\t</tr>\n\t";
					$this->row_count++;
				}// end of while

				$this->body .="</td>\n\t</tr>\n\t";
				$this->body .="<tr class=user_management_home_link>\n\t<td colspan=5>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[1180])."</a>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
			}
			else
			{
				//there are no current ads for this user
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
				$this->body .="<tr class=section_title><td valign=top width=100%>\n\t\t".urldecode($this->messages[1172])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=page_title>\n\t\t<td colspan=5>\n\t\t".urldecode($this->messages[1173])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=error_message>\n\t<td>\n\t".urldecode($this->messages[1181])."</td>\n\t</tr>\n\t";
				$this->body .="<tr class=user_management_home_link>\n\t<td>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[1180])."</a>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
			}
			$this->display_page($db);
			return true;
		}
		else
		{
			//no user id
			$this->error_message = $this->data_missing_error_message;
			return false;
		}
	} //end of function list_current_ads

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_category_questions($db,$classified_id=0)
	{
		//echo count($this->category_questions)." is the count of category questions<br>\n";
		if ((count($this->category_questions) > 0) && ($classified_id))
		{
			//$category_questions = array_reverse($category_questions);  //puts question in order of general to specific

			$this->body .="<tr class=field_labels>\n\t<td colspan=2 >".urldecode($this->messages[404])."</td>\n</tr>\n";
			$this->body .="<tr class=field_labels>\n\t<td colspan=2 >".urldecode($this->messages[405])."</td>\n</tr>\n";
			//asort($this->category_questions); //crutch
			foreach ($this->category_questions as $key => $value)
			{
				$this->sql_query = "select * from ".$this->classified_extra_table." where classified_id = ".$classified_id." and question_id = ".$key;
				$classified_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<bR>\n";
				if (!$classified_result)
				{
					return false;
				}
				elseif ($classified_result->RecordCount() == 1)
				{
					$show_question_value = $classified_result->FetchNextObject();
				}
				else
					$show_question_value = 0;
				//spit out the questions
				$this->body .="<tr>\n\t<td class=field_labels>".$this->category_questions[$key]."\n\t</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				//$this->body .=$this->category_choices[$key]." is category choices ".$key." <br>\n\t";
				if (($this->category_choices[$key] == "none") || ($this->category_choices[$key] == "url"))
				{
					//spit out the normal input tag if there are no choices for this question
					$this->body .="<input class=data_fields type=text name=d[question_value][".$key."] value=\"".urldecode($show_question_value->VALUE)."\" length=30 maxlength=100>\n\t";
				}
				elseif ($this->category_choices[$key] == "textarea")
				{
					$this->body .="\n\t<textarea name=d[question_value][".$key."] cols=60 rows=15 class=data_fields ";
					if ($this->ad_configuration_data->TEXTAREA_WRAP)
					{
						$this->body .= "wrap=virtual>";
						$this->body .= eregi_replace('<BR[[:space:]]*/?[[:space:]]*>',"",stripslashes(urldecode($show_question_value->VALUE)));
					}
					else
					{
						$this->body .= "wrap=soft>";
						$this->body .= stripslashes(urldecode($show_question_value->VALUE));
					}
					$this->body .=" </textarea>";
				}
				elseif ($this->category_choices[$key] == "check")
				{
					//display a checkbox

					$this->body .= "<input  class=data_fields type=checkbox name=d[question_value][".$key."] value=\"".urldecode($this->category_questions[$key])."\" ";
					//if ($show_question_value->VALUE == urldecode($this->category_questions[$key]))
					$this->category_questions[$key] = str_replace("\n"," ",urldecode($this->category_questions[$key]));
					$value = str_replace("\n"," ",urldecode($show_question_value->VALUE));
					if (strcmp(trim($value),trim($this->category_questions[$key])) == 0)
						$this->body .= "checked";
					$this->body .= ">";
				}
				else
				{
					//get the list of choices for this question
					$this->sql_query = "SELECT * FROM ".$this->sell_choices_table." WHERE type_id = \"".$this->category_choices[$key]."\" ORDER BY display_order";
					//echo $this->sql_query." is the query to get sell_choices<br>\n";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						return false;
					}
					elseif ($result->RecordCount() > 0)
					{
						$this->body .="<select  class=data_fields name=d[question_value][".$key."]>\n\t\t";
						$this->body .="<option></option>\n\t\t";
						$matched = 0;
						while ($show_choices = $result->FetchNextObject())
						{
							//put choices in options of this select statement
							$this->body .="<option ";
							$show_question = str_replace("\n"," ",urldecode($show_question_value->VALUE));
							$value = str_replace("\n"," ",urldecode($show_choices->VALUE));
							//if (urldecode($show_question_value->VALUE) == urldecode($show_choices->VALUE))
							if (strcmp(trim($show_question),trim($value)) == 0)
							{
								$this->body .= "selected";
								$matched = 1;
							}
							$this->body .=">".urldecode($show_choices->VALUE)."</option>\n\t\t";
						}
						$this->body .="</select>\n\t";
					}
					if ($this->category_other_box[$key] == 1)
					{
						$this->body .=urldecode($this->messages[406])."<input type=text size=12 maxlength=50 name=d[question_value_other][".$key."] value=\"";
						if (!$matched)
							$this->body .= urldecode($show_question_value->VALUE);
						$this->body .= "\">";
					}
				} //end of if $category_questions[$i]["choices"] != "none"
				//$this->body .= "<a href=\"message.php\" onmouseover=\"window.status='explanation to ".$category_questions[$key]."';  return true;\" onmouseout=\"window.status=''; return true;\" onClick='enterWindow=window.open(\"message.php?msg=".urlencode($category_explanation[$key])."&msgheader=Explanation\",\"Explanation to ".$category_questions[$key]."\",\"width=300,height=150,top=50,left=100,resizeable=no\"); return false'>explanation</a>";
				//if (strlen(trim($this->category_explanation[$key])) > 0)
				//{
				//	$this->body .=$this->category_explanation[$key]." is the explanation<bR>\n";
				//	$this->body .=$this->display_help_link($this->category_choices[$key],1);
				//}
				$this->body .="</td>\n</tr>\n";

			} // end of while
		} //end of if (count($category_questions) > 0)
	} //end of function display_category_questions

//##################################################################################

	function display_group_questions($db,$classified_id=0)
	{
		//echo count($this->group_questions)." is the count of category questions<br>\n";
		//echo $classified_id." is classified id<bR>\n";
		if ((count($this->group_questions) > 0) && ($classified_id))
		{
			//$category_questions = array_reverse($category_questions);  //puts question in order of general to specific

			$this->body .="<tr>\n\t<td colspan=2  class=field_labels>".urldecode($this->messages[404])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td colspan=2 class=field_labels>".urldecode($this->messages[405])."</td>\n</tr>\n";
			//asort($this->category_questions); //crutch
			foreach ($this->group_questions as $key => $value)
			{
				$this->sql_query = "select * from ".$this->classified_extra_table." where classified_id = ".$classified_id." and group_id != 0 and question_id = ".$key;
				$classified_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<bR>\n";
				if (!$classified_result)
				{
					return false;
				}
				elseif ($classified_result->RecordCount() == 1)
				{
					$show_question_value = $classified_result->FetchNextObject();
				}
				else
					$show_question_value = 0;
				//spit out the questions
				$this->body .="<tr>\n\t<td  class=field_labels >".$this->group_questions[$key]."\n\t</td>\n\t";
				$this->body .="<td class=data_fields>\n\t";
				//$this->body .=$this->category_choices[$key]." is category choices ".$key." <br>\n\t";
				//echo $this->group_choices[$key]." is group choices and ".$key."<bR>\n";
				if (($this->group_choices[$key] == "none")  || ($this->group_choices[$key] == "url"))
				{
					//spit out the normal input tag if there are no choices for this question
					$this->body .="<input class=data_fields type=text name=d[group_value][".$key."] value=\"".urldecode($show_question_value->VALUE)."\" length=30 maxlength=100>\n\t";
				}
				elseif ($this->group_choices[$key] == "check")
				{
					//display a checkbox
					$this->body .= "<input  class=data_fields type=checkbox name=d[group_value][".$key."] value=\"".urldecode($show_question_value->VALUE)."\" ";
					if ($show_question_value->VALUE == $this->group_questions[$key])
						$this->body .= "checked";
					$this->body .= ">".$show_choices->VALUE;
				}
				elseif ($this->group_choices[$key] != "none")
				{
					//get the list of choices for this question
					$this->sql_query = "SELECT * FROM ".$this->sell_choices_table." WHERE type_id = \"".$this->group_choices[$key]."\" ORDER BY display_order";
					//echo $this->sql_query." is the query to get sell_choices<br>\n";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						return false;
					}
					elseif ($result->RecordCount() > 0)
					{
						$this->body .="<select  class=data_fields name=d[group_value][".$key."]>\n\t\t";
						$this->body .="<option></option>\n\t\t";
						while ($show_choices = $result->FetchNextObject())
						{
							//put choices in options of this select statement
							$this->body .="<option ";
							if ($show_question_value->VALUE == $show_choices->VALUE)
								$this->body .="selected";
							$this->body .=">".$show_choices->VALUE."</option>\n\t\t";
						}
						$this->body .="</select>\n\t";
					}
					if ($this->group_other_box[$key] == 1)
						$this->body .=urldecode($this->messages[406])."<input type=text size=12 maxlength=50 name=d[group_value_other][".$key."] value=\"".urldecode($show_question_value->VALUE)."\">";
				} //end of if $category_questions[$i]["choices"] != "none"
				else
				{
					//spit out the normal input tag if there are no choices for this question
					$this->body .="<input  class=data_fields type=text name=d[group_value][".$key."] value=\"".urldecode($show_question_value->VALUE)."\" length=30 maxlength=100>\n\t";
				}
				$this->body .="</td>\n</tr>\n";

			} // end of while
		} //end of if (count($category_questions) > 0)
	} //end of function display_group_questions

//##################################################################################

	function get_group_questions($db,$group_id=0)
	{
		//get sell questions specific to this category
		if ($group_id != 0)
		{
			//get the questions for this category
			$this->sql_query = "SELECT * FROM ".$this->classified_sell_questions_table." WHERE group_id = ".$group_id." ORDER BY display_order";
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			if ($result->RecordCount() > 0)
			{
				//$this->body .="hello from inside a positive results<br>\n";
				while ($get_questions = $result->FetchNextObject())
				{
					//get all the questions for this category and store them in the auction_questions variable
					//$this->body .=$get_questions["question_key"]." is the question key<br>\n";
					$this->group_questions[$get_questions->QUESTION_ID] = $get_questions->NAME;
					$this->group_explanation[$get_questions->QUESTION_ID] = $get_questions->EXPLANATION;
					$this->group_choices[$get_questions->QUESTION_ID] = $get_questions->CHOICES;
					$this->group_other_box[$get_questions->QUESTION_ID] = $get_questions->OTHER_INPUT;

					//$this->body .=$get_questions->CHOICES." is the choices for ".$get_questions->QUESTION_ID."<br>\n\t";
				} //end of while $get_questions = mysql_fetch_array($result)
			} //end of if ($result)

		} //end of if ($group_id != 0)

	} //end of function get_group_questions

//##################################################################################

	function get_ads_extra_values($db,$classified_id)
	{
		$this->sql_query = "select * from ".$this->classified_extra_table." where classified_id = ".$classified_id." and checkbox != 1 order by display_order";
		$special_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<Br>\n";
		if (!$special_result)
		{
			//$this->body .=$this->sql_query." is the query<br>\n";
			$this->error_message = urldecode($this->messages[81]);
			return false;
		}
		elseif ($special_result->RecordCount() > 0 )
		{
			//$this->body .="<tr>\n\t<td>\n\t<table cellpadding=2 cellspacing=1 border=0>\n\t";
			//list the special description fields
			while ($show_special = $special_result->FetchNextObject())
			{
				$this->body .= "<tr><td class=field_labels>".urldecode($show_special->NAME)."</td>";
				if ($this->page_id == 32)
					$this->body .= "<td class=data_fields>".urldecode(urldecode($show_special->VALUE))."<input type=hidden name=d[question_value][".$show_special->QUESTION_ID."] value=\"".urldecode($show_special->VALUE)."\">\n\t";
				else
					$this->body .= "<td class=data_values>".urldecode(urldecode($show_special->VALUE))."</td></tr>\n";
			}
			//$this->body .="</table>\n\t</td>\n</tr>\n";
		}
	} //end of function get_ads_extra_values

//#################################################################################

	function get_ads_extra_checkboxes($db,$classified_id)
	{
		$this->sql_query = "select * from ".$this->classified_extra_table." where classified_id = ".$classified_id." and checkbox = 1 order by display_order";
		$special_result = $db->Execute($this->sql_query);
		//echo $this->sql_query." inside of checkboxes<Br>\n";
		if (!$special_result)
		{
			//$this->body .=$this->sql_query." is the query<br>\n";
			$this->error_message = urldecode($this->messages[81]);
			return false;
		}
		elseif ($special_result->RecordCount() > 0 )
		{
			while ($show_special = $special_result->FetchNextObject())
			{
				$this->body .= "<tr><td class=field_labels>&nbsp;</td><td class=data_values>".urldecode($show_special->NAME)."</td>";
			}
		}
	} //end of function get_ads_extra_checkboxes

//#################################################################################

	function display_group_values($db,$classified_id)
	{
		//$this->body .=count($this->category_questions)." is the count of category questions<br>\n";
		if (count($this->group_questions) > 0)
		{
			//$category_questions = array_reverse($category_questions);  //puts question in order of general to specific

			$this->body .="<tr class=place_an_ad_details_category_fields>\n\t<td colspan=2 >".urldecode($this->messages[404])."</td>\n</tr>\n";
			$this->body .="<tr class=place_an_ad_details_category_fields>\n\t<td colspan=2 >".urldecode($this->messages[405])."</td>\n</tr>\n";
			//asort($this->category_questions); //crutch
			foreach ($this->group_questions as $key => $value)
			{
				//spit out the questions
				$this->body .="<tr class=place_an_ad_details_category_fields>\n\t<td valign=top >".$this->group_questions[$key]."\n\t</td>\n\t";
				$this->body .="<td valign=top>\n\t";
				//$this->body .=$this->category_choices[$key]." is category choices ".$key." <br>\n\t";
				if ($this->group_choices[$key] != "none")
				{
					//get the list of choices for this question
					$this->sql_query = "SELECT * FROM ".$this->sell_choices_table." WHERE type_id = \"".$this->group_choices[$key]."\" ORDER BY display_order";
					//$this->body .=$this->sql_query." is the query to get sell_choices<br>\n";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						return false;
					}
					elseif ($result->RecordCount() > 0)
					{
						$this->body .="<select name=d[question_value][".$key."]>\n\t\t";
						$this->body .="<option></option>\n\t\t";
						while ($show_choices = $result->FetchNextObject())
						{
							//put choices in options of this select statement
							$this->body .="<option ";
							if ($this->classified_variables["question_value"][$key] == $show_choices->VALUE)
								$this->body .="selected";
							$this->body .=">".$show_choices->VALUE."</option>\n\t\t";
						}
						$this->body .="</select>\n\t";
					}
					if ($this->group_other_box[$key] == 1)
						$this->body .=urldecode($this->messages[406])."<input type=text size=12 maxlength=50 name=d[question_value_other][".$key."] value=\"".$this->classified_variables["question_value_other"][$key]."\">";
				} //end of if $category_questions[$i]["choices"] != "none"
				else
				{
					//spit out the normal input tag if there are no choices for this question
					$this->body .="<input type=text name=[question_value][".$key."] value=\"".$this->classified_variables["question_value"][$key]."\" length=30 maxlength=100>\n\t";
				}
				$this->body .="</td>\n</tr>\n";

			} // end of while
		} //end of if (count($category_questions) > 0)
	} //end of function display_group_values

//#################################################################################

	function edit_classified_ad_category($db,$parent_category=0,$classified_id=0,$terminal_or_not=0)
	{
		$this->page_id = 34;
		$this->get_text($db);
		$ad_data = $this->get_classified_data($db,$classified_id);
		if ((($this->classified_user_id) && ($classified_id) && ($ad_data->SELLER == $this->classified_user_id)) || (($this->classified_user_id == 1) && ($classified_id)))
		{
			if ($parent_category)
			{
				$parent_name = $this->get_category_name($db,$parent_category);
				if ($parent_name)
				{
					//$this->sql_query = "select * from ".$this->categories_table." where parent_id = ".$parent_category." order by display_order,category_name";
					if ($this->configuration_data['order_choose_category_by_alpha'])
					{
						$this->sql_query = "select ".$this->categories_table.".category_id,
							".$this->categories_languages_table.".category_name
							from ".$this->categories_table.",".$this->categories_languages_table." where
							parent_id = ".$parent_category." and
							".$this->categories_table.".category_id = ".$this->categories_languages_table.".category_id and
							".$this->categories_languages_table.".language_id = ".$this->language_id;
						switch($ad_data->ITEM_TYPE)
						{
							case 1:
								$this->sql_query .= " AND (".$this->categories_table.".listing_types_allowed = 0 OR ".$this->categories_table.".listing_types_allowed = 1)";
								break;
							case 2:
								$this->sql_query .= " AND (".$this->categories_table.".listing_types_allowed = 0 OR ".$this->categories_table.".listing_types_allowed = 2)";
								break;
							default:
								$this->sql_query .= " AND ".$this->categories_table.".listing_types_allowed = 0";
								break;
						}
						$this->sql_query .= "order by ".$this->categories_languages_table.".category_name";
					}
					else
					{
						$this->sql_query = "select ".$this->categories_table.".category_id,
							".$this->categories_languages_table.".category_name
							from ".$this->categories_table.",".$this->categories_languages_table." where
							parent_id = ".$parent_category." and
							".$this->categories_table.".category_id = ".$this->categories_languages_table.".category_id and
							".$this->categories_languages_table.".language_id = ".$this->language_id;
						switch($ad_data->ITEM_TYPE)
						{
							case 1:
								$this->sql_query .= " AND (".$this->categories_table.".listing_types_allowed = 0 OR ".$this->categories_table.".listing_types_allowed = 1)";
								break;
							case 2:
								$this->sql_query .= " AND (".$this->categories_table.".listing_types_allowed = 0 OR ".$this->categories_table.".listing_types_allowed = 2)";
								break;
							default:
								$this->sql_query .= " AND ".$this->categories_table.".listing_types_allowed = 0";
								break;
						}
						$this->sql_query .= "order by ".$this->categories_table.".display_order,".$this->categories_languages_table.".category_name";
					}
					$sub_result = $db->Execute($this->sql_query);
					if (!$sub_result)
					{
						echo $this->sql_query." is the query<br>\n";
						$this->error_message = urldecode($this->messages[57]);
						return false;
					}
					elseif (($sub_result->RecordCount() > 0) && (!$terminal_or_not))
					{
						$number_of_sub_cats = $sub_result->RecordCount();
						//do this if there are 1 or more subcategory to choose from
						$this->body .="<table cellpadding=3 cellspacing=1 border=0 width=100%>
							<tr class=section_title><td valign=top width=100%>\n\t\t".urldecode($this->messages[630])."\n\t\t</td>\n\t</tr>\n\t";

						$this->body .="<tr class=user_management_page_title>\n\t<td>\n\t".urldecode($this->messages[460])."\n\t</td>\n</tr>\n";
						$this->body .="<tr class=page_description>\n\t<td>\n\t".urldecode($this->messages[461])."\n\t</td>\n</tr>\n";

						$this->body .=  "<tr class=page_description>\n\t<td>\n\t".urldecode($this->messages[1978]).
							urldecode(stripslashes($parent_name->CATEGORY_NAME))."  ".urldecode($this->messages[1979]).
							$number_of_sub_cats." ".urldecode($this->messages[1981])." \n\t</td>\n</tr>\n";
						$this->body .=  "<tr>\n\t<td align=center>\n\t\n\t";
						$this->body .=  "<tr class=page_description>\n\t\t<td>\n\t\t".urldecode($this->messages[1980])."\n\t\t</td>\n\t</tr>\n\t";
						 while ($show_sub_cats = $sub_result->FetchNextObject())
						 {
							//display the subcategories of this parent_category
							$this->body .=  "<tr class=field_label>\n\t\n\t\t<td>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=3&e=".$classified_id."&category_id=".$show_sub_cats->CATEGORY_ID."  class=field_label>".urldecode(stripslashes($show_sub_cats->CATEGORY_NAME))."</a>\n\t\t</td>\n\t</tr>\n\t";
						}
						//need to display this category as a choice here
						if (!$this->configuration_data['place_ads_only_in_terminal_categories'])
						{
							$this->body .=  "<tr class=field_label>\n\t\t<td>\n\t\t".urldecode($this->messages[1982])."
								<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=3&e=".$classified_id."&category_id=".$parent_category."&f=1 class=field_label>".urldecode(stripslashes($parent_name->CATEGORY_NAME))."</a> ".urldecode($this->messages[1983])."\n\t\t</td>\n\t</tr>\n\t";
						}

						//$this->body .=  "\n\t</table>\n\t</td>\n</tr>\n";
						$this->body .="<tr class=edit_ad_home_link>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&e=".$classified_id." class=edit_ad_home_link>
							".urldecode($this->messages[464])."</a>\n\t</td>\n</tr>\n";
						$this->body .=  "</table>\n";
						$this->display_page($db);
						return true;
					}
					else
					{
						//this is the terminal category
						//there are no sub categories underneath it
						//check this is really a category
						if ($this->check_if_category($db,$parent_category))
						{
							//this is a true category id make it the terminal one
							if (!$this->update_classified_ad_category($db,$parent_category,$classified_id))
							{
								return false;
							}
							else
							{
								$this->edit_classified_ad_home($db,$classified_id);
								return true;
							}
						}
						else
						{
							return false;
						}
					}
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				//choose the main category
				//$this->sql_query = "select * from ".$this->categories_table." where parent_id = 0 order by display_order,category_name";
				if ($this->configuration_data['order_choose_category_by_alpha'])
				{
					$this->sql_query = "select ".$this->categories_table.".category_id,
						".$this->categories_languages_table.".category_name
						from ".$this->categories_table.",".$this->categories_languages_table." where
						parent_id = 0 and
						".$this->categories_table.".category_id = ".$this->categories_languages_table.".category_id and
						".$this->categories_languages_table.".language_id = ".$this->language_id."
						order by ".$this->categories_languages_table.".category_name";
				}
				else
				{
					$this->sql_query = "select ".$this->categories_table.".category_id,
						".$this->categories_languages_table.".category_name
						from ".$this->categories_table.",".$this->categories_languages_table." where
						parent_id = 0 and
						".$this->categories_table.".category_id = ".$this->categories_languages_table.".category_id and
						".$this->categories_languages_table.".language_id = ".$this->language_id."
						order by ".$this->categories_table.".display_order,".$this->categories_languages_table.".category_name";
				}
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is the query<br>\n";
				if (!$result)
				{
					echo $this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[57]);
					return false;
				}
				$this->body .=  "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
				$this->body .="<tr class=section_title><td colspan=2 valign=top width=100%>\n\t\t".urldecode($this->messages[630])."\n\t\t</td>\n\t</tr>\n\t";

				$this->body .="<tr class=user_management_page_title>\n\t<td colspan=2 >\n\t".urldecode($this->messages[460])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t<td colspan=2>\n\t".urldecode($this->messages[461])."\n\t</td>\n</tr>\n";

				$this->body .=  "<tr>\n\t<td class=page_description>\n\t".urldecode($this->messages[462])."\n\t</td>\n</tr>\n";
				if ($result->RecordCount() > 0)
				{
					while ($show = $result->FetchNextObject())
					{
						//show all the categories in the option list
						$this->body .=  "<tr class=field_label>\n\t<td>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=3&e=".$classified_id."&category_id=".$show->CATEGORY_ID." class=category_links>".urldecode(stripslashes($show->CATEGORY_NAME))."</a>\n\t</td>\n</tr>\n";
					} //end of while
				}
				else
				{
					$this->body .=  "<tr class=error_message>\n\t<td>\n\tno main categories\n\t</td>\n</tr>\n";
				}
				$this->body .=  "\n\t</td>\n</tr>\n";
				$this->body .="<tr class=edit_ad_home_link>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&e=".$classified_id." class=edit_ad_home_link>
					".urldecode($this->messages[464])."</a>\n\t</td>\n</tr>\n";
				$this->body .=  "</table>\n";
				$this->display_page($db);
				return true;
			}

		}
		else
		{
			//no user_id
			return false;
		}
	} //end of function edit_classified_ad_category

//#####################################################################

	function edit_classified_ad_category_($db,$classified_id=0)
	{
		$this->page_id = 34;
		$this->get_text($db);
		if ((($classified_id) && ($this->check_no_category_specific_plan($db))) || (($classified_id) && ($this->classified_user_id == 1)))		{
			$show = $this->get_classified_data($db,$classified_id);
			if (($show->SELLER == $this->classified_user_id) || ($this->classified_user_id == 1))
			{
				if ($show)
				{
					//show the form to begin editing this classified ad details
					$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&c=3&e=".$classified_id." method=post>\n";
					$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
					$this->body .="<tr class=section_title><td colspan=2 valign=top width=100%>\n\t\t".urldecode($this->messages[630])."\n\t\t</td>\n\t</tr>\n\t";

					$this->body .="<tr class=user_management_page_title>\n\t<td colspan=2 >\n\t".urldecode($this->messages[460])."\n\t</td>\n</tr>\n";
					$this->body .="<tr class=page_description>\n\t<td colspan=2>\n\t".urldecode($this->messages[461])."\n\t</td>\n</tr>\n";

					$this->body .="<tr>\n\t<td class=field_label>\n\t".urldecode($this->messages[462])."\n\t</td>\n\t";
					$this->body .="<td>\n\t";
					$this->get_category_dropdown($db,"d[category]",$show->CATEGORY,1);
					$this->body .="</td>\n</tr>\n";

					$this->body .="<tr class=save_changes_button >\n\t<td colspan=2>\n\t"."<input type=submit name=submit value=\"".urldecode($this->messages[463])."\" class=save_changes_button >\n\t</td>\n</tr>\n";
					$this->body .="<tr class=edit_ad_home_link>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=5&e=".$classified_id.">
						".urldecode($this->messages[464])."</a>\n\t</td>\n</tr>\n";
					$this->body .="</table>\n\t";
					$this->body .="</form>\n";
					$this->display_page($db);
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
				$this->body .="<tr class=user_management_title><td valign=top colspan=2 width=100%>\n\t\t".urldecode($this->messages[634])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=user_management_section_title>\n\t<td colspan=2 >\n\t".urldecode($this->messages[401])."\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
				$this->display_page($db);
				return true;
			}
		}
		else
		{
			return false;
		}

	} //end of function edit_classified_ad_category

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_classified_ad_category($db,$classified_category=0,$classified_id=0)
	{
		$show = $this->get_classified_data($db,$classified_id);
		if (($show->SELLER == $this->classified_user_id) || ($this->classified_user_id == 1))
		{
			if (($classified_id) && ($classified_category) && ($show->CATEGORY != $classified_category))
			{
				$this->sql_query = "update ".$this->classifieds_table."
					set category = ".$classified_category."
					where id = ".$classified_id;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					return false;
				}
				$this->update_category_count($db,$show->CATEGORY);
				$this->update_category_count($db,$classified_category);

				if ($this->configuration_data['admin_email_edit'])
				{
					$subject = "An ad's category has been edited - #".$classified_id;
					$message = "The below ad has been edited:\n\n";
					$message .= $this->configuration_data['classifieds_url']."?a=2&b=".$classified_id;
					if ($this->configuration_data['email_configuration_type'] == 1)
						$message = str_replace("\n\n","\n",$message);
					if ($this->configuration_data['email_header_break'])
						$separator = "\n";
					else
						$separator = "\r\n";
					$from = "From: ".$this->configuration_data['site_email'].$separator."Reply-to: ".$this->configuration_data['site_email'].$separator;
					$additional = "-f".$this->configuration_data['site_email'];
					if ($this->configuration_data['email_configuration'] == 1)
						mail($this->configuration_data['site_email'],$subject,$message,$from,$additional);
					elseif ($this->configuration_data['email_configuration'] == 2)
						mail($this->configuration_data['site_email'],$subject,$message,$from);
					else
						mail($this->configuration_data['site_email'],$subject,$message);
				}
				return true;
			}
			elseif ($show->CATEGORY == $classified_category)
			{
				return true;
			}
			else
			{
				//echo $classified_id." is class id<BR>\n";
				//echo $classified_category." is classified_category<BR>\n";
				//echo $show->CATEGORY." is show->CATEGORY<BR>\n";
				return false;
			}

		}
		else
			return false;

	} //end of function update_classified_ad_category

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_if_category($db,$category=0)
	{
		if ($category)
		{
			//check to see if this number is even a category
			$this->sql_query = "select * from ".$this->categories_table." where category_id = ".$category;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = urldecode($this->messages[57]);
				return false;
			}
			else
			{
				if ($result->RecordCount() == 1)
					return true;
				else
				{
					//$this->body .=$this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[57]);
					return false;
				}
			}
		}
		else
		{
			$this->error_message = urldecode($this->messages[57]);
			return false;
		}
	} //end of function check_if_category

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

//####################+
} //end of class User_management_current_ads
