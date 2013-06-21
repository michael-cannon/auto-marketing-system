<? //user_management_list_bids.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Auction_list_bids extends Site {

	var $auction_id;
	var $auction_user_id;
	var $feedback_messages;
	var $user_data;

	// Debug variables
	var $filename = "user_management_list_bids_auctions.php";
	var $function_name;
	
	var $debug_bids = 0;
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Auction_list_bids($db,$language_id,$auction_user_id,$production_configuration=0)
	{
		$this->Site($db,6,$language_id,$auction_user_id,$production_configuration);
		$this->auction_user_id = $auction_user_id;
		$this->user_data = $this->get_user_data($db, $this->auction_user_id);
	} //end of function Auction_feedback

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_auctions_with_your_bid($db)
	{
		$this->page_id = 10175;
		$this->get_text($db);
		if ($this->auction_user_id)
		{
			$this->function_name = "list_auctions_with_your_bid";
			$this->sql_query = "select distinct(auction_id) from ".$this->bid_table." where bidder = ".$this->auction_user_id." order by time_of_bid desc";
			$bid_result = $db->Execute($this->sql_query);
			if ($this->debug_bids) echo $this->sql_query."<r>\n";
			if($this->configuration_data['debug_user_management'])
			{
				$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "bid_table", "get data from bid table by user id");
			}
			if (!$bid_result)
			{
				if ($this->debug_bids) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($bid_result->RecordCount() > 0)
			{
				$this->body .=  "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
				$this->body .= "<tr class=user_management_page_title>\n\t\t<td colspan=6>\n\t\t".urldecode($this->messages[102787])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "<tr class=page_description>\n\t\t<td colspan=6>\n\t\t".urldecode($this->messages[102788])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "<tr class=table_column_header>\n\t\t<td>\n\t\t".urldecode($this->messages[102789])."\n\t\t</td>\n\t\t";
				$this->body .= "<td>\n\t\t".urldecode($this->messages[102790])."\n\t\t</td>\n\t\t";//auction ends
				$this->body .= "<td>\n\t\t".urldecode($this->messages[102791])."\n\t\t</td>\n\t\t";//bid amount
				$this->body .= "<td>\n\t\t".urldecode($this->messages[102792])."\n\t\t</td>\n\t\t";//bid quantity
				$this->body .= "<td>\n\t\t".urldecode($this->messages[102793])."\n\t\t</td>\n\t\t";//proxy max
				$this->body .= "<td>\n\t\t".urldecode($this->messages[102794])."\n\t\t</td>\n\t\t";//bid status
				$this->body .= "\n\t</tr>\n\t";
				$this->row_count = 0;
				while ($show_list = $bid_result->FetchNextObject())
				{
					$auction_data = $this->get_classified_data($db,$show_list->AUCTION_ID);
					if ($auction_data)
					{
						if (($this->row_count % 2) == 0)
							$css_tag = "result_set_even_rows";
						else
							$css_tag = "result_set_odd_rows";						
						if ($auction_data->AUCTION_TYPE == 1)
						{

							$this->body .= "<tr class=".$css_tag.">\n\t\t<td>\n\t\t
								<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_list->AUCTION_ID.">".
								urldecode($auction_data->TITLE)."</a>";
							if ($auction_data->LIVE == 0)
								$this->body .= " - ".urldecode($this->messages[102795]);
							$this->body .= "\n\t\t</td>\n\t\t";
							$this->body .= "<td>\n\t\t".date(trim($this->configuration_data['entry_date_configuration']),$auction_data->ENDS)."\n\t\t</td>\n\t\t";
							$this->sql_query = "select bid,time_of_bid from ".$this->bid_table." where bidder = ".$this->auction_user_id." and auction_id = ".$show_list->AUCTION_ID." order by time_of_bid desc limit 1";
							$user_bid_result = $db->Execute($this->sql_query);
							if ($this->debug_bids) echo $this->sql_query."<br>\n";
							if($this->configuration_data['debug_user_management'])
							{
								$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "bid_table", "get bid and bid time from bid table by auction id");
							}
							if (!$user_bid_result)
							{
								if ($this->debug_bids) echo $this->sql_query."<br>\n";
								$this->site_error($db->ErrorMsg());
								return false;
							}
							elseif ($user_bid_result->RecordCount() == 1)
							{
								$show_last_bid = $user_bid_result->FetchNextObject();
							}
							$this->body .= "<td align=center>\n\t\t".$this->configuration_data['precurrency'].sprintf("%0.2f",$show_last_bid->BID)." ".$this->configuration_data['postcurrency']."\n\t\t</td>\n\t\t";
							$this->body .= "<td align=center>\n\t\t".$auction_data->QUANTITY."\n\t\t</td>\n\t\t";
							$this->sql_query = "select maxbid,time_of_bid from ".$this->autobid_table." where bidder = ".$this->auction_user_id." and auction_id = ".$show_list->AUCTION_ID;
							$user_maxbid_result = $db->Execute($this->sql_query);
							if ($this->debug_bids) echo $this->sql_query."<br>\n";
							if($this->configuration_data['debug_user_management'])
							{
								$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "bid_table", "get max bid and bid time from bid table by auction id");
							}
							if (!$user_maxbid_result)
							{
								if ($this->debug_bids) echo $this->sql_query."<br>\n";
								$this->site_error($db->ErrorMsg());
								return false;
							}
							elseif ($user_maxbid_result->RecordCount() == 1)
							{
								$show_maxbid = $user_maxbid_result->FetchNextObject();
								$maxbid = $show_maxbid->MAXBID;
							}
							else 
							{
								$maxbid = 0;	
							}
							$this->body .= "<td align=center>\n\t\t";
							if ($maxbid != 0)
								$this->body .= $this->configuration_data['precurrency'].sprintf("%0.2f",$maxbid)." ".$this->configuration_data['postcurrency'];
							else
								$this->body .= " - ";
							$this->body .= "\n\t\t</td>\n\t\t";
							$current_high_bidder = $this->get_high_bidder($db,$show_list->AUCTION_ID);
							
							if ($this->debug_bids)
							{
								echo $current_high_bidder["bidder"]." is the current high bidder<bR>\n";	
								echo $this->auction_user_id." is auction_user_id<bR>\n";
							}
							
							if ($current_high_bidder["bidder"] == $this->auction_user_id)
								$this->body .= "<td>\n\t\t".urldecode($this->messages[102796])."\n\t\t</td>\n\t</tr>\n\t";
							else
								$this->body .= "<td>\n\t\t".urldecode($this->messages[102797])."\n\t\t</td>\n\t</tr>\n\t";
							$this->row_count++;
						}
						else
						{
							//get other possible bids for this dutch auction
							$this->body .= "<tr class=".$css_tag.">\n\t\t<td>\n\t\t
								<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_list->AUCTION_ID.">".
								urldecode($auction_data->TITLE)."</a>";
							if ($auction_data->LIVE == 0)
								$this->body .= " - ".urldecode($this->messages[102795]);
							$this->body .= "\n\t\t</td>\n\t\t";
							$this->body .= "<td>\n\t\t".date(trim($this->configuration_data['entry_date_configuration']),$auction_data->ENDS)."\n\t\t</td>\n\t\t";
							$this->sql_query = "select bid,time_of_bid,quantity from ".$this->bid_table." where bidder = ".$this->auction_user_id." and auction_id = ".$show_list->AUCTION_ID;
							$user_bid_result = $db->Execute($this->sql_query);
							if ($this->debug_bids) echo $this->sql_query."<br>\n";
							if($this->configuration_data['debug_user_management'])
							{
								$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "bid_table", "get max bid, quantity and bid time from bid table by auction id");
							}
							if (!$user_bid_result)
							{
								if ($this->debug_bids) echo $this->sql_query."<br>\n";
								$this->site_error($db->ErrorMsg());
								return false;
							}
							elseif ($user_bid_result->RecordCount() == 1)
							{
								$show_last_bid = $user_bid_result->FetchNextObject();
							}
							$this->body .= "<td align=center>\n\t\t".$this->configuration_data['precurrency'].sprintf("%0.2f",$show_last_bid->BID)." ".$this->configuration_data['postcurrency']."\n\t\t</td>\n\t\t";
							$this->body .= "<td align=center>\n\t\t".$show_last_bid->QUANTITY."\n\t\t</td>\n\t\t";
							$this->body .= "<td align=center>\n\t\t - \n\t\t</td>\n\t\t";
							//check to see if winning anything
							$this->sql_query = "select * from ".$this->bid_table." where auction_id=".$show_list->AUCTION_ID." order by bid desc,time_of_bid asc";
							$dutch_bid_result = $db->Execute($this->sql_query);
							if ($this->debug_bids) echo $this->sql_query."<br>\n";
							if($this->configuration_data['debug_user_management'])
							{
								$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "bid_table", "get data from bid table by auction id");
							}
							if (!$dutch_bid_result)
							{
								if ($this->debug_bids) echo $this->sql_query."<br>\n";
								return false;
							}
							elseif ($dutch_bid_result->RecordCount() > 0)
							{
								$total_quantity = $auction_data->QUANTITY;
								if ($this->debug_bids)
								{
									echo 	"<br>".$total_quantity." is total_quantity<bR>\n";
									echo $dutch_bid_result->RecordCount()." is count of dutch bidders<br>\n";
								}
								
								$final_dutch_bid = 0;
								$quantity_winning = 0;
								$seller_report = "";
								$show_bidder = $dutch_bid_result->FetchNextObject();
								do
								{
									$quantity_bidder_receiving = 0;
									if ( $show_bidder->QUANTITY <= $total_quantity )
									{
										if ($this->debug_bids) 
										{
											echo "quantity bid on is LESS than total quantity<Br>\n";
											echo $show_bidder->QUANTITY." is quantity bid on and ".$total_quantity." is quantity left<Br>\n";
										}
										$quantity_bidder_receiving = $show_bidder->QUANTITY ;
										//if ( $show_bidder->QUANTITY == $total_quantity )
										//{
											//$final_dutch_bid = $show_bidder->BID;
											//echo $final_dutch_bid." is final bid after total = bid quantity<bR>\n";
										//}
										$total_quantity = $total_quantity - $quantity_bidder_receiving;
									}
									else
									{
										if ($this->debug_bids) 
										{
											echo "quantity bid on is GREATER than total quantity<Br>\n";
											echo $show_bidder->QUANTITY." is quantity bid on and ".$total_quantity." is quantity left<Br>\n";
										}										
										$quantity_bidder_receiving = $total_quantity;
										$total_quantity = 0;
										//$final_dutch_bid = $show_bidder->BID;
										//echo $final_dutch_bid." is final bid after total < bid quantity<bR>\n";
									}
									if ($quantity_bidder_receiving)
									{
										if ($this->debug_bids) 
										{
											echo "quantity bidder is receiving: ".$quantity_bidder_receiving."<Br>\n";
											echo $this->auction_user_id." is auction_user_id<br>\n";
											echo $show_bidder->BIDDER." is bidder and their bid: ".$show_bidder->BID."<bR>\n";
										}											
										if ($this->auction_user_id == $show_bidder->BIDDER)
										{
											$quantity_winning = $quantity_bidder_receiving;
											$bid_made = $show_bidder->BID;
											$final_dutch_bid = $show_bidder->BID;
											break;
										}
									}
									if ($this->debug_bids)
									{
										echo $show_bidder->BIDDER." is the cirrent bidder id<BR>\n";
										echo $total_quantity." is total_quantity<bR>\n";
										echo $final_dutch_bid." is final_dutch_bid<Br>\n";
										echo $quantity_winning." is quantity_winning<Br>\n";
										//$show_bidder = $dutch_bid_result->FetchNextObject();
										//echo $show_bidder->BIDDER." is the next bidder id<BR>\n";
									}
								} while (($show_bidder = $dutch_bid_result->FetchNextObject()) && ($total_quantity != 0));
								//} while (($show_bidder = $dutch_bid_result->FetchNextObject()) && ($total_quantity != 0) && ($final_dutch_bid == 0) && ($quantity_winning != 0));
								
								if ($this->debug_bids)
								{
									echo $quantity_winning." is quantity_winning<bR>\n";
								}
								
								if ($quantity_winning)
									$this->body .= "<td>\n\t\t".$quantity_winning." ".urldecode($this->messages[102798])."\n\t\t</td>\n\t</tr>\n\t";
								else
									$this->body .= "<td>\n\t\t".urldecode($this->messages[102799])."\n\t\t</td>\n\t</tr>\n\t";
								$this->row_count++;
							}
						}
					}
				}
				$this->body .= "<tr class=edit_approval_links>\n\t<td colspan=6>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=edit_approval_links>".urldecode($this->messages[102800])."</a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n\t";
			}
			else
			{
				//there are no auction filters for this user
				$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
				$this->body .= "<tr class=user_management_page_title>\n\t\t<td>".urldecode($this->messages[102787])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "<tr class=page_description>\n\t\t<td>\n\t\t".urldecode($this->messages[102801])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "<tr class=edit_approval_links>\n\t<td>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=edit_approval_links>".urldecode($this->messages[102800])."</a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n\t";
			}
			$this->display_page($db);
			return true;
		}
		else
		{
			return false;
		}
	} //end of function list_auctions_with_your_bid

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end of Auction_list_bids
?>