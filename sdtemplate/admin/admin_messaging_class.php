<? //admin_messaging_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Admin_messaging extends Admin_site {

	var $debug_messaging = 0;
	var $do_not_email = 0;  //0 sends the emails, 1 does not send the email
	var $limit = 50; //number of emails to send with each iteration before script resets form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Admin_messaging($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function admin_messaging_form($db,$list_info=0,$message_id=0)
	{
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=25&x=1 method=post>\n";
		$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
		$this->title = "Messaging > Send Message";
		$this->description = "Send messages to your registrants with this
			administration tool.  To the left of the form is a list of registrants that will receive the current message.  If there are a couple you do not wish
			to send this message to uncheck them.  You can send a message to any list of users returned from certain list of users in \"list users\" or a list
			of users returned from a search in \"search users\".  In each you will given a button at the bottom of your results taking you to this messaging
			administration tool and the list of recipients will automatically appear on the left of this form.";

		//checklist of recipients
		$this->body .= "<tr>\n\t<td width=22% valign=top rowspan=2>\n\t<table cellpadding=1 cellspacing=1 border=0 align=center width=100% class=row_color1>\n\t
			<tr class=row_color_black>\n\t\t<td colspan=2 class=medium_font_light>\n\tlist of recipients \n\t\t</td>\n\t</tr>\n\t";
		$this->row_count = 0;
		$this->body .= "<tr class=".$this->get_row_color().">\n\t\t<td align=right valign=top class=medium_font>\n\t<b>All Groups</b> \n\t\t</td>\n\t\t";
		//echo $list_info." is list info<br>\n";
		$this->body .= "<td>\n\t\t<input type=checkbox name=b[all] value=1";
		if (($list_info["all"] == 1) || ($list_info == 0))
			$this->body .= " checked";
		$this->body .= ">\n\t\t</td>\n\t</tr>\n\t";
		$this->row_count++;
		if (count($list_info) > 0)
		{
			reset($list_info);
			while (list($key,$value) = each($list_info))
			{
				if ($key != "all")
				{
					$this->body .= "<tr class=".$this->get_row_color().">\n\t\t<td align=right class=small_font>\n\t".$value." \n\t\t</td>\n\t\t";
					$this->body .= "<td>\n\t\t<input type=checkbox checked name=b[".$key."] value=\"".$value."\">\n\t\t</td>\n\t</tr>\n\t";
					$this->row_count++;
				}
				//else
				//{
				//	echo "<tr class=".$this->get_row_color().">\n\t\t<td align=right class=small_font>\n\t".$value." \n\t\t</td>\n\t\t";
				//	echo "<td>\n\t\t".$key."\n\t\t</td>\n\t</tr>\n\t";
				//	$this->row_count++;
				//}
			}
		}

		// display the groups
		$this->body .= "<tr class=row_color_black>\n\t\t<td colspan=2 class=medium_font_light>\n\tgroups \n\t\t</td>\n\t</tr>\n\t";
		$this->sql_query = "select name, group_id from ".$this->classified_groups_table." order by group_id";
		$result = $db->Execute($this->sql_query);
		if(!$result)
			return false;
		else
			$group_number = 0;
		while($group = $result->FetchRow())
		{
			$this->body .= "<tr class=".$this->get_row_color().">\n\t\t<td align=right valign=top class=medium_font>\n\t<b>".$group["name"]."</b> \n\t\t</td>\n\t\t";
			$this->body .= "<td>\n\t\t<input type=checkbox name=b[group".$group_number."] value=".$group["group_id"].">\n\t\t</td>\n\t</tr>\n\t";
			$group_number++;

		}
		$this->body .= "</table>\n\t</td>\n\t";

		//message body
		$this->body .= "<td width=78% valign=top>\n\t";
		$this->body .= "<table cellpadding=1 cellspacing=1 border=0 align=center width=100% class=row_color2>\n\t";
		if ($message)
		{
			$message = $message["message"];
			$subject = $message["subject"];
		}
		if ($message_id)
		{
			if ($message_id["message_type"] == "select form")
				$this->sql_query = "select * from ".$this->form_messages_table." where message_id = ".$message_id["message_id_form"];
			else
				$this->sql_query = "select * from ".$this->past_messages_table." where message_id = ".$message_id["message_id_past"];
			//echo $this->sql_query;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			if ($result->RecordCount() == 1)
			{
				//get message
				$show_message = $result->FetchRow();
				$message = $show_message["message"];
				$subject = $show_message["subject"];
				$name_of_message = $show_message["message_name"];
			}

			$this->body .= "<tr>\n\t\t<td class=large_font>\n\t<b>Message and subject to be sent</b> \n\t\t</td>\n\t</tr>\n\t";
			$this->body .= "<tr>\n\t\t<td class=medium_font>\n\tConfirm the message and subject by reviewing and editing the form
				below.  Also confirm the list of users to the left.   \n\t\t</td>\n\t</tr>\n\t";
			$this->body .= "<tr>\n\t\t<td class=medium_font>\n\tname of message:<br>
				<input type=input size=50 maxsize=100 name=d[message_name] value=\"".$name_of_message."\"> \n\t\t</td>\n\t</tr>\n\t";
			$this->body .= "<tr>\n\t\t<td class=medium_font>\n\tSubject:<br>
				<input type=input size=50 maxsize=100 name=d[subject] value=\"".$subject."\"> \n\t\t</td>\n\t</tr>\n\t";
			$this->body .= "<tr>\n\t\t<td class=medium_font>\n\tMessage:<br><textarea name=d[message] cols=50 rows=20>".$message."
				</textarea> \n\t\t</td>\n\t</tr>\n\t";

		}
		else
		{
			$this->body .= "<tr>\n\t\t<td class=large_font>\n\t<b>Message and subject to be sent</b> \n\t\t</td>\n\t</tr>\n\t";
			$this->body .= "<tr>\n\t\t<td class=medium_font>\n\tCreate your own message and subject below, choose one of the
				messages sent in the past or choose one of the form messages you have created in the past.  If you fill in the form
				with new messages your message will be sent to the list of users to the left once you click send a message.  If you choose
				either a previous or form message you will be able to edit the message before being sent.  \n\t\t</td>\n\t</tr>\n\t";
			//display the past messages list
			$this->get_last_ten_messages_list($db);

			//display the form messages list
			$this->get_form_messages_list($db);

			$this->body .= "<tr>\n\t\t<td class=medium_font>\n\tmessage name:<br>
				<input type=input size=50 maxsize=100 name=d[message_name] value=\"".$name_of_message."\"> \n\t\t</td>\n\t</tr>\n\t";
			$this->body .= "<tr>\n\t\t<td class=medium_font>\n\tsubject:<br>
				<input type=input size=50 maxsize=100 name=d[subject] value=\"".$subject."\"> \n\t\t</td>\n\t</tr>\n\t";
			$this->body .= "<tr>\n\t\t<td class=medium_font>\n\tmessage:<br>
				<textarea name=d[message] cols=50 rows=20>".$this->special_chars($message)."</textarea> \n\t\t</td>\n\t</tr>\n\t";
		}


		if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td align=center valign=top>\n\t<input type=submit name=z value=\"Send\">\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n</form>";
		return true;
	} //end of function admin_message_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_last_ten_messages_list($db)
	{
		//get the last ten messages
		$this->sql_query = "select * from ".$this->past_messages_table." order by date_sent desc limit 0,10";
		$past_messages_result = $db->Execute($this->sql_query);
		if (!$past_messages_result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($past_messages_result->RecordCount() > 0)
		{
			$this->body .= "<tr>\n\t\t<td>\n\t\t<select name=c[message_id_past]>\n\t\t\t";
			while ($show = $past_messages_result->FetchRow())
			{
				$this->body .= "<option value=".$show["message_id"].">".$show["subject"]." - ".date("M d,Y G:i - D",$show["date_sent"])."</option>\n\t\t\t";
			}
			$this->body .= "</select>\n\t\t";
			if (!$this->admin_demo()) $this->body .= "<input type=submit name=c[message_type] value=\"Submit\">\n\t\t";
			$this->body .= "</td>\n\t</tr>\n\t";
		}

	} //end of function get_last_ten_messages_list

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_form_messages_list($db)
	{
		//get the form messages list
		$this->sql_query = "select * from ".$this->form_messages_table." order by message_name";
		$form_messages_result = $db->Execute($this->sql_query);
		if (!$form_messages_result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($form_messages_result->RecordCount() > 0)
		{
			$this->body .= "<tr>\n\t\t<td>\n\t\t<select name=c[message_id_form]>\n\t\t\t";
			while ($show = $form_messages_result->FetchRow())
			{
				$this->body .= "<option value=".$show["message_id"].">".$show["message_name"]."</option>\n\t\t\t";
			}
			$this->body .= "</select>\n\t\t";
			if (!$this->admin_demo()) $this->body .= "<input type=submit name=c[message_type] value=\"Save\">\n\t\t";
		}
		else
		{
			$this->body .= "<tr>\n\t\t<td class=medium_font align=center>\n\t<b>There are currently no form messages to display.</b><br><br> \n\t\t";
		}
		//echo "<br><a href=index.php?a=26><span class=medium_font>click to add or edit form messages</span></a>";
		$this->body .= "</td>\n\t</tr>\n\t";

	} //end of function get_form_messages_list

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_form_messages_list($db)
	{
		//get the form messages list
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=25&x=2 method=post>\n\t";
		$this->body .= "<table cellpadding=2 cellspacing=0 border=0 align=center width=100% class=row_color1>\n\t";
		$this->title = "Messaging > Form Messages";
		$this->description = "The following is the list of form messages
			you can use in sending messages to your users.  Click the edit link to \"edit\" that message or \"delete\" to remove the message from
			the list.  You can add a new form message using the form below";
		$this->sql_query = "select * from ".$this->form_messages_table." order by message_name";
		$form_messages_result = $db->Execute($this->sql_query);
		if (!$form_messages_result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($form_messages_result->RecordCount() > 0)
		{
			$this->row_count = 0;
			$this->body .= "<tr>\n\t<td colspan=2 align=center>\n\t<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
			$this->body .= "<tr class=row_color_black>\n\t\t<td class=medium_font_light>\n\t<b>message name</b> \n\t\t</td>\n\t\t";
			$this->body .= "<td colspan=2 class=medium_font>\n\t&nbsp;\n\t\t</td>\n\t</tr>\n\t";
			while ($show = $form_messages_result->FetchRow())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t\t<td class=medium_font>".$show["message_name"]." \n\t\t</td>\n\t\t";
				$this->body .= "<td width=100 align=center>\n\t\t<a href=index.php?a=25&x=2&b=".$show["message_id"]."><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></a>\n\t\t</td>\n\t\t";
				$this->body .= "<td width=100 align=center>\n\t\t<a href=index.php?a=25&x=2&c=".$show["message_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a>\n\t\t</td>\n\t</tr>\n\t";
				$this->row_count++;
			}
			$this->body .= "</table>\n\t</td>\n</tr>\n";
		}
		else
		{
			$this->body .= "<tr>\n\t\t<td colspan=2 class=medium_font align=center>\n\t<b>There are currently no form messages to display.</b><br><br> \n\t\t";
		}

		//display the form to add a new form message
		$this->body .= "<tr>\n\t<td colspan=2 align=center>\n\t<table cellpadding=2 cellspacing=0 border=0 class=row_color1 width=100%>\n\t";
		$this->body .= "<tr bgcolor=000066>\n\t<td colspan=2 class=medium_font_light align=center>\n\t<b>Add New Form Message</b> \n\t</td>\n</tr>\n";
    	$this->body .= "<tr>\n\t<td align=right width=50% class=medium_font>\n\t<b>form message name:</b> \n\t</td>\n\t";
		$this->body .= "<td width=50% class=medium_font>\n\t<input type=text name=d[form_name] size=30 maxsize=50> \n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\t<b>form subject:</b> \n\t</td>\n\t";
		$this->body .= "<td class=medium_font>\n\t<input type=text name=d[form_subject] size=30 maxsize=50> \n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=2 align=center valign=top class=medium_font>\n\t<b>form message</b><br>
			<textarea name=d[form_message] cols=50 rows=20></textarea> \n\t</td>\n</tr>\n";
		if (!$this->admin_demo())
		{
			$this->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center>\n\t<input type=submit name=submit value=\"Save\">
				 \n\t</td>\n</tr>\n";
		}
		$this->body .= "</table>\n\t</td>\n</tr>\n</table>\n</form>\n";
		return true;
	} //end of function get_form_messages_list

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function form_message_edit($db,$message_id=0)
	{
		if ($message_id)
		{
			//get the form messages list
			$this->sql_query = "select * from ".$this->form_messages_table." where message_id = ".$message_id;
			$form_messages_result = $db->Execute($this->sql_query);
			if (!$form_messages_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($form_messages_result->RecordCount() == 1)
			{
				$show = $form_messages_result->FetchRow();
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=25&x=2&b=".$message_id." method=post>\n\t";
				$this->body .= "<table cellpadding=2 cellspacing=0 class=row_color1 border=0 align=center width=100%>\n\t";
				$this->title = "Messaging > Edit Form Message";
				$this->description = "Edit the chosen form message below.  When you are through click the \"save\" button.";

				$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\t<b>form message name:</b> \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t
					<input type=text name=e[form_name] size=30 maxsize=50 value=\"".$show["message_name"]."\"> \n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\t<b>form subject:</b> \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t
					<input type=text name=e[form_subject] size=30 maxsize=50 value=\"".$show["subject"]."\"> \n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td colspan=2 class=medium_font>\n\t<b>form message:</b><br>
					<textarea name=e[form_message] cols=65 rows=20>".$this->special_chars($show["message"])."</textarea> \n\t</td>\n</tr>\n";
				if (!$this->admin_demo())
				{
					$this->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center>\n\t<input type=submit name=submit value=\"Save\">
					 \n\t</td>\n</tr>\n";
				}
				$this->body .= "</table>\n</form>\n";
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
	} //end of function form_message_edit

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_new_message($db,$message_info=0)
	{
		if ($message_info)
		{
			if ((strlen(trim($message_info["form_name"])) > 0) &&
				(strlen(trim($message_info["form_subject"])) > 0) &&
				(strlen(trim($message_info["form_message"])) > 0))
			{
				//insert the new info
				$this->sql_query = "insert into ".$this->form_messages_table."
					(message_name,subject,message)
					values
					(\"".addslashes($message_info["form_name"])."\",\"".addslashes($message_info["form_subject"])."\",
					\"".addslashes($message_info["form_message"])."\")";
				if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
					$this->site_error($db->ErrorMsg());
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
			return false;

		}

	} //end of function insert_new_message

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_form_message($db,$message_id=0)
	{
		if ($message_id)
		{
			//insert the new info
			$this->sql_query = "delete from ".$this->form_messages_table."
				where message_id = ".$message_id;
			if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}

	} //end of function delete_form_message

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_message_form($db,$message_id=0,$message_info=0)
	{
		if (($message_id) && ($message_info))
		{
			if ((strlen(trim($message_info["form_name"])) > 0) &&
				(strlen(trim($message_info["form_subject"])) > 0) &&
				(strlen(trim($message_info["form_message"])) > 0))
			{
				//insert the new info
				$this->sql_query = "update ".$this->form_messages_table." set
					message_name = \"".addslashes($message_info["form_name"])."\",
					subject = \"".addslashes($message_info["form_subject"])."\",
					message = \"".addslashes($message_info["form_message"])."\"
					where message_id = ".$message_id;

				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->site_error($db->ErrorMsg());
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
			return false;

		}

	} //end of function update_message_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function send_admin_message($db,$list_info=0,$message_info=0,$start_from=0,$message_id=0)
	{
		if (($list_info) && (($message_info) || ($message_id)))
		{
			$limit = $this->limit;
			if (!$start_from)
				$start_from = 0;

			$todays_date = $this->shifted_time();
			if (!$message_id)
			{
				if (count($list_info) > 0)
				{
					reset($list_info);
					while (list($key,$value) = each($list_info))
					{
						if ($key == "all")
						{
							$send_to_all = 1;
						}
						elseif(substr_count($key,"group") > 0)
						{
							$send_to_groups = 1;
							$groups[] = $value;
						}
					}
				}
				if(!$send_to_all)
					$send_to_all = 0;
				if(!$send_to_groups)
					$send_to_groups = 0;

				if ((strlen(trim($message_info["subject"])) > 0) &&
					(strlen(trim($message_info["message"])) > 0))
				{
					//insert the new info

					$this->sql_query = "insert into ".$this->past_messages_table."
						(date_sent,all_sent,message_name,subject,message)
						values
						(".$todays_date.",".$send_to_all.", \"".addslashes($message_info["message_name"])."\",
						\"".addslashes($message_info["subject"])."\", \"".addslashes($message_info["message"])."\")";
					$result = $db->Execute($this->sql_query);
					if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
					if (!$result)
					{
						if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
						$this->site_error($db->ErrorMsg());
						return false;
					}
					$message_id = $db->Insert_ID();
				}
			}
			else
			{
				if ($list_info == 1)
					$send_to_all = 1;
				else
					$send_to_all = 0;

				$this->sql_query = "select * from ".$this->past_messages_table."
					where message_id = ".$message_id;
				$result = $db->Execute($this->sql_query);
				if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
				if (!$result)
				{
					if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show_message = $result->FetchRow();
					$message_info["subject"] = stripslashes($show_message["subject"]);
					$message_info["message"] = stripslashes($show_message["message"]);
					$message_info["message_name"] = stripslashes($show_message["message_name"]);
				}
				else
				{
					return false;
				}
			}

			$this->sql_query = "select * from ".$this->site_configuration_table;
			$site_email_result = $db->Execute($this->sql_query);
			if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
			if (!$site_email_result)
			{
				if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($site_email_result->RecordCount() == 1)
			{
				$show_site = $site_email_result->FetchRow();
			}
			else
			{
				if ($this->debug_messaging) echo $this->sql_query." - returns ".$site_email_result->RecordCount()." results when it should<bR>\n";
				return false;
			}

			$subject = stripslashes(urldecode($message_info["subject"]));
			$message = stripslashes(urldecode($message_info["message"]));

			if ($send_to_all)
			{
				$this->sql_query = "select count(*) as total_receivers from ".$this->userdata_table." where id != 1";
				$total_result = $db->Execute($this->sql_query);
				if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
				if (!$total_result)
				{
					if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
					$this->site_error($db->ErrorMsg());
					return false;
				}

				$this->sql_query = "select username,email from ".$this->userdata_table." where id != 1 limit ".$start_from.",".$limit;
				$result = $db->Execute($this->sql_query);
				if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
				if (!$result)
				{
					if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif (($result->RecordCount() > 0) && ($total_result))
				{
					$show_total = $total_result->FetchRow();
					if ($show_total["total_receivers"] > $limit)
					{
						//loop through the emails using the limit as the max number per invocation
						$this->body .= "<html><head><title>sent to ".$start_from." of ".$show_total["total_receivers"]." so far</title>";
						$this->body .= "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"10;URL=".$_SERVER['PHP_SELF']."?a=25&x=1&e=".$message_id."&b=1&start_from=".($start_from + $limit)."\">";
						$this->body .= "</head><body><table width=100%>";
						$this->body .= "<tr class=row_color_black><td class=medium_font_light>Emails have just been sent to:</td></tr>";
						while ($show = $result->FetchRow())
						{
							@set_time_limit(300);
							$additional = "-f".$show_site["site_email"];
							if ($show_site["email_header_break"])
								$separator = "\n";
							else
								$separator = "\r\n";
							$from = "From: ".$show_site["site_email"].$separator."Reply-to: ".$show_site["site_email"].$separator;
							if (!$this->do_not_email)
							{
								if ($this->debug_messaging)
								{
									echo "sending to: ".$show["email"]."<bR>\n";
								}
								if ($show_site["email_configuration"] == 1)
									mail($show["email"],$subject,$message,$from,$additional);
								elseif ($show_site["email_configuration"] == 2)
									mail($show["email"],$subject,$message,$from);
								else
									mail($show["email"],$subject,$message);
							}

							$this->body .= "<tr class=".$this->get_row_color().">\n\t
								<td class=medium_font>\n\t".$show["username"]." - ".$show["email"]." \n\t</td>\n</tr>\n";
							$this->row_count++;
						}
						$this->body .= "<tr class=row_color_black><td class=medium_font_light>sent to ";
						if (($start_from + $limit) > $show_total["total_receivers"])
							$this->body .= "all chosen recipients";
						else
							$this->body .= ($start_from + $limit)." of ".$show_total["total_receivers"]." so far</td></tr>";
						$this->body .= "</table></body></html>";

					}
					else
					{
						$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n";
						$this->title .= "Admin Messaging";
						$this->body .= "<tr>\n\t<td align=center colspan=2 class=medium_font><b>The following
							message has been sent:</b><br><br> \n\t</td>\n</tr>\n";
						$this->body .= "<tr>\n\t<td valign=top align=left class=medium_font width=20%>\n\t<b>subject:</b> \n\t</td>\n\t";
						$this->body .= "<td class=medium_font>\n\t".$subject." \n\t</td>\n</tr>\n";
						$this->body .= "<tr>\n\t<td valign=top align=left class=medium_font>\n\t<b>message:</b> \n\t</td>\n\t";
						$this->body .= "<td class=medium_font>\n\t<pre>".$message."</pre> \n\t</td>\n</tr>\n";
						$this->body .= "<tr class=row_color_black height=1>\n\t<td colspan=2></td>\n</tr>\n";
						$this->row_count = 0;
						while ($show = $result->FetchRow())
						{
							@set_time_limit(300);
							$additional = "-f".$show_site["site_email"];
							if ($show_site["email_header_break"])
								$separator = "\n";
							else
								$separator = "\r\n";
							$from = "From: ".$show_site["site_email"].$separator."Reply-to: ".$show_site["site_email"].$separator;
							if (!$this->do_not_email)
							{
								if ($this->debug_messaging)
								{
									echo "sending to: ".$show["email"]."<bR>\n";
								}
								if ($show_site["email_configuration"] == 1)
									mail($show["email"],$subject,$message,$from,$additional);
								elseif ($show_site["email_configuration"] == 2)
									mail($show["email"],$subject,$message,$from);
								else
									mail($show["email"],$subject,$message);
							}
							$this->body .= "<tr class=".$this->get_row_color().">\n\t
								<td colspan=2 class=medium_font>\n\t".$show["username"]." - ".$show["email"]." \n\t</td>\n</tr>\n";
							$this->row_count++;
						}
						$this->body .= "<tr><td colspan=2><a href=index.php><span class=medium_font><br><br><b>back to Admin Home</b></span></a></td></tr>\n";
						$this->body .= "</table>\n";
					}
				}
				else
				{
					$this->admin_header($db,$user_level,$_REQUEST["a"],$_REQUEST["z"]);
					$this->body .= "<table width=100%>";
					$this->body .= "<tr class=row_color_black><td class=medium_font_light>email message sent to complete list</td></tr>\n";
					$this->body .= "<tr class=row_color_black><td><a href=index.php><span class=medium_font_light>return to admin</span></a></td></tr>\n";
					$this->body .= "</table>";
				}
			}
			elseif($send_to_groups)
			{
				// Send to specific groups
				// get username and password
				$this->sql_query = "select username, email from ".$this->userdata_table." as user, ".$this->user_groups_price_plans_table." as groups where user.id = groups.id and user.id != 1 and (";
				if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
				reset($groups);
				for($i = 0; $i < sizeof($groups); $i++)
				{
					$this->sql_query .= " groups.group_id = ".$groups[$i];
					if($i != sizeof($groups)-1)
						$this->sql_query .= " or";
				}
				$this->sql_query .= ")";
				$result = $db->Execute($this->sql_query);
				if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
				if (!$result)
				{
					if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif($result->RecordCount() > 0)
				{
					$show_total = sizeof($groups);
					if ($show_total > $limit)
					{
						//loop through the emails using the limit as the max number per invocation
						$this->body .= "<html><head>";
						$this->body .= "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"10;URL=".$_SERVER['PHP_SELF']."?a=25&x=1&e=".$message_id."&b=1\">";
						$this->body .= "</head><body><table width=100%>";
						$this->body .= "<tr class=row_color_black><td class=medium_font_light>Emails have just been sent to:</td></tr>";
						while ($show = $result->FetchRow())
						{
							@set_time_limit(300);
							$additional = "-f".$show_site["site_email"];
							if ($show_site["email_header_break"])
								$separator = "\n";
							else
								$separator = "\r\n";
							$from = "From: ".$show_site["site_email"].$separator."Reply-to: ".$show_site["site_email"].$separator;
							if (!$this->do_not_email)
							{
								if ($this->debug_messaging)
								{
									echo "sending to: ".$show["email"]."<bR>\n";
								}
								if ($show_site["email_configuration"] == 1)
									mail($show["email"],$subject,$message,$from,$additional);
								elseif ($show_site["email_configuration"] == 2)
									mail($show["email"],$subject,$message,$from);
								else
									mail($show["email"],$subject,$message);
							}

							$this->body .= "<tr class=".$this->get_row_color().">\n\t
								<td class=medium_font>\n\t".$show["username"]." - ".$show["email"]." \n\t</td>\n</tr>\n";
							$this->row_count++;
						}
						$this->body .= "<tr class=row_color_black><td class=medium_font_light>sent to all chosen recipients";
						$this->body .= "</table></body></html>";

					}
					else
					{
						$this->body .= "<table cellpadding=0 cellspacing=1 border=0 align=center>\n";
						$this->body .= "<tr class=row_color_black>\n\t<td align=center colspan=2 class=large_font_light>The following
							message has been sent \n\t</td>\n</tr>\n";
						$this->body .= "<tr class=row_color_black>\n\t<td valign=top align=right class=medium_font_light>\n\tsubject: \n\t</td>\n\t";
						$this->body .= "<td class=medium_font_light>\n\t".$subject." \n\t</td>\n</tr>\n";
						$this->body .= "<tr class=row_color_black>\n\t<td valign=top align=right class=medium_font_light>\n\tmessage: \n\t</td>\n\t";
						$this->body .= "<td class=medium_font_light>\n\t<pre>".$message."</pre> \n\t</td>\n</tr>\n";
						$this->row_count = 0;
						while ($show = $result->FetchRow())
						{
							@set_time_limit(300);
							$additional = "-f".$show_site["site_email"];
							if ($show_site["email_header_break"])
								$separator = "\n";
							else
								$separator = "\r\n";
							$from = "From: ".$show_site["site_email"].$separator."Reply-to: ".$show_site["site_email"].$separator;
							if (!$this->do_not_email)
							{
								if ($this->debug_messaging)
								{
									echo "sending to: ".$show["email"]."<bR>\n";
								}
								if ($show_site["email_configuration"] == 1)
									mail($show["email"],$subject,$message,$from,$additional);
								elseif ($show_site["email_configuration"] == 2)
									mail($show["email"],$subject,$message,$from);
								else
									mail($show["email"],$subject,$message);
							}
							$this->body .= "<tr class=".$this->get_row_color().">\n\t
								<td colspan=2 class=medium_font>\n\t".$show["username"]." - ".$show["email"]." \n\t</td>\n</tr>\n";
							$this->row_count++;
						}
						$this->body .= "<tr class=row_color_black><td colspan=2><a href=index.php><span class=medium_font_light>return to admin</span></a></td></tr>\n";
						$this->body .= "</table>\n";
					}
				}
				else
				{
					$this->admin_header($db,$user_level,$_REQUEST["a"],$_REQUEST["z"]);
					$this->body .= "<table width=100%>";
					$this->body .= "<tr class=row_color_black><td class=medium_font_light>email message sent to complete list</td></tr>\n";
					$this->body .= "<tr class=row_color_black><td><a href=index.php><span class=medium_font_light>return to admin</span></a></td></tr>\n";
					$this->body .= "</table>";
				}
			}
			else
			{
				//send to just the ones on the list
				$this->body .= "<table cellpadding=0 cellspacing=1 border=0 align=center>\n";
				$this->body .= "<tr class=row_color_black>\n\t<td align=center colspan=2 class=large_font_light>The following
					message has been sent \n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color_black>\n\t<td valign=top align=right class=medium_font_light>\n\tsubject: \n\t</td>\n\t";
				$this->body .= "<td class=medium_font_light>\n\t".$subject." \n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color_black>\n\t<td valign=top align=right class=medium_font_light>\n\tmessage: \n\t</td>\n\t";
				$this->body .= "<td class=medium_font_light>\n\t<pre>".$message."</pre> \n\t</td>\n</tr>\n";
				$this->row_count = 0;
				reset($list_info);
				while (list($key,$value) = each($list_info))
				{
					@set_time_limit(300);
					$this->sql_query = "select email from ".$this->userdata_table." where id = ".$key;
					$result = $db->Execute($this->sql_query);
					if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
					if (!$result)
					{
						if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
						$this->site_error($db->ErrorMsg());
						return false;
					}
					$show_email = $result->FetchRow();
					$additional = "-f".$show_site["site_email"];
					if ($show_site["email_header_break"])
						$separator = "\n";
					else
						$separator = "\r\n";
					$from = "From: ".$show_site["site_email"].$separator."Reply-to: ".$show_site["site_email"].$separator;
					if ($this->debug_messaging)
					{
						echo $show_site["email_configuration"]." is email config<Br>\n";
						echo $show_site["email_header_break"]." is EMAIL_HEADER_BREAK<br>\n";
					}
					if (!$this->do_not_email)
					{
						if ($this->debug_messaging)
						{
							echo "sending to: ".$show_email["email"]."<bR>\n";
						}
						if ($show_site["email_configuration"] == 1)
							mail($show_email["email"],$subject,$message,$from,$additional);
						elseif ($show_site["email_configuration"] == 2)
							mail($show_email["email"],$subject,$message,$from);
						else
							mail($show_email["email"],$subject,$message);

					}
					$this->body .= "<tr class=".$this->get_row_color().">\n\t
						<td colspan=2 class=medium_font>\n\t".$value."- ".$show_email["email"]." \n\t</td>\n</tr>\n";
					$this->sql_query = "insert into ".$this->past_messages_recipients_table."
						(user_id,message_id)
						values
						(".$key.",".$message_id.")";
					$result = $db->Execute($this->sql_query);
					if ($this->debug_messaging) $this->body .= $this->sql_query."<bR>\n";
					if (!$result)
					{
						if ($this->debug_messaging) echo $this->sql_query."<bR>\n";
						$this->site_error($db->ErrorMsg());
						return false;
					}
					$this->row_count++;
				} //end of while
				$this->body .= "</table>\n";
			}
			return true;
		}
		else
		{
			//echo "no message info";
			return false;

		}

	} //end of function send_admin_message

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_message_history($db)
	{
		//get the message history list
		$this->body .= "<table cellpadding=2 cellspacing=0 border=0 align=center width=100%>\n\t";
		$this->title .= "Messaging > Messages Sent";
		$this->description .= "The list below is the list of messages that
			have been sent to registrants in the past.  View details of a specific message by clicking the \"details\" link next to the appropriate
			message.  Delete a message from the history by clicking the \"delete\" link next to the appropriate message.";
		$this->sql_query = "select * from ".$this->past_messages_table." order by date_sent desc";
		$form_messages_result = $db->Execute($this->sql_query);
		if (!$form_messages_result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($form_messages_result->RecordCount() > 0)
		{
			$this->row_count = 0;
			$this->body .= "<tr>\n\t<td colspan=2>\n\t<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
			$this->body .= "<tr class=row_color_black>\n\t\t<td class=medium_font_light>\n\t<b>message name </b>\n\t\t</td>\n\t\t";
			$this->body .= "<td class=medium_font_light>\n\t<b>date sent</b> \n\t\t</td>\n\t\t";
			$this->body .= "<td class=medium_font_light>\n\t<b>subject</b> \n\t\t</td>\n\t\t";
			$this->body .= "<td colspan=2 class=medium_font_light>\n\t&nbsp; \n\t\t</td>\n\t</tr>\n\t";
			while ($show = $form_messages_result->FetchRow())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td valign=top class=medium_font>\n\t".$show["message_name"]." \n\t</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t".date("M d,Y G:i - l",$show["date_sent"])." \n\t</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t".$show["subject"]." \n\t</td>\n\t";
				$this->body .= "<td valign=top width=100 align=center>\n\t<a href=index.php?a=25&x=3&b=".$show["message_id"]."><span class=medium_font><img src=admin_images/btn_admin_view.gif alt=view border=0></span></a>\n\t</td>\n\t";
				$this->body .= "<td valign=top width=100 align=center>\n\t<a href=index.php?a=25&x=3&c=".$show["message_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a>\n\t</td>\n</tr>\n";
				$this->row_count++;
			}
			$this->body .= "</table>\n\t</td>\n</tr>\n";
		}
		else
		{
			$this->body .= "<tr>\n\t\t<td colspan=2 class=medium_font align=center>\n\t<b>There are currently no messages to display.</b> \n\t\t";
		}
		$this->body .= "</table>\n";
		return true;
	} //end of function display_message_history

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_from_message_history($db,$message_id=0)
	{
		if ($message_id)
		{
			//insert the new info
			$this->sql_query = "delete from ".$this->past_messages_table."
				where message_id = ".$message_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			$this->sql_query = "delete from ".$this->past_messages_recipients_table."
				where message_id = ".$message_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}

	} //end of function delete_form_message

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_message_history_detail($db,$message_id)
	{
		if ($message_id)
		{
			$this->sql_query = "select * from ".$this->past_messages_table." where message_id = ".$message_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();

				//get the message details
				$this->body .= "<table cellpadding=2 cellspacing=0 border=0 align=center width=100% class=row_color1>\n\t";
				$this->title = "Message History Details";
				$this->description = "Below is the details of the message that was sent.";
				$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\t<b>message name:</b> \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t".$show["message_name"]." \n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color2>\n\t<td align=right class=medium_font>\n\t<b>date sent:</b> \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t".date("M d,Y G:i - l",$show["date_sent"])." \n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\t<b>subject:</b> \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t".$show["subject"]." \n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color2>\n\t<td colspan=2 class=medium_font>\n\t<b>message:</b> <br>";
				$this->body .= "\n\t<pre>".$show["message"]."</pre>\n\t</td>\n</tr>\n";

				//display list of registrants that received this message
				$this->body .= "<tr>\n\t<td colspan=2 class=medium_font>\n\t<b>list of users that recieved this message:</b> \n\t</td>\n</tr>\n";
				if ($show["all_sent"])
				{
					$this->body .= "<tr>\n\t<td colspan=2 class=medium_font>\n\tthis message was sent to all current registrants at the time \n\t</td>\n</tr>\n";
				}
				else
				{
					$this->body .= "<tr>\n\t<td colspan=2>\n\t<table cellpadding=1 cellspacing=1 border=0 width=100%>\n\t";
					$this->sql_query = "select * from ".$this->past_messages_recipients_table." where message_id = ".$message_id;
					$message_result = $db->Execute($this->sql_query);
					if (!$message_result)
					{
						$this->site_error($db->ErrorMsg());
						return false;
					}
					elseif ($message_result->RecordCount() > 0)
					{
						while ($show_message_recipient = $message_result->FetchRow())
						{
							$this->sql_query = "select username,email from ".$this->userdata_table." where id = ".$show_message_recipient["user_id"];
							$user_result = $db->Execute($this->sql_query);
							if (!$user_result)
							{
								$this->site_error($db->ErrorMsg());
								return false;
							}
							elseif ($user_result->RecordCount() == 1)
							{
								$show_user = $user_result->FetchRow();
								$this->body .= "<tr>\n\t\t<td align=center class=small_font>\n\t".$show_user["username"]." - ".$show_user["email"]." \n\t\t</td>\n\t</tr>\n\t";
							}
						}
					}
					else
					{
						$this->body .= "<tr>\n\t\t<td class=small_font>\n\tno recipients \n\t\t</td>\n\t</tr>\n\t";
					}
					$this->body .= "</table>\n\t</td>\n</tr>\n";
				}
				$this->body .= "<tr class=row_color_black>\n\t<td colspan=2>\n\t
					<a href=index.php?a=25&x=3><span class=medium_font_light>back to messaging history</span></a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
				return true;
			}
			else
			{
				$this->body .= "<tr>\n\t\t<td colspan=2 class=medium_font>\n\tthere are no messages to display \n\t\t";
			}
			$this->body .= "<tr class=row_color_black>\n\t<td colspan=2>\n\t
				<a href=index.php?a=25&x=3><span class=medium_font_light>back to messaging history</span></a>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			return true;
		}
		else
		{
			return false;
		}
	} //end of function display_message_history_detail

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function messaging_home()
	{
		$this->body .= "<table cellpadding=3 cellspacing=0 width=100% border=0 align=center class=row_color1>\n";
		$this->title = "Admin Message Administration configuration";
		$this->description = "Communicate with your registered users through
			this administration tool.  If you want to only send messages to a few users find the users within the \"search users\" section of the
			administration.  At the bottom of the search result there will be a \"send a message\" button.  Click that button and you will be shown the
			form allowing you to send those users a message.";
		$this->body .= "<tr>\n\t\t<td align=right valign=top><a href=index.php?a=25&x=1><span class=medium_font><b>send message</b></span></a>\n\t\t</td>\n\t\t
			<td class=medium_font>send a message to all users </a>\n\t\t</td>\n\t</tr>\n\t";
		$this->body .= "<tr class=row_color2>\n\t\t<td align=right valign=top><a href=index.php?a=25&x=2><span class=medium_font><b>form messages</b></span></a>\n\t\t</td>\n\t\t
			<td class=medium_font>edit the form messages you can send to your registered users through this form administration tool </a>\n\t\t</td></tr>\n\t";
		$this->body .= "<tr>\n\t\t<td align=right valign=top><a href=index.php?a=25&x=3><span class=medium_font><b>message history</b></span></a>\n\t\t</td>\n\t\t
			<td class=medium_font>view the messages you have sent to users in the past </a>\n\t\t</td></tr>\n\t";
		$this->body .= "</table>\n";
		return true;
	} //end of function messaging_home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Admin_messaging

?>
