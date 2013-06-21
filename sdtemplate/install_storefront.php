<?
echo "<html><head>";
echo "<title>Geodesic Storefront Install Routine</title>";
echo "<style type=text/css>";
echo "<!-- .green {  font-family: Arial, Helvetica, sans-serif; font-size: 10pt; font-weight: normal; color: green}";
echo ".nongreen {  font-family: Arial, Helvetica, sans-serif; font-size: 10pt} -->";
echo "</style></head>";
echo "<body bgcolor=ffffff>";

include("config.php");
include("classes/adodb.inc.php");
error_reporting  (E_ERROR | E_WARNING | E_PARSE);
$db = &ADONewConnection('mysql');

echo "<span class = nongreen><br>Installing your new Software...<br>Please wait for a complete message<br></span><br>";

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

echo "<span class = nongreen>Database connection - </span><span class = green>success!</span><br>\n";

function splitSqlFile(&$ret, $sql,$db)
{

    $sql          = trim($sql);
    $sql_len      = strlen($sql);
    $char         = '';
    $string_start = '';
    $in_string    = FALSE;
    $time0        = time();
	//echo $sql_len." is the sql_query length<Br>\n";
    for ($i = 0; $i < $sql_len; ++$i) {
        $char = $sql[$i];

        // We are in a string, check for not escaped end of strings except for
        // backquotes that can't be escaped
        if ($in_string) {
            for (;;) {
                $i         = strpos($sql, $string_start, $i);
                // No end of string found -> add the current substring to the
                // returned array
                if (!$i) {
                    $ret[] = $sql;
                    return TRUE;
                }
                // Backquotes or no backslashes before quotes: it's indeed the
                // end of the string -> exit the loop
                else if ($string_start == '`' || $sql[$i-1] != '\\') {
                    $string_start      = '';
                    $in_string         = FALSE;
                    break;
                }
                // one or more Backslashes before the presumed end of string...
                else {
                    // ... first checks for escaped backslashes
                    $j                     = 2;
                    $escaped_backslash     = FALSE;
                    while ($i-$j > 0 && $sql[$i-$j] == '\\') {
                        $escaped_backslash = !$escaped_backslash;
                        $j++;
                    }
                    // ... if escaped backslashes: it's really the end of the
                    // string -> exit the loop
                    if ($escaped_backslash) {
                        $string_start  = '';
                        $in_string     = FALSE;
                        break;
                    }
                    // ... else loop
                    else {
                        $i++;
                    }
                } // end if...elseif...else
            } // end for
        } // end if (in string)

        // We are not in a string, first check for delimiter...
        else if ($char == ';') {
       		//echo "found delimiter<Br>\n";
            // if delimiter found, add the parsed part to the returned array
            $ret[]      = substr($sql, 0, $i);
            $current      = substr($sql, 0, $i);
            //echo $current."<br>\n";
            $result = $db->Execute($current);
            //if ($result)
            //	echo $current."<Br>\n";
            $sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
            $sql_len    = strlen($sql);
            if ($sql_len) {
                $i      = -1;
            } else {
                // The submited statement(s) end(s) here
                return TRUE;
            }
        } // end else if (is delimiter)

        // loic1: send a fake header each 30 sec. to bypass browser timeout
        $time1     = time();
        if ($time1 >= $time0 + 30) {
            $time0 = $time1;
            //header('X-pmaPing: Pong');
            echo " ";
            flush();
            ob_flush();

        } // end if
    } // end for

    // add any rest to the returned array
    if (!empty($sql) && ereg('[^[:space:]]+', $sql)) {
        $ret[] = $sql;
    }

    return TRUE;
} // end of the 'splitSqlFile()' function

$sql_query = "
ALTER TABLE `geodesic_userdata` ADD `storefront_header` TEXT NOT NULL ,
ADD `storefront_template_id` INT NOT NULL ,
ADD `storefront_welcome_message` TEXT NOT NULL ,
ADD `storefront_on_hold` INT NOT NULL default '1';

ALTER TABLE `geodesic_templates` ADD `storefront_template` TINYINT NOT NULL ,
ADD `storefront_template_default` TINYINT NOT NULL ;

ALTER TABLE `geodesic_categories` ADD `display_storefront_link` INT NOT NULL ;

ALTER TABLE `geodesic_groups` ADD `storefront` INT DEFAULT '0' NOT NULL ;

CREATE TABLE `geodesic_storefront_pages` (
`page_id` INT NOT NULL AUTO_INCREMENT ,
`user_id` INT NOT NULL ,
`page_link_text` TEXT NOT NULL ,
`page_name` TEXT NOT NULL ,
`page_body` TEXT NOT NULL ,
PRIMARY KEY ( `page_id` )
) TYPE = MYISAM ;

CREATE TABLE `geodesic_storefront_categories` (
`category_id` INT NOT NULL AUTO_INCREMENT ,
`user_id` INT NOT NULL ,
`category_name` TEXT NOT NULL ,
`display_order` INT NOT NULL ,
PRIMARY KEY ( `category_id` )
) TYPE = MYISAM ;

CREATE TABLE `geodesic_storefront_users` (
`store_id` INT NOT NULL ,
`user_id` INT NOT NULL
) TYPE = MYISAM ;

CREATE TABLE `geodesic_storefront_subscriptions` (
`subscription_id` INT NOT NULL AUTO_INCREMENT ,
`expiration` INT NOT NULL ,
`user_id` INT NOT NULL ,
PRIMARY KEY ( `subscription_id` )
) TYPE = MYISAM ;

CREATE TABLE `geodesic_storefront_subscriptions_choices` (
  `period_id` int(14) NOT NULL auto_increment,
  `display_value` tinytext NOT NULL,
  `value` int(11) NOT NULL default '0',
  `amount` double(5,2) NOT NULL default '0.00',
  PRIMARY KEY  (`period_id`),
  KEY `id` (`period_id`)
) TYPE=MyISAM ;

CREATE TABLE `geodesic_storefront_group_subscriptions_choices` (
`group_id` INT NOT NULL ,
`choice_id` INT NOT NULL
);

CREATE TABLE `geodesic_storefront_display` (
  `display_business_type` int(11) NOT NULL default '0',
  `use_site_default` int(11) NOT NULL default '0',
  `display_photo_icon` int(11) NOT NULL default '0',
  `display_price` int(11) NOT NULL default '0',
  `display_browsing_zip_field` int(11) NOT NULL default '0',
  `display_browsing_city_field` int(11) NOT NULL default '0',
  `display_browsing_state_field` int(11) NOT NULL default '0',
  `display_browsing_country_field` int(11) NOT NULL default '0',
  `display_entry_date` int(11) NOT NULL default '0',
  `display_optional_field_1` int(11) NOT NULL default '0',
  `display_optional_field_2` int(11) NOT NULL default '0',
  `display_optional_field_3` int(11) NOT NULL default '0',
  `display_optional_field_4` int(11) NOT NULL default '0',
  `display_optional_field_5` int(11) NOT NULL default '0',
  `display_optional_field_6` int(11) NOT NULL default '0',
  `display_optional_field_7` int(11) NOT NULL default '0',
  `display_optional_field_8` int(11) NOT NULL default '0',
  `display_optional_field_9` int(11) NOT NULL default '0',
  `display_optional_field_10` int(11) NOT NULL default '0',
  `display_optional_field_11` int(11) NOT NULL default '0',
  `display_optional_field_12` int(11) NOT NULL default '0',
  `display_optional_field_13` int(11) NOT NULL default '0',
  `display_optional_field_14` int(11) NOT NULL default '0',
  `display_optional_field_15` int(11) NOT NULL default '0',
  `display_optional_field_16` int(11) NOT NULL default '0',
  `display_optional_field_17` int(11) NOT NULL default '0',
  `display_optional_field_18` int(11) NOT NULL default '0',
  `display_optional_field_19` int(11) NOT NULL default '0',
  `display_optional_field_20` int(11) NOT NULL default '0',
  `display_ad_description` int(11) NOT NULL default '0',
  `display_ad_description_where` int(11) NOT NULL default '0',
  `display_all_of_description` int(11) NOT NULL default '0',
  `display_ad_title` int(11) NOT NULL default '1',
  `display_number_bids` int(11) NOT NULL default '0',
  `display_time_left` int(11) NOT NULL default '0'
) TYPE=MyISAM ;

ALTER TABLE `geodesic_classifieds` ADD `storefront_category` INT DEFAULT '0' NOT NULL ;

ALTER TABLE `geodesic_classifieds_sell_session` ADD `storefront` TINYINT NOT NULL ;

ALTER TABLE `geodesic_classifieds_user_subscriptions_holds` ADD `storefront` TINYINT NOT NULL ;

ALTER TABLE `geodesic_paypal_transactions` ADD `storefront` TINYINT NOT NULL ;

ALTER TABLE `geodesic_classifieds_sell_session` ADD `storefront_category` INT NOT NULL ;

ALTER TABLE `geodesic_classifieds_configuration` ADD `display_storefront_link` INT NOT NULL ;

ALTER TABLE `geodesic_classifieds_configuration` ADD `storefront_url` TINYTEXT NOT NULL ;

ALTER TABLE `geodesic_userdata` ADD `storefront_home_link` VARCHAR( 255 ) NOT NULL ;

CREATE TABLE `geodesic_storefront_template_modules` (
`module_id` INT NOT NULL ,
`template_id` INT NOT NULL ,
`connection_time` INT NOT NULL
);
";

splitSqlFile($pieces, $sql_query,$db);

echo "<span class = nongreen>General updates to database - </span><span class = green>success!</span><br>\n";
///////////////////

$insert_text_array = array (
1 =>
	array (500000, 'Storefront+Subscription+Title', 'Title+text', '', 37, 0, 0),
	array (500001, 'Storefront+Description', 'Descriptive+text+below+the+title', '', 37, 0, 0),
	array (500002, 'Storefront+category', 'Storefront+category+choices+if+the+user+has+a+storefront+subscription', '', 9, 0, 0),
	array (500003, 'Storefront+Header', 'Column+header+for+storefront+links', '', 3, 0, 0),
	array (500004, 'Storefront+Link+Text', 'Text+that+goes+in+the+row+for+the+storefront+link', '', 3, 0, 0),
	array (500005, 'Storefront+Link', 'Storefront+link+if+the+user+has+a+storefront+subscription', '', 1, 0, 0),
	array (500006, 'Storefront+URL', 'Storefront+URL+label', '', 37, 0, 0),
	array (500007, 'Storefront+category', 'Storefront+category+choices+if+the+user+has+a+storefront+subscription', '', 32, 0, 0)
);

reset ($insert_text_array);
foreach ($insert_text_array as $key => $value)
{
	$sql_query = "select * from geodesic_pages_messages where message_id = ".$insert_text_array[$key][0];
	$test_result = $db->Execute($sql_query);
	//echo $sql_query."<bR>\n";
	if (!$test_result)
	{
		echo "error in geodesic_text_messages select<bR>\n";
		exit;
	}
	elseif ($test_result->RecordCount() == 0)
	{
		if (strlen(trim($insert_text_array[$key][7])) == 0)
			$insert_text_array[$key][7] = 0;
		$sql_query = "insert into geodesic_pages_messages
			(message_id,name,description,text,page_id,display_order,classauctions)
			values
			(".$insert_text_array[$key][0].",\"".$insert_text_array[$key][1]."\",\"".$insert_text_array[$key][2]."\",\"".$insert_text_array[$key][3]."\",
			\"".$insert_text_array[$key][4]."\",\"".$insert_text_array[$key][5]."\",\"".$insert_text_array[$key][6]."\")";
		//echo $sql_query."<bR>\n";
		//echo "geodesic_pages_messages key ".$insert_text_array[$key][0]." of ".$insert_text_array[$key][1]." - inserted<bR>\n";
		$insert_result = $db->Execute($sql_query);

		if (!$insert_result)
		{
			echo $sql_query."<br>\n";
			echo "error in geodesic_pages_messages insert<bR>\n";
			exit;
		}
	}
	//else
		//echo "geodesic_pages_messages key ".$insert_text_array[$key][0]." of ".$insert_text_array[$key][1]." - already exists<bR>\n";
}

$sql_query = "update geodesic_pages_messages set
	name = \"Please choose a main category\"
	where message_id = 462";
$update_result = $db->Execute($sql_query);
//echo $sql_query."<bR>\n";
if (!$update_result)
{
	echo "error in geodesic_text_messages select<bR>\n";
	exit;
}

$sql_query = "update geodesic_pages_messages set
	name = \"category has\",
	description = \"Used in the statement explaining that the category chosen has subcategories so would the user choose one of the subcategories or affirm placement in the current category.\"
	where message_id = 463";
$update_result = $db->Execute($sql_query);
//echo $sql_query."<bR>\n";
if (!$update_result)
{
	echo "error in geodesic_text_messages select<bR>\n";
	exit;
}
//echo "<br><b>geodesic_pages_messages table upgraded</b><br><Br>";

/////////////////////////////

$upgrade_array = array (
1 =>
	array (37, 500000, 1, 'Storefront+Subscription+Information'),
	array (37, 500001, 1, 'renew+or+extend+your+storefront+subscription+below.'),
	array (9, 500002, 1, 'Storefront+Category'),
	array (3, 500003, 1, 'Storefront'),
	array (3, 500004, 1, 'storefront'),
	array (1, 500005, 1, 'Storefront'),
	array (37, 500006, 1, 'Storefront+URL'),
	array (32, 500007, 1, 'Storefront+Category')
);

reset ($upgrade_array);
$sql_query = "select language_id from geodesic_pages_languages";
$language_result = $db->Execute($sql_query);
if (!$language_result)
{
	echo "error in geodesic_text_languages select<bR>\n";
	exit;
}
else
{
	while ($show_language = $language_result->FetchRow())
	{
		reset($upgrade_array);
		foreach ($upgrade_array as $key => $value)
		{
			$sql_query = "select * from geodesic_pages_messages_languages where text_id = ".$upgrade_array[$key][1]." and language_id = ".$show_language["language_id"];
			//echo $sql_query."<bR>\n";
			$test_result = $db->Execute($sql_query);
			if (!$test_result)
			{
				echo "error in geodesic_pages_messages_languages select<bR>\n";
				exit;
			}
			elseif ($test_result->RecordCount() == 0)
			{
				$sql_query = "insert into geodesic_pages_messages_languages
					(page_id, text_id,language_id,text)
					values
					(".$upgrade_array[$key][0].",".$upgrade_array[$key][1].",\"".$show_language["language_id"]."\",\"".$upgrade_array[$key][3]."\")";
				//echo $sql_query."<bR>\n";
				$result = $db->Execute($sql_query);
				//echo "geodesic_pages_messages_languages key ".$upgrade_array[$key][1]." of language ".$upgrade_array[$key][2]." - inserted<bR>\n";

				if (!$result)
				{
					echo "error in geodesic_pages_messages_languages insert<bR>\n";
					exit;
				}
			}
			//else
			//	echo "geodesic_pages_messages_languages key ".$upgrade_array[$key][1]." of language ".$upgrade_array[$key][2]." - already exists<bR>\n";

		}
	}
}


$templateCode = "<html>
<head>

(!STOREFRONT_HEAD!)

<script LANGUAGE=\"JavaScript\" type=\"text/javascript\">
var isDrag = false;
var dragDOMElement;
var iniX,iniY;
var objX,objY;
function grab(e)
{
	if(!e)
		e=event;
	isDrag=true;
	iniX=e.clientX;
	iniY=e.clientY;
	dragDOMElement = document.getElementById(\'container\')
	objX=parseInt(dragDOMElement.style.left+0,10);
	objY=parseInt(dragDOMElement.style.top+0,10);
	document.onmousemove=drag;
	return false;
}
function drag(e)
{
	if(isDrag)
	{
		if(!e)
			e=event;
		dragDOMElement.style.left = objX+e.clientX-iniX;
		dragDOMElement.style.top = objY+e.clientY-iniY;
	}
	return false;
}
function drop(e)
{
	isDrag = false;
	return false;
}
var displayContent = true;
function toggleContent()
{
	displaySetting = displayContent ? \'none\' : \'\';
	document.getElementById(\'content\').style.display = displaySetting;
	displayContent = displayContent ? false : true;
}

</script>

<style type=\"text/css\">

a, a:active, a:visited{
	color:#000000;
	text-decoration:underline;
}
a:hover{
	color:#ff9900;
	text-decoration:underline;
}	
body {
	margin:0px;
	padding:0px;
	background-color:#eeeeee;
	font-family:Arial, Helvetica, sans-serif;
	font-size: 12px;
}
input {
	margin:0px;
	padding:0px;
	background-color:#fff;
	border:1px solid #000;
}
form {
	display:inline;
}
li {
	list-style:none outside;
        margin:0px;
        padding:0px;
	margin-left:-40px;
}
.file {
	margin:0px;
	padding:0px;
	background-color:#fff;
	border:1px solid #000;
}
.button {
	margin:0px;
	padding:0px;
	background-color:#fff;
	border:1px solid #000;
}
.select {
	margin:0px;
	padding:0px;
	background-color:#fff;
	border:1px solid #000;
}
#toggleContent {
	display:block;
	font-size: 14px;
	padding: 1px;
	margin: 2px 0;
	text-decoration: none;
	text-align: center;
	border: 1px solid #ffffff;
	color: #fff;
}
#toggleContent:hover {
	display:block;
	font-size: 14px;
	padding: 1px;
	margin: 2px 0;
	text-decoration: none;
	text-align: center;
	border: 1px solid #ffffff;
	color: #fff;
	background-color:#333333;
}
#container {
	position:relative;
	height:100px;
	width:500px;
	margin:0px;
}
#handle {
	position:relative;
	width:500px;
	background-color:#000000;
	padding:4px;
	margin:0px;
	-moz-border-radius-topleft:3px;
	-moz-border-radius-topright:3px;
}
#content {
	padding:3px;
	margin:0px;
	background-color:#dedede;
	width:500px;
	border:1px solid #000;
	font-size: 10pt;
}
#categories{
	text-align:left;
	background-color: #D2D4D6;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color:#fff;
}
#pages{
	text-align:left;
	background-color: #D2D4D6;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color:#fff;
}
#menu_title{
	border-bottom:2px solid #D2D4D6;
	background-color: #999999;
	text-align:center;
	padding:2px;
	border:1px solid #666;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-weight:bold;
	font-size:20px;
}
.menu_item{
	padding:2px;
	background-color: #D2D4D6;
	border:1px solid #666;
	border-top:0px;
	font-weight:400;
	font-size:9px;
}
.menu_item a,.menu_item a:active,.menu_item a:visited{
	color:#000000;
	text-decoration:none;
}
.menu_item a:hover{
	color:#ff9900;
	text-decoration:none;
}	
#body_table{
	margin:0px;
	border:10px solid #fff;
}
#normal_result_colum{
	margin:0px;
	padding:2px;
	background-color:#666;
	font-size: 10pt;
	font-weight: bold;
	color: #fff;
}
#listings_column{
	padding:0px;
	padding-bottom:10px;
	background-color:#fff;
}
#listings_table{
	margin:0px;
	padding:0px;
	border-left:1px solid #666;
	border-top:1px solid #666;
}
#header_column{
	background-color: #999;
	margin:1px;
	padding:2px;
	border-right:1px solid #666;
	border-bottom:1px solid #666;
	font-size: 10pt;
	color: #ffffff;
}
#header_column a,#header_column a:active,#header_column a:visited{
	color:#ffffff;
	text-decoration:none;
}
#header_column a:hover{
	color:#ff9900;
	text-decoration:none;
}	
#listing_column{
	margin:1px;
	padding:2px;
	border-right:1px solid #666;
	border-bottom:1px solid #666;
	font-size: 10pt;
}
#listing_column a,#listing_column a:active,#listing_column a:visited{
	color:#000000;
}
#listing_column a:hover{
	color:#ff9900;
}	
.listing_even_bold{
	background-color:#dddddd;
	font-weight:bold;
}
.listing_even{
	background-color:#dddddd;
}
.listing_odd_bold{
	background-color:#eeeeee;
	font-weight:bold;
}
.listing_odd{
	background-color:#eeeeee;
}
.no_ads_in_category{
	text-align:center;
	color:#ffffff;
}
.message_bar{
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	font-style: normal;
	line-height: normal;
	font-weight: bold;
	text-transform: none;
	color: ffffff;
        border-top:2px solid #ffcc99;
        border-bottom:2px solid #CC7A00;
        padding-right:5px;
        background-color:#ff9900;
}
.footer{
	text-align: center;
	font-size: 10pt;
}
.footer_link{
	font-size: 10pt;
}

</style>
<title>Storefronts</title>
</head>


<body onMouseUp=\'javascript: drop()\'>



<table align=center width=100% cellpadding=\"0\" cellspacing=\"0\">
	<tr style=\'background-color:#fff;\'>
		<td><img src=\"images/template/logo_hdr.gif\" alt=\'My Site Logo\'>
		</td>
		<td>
		(!STOREFRONT_LOGO!)
		</td>
	</tr>
	<tr>
		<td align=right colspan=\"2\" class=\'message_bar\'>
		(!STOREFRONT_WELCOME_NOTE!)
		</td>
	</tr>
	<tr>
		<td colspan=\"2\">
			<table cellpadding=\"5\" cellspacing=\"2\" style=\'width:100%;\'>
				<td align=\"center\" style=\'vertical-align:top;width:160px;		background-color: #575C62;\'>
				(!STOREFRONT_CATEGORIES MENU_TITLE=\"Categories\"!)
				(!STOREFRONT_PAGES MENU_TITLE=\"Pages\"!)
				</td>
				<td style=\'background-color: #89919A;\' align=\"center\">
				(!STOREFRONT_LISTINGS!)
				</td>
			</table>
		</td>
	</tr>
</table>

<div class=\"footer\" style=\"width:100%;\" align=\"center\">
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"footer\" align=\"center\">
    <tr>
        <td class=\"footer_links\" align=\"center\" width=\"50\"><a href=\"index.php\" class=\"footer_links\">home</a></td>
       <td width=\"1\">|</td>
       <td class=\"footer_links\" align=\"center\" width=\"80\"><a href=\"register.php\" class=\"footer_links\">register</a></td>
       <td width=\"1\">|</td>
       <td class=\"footer_links\" align=\"center\" width=\"80\"><a href=\"index.php?a=28&b=139\" class=\"footer_links\">services</a></td>
       <td width=\"1\">|</td>
       <td class=\"footer_links\" align=\"center\" width=\"150\"><a href=\"index.php?a=28&b=140\" class=\"footer_links\">terms & conditions</a></td>
       <td width=\"1\">|</td>
       <td class=\"footer_links\" align=\"center\" width=\"50\"><a href=\"index.php?a=28&b=141\" class=\"footer_links\">help</a></td>
   </tr>
</table>
</div>
<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"footer\">
     <tr>
         <td align=\"center\"> Copyright 2005 www.MyClassifiedsSite.com All Rights Reserved. </td>
     </tr>
</table>
<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"footer\">             
    <tr>
       <td align=\"center\"><img src=\"images/template/ccards.gif\" width=\"98\" height=\"24\"> <img src=\"images/template/paypal.gif\" width=\"78\" height=\"24\"></td>
    </tr>
</table>





(!STOREFRONT_MANAGER!)
<div id=container onMouseOver=\'javascript: document.getElementById(\"handle\").onmousedown=grab;\'>
<div id=handle>
	<table cellspacing=0 cellpadding=0>
		<tr style=\'vertical-align:bottom;\'>
		<td style=\'width:480px; color:#ffffff;vertical-align:bottom;\'>storefront manager</td>
		<td style=\'text-align:right;width:20px; color:#ffffff;vertical-align:bottom;\'>
		<a id=toggleContent href=\'javascript: toggleContent()\'>_</a>
		</td>
		</tr>
	</table>
</div>
<div id=content>
<table align=center width=100% cellpadding=2 cellspacing=0>
	<tr>
		<td align=right colspan=2>
		(!STOREFRONT_MANAGER_ERROR!)
		</td>
	</tr>
	<tr>
		<td>
		Edit Logo:
		</td>
		<td align=right>
		(!STOREFRONT_MANAGER_LOGO BUTTON_TEXT=\"update\"!)
		</td>
	</tr>
	<tr>
		<td>
		Edit Welcome Note:
		</td>
		<td align=right>
		(!STOREFRONT_MANAGER_WELCOME_NOTE BUTTON_TEXT=\"update\"!)
		</td>
	</tr>
	<tr>
		<td>
		Edit Home Link:
		</td>
		<td align=right>
		(!STOREFRONT_MANAGER_HOME_LINK BUTTON_TEXT=\"edit\"!)
		</td>
	</tr>
	<tr>
		<td>
		Add Categories:
		</td>
		<td align=right>
		(!STOREFRONT_MANAGER_CATEGORIES_ADD BUTTON_TEXT=\"add\"!)
		</td>
	</tr>
	<tr>
		<td>
		Edit Categories:
		</td>
		<td align=right>
		(!STOREFRONT_MANAGER_CATEGORIES_EDIT BUTTON_TEXT=\"edit\"!)
		</td>
	</tr>
	<tr>
		<td>
		Delete Categories:
		</td>
		<td align=right>
		(!STOREFRONT_MANAGER_CATEGORIES_DELETE BUTTON_TEXT=\"delete\"!)
		</td>
	</tr>
	<tr>
		<td>
		Sort categories:
		</td>
		<td align=right>
		(!STOREFRONT_MANAGER_CATEGORIES_SORT BUTTON_TEXT=\"sort\"!)
		</td>
	</tr>
	<tr>
		<td>
		Add a page:
		</td>
		<td align=right>
		(!STOREFRONT_MANAGER_PAGE_ADD BUTTON_TEXT=\"add\"!)
		</td>
	</tr>
	<tr>
		<td>
		Switch Template:
		</td>
		<td align=right>
		(!STOREFRONT_MANAGER_TEMPLATE BUTTON_TEXT=\"update\"!)
		</td>
	</tr>
	<tr>
		<td>
		Active?:
		</td>
		<td align=right>
		(!STOREFRONT_MANAGER_HOLD ON_BUTTON_TEXT=\"turn off\" OFF_BUTTON_TEXT=\"turn on\"!)
		</td>
	</tr>
</table>
</div>
</div>
(!END_STOREFRONT_MANAGER!)

(!STOREFRONT_MANAGER_EXTRA_PAGE!)
<div id=container style=\"width:650;\" onMouseOver=\'javascript: document.getElementById(\"handle\").onmousedown=grab;\'>
<div id=handle style=\"width:650;\">
	<table cellspacing=0 cellpadding=0>
		<tr style=\'vertical-align:bottom;\'>
		<td style=\'width:630px; color:#ffffff;vertical-align:bottom;\'>storefront manager</td>
		<td style=\'text-align:right;width:20px; color:#ffffff;vertical-align:bottom;\'>
		<a id=toggleContent href=\'javascript: toggleContent()\'>_</a>
		</td>
		</tr>
	</table>
</div>
<div id=content style=\"width:650;\">
<table align=center width=100%>
	<tr>
		<td align=right colspan=2>
		(!STOREFRONT_MANAGER_ERROR!)
		</td>
	</tr>
	<tr>
		<td>
		Page Body:
		</td>
		<td align=right>
		(!STOREFRONT_MANAGER_PAGE_BODY BUTTON_TEXT=\"edit\" COLUMNS=\"60\" ROWS=\"20\"!)
		</td>
	</tr>
	<tr>
		<td>
		Page Name:
		</td>
		<td align=right>
		(!STOREFRONT_MANAGER_PAGE_NAME BUTTON_TEXT=\"edit\"!)
		</td>
	</tr>
	<tr>
		<td>
		Page Link Text:
		</td>
		<td align=right>
		(!STOREFRONT_MANAGER_PAGE_LINK_TEXT BUTTON_TEXT=\"edit\"!)
		</td>
	</tr>
	<tr>
		<td>
		Delete Page:
		</td>
		<td align=right>
		(!STOREFRONT_MANAGER_PAGE_DELETE BUTTON_TEXT=\"delete\"!)
		</td>
	</tr>
</table>
</div>
</div>
(!END_STOREFRONT_MANAGER_EXTRA_PAGE!)
</body>
</html>";
$sql_query = "
INSERT INTO `geodesic_templates` ( `template_id` , `name` , `description` , `language_id` , `location` , `template_code` , `last_template` , `storefront_template` , `storefront_template_default` )
VALUES (
'', 'Storefront Default Template', 'Mimmicks the original default template for classauction software.', '1', '', '$templateCode', '', '1', '1'
)";
$update_result = $db->Execute($sql_query);
//echo $sql_query."<bR>\n";
if (!$update_result)
{
	echo "error in geodesic_text_messages select<bR>\n";
	exit;
}

echo "<span class = nongreen><br>Complete<br></span><br>";