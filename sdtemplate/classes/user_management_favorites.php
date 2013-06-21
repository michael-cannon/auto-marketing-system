<? //user_management_favorites.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class User_management_favorites extends Site
{
	var $debug_favorites = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function User_management_favorites ($db,$language_id,$classified_user_id=0, $product_configuration=0)
	{
		$this->Site($db,6,$language_id,$classified_user_id, $product_configuration);

	} //end of function User_management

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_all_favorites($db)
	{
		$this->page_id = 30;
		$this->get_text($db);
		if ($this->classified_user_id)
		{
			$this->sql_query = "select * from ".$this->favorites_table." where user_id = ".$this->classified_user_id." order by date_inserted asc";
			if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
				$this->body .="<tr class=user_management_page_title ><td colspan=4 valign=top width=100%>\n\t\t".urldecode($this->messages[621])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=my_favorite_ads_title>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[352]);
				$this->body .=$this->display_help_link(354);
				$this->body .="\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=table_description>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[353])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=table_column_headers>\n\t\t<td>\n\t\t".urldecode($this->messages[358])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[357])."\n\t\t</td>\n\t\t";//ad started
				$this->body .="<td>\n\t\t".urldecode($this->messages[356])."\n\t\t</td>\n\t\t";//added to favorites
				$this->body .="<td>\n\t\t"."&nbsp;\n\t\t</td>\n\t</tr>\n\t";

				$this->row_count = 0;
				while ($show_list = $result->FetchNextObject())
				{
					$this->sql_query = "select title,date from ".$this->classifieds_table." where id = ".$show_list->CLASSIFIED_ID;
					$classified_result = $db->Execute($this->sql_query);
					if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
					if (!$classified_result)
					{
						if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
						$this->site_error($db->ErrorMsg());
						return false;
					}
					elseif ($classified_result->RecordCount() == 1)
					{
						$show_classified = $classified_result->FetchNextObject();
						$this->body .="<tr class=";
						if (($this->row_count % 2) == 0)
							$this->body .= "result_set_even_rows";
						else
							$this->body .= "result_set_odd_rows";
						$this->body .= ">\n\t\t<td>\n\t\t
							<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_list->CLASSIFIED_ID.">".
							urldecode($show_classified->TITLE)."</a>\n\t\t</td>\n\t\t";
						$this->body .="<td>\n\t\t".date("M j, Y - h:i - l",$show_classified->DATE)."\n\t\t</td>\n\t\t";
						$this->body .="<td>\n\t\t".date("M j, Y - h:i - l",$show_list->DATE_INSERTED)."\n\t\t</td>\n\t\t";
						$this->body .="<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=10&c=1&d=".$show_list->FAVORITE_ID.">".urldecode($this->messages[360])."\n\t\t</td>\n\t</tr>\n\t";
						$this->row_count++;
					}
				}
				$this->body .="<tr class=user_management_home_link>\n\t<td colspan=4>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4>".urldecode($this->messages[361])."</a>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
			}
			else
			{
				//there are no favorites for this user
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
				$this->body .="<tr class=user_management_page_title ><td colspan=4 valign=top width=100%>\n\t\t".urldecode($this->messages[621])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=my_favorite_ads_title>\n\t\t<td>\n\t\t".urldecode($this->messages[352]);
				$this->body .="<tr class=error_message >\n\t\t<td>".urldecode($this->messages[355])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=user_management_home_link>\n\t<td>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4>".urldecode($this->messages[361])."</a>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
			}
			$this->display_page($db);
			return true;

		}
		else
		{
			return false;
		}

	} //end of function display_all_favorites

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_favorite($db,$favorite_id)
	{
		if ($this->classified_user_id)
		{
			if ($favorite_id)
			{
				$this->sql_query = "delete from ".$this->favorites_table." where favorite_id = ".$favorite_id;
				$result = $db->Execute($this->sql_query);
				if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
				if (!$result)
				{
					if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
					return false;
				}
				return true;
			}
			else
			{
				//no communication id
				$this->error_message = $this->data_missing_error_message;
				return false;
			}
		}
		else
			return false;

	} //end of function delete_favorite

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_favorite($db,$favorite_id)
	{
		if ($this->classified_user_id)
		{
			if ($favorite_id)
			{
				$this->sql_query = "select * from ".$this->favorites_table."
					where classified_id = ".$favorite_id." and user_id = ".$this->classified_user_id;
				$result = $db->Execute($this->sql_query);
				if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
				if (!$result)
				{
					if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
					return false;
				}
				if ($result->RecordCount() == 0)
				{
					$this->sql_query = "insert into ".$this->favorites_table."
						(user_id,classified_id,date_inserted)
						values
						(".$this->classified_user_id.",".$favorite_id.",".$this->shifted_time($db).")";
					$result = $db->Execute($this->sql_query);
					if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
					if (!$result)
					{
						if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
						return false;
					}
				}
				return true;
			}
			else
			{
				//no favorite_id
				$this->error_message = $this->data_missing_error_message;
				return false;
			}
		}
		else
		{
			$this->error_message = urldecode($this->messages[359]);
			return false;
		}

	} //end of function insert_favorite

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function expire_old_favorites($db)
	{
		if ($this->classified_user_id)
		{
			$this->sql_query = "select * from ".$this->favorites_table."
				where user_id = ".$this->classified_user_id;
			$result = $db->Execute($this->sql_query);
			if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
			if (!$result)
			{
				if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
				return false;
			}
			if ($result->RecordCount() > 0)
			{
				while ($show = $result->FetchNextObject())
				{
					$this->sql_query = "select * from ".$this->classifieds_table."
						where id = ".$show->CLASSIFIED_ID;
					$classified_result = $db->Execute($this->sql_query);
					if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
					if (!$classified_result)
					{
						if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
						return false;
					}
					elseif ($classified_result->RecordCount() ==0)
					{
						//expire all favorites with this classified id
						$this->sql_query = "delete from ".$this->favorites_table."
							where classified_id = ".$show->CLASSIFIED_ID;
						$delete_result = $db->Execute($this->sql_query);
						if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
						if (!$delete_result)
						{
							if ($this->debug_favorites) echo $this->sql_query."<bR>\n";
							return false;
						}
					}
				}
				return true;
			}
		}
		else
		{
			$this->error_message = urldecode($this->messages[296]);
			return false;
		}

	} //end of function delete_favorite

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
}

?>