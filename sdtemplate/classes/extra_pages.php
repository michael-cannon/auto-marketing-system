<? //extra_pages.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class extra_page extends Site 
{
	function extra_page($db, $page_id, $language_id, $user_id, $product_configuration)
	{
		if(ereg("^[0-9]{3}$", $page_id))
			$this->page_id = $page_id;

		$this->Site($db, 0, $language_id, $user_id, $product_configuration);
	}

	function setup_filters($filter_id, $state_filter, $zip_filter, $zip_distance_filter)
	{
		$this->state_filter = $state_filter;
		$this->zip_filter = $zip_filter;
		$this->zip_distance_filter = $zip_distance_filter;
		$this->filter_id = $filter_id;
	}

	function build_extra_page($db)
	{
		$this->sql_query = "select extra_page_text from ".$this->pages_table." where page_id = ".$this->page_id;
		$result = $db->Execute($this->sql_query);
		if(!$result)
			return false;
		else
			$body = $result->FetchRow();

		$this->body = stripslashes(urldecode($body['extra_page_text']));

		return true;
	}

	function display_extra_page($db)
	{
		// Build the body of the extra page
		if(!$this->build_extra_page($db))
			return false;

		// Do anything thats needed before here

		return $this->display_page($db);
	}
}

?>