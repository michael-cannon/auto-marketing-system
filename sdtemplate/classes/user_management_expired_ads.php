<? //user_management_expired_ads.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class User_management_expired_ads extends Site
{
	var $debug_expired = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function User_management_expired_ads ($db,$language_id,$classified_user_id=0, $page=0, $product_configuration=0)
	{
		$this->Site($db,6,$language_id,$classified_user_id, $product_configuration);

		if(!$page)
			$this->page_result = 1;
		else
			$this->page_result = $page;
	} //end of function User_management

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_expired_ads ($db)
	{
		$this->page_id = 23;
		$this->get_text($db);
		if ($this->classified_user_id)
		{
			// First we need the number of elements to be returned
			$this->sql_query = "select count(id) as count from ".$this->classifieds_table." where seller = ".$this->classified_user_id." and (live = 0 and renewal_payment_expected != 1 and renewal_payment_expected != 2) and ends < ".$this->shifted_time($db)." order by ends desc ";
			$class_result = $db->Execute($this->sql_query);
			if (!$class_result)
			{
				if ($this->debug_expired)
					echo $this->sql_query."<br>";
				$this->debug_message = "no user data returned";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			else
				$class_count = $class_result->FetchRow();

			$this->sql_query = "select count(id) as count from ".$this->classifieds_expired_table." where seller = ".$this->classified_user_id." order by ad_ended desc ";
			$expired_result = $db->Execute($this->sql_query);
			if (!$expired_result)
			{
				$this->debug_message = "no user data returned";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			else
				$expired_count = $expired_result->FetchRow();

			// Record the total count
			$total_returned = $class_count['count'] + $expired_count['count'];

			// Now run the actual queries
			$this->sql_query = "select * from ".$this->classifieds_table." where seller = ".$this->classified_user_id." and (live = 0 and renewal_payment_expected != 1 and renewal_payment_expected != 2) and ends < ".$this->shifted_time($db)." order by ends desc ";
			if($this->page_result != 1)
			{
				if(($this->page_result-1) * $this->configuration_data['number_of_ads_to_display'] < $class_count['count'])
				{
					$start = ($this->page_result-1) * $this->configuration_data['number_of_ads_to_display'];
					$num_return = $this->configuration_data['number_of_ads_to_display'];
					$this->sql_query .= "limit ".$start.", ".$num_return;
				}
			}
			else
			{
				$this->sql_query .= "limit ".$this->configuration_data['number_of_ads_to_display'];
			}
			if ($this->debug_expired)
				echo $this->sql_query."<br>";
			$newly_closed_result = $db->Execute($this->sql_query);
			if (!$newly_closed_result)
			{
				if ($this->debug_expired)
					echo $this->sql_query."<br>";
				$this->debug_message = "no user data returned";
				$this->error_message = $this->internal_error_message;
				return false;
			}

			if($newly_closed_result->RecordCount() < $this->configuration_data['number_of_ads_to_display'])
			{
				$this->sql_query = "select * from ".$this->classifieds_expired_table." where seller = ".$this->classified_user_id." order by ad_ended desc ";
				if($this->page_result != 1)
				{
					if($newly_closed_result->RecordCount() > 0)
					{
						// Display a few from previous table and from the beginning of this table
						$start = 0;
						$end = $this->configuration_data['number_of_ads_to_display'] - $newly_closed_result->RecordCount();
					}
					else
					{
						// Calculate the number of listings off of the number_of_ads_to_display variable we are
						$offset = $class_count['count'] % $this->configuration_data['number_of_ads_to_display'];

						// Calculate the number of pages used in the first query
						$first_pages = $class_count['count'] / $this->configuration_data['number_of_ads_to_display'];

						// Find how many pages deep we are into the second query
						$current_page = $this->page_result - $first_pages;

						$start = $offset+($current_page * $this->configuration_data['number_of_ads_to_display']);
						$end = $this->configuration_data['number_of_ads_to_display'];
					}

					$this->sql_query .= "limit ".$start.", ".$end;
				}
				else
				{
					$this->sql_query .= "limit ".$this->configuration_data['number_of_ads_to_display'];
				}
				if ($this->debug_expired)
					echo $this->sql_query."<br>";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->debug_message = "no user data returned";
					$this->error_message = $this->internal_error_message;
					return false;
				}
			}

			if (($result &&($result->RecordCount() > 0)) || ($newly_closed_result && ($newly_closed_result->RecordCount() > 0)))
			{
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n\t";
				$this->body .="<tr class=user_management_page_title><td colspan=100% valign=top width=100%>\n\t\t".urldecode($this->messages[628])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=my_expired_ads_title>\n\t\t<td colspan=100%>\n\t\t".urldecode($this->messages[438])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=table_description>\n\t\t<td colspan=100%>\n\t\t".urldecode($this->messages[439])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=table_column_headers >\n\t\t";
				if($this->is_class_auctions())
					$this->body .="<td>\n\t\t".urldecode($this->messages[200003])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[441])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[442])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[443])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[867])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t&nbsp;\n\t\t</td>\n\t";
				if (!defined('ALLOW_COPYING_NEW_LISTING'))
					$allow_copying_new_listing = 0;
				else
					$allow_copying_new_listing = ALLOW_COPYING_NEW_LISTING;
				if($allow_copying_new_listing)
					$this->body .="<td>\n\t\t&nbsp;\n\t\t</td>\n\t";
				$this->body .="</tr>\n\t";

				$this->row_count = 0;
				if($newly_closed_result && $newly_closed_result->RecordCount() > 0)
				{
					while ($show_closed = $newly_closed_result->FetchNextObject())
					{
						// Check if we need to skip this loop
						if($newly_closed_result->RecordCount() == 0)
							break;

						$renew_cutoff = ($show_closed->ENDS - ($this->configuration_data['days_to_renew'] * 86400));
						$renew_postcutoff = ($show_closed->ENDS + ($this->configuration_data['days_to_renew'] * 86400));

						if (($this->row_count % 2) == 0)
							$css_row_tag = "result_set_even_rows";
						else
							$css_row_tag = "result_set_odd_rows";
						$this->body .="<tr class=".$css_row_tag.">\n\t\t";
						if($this->is_class_auctions())
						{
							if($show_closed->ITEM_TYPE == 2)
							{
								if ($this->debug_expired) echo "this is an auction<BR>\n";
								$this->body .="<td>\n\t\t".urldecode($this->messages[200004])."\n\t\t</td>\n\t\t";
							}
							elseif($show_closed->ITEM_TYPE == 1)
							{
								if ($this->debug_expired) echo "this is a classified ad<BR>\n";
								$this->body .="<td>\n\t\t".urldecode($this->messages[200005])."\n\t\t</td>\n\t\t";
							}
							else
							{
								$this->body .="<td>&nbsp;</td>\n\t\t";
							}
						}
						$this->body .="<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2&c=".$show_closed->ID." class=".$css_row_tag.">".urldecode($show_closed->TITLE)." (".$show_closed->ID.")</a>\n\t\t</td>\n\t\t";
						$this->body .="<td>\n\t\t".date("M j, Y - h:i - l",$show_closed->DATE)."\n\t\t</td>\n\t\t";
						$this->body .="<td>\n\t\t".date("M j, Y - h:i - l",$show_closed->ENDS)."\n\t\t</td>\n\t\t";
						$this->body .="<td>\n\t\t";
						if (($this->configuration_data['days_to_renew']) && ($this->shifted_time($db) > $renew_cutoff) && ($this->shifted_time($db) < $renew_postcutoff))
						{
							$this->body .= "<a href=";
							if ($this->configuration_data['use_ssl_in_sell_process'])
								$this->body .= trim($this->configuration_data['classifieds_ssl_url']);
							else
								$this->body .= trim($this->configuration_data['classifieds_file_name']);
							$this->body .= "?a=7&b=".$show_closed->ID."&r=1 class=action_links>".urldecode($this->messages[866])."</a>";
						}
						$this->body .= "</td>\n\t";
						$this->body .="<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2&c=".$show_closed->ID."  class=".$css_row_tag.">".urldecode($this->messages[444])."</a>\n\t\t</td>\n\t\t";

						if (!defined('ALLOW_COPYING_NEW_LISTING'))
							$allow_copying_new_listing = 0;
						else
							$allow_copying_new_listing = ALLOW_COPYING_NEW_LISTING;
						if($allow_copying_new_listing)
							$this->body .= "<td><a href=\"".$this->configuration_data['classifieds_file_name']."?a=1&copy_id=".$show_closed->ID."\">".$this->messages[200177]."</a></td>";
						$this->body .= "</tr>\n\t";

						// If not auctions or classauctions then lets move on
						// did this so that the indentation woudlnt be so deep below
						if(!$this->is_class_auctions() && !$this->is_auctions())
						{
							$this->row_count++;
							continue;
						}

						// Winning bidders
						// Note: Only gets here if it is an auction
						if ($show_closed->CURRENT_BID >= $show_closed->RESERVE_PRICE)
						{

							if ($show_closed->AUCTION_TYPE == 1)
							{
								//display auction winner
								$high_bidder = $this->get_high_bidder($db,$show_closed->ID);
								if ($high_bidder)
								{
									$this->body .= "<tr class=".$css_row_tag."><td>&nbsp;</td><td colspan=100%>";
									$user_info = $this->get_user_data($db, $high_bidder['bidder']);
									$this->body .= $user_info->USERNAME." (".$user_info->EMAIL.") - ".urldecode($show_closed->PRECURRENCY)." ".$high_bidder['bid']." ".urldecode($show_closed->POSTCURRENCY);
									$this->body .= "</td></tr>";
								}
							}
							else
							{
								//display dutch auction winners

								$this->sql_query = "select * from ".$this->bid_table." where auction_id=".$show_closed->ID." order by bid desc,time_of_bid asc";
								$bid_result = $db->Execute($this->sql_query);
								if ($debug_close) echo $this->sql_query."<BR>\n";
								if (($this->configuration_data->DEBUG_BROWSE) || ($this->debug_browse))
								{
									$this->debug_display($this->sql_query, $db, $this->filename,$this->function_name,"bid_table","");
								}
								if (!$bid_result)
								{
									if ($debug_close) echo $this->sql_query."<BR>\n";
									return false;
								}
								elseif ($bid_result->RecordCount() > 0)
								{
									$total_quantity = $show_closed->QUANTITY;
									//echo "total items sold - ".$total_quantity."<br>\n";
									$final_dutch_bid = 0;
									$seller_report = "";
									$this->dutch_bidders = array();
									$show_bidder = $bid_result->FetchNextObject();
									do
									{
										if ($show_bidder->BID > $show->RESERVE_PRICE)
										{
											$quantity_bidder_receiving = 0;
											if ( $show_bidder->QUANTITY <= $total_quantity )
											{
												$quantity_bidder_receiving = $show_bidder->QUANTITY ;
												if ( $show_bidder->QUANTITY == $total_quantity )
												{
													$final_dutch_bid = $show_bidder->BID;
													//echo $final_dutch_bid." is final bid after total = bid quantity<bR>\n";
												}
												$total_quantity = $total_quantity - $quantity_bidder_receiving;
											}
											else
											{
												$quantity_bidder_receiving = $total_quantity;
												$total_quantity = 0;
												$final_dutch_bid = $show_bidder->BID;
												//echo $final_dutch_bid." is final bid after total < bid quantity<bR>\n";
											}
											//echo $total_quantity." is total quantity after bidder - ".$show_bidder->BIDDER."<br>";
											if ($quantity_bidder_receiving)
											{
												$bidder_info = $this->get_user_data($db,$show_bidder->BIDDER);
												$seller_report .= $quantity_bidder_receiving. " - ".$bidder_info->USERNAME." ( ".$bidder_info->EMAIL." ) - ".urldecode($show_closed->PRECURRENCY)." ".$show_bidder->BID." ".urldecode($show_closed->POSTCURRENCY)."<BR>";
											}
										}

									} while (($show_bidder = $bid_result->FetchNextObject()) && ($total_quantity != 0) && ($final_dutch_bid == 0));
									if ($final_dutch_bid == 0)
										$final_dutch_bid = $this->dutch_bidders[$local_key]["bid"];
									if (strlen(trim($seller_report)) > 0)
									{
										$this->body .= "<tr class=".$css_row_tag."><td>&nbsp;</td><td colspan=4>";
										$this->body .= $seller_report;
										$this->body .= "</td></tr>";
									}

								}
								else
								{
									//there are no winning bidders
								}
							}
						}
						$this->row_count++;

						if($this->configuration_data['number_of_ads_to_display'] && ($this->configuration_data['number_of_ads_to_display'] == $this->row_count))
						{
							$skip_second = 1;
							break;
						}
					}// end of while
				}

				if($result && $result->RecordCount() > 0)
				{
					while ($show = $result->FetchNextObject())
					{
						// If we have already displayed enough lets skip these
						if(isset($skip_second))
							break;

						if (($this->row_count % 2) == 0)
							$css_row_tag = "result_set_even_rows";
						else
							$css_row_tag = "result_set_odd_rows";
						$this->body .="<tr class=".$css_row_tag.">\n\t\t";
						if($this->is_class_auctions())
						{
							if($show->ITEM_TYPE == 2)
								$this->body .="<td>\n\t\t".urldecode($this->messages[200004])."\n\t\t</td>\n\t\t";
							elseif($show->ITEM_TYPE == 1)
								$this->body .="<td>\n\t\t".urldecode($this->messages[200005])."\n\t\t</td>\n\t\t";
							else
								$this->body .="<td>&nbsp;</td>\n\t\t";
						}
						$this->body .= "<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2&c=".$show->ID." class=".$css_row_tag.">".urldecode($show->TITLE)."</a>\n\t\t</td>\n\t\t";
						$this->body .="<td>\n\t\t".date("M j, Y - h:i - l",$show->DATE)."\n\t\t</td>\n\t\t";
						$this->body .="<td>\n\t\t".date("M j, Y - h:i - l",$show->AD_ENDED)."\n\t\t</td>\n\t\t";
						$this->body .="<td>\n\t\t&nbsp;";
						$this->body .= "</td>\n\t";
						$this->body .="<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2&c=".$show->ID."  class=".$css_row_tag.">".urldecode($this->messages[444])."</a>\n\t\t</td>\n\t\t</tr>\n\t";
						$this->row_count++;
					}// end of while
				}

				if ($this->configuration_data['number_of_ads_to_display'] < $total_returned)
				{
					//display the link to the next 10
					$number_of_page_results = ceil($total_returned / $this->configuration_data['number_of_ads_to_display']);
					$this->body .="<tr class=\"more_results\">\n\t<td colspan=100%>".urldecode($this->messages[200174])." ";
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
								$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2&page=".$i." class=\"browsing_result_page_links\">".$i."</a> ";
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
										$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2&page=".$page;
										$this->body .= " class=\"browsing_result_page_links\">".$page."</a> ";
									}
								}

							}
							else
							{
								//display the link to the section
								$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2&page=".(($section*10)+1);
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
												$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2&page=".$page_link;
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
											$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2&page=".$page;
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
								$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2&page=".(($section*100)+1);
								$this->body .= " class=\"browsing_result_page_links\">".(($section*100)+1)."</a>";
								if (($section+1) < $number_of_sections)
									$this->body .= " <font class=\"browsing_result_page_links\">...</font> ";
							}
						}
					}
					$this->body .="</td>\n</tr>\n";
				}

				$this->body .="</table>\n\t";
			}
			else
			{
				//there are no expired ads for this user
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
				$this->body .="<tr class=user_management_page_title><td colspan=5 valign=top width=100%>\n\t\t".urldecode($this->messages[628])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=my_expired_ads_title>\n\t\t<td colspan=5>\n\t\t".urldecode($this->messages[438])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=table_description>\n\t<td>\n\t".urldecode($this->messages[440])."</td>\n\t</tr>\n\t";
				//$this->body .="<tr class=user_management_home_link>\n\t<td>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4>".urldecode($this->messages[445])."</a>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
			}

			$this->sql_query = "select * from ".$this->classifieds_table." where  live = 0 and (renewal_payment_expected = 1 or renewal_payment_expected = 2) and ends < ".$this->shifted_time($db)." and customer_approved = 1 and seller = ".$this->classified_user_id." order by date asc";
			$result = $db->Execute($this->sql_query);
			if ($this->debug_expired) echo $this->sql_query."<br>\n";
			if($this->configuration_data->DEBUG_USER_MANAGEMENT)
			{
				$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_table", "get auctions that are active from auctions table");
			}
			if (!$result)
			{
				if ($this->debug_expired)
				{
					echo $db->ErrorMsg()."br>\n";
					echo $this->sql_query."<br>\n";
				}
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n\t";
				$this->body .="<tr class=my_expired_ads_title>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[102854])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=table_description>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[102855])."\n\t\t</td>\n</tr>\n\t\t";
				$this->body .="<tr class=table_column_headers>\n\t<td>\n\t\t".urldecode($this->messages[100441])."\n\t\t</td>\n\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[100443])."\n\t\t</td>\n\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[102856])."\n\t\t</td>\n\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[102785])."\n\t\t</td>\n\t</tr>\n\t";
				while ($show = $result->FetchRow())
				{
					//display the renewed auctions awaiting approval
					$css_tag = $this->get_row_color(2);
					if (($this->row_count % 2) == 0)
						$css_tag = "result_set_even_rows";
					else
						$css_tag = "result_set_odd_rows";
					$this->body .="<tr class=".$css_row_tag.">\n\t\t
						<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2&c=".$show["id"]." class=".$css_row_tag.">".urldecode($show["title"])."</a>\n\t\t</td>\n\t\t";
					$this->body .="<td>\n\t\t";
					if ($show->AD_ENDED)
						$this->body .= date("M j, Y - h:i - l",$show["ad_ended"]);
					else
						$this->body .= date("M j, Y - h:i - l",time());
					$this->body .= "\n\t\t</td>\n\t\t";
					$this->body .= "<td>\n\t\t";
					if ($show->DATE)
						$this->body .= date("M j, Y - h:i - l",$show["date"]);
					else
						$this->body .= date("M j, Y - h:i - l",time());
					$this->body .= "\n\t\t</td>\n\t\t";
					$this->body .= "<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2&c=".$show["id"]."  class=".$css_row_tag.">".urldecode($this->messages[100444])."</a>\n\t\t</td>\n\t\t</tr>";

					$this->row_count++;

				}
				$this->body .= "</table>\n\t";
			}

			// final fee table
			// Displays the final fees for auctions underneath the expired auctions
			$this->sql_query = "select auction_price_plan_id,price_plan_id from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
			$price_plan_result = $db->Execute($this->sql_query);
			if ($this->debug_expired) echo $this->sql_query."<br>\n";
			if(!$price_plan_result)
			{
				if ($this->debug_expired) echo $this->sql_query."<br>\n";
				return false;
			}
			$price_plan_id = $price_plan_result->FetchNextObject();
			$this->row_count = 0;
			if ($price_plan_id->AUCTION_PRICE_PLAN_ID)
				$this->sql_query = "select * from ".$this->price_plans_table." where price_plan_id = ".$price_plan_id->AUCTION_PRICE_PLAN_ID;
			else
				$this->sql_query = "select * from ".$this->price_plans_table." where price_plan_id = ".$price_plan_id->PRICE_PLAN_ID;
			$price_plan_result = $db->Execute($this->sql_query);
			if ($this->debug_expired) echo $this->sql_query."<br>\n";
			if(!$price_plan_result)
			{
				$this->site_error($db->ErrorMsg());
				if ($this->debug_expired) echo $this->sql_query."<br>\n";
				return false;
			}
			$price_plan = $price_plan_result->FetchNextObject();

			if ($this->debug_expired)
			{
				echo $price_plan->CHARGE_PERCENTAGE_AT_AUCTION_END." is CHARGE_PERCENTAGE_AT_AUCTION_END<br>\n";
				echo $price_plan->ROLL_FINAL_FEE_INTO_FUTURE." is ROLL_FINAL_FEE_INTO_FUTURE<br>\n";
			}

			if (($price_plan->CHARGE_PERCENTAGE_AT_AUCTION_END)  && ($price_plan->ROLL_FINAL_FEE_INTO_FUTURE))
			{
				//display the current totals for final fee and auction to total cost of this auction
				$this->sql_query = "select * from ".$this->classifieds_table." where
					seller = ".$this->classified_user_id."
					and final_fee = 1
					and final_fee_transaction_number = 0
					and final_price >= reserve_price
					and final_price != 0
					and ends < ".time();
				$seller_auction_result = $db->Execute($this->sql_query);
				if ($this->debug_expired) echo $this->sql_query."<br>\n";
				if (!$seller_auction_result)
				{
					$this->site_error($db->ErrorMsg());
					if ($this->debug_expired) echo $this->sql_query."<br>\n";
					return false;
				}
				elseif ($seller_auction_result->RecordCount() > 0)
				{
					$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n\t";
					$this->body .="<tr class=final_fee_header>\n\t\t<td colspan=4>\n\t\t".stripslashes(urldecode($this->messages[103074]))."\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=final_fee_table_column_headers>\n\t\t<td>".stripslashes(urldecode($this->messages[103075]))."</td>\n\t<td>".stripslashes(urldecode($this->messages[103076]))."</td>\n\t<td>\n\t".stripslashes(urldecode($this->messages[103077]))."</td>\n\t<td>\n\t".stripslashes(urldecode($this->messages[103078]))."</td>\n\t</td>\n\t";
					while($show_final_fee = $seller_auction_result->FetchRow())
					{
						//check to see that final fee should be charged
						if (($show_final_fee["auction_type"] == 1) && ($show_final_fee["item_type"] == 2))
						{
							//regular auction with only one winner
							$this->sql_query = "select * from ".$this->bid_table." where auction_id = ".$show_final_fee["id"]." order by bid desc limit 1";
							$bid_count_result = $db->Execute($this->sql_query);
							if ($this->debug_expired) echo $this->sql_query."<br>\n";
							if (!$bid_count_result)
							{
								if ($this->debug_expired) echo $this->sql_query."<br>\n";
								$this->error_message = urldecode($this->messages[100081]);
								return false;
							}
							elseif ($bid_count_result->RecordCount() == 1)
							{
								if ($this->debug_expired)
								{
									echo $bid_count_result->RecordCount()." is bid count<BR>\n";
									echo $show_final_fee["final_price"]." > ".$show_final_fee["reserve_price"]."<br>\n";
								}
								if ($show_final_fee["final_price"] >= $show_final_fee["reserve_price"])
								{
									//get final fee percentage
									$this->sql_query = "select charge from ".$this->final_fee_table." where ".
										"low<=".$show_final_fee["final_price"]." AND high>=".$show_final_fee["final_price"]." and price_plan_id = ".$price_plan_id->AUCTION_PRICE_PLAN_ID." ORDER BY charge DESC limit 1";
									$increment_result = $db->Execute($this->sql_query);
									if ($this->debug_expired) echo $this->sql_query."<br>\n";
									if (!$increment_result)
									{
										if ($this->debug_expired) echo $this->sql_query."<br>\n";
										return false;
									}
									elseif($increment_result->RecordCount() == 1)
									{
										$show_increment = $increment_result->FetchNextObject();
										$final_fee_percentage = $show_increment->CHARGE;
									}
									else
									{
										return false;
									}
									if ($final_fee_percentage > 0)
									{
										$final_fee_charge = (($final_fee_percentage/100) * $show_final_fee["final_price"]);
									}
									if($this->debug_expired)
									{
										echo $final_fee_charge." is final fee charge for ".$show_final_fee["id"]."<br>\n";
										echo $final_fee_percentage.' is the final fee percentage<br>';
									}
									$final_fee_total = $final_fee_total + $final_fee_charge;
									if ($this->debug_expired)
									{
										echo $final_fee_charge." was just added to final fee total<br>\n";
									}
								}
							}

							// legend row


							if ($final_fee_total > 0)
							{
								// data rows
								$css_tag = $this->get_row_color(2);
								if (($this->row_count % 2) == 0)
									$css_tag = "final_fee_result_set_even_rows";
								else
									$css_tag = "final_fee_result_set_odd_rows";
								$this->body .="<tr class=".$css_row_tag.">\n\t\t
									<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2&c=".$show_final_fee["id"]." class=".$css_row_tag.">".urldecode($show_final_fee["title"])."</a>\n\t\t</td>\n\t\t";
								$this->body .="<td>\n\t\t".date("M j, Y - h:i - l",$show_final_fee["date"])."\n\t\t</td>\n\t\t";
								$this->body .="<td>\n\t\t".date("M j, Y - h:i - l",$show_final_fee["ends"])."\n\t\t</td>\n\t\t";
								$this->body .= "<td>".urldecode($show_final_fee["precurrency"])." ".$this->print_number($final_fee_total)." ".urldecode($show_final_fee["postcurrency"])."</td></tr>";

								$this->row_count++;
							}
						}
					}
				}
				$this->body .= "</table>\n\t";
			}

			$this->body .= "<table width=100%>\n\t";
			$this->body .="<tr class=user_management_home_link>\n\t<td colspan=5>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4>".urldecode($this->messages[445])."</a>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n\t";

			$this->display_page($db);
			return true;
		}
		else
		{
			//no user id
			$this->error_message = $this->data_missing_error_message;
			return false;
		}
	} //end of function list_expired_ads

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_expired_ad($db,$classified_id=0)
	{
		$this->page_id = 35;
		$this->get_text($db);
		if ($classified_id)
		{
			$this->sql_query = "select * from ".$this->classifieds_expired_table." where id = ".$classified_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($result->RecordCount() > 1 )
			{
				//more than one auction matches
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($result->RecordCount() <= 0)
			{
				//check the live table
				$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$classified_id;
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					//$this->body .=$this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}
				elseif ($result->RecordCount() > 1 )
				{
					//more than one auction matches
					//$this->body .=$this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}
				elseif ($result->RecordCount() <= 0)
				{
					//$this->body .=$this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}
			}
			//the classified ad exists display it
			$show = $result->FetchNextObject();

			$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
			$this->body .="<tr class=user_management_page_title><td colspan=2 width=100% valign=top>\n\t\t".urldecode($this->messages[629])."\n\t\t</td>\n\t</tr>\n\t";
			$this->body .="<tr class=expired_ad_detail_title>\n\t\t<td colspan=2>".urldecode($this->messages[447]);
			$this->body .="<tr class=page_description>\n\t\t<td colspan=2>".urldecode($this->messages[458]);
			$this->body .="</td>\n\t</tr>\n\t";

			$this->classified_ad_detail($db,$show);

			$this->body .="<tr class=back_to_expired_ads_link>\n\t\t<td colspan=2><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2 class=back_to_expired_ads_link>".urldecode($this->messages[456])."</a></td>\n\t</tr>\n\t";
			$this->body .="<tr class=user_management_home_link>\n\t\t<td colspan=2><a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[457])."</a></td>\n\t</tr>\n\t";
			$this->body .="</table>\n";
			$this->display_page($db);
			return true;
		}
		else
		{
			//no id to display
			return false;
		} //end of else
	} //end of function show_expired_ad

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function classified_ad_detail($db,$show)
	{
		if ($show)
		{
			$this->get_ad_configuration($db);
			if (is_numeric($show->CATEGORY))
			{
				$category_tree = $this->get_category_tree($db,$show->CATEGORY);
				reset ($this->category_tree_array);

				if ($category_tree)
				{
					//category tree
					$this->body .="<tr>\n\t<td colspan=2 class=category_tree>\n\t";
					$this->body .=urldecode($this->messages[711]).": <a href=".$this->configuration_data['classifieds_file_name']."?a=5 class=category_tree>".urldecode($this->messages[448])."</a> > ";
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
			$this->body .="<tr class=ad_title>\n\t<td colspan=2>".stripslashes(urldecode($show->TITLE))."\n\t</td>\n</tr>\n";

			if (($this->ad_configuration_data->USE_PRICE_FIELD) && ($show->ITEM_TYPE == 1))
			{
				//classified price
				$this->body .="<tr>\n\t<td valign=top width=35% class=form_labels >".urldecode($this->messages[706])."</td>\n\t";
				$this->body .="<td valign=top width=65% class=data_values>\n\t".stripslashes(urldecode($show->PRICE))."</td>\n</tr>\n";
			}
			elseif (($show->ITEM_TYPE == 2) && ($show->FINAL_PRICE > 0) && ($show->FINAL_PRICE != "0"))
			{
				//auction price
				$this->body .="<tr>\n\t<td valign=top class=form_labels >".urldecode($this->messages[100706])."</td>\n\t";
				$this->body .="<td valign=top  class=data_values>\n\t".stripslashes(urldecode($show->FINAL_PRICE))."</td>\n</tr>\n";
			}

			//classified id
			$this->body .="<tr>\n\t<td valign=top width=35% class=form_labels >".urldecode($this->messages[449])."</td>\n\t";
			$this->body .="<td valign=top width=65% class=data_values>\n\t".$show->ID."\n\t</td>\n</tr>\n";

			//viewed count
			$this->body .="<tr>\n\t<td valign=top width=35% class=form_labels >".urldecode($this->messages[450])."</td>\n\t";
			$this->body .="<td valign=top width=65% class=data_values>\n\t".($show->VIEWED + 1)."\n\t</td>\n</tr>\n";

			//start date
			$start_date = date("M d, Y H:i:s", $show->DATE);
			$this->body .="<tr>\n\t\t<td valign=top width=35% class=form_labels >".urldecode($this->messages[451])."</td>\n\t";
			$this->body .="<td valign=top width=65% class=data_values>\n\t".$start_date."</td>\n\t</tr>\n\t";

			//location
			$this->body .="<tr>\n\t\t<td class=form_labels >".urldecode($this->messages[454])."</td>\n\t";
			$this->body .="<td width=65% class=data_values>\n\t".urldecode($show->LOCATION_COUNTRY)." ".urldecode($show->LOCATION_STATE)." ".$show->LOCATION_ZIP."</td>\n\t</tr>\n\t";

			//ended date
			if ($show->AD_ENDED)
				$ended_date = date("M d, Y H:i:s", $show->AD_ENDED);
			else
				$ended_date = date("M d, Y H:i:s", $show->ENDS);
			$this->body .="<tr>\n\t\t<td width=35% class=form_labels >".urldecode($this->messages[452])." :</td>\n\t";
			$this->body .="<td valign=top width=65% class=data_values>\n\t".$ended_date."</td>\n\t</tr>\n\t";

			//reason ended
			$this->body .="<tr>\n\t\t<td width=35% class=date_reason_ended_field_labels>".urldecode($this->messages[453])." :</td>\n\t";
			$this->body .="<td width=65% class=date_reason_ended_data_values>\n\t".$show->REASON_AD_ENDED."</td>\n\t</tr>\n\t";

			//description
			$this->body .="<tr>\n\t\t<td class=form_labels>".stripslashes(urldecode($this->messages[455]))."</td>";
			$this->body .="<td width=65% class=data_values>".urldecode($show->DESCRIPTION)."</td>\n\t</tr>\n\t";

			if ($show->ITEM_TYPE == 2)
			{
				//get bid history if any
				$this->sql_query = "select * from ".$this->bid_table." where auction_id = ".$show->ID." order by time_of_bid asc";
				$bid_history_result = $db->Execute($this->sql_query);
				if ($this->debug_expired) echo $this->sql_query."<Br>\n";
				if (!$bid_history_result)
				{
					if ($this->debug_expired) echo $this->sql_query."<Br>\n";
					return false;
				}
				elseif ($bid_history_result->RecordCount() > 0)
				{
					//there are bids on this auction and show them
					$this->body .= "<tr><td colspan=2>";
					$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
					$this->body .= "<tr class=expired_auction_detail_bid_history_title>\n\t<td colspan=4>".urldecode($this->messages[103307])."</td>\n</tr>\n";
					$this->body .= "<tr class=expired_auction_bid_column_header>\n\t<td>".urldecode($this->messages[103308])."</td>\n\t";
					$this->body .= "<td>".urldecode($this->messages[103309])."</td>\n\t";
					if($show->AUCTION_TYPE == 2)
					{
						// If dutch auctions show quantity label
						$this->body .= "<td>".urldecode($this->messages[103310])."</td>\n\t";
					}
					$this->body .= "<td>".urldecode($this->messages[103311])."</td>\n</tr>\n\t";
					$this->row_count = 0;
					while ($show_bid = $bid_history_result->FetchNextObject())
					{
						if (($this->row_count % 2) == 0)
							$css_row_tag = "bid_history_result_even";
						else
							$css_row_tag = "bid_history_result_odd";

						$this->body .= "<tr class=".$css_row_tag."><td>".date("H:i - l, M j, Y",$show_bid->TIME_OF_BID)."</td>\n\t";
						$this->body .= "<td>".urldecode($this->configuration_data->PRECURRENCY)." ".$this->print_number($show_bid->BID)." ".urldecode($this->configuration_data->POSTCURRENCY)."</td>\n\t";
						if($show->AUCTION_TYPE == 2)
						{
							// If dutch auctions show quantity
							$this->body .= "<td>".$show_bid->QUANTITY."</td>\n\t";
						}
						$bidder_data = $this->get_user_data($db,$show_bid->BIDDER);
						$this->body .= "<td>".$bidder_data->USERNAME;
						if ($this->auction_user_id == $show->SELLER)
							$this->body .= " (".$bidder_data->EMAIL.")";
						$this->body .= "</td>\n\t</tr>\n";
						$this->row_count++;
					}
					$this->body .= "</table>";
					$this->body .= "</td></tr>";
				}
				else
				{
					//there were no bids for this auction
					$this->body .= "<tr><td colspan=2>";
					$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
					$this->body .= "<tr class=expired_auction_detail_bid_history_title>\n\t<td colspan=3>".urldecode($this->messages[103307])."</td>\n</tr>\n";
					$this->body .= "<tr class=data_values>\n\t<td colspan=3>".urldecode($this->messages[103312])."</td>\n</tr>\n";
					$this->body .= "</table>";
					$this->body .= "</td></tr>";
				}
			}

		}
		else
		{
			return false;
		}

	} //end of function classified_ad_detail

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end

?>