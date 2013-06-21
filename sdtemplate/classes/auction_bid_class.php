<? //auction_bid_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Auction_bid extends Site {

	var $auction_id;
	var $classified_user_id;
	var $bid_error = 0;
	var $bid_success = 0;
	var $auction;
	var $bidder;
	var $dutch_bidders;
	var $winning_dutch_bidder = 0;
	var $dutch_bidder_quantity = 0;
	var $DEBUG_BID = 0;
	var $filename = "auction_bid_class.php";
	var $function_name;
	var $separator;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Auction_bid ($db,$language_id,$classified_user_id,$auction_id,$product_configuration=0)
	{
		$this->Site($db,12,$language_id,$classified_user_id,$product_configuration);
		$this->classified_user_id = $classified_user_id;
		$this->auction_id = $auction_id;
		if ($this->DEBUG_BID)
		{
			$this->body .= $this->classified_user_id . " is classified_user_id<br>\n";
			$this->body .= $this->auction_id." is auction_id<br>\n";
		}
		
		if ($this->configuration_data->EMAIL_HEADER_BREAK)
			$this->separator = "\n";
		else
			$this->separator = "\r\n";		

	} // end of function Auction_bid

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function bid_setup($db,$buy_now=0,$bid_amount=0)
	{
		//if($this->configuration_data['subscription_to_view_or_bid_auctions'])
		//{
			//if($this->check_user_subscription($db))
			//{
				$this->page_id = 10163;
				$this->get_text($db);
				$function_name = "bid_setup";

				//show the form to bid on this auction
				if (($this->classified_user_id) && ($this->auction_id))
				{
					//get auction information
					$this->auction = $this->get_classified_data($db,$this->auction_id);
					$this->bidder = $this->get_user_data($db,$this->classified_user_id);
					
					if ($this->DEBUG_BID)
					{
						echo $this->classified_user_id." is the auction user id<bR>\n";
						echo $this->auction." is the auction<Br>\n";
						echo $this->bidder." is the bidder<Br>\n";
					}
					
					// Find out if buy now auction only
					$buy_now_only = $this->auction->BUY_NOW_ONLY;
					
					if (($this->configuration_data['black_list_of_buyers']) && ($this->configuration_data['invited_list_of_buyers']))
					{
						$invited = $this->check_invitedlist($db,$this->auction->SELLER,$this->classified_user_id);
						$banned = $this->check_blacklist($db,$this->auction->SELLER,$this->classified_user_id);
						if ($this->DEBUG_BID)
						{
							echo $banned." is banned<bR>\n";
							echo $invited." is invited<bR>\n";
						}
						if ($invited == 1)
						{
							if ($this->DEBUG_BID) echo "this buyer is on the invited list<br>";
							$can_bid = 1;
						}
						else
						{
							if ($this->DEBUG_BID) echo "this user is not on the invitedlist<bR>";
							if ($banned)
							{
								if ($this->DEBUG_BID) echo "this buyer is on the blacklist<br>";									
								$can_bid = 0;
							}
							else
							{
								//this user is not on the invited or 
								if ($this->DEBUG_BID)
								{
									echo "this user is not on the blacklist<bR>\n";
									echo "this buyer can bid -1<br>\n";
								}
								if ($invited == 2)
									$can_bid = 1;	
							}
						}						
					}
					elseif ($this->configuration_data['black_list_of_buyers'])
					{
						//check black list only
						if ($this->DEBUG_BID) echo "checking only black list of buyers<BR>\n"; 
						if ($this->check_blacklist($db,$this->auction->SELLER,$this->classified_user_id))
						{
							if ($this->DEBUG_BID) echo "this buyer is on the blacklist 2<br>";										
							$can_bid = 0;
						}								
						else
						{
							if ($this->DEBUG_BID) echo "not on the blacklist so can bid<BR>\n"; 
							$can_bid = 1;	
						}						
					}
					elseif ($this->configuration_data['invited_list_of_buyers'])
					{
						//check invited only
						if ($this->check_invitedlist($db,$this->auction->SELLER,$this->classified_user_id))
						{
							if ($this->DEBUG_BID) echo "this buyer is on the invited list 2<br>";									
							//this user is on the invited list	
							$can_bid = 1;
						}							
					}
					elseif($buy_now_only && !$buy_now)
					{
						// If not buying now and auction is buy now only dont allow to bid
						$can_bid = 0;
					}
					else
						$can_bid = 1;

					if ($this->DEBUG_BID) echo $can_bid." is can_bid before can_bid if <br>\n";
					if ($can_bid)
					{
						if($this->DEBUG_BID)
						{
							echo $this->auction->START_TIME . ' is the start time.<br>';
							echo $this->shifted_time($db) . ' is the time.<br>';
						}
						if($this->auction->START_TIME > $this->shifted_time($db))
						{
							$this->bid_error = 8;
							if($this->DEBUG_BID) echo "start time is greater than current time<bR>\n";
							return false;
						}
						if ($this->auction->AUCTION_TYPE == 2)
						{
							// Dutch Auctions
							$this->sql_query = "select * from ".$this->bid_table." where auction_id=".$this->auction->ID." order by bid desc,time_of_bid asc";
							$bid_result = $db->Execute($this->sql_query);
							if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
							if($this->configuration_data['debug_bid'])
							{
								$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "get data from bid table by auction id");
							}
							if (!$bid_result)
							{
								if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
								return false;
							}
							elseif ($bid_result->RecordCount() > 0)
							{
								$total_quantity = $show_final_fee->QUANTITY;
								$final_dutch_bid = 0;
								$total_quantity_sold = 0;
								$show_bidder = $bid_result->FetchNextObject();
								if ($bid_result->RecordCount() > 0)
								{
									$total_quantity = $show_final_fee->QUANTITY;
									$final_dutch_bid = 0;
									$total_quantity_sold = 0;
									$show_bidder = $bid_result->FetchNextObject();
									do
									{
										$quantity_bidder_receiving = 0;
										if ( $show_bidder->QUANTITY <= $total_quantity )
										{
											$quantity_bidder_receiving = $show_bidder->QUANTITY ;
											if ( $show_bidder->QUANTITY == $total_quantity )
											{
												$final_dutch_bid = $show_bidder->BID;
											}
											$total_quantity = $total_quantity - $quantity_bidder_receiving;
										}
										else
										{
											$quantity_bidder_receiving = $total_quantity;
											$total_quantity = 0;
											$final_dutch_bid = $show_bidder->BID;
										}
										if ($quantity_bidder_receiving)
										{
											$dutch_bidder_bid = $show_bidder->BID;
										}
										$total_quantity_sold = $total_quantity_sold + $quantity_bidder_receiving;
									} while (($show_bidder = $bid_result->FetchNextObject()) && ($total_quantity != 0) && ($final_dutch_bid == 0));
									if ($final_dutch_bid == 0)
										$bid_to_show = $dutch_bidder_bid;
									else
										$bid_to_show = $final_dutch_bid;
								}
								else
								{
									$bid_to_show = $this->get_minimum_bid($db);
								}
	
							}
							elseif ($bid_amount != 0)
								$bid_to_show = $bid_amount;
							else
								$bid_to_show = $this->get_minimum_bid($db);
						}

						if ($this->DEBUG_BID)
						{
							echo $this->auction." is auction<bR>\n";
							echo $this->bidder." is bidder<bR>\n";
							echo $this->auction->LIVE." is auction->LIVE<bR>\n";
						}
						
						if (($this->auction) && ($this->bidder) && ($this->auction->LIVE == 1))
						{
							if ($this->auction->SELLER == $this->bidder->ID)
							{
								$this->bid_error = 4;
								return false;
							}
	
							$this->body .= "<form action=".$this->configuration_data['classifieds_file_name']."?a=1029&b=".$this->auction_id." method=post>\n";
							$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
							$this->body .= "<tr class=page_title>\n\t<td colspan=2>".urldecode($this->messages[102437])."</td>\n</tr>\n";
							$this->body .= "<tr class=display_auction_title>\n\t<td colspan=2>".urldecode($this->auction->TITLE)."</td>\n</tr>\n";
	
							if (($buy_now) && ($this->auction->BUY_NOW))
							{
								$this->body .= "<input type=hidden name=c[bid_amount] value=".$this->auction->BUY_NOW.">";
								$this->body .= "<tr class=page_instructions>\n\t<td colspan=2>".urldecode($this->messages[102442])."</td>\n</tr>\n";
								$this->body .= "<tr>\n\t<td class=info_label width=40%>".urldecode($this->messages[102443])."</td>\n\t";
								$this->body .= "<td class=right_data>".urldecode($this->auction->PRECURRENCY)." ".$this->print_number($this->auction->BUY_NOW)." ".urldecode($this->auction->POSTCURRENCY)."</td></tr>\n";
								$this->body .= "<tr class=submit_button>\n\t<td colspan=2><input type=submit name=c[buy_now_bid] value=\"".urldecode($this->messages[102444])."\">\n\t</td>\n</tr>\n";
								$this->body .= "<tr class=return_to_auction_link>\n\t<td colspan=2><a href=\"".$this->configuration_data['classifieds_url']."?a=2&b=".$this->auction_id."\">".urldecode($this->messages[103055])."</a>\n\t</td>\n</tr>\n";
							}
							elseif ($this->auction->AUCTION_TYPE == 2)
							{
								//dutch auction
								//show quantity bid for
								$this->body .= "<tr class=page_instructions>\n\t<td colspan=2>".urldecode($this->messages[102446])."</td>\n</tr>\n";
								$this->body .= "<tr><td class=info_label width=40%>".urldecode($this->messages[102445])."</td>";
								$this->body .= "<td class=right_data><input type=text size=7 maxsize=7 name=c[bid_quantity] value=1></td></tr>\n";
								$this->body .= "<tr>\n\t<td class=info_label width=40%>".urldecode($this->messages[102440])."</td>\n\t";
								$this->body .= "<td class=right_data>".urldecode($this->auction->PRECURRENCY)." <input type=text name=c[bid_amount] value=\"".$this->print_number($bid_to_show)."\"> ".urldecode($this->auction->POSTCURRENCY)."</td>\n</tr>\n";
								$this->body .= "<tr class=submit_button>\n\t<td colspan=2><input type=submit name=submit value=\"".urldecode($this->messages[102439])."\">\n\t</td>\n</tr>\n";
								$this->body .= "<tr class=return_to_auction_link>\n\t<td colspan=2><a href=\"".$this->configuration_data['classifieds_url']."?a=2&b=".$this->auction_id."\">".urldecode($this->messages[103055])."</a>\n\t</td>\n</tr>\n";
							}
							else
							{
								//get minimum bid amount
								$bid_to_show = $this->get_minimum_bid($db);
								//regular auction type
								$this->body .= "<tr class=page_instructions>\n\t<td colspan=2>".urldecode($this->messages[102438])."</td>\n</tr>\n";
								$this->body .= "<tr>\n\t<td class=info_label width=40%>".urldecode($this->messages[102440])."</td>\n\t";
								$this->body .= "<td class=right_data>".urldecode($this->auction->PRECURRENCY)." <input type=text name=c[bid_amount] value=\"".$this->print_number($bid_to_show)."\"> ".urldecode($this->auction->POSTCURRENCY)."</td>\n</tr>\n";
								$this->body .= "<tr class=submit_button>\n\t<td colspan=2><input type=submit name=submit value=\"".urldecode($this->messages[102439])."\">\n\t</td>\n</tr>\n";
								$this->body .= "<tr class=return_to_auction_link>\n\t<td colspan=2><a href=\"".$this->configuration_data['classifieds_url']."?a=2&b=".$this->auction_id."\">".urldecode($this->messages[103055])."</a>\n\t</td>\n</tr>\n";
							}
	
							$this->body .= "</table>\n</form>\n";
							$this->display_page($db);
							return true;
						}
						else
						{
							//bad bid setup
							return false;
						}
						
					}
					else 
					{
						//this user cannot bid on this auction
						//they are blacklisted or not on the invited list
						//this is a hacking attempt to bypass these features
						return false;	
					}
				}
				else
				{
					//bad initialization setup
					return false;
				}
			//}
			/*else
			{
				$this->error_message = urldecode($this->messages[102782]);
				$this->browse_error($db);
				return true;
			}*/
		//}
	} // end of function bid_setup

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function bid_error($db)
	{
		$this->page_id = 10164;
		$this->get_text($db);

		//not enough information to bid
		$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .= "<tr class=page_title>\n\t<td>".urldecode($this->messages[102457])."</td>\n</tr>\n";
		$this->body .= "<tr class=page_instructions>\n\t<td>";
		switch ($this->bid_error)
		{
			case 1: //current bidder is the high bidder
				$this->body .= urldecode($this->messages[102458]);
				break;
			case 2: //raise bid
				$this->body .= urldecode($this->messages[102459]);
				break;
			case 3: //unrecognizable data for bid amount
				$this->body .= urldecode($this->messages[102460]);
				break;
			case 4: //seller cannot make a bid on their own auction
				$this->body .= urldecode($this->messages[102462]);
				break;
			case 5: //dutch bid quantity error
				$this->body .= urldecode($this->messages[102463]);
				break;
			case 6: //raise dutch bid amount...you are not in the money
				$this->body .= urldecode($this->messages[102464]);
				break;
			case 7: //you cannont lower your dutch bid amount or dutch bid quantity
				$this->body .= urldecode($this->messages[102465]);
				break;
			case 8: //cannot bid before start time
				$this->body .= urldecode($this->messages[102817]);
				break;
			default: //internal bidding error
				$this->body .= urldecode($this->messages[102461]);
				break;
		} //end of switch
		$this->body .= "</td>\n</tr>\n";
		$this->body .= "<tr class=edit_approval_links>\n\t<td colspan=2>
			<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->auction->CATEGORY." class=edit_approval_links>".urldecode($this->messages[102720])."</a></td>\n</tr>\n";
		//back to auction
		$this->body .= "<tr class=edit_approval_links>\n\t<td colspan=2>
			<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$this->auction_id." class=edit_approval_links>".urldecode($this->messages[102721])."</a></td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->display_page($db);
	} // end of function bid_error

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function bid_successful($db)
	{
		$this->page_id = 10165;
		$this->get_text($db);
		$function_name = "bid_successful";

		//successful bid
		$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .= "<tr class=page_title>\n\t<td colspan=2>".urldecode($this->messages[102447])."</td>\n</tr>\n";

		//refresh auction data
		$this->auction = $this->get_classified_data($db,$this->auction_id);
		$this->body .= "<tr>\n\t<td class=info_label width=50%>".urldecode($this->messages[102450])."</td>\n\t";
		$this->body .= "<td class=right_data>".urldecode($this->auction->TITLE)."</td>\n</tr>\n";

		if ($this->auction->AUCTION_TYPE == 2)
		{
			//first check to see if the bid could possibly win
			//dutch auction
			$this->sql_query = "select * from ".$this->bid_table." where auction_id = ".$this->auction_id." and bidder = ".$this->classified_user_id." order by time_of_bid desc limit 1";
			$dutch_bid_result = $db->Execute($this->sql_query);
			if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
			if($this->configuration_data['debug_feedback'])
			{
				$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "get data from bid table by auction id for dutch auction");
			}
			if (!$dutch_bid_result)
			{
				if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($dutch_bid_result->RecordCount() == 1)
			{
				$show_dutch_bid = $dutch_bid_result->FetchNextObject();
			}
			else
			{
				if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
				return false;
			}
			//show quantity bid for
			$this->body .= "<tr><td class=info_label>".urldecode($this->messages[102455])."</td>";
			$this->body .= "<td class=right_data>".$show_dutch_bid->QUANTITY."</td></tr>\n";
			$this->body .= "<tr>\n\t<td class=info_label>".urldecode($this->messages[102441])."</td>\n\t";
			$this->body .= "<td class=right_data>".urldecode($this->configuration_data['precurrency'])." ".$this->print_number($show_dutch_bid->BID)." ".urldecode($this->configuration_data['postcurrency'])."</td>\n</tr>\n";
		}
		else
		{
			$this->body .= "<tr class=page_instructions>\n\t<td colspan=2>";
			switch ($this->bid_success)
			{
				case 1: //you are current high bidder,  your high bid is saved
					$this->body .= urldecode($this->messages[102448]);
					break;
				case 3: //bid received but you have been outbid
					$this->body .= urldecode($this->messages[102449]);
					break;
				case 4: //buy now bid accepted
					$this->body .= urldecode($this->messages[102456]);
				default: //internal bidding error
			} //end of switch
			$this->body .= "</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td class=info_label>".urldecode($this->messages[102451])."</td>\n\t";
			$this->body .= "<td class=right_data>".urldecode($this->auction->PRECURRENCY)." ";
			if ($this->bid_success != 4)
				$this->body .= $this->print_number($this->auction->CURRENT_BID);
			else
				$this->body .= $this->print_number($this->auction->BUY_NOW);
			$this->body .= " ".urldecode($this->auction->POSTCURRENCY)."</td>\n</tr>\n";
		}
		if ($this->bid_success == 3)
		{
			//click here to rebid
			$this->body .= "<tr class=page_instructions>\n\t<td colspan=2>
				<a href=".$this->configuration_data['classifieds_file_name']."?a=1029&b=".$this->auction_id." class=page_instructions>".urldecode($this->messages[102452])."</a></td>\n</tr>\n";
		}
		//catetory
		$this->body .= "<tr class=page_instructions>\n\t<td colspan=2>
			<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->auction->CATEGORY." class=page_instructions>".urldecode($this->messages[102454])."</a></td>\n</tr>\n";
		//back to auction
		$this->body .= "<tr class=page_instructions>\n\t<td colspan=2>
			<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$this->auction_id." class=page_instructions>".urldecode($this->messages[102453])."</a></td>\n</tr>\n";

		$this->body .= "</table>\n";

		$this->display_page($db);
	} // end of function bid_successful

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function process_bid($db,$bid_info=0)
	{
		$function_name = "process_bid";

		if($this->DEBUG_BID)
		{
			echo "<br>TOP OF PROCESS_BID<bR>\n";
			echo $bid_info[bid_amount]." is bid amount at top<br>\n";
			echo $this->auction_id," is the auction id<bR>";
			echo $this->auction->SELLER." is the seller<bR>";
			echo $this->auction_user_id." is the bidder<bR>";
			echo $this->configuration_data->NUMBER_FORMAT." is NUMBER_FORMAT<br>\n";
		}
		
		$bid_info[bid_quantity] = trim($bid_info['bid_quantity']);
		$bid_info[bid_amount] = trim($bid_info['bid_amount']);
		if($this->configuration_data->NUMBER_FORMAT == 0)
		{
			if ($this->DEBUG_BID) echo "american format<BR>\n";
			// American
			$bid_amount=str_replace(",","",$bid_info[bid_amount]);
		}
		elseif($this->configuration_data->NUMBER_FORMAT == 1)
		{
			if ($this->DEBUG_BID) echo "European format<BR>\n";
			// European
			$bid_amount=str_replace(".","",$bid_info[bid_amount]);
			$bid_amount=str_replace(",",".",$bid_amount);
		}
		elseif($this->configuration_data->NUMBER_FORMAT == 2)
		{
			if ($this->DEBUG_BID) echo "japanese format<BR>\n";
			// Japanese...no decimal point
			$bid_amount=str_replace(".","",$bid_info[bid_amount]);
		}		
		else 
		{
			if ($this->DEBUG_BID) echo "default American format<BR>\n";
			// American
			$bid_amount=str_replace(",","",$bid_info[bid_amount]);
		}	
		
		if (!ereg("^[0-9]{1,10}.?[0-9]{0,2}$", $bid_amount))
		{
			$this->bid_error = 3;
			if ($this->DEBUG_BID)
				echo $this->bid_error." 3 - is error<br>\n";
			return false;
		}			
			
		if ($this->DEBUG_BID)
		{
			echo $bid_amount." is bid amount after formatting change<br>\n";
		}

		settype($bid_amount, "float");
		
		if ($this->DEBUG_BID)
		{
			echo $bid_amount." is bid amount after formatting change2<br>\n";
		}		
		
		$this->auction = $this->get_classified_data($db,$this->auction_id);
		$seller = $this->get_user_data($db,$this->auction->SELLER);
		$this->bidder = $this->get_user_data($db,$this->classified_user_id);
		if (($bid_amount) && ($this->auction) && ($this->bidder) && ($seller) && ($this->auction->LIVE == 1))
		{
			if ($seller->ID == $this->bidder->ID)
			{
				$this->bid_error = 4;
				if ($this->configuration_data['debug_bid'])
					echo $this->bid_error." 4 - is error<br>\n";
				return false;
			}
			if (!$quantity)
				$quantity = 1;
			if ($this->auction->AUCTION_TYPE == 2)
			{
				if ($this->DEBUG_BID)
				{
					echo "<BR>THIS IS A DUTCH AUCTION<br>\n";	
				}				
				//dutch auction
				//no autobidding on dutch auctions
				//no reserve price ?
				//save the quantity and the bid amount
				//all the hard work is at the close of a dutch auction
				$bid_info[bid_quantity] = trim($bid_info['bid_quantity']);
				if($this->configuration_data->NUMBER_FORMAT == 0)
				{
					if ($this->DEBUG_BID) echo "american format<BR>\n";
					// American
					$bid_quantity=str_replace(",","",$bid_info[bid_quantity]);
				}
				elseif($this->configuration_data->NUMBER_FORMAT == 1)
				{
					if ($this->DEBUG_BID) echo "European format<BR>\n";
					// European
					$bid_quantity=str_replace(".","",$bid_info[bid_quantity]);
					$bid_quantity=str_replace(",",".",$bid_quantity);
				}
				elseif($this->configuration_data->NUMBER_FORMAT == 2)
				{
					if ($this->DEBUG_BID) echo "japanese format<BR>\n";
					// Japanese...no decimal point
					$bid_quantity=str_replace(".","",$bid_info[bid_quantity]);
				}		
				else 
				{
					if ($this->DEBUG_BID) echo "default American format<BR>\n";
					// American
					$bid_quantity=str_replace(",","",$bid_info[bid_quantity]);
				}	
				
				if (!ereg("^[0-9]{1,10}$", $bid_quantity))
				{
					$this->bid_error = 5;
					if ($this->configuration_data['debug_bid'])
						echo $this->bid_error." 5 - is error<br>\n";
					return false;
				}					

				if ($bid_quantity > $this->auction->QUANTITY)
					$bid_quantity = $this->auction->QUANTITY;
				//check to see if above the minimum bid
				if ($bid_amount < $this->auction->STARTING_BID)
				{
					$this->bid_error = 2;
					if ($this->configuration_data['debug_bid'])
						echo $this->bid_error." 2 - is error<br>\n";
					return false;
				}
				
				if ($this->DEBUG_BID)
				{
					echo $bid_amount." is the amount of the bid entered for this dutch auction<br>\n";	
					echo $bid_quantity." is the quantity entered for this dutch auction<br>\n";
				}

				$this->sql_query = "select * from ".$this->bid_table." where auction_id =".$this->auction_id." and bidder = ".$this->bidder->ID;
				$get_bid_result = $db->Execute($this->sql_query);
				if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
				if($this->configuration_data['debug_bid'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "get data from bid table by auction and bidder id");
				}
				if (!$get_bid_result)
				{
					//No record was found
					if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
					return false;
				}
				elseif ($get_bid_result->RecordCount() == 1)
				{
					//update the bid
					$show_current_dutch_bid = $get_bid_result->FetchNextObject();
					if (($show_current_dutch_bid->BID < $bid_amount) && ($show_current_dutch_bid->QUANTITY <= $bid_quantity ))
					{
						//Bid amount and quantity are greater
						$this->sql_query = "update ".$this->bid_table." set bid = ".$bid_amount.", time_of_bid = ".$this->shifted_time($db).", quantity = ".$bid_quantity." where auction_id = ".$this->auction_id." and bidder = ".$this->bidder->ID;
						$insert_bid_result = $db->Execute($this->sql_query);
						if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
						if($this->configuration_data['debug_bid'])
						{
							$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update data in bid table with auction and bidder id");
						}
						if (!$insert_bid_result)
						{
							if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
							return false;
						}
					}
					else
					{
						//cannot lower your bid amount or quantity
						$this->bid_error = 7;
						if ($this->configuration_data['debug_bid'])
							echo $this->bid_error." 7 - is error<br>\n";
						return false;
					}
				}
				elseif ($get_bid_result->RecordCount() == 0)
				{
					
					//insert the bid
					$this->sql_query = "insert into ".$this->bid_table." (auction_id,bidder,bid,time_of_bid,quantity) values (\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$bid_amount."\",".$this->shifted_time($db).", ".$bid_quantity.")";
					$insert_bid_result = $db->Execute($this->sql_query);
					if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
					if($this->configuration_data['debug_bid'])
					{
						$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table by auction and bidder id");
					}
					if (!$insert_bid_result)
					{
						if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
						return false;
					}
				}
				else
				{
					return false;
				}
				
				if ($this->DEBUG_BID)
				{
					echo "checking to reset the current bid<BR>\n";
					echo $this->auction->CURRENT_BID." is current bid<BR>\n";
					echo $this->auction->RESERVE_PRICE."  is reserve<br>\n";
					echo $bid_amount." is the bid_amount<BR>\n";
					if (($this->auction->CURRENT_BID < $this->auction->RESERVE_PRICE) && ($bid_amount >= $this->auction->RESERVE_PRICE))
						echo "the current bid is below the auction reserve and the bid is above the reserve so the current bid should be reset<BR>\n";
				}
				
				//check to see if this dutch bid is in the money
				$this->sql_query = "select * from ".$this->bid_table." where auction_id=".$this->auction_id." order by bid desc,time_of_bid asc";
				$bid_result = $db->Execute($this->sql_query);
				if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
				if($this->configuration_data['debug_bid'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "get data from bid table by auction");
				}
				if (!$bid_result)
				{
					if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
					return false;
				}
				elseif ($bid_result->RecordCount() > 0)
				{

					$total_quantity = $this->auction->QUANTITY;
					$final_dutch_bid == 0;
					$bid_count = 0;
					$show_bidder = $bid_result->FetchNextObject();
					do
					{
						$quantity_bidder_receiving = 0;
						if ( $show_bidder->QUANTITY <= $total_quantity )
						{
							//Show bidder quantity is less than total quantity
							$quantity_bidder_receiving = $show_bidder->QUANTITY ;
							if ( $show_bidder->QUANTITY == $total_quantity )
							{
								$final_dutch_bid = $show_bidder->BID;
							}
							$total_quantity = $total_quantity - $quantity_bidder_receiving;
						}
						else
						{
							//Show bidder quantity is not less than total quantity
							$quantity_bidder_receiving = $total_quantity;
							$total_quantity = 0;
							$final_dutch_bid = $show_bidder->BID;
						}
						if ($quantity_bidder_receiving)
						{
							//save this bidder as an in the money bidder
							//send an email
							$this->dutch_bidders[$bid_count]["bidder"] = $show_bidder->BIDDER;
							$this->dutch_bidders[$bid_count]["quantity"] = $quantity_bidder_receiving;
							$this->dutch_bidders[$bid_count]["bid"] =  $show_bidder->BID;
							$bid_count++;
							if ($show_bidder->BIDDER == $this->bidder->ID)
							{
								//this bidder is in the money
								$this->winning_dutch_bidder = 1;
								$this->dutch_bidder_quantity = $quantity_bidder_receiving;
								$this->winning_dutch_bidder_count = $bid_count;
							}
						}
					} while (($show_bidder = $bid_result->FetchNextObject()) && ($total_quantity != 0));
					if ($final_dutch_bid == 0)
						$final_dutch_bid = $this->dutch_bidders[$bid_count-1]["bid"];
					if (($bid_result->RecordCount() > $bid_count) && ($this->winning_dutch_bidder))
					{
						$this->page_id = 10169;
						$this->get_text($db);
						for ($i = $this->winning_dutch_bidder_count ; $i <= $bid_count ; $i++)
						{
							$current_bidder = $this->get_user_data($db,$this->dutch_bidders[$i]["bidder"]);
							switch ($this->configuration_data['email_salutation_type'])
							{
								case 1: //display username
									$message_data["message"] = $current_bidder->USERNAME.",\n\n";
									break;
								case 2: //display firstname
									$message_data["message"] = $current_bidder->FIRSTNAME.",\n\n";
									break;
								case 3: //display firstname and lastname
									$message_data["message"] = $current_bidder->FIRSTNAME." ".$current_bidder->LASTNAME.",\n\n";
									break;
								case 4: //display lastname and firstname
									$message_data["message"] = $current_bidder->LASTNAME." ".$current_bidder->FIRSTNAME.",\n\n";
									break;
								case 5: //display email address
									$message_data["message"] = $current_bidder->EMAIL.",\n\n";
									break;
								default:
									$message_data["message"] = $current_bidder->USERNAME.",\n\n";
									break;
							}
							$message_data["message"] .= urldecode($this->messages[102491])."\n\n";
							$message_data["message"] .= urldecode($this->messages[102489]).$this->dutch_bidders[$i]["quantity"]."\n";
							$message_data["message"] .= urldecode($this->messages[102490]).urldecode($this->auction->PRECURRENCY)." ".$this->print_number($this->dutch_bidders[$i]["bid"])." ".urldecode($this->auction->POSTCURRENCY)."\n\n";
							$message_data["message"] .= urldecode($this->auction->TITLE)."\n";
							$message_data["message"] .= $this->configuration_data['classifieds_url']."?a=2&b=".$this->auction_id."\n\n";
							$message_data["from"] = "From: ".$this->configuration_data['site_email'].$this->separator."Reply-to: ".$this->configuration_data['site_email'].$this->separator;
							$message_data["subject"] = urldecode($this->messages[102488]);
							$additional = "-f".$this->configuration_data['site_email']."\n\r";
							if ($this->configuration_data['email_configuration_type'] == 1)
								$message_data["message"] = str_replace("\n\n","\n",$message_data["message"]);
							if ($this->configuration_data['email_configuration'] == 1)
								mail($current_bidder->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"],$additional);
							elseif ($this->configuration_data['email_configuration'] == 2)
								mail($current_bidder->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"]);
							else
								mail($current_bidder->EMAIL, $message_data["subject"], $message_data["message"]);
						}
					}

					if ($this->winning_dutch_bidder == 1)
					{
						//update auction info
						$this->page_id = 10168;
						$this->get_text($db);

						if (($this->auction->CURRENT_BID < $this->auction->RESERVE_PRICE) && ($bid_amount >= $this->auction->RESERVE_PRICE))
							$set_current_bid = $bid_amount;
						else 
							$set_current_bid = $final_dutch_bid;
						$this->sql_query = "update ".$this->classifieds_table." set
							minimum_bid = ".$final_dutch_bid.",
							current_bid = ".$set_current_bid.",
							price = ".$set_current_bid."
							where id=".$this->auction_id;
						$bid_result = $db->Execute($this->sql_query);
						if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
						if($this->configuration_data['debug_bid'])
						{
							$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "classifieds_table", "update data in auctions table by auction id");
						}
						if (!$bid_result)
						{
							if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
							return false;
						}
						else
						{
							$this->page_id = 10166;
							$this->get_text($db);
							//this dutch bid is a successful one
							switch ($this->configuration_data['email_salutation_type'])
							{
								case 1: //display username
									$message_data["message"] = $this->bidder->USERNAME.",\n\n";
									break;
								case 2: //display firstname
									$message_data["message"] = $this->bidder->FIRSTNAME.",\n\n";
									break;
								case 3: //display firstname and lastname
									$message_data["message"] = $this->bidder->FIRSTNAME." ".$this->bidder->LASTNAME.",\n\n";
									break;
								case 4: //display lastname and firstname
									$message_data["message"] = $this->bidder->LASTNAME." ".$this->bidder->FIRSTNAME.",\n\n";
									break;
								case 5: //display email address
									$message_data["message"] = $this->bidder->EMAIL.",\n\n";
									break;
								default:
									$message_data["message"] = $this->bidder->USERNAME.",\n\n";
									break;
							}
							$message_data["message"] .= urldecode($this->messages[102484])."\n\n";
							$message_data["message"] .= urldecode($this->messages[102455]).$this->dutch_bidder_quantity."\n";
							$message_data["message"] .= urldecode($this->messages[102482]).urldecode($this->auction->PRECURRENCY)." ".$this->print_number($bid_amount)." ".urldecode($this->auction->POSTCURRENCY)."\n\n";
							$message_data["message"] .= urldecode($this->auction->TITLE)."\n";
							$message_data["message"] .= $this->configuration_data['classifieds_url']."?a=2&b=".$this->auction_id."\n\n";
							$message_data["from"] = "From: ".$this->configuration_data['site_email'].$this->separator."Reply-to: ".$this->configuration_data['site_email'].$this->separator;
							$message_data["subject"] = urldecode($this->messages[102485]);
							$additional = "-f".$this->configuration_data['site_email']."\n\r";
							if ($this->configuration_data['email_configuration_type'] == 1){
								$message_data["message"] = str_replace("\n\n","\n",$message_data["message"]);
							}
							if ($this->configuration_data['email_configuration'] == 1){
								mail($this->bidder->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"],$additional);
							}elseif ($this->configuration_data['email_configuration'] == 2){
								mail($this->bidder->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"]);
							}else{
								mail($this->bidder->EMAIL, $message_data["subject"], $message_data["message"]);
							}
							return true;
						}
					}
					else
					{
						$this->page_id = 10166;
						$this->get_text($db);
						//this dutch bid is not a successful one
						switch ($this->configuration_data['email_salutation_type'])
						{
							case 1: //display username
								$message_data["message"] = $this->bidder->USERNAME.",\n\n";
								break;
							case 2: //display firstname
								$message_data["message"] = $this->bidder->FIRSTNAME.",\n\n";
								break;
							case 3: //display firstname and lastname
								$message_data["message"] = $this->bidder->FIRSTNAME." ".$this->bidder->LASTNAME.",\n\n";
								break;
							case 4: //display lastname and firstname
								$message_data["message"] = $this->bidder->LASTNAME." ".$this->bidder->FIRSTNAME.",\n\n";
								break;
							case 5: //display email address
								$message_data["message"] = $this->bidder->EMAIL.",\n\n";
								break;
							default:
								$message_data["message"] = $this->bidder->USERNAME.",\n\n";
								break;
						}
						$message_data["message"] .= urldecode($this->messages[102487])."\n\n";
						$message_data["message"] .= urldecode($this->messages[102482]).urldecode($this->auction->PRECURRENCY)." ".$this->print_number($bid_amount)." ".urldecode($this->auction->POSTCURRENCY)."\n\n";
						$message_data["message"] .= urldecode($this->auction->TITLE)."\n";
						$message_data["message"] .= $this->configuration_data['classifieds_url']."?a=2&b=".$this->auction_id."\n\n";
						$message_data["subject"] = urldecode($this->messages[102486]);
						$message_data["from"] = "From: ".$this->configuration_data['site_email'].$this->separator."Reply-to: ".$this->configuration_data['site_email'].$this->separator;
						$additional = "-f".$this->configuration_data['site_email']."\n\r";
						if ($this->configuration_data['email_configuration_type'] == 1)
							$message_data["message"] = str_replace("\n\n","\n",$message_data["message"]);
						if ($this->configuration_data['email_configuration'] == 1)
							mail($this->bidder->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"],$additional);
						elseif ($this->configuration_data['email_configuration'] == 2)
							mail($this->bidder->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"]);
						else
							mail($this->bidder->EMAIL, $message_data["subject"], $message_data["message"]);
						$this->bid_error = 6;
						if ($this->configuration_data['debug_bid'])
							echo $this->bid_error." - is error<br>\n";
						return false;
					}
				}
				else
				{
					$this->page_id = 10168;
					$this->get_text($db);
					//this bidder is the only bidder at the moment
					switch ($this->configuration_data['email_salutation_type'])
					{
						case 1: //display username
							$message_data["message"] = $this->bidder->USERNAME.",\n\n";
							break;
						case 2: //display firstname
							$message_data["message"] = $this->bidder->FIRSTNAME.",\n\n";
							break;
						case 3: //display firstname and lastname
							$message_data["message"] = $this->bidder->FIRSTNAME." ".$this->bidder->LASTNAME.",\n\n";
							break;
						case 4: //display lastname and firstname
							$message_data["message"] = $this->bidder->LASTNAME." ".$this->bidder->FIRSTNAME.",\n\n";
							break;
						case 5: //display email address
							$message_data["message"] = $this->bidder->EMAIL.",\n\n";
							break;
						default:
							$message_data["message"] = $this->bidder->USERNAME.",\n\n";
							break;
					}
					$message_data["message"] .= urldecode($this->messages[102484])."\n\n";
					$message_data["message"] .= urldecode($this->messages[102455]).$bid_quantity."\n";
					$message_data["message"] .= urldecode($this->messages[102482]).urldecode($this->auction->PRECURRENCY)." ".$this->print_number($bid_amount)." ".urldecode($this->auction->POSTCURRENCY)."\n\n";
					$message_data["message"] .= urldecode($this->auction->TITLE)."\n";
					$message_data["message"] .= $this->configuration_data['classifieds_url']."?a=2&b=".$this->auction_id."\n\n";
					$message_data["from"] = "From: ".$this->configuration_data['site_email'].$this->separator."Reply-to: ".$this->configuration_data['site_email'].$this->separator;
					$message_data["subject"] = urldecode($this->messages[102485]);
					$additional = "-f".$this->configuration_data['site_email']."\n\r";
					if ($this->configuration_data['email_configuration_type'] == 1)
						$message_data["message"] = str_replace("\n\n","\n",$message_data["message"]);
					if ($this->configuration_data['email_configuration'] == 1)
						mail($this->bidder->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"],$additional);
					elseif ($this->configuration_data['email_configuration'] == 2)
						mail($this->bidder->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"]);
					else
						mail($this->bidder->EMAIL, $message_data["subject"], $message_data["message"]);
					return true;
				}
			}
			else
			{
				//regular auction
				//EXTENDED TIME ON BIDS?
				$current_time = $this->shifted_time($db);
				if ($bid_info["buy_now_bid"])
				{
					$this->page_id = 10167;
					$this->get_text($db);
					//this user is closing the auction by choosing the buy now option
					$this->sql_query = "update ".$this->classifieds_table."
						set live = 0,
						current_bid = ".$this->auction->BUY_NOW.",
						price = ".$this->auction->BUY_NOW.",
						minimum_bid = ".$this->auction->BUY_NOW.",
						final_price = ".$this->auction->BUY_NOW.",
						ends = ".$this->shifted_time($db)."
						where id = ".$this->auction_id;
					$update_result = $db->Execute($this->sql_query);
					if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
					if($this->configuration_data['debug_bid'])
					{
						$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "classifieds_table", "update data in auctions table by auction id");
					}
					if (!$update_result)
					{
						$this->error_message = urldecode($this->messages[81]);
						if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
						return false;
					}
					//insert buy now bid
					$this->sql_query = "insert into ".$this->bid_table."
						(auction_id,bidder,bid,time_of_bid,quantity,buy_now_bid)
						values
						(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$this->auction->BUY_NOW."\",".$current_time.", ".$this->auction->QUANTITY.",1)";
					$insert_bid_result = $db->Execute($this->sql_query);
					if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
					if($this->configuration_data['debug_bid'])
					{
						$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update data in bid table by auction and bidder id");
					}
					if (!$insert_bid_result)
					{
						//put things back
						$this->sql_query = "update ".$this->classifieds_table."
							set current_bid = 0,
							set price = 0
							where id = \"".$this->auction_id."\"";
						$set_minimum_result = $db->Execute($this->sql_query);
						if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
						if($this->configuration_data['debug_bid'])
						{
							$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "classifieds_table", "update data in auction table by auction id");
						}
						return false;
					}

					$this->sql_query = "insert into ".$this->auctions_feedbacks_table."
						(rated_user_id,rater_user_id,date,auction_id)
						values
						(".$this->auction->SELLER.",".$this->bidder->ID.",".$this->shifted_time($db).",".$this->auction_id.")";
					$insert_feedback_result = $db->Execute($this->sql_query);
					if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
					if($this->configuration_data['debug_bid'])
					{
						$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "auctions_feedbacks_table", "insert data into feedback table by auction, bidder, and seller id");
					}
					if (!$insert_feedback_result)
					{
						if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}

					$this->sql_query = "insert into ".$this->auctions_feedbacks_table."
						(rated_user_id,rater_user_id,date,auction_id)
						values
						(".$this->bidder->ID.",".$this->auction->SELLER.",".$this->shifted_time($db).",".$this->auction_id.")";
					$insert_feedback_result = $db->Execute($this->sql_query);
					if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
					if($this->configuration_data['debug_bid'])
					{
						$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "auctions_feedbacks_table", "insert data into feedback table by auction, bidder, and seller id");
					}
					if (!$insert_feedback_result)
					{
						if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}
					
					//check for final fees
					//get the final_fee charge
					if ($this->DEBUG_BID)
					{
						echo $auction_price_plan_id." is auction_price_plan_id<bR>\n";
						echo $this->auction->PRICE_PLAN_ID." is PRICE_PLAN_ID<bR>\n";
						echo $this->auction->FINAL_FEE." is FINAL_FEE<BR>\n";
						echo $this->auction->BUY_NOW." is BUY_NOW<br>\n";
					}
					if ($this->auction->PRICE_PLAN_ID)
					{
						$auction_price_plan_id = $this->auction->PRICE_PLAN_ID;
					}
					else
					{
						//get the price plan attached to this seller
						$this->sql_query = "select auction_price_plan_id from ".$this->user_groups_price_plans_table." where
							id = ".$this->auction->SELLER;
						$price_plan_result = $db->Execute($this->sql_query);
						if ($this->DEBUG_BID) echo $this->sql_query."<BR>\n";
						if (!$price_plan_result)
						{
							if ($this->DEBUG_BID) echo $this->sql_query."<BR>\n";
							return false;
						}
						elseif  ($price_plan_result->RecordCount() == 1)
						{
							$show_price_plan = $price_plan_result->FetchNextObject();
							$auction_price_plan_id = $show_price_plan->AUCTION_PRICE_PLAN_ID;
						}
						else
						{
							if ($this->DEBUG_BID) echo $this->sql_query." - returned the wrong count<BR>\n";
							return false;
						}
	
					}
					
					if ($this->auction->FINAL_FEE)
					{
						//check to see if a final fee needs to be charged
						$this->sql_query = "select charge from ".$this->final_fee_table." where".
							"(low<=".$this->auction->BUY_NOW." AND high>=".$this->auction->BUY_NOW.")
							and price_plan_id = ".$auction_price_plan_id." ORDER BY charge DESC limit 1";
						$increment_result = $db->Execute($this->sql_query);
						if ($this->DEBUG_BID) echo $this->sql_query."<BR>\n";
						if (!$increment_result)
						{
							if ($this->DEBUG_BID) echo $this->sql_query."<BR>\n";
							return false;
						}
						elseif  ($increment_result->RecordCount() == 1)
						{
							$show_increment = $increment_result->FetchNextObject();
							$final_fee_percentage = $show_increment->CHARGE;
							
							if ($this->DEBUG_BID)
							{
								echo $final_fee_percentage." is final_fee_percentage<br>\n";
							}
							
							if ($final_fee_percentage > 0)
							{				
								$final_fee_charge = $this->print_number((($final_fee_percentage * $this->auction->BUY_NOW) / 100));
								if ($this->DEBUG_BID) echo $final_fee_charge." is the final_fee_charge<Br>\n";
								
								if ($this->DEBUG_BID)
								{
									echo $final_fee_charge." is final_fee_charge<br>\n";
									echo $this->configuration_data['positive_balances_only']." is POSITIVE_BALANCES_ONLY<br>\n";
									echo $this->configuration_data['use_account_balance']." is USE_ACCOUNT_BALANCE<br>\n";
								}							
								
								if($this->auction->TRANSACTION_TYPE == 7)
								{
									if (($this->configuration_data['positive_balances_only']) && ($this->configuration_data['use_account_balance']))
									{
										//this is a balance transaction
										//remove final fee from balance
										if (($seller->ACCOUNT_BALANCE > 0) && ($seller->ACCOUNT_BALANCE > $final_fee_charge))
										{
											//update user balance with old balance minus final_fee_percentage
											//place transaction in balance transactions table
											//put balance transaction id into final_fee_transaction_number
			
											$new_balance = $seller->ACCOUNT_BALANCE - $final_fee_charge;
											if ($this->DEBUG_BID) echo $seller->ACCOUNT_BALANCE." is the current balance for this seller - ".$this->auction->SELLER."<br>\n";
											$this->sql_query = "update ".$this->userdata_table." set
												account_balance = ".$new_balance."
												where id = ".$this->auction->SELLER;
											if ($this->DEBUG_BID) echo $this->sql_query."<BR>\n";
											$update_balance_results = $db->Execute($this->sql_query);
											if (!$update_balance_results)
											{
												if ($this->DEBUG_BID) echo $this->sql_query."<BR>\n";
												return false;
											}
			
											$this->sql_query = "insert into ".$this->balance_transactions."
												(user_id,auction_id,amount,date,cc_transaction_id,invoice_id,final_fee,approved)
												values
												(".$this->auction->SELLER.",".$this->auction->ID.",".$final_fee_charge.",".$this->shifted_time($db).",999999999,999999999,".$this->bidder->ID.",1)";
											if ($this->DEBUG_BID) echo $this->sql_query."<br>\n";
											$insert_invoice_item_result = $db->Execute($this->sql_query);
											if (!$insert_invoice_item_result)
											{
												if ($this->DEBUG_BID) echo $this->sql_query."<br>\n";
												return false;
											}
											$balance_transaction_id = $db->Insert_ID();
			
											//repitive but making sure the final fee is removed from consideration to be removed from the site balance
											$this->sql_query = "update ".$this->classifieds_table." set
												final_fee_transaction_number = ".$balance_transaction_id."
												where id  = ".$this->auction->ID." and final_fee = 1 and final_fee_transaction_number = 0 and final_price != 0 and seller = ".$this->auction->SELLER."";
											$final_fee_result = $db->Execute($this->sql_query);
											if ($this->DEBUG_BID) echo $this->sql_query."<BR>\n";
											if (!$final_fee_result)
											{
												if ($this->DEBUG_BID) echo $this->sql_query."<BR>\n";
												$this->site_error($db->ErrorMsg());
												return false;
											}
			
										}
										else
										{
											//there is not enough account balance to cover the final fee
											//do not reset the final fee transaction number
											if ($this->DEBUG_BID) echo "not enough in account balance to cover final fee - ".$this->auction->ID." - ".$this->auction->SELLER."<BR>\n";
			
										}
									}
									elseif ((!$this->configuration_data['positive_balances_only']) && ($this->configuration_data['use_account_balance']))
									{
										//this is an invoice transaction
										//place transaction in balance transactions table
										//put balance transaction id into final_fee_transaction_number
										$this->sql_query = "insert into ".$this->balance_transactions."
											(user_id,auction_id,amount,date,final_fee)
											values
											(".$this->auction->SELLER.",".$this->auction->ID.",".$final_fee_charge.",".$this->shifted_time($db).",".$this->bidder->ID.")";
										if ($this->DEBUG_BID) echo $this->sql_query."<br>\n";
										$insert_invoice_item_result = $db->Execute($this->sql_query);
										if (!$insert_invoice_item_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<br>\n";
											return false;
										}
										$balance_transaction_id = $db->Insert_ID();
			
										//repitive but making sure the final fee is removed from consideration to be moved to an invoice
										$this->sql_query = "update ".$this->classifieds_table." set
											final_fee_transaction_number = ".$balance_transaction_id."
											where id  = ".$this->auction->ID." and final_fee = 1 and final_fee_transaction_number = 0 and final_price != 0 and seller = ".$this->auction->SELLER."";
										$final_fee_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<BR>\n";
										if (!$final_fee_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<BR>\n";
											$this->site_error($db->ErrorMsg());
											return false;
										}
									}
								}
							}
						}
					}

					//send emails to seller and buy now bidder
				    	//to buy now bidder
					switch ($this->configuration_data['email_salutation_type'])
					{
						case 1: //display username
							$message_data["message"] = $this->bidder->USERNAME.",\n\n";
							break;
						case 2: //display firstname
							$message_data["message"] = $this->bidder->FIRSTNAME.",\n\n";
							break;
						case 3: //display firstname and lastname
							$message_data["message"] = $this->bidder->FIRSTNAME." ".$this->bidder->LASTNAME.",\n\n";
							break;
						case 4: //display lastname and firstname
							$message_data["message"] = $this->bidder->LASTNAME." ".$this->bidder->FIRSTNAME.",\n\n";
							break;
						case 5: //display email address
							$message_data["message"] = $this->bidder->EMAIL.",\n\n";
							break;
						default:
							$message_data["message"] = $this->bidder->USERNAME.",\n\n";
							break;
					}
					$message_data["message"] .= urldecode($this->messages[102493])."\n\n";
					$message_data["message"] .= urldecode($this->messages[102494]).urldecode($this->auction->PRECURRENCY)." ".$this->print_number($this->auction->BUY_NOW)." ".urldecode($this->auction->POSTCURRENCY)."\n\n";
					$message_data["message"] .= $seller->FIRSTNAME." ".$seller->LASTNAME."\n".$seller->EMAIL."\n\n";
					$message_data["message"] .= urldecode($this->auction->TITLE)."\n";
					$message_data["message"] .= $this->configuration_data['classifieds_url']."?a=2&b=".$this->auction_id."\n\n";
					$message_data["from"] = "From: ".$this->configuration_data['site_email'].$this->separator."Reply-to: ".$this->configuration_data['site_email'].$this->separator;
					$message_data["subject"] = urldecode($this->messages[102492]);
					
					if ($this->DEBUG_BID)
					{
						echo $message_data["message"]." is the message sent to the high bidder<bR>\n";	
					}
					
					$additional = "-f".$this->configuration_data['site_email']."\n\r";
					if ($this->configuration_data['email_configuration_type'] == 1)
						$message_data["message"] = str_replace("\n\n","\n",$message_data["message"]);
					if ($this->configuration_data['email_configuration'] == 1)
						mail($this->bidder->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"],$additional);
					elseif ($this->configuration_data['email_configuration'] == 2)
						mail($this->bidder->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"]);
					else
						mail($this->bidder->EMAIL, $message_data["subject"], $message_data["message"]);

					//to seller
					switch ($this->configuration_data['email_salutation_type'])
					{
						case 1: //display username
							$message_data["message"] = $seller->USERNAME.",\n\n";
							break;
						case 2: //display firstname
							$message_data["message"] = $seller->FIRSTNAME.",\n\n";
							break;
						case 3: //display firstname and lastname
							$message_data["message"] = $seller->FIRSTNAME." ".$seller->LASTNAME.",\n\n";
							break;
						case 4: //display lastname and firstname
							$message_data["message"] = $seller->LASTNAME." ".$seller->FIRSTNAME.",\n\n";
							break;
						case 5: //display email address
							$message_data["message"] = $seller->EMAIL.",\n\n";
							break;
						default:
							$message_data["message"] = $seller->USERNAME.",\n\n";
							break;
					}
					$message_data["message"] .= urldecode($this->messages[102496])."\n\n";
					$message_data["message"] .= urldecode($this->messages[102494])." ".urldecode($this->auction->PRECURRENCY)." ".$this->print_number($this->auction->BUY_NOW)." ".urldecode($this->auction->POSTCURRENCY)."\n\n";
					$message_data["message"] .= $this->bidder->FIRSTNAME." ".$this->bidder->LASTNAME."\n".$this->bidder->EMAIL."\n\n";
					$message_data["message"] .= urldecode($this->auction->TITLE)."\n";
					$message_data["message"] .= $this->configuration_data['classifieds_url']."?a=2&b=".$this->auction_id."\n\n";
					$message_data["from"] = "From: ".$this->configuration_data['site_email'].$this->separator."Reply-to: ".$this->configuration_data['site_email'].$this->separator;
					$message_data["subject"] = urldecode($this->messages[102495]);
					
					if ($this->DEBUG_BID)
					{
						echo $message_data["message"]." is the message sent to the seller<bR>\n";	
					}					
					
					$additional = "-f".$this->configuration_data['site_email']."\n\r";
					if ($this->configuration_data['email_configuration_type'] == 1)
					{
						$message_data["message"] = str_replace("\n\n","\n",$message_data["message"]);
					}
					if ($this->configuration_data['email_configuration'] == 1)
					{
						mail($seller->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"],$additional);
					}
					elseif ($this->configuration_data['email_configuration'] == 2)
					{
						mail($seller->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"]);
					}
					else
					{
						mail($seller->EMAIL, $message_data["subject"], $message_data["message"]);
					}

					$this->update_category_count($db,$this->auction->CATEGORY);
					$this->bid_success = 4;
					return true;
				}

				//get minimum bid
				$minimum_bid = $this->get_minimum_bid($db);
				if (!$minimum_bid)
				{
					return false;
				}
				if ($bid_amount >= $minimum_bid)
				{
					//check to make sure the current bidder is not winning already
					$auction_ends = date($this->configuration_data['entry_date_configuration'],$this->auction->ENDS);

					$this->sql_query = "select * from ".$this->bid_table." where auction_id=".$this->auction_id." order by bid desc,time_of_bid asc limit 1";
					$high_bid_result = $db->Execute($this->sql_query);
					if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
					if($this->configuration_data['debug_bid'])
					{
						$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "get data from bid table by auction id");
					}
					if (!$high_bid_result)
					{
						if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
						return false;
					}
					elseif ($high_bid_result->RecordCount() == 1)
					{
						$show_high_bidder = $high_bid_result->FetchNextObject();
						if ($show_high_bidder->BIDDER == $this->bidder->ID)
						{
							//this bidder is the current high bidder
							if (!defined('ALLOW_BIDDING_AGAINST_SELF'))
								$allow_bidding_against_self = 0;
							else 
								$allow_bidding_against_self = ALLOW_BIDDING_AGAINST_SELF;
							if ($allow_bidding_against_self)
							{
								//this will allow the client to bid against their own bid
							}
							else 
							{
								$this->bid_error = 1;
								if ($this->DEBUG_BID)
									echo $this->bid_error." 1 - is error<br>\n";
								return false;
							}
						}
					}

					//Sunit
					if($this->configuration_data['auction_extension_check'] > 0)
					{
						//Fetch the time of the current bid
						//Check current_bid time is greater than auction_ends - auction_extension_check and less than
						// auction_ends
						//if true, add auction_extension to auction_ends and update the data
						//else do nothing
						if(($current_time >($this->auction->ENDS-$this->configuration_data['auction_extension_check']*60))
							&& ($current_time < $this->auction->ENDS))
						{
							$this->auction->ENDS = $this->auction->ENDS+$this->configuration_data['auction_extension']*60;
							//echo $this->auction->ENDS." is the new end time <bR>";
							$this->sql_query = "update ".$this->classifieds_table."
								set ends = \"".$this->auction->ENDS."\"
								where id = \"".$this->auction_id."\"";
							$update_result = $db->Execute($this->sql_query);
							if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
							if($this->configuration_data['debug_bid'])
							{
								$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "classifieds_table", "update ends attribute in auction table by auction id");
							}
							else 
							{
								if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
							}
						}
						else
						{
							//echo "Time is not in auction extension check range<bR>";
						}
					}
					//Sunit

					if ($this->auction->CURRENT_BID == 0)
					{
						//got here because there are no bids yet on this item
						//set current bid as the minimum bid in auction table
						//insert into bids auction_id,bidder,bid = minimum bid of auction, null, quantity

						if (($this->auction->RESERVE_PRICE > 0) && ($bid_amount >= $this->auction->RESERVE_PRICE))
						{
							//get the minimum bid
							//get increment first
							$increment = $this->get_increment($db,$this->auction->RESERVE_PRICE);

							$this->sql_query = "update ".$this->classifieds_table." set 
								current_bid = \"".$this->auction->RESERVE_PRICE."\",
								price = \"".$this->auction->RESERVE_PRICE."\",
								minimum_bid = ".($this->auction->RESERVE_PRICE+ $increment)."
								where id = \"".$this->auction_id."\"";
							$set_minimum_result = $db->Execute($this->sql_query);
							if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
							if($this->configuration_data['debug_bid'])
							{
								$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update bids in auctions table by auction id");
							}
							if (!$set_minimum_result)
							{
								if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
								return false;
							}
							else
							{
								$this->sql_query = "insert into ".$this->bid_table."
									(auction_id,bidder,bid,time_of_bid,quantity)
									values
									(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$this->auction->RESERVE_PRICE."\",".$current_time.", ".$quantity.")";
								$insert_bid_result = $db->Execute($this->sql_query);
								if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
								if($this->configuration_data['debug_bid'])
								{
									$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update bids in auctions table by auction id");
								}
								if (!$insert_bid_result)
								{
									//put things back
									$this->sql_query = "update ".$this->classifieds_table." set 
										current_bid = 0,
										price = 0
										where id = \"".$this->auction_id."\"";
									$set_minimum_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update bids in auctions table by auction id");
									}
									return false;
								}
								else
								{
									$a_bid = $this->auction->RESERVE_PRICE;
									if ($bid_amount > $this->auction->RESERVE_PRICE)
									{
										//insert bid into the autobid table because it was greater then minimum_bid
										//minimum_bid is the current minimum because there were no previous bids
										$this->sql_query = "insert into ".$this->autobid_table."
										(auction_id,bidder,maxbid,time_of_bid,quantity)
										values
										(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$bid_amount."\",".$current_time.", ".$quantity.")";
										$insert_autobid_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into auctions table");
										}
										if (!$insert_autobid_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}
									}
									$this->send_current_high_bidder_email($db,$this->bidder->ID);
									$this->bid_success = 1;
									return true;
								}
							}
						}
						else
						{
							//set current bid to starting bid
							//set minimum bid to starting bid plus increment
							//get the minimum bid
							//get increment first
							$increment = $this->get_increment($db,$this->auction->STARTING_BID);

							$this->sql_query = "update ".$this->classifieds_table." set 
								current_bid = \"".$this->auction->STARTING_BID."\",
								price = \"".$this->auction->STARTING_BID."\",
								minimum_bid = ".($this->auction->STARTING_BID + $increment)."
								where id = \"".$this->auction_id."\"";
							$set_minimum_result = $db->Execute($this->sql_query);
							if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
							if($this->configuration_data['debug_bid'])
							{
								$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update current and min bid in auctions table by auction id");
							}
							if (!$set_minimum_result)
							{
								if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
								return false;
							}
							else
							{
								$this->sql_query = "insert into ".$this->bid_table."
									(auction_id,bidder,bid,time_of_bid,quantity)
									values
									(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$this->auction->STARTING_BID."\",".$current_time.", ".$quantity.")";
								$insert_bid_result = $db->Execute($this->sql_query);
								if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
								if($this->configuration_data['debug_bid'])
								{
									$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
								}
								if (!$insert_bid_result)
								{
									//put things back
									$this->sql_query = "update ".$this->classifieds_table." set 
										current_bid = 0,
										price = 0 
										where id = \"".$this->auction_id."\"";
									$set_minimum_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update current in auctions table by auction id");
									}
									return false;
								}
								else
								{
									$a_bid = $minimum_bid;
									if ($bid_amount > $minimum_bid)
									{
										//insert bid into the autobid table because it was greater then minimum_bid
										//minimum_bid is the current minimum because there were no previous bids
										$this->sql_query = "insert into ".$this->autobid_table."
										(auction_id,bidder,maxbid,time_of_bid,quantity)
										values
										(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$bid_amount."\",".$current_time.", ".$quantity.")";
										$insert_autobid_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update data into auctions table");
										}
										if (!$insert_autobid_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}
									}
									$this->send_current_high_bidder_email($db,$this->bidder->ID);
									$this->bid_success = 1;
									return true;
								}
							}
						}
		  			} // end of if ($this->auction->CURRENT_BID == 0)
		  			else
		  			{
		  				//got here because this auction already has a bid on it
		  				//this bid is above the minimum bid so at least some bid activity will take place.
						//check to see if there is a current autobid

						$this->sql_query = "select * from ".$this->autobid_table." where auction_id = \"".$this->auction_id."\"";
						$get_autobid_result = $db->Execute($this->sql_query);
						if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
						if($this->configuration_data['debug_bid'])
						{
							$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "get data from autobid table by auction id");
						}
				  		if (!$get_autobid_result)
						{
							if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
							return false;
						}
						elseif ($get_autobid_result->RecordCount() == 0)
						{
							//there is no proxy bid for this auction
							//this bid is above the minimum
							//this bid is the highest bid so far

							//check to see if reserve is above current minimum bid
							//this bid is above the reserve and a reserve exists but the current minimum bid was below the
							//reserve price.
							//the reserve price becomes the new current bid
							//the new minimum bid for this auction is the reserve plus the increment
							if (($bid_amount >= $this->auction->RESERVE_PRICE) && ($this->auction->RESERVE_PRICE !=0 )
								&& ($minimum_bid <= $this->auction->RESERVE_PRICE))
							{
								$increment = $this->get_increment($db,$this->auction->RESERVE_PRICE);
								$this->sql_query = "update ".$this->classifieds_table." set
									current_bid = \"".$this->auction->RESERVE_PRICE."\",
									price = \"".$this->auction->RESERVE_PRICE."\",
									minimum_bid = ".($this->auction->RESERVE_PRICE+ $increment)."
									where id = \"".$this->auction_id."\"";
								$set_minimum_result = $db->Execute($this->sql_query);
								if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
								if($this->configuration_data['debug_bid'])
								{
									$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update data in auctions table by auction id");
								}
								if (!$set_minimum_result)
								{
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									return false;
								}
								else
								{
									$this->sql_query = "insert into ".$this->bid_table."
										(auction_id,bidder,bid,time_of_bid,quantity)
										values
										(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$this->auction->RESERVE_PRICE."\",".$current_time.", ".$quantity.")";
									$insert_bid_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
									}
									if (!$insert_bid_result)
									{
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										//put things back
										$this->sql_query = "update ".$this->classifieds_table." set 
											current_bid = ".$this->auction->CURRENT_BID.",
											price = ".$this->auction->CURRENT_BID.",
											minimum_bid = ".$this->auction->MINIMUM_BID.",
											where id = \"".$this->auction_id."\"";
										$set_minimum_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update data in auctions table by auction id");
										}
										return false;
									}
									else
									{
										if ($bid_amount > $this->auction->RESERVE_PRICE)
										{
											//insert bid into the autobid table because it was greater then minimum_bid
											//minimum_bid is the current minimum because there were no previous bids
											$this->sql_query = "insert into ".$this->autobid_table."
											(auction_id,bidder,maxbid,time_of_bid,quantity)
											values
											(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$bid_amount."\",".$current_time.", ".$quantity.")";
											$insert_autobid_result = $db->Execute($this->sql_query);
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											if($this->configuration_data['debug_bid'])
											{
												$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into autobid table");
											}
											if (!$insert_autobid_result)
											{
												if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
												return false;
											}
										}
										$this->send_current_high_bidder_email($db,$this->bidder->ID);
										// MLC 20071104 don't send outbid to
										// bidder if same person
										if ( $this->bidder->ID != $show_high_bidder->BIDDER )
										{
											$this->send_outbid_email($db,$show_high_bidder->BIDDER);
										}
										$this->bid_success = 1;
										return true;
									}
								}
							}
							else
							{
								//the reserve has already been met or is 0
								//there is no bid in the autobid table to test for a higher price
								//the current bid becomes the minimum bid
								//the minimum bid becomes the current minimum bid plus increment
								$increment = $this->get_increment($db,$minimum_bid);
								$this->sql_query = "update ".$this->classifieds_table." set
									current_bid = \"".$minimum_bid."\",
									price = \"".$minimum_bid."\",
									minimum_bid = \"".($minimum_bid + $increment)."\"
									where id = \"".$this->auction_id."\"";
								$update_current_bid_result = $db->Execute($this->sql_query);
								if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
								if($this->configuration_data['debug_bid'])
								{
									$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update data in auctions table by auction id");
								}
								if (!$update_current_bid_result)
								{
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									return false;
								}
								else
								{
									$this->sql_query = "insert into ".$this->bid_table."
										(auction_id,bidder,bid,time_of_bid,quantity)
										values
										(\"".$this->auction_id."\",\"". $this->bidder->ID."\", \"".$minimum_bid."\",".$current_time.", \"".$quantity_bid."\")";
									$insert_bid_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
									}
									if (!$insert_bid_result)
									{
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										return false;
									}
									else
									{
										if ($bid_amount > $minimum_bid)
										{
											//got here because no bid in the autobid and bid is large enough to insert into the autobid table
											$this->sql_query = "insert into ".$this->autobid_table."
												(auction_id,bidder,maxbid,time_of_bid,quantity)
												values
												(\"".$this->auction_id."\",\"". $this->bidder->ID."\", \"".$bid_amount."\",".$current_time.", \"".$quantity_bid."\")";
											$insert_autobid_result = $db->Execute($this->sql_query);
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											if($this->configuration_data['debug_bid'])
											{
												$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
											}
											if (!$insert_autobid_result)
											{
												if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
												return false;
											}
										}
										$this->bid_success = 1;
										$this->send_current_high_bidder_email($db,$this->bidder->ID);
										// MLC 20071104 don't send outbid to
										// bidder if same person
										if ( $this->bidder->ID != $show_high_bidder->BIDDER )
										{
											$this->send_outbid_email($db,$show_high_bidder->BIDDER);
										}
										return true;
									}
								}
							}
						}
	  					else
	  					{
							//there is proxy bid for this auction
							//there already is a price in the autobid table higher than the current price
							//pull the price from the autobid table and test it against this bid
//
							$show_autobid = $get_autobid_result->FetchNextObject();

//
							//get increment for the maxbid range
							$increment = $this->get_increment($db,$show_autobid->MAXBID);
	          						$maxbid_increment = $show_autobid->MAXBID + $increment;
							if ($bid_amount > $show_autobid->MAXBID)
							{
								//current bid is greater than maxbid of other user
								//we have a new high bid
								if (($show_autobid->MAXBID >= $this->auction->RESERVE_PRICE) ||
									($this->auction->RESERVE_PRICE == 0) ||
									($bid_amount < $this->auction->RESERVE_PRICE) )
								{
									//Max bid set is equal to the reserve price or bid amount is less than reserve price
									//current bid is less than the reserve but greater than proxy bid
									if ($bid_amount > $maxbid_increment)
									{
										$increment = $this->get_increment($db,$maxbid_increment);
										//Maxbid_increment becomes the new current bid for this bidder
										//enter maxbid_increment into bid table as the current bid of the current bidder
										//update autobid enter bid as the maxbid for this table
										//update auctions table set maxbid_increment as the current bid
										$this->sql_query = "update ".$this->classifieds_table." set
											current_bid = \"".$maxbid_increment."\",
											price = \"".$maxbid_increment."\",
											minimum_bid = ".($increment + $maxbid_increment)."
											where id = \"".$this->auction_id."\"";
										$update_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update data in auctions table by auction id");
										}
										if (!$update_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}

										$this->sql_query = "insert into ".$this->bid_table."
											(auction_id,bidder,bid,time_of_bid,quantity)
											values
											(\"".$this->auction_id."\", \"".$show_autobid->BIDDER."\", \"".$show_autobid->MAXBID."\",".$current_time.", \"".$show_autobid->QUANTITY."\")";
										$insert_bid_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
										}
										if (!$insert_bid_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}

										$this->sql_query = "insert into ".$this->bid_table."
											(auction_id,bidder,bid,time_of_bid,quantity)
											values
											(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$maxbid_increment."\", ".$current_time.", \"".$quantity."\")";
										$insert_bid_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
										}
										if (!$insert_bid_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}

										$this->sql_query = "update ".$this->autobid_table."  set
											maxbid = \"".$bid_amount."\",
											bidder = \"".$this->bidder->ID."\"
											where auction_id = \"".$this->auction_id."\"";
										$update_autobid_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update max bid and bidder in auctions table by auction id");
										}
										if (!$update_autobid_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}

										$a_bid = $maxbid_increment;
										//bidder is current high bidder
										$this->bid_success = 1;
										$this->send_current_high_bidder_email($db,$this->bidder->ID);
										// MLC 20071104 don't send outbid to
										// bidder if same person
										if ( $this->bidder->ID != $show_high_bidder->BIDDER )
										{
											$this->send_outbid_email($db,$show_high_bidder->BIDDER);
										}
										return true;
									} //end of if ($bid > $maxbid_increment)
				  					elseif ($bid_amount == $maxbid_increment)
									{
										//maxbid_increment becomes the new current bid
										//enter maxbid_increment into the bid table as the current bid for this bidder
										//update auctions table set maxbid_increment as the current bid
										//delete this auction from the autobid table

										$increment = $this->get_increment($db,$maxbid_increment);
										$this->sql_query = "update ".$this->classifieds_table." set
											current_bid = \"".$maxbid_increment."\",
											price = \"".$maxbid_increment."\",
											minimum_bid = ".($increment + $maxbid_increment)."
											where id = \"".$this->auction_id."\"";
										$update_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update current and min bid in auctions table by auction id");
										}
										if (!$update_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}

										$this->sql_query = "insert into ".$this->bid_table."
											(auction_id,bidder,bid,time_of_bid,quantity)
											values
											(\"".$this->auction_id."\", \"".$show_autobid->BIDDER."\", \"".$show_autobid->MAXBID."\",".$current_time.", \"".$show_autobid->QUANTITY."\")";
										$insert_bid_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
										}
										if (!$insert_bid_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}

										$this->sql_query = "insert into ".$this->bid_table."
											(auction_id,bidder,bid,time_of_bid,quantity)
											values
											(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$maxbid_increment."\", ".$current_time.", \"".$quantity."\")";
										$insert_bid_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
										}
										if (!$insert_bid_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}

										$this->sql_query = "delete from ".$this->autobid_table."  where auction_id = \"".$this->auction_id."\"";
										$delete_autobid_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "delete data in bid table by auction id");
										}
										if (!$delete_autobid_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}
										$this->bid_success = 1; //bid made but not high bid
										$this->send_current_high_bidder_email($db,$this->bidder->ID);
										// MLC 20071104 don't send outbid to
										// bidder if same person
										if ( $this->bidder->ID != $show_high_bidder->BIDDER )
										{
											$this->send_outbid_email($db,$show_high_bidder->BIDDER);
										}
										return true;
									} //end of elseif ($bid == $maxbid_increment)
				  					else
									{
										//bid is greater than the current maxbid in the autobid table
										//but not bigger than the incremented autobid value
										//enter bid into the bid table as the bid for this bidder
										//update auctions table current bid equals bid
										//delete this auction from the autobid table

										$increment = $this->get_increment($db,$maxbid_increment);
										$this->sql_query = "update ".$this->classifieds_table." set
											current_bid = \"".$bid_amount."\",
											price = \"".$bid_amount."\",
											minimum_bid = ".($bid_amount + $increment)."
											where id = \"".$this->auction_id."\"";
										$update_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update current and min bid in auctions table by auction id");
										}
										if (!$update_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}

										$this->sql_query = "insert into ".$this->bid_table."
											(auction_id,bidder,bid,time_of_bid,quantity)
											values
											(\"".$this->auction_id."\", \"".$show_autobid->BIDDER."\", \"".$show_autobid->MAXBID."\",".$current_time.", \"".$show_autobid->QUANTITY."\")";
										$insert_bid_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
										}
										if (!$insert_bid_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}

										$this->sql_query = "insert into ".$this->bid_table."
											(auction_id,bidder,bid,time_of_bid,quantity)
										values
										(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$bid_amount."\", ".$current_time.", \"".$quantity."\")";
										$insert_bid_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
										}
										if (!$insert_bid_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}

										$this->sql_query = "delete from ".$this->autobid_table."  where auction_id = \"".$this->auction_id."\"";
										$delete_autobid_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "delete data in autobid table by auction id");
										}
										if (!$delete_autobid_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}

										$this->send_current_high_bidder_email($db,$this->bidder->ID);
										// MLC 20071104 don't send outbid to
										// bidder if same person
										if ( $this->bidder->ID != $show_high_bidder->BIDDER )
										{
											$this->send_outbid_email($db,$show_high_bidder->BIDDER);
										}
										$this->bid_success = 1;
										return true;
									}// end of else
								}
								else
								{
									//bid amount is equal to or greater than reserve price
									//got here because the max proxy bid was less than reserve
									//and there is a reserve price
									//and the current bid is greater than the reserve price

									//set the new minimum bid = reserve price
									//set proxy bid amount if necessary
									//remove old proxy bid
									$increment = $this->get_increment($db,$this->auction->RESERVE_PRICE);
									$this->sql_query = "update ".$this->classifieds_table." set 
										current_bid = \"".$this->auction->RESERVE_PRICE."\",
										price = \"".$this->auction->RESERVE_PRICE."\",
										minimum_bid = ".($this->auction->RESERVE_PRICE+ $increment)."
										where id = \"".$this->auction_id."\"";
									$set_minimum_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "classifieds_table", "update current bid in auctions table by auction id");
									}
									if (!$set_minimum_result)
									{
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										return false;
									}
									else
									{
										$this->sql_query = "insert into ".$this->bid_table."
											(auction_id,bidder,bid,time_of_bid,quantity)
											values
											(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$this->auction->RESERVE_PRICE."\",".$current_time.", ".$quantity.")";
										$insert_bid_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into auctions table");
										}
										if (!$insert_bid_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											//put things back
											$this->sql_query = "update ".$this->classifieds_table." set 
												current_bid = 0,
												price = 0 
												where id = \"".$this->auction_id."\"";
											$set_minimum_result = $db->Execute($this->sql_query);
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											if($this->configuration_data['debug_bid'])
											{
												$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update current bid in auctions table by auction id");
											}
											return false;
										}
										else
										{
											if ($bid_amount > $this->auction->RESERVE_PRICE)
											{
													//insert bid into the autobid table because it was greater then minimum_bid
													//minimum_bid is the current minimum because there were no previous bids
													$this->sql_query = "insert into ".$this->autobid_table."
													(auction_id,bidder,maxbid,time_of_bid,quantity)
													values
													(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$bid_amount."\",".$current_time.", ".$quantity.")";
													$insert_autobid_result = $db->Execute($this->sql_query);
													if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
													if($this->configuration_data['debug_bid'])
													{
														$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into auctions table");
													}
													if (!$insert_autobid_result)
													{
														if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
														return false;
													}
												}
												$this->send_current_high_bidder_email($db,$this->bidder->ID);
												// MLC 20071104 don't send outbid to
												// bidder if same person
												if ( $this->bidder->ID != $show_high_bidder->BIDDER )
												{
													$this->send_outbid_email($db,$show_high_bidder->BIDDER);
												}
												$this->bid_success = 1;

										}
										$this->sql_query = "delete from ".$this->autobid_table."  where auction_id = \"".$this->auction_id."\"";
										$delete_autobid_result = $db->Execute($this->sql_query);
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										if($this->configuration_data['debug_bid'])
										{
											$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "delete data in autobid table by auction id");
										}
										if (!$delete_autobid_result)
										{
											if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
											return false;
										}
										return true;
									}
								}// bid amount is equal to or greater than reserve price
		  					} //end of if ($bid_amount > $show_autobid->MAXBID)
		  					elseif ($bid_amount == $show_autobid->MAXBID)
		  					{
								//Bid amount is equal to the max bid by another user
								//the old bid stands as the new current bid
								//show_autobid[maxbid] is entered into the bid table as a bid for the old bidder
								//remove this auction from the autobid table

								$increment = $this->get_increment($db,$bid_amount);
								$this->sql_query = "update ".$this->classifieds_table." set
									current_bid = \"".$bid_amount."\",
									price = \"".$bid_amount."\",
									minimum_bid = ".($increment + $bid_amount)."
									where id = \"".$this->auction_id."\"";
								$update_result = $db->Execute($this->sql_query);
								if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
								if($this->configuration_data['debug_bid'])
								{
									$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update current bid in auctions table by auction id");
								}
								if (!$update_result)
								{
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									return false;
								}

								$this->sql_query = "insert into ".$this->bid_table."
									(auction_id,bidder,bid,time_of_bid,quantity)
									values
									(\"".$this->auction_id."\", \"".$show_autobid->BIDDER."\", \"".$show_autobid->MAXBID."\",".$current_time.", \"".$show_autobid->QUANTITY."\")";
								$insert_bid_result = $db->Execute($this->sql_query);
								if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
								if($this->configuration_data['debug_bid'])
								{
									$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into auctions table");
								}
								if (!$insert_bid_result)
								{
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									return false;
								}

								$this->sql_query = "insert into ".$this->bid_table."
									(auction_id,bidder,bid,time_of_bid,quantity)
									values
									(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$bid_amount."\", ".($current_time+1).", \"".$quantity."\")";
								$insert_bid_result = $db->Execute($this->sql_query);
								if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
								if($this->configuration_data['debug_bid'])
								{
									$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into auctions table");
								}
								if (!$insert_bid_result)
								{
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									return false;
								}

								$this->sql_query = "delete from ".$this->autobid_table."  where auction_id = \"".$this->auction_id."\"";
								$delete_autobid_result = $db->Execute($this->sql_query);
								if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
								if($this->configuration_data['debug_bid'])
								{
									$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "delete data in autobid table by auction id");
								}
								if (!$delete_autobid_result)
								{
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									return false;
								}

								$this->bid_success = 3;
								$this->send_outbid_email($db,$this->bidder->ID);
								return true;

		  					} //end of elseif ($bid_amount == $show_autobid->MAXBID)
		  					else
		  					{
		  						//Bid is less than the max bid
								$increment = $this->get_increment($db,$bid_amount);
								$incremented_bid = $increment + $bid_amount;
								if ($show_autobid->MAXBID > $incremented_bid)
								{
									//show_autobid["maxbid"] remains the same in the autobid table
									//bid is entered into the bid table as a bid for the current bidder
									//incremented bid becomes the new bid for the autobid bidder in the bid table at the same time
									//incremented_bid becomes the current bid in the auction table

									$this->sql_query = "insert into ".$this->bid_table."
										(auction_id,bidder,bid,time_of_bid,quantity)
										values
										(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$bid_amount."\", ".$current_time.", \"".$quantity."\")";
									$insert_bid_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
									}
									if (!$insert_bid_result)
									{
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										return false;
									}

									$this->sql_query = "insert into ".$this->bid_table."
										(auction_id,bidder,bid,time_of_bid,quantity)
										values
										(\"".$this->auction_id."\", \"".$show_autobid->BIDDER."\", \"".$incremented_bid."\", ".$current_time.", \"".$quantity."\")";
									$insert_bid_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
									}
									if (!$insert_bid_result)
									{
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										return false;
									}

									$increment = $this->get_increment($db,$incremented_bid);
									$this->sql_query = "update ".$this->classifieds_table." set
										current_bid = \"".$incremented_bid."\",
										price = \"".$incremented_bid."\",
										minimum_bid = ".($increment + $incremented_bid)."
										where id = \"".$this->auction_id."\"";
									$update_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update current and min bid in auctions table by auction id");
									}
									if (!$update_result)
									{
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										return false;
									}

									$this->bid_success = 3;
									$this->send_outbid_email($db,$this->bidder->ID);
									return true;
			  				} //if ($show_autobid->MAXBID > $incremented_bid)
				  				elseif ($show_autobid->MAXBID == $incremented_bid)
				  				{
									// the autobid and the incremented bid are equal
									//the old show_autobid[maxbid] is the new current bid in the auction table
									//show_autobid[maxbid] is entered into bid table as the current bid for the show_autobid[bidder]
									//this auction is removed from the autobid table
									//current bid is entered into the bid table for the current bidder first the old bid is entered

									$this->sql_query = "insert into ".$this->bid_table."
										(auction_id,bidder,bid,time_of_bid,quantity)
										values
										(\"".$this->auction_id."\", \"".$show_autobid->BIDDER."\", \"".$incremented_bid."\", ".$current_time.", \"".$quantity."\")";
									$insert_bid_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
									}
									if (!$insert_bid_result)
									{
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										return false;
									}

									$this->sql_query = "insert into ".$this->bid_table."
										(auction_id,bidder,bid,time_of_bid,quantity)
										values
										(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$bid_amount."\", ".$current_time.", \"".$quantity."\")";
									$insert_bid_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
									}
									if (!$insert_bid_result)
									{
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										return false;
									}

									$increment = $this->get_increment($db,$incremented_bid);
									$this->sql_query = "update ".$this->classifieds_table." set
										current_bid = \"".$incremented_bid."\",
										price = \"".$incremented_bid."\",
										minimum_bid = ".($incremented_bid + $increment)."
										where id = \"".$this->auction_id."\"";
									$update_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update current and min bid in auctions table by auction id");
									}
									if (!$update_result)
									{
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										return false;
									}

									$this->sql_query = "delete from ".$this->autobid_table."  where auction_id = \"".$this->auction_id."\"";
									$delete_autobid_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "delete data in autobid table by auction id");
									}
									if (!$delete_autobid_result)
									{
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										return false;
									}

									$this->bid_success = 3;
									$this->send_outbid_email($db,$this->bidder->ID);
									return true;

				  				} //end of elseif ($show_autobid->MAXBID == $incremented_bid)
								else
								{
									//show_autobid[maxbid] is greater than bid but not bigger than incremented bid
									//show_autobid[maxbid] becomes the current bid in the auction table
									//bid is entered into the bid table as a bid for the current bidder
									//show_autobid[maxbid] is entered into the bid table as a bid for the autobid bidder
									//this auction is removed from the autobid table

									$this->sql_query = "insert into ".$this->bid_table."
										(auction_id,bidder,bid,time_of_bid,quantity)
										values
										(\"".$this->auction_id."\", \"".$this->bidder->ID."\", \"".$bid_amount."\", ".$current_time.", \"".$quantity."\")";
									$insert_bid_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
									}
									if (!$insert_bid_result)
									{
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										return false;
									}

									$this->sql_query = "insert into ".$this->bid_table."
										(auction_id,bidder,bid,time_of_bid,quantity)
										values
										(\"".$this->auction_id."\", \"".$show_autobid->BIDDER."\", \"".$show_autobid->MAXBID."\", ".$current_time.", \"".$quantity."\")";
									$insert_bid_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "insert data into bid table");
									}
									if (!$insert_bid_result)
									{
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										return false;
									}
									
									$increment = $this->get_increment($db,$show_autobid->MAXBID);
									$this->sql_query = "update ".$this->classifieds_table." set
										current_bid = \"".$show_autobid->MAXBID."\",
										price = \"".$show_autobid->MAXBID."\",
										minimum_bid = ".($increment + $show_autobid->MAXBID)."
										where id = \"".$this->auction_id."\"";
									$update_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "update current and min bid in auctions table by auction id");
									}
									if (!$update_result)
									{
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										return false;
									}

									$this->sql_query = "delete from ".$this->autobid_table."  where auction_id = \"".$this->auction_id."\"";
									$delete_autobid_result = $db->Execute($this->sql_query);
									if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
									if($this->configuration_data['debug_bid'])
									{
										$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "delete data in autobid table by auction id");
									}
									if (!$delete_autobid_result)
									{
										if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
										return false;
									}

									$this->bid_success = 3;
									$this->send_outbid_email($db,$this->bidder->ID);
									return true;

				  				} //endof else elseif ($show_autobid->MAXBID == $incremented_bid)
							} //end of else
		  				} //end of else of if (!result)
	  				} //end of else of if(current_bid == 0)
	  			//Sunit
				}
				else
				{
				//bid_amount not enough
					//raise your bid
					$this->bid_error = 2;
					if ($this->configuration_data['debug_bid'])
						echo $this->bid_error."2 - is error<br>\n";
					return false;
				}
			}
		}
		else
		{
			return false;
		}
	} //end of function process_bid

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_minimum_bid($db)
	{
		if ($this->auction->CURRENT_BID == 0)
			return $this->auction->STARTING_BID;
		else
		{
			//get bid increment
			$increment = $this->get_increment($db,$this->auction->CURRENT_BID);
			if ($increment)
				return $this->auction->CURRENT_BID + $increment;
			else
			{
				return false;
			}
		}
	} // end of function get_minimum_bid

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_increment($db,$amount)
	{
		$function_name = "get_increment";

		$this->sql_query = "select increment from ".$this->increments_table." where".
			"((low<=\"".$amount."\" AND high>=\"".$amount."\") OR".
               	  	"(low<\"".$amount."\" AND high<\"".$amount."\")) ORDER BY increment DESC limit 1";
        $increment_result = $db->Execute($this->sql_query);
        if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
		if($this->configuration_data['debug_bid'])
		{
			$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "increments_table", "get increment from increments table by low and high");
		}
		if (!$increment_result)
		{
			if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
			$this->error_message = urldecode($this->messages[100081]);
			return 1;
		}
		elseif  ($increment_result->RecordCount() == 1)
		{
			$show_increment = $increment_result->FetchNextObject();
			return $show_increment->INCREMENT;
		}
		else
		{
			return 1;
		}
	} //end of function get_increment

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function send_current_high_bidder_email($db,$bidder_id)
	{
		//echo "Inside senc_current_high_bidder_emai in auction_bid_class<br>";
		$this->page_id = 10168;
		$this->get_text($db);
		$this->bidder_info = $this->get_user_data($db,$bidder_id);
        $bidder_info = $this->bidder_info;

		if (($bidder_info) && ($this->auction))
		{
            $auction = $this->get_classified_data($db,$this->auction_id);
            $seller_info = $this->get_user_data($db,$this->auction->SELLER);
            $message_data["subject"] = urldecode($seller_info->COMPANY_NAME).": You're the high bidder on a ".urldecode($auction->TITLE);
			switch ($this->configuration_data['email_salutation_type'])
			{
				case 1: //display username
					$message_data["message"] = $bidder_info->USERNAME.",\n\n";
					break;
				case 2: //display firstname
					$message_data["message"] = $bidder_info->FIRSTNAME.",\n\n";
					break;
				case 3: //display firstname and lastname
					$message_data["message"] = $bidder_info->FIRSTNAME." ".$bidder_info->LASTNAME.",\n\n";
					break;
				case 4: //display lastname and firstname
					$message_data["message"] = $bidder_info->LASTNAME." ".$bidder_info->FIRSTNAME.",\n\n";
					break;
				case 5: //display email address
					$message_data["message"] = $bidder_info->EMAIL.",\n\n";
					break;
				default:
					$message_data["message"] = $bidder_info->USERNAME.",\n";
					break;
			}
			// $message_data["message"] .= urldecode($this->messages[102481]);
			$message_data["message"] .= $this->show_email_auction_specs($db,$bidder_id);

            $message_data["message"] .= "You are the new high bidder in the auction listed above. Please check often to verify that you are still the high bidder or visit ".urldecode($seller_info->COMPANY_NAME)." at ".urldecode($seller_info->ADDRESS).", ".urldecode($seller_info->CITY).", ".urldecode($seller_info->STATE)." ".urldecode($seller_info->ZIP)." who is sponsoring this event. They reserve the right to accept any reasonable offer onsite and to end auction if offer is accepted.\r\n\r\n";
            $message_data["message"] .= "If you visit the dealership, ask for stock number ".urldecode($this->auction->OPTIONAL_FIELD_18)." to see the ".urldecode($this->auction->TITLE)." in person.\r\n\r\n";
            $message_data["message"] .= "Good Luck!\r\n\r\n";
            $message_data["message"] .= urldecode($seller_info->FIRSTNAME);
            $message_data["message"] .= ' ';
            $message_data["message"] .= urldecode($seller_info->LASTNAME);
            $message_data["message"] .= "\r\n";
            $message_data["message"] .= urldecode($seller_info->COMPANY_NAME)." \r\n";
            $message_data["message"] .= urldecode($seller_info->EMAIL)."\r\n";
            $message_data["message"] .= urldecode($seller_info->PHONE);
            if($seller_info->PHONEEXT) $message_data["message"] .= " Ext.: ".urldecode($seller_info->PHNEEXT)."\r\n\r\n";

			$message_data["from"] = "From: ".$this->configuration_data['site_email'].$this->separator."Reply-to: ".$this->configuration_data['site_email'].$this->separator;

			$additional = "-f".$this->configuration_data['site_email']."\n\r";
			if ($this->configuration_data['email_configuration_type'] == 1)
				$message_data["message"] = str_replace("\n\n","\n",$message_data["message"]);
			if ($this->configuration_data['email_configuration'] == 1)
				mail($bidder_info->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"],$additional);
			elseif ($this->configuration_data['email_configuration'] == 2)
				mail($bidder_info->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"]);
			else
				mail($bidder_info->EMAIL, $message_data["subject"], $message_data["message"]);

//            mail('michael@peimic.com', $message_data["subject"], $message_data["message"], $message_data["from"],$additional);

            return true;
		}
		else
		{
			return false;
		}
	} //end of function send_current_high_bidder_email

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function send_outbid_email($db,$bidder_id)
	{
		$this->page_id = 10169;
		$this->get_text($db);
        $this->bidder_info = $this->get_user_data($db,$bidder_id);
        $bidder_info = $this->bidder_info;

		if (($bidder_info) && ($this->auction))
		{
        	$seller_info = $this->get_user_data($db,$this->auction->SELLER);
			switch ($this->configuration_data['email_salutation_type'])
			{
				case 1: //display username
					$message_data["message"] = $bidder_info->USERNAME.",\n\n";
					break;
				case 2: //display firstname
					$message_data["message"] = $bidder_info->FIRSTNAME.",\n\n";
					break;
				case 3: //display firstname and lastname
					$message_data["message"] = $bidder_info->FIRSTNAME." ".$bidder_info->LASTNAME.",\n\n";
					break;
				case 4: //display lastname and firstname
					$message_data["message"] = $bidder_info->LASTNAME." ".$bidder_info->FIRSTNAME.",\n\n";
					break;
				case 5: //display email address
					$message_data["message"] = $bidder_info->EMAIL.",\n\n";
					break;
				default:
					$message_data["message"] = $bidder_info->USERNAME.",\n";
					break;
			}
			//$message_data["message"] .= urldecode($this->messages[102474])."\n";
			$message_data["message"] .= $this->show_email_auction_specs($db);

            $message_data["message"] .= "We are sorry. You have been outbid in the auction described above. Please return to the auction and bid again to become the high bidder or visit ".urldecode($seller_info->COMPANY_NAME)." at ".urldecode($seller_info->ADDRESS).", ".urldecode($seller_info->CITY).", ".urldecode($seller_info->STATE)." ".urldecode($seller_info->ZIP)." who is sponsoring this event. They reserve the right to accept any reasonable offer onsite and to end auction if offer is accepted.\r\n\r\n";
            $message_data["message"] .= "If you visit the dealership, ask for stock number ".urldecode($this->auction->OPTIONAL_FIELD_18)." to see the ".urldecode($this->auction->TITLE)." in person.\r\n\r\n";
            $message_data["message"] .= "Good Luck!\r\n\r\n";
            $message_data["message"] .= urldecode($seller_info->FIRSTNAME);
            $message_data["message"] .= ' ';
            $message_data["message"] .= urldecode($seller_info->LASTNAME);
            $message_data["message"] .= "\r\n";
            $message_data["message"] .= urldecode($seller_info->COMPANY_NAME)." \r\n";
            $message_data["message"] .= urldecode($seller_info->EMAIL)."\r\n";
            $message_data["message"] .= urldecode($seller_info->PHONE);
            if($seller_info->PHONEEXT) $message_data["message"] .= " Ext.: ".urldecode($seller_info->PHNEEXT)."\r\n\r\n";

			$message_data["from"] = "From: ".$this->configuration_data['site_email'].$this->separator."Reply-to: ".$this->configuration_data['site_email'].$this->separator;
			$message_data["subject"] = urldecode($seller_info->COMPANY_NAME).": You're been outbid on a ".urldecode($this->auction->TITLE);
			$additional = "-f".$this->configuration_data['site_email']."\n\r";
			if ($this->configuration_data['email_configuration_type'] == 1)
				$message_data["message"] = str_replace("\n\n","\n",$message_data["message"]);
			if ($this->configuration_data['email_configuration'] == 1)
				mail($bidder_info->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"],$additional);
			elseif ($this->configuration_data['email_configuration'] == 2)
				mail($bidder_info->EMAIL, $message_data["subject"], $message_data["message"], $message_data["from"]);
			else
				mail($bidder_info->EMAIL, $message_data["subject"], $message_data["message"]);

//            mail('michael@peimic.com', $message_data["subject"], $message_data["message"], $message_data["from"],$additional);

            return true;
		}
		else
		{
			return false;
		}
	} //end of send_outbid_email

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_email_auction_specs($db,$bidder_id=0)
	{
		$this->page_id = 10170;
		$this->get_text($db);
		$function_name = "show_email_auction_specs";

		if ($this->auction)
		{
			$email_auction_data = "\r\n".urldecode($this->messages[102475])." ".urldecode($this->auction->TITLE);
			//$email_auction_data .= "$this->separator".urldecode($this->messages[102476]).urldecode($this->auction->DESCRIPTION);

			//get current bid
			$this->sql_query = "select current_bid from ".$this->classifieds_table."
				where id = \"".$this->auction_id."\"";
			$get_current_result = $db->Execute($this->sql_query);
			if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
			if($this->configuration_data['debug_bid'])
			{
				$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "classifieds_table", "get current bid from auctions table by auction id");
			}
			if (!$get_current_result)
			{
				if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
				return false;
			}
			elseif ($get_current_result->RecordCount() == 1)
			{
				$show_current_bid = $get_current_result->FetchNextObject();
				$email_auction_data .= "\r\n".urldecode($this->messages[102482]).urldecode($this->auction->PRECURRENCY)." ".$this->print_number($show_current_bid->CURRENT_BID)." ".urldecode($this->auction->POSTCURRENCY);
			}

			if ($bidder_id)
			{
				$this->sql_query = "select * from ".$this->autobid_table." where auction_id = \"".$this->auction_id."\" and bidder = ".$bidder_id;
				$get_autobid_result = $db->Execute($this->sql_query);
				if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
				if($this->configuration_data['debug_bid'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "classifieds_table", "get data from autobid table by auction and bidder id");
				}
				if (!$get_autobid_result)
				{
					if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
					return false;
				}
				elseif ($get_autobid_result->RecordCount() == 1)
				{
					$show_autobid = $get_autobid_result->FetchNextObject();
					$email_auction_data .= "\r\n".urldecode($this->messages[102483]).urldecode($this->configuration_data['precurrency'])." ".$this->print_number($show_autobid->MAXBID)." ".urldecode($this->configuration_data['postcurrency']);
				}
			}
			$email_auction_data .= "\r\n".urldecode($this->messages[102478])." ".date($this->configuration_data['entry_date_configuration'],$this->auction->ENDS);
			$email_auction_data .= "\r\n".urldecode($this->messages[102479])."\r\n".$this->configuration_data['classifieds_url']."?a=2&b=".$this->auction_id."\r\n\r\n";

            return $email_auction_data;
		}
	} //function show_email_auction_specs



//##############################################################################

	function get_bid_history($db,$auction_id)
	{
		$this->page_id = 10171;
		$this->get_text($db);
		$function_name = "get_bid_history";

		if ($auction_id)
		{
			$this->sql_query = "select * from ".$this->bid_table." where auction_id = ".$auction_id." order by time_of_bid asc,bid asc";
			$bid_history_result = $db->Execute($this->sql_query);
			if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
			if($this->configuration_data['debug_bid'])
			{
				$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "bid_table", "get data from autobid table by auction and bidder id");
			}

			if (!$bid_history_result)
			{
				if ($this->DEBUG_BID) echo $this->sql_query."<Br>\n";
				return false;
			}
			elseif ($bid_history_result->RecordCount() > 0)
			{
				$current_auction = $this->get_classified_data($db,$auction_id);
				//there are bids on this auction and show them
				if ($this->DEBUG_BID) echo $this->body." is the body so far<BR>\n";
				$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
				$this->body .= "<tr class=page_title>\n\t<td colspan=4>".urldecode($this->messages[102466])."</td>\n</tr>\n";
				$this->body .= "<tr class=page_instructions>\n\t<td colspan=4>".urldecode($this->messages[102467])."</td>\n</tr>\n";
				$this->body .= "<tr class=browsing_result_table_header>\n\t<td>".urldecode($this->messages[102469])."</td>\n\t";
				$this->body .= "<td>".urldecode($this->messages[102470])."</td>\n\t";
				if($current_auction->AUCTION_TYPE == 2)
				{
					// If dutch auctions show quantity label
					$this->body .= "<td>".urldecode($this->messages[103043])."</td>\n\t";
				}
				$this->body .= "<td>".urldecode($this->messages[102471])."</td>\n</tr>\n\t";
				$this->row_count = 0;
				while ($show_bid = $bid_history_result->FetchNextObject())
				{
					if (($this->row_count % 2) == 0)
						$css_row_tag = "browsing_result_table_body_even";
					else
						$css_row_tag = "browsing_result_table_body_odd";

					$this->body .= "<tr class=".$css_row_tag."><td>".date($this->configuration_data['entry_date_configuration'],$show_bid->TIME_OF_BID)."</td>\n\t";
					$this->body .= "<td>".urldecode($current_auction->PRECURRENCY)." ".$this->print_number($show_bid->BID)." ".urldecode($current_auction->POSTCURRENCY)."</td>\n\t";
					if($current_auction->AUCTION_TYPE == 2)
					{
						// If dutch auctions show quantity
						$this->body .= "<td>".$show_bid->QUANTITY."</td>\n\t";
					}
					$bidder_data = $this->get_user_data($db,$show_bid->BIDDER);
					$this->body .= "<td>".$bidder_data->USERNAME;
					if ($this->classified_user_id == $current_auction->SELLER)
						$this->body .= " (".$bidder_data->EMAIL.")";
					$this->body .= "</td>\n\t</tr>\n";
					$this->row_count++;
				}
				$this->body .= "<tr class=edit_approval_links>\n\t<td colspan=3><a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$auction_id." class=edit_approval_links>".urldecode($this->messages[102468])."</a></td>\n</tr>\n";
				$this->body .= "</table>";
			}
			else
			{
				//there were no bids for this auction
				$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
				$this->body .= "<tr class=page_title>\n\t<td colspan=3>".urldecode($this->messages[102466])."</td>\n</tr>\n";
				$this->body .= "<tr class=page_instructions>\n\t<td colspan=3>".urldecode($this->messages[102467])."</td>\n</tr>\n";
				$this->body .= "<tr class=page_instructions>\n\t<td colspan=3>".urldecode($this->messages[102472])."</td>\n</tr>\n";
				$this->body .= "<tr class=page_instructions>\n\t<td colspan=2><a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$auction_id." class=page_instructions>".urldecode($this->messages[102468])."</a></td>\n</tr>\n";
				$this->body .= "</table>";
			}
			$this->display_page($db);
			return true;
		}
		else
		{
			return false;
		}

	} //end of function get_bid_history

//##############################################################################

} //end of class Auction_bid
?>
