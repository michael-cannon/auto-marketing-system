<?php
include_once("../config.php");
include_once("../classes/adodb.inc.php");
include_once("../classes/site_class.php");

if (isset ($HTTP_SERVER_VARS))
{
    $_SERVER = $HTTP_SERVER_VARS;
}
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

$sql_query_classifieds_count =
		"SELECT u.email as user_email
		FROM geodesic_logins l
		left join geodesic_userdata u on l.id = u.id
		WHERE 1 = 1
		and l.status = 1
		and level = 0
		and u.email != ''
		group by u.email asc";

$result_count = $db->Execute($sql_query_classifieds_count);

$solt = date(Ymd);
$filename = 'ams-leads-' . $solt . '.csv';

if(!$file = fopen($filename, "w"))
{
	Echo "<br>Can't open file $filename<br>";
    exit;
}

while ($show_classifieds = $result_count->FetchRow())
{
    $text = $show_classifieds['user_email'] . "\n";
    $textView .= $show_classifieds['user_email'] . '<br>';
    fwrite($file, $text, 1024);
}
fclose($file);

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Disposition: attachment; filename=$filename");

readfile($filename);
$pathToFile = $_SERVER["DOCUMENT_ROOT"] . '/scripts/' . $filename;
unlink($pathToFile);
?>