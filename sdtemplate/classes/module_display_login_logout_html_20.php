<? //module_display_login_logout_html_6.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$this->body = "";
if ($this->classified_user_id)
{
	$this->sql_query = "select module_logged_in_html from ".$this->pages_table." where page_id = 197";
	$logged_result = $db->Execute($this->sql_query);
	if (!$logged_result)
	{
		return false;
	}
	elseif ($logged_result->RecordCount() == 1)
	{
		$show_logged = $logged_result->FetchRow();
		$this->body = stripslashes($show_logged['module_logged_in_html']);
		//echo $show_logged['module_logged_in_html']." is logged in html<bR>\n";
	}
	else
		$this->body = "";
}
else
{
	$this->sql_query = "select module_logged_out_html from ".$this->pages_table." where page_id = 197";
	$logged_result = $db->Execute($this->sql_query);
	if (!$logged_result)
	{
		return false;
	}
	elseif ($logged_result->RecordCount() == 1)
	{
		$show_logged = $logged_result->FetchRow();
		$this->body = stripslashes($show_logged['module_logged_out_html']);
		//echo $show_logged['module_logged_out_html']." is logged out html<bR>\n";
	}
	else
		$this->body = "";
}
?>