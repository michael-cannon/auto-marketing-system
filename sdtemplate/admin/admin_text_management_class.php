<?// admin_text_management_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Text_management extends Admin_site {

	var $internal_error_message = "There was an internal error";
	var $data_error_message = "Not enough data to complete request";
	var $page_text_error_message = "No text connected to this page";
	var $no_pages_message = "No pages to list";
	var $no_messages_on_page_message = "There are no labels attached to this page.";

	var $text_management_title_message = "Site Text Management";
	var $text_management_instruction_message = "";
	var $debug_text = 0;
	var $debug_search = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Text_management($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	} //end of function Text_management

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_page_messages($db,$page_id=0,$language_id=1)
	{
		//echo $language_id." is language id<br>\n";

		if ($page_id)
		{
			include ("../config.php");
			$page_name = $this->get_page_name(&$db,$page_id);
			$language_name = $this->get_language_name(&$db,$language_id);
			$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=100%>\n";
			$this->title = $this->text_management_title_message." for the <b>".$language_name."</b> language";
			$this->description = "Labels attached to the <b>".$language_name."</b> language of the <b>".$page_name."</b> section";

			//display link back to page
			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=3>\n\t<a href=index.php?a=44&z=3&b=".$page_id.">
					<span class=medium_font_light><font face=arial,helvetica size=2 color=#FFFFFF><b>back to ".$page_name."</b></font></span></a>\n\t</td>\n</tr>\n";

			//get current pages messages

			if ($this->debug_text)
			{
				echo $this->is_class_auctions()." is is_class_auctions<BR>\n";
			}
			if ($this->is_class_auctions())
			{
				//this is the classauctions...display all text attached to this page
				$this->sql_query = "SELECT * FROM ".$this->pages_text_table." where
					page_id = ".$page_id." order by display_order";
			}
			else
			{
				//only display the text that concerns classifieds only
				$this->sql_query = "SELECT * FROM ".$this->pages_text_table." where
					page_id = ".$page_id." and classauctions = 0 order by display_order";
			}
			if ($this->debug_text) echo $this->sql_query."<br>\n";
			$page_result = &$db->Execute($this->sql_query);
			if (!$page_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($page_result->RecordCount() > 0)
			{
				$this->row_count = 0;
				$this->body .= "<tr class=row_color_black>\n\t<td align=center class=medium_font_light>\n\t<b>message label and explanation</b> \n\t</td>\n\t";
				$this->body .= "<td align=center class=medium_font_light>\n\ttext displayed when <b>".$language_name."</b> selected \n\t</td>\n\t";
				$this->body .= "<td class=medium_font_light>\n\t\n\t</td>\n</tr>\n";
				while ($show_page_messages = $page_result->FetchRow())
				{
					//get the current value of this message within this language
					$this->sql_query = "SELECT * FROM ".$this->pages_text_languages_table." where
						page_id = ".$page_id." and text_id = ".$show_page_messages["message_id"]." and language_id = ".$language_id;
					//echo $this->sql_query."<br>\n";
					$text_result = &$db->Execute($this->sql_query);
					if (!$text_result)
					{
						$this->error_message = $this->internal_error_message;
						$this->site_error($db->ErrorMsg());
						return false;
					}
					elseif ($text_result->RecordCount() == 1)
					{
						$this->get_configuration_data($db);
						$text_label_name = stripslashes(urldecode(urldecode($show_page_messages["name"])));
						$text_label_desc = stripslashes(urldecode(urldecode($show_page_messages["description"])));
						$show_text = $text_result->FetchRow();
						$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=45% class=medium_font>\n\t";


						$append = '';
						$search_terms = array('error','label','header');
						foreach ($search_terms as $name)
						{
							//this will only be used for optional fields
							if (preg_match('/'.$name.'/',$text_label_name))
								$append = ' - '.$name;
						}
						if (!preg_match('/registration/',$text_label_name)
							&& preg_match('/optional field/',$text_label_name))
						{
							//Site Wide Optional Fields
							for ($i=1;$i<21;$i++)
							{
								if (preg_match('/ '.$i.' /',$text_label_name))
								{
									$name = $this->configuration_data['optional_field_'.$i.'_name'].$append;
									$message = $text_label_name.'<br>'.$text_label_desc;
								}
							}
						}
						elseif (preg_match('/registration/',$text_label_name)
							&& preg_match('/optional field/',$text_label_name))
						{
							//Registration Optional Fields
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
							}
							for ($i=1;$i<11;$i++)
							{
								if (preg_match('/ '.$i.' /',$text_label_name))
								{
									$name = $registration_configuration['registration_optional_'.$i.'_field_name'].$append;
									$message = $text_label_name.'<br>'.$text_label_desc;
								}
							}
						}
						else
						{
							$name = $text_label_name;
							$message = $text_label_desc;
						}

						$this->body .= "<b>$name</b><br>
							<span class=small_font>$message</span></td>\n\t";

						$this->body .= "<td valign=top width=45% class=medium_font>\n\t<textarea cols=40 rows=2 readonly>".$this->special_chars(stripslashes(urldecode($show_text["text"])))."</textarea></td>\n\t";
						$this->body .= "<td valign=top width=10%>\n\t<a href=index.php?a=14&b=".$page_id."&c=".
							$show_page_messages["message_id"]."&l=".$language_id."><span class=medium_font>
							<img src=admin_images/btn_admin_edit_text.gif alt=edit border=0></span></a>\n\t</td>\n</tr>\n";

					}
					else
					{
						$this->body .= "<tr class=".$this->get_row_color().">\n\t<td colspan=3 class=medium_error_font".$this->no_messages_on_page_message." \n\t</td>\n</tr>\n";
					}
					$this->row_count++;
					//remove before release
				}


			}
			else
			{
				$this->body .= "<tr>\n\t<td colspan=3 class=medium_font align=center><br><br><br><b>There is no text attached to this page and language.</b><br><br><br><br></td>\n</tr>\n";
			}

			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=3><a href=index.php?a=44&z=3&b=".$page_id."><span class=medium_font_light><font face=arial,helvetica size=2 color=#FFFFFF><b>back to ".$page_name." </b></font></span></a>\n\t</td>\n</tr>\n";
			if ($iamdeveloper)
			{
				$this->body .= "<tr>\n\t<td colspan=3><a href=index.php?a=99&b=".$page_id."><span class=medium_font>click to add message to this page</span></a> - remove before release - DO NOT USE _ YOU WILL CAUSE PROBLEMS IN THE DATABASE\n\t</td>\n</tr>\n";
			}
			$this->body .= "</table>\n";
			return true;
		}
		else
		{
			$this->error_message = $internal_error_message;
			return false;
		}

	} //end of function display_page_messages

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function edit_text_message($db,$text_id=0,$language_id=1,$page_id=0)
	{
		if ($text_id)
		{
			include ("../config.php");
			$this->sql_query = "SELECT * FROM ".$this->pages_text_table." where
				message_id = ".$text_id;
			$result = &$db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$text = $result->FetchRow();
				//get the text attached to this language
				$this->sql_query = "select text from ".$this->pages_text_languages_table." where
					text_id = ".$text_id." and language_id=".$language_id;
				$language_result = &$db->Execute($this->sql_query);
				if (!$language_result)
				{
					$this->error_message = $this->internal_error_message;
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($language_result->RecordCount() == 1)
				{
					$show_language_message = $language_result->FetchRow();
					if (!$this->admin_demo())$this->body .= "<form action=index.php?a=14&b=".$text["page_id"]."&c=".$text_id."&l=".$language_id." method=post>\n";
					$this->body .= "<table cellpadding=0 cellspacing=0 border=0 class=row_color1>\n";
					$this->title = $this->text_management_title_message;
					$this->title .= " > Text Edit";
					$this->description = "Edit this site text message in this form.  Below is the name of text,
						the page the text appears on, the description of where the text appears as well as its use and the current
						text that goes in the place described.  Change the text by editing the text within the input box or textarea box
						below. Once your changes are made click the save changes button below.";
					$page_name = $this->get_page_name(&$db,$page_id);
					$language_name = $this->get_language_name(&$db,$language_id);
					$this->body .= "<tr>\n\t<td>\n\t<table cellpadding=0><tr class=row_color2><td align=right valign=top class=medium_font width=150>Text Name: </td><td class=medium_font><b>".urldecode($text["name"])."</b></td></tr>";
					$this->body .= "<tr class=row_color1><td align=right valign=top class=medium_font>Page Attached To: </td><td class=medium_font><b>".$page_name."</b></td></tr>";
					$this->body .= "<tr class=row_color2><td align=right valign=top class=medium_font>Language: </td><td class=medium_font><b>".$language_name."</b></td></tr>";
					$this->body .= "<tr class=row_color1><td align=right valign=top class=medium_font>Description: </td><td class=medium_font><b>".stripslashes(urldecode($text["description"]))."</b></td></tr>";
					$this->body .= "<tr class=row_color2><td align=right valign=top class=medium_font>Text Displayed: </td><td class=medium_font><textarea name=z[text] cols=50 rows=15>".$this->special_chars(urldecode($show_language_message["text"]))."</textarea></td></tr>";
					$this->body .= "</table>\n\t</td>\n</tr>\n";
					if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td align=center><input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
					$this->body .= "<tr>\n\t<td colspan=3><a href=index.php?a=14&b=".$text["page_id"]."&l=".$language_id."><span class=medium_font><b>back to ".$page_name." Labels</b></span></a>\n\t</td>\n</tr>\n";
					if ($iamdeveloper)
					{
						$this->body .= "<tr>\n\t<td colspan=3><a href=index.php?a=99&d=".$text_id."<span class=medium_font>edit name and description</span></a> - remove before release - DO NOT USE _ YOU WILL CAUSE PROBLEMS IN THE DATABASE\n\t</td>\n</tr>\n";
					}
					$this->body .= "</table>\n</form>\n";
				}
				else
				{
					$this->body .= "<table cellpadding=3 cellspacing=0 border=0>\n";
					$this->body .= "<tr>\n\t<td class=very_large_font><b>".$this->text_management_title_message."</b> </td>\n</tr>\n";
					$this->body .= "<tr>\n\t<td class=medium_font>There are no messages for this language </td>\n</tr>\n";
					$this->body .= "</table>\n";
				}
				return true;
			}
			else
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function edit_text_message

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_text_message($db,$text_page_id=0,$text_id=0,$text_info=0,$language_id)
	{
		//echo $text_id." is text_id<br>\n";
		//echo $text_page_id." is text_page_id<br>\n";
		//echo $text_info["text"]." is text_info<br>\n";
		if (($text_id) && ($text_page_id) && ($text_info))
		{
			$this->sql_query = "update ".$this->pages_text_languages_table." set text = \"".urlencode(stripslashes($text_info["text"]))."\"
				where text_id = ".$text_id." and language_id = ".$language_id;
			$result = &$db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
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
			return false;
		}
	} //end of function update_text_message

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_sections($db,$language_id)
	{
		$this->sql_query = "SELECT * FROM ".$this->text_page_table;
		$result = &$db->Execute($this->sql_query);
		if (!$result)
		{
			$this->error_message = $this->internal_error_message;
			$this->site_error($db->ErrorMsg());
			return false;
		}
		else
		{
			if ($result->RecordCount() > 0)
			{
				$this->title = "Site Sections";
				$this->row_count = 0;
				while ($show = $result->FetchRow())
				{
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td>\n\t<a href=index.php?a=14&b=".$show["text_page_id"]."&l=".$language_id.">
						<span class=medium_font>".$show["page_name"]."</span>\n\t</td>\n</tr>\n";
					$this->row_count++;
				}
			}
			else
			{
				$this->body .= "<tr>\n\t<td class=medium_font>\n\t".$this->no_pages_message." \n\t</td>\n</tr>\n";
			}
			$this->body .= "<tr class=row_color_black>\n\t<td><a href=index.php?a=14><span class=medium_font_light>site message home</span></a>\n\t</td>\n</tr>\n";
		}

		return true;

	} //end of function list_sections

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_languages($db)
	{
		$this->sql_query = "SELECT * FROM ".$this->pages_languages_table;
		$result = &$db->Execute($this->sql_query);
		if (!$result)
		{
			$this->error_message = $this->internal_error_message;
			$this->site_error($db->ErrorMsg());
			return false;
		}
		else
		{
			if ($result->RecordCount() > 0)
			{
				$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=100%>\n";
				$this->title = "Languages Home";
				$this->body .= "<tr bgcolor=000066><td class=medium_font_light colspan=4 align=center>\n\t<b>Current Languages</b> \n\t</td></tr>\n\t";
				$this->body .= "<tr class=row_color_red><td class=medium_font_light>\n\t<b>language</b> \n\t</td>\n\t";
				$this->body .= "<td class=medium_font_light align=center>\n\t<b>active/inactive</b>\n\t</td>\n\t";
				$this->body .= "<td class=medium_font_light width=100></td>\n\t";
					$this->body .= "</tr>\n";
				$this->row_count = 0;
				while ($show = $result->FetchRow())
				{
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t".$show["language"].
						" \n\t</td>\n\t";
					$this->body .= "<td class=medium_font align=center>\n\t";
					if ($show["active"])
						$this->body .= "active";
					else
						$this->body .= "inactive";
					$this->body .= "</td>\n\t";
					//echo "<td>\n\t<a href=index.php?a=14&l=".$show["language_id"]."><span class=medium_font>edit language site text</span></a>\n\t</td>\n";
					$this->body .= "<td align=center width=100>\n\t<a href=index.php?a=29&l=".$show["language_id"]."><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></a>\n\t</td>\n";
					$this->body .= "</tr>\n";
					$this->row_count++;
				}
				$this->body .= "<tr>\n\t<td colspan=4 align=center>\n\t<a href=index.php?a=30&z=1><span class=medium_font><br><b>Add New Language</b><br></span></a></td>\n</tr>\n";
				$this->body .= "</table>\n";
			}
			else
			{
				$this->body .= "<tr>\n\t<td class=medium_font>\n\t".$this->no_pages_message." \n\t</td>\n</tr>\n";
			}
		}

		return true;

	} //end of function list_languages

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function home($db)
	{
		$this->body .= "<table cellpadding=0 cellspacing=0 border=0 width=100% class=row_color1>\n";
		$this->title = $this->text_management_title_message;
		$this->description = "The table below displays a list of languages currently available in this admin panel. To edit each language's
		text simply click the appropriate link to enter the text area for that language. In order to display the \"choose language\" option
		to your visitors, you will need to point a link to this page: index.php?a=21. Languages must be set to \"active\" in order for the language
		to show up as a selection for your visitors.";
		$this->body .= "<tr>\n\t<td>\n\t";
		$this->list_languages(&$db);
		$this->body .= "</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td align=center>\n\t<a href=index.php?a=31><span class=medium_font><b>Delete a Language</b></span></a>\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function language_home($db,$language_id)
	{
		$this->body .= "<table cellpadding=0 cellspacing=0 border=0 class=row_color1>\n";
		$this->title = $this->text_management_title_message." for the <b>".$this->get_language_name(&$db,$language_id)."</b> language";
		$this->description = "You can control all program generated
			text messages through this administration.  Choose the page you wish to view and edit the messages from in the menu below.";
		$this->list_sections(&$db,$language_id);
		$this->body .= "</table>\n";
		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//remove before release
	function new_message_form($db,$page_id=0)
	{
		if ($page_id)
		{
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=99&b=".$page_id." method=post>\n";
			$this->body .= "<table cellpadding=0 cellspacing=0 border=0>\n";
			$this->title = "Add message to <b>".$this->get_page_name(&$db,$page_id)."</b> page";
			$this->body .= "<tr>\n\t<td align=right width=30% class=medium_font>\n\tname \n\t</td>\n\t";
			$this->body .= "<td width=70% class=medium_font>\n\t<input type=text name=c[name] size=50> \n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\tdescription \n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\t<input type=text name=c[description] size=50> \n\t</td>\n</tr>\n";

			$this->body .= "<tr>\n\t<td colspan=2><textarea name=c[text] rows=10 cols=50></textarea>\n\t</td>\n</tr>\n";

			$this->body .= "<tr>\n\t<td class=medium_font>classauctions only</td>";
			$this->body .= "<td><input type=radio name=c[classauctions_only] value=0 checked>no<br>
				<input type=radio name=c[classauctions_only] value=1>yes</td></tr>\n";

			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td align=center colspan=2><input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td align=center colspan=2><a href=index.php?a=14><span class=medium_font>back to text admin</span></a>\n\t</td>\n</tr>\n";
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
			$this->sql_query = "insert into ".$this->pages_text_table."
				(page_id,name,description,display_order,classauctions)
				values
				(".$page_id.",\"".urlencode($information["name"])."\",\"".urlencode($information["description"])."\",\"".$information["display_order"]."\",\"".$information["classauctions_only"]."\")";
			$result = &$db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}

			$message_id = $db->Insert_ID();
			if(!$information["classauctions_only"])
				$information["classauctions_only"] = 0;
			if(!$information["display_order"])
				$information["display_order"] = 0;

			$this->body .= "array (".$message_id.", '".urlencode($information["name"])."', '".urlencode($information["description"])."', '', ".$page_id.", ".$information["display_order"].", ".$information["classauctions_only"].")<br><br>";

			$this->sql_query = "select language_id from ".$this->pages_languages_table;
			$language_result = &$db->Execute($this->sql_query);
			if (!$language_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
			{
				while ($show_language = $language_result->FetchRow())
				{
					$this->sql_query = "insert into ".$this->pages_text_languages_table."
						(page_id, text_id,language_id,text)
						values
						(".$page_id.",".$message_id.",\"".$show_language["language_id"]."\",\"".urlencode($information["text"])."\")";
					$result = &$db->Execute($this->sql_query);

					if (!$result)
					{
						$this->error_message = $this->internal_error_message;
						$this->site_error($db->ErrorMsg());
						return false;
					}

					$this->body .= "array (".$page_id.", ".$message_id.", ".$show_language["language_id"].", '".urlencode($information["text"])."')<br>";
				}

				$this->body .= "the message id entered was ".$message_id."<br><br>";

				return true;
			}

		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function new_message_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//remove before release
	function name_and_description_form($db,$text_id=0)
	{
		if ($text_id)
		{
			$this->sql_query = "SELECT * FROM ".$this->pages_text_table." WHERE message_id = ".$text_id;
			$result = &$db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$text = $result->FetchRow();
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=99&d=".$text_id." method=post>\n";
				$this->body .= "<table cellpadding=0 cellspacing=0 border=0>\n";
				$this->body .= "<tr>\n\t<td class=medium_font>\n\tname \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t<input type=text name=e[name] size=50
					value=\"".urldecode($text["name"])."\"> \n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td valign=top class=medium_font>\n\tdescription \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t
					<textarea name=e[description] cols=50 rows=20>".$this->special_chars(stripslashes(urldecode($text["description"])))."</textarea>
					 \n\t</td>\n</tr>\n";

				$this->body .= "<tr>\n\t<td valign=top class=medium_font>\n\tbelongs to ".$this->get_page_name(&$db,$text["text_page_id"])." \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t<select name=e[page_id]>\n\t\t";
				$this->sql_query = "SELECT * FROM ".$this->pages_table;
				$page_result = &$db->Execute($this->sql_query);
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
						$this->body .= ">".$this->get_page_name(&$db,$show_page["page_id"])."</option>\n\t\t";
					}
				}
				$this->body .= "</select> \n\t</td>\n</tr>\n";

				$this->body .= "<tr>\n\t<td valign=top class=medium_font>\n\tdisplay order \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t<select name=e[display_order]>\n\t\t";
				for ($i=1;$i<100;$i++)
				{
					$this->body .= "<option ";
					if ($text["display_order"] == $i)
						$this->body .= " selected";
					$this->body .= ">".$i."</option>\n\t\t";
				}
				$this->body .= "</select> \n\t</td>\n</tr>\n";

				$this->body .= "</td>\n</tr>\n";
				if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td align=center colspan=2><input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td align=center colspan=2><a href=index.php?a=44&z=3&b=".$text["page_id"].">back to ".$this->get_page_name(&$db,$text["page_id"])."</a>\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td align=center colspan=2><a href=index.php?a=44&z=4&b=".$text["page_id"]."&t=".$element_id.">delete this text element</a>\n\t</td>\n</tr>\n";
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
	function update_message_name_and_description($db,$text_id=0,$information=0)
	{
		//echo $text_id." in update_message_name_and_description<br>\n";
		if (($text_id) && ($information))
		{

			$this->sql_query = "update ".$this->pages_text_table." set
				name = \"".$information["name"]."\",
				page_id= ".$information["page_id"].",
				display_order= ".$information["display_order"].",
				description = \"".$information["description"]."\"
				where message_id = ".$text_id;
			//echo $this->sql_query."<bR>\n";
			$result = &$db->Execute($this->sql_query);
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
	function delete_text_element($db,$element_id=0)
	{
		//echo $text_id." in update_message_name_and_description<br>\n";
		if ($element_id)
		{
			$this->sql_query = "delete from ".$this->pages_text_table."
				where message_id = ".$element_id;
			//echo $this->sql_query."<bR>\n";
			$result = &$db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}

			$this->sql_query = "delete from ".$this->geodesic_pages_messages_languages."
				where text_id = ".$element_id;
			//echo $this->sql_query."<bR>\n";
			$result = &$db->Execute($this->sql_query);
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

	function get_page_name($db,$page_id=0)
	{
		if ($page_id)
		{
			$this->sql_query = "select name from ".$this->pages_table." WHERE page_id = ".$page_id;
			//echo $this->sql_query."<br>\n";
			$result = &$db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
				return urldecode($show["name"]);
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
	} //end of function get_page_name

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_sub_page_name($db,$sub_page_id=0)
	{
		if ($sub_page_id)
		{
			$this->sql_query = "select name from ".$this->text_subpages_table." WHERE sub_page_id = ".$sub_page_id;
			$result = &$db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
				return urldecode($show["name"]);
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
	} //end of function get_sub_page_name

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function edit_language_form($db,$language_id=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"language name\", \"Language name used throughout your site to identify it.\"]\n
			Text[2] = [\"default language\", \"Choosing \\\"yes\\\" will set the above language as the default if the user does not choose a language.\"]\n
			Text[3] = [\"active language\", \"This language will not be available as a choice on the site's front end until the language is set as active. This allows you to setup and edit the language before releasing it as a language choice for your site.\"]\n";

		//".$this->show_tooltip(3,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		if ($language_id)
		{
			$this->sql_query = "SELECT * FROM ".$this->pages_languages_table." WHERE language_id = ".$language_id;
			$result = &$db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=29&l=".$language_id." method=post>\n";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 class=row_color1>\n";
				$this->title = "Languages > Edit";
				$this->description = "Make any necessary changes to language and click the \"save\" button below";
				$this->body .= "<tr>\n\t<td align=right width=50% class=medium_font>\n\tlanguage name:".$this->show_tooltip(1,1)."</td>\n\t";
				$this->body .= "<td valign=top width=50% class=medium_font>\n\t<input type=text name=b[language] value=\"".$show["language"]."\"> \n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font>\n\tdefault language:".$this->show_tooltip(2,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=b[default_language] value=1 ";
				if ($show["default_language"] ==1)
					$this->body .= "checked";
				$this->body .= "> yes<br><input type=radio name=b[default_language] value=0 ";
				if ($show["default_language"] == 0)
					$this->body .= "checked";
				$this->body .= ">no \n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td align=right valign=top class=medium_font>\n\tactive language:".$this->show_tooltip(3,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=b[active] value=1 ";
				if ($show["active"] ==1)
					$this->body .= "checked";
				$this->body .= "> yes<br><input type=radio name=b[active] value=0 ";
				if ($show["active"] == 0)
					$this->body .= "checked";
				$this->body .= ">no \n\t</td>\n</tr>\n";
				if (!$this->admin_demo()) $this->body .= "<tr class=row_color2>\n\t<td colspan=2 class=medium_font align=center>\n\t<input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
				$this->body .= "</form>\n";
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
	} //end of function edit_language_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_language($db,$language_id=0,$information=0)
	{
		if (($language_id) && ($information))
		{
			if ($information["default_language"] == 1)
			{
				$this->sql_query = "update ".$this->pages_languages_table." set
					default_language = 0";
				$result = &$db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					$this->site_error($db->ErrorMsg());
					return false;
				}
			}

			$this->sql_query = "update ".$this->pages_languages_table." set
				language = \"".$information["language"]."\",
				active = ".$information["active"].",
				default_language = ".$information["default_language"]."
				where language_id = ".$language_id;
			//echo $this->sql_query."<bR>\n";
			$result = &$db->Execute($this->sql_query);
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
	} //end of function update_language

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function new_language_form()
	{
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=30&z=1 method=post>\n";
		$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100%>\n";
		$this->title = "Languages > Add New Language";
		$this->description = "Enter a name for the new language. The text entries for this new language will be populated from the base language
		(first in the language list). Once created you will have the ability to change the text to the new language. When you are through making
		your changes to the new language set the new language to \"active\" using the \"edit\" link in the language list. This will make your new
		language a choice for visitors browsing your site. <br><br>

        <b>IMPORTANT:</b> To save yourself time we recommend setting up the base language text to display exactly as you want it to appear before
        creating your new language with this tool.";
		$this->body .= "<tr class=row_color1>\n\t<td align=right class=medium_font>\n\t<b>language name: </b> \n\t</td>\n\t";
		$this->body .= "<td class=medium_font>\n\t<input type=text name=b[language] value=\"".$show["language"]."\"> \n\t</td>\n</tr>\n";
		if (!$this->admin_demo()) $this->body .= "<tr class=row_color2>\n\t<td colspan=2 class=medium_font align=center>\n\t<input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;
	} //end of function edit_language_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_new_language($db,$information=0)
	{
		if ($information)
		{
			$this->sql_query = "insert into ".$this->pages_languages_table."
				(language)
				values
				(\"".$information["language"]."\")";
			$new_language_result = &$db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$new_language_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}

			$language_id = $db->Insert_ID();

			$this->sql_query = "SELECT * FROM ".$this->pages_text_languages_table." WHERE language_id = 1";
			$result = &$db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				while ($show = $result->FetchRow())
				{
					$text = urldecode($show["text"]);
					$text = urlencode($text);
					$this->sql_query = "insert into ".$this->pages_text_languages_table."
						(page_id,text_id,language_id,text)
						values
						(".$show["page_id"].",".$show["text_id"].",".$language_id.",\"".$text."\")";
					$new_message_result = &$db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$new_message_result)
					{
						$this->error_message = $this->internal_error_message;
						$this->site_error($db->ErrorMsg());
						return false;
					}
				}
			}
			else
			{
				return false;
			}

			// Copy template attachments
			$this->sql_query = "select page_id, template_id from ".$this->pages_templates_table." WHERE language_id = 1";
			//echo $this->sql_query.'<Br>';
			$template_result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
			{
				while($templates = $template_result->FetchRow())
				{
					$this->sql_query = "insert into ".$this->pages_templates_table."
							(page_id, language_id, template_id)
							values
							(".$templates["page_id"].", ".$language_id.", ".$templates["template_id"].")";
					//echo $this->sql_query.'<Br>';
					$result = $db->Execute($this->sql_query);
				}
			}

			$this->sql_query = "SELECT * FROM ".$this->classified_categories_languages_table." WHERE language_id = 1";
			$result = &$db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				while ($show = $result->FetchRow())
				{
					$sql_query = "insert into ".$this->classified_categories_languages_table."
						(category_id,category_name,description,language_id)
						values
						(".$show["category_id"].",\"".addslashes(urlencode(urldecode(stripslashes($show["category_name"]))))."\",\"".addslashes(urlencode(urldecode(stripslashes($show["description"]))))."\",".$language_id.")";
					$insert_result = &$db->Execute($sql_query);
					//echo $sql_query." is the query<br>\n";
					if (!$insert_result)
					{
						//echo $sql_query." is the query<br>\n";
						$this->error_message = $this->messages[3500];
						return false;
					}
				}
			}
			else
			{
				return false;
			}

			// Add filter
			$this->sql_query = "select distinct(filter_id) from ".$this->classified_filters_languages_table;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query.'<br>';
			if(!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif($result->RecordCount() > 0)
			{
				while($filter = $result->FetchRow())
				{

					$this->sql_query = "insert into ".$this->classified_filters_languages_table." (filter_id, filter_name, language_id)".
									" values (".$filter["filter_id"].", \"\", ".$language_id.")";
					$insert_result = $db->Execute($this->sql_query);
					if(!$insert_result)
					{
						$this->error_message = $this->internal_error_message;
						$this->site_error($db->ErrorMsg());
						return false;
					}
				}
			}

			return $language_id;
		}
		else
		{
			//no information to enter
			return false;
		}

		return true;

	} //end of function insert_new_language

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_language_delete_form($db)
	{
		$this->sql_query = "SELECT * FROM ".$this->pages_languages_table;
		$result = &$db->Execute($this->sql_query);
		if (!$result)
		{
			$this->error_message = $this->internal_error_message;
			$this->site_error($db->ErrorMsg());
			return false;
		}
		else
		{
			$this->body .= "<table align=left cellpadding=3 cellspacing=1 border=0 width=100%>\n";
			$this->title = "Languages > Delete";
			$this->description = "Use this form to delete a language from your site.  You are able to delete all languages except for the base language.";
			if ($result->RecordCount() > 0)
			{
				$this->body .= "<tr bgcolor=000066>\n\t<td class=medium_font_light align=center colspan=3>\n\t<b>Current Languages</b> \n\t</td></tr>\n\t";
				$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light>\n\t<b>language</b> \n\t</td>\n\t";
				$this->body .= "<td class=medium_font_light align=center>\n\t<b>active/inactive</b> \n\t</td>\n\t";
				$this->body .= "<td class=medium_font_light align=center>\n\t<b>edit language</b> \n\t</td>\n\t";
				$this->row_count = 0;
				while ($show = $result->FetchRow())
				{
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t".$show["language"].
						"</span></a>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font align=center>\n\t";
					if ($show["active"])
						$this->body .= "active";
					else
						$this->body .= "inactive";
					$this->body .= "</td>\n\t";
					$this->body .= "<td align=center>\n\t";
					if ($show["language_id"] != 1)
						$this->body .= "<a href=index.php?a=31&l=".$show["language_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=edit border=0></span></a>";
					else
						$this->body .= "<span class=medium_font>base language</span>";
					$this->body .= "\n\t</td>\n";
					$this->body .= "</tr>\n";
					$this->row_count++;
				}
			}
			else
			{
				$this->body .= "<tr>\n\t<td colspan=2 class=medium_font>\n\tThere are no languages by that id \n\t</td>\n</tr>\n";
			}
			$this->body .= "</table>\n";
		}

		return true;
	} //end of function list_language_delete_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_language_verify($db,$language_id)
	{
		if (($language_id) && ($language_id))
		{
			$this->sql_query = "SELECT * FROM ".$this->pages_languages_table." WHERE language_id = ".$language_id;
			$result = &$db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$language_name = $this->get_language_name(&$db,$language_id);
				$this->body .= "<table cellpadding=0 cellspacing=0 border=0 width=100% class=row_color1>\n";
				$this->title = "Languages > Delete > Verify";
				$this->description = "Verify that you wish to remove the following language choice from your
					site by clicking the link below.";
				$this->body .= "<tr>\n\t<td align=center>\n\t<a href=index.php?a=31&l=".$language_id."&z=delete><span class=medium_font>
					<b>Verify deletion of ".$language_name." language</b></span></a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
			}
			else
			{
				$this->body .= "<table cellpadding=0 cellspacing=0 border=0 width=100%>\n";
				$this->title = "Languages > Delete > Verify Removal";
				$this->body .= "<tr class=row_color_red>\n\t<td class=medium_font_light>\n\tNo language exists by that id. \n\t</td>\n<\tr>\n";
				$this->body .= "</table>\n";
			}
			return true;
		}
		else
		{
			return false;
		}

		return true;
	} //end of function delete_language_verify

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_language($db,$language_id,$delete_verify)
	{
		if (($language_id) && ($delete_verify == "delete"))
		{
			$this->sql_query = "delete from ".$this->pages_languages_table." WHERE language_id = ".$language_id;
			//echo $this->sql_query."<br>\n";
			$result = &$db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
			{
				$this->sql_query = "delete from ".$this->pages_text_languages_table." WHERE language_id = ".$language_id;
				//echo $this->sql_query."<br>\n";
				$result = &$db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					$this->site_error($db->ErrorMsg());
					return false;
				}
			}
			$this->sql_query = "delete from ".$this->classified_categories_languages_table." WHERE language_id = ".$language_id;
			$result = &$db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}

			$this->sql_query = "delete from ".$this->pages_templates_table." WHERE language_id = ".$language_id;
			$result = &$db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}

			$this->sql_query = "delete from ".$this->classified_filters_languages_table." WHERE language_id = ".$language_id;
			$result = &$db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
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
			return false;
		}
	} //end of function delete_language

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function search_text($db, $search_term, $search_type=0)
	{
		if (strlen($search_term)>0)
		{
			if ($this->debug_search) echo "SEARCH STRING--><font color=red>".$search_term."</font><--<br>";

			//remove extra white space
			$search_term = preg_replace('/\s\s+/', ' ', $search_term);
			//escape exclamation
			$search_term = preg_replace('/!/', '\%21', $search_term);

			//find and remove sub-strings to treat as one term
			$search_terms = array();
			preg_match_all ("|([\"'`])(.+?)\\1|i", $search_term, $matches);
       		$search_term = preg_replace ("|([\"'`])(.+?)\\1|i", '', $search_term);
       		$search_terms = $matches[2];
       		//add search terms that have +s instead of spaces to the query
       		foreach($search_terms as $key => $word)
       		{
				if (strchr($search_terms[$key],' ') != false)
				{
		       		$search_term_with_plus = preg_replace('/\s/', '+', $search_terms[$key]);
		       		array_push($search_terms,$search_term_with_plus);
				}
       		}
       		//separate all remaining words as place in array
       		foreach (explode(' ',$search_term) as $word)
       		{
       			//remove zero length words in the array and skip to next iteration
				if(0==strlen($word))
				{
					unset($word);
					continue;
				}

				array_push($search_terms,' '.$word.' ');
       			array_push($search_terms,'+'.$word.'+');
       		}
       		if ($this->debug_search) highlight_string(print_r($search_terms,1));
       		$pages_where_clause = "";
			$templates_where_clause = "";
			$modules_where_clause = "";
       		foreach($search_terms as $word)
			{
				//BUILD QUERIES
				$pages_where_clause .= "OR text LIKE \"%$word%\"";
				$templates_where_clause .= "OR name LIKE \"%$word%\"
					OR template_code LIKE \"%$word%\"";
				$modules_where_clause .= "OR module_logged_in_html LIKE \"%$word%\"
					OR module_logged_out_html LIKE \"%$word%\"";
			}
			$pages_where_clause = " WHERE (".substr($pages_where_clause,3).")";
			$templates_where_clause = " WHERE (".substr($templates_where_clause,3).")";
			$modules_where_clause = " WHERE (".substr($modules_where_clause,3).")";
			$row_color = 0;
			//highlight_string(print_r($search_terms,1));exit;

			// Get languages
			$this->sql_query = "SELECT language_id, language FROM ".$this->pages_languages_table;
			$result = $db->Execute($this->sql_query);
			while($language_result = $result->FetchRow())
				$languages[$language_result["language_id"]] = $language_result["language"];
			if ($this->is_auctions())
			{
				$text_id_range_statement = '';
				$applies_to_statement = ' AND (applies_to=0 OR applies_to=2)';

			}
			elseif ($this->is_classifieds())
			{
				$text_id_range_statement = ' AND (text_id<100000 OR text_id>=150000)';
				$applies_to_statement =  ' AND (applies_to=0 OR applies_to=1)';
			}
			else
			{
				$text_id_range_statement = '';
				$applies_to_statement = '';
			}
			//PAGES_TEXT_TABLE SEARCH
 		  	$this->sql_query = "
				SELECT * FROM ".$this->pages_text_languages_table.$pages_where_clause.$text_id_range_statement;
			//PAGES_TEXT_TABLE SEARCH
			$pages_text_result = $db->Execute($this->sql_query);
			if(!$pages_text_result)
				return false;

 		    $this->sql_query = "
				SELECT * FROM ".$this->templates_table.$templates_where_clause;
			//TEMPLATES_TABLE SEARCH
			$templates_table_result = $db->Execute($this->sql_query);
			if(!$templates_table_result)
				return false;

			$this->sql_query = "
				SELECT * FROM ".$this->pages_table.$modules_where_clause.$applies_to_statement;
			//PAGES_TABLE SEARCH
			$pages_table_result = $db->Execute($this->sql_query);
			if(!$pages_table_result)
				return false;

			$this->title = "Text Search";
			$this->description = "Below are the results of your text
				search.  These results may be divided into separate categories depending on where they were found within your database.";

			$this->search_text_form();
			if (($pages_text_result->RecordCount() > 0) || ($templates_table_result->RecordCount() > 0) || ($pages_table_result->RecordCount() > 0))
			{
				$this->body .= "<table cellpadding=0 cellspacing=0 border=0 width=100% class=row_color1>\n";
				if ($pages_text_result->RecordCount() > 0)
				{
					$this->body .= "<tr><td><table width=100% border=0 cellspacing=1>";
					$this->body .= "<tr bgcolor=000066><td colspan=5 class=medium_font_light align=center><b>Pages / Modules Search Results</b>
						\n\t</td>\n\t</tr>";
					$this->body .= "<tr width=100% class=row_color_black>
						<td class=medium_font_light width=20%><b>page / module name</b></td>
						<td class=medium_font_light width=20%><b>text label</b></td>
						<td class=medium_font_light width=10%><b>language</b></td>";
					if ($search_type) $this->body .= "<td class=medium_font_light width=40%><b>full text</b></td>";
					else $this->body .= "<td class=medium_font_light width=40%><b>partial text</b></td>";
					$this->body .= "<td class=medium_font_light width=10% align=center><b></b></td></tr>";
					while($text_result = $pages_text_result->FetchRow())
					{
						if ($text_result["page_id"] != 0)
						{
							$this->sql_query = "SELECT name, page_id FROM ".$this->pages_table." WHERE page_id = ".$text_result["page_id"];
							$page_result = $db->Execute($this->sql_query);
							if(!$page_result)
							{
								//echo $this->sql_query.'<br>';
								return false;
							}
							elseif ($page_result->RecordCount() == 1)
							{
								$page_data = $page_result->FetchRow();
								$page_name = $page_data["name"];
								$page_id = $page_data["page_id"];
								$this->sql_query = "SELECT name FROM ".$this->pages_text_table." WHERE message_id = ".$text_result["text_id"];
								$page_result = $db->Execute($this->sql_query);
								if(!$page_result)
									return false;
								else
								{
									$page_data = $page_result->FetchRow();
									$label = urldecode($page_data["name"]);
								}

								if($row_color % 2 == 0)	$color = "row_color1";
								else $color = "row_color2";

								//remove extra white space from text
								$haystack = preg_replace('/\s\s+/', ' ', trim(stripslashes(urldecode($text_result["text"]))));
								if ($search_type == 0)
								{
									//start at some hi value and find the 1st occurance of the first word
									$p_start = strlen(stripslashes(urldecode($haystack)));
									$first_term = "";
									//POINTER CALCULATION, SHOW 1ST OCCURRENCE ONLY
									foreach ($search_terms as $word)
									{
										$pointer = 0;
										$pointer = strpos(strtolower(stripslashes(urldecode($haystack))),strtolower(stripslashes(urldecode($word))));
										if ($pointer<$p_start && $pointer!==false)
										{
											$p_start = $pointer;
											$first_term = $search_terms[$key];
										}
									}
									$p_end = $p_start+strlen($first_term);
									if (($p_start-25)<0)
									{
										$begin = 0;
										$beg_dots = "";
									}
									else
									{
										$begin = $p_start-25;
										$beg_dots = "....";
									}
									if (($p_end+25)>strlen($haystack))
									{
										$end = strlen($haystack);
										$end_dots = "";
									}
									else
									{
										$end = $p_end+25;
										$end_dots = "....";
									}
									$display_text = substr($haystack,$begin, $end-$begin);
								}
								elseif ($search_type == 1)
								{
									//no pointer, show whole file with every occurrence
									$display_text = $haystack;
									$beg_dots = "";
									$end_dots = "";
								}
								else
									return false;

								//convert all the angle brackets prior to displaying
								$search = array ('|<|','|>|','|"|');
								$replace = array ('&lt;','&gt;','&quot;');
								$display_text = preg_replace($search,$replace,$display_text);
								foreach ($search_terms as $term)
								{
									$term = preg_quote(preg_replace($search,$replace,$term));
									//restore exclamation
									$term = preg_replace('|\\\%21|','!',$term);

									//highlight every occurrence of search term
									$display_text = preg_replace("/$term/i","<span class=medium_error_font>$term</span>",$display_text);
									$display_text = preg_replace('/\+/',' ',$display_text);
								}
								$label = stripslashes(preg_replace('/\+/',' ',$label));
								$this->body .= "<tr class=".$color.">";
								$this->body .= "<td class=search_font><a class=search_font href=\"index.php?a=44&z=3&b=".$text_result["page_id"]."\"><span class=search_font>".$page_name."</span></a></td>\n\t";
								$this->body .= "<td class=search_font>".$label."</td>\n\t";
								$this->body .= "<td class=search_font>".$languages[$text_result["language_id"]]."</td>\n\t";
								$this->body .= "<td class=search_font align=center><font size=1 color=#888888><br>$beg_dots".stripslashes($display_text)."$end_dots<br></font></td>\n\t";
								$this->body .= "<td class=search_font valign=middle align=center><br><a href=\"index.php?a=14&b=".$text_result["page_id"]."&c=".$text_result["text_id"]."&l=1\"><span class=medium_font><font color=000000><b>edit&nbsp;text</b></font></span></a>\n\t";
								$this->body .= "<br><br><a href=\"index.php?a=38&b=".$text_result["page_id"]."\"><span class=medium_font><font color=000000><b>edit&nbsp;font</b></font></span></a><br><br>\n\t</td>";
								$this->body .= "</tr>";

								$row_color++;
							}
						}
					}
					$this->body .= "</table></td></tr>";
				}
				if ($templates_table_result->RecordCount() > 0)
				{
					$this->body .= "<tr><td><table width=100% border=0 cellspacing=1>";
					$this->body .= "<tr bgcolor=000066><td colspan=5 class=medium_font_light align=center><b>Templates Search Results</b>\n\t</td>\n\t</tr>";
					$this->body .= "<tr class=row_color_black>
						<td class=medium_font_light><b>template name</b></td>
						<td class=medium_font_light><b>description</b></td>
						<td class=medium_font_light><b>language</b></td>";
					if ($search_type)$this->body .= "<td class=medium_font_light width=40%><b>full text</b></td>";
					else $this->body .= "<td class=medium_font_light width=40%><b>partial text</b></td>";
					$this->body .= "<td class=medium_font_light align=center></td></tr>";
					while($text_result = $templates_table_result->FetchRow())
					{
						if($row_color % 2 == 0)	$color = "row_color1";
						else $color = "row_color2";
						//remove extra white space from text
						$haystack = preg_replace('/\s\s+/', ' ', trim(stripslashes(urldecode($text_result["template_code"]))));
						if ($search_type == 0)
						{
							//start at some hi value and find the 1st occurance of the first word
							$p_start = strlen(stripslashes(urldecode($haystack)));
							$first_term = "";
							//POINTER CALCULATION, SHOW 1ST OCCURRENCE ONLY
							foreach ($search_terms as $word)
							{
								$pointer = 0;
								$pointer = strpos(strtolower(stripslashes(urldecode($haystack))),strtolower(stripslashes(urldecode($word))));
								if ($pointer<$p_start && $pointer!==false)
								{
									$p_start = $pointer;
									$first_term = $search_terms[$key];
								}
							}
							$p_end = $p_start+strlen($first_term);
							if (($p_start-25)<0)
							{
								$begin = 0;
								$beg_dots = "";
							}
							else
							{
								$begin = $p_start-25;
								$beg_dots = "....";
							}
							if (($p_end+25)>strlen($haystack))
							{
								$end = strlen($haystack);
								$end_dots = "";
							}
							else
							{
								$end = $p_end+25;
								$end_dots = "....";
							}
							$display_text = substr($haystack,$begin, $end-$begin);
						}
						elseif ($search_type == 1)
						{
							//no pointer, show whole file with every occurrence
							$display_text = $haystack;
							$beg_dots = "";
							$end_dots = "";
						}
						else
							return false;

						//convert all the angle brackets prior to displaying
						$search = array ('|<|','|>|','|"|');
						$replace = array ('&lt;','&gt;','&quot;');
						$display_text = preg_replace($search,$replace,$display_text);
						foreach ($search_terms as $term)
						{
							$term = preg_quote(preg_replace($search,$replace,$term));
							//restore exclamation
							$term = preg_replace('|\\\%21|','!',$term);

							//highlight every occurrence of search term
							$display_text = preg_replace("/$term/i","<span class=medium_error_font>$term</span>",$display_text);
							$display_text = stripslashes(preg_replace('/\+/',' ',$display_text));
						}
						$this->body .= "<tr class=".$color.">";
						$this->body .= "<td class=search_font><span class=search_font>".$text_result["name"]."</span></a></td>\n\t";
						$this->body .= "<td class=search_font>".stripslashes($text_result["description"])."</td>\n\t";
						$this->body .= "<td class=search_font align=center>N/A</td>\n\t";
						$this->body .= "<td class=search_font align=center><font size=1 color=#888888><br>$beg_dots".$display_text."$end_dots<br></font></td>\n\t";
						$this->body .= "<td class=search_font valign=middle align=center><a href=\"index.php?a=45&z=2&b=".$text_result["template_id"]."\"><span class=medium_font><font color=00000><b>edit&nbsp;text</b></font></span></a>\n\t<br>";
						$this->body .= "</tr>";

						$row_color++;
					}
					$this->body .= "</table></td></tr>";
				}
				if ($pages_table_result->RecordCount() > 0)
				{
					$this->body .= "<tr><td><table width=100% cellspacing=1>";
					$this->body .= "<tr bgcolor=000066><td colspan=5 class=medium_font_light align=center><b>Logged In/Logged Out HTML Modules Search Results</b>\n\t</td>\n\t</tr>";
					$this->body .= "<tr class=row_color_black>
						<td class=medium_font_light><b>sshtml module name</b></td>
						<td class=medium_font_light><b>description</b></td>
						<td class=medium_font_light><b>language</b></td>";
					if ($search_type)$this->body .= "<td class=medium_font_light width=40%><b>full text of html logged in(above) / out(below)</b></td>";
					else $this->body .= "<td class=medium_font_light width=40%><b>partial text of html logged in(above) / out(below)</b></td>";
					$this->body .= "<td class=medium_font_light align=center></td></tr>";
					while($text_result = $pages_table_result->FetchRow())
					{
						if($row_color % 2 == 0)	$color = "row_color1";
						else $color = "row_color2";

						//remove extra white space from text
						$haystack = preg_replace('/\s\s+/', ' ', trim(stripslashes(urldecode($text_result["module_logged_in_html"]))));
						if ($search_type == 0)
						{
							//start at some hi value and find the 1st occurance of the first word
							$p_start = strlen(stripslashes(urldecode($haystack)));
							$first_term = "";
							//POINTER CALCULATION, SHOW 1ST OCCURRENCE ONLY
							foreach ($search_terms as $word)
							{
								$pointer = 0;
								$pointer = strpos(strtolower(stripslashes(urldecode($haystack))),strtolower(stripslashes(urldecode($word))));
								if ($pointer<$p_start && $pointer!==false)
								{
									$p_start = $pointer;
									$first_term = $search_terms[$key];
								}
							}
							$p_end = $p_start+strlen($first_term);
							if (($p_start-25)<0)
							{
								$begin = 0;
								$beg_dots = "";
							}
							else
							{
								$begin = $p_start-25;
								$beg_dots = "....";
							}
							if (($p_end+25)>strlen($haystack))
							{
								$end = strlen($haystack);
								$end_dots = "";
							}
							else
							{
								$end = $p_end+25;
								$end_dots = "....";
							}
							$display_text = substr($haystack,$begin, $end-$begin);
						}
						elseif ($search_type == 1)
						{
							//no pointer, show whole file with every occurrence
							$display_text = $haystack;
							$in_beg_dots = "";
							$in_end_dots = "";
						}
						else
							return false;

						//convert all the angle brackets prior to displaying
						$search = array ('|<|','|>|','|"|');
						$replace = array ('&lt;','&gt;','&quot;');
						$display_text = preg_replace($search,$replace,$display_text);
						foreach ($search_terms as $term)
						{
							$term = preg_quote(preg_replace($search,$replace,$term));
							//restore exclamation
							$term = preg_replace('|\\\%21|','!',$term);

							//highlight every occurrence of search term
							$display_text = preg_replace("/$term/i","<span class=medium_error_font>$term</span>",$display_text);
							$display_text_in = stripslashes(preg_replace('/\+/',' ',$display_text));
						}

						//remove extra white space from text
						$haystack = preg_replace('/\s\s+/', ' ', trim(stripslashes(urldecode($text_result["module_logged_out_html"]))));
						if ($search_type == 0)
						{
							//start at some hi value and find the 1st occurance of the first word
							$p_start = strlen(stripslashes(urldecode($haystack)));
							$first_term = "";
							//POINTER CALCULATION, SHOW 1ST OCCURRENCE ONLY
							foreach ($search_terms as $word)
							{
								$pointer = 0;
								$pointer = strpos(strtolower(stripslashes(urldecode($haystack))),strtolower(stripslashes(urldecode($word))));
								if ($pointer<$p_start && $pointer!==false)
								{
									$p_start = $pointer;
									$first_term = $search_terms[$key];
								}
							}
							$p_end = $p_start+strlen($first_term);
							if (($p_start-25)<0)
							{
								$begin = 0;
								$out_beg_dots = "";
							}
							else
							{
								$begin = $p_start-25;
								$out_beg_dots = "....";
							}
							if (($p_end+25)>strlen($haystack))
							{
								$end = strlen($haystack);
								$out_end_dots = "";
							}
							else
							{
								$end = $p_end+25;
								$out_end_dots = "....";
							}
							$display_text = substr($haystack,$begin, $end-$begin);
						}
						elseif ($search_type == 1)
						{
							//no pointer, show whole file with every occurrence
							$display_text = $haystack;
							$out_beg_dots = "";
							$out_end_dots = "";
						}
						else
							return false;

						//convert all the angle brackets prior to displaying
						$search = array ('|<|','|>|','|"|');
						$replace = array ('&lt;','&gt;','&quot;');
						$display_text = preg_replace($search,$replace,$display_text);
						foreach ($search_terms as $term)
						{
							$term = preg_quote(preg_replace($search,$replace,$term));
							//restore exclamation
							$term = preg_replace('|\\\%21|','!',$term);

							//highlight every occurrence of search term
							$display_text = preg_replace("/$term/i","<span class=medium_error_font>$term</span>",$display_text);
							$display_text_out = stripslashes(preg_replace('/\+/',' ',$display_text));
						}

						$this->body .= "<tr class=".$color.">";
						$this->body .= "<td class=search_font><span class=search_font>".$text_result["name"]."</span></a></td>\n\t";
						$this->body .= "<td class=search_font>".stripslashes($text_result["description"])."</td>\n\t";
						$this->body .= "<td class=search_font align=center>N/A</td>\n\t";
						if ($search_type)
						{
							$this->body .= "<td class=search_font align=center><font size=1 color=#888888><br>".stripslashes($display_text_in)."<br></font>\n\t";
							$this->body .= "<br>*************************************************<br>\n";
							$this->body .= "<font size=1 color=#888888><br>".stripslashes($display_text_out)."<br></font></td>\n\t";
						}
						else
						{
							$this->body .= "<td class=search_font align=center><font size=1 color=#888888><br>$in_beg_dots".stripslashes($display_text_in)."$in_end_dots<br></font>\n\t";
							$this->body .= "<br>*****************<br>\n";
							$this->body .= "<font size=1 color=#888888><br>$out_beg_dots".stripslashes($display_text_out)."$out_end_dots<br></font></td>\n\t";
						}
						//echo "<td class=search_font valign=middle><a href=\"index.php?a=74&b=".$text_result["page_id"]."\"><img border=0 src=\"admin_images/btn_admin_edit_text.gif\"></a></td>";
						$this->body .= "<td class=search_font valign=middle align=center><a href=\"index.php?a=74&b=".$text_result["page_id"]."\"><span class=medium_font><font color=000000><b>edit&nbsp;text</b></font></span></a>\n\t<br>";
						$this->body .= "</tr>";

						$row_color++;
					}
					$this->body .= "</table></td></tr>";
				}
				$this->search_text_form(1);

			}
			else
			{
				$this->body .= "<tr><td colspan=100% class=medium_error_font align=center>No text matched your search word or phrase.<br><br></td></tr>";
			}
		}
		else
		{
			$this->title .= "Text Search - Error";
			$this->body .= "<tr class=row_color1><td class=medium_error_font align=center>Please enter text into the keyword search box.<br><br>
				</td></tr>";
			$this->search_text_form();
		}
		return true;
	}// end function search_text

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function search_text_page($db, $search_type=0)
	{
		$this->title = "Text Search";
		$this->description = "Use this page to search for text that is stored in your database. This search will be applied to \"page text\",
			\"page modules\" and \"page templates\".";
		$this->body .= "<table cellpadding=0 cellspacing=0 border=0 width=100%>\n";
		$this->search_text_form($search_type);
		$this->body .= "</table>\n\t";
	}//end function search_text_page

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Text_management
?>
