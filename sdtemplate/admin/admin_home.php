<?
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
\**************************************************************************/

$this->header_html .= "<table width=\"760\" border=\"0\" cellspacing=\"10\" cellpadding=\"0\" align=center>\n";
$this->header_html .= "<tr valign=\"top\" align=\"center\"> \n";
$this->header_html .= "<td> \n";
$this->header_html .= "<table width=\"760\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"9\" height=\"9\" valign=\"top\"><img src=\"admin_images/stats_box_ul.gif\" width=\"9\" height=\"9\"></td>\n";
$this->header_html .= "<td valign=\"top\" background=\"admin_images/stats_box_top.gif\" height=\"9\"><img src=\"admin_images/stats_box_top.gif\" width=\"6\" height=\"9\"></td>\n";
$this->header_html .= "<td width=\"9\" height=\"9\" valign=\"top\" align=\"right\"><img src=\"admin_images/stats_box_ur.gif\" width=\"9\" height=\"9\"></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"9\" background=\"admin_images/stats_box_lft.gif\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"top\" bordercolor=\"#FFFFFF\"> \n";
$this->header_html .= "<table width=\"742\" border=\"2\" cellspacing=\"0\" cellpadding=\"0\" bordercolor=\"#FFFFFF\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"150\" align=\"center\" valign=\"top\"> \n";
$this->header_html .= "<table width=\"150\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr bgcolor=\"#CC99CC\"> \n";
$this->header_html .= "<td class=\"stats_txt1\" height=\"15\"> \n";
$this->header_html .= "<div align=\"center\">Site Stats</div>\n";
$this->header_html .= "</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "<tr align=\"center\" valign=\"top\"> \n";
$this->header_html .= "<td> \n";
$this->header_html .= "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\" bordercolor=\"#FFFFFF\" bgcolor=\"#FAF5FA\">\n";
$this->header_html .= "<tr> \n";

// General Site Stats
$sql_query = "select count(*) as total_users from geodesic_logins where id != 1";
$result = $db->Execute($sql_query);
if (!$result)
{
	$this->site_error($db->ErrorMsg());
	return false;
}
else
	$show_stats = $result->FetchRow();
$this->header_html .= "<td class=\"stats_txt2\">Registered Users:</td>\n";
$this->header_html .= "<td class=\"stats_txt3\">".$show_stats['total_users']."</td>\n";
$this->header_html .= "</tr>\n";

$sql_query = "select count(*) as total_users from geodesic_userdata where id != 1 and date_joined > ".($this->shifted_time() - 86400);
$result = $db->Execute($sql_query);
if (!$result)
{
	$this->site_error($db->ErrorMsg());
	return false;
}
else
	$show_stats = $result->FetchRow();
$this->header_html .= "<tr>\n";
$this->header_html .= "<td class=\"stats_txt2\">in Last 24 hours:</td>\n";
$this->header_html .= "<td class=\"stats_txt3\">".$show_stats['total_users']."</td>\n";
$this->header_html .= "</tr>\n";

$sql_query = "select count(*) as total_users from geodesic_userdata where id != 1 and date_joined > ".($this->shifted_time() - 604800);
$result = $db->Execute($sql_query);
if (!$result)
{
	$this->site_error($db->ErrorMsg());
	return false;
}
else
	$show_stats = $result->FetchRow();
$this->header_html .= "<tr>\n";
$this->header_html .= "<td class=\"stats_txt2\">in Last 7 days:</td>\n";
$this->header_html .= "<td class=\"stats_txt3\">".$show_stats['total_users']."</td>\n";
$this->header_html .= "</tr>\n";

$sql_query = "select count(*) as total_users from geodesic_userdata where id != 1 and date_joined > ".($this->shifted_time() - 2592000);
$result = $db->Execute($sql_query);
if (!$result)
{
	$this->site_error($db->ErrorMsg());
	return false;
}
else
	$show_stats = $result->FetchRow();
$this->header_html .= "<tr>\n";
$this->header_html .= "<td class=\"stats_txt2\">in Last 30 days:</td>\n";
$this->header_html .= "<td class=\"stats_txt3\">".$show_stats['total_users']."</td>\n";
$this->header_html .= "</tr>\n";

$this->header_html .= "</table>\n";
$this->header_html .= "</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "</td>\n";

// Auction Stats
if($this->is_class_auctions() || $this->is_auctions())
{
	$this->header_html .= "<td width=\"150\" valign=\"top\"> \n";
	$this->header_html .= "<table width=\"150\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	$this->header_html .= "<tr> \n";
	$this->header_html .= "<td class=\"stats_txt1\" height=\"15\" bgcolor=\"#CC3300\"> \n";
	$this->header_html .= "<div align=\"center\">Auction Stats</div>\n";
	$this->header_html .= "</td>\n";
	$this->header_html .= "</tr>\n";
	$this->header_html .= "<tr align=\"center\" valign=\"top\"> \n";
	$this->header_html .= "<td> \n";
	$this->header_html .= "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\" bordercolor=\"#FFFFFF\" bgcolor=\"#FFEEE8\">\n";
	$sql_query = "select count(*) as total_ads from ".$this->auctions_table." where live=1 and item_type=2";
	$result = $db->Execute($sql_query);
	if (!$result)
	{
		$this->site_error( $db->ErrorMsg());
		return false;
	}
	else
		$show_stats = $result->FetchRow();
	$this->header_html .= "<tr>\n";
	$this->header_html .= "<td class=\"stats_txt2\">Auction Count:</td>\n";
	$this->header_html .= "<td class=\"stats_txt3\">".$show_stats['total_ads']."</td>\n";
	$this->header_html .= "</tr>\n";

	$this->sql_query = "select count(distinct(geodesic_logins.id)) as total_users_with_ads from geodesic_logins,geodesic_classifieds where geodesic_logins.id = geodesic_classifieds.seller and geodesic_classifieds.live = 1 and item_type=2";
	$result = $db->Execute($this->sql_query);
	if (!$result)
	{
		$this->site_error($db->ErrorMsg());
		return false;
	}
	else
		$show_stats = $result->FetchRow();
	$this->header_html .= "<tr>\n";
	$this->header_html .= "<td class=\"stats_txt2\">Users with Auctions:</td>\n";
	$this->header_html .= "<td class=\"stats_txt3\">".$show_stats['total_users_with_ads']."</td>\n";
	$this->header_html .= "</tr>\n";

	$this->sql_query = "select sum(viewed) as total_viewed from ".$this->auctions_table." where live=1 and item_type=2";
	$result = $db->Execute($this->sql_query);
	if (!$result)
	{
		$this->site_error($db->ErrorMsg());
		return false;
	}
	else
		$show_stats = $result->FetchRow();
	$this->header_html .= "<tr>\n";
	$this->header_html .= "<td class=\"stats_txt2\">Viewed Count:</td>\n";
	if(!$show_stats['total_viewed'])
		$this->header_html .= "<td class=\"stats_txt3\">0</td>\n";
	else
		$this->header_html .= "<td class=\"stats_txt3\">".$show_stats['total_viewed']."</td>\n";
	$this->header_html .= "</tr>\n";

	$this->sql_query = "select count(*) as renew_unapproved from ".$this->auctions_table." where (item_type = 2) and ((live = 0 and ends > ".$this->shifted_time()." and customer_approved = 1) or (renewal_payment_expected != 0) or (live = 2))";
	$result = $db->Execute($this->sql_query);
	if (!$result)
	{
		$this->site_error($db->ErrorMsg());
		return false;
	}
	else
		$show_stats = $result->FetchRow();
	$this->header_html .= "<tr>\n";
	if($show_stats['renew_unapproved'] > 0)
		$this->header_html .= "<td><a href=\"index.php?a=40&z=1\" class=\"stats_txt3\">Awaiting Approval:</a></td>\n";
	else
		$this->header_html .= "<td class=\"stats_txt2\">Awating Approval:</td>\n";
	$this->header_html .= "<td class=\"stats_txt3\">".$show_stats['renew_unapproved']."</td>\n";
	$this->header_html .= "</tr>\n";

	$this->header_html .= "</table>\n";
	$this->header_html .= "</td>\n";
	$this->header_html .= "</tr>\n";
	$this->header_html .= "</table>\n";
	$this->header_html .= "</td>\n";
}

// Current Ad Stats
if($this->is_class_auctions() || $this->is_classifieds())
{
	$this->header_html .= "<td width=\"150\" valign=\"top\"> \n";
	$this->header_html .= "<table width=\"150\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	$this->header_html .= "<tr> \n";
	$this->header_html .= "<td class=\"stats_txt1\" height=\"15\" bgcolor=\"#CC3300\"> \n";
	$this->header_html .= "<div align=\"center\">Ad Stats</div>\n";
	$this->header_html .= "</td>\n";
	$this->header_html .= "</tr>\n";
	$this->header_html .= "<tr align=\"center\" valign=\"top\"> \n";
	$this->header_html .= "<td> \n";
	$this->header_html .= "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\" bordercolor=\"#FFFFFF\" bgcolor=\"#FFEEE8\">\n";
	$sql_query = "select count(*) as total_ads from ".$this->classifieds_table." where live=1 and item_type=1";
	$result = $db->Execute($sql_query);
	if (!$result)
	{
		$this->site_error($db->ErrorMsg());
		return false;
	}
	else
		$show_stats = $result->FetchRow();
	$this->header_html .= "<tr>\n";
	$this->header_html .= "<td class=\"stats_txt2\">Ad Count:</td>\n";
	$this->header_html .= "<td class=\"stats_txt3\">".$show_stats['total_ads']."</td>\n";
	$this->header_html .= "</tr>\n";

	$this->sql_query = "select count(distinct(geodesic_logins.id)) as total_users_with_ads from geodesic_logins,geodesic_classifieds where geodesic_logins.id = geodesic_classifieds.seller and geodesic_classifieds.live = 1 and item_type=1";
	$result = $db->Execute($this->sql_query);
	if (!$result)
	{
		$this->site_error($db->ErrorMsg());
		return false;
	}
	else
		$show_stats = $result->FetchRow();
	$this->header_html .= "<tr>\n";
	$this->header_html .= "<td class=\"stats_txt2\">Users with Ads:</td>\n";
	$this->header_html .= "<td class=\"stats_txt3\">".$show_stats['total_users_with_ads']."</td>\n";
	$this->header_html .= "</tr>\n";

	$this->sql_query = "select sum(viewed) as total_viewed from ".$this->classifieds_table." where live=1 and item_type=1";
	$result = $db->Execute($this->sql_query);
	if (!$result)
	{
		$this->site_error($db->ErrorMsg());
		return false;
	}
	else
		$show_stats = $result->FetchRow();
	$this->header_html .= "<tr>\n";
	$this->header_html .= "<td class=\"stats_txt2\">Viewed Count:</td>\n";
	if(!$show_stats['total_viewed'])
		$this->header_html .= "<td class=\"stats_txt3\">0</td>\n";
	else
		$this->header_html .= "<td class=\"stats_txt3\">".$show_stats['total_viewed']."</td>\n";
	$this->header_html .= "</tr>\n";

	$this->sql_query = "select count(*) as renew_unapproved from ".$this->classifieds_table." where (item_type = 1) and ((live = 0 and ends > ".$this->shifted_time()." and customer_approved = 1) or (renewal_payment_expected != 0) or (live = 2))";
	$result = $db->Execute($this->sql_query);
	if (!$result)
	{
		$this->site_error($db->ErrorMsg());
		return false;
	}
	else
		$show_stats = $result->FetchRow();
	$this->header_html .= "<tr>\n";
	if($show_stats['renew_unapproved'] > 0)
		$this->header_html .= "<td><a href=\"index.php?a=40&z=1\" class=\"stats_txt3\">Awaiting Approval:</a></td>\n";
	else
		$this->header_html .= "<td class=\"stats_txt2\">Awating Approval:</td>\n";
	$this->header_html .= "<td class=\"stats_txt3\">".$show_stats['renew_unapproved']."</td>\n";
	$this->header_html .= "</tr>\n";

	$this->header_html .= "</table>\n";
	$this->header_html .= "</td>\n";
	$this->header_html .= "</tr>\n";
	$this->header_html .= "</table>\n";
	$this->header_html .= "</td>\n";
}

$this->header_html .= "<td width=\"150\" valign=\"top\"> \n";
$this->header_html .= "<table width=\"150\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" height=\"100%\">\n";
$this->header_html .= "<tr bgcolor=\"#003399\"> \n";
$this->header_html .= "<td class=\"stats_txt1\"> \n";
$this->header_html .= "<div align=\"center\">User Groups</div>\n";
$this->header_html .= "</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "<tr align=\"center\" valign=\"top\"> \n";
$this->header_html .= "<td>\n";
$this->header_html .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bordercolor=\"#FFFFFF\" bgcolor=\"#F2F7FF\">\n";
$this->header_html .= "<tr valign=\"top\"> \n";
$this->header_html .= "<td height=\"88\">\n";

$this->sql_query = "SELECT * FROM geodesic_groups";
$group_result = $db->Execute($this->sql_query);
if (!$group_result)
{
	$this->header_html .= "error in getting groups<BR>\n";
	$this->site_error($db->ErrorMsg());
	return false;
}
elseif ($group_result->RecordCount() > 0)
{
	while ($show_group = $group_result->FetchRow())
	{
		$this->sql_query = "select count(*) as group_total from ".$this->user_groups_price_plans_table." where group_id = ".$show_group['group_id']." and id!=1";
		//$this->header_html .= $this->sql_query." is thequery <bR>";
		$group_count_result = $db->Execute($this->sql_query);
		if (!$group_count_result)
		{
			//$this->header_html .= $this->sql_query."<br>\n";
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($group_count_result->RecordCount() == 1)
			$show_group_count = $group_count_result->FetchRow();
		else
			return false;
		if($this->is_class_auctions() || $this->is_classifieds())
			$user_group = "<a class=\"stats_txt3\" href=\"index.php?a=36&b=4&c=".$show_group['group_id']."\">".$show_group['name']." (".$show_group_count['group_total'].")</a>";
		elseif($this->is_auctions())
			$user_group = "<a class=\"stats_txt3\" href=\"index.php?a=1036&b=4&c=".$show_group['group_id']."\">".$show_group['name']." (".$show_group_count['group_total'].")</a>";
		$this->header_html .= $user_group."<br>\n";
	}
}
$this->header_html .= "</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "</td>\n";
$this->header_html .= "<td width=\"150\" valign=\"top\"> \n";
$this->header_html .= "<table width=\"150\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" height=\"100%\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td class=\"stats_txt1\" bgcolor=\"#CC9900\"> \n";
$this->header_html .= "<div align=\"center\">Price Plans</div>\n";
$this->header_html .= "</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "<tr align=\"center\" valign=\"top\"> \n";
$this->header_html .= "<td> \n";
$this->header_html .= "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\" bordercolor=\"#FFFFFF\" bgcolor=\"#FFFAEA\" height=\"100%\">\n";
$this->header_html .= "<tr valign=\"top\"> \n";

// Price Plans
$this->header_html .= "<td class=\"stats_txt2\" height=\"88\">";

// Auction Price Plans
if($this->is_auctions() || $this->is_class_auctions())
{
	$this->sql_query = "SELECT * FROM ".$this->price_plan_table." where applies_to = 2";
	//$this->header_html .= $this->sql_query." is the query <bR>";
	$price_plan_result = $db->Execute($this->sql_query);
	if (!$price_plan_result)
	{
		$this->site_error($db->ErrorMsg());
		return false;
	}
	elseif ($price_plan_result->RecordCount() > 0)
	{
		while ($show_price_plan = $price_plan_result->FetchRow())
		{
			$this->sql_query = "select count(*) as price_plan_total from ".$this->user_groups_price_plans_table." where ";
			$this->sql_query .= "auction_price_plan_id = ".$show_price_plan['price_plan_id']." and id != 1";
			$plan_count_result = $db->Execute($this->sql_query);
			if (!$plan_count_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($plan_count_result->RecordCount() == 1)
				$show_plan_count = $plan_count_result->FetchRow();
			$this->header_html .= "<a href=\"index.php?a=37&b=3&g=".$show_price_plan['price_plan_id']."\" class=\"stats_txt3\">".$show_price_plan['name']." (".$show_plan_count['price_plan_total'].")<br>";
		}
	}
}

// Classified Price Plans
if($this->is_classifieds() || $this->is_class_auctions())
{
	$this->sql_query = "SELECT * FROM ".$this->price_plan_table." where applies_to = 1";
	//echo $this->sql_query." is the query <bR>";
	$price_plan_result = $db->Execute($this->sql_query);
	if (!$price_plan_result)
	{
		$this->site_error($db->ErrorMsg());
		return false;
	}
	elseif ($price_plan_result->RecordCount() > 0)
	{
		while ($show_price_plan = $price_plan_result->FetchRow())
		{
			$this->sql_query = "select count(*) as price_plan_total from ".$this->user_groups_price_plans_table." where ";
			$this->sql_query .= "price_plan_id = ".$show_price_plan['price_plan_id'];
			//echo $this->sql_query." is the query <bR>";
			$plan_count_result = $db->Execute($this->sql_query);
			if (!$plan_count_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($plan_count_result->RecordCount() == 1)
				$show_plan_count = $plan_count_result->FetchRow();
			$this->header_html .= "<a href=\"index.php?a=37&b=3&g=".$show_price_plan['auction_price_plan_id']."\" class=\"stats_txt3\">".$show_price_plan['name']." (".$show_plan_count['price_plan_total'].")<br>";
		}
	}
}
$this->header_html .= "</td>\n";
$this->header_html .= "</tr>\n";

$this->header_html .= "</table>\n";
$this->header_html .= "</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "</td>\n";
$this->header_html .= "<td width=\"9\" align=\"right\" background=\"admin_images/stats_box_rt.gif\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td height=\"9\" width=\"9\" valign=\"bottom\" align=\"left\"><img src=\"admin_images/stats_box_ll.gif\" width=\"9\" height=\"9\"></td>\n";
$this->header_html .= "<td valign=\"bottom\" background=\"admin_images/stats_box_bot.gif\" height=\"9\"><img src=\"admin_images/stats_box_bot.gif\" width=\"6\" height=\"9\"></td>\n";
$this->header_html .= "<td width=\"9\" height=\"9\" valign=\"bottom\" align=\"right\"><img src=\"admin_images/stats_box_lr.gif\" width=\"9\" height=\"9\"></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td height=\"8\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"760\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=center>\n";
$this->header_html .= "<tr>\n";
$this->header_html .= "<td width=\"250\" valign=\"top\" height=\"188\"> \n";
$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_siteconfig.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">Site Setup</td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=28&amp;z=6\" class=\"menu_txt2\">General Settings</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=28&amp;z=7\" class=\"menu_txt2\">Browsing Settings</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt\"><a href=\"index.php?a=100000\" class=\"menu_txt2\">API Integration</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=24\" class=\"menu_txt2\">Allowed HTML</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/menu_dot.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=15\" class=\"menu_txt2\">Badwords</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

//STOREFRONT CODE
if(file_exists('storefront/admin_store.php'))
{
	$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	$this->header_html .= "<tr> \n";
	$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
	$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/menu_dot.gif\" width=\"1\" height=\"10\"></td>\n";
	$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
	$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=68\" class=\"menu_txt2\">Filter Management</a></td>\n";
	$this->header_html .= "</tr>\n";
	$this->header_html .= "</table>\n";

	$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	$this->header_html .= "<tr> \n";
	$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
	$this->header_html .= "<td valign=\"top\" width=\"1\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
	$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
	$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=201\" class=\"menu_txt2\">Storefront Management</a></td>\n";
	$this->header_html .= "</tr>\n";
	$this->header_html .= "</table>\n";
} else {
//STOREFRONT CODE
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"top\" width=\"1\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=68\" class=\"menu_txt2\">Filter Management</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
}

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_regconfig.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">Registration Setup</td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=26&amp;z=1\" class=\"menu_txt2\">General Settings</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=26&amp;z=2\" class=\"menu_txt2\">Block Email Domains</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr>\n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=26&amp;b=1&amp;z=4\" class=\"menu_txt2\">Unapproved Registrations</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
if ($this->is_class_auctions() || $this->is_auctions())
{
	$config_title = "Listing Setup";
	$item_name = "Listing";
}
elseif ($this->is_auctions())
{
	$config_title = "Auction Setup";
	$item_name = "Auction";
}
else
{
	$config_title = "Ad Setup";
	$item_name = "Ad";
}
$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_config.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">".$config_title."</td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=23&r=22\" class=\"menu_txt2\">General Settings</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=23&amp;r=4\" class=\"menu_txt2\">Fields to Use</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=23&amp;r=6\" class=\"menu_txt2\">Listing Extras</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=66\" class=\"menu_txt2\">Attention Getters</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

if ($this->is_class_auctions() || $this->is_auctions())
{

	$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	$this->header_html .= "<tr> \n";
	$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
	$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
	$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
	$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=23&amp;r=20\" class=\"menu_txt2\">Bid Increments</a></td>\n";
	$this->header_html .= "</tr>\n";
	$this->header_html .= "</table>\n";

	$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	$this->header_html .= "<tr>\n";
	$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
	$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
	$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
	$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=23&amp;r=21\" class=\"menu_txt2\">Payment Types</a></td>\n";
	$this->header_html .= "</tr>\n";
	$this->header_html .= "</table>\n";
}

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=23&amp;r=2\" class=\"menu_txt2\">Listing Lengths</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=23&r=3\" class=\"menu_txt2\">Allowed Uploads</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=23&amp;r=1\" class=\"menu_txt2\">Photo Upload Settings</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=23&amp;r=14\" class=\"menu_txt2\">Currency Types</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=23&amp;r=11\" class=\"menu_txt2\">Signs Setup</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=23&amp;r=10\" class=\"menu_txt2\">Flyers Setup</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=32&amp;e=0\" class=\"menu_txt2\">Pre-Valued Dropdowns</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr>\n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=23&amp;r=23\" class=\"menu_txt2\">Delete Archived Listings</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

if($this->is_class_auctions() || $this->is_auctions())
{
	$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	$this->header_html .= "<tr> \n";
	$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
	$this->header_html .= "</tr>\n";
	$this->header_html .= "</table>\n";
	$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
	$this->header_html .= "<tr> \n";
	$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_feedback.gif\" width=\"47\" height=\"43\"></td>\n";
	$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">Feedback\n";
	$this->header_html .= "</td>\n";
	$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
	$this->header_html .= "</tr>\n";
	$this->header_html .= "</table>\n";
	$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	$this->header_html .= "<tr>\n";
	$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
	$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
	$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
	$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=110\" class=\"menu_txt2\">Feedback Management</a></td>\n";
	$this->header_html .= "</tr>\n";
	$this->header_html .= "</table>\n";
}

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_cats.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">Categories \n";
$this->header_html .= "</td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr>\n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=7\" class=\"menu_txt2\">Categories Setup</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr>\n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=32&amp;e=0\" class=\"menu_txt2\">Pre-Valued Dropdowns</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_geo.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">Geographic \n";
$this->header_html .= "Setup</td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=21\" class=\"menu_txt2\">States / Provinces / Regions</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=22\" class=\"menu_txt2\">Countries</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "</td>\n";
$this->header_html .= "<td width=\"250\" valign=\"top\"> \n";
$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_users.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">Users \n";
$this->header_html .= "/ User Groups</td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=303\" class=\"menu_txt2\">List of registered Users</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=1616\" class=\"menu_txt2\">User Emails</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=36\" class=\"menu_txt2\">User Groups Home</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=36&amp;b=1\" class=\"menu_txt2\">Add New User Group</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=19\" class=\"menu_txt2\">List Users</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=16\" class=\"menu_txt2\">Search Users</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

//Listings Admin Section
$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_config.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">Listings Admin</td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=304&b=1\" class=\"menu_txt2\">Auction Close Update</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"video-csv.php\" class=\"menu_txt2\">Video merical CSV Export</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=302&b=1\" class=\"menu_txt2\">Convert classifieds to auctions</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=302&b=3\" class=\"menu_txt2\">Convert auctions to classifieds</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
// delete inventory start
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=302&b=6\" class=\"menu_txt2\">Delete Inventory</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=302&b=8\" class=\"menu_txt2\">Hide No-image Inventory</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
// delete inventory end
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
//Listings Admin Section

$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_pricing.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">Pricing</td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=37\" class=\"menu_txt2\">Price Plans Home</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=37&amp;b=1\" class=\"menu_txt2\">Add New Price Plan</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\">Discount Codes</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=75\" class=\"menu_txt2\">View Discount Codes</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=75&amp;b=1\" class=\"menu_txt2\">Add New Discount Code</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_payments.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">Payments \n";
$this->header_html .= "</td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=1\" class=\"menu_txt2\">Charge for Listings?</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=6\" class=\"menu_txt2\">Currency \n";
$this->header_html .= "Designation</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\">Payment Types</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"22\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=2\" class=\"menu_txt2\">Payment \n";
$this->header_html .= "Types Accepted</a> </td>\n";
$this->header_html .= "</tr>\n";

$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"22\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=11\" class=\"menu_txt2\">NOCHEX Setup</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"22\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=3\" class=\"menu_txt2\">PayPal Setup</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"22\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=10\" class=\"menu_txt2\">Site Balance Setup</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"22\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\">Credit Card Setup</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"45\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"143\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=4\" class=\"menu_txt2\">Credit Card Home</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"45\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"143\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=8\" class=\"menu_txt2\">Worldpay.com</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"45\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"143\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=5&amp;c=2\" class=\"menu_txt2\">2Checkout.com</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"45\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"143\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=5&amp;c=1\" class=\"menu_txt2\">Authorize.net</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"45\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"143\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=5&amp;c=5\" class=\"menu_txt2\">Internet Secure</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"45\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"143\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=5&amp;c=4\" class=\"menu_txt2\">Linkpoint</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"45\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"143\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=5&amp;c=6\" class=\"menu_txt2\">Payflow Pro</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"45\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"143\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=5&amp;c=7\" class=\"menu_txt2\">PayPal Pro</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"45\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"143\" class=\"menu_txt2\"><a href=\"index.php?a=39&amp;b=5\" class=\"menu_txt2\">Manual Processing</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_trans.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">Transactions \n";
$this->header_html .= "</td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=40\" class=\"menu_txt2\">Search Transactions</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=40&amp;z=1\" class=\"menu_txt2\">Awaiting Approval</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr>\n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=77\" class=\"menu_txt2\">Final Fee Transactions</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a class=\"menu_txt2\" href=\"index.php?a=78\">Invoice System</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=78&amp;z=3\" class=\"menu_txt2\">Create Invoices</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=78&amp;z=7\" class=\"menu_txt2\">Unpaid Invoices</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=78&amp;z=6\" class=\"menu_txt2\">Paid Invoices</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "\n";
$this->header_html .= "</td>\n";
$this->header_html .= "<td width=\"260\" valign=\"top\" height=\"188\"> \n";

$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_pages.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">";
$this->header_html .= " Pages Management</td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=44\" class=\"menu_txt2\">Sections</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=44\" class=\"menu_txt2\">Pages Home</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=44&amp;z=1&amp;b=1\" class=\"menu_txt2\">Browsing Listings</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=44&amp;z=1&amp;b=2\" class=\"menu_txt2\">Listing Process</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=44&amp;z=1&amp;b=3\" class=\"menu_txt2\">Registration</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=44&amp;z=1&amp;b=4";
$this->header_html .= "\" class=\"menu_txt2\">User Management</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=44&amp;z=1&amp;b=5\" class=\"menu_txt2\">Login and Languages</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=44&amp;z=1&amp;b=12\" class=\"menu_txt2\">Extra Pages</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
if ($this->is_class_auctions() || $this->is_auctions())
{
	$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	$this->header_html .= "<tr> \n";
	$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
	$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
	$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
	$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=44&z=1&b=14\" class=\"menu_txt2\">Bidding</a></td>\n";
	$this->header_html .= "</tr>\n";
	$this->header_html .= "</table>\n";
}
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=109\" class=\"menu_txt2\">Text Search</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_pagemod.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\"> Page Modules</td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\">Module Types</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=79\" class=\"menu_txt2\">Modules Home </td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=79&amp;b=1\" class=\"menu_txt2\">Browsing </td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=79&amp;b=2\" class=\"menu_txt2\">Featured</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=79&amp;b=3\" class=\"menu_txt2\">Newest</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=79&amp;b=4\" class=\"menu_txt2\">HTML \n";
$this->header_html .= "</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=79&amp;b=5\" class=\"menu_txt2\">PHP \n";
$this->header_html .= "</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=79&amp;b=6\" class=\"menu_txt2\">Miscellaneous</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"43\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=79&amp;b=7\" class=\"menu_txt2\">Misc. Display</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_temp.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">Templates </td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=45\" class=\"menu_txt2\">Templates Home</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=45&amp;z=5\" class=\"menu_txt2\">Add New Template</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=109\" class=\"menu_txt2\">Text Search</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_lang.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">Languages \n";
$this->header_html .= "</td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=30\" class=\"menu_txt2\">Languages Home</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=30&amp;z=1\" class=\"menu_txt2\">Add New Language</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"230\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"245\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"admin_images/admin_front_menu_btn.jpg\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"47\" valign=\"top\"><img src=\"admin_images/menu_admintools.gif\" width=\"47\" height=\"43\"></td>\n";
$this->header_html .= "<td width=\"181\" valign=\"middle\" class=\"menu_txt\">Admin Tools &amp; Settings</td>\n";
$this->header_html .= "<td width=\"12\" valign=\"top\">&nbsp;</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\">Messaging </td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr>\n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td> \n";
$this->header_html .= "<td width=\"22\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=25&amp;x=1\" class=\"menu_txt2\">Send \n";
$this->header_html .= "Message</a> </td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"22\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=25&amp;x=2\" class=\"menu_txt2\">Form Messages</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"22\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=25&amp;x=3\" class=\"menu_txt2\">Message History</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr>\n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\">Global CSS Management</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"22\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=108\" class=\"menu_txt2\">View All CSS Tags</a> </td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"22\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=108&amp;b=5\" class=\"menu_txt2\">Global CSS Fonts</a> </td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr>\n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"22\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=108&amp;b=6\" class=\"menu_txt2\">Global CSS Colors</a> </td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=35\" class=\"menu_txt2\">Database Backup </a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

if(is_dir("bulk_uploader"))
{
	$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	$this->header_html .= "<tr> \n";
	$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
	$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
	$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
	$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=200\" class=\"menu_txt2\">Bulk Uploader</a></td>\n";
	$this->header_html .= "</tr>\n";
	$this->header_html .= "</table>\n";
}

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\"><a href=\"index.php?a=51\" class=\"menu_txt2\">Change Password</a></td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr>\n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"189\" class=\"menu_txt2\">Reports</td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"22\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\" background=\"admin_images/menu_dot.jpg\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=300\" class=\"menu_txt2\">Auction reports</a> </td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";
$this->header_html .= "<table width=\"240\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$this->header_html .= "<tr> \n";
$this->header_html .= "<td width=\"20\">&nbsp;</td>\n";
$this->header_html .= "<td valign=\"middle\" width=\"1\"><img src=\"admin_images/shim1x1.gif\" width=\"1\" height=\"1\"></td>\n";
$this->header_html .= "<td width=\"22\">&nbsp;</td>\n";
$this->header_html .= "<td width=\"1\" valign=\"top\"><img src=\"admin_images/menu_dot_end.gif\" width=\"1\" height=\"10\"></td>\n";$this->header_html .= "<td width=\"30\"><img src=\"admin_images/menu_bullet.jpg\" width=\"30\" height=\"13\"></td>\n";
$this->header_html .= "<td width=\"166\" class=\"menu_txt2\"><a href=\"index.php?a=301\" class=\"menu_txt2\">Classified reports</a> </td>\n";
$this->header_html .= "</tr>\n";
$this->header_html .= "</table>\n";

$this->header_html .= "<br></td>\n";
$this->header_html .= "</tr></table>\n";
$this->header_html .= "</td> \n";
$this->header_html .= "</tr>\n";

// Build footer
$this->admin_footer($db);
?>