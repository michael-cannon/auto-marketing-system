<?php //root file(index)
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$get_execution_time =0;
$last_time = 0;

if ($get_execution_time)
{
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$starttime = $mtime;
	$last_time = $starttime;

	function get_end_time($starttime)
	{
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = ($endtime - $starttime);
		echo "This page was created in ".$totaltime." seconds";
	} // end of function get_end_time

	function get_this_time($lasttime)
	{
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$currenttime = $mtime;
		$totaltime = ($currenttime - $lasttime);
		echo "This time = ".$totaltime." seconds";
		return $currenttime;
	} // end of function get_end_time

}



error_reporting  (E_ERROR | E_WARNING | E_PARSE);

include_once("config.php");
include_once("classes/adodb.inc.php");
include_once("classes/site_class.php");

if (isset ($HTTP_SERVER_VARS))
{
	$_SERVER = $HTTP_SERVER_VARS;
}
$db = &ADONewConnection('mysql');
//$db = &ADONewConnection('access');
//$db = &ADONewConnection('ado');
//$db = &ADONewConnection('ado_mssql');
//$db = &ADONewConnection('borland_ibase');
//$db = &ADONewConnection('csv');
//$db = &ADONewConnection('db2');
//$db = &ADONewConnection('fbsql');
//$db = &ADONewConnection('firebird');
//$db = &ADONewConnection('ibase');
//$db = &ADONewConnection('informix');
//$db = &ADONewConnection('mssql');
//$db = &ADONewConnection('mysqlt');
//$db = &ADONewConnection('oci8');
//$db = &ADONewConnection('oci8po');
//$db = &ADONewConnection('odbc');
//$db = &ADONewConnection('odbc_mssql');
//$db = &ADONewConnection('odbc_oracle');
//$db = &ADONewConnection('oracle');
//$db = &ADONewConnection('postgres7');
//$db = &ADONewConnection('postgress');
//$db = &ADONewConnection('proxy');
//$db = &ADONewConnection('sqlanywhere');
//$db = &ADONewConnection('sybase');
//$db = &ADONewConnection('vfp');

$debug = 0;
$cookie_debug = 0;
$debug_closing = 0; //to use change to 1 and go to the "a=950" case on this file

if($persistent_connections)
{
	//echo " Persistent Connection <bR>";
	if (!$db->PConnect($db_host, $db_username, $db_password, $database))
	{
		echo "could not connect to database";
		if ($get_execution_time) get_end_time($starttime);
		exit;
	}
}
else
{
	//echo " No Persistent Connection <bR>";
	//echo $db_host." is db_host<br>\n";
	//echo $db_username." is db_username<br>\n";
	//echo $db_password." is db_password<br>\n";
	//echo $database." is database<br>\n";
	//echo $db." is db<br>\n";
	if (!$db->Connect($db_host, $db_username, $db_password, $database))
	{
		echo "could not connect to database";
		if ($get_execution_time) get_end_time($starttime);
		exit;
	}
}
//reset ($_POST);
//foreach ($_POST as $key => $value)
//{
//	if ($value != "none")
//	{
//		if (!is_array($value))
//		{
//			echo $key." is the key and this is the value - ".$value."<br>\n";
//		}
//		else
//		{
//			foreach ($value as $category_specific_key => $category_specific_value)
//			{
//				$this->classified_variables[$key][$category_specific_key] = stripslashes($category_specific_value);
//				echo $key."[".$category_specific_key."] is the category_specific_key - ".$category_specific_value."<br>\n";
//			}
//		}
//
//	}
//}
if (isset($HTTP_COOKIE_VARS))
	$_COOKIE = $HTTP_COOKIE_VARS;

if ($cookie_debug) echo $_COOKIE["classified_session"]." is the classified_session id<bR>\n";

include_once('products.php');
if(!$product_configuration)
	$product_configuration = new product_configuration($db);

include_once("classes/authenticate_class.php");
$auth = new Auth($db,$language_id);

$current_time = $auth->shifted_time($db);
$sql_query = "delete from geodesic_sessions WHERE last_time < ".($current_time - 3600);
if ($cookie_debug) echo $sql_query." is the query<br>\n";
$delete_session_result = $db->Execute($sql_query);
if (!$delete_session_result)
{
	if ($cookie_debug)
	{
		echo $db->ErrorMsg()."<Br>\n";
		echo $sql_query."<br>\n";
	}
	return false;
}

if (!$_COOKIE["classified_session"])
{
	//set session in db
	do {
		$custom_id = md5(uniqid(rand(),1));
		$custom_id = substr( $custom_id, 0,32);
		$sql_query = "SELECT classified_session FROM geodesic_sessions WHERE classified_session = \"".$custom_id."\"";
		//echo $sql_query." is the query<br>\n";
		$custom_id_result = $db->Execute($sql_query);
		if (!$custom_id_result)
		{
			if ($cookie_debug)
			{
				echo $db->ErrorMsg()."<Br>\n";
				echo $sql_query."<br>\n";
			}
			return false;
		}
	} while ($custom_id_result->RecordCount() > 0);
	//$ip = getenv("REMOTE_ADDR");
	$ip = 0;
	$sql_query = "insert into geodesic_sessions
		(classified_session,user_id,last_time,ip,level)
		values
		(\"".$custom_id."\",0,".$current_time.",\"".$ip."\",0)";
	//echo $sql_query." is the query<br>\n";
	$insert_session_result = $db->Execute($sql_query);
	if (!$insert_session_result)
	{
		if ($cookie_debug)
		{
			echo $db->ErrorMsg()."<Br>\n";
			echo $sql_query."<br>\n";
		}
		return false;
	}
	$expires = time() + 31536000;
	$user_id = 0;
	$user_level = 0;
	$classified_session = $custom_id;
	header("Set-Cookie: classified_session=".$custom_id."; path=/; domain=".$_SERVER["HTTP_HOST"]."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
}
else
{
	//get session information
	$sql_query = "SELECT * FROM geodesic_sessions WHERE classified_session = \"".$_COOKIE["classified_session"]."\"";
	$session_result = $db->Execute($sql_query);
	//echo $sql_query." is the query<br>\n";
	if (!$session_result)
	{
		if ($cookie_debug) echo $sql_query."<br>\n";
		return false;
	}
	elseif ($session_result->RecordCount() == 1)
	{
		//$current_ip = getenv("REMOTE_ADDR");
		$current_ip = 0;
		$show = $session_result->FetchRow();

		$sql_query = "update geodesic_sessions set last_time = ".$current_time." where classified_session = \"".$_COOKIE["classified_session"]."\"";
		$update_session_result = $db->Execute($sql_query);
		//echo $sql_query." is the query<br>\n";
		if (!$update_session_result)
		{
			if ($cookie_debug)
			{
				echo $db->ErrorMsg()."<Br>\n";
				echo $sql_query."<br>\n";
			}
			return false;
		}
		elseif ($session_result->RecordCount() == 1)
		{
			if (($show["last_time"] < ($current_time + 60)) && ($current_ip == $show["ip"]))
			{
				$user_id = $show["user_id"];
				$user_level = $show["level"];
			}
			else
			{
				//change session
				setcookie("classified_session","",0,"/","$HTTP_HOST");
				$sql_query = "delete from geodesic_sessions where classified_session = \"".$_COOKIE["classified_session"]."\"";
				$delete_session_result = $db->Execute($sql_query);
				//echo $sql_query." is the query<br>\n";
				if (!$delete_session_result)
				{
					if ($cookie_debug)
					{
						echo $db->ErrorMsg()."<Br>\n";
						echo $sql_query."<br>\n";
					}
					return false;
				}
				if (($_REQUEST["a"]) && ($_REQUEST["b"]) && (!$_REQUEST["c"]) && (!$_REQUEST["d"]) && (!$_REQUEST["e"]))
					header("Location: ".$auth->configuration_data['classifieds_url']."?a=".$_REQUEST["a"]."&b=".$_REQUEST["b"]);
				else
					header("Location: ".$auth->configuration_data['classifieds_url']."?".$_SERVER["QUERY_STRING"]);


			}
		}
		else
		{
			setcookie("classified_session","",0,"/","$HTTP_HOST");
			if (($_REQUEST["a"]) && ($_REQUEST["b"]) && (!$_REQUEST["c"]) && (!$_REQUEST["d"]) && (!$_REQUEST["e"]))
				header("Location: ".$auth->configuration_data['classifieds_url']."?a=".$_REQUEST["a"]."&b=".$_REQUEST["b"]);
			else
				header("Location: ".$auth->configuration_data['classifieds_url']."?".$_SERVER["QUERY_STRING"]);

		}
	}
	else
	{
		$ip = 0;
		$sql_query = "insert into geodesic_sessions
			(classified_session,user_id,last_time,ip,level)
			values
			(\"".$_COOKIE["classified_session"]."\",0,".$current_time.",\"".$ip."\",0)";
		//echo $sql_query." is the query<br>\n";
		$insert_session_result = $db->Execute($sql_query);
		if (!$insert_session_result)
		{
			if ($cookie_debug)
			{
				echo $db->ErrorMsg()."<Br>\n";
				echo $sql_query."<br>\n";
			}
			return false;
		}
		//setcookie("classified_session","",0,"/","$HTTP_HOST");
		if (($_REQUEST["a"]) && ($_REQUEST["b"]) && (!$_REQUEST["c"]) && (!$_REQUEST["d"]) && (!$_REQUEST["e"]))
			header("Location: ".$auth->configuration_data['classifieds_url']."?a=".$_REQUEST["a"]."&b=".$_REQUEST["b"]);
		else
			header("Location: ".$auth->configuration_data['classifieds_url']."?".$_SERVER["QUERY_STRING"]);
	}
}

if ($_REQUEST["set_language_cookie"])
{
	$expires = time() + 31536000;
	setcookie("language_id",$_REQUEST["set_language_cookie"],$expires);
	$language_id = $_REQUEST["set_language_cookie"];
	$auth->reset_language($db,$_REQUEST["set_language_cookie"]);
	//header("Location: ".$auth->configuration_data['classifieds_url']);
	//$db->Close();
	//exit;
}
elseif ($_COOKIE["language_id"])
{
	$language_id = $_COOKIE["language_id"];
}
else
{
	//get default language
	$sql_query = "SELECT language_id FROM geodesic_pages_languages where default_language = 1";
	$default_language_result = $db->Execute($sql_query);
	//echo $sql_query."<bR>";
	if (($default_language_result) && ($default_language_result->RecordCount() == 1))
	{
		//echo "am here got cookie<br>";
		$show_language_id = $default_language_result->FetchRow();
		$expires = time() + 31536000;
		setcookie("language_id",$show_language_id["language_id"],$expires);
		$auth->reset_language($db,$show_language_id["language_id"]);
		$language_id = $show_language_id["language_id"];
	}
	else
	{

		$language_id = 1;
	}
}

if ($debug)
{
	highlight_string(print_r($set_filter_id,1));
}

if (is_array($set_filter_id))
{
	reset($set_filter_id);
	foreach ($set_filter_id as $value)
	{
		if ($value == "clear")
		{
			$filter_id = "";
			break;
		}
		elseif (strlen(trim($value)) > 0)
		{
			if ($debug) echo $value." is the filter_id<br>\n";
			$filter_id = $value;
			//break;
		}
	}
	$expires = time() + 86400;
	setcookie("filter_id","",0);
	setcookie("filter_id",$filter_id,$expires);
	if ($debug)
	{
		echo $filter_id." is filter_id<bR>\n";
		echo $set_filter_id." is set_filter_id - \n";
		foreach ($set_filter_id as $value)
		{
			echo $value." - \n";
			if ($value == "clear")
			{
				$filter_id = "";
				break;
			}
			elseif (strlen(trim($value)) > 0)
			{
				$filter_id = $value;
				//break;
			}
		}
		echo "<br><bR>\n";
	}
}
elseif ($_COOKIE["filter_id"])
{
	$filter_id = $_COOKIE["filter_id"];
}

if ($_POST["set_state_filter"])
{
	$expires = time() + 31536000;
	setcookie("state_filter","",0);
	if (($_POST["set_state_filter"] != "clear state") && ($_POST["clear_zip_filter"] != "clear localizer"))
	{
		setcookie("state_filter",$_POST["set_state_filter"],$expires);
		$state_filter = $set_state_filter;
	}
	elseif (($_POST["set_state_filter"] == "clear state") || ($_POST["clear_zip_filter"] == "clear localizer"))
	{
		$state_filter = "";
	}
	else
		$state_filter = "";
}
elseif ($_COOKIE["state_filter"])
{
	$state_filter = $_COOKIE["state_filter"];
}
else
	$state_filter = "";

if (($_POST["set_zip_filter"]) && ($_POST["set_zip_filter_distance"]))
{
	$expires = time() + 31536000;
	setcookie("zip_filter","",0);
	setcookie("zip_distance_filter","",0);
	if (strlen(trim($_POST["submit_zip_filter"])) > 0)
	{
		setcookie("zip_filter",$_POST["set_zip_filter"],$expires);
		$zip_filter = $_POST["set_zip_filter"];
		setcookie("zip_distance_filter",$_POST["set_zip_filter_distance"],$expires);
		$zip_distance_filter = $_POST["set_zip_filter_distance"];
		if ($debug) echo "setting zip filter cookie to ".$_POST["set_zip_filter"]."<br>";
	}
	else
	{
		setcookie("state_filter","",0);
		$zip_filter = 0;
		$zip_distance_filter = 0;
		$state_filter = "";
	}
}
elseif (($_COOKIE["zip_distance_filter"]) && ($_COOKIE["zip_filter"]))
{
	$zip_distance_filter = $_COOKIE["zip_distance_filter"];
	$zip_filter = $_COOKIE["zip_filter"];
	if ($debug) echo $_COOKIE["zip_filter"]." is the zip_filter from cookie<bR>\n";
}

if ($debug)
{
	echo $user_id." is user_id<br>\n";
	echo $_REQUEST["a"]." is a<bR>\n";
}

switch ($_REQUEST["a"]) {
	case 1:
		//put an ad into the classifieds
		if ($user_id)
		{
			include_once("classes/classified_sell_class.php");
			$sell = new Classified_sell($db,$user_id,$language_id,$_COOKIE["classified_session"], $_REQUEST['copy_id'], $product_configuration);
			if ($sell->configuration_data->IP_BAN_CHECK) $sell->check_ip($db);
			if (strlen(trim($sell->setup_error)) == 0)
			{
				if (!$sell->check_splash)
					$sell->display_splash_page($db);
				if ($debug)
				{
					echo $sell->terminal_category." is terminal_category<br>\n";
					echo $sell->sell_type." is sell_type<Br>\n";
				}
				if($sell->is_class_auctions() && !$sell->sell_type && $product_configuration->site->configuration_data['listing_type_allowed']==0)
				{
					if($_REQUEST["set_type"])
						$sell->set_sell_type($db, $_REQUEST["set_type"]);
					else
					{
						// is this item going to be an auction or a classified
						$sell->choose_sell_type($db);
						break;
					}
				}
				elseif($sell->is_class_auctions() && !$sell->sell_type && !$product_configuration->site->configuration_data['listing_type_allowed']==0)
				{
					$sell->set_sell_type($db, $product_configuration->site->configuration_data['listing_type_allowed']);
				}
				if (!$sell->users_price_plan)
				{
					$sell->set_price_plan($db,$_REQUEST["price_plan"]);
					//if ($_REQUEST["price_plan"])
					//{
					//	if ($debug) echo "entering set_price_plan<br>\n";
					//	$sell->set_price_plan($db,$_REQUEST["price_plan"]);
					//}
					//else
					//{
					//	if ($debug) echo "entering display_price_plan_choice_form<br>\n";
					//	$sell->display_price_plan_choice_form($db, $sell->sell_type);
					//}
				}
				if ($debug)
				{
					echo "after set_sell_type<BR>\n";
					echo $sell->users_price_plan." is users_price_plan<bR>\n";
					echo $_REQUEST["price_plan"]." is request[price_plan]<bR>\n";
					echo "after set_price_plan<BR>\n";
				}
				if (!$sell->terminal_category || $_REQUEST["set_cat"])
				{
					if ($debug) echo $_REQUEST["b"]." is request[b]<br>\n";
					if (($_REQUEST["b"]) && ($_REQUEST["c"] == "terminal") && (is_numeric($_REQUEST["b"])))
					{
						if ($sell->set_terminal_category($db,$_REQUEST["b"]))
						{
							// collect details
							if (!$sell->details_collected || $_REQUEST["set_cat"])
							{
								if (!$sell->display_classified_detail_form($db))
								{
									$sell->site_error($db);
								}
							}
							// add images
							elseif (!$sell->classified_images_collected)
							{
								$sell->display_classified_image_form($db);
							}
							else
							{
								$sell->classified_approval_display($db);
							}
						}
						else
						{
							$sell->site_error($db);
						}
					}
					elseif (($_REQUEST["b"]) && ($_REQUEST["b"] != "accepted") && (is_numeric($_REQUEST["b"])))
					{
						if (!$sell->choose_category($db,$_REQUEST["b"]))
							$sell->site_error($db);
					}
					else
					{
						if (!$sell->choose_category($db))
							$sell->site_error($db);
					}
				}
				elseif (!$sell->classified_details_collected || $_REQUEST["set_details"])
				{
					if ($debug) echo $_REQUEST["b"]." is request[b]<br>\n";
					if (($_REQUEST["b"]) && (is_array($_REQUEST["b"])))
					{
						$sell->get_badword_array($db);
						$sell->get_html_disallowed_array($db);
						$sell->get_form_variables($_REQUEST["b"]);
						$sell->check_extra_questions($db);
						if ($sell->classified_detail_check($db,$sell->terminal_category))
						{
							$sell->save_form_variables($db);
							$sell->update_classified_details_collected($db,1);
							if (!$sell->classified_images_collected || $_REQUEST["set_details"])
							{
								unset($_REQUEST["set_images"]);
								$sell->display_classified_image_form($db);
							}
							else
								$sell->classified_approval_display($db);
						}
						else
						{
							$sell->save_form_variables($db);
							$sell->display_classified_detail_form($db);
						}
					}
					else
					{
						//detail form has not been submitted yet
						$sell->display_classified_detail_form($db);
					}
				}
				elseif (!$sell->classified_images_collected || $_REQUEST["set_images"])
				{
					unset($_REQUEST["set_images"]);

					//$sell->get_form_variables($_REQUEST["b"]);
					//$sell->save_form_variables($db);
					if (($_REQUEST["f"]) && ($_REQUEST["g"]))
					{
						//remove the image
						$sell->remove_image($db,$_REQUEST["f"],$_REQUEST["g"]);
						$sell->display_classified_image_form($db);
					}
					elseif (($_REQUEST["c"]) || ($_REQUEST["d"]))
					{
						$sell->process_images($db,$_REQUEST["c"],$_FILES);
						if ($sell->classified_images_collected)
						{
							$sell->classified_approval_display($db);
						}
						else
							$sell->display_classified_image_form($db);
					}
					elseif ($sell->classified_images_collected)
					{
						$sell->classified_approval_display($db);
					}
					else
						$sell->display_classified_image_form($db);
				}
				elseif (!$sell->classified_approved)
				{
					if ($debug)
					{
						echo "classified_approved not set<BR>\n";
						echo $_REQUEST["b"]." is request[b]<br>\n";
						echo $sell->configuration_data['all_ads_are_free']." is all_ads_are_free<bR>\n";
					}
					if ($_REQUEST["b"])
					{
						if ($_REQUEST["b"] == "ad_accepted")
						{
							if ($debug) echo "about to call insert_classified<bR>\n";
							if ($sell->insert_classified($db))
							{
								if (!$sell->configuration_data['all_ads_are_free'])
								{
									if (!$sell->classified_billing_form($db))
										$sell->site_error($db);
								}
								else
								{
									include_once("classes/user_management_ad_filters.php");
									$user_management = new User_management_ad_filters($db,$language_id,$auth->classified_user_id,$product_configuration);
									$user_management->check_ad_filters($db,$sell->classified_id);
									if (!$sell->sell_success($db))
										$sell->site_error($db);
								}
							}
							else
							{
								if (!$sell->classified_approval_display($db))
									$sell->site_error($db);
							}
						}
						elseif ($_REQUEST["b"] == "edit_details")
						{
							$sell->update_images_collected($db,0);
							$sell->update_classified_details_collected($db,0);
							$sell->update_billing_approved($db,0);
							$sell->update_final_approval($db,0);
							$sell->display_classified_detail_form($db);
						}
						elseif ($_REQUEST["b"] == "edit_image")
						{
							$sell->update_images_collected($db,0);
							$sell->update_billing_approved($db,0);
							$sell->update_final_approval($db,0);
							$sell->display_classified_image_form($db);
						}
						elseif ($_REQUEST["b"] == "edit_category")
						{
							$sell->update_terminal_category($db,0);
							$sell->update_classified_details_collected($db,0);
							$sell->update_images_collected($db,0);
							$sell->update_billing_approved($db,0);
							$sell->update_final_approval($db,0);
							$sell->choose_category($db);
						}
						elseif ($_REQUEST["b"] == "edit_print")
						{
							if ($debug) echo "resetting print web choice<Br>\n";
							$sell->update_print_web_approved($db,0);
							$sell->update_terminal_category($db,0);
							$sell->update_classified_details_collected($db,0);
							$sell->update_images_collected($db,0);
							$sell->update_billing_approved($db,0);
							$sell->update_final_approval($db,0);
							$sell->choose_category($db);
						}
						else
						{
							if (!$sell->classified_approval_display($db))
								$sell->site_error($db);
						}
					}
					else
					{
						if (!$sell->classified_approval_display($db))
							$sell->site_error($db);
					}
				}
				elseif (!$sell->billing_approved)
				{
					if ($debug)
					{
						echo $_REQUEST["b"]." is request[b]<br>\n";
						echo "billing_approved not set<bR>\n";
					}
					if ($_REQUEST["b"])
					{
						$sell->get_form_variables($_REQUEST["c"]);
						$sell->save_form_variables($db);
						if (($_REQUEST["b"] == "billing_accepted") && ($_REQUEST["z"]))
						{
							if ($sell->check_transaction_variables($db))
							{
								//show the final approval
								//try the card
								if (!$sell->final_approval_form($db))
								{
									if (!$sell->classified_billing_form($db))
										$sell->site_error($db);
								}
							}
							else
							{
								if (!$sell->classified_billing_form($db))
									$sell->site_error($db);
							}
						}
						elseif ($_REQUEST["b"] == "edit_details")
						{
							$sell->update_classified_approved($db,0);
							$sell->update_images_collected($db,0);
							$sell->update_classified_details_collected($db,0);
							$sell->update_billing_approved($db,0);
							$sell->update_final_approval($db,0);
							$sell->display_classified_detail_form($db);
						}
						elseif ($_REQUEST["b"] == "edit_print")
						{
							$sell->update_print_web_approved($db,0);
							$sell->update_classified_approved($db,0);
							$sell->update_images_collected($db,0);
							$sell->update_classified_details_collected($db,0);
							$sell->update_terminal_category($db,0);
							$sell->update_billing_approved($db,0);
							$sell->update_final_approval($db,0);
							$sell->choose_category($db);
						}
						elseif ($_REQUEST["b"] == "edit_category")
						{
							$sell->update_classified_approved($db,0);
							$sell->update_images_collected($db,0);
							$sell->update_classified_details_collected($db,0);
							$sell->update_terminal_category($db,0);
							$sell->update_billing_approved($db,0);
							$sell->update_final_approval($db,0);
							$sell->choose_category($db);
						}
						elseif ($_REQUEST["b"] == "edit_image")
						{
							$sell->update_classified_approved($db,0);
							$sell->update_images_collected($db,0);
							$sell->update_billing_approved($db,0);
							$sell->update_final_approval($db,0);
							$sell->display_classified_image_form($db);
						}
						else
						{
							if (!$sell->classified_billing_form($db))
								$sell->site_error($db);
						}
					}
					else
					{
						if (!$sell->classified_billing_form($db))
							$sell->site_error($db);
					}
				}
				elseif (!$sell->final_approval)
				{
					//this is a cash, check or money order transaction
					//or the user is wanting to edit the category, details, images or transaction data
					if ($debug)
					{
						echo "final_approval not set<BR>\n";
						echo $_REQUEST["b"]." is request b<BR>\n";
					}

					if ($_REQUEST["b"])
					{
						if ($_REQUEST["b"] == "final_accepted")
						{
							if ($debug) echo "calling get_form_variables<BR>";
							$sell->get_form_variables($_REQUEST["c"]);
							if ($debug) echo "calling save_form_variables<BR>";
							$sell->save_form_variables($db);
							//do the cash, check or money order hold routine
							//display the cash, check or money order success messages
							if ($debug) echo "calling check_transaction_variables<BR>";
							if ($sell->check_transaction_variables($db))
							{
								//show the final approval
								//try the card
								if ($debug) echo "calling transaction_approved<BR>";
								if ($sell->transaction_approved($db))
								{
									if ($debug) echo "transaction_approved returned true<BR>";
									include_once("classes/user_management_ad_filters.php");
									$user_management = new User_management_ad_filters($db,$language_id,$auth->classified_user_id,$product_configuration);
									$user_management->check_ad_filters($db,$sell->classified_id);
									$sell->sell_success($db);
								}
								else
								{
									if ($debug) echo "transaction_approved returned false<BR>";
									if (!$sell->final_approval_form($db))
										$sell->site_error($db);
								}
							}
							else
							{
								if ($debug) echo "check_transaction_variables returned false<BR>";
								if (!$sell->classified_billing_form($db))
									$sell->site_error($db);
							}
						}
						elseif ($_REQUEST["b"] == "edit_print")
						{
							$sell->update_print_web_approved($db,0);
							$sell->update_classified_approved($db,0);
							$sell->update_images_collected($db,0);
							$sell->update_classified_details_collected($db,0);
							$sell->update_terminal_category($db,0);
							$sell->update_billing_approved($db,0);
							$sell->update_final_approval($db,0);
							$sell->choose_category($db);
						}
						elseif ($_REQUEST["b"] == "edit_category")
						{
							$sell->update_classified_approved($db,0);
							$sell->update_images_collected($db,0);
							$sell->update_classified_details_collected($db,0);
							$sell->update_terminal_category($db,0);
							$sell->update_billing_approved($db,0);
							$sell->update_final_approval($db,0);
							$sell->choose_category($db);
						}
						elseif ($_REQUEST["b"] == "edit_details")
						{
							$sell->update_classified_approved($db,0);
							$sell->update_images_collected($db,0);
							$sell->update_classified_details_collected($db,0);
							$sell->update_billing_approved($db,0);
							$sell->update_final_approval($db,0);
							$sell->display_classified_detail_form($db);
						}
						elseif ($_REQUEST["b"] == "edit_image")
						{
							$sell->update_classified_approved($db,0);
							$sell->update_images_collected($db,0);
							$sell->update_billing_approved($db,0);
							$sell->update_final_approval($db,0);
							$sell->display_classified_image_form($db);
						}
						elseif ($_REQUEST["b"] == "edit_transaction")
						{
							$sell->update_billing_approved($db,0);
							$sell->update_final_approval($db,0);
							$sell->classified_billing_form($db);
						}
						else
						{
							if (!$sell->final_approval_form($db))
							{
								$sell->site_error($db);
							}
						}
					}
					elseif ($_REQUEST["credit_approval"])
					{
						include_once("classes/user_management_ad_filters.php");
						$user_management = new User_management_ad_filters($db,$language_id,$auth->classified_user_id,$product_configuration);
						$user_management->check_ad_filters($db,$sell->classified_id);
						$sell->sell_success($db,$_REQUEST["credit_approval"],$declined);
					}
					else
					{
						if (!$sell->final_approval_form($db))
						{
							$sell->site_error($db);
						}
					}
				}
				else
				{
					if ($_REQUEST["credit_approval"])
					{
						include_once("classes/user_management_ad_filters.php");
						$user_management = new User_management_ad_filters($db,$language_id,$auth->classified_user_id,$product_configuration);
						$user_management->check_ad_filters($db,$sell->classified_id);
						$sell->sell_success($db,$_REQUEST["credit_approval"],$declined);
					}
					else
					{
						$sell->update_final_approval($db,0);
						if (!$sell->final_approval_form($db))
						{
							$sell->site_error($db);
						}
					}
				}
			}
			else
			{
				$sell->remove_sell_session($db,$sell->session_id);
				$sell->setup_sell_error_display($db);
			}
		}
  		else
  		{
  			//must be logged in to place a listing
  			if (!$auth)
  			{
	  			include_once("classes/authenticate_class.php");
				$auth = new Auth($db,$language_id,$product_configuration);
  			}
			header("Location: ".$auth->configuration_data->CLASSIFIEDS_URL."?a=10&c=a*is*1");
			exit;
  			//$auth->login_form($db, "", "", "a*is*1");
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 2:
		//display a classified
		include_once("classes/browse_display_ad.php");
		$browse = new Display_ad($db, $user_id, $language_id, 0, $_REQUEST["page"], $_REQUEST["b"],0, $product_configuration);
		if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
		//$browse->classified_close($db);
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
		{
			if ($browse->classified_exists($db,$_REQUEST["b"]))
			{
				if (!$browse->display_classified($db,$_REQUEST["b"]))
					$browse->browse_error($db);
			}
			else
			{
				$browse->browse_error($db);
			}
		}
		else
		{
			//display the home page
			include_once("classes/browse_ads.php");
			$browse = new Browse_ads($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,0,$product_configuration);
			if (!$browse->main($db))
				$browse->browse_error($db);
		}
		break;

	case 3:
		//send communication
		include_once("classes/user_management_communications.php");
		$communication = new User_management_communications($db,$language_id,$user_id, $product_configuration);
		if ($communication->configuration_data->IP_BAN_CHECK) $communication->check_ip($db);
		if ($debug)
		{
			echo $_REQUEST["b"]." is b when sending a reply<Br>\n";
			echo $_REQUEST["d"]." is d when sending a reply<Br>\n";
		}
		if (($_REQUEST["b"]) && ($_REQUEST["d"]))
		{
			if (!$communication->send_communication($db,$_REQUEST["b"],$_REQUEST["d"]))
				$communication->site_error($db);
			else
			{
				if (!$communication->communication_success($db))
					$communication->site_error($db);
			}
		}
		elseif (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
		{
			//display the home page
			if (!$communication->send_communication_form($db,$_REQUEST["b"],$_REQUEST["c"]))
				$communication->site_error($db);
		}
		else
		{
			$communication->site_error($db);
		}
		break;

	case 4:
		//user management
		//b is the secondary switch within user management
		if ($user_id)
		{
			switch ($_REQUEST["b"])
			{
				case 1:
					//show current ads
					include_once("classes/user_management_current_ads.php");
					$user_management = new User_management_current_ads($db,$language_id,$user_id, $_REQUEST['page'], $product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (!$user_management->list_current_ads($db))
						$user_management->site_error($db);
					break;
				case 2:
					//show past ads
					include_once("classes/user_management_expired_ads.php");
					$user_management = new User_management_expired_ads($db,$language_id,$user_id,$_REQUEST['page'],$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (($_REQUEST["c"]) && (is_numeric($_REQUEST["c"])))
					{
						if (!$user_management->show_expired_ad($db,$_REQUEST["c"]))
							$user_management->site_error($db);
					}
					elseif (!$user_management->list_expired_ads($db))
						$user_management->site_error($db);
					break;
				case 3:
					//show user info
					include_once("classes/user_management_information.php");
					$user_management = new User_management_information($db,$language_id,$user_id,$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (!$user_management->display_user_data($db))
						$user_management->site_error($db);
					break;
				case 4:
					//edit user info
					include_once("classes/user_management_information.php");
					$user_management = new User_management_information($db,$language_id,$user_id,$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if ($_REQUEST["c"])
					{
						//update the current user_info
						if ($user_management->check_info($db,$_REQUEST["c"]))
						{
							$user_management->update_user($db,$_REQUEST["c"],$_REQUEST["d"]);
							$db = &ADONewConnection('mysql');
							if($persistent_connections)
							{
								if (!$db->PConnect($db_host, $db_username, $db_password, $database))
								{
									echo "could not connect to database";
									if ($get_execution_time) get_end_time($starttime);
									exit;
								}
							}
							else
							{
								if (!$db->Connect($db_host, $db_username, $db_password, $database))
								{
									echo "could not connect to database";
									if ($get_execution_time) get_end_time($starttime);
									exit;
								}
							}

							if (!$user_management->display_user_data($db))
								$user_management->site_error($db);
						}
						elseif (!$user_management->edit_user_form($db,$_REQUEST["c"]))
								$user_management->site_error($db);
					}
					else
					{
						//show edit form
						if (!$user_management->edit_user_form($db))
							$user_management->site_error($db);
					}

					break;
				case 5:
					//edit a classified ad
					include_once("classes/user_management_current_ads.php");
					$user_management = new User_management_current_ads($db,$language_id,$user_id,$_REQUEST['page'],$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (($_REQUEST["e"]) && (is_numeric($_REQUEST["e"])))
					{
						switch ($_REQUEST["c"])
						{
							case 1:
								//edit the classified ads details
								if ($_REQUEST["d"])
								{
									$user_management->get_badword_array($db);
									$user_management->get_html_disallowed_array($db);
									$user_management->get_form_variables($_REQUEST["d"]);
									$user_management->check_extra_questions($db);
									$ad_data = $user_management->get_classified_data($db,$_REQUEST["e"]);
									if ($user_management->classified_detail_check($db,$ad_data->CATEGORY))
									{
										if (!$user_management->update_classified_ad_details($db,$_REQUEST["e"]))
											$user_management->site_error($db);
										else
											if (!$user_management->edit_classified_ad_home($db,$_REQUEST["e"]))
												$user_management->site_error($db);
									}
									else
									{
										if (!$user_management->edit_classified_ad_detail($db,$_REQUEST["e"]))
											$user_management->site_error($db);
									}
								}
								else
									if (!$user_management->edit_classified_ad_detail($db,$_REQUEST["e"]))
										$user_management->site_error($db);

								break;
							case 2:
								//edit the classified ads images
								if (!$user_management->edit_classified_ad_images($db,$_REQUEST["e"]))
									$user_management->site_error($db);
								break;
							case 3:
								//edit the classified ads category
								if (!$user_management->edit_classified_ad_category($db,$_REQUEST["category_id"],$_REQUEST["e"],$_REQUEST["f"]))
									$user_management->site_error($db);

								break;
							case 4:
								//delete classified ads images
								if ($_REQUEST["d"])
								{
									//delete image
									if (!$user_management->delete_classified_ad_image($db,$_REQUEST["d"],$_REQUEST["e"],$_REQUEST["f"]))
										$user_management->site_error($db);
									else
										if (!$user_management->edit_classified_ad_images($db,$_REQUEST["e"]))
											$user_management->site_error($db);
								}
								else
									if (!$user_management->edit_classified_ad_images($db,$_REQUEST["e"]))
										$user_management->site_error($db);

								break;

							case 5:
								//add classified ads image
								//add image
								if (!$user_management->process_images($db,$_REQUEST["f"],$_REQUEST["e"],$_FILES))
									$user_management->site_error($db);
								else
									if (!$user_management->edit_classified_ad_images($db,$_REQUEST["e"]))
										$user_management->site_error($db);
								break;
							default:
								if (!$user_management->edit_classified_ad_home($db,$_REQUEST["e"]))
									$user_management->site_error($db);
								break;
						}
					}
					else
					{
						if (!$user_management->list_current_ads($db))
							$user_management->site_error($db);
					}
					break;

				case 6:
					//delete a classified ad
					include_once("classes/user_management_current_ads.php");
					$user_management = new User_management_current_ads($db,$language_id,$user_id,$_REQUEST['page'],$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (($_REQUEST["c"]) && ($_REQUEST["z"]))
					{
						//go ahead and delete
						if (!$user_management->remove_current_ad($db,$_REQUEST["c"]))
							$user_management->site_error($db);
						else
							if (!$user_management->verify_remove_success($db))
								$user_management->site_error($db);
					}
					elseif (is_numeric($_REQUEST["c"]))
					{
						if (!$user_management->verify_remove_current_ad($db,$_REQUEST["c"]))
							$user_management->site_error($db);
					}
					else
						$user_management->site_error($db);
					break;

				case 7:
					//communication configuration
					include_once("classes/user_management_communications.php");
					$user_management = new User_management_communications($db,$language_id,$user_id,$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (($_REQUEST["c"]) && ($_REQUEST["z"]))
					{
						//go ahead and delete
						if (!$user_management->update_communication_configuration($db,$_REQUEST["c"]))
							$user_management->site_error($db);
						else
							if (!$user_management->list_communications($db))
								$user_management->site_error($db);
					}
					else
					{
						if (!$user_management->communications_configuration($db))
							$user_management->site_error($db);
					}
					break;

				case 8:
					//communication management and viewing
					include_once("classes/user_management_communications.php");
					$user_management = new User_management_communications($db,$language_id,$user_id,$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					switch ($_REQUEST["c"])
					{
						case 1:
							//view message
							if (is_numeric($_REQUEST["d"]))
							{
								if (!$user_management->view_this_communication($db,$_REQUEST["d"]))
										$user_management->site_error($db);
							}
							else
								$user_management->site_error($db);
							break;
						case 2:
							//delete message
							if (is_numeric($_REQUEST["d"]))
							{
								if (!$user_management->delete_this_communication($db,$_REQUEST["d"]))
										$user_management->site_error($db);
								elseif (!$user_management->list_communications($db))
										$user_management->site_error($db);
							}
							else
								$user_management->site_error($db);
							break;
						case 3:
							//respond to message


							break;
						case 4:
							//send a message
							if (!$user_management->send_current_communication($db,$d["to"],$_REQUEST["d"]))
								$user_management->site_error($db);
							else
								if (!$user_management->list_communications($db))
									$user_management->site_error($db);

							break;
						default:
							//show communications list
							if (!$user_management->list_communications($db))
								$user_management->site_error($db);
							break;
					}
					break;

				case 9:
					//view, edit and update ad filters
					include_once("classes/user_management_ad_filters.php");
					$user_management = new User_management_ad_filters($db,$language_id,$user_id,$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					switch ($_REQUEST["c"])
					{
						case 1:
							//ad filter form
							if (!$user_management->add_new_filter_form($db))
								$user_management->site_error($db);
							break;
						case 2:
							//delete filter
							if (is_numeric($_REQUEST["d"]))
							{
								if (!$user_management->delete_ad_filter($db,$_REQUEST["d"]))
									$user_management->site_error($db);
								else
									if (!$user_management->display_all_ad_filters($db))
										$user_management->site_error($db);
							}
							else
								if (!$user_management->display_all_ad_filters($db))
									$user_management->site_error($db);
							break;
						case 3:
							//remove all ad filters
							if (!$user_management->clear_ad_filters($db))
								$user_management->site_error($db);
							else
								if (!$user_management->display_all_ad_filters($db))
									$user_management->site_error($db);
							break;
						case 4:
							//insert an ad filter
							if (!$user_management->insert_new_filter($db,$_REQUEST["d"]))
								$user_management->site_error($db);
							else
								if (!$user_management->display_all_ad_filters($db))
									$user_management->site_error($db);
							break;
						default:
							//view all filters
							if (!$user_management->display_all_ad_filters($db))
								$user_management->site_error($db);
					}
					break;

				case 10:
					//view and delete favorite
					include_once("classes/user_management_favorites.php");
					$user_management = new User_management_favorites($db,$language_id,$user_id,$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					$user_management->expire_old_favorites($db);
					switch ($_REQUEST["c"])
					{
						case 1:
							//delete favorite
							if (is_numeric($_REQUEST["d"]))
							{
								if (!$user_management->delete_favorite($db,$_REQUEST["d"]))
									$user_management->site_error($db);
								elseif (!$user_management->display_all_favorites($db))
									$user_management->site_error($db);
							}
							elseif (!$user_management->display_all_favorites($db))
								$user_management->site_error($db);
							break;

						default:
							//view all filters
							if (!$user_management->display_all_favorites($db))
								$user_management->site_error($db);
					}
					break;

				case 11:
					//change sold sign display
					include_once("classes/user_management_current_ads.php");
					$user_management = new User_management_current_ads($db,$language_id,$user_id,$_REQUEST['page'],$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (($_REQUEST["c"]) && (is_numeric($_REQUEST["c"])))
					{
						//change sold sign display status
						$user_management->change_sold_sign_status($db,$_REQUEST["c"]);
						if (!$user_management->list_current_ads($db))
							$user_management->site_error($db);
					}
					else
					{
						//display current ads
						if (!$user_management->list_current_ads($db))
							$user_management->site_error($db);
					}
					break;

				case 12:
					//display sellers sign
					include_once("classes/user_management_current_ads.php");
					$user_management = new User_management_current_ads($db,$language_id,$user_id,$_REQUEST['page'],$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (($_REQUEST["c"]) && ($_REQUEST["d"]))
					{
						//display sellers sign
						$user_management->display_sellers_sign($db,$_REQUEST["c"],$_REQUEST["d"]);
						if (!$user_management->list_current_ads($db))
							$user_management->site_error($db);
					}
					elseif (($_REQUEST["c"]) && (is_numeric($_REQUEST["c"])))
					{
						//form for sellers sign
						if (!$user_management->sellers_sign_form($db,$_REQUEST["c"]))
							$user_management->site_error($db);
					}
					else
					{
						if (!$user_management->signs_and_flyers_list($db))
							$user_management->site_error($db);
					}
					break;

				case 13:
					//display sellers flyer
					include_once("classes/user_management_current_ads.php");
					$user_management = new User_management_current_ads($db,$language_id,$user_id,$_REQUEST['page'],$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (($_REQUEST["c"]) && ($_REQUEST["d"]))
					{
						//display sellers flyer
						$user_management->display_sellers_flyer($db,$_REQUEST["c"]);
						if (!$user_management->list_current_ads($db))
							$user_management->site_error($db);
					}
					elseif (($_REQUEST["c"]) && (is_numeric($_REQUEST["c"])))
					{
						//display flyer form
						if (!$user_management->sellers_flyer_form($db,$_REQUEST["c"]))
							$user_management->site_error($db);
					}
					else
					{
						if (!$user_management->signs_and_flyers_list($db))
							$user_management->site_error($db);
					}
					break;
				case 14:
					//edit users filter
					include_once("classes/user_management_information.php");
					$user_management = new User_management_information($db,$language_id,$user_id,$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (!$user_management->filter_select($db,$user_management_filter_id))
						$user_management->site_error($db);
					elseif (!$user_management->display_user_data($db))
						$user_management->site_error($db);
					break;

				case 15:
					//show paid invoices
					include_once("classes/user_management_invoices.php");
					$user_management = new User_management_invoices($db,$language_id,$user_id,$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (!$user_management->show_paid_invoices($db))
						$user_management->site_error($db);
					break;

				case 16:
					//show unpaid invoices
					include_once("classes/user_management_invoices.php");
					$user_management = new User_management_invoices($db,$language_id,$user_id,$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (!$user_management->show_unpaid_invoices($db))
						$user_management->site_error($db);
					break;

				case 17:
					//show invoice details
					include_once("classes/user_management_invoices.php");
					$user_management = new User_management_invoices($db,$language_id,$user_id,$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (is_numeric($_REQUEST["c"]))
					{
						if (!$user_management->show_invoice($db,$_REQUEST["c"]))
							$user_management->site_error($db);
					}
					else
					{
						if (!$user_management->user_management_home_body($db))
							$user_management->site_error($db);
					}
					break;

				case 18:
					//show balance transactions
					include_once("classes/user_management_balance_transactions.php");
					$user_management = new User_management_balance($db,$language_id,$user_id,$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (!$user_management->show_past_balance_transactions($db,$_REQUEST["c"]))
						$user_management->site_error($db);
					break;

				case 19:
					//show black listed buyers
					include_once("classes/user_management_black_list_buyers.php");
					$blacklist_buyers = new Black_list_buyers($db,$language_id,$user_id,$product_configuration);
					if ($blacklist_buyers->configuration_data->IP_BAN_CHECK) $blacklist_buyers->check_ip($db);
					switch(($_REQUEST["c"])){
						case 1:
							if(($_REQUEST["d"])){
								if (!$blacklist_buyers->list_search_blacklisted_buyers_results($db,$_REQUEST["d"]))
									$blacklist_buyers->site_error($db);
							}
							else
							{
								if (!$blacklist_buyers->list_blacklisted_buyers($db))
									$blacklist_buyers->site_error($db);
							}
							break;
						case 2:
							if(($_REQUEST["d"])){
								$blacklist_buyers->update_blacklisted_users($db,$_REQUEST["d"]);
								if(!$blacklist_buyers->list_blacklisted_buyers($db,$_REQUEST["d"]))
									$blacklist_buyers->site_error($db);
							}
							else
							{
								if(!$blacklist_buyers->list_blacklisted_buyers($db))
									$blacklist_buyers->site_error($db);
							}
							break;
						default:
							if (!$blacklist_buyers->list_blacklisted_buyers($db))
								$blacklist_buyers->site_error($db);

					}//switch
					break;

				case 20:
					//show invited listed buyers
					include_once("classes/user_management_invited_list_buyers.php");
					$invitedlist_buyers = new Invited_list_buyers($db,$language_id,$user_id,$product_configuration);
					if ($invitedlist_buyers->configuration_data->IP_BAN_CHECK) $invitedlist_buyers->check_ip($db);
					switch(($_REQUEST["c"]))
					{
						case 1: //search for invited buyers
							if(($_REQUEST["d"]))
							{
								if (!$invitedlist_buyers->list_search_invited_buyers_results($db,$_REQUEST["d"]))
									$invitedlist_buyers->site_error($db);
							}
							else
							{
								if (!$invitedlist_buyers->list_invited_buyers($db))
									$invitedlist_buyers->site_error($db);
							}
							break;
						case 2: //add/update invited buyers
							if(($_REQUEST["d"]))
							{
								$invitedlist_buyers->update_invited_users($db,$_REQUEST["d"]);
								if(!$invitedlist_buyers->list_invited_buyers($db))
									$invitedlist_buyers->site_error($db);
							}
							else
							{
								if(!$invitedlist_buyers->list_invited_buyers($db))
									$invitedlist_buyers->site_error($db);
							}
							break;
						default:
							if (!$invitedlist_buyers->list_invited_buyers($db))
								$invitedlist_buyers->site_error($db);
					}//switch
					break;

				case 21:
					//view current bids
					//auctions you currently have bids on
					include_once("classes/user_management_list_bids.php");
					$list_bids = new Auction_list_bids($db, $language_id, $user_id,$product_configuration);
					if ($list_bids->configuration_data->IP_BAN_CHECK) $list_bids->check_ip($db);
					if (!$list_bids->list_auctions_with_your_bid($db))
						$list_bids->site_error($db);
					break;

				case 22:
					//view and leave feedback
					include_once("classes/auction_feedback_class.php");
					$feedback = new Auction_feedback($db,$language_id,$user_id, $_REQUEST['page'], $product_configuration);
					if ($feedback->configuration_data->IP_BAN_CHECK) $feedback->check_ip($db);
					switch ($_REQUEST["c"])
					{
						case 1:
							//list open feedback
							if (!$feedback->list_open_feedback($db,$user_id))
								$feedback->site_error($db);
							break;

						case 2:
							//feedback form
							if (($_REQUEST["d"]) && ($_REQUEST["e"]))
							{
								if ($feedback->check_feedback($db,$_REQUEST["d"],$user_id,$_REQUEST["e"]))
								{
									if (!$feedback->save_feedback($db,$_REQUEST["d"],$user_id,$_REQUEST["e"]))
									{
										if (!$feedback->leave_feedback($db,$user_id,$_REQUEST["d"],$_REQUEST["e"]))
											$feedback->site_error($db);
									}
									else
									{
										if (!$feedback->feedback_thank_you($db))
											$feedback->site_error($db);
									}
								}
								else
								{
									if (!$feedback->leave_feedback($db,$user_id,$_REQUEST["d"],$_REQUEST["e"]))
										$feedback->site_error($db);
								}
							}
							elseif ($_REQUEST["d"])
							{
								if (!$feedback->leave_feedback($db,$user_id,$_REQUEST["d"],0,$_REQUEST["f"]))
									$feedback->site_error($db);
							}
							else
							{
								if (!$feedback->feedback_home($db))
									$feedback->site_error($db);
							}
							break;

						case 3:
							//review feedback
							if($_REQUEST["z"])
							{
								// This one is for showing feedback to other users
								if(!$feedback->feedback_about_user($db,$_REQUEST["z"],$_REQUEST["p"]))
									$feedback->site_error($db);
							}
							elseif (!$feedback->feedback_about_user($db,$user_id,$_REQUEST["p"]))
								$feedback->site_error($db);
							break;

						default:
							//feedback home
							if (!$feedback->feedback_home($db))
								$feedback->site_error($db);
					}
					break;

				default:
					//display user management home
					include_once("classes/user_management_home.php");
					$user_management = new User_management_home($db,$language_id,$user_id,$product_configuration);
					if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
					if (!$user_management->user_management_home_body($db))
						$user_management->site_error($db);
			} //end of switch
			//$user_management->user_management_footer($db);
		}
		else
		{
			//no user id
  			if (!$auth)
  			{
	  			include_once("classes/authenticate_class.php");
				$auth = new Auth($db,$language_id,$product_configuration);
  			}
			$auth->login_form($db, "", "", "a*is*4");
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case "5":
		//display a category
		//b will contain the category id
		include_once("classes/browse_ads.php");
		$browse = new Browse_ads($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
		if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
		//$browse->classified_close($db);
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])) && ($_REQUEST["b"] != 9999))
		{
			if (!$browse->browse($db,$_REQUEST["c"]))
			{
				$browse->browse_error($db);
			}
		}
        elseif($_REQUEST["b"] == 'ViewAll')
        {
            if (!$browse->browse_all($db,$_REQUEST["c"]))
            {
                $browse->browse_error($db);
            }
        }
		else
		{
			if (!$browse->main($db))
			{
				$browse->browse_error($db);
			}
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 6:
		//display sellers other ads
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
		{
			include_once("classes/browse_displays_sellers_ads.php");
			$browse = new Browse_display_sellers_ads($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],$product_configuration);
			if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
			if (!$browse->browse($db))
				$browse->browse_error($db);
		}
		else
		{
			include_once("classes/browse_ads.php");
			$browse = new Browse_ads($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
			if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
			//$browse->classified_close($db);
			if (!$browse->main($db))
			{
				$browse->browse_error($db);
			}
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 7:
		//renew/upgrade an ad
		if ($debug)
		{
			echo "TOP OF CASE 7<BR>\n";
			echo $_REQUEST["b"]." is b<BR>\n";
			echo $_REQUEST["c"]." is c<BR>\n";
			echo $_REQUEST["d"]." is d<BR>\n";
			echo $_REQUEST["r"]." is r<bR>\n";
			echo $_REQUEST["z"]." is z<BR>\n";
		}
		include_once("classes/renew_upgrade_sellers_ads.php");
		$renew = new Renew_upgrade_sellers_ads($db,$user_id,$language_id,$_REQUEST["b"],$_COOKIE["classified_session"],$_REQUEST["r"],$product_configuration);
		if ($renew->configuration_data->IP_BAN_CHECK) $renew->check_ip($db);
		if ($user_id)
		{
			if ($debug)
			{
				echo $_REQUEST["b"]." is b<BR>\n";
				echo $_REQUEST["c"]." is c<BR>\n";
				echo $_REQUEST["d"]." is d<BR>\n";
				echo $_REQUEST["r"]." is r<bR>\n";
				echo $_REQUEST["z"]." is z<BR>\n";
			}
			if ($_REQUEST["d"] == "final_accepted")
			{
				$renew->get_renew_upgrade_variables($_REQUEST["c"]);
				$renew->save_renew_upgrade_variables($db);
				if ($renew->check_transaction_variables($db))
				{
					if ($renew->transaction_approved($db))
					{
						//
					}
					else
					{
						//error within the process
					}

				}
				else
				{
					if (!$renew->classified_upgrade_form($db))
					{
						$renew->remove_renew_upgrade_session($db,$_COOKIE["classified_session"]);
						include_once("classes/browse_displays_sellers_ads.php");
						$browse = new Browse_display_sellers_ads($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],$product_configuration);
						if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
						if (!$browse->browse($db))
							$browse->browse_error($db);
					}
				}
			}
			elseif ($_REQUEST["c"])
			{
				//show totals
				$renew->get_renew_upgrade_variables($_REQUEST["c"]);
				$renew->save_renew_upgrade_variables($db);
				if (!$renew->check_transaction_variables($db))
				{
					if (!$renew->classified_upgrade_form($db,$_REQUEST["b"]))
					{
						$renew->remove_renew_upgrade_session($db,$_COOKIE["classified_session"]);
						include_once("classes/browse_displays_sellers_ads.php");
						$browse = new Browse_display_sellers_ads($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],$product_configuration);
						if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
						if (!$browse->browse($db))
							$browse->browse_error($db);
					}
				}
				else
				{
					if (!$renew->final_approval_form($db))
					{
						if (!$renew->classified_upgrade_form($db,$_REQUEST["b"]))
						{
							$renew->remove_renew_upgrade_session($db,$_COOKIE["classified_session"]);
							include_once("classes/browse_displays_sellers_ads.php");
							$browse = new Browse_display_sellers_ads($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],$product_configuration);
							if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
							if (!$browse->browse($db))
								$browse->browse_error($db);
						}
					}
				}
			}
			else
			{
				//show the renewal form
				if (!$renew->classified_upgrade_form($db))
				{
					$renew->remove_renew_upgrade_session($db,$_COOKIE["classified_session"]);
					$renew->site_error($db);
				}
			}
		}
		else
		{
			$renew->remove_renew_upgrade_session($db,$_COOKIE["classified_session"]);
			include_once("classes/browse_ads.php");
			$browse = new Browse_ads($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
			if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
			if (!$browse->main($db))
				$browse->browse_error($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 8:
		//display a featured ad pics in this category
		//b will contain the category id
		include_once("classes/browse_featured_pic_ads.php");
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
			$browse = new Browse_featured_pic_ads($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
		else
			$browse = new Browse_featured_pic_ads($db,$user_id,$language_id,0,$_REQUEST["page"],$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
		if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
		if (!$browse->browse($db))
			$browse->browse_error($db);
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 9:
		//display a featured ad text only in this category
		//b will contain the category id
		include_once("classes/browse_featured_text_ads.php");
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
			$browse = new Browse_featured_text_ads($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
		else
			$browse = new Browse_featured_text_ads($db,$user_id,$language_id,0,$_REQUEST["page"],$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
		if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
		if (!$browse->browse($db))
			$browse->browse_error($db);
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 10:
		//login
  		if (!$auth)
  		{
	 		include_once("classes/authenticate_class.php");
			$auth = new Auth($db,$language_id,$product_configuration);
  		}
  		if ($auth->configuration_data->IP_BAN_CHECK) $auth->check_ip($db);
		if (!$user_id)
		{
			if (($_REQUEST["b"]) && (is_array($_REQUEST["b"])))
			{
				$authorized = $auth->login($db,$_REQUEST["b"]['username'],$_REQUEST["b"]['password'],$_COOKIE["classified_session"]);
				if ($authorized)
				{
					// seller redirect
					if ($_REQUEST["seller"])
					{
						$seller	= intval( $_REQUEST[ 'seller'] );

						header("Location: /index.php?a=6&b=$seller");
					}
					elseif ($_REQUEST["c"])
					{
						$c = str_replace("*is*", "=", urldecode($_REQUEST["c"]));
						header("Location: ".$browse->configuration_data['classifieds_url']."?".$c);
					}
					else
					{
						if ($auth->configuration_data['post_login_page'] == 0)
						{
							include_once("classes/user_management_home.php");
							$user_management = new User_management_home($db,$language_id,$authorized,$product_configuration);
							if (!$user_management->user_management_home_body($db))
								$user_management->site_error($db);
						}
						elseif ($auth->configuration_data['post_login_page'] == 1)
						{
							include_once("classes/browse_ads.php");
							$browse = new Browse_ads($db,$authorized,$language_id,0,$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
							if (!$browse->main($db))
							{
								if ($debug) echo "in no main browse<Br>\n";
								$browse->site_error($db);
							}
						}
					}
				}
				else
				{
					$auth->login_form($db,$_REQUEST["b"]['username'], $_REQUEST["b"]['password'], urldecode($_REQUEST["c"]));
				}
			}
			else
			{
				$auth->login_form($db,0,0,urldecode($_REQUEST["c"]));
			}
		}
		else
		{
			$auth->already_logged_in($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 11:
		//display the newest ads only in this category
		//b will contain the category id
		include_once("classes/browse_newest_ads.php");
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
			$browse = new Browse_newest_ads($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],$_REQUEST["c"],$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
		else
			$browse = new Browse_newest_ads($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["c"],$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
		if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
		if (($_REQUEST["d"]) && (is_numeric($_REQUEST["d"])))
		{
			if (!$browse->browse($db,$_REQUEST["d"]))
				$browse->browse_error($db);
		}
		elseif (!$browse->browse($db))
				$browse->browse_error($db);
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 12:
		//notify a friend
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
		{
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				include_once("classes/browse_notify_friend.php");
				$browse = new Notify_friend($db,$user_id,$language_id,0,0,0,0,$product_configuration);
				if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
				if ($browse->verify_notify_friend($db,$_REQUEST["b"],$_REQUEST["c"]))
				{
					if ($browse->notify_friend_($db,$_REQUEST["b"],$_REQUEST["c"]))
						$browse->notify_success($db,$_REQUEST["b"]);
					else
						$browse->site_error($db);
				}
				elseif (!$browse->notify_friend_form($db,$_REQUEST["b"]))
					$browse->site_error($db);
			}
			elseif ($_REQUEST["b"])
			{
				include_once("classes/browse_notify_friend.php");
				$browse = new Notify_friend($db,$user_id,$language_id,0,0,0,0,$product_configuration);
				if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
				$browse->notify_friend_form($db,$_REQUEST["b"]);
			}
			else
			{
				include_once("classes/browse_ads.php");
				$browse = new Browse_ads($db,$user_id,$language_id,0,$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
				if (!$browse->main($db))
				{
					$browse->browse_error($db);
				}
			}
		}
		else
		{
			include_once("classes/browse_ads.php");
			$browse = new Browse_ads($db,$user_id,$language_id,0,$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
			if (!$browse->main($db))
			{
				$browse->browse_error($db);
			}
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 13:
		//send a message to seller
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
		{
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				include_once("classes/browse_notify_seller.php");
				$browse = new Notify_seller($db,$user_id,$language_id,0,0,0,0,$product_configuration);
				if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
				if ($browse->notify_seller_($db,$_REQUEST["b"],$_REQUEST["c"]))
					$browse->notify_seller_success($db,$_REQUEST["b"]);
				elseif (!$browse->send_a_message_to_seller_form($db,$_REQUEST["b"]))
					$browse->site_error($db);
			}
			elseif ($_REQUEST["b"])
			{
				include_once("classes/browse_notify_seller.php");
				$browse = new Notify_seller($db,$user_id,$language_id,0,0,0,0,$product_configuration);
				if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
				$browse->send_a_message_to_seller_form($db,$_REQUEST["b"]);
			}
			else
			{
				include_once("classes/browse_ads.php");
				$browse = new Browse_ads($db,$user_id,$language_id,0,$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
				if (!$browse->main($db))
				{
					$browse->browse_error($db);
				}
			}
		}
		else
		{
			include_once("classes/browse_ads.php");
			$browse = new Browse_ads($db,$user_id,$language_id,0,$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
			if (!$browse->main($db))
			{
				$browse->browse_error($db);
			}
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

    case 1313:
        //send a message to seller
        if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
        {
            if (($_REQUEST["b"]) && ($_REQUEST["c"]))
            {
                include_once("classes/browse_notify_seller.php");
                $browse = new Notify_seller($db,$user_id,$language_id,0,0,0,0,$product_configuration);
                if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
                if ($browse->notify_seller_Make_Offer($db,$_REQUEST["b"],$_REQUEST["c"]))
                    $browse->notify_seller_success($db,$_REQUEST["b"]);
                elseif (!$browse->send_a_message_to_seller_form_Make_Offer($db,$_REQUEST["b"]))
                    $browse->site_error($db);
            }
            elseif ($_REQUEST["b"])
            {
                include_once("classes/browse_notify_seller.php");
                $browse = new Notify_seller($db,$user_id,$language_id,0,0,0,0,$product_configuration);
                if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
                $browse->send_a_message_to_seller_form_Make_Offer($db,$_REQUEST["b"]);
            }
            else
            {
                include_once("classes/browse_ads.php");
                $browse = new Browse_ads($db,$user_id,$language_id,0,$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
                if (!$browse->main($db))
                {
                    $browse->browse_error($db);
                }
            }
        }
        else
        {
            include_once("classes/browse_ads.php");
            $browse = new Browse_ads($db,$user_id,$language_id,0,$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
            if (!$browse->main($db))
            {
                $browse->browse_error($db);
            }
        }
        $db->Close();
        if ($get_execution_time) get_end_time($starttime);
        exit;
        break;

	case 14:
		//display a classified in print friendly format
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
		{
			include_once("classes/browse_display_ad_print_friendly.php");
			$browse = new Display_ad_print_friendly($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],0,$product_configuration);
			if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
			if ($browse->classified_exists($db,$_REQUEST["b"]))
			{
				if (!$browse->display_classified($db,$_REQUEST["b"]))
					$browse->browse_error($db);
			}
			else
			{
				$browse->browse_error($db);
			}
		}
		else
		{
			//display the home page
			include_once("classes/browse_ads.php");
			$browse = new Browse_ads($db,$user_id,$language_id,0,$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
			if (!$browse->main($db))
				$browse->browse_error($db);
		}
		break;

	case 15:
		//display a classified images in full size format
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
		{
			include_once("classes/browse_display_ad_full_images.php");
			$browse = new Display_ad_full_images($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],$affiliate_id,$product_configuration);
			if (!$browse->display_classified_full_images($db,$_REQUEST["b"]))
			{
				$browse->browse_error($db);
			}
		}
		else
		{
			//display the home page
			include_once("classes/browse_ads.php");
			$browse_ads = new Browse_ads($db,$user_id,$language_id,0,$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
			if (!$browse_ads->main($db))
				$browse_ads->browse_error($db);
		}
		break;

	case 17:
		//log this user out
		include_once("classes/browse_ads.php");
		$browse = new Browse_ads($db,$user_id,$language_id,0,0,0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
		if ($user_id)
		{
			//destroy the cookie
			//setcookie("classified_session","",0,"/","$HTTP_HOST");
			$sql_query = "delete from geodesic_sessions where classified_session = \"".$_COOKIE["classified_session"]."\"";
			$delete_session_result = $db->Execute($sql_query);
			if (!$delete_session_result)
			{
				if ($debug) echo $sql_query."<br>\n";
				return false;
			}

			$sql_query = "delete from geodesic_classifieds_sell_session where session = \"".$_COOKIE["classified_session"]."\"";
			$delete_session_result = $db->Execute($sql_query);
			if (!$delete_session_result)
			{
				if ($debug) echo $sql_query."<br>\n";
				return false;
			}

			$sql_query = "delete from geodesic_classifieds_sell_session_questions where session = \"".$_COOKIE["classified_session"]."\"";
			$delete_session_result = $db->Execute($sql_query);
			if (!$delete_session_result)
			{
				if ($debug) echo $sql_query."<br>\n";
				return false;
			}

			$sql_query = "delete from geodesic_classifieds_sell_session_images where session = \"".$_COOKIE["classified_session"]."\"";
			$delete_session_result = $db->Execute($sql_query);
			if (!$delete_session_result)
			{
				if ($debug) echo $sql_query."<br>\n";
				return false;
			}

			//api logout
			if ($browse->configuration_data['use_api'])
			{
				$auth_variables = array();
				$auth_variables["db_host"] = $db_host;
				$auth_variables["db_name"] = $database;
				$auth_variables["username"] = $username;
				$auth_variables["password"] = $password;
				$auth_variables["installation_type"] = 1;
				include_once("classes/api_login.php");
				$api_login = new API_login($auth_variables);
				$api_login->api_user_logout();
			}
			header("Location: ".$browse->configuration_data['classifieds_url']);
			$browse->main($db);
			$db->Close();
			if ($get_execution_time) get_end_time($starttime);
			exit;
		}
		else
		{
			$browse->main($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 18:
		//lost password
  		if (!$auth)
  		{
  			include_once("classes/authenticate_class.php");
			$auth = new Auth($db,$language_id,$product_configuration);
		}
		if ($auth->configuration_data->IP_BAN_CHECK) $auth->check_ip($db);
		if (!$user_id)
		{
			if (($_REQUEST["b"]) && (is_array($_REQUEST["b"])))
			{
				if (!$auth->lostpassword($db,$_REQUEST["b"]))
					$auth->site_error($db);
				else
					//$browse->main($db);
					$auth->lostpassword_form($db,1);
			}
			else
			{
				//show the lost password form
				$auth->lostpassword_form($db);
			}
		}
		else
		{
			//show the edit userdata form
			include_once("classes/browse_ads.php");
			$browse = new Browse_ads($db,$user_id,$language_id,0,0,0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
			if (!$browse->main($db))
				$browse->browse_error($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 19:
		//search
		include_once("classes/search_class.php");
		$search_the_classifieds = new Search_classifieds($db,$language_id,$user_id,$_REQUEST["c"],$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
		if ($search_the_classifieds->configuration_data->IP_BAN_CHECK) $search_the_classifieds->check_ip($db);
		if($_REQUEST["b"] && $_REQUEST["order"])
		{
			if(!$search_the_classifieds->Search($db, $_REQUEST["b"], $_REQUEST["change"], $_REQUEST["order"]))
			{
				if (!$search_the_classifieds->search_form($db,$_REQUEST["b"]))
					$search_the_classifieds->site_error($db);
			}
		}
		elseif($_REQUEST["b"])
		{
			if(!$search_the_classifieds->Search($db, $_REQUEST["b"], $_REQUEST["change"]))
			{
				if (!$search_the_classifieds->search_form($db,$_REQUEST["b"]))
					$search_the_classifieds->site_error($db);
			}
		}
		else
		{
			if (!$search_the_classifieds->search_form($db, $_REQUEST["b"], $_REQUEST["change"]))
				$search_the_classifieds->site_error($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 20:
		//add to favorites
		if ($user_id)
		{
			if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
			{
				include_once("classes/user_management_favorites.php");
				$user_management = new User_management_favorites($db,$language_id,$user_id,$product_configuration);
				if ($user_management->configuration_data->IP_BAN_CHECK) $user_management->check_ip($db);
				if (!$user_management->insert_favorite($db,$_REQUEST["b"]))
					$user_management->site_error($db);
				elseif (!$user_management->display_all_favorites($db))
					$user_management->site_error($db);
			}
			else
			{
				//show the edit userdata form
				include_once("classes/browse_ads.php");
				$browse = new Browse_ads($db,$user_id,$language_id,0,0,0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
				if (!$browse->main($db))
					$browse->browse_error($db);
			}
		}
		else
		{
  			if (!$auth)
  			{
	  			include_once("classes/authenticate_class.php");
				$auth = new Auth($db,$language_id,$product_configuration);
  			}
  			if ($auth->configuration_data->IP_BAN_CHECK) $auth->check_ip($db);
			if (($_REQUEST["b"]) && (is_array($_REQUEST["b"])))
				$auth->login_form($db,0,0,"a=20&b=".$_REQUEST["b"]);
			else
				$auth->login_form($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 21:
		//choose languages
		$site = new Site($db,0,$language_id,$user_id,$product_configuration);
		//show the edit userdata form
		if (!$site->choose_language_form($db))
			$site->site_error($db);
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 22:
		//extra page
		$site = new Site($db,0,$language_id,$user_id,$product_configuration);
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
		{
			if (!$site->extra_page($db,$_REQUEST["b"]))
			{
				include_once("classes/browse_ads.php");
				$browse = new Browse_ads($db,$user_id,$language_id,0,0,0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
				if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
				if (!$browse->main($db))
					$browse->browse_error($db);
			}
		}
		else
		{
			include_once("classes/browse_ads.php");
			$browse = new Browse_ads($db,$user_id,$language_id,0,0,0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
			if (!$browse->main($db))
				$browse->browse_error($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;
	case 23:
		//click through of banner ad
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 24:
		//renew a subscription
		if ($user_id)
		{
			include_once("classes/renew_subscriptions.php");
			$renew = new Renew_subscriptions($db,$user_id,$language_id,$_COOKIE["classified_session"],$product_configuration);
			if ($renew->configuration_data->IP_BAN_CHECK) $renew->check_ip($db);
			if (($_REQUEST["z"]) && ($_REQUEST["d"] == "final_accepted"))
			{
				//$renew->get_renew_subscription_variables($_REQUEST["c"]);
				//$renew->save_renew_subscription_variables($db);
				if ($renew->check_transaction_variables($db))
				{
					if ($renew->transaction_approved($db))
					{
						//
					}
					else
					{
						//error within the process
					}
				}
				else
				{
					if (!$renew->subscription_renewal_form($db))
					{
						$renew->remove_renew_subscription_session($db,$_COOKIE["classified_session"]);
						include_once("classes/browse_displays_sellers_ads.php");
						$browse = new Browse_display_sellers_ads($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],$product_configuration);
						if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
						if (!$browse->browse($db))
							$browse->site_error($db);
					}
				}
			}
			elseif ($_REQUEST["c"])
			{
				//show totals
				$renew->get_renew_subscription_variables($_REQUEST["c"]);
				$renew->save_renew_subscription_variables($db);
				if (!$renew->check_transaction_variables($db))
				{
					if (!$renew->subscription_renewal_form($db,$_REQUEST["b"]))
					{
						$renew->remove_renew_subscription_session($db,$_COOKIE["classified_session"]);
						include_once("classes/browse_displays_sellers_ads.php");
						$browse = new Browse_display_sellers_ads($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],$product_configuration);
						if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
						if (!$browse->browse($db))
							$browse->site_error($db);
					}
				}
				else
				{
					if (!$renew->final_approval_form($db))
					{
						if (!$renew->subscription_renewal_form($db,$_REQUEST["b"]))
						{
							$renew->remove_renew_subscription_session($db,$_COOKIE["classified_session"]);
							include_once("classes/browse_displays_sellers_ads.php");
							$browse = new Browse_display_sellers_ads($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],$product_configuration);
							if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
							if (!$browse->browse($db))
								$browse->site_error($db);
						}
					}
				}
			}
			else
			{
				//show the renewal form
				if (!$renew->subscription_renewal_form($db,$_REQUEST["b"]))
				{
					$renew->remove_renew_subscription_session($db,$_COOKIE["classified_session"]);
					$renew->site_error($db);
				}
			}
		}
		else
		{
  			if (!$auth)
  			{
	  			include_once("classes/authenticate_class.php");
				$auth = new Auth($db,$language_id,$product_configuration);
  			}
			if ($_REQUEST["b"])
				$auth->login_form($db,0,0,"a=20&b=".$_REQUEST["b"]);
			else
				$auth->login_form($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case "25":
		//display sellers within a category
		//b will contain the category id
		include_once("classes/browse_sellers.php");
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
			$browse = new Browse_sellers($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,$product_configuration);
		else
			$browse = new Browse_sellers($db,$user_id,$language_id,0,$_REQUEST["page"],0, $product_configuration);
		if ($browse->configuration_data->IP_BAN_CHECK) $browse->check_ip($db);
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
		{
			if (!$browse->browse($db,$_REQUEST["c"]))
			{
				$browse->browse_error($db);
			}
		}
		else
		{
			if (!$browse->main($db))
			{
				$browse->browse_error($db);
			}
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 26:
		//classified voting
		include_once("classes/browse_vote.php");
		$vote = new Browse_vote($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,0,$product_configuration);
		if ($vote->configuration_data->IP_BAN_CHECK) $vote->check_ip($db);
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])) && ($_REQUEST["c"]) && (is_array($_REQUEST["c"])))
		{
			//collect the vote and go back to classified id
			if (!$vote->collect_vote($db,$_REQUEST["b"],$_REQUEST["c"]))
			{
				$vote->site_error($db);
			}
		}
		elseif (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
		{
			//show the voting form
			if (!$vote->voting_form($db,$_REQUEST["b"]))
			{
				include_once("classes/browse_display_ad.php");
				$browse = new Display_ad($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],0,$product_configuration);
				if ($browse->classified_exists($db,$_REQUEST["b"]))
				{
					if (!$browse->display_classified($db,$_REQUEST["b"]))
						$browse->browse_error($db);
				}
				else
				{
					$browse->browse_error($db);
				}
			}
		}
		else
		{
			include_once("classes/browse_ads.php");
			$browse = new Browse_ads($db,$user_id,$language_id,0,0,0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
			if (!$browse->main($db))
				$browse->browse_error($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 27:
		//classified vote browsing
		include_once("classes/browse_vote.php");
		$vote = new Browse_vote($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,0,$product_configuration);
		if ($vote->configuration_data->IP_BAN_CHECK) $vote->check_ip($db);
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
		{
			//collect the vote and go back to classified id
			if (!$vote->browse_vote_comments($db,$_REQUEST["b"]))
			{
				$vote->site_error($db);
			}
		}
		else
		{
			include_once("classes/browse_ads.php");
			$browse = new Browse_ads($db,$user_id,$language_id,0,0,0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
			if (!$browse->main($db))
				$browse->browse_error($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 28:
		//display extra page
		include_once('classes/extra_pages.php');
		$extra_page = new extra_page($db, $_REQUEST['b'], $language_id, $user_id, $product_configuration);
		if ($extra_page->configuration_data->IP_BAN_CHECK) $extra_page->check_ip($db);
		if ($debug) echo $_REQUEST["b"]." is request b in extra page<BR>\n";
		if ($extra_page->page_id)
		{
			$extra_page->setup_filters($filter_id, $state_filter, $zip_filter, $zip_distance_filter);

			//collect the vote and go back to classified id
			if (!$extra_page->display_extra_page($db))
			{
				$extra_page->browse_error($db);
			}
		}
		else
		{
			include_once("classes/browse_ads.php");
			$browse = new Browse_ads($db,$user_id,$language_id,0,0,0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
			if (!$browse->main($db))
				$browse->browse_error($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case "29":
		//add to client account balance
		include_once("classes/add_to_account_balance.php");
		$add_to_balance = new Account_balance($db,$user_id,$language_id,$_COOKIE["classified_session"],$product_configuration);
		if ($add_to_balance->configuration_data->IP_BAN_CHECK) $add_to_balance->check_ip($db);
		if ($user_id)
		{
			if ($_REQUEST["d"] == "final_accepted")
			{
				$add_to_balance->get_account_variables($_REQUEST["c"]);
				if ($add_to_balance->check_transaction_variables($db))
				{
					if ($add_to_balance->transaction_approved($db))
					{
						//
					}
					else
					{
						//error within the process
					}

				}
				else
				{
					if (!$add_to_balance->account_form($db))
					{
						$add_to_balance->remove_account_session($db,$_COOKIE["classified_session"]);
						include_once("classes/user_management_information.php");
						$user_management = new User_management_information($db,$language_id,$user_id,$product_configuration);
						if (!$user_management->display_user_data($db))
							$user_management->site_error($db);
					}
				}
			}
			elseif ($_REQUEST["c"])
			{
				//show totals
				$add_to_balance->get_account_variables($_REQUEST["c"]);
				$add_to_balance->save_account_variables($db);
				if (!$add_to_balance->check_transaction_variables($db))
				{
					if (!$add_to_balance->account_form($db,$_REQUEST["b"]))
					{
						$add_to_balance->remove_account_session($db,$_COOKIE["classified_session"]);
						include_once("classes/user_management_information.php");
						$user_management = new User_management_information($db,$language_id,$user_id,$product_configuration);
						if (!$user_management->display_user_data($db))
							$user_management->site_error($db);
					}
				}
				else
				{
					if (!$add_to_balance->final_approval_form($db))
					{
						if (!$add_to_balance->account_form($db,$_REQUEST["b"]))
						{
							$add_to_balance->remove_account_session($db,$_COOKIE["classified_session"]);
							include_once("classes/user_management_information.php");
							$user_management = new User_management_information($db,$language_id,$user_id,$product_configuration);
							if (!$user_management->display_user_data($db))
								$user_management->site_error($db);
						}
					}
				}
			}
			else
			{
				//show the renewal form
				if (!$add_to_balance->account_form($db))
				{
					$add_to_balance->remove_account_session($db,$_COOKIE["classified_session"]);
					$add_to_balance->site_error($db);
				}
			}
		}
		else
		{
			$add_to_balance->remove_account_session($db,$_COOKIE["classified_session"]);
  			if (!$auth)
  			{
	  			include_once("classes/authenticate_class.php");
				$auth = new Auth($db,$language_id,$product_configuration);
  			}
			if ($_REQUEST["b"])
				$auth->login_form($db,0,0,"a=29&b=".$_REQUEST["b"]);
			else
				$auth->login_form($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case "30":
		//pay a client invoice
		include_once("classes/pay_invoice.php");
		$pay_invoice = new Pay_invoice($db,$user_id,$language_id,$_COOKIE["classified_session"],$_REQUEST["b"],$product_configuration);
		if ($pay_invoice->configuration_data->IP_BAN_CHECK) $pay_invoice->check_ip($db);
		if ($user_id)
		{
			if ($_REQUEST["d"] == "final_accepted")
			{
				$pay_invoice->get_invoice_variables($_REQUEST["c"]);
				if ($pay_invoice->check_transaction_variables($db))
				{
					if ($pay_invoice->transaction_approved($db))
					{
						//
					}
					else
					{
						//error within the process
					}

				}
				else
				{
					if (!$pay_invoice->invoice_form($db))
					{
						$pay_invoice->remove_invoice_session($db,$_COOKIE["classified_session"]);
						include_once("classes/user_management_information.php");
						$user_management = new User_management_information($db,$language_id,$user_id,$product_configuration);
						if (!$user_management->display_user_data($db))
							$user_management->site_error($db);
					}
				}
			}
			elseif ($_REQUEST["c"])
			{
				//show totals
				$pay_invoice->get_invoice_variables($_REQUEST["c"]);
				$pay_invoice->save_invoice_variables($db);
				if (!$pay_invoice->check_transaction_variables($db))
				{
					if (!$pay_invoice->invoice_form($db,$_REQUEST["b"]))
					{
						$pay_invoice->remove_invoice_session($db,$_COOKIE["classified_session"]);
						include_once("classes/user_management_information.php");
						$user_management = new User_management_information($db,$language_id,$user_id,$product_configuration);
						if (!$user_management->display_user_data($db))
							$user_management->site_error($db);
					}
				}
				else
				{
					if (!$pay_invoice->final_approval_form($db))
					{
						if (!$pay_invoice->invoice_form($db,$_REQUEST["b"]))
						{
							$pay_invoice->remove_invoice_session($db,$_COOKIE["classified_session"]);
							include_once("classes/user_management_information.php");
							$user_management = new User_management_information($db,$language_id,$user_id,$product_configuration);
							if (!$user_management->display_user_data($db))
								$user_management->site_error($db);
						}
					}
				}
			}
			else
			{
				//show the renewal form
				if (!$pay_invoice->invoice_form($db))
				{
					$pay_invoice->remove_invoice_session($db,$_COOKIE["classified_session"]);
					$pay_invoice->site_error($db);
				}
			}
		}
		else
		{
			$pay_invoice->remove_invoice_session($db,$_COOKIE["classified_session"]);
  			if (!$auth)
  			{
	  			include_once("classes/authenticate_class.php");
				$auth = new Auth($db,$language_id,$product_configuration);
  			}
			if ($_REQUEST["b"])
				$auth->login_form($db,0,0,"a=30&b=".$_REQUEST["b"]);
			else
				$auth->login_form($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	  case "31":
	    // This logic if for the PayPal "Buy It Now" module
	    require_once 'classes/class.paypal.php';
	    // first param is for test transaction (0=No, 1=Yes). Second is passing database connection object
	    $ppal = new Paypal(1, $db);
	    switch ($_REQUEST['status'])
	    {
	      // Buyer has successfully made a purchase
	      case "success":
	        $ppal->showSuccess();
	      break;
	      // Buyer has cancelled
	      case "cancel":
	        $ppal->showCancel();
	      break;
	      // PayPal is sending an Instant Payment Notification
	      case "ipn":
	        $ppal->getResponse();
	        $ppal->logTransaction();
	        $ppal->mailNotification();
	      break;
	      // Get the details of the ad and seller and send to PayPal
	      default:
	      $ppal->getAdData($_REQUEST['b']);
	      $ppal->postTransaction();
	    }
	    $db->Close();
	    break;

	case 1029:
		//bid on auction
		if ($debug) echo "TOP OF CASE 1029<br>";
		//include_once("classes/browse_ads.php");
		//$browse = new Browse_ads($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
		//$browse->classified_close($db);
		include_once("classes/auction_bid_class.php");
		$bid = new Auction_bid($db,$language_id,$user_id,$_REQUEST["b"],$product_configuration);
		if ($bid->configuration_data->IP_BAN_CHECK) $bid->check_ip($db);
		if ($user_id)
		{
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				//process the bid
				if (!$bid->process_bid($db,$_REQUEST["c"],$_REQUEST["d"]))
				{
					if ($debug) echo "about to process bid<br>\n";
					$bid->bid_error($db);
				}
				else
				{
					if ($debug) echo "Bid successful<bR>";
					$bid->bid_successful($db);
				}
			}
			elseif (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
			{
				//show the bid form
				if ($debug) echo "Just over here<bR>";
				if (!$bid->bid_setup($db,$_REQUEST["d"]))
				{
					if ($debug) echo " BID error is giv ven here<bR>";
					$bid->bid_error($db);
				}
			}
			else
			{
				//show the error
				//*** DOESN'T EXIST IN ANY PRODUCT ***
				include_once("classes/auction_browse_class.php");
				$browse = new Auction_browse($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"]);
				if (!$browse->main($db))
					$browse->browse_error($db);
			}
		}
		else
		{
  			if (!$auth)
  			{
	  			include_once("classes/authenticate_class.php");
				$auth = new Auth($db,$language_id,$product_configuration);
  			}
			$auth->login_form($db,0,0,0,$_REQUEST["c"]);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 1030:
		//user feedback
		if ($debug)
		{
			echo "TOP OF CASE 1030<br>";
			echo $_REQUEST["d"]." is d<br>\n";
			echo $_REQUEST["b"]." is b<br>\n";
			echo $_REQUEST["p"]." is p<br>\n";
		}
		include_once("classes/auction_feedback_class.php");
		$feedback = new Auction_feedback($db,$language_id,$user_id, $_REQUEST['page'], $product_configuration);
		if ($feedback->configuration_data->IP_BAN_CHECK) $feedback->check_ip($db);
		if ($_REQUEST["d"])
		{
			$feedback->feedback_about_user($db,$_REQUEST["d"],$_REQUEST["b"],$_REQUEST["p"]);
		}
		else
		{
			//back to main browse
			include_once("classes/auction_browse_class.php");
			$browse = new Auction_browse($db,$user_id,$language_id,0,0);
			if (!$browse->main($db))
				$browse->browse_error($db);
		}
		break;
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 1031:
		//displays bid history
		if ($debug) echo "TOP OF CASE 1031<br>";
		include_once("classes/auction_bid_class.php");
		$bid = new Auction_bid($db,$language_id,$user_id,$_REQUEST["b"],$product_configuration);
		if ($bid->configuration_data->IP_BAN_CHECK) $bid->check_ip($db);
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
		{
			if(!$bid->get_bid_history($db,$_REQUEST["b"]))
			{
				$bid->bid_error($db);
			}
		}
		else
			$bid->bid_error($db);
		break;
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 98:
		//end this sell process
		include_once("classes/browse_ads.php");
		$browse = new Browse_ads($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
		if ($user_id)
		{
			require_once("classes/classified_sell_class.php");
			$sell = new Classified_sell($db,$user_id,$language_id,$_COOKIE["classified_session"], $_REQUEST['copy_id'], $product_configuration);
			if (strlen(trim($sell->setup_error)) == 0)
			{
				$sell->end_sell_process($db);
			}
			else
			{
				$browse->main($db);
			}
		}
		else
		{
			$browse->main($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 99:
		//this is the admin
		//trying to delete a classified
		include_once("classes/browse_ads.php");
		$browse = new Browse_ads($db,$user_id,$language_id,$_REQUEST["c"],0,0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
		if (($user_level == 1) && ($user_id == 1))
		{
			if ($_REQUEST["b"])
			{
				if ($browse->admin_delete_classified($db,$_REQUEST["b"]))
				{
					if (!$browse->main($db))
						$browse->site_error($db);
				}
				else
				{
					$browse->site_error($db);
				}
			}
			else
			{
				if (!$browse->main($db))
					$browse->site_error($db);
			}
		}
		else
		{
			if (!$browse->main($db))
				$browse->site_error($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;

	case 'ajax':
		require('ajaxBackend.php');
		initAjax($db);
		break;

	case '950':
		//debug closing routine
		if ($debug_closing)
		{
			include_once("classes/site_class.php");
			include_once("classes/browse_ads.php");
			$browse = new Browse_ads($db,1,1);
			$browse->classified_close($db);
			$db->Close();			
		}
		exit;
		break;

//STOREFRONT CODE
	case 200:
		//renew a subscription
		if ($user_id)
		{
			include_once("classes/renew_subscriptions.php");
			$renew = new Renew_subscriptions($db,$user_id,$language_id,$_COOKIE["classified_session"],$product_configuration);
			if ($renew->configuration_data->IP_BAN_CHECK) $renew->check_ip($db);
			$renew->renewStorefrontSubscription = true;
			if (($_REQUEST["z"]) && ($_REQUEST["d"] == "final_accepted"))
			{
				//$renew->get_renew_subscription_variables($_REQUEST["c"]);
				//$renew->save_renew_subscription_variables($db);
				if ($renew->check_transaction_variables($db))
				{
					if ($renew->transaction_approved($db))
					{
						//
					}
					else
					{
						//error within the process
					}
				}
				else
				{
					if (!$renew->storefront_subscription_renewal_form($db))
					{
						$renew->remove_renew_subscription_session($db,$_COOKIE["classified_session"]);
						include_once("classes/browse_displays_sellers_ads.php");
						$browse = new Browse_display_sellers_ads($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],$product_configuration);
						if (!$browse->browse($db))
							$browse->site_error($db);
					}
				}
			}
			elseif ($_REQUEST["c"])
			{
				//show totals
				$renew->get_renew_subscription_variables($_REQUEST["c"]);
				$renew->save_renew_subscription_variables($db);
				if (!$renew->check_transaction_variables($db))
				{
					if (!$renew->storefront_subscription_renewal_form($db,$_REQUEST["b"]))
					{
						$renew->remove_renew_subscription_session($db,$_COOKIE["classified_session"]);
						include_once("classes/browse_displays_sellers_ads.php");
						$browse = new Browse_display_sellers_ads($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],$product_configuration);
						if (!$browse->browse($db))
							$browse->site_error($db);
					}
				}
				else
				{
					if (!$renew->final_approval_storefront_form($db))
					{
						if (!$renew->storefront_subscription_renewal_form($db,$_REQUEST["b"]))
						{
							$renew->remove_renew_subscription_session($db,$_COOKIE["classified_session"]);
							include_once("classes/browse_displays_sellers_ads.php");
							$browse = new Browse_display_sellers_ads($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],$product_configuration);
							if (!$browse->browse($db))
								$browse->site_error($db);
						}
					}
				}
			}
			else
			{
				//show the renewal form
				if (!$renew->storefront_subscription_renewal_form($db,$_REQUEST["b"]))
				{
					$renew->remove_renew_subscription_session($db,$_COOKIE["classified_session"]);
					$renew->site_error($db);
				}
			}
		}
		else
		{
			include_once("classes/authenticate_class.php");
			$auth = new Auth($db,$language_id,$product_configuration);
			if ($_REQUEST["b"])
				$auth->login_form($db,0,0,"a=20&b=".$_REQUEST["b"]);
			else
				$auth->login_form($db);
		}
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
		break;
//STOREFRONT CODE

	default:
		if ($debug)
		{
			echo "displaying default page<bR>\n";
		}
		include_once("classes/browse_ads.php");
		if (($_REQUEST["b"]) && (is_numeric($_REQUEST["b"])))
			$browse = new Browse_ads($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
		else
			$browse = new Browse_ads($db,$user_id,$language_id,0,$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration);
		$browse->main($db);
		$db->Close();
		if ($get_execution_time) get_end_time($starttime);
		exit;
} //end of switch ($_REQUEST["a"])
?>
