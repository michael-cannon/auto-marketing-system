<?//module_title_auctions.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$this->body = "";
$this->get_css($db,$show_module['page_id']);
$this->get_text($db,$show_module['page_id']);

$page_debug = 0;

if($page_debug) echo "Page_id is ".$this->page_id."<hr size=1 color=#ff9900>";

if($result)
{
	if($this->page_id == 1 || $this->page_id == 84)
	{
    	if($this->page_id == 84) $this->ad_id = $_REQUEST["b"];
        $this->sql_query = "select title_module_text from geodesic_classifieds_ad_configuration";
        $title_result = $db->Execute($this->sql_query);
        if($title_result)
        {
            $page_result = $title_result->FetchRow();
            if (strlen(trim($page_result)) > 0)
                $this->body = stripslashes(urldecode($page_result['title_module_text']));
            else
                $this->body = "";
        }

		$ad = $this->get_classified_data($db, $this->ad_id);
		$this->body  .= stripslashes(urldecode($ad->TITLE));
	}
	elseif($this->page_id == 2)
	{
		$this->body = stripslashes(urldecode($this->messages[2462]));
	}
	elseif($this->page_id == 3 && $_REQUEST['b'] != 'ViewAll')
	{
		$name = $this->get_category_name($db, $this->site_category);

		$this->body = stripslashes(urldecode($name->CATEGORY_NAME));
	}
	else
	{
		$this->sql_query = "select title_module_text from geodesic_classifieds_ad_configuration";
		$title_result = $db->Execute($this->sql_query);
		if($title_result)
		{
			$page_result = $title_result->FetchRow();
			if (strlen(trim($page_result)) > 0)
				$this->body = stripslashes(urldecode($page_result['title_module_text']));
			else
				$this->body = "";
		}
	}

    if($_REQUEST['a'] == 4 && $_REQUEST['b'] == 10) $this->body .= " Favorites";
    elseif($_REQUEST['a'] == 4) $this->body .= " My Account";
    elseif($_REQUEST['a'] == 28 && $_REQUEST['b'] == 136) $this->body .= " Contact Us";
    elseif($_REQUEST['a'] == 28	&& $_REQUEST['b'] == 141) $this->body .= " Help";
    elseif($_REQUEST['a'] == 28 && $_REQUEST['b'] == 142) $this->body .= " About Us";
    elseif($_REQUEST['a'] == 1031) $this->body .= " Bid history";
    elseif($_REQUEST['b'] == 'ViewAll') $this->body .= " View All listings";
    elseif($_REQUEST['a'] == 19) $this->body .= " Advanced Search";
    elseif($_REQUEST['a'] == 28	&& $_REQUEST['b'] == 145) $this->body .= " Privacy Policy";
    elseif($_REQUEST['a'] == 28 && $_REQUEST['b'] == 140) $this->body .= " Terms & Conditions";
    elseif($_REQUEST['a'] == 28 && $_REQUEST['b'] == 139) $this->body .= " Buyer Features";
    elseif($_REQUEST['a'] == 10) $this->body .= " Log In";
    //elseif($_REQUEST['a'] == 14) $this->body .= " Printer friendly";
	elseif($_REQUEST['a'] == 12) $this->body .= " Tell a friend";
    elseif($_REQUEST['a'] == 20) $this->body .= " Add to favorites";
    elseif($_REQUEST['a'] == 13) $this->body .= " Contact Seller";
    elseif($_REQUEST['a'] == 1029) $this->body .= " Bid Submission";
    elseif($_REQUEST['b'] == 1013) $this->body .= " Seller's other listings";
}
?>