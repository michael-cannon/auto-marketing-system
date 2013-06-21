<? //admin_category_questions_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Admin_category_questions extends Admin_site {

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Admin_category_questions($db, $product_configuration=0)
	{
		//constructor
		$this->Admin_site($db, $product_configuration);

		$this->messages["5500"] = "wrong count in return - either category does not exist or too many returns";
		$this->messages["5501"] = "internal db error";
		$this->messages["5502"] = "The subcategories of ";
		$this->messages["5503"] = "There are no subcategories in this category";
		$this->messages["5504"] = "An error ocurred while processing";
		$this->messages["5505"] = "there are no questions attached to this category";
		$this->messages["5506"] = "questions attached to the ";
		$this->messages["5507"] = "Not enough information to complete your request";
		$this->messages["5508"] = "The main category is the parent category and has no questions attached to it";
		$this->messages["5509"] = "Add New Question";
		$this->messages["5510"] = "There are no question types to choose from";
		$this->messages["5511"] = "A question already exists by that name.<br>click the back button and change the name.";

	} //end of function Admin_category_questions

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_if_group($db,$group_id=0)
	{
		if ($group_id)
		{
			//check to see if this number is even a category
			$this->sql_query = "select * from ".$this->classified_groups_table." where group_id = ".$group_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
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
	} //end of function check_if_category

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function admin_question_error()
	{
		$this->body .= "<table cellpadding=5 cellspacing=1 border=0>\n";
		$this->body .= "<tr>\n\t<td>".$this->messages[5504]."</td>\n</tr>\n";
		if ($this->error_message)
			$this->body .= "<tr>\n\t<td>".$this->error_message."</td>\n</tr>\n";
		$this->body .= "</table>\n";

	} //function admin_question_error

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function group_question_form($db,$question_id=0,$group_id=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"name\", \"The value entered into this blank will appear as the question next to the type of question answer method chosen (ie. blank box, dropdown,...). It could be a question or a field label, whatever you choose.\"]\n
			Text[2] = [\"explanation\", \"If you feel that your question needs an explanation you can enter a value into this box. If you enter an explanation a question mark will appear next to the question in the sell process. When the question mark is clicked this explanation will appear in a popup box further explaining how the question helps or should be answered.\"]\n
			Text[3] = [\"choices\", \"The value entered here determines the method this question can be answered. You can leave just a blank box or if you have added pre-valued dropdown boxes choose from one of them.\"]\n
			Text[4] = [\"display \\\"other\\\" box\", \"Here, you can opt to give the classified seller an \\\"other\\\" box if one of the choices you give in the dropdown box does not fit the product or service they are selling. The \\\"other\\\" box will only appear if a dropdown box has been chosen in the \\\"choices\\\" field above. If \\\"just blank input box\\\" is selected in the \\\"choices\\\" field above this value will have no effect.\"]\n
			Text[5] = [\"display order\", \"Choose the order in the existing group questions that this question appears in the group question list.\"]\n";

		//".$this->show_tooltip(5,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		if ($question_id)
		{

			$this->sql_query = "select * from ".$this->sell_questions_table." where question_id = ".$question_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_question = $result->FetchRow();
			}
			else
			{
				$this->error_message = $this->messages[5500];
				return false;
			}

			$group_name = $this->get_group_name($db,$show_question["group_id"]);
			$some_group = $show_question["group_id"];
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=65&c=".$show_question["question_id"]."&d=".$show_question["group_id"]." method=post>";
			$this->body .= "<table cellpadding=5 border=0 cellspacing=0 width=100%  class=row_color1>\n";
			$this->title = "Users / User Groups > User Group Questions > Edit";
			$this->description = "Edit the group question currently assigned with the form below.  Edit the fields as necessary and click \"save\".";
			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=2 class=large_font_light align=center>\n\t<b>User Group: ".$group_name."</b>\n\t</td>\n</tr>\n";

		}
		elseif ($group_id)
		{
			$group_name = $this->get_group_name($db,$group_id);
			$some_group = $group_id;
			//this is a new attached to this category
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=63&c=".$group_id." method=post>";
			$this->body .= "<table cellpadding=5 border=0 cellspacing=0 width=100%  class=row_color1>\n";
			$this->title = "Users / User Groups > Edit Category Questions > Add New Question";
			$this->description = "Add a group question to this group with the form below.  Fill in the blanks and the question will be added to the
				".$group_name." group.";
			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=2 class=large_font_light align=center>\n\t<b>User Group: ".$group_name."</b>\n\t</td>\n</tr>\n";


		}
		else
		{
			$this->error_message = $this->messages["5507"];
			return false;
		}

		//display the current quesions attached this category
		$this->body .= "<tr class=row_color2>\n\t<td valign=top align=right class=medium_font><b>name:</b>".$this->show_tooltip(1,1)."</td>\n\t";
		$this->body .= "<td valign=top class=medium_font><input type=text name=b[question_name] value=\"".$show_question["name"]."\"> \n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td valign=top align=right class=medium_font><b>explanation:</b>".$this->show_tooltip(2,1)."</td>\n\t";
		$this->body .= "<td valign=top class=medium_font><textarea name=b[question_explanation] cols=40 rows=10>".$this->special_chars($show_question["explanation"])."</textarea> \n\t</td>\n\t";

		$this->body .= "<tr class=row_color2>\n\t<td valign=top align=right class=medium_font><b>choices:</b>".$this->show_tooltip(3,1)."</td>\n\t";
		$this->body .= "<td valign=top class=medium_font><select name=b[question_choices]>\n\t";
		$this->body .= "<option value=none ";
		if ($show_question["choices"] == "none")
			$this->body .= "selected";
		$this->body .= "> just blank input box</option>\n\t";
		$this->body .= "<option value=check ";
		if ($show_question["choices"] == "check")
			$this->body .= "selected";
		$this->body .= "> check box</option>\n\t";
		$this->body .= "<option value=textarea ";
		if ($show_question["choices"] == "textarea")
			$this->body .= "selected";
		$this->body .= "> just blank textarea box</option>\n\t";
		$this->body .= "<option value=url ";
		if ($show_question["choices"] == "url")
			$this->body .= "selected";
		$this->body .= "> url</option>\n\t";
		$this->sql_query = "select * from ".$this->sell_choices_types_table;
		$types_result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$types_result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[5501];
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($types_result->RecordCount() > 0)
		{
			while ($show_type = $types_result->FetchRow())
			{
				//show questions as drop down box
				$this->body .= "<option value=".$show_type["type_id"];
				if ($show_type["type_id"] == $show_question["choices"])
					$this->body .= " selected";
				$this->body .= ">".$show_type["type_name"]."\n\t";
			} //end of while
		}
		$this->body .= "</select> \n\t</td>\n</tr>\n";

		$this->body .= "<tr>\n\t<td valign=top align=right class=medium_font><b>display \"other\" box:</b>".$this->show_tooltip(4,1)."</td>\n\t";
		$this->body .= "<td valign=top class=medium_font><input type=radio name=b[other_input_box] value=1 ";
		if ($show_question["other_input"] == 1)
			$this->body .= " checked ";
		$this->body .= ">yes<br><input type=radio name=b[other_input_box] value=0 ";
		if ($show_question["other_input"] == 0)
			$this->body .= " checked ";
		$this->body .= ">no \n\t</td>\n</tr>\n";
		$this->body .= "<tr class=row_color2>\n\t<td valign=top align=right class=medium_font><b>display order:</b>".$this->show_tooltip(5,1)."</td>\n\t";
		$this->body .= "<td valign=top class=medium_font><select name=b[question_display_order]>\n\t";
		for ($i=0;$i<50;$i++)
		{
			$this->body .= "<option ";
			if ($show_question["display_order"] == $i)
				$this->body .= "selected";
			$this->body .= ">".$i."\n\t";
		} // end of for
		$this->body .= "</select> \n\t</td>\n\t";
		$this->body .= 	"</tr>\n";

		if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center><input type=submit name=action value=\"Save\"> \n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=2><a href=index.php?a=60&b=".$some_group."><span class=medium_font><b>back to ".$group_name." Questions</b></span></a>\n\t</td>\n</tr>\n";
		$this->body .= "</table>";
		return true;

	} //end of function sell_question_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_sell_question($db,$question_id=0,$info=0)
	{
		if (($question_id) && ($info))
		{
			$info["question_reference"] = str_replace(" ","_",$info["question_reference"]);

			$this->sql_query = "UPDATE ".$this->sell_questions_table." set
				name = \"".$info["question_name"]."\",
				explanation = \"".$info["question_explanation"]."\",
				choices = \"".$info["question_choices"]."\",
				other_input = ".$info["other_input_box"].",
				display_order = ".$info["question_display_order"]."
				where question_id = ".$question_id;

			//echo $this->sql_query." is the query<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			return true;
		}
		else
		{
			$this->error_message = $this->messages[5507];
			return false;
		}
	} //end of function update_sell_question

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_sell_question($db,$info=0,$group_id=0)
	{
		if (($info) && ($group_id))
		{
			$this->sql_query = "select name from ".$this->sell_questions_table." where name = \"".$info["question_name"]."\"";
			//echo $this->sql_query." is the query<br>\n";
			$name_result = $db->Execute($this->sql_query);
			if (!$name_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($name_result->RecordCount() == 0)
			{
				//echo $name_result->RecordCount()." is the recordcount<br>\n";

				$this->sql_query = "insert into ".$this->sell_questions_table."
					(group_id, name, explanation, choices, other_input, display_order)
					values
					(".$group_id.", \"".$info["question_name"]."\",
					\"".$info["question_explanation"]."\", \"".$info["question_choices"]."\",
					".$info["other_input_box"].",".$info["question_display_order"]." )";

				//echo $this->sql_query." is the query<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[5501];
					return false;
				}
				return true;
			}
			else
			{
				$this->error_message = $this->messages["5511"];
				return false;
			}
		}
		else
		{
			$this->error_message = $this->messages[5507];
			return false;
		}
	} //end of function insert_sell_question

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_current_questions($db,$group_id)
	{
		$group_name = $this->get_group_name($db,$group_id);
		if (!$group_name)
			return false;

		$this->sql_query = "select * from ".$this->sell_questions_table." where group_id = ".$group_id." order by display_order";
		$result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}

		//display the current quesions attached this category
		$this->title = "Users / User Groups > User Group Questions";
		$this->description = "You can change, edit or remove questions attached to specific groups in this administration.  Group questions appear in the
			listing process.  They provide non required fields to help your sellers better describe, better organize and quickly enter their
			listings into your site.  These questions help your sellers quickly show the most important aspects of their listings.  They also remind them
			of what aspects of their listings their possible buyers are more interested in.  If a seller answers one of these in the listing process the
			value they entered will appear in a separate section of their listing than the description.  This allows possible buyers a quicker way
			to see the most important aspects of products in a certain category.";
		$this->body .= "
			<table border=0 cellpadding=1 cellspacing=1 width=100%>
				<tr bgcolor=000066>
					<td colspan=7 class=large_font_light align=center><b>User Group: ".$group_name."</b></td>
				</tr>
				<tr class=row_color_black>
					<td class=medium_font_light><b>name</b></td>
					<td class=medium_font_light><b>explanation</b></td>
					<td class=medium_font_light><b>question type</b></td>
					<td class=medium_font_light align=center><b>\"other\" box?</b></td>
					<td class=medium_font_light align=center><b>display order</b></td>
					<td class=medium_font_light width=100 align=center><b>edit</b></td>
					<td class=medium_font_light width=100 align=center><b>delete</b></td>
				</tr>";


		if ($result->RecordCount() > 0)
		{
			$this->row_count = 0;
			while ($show_current_questions = $result->FetchRow())
			{
				//show the current questions by row
				$this->body .= "
				<tr class=".$this->get_row_color().">
					<td valign=top class=medium_font>".$show_current_questions["name"]."</td>
					<td valign=top class=medium_font>".$show_current_questions["explanation"]."&nbsp;</td>
					<td valign=top class=medium_font>";
				if ($show_current_questions["choices"] == "none")
					$this->body .= "";
				elseif ($show_current_questions["choices"] == "check")
					$this->body .= "checkbox";
				elseif ($show_current_questions["choices"] == "textarea")
					$this->body .= "blank textarea box";
				elseif ($show_current_questions["choices"] == "url")
					$this->body .= "url";
				else
				{
					$this->sql_query = "select type_name from ".$this->sell_choices_types_table." where type_id = ".$show_current_questions["choices"];
					$choice_result = $db->Execute($this->sql_query);
					if (!$choice_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[5501];
						return false;
					}
					elseif ($choice_result->RecordCount() == 1)
					{
						$show_choice_name = $choice_result->FetchRow();
					}
					else
					{
						return false;
					}
					$this->body .= $show_choice_name["type_name"];
				}
				$this->body .= "&nbsp;</td>
					<td valign=top class=medium_font align=center>";
				if ($show_current_questions["other_input"] == 1)
					$this->body .= "yes";
				else
					$this->body .= "no";
				$this->body .= "&nbsp;</td>
					<td valign=top class=medium_font align=center>".$show_current_questions["display_order"]."&nbsp;</td>
					<td valign=top align=center>
						<a href=index.php?a=61&b=".$show_current_questions["question_id"]."><img src=admin_images/btn_admin_edit.gif alt=edit border=0></a>
					</td>
					<td valign=top align=center>
						<a href=index.php?a=64&b=".$show_current_questions["question_id"]."&c=".$group_id.">
							<img src=admin_images/btn_admin_delete.gif alt=delete border=0>
						</a>
					</td>
				</tr>";
				$this->row_count++;
			}// end of while

		} //end of if
		else
		{
			//say there are no questions in this category
			$this->body .= "
				<tr><td class=medium_error_font colspan=7 align=center><b><br><br>There are no questions attached to this group.<br><br></b></td></tr>";
		}
		$this->body .= "
				<tr><td colspan=100% class=medium_font align=center><br><br><a href=index.php?a=62&b=".$group_id."><b>".$this->messages["5509"]."</b></a><br><br></td></tr>
				<tr><td colspan=100% class=medium_font><a href=index.php?a=32><b>view current Pre-Valued Dropdowns</b></a></td></tr>
				<tr><td colspan=100% class=medium_font><a href=index.php?a=36&b=4&c=".$group_id.">return to <b>".$group_name."</b> group</a></td></tr>
			</table>";
		return true;
	} //end of function show_current_questions

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_all_dropdowns($db)
	{
		$this->sql_query = "select * from ".$this->sell_choices_types_table." order by type_name";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->error_message = $this->messages[5501];
			return false;
		}

		$this->title = "Current Pre-configured Sell Question Dropdown Boxes";
		$this->description = "This is the current list of dropdown box choices
			that can be used by any customized question. <br> - To change a dropdown box click the edit link next to it<br>
			- To delete that dropdown set click delete link next to it<br>
			<b>remember:</b>  these are just dropdown box choices you attach to sell questions that are then displayed with the group
			they are attached to.  These dropdowns will show up as a choice in the \"choices\" category of the add or edit sell question form.
			So create your dropdowns here first then they will then become a choice to attach to a question
			<br><br>All dropdown choice boxes are administered here.";
		$this->body .= "
			<table cellpadding=2 cellspacing=0 border=0 class=row_color1>
				<tr>
					<td>";
		if ($result->RecordCount() > 0)
		{
			$this->body .= "
						<tr class=row_color_black>
							<td colspan=3 class=medium_font_light>
								Current dropdown boxes
							</td>
						</tr>
						<tr class=row_color_black>
							<td class=medium_font_light>name </td>
							<td class=medium_font_light>&nbsp; </td>
							<td class=medium_font_light>&nbsp; </td>
						</tr>";
			$this->row_count = 1;
			while ($show = $result->FetchRow())
			{
				$this->body .= "
						<tr class=".$this->get_row_color().">
							<td colspan=100% class=medium_font>".$show["type_name"]." </td>
							<td><a href=index.php?a=32&c=".$show["type_id"]."><img src=admin_images/btn_admin_edit.gif alt=edit border=0></a></td>
							<td><a href=index.php?a=32&d=".$show["type_id"]."><img src=admin_images/btn_admin_delete.gif alt=delete border=0></a></td>
						</tr>";
				$this->row_count++;
			}
		}
		else
			$this->body .= "
						<tr><td class=medium_font>There are no current dropdowns</td></tr>";
		$this->body .= "
						<tr><td><a href=index.php?a=32&e=1>insert a new dropdown</a></td></tr>
						<tr><td><a href=index.php?a=7>back to category admin</span></a></td></tr>
					<td>
				</tr>
			</table>";
		return true;
	} //end of function show_all_dropdowns

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function new_dropdown_form()
	{
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=32&e=1 method=post>\n";
		$this->body .= "<table cellpadding=2 cellspacing=0 border=0 class=row_color1>\n";
		$this->title = "Add a new Sell question Dropdown Form";
		$this->description = "Use this form to add a new dropdown to
			the dropdowns usable as a question.  Type the name below and click \"enter\".  You will then be able to add values to
			the dropdown you have just created.";
		$this->body .= "<tr>\n\t
			<td align=right class=medium_font>dropdown label: </td>\n\t
			<td class=medium_font><input type=text name=b[dropdown_label] size=35></td>\n</tr>\n";
		if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 align=center><input type=submit name=b[enter] value=\"Save\">\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;
	} //end of function new_dropdown_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_new_dropdown($db,$information=0)
	{
		if ($information)
		{
			if (strlen(trim($information["dropdown_label"])) > 0)
			{
				$this->sql_query = "insert into ".$this->sell_choices_types_table."
					(type_name)
					values
					(\"".$information["dropdown_label"]."\")";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					//echo $this->sql_query."<br>\n";
					return false;
				}
				$id = $db->Insert_ID();
				return $id;
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
	} //end of function insert_new_dropdown

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function edit_dropdown($db,$dropdown_id=0)
	{
		if ($dropdown_id)
		{
			$this->sql_query = "select * from ".$this->sell_choices_types_table." where type_id = ".$dropdown_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				//this dropdown exists
				$show_dropdown = $result->FetchRow();
				$this->sql_query = "select * from ".$this->classified_sell_choices_table." where type_id = ".$dropdown_id." order by display_order";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}
				//show the form to edit this dropdown
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=48&c=".$dropdown_id." method=post>\n";
				$this->body .= "<table cellpadding=2 cellspacing=0 border=0 class=row_color1 width=100%>\n";
				$this->title = "Edit Sell question Dropdown Form";
				$this->description = "Use this form to add or delete values
					appearing in the category question dropdowns.  Insert a new value by typing the value and then choosing a value for
					display order.  The display order value determines the order the values appear in the dropdown.  Otherwise the order is
					alphabetically.";
				$this->body .= "<tr>\n\t<td>\n\t<table cellpadding=2 cellspacing=0 border=0 class=row_color2>\n\t";
				$this->body .= "<tr class=row_color_black>\n\t\t<td class=medium_font_light>\n\tvalue \n\t\t</td>\n\t\t";
				$this->body .= "<td class=medium_font_light>\n\tdisplay order \n\t\t</td>\n\t\t";
				$this->body .= "<td class=medium_font_light>\n\t&nbsp; \n\t\t</td>\n\t</tr>\n\t";
				if ($result->RecordCount() > 0)
				{
					//this dropdown exists
					//show the value in a list
					$this->row_count = 0;
					while ($show = $result->FetchRow())
					{
						$this->body .= "<tr class=".$this->get_row_color().">\n\t\t<td class=medium_font>\n\t".$show["value"]." \n\t\t</td>\n\t\t";
						$this->body .= "<td class=medium_font>\n\t".$show["display_order"]." \n\t\t</td>\n\t\t";
						$this->body .= "<td>\n\t\t<a href=index.php?a=48&g=".$show["value_id"]."&c=".$dropdown_id."><span class=medium_font>delete</span></a>\n\t\t</td>\n\t\t";
						$this->row_count++;
					}
				}
				$this->body .= "<tr>\n\t<td class=medium_font>\n\t<input type=text name=b[value] size=25 maxsize=50> \n\t</td>\n\t
					<td class=medium_font>\n\t<select name=b[display_order]>\n\t\t\t";
				for ($i=1;$i < 51;$i++)
				{
					$this->body .= "<option>".$i."</option>\n\t\t\t";
				}
				$this->body .= "</select> \n\t</td>\n\t";
				if (!$this->admin_demo()) $this->body .= "<td class=medium_font>\n\t<input type=submit name=submit value=\"Save\"> \n\t</td>\n\t</tr>\n\t";
				$this->body .= "</table>\n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color_red>\n\t<td><a href=index.php?a=48><span class=medium_font_light>show all dropdowns</span></a></td>\n</tr>\n";
				$this->body .= "<tr class=row_color_red>\n\t<td><a href=index.php?a=7><span class=medium_font_light>go back to category edit</span></a></td>\n</tr>\n";
				$this->body .= "</td>\n</tr>\n</table>\n";
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
	} //end of function edit_dropdown

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function add_dropdown_value($db,$dropdown_id=0,$information=0)
	{
		if (($information) && ($dropdown_id))
		{
			if (strlen(trim($information["value"])) > 0)
			{
				$this->sql_query = "insert into ".$this->classified_sell_choices_table."
					(type_id,value,display_order)
					values
					(".$dropdown_id.",\"".$information["value"]."\",".$information["display_order"].")";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}
				$id = $db->Insert_ID();
				return $id;
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
	} //end of function add_dropdown_value

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_dropdown_value($db,$value_id=0)
	{
		if ($value_id)
		{
			$this->sql_query = "delete from ".$this->classified_sell_choices_table." where value_id = ".$value_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	} //end of function delete_dropdown_value

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_dropdown_intermediate($db,$dropdown_id=0)
	{
		if ($dropdown_id)
		{
			$this->sql_query = "select * from ".$this->sell_choices_types_table." where type_id = ".$dropdown_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=48&d=".$dropdown_id." method=post>\n";
				$this->body .= "<table cellpadding=2 cellspacing=0 border=0 class=row_color1 width=100%>\n";
				$this->title = "Delete Sell question Dropdown Form (verification)";
				$this->description = "If the sell question dropdown you are trying to delete
					is attached to existing categories you will be given a choice to push those category questions to other dropdowns (if any).
					Or just remove the sell questions attached (if any) to this dropdown as well as the dropdown itself.";
				$show_dropdown = $result->FetchRow();
				$this->sql_query = "select * from ".$this->sell_questions_table." where choices = ".$dropdown_id;
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}
				elseif ($result->RecordCount() > 0)
				{
					//there are sell questions attached to this
					$attached = 1;

					//show attached categories
					$this->body .= "<tr>\n\t<td>\n\t";
					$this->body .= "<table cellpadding=2 cellspacing=0 border=0 class=row_color1>\n";
					$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light>\n\tcategories attached to<br>
						this question dropdown \n\t</td>\n</tr>\n";
					$this->row_count = 1;
					while ($show_categories = $result->FetchRow())
					{
						$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>".$this->get_category_name($db,$show_categories["category_id"])." \n\t</td>\n</tr>\n";
						$this->row_count++;
					}
					$this->body .= "</td>\n\t</tr>\n";

					$this->sql_query = "select * from ".$this->sell_choices_types_table." where type_id = ".$dropdown_id;
					$dropdown_result = $db->Execute($this->sql_query);
					if (!$dropdown_result)
					{
						return false;
					}
					elseif ($dropdown_result->RecordCount() > 0)
					{
						$this->body .= "<tr>\n\t<td class=medium_font>\n\tmove these category sell <br>questions to this dropdown ";
						$this->body .= "<select name=z[new_dropdown]>\n\t\t";
						$this->body .= "<option value=none>choose dropdown</option>\n\t\t";
						while ($show_other = $dropdown_result->FetchRow())
						{
							$this->body .= "<option value=".$show_other["type_id"].">".$show_other["type_name"]."</option>\n\t\t";
						}
						$this->body .= " \n\t</td>\n</tr>\n";
					}
					if (!$this->admin_demo())
					{
						$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light>\n\t<input type=submit name=z[type_of_submit]
							value=\"change and delete\"> \n\t</td>\n</tr>\n";
						$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light>\n\t<input type=submit name=z[type_of_submit]
							value=\"delete all references\"> \n\t</td>\n</tr>\n";
					}
						$this->body .= "</table>\n";
				}
				else
				{
					$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light>\n\t";
					if (!$this->admin_demo()) $this->body .= "<input type=submit name=z[type_of_submit] value=\"delete all references\"> \n\t";
					$this->body .= "</td>\n</tr>\n";
				}
				$this->body .= "</table>\n";

				//show the delete from db (and everywhere else
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
	} //end of function delete_dropdown_intermediate

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_dropdown($db,$dropdown_id=0,$information=0)
	{
		//echo "hello from delete dropdown<br>\n";
		if (($dropdown_id) && ($information))
		{
			//echo $information["type_of_submit"]." delete<br>\n";
			if ($information["type_of_submit"] == "delete all references")
			{
				$this->sql_query = "delete from ".$this->sell_questions_table." where choices = ".$dropdown_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}

				$this->sql_query = "delete from ".$this->classified_sell_choices_table." where type_id = ".$dropdown_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}

				$this->sql_query = "delete from ".$this->sell_choices_types_table." where type_id = ".$dropdown_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}
				return true;
			}
			elseif ($information["type_of_submit"] == "change and delete")
			{
				if ($information["new_dropdown"] != "none")
				{
					$this->sql_query = "update ".$this->sell_questions_table." set
						choices = ".$information["new_dropdown"]."
						where choices = ".$dropdown_id;
					//echo $this->sql_query."<br>\n";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						return false;
					}
				}
				$this->sql_query = "delete from ".$this->classified_sell_choices_table.",".$this->sell_choices_types_table." where type_id = ".$dropdown_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
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
			//echo "not enough info<br>\n";
			return false;
		}
	} //end of function delete_dropdown

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_sell_question($db,$question_id=0)
	{
		if ($question_id)
		{
			$this->sql_query = "delete from ".$this->sell_questions_table." where question_id = ".$question_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	} //end of function delete_dropdown_value

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Admin_category_questions

?>