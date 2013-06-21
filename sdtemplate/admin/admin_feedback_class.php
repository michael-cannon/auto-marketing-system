<? //admin_feedback_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Admin_feedback extends Admin_site
{
	var $filename = "admin_feedback_class.php";

	var $error_code;

	var $debug_feedback = 0;

//########################################################################

	function Admin_feedback($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	}

//########################################################################

	function display_icon_info($db)
	{
		$function_name = "display_icon_info";

		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center width=100% class=row_color1 >\n";
		$this->title .= "Feedback Management";
		$this->description .= "The Feedback Menu allows you to configure the properties and images associated with the feedback functionality of your site. You can allow your sellers and buyers the ability to provide feedback comments and ratings to each other for each transaction they conduct. This feedback will encourage, or discourage, future visitors from conducting business with those users, because each user’s comments and ratings are displayed on the site next to their username.<br><br>
					Use this page to designate certain \"images\" to be displayed next each user’s username depending on their current feedback rating, which is tabulated by adding all of the users feedback scores. Each user who’s score falls within that scoring window below will have the appropriate color star next to their username on the Listing Display Page. In order for visitors to know what these stars actually represent, we have included a \"help\" popup box next to each star that gets displayed on your site. You can change the html content of this popup box to match the settings you have placed below by editing the appropriate text area on the following admin page:<br>PAGES MANAGEMENT > BROWSING > LISTING DISPLAY PAGE";

		$this->body .= "<tr><td>";
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=100% class=row_color2 >\n";

		$this->row_count = 0;
		$this->body .= "<form action=\"index.php?a=110\" method=\"post\">";

		$dropdown = "<select name=\"feedback[number_of_feedbacks_to_display]\">";
		for($i = 1; $i < 101; $i++)
		{
			$dropdown .= "<option value=\"".$i."\"";
			if($this->configuration_data['number_of_feedbacks_to_display'] == $i)
				$dropdown .= " selected";
			$dropdown .= ">".$i."</option>";
		}
		$dropdown .= "</select>";
		$this->body .= "<tr bgcolor=000066><td class=medium_font_light align=center colspan=4><b>Feedback Settings</b></td></tr>\n\t";
		$this->body .= "<tr class=\"".$this->get_row_color()."\">\n\t
								<td colspan=100%>
									<table class=\"".$this->get_row_color()."\" width=100%>
										<tr>
											<td class=medium_font width=60%>
												Choose the number of feedback details to display on each page:
											</td>
											<td align=left class=medium_font>
												".$dropdown."
											</td>
										</tr>
									</table>
								</td>
							</tr>";
		$this->row_count++;
		$this->body .= "<tr ".$this->get_row_color().">
								<td align=center colspan=100%><input type=\"submit\" value=\"Save\"></td>
							</tr>";
		$this->row_count++;

		$this->body .= "</form>";

		$this->body .= "<tr bgcolor=000066><td class=medium_font_light align=center colspan=4><b>Current Feedback Increments</b></td></tr>\n\t";

		// Show table with data
		$this->sql_query = "select * from ".$this->feedback_icons_table." where begin > 0 order by icon_num asc";
		if($this->configuration_data['debug_admin'])
		{
			$this->debug_display($db, $this->filename, $function_name, "feedback_icons_table", "get auction feedback icon info");
		}
		if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
		$icon_result = $db->Execute($this->sql_query);
		if (!$icon_result)
		{
			$this->error_code = 2;
			if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif($icon_result->RecordCount() > 0)
		{
			$this->body .= "<tr class=row_color_black>\n\t<td align=center class=medium_font_light><b>Low End</b></td>\n\t
			<td>&nbsp;</td>\n\t
			<td align=center class=medium_font_light><b>High End</b></font></td>\n\t
			<td align=center class=medium_font_light><b>Icon</b></font></td>\n</tr>\n";

			while($show = $icon_result->FetchNextObject())
			{
				if(!$mod)
					$this->body .= "<tr class=row_color2 >\n\t";
				else
					$this->body .= "<tr class=row_color1 >\n\t";
				$this->body .= "<td align=center class=medium_font>\n\t".$show->BEGIN." ";
				$this->body .= "\n\t</td>\n\t
					<td align=center class=medium_font>\n\tto</font>\n\t</td>\n\t
					<td align=center class=medium_font>\n\t";
				if ($show->END == 100000000)
					$this->body .= "and up";
				else
					$this->body .= $show->END."\n\t</td>\n\t";
				$this->body .= "<td align=center>\n\t<img src=\"../".$show->FILENAME."\">\n\t</td>\n</tr>\n";

				// Alternate the row colors
				$mod = !$mod;
			}
		}
		else
			$this->body .= "<tr class=\"".$this->get_row_color()."\"><td align=\"center\"><span class=medium_font><b>There are currently no feedback icons set for the scores.</b></span></td></tr>";

		$this->body .= "</table>\n";
		$this->body .= "</td></tr>";

		$this->body .= "<tr bgcolor=000066><td class=medium_font_light align=center><b>Additional Feedback Criteria</b>
			</font></td></tr>";
		$this->sql_query = "select * from ".$this->feedback_icons_table." where begin = 0 order by icon_num asc";
		if($this->configuration_data['debug_admin'])
		{
			$this->debug_display($db, $this->filename, $function_name, "feedback_icons_table", "get 0 auction feedback icon info");
		}
		if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
		$zero_icon_result = $db->Execute($this->sql_query);
		if (!$zero_icon_result)
		{
			$this->error_code = 2;
			if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif($zero_icon_result->RecordCount() == 1)
		{
			$zero_icon = $zero_icon_result->FetchNextObject();
			$this->body .= "<tr><td><table cellpadding=3 cellspacing=1 border=0 width=100%>";
			$this->body .= "<tr class=row_color2 >\n\t<td class=medium_font>for feedback ratings equal to 0</td>";
			$this->body .= "<td align=center>\n\t";
			if (strlen(trim($zero_icon->FILENAME)) > 0)
				$this->body .= "<img src=\"../".$zero_icon->FILENAME."\" border=0>";
			else
				$this->body .= "<span class=medium_font>no image set</span>";
			$this->body .= "\n\t</td>\n</tr>\n";
			$this->body .= "</table></td></tr>";
		}

		$this->sql_query = "select * from ".$this->feedback_icons_table." where begin = -1 order by icon_num asc";
		if($this->configuration_data['debug_admin'])
		{
			$this->debug_display($db, $this->filename, $function_name, "feedback_icons_table", "get 0 auction feedback icon info");
		}
		if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
		$negative_icon_result = $db->Execute($this->sql_query);
		if (!$negative_icon_result)
		{
			$this->error_code = 2;
			if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif($negative_icon_result->RecordCount() == 1)
		{
			$negative_icon = $negative_icon_result->FetchNextObject();
			$this->body .= "<tr><td><table cellpadding=3 cellspacing=1 border=0 width=100%>";
			$this->body .= "<tr class=row_color1 >\n\t<td class=medium_font>for feedback ratings less than 0</font></td>";
			$this->body .= "<td align=center>\n\t";
			if (strlen(trim($negative_icon->FILENAME)) > 0)
				$this->body .= "<img src=\"../".$negative_icon->FILENAME."\" border=0>";
			else
				$this->body .= "<span class=medium_font>no image set</span>";
			$this->body .= "\n\t</td>\n</tr>\n";
			$this->body .= "</table></td></tr>";
		}

		$this->body .= "<tr class=row_color1 align=center><td><a href=index.php?a=110&z=1><img src=admin_images/btn_admin_edit.gif border=0 alt=edit></font></a></td></tr>";
		$this->body .= "</table>";

		return true;

	} // End of display_icon_info

//########################################################################

	function update_feedback_settings($db, $feedback_settings)
	{
		$this->sql_query = "update ".$this->site_configuration_table." set ";
		foreach ($feedback_settings as $key => $value)
		{
			$this->sql_query .= $key." = ".$value.", ";
		}

		$this->sql_query = rtrim($this->sql_query, " ,");
		$result = $db->Execute($this->sql_query);
		if(!$result)
			return false;
		else
			return true;
	}

//########################################################################

	function display_icon_form($db)
	{
		$function_name = "display_icon_form";

		$sql_query = "select * from ".$this->feedback_icons_table." where begin > 0 order by icon_num asc";
		if($this->configuration_data['debug_admin'])
		{
			$this->debug_display($db, $this->filename, $function_name, "feedback_icons_table", "get auction feedback icon info");
		}
		if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
		$icon_result = $db->Execute($sql_query);
		if (!$icon_result)
		{
			$this->error_code = 2;
			$this->site_error($db->ErrorMsg());
			if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
			return false;
		}

		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=110 method=post>\n";
		$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1  width=100%>\n";
		if ($this->ad_configuration_message)
			$this->body .= "<tr>\n\t<td colspan=2 class=medium_error_font>\n\t".$this->ad_configuration_message."</font>\n\t</td>\n</tr>\n";
		$this->title .= "Feedback Management > Edit Icons";
		$this->description .= "You can control the aspects of the feedback system through this administration.  Control the number of feedback icons and their ranges with the feedback score.  In the URL field enter the full URL of the icon and in the score fields enter the range of scores you want.  Please put these in numerical order from lowest value to highest for optimal performance.  To designate a value as being from something on up put your lower value in the min and check the and up checkbox.  To delete or not use an icon leave all fields corresponding to it blank.";

		// Display the form loop
		for($i = 0; $i < 10; $i++)
		{
			$icons = $icon_result->FetchNextObject();

			if(!$mod)
				$this->body .= "<tr class=row_color2 >\n\t";
			else
				$this->body .= "<tr class=row_color1 >\n\t";
			$this->body .= "<td align=right width=50% valign=top class=medium_font>\n\tURL for icon ".($i+1).":</font>\n\t\n\t</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input size=55 type=text name=b[icon][".$i."] value=\"";
			if($icons->ICON_NUM == $i && $icons->FILENAME)
				$this->body .= "".$icons->FILENAME;
			$this->body .= "\">\n\t</font>\n\t\n\t</td>\n</tr>\n";

			if(!$mod)
				$this->body .= "<tr class=row_color2 >\n\t";
			else
				$this->body .= "<tr class=row_color1 >\n\t";
			$this->body .= "<td align=right valign=top class=medium_font>\n\tMinimum score for icon".($i+1).":</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=text name=b[min_icon][".$i."] size=5 value=\"";
			if($icons->BEGIN)
				$this->body .= $icons->BEGIN;
			$this->body .= "\">\n\t</td>\n</tr>\n";

			if(!$mod)
				$this->body .= "<tr class=row_color2 >\n\t";
			else
				$this->body .= "<tr class=row_color1 >\n\t";
			$this->body .= "<td align=right valign=top class=medium_font>\n\tMaximum score for icon".($i+1).":</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=text name=b[max_icon][".$i."] size=5 value=\"";
			if($icons->END && $icons->END != 100000000)
				$this->body .= $icons->END;
			$this->body .= "\">\n\t";
			$this->body .= "&nbsp;&nbsp;And up? <input type=checkbox name=b[and_up][".$i."] ";
			if($icons->END == 100000000)
				$this->body .= "checked";
			$this->body .= "></td>\n</tr>\n";
			if($icons->ICON_NUM == $i && $icons->FILENAME)
			{
				if(!$mod)
					$this->body .= "<tr align=center class=row_color2 >\n\t";
				else
					$this->body .= "<tr align=center class=row_color1 >\n\t";
				$this->body .= "<td colspan=2><table align=center cellspacing=0 width=100%>";
				if(!$mod)
					$this->body .= "<tr align=center class=row_color2 >\n\t";
				else
					$this->body .= "<tr align=center class=row_color1 >\n\t";
				$this->body .= "<td width=100% valign=top>\n\t";
				$this->body .= "<img src=\"../".$icons->FILENAME."\">";
				$this->body .= "\n\t</td>\n</tr>\n</table></td></tr>";
			}

			// Alternate the row colors
			$mod = !$mod;
		}

		//display the 0 rating icon
		$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=medium_font_light align=center>\n\t<b>Icon displayed when a user has a feedback rating of 0 (typically new users).</b></font>\n\t</td>\n</tr>\n";

		$sql_query = "select * from ".$this->feedback_icons_table." where begin = 0 order by icon_num asc";
		if($this->configuration_data['debug_admin'])
		{
			$this->debug_display($db, $this->filename, $function_name, "feedback_icons_table", "get 0 auction feedback icon info");
		}
		if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
		$zero_icon_result = $db->Execute($sql_query);
		if (!$zero_icon_result)
		{
			$this->error_code = 2;
			$this->site_error($db->ErrorMsg());
			if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
			return false;
		}

		$zero_icon = $zero_icon_result->FetchNextObject();
		$this->body .= "<tr class=row_color1 >\n\t";
		$this->body .= "<td align=right width=50% valign=top class=medium_font>\n\tURL for icon of a feedback rating that is 0:</font>\n\t\n\t</td>\n\t";
		$this->body .= "<td valign=top class=medium_font>\n\t<input size=55 type=text name=b[zero_icon] value=\"";
		if(strlen(trim($zero_icon->FILENAME)) > 0)
			$this->body .= "".$zero_icon->FILENAME;
		$this->body .= "\">\n\t</font>\n\t\n\t</td>\n</tr>\n";
		if(strlen(trim($zero_icon->FILENAME)) > 0)
		{
			$this->body .= "<tr><td colspan=2 align=center><img src=../".$zero_icon->FILENAME."></td></tr>";
		}

		//display the negative rating icon
		$sql_query = "select * from ".$this->feedback_icons_table." where begin = -1 order by icon_num asc";
		if($this->configuration_data['debug_admin'])
		{
			$this->debug_display($db, $this->filename, $function_name, "feedback_icons_table", "get negative auction feedback icon info");
		}
		if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
		$negative_icon_result = $db->Execute($sql_query);
		if (!$negative_icon_result)
		{
			if ($this->debug_feedback) echo $this->sql_query."<bR>\n";
			$this->error_code = 2;
			$this->site_error($db->ErrorMsg());
			return false;
		}
		$negative_icon = $negative_icon_result->FetchNextObject();
		$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=medium_font_light align=center>\n\t<b>Icon displayed
			when a user has a feedback rating of less than 0 (due to negative feedbacks).</b></font>\n\t</td>\n</tr>\n";
		$this->body .= "<tr class=row_color1 >\n\t";
		$this->body .= "<td align=right width=50% valign=top class=medium_font>\n\tURL for icon for a feedback rating that is less than 0:</font>\n\t\n\t</td>\n\t";
		$this->body .= "<td valign=top class=medium_font>\n\t<input size=55 type=text name=b[negative_icon] value=\"";
		if(strlen(trim($negative_icon->FILENAME)) > 0)
			$this->body .= "".$negative_icon->FILENAME;
		$this->body .= "\">\n\t</font>\n\t\n\t</td>\n</tr>\n";
		if(strlen(trim($negative_icon->FILENAME)) > 0)
		{
			$this->body .= "<tr><td colspan=2 align=center><img src=../".$negative_icon->FILENAME."></td></tr>";
		}
		if (!$this->admin_demo())
			$this->body .= "<tr>\n\t<td colspan=2 align=center class=medium_font>\n\t<input type=submit value=\"Save\" name=submit></font>\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";

		return true;
	} // End of display_icon_form

//########################################################################

	function update_feedback_icon($db, $info=0)
	{
		$function_name = "update_feedback";

		// Find last element to insert
		$last = 9;
		for($i = 0; $i < 10; $i++)
		{
			if($info['and_up'][$i] == 'on')
			{
				$last = $i;
				break;
			}
			elseif((!$info['icon'][$i] && !$info['min_icon'][$i] && !$info['max_icon'][$i]))
			{
				$last = $i-1;
				break;
			}
		}

		if($this->configuration_data['debug_admin'])
		{
			echo $last . ' is $last';
			echo '<br><br>';
		}


		// Range checking
		for($i = 0; $i < $last; $i++)
		{
			if($this->configuration_data['debug_admin'])
			{
				echo '$i is '. $i . '<br>';
				echo $info['icon'][$i] . ' is i $info[icon]['.$i.']<br>';
				echo $info['min_icon'][$i] . ' is i $info[min_icon]['.$i.']<br>';
				echo $info['max_icon'][$i] . '  is i $info[max_icon]['.$i.']<br>';
				echo $info['and_up'][$i] . ' is i $info[and_up]['.$i.']<br><br>';
			}

			for($j = 1; $j < $last+1; $j++)
			{
				if($this->configuration_data['debug_admin'])
				{
					echo '$j is '. $j . '<br>';
					echo $info['icon'][$j] . ' is j $info[icon]['.$j.']<br>';
					echo $info['min_icon'][$j] . ' is j $info[min_icon]['.$j.']<br>';
					echo $info['max_icon'][$j] . '  is j $info[max_icon]['.$j.']<br>';
					echo $info['and_up'][$j] . ' is j $info[and_up]['.$j.']<br><br>';
				}

				if($info['min_icon'][$i] >=  $info['min_icon'][$j] &&
				   $info['max_icon'][$i] >=  $info['max_icon'][$j] &&
				   $info['min_icon'][$i] >=  $info['max_icon'][$i] &&
				   $info['min_icon'][$j] >=  $info['max_icon'][$j] &&
				   $info['max_icon'][$i] >=  $info['min_icon'][$j])
				{
					$this->error_code = 1;
					return false;
				}
			}
		}

		// Clear out the table
		$this->sql_query = "delete from ".$this->feedback_icons_table." where begin > 0";
		if($this->configuration_data['debug_admin'])
		{
			$this->debug_display($db, $this->filename, $function_name, "feedback_icons_table", "delete auction feedback icon info");
		}
		$result = $db->Execute($this->sql_query);
		if(!$result)
		{
			$this->error_code = 2;
			return false;
		}

		// Insert elements
		for($i = 0; $i < $last+1; $i++)
		{
			if($info['and_up'][$i]) $info['max_icon'][$i] = 100000000;

			$this->sql_query = "insert into ".$this->feedback_icons_table." (filename, icon_num, begin, end) values (\"".$info['icon'][$i]."\", ".$i.", ".$info['min_icon'][$i].", ".$info['max_icon'][$i].")";
			if($this->configuration_data['debug_admin'])
			{
				$this->debug_display($db, $this->filename, $function_name, "feedback_icons_table", "insert new auction feedback icon info");
			}
			$result = $db->Execute($this->sql_query);
			if(!$result)
			{
				$this->error_code = 2;
				return false;
			}
		}

		//update 0 feedback symbol
		$this->sql_query = "update ".$this->feedback_icons_table." set
			filename =\"".$info['zero_icon']."\"
			where begin = 0";
		$result = $db->Execute($this->sql_query);
		if($this->configuration_data['debug_admin'])
		{
			$this->debug_display($db, $this->filename, $function_name, "feedback_icons_table", "update 0 auction feedback icon info");
		}

		//update negative feedback symbol
		$this->sql_query = "update ".$this->feedback_icons_table." set
			filename =\"".$info['negative_icon']."\"
			where begin = -1";
		$result = $db->Execute($this->sql_query);
		if($this->configuration_data['debug_admin'])
		{
			$this->debug_display($db, $this->filename, $function_name, "feedback_icons_table", "update -1 auction feedback icon info");
		}

		return true;

	} // End update_feedback

//########################################################################

	function edit_feedback_form($db, $auction_id, $rater_id)
	{
		$function_name = "edit_feedback_form";

		$this->sql_query = "select * from ".$this->feedbacks_table." where rater_user_id = ".$rater_id." and auction_id = ".$auction_id;
		if($this->configuration_data['debug_admin'])
		{
			$this->debug_display($db, $this->filename, $function_name, "feedbacks_table", "get feedback");
		}
		$result = $db->Execute($this->sql_query);

		if($result)
		{
			echo "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1  width=100%>\n";
			if ($this->ad_configuration_message)
				echo "<tr>\n\t<td colspan=2 class=medium_error_font>\n\t".$this->ad_configuration_message."</font>\n\t</td>\n</tr>\n";
			echo "<tr class=row_color_red>\n\t<td colspan=2 class=very_large_font_light>\n\t<b>Edit Feedback</b></font>\n\t</td>\n</tr>\n";
			echo "<tr class=row_color_red>\n\t<td colspan=2 class=medium_font_light>\n\tEdit the feedback for the user in the box below to how you like it and then hit Save.</font>\n\t</td>\n</tr>\n";

			// Output the feedback
			$show = $result->FetchNextObject();
			if (!$this->admin_demo())echo "<form action=index.php?a=110&b=".$auction_id."&c=".$rater_id." method=post>\n";
			echo "<tr align=center>\n\t<td>\n\t";
			echo "<tr align=center>\n\t<td>\n\t";
			echo "<input type=radio name=d[rate] ";
			if ($show->RATE == -1) echo "checked";
			echo " value=-1>Negative<br>\n\t\t
				<input type=radio name=d[rate] ";
			if ($show->RATE == 0) echo "checked";
			echo " value=0>Neutral<br>\n\t\t
				<input type=radio name=d[rate] ";
			if ($show->RATE == 1) echo "checked";
			echo " value=1>Positive\n\t\t</td>\n\t</tr>\n\t";
			echo "<tr align=center>\n\t<td>\n\t<textarea name=d[feedback] rows=10 cols=30>".$this->special_chars(urldecode($show->FEEDBACK))."</textarea></td>\n</tr>\n";
			if (!$this->admin_demo())
				echo "<tr align=center>\n\t<td>\n\t<input type=submit value=\"Save\" name=save_feedback>\n\t</td>\n</tr>\n";
			echo "</form></table>";
			return true;
		}
		else
		{
			$this->error_code = 2;
			return false;
		}
	} // End edit_feedback_form

//########################################################################

	function update_edit_feedback($db, $auction_id, $rater_id, $feedback)
	{
		$function_name = "update_edit_feedback";

		$this->sql_query = "select * from ".$this->feedbacks_table." where rater_user_id = ".$rater_id." and auction_id = ".$auction_id;
		$result = $db->Execute($this->sql_query);
		if(!$result)
			return false;
		else
			$previous = $result->FetchNextObject();

		//echo $feedback['feedback'] . ' is the feedback<br>';
		//echo $feedback['rate'] . ' is the rate<br>';
		$this->sql_query = "update ".$this->feedbacks_table." set feedback = \"".$feedback['feedback']."\" where auction_id = ".$auction_id." and rater_user_id = ".$rater_id;
		//echo $this->sql_query . '<Br>';
		$result = $db->Execute($this->sql_query);
		$this->sql_query = "update ".$this->feedbacks_table." set rate = ".$feedback['rate']." where auction_id = ".$auction_id." and rater_user_id = ".$rater_id;
		$result2 = $db->Execute($this->sql_query);
		//echo $this->sql_query . '<Br>';

		if(!$result || !$result2)
		{
			$this->error_code = 2;
			return false;
		}

		// If rates match we are finished
		if($previous->RATE == $feedback["rate"])
			return true;

		// Fix the count up in the userdata table
		$this->sql_query = "select rated_user_id from ".$this->feedbacks_table." where rater_user_id = ".$rater_id." and auction_id = ".$auction_id;
		$result = $db->Execute($this->sql_query);
		//echo $this->sql_query .'<br>';
		if(!$result)
			return false;
		else
		{
			$rated_user_result = $result->FetchNextObject();
			$rated_id = $rated_user_result->RATED_USER_ID;
		}

		$this->sql_query = "select * from ".$this->userdata_table." where id = ".$rated_id;
		$result = $db->Execute($this->sql_query);
		if(!$result)
			return false;
		else
			$userdata = $result->FetchNextObject();

		if($feedback["rate"] == 1 && $previous->RATE != 1)
		{
			$this->sql_query = "update ".$this->userdata_table." set feedback_positive_result = ".($userdata->FEEDBACK_POSITIVE_COUNT+1);
			if($previous->RATE == 0)
			{
				// If the previous rate was neutral
				$this->sql_query .= " and feedback_score = ".($userdata->FEEDBACK_SCORE+1);
			}
			else
			{
				// If the previous rate was negative
				$this->sql_query .= " and feedback_score = ".($userdata->FEEDBACK_SCORE+2);
			}
		}
		elseif($feedback["rate"] == 0 && $previous->RATE != 0)
		{
			$this->sql_query = "update ".$this->userdata_table." set ";
			if($previous->RATE == 1)
			{
				// If the previous rate was positive
				$this->sql_query .= "feedback_score = ".($userdata->FEEDBACK_SCORE-1);
			}
			else
			{
				// If the previous rate was negative
				$this->sql_query .= "feedback_score = ".($userdata->FEEDBACK_SCORE+1);
			}
		}
		elseif($feedback["rate"] == -1 && $previous->RATE != -1)
		{
			$this->sql_query = "update ".$this->userdata_table." set ";
			if($previous->RATE == 1)
			{
				// If the previous rate was positive
				$this->sql_query .= "feedback_score = ".($userdata->FEEDBACK_SCORE-2);
			}
			else
			{
				// If the previous rate was neutral
				$this->sql_query .= "feedback_score = ".($userdata->FEEDBACK_SCORE-1);
			}
		}

		$result = $db->Execute($this->sql_query);
		if(!$result)
			return false;
		else
			return true;
	}

//########################################################################

	function delete_feedback($db, $auction_id, $rater_user_id, $rated_user_id)
	{
		if($auction_id && $rater_user_id && $rated_user_id)
		{
			$this->sql_query = "select * from ".$this->feedbacks_table.", ".$this->userdata_table." where rater_user_id = ".$rater_user_id." and auction_id = ".$auction_id." and rated_user_id = ".$rated_user_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query.'<br>';
			if(!result)
				return false;
			else
				$feedback_result = $result->FetchNextObject();

			$this->sql_query = "delete from ".$this->feedbacks_table." where rater_user_id = ".$rater_user_id." and auction_id = ".$auction_id." and rated_user_id = ".$rated_user_id;
			$result = $db->Execute($this->sql_query);
			if(!$result)
				return false;

			$this->sql_query = "update ".$this->userdata_table." set feedback_count = ".($feedback_result->FEEDBACK_COUNT-1);
			if($feedback_result->RATE == 1)
				$this->sql_query .= " and feedback_positive_count = ".($feedback_result->FEEDBACK_POSITIVE_COUNT-1)." and feedback_score = ".($feedback_result->FEEDBACK_SCORE-1);
			elseif($feedback_result->RATE == -1)
				$this->sql_query .= " and feedback_score = ".($feedback_result->FEEDBACK_SCORE+1);

			$this->sql_query .= " where username = ".$feedback_result->RATED_USER_ID;

			$result = $db->Execute($this->sql_query);
			if(!$result)
				return false;
		}
		else
			return false;

		return true;
	}

//########################################################################

	function feedback_error()
	{
		switch($this->error_code)
		{
			// Bounds value problem
			case 1:
				$error = "Error with your range of values, please make sure that that none of your values overlap and that all high ends are greater than the low end and vice versa.";
				break;

			// DB error
			case 2:
				$error = "Database error.  Please file a bug report with Geodesic Solutions.";
				break;

			// Other error
			default:
				$error = "Misc Error.  Please file a bug report.";
				break;
		}

		echo $error . "\n<br>";

		return true;

	} // End feedback_error
}

//########################################################################
?>
