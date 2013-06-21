<? //module_display_filters_2.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$debug_filter_module = 0;
if ($debug_filter_module)
{
	echo "<br>TOP OF MODULE_DISPLAY_FILTERS_2.PHP<bR>\n";
	echo $this->configuration_data->USE_FILTERS." is use filters in display filter module 2<br>\n";
	echo $show_module['module_display_filter_in_row']." is MODULE_DISPLAY_FILTER_IN_ROW<bR>\n";
	echo $show_module['display_unselected_subfilters']." is DISPLAY_UNSELECTED_SUBFILTERS - 0 is yes<br>\n";
	echo $this->filter_id." is the current filter_id<BR>\n";
}
if ($this->configuration_data['use_filters'])
{
	$this->get_css($db,$show_module['page_id']);
	$this->get_text($db,$show_module['page_id']);

	//setup dropdown labels
	$dropdown_labels = array();
	$dropdown_labels[1] = urldecode($this->messages[1541]);
	$dropdown_labels[2] = urldecode($this->messages[1542]);
	$dropdown_labels[3] = urldecode($this->messages[1543]);
	$dropdown_labels[4] = urldecode($this->messages[1544]);
	$dropdown_labels[5] = urldecode($this->messages[1545]);
	$dropdown_labels[6] = urldecode($this->messages[1546]);
	$dropdown_labels[7] = urldecode($this->messages[1547]);
	$dropdown_labels[8] = urldecode($this->messages[1548]);
	$dropdown_labels[9] = urldecode($this->messages[1549]);
	$dropdown_labels[10] = urldecode($this->messages[1550]);
	$select_previous[1] = urldecode($this->messages[1561]);
	$select_previous[2] = urldecode($this->messages[1562]);
	$select_previous[3] = urldecode($this->messages[1563]);
	$select_previous[4] = urldecode($this->messages[1564]);
	$select_previous[5] = urldecode($this->messages[1565]);
	$select_previous[6] = urldecode($this->messages[1566]);
	$select_previous[7] = urldecode($this->messages[1567]);
	$select_previous[8] = urldecode($this->messages[1568]);
	$select_previous[9] = urldecode($this->messages[1569]);
	$select_previous[10] = urldecode($this->messages[1570]);
	$clear_labels[1] = urldecode($this->messages[1551]);
	$clear_labels[2] = urldecode($this->messages[1552]);
	$clear_labels[3] = urldecode($this->messages[1553]);
	$clear_labels[4] = urldecode($this->messages[1554]);
	$clear_labels[5] = urldecode($this->messages[1555]);
	$clear_labels[6] = urldecode($this->messages[1556]);
	$clear_labels[7] = urldecode($this->messages[1557]);
	$clear_labels[8] = urldecode($this->messages[1558]);
	$clear_labels[9] = urldecode($this->messages[1559]);
	$clear_labels[10] = urldecode($this->messages[1560]);
	$last_one = 0;
	$this->body = "<form name=filter_display_module_form_2 action=".$this->configuration_data['classifieds_file_name']."?".$_SERVER["QUERY_STRING"]." method=post>\n";
	$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>";
	if ($this->filter_id)
	{
		//get the current filter settings
		$this->sql_query = "select * from geodesic_classifieds_filters
			where filter_id = ".$this->filter_id;
		if ($debug_filter_module) echo $this->sql_query." is getting the current filter stuff<br>\n";
		$current_filter_result =  $db->Execute($this->sql_query);
		if (!$current_filter_result)
		{
			if ($debug_filter_module) echo $db->ErrorMsg()." is the error<bR>\n";
			return false;
		}
		elseif ($current_filter_result->RecordCount() == 1)
		{
			$current_filter = $current_filter_result->FetchRow();
		}
		else
		{
			if ($debug_filter_module) echo $db->ErrorMsg()." is the error<bR>\n";
			return false;
		}

		if ($debug_filter_module) echo $current_filter["filter_level"]." is the current filter level<bR>\n";
		//get parents of current filter_id
		if ($current_filter["filter_level"] != 1)
		{
			//$i = $current_filter["filter_level"] - 1;
			$i = $current_filter["filter_level"];
			//$filter_next = $current_filter["parent_id"];
			$filter_next = $current_filter["filter_id"];
			$filter_level_array = array();
			if ($debug_filter_module)
			{
				echo "current filter level != 1<BR>\n";
				echo $filter_next." is filter_next<BR>\n";
				echo $i." is i<Br>\n";
			}
			while ($filter_next != 0)
			{
				$this->sql_query = "select * from geodesic_classifieds_filters where filter_id = ".$filter_next;
				if ($debug_filter_module) echo $this->sql_query." is the query<br>\n";
				$filter_result =  $db->Execute($this->sql_query);
				if (!$filter_result)
				{
					if ($debug_filter_module) echo $db->ErrorMsg()." is the error<bR>\n";
					$this->error_message = $this->messages[3501];
					return false;
				}
				elseif ($filter_result->RecordCount() == 1)
				{
					$show_filter = $filter_result->FetchRow();
					$filter_level_array[$i]["parent_id"]  = $show_filter["parent_id"];
					$filter_level_array[$i]["filter_name"] = $show_filter["filter_name"];
					$filter_level_array[$i]["filter_id"]   = $show_filter["filter_id"];
					$i--;
					$filter_next = $show_filter["parent_id"];
				}
				else
				{
					if ($debug_filter_module) echo $db->ErrorMsg()." is the error<bR>\n";
					return false;
				}
			}

			if ($debug_filter_module)
			{
				echo "<bR><b>filter_level_array</b><Br>\n";
				var_dump($filter_level_array);
				echo "<BR><Br>";
			}

			//display dropdowns above current filter's level
			if ($debug_filter_module) echo "about to display above current filter level<bR>\n";
			$parents_level = 1;
			$parents_id = 0 ;
			$parent_in_statement = "";
			$this->body .= "<tr>";
			do
			{
				$this->sql_query = "select
					geodesic_classifieds_filters.filter_id,
					geodesic_classifieds_filters.filter_level,
					geodesic_classifieds_filters.parent_id,
					geodesic_classifieds_filters.in_statement,
					geodesic_classifieds_filters_languages.filter_name
					from geodesic_classifieds_filters,geodesic_classifieds_filters_languages
					where geodesic_classifieds_filters.filter_id = geodesic_classifieds_filters_languages.filter_id
					and geodesic_classifieds_filters.filter_level = ".$parents_level;
				if ($parent_in_statement)
					$this->sql_query .= " and geodesic_classifieds_filters.filter_id ".$parent_in_statement;
				$this->sql_query .= " and geodesic_classifieds_filters_languages.language_id = ".$this->language_id." order by filter_level asc, display_order asc, geodesic_classifieds_filters_languages.filter_name";

				$level_filter_result = $db->Execute($this->sql_query);
				if ($debug_filter_module) echo $this->sql_query." is getting ".$parents_level." stuff 1<br><br>\n";
				if (!$level_filter_result)
				{
					if ($debug_filter_module) echo $db->ErrorMsg()." is the error<bR><br>\n";
					$this->error_message = $this->messages[5501];
					return false;
				}
				elseif ($level_filter_result->RecordCount() > 0)
				{
					$this->body .= "<td class=filter_dropdown_2><select class=filter_dropdown_2 name=set_filter_id[".$parents_level."] onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\">\n\t";
					$this->body .= "<option value=0>".$dropdown_labels[$parents_level]."</option>\n\t";

					while ($show_filter = $level_filter_result->FetchRow())
					{
						if ($debug_filter_module)
						{
							echo "if ".$show_filter["parent_id"]." equals ".$filter_level_array[$show_filter["filter_level"]]["parent_id"]." (".($show_filter["filter_level"] - 1).") then ".$show_filter["filter_name"]." is displayed<bR>\n";
							echo $show_filter["parent_id"]." is the current filter parent id<bR>\n";
							echo $show_filter["filter_level"]." is the current filter filter level<BR>\n";

						}
						if ($show_filter["parent_id"] == $filter_level_array[$show_filter["filter_level"]]["parent_id"])
						{
							$this->body .= "<option value=";
							if ($show_filter["filter_id"] == $current_filter["filter_id"])
							{
								$this->body .=  "\"\" selected";
								$show_all_selection = 1;
							}
							elseif ($show_filter["filter_id"] == $filter_level_array[$parents_level]["filter_id"])
								$this->body .=  "\"\" selected";
							else
								$this->body .= "\"".$show_filter["filter_id"]."\"";
							$this->body .= ">".$show_filter["filter_name"]."</option>\n\t";
							if ($show_filter["filter_id"] == $current_filter["parent_id"])
							{
								$parent_in_statement = $show_filter["in_statement"];

							}

							if ($debug_filter_module)
							{
								echo "displaying <b>".$show_filter["filter_name"]."</b> filter name<BR>\n";
								echo "if ".$current_filter["parent_id"]." equals ".$show_filter["filter_id"]." the parent_in_statement is set<Br>\n";
								echo $parent_in_statement." is parent_in_statement<BR>\n";
							}
						}
					}
					$this->body .= "<option value=";
					if ($show_all_selection)
						$this->body .= $current_filter["parent_id"];
					elseif ($filter_level_array[$parents_level]["parent_id"] == 0)
						$this->body .= "\"clear\"";
					else
						$this->body .= "\"".$filter_level_array[$parents_level]["parent_id"]."\"";
					$this->body .= ">".$clear_labels[$parents_level]."</option>\n\t\t";
					$this->body .= "</select></td>";
					if (!$show_module['module_display_filter_in_row'])
						$this->body .= "</tr><tr>";

				}
				else
				{
					if ($debug_filter_module) echo "error in getting subfilters<Br>\n";
					return false;
				}
				$parents_level++;
				$parents_id = $current_filter["parent_id"];

			}
			while ($parents_level <= $current_filter["filter_level"]);

			$this->sql_query = "select geodesic_classifieds_filters.filter_id,geodesic_classifieds_filters.filter_level, geodesic_classifieds_filters.parent_id, geodesic_classifieds_filters_languages.filter_name
				from geodesic_classifieds_filters,geodesic_classifieds_filters_languages
				where geodesic_classifieds_filters.filter_id = geodesic_classifieds_filters_languages.filter_id
				and geodesic_classifieds_filters.filter_id ".$current_filter["in_statement"]."
				and geodesic_classifieds_filters.filter_level > ".$current_filter["filter_level"]."
				and geodesic_classifieds_filters_languages.language_id = ".$this->language_id."
				and geodesic_classifieds_filters.parent_id = ".$current_filter["filter_id"]."
				order by filter_level asc, display_order asc, geodesic_classifieds_filters_languages.filter_name";
			$level = $parents_level;

			if ($debug_filter_module)
			{
				echo "<b>END OF PARENT DISPLAY</b><BR><br>\n";
			}
		}
		else
		{
			if ($debug_filter_module)
			{
				echo "current filter level == 1<BR>\n";
				echo $current_filter["in_statement"]." is current_filter->IN_STATEMENT<BR>\n";
			}
			$this->sql_query = "select geodesic_classifieds_filters.filter_id,geodesic_classifieds_filters.filter_level, geodesic_classifieds_filters.parent_id, geodesic_classifieds_filters_languages.filter_name
				from geodesic_classifieds_filters,geodesic_classifieds_filters_languages
				where geodesic_classifieds_filters.filter_id = geodesic_classifieds_filters_languages.filter_id
				and (geodesic_classifieds_filters.filter_id ".$current_filter["in_statement"]."
				or geodesic_classifieds_filters.filter_level = 1 )
				and geodesic_classifieds_filters_languages.language_id = ".$this->language_id."
				order by filter_level asc, display_order asc ,geodesic_classifieds_filters_languages.filter_name";
			$level = 1;
		}
		$filter_result = $db->Execute($this->sql_query);
		if ($debug_filter_module) echo $this->sql_query." is the query<br>\n";

		if (!$filter_result)
		{
			if ($debug_filter_module) echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}
		elseif ($filter_result->RecordCount() > 0)
		{
			if ($debug_filter_module) echo $filter_result->RecordCount()." is the recordcount of total filters retrieved<bR>\n";
			$this->sql_query = "select count(distinct(filter_level)) as level_count from geodesic_classifieds_filters";
			$level_count_result = $db->Execute($this->sql_query);
			if ($debug_filter_module)
			{
				echo $this->sql_query."<br>\n";
				echo $current_filter->FILTER_LEVEL." is current_filter->FILTER_LEVEL before dropdown display<BR>\n";
				echo $level." is level before dropdown display<Br>\n";
			}
			if (!$level_count_result)
			{
				if ($debug_filter_module)
				{
					echo $db->ErrorMsg()." is the error<bR>\n";
					echo $this->sql_query."<br>\n";
				}
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($level_count_result->RecordCount() ==1)
			{
				$show_count = $level_count_result->FetchRow();
				$total_levels = $show_count["level_count"];
			}

			if ($debug_filter_module)
			{
				echo $current_filter["filter_level"]." is current_filter->FILTER_LEVEL before dropdown display<BR>\n";
				echo $level." is level before dropdown display<Br>\n";
				echo $level_count_result->RecordCount()." is level_count_result->recordcount<BR>\n";
				echo $total_levels." is total_levels<bR>\n";
				echo $show_module["display_unselected_subfilters"]." is display_unselected_subfilters<bR>\n";
				echo $filter_result->RecordCount()." is filter_result->recordcount<bR>\n";
			}

			$total_options = 0;
			if (!$show_module['module_display_filter_in_row'])
				$this->body .= "<tr>";
			$this->body .= "<td class=filter_dropdown_2><select class=filter_dropdown_2 name=set_filter_id[".$level."] onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\">\n\t\t";
			if (($show_module['display_unselected_subfilters']) && ($current_filter["filter_level"] <= $level))
				$this->body .= "<option value=\"\">".$dropdown_labels[$level]."</option>\n\t\t";
			else
				$this->body .= "<option value=\"\">".$select_previous[$level]."</option>\n\t\t";
			$show_all_selection = 0;
			while ($show_filter = $filter_result->FetchRow())
			{
				if ($debug_filter_module)
				{
					echo "<br>".$show_filter["filter_name"]." is show_filter[filter_name]<bR>\n";
					echo $show_filter["filter_level"]." is show_filter[filter_level] in loop<BR>\n";
					echo $level." is level i loop<Br>\n";
					echo $show_filter["filter_id"]." is show_filter->filter_id<BR>\n";
					echo $show_all_selection." is show_all_selection<br>\n";
					echo $current_filter["filter_level"]." is current_filter[filter_level]<br>\n";
				}
				if ($show_filter["filter_level"] == $level)
				{
					if ($debug_filter_module)
					{
						echo "doing the if condition (".$show_module['display_unselected_subfilters']." is display_unselected_subfilters - ".$current_filter["filter_level"]." current_filter[filter_level] - ".$level." is level <BR>\n";
					}
					if (($show_module['display_unselected_subfilters']) && ($current_filter["filter_level"] <= $level))
					{
						if ($debug_filter_module) echo "displaying filter- ".$show_filter["filter_name"]." - ".$show_filter["filter_level"]." <= ".$current_filter["filter_level"]."<bR>\n";
						$this->body .= "<option value=\"".$show_filter["filter_id"]."\"";
						if ($show_filter["filter_id"] == $this->filter_id)
						{
							$this->body .= " selected";
							$show_all_selection = 1;
							if ($debug_filter_module) echo "selecting - ".$show_filter["filter_id"]." - ".$show_filter["filter_name"]."<br>\n";
						}
						$this->body .= ">".$show_filter["filter_name"]."</option>\n\t\t";
					}
					$total_options++;
				}
				elseif (($show_filter["filter_level"] == ($current_filter["filter_level"] + 1)) && ($show_filter["parent_id"] == $this->filter_id))
				//elseif ($show_filter["filter_level"] == ($level + 1))
				//else
				{
					if ($debug_filter_module)
					{
						echo "doing the elseif condition (".$current_filter["filter_level"]." +1 == ".$show_filter["filter_level"]." show_filter[filter_level] - ".$show_filter["filter_name"]." - ".$show_filter["parent_id"]." == ".$this->filter_id."<BR>\n";
						echo $show_all_selection." is show_all_selection<BR>\n";
						echo $current_filter["parent_id"]." is current_filter[parent_id]<bR>\n";
						echo $level." is level<bR>\n";
					}
					if ($show_all_selection)
					{
						$this->body .= "<option value=";
						if ($current_filter["parent_id"] == 0)
						{
							$this->body .= "\"clear\"";
						}
						else
						{
							$this->body .= "\"".$current_filter["parent_id"]."\"";
						}
						$this->body .= ">".$clear_labels[$level]."</option>\n\t\t";
						if ($debug_filter_module) echo "just displayed the clear for this one - ".$clear_labels[$level]."<bR>\n";
					}
					$level++;
					if ($debug_filter_module) echo "displaying the closing select<bR>\n";
					$this->body .= "</select>\n\t";
					$show_all_selection = 0;
					if (($level <= $total_levels) && ($filter_result->RecordCount() > $total_options))
					//if (($show_filter["filter_level"] == ($current_filter["filter_level"] + 1)) && ($filter_result->RecordCount() > $total_options))
					{
						if ($debug_filter_module) echo "got in here to start the next one<BR>\n";
						if ($show_module['module_display_filter_in_row'])
						{
							$this->body .= "</td>\n\t<td class=filter_dropdown_2>\n\t";
							$this->body .= "<select name=set_filter_id[".$level."] class=filter_dropdown_2 onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\">\n\t";
						}
						else
						{
							$this->body .= "</td>\n</tr>\n<tr>\n\t<td class=filter_dropdown_2>\n\t";
							$this->body .= "<select name=set_filter_id[".$level."] class=filter_dropdown_2 onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\">\n\t";
						}
						if (($show_module['display_unselected_subfilters']) && ($current_filter["filter_level"] <= ($level - 1)))
						{
							$this->body .= "<option value=\"\">".$dropdown_labels[$level]."</option>\n\t\t";
							$this->body .= "<option value=\"".$show_filter["filter_id"]."\">".$show_filter["filter_name"]."</option>\n\t\t";
						}
						else
							$this->body .= "<option value=\"\">".$select_previous[$level]."</option>\n\t\t";
						$total_options++;
					}
					else
					{
						//last one
						if ($debug_filter_module) echo "this is the last one<bR>\n";
						$this->body .= "</td>\n</tr>\n";
					}

				}
				elseif ($show_filter["filter_level"] > ($current_filter["filter_level"] + 1))
				{
					if ($debug_filter_module) echo " greater than current filter - ".$last_one." is last_one<BR>\n";

					if (!$last_one)
					{
						if ($debug_filter_module) echo "displaying last closing select and td<BR>\n";
						$this->body .= "</select></td>";
					}
					$last_one = 1;
				}
				else
				{
					if ($debug_filter_module)
					{
						echo "not displaying this one....".$show_filter["filter_name"]." - or ".$show_filter["parent_id"]." != ".$this->filter_id."<BR>\n";
					}
				}

			}
		}
		else
		{
			$this->body .= "</tr>";
		}
	}
	else
	{
		$this->sql_query = "select geodesic_classifieds_filters.filter_id,geodesic_classifieds_filters.filter_level,
			geodesic_classifieds_filters_languages.filter_name
			from geodesic_classifieds_filters,geodesic_classifieds_filters_languages
			where geodesic_classifieds_filters.filter_id = geodesic_classifieds_filters_languages.filter_id
			and geodesic_classifieds_filters_languages.language_id = ".$this->language_id."
			order by filter_level asc, display_order asc, geodesic_classifieds_filters_languages.filter_name";

		$filter_result = $db->Execute($this->sql_query);
		if ($debug_filter_module) echo $this->sql_query." is the query<br>\n";
		if (!$filter_result)
		{
			if ($debug_filter_module) echo $db->ErrorMsg()." is the error<bR>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}
		elseif ($filter_result->RecordCount() > 0)
		{
			$this->sql_query = "select count(distinct(filter_level)) as level_count from ".$this->filters_table;
			$level_count_result = $db->Execute($this->sql_query);
			if ($debug_filter_module) echo $this->sql_query."<br>\n";
			if (!$level_count_result)
			{
				if ($debug_filter_module) echo $db->ErrorMsg()." is the error<bR>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($level_count_result->RecordCount() ==1)
			{
				$show_count = $level_count_result->FetchRow();
				$total_levels = $show_count["level_count"];
			}

			$level = 1;
			$this->body .= "<tr><td class=filter_dropdown_2><select class=filter_dropdown_2 name=set_filter_id[".$level."] onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\">\n\t\t";
			$this->body .= "<option value=\"\">".$dropdown_labels[$level]."</option>\n\t\t";
			//if ($show_module['display_unselected_subfilters'])
			if (0)
			{
				if ($debug_filter_module)
				{
					echo "doing the if while<BR>\n";
				}
				while ($show_filter = $filter_result->FetchRow())
				{
					if ($show_filter["filter_level"] == $level)
					{
						if ($debug_filter_module)
						{
							echo $show_filter["filter_level"]." is filter_level of ".$show_filter["filter_name"]." in if while<BR>\n";
						}
						$this->body .= "<option value=\"".$show_filter["filter_id"]."\">".$show_filter["filter_name"]."</option>\n\t\t";
					}
					else
					{
						$level++;
						$this->body .= "</select>\n\t";
						if ($level <= $total_levels)
						{
							if ($show_module['module_display_filter_in_row'])
							{
								$this->body .= "</td>\n\t<td class=filter_dropdown_2>\n\t";
								$this->body .= "<select name=set_filter_id[".$level."] class=filter_dropdown_2 onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\">\n\t";
							}
							else
							{
								$this->body .= "</td>\n</tr>\n<tr>\n\t<td class=filter_dropdown_2>\n\t";
								$this->body .= "<select name=set_filter_id[".$level."] class=filter_dropdown_2 onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\">\n\t";
							}
							$this->body .= "<option value=\"\">".$dropdown_labels[$level]."</option>\n\t\t";
							$this->body .= "<option value=\"".$show_filter["filter_id"]."\">".$show_filter["filter_name"]."</option>\n\t\t";
						}
						else
						{
							//last one
							$this->body .= "</td>\n</tr>\n";
						}

					}

				}
			}
			else
			{
				//display only the first level dropdown
				if ($debug_filter_module)
				{
					echo "doing the else while<BR>\n";
				}
				while ($show_filter = $filter_result->FetchRow())
				{
					if ($show_filter["filter_level"] == 1)
					{
						if ($debug_filter_module)
						{
							echo $show_filter["filter_level"]." is filter_level of ".$show_filter["filter_name"]."<BR>\n";
						}
						$this->body .= "<option value=\"".$show_filter["filter_id"]."\">".$show_filter["filter_name"]."</option>\n\t\t";
					}
					elseif ($show_filter["filter_level"] == $level)
					{
						//do not display selection
					}
					else
					{
						$level++;
						$this->body .= "</select>\n\t";
//						if ($level <= $total_levels)
//						{
//							if ($show_module['module_display_filter_in_row'])
//							{
//								$this->body .= "</td>\n\t<td class=filter_dropdown_2>\n\t";
//								$this->body .= "<select name=set_filter_id[".$level."] class=filter_dropdown_2 onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\">\n\t";
//							}
//							else
//							{
//								$this->body .= "</td>\n</tr>\n<tr>\n\t<td class=filter_dropdown_2>\n\t";
//								$this->body .= "<select name=set_filter_id[".$level."] class=filter_dropdown_2 onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\">\n\t";
//							}
//							$this->body .= "<option value=\"\">".$select_previous[$level]."</option>\n\t\t";
//						}
//						else
//						{
//							//last one
							$this->body .= "</td>\n</tr>\n";
//						}
						break;

					}

				}
			}
		}
	}
	$this->body .= "</table>\n</form>\n";
}

?>