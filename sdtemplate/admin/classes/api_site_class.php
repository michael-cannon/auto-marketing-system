<? // api_site_class.php

class Api_site
{
	var $api_db;
	var $installations;
	var $installation_info;
	var $installation_configuration_data;
	var $sql_query;
	var $user_info;
	var $user_id;
	var $error_message;

	var $latent_db_host;
	var $latent_db_username;
	var $latent_db_password;
	var $latent_db_name;
	var $latent_installation_type;

	var $debug = 0;
	var $debug_cookie = 0;

	//vBulletin data
	var $vboptions;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function API_site ()
	{
		include("config.php");
		include("classes/adodb.inc.php");

		$this->api_db = &ADONewConnection('mysql');
		//$this->api_db = &ADONewConnection('access');
		//$this->api_db = &ADONewConnection('ado');
		//$this->api_db = &ADONewConnection('ado_mssql');
		//$this->api_db = &ADONewConnection('borland_ibase');
		//$this->api_db = &ADONewConnection('csv');
		//$this->api_db = &ADONewConnection('db2');
		//$this->api_db = &ADONewConnection('fbsql');
		//$this->api_db = &ADONewConnection('firebird');
		//$this->api_db = &ADONewConnection('ibase');
		//$this->api_db = &ADONewConnection('informix');
		//$this->api_db = &ADONewConnection('mssql');
		//$this->api_db = &ADONewConnection('mysqlt');
		//$this->api_db = &ADONewConnection('oci8');
		//$this->api_db = &ADONewConnection('oci8po');
		//$this->api_db = &ADONewConnection('odbc');
		//$this->api_db = &ADONewConnection('odbc_mssql');
		//$this->api_db = &ADONewConnection('odbc_oracle');
		//$this->api_db = &ADONewConnection('oracle');
		//$this->api_db = &ADONewConnection('postgres7');
		//$this->api_db = &ADONewConnection('postgress');
		//$this->api_db = &ADONewConnection('proxy');
		//$this->api_db = &ADONewConnection('sqlanywhere');
		//$this->api_db = &ADONewConnection('sybase');
		//$this->api_db = &ADONewConnection('vfp');
		
		if ($this->debug)
		{
			echo $api_db_host." is \$api_db_host in api_site_class<bR>\n";
			echo $api_db_username." is \$api_db_username<bR>\n";
			echo $db_host." is db_host<bR>\n";
			echo $database." is database<BR>\n";
		}

		if (!$this->api_db->Connect($api_db_host, $api_db_username, $api_db_password, $api_database))
		{
			if ($this->debug)
			{
				echo "error connecting to api database<br>\n";
				echo $this->api_db->ErrorMsg()." is the error message<br>\n";
			}
			$this->send_admin_error_email(1);
			return false;
		}
		if ($this->debug)
		{
			echo "connected successfully to ".$api_db_host." / ".$api_database."<br>\n";
		}
		return true;

	} // end of function API_site

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function connect_to_installation_db()
	{
		$this->installation_db = &ADONewConnection('mysql');
		//$this->installation_db = &ADONewConnection('access');
		//$this->installation_db = &ADONewConnection('ado');
		//$this->installation_db = &ADONewConnection('ado_mssql');
		//$this->installation_db = &ADONewConnection('borland_ibase');
		//$this->installation_db = &ADONewConnection('csv');
		//$this->installation_db = &ADONewConnection('db2');
		//$this->installation_db = &ADONewConnection('fbsql');
		//$this->installation_db = &ADONewConnection('firebird');
		//$this->installation_db = &ADONewConnection('ibase');
		//$this->installation_db = &ADONewConnection('informix');
		//$this->installation_db = &ADONewConnection('mssql');
		//$this->installation_db = &ADONewConnection('mysqlt');
		//$this->installation_db = &ADONewConnection('oci8');
		//$this->installation_db = &ADONewConnection('oci8po');
		//$this->installation_db = &ADONewConnection('odbc');
		//$this->installation_db = &ADONewConnection('odbc_mssql');
		//$this->installation_db = &ADONewConnection('odbc_oracle');
		//$this->installation_db = &ADONewConnection('oracle');
		//$this->installation_db = &ADONewConnection('postgres7');
		//$this->installation_db = &ADONewConnection('postgress');
		//$this->installation_db = &ADONewConnection('proxy');
		//$this->installation_db = &ADONewConnection('sqlanywhere');
		//$this->installation_db = &ADONewConnection('sybase');
		//$this->installation_db = &ADONewConnection('vfp');

		if (!$this->installation_db->PConnect($this->installation_info["db_host"], $this->installation_info["db_username"], $this->installation_info["db_password"], $this->installation_info["db_name"]))
		{
			if ($this->debug)
			{
				echo "error connecting to database ".$this->installation_info["db_host"]." - ".$this->installation_info["db_name"]."<br>\n";
				echo $this->installation_db->ErrorMsg()." is the error message<br>\n";
			}
			$this->error_message = "API ERROR - CONNECTING TO DATABASE INSTALLATION";
			//echo $this->error_message."<br>\n";
			$this->send_admin_error_email();
			return false;
		}
		else
		{
			if ($this->debug) echo "connected to ".$this->installation_info["db_host"]." - ".$this->installation_info["db_name"]." - ".$this->installation_info["installation_type"]."<br>\n";
			return true;
		}
	} // end of function connect_to_installation_db()

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function reconnect_to_latent_installation_db()
	{
		if (!$this->installation_db->PConnect($this->latent_db_host, $this->latent_db_username, $this->latent_db_password, $this->latent_db_name))
		{
			if ($this->debug)
			{
				echo "error reconnecting to latent database using: ".$this->installation_info["db_host"]." - ".$this->installation_info["db_name"]."<br>\n";
				echo $this->installation_db->ErrorMsg()." is the error message<br>\n";
			}
			$this->error_message = "API ERROR - RECONNECTING TO DATABASE INSTALLATION";
			//echo $this->error_message."<br>\n";
			$this->send_admin_error_email();
			return false;
		}
		if ($this->debug)
		{
			echo "reconnected to latent database using: ".$this->installation_info["db_host"]." - ".$this->installation_info["db_name"]."<br>\n";
		}
		return true;
	} // end of function disconnect_from_installation_db()

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_installation_info ()
	{
		//select all live installations
		$this->sql_query = "select * from geodesic_api_installation_info where active = 1";
		$get_installation_result = $this->api_db->Execute($this->sql_query);
		if ($this->debug) echo $this->sql_query."<br>\n";
		if (!$get_installation_result)
		{
			if ($this->debug)
			{
				 echo $this->sql_query."<br>\n";
				 echo $this->api_db->ErrorMsg()."<br>\n";
			}
			$this->error_message = "API ERROR - GETTING ALL ACTIVE INSTALLATIONS\n\n";
			$this->error_message .= $this->api_db->ErrorMsg()."\n\n";
			$this->send_admin_error_email();
			return false;
		}
		elseif ($get_installation_result->RecordCount() > 0)
		{
			//put in an array?
			//echo "there are installations detected<BR>\n";
			if ($this->debug)
			{
				echo $get_installation_result->RecordCount()." is the installation record count<BR>\n";
			}
			$this->installations = $get_installation_result;
			return $get_installation_result;
		}
		else
		{
			if ($this->debug)
			{
				echo "there are no installations to integrate<Br>\n";
			}
			$this->installations = 0;
			return true;
		}

	} // end of function get_installation_info ()

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function send_admin_error_email()
	{
		$this->error_message .= "An error occurred trying to register a user at the following installation:\n";
		$this->error_message .= "\n\nwith the following db_host:\n".$this->installation_info["db_host"]."\n";
		$this->error_message .= "\n\nwith the following db_name:\n".$this->installation_info["db_name"]."\n";
		$this->error_message .= "\n\nwith the following installation_type:\n".$this->installation_info["installation_type"]."\n";
		$this->error_message .= "\nwith the following registration information:\n";
		if (is_array($this->user_info))
		{
			reset($this->user_info);
			foreach ($this->user_info as $key => $value)
			{
				$this->error_message .= "\n".$key.": ".$value;
			}
		}

		$this->error_message .= "ERROR MAY HAVE OCURRED WITH THE FOLLOWING QUERY:\n";
		$this->error_message .= $this->sql_query;

		$from = "From: ".$this->installation_info["admin_email"]."\r\nReply-to: ".$this->installation_info["admin_email"]."\r\n";
		$additional = "-f".$this->installation_info["admin_email"];
		mail($this->installation_info["admin_email"],"api registration error",$this->error_message,$from,$additional);
	} // end of function send_admin_error_email()

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_user_id()
	{
		//use login info from active installation to get id from latent installation
		if ($this->debug)
		{
			echo $this->installation_info["installation_type"]." is installation_type in the top of get_user_id<BR>\n";	
		}
		
		if (($this->installation_info["installation_type"] == 2) ||
			($this->installation_info["installation_type"] == 3) ||
			($this->installation_info["installation_type"] == 4))
		{
			//classifieds type
			if (strlen(trim($this->user_info["old_username"])) > 0)
			{
				$this->sql_query = "select id from geodesic_classifieds_logins where
					username = \"".$this->user_info["old_username"]."\" and
					password = \"".$this->user_info["password"]."\"";
			}
			else
			{
				$this->sql_query = "select id from geodesic_classifieds_logins where
					username = \"".$this->user_info["username"]."\" and
					password = \"".$this->user_info["password"]."\"";
			}
			$get_user_id_result = $this->installation_db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$get_user_id_result)
			{
				if ($this->debug) echo $this->sql_query." -  error<br>\n";
				$this->error_message = "API ERROR - GETTING ALL USER ID - 1\n";
				$this->send_admin_error_email();
				return false;
			}
			elseif ($get_user_id_result->RecordCount() == 1)
			{
				//put in an array?
				$show_id = $get_user_id_result->FetchNextObject();
				$this->user_id = $show_id->ID;
				if ($this->debug)
				{
					echo $this->user_id." is the user_id found in get_user_id<BR>\n";	
				}
				return true;
			}
			else
			{
				if ($this->debug)
				{
					echo "no user_id found in get_user_id<BR>\n";	
				}				
				$this->error_message = "API ERROR - GETTING ALL USER ID - 2\n";
				$this->send_admin_error_email();
				return false;
			}
		}
		elseif ($this->installation_info["installation_type"] == 1)
		{
			//classifieds type
			if (strlen(trim($this->user_info["old_username"])) > 0)
			{
				$this->sql_query = "select id from geodesic_logins where
					username = \"".$this->user_info["old_username"]."\" and
					password = \"".$this->user_info["password"]."\"";
			}
			else
			{
				$this->sql_query = "select id from geodesic_logins where
					username = \"".$this->user_info["username"]."\" and
					password = \"".$this->user_info["password"]."\"";
			}
			$get_user_id_result = $this->installation_db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$get_user_id_result)
			{
				if ($this->debug) echo $this->sql_query."<br>\n";
				$this->error_message = "API ERROR - GETTING ALL USER ID - 1\n";
				$this->send_admin_error_email();
				return false;
			}
			elseif ($get_user_id_result->RecordCount() == 1)
			{
				//put in an array?
				$show_id = $get_user_id_result->FetchNextObject();
				$this->user_id = $show_id->ID;
				if ($this->debug)
				{
					echo $this->user_id." is the user_id found in get_user_id<BR>\n";	
				}
				return true;
			}
			else
			{
				if ($this->debug)
				{
					echo "no user_id found in get_user_id<BR>\n";	
				}					
				$this->error_message = "API ERROR - GETTING ALL USER ID - 2\n";
				$this->send_admin_error_email();
				return false;
			}
		}
		elseif ($this->installation_info["installation_type"] == 5)
		{
			//auction type
			if (strlen(trim($this->user_info["old_username"])) > 0)
			{
				$this->sql_query = "select id from geodesic_auctions_logins where
					username = \"".$this->user_info["old_username"]."\" and
					password = \"".$this->user_info["password"]."\"";
			}
			else
			{
				$this->sql_query = "select id from geodesic_auctions_logins where
					username = \"".$this->user_info["username"]."\" and
					password = \"".$this->user_info["password"]."\"";
			}
			$get_user_id_result = $this->installation_db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$get_user_id_result)
			{
				if ($this->debug) echo $this->sql_query."<br>\n";
				$this->error_message = "API ERROR - GETTING ALL USER ID - 3\n";
				$this->send_admin_error_email();
				return false;
			}
			elseif ($get_user_id_result->RecordCount() == 1)
			{
				//put in an array?
				$show_user_id = $get_user_id_result->FetchNextObject();
				$this->user_id = $show_user_id->ID;
				if ($this->debug)
				{
					echo $this->user_id." is the user_id found in get_user_id<BR>\n";	
				}				
				return true;
			}
			else
			{
				if ($this->debug)
				{
					echo "no user_id found in get_user_id<BR>\n";	
				}					
				$this->error_message = "API ERROR - GETTING ALL USER ID - 4\n";
				$this->send_admin_error_email();
				return false;
			}
		}
		elseif ($this->installation_info["installation_type"] == 6)
		{
			//auction type
			if (strlen(trim($this->user_info["old_username"])) > 0)
			{
				$this->sql_query = "select id from geodesic_logins where
					username = \"".$this->user_info["old_username"]."\" and
					password = \"".$this->user_info["encrypted_password"]."\"";
			}
			else
			{
				$this->sql_query = "select id from geodesic_logins where
					username = \"".$this->user_info["username"]."\" and
					password = \"".$this->user_info["encrypted_password"]."\"";
			}
			$get_user_id_result = $this->installation_db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$get_user_id_result)
			{
				if ($this->debug) echo $this->sql_query."<br>\n";
				$this->error_message = "API ERROR - GETTING ALL USER ID - ENT AUCTIONS - 3\n";
				$this->send_admin_error_email();
				return false;
			}
			elseif ($get_user_id_result->RecordCount() == 1)
			{
				//put in an array?
				$show_user_id = $get_user_id_result->FetchNextObject();
				$this->user_id = $show_user_id->ID;
				if ($this->debug)
				{
					echo $this->user_id." is the user_id found in get_user_id<BR>\n";	
				}				
				return true;
			}
			else
			{
				if ($this->debug)
				{
					echo "no user_id found in get_user_id<BR>\n";	
				}					
				$this->error_message = "API ERROR - GETTING ALL USER ID - ENT AUCTIONS - 4\n";
				$this->send_admin_error_email();
				return false;
			}
		}
		elseif ($this->installation_info["installation_type"] == 7)
		{
			//auction type
			if (strlen(trim($this->user_info["old_username"])) > 0)
			{
				$this->sql_query = "select id from geocore_users where
					username = \"".$this->user_info["old_username"]."\" and
					password = \"".$this->user_info["password"]."\"";
			}
			else
			{
				$this->sql_query = "select id from geocore_users where
					username = \"".$this->user_info["username"]."\" and
					password = \"".$this->user_info["password"]."\"";
			}
			$get_user_id_result = $this->installation_db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$get_user_id_result)
			{
				if ($this->debug) echo $this->sql_query."<br>\n";
				$this->error_message = "API ERROR - GETTING ALL USER ID - GEOCORE - 3\n";
				$this->send_admin_error_email();
				return false;
			}
			elseif ($get_user_id_result->RecordCount() == 1)
			{
				//put in an array?
				$show_user_id = $get_user_id_result->FetchNextObject();
				$this->user_id = $show_user_id->ID;
				if ($this->debug)
				{
					echo $this->user_id." is the user_id found in get_user_id<BR>\n";	
				}					
				return true;
			}
			else
			{
				if ($this->debug)
				{
					echo "no user_id found in get_user_id<BR>\n";	
				}					
				$this->error_message = "API ERROR - GETTING ALL USER ID - GEOCORE - 4\n";
				$this->send_admin_error_email();
				return false;
			}
		}
		elseif ($this->installation_info["installation_type"] == 8)
		{
			//vBulletin
			//must have username
			if ($this->debug)
			{
				echo "about to check vbulletin in get_user_id - ".$this->user_info["username"]."<bR>\n";	
			}
			
			include ($this->installation_info["vbulletin_config_path"]);
				
			if (isset($config['Database']['tableprefix']))
			{
				//this changed in some version...started at least in 3.5.2
				$tableprefix = $config['Database']['tableprefix'];
			}			
			
			if (strlen(trim($this->user_info["username"])) > 0)
			{
				$this->sql_query = "select userid from " . $tableprefix . "user where username = \"" . $this->user_info["username"]. "\"";
				$get_user_id_result = $this->installation_db->Execute($this->sql_query);
				if ($this->debug) echo $this->sql_query."<br>\n";
				if (!$get_user_id_result)
				{
					if ($this->debug)
					{
						echo "no user_id found in get_user_id<BR>\n";	
					}					
					return false;
				}
				elseif ($get_user_id_result->RecordCount() == 1)
				{
					$current_userid = $get_user_id_result->FetchNextObject();
					$this->user_id = $current_userid->USERID;
					if ($this->debug)
					{
						echo $this->user_id." is the user_id found in get_user_id<BR>\n";	
					}						
					return true;
				}
				else
				{
					if ($this->debug)
					{
						echo "no user_id found in get_user_id<BR>\n";	
					}						
					return false;
				}
			}
			else
			{
				if ($this->debug)
				{
					echo "user_info[username] is missing in vbulletin type in get_user_id<Br>\n";	
				}
				return false;
			}
		}
		elseif ($this->installation_info["installation_type"] == 9)
		{
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
				$this->sql_query = "select id from ".$this->installation_info["phorum_database_table_prefix"].$this->phorum_auth_table." where
					username = \"".$this->user_info["old_username"]."\" and
					password = \"".md5($this->user_info["password"])."\"";				
			}
			else
			{
				$this->phorum_auth_table = "_users";
				$this->phorum_id_field = "user_id";
				$this->sql_query = "select user_id from ".$this->installation_info["phorum_database_table_prefix"].$this->phorum_auth_table." where
					username = \"".$this->user_info["username"]."\"";				
			}			
			
			//begin Phorum
			//may not be necessary

			$get_user_id_result = $this->installation_db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$get_user_id_result)
			{
				if ($this->debug) echo $this->sql_query."<br>\n";
				$this->error_message = "API ERROR - GETTING ALL USER ID - PHORUM - 1\nUSERNAME: ".$this->user_info["username"]."\nPASSWORD: ".md5($this->user_info["password"])."\n\n".$this->sql_query;
				$this->send_admin_error_email();
				return false;
			}
			elseif ($get_user_id_result->RecordCount() == 1)
			{
				//put in an array?
				$show_user_id = $get_user_id_result->FetchNextObject();
				if ($this->phorum_ver == "v3")
				{
					$this->user_id = $show_user_id->ID;
				}
				else 
				{
					$this->user_id = $show_user_id->USER_ID;	
				}
				return true;
			}
			else
			{
				$this->error_message = "API ERROR - GETTING ALL USER ID - PHORUM - 2\nUSERNAME: ".$this->user_info["username"]."\nPASSWORD: ".md5($this->user_info["password"])."\n\n".$this->sql_query;
				$this->send_admin_error_email();
				return false;
			}

			//end Phorum
		}
	} // end of function get_user_id

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_installation_configuration_info()
	{
		if (($this->installation_info["installation_type"] == 1) ||
			($this->installation_info["installation_type"] == 2) ||
			($this->installation_info["installation_type"] == 3) ||
			($this->installation_info["installation_type"] == 4))
		{
			//classified type
			$this->sql_query = "SELECT * FROM geodesic_classifieds_configuration";
			$result = $this->installation_db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug) echo $this->sql_query."<br>\n";
				$this->error_message = "API ERROR - GETTING INSTALLATION CONFIGURATION INFO - 1\n";
				$this->send_admin_error_email();
				return false;
			}
			else
			{
				$this->installation_configuration_data = $result->FetchNextObject();
			}
			return true;
		}
		elseif ($this->installation_info["installation_type"] == 5)
		{
			//auction type
			$this->sql_query = "SELECT * FROM geodesic_auctions_configuration";
			$result = $this->installation_db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug) echo $this->sql_query."<br>\n";
				$this->error_message = "API ERROR - GETTING INSTALLATION CONFIGURATION INFO - 2\n";
				$this->send_admin_error_email();
				return false;
			}
			else
			{
				$this->installation_configuration_data = $result->FetchNextObject();
			}
			return true;
		}
		elseif ($this->installation_info["installation_type"] == 6)
		{
			//auction type
			$this->sql_query = "SELECT * FROM geodesic_auctions_configuration";
			$result = $this->installation_db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug) echo $this->sql_query."<br>\n";
				$this->error_message = "API ERROR - GETTING INSTALLATION CONFIGURATION INFO - 2\n";
				$this->send_admin_error_email();
				return false;
			}
			else
			{
				$this->installation_configuration_data = $result->FetchNextObject();
			}
			return true;
		}
		elseif ($this->installation_info["installation_type"] == 7)
		{
			//auction type
			$this->sql_query = "SELECT * FROM geodesic_auctions_configuration";
			$result = $this->installation_db->Execute($this->sql_query);
			if ($this->debug) echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug) echo $this->sql_query."<br>\n";
				$this->error_message = "API ERROR - GETTING INSTALLATION CONFIGURATION INFO - 2\n";
				$this->send_admin_error_email();
				return false;
			}
			else
			{
				$this->installation_configuration_data = $result->FetchNextObject();
			}
			return true;
		}
		elseif ($this->installation_info["installation_type"] == 8)
		{
			//vBulletin
			//may not be necessary
			return true;
		}
		elseif ($this->installation_info["installation_type"] == 9)
		{
			//begin Phorum
			//may not be necessary to code anything here
			//end Phorum
			return true;
		}
		elseif ($this->installation_info["installation_type"] == 10)
		{
			//Cerberus
			return true;
		}
		elseif ($this->installation_info["installation_type"] ==11)
		{
			//Jive
			//may not be necessary to code anything here
			return true;
		}				
		else
		{
			$this->error_message = "API ERROR - GETTING INSTALLATION CONFIGURATION INFO - 1\n";
			$this->send_admin_error_email();
			return false;
		}
	} //end of function get_installation_configuration_info

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_latent_installation()
	{
		if ($this->installation_info["db_host"] == $this->user_info["db_host"])
		{
			//check database name
			if ($this->installation_info["db_name"] == $this->user_info["db_name"])
			{
				if ($this->installation_info["installation_type"] == $this->user_info["installation_type"])
				{
					$this->latent_db_host =$this->installation_info["db_host"];
					$this->latent_db_username = $this->installation_info["db_username"];
					$this->latent_db_password = $this->installation_info["db_password"];
					$this->latent_db_name = $this->installation_info["db_name"];
					$this->latent_installation_type = $this->installation_info["installation_type"];

					if ($this->debug)
					{
						echo "this is latent installation<br>\n";
						echo $this->installation_info["db_host"]." is latent db_host in check_latent_installation<br>\n";
						echo $this->installation_info["db_username"]." is latent db_username in check_latent_installation<br>\n";
						echo $this->installation_info["db_name"]." is latent db_name in check_latent_installation<br>\n";
						echo $this->installation_info["installation_type"]." is latent installation_type in check_latent_installation<br>\n";
					}
					//$this->error_message = "API ERROR - CHECKING LATENT INFORMATION - 1\n";
					//$this->send_admin_error_email();
					return false;
				}
				else
				{
					if ($this->debug) echo "this is not the latent installation - installation_type difference - ".$this->installation_info["installation_type"]." != ".$this->user_info["installation_type"]."<br>\n";
					return true;
				}
			}
			else
			{
				//echo "this is not latent installation<br>\n";
				if ($this->debug) echo "this is not the latent installation - db_name - ".$this->installation_info["db_name"]." != ".$this->user_info["db_name"]."<br>\n";
				return true;
			}
		}
		else
		{
			if ($this->debug) echo "this is not the latent installation - db_host - ".$this->installation_info["db_host"]." != ".$this->user_info["db_host"]."<br>\n";
			return true;
		}
	} // end of function check_latent_installation

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_vBulletin_vboptions($tableprefix=0)
	{
		//get vboptions array
		$this->sql_query = "SELECT varname,value FROM " . $tableprefix . "setting";
		$get_vboptions_result = $this->installation_db->Execute($this->sql_query);
		if ($this->debug) echo $this->sql_query."<bR>\n";
		if (($get_vboptions_result) && ($get_vboptions_result->RecordCount()))
		{
			while ($setting = $get_vboptions_result->FetchRow())
			{
				$this->vboptions[$setting["varname"]] = $setting["value"];
				//echo $setting["varname"]." is ".$setting["value"]."<br>\n";
			}
			return true;
		}
		else
		{
			if ($this->debug) echo "error getting vBulletin vboptions<bR>\n";
			return false;
		}

		//$this->sql_query = "SELECT title, data FROM " . $tableprefix . "datastore WHERE title IN ('options')";
		//$this->vboptions_result = $this->installation_db->Execute($this->sql_query);
		//if (!$this->vboptions_result)
		//{
		//	return false;
		//}
		//elseif ($this->vboptions_result->RecordCount() == 1)
		//{
		//	$storeitem = $this->vboptions_result->FetchNextObject;
		//	$this->vboptions = unserialize($storeitem->DATA);
		//	return true;
		//}
		//else
		//	return false;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function iif($expression, $returntrue, $returnfalse = '')
	{
		//used within vBulletin only
		return ($expression ? $returntrue : $returnfalse);
	}//end of function iif

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function bitwise($value, $bitfield)
	{
		//used within vBulletin only
		// Do not change this to return true/false!
		// Returns 1 if the bitwise is successful, 0 other wise
		return iif(intval($value) & $bitfield, 1, 0);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function vbsetcookie($name, $value = '', $permanent = 1,$cookieprefix)
	{
		//this is used from vBulletin only
		if ($this->debug) echo $permanent." is permanent setting for cookie<br>\n";
		if ($permanent == 1)
		{
			$expire = time() + 60 * 60 * 24 * 365;
		}
		elseif ($permanent == 2)
		{
			//remove the cookie
			$expire = time() - 86400;
		}
		else
		{
			$expire = 0;
		}

		if ($_SERVER['SERVER_PORT'] == '443')
		{
			// we're using SSL
			$secure = 1;
		}
		else
		{
			$secure = 0;
		}

		$name = $cookieprefix . $name;

		$filename = 'N/A';
		$linenum = 0;
		
		if (strlen(trim($this->vboptions['cookiedomain'])) == 0)
		{
			$this->vboptions['cookiedomain'] = $_SERVER["HTTP_HOST"];
			
			if ($this->vboptions['cookiedomain'] != $_SERVER["SERVER_ADDR"])
			{
				$dotpos = strpos($this->vboptions['cookiedomain'], '.');
				$this->vboptions['cookiedomain'] = substr($this->vboptions['cookiedomain'], $dotpos + 1);
			}			
			
		}

		if ($this->debug)
		{
			echo $this->vboptions['cookiepath']." is the cookiepath<br>\n";
			echo $this->vboptions['cookiedomain']." is the cookie domain<bR>\n";
		}		
		
		// consider showing an error message if there not sent using above variables?
		if (substr($this->vboptions['cookiepath'], -1, 1) != '/')
			$this->vboptions['cookiepath'] == "/";
		$alldirs = '';
		if ($value == '' AND strlen($this->vboptions['cookiepath']) > 1 AND strpos($this->vboptions['cookiepath'], '/') !== false)
		{
			// this will attempt to unset the cookie at each directory up the path.
			// ie, cookiepath = /test/vb3/. These will be unset: /, /test, /test/, /test/vb3, /test/vb3/
			// This should hopefully prevent cookie conflicts when the cookie path is changed.
			$dirarray = explode('/', preg_replace('#/+$#', '', $this->vboptions['cookiepath']));
			$alldirs = '';
			foreach ($dirarray AS $thisdir)
			{
				$alldirs .= "$thisdir";
				if (!empty($thisdir))
				{ // try unsetting without the / at the end
					setcookie($name, $value, $expire, $alldirs, $this->vboptions['cookiedomain'], $secure);
				}
				$alldirs .= "/";
				setcookie($name, $value, $expire, $alldirs, $this->vboptions['cookiedomain'], $secure);
				if ($this->debug)
				{
					echo $name." is the name<BR>\n";
					echo $value." is the value<Br>\n";
					echo $expire." is expire<bR>\n";
					echo $alldirs." is alldirs<Br>\n";
					echo $this->vboptions['cookiedomain']." is the cookiedomain<Br>\n";
					echo $secure." is secure<BR>\n";
				}
			}
		}
		else
		{
			if (strlen(trim($this->installation_info["cookie_path"])) > 0)
				$cookie_path = $this->installation_info["cookie_path"];
			else
				$cookie_path = "/";
			setcookie($name, $value, $expire, $cookie_path, $this->vboptions['cookiedomain'],$secure);
			if ($this->debug)
			{
				echo $name." is the name 2<BR>\n";
				echo $value." is the value 2<Br>\n";
				echo $expire." is expire 2<bR>\n";
				echo $alldirs." is alldirs 2<Br>\n";
				echo $this->vboptions['cookiepath']." is the cookiepath 2<Br>\n";
				echo $secure." is secure 2<BR>\n";
			}
		}
		return true;
	} //end of function vbsetcookie

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function vbulletin_htmlspecialchars_uni($text)
	{
		// this is a version of htmlspecialchars that still allows unicode to function correctly
		$text = preg_replace('/&(?!#[0-9]+;)/si', '&amp;', $text); // translates all non-unicode entities

		return str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $text);
	}  //end of function vbulletin_htmlspecialchars

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_session()
	{
		if ($this->debug)
		{
			echo "<BR>TOP OF INSERT_SESSION<br>";
			echo $this->user_info["installation_type"]." is the user installation type<BR>\n";	
			echo $this->installation_info["installation_type"]." is the current/secondary installation type<BR>\n";	
		}
		$ip = 0;
		//check to see if the current session id can be used within the other installation
		
		//this user is not logged into the secondary installation
		//use the same session id as the current session id in the secondary installation also
		switch ($this->installation_info["installation_type"])
		{
			case 1:
			case 2:
			case 3:
			case 4:
				$session_id = $_COOKIE["classified_session"];
				if ($this->debug) echo "using classified_session cookie from the user_info passed in by the calling/latent installation 1,2,3,4<BR>\n";
				break;
			case 5:
			case 6:
				$session_id = $_COOKIE["auction_session"];
				if ($this->debug) echo "using auction_session cookie from the user_info passed in by the calling/latent installation 5,6<BR>\n";
				break;
			case 7:
				$session_id = $_COOKIE["geocore_session"];
				if ($this->debug) echo "using geocore_session cookie from the user_info passed in by the calling/latent installation 7<BR>\n";
				break;
			case 8://vBulletin
				//do all vbulletin login procedures at once here
				//the vbulletin cookie can not be used within a geo system
				if ($this->debug) echo "returning from insert_session without doing anything as this is a vBulletin installation<br>\n";
				return true;
				break;
			case 9:
				//begin Phorum
				//end Phorum
				//return here if everything is done in the other function call
				//the phorum cookie can not be used within a geo system
				if ($this->debug) echo "returning from insert_session without doing anything as this is a Phorum installation<br>\n";
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
				if ($this->debug) echo "no user installation type<BR>\n";
				return false;
		}		
		//check other database to see if this session already exists
		
		if ($this->debug)
		{
			echo "The session for this user does not exist...use the session of the current installation<bR>\n";
			echo $session_id." is the session id that will be inserted.<br>\n";
		}
		
		//if the session exists make sure it has the same user id
		//if the session user_id is 0 then set the user_id to the user_id within that installation
		//if the session user_id is not 0 then set a new session
		
		//see if a session of the same id already exists
		if ($session_id)
		{
			if ($this->debug) echo "inserting the session id from the user installation<Br>\n";
			switch ($this->installation_info["installation_type"])
			{
				case 1:
				case 2:
				case 3:
				case 4:
					$this->sql_query = "SELECT classified_session FROM geodesic_sessions WHERE classified_session = \"".$session_id."\"";
					break;
				case 5:
				case 6:
					$this->sql_query = "SELECT auction_session FROM geodesic_sessions WHERE auction_session = \"".$session_id."\"";
					break;
				case 7:
					$this->sql_query = "SELECT geocore_session FROM geocore_sessions WHERE geocore_session = \"".$session_id."\"";
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
					return true;
					break;
				case 11:
					//Jive
					return true;
					break;										
				default:
					return false;
			}
			
			//compare user_info["installation_type"] and $this->installation_info["installation_type"]
			//if both use different cookies then try to set another cookie for the different type
			$check_session_result = $this->installation_db->Execute($this->sql_query);
			if ($this->debug) echo  $this->sql_query." is the query<bR>\n";
			if (!$check_session_result)
			{
				if ($this->debug) echo  $this->sql_query." is the query with an error<bR>\n";
				return false;
			}	
			
			if ($check_session_result->RecordCount() == 1)
			{
				//the session id already exists
				//update the current session if the user id is 0
				//if session id has a user attached (user_id != 0) create a new session
				$current_session = $check_session_result->FetchNextObject();
				if ($current_session->USER_ID != 0)
				{
					//user id does not equal 0...check to see if cookie matches for both on the same session id
					//if it does then create another cookie for the same installation						
					if ($this->debug) 
					{
						echo "creating new session because a session id already exists and already has a user attached<bR>\n";
						echo $current_session->USER_ID." is the current_session->user_id<br>\n";
					}
					
					$this->create_new_session();
				}
				else 
				{
					//the session of the same session id exists but contains no user id
					//set the user id in that session to the current user_id
					switch ($this->installation_info["installation_type"])
					{
						case 1:
						case 2:
						case 3:
						case 4:
							$this->sql_query = "UPDATE geodesic_sessions SET user_id = ".$this->login_return->ID." WHERE classified_session = \"".$session_id."\"";
							break;
						case 5:
						case 6:
							$this->sql_query = "UPDATE geodesic_sessions SET user_id = ".$this->login_return->ID." WHERE auction_session = \"".$session_id."\"";
							break;
						case 7:
							$this->sql_query = "UPDATE geocore_sessions SET user_id = ".$this->login_return->ID."  WHERE geocore_session = \"".$session_id."\"";
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
							//Cerberis
							return true;
							break;
						case 11:
							//Jive
							return true;
							break;														
						default:
							return false;
					}			
					$update_session_id_result = $this->installation_db->Execute($this->sql_query);
					if ($this->debug) echo  $this->sql_query." is the query<bR>\n";
					if (!$update_session_id_result)
					{
						if ($this->debug) echo  $this->sql_query." is the query with an error<bR>\n";
						return false;
					}						
						
				}
				return true;
				
			}
			elseif ($check_session_result->RecordCount() == 0)
			{
				//the session id does not exist
				//insert the new session_id from the user_info array
				if ($this->debug)
				{
					echo "no session exists matching the current session - so using current session id<bR>\n";
					
				}
				switch ($this->installation_info["installation_type"])
				{
					case 1:
					case 2:
					case 3:
					case 4:
						//the installation type and the user_info type are the same
						$this->sql_query = "insert into geodesic_sessions
							(classified_session,user_id,last_time,ip,level)
							values
							(\"".$session_id."\",".$this->login_return->ID.",".time().",\"".$ip."\",0)";								
						break;
					case 5: //premier auctions
						//same as enterprise auctions below

					case 6: //enterprise auctions
						$this->sql_query = "insert into geodesic_sessions
							(auction_session,user_id,last_time,ip,level)
							values
							(\"".$session_id."\",".$this->login_return->ID.",".time().",\"".$ip."\",0)";								
						break;
					case 7:
						$this->sql_query = "insert into geocore_sessions
							(geocore_session,user_id,last_time,ip,level)
							values
							(\"".$session_id."\",".$this->login_return->ID.",".time().",\"".$ip."\",0)";									
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
						return true;
						break;
					case 11:
						//Jive
						return true;		
						break;										
					default:
						return false;
				}	
				$insert_new_session_result = $this->installation_db->Execute($this->sql_query);
				if ($this->debug) echo  $this->sql_query." is the query<bR>\n";
				if (!$insert_new_session_result)
				{
					if ($this->debug) echo  $this->sql_query." is the query with an error<bR>\n";
					return false;
				}	

				//set an actual cookie for the second installation
				$expires = time() + 31536000;
				switch ($this->installation_info["installation_type"])
				{
					case 1:
					case 2:
					case 3:
					case 4:
						//set a classified_session cookie
						//header("Set-Cookie: classified_session=".$session_id."; path=/; domain=".$_SERVER["HTTP_HOST"]."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
						setcookie("classified_session",$session_id,$expires,"/",$_SERVER["HTTP_HOST"]);
						//echo  "setting classified_session cookie - ".$session_id."<bR>\n";
						break;
					case 5:
					case 6:
						//set a auction_session cookie
						setcookie("auction_session",$session_id,time(),"/",$_SERVER["HTTP_HOST"]);
						//echo  "setting auction_session cookie - ".$session_id."<bR>\n";
						break;
					case 7:
						//set a geocore_session cookie
						setcookie("geocore_session",$session_id,time(),"/",$_SERVER["HTTP_HOST"]);
						//echo  "setting geocore_session cookie - ".$session_id."<bR>\n";
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
						return true;
						break;
					case 11:
						//Jive
						return true;
						break;												
					default:
						return false;
				}
			}
			else 
			{
				//goofy things are happening it got here............
				if ($this->debug) echo  $this->sql_query." is the query with an error - that returned more than one session result<bR>\n";
				return false;	
			}		
		}
		else 
		{
			//no session_id was passed...or not found
			//create a session_id
			if ($this->debug) 
			{
				echo "creating new session because a session id already exists and already has a user attached<bR>\n";
				echo $current_session->USER_ID." is the current_session->user_id<br>\n";
				echo $session_id." is session_id<BR>\n";
			}			
			$this->create_new_session();
			if ($this->debug) echo "cookie set with ".$custom_id." and ".$this->installation_info["cookie_domain"]." - ".$this->installation_info["cookie_path"]."<BR>\n";
		}
		if ($this->debug) echo "<BR>BOTTOM OF INSERT_SESSION<br><BR>";
		return true;

	} // end of function insert_session

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end of class Api_site

?>