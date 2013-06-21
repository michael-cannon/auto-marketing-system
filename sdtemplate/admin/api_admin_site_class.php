<? // api_admin_site_class.php

class Api_admin_site
{
	var $api_db;
	var $installations;
	var $installation_info;
	var $installation_configuration_data;
	var $sql_query;
	var $error_message;
	var $row_count;
	var $row_color1 = "ffffff";
	var $row_color2 = "f0faff";
	var $row_color_red = "659ACC";
	var $row_color_black = "659ACC";
	var $very_large_font_tag_light = "<font face=arial,helvetica size=4 color=#FFFFFF>";
	var $medium_font_tag_light = "<font face=arial,helvetica size=2 color=#FFFFFF>";
	var $medium_font_tag = "<font face=arial,helvetica size=2 color=#000000>";
	var $small_font_tag = "<font face=arial,helvetica size=1 color=#000000>";
	var $debug_admin_site = 0;
	var $connected = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Api_admin_site ()
	{
		$configFile = $_SERVER['DOCUMENT_ROOT'].'/config.php';		
		include($configFile);
		//include("../config.php");
		$adodbFile = $_SERVER['DOCUMENT_ROOT'].'/classes/adodb.inc.php';
		include($adodbFile);
		//include("../classes/adodb.inc.php");
		if ($this->debug_admin_site)
		{
			echo $api_db_username." is username<BR>\n";
			echo $api_db_host." is api_db_host<BR>\n";
			echo $api_database." is api_database<BR>\n";
		}
		if ((strlen(trim($api_db_host)) > 0) &&
			(strlen(trim($api_db_username)) > 0) &&
			(strlen(trim($api_database)) > 0))
		{

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

			if (!$this->api_db->PConnect($api_db_host, $api_db_username, $api_db_password, $api_database))
			{
				if ($this->debug_admin_site)
				{
					echo "connection COULD NOT be made to database using:<br>\nhost: ".$api_db_host."<br>database: ".$api_database."<Br>\n";

				}
				echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
				$this->send_admin_error_email(1);
				return false;
			}
			if ($this->debug_admin_site)
			{
				echo "connection made to database using:<br>\nhost: ".$api_db_host."<br>database: ".$api_database."<Br>\n";
			}
			$this->connected = 1;
			return true;
		}
		else
		{
			//display message saying api doesn't exist
			echo "<table cellpadding=3 cellspacing=0 border=0 align=center bgcolor=".$this->row_color1." width=100%>\n";
			echo "<tr bgcolor=".$this->row_color_red.">\n\t<td colspan=2>\n\t".$this->very_large_font_tag_light."<b>API Installations Management</b></font>\n\t</td>\n</tr>\n";
			echo "<tr bgcolor=".$this->row_color_red.">\n\t<td colspan=2>\n\t".$this->medium_font_tag_light."
				Your installation does not have this functionality.  You must purchase an upgrade from Geodesic Solutions
				to add this functionality to your site(s).<Br><Br>
				The API allows you to have multiple installations of Geodesic Products (all classifieds and auctions programs)
				and have the user login and personal data synchronized between all installations.  This will allow one user to
				register with one installation and their information will be registered with the other installations automatically.</td></tr>";
			echo "</table>";
			$this->connected = 0;
			return true;
		}

	} // end of function Api_admin_site

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_all_installation_info ()
	{
		//select all live installations
		$this->sql_query = "select * from geodesic_api_installation_info";
		$get_installation_result = $this->api_db->Execute($this->sql_query);
		if ($this->debug_admin_site)  echo $this->sql_query."<br>\n";
		if (!$get_installation_result)
		{
			if ($this->debug_admin_site)
			{
				echo $this->sql_query."<br>";
				echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
			}
			return false;
		}
		elseif ($get_installation_result->RecordCount() > 0)
		{
			//put in an array?
			$this->installations = $get_installation_result;
			return true;
		}
		else
		{
			$this->installations = 0;
			return true;
		}

	} //end of function get_all_installation_info

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function send_admin_error_email($api_error=0)
	{
		$this->error_message .= "An error occurred trying to register a user at the following installation:\n";
		$this->error_message .= "\n\nwith the following registration information:\n".$this->installation_info["db_host"]."\n";
		$this->error_message .= "\n\nwith the following registration information:\n".$this->installation_info["db_name"]."\n";
		$this->error_message .= "\nwith the following registration information:\n";
		if (is_array($this->user_info))
		{
			foreach ($this->user_info as $key => $value)
			{
				$this->error_message .= "\n\n".$key.":\n".$value."\n";
			}
		}

		$this->error_message .= "ERROR MAY HAVE OCURRED WITH THE FOLLOWING QUERY:\n";
		$this->error_message .= $this->sql_query;

		mail($this->installation_info["admin_email"],"api registration error",$this->error_message);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function installation_form ($installation_id=0)
	{
		if ($installation_id)
		{
			$this->get_this_installation_information($installation_id);
			echo "<form action=index.php?a=100001&b=".$installation_id." method=post>";
			echo "<table cellpadding=3 cellspacing=0 border=0 align=center bgcolor=".$this->row_color1." width=100%>\n";
			echo "<tr bgcolor=".$this->row_color_red.">\n\t<td colspan=2>\n\t".$this->very_large_font_tag_light."<b>New API Installation Form</b></font>\n\t</td>\n</tr>\n";
			echo "<tr bgcolor=".$this->row_color_red.">\n\t<td colspan=2>\n\t".$this->medium_font_tag_light."Enter the information
			to the new installation below.  When through click the save button at the bottom of the form to enter the new installations
			information into the api database.</font>\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top width=50%>\n\t".$this->medium_font_tag."name of installation:</font><br>".
				$this->small_font_tag."name you can remember this specific installation by.\n\t</td>\n\t";
			echo "<td valign=top width=50%>\n\t".$this->medium_font_tag."
				<input type=text name=c[installation_name] value=\"".$this->installation_info->INSTALLATION_NAME."\">\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color1.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."database host:</font><br>".
				$this->small_font_tag."Location of the mysql server where this installations database is.  Many times this is \"localhost\".\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[db_host] value=\"".$this->installation_info->DB_HOST."\">\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."database username:</font><br>".
				$this->small_font_tag."Username for this database.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[db_username] value=\"".$this->installation_info->DB_USERNAME."\">\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color1.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."database password:</font><br>".
				$this->small_font_tag."Password for this database.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[db_password] value=\"".$this->installation_info->DB_PASSWORD."\">\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."database name:</font><br>".
				$this->small_font_tag."Name of the database for this installation.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[db_name] value=\"".$this->installation_info->DB_NAME."\">\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."cookie path:</font><br>".
				$this->small_font_tag."This is the directory path to the this installation on the on your site.  The api will set the cookies that are needed for the
				this installation to work on the whole domain the api is installed on.  The default cookie path is set to \"/\" (if this is left blank - recommended) and the cookies should
				work for the whole domain.  You can enter a path here so that the cookie will only be set for that directory but this is not
				necessary.  If your installation is at the following url:<BR>http://www.yoursite.com/subdirectory/index.php<Br>
				the cookie path would be \"/subdirectory/\".  If this is left empty the default value \"/\" will be used - recommended.</font></td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[cookie_path] value=\"".$this->installation_info->COOKIE_PATH."\">\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."cookie domain:</font><br>".
				$this->small_font_tag."This is the domain to the this installation on the on your site.  The api will set the cookies that are needed for the
				this installation to work on the whole domain the api is installed on.  You can enter a domain here so that the cookie will only be set for that
				domain but this is not necessary.  If your installation is at the following url:<BR>http://www.yoursite.com/subdirectory/index.php<Br>
				the cookie domain would be \"www.yoursite.com\".  If this is left empty the default be set for current domain.  Set to \".yoursite.com\"
				to cover subdomains.</font></td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[cookie_domain] value=\"".$this->installation_info->COOKIE_DOMAIN."\">\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color1.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."installation type:</font><br>".
				$this->small_font_tag."Type of Geodesic Application this installation is.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=radio name=c[installation_type] ";
			if ($this->installation_info->INSTALLATION_TYPE == 1)
				echo "checked";
			echo " value=1>Enterprise Classifieds<br>
				<input type=radio name=c[installation_type] ";
			if ($this->installation_info->INSTALLATION_TYPE == 2)
				echo "checked";
			echo " value=2>Premier Classifieds<br>
				<input type=radio name=c[installation_type] ";
			if ($this->installation_info->INSTALLATION_TYPE == 3)
				echo "checked";
			echo " value=3>Full Classifieds<br>
				<input type=radio name=c[installation_type] ";
			if ($this->installation_info->INSTALLATION_TYPE == 4)
				echo "checked";
			echo " value=4>Basic Classifieds<br>
				<input type=radio name=c[installation_type] ";
			if ($this->installation_info->INSTALLATION_TYPE == 5)
				echo "checked";
			echo " value=5>Premier Auctions<br>
				<input type=radio name=c[installation_type] ";
			if ($this->installation_info->INSTALLATION_TYPE == 6)
				echo "checked";
			echo " value=6>Enterprise Auctions<br>
				<input type=radio name=c[installation_type] ";
			if ($this->installation_info->INSTALLATION_TYPE == 7)
				echo "checked";
			echo " value=7>GeoCore<br>
				<input type=radio name=c[installation_type] ";
			if ($this->installation_info->INSTALLATION_TYPE == 8)
				echo "checked";
			echo " value=8>VBulletin<br>
				<input type=radio name=c[installation_type] ";
			if ($this->installation_info->INSTALLATION_TYPE == 9)
				echo "checked";
			echo " value=9>Phorum
				\n\t</td>\n</tr>\n";

			//echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."installation active:</font><br>".
			//	$this->small_font_tag."Name of the database for this installation.\n\t
			//	</td>\n\t";
			//echo "<td valign=top>\n\t".$this->medium_font_tag."
			//	<input type=radio name=c[active] value=1>\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."admin email address:</font><br>".
				$this->small_font_tag."Email address that should be contacted if there are any errors when contacting or manipulating
				the current installation.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[admin_email] value=\"".$this->installation_info->ADMIN_EMAIL."\">\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color_black.">\n\t<td colspan=2>\n\t".$this->medium_font_tag_light."<b>Additional vBulletin Configuration</b></font>
				</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color_black.">\n\t<td colspan=2>\n\t".$this->medium_font_tag_light."The API operates on vBulletin with these caveats:<Br>
				There are no changes done to vBulletin...therefore there is no way to have changes within vBulletin (registration, login, etc) affect the Geodesic installation
				it may be connected with. You may want to turn off registration, login and update user within vBulletin.<br>
				When updating user information only the password, name and email are updated...username is not changed - this must be changed within the vBulletin admin tool.<br>
				The vBulletin cookie is currently set to encompass the entire current domain.  Logout procedure within vBulletin may not remove the cookie properly.
				</font></td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."server path to config.php:</font><br>".
				$this->small_font_tag."Path to this domain root: ".$_SERVER["DOCUMENT_ROOT"]."<br>Enter the full server path to the config.php path file (including config.php) to the vBulletin config.php
				file.  The vBulletin installation must be on the same server as your Geodesic installation.</font>.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[vbulletin_config_path] value=\"".$this->installation_info->VBULLETIN_CONFIG_PATH."\">\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."vBulletin License:</font><br>".
				$this->small_font_tag."This is the license key you were given when you bought your license.  It can also be found in the
				top of one of your vBulletin files.</font>.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[vbulletin_license_key] value=\"".$this->installation_info->VBULLETIN_LICENSE_KEY."\">\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color_black.">\n\t<td colspan=2>\n\t".$this->medium_font_tag_light."<b>Additional Phorum Configuration</b></font>
				</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color_black.">\n\t<td colspan=2>\n\t".$this->medium_font_tag_light."The API operates on Phorum with these caveats:<Br>
				There are no changes done to Phorum...therefore there is no way to have changes within Phorum (registration, login, etc) affect the Geodesic installation
				it may be connected with.  You may want to turn off registration, login and update user within Phorum.<br>
				These fields in the database are not dealt with: webpage, image, signature, icq, yahoo, aol, msn or jabber user registration values.<br>
				When updating user information only the password, name and email are updated...username is not changed - this must be changed within the phorum admin tool.<br>
				The phorum cookie is currently set to encompass the entire current domain.  Logout procedure within Phorum may not remove the cookie properly.
				</font></td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."database table prefix:</font><br>".
				$this->small_font_tag."Enter the prefix text your Phorum installation uses to place in the front of all database table names.  </font>.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[phorum_database_table_prefix] value=\"".$this->installation_info->PHORUM_DATABASE_TABLE_PREFIX."\">\n\t</td>\n</tr>\n";
			
			echo "<tr bgcolor=".$this->row_color_black.">\n\t<td colspan=2>\n\t".$this->medium_font_tag_light."Additional configuration for Cerberus:<Br>
				</font></td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."server path to directory containing 
				config.php and session.php files:</font><br>".
				$this->small_font_tag."Enter the server path to the directory containing these files.  Start with a slash and end with a slash. Like:<br>
				/home/path/to/cerberus/installation/cerberus-support-center/ </font>.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[cerberus_directory_path] value=\"".$this->installation_info->CERBERUS_DIRECTORY_PATH."\">\n\t</td>\n</tr>\n";	
			
			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."server path to directory cer_Public_GUISettings.class.php 
				file:</font><br>".
				$this->small_font_tag."Enter the server path to the directory containing these files.  Start with a slash and end with a slash. Like:<br>
				/home/path/to/cerberus/installation/cerberus-api/public-gui/ </font>.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[cerberus_publicgui_path] value=\"".$this->installation_info->CERBERUS_PUBLICGUI_PATH."\">\n\t</td>\n</tr>\n";						
		}
		else
		{
			echo "<form action=index.php?a=100002 method=post>";
			echo "<table cellpadding=3 cellspacing=0 border=0 align=center bgcolor=".$this->row_color1." width=100%>\n";
			echo "<tr bgcolor=".$this->row_color_red.">\n\t<td colspan=2>\n\t".$this->very_large_font_tag_light."<b>New API Installation Form</b></font>\n\t</td>\n</tr>\n";
			echo "<tr bgcolor=".$this->row_color_red.">\n\t<td colspan=2>\n\t".$this->medium_font_tag_light."Enter the information
			to the new installation below.  When through click the save button at the bottom of the form to enter the new installations
			information into the api database.</font>\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color1.">\n\t<td align=right valign=top width=50%>\n\t".$this->medium_font_tag."name of installation:</font><br>".
				$this->small_font_tag."name you can remember this specific installation by.\n\t</td>\n\t";
			echo "<td valign=top width=50%>\n\t".$this->medium_font_tag."
				<input type=text name=c[installation_name]>\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."database host:</font><br>".
				$this->small_font_tag."Location of the mysql server where this installations database is.  Many times this is \"localhost\".\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[db_host]>\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color1.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."database username:</font><br>".
				$this->small_font_tag."Username for this database.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[db_username]>\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."database password:</font><br>".
				$this->small_font_tag."Password for this database.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[db_password]>\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color1.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."database name:</font><br>".
				$this->small_font_tag."Name of the database for this installation.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[db_name]>\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."installation type:</font><br>".
				$this->small_font_tag."Type of Geodesic Application this installation is.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=radio name=c[installation_type] value=1>Enterprise Classifieds<br>
				<input type=radio name=c[installation_type] value=2>Premier Classifieds<br>
				<input type=radio name=c[installation_type] value=3>Full Classifieds<br>
				<input type=radio name=c[installation_type] value=4>Basic Classifieds<br>
				<input type=radio name=c[installation_type] value=5>Premier Auctions<br>
				<input type=radio name=c[installation_type] value=6>Enterprise Auctions<br>
				<input type=radio name=c[installation_type] value=7>GeoCore<br>
				<input type=radio name=c[installation_type] value=8>vBulletin<br>
				<input type=radio name=c[installation_type] value=9>Phorum<br>
				\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."cookie path:</font><br>".
				$this->small_font_tag."This is the directory path to the this installation on the on your site.  The api will set the cookies that are needed for the
				this installation to work on the whole domain the api is installed on.  The default cookie path is set to \"/\" (if this is left blank - recommended) and the cookies should
				work for the whole domain.  You can enter a path here so that the cookie will only be set for that directory but this is not
				necessary.  If your installation is at the following url:<BR>http://www.yoursite.com/subdirectory/index.php<Br>
				the cookie path would be \"/subdirectory/\".  If this is left empty the default value \"/\" will be used - recommended.</font></td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[cookie_path] value=\"".$this->installation_info->COOKIE_PATH."\">\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."cookie domain:</font><br>".
				$this->small_font_tag."This is the domain to the this installation on the on your site.  The api will set the cookies that are needed for the
				this installation to work on the whole domain the api is installed on.  You can enter a domain here so that the cookie will only be set for that
				domain but this is not necessary.  If your installation is at the following url:<BR>http://www.yoursite.com/subdirectory/index.php<Br>
				the cookie domain would be \"www.yoursite.com\".  If this is left empty the default be set for current domain.</font></td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[cookie_domain] value=\"".$this->installation_info->COOKIE_DOMAIN."\">\n\t</td>\n</tr>\n";

			//echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."installation active:</font><br>".
			//	$this->small_font_tag."Name of the database for this installation.\n\t
			//	</td>\n\t";
			//echo "<td valign=top>\n\t".$this->medium_font_tag."
			//	<input type=radio name=c[active] value=1>\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color1.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."admin email address:</font><br>".
				$this->small_font_tag."Email address that should be contacted if there are any errors when contacting or manipulating
				the current installation.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[admin_email]>\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color_black.">\n\t<td colspan=2>\n\t".$this->medium_font_tag_light."<b>Additional vBulletin Configuration</b></font>
				</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color_black.">\n\t<td colspan=2>\n\t".$this->medium_font_tag_light."The API operates on vBulletin with these caveats:<Br>
				There are no changes done to vBulletin...therefore there is no way to have changes within vBulletin (registration, login, etc) affect the Geodesic installation
				it may be connected with. You may want to turn off registration, login and update user within vBulletin.<br>
				When updating user information only the password, name and email are updated...username is not changed - this must be changed within the vBulletin admin tool.<br>
				The vBulletin cookie is currently set to encompass the entire current domain.  Logout procedure within vBulletin may not remove the cookie properly.
				</font></td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."server path to config.php:</font><br>".
				$this->small_font_tag."Path to this domain root: ".$_SERVER["DOCUMENT_ROOT"]."<br>Enter the full server path to the config.php path file (including config.php) to the vBulletin config.php
				file.  The vBulletin installation must be on the same server as your Geodesic installation.</font>.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[vbulletin_config_path] value=\"".$this->installation_info->VBULLETIN_CONFIG_PATH."\">\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."vBulletin License:</font><br>".
				$this->small_font_tag."This is the license key you were given when you bought your license.  It can also be found in the
				top of one of your vBulletin files.</font>.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[vbulletin_license_key] value=\"".$this->installation_info->VBULLETIN_LICENSE_KEY."\">\n\t</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color_black.">\n\t<td colspan=2>\n\t".$this->medium_font_tag_light."<b>Additional Phorum Configuration</b></font>
				</td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color_black.">\n\t<td colspan=2>\n\t".$this->medium_font_tag_light."The API operates on Phorum with these caveats:<Br>
				There are no changes done to Phorum...therefore there is no way to have changes within Phorum (registration, login, etc) affect the Geodesic installation
				it may be connected with.  You may want to turn off registration, login and update user within Phorum.<br>
				These fields in the database are not dealt with: webpage, image, signature, icq, yahoo, aol, msn or jabber user registration values.<br>
				When updating user information only the password, name and email are updated...username is not changed - this must be changed within the phorum admin tool.<br>
				The phorum cookie is currently set to encompass the entire current domain.  Logout procedure within Phorum may not remove the cookie properly.
				</font></td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."database table prefix:</font><br>".
				$this->small_font_tag."Enter the prefix text your Phorum installation uses to place in the front of all database table names.  </font>.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[phorum_database_table_prefix] value=\"".$this->installation_info->PHORUM_DATABASE_TABLE_PREFIX."\">\n\t</td>\n</tr>\n";
			
			echo "<tr bgcolor=".$this->row_color_black.">\n\t<td colspan=2>\n\t".$this->medium_font_tag_light."Additional configuration for Cerberus:<Br>
				</font></td>\n</tr>\n";

			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."server path to directory containing 
				config.php and session.php files:</font><br>".
				$this->small_font_tag."Enter the server path to the directory containing these files.  Start with a slash and end with a slash. Like:<br>
				/home/path/to/cerberus/installation/cerberus-support-center/ </font>.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[cerberus_directory_path] value=\"".$this->installation_info->CERBERUS_DIRECTORY_PATH."\">\n\t</td>\n</tr>\n";	
			
			echo "<tr bgcolor=".$this->row_color2.">\n\t<td align=right valign=top>\n\t".$this->medium_font_tag."server path to directory cer_Public_GUISettings.class.php 
				file:</font><br>".
				$this->small_font_tag."Enter the server path to the directory containing these files.  Start with a slash and end with a slash. Like:<br>
				/home/path/to/cerberus/installation/cerberus-api/public-gui/ </font>.\n\t
				</td>\n\t";
			echo "<td valign=top>\n\t".$this->medium_font_tag."
				<input type=text name=c[cerberus_publicgui_path] value=\"".$this->installation_info->CERBERUS_PUBLICGUI_PATH."\">\n\t</td>\n</tr>\n";				

		}

		echo "<tr><td colspan=2><input type=submit value=save></td></tr>";
		echo "</table></form>";
	} // end of function new_installation_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_new_installation ($installation_info=0)
	{
		if ($installation_info)
		{
			//check that the installation can be connected to
			//if (!$test_db->PConnect($installation_info["db_host"], $installation_info["db_username"], $installation_info["db_password"], $installation_info["db_name"]))
			//{
			//	echo "cannot connect to this installation:<br>";
			//	echo "host info: ".$installation_info["db_host"]."<br>";
			//	echo "db_username info: ".$installation_info["db_username"]."<br>";
			//	echo "db_password info: ".$installation_info["db_password"]."<br>";
			//	echo "db_name info: ".$installation_info["db_name"]."<br>";
			//	return false;
			//}

			$this->sql_query = "insert into geodesic_api_installation_info
				(installation_name,db_host,db_username,db_password,db_name,installation_type,admin_email,synchronous_login,vbulletin_config_path,phorum_database_table_prefix,vbulletin_license_key,cookie_path,cookie_domain,
				cerberus_publicgui_path,cerberus_directory_path)
				values
				(\"".$installation_info["installation_name"]."\",\"".$installation_info["db_host"]."\",\"".$installation_info["db_username"]."\",
				\"".$installation_info["db_password"]."\",\"".$installation_info["db_name"]."\",\"".$installation_info["installation_type"]."\",
				\"".$installation_info["admin_email"]."\",\"".$installation_info["synchronous_login"]."\",\"".$installation_info["vbulletin_config_path"]."\",
				\"".$installation_info["phorum_database_table_prefix"]."\",\"".$installation_info["vbulletin_license_key"]."\",\"".$installation_info["cookie_path"]."\",
				\"".$installation_info["cookie_domain"]."\",\"".$installation_info["cerberus_publicgui_path"]."\",\"".$installation_info["cerberus_directory_path"]."\")";
			if ($this->debug_admin_site)  echo $this->sql_query."<br>\n";
			$result = $this->api_db->Execute($this->sql_query);
			if (!$result)
			{
				if ($this->debug_admin_site)
				{
					echo $this->sql_query."<br>";
					echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
				}
				return false;
			}
			return true;
		}
		else
		{

		}
	} // end of function insert_new_installation

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_installation ($installation_id=0, $installation_info=0)
	{
		if (($installation_info) && ($installation_id))
		{
			$this->sql_query = "update geodesic_api_installation_info set
				installation_name = \"".$installation_info["installation_name"]."\",
				db_host = \"".$installation_info["db_host"]."\",
				db_username = \"".$installation_info["db_username"]."\",
				db_password = \"".$installation_info["db_password"]."\",
				db_name = \"".$installation_info["db_name"]."\",
				installation_type = \"".$installation_info["installation_type"]."\",
				admin_email  = \"".$installation_info["admin_email"]."\",
				vbulletin_config_path  = \"".$installation_info["vbulletin_config_path"]."\",
				vbulletin_license_key  = \"".$installation_info["vbulletin_license_key"]."\",
				cerberus_publicgui_path  = \"".$installation_info["cerberus_publicgui_path"]."\",
				cerberus_directory_path  = \"".$installation_info["cerberus_directory_path"]."\",
				phorum_database_table_prefix  = \"".$installation_info["phorum_database_table_prefix"]."\",
				cookie_path  = \"".$installation_info["cookie_path"]."\",
				cookie_domain  = \"".$installation_info["cookie_domain"]."\",
				synchronous_login  = \"".$installation_info["synchronous_login"]."\"
				where installation_id = ".$installation_id;
			if ($this->debug_admin_site) echo $this->sql_query."<br>\n";
			$result = $this->api_db->Execute($this->sql_query);
			if (!$result)
			{
				if ($this->debug_admin_site)
				{
					echo $this->sql_query."<br>";
					echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
				}
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}

	} // end of function update_installation

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function remove_installation ($installation_id=0)
	{
		if ($installation_id)
		{
			$this->sql_query = "delete from geodesic_api_installation_info where installation_id = ".$installation_id;
			if ($this->debug_admin_site)  echo $this->sql_query."<br>\n";
			$result = $this->api_db->Execute($this->sql_query);
			if (!$result)
			{
				if ($this->debug_admin_site)
				{
					echo $this->sql_query."<br>";
					echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
				}
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}


	} // end of function remove_installation

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_installations()
	{
		echo "<table cellpadding=3 cellspacing=2 border=0 align=center bgcolor=".$this->row_color1." width=100%>\n";
		echo "<tr bgcolor=".$this->row_color_red.">\n\t<td colspan=7>\n\t".$this->very_large_font_tag_light."<b>API Installations Management</b></font>\n\t</td>\n</tr>\n";
		echo "<tr bgcolor=".$this->row_color_red.">\n\t<td colspan=7>\n\t".$this->medium_font_tag_light."Below are the current installations
			you have to sync user information with the current installation.  <br><br><b>Make sure all installations are listed below
			including this installation that you want to network the users for.</b><br><br><b>Make sure the \"use api\" is turned on
			within all installations SITE CONFIGURATION > GENERAL > USE API admin settings.</b></font>\n\t</td>\n</tr>\n";
		$this->get_all_installation_info();
		if ($this->installations)
		{
			echo "<tr bgcolor=".$this->row_color_black.">\n\t<td>\n\t".$this->medium_font_tag_light."name</font>\n\t</td>\n";
			echo "<td>\n\t".$this->medium_font_tag_light."type</font>\n\t</td>\n";
			echo "<td>\n\t".$this->medium_font_tag_light."active/inactive</font>\n\t</td>\n";
			echo "<td>\n\t".$this->medium_font_tag_light."admin email address</font>\n\t</td>\n";
			echo "<td>\n\t".$this->medium_font_tag_light."edit</font>\n\t</td>\n";
			echo "<td>\n\t".$this->medium_font_tag_light."check</font>\n\t</td>\n";
			echo "<td>\n\t".$this->medium_font_tag_light."remove</font>\n\t</td>\n";
			echo "</tr>\n";
			$this->row_count = 0;
			while ($this->installation_info = $this->installations->FetchRow())
			{
				echo "<tr bgcolor=".$this->get_row_color().">\n\t<td>\n\t".
					$this->medium_font_tag.$this->installation_info["installation_name"]."\n\t</td>\n\t";
				echo "<td valign=top>\n\t".$this->medium_font_tag;
				if ($this->installation_info["installation_type"] == 1)
					echo "enterprise classified";
				elseif ($this->installation_info["installation_type"] == 2)
					echo "premier classified";
				elseif ($this->installation_info["installation_type"] == 3)
					echo "full classifieds";
				elseif ($this->installation_info["installation_type"] == 4)
					echo "basic classifieds";
				elseif ($this->installation_info["installation_type"] == 5)
					echo "premier auctions";
				elseif ($this->installation_info["installation_type"] == 6)
					echo "enterprise auctions";
				elseif ($this->installation_info["installation_type"] == 7)
					echo "GeoCore";
				elseif ($this->installation_info["installation_type"] == 8)
					echo "vBulletin";
				elseif ($this->installation_info["installation_type"] == 9)
					echo "Phorum";
				elseif ($this->installation_info["installation_type"] == 10)
					echo "Cerberus";					
				echo "\n\t</td>\n";
				echo "<td valign=top>\n\t".$this->medium_font_tag;
				if ($this->installation_info["active"])
					echo "active";
				else
					echo "inactive";
				echo "\n\t</td>\n";
				echo "<td>\n\t".$this->medium_font_tag.$this->installation_info["admin_email"]."\n\t</td>\n\t";
				echo "<td>\n\t<a href=index.php?a=100001&b=".$this->installation_info["installation_id"].">".$this->medium_font_tag."edit</font></a>\n\t</td>\n\t";
				echo "<td>\n\t<a href=index.php?a=100004&b=".$this->installation_info["installation_id"].">".$this->medium_font_tag."check db connection</font></a>\n\t</td>\n\t";
				echo "<td>\n\t<a href=index.php?a=100003&b=".$this->installation_info["installation_id"].">".$this->medium_font_tag."delete</font></a>\n\t</td>\n\t";
				echo "</tr>\n";
				$this->row_count++;
			} // end of while
		}
		else
		{
			echo "<tr><td colspan=7>\n\t".$this->medium_font_tag."there are no installations entered\n\t</td>\n\t</tr>\n";
		}
		echo "<tr bgcolor=".$this->row_color_black."><td colspan=7>\n\t<a href=index.php?a=100002>".$this->medium_font_tag_light."add new installation</a>\n\t</font>\n\t</td>\n\t</tr>\n";
		echo "</table>\n";

	} // end of function list_installations

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_row_color()
	{
		if (($this->row_count % 2) == 0)
			$row_color = $this->row_color2;
		else
			$row_color = $this->row_color1;
		return $row_color;
	} //end of function get_row_color

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_this_installation_information ($installation_id=0)
	{
		if ($installation_id)
		{
			//select all live installations
			$this->sql_query = "select * from geodesic_api_installation_info where installation_id = ".$installation_id;
			$get_installation_result = $this->api_db->Execute($this->sql_query);
			if ($this->debug_admin_site)  echo $this->sql_query."<br>\n";
			if (!$get_installation_result)
			{
				if ($this->debug_admin_site)
				{
					echo $this->sql_query."<br>";
					echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
				}
				$this->installation_info = 0;
				return false;
			}
			elseif ($get_installation_result->RecordCount() == 1)
			{
				//put in an array?
				$this->installation_info = $get_installation_result->FetchNextObject();
				return true;
			}
			else
			{
				$this->installation_info = 0;
				return false;
			}
		}
		else
		{
			$this->installation_info = 0;
			return false;
		}

	} // end of function get_this_installation_information ()

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function remove_user()
	{
		$this->get_installation_info();
		while ($this->installation_info = $this->installations->FetchRow())
		{
			if ($this->check_latent_installation())
			{
				//this is not the invoking installation
				//connect to the installation database
				if ($this->connect_to_installation_db())
				{
					//delete userdata history
					switch ($this->installation_info["installation_type"])
					{
						case 1:
						case 2:
						case 3:
						case 4:
							$this->sql_query = "delete from geodesic_classifieds_userdata_history where id = ".$this->user_id;
							break;
						case 5:
							$this->sql_query = "delete from geodesic_auctions_userdata_history where id = ".$this->user_id;
							break;
						case 6:
							$this->sql_query = "delete from geodesic_auctions_userdata_history where id = ".$this->user_id;
							break;
						case 7:
							$this->sql_query = "delete from geoistory where id = ".$this->user_id;
							break;
						case 8:
							//remove vbulletin users from vbulletin administration
							echo "must remove vBulletin users with the vBulletin administration<bR>\n";
							return true;
							break;
						case 9:
							//remove phorum users from phorum administration
							echo "must remove Phorum users with the Phorum administration<bR>\n";
							return true;
							break;
						default:
							return false;
					}
					$delete_userdata_history_result = $db->Execute($this->sql_query);
					if ($this->debug_admin_site) echo $this->sql_query."<br>\n";
					if (!$delete_userdata_history_result)
					{
						if ($this->debug_admin_site)
						{
							echo $this->sql_query."<br>";
							echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
						}
						return false;
					}

					//communications message_to
					switch ($this->installation_info["installation_type"])
					{
						case 1:
						case 2:
						case 3:
						case 4:
							$this->sql_query = "delete from geodesic_classifieds_user_communications where message_to = ".$this->user_id;
							break;
						case 5:
							$this->sql_query = "delete from geodesic_auctions_user_communications where message_to = ".$this->user_id;
							break;
						default:
							return false;
					}

					if ($this->debug_admin_site) echo $this->sql_query." is the query<br>\n";
					$delete_user_communications_result = $db->Execute($this->sql_query);
					if (!$delete_user_communications_result)
					{
						if ($this->debug_admin_site)
						{
							echo $this->sql_query."<br>";
							echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
						}
						return false;
					}

					//delete expired
					switch ($this->installation_info["installation_type"])
					{
						case 1:
						case 2:
						case 3:
						case 4:
							$this->sql_query = "delete from geodesic_classifieds_expired where seller = ".$this->user_id;
							break;
						case 5:
							$this->sql_query = "delete from geodesic_auctions_expired where seller = ".$this->user_id;
							break;
						default:
							return false;
					}
					$delete_expired_result = $db->Execute($this->sql_query);
					if ($this->debug_admin_site) echo $this->sql_query."<br>\n";
					if (!$delete_expired_result)
					{
						if ($this->debug_admin_site)
						{
							echo $this->sql_query."<br>";
							echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
						}
						return false;
					}

					//get current ads
					switch ($this->installation_info["installation_type"])
					{
						case 1:
						case 2:
						case 3:
						case 4:
							$this->sql_query = "select * from geodesic_classifieds where seller = ".$this->user_id;
							break;
						case 5:
							$this->sql_query = "delete from geodesic_auctions where seller = ".$this->user_id;
							break;
						default:
							return false;
					}

					$get_current_ads_result = $db->Execute($this->sql_query);
					if ($this->debug_admin_site) echo $this->sql_query."<br>\n";
					if (!$get_current_ads_result)
					{
						if ($this->debug_admin_site)
						{
							echo $this->sql_query."<br>";
							echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
						}
						return false;
					}
					elseif ($get_current_ads_result->RecordCount() > 0)
					{
						while ($show = $get_current_ads_result->FetchNextObject())
						{
							//delete image url
							switch ($this->installation_info["installation_type"])
							{
								case 1:
								case 2:
								case 3:
								case 4:
									$this->sql_query = "select * from geodesic_classifieds_images_urls where classified_id = ".$show->ID;
									break;
								case 5:
									$this->sql_query = "select * from geodesic_auctions_images_urls where auction_id = ".$show->ID;
									break;
								default:
									return false;
							}

							$get_url_result = $db->Execute($this->sql_query);
							if ($this->debug_admin_site) echo $this->sql_query."<br>\n";
							if (!$get_url_result)
							{
								if ($this->debug_admin_site)
								{
									echo $this->sql_query."<br>";
									echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
								}
								return false;
							}
							elseif ($get_url_result->RecordCount())
							{
								while ($show_url = $get_url_result->FetchNextObject())
								{
									if ($show_url->FULL_FILENAME)
										unlink($show_url->FILE_PATH.$show_url->FULL_FILENAME);
									if ($show_url->THUMB_FILENAME)
										unlink($show_url->FILE_PATH.$show_url->THUMB_FILENAME);
								}
								switch ($this->installation_info["installation_type"])
								{
									case 1:
									case 2:
									case 3:
									case 4:
										$this->sql_query = "delete from geodesic_classifieds_images_urls where classified_id = ".$show->ID;
										break;
									case 5:
										$this->sql_query = "select * from geodesic_auctions_images_urls where auction_id = ".$show->ID;
										break;
									default:
										return false;
								}

								$delete_url_result = $db->Execute($this->sql_query);
								if ($this->debug_admin_site) echo $this->sql_query."<br>\n";
								if (!$delete_url_result)
								{
									if ($this->debug_admin_site)
									{
										echo $this->sql_query."<br>";
										echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
									}
									return false;
								}
							}

							//delete images
							switch ($this->installation_info["installation_type"])
							{
								case 1:
								case 2:
								case 3:
								case 4:
									$this->sql_query = "delete from geodesic_classifieds_images where classified_id = ".$show->ID;
									break;
								case 5:
									$this->sql_query = "select * from geodesic_auctions_images where auction_id = ".$show->ID;
									break;
								default:
									return false;
							}
							$delete_result = $db->Execute($this->sql_query);
							if ($this->debug_admin_site) echo $this->sql_query."<br>\n";
							if (!$delete_result)
							{
								if ($this->debug_admin_site)
								{
									echo $this->sql_query."<br>";
									echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
								}
								return false;
							}

							//delete ads extra questions
							switch ($this->installation_info["installation_type"])
							{
								case 1:
								case 2:
								case 3:
								case 4:
									$this->sql_query = "delete from geodesic_classifieds_ads_extra where classified_id = ".$show->ID;
									break;
								case 5:
									$this->sql_query = "select * from geodesic_auctions_ads_extra where auction_id = ".$show->ID;
									break;
								default:
									return false;
							}
							$delete_extras_result = $db->Execute($this->sql_query);
							if ($this->debug_admin_site) echo $this->sql_query."<br>\n";
							if (!$delete_extras_result)
							{
								if ($this->debug_admin_site)
								{
									echo $this->sql_query."<br>";
									echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
								}
								return false;
							}

							//delete ads
							switch ($this->installation_info["installation_type"])
							{
								case 1:
								case 2:
								case 3:
								case 4:
									$this->sql_query = "delete from geodesic_classifieds where id = ".$show->ID;
									break;
								case 5:
									$this->sql_query = "delete from geodesic_auctions where id = ".$show->ID;
									break;
								default:
									return false;
							}

							if ($this->debug_admin_site) echo $this->sql_query."<br>\n";
							$delete_classifieds_result = $db->Execute($this->sql_query);
							if (!$delete_classifieds_result)
							{
								if ($this->debug_admin_site)
								{
									echo $this->sql_query."<br>";
									echo "<br><br>Database Error:<br>".$this->api_db->ErrorMsg()."<BR><Br>\n";
								}
								return false;
							}

							$this->update_category_count($db,$show->CATEGORY);
						}
					}

				} //end of connect
			} // end of check
		} // end of while
	} // end of function remove_user

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_connection_to_installation_db($installation_id=0)
	{
		if ($installation_id)
		{
			$this->get_this_installation_information($installation_id);
			
			if ($this->installation_info->INSTALLATION_TYPE == 10)
			{
				echo "<table cellpadding=3 cellspacing=0 border=0 align=center bgcolor=".$this->row_color1." width=100%>\n";
				echo "<tr bgcolor=".$this->row_color_red.">\n\t<td>\n\t".$this->very_large_font_tag."<b>Cerberus interaction does not require database connection</b></font>\n\t</td>\n</tr>\n";
				echo "</table>\n";			
			}
			else 
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
	
				echo "<table cellpadding=3 cellspacing=0 border=0 align=center bgcolor=".$this->row_color1." width=100%>\n";
				echo "<tr bgcolor=".$this->row_color_red.">\n\t<td>\n\t".$this->very_large_font_tag."<b>Check Connection to Installation</b></font>\n\t</td>\n</tr>\n";
				echo "<tr bgcolor=".$this->row_color_red.">\n\t<td>\n\t".$this->medium_font_tag."The information below was
					used to test a connection to that database.  The results of that test are below.</font>\n\t</td>\n</tr>\n";
	
	
				if (!$this->installation_db->PConnect($this->installation_info->DB_HOST, $this->installation_info->DB_USERNAME, $this->installation_info->DB_PASSWORD, $this->installation_info->DB_NAME))
				{
					echo "<tr><td><br>".$this->medium_font_tag."Could NOT connect to installation using the information below:<Br><bR>
						host: ".$this->installation_info->DB_HOST."<br>
						username: ".$this->installation_info->DB_USERNAME."<br>
						password: ".$this->installation_info->DB_PASSWORD."<br>
						database name: ".$this->installation_info->DB_NAME."<br><br>
						With the following error:<br>".$this->installation_db->ErrorMsg()."</font></td></tr>";
				}
				else
				{
					echo "<tr><td><br>".$this->medium_font_tag."Database connection established</font></td></tr>";
	
				}
				echo "<tr><td><br><a href=index.php?a=100000>".$this->medium_font_tag."Return to Installation List</font></a></td></tr>";
				echo "</table>";
				if ($this->debug_admin_site) echo "returning true in check_connection_to_installation_db function<bR>\n";
			}
			return true;
		}
		else
		{
			if ($this->debug_admin_site) echo "returning false in check_connection_to_installation_db function<bR>\n";
			return false;
		}
	} // end of function check_connection_to_installation_db()

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end of class Api_admin_site
?>