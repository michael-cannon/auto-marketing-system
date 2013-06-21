<? //register_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Register extends Site {

	var $debug_register = 0;

	var $registered_variables;
	var $error_found;
	var $error;
	var $username;
	var $password;
	var $hash;
	var $personal_info_check = 0;
	var $bad_registration_code = 0;
	var $registration_code_checked = 0;
	var $registration_code_use = 0;
	var $registration_group;
	var $registration_id;
	var $session_id;
	var $setup_error;
	var $registration_configuration;
	var $initial_account_balance_given = 0;
	var $filter_level_array = array();
	var $registration_filter_id;
	var $user_id;


//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Register ($db,$language_id,$session_id,$product_configuration=0)
	{
        $this->Site($db,10,$language_id,0,$product_configuration);

		$this->session_id = $session_id;
		if ($this->debug_register) echo $this->session_id." is the session_id in the constructor<BR>\n";

		$this->setup_registration_session($db);

		//check to see if there is a group with a registration code
		$this->check_groups_for_registration_code_use($db);

		$this->get_registration_configuration_data($db);

		 //delete expired registration sessions (after 24 hours)
		 $this->remove_old_sell_sessions($db);
	} //end of function Register

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function setup_registration_session($db)
	{
		if ($this->session_id)
		{
			$this->sql_query = "select * from ".$this->registration_table." where session = \"".$this->session_id."\"";
			$setup_registration_result = $db->Execute($this->sql_query);
			if ($this->debug_register) echo $this->sql_query."<bR>\n";
			if (!$setup_registration_result)
			{
				$this->body .="no select reg<br>\n";
				$this->setup_error = 1;
				return false;
			}
			elseif ($setup_registration_result->RecordCount() == 1)
			{
				//get variables from db and save in local variables
				$show = $setup_registration_result->FetchNextObject();

				$this->registration_group = $show->REGISTRATION_GROUP;
				$this->registration_code_checked = $show->REGISTRATION_CODE_CHECKED;
				$this->personal_info_check = $show->PERSONAL_INFO_CHECK;
				$this->registration_code_use = $show->REGISTRATION_CODE_USE;
				$this->registration_filter_id = $show->FILTER_ID;

				$this->registered_variables["email"] = $show->EMAIL;
				$this->registered_variables["email2"] = $show->EMAIL2;
				$this->registered_variables["email_verifier"] = $show->EMAIL_VERIFIER;
				$this->registered_variables["email_verifier2"] = $show->EMAIL_VERIFIER2;
				$this->registered_variables["username"] = $show->USERNAME;
				$this->registered_variables["password"] = $show->PASSWORD;
				$this->registered_variables["agreement"] = $show->AGREEMENT;
				$this->registered_variables["company_name"] = stripslashes(urldecode($show->COMPANY_NAME));
				$this->registered_variables["business_type"] = $show->BUSINESS_TYPE;
				$this->registered_variables["firstname"] = stripslashes(urldecode($show->FIRSTNAME));
				$this->registered_variables["lastname"] = stripslashes(urldecode($show->LASTNAME));
				$this->registered_variables["address"] = stripslashes(urldecode($show->ADDRESS));
				$this->registered_variables["address_2"] = stripslashes(urldecode($show->ADDRESS_2));
				$this->registered_variables["city"] = stripslashes(urldecode($show->CITY));
				$this->registered_variables["state"] = $show->STATE;
				$this->registered_variables["country"] = $show->COUNTRY;
				$this->registered_variables["zip"] = stripslashes(urldecode($show->ZIP));
				$this->registered_variables["phone"] = stripslashes(urldecode($show->PHONE));
				$this->registered_variables["phone_2"] = stripslashes(urldecode($show->PHONE_2));
                $this->registered_variables["phoneext"] = stripslashes(urldecode($show->PHONEEXT));
                $this->registered_variables["phoneext_2"] = stripslashes(urldecode($show->PHONEEXT_2));
				$this->registered_variables["fax"] = stripslashes(urldecode($show->FAX));
				$this->registered_variables["url"] = stripslashes(urldecode($show->URL));
				$this->registered_variables["registration_code"] = $show->REGISTRATION_CODE;

				$this->registered_variables["optional_field_1"] = stripslashes(urldecode($show->OPTIONAL_FIELD_1));
				$this->registered_variables["optional_field_2"] = stripslashes(urldecode($show->OPTIONAL_FIELD_2));
				$this->registered_variables["optional_field_3"] = stripslashes(urldecode($show->OPTIONAL_FIELD_3));
				$this->registered_variables["optional_field_4"] = stripslashes(urldecode($show->OPTIONAL_FIELD_4));
				$this->registered_variables["optional_field_5"] = stripslashes(urldecode($show->OPTIONAL_FIELD_5));
				$this->registered_variables["optional_field_6"] = stripslashes(urldecode($show->OPTIONAL_FIELD_6));
				$this->registered_variables["optional_field_7"] = stripslashes(urldecode($show->OPTIONAL_FIELD_7));
				$this->registered_variables["optional_field_8"] = stripslashes(urldecode($show->OPTIONAL_FIELD_8));
				$this->registered_variables["optional_field_9"] = stripslashes(urldecode($show->OPTIONAL_FIELD_9));
				$this->registered_variables["optional_field_10"] = stripslashes(urldecode($show->OPTIONAL_FIELD_10));
                //
                //$this->registered_variables["referrer"] = stripslashes(urldecode($referrer));
			}
			else
			{
				//create new sell session
				$this->sql_query = "insert into ".$this->registration_table."
					(session,time_started) values (\"".$this->session_id."\",".$this->shifted_time($db).")";
				$insert_sell_result = $db->Execute($this->sql_query);
				if ($this->debug_register) echo $this->sql_query."<bR>\n";
				if (!$insert_sell_result)
				{
					//$this->body .="no insert<br>\n";
					$this->setup_error = 1;
					return false;
				}
			}
		}
		else
		{
			return false;
		}
	} //end of funciton setup_registration_session

//####################################################################

	function remove_old_sell_sessions($db)
	{
		$this->sql_query = "select * from ".$this->registration_table." where time_started < ".($this->shifted_time($db) - (24 * 60 * 60));
		$get_old_sell_result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<bR>\n";
		if (!$get_old_sell_result)
		{
			return false;
		}
		elseif ($get_old_sell_result->RecordCount() > 0)
		{
			while ($show_old = $get_old_sell_result->FetchNextObject())
			{
				$this->remove_registration_session($db,$show_old->SESSION);
			}
		}

		$this->sql_query = "delete from ".$this->confirm_table." where date < ".($this->shifted_time($db) - (24 * 60 * 60 * 30));
		//$this->body .=$this->sql_query." is the query<br>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->site_error($this->sql_query,$db->ErrorMsg());
			////$this->body .=$this->sql_query." is the query<br>\n";
			$this->error["confirm"] =urldecode($this->messages[229]);
			return false;
		}
	} //end of function remove_old_sell_sessions

//####################################################################

	function remove_registration_session($db,$delete_session_id=0)
	{
		$current_session_id = 0;
		if ($delete_session_id)
			$current_session_id = $delete_session_id;
		elseif ($this->session_id)
			$current_session_id = $this->session_id;
		if ($current_session_id)
		{
			$this->sql_query = "delete from ".$this->registration_table." where session = \"".$current_session_id."\"";
			$delete_registration_result = $db->Execute($this->sql_query);
			if (!$delete_registration_result)
			{
				return false;
			}
		}
	} //end of funciton remove_registration_session

//####################################################################

	function save_form_variables ($db)
	{
		$this->sql_query = "update ".$this->registration_table." set
			email = \"".$this->registered_variables["email"]."\",
			email_verifier = \"".$this->registered_variables["email_verifier"]."\",
			email2 = \"".$this->registered_variables["email2"]."\",
			email_verifier2 = \"".$this->registered_variables["email_verifier2"]."\",
			username = \"".$this->registered_variables["username"]."\",
			password = \"".$this->registered_variables["password"]."\",
			company_name = \"".urlencode($this->registered_variables["company_name"])."\",
			firstname = \"".urlencode($this->registered_variables["firstname"])."\",
			lastname = \"".urlencode($this->registered_variables["lastname"])."\",
			address = \"".urlencode($this->registered_variables["address"])."\",
			address_2 = \"".urlencode($this->registered_variables["address_2"])."\",
			city = \"".urlencode($this->registered_variables["city"])."\",
			state = \"".$this->registered_variables["state"]."\",
			country = \"".$this->registered_variables["country"]."\",
			zip = \"".urlencode($this->registered_variables["zip"])."\",
			phone = \"".urlencode($this->registered_variables["phone"])."\",
			phone_2 = \"".urlencode($this->registered_variables["phone_2"])."\",
            phoneext = \"".urlencode($this->registered_variables["phoneext"])."\",
            phoneext_2 = \"".urlencode($this->registered_variables["phoneext_2"])."\",
			fax = \"".urlencode($this->registered_variables["fax"])."\",
			business_type = \"".$this->registered_variables["business_type"]."\",
			agreement = \"".urlencode($this->registered_variables["agreement"])."\",
			optional_field_1 = \"".urlencode($this->registered_variables["optional_field_1"])."\",
			optional_field_2 = \"".urlencode($this->registered_variables["optional_field_2"])."\",
			optional_field_3 = \"".urlencode($this->registered_variables["optional_field_3"])."\",
			optional_field_4 = \"".urlencode($this->registered_variables["optional_field_4"])."\",
			optional_field_5 = \"".urlencode($this->registered_variables["optional_field_5"])."\",
			optional_field_6 = \"".urlencode($this->registered_variables["optional_field_6"])."\",
			optional_field_7 = \"".urlencode($this->registered_variables["optional_field_7"])."\",
			optional_field_8 = \"".urlencode($this->registered_variables["optional_field_8"])."\",
			optional_field_9 = \"".urlencode($this->registered_variables["optional_field_9"])."\",
			optional_field_10 = \"".urlencode($this->registered_variables["optional_field_10"])."\",
			url = \"".urlencode($this->registered_variables["url"])."\"
			where session = \"".$this->session_id."\"";
		$save_registered_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$save_registered_result)
		{
			//$this->body .=$this->sql_query."<br>\n";
			return false;
		}

	} //end of function save_form_variables

//####################################################################

    /**
     * Verify that $email meets requirements specified by regular expression.
     * Store various parts in $check_pieces array and then checks to see that
     * the top level domain is valid, but not the username itself.
     *
     * strstr() returns all of first parameter found after second parameter.
     * substr() returns all of the string found between the first and second
     * parameters.
     * getmxrr() verifies that domain MX record exists.
     * checkdnsrr() checks DNS's not MX'd.
     *
     * Resource:
     * 1. Gilmore, W.J. PHP Networking. April 5, 2001.
     *    http://www.onlamp.com/lpt/a//php/2001/04/05/networking.html.
     *    wj@wjgilmore.com.
     *
     * @param string $email
     * @param boolean $check_mx verify mail exchange or DNS records
     * @return boolean true if valid e-mail address
     */
    function cb_is_email($email, $check_mx = true)
    {
        // all characters except @ and whitespace
        $name = '[^@\s]+';

        // letters, numbers, hyphens separated by a period
        $sub_domain = '[-a-z0-9]+\.';

        // country codes
        $cc = '[a-z]{2}';

        // top level domains
        $tlds =
        "$cc|com|net|edu|org|gov|mil|int|biz|pro|info|arpa|aero|coop|name|museum";

        $email_pattern = "/^$name@($sub_domain)+($tlds)$/ix";

        if ( preg_match($email_pattern, $email, $check_pieces) )
        {
            // check mail exchange or DNS
            if ( $check_mx )
            {
                $host = substr(strstr($check_pieces[0], '@'), 1).".";

                if ( getmxrr($host, $validate_email_temp) )
                {
                    return true;
                }
                else
                {
                    $this->error[email] = "error5";
                    $this->error_found++;
                    return false;
                }

                // THIS WILL CATCH DNSs THAT ARE NOT MX.
                if ( checkdnsrr($host, 'ANY') )
                {
                    $this->error[email] = "error5";
                    $this->error_found++;
                    return true;
                }
            }
        }
        $this->error[email] = "error3";
        $this->error_found++;
        return false;
    }


	function check_info($db,$info=0)
	{
		if ($info)
			$this->save_variables($info);

		$this->save_form_variables($db);

		$this->error = array();
		$this->error_found = 0;

		if (($this->registration_configuration->USE_REGISTRATION_COMPANY_NAME_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_COMPANY_NAME_FIELD))
		{
			if (strlen(trim($this->registered_variables[company_name])) == 0) {
				$this->error[company_name] ="error";
				$this->error_found++;
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_FIRSTNAME_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_FIRSTNAME_FIELD))
		{
			if (strlen(trim($this->registered_variables[firstname])) == 0)
			{
				$this->error[firstname] ="error";
				$this->error_found++;
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_LASTNAME_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_LASTNAME_FIELD))
		{
			if (strlen(trim($this->registered_variables[lastname])) == 0 )
			{
				$this->error[lastname] ="error";
				$this->error_found++;
			}
  		}

		if (($this->registration_configuration->USE_REGISTRATION_ADDRESS_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_ADDRESS_FIELD))
		{
			if (strlen(trim($this->registered_variables[address]))== 0 ) {
				$this->error[address] ="error";
				$this->error_found++;
			}
		}
		if (($this->registration_configuration->USE_REGISTRATION_ADDRESS2_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_ADDRESS2_FIELD))
		{
			if (strlen(trim($this->registered_variables[address_2]))== 0 ) {
				$this->error[address_2] ="error";
				$this->error_found++;
			}
		}

		if (strlen(trim($this->registered_variables[email])) > 0)
		{
			//Cheking the email address
			if ($this->cb_is_email($this->registered_variables[email], 1))
			{
				$tok = strtok($this->registered_variables[email],"@");
				$this->sql_query = "select * from ".$this->block_email_domains. " where domain = \"".$tok."\"";
				if ($this->debug_register)
					echo $this->sql_query."<br>\n";
				//Cheking the domain
                $domain_result = $db->Execute($this->sql_query);
				if (!$domain_result)
				{
					$this->error["registration"] ="error";
					return false;
				}
				elseif($domain_result->RecordCount() == 0)
				{
					$this->sql_query = "select id from ".$this->userdata_table." where email = \"".$this->registered_variables[email]."\"";
					$email_result = $db->Execute($this->sql_query);
					if ($this->debug_register)
						echo $this->sql_query."<br>\n";
					if (!$email_result)
					{
						$this->error["registration"] ="error";
						return false;
					}
					elseif ($email_result->RecordCount() > 0)
					{
						//email already in use
						$this->error[email] ="error3";
						$this->error_found++;
					}
				}
				elseif ($domain_result->RecordCount() > 0)
				{
					$this->error["email"] = "error4";
					$this->error_found++;
					//echo "Email is being sent from domain that is blocked<bR>";
					//return false;
				}
			}
			else
			{
				if(!$this->error[email])
                {
                	$this->error[email] ="error2";
					$this->error_found++;
                }
			}
		}
		else
		{
        	if(!$this->error[email])
            {
				$this->error[email] ="error1";
				$this->error_found++;
        	}
        }

		if ((strlen(trim($this->registered_variables["email"])) > 0) && (strlen(trim($this->registered_variables["email_verifier"])) > 0))
		{
			if (strcmp(trim($this->registered_variables["email"]), trim($this->registered_variables["email_verifier"])) !== 0)
			{
				$this->error[email] ="error4";
				$this->error_found++;
			}
		}
		else
		{
			$this->error[email] ="error4";
			$this->error_found++;
		}

		if (($this->registration_configuration->USE_REGISTRATION_EMAIL2_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_EMAIL2_FIELD))
		{
			if (strlen(trim($this->registered_variables[email2])) > 0)
			{
				if (eregi("^([a-z0-9]+)(([a-z0-9._-]+))*[@]([a-z0-9]+)([._-]([a-z0-9]+))*[.]([a-z0-9]){2}([a-z0-9])?$", $this->registered_variables[email2]))
				{
					$this->sql_query = "select id from ".$this->userdata_table." where email = \"".$this->registered_variables[email2]."\"";
					$email_result2 = $db->Execute($this->sql_query);
					if (!$email_result2)
					{
						//$this->body .=$this->sql_query." is the id check query<br>\n";
						$this->error["registration"] ="error";
						return false;
					}
					elseif ($email_result2->RecordCount() > 0)
					{
						//email already in use
						$this->error[email2] ="error3";
						$this->error_found++;
					}
				}
				else
				{
					$this->error[email2] ="error2";
					$this->error_found++;
				}
			}
			else
			{
				$this->error[email2] ="error1";
				$this->error_found++;
			}
			if (strlen(trim($this->registered_variables[email2])) != strlen(trim($this->registered_variables[email_verifier2])))
			{
				$this->error[email2] ="error4";
				$this->error_found++;
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_CITY_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_CITY_FIELD))
		{
			if (strlen(trim($this->registered_variables[city])) == 0 )
			{
				$this->error[city] ="error";
				$this->error_found++;
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_STATE_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_STATE_FIELD))
		{
			if ($this->registered_variables[state] == "none" )
			{
				$this->error[state] ="error";
				$this->error_found++;
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_COUNTRY_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_COUNTRY_FIELD))
		{
			if ($this->registered_variables[country] == "none" )
			{
				$this->error[country] ="error";
				$this->error_found++;
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_BUSINESS_TYPE_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_BUSINESS_TYPE_FIELD))
		{
			if ($this->registered_variables[business_type] == 0)
			{
				$this->error[business_type] ="error";
				$this->error_found++;
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_ZIP_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_ZIP_FIELD))
		{
			if (strlen(trim($this->registered_variables[zip])) == 0 )
			{
				$this->error[zip] ="error";
				$this->error_found++;
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_PHONE_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_PHONE_FIELD))
		{
			if (strlen(trim($this->registered_variables[phone])) == 0 )
			{
				$this->error[phone] ="error";
				$this->error_found++;
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_PHONE2_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_PHONE2_FIELD))
		{
			if (strlen(trim($this->registered_variables[phone_2])) == 0 )
			{
				$this->error[phone_2] = "error";
				$this->error_found++;
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_FAX_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_FAX_FIELD))
		{
			if (strlen(trim($this->registered_variables[fax])) == 0 )
			{
				$this->error[fax] ="error";
				$this->error_found++;
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_URL_FIELD) && ($this->registration_configuration->REQUIRE_REGISTRATION_URL_FIELD))
		{
			if (strlen(trim($this->registered_variables[url])) == 0 )
			{
				$this->error[url] ="error";
				$this->error_found++;
			}
		}

		if ($this->registration_configuration->OPTIONAL_1_MAXLENGTH > 0)
			$this->registered_variables["optional_field_1"] = substr($this->registered_variables["optional_field_1"],0,$this->registration_configuration->OPTIONAL_1_MAXLENGTH);

		if ($this->registration_configuration->OPTIONAL_2_MAXLENGTH > 0)
			$this->registered_variables["optional_field_2"] = substr($this->registered_variables["optional_field_2"],0,$this->registration_configuration->OPTIONAL_2_MAXLENGTH);

		if ($this->registration_configuration->OPTIONAL_3_MAXLENGTH > 0)
			$this->registered_variables["optional_field_3"] = substr($this->registered_variables["optional_field_3"],0,$this->registration_configuration->OPTIONAL_3_MAXLENGTH);

		if ($this->registration_configuration->OPTIONAL_4_MAXLENGTH > 0)
			$this->registered_variables["optional_field_4"] = substr($this->registered_variables["optional_field_4"],0,$this->registration_configuration->OPTIONAL_4_MAXLENGTH);

		if ($this->registration_configuration->OPTIONAL_5_MAXLENGTH > 0)
			$this->registered_variables["optional_field_5"] = substr($this->registered_variables["optional_field_5"],0,$this->registration_configuration->OPTIONAL_5_MAXLENGTH);

		if ($this->registration_configuration->OPTIONAL_6_MAXLENGTH > 0)
			$this->registered_variables["optional_field_6"] = substr($this->registered_variables["optional_field_6"],0,$this->registration_configuration->OPTIONAL_6_MAXLENGTH);

		if ($this->registration_configuration->OPTIONAL_7_MAXLENGTH > 0)
			$this->registered_variables["optional_field_7"] = substr($this->registered_variables["optional_field_7"],0,$this->registration_configuration->OPTIONAL_7_MAXLENGTH);

		if ($this->registration_configuration->OPTIONAL_8_MAXLENGTH > 0)
			$this->registered_variables["optional_field_8"] = substr($this->registered_variables["optional_field_8"],0,$this->registration_configuration->OPTIONAL_8_MAXLENGTH);

		if ($this->registration_configuration->OPTIONAL_9_MAXLENGTH > 0)
			$this->registered_variables["optional_field_9"] = substr($this->registered_variables["optional_field_9"],0,$this->registration_configuration->OPTIONAL_9_MAXLENGTH);

		if ($this->registration_configuration->OPTIONAL_10_MAXLENGTH > 0)
			$this->registered_variables["optional_field_10"] = substr($this->registered_variables["optional_field_10"],0,$this->registration_configuration->OPTIONAL_10_MAXLENGTH);

		if (($this->registration_configuration->USE_REGISTRATION_OPTIONAL_1_FIELD) && (!$this->registration_configuration->REGISTRATION_OPTIONAL_1_FILTER_ASSOCIATION))
		{
			if (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_1_FIELD) ||
				(($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_1_FIELD_DEP) &&
				($this->registered_variables["business_type"] == 2)))
			{
				if (strlen(trim($this->registered_variables[optional_field_1])) == 0 )
				{
					$this->error[optional_field_1] ="error";
					$this->error_found++;
				}
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_OPTIONAL_2_FIELD) && (!$this->registration_configuration->REGISTRATION_OPTIONAL_2_FILTER_ASSOCIATION))
		{
			if (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_2_FIELD) || (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_2_FIELD_DEP) && ($this->registered_variables["business_type"] == 2)))
			{
				if (strlen(trim($this->registered_variables[optional_field_2])) == 0 )
				{
					$this->error[optional_field_2] ="error";
					$this->error_found++;
				}
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_OPTIONAL_3_FIELD) && (!$this->registration_configuration->REGISTRATION_OPTIONAL_3_FILTER_ASSOCIATION))
		{
			if (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_3_FIELD) || (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_3_FIELD_DEP) && ($this->registered_variables["business_type"] == 2)))
			{
				if (strlen(trim($this->registered_variables[optional_field_3])) == 0 )
				{
					$this->error[optional_field_3] ="error";
					$this->error_found++;
				}
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_OPTIONAL_4_FIELD) && (!$this->registration_configuration->REGISTRATION_OPTIONAL_4_FILTER_ASSOCIATION))
		{
			if (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_4_FIELD) || (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_4_FIELD_DEP) && ($this->registered_variables["business_type"] == 2)))
			{
				if (strlen(trim($this->registered_variables[optional_field_4])) == 0 )
				{
					$this->error[optional_field_4] ="error";
					$this->error_found++;
				}
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_OPTIONAL_5_FIELD) && (!$this->registration_configuration->REGISTRATION_OPTIONAL_5_FILTER_ASSOCIATION))
		{
			if (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_5_FIELD) || (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_5_FIELD_DEP) && ($this->registered_variables["business_type"] == 2)))
			{
				if (strlen(trim($this->registered_variables[optional_field_5])) == 0 )
				{
					$this->error[optional_field_5] ="error";
					$this->error_found++;
				}
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_OPTIONAL_6_FIELD) && (!$this->registration_configuration->REGISTRATION_OPTIONAL_6_FILTER_ASSOCIATION))
		{
			if (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_6_FIELD) || (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_6_FIELD_DEP) && ($this->registered_variables["business_type"] == 2)))
			{
				if (strlen(trim($this->registered_variables[optional_field_6])) == 0 )
				{
					$this->error[optional_field_6] ="error";
					$this->error_found++;
				}
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_OPTIONAL_7_FIELD) && (!$this->registration_configuration->REGISTRATION_OPTIONAL_7_FILTER_ASSOCIATION))
		{
			if (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_7_FIELD) || (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_7_FIELD_DEP) && ($this->registered_variables["business_type"] == 2)))
			{
				if (strlen(trim($this->registered_variables[optional_field_7])) == 0 )
				{
					$this->error[optional_field_7] ="error";
					$this->error_found++;
				}
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_OPTIONAL_8_FIELD) && (!$this->registration_configuration->REGISTRATION_OPTIONAL_8_FILTER_ASSOCIATION))
		{
			if (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_8_FIELD) || (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_8_FIELD_DEP) && ($this->registered_variables["business_type"] == 2)))
			{
				if (strlen(trim($this->registered_variables[optional_field_8])) == 0 )
				{
					$this->error[optional_field_8] ="error";
					$this->error_found++;
				}
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_OPTIONAL_9_FIELD) && (!$this->registration_configuration->REGISTRATION_OPTIONAL_9_FILTER_ASSOCIATION))
		{
			if (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_9_FIELD) || (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_9_FIELD_DEP) && ($this->registered_variables["business_type"] == 2)))
			{
				if (strlen(trim($this->registered_variables[optional_field_9])) == 0 )
				{
					$this->error[optional_field_9] ="error";
					$this->error_found++;
				}
			}
		}

		if (($this->registration_configuration->USE_REGISTRATION_OPTIONAL_10_FIELD) && (!$this->registration_configuration->REGISTRATION_OPTIONAL_10_FILTER_ASSOCIATION))
		{
			if (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_10_FIELD) || (($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_10_FIELD_DEP) && ($this->registered_variables["business_type"] == 2)))
			{
				if (strlen(trim($this->registered_variables[optional_field_10])) == 0 )
				{
					$this->error[optional_field_10] ="error";
					$this->error_found++;
				}
			}
		}

		$this->check_username($db,$this->registered_variables["username"]);
		$this->check_password($this->registered_variables["password"],$this->registered_variables["password_confirm"]);
		$this->check_agreement($this->registered_variables["agreement"]);
        if ($this->debug_register)
		{
			echo $this->error_found." is error in check<bR>\n";
			reset($this->error);
			foreach ($this->error as $key => $value)
				echo $key." is key to ".$value."<bR>\n";
		}
		if ($this->error_found > 0)
			return false;
		else
		{
			$this->update_personal_info_check($db,1);
			return true;
		}

	} //end of function check_info($info)

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function save_variables($info) {

		$this->registered_variables["company_name"] = stripslashes($info[company_name]);
		$this->registered_variables["business_type"] = stripslashes($info[business_type]);
   		$this->registered_variables["phone"] = stripslashes($info[phone]);
   		$this->registered_variables["phone_2"] = stripslashes($info[phone_2]);
        $this->registered_variables["phoneext"] = stripslashes($info[phoneext]);
        $this->registered_variables["phoneext_2"] = stripslashes($info[phoneext_2]);
   		$this->registered_variables["zip"] = stripslashes($info[zip]);
   		$this->registered_variables["state"] = stripslashes($info[state]);
   		$this->registered_variables["city"] = stripslashes($info[city]);
   		$this->registered_variables["email"] = stripslashes($info[email]);
   		$this->registered_variables["email_verifier"] = stripslashes($info[email_verifier]);
   	   	$this->registered_variables["email2"] = stripslashes($info[email2]);
   		$this->registered_variables["email_verifier2"] = stripslashes($info[email_verifier2]);
   		$this->registered_variables["address"] = stripslashes($info[address]);
   		$this->registered_variables["address_2"] = stripslashes($info[address_2]);
   		$this->registered_variables["firstname"] = stripslashes($info[firstname]);
   		$this->registered_variables["lastname"] = stripslashes($info[lastname]);
   		$this->registered_variables["fax"] = stripslashes($info[fax]);
   		$this->registered_variables["url"] = stripslashes($info[url]);
   		$this->registered_variables["country"] = stripslashes($info[country]);
   		$this->registered_variables["username"] = $info[username];
   		$this->registered_variables["password"] = $info[password];
   		$this->registered_variables["password_confirm"] = $info[password_confirm];
   		$this->registered_variables["agreement"] = $info[agreement];
   		$this->registered_variables["optional_field_1"] = stripslashes($info[optional_field_1]);
   		$this->registered_variables["optional_field_2"] = stripslashes($info[optional_field_2]);
   		$this->registered_variables["optional_field_3"] = stripslashes($info[optional_field_3]);
   		$this->registered_variables["optional_field_4"] = stripslashes($info[optional_field_4]);
   		$this->registered_variables["optional_field_5"] = stripslashes($info[optional_field_5]);
   		$this->registered_variables["optional_field_6"] = stripslashes($info[optional_field_6]);
   		$this->registered_variables["optional_field_7"] = stripslashes($info[optional_field_7]);
   		$this->registered_variables["optional_field_8"] = stripslashes($info[optional_field_8]);
   		$this->registered_variables["optional_field_9"] = stripslashes($info[optional_field_9]);
   		$this->registered_variables["optional_field_10"] =stripslashes($info[optional_field_10]);
        $referrer = $_COOKIE['referrer'];
        //$this->registered_variables["referrer"] = stripslashes(urldecode($referrer));
   	} //end of function save_variables() {

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function registration_form_1($db)
	{
    	$this->debug_register = 0;
		$registration_url = ($this->configuration_data['use_ssl_in_registration'])?$this->configuration_data['registration_ssl_url']:$this->configuration_data['registration_url'];
		if ($this->debug_register)
		{
			echo "hello from the top of registration_form_1<bR>\n";
		}
		$this->page_id = 15;
		$this->get_text($db);

		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr>\n\t<td>\n\t<form name=registration_form action=".$registration_url."?b=1 method=post>\n\t";
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n\t";
		$this->body .="<tr class=section_title>\n\t\t<td colspan=3>".urldecode($this->messages[614])."</td>\n\t</tr>\n\t";
		$this->body .="<tr class=page_title>\n\t\t<td colspan=3>".urldecode($this->messages[239])."</td>\n\t</tr>\n\t";
		$this->body .="<tr class=page_description>\n\t\t<td colspan=3>".urldecode($this->messages[245])."</td>\n\t</tr>\n\t";

		if ($this->registration_configuration->USE_REGISTRATION_FIRSTNAME_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[258]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_FIRSTNAME_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t
				<input type=text name=c[firstname] value=\"".$this->registered_variables["firstname"]."\" size=".$this->registration_configuration->FIRSTNAME_MAXLENGTH." maxlength=".$this->registration_configuration->FIRSTNAME_MAXLENGTH." class=data_field_values>";

			$this->body .= "\n\t\t</td>\n\t\t";
			$this->body .="<td >&nbsp;";
			if (isset($this->error[firstname]))
				$this->body .="<font class=error_message>".urldecode($this->messages[267])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_LASTNAME_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[259]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_LASTNAME_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t
				<input type=text name=c[lastname] value=\"".$this->registered_variables["lastname"]."\" size=".$this->registration_configuration->LASTNAME_MAXLENGTH." maxlength=".$this->registration_configuration->LASTNAME_MAXLENGTH." class=data_field_values>";
			$this->body .= "\n\t\t</td>\n\t\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[lastname]))
				$this->body .="<font class=error_message>".urldecode($this->messages[268])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_COMPANY_NAME_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[248]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_COMPANY_NAME_FIELD)
				$this->body .="*";
			$this->body .= "</td>\n\t\t\t";
			$this->body .="<td class=data_field_values>\n\t\t
			 	<input type=text name=c[company_name] value=\"".$this->registered_variables["company_name"]."\" size=";
			if ($this->registration_configuration->COMPANY_NAME_MAXLENGTH > 30)
				$this->body .= "30";
			else
				$this->body .= $this->registration_configuration->COMPANY_NAME_MAXLENGTH;
			$this->body .= " maxlength=".$this->registration_configuration->COMPANY_NAME_MAXLENGTH." class=data_field_values>";

			$this->body .= "</td>\n\t\t<td>&nbsp;";
			if (isset($this->error[company_name]))
				$this->body .="<font class=error_message>".urldecode($this->messages[266])."</font>";
			$this->body .="&nbsp;</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_BUSINESS_TYPE_FIELD)
		{
			$this->body .="<tr><td class=data_field_labels>".urldecode($this->messages[769]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_BUSINESS_TYPE_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t";
			$this->body .="<td class=data_field_values>\n\t\t
				<input type=radio name=c[business_type] value=1";
			 if ($this->registered_variables[business_type] == 1) $this->body .=" checked";
			$this->body .="  class=data_field_values>".urldecode($this->messages[247])."<br>
			 	<input type=radio name=c[business_type] value=2 ";
			if ($this->registered_variables[business_type] == 2) $this->body .=" checked";
			$this->body .="  class=data_field_values> ".urldecode($this->messages[246]);

			$this->body .= "\n\t\t</td><td>&nbsp;";

			if (isset($this->error[business_type]))
				$this->body .="<font class=error_message>".urldecode($this->messages[772])."</font>";
			$this->body .= "</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_ADDRESS_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[249]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_ADDRESS_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t<input type=text name=c[address] value=\"";
			$this->body .=$this->registered_variables["address"];
			$this->body .="\" size=".$this->registration_configuration->ADDRESS_MAXLENGTH." maxlength=".$this->registration_configuration->ADDRESS_MAXLENGTH." class=data_field_values> ";

			$this->body .="\n\t\t</td>\n\t\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[address]))
				$this->body .="<font class=error_message>".urldecode($this->messages[269])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_ADDRESS2_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[250]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_ADDRESS2_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t<input type=text name=c[address_2] value=\"";
			$this->body .=$this->registered_variables["address_2"];
			$this->body .="\" size=".$this->registration_configuration->ADDRESS_2_MAXLENGTH." maxlength=".$this->registration_configuration->ADDRESS_2_MAXLENGTH." class=data_field_values> ";

			$this->body .="\n\t\t</td>\n\t\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[address_2]))
				$this->body .="<font class=error_message>".urldecode($this->messages[269])."</font>";
			$this->body .= "\n\t\t</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_CITY_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[251]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_CITY_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t
				<input type=text name=c[city] value=\"".$this->registered_variables["city"]."\" size=".$this->registration_configuration->CITY_MAXLENGTH." maxlength=".$this->registration_configuration->CITY_MAXLENGTH." class=data_field_values> ";

			$this->body .="\n\t\t</td>\n\t\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[city]))
				$this->body .="<font class=error_message>".urldecode($this->messages[265])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_STATE_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[253]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_STATE_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>";
			$this->sql_query = "SELECT * FROM ".$this->states_table." order by display_order, name";
			$state_result = $db->Execute($this->sql_query);
			if (!$state_result)
			{
				if ($this->debug_register)
				{
					echo $this->sql_query."<br>\n";
					echo "error getting states<bR>\n";
				}
				return false;
			}
			else
			{
				$this->body .="<select name=c[state] class=data_field_values>\n\t\t";
				$this->body .="<option value=none>".urldecode($this->messages[1208])."</option>\n\t\t";
				while ($show = $state_result->FetchNextObject())
				{
					//spit out the state list
					$this->body .="<option value=\"".$show->ABBREVIATION."\"";
					if (($this->registered_variables["state"] == $show->ABBREVIATION)|| ($this->registered_variables["state"] == $show->NAME))
					$this->body .="selected";
					$this->body .=">".$show->NAME."\n\t\t";
				}
				$this->body .="</select>\n\t";
			}

			$this->body .="</td>\n\t\t";
			$this->body .="<td>&nbsp;";

			if (isset($this->error[state]))
				$this->body .="<font class=error_message>".urldecode($this->messages[262])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_ZIP_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[254]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_ZIP_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t
				<input type=text name=c[zip] value=\"".$this->registered_variables["zip"]."\" size=".$this->registration_configuration->ZIP_MAXLENGTH." maxlength=".$this->registration_configuration->ZIP_MAXLENGTH." class=data_field_values> ";

			$this->body .="\n\t\t</td>\n\t\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[zip]))
				$this->body .="<font class=error_message>".urldecode($this->messages[273])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_COUNTRY_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[252]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_COUNTRY_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>";
			$this->sql_query = "SELECT * FROM ".$this->countries_table." order by display_order, name";
			$country_result = $db->Execute($this->sql_query);
			if (!$country_result)
			{
				if ($this->debug_register)
				{
					echo $this->sql_query."<br>\n";
					echo "error getting countries<bR>\n";
				}
				return false;
			}
			else
			{
				$this->body .="<select name=c[country] class=data_field_values>\n\t\t";
				$this->body .="<option value=none>".urldecode($this->messages[1209])."</option>\n\t\t";
				while ($show = $country_result->FetchNextObject())
				{
					//spit out the country list
					$this->body .="<option ";
					if (($this->registered_variables["country"] == $show->ABBREVIATION) || ($this->registered_variables["country"] == $show->NAME))
					$this->body .="selected";
					$this->body .=">".$show->NAME."\n\t\t";
				}
				$this->body .="</select>\n\t";
			}

			$this->body .="</td>\n\t<td>&nbsp;";

			if (isset($this->error[country]))
				$this->body .="<font class=error_message>".urldecode($this->messages[263])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_PHONE_FIELD)
		{
			$this->body .="<tr>\n\t<td class=data_field_labels>".urldecode($this->messages[255]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_PHONE_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t
				<input type=text name=c[phone] value=\"".$this->registered_variables["phone"]."\" size=14 maxlength=".$this->registration_configuration->PHONE_MAXLENGTH." class=data_field_values>"." Ext. <input type=text name=c[phoneext] value=\"".$this->registered_variables["phoneext"]."\" size=9 maxlength=6 class=data_field_values>";

			$this->body .="\n\t\t</td>\n\t\t";
			$this->body .="<td>&nbsp;";

			if (isset($this->error[phone]))
				$this->body .="<font class=error_message>".urldecode($this->messages[274])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";

            //Extention phone
            //$this->body .="<tr>\n\t<td class=data_field_labels>Extension: ";
            //$this->body .="</td>\n\t\t";
            //$this->body .="<td class=data_field_values>\n\t\t
            //   <input type=text name=c[phoneext] value=\"".$this->registered_variables["phoneext"]."\" size=9 maxlength=6 class=data_field_values>";

            //$this->body .="\n\t\t</td>\n\t\t";
            //$this->body .="<td>&nbsp</td>\n\t</tr>\n\t";
		}

		//<input type=text name=c[phoneext] value=\"".$this->registered_variables["phoneext"]."\" size=9 maxlength=6 class=data_field_values>

		if ($this->registration_configuration->USE_REGISTRATION_PHONE2_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[256]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_PHONE2_FIELD)
				$this->body .="*";
			$this->body .="\n\t\t</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t
				<input type=text name=c[phone_2] value=\"".$this->registered_variables["phone_2"]."\" size=14 maxlength=".$this->registration_configuration->PHONE_2_MAXLENGTH." class=data_field_values>"." Ext. <input type=text name=c[phoneext_2] value=\"".$this->registered_variables["phoneext_2"]."\" size=9 maxlength=6 class=data_field_values>";

			$this->body .="\n\t\t</td>\n\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[phone_2]))
				$this->body .="<font class=error_message>".urldecode($this->messages[275])."</font>";
			$this->body .="</td></tr>\n\t";

            //Extention phone
            //$this->body .="<tr>\n\t<td class=data_field_labels>Extension:";
            //$this->body .="</td>\n\t\t";
            //$this->body .="<td class=data_field_values>\n\t\t
            //    <input type=text name=c[phoneext_2] value=\"".$this->registered_variables["phoneext_2"]."\" size=9 maxlength=6 class=data_field_values>";

            //$this->body .="\n\t\t</td>\n\t\t";
            //$this->body .="<td>&nbsp</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_FAX_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[257]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_FAX_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t
				<input type=text name=c[fax] value=\"".$this->registered_variables["fax"]."\" size=".$this->registration_configuration->FAX_MAXLENGTH." maxlength=".$this->registration_configuration->FAX_MAXLENGTH." class=data_field_values> ";

			$this->body .="\n\t\t</td>\n\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[fax]))
				$this->body .="<font class=error_message>".urldecode($this->messages[276])."</font>";
			$this->body .="</td></tr>\n\t";
		}

		$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[260])."*</td>\n\t\t";
		$this->body .="<td class=data_field_values>\n\t\t
			<input type=text name=c[email] value=\"".$this->registered_variables["email"]."\" size=30 maxlength=50 class=data_field_values>\n\t\t</td>\n\t\t";
		$this->body .="<td align=left>&nbsp;";
		if (isset($this->error[email]))
		{
			$this->body .="<span class=error_message>";
			switch ($this->error[email])
			{
				case "error1":
					$this->body .= urldecode($this->messages[264]);
					break;
				case "error2":
					$this->body .= urldecode($this->messages[271]);
					break;
				case "error3":
					$this->body .= urldecode($this->messages[270]);
					break;
				case "error4":
					$this->body .= urldecode($this->messages[781]);
					break;
				//Was changed on 03.04.06 BCS
                case "error5":
                	$this->body .= "Though your email address might be valid, we are unable to verify it at your mail server. As such, please try another";
                    break;
			}
			$this->body .= "</span>";
		}
		$this->body .="</td>\n\t</tr>\n\t";

		$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[761])."*</td>\n\t\t";
		$this->body .="<td class=data_field_values>\n\t\t
			<input type=text name=c[email_verifier] value=\"".$this->registered_variables["email_verifier"]."\" size=30 maxlength=100 class=data_field_values>\n\t\t</td>\n\t\t";
		$this->body .="<td>&nbsp;</td>\n\t</tr>\n\t";

		if ($this->registration_configuration->USE_REGISTRATION_EMAIL2_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[1240]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_EMAIL2_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t
				<input type=text name=c[email2] value=\"".$this->registered_variables["email2"]."\" size=30 maxlength=50 class=data_field_values>";

			$this->body .= "\n\t\t</td>\n\t\t";
			$this->body .="<td align=left>&nbsp;";
			if (isset($this->error[email2]))
			{
				$this->body .="<span class=error_message>";
				switch ($this->error[email2])
				{
					case "error1":
						$this->body .= urldecode($this->messages[264]);
						break;
					case "error2":
						$this->body .= urldecode($this->messages[271]);
						break;
					case "error3":
						$this->body .= urldecode($this->messages[270]);
						break;
					case "error4":
						$this->body .= urldecode($this->messages[781]);
						break;
				}
				$this->body .= "</span>";
			}
			$this->body .="</td>\n\t</tr>\n\t";

			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[761]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_EMAIL2_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t
				<input type=text name=c[email_verifier2] value=\"".$this->registered_variables["email_verifier2"]."\" size=30 maxlength=50 class=data_field_values>";

			$this->body .= "\n\t\t</td>\n\t\t";
			$this->body .="<td>&nbsp;</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_URL_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[261]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_URL_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t
				<input type=text name=c[url] value=\"".$this->registered_variables["url"]."\" size=";
			if ($this->registration_configuration->URL_MAXLENGTH > 30)
				$this->body .= "30";
			else
				$this->body .= $this->registration_configuration->URL_MAXLENGTH;
			$this->body .= " maxlength=".$this->registration_configuration->URL_MAXLENGTH." class=data_field_values> ";

			$this->body .="\n\t\t</td>\n\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[url]))
				$this->body .="<font class=error_message>".urldecode($this->messages[277])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}

		//optional fields instructions
		if (strlen(urldecode($this->messages[1217])) > 0)
		{
			$this->body .=  "<tr class=page_description>\n\t\t<td colspan=3>".urldecode($this->messages[1217])."</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_1_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[1220]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_1_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t";
			if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_1_FILTER_ASSOCIATION))
			{
				$this->body .= $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_1_FILTER_ASSOCIATION);
				$this->body .= " <a href=".$registration_url."?b=5 class=data_field_values>".$this->messages[1528]."</a>";
			}
			else
			{

				if (!$this->registration_configuration->REGISTRATION_OPTIONAL_1_FIELD_TYPE)
				{
					$this->body .= "<input type=text name=c[optional_field_1] value=\"".$this->registered_variables["optional_field_1"]."\" ";
					if ($this->registration_configuration->OPTIONAL_1_MAXLENGTH > 30)
						$this->body .= "size=30 maxlength=".$this->registration_configuration->OPTIONAL_1_MAXLENGTH." class=data_field_values>";
					else
						$this->body .= "size=".$this->registration_configuration->OPTIONAL_1_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_1_MAXLENGTH." class=data_field_values>";
				}
				elseif($this->registration_configuration->REGISTRATION_OPTIONAL_1_FIELD_TYPE == 1)
				{
					$this->body .= "<textarea name=c[optional_field_1] ";
					$this->body .= "rows=8 cols=50 class=data_field_values>";
					$this->body .= $this->registered_variables["optional_field_1"]."</textarea>";
				}
				else
				{
					$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_1_FIELD_TYPE." order by display_order, value";
					$type_result = $db->Execute($this->sql_query);
					if (!$type_result)
					{
						return false;
					}
					elseif ($type_result->RecordCount() > 0)
					{
						$this->body .= "<select name=c[optional_field_1] class=data_field_values>";
						$matched = 0;
						while ($show_dropdown = $type_result->FetchNextObject())
						{
							$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
							if ($this->registered_variables["optional_field_1"] == $show_dropdown->VALUE)
							{
								$this->body .= "selected";
								$matched = 1;
							}
							$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
						}
						$this->body .= "</select>";
					}
					else
						//blank text box
						$this->body .= "<input type=text name=c[optional_field_1] value=\"".$this->registered_variables["optional_field_1"]."\"  size=".$this->registration_configuration->OPTIONAL_1_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_1_MAXLENGTH." class=data_field_values>";
				}
				if (($this->registration_configuration->REGISTRATION_OPTIONAL_1_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_1_FIELD_TYPE))
				{
					$this->body .= " ".urldecode($this->messages[1261])." <input type=text name=c[optional_field_1_other] value=\"";
					if (!$matched)
						$this->body .= $this->registered_variables["optional_field_1"];
					$this->body .= "\" size=15 maxlength=".$this->registration_configuration->OPTIONAL_1_MAXLENGTH." class=data_field_values>\n\t";
				}
			}

			$this->body .="\n\t\t</td>\n\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[optional_field_1]))
				$this->body .="<font class=error_message>".urldecode($this->messages[1221])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_2_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[1222]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_2_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t";
			if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_2_FILTER_ASSOCIATION))
			{
				$this->body .= $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_2_FILTER_ASSOCIATION);
				$this->body .= " <a href=".$registration_url."?b=5 class=data_field_values>".$this->messages[1528]."</a>";
			}
			else
			{
				if (!$this->registration_configuration->REGISTRATION_OPTIONAL_2_FIELD_TYPE)
				{
					$this->body .= "<input type=text name=c[optional_field_2] value=\"".$this->registered_variables["optional_field_2"]."\" ";
					if ($this->registration_configuration->OPTIONAL_2_MAXLENGTH > 30)
						$this->body .= "size=30 maxlength=".$this->registration_configuration->OPTIONAL_2_MAXLENGTH." class=data_field_values>";
					else
						$this->body .= "size=".$this->registration_configuration->OPTIONAL_2_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_2_MAXLENGTH." class=data_field_values>";
				}
				elseif($this->registration_configuration->REGISTRATION_OPTIONAL_2_FIELD_TYPE == 1)
				{
					$this->body .= "<textarea name=c[optional_field_2] ";
					$this->body .= "rows=8 cols=50 class=data_field_values>";
					$this->body .= $this->registered_variables["optional_field_2"]."</textarea>";
				}
				else
				{
					$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_2_FIELD_TYPE." order by display_order, value";
					$type_result = $db->Execute($this->sql_query);
					if (!$type_result)
					{
						return false;
					}
					elseif ($type_result->RecordCount() > 0)
					{
						$this->body .= "<select name=c[optional_field_2] class=data_field_values>";
						$matched = 0;
						while ($show_dropdown = $type_result->FetchNextObject())
						{
							$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
							if ($this->registered_variables["optional_field_2"] == $show_dropdown->VALUE)
							{
								$this->body .= "selected";
								$matched = 1;
							}
							$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
						}
						$this->body .= "</select>";
					}
					else
						//blank text box
						$this->body .= "<input type=text name=c[optional_field_2] value=\"".$this->registered_variables["optional_field_2"]."\"  size=".$this->registration_configuration->OPTIONAL_2_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_2_MAXLENGTH." class=data_field_values>";
				}
				if (($this->registration_configuration->REGISTRATION_OPTIONAL_2_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_2_FIELD_TYPE))
				{
					$this->body .= " ".urldecode($this->messages[1261])." <input type=text name=c[optional_field_2_other] value=\"";
					if (!$matched)
						$this->body .= $this->registered_variables["optional_field_2"];
					$this->body .= "\" size=15 maxlength=".$this->registration_configuration->OPTIONAL_2_MAXLENGTH." class=data_field_values>\n\t";
				}
			}
			$this->body .="\n\t\t</td>\n\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[optional_field_2]))
				$this->body .="<font class=error_message>".urldecode($this->messages[1223])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_3_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[1224]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_3_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t";
			if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_3_FILTER_ASSOCIATION))
			{
				$this->body .= $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_3_FILTER_ASSOCIATION);
				$this->body .= " <a href=".$registration_url."?b=5 class=data_field_values>".$this->messages[1528]."</a>";
			}
			else
			{
				if (!$this->registration_configuration->REGISTRATION_OPTIONAL_3_FIELD_TYPE)
				{
					$this->body .= "<input type=text name=c[optional_field_3] value=\"".$this->registered_variables["optional_field_3"]."\" ";
					if ($this->registration_configuration->OPTIONAL_1_MAXLENGTH > 30)
						$this->body .= "size=30 maxlength=".$this->registration_configuration->OPTIONAL_1_MAXLENGTH." class=data_field_values>";
					else
						$this->body .= "size=".$this->registration_configuration->OPTIONAL_1_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_1_MAXLENGTH." class=data_field_values>";
				}
				elseif($this->registration_configuration->REGISTRATION_OPTIONAL_3_FIELD_TYPE == 1)
				{
					$this->body .= "<textarea name=c[optional_field_3] ";
					$this->body .= "rows=8 cols=50 class=data_field_values>";
					$this->body .= $this->registered_variables["optional_field_3"]."</textarea>";
				}
				else
				{
					$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_3_FIELD_TYPE." order by display_order, value";
					$type_result = $db->Execute($this->sql_query);
					if (!$type_result)
					{
						return false;
					}
					elseif ($type_result->RecordCount() > 0)
					{
						$this->body .= "<select name=c[optional_field_3] class=data_field_values>";
						$matched = 0;
						while ($show_dropdown = $type_result->FetchNextObject())
						{
							$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
							if ($this->registered_variables["optional_field_3"] == $show_dropdown->VALUE)
							{
								$this->body .= "selected";
								$matched = 1;
							}
							$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
						}
						$this->body .= "</select>";
					}
					else
						//blank text box
						$this->body .= "<input type=text name=c[optional_field_3] value=\"".$this->registered_variables["optional_field_3"]."\"  size=".$this->registration_configuration->OPTIONAL_3_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_3_MAXLENGTH." class=data_field_values>";
				}
				if (($this->registration_configuration->REGISTRATION_OPTIONAL_3_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_3_FIELD_TYPE))
				{
					$this->body .= " ".urldecode($this->messages[1261])." <input type=text name=c[optional_field_3_other] value=\"";
					if (!$matched)
						$this->body .= $this->registered_variables["optional_field_3"];
					$this->body .= "\" size=15 maxlength=".$this->registration_configuration->OPTIONAL_3_MAXLENGTH." class=data_field_values>\n\t";
				}
			}
			$this->body .="\n\t\t</td>\n\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[optional_field_3]))
				$this->body .="<font class=error_message>".urldecode($this->messages[1225])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_4_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[1226]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_4_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t";
			if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_4_FILTER_ASSOCIATION))
			{
				$this->body .= $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_4_FILTER_ASSOCIATION);
				$this->body .= " <a href=".$registration_url."?b=5 class=data_field_values>".$this->messages[1528]."</a>";
			}
			else
			{
				if (!$this->registration_configuration->REGISTRATION_OPTIONAL_4_FIELD_TYPE)
				{
					$this->body .= "<input type=text name=c[optional_field_4] value=\"".$this->registered_variables["optional_field_4"]."\" ";
					if ($this->registration_configuration->OPTIONAL_4_MAXLENGTH > 30)
						$this->body .= "size=30 maxlength=".$this->registration_configuration->OPTIONAL_4_MAXLENGTH." class=data_field_values>";
					else
						$this->body .= "size=".$this->registration_configuration->OPTIONAL_4_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_4_MAXLENGTH." class=data_field_values>";
				}
				elseif($this->registration_configuration->REGISTRATION_OPTIONAL_4_FIELD_TYPE == 1)
				{
					$this->body .= "<textarea name=c[optional_field_4] ";
					$this->body .= "rows=8 cols=50 class=data_field_values>";
					$this->body .= $this->registered_variables["optional_field_4"]."</textarea>";
				}
				else
				{
					$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_4_FIELD_TYPE." order by display_order, value";
					$type_result = $db->Execute($this->sql_query);
					if (!$type_result)
					{
						return false;
					}
					elseif ($type_result->RecordCount() > 0)
					{
						$this->body .= "<select name=c[optional_field_4] class=data_field_values>";
						$matched = 0;
						while ($show_dropdown = $type_result->FetchNextObject())
						{
							$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
							if ($this->registered_variables["optional_field_4"] == $show_dropdown->VALUE)
							{
								$this->body .= "selected";
								$matched = 1;
							}
							$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
						}
						$this->body .= "</select>";
					}
					else
						//blank text box
						$this->body .= "<input type=text name=c[optional_field_4] value=\"".$this->registered_variables["optional_field_4"]."\"  size=".$this->registration_configuration->OPTIONAL_4_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_4_MAXLENGTH." class=data_field_values>";
				}
				if (($this->registration_configuration->REGISTRATION_OPTIONAL_4_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_4_FIELD_TYPE))
				{
					$this->body .= " ".urldecode($this->messages[1261])." <input type=text name=c[optional_field_4_other] value=\"";
					if (!$matched)
						$this->body .= $this->registered_variables["optional_field_4"];
					$this->body .= "\" size=15 maxlength=".$this->registration_configuration->OPTIONAL_4_MAXLENGTH." class=data_field_values>\n\t";
				}
			}
			$this->body .="\n\t\t</td>\n\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[optional_field_4]))
				$this->body .="<font class=error_message>".urldecode($this->messages[1227])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_5_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[1228]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_5_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t";
			if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_5_FILTER_ASSOCIATION))
			{
				$this->body .= $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_5_FILTER_ASSOCIATION);
				$this->body .= " <a href=".$registration_url."?b=5 class=data_field_values>".$this->messages[1528]."</a>";
			}
			else
			{
				if (!$this->registration_configuration->REGISTRATION_OPTIONAL_5_FIELD_TYPE)
				{
					$this->body .= "<input type=text name=c[optional_field_5] value=\"".$this->registered_variables["optional_field_5"]."\" ";
					if ($this->registration_configuration->OPTIONAL_5_MAXLENGTH > 30)
						$this->body .= "size=30 maxlength=".$this->registration_configuration->OPTIONAL_5_MAXLENGTH." class=data_field_values>";
					else
						$this->body .= "size=".$this->registration_configuration->OPTIONAL_5_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_5_MAXLENGTH." class=data_field_values>";
				}
				elseif($this->registration_configuration->REGISTRATION_OPTIONAL_5_FIELD_TYPE == 1)
				{
					$this->body .= "<textarea name=c[optional_field_5] ";
					$this->body .= "rows=8 cols=50 class=data_field_values>";
					$this->body .= $this->registered_variables["optional_field_5"]."</textarea>";
				}
				else
				{
					$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_5_FIELD_TYPE." order by display_order, value";
					$type_result = $db->Execute($this->sql_query);
					if (!$type_result)
					{
						return false;
					}
					elseif ($type_result->RecordCount() > 0)
					{
						$this->body .= "<select name=c[optional_field_5] class=data_field_values>";
						$matched = 0;
						while ($show_dropdown = $type_result->FetchNextObject())
						{
							$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
							if ($this->registered_variables["optional_field_5"] == $show_dropdown->VALUE)
							{
								$this->body .= "selected";
								$matched = 1;
							}
							$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
						}
						$this->body .= "</select>";
					}
					else
						//blank text box
						$this->body .= "<input type=text name=c[optional_field_5] value=\"".$this->registered_variables["optional_field_5"]."\"  size=".$this->registration_configuration->OPTIONAL_5_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_5_MAXLENGTH." class=data_field_values>";
				}
				if (($this->registration_configuration->REGISTRATION_OPTIONAL_5_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_5_FIELD_TYPE))
				{
					$this->body .= " ".urldecode($this->messages[1261])." <input type=text name=c[optional_field_5_other] value=\"";
					if (!$matched)
						$this->body .= $this->registered_variables["optional_field_5"];
					$this->body .= "\" size=15 maxlength=".$this->registration_configuration->OPTIONAL_5_MAXLENGTH." class=data_field_values>\n\t";
				}
			}
			$this->body .="\n\t\t</td>\n\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[optional_field_5]))
				$this->body .="<font class=error_message>".urldecode($this->messages[1229])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_6_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[1230]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_6_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t";
			if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_6_FILTER_ASSOCIATION))
			{
				$this->body .= $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_6_FILTER_ASSOCIATION);
				$this->body .= " <a href=".$registration_url."?b=5 class=data_field_values>".$this->messages[1528]."</a>";
			}
			else
			{
				if (!$this->registration_configuration->REGISTRATION_OPTIONAL_6_FIELD_TYPE)
				{
					$this->body .= "<input type=text name=c[optional_field_6] value=\"".$this->registered_variables["optional_field_6"]."\" ";
					if ($this->registration_configuration->OPTIONAL_6_MAXLENGTH > 30)
						$this->body .= "size=30 maxlength=".$this->registration_configuration->OPTIONAL_6_MAXLENGTH." class=data_field_values>";
					else
						$this->body .= "size=".$this->registration_configuration->OPTIONAL_6_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_6_MAXLENGTH." class=data_field_values>";
				}
				elseif($this->registration_configuration->REGISTRATION_OPTIONAL_6_FIELD_TYPE == 1)
				{
					$this->body .= "<textarea name=c[optional_field_6] ";
					$this->body .= "rows=8 cols=50 class=data_field_values>";
					$this->body .= $this->registered_variables["optional_field_6"]."</textarea>";
				}
				else
				{
					$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_6_FIELD_TYPE." order by display_order, value";
					$type_result = $db->Execute($this->sql_query);
					if (!$type_result)
					{
						return false;
					}
					elseif ($type_result->RecordCount() > 0)
					{
						$this->body .= "<select name=c[optional_field_6] class=data_field_values>";
						$matched = 0;
						while ($show_dropdown = $type_result->FetchNextObject())
						{
							$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
							if ($this->registered_variables["optional_field_6"] == $show_dropdown->VALUE)
							{
								$this->body .= "selected";
								$matched = 1;
							}
							$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
						}
						$this->body .= "</select>";
					}
					else
						//blank text box
						$this->body .= "<input type=text name=c[optional_field_6] value=\"".$this->registered_variables["optional_field_6"]."\"  size=".$this->registration_configuration->OPTIONAL_6_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_6_MAXLENGTH." class=data_field_values>";
				}
				if (($this->registration_configuration->REGISTRATION_OPTIONAL_6_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_6_FIELD_TYPE))
				{
					$this->body .= " ".urldecode($this->messages[1261])." <input type=text name=c[optional_field_6_other] value=\"";
					if (!$matched)
						$this->body .= $this->registered_variables["optional_field_6"];
					$this->body .= "\" size=15 maxlength=".$this->registration_configuration->OPTIONAL_6_MAXLENGTH." class=data_field_values>\n\t";
				}
			}
			$this->body .="\n\t\t</td>\n\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[optional_field_6]))
				$this->body .="<font class=error_message>".urldecode($this->messages[1231])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}
		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_7_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[1232]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_7_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t";
			if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_7_FILTER_ASSOCIATION))
			{
				$this->body .= $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_7_FILTER_ASSOCIATION);
				$this->body .= " <a href=".$registration_url."?b=5 class=data_field_values>".$this->messages[1528]."</a>";
			}
			else
			{
				if (!$this->registration_configuration->REGISTRATION_OPTIONAL_7_FIELD_TYPE)
				{
					$this->body .= "<input type=text name=c[optional_field_7] value=\"".$this->registered_variables["optional_field_7"]."\" ";
					if ($this->registration_configuration->OPTIONAL_7_MAXLENGTH > 30)
						$this->body .= "size=30 maxlength=".$this->registration_configuration->OPTIONAL_7_MAXLENGTH." class=data_field_values>";
					else
						$this->body .= "size=".$this->registration_configuration->OPTIONAL_7_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_7_MAXLENGTH." class=data_field_values>";
				}
				elseif($this->registration_configuration->REGISTRATION_OPTIONAL_7_FIELD_TYPE == 1)
				{
					$this->body .= "<textarea name=c[optional_field_7] ";
					$this->body .= "rows=8 cols=50 class=data_field_values>";
					$this->body .= $this->registered_variables["optional_field_7"]."</textarea>";
				}
				else
				{
					$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_7_FIELD_TYPE." order by display_order, value";
					$type_result = $db->Execute($this->sql_query);
					if (!$type_result)
					{
						return false;
					}
					elseif ($type_result->RecordCount() > 0)
					{
						$this->body .= "<select name=c[optional_field_7] class=data_field_values>";
						$matched = 0;
						while ($show_dropdown = $type_result->FetchNextObject())
						{
							$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
							if ($this->registered_variables["optional_field_7"] == $show_dropdown->VALUE)
							{
								$this->body .= "selected";
								$matched = 1;
							}
							$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
						}
						$this->body .= "</select>";
					}
					else
						//blank text box
						$this->body .= "<input type=text name=c[optional_field_7] value=\"".$this->registered_variables["optional_field_7"]."\"  size=".$this->registration_configuration->OPTIONAL_7_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_7_MAXLENGTH." class=data_field_values>";
				}
				if (($this->registration_configuration->REGISTRATION_OPTIONAL_7_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_7_FIELD_TYPE))
				{
					$this->body .= " ".urldecode($this->messages[1261])." <input type=text name=c[optional_field_7_other] value=\"";
					if (!$matched)
						$this->body .= $this->registered_variables["optional_field_7"];
					$this->body .= "\" size=15 maxlength=".$this->registration_configuration->OPTIONAL_7_MAXLENGTH." class=data_field_values>\n\t";
				}
			}
			$this->body .="\n\t\t</td>\n\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[optional_field_7]))
				$this->body .="<font class=error_message>".urldecode($this->messages[1233])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}
		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_8_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[1234]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_8_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t";
			if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_8_FILTER_ASSOCIATION))
			{
				$this->body .= $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_8_FILTER_ASSOCIATION);
				$this->body .= " <a href=".$registration_url."?b=5 class=data_field_values>".$this->messages[1528]."</a>";
			}
			else
			{
				if (!$this->registration_configuration->REGISTRATION_OPTIONAL_8_FIELD_TYPE)
				{
					$this->body .= "<input type=text name=c[optional_field_8] value=\"".$this->registered_variables["optional_field_8"]."\" ";
					if ($this->registration_configuration->OPTIONAL_8_MAXLENGTH > 30)
						$this->body .= "size=30 maxlength=".$this->registration_configuration->OPTIONAL_8_MAXLENGTH." class=data_field_values>";
					else
						$this->body .= "size=".$this->registration_configuration->OPTIONAL_8_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_8_MAXLENGTH." class=data_field_values>";
				}
				elseif($this->registration_configuration->REGISTRATION_OPTIONAL_8_FIELD_TYPE == 1)
				{
					$this->body .= "<textarea name=c[optional_field_8] ";
					$this->body .= "rows=8 cols=50 class=data_field_values>";
					$this->body .= $this->registered_variables["optional_field_8"]."</textarea>";
				}
				else
				{
					$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_8_FIELD_TYPE." order by display_order, value";
					$type_result = $db->Execute($this->sql_query);
					if (!$type_result)
					{
						return false;
					}
					elseif ($type_result->RecordCount() > 0)
					{
						$this->body .= "<select name=c[optional_field_8] class=data_field_values>";
						$matched = 0;
						while ($show_dropdown = $type_result->FetchNextObject())
						{
							$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
							if ($this->registered_variables["optional_field_8"] == $show_dropdown->VALUE)
							{
								$this->body .= "selected";
								$matched = 1;
							}
							$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
						}
						$this->body .= "</select>";
					}
					else
						//blank text box
						$this->body .= "<input type=text name=c[optional_field_1] value=\"".$this->registered_variables["optional_field_8"]."\"  size=".$this->registration_configuration->OPTIONAL_8_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_8_MAXLENGTH." class=data_field_values>";
				}
				if (($this->registration_configuration->REGISTRATION_OPTIONAL_8_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_8_FIELD_TYPE))
				{
					$this->body .= " ".urldecode($this->messages[1261])." <input type=text name=c[optional_field_8_other] value=\"";
					if (!$matched)
						$this->body .= $this->registered_variables["optional_field_8"];
					$this->body .= "\" size=15 maxlength=".$this->registration_configuration->OPTIONAL_8_MAXLENGTH." class=data_field_values>\n\t";
				}
			}
			$this->body .="\n\t\t</td>\n\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[optional_field_8]))
				$this->body .="<font class=error_message>".urldecode($this->messages[1235])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}
		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_9_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[1236]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_9_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t";
			if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_9_FILTER_ASSOCIATION))
			{
				$this->body .= $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_9_FILTER_ASSOCIATION);
				$this->body .= " <a href=".$registration_url."?b=5 class=data_field_values>".$this->messages[1528]."</a>";
			}
			else
			{
				if (!$this->registration_configuration->REGISTRATION_OPTIONAL_9_FIELD_TYPE)
				{
					$this->body .= "<input type=text name=c[optional_field_9] value=\"".$this->registered_variables["optional_field_9"]."\" ";
					if ($this->registration_configuration->OPTIONAL_9_MAXLENGTH > 30)
						$this->body .= "size=30 maxlength=".$this->registration_configuration->OPTIONAL_9_MAXLENGTH." class=data_field_values>";
					else
						$this->body .= "size=".$this->registration_configuration->OPTIONAL_9_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_9_MAXLENGTH." class=data_field_values>";
				}
				elseif($this->registration_configuration->REGISTRATION_OPTIONAL_9_FIELD_TYPE == 1)
				{
					$this->body .= "<textarea name=c[optional_field_9] ";
					$this->body .= "rows=8 cols=50 class=data_field_values>";
					$this->body .= $this->registered_variables["optional_field_9"]."</textarea>";
				}
				else
				{
					$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_9_FIELD_TYPE." order by display_order, value";
					$type_result = $db->Execute($this->sql_query);
					if (!$type_result)
					{
						return false;
					}
					elseif ($type_result->RecordCount() > 0)
					{
						$this->body .= "<select name=c[optional_field_9] class=data_field_values>";
						$matched = 0;
						while ($show_dropdown = $type_result->FetchNextObject())
						{
							$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
							if ($this->registered_variables["optional_field_9"] == $show_dropdown->VALUE)
							{
								$this->body .= "selected";
								$matched = 1;
							}
							$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
						}
						$this->body .= "</select>";
					}
					else
						//blank text box
						$this->body .= "<input type=text name=c[optional_field_9] value=\"".$this->registered_variables["optional_field_9"]."\"  size=".$this->registration_configuration->OPTIONAL_9_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_9_MAXLENGTH." class=data_field_values>";
				}
				if (($this->registration_configuration->REGISTRATION_OPTIONAL_9_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_9_FIELD_TYPE))
				{
					$this->body .= " ".urldecode($this->messages[1261])." <input type=text name=c[optional_field_9_other] value=\"";
					if (!$matched)
						$this->body .= $this->registered_variables["optional_field_9"];
					$this->body .= "\" size=15 maxlength=".$this->registration_configuration->OPTIONAL_9_MAXLENGTH." class=data_field_values>\n\t";
				}
			}
			$this->body .="\n\t\t</td>\n\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[optional_field_9]))
				$this->body .="<font class=error_message>".urldecode($this->messages[1237])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}
		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_10_FIELD)
		{
			$this->body .="<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[1238]);
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_10_FIELD)
				$this->body .="*";
			$this->body .="</td>\n\t\t";
			$this->body .="<td class=data_field_values>\n\t\t";
			if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_10_FILTER_ASSOCIATION))
			{
				$this->body .= $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_10_FILTER_ASSOCIATION);
				$this->body .= " <a href=".$registration_url."?b=5 class=data_field_values>".$this->messages[1528]."</a>";
			}
			else
			{
				if (!$this->registration_configuration->REGISTRATION_OPTIONAL_10_FIELD_TYPE)
				{
					$this->body .= "<input type=text name=c[optional_field_10] value=\"".$this->registered_variables["optional_field_10"]."\" ";
					if ($this->registration_configuration->OPTIONAL_10_MAXLENGTH > 30)
						$this->body .= "size=30 maxlength=".$this->registration_configuration->OPTIONAL_10_MAXLENGTH." class=data_field_values>";
					else
						$this->body .= "size=".$this->registration_configuration->OPTIONAL_10_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_10_MAXLENGTH." class=data_field_values>";
				}
				elseif($this->registration_configuration->REGISTRATION_OPTIONAL_10_FIELD_TYPE == 1)
				{
					$this->body .= "<textarea name=c[optional_field_10] ";
					$this->body .= "rows=8 cols=50 class=data_field_values>";
					$this->body .= $this->registered_variables["optional_field_10"]."</textarea>";
				}
				else
				{
					$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_10_FIELD_TYPE." order by display_order, value";
					$type_result = $db->Execute($this->sql_query);
					if (!$type_result)
					{
						return false;
					}
					elseif ($type_result->RecordCount() > 0)
					{
						$this->body .= "<select name=c[optional_field_10] class=data_field_values>";
						$matched = 0;
						while ($show_dropdown = $type_result->FetchNextObject())
						{
							$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
							if ($this->registered_variables["optional_field_10"] == $show_dropdown->VALUE)
							{
								$this->body .= "selected";
								$matched = 1;
							}
							$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
						}
						$this->body .= "</select>";
					}
					else
						//blank text box
						$this->body .= "<input type=text name=c[optional_field_10] value=\"".$this->registered_variables["optional_field_10"]."\"  size=".$this->registration_configuration->OPTIONAL_10_MAXLENGTH." maxlength=".$this->registration_configuration->OPTIONAL_10_MAXLENGTH." class=data_field_values>";
				}
				if (($this->registration_configuration->REGISTRATION_OPTIONAL_10_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_10_FIELD_TYPE))
				{
					$this->body .= " ".urldecode($this->messages[1261])." <input type=text name=c[optional_field_10_other] value=\"";
					if (!$matched)
						$this->body .= $this->registered_variables["optional_field_10"];
					$this->body .= "\" size=15 maxlength=".$this->registration_configuration->OPTIONAL_10_MAXLENGTH." class=data_field_values>\n\t";
				}
			}

			$this->body .="\n\t\t</td>\n\t";
			$this->body .="<td>&nbsp;";
			if (isset($this->error[optional_field_10]))
				$this->body .="<font class=error_message>".urldecode($this->messages[1239])."</font>";
			$this->body .="</td>\n\t</tr>\n\t";
		}
		//buy,sell,buy and sell
		//primary business type(dropdown)
		//industry focus type dropdown
		//position/function dropdown
		//job title
		//number of employees dropdown
		//annual sales range dropdown
		//how did you learn of us
		$this->body .= "<tr><td colspan=3><hr width=85%></td></tr>";			
		$this->body .=  "<tr class=page_description>\n\t\t<td colspan=3>".urldecode($this->messages[774])."<br><br></td>\n\t</tr>\n\t";

		$this->body .=  "<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[762])." *</td>\n\t\t";
		$this->body .=  "<td class=data_field_values>\n\t\t<input type=text name=c[username] size=15 value=\"".$this->registered_variables["username"]."\"";
		$this->body .=  " size=15 maxlength=15 class=data_field_values>\n\t\t</td>\n\t\t";
		$this->body .=  "<td class=data_field_values>&nbsp;";
		if (isset($this->error[username]))
		{
			$this->body .=  "<font class=error_message>";
			if ($this->error[username] == "error1")
				$this->body .= urldecode($this->messages[773]);
			elseif ($this->error[username] == "error2")
				$this->body .= urldecode($this->messages[775]);
		   	$this->body .= "</font>";
		}
		$this->body .=  "</td>\n\t</tr>\n\t";

		$this->body .=  "<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[763])." *</td>\n\t\t";
		$this->body .=  "<td class=data_field_values>\n\t\t<input type=password name=c[password] size=15 maxlength=12 class=data_field_values>\n\t</td>\n\t";
		
		$this->body .=  "<td rowspan=2 class=data_field_values align=left>&nbsp;";
		//invalid character in string
		if ($this->error1[password])
			$this->body .=  "<span class=error_message>".urldecode($this->messages[2465])." </span>";
		//password not same as password confirm
		if ($this->error2[password])
			$this->body .=  "<span class=error_message>".urldecode($this->messages[776])." </span>";
		//password less than 6 or greater than 12 characters
		if ($this->error3[password])
			$this->body .=  "<span class=error_message>".urldecode($this->messages[777])." </span>";
		else
			$this->body .=  "<span class=error_message></span>";
		$this->body .=  "</td>\n\t</tr>\n\t";
		
		$this->body .=  "<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[764])." *</td>\n\t\t";
		$this->body .=  "<td class=data_field_values>\n\t\t<input type=password name=c[password_confirm] size=15 maxlength=12 class=data_field_values>\n\t\t</td>\n\t</tr>\n\t";
		
		if ($this->registration_configuration->USE_USER_AGREEMENT_FIELD)
		{
			$this->body .=  "<tr>\n\t\t<td class=data_field_labels>".urldecode($this->messages[765])." *</td>\n\t";
			$this->body .=  "<td class=data_field_values  colspan=2>";
			$this->body .=  "<input type=radio name=c[agreement] value=yes ";
				if ($this->registered_variables[agreement] == "yes" || !$this->registered_variables[agreement]) $this->body .=" checked";
			$this->body .=  ">".urldecode($this->messages[766]);
			$this->body .=  "<br><input type=radio name=c[agreement] value=no ";
				if ($this->registered_variables[agreement] == "no") $this->body .=" checked";
			$this->body .=  ">".urldecode($this->messages[767])."\n\t\t</td>\n\t</tr>";
			$this->body .=  "<tr class=registration_agreement><td align=center colspan=3>";
			if ($this->error[yes_to_agreement])
				$this->body .=  "<font class=error_message>".urldecode($this->messages[782])."</font><br>";
			$this->body .=  "<textarea cols=70 rows=10 name=registration_agreement class=registration_agreement readonly onFocus='registration_agreement.blur()'>".urldecode($this->messages[768])."</textarea>";
			$this->body .=  "</td>\n\t</tr>\n\t";
		}


		$this->body .="<tr class=required_fields_note >\n\t\t<td colspan=3 align=center>\n\t\t
			".urldecode($this->messages[244])."\n\t\t</td>\n\t</tr>\n\t";
		$this->body .="<tr>\n\t\t<td colspan=3 align=center>\n\t\t";
		
		//$this->body	.="<input class=submit type=submit name=submit value=\"".urldecode($this->messages[278])."\">\n\t\t";
		$dom = "";
		$this->body .= '<script>var dom="'.$dom.'"; var ov = new Image(); ov.src=dom+"images/form-submit-over.gif"; var out = new Image(); out.src=dom+"images/form-submit-down.gif";</script>' . "\n\t\t" . '<input name="Submit" type="image" value="Submit"
					src="'.$dom.'images/form-submit.gif"
					onmouseover="this.src=\''.$dom.'images/form-submit-over.gif\'"
					onmouseout="this.src=\''.$dom.'images/form-submit-down.gif\'"
					alt="Submit" />';
		
		$this->body .="</td>\n\t</tr>\n\t";
		$this->body .="<tr class=end_registration>\n\t\t<td colspan=3 align=center>\n\t\t
			<a href=".$this->configuration_data['registration_url']."?b=4 class=end_registration>".urldecode($this->messages[241])."</a>\n\t\t</td>\n\t</tr>\n\t";
		$this->body .="</table>\n\t</form>\n\t</td>\n</tr>\n</table>\n";

		if ($this->debug_register)
		{
			echo "hello from the bottom of registration_form_1<bR>\n";
		}

		$this->display_page($db);
		return true;

	} //end of function registration_form_1()

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_user($db) {

		//there are no error in the final part of the form so enter everything into the database
		//get unique id
		//$this->body .="hello from insert_user<br>\n";
		if ($this->configuration_data['use_filters'])
		{
			if ($this->registration_configuration->REGISTRATION_OPTIONAL_1_FILTER_ASSOCIATION)
				$this->registered_variables["optional_field_1"] = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_1_FILTER_ASSOCIATION);
			if ($this->registration_configuration->REGISTRATION_OPTIONAL_2_FILTER_ASSOCIATION)
				$this->registered_variables["optional_field_2"] = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_2_FILTER_ASSOCIATION);
			if ($this->registration_configuration->REGISTRATION_OPTIONAL_3_FILTER_ASSOCIATION)
				$this->registered_variables["optional_field_3"] = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_3_FILTER_ASSOCIATION);
			if ($this->registration_configuration->REGISTRATION_OPTIONAL_4_FILTER_ASSOCIATION)
				$this->registered_variables["optional_field_4"] = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_4_FILTER_ASSOCIATION);
			if ($this->registration_configuration->REGISTRATION_OPTIONAL_5_FILTER_ASSOCIATION)
				$this->registered_variables["optional_field_5"] = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_5_FILTER_ASSOCIATION);
			if ($this->registration_configuration->REGISTRATION_OPTIONAL_6_FILTER_ASSOCIATION)
				$this->registered_variables["optional_field_6"] = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_6_FILTER_ASSOCIATION);
			if ($this->registration_configuration->REGISTRATION_OPTIONAL_7_FILTER_ASSOCIATION)
				$this->registered_variables["optional_field_7"] = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_7_FILTER_ASSOCIATION);
			if ($this->registration_configuration->REGISTRATION_OPTIONAL_8_FILTER_ASSOCIATION)
				$this->registered_variables["optional_field_8"] = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_10_FILTER_ASSOCIATION);
			if ($this->registration_configuration->REGISTRATION_OPTIONAL_9_FILTER_ASSOCIATION)
				$this->registered_variables["optional_field_9"] = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_9_FILTER_ASSOCIATION);
			if ($this->registration_configuration->REGISTRATION_OPTIONAL_10_FILTER_ASSOCIATION)
				$this->registered_variables["optional_field_10"] = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_10_FILTER_ASSOCIATION);
			$this-> save_form_variables($db);
		}
		if ($this->configuration_data['use_email_verification_at_registration'] ||
			$this->configuration_data['admin_approves_all_registration'])
		{
			do {
				$id = md5(uniqid(rand()));
				$id = eregi_replace("[a-f]","",$id);
				$id = substr( $id, 0,6);
				$this->sql_query = "SELECT * FROM ".$this->confirm_email_table." WHERE id = \"".$id."\"";
				//$this->body .=$this->sql_query." is the query<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error["registration"] =urldecode($this->messages[230]);
					return false;
				}
			} while ($result->RecordCount() > 0);

			//insert into the confirm_email table and get an id
			$this->hash = md5($this->configuration_data['secret_for_hash'].$this->username);
			$time = $this->shifted_time($db);
			//$this->sql_query = "insert into ".$this->confirm_email_table."
			//	(id,email,mdhash,date)
			//	VALUES
			//	(\"".$id."\",\"".$this->registered_variables["email"]."\",\"".$this->hash."\",\"".$time."\")";
			$this->sql_query = "insert into ".$this->confirm_email_table."
				(id,email,mdhash,date)
				VALUES
				(\"".$id."\",\"".$this->registered_variables["email"]."\",\"".$id."\",\"".$time."\")";

			//echo $this->sql_query." is the query<br>\n";
			$email_confirm_result = $db->Execute($this->sql_query);
			if (!$email_confirm_result)
			{
				$this->error["registration"] =urldecode($this->messages[230]);
				return false;
			}

            $referrer = $_SESSION['referrer'];
			//need to finish all inserts
			$this->sql_query="INSERT INTO ".$this->confirm_table."
				(mdhash, id, username, password,date,firstname, lastname,
				address, address_2, city, state, country, zip, phone,phone_2,
				fax, email, email2, company_name,business_type, url,
				optional_field_1,optional_field_2,optional_field_3,optional_field_4,optional_field_5,
				optional_field_6,optional_field_7,optional_field_8,optional_field_9,optional_field_10,
				newsletter,group_id,filter_id,registration_code, referrer, subdomain, phoneext, phoneext_2)
				VALUES
				(\"$this->hash\",\"$id\", \"". Addslashes ($this->username)."\", \"". Addslashes ($this->password)."\",
				".$time." ,\""
				. Addslashes ($this->registered_variables[firstname])."\", \""
				. Addslashes ($this->registered_variables[lastname])."\", \""
				. AddSlashes ($this->registered_variables[address])."\", \""
				. Addslashes ($this->registered_variables[address_2])."\", \""
				. AddSlashes ($this->registered_variables[city])."\", \""
				. AddSlashes ($this->registered_variables[state])."\", \""
				. AddSlashes ($this->registered_variables[country])."\", \""
				. AddSlashes ($this->registered_variables[zip])."\", \""
				. AddSlashes ($this->registered_variables[phone])."\", \""
				. AddSlashes ($this->registered_variables[phone_2])."\", \""
				. Addslashes ($this->registered_variables[fax])."\", \""
				. Addslashes ($this->registered_variables[email])."\", \""
				. Addslashes ($this->registered_variables[email2])."\", \""
				. Addslashes ($this->registered_variables[company_name])."\", \""
				. Addslashes ($this->registered_variables[business_type])."\", \""
				. Addslashes ($this->registered_variables[url])."\", \""
				. Addslashes ($this->registered_variables[optional_field_1])."\",\""
				. Addslashes ($this->registered_variables[optional_field_2])."\",\""
				. Addslashes ($this->registered_variables[optional_field_3])."\",\""
				. Addslashes ($this->registered_variables[optional_field_4])."\",\""
				. Addslashes ($this->registered_variables[optional_field_5])."\",\""
				. Addslashes ($this->registered_variables[optional_field_6])."\",\""
				. Addslashes ($this->registered_variables[optional_field_7])."\",\""
				. Addslashes ($this->registered_variables[optional_field_8])."\",\""
				. Addslashes ($this->registered_variables[optional_field_9])."\",\""
				. Addslashes ($this->registered_variables[optional_field_10])."\",\""
				. Addslashes ($newsletter)."\",
				".$this->registration_group.",
				".$this->registration_filter_id.",\""
                . Addslashes ($this->registered_variables[registration_code])."\",\""
                . Addslashes ($referrer)."\",\""
                . Addslashes ($_SERVER['HTTP_HOST'])."\",\""
                . Addslashes ($this->registered_variables[phoneext])."\",\""
                . Addslashes ($this->registered_variables[phoneext_2])."\"
                )";

			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$result)
			{
				$this->error["registration"] = urldecode($this->messages[230]);
				return false;
			}

			if($this->configuration_data['use_email_verification_at_registration'])
			{
				$this->page_id = 20;
				$this->get_text($db);
				if (!$this->configuration_data['admin_approves_all_registration'])
				{
					if ($this->configuration_data['use_ssl_in_registration'])
						$return_url = trim($this->configuration_data['registration_ssl_url']);
					else
						$return_url =  trim($this->configuration_data['registration_url']);
					$confirmurl = ($return_url."?b=3&hash=" . "$id" . "&username=" . "$this->username");
				}

				$mailto = $this->registered_variables["email"];
				$subject = urldecode($this->messages[228]);
				$message = urldecode($this->messages[672])." ".$this->username.",\n\n";
				$message .= urldecode($this->messages[229])."\n\n";
				$message .= urldecode($this->messages[1329]).": ".$this->username."\n\n".urldecode($this->messages[1330]).": ".$this->password."\n";
				$message .= urldecode($this->messages[1331]).": ".$this->registered_variables["email"]."\n\n";
				if (!$this->configuration_data['admin_approves_all_registration'])
				{
					$message .= "\n\n".urldecode($this->messages[230])."\n\n".$confirmurl."\n\n".urldecode($this->messages[231]);
				}

				if ($this->configuration_data['email_header_break'])
					$separator = "\n";
				else
					$separator = "\r\n";
				$from = "From: ".$this->configuration_data['registration_admin_email'].$separator."Reply-to: ".$this->configuration_data['registration_admin_email'].$separator;
				$additional = "-f".$this->configuration_data['registration_admin_email'];
				if ($this->configuration_data['email_configuration'] == 1)
					mail($mailto, $subject, $message, $from,$additional);
				elseif ($this->configuration_data['email_configuration'] == 2)
					mail($mailto, $subject, $message, $from);
				else
					mail($mailto, $subject, $message);
			}
			if ($this->configuration_data['send_register_attempt_email_admin'])
			{
				$mailto = $this->configuration_data['registration_admin_email'];
				$subject = "NOTIFY ".$this->site_name." Registration attempt";
				$message ="Username : ".$this->username."\nPassword : ".$this->password."\nE-Mail : ".$this->registered_variables[email]."\n\n";
				if ($this->configuration_data['email_header_break'])
					$separator = "\n";
				else
					$separator = "\r\n";
				$from = "From: ".$this->configuration_data['registration_admin_email'].$separator."Reply-to: ".$this->configuration_data['registration_admin_email'].$separator;
				$ip = $_SERVER['REMOTE_ADDR'];
				$host = @gethostbyaddr($ip);
				//$host = preg_replace("/^[^.]+./", "*.", $host);
				$message .= "\n".$_SERVER["REMOTE_ADDR"]." : ".$host;

				$additional = "-f".$this->configuration_data['registration_admin_email'];
				if ($this->configuration_data['email_configuration'] == 1)
					mail($mailto, $subject, $message, $from,$additional);
				elseif ($this->configuration_data['email_configuration'] == 2)
					mail($mailto, $subject, $message, $from);
				else
					mail($mailto, $subject, $message);
			}
		}
		else
		{

		  	if ($this->configuration_data['registration_approval'])
		  		$current_status = 3;
		  	else
		  		$current_status = 1;

		  	$this->sql_query = "insert into ".$this->logins_table." (username, password,status)
		  		values
		  		(\"".$this->registered_variables["username"]."\", \"".$this->registered_variables["password"]."\",".$current_status.")";
		  	//echo $this->sql_query." is the query<br>\n";
			$login_result = $db->Execute($this->sql_query);
			if (!$login_result)
			{
				$this->site_error($this->sql_query,$db->ErrorMsg());
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error["confirm"] = "error1";
				return false;
			}
			else
			{
				$this->user_id = $db->Insert_ID();
				//insert login data into the login table
				
				if (!defined('DEFAULT_COMMUNICATION_SETTING'))
					$default_communication_setting = 1;
				else
					$default_communication_setting = DEFAULT_COMMUNICATION_SETTING;

            	$referrer = $_SESSION['referrer'];

        		//$this->registered_variables["referrer"] = stripslashes(urldecode($referrer));
				$this->sql_query = "insert into ".$this->userdata_table." (id,username,email,email2,newsletter,level,company_name,
				business_type,firstname,lastname,address,address_2,zip,city,state,country,phone,phone2,fax,url,date_joined,
				communication_type,rate_sum,rate_num,optional_field_1,optional_field_2,optional_field_3,optional_field_4,
				optional_field_5,optional_field_6,optional_field_7,optional_field_8,optional_field_9,optional_field_10,filter_id, referrer, subdomain, phoneext, phoneext_2) values
					(".$this->user_id.",\"".$this->registered_variables["username"]."\",\"".$this->registered_variables["email"]."\",
					\"".$this->registered_variables["email2"]."\",
					\"0\", 0,\"".addslashes($this->registered_variables["company_name"])."\",
					\"".$this->registered_variables["business_type"]."\",\"".addslashes($this->registered_variables["firstname"])."\",
					\"".addslashes($this->registered_variables["lastname"])."\",
					\"".addslashes($this->registered_variables["address"])."\",\"".addslashes($this->registered_variables["address_2"])."\",
					\"".addslashes($this->registered_variables["zip"])."\",
					\"".addslashes($this->registered_variables["city"])."\",\"".$this->registered_variables["state"]."\",
					\"".$this->registered_variables["country"]."\",
		  			\"".addslashes($this->registered_variables["phone"])."\",\"".addslashes($this->registered_variables["phone_2"])."\",
		  			\"".addslashes($this->registered_variables["fax"])."\",\"".addslashes($this->registered_variables["url"])."\",".$this->shifted_time($db).",".$default_communication_setting.",0,0,
					\"".addslashes($this->registered_variables["optional_field_1"])."\",\"".addslashes($this->registered_variables["optional_field_2"])."\",
					\"".addslashes($this->registered_variables["optional_field_3"])."\",\"".addslashes($this->registered_variables["optional_field_4"])."\",
					\"".addslashes($this->registered_variables["optional_field_5"])."\",\"".addslashes($this->registered_variables["optional_field_6"])."\",
					\"".addslashes($this->registered_variables["optional_field_7"])."\",\"".addslashes($this->registered_variables["optional_field_8"])."\",
					\"".addslashes($this->registered_variables["optional_field_9"])."\",\"".addslashes($this->registered_variables["optional_field_10"])."\",
					\"".$this->registration_filter_id."\",
                    \"".$referrer."\",
                    \"".addslashes($_SERVER['HTTP_HOST'])."\",
                    \"".addslashes($this->registered_variables["phoneext"])."\",
                    \"".addslashes($this->registered_variables["phoneext_2"])."\"
                    )";

                //$this->body .=$this->sql_query." is the query<br>\n";
				$userdata_result = $db->Execute($this->sql_query);
				if ($this->debug_register) echo $this->registered_variables["business_type"]." is business type<BR>\n";
				if (!$userdata_result)
				{
					$this->site_error($this->sql_query,$db->ErrorMsg());
					if ($this->debug_register) echo $this->sql_query." is the query<br>\n";
					$this->error["confirm"] = "error";
					return false;
				}
				else
				{
			  		//insert into users_group_price_plans table
			  		if($this->is_class_auctions())
			  		{
			  			$price_plan = $this->get_price_plan_from_group($db, $this->registration_group);
			  			$auction_price_plan = $this->get_price_plan_from_group($db, $this->registration_group, 1);
			  		}
			  		elseif($this->is_auctions())
			  		{
			  			$price_plan = $this->get_price_plan_from_group($db, $this->registration_group, 1);
			  			$auction_price_plan = $price_plan;
			  		}
			  		elseif($this->is_classifieds())
			  		{
			  			$price_plan = $this->get_price_plan_from_group($db, $this->registration_group);
			  		}

					$this->sql_query = "insert into ".$this->user_groups_price_plans_table."
						(id,group_id,price_plan_id,auction_price_plan_id)
						values
						(".$this->user_id.",".$this->registration_group.",\"".$price_plan->PRICE_PLAN_ID."\",\"".$auction_price_plan->PRICE_PLAN_ID."\")";
					if ($this->debug_register) echo $this->sql_query." is the query<br>\n";
					$group_result = $db->Execute($this->sql_query);
					if (!$group_result)
					{
						$this->site_error($this->sql_query,$db->ErrorMsg());
						if ($this->debug_register) echo $this->sql_query." is the query<br>\n";
						$this->error["confirm"] = "error";
						return false;
					}			  		
			  		
			  		//check for expiration of price plans
			  		if ($price_plan->EXPIRATION_TYPE == 2)
			  		{
			  			//dynamic expiration of this price plan from the date of registration
			  			$expiration_date = ($this->shifted_time($db) + ($price_plan->EXPIRATION_FROM_REGISTRATION * 84600));

			  			$this->sql_query = "insert into ".$this->expirations_table."
			  				(type,user_id,expires,type_id)
			  				values
			  				(2,".$this->user_id.",".$expiration_date.",".$price_plan->PRICE_PLAN_ID.")";
						$plan_expiration_result = $db->Execute($this->sql_query);
						//echo $this->sql_query." is the query<br>\n";
						if (!$plan_expiration_result)
						{
							$this->site_error($this->sql_query,$db->ErrorMsg());
							//$this->body .=$this->sql_query." is the query<br>\n";
							$this->error["confirm"] = "error";
							return false;
						}
			  		}

					//check to see if registration credits or free subscription period
					if ($price_plan->TYPE_OF_BILLING == 1)
					{
						//fee based subscriptions
						if ($price_plan->CREDITS_UPON_REGISTRATION > 0)
						{
							if ($price_plan->CREDITS_EXPIRE_TYPE == 1)
							{
								//expire on fixed days from registration
								$expiration = (($price_plan->CREDITS_EXPIRE_PERIOD * 86400) + $this->shifted_time($db));
							}
							elseif ($price_plan->CREDITS_EXPIRE_TYPE == 2)
							{
								//expire on fixed date
								$expiration = $price_plan->CREDITS_EXPIRE_DATE;
							}
							$this->sql_query = "insert into ".$this->user_credits_table."
								(user_id,credit_count,credits_expire)
								values
								(".$this->user_id.",".$price_plan->CREDITS_UPON_REGISTRATION.",".$expiration.")";
							//echo $this->sql_query." is the query<br>\n";
							$free_credits_result = $db->Execute($this->sql_query);
							if (!$free_credits_result)
							{
								$this->site_error($this->sql_query,$db->ErrorMsg());
								//$this->body .=$this->sql_query." is the query<br>\n";
								$this->error["confirm"] = "error";
								return false;
							}
						}
						
						// Insert initial site balance
						
						if ($this->debug_register)
						{
							echo "about to check for initial balance<Br>\n";
							echo $this->configuration_data['positive_balances_only']." is positive_balances_only<br>\n";
							echo $this->configuration_data['use_account_balance']." is use_account_balance<Br>\n";
							echo $price_plan->INITIAL_SITE_BALANCE." is INITIAL_SITE_BALANCE for price plan<BR>";
						}
						
						$this->add_initial_site_balance($db,$price_plan);
					}
					elseif ($price_plan->TYPE_OF_BILLING == 2)
					{
						//subscription based
						if ($price_plan->FREE_SUBSCRIPTION_PERIOD_UPON_REGISTRATION > 0)
						{
							//get expiration from now
							$expiration = (($price_plan->FREE_SUBSCRIPTION_PERIOD_UPON_REGISTRATION * 86400) + $this->shifted_time($db));

							$this->sql_query = "insert into ".$this->user_subscriptions_table."
								(user_id,subscription_expire)
								values
								(".$this->user_id.",".$expiration.")";
							//echo $this->sql_query." is the query<br>\n";
							$free_subscription_result = $db->Execute($this->sql_query);
							if (!$free_subscription_result)
							{
								$this->site_error($this->sql_query,$db->ErrorMsg());
								//$this->body .=$this->sql_query." is the query<br>\n";
								$this->error["confirm"] = "error";
								return false;
							}
						}
					}

					if ($this->registration_configuration_data->CLASSAUCTIONS)
					{
						//add ability to add auction price plan
						//add free subscription period
						//add auction credits
						$this->insert_auction_price_plan($db);

					} //  end of if ($this->registration_configuration_data->CLASSAUCTIONS)

			  		//send email saying registration is complete
			  		if ($this->configuration_data['send_register_complete_email_client'])
			  		{
						$this->page_id = 21;
						$this->get_text($db);
						$mailto = $this->registered_variables["email"];
						$subject = urldecode($this->messages[678]);
						$message = urldecode($this->messages[676])." ".$this->registered_variables["firstname"]."\n\n".urldecode($this->messages[677]);
						if ($this->configuration_data['email_header_break'])
							$separator = "\n";
						else
							$separator = "\r\n";

						$from = "From: ".$this->configuration_data['registration_admin_email'].$separator."Reply-to: ".$this->configuration_data['registration_admin_email'].$separator;
			  			if ($this->debug_register)
			  				echo $from." is the from<bR>\n";
			  			$additional = "-f".$this->configuration_data['registration_admin_email'];

						if ($this->configuration_data['email_configuration'] == 1)
							mail($mailto, $subject, $message, $from,$additional);
						elseif ($this->configuration_data['email_configuration'] == 2)
							mail($mailto, $subject, $message, $from);
						else
							mail($mailto, $subject, $message);
			  		}

			  		if ($this->configuration_data['send_register_complete_email_admin'])
			  		{
			  			if ($this->registered_variables["business_type"] == 1)
			  				$business_type = "individual";
			  			elseif ($this->registered_variables["business_type"] == 2)
			  				$business_type = "business";
			  			else
			  				$business_type = "none";
						$mailto = $this->configuration_data['registration_admin_email'];
						$subject = "registration complete for ".$this->registered_variables["username"];
						$message = "registration code: ".$this->registered_variables["registration_code"]."\n";
						$message .= "just registered: ".$this->registered_variables["username"]."\n";
						$message .= "user_id: ".$this->user_id."\n";
						$message .= "username: ".$this->registered_variables["username"]."\n";
						$message .= "password: ".$this->registered_variables["password"]."\n";
						$message .= "email: ".$this->registered_variables["email"]."\n";
						$message .= "email2: ".$this->registered_variables["email2"]."\n";
						$message .= "company name: ".$this->registered_variables["company_name"]."\n";
						$message .= "business type: ".$business_type."\n";
						$message .= "first name: ".$this->registered_variables["firstname"]."\n";
						$message .= "last name: ".$this->registered_variables["lastname"]."\n";
						$message .= "address: ".$this->registered_variables["address"]."\n";
						$message .= "address line 2: ".$this->registered_variables["address_2"]."\n";
						$message .= "city: ".$this->registered_variables["city"]."\n";
						$message .= "state: ".$this->registered_variables["state"]."\n";
						$message .= "zip: ".$this->registered_variables["zip"]."\n";
						$message .= "country: ".$this->registered_variables["country"]."\n";
						$message .= "phone: ".$this->registered_variables["phone"]."\n";
                        $message .= "phoneext: ".$this->registered_variables["phoneext"]."\n";
						$message .= "phone 2: ".$this->registered_variables["phone_2"]."\n";
                        $message .= "phoneext_2: ".$this->registered_variables["phoneext_2"]."\n";
                        //echo "<h1><font color=#ff9900> Fone 2 is ".$this->registered_variables["phone_2"]."</font></h1>";
						$message .= "fax: ".$this->registered_variables["fax"]."\n";
						$message .= "url: ".$this->registered_variables["url"]."\n";
						$message .= "optional field 1: ".$this->registered_variables["optional_field_1"]."\n";
						$message .= "optional field 2: ".$this->registered_variables["optional_field_2"]."\n";
						$message .= "optional field 3: ".$this->registered_variables["optional_field_3"]."\n";
						$message .= "optional field 4: ".$this->registered_variables["optional_field_4"]."\n";
						$message .= "optional field 5: ".$this->registered_variables["optional_field_5"]."\n";
						$message .= "optional field 6: ".$this->registered_variables["optional_field_6"]."\n";
						$message .= "optional field 7: ".$this->registered_variables["optional_field_7"]."\n";
						$message .= "optional field 8: ".$this->registered_variables["optional_field_8"]."\n";
						$message .= "optional field 9: ".$this->registered_variables["optional_field_9"]."\n";
						$message .= "optional field 10: ".$this->registered_variables["optional_field_10"]."\n";
                        $message .= "Referrer: ".$referrer."\n";
                            $ip = $_SERVER['REMOTE_ADDR'];
  					        $host = @gethostbyaddr($ip);
					        //$host = preg_replace("/^[^.]+./", "*.", $host);
					        $message .= "\n".$_SERVER["REMOTE_ADDR"]." : ".$host;
						if ($this->configuration_data['email_header_break'])
							$separator = "\n";
						else
							$separator = "\r\n";
						$from = "From: ".$this->configuration_data['registration_admin_email'].$separator."Reply-to: ".$this->configuration_data['registration_admin_email'].$separator;
						$additional = "-f".$this->configuration_data['registration_admin_email'];
			  			if ($this->configuration_data['email_configuration'] == 1)
                        {
							mail($mailto, $subject, $message, $from,$additional);
                        }
                        elseif ($this->configuration_data['email_configuration'] == 2)
                        {
                        	mail($mailto, $subject, $message, $from);
                        }
                        else
						{
                        	mail($mailto, $subject, $message);
                        }
			  		}
			  	}
			  	//set the current session user_id to this new user_id
			  	$this->set_new_user_id_in_current_session($db);

				if ($this->debug_register)
					echo $this->configuration_data['use_api']." is use api<BR>\n";
				if ($this->configuration_data['use_api'])
				{
                	//echo "Use API integration<hr color=#ff9900>";
					include("config.php");
					$this->registered_variables["db_host"] = $db_host;
					$this->registered_variables["db_name"] = $database;
					$this->registered_variables["installation_type"] = 1;
					include_once("classes/api_register_class.php");
                    $this->debug_register = 0;
                    if ($this->debug_register)
					{
						foreach ($this->registered_variables as $key => $value)
						{
							echo $key." - ".$value."<br>\n";
						}
					}
					$api_register = new API_Register($this->registered_variables);
					$api_register->api_insert_user();
				}
		  	}
		}
		return true;
	} //end of function insert_user

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_username($db,$username=0)
	{
		//$this->body .="hello from check_username<br>\n";
		$this->username = trim($username);
		$this->username = trim(preg_replace('#\s+#si', ' ', $this->username));
		$this->error[username] = "";
		$username_length = strlen(trim($username));
		if (($username_length == 0 ) || ($username_length > 12) || ($username_length < 6))
		{
			$this->error[username] = "error1";
			$this->error_found++;
		}
		if (!eregi('^[A-Za-z0-9_\-]+$', $this->username))
		{
			$this->error[username] = "error1";
			$this->error_found++;
		}
		else
		{
			$this->sql_query = "select id from ".$this->logins_table." where username = \"".$username."\"";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error["registration"] = urldecode($this->messages[230]);
				return false;
			}

			if ($result->RecordCount() > 0)
			{
				$this->error[username] = "error2";
				$this->error_found++;
			}
		 }
		 return true;
	} //end of function check_username($username)

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_password($password=0,$password_confirm=0)
	{
		/*$this->password = trim($password);
		$password_length = strlen(trim($this->password));
		if ((($password_length == 0 ) || ($password_length >12) || ($password_length < 6)))
		{
			$this->error[password] = "error";
			$this->error_found++;
		}

		if (!eregi('^[A-Za-z0-9_\-]+$', $this->password))
		{
			$this->error[alpha_password] = "error";
			$this->error_found++;
		}

		if ($password_confirm != $this->password ) {
			$this->error[repeat_password] = "error";
			$this->error_found++;
		}
		return true;*/
		
		$this->password = trim($password);
		$password_length = strlen(trim($this->password));
		if (ereg('[^A-Za-z0-9]', $this->password))
		{
			//invalid character in string
			$this->error1[password] = 1;
			$this->error_found++;
		}
		if ($password_confirm != $this->password ) 
		{
			//password not same as password confirm
			$this->error2[password] = 1;
			$this->error_found++;
		}
		if ($password_length < 6 || $password_length > 12)
		{
			//password less than 6 or greater than 12 characters
			$this->error3[password] = 1;
			$this->error_found++;
		}
		return true;
	} //end of function check_password


//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_agreement($agreement)
	{
		if ($this->registration_configuration->USE_USER_AGREEMENT_FIELD)
		{
			if ((empty($agreement)) || ($agreement != "yes")) {
				$this->error[yes_to_agreement] = "error";
				$this->error_found++;
			}
		}
		return true;
	} //end of function check_agreement($agreement)

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function confirm ($db,$hash=0,$username=0)
	{
		$this->page_id = 21;
		$this->get_text($db);
		if (!$this->expire_confirmations($db))
		{
			$this->error["confirm"] = "error";
			return false;
		}

  		if (($hash) && ($username))
		{
			//$this->body .=$hash." is the hash inside confirm<br>\n";
			//$this->body .=$username." is the username inside confirm<br>\n";
			//$this->sql_query = "select * from ".$this->confirm_table." where mdhash = \"".$hash."\" AND username = \"".$username."\"";
			$this->sql_query = "select * from ".$this->confirm_table." where id = \"".$hash."\" AND username = \"".$username."\"";
			//echo $this->sql_query." is the query<br>\n";
			$confirm_result = $db->Execute($this->sql_query);
			if (!$confirm_result)
			{
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->site_error($this->sql_query,$db->ErrorMsg());
				$this->error["confirm"] = "error";
				return false;
		  	}
		  	elseif (($confirm_result->RecordCount() == 0) || ($confirm_result->RecordCount() > 1))
		  	{
		  		//bad return or not in the confirm table
		  		//echo $this->sql_query." is the query<br>\n";
		  		$this->error["confirm"] = "error1";
		  		return false;
		  	}
		  	elseif ($confirm_result->RecordCount() == 1)
		  	{
		  		$show = $confirm_result->FetchNextObject();

		  		//double check the username again to make sure
		  		$this->error_found = 0;
		  		$this->check_username($db,$show->USERNAME);
		  		if ($this->error_found > 1)
		  		{
		  			//the username has been taken since trying to register the first time
		  			$this->error["confirm"] = "error2";
		  			return false;
		  		}

		  		if ($this->configuration_data['registration_approval'])
		  			$current_status = 3;
		  		else
		  			$current_status = 1;

		  		$this->sql_query = "insert into ".$this->logins_table." (username, password,status)
		  			values
		  			(\"".$show->USERNAME."\", \"".$show->PASSWORD."\",".$current_status.")";
		  		//echo $this->sql_query." is the query<br>\n";
				$login_result = $db->Execute($this->sql_query);
				if (!$login_result)
				{
					$this->site_error($this->sql_query,$db->ErrorMsg());
					//$this->body .=$this->sql_query." is the query<br>\n";
					$this->error["confirm"] = "error1";
					return false;
			  	}
			  	else
			  	{
			  		$this->user_id = $db->Insert_ID();
			  		//insert login data into the login table
			  		
					if (!defined('DEFAULT_COMMUNICATION_SETTING'))
						$default_communication_setting = 1;
					else 
						$default_communication_setting = DEFAULT_COMMUNICATION_SETTING;				  		
			  		
					$this->sql_query = "insert into ".$this->userdata_table."
						(id,username,email,email2,newsletter,level,company_name,
						business_type,firstname,lastname,address,address_2,zip,city,state,country,phone,phone2,phoneext,phoneext_2,fax,url,date_joined,
						communication_type,rate_sum,rate_num,optional_field_1,optional_field_2,optional_field_3,optional_field_4,
						optional_field_5,optional_field_6,optional_field_7,optional_field_8,optional_field_9,optional_field_10,filter_id) values
						(".$this->user_id.",\"".$show->USERNAME."\",\"".$show->EMAIL."\",
						\"".$show->EMAIL2."\",
						\"0\", 0,\"".addslashes($show->COMPANY_NAME)."\",
						\"".$show->BUSINESS_TYPE."\",\"".addslashes($show->FIRSTNAME)."\",
						\"".addslashes($show->LASTNAME)."\",
						\"".addslashes($show->ADDRESS)."\",\"".addslashes($show->ADDRESS_2)."\",
						\"".addslashes($show->ZIP)."\",
						\"".addslashes($show->CITY)."\",\"".$show->STATE."\",
						\"".$show->COUNTRY."\",
						\"".addslashes($show->PHONE)."\",\"".addslashes($show->PHONE_2)."\",
                        \"".addslashes($show->PHONEEXT)."\",\"".addslashes($show->PHONEEXT_2)."\",
						\"".addslashes($show->FAX)."\",\"".addslashes($show->URL)."\",".$this->shifted_time($db).",".$default_communication_setting.",0,0,
						\"".addslashes($show->OPTIONAL_FIELD_1)."\",\"".addslashes($show->OPTIONAL_FIELD_2)."\",
						\"".addslashes($show->OPTIONAL_FIELD_3)."\",\"".addslashes($show->OPTIONAL_FIELD_4)."\",
						\"".addslashes($show->OPTIONAL_FIELD_5)."\",\"".addslashes($show->OPTIONAL_FIELD_6)."\",
						\"".addslashes($show->OPTIONAL_FIELD_7)."\",\"".addslashes($show->OPTIONAL_FIELD_8)."\",
						\"".addslashes($show->OPTIONAL_FIELD_9)."\",\"".addslashes($show->OPTIONAL_FIELD_10)."\",
						\"".$show->FILTER_ID."\")";

		  			//echo $this->sql_query." is the query<br>\n";
					$userdata_result = $db->Execute($this->sql_query);
					if (!$userdata_result)
					{
						$this->site_error($this->sql_query,$db->ErrorMsg());
						//$this->body .=$this->sql_query." is the query<br>\n";
						$this->error["confirm"] = "error";
						return false;
				  	}
				  	else
				  	{
				  		//insert into users_group_price_plans table
				  		if($this->is_classifieds() || $this->is_class_auctions())
				  			$price_plan = $this->get_price_plan_from_group($db,$show->GROUP_ID);
				  		if($this->is_auctions() || $this->is_class_auctions())
				  			$auction_price_plan = $this->get_price_plan_from_group($db,$show->GROUP_ID,1);

						//check for expiration of price plans
						if ($price_plan->EXPIRATION_TYPE == 2)
						{
							//dynamic expiration of this price plan from the date of registration
							$expiration_date = ($this->shifted_time($db) + ($price_plan->EXPIRATION_FROM_REGISTRATION * 84600));

							$this->sql_query = "insert into ".$this->expirations_table."
								(type,user_id,expires,type_id)
								values
								(2,".$this->user_id.",".$expiration_date.",".$price_plan->PRICE_PLAN_ID.")";
							$plan_expiration_result = $db->Execute($this->sql_query);
							if (!$plan_expiration_result)
							{
								$this->site_error($this->sql_query,$db->ErrorMsg());
								//$this->body .=$this->sql_query." is the query<br>\n";
								$this->error["confirm"] = "error";
								return false;
							}
						}

						if($this->is_class_auctions())
						{
							$this->sql_query = "insert into ".$this->user_groups_price_plans_table."
								(id, group_id, price_plan_id, auction_price_plan_id)
									values
									(".$this->user_id.",".$show->GROUP_ID.",".$price_plan->PRICE_PLAN_ID.",".$auction_price_plan->PRICE_PLAN_ID.")";
						}
						elseif($this->is_auctions())
						{
							$this->sql_query = "insert into ".$this->user_groups_price_plans_table."
								(id, group_id, auction_price_plan_id)
									values
									(".$this->user_id.",".$show->GROUP_ID.",".$auction_price_plan->PRICE_PLAN_ID.")";
						}
						elseif($this->is_classifieds())
						{
							$this->sql_query = "insert into ".$this->user_groups_price_plans_table."
								(id, group_id, price_plan_id)
									values
									(".$this->user_id.",".$show->GROUP_ID.",".$price_plan->PRICE_PLAN_ID.")";
						}
						//echo $this->sql_query." is the query<br>\n";
						$group_result = $db->Execute($this->sql_query);
						if (!$group_result)
						{
							$this->site_error($this->sql_query,$db->ErrorMsg());
							//$this->body .=$this->sql_query." is the query<br>\n";
							$this->error["confirm"] = "error";
							return false;
						}

						$initial_account_balance_given = 0;
						if($this->is_class_auctions() || $this->is_classifieds())
						{
							//check to see if registration credits or free subscription period
							if ($price_plan->TYPE_OF_BILLING == 1)
							{
								//fee based subscriptions
								if ($price_plan->CREDITS_UPON_REGISTRATION > 0)
								{
									if ($price_plan->CREDITS_EXPIRE_TYPE == 1)
									{
										//expire on fixed days from registration
										$expiration = (($price_plan->CREDITS_EXPIRE_PERIOD * 86400) + $this->shifted_time($db));
									}
									elseif ($price_plan->CREDITS_EXPIRE_TYPE == 2)
									{
										//expire on fixed date
										$expiration = $price_plan->CREDITS_EXPIRE_DATE;
									}
									$this->sql_query = "insert into ".$this->user_credits_table."
											(user_id,credit_count,credits_expire)
											values
											(".$this->user_id.",".$price_plan->CREDITS_UPON_REGISTRATION.",".$expiration.")";
									//echo $this->sql_query." is the query<br>\n";
									$free_credits_result = $db->Execute($this->sql_query);
									if (!$free_credits_result)
									{
										$this->site_error($this->sql_query,$db->ErrorMsg());
										//$this->body .=$this->sql_query." is the query<br>\n";
										$this->error["confirm"] = "error";
										return false;
									}
								}
								// Insert initial site balance
								if ($this->debug_register)
								{
									echo "about to check for initial balance<Br>\n";
									echo $this->configuration_data['positive_balances_only']." is positive_balances_only<br>\n";
									echo $this->configuration_data['use_account_balance']." is use_account_balance<Br>\n";
									
								}
								$this->add_initial_site_balance($db,$price_plan);						
							}
							elseif ($price_plan->TYPE_OF_BILLING == 2)
							{
								//subscription based
								if ($price_plan->FREE_SUBSCRIPTION_PERIOD_UPON_REGISTRATION > 0)
								{
									//get expiration from now
									$expiration = (($price_plan->FREE_SUBSCRIPTION_PERIOD_UPON_REGISTRATION * 86400) + $this->shifted_time($db));
	
									$this->sql_query = "insert into ".$this->user_subscriptions_table."
											(user_id,subscription_expire)
											values
											(".$this->user_id.",".$expiration.")";
									//echo $this->sql_query." is the query<br>\n";
									$free_subscription_result = $db->Execute($this->sql_query);
									if (!$free_subscription_result)
									{
										$this->site_error($this->sql_query,$db->ErrorMsg());
										//$this->body .=$this->sql_query." is the query<br>\n";
										$this->error["confirm"] = "error";
										return false;
									}
								}
							}
						}

						if($this->is_auctions())
						{
							//check to see if registration credits or free subscription period
							if ($auction_price_plan->TYPE_OF_BILLING == 1)
							{
								//fee based subscriptions
								if ($auction_price_plan->CREDITS_UPON_REGISTRATION > 0)
								{
									if ($auction_price_plan->CREDITS_EXPIRE_TYPE == 1)
									{
										//expire on fixed days from registration
										$expiration = (($auction_price_plan->CREDITS_EXPIRE_PERIOD * 86400) + $this->shifted_time($db));
									}
									elseif ($auction_price_plan->CREDITS_EXPIRE_TYPE == 2)
									{
										//expire on fixed date
										$expiration = $auction_price_plan->CREDITS_EXPIRE_DATE;
									}
									$this->sql_query = "insert into ".$this->user_credits_table."
											(user_id,credit_count,credits_expire)
											values
											(".$this->user_id.",".$auction_price_plan->CREDITS_UPON_REGISTRATION.",".$expiration.")";
									//echo $this->sql_query." is the query<br>\n";
									$free_credits_result = $db->Execute($this->sql_query);
									if (!$free_credits_result)
									{
										$this->site_error($this->sql_query,$db->ErrorMsg());
										//$this->body .=$this->sql_query." is the query<br>\n";
										$this->error["confirm"] = "error";
										return false;
									}
								}
								
								// Insert initial site balance
								if ($this->debug_register)
								{
									echo "about to check for initial balance<Br>\n";
									echo $this->configuration_data['positive_balances_only']." is positive_balances_only<br>\n";
									echo $this->configuration_data['use_account_balance']." is use_account_balance<Br>\n";
									
								}
								$this->add_initial_site_balance($db,$price_plan);
				
							}
							elseif ($auction_price_plan->TYPE_OF_BILLING == 2)
							{
								//subscription based
								if ($auction_price_plan->FREE_SUBSCRIPTION_PERIOD_UPON_REGISTRATION > 0)
								{
									//get expiration from now
									$expiration = (($auction_price_plan->FREE_SUBSCRIPTION_PERIOD_UPON_REGISTRATION * 86400) + $this->shifted_time($db));
	
									$this->sql_query = "insert into ".$this->user_subscriptions_table."
											(user_id,subscription_expire)
											values
											(".$this->user_id.",".$expiration.")";
									//echo $this->sql_query." is the query<br>\n";
									$free_subscription_result = $db->Execute($this->sql_query);
									if (!$free_subscription_result)
									{
										$this->site_error($this->sql_query,$db->ErrorMsg());
										//$this->body .=$this->sql_query." is the query<br>\n";
										$this->error["confirm"] = "error";
										return false;
									}
								}
							}
						}

						//delete from the confirm table
						$this->sql_query = "delete from ".$this->confirm_table." where username = \"".$username."\"";
						//$this->body .=$this->sql_query." is the query<br>\n";
						$delete_result = $db->Execute($this->sql_query);
						if (!$delete_result)
						{
							$this->site_error($this->sql_query,$db->ErrorMsg());
							//$this->body .=$this->sql_query." is the query<br>\n";
							$this->error["confirm"] = "error";
							return false;
						}

						$this->sql_query = "delete from ".$this->confirm_email_table." where mdhash = \"".$hash."\"";
						//$this->body .=$this->sql_query." is the query<br>\n";
						$email_result = $db->Execute($this->sql_query);
						if (!$email_result)
						{
							$this->site_error($this->sql_query,$db->ErrorMsg());
							$this->body .=$this->sql_query." is the query<br>\n";
							$this->error["confirm"] = "error";
							return false;
						}

				  		//send email saying registration is complete
				  		if ($this->configuration_data['send_register_complete_email_client'])
				  		{
							$mailto = $show->EMAIL;
							$subject = urldecode($this->messages[678]);
							$message = urldecode($this->messages[676])." ".$show->USERNAME."\n\n".urldecode($this->messages[677]);

							if ($this->configuration_data['email_header_break'])
								$separator = "\n";
							else
								$separator = "\r\n";
							$from = "From: ".$this->configuration_data['registration_admin_email'].$separator."Reply-to: ".$this->configuration_data['registration_admin_email'].$separator;
				  			$additional = "-f".$this->configuration_data['registration_admin_email'];
				  			//@mail($mailto, $subject, $message, $from,$additional);
							if ($this->configuration_data['email_configuration'] == 1)
								mail($mailto, $subject, $message, $from,$additional);
							elseif ($this->configuration_data['email_configuration'] == 2)
								mail($mailto, $subject, $message, $from);
							else
								mail($mailto, $subject, $message);
				  		}

				  		if ($this->configuration_data['send_register_complete_email_admin'])
				  		{
							if ($this->registered_variables["business_type"] == 1)
								$business_type = "individual";
							elseif ($this->registered_variables["business_type"] == 2)
								$business_type = "business";
							else
								$business_type = "none";
							$mailto = $this->configuration_data['registration_admin_email'];
							$subject = urldecode($this->messages[679]);

							$this->page_id = 15;
							$this->get_text($db);
							$message = "just registered: ".$show->USERNAME."\n";
							$message .= "registration code: ".$show->REGISTRATION_CODE."\n";
							$message .= "user_id: ".$this->user_id."\n";
							$message .= "email: ".$show->EMAIL."\n";
							$message .= "email2: ".$show->EMAIL2."\n";
							$message .= "company name: ".$show->COMPANY_NAME."\n";
							$message .= "business type: ".$business_type."\n";
							$message .= "first name: ".$show->FIRSTNAME."\n";
							$message .= "last name: ".$show->LASTNAME."\n";
							$message .= "address: ".$show->ADDRESS."\n";
							$message .= "address line 2: ".$show->ADDRESS_2."\n";
							$message .= "city: ".$show->CITY."\n";
							$message .= "state: ".$show->STATE."\n";
							$message .= "zip: ".$show->ZIP."\n";
							$message .= "country: ".$show->COUNTRY."\n";
							$message .= "phone: ".$show->PHONE."\n";
                            $message .= "phoneext: ".$show->PHONEEXT."\n";
							$message .= "phone 2: ".$show->PHONE_2."\n";
                            $message .= "phoneext_2: ".$show->PHONEEXT_2."\n";
							$message .= "fax: ".$show->FAX."\n";
							$message .= "url: ".$show->URL."\n";
							$message .= "optional field 1: ".$show->OPTIONAL_FIELD_1."\n";
							$message .= "optional field 2: ".$show->OPTIONAL_FIELD_2."\n";
							$message .= "optional field 3: ".$show->OPTIONAL_FIELD_3."\n";
							$message .= "optional field 4: ".$show->OPTIONAL_FIELD_4."\n";
							$message .= "optional field 5: ".$show->OPTIONAL_FIELD_5."\n";
							$message .= "optional field 6: ".$show->OPTIONAL_FIELD_6."\n";
							$message .= "optional field 7: ".$show->OPTIONAL_FIELD_7."\n";
							$message .= "optional field 8: ".$show->OPTIONAL_FIELD_8."\n";
							$message .= "optional field 9: ".$show->OPTIONAL_FIELD_9."\n";
							$message .= "optional field 10: ".$show->OPTIONAL_FIELD_10."\n";
							if ($this->configuration_data['email_header_break'])
								$separator = "\n";
							else
								$separator = "\r\n";
							$from = "From: ".$this->configuration_data['registration_admin_email'].$separator."Reply-to: ".$this->configuration_data['registration_admin_email'].$separator;
							$ip = $_SERVER['REMOTE_ADDR'];
							$host = @gethostbyaddr($ip);
							//$host = preg_replace("/^[^.]+./", "*.", $host);
							$message .= "\n".$_SERVER["REMOTE_ADDR"]." : ".$host;
							$additional = "-f".$this->configuration_data['registration_admin_email'];
				  			//@mail($mailto, $subject, $message, $from,$additional);
							if ($this->configuration_data['email_configuration'] == 1)
								mail($mailto, $subject, $message, $from,$additional);
							elseif ($this->configuration_data['email_configuration'] == 2)
								mail($mailto, $subject, $message, $from);
							else
								mail($mailto, $subject, $message);
				  		}

						if ($this->configuration_data['use_api'])
						{
							if ($this->debug_register)
								echo "including api register<br>\n";
							include_once("classes/api_register_class.php");
							include("config.php");
							$this->set_new_user_id_in_current_session($db);
							$this->registration_confirmation_success($db);
							$this->registered_variables["db_host"] = $db_host;
							$this->registered_variables["db_name"] = $database;
							$this->registered_variables["installation_type"] = 1;
							$this->registered_variables["username"] = $show->USERNAME;
							$this->registered_variables["password"] = $show->PASSWORD;
							$this->registered_variables["email"] = stripslashes(urldecode($show->EMAIL));
							$this->registered_variables["company_name"] = stripslashes(urldecode($show->COMPANY_NAME));
							$this->registered_variables["business_type"] = stripslashes(urldecode($show->BUSINESS_TYPE));
							$this->registered_variables["firstname"] = stripslashes(urldecode($show->FIRSTNAME));
							$this->registered_variables["lastname"] = stripslashes(urldecode($show->LASTNAME));
							$this->registered_variables["address"] = stripslashes(urldecode($show->ADDRESS));
							$this->registered_variables["address_2"] = stripslashes(urldecode($show->ADDRESS_2));
							$this->registered_variables["zip"] = stripslashes(urldecode($show->ZIP));
							$this->registered_variables["city"] = stripslashes(urldecode($show->CITY));
							$this->registered_variables["state"] = stripslashes(urldecode($show->STATE));
							$this->registered_variables["country"] = stripslashes(urldecode($show->COUNTRY));
							$this->registered_variables["phone"] = stripslashes(urldecode($show->PHONE));
                            $this->registered_variables["phoneext"] = stripslashes(urldecode($show->PHONEEXT));
							$this->registered_variables["phone_2"] = stripslashes(urldecode($show->PHONE_2));
                            $this->registered_variables["phoneext_2"] = stripslashes(urldecode($show->PHONEEXT_2));
							$this->registered_variables["fax"] = stripslashes(urldecode($show->FAX));
							$this->registered_variables["url"] = stripslashes(urldecode($show->URL));
							$this->registered_variables["optional_field_1"] = stripslashes(urldecode($show->OPTIONAL_FIELD_1));
							$this->registered_variables["optional_field_2"] = stripslashes(urldecode($show->OPTIONAL_FIELD_2));
							$this->registered_variables["optional_field_3"] = stripslashes(urldecode($show->OPTIONAL_FIELD_3));
							$this->registered_variables["optional_field_4"] = stripslashes(urldecode($show->OPTIONAL_FIELD_4));
							$this->registered_variables["optional_field_5"] = stripslashes(urldecode($show->OPTIONAL_FIELD_5));
							$this->registered_variables["optional_field_6"] = stripslashes(urldecode($show->OPTIONAL_FIELD_6));
							$this->registered_variables["optional_field_7"] = stripslashes(urldecode($show->OPTIONAL_FIELD_7));
							$this->registered_variables["optional_field_8"] = stripslashes(urldecode($show->OPTIONAL_FIELD_8));
							$this->registered_variables["optional_field_9"] = stripslashes(urldecode($show->OPTIONAL_FIELD_9));
							$this->registered_variables["optional_field_10"] =stripslashes(urldecode($show->OPTIONAL_FIELD_10));
							$this->registered_variables["registration_code"] =stripslashes(urldecode($show->REGISTRATION_CODE));
							if ($this->debug_register)
							{
								foreach ($this->registered_variables as $key => $value)
								{
									echo $key." - ".$value."<br>\n";
								}
							}
							$api_register = new API_Register($this->registered_variables);
							$api_register->api_insert_user();

						}
				  		return true;
				  	}
			  	}
		  	}
		}
		else
		{
			$this->error["confirm"] = "error";
			return false;
		}
	} //end of function confirm

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function confirmation_instructions ($db)
	{
		$this->page_id = 17;
		$this->get_text($db);
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr class=section_title>\n\t\t<td colspan=3>".urldecode($this->messages[1398])."</td>\n\t</tr>\n\t";
		$this->body .="<tr class=confirmation_title>\n\t<td>".urldecode($this->messages[673])."</td>\n</tr>\n";
		$this->body .="<tr class=confirmation_instructions>\n\t<td>".urldecode($this->messages[674])."</td>\n</tr>\n";
		$this->body .="</table>\n";
		$this->display_page($db);
		return true;
	} //end of function confirm

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function registration_confirmation_success($db)
	{
		$this->page_id = 18;
		$this->get_text($db);
		//registration confirmation was successfull
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr class=registration_confirmation_message>\n\t<td>".urldecode($this->messages[320])."</td>\n</tr>\n";
		$this->body .="<tr class=registration_message >\n\t<td>".urldecode($this->messages[325])."</td>\n</tr>\n";
		$this->body .="</table>\n";

		$this->display_page($db);
		return true;

	} //end of function registration_confirmation_success()

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function confirmation_error($db)
	{
		// confirmation was unsuccessfull
		//display the error message
		$this->page_id = 18;
		$this->get_text($db);
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr class=registration_title>\n\t<td>".$this->large_font_tag.urldecode($this->messages[201])."</td>\n</tr>\n";
		$this->body .="<tr class=registration_instructions>\n\t<td>".urldecode($this->messages[200])."</td>\n</tr>\n";
		$this->body .="<tr class=registration_field_error>\n\t<td>";
		if ($this->error["confirm"] == "error")
			$this->body .= urldecode($this->messages[326]);
		elseif ( $this->error["confirm"] == "error1")
			$this->body .= urldecode($this->messages[323]);
		else
			$this->body .= urldecode($this->messages[324]);
		$this->body .= "</td>\n</tr>\n";
		$this->body .="</table>\n";

	} //end of function confirmation_error()

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function registration_error($db)
	{
		//registration was unsuccessfull
		//display the error message
		$this->page_id = 19;
		$this->get_text($db);
		switch ($this->error["registration"])
		{
			case 1: $error = urldecode($this->messages[398]); break;
			default: $error = urldecode($this->messages[398]); break;
		}
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[396])."</td>\n</tr>\n";
		$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[397])."</td>\n</tr>\n";
		$this->body .="<tr class=error_message>\n\t<td>".$error."</td>\n</tr>\n";
		$this->body .="</table>\n";
		$this->display_page($db);
		return true;
	} //end of function registration_error()

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function expire_confirmations($db)
	{
		//expire the confirmation in the database that exceed the admin limit in days
		$time = $this->shifted_time($db);
		//$expire_time = $time - (86400 * $this->confirm_expiration_in_days);
		$expire_time = $time - 86400;

		$this->sql_query = "select * from ".$this->confirm_email_table." where date < ".$expire_time;
		//echo $this->sql_query." is the query<br>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			////$this->body .=$this->sql_query." is the query<br>\n";
			$this->error["confirm"] = "error";
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			//get all the ids to delete from the confirm table
			while ($show = $result->FetchNExtObject())
			{
				$this->sql_query = "delete from ".$this->confirm_table." where id = ".$show->ID;
				//echo $this->sql_query." is the query<br>\n";
				$delete_result = $db->Execute($this->sql_query);
				if (!$delete_result)
				{
					////$this->body .=$this->sql_query." is the query<br>\n";
					$this->error["confirm"] = "error";
					return false;
				}

				$this->sql_query = "delete from ".$this->confirm_email_table." where id = ".$show->ID;
				//echo $this->sql_query." is the query<br>\n";
				$delete_result = $db->Execute($this->sql_query);
				if (!$delete_result)
				{
					////$this->body .=$this->sql_query." is the query<br>\n";
					$this->error["confirm"] = "error";
					return false;
				}
			}
		}
		return true;

	} //end of function expire_confirmations()

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function end_registration($db)
	{
		$this->page_id = 15;
		$this->get_text($db);
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[1411])."</td>\n</tr>\n";
		if ($this->already_registered)
		{
			$this->body .="<tr class=end_registration>\n\t<td><br><a href=".$this->configuration_data['registration_url'].">".urldecode($this->messages[779])."</a></td>\n</tr>\n";
		}
		else
		{
			$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[243])."</td>\n</tr>\n";
			$this->body .="<tr class=end_registration>\n\t<td><br><a href=".$this->configuration_data['registration_url'].">".urldecode($this->messages[242])."</a></td>\n</tr>\n";
		}
		$this->body .="</table>\n";

		$this->remove_registration_session($db);
		$this->display_page($db);
		return true;
	} //end of function end_registration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_group_from_registration_id($db,$registration_code=0)
	{
		if ($registration_id)
		{
			$this->sql_query = "select * from ".$this->groups_table." where registration_code = \"".$registration_code."\"";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error["confirm"] = "error";
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$this->update_registration_code_use($db,0);
				$this->update_registration_code_checked($db,1);
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	} //end of get_group_from_registration_id

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_groups_for_registration_code_use($db)
	{
		$this->sql_query = "select * from ".$this->groups_table." where registration_code != \"\"";
		$registration_check_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$registration_check_result)
		{
			return false;
		}
		elseif ($registration_check_result->RecordCount() > 0)
		{
			$this->update_registration_code_use($db,1);
			return true;
		}
		else
		{
			$this->update_registration_code_use($db,0);
			$this->update_registration_code_checked($db,1);
			$this->set_default_group($db);
			return true;
		}
	} //end of check_groups_for_registration_code_use

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function registration_code_form($db)
	{
		$this->page_id = 19;
		//echo $this->page_id." is the page id<br>\n";
		$this->get_text($db);

		$registration_url = ($this->configuration_data['use_ssl_in_registration'])?$this->configuration_data['registration_ssl_url']:$this->configuration_data['registration_url'];
		//ask for the registration code to register under a special group
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n<tr>\n\t";
		$this->body .="<td>\n\t<form action=".$registration_url." method=post>\n\t";
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n\t";
		$this->body .="<tr class=section_title>\n\t\t<td>".urldecode($this->messages[613])."</td>\n\t</tr>\n\t";
		$this->body .="<tr class=page_title>\n\t\t<td>".urldecode($this->messages[232])."</td>\n\t</tr>\n\t";

		$this->body .="<tr class=page_description>\n\t\t<td>".urldecode($this->messages[233])."<br><br></td>\n\t</tr>\n\t";
		if ($this->error["registration_code"])
		{	//message442
			$this->body .="<tr class=error_message>\n\t\t<td align=center>".urldecode($this->messages[234])."</td>\n\t</tr>\n\t";
		}
		$this->body .="<tr class=registration_code_field_label>\n\t\t<td align=center>".urldecode($this->messages[235])."\n\t\t
			<input type=text name=c[registration_code] ";
		if ($this->error["registration_code"])
			$this->body .="value=\"".$this->bad_registration_code."\"";
		$this->body .=" size=30 maxlength=30 class=registration_code_data_value>\n\t\t</td>\n\t</tr>\n\t";

		$this->body .="<tr class=enter_registration_code_button>\n\t\t<td align=center>"."\n\t\t
			<input type=submit name=c[submit_registration_code] value=\"".urldecode($this->messages[236])."\" class=enter_registration_code_button tabindex=0>\n\t\t</td>\n\t</tr>\n\t";

		$this->body .="<tr class=no_code_button>\n\t\t<td align=center>"."\n\t\t
			<input type=submit name=c[bypass_registration_code] value=\"".urldecode($this->messages[237])."\" class=no_code_button>\n\t\t</td>\n\t</tr>\n\t";

		$this->body .="<tr class=end_registration_link>\n\t\t<td align=center>\n\t\t
			<a href=".$registration_url."?b=4 class=end_registration_link>".urldecode($this->messages[887])."</a>\n\t\t</td>\n\t</tr>\n\t";

		$this->body .="</table>\n\t</form>\n\t</td>\n</tr>\n</table>";

		$this->display_page($db);
		return true;

	} //end of function registration_code_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_registration_code($db,$registration_code=0)
	{
		if ($registration_code)
		{
			$this->sql_query = "select * from ".$this->groups_table." where registration_code = \"".$registration_code."\"";
			$code_result = $db->Execute($this->sql_query);
			if ($this->debug_register)
				echo $this->sql_query."<br>\n";
			if (!$code_result)
			{
				$this->error["confirm"] = "error";
				return false;
			}
			elseif ($code_result->RecordCount() == 1)
			{
				if ($this->debug_register)
					echo "registration code is good<br>\n";
				$show = $code_result->FetchNextObject();
				$this->registration_code = $registration_code;
				$this->update_registration_code($db,$registration_code);
				$this->update_registration_group($db,$show->GROUP_ID);
				$this->update_registration_code_checked($db,1);
				return true;
			}
			else
			{
				$this->error["registration_code"] = 1;
				$this->bad_registration_code = $registration_code;
				return false;
			}
		}
		else
		{
			return 0;
		}
	} //end of function check_registration_code

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_registration_code($db,$registration_code)
	{
		$this->registration_code = $registration_code;
		$this->registered_variables["registration_code"] = $registration_code;
		$this->sql_query = "update ".$this->registration_table." set
			registration_code = \"".$registration_code."\"
			where session=\"".$this->session_id."\"";
		$registration_code_checked_result = $db->Execute($this->sql_query);
		if ($this->register_debug)
			echo $this->sql_query."<br>\n";
		if (!$registration_code_checked_result)
		{
			return false;
		}
		return true;

	} //end of function update_registration_code

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_registration_code_checked($db,$registration_code_checked)
	{
		$this->registration_code_checked = $registration_code_checked;
		$this->sql_query = "update ".$this->registration_table." set
			registration_code_checked = ".$registration_code_checked."
			where session=\"".$this->session_id."\"";
		$registration_code_checked_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$registration_code_checked_result)
		{
			return false;
		}
		return true;

	} //end of function update_registration_code_checked

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_registration_code_use($db,$registration_code_use)
	{
		$this->registration_code_use = $registration_code_use;
		$this->sql_query = "update ".$this->registration_table." set
			registration_code_use = ".$registration_code_use."
			where session=\"".$this->session_id."\"";
		$registration_code_use_result = $db->Execute($this->sql_query);
		if (!$registration_code_use_result)
		{
			return false;
		}
		return true;

	} //end of function update_registration_code_use

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_personal_info_check($db,$personal_info_check)
	{
		$this->personal_info_check = $personal_info_check;
		$this->sql_query = "update ".$this->registration_table." set
			personal_info_check = ".$personal_info_check."
			where session=\"".$this->session_id."\"";
		$personal_info_check_result = $db->Execute($this->sql_query);
		if (!$personal_info_check_result)
		{
			return false;
		}
		return true;

	} //end of function update_personal_info_check

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_registration_group($db,$registration_group)
	{
		$this->registration_group = $registration_group;
		$this->sql_query = "update ".$this->registration_table." set
			registration_group = ".$registration_group."
			where session=\"".$this->session_id."\"";
		$registration_group_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$update_registration_group)
		{
			return false;
		}
		return true;

	} //end of function update_registration_code

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function set_default_group($db)
	{
		$this->sql_query = "select * from ".$this->groups_table." where default_group = 1";
		$group_result = $db->Execute($this->sql_query);
		//$this->body .=$this->sql_query."<br>\n";
		if (!$group_result)
		{
			$this->error["confirm"] = "error";
			return false;
		}
		elseif ($group_result->RecordCount() == 1)
		{
			$show_group = $group_result->FetchNextObject();
			$this->update_registration_group($db, $show_group->GROUP_ID);
			$this->update_registration_code_checked($db,1);
			return true;
		}
		else
		{
			$this->update_registration_group($db, 1);
			$this->update_registration_code_checked($db,1);
		}
	} //end of function set_default_group

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function group_splash_page($db)
	{
		if ($this->registration_group)
		{
			$registration_url = ($this->configuration_data['use_ssl_in_registration'])?$this->configuration_data['registration_ssl_url']:$this->configuration_data['registration_url'];
			$this->sql_query = "select * from ".$this->groups_table." where group_id = ".$this->registration_group;
			$group_result = $db->Execute($this->sql_query);
			if ($this->debug_register)
				echo $this->sql_query." is get_price_plan query<br>\n";
			if (!$group_result)
			{
				//echo "error<Br>\n";
				$this->error_message = $this->internal_error_message;
				$this->site_error($this->sql_query,$db->ErrorMsg());
				return false;
			}
			elseif ($group_result->RecordCount() == 1)
			{
				$group_splash_page = $group_result->FetchNextObject();
				if (strlen(trim($group_splash_page->REGISTRATION_SPLASH_CODE)) > 0)
				{
					if ($this->debug_register)
						echo "displaying splash page<br>\n";
					$this->page_id = 19;
					$this->get_text($db);
					$this->body .= stripslashes($group_splash_page->REGISTRATION_SPLASH_CODE);
					$this->body .= "<a href=".$registration_url." class=continue_registration_link>".urldecode($this->messages[722])."</a>\n\t";
					$this->display_page($db);
					exit;
				}

			}
			//echo "no result<Br>\n";
			return true;
		}
		else
		{
			return false;
		}

	} // end of function group_splash_page

//########################################################################

	function set_new_user_id_in_current_session($db)
	{
		if ($this->debug_register)
		{
			echo "<br>TOP OF SET_NEW_USER_ID_IN_CURRENT_SESSION<br>\n";	
		}
		if ($this->user_id)
		{
			$this->classified_user_id = $this->user_id;
			$this->sql_query = "select * from geodesic_sessions where classified_session = \"".$this->session_id."\"";
			$check_session_result = $db->Execute($this->sql_query);
			if ($this->debug_register) echo $this->sql_query." is the query<bR>\n";
			if (!$check_session_result)
			{
				//$this->body .=  $this->sql_query." is the query<br>\n";
				$this->auth_messages["login"] = $this->messages[132];
				return false;
			}
			elseif ($check_session_result->RecordCount() == 1)
			{
				$this->sql_query = "update geodesic_sessions set
					user_id = ".$this->user_id."
					where classified_session = \"".$this->session_id."\"";
				$session_result = $db->Execute($this->sql_query);
				if ($this->debug_register) echo $this->sql_query." is the query<bR>\n";
				if (!$session_result)
				{
					if ($this->debug_register)
					{
						echo $db->ErrorMsg()."<bR>\n";
						echo $this->sql_query." is the query<bR>\n";
					}
					$this->auth_messages["login"] = $this->messages[132];
					if ($this->debug_register)
					{
						echo "<br>BOTTOM OF SET_NEW_USER_ID_IN_CURRENT_SESSION<br>\n";	
					}					
					return false;
				}
				if ($this->debug_register)
				{
					echo "<br>BOTTOM OF SET_NEW_USER_ID_IN_CURRENT_SESSION<br>\n";	
				}					
				return true;
			}
			else
			{
				//session does not exist yet
				if ($this->debug_register)
				{
					echo "<br>BOTTOM OF SET_NEW_USER_ID_IN_CURRENT_SESSION<br>\n";	
				}				
				return true;
			}
		}
		else
		{
			if ($this->debug_register)
			{
				echo "<br>BOTTOM OF SET_NEW_USER_ID_IN_CURRENT_SESSION<br>\n";	
			}				
			return false;
		}
	} //end of function set_new_user_id_in_current_session

//########################################################################

	function get_registration_configuration_data($db)
	{
		$this->sql_query = "SELECT * FROM ".$this->registration_configuration_table;
		//echo $this->sql_query." is the query<bR>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		else
		{
			$this->registration_configuration = $result->FetchNextObject();
		}
		return true;
	} //end of function get_registration_configuration_data

//########################################################################

	function check_filter($db)
	{
		if ($this->configuration_data['use_filters'])
		{
			if ((($this->registration_configuration->REGISTRATION_OPTIONAL_1_FILTER_ASSOCIATION) ||
				($this->registration_configuration->REGISTRATION_OPTIONAL_2_FILTER_ASSOCIATION) ||
				($this->registration_configuration->REGISTRATION_OPTIONAL_3_FILTER_ASSOCIATION) ||
				($this->registration_configuration->REGISTRATION_OPTIONAL_4_FILTER_ASSOCIATION) ||
				($this->registration_configuration->REGISTRATION_OPTIONAL_5_FILTER_ASSOCIATION) ||
				($this->registration_configuration->REGISTRATION_OPTIONAL_6_FILTER_ASSOCIATION) ||
				($this->registration_configuration->REGISTRATION_OPTIONAL_7_FILTER_ASSOCIATION) ||
				($this->registration_configuration->REGISTRATION_OPTIONAL_8_FILTER_ASSOCIATION) ||
				($this->registration_configuration->REGISTRATION_OPTIONAL_9_FILTER_ASSOCIATION) ||
				($this->registration_configuration->REGISTRATION_OPTIONAL_10_FILTER_ASSOCIATION)) &&
				($this->registration_filter_id == 0))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
			return false;
	} //end of function check_filter

//########################################################################

	function filter_select($db,$filter_id=0)
	{
		//check current temp filter
		//see if there are subfilters
		if ($filter_id)
		{
			$this->sql_query = "select ".$this->filters_table.".filter_id, ".$this->filters_table.".filter_level, ".$this->filters_table.".parent_id,".$this->filters_languages_table.".filter_name
				from ".$this->filters_table.",".$this->filters_languages_table."
				where ".$this->filters_table.".filter_id = ".$this->filters_languages_table.".filter_id
				and ".$this->filters_languages_table.".language_id = ".$this->language_id."
				and ".$this->filters_table.".parent_id = ".$filter_id." order by display_order asc,".$this->filters_languages_table.".filter_name";
		}
		else
			$this->sql_query = "select ".$this->filters_table.".filter_id, ".$this->filters_table.".filter_level, ".$this->filters_table.".parent_id,".$this->filters_languages_table.".filter_name
				from ".$this->filters_table.",".$this->filters_languages_table."
				where ".$this->filters_table.".filter_id = ".$this->filters_languages_table.".filter_id
				and ".$this->filters_languages_table.".language_id = ".$this->language_id."
				and filter_level = 1 order by display_order asc,".$this->filters_languages_table.".filter_name";
		$sub_filter_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$sub_filter_result)
		{
			$this->error_message = $this->messages[5501];
			return false;
		}
		elseif ($sub_filter_result->RecordCount() > 0)
		{
			$registration_url = ($this->configuration_data['use_ssl_in_registration'])?$this->configuration_data['registration_ssl_url']:$this->configuration_data['registration_url'];
			$this->page_id = 92;
			$this->get_text($db);
			//display the form top
			$this->body .= "<form action=".$registration_url." method=post>\n";
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n\t";
			$this->body .="<tr class=section_title>\n\t\t<td>".urldecode($this->messages[1502])."</td>\n\t</tr>\n\t";
			$this->body .="<tr class=page_title>\n\t\t<td>".urldecode($this->messages[1503])."</td>\n\t</tr>\n\t";
			$this->body .="<tr class=page_description>\n\t\t<td>".urldecode($this->messages[1504])."</td>\n\t</tr>\n\t";
			$this->body .="<tr class=filter_selection>\n\t\t<td>";
			//get the parent filters to this one
			$show_level = $sub_filter_result->FetchNextObject();
			if ($show_level->PARENT_ID != 0)
			{
				//show the parent levels
				$filter_tree = $this->get_filter_level($db,$show_level->PARENT_ID);
				$this->filter_level_array = array_reverse($this->filter_level_array);
				reset ($this->filter_level_array);
				if ($filter_tree)
				{
					foreach ($this->filter_level_array as $key => $value)
						$this->body .= $this->filter_level_array[$key]["filter_name"]." > ";
				}
			}
			$sub_filter_result->Move(0);

			//show the form to select filter
			$this->body .= "<select name=registration_filter_id class=filter_dropdown onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\">\n\t\t";
			$this->body .= "<option value=\"\">".urldecode($this->messages[1505])."</option>\n\t\t";
			while ($show_filter = $sub_filter_result->FetchNextObject())
			{
				$this->body .= "<option value=".$show_filter->FILTER_ID.">".$show_filter->FILTER_NAME."</option>\n\t\t";
			}
			$this->body .= "</select>";
			//display the form bottom
			$this->body .= "</td></tr>";
			$this->body .= "</table></form>";
			$this->display_page($db);
			exit;
		}
		else
		{
			//this is the terminal filter...set it
			$this->update_filter_id($db,$filter_id);
			$this->registration_form_1($db);
		}

	} //end of function filter_select

//########################################################################

	function update_filter_id($db,$filter_id)
	{
		$this->registration_filter_id = $filter_id;
		$this->sql_query = "update ".$this->registration_table." set
			filter_id = ".$filter_id."
			where session=\"".$this->session_id."\"";
		$registration_filter_id_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$registration_filter_id_result)
		{
			return false;
		}
	} // end of update_filter_id

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_auction_price_plan($db)
	{
		$price_plan = $this->get_auction_price_plan_from_group($db,$this->registration_group);

		//check for expiration of price plans
		if ($price_plan->EXPIRATION_TYPE == 2)
		{
			//dynamic expiration of this price plan from the date of registration
			$expiration_date = ($this->shifted_time($db) + ($price_plan->EXPIRATION_FROM_REGISTRATION * 84600));

			$this->sql_query = "insert into ".$this->auctions_expirations_table."
				(type,user_id,expires,type_id)
				values
				(2,".$this->user_id.",".$expiration_date.",".$price_plan->PRICE_PLAN_ID.")";
			$plan_expiration_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$plan_expiration_result)
			{
				$this->site_error($this->sql_query,$db->ErrorMsg());
				//$this->body .=$this->sql_query." is the query<br>\n";
				$this->error["confirm"] = "error";
				return false;
			}
		}

		$this->sql_query = "insert into ".$this->user_groups_price_plans_table."
			(id,group_id,price_plan_id)
			values
			(".$this->user_id.",".$this->registration_group.",".$price_plan->PRICE_PLAN_ID.")";
		//echo $this->sql_query." is the query<br>\n";
		$group_result = $db->Execute($this->sql_query);
		if (!$group_result)
		{
			$this->site_error($this->sql_query,$db->ErrorMsg());
			//$this->body .=$this->sql_query." is the query<br>\n";
			$this->error["confirm"] = "error";
			return false;
		}

		//check to see if registration credits or free subscription period
		if ($price_plan->TYPE_OF_BILLING == 1)
		{
			//fee based subscriptions
			if ($price_plan->CREDITS_UPON_REGISTRATION > 0)
			{
				if ($price_plan->CREDITS_EXPIRE_TYPE == 1)
				{
					//expire on fixed days from registration
					$expiration = (($price_plan->CREDITS_EXPIRE_PERIOD * 86400) + $this->shifted_time($db));
				}
				elseif ($price_plan->CREDITS_EXPIRE_TYPE == 2)
				{
					//expire on fixed date
					$expiration = $price_plan->CREDITS_EXPIRE_DATE;
				}
				$this->sql_query = "insert into ".$this->user_credits_table."
					(user_id,credit_count,credits_expire)
					values
					(".$this->user_id.",".$price_plan->CREDITS_UPON_REGISTRATION.",".$expiration.")";
				//echo $this->sql_query." is the query<br>\n";
				$free_credits_result = $db->Execute($this->sql_query);
				if (!$free_credits_result)
				{
					$this->site_error($this->sql_query,$db->ErrorMsg());
					//$this->body .=$this->sql_query." is the query<br>\n";
					$this->error["confirm"] = "error";
					return false;
				}
			}
		}
		elseif ($price_plan->TYPE_OF_BILLING == 2)
		{
			//subscription based
			if ($price_plan->FREE_SUBSCRIPTION_PERIOD_UPON_REGISTRATION > 0)
			{
				//get expiration from now
				$expiration = (($price_plan->FREE_SUBSCRIPTION_PERIOD_UPON_REGISTRATION * 86400) + $this->shifted_time($db));

				$this->sql_query = "insert into ".$this->user_subscriptions_table."
					(user_id,subscription_expire)
					values
					(".$this->user_id.",".$expiration.")";
				//echo $this->sql_query." is the query<br>\n";
				$free_subscription_result = $db->Execute($this->sql_query);
				if (!$free_subscription_result)
				{
					$this->site_error($this->sql_query,$db->ErrorMsg());
					//$this->body .=$this->sql_query." is the query<br>\n";
					$this->error["confirm"] = "error";
					return false;
				}
			}
		}
	} //  end of function insert_auction_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_filter_value($db,$association=0)
	{
		if ($association)
		{
			//association is the filter level this value is associated with
			$this->sql_query = "select count(distinct(filter_level)) as level_count from ".$this->filters_table;
			$level_count_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$level_count_result)
			{
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($level_count_result->RecordCount() == 1)
			{
				$level_count = $level_count_result->FetchNextObject();
				if ($level_count->LEVEL_COUNT == $association)
				{
					//get current filter id filter name
					$this->sql_query = "select ".$this->filters_languages_table.".filter_name
						from ".$this->filters_languages_table."
						where ".$this->filters_languages_table.".language_id = ".$this->language_id."
						and ".$this->filters_languages_table.".filter_id = ".$this->registration_filter_id;
					$filter_result =  $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$filter_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[3501];
						return false;
					}
					elseif ($filter_result->RecordCount() == 1)
					{
						$show_filter_name = $filter_result->FetchNextObject();
						return $show_filter_name->FILTER_NAME;
					}
					else
						return false;

				}
				else
				{
					$filter_name = $this->get_filter_level($db,$this->registration_filter_id,$association);
					return $filter_name;
				}
			}
			else
			{
				return false;
			}
		}
		else
			return false;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_filter_level($db,$filter=0,$level_result=0)
	{
		if ($filter)
		{
			$i = 0;
			$filter_next = $filter;
			do
			{
				$this->sql_query = "select ".$this->filters_table.".filter_id,".$this->filters_table.".parent_id,
					".$this->filters_languages_table.".filter_name, ".$this->filters_table.".filter_level
					from ".$this->filters_table.",".$this->filters_languages_table."
					where ".$this->filters_table.".filter_id = ".$this->filters_languages_table.".filter_id
					and ".$this->filters_languages_table.".language_id = ".$this->language_id."
					and ".$this->filters_table.".filter_id = ".$filter_next;
				$filter_result =  $db->Execute($this->sql_query);
				//echo $this->sql_query." is the query<br>\n";
				if (!$filter_result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[3501];
					return false;
				}
				elseif ($filter_result->RecordCount() == 1)
				{
					$show_filter = $filter_result->FetchNextObject();
					$this->filter_level_array[$i]["parent_id"]  = $show_filter->PARENT_ID;
					$this->filter_level_array[$i]["filter_name"] = $show_filter->FILTER_NAME;
					$this->filter_level_array[$i]["filter_id"]   = $show_filter->FILTER_ID;
					$this->filter_level_array[$i]["filter_level"]   = $show_filter->FILTER_LEVEL;
					if (($level_result) && ($level_result == $show_filter->FILTER_LEVEL))
						return $show_filter->FILTER_NAME;
					$i++;
					$filter_next = $show_filter->PARENT_ID;
				}
				else
				{
					//echo "wrong return<Br>\n";
					return false;
				}

			} while ( $show_filter->PARENT_ID != 0 );

			return $i;
		}
		else
			return false;

	} // end of function get_filter_level

//########################################################################

	function add_initial_site_balance($db,$price_plan=0)
	{
		if ($this->debug_register)
		{
			echo "<bR>TOP OF ADD_INITIAL_SITE_BALANCE<BR>\n";
		}
		if (($price_plan) && ($this->configuration_data['positive_balances_only']) && ($this->configuration_data['use_account_balance']) && (!$initial_account_balance_given))
		{
			$this->initial_account_balance_given = 1;
			$this->sql_query = "update ".$this->userdata_table." set account_balance = ".$price_plan->INITIAL_SITE_BALANCE." where id = ".$this->user_id;
			$result = $db->Execute($this->sql_query);
			if ($this->debug_register) echo $this->sql_query."<bR>\n";
			if (!$result)
			{
				if ($this->debug_register)
				{
					echo $db->ErrorMsg()."<Br>\n";
					echo $this->sql_query."<bR>\n";
				}
			}
			return true;
		}
		else 
		{
			return false;
		}
	} //end of function add_initial_site_balance
	
//########################################################################

} //end of class Register
?>