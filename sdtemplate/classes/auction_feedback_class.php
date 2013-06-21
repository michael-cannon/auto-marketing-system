<? //auction_feedback_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Auction_feedback extends Site {
	var $auction_id;
	var $auction_user_id;
	var $feedback_messages;
	var $user_data;
	
	var $debug_feedback = 0;

	// Debug variables
	var $filename = "auction_feedback_class.php";
	var $function_name;
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Auction_feedback($db,$language_id,$auction_user_id, $page=0, $product_configuration=0)
	{
		$this->Site($db,6,$language_id,$auction_user_id,$product_configuration);
		$this->auction_user_id = $auction_user_id;
		$this->user_data = $this->get_user_data($db,$this->auction_user_id);

		if($page)
			$this->page_result = $page;
		else
			$this->page_result = 1;
	} //end of function Auction_feedback

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function feedback_home($db)
	{
		$this->page_id = 10157;
		$this->get_text($db);
		$this->function_name = "feedback_home";
		
		// Get the total feedbacks count from the table
		$this->sql_query = "SELECT COUNT(*) AS total_feedback_count FROM ". $this->auctions_feedbacks_table." WHERE rated_user_id = ".$this->auction_user_id." AND done = 1";
		if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
		$total_feedback_count_result = $db->Execute($this->sql_query);
		$total_feedback_count_data = $total_feedback_count_result->FetchNextObject();
		if($this->configuration_data['debug_feedback'])
		{
			if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
			$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "count total feedbacks query");
		}							
				
		// Get the total feedbacks score from the table
		$this->sql_query = "SELECT sum(rate) AS feedback_score FROM ". $this->auctions_feedbacks_table." WHERE rated_user_id = ".$this->auction_user_id." AND done = 1";
		if ($this->debug_feedback)
			echo $this->sql_query."<bR>\n";
		$feedback_score_result = $db->Execute($this->sql_query);
		$feedback_score_data = $feedback_score_result->FetchNextObject();
		if($this->configuration_data['debug_feedback'])
		{
			if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
			$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "count total feedbacks query");
		}						
		
		$this->body .="<table border=0 cellpadding=2 cellspacing=1 width=100% align=center>\n";
		$this->body .="<tr class=section_title>\n\t<td colspan=2>\n\t".urldecode($this->messages[102410])."\n\t</td>\n</tr>\n";
		$this->body .="<tr class=page_instructions>\n\t<td colspan=2>\n\t".urldecode($this->messages[102411])."\n\t</td>\n</tr>\n";
		$this->body .="<tr>\n\t<td class=info_label width=50%>\n\t(".$this->user_data->USERNAME.") ".urldecode($this->messages[102412])."\n\t</td>\n\t";
		$this->body .="<td class=right_data width=50%>\n\t";
		if ($total_feedback_count_data->TOTAL_FEEDBACK_COUNT == 0)
			$this->body .= urldecode($this->messages[102436]);
		else
		{
			$this->body .= sprintf("%01.0f", $feedback_score_data->FEEDBACK_SCORE )." (".sprintf("%01.2f", (($feedback_score_data->FEEDBACK_SCORE/$total_feedback_count_data->TOTAL_FEEDBACK_COUNT) * 100))."%)";
			
		}
		$this->body .= "\n\t<a href=javascript:win('show_help.php?a=102826&l=1');><img src=".urldecode($this->configuration_data['help_image'])." hspace=2 vspace=0 border=0></a></td>\n</tr>\n";
		$this->body .= "<tr class=edit_approval_links>\n\t<td colspan=2>\n\t";
		$this->body .= "<a href=".$this->configuration_data['auctions_file_name']."?a=4&b=22&c=3>".urldecode($this->messages[102434])."</a>\n\t</td>\n</tr>\n";
		$this->body .= "<tr class=edit_approval_links>\n\t<td colspan=2>\n\t";
		$this->body .= "<a href=".$this->configuration_data['auctions_file_name']."?a=4&b=22&c=1>".urldecode($this->messages[102435])."</a>\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->display_page($db);
		return true;
	} //end of function feedback_home

//####################################################################################

	function feedback_about_user($db,$user_id,$auction_id=0,$page_id=0)
	{
		$this->page_id = 10158;
		$this->get_text($db);
		$this->function_name = "feedback_about_user";
		
		$this->sql_query = "select username,feedback_count,feedback_score,feedback_positive_count, date_joined from ".$this->userdata_table." where id = ".$user_id;
		if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
		$rated_result = $db->Execute($this->sql_query);
		if($this->configuration_data['debug_feedback'])
		{
			$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "get data from user data table by user id");
		}
		if ($rated_result)
		{
			$show_rated = $rated_result->FetchNextObject();
			$rated_username = $show_rated->USERNAME;
			$this->sql_query = "SELECT count(*) as total_feedbacks FROM ". $this->auctions_feedbacks_table. "
				where rated_user_id = ".$user_id." and done = 1 ORDER by date DESC ";
			if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
			$feedback_count_result = $db->Execute($this->sql_query);
			if($this->configuration_data['debug_feedback'])
			{
				$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "count feedbacks query");
			}
			
			if (!$feedback_count_result)
			{
				if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
				return false;
			}
			elseif ($feedback_count_result->RecordCount() == 1)
			{
				$show_count = $feedback_count_result->FetchNextObject();
			}
			if ($show_count->TOTAL_FEEDBACKS != 0)
			{
				$this->sql_query = "SELECT * FROM ".$this->auctions_feedbacks_table." where rated_user_id = ".$user_id." and done = 1 ORDER by date DESC ";
				
				if ($page_id)
				{
					$this->sql_query .= "limit ".($page_id * 10).",10";
				}
				else
				{
					$this->sql_query .= "limit 10";
				}
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "user's feedbacks query");
				}
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$feedback_result = $db->Execute($this->sql_query);

				/* 
				 *	Get current time and calculate out 1, 6, and 12 month times
				 *	formula for time is as follows:
				 *	(60 seconds * 60 minutes * 24 hours) * # of days
				 *	or 86400 seconds * # of days
				 */
				$current_time = $this->shifted_time($db);
				$one_month_time = $current_time - 2592000;
				$six_month_time = $current_time - 15724800;
				$twelve_month_time = $current_time - 31536000;
				
				/*
				 *	Past 1 month Feedbacks
				 *	Positive
				 */
				$this->sql_query = "SELECT COUNT(*) AS one_month_count FROM ".$this->auctions_feedbacks_table." WHERE rated_user_id = ".$user_id." AND date >= ". $one_month_time ." AND rate = 1 AND done = 1";
				$one_month_result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$one_month_pos_data = $one_month_result->FetchNextObject();
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "past 1 month positive feedback query");
				}

				/*
				 *	Past 1 month Feedbacks
				 *	Neutral
				 */
				$this->sql_query = "SELECT COUNT(*) AS one_month_count FROM ".$this->auctions_feedbacks_table." WHERE rated_user_id = ".$user_id." AND date >= ". $one_month_time ." AND rate = 0 AND done = 1";
				$one_month_result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$one_month_neu_data = $one_month_result->FetchNextObject();
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "past 1 month neutral feedback query");
				}

				/*
				 *	Past 1 month Feedbacks
				 *	Negative	
				 */
				$this->sql_query = "SELECT COUNT(*) AS one_month_count FROM ".$this->auctions_feedbacks_table." WHERE rated_user_id = ".$user_id." AND date >= ". $one_month_time ." AND rate = -1 AND done = 1";
				$one_month_result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$one_month_neg_data = $one_month_result->FetchNextObject();
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "past 1 month negative feedback query");
				}

				
				/*
				 *	Past 6 months Feedback
				 *	Positive
				 */
				$this->sql_query = "SELECT COUNT(*) AS six_month_count FROM ".$this->auctions_feedbacks_table." WHERE rated_user_id = ".$user_id." AND date >= ". $six_month_time ." AND rate = 1 AND done = 1";
				$six_month_result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$six_month_pos_data = $six_month_result->FetchNextObject();
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "past 6 months positive feedback query");
				}

				/*
				 *	Past 6 months Feedback
				 *	Neutral
				 */
				// neutral
				$this->sql_query = "SELECT COUNT(*) AS six_month_count FROM ".$this->auctions_feedbacks_table." WHERE rated_user_id = ".$user_id." AND date >= ". $six_month_time ." AND rate = 0 AND done = 1";
				$six_month_result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$six_month_neu_data = $six_month_result->FetchNextObject();
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "past 6 months neutral feedback query");
				}

				/*
				 *	Past 6 months Feedback
				 *	Negative
				 */
				$this->sql_query = "SELECT COUNT(*) AS six_month_count FROM ".$this->auctions_feedbacks_table." WHERE rated_user_id = ".$user_id." AND date >= ". $six_month_time ." AND rate = -1 AND done = 1";
				$six_month_result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$six_month_neg_data = $six_month_result->FetchNextObject();
				if($this->configuration_data['debug_feedback'])
				{
					echo $this->sql_query . ' is the past 6 months negative feedback query.<br>';
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "past 6 months negative feedback query");				
				}


				/*
				 *	Past 12 months Feedback
				 *	Positive
				 */
				$this->sql_query = "SELECT COUNT(*) AS twelve_month_count FROM ".$this->auctions_feedbacks_table." WHERE rated_user_id = ".$user_id." AND date >= ". $twelve_month_time ." AND rate = 1 AND done = 1";
				$twelve_month_result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$twelve_month_pos_data = $twelve_month_result->FetchNextObject();
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "past year positive feedback query");
				}

				/*
				 *	Past 12 months Feedback
				 *	Neutral
				 */
				$this->sql_query = "SELECT COUNT(*) AS twelve_month_count FROM ".$this->auctions_feedbacks_table." WHERE rated_user_id = ".$user_id." AND date >= ". $twelve_month_time ." AND rate = 0 AND done = 1";
				$twelve_month_result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$twelve_month_neu_data = $twelve_month_result->FetchNextObject();
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "past year neutral feedback query");
				}

				/*
				 *	Past 12 months Feedback
				 *	Negative
				 */
				$this->sql_query = "SELECT COUNT(*) AS twelve_month_count FROM ".$this->auctions_feedbacks_table." WHERE rated_user_id = ".$user_id." AND date >= ". $twelve_month_time ." AND rate = -1 AND done = 1";
				$twelve_month_result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$twelve_month_neg_data = $twelve_month_result->FetchNextObject();
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "past year negative feedback query");
				}

				// Get the total negative scores from the table
				$this->sql_query = "SELECT COUNT(*) AS neg_count FROM ". $this->auctions_feedbacks_table." WHERE rated_user_id = ". $user_id." AND rate = -1 AND done = 1";
				$neg_count_result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$neg_count_data = $neg_count_result->FetchNextObject();
				if($this->configuration_data['debug_feedback'])
				{
					echo $this->sql_query . ' is the count negative scores query.<br>';
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "count negative scores query");
				}
				
				// Get the total positive scores from the table
				$this->sql_query = "SELECT COUNT(*) AS pos_count FROM ". $this->auctions_feedbacks_table." WHERE rated_user_id = ". $user_id." AND rate = 1 AND done = 1";
				$pos_count_result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$pos_count_data = $pos_count_result->FetchNextObject();
				if($this->configuration_data['debug_feedback'])
				{
					echo $this->sql_query . ' is the count positive scores query.<br>';
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "count positive scores query");
				}				
				
				// Get the total feedbacks count from the table
				$this->sql_query = "SELECT COUNT(*) AS total_feedback_count FROM ". $this->auctions_feedbacks_table." WHERE rated_user_id = ". $user_id." AND done = 1";
				$total_feedback_count_result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$total_feedback_count_data = $total_feedback_count_result->FetchNextObject();
				if($this->configuration_data['debug_feedback'])
				{
					echo $this->sql_query . ' is the count positive scores query.<br>';
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "count total feedbacks query");
				}							
				
				// Get the total feedbacks score from the table
				$this->sql_query = "SELECT sum(rate) AS feedback_score FROM ". $this->auctions_feedbacks_table." WHERE rated_user_id = ". $user_id." AND done = 1";
				$feedback_score_result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$feedback_score_data = $feedback_score_result->FetchNextObject();
				if($this->configuration_data['debug_feedback'])
				{
					echo $this->sql_query . ' is the count positive scores query.<br>';
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "count total feedbacks query");
				}						

				// Set member since
				$member_since = date("F j, Y, g:i a", $show_rated->DATE_JOINED);		

				if (!$feedback_result)
				{
					return false;
				}
				else
				{
					//there was feedback about this user so give it to them
					$this->row_count = 0;
					$this->body .="<table border=0 width=100%>";
					$this->body .="<tr><td width=100% class=page_title>\n\t".urldecode($this->messages[102416])."\n\t</td></tr>";
					$this->body .="</table>";
					$this->body .="<table align=center border=0 cellpadding=2 cellspacing=1 width=100%>";
					$this->body .="<tr><td class=total_data>".urldecode($this->messages[102967])."</td><td class=total_data>".$feedback_score_data->FEEDBACK_SCORE."</td>";
					$this->body .= "<td class=header_text>".urldecode($this->messages[102968])."</td>";
					$this->body .="<td class=header_text width=12%>".urldecode($this->messages[102969])."</td>";
					$this->body .="<td class=header_text width=12%>".urldecode($this->messages[102970])."</td>";
					$this->body .="<td class=header_text width=12%>".urldecode($this->messages[102971])."</td></tr>";
					if(!$show_rated->FEEDBACK_COUNT)
						$this->body .="<tr><td class=total_data>".urldecode($this->messages[102972])."</td><td class=total_data> 0%</td>";
					else
						$this->body .="<tr><td class=total_data>".urldecode($this->messages[102972])."</td><td class=total_data>".sprintf("%01.0f", (($pos_count_data->POS_COUNT / $total_feedback_count_data->TOTAL_FEEDBACK_COUNT)*100))." %</td>";
					$this->body .="<td class=pos_feedback>".urldecode($this->messages[102973])."</td>";
					$this->body .="<td class=pos_feedback>". $one_month_pos_data->ONE_MONTH_COUNT ."</td>";
					$this->body .="<td class=pos_feedback>". $six_month_pos_data->SIX_MONTH_COUNT ."</td>";
					$this->body .="<td class=pos_feedback>". $twelve_month_pos_data->TWELVE_MONTH_COUNT ."</td></tr>";
					$this->body .="<tr><td class=total_data>".urldecode($this->messages[102974])."</td><td class=total_data>".$pos_count_data->POS_COUNT."</td>";
					$this->body .="<td class=neu_feedback>".urldecode($this->messages[102975])."</td>";
					$this->body .="<td class=neu_feedback>". $one_month_neu_data->ONE_MONTH_COUNT ."</td>";
					$this->body .="<td class=neu_feedback>". $six_month_neu_data->SIX_MONTH_COUNT ."</td>";
					$this->body .="<td class=neu_feedback>". $twelve_month_neu_data->TWELVE_MONTH_COUNT ."</td></tr>";
					$this->body .="<tr><td class=total_data>".urldecode($this->messages[102976])."</td><td class=total_data>".$neg_count_data->NEG_COUNT."</td>";
					$this->body .="<td class=neg_feedback>".urldecode($this->messages[102977])."</td>";
					$this->body .="<td class=neg_feedback>". $one_month_neg_data->ONE_MONTH_COUNT ."</td>";
					$this->body .="<td class=neg_feedback>". $six_month_neg_data->SIX_MONTH_COUNT ."</td>";
					$this->body .="<td class=neg_feedback>". $twelve_month_neg_data->TWELVE_MONTH_COUNT ."</td>";
					$this->body .="</tr></table><br>";

					$this->body .="<table  class=member_since><tr><td>". urldecode($this->messages[102736]) . $member_since . "</td></tr></table><br>";				

					$this->body .="<br><table align=center width=100% border=0>\n";
					$this->body .="<td class=section_title>".urldecode($this->messages[102978])."</td>";
					$this->body .="</table>";
					$this->body .="<table align=center cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
					$this->body .="<tr class=header_text>\n\t\t<td width=20%>".urldecode($this->messages[102417])."</td>\n\t\t";
					$this->body .="<td width=40%>\n\t\t".urldecode($this->messages[102418])."\n\t\t</td>\n\t\t";
					//echo "<td align=center>\n\t\t".urldecode($this->messages[102420])."\n\t\t</td>\n\t\t";
					$this->body .="<td width=8%>\n\t\t".urldecode($this->messages[102419])."\n\t\t</td>\n\t";
					$this->body .="<td>\n\t\t".urldecode($this->messages[102421])."\n\t\t</td>\n\t</tr>\n\t";

					// Get the number of feedbacks
					$total_returned = $feedback_result->RecordCount();

					// We need to requery the feedbacks so we get only enough for the page
					$this->sql_query = "SELECT * FROM ".$this->auctions_feedbacks_table." where rated_user_id = ".$user_id." and done = 1 ORDER by date DESC ";
					$this->sql_query .= "LIMIT ".(($this->page_result-1) * $this->configuration_data['number_of_feedbacks_to_display'])." ,".$this->configuration_data['number_of_feedbacks_to_display'];
					$feedback_result = $db->Execute($this->sql_query);
					if(!$feedback_result)
						return false;

					while ($show = $feedback_result->FetchNextObject())
					{
						$auction_data = $this->get_feedback_auction_data($db,$show->AUCTION_ID);

						$this->sql_query = "select username,feedback_count,feedback_score from ".$this->userdata_table." where id = ".$show->RATER_USER_ID;
						$user_result = $db->Execute($this->sql_query);
						if ($this->debug_feedback) echo $this->sql_query."<br>\n";
						if($this->configuration_data['debug_feedback'])
						{
							$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name,"userdata_table", "get user data query");
						}
						
						if ($user_result)
						{
							$show_user = $user_result->FetchNextObject();
							$rater_username = $show_user->USERNAME;
						}
						else
							$rater_username = $show->RATER_USER_ID;
							
						if (($this->row_count % 2) == 0)
							$css_row_tag = "result_set_even_rows";
						else
							$css_row_tag = "result_set_odd_rows";							
							
						$this->body .="<tr class=".$css_row_tag.">\n\t\t<td >".$rater_username."&nbsp;";
						if ($auction_data->SELLER != $show->RATER_USER_ID)
							$this->body .= urldecode($this->messages[103361]);
						else 
							$this->body .= urldecode($this->messages[103362]);
						$this->body .= "\n\t\t</td>\n\t\t";
						$this->body .="\n\t\t<td width=20%>".urldecode($auction_data->TITLE)." - ".$show->AUCTION_ID."\n\t\t</td>\n\t\t";
						$this->body .="\n\t\t<td align=center>";
						if ($show->RATE == 1)
							$this->body .= urldecode($this->messages[103363]);
						elseif ($show->RATE == 0)
							$this->body .= urldecode($this->messages[103364]);
						elseif ($show->RATE == -1)
							$this->body .= urldecode($this->messages[103365]);
						$this->body .= "&nbsp;\n\t\t</td>\n\t\t";
						$this->body .="\n\t\t<td align=center>".date("F d - G:i",$show->DATE)."&nbsp;\n\t\t</td>\n\t</tr>\n\t";
						$this->body .="<tr class=".$css_row_tag.">\n\t\t<td colspan=4>".urldecode($this->messages[102497])." - ".stripslashes(urldecode($show->FEEDBACK))."&nbsp;\n\t\t</td>\n\t</tr>";

						// Increment the row count
						$this->row_count++;

						if($this->row_count == $this->configuration_data['number_of_feedbacks_to_display'])
							break;
					}

					if ($this->configuration_data['number_of_feedbacks_to_display'] < $total_returned)
					{
						//display the link to the next 10
						$number_of_page_results = ceil($total_returned / $this->configuration_data['number_of_feedbacks_to_display']);
						$this->body .="<tr class=\"more_results\">\n\t<td colspan=100%>".urldecode($this->messages[200175])." ";
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
									$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=22&c=3&page=".$i." class=\"browsing_result_page_links\">".$i."</a> ";
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
											$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=22&c=3&page=".$page;
											$this->body .= " class=\"browsing_result_page_links\">".$page."</a> ";
										}
									}

								}
								else
								{
									//display the link to the section
									$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=22&c=3&page=".(($section*10)+1);
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
													$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=22&c=3&page=".$page_link;
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
												$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=22&c=3&page=".$page;
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
									$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=22&c=3&page=".(($section*100)+1);
									$this->body .= " class=\"browsing_result_page_links\">".(($section*100)+1)."</a>";
									if (($section+1) < $number_of_sections)
									$this->body .= " <font class=\"browsing_result_page_links\">...</font> ";
								}
							}
						}
						$this->body .="</td>\n</tr>\n";
					}

					if ($show_rated->FEEDBACK_COUNT > 0)
						$this->body .="<tr  class=info_label>\n\t<td colspan=4>\n\t".urldecode($this->messages[102498])." ".sprintf("%01.0f",$feedback_score_data->FEEDBACK_SCORE)." (".sprintf("%01.2f", (($feedback_score_data->FEEDBACK_SCORE/$total_feedback_count_data->TOTAL_FEEDBACK_COUNT) * 100))."%)\n\t</td>\n</tr>\n";
					if ($show_count->TOTAL_FEEDBACKS > 10)
					{
						//show different pages
						$this->body .="<tr>\n\t<td colspan=5>\n\t";
						$total_pages = ceil($show_count->TOTAL_FEEDBACKS/10);
						for ($i=1;$i<=$total_pages;$i++)
						{
							if ($page == $i)
							{
								$this->body .=" <b>".$i."</b> ";
							}
							else
							{
								$this->body .="<a href=".$this->configuration_data['auctions_file_name']."?a=4&b=22&c=3&p=".$i.">".$i."</a> ";
							}
						}
						$this->body .="\n\t</td>\n</tr>\n";
					}
					if ($user_id == $this->auction_user_id)
						$this->body .="<tr class=edit_approval_links>\n\t<td ><a href=".$this->configuration_data['auctions_file_name']."?a=4&b=22 class=edit_approval_links>".urldecode($this->messages[102422])."</a>\n\t</td>\n</tr>\n";
					if ($auction_id)
						$this->body .="<tr class=edit_approval_links>\n\t<td ><a href=".$this->configuration_data['auctions_file_name']."?a=2&b=".$auction_id." class=edit_approval_links>".urldecode($this->messages[103372])."</a>\n\t</td>\n</tr>\n";
					$this->body .="</table>\n\t"; //</td>\n</tr>\n";
				} //end of else
			}
			else
			{
				//there are no feedbacks to look at
				$this->body .="<table align=center cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n\t";
				$this->body .="<tr>\n\t<td class=section_title>\n\t".urldecode($this->messages[102416])."\n\t</td>\n</tr>\n";
				$this->body .="<tr>\n\t<td class=page_instructions>".urldecode($this->messages[102499])."</td>\n</tr>\n";
				if ($user_id == $this->auction_user_id)
					$this->body .="<tr class=edit_approval_links>\n\t<td ><a href=".$this->configuration_data['auctions_file_name']."?a=4&b=22 class=edit_approval_links>".urldecode($this->messages[102422])."</a>\n\t</td>\n</tr>\n";
				if ($auction_id)
					$this->body .="<tr class=edit_approval_links>\n\t<td ><a href=".$this->configuration_data['auctions_file_name']."?a=2&b=".$auction_id." class=edit_approval_links>".urldecode($this->messages[103372])."</a>\n\t</td>\n</tr>\n";
				
				$this->body .="</table>\n";
			}
			$this->display_page($db);
			return true;
		}
		else
		{
			if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
			return false;
		}
	} //end of function feedback_about_you

//####################################################################################

	function list_open_feedback($db,$user_id=0)
	{
		$this->page_id = 10159;
		$this->get_text($db);
		$this->function_name = "list_open_feedback";

		if ($user_id)
		{
			$this->sql_query = "select * from ".$this->auctions_feedbacks_table." where rater_user_id=\"".$user_id."\" AND done=0";
			if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
			$result = $db->Execute($this->sql_query);
			if($this->configuration_data['debug_feedback'])
			{
				$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "open feedback query");
			}
			if ($this->debug_feedback)
			{
				echo $result->RecordCount()." is the number of open feedback returned<br>\n";
			}
			if (!$result)
			{
				if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
				return false;
			}
			elseif ($result->RecordCount() == 0)
			{
				//no feedbacks open
				$this->body .="<table cellpadding=2 cellspacing=1 border=0  align=center width=100%>\n";
				$this->body .="<tr>\n\t<td class=section_title>\n\t";
				$this->body .=urldecode($this->messages[102423])."\n\t</td>\n</tr>\n";
				$this->body .="<tr>\n\t<td class=page_instructions>\n\t";
				$this->body .=urldecode($this->messages[102424])."\n\t</td>\n</tr>\n";
				$this->body .="<tr>\n\t<td align=center>\n\t";

				//$this->display_page($db);
				//return true;
			}
			else
			{
				//there are auctions this user can leave feedback for
				//so show them
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
				$this->body .="<tr class=section_title>\n\t<td colspan=6>\n\t".urldecode($this->messages[102423])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_instructions>\n\t<td colspan=6>\n\t".urldecode($this->messages[102425])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=browsing_result_table_header>\n\t\t<td align=center>\n\t\t".urldecode($this->messages[102501])."\n\t\t</td>\n\t\t";
				$this->body .="<td align=center>\n\t\t".urldecode($this->messages[102426])."\n\t\t";
				$this->body .="<td align=center>\n\t\t".urldecode($this->messages[102427])."\n\t\t</td>\n\t\t";
				$this->body .="<td align=center>\n\t\t".urldecode($this->messages[102428])."\n\t\t</td>\n\t\t";
				$this->body .="<td align=center>\n\t\t".urldecode($this->messages[102429])."\n\t\t</td>\n\t";
				$this->body .="<td align=center>\n\t\t&nbsp;\n\t\t</td>\n\t";
				$this->body .="</tr>\n\t";
				$this->row_count = 0;
				while ($show = $result->FetchNextObject())
				{
					//pull from the feedback table with this users criteria
					//if nothing comes back then this user has not rated this auction yet
					//if something comes back this user has already rated this auction
					$this->sql_query = "SELECT title,date,ends,seller FROM ".$this->classifieds_table." WHERE id = ".$show->AUCTION_ID;
					if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
					$auction_result = $db->Execute($this->sql_query);
					if($this->configuration_data['debug_feedback'])
					{
						$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "classifieds_table", "user crieria query");
					}

					if ($auction_result)
					{
						if ($auction_result->RecordCount() == 1)
						{
							$show_auction = $auction_result->FetchNExtObject();
						}
						elseif ($auction_result->RecordCount() == 0)
						{
							//get auction data from expired table
							$this->sql_query = "SELECT title,date,ends,seller FROM ".$this->classifieds_expired_table." WHERE id = ".$show->AUCTION_ID;
							$auction_result = $db->Execute($this->sql_query);
							if($this->configuration_data['debug_feedback'])
							{
								$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "classifieds_expired_table", "expired table query");
							}
							
							if ($auction_result->RecordCount() == 1)
								$show_auction = $auction_result->FetchNExtObject();
						}
						if ($show_auction)
						{
							$this->sql_query = "select username from ".$this->userdata_table." where id = ".$show->RATED_USER_ID;
							$user_result = $db->Execute($this->sql_query);
							if($this->configuration_data['debug_feedback'])
							{
								$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "userdata_table", "username query");
							}

							if ($user_result)
							{
								if ($user_result->RecordCount() == 1)
								{
									$show_user = $user_result->FetchNExtObject();
									$rated_user = $show_user->USERNAME;
								}
								else
									$rated_user = $show->RATED_USER_ID;
							}
							else
								$rated_user = $show->RATED_USER_ID;
							if (($this->row_count % 2) == 0)
								$css_tag = "result_set_even_rows";
							else
								$css_tag = "result_set_odd_rows";
							$this->body .="<tr class=".$css_tag.">\n\t\t<td align=center>".urldecode($show_auction->TITLE)."\n\t\t</td>\n\t\t";
							$this->body .="<td align=center>".date("F d, G:i",$show_auction->DATE)."\n\t\t</td>\n\t\t";
							$this->body .="<td align=center>".date("F d, G:i",$show_auction->ENDS)."\n\t\t</td>\n\t\t";
							$this->body .="<td align=center>";
							if ($show_auction->SELLER == $show->RATED_USER_ID)
							{
								$this->body .=$rated_user." (".urldecode($this->messages[102430]).")";
							}
							else
							{
								$this->body .=$rated_user." (".urldecode($this->messages[102431]).")";
							}
							$this->body .="\n\t\t</td>\n\t\t";
							if ($show_auction->SELLER == $show->RATED_USER_ID)
							{
								$this->body .="<td align=center><a href=".$this->configuration_data['auctions_file_name']."?a=4&b=22&c=2&d=".$show->AUCTION_ID." class=".$css_tag.">".urldecode($this->messages[102432])."</a>\n\t\t</td>\n\t\t";
							}
							else
							{
								$this->body .="<td align=center><a href=".$this->configuration_data['auctions_file_name']."?a=4&b=22&c=2&d=".$show->AUCTION_ID."&f=".$show->RATED_USER_ID." class=".$css_tag.">".urldecode($this->messages[102432])."</a>\n\t\t</td>\n\t\t";
							}
							$this->body .="<td align=center><a href=".$this->configuration_data['auctions_file_name']."?a=2&b=".$show->AUCTION_ID."&z=2 class=".$css_tag.">".urldecode($this->messages[102433])."</a>\n\t\t</td>\n\t</tr>\n\t";
							$this->row_count++;
						}
					}
					else 
					{
						if ($this->debug_feedback) echo $this->sql_query."<bR>\n";	
					}
				}//end of while
				if ($this->row_count == 0)
				{
					$this->body .="<tr>\n\t<td class=page_instructions colspan=6>\n\t";
					$this->body .=urldecode($this->messages[102424])."\n\t</td>\n</tr>\n";
				}
				//$this->body .="</table>\n\t</td>\n</tr>\n";
				//$this->body .= "<tr class=edit_approval_links>\n\t<td>\n\t<a href=".$this->configuration_data['auctions_file_name']."?a=4&b=22 class=edit_approval_links>".urldecode($this->messages[102804])."</a>\n\t</td>\n</tr>\n";
				////$this->body .= "<tr class=edit_approval_links>\n\t<td>\n\t<a href=".$this->configuration_data['auctions_file_name']."?a=4 class=edit_approval_links>".urldecode($this->messages[102803])."</a>\n\t</td>\n</tr>\n";
				//$this->body .= "</table>";

			}
			$this->body .="<a href=".$this->configuration_data['auctions_file_name']."?a=4&b=22 class=edit_approval_links>".urldecode($this->messages[102502])."</a>\n\t</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td align=center>\n\t";
			$this->body .="<a href=".$this->configuration_data['auctions_file_name']."?a=4 class=edit_approval_links>".urldecode($this->messages[102803])."</a>\n\t</td>\n</tr>\n";				
			$this->body .="</table>\n";		
			$this->display_page($db);
			return true;
		}
		else
		{
			return false;
		}
	} // end of function list_open_feedback

//####################################################################################

	function leave_feedback($db,$user_id=0,$auction_id=0,$info=0,$rated_id=0)
	{
		$this->page_id = 10160;
		$this->get_text($db);
		$function_name = "leave_feedback";

		if ($user_id)
		{
			if ($auction_id)
			{
				$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$auction_id;
				$result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "classifieds_table", "auction query");
				}

				if (!$result)
				{
					if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					//echo "Inside Here<bR>";
					$show_auction = $result->FetchNextObject();
				}
				elseif ($result->RecordCount() == 0)
				{
					//get auction data from expired table
					$this->sql_query = "SELECT * FROM ".$this->classifieds_expired_table." WHERE id = ".$auction_id;
					$result = $db->Execute($this->sql_query);
					if($this->configuration_data['debug_feedback'])
					{
						$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "classifieds_expired_table", "expired auction query");
					}
					
					if (!$result)
					{
						return false;
					}
					elseif ($result->RecordCount() == 1)
					{
						$show_auction = $result->FetchNextObject();
					}
					else
						return false;
				}

				if (($show_auction->AUCTION_TYPE == 2) && ($rated_id))
				{
					$this->sql_query = "select * from ".$this->auctions_feedbacks_table." where rater_user_id = ".$user_id." and auction_id = ".$auction_id." and rated_user_id = ".$rated_id;
				}
				elseif (($show_auction->AUCTION_TYPE == 2) && ($show_auction->SELLER != $user_id))
				{
					$this->sql_query = "select * from ".$this->auctions_feedbacks_table." where rater_user_id = ".$user_id." and auction_id = ".$auction_id;
					$rated_id = $show_auction->SELLER;
				}
				else
				{
					$this->sql_query = "select * from ".$this->auctions_feedbacks_table." where rater_user_id = ".$user_id." and auction_id = ".$auction_id;
				}
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $function_name, "auctions_feedbacks_table", "rater_user_id query");
				}
				$result = $db->Execute($this->sql_query);

				if (!$result)
				{
					//echo $this->sql_query." is the query<br>\n";

					return false;
				}
				elseif ((($result->RecordCount() == 1)) ||
					(($result->RecordCount() > 0) && ($rated_id)))
				{
					$show = $result->FetchNExtObject();
					if ($show->DONE == 0)
					{
						if ($show_auction->AUCTION_TYPE == 2)
							$this->sql_query = "select username from ".$this->userdata_table." where id = ".$rated_id;
						else
							$this->sql_query = "select username from ".$this->userdata_table." where id = ".$show->RATED_USER_ID;

						$result = $db->Execute($this->sql_query);
						if($this->configuration_data['debug_feedback'])
						{
							$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "userdata_table", "username query");
						}
						
						if (!$result)
						{
							//edho $this->sql_query." is the query<br>\n";

							return false;
						}
						elseif ($result->RecordCount() == 1)
						{
							$show_username = $result->FetchNextObject();
						}

						$this->body .="<form action=".$this->configuration_data['auctions_file_name']."?a=4&b=22&c=2&d=".$auction_id." method=post>\n\t";
						if (($show_auction->AUCTION_TYPE == 2) && ($rated_id))
							$this->body .="<input type=hidden name=e[rated_id] value=".$rated_id.">\n";
						$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
						$this->body .="<tr class=section_title>\n\t<td colspan=2>\n\t".urldecode($this->messages[102520])."\n\t</td>\n</tr>\n";
						$this->body .="<tr class=page_instructions>\n\t\t<td colspan=2>".urldecode($this->messages[102503])."</td>\n\t</tr>\n\t";
						$this->body .="<tr>\n\t\t<td class=info_label>".urldecode($this->messages[102504])."</td>\n\t\t";
						$this->body .="<td class=right_data>\n\t\t".$show_username->USERNAME."\n\t\t</td>\n\t</tr>\n\t";

						$this->body .="<tr>\n\t\t<td class=info_label>".urldecode($this->messages[102505])."</td>\n\t\t";
						$this->body .="<td class=right_data>".$auction_id."- ".urldecode($show_auction->TITLE)."</td>\n\t</tr>\n\t";

						$this->body .="<tr>\n\t\t<td class=info_label>".urldecode($this->messages[102506])."</td>\n\t\t";
						$this->body .="<td class=right_data>".date("F d, G:i",$show_auction->DATE)."</td>\n\t</tr>\n\t";

						$this->body .="<tr>\n\t\t<td class=info_label>".urldecode($this->messages[102507])."</td>\n\t\t";
						$this->body .="<td class=right_data>".date("F d, G:i",$show->DATE)."</td>\n\t</tr>\n\t";

						$this->body .="<tr>\n\t\t<td class=info_label>".urldecode($this->messages[102508]);
						if (strlen(trim($this->feedback_messages["rating"])) > 0)
							$this->body .="<br><font class=site_errors>".$this->feedback_messages["rating"]."</font>";
						$this->body .="</td>\n\t\t";
						$this->body .="<td class=info_label>\n\t\t
							<input type=radio name=e[rating] ";
							if ($info["rating"] == -1) $this->body .="checked";
							$this->body .=" value=a>".urldecode($this->messages[102735])."<br>\n\t\t
							<input type=radio name=e[rating] ";
							if ($info["rating"] == 0) $this->body .="checked";
							$this->body .=" value=b>".urldecode($this->messages[102734])."<br>\n\t\t
							<input type=radio name=e[rating] ";
							if ($info["rating"] == 1) $this->body .="checked";
							$this->body .=" value=c>".urldecode($this->messages[102733])."\n\t\t</td>\n\t</tr>\n\t";

						$this->body .="<tr>\n\t\t<td class=info_label>".urldecode($this->messages[102514]);
						if (strlen(trim($this->feedback_messages["feedback"])) > 0)
							$this->body .="<br><font class=site_errors>".$this->feedback_messages["feedback"]."</font>";
						$this->body .="</td>\n\t\t";
						$this->body .="<td>\n\t\t<textarea name=e[feedback] rows=10 cols=30>".stripslashes($info["feedback"])."</textarea>\n\t\t</td>\n\t</tr>\n\t";

						$this->body .="<tr>\n\t\t<td colspan=2><input type=submit name=save_feedback value=\"".urldecode($this->messages[102516])."\"><input type=reset></form></td>\n\t</tr>\n\t";
						$this->body .="</table>\n\t";
						//echo $this->body."Hello<br>";
						$this->display_page($db);
						return true;
					}
					else
					{
						//already left feedback for this auction
						$this->body .="<table cellpadding=2 cellspacing=1 border=1 align=center width=100%>\n";
						$this->body .="<tr class=section_title>\n\t<td colspan=2>\n\t".urldecode($this->messages[102517])."\n\t</td>\n</tr>\n";
						$this->body .="<tr class=page_instructions>\n\t<td>".urldecode($this->messages[102518])."</td>\n</tr>\n";
						$this->body .="</table>\n";
						$this->display_page($db);
						return true;
					}
				}
				else
				{
					//too many feedbacks for this user for this auction
					$this->body .="too many<br>\n";
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
			$this->error_message = urldecode($this->messages[102519]);
			return false;
		}
		$this->display_page($db);
		return true;
	} // end of function leave_feedback

//####################################################################################

	function check_feedback($db,$auction_id=0,$user_id=0,$info=0)
	{
		if ($this->debug_feedback) echo "TOP OF CHECK_FEEDBACK<br>\n";
		$this->page_id = 10160;
		$this->get_text($db);
		$this->function_name = "check_feedback";
		$error=0;

		$feedback = ltrim(chop($info["feedback"]));
		if (strlen(trim($feedback)) == 0)
		{
			$error++;
			$this->feedback_messages["feedback"] = urldecode($this->messages[102521]);
		}

		if (empty($info["rating"]))
		{
			$error++;
			$this->feedback_messages["rating"] = urldecode($this->messages[102522]);
		}
		if ($error > 0)
		{
			return false;
		}

		$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$auction_id;
		if ($this->debug_feedback) echo $this->sql_query."<br>\n";
		$auction_result = $db->Execute($this->sql_query);
		if($this->configuration_data['debug_feedback'])
		{
			$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "classifieds_table", "auction query");
		}	
		
		if (!$auction_result)
		{
			if ($this->debug_feedback) echo $this->sql_query."<br>\n";
			return false;
		}
		else
		{
			if ((!$auction_result) || ($auction_result->RecordCount() == 0))
			{
				$this->sql_query = "select * from ".$this->classifieds_expired_table." where id = ".$auction_id;
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				$auction_result = $db->Execute($this->sql_query);
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "classifieds_expired_table", "expired auctions query");
				}
				
				if ((!$auction_result) || ($auction_result->RecordCount() == 0))
				{
					if ($this->debug_feedback) echo $this->sql_query."<br>\n";
					return false;
				}
			}

			$show_auction = $auction_result->FetchNextObject();
			if ($this->debug_feedback)
			{
				echo $show_auction->SELLER." is the seller<bR>\n";
				echo $user_id." is the user id<br>\n";
				echo $show_auction->AUCTION_TYPE . " is the auction type<br>";
				echo $auction_id." is the auction_id<bR>\n";
			}
			if ($show_auction->AUCTION_TYPE == 1)
			{
				if ($show_auction->SELLER != $user_id)
				{
					$high_bidder = $this->get_high_bidder($db,$auction_id);
					if ($this->debug_feedback) 
					{
						echo $high_bidder["bidder"]. " is the high bidder<br>";
						echo $user_id." is the user id<BR>\n";
					}
					if ($high_bidder)
					{
						if ($high_bidder["bidder"] != $user_id)
						{
							if ($this->debug_feedback) echo "high bidder does not match user_id<br>\n";
							return false;
						}
						else
						{
							//the buyer is rating the seller
							return true;
						}
					}
					else
					{
						//no bids were accepted for this auction
						//no feedback can be accepted either
						return false;
					}
				}
				else
				{
					//the seller is rating the buyer
					return true;
				}
			}
			elseif ($show_auction->AUCTION_TYPE == 2)
			{
				//this is a dutch auction
				//check to see if bidder or seller
				if ($show_auction->SELLER != $user_id)
				{
					$this->sql_query = "select * from ".$this->auctions_feedbacks_table." where rater_user_id = ".$user_id." and auction_id = ".$auction_id;
					$bidder_result = $db->Execute($this->sql_query);
					if ($this->debug_feedback) echo $this->sql_query."<br>\n";
					if($this->configuration_data['debug_feedback'])
					{
						$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "rater_user_id query");
					}
					
					if (!$bidder_result)
					{
						if ($this->debug_feedback) echo $this->sql_query."<br>\n";
						return false;
					}
					elseif ($bidder_result->RecordCount() == 1)
					{
						if ($this->debug_feedback) echo "BOTTOM OF CHECK_FEEDBACK<bR><br>\n";
						//this buyer can rate the seller of this auction
						return true;
					}
				}
				else
				{
					//the seller is rating the buyer
					return true;
				}
			}
			else
			{
				return false;
			}
		}

		if ($error > 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	} //end of function check_feedback

//####################################################################################

	function save_feedback($db,$auction_id=0,$user_id=0,$info=0)
	{
		$this->function_name = "save_feedback";
		// there are no errors in the feedback field
		// lets enter it into the database
		if ($auction_id)
		{
			$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$auction_id;
			$auction_result = $db->Execute($this->sql_query);
			if ($this->debug_feedback) echo $this->sql_query."<br>\n";
			if($this->configuration_data['debug_feedback'])
			{
				$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "classifieds_table", "auction query");
			}
			
			if (!$auction_result)
			{
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				return false;
			}
			elseif ((!$auction_result) || ($auction_result->RecordCount() == 0))
			{
				$this->sql_query = "select * from ".$this->classifieds_expired_table." where id = ".$auction_id;
				$auction_result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "classifieds_expired_table", "expired auction query");
				}
				
				if ((!$auction_result) || ($auction_result->RecordCount() == 0))
				{
					if ($this->debug_feedback) echo $this->sql_query."<br>\n";
					return false;
				}
				$show_auction = $auction_result->FetchNextObject();
			}
			elseif ($auction_result->RecordCount() == 1)
			{
				//echo $show_auction->AUCTION_TYPE." is auction_type 1<bR>\n";
				$show_auction = $auction_result->FetchNextObject();
			}
			else
			{
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				return false;
			}

			// Check for bad words and bad html
			$this->get_badword_array($db);
			$this->get_html_disallowed_array($db);
			$info["feedback"] = $this->replace_disallowed_html($db, $info["feedback"]);
			$info["feedback"] = $this->check_for_badwords($info["feedback"]);

			//echo $info["rating"] . ' is the rating<br>';
			if ($info["rating"] == "a")
				$rating = -1;
			elseif ($info["rating"] == "b")
				$rating = 0;				
			elseif ($info["rating"] == "c")
				$rating = 1;
				
			//echo $show_auction->AUCTION_TYPE." is auction_type 2<bR>\n";
			if (($user_id) && ($info))
			{
				if ($show_auction->AUCTION_TYPE == 1)
				{
					$this->sql_query = "update ".$this->auctions_feedbacks_table."
						set
						feedback=\"".urlencode($info["feedback"])."\",
						rate= ".$rating.",
						date = ".$this->shifted_time($db).",
						done = 1
						where
						auction_id=".$auction_id." AND
						rater_user_id=".$user_id;
				}
				elseif ($show_auction->AUCTION_TYPE == 2)
				{
					$this->sql_query = "update ".$this->auctions_feedbacks_table."
						set
						feedback=\"".urlencode($info["feedback"])."\",
						rate= ".$rating.",
						date = ".$this->shifted_time($db).",
						done = 1
						where
						auction_id=".$auction_id." AND
						rater_user_id=".$user_id." AND
						rated_user_id = ".$info["rated_id"];
				}
				else
				{
					if ($this->debug_feedback) echo $this->sql_query."<br>\n";
					return false;
				}
				$result = $db->Execute($this->sql_query);
				if ($this->debug_feedback) echo $this->sql_query."<br>\n";
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "update feedback query");
				}
				
				if (!$result)
				{
					if ($this->debug_feedback) echo $this->sql_query."<br>\n";
					return false;
				}
				else
				{
					//echo $show_auction->AUCTION_TYPE." is auction_type<bR>\n";
					if ($show_auction->AUCTION_TYPE == 1)
					{
						$this->sql_query = "select rated_user_id from ".$this->auctions_feedbacks_table." where auction_id = ".$auction_id." and rater_user_id=".$user_id;

						$result = $db->Execute($this->sql_query);
						if($this->configuration_data['debug_feedback'])
						{
							$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "auctions_feedbacks_table", "rated_user_id query");
						}	
						
						if (!$result)
						{
							//error in getting the rated user id
							if ($this->debug_feedback) echo $this->sql_query."<br>\n";
							return false;
						}
						elseif ($result->RecordCount() == 1)
						{
							$show_rated_user_id = $result->FetchNextObject();
							$rated_user_id = $show_rated_user_id->RATED_USER_ID;
						}
						else
						{
							if ($this->debug_feedback) echo $this->sql_query."<br>\n";
							return false;
						}
					}
					else
					{
						$rated_user_id = $info["rated_id"];
					}

					$this->sql_query = "select sum(rate) as feedback_score, count(rate) as feedback_count from ".$this->auctions_feedbacks_table." where rated_user_id = ".$rated_user_id." and done = 1";
					if($this->configuration_data['debug_feedback'])
					{
						$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name,"auctions_feedbacks_table", "feedback_score query");
					}
					if ($this->debug_feedback) echo $this->sql_query."<br>\n";
					$result = $db->Execute($this->sql_query);
					
					// Get the count of positive scores from the database
					$this->sql_query = "select count(*) as feedback_positive_count from ".$this->auctions_feedbacks_table." where rated_user_id = ".$rated_user_id." and rate = 1 and done = 1";
					if($this->configuration_data['debug_feedback'])
					{
						$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name,"auctions_feedbacks_table", "positive count query");
					}
					if ($this->debug_feedback) echo $this->sql_query."<br>\n";
					$positive_result = $db->Execute($this->sql_query);
					$new_result = $positive_result->FetchNextObject();

					if (!$result || !$positive_result)
					{
						// error in getting the rated user id
						$this->sql_query = "update ".$this->auctions_feedbacks_table."
							set
							feedback=\"\",
							rate=0,
							done = 0
							where
							auction_id=\"".$auction_id."\" AND
							rater_user_id=\"".$user_id."\"";
						$result = $db->Execute($this->sql_query);
						if($this->configuration_data['debug_feedback'])
						{
							$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name,"auctions_feedbacks_table", "update feedback query");
						}
						if ($this->debug_feedback) echo $this->sql_query."<br>\n";
						return false;
					}
					elseif ($result->RecordCount() == 1)
					{
						$show_ratings = $result->FetchNextObject();

						// Check to make sure we dont need to update the feedback icon
						$this->sql_query = "select filename from ".$this->auctions_feedback_icons_table." where begin <= ".$show_ratings->FEEDBACK_SCORE." AND end >= ".$show_ratings->FEEDBACK_SCORE;
						if($this->configuration_data['debug_feedback'])
						{
							$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name,"auctions_feedback_icons_table", "get filename from feedback icons table");
						}
						if ($this->debug_feedback) echo $this->sql_query."<br>\n";
						$update = $db->Execute($this->sql_query);
						if(!$update)
						{
							// error so get out of there
							if ($this->debug_feedback) echo $this->sql_query."<br>\n";
							return false;
						}
						$update_result = $update->FetchNextObject();
					
						$this->sql_query = "update ".$this->userdata_table." set
							feedback_score = ".$show_ratings->FEEDBACK_SCORE.",
							feedback_count = ".$show_ratings->FEEDBACK_COUNT.",
							feedback_positive_count = ".$new_result->FEEDBACK_POSITIVE_COUNT.",
							feedback_icon = \"".$update_result->FILENAME."\" where id = ".$rated_user_id;
						if($this->configuration_data['debug_feedback'])
						{
							$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name,"userdata_table", "update userdata query");
						}
						if ($this->debug_feedback) echo $this->sql_query."<br>\n";
						$result = $db->Execute($this->sql_query);
						
						if (!$result)
						{
							//back out feedback
							if ($this->debug_feedback) echo $this->sql_query."<br>\n";
							return false;
						}
					}
					else
					{
						//no ratings yet for this user
						//when there should have been
						$this->sql_query = "update ".$this->auctions_feedbacks_table." set
							feedback=\"\",
							rate=0,
							done = 0
							where
							auction_id=".$auction_id." AND
							rater_user_id=".$user_id;
						$result = $db->Execute($this->sql_query);
						if ($this->debug_feedback) echo $this->sql_query."<br>\n";
						if($this->configuration_data['debug_feedback'])
						{
							$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name,"auctions_feedbacks_table", "update feedback query");
						}
						if ($this->debug_feedback) echo $this->sql_query."<br>\n";
						return false;
					}
				}
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
		return true;
	} //end of function save_feedback

//####################################################################################

	function feedback_thank_you($db)
	{
		$this->page_id = 10161;
		$this->get_text($db);
		$this->function_name = "feedback_thank_you";
		
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
		$this->body .="<tr class=section_title>\n\t<td>\n\t".urldecode($this->messages[102526])."</td>\n</tr>\n";
		$this->body .="<tr class=page_instructions>\n\t<td>".urldecode($this->messages[102525])."</td>\n</tr>\n";
		$this->body .="<tr>\n\t<td align=center>\n\t";
		$this->body .="<a href=".$this->configuration_data['auctions_file_name']."?a=4&b=22 class=edit_approval_links>".urldecode($this->messages[102527])."</a>\n\t</td>\n</tr>\n";
		$this->body .="</table>\n";
		$this->display_page($db);
		return true;
	} //end of function feedback_thank_you

//####################################################################################

	function feedback_error()
	{
		$this->page_id = 10162;
		$this->get_text($db);
		$this->function_name = "feedback_error";

		$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=600>\n";
		$this->body .="<tr>\n\t<td class=site_errors align=center>".urldecode($this->messages[2704])."</td>\n</tr>\n";
		if ($this->error_message)
			$this->body .="<tr>\n\t<td>".$this->error_messages."</td>\n</tr>\n";
		$this->body .="</table>\n";
		$this->display_page($db);
	} //end of function feedback_error

//####################################################################################

	function get_feedback_auction_data($db,$auction_id=0)
	{
		$this->function_name = "get_feedback_auction_data";

		if ($auction_id)
		{
			$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$auction_id;
			$result = $db->Execute($this->sql_query);
			if($this->configuration_data['debug_feedback'])
			{
				$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name,"classifieds_table", "auctions table query");
			}
			
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($result->RecordCount() > 1 )
			{
				//more than one auction matches
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($result->RecordCount() <= 0)
			{
				$this->sql_query = "select * from ".$this->classifieds_expired_table." where id = ".$auction_id;
				$result = $db->Execute($this->sql_query);
				if($this->configuration_data['debug_feedback'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name,"classifieds_expired_table", "expired auctions query");
				}
				
				if (!$result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}
				elseif ($result->RecordCount() > 1 )
				{
					//more than one auction matches
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}
				elseif ($result->RecordCount() <= 0)
				{
					return false;
				}
			}
			$show = $result->FetchNextObject();
			return $show;
		}
		else
		{
			return false;
		}

	} //end of function get_feedback_auction_data

//####################################################################################

} //end of class Auction_feedback