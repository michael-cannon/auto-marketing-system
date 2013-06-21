<? //module_total_live_users.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

 	// Debug variables
	$filename = "module_total_live_users.php";
	$function_name = "module_total_live_users";

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

$this->get_css($db,170);
$this->get_text($db,170);

$this->body = "";

$this->sql_query = "select * from ".$this->pages_table." where page_id = 170";
$logged_result = $db->Execute($this->sql_query);
if($this->configuration_data['debug_modules'])
{
	$this->debug_display($this->sql_query, $db, $filename, $function_name, "geodesic_sessions", "get module logged in users from pages table by page id");
}
if (!$logged_result)
{
	return false;
}
elseif ($logged_result->RecordCount() == 1)
{
	$this->sql_query = "select count(*) counter from geodesic_sessions";
	$logged_result = $db->Execute($this->sql_query);
	if($this->configuration_data['debug_modules'])
	{
		$this->debug_display($this->sql_query, $db, $filename, $function_name, "pages_table", "get count of live users");
	}
	if (!$logged_result)
	{
		return false;
	}

	$count = $logged_result->FetchRow();

	$this->body = "<font class=live_users_text>" . urldecode($this->messages[2458]) . $count['counter'] . "</font>";
}
else 
	$this->body = "";
?>
