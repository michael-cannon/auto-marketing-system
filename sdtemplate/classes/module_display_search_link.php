<? //module_display_search_link.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$this->get_css($db,$show_module['page_id']);
$this->get_text($db,$show_module['page_id']);
$this->body = "";

if ($this->site_category)
{
	$category_name = $this->get_category_name($db,$this->site_category);
	$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=19&c=".$this->site_category." class=search_link>".urldecode($this->messages[1470])." ".$category_name->CATEGORY_NAME."</a>";
}
elseif($_REQUEST['b'] == 'ViewAll')
{
	$this->body .= '';
}
else
{
	$this->messages[1469] = 'Search';
	$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=19 class=search_link>".urldecode($this->messages[1469])."</a>";
}
?>
