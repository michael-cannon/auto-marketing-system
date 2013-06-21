<?php
/**
 * User counter for movingiron.com
 *
 * @author Sergey Burlakov <burlakov@bcs-it.com>
 * @version $Id: counter.php,v 1.1 2006/06/22 16:48:00
 */

$db_host = MI_LEADS_HOST;
$db_username = MI_LEADS_USER;
$db_password = MI_LEADS_PW;
$db_name = MI_LEADS_DB;

$_SESSION['date'] = date("d-m-Y");

$HTTP_USER_AGENT = $_SERVER[ 'HTTP_USER_AGENT' ];

if(preg_match("/Windows/", $HTTP_USER_AGENT)) $os = 'Windows';
elseif(preg_match("/Linux/", $HTTP_USER_AGENT)) $os = 'Linux';
elseif(preg_match("/Unix/", $HTTP_USER_AGENT)) $os = 'Unix';
elseif(preg_match("/Mac/", $HTTP_USER_AGENT)) $os = 'Mac OS';
else $os = 'Other';

if(preg_match("/Opera.(\d{1,2}).(\d)(\d?)/", $HTTP_USER_AGENT)) $browser = 'Opera';
elseif(preg_match("/Firefox\/(\d{1,2})\.(\d)\.(\d)/", $HTTP_USER_AGENT)) $browser = 'Firefox';
elseif(preg_match("/MSIE (\d{1,2})\.(\d)(\d?)/", $HTTP_USER_AGENT)) $browser = 'IE';
elseif(preg_match("/Netscape\d?\/(\d{1,2})\.(\d)\.?(\d?)/", $HTTP_USER_AGENT)) $browser = 'Netscape';
elseif(preg_match("/Konqueror\/(\d{1,2})\.?(\d?)\.?(\d?)/", $HTTP_USER_AGENT)) $browser = 'Konqueror';
elseif(preg_match("/Safari\/(\d)*(\d)(\d)/", $HTTP_USER_AGENT)) $browser = 'Safari';
elseif(preg_match("/ rv\:(\d{1,2})\.(\d).?(\d?)\) Gecko/", $HTTP_USER_AGENT)) $browser = 'Mozilla';
elseif(preg_match("/Lynx\/(\d{1,2})\.(\d)\.(\d)rel/", $HTTP_USER_AGENT)) $browser = 'Lynx';
else $browser = 'Other';

$session_id = session_id();

if(!$db = mysql_connect($db_host, $db_username, $db_password))
{
	echo "Can't connect to database.";
	echo "MySql errno: ".mysql_errno().". MySql error: ".mysql_error();
}
if(!mysql_select_db($db_name, $db))
{
	echo "Can't select database.";
	echo "MySql errno: ".mysql_errno().". MySql error: ".mysql_error();
}

$query = "INSERT INTO counter
VALUES (1,
'".$os."',
'".$browser."',
'".date("d-m-Y")."',
now(),
'".$_SERVER['HTTP_REFERER']."',
'".$_SERVER['REMOTE_ADDR']."',
'".$session_id."',
'".date("Y")."',
'".date("m")."',
'".date("d")."',
'".$_SERVER['HTTP_HOST']."',
'".$_SERVER['SCRIPT_NAME']."',
'".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."')
ON DUPLICATE KEY UPDATE count = count + 1
";

$result = mysql_query($query);

mysql_close($db);
?>