<? //admin_category_questions_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Admin_category_questions extends Admin_site {

	var $debug_questions = 0;

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
		$this->messages["5505"] = "There are no questions attached to this category.";
		$this->messages["5506"] = "questions attached to the ";
		$this->messages["5507"] = "Not enough information to complete your request";
		$this->messages["5508"] = "The main category is the parent category and has no questions attached to it.";
		$this->messages["5509"] = "Add New Question";
		$this->messages["5510"] = "There are no question types to choose from";
		$this->messages["5511"] = "A question already exists by that name.<br>click the back button and change the name.";

	} //end of function Admin_category_questions

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_if_category($db,$category=0)
	{
		if ($category)
		{
			//check to see if this number is even a category
			$this->sql_query = "select * from ".$this->classified_categories_table." where category_id = ".$category;
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

	function sell_question_form($db,$question_id=0,$category=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"name\", \"The value entered into this blank will appear as the question next to the type of question answer method chosen (ie. blank box, dropdown,...).  It could be a question or a field label, whatever you choose.\"]\n
			Text[2] = [\"explanation\", \"If you feel that your question needs an explanation you can enter a value into this box. If you enter an explanation a question mark will appear next to the question in the sell process.  When the question mark is clicked this explanation will appear in a popup box further explaining how the question helps or should be answered.\"]\n
			Text[3] = [\"choices\", \"The value entered here determines the method this question can be answered. You can leave just a blank box or if you have added pre-valued dropdown boxes, choose one among them.\"]\n
			Text[4] = [\"display \\\"other\\\" box\", \"You can opt to give the seller another box if one of the choices you give in the dropdown box does not fit the product or service they are selling. The other box will only appear if a dropdown box has been chosen in the \\\"choices\\\" field above. If \\\"just blank input box\\\" is selected in the \\\"choices\\\" field above this value will have no effect.\"]\n
			Text[5] = [\"display order\", \"Choose the order in the existing category questions that this question appears in the category question list.\"]\n";

		//".$this->show_tooltip(3,1)."

		// Set style for tooltip
		//$this->body .= "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
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

			$category_name = $this->get_category_name($db,$show_question["category_id"]);
			$some_category = $show_question["category_id"];
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=13&c=".$show_question["question_id"]."&d=".$show_question["category_id"]." method=post>";
			$this->body .= "<table cellpadding=5 border=0 cellspacing=0 width=100% class=row_color1>\n";
			$this->title = "Edit this question for the ".$category_name." category ( ".$show_question["category_id"]." )";
			$this->description = "Edit a category question to this category with the form below.  Edit the fields, click save and your setting will be saved.";
		}
		elseif ($category)
		{
			$category_name = $this->get_category_name($db,$category);
			$some_category = $category;
			//this is a new attached to this category
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=11&c=".$category." method=post>";
			$this->body .= "<table cellpadding=5 border=0 cellspacing=0 width=100% class=row_color1>\n";
			$this->title = "Add a Question to the ".$category_name." category ( ".$category." )";
			$this->description = "Add a category question to this category with the form below.  Fill in the blanks and the question will be added to the
				".$category_name." category";


		}
		else
		{
			$this->error_message = $this->messages["5507"];
			return false;
		}

		//display the current quesions attached this category
		$this->body .= "<tr class=row_color2>\n\t<td valign=top align=right class=medium_font>name:".$this->show_tooltip(1,1)."</td>\n\t";
		$this->body .= "<td valign=top class=medium_font><input type=text name=b[question_name] value=\"".$show_question["name"]."\"></font>\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td valign=top align=right class=medium_font>explanation:".$this->show_tooltip(2,1)."</td>\n\t";
		$this->body .= "<td valign=top class=medium_font><textarea name=b[question_explanation] cols=40 rows=10>".$this->special_chars($show_question["explanation"])."</textarea></font>\n\t</td>\n\t";

		$this->body .= "<tr class=row_color2>\n\t<td valign=top align=right class=medium_font>choices:".$this->show_tooltip(3,1)."</td>\n\t";
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
		$this->body .= "</select></font>\n\t</td>\n</tr>\n";

		$this->body .= "<tr>\n\t<td valign=top align=right class=medium_font>display \"other\" box:".$this->show_tooltip(4,1)."</td>\n\t";
		$this->body .= "<td valign=top class=medium_font><input type=radio name=b[other_input_box] value=1 ";
		if ($show_question["other_input"] == 1)
			$this->body .= " checked ";
		$this->body .= ">yes<br><input type=radio name=b[other_input_box] value=0 ";
		if ($show_question["other_input"] == 0)
			$this->body .= " checked ";
		$this->body .= ">no</font>\n\t</td>\n</tr>\n";
		$this->body .= "<tr class=row_color2>\n\t<td valign=top align=right class=medium_font>display order:".$this->show_tooltip(5,1)."</td>\n\t";
		$this->body .= "<td valign=top class=medium_font><select name=b[question_display_order]>\n\t";
		for ($i=0;$i<50;$i++)
		{
			$this->body .= "<option ";
			if ($show_question["display_order"] == $i)
				$this->body .= "selected";
			$this->body .= ">".$i."\n\t";
		} // end of for
		$this->body .= "</select></font>\n\t</td>\n\t";
		$this->body .= 	"</tr>\n";
		if (!$this->admin_demo())
			$this->body .= "<tr align=center>\n\t<td colspan=2 class=medium_font><input type=submit name=action value=\"Save\"></font>\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=2><a href=index.php?a=8&b=".$some_category."><span class=medium_font>back to ".$category_name." category questions</span></a>\n\t</td>\n</tr>\n";
		$this->body .= "</table>";
		return true;

	} //end of function sell_question_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_sell_question($db,$question_id=0,$info=0)
	{
		if (($question_id) && ($info))
		{
			$info["question_reference"] = str_replace(" ","_",$info["question_reference"]);
			if ($info["question_choices"] == "check")
				$info["other_input_box"] = 0;
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

	function insert_sell_question($db,$info=0,$category_id=0)
	{
		if (($info) && ($category_id))
		{
			//$this->sql_query = "select name from ".$this->sell_questions_table." where name = \"".$info["question_name"]."\" and category_id = ".$category_id;
			//echo $this->sql_query." is the query<br>\n";
			//$name_result = $db->Execute($this->sql_query);
			//if (!$name_result)
			//{
			//	//echo $this->sql_query." is the query<br>\n";
			//	$this->error_message = $this->messages[5501];
			//	return false;
			//}
			//elseif ($name_result->RecordCount() == 0)
			//{
				//echo $name_result->RecordCount()." is the recordcount<br>\n";
				if ($info["question_choices"] == "check")
					$info["other_input_box"] = 0;

				$this->sql_query = "insert into ".$this->sell_questions_table."
					(category_id, name, explanation, choices, other_input, display_order)
					values
					(".$category_id.", \"".$info["question_name"]."\",
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
			//}
			//else
			//{
			//	$this->error_message = $this->messages["5511"];
			//	return false;
			//}
		}
		else
		{
			$this->error_message = $this->messages[5507];
			return false;
		}
	} //end of function insert_sell_question

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_current_questions($db,$category)
	{
		$category_name = $this->get_category_name($db,$category);
		$this->sql_query = "select parent_id from ".$this->classified_categories_table." where category_id = ".$category;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show_name = $result->FetchRow();
		}
		else
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}

		$this->sql_query = "select * from ".$this->sell_questions_table." where category_id = ".$category." order by display_order";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}

		//display the current quesions attached this category
		$this->body .= "<table cellpadding=5 border=0 cellpadding=2 cellspacing=1 width=100%>\n";
		$this->title = "Categories Setup > Edit Category Questions";
		$this->description = "This page allows you to designate specific questions to be asked of the seller when they place an listing in this category. These same fields will
		also display on the \"advanced search page\" as searchable criteria when a site visitor selects this particular category to search for an item.";
		$this->body .= "<tr bgcolor=000066>\n\t<td colspan=7 class=medium_font_light><b>".$this->messages["5506"].$category_name." category</b> ( ".$category." )</td>\n</tr>\n";
		$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light><b>name</b></font>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light><b>explanation</b></font>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light><b>question type</b></font>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light align=center><b>\"other\" box?</b></font></td>\n";
		$this->body .= "<td class=medium_font_light align=center><b>display order</b></font>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light align=center><b>edit</b></font></td>\n\t";
		$this->body .= "<td class=medium_font_light align=center><b>delete</b></font></td>\n</tr>\n";

		if ($result->RecordCount() > 0)
		{
			$this->row_count = 0;
			while ($show_current_questions = $result->FetchRow())
			{
				//show the current questions by row
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top class=medium_font>\n\t".$show_current_questions["name"]."(".$show_current_questions["question_id"].")\n\t</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t".$show_current_questions["explanation"]."\n\t&nbsp;</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t";
				if ($show_current_questions["choices"] == "none")
				{
					$this->body .= "";
				}
				elseif ($show_current_questions["choices"] == "check")
				{
					$this->body .= "checkbox";
				}
				elseif ($show_current_questions["choices"] == "textarea")
				{
					$this->body .= "blank textarea box";
				}
				elseif ($show_current_questions["choices"] == "url")
				{
					$this->body .= "url";
				}
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

				$this->body .= "&nbsp;\n\t</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t";
				if ($show_current_questions["other_input"] == 1)
					$this->body .= "yes";
				else
					$this->body .= "no";
				$this->body .= "&nbsp;\n\t</td>\n\t";
				$this->body .= "<td valign=top class=medium_font align=center>\n\t".$show_current_questions["display_order"]."&nbsp;\n\t</td>\n\t";
				$this->body .= "<td valign=top align=center><a href=index.php?a=9&b=".$show_current_questions["question_id"]."><span class=medium_font>edit</span></a>\n\t</td>\n\t";
				$this->body .= "<td valign=top align=center><a href=index.php?a=12&b=".$show_current_questions["question_id"]."&c=".$category."><span class=medium_font>delete</span></a>\n\t</td>\n\t";
				$this->body .= "</tr>";
				$this->row_count++;
			}// end of while

		} //end of if
		else
		{
			//say there are no questions in this category
			$this->body .= "<tr>\n\t<td colspan=8 align=center class=medium_font>\n\t<br><br><b>".$this->messages["5505"]."</b><br><br><br>\n\t</td>\n</tr>\n";
		}
		$this->body .= "<tr>\n\t<td colspan=8 align=center>\n\t<a href=index.php?a=10&b=".$category."><span class=medium_font><b><font color=000000>".$this->messages["5509"]."</font></b></span></a>\n\t</td>\n</tr>\n";
		$this->body .= "</table><br>";

		//get inherited questions
		$parent_id = $show_name["parent_id"];
		$this->body .= "<table cellpadding=5 border=0 cellpadding=2 cellspacing=1 width=100%>\n
			<tr bgcolor=000066>\n\t<td colspan=8 class=medium_font_light>
			<b>questions inherited from the categories above ".$category_name." category </b>( ".$category." )</font>\n\t</td>\n</tr>\n";
		$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light><b>name</b></font>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light><b>explanation</b></font>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light><b>question type</b></font>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light align=center><b>\"other\" box?</b></font></td>\n";
		$this->body .= "<td class=medium_font_light align=center><b>display order</b></font>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light align=center><b>edit</b></font></td>\n\t";
		$this->body .= "<td class=medium_font_light align=center><b>delete</b></font></td>\n</tr>\n";
		if ($parent_id != 0)
		{
			while ($parent_id != 0)
			{
				$parent_category_name = $this->get_category_name($db,$parent_id);
				$this->sql_query = "select parent_id from ".$this->classified_categories_table." where category_id = ".$parent_id;
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[5501];
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show_category = $result->FetchRow();
					$this->body .= "<tr class=row_color2>\n\t<td colspan=7>\n\tquestions in the ".$parent_category_name." category ( ".$parent_id." )\n\t</td>\n</tr>\n";
					$this->sql_query = "select * from ".$this->sell_questions_table." where category_id = ".$parent_id;
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[5501];
						return false;
					}
					elseif ($result->RecordCount() > 0)
					{
						$this->row_count = 0;
						$css_tag = $this->get_row_color(2);
						while ($show_current_questions = $result->FetchRow())
						{
							//show the current questions by row
							$css_tag = $this->get_row_color(2);
							$this->body .= "<tr class=".$css_tag.">\n\t<td class=medium_font>".$show_current_questions["name"]."</font>\n\t</td>\n\t";
							$this->body .= "<td class=medium_font>".$show_current_questions["explanation"]."\n\t&nbsp;</font></td>\n\t";
							$this->body .= "<td class=medium_font>".$show_current_questions["choices"]."&nbsp;</font>\n\t</td>\n\t";
							$this->body .= "<td class=medium_font>";
							if ($show_current_questions["other_input"] == 1)
								$this->body .= "yes";
							else
								$this->body .= "no";
							$this->body .= "&nbsp;\n\t</td>\n\t";
							$this->body .= "<td class=medium_font align=center>".$show_current_questions["display_order"]."&nbsp;</font>\n\t</td>\n\t";
							$this->body .= "<td align=center><a href=index.php?a=9&b=".$show_current_questions["question_id"]."&terminal_category=".$category."><span class=medium_font>edit</span></a>\n\t</td>\n\t";
							$this->body .= "<td align=center><a href=index.php?a=12&b=".$show_current_questions["question_id"]."&c=".$category."><span class=medium_font>delete</span></a>\n\t</td>\n\t";
							$this->body .= "</tr>";
							$this->row_count++;
						}// end of while
					}
					else
					{
						$this->body .= "<tr>\n\t<td colspan=8 class=medium_font>".$this->messages["5505"]."</td>\n</tr>\n";
					}
					$parent_id = $show_category["parent_id"];
				}
				else
				{
					//$this->body .= $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[5501];
					return false;
				}

			}//end of while
		}
		else
		{
			//this is a subcategory of main and can inherit no questions from main
			$this->body .= "<tr>\n\t<td colspan=8 class=medium_font align=center><br><br><b>".$this->messages["5508"]."</font></b><br><br><br>\n\t</td>\n</tr>\n";
		}

		$this->body .= "<tr>\n\t<td colspan=8>\n\t<a href=index.php?a=32><span class=medium_font><font color=000000><b>view current Pre-Valued Dropdowns</b></font></span></a>\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=8>\n\t<a href=index.php?a=";
		if ($this->classauctions)
			$this->body .= "1007";
		else
			$this->body .= "7";
		$this->body .= "&b=".$category."><span class=medium_font><font color=000000><b>back to ".$category_name." category</b></font></span></a>\n\t</td>\n</tr>\n";

		$this->body .= "</table>\n";
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

		$this->title = "Listing Setup > Pre-Valued Dropdowns";
		$this->description = "This is the current list of Pre-Valued Dropdown choices that can be used (assigned to) any customized category question,
		which is presented to the seller during the Listing process.
		First, create your dropdowns using the form below. These dropdowns will then appear as choices when you set up your \"category questions\".
		Do not confuse these Pre-Valued Dropdowns with Registration Pre-Valued Dropdowns, which are used only for Registration Questions.";
		$this->body .= "
			<table width=450 cellpadding=2 cellspacing=1 border=0 class=row_color1>";
		if ($result->RecordCount() > 0)
		{
			$this->body .= "
				<tr bgcolor=000066><td class=medium_font_light align=center colspan=3><b>Current Pre-Valued Dropdowns</b></td></tr>
				<tr class=row_color_black>
					<td class=medium_font_light><b>dropdown name</b></font></td>
					<td class=medium_font_light align=center>&nbsp;</font></td>
					<td class=medium_font_light align=center>&nbsp;</font></td>
				</tr>";
			$this->row_count = 1;
			while ($show = $result->FetchRow())
			{
				$this->body .= "
				<tr class=".$this->get_row_color().">
					<td class=medium_font>".$show["type_name"]."</td>
					<td align=center width=80><a href=index.php?a=32&c=".$show["type_id"]."><img src=admin_images/btn_admin_edit.gif alt=edit border=0></a></td>
					<td align=center width=80><a href=index.php?a=32&d=".$show["type_id"]."><img src=admin_images/btn_admin_delete.gif alt=delete border=0></a></td>
				</tr>";
				$this->row_count++;
			}
		}
		else
		{
			$this->body .= "<tr>\n\t<td class=medium_font align=center><b>There are currently no Pre-Valued Dropdowns.<br><br><br></b></font>\n\t</td>\n</tr>\n";
		}
		$this->body .= "<tr>\n\t<td align=center colspan=3><a href=index.php?a=32&e=1><span class=medium_font><font color=000000><b>Add New Pre-Valued Dropdown</b></font></span></a>\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td align=center colspan=3><a href=index.php?a=";
		if ($this->classauctions)
			$this->body .= "1007";
		else
			$this->body .= "7";
		$this->body .= "><span class=medium_font><br><br><font color=000000><b>back to Categories Setup</b></font></span></a>\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		return true;
	} //end of function show_all_dropdowns

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function new_dropdown_form()
	{
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=32&e=1 method=post>\n";
		$this->body .= "<table cellpadding=2 cellspacing=0 border=0 class=row_color1>\n";
		$this->title = "Listing Setup > Pre-Valued Dropdowns > New";
		$this->description = "Use the form to create a new dropdown that can be used as a choice when you create category
		questions on a category by category basis. The next step will then allow you to add values to the dropdown you have
		just created.";
		$this->body .= "<tr>\n\t
			<td align=right class=medium_font>dropdown label:</font></td>\n\t
			<td class=medium_font><input type=text name=b[dropdown_label] size=35></td>\n</tr>\n";
		if (!$this->admin_demo())
			$this->body .= "<tr align=center>\n\t<td colspan=2><input type=submit name=b[enter] value=\"Save\">\n\t</td>\n</tr>\n";
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
				$this->sql_query = "insert into ".$this->sell_choices_types_table."
					(type_name)
					values
					(\"".$information["dropdown_label"]."\")";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					echo $this->sql_query."<br>\n";
					return false;
				}
				$id = $db->Insert_ID();
				return $id;
				//return false;
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
				$this->sql_query = "select * from ".$this->classified_sell_choices_table." where type_id = ".$dropdown_id." order by display_order,value";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}
				//show the form to edit this dropdown
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=32&c=".$dropdown_id." method=post>\n";
				$this->body .= "<table cellpadding=2 cellspacing=0 border=0 class=row_color1 width=100%>\n";
				$this->title = "Listing Setup > Pre-Valued Dropdowns > Edit Dropdown";
				$this->description = "Use this form to add or delete values
					appearing in this Pre-Valued Dropdown.  Insert a new value by typing the value and then choosing a display order.  The display order determines
					the order the value will appear in the dropdown.  Otherwise the order is alphabetically.";
				$this->body .= "<tr>\n\t<td align=center>\n\t<table width=450 cellpadding=2 cellspacing=1 border=0 class=row_color2>\n\t";
				$this->body .= "<tr bgcolor=000066><td class=medium_font_light align=center colspan=3><b>Dropdown Values</b></td></tr>\n\t";
				$this->body .= "<tr class=row_color_black>\n\t\t<td class=medium_font_light>\n\t<b> dropdown value</b></font>\n\t\t</td>\n\t\t";
				$this->body .= "<td class=medium_font_light align=center>\n\t<b>display order</b></font>\n\t\t</td>\n\t\t";
				$this->body .= "<td class=medium_font_light align=center>\n\t&nbsp;</font>\n\t\t</td>\n\t</tr>\n\t";
				if ($result->RecordCount() > 0)
				{
					//this dropdown exists
					//show the value in a list
					$this->row_count = 0;
					while ($show = $result->FetchRow())
					{
						$this->body .= "<tr class=".$this->get_row_color().">\n\t\t<td class=medium_font>\n\t".$show["value"]."</font>\n\t\t</td>\n\t\t";
						$this->body .= "<td class=medium_font align=center width=120>\n\t".$show["display_order"]."</font>\n\t\t</td>\n\t\t";
						$this->body .= "<td align=center width=80>\n\t\t<a href=index.php?a=32&g=".$show["value_id"]."&c=".$dropdown_id."><img src=admin_images/btn_admin_delete.gif alt=delete border=0></a>\n\t\t</td>\n\t\t";
						$this->row_count++;
					}
				}
				$this->body .= "<tr>\n\t<td class=medium_font>\n\t<input type=text name=b[value] size=25 maxsize=50></font>\n\t</td>\n\t
					<td class=medium_font align=center>\n\t<select name=b[display_order]>\n\t\t\t";
				for ($i=1;$i < 151;$i++)
				{
					$this->body .= "<option>".$i."</option>\n\t\t\t";
				}
				$this->body .= "</select></font>\n\t</td>\n\t
					<td class=medium_font align=center>\n\t";
				if (!$this->admin_demo())
					$this->body .= "<input type=submit name=submit value=\"Save\">";
				$this->body .= "\n\t</td>\n\t</tr>\n\t";
				$this->body .= "</table>\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td class=medium_font><a href=index.php?a=32><br><br><b><font color=000000>back to Pre-Valued Dropdown Choices</font></b></a></td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td class=medium_font><a href=index.php?a=";
				if ($this->classauctions)
					$this->body .= "1007";
				else
					$this->body .= "7";
				$this->body .= "><b><font color=000000>back to Categories Setup</font></b></a></td>\n</tr>\n";
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
		if ($this->debug_questions)
		{
			echo "<BR><BR>TOP OF ADD_DROPDOWN_VALUE<BR>";
			echo $dropdown_id." is dropdown_id<br>\n";
			echo $information["value"]." is new value to add<bR>\n";
		}
		if (($information) && ($dropdown_id))
		{
			if (strlen(trim($information["value"])) > 0)
			{
				$this->sql_query = "insert into ".$this->classified_sell_choices_table."
					(type_id,value,display_order)
					values
					(".$dropdown_id.",\"".$information["value"]."\",".$information["display_order"].")";
				$result = $db->Execute($this->sql_query);
				if ($this->debug_questions) echo $this->sql_query." is 1<bR>\n";
				if (!$result)
				{
					if ($this->debug_questions) echo $this->sql_query."<bR>\n";
					return false;
				}
				$id = $db->Insert_ID();
				return $id;
			}
			else
			{
				$this->sql_query = "insert into ".$this->classified_sell_choices_table."
					(type_id,value,display_order)
					values
					(".$dropdown_id.",\"".$information["value"]."\",".$information["display_order"].")";
				$result = $db->Execute($this->sql_query);
				if ($this->debug_questions) echo $this->sql_query." is 2<bR>\n";
				if (!$result)
				{
					if ($this->debug_questions) echo $this->sql_query."<bR>\n";
					return false;
				}
				$id = $db->Insert_ID();
				return $id;
				//return false;
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
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=32&d=".$dropdown_id." method=post>\n";
				$this->body .= "<table cellpadding=2 cellspacing=0 border=0 class=row_color1 width=100%>\n";
				$this->title .= "Listing Setup > Pre-Valued Dropdowns > Delete";
				$this->description .= "If the sell question dropdown you are trying to delete
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
						this question dropdown</font>\n\t</td>\n</tr>\n";
					$this->row_count = 1;
					while ($show_categories = $result->FetchRow())
					{
						$current_category_name = $this->get_category_name($db,$show_categories["category_id"]);
						$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t".$current_category_name."</font>\n\t</td>\n</tr>\n";
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
						$this->body .= "</font>\n\t</td>\n</tr>\n";
					}
					if (!$this->admin_demo())
					{
						$this->body .= "<tr>\n\t<td class=medium_font align=center>\n\t<input type=submit name=z[type_of_submit]
							value=\"change and delete\"></font>\n\t</td>\n</tr>\n";
						$this->body .= "<tr>\n\t<td class=medium_font align=center>\n\t<input type=submit name=z[type_of_submit]
							value=\"delete\"></font>\n\t</td>\n</tr>\n";
					}
					$this->body .= "</table>\n";
				}
				else
				{
					$this->body .= "<tr>\n\t<td class=medium_font align=center>\n\t";
					if (!$this->admin_demo())
						$this->body .= "<input type=submit name=z[type_of_submit] value=\"delete all references\"></font>\n\t";
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