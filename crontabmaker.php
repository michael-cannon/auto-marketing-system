<?php
/**
 * Crontab maker
 *
 * @author Sergey Burlakov <burlakov@bcs-it.com>
 * @version $Id: crontabmaker.php,v 1.1 2006/06/28 14:34:00
 */

require_once( 'config.php' );

$debug = 0;
$mailto = $adminEmail;

$session_id = session_id();

if(!$db = mysql_connect($db_host, $db_username, $db_password))
{
	echo "Can't connect to database.";
	echo "MySql errno: ".mysql_errno().". MySql error: ".mysql_error();
	exit();
}
if(!mysql_select_db($db_name, $db))
{
	echo "Can't select database.";
	echo "MySql errno: ".mysql_errno().". MySql error: ".mysql_error();
	exit();
}

$query = "SELECT crontab FROM crontab WHERE live = 1 AND endtime > ".time();
$result = mysql_query($query);
if($result)
{
	$i = 0;
	while($row = mysql_fetch_array($result))
	{
		$crontab[$i] = stripslashes($row['crontab'])."\n";
		if($debug) echo $crontab[$i].'<br>';
		$i ++;
	}
}
else
{
	echo "Can't execute mysql query.";
	echo "MySql errno: ".mysql_errno()." Mysql error: ".mysql_errno();
	exit();
}

if(!$file = fopen($cpConfig['webdirectory'] . "cp/crontab.dat", "w"))
{
	echo "Error. Can't open file {$cpConfig['webdirectory']}/cp/crontab.dat";
	exit();
}

if(!fwrite($file, "MAILTO=\"".$mailto."\"\n"))
{
	echo "Error. Can't write string in file {$cpConfig['webdirectory']}/cp/crontab.dat";
	exit();
}

for($i = 0; $i < count($crontab); $i ++)
{
	if(!fwrite($file, $crontab[$i]))
	{
		echo "Error. Can't write string in file {$cpConfig['webdirectory']}/crontab.dat";
		exit();
	}
}

fclose($file);

if($debug)
{
	$str = implode("", file($cpConfig['webdirectory'] . "crontab.dat"));
	echo $str;
}

// turn off excess crons
$query = "UPDATE crontab SET live = 0 WHERE endtime < ".time();
$result = mysql_query($query);
?>