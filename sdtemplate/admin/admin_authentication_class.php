<? //authenticate_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Admin_auth extends Admin_site {

	   var $secret;
	   var $error;
	   var $login_cookie_time;
	   var $classified_user_id;
	   var $username;
	   var $classified_level;
	   var $auth_messages;
	   var $error_messages;
	   var $error_found;

	   //email that all administration messages will be sent to
	   var $admin_email = "";

	   var $messages = array();

	   var $notify_data;

	   var $debug = 0;


//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Admin_auth ($db, $product_configuration=0)
	{
		//constuctor
		//echo "hello from admin auth<br>\n";
		$this->Admin_site($db, $product_configuration);
		$this->secret = "somethingverylong";

		$this->messages[800] = "Login Form";
		$this->messages[801] = "login instructions";
		$this->messages[802] = "<b>Admin Username:</b>";
		$this->messages[803] = "<b>Admin Password:</b>";
		$this->messages[804] = "Login";
		$this->messages[805] = "Please enter your username";
		$this->messages[806] = "Please enter your password";
		$this->messages[807] = "Please re-enter your username";
		$this->messages[808] = "Please re-enter your password";
		$this->messages[809] = "Your login information is incorrect";
		$this->messages[810] = "No account exists by that username";
		$this->messages[811] = "Edit Userdata Form";
		$this->messages[812] = "Edit userdata form instructions";
		$this->messages[813] = "username";
		$this->messages[814] = "Email address";
		$this->messages[815] = "Company name";
		$this->messages[816] = "Business type";
		$this->messages[817] = "Firstname";
		$this->messages[818] = "Lastname";
		$this->messages[819] = "Address";
		$this->messages[820] = "Address line 2";
		$this->messages[821] = "City";
		$this->messages[822] = "State";
		$this->messages[823] = "Zip Code";
		$this->messages[824] = "Country";
		$this->messages[825] = "Phone";
		$this->messages[826] = "Phone 2";
		$this->messages[827] = "Fax";
		$this->messages[828] = "Url";
		$this->messages[829] = "lost password email subject";
		$this->messages[830] = "Here is the missplaced login information";
		$this->messages[831] = "From: ";
		$this->messages[832] = "There was an error in processing your request";
		$this->messages[833] = "Change Password Form";
		$this->messages[834] = "Enter the new password twice to verify it and press submit";
		$this->messages[835] = "password verification";
		$this->messages[836] = "Please retry changing your password.";
		$this->messages[837] = "You must login before you can bid on an auction.";
		$this->messages[838] = "submit your changes";
		$this->messages[839] = "You are already logged in.";
		$this->messages[840] = "click here to logout";
		$this->messages[841] = "Admin Tools & Settings > Change Password";
		$this->messages[842] = "Edit admin login form instructions";
		$this->messages[843] = "No account exists by that email address ";
		$this->messages[844] = "Your friends name";
		$this->messages[845] = "Your friends email address";
		$this->messages[846] = "Your name";
		$this->messages[847] = "Your email address";
		$this->messages[848] = "Comments you wish to give your friend";
		$this->messages[849] = "Notify a Friend Form";
		$this->messages[850] = "Enter your friends name and email address as well as your own name and email address if you are not logged in.  Leave comments for your friend if you like and press the submit button when through.";
		$this->messages[851] = "Your friends email address is missing";
		$this->messages[852] = "Your email address is missing";
		$this->messages[853] = "Your friends email address is invalid";
		$this->messages[854] = "Your email address is invalid";
		$this->messages[855] = "Your friends name is missing";
		$this->messages[856] = "Your name is missing";
		$this->messages[857] = "Message from ";
		$this->messages[858] = "Your friend, ";
		$this->messages[859] = "thought you would be interested in this item in the Geodesic Classifieds: ";
		$this->messages[860] = "Click on the above link or cut and paste it into your browser\n\n\rThis classifieds program was created by Geodesic Solutions\n\rhttp://www.geodesicsolutions.com/products/index.htm for product information";
		$this->messages[861] = "From: ";
		$this->messages[862] = "Reply-To: ";
		$this->messages[863] = "With the following comments: ";
		$this->messages[864] = "Hello ";
		$this->messages[865] = "please choose a state";
		$this->messages[866] = "<b>Admin Login Form</b><br>";
		include('../config.php');
		if($demo == 1)
			$this->messages[867] = "Enter the administration username and password and click the login button.<br><br>username: admin<br>password: geodesic<br><br>";
		else
			$this->messages[867] = "Enter the administration username and password and click the login button.<br><br>";

	} //end of function Auth

//#############################################################################

	function login_form($username=0,$password=0)
	{
		$this->body .= "<form action=classifieds.php?a=10 method=post>\n";
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center width=600>\n";
		$this->body .= "<tr>\n\t<td colspan=3 align=center class=large_font>\n\t".$this->messages[800]."\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=3 align=center class=medium_font>\n\t".$this->messages[801]."\n\t</td>\n</tr>\n";
		if ($this->auth_messages["login"])
			$this->body .= "<tr>\n\t<td colspan=3 class=medium_font>\n\t".$this->auth_messages["login"]."\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td align=right width=50% class=medium_font>\n\t".$this->messages[802]."\n\t";
		$this->body .= "<td>\n\t<input type=text name=b[username] size=15 ";
		if ($username)
			$this->body .= "value=\"".$username."\"";
		$this->body .= ">";
		if ($this->error_messages["username"])
			$this->body .= $this->error_messages["username"];
		$this->body .= "\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\t".$this->messages[803]."\n\t</td>\n\t";
		$this->body .= "<td>\n\t<input type=password name=b[password] size=15 class=medium_font>\n\t";
		if ($this->error_messages["password"])
			$this->body .= $this->error_messages["password"];
		$this->body .= "</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=2 align=center>\n\t<input type=submit name=submit value=\"".$this->messages[804]."\">\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";

		$this->auth_messages["login"] = 0;
		$this->error_messages["username"] = 0;
		$this->error_messages["password"] = 0;

	} //end of function login_form

//#############################################################################

	function login($db,$username,$password,$license_key=0,$session=0)
	{
		if (!$session)
		{
			if ($this->debug)
				echo "there is no session value so returning false<br>\n";
			return false;
		}
		$this->error_found = 0;
		$this->auth_messages["login"] = 0;
		$this->error_messages["username"] =0;
		$this->error_messages["password"] = 0;

		if (strlen(trim($username)) == 0)
		{
			$this->error_messages["username"] = $this->messages[805];
			$this->error_found++;
		}

		if (strlen(trim($password)) == 0 )
		{
			$this->error_messages["password"] = $this->messages[806];
			$this->error_found++;
		}

		if (!eregi("^[[:alnum:]_-]+$", $username))
		{
			$this->error_messages["username"] = $this->messages[807];
			$this->error_found++;
		}

		if (!eregi("^[[:alnum:]_-]+$", $password))
		{
			$this->error_messages["password"] = $this->messages[808];
			$this->error_found++;
		}

		if ($error_found > 0)
		{
			$this->auth_messages["login"] = $this->messages[809];
			return false;
		}

		if ($this->debug)
		{
			echo $this->error_found." is the error count<br>\n";
			reset($this->error_messages);
			foreach ($this->error_messages as $key => $value)
				echo $key." is the key to ".$value."<br>\n";
		}

		$sql_query = "select id from ".$this->logins_table." where username like \"".$username."\" and password like \"".$password."\"";
		if ($this->debug) echo $sql_query." is the query<br>\n";
		$result = $db->Execute($sql_query);

		if (!$result)
		{
			$this->auth_messages["login"] = $this->messages[809];
			return false;
		}
		elseif ($result->RecordCount() == 0)
		{
			//account not found
			$this->auth_messages["login"] = $this->messages[809];
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			//account found
			$show = $result->FetchRow();
			$sql_query = "select level,email,firstname,lastname from ".$this->userdata_table." where id = ".$show["id"];
			$level_result = $db->Execute($sql_query);
			if ($this->debug) echo $sql_query." is the query<br>\n";
			if (!$level_result)
			{
				if ($this->debug)
				{
					echo $sql_query." contains an error<br>\n";
					echo $db->ErrorMsg()."<br>\n";
				}
				$this->auth_messages["login"] = $this->messages[809];
				return false;
			}
			elseif (($level_result->RecordCount() == 0) || ($level_result->RecordCount() > 1))
			{
				if ($this->debug) echo $sql_query." is the query returned the wrong result count<br>\n";
				$this->auth_messages["login"] = $this->messages[809];
				return false;
			}
			else
			{
				// User is authenticated
				if(!$this->is_auctions()
					&& !$this->is_class_auctions()
					&& !$this->is_classifieds())
				{
					if(!$this->set_license($db, $license_key))
					{
						$this->auth_messages["license_key"] = "Error saving license key.";
						return false;
					}

					if(!$this->verify_license($db))
					{
						$this->auth_messages["license_key"] = "Invalid License";
						return false;
					}
				}

				$this->classified_user_id = $show["id"];
				$this->level = $show_level["level"];
				$this->email_address = $show_level["email"];
				$this->firstname = $show_level["firstname"];
				$this->lastname = $show_level["lastname"];

				$show_level = $level_result->FetchRow();
				$sql_query = "update geodesic_sessions set
					user_id = ".$show["id"].",
					level = ".$show_level["level"]."
					where classified_session = \"".$session."\"";
				$session_result = $db->Execute($sql_query);
				if ($this->debug) echo $sql_query." is the query<bR>\n";
				if (!$session_result)
				{
					//echo $sql_query." is the query<br>\n";
					$this->auth_messages["login"] = $this->messages[132];
					return false;
				}

				return true;
			}
		}
		else
		{
			//internal account error
			//more than one account with the same login information
			$this->auth_messages["login"] = $this->messages[809];
			return false;
		}
	} //end of function login

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function auth_error()
	{
		$this->body .= "<table cellpadding=5 cellspacing=1 border=0 align=center width=600>\n";
		$this->body .= "<tr>\n\t<td class=medium_error_font>".$this->messages[832]."</td>\n</tr>\n";
		if ($this->error_message)
			$this->body .= "<tr>\n\t<td class=medium_font>\n\t".$this->error_message."</td>\n</tr>\n";
		$this->body .= "</table>\n";
	} //end of function auth_error

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function admin_login_form($db, $username=0,$password=0,$license_key=0)
	{
		if($this->is_class_auctions())
			$template = file_get_contents("login_template.html");
		elseif($this->is_auctions())
			$template = file_get_contents("login_template_a.html");
		elseif($this->is_classifieds())
			$template = file_get_contents("login_template_c.html");
		else
			// Login form for license key insertion
			$template = file_get_contents("login_template_none.html");
		include("../config.php");
		$username_label .= $this->messages[802];
		if ($this->auth_messages["login"])
			$error .= "<p>".$this->auth_messages["login"]."</p>";
		if ($this->auth_messages["javascript"])
			$error .= "<p>".$this->auth_messages["javascript"]."</p>";
		$username_field = "<input type=text name=b[username] size=20 ";
		include('../config.php');
		if($demo)
		{
			$username_field .= "value=\"admin\"";
		}
		elseif ($username)
		{
			$username_field .= "value=\"".$username."\"";
		}
		$username_field .= ">\n\t";

		$password_label = $this->messages[803];
		$password_field .= "<input type=password name=b[password] size=20 ";
		if($demo)
		{
			$password_field .= "value=\"geodesic\"";
		}
		$password_field .= ">\n\t";

		$license_label = "License Key: <br>(location: ";
		$license_label .= $_SERVER['SERVER_NAME'].")";
		$license_field = "<input name=b[license_key] size=50 class=admin_login7";
		if($license_key)
			$license_field .= " value=\"".$license_key."\"";
		$license_field .= ">\n\t";
		if ($this->auth_messages["license_key"])
			$license_error .= $this->auth_messages["license_key"];

		$submit = "<input type=image src=admin_images/btn_admin_login.gif border=0>";

		$this->sql_query = "select * from ".$this->version_table;
		$result = $db->Execute($this->sql_query);
		$show = $result->FetchRow();
		if ($this->debug_auth) echo $show["db_version"]." is version<br>\n";
		$template = str_replace("<<VERSION>>", "Version ".$show["db_version"], $template);
		$template = str_replace("<<USERNAME_LABEL>>", $username_label, $template);
		$template = str_replace("<<USERNAME_FIELD>>", $username_field, $template);
		$template = str_replace("<<PASSWORD_LABEL>>", $password_label, $template);
		$template = str_replace("<<PASSWORD_FIELD>>", $password_field, $template);
		$template = str_replace("<<ERROR>>", $error, $template);
		$template = str_replace("<<LICENSE_ERROR>>", $license_error, $template);
		$template = str_replace("<<SUBMIT_BUTTON>>", $submit, $template);

		// Check to display the license field or not
		if(!$this->is_auctions()
			&& !$this->is_class_auctions()
			&& !$this->is_classifieds())
		{
			$template = str_replace("<<LICENSE_LABEL>>", $license_label, $template);
			$template = str_replace("<<LICENSE_FIELD>>", $license_field, $template);
		}

		echo $template;

		$this->auth_messages["login"] = 0;
		$this->error_messages["username"] = 0;
		$this->error_messages["password"] = 0;

	} //end of function admin_login_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function edit_admin_login_form($db)
	{
		//if ($this->level == 1)
		//{
			$sql_query = "select * from ".$this->logins_table." where id = 1";
			$result = $db->Execute($sql_query);
			if (!$result)
			{
				$this->auth_messages["login"] = $this->messages[809];
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				include("../config.php");
				$sql_query = "select * from ".$this->userdata_table." where id = 1";
				$data_result = $db->Execute($sql_query);
				if (!$data_result)
				{
					$this->auth_messages["login"] = $this->messages[809];
					return false;
				}
				elseif ($data_result->RecordCount() == 1)
				{
					$show = $result->FetchRow();
					$show_data = $data_result->FetchRow();
					//get this users info and show the form
					$this->body .= "<form action=index.php?a=51 method=post>\n";
					$this->body .= "<table cellpadding=2 cellspacing=1 border=0>\n";
					$this->title = $this->messages[841];
					$this->description = $this->messages[842]." The email field below will
						only affect the ads that the admin places (not recommended) not any site administration or registration
						communications.  It is recommended that if the site owner wishes to places ads that he create a separate
						user account for that purpose.\n\t</td>\n</tr>\n";
					if (!$demo)
					{
						if (strlen(trim($this->auth_messages["username"])) > 0)
							$this->body .= "<tr>\n\t<td colspan=3 align=center>\n\t".$this->auth_messages["username"]."</font>\n\t</td>\n</tr>\n";
						$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\t".$this->messages[802]."\n\t</td>\n\t";
						$this->body .= "<td><input type=text name=b[username] value=\"".$show["username"]."\">\n\t</td>\n</tr>\n";
						$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\t".$this->messages[803]."\n\t</td>\n\t";
						$this->body .= "<td><input type=text name=b[password] value=\"".$show["password"]."\">\n\t</td>\n";
						if ($this->auth_messages["password"])
							$this->body .= "<td align=left class=medium_error_font>\n\t".$this->auth_messages["password"]."\n\t</td>";
						$this->body .= "</tr>\n";
						$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\t<b>Admin Email:</b><Br>\n\t</td>\n\t";
						$this->body .= "<td><input type=text name=b[email] value=\"".$show_data["email"]."\">\n\t</td>\n</tr>\n";
						$this->body .= "<tr>\n\t<td>\n\t</td>\n\t<td align=left>\n\t<input type=submit value=\"Save\">\n\t</td>\n</tr>\n";
					}
					if ($demo)
					{
						$this->body .= "<tr>\n\t<td align=center class=medium_font>\n\t<b>Fields Turned Off</b><Br>\n\t</td>\n\t<td>&nbsp</td>\n\t</tr>\n";
					}
					$this->body .= "</table>\n";
					$this->body .= "</form>\n";
					return true;
				}
				else
				{
					$this->auth_messages["login"] = $this->messages[809];
					return false;
				}
			}
			else
			{
				$this->auth_messages["login"] = $this->messages[809];
				return false;
			}
		//}
		//else
		//{
		//	$this->auth_messages["login"] = $this->messages[809];
		//	return false;
		//}

	} //end of function edit_admin_login_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_admin_login($db,$info=0)
	{
		if (($info))
		{
			//needs error checking
			//see if username already exists
			$this->sql_query = "select id from ".$this->logins_table." where username = \"".$info["username"]."\" and id != 1";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error["registration"] = urldecode($this->messages[230]);
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$this->auth_messages["username"] = "That username already exists...try another";
				$this->error_found++;
				return false;
			}
			elseif(ereg('[^A-Za-z0-9]', $info["password"]))
			{
				$this->auth_messages["password"] = "Password may only contain numbers or letters or both - all other symbols are invalid.";
				$this->error_found++;
				return false;
			}
			else
			{
				$sql_query = "update ".$this->logins_table." set
					username = \"".$info["username"]."\",
					password = \"".$info["password"]."\"
					where id = 1";
				$result = $db->Execute($sql_query);
				//echo $sql_query." is the query<br>\n";

				if (!$result)
				{

					return false;
				}
				else
				{
					$sql_query = "update ".$this->userdata_table." set
						email = \"".$info["email"]."\"
						where id = 1";
					$data_result = $db->Execute($sql_query);
					//echo $sql_query." is the query<br>\n";

					if (!$data_result)
					{
						return false;
					}
					return true;

				}
			}
		}
		else
		{
			return false;
		}

	} //end of function update_admin_login

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function set_license($db, $license_key=0)
	{
		if($license_key)
		{
			return $this->product_configuration->set_license($db, $license_key);
		}
		else
		{
			return false;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function verify_license($db)
	{
		return $this->product_configuration->verify_license($db);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
} //end of class Auth
?>
