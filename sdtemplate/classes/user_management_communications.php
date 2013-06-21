<? //user_management_communications.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class User_management_communications extends Site
{
	var $debug_comm = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function User_management_communications ($db,$language_id,$classified_user_id=0,$product_configuration=0)
	{
		$this->Site($db,6,$language_id,$classified_user_id,$product_configuration);

	} //end of function User_management

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_communications($db)
	{
		$this->page_id = 24;
		$this->get_text($db);
		$this->body .= "<table width=100% cellpadding=2 cellspacing=1 border=0>";
		$this->body .="<tr class=user_management_page_title><td colspan=5>\n\t\t".urldecode($this->messages[624])."\n\t\t</td>\n\t</tr>\n\t";
		$this->body .="<tr class=my_current_messages_title >\n\t\t<td colspan=5>\n\t\t".urldecode($this->messages[387]);
		$this->body .=$this->display_help_link(389);
		$this->body .="\n\t\t</td>\n\t</tr>\n\t";
		if ($this->classified_user_id)
		{
			$this->sql_query = "select * from ".$this->user_communications_table." where message_to = ".$this->classified_user_id." order by
				date_sent desc";
			//$this->body .=$this->sql_query." is the query<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$this->body .="<tr class=page_instructions>\n\t\t<td colspan=5>\n\t\t".urldecode($this->messages[388])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=table_column_headers >\n\t\t<td>\n\t\t".urldecode($this->messages[391])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[392])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[393])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t"."&nbsp;\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t"."&nbsp;\n\t\t</td>\n\t</tr>\n\t";

				$this->row_count = 0;
				while ($show = $result->FetchNextObject())
				{
					if (($this->row_count % 2) == 0)
						$css_style = "result_set_even_rows";
					else
						$css_style = "result_set_odd_rows";
					$this->body .="<tr class=".$css_style.">\n\t\t<td>\n\t\t";
					if ($show->MESSAGE_FROM)
						$sender = $this->get_user_name($db,$show->MESSAGE_FROM);
					else
						$sender = $show->MESSAGE_FROM_NON_USER;
					$this->body .=$sender;
					$this->body .="\n\t\t</td>\n\t\t";
					$this->body .="<td>\n\t\t";
					$ad_title = $this->get_ad_title($db,$show->REGARDING_AD);
					$this->body .=$ad_title."\n\t\t</td>\n\t\t";
					$this->body .="<td>\n\t\t".date("M j, Y - h:i - l",$show->DATE_SENT)."\n\t\t</td>\n\t\t";
					$this->body .="<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=8&c=1&d=".$show->MESSAGE_ID.">".urldecode($this->messages[394])."</a>\n\t\t</td>\n\t\t";
					$this->body .="<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=8&c=2&d=".$show->MESSAGE_ID.">".urldecode($this->messages[395])."</a>\n\t\t</td>\n\t</tr>\n\t";
					$this->row_count++;
				}
				$this->body .="<tr class=communication_configuration_link>\n\t<td colspan=5>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=7>".urldecode($this->messages[396])."</a>\n\t</td>\n</tr>\n";
				$this->body .="<tr class=user_management_home_link>\n\t<td colspan=5>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4>".urldecode($this->messages[397])."</a>\n\t</td>\n</tr>\n";
			}
			else
			{
				//there are no communications for this user
				$this->body .="<tr class=page_instructions>\n\t\t<td olspan=5>".urldecode($this->messages[390])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=communication_configuration_link>\n\t<td olspan=5>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=7>".urldecode($this->messages[396])."</a>\n\t</td>\n</tr>\n";
				$this->body .="<tr class=user_management_home_link>\n\t<td olspan=5>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4>".urldecode($this->messages[397])."</a>\n\t</td>\n</tr>\n";
			}
			$this->body .= "</table>";
			$this->display_page($db);
			return true;
		}
		else
		{
			//no user id
			$this->error_message = $this->data_missing_error_message;
			return false;
		}
	} //end of function list_communications

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function view_this_communication($db,$communication_id=0)
	{
		$this->page_id = 25;
		$this->get_text($db);

		if ($this->classified_user_id)
		{
			if ($communication_id)
			{
				$this->sql_query = "select * from ".$this->user_communications_table." where message_id = ".$communication_id;
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show = $result->FetchNextObject();
					if ($show->MESSAGE_TO == $this->classified_user_id)
					{
						$this->body .= "<table width=100% cellpadding=2 cellspacing=1 border=0>";
						$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=3&b=reply method=post>\n\t";
						$this->body .="<tr class=section_title><td colspan=2>\n\t\t".urldecode($this->messages[625])."\n\t\t</td>\n\t</tr>\n\t";
						$this->body .="<tr class=user_management_page_title><td colspan=2>\n\t\t".urldecode($this->messages[410])."\n\t\t</td>\n\t</tr>\n\t";
						$this->body .="<tr class=view_this_message_instructions>\n\t\t<td colspan=2>\n\t\t".urldecode($this->messages[411])."\n\t</td>\n\t</tr>\n\t";
						$this->body .="<tr>\n\t\t<td class=field_labels>\n\t\t".urldecode($this->messages[412])."\n\t\t</td>\n\t\t";
						$this->body .="<td class=data_values>\n\t\t";
						if ($show->MESSAGE_FROM)
							$sender = $this->get_user_name($db,$show->MESSAGE_FROM);
						else
							$sender = $show->MESSAGE_FROM_NON_USER;
						$this->body .=$sender;
						$this->body .="\n\t\t</td>\n\t</tr>\n\t";
						$this->body .="<tr>\n\t\t<td class=field_labels>\n\t\t".urldecode($this->messages[413])."\n\t\t</td>\n\t\t";
						$this->body .="<td class=data_values>\n\t\t".date("M j, Y - h:i - l",$show->DATE_SENT)."\n\t\t</td>\n\t</tr>\n\t";
						$this->body .="<tr>\n\t\t<td class=field_labels>\n\t\t".urldecode($this->messages[1186])."\n\t\t</td>\n\t\t";
						$this->body .="<td class=data_values>\n\t\t";
						$ad_title = $this->get_ad_title($db,$show->REGARDING_AD);
						$this->body .=$ad_title."\n\t\t</td>\n\t</tr>\n\t";

						$message = str_replace("\n","<br>",urldecode($show->MESSAGE));
						//$message = str_replace(urldecode($this->messages[249]),"",str_replace("\n\n\r","<br><br>",str_replace("\n\r","<br>",urldecode($show->MESSAGE))));
						//$message = str_replace(urldecode($this->messages[248]),"",$message);
						$this->body .="<tr>\n\t\t<td colspan=2 class=data_values>\n\t\t<font class=field_labels>".urldecode($this->messages[414])." <br>".$message."\n\t\t</td>\n\t</tr>\n\t";
						//if ($show->REPLIED_TO_THIS_MESSAGE)
						//{
						//	$this->sql_query = "select message from ".$this->user_communications_table."
						//		where message_id = ".$show->REPLIED_TO_THIS_MESSAGE;
						//	$message_result = $db->Execute($this->sql_query);
						//	if (!$message_result)
						//	{
						//		$this->error_message = $this->internal_error_message;
						//		return false;
						//	}
						//	elseif ($message_result->RecordCount() == 1)
						//	{
						//		$show_reply_message = $message_result->FetchNextObject();
						//		$this->body .="<tr>\n\t\t<td colspan=2 class=medium_font>\n\t<b>".urldecode($this->messages[412])."</b> \n\t\t</td>\n\t</tr>\n\t";
						//		$this->body .="<tr>\n\t\t<td colspan=2 class=medium_font>\n\t".urldecode($show_reply_message->MESSAGE)."\n\t\t</td>\n\t</tr>\n\t";
						//	}
						//}

						$this->body .="<tr class=please_enter_reply_label>\n\t\t<td colspan=2>\n\t\t".urldecode($this->messages[415])."\n\t\t</td>\n\t</tr>\n\t";
						$this->body .="<tr class=message_body>\n\t\t<td colspan=2>\n\t\t";
						$this->body .="<textarea cols=40 rows=15 name=d[message]>".urldecode($this->messages[254])."</textarea>\n\t\t</td>\n\t</tr>\n\t";
						$this->body .="<tr class=send_reply_button>\n\t\t<td colspan=2>\n\t\t";
						$this->body .="<input type=submit name=z value=\"".urldecode($this->messages[1197])."\" class=send_reply_button>";
						$this->body .="<input type=hidden name=d[replied_to_this_messages] value=".$communication_id.">\n\t\t";
						$this->body .="<input type=hidden name=d[message_to] value=".$show->MESSAGE_FROM.">\n\t\t";
						$this->body .="<input type=hidden name=d[from] value=".$this->classified_user_id.">\n\t\t";
						$this->body .="<input type=hidden name=d[regarding_ad] value=".$show->REGARDING_AD.">\n\t\t";

						//include my email address in reply?

						$this->body .="\n\t\t</td>\n\t</tr>\n\t";
						$this->body .="<tr class=user_management_home_link>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[416])."</a>\n\t</td>\n</tr>\n";
						$this->body .="\n\t</form></table>\n\t";
						$this->display_page($db);
						return true;
					}
					else
					{
						$this->error_message = $this->data_missing_error_message;
					}
				}
				else
				{
					//wrong return count
					$this->error_message = $this->internal_error_message;
				}
			}
			else
			{
				//no communication id
				$this->error_message = $this->data_missing_error_message;
			}
		}

		return false;
	} //end of function view_this_communication

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_this_communication($db,$communication_id)
	{
		if ($this->classified_user_id)
		{
			if ($communication_id)
			{
				$this->sql_query = "delete from ".$this->user_communications_table." where message_id = ".$communication_id." and message_to = ".$this->classified_user_id;
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}
				return true;
			}
			else
			{
				//no communication id
				$this->error_message = $this->data_missing_error_message;
				return false;
			}
		}
		else
			return false;

	} //end of function delete_this_communication

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function communication_success($db)
	{
		$this->page_id = 45;
		$this->get_text($db);
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
		if ($this->classified_user_id)
		{
			$this->body .="<tr class=section_title>\n\t\t<td colspan=2>\n\t\t".urldecode($this->messages[623])."</td>\n</tr>\n";
			$this->body .="<tr class=user_management_page_title>\n\t\t<td colspan=2>\n\t\t".urldecode($this->messages[399])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td class=page_description>\n\t".urldecode($this->messages[407])."\n\t</td>\n</tr>\n";
			$this->body .="<tr class=user_management_home_link>\n\t<td>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4>".urldecode($this->messages[408])."</a>\n\t</td>\n</tr>\n";
		}
		else
		{
			$this->body .="<tr>\n\t<td class=page_description>\n\t".urldecode($this->messages[407])."\n\t</td>\n</tr>\n";

		}
		$this->body .="</table>\n";
		$this->display_page($db);
		return true;
	} //end of function communication_success

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function send_communication_form($db,$to=0,$classified_id=0,$affiliate_id=0)
	{
		$this->page_id = 45;
		$this->get_text($db);
		if ($to)
		{
			$to_data = $this->get_user_data($db,$to);
			if ($to_data)
			{
				$this->body .="<form action=";
				if ($affiliate_id)
					$this->body .= $this->configuration_data['affiliate_url']."?a=3&b=".$to;
				else
					$this->body .= $this->configuration_data['classifieds_url']."?a=3&b=".$to;
				$this->body .= " method=post>\n";
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
				$this->body .="<tr class=section_title>\n\t\t<td colspan=2>\n\t\t".urldecode($this->messages[623])."</td>\n</tr>\n";
				$this->body .="<tr class=user_management_page_title>\n\t\t<td colspan=2>\n\t\t".urldecode($this->messages[399])."</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t\t<td colspan=2>\n\t\t".urldecode($this->messages[400])."</td>\n</tr>\n";

				//message to
				$this->body .="<tr>\n\t<td align=right width=50% class=field_labels>\n\t".urldecode($this->messages[1190])."</td>\n\t";
				$this->body .="<td width=50% class=data_values>\n\t";
				if ($to_data->COMMUNICATION_TYPE == 1)
					$this->body .=$to_data->EMAIL;
				else
					$this->body .=$to_data->USERNAME;
				if ($this->classified_user_id == $to)
					$this->body .=" "."( ".urldecode($this->messages[1195])." )\n\t";
				$this->body .="</td>\n</tr>\n";

				//message from
				$this->body .="<tr>\n\t<td align=right class=field_labels>\n\t".urldecode($this->messages[401])."</td>\n\t";
				if ($this->classified_user_id)
				{
					$from_data = $this->get_user_data($db,$this->classified_user_id);
					if ($from_data)
					{
						if ($from_data->COMMUNICATION_TYPE == 1)
							$from = $from_data->EMAIL;
						else
							$from = $from_data->USERNAME;
						$this->body .="<td class=data_values>\n\t".$from."\n\t";
						$this->body .="<input type=hidden name=d[from] value=".$this->classified_user_id.">\n\t";
						$this->body .="<input type=hidden name=d[from_user] value=".$this->classified_user_id.">\n\t";
						$this->body .="</td>\n</tr>\n";
					}
				}
				else
					$this->body .="<td class=data_values>\n\t<input type=text name=d[from] class=data_values></td>\n</tr>\n";

				if ($classified_id)
				{
					$subject = $this->get_ad_title($db,$classified_id);
				}

				//subject
				$this->body .="<tr>\n\t<td align=right class=field_labels>\n\t".urldecode($this->messages[403])."</td>\n\t";
				if ($classified_id)
					$this->body .="<td class=data_values>\n\t<input type=text name=d[subject] value=\"".urldecode($subject)."-".$classified_id."\"  class=data_values>\n\t";
				else
					$this->body .="<td class=data_values>\n\t<input type=text name=d[subject] value=\"".urldecode($subject)."\"  class=data_values>\n\t";
				if ($classified_id)
				{
					$this->body .="<input type=hidden name=d[classified_id] value=".$classified_id.">\n\t";
				}
				$this->body .="</td>\n</tr>\n";

				//message
				$this->body .="<tr class=message_header_row>\n\t<td colspan=2 align=center>\n\t".urldecode($this->messages[405])."<br>";
				$this->body .="<textarea cols=50 rows=15 name=d[message] class=data_values align=center>".urldecode($this->messages[404])."</textarea>\n\t\t</td>\n\t</tr>\n\t";

				$this->body .="<tr class=send_reply_button>\n\t<td colspan=2>\n\t<input class=send_reply_button type=submit name=z value=\"".urldecode($this->messages[1333])."\">
					\n\t</td>\n</tr>\n";
				$this->body .="<tr class=user_management_home_link>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[287])."</a>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n</form>\n";
				$this->display_page($db);
				return true;
			}
			else
			{
				$this->error_message = $this->data_missing_error_message;
				return false;
			}
		}
		else
		{
			//no communication info
			$this->error_message = $this->data_missing_error_message;
			return false;
		}
	} //end of function send_communication_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function communications_configuration($db)
	{
		$this->page_id = 26;
		$this->get_text($db);
		//display the form for communication configuration
		if ($this->classified_user_id)
		{
			$this->sql_query = "select communication_type from ".$this->userdata_table." where id = ".$this->classified_user_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->debug_message = "no configuration data returned";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchNextObject();
				$this->body .= "<table cellspacing=1 cellpadding=2 border=0 width=100%>";
				$this->body .="<tr class=user_management_page_title><td colspan=5>\n\t\t".urldecode($this->messages[622])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=communication_configuration_title>\n\t<td colspan=5>\n\t".urldecode($this->messages[363]);
				$this->body .=$this->display_help_link(1400);
				$this->body .="\n\t\t</td>\n\t</tr>\n\t";

				$this->body .="<tr><td><form action=".$this->configuration_data['classifieds_file_name']."?a=4&b=7&z=1 method=post>\n";
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";

				$this->body .="<tr class=page_instructions>\n\t<td colspan=2>\n\t".urldecode($this->messages[364])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=public_communication_row>\n\t\t<td align=right valign=top>\n\t\t<input type=radio name=c[communication_type] value=1 ";
				if ($show->COMMUNICATION_TYPE == 1)
					$this->body .=" checked ";
				$this->body .="></td>\n\t\t<td>\n\t\t".urldecode($this->messages[365])."<br>".urldecode($this->messages[366])."\n\t\t</td>\n\t</tr>\n\t";
				//$this->body .="<tr class=semiprivate_communication_row>\n\t\t<td align=right valign=top>\n\t\t<input type=radio name=c[communication_type] value=2 ";
				//if ($show->COMMUNICATION_TYPE == 2)
				//	$this->body .=" checked ";
				//$this->body .="></td>\n\t\t<td>\n\t\t".urldecode($this->messages[367])."<br>".urldecode($this->messages[368])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=completely_private_communication_row>\n\t\t<td align=right valign=top>\n\t\t<input type=radio name=c[communication_type] value=3 ";
				if ($show->COMMUNICATION_TYPE == 3)
					$this->body .=" checked ";
				$this->body .="></td>\n\t\t<td>\n\t\t".urldecode($this->messages[369])."<br>".urldecode($this->messages[370])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="</td>\n\t</tr>\n\t";
				$this->body .="<tr class=save_communication_button>\n\t<td colspan=2>\n\t<input type=submit name=submit value=\"".urldecode($this->messages[372])."\" class=save_communication_button>\n\t</td>\n</tr>\n";
				$this->body .="<tr class=user_management_home_link>\n\t<td colspan=2>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4>".urldecode($this->messages[371])."</a>\n\t</td>\n</tr>\n";
				$this->body .="</table>\n\t";
				$this->body .="</form>\n\t";
				$this->body .= "</td></tr></table>";
			}
			else
			{
				//there are no configuration for this user
				$this->error_message = $this->internal_error_message;
				return false;
			}
			$this->display_page($db);
			return true;
		}
		else
		{
			//no user id
			$this->error_message = $this->data_missing_error_message;
			return false;
		}
	} //end of function communications_configuration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_communication_configuration($db,$configuration_information=0)
	{
		if ($this->classified_user_id)
		{
			//update the communication configuration
			if ($configuration_information)
			{
				$this->sql_query = "update ".$this->userdata_table." set communication_type = ".
					$configuration_information["communication_type"]." where id = ".$this->classified_user_id;
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}
				return true;
			}
			else
			{
				//no communication information
				$this->error_message = $this->data_missing_error_message;
				return false;
			}
		}
		else
			return false;

	} //end of function update_communication_configuration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class
?>
