<?// admin_text_management_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Font_management extends Admin_site {

	var $internal_error_message = "There was an internal error";
	var $data_error_message = "Not enough data to complete request";
	var $page_text_error_message = "No text connected to this page";
	var $no_pages_message = "No pages to list";
	var $no_messages_on_page_message = "There are no messages attached to this page.";

	var $text_management_title_message = "Site Font Management";
	var $text_management_instruction_message = "";

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Text_management($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);

	} //end of function Text_management

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_page_fonts($db,$page_id=0)
	{
		if ($page_id)
		{
			include ("../config.php");
			$page = $this->get_page($db,$page_id);

			$this->body .= "<table cellpadding=3 cellspacing=1 border=0>\n";
			$this->title = "Font Management";
			$this->description = "CSS elements and subpages attached to the <b>".$page["name"]."</b>";

			//display link back to page
			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=3>\n\t<a href=index.php?a=44&z=3&&b=".$page_id.">
				<span class=medium_font_light><font face=arial,helvetica size=2 color=#FFFFFF><b>back to ".$page["name"]." </b></font></span></a>\n\t</td>\n</tr>\n";

			//get current pages messages
			$this->sql_query = "select * from ".$this->pages_fonts_table." where
				page_id = ".$page_id." order by display_order";
			$page_result = $db->Execute($this->sql_query);
			if (!$page_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($page_result->RecordCount() > 0)
			{
				$this->row_count = 0;
				$this->body .= "<tr class=row_color_black>\n\t<td align=center class=medium_font_light><b>CSS element label and explanation</b></font>\n\t</td>\n\t";
				$this->body .= "<td align=center class=medium_font_light><b>css tag used</b> </font>\n\t</td>\n\t";
				$this->body .= "<td class=medium_font_light>\n\t</font>\n\t</td>\n</tr>\n";
				while ($show_page_messages = $page_result->FetchRow())
				{
					//get the current value of this message within this language
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=45% class=medium_font>\n\t<b>";
					$this->body .= urldecode($show_page_messages["name"])."</b></font><br><span class=small_font>".urldecode($show_page_messages["description"])."</span></td>\n\t";
					$this->body .= "<td valign=top width=45% valign=top>\n\t<span class=medium_font>".urldecode($show_page_messages["element"])."</span>\n\t</td>\n\t";
					$this->body .= "<td valign=top width=10%>\n\t<a href=index.php?a=38&b=".$page_id."&c=".$show_page_messages["element_id"].
						"><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></a>\n\t</td>\n</tr>\n";
					$this->row_count++;
					//remove before release
				}


			}
			else
			{
				//echo "<table cellpadding=3 cellspacing=1 border=0>\n";
				$this->body .= "<tr>\n\t<td colspan=3 class=medium_font align=center><b><br><br><br>There are no CSS elements associated with this page.<br><br><br><br></b></td>\n</tr>\n";
				//echo "</table>\n";
			}

			//display link back to page
			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=3>\n\t<a href=index.php?a=44&z=3&&b=".$page_id.">
				<span class=medium_font_light><font face=arial,helvetica size=2 color=#FFFFFF><b>back to ".$page["name"]." </b></font></span></a>\n\t</td>\n</tr>\n";
			if ($iamdeveloper)
			{
				$this->body .= "<tr>\n\t<td colspan=3><a href=index.php?a=103&b=".$page_id."><span class=medium_font>
					click to add message to this page</font></a> - remove before release - DO NOT USE _ YOU WILL CAUSE PROBLEMS IN THE DATABASE\n\t</span></td>\n</tr>\n";
			}
			$this->body .= "</table>\n";
			return true;
		}
		else
		{
			$this->error_message = $internal_error_message;
			return false;
		}

	} //end of function display_page_fonts

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function edit_font_element($db,$element_id=0,$page_id=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"decoration\", \"This removes or places (underline) special decoration that the browser may place on text - for example links may be a different color and underlined by the browser where it leaves normal text alone.  Choosing \\\"none\\\" in this field will make the browser remove the special decoration from the link text.\"]\n
			Text[2] = [\"background color\", \"This places a background color around the text, table data box or table row (most cases) that the text appears on. Many combinations of colors can be achieved with this control.\"]\n
			Text[3] = [\"background image\", \"This places a background image around the text, table data box or table row (most cases) where this text appears.\"]\n
			Text[4] = [\"horizontal text align\", \"This is the horizontal alignment of the text within the area it appears.\"]\n
			Text[5] = [\"vertical text align\", \"This is the vertical alignment of the text within the area it appears.\"]\n";

		//".$this->show_tooltip(5,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		if (($element_id) && ($page_id))
		{
			$this->sql_query = "select * from ".$this->pages_fonts_table." where
				element_id = ".$element_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$page = $this->get_page($db,$page_id);
				$text = $result->FetchRow();
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=38&b=".$page_id."&c=".$element_id." method=post>\n";
				$this->body .= "<table cellpadding=2 cellspacing=0 border=0 class=row_color1>\n";
				//$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=very_large_font_light><b>".$this->text_management_title_message."</b></font></td>\n</tr>\n";
				$this->title = "CSS Element Edit";
				$this->description = "Edit this CSS tag in this form.  Below are all the elements of the
					tag you can edit.  Many of the css tags modify the table row or table data tag that the text is in.  This can allow you
					to modify the background colors of that element when possible.";
				$this->body .= "<tr class=row_color1><td align=right class=medium_font>color chart:</font></td><td>";
				$this->body .= "<a href=\"javascript:win('colorcodes.html','500','400')\"><span class=medium_font><img src=admin_images/btn_admin_colors.gif alt=colors border=0></span></a>";
				$this->body .= "</td>\n</tr>\n";
				$this->body .= "<tr class=row_color2><td align=right valign=top class=medium_font>Page Attached To: </td><td class=medium_font><b>".$page["name"]."</b></td></tr>";
				$this->body .= "<tr class=row_color1><td align=right valign=top class=medium_font>Element Name: </td><td class=medium_font><b>".stripslashes(urldecode($text["name"]))."</b></td></tr>";
				$this->body .= "<tr class=row_color2><td align=right valign=top class=medium_font>Element Description: </td><td class=medium_font><b>".stripslashes(urldecode($text["description"]))."</b></td></tr>";
				$this->body .= "<tr class=row_color1><td align=right valign=top class=medium_font>Element CSS Tag: </td><td class=medium_font><b>".stripslashes(urldecode($text["element"]))."</b></td></tr>";

				$this->body .= "<tr class=row_color2><td align=right class=medium_font>font family:</font></td><td>";
				$this->display_font_type_select($db,"z[font_family]",$text["font_family"]);
				$this->body .= "</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\tsize:</td>\n\t<td>\n\t";
				$this->body .= $this->display_font_size_select($db,"z[font_size]",$text["font_size"]);
				$this->body .= "</td>\n</tr>\n";
				$this->body .= "<tr class=row_color2>\n\t<td align=right class=medium_font>\n\tstyle:</td>\n\t<td>\n\t";
				$this->display_font_style_select($db,"z[font_style]",$text["font_style"]);
				$this->body .= "</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\tweight:";
				$this->body .= "</td>\n\t<td valign=top>\n\t";
				$this->body .= $this->display_font_weight_select($db,"z[font_weight]",$text["font_weight"]);
				$this->body .= "</td>\n</tr>\n";
				$this->body .= "<tr class=row_color2>\n\t<td align=right class=medium_font>\n\ttext color:";
				$this->body .= "</td>\n\t<td valign=top>\n\t
					<input type=text name=z[color] value=\"".$text["color"]."\"></td>\n</tr>\n";

				$this->body .= "<tr>\n\t<td align=right width=45% class=medium_font>\n\tdecoration:".$this->show_tooltip(1,1);
				$this->body .= "</td>\n\t<td valign=top>\n\t";
				$this->body .= $this->display_font_decoration_select($db,"z[text_decoration]",$text["text_decoration"]);
				$this->body .= "</td>\n</tr>\n";

				$this->body .= "<tr class=row_color2>\n\t<td align=right width=45% class=medium_font>\n\tbackground color:".$this->show_tooltip(2,1);
				$this->body .= "</td>\n\t<td valign=top>\n\t";
				$this->body .= "<input type=text name=z[background_color] value=\"".$text["background_color"]."\">";
				$this->body .= "</td>\n</tr>\n";

				$this->body .= "<tr>\n\t<td align=right width=45% class=medium_font>\n\tbackground image:".$this->show_tooltip(3,1);
				$this->body .= "</td>\n\t<td valign=top>\n\t";
				$this->body .= "<input type=text name=z[background_image] value=\"".$text["background_image"]."\">";
				$this->body .= "</td>\n</tr>\n";

				$this->body .= "<tr class=row_color2>\n\t<td align=right width=45% class=medium_font>\n\thorizontal text align:".$this->show_tooltip(4,1);
				$this->body .= "</td>\n\t<td valign=top>\n\t";
				$this->body .= $this->display_text_align_select($db,"z[text_align]",$text["text_align"]);
				$this->body .= "</td>\n</tr>\n";

				$this->body .= "<tr class=row_color1>\n\t<td align=right width=45% class=medium_font>\n\tvertical text align:".$this->show_tooltip(5,1);
				$this->body .= "</td>\n\t<td valign=top>\n\t";
				$this->body .= $this->display_text_vertical_align_select($db,"z[text_vertical_align]",$text["text_vertical_align"]);
				$this->body .= "</td>\n</tr>\n";

				if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td align=center colspan=2><input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td colspan=2><a href=index.php?a=38&b=".$page_id."><span class=medium_font><b>back to ".$page["name"]." css style section</b></span></a>\n\t</td>\n</tr>\n";

				//echo "<tr>\n\t<td colspan=2><a href=index.php?a=103&d=".$text["element_id"]."><span class=medium_font>edit name and description</span></a> - remove before release - DO NOT USE _ YOU WILL CAUSE PROBLEMS IN THE DATABASE\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n</form>\n";
			}
			else
			{
				$this->body .= "<table cellpadding=3 cellspacing=1 border=0>\n";
				$this->body .= "<tr>\n\t<td class=very_large_font><b>".$this->text_management_title_message."</b></font></td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td class=medium_font>There are no messages for this language</font></td>\n</tr>\n";
				$this->body .= "</table>\n";
			}
			return true;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function edit_font_element

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_font_element($db,$text_page_id=0,$element_id=0,$text_info=0)
	{
		//echo $element_id." is element_id<br>\n";
		//echo $text_page_id." is text_page_id<br>\n";
		//echo $text_info." is text_info<br>\n";
		if (($element_id) && ($text_page_id) && ($text_info))
		{
			$this->sql_query = "update ".$this->pages_fonts_table." set
				font_family = \"".$text_info["font_family"]."\",
				font_size = \"".$text_info["font_size"]."\",
				font_style = \"".$text_info["font_style"]."\",
				font_weight = \"".$text_info["font_weight"]."\",
				color = \"".$text_info["color"]."\",
				text_decoration = \"".$text_info["text_decoration"]."\",
				background_color = \"".$text_info["background_color"]."\",
				text_align = \"".$text_info["text_align"]."\",
				text_vertical_align = \"".$text_info["text_vertical_align"]."\",
				text_transform = \"".$text_info["text_transform"]."\",
				background_image = \"".$text_info["background_image"]."\"
				where element_id = ".$element_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			//echo "whatever";
			return false;
		}
	} //end of function update_font_element

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//remove before release
	function new_message_form($db,$page_id=0)
	{
		if ($page_id)
		{
			$page = $this->get_page($db,$page_id);
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=103&b=".$page_id." method=post>\n";
			$this->body .= "<table cellpadding=2 cellspacing=1 border=1>\n";
			$this->body .= "<tr>\n\t<td align=right width=30% class=medium_font>\n\telement ( no spaces-only underscores)</font>\n\t</td>\n\t";
			$this->body .= "<td width=70% class=medium_font>\n\t<input type=text name=c[element] size=50></font>\n\t</td>\n</tr>\n";

			$this->body .= "<tr>\n\t<td align=right width=30% class=medium_font>\n\tname</font>\n\t</td>\n\t";
			$this->body .= "<td width=70% class=medium_font>\n\t<input type=text name=c[name] size=50></font>\n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\tdescription</font>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\t<input type=text name=c[description] size=50></font>\n\t</td>\n</tr>\n";

			$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\twill belong to the </font>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>".$page["name"]." page</font>\n\t</td>\n</tr>\n";

			$this->body .= "<tr>\n\t<td valign=top class=medium_font>\n\tdisplay order</font>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\t<select name=c[display_order]>\n\t\t";
			for ($i=1;$i<100;$i++)
			{
				$this->body .= "<option ";
				if ($text["display_order"] == $i)
					$this->body .= " selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select></font>\n\t</td>\n</tr>\n";

			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td align=center colspan=2><input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td align=center colspan=2 class=medium_font><a href=index.php?a=38&b=".$page_id.">back to ".$page["name"]."</a>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n</form>\n";
			return true;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function new_message_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//remove before release
	function insert_new_message($db,$page_id=0,$information=0)
	{
		if (($page_id) && ($information))
		{
			$this->sql_query = "insert into ".$this->pages_fonts_table."
				(page_id,element,name,description,display_order)
				values
				(".$page_id.",\"".$information["element"]."\",\"".$information["name"]."\",
				\"".$information["description"]."\",\"".$information["display_order"]."\")";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}

			$font_id = $db->Insert_ID();

			echo "array (".$font_id.", ".$page_id.", '".$information["element"]."', '".$information["name"]."', '', '', 0, '1', '', '', '', '', '', '', 1, '', '')<br>";

			return true;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function new_message_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

//remove before release
	function update_subpage_message_name_and_description($db,$subpage_id=0,$information=0)
	{
		//echo $element_id." in update_message_name_and_description<br>\n";
		if (($subpage_id) && ($information))
		{

			$this->sql_query = "update ".$this->font_sub_page_table." set
				name = \"".$information["name"]."\",
				explanation = \"".$information["explanation"]."\"
				where sub_page_id = ".$subpage_id;
			//echo $this->sql_query."<bR>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;

		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_subpage_message_name_and_description

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//remove before release

	function name_and_description_form($db,$element_id=0)
	{
		if ($element_id)
		{
			$this->sql_query = "select * from ".$this->pages_fonts_table." where element_id = ".$element_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$text = $result->FetchRow();
				$page = $this->get_page($db,$text["page_id"]);
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=103&d=".$element_id." method=post>\n";
				$this->body .= "<table cellpadding=2 cellspacing=1 border=0>\n";
				$this->body .= "<tr>\n\t<td class=medium_font>\n\tname</font>\n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t<input type=text name=e[name] size=50
					value=\"".urldecode($text["name"])."\"></font>\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td valign=top class=medium_font>\n\tdescription</font>\n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t
					<textarea name=e[description] cols=50 rows=20>".$this->special_chars(urldecode($text["description"]))."</textarea>
					</font>\n\t</td>\n</tr>\n";

				$this->body .= "<tr>\n\t<td valign=top class=medium_font>\n\ttag</font>\n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t
					<input type=text name=e[element] size=50 value=\"".urldecode($text["element"])."\">
					</font>\n\t</td>\n</tr>\n";

				$this->body .= "<tr>\n\t<td valign=top class=medium_font>\n\tbelongs to </font>\n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t<select name=e[page_id]>\n\t\t";
				$this->sql_query = "select * from ".$this->pages_table;
				$page_result = $db->Execute($this->sql_query);
				if (!$page_result)
				{
					$this->error_message = $this->internal_error_message;
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($page_result->RecordCount() > 0)
				{
					while ($show_page = $page_result->FetchRow())
					{
						$this->body .= "<option value=".$show_page["page_id"];
						if ($show_page["page_id"] == $text["page_id"])
							$this->body .= " selected";
						$this->body .= ">".$show_page["name"]."</option>\n\t\t";
					}
				}
				$this->body .= "</select></font>\n\t</td>\n</tr>\n";

				$this->body .= "<tr>\n\t<td valign=top class=medium_font>\n\tdisplay order</font>\n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t<select name=e[display_order]>\n\t\t";
				for ($i=1;$i<100;$i++)
				{
					$this->body .= "<option ";
						if ($text["display_order"] == $i)
							$this->body .= " selected";
						$this->body .= ">".$i."</option>\n\t\t";
				}
				$this->body .= "</select></font>\n\t</td>\n</tr>\n";

				$this->body .= "</td>\n</tr>\n";
				if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td align=center colspan=2><input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td align=center colspan=2 class=medium_font><a href=index.php?a=38&b=".$text["page_id"].">back to ".$page["name"]."</a>\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td align=center colspan=2 class=medium_font><a href=index.php?a=38&b=".$text["page_id"]."&t=".$element_id.">delete this css tag</a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n</form>\n";
			}
			else
			{
				$this->body .= "nothing returned<br>\n";
			}
				return true;
		}
		else
		{
			$this->body .= "no text id";
			return false;
		}
	} //end of function name_and_description_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//remove before release
	function update_message_name_and_description($db,$element_id=0,$information=0)
	{
		//echo $element_id." in update_message_name_and_description<br>\n";
		if (($element_id) && ($information))
		{

			$this->sql_query = "update ".$this->pages_fonts_table." set
				name = \"".$information["name"]."\",
				page_id= ".$information["page_id"].",
				description = \"".$information["description"]."\",
				element = \"".$information["element"]."\",
				display_order = \"".$information["display_order"]."\"
				where element_id = ".$element_id;
			//echo $this->sql_query."<bR>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;

		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_message_name_and_description

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_font_element($db,$element_id=0)
	{
		//echo $text_id." in update_message_name_and_description<br>\n";
		if ($element_id)
		{
			$this->sql_query = "delete from ".$this->pages_fonts_table."
				where element_id = ".$element_id;
			//echo $this->sql_query."<bR>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_message_name_and_description

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_font_page_name($db,$page_id=0)
	{
		if ($page_id)
		{
			$this->sql_query = "select page_name from ".$this->font_page_table." where text_page_id = ".$page_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
				return urldecode($show["page_name"]);
			}
			else
			{
				return "no name";
			}
		}
		else
		{
			return "no name";
		}
	} //end of function get_font_page_name

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
} //end of class Font_management
?>
