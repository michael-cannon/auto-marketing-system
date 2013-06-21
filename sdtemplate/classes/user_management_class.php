<? //user_management_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class User_management extends Site
{
	var $error_found;
	var $error;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function User_management ($db,$language_id,$classified_user_id=0,$product_configuration=0)
	{
		$this->Site($db,6,$language_id,$classified_user_id,$product_configuration);

	} //end of function User_management

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function user_management_home ($db)
	{
		if ($this->classified_user_id)
		{
			//display the user management home page
			echo "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
			echo "<tr class=user_management_title>\n\t\t<td valign=top>\n\t\t".urldecode($this->messages[89])."\n\t\t</td>\n\t</tr>\n\t";
			echo "<tr>\n\t\t<td>\n\t\t";
			$this->user_management_menu($db,$switch);
			echo "</td>\n\t</tr>\n\t";
			echo "</table>\n\t";
		}
		return true;
	} //end of function user_management_home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function user_management_menu ($db,$switch=0)
	{
		if ($this->classified_user_id)
		{
			echo "<table cellpadding=2 cellspacing=1 border=0 align=center width=100% class=user_management_menu_links>\n\t";
			echo "<tr>\n\t\t<td align=center><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=1 class=user_management_menu_links>".urldecode($this->messages[93])."</a>\n\t\t</td>\n\t</tr>\n\t";
			echo "<tr>\n\t\t<td align=center><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2 class=user_management_menu_links>".urldecode($this->messages[94])."</a>\n\t\t</td>\n\t</tr>\n\t";
			echo "<tr>\n\t\t<td align=center><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=3 class=user_management_menu_links>".urldecode($this->messages[95])."</a>\n\t\t</td>\n\t</tr>\n\t";
			echo "<tr>\n\t\t<td align=center><a href=".$this->configuration_data['classifieds_file_name']."?a=1 class=user_management_menu_links>".urldecode($this->messages[96])."</a>\n\t\t</td>\n\t</tr>\n\t";
			echo "<tr>\n\t\t<td align=center><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=9 class=user_management_menu_links>".urldecode($this->messages[97])."</a>\n\t\t</td>\n\t</tr>\n\t";
			echo "<tr>\n\t\t<td align=center>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=10 class=user_management_menu_links>".urldecode($this->messages[289])."</a>\n\t\t</td>\n\t</tr>\n\t";
			echo "<tr>\n\t\t<td align=center><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=8 class=user_management_menu_links>".urldecode($this->messages[98])."</a>\n\t\t</td>\n\t</tr>\n\t";
			echo "<tr>\n\t\t<td align=center><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=7 class=user_management_menu_links>".urldecode($this->messages[99])."</a>\n\t\t</td>\n\t</tr>\n\t";
			echo "</table>\n\t";
		}
		else
		{
			//no user id
			$this->error_message = $this->data_missing_error_message;
			return false;
		}
	} //end of function user_management_menu

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%





} //end of class User_management_class

?>