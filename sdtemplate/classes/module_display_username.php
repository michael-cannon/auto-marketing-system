<? //module_display_username.php	
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$this->get_css($db,$show_module['page_id']);
$this->body = "<font class=identifier_field>";
if ($this->classified_user_id)
{
	switch ($show_module['module_display_username'])
	{
		case 1:
		{
			//display username only
			$this->body .= $this->get_user_name($db,$this->classified_user_id);
			break;
		}
		case 2:
		{
			//display firstname only
			$firstname = $this->get_user_data($db,$this->classified_user_id);
			$this->body .= stripslashes($firstname->FIRSTNAME);
			break;		
		}
		case 3:
		{
			//display lastname only
			$lastname = $this->get_user_data($db,$this->classified_user_id);
			$this->body .= stripslashes($lastname->LASTNAME);
			break;		
		}
		case 4:
		{
			//display firstname lastname
			$name = $this->get_user_data($db,$this->classified_user_id);
			$this->body .= stripslashes($name->FIRSTNAME." ".$name->LASTNAME);
			break;		
		}
		case 5:
		{
			//display lastname, firstname
			$name = $this->get_user_data($db,$this->classified_user_id);
			$this->body .=  stripslashes($name->LASTNAME." ".$name->FIRSTNAME);
			break;		
		}		
		case 6:
		{
			//display email address
			$this->body .= $this->get_user_email($db,$this->classified_user_id);
			break;		
		}				
		default:
		{
			//default display username only
			$this->body .= $this->get_user_name($db,$this->classified_user_id);
			break;		
		}
	}	
}
else
{
	$this->get_text($db,$show_module['page_id']);
	//display the guest monikor
	$this->body .= urldecode($this->messages[728]);
}
$this->body .= "</font>";
?>