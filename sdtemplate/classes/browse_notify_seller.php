<? //browse_notify_seller.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Notify_seller extends Site {
	var $subcategory_array = array();
	var $notify_data = array();

//########################################################################

	function Notify_seller($db,$classified_user_id,$language_id,$category_id=0,$page=0,$classified_id=0,$affiliate=0,$product_configuration=0)
	{
		$this->Site($db,1,$language_id,$classified_user_id,$product_configuration);
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
		
		$this->get_ad_configuration($db);
		if ($page)
			$this->page_result = $page;
		else
			$this->page_result = 1;
			
		if (($affiliate) && (is_numeric($affiliate)))
		{
			//check that has affiliate privileges
			$sql_query = "select * from geodesic_user_groups_price_plans where id = ".$affiliate;
			if ($debug) echo $sql_query." is the query in no cookie<br>\n";
			$aff_group_result = $db->Execute($sql_query);
			if (!$aff_group_result)
			{
				if ($debug) echo $sql_query." in no cookie<br>\n";
				return false;
			}
			elseif ($aff_group_result->RecordCount() == 1)
			{
				$show_group = $aff_group_result->FetchNextObject();
				$sql_query = "select * from geodesic_groups where group_id = ".$show_group->GROUP_ID;
				if ($debug) echo $sql_query." is the query in no cookie<br>\n";
				$group_result = $db->Execute($sql_query);
				if (!$group_result)
				{
					if ($debug) echo $sql_query." in no cookie<br>\n";
					return false;
				}
				elseif ($group_result->RecordCount() == 1)
				{
					$show_affiliate = $group_result->FetchNextObject();
					if ($show_affiliate->AFFILIATE)
					{
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
	} //end of function Notify_seller

//###########################################################

	function send_a_message_to_seller_form($db,$classified_id=0)
	{
		if ($classified_id)
		{
			if ((($this->classified_user_id) && ($this->configuration_data['seller_contact'])) || (!$this->configuration_data['seller_contact']))
			{
				$this->page_id = 6;
				$this->get_text($db);
				$this->sql_query = "select seller,title from ".$this->classifieds_table." where id =".$classified_id;
				$result = $db->Execute($this->sql_query);

				if (!$result)
				{
					//$this->body .=$this->sql_query." is the state query<br>\n";
					$this->error_message = $this->messages[80];
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show = $result->FetchNextObject();
				}
				else
				{
					$this->error_message = $this->messages[80];
					return false;
				}
				if ($this->classified_user_id)
				{
					$current_user_data = $this->get_user_data($db,$this->classified_user_id);
				}

				$user_data = $this->get_user_data($db,$show->SELLER);

				if ($user_data)
				{
					$this->body .="<form action=";
					if ($this->affiliate_id)
						$this->body .= $this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=13&b=".$classified_id;
					else
						$this->body .= $this->configuration_data['classifieds_url']."?a=13&b=".$classified_id;
					$this->body .=" method=post>\n\t";
				}
				$this->body .="<table width=100% border=0 cellpadding=2 cellspacing=1>\n";
				$this->body .="<TR class=section_title>\n\t<TD colspan=2>\n\t".urldecode($this->messages[605]);
				$this->body .="<TR class=send_seller_message_page_title>\n\t<TD colspan=2>\n\t".urldecode($this->messages[53]);
				$this->body .="\n\t</td>\n</tr>\n";

				$this->body .="<TR class=send_seller_message_instructions>\n\t<TD colspan=2>\n\t".urldecode($this->messages[54])."\n\t</td>\n</tr>\n";

				if ($user_data)
				{
					//sellers username
					$this->body .="<TR>\n\t\t<TD ALIGN=right class=send_seller_message_field_labels>\n\t\t".urldecode($this->messages[55])."\n\t\t</TD>\n\t\t";
					$this->body .="<TD class=send_seller_message_input_box>".$user_data->USERNAME."\n\t\t</TD>\n\t</TR>\n\t";

					// title as subject
					$this->body .="<TR><TD ALIGN=right class=send_seller_message_field_labels>".urldecode($this->messages[56])."\n\t\t</TD>\n\t\t";
					$this->body .="<TD class=send_seller_message_input_box>".stripslashes(urldecode($show->TITLE))." - #".$classified_id."\n\t\t</TD>\n\t</TR>\n\t";

					// your email
					$this->body .="<TR>\n\t\t<TD ALIGN=right class=send_seller_message_field_labels>".urldecode($this->messages[57])."\n\t\t</TD>\n\t\t";
					if ($this->classified_user_id)
					{
						$this->body .="<TD class=send_seller_message_field_labels>\n\t\t<INPUT TYPE=TEXT NAME=c[senders_email] SIZE=40 VALUE=\"".urldecode($current_user_data->EMAIL)."\" class=send_seller_message_input_box>\n\t\t</TD>\n\t</TR>\n\t";
					}
					else
					{
						$this->body .="<TD class=send_seller_message_input_box>\n\t\t<INPUT TYPE=TEXT NAME=c[senders_email] SIZE=40 class=send_seller_message_input_box>\n\t\t</TD>\n\t</TR>\n\t";
					}

					//contact name
					$this->body .="<TR><TD ALIGN=right class=send_seller_message_field_labels>".urldecode($this->messages[1366])."\n\t\t</TD>\n\t\t";
					$this->body .="<TD class=send_seller_message_input_box><INPUT TYPE=TEXT NAME=c[senders_name] SIZE=40 class=send_seller_message_input_box ";
					if ($this->classified_user_id)
						$this->body .= " value=\"".urldecode($current_user_data->FIRSTNAME)."\" ";
					$this->body .= ">\n\t\t</TD>\n\t</TR>\n\t";

					//phone to contact
					$this->body .="<TR><TD ALIGN=right class=send_seller_message_field_labels>".urldecode($this->messages[1512])."\n\t\t</TD>\n\t\t";
					$this->body .="<TD class=send_seller_message_input_box><INPUT TYPE=TEXT NAME=c[senders_phone] SIZE=40 class=send_seller_message_input_box ";
					if ($this->classified_user_id)
						$this->body .= " value=\"".urldecode($current_user_data->PHONE)."\" ";
					$this->body .= ">\n\t\t</TD>\n\t</TR>\n\t";

					//comment
					$this->body .="<TR>\n\t\t<TD ALIGN=right VALIGN=TOP class=send_seller_message_field_labels>\n\t\t".urldecode($this->messages[58])."\n\t\t</TD>\n\t\t";
					$this->body .="<TD class=send_seller_message_input_box>\n\t\t<TEXTAREA NAME=c[senders_comments] COLS=40 ROWS=6 class=send_seller_message_input_box></TEXTAREA>\n\t\t</td>\n\t</tr>\n\t";

					//submit button
					$this->body .="<tr>\n\t\t<td align=center colspan=2>\n\t\t";
					$this->body .="<INPUT TYPE=submit NAME=submit value=\"".urldecode($this->messages[60])."\" class=send_seller_message_input_box>";
					$this->body .="<INPUT TYPE=reset NAME=reset class=send_seller_message_input_box>\n\t\t</TD>\n\t</TR>\n\t";
					$this->body .="<TR class=send_seller_message_link_text>\n\t<TD colspan=2><a href=";
					if ($this->affiliate_id)
						$this->body .= $this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=2&b=".$classified_id;
					else 
						$this->body .= $this->configuration_data['classifieds_url']."?a=2&b=".$classified_id;
					$this->body .= " class=send_seller_message_link_text>".urldecode($this->messages[1187])."</a>\n\t</td>\n</tr>\n</TABLE>\n";
					$this->body .="</FORM>";
				}
				else
				{
					//display the contact information within the ad
				}

				$this->error_found = 0;
				$this->display_page($db);
				return true;
			}
			else
			{
				include_once("authenticate_class.php");
				$auth = new Auth($db,$this->language_id);
				$auth->login_form($db,0,0,"a*is*".$_REQUEST["a"]."&b*is*".$_REQUEST["b"],1);
			}
		}
		else
		{
			return false;
		}

	} //end of send_a_message_to_seller_form

//########################################################################3


//###########################################################

    function send_a_message_to_seller_form_Make_Offer($db,$classified_id=0)
    {
        if ($classified_id)
        {
            if ((($this->classified_user_id) && ($this->configuration_data['seller_contact'])) || (!$this->configuration_data['seller_contact']))
            {
                $this->page_id = 6;
                $this->get_text($db);
                $this->sql_query = "select seller,title from ".$this->classifieds_table." where id =".$classified_id;
                $result = $db->Execute($this->sql_query);

                if (!$result)
                {
                    //$this->body .=$this->sql_query." is the state query<br>\n";
                    $this->error_message = $this->messages[80];
                    return false;
                }
                elseif ($result->RecordCount() == 1)
                {
                    $show = $result->FetchNextObject();
                }
                else
                {
                    $this->error_message = $this->messages[80];
                    return false;
                }
                if ($this->classified_user_id)
                {
                    $current_user_data = $this->get_user_data($db,$this->classified_user_id);
                }

                $user_data = $this->get_user_data($db,$show->SELLER);

                if ($user_data)
                {
                    $this->body .="<form action=";
                    if ($this->affiliate_id)
                        $this->body .= $this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=1313&b=".$classified_id;
                    else
                        $this->body .= $this->configuration_data['classifieds_url']."?a=1313&b=".$classified_id;
                    $this->body .=" method=post>\n\t";
                }
                $this->body .="<table width=100% border=0 cellpadding=2 cellspacing=1>\n";
                $this->body .="<TR class=section_title>\n\t<TD colspan=2>\n\t".urldecode($this->messages[605]);
                $this->body .="<TR class=send_seller_message_page_title>\n\t<TD colspan=2>\n\t".urldecode("Contact Seller. Offer to purchase");
                $this->body .="\n\t</td>\n</tr>\n";

                $this->body .="<TR class=send_seller_message_instructions>\n\t<TD colspan=2>\n\t".urldecode("Would you like to offer the seller of this item a your bid price? Simply fill in the form below with your return email address and your offer and we will forward your message to the seller.")."\n\t</td>\n</tr>\n";

                if ($user_data)
                {
                    //sellers username
                    $this->body .="<TR>\n\t\t<TD ALIGN=right class=send_seller_message_field_labels>\n\t\t".urldecode($this->messages[55])."\n\t\t</TD>\n\t\t";
                    $this->body .="<TD class=send_seller_message_input_box>".$user_data->USERNAME."\n\t\t</TD>\n\t</TR>\n\t";

                    // title as subject
                    $this->body .="<TR><TD ALIGN=right class=send_seller_message_field_labels>".urldecode($this->messages[56])."\n\t\t</TD>\n\t\t";
                    $this->body .="<TD class=send_seller_message_input_box>".stripslashes(urldecode($show->TITLE))." - #".$classified_id."\n\t\t</TD>\n\t</TR>\n\t";

                    // your email
                    $this->body .="<TR>\n\t\t<TD ALIGN=right class=send_seller_message_field_labels>".urldecode($this->messages[57])."\n\t\t</TD>\n\t\t";
                    if ($this->classified_user_id)
                    {
                        $this->body .="<TD class=send_seller_message_field_labels>\n\t\t<INPUT TYPE=TEXT NAME=c[senders_email] SIZE=40 VALUE=\"".urldecode($current_user_data->EMAIL)."\" class=send_seller_message_input_box>\n\t\t</TD>\n\t</TR>\n\t";
                    }
                    else
                    {
                        $this->body .="<TD class=send_seller_message_input_box>\n\t\t<INPUT TYPE=TEXT NAME=c[senders_email] SIZE=40 class=send_seller_message_input_box>\n\t\t</TD>\n\t</TR>\n\t";
                    }

                    //contact name
                    $this->body .="<TR><TD ALIGN=right class=send_seller_message_field_labels>".urldecode($this->messages[1366])."\n\t\t</TD>\n\t\t";
                    $this->body .="<TD class=send_seller_message_input_box><INPUT TYPE=TEXT NAME=c[senders_name] SIZE=40 class=send_seller_message_input_box ";
                    if ($this->classified_user_id)
                        $this->body .= " value=\"".urldecode($current_user_data->FIRSTNAME)."\" ";
                    $this->body .= ">\n\t\t</TD>\n\t</TR>\n\t";

                    //phone to contact
                    $this->body .="<TR><TD ALIGN=right class=send_seller_message_field_labels>".urldecode($this->messages[1512])."\n\t\t</TD>\n\t\t";
                    $this->body .="<TD class=send_seller_message_input_box><INPUT TYPE=TEXT NAME=c[senders_phone] SIZE=40 class=send_seller_message_input_box ";
                    if ($this->classified_user_id)
                        $this->body .= " value=\"".urldecode($current_user_data->PHONE)."\" ";
                    $this->body .= ">\n\t\t</TD>\n\t</TR>\n\t";

                    //comment
                    $this->body .="<TR>\n\t\t<TD ALIGN=right VALIGN=TOP class=send_seller_message_field_labels>\n\t\t".urldecode("Bid Price")."\n\t\t</TD>\n\t\t";
                    $this->body .="<TD class=send_seller_message_input_box>\n\t\t<INPUT NAME=c[senders_comments] SIZE=40 class=send_seller_message_input_box>\n\t\t</td>\n\t</tr>\n\t";

                    //submit button
                    $this->body .="<tr>\n\t\t<td align=center colspan=2>\n\t\t";
                    $this->body .="<INPUT TYPE=submit NAME=submit value=\"".urldecode($this->messages[60])."\" class=send_seller_message_input_box>";
                    $this->body .="<INPUT TYPE=reset NAME=reset class=send_seller_message_input_box>\n\t\t</TD>\n\t</TR>\n\t";
                    $this->body .="<TR class=send_seller_message_link_text>\n\t<TD colspan=2><a href=";
                    if ($this->affiliate_id)
                        $this->body .= $this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=2&b=".$classified_id;
                    else
                        $this->body .= $this->configuration_data['classifieds_url']."?a=2&b=".$classified_id;
                    $this->body .= " class=send_seller_message_link_text>".urldecode($this->messages[1187])."</a>\n\t</td>\n</tr>\n</TABLE>\n";
                    $this->body .="</FORM>";
                }
                else
                {
                    //display the contact information within the ad
                }

                $this->error_found = 0;
                $this->display_page($db);
                return true;
            }
            else
            {
                include_once("authenticate_class.php");
                $auth = new Auth($db,$this->language_id);
                $auth->login_form($db,0,0,"a*is*".$_REQUEST["a"]."&b*is*".$_REQUEST["b"],1);
            }
        }
        else
        {
            return false;
        }

    } //end of send_a_message_to_seller_form

//########################################################################3


    function notify_seller_Make_Offer($db,$classified_id=0,$info=0)
    {
        if (($classified_id) && ($info))
        {
            $this->page_id = 7;
            $this->get_text($db);
            if ((strlen(trim($info["senders_comments"])) > 0) && (strlen(trim($info["senders_email"])) > 0))
            {
                $this->sql_query = "select seller,title,responded,email from ".$this->classifieds_table." where id =".$classified_id;
                $result = $db->Execute($this->sql_query);
                //echo $this->sql_query." is the state query<br>\n";
                if (!$result)
                {
                    //$this->body .=$this->sql_query." is the state query<br>\n";
                    $this->error_message = $this->messages[80];
                    return false;
                }
                elseif ($result->RecordCount() == 1)
                {
                    $show = $result->FetchNextObject();
                }
                else
                {
                    $this->error_message = $this->messages[80];
                    return false;
                }

                $this->sql_query = "update ".$this->classifieds_table." set
                    responded = ".($show->RESPONDED + 1)."
                    where id = ".$classified_id;
                $update_result = $db->Execute($this->sql_query);
                //echo $this->sql_query." is the state query<br>\n";
                if (!$update_result)
                {
                    //echo $this->sql_query." is the state query<br>\n";
                    $this->error_message = $this->messages[832];
                    return false;
                }

                $seller = $this->get_user_data($db,$show->SELLER);
                if (strlen(trim($show->EMAIL)) == 0)
                {
                    $mailto = urldecode($seller->EMAIL);
                }
                else
                {
                    $mailto = urldecode($show->EMAIL);
                }

                $message["subject"] = stripslashes(urldecode("Offer to purchase. ")).stripslashes(urldecode($show->TITLE));


                $message["message"] .= stripslashes(urldecode($this->messages[1189])).stripslashes(urldecode($seller->USERNAME))."\n\n";

                if ($this->affiliate_id)
                    $message["message"] .= stripslashes(urldecode($this->messages[1332]))."\n".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=2&b=".$classified_id."\n\n";
                else
                    $message["message"] .= stripslashes(urldecode($this->messages[1332]))."\n".$this->configuration_data['classifieds_url']."?a=2&b=".$classified_id."\n\n";

                if (strlen(trim($info["senders_name"])) > 0)
                    $message["message"] .= stripslashes(urldecode($this->messages[1513].$info["senders_name"]))."\n";

                if (strlen(trim($info["senders_phone"])) > 0)
                    $message["message"] .= stripslashes(urldecode($this->messages[1514].$info["senders_phone"]))."\n\n";

                $message["message"] .= "\r\nFor ".stripslashes(urldecode($show->TITLE ." #".$classified_id." would you accept $".$info["senders_comments"]."\r\n==================\n\n"));

                //if (strlen(trim($info["senders_comments"])) > 0)
                    //$message["message"] .= stripslashes(urldecode($this->messages[61]."\n\n".$info["senders_comments"]))."\n\n";

                if ($this->configuration_data['email_header_break'])
                    $separator = "\n";
                else
                    $separator = "\r\n";

                //$from = "From: ".urldecode($info["senders_email"])."\r\n Reply-to: ".urldecode($info["senders_email"])."\r\n";
                $from = "From: ".urldecode($info["senders_email"]).$separator."Reply-to: ".urldecode($info["senders_email"]).$separator;

                //send an email
                $additional = "-f".urldecode($info["senders_email"]);

                $ip = $_SERVER['REMOTE_ADDR'];
                $host = @gethostbyaddr($ip);
                //$host = preg_replace("/^[^.]+./", "*.", $host);
                $message["message"] .= "\n".$_SERVER["REMOTE_ADDR"]." : ".$host;

                //$mailto = 'burlakov@bcs-it.com';

                if ($this->configuration_data['email_configuration_type'] == 1)
                    $message["message"] = str_replace("\n\n","\n",$message["message"]);
                if ($this->configuration_data['email_configuration'] == 1)
                    mail($mailto, $message["subject"], $message["message"], $from ,$additional);
                elseif ($this->configuration_data['email_configuration'] == 2)
                    mail($mailto, $message["subject"], $message["message"], $from);
                else
                    mail($mailto, $message["subject"], $message["message"]);

                if (strlen(trim($this->configuration_data['admin_email_bcc'])) > 0)
                {
                    if ($this->configuration_data['email_configuration'] == 1)
                        mail($this->configuration_data['admin_email_bcc'], $message["subject"], $message["message"], $from,$additional);
                    elseif ($this->configuration_data['email_configuration'] == 2)
                        mail($this->configuration_data['admin_email_bcc'], $message["subject"], $message["message"], $from);
                    else
                        mail($this->configuration_data['admin_email_bcc'], $message["subject"], $message["message"]);
                }

                if ($this->classified_user_id)
                {
                    $this->sql_query = "insert into ".$this->user_communications_table."
                        (message_to,message_from,regarding_ad,date_sent,message)
                        values
                        (".$show->SELLER.",".$this->classified_user_id.",".$classified_id.",".$this->shifted_time($db).",\"".urlencode($message["message"])."\")";
                }
                else
                {
                    $this->sql_query = "insert into ".$this->user_communications_table."
                        (message_to,message_from_non_user,regarding_ad,date_sent,message)
                        values
                        (".$show->SELLER.",\"".$info["senders_email"]."\",".$classified_id.",".$this->shifted_time($db).",\"".urlencode($message["message"])."\")";
                }
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
            return false;
        }
    } //end of function notify_friend

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%





	function notify_seller_($db,$classified_id=0,$info=0)
	{
		if (($classified_id) && ($info))
		{
			$this->page_id = 7;
			$this->get_text($db);
			if ((strlen(trim($info["senders_comments"])) > 0) && (strlen(trim($info["senders_email"])) > 0))
			{
				$this->sql_query = "select seller,title,responded,email from ".$this->classifieds_table." where id =".$classified_id;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is the state query<br>\n";
				if (!$result)
				{
					//$this->body .=$this->sql_query." is the state query<br>\n";
					$this->error_message = $this->messages[80];
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show = $result->FetchNextObject();
				}
				else
				{
					$this->error_message = $this->messages[80];
					return false;
				}

				$this->sql_query = "update ".$this->classifieds_table." set
					responded = ".($show->RESPONDED + 1)."
					where id = ".$classified_id;
				$update_result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is the state query<br>\n";
				if (!$update_result)
				{
					//echo $this->sql_query." is the state query<br>\n";
					$this->error_message = $this->messages[832];
					return false;
				}

				$seller = $this->get_user_data($db,$show->SELLER);
				if (strlen(trim($show->EMAIL)) == 0)
				{
					$mailto = urldecode($seller->EMAIL);
				}
				else
				{
					$mailto = urldecode($show->EMAIL);
				}

				$message["subject"] = stripslashes(urldecode($this->messages[727])).stripslashes(urldecode($show->TITLE));


				$message["message"] .= stripslashes(urldecode($this->messages[1189])).stripslashes(urldecode($seller->USERNAME))."\n\n";

				if ($this->affiliate_id)
					$message["message"] .= stripslashes(urldecode($this->messages[1332]))."\n".$this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=2&b=".$classified_id."\n\n";
				else
					$message["message"] .= stripslashes(urldecode($this->messages[1332]))."\n".$this->configuration_data['classifieds_url']."?a=2&b=".$classified_id."\n\n";

				if (strlen(trim($info["senders_name"])) > 0)
					$message["message"] .= stripslashes(urldecode($this->messages[1513].$info["senders_name"]))."\n";

				if (strlen(trim($info["senders_phone"])) > 0)
					$message["message"] .= stripslashes(urldecode($this->messages[1514].$info["senders_phone"]))."\n\n";

				if (strlen(trim($info["senders_comments"])) > 0)
					$message["message"] .= stripslashes(urldecode($this->messages[61]."\n\n".$info["senders_comments"]))."\n\n";

				if ($this->configuration_data['email_header_break'])
					$separator = "\n";
				else
					$separator = "\r\n";

				//$from = "From: ".urldecode($info["senders_email"])."\r\n Reply-to: ".urldecode($info["senders_email"])."\r\n";
				$from = "From: ".urldecode($info["senders_email"]).$separator."Reply-to: ".urldecode($info["senders_email"]).$separator;

				//send an email
				$additional = "-f".urldecode($info["senders_email"]);

				$ip = $_SERVER['REMOTE_ADDR'];
				$host = @gethostbyaddr($ip);
				//$host = preg_replace("/^[^.]+./", "*.", $host);
				$message["message"] .= "\n".$_SERVER["REMOTE_ADDR"]." : ".$host;

				if ($this->configuration_data['email_configuration_type'] == 1)
					$message["message"] = str_replace("\n\n","\n",$message["message"]);
				if ($this->configuration_data['email_configuration'] == 1)
					mail($mailto, $message["subject"], $message["message"], $from ,$additional);
				elseif ($this->configuration_data['email_configuration'] == 2)
					mail($mailto, $message["subject"], $message["message"], $from);
				else
					mail($mailto, $message["subject"], $message["message"]);

				if (strlen(trim($this->configuration_data['admin_email_bcc'])) > 0)
				{
					if ($this->configuration_data['email_configuration'] == 1)
						mail($this->configuration_data['admin_email_bcc'], $message["subject"], $message["message"], $from,$additional);
					elseif ($this->configuration_data['email_configuration'] == 2)
						mail($this->configuration_data['admin_email_bcc'], $message["subject"], $message["message"], $from);
					else
						mail($this->configuration_data['admin_email_bcc'], $message["subject"], $message["message"]);
				}

				if ($this->classified_user_id)
				{
					$this->sql_query = "insert into ".$this->user_communications_table."
						(message_to,message_from,regarding_ad,date_sent,message)
						values
						(".$show->SELLER.",".$this->classified_user_id.",".$classified_id.",".$this->shifted_time($db).",\"".urlencode($message["message"])."\")";
				}
				else
				{
					$this->sql_query = "insert into ".$this->user_communications_table."
						(message_to,message_from_non_user,regarding_ad,date_sent,message)
						values
						(".$show->SELLER.",\"".$info["senders_email"]."\",".$classified_id.",".$this->shifted_time($db).",\"".urlencode($message["message"])."\")";
				}
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
			return false;
		}
	} //end of function notify_friend

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function notify_seller_success($db,$classified_id)
	{
		$this->page_id = 6;
		$this->get_text($db);
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<TR class=section_title>\n\t<TD colspan=2>\n\t".urldecode($this->messages[605]);
		$this->body .="<TR class=send_seller_message_page_title>\n\t<TD colspan=2>\n\t".urldecode($this->messages[53]);
		$this->body .="\n\t</td>\n</tr>\n";
		$this->body .="<tr class=send_seller_message_instructions>\n\t<td>\n\t".urldecode($this->messages[59])."\n\t</td>\n</tr>\n";
		$this->body .="<tr class=send_seller_message_link_text>\n\t<td>\n\t<a href=";
		if ($this->affiliate_id)
			$this->body .= $this->configuration_data['affiliate_url']."?aff=".$this->affiliate_id."&a=2&b=".$classified_id;
		else
			$this->body .= $this->configuration_data['classifieds_url']."?a=2&b=".$classified_id;
		$this->body .= " class=send_seller_message_link_text>".urldecode($this->messages[1187])."</a>\n\t</td>\n</tr>\n";
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

} // end class Notify_seller

?>
