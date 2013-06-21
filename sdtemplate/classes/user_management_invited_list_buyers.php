<? //user_management_invited_list_buyers.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Invited_list_buyers extends Site {

	var $item_id;
	var $user_id;
	var $feedback_messages;
	var $user_data;
	var $search_error_message;

	// Debug variables
	var $filename = "user_management_invited_list_buyers.php";
	var $function_name;
	
	var $debug_invited = 0;
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Invited_list_buyers($db,$language_id,$user_id,$product_configuration=0)
	{
		$this->Site($db,6,$language_id,$user_id,$product_configuration);
		$this->user_id = $user_id;
		$this->user_data = $this->get_user_data($db,$this->user_id);
	} //end of function Invited_list_buyers

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_search_invited_buyers_results($db,$search=0)
	{
		$this->page_id = 10184;
		$this->get_text($db);	
		$this->function_name = "list_search_invited_buyers_results";
		if ($this->user_id)
		{
			if($search && $search["text_to_search"] != "0")
			{
				//display the results from the search
				$this->sql_query = "select id,username, email, feedback_score from ".$this->userdata_table." where level = 0 and ";
				$this->sql_query .=	"id != ".$this->user_id." and ";
				$this->select_query = "select user_id from ".$this->invitedlist_table." where seller_id =".$this->user_id." ";
				$select_result = $db->Execute($this->select_query);
				if ($this->debug_invited)
					echo $this->sql_query."<bR>\n";
				if ($this->debug_invited)
					echo $this->select_query."<bR>\n";
				if($select_result)
				{
					$records = 0;
					if ($select_result->RecordCount() > 0)
					{
						$records = $select_result->RecordCount();
						$this->sql_query .=" id NOT IN (".$this->user_id." , ";
						for($i = 0 ; $i  < $records-1; $i++)
						{
							$select_list = $select_result->FetchNextObject();
							$this->sql_query .= $select_list->USER_ID.", ";
						}
						$select_list = $select_result->FetchNextObject();
						$this->sql_query .=$select_list->USER_ID.") and ";
					}
				}
				if($search["field_type"] == 3)
				{
					if(!settype($search["text_to_search"], "integer"))
					{
						echo "Only numbers are allowed <bR>";
						$this->site_error($db);
						return false;
					}
					else
					{
						$this->sql_query .= " feedback_score >= ".$search["text_to_search"]." order by feedback_score ";
					}
				}
				else if($search["field_type"] == 2)
					$this->sql_query .= " email LIKE \"%".$search["text_to_search"]."%\" order by feedback_score ";
				if($search["field_type"] == 1)
					$this->sql_query .= " username LIKE  \"%".$search["text_to_search"]."%\" order by feedback_score ";

				$invitedlist_result = $db->Execute($this->sql_query);
				if ($this->debug_invited)
					echo $this->sql_query."<bR>\n";
				if (!$invitedlist_result)
				{
					//if ($this->debug_invited)
						echo $this->sql_query."<bR>\n";
					$this->site_error($db);
					return false;
				}
				elseif ($invitedlist_result->RecordCount() > 0)
				{
					$this->body .= "<form action=".$this->configuration_data['classifieds_file_name']."?a=4&b=20&c=2 method=post>\n\t";
					$this->body .=  "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
					$this->body .= "<tr class=user_management_page_title>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[102982])."\n\t\t</td>\n\t</tr>\n\t";
					$this->body .= "<tr class=page_description>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[102990])."\n\t\t</td>\n\t</tr>\n\t";
					$this->body .= "<tr class=table_column_headers>\n\t\t<td>\n\t\t".urldecode($this->messages[102845])."\n\t\t</td>\n\t\t";//username
					//email address
					$this->body .= "<td>\n\t\t".urldecode($this->messages[102846])."\n\t\t</td>\n\t\t";
					//feedback score
					$this->body .= "<td>\n\t\t".urldecode($this->messages[102847])."\n\t\t</td>\n\t\t";
					//check box
					$this->body .= "<td>\n\t\t".urldecode($this->messages[102848])."\n\t\t</td>\n\t\t";
					$this->body .= "\n\t</tr>\n\t";
					$count = 0;
					while ($show_list = $invitedlist_result->FetchNextObject())
					{
						//show the users to be invited listed
						$css_tag = $this->get_row_color(2);
						if (($count % 2) == 0)
							$css_tag = "invited_result_set_even_rows";
						else
							$css_tag = "invited_result_set_odd_rows";
						$this->body .= "<tr class=".$css_tag.">\n\t\t";
						$this->body .= "<td>\n\t\t".$show_list->USERNAME."\n\t\t</td>\n\t\t";
						$this->body .= "<td>\n\t\t".$show_list->EMAIL."\n\t\t</td>\n\t\t";
						$this->body .= "<td>\n\t\t".$show_list->FEEDBACK_SCORE."\n\t\t</td>\n\t\t";
						$this->body .= "<td><input type=checkbox name=d[user_id][".$count."] value=".$show_list->ID." >\n\t\t</td>\n\t\t";
						$count++;
					}
					$this->body .= "<tr class=save_changes_button>\n\t<td colspan=4>\n\t<input type=hidden  name=d[insertcount] value =".($count).">
						<input type=submit name=addUsers value=\"".urldecode($this->messages[102991])."\"></td>\n</tr>\n";
					//$this->body .="<tr class=user_management_home_link>\n\t<td colspan=4>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[102979])."</a>\n\t</td>\n</tr>\n";
					$this->body .= "</table></form>\n\t";
				}
				else
				{
					//there are no invited buyers for this user
					$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
					$this->body .= "<tr class=user_management_page_title>\n\t\t<td>".urldecode($this->messages[102849])."\n\t\t</td>\n\t</tr>\n\t";
					//$this->body .="<tr class=user_management_home_link>\n\t<td>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[102979])."</a>\n\t</td>\n</tr>\n";
					$this->body .= "</table>\n\t";
				}
				$this->advanced_user_search($db);
				$this->body .= "<a href=\"".$this->configuration_data['classifieds_file_name']."?a=4\" class=user_management_home_link>".urldecode($this->messages[102979])."</a>";
				$this->display_page($db);
				return true;	
			}
			else
			{
				//no user id
				return false;
			}
		}
		else
		{
			//there is nothing to search by
			//display the form again within error message
			$this->search_error_message = urldecode($this->messages[102983]);
			$this->advanced_user_search($db);
			$this->list_invited_buyers($db);
			$this->display_page($db);
			return true;			
		}
	
	} //end of function list_search_invited_buyers_results
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%	
	
	function update_invited_users($db,$users=0)
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
				for($i = 0; $i <= $users[insertcount]; $i++)
				{
					if($users[user_id][$i])
					{
						$this->sql_query = "select * from ".$this->invitedlist_table." where seller_id = ".$this->user_id." and user_id = ".$users[user_id][$i];
						$check_result = $db->Execute($this->sql_query);
						if ($this->debug_invited) echo $this->sql_query."<bR>\n";
						if (!$check_result)
						{
							if ($this->debug_invited) echo $this->sql_query."<bR>\n";
							$this->site_error($db);
							return false;
						}						
						elseif ($check_result->RecordCount() == 0)
						{
							$this->sql_query = "insert into ".$this->invitedlist_table." 
								(seller_id,user_id)
								values 
								(".$this->user_id.", ".$users['user_id'][$i].")  ";
							$insert_result = $db->Execute($this->sql_query);
							if ($this->debug_invited) echo $this->sql_query."<bR>\n";
							if (!$insert_result)
							{
								if ($this->debug_invited) echo $this->sql_query."<bR>\n";
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
						$this->delete_query = "delete from ".$this->invitedlist_table." where seller_id =".$this->user_id." and user_id = ".$users[user_id][$i]."  ";
						$delete_result = $db->Execute($this->delete_query);
						if ($this->debug_invited) echo $this->sql_query."<bR>\n";
						if (!$delete_result)
						{
							if ($this->debug_invited) echo $this->sql_query."<bR>\n";
							$this->site_error($db);
							return false;
						}
					}
				}
			}
		}
	} //end of function update_invited_users

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_buyers($db,$search=0)
	{
		if ($this->user_id)
		{
			$this->page_id = 1010184;
			$this->get_text($db);
			$this->advanced_user_search($db);
			$this->function_name = "list_buyers";
			if(!$search["text_to_search"] && $search["text_to_search"] != "0")
			{
				$this->list_invited_buyers($db);
				$this->advanced_user_search($db);
			}
		}
		else
		{
			return false;
		}
	} //end of function list_buyers

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function advanced_user_search($db)
	{
		$this->page_id = 10184;
		$this->get_text($db);
		$this->body .= "<form action=".$this->configuration_data['classifieds_file_name']."?a=4&b=20&c=1 method=post>\n\t";
		$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
		$this->body .= "<tr class=user_management_page_title>\n\t<td colspan=3>\n\t<b>".urldecode($this->messages[102984])."</b></font>\n\t</td>\n</tr>\n";
		$this->body .= "<tr class=page_description>\n\t<td colspan=3>\n\t".urldecode($this->messages[102985])."\n\t</td>\n</tr>\n";
		$this->body .= "<tr class=page_description>\n\t<td>\n\t<input type=radio name=d[field_type] value=1 checked>".urldecode($this->messages[102986])."</td>\n";
		$this->body .= "<td>\n\t<input type=radio name=d[field_type] value=2>".urldecode($this->messages[102987])."</td>\n";
		$this->body .= "<td>\n\t<input type=radio name=d[field_type] value=3>".urldecode($this->messages[102988])."</td>\n </tr>\n";
		$this->body .= "<tr class=search_buyers>\n\t<td colspan=3>\n\t<input type=text name=d[text_to_search] size=30 maxsize=30>";
		if (strlen($this->search_error_message) > 0)
			$this->body .= "<font class=error_message>".urldecode($this->search_error_message)."</font>";		
		$this->body .= "<input type=submit name=search value=\"".urldecode($this->messages[102989])."\" class=save_changes_button></td>\n</tr>\n";
		$this->body .= "</table>\n\t</form>\n\t\n";
	} //end of function advanced_user_search
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_invited_buyers($db)
	{
		$feedback_score = 1;
		$this->page_id = 10184;
		$this->get_text($db);
		if ($this->user_id)
		{
			$this->function_name = "list_invitedlisted_buyers";
			$this->sql_query = "select user_id from ".$this->invitedlist_table." where seller_id = ".$this->user_id." ";
			$invitedlist_result = $db->Execute($this->sql_query);
			if ($this->debug_invited)
				echo $this->sql_query."<bR>\n";
			if($this->configuration_data['debug_user_management'])
			{
				$this->debug_display($this->sql_query, $db, $this->filename, $this->function_name, "userdata_table", "get users from userdata_table");
			}
			if (!$invitedlist_result)
			{
			if ($this->debug_invited) echo $this->sql_query."<bR>\n";
				$this->site_error($db);
				return false;
			}
			elseif ($invitedlist_result->RecordCount() > 0)
			{
				$this->body .= "<form action=".$this->configuration_data['classifieds_file_name']."?a=4&b=20&c=2 method=post>\n\t";
				$this->body .=  "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
				$this->body .= "<tr class=user_management_page_title>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[102844])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "<tr class=page_description>\n\t\t<td colspan=4>".urldecode($this->messages[102980])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "<tr class=table_column_headers>\n\t\t<td>\n\t\t".urldecode($this->messages[102845])."\n\t\t</td>\n\t\t";//username
				$this->body .= "<td>\n\t\t".urldecode($this->messages[102846])."\n\t\t</td>\n\t\t";//email address
				$this->body .= "<td>\n\t\t".urldecode($this->messages[102847])."\n\t\t</td>\n\t\t";//feedback score
				$this->body .= "<td>\n\t\t".urldecode($this->messages[102848])."\n\t\t</td>\n\t\t";//check box
				$this->body .= "\n\t</tr>\n\t";
				$count = 0;
				while ($show_list = $invitedlist_result->FetchNextObject())
				{
					//show the users to be invited listed
					if (($count % 2) == 0)
						$css_tag = "invited_result_set_even_rows";
					else
						$css_tag = "invited_result_set_odd_rows";

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
				$this->body .= "<tr class=save_changes_button>\n\t<td colspan=4>\n\t<input type=hidden name=d[updatecount] value=".$count--."><input type=submit name=submit_value value=\"".urldecode($this->messages[102992])."\"></td>\n</tr>\n";
				$this->body .= "</table></form>\n\t";
			}
			else
			{
				//there are no invited buyers for this seller
				$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
				$this->body .= "<tr class=user_management_page_title>\n\t\t<td>".urldecode($this->messages[102844])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "<tr class=page_description>\n\t\t<td>".urldecode($this->messages[102980])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "<tr class=error_message>\n\t\t<td>".urldecode($this->messages[102981])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "</table></form>\n\t";
			}
			$this->advanced_user_search($db);
			$this->body .= "<a class=user_management_home_link href=".$this->configuration_data['classifieds_file_name']."?a=4>".urldecode($this->messages[102979])."</a>";
			$this->display_page($db);
			return true;
		}
		else
		{
			return false;
		}
	} //end of function list_invited_buyers

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end of Invited_list_buyers
?>