<? //module_display_login_register.php	
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$this->get_css($db,$show_module['page_id']);
$this->body = "";
$this->get_text($db,$show_module['page_id']);
if ($this->classified_user_id)
{
	//display the my account link
	$this->body = "<a href=".$this->configuration_data['classifieds_url']."?a=17 class=login_register_logout_link>".urldecode($this->messages[749])."</a> |";
	
	$this->body .= "<a href=".$this->configuration_data['classifieds_url']."?a=4 class=login_register_my_account_link>".urldecode($this->messages[748])."</a>";

	
}
else
{
	//display the register login links
	$this->body = "<a href=".$this->configuration_data['classifieds_url']."?a=10 class=login_register_login_link>".urldecode($this->messages[746])."</a> | <a href=";
	if ($this->configuration_data['use_ssl_in_registration'])
		$this->body .= $this->configuration_data['registration_ssl_url'];
	else
		$this->body .= $this->configuration_data['registration_url'];
	$this->body .= " class=login_register_register_link>".urldecode($this->messages[747])."</a>";
}
?>