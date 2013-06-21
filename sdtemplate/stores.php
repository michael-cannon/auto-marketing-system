<?php

include("config.php");
include("classes/adodb.inc.php");
include("classes/storefront/store_class.php");

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


if (isset($HTTP_COOKIE_VARS))
	$_COOKIE = $HTTP_COOKIE_VARS;

if ($cookie_debug) echo $_COOKIE["classified_session"]." is the classified_session id<bR>\n";

include_once("classes/authenticate_class.php");
$auth = new Auth($db,$language_id);

$current_time = $auth->shifted_time($db);
$sql_query = "delete from geodesic_sessions WHERE last_time < ".($current_time - 3600);
if ($cookie_debug) echo $sql_query." is the query<br>\n";
$delete_session_result = $db->Execute($sql_query);
if (!$delete_session_result)
{
	if ($cookie_debug) echo $sql_query." <br>\n";
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
			if ($cookie_debug) echo $sql_query."<br>\n";
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
		if ($cookie_debug) echo $sql_query."<br>\n";
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
			if ($cookie_debug) echo $sql_query."<br>\n";
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
					if ($cookie_debug) echo $sql_query."<br>\n";
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
			if ($cookie_debug) echo $sql_query."<br>\n";
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
			$filter_id = $value;
			break;
		}
	}
	$expires = time() + 86400;
	setcookie("filter_id","",0);
	setcookie("filter_id",$filter_id,$expires);
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

include_once('products.php');
if(!$product_configuration)
	$product_configuration = new product_configuration($db);

if(isset($_REQUEST["ajax"])||isset($_REQUEST["update"]))
{
	if($user_id==$_REQUEST["store"])
	{
		include("classes/storefront/user_management_storefront.php");
		$storefrontManagement = new User_management_storefront($db,$language_id,$user_id,$product_configuration,$_REQUEST["store"]);
		
		if(isset($_REQUEST["p"])) { $storefrontManagement->pageId = $_REQUEST["p"]; };
		if(isset($_REQUEST["ajax"])) { $storefrontManagement->ajax=true; }
		
		if(isset($_POST["homeLinkAdd"]))
			$storefrontManagement->editHomeLink($db, $_POST["homeLink"]);
		if(isset($_POST["categoryAdd"]))
			$storefrontManagement->addCategory($db, $_POST["categoryName"]);
		if(isset($_POST["categoryEdit"]))
			$storefrontManagement->editCategory($db, $_POST["categoryId"], $_POST["categoryName"]);
		if(isset($_POST["categoryDelete"]))
			$storefrontManagement->deleteCategory($db, $_POST["categoryId1"], $_POST["categoryId2"]);
		if(isset($_POST["categorySort"]))
			$storefrontManagement->sortCategory($db, $_POST["categoryOrder"]);
		if(isset($_POST["storefrontUpdateLogo"]))
			$storefrontManagement->addHeaderImage($db, $_FILES["storefrontLogo"]);
		if(isset($_POST["storefrontUpdateNote"]))
			$storefrontManagement->addWelcomeNote($db, $_POST["storefrontNote"]);
		if(isset($_POST["templateSwitch"]))
			$storefrontManagement->switchTemplate($db, $_POST["storefrontTemplate"]);
		if(isset($_POST["storefrontToggle"]))
			$storefrontManagement->toggleStorefrontActivity($db);
		if(isset($_POST["pageAdd"]))
			$storefrontManagement->addPage($db, $_POST["pageName"]);
		if(isset($_POST["pageBodyEdit"]))
			$storefrontManagement->editPage($db, $_POST["pageBody"], "page_body");
		if(isset($_POST["pageNameEdit"]))
			$storefrontManagement->editPage($db, $_POST["pageName"], "page_name");
		if(isset($_POST["pageLinkTextEdit"]))
			$storefrontManagement->editPage($db, $_POST["pageLinkText"], "page_link_text");
		if(isset($_POST["pageDelete"]))
			$storefrontManagement->deletePage($db);
	}	
}
	
if($_REQUEST["store"])
{
	include("classes/storefront/display_storefront.php");
	$storefrontDisplay = new Display_Storefront($db,$language_id,$user_id,$product_configuration,$_REQUEST["store"]);
		if(($storefrontDisplay->storefrontUserData["storefront_on_hold"]&&($user_id!=$_REQUEST["store"]))||$storefrontDisplay->getStorefrontSubscription($db)===false)
			header("location: index.php?a=6&b=".$_REQUEST["store"]."");
		$storefrontDisplay->storefrontManagementError = $storefrontManagement->storefrontManagementError;
		
		//display
		$storefrontDisplay->getTemplate($db);
		$storefrontDisplay->renderTemplateModules($db);
		if(isset($_REQUEST["p"]))
		{
			if(!$storefrontDisplay->generatePage($db,$_REQUEST["p"]))
				$storefrontDisplay->generateListings($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration,$_REQUEST["c"]);
			else
				$storefrontDisplay->displayPage = true;
		}else
			$storefrontDisplay->generateListings($db,$user_id,$language_id,$_REQUEST["b"],$_REQUEST["page"],0,$filter_id,$state_filter,$zip_filter,$zip_distance_filter,$product_configuration,$_REQUEST["c"]);
		
		$storefrontDisplay->renderTemplate($db);
		echo $storefrontDisplay->storefrontTemplate;
}
?>