<? //user_management_list_bids_auctions.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Black_list_buyers extends Site {

	var $auction_id;
	var $auction_user_id;
	var $feedback_messages;
	var $user_data;
	var $search_error_message;

	// Debug variables
	var $filename = "user_management_list_bids_auctions.php";
	var $function_name;
	
	var $debug_blacklist =0;
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Black_list_buyers($db,$language_id,$auction_user_id,$production_configuration=0)
	{
		$this->Site($db,6,$language_id,$auction_user_id,$production_configuration);
		$this->auction_user_id = $auction_user_id;
		$this->user_data = $this->get_user_data($db,$this->auction_user_id);
	} //end of function Auction_feedback

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_search_blacklisted_buyers_results($db,$search=0)
	{
		$this->page_id = 10183;
		$this->get_text($db);
		$this->function_name = "list_search_blacklisted_buyers_results";
		if ($this->auction_user_id)
		{
			if($search && $search["text_to_search"] != "0")
			{
				$this->sql_query = "select id,username, email, feedback_score from ".$this->userdata_table." where level = 0 and ";
				if ($this->debug_blacklist) echo $this->sql_query." TO START WITH<bR>";
				$this->select_query = "select user_id from ".$this->blacklist_table." where seller_id =".$this->auction_user_id." 
					and user_id != ".$this->auction_user_id;
				$select_result = $db->Execute($this->select_query);
				$this->sql_query .=" id NOT IN (".$this->auction_user_id;
				
				if ($this->debug_blacklist) echo $this->select_query."<bR>";
				if($select_result)
				{
					$records = 0;
					if ($select_result->RecordCount() > 0)
					{
						$records = $select_result->RecordCount();
						for($i = 0 ; $i  < $records-1; $i++)
						{
							$select_list = $select_result->FetchNextObject();
							$this->sql_query .= ",".$select_list->USER_ID;
						}
						$select_list = $select_result->FetchNextObject();
						$this->sql_query .= ",".$select_list->USER_ID;
					}
				}
				$this->sql_query .= ") and ";
				if ($this->debug_blacklist) echo $this->sql_query." PART 2<bR>";
				if($search["field_type"] == 3)
				{
					if(ereg('[^0-9]',$search["text_to_search"]))
					{
						echo "Only numbers are allowed <bR>";
						$this->site_error($db);
						return false;
					}
					else
					{
						$this->sql_query .= " feedback_score <= ".$search["text_to_search"]." order by feedback_score ";
					}
				}else if($search["field_type"] == 2)
					$this->sql_query .= " email LIKE \"%".$search["text_to_search"]."%\" order by feedback_score ";
				if($search["field_type"] == 1)
					$this->sql_query .= " username LIKE  \"%".$search["text_to_search"]."%\" order by feedback_score ";
				$blacklist_result = $db->Execute($this->sql_query);
				if ($this->debug_blacklist) echo $this->sql_query."<bR>";
				if($this->configuration_data['debug_user_management'])
				{
					$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "userdata_table", "get users from userdata_table");
				}
				if (!$blacklist_result)
				{
					if ($this->debug_blacklist) echo $this->sql_query."<bR>";
					$this->site_error($db);
					return false;
				}
				elseif ($blacklist_result->RecordCount() > 0)
				{
					$this->body .= "<form action=".$this->configuration_data['auctions_file_name']."?a=4&b=19&c=2 method=post>\n\t";
					$this->body .=  "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
					$this->body .= "<tr class=user_management_page_title>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[102830])."\n\t\t</td>\n\t</tr>\n\t";
					$this->body .= "<tr class=page_description>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[102788])."\n\t\t</td>\n\t</tr>\n\t";
					$this->body .= "<tr class=table_column_headers>\n\t\t<td>\n\t\t".urldecode($this->messages[102831])."\n\t\t</td>\n\t\t";//username
					$this->body .= "<td>\n\t\t".urldecode($this->messages[102832])."\n\t\t</td>\n\t\t";//email address
					$this->body .= "<td>\n\t\t".urldecode($this->messages[102833])."\n\t\t</td>\n\t\t";//feedback score
					$this->body .= "<td>\n\t\t".urldecode($this->messages[102834])."\n\t\t</td>\n\t\t";//check box
					$this->body .= "\n\t</tr>\n\t";
					$count = 0;
					while ($show_list = $blacklist_result->FetchNextObject())
					{
						//show the users to be black listed
						if (($count % 2) == 0)
							$css_tag = "blacklist_result_set_even_rows";
						else
							$css_tag = "blacklist_result_set_odd_rows";
						$this->body .= "<tr class=".$css_tag.">\n\t\t";
						$this->body .= "<td>\n\t\t".$show_list->USERNAME."\n\t\t</td>\n\t\t";
						$this->body .= "<td>\n\t\t".$show_list->EMAIL."\n\t\t</td>\n\t\t";
						$this->body .= "<td>\n\t\t".$show_list->FEEDBACK_SCORE."\n\t\t</td>\n\t\t";
						$this->body .= "<td><input type=checkbox name=d[user_id][".$count."] value=".$show_list->ID." >\n\t\t</td>\n\t\t";
						$count++;
					}
					$this->body .= "<tr class=save_changes_button>\n\t<td colspan=4>\n\t<input type=hidden  name=d[insertcount] value =".($count).">
					<input type=submit name=addUsers value=\"".urldecode($this->messages[102842])."\"></td>\n</tr>\n";
					//$this->body .="<tr class=user_management_home_link>\n\t<td colspan=4>\n\t<a href=".$this->configuration_data['auctions_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[102999])."</a>\n\t</td>\n</tr>\n";
					$this->body .= "</table></form>\n\t";
				}
				else
				{
					//there are no auction filters for this user
					$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
					$this->body .= "<tr class=user_management_page_title>\n\t\t<td>".urldecode($this->messages[102835])."\n\t\t</td>\n\t</tr>\n\t";
					//$this->body .="<tr class=user_management_home_link>\n\t<td colspan=4>\n\t<a href=".$this->configuration_data['auctions_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[102999])."</a>\n\t</td>\n</tr>\n";
					$this->body .= "</table>\n\t";
				}
			}
			$this->advanced_user_search($db);
			$this->body .= "<a class=user_management_home_link href=".$this->configuration_data['auctions_file_name']."?a=4>".urldecode($this->messages[102999])."</a>";
			$this->display_page($db);
			return true;
		}
		else
		{
			//there is nothing to search by
			//display the form again within error message
			$this->search_error_message = urldecode($this->messages[102983]);
			$this->advanced_user_search($db);
			$this->list_blacklisted_buyers($db);
			$this->body .= "<a class=user_management_home_link href=".$this->configuration_data['auctions_file_name']."?a=4>".urldecode($this->messages[102999])."</a>";
			$this->display_page($db);
			return true;	
		}
	} //end of function list_search_blacklisted_buyers_results

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_blacklisted_users($db,$users=0)
	{
		if ($this->debug_blacklist) 
		{
			echo $users." is the value of users within update_blacklisted_users<bR>";
			echo $users["insertcount"]." is insertcount<br>\n";
			echo $users["updatecount"]." is updatecount<br>\n";
		}
		if ($users)
		{
			if($users[insertcount] != 0)
			{
				for($i = 0; $i < $users[insertcount]; $i++)
				{
					if($users[user_id][$i])
					{
						$this->insert_query = "select * from ".$this->blacklist_table." where seller_id = ".$this->auction_user_id." and user_id = ".$users[user_id][$i];
						$check_result = $db->Execute($this->insert_query);
						if ($this->debug_blacklist) echo $this->sql_query."<bR>";
						if (!$check_result)
						{
							if ($this->debug_blacklist) echo $this->sql_query."<bR>";
							$this->site_error($db);
							return false;
						}						
						elseif ($check_result->RecordCount() == 0)
						{
							$this->insert_query = "insert into ".$this->blacklist_table." 
								(seller_id,user_id) 
								values 
								(".$this->auction_user_id.", ".$users[user_id][$i].")  ";
							$insert_result = $db->Execute($this->insert_query);
							if ($this->debug_blacklist) echo $this->sql_query."<bR>";
							if (!$insert_result)
							{
								if ($this->debug_blacklist) echo $this->sql_query."<bR>";
								$this->site_error($db);
								return false;
							}
						}
					}
				}
			}
			else if($users[updatecount] != 0)
			{
				for($i = 0; $i < $users[updatecount]; $i++)
				{
					if($users[user_id][$i])
					{
						$this->delete_query = "delete from ".$this->blacklist_table." where seller_id =".$this->auction_user_id." and user_id = ".$users[user_id][$i]."  ";
						$delete_result = $db->Execute($this->delete_query);
						if ($this->debug_blacklist) echo $this->sql_query."<bR>";
						if (!$delete_result)
						{
							if ($this->debug_blacklist) echo $this->sql_query."<bR>";
							$this->site_error($db);
							return false;
						}
					}
				}
			}
		}
	} //end of function update_blacklisted_users

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function advanced_user_search($db)
	{
		$this->page_id = 10183;
		$this->get_text($db);
		$this->body .= "<form action=".$this->configuration_data['auctions_file_name']."?a=4&b=19&c=1 method=post>\n\t";
		$this->body .= "<table cellpadding=2 cellspacing=0 border=0 align=center width=100%>\n";
		$this->body .= "<tr class=user_management_page_title>\n\t<td colspan=3>\n\t".urldecode($this->messages[102993])."\n\t</td>\n</tr>\n";
		$this->body .= "<tr class=page_description>\n\t<td colspan=3>\n\t".urldecode($this->messages[103057])."\n\t</td>\n</tr>\n";
		$this->body .= "<tr class=page_description>\n\t<td>\n\t<input type=radio name=d[field_type] value=1 checked>".urldecode($this->messages[102995])."</td>\n";
		$this->body .= "<td>\n\t<input type=radio name=d[field_type] value=2>".urldecode($this->messages[102996])."</td>\n";
		$this->body .= "<td>\n\t<input type=radio name=d[field_type] value=3>".urldecode($this->messages[102997])."</td>\n </tr>\n";
		$this->body .= "<tr class=search_buyers>\n\t<td colspan=3>\n\t<input type=text name=d[text_to_search] size=30 maxsize=30>";
		if (strlen($this->search_error_message) > 0)
			$this->body .= "<font class=error_message>".urldecode($this->search_error_message)."</font>";
		$this->body .= "<input type=submit name=search value=\"".urldecode($this->messages[102998])."\" class=save_changes_button></td>\n</tr>\n";
		$this->body .= "</table>\n\t</form>\n\t\n";
		
	} //end of function advanced_user_search
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_blacklisted_buyers($db,$users=0)
	{
		$feedback_score = 1;
		$this->page_id = 10183;
		$this->get_text($db);
		if ($this->auction_user_id)
		{
			$this->function_name = "list_blacklisted_buyers";
			$this->sql_query = "select user_id from ".$this->blacklist_table." where seller_id = ".$this->auction_user_id." ";
			$blacklist_result = $db->Execute($this->sql_query);
			if ($this->debug_blacklist) echo $this->sql_query."<bR>";
			if($this->configuration_data['debug_user_management'])
			{
				$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "userdata_table", "get users from userdata_table");
			}
			if (!$blacklist_result)
			{
				$this->site_error($db);
				return false;
			}
			elseif ($blacklist_result->RecordCount() > 0)
			{
				$this->body .= "<form action=".$this->configuration_data['auctions_file_name']."?a=4&b=19&c=2 method=post>\n\t";
				$this->body .=  "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
				$this->body .= "<tr class=user_management_page_title>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[102830])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "<tr class=page_description>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[103000])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "<tr class=table_column_headers>\n\t\t<td>\n\t\t".urldecode($this->messages[102831])."\n\t\t</td>\n\t\t";//username
				$this->body .= "<td>\n\t\t".urldecode($this->messages[102832])."\n\t\t</td>\n\t\t";//email address
				$this->body .= "<td>\n\t\t".urldecode($this->messages[102833])."\n\t\t</td>\n\t\t";//feedback score
				$this->body .= "<td>\n\t\t".urldecode($this->messages[102836])."\n\t\t</td>\n\t\t";//check box
				$this->body .= "\n\t</tr>\n\t";
				$count = 0;
				while ($show_list = $blacklist_result->FetchNextObject())
				{
					//show the users to be black listed
					if (($count % 2) == 0)
						$css_tag = "blacklist_result_set_even_rows";
					else
						$css_tag = "blacklist_result_set_odd_rows";

					$this->sql_query = "select id,username,email,feedback_score from ".$this->userdata_table." where id = ".$show_list->USER_ID." ";
					//echo $this->sql_query." is the query <bR>";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						$this->site_error($db);
						return false;
					}
					elseif ($result->RecordCount() == 1)
					{
						$show_user = $result->FetchNextObject();
						$this->body .= "<tr class=".$css_tag.">\n\t\t";
						$this->body .= "<td>\n\t\t".$show_user->USERNAME."\n\t\t</td>\n\t\t";
						$this->body .= "<td>\n\t\t".$show_user->EMAIL."\n\t\t</td>\n\t\t";
						$this->body .= "<td>\n\t\t".$show_user->FEEDBACK_SCORE."\n\t\t</td>\n\t\t";
						$this->body .= "<td><input type=checkbox name=d[user_id][".$count."] value=".$show_user->ID." >\n\t\t</td>\n\t\t";
						$count++;
					}
				}
				$this->body .= "<tr class=save_changes_button>\n\t<td colspan=4>\n\t<input type=hidden name=d[updatecount] value=".($count)."><input type=submit name=submit_value value=\"Remove Buyers \"></td>\n</tr>\n";
				$this->body .= "</table></form>\n\t";
			}
			else
			{
				//there are no auction filters for this user
				$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
				$this->body .= "<tr class=user_management_page_title>\n\t\t<td>".urldecode($this->messages[102830])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "<tr class=page_description>\n\t\t<td>".urldecode($this->messages[103000])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "<tr class=error_message>\n\t\t<td>".urldecode($this->messages[103001])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "</table>\n\t";
			}
			$this->advanced_user_search($db);
			
			// Display the link back to the user management page
			$this->body .= "<a class=user_management_home_link href=".$this->configuration_data['auctions_file_name']."?a=4>".urldecode($this->messages[102999])."</a>";
			$this->display_page($db);
			return true;
		}
		else
		{
			return false;
		}
	} //end of function list_blacklisted_buyers

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end of Auction_list_bids
?>