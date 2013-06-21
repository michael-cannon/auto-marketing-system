<? //module_total_live_users.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

 	// Debug variables
	$filename = "module_total_registered_users.php";
	$function_name = "module_total_registered_users";

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

$this->get_css($db,169);
$this->get_text($db,169);

$this->body = "";

$this->sql_query = "select * from ".$this->pages_table." where page_id = 169";
$logged_result = $db->Execute($this->sql_query);
if($this->configuration_data['debug_modules'])
{
	$this->debug_display($this->sql_query, $db, $filename, $function_name, "pages_table", "get module registered users from pages table by page id");
}
if (!$logged_result)
{
	return false;
}
elseif ($logged_result->RecordCount() == 1)
{
	// This will get all users other than the admin
	$this->sql_query = "select count(id) counter from geodesic_logins where id > 1 and status = 1";
	$logged_result = $db->Execute($this->sql_query);
	if($this->configuration_data['debug_modules'])
	{
		$this->debug_display($this->sql_query, $db, $filename, $function_name, "pages_table", "get count of registered users");
	}
	if (!$logged_result)
	{
		return false;
	}

	$count = $logged_result->FetchRow();

	$this->body = "<font class=registered_users_text>" . urldecode($this->messages[2459]) . $count['counter'] . "</font>";
}
else 
	$this->body = "";
?>
