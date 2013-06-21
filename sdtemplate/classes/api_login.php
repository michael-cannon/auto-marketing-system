<? //api_login.php
include_once("classes/api_site_class.php");

class API_login extends Api_site
{
	var $login_return;
	var $user_level;
	var $current_session;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function API_login ($user_info=0)
	{
		$this->Api_site();

		$this->user_info = $user_info;
		//echo $this->debug." is debug in api login<br>\n";
	} // end of function API_login

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function api_log_user_in()
	{
		//check data
		//get installation(s) info

		$this->get_installation_info();

		//foreach ($this->installations as $this->installation_info)
		while ($this->installation_info = $this->installations->FetchRow())
		{
			if ($this->check_latent_installation())
			{
				//this is not the invoking installation
				//connect to the installation database
				if ($this->connect_to_installation_db())
				{
					if ($this->debug) echo "hello from before get_login_info function<Br>\n";
					if($this->get_login_info())
					{
						if ($this->login_return->STATUS == 1)
						{
							if ($this->debug) echo "login_return->status is 1<Br>\n";
							if ($this->check_if_logged_in())
							{
								if ($this->debug) echo "user is already logged into installation...update_session<Br>\n";
								//logged in
								$this->update_session();
							}
							else
							{
								//not logged in
								if ($this->debug) echo "user is NOT already logged into installation...get_user_level<Br>\n";
								if ($this->get_user_level())
								{
									if ($this->debug) echo "user is NOT already logged into installation...insert_session<Br>\n";
									$this->insert_session();
								}
							}
						}
					}
				}
			} //end of if($this->check_latent_installation())
		} //end of foreach
		$this->reconnect_to_latent_installation_db();
	} //end of function api_log_user_in

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_login_info()
	{
		switch ($this->installation_info["installation_type"])
		{
			case 1: //enterprise classifieds
				$this->sql_query = "select id,status,password from geodesic_logins where username = \"".$this->user_info["username"]."\"";
				break;			
			case 2: //premier classifieds
			case 3: //full classifieds
			case 4: //basic classifieds
				$this->sql_query = "select id,status,password from geodesic_classifieds_logins where username = \"".$this->user_info["username"]."\"";
				break;
			case 5: //premier auctions
				$this->sql_query = "select id,status,password from geodesic_auctions_logins where username = \"".$this->user_info["username"]."\"";
				break;
			case 6: //enterprise auctions
				$this->sql_query = "select id,status,password from geodesic_logins where username = \"".$this->user_info["username"]."\"";
				break;
			case 7: //GeoCore
				$this->sql_query = "select id,status,password from geocore_users where username = \"".$this->user_info["username"]."\"";
				break;
			case 8: //vBulletin
				//do all vbulletin login procedures at once here
				include ($this->installation_info["vbulletin_config_path"]);
				if ($this->debug)
				{
					echo "Hello from get_login_info for vBulletin<bR>\n";
					echo $tableprefix." is tableprefix<Br>\n";
				}
				
				if (isset($config['Database']['tableprefix']))
				{
					//this changed in some version...started at least in 3.5.2
					$tableprefix = $config['Database']['tableprefix'];
				}				
				
				//$this->get_vBulletin_vboptions($tableprefix);
				$current_time = time();
				if ($this->get_vBulletin_vboptions($tableprefix))
				{
					$this->sql_query = "SELECT userid, usergroupid, membergroupids, username, password, salt FROM " . $tableprefix . "user
						WHERE username = '" . addslashes($this->vbulletin_htmlspecialchars_uni($this->user_info["username"])) . "'";
					$userdata_result = $this->installation_db->Execute($this->sql_query);
					if ($this->debug)
						echo $this->sql_query."<br>\n";
					if (!$userdata_result)
					{
						$this->error_message = "API ERROR - LOGGING INTO VBULLETIN INSTALLATION\nGETTING USERDATA USING THE USERNAME\n\n
							USING THE FOLLOWING USERNAME\n\n".$this->user_info["username"]."\n\n";
						if ($this->debug)
							echo $this->sql_query."<bR>\n";
						$this->send_admin_error_email();
						return false;
					}
					elseif ($userdata_result->RecordCount() == 1)
					{
						//check password
						$bbuserinfo = $userdata_result->FetchNextObject();
						$encrypted_password = md5(md5($this->user_info["password"]).$bbuserinfo->SALT);
						if ($this->debug)
						{
							echo $bbuserinfo->SALT." is the salt<br>\n";
							echo "vbulletin password: ".$bbuserinfo->PASSWORD."<br>";
							if ($bbuserinfo->PASSWORD != $encrypted_password)
								echo "did not match";
							else
								echo "DID match";
							echo "<bR>entered password: ".$this->user_info["password"]." - ".$encrypted_password."<br>\n";
						}
						if ($bbuserinfo->PASSWORD != $encrypted_password)
						{
							//password does not match
							return false;
						}
						else
						{
							//password matches
							if ($this->debug)
								echo "password matched<Br>\n";
							$this->vbsetcookie('userid', $bbuserinfo->USERID ,1,$cookieprefix);
							$cookie_password = md5($bbuserinfo->PASSWORD.$this->installation_info["vbulletin_license_key"]);
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

							$this->sql_query = "DELETE FROM " . $tableprefix . "session WHERE sessionhash = '" . addslashes($session['dbsessionhash']) . "'";
							$delete_session_result = $this->installation_db->Execute($this->sql_query);
							if ($this->debug)
								echo $this->sql_query."<br>\n";
							if (!$delete_session_result)
							{
								if ($this->debug)
									echo $this->sql_query." has an error<br>\n";
								$this->error_message = "API ERROR - LOGGING INTO VBULLETIN INSTALLATION\n
									USING THE FOLLOWING USERNAME\n\n".$this->user_info["username"]."\nAND
									DELETING OLD SESSIONS\n\n".$this->sql_query;
								if ($this->debug)
									echo $this->sql_query."<bR>\n";
								$this->send_admin_error_email();
								return false;
							}

							$this->sql_query = "INSERT INTO " . $tableprefix . "session
									(sessionhash, userid, host, idhash, lastactivity, styleid, loggedin, bypass, useragent)
								VALUES
									('" . addslashes($session['sessionhash']) . "', " . intval($bbuserinfo->USERID) . ", '" . addslashes($session_host) . "', '" . addslashes($session_idhash) . "', " . $current_time . ", ".$this->vboptions["styleid"].", 1, 0, '" . addslashes($_SERVER["HTTP_USER_AGENT"]) . "')";
							$insert_session_result = $this->installation_db->Execute($this->sql_query);
							if ($this->debug)
								echo $this->sql_query."<br>\n";
							if (!$insert_session_result)
							{
								if ($this->debug)
									echo $this->sql_query." has an error<br>\n";
								$this->error_message = "API ERROR - LOGGING INTO VBULLETIN INSTALLATION\n
									USING THE FOLLOWING USERNAME\n\n".$this->user_info["username"]."\nAND
									INSERTING NEW SESSIONS\n\n".$this->sql_query;
								$this->send_admin_error_email();
							}
							$this->vbsetcookie('sessionhash', $session['sessionhash'], 0,$cookieprefix);
						}

					}
					else
					{
						// invalid username entered
						return false;
					}

				}
				else
				{
					//can't get vboptions data
					//things went bad
					$this->error_message = "API ERROR - GETTING VBULLTIN INSTALLATION INFORMATION TO COMPLETE LOGIN\n
						USING THE FOLLOWING QUERY\n\n".$this->sql_query."\n\n";
					if ($this->debug)
						echo $this->sql_query."<bR>\n";
					$this->send_admin_error_email();
					return false;
				}
				return true;
				break;
			case 9:
				//begin Phorum
				//you can do everything needed for login within this function or break it across several function calls
				//Phorum is doing everything within this function
				if ($this->debug)
					echo "Hello from get_login_info for Phorum<bR>\n";
				$md5_pass = md5($this->user_info["password"]);
				
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
					$this->phorum_id_field = "id";
					$this->phorum_cookie = "phorum_cookieauth";
					$this->sql_query = "select id from ".$this->installation_info["phorum_database_table_prefix"].$this->phorum_auth_table."
						where username = \"".$this->user_info["username"]."\" and password = \"".$md5_pass."\"";					
				}
				else
				{
					$this->phorum_auth_table = "_users";
					$this->phorum_id_field = "user_id";
					$this->phorum_cookie = "phorum_session_v5";
					$this->sql_query = "select user_id from ".$this->installation_info["phorum_database_table_prefix"].$this->phorum_auth_table."
						where username = \"".$this->user_info["username"]."\" and password = \"".$md5_pass."\"";					
				}				
				
				$user_result = $this->installation_db->Execute($this->sql_query);
				if ($this->debug)
					echo $this->sql_query."<bR>\n";
				if ($user_result)
				{
					if ($user_result->RecordCount() == 1)
					{
						if ($this->debug)
							echo "<bR>\n";
						$ph_sessionID=$this->user_info["username"].":".$md5_pass;
						if (strlen(trim($this->installation_info["cookie_path"])) > 0)
							$cookie_path = $this->installation_info["cookie_path"];
						else
							$cookie_path = "/";
						if ($this->debug)
						{
							echo "setting phorum cookie (phorum_cookieauth) with value of ".$ph_sessionID." and cookie path of ".$cookie_path."<bR>\n";
						}
						SetCookie($this->phorum_cookie, $ph_sessionID , time()+(86400*365), $cookie_path,$this->installation_info["cookie_domain"]);
						if ($this->debug_cookie)
						{
							echo $this->sql_query."<bR>\n";
							echo $ph_sessionID." is the ph_sessionID<BR>\n";
							echo $cookie_path." is cookie_path<Br>\n";
							echo time()." is current time and ".time()+(86400*365)." is when this cookie expires<bR>\n";
						}
						return true;
					}
					elseif ($user_result->RecordCount() == 0)
					{
						return false;
					}
					else
					{
						$this->error_message = "ERROR - GETTING ID FROM PHORUM INSTALLATION FOR LOGIN PURPOSES\n
							WITH THE FOLLOWING USERNAME: ".$this->user_info["username"]."\n
							password: ".$this->user_info["password"]." -md5 password ".$md5_pass."\n\n
							MORE THAN ONE ACCOUNT MATCHED THE LOGIN DATA...POSSIBLY CORRUPTED DATABASE TABLE????".$this->sql_query;
						if ($this->debug)
							echo $this->sql_query." - produced error<bR>\n";
						$this->send_admin_error_email();
						return false;
					}
				}
				else
				{
					$this->error_message = "API ERROR - GETTING ID FROM PHORUM INSTALLATION FOR LOGIN PURPOSES\n
						WITH THE FOLLOWING USERNAME: ".$this->user_info["username"]."\n
						password: ".$this->user_info["password"]." -md5 password ".$md5_pass."\n\n".$this->sql_query;
					if ($this->debug)
						echo $this->sql_query." - produced error<bR>\n";
					$this->send_admin_error_email();
					return false;
				}
				break;
				//if user does not exist in Phorum
				//the user must be added
				//end Phorum
				
			case 10:
				// Cerberus Login
				require_once ($this->installation_info["cerberus_directory_path"]."config.php");
				require_once ($this->installation_info["cerberus_directory_path"]."session.php");
				require_once($this->installation_info["cerberus_publicgui_path"]."cer_PublicGUISettings.class.php");
				$pubgui = new cer_PublicGUISettings(PROFILE_ID);

				$cer_session->doLogin($this->user_info["email"],$this->user_info["password"]);
				$_SESSION["cer_login_serialized"] = serialize($cer_session);
				return true;
				break;	

			case 11:
				//jive
				//no code
				return true;
				break;		
				
			default:
				return false;
		}

		$get_login_result = $this->installation_db->Execute($this->sql_query);
		if ($this->debug)
			echo $this->sql_query." - just run<bR>\n";

		if (!$get_login_result)
		{
			$this->error_message = "API ERROR - GETTING ID FROM GEODESIC INSTALLATION FOR LOGIN PURPOSES\n
				WITH THE FOLLOWING USERNAME: ".$this->user_info["username"]."\n
				password: ".$this->user_info["password"]."\n\n".$this->sql_query;
			if ($this->debug)
				echo $this->sql_query." - produced error<bR>\n";
			$this->send_admin_error_email();
			return false;
		}
		elseif ($get_login_result->RecordCount() == 1)
		{
			$this->login_return = $get_login_result->FetchNextObject();
			if ($this->debug)
			{
				echo $this->login_return->ID." is id<bR>\n";
				echo $this->login_return->STATUS." is status<bR>\n";
				echo $this->login_return->PASSWORD." is password<bR>\n";
				
			}
			return true;
		}
		else
			return false;

	} //end of function get_login_info

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_user_level()
	{
		switch ($this->installation_info["installation_type"])
		{
			case 1:
				$this->sql_query = "select level,email,firstname,lastname from geodesic_userdata where id = ".$this->login_return->ID;
				break;			
			case 2:
			case 3:
			case 4:
				$this->sql_query = "select level,email,firstname,lastname from geodesic_classifieds_userdata where id = ".$this->login_return->ID;
				break;	
			case 5:
				$this->sql_query = "select level,email,firstname,lastname from geodesic_auctions_userdata where id = ".$this->login_return->ID;
				break;
			case 6:
				$this->sql_query = "select level,email,firstname,lastname from geodesic_userdata where id = ".$this->login_return->ID;
				break;
			case 7:
				$this->sql_query = "select email,firstname,lastname from geocore_users where id = ".$this->login_return->ID;
				break;
			case 8: //vBulletin

				return true;
			case 9:
				//begin Phorum

				//end Phorum
				//return here if everything is done in the other function call
				return true;
				break;
			case 10:
				//begin Cerberus

				//end Cerberus
				//return here if everything is done in the other function call
				return true;
				break;				
			case 11:
				//jive
				//no code
				return true;
				break;					
			default:
				return false;
		}

		if ($this->debug)
			echo $this->sql_query."<br>\n";
		$level_result = $this->installation_db->Execute($this->sql_query);
		if (!$level_result)
		{
			if ($this->debug)
				echo $this->sql_query." - has an error<bR>\n";
			$this->user_level = 0;
			return false;
		}
		elseif (($level_result->RecordCount() == 0) || ($level_result->RecordCount() > 1))
		{
			if ($this->debug)
				echo $this->sql_query." - returned either 0 or too many in get_user_level<bR>\n";
			$this->user_level = 0;
			return false;
		}
		else
		{
			if ($this->debug)
				echo "user level has been returned<bR>\n";
			$show_level = $level_result->FetchNextObject();
			$this->user_level = $show_level->LEVEL;
			return true;
		}
	} //end of function get_user_level

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_session()
	{
		//update the time for the session found
		if ($this->debug) echo "<BR>TOP OF UPDATE_SESSION<br>";
		switch ($this->installation_info["installation_type"])
		{
			case 1:
			case 2:
			case 3:
			case 4:
				$this->sql_query = "update geodesic_sessions set last_time = ".time()." where user_id = ".$this->login_return->ID;
				break;
			case 5:
				$this->sql_query = "update geodesic_sessions set last_time = ".time()." where user_id = ".$this->login_return->ID;
				break;
			case 6:
				$this->sql_query = "update geodesic_sessions set last_time = ".time()." where user_id = ".$this->login_return->ID;
				break;
			case 7:
				$this->sql_query = "update geocore_sessions set last_time = ".time()." where user_id = ".$this->login_return->ID;
				break;
			case 8://vBulletin
				//do all vbulletin login procedures at once here
				return false;
				break;
			case 9:
				//begin Phorum
				//end Phorum
				//return here if everything is done in the other function call
				return false;
				break;
			case 10:
				//begin Cerberus
				//end Cerberus
				//return here if everything is done in the other function call
				return false;
				break;				
			case 11:
				//jive
				//no code
				return true;
				break;					
			default:
				return false;
		}
		$update_session_time_result = $this->installation_db->Execute($this->sql_query);	
		if ($this->debug)
		{
			echo $this->sql_query." is the query<br>\n";
		}
		if (!$update_session_time_result)
		{
			if ($this->debug) echo $this->sql_query." is the query<br>\n";
			return false;
		}	
		
		if ($this->debug) echo "<BR>END OF UPDATE_SESSION<br><BR>";
		return true;

		/*
		replaced this code with that above
		this will be removed later
		$this->sql_query = "update geodesic_sessions set last_time = ".time()." where classified_session = \"".$this->current_session."\"";
		$update_session_result = $this->installation_db->Execute($this->sql_query);
		if ($this->debug)
			echo $this->sql_query." is the query<br>\n";
		if (!$update_session_result)
		{
			if ($this->debug)
				echo $this->sql_query." has an error<br>\n";
			return false;
		}
		*/
	} //end of function update_session

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_if_logged_in()
	{
		if ($thise->debug) echo $this->installation_info["installation_type"]." is installation_type in check_if_logged_in<br>\n";
		//get other user_id within this installation within get_login_info function
		//check to see if this user is already logged into the current installation using user id returned from get_login_info function
		switch ($this->installation_info["installation_type"])
		{
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
			case 6:
				$this->sql_query = "select * from geodesic_sessions where user_id = ".$this->login_return->ID;
				break;
			case 7:
				$this->sql_query = "select * from geocore_sessions where user_id = ".$this->login_return->ID;
				break;

			case 8://vBulletin
				//do all vbulletin login procedures at once above
				return false;
				break;
			case 9://Phorum
				//do all Phorum login procedures at once above
				return false;
				break;
			case 10://Cerberus
				//do all Cerberus login procedures at once above
				return false;
				break;			
			case 11:
				//jive
				//no code
				return false;
				break;						
			default:
				$this->sql_query = "select * from geodesic_sessions where user_id = ".$this->login_return->ID;
				break;
		}
		$check_login_result = $this->installation_db->Execute($this->sql_query);
		if ($this->debug)
			echo  $this->sql_query." is the query<bR>\n";
		if (!$check_login_result)
		{
			if ($this->debug)
			{
				echo $this->installation_db->ErrorMsg()." is the database error<br>\n";
				echo  $this->sql_query." has an error<bR>\n";
			}
			$this->auth_messages["login"] = $this->messages[341];
			return false;
		}
		elseif ($check_login_result->RecordCount() == 1)
		{
			if ($this->debug) echo "this user was already logged into this installation<BR>\n";
			$session_result = $check_login_result->FetchNextObject();
			
			if ($this->debug)
			{
				echo $this->current_session." is the this->current_session<br>\n";
			}			
			return true;
		}
		else
		{
			if ($this->debug) echo "this user was not currently logged into this installation<bR>\n";
			return false;
		}

	} //end of function check_if_logged_in

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function api_user_logout()
	{
		if ($this->debug) echo "hello from api_user_logout<br>\n";
		$this->get_installation_info();
		while ($this->installation_info = $this->installations->FetchRow())
		{
			if ($this->check_latent_installation())
			{
				//this is not the invoking installation
				//connect to the installation database
				if ($this->connect_to_installation_db())
				{
					if ($this->debug) echo "hello from before logout call<br>\n";
					$this->logout();
				}
			}
		}



	} //end of function logout

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function logout()
	{
		if ($this->debug) echo "hello from logout to - ".$this->installation_info["installation_type"]."<br>\n";
		
		$this->get_login_info();
		if ($this->debug)
		{
			echo $this->login_return->ID." is id in logout<bR>\n";
			echo $this->login_return->STATUS." is status in logout<bR>\n";
			echo $this->login_return->PASSWORD." is password in logout<bR>\n";
		}		
		switch ($this->installation_info["installation_type"])
		{

			case 1: //enterprise classifieds
			case 2: //premier classifieds
			case 3: //full classifieds
			case 4: //basic classifieds
				//$cookie_url = str_replace("classifieds.php", "",$this->installation_configuration_data->CLASSIFIEDS_URL);
				//setcookie("classified_session",$custom_id,time(),"/");
				//header("Set-Cookie: classified_session=".$custom_id."; path=/; domain=".$cookie_url."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
				$this->sql_query = "delete from geodesic_sessions where classified_session = \"".$_COOKIE["classified_session"]."\"";
				$delete_session_result = $this->installation_db->Execute($this->sql_query);
				
				$this->sql_query = "delete from geodesic_classifieds_sell_session where session = \"".$_COOKIE["classified_session"]."\"";
				$delete_session_result = $this->installation_db->Execute($this->sql_query);
				
				$this->sql_query = "delete from geodesic_classifieds_sell_session_questions where session = \"".$_COOKIE["classified_session"]."\"";
				$delete_session_result = $this->installation_db->Execute($this->sql_query);
				
				$this->sql_query = "delete from geodesic_classifieds_sell_session_images where session = \"".$_COOKIE["classified_session"]."\"";
				$delete_session_result = $this->installation_db->Execute($this->sql_query);
				
				$this->sql_query = "delete from geodesic_sessions where user_id = ".$this->login_return->ID;
				$delete_session_result = $this->installation_db->Execute($this->sql_query);
				
				break;
			case 5: //premier auctions
				//$cookie_url = str_replace("auctions.php", "",$this->installation_configuration_data->AUCTIONS_URL);
				//setcookie("auction_session",$custom_id,time(),"/");
				//header("Set-Cookie: auction_session=".$custom_id."; path=/; domain=".$cookie_url."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
				//break;
			case 6: //enterprise auctions
				//$cookie_url = str_replace("auctions.php", "",$this->installation_configuration_data->AUCTIONS_URL);
				//setcookie("auction_session",$custom_id,time(),"/");
				//header("Set-Cookie: auction_session=".$custom_id."; path=/; domain=".$cookie_url."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
				$this->sql_query = "delete from geodesic_sessions where classified_session = \"".$_COOKIE["auction_session"]."\"";
				$delete_session_result = $this->installation_db->Execute($this->sql_query);
				
				$this->sql_query = "delete from geodesic_auctions_sell_session where session = \"".$_COOKIE["auction_session"]."\"";
				$delete_session_result = $this->installation_db->Execute($this->sql_query);
				
				$this->sql_query = "delete from geodesic_auctions_sell_session_questions where session = \"".$_COOKIE["auction_session"]."\"";
				$delete_session_result = $this->installation_db->Execute($this->sql_query);
				
				$this->sql_query = "delete from geodesic_auctions_sell_session_images where session = \"".$_COOKIE["auction_session"]."\"";
				$delete_session_result = $this->installation_db->Execute($this->sql_query);
				
				$this->sql_query = "delete from geodesic_sessions where user_id = ".$this->login_return->ID;
				$delete_session_result = $this->installation_db->Execute($this->sql_query);
				
				break;
			case 7: //geocore
				//$cookie_url = str_replace("auctions.php", "",$this->installation_configuration_data->AUCTIONS_URL);
				//header("Set-Cookie: geocore_session=".$custom_id."; path=/; domain=".$cookie_url."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
				$this->sql_query = "delete from geodesic_sessions where user_id = ".$this->login_return->ID;
				return true;
				break;
			case 8://vBulletin
				//do all vbulletin login procedures at once here
				// clear all cookies beginning with COOKIE_PREFIX
				include ($this->installation_info["vbulletin_config_path"]);
				
				if (isset($config['Database']['tableprefix']))
				{
					//this changed in some version...started at least in 3.5.2
					$tableprefix = $config['Database']['tableprefix'];
				}				
				
				$this->get_vBulletin_vboptions($tableprefix);
				$prefix_length = strlen($cookieprefix);
				foreach ($_COOKIE AS $key => $val)
				{
					if ($this->debug) echo $key." is ".$val." of _COOKIE trying to remove<bR>\n";
					$index = strpos($key, $cookieprefix);
					if ($index == 0 AND $index !== false)
					{
						$key = substr($key, $prefix_length);
						if (trim($key) == '')
						{
							continue;
						}
						$this->vbsetcookie($key, '', 1,$cookieprefix);
					}
				}
				$cookie_name = $cookieprefix."userid";
				if ($this->debug) echo $cookie_name." is cookie_name<BR>\n";
				$time = time();
				$this->sql_query = "UPDATE " . $tableprefix . "user
					SET lastactivity = " . ($time - $this->vboptions['cookietimeout']) . ",
					lastvisit = " . $time . "
					WHERE userid = ".$_COOKIE[$cookie_name];
				$update_user_session_result = $this->installation_db->Execute($this->sql_query);
				if ($this->debug) echo $this->sql_query."<br>\n";
				if (!$update_user_session_result)
				{
					if ($this->debug) echo $this->sql_query." has an error<br>\n";
				}
				// make sure any other of this user's sessions are deleted (in case they ended up with more than one)
				$this->sql_query = "DELETE FROM " . $tableprefix . "session WHERE userid = ".$_COOKIE[$cookie_name];
				$delete_user_session_result = $this->installation_db->Execute($this->sql_query);
				if ($this->debug) echo $this->sql_query."<br>\n";
				if (!$delete_user_session_result)
				{
					if ($this->debug) echo $this->sql_query." has an error<br>\n";
				}


				$this->sql_query = "DELETE FROM " . $tableprefix . "session WHERE sessionhash = \"" . addslashes($_COOKIE["sessionhash"])."\"";
				$delete_sessionhash_result = $this->installation_db->Execute($this->sql_query);
				if ($this->debug) echo $this->sql_query."<br>\n";
				if (!$delete_sessionhash_result)
				{
					if ($this->debug) echo $this->sql_query." has an error<br>\n";
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
				$session['sessionhash'] = md5($time . $scriptpath . $session_idhash . $session_host . $random_seed);

				$session['dbsessionhash'] = $session['sessionhash'];
				$this->sql_query = "INSERT INTO ".$tableprefix."session
						(sessionhash, userid, host, idhash, lastactivity, styleid, useragent)
					VALUES
						('" . addslashes($session['sessionhash']) . "', 0, '" . addslashes($session['host']) . "', '" . addslashes($session['idhash']) . "', " . time() . ", 0, '" . addslashes($_SERVER["HTTP_USER_AGENT"]) . "')";
				$delete_session_result = $this->installation_db->Execute($this->sql_query);
				if ($this->debug) echo $this->sql_query."<br>\n";
				if (!$delete_session_result)
				{
					if ($this->debug) echo $this->sql_query." has an error<br>\n";
				}
				//$this->vbsetcookie('userid',0,2,$cookieprefix);
				//$this->vbsetcookie('password', 0,2,$cookieprefix);
				//$this->vbsetcookie('sessionhash', $session['sessionhash'], 2,$cookieprefix);
				return true;
				break;
			case 9:
				//begin Phorum
				if ($_COOKIE["phorum_cookieauth"])
				{
					list($user, $pass)=explode(":", urldecode($_COOKIE['phorum_cookieauth']));
					if(!get_magic_quotes_gpc())
						$user=addslashes($user);
					$this->sql_query = "select * from ".$this->installation_info["phorum_database_table_prefix"]."_auth where
						username=\"".$user."\" and password= \"".$pass."\"";
					if ($this->debug) echo $this->sql_query."<br>\n";
					$userdata_result = $this->installation_db->Execute($this->sql_query);
					if (!$userdata_result)
					{
						if ($this->debug) echo $this->sql_query." has an error<br>\n";
					}
					if ($this->debug) echo $userdata_result->RecordCount()." is count<BR>\n";

					if (($userdata_result) && ($userdata_result->RecordCount() == 1))
					{
						$phorum_user =$userdata_result->FetchNextObject();
						$this->sql_query = " update ".$this->installation_info["phorum_database_table_prefix"]."_auth set
							combined_token = '' where id=".$phorum_user->ID;
						if ($this->debug) echo $this->sql_query."<br>\n";
						$delete_session_result = $this->installation_db->Execute($this->sql_query);
						if (!$delete_session_result)
						{
							if ($this->debug) echo $this->sql_query." has an error<br>\n";
						}
						//if (strlen(trim($this->installation_info["cookie_path"])) > 0)
						//	$cookie_path = $this->installation_info["cookie_path"];
						//else
							$cookie_path = "/";
						SetCookie("phorum_cookieauth",'',0,$cookie_path);
						if ($this->debug_cookie)
						{
							echo $_COOKIE['phorum_cookieauth']." is cookie['phorum_cookieauth'] before unset<bR>\n";
							echo $this->sql_query."<br>\n";
							echo "phorum cookies cleared<BR>\n";
							echo "phorum_cookieauth is the cookie cleared for the ".$cookie_path." cookie path<bR>\n";
							echo $_COOKIE['phorum_cookieauth']." is the value or cookie-phorum_cookieauth<BR>\n";
						}
						unset($_COOKIE['phorum_cookieauth']);
						return true;
					}
					else
						return false;
				}
				else
				{
					if ($_COOKIE["phorum_session_v5"])
					{ 
						 $cookie_path = "/";
						 SetCookie("phorum_session_v5",'',0,$cookie_path);
						 return true;
					}
					else
					{ 					
						if ($this->debug) echo "the phorum_cookieauth does not exist<Br>\n";
						return false;
					}
				}

				//end Phorum
				//return here if everything is done in the other function call
				return true;
				break;
				
			case 10:
				// Cerberus Logout
				require_once ($this->installation_info["cerberus_directory_path"]."config.php");
				require_once ($this->installation_info["cerberus_directory_path"]."session.php");
				require_once($this->installation_info["cerberus_publicgui_path"]."cer_PublicGUISettings.class.php");
				$pubgui = new cer_PublicGUISettings(PROFILE_ID);

				unset($_COOKIE[session_name()]);
				session_unset();
				session_destroy();

				break;				
			case 11:
				//jive
				//no code
				break;					
			default:
				return false;
		}
		return true;

	} //end of function logout

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function create_new_session()
	{
		if ($this->debug) echo "<BR>TOP OF CREATE_NEW_SESSION<Br>\n";
		do {
			$custom_id = md5(uniqid(rand(),1));
			$custom_id = substr( $custom_id, 0,32);
			switch ($this->installation_info["installation_type"])
			{
				case 1:
				case 2:
				case 3:
				case 4:
					$this->sql_query = "SELECT classified_session FROM geodesic_sessions WHERE classified_session = \"".$custom_id."\"";
					break;
				case 5:
				case 6:
					$this->sql_query = "SELECT auction_session FROM geodesic_sessions WHERE auction_session = \"".$custom_id."\"";
					break;
				case 7:
					$this->sql_query = "SELECT geocore_session FROM geocore_sessions WHERE geocore_session = \"".$custom_id."\"";
					break;
				case 8://vBulletin
					//do all vbulletin login procedures at once here
					return true;
					break;
				case 9:
					//begin Phorum
					//end Phorum
					//return here if everything is done in the other function call
					return true;
					break;
				case 10:
					//Cerberus
					//no code
					return true;
					break;						
				case 11:
					//jive
					//no code
					return true;
					break;						
					
				default:
					return false;
			}
			$custom_id_result = $this->installation_db->Execute($this->sql_query);
			if ($this->debug) echo  $this->sql_query." is the query<bR>\n";
			if (!$custom_id_result)
			{
				if ($this->debug) echo $this->sql_query." has an error<br>\n";
				return false;
			}
		} while ($custom_id_result->RecordCount() > 0);
		switch ($this->installation_info["installation_type"])
		{
			case 1:
			case 2:
			case 3:
			case 4:
				$this->sql_query = "insert into geodesic_sessions
					(classified_session,user_id,last_time,ip,level)
					values
					(\"".$custom_id."\",".$this->login_return->ID.",".time().",\"".$ip."\",0)";
				break;
			case 5:
				$this->sql_query = "insert into geodesic_sessions
					(auction_session,user_id,last_time,ip,level)
					values
					(\"".$custom_id."\",".$this->login_return->ID.",".time().",\"".$ip."\",0)";
				break;
			case 6:
				$this->sql_query = "insert into geodesic_sessions
					(auction_session,user_id,last_time,ip,level)
					values
					(\"".$custom_id."\",".$this->login_return->ID.",".time().",\"".$ip."\",0)";
				break;
			case 7:
				$this->sql_query = "insert into geocore_sessions
					(geocore_session,user_id,last_time,ip)
					values
					(\"".$custom_id."\",".$this->login_return->ID.",".time().",\"".$ip."\")";
				break;
			case 8://vBulletin
				//do all vbulletin login procedures at once here
				return true;
				break;
			case 9:
				//begin Phorum
				//end Phorum
				//return here if everything is done in the other function call
				return true;
				break;
				
			case 10:
				//begin Cerbersu
				//end Cerberus
				//return here if everything is done in the other function call
				return true;
				break;	
			case 11:
				//jive
				//no code
				return true;
				break;	
			default:
				return false;
		}
		$session_result = $this->installation_db->Execute($this->sql_query);
		if ($this->debug) echo  $this->sql_query." is the query<bR>\n";
		if (!$session_result)
		{
			if ($this->debug) echo  $this->sql_query." is the query with an error<bR>\n";
			return false;
		}

		$this->get_installation_configuration_info();
		$expires = (time() + 31536000);
		if (strlen(trim($this->installation_info["domain"])) > 0)
			$domain = $this->installation_info["domain"];
		else
			$domain = "";
		switch ($this->installation_info["installation_type"])
		{
			case 1: //enterprise classifieds
			case 2: //premier classifieds
			case 3: //full classifieds
			case 4: //basic classifieds
				//$cookie_url = str_replace("classifieds.php", "",$this->installation_configuration_data->CLASSIFIEDS_URL);
				//setcookie("classified_session",$custom_id,$expires,$this->installation_info["cookie_path"],$domain);
				//header("Set-Cookie: classified_session=".$custom_id."; path=/; domain=".$cookie_url."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
				break;
			case 5: //premier auctions
				//$cookie_url = str_replace("auctions.php", "",$this->installation_configuration_data->AUCTIONS_URL);
				//setcookie("auction_session",$custom_id,$expires,$this->installation_info["cookie_path"],$domain);
				//header("Set-Cookie: auction_session=".$custom_id."; path=/; domain=".$cookie_url."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
				break;
			case 6: //enterprise auctions
				//$cookie_url = str_replace("auctions.php", "",$this->installation_configuration_data->AUCTIONS_URL);
				//setcookie("auction_session",$custom_id,$expires,$this->installation_info["cookie_path"],$domain);
				//header("Set-Cookie: auction_session=".$custom_id."; path=/; domain=".$cookie_url."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
				break;
			case 7: //geocore
				//$cookie_url = str_replace("auctions.php", "",$this->installation_configuration_data->AUCTIONS_URL);
				//header("Set-Cookie: classified_session=".$custom_id."; path=/; domain=".$cookie_url."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
				return true;
				break;
			case 8://vBulletin
				//do all vbulletin login procedures at once here
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
				//end Cerberus
				//return here if everything is done in the other function call
				return true;
				break;		
			case 11:
				//jive
				//no code
				return true;
				break;							
			default:
				return false;
		}		
		
		if ($this->debug) echo "<BR>BOTTOM OF CREATE_NEW_SESSION<Br>\n";
		return true;
	} //end of function create_new_session

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class API_login
?>