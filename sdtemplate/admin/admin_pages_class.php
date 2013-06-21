<?//admin_pages_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
\**************************************************************************/

class Admin_pages extends Admin_site{

	var $pages_debug = 0;
	var $modules_debug = 0;
	var $modules_not_in_statement = "";

	//module and page id definitions
	var $main_cat_nav_mods = array(114);
	var $filter_dropdown_mods = array(91,101);
	var $display_username_mods = array(53);
	var $cat_tree_display_mods = array(97,98,99);
	var $reg_login_link_mods = array(54,66,67,68,78,79,80,88);
	var $cat_nav_mods = array(94,95,96,100);
	var $featured_pics_mods = array(89,90,102,117,118,119,120,121,122,123,124,20089);
	var $logged_in_out_HTML_mods = array(75,76,77,165,166,167,185,186,187,188,189,190,191,192,193,194,195,196,197,198);
	var $PHP_mods = array(103,104,105,110,111,112);
	var $search_mods = array(106);
	var $zip_browse_mods = array(133);
	var $state_browse_mods = array(134);
	var $extra_pages = array(135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154);
	var $featured_listings_mods = array(155,156);
	var $fixed_cat_nav_mods = array(158,159,160,161,162,163,164);
	var $title_mods = array(171);
	var $featured_and_newest_mods = array(125,126,127,128,129,130,131,132,46,47,48,49,50,60,61);
	var $hottest = array(172);
	var $users = array(169,170);

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function Admin_pages($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	} //end of function Admin_pages

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function home ()
	{
		$this->body .= "<table align=center width=100%>\n";
		$this->body .= "<tr>\n\t<td><a href=index.php?a=7>browse and edit categories</a>\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
	} //end of function home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_current_page($db,$page_id=0)
	{
		if (!$page_id)
			return false;

		$this->sql_query = "select * from ".$this->pages_table." where page_id = ".$page_id;
		$page_result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$page_result)
			return false;
		elseif ($page_result->RecordCount() == 1)
			$show = $page_result->FetchRow();
		else
			return false;

	 	if ($show["module"] == 0)
	 	{
			$section = $this->get_section($db,$show["section_id"]);
			$page = $this->get_page($db,$page_id);

			// Find number of languages so we can fix the colspan
			$this->sql_query = "select count(language_id) as count from ".$this->pages_languages_table." where active = 1";
			$result = $db->Execute($this->sql_query);
			$languages = $result->FetchRow();
			$num_languages = $languages["count"];

			$this->title = "Pages Management > Section: ".$section["name"]." > Page: ".$show["name"];
			$this->description = "Configure the fonts, text and template used in this page through this form.  To edit the fonts used on this
				page click the edit fonts link.  To edit the text that appears in the page click the text edit link.  To change the
				template attached to this page choose from the template list below. <br>\n\t</td>\n</tr>\n";

			$this->body .= "	<table cellpadding=2 cellspacing=1 border=0 width=100% class=row_color1>\n";
			if (!$this->admin_demo())
				$this->body .= "<form action=index.php?a=44&b=".$page_id."&z=7 method=post>\n";
			$this->input_admin_label_and_tag_name($page,1,1);
			$this->body .= "		<tr bgcolor=000066>
										<td colspan=100% class=medium_font_light align=center>
											<b>Template(s) used to build this Page:</b>
										</td>
									</tr>";

			$this->sql_query = "select * from ".$this->pages_languages_table;
			$language_result = $db->Execute($this->sql_query);
			if (!$language_result)
				return false;
			elseif ($language_result->RecordCount() > 0)
			{
				$this->row_count = 0;
				while ($show_language = $language_result->FetchRow())
				{
					//get template attached to this language and page
					$template_id = $this->get_page_language_template($db,$page_id,$show_language["language_id"]);
					$template = $this->get_template($db,$template_id);

					if($template_id)
					{
						// Attach modules from template
						if(!$this->AttachEmbeddedModules($db, $template_id, 1, $template["template_code"]))
						{
							// Failed to insert modules to database
							return false;
						}
					}

					$this->body .= "<tr class=".$this->get_row_color().">
										<td colspan=100%>
											<table width=100%>
												<tr>
													<td align=right width=40% class=medium_font>
														".$this->get_language_name($db,$show_language["language_id"])."
													</td>
													<td width=50% class=medium_font>
														<b>".((strlen($template["name"]) > 0) ? $template["name"] : "none attached")."</b>
													</td>";
					if ($page_id == 69)
					{
						$this->body .= "
													<td align=right>
														<a href=index.php?a=23&r=16>
															<span class=medium_font>
																<img src=admin_images/btn_admin_edit.gif alt=edit border=0>
															</span>
														</a>
													</td>
												</tr>
											</table>
										</td>
									</tr>";
					}
					elseif ($page_id == 157)
					{
						$this->body .= "
													<td align=right>
														<a href=index.php?a=23&r=19>
															<span class=medium_font>
																<img src=admin_images/btn_admin_edit.gif alt=edit border=0>
															</span>
														</a>
													</td>
												</tr>
											</table>
										</td>
									</tr>";
					}
					elseif($page_id == 1)
					{
						$this->body .= "
													<td align=right>
														<a href=index.php?a=44&z=5&b=".$page_id."&c=".$show_language["language_id"].">
															<span class=medium_font>
																<img src=admin_images/btn_admin_edit.gif alt=edit border=0>
															</span>
														</a>
													</td>
												</tr>
											</table>
										</td>
									</tr>";
						$this->row_count++;

						if($this->is_class_auctions())
							$this->sql_query = "SELECT user_ad_template, user_extra_template, user_checkbox_template, auctions_user_ad_template,
								auctions_user_extra_template, auctions_user_checkbox_template";
						elseif($this->is_auctions())
							$this->sql_query = "SELECT auctions_user_ad_template, auctions_user_extra_template, auctions_user_checkbox_template";
						elseif($this->is_classifieds())
							$this->sql_query = "SELECT user_ad_template, user_extra_template, user_checkbox_template";
						$this->sql_query .= " FROM ".$this->ad_configuration_table;
						//echo $this->sql_query.'<Br>';
						$result = $db->Execute($this->sql_query);
						if(!$result)
							return false;
						else
							$misc_templates = $result->FetchRow();

						foreach($misc_templates as $key => $template_info)
						{
							if(is_int($key))
							{
								$template = $this->get_template($db,$template_info);
								$this->body .= "
									<tr class=".$this->get_row_color().">
										<td colspan=100%>
											<table width=100%>
												<tr>
													<td align=right width=40% class=medium_font>
														".$this->get_language_name($db,$show_language["language_id"])."
													</td>
													<td width=60% class=medium_font>
														<b>".((strlen($template["name"]) > 0) ? $template["name"] : "none attached")."</b>
													</td>
													<td align=right>
														&nbsp;
													</td>
												</tr>
											</table>
										</td>
									</tr>";
								$this->row_count++;
							}
						}

						// Revert back one row_count number
						$this->row_count--;
					}
					else
					{
						$this->body .= "
													<td align=right>
														<a href=index.php?a=44&z=5&b=".$page_id."&c=".$show_language["language_id"].">
															<span class=medium_font>
																<img src=admin_images/btn_admin_edit.gif alt=edit border=0>
															</span>
														</a>
													</td>
												</tr>
											</table>
										</td>
									</tr>";
					}
					$this->row_count++;

					if($this->row_count % $language_result->RecordCount() != 0)
						$this->body .= "<tr class=\"row_color_red\"><td colspan=100%></td></tr>";
				}
			}
			if ($page_id<135 || $page_id>154)
			{
				$this->sql_query = "select * from ".$this->pages_languages_table;
				$language_result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is the query<br>\n";
				if (!$language_result)
				{
					return false;
				}
				elseif ($language_result->RecordCount() > 0)
				{
					$this->row_count = 0;
					$this->body .= "
									<tr bgcolor=#000066>
										<td colspan=100% class=medium_font_light align=center>
											<b>Text appearing on this page:</b>
										</td>
									</tr>
									<tr><td colspan=100%><table width=100%>";
					while ($show_language = $language_result->FetchRow())
					{
						//get template attached to this language and page
						$template = $this->get_template($db,$show["template_id"]);
						$this->body .= "
									<tr class=".$this->get_row_color().">
										<td align=right width=50% class=medium_font>
											".$this->get_language_name($db,$show_language["language_id"])."
										</td>
										<td colspan=100% width=40% align=right class=medium_font>
											<a href=index.php?a=14&b=".$page_id."&l=".$show_language["language_id"].">
												<img src=admin_images/btn_admin_edit_text.gif alt=edit border=0>
											</a>
										</td>
									</tr>";
						$this->row_count++;
						if($this->row_count % $language_result->RecordCount() != 0)
							$this->body .= "<tr class=\"row_color_red\"><td colspan=100%></td></tr>";
					}
					$this->body .= "</table></td></tr>";
				}
				$this->body .= "	<tr bgcolor=000066>
										<td colspan=100% class=medium_font_light align=center>
											<b>CSS Fonts appearing on this page:</b>
										</td>
									</tr>
									<tr align=center valign=middle>
										<td colspan=100%>
											<a href=index.php?a=38&b=".$page_id.">
												<span class=medium_font_light><img src=admin_images/btn_admin_edit_css.gif alt=edit border=0></span>
											</a>
										</td>
									</tr>";
			}
			else
			{
				// Color the description cell the right color
				$this->row_count = 1;

				// Extra pages
				$this->body .= "	<tr bgcolor=#000066>
										<td colspan=100% class=medium_font_light align=center>
											<b>Main Body Text:</b>
										</td>
									</tr>
									<tr>
										<td colspan=100%>
											<table width=100% cellpadding=0 cellspacing=0>
												<tr class=\"".$this->get_row_color()."\">
													<td colspan=100% align=center class=medium_font>
														This HTML text will be displayed in place of the &lt;&lt;MAINBODY&gt;&gt; tag in your assigned template.
													</td>
												</tr>
												<tr>
													<td colspan=100% align=center>
														<textarea name=c[extra_page_text] rows=15 cols=85%>".stripslashes(urldecode($page['extra_page_text']))."</textarea>
													</td>
												</tr>
												<tr>
													<td colspan=100% align=center>
														<input type=\"submit\" value=\"Save\">
													</td>
												</tr>
											</table>
										</td>
									</tr>";
			}

			//modules attached
				$this->body .= "	<tr>
										<td colspan=100%>
											<table>
												<tr bgcolor=#000066>
													<td colspan=100% class=medium_font_light align=center>
														<b>Modules attached to this Page:</b>
													</td>
												</tr>
												<tr class=row_color2>
													<td colspan=".(2+$num_languages)." class=medium_font>
														<font color=#33bb33 size=2 face=arial,helvetica><b>found</b></font> - module found within template<br>
														<font color=#bb3333 size=2 face=arial,helvetica><b>not found</b></font> - module not found within template<br>
														<font color=#0000FF size=2 face=arial,helvetica><b>embedded</b></font> - module's tag found within another module
													</td>
												</tr>
												<tr>
													<td colspan=100% class=medium_font align=left width=90%>
														<b>IMPORTANT:</b> Each <b><font color=#0000FF size=2 face=arial,helvetica>\"embedded\"</font></b> module
														must appear lower in the list than the module it is <b><font color=#0000FF size=2 face=arial,helvetica>
														\"embedded within\"</font></b>. Otherwise, the <b><font color=#0000FF size=2 face=arial,helvetica>
														\"embedded\"</font></b> module will not display correctly within your template. Also, if you \"remove\"
														a Module that is currently used within a template attached to this page, it will automatically reattach
														itself to the table below.
													</td>
												</tr>";

			$this->sql_query = "select * from ".$this->pages_modules_table." where page_id = ".$page_id." order by time asc";
			if ($this->pages_debug)
				echo $this->sql_query."<br>\n";
			$module_result = $db->Execute($this->sql_query);
			 if (!$module_result)
			 {
				 if ($this->pages_debug) echo $this->sql_query."<br>\n";
				//$this->error_message = $this->messages[3501];
				return false;
			 }

			 $this->sql_query = "select * from ".$this->pages_languages_table;
			 if ($this->pages_debug)
			 	echo $this->sql_query."<br>\n";
			 $language_result = $db->Execute($this->sql_query);
			 if($this->configuration_data["debug_admin"])
			 {
			 	$this->debug_display($db, $this->filename, $this->function_name, "pages_languages_table", "get pages languages data");
			 }
			 if (!$language_result)
			 {
			 	if ($this->pages_debug)
			 	{
			 		echo  $this->sql_query."<br>\n";
			 		echo  $db->ErrorMsg()."<bR>\n";
			 	}
			 	//$this->error_message = $this->messages[3501];
			 	return false;
			 }
			 elseif ($language_result->RecordCount() > 0)
			 {
			 	$language_name_array = array();
			 	$language_id_array = array();
			 	$this->row_count = 0;
			 	while ($show_language = $language_result->FetchRow())
			 	{
			 		//put all languages within an array to be reused
			 		array_push($language_name_array, $show_language["language"]);
			 		array_push($language_id_array, $show_language["language_id"]);
			 	} //end of while
			 }
			 else
			 {
			 	$this->body .=  "there are no languages<bR>\n";
			 }
			reset($language_name_array);
			reset($language_id_array);

			 if ($module_result->RecordCount() > 0)
			 {
				$this->body .= "
												<tr class=row_color_red>
													<td width=50% class=medium_font_light align=center>
														<b>Module Name</b><br>
				 									</td>\n\t";
				foreach ($language_name_array as $key => $value)
				{
					$this->body .= "
													<td class=medium_font_light align=center>
														status in<br><b>".$value."</b>
													</td>";
				}
				$this->body .= "
													<td class=medium_font_light align=center>
														<b>Action</b>
													</td>
												</tr>";

				$this->row_count = 0;
				$modules_in = array();
				$embedded = array();	// Used to store embedded modules
				while ($show_module = $module_result->FetchRow())
				{
					reset($language_name_array);
					reset($language_id_array);
					$current_module = $this->get_page($db,$show_module["module_id"]);
					array_push($modules_in,$show_module["module_id"]);
					$this->body .= "
												<tr class=".$this->get_row_color().">
													<td align=right width=50%>
														<a href=index.php?a=44&z=3&b=".$show_module["module_id"]."><span class=medium_font><font color=000000>".$current_module["name"]."</font></span></a><br>\n";

					$replace_tag = str_replace("<","&lt;",$current_module["module_replace_tag"]);
					$replace_tag = str_replace(">","&gt;",$replace_tag);
					$this->body .= "
														<span class=medium_font>".$replace_tag."</span>\n\t";

					// If a HTML module then list modules referenced in it
					if(strpos($current_module["name"], "HTML") !== false)
					{
						// Its an HTML module
						// Get module HTML code
						$this->sql_query = "select page_id, module_logged_in_html, module_logged_out_html from ".$this->pages_table." where page_id=".$show_module["module_id"];
						$new_module_result = $db->Execute($this->sql_query);
						$module_code = $new_module_result->FetchRow();
						if(!$new_module_result)
						{
							return false;
						}

						// Get all modules so we can test their replace tags
						$this->sql_query = "select page_id, module_replace_tag from ".$this->pages_table." where module = 1";
						$new_module_result = $db->Execute($this->sql_query);
						if(!$new_module_result)
						{
							return false;
						}
						$module_tags = array();
						while ($new_module = $new_module_result->FetchRow())
							array_push($module_tags,$new_module);
						for($i = 0; $i < sizeof($module_tags); $i++)
						{
							//echo strlen($module_code["module_logged_in_html"]).' is the length<Br>';
							//echo eregi($module_tags[$i]['module_replace_tag'], $module_code["module_logged_in_html"]).' is the counter<br>';
							if(eregi($module_tags[$i]['module_replace_tag'], $module_code["module_logged_in_html"]))
							{
								// Module found so lets attach it and display it
								$this->sql_query = "select page_id from ".$this->pages_modules_table." where page_id = ".$page_id." and module_id = ".$module_tags[$i]['page_id'];
								$result_check = $db->Execute($this->sql_query);
								if(!$result_check)
								{
									return false;
								}

								if($result_check->RecordCount() == 0)
								{
									// Not been inserted yet, so lets insert it
									$this->sql_query = "insert into ".$this->pages_modules_table." (module_id, page_id, time) values (".$module_tags[$i]['page_id'].", ".$page_id.", ".$this->shifted_time().")";
									$result_check = $db->Execute($this->sql_query);
									if(!$result_check)
									{
										return false;
									}
								}

								// Print out the replace tag for the module
								$replace_tag = str_replace("<","&lt;",$module_tags[$i]['module_replace_tag']);
								$replace_tag = str_replace(">","&gt;",$replace_tag);
								if(sizeof($embedded) == 0)
								{
									$this->body .= "	<br>
														<b><font color=#0000FF size=2 face=arial,helvetica>Embedded within:</font></b><Br><font font color=#0000FF size=2 face=arial,helvetica>".$replace_tag."</font>";
								}
								else
								{
									$this->body .= "	<br>
														<font color=#0000FF size=2 face=arial,helvetica>".$replace_tag."</font>";
								}

								// Finally put it in the embedded array
								$embedded[] = $module_tags[$i]['page_id'];
							}

							if(eregi($module_tags[$i]['module_replace_tag'], $module_code["module_logged_out_html"]))
							{
								// Module found so lets attach it and display it
								$this->sql_query = "select page_id from ".$this->pages_modules_table." where page_id = ".$page_id." and module_id = ".$module_tags[$i]['page_id'];
								$result_check = $db->Execute($this->sql_query);
								if(!$result_check)
								{
									return false;
								}

								if($result_check->RecordCount() == 0)
								{
									// Not been inserted yet, so lets insert it
									$this->sql_query = "insert into ".$this->pages_modules_table." (module_id, page_id, time) values (".$module_tags[$i]['page_id'].", ".$page_id.", ".$this->shifted_time().")";
									$result_check = $db->Execute($this->sql_query);
									if(!$result_check)
									{
										return false;
									}
								}

								// Print out the replace tag for the module
								$replace_tag = str_replace("<","&lt;",$module_tags[$i]['module_replace_tag']);
								$replace_tag = str_replace(">","&gt;",$replace_tag);
								if(sizeof($embedded) == 0)
								{
									$this->body .= "	<br>
														<font size=2 face=arial,helvetica>Embedded within:</font><Br><font font color=#0000FF size=2 face=arial,helvetica>".$replace_tag."</font>";
								}
								else
								{
									$this->body .= "	<br>
														<font color=#0000FF size=2 face=arial,helvetica>".$replace_tag."</font>";
								}

								// Finally put it in the embedded array
								$embedded[] = $module_tags[$i]['page_id'];
							}
						}

						$this->body .= "			\n</td>\n";
					}

					foreach ($language_id_array as $key => $value)
					{
						//echo $key." is the ".$value." and ".$language_name_array[$key]."- checking - ".$current_module["module_replace_tag"]."<bR>\n";
						$in_template = $this->check_module_in_template($db,$current_module["module_replace_tag"],$page_id,$value);
						if ($in_template)
						{
							$this->body .= "
													<td align=center>
														<font color=#33bb33 size=2 face=arial,helvetica><b>found</b></font>
													</td>";
						}
						elseif(array_search($current_module["page_id"], $embedded) !== false)
						{
							// Put embedded state if it is embedded
							// It is embedded in the module
							$this->body .= "
													<td align=center>
														<font color=#0000FF size=2 face=arial,helvetica><b>embedded</b></font>
													</td>";
						}
						else
						{
							$this->body .= "
													<td align=center>
														<font color=#bb3333 size=2 face=arial,helvetica><b>not found</b></font>
													</td>";
						}
					}
					$this->body .= "
													<td width=10%>
														<a href=index.php?a=44&z=2&b=".$page_id."&c=".$show_module["module_id"]."><span class=medium_font><img src=admin_images/btn_admin_remove.gif alt=remove border=0></span></a>
													</td>
												</tr>\n";
					$this->row_count++;
				}
				//$this->body .= "</td>\n</tr>\n";

				if ($this->pages_debug)
				{
					echo count($modules_in)." is count of modules_in<BR>\n";
				}
				//get modules to exclude from matching...because they are already attached to this page
				if (count($modules_in) > 0)
				{
					reset($modules_in);
					while (list($module_key,$module_value) = each($modules_in))
					{
						if (strlen($this->modules_not_in_statement) == 0)
						{
							$this->modules_not_in_statement = " and page_id not in (".$module_value;
						}
						else
						{
							$this->modules_not_in_statement .= ",".$module_value;
						}
					}
					$this->modules_not_in_statement .= ")";
				}
				else
				{
					$this->modules_not_in_statement = "";
				}
			}
			else
			{
				$this->body .= "
												<tr>
													<td colspan=100% class=medium_error_font>
														There are no modules currently attached.
													</td>
												</tr>";
			}
			$this->body .= "
											</table>
										</td>
									</tr>";
			$this->body .= "
									<tr align=center valign=middle>
										<td colspan=100%>
											<a href=index.php?a=44&z=6&b=".$page_id."><span class=medium_font_light><br><br><img src=admin_images/btn_admin_add_module.gif alt=add border=0></span></a>
										</td>
									</tr>";


			$parent_name = $this->get_page($db,$page_id);
			$this->body .= "
									<tr align=center valign=middle>
										<td colspan=100%>
											<a href=index.php?a=44&z=1&b=".$show["section_id"]."><span class=medium_font_light><img src=admin_images/btn_admin_page_admin.gif alt=pages border=0></span></a>
										</td>
									</tr>";

			//if (strlen($show["special_instructions"]) > 0)
				//$this->body .= "<tr class=row_color_black>\n\t<td colspan=".(2+$num_languages)." class=medium_font_light>".$show["special_instructions"]." </a>\n\t</td>\n</tr>\n";

			if (($page_id == 70) || ($page_id == 71))
			{
				//this is the sign or flyer page
				if ($page_id == 70)
				{
					$page_large_name = "Flyer";
					$page_name = "flyer";
				}
				else
				{
					$page_large_name = "Sign";
					$page_name = "sign";
				}
				$this->body .= "<table width=100% cellpadding=3 cellspacing=1>";
				$this->body .= "<tr class=row_color_black>\n\t<td colspan=".(2+$num_languages)." class=large_font_light><b>".$page_large_name." Fields Form</b> </td>\n</tr>\n";
				$this->body .= "<tr class=row_color_red>\n\t<td colspan=".(2+$num_languages)." class=medium_font_light>Choose which fields to use
					within your ".$page_name." page.   </td>\n</tr>\n";

				$this->input_radio_yes_no($show,'module_use_image','display image within sign');
				$this->input_sign_flyer_width_height($db,$page_id,$page);
				$this->input_module_display_title($page,1);
				$this->input_radio_yes_no($show,'module_display_price','display price of listing');
				$this->input_radio_yes_no($show,'module_display_phone1','display phone 1 field');
				$this->input_radio_yes_no($show,'module_display_phone2','display phone 2 field');
				$this->input_radio_yes_no($show,'module_display_contact','display contact info');
				$this->input_module_display_location($page,1);
				$this->input_radio_yes_no($show,'module_display_ad_description','display description');
				$this->input_radio_yes_no($show,'module_display_classified_id','display listing id');
				$this->input_module_display_optional_fields($page);
				if (!$this->admin_demo()) $this->body .= "
						<tr class=row_color2 align=center>
							<td colspan=2>
								<input type=submit name=save value=\"Save\">
							</td>
						</tr>";
			}
			elseif ($page_id==113)
			{
				$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
				// Set title and text for tooltip
				$this->body .= "Text[1] = [\"number of sellers to display on a page\", \"Set the number of sellers to display on any result page while browsing the sellers.\"]\n
					Text[2] = [\"number of category navigation columns to display on a page\", \"Set the columns the categories will display so the user can browse.\"]\n
					Text[3] = [\"display sub-category sellers\", \"Choosing \\\"\\\"yes\\\"\\\" will display the sellers of a current categories sub-categories while browsing the seller listing.\"]\n
					Text[4] = [\"display sellers category count\", \"Choosing \\\"yes\\\" will display the number of unique sellers of the categories listed.\"]\n
					Text[5] = [\"display category description\", \"Choosing \\\"yes\\\" will display the category description.\"]\n
					Text[6] = [\"display category image\", \"Choosing \\\"yes\\\" will display the category image next to the category name.\"]\n";

				//".$this->show_tooltip(6,1)."

				// Set style for tooltip
				//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
				$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
				$this->body .= "var TipId = \"tiplayer\"\n";
				$this->body .= "var FiltersEnabled = 1\n";
				$this->body .= "mig_clay()\n";
				$this->body .= "</script>";

				$this->body .= "
					<table width=100% cellpadding=0 cellspacing=0>
						<tr class=row_color_black>
							<td colspan=2 class=very_large_font_light>
								<b>Browse Seller Configuration Form</b>
							</td>
						</tr>
						<tr class=row_color_red>
							<td colspan=2 class=large_font_light>
								Choose how your seller browsing section will appear.
							</td>
						</tr>";

				$this->input_option_dropdown($show,'module_number_of_ads_to_display','number of sellers to display on a page'.$this->show_tooltip(1,1));
				$this->input_option_dropdown($show,'module_number_of_columns','number of category navigation columns to display on a page'.$this->show_tooltip(2,1));
				$this->input_radio_yes_no($show,'display_no_subcategory_message','display sub-category sellers'.$this->show_tooltip(3,1));
				$this->input_radio_yes_no($show,'display_category_count','display sellers category count'.$this->show_tooltip(4,1));
				$this->input_radio_yes_no($show,'display_category_description','display category description'.$this->show_tooltip(5,1));
				$this->input_radio_yes_no($show,'display_category_image','display category image'.$this->show_tooltip(6,1));
				$this->input_radio_yes_no($show,'module_display_business_type','display business type column');
				$this->input_radio_yes_no($show,'module_display_username','display username column');
				$this->input_radio_yes_no($show,'module_display_name','display name column');
				$this->input_radio_yes_no($show,'module_display_phone','display phone column');
				$this->input_radio_yes_no($show,'module_display_phone2','display phone 2 column');
				$this->input_module_display_location($page,1);
				$this->input_module_display_optional_fields($show);
				if (!$this->admin_demo()) $this->body .= "
						<tr class=row_color2 align=center>
							<td colspan=100%>
								<input type=submit name=save value=\"Save\">
							</td>
						</tr>";
			}
			elseif ($page_id>=135 && $page_id<=154)
			{
				$this->sql_query = "select * from ".$this->site_configuration_table;
				$configuration_result = $db->Execute($this->sql_query);
				if (!$configuration_result)
				{
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($configuration_result->RecordCount() == 1)
				{
					$this->configuration_data = $configuration_result->FetchRow();
				}
				else
					return false;
				$this->body .= "<tr align=center valign=middle>\n\t<td colspan=3 class=medium_font><b>To access this page you must use one of the urls below:</b><br><br>
						<a href=".$this->configuration_data["classifieds_url"]."?a=28&b=".$page_id."><span class=medium_font><font color=000000>".$this->configuration_data["classifieds_url"]."?a=28&b=".$page_id."</font></a><br><br>-- or --<br><br>
						".$this->configuration_data["classifieds_file_name"]."?a=28&b=".$page_id."\n\t</span></td>\n</tr>\n";

				$this->body .= "<table width=100% border=0 cellpadding=0 cellspacing=0>";
			}
			$this->body .= "
							</table>
						</form>";
		}
		else
		{
			//this is a module
			$this->edit_module_specifics_form($db,$page_id);
		}

		return true;
	} //end of function display_current_page

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function browse_sections($db,$section=0)
	{
		$this->title .= "Pages Management > Sections";
		$this->description = "Configure the pages on your site in this section of the admin. Navigate through the sections of the site to find the page
			you wish to configure.  You can then configure the fonts, text and template used in that page.";
		$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100% class=row_color1>\n";
		$this->search_text_form();
		//browse the listings in this category that are open

		if ($section)
		{
			$this->sql_query = "select * from ".$this->pages_sections_table." where section_id = ".$section;
			if($this->is_auctions())
				$this->sql_query .= " and (applies_to = 0 or applies_to = 2)";
			elseif($this->is_classifieds())
				$this->sql_query .= " and (applies_to = 0 or applies_to = 1)";
			$section_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$section_result)
			{
				return false;
			}
			elseif ($section_result->RecordCount() == 1)
			{
				$show_section_data = $section_result->FetchRow();
				$section_name = $show_section_data["name"];
				$section_description = $show_section_data["description"];
				$parent_section = $show_section_data["parent_section"];
			}
			else
			{
				//category does not exist
				$this->error_message = $this->messages["5500"];
				return false;
			}
		}
		else
		{
			$section_name = "Main";
			$section_description = "";
			$parent_section = 0;
		}

		$this->sql_query = "select * from ".$this->pages_sections_table." where parent_section = ".$section;
		if($this->is_auctions())
			$this->sql_query .= " and (applies_to = 0 or applies_to = 2)";
		elseif($this->is_classifieds())
			$this->sql_query .= " and (applies_to = 0 or applies_to = 1)";
		$this->sql_query .= " order by display_order";
		$sub_section_result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$sub_section_result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}
		else
		{
			if ($parent_section)
			{
				$parent_section_data = $this->get_section($db,$parent_section);
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=4>\n\t
					<a href=index.php?a=44&z=1&b=".$parent_section."><span class=medium_font_light> <font face=arial,helvetica size=2 color=#FFFFFF><b>back to ".$parent_section_data["name"]."</b></font></span></a>\n\t</td>\n</tr>\n";
			}
			elseif ($section != 0)
			{
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=4>\n\t
					<a href=index.php?a=44><span class=medium_font_light> <font face=arial,helvetica size=2 color=#FFFFFF><b>back to Main</b></font></span></a>\n\t</td>\n</tr>\n";
			}
			if ($sub_section_result->RecordCount() > 0)
			{
				//display subsections to this section
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=4 class=medium_font_light align=center>\n\t <b>sub sections of ".$section_name."</b> </a>\n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color_black>\n\t<td align=center width=45% class=medium_font_light><b>subsection name and description</b>\n\t</td>\n\t";
				$this->body .= "<td align=center width=25% class=medium_font_light>\n\t<b>subsections</b>\n\t</td>\n\t";
				$this->body .= "<td align=center width=25% class=medium_font_light>\n\t<b>subpages</b>\n\t</td>\n";
				$this->body .= "<td align=center width=5% class=medium_font_light>\n\t&nbsp;\n\t</td>\n</tr>";
				$this->row_count = 0;
				while ($show_sub_sections = $sub_section_result->FetchRow())
				{
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top>\n\t<a href=index.php?a=44&z=1&b=".$show_sub_sections["section_id"]."><span class=medium_font><font color=000000>".$show_sub_sections["name"]."</font></span></a><br><span class=small_font>";
					//$this->body .= $show_sub_sections["description"];
					$this->body .= "</span></td>\n\t";
					$this->body .= "<td align=center valign=top class=small_font>\n\t";

					$this->sql_query = "select * from ".$this->pages_sections_table." where parent_section = ".$show_sub_sections["section_id"];
					if($this->is_auctions())
						$this->sql_query .= " and (applies_to = 0 or applies_to = 2)";
					elseif($this->is_classifieds())
						$this->sql_query .= " and (applies_to = 0 or applies_to = 1)";
					$this->sql_query .= " order by display_order";
					$sub_section_sections_result = $db->Execute($this->sql_query);
					//echo $this->sql_query." is the query<br>\n";
					if (!$sub_section_sections_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[5501];
						return false;
					}
					elseif ($sub_section_sections_result->RecordCount() > 0)
					{
						while ($show_this_sub_section = $sub_section_sections_result->FetchRow())
						{
							$this->body .= $show_this_sub_section["name"]."<br>\n";
						}
					}
					else
					{
						$this->body .= "none";
					}
					$this->body .= " \n\t</td>\n\t";

					$this->body .= "<td align=center valign=top class=small_font>\n\t";
					//$this->sql_query = "select * from ".$this->pages_table." where section_id = ".$show_sub_sections["section_id"]." order by display_order";
					$this->sql_query = "select * from ".$this->pages_table." where section_id = ".$show_sub_sections["section_id"]." and module = 0";
					if($this->is_auctions())
						$this->sql_query .= " and (applies_to = 0 or applies_to = 2)";
					elseif($this->is_classifieds())
						$this->sql_query .= " and (applies_to = 0 or applies_to = 1)";
					$this->sql_query .= " order by page_id";
					$sub_pages_result = $db->Execute($this->sql_query);
					//echo $this->sql_query." is the query<br>\n";
					if (!$sub_pages_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[5501];
						return false;
					}
					elseif ($sub_pages_result->RecordCount() > 0)
					{
						while ($show_sub_pages = $sub_pages_result->FetchRow())
						{
							$this->body .= $show_sub_pages["name"]."<br>\n";
						}
					}
					else
					{
						$this->body .= "none";
					}
					$this->body .= " \n\t</td>\n\t";
					$this->body .= "<td align=center valign=top><a href=index.php?a=44&z=1&b=".$show_sub_sections["section_id"]."><span class=medium_font><img src=admin_images/btn_admin_enter.gif alt=enter border=0></span></a></td>\n\t";
					$this->body .= "</tr>\n";
					$this->row_count++;
				}
			}

			$this->sql_query = "select * from ".$this->pages_table." where section_id = ".$section." and module = 0";
			if($this->is_auctions())
				$this->sql_query .= " and (applies_to = 0 or applies_to = 2)";
			elseif($this->is_classifieds())
				$this->sql_query .= " and (applies_to = 0 or applies_to = 1)";
			$this->sql_query .= " order by page_id, name";
			$sub_pages_result = $db->Execute($this->sql_query);
			if (!$sub_pages_result)
			{
				return false;
			}
			elseif ($sub_pages_result->RecordCount() > 0)
			{
				//display subpages to this section
				$this->body .= "
					<tr>
						<td colspan=100%>
							<table width=100%>
								<tr class=row_color_red>
									<td align=left class=medium_font_light>
										<b>Page Name</b>
									</td>
									<td align=left width=50% class=medium_font_light>
										<b>Admin Note</b>
									</td>
									<td width=20%>&nbsp;</td>
								</tr>";
				$this->row_count = 0;
				while ($show_sub_pages = $sub_pages_result->FetchRow())
				{
					$this->body .= "
								<tr class=".$this->get_row_color().">
									<td valign=top>
										<a href=index.php?a=44&z=3&b=".$show_sub_pages["page_id"].">
											<span class=medium_font>
												<font color=000000>".$show_sub_pages['name']."</font>
											</span>
										</a>
									</td>
									<td class=medium_font align=left>
										".$show_sub_pages['admin_label']."<br>
									</td>
									<td align=center valign=top>
										<a href=index.php?a=44&z=3&b=".$show_sub_pages["page_id"].">
											<span class=medium_font>
												<img src=admin_images/btn_admin_edit.gif alt=edit border=0>
											</span>
										</a>
									</td>
								</tr>\n";
					$this->row_count++;
				}
				$this->body .= "
							</table>
						</td>
					</tr>";
			}
		}
		$this->body .= "</table>\n";
		return true;
	} //end of function browse_sections

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function edit_template_attachment($db,$page_id=0,$language_id=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"template to attach\", \"This is the template that is used to display the overall page content including the html for your header, footer, and menu columns if applicable. It will also contain the MAINBODY tag, which is used to pull and display any information associated with the remaining templates indicated below.\"]\n
			Text[2] = [\"details template\", \"This template displays the details of the listings wherever the MAINBODY tag is placed within the Template to Attach template you have designated above. You have the ability to insert the various tags displayed further down on this page within the Tag Verification Tool for this template. You will notice in the available tags, that there are two tags called EXTRA_QUESTION_BLOCK and EXTRA_CHECKBOX_BLOCK. Each of these tags will display their associated html based upon the next two templates you select below.\"]\n
			Text[3] = [\"extra display question template\", \"This template allows you to display Optional Site Wide Fields within the Details Template you have assigned above.\"]\n
			Text[4] = [\"extra question checkbox display template\",\"This template allows you to display the extra questions that you have designated as having a question type of \\\"Checkbox\\\". It will be displayed within the Details Template you have assigned above.\"]\n
			Text[5] = [\"full size image display template\",\"This template will replace the MAINBODY tag in the page template above.  Make sure that the MAINBODY tag in the template above is placed between an html table start and stop tags.  The full-size template you create (with the FULL_SIZE_IMAGE and FULL_SIZE_TEXT tags below) is suggested to be created as a whole html table row.  For each image a table row will be created within the table tags you create.\"]\n";
		//".$this->show_tooltip(5,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		if (($page_id) && ($language_id))
		{
			$this->sql_query = "select * from ".$this->templates_table." order by name";
			$templates_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$templates_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($templates_result->RecordCount() > 0)
			{
				$page = $this->get_page($db,$page_id);

				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=44&z=5&b=".$page_id."&c=".$language_id." method=post>\n";
				$this->body .= "<table cellpadding=2 cellspacing=0 border=0 width=100% class=row_color1>\n";
				$this->title = "Pages Management";
				$this->description = "The template(s) indicated below will be used to create your Page. If there is more than one
				template entry, then this means that this page allows for multiple templates used in combination to assemble
				the page contents. For example, the Listing Display Page is comprised of 4 templates used in combination to create
				the page displayed to your users. Please review this software's User Manual for a pictorial reference of the Listing
				Display Page's unique layout";

				$this->sql_query = "select * from ".$this->pages_languages_table;
				$language_result = $db->Execute($this->sql_query);
				$row_color = 0;
				while($show_languages = $language_result->FetchRow())
				{
					if($row_color % 2)
						$css_tag = "row_color1";
					else
						$css_tag = "row_color2";

					$this->sql_query = "select * from ".$this->templates_table." order by name";
					$templates_result = $db->Execute($this->sql_query);

					$page_template = $this->get_page_language_template($db,$page_id,$show_languages["language_id"]);

					$this->body .= "<tr class=".$css_tag."><td align=right class=medium_font width=40%>Page Name:</td>\n\t";
					$this->body .= "<td align=left class=medium_font>".$page["name"]."\n\t</td></tr>\n";

					$this->body .= "<tr class=".$css_tag."><td align=right class=medium_font width=40%>Language:</td>\n\t";
					$this->body .= "<td class=medium_font>".$this->get_language_name($db,$show_languages["language_id"])."\n\t</td></tr>\n";

					$this->body .= "<tr class=".$css_tag."><td align=right class=medium_font width=40%>Overall Page Template to Attach:".$this->show_tooltip(1,1)."</td>\n\t";
					$this->body .= "<td class=medium_font><select name=d[language".$show_languages["language_id"]."]>\n\t";
					$this->body .= "<option value=0>None</option>\n";
					while ($show_templates = $templates_result->FetchRow())
					{
						$this->body .= "<option value=".$show_templates["template_id"];
						if ($show_templates["template_id"] == $page_template)
						{
							$this->body .= " selected";
							$template_code = $show_templates["template_code"];
						}
						$this->body .= ">".$show_templates["name"]." - ".$this->get_language_name($db,$show_templates["language_id"])."</option>\n";
					}
					$this->body .= "</select></td>\n</tr>";

					$row_color++;
				}

				if($page_id == 1)
				{
					$this->sql_query = "select user_ad_template, user_extra_template, user_checkbox_template, auctions_user_ad_template, auctions_user_extra_template, auctions_user_checkbox_template from ". $this->ad_configuration_table;
					$result = $db->Execute($this->sql_query);
					if(!$result)
						return false;
					else
						$misc_templates = $result->FetchRow();

					if($this->is_class_auctions() || $this->is_classifieds())
					{
						$this->body .= "<tr class=".$css_tag."><td bgcolor=000066 colspan=2 align=center><font class=medium_font_light><b>Ad Display Page - Templates Used</b></font></td></tr>\n\t";
						$this->body .= "<tr class=row_color1 ><td align=right class=medium_font>\n\tAd Details Template:".$this->show_tooltip(2,1)."</td>";
						$sql_query = "select name,template_id, template_code from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=d[user_ad_template]>\n\t\t";
						$templates_result = $db->Execute($sql_query);
						if (!$templates_result)
						{
							$this->error_message = $this->messages[3501];
							return false;
						}
						elseif ($templates_result->RecordCount() > 0)
						{
							while ($show_template = $templates_result->FetchRow())
							{
								$this->body .= "<option value=\"".$show_template["template_id"]."\"";
								if ($show_template["template_id"] == $misc_templates["user_ad_template"])
								{
									$this->body .= " selected";
									$ad_detail = $show_template["name"];
									$ad_detail_code = $show_template["template_code"];
								}
								$this->body .= ">".$show_template["name"]."</option>\n\t";
							}
						}
						$this->body .= "</select></td></tr>";

						$this->body .= "<tr class=row_color2 ><td align=right class=medium_font>\n\tAd Extra Question Template:".$this->show_tooltip(3,1)."</td>";
						$sql_query = "select name,template_id, template_code from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=d[user_extra_template]>\n\t\t";
						$templates_result = $db->Execute($sql_query);
						if (!$templates_result)
						{
							$this->error_message = $this->messages[3501];
							return false;
						}
						elseif ($templates_result->RecordCount() > 0)
						{
							while ($show_template = $templates_result->FetchRow())
							{
								$this->body .= "<option value=\"".$show_template["template_id"]."\"";
								if ($show_template["template_id"] == $misc_templates["user_extra_template"])
								{
									$this->body .= " selected";
									$extra = $show_template["name"];
									$extra_code = $show_template["template_code"];
								}
								$this->body .= ">".$show_template["name"]."</option>\n\t";
							}
						}
						$this->body .= "</select></td></tr>";

						$this->body .= "<tr class=row_color1 ><td align=right class=medium_font>\n\tAd Checkbox Template:".$this->show_tooltip(4,1)."</td>";
						$sql_query = "select name,template_id, template_code from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=d[user_checkbox_template]>\n\t\t";
						$templates_result = $db->Execute($sql_query);
						if (!$templates_result)
						{
							$this->error_message = $this->messages[3501];
							return false;
						}
						elseif ($templates_result->RecordCount() > 0)
						{
							while ($show_template = $templates_result->FetchRow())
							{
								$this->body .= "<option value=".$show_template["template_id"];
								if ($show_template["template_id"] == $misc_templates["user_checkbox_template"])
								{
									$this->body .= " selected ";
									$checkbox = $show_template["name"];
									$checkbox_code = $show_template["template_code"];
								}
								$this->body .= ">".$show_template["name"]."</option>\n\t";
							}
						}
						$this->body .= "</select></td></tr>";
					}

					if($this->is_class_auctions() || $this->is_auctions())
					{
						//
						//auctions
						//
						$this->body .= "<tr class=".$css_tag."><td bgcolor=000066 colspan=2 align=center><font class=medium_font_light><b>Auction Display Page - Templates Used</b></font></td></tr>\n\t";
						$this->body .= "<tr class=row_color2 ><td align=right class=medium_font>\n\tAuction Details Template:".$this->show_tooltip(2,1)."</td>";
						$sql_query = "select name,template_id, template_code from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=d[auctions_user_ad_template]>\n\t\t";
						$templates_result = $db->Execute($sql_query);
						if (!$templates_result)
						{
							$this->error_message = $this->messages[3501];
							return false;
						}
						elseif ($templates_result->RecordCount() > 0)
						{
							while ($show_template = $templates_result->FetchRow())
							{
								$this->body .= "<option value=\"".$show_template["template_id"]."\"";
								if ($show_template["template_id"] == $misc_templates["auctions_user_ad_template"])
								{
									$this->body .= " selected";
									$auction_detail = $show_template["name"];
									$auction_detail_code = $show_template["template_code"];
								}
								$this->body .= ">".$show_template["name"]."</option>\n\t";
							}
						}
						$this->body .= "</select></td></tr>";

						$this->body .= "<tr class=row_color1 ><td align=right class=medium_font>\n\tAuction Extra Question Template:".$this->show_tooltip(3,1)."</td>";
						$sql_query = "select name,template_id, template_code from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=d[auctions_user_extra_template]>\n\t\t";
						$templates_result = $db->Execute($sql_query);
						if (!$templates_result)
						{
							$this->error_message = $this->messages[3501];
							return false;
						}
						elseif ($templates_result->RecordCount() > 0)
						{
							while ($show_template = $templates_result->FetchRow())
							{
								$this->body .= "<option value=\"".$show_template["template_id"]."\"";
								if ($show_template["template_id"] == $misc_templates["auctions_user_extra_template"])
								{
									$this->body .= " selected";
									$extra = $show_template["name"];
									$auction_extra_code = $show_template["template_code"];
								}
								$this->body .= ">".$show_template["name"]."</option>\n\t";
							}
						}
						$this->body .= "</select></td></tr>";

						$this->body .= "<tr class=row_color2 ><td align=right class=medium_font>\n\tAuction Checkbox Template:".$this->show_tooltip(4,1)."</td>";
						$sql_query = "select name,template_id, template_code from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=d[auctions_user_checkbox_template]>\n\t\t";
						$templates_result = $db->Execute($sql_query);
						if (!$templates_result)
						{
							$this->error_message = $this->messages[3501];
							return false;
						}
						elseif ($templates_result->RecordCount() > 0)
						{
							while ($show_template = $templates_result->FetchRow())
							{
								$this->body .= "<option value=".$show_template["template_id"];
								if ($show_template["template_id"] == $misc_templates["auctions_user_checkbox_template"])
								{
									$this->body .= " selected ";
									$checkbox = $show_template["name"];
									$auction_checkbox_code = $show_template["template_code"];
								}
								$this->body .= ">".$show_template["name"]."</option>\n\t";
							}
						}
						$this->body .= "</select></td></tr>";
					}
				}
				elseif($page_id == 43)
				{
					// Spacer -- Remove when updated
					$this->body .= "<tr class=row_color_black><td colspan=2></td></tr>\n\t";

					$this->sql_query = "select * from ".$this->site_configuration_table;
					$result = $db->Execute($this->sql_query);
					if ($this->pages_debug) echo  $this->sql_query."<br>\n";
					if (!$result)
					{
						if ($this->pages_debug) echo  $this->sql_query."<br>\n";
						$this->site_error($db->ErrorMsg());
						return false;
					}
					else
						$show = $result->FetchRow();

					// Ad display template
					$this->body .= "<tr class=row_color1>\n\t<td align=right class=medium_font width=40%>\n\tUser Management Home Template:</td>\n\t";
					$this->sql_query = "select name,template_id, template_code from ".$this->templates_table." order by name";
					if($this->pages_debug) echo $this->sql_query.'<br>';
					$this->body .= "<td><select name=d[user_management_template]>\n\t\t";
					$extra_template_result = $db->Execute($this->sql_query);
					if ($this->pages_debug) echo  $this->sql_query."<br>\n";
					if (!$extra_template_result)
					{
						if ($this->pages_debug) echo  $this->sql_query."<br>\n";
						$this->error_message = $this->messages[3501];
						return false;
					}
					elseif ($extra_template_result->RecordCount() > 0)
					{
						$this->body .= "<option value=0>None</option>\n\t";
						while ($show_template = $extra_template_result->FetchRow())
						{
							$this->body .= "<option value=".$show_template["template_id"];
							if ($show_template["template_id"] == $show["home_template"])
							{
								$user_template = $show_template["name"];
								$user_code = $show_template["template_code"];
								$this->body .= " selected";
							}
							$this->body .= ">".$show_template["name"]."</option>\n\t";
						}
					}
					$this->body .= "</select></td></tr>";
				}
				elseif($page_id == 84)
				{
					$this->body .= "<tr class=row_color_black><td colspan=2></td></tr>\n\t";
					$this->sql_query = "select * from ".$this->ad_configuration_table;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						$this->site_error($db->ErrorMsg());
						return false;
					}
					elseif ($result->RecordCount() == 1)
					{
						$show = $result->FetchRow();
						$this->body .= "<tr  class=row_color2>\n\t<td valign=top align=right class=medium_font>\n\t
							ad full size image display template:".$this->show_tooltip(5,1)."\n\t</td>\n\t";
						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=d[user_template]>\n\t\t";
						$category_template_result = $db->Execute($this->sql_query);
						if (!$category_template_result)
					 	{
							$this->site_error($db->ErrorMsg());
							return false;
					 	}
					 	elseif ($category_template_result->RecordCount() > 0)
					 	{
					 		$this->body .= "<option value=0>None</option>\n\t";
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "<option value=".$show_template["template_id"];
								if ($show_template["template_id"] == $show["full_size_image_template"])
								{
									$template_code = $show_template['template_code'];
									$this->body .= " selected ";
								}
								$this->body .= ">".$show_template["name"]."</option>\n\t";
							}
			 			}
						$this->body .= "</select></td></tr>";
						$this->body .= "<tr>\n\t<td valign=top colspan=2 class=medium_font>\n\t
							Below is the list of template fields possible for the listing html table.  Place the fields below within the table
							where you would like them to appear.  Next to each field is an explanation of what that field represents.
							The data will be replaced on the fly by this Application.</font>\n\t</td>\n</tr>\n";

						$data = array(	"FULL_SIZE_IMAGE" 	=>	"The full size image.",
										"FULL_SIZE_TEXT"	=>	"The text attached to the full size image.");
						$this->display_check($data, $template_code);
					}
					else
					{
						return false;
					}
				}

				// Display Submit button
				if (!$this->admin_demo()) $this->body .= "<tr><td align=center colspan=2 class=medium_font><input type=submit name=submit value=\"Save\"></td></tr>";
				$this->body .= "<tr>\n\t<td colspan=2><a href=index.php?a=44&z=3&b=".$page_id."><span class=medium_font><b>back to ".$page["name"]."</b></span></a></td>\n</tr>\n";
				$this->body .= "</form>\n";

				if($page_id == 1)
				{
					$this->body .= "</table>";
					$this->body .= "<table>";

					// Tag verification tools
					$this->body .= "<tr><td colspan=3 class=row_color_red></td></tr>";

					$this->body .= "<tr class=".$css_tag."><td class=row_color_red colspan=3 align=center><font class=large_font_light>Tag Verification Tool</font></td></tr>\n\t";
					$this->body .= "<tr class=".$css_tag."><td class=medium_font colspan=3>The Tag Verification Tool helps you to verify the existence of template tags
						(item within brackets) that can be used within each of the templates you have chosen above. When these tags
						are used within their respective template, the tag will display information by pulling it directly from
						the database. In most cases, these tags will simply display a \"label\" name that you have defined, and
						a \"value\" name that the seller defines when placing their listing. The system automatically searches the
						templates you have assigned at the top of this page and returns a result for each tag. The search result
						will either be <font color=#33dd33 size=2 face=arial,helvetica><b>found</b></font> or
						<font color=#bb3333 size=2 face=arial,helvetica><b>not found</b></font>. If there is a tag that displays
						a <font color=#bb3333 size=2 face=arial,helvetica><b>not found</b></font> result, but you want it to
						show up within the ".$page["name"].", simply insert that tag (item within brackets) into its appropriate
						template. IMPORTANT: When placing these tags in the html of your template, you must also go to the
						LISTING SETUP > FIELDS TO USE page of this admin and set each of them to \"use\".</td></tr>\n\t";

					$this->body .= "<tr bgcolor=000066><td align=center><font class=medium_font_light align=center><b>Available Tags:</b></font></td>\n\t";
					if($this->is_class_auctions() || $this->is_classifieds())
						$this->body .= "<td align=center><font class=medium_font_light><b>Ad Details<br>Template</b></font></td>";
					if($this->is_class_auctions() || $this->is_auctions())
						$this->body .= "<td align=center><font class=medium_font_light><b>Auction Details<br>Template</b></font></td>\n\t";
					$this->body .= "</tr>\n\t";

					$data = array(	"CATEGORY_TREE"	=>	"The current category as well as links to parent categories this listing is in.<bR><b>not used on affiliate side</b>",
									"TITLE"	=>	"The title of the listing.",
									"CLASSIFIED_ID_LABEL"	=>	"Label text for the listing id.",
									"CLASSIFIED_ID"	=>	"The listing id data.",
									"VIEWED_COUNT_LABEL"	=>	"Label text for the count of the number of times the listing has been viewed.",
									"VIEWED_COUNT"	=>	"The data for the number of times the listing has been viewed.",
									"SELLER_LABEL"	=>	"Label text for the sellers username field.",
									"SELLER"	=>	"Sellers username data.",
									"MEMBER_SINCE"	=>	"This is the date the seller first registered.",
									"DATE_STARTED_LABEL"	=>	"Label text for the date the listing was started.",
									"DATE_STARTED"	=>	"Data for the date the listing was started.",
									"CITY_LABEL"	=>	"Label text for the city.",
									"CITY_DATA"	=>	"City data for this listing.",
									"STATE_LABEL"	=>	"Label text for the state.",
									"STATE_DATA"	=>	"State data for this listing.",
									"COUNTRY_LABEL"	=>	"Label text for the country.",
									"COUNTRY_DATA"	=>	"Country data for this listing.",
									"ZIP_LABEL"	=>	"Label text for the zip.",
									"ZIP_DATA"	=>	"Zip data for this listing.",
									"PRICE_LABEL"	=>	"Label text for the price.",
									"PRICE"	=>	"Price and currency information for this listing.",
									"PUBLIC_EMAIL_LABEL"	=>	"Label text for the publically exposed email (if shown).",
									"PUBLIC_EMAIL"	=>	"Publically exposed email data for this listing (if shown).",
									"PHONE_LABEL"	=>	"Label text for the phone (if shown).",
									"PHONE_DATA"	=>	"Phone data for this listing (if shown).",
									"PHONE2_LABEL"	=>	"Label text for the phone 2 (if shown).",
									"PHONE2_DATA"	=>	"Phone 2 data for this listing (if shown).",
									"FAX_LABEL"	=>	"Label text for the fax label (if shown).",
									"FAX_DATA"	=>	"Fax data for this ad (if shown).",
									"URL_LINK_1"	=>	"This is the link text allowing the user to link to the url within the data field.  The text (html,image) of the link is determined within the listing display page text administration.  The link the client inserts into the field while placing an image will determine where the link goes to.",
									"URL_LINK_2"	=>	"This is the link text allowing the user to link to the url within the data field.  The text (html,image) of the link is determined within the listing display page text administration.  The link the client inserts into the field while placing an image will determine where the link goes to.",
									"URL_LINK_3"	=>	"This is the link text allowing the user to link to the url within the data field.  The text (html,image) of the link is determined within the listing display page text administration.  The link the client inserts into the field while placing an image will determine where the link goes to.",
									"DESCRIPTION_LABEL"	=>	"Label text for the description.",
									"DESCRIPTION"	=>	"Description data for this listing.",
									"EXTRA_QUESTION_BLOCK"	=>	"Where the specific block of category specific questions will be placed.  You can configure the extra question block in this same section under template questions.",
									"EXTRA_CHECKBOX_BLOCK"	=>	"Where the specific block of category specific checkbox questions will be placed.  You can configure the extra question block in this same section under template questions.",
									"IMAGE_BLOCK"	=>	"The images for this listing.  This is the block of all images attached to this listing.  You can configure the number of columns the images should be and the size of the images through the admin tool in <b>listing configuration &gt; images configure</b>.",
									"LEAD_PICTURE"	=>	"Placing this tag will display the first image attached to the listing by itself within the listing display template.",
									"NOTIFY_FRIEND_LINK"	=>	"This is the link allowing the client to send a notification to a friend about the current listing.",
									"MESSAGE_TO_SELLER_LINK"	=>	"This is the link allowing the client to send a message to the seller of the listing.",
									"FAVORITES_LINK"	=>	"This is the link text allowing the user to add the current listing to their favorites list in their user home page.<br><b>not used on affiliate side</b>",
									"SELLERS_OTHER_ADS_LINK"	=>	"This is the link text allowing the user to view the current sellers other listings.<br><b>not used on affiliate side</b>",
									"SHOW_AD_VOTE_COMMENTS_LINK"	=>	"This is the link text allowing the user to view the votes and comments attached to the current listing.<br><b>not used on affiliate side</b>",
									"VOTE_ON_AD_LINK"	=>	"This is the link text allowing the user to vote and leave comments about the current listing.<br><b>not used on affiliate side</b>",
									"FULL_IMAGES_LINK"	=>	"This is the link text allowing the user to view all images at their full size on the same page.",
									"PRINT_FRIENDLY_LINK"	=>	"This is the link text allowing the user to view the listing details page in a page that is more	print friendly.",
									"SPONSORED_BY"	=>	"This is where the sponsored by html is placed within sellers listings where that sellers group has \"sponsered by\" html has been placed.  If none of your groups use the sponsored by html fields this tag does not need to be placed within the listing display template.",
									"PREVIOUS_AD_LINK"	=>	"Link that will display in the listing allowing the user to see the previous listing within the category.",
									"NEXT_AD_LINK"	=>	"Link that will display in the listing allowing the user to see the next listing within the category.",
									"RETURN_TO_LIST_LINK"	=>	"Return user to affiliated listing list - <br><b>only used on affiliate side</b>",
									"MAPPING_LINK"	=>	"Mapquest link to create a map to the location entered for this listing."
									);
					for($i = 1; $i < 21; $i++)
					{
						$data["OPTIONAL_FIELD_".$i."_LABEL"] = "";
						$data["OPTIONAL_FIELD_".$i] = "";
					}
					$this->display_check($data, $ad_detail_code, $auction_detail_code);

					if($this->is_class_auctions() || $this->is_auctions())
					{
						//AUCTION SPECIFIC TAGS
						$this->body .= "<tr bgcolor=000066>\n\t<td colspan=3 class=medium_font_light align=center>\n\t<b>Auction Specific Tags</b> - to be used within Auction Details Template</td></tr>\n\t";

						$data = array(	"BID_HISTORY_LINK"	=>	"This is the link text allowing the user to view the bid history of the current auction.  You can turn off and on the visibility of this link while the auction is live within the admin.  The link will be visible when the auction ends",
										"PAYMENT_OPTIONS_LABEL"	=>	"This is the lavle for the payment options the seller will accept for this auction.</b></span>",
										"RESERVE"	=>	"<span class=medium_font>\n\tThis is the reserve price of the item. </span>",
										"HIGH_BIDDER_LABEL"	=>	"<span class=medium_font>\n\tLabel text for the high bidder.</span>",
										"HIGH_BIDDER"	=>	"<span class=medium_font>\n\tThis is the high bidder's user id.</span>",
										"WINNING_DUTCH_BIDDERS_LABEL"	=>	"<span class=medium_font>\n\tLabel text for the winning bidder on a dutch auction.</span>",
										"WINNING_DUTCH_BIDDERS"	=>	"This is the winning bidder's user id on a dutch auction.",
										"NUM_BIDS"	=>	"This is the number of bids on the item.",
										"NUM_BIDS_LABEL"	=>	"Label text for the number of bids.",
										"QUANTITY_LABEL"	=>	"Label for the item quantity.",
										"QUANTITY"	=>	"This is the quantity of items.",
										"AUCTION_TYPE_LABEL"	=>	"Label text for the auction type.",
										"AUCTION_TYPE_DATA"	=>	"This is the auction type.",
										"AUCTION_TYPE_HELP"	=>	"Help link for the auction types.",
										"BUY_NOW_LABEL"	=>	"Label text for buy now.",
										"BUY_NOW_DATA"	=>	"This is the buy now data.",
										"BUY_NOW_LINK"	=>	"This is the link to buy now.",
										"DATE_ENDED_LABEL"	=>	"Label text for date ended.",
										"DATE_ENDED"	=>	"This is the date ended and appears after the auction has been closed.",
										"TIME_REMAINING_LABEL"	=>	"Label text for time left.",
										"TIME_REMAINING"	=>	"This is the time left in the current auction.",
										"MINIMUM_LABEL"	=>	"Label text for minimum bid.",
										"MINIMUM_BID"	=>	"This is the minimum bid.",
										"STARTING_LABEL"	=>	"Label text for the starting bid.",
										"STARTING_BID"	=>	"This is the starting bid.",
										"SELLER_RATING_LABEL"	=>	"Label text for the seller rating.",
										"SELLER_RATING"	=>	"This is the seller rating.",
										"FEEDBACK_LINK"	=>	"This is the link to the feedback page.",
										"SELLER_NUMBER_RATES_LABEL"	=>	"Label text for the seller's feedback score.",
										"SELLER_NUMBER_RATES"	=>	"This is the seller's feedback score.",
										"SELLER_RATING_SCALE_EXPLANATION"	=>	"Explanations of seller rating scale.",
										"BID_START_DATE_LABEL"	=>	"This displays label for the date the auction will start if there is a date set for this auction.",
										"BID_START_DATE"	=>	"This displays the date the auction will start if there is a date set for this auction.",
										"MAKE_BID_LINK"	=>	"This displays the link to make a bid on the auction.  It is only displayed when the auction is live.",
										);
						$this->display_check($data, $ad_detail_code, $auction_detail_code);
					}

					//REGISTRATION TAGS
					$this->body .= "<tr bgcolor=000066>\n\t<td colspan=3 class=medium_font_light align=center>\n\t<b>Registration Information Tags</b> - to be used within Ad or Auctions Details Templates</td></tr>\n\t";
					$data = array(	"SELLER_FIRST_NAME"	=>	"Sellers first name pulled from sellers registration data.",
									"SELLER_LAST_NAME"	=>	"Sellers last name pulled from sellers registration data.",
									"SELLER_URL"	=>	"Sellers url pulled from sellers registration data.",
									"SELLER_ADDRESS"	=>	"Sellers address (line 1 and 2) pulled from sellers registration data.",
									"SELLER_CITY"	=>	"Sellers city pulled from sellers registration data.",
									"SELLER_STATE"	=>	"Sellers state pulled from sellers registration data.",
									"SELLER_COUNTRY"	=>	"Sellers country pulled from sellers registration data.",
									"SELLER_ZIP"	=>	"Sellers zip pulled from sellers registration data.",
									"SELLER_PHONE"	=>	"Sellers phone pulled from sellers registration data.",
									"SELLER_PHONE2"	=>	"Sellers phone2 pulled from sellers registration data.",
									"SELLER_FAX"	=>	"Sellers fax pulled from sellers registration data.",
									"SELLER_COMPANY_NAME"	=>	"Sellers company name pulled from sellers registration data."
									);

					for($i = 1; $i < 11; $i++)
					{
						$data["SELLER_OPTIONAL_".$i] = "Sellers optional ".$i." pulled from sellers registration data.";
					}

					$this->display_check($data, $ad_detail_code, $auction_detail_code);

					// Extra questions template
					$this->body .= "<tr bgcolor=000066>\n\t<td colspan=3 class=medium_font_light align=center>\n\t<b>Extra Question Tags</b> - to be used in Ad or Auction Extra Question Templates</td></tr>\n\t";
					$data = array(	"EXTRA_QUESTION_NAME"	=>	"The name attached to the data field.",
									"EXTRA_QUESTION_VALUE"	=>	"The data value saved in this data field.");
					$this->display_check($data, $extra_code, $auction_extra_code);

					$this->body .= "<tr bgcolor=000066>\n\t<td colspan=3 class=medium_font_light align=center>\n\t<b>CheckBox Tags</b> - to be used in Ad or Auction Checkbox Templates</td></tr>\n\t";
					$data = array(	"EXTRA_CHECKBOX_NAME"	=>	"The name attached to the data field.");
					$this->display_check($data, $checkbox_code, $auction_checkbox_code);
				}
				elseif($page_id == 43)
				{
					// Tag verification tools
					$this->body .= "<tr><td colspan=2 class=row_color_red></td></tr>";

					$this->body .= "<tr class=".$css_tag."><td class=row_color_red colspan=3><font class=large_font_light>Tag Verification Tool</font></td></tr>\n\t";
					$this->body .= "<tr class=".$css_tag."><td class=medium_font colspan=3>The Tag Verification Tool helps you to verify the existence of template tags
						(item within brackets) that can be used within each of the templates you have chosen above. When these tags
						are used within their respective template, the tag will display information by pulling it directly from
						the database. In most cases, these tags will simply display a \"label\" name that you have defined, and
						a \"value\" name that the seller defines when placing their listing. The system automatically searches the
						templates you have assigned at the top of this page and returns a result for each tag. The search result
						will either be <font color=#33dd33 size=2 face=arial,helvetica><b>found</b></font> or
						<font color=#bb3333 size=2 face=arial,helvetica><b>not found</b></font>. If there is a tag that displays
						a <font color=#bb3333 size=2 face=arial,helvetica><b>not found</b></font> result, but you want it to
						show up within the Listing Display Page, simply insert that tag (item within brackets) into its appropriate
						template. IMPORTANT: When placing these tags in the html of your template, you must also go to the
						LISTING SETUP > FIELDS TO USE page of this admin and set each of them to \"use\".</td></tr>\n\t";

					$this->body .= "<tr class=row_color2><td colspan=2><font class=large_font>Tag Verification for the ".$user_template." template below.</font></td></tr>\n\t";
					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;SECTION_TITLE&gt;&gt;<br>";
					$this->body .= "Title for this section.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<SECTION_TITLE>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;PAGE_TITLE&gt;&gt;<br>";
					$this->body .= "The title of the page.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<PAGE_TITLE>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;DESCRIPTION&gt;&gt;<br>";
					$this->body .= "Description for this page.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<DESCRIPTION>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;ACTIVE_ADS&gt;&gt;<br>";
					$this->body .= "Link to the users currently active listings.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<ACTIVE_ADS>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;EXPIRED_ADS&gt;&gt;<br>";
					$this->body .= "Link to the users recently expired listings.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<EXPIRED_ADS>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;CURRENT_INFO&gt;&gt;<br>";
					$this->body .= "Link to the users information.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<CURRENT_INFO>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;PLACE_AD&gt;&gt;<br>";
					$this->body .= "Link to List an Item.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<PLACE_AD>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;FAVORITES&gt;&gt;<br>";
					$this->body .= "Link to users favorite listings.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<FAVORITES>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;COMMUNICATIONS&gt;&gt;<br>";
					$this->body .= "Link to user's communications.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<COMMUNICATIONS>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;COMMUNICATIONS_CONFIG&gt;&gt;<br>";
					$this->body .= "Link to user's communication configuration.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<COMMUNICATIONS_CONFIG>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;SIGNS_AND_FLYERS&gt;&gt;<br>";
					$this->body .= "Link to signs and flyers page.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<SIGNS_AND_FLYERS>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;RENEW_EXTEND_SUBSCRIPTION&gt;&gt;<br>";
					$this->body .= "Link to renew/extend subscription.<br>
						<b>Will only appear if user is a member of a subscription-based price plan.</b>\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<RENEW_EXTEND_SUBSCRIPTION>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;ADD_MONEY_WITH_BALANCE&gt;&gt;<br>";
					$this->body .= " Link to add money to your account. <b>This will also display the account balance beside it.</b>\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<ADD_MONEY_WITH_BALANCE>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;ADD_MONEY&gt;&gt;<br>";
					$this->body .= "Link to add money to your account.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<ADD_MONEY>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;BALANCE_TRANSACTIONS&gt;&gt;<br>";
					$this->body .= "Link to display all balance transactions that have happened to the clients site balance.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<BALANCE_TRANSACTIONS>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;PAID_INVOICES&gt;&gt;<br>";
					$this->body .= "Link to display all paid invoices if the invoice system is used.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<PAID_INVOICES>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;UNPAID_INVOICES&gt;&gt;<br>";
					$this->body .= "Link to display all unpaid invoices if the invoice system is used.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($user_code, "<<UNPAID_INVOICES>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					if($this->is_class_auctions())
					{
						$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
							&lt;&lt;FEEDBACK&gt;&gt;<br>";
						$this->body .= "Link to the Feedback Management system.\n\t</td>\n";
						$this->body .= "<td align=center valign=middle>";
						if(strstr($user_code, "<<FEEDBACK>>"))
							$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
						else
							$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
						$this->body .= "</td>\n\t</tr>\n";

						$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
							&lt;&lt;CURRENT_BIDS&gt;&gt;<br>";
						$this->body .= "Link to the user's current bids.\n\t</td>\n";
						$this->body .= "<td align=center valign=middle>";
						if(strstr($user_code, "<<CURRENT_BIDS>>"))
							$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
						else
							$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
						$this->body .= "</td>\n\t</tr>\n";

						$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
							&lt;&lt;BLACKLIST_BUYERS&gt;&gt;<br>";
						$this->body .= "Link to the user's blacklist.\n\t</td>\n";
						$this->body .= "<td align=center valign=middle>";
						if(strstr($user_code, "<<BLACKLIST_BUYERS>>"))
							$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
						else
							$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
						$this->body .= "</td>\n\t</tr>\n";

						$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
							&lt;&lt;INVITED_BUYERS&gt;&gt;<br>";
						$this->body .= "Link to the user's invited list.\n\t</td>\n";
						$this->body .= "<td align=center valign=middle>";
						if(strstr($user_code, "<<INVITED_BUYERS>>"))
							$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					}
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";
				}
				elseif($page_id == 73)
				{
					// Flyers
					// Tag verification tools
					$this->body .= "<tr><td colspan=2 class=row_color_red></td></tr>";

					$this->body .= "<tr class=".$css_tag."><td class=row_color_red colspan=3><font class=large_font_light>Tag Verification Tool</font></td></tr>\n\t";
					$this->body .= "<tr class=".$css_tag."><td class=medium_font colspan=3>The Tag Verification Tool helps you to verify the existence of template tags
						(item within brackets) that can be used within each of the templates you have chosen above. When these tags
						are used within their respective template, the tag will display information by pulling it directly from
						the database. In most cases, these tags will simply display a \"label\" name that you have defined, and
						a \"value\" name that the seller defines when placing their listing. The system automatically searches the
						templates you have assigned at the top of this page and returns a result for each tag. The search result
						will either be <font color=#33dd33 size=2 face=arial,helvetica><b>found</b></font> or
						<font color=#bb3333 size=2 face=arial,helvetica><b>not found</b></font>. If there is a tag that displays
						a <font color=#bb3333 size=2 face=arial,helvetica><b>not found</b></font> result, but you want it to
						show up within the Listing Display Page, simply insert that tag (item within brackets) into its appropriate
						template. IMPORTANT: When placing these tags in the html of your template, you must also go to the
						LISTING SETUP > FIELDS TO USE page of this admin and set each of them to \"use\".</td></tr>\n\t";

					$this->body .= "<tr class=row_color2><td colspan=2><font class=large_font>Tag Verification for the ".$user_template." template below.</font></td></tr>\n\t";
					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;TITLE&gt;&gt;<br>";
					$this->body .= "Title of the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<TITLE>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;ADDRESS&gt;&gt;<br>";
					$this->body .= "Address of the seller.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<ADDRESS>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;CITY&gt;&gt;<br>";
					$this->body .= "City of the seller.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<CITY>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;STATE&gt;&gt;<br>";
					$this->body .= "State of the seller.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<STATE>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;ZIP&gt;&gt;<br>";
					$this->body .= "Zip code of the seller.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<ZIP>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					for($i = 1; $i < 21; $i++)
					{
						$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
							&lt;&lt;MODULE_DISPLAY_OPTIONAL_FIELD_".$i."&gt;&gt;<br>";
						$this->body .= "Optional field ".$i." for the listing.\n\t</td>\n";
						$this->body .= "<td align=center valign=middle>";
						if(strstr($template_code, "<<MODULE_DISPLAY_OPTIONAL_FIELD_".$i.">>"))
							$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
						else
							$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
						$this->body .= "</td>\n\t</tr>\n";
					}

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;PRICE&gt;&gt;<br>";
					$this->body .= "Price for the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<PRICE>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;AUCTION_ID&gt;&gt;<br>";
					$this->body .= "Auction ID of the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<CLASSIFIED_ID>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;CLASSIFIED_ID&gt;&gt;<br>";
					$this->body .= "Classified ID of the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<CLASSIFIED_ID>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;DESCRIPTION&gt;&gt;<br>";
					$this->body .= "Description of the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<DESCRIPTION>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;PHONE_1&gt;&gt;<br>";
					$this->body .= "Phone number 1 for the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<PHONE_1>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;PHONE_2&gt;&gt;<br>";
					$this->body .= "Phone number 2 for the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<PHONE_2>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;CONTACT&gt;&gt;<br>";
					$this->body .= "Contact information for the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<CONTACT>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;BUY_NOW_PRICE&gt;&gt;<br>";
					$this->body .= "Buy Now Price for the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<BUY_NOW_PRICE>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;STARTING_BID&gt;&gt;<br>";
					$this->body .= "Starting Bid for the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<STARTING_BID>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";
				}
				elseif($page_id == 74)
				{
					// Signs
					// Tag verification tools
					$this->body .= "<tr><td colspan=2 class=row_color_red></td></tr>";

					$this->body .= "<tr class=".$css_tag."><td class=row_color_red colspan=3><font class=large_font_light>Tag Verification Tool</font></td></tr>\n\t";
					$this->body .= "<tr class=".$css_tag."><td class=medium_font colspan=3>The Tag Verification Tool helps you to verify the existence of template tags
						(item within brackets) that can be used within each of the templates you have chosen above. When these tags
						are used within their respective template, the tag will display information by pulling it directly from
						the database. In most cases, these tags will simply display a \"label\" name that you have defined, and
						a \"value\" name that the seller defines when placing their listing. The system automatically searches the
						templates you have assigned at the top of this page and returns a result for each tag. The search result
						will either be <font color=#33dd33 size=2 face=arial,helvetica><b>found</b></font> or
						<font color=#bb3333 size=2 face=arial,helvetica><b>not found</b></font>. If there is a tag that displays
						a <font color=#bb3333 size=2 face=arial,helvetica><b>not found</b></font> result, but you want it to
						show up within the Listing Display Page, simply insert that tag (item within brackets) into its appropriate
						template. IMPORTANT: When placing these tags in the html of your template, you must also go to the
						LISTING SETUP > FIELDS TO USE page of this admin and set each of them to \"use\".</td></tr>\n\t";

					$this->body .= "<tr class=row_color2><td colspan=2><font class=large_font>Tag Verification for the ".$user_template." template below.</font></td></tr>\n\t";
					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;TITLE&gt;&gt;<br>";
					$this->body .= "Title of the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<TITLE>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;ADDRESS&gt;&gt;<br>";
					$this->body .= "Address of the seller.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<ADDRESS>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;CITY&gt;&gt;<br>";
					$this->body .= "City of the seller.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<CITY>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;STATE&gt;&gt;<br>";
					$this->body .= "State of the seller.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<STATE>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;ZIP&gt;&gt;<br>";
					$this->body .= "Zip code of the seller.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<ZIP>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					for($i = 1; $i < 21; $i++)
					{
						$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
							&lt;&lt;MODULE_DISPLAY_OPTIONAL_FIELD_".$i."&gt;&gt;<br>";
						$this->body .= "Optional field ".$i." for the listing.\n\t</td>\n";
						$this->body .= "<td align=center valign=middle>";
						if(strstr($template_code, "<<MODULE_DISPLAY_OPTIONAL_FIELD_".$i.">>"))
							$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
						else
							$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
						$this->body .= "</td>\n\t</tr>\n";
					}

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;PRICE&gt;&gt;<br>";
					$this->body .= "Price for the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<PRICE>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;AUCTION_ID&gt;&gt;<br>";
					$this->body .= "Auction ID of the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<CLASSIFIED_ID>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;CLASSIFIED_ID&gt;&gt;<br>";
					$this->body .= "Classified ID of the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<CLASSIFIED_ID>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;DESCRIPTION&gt;&gt;<br>";
					$this->body .= "Description of the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<DESCRIPTION>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;PHONE_1&gt;&gt;<br>";
					$this->body .= "Phone number 1 for the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<PHONE_1>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;PHONE_2&gt;&gt;<br>";
					$this->body .= "Phone number 2 for the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<PHONE_2>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;CONTACT&gt;&gt;<br>";
					$this->body .= "Contact information for the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<CONTACT>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;BUY_NOW_PRICE&gt;&gt;<br>";
					$this->body .= "Buy Now Price for the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<BUY_NOW_PRICE>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";

					$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
						&lt;&lt;STARTING_BID&gt;&gt;<br>";
					$this->body .= "Starting Bid for the listing.\n\t</td>\n";
					$this->body .= "<td align=center valign=middle>";
					if(strstr($template_code, "<<STARTING_BID>>"))
						$this->body .= "<b><font color=#33dd33 size=2 face=arial>found</font></b>";
					else
						$this->body .= "<b><font color=#dd3333 size=2 face=arial>not found</font></b>";
					$this->body .= "</td>\n\t</tr>\n";
				}

				$this->body .= "</table>\n";
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

	} //end of function edit_template_attachment

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_template_attachment($db,$page_id=0,$language_id=0,$template_id=0)
	{
		//echo $page_id." is page id<br>\n";
		//echo $element_id." is element_id<br>\n";
		//echo $text_page_id." is text_page_id<br>\n";
		//echo $text_info." is text_info<br>\n";
		if (($page_id) && ($template_id))
		{
			$module_attached = array();

			$this->sql_query = "select * from ".$this->pages_languages_table;
			if ($this->pages_debug) echo  $this->sql_query."<br>\n";
			$language_result = $db->Execute($this->sql_query);
			while($languages = $language_result->FetchRow())
			{
				$this->sql_query = "delete from ".$this->pages_templates_table."
					where page_id = ".$page_id." and language_id = ".$languages["language_id"];
				if ($this->pages_debug) echo  $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if($this->configuration_data["debug_admin"])
				{
					$this->debug_display($db, $this->filename, $this->function_name, "pages_templates_table", "delete page template data");
				}
				if (!$result)
				{
					if ($this->pages_debug) echo  $this->sql_query."<br>\n";
					return false;
				}

				$this->sql_query = "insert into ".$this->pages_templates_table."
				(page_id,language_id,template_id)
				values
				(".$page_id.",".$languages["language_id"].",".$template_id["language".$languages["language_id"]].")";
				if ($this->pages_debug) echo  $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if($this->configuration_data["debug_admin"])
				{
					$this->debug_display($db, $this->filename, $this->function_name, "pages_templates_table", "insert new page template data");
				}
				if (!$result)
				{
					if ($this->pages_debug) echo  $this->sql_query."<br>\n";
					$this->error_message = $this->internal_error_message;
					$this->site_error($db->ErrorMsg());
					return false;
				}

				// Attach modules that arent yet attached
				$this->sql_query = "select template_code from ".$this->templates_table." where template_id = ".$template_id["language".$languages["language_id"]];
				//echo $this->sql_query.'<br>';
				$result = $db->Execute($this->sql_query);
				if(!$result)
					return false;
				else
				{
					$current_template_info = $result->FetchRow();
				}

				$this->sql_query = "select module_replace_tag, page_id from ".$this->pages_table." where module = 1";
				//echo $this->sql_query.'<Br>';
				$result = $db->Execute($this->sql_query);
				if(!$result)
				{
					return false;
				}
				else
				{
					// Holds the IDs of the html modules attached
					$html_modules = array();

					// Get all module tags
					$module_replace_tag = $result->GetArray();
					//$module_replace_tag = $result->FetchRow();

					for($i = 0; $i < sizeof($module_replace_tag); $i++)
					{
						if(eregi($module_replace_tag[$i]['module_replace_tag'], $current_template_info["template_code"]))
						{
							// First check if it is a HTML module
							if(eregi("HTML", $module_replace_tag[$i]['name']))
							{
								// If it is a HTML module add it to the html_modules array
								$html_modules[] = $module_replace_tag[$i]['page_id'];
							}

							// If replace tag is found in template then lets find out if its attached or not
							$this->sql_query = "select * from ".$this->pages_modules_table." where page_id = ".$page_id." and module_id = ".$module_replace_tag[$i]['page_id'];
							//echo $this->sql_query.'<Br>';
							$result = $db->Execute($this->sql_query);
							if($result->RecordCount() == 0)
							{
								// If not attached insert into proper table to attach it
								$this->sql_query = "insert into ".$this->pages_modules_table." (module_id, page_id, time) values (".$module_replace_tag[$i]['page_id'].", ".$page_id.", ".$this->shifted_time().")";
								//echo $this->sql_query.'<Br>';
								$result = $db->Execute($this->sql_query);
								if(!$result)
									return false;
							}
							else
							{
								// This means its attached so continue on next iteration
								continue;
							}
						}
					}

					if(sizeof($html_modules) > 0)
					{
						// If any HTML Modules were found then lets check them out also.
						for($i = 0; $i < sizeof($module_replace_tag); $i++)
						{
							for($module_num = 0; $module_num < sizeof($html_modules); $module_num++)
							{
								$this->sql_query = "select module_logged_in_html, module_logged_in_html from ".$this->pages_table." where page_id = ".$html_modules[$module_num];
								$result = $db->Execute($this->sql_query);
								if(!$result)
								{
									return false;
								}

								$module_code = $result->FetchRow();

								// Check the logged in HTML
								if(eregi($module_replace_tag[$i]['module_replace_tag'], $module_code["module_logged_in_html"]))
								{
									// If replace tag is found in template then lets find out if its attached or not
									$this->sql_query = "select * from ".$this->pages_modules_table." where page_id = ".$page_id["page_id"]." and module_id = ".$module_replace_tag[$i]['page_id'];
									$result = $db->Execute($this->sql_query);
									if ($this->debug) echo $this->sql_query."<br>\n";
									if($result->RecordCount() == 0)
									{
										// If not attached insert into proper table to attach it
										$this->sql_query = "insert into ".$this->pages_modules_table." (module_id, page_id, time) values (".$module_replace_tag[$i]['page_id'].", ".$page_id["page_id"].", ".$this->shifted_time().")";
										$insert_result = $db->Execute($this->sql_query);
										if ($this->debug)
											echo $this->sql_query."<br>\n";
										if(!$insert_result)
										{
											echo $db->ErrorMsg()." is the sql error<BR>\n";
											if ($this->debug) echo $this->sql_query."<br>\n";
											return false;
										}
									}
								}

								// Check logged out HTML
								if(eregi($module_replace_tag[$i]['module_replace_tag'], $module_code["module_logged_out_html"]))
								{
									// If replace tag is found in template then lets find out if its attached or not
									$this->sql_query = "select * from ".$this->pages_modules_table." where page_id = ".$page_id["page_id"]." and module_id = ".$module_replace_tag[$i]['page_id'];
									$result = $db->Execute($this->sql_query);
									if ($this->debug) echo $this->sql_query."<br>\n";
									if($result->RecordCount() == 0)
									{
										// If not attached insert into proper table to attach it
										$this->sql_query = "insert into ".$this->pages_modules_table." (module_id, page_id, time) values (".$module_replace_tag[$i]['page_id'].", ".$page_id["page_id"].", ".$this->shifted_time().")";
										$insert_result = $db->Execute($this->sql_query);
										if ($this->debug)
											echo $this->sql_query."<br>\n";
										if(!$insert_result)
										{
											echo $db->ErrorMsg()." is the sql error<BR>\n";
											if ($this->debug) echo $this->sql_query."<br>\n";
											return false;
										}
									}
								}
							}
						}
					}
				}
			}

			if($page_id == 1)
			{
				$this->sql_query = "update ".$this->ad_configuration_table." set
					user_ad_template = ".$template_id['user_ad_template'].
					", user_checkbox_template = ".$template_id['user_checkbox_template'].
					", user_extra_template = ".$template_id['user_extra_template'].
					", auctions_user_ad_template = ".$template_id['auctions_user_ad_template'].
					", auctions_user_checkbox_template = ".$template_id['auctions_user_checkbox_template'].
					", auctions_user_extra_template = ".$template_id['auctions_user_extra_template'];
				//echo  $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if(!$result)
				{
					if ($this->pages_debug) echo  $this->sql_query."<br>\n";
					$this->error_message = $this->internal_error_message;
					$this->site_error($db->ErrorMsg());
					return false;
				}
			}
			elseif($page_id == 43)
			{
				$this->sql_query = "update ".$this->site_configuration_table." set
					home_template = ".$template_id['user_management_template'];
				if ($this->pages_debug) echo  $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if(!$result)
				{
					if ($this->pages_debug) echo  $this->sql_query."<br>\n";
					$this->error_message = $this->internal_error_message;
					$this->site_error($db->ErrorMsg());
					return false;
				}
			}
			elseif ($page_id == 84)
			{
				$this->sql_query = "update ".$this->ad_configuration_table." set
					full_size_image_template = \"".$template_id['user_template']."\"";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->site_error($db->ErrorMsg());
					return false;
				}
			}

			return true;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			//echo "whatever";
			return false;
		}
	} //end of function update_template_attachment

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function add_module_form($db,$page_id=0)
	{
		if ($page_id)
		{
			$page = $this->get_page($db,$page_id);
			$this->sql_query = "select * from ".$this->pages_table." where module = 1";
			$modules_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$modules_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			$this->body .= "<table cellpadding=3 cellspacing=1 border=0 class=row_color1>\n";
			$this->title = "Add a Module to a Page Form";
			$this->description = "Below is the list of possible modules that
				can be added to a page.  Choose the one you wish to add and click \"add\" at the bottom of the page.  Currently attached modules are bolded.";

			if ($modules_result->RecordCount() > 0)
			{
				$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light><b>Module Name</b> </td>\n\t";
				$this->body .= "<td class=medium_font_light><b>Description</b> </td>\n\t";
				$this->body .= "<td class=medium_font_light>&nbsp; </a></td>\n</tr>\n";

				$this->row_count = 0;
				while ($show_modules = $modules_result->FetchRow())
				{
					$this->sql_query = "select * from ".$this->pages_modules_table." where page_id = ".$page_id." and module_id = ".$show_modules["page_id"];
					$module_attached_result = $db->Execute($this->sql_query);
					//echo $this->sql_query." is the query<br>\n";
					if (!$module_attached_result)
					{
						//echo $this->sql_query."<br>";
						return false;
					}
					if (!$module_attached_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[5501];
						return false;
					}
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td><a href=index.php?a=44&z=7&b=".$show_modules["page_id"].">";
					if ($module_attached_result->RecordCount() == 1)
					{
						$this->body .= "<b><span class=medium_font><font color=000000>".$show_modules["name"]."</font></span></b></a><br>";
						$show_modules["module_replace_tag"] = str_replace("<", "&lt;", $show_modules["module_replace_tag"]);
						$show_modules["module_replace_tag"] = str_replace(">", "&gt;", $show_modules["module_replace_tag"]);
						$this->body .= "<span class=small_font>".$show_modules["module_replace_tag"]."</span></td>\n\t";
					}
					else
					{
						$this->body .= "<span class=medium_font><font color=000000>".$show_modules["name"]."</font></span></b></a><br>";
						$show_modules["module_replace_tag"] = str_replace("<", "&lt;", $show_modules["module_replace_tag"]);
						$show_modules["module_replace_tag"] = str_replace(">", "&gt;", $show_modules["module_replace_tag"]);
						$this->body .= "<span class=small_font>".$show_modules["module_replace_tag"]."</span></td>\n\t";
					}

					$this->body .= "<td class=medium_font>".$show_modules["description"]." </td>\n\t";
					if ($module_attached_result->RecordCount() == 1)
					{
						$this->body .= "<td><a href=index.php?a=44&z=2&b=".$page_id."&c=".$show_modules["page_id"]."><span class=medium_font><img src=admin_images/btn_admin_remove.gif alt=remove border=0></span></a></td>\n</tr>\n";
					}
					else
					{
						$this->body .= "<td><a href=index.php?a=44&z=6&b=".$page_id."&c=".$show_modules["page_id"]."><span class=medium_font><img src=admin_images/btn_admin_attach.gif alt=attach border=0></span></a></td>\n</tr>\n";
					}

					$this->row_count++;
				}
			}
			else
			{
				$this->body .= "<tr>\n\t<td colspan=3 class=medium_font>There are no modules currently. </td>\n</tr>\n";
			}
			$this->body .= "<tr>\n\t<td colspan=3><br><a href=index.php?a=44&z=3&b=".$page_id."><span class=medium_font><font color=000000><b>back to ".$page["name"]." page</b></span></a></td>\n</tr>\n";
			$this->body .= "</table>\n";
			return true;
		}
		else
		{
			return false;
		}

	} //end of function add_module_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function remove_module_from_page($db,$page_id=0,$module_id=0)
	{
		//echo $element_id." is element_id<br>\n";
		//echo $text_page_id." is text_page_id<br>\n";
		//echo $text_info." is text_info<br>\n";
		if ($page_id && $module_id)
		{
			$this->sql_query = "delete from ".$this->pages_modules_table."
				where page_id = ".$page_id." and module_id = ".$module_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>";
			if (!$result)
			{
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
	} //end of function remove_module_from_page

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function add_module_to_page($db,$page_id,$module_id)
	{
		//echo $element_id." is element_id<br>\n";
		//echo $text_page_id." is text_page_id<br>\n";
		//echo $text_info." is text_info<br>\n";
		if (($page_id) && ($module_id))
		{
			$this->sql_query = "insert into ".$this->pages_modules_table."
				(page_id,module_id,time)
				values
				(".$page_id.",".$module_id.",".$this->shifted_time().")";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
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
	} //end of function remove_module_from_page

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function edit_module_specifics_form($db,$page_id=0)
	{
		if (!$page_id)
			return false;
		$page = $this->get_page($db,$page_id);
		if (!$this->admin_demo()) $this->body .= "
			<form action=index.php?a=44&z=7&b=".$page_id." method=post>\n";

		$this->row_count=0;
		$this->body .= "
			<table cellpadding=3 cellspacing=1 border=0>";

		$this->input_admin_label_and_tag_name($page);

		if (in_array($page_id,$this->cat_nav_mods))
		{
			//category navigation module
			$this->title = "Category Navigation Form Module Admin";
			$this->description = "Choose how to display this module from the choices below.
				<br><Br>PLEASE NOTE:  This module will not display on \"non-browsing\" pages.  Non-browsing	page are pages that do not
				display listings within the category display pages.   This includes pages like extra blank, registration, login/logout,
				user management and List an Item pages.";

			$this->input_option_dropdown($page,'number_of_browsing_columns','number of columns of categories to display',6);
			$this->input_radio_yes_no($page,'display_category_count','display category count next to category name');
			if($this->is_class_auctions($page))
			{
				$this->input_browsing_count_format($page);
			}
			$this->input_radio_yes_no($page,'display_category_image','display category image');
			$this->input_radio_yes_no($page,'display_category_description','display category description below the category name');
			$this->input_radio_yes_no($page,'display_no_subcategory_message','display the no sub category message');
			$this->input_radio_yes_no($page,'module_display_ad_description','display the back to parent category link at bottom of listing');
			$message = "
				display the \"new listing\" icon within module<br>
				<span class=small_font>
					The icon used is set within the site setup > browsing section.  You choose whether to display the site wide
					\"new listing\" icon within this module here.  The site wide time limit rules apply here also.
				</span>";
			$this->input_radio_yes_no($page,'module_display_new_ad_icon',$message);
		}
		elseif (in_array($page_id,$this->main_cat_nav_mods))
		{
			//category navigation module
			$this->title = "Category Navigation Level Form Module Admin";
			$this->description = "This module is just like a normal category navigation module except that it will only display one of
				the top two levels of categories no matter where it is displayed.  It will display the main categories or the categories
				beneath the main categories (level 2 - and not the main categories).  Choose how to display this module from the choices below.";

			$this->input_module_category_level_to_display($page);
			$this->input_option_dropdown($page,'number_of_browsing_columns','number of columns of categories to display',6);
			$this->input_radio_yes_no($page,'display_category_count','display category count next to category name');
			if($this->is_class_auctions($page))
			{
				$this->input_browsing_count_format($page);
			}
			$this->input_radio_yes_no($page,'display_category_image','display category image');
			$this->input_radio_yes_no($page,'display_category_description','display category description below the category name');
			$this->input_radio_yes_no($page,'display_no_subcategory_message','display the no sub category message');
			$this->input_radio_yes_no($page,'module_display_ad_description','display the back to parent category link at bottom of listing');
		}
		elseif (in_array($page_id,$this->filter_dropdown_mods))
		{
			//filters dropdown
			$this->title = "Filter Dropdown Form Module Admin";
			$this->description = "Choose how to display this module from the choice below.";
			$this->input_module_display_filter_in_row($page);
		}
		elseif (in_array($page_id,$this->display_username_mods))
		{
			//display username module
			$this->title = "Edit the Display User Data Module Form";
			$this->description = "Choose what personal information you wish to display when you use this module.";
			$this->input_module_display_username($db,$page);
		}
		elseif (in_array($page_id,$this->cat_tree_display_mods))
		{
			$this->title = "Edit the Category Tree Display Module Form";
			$this->description = "Set the text of the links to be displayed and the style they
				are displayed with through the appropriate links below.<br><Br>
				PLEASE NOTE:  This module will not display on \"non-browsing\" pages.  Non-browsing pages are pages that do not display
				listings within the category display pages.  This includes pages like extra blank pages, registration, login/logout, and
				client side management.";
		}
		elseif (in_array($page_id,$this->reg_login_link_mods))
		{
			//display register/login link module
			$this->title = "Edit the Display Register/Login/My Account Link Module Form";
			$this->description = "Set the text of the links to be displayed and the style they are displayed with through the appropriate
				links below.";
		}
		elseif (in_array($page_id,$this->logged_in_out_HTML_mods))
		{
			//display register/login link module
			$this->title = "Display Logged In/Out HTML Module";
			$this->description = "When a user is logged in one set of HTML will be displayed while different HTML will be displayed
				when the user is logged out.  Enter the corresponding HTML into the fields below.";

			$this->input_module_logged_in_out_html($page);
		}
		elseif (in_array($page_id,$this->featured_pics_mods))
		{
			//display featured pic module
			$this->title = "Featured Picture Module 1";
			$this->description = "This displays the featured listings by picture only with the number of columns and rows that you specify below.";
			if($this->is_class_auctions())
			{
				$this->input_module_display_type_listing($page);
			}
			$this->input_radio_yes_no($page,'module_display_header_row','display header row');
			$this->input_option_dropdown($page,'module_number_of_ads_to_display','number of featured pic rows to display',1000);
			$this->input_option_dropdown($page,'module_number_of_columns','number of featured pic columns to display',11);
			$this->input_module_thumb_width_height($page);
			$this->input_module_display_title($page);
			$message = "
				Max characters of title to display<br>
				<span class=small_font>
					Choose the maximum number of characters of the title that will be displayed
				</span>";
			$this->input_option_dropdown($page,'length_of_description',$message);
			$this->input_radio_yes_no($page,'module_display_price','display price below title/thumbnail');
			if($this->is_class_auctions())
			{
				$this->input_radio_yes_no($page,'module_display_type_text','display listing type below title(classified/auction)');
			}

			$this->input_display_empty_message($page);
		}
		elseif (in_array($page_id,$this->PHP_mods))
		{
			//display php module controls
			$this->title = "PHP Code Module";
			$this->description = "This displays the
				php that you enter into the form below.  Enter the php code you wish to run into the form below.  Where the
				tag below is displayed within the template your php code will be executed and the results displayed.";
			$this->input_php_code($page);
		}
		elseif (in_array($page_id,$this->search_mods))
		{
			//display search box module
			$this->title = "Search Box Module";
			$this->description = "Make the necessary changes to display the search module the way you need.";
			$this->input_radio_yes_no($page,'display_category_description','display title/description choice dropdown');
		}
		elseif (in_array($page_id,$this->zip_browse_mods))
		{
			//display zip browsing module
			$this->title = "Zip Browsing Module";
			$this->description = "Make the necessary changes to display the zip browsing module the way you need.  This module allows
				the user to set a zip code and radius around that zip code.  Any listings that fall into this zip/radius will be
				displayed while browsing.  Make sure to place this module on all \"browsing\" pages to make sure the user can change
				the zip code and distance data within the filter at any time thus expanding/limiting their results while browsing.";
		}
		elseif (in_array($page_id,$this->state_browse_mods))
		{
			//display state filter module
			$this->title = "State Browsing Module";
			$this->description = "Make the necessary changes to display the	state browsing module the way you need.  This module allows the
				user to choose a state.  Any listings that fall	into this state will be displayed while browsing.  Make sure to place this
				module on all \"browsing\" pages to make sure the user can change the state data within the	filter at any time thus changing
				their results while browsing.";
		}
		elseif (in_array($page_id,$this->featured_listings_mods))
		{
			//featured listings module that displays a specific category
			$this->title = "Featured Listings from a Specific Category Module";
			$this->description = "Make the necessary changes to display the
				category specific browsing module the way you need.  This module allows the admin to choose a category that they
				wish to display featured listings from within this module.  The featured listings from the specific category are chosen at
				random. Choose the category you wish to display the featured from below.";
			if($this->is_class_auctions())
			{
				$this->input_module_display_type_listing($page);
			}
			$this->input_option_dropdown($page,'module_number_of_ads_to_display','number of featured pic rows to display',1000);
			$this->input_category_dropdown($db,$page);
			$this->input_radio_yes_no($page,'module_display_header_row','display header row');
			if($this->is_class_auctions())
			{
				$this->input_radio_yes_no($page,'module_display_listing_column','display listing type');
			}
			$this->input_radio_yes_no($page,'module_display_photo_icon','display photo');

			$this->input_photo_or_icon($page);
			$this->input_module_thumb_width_height($page);
			$this->input_radio_yes_no($page,'display_all_of_description','display all of description');
			$message = "
				Max characters of description to display<br>
				<span class=small_font>
				Choose the maximum number of characters of the description that will be displayed
				</span>";
			$this->input_option_dropdown($page,'length_of_description',$message);
			$this->input_module_display_ad_description_where($page);
			$this->input_radio_yes_no($page,'module_display_ad_description','display the back to parent category link at bottom of listing');

			$this->input_module_display_location($page);
			if($this->is_class_auctions() || $this->is_auctions())
			{
				$this->input_radio_yes_no($page,'module_display_number_bids','display number of bids');
			}
			$this->input_radio_yes_no($page,'module_display_price','display price below title/thumbnail');
			$this->input_radio_yes_no($page,'module_display_entry_date','display entry date column');
			if($this->is_class_auctions() || $this->is_auctions())
			{
				$this->input_radio_yes_no($page,'module_display_time_left','display time left');
			}
			$this->input_radio_yes_no($page,'module_display_attention_getter','display attention getters within module');
			$this->input_module_display_optional_fields($page);
		}
		elseif (in_array($page_id,$this->fixed_cat_nav_mods))
		{
			$this->title = "Fixed Category Navigation Form Module Admin";
			$this->description = "Choose how to display this module from the choices below.  Also choose the category from which you wish
				to display the subcategories of.  This module will display the immediate subcategories of the category you choose below.";
			$this->input_category_dropdown($db,$page);
			$this->input_option_dropdown($page,'number_of_browsing_columns','number of columns of categories to display',6);
			$this->input_radio_yes_no($page,'display_category_count','display category count next to category name');
			// Choose how to display the category counts
			if($this->is_class_auctions())
			{
				$this->input_browsing_count_format($page);
			}
			$this->input_radio_yes_no($page,'display_category_image','display category image');
			$this->input_radio_yes_no($page,'display_category_description','display category description below the category name');
			$this->input_radio_yes_no($page,'display_no_subcategory_message','display the no sub category message');
			$this->input_radio_yes_no($page,'module_display_ad_description','display the back to parent category link at bottom of listing');
			$message = "
				display the \"new listing\" icon within module<br>
				<span class=small_font>
					The icon used is set within the site setup > browsing section.  You choose whether to display the site
					wide \"new listing\" icon within this module here.
					The site wide time limit rules apply here also.
				</span>";
			$this->input_radio_yes_no($page,'module_display_new_ad_icon',$message);
		}
		elseif(in_array($page_id,$this->title_mods))
		{
			// Title module
			$this->title = "Fixed Category Navigation Form Module Admin";
			$this->description = "Choose how to display this module from the choices below.  Also choose the category from which you wish
				to display the subcategories of.  This module will display the immediate subcategories of the category you choose below.";
			if (!$this->input_title_module_text($db,$page_id,$page))
				return false;
		}
		elseif (in_array($page_id,$this->hottest))
		{
			$this->title = "Hottest Listings Module > Edit Properties";
			$this->description = "Edit the specific fields to display with this	module.  Turn off and on the display of a column by
				checking it on or off.";
			if($this->is_class_auctions())
			{
				$this->input_module_display_type_listing($page);
			}
			$this->input_option_dropdown($page,'module_number_of_ads_to_display','number of featured pic rows to display',1000);
			if($this->is_class_auctions())
			{
				$this->input_radio_yes_no($page,'module_display_listing_column','display listing type');
			}
			$this->input_module_display_title($page);
			$this->input_radio_yes_no($page,'module_display_header_row','display header row');
			$this->input_radio_yes_no($page,'module_display_photo_icon','display photo');
			$this->input_photo_or_icon($page);
			$this->input_module_thumb_width_height($page);
			$this->input_radio_yes_no($page,'display_all_of_description','display all of description');
			$message = "
				Max characters of description to display<br>
				<span class=small_font>
				Choose the maximum number of characters of the description that will be displayed
				</span>";
			$this->input_option_dropdown($page,'length_of_description',$message);
			$this->input_module_display_ad_description_where($page);
			$this->input_radio_yes_no($page,'module_display_ad_description','display the back to parent category link at bottom of listing');
			$this->input_module_display_location($page);
			if($this->is_class_auctions() || $this->is_auctions())
			{
				$this->input_radio_yes_no($page,'module_display_number_bids','display number of bids');;
			}
			$this->input_radio_yes_no($page,'module_display_price','display price below title/thumbnail');
			$this->input_radio_yes_no($page,'module_display_entry_date','display entry date column');
			if($this->is_class_auctions() || $this->is_auctions())
			{
				$this->input_radio_yes_no($page,'module_display_time_left','display time left');
			}
			$this->input_radio_yes_no($page,'module_display_attention_getter','display attention getters within module');
			$this->input_module_display_optional_fields($page);
		}
		elseif (in_array($page_id,$this->users))
		{
			$this->title = "Users Module > Edit Properties";
			$this->description = "Edit the specific fields to display with this	module.";
		}
		else
		{
			$this->title = "Edit a Module Form";
			$this->description = "Edit the specific fields to display with this
				module.  Turn off and on the display of a column by checking it on or off.";
			if($this->is_class_auctions())
			{
				$this->input_module_display_type_listing($page);
			}
			$this->input_option_dropdown($page,'module_number_of_ads_to_display','number of featured pic rows to display',1000);
			if($this->is_class_auctions())
			{
				$this->input_radio_yes_no($page,'module_display_listing_column','display listing type');
			}
			$this->input_module_display_title($page);
			$this->input_radio_yes_no($page,'module_display_header_row','display header row');
			$this->input_radio_yes_no($page,'module_display_photo_icon','display photo');
			// Photo or Icon for featured listings modules and newest modules
			if(in_array($page_id,$this->featured_and_newest_mods))
			{
				$this->input_photo_or_icon($page);
			}
			$this->input_module_thumb_width_height($page);
			$this->input_radio_yes_no($page,'display_all_of_description','display all of description');
			$message = "
				Max characters of description to display<br>
				<span class=small_font>
				Choose the maximum number of characters of the description that will be displayed
				</span>";
			$this->input_option_dropdown($page,'length_of_description',$message);
			$this->input_module_display_ad_description_where($page);
			$this->input_radio_yes_no($page,'module_display_ad_description','display the back to parent category link at bottom of listing');
			$this->input_module_display_location($page);
			if($this->is_class_auctions() || $this->is_auctions())
			{
				$this->input_radio_yes_no($page,'module_display_number_bids','display number of bids');
			}
			$this->input_radio_yes_no($page,'module_display_price','display price below title/thumbnail');
			$this->input_radio_yes_no($page,'module_display_entry_date','display entry date column');
			if($this->is_class_auctions() || $this->is_auctions())
			{
				$this->input_radio_yes_no($page,'module_display_time_left','display time left');
			}
			$this->input_radio_yes_no($page,'module_display_attention_getter','display attention getters within module');
			$this->input_module_display_optional_fields($page);
		}
		if (!$this->admin_demo())
		{
			$this->body .= "
				<tr class=".$this->get_row_color().">
					<td colspan=2 align=center class=medium_font>
						<input type=submit name=enter value=\"Save\">
					</td>
				</tr>";
		}

		$this->body .= "<tr bgcolor=000066>\n\t<td colspan=3 class=medium_font_light align=center><b>Edit Text appearing within this Module
			</b> </td>\n</tr>\n";
		$this->sql_query = "select * from ".$this->pages_languages_table;
		$language_result = $db->Execute($this->sql_query);
		 if (!$language_result)
		 {
			//$this->body .= $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[3501];
			return false;
		 }
		 elseif ($language_result->RecordCount() > 0)
		 {
			$this->row_count = 0;
			while ($show_language = $language_result->FetchRow())
			{
				$this->body .= "
				<tr class=".$this->get_row_color().">
					<td align=right width=50%>
						<span class=medium_font>
							".$this->get_language_name($db,$show_language["language_id"])."
						</span>
					</td>
					<td colspan=2 width=40% class=medium_font>
						<a href=index.php?a=14&b=".$page_id."&l=".$show_language["language_id"].">
							<img src=admin_images/btn_admin_edit_text.gif alt=edit border=0>
						</a>
					</td>
				</tr>";$this->row_count++;
			}
		 }

		 $this->body .= "
		 		<tr bgcolor=000066>
					<td colspan=4 class=medium_font_light align=center>
						<b>CSS Fonts appearing in this module:</b>
					</td>
				</tr>
				<tr>
					<td colspan=3 align=center>
						<a href=index.php?a=38&b=".$page_id.">
							<span class=medium_font_light>
								<img src=admin_images/btn_admin_edit_css.gif alt=edit border=0>
							</span>
						</a>
					</td>
				</tr>";

		$this->body .= "
				<tr class=row_color_black>
					<td colspan=2 class=medium_font_light>
						The following pages	currently have this module attached to them.
					</td>
				</tr>";

		$this->sql_query = "select * from ".$this->pages_modules_table." where module_id = ".$page_id;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->row_count = 0;
			while ($show_pages = $result->FetchRow())
			{
				$attached_page = $this->get_page($db,$show_pages["page_id"]);
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td colspan=2>
					<a href=index.php?a=44&z=3&b=".$show_pages["page_id"].">
					<span class=medium_font><font color=000000>".$attached_page["name"]."</font></span></a></td>\n</tr>\n";
				$this->row_count++;
			}
		}
		else
		{
			$this->body .= "
				<tr>
					<td colspan=2 class=medium_font align=center>
						<br><b>There currently are no pages with this module attached to them.</b><br><br>
					</td>
				</tr>";
		}

		$this->body .= "<tr>\n\t<td colspan=3 align=center>
			<a href=index.php?a=44&z=8&b=".$page["page_id"].">
			<b><span class=medium_font><font color=000000><b>View All Pages to attach this Module</b></font><br><br></span></a></a></td>\n</tr>\n";

		if (strlen($page["special_instructions"]) > 0)
			$this->body .= "
				<tr class=row_color_black>
					<td colspan=3 class=medium_font_light>
						".$page["special_instructions"]."
					</td>
				</tr>";
		$this->body .= "
				<tr>
					<td colspan=2>
						<a href=index.php?a=44>
							<span class=medium_font>
								<b>back to Pages Home</b>
							</span>
						</a>
					</td>
				</tr>
				<tr>
					<td colspan=2>
						<a href=index.php?a=79>
							<span class=medium_font>
								<b>back to Modules Home</b>
							</span>
						</a>
					</td>
				</tr>
				<tr>
					<td colspan=2>
						<a href=index.php?a=74>
							<span class=medium_font>
								<b>View Complete Modules List</b>
							</span>
						</a>
					</td>
				</tr>
			</table>
			</form>";
		return true;
	} //end of function edit_module_specifics_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_module_specifics($db,$page_id=0,$module_info=0)
	{
		if (in_array($page_id,$this->extra_pages))
		{
			//this is an extra page, not a module
			$this->sql_query = "update ".$this->pages_table." set
				admin_label = \"".$module_info["admin_label"]."\",
				description = \"".$module_info["description"]."\",
				extra_page_text = \"".urlencode($module_info['extra_page_text'])."\"
				where page_id = ".$page_id;

			//$this->body .= $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}

			return true;
		}
		elseif(in_array($page_id,$this->title_mods))
		{
			//this is a title module
			$this->sql_query = "UPDATE ".$this->ad_configuration_table." SET
				title_module_text = \"".urlencode($module_info["title_module_text"])."\"";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}

			$this->sql_query = "UPDATE ".$this->pages_text_languages_table." SET
				text = \"".urlencode($module_info["title_module_home_text"])."\"
				WHERE text_id = 2462";
			//echo $this->sql_query.'<br>';
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			//this is an extra page, not a module
			$this->sql_query = "UPDATE ".$this->pages_table." SET
				admin_label = \"".$module_info["admin_label"]."\"
				WHERE page_id = ".$page_id;

			//$this->body .= $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}

			return true;
		}
		elseif ($page_id && $module_info)
		{
			$content = trim(addslashes($module_info["module_logged_in_html"])).trim(addslashes($module_info["module_logged_out_html"]));
			if (strlen($content)>0)
				$this->AttachEmbeddedModules($db, $page_id, 0, $content);

			$this->sql_query = "update ".$this->pages_table." set
				module_display_header_row = \"".$module_info["module_display_header_row"]."\",
				module_number_of_ads_to_display = \"".$module_info["module_number_of_ads_to_display"]."\",
				module_display_photo_icon = \"".$module_info["module_display_photo_icon"]."\",
				module_display_ad_description = \"".$module_info["module_display_ad_description"]."\",
				module_display_ad_description_where = \"".$module_info["module_display_ad_description_where"]."\",
				module_number_of_columns = \"".$module_info["module_number_of_columns"]."\",
				module_display_filter_in_row = \"".$module_info["module_display_filter_in_row"]."\",
				module_display_price = \"".$module_info["module_display_price"]."\",
				module_use_image = \"".$module_info["module_use_image"]."\",
				module_display_entry_date  = \"".$module_info["module_display_entry_date"]."\",
				display_all_of_description  = \"".$module_info["display_all_of_description"]."\",
				length_of_description  = \"".$module_info["length_of_description"]."\",
				admin_label = \"".$module_info["admin_label"]."\",
				module_logged_in_html = \"".trim(addslashes($module_info["module_logged_in_html"]))."\",
				module_logged_out_html = \"".trim(addslashes($module_info["module_logged_out_html"]))."\",
				module_display_optional_field_1 = \"".$module_info["module_display_optional_field_1"]."\",
				module_display_optional_field_2 = \"".$module_info["module_display_optional_field_2"]."\",
				module_display_optional_field_3 = \"".$module_info["module_display_optional_field_3"]."\",
				module_display_optional_field_4 = \"".$module_info["module_display_optional_field_4"]."\",
				module_display_optional_field_5 = \"".$module_info["module_display_optional_field_5"]."\",
				module_display_optional_field_6 = \"".$module_info["module_display_optional_field_6"]."\",
				module_display_optional_field_7 = \"".$module_info["module_display_optional_field_7"]."\",
				module_display_optional_field_8 = \"".$module_info["module_display_optional_field_8"]."\",
				module_display_optional_field_9 = \"".$module_info["module_display_optional_field_9"]."\",
				module_display_optional_field_10 = \"".$module_info["module_display_optional_field_10"]."\",
				module_display_optional_field_11 = \"".$module_info["module_display_optional_field_11"]."\",
				module_display_optional_field_12 = \"".$module_info["module_display_optional_field_12"]."\",
				module_display_optional_field_13 = \"".$module_info["module_display_optional_field_13"]."\",
				module_display_optional_field_14 = \"".$module_info["module_display_optional_field_14"]."\",
				module_display_optional_field_15 = \"".$module_info["module_display_optional_field_15"]."\",
				module_display_optional_field_16 = \"".$module_info["module_display_optional_field_16"]."\",
				module_display_optional_field_17 = \"".$module_info["module_display_optional_field_17"]."\",
				module_display_optional_field_18 = \"".$module_info["module_display_optional_field_18"]."\",
				module_display_optional_field_19 = \"".$module_info["module_display_optional_field_19"]."\",
				module_display_optional_field_20 = \"".$module_info["module_display_optional_field_20"]."\",
				module_display_city = \"".$module_info["module_display_city"]."\",
				module_display_state = \"".$module_info["module_display_state"]."\",
				module_display_country = \"".$module_info["module_display_country"]."\",
				module_display_zip = \"".$module_info["module_display_zip"]."\",
				module_display_address = \"".$module_info["module_display_address"]."\",
				module_display_phone2 = \"".$module_info["module_display_phone2"]."\",
				module_display_phone1 = \"".$module_info["module_display_phone1"]."\",
				module_display_price = \"".$module_info["module_display_price"]."\",
				module_display_title = \"".$module_info["module_display_title"]."\",
				module_text_type = \"".$module_info["module_text_type"]."\",
				module_display_contact = \"".$module_info["module_display_contact"]."\",
				module_display_classified_id = \"".$module_info["module_display_classified_id"]."\",
				module_display_attention_getter = \"".$module_info["module_display_attention_getter"]."\",
				module_thumb_width = \"".$module_info["module_thumb_width"]."\",
				module_thumb_height = \"".$module_info["module_thumb_height"]."\",
				display_category_count = \"".$module_info["display_category_count"]."\",
				browsing_count_format = \"".$module_info["browsing_count_format"]."\",
				display_category_image = \"".$module_info["display_category_image"]."\",
				module_category = \"".$module_info["module_category"]."\",
				number_of_browsing_columns = \"".$module_info["number_of_browsing_columns"]."\",
				display_category_description = \"".$module_info["display_category_description"]."\",
				display_no_subcategory_message = \"".$module_info["display_no_subcategory_message"]."\",
				php_code = \"".addslashes($module_info["php_code"])."\",
				display_empty_message = \"".$module_info["display_empty_message"]."\",
				module_display_name = \"".$module_info["module_display_name"]."\",
				module_display_username = \"".$module_info["module_display_username"]."\",
				module_display_new_ad_icon = \"".$module_info["module_display_new_ad_icon"]."\",
				module_category_level_to_display = \"".$module_info["module_category_level_to_display"]."\",
				module_display_type_listing = \"".$module_info["module_display_type_listing"]."\",
				module_display_type_text = \"".$module_info["module_display_type_text"]."\",
				module_display_listing_column = \"".$module_info["module_display_listing_column"]."\",
				module_display_number_bids = \"".$module_info["module_display_number_bids"]."\",
				module_display_time_left = \"".$module_info["module_display_time_left"]."\"
				where page_id = ".$page_id;

			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			elseif ($page_id == 70)
			{
				$this->sql_query = "update ".$this->ad_configuration_table." set
					flyer_maximum_image_width = ".$module_info["flyer_maximum_image_width"].",
					flyer_maximum_image_height = ".$module_info["flyer_maximum_image_height"];
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}
			}
			elseif ($page_id == 71)
			{
				$this->sql_query = "update ".$this->ad_configuration_table." set
					sign_maximum_image_width = ".$module_info["sign_maximum_image_width"].",
					sign_maximum_image_height = ".$module_info["sign_maximum_image_height"];
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}

			}
			// Photo or Icon for featured listings modules and newest modules
			elseif(in_array($page_id,$this->featured_and_newest_mods) || in_array($page_id,$this->hottest))
			{
				$this->sql_query = "update ".$this->pages_table." set
					photo_or_icon = ".$module_info["photo_or_icon"]." where page_id = ".$page_id;
				//echo $this->sql_query.'<br>';
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}
			}
			return true;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			//echo "whatever";
			return false;
		}
	} //end of function update_template_attachment

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function module_to_page_attachments_form($db,$module_id=0)
	{
		if ($module_id)
		{
			$this->sql_query = "select * from ".$this->pages_table." where page_id = ".$module_id;
			$module_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$module_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			if ($module_result->RecordCount() == 1)
			{
				$module_info = $module_result->FetchRow();

				$this->sql_query = "select * from ".$this->pages_table." where module = 0";
				$pages_result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is the query<br>\n";
				if (!$pages_result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[5501];
					return false;
				}
				if (!$this->admin_demo())$this->body .= "<form name=module_page_list method=post action=index.php?a=44&z=8&b=".$module_id.">";
				$this->body .= "<table cellpadding=3 cellspacing=1 border=0 class=row_color1>\n";
				$this->title .= "Page Modules > Attach / Remove to Pages</b>";
				$this->description .= "Below is a complete list of pages used within this software.
					The pages that are checked currently have this module attached.  If you wish a module
					functionality to appear on a page you must attach the module to that page and add the module's tag to Template
					that is assigned to the Page.
					<br><br><b>Only attach modules to pages that you use the module functionality within. Redundant module attachments
					will make unnecessary calls to the database, which can slow down your site.</b>";

				$this->row_count = 0;

				$this->body .= "<tr>\n\t<td colspan=2 align=right>
					<SCRIPT LANGUAGE=\"JavaScript\">
					<!-- Begin
					var checkflag = \"false\";
					function check() {
					if (checkflag == \"false\") {
					  for (i = 0; i < module_page_list.elements.length; i++)
					  {
						  var e = module_page_list.elements[i];
						  if (e.name == \"c[]\")
						  {
							e.checked = true;
						  }
					  }
					  checkflag = \"true\";
					  return \"Uncheck all\"; }
					else {
					  for (i = 0; i < module_page_list.elements.length; i++)
					  {
						  var e = module_page_list.elements[i];
						  if (e.name == \"c[]\")
						  {
							e.checked = false;
						  }
					  }
					  checkflag = \"false\";
					  return \"Check all\"; }
					}
					//  End -->
					</script>

				";
				$this->body .= "<input type=button value=\"Check all\" onClick=\"this.value=check(this.form.c)\"></td></tr>";
				$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light><b>page name and description</b> </td>\n\t";
				$this->body .= "<td class=medium_font_light align=center width=80><b>attached</b></a></td>\n</tr>\n";
				while ($show_pages = $pages_result->FetchRow())
				{
					$this->sql_query = "select * from ".$this->pages_modules_table." where page_id = ".$show_pages["page_id"]." and module_id = ".$module_id;
					$module_attached_result = $db->Execute($this->sql_query);
					//$this->body .= $this->sql_query." is the query<br>\n";
					if (!$module_attached_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[5501];
						return false;
					}
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td><a href=index.php?a=44&z=3&b=".$show_pages["page_id"]."><b>";
					$this->body .= "<span class=medium_font><font color=000000>".$show_pages["name"]."</font></span></b></a><br>\n\t";
					//$this->body .= "<span class=small_font>".$show_pages["description"]."</span><br>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font align=center><input type=checkbox name=c[] value=".$show_pages["page_id"]." ";
					if ($module_attached_result->RecordCount() == 1)
						$this->body .= "checked";
					$this->body .= "></td></tr>";
					$this->row_count++;
				}
				if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 align=center><input type=submit value=\"Save\"></td>\n</tr>\n";

				$this->body .= "<tr>\n\t<td colspan=2 align=right><input type=button value=\"Check all\" onClick=\"this.value=check(this.form.c)\"></td></tr>";
				$this->body .= "<tr>\n\t<td colspan=2><a href=index.php?a=74&b=".$module_id."><span class=medium_font><font color=000000><b>back to ".$module_info["name"]." page</b></font></span></a></td>\n</tr>\n";
				$this->body .= "</table>\n</form>";
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

	} //end of function module_to_page_attachments_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_module_to_page_attachments($db,$module_id=0,$attach_info=0)
	{
		if (!$module_id)
			return false;

		$this->sql_query = "delete from ".$this->pages_modules_table." where module_id = ".$module_id;
		$clear_result = $db->Execute($this->sql_query);
		if (!$clear_result)
		{
			return false;
		}
			$clear_result = $db->Execute($this->sql_query);
			if (!$clear_result)
			{
				return false;
			}

		if (is_array($attach_info))
		{
			foreach ($attach_info as $key => $value)
			{
				$this->sql_query = "insert into ".$this->pages_modules_table."
					(page_id,module_id,time)
					values
					(".$value.",".$module_id.",".$this->shifted_time().")";
				$insert_result = $db->Execute($this->sql_query);
				if (!$insert_result)
				{
					return false;
				}
			}
		}
		return true;

			$this->error_message = $this->internal_error_message;
			//echo "whatever";
			return false;
	} //end of function update_module_to_page_attachments

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_modules(&$db)
	{
		$this->function_name = "show_modules";

		$this->sql_query = "select * from ".$this->pages_table." where module = 1 order by name";
		$module_result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$module_result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}
		elseif ($module_result->RecordCount() > 0)
		{
			//display subsections to this section
			$this->title .= "Page Modules List";
			$this->description .= "The list below displays all of the available modules included in this software. Below each module name is
			the tag associated with that module. Insert the module's tag into the html of your page template in order to display the module on your site. Each module
			also has it's own properties (css, text, and switches) that can be applied on module by module basis.";
			$this->body .= "<table cellpadding=3 cellspacing=1 border=0 class=row_color1>\n";
			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=4 class=medium_font_light align=center>\n\t <b>Complete Page Modules List</b> </a>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_black>\n\t<td align=center colspan=2 class=medium_font_light><b>Module Name</b>\n\t</td>\n\t";
			$this->body .= "<td align=center colspan=2 class=medium_font_light>\n\t\n\t</td>\n</tr>";
			$this->row_count = 0;
			while ($show_module = $module_result->FetchRow())
			{
				$tag = str_replace(">", "&gt;", $show_module["module_replace_tag"]);
				$tag = str_replace("<", "&lt;", $tag);
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top colspan=2>\n\t<a href=index.php?a=74&b=".$show_module["page_id"]."><span class=medium_font>".$show_module["name"]."</span></a><br><span class=small_font>".$tag."<br>".$show_module["description"]."</span></td>\n\t";
				$this->body .= "<td align=center valign=top colspan=2><a href=index.php?a=74&b=".$show_module["page_id"]."><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></a></td>\n\t";
				$this->body .= "</tr>\n";
				$this->row_count++;
			}
			$this->body .= "</table>";
		}
		return true;
	} // end of functio show_modules

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_module_in_template($db,$module_tag=0,$page_id=0,$language_id=0)
	{
		if (($page_id) && ($module_tag) && ($language_id))
		{
			//select templates for this page for each language
			$this->sql_query = "select * from ".$this->pages_templates_table."
				where page_id = ".$page_id." and language_id = ".$language_id;
			$template_id_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<bR>\n";
			if (!$template_id_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($template_id_result)
			{
				$template_id = $template_id_result->FetchRow();
				$this->sql_query = "select * from ".$this->templates_table."
					where template_id = ".$template_id["template_id"];
				//echo $this->sql_query."<br>\n";
				$template_result = $db->Execute($this->sql_query);
				if (!$template_result)
				{
					return false;
				}
				elseif ($template_result->RecordCount() == 1)
				{
					$template = $template_result->FetchRow();
					//echo $template["template_code"]." is the template code<bR>\n";
					if (ereg($module_tag, stripslashes($template["template_code"])))
					{
						//tag is within this template
						//echo "in there<Br>\n";
						return true;
					}
					else
					{
						//tag in NOT within the template
						//echo "not in there<Br>\n";
						return false;
					}
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
		}
		else
		{
			return false;
		}
	} //end of function check_module_in_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function browse_module_sections($db,$section=0)
	{

		$this->title = "Page Modules";
		$this->description = "Page Modules allow you to display each module's feature / functionality on the pages that you specify. Each module has its own distinct \"tag\" which you insert within your Templates. Wherever you insert the
		tag into the html of your template is where that Module's functionality will be displayed. So, to use a module, determine that module's \"tag name\", insert that tag into your template, refresh that template through the Pages
		Management section (to ensure the system \"attaches\" the module to the page) and then edit the module's properties to display as you wish. Each module has it's own distinct display properties.";
		if ($section)
			$this->body .= "
				<table cellpadding=4 cellspacing=0 border=0 width=100% class=row_color1>
					<tr bgcolor=000066>
						<td colspan=100%>
							<a href=index.php?a=79>
								<span class=medium_font_light>
									<font face=arial,helvetica size=2 color=#FFFFFF><b>back to Modules Home</b></font>
								</span>
							</a>
						</td>
					</tr>
				</table>";
		$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100% class=row_color1>\n";
		if (!$section)
		{
			$this->function_name = "browse_module_sections";

			$this->sql_query = "select * from ".$this->pages_modules_sections_table." where parent_section = 0";
			//echo $this->sql_query. "is the query<br>";
			$section_result = $db->Execute($this->sql_query);
			//if($this->configuration_data["debug_admin"])
			//{
			//	$this->debug_display($db, $this->filename, $this->function_name, "pages_sections_modules_table", "get page sections data");
			//}
			if (!$section_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				//$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($section_result->RecordCount() > 0)
			{
				$this->row_count = 0;
				while ($show_sections = $section_result->FetchRow())
				{
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top>\n\t<a href=index.php?a=79&b=".$show_sections["section_id"]."><span class=medium_font><font color=000000>".$show_sections["name"]."</font></span></a><span class=small_font>";
					//$this->body .= $show_sections["description"];
					$this->body .= "</span></td>\n\t";
					$this->body .= "<td align=center valign=top width=100><a href=index.php?a=79&b=".$show_sections["section_id"]."><span class=medium_font><img src=admin_images/btn_admin_enter.gif alt=enter border=0></span></a></td>\n\t";
					$this->body .= "</tr>\n";
					$this->row_count++;
				}
			}
			else
			{
				//category does not exist
				$this->error_message = $this->messages["5500"];
				return false;
			}
		}
		else
		{
			//does this section have subsections
			$this->sql_query = "select * from ".$this->pages_modules_sections_table." where parent_section = ".$section;
			//echo $this->sql_query. "is the query<br>";
			$parent_section_result = $db->Execute($this->sql_query);
			if($this->configuration_data["debug_admin"])
			{
				$this->debug_display($db, $this->filename, $this->function_name, "pages_sections_modules_table", "get page sections data");
			}
			if (!$parent_section_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}

			//if it does display these sections in a list like above

			elseif ($parent_section_result->RecordCount() > 0)
			{
				$this->row_count = 0;
				while ($show_sections = $parent_section_result->FetchRow())
				{
					$this->body .= "
						<tr class=".$this->get_row_color().">
							<td valign=top>
								<a href=index.php?a=79&b=".$show_sections["section_id"].">
									<span class=medium_font><font color=000000>".$show_sections["name"]."</font></span></a>
							</td>
							<td align=center valign=top>
								<a href=index.php?a=79&b=".$show_sections["section_id"].">
								<span class=medium_font><img src=admin_images/btn_admin_enter.gif alt=enter border=0></span></a>
							</td>
						</tr>";
					$this->row_count++;
				}
			}
			else
			{
				// it it does not have subsections then call the module list function
				//and pass it the subsection to display modules specific to that section

				$this->sql_query = "SELECT name,admin_label,page_id,module_replace_tag FROM ".$this->pages_table."
					WHERE module = 1 AND module_type=".$section;
				$parent_section_result = $db->Execute($this->sql_query);
				if (!$parent_section_result)
					return false;
				$unsorted_modules = array();
				while ($show_module = $parent_section_result->FetchRow())
					array_push($unsorted_modules,$show_module);
				if ($section == 4)
				{
					//HTML MODULES
					//lets sort the array before displaying
					$new_array = array();
					for($i=1;$i<21;$i++)
					{
						$regexp = ($i==1) ? preg_quote('/HTML>/') : preg_quote('/HTML_'.$i.'>/');
						foreach ($unsorted_modules as $u_module)
						{
							if (preg_match($regexp,$u_module['module_replace_tag']))
								array_push($new_array,$u_module);
						}
					}
					$modules = $new_array;
				}
				else
				{
					//leave the other types of modules alone
					$modules = $unsorted_modules;
				}

				$this->body .= "
						<tr>
							<td colspan=100%>
								<tr class=row_color_red>
									<td align=left class=medium_font_light>
										<b>Module Name</b>
									</td>
									<td align=left width=50% class=medium_font_light>
										<b>Admin Note</b>
									</td>
									<td width=20%>&nbsp;</td>
								</tr>
							</td>
						</tr>";
				$this->row_count = 0;
				foreach ($modules as $module)
				{
					$search = array ('|<|','|>|');
					$replace = array('&lt;','&gt;');
					$tag = preg_replace($search,$replace,$module['module_replace_tag']);
					$this->body .= "
						<tr class=".$this->get_row_color().">
							<td valign=top>
								<a href=index.php?a=74&b=".$module['page_id'].">
									<span class=medium_font>
										<font color=000000>".$module['name']."</font>
									</span>
								</a><br>
								<span class=small_font>$tag</span>
							</td>
							<td class=medium_font align=left>
								".$module['admin_label']."<br>
							</td>
							<td align=center valign=top>
								<a href=index.php?a=74&b=".$module['page_id'].">
									<span class=medium_font>
										<img src=admin_images/btn_admin_edit.gif alt=edit border=0>
									</span>
								</a>
							</td>
						</tr>\n";
					$this->row_count++;
				}
			}
		}
		$this->body .= "</table>\n";
		return true;
	} //end of function browse_module_sections

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_admin_label_and_tag_name($page,$is_page=0,$show_submit=0)
	{
		$type = ($is_page) ? 'page' : 'module';
		$this->body .= "
			<tr class=".$this->get_row_color().">

				<td align=right width=50% class=medium_font>
					<b>admin note:</b><BR>
					<span class=small_font>This <i>note</i> is only a tool to help you keep track of your {$type}s.<BR>
					<b>IMPORTANT: It will only be viewable here in your Administration Menu.</b></span>
				</td>
				<td class=medium_font colspan=50%>
					<nobr>
					<input type=text size=50 name=c[admin_label] value=\"".$page["admin_label"]."\">";
		if ($show_submit && !in_array($page['page_id'],$this->extra_pages))
			$this->body .= "&nbsp;<input type=submit name=save value=\"Save\">";
		$this->body .= "</nobr>
				</td>
			</tr>";
			$this->row_count++;
		if (!$is_page)
		{
			//this is a module, not a page so lets show its tag
			//convert all the angle brackets prior to displaying
			$search = array ('|<|','|>|');
			$replace = array('&lt;','&gt;');
			$show_tag = preg_replace($search,$replace,$page["module_replace_tag"]);
			$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align=right width=50% class=medium_font>
					<b>tag name:</b> <Br>
					<span class=small_font>Insert this tag into your templates to use this module.  Remember to also attach this module to
					the page you wish it to appear on also.</span>
				</td>
				<td class=medium_font>
					$show_tag
				</td>
			</tr>";
			$this->row_count++;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_browsing_count_format($page)
	{
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align=right valign=top width=50% class=medium_font>
					choose how to display category counts
				</td>
				<td width=50% valign=top class=medium_font>
					<select name=c[browsing_count_format]>
						<option ".(($page["browsing_count_format"] == 1) ? "selected" : "")." value=1>
							Display Auction Count Only
						</option>
						<option ".(($page["browsing_count_format"] == 2) ? "selected" : "")." value=2>
							Display Classified Count Only
						</option>
						<option ".(($page["browsing_count_format"] == 3) ? "selected" : "")." value=3>
							Display Auction then Classified Count
						</option>
						<option ".(($page["browsing_count_format"] == 4) ? "selected" : "")." value=4>
							Display Classified then Auction Count
						</option>
						<option ".(($page["browsing_count_format"] == 5) ? "selected" : "")." value=5>
							Combined Count
						</option>
					</select>
				</td>
			</tr>";
		if ($this->modules_debug)
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>browsing_count_format</td></tr>";
		$this->row_count++;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_module_category_level_to_display($page)
	{
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align=right class=medium_font>
					display category count next to category name
				</td>
				<td>
					<span class=medium_font>
						<input type=radio name='c[module_category_level_to_display]' value=0 ".
						(($page["module_category_level_to_display"] == 0) ? "checked" : "").">
						main categories<br>
						<input type=radio name='c[module_category_level_to_display]' value=1 ".
						(($page["module_category_level_to_display"] == 1) ? "checked" : "").">
						second level (all subcategories of the main categories only -- not main categories)
					</span>
				</td>
			</tr>";
		if ($this->modules_debug)
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>module_category_level_to_display</td></tr>";
		$this->row_count++;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_module_display_filter_in_row($page)
	{
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align=right class=medium_font>
					choose how to display dropdowns
				</td>
				<td>
					<span class=medium_font>
						<input type=radio name='c[module_display_filter_in_row]' value=1 ".
						(($page["module_display_filter_in_row"] == 1) ? "checked" : "").">
						display dropdowns in a single row<br>
						<input type=radio name='c[module_display_filter_in_row]' value=0 ".
						(($page["module_display_filter_in_row"] == 0) ? "checked" : "").">
						display dropdowns in a single column
					</span>
				</td>
			</tr>";
		if ($this->modules_debug)
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>module_display_filter_in_row</td></tr>";
		$this->row_count++;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_module_display_username($db,$page)
	{
		$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 12";
		$choices_result = $db->Execute($this->sql_query);
		if (!$choices_result)
			return false;
		elseif ($choices_result->RecordCount() > 0)
		{
			$options = "";
			while ($show = $choices_result->FetchRow())
			{
				$options .= "
					<option value=".$show["value"].(($page["module_display_username"] == $show["value"]) ? "selected" : "").">
						".$show["display_value"]."
					</option>";
			}
		}
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align=right width=50% class=medium_font>
					display options
				</td>
				<td class=medium_font>
					<select name=c[module_display_username]>
						$options
					</select>
				</td>
			</tr>";
		if ($this->modules_debug)
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>module_display_username</td></tr>";
		$this->row_count++;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_module_display_type_listing($page)
	{
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align=right width=50% class=medium_font>
					choose listing types to display
				</td>
				<td class=medium_font>
					<input type=radio name='c[module_display_type_listing]' value=2 ".
					(($page["module_display_type_listing"] == 2) ? "checked" : "").">
					classifieds only<Br>
					<input type=radio name='c[module_display_type_listing]' value=1 ".
					(($page["module_display_type_listing"] == 1) ? "checked" : "").">
					auctions only<Br>
					<input type=radio name='c[module_display_type_listing]' value=0 ".
					(($page["module_display_type_listing"] == 0) ? "checked" : "").">
					classifieds & auctions
				</td>
			</tr>";
		if ($this->modules_debug)
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>module_display_type_listing</td></tr>";
		$this->row_count++;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_module_logged_in_out_html($page)
	{
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align=center colspan=2 class=medium_font>
					logged in html <Br>
					<span class=small_font>
						Where this tag is placed the html below will be displayed when the user is logged in.
					</span>
				</td>
			</tr>
			<tr class=".$this->get_row_color().">
				<td align=left colspan=2>
					<textarea name='c[module_logged_in_html]' style=\"width:100%\" cols=75 rows=30>".$this->special_chars(stripslashes($page["module_logged_in_html"]))."</textarea>
				</td>
			</tr>
			<tr class=".$this->get_row_color().">
				<td align=center colspan=2 class=medium_font>
					logged out html<Br>
					<span class=small_font>
						Where this tag is placed the html below will be displayed when the user is logged out.
					</span>
				</td>
			</tr>
			<tr class=".$this->get_row_color().">
				<td align=left colspan=2>
					<textarea name='c[module_logged_out_html]' style=\"width:100%\" cols=75 rows=30>".$this->special_chars(stripslashes($page["module_logged_out_html"]))."</textarea>
				</td>
			</tr>";
		if ($this->modules_debug)
		{
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>module_logged_in_html</td></tr>";
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>module_logged_out_html</td></tr>";
		}
		$this->row_count++;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_module_thumb_width_height($page)
	{
		//width of thumbnail
		$message = "
			max width of thumbnail<br>
			<span class=small_font>
				If set to 0 the thumb width size will default to the thumb width set in site setup > browsing
			</span>";
		$this->input_option_dropdown($page,'module_thumb_width',$message,1000);

		//height of thumbnail
		$message = "
			max height of thumbnail<br>
			<span class=small_font>
				If set to 0 the thumb height size will default to the thumb height set in site setup > browsing
			</span>";
		$this->input_option_dropdown($page,'module_thumb_height',$message,1000);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_sign_flyer_width_height($db,$page_id,$page)
	{
		$sql_query = "select * from ".$this->ad_configuration_table;
		$ad_result = $db->Execute($sql_query);
		if (!$ad_result)
			return false;
		elseif ($ad_result->RecordCount() == 1)
			$show_ad_configuration = $ad_result->FetchRow();

		$type = ($page_id == 70) ? 'flyer' : 'sign';
		//width of sign/flyer
		$message = "maximum image width to display user image";
		$this->input_option_dropdown($show_ad_configuration,$type.'_maximum_image_width',$message,1000);

		//height of sign/flyer
		$message = "maximum image height to display user image";
		$this->input_option_dropdown($show_ad_configuration,$type.'_maximum_image_height',$message,1000);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_module_display_title($page,$title_only=0)
	{
		// Display text below thumbnail
		$message = ($title_only) ? 'display title' : 'display text below thumbnail';
		$this->input_radio_yes_no($page,'module_display_title',$message);
		if (!$title_only)
		{
			$this->row_count++;
			// Text Type below thumbnail
			// List for text types in dropdown
			$text_types = array('Title' => "title", 'Description' => "description", 'City' => "location_city", 'State' => "location_state",
								'Country' => "location_country", 'Zip' => "location_zip");
			for($i = 1; $i < 21; $i++)
				$text_types[$this->configuration_data['optional_field_'.$i.'_name']] = "optional_field_".$i;
			$this->body .= "
				<tr class=".$this->get_row_color().">
					<td align=right width=50% class=medium_font>
						type of text for title to display below thumbnail<br>
						<span class=small_font>
							This is only enabled if you selected yes above
						</span>
					</td>
					<td class=medium_font>
						<select name=c[module_text_type]>";
			foreach($text_types as $key => $value)
			{
				$this->body .= "<option value=".$value.(($page["module_text_type"] == $value) ? " selected" : "").">".$key."</option>";
			}
			$this->body .= "
						</select>
					</td>
				</tr>";
			if ($this->modules_debug)
				$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>module_text_type</td></tr>";
			$this->row_count++;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_display_empty_message($page)
	{
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align=right width=50% class=medium_font>
					empty message display<br>
					<span class=small_font>
						This message will display when there are no featured listings to display in the location it is being used in.
						To not display a message leave the textarea box emtpy.
					</span>
				</td>
				<td class=medium_font>
					<textarea name=c[display_empty_message]>".urldecode($page["display_empty_message"])."</textarea>
				</td>
			</tr>";
		if ($this->modules_debug)
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>display_empty_message</td></tr>";
		$this->row_count++;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_php_code($page)
	{
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align=right width=50% class=medium_font>
					PHP code to use</td>
				<td class=medium_font>
					<textarea name=c[php_code] cols=40 rows=15>
						".$this->special_chars(stripslashes($page["php_code"]))."
					</textarea>
				</td>
			</tr>";
		if ($this->modules_debug)
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>php_code</td></tr>";
		$this->row_count++;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_category_dropdown($db,$page)
	{
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align=right width=50% class=medium_font>
					category<Br>
					<span class=small_font>
						Choose the category that you wish to display featured listings from within this module.  The only ads that
						will be displayed by this module will be the featured listings within this category.
					</span>
				</td>
				<td class=medium_font>";
		$this->get_category_dropdown($db,"c[module_category]",$page["module_category"],1,$this->configuration_data["levels_of_categories_displayed_admin"]);
		$this->body .= $this->dropdown_body;
		$this->body .= "
				</td>
			</tr>";
		if ($this->modules_debug)
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>module_category</td></tr>";
		$this->row_count++;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_module_display_location($page,$show_address=0)
	{
		if ($show_address)
			$this->input_radio_yes_no($page,'module_display_address','display address');
		$this->input_radio_yes_no($page,'module_display_city','display city');
		$this->input_radio_yes_no($page,'module_display_state','display state');
		$this->input_radio_yes_no($page,'module_display_country','display country');
		$this->input_radio_yes_no($page,'module_display_zip','display zip');
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_module_display_optional_fields($page)
	{
		for ($i=1;$i<21;$i++)
		{
			$message = "display <b>".$this->configuration_data['optional_field_'.$i.'_name']."</b> column";
			$this->input_radio_yes_no($page,'module_display_optional_field_'.$i,$message);
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_title_module_text($db,$page_id,$page)
	{
		// Get text for the home page text
		$this->get_text($db, $page_id);

		$this->sql_query = "select title_module_text from ".$this->ad_configuration_table;
		$result = $db->Execute($this->sql_query);
		if(!$result)
			return false;
		else
			$title_result = $result->FetchRow();
		// Default text
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align=right valign=top width=50% class=medium_font>
					Default Text to display on non-ad pages:
				</td>
				<td width=50% valign=top class=medium_font>
					<input type=text name=c[title_module_text] size=50 value=\"
					".stripslashes(urldecode($title_result["title_module_text"]))."\">
				</td>
			</tr>";
		if ($this->modules_debug)
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>title_module_text</td></tr>";
		$this->row_count++;
		// Home page text
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align=right valign=top width=50% class=medium_font>
					Text to display on home page:
				</td>
				<td width=50% valign=top class=medium_font>
					<input type=text name=c[title_module_home_text] size=50 value=\"
					".stripslashes(urldecode($this->messages[2462]))."\">
				</td>
			</tr>";
		if ($this->modules_debug)
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>title_module_home_text</td></tr>";
		$this->row_count++;
		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_photo_or_icon($page)
	{
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align=right width=50% class=medium_font>
					display photo or icon or use site default within module
				</td>
				<td class=medium_font>
					<input type=radio name=c[photo_or_icon] value=1 ".
					(($page["photo_or_icon"]==1) ? "checked" : "").">
					photo<Br>
					<input type=radio name=c[photo_or_icon] value=3 ".
					(($page["photo_or_icon"]==3) ? "checked" : "").">
					icon<br>
					<input type=radio name=c[photo_or_icon] value=2 ".
					(($page["photo_or_icon"]==2 || !$page["photo_or_icon"]) ? "checked" : "").">
					site default
				</td>
			</tr>";
		if ($this->modules_debug)
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>photo_or_icon</td></tr>";
		$this->row_count++;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_radio_yes_no($key,$variable,$message)
	{
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align='right' width='50%' class='medium_font'>
					$message
				</td>
				<td class=medium_font>
					<input type='radio' name='c[$variable]' value='1' ".
					(($key[$variable] == 1) ? "checked" : "").">
					yes<Br>
					<input type='radio' name='c[$variable]' value='0' ".
					(($key[$variable] == 0) ? "checked" : "").">
					no
				</td>
			</tr>";
		if ($this->modules_debug)
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>$variable</td></tr>";
		$this->row_count++;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_module_display_ad_description_where($page)
	{
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align='right' width='50%' class='medium_font'>
					where to display ad description
				</td>
				<td class=medium_font>
					<input type='radio' name='c[module_display_ad_description_where]' value='1' ".
					(($key[$variable] == 1) ? "checked" : "").">
					below title<Br>
					<input type='radio' name='c[module_display_ad_description_where]' value='0' ".
					(($key[$variable] == 0) ? "checked" : "").">
					own column
				</td>
			</tr>";
		if ($this->modules_debug)
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>module_display_ad_description_where</td></tr>";
		$this->row_count++;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function input_option_dropdown($key,$variable,$message,$MAX=100)
	{
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td align='right' width='50%' class='medium_font'>
					$message
				</td>
				<td class=medium_font>
					<select name='c[$variable]'>";
		for ($i=1;$i<$MAX;$i++)
		{
			$this->body .= "<option ".(($i == $key[$variable]) ? 'selected' : '').">$i</option>";
		}
		$this->body .= "
					</select>
				</td>
			</tr>";
		if ($this->modules_debug)
			$this->body .= "<tr class=".$this->get_row_color()."><td align=center colspan=2 class=medium_error_font>$variable</td></tr>";
		$this->row_count++;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end of class Admin_pages
?>
