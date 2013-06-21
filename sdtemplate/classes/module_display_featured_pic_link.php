<? //module_display_featured_pic_link.php	
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$this->get_css($db,$show_module['page_id']);
$this->get_text($db,$show_module['page_id']);
$this->body = "";
$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=8&b=".$this->site_category." class=featured_pic_link_text>".urldecode($this->messages[1059])."</a>";
?>