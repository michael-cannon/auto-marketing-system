<? //module_display_newest_link_2_week.php	
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$this->get_css($db,$show_module['page_id']);
$this->get_text($db,$show_module['page_id']);
$this->body = "";
if (is_array($this->site_category))
	$this->site_category = 0;
$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=11&b=".$this->site_category."&c=2 class=newest_2_week_link>".urldecode($this->messages[1211])."</a>";
?>