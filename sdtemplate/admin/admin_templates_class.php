<?// admin_text_management_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
\**************************************************************************/

class Admin_template_management extends Admin_site {

	var $internal_error_message = "There was an internal error";
	var $data_error_message = "Not enough data to complete request";
	var $page_text_error_message = "No text connected to this page";
	var $no_pages_message = "No pages to list";
	var $no_messages_on_page_message = "There are no messages attached to this page.";
	var $debug_templates=0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Admin_template_management($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	} //end of function Admin_template_management

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_template($db,$template_id=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"modules used in this template\", \"Make sure these modules are referenced in all of the pages below or some functionality will not appear on the page as expected. If a module does not appear here as expected check the module tags in your template again for correct spelling and brackets.\"]\n";

		//".$this->show_tooltip(1,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$template = $this->get_template($db,$template_id);
		if (($template_id) && ($template))
		{
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 class=row_color1 width=100%>\n";
			$this->title = "Templates > View";
			$this->description = "You are currently viewing the <b>".stripslashes($template["name"])."</b> template particulars";

			$this->row_count = 0;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=50% class=medium_font>\n\t<b>Name:</b> \n\t</td>\n\t";
			$this->body .= "<td width=50% class=medium_font>\n\t".stripslashes($template["name"])." \n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>Description: </b>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\t".stripslashes($template["description"])." \n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>Template Classification: </b>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>";
			switch($template["applies_to"])
			{
				case 0:
					$this->body .= "General";
					break;
				case 1:
					$this->body .= "Classifieds";
					break;
				case 2:
					$this->body .= "Auctions";
					break;
			}
			$this->body .= " \n\t</td>\n</tr>\n";
			$this->row_count++;

			//get the modules referenced within the template
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=50% class=medium_font>\n\t
				<b>Modules used in this template:</b>".$this->show_tooltip(1,1)."</td>\n\t";
			$this->body .= "<td width=50% class=medium_font>\n\t";
			$this->sql_query = "select * from ".$this->pages_table." where module = 1";
			$module_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$module_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($module_result->RecordCount() > 0)
			{
				while ($show_module = $module_result->FetchRow())
				{
					$counter = 0;
					if (strstr($template["template_code"],$show_module["module_replace_tag"]))
					{
						$this->body .= "<a href=index.php?a=44&z=3&b=".$show_module["page_id"]."><span class=medium_font>";
						$page_name = $this->get_page($db,$show_module["page_id"]);
						$this->body .= $page_name["name"];
						$this->body .= "</span></a><br>";
						$counter++;
					}
				}
				if (!$counter) $this->body .= "No modules used in this template.";
			}
			else
			{
				$this->body .= "none";
			}
			$this->body .= "</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>Language:</b> \n\t</td>\n\t";
			$this->body .= "<td class=medium_font>";
			$language = $this->get_language_name($db,$template["language_id"]);
			$this->body .= $language." \n\t</td>\n</tr>\n";
			$this->row_count++;

			//pages attached to this template
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>Pages using this template:</b> \n\t</td>\n\t";
			$this->body .= "<td class=medium_font>";
			$this->sql_query = "select * from ".$this->pages_templates_table." where
				template_id = ".$template_id;
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
					$this->body .= "<a href=index.php?a=44&z=3&b=".$show_page["page_id"]."><span class=medium_font>";
					$page_name = $this->get_page($db,$show_page["page_id"]);
					$this->body .= $page_name["name"];
					$this->body .= " </span></a><br>";
				}
			}
			else
			{
				$this->sql_query = "select user_ad_template, user_extra_template, user_checkbox_template, full_size_image_template,
									ad_detail_print_friendly_template, popup_image_template_id from ".$this->ad_configuration_table
									." where 1";
				$result = $db->Execute($this->sql_query);
				if(!$result)
				{
					return false;
				}

				$special = $result->FetchRow();

				// check for special cases
				if($special["user_ad_template"] == $template_id)
				{
					$this->body .= "<span class=medium_font>Ad Display Page</span><br>";
					$set = true;
				}

				if($special["user_extra_template"] == $template_id)
				{
					$this->body .= "<span class=medium_font>Extra Page</span><br>";
					$set = true;
				}

				if($special["user_checkbox_template"] == $template_id)
				{
					$this->body .= "<span class=medium_font>Ad Display Checkbox Page</span><br>";
					$set = true;
				}

				if($special["full_size_image_template"] == $template_id)
				{
					$this->body .= "<span class=medium_font>Ad Display Full Size Image Page</span><br>";
					$set = true;
				}

				if($special["ad_detail_print_friendly_template"] == $template_id)
				{
					$this->body .= "<span class=medium_font>Ad Display Print Friendly Page</span><br>";
					$set = true;
				}

				if($special["popup_image_template_id"] == $template_id)
				{
					$this->body .= "<span class=medium_font>Ad Display Popup Image Page</span><br>";
					$set = true;
				}

				if(!$set)
				{
					$this->body .= "none";
				}
			}
			$this->body .= " \n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->sql_query = "select * from ".$this->templates_history_table." where template_id = ".$template_id." order by date asc";
			$hist_result = $db->Execute($this->sql_query);
			if ($hist_result->RecordCount() > 0)
			{
				$this->body .= "<tr  class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font><b>Template history to revert template to:</b>
					</font>\n\t</td>\n\t<td>";
				while ($hist_template = $hist_result->FetchRow())
				{
					$this->body .= "<a href=index.php?a=45&z=6&b=".$hist_template["history_id"]."
						><span class=medium_font>revert to template ".date("F j, Y, g:i a",$hist_template["date"])."</span><br>\n";
				}
				$this->body .= "</td></tr>";
			}

			$this->body .= "<tr>\n\t<td colspan=2 align=center><a href=index.php?a=45&z=2&b=".$template_id." class=medium_font><b>Edit this Template</b></font></td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td colspan=2><a href=index.php?a=45><span class=medium_font><b>Templates Home</b></span></td>\n</tr>\n";
			$this->body .= "</table>\n";
			return true;
		}
		else
		{
			$this->error_message = $internal_error_message;
			return false;
		}

	} //end of function display_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function edit_template($db,$template_id=0)
	{
		$template = $this->get_template($db,$template_id);
		$this->function_name = "edit_template";
		if (($template_id) && ($template))
		{
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=45&z=3&b=".$template_id." method=post  id=edit name=edit>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 class=row_color1 width=100%>\n";
			$this->title = "Templates > Edit";
			$this->description = "You are currently editing the <b>".$template["name"]."</b> template details.<br>
				Fill in the name and description, and then enter your html into the textarea box.";

				}
		else
		{
			//this is a new template
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=45&z=5 method=post id=edit name=edit>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 class=row_color1 width=100%>\n";
			$this->title = "Templates";
			$this->description = "Add a new template using the form below.
				Fill in the name and description.  Then enter your html in the textarea box.";
		}

		$this->row_count = 0;

		$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>Name:</b> \n\t</td>\n\t";
		$this->body .= "<td class=medium_font><input type=text size=50 name=c[name] value=\"".stripslashes($template["name"])."\"> \n\t</td>\n</tr>\n";
		$this->row_count++;

		$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>Description:</b> \n\t</td>\n\t";
		$this->body .= "<td class=medium_font><textarea name=c[description] rows=3 cols=50 >".$this->special_chars(stripslashes($template["description"]))."</textarea> \n\t</td>\n</tr>\n";
		$this->row_count++;

		$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>Template Classification:</b> \n\t</td>\n\t";
		$this->body .= "<td class=medium_font>";
		$this->body .= "<input type=\"radio\" name=\"c[applies_to]\" value=\"0\" ".(($template['applies_to'] == 0) ? "checked" : "").">General<Br>";
		if($this->is_class_auctions() || $this->is_classifieds())
			$this->body .= "<input type=\"radio\" name=\"c[applies_to]\" value=\"1\" ".(($template['applies_to'] == 1) ? "checked" : "").">Classifieds<Br>";
		if($this->is_class_auctions() || $this->is_auctions())
			$this->body .= "<input type=\"radio\" name=\"c[applies_to]\" value=\"2\" ".(($template['applies_to'] == 2) ? "checked" : "").">Auctions<Br>";
		$this->body .= "</td>\n</tr>\n";
		$this->row_count++;

		$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font><b>Language Label:</b> \n\t</td>\n\t";
		$this->body .= "<td class=medium_font><select name=c[language_id]>";
		$this->sql_query = "select * from ".$this->pages_languages_table;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->error_message = $this->internal_error_message;
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			while ($show_language = $result->FetchRow())
			{
				$this->body .= "<option value=".$show_language["language_id"];
				if ($show_language["language_id"] == $template["language_id"])
					$this->body .= " selected";
				$this->body .= ">".$show_language["language"]."</option>\n\t";
			}
		}
		$this->body .= "</select> \n\t</td>\n</tr>\n";
		$this->row_count++;

		/*
		remove tags from template and replace with text
		$this->sql_query = "select module_replace_tag from ".$this->pages_table." where module = 1";
		$module_result = $db->Execute($this->sql_query);
		if (!$module_result)
		{
			$this->error_message = $this->internal_error_message;
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($module_result->RecordCount() > 0)
		{
		*/
			$template = stripslashes($template["template_code"]);
		/*
			while ($show_tag = $module_result->FetchRow())
			{
				$replace_tag = str_replace("<","",$show_tag["module_replace_tag"]);
				$replace_tag = str_replace(">","",$replace_tag);
				//$replace_tag = "<font color=#dd0000 size=2>".$replace_tag." ";
				//echo $show_tag["module_replace_tag"]." - ".$replace_tag."<bR>\n";
				$template = str_replace($show_tag["module_replace_tag"],$replace_tag,$template);
			}
		}
		$template = str_replace("<<MAINBODY>>","MAINBODY",$template);
		$template = str_replace("<<CSSSTYLESHEET>>","CSSSTYLESHEET",$template);
		*/

		$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top colspan=2 class=medium_font><b>Template Code:</b><br>Cut and paste your html template into the space below. </td>\n\t</tr>";
		$this->body .= "<tr><td colspan=2 class=medium_font>
			<textarea id=\"c[template_code]\" name=\"c[template_code]\" style=\"width:100%\" cols=50 rows=50 id=edit name=edit>".$this->special_chars($template)."</textarea> \n\t</td>\n</tr>\n";
		$this->row_count++;

		if (!$this->admin_demo()) $this->body .= "<tr class=row_color2>\n\t<td colspan=2 class=medium_font align=center><input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";

		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;
	} //end of function edit_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_template($db,$template_info=0)
	{
		if ($template_info)
		{
			$this->sql_query = "insert into ".$this->templates_table."
				(name,description,template_code,language_id,applies_to)
				values
				(\"".addslashes($template_info["name"])."\",\"".addslashes($template_info["description"])."\",\"".addslashes($template_info["template_code"])."\",
				\"".$template_info["language_id"]."\", ".$template_info['applies_to'].")";
			$result = $db->Execute($this->sql_query);
			if ($this->debug_templates) echo $this->sql_query."<Br>\n";
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
	} //end of function insert_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_template($db,$template_id=0,$template_info=0)
	{
		$this->function_name = "update_template";
		/*
		echo $element_id." is element_id<br>\n";
		echo $text_page_id." is text_page_id<br>\n";
		echo $text_info." is text_info<br>\n";

		remove text from template and replace with tags for saving
		$this->sql_query = "select module_replace_tag from ".$this->pages_table." where module = 1";
		$module_result = $db->Execute($this->sql_query);
		if (!$module_result)
		{
			$this->error_message = $this->internal_error_message;
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($module_result->RecordCount() > 0)
		{
			$template = $template_info["template_code"];
			while ($show_tag = $module_result->FetchRow())
			{
				$replace_tag = str_replace("<","",$show_tag["module_replace_tag"]);
				$replace_tag = str_replace(">","",$replace_tag);
				//$replace_tag = "<font color=#dd0000 size=2>".$replace_tag." ";
				//echo $replace_tag." - ".$show_tag["module_replace_tag"]."<bR>\n";
				$template = str_replace($replace_tag,$show_tag["module_replace_tag"],$template);
			}
		}
		$template = str_replace("MAINBODY","<<MAINBODY>>",$template);
		$template = str_replace("CSSSTYLESHEET","<<CSSSTYLESHEET>>",$template);
		*/
		$current_template_info = $this->get_template($db,$template_id);

		$this->sql_query = "select * from ".$this->templates_history_table.
			" where template_id = ".$template_id." order by date asc";
		if ($this->debug_templates) echo $this->sql_query." here 11<br>\n";
		$history_result = $db->Execute($this->sql_query);
		if (!$history_result)
		{
			return false;
		}
		elseif  ($history_result->RecordCount() > 4)
		{
			$sixth_template=$history_result->FetchRow();
			$this->sql_query = "delete from ".$this->templates_history_table.
				" where history_id = ".$sixth_template["history_id"];
			if ($this->debug_templates) echo $this->sql_query." here 12<br>\n";
			$delete_history_result = $db->Execute($this->sql_query);
		}
		$this->sql_query = "insert into ".$this->templates_history_table."
			(template_id,date,template_code)
			values
			(\"".$template_id."\",\"".$this->shifted_time()."\",\"".addslashes($current_template_info["template_code"])."\")";
			if ($this->debug_templates) echo $this->sql_query." here 13<br>\n";
		$insert_result = $db->Execute($this->sql_query);
		if (!$insert_result)
		{
			if ($this->debug_templates) echo "this has an error<BR>\n";
			$this->error_message = $this->internal_error_message;
			$this->site_error($db->ErrorMsg());
			return false;
		}
		else
		{
			if (($template_id) && ($template_info))
			{
				$this->sql_query = "update ".$this->templates_table." set
					name = \"".addslashes($template_info["name"])."\",
					description = \"".addslashes($template_info["description"])."\",
					language_id = \"".$template_info["language_id"]."\",
					template_code = \"".addslashes($template_info["template_code"])."\",
					applies_to = \"".$template_info['applies_to']."\"
					where template_id = ".$template_id;
					if ($this->debug_templates) echo $this->sql_query." here 14<br>\n";
				$update_result = $db->Execute($this->sql_query);
				if($this->configuration_data["debug_admin"])
				{
					$this->debug_display($db, $this->filename, $this->function_name,
						"templates_table", "update pages template info");
				}
				if (!$update_result)
				{
					$this->error_message = $this->internal_error_message;
					$this->site_error($db->ErrorMsg());
					return false;
				}

				if(!$this->AttachEmbeddedModules($db, $template_id, 1, $template_info["template_code"]))
				{
					// Failed to insert modules to database
					return false;
				}

				if(is_file('storefront/admin_store.php'))
				{
					$this->sql_query = "select storefront_template from ".$this->templates_table." where template_id = ".$template_id;
					$storefrontTemplateResults = $db->Execute($this->sql_query);
					if(!$storefrontTemplateResults)
					{
						$this->error_message = $this->internal_error_message;
						$this->site_error($db->ErrorMsg());
						return false;
					}
					$storefrontTemplateInfo = $storefrontTemplateResults->FetchRow();
					if($storefrontTemplateInfo["storefront_template"]==1)
					{
						include_once('storefront/admin_store.php');
						include_once('storefront/admin_store_management.php');
						Admin_store_management::adminStoreSetTemplateModules($db, $template_id);
					}
				}
			}

			return true;
		}

	} //end of function update_font_element

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_templates($db)
	{
		$sql_query = "select * from ".$this->templates_table." order by applies_to, name";
		$templates_result = $db->Execute($sql_query);
		if ($this->debug_templates) echo $sql_query."<br>\n";
		if (!$templates_result)
		{
			if ($this->debug_templates)
			{
				echo $db->ErrorMsg()." is the error<br>\n";
				echo $sql_query." is the busted query<br>\n";
			}
			$this->error_message = $this->messages[5501];
			return false;
		}
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 class=row_color1>\n";
		$this->title = "Templates Home";
		$this->description = "Below is the list of current templates
			useable by pages on this site.  Add, delete or modify templates through the controls below.";

		if ($templates_result->RecordCount() > 0)
		{
			while ($show_templates = $templates_result->FetchRow())
			{
				// Since it is ordered by applies_to 0 will be first, then 1, then 2...

				if($show_templates['applies_to'] == 0 && !isset($general_used))
				{
					$this->body .= "<tr bgcolor=000066>\n\t<td class=medium_font_light align=center colspan=100%><b>General Templates</b> </td></tr>\n\t";
					$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light><b>Name</b> </td>\n\t";
					$this->body .= "<td class=medium_font_light><b>Description</b></td>\n\t";
					$this->body .= "<td colspan=3 class=medium_font_light>&nbsp; </a></td>\n</tr>\n";

					$general_used = 1;
					$this->row_count = 0;
				}
				elseif($show_templates['applies_to'] == 1 && !isset($class_used) &&
						($this->is_class_auctions() || $this->is_classifieds()))
				{
					$this->body .= "<tr><td>&nbsp;</td></tr>";
					$this->body .= "<tr bgcolor=000066>\n\t<td class=medium_font_light align=center colspan=100%><b>Classifieds Templates</b> </td></tr>\n\t";
					$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light><b>Name</b> </td>\n\t";
					$this->body .= "<td class=medium_font_light><b>Description</b></td>\n\t";
					$this->body .= "<td colspan=3 class=medium_font_light>&nbsp; </a></td>\n</tr>\n";

					$class_used = 1;
					$this->row_count = 0;
				}
				elseif($show_templates['applies_to'] == 2 && !isset($auc_used) &&
						($this->is_class_auctions() || $this->is_auctions()))
				{
					$this->body .= "<tr><td>&nbsp;</td></tr>";
					$this->body .= "<tr bgcolor=000066>\n\t<td class=medium_font_light align=center colspan=100%><b>Auctions Templates</b> </td></tr>\n\t";
					$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light><b>Name</b> </td>\n\t";
					$this->body .= "<td class=medium_font_light><b>Description</b></td>\n\t";
					$this->body .= "<td colspan=3 class=medium_font_light>&nbsp; </a></td>\n</tr>\n";

					$auc_used = 1;
					$this->row_count = 0;
				}

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t".stripslashes($show_templates["name"])." </td>\n\t";
				$this->body .= "<td class=medium_font>\n\t".stripslashes($show_templates["description"])." </td>\n\t";
				$this->body .= "<td><a href=index.php?a=45&z=2&b=".$show_templates["template_id"]."><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></a></td>\n\t";
				$this->body .= "<td><a href=index.php?a=45&z=1&b=".$show_templates["template_id"]."><span class=medium_font><img src=admin_images/btn_admin_view.gif alt=view border=0></span></a></td>\n\t";
				$this->body .= "<td><a href=index.php?a=45&z=4&c=".$show_templates["template_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a></td>\n</tr>\n";
				$this->row_count++;
			}
		}
		else
		{
			$this->body .= "<tr>\n\t<td colspan class=medium_font>There are no templates currently in the database. </td>\n</tr>\n";
		}

		// Put a blank row in between last and the Add link
		$this->body .= "<tr><td colspan=100%>&nbsp;</td></tr>";

		$this->body .= "<tr>\n\t<td colspan=100% align=center><a href=index.php?a=45&z=5><span class=medium_font><b>Add New Template<br><br></b></span></a></td>\n</tr>\n";
		$this->body .= "</table>\n";

		$this->body .= $this->check_templates($db);

		return true;

	} //end of function list_templates

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_verify_template($db,$template_id=0)
	{
		if ($template_id)
		{
			//if this template is not connected to anything then go ahead and delete it
			//if it is connected to a page give a list of pages it is connected to .
			$template = $this->get_template($db,$template_id);
			$sql_query = "select * from ".$this->pages_templates_table." where template_id = ".$template_id;
			$attached_templates_result = $db->Execute($sql_query);
			//echo $sql_query." is the query<br>\n";

			$sql_query = "select * from ".$this->classified_categories_languages_table." where template_id = ".$template_id."
				or secondary_template_id = ".$template_id." or ad_display_template_id = ".$template_id;
			$category_templates_result = $db->Execute($sql_query);
			//echo $sql_query." is the query<br>\n";

			$sql_query = "select * from ".$this->templates_table." order by name";
			//echo $sql_query." is the query<br>\n";
			$templates_result = $db->Execute($sql_query);

			//echo $attached_templates_result->RecordCount() ." is attached_templates_result count<br>";
			//echo $category_templates_result->RecordCount() ." is category_templates_result count<br>";
			if ((!$templates_result) || (!$attached_templates_result))
			{
				//echo "error in template_result or attached_templates_result<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ((($attached_templates_result->RecordCount() > 0) || ($category_templates_result->RecordCount() > 0)) && ($templates_result->RecordCount() > 0))
			{
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=45&z=4&c=".$template_id." method=post>\n";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 class=row_color1 width=100%>\n";
				$this->title = "Templates > Delete the ".$template["name"]." template";
				$this->description = "Below is the list of current pages
					using this template.  Choose the template from the dropdown box below you wish to attach to the currently attached pages in the list.";

				if ($attached_templates_result->RecordCount() > 0)
				{
					$this->body .= "<tr class=row_color_black>\n\t<td colspan=2 class=very_large_font_light><b>
						pages attached </td>\n</tr>\n";

					while ($show_templates = $attached_templates_result->FetchRow())
					{
						$page_name = $this->get_page($db,$show_templates["page_id"]);
						$this->body .= "<tr>\n\t<td colspan=2 class=medium_font>\n\t".$page_name["name"]." </td>\n</tr>\n";
					}
				}

				if ($category_templates_result->RecordCount() > 0)
				{
					$this->body .= "<tr class=row_color_black>\n\t<td colspan=2 class=very_large_font_light><b>
						categories attached </td>\n</tr>\n";

					while ($show_templates = $category_templates_result->FetchRow())
					{
						$category_name = $this->get_category_name($db,$show_templates["category_id"]);
						$this->body .= "<tr>\n\t<td colspan=2 class=medium_font>\n\t".$category_name." </td>\n</tr>\n";
					}
				}

				$this->body .= "<tr class=row_color2>\n\t<td medium_font_light>template to move to: </td>\n\t";
				$this->body .= "<td medium_font_light><select name=d>";
				while ($show_replace_templates = $templates_result->FetchRow())
				{
					$this->body .= "<option value=".$show_replace_templates["template_id"].">".$show_replace_templates["name"]."</option>";
				}
				$this->body .= "</select></td>\n</tr>";
				if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center>\n\t<input type=submit value=\"Save\"></td>\n</tr>\n";
				$this->body .= "</table>\n";
				$this->body .= "</form>\n";

				return true;
			}
			else
			{
				//this can be deleted but verify that they want to
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 class=row_color1 width=100%>\n";
				$this->title = "Templates > Delete";
				$this->description = "There are no pages attached to this template.  Verify that you wish to remove the template below.";

				$this->body .= "<tr>\n\t<td class=medium_font align=center><a href=index.php?a=45&z=4&c=".$template_id."&x=1><span class=medium_font>
					<b>Verify Removal of Template</b></span></a></td>\n</tr>\n";

				$this->body .= "</table>\n";
				//$this->body .= "returning true<br>\n";
				return true;
			}
		}
		else
		{
			return false;
		}
	} //end of function delete_verify_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_template($db,$template_id=0)
	{
		if ($template_id)
		{
			$sql_query = "delete from ".$this->templates_table." where template_id = ".$template_id;
			$templates_result = $db->Execute($sql_query);
			//echo $sql_query." is the query<br>\n";
			if (!$templates_result)
			{
				//echo $sql_query." is the query<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	} //end of function delete_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function move_to_template($db,$template_from=0,$template_to=0)
	{
		if (($template_from) && ($template_to) && ($template_from != $template_to))
		{
			$this->sql_query = "update ".$this->pages_templates_table." set
				template_id = ".$template_to."
				where template_id = ".$template_from;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			else
			{
				$this->sql_query = "update ".$this->classified_categories_languages_table." set
					template_id = ".$template_to."
					where template_id = ".$template_from;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					$this->body .= "still need<br>ad_detail_print_friendly_template,ad_detail_full_image_display_template_id,
						ad_detail_checkbox_display_template_id,ad_detail_extra_display_template_id,ad_detail_display_template_id,
						ad_display_template_id";
					//echo $this->sql_query." - has an error<br>\n";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				$this->sql_query = "update ".$this->classified_categories_languages_table." set
					secondary_template_id = ".$template_to."
					where secondary_template_id = ".$template_from;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					$this->body .= "still need<br>ad_detail_print_friendly_template,ad_detail_full_image_display_template_id,
						ad_detail_checkbox_display_template_id,ad_detail_extra_display_template_id,ad_detail_display_template_id";
					//echo $this->sql_query." - has an error<br>\n";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				$this->sql_query = "update ".$this->classified_categories_languages_table." set
					ad_display_template_id = ".$template_to."
					where ad_display_template_id = ".$template_from;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					echo "still need<br>ad_detail_print_friendly_template,ad_detail_full_image_display_template_id,
						ad_detail_checkbox_display_template_id,ad_detail_extra_display_template_id,ad_detail_display_template_id";
					//echo $this->sql_query." - has an error<br>\n";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				$this->sql_query = "update ".$this->classified_categories_languages_table." set
					ad_detail_display_template_id = ".$template_to."
					where ad_detail_display_template_id = ".$template_from;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					echo "still need<br>ad_detail_print_friendly_template,ad_detail_full_image_display_template_id,
						ad_detail_checkbox_display_template_id,ad_detail_extra_display_template_id";
					//echo $this->sql_query." - has an error<br>\n";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				$this->sql_query = "update ".$this->classified_categories_languages_table." set
					ad_detail_extra_display_template_id  = ".$template_to."
					where ad_detail_extra_display_template_id  = ".$template_from;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					echo "still need<br>ad_detail_print_friendly_template,ad_detail_full_image_display_template_id,
						ad_detail_checkbox_display_template_id";
					//echo $this->sql_query." - has an error<br>\n";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				$this->sql_query = "update ".$this->classified_categories_languages_table." set
					ad_detail_checkbox_display_template_id = ".$template_to."
					where ad_detail_checkbox_display_template_id = ".$template_from;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					echo "still need<br>ad_detail_print_friendly_template,ad_detail_full_image_display_template_id";
					//echo $this->sql_query." - has an error<br>\n";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				$this->sql_query = "update ".$this->classified_categories_languages_table." set
					ad_detail_full_image_display_template_id = ".$template_to."
					where ad_detail_full_image_display_template_id = ".$template_from;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					//echo $this->sql_query." - has an error<br>\n";
					echo "still need<br>ad_detail_print_friendly_template";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				$this->sql_query = "update ".$this->classified_categories_languages_table." set
					ad_detail_print_friendly_template = ".$template_to."
					where ad_detail_print_friendly_template = ".$template_from;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					//echo $this->sql_query." - has an error<br>\n";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				return true;
			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function move_to_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_templates($db)
	{
		$this->sql_query = "select page.page_id, page.name from ".$this->pages_table." as page left join ".$this->pages_templates_table." as template on page.page_id = template.page_id WHERE template.page_id is NULL AND page.module = 0";// and page.email = 0
		if($this->is_auctions())
			$this->sql_query .= " AND (page.applies_to = 2 OR page.applies_to = 0)";
		elseif($this->is_classifieds())
			$this->sql_query .= " AND (page.applies_to = 1 OR page.applies_to = 0)";
		if ($this->debug_templates) echo $this->sql_query."<bR>\n";
		$result = $db->Execute($this->sql_query);

		$body .= "";

		if($result)
		{
			$body .= "<table cellpadding=3 cellspacing=1 border=0 class=row_color1 width=100%>\n";
			$body .= "<tr class=row_color_black>\n\t<td colspan=7 class=medium_font_light align=center><b>Template Attachment</b></td>\n</tr>\n";
			$body .= "<tr class=row_color1>\n\t<td colspan=6 class=medium_font>The list below indicates pages in the system that currently do not have a template
			assigned to them. Pages cannot be displayed on your site if they do not have a template assigned. Ignore email results, as they do not require template assignments.</font></td>\n</tr>\n";
			$body .= "<tr bgcolor=000066>\n<td class=medium_font_light><b>Page ID</b></td>\n<td colspan=2 class=medium_font_light><b>Page Name</b></td>\n</tr>";
			$this->row_count = 0;
			while($page = $result->FetchRow())
			{
				$body .= "<tr class=".$this->get_row_color().">\n\t";
				$body .= "<td width=15% class=medium_font>".$page["page_id"]."</font></td>\n<td class=medium_font>\n".$page["name"]."</font></td>\n\t";
				$body .= "<td width=100 align=center>\n<a href=index.php?a=44&z=3&b=".$page["page_id"]."><img src=\"admin_images/btn_admin_edit.gif\" border=0></a></td>\n\t";
				$body .= "</tr>\n";
				$this->row_count++;
			}
			$body .= "</table>";

			return $body;
		}
		else
		{
			if ($this->debug_templates) echo $this->sql_query."<bR>\n";
			return false;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

function revert_template($db,$history_id)
	{
		if (!$history_id)
		{
			$this->error_message = $this->internal_error_message;
			$this->site_error($db->ErrorMsg());
			return false;
		}
		else
		{
			$this->function_name = "revert_template";
			$hist_template = $this->get_history_template($db,$history_id);
			$template = $this->get_template($db,$hist_template["template_id"]);
			$this->sql_query = "update ".$this->templates_table." set
				template_code = \"".addslashes($hist_template["template_code"])."\"
				where template_id = ".$hist_template["template_id"];
			///echo $this->sql_query." HERE1 <BR>\n";
			$update_templates_table_result = $db->Execute($this->sql_query);

			if($this->configuration_data["debug_admin"])
			{
				$this->debug_display($db, $this->filename, $this->function_name, "templates_table", "update LAST TEMPLATE info");
			}
			if (!$update_templates_table_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
			{
				$this->sql_query = "select * from ".$this->templates_history_table.
					" where template_id = ".$hist_template["template_id"]." order by date asc";
				//echo $this->sql_query." HERE1 <BR>\n";
				$history_result = $db->Execute($this->sql_query);

				if (!$history_result)
				{
					return false;
				}
				elseif  ($history_result->RecordCount() > 4)
				{
					$sixth_template=$history_result->FetchRow();
					$this->sql_query = "delete from ".$this->templates_history_table.
						" where history_id = ".$sixth_template["history_id"];
					//echo $this->sql_query." HERE2 <BR>\n";
					$delete_history_result = $db->Execute($this->sql_query);
				}

				$this->sql_query = "insert into ".$this->templates_history_table."
					(template_id,date,template_code)
					values
					(\"".$template["template_id"]."\",\"".$this->shifted_time()."\",\"".addslashes($template["template_code"])."\")";
				//echo $this->sql_query." HERE3 <BR>\n";
				$insert_result = $db->Execute($this->sql_query);
			}
		}
	return $hist_template["template_id"];
	} //end of function revert_template

//########################################################################

	function get_history_template($db,$history_id=0)
	{
		if ($history_id)
		{
			$function_name->this = "get_template";

			$this->sql_query = "select * from ".$this->templates_history_table." where history_id = ".$history_id;
			$template_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if($this->configuration_data["debug_admin"])
			{
				$this->debug_display($db, $this->filename, $this->function_name, "templates_table", "get template data");
			}
			if (!$template_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($template_result->RecordCount() == 1)
			{
				$show = $template_result->FetchRow();
				return $show;
			}
			else
			{
				//just display the user_id
				return false;
			}

		}
		else
		{
			return false;
		}
	} //end of function get_template

//########################################################################


}
?>
