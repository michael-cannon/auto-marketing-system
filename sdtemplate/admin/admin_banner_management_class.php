<? //admin_banner_management_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Admin_banner_management extends Admin_site{



//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_banners($db,$list_info=0)
	{

		$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
		$this->body .= "<tr>\n\t\t<td colspan=6 class=very_large_font>Ad Banners</td>\n\t</tr>\n\t";
		$this->body .= "<tr>\n\t\t<td colspan=6 class=medium_font>Because this version of our classified products is template driven and the fact<br>
																that there are some wonderful products available for banner advertising campaigns, we<br>
																have decided to remove our banner management application and devote our development efforts<br>
																toward functionality more directly related to classifieds.<br><br>
																If you would like to pursue a banner advertising campaign in association with<br>
																GeoClassifieds Enterprise, we recommend phpAdsNew.<br>
																This software is free and can be found here:<br><br>
																http://www.phpadsnew.com</td>\n\t</tr>\n\t";
		$this->body .= "</table>";
		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Admin_banner_management

?>