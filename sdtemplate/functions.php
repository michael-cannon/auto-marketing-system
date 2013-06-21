<?php

include_once("config.php");
set_time_limit(60 * 60 * 60 * 4);
ini_set('auto_detect_line_endings', true);

$styleStr = "<style type='text/css'>

table.blue
{
	background-color: #cccccc;
	Padding:0;
	cellpadding: 5;
	border: 0;
	align: center;
	width:90%;
	font-color: #454545;
	font-size: 12px;
	font-family: arial, helvetica, verdana, tahoma;
}
table.blue td
{
	align: center;
	valign: middle;
	color: #454545;
	padding-left: 10px;
	background-image: url(./images/bgTd.gif);
	background-position:middle center;
}
table.blue th
{
	align: center;
	height: 26;
	background-color: #d6e2ef;
	background-image: url(./images/bgTh.gif);
	background-position:middle center;
}
h3
{
	font-color: #0077cc;
	font-size: 16px;
	font-family: arial, helvetica, verdana, tahoma;
}
input
{
	color: #454545;
	margin: 4;
}
button
{
	color: #ffffff;
	background-color: #6792C9;
	font-weight: bold;
	margin: 4;
}
.small_text
{
	font-family: arial, helvetica, verdana, tahoma;
	font-size: 10px;
	color: #777777;
}
</style>";


/*
geobu.php - bulk uploader for Geodesic Class/Auctions
	features:
		uses XML-based processing instead of proprietary Excel parsing software
		image files can be listed in the table rather than requiring manual selection in the form
	environment:
		linux/unix for the unzipping - it can work in windows, but then requires the
			php_zip.dll inside of php.ini - see http://us3.php.net/manual/en/ref.zip.php

	INSTRUCTIONS:
		Place this file in your main geodesic directory.
		Place a link to 'geobu.php' wherever you want your logged in users to have access to the bulk upload
		capabilities.
		Copy the example .xml file to your main directory to allow your customers to download the XML template.

		XML data file format:
		I'll have to add this to the instructions:

		Optional detail field names are configured by the GeoClassified administrator.
			in [Ad Configuration > Fields to Use]

		For example, on a real_estate site
			optional_field_1 = number of stories
			optional_field_2 = number of bedrooms
			optional_field_4 = number of bathrooms
			optional_field_7 = garage size

		Just add a column to the .xml data file with the cell in row 1 named EXACTLY by the optional field being used (lower case).
		For the real-estate example, there needs to be a column with optional_field_2 and optional_field_3 in the first row,
		and the number of bedrooms and bathrooms data can be entered in that column for each home advertisement being uploaded.
		If additional fields are added for other types of ads, the corresponding field names need to be put in the data file.
		If they are not used, you can remove them from the data file just to keep it less confusing, but you don't have to.

	history:
		initial release 1/21/2006
		testing: multiple image uploads
		environment: against Geodesic v205 under php5.1.6, FreeBSD 5.4, Apache 1.3.33, MySQL 4.0.24

		author: Arthur T. Manning - Freelance Software Developer - arthur.manning@gmail.com
		credits: The marked preamble for connection to the Geodesic db and setup taken right from index.php
			this requires minor modification to the geoclassifieds file 'class/classified_sell_class.php' - that file is
			mostly duplicated in classified_sell_class_geobu.php.  Rather than modifying the working geodesic code,
			just add this file to the class directory, even though geobu.php is the only file needing it.
*/

// some global parameters you might want to change to your liking
$unzip_dir = 'user_images/';
$zip_max = '5000000'; // maximum size allowed for image zip file


// default values for some fields (they used to be in the upload data file, but got moved here)
// these will be overwritten if present in the data file
$geobu_default_payment_type = 7;
$geobu_default_currency_type = 1;
$geobu_default_country = "United States";	// some sites may prefer 'USA'
$geobu_example_xml = "geobu-template.xml"; // download for testing
$geobu_example_zip = "geobu-demo-images.zip";	// download for testing

// ********** global variables you most likely will not need to change
$geobu_error = "error message not set";
$geobu_cookie = "geobu_upload_session";

// this was manually configured until I learned how to auto-detect php version
$php_version = 'php'.(substr(PHP_VERSION,0,1));

$unzip = 'tar';		// command line for unzipping image files - might be possible to auto-detect linux or unix
if("Linux" == PHP_OS) $unzip = 'unzip';

$geobu_debug = 0;  // this can be turned on/off by adding in the link to this file

if(isset($_POST['geobu_debug'])) $geobu_debug=$_POST['geobu_debug'];

if(4 == $geobu_debug)
{
phpinfo();
die( "done!");
}

header( "Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");

// manipulate cookies here to avoid 'header already sent warning error message
if (isset($_POST['geobu']))
{
	// implement a little tighter form security to make sure customer doesn't accidentally upload the data by clicking back to the upload page
	// this cookie gets destroyed after the form is processed on the next upload
	$geobu_cookie_id = md5(time());
	setcookie($geobu_cookie, $geobu_cookie_id, time() + ( 60 * 60 *24 ) );
	if ( 1 <= $geobu_debug )  echo __LINE__.": cookie[$geobu_cookie] => $geobu_cookie_id <br/>";

	if ( 4 == $_POST['geobu'] )  // wanting to process xml data file
	{

		// don't do anything if the geobu_session_cookie is not properly set
		if (!isset($_COOKIE[$geobu_cookie]) )   // redirect
		{
			header("Location: ".$_SERVER['PHP_SELF']);
		}
		else // check value of geobu_cookie against hidden form variable from the upload and process page
		{
			if ( $_POST['$geobu_cookie'] != $_COOKIE['$geobu_cookie'])  // redirect
			{
				header("Location: ".$_SERVER['PHP_SELF']);
			}
		}
		if ( 2 <= $geobu_debug )  echo  __LINE__.":  \$_COOKIE: <pre>".print_r($_COOKIE,true)."</pre>";
	}
	
	// setcookie($geobu_cookie, false );  // make sure cookie is removed (won't be seen on next load; doesn't affect current $_COOKIE var)
}

if ( 1 <= $geobu_debug) {
echo __LINE__.": PHP_OS: ".PHP_OS."<br/>";
echo __LINE__." : \$geobu_debug = [$geobu_debug]...<br/>";
echo __LINE__." : php_version = [$php_version]...<br/>";
echo "\$_POST:<pre>".print_r($_POST,true)."</pre>";
echo "\$_COOKIE:<pre>".print_r($_COOKIE,true)."</pre>";
}


if ( 1 <= $geobu_debug )
echo "PHP_VERSION: ".PHP_VERSION."<br/>";


/* **************************************************************************************
huge preamble copied from index.php
I started fixing this in multiple places to clean up error messages I was getting until setting the error level
If not logged in, this section redirects to the main site.  This may happen if the customer goes to the bulk
upload page after the session times out.
****************************************************************************************** */
{
$debug = 0;
$cookie_debug = 0;

$get_execution_time = 1;
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
	echo "<div align=center>".$totaltime."</div>";
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

/**************************************************************************\
Copyright (c) 2002 Geodesic Solutions, LLC
GeoClassifieds Enterprise @version V1.01 August 31, 2002
All rights reserved
http://www.geodesicsolutions.com
This file written by
James Park
IT Project Manager
<webmaster@geodesicsolutions.com>
see license attached to distribution
\**************************************************************************/

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
// header("Set-Cookie: classified_session=".$custom_id."; path=/; domain=".$_SERVER["HTTP_HOST"]."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
// equivalent? by atm
setcookie('classified_session', $custom_id, $expires, '/', $_SERVER["HTTP_HOST"]);
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
	$show = $session_result->FetchNextObject();

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
		if (($show->LAST_TIME < ($current_time + 60)) && ($current_ip == $show->IP))
		{
			$user_id = $show->USER_ID;
			$user_level = $show->LEVEL;
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
			include_once("classes/authenticate_class.php");
			$auth = new Auth($db,$language_id);
			if (($_REQUEST["a"]) && ($_REQUEST["b"]) && (!$_REQUEST["c"]) && (!$_REQUEST["d"]) && (!$_REQUEST["e"]))
				header("Location: ".$auth->configuration_data->CLASSIFIEDS_URL."?a=".$_REQUEST["a"]."&b=".$_REQUEST["b"]);
			else
				header("Location: ".$auth->configuration_data->CLASSIFIEDS_URL."?".$_SERVER["QUERY_STRING"]);


		}
	}
	else
	{
		setcookie("classified_session","",0,"/","$HTTP_HOST");
		include_once("classes/authenticate_class.php");
		$auth = new Auth($db,$language_id);
		if (($_REQUEST["a"]) && ($_REQUEST["b"]) && (!$_REQUEST["c"]) && (!$_REQUEST["d"]) && (!$_REQUEST["e"]))
			header("Location: ".$auth->configuration_data->CLASSIFIEDS_URL."?a=".$_REQUEST["a"]."&b=".$_REQUEST["b"]);
		else
			header("Location: ".$auth->configuration_data->CLASSIFIEDS_URL."?".$_SERVER["QUERY_STRING"]);

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
	include_once("classes/authenticate_class.php");
	$auth = new Auth($db,$language_id);
	if (($_REQUEST["a"]) && ($_REQUEST["b"]) && (!$_REQUEST["c"]) && (!$_REQUEST["d"]) && (!$_REQUEST["e"]))
		header("Location: ".$auth->configuration_data->CLASSIFIEDS_URL."?a=".$_REQUEST["a"]."&b=".$_REQUEST["b"]);
	else
		header("Location: ".$auth->configuration_data->CLASSIFIEDS_URL."?".$_SERVER["QUERY_STRING"]);
}
}

if ($_REQUEST["set_language_cookie"])
{
$language_id = $_REQUEST["set_language_cookie"];
include_once("classes/authenticate_class.php");
$auth = new Auth($db,$language_id);
$expires = time() + 31536000;
setcookie("language_id",$_REQUEST["set_language_cookie"],$expires);
$auth->reset_language($db,$_REQUEST["set_language_cookie"]);
//header("Location: ".$auth->configuration_data->CLASSIFIEDS_URL);
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
	$show_language_id = $default_language_result->FetchNextObject();
	$expires = time() + 31536000;
	setcookie("language_id",$show_language_id->LANGUAGE_ID,$expires);
	include_once("classes/authenticate_class.php");
	$auth = new Auth($db,$show_language_id->LANGUAGE_ID);
	$auth->reset_language($db,$show_language_id->LANGUAGE_ID);
	$language_id = $show_language_id->LANGUAGE_ID;
}
else
{

	$language_id = 1;
}
}


if (is_array($_REQUEST["set_filter_id"]))
{
if ($debug)
{
	echo "SET_FILTER_ID IS SET<br>\n";
	reset($_REQUEST["set_filter_id"]);
	foreach ($_REQUEST["set_filter_id"] as $key =>  $value)
	{
		echo $value." is the set_filter_id value for ".$key."<bR>\n";
	}
}
reset($_REQUEST["set_filter_id"]);
foreach ($_REQUEST["set_filter_id"] as $key => $value)
{
	if ($value == "clear")
	{
		$filter_id = "";
		break;
	}
	elseif (strlen(trim($value)) > 0)
	{
		if ($debug) echo "setting ".$value." as the filter_id for ".$key."<bR>\n";
		$filter_id = $value;
		//break;
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

if ($debug)
{
echo $user_id." is user_id<br>\n";
echo $_REQUEST["a"]." is a<bR>\n";
}
}
/* ******************************************************************************************
					  end of index.php   preamble
****************************************************************************************** */

echo $styleStr;

if ( 0 == $user_id){
echo "<table class=blue cellspacing=1 align=center><tr><td>";
geobu_help();
echo "</td></tr><tr><td align='center'>
	<h3>You don't seem to be logged in!</h3>
	<a href='index.php?a=4'>Login to Geodesic</a><br/>
	Then come back to the bulk upload link.
	</td></tr></table>";
	die();
}

if (1<=$geobu_debug) echo __LINE__." : ".__FILE__." : ".__FUNCTION__."<br/>\n";


// *******************************************************************************************
function geobu_help() {  // echo text of instructions - can be wrapped in a td element by calling code
global $geobu_example_xml, $geobu_example_zip;


// define how our pages look
echo "
	<b>Geodesic Bulk Uploader - Help</b><br/>
	You can upload your whole inventory of items for sale using this tool.  Use the xml
	data file provided as a template to list all your items along with the image to go with it.
	The uploading requires two steps: first upload the images as a single .zip file, then upload
	your data file. <br/>
	<b>Requirements:</b><br/>
	<ul><li>Data file: Edit the XML-based data file using Excel 2003 or OpenOffice
	Calc (save as Microsoft Excel 2003 XML format).  Download <a href='$geobu_example_xml'>(rclick-save) this example template</a> and fill in the information.  <b>DO NOT CHANGE THE FIRST ROW</b>, but you can rename this file.  Use Excel 2003 or OpenOffice 2.0 (free from <a href='http://www.openoffice.org'>OpenOffice.org</a>).<br/>
	Example zip file: <a href='$geobu_example_zip'>click to download</a> for image upload testing.
	</li><li>The only acceptable payment type at this time is 7 (site balance)<br/>
	Set your price plan value.<br/>
	<table class=blue cellspacing=1 align=center><tr><th colspan=2>Price Plans:</th></tr>
		<tr><td>2</td><td>2 day listing </td></tr>
		<tr><td>3</td><td>3 day listing </td></tr>
		<tr><td>4</td><td>4 day listing </td></tr>
		<tr><td>5</td><td>5 day listing </td></tr>
		<tr><td>7</td><td>7 day listing </td></tr>
		<tr><td colspan=2>Contact this site's admin for prices and other plans</td></tr>
		</table>
	</li><li>
	Remember, you must have a positive site balance to cover the cost of your ads. The
	uploader needs to use site balance for automated entry.
	</li><li>

	If you are re-using images, you can skip the image upload step and simply upload a new data file.
";
}
// ****************************** end of function ***********************************************

// *******************************************************************************************
function geobu_help_xml() { // display help regarding XML file format

echo "<table class=blue width='100%' cellspacing=1 align=center>
<tr><th colspan=2>XML data file explanation:</th></tr>
<tr><td colspan=2>
The XML data is arranged in columns that match the names of the fields in the Geodesic database.  Some are not as obvious as others.  These columns can be re-arranged, but it is best not to delete them unless you are sure you don't need a value there.  Removing a column should have the same effect as setting the value to zero or blank.
</td></tr>
<tr><td>
classified_title
</td><td>  The title of your ad
</td></tr><tr><td>
category
</td><td>
When you view any existing ads, you will see it's category listed with
category levels separated by '>'.  You can use this same format to designate
the category in which you want your ad to be placed.  The category you name
here must actually exist on your site in order for your ad to be placed
correctly.  Example: 'Main > Ford > Taurus'
</td></tr><tr><td>
classified_length
</td><td>
This is the length (in days) that you want your ad to last.
</td></tr><tr><td>
price
</td><td>
This is the price of your item
</td></tr><tr><td>
image, image2, image3,...
</td><td>
The EXACT full name of the image file you have uploaded via the zip file upload.  The name is CASE SENSITIVE.  If you don't see the image files listed after the zip file upload, check for the list of image files that should appear at the bottom of the screen after uploading the image zip file.
</td></tr><tr><td>

payment_type
</td><td>
7 - site balance (this is the only type available at this time)
</td></tr><tr><td>
price_plan
</td><td>
This value is determined by your site configuration.  For homesales, 8 is 28 day, 9 is run-until-sale.  Your customer will be charged based on this.  This might possibly be ignored by Geodesic depending on the price-plan group of your various customers.

</td></tr><tr><td>
currency_type
</td><td>
set to 1 - US Dollars,  we can make this default to one if you don't want this column in the data file.

</td></tr><tr><td>
optional_field_X
</td><td>
the meaning of these optional fields are set by the Geodesic admin. For homesales, optional_field_2 = bedrooms, and optional_field_4 = baths.
</td></tr><tr><td colspan=2>

The other fields should be self-explanatory, but some may be unneeded. I just copied what Geodesic seemed to want.  I should probably remove these columns and test to see whether Geodesic has a problem with it.   For example, I don't know what the cc_exp_month, cc_exp_year fields really mean.
</td></tr></table>";

}
//****************************** end function *******************************
//****************************************************************************************** *
function unzip($zip_file, $extract_dir) {  // unzip a file to the extract_dir
// unzip the file to the extract directory - this version using unix function
global $geobu_debug, $unzip;
$pecl = false; 	// use pecl functions?
 if($geobu_debug)
 {
	 echo __LINE__.": here in function ".__FUNCTION__."<br/>";
	//copy($src_dir.$zip_file, $extract_dir.$zip_file);
	//chdir($extract_dir);

	$cmd="pwd";
	$output = shell_exec($cmd);
	if ( $geobu_debug ) echo "<pre>$cmd \n$output\n</pre>";
 }

// command-line-based unzip (worked on 1and1.com site, but may not work on others)
if ( $pecl ) {
	echo __LINE__.": php5 - zip file: $zip_file<br/>";
	$zip = zip_open( $zip_file );
	while ($zipentry = zip_read( $zip )) {
		$flength = zip_entry_filesize($zipentry);
		$contents = zip_entry_read( $zip, $flength );
		$fdest =  $extract_dir."/".zip_entry_name( $zipentry );
		if ( 1 <= $geobu_debug) echo __LINE__." : writing zip [$fdest]...<br/>";
		file_put_contents($fdest,$contents);
	}
	zip_close( $zip );
}
else { // if not using php5, the zip method above seems to break
	if ( 'tar' == $unzip ) {
		$cmd = "tar -zxf '$zip_file' -C $extract_dir --exclude '*.php' --exclude '*.php?'";
	} else {
		$cmd="unzip -o '$zip_file' -d $extract_dir";
	}

	// attempt shell command
	$output = shell_exec($cmd);
	 if ( $geobu_debug ) echo "<pre>$cmd \n$output\n</pre>";
	 // for safety, remove any possible scripts that may have been uploaded - BIG security risk
	 $cmd = "rm $extract_dir/*.php";
	 shell_exec($cmd);
 }
}
// ************** end of function *************************************************************

// ******************************************************************************************
// process an array of imagename,imagetext pairs
// insert an image into the images table corresponding to this classified_id
// user's images reside in folder corresponding to user_id
// $iinfo is an array of image file, description pairs - Geodesic limits
// this to five images for manual entry
function image_process2( $db, $cid, $iinfo ) {
global $geobu_debug, $unzip_dir;
//global $user_id;
//$geobu_uploaddir = $unzip_dir;
//$geobu_uploaddir .= "/".$user_id."/";


$icount = 0;
 if ( 1 <= $geobu_debug )
	echo __LINE__." : ".__FUNCTION__."(".$db.", ".$cid.", ".$iinfo.", ".$image_text.")<br/>";
if ( 1 <= $geobu_debug ) echo __LINE__." : \$iinfo: <pre>".print_r($iinfo,true)."</pre>";

$display_order = 1;
foreach( $iinfo as $key => $val ) {
	if ( 1 <= $geobu_debug ) echo __LINE__." :  image $display_order: <pre>".print_r($val,true)."</pre>";

	$fname = $val['image'];
	$image_text = $val['text'];

	if (0 == strlen($fname)) return false;

	// get seller's id
	$sql = "SELECT seller from geodesic_classifieds WHERE id='$cid'";
	$results = $db->Execute($sql);
	if ($results) {
		if (1 == $results->RecordCount()) {
			$show = $results->FetchNextObject();
			$user_id = $show->SELLER;
		}
		else
			return false;
	}
	if ( 1 <= $geobu_debug )
		echo __LINE__." - Query [$sql] : result: <pre>".print_r($show,true)."</pre>";

	$geobu_uploaddir = $unzip_dir;  // in case $sql query has problems, revert to default
	$sql = "SELECT url_image_directory FROM geodesic_classifieds_ad_configuration";
	$results = $db->Execute($sql);
	if ($results) {
		if (1 == $results->RecordCount()) {
			$show = $results->FetchNextObject();
			$geobu_uploaddir = $show->URL_IMAGE_DIRECTORY;
		}
	}

	// Strip any trailing slashes for consistency
	if ('/' == substr($geobu_uploaddir, -1)) $geobu_uploaddir = substr($geobu_uploaddir, 0, -1);
	//$geobu_uploaddir = basename($geobu_uploaddir);    // strip off unnecessary leading pathname - gives nothing if ending in /
	$geobu_uploaddir .= "/".$user_id."/";
	//$uploadfile = $uploaddir . basename($_FILES['zipfile']['name']);

	// manually insert image information into the images table (circumventing Geodesic code completely)
	// check for file existence
	//BCS-IT 02.05.2006

	$fnameBase				= basename($fname);
	$fnamePath				= $geobu_uploaddir . $fnameBase;
	if(!empty($fname) )
	{          
		// remove previous watermarked image
		if ( file_exists($fnamePath) )
		{
			unlink( $fnamePath );
		}

		$ch				= curl_init($fname);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$rawdata		= curl_exec($ch);
		curl_close($ch);

		$fp				= fopen($fnamePath, 'w');
		fwrite($fp, $rawdata);
		fclose($fp); 
		
		$fname				= basename($fname);
	}
	//BCS-IT 02.05.2006

	$dirname = dirname(__FILE__);
	$fullpath = $dirname."/".$geobu_uploaddir.$fname;
	if ( 1 <= $geobu_debug ) echo __LINE__.": \$fullpath: [".$fullpath."]";

	//echo "<hr color=#ff0000>".$geobu_uploaddir.$fname."<hr color=#ff0000>";
	//BCS-IT 21.06.2006
	//$imgp = explode(".", $fname);
	//$fname = strtoupper($imgp[0]).'.jpg';
	//echo "<hr color=#ff9900>".$geobu_uploaddir.$fname."<hr color=#ff9900>";
	
	$isize = @getimagesize($geobu_uploaddir.$fname); 
	if (file_exists($geobu_uploaddir.$fname) && $isize[1] != 0)
	{
		$wmlogosmall = $dirname.'/'.'images/wmlogosmall.gif';

		$thnPath = $dirname."/".$geobu_uploaddir.$fname;
		$thnInfo = getImageSize($thnPath);
		$thnWidth = $thnInfo[0];
		$thnHeight = $thnInfo[1];
		$thnMimeType = $thnInfo['mime'];

		$thnDir = $dirname.'/'.$geobu_uploaddir.'thumbs';
		if(!is_dir($thnDir))
		{
			mkdir("$thnDir");
		}
		$thnFile = $thnDir.'/'.$fname;

		if(file_exists($thnFile))
		{
			if(!unlink($thnFile))
			{
				echo "Can't delete file $thnFile<br>";
			}
		}

		if($thnMimeType == 'image/gif')
		{
			$thn = imageCreateFromGif($thnPath);
		}
		elseif($thnMimeType == 'image/png')
		{
			$thn = imageCreateFromPng($thnPath);
		}
		elseif($thnMimeType == 'image/jpg' || $thnMimeType == 'image/jpeg')
		{
			$thn = imageCreateFromJpeg($thnPath);
		}

		$height = 95;
		$isize = getimagesize($geobu_uploaddir.$fname);            
		if($isize[1] == 0) {
		  print "Getimagesize failed for " . $geobu_uploaddir.$fname."<br>";
		}
		$width = round($height*($isize[0]/$isize[1]));

		$im = imageCreateTrueColor($width, $height);
		imageCopyResized($im, $thn, 0,0,0,0, $width, $height, $thnWidth, $thnHeight);
		$fl = fopen($thnDir.'/'.$fname, "w");
		fclose($fl);

		$logo = imageCreateFromGif($wmlogosmall);
		$logoInfo = getImageSize($wmlogosmall);
		$logoWidth = $logoInfo[0];
		$logoHeight = $logoInfo[1];

		imageCopyMerge($im, $logo, $width-$logoWidth-5, $height-$logoHeight-5, 0, 0, $logoWidth, $logoHeight, 70);

		if($thnMimeType == 'image/gif')
		{
			imageGif($im, $thnDir.'/'.$fname);
		}
		if($thnMimeType == 'image/png')
		{
			imagePng($im, $thnDir.'/'.$fname);
		}
		if($thnMimeType == 'image/jpeg' || $thnMimeType)
		{
			imageJpeg($im, $thnDir.'/'.$fname);
		}


		// target height = 95;
		$sql = "INSERT into geodesic_classifieds_images_urls
			SET classified_id='".$cid."', image_url='".$geobu_uploaddir.$fname."',
			thumb_url='".$geobu_uploaddir."thumbs/".$fname."',
			thumb_filename='".$fname."',
			full_filename='".$fname."',image_text='".$image_text."',
			file_path='".dirname($_SERVER['SCRIPT_FILENAME'])."/".$geobu_uploaddir."', ".
			"image_width='".$width."', image_height='".$height."',".
			"original_image_width='".$isize['0']."', ".
			"original_image_height='".$isize['1']."', ".
			"display_order='$display_order', date_entered='".time()."'";

		if ( 1 <= $geobu_debug ) echo __LINE__."sql: [".$sql."]<br/>";

		 $db->execute($sql); // try it!
		 $display_order++;
		 $icount++;
		}
	else {
		if ( 1 <= $geobu_debug )
			echo "<h4>".__LINE__." : ".__FUNCTION__
				." Missing image file: [$fname] in dir [$geobu_uploaddir] -- [".$fullpath."]</h4>\n";
	}
}

// this should cause my ad's photo to display properly
$sql = "UPDATE geodesic_classifieds SET image='$icount' WHERE id='$cid'";
$db->Execute($sql);

return true;  // all is well
}
// ************** end of function *************************************************************

// *****************************************************************************************
function category_get( $db, $catstring ) { // get numeric category from string

// this can be very confusing due to the way Geodesic handles category naming and display to users
//  with no description set for the category, the category_id field is displayed to the user
//    when a description IS set, the description is displayed, and MIGHT NOT MATCH the category_id
//    this was the case in a home-sales website, where Log Home Packages described the category_id 'Homes' (#184)
// lesson: test every function with every possible input to avoid future troubleshooting/debugging
global $geobu_debug;

$geoclass_categories = 'geodesic_categories';
$geoclass_catlanguages = 'geodesic_classifieds_categories_languages';
$debug_file=1;
$result = 0;
if ($catstring) {
	// get the category id from geodesic_categories
	$catlist = explode(">",$catstring);
	// trim leading and trailing spaces
	foreach ($catlist as $key=>$val) {
		$catlist[$key] = ltrim($val);
		$catlist[$key] = rtrim($catlist[$key]);
		}

	if ( 1 <= $geobu_debug ) echo __LINE__.": \$catlist <pre>: ".print_r( $catlist,true )."</pre>\n";

	// this is simple if there is only one category name/descriptino for the bottom level, otherwise we have to trace parentage
	// the naming of categories is controlled by the site admin (customer), so this better be bullet-proof!
	$levels = count($catlist);
	$sql = "SELECT category_id, parent_id, in_statement, category_name, description "
		."FROM geodesic_categories WHERE category_name='".$catlist[$levels-1]."'"
		." OR description='".$catlist[$levels-1]."' ";
	if ( 1 <= $geobu_debug ) echo __LINE__.": Query [$sql]<br/>";
	$sqlresult = $db->Execute($sql);
	if($sqlresult) {
		$count = $sqlresult->RecordCount();
		$catresult = $sqlresult->FetchNextObject();
		if ( 1 <= $geobu_debug ) echo __LINE__.": <pre>".print_r($catresult,true)."</pre>\n";

		if ( 1 == $count )
			return $catresult->CATEGORY_ID;
		else {
			if ( 1 <= $geobu_debug ) echo __LINE__."<h3>\$count = $count</h3>";

			// site was set up completely wrong if this is not at least a 2nd level category that happens to have duplicates
			if ( $levels < 2 )
				return 0; // error condition

			do {  // compare parent information until we have a match of parent name/desc AND child's parent_id
				if ( 1 <= $geobu_debug ) echo __LINE__.": <pre>".print_r($catresult,true)."</pre>\n";

				// echo __LINE__.": <h3>I don't know how to trace parentage yet</h3>\n";

				{// multiple categories with same name - match parent name and ID to resolve
					$sql = "SELECT category_id, parent_id, in_statement, category_name, description "
						."FROM geodesic_categories WHERE ( category_name='".$catlist[$levels-2]."'"
						." OR description='".$catlist[$levels-2]
						."') AND category_id='".$catresult->PARENT_ID."'";

					if ( 1 <= $geobu_debug ) echo __LINE__.": Query [$sql]<br/>";

					$sqlresult2 = $db->Execute($sql);
					if ( $sqlresult2 )
						if (1 == $sqlresult2->RecordCount())
							return $catresult->CATEGORY_ID;
					}
				} while( $catresult = $sqlresult->FetchNextObject() );

			}
	}

}
return $result;
} // *************************** end of function ***********************
// *************** end of function ************************************************************


function bulk_sell($site, $db, $user_id, &$bu)
{
	// bulk sell using information in $bu
	global $geobu_debug, $geobu_error;
	global $geobu_default_payment_type, $geobu_default_currency_type, $geobu_default_country;

	if(1 <= $geobu_debug) $site->sell_debug  = true;

	// set default values of some unset items
	$bu['manning']='testing';
	if(!isset($bu['payment_type'])) $bu['payment_type']= $geobu_default_payment_type;
	if(!isset($bu['currency_type'])) $bu['currency_type']=$geobu_default_currency_type;
	if(!isset($bu['country'])) $bu['country']=$geobu_default_country;

	if(1 <= $geobu_debug)
	{
		echo __LINE__.": \$bu:<pre>".print_r($bu,true)."</pre>";
		//list in table format for excel
		echo "<h2>".__LINE__."</h2>\n<table border='2'><tr>";
		foreach($bu as $key => $val) echo "<td>".$key."</td>";
		echo "</tr><tr>";
		foreach($bu as $key => $val) echo "<td>".$val."</td>";
		echo "</tr></table><br/>\n";
	}
	// start a sell session
	{
	include_once("classes/classified_sell_class.php");
	// create a new  unique sell session for each item for multiple approval later
	$sell_session_id = md5(microtime() + $_COOKIE["classified_session"]);
	if(1 <= $geobu_debug) echo "<h3>".__LINE__.": creating sell session...</h3>";

	// modified this function to enable bulk upload flag in classified_sell_class  -- ADD paramter after testing withou (old code test)
	$sell = new Classified_sell($db,$user_id,$language_id,$sell_session_id,$classauctions);
	if(1 <= $geobu_debug) echo "<h3> geobu: ".__LINE__.": DONE</h3>";
	if(2 <= $geobu_debug) $sell->sell_debug = 1;  // debug switch for Geo software - higher level of debugging

	if(isset($bu['sell_type'])) $sell->set_sell_type($db, $bu['sell_type']);

	if(1 <= $geobu_debug) echo __LINE__.": setting price plan! [".$bu['price_plan']."]<br/>";
	if(1 <= $geobu_debug) echo  "<h3>".__LINE__.": DONE</h3>";

	if (isset($bu['price_plan']))
	{
		$sell->set_price_plan($db,$bu['price_plan']);
		if(1 <= $geobu_debug) echo __LINE__.": done setting price plan! [".$bu['price_plan']."]<br/>";
	}

	// determine category_id from the string in the $bu array
	if(!empty($bu['category']))
	{
		if(!$category_id = category_get($db, $bu['category']))
		{
			$cats = explode(">", $bu['category']);
			if(count($cats) > 0)
			{
				for($n = 0; $n < count($cats); $n ++)
				{
					if($n > 0)
					{
						$cats[$n] = str_replace("-", "", $cats[$n]);
						$catstr .= " > ".$cats[$n];
					}
					else
					{
						$catstr = $cats[$n];
					}
				}
				$bu['category'] = $catstr;
			}

			if(!$category_id = category_get($db, $bu['category']))
			{
				if(!category_create($db, $bu['category']))
				{
					// die ("problem finding ID for category [".$bu['category']."]");
					$geobu_error = "E".__LINE__." Unknown category 1 [".$bu['category']."]";
					return(0);  // suspect error in transaction variables
					// die ( "error setting category! [$category_id] - ".$bu['classified_title'] );
				}
				else
				{
					if(!$category_id = category_get($db, $bu['category']))
					{
						// die ("problem finding ID for category [".$bu['category']."]");
						$geobu_error = "E".__LINE__." Unknown category 2 [".$bu['category']."]";
						return(0);  // suspect error in transaction variables
						// die ( "error setting category! [$category_id] - ".$bu['classified_title'] );
					}
				}
			}
		}
	}
	else
	{
		return(0);
	}

	$classified_query = "SELECT id FROM geodesic_classifieds WHERE title='".urlencode($bu['classified_title'])."' AND category=".$category_id." AND optional_field_18='".trim($bu['optional_field_18'])."' ";
	$classified_result = $db->Execute($classified_query);
	if($classified_result)
	{
		$dat = $classified_result->FetchNextObject();
		$sell->classified_id = $dat->ID;
		$classified_id = $dat->ID;
		$del_query = "DELETE FROM geodesic_classifieds_images_urls WHERE classified_id=".$classified_id." LIMIT 20";
		$del_result = $db->Execute($del_query);
	}

	if(!$sell->classified_id)
	{
		$cat_bu = $category_id;
		$parent_bu = 0;
		$cats = Array();
		$j = 0;
		$cats[$j] = $category_id;
		$j ++;

		$cat_query = "SELECT parent_id FROM geodesic_categories WHERE category_id = $cat_bu";
		$cat_result = $db->Execute($cat_query);
		if($cat_result)
		{
			if(1 == $cat_result->RecordCount())
			{
				$cat = $cat_result->FetchNextObject();
				$parent_bu = $cat->PARENT_ID;
				if($parent_bu) $cats[$j] = $parent_bu;
				if($geobu_debug) echo "Parent First: $parent_bu<br>";
				$j ++;
			}
		}

		while($parent_bu != 0)
		{
			$cat_query = "SELECT parent_id FROM geodesic_categories WHERE category_id = $parent_bu";
			$cat_result = $db->Execute($cat_query);
			if($cat_result)
			{
				if(1 == $cat_result->RecordCount())
				{
					$cat = $cat_result->FetchNextObject();
					$parent_bu = $cat->PARENT_ID;
					if($parent_bu) $cats[$j] = $parent_bu;
					if($geobu_debug) echo "Parent $j: $parent_bu<br>";
					$j ++;
				}
			}
		}
		
		//$site->update_category_count($db,$category_id);

		if(count($cats) > 0)
		{
			foreach($cats as $key => $value)
			{
				if($bu['sell_type'] == 1)
				{
					$cat_query = "UPDATE geodesic_categories SET category_count = category_count + 1 WHERE category_id=$value LIMIT 1";
				}
				elseif($bu['sell_type'] == 2)
				{
					$cat_query = "UPDATE geodesic_categories SET auction_category_count = auction_category_count + 1 WHERE category_id=$value LIMIT 1";
				}
				$cat_result = $db->Execute($cat_query);
				if(!$cat_result)
				{
					return false;
				}
				else
				{

				}
			}
		}
	}



	// set category
	if(!$sell->set_terminal_category($db,$category_id))
	{
		$geobu_error = "E".__LINE__." problem setting terminal Category";
		return ( 0 );  // suspect error in transaction variables
		// die ( "error setting category! [$category_id] - ".$bu['classified_title'] );
	}

	$sell->get_form_variables($bu);  // Geo has no error checking here!
	if(!$sell->save_form_variables($db))
		if(1 <= $geobu_debug) echo  __LINE__." : ".basename(__FILE__).": save_form_variables returned false <br/>\n";

	$sell->update_classified_details_collected($db,1);
	$sell->update_images_collected($db,1);

	// these are needed to avoid sql query error in final approval function
	$sell->get_form_variables($required);  // Geo has no error checking here!

	if(!$sell->save_form_variables($db))
		if(1 <= $geobu_debug) echo __LINE__." : save_form_variables returned false <br/>\n";

	$site->classified_variables["payment_type"] = '7';
	$sell->update_classified_approved($db,1);
	$sell->update_billing_approved($db,1);
	$sell->update_final_approval($db,1);

	if(!$sell->insert_classified($db))
	{
		die ( __LINE__."error with insert_classifieds got longitude and
		latitude in geodesic_classfieds table?");
	}

	// if ( $bu->image )  // process non-blank image name
	$icount = 0;
	if(0 != strlen($bu['image']))
		if(0 < strlen($bu['image2']))
		{
			$icount = 2;
			$ikey = "image".$icount;
			$ikeytext = "image".$icount."_text";
			// build a command that creates the array to pass to our image process function (for multiple images)
			// this will break if they change the header columnn names in the data file!!!
			$ieval = "\$images = array (array( 'image'=>'".$bu['image']."', 'text'=>'".$bu['image_text']."' ) ";
			while((0 < strlen($bu[$ikey])) && ($icount<=10))
			{
				if(1 <= $geobu_debug) echo "<h3>".__LINE__." -Multiple Image [$ikey].[".$bu[$ikey]."][".$bu[$ikeytext]."]..</h3>";

				$ieval .= "\n, array( 'image'=>'".$bu[$ikey]."', 'text'=>'".$bu[$ikeytext]."' )";
				$icount++;
				$ikey = "image".$icount;
				$ikeytext = "image".$icount."_text";
			}
			$ieval .= " );";

			if(1 <= $geobu_debug) echo __LINE__.": \$ieval<pre>: $ieval</pre><br/>";
			eval($ieval);  // now run the command we just built!

			//if ( 1 <= $geobu_debug ) echo __LINE__.": \$images: <pre>".print_r($images,true)."</pre>";
		}
		else
		{
			$icount++;
			$images = array (array( 'image'=>$bu['image'], 'text'=>$bu['image_text']));
		}

		// now process the image array
		if(0 < $icount)
		{
			if(!image_process2($db,$sell->classified_id, $images)) echo __LINE__." : ".__FUNCTION__." image processing[2] error [".$bu['image']."]<br/>";
			else {
			   $bu['image'] = $icount; // non-zero if I want photos to show
			   makeDescription($bu);
				$sql = "SELECT image_url FROM geodesic_classifieds_images_urls
						WHERE classified_id='".$sell->classified_id."' ORDER BY image_url";
				$sqlresult = $db->Execute($sql);
				// MLC 20080208 submit video?
				if($sqlresult && SHOW_VIDEO_DEFAULT) {
					$xml = "<create>
<itemid>".$bu['optional_field_16']."</itemid>
<account>{$video_account}</account>
<site>{$video_site}</site>
<token>{$video_token}</token>
<version>20000</version>
<campaign>".date('m-d-y')."</campaign>
<clobber>1</clobber>
<publish>1</publish>
<attach>1</attach>
<browsable>0</browsable>
<title>".$bu['classified_title']."</title>
<description>".$bu['description']."</description>
<category>6001</category>
<group>
	<pic>
		<url>http://".$_SERVER["HTTP_HOST"]."/user_images/ace-no-image-logo.gif</url>
		<duration>200</duration>
	</pic>
	<sound>
		<type>pad</type>
		<data>200</data>
	</sound>
	<picthreshold>100</picthreshold>
</group>
<group>\n";
$first_pic = "";
while ($res = $sqlresult->FetchNextObject()) {        
	if(!$first_pic) $first_pic = $res->IMAGE_URL;
	$xml .= "<pic>
		<url>http://".$_SERVER["HTTP_HOST"]."/".$res->IMAGE_URL."</url>
	</pic>\n";
}		
	$xml .= "<picthreshold>75</picthreshold>
	<sound>
		<voice>5</voice>
		<type>tts</type>
		<data>".$bu['description']."</data>
	</sound>
</group>
<thumbnail>http://".$_SERVER["HTTP_HOST"]."/".$first_pic."</thumbnail>
</create>
";
	
	$tmpfname = tempnam("/tmp", "xml");
	$tmp_file = fopen($tmpfname, "w");
	fwrite($tmp_file, $xml);
	fclose($tmp_file);
	chmod($tmpfname, 0755);
	$out = shell_exec("curl --data-binary @".$tmpfname." http://www.auctionmercial.com/powercreate.asp");
	unlink($tmpfname);
					}
			}
		}

		if(!$sell->check_transaction_variables($db))
		{
			$geobu_error = "E".__LINE__." Transaction failed - check site balance";
			return(0);  // suspect error in transaction variables
			// die (": FATAL: problem with transaction variables");
		}

		if(1 <= $geobu_debug) echo "<h3>". __LINE__.": payment type: ".$site->classified_variables["payment_type"]."</h3><br/>";
		{
			//show the final approval
			//try the card
			if ($sell->transaction_approved($db))
			{
				include_once("classes/user_management_ad_filters.php");
				$user_management = new User_management_ad_filters($db,$language_id,$auth->classified_user_id);
				$user_management->check_ad_filters($db,$sell->classified_id);
				//$sell->sell_success($db);
				if(1 <= $geobu_debug) echo "<h3>".__LINE__." calling remove_sell_session...</h3><br/>";
				$sell->remove_sell_session($db, $sell_session_id    );
			}
			else
			{
				if(1 <= $geobu_debug) echo "E".__LINE__.": error with transaction approved, but going on...";
				$geobu_error = "E".__LINE__." - needs transaction approval";
			}
		}
		//die ("<h3>done! line: ".__LINE__."</h3><br/>");
	}

	$bu['subtotal']=$sell->cost_of_ad;
	$result = $sell->classified_id;
	return $result;
}
// ********************* end of function ************************

// ********************* Start of function ************************
function category_create($db, $catstring)
{
	global $geobu_debug;
	$product_type = 4;
	require_once('./products.php');
	//insert the new subcategory
	require_once("./admin/admin_site_class.php");
	require_once("./admin/admin_categories_class.php");
	//include("./admin/admin_site_class.php");
	//include("./admin/admin_authentication_class.php");
	$product_configuration = new product_configuration($db, $product_type, 1);
	$admin_category = new Admin_categories($db, $product_configuration);

	$geoclass_categories = 'geodesic_categories';
	$geoclass_catlanguages = 'geodesic_classifieds_categories_languages';
	$debug_file=1;
	$result = 0;
	if($catstring)
	{
		// get the category id from geodesic_categories
		$catlist = explode(">",$catstring);
		// trim leading and trailing spaces
		foreach($catlist as $key=>$val)
		{
			$catlist[$key] = ltrim($val);
			$catlist[$key] = rtrim($catlist[$key]);
		}

		if(1 <= $geobu_debug) echo __LINE__.": \$catlist <pre>: ".print_r( $catlist,true )."</pre>\n";

		// this is simple if there is only one category name/descriptino for the bottom level, otherwise we have to trace parentage
		// the naming of categories is controlled by the site admin (customer), so this better be bullet-proof!
		$levels = count($catlist);
		$sql = "SELECT category_id, parent_id, in_statement, category_name, description "
		."FROM geodesic_categories WHERE category_name='".$catlist[$levels-2]."'"
		." OR description='".$catlist[$levels-2]."' ";

		$sqlresult = $db->Execute($sql);
		if($sqlresult)
		{
			$count = $sqlresult->RecordCount();
			$catresult = $sqlresult->FetchNextObject();
			if(1 <= $geobu_debug) echo __LINE__.": <pre>".print_r($catresult,true)."</pre>\n";

			if(1 == $count)
			{
				$parent = $catresult->CATEGORY_ID;
				$display_order = $db->Insert_ID("geodesic_categories") + 1;

				$b[display_order] = $display_order;
				$b[category_image] = '';
				$b[listing_types_allowed] = 0;
				$b[1][category_name] = $catlist[$levels-1];
				$b[1][description] = '';

				if(!$category_id = $admin_category->insert_category($db,$b,$parent))
				{
					 return $result;
				}
				echo "<hr color=#ff9900><strong>Category was created successfully: ".$category_id."</font></hr color=#ff9900 size=1>";
				return 1;
			}
			elseif($count == 0)
			{
				$parent = 0;
				$display_order = $db->Insert_ID("geodesic_categories") + 1;

				$b[display_order] = $display_order;
				$b[category_image] = '';
				$b[listing_types_allowed] = 0;
				$b[1][category_name] = $catlist[$levels-2];
				$b[1][description] = '';

				if(!$category_id = $admin_category->insert_category($db,$b,0))
				{
					 return $result;
				}
				echo "<hr color=#ff9900><strong>Category was created successfully: ".$category_id."</font></hr color=#ff9900 size=1>";

				if($category_id)
				{
					$parent = $category_id;
					$category_id ++;

					$b[display_order] = $category_id;
					$b[category_image] = '';
					$b[listing_types_allowed] = 0;
					$b[1][category_name] = $catlist[$levels-1];
					$b[1][description] = '';

					if(!$admin_category->insert_category($db,$b,$parent))
					{
						return $result;
					}
					echo "<hr color=#ff9900><strong>Category was created successfully: ".$category_id."</font></hr color=#ff9900 size=1>";

					return 1;
				}
				else return $result;
			}
			else return $result;
		}
		return $result;
	}
	return $result;
}
// ********************* end of function ************************

// ********************* end of function ************************
function makeDescription(&$bu)
{
	//Auto create descriptions
	$desc = 'This is a';

	$desc					.= ( stristr( $bu['optional_field_1'], 'used' ) )
								? " used "
								: " new ";

	if(!empty($bu['optional_field_13'])) $desc .= strtolower($bu['optional_field_13'])." ";
	$desc .= $bu['classified_title'];
	$desc .= ($bu['optionsl_field_11']) ? " with ".$bu['classified_field_11']." interior color. " : ". ";

	if(!empty($bu['optional_field_4']) || !empty($bu['optional_field_7']) || !empty($bu['optional_field_9']) || !empty($bu['optional_field_6']))
	{
		$desc .= ($bu['optional_field_4']) ? "This ".$bu['optional_field_4']." model" : "This model ";
		if(!empty($bu['optional_field_7']) || !empty($bu['optional_field_9']) || !empty($bu['optional_field_6']))
		{
			$desc .= ($bu['optional_field_7']) ? " features a ".$bu['optional_field_7']." engine" : " features a engine";
			$desc .= ($bu['optional_field_9']) ? " with ".$bu['optional_field_9'] : "";
			if($bu['optional_field_9'])
			{
				$desc .= ($bu['optional_field_6']) ? " and ".strtolower($bu['optional_field_6'])." transmission" : "";
			}
			else
			{
				$desc .= ($bu['optional_field_6']) ? " with ".strtolower($bu['optional_field_6'])." transmission" : "";
			}
		}
		$desc .= ".";
	}
	$desc .= ($bu['optional_field_5']) ? " The odometer shows ".$bu['optional_field_5']." miles." : "";
	$desc .= ($bu['optional_field_16']) ? " Vehicle identification number is ".$bu['optional_field_16']."." : "";
	$desc .= ($bu['options']) ? " Included options are ".$bu['options']."." : "";

	$bu['description'] = $desc;
}
// ********************* Start of function ************************
?>