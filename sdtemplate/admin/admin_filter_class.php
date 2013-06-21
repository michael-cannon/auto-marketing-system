<?//admin_filter_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Admin_filter extends Admin_site{

	var $current_filter;
	var $filter_level_array = array();
	var $in_statement;
	var $subfilter_array = array();

	var $messages = array();

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Admin_filter ($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);

		$this->messages[3500] = "Not enough information to display the filter data";
		$this->messages[3501] = "Internal browse error!";
		$this->messages[3502] = "no filter id";
		$this->messages[3503] = "cannot update the top filter";
		$this->messages[3504] = "current filter";
		$this->messages[3505] = "Subfilters of ";
		$this->messages[3506] = "There are no subfilters";
		$this->messages[3507] = "subfilters exist";
		$this->messages[3508] = "<img src=admin_images/btn_admin_delete.gif alt=delete border=0>";
		$this->messages[3509] = "cannot delete the main filter";
		$this->messages[3510] = "There was an error processing your request";
	} //end of function Admin_filter

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_filter_form ($db,$filter=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"filter order\", \"Filter order determines the order of the filter names when this filter's parent is displayed.\"]\n";

		//".$this->show_tooltip(1,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		if ($filter)
		{
			//edit this filter after getting current info
			$this->sql_query = "select * from ".$this->classified_filters_table." where filter_id = ".$filter;
			//echo $this->sql_query."<bR>\n";
			$result = $db->Execute($this->sql_query);
			 if (!$result)
			 {
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3501];
				return false;
			 }
			 elseif ($result->RecordCount() == 1)
			 {
			 	$show_filter = $result->FetchRow();
			 }
			 else
			 {
			 	return false;
			 }

			//add
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=68&i=insert&j=".$filter." method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% class=row_color1>\n";
			$this->title = "Site Setup > Filters";
			$this->description = "Insert a subfilter to the <b>".$show_filter["filter_name"]."</b> filter";

			$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font>\n\tfilter order:".$this->show_tooltip(1,1)."</td>\n\t";
			$this->body .= "<td>\n\t<select name=g[display_order]>\n\t\t";
			for ($i=1;$i<500;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_filter["display_order"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";

			$this->sql_query = "select distinct(language_id) from ".$this->pages_languages_table;
			$language_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$language_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3501];
				return false;
			 }
			 elseif ($language_result->RecordCount() > 0)
			 {
			  	while ($show = $language_result->FetchRow())
			 	{
			 		$this->body .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light>\n\tfilter name and description for: <b>".$this->get_language_name($db,$show["language_id"])."</b> language \n\t</td>\n\t";
					$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font>\n\tfilter name: \n\t</td>\n\t";
					$this->body .= "<td valign=top>\n\t<input type=text name=g[".$show["language_id"]."][filter_name] value=\"".$show_filter["filter_name"]."\">\n\t</td>\n</tr>\n";
				}
			}
			if (!$this->admin_demo())
				$this->body .= "<tr>\n\t<td align=center class=medium_font colspan=2><input type=submit value=\"Save\"></td>\n</tr>\n";
			$this->body .= "</table>\n</form>\n";
		}
		else
		{
			//this is the main filter
			//you can only add a filter to the main filter
			//there is no edit of the main filter
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=68&i=insert&j=0 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=100% class=row_color1>\n";
			$this->title .= "Site Setup > Filters";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=medium_font_light>
				Insert a new filter at level 1.  Choose the name for each language and the display order in which it will appear with other filters
				at this level. \n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font>\n\tfilter order:".$this->show_tooltip(1,1)."</td>\n\t";
			$this->body .= "<td>\n\t<select name=g[display_order]>\n\t\t";
			for ($i=1;$i<99;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_filter["display_order"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";

			$this->sql_query = "select distinct(language_id) from ".$this->pages_languages_table;
			$language_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			 if (!$language_result)
			 {
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3501];
				return false;
			 }
			 elseif ($language_result->RecordCount() > 0)
			 {
			 	while ($show = $language_result->FetchRow())
			 	{
			 		$this->body .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light>\n\tfilter name for: <b>".$this->get_language_name($db,$show["language_id"])."</b> language \n\t</td>\n\t";
					$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font>\n\tfilter name: \n\t</td>\n\t";
					$this->body .= "<td valign=top>\n\t<input type=text name=g[".$show["language_id"]."][filter_name] value=\"".$show_filter["filter_name"]."\">\n\t</td>\n</tr>\n";
				}
			}
			if (!$this->admin_demo())
				$this->body .= "<tr>\n\t<td align=center class=medium_font colspan=2><input name=Save type=submit value=\"Save\"></td>\n</tr>\n";
			$this->body .= "</table>\n</form>\n";
		}
		return true;

	} //end of function display_filter_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function edit_filter_form($db,$filter_id=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"filter order\", \"Filter order determines the order of the filter names when this filter's parent is displayed.\"]\n";

		//".$this->show_tooltip(1,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		if ($filter_id)
		{
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=68&b=".$filter_id."&e=edit method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% class=row_color1>\n";
			$this->title = "Site Setup > Filters > Edit";
			$this->description = "Edit this filters name, description, display order within this form.";

			$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font>\n\tfilter order:".$this->show_tooltip(1,1)."</td>\n\t";
			$this->body .= "<td valign=top>\n\t<select name=f[display_order]>\n\t\t";
			$this->sql_query = "select * from ".$this->classified_filters_table." where filter_id = ".$filter_id;
			//echo $this->sql_query."<bR>\n";
			$result = $db->Execute($this->sql_query);
			 if (!$result)
			 {
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3501];
				return false;
			 }
			 elseif ($result->RecordCount() == 1)
			 {
			 	$show_filter = $result->FetchRow();
			 }
			 else
			 {
			 	return false;
			 }
			for ($i=1;$i<500;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_filter["display_order"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";

			$this->sql_query = "select * from ".$this->classified_filters_languages_table." where filter_id = ".$filter_id;
			$language_result = $db->Execute($this->sql_query);
			 if (!$language_result)
			 {
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3501];
				return false;
			 }
			 elseif ($language_result->RecordCount() > 0)
			 {
			 	while ($show = $language_result->FetchRow())
			 	{
			 		$this->body .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light>\n\tfilter name and description for: <b>".$this->get_language_name($db,$show["language_id"])."</b> language \n\t</td>\n\t";
					$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font>\n\tfilter name: \n\t</td>\n\t";
					$this->body .= "<td valign=top>\n\t<input type=text name=g[".$show["language_id"]."][filter_name] value=\"".$show["filter_name"]."\">\n\t</td>\n</tr>\n";
				}
			}
			if (!$this->admin_demo())
				$this->body .= "<tr>\n\t<td align=center class=medium_font colspan=2><input type=submit value=\"Save\"></td>\n</tr>\n";

			$this->body .= "</table>\n</form>\n";
			return true;
		}
		else
			return false;
	} //end of function edit_filter_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_sql_in_statement($db,$filter_id)
	{
		if ($filter_id)
		{
			$this->subfilter_array = array();
			//echo "empty subfilter_array for ".$filter_id."<BR>\n";
			$this->get_sql_in_array($db,$filter_id);
			//echo count($this->subfilter_array)." is subfilter array count<br>\n";
			if (count($this->subfilter_array) > 0)
			{
				$this->in_statement = "";
				$this->in_statement .= "in (";
				while (list($key,$value) = each($this->subfilter_array))
				{
					if ($key == 0)
						$this->in_statement .= $value;
					else
						$this->in_statement .= ",".$value;
				}
				$this->in_statement .= ")";
				return $this->in_statement;
			}
			else
			{
				return false;
			}
		}
		else
		{
			//filter_id is missing
			return false;
		}

	} //end of get_sql_in_statement

//####################################################################################

	function get_sql_in_array($db,$filter_id)
	{
		if ($filter_id)
		{
			//get the count for this filter
			$count = 0;

			//$this->subfilter_array = array();
			$this->sql_query = "select filter_id from ".$this->classified_filters_table." where parent_id = ".$filter_id;
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[2524];
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				while ($show_filter = $result->FetchRow())
				{
					//echo "trying to get sql in for ".$show_filter["filter_id"]."<BR>\n";
					$this->get_sql_in_array($db,$show_filter["filter_id"]);
				}
			}
			//echo "pushing - ".$filter_id."<br>";
			array_push ($this->subfilter_array, $filter_id);

			return true;
		}
		else
		{
			//filter_id is missing
			return false;
		}

	} //end of get_sql_in_array

//##################################################################################

	function insert_filter($db,$info,$parent_filter=0)
	{
		$this->sql_query = "select * from ".$this->pages_languages_table;
		$language_result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$language_result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[3500];
			return false;
		}
		elseif ($language_result->RecordCount() > 0)
		{
			//get sql in statement for this filter

			//reset sql in statement for categories above

			$this->sql_query = "insert into ".$this->classified_filters_table."
				(parent_id,filter_name,display_order)
				values
				(".$parent_filter.",\"".$info[1]["filter_name"]."\",".$info["display_order"].")";
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query to insert the base<br>\n";
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
			$filter_id = $db->Insert_ID();

			$filter_in_statement = "";
			$filter_in_statement = $this->get_sql_in_statement($db,$filter_id);

			$filter_level = $this->get_filter_level($db,$filter_id);

			$this->sql_query = "update ".$this->classified_filters_table." set
				filter_level = ".$filter_level.",
				in_statement = \"".$filter_in_statement."\" where filter_id = ".$filter_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is updating the base in_statement<br>\n";
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}

			while ($show = $language_result->FetchRow())
			{
				$this->sql_query = "insert into ".$this->classified_filters_languages_table."
					(filter_id,filter_name,language_id)
					values
					(".$filter_id.",\"".$info[$show["language_id"]]["filter_name"]."\",".$show["language_id"].")";
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is inserting the languages for this filter<br>\n";
				if (!$result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[3500];
					return false;
				}
				//echo $show["language_id"]." is the language_id at end<br>\n";
			}

			if ($parent_filter)
			{
				//check if parent is main filter
				$current_parent_filter = $parent_filter;
				while ($current_parent_filter != 0)
				{
					$parent_in_statement = "";
					$parent_in_statement = $this->get_sql_in_statement($db,$current_parent_filter);

					$this->sql_query = "update ".$this->classified_filters_table." set
						in_statement = \"".$parent_in_statement."\" where filter_id = ".$current_parent_filter;
					//echo $this->sql_query." is updating the parent in statement<br>\n";
					$result = $db->Execute($this->sql_query);
					//$this->body .=$this->sql_query." is the query<br>\n";
					if (!$result)
					{
						return false;
					}

					$this->sql_query = "SELECT parent_id FROM ".$this->classified_filters_table." WHERE filter_id = ".$current_parent_filter;
					//echo $this->sql_query."<br>\n";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						return false;
					}
					elseif ($result->RecordCount() == 1)
					{
						$show_filter = $result->FetchRow();
						$current_parent_filter = $show_filter["parent_id"];
					}
					else
					{
						//$this->body .=$this->sql_query." is the query where count is not 1<br>\n";
						return false;
					}

				} //end of while
			}
		}

		return $filter_id;

	} // end of function insert_filter

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_filter_check($db,$filter=0)
	{
		if ($filter)
		{
			$this->sql_query = "select * from ".$this->classified_filters_table." where filter_id = ".$filter;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
				if ($show["parent_id"])
				{
					$this->sql_query = "select * from ".$this->classified_filters_table." where filter_id = ".$show["parent_id"];
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						$this->error_message = $this->messages[3500];
						return false;
					}
					elseif ($result->RecordCount() == 1)
					{
						$show_parent = $result->FetchRow();

						$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100% class=row_color1>\n";
						$this->title .= "Site Setup > Filters > Delete";
						$this->description .= "Verify deletion of this filter below.";
						$this->body .= "<tr>\n\t<td align=center><a href=index.php?a=68&x=".$filter."&y=delete>
							<span class=medium_font><br><br>Delete the <b>".$show["filter_name"]."</b> filter entry.</span></a>\n\t</td>\n</tr>\n";
						$this->body .= "</table>\n";
					}
					else
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[3500];
						return false;
					}
				}
				else
				{
					//delete a main filter
					$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100% class=row_color1>\n";
						$this->title .= "Site Setup > Filters > Delete";
						$this->description .= "Verify deletion of a filter entry. Are you sure you want to
						delete the <b>".$show["filter_name"]."</b> filter entry?  If so click the link below.";
					$this->body .= "<tr>\n\t<td align=center><a href=index.php?a=68&x=".$filter."&y=delete><span class=medium_font>
						Delete the <b>".$show["filter_name"]."</b> filter along with its subfilter entries.</span></a>\n\t</td>\n</tr>\n";
					$this->body .= "</table>\n";
				}
				return true;
			}
			else
			{
				$this->error_message = $this->messages[3500];
				return false;
			}
		}
		else
		{
			$this->error_message = $this->messages[3509];
			return false;
		}
	} // end of function delete_filter_check

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_filter($db,$filter=0,$delete_verify=0)
	{
		//echo "hello from delete filter<br>\n";
		if (($filter) && ($delete_verify == "delete"))
		{
			$this->sql_query = "delete from ".$this->classified_filters_languages_table."
				where filter_id = ".$filter;
			$delete_filter_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$delete_filter_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}

			$this->sql_query = "delete from ".$this->classified_filters_table."
				where filter_id = ".$filter;
			$delete_filter_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$delete_filter_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
		}
		else
		{
			$this->error_message = $this->messages[3500];
			return false;
		}
		return true;
	} // end of function delete_filter

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_filter($db,$filter=0,$info=0,$info2=0)
	{
		if (($filter) && ($info) && ($info2))
		{
			$this->sql_query = "update ".$this->classified_filters_table." set
				display_order = ".$info["display_order"].",
				filter_name = \"".$info2[1]["filter_name"]."\"
				where filter_id = ".$filter;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
			reset ($info2);
			foreach ($info2 as $key => $value)
			{
				$this->sql_query = "update ".$this->classified_filters_languages_table." set
					filter_name = \"".$info2[$key]["filter_name"]."\"
					where filter_id = ".$filter." and language_id = ".$key;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is the query<br>\n";
				if (!$result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[3500];
					return false;
				}
			}

			return true;
		}
		else
		{
			$this->error_message = $this->messages[3503];
			return false;
		}
	} // end of function update_filter

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function home ()
	{
		$this->body .= "<table align=center width=100%>\n";
		$this->body .= "<tr>\n\t<td><a href=index.php?a=68>browse and edit categories</a>\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
	} //end of function home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_current_filter($db,$filter=0)
	{
		//echo $filter." is filter in the top of display_current_filter<br>\n";
		$this->body .= "<table cellpadding=3 cellspacing=0 border=1 width=100%>\n<tr>\n\t<td colspan=5>\n\t";
		$this->sql_query = "select * from ".$this->classified_filters_table." where filter_id = ".$filter;
		$result = $db->Execute($this->sql_query);
		 if (!$result)
		 {
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[3501];
			return false;
		 }
		 elseif ($result->RecordCount() == 1)
		 {
			$show_this_filter = $result->FetchRow();

			 $this->sql_query = "select * from ".$this->classified_filters_table." where parent_id = ".$filter." order by display_order asc";
			 $subfilter_result = $db->Execute($this->sql_query);
			// echo $this->sql_query." is the query<br>\n";
			 if (!$subfilter_result)
			 {
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			 }

			 if ($filter == 0)
			 {
				//parent is main
				//echo "categories under the main filter";
				$this->body .= "main categories";
			 }
			 else
			 {
				$this->body .= "categories under the ".$show_this_filter["filter_name"]." filter --- #".$show_this_filter["filter_id"];
			 }
			$this->body .= "</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td>filter (filter id#)<br>click to enter</td>\n\t<td>description</td>\n\t<td>display order</td>\n\t<td>click to edit</td>\n\t<td>click to delete</td>\n</tr>\n";
			while ($show = $subfilter_result->FetchRow())
			{
				//show each subfilter
				$this->body .= "<tr>\n\t<td><a href=index.php?a=3&b=".$show["filter_id"].">".$show["filter_name"]." (".$show["filter_id"].")</a></td>\n\t";
				$this->body .= "<td>".$show["description"]."&nbsp;</td>\n\t";
				$this->body .= "<td>".$show["display_order"]."</td>\n\t";
				$this->body .= "<td><a href=index.php?a=68&b=".$show["filter_id"]."><img src=admin_images/btn_admin_edit.gif alt=edit border=0></a></td>\n\t";
				$this->body .= "<td><a href=index.php?a=4&b=".$show["filter_id"]."><img src=admin_images/btn_admin_delete.gif alt=delete border=0></a></td>\n";
				$this->body .= "</tr>\n";
			}
				$this->body .= "</table>\n";
				$this->body .= "<br><br><a href=index.php?a=1&b=".$filter.">add a sub filter to this filter</a>";
				$this->body .= "<br><br><a href=index.php?a=3&b=0>back to main filter</a>";
		}
		else
		{
			//filter does not exist
			$this->error_message = $this->messages[3500];
			return false;
		}
		return true;
	} //end of function display_current_filter

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function browse($db,$filter=0)
	{
		$this->sql_query = "select * from ".$this->site_configuration_table;
		$configuration_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$configuration_result)
		{
			$this->error_message = $this->messages[5501];
			return false;
		}
		elseif ($configuration_result->RecordCount() == 1)
		{
			$filter_config = $configuration_result->FetchRow();
		}
		else
			return false;
		$this->body .= "<table cellpadding=2 cellspacing=0 border=0 width=100% class=row_color1>\n";
		$this->title = "Site Setup > Filters Home";
		$this->description = "Filters are optional browsing tools that you may implement on your site to give your visitors more control over
		each page's listing results. Filters essentially \"filter out\" or \"screen\" lising results tables so that the only results displayed
		in the table meet the user's search filter criteria. Essentially, filters allow users to screen listing results based upon filters
		you set up that correlate with information gathered from the \"seller\" during the registration and \"listing\" process.
		<Br><Br>The filters are built in a hierarchy just like the categories
		the listings are placed in.  Each level of the hierarchy will be placed in its own dropdown (level) filter.  Once a
		certain filter is chosen only filters that are in the hierarchy below it will be displayed in the dropdowns (levels) below it.
		The direct parents of the chosen filter will appear in the dropdowns (levels) above the chosen filter.<br><br>
		After you have set up your filters, you can display them on your site by inserting one of the filter module tags into your templates:
		&lt;&lt;FILTER_DISPLAY_1&gt;&gt; or &lt;&lt;FILTER_DISPLAY_2&gt;&gt;. <br><br>
		Attach the filter \"levels\" to the registration and site wide fields you wish to attach them to.  You can attach them to any of the ten
		optional registration fields and ten site wide optional fields.  Every new registrant will be asked to choose their selections
		from each level.  Once done the fields they are connected to within the optional registration and optional site wide fields
		will be automatically filled with the values they chose.<br><br>
		The filter will not work for existing users unless they \"edit\" those \"associated\" fields within their personal information
		display page.  This resets those same fields automatically for the current live listings for the same user.<br><br>
		When browsing the \"filter dropdowns\" should appear where you place them in the dropdown/level you created them.
		One level per dropdown.  Once a filter is chosen the \"parent filters\" in each level is automatically selected for the levels
		above the selected filter.  The \"children\" of the current filter will populate the dropdown/levels below the selected filter.";
		$this->body .= "<tr class=row_color2>\n\t<td colspan=4 class=medium_font align=center>";
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=68 method=post ><br>";
		$this->body .= "<input type=radio name=\"m\" value=\"1\" ";
		if ($filter_config["use_filters"] == 1) $this->body .= "checked";
		if (!$this->admin_demo()) $this->body .= " onClick=\"if(this.value != '') this.form.submit();\"";
		$this->body .= ">filters are on <br>";
		$this->body .= "<input type=radio name=\"m\" value=\"2\" ";
		if ($filter_config["use_filters"] == 0) $this->body .= "checked";
		if (!$this->admin_demo()) $this->body .= " onClick=\"if(this.value != '') this.form.submit();\"";
		$this->body .= ">filters are off <br>";
		$this->body .= "</form> \n\t</td>\n</tr>\n";
		//browse the listings in this filter that are open

		if ($filter)
		{
			$this->sql_query = "select * from ".$this->classified_filters_table." where filter_id = ".$filter;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
				$parent_id = $show["parent_id"];
				$filter_name = $show["filter_name"];
			}
			else
			{
				//filter does not exist
				$this->error_message = $this->messages["5500"];
				return false;
			}

			//get the current level

			$this->sql_query = "select * from ".$this->classified_filters_table." where parent_id = ".$filter." order by display_order";
		}
		else
		{
			$parent_id = 0;
			$filter_name = "none";
			$this->sql_query = "select * from ".$this->classified_filters_table." where parent_id = 0 order by display_order";
		}



		$result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}
		else
		{
			if ($filter)
			{
				$filter_tree = $this->get_filter_level($db,$filter);
				$current_level = count($this->filter_level_array);
				//echo $current_level." is current level<bR>\n";
				reset ($this->filter_level_array);
				if ($filter_tree)
				{

					//filter tree
					$this->body .= "<tr bgcolor=000066>\n\t<td colspan=5 class=medium_font align=center>\n\t<b>";
					$this->body .= "</b> <a href=index.php?a=68><span class=medium_font_light><b>show all Level 1</b></span></a> : ";
					if (is_array($this->filter_level_array))
					{
						$i = 0;
						//$categories = array_reverse($this->filter_tree_array);
						$i = count($this->filter_level_array);
						$j = 1;
						while ($i > 0 )
						{
							//display all the filters
							$i--;
							if ($i == 0)
								$this->body .= "<span class=medium_font_light><b>".$this->filter_level_array[$i]["filter_name"]." - (level ".$j.")</b></span>";
							else
								$this->body .= "<a href=index.php?a=68&b=".$this->filter_level_array[$i]["filter_id"]."><span class=medium_font_light>".$this->filter_level_array[$i]["filter_name"]." - (level ".$j.")</span></a> > ";
							$j++;
						}
					}
					else
					{
						$this->body .= $filter_tree;
					}
					$this->body .= "\n\t</td>\n</tr>\n";
				}
			}
			else
			{
				$current_level = 1;
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=4 class=medium_font_light align=center>\n\t<b>Level 1 Filters</b>\n\t</td>\n</tr>\n";

			}

			if ($result->RecordCount() > 0)
			{
				//$this->body .= $result->RecordCount()." is the record count<br>\n";
				//display the sub categories of this filter
				if ($filter)
					$this->body .= "<tr class=row_color_black>\n\t<td colspan=4 class=medium_font_light>\n\t".$this->messages[3505]." <b>".$filter_name."</b> \n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color_black>\n\t<td align=center class=medium_font_light><b>filter name:</b>\n\t</td>\n\t";
				$this->body .= "<td align=center class=medium_font_light>\n\t<b>edit filter name</b>\n\t</td>\n\t";
				$this->body .= "<td align=center class=medium_font_light>\n\t<b>delete filter</b>\n\t</td>\n\t";
				$this->body .= "<td align=center class=medium_font_light>\n\t<b>show subfilters</b>\n\t</td>\n\t</tr>";
				$this->row_count = 0;
				while ($show_sub_categories = $result->FetchRow())
				{
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td>\n\t<a href=index.php?a=68&b=".$show_sub_categories["filter_id"]."><span class=medium_font>".$show_sub_categories["filter_name"]."</span></a></td>\n\t";
					$this->body .= "<td align=center>\n\t<a href=index.php?a=68&b=".$show_sub_categories["filter_id"]."&e=edit><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></a>\n\t</span>\n\t";
					$this->sql_query = "select * from ".$this->classified_filters_table." where parent_id = ".$show_sub_categories["filter_id"]." order by display_order";
					$subfilter_result = $db->Execute($this->sql_query);
					//echo $this->sql_query." is the query<br>\n";
					if (!$subfilter_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[5501];
						return false;
					}
					elseif ($subfilter_result->RecordCount() == 0)
					{
						$this->body .= "<td align=center>\n\t
							<a href=index.php?a=68&x=".$show_sub_categories["filter_id"]."><span class=medium_font>".$this->messages[3508]."</span>\n\t</td>\n";
					}
					else
					{
						//echo "<td>\n\t".$this->messages[3507]."\n\t</td>\n";
						$this->body .= "<td align=center>-</td>\n\t";
					}
					$this->body .= "<td align=center><a href=index.php?a=68&b=".$show_sub_categories["filter_id"]."><span class=medium_font><img src=admin_images/btn_admin_enter.gif alt=enter border=0></span></a></td>\n\t";
					$this->body .= "</tr>\n";
					$this->row_count++;
				}
			}
			else
			{
				//no sub categories to this filter
				$this->body .= "<tr>\n\t<td colspan=4 class=medium_font align=center>\n\t<br><br><b>There are currently no level ".($current_level+1)." subfilters.</b><br><br>";
				if ($filter)
					$this->body .= " of <b>".$filter_name." - level ".$current_level."</b>";
				$this->body .= " \n\t</td>\n</tr>\n";
			}
		}
		if ($filter)
			$this->body .= "<tr>\n\t<td colspan=4 align=center>\n\t<a href=index.php?a=68&i=insert&j=".$filter."><span class=medium_font>
				<b>add a Level ".($current_level + 1)." subfilter to the ".$filter_name." Filter</b><br><br></span></a>\n\t</td>\n</tr>\n";
		else
			$this->body .= "<tr>\n\t<td colspan=4 align=center>\n\t<a href=index.php?a=68&i=insert><span class=medium_font>
				<b>insert a new Level 1 Filter</b><br><br></span></a>\n\t</td>\n</tr>\n";

		//associate levels with registration and optional site wide fields
		$this->body .= "<tr bgcolor=000066>\n\t<td colspan=4 class=large_font_light>\n\t
			Current Filter Level Associations </a>\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=4 class=medium_font>\n\t
			Choose the associations you wish to make from the dropdown below.  You must associate each level of the filters
			with a field within the registration and browse/display fields of each listing.  You can associate the filter with
			anyone of the ten optional registration fields and the twenty optional site wide fields.  These fields (within registration and
			listing setup / fields to use) will automatically be populated from the filters names above.<br><br> </a>\n\t</td>\n</tr>\n";
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=68&b=".$filter." method=post name=association>\n";
		$this->sql_query = "select count(distinct(filter_level)) as level_count  from ".$this->classified_filters_table;
		$level_count_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$level_count_result)
		{
			$this->error_message = $this->messages[5501];
			return false;
		}
		elseif ($level_count_result->RecordCount() == 1)
		{
			$show_level = $level_count_result->FetchRow();
			$this->sql_query = "select * from ".$this->registration_configuration_table;
			$registration_result = $db->Execute($this->sql_query);
			// echo $this->sql_query."<br>\n";
			if (!$registration_result)
			{
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($registration_result->RecordCount() > 0)
			{
				$registration_configuration = $registration_result->FetchRow();
				$reg_config[1] = $registration_configuration["registration_optional_1_filter_association"];
				$reg_config[2] = $registration_configuration["registration_optional_2_filter_association"];
				$reg_config[3] = $registration_configuration["registration_optional_3_filter_association"];
				$reg_config[4] = $registration_configuration["registration_optional_4_filter_association"];
				$reg_config[5] = $registration_configuration["registration_optional_5_filter_association"];
				$reg_config[6] = $registration_configuration["registration_optional_6_filter_association"];
				$reg_config[7] = $registration_configuration["registration_optional_7_filter_association"];
				$reg_config[8] = $registration_configuration["registration_optional_8_filter_association"];
				$reg_config[9] = $registration_configuration["registration_optional_9_filter_association"];
				$reg_config[10] = $registration_configuration["registration_optional_10_filter_association"];
				//$reg_config[11] = $registration_configuration["registration_optional_11_filter_association"];
				//$reg_config[12] = $registration_configuration["registration_optional_12_filter_association"];
				//$reg_config[13] = $registration_configuration["registration_optional_13_filter_association"];
				//$reg_config[14] = $registration_configuration["registration_optional_14_filter_association"];
				//$reg_config[15] = $registration_configuration["registration_optional_15_filter_association"];
				//$reg_config[16] = $registration_configuration["registration_optional_16_filter_association"];
				//$reg_config[17] = $registration_configuration["registration_optional_17_filter_association"];
				//$reg_config[18] = $registration_configuration["registration_optional_18_filter_association"];
				//$reg_config[19] = $registration_configuration["registration_optional_19_filter_association"];
				//$reg_config[20] = $registration_configuration["registration_optional_20_filter_association"];

				$sql_query = "select * from ".$this->registration_configuration_table;
				$result = $db->Execute($sql_query);
				if (!$result)
				{
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$registration_configuration = $result->FetchRow();
					for ($i=1;$i <= 10;$i++)
					{
						$this->body .= "<tr class=row_color2>\n\t<td colspan=2 align=right width=50% class=medium_font><b>".$registration_configuration['registration_optional_'.$i.'_field_name'].": </b> </td>";

						//reset ($reg_config);
						//foreach ($reg_config as $key => $value)
						//	echo $key." is the key and ".$value." is the value<bR>\n";
						$this->body .= "<td colspan=2 class=medium_font><select";
						if (!$this->admin_demo()) $this->body .= " name=j[".$i."] onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\"";
						$this->body .= "><option value=0>no association</option>\n";
						for ($n = 1;$n <= $show_level["level_count"];$n++)
						{
							$this->body .= "<option value=".$n;
							if ($reg_config[$i] == $n)
								$this->body .= " selected";
							$this->body .= ">filter level ".$n."</option>\n\t\t";
						} // end of while
						$this->body .= "</select>\n";
						$this->body .= " </td></tr>\n";
					}
				}

				//associate level 1 with registration and optional site wide fields
				$site_config[1] = $filter_config["optional_1_filter_association"];
				$site_config[2] = $filter_config["optional_2_filter_association"];
				$site_config[3] = $filter_config["optional_3_filter_association"];
				$site_config[4] = $filter_config["optional_4_filter_association"];
				$site_config[5] = $filter_config["optional_5_filter_association"];
				$site_config[6] = $filter_config["optional_6_filter_association"];
				$site_config[7] = $filter_config["optional_7_filter_association"];
				$site_config[8] = $filter_config["optional_8_filter_association"];
				$site_config[9] = $filter_config["optional_9_filter_association"];
				$site_config[10] = $filter_config["optional_10_filter_association"];
				$site_config[11] = $filter_config["optional_11_filter_association"];
				$site_config[12] = $filter_config["optional_12_filter_association"];
				$site_config[13] = $filter_config["optional_13_filter_association"];
				$site_config[14] = $filter_config["optional_14_filter_association"];
				$site_config[15] = $filter_config["optional_15_filter_association"];
				$site_config[16] = $filter_config["optional_16_filter_association"];
				$site_config[17] = $filter_config["optional_17_filter_association"];
				$site_config[18] = $filter_config["optional_18_filter_association"];
				$site_config[19] = $filter_config["optional_19_filter_association"];
				$site_config[20] = $filter_config["optional_20_filter_association"];
				for ($i=1;$i <= 20;$i++)
				{
					$this->body .= "<tr class=row_color1>\n\t<td colspan=2 align=right class=medium_font><b>".$this->configuration_data['optional_field_'.$i.'_name'].":</b> </td>";
					//reset ($site_config);
					//foreach ($site_config as $key => $value)
					//	echo $key." is the key and ".$value." is the value<bR>\n";
					$this->body .= "<td colspan=2 class=medium_font><select";
					if (!$this->admin_demo()) $this->body .= " name=k[".$i."] onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\"";
					$this->body .= "><option value=0>no association</option>\n";
					for ($n = 1;$n <= $show_level["level_count"];$n++)
					{
						$this->body .= "<option value=".$n;
						if ($site_config[$i] == $n)
							$this->body .= " selected";
						$this->body .= ">filter level ".$n."</option>\n\t\t";
					} // end of while
					$this->body .= "</select>\n";
					$this->body .= " </td></tr>\n";
				}
			}
		}
		$this->body .= "</form>";

		//display the current filters for each language
		$this->sql_query = "select language_id,language from ".$this->pages_languages_table." order by language_id asc";
		$language_result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$language_result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[3501];
			return false;
		 }
		 elseif ($language_result->RecordCount() > 0)
		 {
			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=4 class=large_font_light>\n\t
				Current Complete Filter Dropdowns \n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td colspan=4 class=medium_font>\n\t
				Below is the contents of the current filter dropdowns.  This is just a quick display to illustrate what is
				currently within each level of the filters.<br><br> \n\t</td>\n</tr>\n";
			$this->sql_query = "select count(distinct(filter_level)) as level_count from ".$this->classified_filters_table;
			$level_count_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$level_count_result)
			{
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($level_count_result->RecordCount() ==1)
			{
				$show_count = $level_count_result->FetchRow();
				$total_levels = $show_count["level_count"];
			}
		 	while ($show_language = $language_result->FetchRow())
		 	{
				$this->body .= "<tr class=row_color2><td colspan=4 align=center><table cellspacing=1 cellpadding=2 border=0>";
				$this->sql_query = "select geodesic_classifieds_filters.filter_id,geodesic_classifieds_filters.filter_level, geodesic_classifieds_filters.parent_id, geodesic_classifieds_filters_languages.filter_name
					from ".$this->classified_filters_table.",".$this->classified_filters_languages_table."
					where ".$this->classified_filters_table.".filter_id = ".$this->classified_filters_languages_table.".filter_id
					and ".$this->classified_filters_languages_table.".language_id = ".$show_language["language_id"]." order by filter_level asc, display_order asc";

				$filter_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$filter_result)
				{
					$this->error_message = $this->messages[5501];
					return false;
				}
				elseif ($filter_result->RecordCount() > 0)
				{
					$level = 1;
					$this->body .= "<tr><td><select>\n\t\t";
					$this->body .=  "<option>level ".$level." dropdown</option>\n\t\t";
					while ($show_filter = $filter_result->FetchRow())
					{
						if ($show_filter["filter_level"] == $level)
						{
							$this->body .=  "<option>".$show_filter["filter_name"]."</option>\n\t\t";
						}
						else
						{
							$level++;
							$this->body .=  "</select>\n\t";
							if ($level <= $total_levels)
							{
								$this->body .=  "</td>\n</tr>\n<tr>\n\t<td>\n\t";
								$this->body .=  "<select>\n\t";
								$this->body .=  "<option>level ".$level." dropdown</option>\n\t\t";
								$this->body .=  "<option>".$show_filter["filter_name"]."</option>\n\t\t";
							}
							else
							{
								//last one
								$this->body .=  "</td>\n</tr>\n";
							}

						}
					}
				}
				$this->body .= "</table></td></tr>";
			}
		}
		$this->body .= "</table>\n";
		return true;
	} //end of function browse

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function filter_error()
	{
		$this->body .= "<table cellpadding=5 cellspacing=1 border=0 width=100%>\n";
		$this->body .= "<tr>\n\t<td>".$this->messages[3510]."</td>\n</tr>\n";
		if ($this->error_message)
			$this->body .= "<tr>\n\t<td>".$this->error_messages."</td>\n</tr>\n";
		$this->body .= "</table>\n";
	} //end of function filter_error

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function migrate_languages_filters($db)
	{
		$this->sql_query = "select * from ".$this->pages_languages_table." where language_id != 1";
		$result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br><br>\n";
		if (!$result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}
		while ($show_language = $result->FetchRow())
		{
			$this->sql_query = "select * from ".$this->classified_filters_table;
			$filter_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br><br>\n";
			if (!$filter_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			while ($show_filter = $filter_result->FetchRow())
			{
				$this->sql_query = "insert into ".$this->classified_filters_languages_table."
					(filter_id,filter_name,description,language_id)
					values
					(".$show_filter["filter_id"].",\"".$show_filter["filter_name"]."\",\"".$show_filter["description"]."\",".$show_language["language_id"].")";
				$insert_result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is the query<br>\n";
				if (!$insert_result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[5501];
					return false;
				}
			}
		}
	} // end of function migrate_languages_filters

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_filter_level($db,$filter)
	{
		$i = 0;
		$filter_next = $filter;
		do
		{
			$this->sql_query = "select filter_id,parent_id,filter_name from ".$this->classified_filters_table."
				where filter_id = ".$filter_next;
			$filter_result =  $db->Execute($this->sql_query);

			//$filter = array();

			//echo $this->sql_query." is the query<br>\n";
			if (!$filter_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3501];
				return false;
			}
			elseif ($filter_result->RecordCount() == 1)
			{
				$show_filter = $filter_result->FetchRow();
				$this->filter_level_array[$i]["parent_id"]  = $show_filter["parent_id"];
				$this->filter_level_array[$i]["filter_name"] = $show_filter["filter_name"];
				$this->filter_level_array[$i]["filter_id"]   = $show_filter["filter_id"];
				$i++;
				$filter_next = $show_filter["parent_id"];
			}
			else
			{
				//echo "wrong return<Br>\n";
				return false;
			}

     		} while ( $show_filter["parent_id"] != 0 );

     		return $i;

	} // end of function get_filter_level

//########################################################################

	function update_filter_associations($db,$registration=0,$ad_display=0)
	{
		if (($registration) && ($ad_display))
		{
			//$ad_display1 = array();
			//echo count($ad_display)." is count of ad_display<br>\n";
			//reset ($ad_display);
			//foreach ($ad_display as $key => $value)
			//{
			//	echo $key." is the key to ".$value." - ".$ad_display[$key]."<br>\n";
			//}

			$this->sql_query = "update ".$this->site_configuration_table." set
				optional_1_filter_association = ".$ad_display[1].",
				optional_2_filter_association = ".$ad_display[2].",
				optional_3_filter_association = ".$ad_display[3].",
				optional_4_filter_association = ".$ad_display[4].",
				optional_5_filter_association = ".$ad_display[5].",
				optional_6_filter_association = ".$ad_display[6].",
				optional_7_filter_association = ".$ad_display[7].",
				optional_8_filter_association = ".$ad_display[8].",
				optional_9_filter_association = ".$ad_display[9].",
				optional_10_filter_association = ".$ad_display[10].",
				optional_11_filter_association = ".$ad_display[11].",
				optional_12_filter_association = ".$ad_display[12].",
				optional_13_filter_association = ".$ad_display[13].",
				optional_14_filter_association = ".$ad_display[14].",
				optional_15_filter_association = ".$ad_display[15].",
				optional_16_filter_association = ".$ad_display[16].",
				optional_17_filter_association = ".$ad_display[17].",
				optional_18_filter_association = ".$ad_display[18].",
				optional_19_filter_association = ".$ad_display[19].",
				optional_20_filter_association = ".$ad_display[20];
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
			//$registration1 = array();
			//echo count($registration)." is count of registration<br>\n";
			//reset ($registration);
			//foreach ($registration as $key => $value)
			//{
			//	echo $key." is the key to ".$value." - ".$registration[$key]."<br>\n";
			//	$registration1[$value] = $key;
			//}
			$this->sql_query = "update ".$this->registration_configuration_table." set
				registration_optional_1_filter_association = ".$registration[1].",
				registration_optional_2_filter_association = ".$registration[2].",
				registration_optional_3_filter_association = ".$registration[3].",
				registration_optional_4_filter_association = ".$registration[4].",
				registration_optional_5_filter_association = ".$registration[5].",
				registration_optional_6_filter_association = ".$registration[6].",
				registration_optional_7_filter_association = ".$registration[7].",
				registration_optional_8_filter_association = ".$registration[8].",
				registration_optional_9_filter_association = ".$registration[9].",
				registration_optional_10_filter_association = ".$registration[10];

				//registration_optional_11_filter_association = ".$registration[11].",
				//registration_optional_12_filter_association = ".$registration[12].",
				//registration_optional_13_filter_association = ".$registration[13].",
				//registration_optional_14_filter_association = ".$registration[14].",
				//registration_optional_15_filter_association = ".$registration[15].",
				//registration_optional_16_filter_association = ".$registration[16].",
				//registration_optional_17_filter_association = ".$registration[17].",
				//registration_optional_18_filter_association = ".$registration[18].",
				//registration_optional_19_filter_association = ".$registration[19].",
				//registration_optional_20_filter_association = ".$registration[20];
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
			return true;
		}
		else
			return false;
	} //end of function update_filter_associations

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_filter_switch($db,$switch=0)
	{
		//echo "hello from update filter switch - ".$switch."<br>\n";
		if ($switch)
		{
			$switch = str_replace("m=1","",$switch);
			$switch = str_replace("m=0","",$switch);
			if ($switch == 2)
				$switch = 0;
			$this->sql_query = "update ".$this->site_configuration_table." set use_filters = ".$switch;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
			return true;
		}
		else
			return false;
	} //end of function update_filter_switch

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end of class Admin_filters

?>