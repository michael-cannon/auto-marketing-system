<? //api_register.php
include_once("classes/api_site_class.php");
class API_Register extends Api_Site
{
	var $user_price_plan;
	var $user_group;
	var $price_plan;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function API_Register ($user_info)
	{
		$this->Api_site();

		$this->user_info = $user_info;
	} // end of function API_Register

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function api_insert_user()
	{
		//check data
		//get installation(s) info
		$this->get_installation_info();
		while ($this->installation_info = $this->installations->FetchRow())
		{
        	if($this->debug) echo "<hr>Inst. type is ".$this->installation_info["installation_type"];
			//check that the current installation is a latent installation
			if ($this->check_latent_installation())
			{
				//connect to the installation database
				if ($this->connect_to_installation_db())
				{
					//get group and price plan
					if ($this->duplicate_email())
					{
						$this->get_user_group_and_price_plan();
						if ($this->insert_into_logins_table())
						{
							$this->user_id = $this->installation_db->Insert_ID();
							if ($this->insert_into_userdata_table())
							{
								//insert into users_group_price_plans table
								$this->get_user_price_plan();

								//check for expiration of price plans
								if ($this->price_plan->EXPIRATION_TYPE == 2)
									$this->insert_price_plan_expiration();

								$this->insert_into_user_groups_and_price_plans();

								//check to see if registration credits or free subscription period
								//echo $this->price_plan->TYPE_OF_BILLING." is type of billing<BR>\n";
								if ($this->price_plan->TYPE_OF_BILLING == 1)
								{
									//fee based subscriptions
									if ($this->price_plan->CREDITS_UPON_REGISTRATION > 0)
										$this->insert_credits_upon_registration();
								}
								elseif ($this->price_plan->TYPE_OF_BILLING == 2)
								{
									//subscription based
									if ($this->price_plan->FREE_SUBSCRIPTION_PERIOD_UPON_REGISTRATION > 0)
										$this->insert_free_subscription_period();
								}
								
								$this->insert_session();
							}
						}
					}
					$this->installation_db->Close();
					if ($this->debug) echo "database connection closed<br>\n";
				}
			} //end if (check_latent_installation())
		} //end of while
		if ($this->debug) echo "about to return true<br>\n";
		return true;
	} //end of function api_insert_user

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function duplicate_email()
	{
		if ($this->debug)
		{
			echo $this->installation_info["installation_type"]." is installation_type in duplicate email<bR>\n";
		}
		switch ($this->installation_info["installation_type"])
		{
			case 1:
				$this->sql_query = "select * from geodesic_userdata where email = \"".$this->user_info["email"]."\"";
				if ($this->debug) echo $this->sql_query." is the query to check classifieds ent<BR>\n";
				break;
			case 2:
				$this->sql_query = "select * from geodesic_classifieds_userdata where email = \"".$this->user_info["email"]."\"";
				if ($this->debug) echo $this->sql_query." is the query to check classifieds premier<BR>\n";
				break;
			case 3:
			case 4:
				$this->user_group = 1;
				$this->user_price_plan = 1;
				return true;
				break;
			case 5:
				$this->sql_query = "select * from geodesic_auctions_userdata where email = \"".$this->user_info["email"]."\"";
				if ($this->debug) echo $this->sql_query." is the query to check auctions premier<BR>\n";
				break;
			case 6:
				$this->sql_query = "select * from geodesic_userdata where email = \"".$this->user_info["email"]."\"";
				break;
			case 7:
				$this->sql_query = "select * from geocore_users where email = \"".$this->user_info["email"]."\"";
				break;
			case 8: //vBulletin
				//do all vBulletin registration here instead of across several function calls
				//include
				if ($this->debug)
				{
					echo "Attempt opening vbulletin_config_path: ".$this->installation_info["vbulletin_config_path"]."<br>\n";
					//if (is_array($this->installation_info))
					//{
					//	reset ($this->installation_info);
					//	foreach ($this->installation_info as $key => $value)
					//		echo "installation key: ".$key." is ".$value."<br>\n";
					//}
				}
				include ($this->installation_info["vbulletin_config_path"]);

				if (isset($config['Database']['tableprefix']))
				{
					//this changed in some version...started at least in 3.5.2
					$tableprefix = $config['Database']['tableprefix'];
				}

				$this->get_vBulletin_vboptions($tableprefix);
				$unicode_name = preg_replace('/&#([0-9]+);/esiU', "convert_int_to_utf8('\\1')", $this->user_info["username"]);
				$this->sql_query = "SELECT username FROM " . $tableprefix . "user
						WHERE username IN ('" . addslashes($this->vbulletin_htmlspecialchars_uni($this->user_info["username"])) . "'
						, '" . addslashes($this->vbulletin_htmlspecialchars_uni($unicode_name)) . "')";
				$duplicate_result = $this->installation_db->Execute($this->sql_query);
				if ($this->debug) echo $this->sql_query." in api<br>\n";
				if (!$duplicate_result)
				{
					$this->error_message = "API ERROR - CHECKING USERNAME DUPLICATION IN REGISTRATION AGAINST VBULLETIN
						INSTALLATION\n
						THERE WAS AN ERROR IN THE SQL STATEMENT\n\n".$this->sql_query."\n\n";
					if ($this->debug)
					{
						echo $this->sql_query."<bR>\n";
						echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
					}
					$this->send_admin_error_email();
					return false;
				}
				elseif ($duplicate_result->RecordCount() == 0)
				{
					//username does not exist yet
					$this->sql_query = "SELECT username,email FROM " . $tableprefix . "user WHERE email='" . addslashes($this->user_info["email"])."'";
					$duplicate_email_result = $this->installation_db->Execute($this->sql_query);
					if ($this->debug)
						echo $this->sql_query." in api<br>\n";
					if (!$duplicate_email_result)
					{
						$this->error_message = "API ERROR - CHECKING EMAIL DUPLICATION IN REGISTRATION AGAINST VBULLETIN
							INSTALLATION\n
							THERE WAS AN ERROR IN THE SQL STATEMENT\n\n".$this->sql_query."\n\n";
						if ($this->debug)
						{
							echo $this->sql_query."<bR>\n";
							echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
						}
						$this->send_admin_error_email();
						return false;
					}
					elseif (($duplicate_email_result->RecordCount() == 0) ||
						(($duplicate_email_result->RecordCount() > 0) && (!$this->vboptions['requireuniqueemail'])))
					{
						//get salt for password
						$salt = '';

						for ($i = 0; $i < 3; $i++)
						{
							$salt .= chr(rand(32, 126));
						}
						$hashedpassword = md5(md5($this->user_info["password"]) . $salt);

						$this->sql_query = "SELECT reputationlevelid FROM " . $tableprefix . "reputationlevel
							WHERE minimumreputation <= " . intval($this->vboptions['reputationdefault']) . "
							ORDER BY minimumreputation DESC
							LIMIT 1";
						$reputation_level_result = $this->installation_db->Execute($this->sql_query);
						if ($this->debug)
							echo $this->sql_query." in api<br>\n";
						if ((!$reputation_level_result) || ($reputation_level_result->RecordCount() == 0))
						{
							$this->error_message = "API ERROR - GETTING REPUTATION LEVELREGISTRATION AGAINST VBULLETIN
								INSTALLATION\n
								THERE WAS AN ERROR IN THE SQL STATEMENT\n\n".$this->sql_query."\n\n";
							if ($this->debug)
							{
								echo $this->sql_query."<bR>\n";
								echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
							}
							$this->send_admin_error_email();
							return false;
						}
						elseif ($reputation_level_result->RecordCount() == 1)
						{
							$reputation_level = $reputation_level_result->FetchNextObject();
							if ($this->vboptions['verifyemail'])
							{
								$newusergroupid = 3;
							}
							else
							{
								if ($this->vboptions['moderatenewmembers'])
								{
									$newusergroupid = 4;
								}
								else
								{
									$newusergroupid = 2;
								}
							}
							define('REGOPTION_ADMINEMAIL', 1);
							define('REGOPTION_INVISIBLEMODE', 2);
							define('REGOPTION_RECEIVEEMAIL', 4);
							define('REGOPTION_ENABLEPM', 8);
							define('REGOPTION_EMAILONPM', 16);
							define('REGOPTION_PMPOPUP', 32);
							define('REGOPTION_VBCODE_NONE', 64);
							define('REGOPTION_VBCODE_STANDARD', 128);
							define('REGOPTION_VBCODE_ENHANCED', 256);
							define('REGOPTION_SUBSCRIBE_NONE', 512);
							define('REGOPTION_SUBSCRIBE_NONOTIFY', 1024);
							define('REGOPTION_SUBSCRIBE_INSTANT', 2048);
							define('REGOPTION_SUBSCRIBE_DAILY', 4096);
							define('REGOPTION_SUBSCRIBE_WEEKLY', 8192);
							define('REGOPTION_VCARD', 16384);
							define('REGOPTION_SIGNATURE', 32768);
							define('REGOPTION_AVATAR', 65536);
							define('REGOPTION_IMAGE', 131072);
							define('REGOPTION_THREAD_LINEAR_OLDEST', 262144);
							define('REGOPTION_THREAD_LINEAR_NEWEST', 524288);
							define('REGOPTION_THREAD_THREADED', 1048576);
							define('REGOPTION_THREAD_HYBRID', 2097152);
							define('REGOPTION_SHOWREPUTATION', 4194304);
							define('REGOPTION_REQBIRTHDAY', 8388608);

							$regoption = array();
							$options = array();
							$regoption['pmpopup'] = $this->vbulletin_bitwise(REGOPTION_PMPOPUP, $this->vboptions['defaultregoptions']);
							if ($this->vbulletin_bitwise(REGOPTION_SUBSCRIBE_NONE, $this->vboptions['defaultregoptions']))
							{
								$regoption['autosubscribe'] = -1;
							}
							else if ($this->vbulletin_bitwise(REGOPTION_SUBSCRIBE_NONOTIFY, $this->vboptions['defaultregoptions']))
							{
								$regoption['autosubscribe'] = 0;
							}
							else if ($this->vbulletin_bitwise(REGOPTION_SUBSCRIBE_INSTANT, $this->vboptions['defaultregoptions']))
							{
								$regoption['autosubscribe'] = 1;
							}
							else if ($this->vbulletin_bitwise(REGOPTION_SUBSCRIBE_DAILY, $this->vboptions['defaultregoptions']))
							{
								$regoption['autosubscribe'] = 2;
							}
							else
							{
								$regoption['autosubscribe'] = 3;
							}

							if ($this->vbulletin_bitwise(REGOPTION_VBCODE_NONE, $this->vboptions['defaultregoptions']))
							{
								$regoption['showvbcode'] = 0;
							}
							else if ($this->vbulletin_bitwise(REGOPTION_VBCODE_STANDARD, $this->vboptions['defaultregoptions']))
							{
								$regoption['showvbcode'] = 1;
							}
							else
							{
								$regoption['showvbcode'] = 2;
							}

							if ($this->vbulletin_bitwise(REGOPTION_THREAD_LINEAR_OLDEST, $this->vboptions['defaultregoptions']))
							{
								$regoption['threadedmode'] = 0;
								$options['postorder'] = 0;
							}
							else if ($this->vbulletin_bitwise(REGOPTION_THREAD_LINEAR_NEWEST, $this->vboptions['defaultregoptions']))
							{
								$regoption['threadedmode'] = 0;
								$options['postorder'] = 1;
							}
							else if ($this->vbulletin_bitwise(REGOPTION_THREAD_THREADED, $this->vboptions['defaultregoptions']))
							{
								$regoption['threadedmode'] = 1;
								$options['postorder'] = 0;
							}
							else if ($this->vbulletin_bitwise(REGOPTION_THREAD_HYBRID, $this->vboptions['defaultregoptions']))
							{
								$regoption['threadedmode'] = 2;
								$options['postorder'] = 0;
							}
							else
							{
								$regoption['threadedmode'] = 0;
								$options['postorder'] = 0;
							}
							$options = array();

							$options['coppauser'] = 0;
							$options['dstauto'] = 0;
							$options['dstonoff'] = 0;

							$options['invisible'] = $this->iif($this->vbulletin_bitwise(REGOPTION_INVISIBLEMODE, $this->vboptions['defaultregoptions']), 1, 0);
							$options['receivepm'] = $this->iif($this->vbulletin_bitwise(REGOPTION_ENABLEPM, $this->vboptions['defaultregoptions']), 1, 0);
							$options['emailonpm'] = $this->iif($this->vbulletin_bitwise(REGOPTION_EMAILONPM, $this->vboptions['defaultregoptions']), 1, 0);
							$options['showreputation'] = $this->iif($this->vbulletin_bitwise(REGOPTION_SHOWREPUTATION, $this->vboptions['defaultregoptions']), 1, 0);
							$options['showvcard'] = $this->iif($this->vbulletin_bitwise(REGOPTION_VCARD, $this->vboptions['defaultregoptions']), 1, 0);
							$options['showsignatures'] = $this->iif($this->vbulletin_bitwise(REGOPTION_SIGNATURE, $this->vboptions['defaultregoptions']), 1, 0);
							$options['showavatars'] = $this->iif($this->vbulletin_bitwise(REGOPTION_AVATAR, $this->vboptions['defaultregoptions']), 1, 0);
							$options['showimages'] = $this->iif($this->vbulletin_bitwise(REGOPTION_IMAGE, $this->vboptions['defaultregoptions']), 1, 0);

							$_USEROPTIONS = array(
								'showsignatures'    => 1,
								'showavatars'       => 2,
								'showimages'        => 4,
								'coppauser'         => 8,
								'adminemail'        => 16,
								'showvcard'         => 32,
								'dstauto'           => 64,
								'dstonoff'          => 128,
								'showemail'         => 256,
								'invisible'         => 512,
								'showreputation'    => 1024,
								'receivepm'         => 2048,
								'emailonpm'         => 4096,
								'hasaccessmask'     => 8192,
								//'emailnotification' => 16384, // this value is now handled by the user.autosubscribe field
								'postorder'         => 32768,
							);

							$options = $this->vbulletin_convert_array_to_bits($options, $_USEROPTIONS);

							$this->sql_query = "SELECT title FROM " . $tableprefix . "usertitle WHERE minposts<=0 ORDER BY minposts DESC LIMIT 1";
							$get_title_result = $this->installation_db->Execute($this->sql_query);
							if (($get_title_result) && ($get_title_result->RecordCount() == 1))
							{
								$show_user_title = $get_title_result->FetchNextObject();
								$user_title = addslashes($show_user_title->TITLE);
							}
							else
							{
								$user_title = addslashes($this->user_info["username"]);
							}


							//parentemail is disabled
							//birthday and birthday search are disabled
							//usertitle is defaulted to username
							$now = time();
							$testreferrerid['userid'] = 0;
							$this->sql_query = "INSERT INTO " . $tableprefix . "user
								(username, salt, password, passworddate, email, styleid, parentemail,
								showvbcode, usertitle, joindate, daysprune, lastvisit, lastactivity, usergroupid, timezoneoffset,
								options, maxposts, threadedmode, startofweek, ipaddress, pmpopup, referrerid,
								reputationlevelid, reputation, autosubscribe, birthday, birthday_search)
								VALUES
								('" . addslashes($this->vbulletin_htmlspecialchars_uni($this->user_info["username"])) . "',
								'" . addslashes($salt) . "',
								'" . addslashes($hashedpassword) . "',
								".$now.",
								'" . addslashes($this->vbulletin_htmlspecialchars_uni($this->user_info["email"])) . "',
								" . $this->vboptions['styleid'] . ",
								'',
								$regoption[showvbcode],
								'" . addslashes($this->user_info["username"]) . "',
								" . $now . ",
								-1,
								" . $now . ",
								" . $now . ",
								" . intval($newusergroupid) . ",
								'0',
								$options,
								-1,
								$regoption[threadedmode],
								1,
								'" . addslashes($_SERVER['REMOTE_ADDR']) . "',
								$regoption[pmpopup],
								0,
								" . intval($reputationlevel->REPUTATIONLEVELID) . ",
								" . intval($this->vboptions['reputationdefault']) . ",
								$regoption[autosubscribe],
								'',
								''
								)";
							$insert_user_result = $this->installation_db->Execute($this->sql_query);
							if ($this->debug)
								echo $this->sql_query."<bR>\n";
							if (!$insert_user_result)
							{
								$this->error_message = "ERROR INSERTING USER INTO VBULLETIN INSTALLATION\n
									USING THE FOLLOWING SQL\n\n".$this->sql_query."\n\n";
								if ($this->debug)
								{
									echo $this->sql_query."<bR>\n";
									echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
								}
								$this->send_admin_error_email();
								return false;
							}
							else
							{
								$user_id = $this->installation_db->Insert_ID();
								$this->sql_query = "INSERT INTO " . $tableprefix . "usertextfield (userid) VALUES (".$user_id.")";
								if ($this->debug)
									echo $this->sql_query."<bR>\n";
								$insert_usertextfield_result = $this->installation_db->Execute($this->sql_query);
								if (!$insert_usertextfield_result)
								{
									$this->error_message = "ERROR INSERTING USERTEXTFIELD INTO VBULLETIN INSTALLATION\n
										USING THE FOLLOWING SQL\n\n".$this->sql_query."\n\n";
									if ($this->debug)
									{
										echo $this->sql_query."<bR>\n";
										echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
									}
									$this->send_admin_error_email();
									return false;
								}
								else
								{
									$this->sql_query = "INSERT INTO " . $tableprefix . "userfield (userid) VALUES (".$user_id.")";
									if ($this->debug)
										echo $this->sql_query."<bR>\n";
									$insert_usertextfield_result = $this->installation_db->Execute($this->sql_query);
									if (!$insert_usertextfield_result)
									{
										$this->error_message = "ERROR INSERTING USERFIELD INTO VBULLETIN INSTALLATION\n
											USING THE FOLLOWING SQL\n\n".$this->sql_query."\n\n";
										if ($this->debug)
										{
											echo $this->sql_query."<bR>\n";
											echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
										}
										$this->send_admin_error_email();
										return false;
									}
									else
									{
										//successful registration
										//set cookies to be able to login
										$this->vbsetcookie('userid', $user_id ,1,$cookieprefix);
										$cookie_password = md5($hashedpassword.$this->installation_info["vbulletin_license_key"]);
										$this->vbsetcookie('password', $cookie_password,1,$cookieprefix);
										if ($this->debug)
										{
											echo $cookie_password." is the password encrypted with vbulletion license key -  ".$this->installation_info["vbulletin_license_key"]."<br>\n";
										}

										if ($_ENV['REQUEST_URI'] OR $_SERVER['REQUEST_URI'])
										{
											$scriptpath = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_ENV['REQUEST_URI'];
										}
										else
										{
											if ($_ENV['PATH_INFO'] OR $_SERVER['PATH_INFO'])
											{
												$scriptpath = $_SERVER['PATH_INFO'] ? $_SERVER['PATH_INFO']: $_ENV['PATH_INFO'];
											}
											else if ($_ENV['REDIRECT_URL'] OR $_SERVER['REDIRECT_URL'])
											{
												$scriptpath = $_SERVER['REDIRECT_URL'] ? $_SERVER['REDIRECT_URL']: $_ENV['REDIRECT_URL'];
											}
											else
											{
												$scriptpath = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_ENV['PHP_SELF'];
											}

											if ($_ENV['QUERY_STRING'] OR $_SERVER['QUERY_STRING'])
											{
												$scriptpath .= '?' . ($_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : $_ENV['QUERY_STRING']);
											}
										}

										if ($_SERVER['HTTP_CLIENT_IP'])
										{
											define('ALT_IP', $_SERVER['HTTP_CLIENT_IP']);
										}
										else if ($_SERVER['HTTP_X_FORWARDED_FOR'] AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches))
										{
											// make sure we dont pick up an internal IP defined by RFC1918
											foreach ($matches[0] AS $ip)
											{
												if (!preg_match("#^(10|172\.16|192\.168)\.#", $ip))
												{
													define('ALT_IP', $ip);
													break;
												}
											}
										}
										else if ($_SERVER['HTTP_FROM'])
										{
											define('ALT_IP', $_SERVER['HTTP_FROM']);
										}
										else
										{
											define('ALT_IP', $_SERVER['REMOTE_ADDR']);
										}

										$scriptpath = preg_replace('/(s|sessionhash)=[a-z0-9]{32}?&?/', '', $scriptpath);

										$seed = (double) microtime() * 1000000;
										mt_srand($seed);
										$random_seed = mt_rand(1, 1000000);
										$session_idhash = md5($_SERVER['HTTP_USER_AGENT'] . ALT_IP );
										$session_host = substr($_SERVER['REMOTE_ADDR'], 0, 15);

										$session['sessionhash'] = md5($current_time . $scriptpath . $session_idhash . $session_host . $random_seed);
										$session['dbsessionhash'] = $session['sessionhash'];

										$this->sql_query = "INSERT INTO " . $tableprefix . "session
												(sessionhash, userid, host, idhash, lastactivity, styleid, loggedin, bypass, useragent)
											VALUES
												('" . addslashes($session['sessionhash']) . "', " . intval($bbuserinfo->USERID) . ", '" . addslashes($session_host) . "', '" . addslashes($session_idhash) . "', " . $now . ", ".$this->vboptions["styleid"].", 1, 0, '" . addslashes($_SERVER["HTTP_USER_AGENT"]) . "')";
										$insert_session_result = $this->installation_db->Execute($this->sql_query);
										if ($this->debug)
											echo $this->sql_query."<br>\n";
										if (!$insert_session_result)
										{
											if ($this->debug)
											{
												echo $this->sql_query."<bR>\n";
												echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
											}
											$this->error_message = "API ERROR - LOGGING INTO VBULLETIN INSTALLATION\n
												USING THE FOLLOWING USERNAME\n\n".$this->user_info["username"]."\nAND
												INSERTING NEW SESSIONS\n\n".$this->sql_query;
											$this->send_admin_error_email();
										}
										$this->vbsetcookie('sessionhash', $session['sessionhash'], 0,$cookieprefix);
										return true;
									}
								}
							}
						}
					}
					else
					{
						$this->error_message = "EMAIL ADDRESS ALREADY EXISTS IN REGISTRATION AGAINST VBULLETIN
							INSTALLATION\n
							USING THE FOLLOWING EMAIL ADDRESS\n\n".$this->user_info["email"]."\n\n";
						if ($this->debug)
							echo $this->sql_query." <bR>\n";
						$this->send_admin_error_email();
						return false;
					}
				}
				else
				{
					//the username already exists
					$this->error_message = "API ERROR - DUPLICATE USERNAME EXISTS IN VBULLETIN INSTALLATION\n
						WITH THE FOLLOWING USERNAME: ".$this->user_info["username"]."\n\n";
					if ($this->debug) echo $this->sql_query." failed because already in other database<bR>\n";
					$this->send_admin_error_email();
					return false;
				}
				return true;
				break;
			case 9:
				//begin Phorum
				//vBulletin is completing whole process within this function

				$this->sql_query = "select * from ".$this->installation_info["phorum_database_table_prefix"]."_auth";
				$this->phorum_ver = "v3";
				$version_result = $this->installation_db->Execute($this->sql_query);
				if (!$version_result)
				{
					$this->phorum_ver = "v5";
				}
				if ($this->phorum_ver == "v3")
				{
					$this->phorum_auth_table = "auth";
					$this->phorum_rn_field = "name";
					$this->phorum_rn_value = $this->user_info["firstname"];
					$this->phorum_extra_fields = "";
					$this->phorum_extra_values = "";
					$this->phorum_cookie = "phorum_cookieauth";
				}
				else
				{
					$this->phorum_auth_table = "_users";
					$this->phorum_rn_field = "user_data";
					$this->phorum_rn_value = "a:1:{s:9:\"real_name\";s:15:\"".$this->user_info["firstname"]."\";}";
					$this->phorum_extra_fields = ",active";
					$this->phorum_extra_values = ",1";
					$this->phorum_cookie = "phorum_session_v5";

				}

				//check that user does not currently exist
				$this->sql_query ="select username, email from ".$this->installation_info["phorum_database_table_prefix"].$this->phorum_auth_table."
					where (upper(username)=upper('".$this->user_info["username"]."')
					or upper(email)=upper('".$this->user_info["email"]."'))";
				$check_phorum_user_result = $this->installation_db->Execute($this->sql_query);
				if ($this->debug) echo $this->sql_query."<bR>\n";
				if ((!$check_phorum_user_result) && ($this->debug))
				{
					if ($this->debug)
					{
						echo $this->sql_query."<bR>\n";
						echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
					}
				}
				if ($check_phorum_user_result->RecordCount() == 0)
				{
					$phorumpassword = md5($this->user_info["password"]);
					$phorumid = $this->classified_user_id;
					$this->sql_query = "insert into ".$this->installation_info["phorum_database_table_prefix"].$this->phorum_auth_table."
						( username, password, ".$this->phorum_rn_field.",email".$this->phorum_extra_fields.")
						values
						( '".$this->user_info["username"]."', '".$phorumpassword."', '".$this->phorum_rn_value."', '".$this->user_info["email"]."'".$this->phorum_extra_values.")";
					$insert_phorum_user_result = $this->installation_db->Execute($this->sql_query);
					if ($this->debug)
						echo $this->sql_query."<bR>\n";
					if ($insert_phorum_user_result)
					{
						$ph_sessionID=$this->user_info["username"].":".$phorumpassword;
						//cookie will be set for the present domain
						if (strlen(trim($this->installation_info["phorum_cookie_path"])) > 0)
							$cookie_path = $this->installation_info["phorum_cookie_path"];
						else
							$cookie_path = "/";
						SetCookie($this->phorum_cookie, $ph_sessionID , time()+(86400*365), $cookie_path,$this->installation_info["cookie_domain"]);
						return true;
					}
					else
					{
						//insert user failed
						$this->error_message = "API ERROR - ERROR INSERTING NEW USER INTO PHORUM INSTALLATION\n
							WITH THE FOLLOWING USERNAME: ".$this->user_info["username"]."\nOR\n
							EMAIL: ".$this->user_info["email"]."\n\n".$this->sql_query;
						if ($this->debug)
						{
							echo $this->sql_query."<bR>\n";
							echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
						}
						$this->send_admin_error_email();
						return false;
					}
				}
				elseif ($check_phorum_user_result->RecordCount() > 0)
				{
					//user currently exists within phorum database
					$this->error_message = "API ERROR - DUPLICATE USERNAME EXISTS IN PHORUM INSTALLATION\n
						WITH THE FOLLOWING USERNAME: ".$this->user_info["username"]."\nOR\n
						EMAIL: ".$this->user_info["email"]."\n\n".$this->sql_query;
					if ($this->debug)
					{
						echo $this->sql_query."<bR>\n";
						echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
					}
					$this->send_admin_error_email();
					return false;
				}
				else
				{
					//user currently exists within phorum database
					$this->error_message = "API ERROR - ERROR CHECK DUPLICATE USERNAME EXISTS IN PHORUM INSTALLATION\n
						WITH THE FOLLOWING USERNAME: ".$this->user_info["username"]."\nOR\n
						EMAIL: ".$this->user_info["email"]."\n\n".$this->sql_query;
					if ($this->debug)
						echo $this->sql_query." - produced error<bR>\n";
					$this->send_admin_error_email();
					return false;
				}
				//end Phorum
				//return here if everything is done in the other function call
				break;

			case 10:
				//begin Cerberus API

				//check that user does not currently exist
				$this->sql_query ="select * from address
					where upper(address_address)=upper('".$this->user_info["email"]."')";
				$check_cerberus_user_result = $this->installation_db->Execute($this->sql_query);
				if ($this->debug) echo $this->sql_query."<bR>\n";
				if ((!$check_cerberus_user_result) && ($this->debug))
				{
					echo $this->installation_db->ErrorMsg()." is the sql error<br>\n";
				}
				if ($check_cerberus_user_result->RecordCount() == 0)
				{
					$cerberuspassword = md5($this->user_info["password"]);
					$this->sql_query = "insert into public_gui_users VALUES('','" . $this->user_info["firstname"] . " " . $this->user_info["lastname"] . "','" .
						$this->user_info["address"] . "','" . $this->user_info["address_2"] . "','" . $this->user_info["city"] . "','" .
						$this->user_info["state"] . "','" . $this->user_info["zip"] . "','" . $this->user_info["country"] . "','" . $this->user_info["phone"] .
						"','" . $this->user_info["phone_2"] . "','','" . $this->user_info["fax"] . "','" . $cerberuspassword . "','0')";
					$insert_cerberus_user_result = $this->installation_db->Execute($this->sql_query);
					if ($this->debug)
						echo $this->sql_query."<bR>\n";
					if ($insert_cerberus_user_result)
					{
						$this->sql_query = "insert into address VALUES('','" . $this->user_info["email"] . "','0','" . mysql_insert_id() . "','" .
							$this->user_info["username"] . "')";
						$insert_cerberus_address_result = $this->installation_db->Execute($this->sql_query);
						if ($this->debug)
						{
							echo $this->sql_query."<bR>\n";
							echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
						}
						if ($insert_cerberus_address_result)
						{
							// log user in here
							require_once ($this->installation_info["cerberus_directory_path"]."config.php");
							require_once ($this->installation_info["cerberus_directory_path"]."session.php");
							require_once($this->installation_info["cerberus_publicgui_path"]."cer_PublicGUISettings.class.php");
							$pubgui = new cer_PublicGUISettings(PROFILE_ID);

							$cer_session->doLogin($this->user_info["email"],$this->user_info["password"]);
							$_SESSION["cer_login_serialized"] = serialize($cer_session);

							return true;
						} else {
							$this->error_message = "API ERROR - ERROR INSERTING NEW USER INTO CERBERUS INSTALLATION\n
								WITH THE FOLLOWING EMAIL: ".$this->user_info["email"]."\n\n".$this->sql_query;
							if ($this->debug)
							{
								echo $this->sql_query."<bR>\n";
								echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
							}
							$this->send_admin_error_email();
							return false;
						}
					}
					else
					{
						$this->error_message = "API ERROR - ERROR INSERTING NEW USER INTO CERBERUS INSTALLATION\n
							WITH THE FOLLOWING EMAIL: ".$this->user_info["email"]."\n\n".$this->sql_query;
						if ($this->debug)
						{
							echo $this->sql_query."<bR>\n";
							echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
						}
						$this->send_admin_error_email();
						return false;
					}
				}
				else
				{
					// update user details here
					$cerberus_user_data = $check_cerberus_user_result->FetchRow();
					$this->sql_query = "select * from public_gui_users where public_user_id='" . $cerberus_user_data["public_user_id"] . "'";
					$check_cerberus_user_result = $this->installation_db->Execute($this->sql_query);
					if ($check_cerberus_user_result) {
						$this->sql_query = "update public_gui_users set full_name='" . $this->user_info["firstname"] . " " . $this->user_info["lastname"] . "',
							mailing_street1='" . $this->user_info["address"] . "', mailing_street2='" . $this->user_info["address_2"] . "', mailing_city='" .
							$this->user_info["city"] . "', mailing_state='" . $this->user_info["state"] . "', mailing_zip='" . $this->user_info["zip"] .
							"', mailing_country='" . $this->user_info["country"] . "', phone_work='" . $this->user_info["phone"] . "', phone_home='" .
							$this->user_info["phone_2"] . "', phone_mobile='', phone_fax='" . $this->user_info["fax"] . "', password='" . $cerberuspassword .
							"', company_id='0' where public_user_id='" . $cerberus_user_data["public_user_id"] . "'";
						$update_cerberus_user_result = $this->installation_db->Execute($this->sql_query);
					} else {
						$this->sql_query = "insert into public_gui_users VALUES('','" . $this->user_info["firstname"] . " " . $this->user_info["lastname"] .
							"','" . $this->user_info["address"] . "','" . $this->user_info["address_2"] . "','" . $this->user_info["city"] . "','" .
							$this->user_info["state"] . "','" . $this->user_info["zip"] . "','" . $this->user_info["country"] . "','" .
							$this->user_info["phone"] . "','" . $this->user_info["phone_2"] . "','','" . $this->user_info["fax"] . "','" . $cerberuspassword .
							"','0')";
						$insert_cerberus_user_result = $this->installation_db->Execute($this->sql_query);
						$this->sql_query = "update address set public_user_id='" . mysql_insert_id() . "', confirmation_code='" . $this->user_info["username"] .
							"' where address_id='" . $cerberus_user_data["address_id"] . "'";
						$update_cerberus_address_result = $this->installation_db->Execute($this->sql_query);
					}
					return true;
				}
				//end Cerberus
				//return here if everything is done in the other function call
				break;
			case 11:
				$this->sql_query = "select * from jiveUser where username='".$this->user_info["username"]."'";
				$check_jive_user_result = $this->installation_db->Execute($this->sql_query);
				if ($this->debug) echo $this->sql_query."<bR>\n";
				if ((!$check_jive_user_result) && ($this->debug))
				{
					echo $this->installation_db->ErrorMsg()." is the sql error<br>\n";
				}
				if ($check_jive_user_result->RecordCount() == 0)
				{
					$this->sql_query = "insert into jiveUser VALUES('".$this->user_info["username"]."','".$this->user_info["password"]."','".$this->user_info["firstname"]." ".$this->user_info["lastname"]."','".$this->user_info["email"]."',unix_timestamp(),unix_timestamp())";
					$this->installation_db->Execute($this->sql_query);
					if ($this->debug) echo $this->sql_query."<bR>\n";
				}
				else
				{
					$this->sql_query = "update jiveUser set password='".$this->user_info["password"]."', name='".$this->user_info["firstname"]." ".$this->user_info["lastname"]."', email='".$this->user_info["email"]."', creationDate=unix_timestamp(), modificationDate=unix_timestamp() where username='".$this->user_info["username"]."'";
					$this->installation_db->Execute($this->sql_query);
					if ($this->debug) echo $this->sql_query."<bR>\n";
				}
				$this->sql_query = "delete * from jiveGroupUser where username='".$this->userinfo["username"]."'";
				$this->installation_db->Execute($this->sql_query);
				if ($this->debug) echo $this->sql_query."<bR>\n";
				$this->sql_query = "insert into jiveGroupUser values('Sexybay Users','".$this->user_info["username"]."',0)";
				$this->installation_db->Execute($this->sql_query);
				if ($this->debug) echo $this->sql_query."<bR>\n";
				return true;
				break;
			default:
				return false;
		}

		if ($this->debug) echo $this->sql_query." is the sql_query used in duplicate_email<br>\n";
		$duplicate_result = $this->installation_db->Execute($this->sql_query);
		if (!$duplicate_result)
		{
			$this->error_message = "API ERROR - CHECKING EMAIL DUPLICATION IN REGISTRATION\n
				THERE WAS AN ERROR IN THE SQL STATEMENT\n\n".
				$this->sql_query."\n\n".
				$this->installation_db->ErrorMsg()."\n\n";
			if ($this->debug) echo $this->sql_query." - produced an error<bR>\n";
			$this->send_admin_error_email();
			return false;
		}
		elseif ($duplicate_result->RecordCount() > 0)
		{
			//that email address already exists
			$this->error_message = "API ERROR - CHECKING EMAIL DUPLICATION IN REGISTRATION\n\n
				THIS EMAIL ALREADY EXISTS IN THIS INSTALLATION AND CANNOT BE ENTERED AS IS.\n
				IT HAS BEEN ENTERED INTO THE CONTROLLING INSTALLATION BUT NOT IN THIS INSTALLATION
				\n\n".$this->sql_query."\n\n".$this->installation_db->ErrorMsg()."\n\n".$duplicate_result->RecordCount()." is the recordcount<bR>\n";
			while ($show_email = $duplicate_result->FetchNextObject())
			{
				$this->error_message .= $show_email->EMAIL."\n";
			}
			$this->error_message .= $this->installation_info["installation_type"]." is the installation type\n";
			$this->error_message .= $this->installation_db->host." is adodb host<bR>\n";
			$this->error_message .= $this->installation_db->database." is adodb database<bR>\n";
			$this->error_message .= $this->installation_db->username." is adodb username<bR>\n";
			//$this->send_admin_error_email();
			return false;
		}
		else
		{
			return true;
		}
	} //end of function duplicate_email

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_user_group_and_price_plan ()
	{
		if (strlen(trim($this->user_info["registration_code"])) > 0)
		{
			switch ($this->installation_info["installation_type"])
			{
				case 1:
				case 2:
					$this->sql_query = "select * from geodesic_groups where registration_code = \"".trim($this->user_info["registration_code"])."\"";
					break;
				case 3:
				case 4:
					$this->user_group = 1;
					$this->user_price_plan = 1;
					return true;
					break;
				case 5:
					$this->sql_query = "select * from geodesic_auctions_groups where registration_code = \"".trim($this->user_info["registration_code"])."\"";
					break;
				case 6:
					$this->sql_query = "select * from geodesic_groups where registration_code = \"".trim($this->user_info["registration_code"])."\"";
					break;
				case 7:
					$this->sql_query = "select * from geocore_users_groups where registration_code = \"".trim($this->user_info["registration_code"])."\"";
					break;
				case 8:
					//vBulletin
					//return here if everything is done in the other function call
					return true;
					break;
				case 9:
					//begin Phorum
					//return here if everything is done in the other function call
					return true;
					break;
				case 10:
					//begin Cerberus
					//return here if everything is done in the other function call
					return true;
					break;
				case 11:
					//begin Jive
					//return here if everything is done in the other function call
					return true;
					break;										
				default:
					return false;
			}

			$code_result = $this->installation_db->Execute($this->sql_query);
			if ($this->debug)
				echo $this->sql_query." in api<br>\n";
			if (!$code_result)
			{
				$this->error_message = "API ERROR - CHECKING REGISTRATION CODE IN REGISTRATION";
				if ($this->debug)
				{
					echo $this->sql_query."<bR>\n";
					echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
				}
				$this->send_admin_error_email();
				return false;
			}
			elseif ($code_result->RecordCount() == 1)
			{
				$show = $code_result->FetchNextObject();
				$this->user_group = $show->GROUP_ID;
				$this->user_price_plan = $show->PRICE_PLAN_ID;
				return true;
			}
			else
			{
				$this->get_default_group();
				return true;
			}
		}
		else
		{
			//get default group and price plan
			$this->get_default_group();
			return true;
		}
	} //end of function get_user_group_and_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_default_group ()
	{
		switch ($this->installation_info["installation_type"])
		{
			case 1:
				$this->sql_query = "select * from geodesic_groups where default_group = 1";
				break;
			case 2:
				$this->sql_query = "select * from geodesic_classifieds_groups where default_group = 1";
				break;
			case 3:
			case 4:
				$this->user_group = 1;
				$this->user_price_plan = 1;
				return true;
				break;
			case 5:
				$this->sql_query = "select * from geodesic_auctions_groups where default_group = 1";
				break;
			case 6:
				$this->sql_query = "select * from geodesic_groups where default_group = 1";
				break;
			case 7:
				$this->sql_query = "select * from georeoc_users_groups where default_group = 1";
				break;
			case 9:
				//vBulletin
				//return here if everything is done in the other function call
				return true;
				break;
			case 9:
				//begin Phorum

				//end Phorum
				//return here if everything is done in the other function call
				return true;
				break;
			case 10:
				//begin Cerberus
				//return here if everything is done in the other function call
				return true;
				break;		
			case 11:
				//begin Jive
				//return here if everything is done in the other function call
				return true;
				break;						
			default:
				return false;
		}
		$group_result = $this->installation_db->Execute($this->sql_query);
		if ($this->debug)
			echo $this->sql_query." in api<br>\n";
		if (!$group_result)
		{
			//echo "API ERROR - GETTING DEFAULT GROUP INFO IN REGISTRATION<br>";
			$this->error_message = "API ERROR - GETTING DEFAULT GROUP INFO IN REGISTRATION - SQL ERROR";
			if ($this->debug)
			{
				echo $this->sql_query."<bR>\n";
				echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
			}
			//echo $this->installation_db->ErrorMsg()."<bR>";
			$this->send_admin_error_email();
			return false;
		}
		elseif ($group_result->RecordCount() == 1)
		{
			$show_group = $group_result->FetchNextObject();
			$this->user_group = $show_group->GROUP_ID;
			$this->user_price_plan = $show_group->PRICE_PLAN_ID;
			if ($this->debug)
			{
				echo $this->user_group." is user_group<Br>\n";
				echo $this->user_price_plan." is user_price_plan<Br>\n";
			}
			return true;
		}
		elseif ($group_result->RecordCount() == 0)
		{
			$show_group = $group_result->FetchNextObject();
			$this->user_group = 1;
			$this->user_price_plan = 1;
			if ($this->debug)
			{
				echo $this->user_group." is user_group<Br>\n";
				echo $this->user_price_plan." is user_price_plan<Br>\n";
			}
			return true;
		}
		else
		{
			$this->error_message = "API ERROR - GETTING DEFAULT GROUP INFO IN REGISTRATION - MULTIPLE DEFAULT ERROR - OR NO DEFAULT GROUP";
			if ($this->debug)
			{
				echo $this->sql_query."<bR>\n";
				echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
			}
			$this->send_admin_error_email();
			return false;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function send_admin_error_email($api_error=0)
	{
		if ($api_error)
		{
			$this->error_message = "An error occurred trying to connect to the api database.\nThe user information below
				will have to be registered on all installations manually.\n";
		}
		else
		{
			$this->error_message .= "An error occurred trying to register a user at the following installation:\n";
			$this->error_message .= "with the following db_host:\n".$this->installation_info["db_host"]."\n";
			$this->error_message .= "with the following db_name:\n".$this->installation_info["db_name"]."\n";
			$this->error_message .= "with the following installation_type:\n".$this->installation_info["installation_type"]."\n";
		}
		$this->error_message .= "\nwith the following registration information:\n";
		if (is_array($this->user_info))
		{
			foreach ($this->user_info as $key => $value)
			{
				$this->error_message .= "".$key." :".$value."\n";
			}
		}

		mail($this->installation_info["admin_email"],"api registration error",$this->error_message);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_user_price_plan ()
	{
		if ($this->user_price_plan)
		{
			switch ($this->installation_info["installation_type"])
			{
				case 1:
				case 2:
				case 3:
				case 4:
					$this->sql_query = "select * from geodesic_classifieds_price_plans where price_plan_id = ".$this->user_price_plan;
					break;
				case 5:
					$this->sql_query = "select * from geodesic_auctions_price_plans where price_plan_id = ".$this->user_price_plan;
					break;
				case 6:
					$this->sql_query = "select * from geodesic_auctions_price_plans where price_plan_id = ".$this->user_price_plan;
					break;
				case 7:
					return true;
					break;
				case 8:
					//vBulletin
					//return here if everything is done in the other function call
					return true;
					break;
				case 9:
					//begin Phorum

					//end Phorum
					//return here if everything is done in the other function call
					return true;
					break;
				case 10:
					//begin Cerberus
					//return here if everything is done in the other function call
					return true;
					break;		
				case 11:
					//begin Jive
					//return here if everything is done in the other function call
					return true;
					break;												
					
				default:
					return false;
			}

			$price_plan_result = $this->installation_db->Execute($this->sql_query);
			if ($this->debug)
				echo $this->sql_query." in api<br>\n";
			if (!$price_plan_result)
			{
				if ($this->debug)
				{
					echo $this->sql_query."<bR>\n";
					echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
				}
				$this->price_plan = 0;
			}
			elseif ($price_plan_result->RecordCount() == 1)
			{
				$this->price_plan = $price_plan_result->FetchNextObject();
			}
			else
			{
				$this->price_plan = 0;
			}
		}
		else
		{
			if ($this->debug)
				echo $this->user_id." is user_id...<br>\n";
			$this->price_plan = 0;
		}
		return true;
	} //end of function get_user_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_into_logins_table()
	{
		switch ($this->installation_info["installation_type"])
		{
			case 1:
				$this->sql_query = "insert into geodesic_logins (username, password,status)
					values
					(\"".$this->user_info["username"]."\", \"".$this->user_info["password"]."\",1)";
				break;
			case 2:
			case 3:
			case 4:
				$this->sql_query = "insert into geodesic_classifieds_logins (username, password,status)
					values
					(\"".$this->user_info["username"]."\", \"".$this->user_info["password"]."\",1)";
				break;
			case 5:
				$this->sql_query = "insert into geodesic_auctions_logins (username, password,status)
					values
					(\"".$this->user_info["username"]."\", \"".$this->user_info["password"]."\",1)";
				break;
			case 6:
				$this->sql_query = "insert into geodesic_logins (username, password,status)
					values
					(\"".$this->user_info["username"]."\", \"".$this->user_info["password"]."\",1)";
				break;
			case 7:
				$this->sql_query = "insert into geodesic_auctions_logins (username, password,status)
					values
					(\"".$this->user_info["username"]."\", \"".$this->user_info["password"]."\",1)";
				break;
			case 8: //vBulletin
				return true;
				break;
			case 9:
				//begin Phorum

				//end Phorum
				//return here if everything is done in the other function call
				return true;
				break;
			case 10:
				//begin Cerberus
				//return here if everything is done in the other function call
				return true;
				break;	
			case 11:
				//begin Jive
				//return here if everything is done in the other function call
				return true;
				break;
			default:
				return false;
		}

		if ($this->debug)
			echo $this->sql_query." in api<br>\n";
		$login_result = $this->installation_db->Execute($this->sql_query);
		if (!$login_result)
		{
			
			$this->error_message = "API ERROR - INSERTING LOGIN INFO IN REGISTRATION";
			if ($this->debug)
			{
				echo $this->sql_query."<bR>\n";
				echo $this->installation_db->ErrorMsg()."<bR>\n";
			}
			$this->send_admin_error_email();
			return false;
		}
		return true;
	} // end of function insert_into_logins_table

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_into_userdata_table()
	{
		switch ($this->installation_info["installation_type"])
		{
            case 1:
                $this->sql_query = "insert into geodesic_userdata (id,username,email,email2,newsletter,level,company_name,
                    business_type,firstname,lastname,address,address_2,zip,city,state,country,phone,phone2,fax,url,date_joined,
                    communication_type,rate_sum,rate_num,optional_field_1,optional_field_2,optional_field_3,optional_field_4,
                    optional_field_5,optional_field_6,optional_field_7,optional_field_8,optional_field_9,optional_field_10,filter_id,
                    phoneext,phoneext_2,referrer,subdomain) values
                    (".$this->user_id.",\"".$this->user_info["username"]."\",\"".$this->user_info["email"]."\",
                    \"".$this->user_info["email2"]."\",
                    \"0\", 0,\"".addslashes($this->user_info["company_name"])."\",
                    \"".$this->user_info["business_type"]."\",\"".addslashes($this->user_info["firstname"])."\",
                    \"".addslashes($this->user_info["lastname"])."\",
                    \"".addslashes($this->user_info["address"])."\",\"".addslashes($this->user_info["address_2"])."\",
                    \"".addslashes($this->user_info["zip"])."\",
                    \"".addslashes($this->user_info["city"])."\",\"".$this->user_info["state"]."\",
                    \"".$this->user_info["country"]."\",
                    \"".addslashes($this->user_info["phone"])."\",\"".addslashes($this->user_info["phone_2"])."\",
                    \"".addslashes($this->user_info["fax"])."\",\"".addslashes($this->user_info["url"])."\",".time().",1,0,0,
                    \"".addslashes($this->user_info["optional_field_1"])."\",\"".addslashes($this->user_info["optional_field_2"])."\",
                    \"".addslashes($this->user_info["optional_field_3"])."\",\"".addslashes($this->user_info["optional_field_4"])."\",
                    \"".addslashes($this->user_info["optional_field_5"])."\",\"".addslashes($this->user_info["optional_field_6"])."\",
                    \"".addslashes($this->user_info["optional_field_7"])."\",\"".addslashes($this->user_info["optional_field_8"])."\",
                    \"".addslashes($this->user_info["optional_field_9"])."\",\"".addslashes($this->user_info["optional_field_10"])."\",
                    \"".$this->registration_filter_id."\",
                    \"".addslashes($this->user_info["phoneext"])."\",
                    \"".addslashes($this->user_info["phoneext_2"])."\",
                    \"".addslashes($_SESSION['referrer'])."\",
                    \"".addslashes($_SERVER['HTTP_HOST'])."\"
                    )";
                break;
			case 2:
			case 3:
			case 4:
				$this->sql_query = "insert into geodesic_classifieds_userdata (id,username,email,newsletter,level,company_name,
					business_type,firstname,lastname,address,address_2,zip,city,state,country,phone,phone2,fax,url,date_joined,
					communication_type,rate_sum,rate_num,phoneext,phoneext_2,referrer,subdomain) values
					(".$this->user_id.",\"".$this->user_info["username"]."\",\"".$this->user_info["email"]."\",
					\"0\", 0,\"".addslashes($this->user_info["company_name"])."\",
					\"".$this->user_info["business_type"]."\",\"".addslashes($this->user_info["firstname"])."\",
					\"".addslashes($this->user_info["lastname"])."\",
					\"".addslashes($this->user_info["address"])."\",\"".addslashes($this->user_info["address_2"])."\",
					\"".addslashes($this->user_info["zip"])."\",
					\"".addslashes($this->user_info["city"])."\",\"".$this->user_info["state"]."\",
					\"".$this->user_info["country"]."\",
					\"".addslashes($this->user_info["phone"])."\",\"".addslashes($this->user_info["phone_2"])."\",
					\"".addslashes($this->user_info["fax"])."\",\"".addslashes($this->user_info["url"])."\",".time().",1,0,0
                    \"".addslashes($this->user_info["phoneext"])."\",
                    \"".addslashes($this->user_info["phoneext_2"])."\",
                    \"".addslashes($_SESSION['referrer'])."\",
                    \"".addslashes($_SERVER['HTTP_HOST'])."\"
                    )";
				break;
			case 5:
				$this->sql_query = "insert into geodesic_auctions_userdata (id,username,email,newsletter,level,company_name,
					business_type,firstname,lastname,address,address_2,zip,city,state,country,phone,phone2,fax,url,date_joined,
					communication_type,rate_sum,rate_num) values
					(".$this->user_id.",\"".$this->user_info["username"]."\",\"".$this->user_info["email"]."\",
					\"0\", 0,\"".addslashes($this->user_info["company_name"])."\",
					\"".$this->user_info["business_type"]."\",\"".addslashes($this->user_info["firstname"])."\",
					\"".addslashes($this->user_info["lastname"])."\",
					\"".addslashes($this->user_info["address"])."\",\"".addslashes($this->user_info["address_2"])."\",
					\"".addslashes($this->user_info["zip"])."\",
					\"".addslashes($this->user_info["city"])."\",\"".$this->user_info["state"]."\",
					\"".$this->user_info["country"]."\",
					\"".addslashes($this->user_info["phone"])."\",\"".addslashes($this->user_info["phone_2"])."\",
					\"".addslashes($this->user_info["fax"])."\",\"".addslashes($this->user_info["url"])."\",".time().",1,0,0)";
				break;
			case 6:
				$this->sql_query = "insert into geodesic_userdata (id,username,email,email2,newsletter,level,company_name,
					business_type,firstname,lastname,address,address_2,zip,city,state,country,phone,phone2,fax,url,date_joined,
					communication_type,optional_field_1,optional_field_2,optional_field_3,optional_field_4,
					optional_field_5,optional_field_6,optional_field_7,optional_field_8,optional_field_9,optional_field_10) values
					(".$this->user_id.",\"".$this->user_info["username"]."\",\"".$this->user_info["email"]."\",
					\"".$this->user_info["email2"]."\",
					\"0\", 0,\"".addslashes($this->user_info["company_name"])."\",
					\"".$this->user_info["business_type"]."\",\"".addslashes($this->user_info["firstname"])."\",
					\"".addslashes($this->user_info["lastname"])."\",
					\"".addslashes($this->user_info["address"])."\",\"".addslashes($this->user_info["address_2"])."\",
					\"".addslashes($this->user_info["zip"])."\",
					\"".addslashes($this->user_info["city"])."\",\"".$this->user_info["state"]."\",
					\"".$this->user_info["country"]."\",
					\"".addslashes($this->user_info["phone"])."\",\"".addslashes($this->user_info["phone_2"])."\",
					\"".addslashes($this->user_info["fax"])."\",\"".addslashes($this->user_info["url"])."\",".time().",1,
					\"".addslashes($this->user_info["optional_field_1"])."\",\"".addslashes($this->user_info["optional_field_2"])."\",
					\"".addslashes($this->user_info["optional_field_3"])."\",\"".addslashes($this->user_info["optional_field_4"])."\",
					\"".addslashes($this->user_info["optional_field_5"])."\",\"".addslashes($this->user_info["optional_field_6"])."\",
					\"".addslashes($this->user_info["optional_field_7"])."\",\"".addslashes($this->user_info["optional_field_8"])."\",
					\"".addslashes($this->user_info["optional_field_9"])."\",\"".addslashes($this->user_info["optional_field_10"])."\")";
				break;
			case 7:
				$this->sql_query = "insert into geocore_users (id,username,email,email2,newsletter,company_name,
					business_type,firstname,lastname,address,address_2,zip,city,state,country,phone,phone_2,phone_3,url,date_joined,
					optional_field_1,optional_field_2,optional_field_3,optional_field_4,
					optional_field_5,optional_field_6,optional_field_7,optional_field_8,optional_field_9,optional_field_10) values
					(".$this->user_id.",\"".$this->user_info["username"]."\",\"".$this->user_info["email"]."\",
					\"".$this->user_info["email2"]."\",
					\"0\",\"".addslashes($this->user_info["company_name"])."\",
					\"".$this->user_info["business_type"]."\",\"".addslashes($this->user_info["firstname"])."\",
					\"".addslashes($this->user_info["lastname"])."\",
					\"".addslashes($this->user_info["address"])."\",\"".addslashes($this->user_info["address_2"])."\",
					\"".addslashes($this->user_info["zip"])."\",
					\"".addslashes($this->user_info["city"])."\",\"".$this->user_info["state"]."\",
					\"".$this->user_info["country"]."\",
					\"".addslashes($this->user_info["phone"])."\",\"".addslashes($this->user_info["phone_2"])."\",
					\"".addslashes($this->user_info["fax"])."\",\"".addslashes($this->user_info["url"])."\",".time().",
					\"".addslashes($this->user_info["optional_field_1"])."\",\"".addslashes($this->user_info["optional_field_2"])."\",
					\"".addslashes($this->user_info["optional_field_3"])."\",\"".addslashes($this->user_info["optional_field_4"])."\",
					\"".addslashes($this->user_info["optional_field_5"])."\",\"".addslashes($this->user_info["optional_field_6"])."\",
					\"".addslashes($this->user_info["optional_field_7"])."\",\"".addslashes($this->user_info["optional_field_8"])."\",
					\"".addslashes($this->user_info["optional_field_9"])."\",\"".addslashes($this->user_info["optional_field_10"])."\")";
				break;
			case 8:
				//vBulletin
				//return here if everything is done in the other function call
				return true;
				break;
			case 9:
				//begin Phorum

				//end Phorum
				//return here if everything is done in the other function call
				return true;
				break;
			case 10:
				//begin Cerberus
				//return here if everything is done in the other function call
				return true;
				break;						
			case 11:
				//begin Jive
				//return here if everything is done in the other function call
				return true;
				break;					
				
			default:
				return false;
		}
		//insert login data into the login table
		$userdata_result = $this->installation_db->Execute($this->sql_query);
		if ($this->debug)
			echo $this->sql_query." in api<br>\n";
		if (!$userdata_result)
		{
			if ($this->debug)
			{
				echo $this->sql_query."<bR>\n";
				echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
			}
			$this->error_message = "API ERROR - INSERTING USERDATA IN REGISTRATION";
			$this->send_admin_error_email();
			return false;
		}
		return true;
	} //end of function insert_into_userdata_table

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_price_plan_expiration()
	{
		$expiration_date = (time() + ($this->price_plan->EXPIRATION_FROM_REGISTRATION * 84600));
		switch ($this->installation_info["installation_type"])
		{
			case 1:
			case 2:
				$this->sql_query = "insert into geodesic_classifieds_expirations
					(type,user_id,expires,type_id)
					values
					(2,".$this->user_id.",".$expiration_date.",".$this->user_price_plan.")";
				break;
			case 3:
				return true;
				break;
			case 4:
				return true;
				break;
			case 5:
				$this->sql_query = "insert into geodesic_auctions_expirations
					(type,user_id,expires,type_id)
					values
					(2,".$this->user_id.",".$expiration_date.",".$this->user_price_plan.")";
				break;
			case 6:
				$this->sql_query = "insert into geodesic_auctions_expirations
					(type,user_id,expires,type_id)
					values
					(2,".$this->user_id.",".$expiration_date.",".$this->user_price_plan.")";
				break;
			case 7:
				return true;
				break;
			case 8:
				//vBulletin
				//return here if everything is done in the other function call
				return true;
				break;
			case 9:
				//begin Phorum

				//end Phorum
				//return here if everything is done in the other function call
				return true;
				break;
			case 10:
				return true;
				break;
			case 11:
				return true;
				break;				
			default:
				return false;
		}


		$plan_expiration_result = $this->installation_db->Execute($this->sql_query);
		if ($this->debug)
			echo $this->sql_query." in api<br>\n";
		if (!$plan_expiration_result)
		{
			if ($this->debug)
			{
				echo $this->sql_query."<bR>\n";
				echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
			}
			$this->error_message = "API ERROR - INSERTING PRICE PLAN EXPIRATION FROM REGISTRATION";
			$this->send_admin_error_email();
			return false;
		}
		return true;
	} // end of function insert_price_plan_expiration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_into_user_groups_and_price_plans()
	{
		switch ($this->installation_info["installation_type"])
		{
			case 1:
				$this->sql_query = "insert into geodesic_user_groups_price_plans
					(id,group_id,price_plan_id)
					values
					(".$this->user_id.",".$this->user_group.",".$this->user_price_plan.")";
				break;
			case 2:
			case 3:
			case 4:
				$this->sql_query = "insert into geodesic_classifieds_user_groups_price_plans
					(id,group_id,price_plan_id)
					values
					(".$this->user_id.",".$this->user_group.",".$this->user_price_plan.")";
				break;
			case 5:
				$this->sql_query = "insert into geodesic_auctions_user_groups_price_plans
					(id,group_id,price_plan_id)
					values
					(".$this->user_id.",".$this->user_group.",".$this->user_price_plan.")";
				break;
			case 6:
				$this->sql_query = "insert into geodesic_user_groups_price_plans
					(id,group_id,price_plan_id)
					values
					(".$this->user_id.",".$this->user_group.",".$this->user_price_plan.")";
				break;
			case 7:
				return true;
				break;
			case 8:
				//vBulletin
				//return here if everything is done in the other function call
				return true;
				break;
			case 9:
				//begin Phorum

				//end Phorum
				//return here if everything is done in the other function call
				return true;
				break;
			case 10:
				return true;
				break;
			case 11:
				return true;
				break;				
			default:
				return false;
		}
		if ($this->debug)
			echo $this->sql_query." in api<br>\n";
		$group_result = $this->installation_db->Execute($this->sql_query);
		if (!$group_result)
		{
			if ($this->debug)
			{
				echo $this->sql_query."<bR>\n";
				echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
			}
			$this->error_message = "API ERROR - INSERTING GROUP/PRICE PLAN INFO IN REGISTRATION";
			$this->send_admin_error_email();
			return false;
		}
		return true;
	} // end of function insert_into_user_groups_and_price_plans

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_credits_upon_registration()
	{
		if ($this->price_plan->CREDITS_EXPIRE_TYPE == 1)
		{
			//expire on fixed days from registration
			$expiration = (($this->price_plan->CREDITS_EXPIRE_PERIOD * 86400) + time());
		}
		elseif ($this->price_plan->CREDITS_EXPIRE_TYPE == 2)
		{
			//expire on fixed date
			$expiration = $this->price_plan->CREDITS_EXPIRE_DATE;
		}

		switch ($this->installation_info["installation_type"])
		{
			case 1: //enterprise classifieds
				$this->sql_query = "insert into geodesic_classifieds_user_credits
					(user_id,credit_count,credits_expire)
					values
					(".$this->user_id.",".$this->price_plan->CREDITS_UPON_REGISTRATION.",".$expiration.")";
			case 2: //premier classifieds
				$this->sql_query = "insert into geodesic_classifieds_user_credits
					(user_id,credit_count,credits_expire)
					values
					(".$this->user_id.",".$this->price_plan->CREDITS_UPON_REGISTRATION.",".$expiration.")";

				break;
			case 3: // full classifieds
				return true;
				break;
			case 4: //basic classifieds
				return true;
				break;
			case 5:
				$this->sql_query = "insert into geodesic_auctions_user_credits
					(user_id,credit_count,credits_expire)
					values
					(".$this->user_id.",".$this->price_plan->CREDITS_UPON_REGISTRATION.",".$expiration.")";

				break;
			case 6:
				$this->sql_query = "insert into geodesic_auction_user_credits
					(user_id,credit_count,credits_expire)
					values
					(".$this->user_id.",".$this->price_plan->CREDITS_UPON_REGISTRATION.",".$expiration.")";

				break;
			case 7:
				return true;
				break;
			case 8:
				//vBulletin
				//return here if everything is done in the other function call
				return true;
				break;
			case 9:
				//begin Phorum

				//end Phorum
				//return here if everything is done in the other function call
				return true;
				break;
			case 10:
				return true;
				break;
			case 11:
				return true;
				break;				
			default:
				return false;
		}
		if ($this->debug)
			echo $this->sql_query." in api<br>\n";
		$free_credits_result = $this->installation_db->Execute($this->sql_query);
		if (!$free_credits_result)
		{
			if ($this->debug)
			{
				echo $this->sql_query."<bR>\n";
				echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
			}
			$this->error_message = "API ERROR - INSERTING CREDITS IN REGISTRATION";
			$this->send_admin_error_email();
			return false;
		}
		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_free_subscription_period()
	{
		$expiration = (($this->price_plan->FREE_SUBSCRIPTION_PERIOD_UPON_REGISTRATION * 86400) + time());

		switch ($this->installation_info["installation_type"])
		{
			case 1:
				$this->sql_query = "insert into geodesic_classifieds_user_subscriptions
					(user_id,subscription_expire)
					values
					(".$this->user_id.",".$expiration.")";

				break;
			case 2:
				$this->sql_query = "insert into geodesic_classifieds_user_subscriptions
					(user_id,subscription_expire)
					values
					(".$this->user_id.",".$expiration.")";

				break;
			case 3:
				return true;
				break;
			case 4:
				return true;
				break;
			case 5:
				$this->sql_query = "insert into geodesic_auctions_user_subscriptions
					(user_id,subscription_expire)
					values
					(".$this->user_id.",".$expiration.")";

				break;
			case 6:
				$this->sql_query = "insert into geodesic_auctions_user_subscriptions
					(user_id,subscription_expire)
					values
					(".$this->user_id.",".$expiration.")";

				break;
			case 7:
				return true;
				break;
			case 8:
				//vBulletin
				//return here if everything is done in the other function call
				return true;
				break;
			case 9:
				//begin Phorum

				//end Phorum
				//return here if everything is done in the other function call
				return true;
				break;
			case 10:
				return true;
				break;
			case 11:
				return true;
				break;				
			default:
				return false;
		}
		if ($this->debug)
			echo $this->sql_query." in api<br>\n";
		$free_subscription_result = $this->installation_db->Execute($this->sql_query);
		if (!$free_subscription_result)
		{
			if ($this->debug)
			{
				echo $this->sql_query."<bR>\n";
				echo $this->installation_db->ErrorMsg()." is the error<bR>\n";
			}
			$this->error_message = "API ERROR - INSERTING FREE SUBSCRIPTION IN REGISTRATION";
			$this->send_admin_error_email();
			return false;
		}
		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function vbulletin_bitwise($value, $bitfield)
	{
		// Do not change this to return true/false!
		return $this->iif(intval($value) & $bitfield, 1, 0);
	} //end of function vbulletin_bitwise

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function iif($expression, $returntrue, $returnfalse = '')
	{
		return ($expression ? $returntrue : $returnfalse);
	} //end of function iif

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function vbulletin_convert_array_to_bits(&$arry, $_FIELDNAMES, $unset = 0)
	{
		$bits = 0;
		foreach($_FIELDNAMES AS $fieldname => $bitvalue)
		{
			if ($arry["$fieldname"] == 1)
			{
				$bits += $bitvalue;
			}
			if ($unset)
			{
				unset($arry["$fieldname"]);
			}
		}
		return $bits;
	} //end of vbulletin_convert_array_to_bits

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class API_Register
?>