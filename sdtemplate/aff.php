<? //aff.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/
 
include("config.php");
include("classes/adodb.inc.php");
include("classes/site_class.php");
  
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

$debug = 0;

function go_to_classifieds($db,$redirect_from=0)
{
	$sql_query = "select classifieds_url,affiliate_url from geodesic_classifieds_configuration";
	$get_url_result = $db->Execute($sql_query);
	if (!$get_url_result)
	{
		//echo $sql_query."<br>\n";
		return false;
	}
	$show_url = $get_url_result->FetchRow();
	if ($redirect_from)
		header("Location: ".$show_url["classifieds_url"]."?".$_SERVER["QUERY_STRING"]."&redirect_from=".$redirect_from."&aff_redirect_id=".$_REQUEST["aff"]);
	else 
		header("Location: ".$show_url["classifieds_url"]."?".$_SERVER["QUERY_STRING"]);
	exit;
} //end of function go_to_classifieds

$language_id = $_COOKIE["language_id"];

if ($_REQUEST['set_language_cookie'])
{
	include("classes/authenticate_class.php");
	$auth = new Auth($db, $language_id);
	$expires = $auth->shifted_time($db) + 31536000;
	setcookie("language_id", $_REQUEST['set_language_cookie'], $expires);
	$auth->reset_language($db, $_REQUEST['set_language_cookie']);
	header("Location: ".$auth->configuration_data["affiliate_url"]);
}

if (!$_COOKIE["classified_session"])
{
	if($auth)
		$current_time = $auth->shifted_time($db);
	else
	{
		$site = new Site($db);
		$current_time = $site->shifted_time($db);
	}

	$sql_query = "delete from geodesic_sessions WHERE last_time < ".($current_time - 3600);
	if ($debug) echo $sql_query." is the query in no cookie<br>\n";
	$delete_session_result = $db->Execute($sql_query);
	if (!$delete_session_result)
	{
		if ($debug) echo $sql_query." in no cookie <br>\n";
		return false;
	}

	//set session in db
	do {
		$custom_id = md5(uniqid(rand(),1));
		$custom_id = substr( $custom_id, 0,32);
		$sql_query = "SELECT classified_session FROM geodesic_sessions WHERE classified_session = \"".$custom_id."\"";
		if ($debug) echo $sql_query." is the query in no cookie<br>\n";
		$custom_id_result = $db->Execute($sql_query);
		if (!$custom_id_result)
		{
			//echo $sql_query."<br>\n";
			return false;
		}
	} while ($custom_id_result->RecordCount() > 0);

	//$ip = getenv("REMOTE_ADDR");
	if ($_REQUEST["aff"])
	{
		//check to see if an affiliate
		$sql_query = "select * from geodesic_user_groups_price_plans where id = ".$_REQUEST["aff"];
		if ($debug) echo $sql_query." is the query in no cookie<br>\n";
		$aff_group_result = $db->Execute($sql_query);
		if (!$aff_group_result)
		{
			if ($debug) echo $sql_query." in no cookie<br>\n";
			return false;
		}
		elseif ($aff_group_result->RecordCount() == 1)
		{
			$show_group = $aff_group_result->FetchRow();
			$sql_query = "select * from geodesic_groups where group_id = ".$show_group["group_id"];
			if ($debug) echo $sql_query." is the query in no cookie<br>\n";
			$group_result = $db->Execute($sql_query);
			if (!$group_result)
			{
				if ($debug) echo $sql_query." in no cookie<br>\n";
				return false;
			}
			elseif ($group_result->RecordCount() == 1)
			{
				$show_affiliate = $group_result->FetchRow();
				if ($show_affiliate["affiliate"])
				{
					//this group is allowed to be an affiliate
					$ip = 0;
					$sql_query = "insert into geodesic_sessions
						(classified_session,user_id,last_time,ip,level,affiliate_id,affiliate_group_id)
						values
						(\"".$custom_id."\",0,".$current_time.",\"".$ip."\",0,\"".$_REQUEST["aff"]."\",\"".$show_group["group_id"]."\")";
					if ($debug) echo $sql_query." is the query in no cookie<br>\n";
					$insert_session_result = $db->Execute($sql_query);
					if (!$insert_session_result)
					{
						if ($debug) echo $sql_query." in no cookie<br>\n";
						return false;
					}
					$expires = time() + 31536000;
					$user_id = 0;
					$user_level = 0;
					$affiliate_id = $_REQUEST["aff"];
					$affiliate_group_id = $show_group["group_id"];
					$classified_session = $custom_id;
					header("Set-Cookie: classified_session=".$custom_id."; path=/; domain=".$_SERVER["HTTP_HOST"]."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
				}
				else
				{
					//this group is not allowed affiliate status
					//redirect to classifieds_url
					go_to_classifieds($db,1);
				}
			}
			else
			{
				//redirect to classifieds_url
				go_to_classifieds($db,2);
			}
		}
		else
		{
			//redirect to classifieds_url
			go_to_classifieds($db,3);
		}
	}
	else
	{
		//redirect to classifieds_url
		go_to_classifieds($db,4);
	}
}
else
{
	if($auth)
		$current_time = $auth->shifted_time($db);
	else
	{
		$site = new Site($db);
		$current_time = $site->shifted_time($db);
	}

	$sql_query = "delete from geodesic_sessions WHERE last_time < ".($current_time - 3600);
	$delete_session_result = $db->Execute($sql_query);
	if ($debug) echo $sql_query." is the query<br>\n";
	if (!$delete_session_result)
	{
		if ($debug) echo $sql_query."<br>\n";
		return false;
	}
	//get session information
	$sql_query = "SELECT * FROM geodesic_sessions WHERE classified_session = \"".$_COOKIE["classified_session"]."\"";
	$session_result = $db->Execute($sql_query);
	if ($debug) echo $sql_query." is the query<br>\n";
	if (!$session_result)
	{
		if ($debug) echo $sql_query."<br>\n";
		return false;
	}
	elseif ($session_result->RecordCount() == 1)
	{
		//$current_ip = getenv("REMOTE_ADDR");
		$current_ip = 0;
		$show = $session_result->FetchRow();
		if (($show["affiliate_id"]) && (!$_REQUEST["aff"]))
		{
			$sql_query = "update geodesic_sessions set last_time = ".$current_time." where classified_session = \"".$_COOKIE["classified_session"]."\"";
			$update_session_result = $db->Execute($sql_query);
			if ($debug) echo $sql_query." is the query<br>\n";
			if (!$update_session_result)
			{
				if ($debug) echo $sql_query."<br>\n";
				return false;
			}
			elseif ($session_result->RecordCount() == 1)
			{
				if (($show["last_time"] < ($current_time + 60)) && ($current_ip == $show["ip"]))
				{
					$user_id = $show["user_id"];
					$user_level = $show["level"];
					$affiliate_id = $show["affiliate_id"];
					$affiliate_group_id = $show["affiliate_group_id"];
				}
				else
				{
					//change session
					setcookie("classified_session","",0,"/",$_SERVER["HTTP_HOST"]);
					$sql_query = "delete from geodesic_sessions where classified_session = \"".$_COOKIE["classified_session"]."\"";
					$delete_session_result = $db->Execute($sql_query);
					if ($debug) echo $sql_query." is the query<br>\n";
					if (!$delete_session_result)
					{
						if ($debug) echo $sql_query."<br>\n";
						return false;
					}
					include("classes/authenticate_class.php");
					$auth = new Auth($db,$language_id);
					header("Location: ".$auth->configuration_data["classifieds_url"]."?".$_SERVER["QUERY_STRING"]);

				}
			}
			else
			{
				setcookie("classified_session","",0,"/",$_SERVER["HTTP_HOST"]);
				include("classes/authenticate_class.php");
				$auth = new Auth($db,$language_id);
				header("Location: ".$auth->configuration_data["classifieds_url"]."?".$_SERVER["QUERY_STRING"]);

			}
		}
		else
		{
			if ($_REQUEST["aff"])
			{
				$sql_query = "select * from geodesic_user_groups_price_plans where id = ".$_REQUEST["aff"];
				if ($debug) echo $sql_query." is the query<br>\n";
				$aff_group_result = $db->Execute($sql_query);
				if (!$aff_group_result)
				{
					if ($debug) echo $sql_query."<br>\n";
					return false;
				}
				elseif ($aff_group_result->RecordCount() == 1)
				{
					$show_group = $aff_group_result->FetchRow();
					$sql_query = "select * from geodesic_groups where group_id = ".$show_group["group_id"];
					if ($debug) echo $sql_query." is the query<br>\n";
					$group_result = $db->Execute($sql_query);
					if (!$group_result)
					{
						if ($debug) echo $sql_query."<br>\n";
						return false;
					}
					elseif ($group_result->RecordCount() == 1)
					{
						$show_affiliate = $group_result->FetchRow();
						if ($show_affiliate["affiliate"])
						{
							$sql_query = "update geodesic_sessions set
								last_time = ".$current_time." ,
								affiliate_id = ".$_REQUEST["aff"].",
								affiliate_group_id = ".$show_group["group_id"]."
								where classified_session = \"".$_COOKIE["classified_session"]."\"";
							$update_session_result = $db->Execute($sql_query);
							if ($debug) echo $sql_query." is the query<br>\n";
							if (!$update_session_result)
							{
								if ($debug) echo $sql_query."<br>\n";
								return false;
							}
							$affiliate_id = $_REQUEST["aff"];
							$affiliate_group_id = $show_group["group_id"];
						}
						else
						{
							go_to_classifieds($db,5);
						}
					}
					else
					{
						go_to_classifieds($db,6);
					}
				}
				else
				{
					go_to_classifieds($db,7);
				}
			}
			else
			{
				go_to_classifieds($db,8);
			}
		}
	}
	else
	{
		//$ip = getenv("REMOTE_ADDR");
		if ($_REQUEST["aff"])
		{
			//check to see if an affiliate
			$sql_query = "select * from geodesic_user_groups_price_plans where id = ".$_REQUEST["aff"];
			if ($debug) echo $sql_query." is the query<br>\n";
			$aff_group_result = $db->Execute($sql_query);
			if (!$aff_group_result)
			{
				if ($debug) echo $sql_query."<br>\n";
				return false;
			}
			elseif ($aff_group_result->RecordCount() == 1)
			{
				$show_group = $aff_group_result->FetchRow();
				$sql_query = "select * from geodesic_groups where group_id = ".$show_group["group_id"];
				if ($debug) echo $sql_query." is the query<br>\n";
				$group_result = $db->Execute($sql_query);
				if (!$group_result)
				{
					//echo $sql_query."<br>\n";
					return false;
				}
				elseif ($group_result->RecordCount() == 1)
				{
					$show_affiliate = $group_result->FetchRow();
					if ($show_affiliate["affiliate"])
					{
						//this group is allowed to be an affiliate
						$ip = 0;
						$sql_query = "insert into geodesic_sessions
							(classified_session,user_id,last_time,ip,level,affiliate_id,affiliate_group_id)
							values
							(\"".$_COOKIE["classified_session"]."\",0,".$current_time.",\"".$ip."\",0,\"".$_REQUEST["aff"]."\",\"".$show_group["group_id"]."\")";
						if ($debug) echo $sql_query." is the query<br>\n";
						$insert_session_result = $db->Execute($sql_query);
						if (!$insert_session_result)
						{
							//echo $sql_query."<br>\n";
							return false;
						}
						$expires = time() + 31536000;
						$user_id = 0;
						$user_level = 0;
						$affiliate_id = $_REQUEST["aff"];
						$affiliate_group_id = $show_group["group_id"];
						$classified_session = $custom_id;
						header("Set-Cookie: classified_session=".$custom_id."; path=/; domain=".$_SERVER["HTTP_HOST"]."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
					}
					else
					{
						//this group is not allowed affiliate status
						//redirect to classifieds_url
						go_to_classifieds($db,9);
					}
				}
				else
				{
					//redirect to classifieds_url
					go_to_classifieds($db,10);
				}
			}
			else
			{
				//redirect to classifieds_url
				go_to_classifieds($db,11);
			}
		}
		else
		{
			//redirect to classifieds_url
			go_to_classifieds($db,12);
		}
	}
}

//echo $affiliate_group_id." is group id in aff<Br>\n";

include_once('products.php');
if(!$product_configuration)
	$product_configuration = new product_configuration($db);

switch ($_REQUEST["a"]) {

	case 2:
		//display a classified
		include("classes/browse_affiliate_display_ads.php");
		$browse = new Display_ad($db,$affiliate_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"], $affiliate_group_id,$product_configuration);
		//$browse->classified_close($db);
		if ($affiliate_id)
		{
			if ($_REQUEST["b"])
			{
				if ($browse->classified_exists($db,$_REQUEST["b"]))
				{
					if (!$browse->display_classified($db,$_REQUEST["b"],$affiliate_id))
						$browse->browse_error();
				}
				else
				{
					$browse->browse_error();
				}
			}
			else
			{
				//display the home page
				if (!$browse->browse($db))
					$browse->browse_error();
			}
		}
		else
		{
			$sql_query = "select classifieds_url from geodesic_classifieds_configuration";
			$get_url_result = $db->Execute($sql_query);
			if ($debug) echo $sql_query." in case 2<BR>\n";
			if (!$get_url_result)
			{
				if ($debug) echo $sql_query." in case 2<BR>\n";
				return false;
			}
			$show_url = $get_url_result->FetchRow();
			header("Location: ".$show_url["classifieds_url"]."?".$_SERVER["QUERY_STRING"]);
		}
		break;

	case 3:
		//send communication
		include("classes/user_management_communications.php");
		$communication = new User_management_communications($db,$language_id,$affiliate_id, $product_configuration);
		if (($_REQUEST["b"]) && ($_REQUEST["d"]))
		{
			if (!$communication->send_communication($db,$_REQUEST["b"],$_REQUEST["d"]))
				$communication->site_error();
			else
				if (!$communication->communication_success($db))
					$communication->site_error();
		}
		elseif ($_REQUEST["b"])
		{
			//display the home page
			if (!$communication->send_communication_form($db,$_REQUEST["b"],$_REQUEST["c"],$affiliate_id))
				$communication->site_error();
		}
		else
		{
			$communication->site_error();
		}
		break;

	case 5:
		//display a category
		//b will contain the category id
		include("classes/browse_affiliate_ads.php");
		$browse = new Browse_ads($db,$affiliate_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,$affiliate_group_id,$product_configuration);
		//$browse->classified_close($db);
		if ($affiliate_id)
		{
			if ($_REQUEST["b"])
			{
				if (!$browse->browse($db,$_REQUEST["b"],$_REQUEST["c"]))
					$browse->browse_error();
			}
			else
			{
				if (!$browse->browse($db,0,$_REQUEST["c"]))
					$browse->browse_error();
			}
		}
		else
		{
			$sql_query = "select classifieds_url from geodesic_classifieds_configuration";
			$get_url_result = $db->Execute($sql_query);
			if ($debug) echo $sql_query." in case 5<BR>\n";
			if (!$get_url_result)
			{
				//echo $sql_query."<br>\n";
				return false;
			}
			$show_url = $get_url_result->FetchRow();
			header("Location: ".$show_url["classifieds_url"]."?".$_SERVER["QUERY_STRING"]);
		}
		exit;
		break;

	case 12:
		//notify a friend
		include("classes/browse_notify_friend.php");
		$browse = new Notify_friend($db,0,$language_id,0,0,0,$affiliate_id,$product_configuration);
		if (($_REQUEST["b"]) && ($_REQUEST["c"]))
		{
			if ($browse->verify_notify_friend($db,$_REQUEST["b"],$_REQUEST["c"]))
			{
				if ($browse->notify_friend_($db,$_REQUEST["b"],$_REQUEST["c"]))
					$browse->notify_success($db,$_REQUEST["b"]);
				else
					$browse->site_error();
			}
			elseif (!$browse->notify_friend_form($db,$_REQUEST["b"]))
				$browse->site_error();
		}
		elseif ($_REQUEST["b"])
		{
			$browse->notify_friend_form($db,$_REQUEST["b"]);
		}
		else
		{

		}
		exit;
		break;

	case 13:
		//send a message to seller
		include("classes/browse_notify_seller.php");
		$browse = new Notify_seller($db,0,$language_id,0,0,0,$affiliate_id,$product_configuration);
		if (($_REQUEST["b"]) && ($_REQUEST["c"]))
		{
			if ($browse->notify_seller_($db,$_REQUEST["b"],$_REQUEST["c"]))
				$browse->notify_seller_success($db, $_REQUEST["b"]);
			elseif (!$browse->send_a_message_to_seller_form($db,$_REQUEST["b"],$affiliate_id))
				$browse->site_error();
		}
		elseif ($_REQUEST["b"])
		{
			$browse->send_a_message_to_seller_form($db,$_REQUEST["b"],$affiliate_id);
		}
		else
		{

		}
		exit;
		break;

	case 14:
		//display a classified in print friendly format
		if ($_REQUEST["b"])
		{
			include("classes/browse_display_ad_print_friendly.php");
			$browse = new Display_ad_print_friendly($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],$affiliate_id,$product_configuration);
			if ($browse->classified_exists($db,$_REQUEST["b"]))
			{
				if (!$browse->display_classified($db,$_REQUEST["b"]))
					$browse->site_error($db);
			}
			else
			{
				$browse->site_error($db);
			}
		}
		else
		{
			//display the home page
			include("classes/browse_ads.php");
			$browse = new Browse_ads($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,$filter_id,0,0,0,$product_configuration);
			if (!$browse->browse($db,$_REQUEST["b"],$_REQUEST["c"]))
				$browse->site_error($db);
		}
		break;

	case 15:
		//display a classified images in full size format
		if ($_REQUEST["b"])
		{
			include("classes/browse_display_ad_full_images.php");
			$browse = new Display_ad_full_images($db,$user_id,$language_id,0,$_REQUEST["page"],$_REQUEST["b"],$affiliate_id,$product_configuration);
			if (!$browse->display_classified_full_images($db,$_REQUEST["b"]))
				$browse->site_error($db);
		}
		else
		{
			//display the home page
			include("classes/browse_ads.php");
			$browse = new Browse_ads($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,$filter_id,0,0,0,$product_configuration);
			if (!$browse->browse($db,$_REQUEST["b"],$_REQUEST["c"]))
				$browse->site_error($db);
		}
		break;

	case 19:
		//search
		include("classes/search_class.php");
		$search_the_classifieds = new Search_classifieds($db,$language_id,$affiliate_id,$_REQUEST["c"],0,0,0,0,$product_configuration);
		if ($_REQUEST["b"])
		{
			$search_the_classifieds->search($db,$_REQUEST["b"],$affiliate_id);
			if (!$search_the_classifieds->search_form($db,$_REQUEST["c"],$affiliate_id))
				$search_the_classifieds->site_error();
		}
		else
		{
			//show the edit userdata form
			if (!$search_the_classifieds->search_form($db,$_REQUEST["c"],$affiliate_id))
				$search_the_classifieds->site_error();
		}
		exit;
		break;

	default:
		include("classes/browse_affiliate_ads.php");
		//echo "calling from default<BR>\n";
		$browse = new Browse_ads($db,$affiliate_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,$affiliate_group_id,$product_configuration);
		$browse->browse($db,$_REQUEST["b"],$_REQUEST["c"]);
		exit;


} //end of switch ($_REQUEST["a"])

?>
