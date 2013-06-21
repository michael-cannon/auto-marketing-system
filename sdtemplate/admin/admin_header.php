<?
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

if ($this->is_class_auctions())
	$this->header_html .= "<html>\n<head><title>GeoClassAuctions Admin</title>";
elseif ($this->is_auctions())
	$this->header_html .= "<html>\n<head><title>GeoAuctions Enterprise Admin</title>";
else
	$this->header_html .= "<html>\n<head><title>GeoClassified Enterprise Admin</title>";

$this->header_html .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"admin.css\">\n";

$this->header_html .=  "<style type=\"text/css\">";
		if($extra_css)
			$this->header_html .= $extra_css;
$this->header_html .= "</style>\n";

// Check for additional header_html
if(strlen($this->extra_header_html))
	$this->header_html .= $this->extra_header_html;

/*
$this->header_html .= "<SCRIPT language=\"JavaScript1.2\" type=\"text/javascript\">";
include("tooltip.js");
$this->header_html .= "</SCRIPT>\n";
*/
if($menu)
	$this->header_html .= "<script type=\"text/javascript\" src=\"DynamicTree.js\"></script>\n";

$this->sql_query = "select charset from ".$this->site_configuration_table;
$result = $db->Execute($this->sql_query);
if(!$result)
{
		return false;
}
elseif ($result->RecordCount() > 0)
{
	$charset = $result->FetchRow();
	$this->header_html .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=\"".$charset["charset"]."\">";
}

// Tooltip
$tooltip = file_get_contents("tooltip.js");
$this->header_html .= "
	<SCRIPT language=\"JavaScript1.2\" type=\"text/javascript\">{$tooltip}</script>{$this->additional_header_html}
</head>
<body {$this->additional_body_tag_attributes}>

	<div id=\"tiplayer\" style=\"visibility:hidden;position:absolute;z-index:1000;top:-100;\"></div>
	<script language=\"JavaScript\">
	<!--
		function win(fileName) {
		     myFloater = window.open('','myWindow','scrollbars=yes,status=no,width=300,height=500')
		     myFloater.location.href = fileName;
		}
		function winimage(fileName,width,height) {
		     myFloater = window.open('','myWindow','scrollbars=yes,status=no,width=' + width + ',height=' + height)
		     myFloater.location.href = fileName;
		}
	-->
	</script>
	<table cellspacing=0 cellpadding=0 border=0 width=100% align=center>
      <tr>
        <td colspan=3>
          <table width=100% cellpadding=0 cellspacing=0 border=0>
            <tr valign=top>
              <td>
                <table width='100%' border='0' cellspacing='0' cellpadding='0' class=admin_header>
                  <tr>
                    <td width='520' valign='top' align='left'>
                      <table width='520' border='0' cellspacing='0' cellpadding='0'>
                        <tr valign='top'>";
if ($this->is_class_auctions())
	$this->header_html .= "
                        <td><img src='admin_images/admin_hdr_logo_classauctions.gif' width='560' height='39'></td>";
elseif ($this->is_auctions())
	$this->header_html .= "
                        <td><img src='admin_images/admin_hdr_logo_auct.gif' width='560' height='39'></td>";
else
	$this->header_html .= "
                        <td><img src='admin_images/admin_hdr_logo.gif' width='560' height='39'></td>";
$this->header_html .= "
					  </tr>
					</table>
					<table width='560' border='0' cellpadding='0' cellspacing='0'>
				 	  <tr valign='top'>
			 			<td>
						   <table width='100%' border='0' cellspacing='0' cellpadding='0'>
						    <tr>
						     <td align='left' width='48%'>
						      <table width='560' border='0' cellspacing='0' cellpadding='0'>
						       <tr>
						        <td height='2'>
						         <table width='560' border='0' cellspacing='0' cellpadding='0'>
						          <tr>
						           <td>
						            <table width='100%' border='0' cellspacing='0' cellpadding='0'>
						             <tr>
						              <td width='8' align='right'><img src='admin_images/menu_bar_home.gif' width='34' height='30'></td>
						              <td align='center' width='100%' nowrap background='admin_images/menu_bar_bg.gif' class='menu_bar_links'><a href='index.php' class='menu_bar_links'>Admin Home</a></td>
						              <td align='right' width='8'><img src='admin_images/tabend.gif' width='31' height='30'></td>
						             </tr>
						            </table>
						           </td>
						           <td width='1'> </td>
						           <td>
						            <table width='100%' border='0' cellspacing='0' cellpadding='0'>
						             <tr>
						              <td width='8' align='left'><img src='admin_images/menu_bar_site.gif' width='18' height='30'></td>
						              <td align='center' width='100%' nowrap background='admin_images/menu_bar_bg.gif'>";

$sql_query = "select classifieds_url from ".$this->site_configuration_table;
$result = $db->Execute($sql_query);
if(!$result)
	return false;
else
	$file_result = $result->FetchRow();
include("../config.php");
if ($demo)
	$this->header_html .= '<a href="../../enterprise/index.php" class="menu_bar_links">My Site </a>';
else
	$this->header_html .= '<a href="'.$file_result["classifieds_url"].'" target = _blank class="menu_bar_links">My Site </a>';


$this->header_html .= "
  									  </td>
						              <td align='right' width='8'><img src='admin_images/tabend.gif' width='31' height='30'></td>
						             </tr>
						            </table>
						           </td>
						           <td width='1'> </td>
						           <td>
						            <table width='100%' border='0' cellspacing='0' cellpadding='0'>
						             <tr>
						              <td width='8' align='left'><img src='admin_images/menu_bar_support.gif' width='27' height='30'></td>
						              <td align='center' width='100%' nowrap background='admin_images/menu_bar_bg.gif'><a href='http://www.geodesicsolutions.com/support/index.htm' target=_blank class='menu_bar_links'>Support
						                              </a></td>
						              <td align='right' width='8'><img src='admin_images/tabend.gif' width='31' height='30'></td>
						             </tr>
						            </table>
						           </td>
						           <td width='1'> </td>
						           <td>
						            <table width='100%' border='0' cellspacing='0' cellpadding='0'>
						             <tr>
						              <td width='8' align='left'><img src='admin_images/menu_bar_logout.gif' width='21' height='30'></td>
						              <td align='center' width='100%' nowrap background='admin_images/menu_bar_bg.gif'><a href='index.php?a=104' class='menu_bar_links'>Logout</a></td>
						              <td align='right' width='8'><img src='admin_images/tabend2.gif' width='28' height='30'></td>
						             </tr>
						            </table>
						           </td>
						          </tr>
						         </table>
						        </td>
						       </tr>
						      </table>
						     </td>
						    </tr>
						   </table>
						 </tr>
					  </table>
                    </td>
                    <td bgcolor='#E3F5FF'>&nbsp;</td>
                      <td width='185' valign='top'><img src='admin_images/admin_hdr_rt.gif' width='185' height='69'></td>
                    </tr>
                  </table>
                  <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                      <td height='3' bgcolor='#000066'><img src='admin_images/shim1x1.gif' width='1' height='1'></td>
                    </tr>
                    <tr>
                      <td height='1'><img src='admin_images/shim1x1.gif' width='1' height='1'></td>
                    </tr>
                    <tr>
                      <td height='1' bgcolor='#000066'><img src='admin_images/shim1x1.gif' width='1' height='1'></td>
                    </tr>
                  </table>
                </td>
              </tr>";
if ($demo)
	$this->header_html .= "
				  <tr>
				  	<td colspan=\"2\" class=\"medium_error_font\" align=\"center\"><b>NOTE:</b> THE ABILITY TO SUBMIT CHANGES IN THE ADMIN HAS BEEN <BR>REMOVED FOR DEMONSTRATION PURPOSES. ANY CHANGES MADE WILL NOT DISPLAY</td>
				  </tr>";
$this->header_html .= "
				</table>
			</td>
		</tr>
		<tr>
			<td valign='top'>";

$config_title = ($this->is_class_auctions() || $this->is_auctions()) ? "Listing Setup" : "Ad Configuration";
$item_name = ($this->is_class_auctions() || $this->is_auctions()) ? "Listing" : "Ad";
if($menu)
{
	// Menu
	// Title info and whatnot
	$this->header_html .= "
		<table cellspacing=1 cellpadding=1 border=0  align=center width=100%>
			<tr>
				<td bgcolor='#ffffff' valign=top width=190 valign='top'>
					<div id='tiplayer' style='visibility:hidden;position:absolute;z-index:1000;top:-100;'></div>
					<img src='admin_images/shim_190x1.gif'/>
					<center class=\"pg_title1\">Administration Menu</center>
					<img src='admin_images/shim_190x1.gif'/>
					<div class='DynamicTree'>
						<div class='top'><a href='index.php' title='Admin Home'>Admin Home</a></div>
						<div class='wrap' id='tree'>
							<div class='folder'>Site Setup
								<div class='doc'><a href='index.php?a=28&z=6' title='General Settings'>General Settings</a></div>
								<div class='doc'><a href='index.php?a=28&z=7' title='Browsing Settings'>Browsing Settings</a></div>
								<div class='doc'><a href='index.php?a=100000' title='API Integration'>API Integration</a></div>
								<div class='doc'><a href='index.php?a=24' title='Allowed HTML'>Allowed HTML</a></div>
								<div class='doc'><a href='index.php?a=15' title='Badwords'>Badwords</a></div>
								<div class='doc'><a href='index.php?a=80' title='IP Banning'>IP Banning</a></div>								
								<div class='doc'><a href='index.php?a=68' title='Filters'>Filters</a></div>";
			//STOREFRONT CODE
			if(file_exists('storefront/admin_store.php'))
			{
				 $this->header_html .= "<div class='doc'><a href='index.php?a=201' title='Storefront'>Storefront</a></div>";
			}
			//END STOREFRONT CODE
				$this->header_html .= "</div>
							<div class='folder'>Registration Setup
								<div class='doc'><a href='index.php?a=26&z=1' title='General Settings'>General Settings</a></div>
								<div class='doc'><a href='index.php?a=26&z=2' title='Block Email Domains'>Block Email Domains</a></div>
								<div class='doc'><a href='index.php?a=26&b=1&z=4' title='Registrations Awaiting Approval'>Unapproved Registrations</a></div>
								<div class='doc'><a href='index.php?a=67' title='Registration Pre-Valued Dropdowns'>Reg. Pre-Valued Dropdowns</a></div>
							</div>
							<div class='folder'>Listing Setup
								<div class='doc'><a href='index.php?a=23&r=22' title='General Settings'>General Settings</a></div>
								<div class='doc'><a href='index.php?a=23&r=4' title='Fields to Use'>Fields to Use</a></div>
								<div class='doc'><a href='index.php?a=23&r=6' title='Listing Extras'>Listing Extras</a></div>
								<div class='doc'><a href='index.php?a=66' title='Attention Getters'>Attention Getters</a></div>";
	if ($this->is_class_auctions() || $this->is_auctions())
	{
		$this->header_html .= "
								<div class=\"doc\"><a href=\"index.php?a=23&r=20\" title=\"Bid Increments\">Bid Increments</a></div>
								<div class=\"doc\"><a href=\"index.php?a=23&r=21\" title=\"Buyer/Seller Payment Types\">Payment Types</a></div>
							  ";
	}
		$this->header_html .= "	<div class='doc'><a href='index.php?a=23&r=2' title='Listing Lengths'>Listing Lengths</a></div>
								<div class='doc'><a href='index.php?a=23&r=3' title='Allowed Uploads'>Allowed Uploads</a></div>
								<div class='doc'><a href='index.php?a=23&r=1' title='Photo Upload Settings'>Photo Upload Settings</a></div>
								<div class='doc'><a href='index.php?a=23&r=14' title='Currency Types'>Currency Types</a></div>
								<div class='doc'><a href='index.php?a=23&r=11' title='Signs'>Signs Setup</a></div>
								<div class='doc'><a href='index.php?a=23&r=10' title='Flyers'>Flyers Setup</a></div>
								<div class='doc'><a href='index.php?a=32&e=0' title='Pre-Valued Dropdowns'>Pre-Valued Dropdowns</a></div>
							</div>";

	if ($this->is_class_auctions() || $this->is_auctions())
	{
		$this->header_html .= "
							<div class='folder'>Feedback
									<div class='doc'><a href='index.php?a=110' title='Feedback Management'>Feedback Management</a></div>
								</div>";
	}
		$this->header_html .= "
							<div class='folder'>Categories
									<div class='doc'><a href='index.php?a=7' title='Categories Setup'>Categories Setup</a></div>
								<div class='doc'><a href='index.php?a=32&e=0' title='Pre-Valued Dropdowns'>Pre-Valued Dropdowns</a></div>
								</div>
								<div class='folder'>Geographic Setup
									<div class='doc'><a href='index.php?a=21' title='States / Provinces'>States / Provinces</a></div>
									<div class='doc'><a href='index.php?a=22' title='Countries'>Countries</a></div>
								</div>
								<div class='folder'>Users / User Groups
                                	<div class='doc'><a href='index.php?a=303' title='List of registered Users'>List of registered Users</a></div>
                                	<div class='doc'><a href='index.php?a=1616' title='User Emails'>User Emails</a></div>
									<div class='doc'><a href='index.php?a=36' title='User Groups Home'>User Groups Home</a></div>
									<div class='doc'><a href='index.php?a=36&b=1' title='Add New User Group'>Add New User Group</a></div>
									<div class='doc'><a href='index.php?a=19' title='List Users'>List Users</a></div>
									<div class='doc'><a href='index.php?a=16' title='Search Users'>Search Users</a></div>
								</div>
                                <div class='folder'>Listings Admin
                                	<div class='doc'><a href=index.php?a=304&b=1 title='Auction Close Update'>Auction Close Update</a></div>
                                	<div class='doc'><a href=video-csv.php title='Video merical CSV Exports'>Video merical CSV Export</a></div>
                                    <div class='doc'><a href=index.php?a=302&b=1 title='Convert classifieds to auctions'>Convert classifieds to auctions</a></div>
                                    <div class='doc'><a href=index.php?a=302&b=3 title='Convert auctions to classifieds'>Convert auctions to classifieds</a></div>
                                    <div class='doc'><a href=index.php?a=302&b=6 title='Delete inventory'>Delete inventory</a></div>
                                    <div class='doc'><a href=index.php?a=302&b=8
									title='Hide no-image inventory'>Hide
									no-image inventory</a></div>
                                </div>
								<div class='folder'>Pricing
									<div class='doc'><a href='index.php?a=37' title='Price Plans Home'>Price Plans Home</a></div>
									<div class='doc'><a href='index.php?a=37&b=1' title='Add New Price Plan'>Add New Price Plan</a></div>
									<div class='folder'>Discount Codes
										<div class='doc'><a href='index.php?a=75' title='View Discount Codes'>View Discount Codes</a></div>
										<div class='doc'><a href='index.php?a=75&b=1' title='Add New Discount Code'>New Discount Code</a></div>
									</div>
								</div>
								<div class='folder'>Payments
								<div class='doc'><a href='index.php?a=39&b=1' title='Charge for Listings?'>Charge for Listings?</a></div>
								<div class='doc'><a href='index.php?a=39&b=6' title='Currency Designation'>Currency Designation</a></div>
								<div class='folder'>Payment Types
									<div class='doc'><a href='index.php?a=39&b=2' title='Payments Accepted'>Payments Accepted</a></div>
									<div class='doc'><a href='index.php?a=39&b=11' title='NOCHEX'>NOCHEX Setup</a></div>
									<div class='doc'><a href='index.php?a=39&b=3' title='PayPal Setup'>PayPal Setup</a></div>
									<div class='doc'><a href='index.php?a=39&b=10' title='Site Balance Setup'>Site Balance Setup</a></div>
									<div class='folder'>Credit Card Setup
										<div class='doc'><a href='index.php?a=39&b=4' title='Credit Card Home'>Credit Card Home</a></div>
										<div class='doc'><a href='index.php?a=39&b=8' title='WorldPay'>WorldPay</a></div>
										<div class='doc'><a href='index.php?a=39&b=5&c=2' title='2Checkout'>2Checkout</a></div>
										<div class='doc'><a href='index.php?a=39&b=5&c=1' title='Authorize.net'>Authorize.net</a></div>
										<div class='doc'><a href='index.php?a=39&b=5&c=5' title='Internet Secure'>Internet Secure</a></div>
										<div class='doc'><a href='index.php?a=39&b=5&c=4' title='Linkpoint'>Linkpoint</a></div>
										<div class='doc'><a href='index.php?a=39&b=5&c=6' title='Payflow Pro'>Payflow Pro</a></div>
										<div class='doc'><a href='index.php?a=39&b=5&c=7' title='PayPal Pro'>PayPal Pro</a></div>
										<div class='doc'><a href='index.php?a=39&b=5' title='Manual Processing'>Manual Processing</a></div>
									</div>
								</div>
							</div>
							<div class='folder'>Transactions
								<div class='doc'><a href='index.php?a=40' title='Search Transactions'>Search Transactions</a></div>
								<div class='doc'><a href='index.php?a=40&z=1' title='Awaiting Approval'>Awaiting Approval</a></div>
								<div class='folder'>Invoice System
									<div class='doc'><a href='index.php?a=78' title='Create Invoices for all Users'>Invoice Home</a></div>
									<div class='doc'><a href='index.php?a=78&z=7' title='List Unpaid Invoices'>Unpaid Invoices</a></div>
									<div class='doc'><a href='index.php?a=78&z=6' title='List Paid Invoices'>Paid Invoices</a></div>
								</div>
							</div>
							<div class='folder'>Pages Management
								<div class='folder'>Sections
									<div class='doc'><a href='index.php?a=44' title='Pages Home'>Pages Home</a></div>
									<div class='doc'><a href='index.php?a=44&z=1&b=1' title='Browsing Listings'>Browsing Listings</a></div>
									<div class='doc'><a href='index.php?a=44&z=1&b=2' title='Listing Process'>Listing Process</a></div>
									<div class='doc'><a href='index.php?a=44&z=1&b=3' title='Registration Pages'>Registration</a></div>
									<div class='doc'><a href='index.php?a=44&z=1&b=4' title='User Management Pages'>User Management</a></div>
									<div class='doc'><a href='index.php?a=44&z=1&b=5' title='Login and Languages Pages'>Login and Languages</a></div>
									<div class='doc'><a href='index.php?a=44&z=1&b=12' title='Extra Pages'>Extra Pages</a></div>";
	if ($this->is_class_auctions() || $this->is_auctions())
	{
		$this->header_html .= "
									<div class=\"doc\"><a href=\"index.php?a=44&z=1&b=14\" title=\"Bidding\">Bidding</a></div>";
	}
	$this->header_html .= "
									<div class='doc'><a href='index.php?a=109' title='Text Search'>Text Search</a></div>
								</div>
							</div>
								<div class='folder'>Page Modules
									<div class='folder'>View Modules
										<div class='doc'><a href='index.php?a=79' title='Modules Home'>Modules Home</a></div>
										<div class='doc'><a href='index.php?a=79&b=1' title='Browsing'>Browsing</a></div>
										<div class='doc'><a href='index.php?a=79&b=2' title='Featured'>Featured</a></div>
										<div class='doc'><a href='index.php?a=79&b=3' title='Newest'>Newest</a></div>
										<div class='doc'><a href='index.php?a=79&b=4' title='HTML'>HTML</a></div>
										<div class='doc'><a href='index.php?a=79&b=5' title='PHP'>PHP</a></div>
										<div class='doc'><a href='index.php?a=79&b=6' title='Miscllaneous'>Misc.</a></div>
										<div class='doc'><a href='index.php?a=79&b=7' title='Miscellaneous Display'>Misc. Display</a></div>
									</div>
								</div>
								<div class='folder'>Templates
									<div class='doc'><a href='index.php?a=45' title='Templates Home'>Templates Home</a></div>
									<div class='doc'><a href='index.php?a=45&z=5' title='Add New Template'>Add New Template</a></div>
									<div class='doc'><a href='index.php?a=109' title='Text Search'>Text Search</a></div>
								</div>
								<div class='folder'>Languages
									<div class='doc'><a href='index.php?a=30' title='Languages Home'>Languages Home</a></div>
									<div class='doc'><a href='index.php?a=30&z=1' title='Add New Language'>Add New Language</a></div>
								</div>
								<div class='folder'>Admin Tools & Settings
									<div class='folder'>Messaging
										<div class='doc'><a href='index.php?a=25&x=1' title='Send Message'>Send Message</a></div>
										<div class='doc'><a href='index.php?a=25&x=2' title='Form Messages'>Form Messages</a></div>
										<div class='doc'><a href='index.php?a=25&x=3' title='Message History'>Message History</a></div>
									</div>
									<div class='folder'>Global CSS Mgmt
										<div class='doc'><a href='index.php?a=108' title='View All CSS Tags'>View All CSS Tags</a></div>
										<div class='doc'><a href='index.php?a=108&b=5' title='Global CSS Fonts'>Global CSS Fonts</a></div>
										<div class='doc'><a href='index.php?a=108&b=6' title='Global CSS Colors'>Global CSS Colors</a></div>
									</div>
									<div class='doc'><a href='index.php?a=35' title='Database Backup'>Database Backup</a></div>
									<div class='doc'><a href='index.php?a=51' title='Change Password'>Change Password</a></div>
                                    <div class='doc'><a href='index.php?a=300' title='Reports'>Reports</a></div>
								</div>
							</div>
						</div>
					</div>

					<script type='text/javascript'>
					var tree = new DynamicTree('tree');
					tree.init();
					</script>

					<br>
				</td>
			</tr>
		</table>";
}
// Border

if ($auth && $switch == 45 && $second_switch == 2)
	$this->header_html .= "
			</td>
			<td valign=top colspan=2>";
elseif ($auth)
{

	$this->header_html .= "
		<tr>
			<td width=150 valign=top></td>
			<td valign=top width=610>";
}
else
	$this->header_html .= "
			</td>
			<td valign=top colspan=2>";
?>