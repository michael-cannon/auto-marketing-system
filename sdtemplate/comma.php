<?php
include_once("config.php");
include_once("classes/adodb.inc.php");
include_once("classes/site_class.php");
include_once("classes/register_class.php");
error_reporting  (E_ERROR | E_WARNING | E_PARSE);

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

$query = "SELECT id, description from geodesic_classifieds where description like (\"%%2C+%2C+.%\") LIMIT 300";

$result = $db->Execute($query);
if($result)
{
	while ($row = $result->FetchRow())
    {
    	$id = $row['id'];
        $description = urldecode($row['description']);

        $description = eregi_replace("(, ){2,10}.", "", $description);

        $sql = "UPDATE geodesic_classifieds SET description = \"".urlencode($description)."\" WHERE id=$id LIMIT 1";

        $sql_result = $db->Execute($sql);
        if($sql_result) {$sql_results .= "$id  - updated<br>";}

        $str .= "UPDATE geodesic_classifieds SET description = \"".urlencode($description)."\" WHERE id=$id LIMIT 1";
        $strId .= $id."<br>";
    }

    echo "<hr size=1 color=#ff9900>".$str."<hr size=1 color=#ff9900>".$strId;
    echo "<hr size = 1 color=#ff9900>".$sql_results;
}
?>