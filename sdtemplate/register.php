<? //register.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
\**************************************************************************/

session_start();

if($_SERVER['REMOTE_ADDR'] == '81.1.223.18')
{
	//foreach($_COOKIE as $key => $value)
	//echo $key." => ".$value."<br>";
}

include_once("config.php");
include_once("classes/adodb.inc.php");
include_once("classes/site_class.php");
include_once("classes/register_class.php");
error_reporting  (E_ERROR | E_WARNING | E_PARSE);
session_start();
$debug_register = 0;
$_SESSION['attempts']++;

// Set session variable for Referrer
if(!isset($_SESSION['referrer']))
{
    if(!$_SERVER['HTTP_REFERER'])
    {
        $referrer = 'None';
    }
    else
    {
        $referrer = $_SERVER['HTTP_REFERER'];
    }

    $_SESSION['referrer'] = $referrer;
}
// Set session variable for referrer

//Reformat phone and phone_2 as N-NNN-NNN-NNNNN
// MLC 20070914 stop phone reformatting
if(false && $_REQUEST['c']['phone'])
{
	$phone = preg_replace("/([\D]+)/", "", $_REQUEST['c']['phone']);
   	if(strlen($phone) == 11) $phone = preg_replace("/(\d)([\d]{3})([\d]{3})([\d]{4})/", "\\1-\\2-\\3-\\4", $phone);
   	elseif(strlen($phone) == 10) $phone = preg_replace("/([\d]{3})([\d]{3})([\d]{4})/", "\\1-\\2-\\3", $phone);
    else $phone = preg_replace("/([\d]{3})([\d]{3})([\d]+)/", "\\1-\\2-\\3", $phone);
    $_REQUEST['c']['phone'] = $phone;
}

if(false && $_REQUEST['c']['phone_2'])
{
    $phone_2 = preg_replace("/([\D]+)/", "", $_REQUEST['c']['phone_2']);
   	if(strlen($phone_2) == 11) $phone_2 = preg_replace("/(\d)([\d]{3})([\d]{3})([\d]{4})/", "\\1-\\2-\\3-\\4", $phone_2);
   	elseif(strlen($phone_2) == 10) $phone_2 = preg_replace("/([\d]{3})([\d]{3})([\d]{4})/", "\\1-\\2-\\3", $phone_2);
    else $phone_2 = preg_replace("/([\d]{3})([\d]{3})([\d]+)/", "\\1-\\2-\\3", $phone_2);
    $_REQUEST['c']['phone_2'] = $phone_2;
}

$db = &ADONewConnection('mysql');

if($persistent_connections)
{
	//echo " Persistent Connection <bR>";
    if (!$db->PConnect($db_host, $db_username, $db_password, $database))
    {
    	echo "Could not connect to database";
        exit;
    }
}
else
{
    //echo " No Persistent Connection <bR>";
    if (!$db->Connect($db_host, $db_username, $db_password, $database))
    {
        echo "could not connect to database";
        exit;
    }
}

$db_api = &ADONewConnection('mysql');

// Connect to central API datebase
if($persistent_connections)
{
	//echo " Persistent Connection <bR>";
    if (!$db_api->PConnect($api_db_host, $api_db_username, $api_db_password, $api_database))
    {
        echo "Could not connect to database";
        exit;
    }
}
else
{
    //echo " No Persistent Connection <bR>";
    if (!$db_api->Connect($api_db_host, $api_db_username, $api_db_password, $api_database))
    {
        echo "could not connect to database";
        exit;
    }
}
// Connect to central API database

if($_REQUEST['debug'] == 'debug') $debug_register = 1;
else $debug_register = 0;

/// Create Product configuration object
include_once('products.php');
if(!$product_configuration) $product_configuration = new product_configuration($db);

$register = new Register($db,$language_id,$_COOKIE["classified_session"],$product_configuration);
/// Create Product configuration object

// Get session information
$current_time = $register->shifted_time($db);
if($_COOKIE["classified_session"])
{
	$sql_query = "SELECT * FROM geodesic_sessions WHERE classified_session = \"".$_COOKIE["classified_session"]."\"";
	$session_result = $db->Execute($sql_query);
	if($debug_register) echo $sql_query." is the query<br>\n";
	$show_sess = $session_result->FetchRow();
	if($session_result->RecordCount() == 1)
	{
		if($show_sess['last_time'] < ($current_time - 3590))
		{
        	if(isset($_COOKIE["classified_session"]))
            {
            	setcookie('classified_session', '', time()-42000, '/');
            }
    		unset($_COOKIE["classified_session"]);
		}
	}
	else
    {
    	if(isset($_COOKIE["classified_session"]))
        {
            setcookie('classified_session', '', time()-42000, '/');
        }
    	unset($_COOKIE["classified_session"]);
    }
}

// Delete old sessions
if($debug_register) echo "Currtime: ".date("F d Y H:i", $current_time)."<br>Time: ".date("F d Y H:i", time());
$sql_query = "DELETE FROM geodesic_sessions WHERE last_time < ".($current_time - 3590);
$delete_session_result = $db->Execute($sql_query);
// Delete old sessions

// Insert data into geodesic_interim_userdata
if(count($_REQUEST['c']) > 0)
{
    $sql_query_interim = "REPLACE INTO geodesic_interim_userdata (session_id,classified_session,user_id,registered,username,email,email2,newsletter,level,company_name,
    business_type,firstname,lastname,address,address_2,zip,city,state,country,phone,phone2,fax,url,date_joined,
    communication_type,rate_sum,rate_num,optional_field_1,optional_field_2,optional_field_3,optional_field_4,
    optional_field_5,optional_field_6,optional_field_7,optional_field_8,optional_field_9,optional_field_10,filter_id, referrer,subdomain,phoneext,phoneext_2,ip,attempts) values
    (\"".session_id()."\",\"".$_COOKIE["classified_session"]."\",\"\",\"NONE\",\"".$_REQUEST['c']["username"]."\",\"".$_REQUEST['c']["email"]."\",
    \"".$_REQUEST['c']["email2"]."\",
    \"0\", 0,\"".addslashes($_REQUEST['c']["company_name"])."\",
    \"".$_REQUEST['c']["business_type"]."\",\"".addslashes($_REQUEST['c']["firstname"])."\",
    \"".addslashes($_REQUEST['c']["lastname"])."\",
    \"".addslashes($_REQUEST['c']["address"])."\",\"".addslashes($_REQUEST['c']["address_2"])."\",
    \"".addslashes($_REQUEST['c']["zip"])."\",
    \"".addslashes($_REQUEST['c']["city"])."\",\"".$_REQUEST['c']["state"]."\",
    \"".$_REQUEST['c']["country"]."\",
    \"".addslashes($_REQUEST['c']["phone"])."\",\"".addslashes($_REQUEST['c']["phone_2"])."\",
    \"".addslashes($_REQUEST['c']["fax"])."\",\"".addslashes($_REQUEST['c']["url"])."\",".time().",\"\",0,0,
    \"".addslashes($_REQUEST['c']["optional_field_1"])."\",\"".addslashes($_REQUEST['c']["optional_field_2"])."\",
    \"".addslashes($_REQUEST['c'])."\",\"".addslashes($_REQUEST['c']["optional_field_4"])."\",
    \"".addslashes($_REQUEST['c']["optional_field_5"])."\",\"".addslashes($_REQUEST['c']["optional_field_6"])."\",
    \"".addslashes($_REQUEST['c']["optional_field_7"])."\",\"".addslashes($_REQUEST['c']["optional_field_8"])."\",
    \"".addslashes($_REQUEST['c']["optional_field_9"])."\",\"".addslashes($_REQUEST['c']["optional_field_10"])."\",
    \"\",
    \"".$referrer."\",
    \"".addslashes($_SERVER['HTTP_HOST'])."\",
    \"".addslashes($_REQUEST['c']["phoneext"])."\",
    \"".addslashes($_REQUEST['c']["phoneext_2"])."\",
    \"".$_SERVER['REMOTE_ADDR']."\",
    \"".$_SESSION['attempts']."\"
    )";

    $interim_userdata_result = $db_api->Execute($sql_query_interim);

    if($debug_register) echo "<hr>".$sql_query_interim."<hr>";

    if (!$interim_userdata_result)
    {
        echo "CAN'T EXECUTE SQL QUERY<br><br>";
        echo $sql_query_interim;
        return false;
    }
}
// Insert data into geodesic_interim_userdata

if ($debug_register) echo "DEBUG: 0 ".$_COOKIE["classified_session"];
if (!$_COOKIE["classified_session"])
{
        //$referrer = $_SERVER['HTTP_REFERER'];
        if ($debug_register) echo "DEBUG: 1 ".$_COOKIE["classified_session"];

        $current_time = $register->shifted_time($db);
        $sql_query = "delete from geodesic_sessions WHERE last_time < ".($current_time - 3590);
        if ($debug_register)
                echo $sql_query." is the query<br>\n";
        $delete_session_result = $db->Execute($sql_query);
        if (!$delete_session_result)
        {
                //echo $sql_query." <br>\n";
                return false;
        }

        //set session in db
        do {
                $custom_id = md5(uniqid(rand(),1));
                $custom_id = substr( $custom_id, 0,32);
                $sql_query = "SELECT classified_session FROM geodesic_sessions WHERE classified_session = \"".$custom_id."\"";
                if ($debug_register)
                        echo $sql_query." is the query<br>\n";
                $custom_id_result = $db->Execute($sql_query);
                if (!$custom_id_result)
                {
                        //echo $sql_query."<br>\n";
                        return false;
                }
        } while ($custom_id_result->RecordCount() > 0);

        //$ip = getenv("REMOTE_ADDR");
        $ip = 0;
        $sql_query = "insert into geodesic_sessions
                (classified_session,user_id,last_time,ip,level)
                values
                (\"".$custom_id."\",0,".$current_time.",\"".$ip."\",0)";
        if ($debug_register)
                echo $sql_query." is the query<br>\n";
        $insert_session_result = $db->Execute($sql_query);
        if (!$insert_session_result)
        {
                //echo $sql_query."<br>\n";
                return false;
        }
        $expires = $register->shifted_time($db) + 31536000;
        $user_id = 0;
        $user_level = 0;
        $classified_session = $custom_id;
        if ($debug_register)
                echo $classified_session." is classified_session in no cookie<br>\n";

                if($debug_register) echo "Set-Cookie: classified_session=".$custom_id." path=/; domain=".$HTTP_HOST."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires);
    header("Set-Cookie: classified_session=".$custom_id."; path=/; domain=".$HTTP_HOST."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
}
else
{
	if ($debug_register) echo "DEBUG: 2 ".$_COOKIE["classified_session"];
        $current_time = $register->shifted_time($db);
        $sql_query = "delete from geodesic_sessions WHERE last_time < ".($current_time - 3600);
        $delete_session_result = $db->Execute($sql_query);
        if ($debug_register)
                echo $sql_query." is the query<br>\n";
        if (!$delete_session_result)
        {
                //echo $sql_query."<br>\n";
                return false;
        }
        //get session information
        $sql_query = "SELECT * FROM geodesic_sessions WHERE classified_session = \"".$_COOKIE["classified_session"]."\"";
        $session_result = $db->Execute($sql_query);
        if ($debug_register)
                echo $sql_query." is the query<br>\n";
        if (!$session_result)
        {
                //echo $sql_query."<br>\n";
                return false;
        }
        elseif ($session_result->RecordCount() == 1)
        {
                //$current_ip = getenv("REMOTE_ADDR");
                $current_ip = 0;
                $show = $session_result->FetchRow();

                $sql_query = "update geodesic_sessions set last_time = ".$current_time." where classified_session = \"".$_COOKIE["classified_session"]."\"";
                $update_session_result = $db->Execute($sql_query);
                if ($debug_register)
                        echo $sql_query." is the query<br>\n";
                if (!$update_session_result)
                {
                        //echo $sql_query."<br>\n";
                        return false;
                }
                else
                {
                        if (($show["last_time"] < ($current_time + 60)) && ($current_ip == $show["ip"]))
                        {
                                $user_id = $show["user_id"];
                                $user_level = $show["level"];
                                $classified_session = $_COOKIE["classified_session"];

                                if($user_id)
                                {
                                        if(count($_REQUEST['c']) > 0)
                                        {
                                        	$sql_query_interim = "UPDATE geodesic_interim_userdata SET classified_session=\"".$classified_session."\", user_id = ".$user_id.", registered = \"USER_ALREADY_REGISTERED_IN_SYSTEM\" WHERE session_id=\"".session_id()."\"";
    										$interim_userdata_result = $db_api->Execute($sql_query_interim);

    										if($debug_register) echo "<hr>".$sql_query_interim."<hr>";

									    	if (!$interim_userdata_result)
    										{
        										echo "CAN'T EXECUTE SQL QUERY<br><br>";
        										echo $sql_query_interim;
                                            	return false;
									    	}
                                        }

                                        $_REQUEST["a"] = 10;
                                        include_once("index.php");
                                }
                        }
                        else
                        {
                                //change session
                                //setcookie("classified_session","",0,"/","$HTTP_HOST");
                                $sql_query = "delete from geodesic_sessions where classified_session = \"".$_COOKIE["classified_session"]."\"";
                                $delete_session_result = $db->Execute($sql_query);
                                if ($debug_register)
                                        echo $sql_query." is the query<br>\n";
                                if (!$delete_session_result)
                                {
                                        //echo $sql_query."<br>\n";
                                        return false;
                                }

                                if(count($_REQUEST['c']) > 0)
                                {
                                	$sql_query_interim = "UPDATE geodesic_interim_userdata SET classified_session=\"".$_COOKIE["classified_session"]."\", registered = \"EXISTS_OLD_SESSION\" WHERE session_id=\"".session_id()."\"";
                                	$interim_userdata_result = $db_api->Execute($sql_query_interim);

                                	if($debug_register) echo "<hr>".$sql_query_interim."<hr>";

                                	if (!$interim_userdata_result)
                                	{
                                    	echo "CAN'T EXECUTE SQL QUERY<br><br>";
                                    	echo $sql_query_interim;
                                    	return false;
                                	}
                                }

                                include_once("classes/authenticate_class.php");
                                $auth = new Auth($db,$_COOKIE["language_id"]);

            					if(isset($_COOKIE["classified_session"]))
            					{
                					setcookie('classified_session', '', time()-42000, '/');
            					}

                                if($debug_register) echo "Location: ".$auth->configuration_data['classifieds_url']."?".$_SERVER["QUERY_STRING"];
                                header("Location: ".$auth->configuration_data['classifieds_url']."?".$_SERVER["QUERY_STRING"]);

                        }
                }
        }
        else
        {
        		if(count($_REQUEST['c']) > 0)
                {
                	$sql_query_interim = "UPDATE geodesic_interim_userdata SET classified_session=\"".$_COOKIE["classified_session"]."\", registered = \"EXISTS_OLD_SESSION\" WHERE session_id=\"".session_id()."\"";
            		$interim_userdata_result = $db_api->Execute($sql_query_interim);

            		if($debug_register) echo "<hr>".$sql_query_interim."<hr>";

            		if (!$interim_userdata_result)
            		{
            			echo "CAN'T EXECUTE SQL QUERY<br><br>";
                		echo $sql_query_interim;
                		return false;
            		}
                }

            	if(isset($_COOKIE["classified_session"]))
            	{
                	setcookie('classified_session', '', time()-42000, '/');
            	}
                //setcookie("classified_session","",0,"/","$HTTP_HOST");
                include_once("classes/authenticate_class.php");
                $auth = new Auth($db,$_COOKIE["language_id"]);
                if($debug_register) echo "Location: ".$auth->configuration_data['classifieds_url']."?".$_SERVER["QUERY_STRING"];
                header("Location: ".$auth->configuration_data['classifieds_url']."?".$_SERVER["QUERY_STRING"]);
        }
}

if ($_REQUEST["set_language_cookie"])
{
        if ($debug_register)
        echo $_REQUEST["set_language_cookie"]." is set_language_cookie<Br>\n";
        $expires = $register->shifted_time($db) + 31536000;
        setcookie("language_id",$set_language_cookie,$expires);
        $site = new Site($db,0,$_REQUEST["set_language_cookie"],$auth->classified_user_id);
        header("Location: ".$site->configuration_data['classifieds_url']."?".$_SERVER["QUERY_STRING"]);
}

$language_id = $_COOKIE["language_id"];

// Insert data into geodesic_interim_userdata
if(count($_REQUEST['c']) > 0)
{
    $sql_query_interim = "REPLACE INTO geodesic_interim_userdata (session_id,classified_session,user_id,registered,username,email,email2,newsletter,level,company_name,
    business_type,firstname,lastname,address,address_2,zip,city,state,country,phone,phone2,fax,url,date_joined,
    communication_type,rate_sum,rate_num,optional_field_1,optional_field_2,optional_field_3,optional_field_4,
    optional_field_5,optional_field_6,optional_field_7,optional_field_8,optional_field_9,optional_field_10,filter_id, referrer,subdomain,phoneext,phoneext_2,ip,attempts) values
    (\"".session_id()."\",\"".$classified_session."\",\"\",\"NONE\",\"".$_REQUEST['c']["username"]."\",\"".$_REQUEST['c']["email"]."\",
    \"".$_REQUEST['c']["email2"]."\",
    \"0\", 0,\"".addslashes($_REQUEST['c']["company_name"])."\",
    \"".$_REQUEST['c']["business_type"]."\",\"".addslashes($_REQUEST['c']["firstname"])."\",
    \"".addslashes($_REQUEST['c']["lastname"])."\",
    \"".addslashes($_REQUEST['c']["address"])."\",\"".addslashes($_REQUEST['c']["address_2"])."\",
    \"".addslashes($_REQUEST['c']["zip"])."\",
    \"".addslashes($_REQUEST['c']["city"])."\",\"".$_REQUEST['c']["state"]."\",
    \"".$_REQUEST['c']["country"]."\",
    \"".addslashes($_REQUEST['c']["phone"])."\",\"".addslashes($_REQUEST['c']["phone_2"])."\",
    \"".addslashes($_REQUEST['c']["fax"])."\",\"".addslashes($_REQUEST['c']["url"])."\",".time().",\"\",0,0,
    \"".addslashes($_REQUEST['c']["optional_field_1"])."\",\"".addslashes($_REQUEST['c']["optional_field_2"])."\",
    \"".addslashes($_REQUEST['c'])."\",\"".addslashes($_REQUEST['c']["optional_field_4"])."\",
    \"".addslashes($_REQUEST['c']["optional_field_5"])."\",\"".addslashes($_REQUEST['c']["optional_field_6"])."\",
    \"".addslashes($_REQUEST['c']["optional_field_7"])."\",\"".addslashes($_REQUEST['c']["optional_field_8"])."\",
    \"".addslashes($_REQUEST['c']["optional_field_9"])."\",\"".addslashes($_REQUEST['c']["optional_field_10"])."\",
    \"\",
    \"".$referrer."\",
    \"".addslashes($_SERVER['HTTP_HOST'])."\",
    \"".addslashes($_REQUEST['c']["phoneext"])."\",
    \"".addslashes($_REQUEST['c']["phoneext_2"])."\",
    \"".$_SERVER['REMOTE_ADDR']."\",
    \"".$_SESSION['attempts']."\"
    )";

    $interim_userdata_result = $db_api->Execute($sql_query_interim);

    if($debug_register) echo "<hr>".$sql_query_interim."<hr>";

    if (!$interim_userdata_result)
    {
        echo "CAN'T EXECUTE SQL QUERY<br><br>";
        echo $sql_query_interim;
        return false;
    }
}
// Insert data into geodesic_interim_userdata

///
if(!$product_configuration)
        $product_configuration = new product_configuration($db);

unset($register);
$register = new Register($db,$language_id,$classified_session,$product_configuration);
///

if($debug_register) echo "Currtime: ".date("F d Y H:i", $current_time)."<br>Time: ".date("F d Y H:i", time());

if (($classified_session) && (!$register->setup_error))
{
        if ($debug_register)
        {
                echo "inside register function<hr>";
                echo $_REQUEST["b"]." is request b<br>\n";
                echo $user_id." is user_id set<Br>\n";
        }
        if ($user_id)
        {
                //cannot register -- already are registered
                $register->error["registration"] = 1;
                $register->registration_error($db);
                exit;
        }

        if ($_REQUEST["b"] == 3)
        {
                //the user has clicked the confirmation sent in the email sent to him
                //process the confirmation and put the user in the

                if ($register->confirm($db,$_REQUEST["hash"],$_REQUEST["username"]))
                {
                        if ($register->configuration_data['use_api'])
                        {
                                $db = &ADONewConnection('mysql');

                                if($persistent_connections)
                                {
                                        //echo " Persistent Connection <bR>";
                                        if (!$db->PConnect($db_host, $db_username, $db_password, $database))
                                        {
                                                echo "could not connect to database";
                                                exit;
                                        }
                                }
                                else
                                {
                                        //echo " No Persistent Connection <bR>";
                                        if (!$db->Connect($db_host, $db_username, $db_password, $database))
                                        {
                                                echo "could not connect to database";
                                                exit;
                                        }
                                }
                        }
                        else
                        {
                                //display the registration confirmation completion
                                $register->set_new_user_id_in_current_session($db);
                                $register->registration_confirmation_success($db);
                        }
                }
                else
                {
                        //display the error message from confirmation
                        $register->confirmation_error($db);
                }
        }
        elseif ($_REQUEST["b"] == 4)
        {

                $register->end_registration($db);
        }
        elseif ($_REQUEST["b"] == 5)
        {
                //reset filter
                $register->update_filter_id($db,0);
                if ($register->check_filter($db))
                        $register->filter_select($db,$_REQUEST["registration_filter_id"]);
                else
                        $register->registration_form_1($db);
        }
        else
        {
                //show the basic form to register
                if ($debug_register)
                {
                        if (is_array($_REQUEST["c"]))
                        {
                                foreach ($_REQUEST["c"] as $key => $value)
                                {
                                        echo $key." is key to ".$value."<br>\n";
                                }
                        }
                        echo "<br>\n";
                        echo $register->registration_code_checked." is registration_code_checked<br>\n";
                        echo $register->registration_code_use." is registration_code_use<br>\n";
                        echo $_REQUEST["registration_code"]." is request registration_code<BR>\n";
                        echo "Steep two.<hr> C is unexepted.<hr>";
                }
                $register->error_found = 0;
                if (!$register->registration_code_checked)
                {
                        if ($register->registration_code_use)
                        {
                                if ($debug_register)
                                {
                                        echo $_REQUEST["registration_code"]." is registration code<BR>\n";
                                }
                                if ($_REQUEST["registration_code"])
                                {
                                        if ($register->check_registration_code($db,$_REQUEST["registration_code"]))
                                        {
                                                $register->group_splash_page($db);
                                                if ($register->check_filter($db))
                                                        $register->filter_select($db,$_REQUEST["registration_filter_id"]);
                                                else
                                                        $register->registration_form_1($db);
                                        }
                                        else
                                        {
                                                $register->registration_code_form($db);
                                        }
                                }
                                elseif (strlen(trim($_REQUEST["c"]["bypass_registration_code"])) > 0)
                                {
                                        $register->update_registration_code_checked($db,1);
                                        $register->set_default_group($db);
                                        if ($register->check_filter($db))
                                                $register->filter_select($db,$_REQUEST["registration_filter_id"]);
                                        else
                                                $register->registration_form_1($db);

                                }
                                elseif (strlen(trim($_REQUEST["c"][submit_registration_code])) > 0)
                                {
                                        if ($register->check_registration_code($db,$_REQUEST["c"]["registration_code"]))
                                        {
                                                //check for group splash page
                                                $register->group_splash_page($db);
                                                if ($register->check_filter($db))
                                                        $register->filter_select($db,$_REQUEST["registration_filter_id"]);
                                                else
                                                        $register->registration_form_1($db);
                                        }
                                        else
                                        {
                                                //display error messages
                                                $register->registration_code_form($db);
                                        }
                                }
                                else
                                {
                                        $register->registration_code_form($db);
                                }
                        }
                        else
                        {
                                $register->update_registration_code_checked($db,1);
                                $register->set_default_group($db);
                                if ($register->check_filter($db))
                                        $register->filter_select($db,$_REQUEST["registration_filter_id"]);
                                else
                                        $register->registration_form_1($db);
                        }
                }
                elseif (!$register->personal_info_check)
                {
                        if ($_REQUEST["c"])
                        {
                                if ($register->check_info($db,$_REQUEST["c"]))
                                {
                                        $register->update_personal_info_check($db,1);
                                        $register->insert_user($db);
                                        if ($register->configuration_data['use_api'])
                                        {
                                                $db = &ADONewConnection('mysql');

                                                if($persistent_connections)
                                                {
                                                        //echo " Persistent Connection <bR>";
                                                        if (!$db->PConnect($db_host, $db_username, $db_password, $database))
                                                        {
                                                                echo "could not connect to database";
                                                                exit;
                                                        }
                                                }
                                                else
                                                {
                                                        //echo " No Persistent Connection <bR>";
                                                        if (!$db->Connect($db_host, $db_username, $db_password, $database))
                                                        {
                                                                echo "could not connect to database";
                                                                exit;
                                                        }
                                                }
                                        }
                                        $register->set_new_user_id_in_current_session($db);
                                        $user_id = $register->user_id;

                                        // REGISTERED STATUS SET
                                        $sql_query_interim = "UPDATE geodesic_interim_userdata SET user_id = ".$user_id.", registered = \"USER_SUCCESSFUL_REGISTERED_IN_SYSTEM\" WHERE session_id=\"".session_id()."\"";
                                        $interim_userdata_result = $db_api->Execute($sql_query_interim);

                                        if($debug_register) echo "<hr>".$sql_query_interim."<hr>";

                                        if (!$interim_userdata_result)
                                        {
                                            echo "CAN'T EXECUTE SQL QUERY<br><br>";
                                            echo $sql_query_interim;
                                            return false;
                                        }
                                        // REGISTERED STATUS SET
                                        if(isset($_COOKIE[session_name()]))
                                        {
   											setcookie(session_name(), '', time()-42000, '/');
										}
                                        session_unset();
                                        session_destroy();

                                        if ($register->configuration_data['use_email_verification_at_registration'] ||
                                                $register->configuration_data['admin_approves_all_registration'])
                                        {
                                                //do the confirmation
                                                $register->confirmation_instructions($db);
                                                $register->remove_registration_session($db);
                                        }
                                        else
                                        {
                                                $register->registration_confirmation_success($db);
                                                $register->remove_registration_session($db);
                                        }
                                }
                                else
                                {
                                        if ($register->check_filter($db))
                                                $register->filter_select($db,$_REQUEST["registration_filter_id"]);
                                        else
                                                $register->registration_form_1($db);
                                }
                        }
                        else
                        {
                                if ($register->check_filter($db))
                                        $register->filter_select($db,$_REQUEST["registration_filter_id"]);
                                else
                                        $register->registration_form_1($db);
                        }
                }
                else
                {
                        if ($register->check_filter($db))
                                $register->filter_select($db,$_REQUEST["registration_filter_id"]);
                        else
                                $register->registration_form_1($db);
                }
        }
}
else
{
        //cannot access registration without a classified session
        if ($debug_register)
        {
                echo "already logged in or register_setup error<br>\n";
                echo $register->setup_error." is setup error<BR>\n";
                echo $classified_session." is classified_session<br>\n";
        }
        $register->error["registration"] = 2;
        $register->registration_error($db);
}

?>
