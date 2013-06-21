<? //api_update_user.php
include_once("../classes/api_site_class.php");

class API_update_user extends Api_site
{
	var $old_user_data;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function API_update_user ($user_info)
	{
		$this->Api_site();
		$this->user_info = $user_info;
	} // end of function API_update_user

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function api_update_user_info()
	{
		//check data
		//get installation(s) info
		$this->get_installation_info();
		while ($this->installation_info = $this->installations->FetchRow())
		{
			if ($this->check_latent_installation())
			{
				//this is not the invoking installation
				//connect to the installation database
				if ($this->connect_to_installation_db())
				{
					$this->get_user_id();
					if (($this->user_info) && ($this->user_id))
					{
						if ($this->get_current_userdata())
						{
							if ($this->insert_into_userdata_history())
							{
								if ($this->update_current_userdata())
								{
									$this->get_installation_configuration_info();
									if (!$this->update_logins_info())
									{
										//error updating logins
										$this->error_message = "API ERROR - UPDATING USER LOGIN DATA AFTER UPDATING PERSONAL DATA";
										$this->send_admin_error_email();
									}
									$this->installation_db->Close();
								}
								else
								{
									$this->error_message = "API ERROR - UPDATING CURRENT USER INFORMATION";
									$this->send_admin_error_email();
								}
							}
							else
							{
								$this->error_message = "API ERROR - INSERTING USERDATA INFO INTO USERDATA HISTORY";
								$this->send_admin_error_email();
							}
						}
						else
						{
							$this->error_message = "API ERROR - GETTING USERDATA INFO IN USER UPDATE";
							$this->send_admin_error_email();
						}
					} //end of switch
				} //end of if ($this->connect_to_installation_db())
			} //end of if($this->check_latent_installation())
		} //end of while

		return true;
	} //end of function api_update_user

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_current_userdata()
	{
		switch ($this->installation_info["installation_type"])
		{
			case 1:
				$this->sql_query = "select * from geodesic_userdata where id = ".$this->user_id;
				break;			
			case 2:
			case 3:
			case 4:
				$this->sql_query = "select * from geodesic_classifieds_userdata where id = ".$this->user_id;
				break;	
			case 5:
				$this->sql_query = "select * from geodesic_auctions_userdata where id = ".$this->user_id;
				break;
			case 6:
				$this->sql_query = "select * from geodesic_userdata where id = ".$this->user_id;
				break;
			case 7:
				$this->sql_query = "select * from geocore_users where id = ".$this->user_id;
				break;
			case 8:
				//vBulletin
				$time = time();
				// check to see if email address has changed
				include ($this->installation_info["vbulletin_config_path"]);
				
				if (isset($config['Database']['tableprefix']))
				{
					//this changed in some version...started at least in 3.5.2
					$tableprefix = $config['Database']['tableprefix'];
				}				
				
				$this->get_vBulletin_vboptions($tableprefix);
				if ($this->get_user_id())
				{
					$this->sql_query = "SELECT password, salt,email FROM " . $tableprefix . "user
						WHERE userid = " . $this->user_id;
					$userdata_result = $this->installation_db->Execute($this->sql_query);
					if ($this->debug)
						echo $this->sql_query."<br>\n";
					if (!$userdata_result)
					{
						$this->error_message = "API ERROR - LOGGING INTO VBULLETIN INSTALLATION\n
							GETTING USERDATA USING THE USER_ID\n\n
							YOU WILL HAVE TO UPDATE THIS DATA WITHIN YOUR VBULLETIN INSTALLATION\n
							TRYING TO UPDATE USING THE FOLLOWING:\n\n
							EMAIL: ".$this->user_info["email"]."\n\n
							USERNAME: ".$this->user_info["username"]."\n\n
							PASSWORD: ".$this->user_info["password"]."\n\n
							USERID: ".$this->user_id;
						if ($this->debug)
							echo $this->sql_query."<bR>\n";
						$this->send_admin_error_email();
						return false;				
					}
					elseif ($userdata_result->RecordCount() == 1)
					{
						$bbuserinfo = $userdata_result->FetchNextObject();				
						if ($this->user_info["email"] != $bbuserinfo->EMAIL)
						{
							//check that email does not already exist
							$this->sql_query = "SELECT username,email FROM " . $tableprefix . "user WHERE email=\"" . addslashes($this->user_info["email"])."\"";
							$duplicate_email_result = $this->installation_db->Execute($this->sql_query);
							if ($this->debug)
								echo $this->sql_query." in api<br>\n";
							if (!$duplicate_email_result)
							{
								$this->error_message = "API ERROR - CHECKING EMAIL DUPLICATION IN UPDATE USER INFORMATION AGAINST VBULLETIN INSTALLATION\n
									YOU WILL HAVE TO UPDATE THIS DATA WITHIN YOUR VBULLETIN INSTALLATION\n
									THERE WAS AN ERROR IN THE SQL STATEMENT\n\n".$this->sql_query."\n\n
									TRYING TO UPDATE USING THE FOLLOWING:\n\n
									EMAIL: ".$this->user_info["email"]."\n\n
									USERNAME: ".$this->user_info["username"]."\n\n
									PASSWORD: ".$this->user_info["password"]."\n\n
									USERID: ".$this->user_id;
								if ($this->debug)
									echo $this->sql_query." has an error<bR>\n";
								$this->send_admin_error_email();
								return false;
							}
							elseif ($duplicate_email_result->RecordCount() > 0)
							{
								$this->error_message = "EMAIL ADDRESS ALREADY EXISTS IN UPDATE USER INFORMATION AGAINST VBULLETIN INSTALLATION\n
									YOU WILL HAVE TO UPDATE THIS DATA WITHIN YOUR VBULLETIN INSTALLATION\n
									TRYING TO UPDATE USING THE FOLLOWING:\n\n
									EMAIL: ".$this->user_info["email"]."\n\n
									USERNAME: ".$this->user_info["username"]."\n\n
									PASSWORD: ".$this->user_info["password"]."\n\n
									USERID: ".$this->user_id;
								if ($this->debug)
									echo $this->sql_query." <bR>\n";
								$this->send_admin_error_email();
								return false;
							}
							else
							{
								//email does not currently exist within the vbulletin installation

								$this->sql_query = "update ".$tableprefix."user set email = \"".addslashes($this->user_info["email"])."\"
									where userid = ".$this->user_id;
								if ($this->debug)
									echo $this->sql_query." <bR>\n";								
								$update_email_result = $this->installation_db->Execute($this->sql_query);
								if(!$update_email_result)
								{
									$this->error_message = "API ERROR - ERROR UPDATING EMAIL ADDRESS IN UPDATE USER INFORMATION AGAINST VBULLETIN INSTALLATION\n
										YOU WILL HAVE TO UPDATE THIS DATA WITHIN YOUR VBULLETIN INSTALLATION\n
										TRYING TO UPDATE USING THE FOLLOWING:\n\nEMAIL: ".$this->user_info["email"]."\n\n
										USERNAME: ".$this->user_info["username"]."\n\n
										PASSWORD: ".$this->user_info["password"]."\n\n
										USERID: ".$this->user_id;
									if ($this->debug)
										echo $this->sql_query." <bR>\n";
									$this->send_admin_error_email();
									return false;						
								}
							}
						}
						//check to see if password needs to be changed..if there is a current one passed the password is supposed 
						//to be changed
						if (strlen(trim($this->user_info["password"])) > 0)
						{
							//get salt for password
							$salt = '';

							for ($i = 0; $i < 3; $i++)
							{
								$salt .= chr(rand(32, 126));
							}
							$hashedpassword = md5(md5($this->user_info["password"]) . $salt);

							$this->sql_query = "update " . $tableprefix . "user set
								password = \"".$hashedpassword."\",
								salt = \"".$salt."\"
								where userid = ".$this->user_id;
							$update_password_result = $this->installation_db->Execute($this->sql_query);
							if ($this->debug)
								echo $this->sql_query." <bR>\n";						
							if(!$update_password_result)
							{
								$this->error_message = "API ERROR - ERROR UPDATING PASSWORD IN UPDATE USER INFORMATION AGAINST VBULLETIN INSTALLATION\n
									YOU WILL HAVE TO UPDATE THIS DATA WITHIN YOUR VBULLETIN INSTALLATION\n
									TRYING TO UPDATE USING THE FOLLOWING:\n\n
									EMAIL: ".$this->user_info["email"]."\n\n
									USERNAME: ".$this->user_info["username"]."\n\n
									PASSWORD: ".$this->user_info["password"]."\n\n
									USERID: ".$this->user_id;
								if ($this->debug)
									echo $this->sql_query." <bR>\n";
								$this->send_admin_error_email();
								return false;						
							}				

						}
						return true;
					}
					else
					{
						//no userdata could be gotten using the vbulletin userid cookie
						$this->error_message = "COULD NOT RETRIEVE USER INFO FROM VBULLETIN INSTALLATION USING USER ID RETRIEVED\n
							YOU WILL HAVE TO UPDATE THIS DATA WITHIN YOUR VBULLETIN INSTALLATION\n
							TRYING TO UPDATE USING THE FOLLOWING:\n\nEMAIL: ".$this->user_info["email"]."\n\n
							USERNAME: ".$this->user_info["username"]."\n\n
							PASSWORD: ".$this->user_info["password"]."\n\n
							USERID: ".$this->user_id;
						if ($this->debug)
							echo "get_user_id returned false<bR>\n";
						$this->send_admin_error_email();
						return false;	
					}
				}
				else
				{
					//get_user_id returned false
					$this->error_message = "COULD NOT RETRIEVE USERID FROM VBULLETIN INSTALLATION\n
						YOU WILL HAVE TO UPDATE THIS DATA WITHIN YOUR VBULLETIN INSTALLATION\n
						TRYING TO UPDATE USING THE FOLLOWING:\n\nEMAIL: ".$this->user_info["email"]."\n\n
						USERNAME: ".$this->user_info["username"]."\n\n
						PASSWORD: ".$this->user_info["password"]."\n\n
						USERID: ".$this->user_id;
					if ($this->debug)
						echo "get_user_id returned false<bR>\n";
					$this->send_admin_error_email();
					return false;					
				}
				
				break;
			case 9:
				//Phorum
				//complete all of update within this function
				//Phorum does not have a userdata history table.
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
					$this->phorum_rn_filed = "name";
					$this->phorum_rn_value = addslashes($this->user_info["firstname"]);
					$this->phorum_id_field = "id";
					$this->phorum_cookie = "phorum_cookieauth";
				}
				else
				{
					$this->phorum_auth_table = "_users";
					$this->phorum_rn_field = "user_data";
					$this->phorum_rn_value = "a:1:{s:9:\"real_name\";s:15:\"".addslashes($this->user_info["firstname"])."\";}";
					$this->phorum_id_field = "user_id";
					$this->phorum_cookie = "phorum_session_v5";
				}				
				//
				if ($this->get_user_id())
				{
					$this->sql_query = "select password, email from ".$this->installation_info["phorum_database_table_prefix"].$this->phorum_auth_table."
						where ".$this->phorum_id_field." = " . $this->user_id;
					$userdata_result = $this->installation_db->Execute($this->sql_query);
					if ($this->debug)
						echo $this->sql_query."<br>\n";
					if (!$userdata_result)
					{
						$this->error_message = "API ERROR - LOGGING INTO PHORUM INSTALLATION\n
							GETTING USERDATA USING THE USER_ID\n\n
							YOU WILL HAVE TO UPDATE THIS DATA WITHIN YOUR PHORUM INSTALLATION\n
							TRYING TO UPDATE USING THE FOLLOWING:\n\n
							EMAIL: ".$this->user_info["email"]."\n\n
							USERNAME: ".$this->user_info["old_username"]."\n\n
							PASSWORD: ".$this->user_info["password"]."\n\n
							USERID: ".$this->user_id;
						if ($this->debug)
							echo $this->sql_query."<bR>\n";
						$this->send_admin_error_email();
						return false;				
					}
					elseif ($userdata_result->RecordCount() == 1)
					{			
						$phorum_user_data = $userdata_result->FetchNextObject();
						if (strlen(trim($this->user_info["email"])) > 0)
						{
							//check if email needs to change
							if ($this->user_info["email"] != $phorum_user_data->EMAIL)
							{
								//update user email
								$this->sql_query = "update ".$this->installation_info["phorum_database_table_prefix"].$this->phorum_auth_table." set
									".$this->phorum_rn_field." = \"".$this->phorum_rn_value."\",
									email = \"".addslashes($this->user_info["email"])."\"
									where ".$this->phorum_id_field." = ".$this->user_id;
								$user_result = $this->installation_db->Execute($this->sql_query);
								if ($this->debug)
									echo $this->sql_query."<bR>\n";
								if (!$user_result)
								{
									$this->error_message = "API ERROR - ERROR UPDATING USER INFO IN PHORUM INSTALLATION\n
										WITH THE FOLLOWING
										\nUSERNAME: ".$this->user_info["old_username"]."\n
										PASSWORD: ".$this->user_info["password"]." - md5 password: ".$md5_pass."\n
										NAME: ".$this->user_info["firstname"]."\n
										EMAIL: ".$this->user_info["email"]."\n\n".$this->sql_query;
									if ($this->debug)
										echo $this->sql_query." - produced error<bR>\n";
									$this->send_admin_error_email();
									return false;
								}								
								
							}
						}
						
						if (strlen(trim($this->user_info["password"])) > 0)
						{
							//check to see if password needs
							$md5_pass = md5($this->user_info["password"]);
							
							if ($md5_pass != $phorum_user_data->PASSWORD)
							{
							
								$this->sql_query = "update ".$this->installation_info["phorum_database_table_prefix"].$this->phorum_auth_table." set
									password = \"".$md5_pass."\"
									where ".$this->phorum_id_field." = " . $this->user_id;
								$user_result = $this->installation_db->Execute($this->sql_query);
								if ($this->debug)
									echo $this->sql_query."<bR>\n";
								if ($user_result)
								{
									$phorum_sessionID=$this->user_info["old_username"].":".$md5_pass;
									if (strlen(trim($this->installation_info["phorum_cookie_path"])) > 0)
										$cookie_path = $this->installation_info["phorum_cookie_path"];
									else
										$cookie_path = "/";									
									SetCookie($this->phorum_cookie, $phorum_sessionID , time()+(86400*365), $cookie_path,$this->installation_info["cookie_domain"]);
									
								}			
								else
								{
									$this->error_message = "API ERROR - ERROR UPDATING USER INFO IN PHORUM INSTALLATION\n
										WITH THE FOLLOWING
										\nUSERNAME: ".$this->user_info["old_username"]."\n
										PASSWORD: ".$this->user_info["password"]." - md5 password: ".$md5_pass."\n
										NAME: ".$this->user_info["firstname"]."\n
										EMAIL: ".$this->user_info["email"]."\n\n".$this->sql_query;
									if ($this->debug)
										echo $this->sql_query." - produced error<bR>\n";
									$this->send_admin_error_email();
									return false;
								}
							}
						}
						return true;
					}
					else
					{
						//no userdata could be gotten using the phorum userid cookie
						$this->error_message = "COULD NOT RETRIEVE USER INFO FROM PHORUM INSTALLATION USING USER ID RETRIEVED\n
							YOU WILL HAVE TO UPDATE THIS DATA WITHIN YOUR PHORUM INSTALLATION\n
							TRYING TO UPDATE USING THE FOLLOWING:\n\n
							EMAIL: ".$this->user_info["email"]."\n\n
							USERNAME: ".$this->user_info["old_username"]."\n\n
							PASSWORD: ".$this->user_info["password"]."\n\n
							USERID: ".$this->user_id;
						if ($this->debug)
							echo "get_user_id returned false<bR>\n";
						$this->send_admin_error_email();
						return false;						
					}
					
				}
				else
				{
					//get_user_id returned false
					$this->error_message = "COULD NOT RETRIEVE USERID FROM PHORUM INSTALLATION\n
						YOU WILL HAVE TO UPDATE THIS DATA WITHIN YOUR PHORUM INSTALLATION\n
						TRYING TO UPDATE USING THE FOLLOWING:\n\n
						EMAIL: ".$this->user_info["email"]."\n\n
						USERNAME: ".$this->user_info["old_username"]."\n\n
						PASSWORD: ".$this->user_info["password"]."\n\n
						USERID: ".$this->user_id;
					if ($this->debug)
						echo "get_user_id returned false<bR>\n";
					$this->send_admin_error_email();
					return false;					
				}
				break;
				
			case 10:
				// update user data for cerberus installation
				$cerberuspassword = md5($this->user_info["password"]);
				$this->sql_query = "select * from address where confirmation_code='" . $this->user_info["old_username"] . "'";
				$cerberus_address_result = $this->installation_db->Execute($this->sql_query);
				if ($cerberus_address_result) {
					$cerberus_address_data = $cerberus_address_result->FetchRow();
					if ($cerberus_address_data["public_user_id"]) {
						$this->sql_query = "update public_gui_users set full_name='" . $this->user_info["firstname"] . " " . $this->user_info["lastname"] . "',
							mailing_street1='" . $this->user_info["address"] . "', mailing_street2='" . $this->user_info["address_2"] . "', mailing_city='" .
							$this->user_info["city"] . "', mailing_state='" . $this->user_info["state"] . "', mailing_zip='" . $this->user_info["zip"] .
							"', mailing_country='" . $this->user_info["country"] . "', phone_work='" . $this->user_info["phone"] . "', phone_home='" .
							$this->user_info["phone_2"] . "', phone_mobile='', phone_fax='" . $this->user_info["fax"] . "', password='" . $cerberuspassword .
							"', company_id='0' where public_user_id='" . $cerberus_address_data["public_user_id"] . "'";
						$update_cerberus_user_result = $this->installation_db->Execute($this->sql_query);
						if ($update_cerberus_user_result) {
							$this->sql_query = "update address set address_address='" . $this->user_info["email"] . "' where address_id='" . $cerberus_address_data["address_id"] . "'";
							$update_cerberus_user_result = $this->installation_db->Execute($this->sql_query);
							if ($update_cerberus_user_result) {
								return true;
							} else {
								$this->error_message = "Failed to update Cerberus Address table in user update\n\n" . $this->sql_query . "\n\n";
								$this->send_admin_error_email();
							}
						} else {
							$this->error_message = "Failed to update Cerberus Public_GUI_Users table in user update\n\n" . $this->sql_query . "\n\n";
							$this->send_admin_error_email();
						}
					} else {
						$this->error_message = "Got a blank public_gui_id from cerberus while updating user\n\n" . $this->sql_query . "\n\n";
						$this->send_admin_error_email();
					}
				} else {
					$this->error_message = "Couldn't find \"" . $this->user_info["old_username"] . "\" in confirmation code of cerberus\n\n" . $this->sql_query . "\n\n";
					$this->send_admin_error_email();
				}
				return true;
				break;
			case 11:
				$sql_query = "update jiveUser set password='".$this->user_info["password"]."', name='".$this->user_info["firstname"]." ".$this->user_info["lastname"]."', email='".$this->user_info["email"]."', modificationDate=unix_timestamp() where username='".$this->user_info["old_username"]."'";
				$this->installation_db->Execute($this->sql_query);
				return true;
				break;				
			default:
				return false;
		}

		$old_userdata_result = $this->installation_db->Execute($this->sql_query);
		//echo $this->sql_query."<Br>\n";
		if (!$old_userdata_result)
		{
			echo $this->sql_query."<Br>\n";
			$this->error_message = "API ERROR - GETTING USERDATA INFO IN USER UPDATE";
			$this->send_admin_error_email();
			return false;
		}
		elseif ($old_userdata_result->RecordCount() == 1)
		{
			$this->old_user_data = $old_userdata_result->FetchNextObject();
			return true;
		}
		else
			return false;


	} //end of function get_current_userdata

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_into_userdata_history()
	{
		switch ($this->installation_info["installation_type"])
		{
			case 1:
				$this->sql_query = "insert into geodesic_userdata_history
					(date_of_change,id,username,email,company_name,business_type,firstname,lastname,
					address,address_2,zip,city,state,country,phone,phone2,fax,url,optional_field_1,
					optional_field_2,optional_field_3,optional_field_4,optional_field_5,optional_field_6,optional_field_7,
					optional_field_8,optional_field_9,optional_field_10)
					values
					(".time().",
					".$this->old_user_data->ID.",
					\"".$this->old_user_data->USERNAME."\",
					\"".$this->old_user_data->EMAIL."\",
					\"".addslashes($this->old_user_data->COMPANY_NAME)."\",
					\"".$this->old_user_data->BUSINESS_TYPE."\",
					\"".addslashes($this->old_user_data->FIRSTNAME)."\",
					\"".addslashes($this->old_user_data->LASTNAME)."\",
					\"".addslashes($this->old_user_data->ADDRESS)."\",
					\"".addslashes($this->old_user_data->ADDRESS_2)."\",
					\"".addslashes($this->old_user_data->ZIP)."\",
					\"".addslashes($this->old_user_data->CITY)."\",
					\"".$this->old_user_data->STATE."\",
					\"".$this->old_user_data->COUNTRY."\",
					\"".addslashes($this->old_user_data->PHONE)."\",
					\"".addslashes($this->old_user_data->PHONE2)."\",
					\"".addslashes($this->old_user_data->FAX)."\",
					\"".addslashes($this->old_user_data->URL)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_1)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_2)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_3)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_4)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_5)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_6)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_7)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_8)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_9)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_10)."\")";
				break;
			case 2:
			case 3:
			case 4:
				$this->sql_query = "insert into geodesic_classifieds_userdata_history
					(date_of_change,id,username,email,company_name,business_type,firstname,lastname,
					address,address_2,zip,city,state,country,phone,phone2,fax,url)
					values
					(".time().",
					".$this->old_user_data->ID.",
					\"".$this->old_user_data->USERNAME."\",
					\"".$this->old_user_data->EMAIL."\",
					\"".addslashes($this->old_user_data->COMPANY_NAME)."\",
					\"".$this->old_user_data->BUSINESS_TYPE."\",
					\"".addslashes($this->old_user_data->FIRSTNAME)."\",
					\"".addslashes($this->old_user_data->LASTNAME)."\",
					\"".addslashes($this->old_user_data->ADDRESS)."\",
					\"".addslashes($this->old_user_data->ADDRESS_2)."\",
					\"".addslashes($this->old_user_data->ZIP)."\",
					\"".addslashes($this->old_user_data->CITY)."\",
					\"".$this->old_user_data->STATE."\",
					\"".$this->old_user_data->COUNTRY."\",
					\"".addslashes($this->old_user_data->PHONE)."\",
					\"".addslashes($this->old_user_data->PHONE2)."\",
					\"".addslashes($this->old_user_data->FAX)."\",
					\"".addslashes($this->old_user_data->URL)."\")";
				break;
			case 5:
				$this->sql_query = "insert into geodesic_auctions_userdata_history
					(date_of_change,id,username,email,company_name,business_type,firstname,lastname,address,address_2,zip,city,state,country,phone,phone2,fax,url)
					values
					(".time().",
					".$this->old_user_data->ID.",
					\"".$this->old_user_data->USERNAME."\",
					\"".$this->old_user_data->EMAIL."\",
					\"".addslashes($this->old_user_data->COMPANY_NAME)."\",
					\"".$this->old_user_data->BUSINESS_TYPE."\",
					\"".addslashes($this->old_user_data->FIRSTNAME)."\",
					\"".addslashes($this->old_user_data->LASTNAME)."\",
					\"".addslashes($this->old_user_data->ADDRESS)."\",
					\"".addslashes($this->old_user_data->ADDRESS_2)."\",
					\"".addslashes($this->old_user_data->ZIP)."\",
					\"".addslashes($this->old_user_data->CITY)."\",
					\"".$this->old_user_data->STATE."\",
					\"".$this->old_user_data->COUNTRY."\",
					\"".addslashes($this->old_user_data->PHONE)."\",
					\"".addslashes($this->old_user_data->PHONE2)."\",
					\"".addslashes($this->old_user_data->FAX)."\",
					\"".addslashes($this->old_user_data->URL)."\")";
				break;
			case 6:
				$this->sql_query = "insert into geodesic_userdata_history
					(date_of_change,id,username,email,company_name,business_type,firstname,lastname,
					address,address_2,zip,city,state,country,phone,phone2,fax,url,optional_field_1,
					optional_field_2,optional_field_3,optional_field_4,optional_field_5,optional_field_6,optional_field_7,
					optional_field_8,optional_field_9,optional_field_10)
					values
					(".time().",
					".$this->old_user_data->ID.",
					\"".$this->old_user_data->USERNAME."\",
					\"".$this->old_user_data->EMAIL."\",
					\"".addslashes($this->old_user_data->COMPANY_NAME)."\",
					\"".$this->old_user_data->BUSINESS_TYPE."\",
					\"".addslashes($this->old_user_data->FIRSTNAME)."\",
					\"".addslashes($this->old_user_data->LASTNAME)."\",
					\"".addslashes($this->old_user_data->ADDRESS)."\",
					\"".addslashes($this->old_user_data->ADDRESS_2)."\",
					\"".addslashes($this->old_user_data->ZIP)."\",
					\"".addslashes($this->old_user_data->CITY)."\",
					\"".$this->old_user_data->STATE."\",
					\"".$this->old_user_data->COUNTRY."\",
					\"".addslashes($this->old_user_data->PHONE)."\",
					\"".addslashes($this->old_user_data->PHONE2)."\",
					\"".addslashes($this->old_user_data->FAX)."\",
					\"".addslashes($this->old_user_data->URL)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_1)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_2)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_3)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_4)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_5)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_6)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_7)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_8)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_9)."\",
					\"".addslashes($this->old_user_data->OPTIONAL_FIELD_10)."\")";
				break;
			case 7:
				return true;
				break;
			case 8:
				//vBulletin
				//no userdata history for
				return true;
				break;
			case 9:
				//Phorum
				//no userdata history for
				return true;
				break;
			case 10:
				//Cerberus
				//no userdata history for
				return true;
				break;
			case 11:
				//Jive
				//no userdata history for
				return true;
				break;
			default:
				return false;
		}


		$history_result = $this->installation_db->Execute($this->sql_query);
		if ($this->debug)
			echo $this->sql_query."<Br>\n";
		if (!$history_result)
		{
			if ($this->debug)
				echo $this->sql_query."<Br>\n";
			$this->error_message = "API ERROR - INSERTING USERDATA HISTORY IN USER UPDATE";
			$this->send_admin_error_email();
			return false;
		}
		return true;

	} //end of function insert_into_userdata_history

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_current_userdata()
	{
		switch ($this->installation_info["installation_type"])
		{
			case 1:
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_1_FILTER_ASSOCIATION))
					$this->user_info["optional_field_1"] = $this->old_user_data->OPTIONAL_FIELD_1;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_2_FILTER_ASSOCIATION))
					$this->user_info["optional_field_2"] = $this->old_user_data->OPTIONAL_FIELD_2;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_3_FILTER_ASSOCIATION))
					$this->user_info["optional_field_3"] = $this->old_user_data->OPTIONAL_FIELD_3;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_4_FILTER_ASSOCIATION))
					$this->user_info["optional_field_4"] = $this->old_user_data->OPTIONAL_FIELD_4;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_5_FILTER_ASSOCIATION))
					$this->user_info["optional_field_5"] = $this->old_user_data->OPTIONAL_FIELD_5;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_6_FILTER_ASSOCIATION))
					$this->user_info["optional_field_6"] = $this->old_user_data->OPTIONAL_FIELD_6;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_7_FILTER_ASSOCIATION))
					$this->user_info["optional_field_7"] = $this->old_user_data->OPTIONAL_FIELD_7;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_8_FILTER_ASSOCIATION))
					$this->user_info["optional_field_8"] = $this->old_user_data->OPTIONAL_FIELD_8;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_9_FILTER_ASSOCIATION))
					$this->user_info["optional_field_9"] = $this->old_user_data->OPTIONAL_FIELD_9;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_10_FILTER_ASSOCIATION))
					$this->user_info["optional_field_10"] = $this->old_user_data->OPTIONAL_FIELD_10;

				$this->sql_query = "update geodesic_userdata set
					email = \"".$this->user_info["email"]."\",
					company_name = \"".addslashes($this->user_info["company_name"])."\",
					business_type = \"".$this->user_info["business_type"]."\",
					firstname = \"".addslashes($this->user_info["firstname"])."\",
					lastname = \"".addslashes($this->user_info["lastname"])."\",
					address = \"".addslashes($this->user_info["address"])."\",
					address_2 = \"".addslashes($this->user_info["address_2"])."\",
					city = \"".addslashes($this->user_info["city"])."\",
					state = \"".$this->user_info["state"]."\",
					country = \"".addslashes($this->user_info["country"])."\",
					zip = \"".addslashes($this->user_info["zip"])."\",
					phone = \"".addslashes($this->user_info["phone"])."\",
					phone2 = \"".addslashes($this->user_info["phone_2"])."\",
					fax = \"".addslashes($this->user_info["fax"])."\",
					url = \"".addslashes($this->user_info["url"])."\",
					affiliate_html = \"".addslashes($this->user_info["affiliate_html"])."\",
					optional_field_1 = \"".addslashes($this->user_info["optional_field_1"])."\",
					optional_field_2 = \"".addslashes($this->user_info["optional_field_2"])."\",
					optional_field_3 = \"".addslashes($this->user_info["optional_field_3"])."\",
					optional_field_4 = \"".addslashes($this->user_info["optional_field_4"])."\",
					optional_field_5 = \"".addslashes($this->user_info["optional_field_5"])."\",
					optional_field_6 = \"".addslashes($this->user_info["optional_field_6"])."\",
					optional_field_7 = \"".addslashes($this->user_info["optional_field_7"])."\",
					optional_field_8 = \"".addslashes($this->user_info["optional_field_8"])."\",
					optional_field_9 = \"".addslashes($this->user_info["optional_field_9"])."\",
					optional_field_10 = \"".addslashes($this->user_info["optional_field_10"])."\"
					where id =".$this->user_id;

				break;
			case 2:
			case 3:
			case 4:
				$this->sql_query = "update geodesic_classifieds_userdata set
					email = \"".$this->user_info["email"]."\",
					company_name = \"".addslashes($this->user_info["company_name"])."\",
					business_type = \"".$this->user_info["business_type"]."\",
					firstname = \"".addslashes($this->user_info["firstname"])."\",
					lastname = \"".addslashes($this->user_info["lastname"])."\",
					address = \"".addslashes($this->user_info["address"])."\",
					address_2 = \"".addslashes($this->user_info["address_2"])."\",
					city = \"".addslashes($this->user_info["city"])."\",
					state = \"".$this->user_info["state"]."\",
					country = \"".addslashes($this->user_info["country"])."\",
					zip = \"".addslashes($this->user_info["zip"])."\",
					phone = \"".addslashes($this->user_info["phone"])."\",
					phone2 = \"".addslashes($this->user_info["phone_2"])."\",
					fax = \"".addslashes($this->user_info["fax"])."\",
					url = \"".addslashes($this->user_info["url"])."\"
					where id =".$this->user_id;
				break;
			case 5:
				$this->sql_query = "update geodesic_auctions_userdata set
					email = \"".$this->user_info["email"]."\",
					company_name = \"".addslashes($this->user_info["company_name"])."\",
					business_type = \"".$this->user_info["business_type"]."\",
					firstname = \"".addslashes($this->user_info["firstname"])."\",
					lastname = \"".addslashes($this->user_info["lastname"])."\",
					address = \"".addslashes($this->user_info["address"])."\",
					address_2 = \"".addslashes($this->user_info["address_2"])."\",
					city = \"".addslashes($this->user_info["city"])."\",
					state = \"".$this->user_info["state"]."\",
					country = \"".addslashes($this->user_info["country"])."\",
					zip = \"".addslashes($this->user_info["zip"])."\",
					phone = \"".addslashes($this->user_info["phone"])."\",
					phone2 = \"".addslashes($this->user_info["phone_2"])."\",
					fax = \"".addslashes($this->user_info["fax"])."\",
					url = \"".addslashes($this->user_info["url"])."\"
					where id =".$this->user_id;
				break;
			case 6:
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_1_FILTER_ASSOCIATION))
					$this->user_info["optional_field_1"] = $this->old_user_data->OPTIONAL_FIELD_1;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_2_FILTER_ASSOCIATION))
					$this->user_info["optional_field_2"] = $this->old_user_data->OPTIONAL_FIELD_2;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_3_FILTER_ASSOCIATION))
					$this->user_info["optional_field_3"] = $this->old_user_data->OPTIONAL_FIELD_3;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_4_FILTER_ASSOCIATION))
					$this->user_info["optional_field_4"] = $this->old_user_data->OPTIONAL_FIELD_4;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_5_FILTER_ASSOCIATION))
					$this->user_info["optional_field_5"] = $this->old_user_data->OPTIONAL_FIELD_5;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_6_FILTER_ASSOCIATION))
					$this->user_info["optional_field_6"] = $this->old_user_data->OPTIONAL_FIELD_6;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_7_FILTER_ASSOCIATION))
					$this->user_info["optional_field_7"] = $this->old_user_data->OPTIONAL_FIELD_7;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_8_FILTER_ASSOCIATION))
					$this->user_info["optional_field_8"] = $this->old_user_data->OPTIONAL_FIELD_8;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_9_FILTER_ASSOCIATION))
					$this->user_info["optional_field_9"] = $this->old_user_data->OPTIONAL_FIELD_9;
				if (($this->installation_configuration_data->USE_FILTERS) && ($this->installation_configuration_data->OPTIONAL_10_FILTER_ASSOCIATION))
					$this->user_info["optional_field_10"] = $this->old_user_data->OPTIONAL_FIELD_10;

				$this->sql_query = "update geodesic_userdata set
					email = \"".$this->user_info["email"]."\",
					company_name = \"".addslashes($this->user_info["company_name"])."\",
					business_type = \"".$this->user_info["business_type"]."\",
					firstname = \"".addslashes($this->user_info["firstname"])."\",
					lastname = \"".addslashes($this->user_info["lastname"])."\",
					address = \"".addslashes($this->user_info["address"])."\",
					address_2 = \"".addslashes($this->user_info["address_2"])."\",
					city = \"".addslashes($this->user_info["city"])."\",
					state = \"".$this->user_info["state"]."\",
					country = \"".addslashes($this->user_info["country"])."\",
					zip = \"".addslashes($this->user_info["zip"])."\",
					phone = \"".addslashes($this->user_info["phone"])."\",
					phone2 = \"".addslashes($this->user_info["phone_2"])."\",
					fax = \"".addslashes($this->user_info["fax"])."\",
					url = \"".addslashes($this->user_info["url"])."\",
					affiliate_html = \"".addslashes($this->user_info["affiliate_html"])."\",
					optional_field_1 = \"".addslashes($this->user_info["optional_field_1"])."\",
					optional_field_2 = \"".addslashes($this->user_info["optional_field_2"])."\",
					optional_field_3 = \"".addslashes($this->user_info["optional_field_3"])."\",
					optional_field_4 = \"".addslashes($this->user_info["optional_field_4"])."\",
					optional_field_5 = \"".addslashes($this->user_info["optional_field_5"])."\",
					optional_field_6 = \"".addslashes($this->user_info["optional_field_6"])."\",
					optional_field_7 = \"".addslashes($this->user_info["optional_field_7"])."\",
					optional_field_8 = \"".addslashes($this->user_info["optional_field_8"])."\",
					optional_field_9 = \"".addslashes($this->user_info["optional_field_9"])."\",
					optional_field_10 = \"".addslashes($this->user_info["optional_field_10"])."\"
					where id =".$this->user_id;

				break;
			case 7:
				$this->sql_query = "update geocore_users set
					email = \"".$this->user_info["email"]."\",
					company_name = \"".addslashes($this->user_info["company_name"])."\",
					business_type = \"".$this->user_info["business_type"]."\",
					firstname = \"".addslashes($this->user_info["firstname"])."\",
					lastname = \"".addslashes($this->user_info["lastname"])."\",
					address = \"".addslashes($this->user_info["address"])."\",
					address_2 = \"".addslashes($this->user_info["address_2"])."\",
					city = \"".addslashes($this->user_info["city"])."\",
					state = \"".$this->user_info["state"]."\",
					country = \"".addslashes($this->user_info["country"])."\",
					zip = \"".addslashes($this->user_info["zip"])."\",
					phone = \"".addslashes($this->user_info["phone"])."\",
					phone2 = \"".addslashes($this->user_info["phone_2"])."\",
					fax = \"".addslashes($this->user_info["fax"])."\",
					url = \"".addslashes($this->user_info["url"])."\",
					affiliate_html = \"".addslashes($this->user_info["affiliate_html"])."\",
					optional_field_1 = \"".addslashes($this->user_info["optional_field_1"])."\",
					optional_field_2 = \"".addslashes($this->user_info["optional_field_2"])."\",
					optional_field_3 = \"".addslashes($this->user_info["optional_field_3"])."\",
					optional_field_4 = \"".addslashes($this->user_info["optional_field_4"])."\",
					optional_field_5 = \"".addslashes($this->user_info["optional_field_5"])."\",
					optional_field_6 = \"".addslashes($this->user_info["optional_field_6"])."\",
					optional_field_7 = \"".addslashes($this->user_info["optional_field_7"])."\",
					optional_field_8 = \"".addslashes($this->user_info["optional_field_8"])."\",
					optional_field_9 = \"".addslashes($this->user_info["optional_field_9"])."\",
					optional_field_10 = \"".addslashes($this->user_info["optional_field_10"])."\"
					where id =".$this->user_id;

				break;
			case 8:
				//vBulletin

				//update user login/user information in vBulletin installation
				return true;
				break;
			case 9:
				//begin Phorum
				//update phorum login/user information

				//end Phorum
				return true;
				break;
			case 10:
				//Cerberus
				return true;
				break;
			case 11:
				//Jive
				return true;
				break;							
			default:
				return false;
				break;
		}
		$update_history_result = $this->installation_db->Execute($this->sql_query);
		if ($this->debug)
			echo $this->sql_query."<Br>\n";
		if (!$update_history_result)
		{
			echo $this->sql_query."<Br>\n";
			$this->error_message = "API ERROR - UPDATING USERDATA INFO IN USER UPDATE";
			$this->send_admin_error_email();
			return false;
		}
		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_logins_info()
	{
		if (strlen(trim($this->user_info["password"])) > 0)
		{
			switch ($this->installation_info["installation_type"])
			{
				case 1:
					$this->sql_query = "update geodesic_logins set
						password = \"".$this->user_info["password"]."\"
						where id =".$this->user_id;
					break;				
				case 2:
				case 3:
				case 4:
					$this->sql_query = "update geodesic_classifieds_logins set
						password = \"".$this->user_info["password"]."\"
						where id =".$this->user_id;
					break;					
				case 5:
					$this->sql_query = "update geodesic_auctions_logins set
						password = \"".$this->user_info["password"]."\"
						where id =".$this->user_id;
					break;
				case 6:
					$this->sql_query = "update geodesic_logins set
						password = \"".$this->user_info["password"]."\"
						where id =".$this->user_id;
					break;
				case 7:
					$this->sql_query = "update geocore_users set
						password = \"".$this->user_info["password"]."\"
						where id =".$this->user_id;
					break;
				case 8:
					//vBulletin
					//update just the password?????????
					//remove return if place query here
					return true;
					break;
				case 9:
					//Phorum
					//update just the password
					//remove return if place query here
					return true;
					break;
				case 10:
					//Cerberus
					return true;
					break;
				case 11:
					//Jive
					return true;
					break;										
				default:
					return false;
			}
			if ($this->debug)
				echo $this->sql_query."<Br>\n";
			$update_login_result = $this->installation_db->Execute($this->sql_query);

			if (!$update_login_result)
			{
				if ($this->debug)
					echo $this->sql_query."<Br>\n";
				$this->error_message = "API ERROR - UPDATING LOGIN INFO IN USER UPDATE";
				$this->send_admin_error_email();
				return false;
			}
		}
		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class API_Register
?>