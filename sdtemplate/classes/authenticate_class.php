<? //authenticate_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Auth extends Site {

	   var $error;
	   var $login_cookie_time;
	   var $classified_user_id;
	   var $username;
	   var $classified_level;
	   var $auth_messages;
	   var $error_messages;
	   var $error_found;

	   var $messages = array();

	   var $notify_data;

	   var $debug_auth = 0;


//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Auth ($db,$language_id,$product_configuration=0)
	{
		//$this->body .=  "hello from auth<br>\n";
		//if (!$db)
		//	$this->body .=  "no connection in Auth top<br>\n";
		//constuctor
		//$this->body .=  $this->classified_user_id." is classified user id in auth<br>\n";
		$this->Site($db,9,$language_id,$this->classified_user_id,$product_configuration);

	} //end of function Auth

//#############################################################################

	function login_form($db,$username=0,$password=0,$encode=0,$must_login=0)
	{
		//echo $must_login."is must login<br>";
		$this->page_id = 39;
		$this->get_text($db);
		$this->body .=  "<form action=".$this->configuration_data['classifieds_file_name']."?a=10 method=post>\n";
		if ($encode)
			$this->body .=  "<input type=hidden name=c value=\"".urlencode($encode)."\">\n";
		$this->body .=  "<table cellpadding=3 cellspacing=1 border=0 width=100%>\n";
		$this->body .=  "<tr class=login_form_title>\n\t<td colspan=3 >\n\t".urldecode($this->messages[332])."\n\t</td>\n</tr>\n";
		$this->body .=  "<tr class=page_description>\n\t<td colspan=3 >\n\t".urldecode($this->messages[333])."\n\t</td>\n</tr>\n";
		if ($this->debug_auth)
			echo $this->auth_messages["login"]." login error message<BR>\n";
		if ($this->auth_messages["login"])
			$this->body .=  "<tr class=error_message>\n\t<td colspan=3>\n\t".urldecode($this->auth_messages["login"])."\n\t</td>\n</tr>\n";
		if ($must_login == 1)
			$this->body .= "<tr class=error_message>\n\t<td colspan=3>\n\t".urldecode($this->messages[2343])."\n\t</td>\n</tr>\n";
		if ($must_login == 2)
			$this->body .= "<tr class=error_message>\n\t<td colspan=3>\n\t".urldecode($this->messages[2344])."\n\t</td>\n</tr>\n";
		if ($must_login == 3)
			$this->body .= "<tr class=error_message>\n\t<td colspan=3>\n\t".urldecode($this->messages[3266])."\n\t</td>\n</tr>\n";
		$this->body .=  "<tr><td class=field_labels width=50%>".urldecode($this->messages[334])."</td>";
		$this->body .=  "<td class=field_data>\n\t<input type=text name=b[username] size=12 maxlength=12 ";
		if ($username)
			$this->body .=  "value=\"".$username."\"";
		$this->body .=  "  class=field_data> ";
		if ($this->error_messages["username"])
			$this->body .=  "<font class=error_message>".urldecode($this->error_messages["username"])."</font>";
		$this->body .=  "</td></tr>\n";
		$this->body .=  "<tr><td  class=field_labels width=50%>".urldecode($this->messages[335])."</td>";
		$this->body .=  "<td class=field_data><input type=password name=b[password] size=12 maxlength=12 class=field_data> ";
		if ($this->error_messages["password"])
			$this->body .=  "<font class=error_message>".urldecode($this->error_messages["password"])."</font>";
		$this->body .=  "</td></tr>\n";
		$this->body .=  "<tr class=login_button>\n\t<td colspan=3>\n\t<input type=submit name=submit value=\"".urldecode($this->messages[336])."\" class=login_button>\n\t</td>\n</tr>\n";
		$this->body .=  "<tr class=login_lost_password_link>\n\t<td colspan=3>\n\t<a href=".$this->configuration_data['classifieds_url']."?a=18 class=login_lost_password_link>".urldecode($this->messages[1316])."</a>\n\t</td>\n</tr>\n";
		$this->body .=  "<tr class=login_register_link>\n\t<td colspan=3>\n\t<a href=";
		if ($this->configuration_data['use_ssl_in_registration'])
			$this->body .= $this->configuration_data['registration_ssl_url'];
		else
			$this->body .= $this->configuration_data['registration_url'];
		$this->body .= " class=login_register_link>".urldecode($this->messages[1317])."</a></td></tr>\n";
		$this->body .=  "</table>\n";
		$this->body .=  "</form>\n";

		$this->auth_messages["login"] = 0;
		$this->error_messages["username"] = 0;
		$this->error_messages["password"] = 0;

		$this->display_page($db);
		return true;

	} //end of function login_form

//#############################################################################

	function login ($db,$username,$password,$session=0)
	{
		if ($this->debug_auth)
			echo "hello from login function top<br>\n";
		$this->page_id = 39;
		$this->get_text($db);
		if (!$session)
		{
			return false;
		}
		$this->error_found = 0;
		$this->auth_messages["login"] = 0;
		$this->error_messages["username"] =0;
		$this->error_messages["password"] = 0;

		$this->expire_subscriptions($db);

		if (strlen(trim($username)) == 0)
		{
			$this->error_messages["username"] = $this->messages[337];
			$this->error_found++;
		}

		if (strlen(trim($password)) == 0 )
		{
                        $this->error_messages["password"] = $this->messages[338];
                        $this->error_found++;
		}

		if ($this->error_found > 0)
		{
			$this->auth_messages["login"] = $this->messages[341];
			return false;
		}

		//if (!eregi("^[[:alnum:]_-.*@*]+$", $username))
		//{
              //          $this->error_messages["username"] = $this->messages[130];
              //          $this->error_found++;
		//}

		if (!eregi("^[[:alnum:]_-]+$", $password))
		{
                        $this->error_messages["password"] = $this->messages[340];
                        $this->error_found++;
		}

		if ($this->debug_auth)
			echo $this->error_found." is the error_found<br>\n";

		if ($this->error_found > 0)
		{
			$this->auth_messages["login"] = $this->messages[341];
			return false;
		}

		$this->sql_query = "select id,status,password from ".$this->logins_table." where username = \"".$username."\"";
		if ($this->debug_auth)
			echo $this->sql_query."<br>\n";
		$result = $db->Execute($this->sql_query);

		if (!$result)
		{
			$this->auth_messages["login"] = $this->messages[341];
			$this->site_error($this->sql_query,$db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 0)
		{
			//can login with email address also
			$this->sql_query = "select * from ".$this->userdata_table." where email = \"".$username."\"";
			if ($this->debug_auth)
				echo $this->sql_query."<br>\n";
			$email_match_result = $db->Execute($this->sql_query);

			if (!$email_match_result)
			{
				$this->auth_messages["login"] = $this->messages[341];
				$this->site_error($this->sql_query,$db->ErrorMsg());
				return false;
			}
			elseif ($email_match_result->RecordCount() == 0)
			{
				//account not found
				$this->auth_messages["login"] = $this->messages[342];
				return false;
			}
			elseif ($email_match_result->RecordCount() == 1)
			{
				$show_email_match = $email_match_result->FetchNextObject();
				$this->sql_query = "select * from ".$this->logins_table." where id = \"".$show_email_match->ID."\" and password = \"".$password."\"";
				if ($this->debug_auth)
					echo $this->sql_query."<br>\n";
				$password_result = $db->Execute($this->sql_query);

				if (!$password_result)
				{
					$this->auth_messages["login"] = $this->messages[341];
					$this->site_error($this->sql_query,$db->ErrorMsg());
					return false;
				}
				elseif ($password_result->RecordCount() == 0)
				{
					//account found but no password match
					$this->auth_messages["login"] = $this->messages[341];
	                       		//$this->error_messages["password"] = $this->messages[131];
	                        	return false;
				}
				elseif ($password_result->RecordCount() == 1)
				{
					$show = $password_result->FetchNextObject();
					if ($show->PASSWORD == $password)
					{
						if ($show->STATUS == 1)
						{
							$this->sql_query = "select level,email,firstname,lastname from ".$this->userdata_table." where id = ".$show->ID;
							if ($this->debug_auth)
								echo $this->sql_query."<br>\n";
							$level_result = $db->Execute($this->sql_query);
							if (!$level_result)
							{
								//$this->body .=  $this->sql_query." is the query<br>\n";
								$this->auth_messages["login"] = $this->messages[341];
								return false;
							}
							elseif (($level_result->RecordCount() == 0) || ($level_result->RecordCount() > 1))
							{
								//$this->body .=  $this->sql_query." is the query<br>\n";
								$this->auth_messages["login"] = $this->messages[341];
								return false;
							}
							else
							{
								$show_level = $level_result->FetchNextObject();
								$this->sql_query = "update geodesic_sessions set
									user_id = ".$show->ID.",
									level = ".$show_level->LEVEL."
									where classified_session = \"".$session."\"";
								$session_result = $db->Execute($this->sql_query);
								if ($this->debug_auth)
									echo $this->sql_query."<br>\n";
								if (!$session_result)
								{
									//$this->body .=  $this->sql_query." is the query<br>\n";
									$this->auth_messages["login"] = $this->messages[341];
									return false;
								}

								$this->classified_user_id = $show->ID;
								$this->level = $show_level->LEVEL;
								$this->email_address = $show_level->EMAIL;
								$this->firstname = $show_level->FIRSTNAME;
								$this->lastname = $show_level->LASTNAME;

								if ($this->debug_auth)
									echo $this->configuration_data['use_api']." is use api in authenticate<bR>\n";
								if ($this->configuration_data['use_api'])
								{
									include("config.php");
									$auth_variables = array();
									$auth_variables["db_host"] = $db_host;
									$auth_variables["db_name"] = $database;
									$auth_variables["username"] = $username;
									$auth_variables["password"] = $password;
									$auth_variables["installation_type"] = 1;
									include_once("classes/api_login.php");
									if ($this->debug_auth)
									{
										foreach ($auth_variables as $key => $value)
										{
											echo $key." - ".$value."<br>\n";
										}
									}
									$api_login = new API_login($auth_variables);
									$api_login->api_log_user_in();

									$db = &ADONewConnection('mysql');

									if (!$db->Connect($db_host, $db_username, $db_password, $database))
									{
										echo "could not connect to database<br>";
										echo $db->ErrorMsg()." is the error<bR>\n";
										exit;
									}
								}
								return $show->ID;
							}
						}
						else
						{
							$this->auth_messages["login"] = $this->messages[345];
							return false;
						}
					}
					else
					{
						$this->auth_messages["login"] = $this->messages[341];
						$this->error_messages["password"] = $this->messages[340];
						return false;
					}
				}
				else
				{
					$this->auth_messages["login"] = $this->messages[341];
					return false;
				}
			}
			else
			{
				$this->auth_messages["login"] = $this->messages[341];
				return false;
			}
		}
		elseif ($result->RecordCount() == 1)
		{
			//account found
			$show = $result->FetchNextObject();
			if ($show->PASSWORD == $password)
			{
				if ($show->STATUS == 1)
				{
					$this->sql_query = "select level,email,firstname,lastname from ".$this->userdata_table." where id = ".$show->ID;
					if ($this->debug_auth)
						echo $this->sql_query."<br>\n";
					$level_result = $db->Execute($this->sql_query);
					if (!$level_result)
					{
						//$this->body .=  $this->sql_query." is the query<br>\n";
						$this->auth_messages["login"] = $this->messages[341];
						return false;
					}
					elseif (($level_result->RecordCount() == 0) || ($level_result->RecordCount() > 1))
					{
						//$this->body .=  $this->sql_query." is the query<br>\n";
						$this->auth_messages["login"] = $this->messages[341];
						return false;
					}
					else
					{
						$show_level = $level_result->FetchNextObject();
						$this->sql_query = "update geodesic_sessions set
							user_id = ".$show->ID.",
							level = ".$show_level->LEVEL."
							where classified_session = \"".$session."\"";
						$session_result = $db->Execute($this->sql_query);
						if ($this->debug_auth)
							echo $this->sql_query."<br>\n";
						if (!$session_result)
						{
							//$this->body .=  $this->sql_query." is the query<br>\n";
							$this->auth_messages["login"] = $this->messages[341];
							return false;
						}

						$this->classified_user_id = $show->ID;
						$this->level = $show_level->LEVEL;
						$this->email_address = $show_level->EMAIL;
						$this->firstname = $show_level->FIRSTNAME;
						$this->lastname = $show_level->LASTNAME;

						if ($this->debug_auth)
							echo $this->configuration_data['use_api']." is use api in authenticate<bR>\n";
						if ($this->configuration_data['use_api'])
						{
							include("config.php");
							$auth_variables = array();
							$auth_variables["db_host"] = $db_host;
							$auth_variables["db_name"] = $database;
							$auth_variables["username"] = $username;
							$auth_variables["password"] = $password;
							$auth_variables["installation_type"] = 1;
							include_once("classes/api_login.php");
							if ($this->debug_register)
							{
								foreach ($auth_variables as $key => $value)
								{
									echo $key." - ".$value."<br>\n";
								}
							}
							$api_login = new API_login($auth_variables);
							$api_login->api_log_user_in();
						}
						return $show->ID;
					}
				}
				else
				{
					$this->auth_messages["login"] = $this->messages[345];
					return false;
				}
			}
			else
			{
				$this->auth_messages["login"] = $this->messages[341];
                       		$this->error_messages["password"] = $this->messages[340];
                        	return false;
			}
		}
		else
		{
			//internal account error
			//more than one account with the same login information
			$this->auth_messages["login"] = $this->messages[341];
			return false;
		}
	} //end of function login

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function lostpassword ($db,$info=0)
	{

		if ($info)
		{
			$this->page_id = 40;
			$this->get_text($db);
			$this->page_id = 41;
			$this->get_text($db);
			$this->sql_query = "select id from ".$this->userdata_table." where email = \"".$info["email"]."\"";
			$result = $db->Execute($this->sql_query);
			//$this->body .=  $this->sql_query." is the query<br>\n";
			if (!$result)
			{

				$this->error_message = urldecode($this->messages[832]);
				return false;
			}
			//elseif ($result->RecordCount() == 1)
			elseif ($result->RecordCount() >0)
			{
				$show_id = $result->FetchNextObject();
				$this->sql_query = "select username,password from ".$this->logins_table." where id =".$show_id->ID;
				$result = $db->Execute($this->sql_query);
				//$this->body .=  $this->sql_query." is the query<br>\n";
				if (!$result)
				{
					$this->body .=  $this->sql_query." is the query<br>\n";
					$this->error_message = urldecode($this->messages[832]);
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show = $result->FetchNextObject();

					$mailto = $info["email"];
					//$this->body .=  $mailto." is the mailto<br>\n";
					$subject = urldecode($this->messages[707]);
					$message = urldecode($this->messages[708])."\n".urldecode($this->messages[709]).": ".$show->USERNAME."\n";
					$message .= urldecode($this->messages[710]).": ".$show->PASSWORD."\n";

					if ($this->configuration_data['email_header_break'])
						$separator = "\n";
					else
						$separator = "\r\n";
					$from = "From: ".$this->configuration_data['site_email'].$separator."Reply-to: ".$this->configuration_data['site_email'].$separator;

					$additional = "-f".$this->configuration_data['site_email'];

					if ($this->configuration_data['email_configuration'] == 1)
						@mail($mailto,$subject,$message,$from,$additional);
					elseif ($this->configuration_data['email_configuration'] == 2)
						@mail($mailto,$subject,$message,$from);
					else
						@mail($mailto,$subject,$message);

					return true;
				}
				else
				{
					$this->error_message = urldecode($this->messages[351]);
					$this->lostpassword_form($db);
					exit;
					return false;
				}
			}
			else
			{
				//no account exist by that email address
				$this->error_message = urldecode($this->messages[351]);
				$this->lostpassword_form($db);
				exit;
				return false;
			}
		}
		else
		{
			//no email to send to
			$this->error_message = urldecode($this->messages[351]);
			$this->lostpassword_form($db);
			exit;
			return false;
		}
	} //end of function lostpassword

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function lostpassword_form ($db,$display_success=0)
	{
		$this->page_id = 40;
		$this->get_text($db);
		//display the form to collect an email address
		$this->body .=  "<form action=".$this->configuration_data['classifieds_url']."?a=18 method=post>\n";
		$this->body .=  "<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
		$this->body .=  "<tr class=lost_password_page_title>\n\t<td colspan=2>\n\t".urldecode($this->messages[347])."\n\t</td>\n</tr>\n";
		$this->body .=  "<tr class=page_description>\n\t<td colspan=2>\n\t".urldecode($this->messages[348])."\n\t</td>\n</tr>\n";
		if (strlen($this->error_message) > 0)
			$this->body .=  "<tr class=error_message>\n\t<td colspan=2>\n\t".$this->error_message."\n\t</td>\n</tr>\n";
		if ($display_success)
			$this->body .=  "<tr class=error_message>\n\t<td colspan=2>\n\t".urldecode($this->messages[2496])."\n\t</td>\n</tr>\n";
		$this->body .=  "<tr class=field_label>\n\t<td align=center>\n\t".urldecode($this->messages[349])."&nbsp;<input type=text name=b[email] size=30 maxsize=100>\n\t</td>\n</tr>\n";
		$this->body .=  "<tr class=send_password_button>\n\t<td  colspan=2>\n\t<input type=submit name=submit value=\"".urldecode($this->messages[350])."\" class=send_password_button>\n\t</td>\n</tr>\n";
		$this->body .=  "</table>\n";
		$this->display_page($db);
	} //end of function lostpassword_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function change_password ($db,$info=0)
	{
		if ($info)
		{
			if (((strlen(trim($info[password1])) > 5) && (strlen(trim($info[password1])) < 15)) && ($info[password1] == $info[password2]))
			{
				//the password passes the restrictions
				$this->sql_query = "select id from ".$this->userdata_table." where email = ".$this->classified_user_id;
				$result = $db->Execute($this->sql_query);

				if (!$result)
				{
					$this->error_message = $this->messages[832];
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$this->sql_query = "select * from ".$this->logins_table." where id = ".$this->classified_user_id;
					$login_result = $db->Execute($this->sql_query);
					if (!$login_result)
					{
						$this->auth_message = urldecode($this->messages[832]);
						return false;
					}
					elseif ($login_result->RecordCount() == 1)
					{
						$this->sql_query = "update ".$this->logins_table." set
							password = \"".$info[password1]."\"
							where id = ".$this->classified_user_id;
						$result = $db->Execute($this->sql_query);
					}
					else
					{
						$this->error_message = urldecode($this->messages[832]);
						return false;
					}
				}
				else
				{
					$this->error_message = urldecode($this->messages[832]);
					return false;
				}
			}
			else
			{
				$this->auth_messages["password_form"] = $this->messages[158];
			}
		}
		else
		{
			$this->error_message = urldecode($this->messages[832]);
			return false;
		}
	} //end of change_password

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function change_password_form($db)
	{
		$this->page_id = 40;
		$this->get_text($db);
		$this->body .=  "<form action=".$this->configuration_data['classifieds_url']."?a=10 method=post>\n";
		$this->body .=  "<table cellpadding=3 cellspacing=1 border=0 width=100%>\n";
		$this->body .=  "<tr class=login_form_title>\n\t<td colspan=3>\n\t".urldecode($this->messages[155])."\n\t</td>\n</tr>\n";
		$this->body .=  "<tr class=login_form_instructions>\n\t<td colspan=3>\n\t".urldecode($this->messages[156])."\n\t</td>\n</tr>\n";
		if ($this->auth_messages["password_form"])
			$this->body .=  "<tr class=login_form_error>\n\t<td colspan=3>\n\t".$this->auth_messages["password_form"]."\n\t</td>\n</tr>\n";
		$this->body .=  "<tr class=login_form_field_labels>\n\t<td align=right>".urldecode($this->messages[126])."\n\t</td>\n\t";
		$this->body .=  "<td>\n\t<input type=password name=b[password1] size=15>\n\t</td>\n\t";
		$this->body .=  "<tr class=login_form_field_labels>\n\t<td align=right>".urldecode($this->messages[157])."\n\t</td>\n\t";
		$this->body .=  "<td>\n\t<input type=password name=b[password2] size=15>\n\t</td>\n\t";
		$this->body .=  "</td>\n</tr>\n";
		$this->body .=  "<tr class=login_form_submit_button>\n\t<td colspan=2>\n\t<input type=submit name=submit value=submit>\n\t</td>\n</tr>\n";
		$this->body .=  "</table>\n";
		$this->body .=  "</form>\n";
		$this->display_page($db);
		$this->error_message = 0;
		$this->auth_messages["password_form"] = 0;
	} //end of function change_password_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function auth_error()
	{
		$this->body .=  "<table cellpadding=5 cellspacing=1 border=0 align=center width=100%>\n";
		$this->body .=  "<tr class=login_form_title>\n\t<td>".urldecode($this->messages[832])."</td>\n</tr>\n";
		if ($this->error_message)
			$this->body .=  "<tr class=login_form_error>\n\t<td>".$this->error_message."</td>\n</tr>\n";
		$this->body .=  "</table>\n";
	} //end of function auth_error

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function already_logged_in($db)
	{
		$this->page_id = 39;
		$this->get_text($db);
		$this->body .=  "<table cellpadding=5 cellspacing=1 border=0 align=center width=100%>\n";
		$this->body .=  "<tr class=login_form_title>\n\t<td align=center>".urldecode($this->messages[343])."</td>\n</tr>\n";
		$this->body .=  "<tr class=page_description>\n\t<td align=center><a href=".$this->configuration_data['classifieds_url']."?a=17 class=page_description>".urldecode($this->messages[344])."</a></td>\n</tr>\n";
		$this->body .=  "</table>\n";
		$this->display_page($db);
	} //end of function auth_error

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function reset_language($db,$language_id=0)
	{
		if ($language_id)
		{
			//$this->body .=  "resetting language to ".$language_id."<br>\n";
			$this->language_id = $language_id;
			//$this->set_language($db);
			//$this->push_messages_into_array($db,9);
		}
		else
		{
			//reset to language 1
			$this->language_id = 1;
			//$this->set_language($db);
			//$this->push_messages_into_array($db,9);
		}
	} //end of function reset_language

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Auth
?>