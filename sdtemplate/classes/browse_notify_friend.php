<? //browse_notify_friend.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Notify_friend extends Site {
	var $subcategory_array = array();
	var $notify_data = array();
	var $debug_notify = 0;

//########################################################################

	function Notify_friend($db,$classified_user_id,$language_id,$category_id=0,$page=0,$classified_id=0,$affiliate=0,$product_configuration=0)
	{
		if ($this->debug_notify) echo $affiliate." is affiliate in constructor<bR>\n";
		if ($category_id)
			$this->site_category = $category_id;
		elseif ($classified_id)
		{
			$show = $this->get_classified_data($db,$classified_id);
			$this->site_category = $show->CATEGORY;
		}
		else
			$this->site_category = 0;
		if ($limit)
			$this->browse_limit = $limit;
		$this->Site($db,1,$language_id,$classified_user_id,$product_configuration);
		$this->get_ad_configuration($db);
		if ($page)
			$this->page_result = $page;
		else
			$this->page_result = 1;
			
		if (($affiliate) && (is_numeric($affiliate)))
		{
			//check that has affiliate privileges
			$sql_query = "select * from geodesic_user_groups_price_plans where id = ".$affiliate;
			if ($this->debug_notify) echo $sql_query." is the query in constructor<br>\n";
			$aff_group_result = $db->Execute($sql_query);
			if (!$aff_group_result)
			{
				if ($this->debug_notify) echo $sql_query." in constructor<br>\n";
				return false;
			}
			elseif ($aff_group_result->RecordCount() == 1)
			{
				$show_group = $aff_group_result->FetchNextObject();
				$sql_query = "select * from geodesic_groups where group_id = ".$show_group->GROUP_ID;
				if ($this->debug_notify) echo $sql_query." is the query in constructor<br>\n";
				$group_result = $db->Execute($sql_query);
				if (!$group_result)
				{
					if ($this->debug_notify) echo $sql_query." in constructor<br>\n";
					return false;
				}
				elseif ($group_result->RecordCount() == 1)
				{
					$show_affiliate = $group_result->FetchNextObject();
					if ($show_affiliate->AFFILIATE)
					{
						if ($this->debug_notify) echo "this affiliate set to ".$affiliate." in constructor<bR>\n";
						//this is an affiliate
						//get the affiliate template that should be used
						//this will use the browsing category template
						$this->affiliate_id = $affiliate;
						$this->affiliate_group_id = $show_group->GROUP_ID;
					}
					else 
					{
						$this->go_to_classifieds($db);	
					}
				}
				else 
				{
					$this->go_to_classifieds($db);	
				}
			}
			else 
			{
				$this->go_to_classifieds($db);		
			}
		}			
	} //end of function Notify_friend

//###########################################################

	function notify_friend_form($db,$classified_id=0)
	{
		$this->page_id = 4;
		$this->get_text($db);
		if (($this->classified_user_id) && ($classified_id))
		{
			$this->sql_query = "select email,firstname,lastname from ".$this->userdata_table." where id = ".$this->classified_user_id;
			if ($this->debug_notify) echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);

			if (!$result)
			{
				if ($this->debug_notify) echo $this->sql_query."<br>\n";
				$this->error_message = $this->messages[80];
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_user = $result->FetchNextObject();
				$senders_name = $show_user->FIRSTNAME." ".$show_user->LASTNAME;
				$senders_email = $show_user->EMAIL;
			}
			else
			{
				$this->error_message = $this->messages[80];
				return false;
			}
		}

		$this->body .="<form action=";
		if ($this->affiliate_id)
			$this->body .= $this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=12&b=".$classified_id;
		else
			$this->body .= $this->configuration_data['classifieds_url']."?a=12&b=".$classified_id;
		$this->body .= " method=post>\n\t";
		$this->body .="<table width=100% border=0 cellpadding=2 cellspacing=1>\n";
		$this->body .="<TR class=section_title>\n\t<TD colspan=2>\n\t".urldecode($this->messages[603])."\n\t</td>\n</tr>\n";
		$this->body .="<TR class=notify_friend_page_title>\n\t<TD colspan=2>\n\t".urldecode($this->messages[41]);
		$this->display_help_link(338);
		$this->body .="\n\t</td>\n</tr>\n";
		$this->body .="<TR class=notify_friend_form_instructions>\n\t<TD colspan=2>\n\t".urldecode($this->messages[42])."\n\t</td>\n</tr>\n";

		if ($error_message["send_a_friend"] !="")
		{
			$this->body .="<TR class=notify_friend_error>\n\t\t<TD colspan=2>\n\t\t".$error_message["send_a_friend"]."\n\t\t</TD>\n\t</TR>\n\t";
		}

		//friend's name
		$this->body .="<TR>\n\t\t<TD ALIGN=right class=notify_friend_field_labels>\n\t\t".urldecode($this->messages[43])."\n\t\t</TD>\n\t\t";
		$this->body .="<TD class=notify_friend_input_box><INPUT TYPE=TEXT NAME=c[friends_name] SIZE=25 VALUE=\"".$this->notify_data["friends_name"]."\">\n\t\t</TD>\n\t</TR>\n\t";

		// friend's email
		$this->body .="<TR><TD ALIGN=right class=notify_friend_field_labels>".urldecode($this->messages[44])."\n\t\t</TD>\n\t\t";
		$this->body .="<TD class=notify_friend_input_box><INPUT TYPE=TEXT NAME=c[friends_email] SIZE=25 VALUE=\"".$this->notify_data["friends_email"]."\">\n\t\t</TD>\n\t</TR>\n\t";

		//your name
		$this->body .="<TR>\n\t\t<TD ALIGN=right class=notify_friend_field_labels>".urldecode($this->messages[45])."\n\t\t</TD>\n\t\t";
		if ($this->classified_user_id)
		{
			$this->body .="<TD class=notify_friend_field_labels>\n\t\t".$senders_name."\n\t\t</TD>\n\t</TR>\n\t";
		}
		else
		{
			$this->body .="<TD class=notify_friend_input_box>\n\t\t<INPUT TYPE=TEXT NAME=c[senders_name] SIZE=25 VALUE=\"".$senders_name."\">\n\t\t</TD>\n\t</TR>\n\t";
		}

		// your email
		$this->body .="<TR>\n\t\t<TD ALIGN=right class=notify_friend_field_labels>".urldecode($this->messages[46])."\n\t\t</TD>\n\t\t";
		if ($this->classified_user_id)
		{
			$this->body .="<TD class=notify_friend_field_labels>\n\t\t".$senders_email."\n\t\t</TD>\n\t</TR>\n\t";
		}
		else
		{
			$this->body .="<TD class=notify_friend_input_box>\n\t\t<INPUT TYPE=TEXT NAME=c[senders_email] SIZE=25 VALUE=\"".$senders_email."\">\n\t\t</TD>\n\t</TR>\n\t";
		}

		//comment
		$this->body .="<TR>\n\t\t<TD ALIGN=right VALIGN=TOP class=notify_friend_field_labels>\n\t\t".urldecode($this->messages[47])."\n\t\t</TD>\n\t\t";
		$this->body .="<TD class=notify_friend_input_box>\n\t\t<TEXTAREA NAME=c[senders_comments] COLS=23 ROWS=6>".$this->notify_data["senders_comments"]."</TEXTAREA>\n\t\t</td>\n\t</tr>\n\t";

		//submit button
	  	$this->body .="<tr>\n\t\t<td align=center colspan=2>\n\t\t";
	  	$this->body .="<INPUT TYPE=submit NAME=submit value=\"".urldecode($this->messages[52])."\" class=notify_friend_input_box>";
	  	$this->body .="<INPUT TYPE=reset NAME=reset class=notify_friend_input_box>\n\t\t</TD>\n\t</TR>\n\t";
	  	$this->body .="<TR class=notify_friend_link_text>\n\t<TD colspan=2><a href=";
	  	if ($this->affiliate_id)
	  		$this->body .= $this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=2&b=".$classified_id;
	  	else 
	  		$this->body .= $this->configuration_data['classifieds_url']."?a=2&b=".$classified_id;
	  	$this->body .= " class=notify_friend_link_text>".urldecode($this->messages[51])."</a>\n\t</td>\n</tr>\n</TABLE>\n";
	  	$this->body .="</FORM>";

	  	$this->error_found = 0;
	  	$this->display_page($db);
	  	return true;

	} //end of notify_friend_form

//########################################################################

	function verify_notify_friend($db,$classified_id=0,$info=0)
	{
		if (($classified_id) && ($info))
		{
			$this->error_found = 0;
			if (!$this->classified_user_id)
			{
				//check for senders stuff
				if (strlen(trim($info["senders_email"])) > 0)
				{
					if (!eregi("^([a-z0-9]+)(([a-z0-9._-]+))*[@]([a-z0-9]+)([._-]([a-z0-9]+))*[.]([a-z0-9]){2}([a-z0-9])?$", $info["senders_email"]))
					{
						$this->error_message[email] = $this->messages[337];
						$this->error_found++;
					}
				}
				else
				{
					$this->error_message["send_a_friend"] = $this->messages[852];
					$this->error_found++;
				}

				if (strlen(trim($info["senders_name"])) == 0)
				{
					$this->error_message["send_a_friend"] = $this->messages[856];
					$this->error_found++;
				}

			}
			if (strlen(trim($info["friends_email"])) > 0)
			{
				if (!eregi("^([a-z0-9]+)(([a-z0-9._-]+))*[@]([a-z0-9]+)([._-]([a-z0-9]+))*[.]([a-z0-9]){2}([a-z0-9])?$", $info["friends_email"]))
				{
					$this->error_message[email] = $this->messages[853];
					$this->error_found++;
				}
			}
			else
			{
				$this->error_message["send_a_friend"] = $this->messages[851];
				$this->error_found++;
			}

			if (strlen(trim($info["friends_name"])) == 0)
			{
				$this->error_message["send_a_friend"] = $this->messages[855];
				$this->error_found++;
			}

			if ($this->error_found)
			{
				if (!$this->classified_user_id)
				{
					$this->notify_data["senders_name"] = $info["senders_name"];
					$this->notify_data["senders_email"] = $info["senders_email"];
				}

				$this->notify_data["friends_name"] = $info["friends_name"];
				$this->notify_data["friends_email"] = $info["friends_email"];
				$this->notify_data["senders_comments"] = $info["senders_comments"];
				$this->notify_data["classified_id"] = $info["classified_id"];

				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}

	} //end of function verify_notify_friend

//########################################################################

	function notify_friend_($db,$classified_id=0,$info=0)
	{
		$this->page_id = 5;
		$this->get_text($db);
		if (($classified_id) && ($info))
		{
			if ($this->classified_user_id)
			{
				$this->sql_query = "select email,firstname,lastname from ".$this->userdata_table." where id = ".$this->classified_user_id;
				$result = $db->Execute($this->sql_query);
				if ($this->debug_notify) echo $this->sql_query."<br>\n";
				if (!$result)
				{
					//$this->body .=$this->sql_query." is the state query<br>\n";
					$this->error_message = $this->messages[832];
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show_user = $result->FetchNextObject();
					$senders_name = $show_user->FIRSTNAME." ".$show_user->LASTNAME;
					$senders_email = $show_user->EMAIL;
				}
				else
				{
					$this->error_message = $this->messages[832];
					return false;
				}
			}
			else
			{
				$senders_name = $info["senders_name"];
				$senders_email = $info["senders_email"];
			}

			$mailto = $info["friends_email"];
			$subject = stripslashes(urldecode($this->messages[36]." ".$senders_name));
			$message = stripslashes(urldecode($this->messages[37]." ".$info["friends_name"])).",\n";
			$message .= stripslashes(urldecode($senders_name." ".$this->messages[38]))."\n\n";
			if ($this->affiliate_id)
				$message .= $this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=2&b=".$classified_id."\n\n";
			else
				$message .= $this->configuration_data['classifieds_url']."?a=2&b=".$classified_id."\n\n";

			if (strlen(trim($info["senders_comments"])) > 0)
				$message .= urldecode($this->messages[39])."\n".stripslashes($info["senders_comments"])."\n\n";
			$message .= urldecode($this->messages[40])."\n\n";

			if ($this->configuration_data['email_header_break'])
				$separator = "\n";
			else
				$separator = "\r\n";

			$from = "From: ".$senders_email.$separator."Reply-to: ".$senders_email.$separator;

			$additional = "-f".$this->configuration_data['site_email'];
			//@mail($mailto, $subject, $message, $from,$additional);
			//@mail($mailto, $subject, $message, $from);

			$ip = $_SERVER['REMOTE_ADDR'];
   			$host = @gethostbyaddr($ip);
   			//$host = preg_replace("/^[^.]+./", "*.", $host);
			$message .= "\n".$_SERVER["REMOTE_ADDR"]." : ".$host;

			if ($this->configuration_data['email_configuration_type'] == 1)
				$message = str_replace("\n\n","\n",$message);
			if ($this->configuration_data['email_configuration'] == 1)
				mail($mailto, $subject, $message, $from ,$additional);
			elseif ($this->configuration_data['email_configuration'] == 2)
				mail($mailto, $subject, $message, $from);
			else
				mail($mailto, $subject, $message);

			if (strlen(trim($this->configuration_data['admin_email_bcc'])) > 0)
			{
				if ($this->configuration_data['email_configuration'] == 1)
					mail($this->configuration_data['admin_email_bcc'], $subject, $message, $from ,$additional);
				elseif ($this->configuration_data['email_configuration'] == 2)
					mail($this->configuration_data['admin_email_bcc'], $subject, $message, $from);
				else
					mail($this->configuration_data['admin_email_bcc'], $subject, $message);
			}

			$classified_data = $this->get_classified_data($db,$classified_id);
			$this->sql_query = "update ".$this->classifieds_table." set
				forwarded = ".($classified_data->FORWARDED + 1)."
				where id = ".$classified_id;
			$update_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$update_result)
			{
				//$this->body .=$this->sql_query." is the state query<br>\n";
				$this->error_message = $this->messages[832];
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	} //end of function notify_friend

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function notify_success($db,$classified_id)
	{
		$this->page_id = 4;
		$this->get_text($db);
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<TR class=section_title>\n\t<TD colspan=2>\n\t".urldecode($this->messages[603])."\n\t</td>\n</tr>\n";
		$this->body .="<TR class=notify_friend_page_title>\n\t<TD colspan=2>\n\t".urldecode($this->messages[41])."\n\t</td>\n</tr>\n";
		$this->body .="<TR class=notify_friend_form_instructions>\n\t<TD colspan=2>\n\t".urldecode($this->messages[50])."\n\t</td>\n</tr>\n";
		$this->body .="<TR class=notify_friend_link_text>\n\t<TD colspan=2><a href=";
		if ($this->affiliate_id)
			$this->body .= $this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=2&b=".$classified_id;
		else
			$this->body .= $this->configuration_data['classifieds_url']."?a=2&b=".$classified_id;
		$this->body .= " class=notify_friend_link_text>".urldecode($this->messages[51])."</a>\n\t</td>\n</tr>\n";
		$this->body .="</table>\n";
		$this->display_page($db);
		return true;
	} //end of function notify_success

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function go_to_classifieds()
	{
		header("Location: ".$this->configuration_data['classifieds_url']."?".$_SERVER["QUERY_STRING"]);
		exit;		
	} // end of function go_to_classifieds
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Notify_friend

?>