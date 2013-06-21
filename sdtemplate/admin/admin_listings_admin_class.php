<?php
class Admin_listings_admin extends Admin_site
{
    function Admin_listings_admin($db, $product_configuration=0)
    {
        //constructor
        $this->Admin_site($db, $product_configuration);

        // Set the admin_icon variable for the admin icon
        $this->admin_icon = "admin_images/menu_storefront.gif";
    }

	function show_classifieds_to_auctions($db)
    {
        $this->title = 'Listings admin';
        $this->description = 'Convert user classifieds to auctions.';

        $sold = $_REQUEST['sold'];
        $amount = ($_REQUEST['amount']) ? $_REQUEST['amount']:6000;

        $query =
        "SELECT * FROM geodesic_classifieds
        WHERE live=1 AND item_type=1";

        $sqlresult = $db->Execute($query);
        if($sqlresult)
        {
        	$count = $sqlresult->RecordCount();

            if($count)
        	{
                $this->body .= "<center><font class=\"menu_txt2\"><strong>Classifieds: [".$count."] <br><br><hr size=1 width=300 align=center color=#ff9900></strong></font></center>\n";

            	$this->body .= "<p><p><hr size=1 color=#ff9900 align=center width=100%>
            	<form name=sold action=\"index.php?a=302&b=2\" method=post>
            	<table cellpadding=5 cellspacing=0>
            	<tr>
            	<td width=50%><strong><span class=medium_font><font color=000000>Sold vehicles: </span></strong></td>
            	<td width=50%><textarea name=sold rows=10 cols=30>".$sold."</textarea></td>
            	</tr>
            	<tr>
            	<td><strong><span class=medium_font><font color=000000>Starting Bid and Reserve Price Difference (Ex: 6000): </span></strong></td>
            	<td><input name=amount value=\"".$amount."\"></td>
            	</tr>
            	<tr>
            	<td><strong><span class=medium_font><font color=000000>Convert classifieds to auctions: </span></strong></td>
            	<td><input type=submit value=\">> Go >>\" style=\"border:1px solid #454545 bgcolor=#fffaf5\"></td>
            	</tr>
            	</table>
            	</form>";
            }
        	else
        	{
            	$this->body .= "<center><font class=\"menu_txt2\"><br><br><hr size=1 width=300 align=center color=#ff9900><strong>There are no classifieds for converting to to auctions.</strong></font></center>\n";
        	}

            return(1);
		}
        else
        {
        	return(0);
        }
    }

    function show_auctions_to_classifieds($db)
    {
        $this->title = 'Listings admin';
        $this->description = 'Convert user auctions to classifieds.';

    	$query =
        "SELECT * FROM geodesic_classifieds
        WHERE live=1 AND item_type=2";

        $sqlresult = $db->Execute($query);
        if($sqlresult)
        {
            $count = $sqlresult->RecordCount();
        }

        $query =
        "SELECT * FROM geodesic_classifieds
        WHERE live=1 AND item_type=2 AND high_bidder = 0";

        $sqlresult = $db->Execute($query);
        if($sqlresult)
        {
            $countNoBid = $sqlresult->RecordCount();

        	if($countNoBid)
        	{
        		$this->body .= "<center><font class=\"menu_txt2\"><strong>Auctions: [".$count."] <br>Auctions without bid history: [".$countNoBid."] <br><a href=\"index.php?a=302&b=4\" class=\"menu_txt2\">Convert auctions to classifieds</a><strong></font></center>\n";
        	}
        	else
        	{
        		$this->body .= "<center><font class=\"menu_txt2\"><strong>Auctions: [".$count."] <br>Auctions without bid history: [".$countNoBid."] <br><br><hr size=1 width=300 align=center color=#ff9900><strong>There are no auctions for converting to classifieds.</strong></font></center>\n";
        	}
        }
        else
        {
        	return(0);
        }
        return(1);
    }

    function classifieds_to_auctions($db)
    {
		global $cpPriceDelta;

        $this->title = 'Listings admin';
        $this->description = 'Convert user classifieds to auctions.';

        $sold = $_REQUEST['sold'];
		$user_id = $_REQUEST['user_id']
			? ' AND id=' . $_REQUEST['user_id']
			: '';
        $amount = ($_REQUEST['amount']) ? $_REQUEST['amount']:6000;

        $j = 0;
        $vins = explode(",", $sold);
        for($i = 0; $i < count($vins); $i ++)
        {
        	$vins[$i] = trim($vins[$i]);
            if(!empty($vins[$i]))
            {
            	$query = "SELECT id, category FROM geodesic_classifieds
                     	WHERE optional_field_16 = '".trim($vins[$i])."'
						$user_id
						AND item_type = 1 AND live=1 LIMIT 1";

            	$result = $db->Execute($query);
            	if($result)
            	{
                	$recordCount = $result->RecordCount();
                	$catresult = $result->FetchNextObject();
                	$category = $catresult->CATEGORY;
            	}
            	else return(0);

            	if($recordCount)
            	{
                	$query = "UPDATE geodesic_classifieds
                         	SET live = 0
                         	WHERE optional_field_16 = '".trim($vins[$i])."'
							$user_id
							AND item_type = 1 LIMIT 1";

                	$result = $db->Execute($query);
                	if($result)
                	{
                    	$this->body .= "<span class=medium_font><font color=000000>Item (VIN: <strong>".trim($vins[$i])."</strong>) has been updated: live = 0</span><br><br>";
                    	$j ++;
                	}
                	else return(0);
            	}
            	else $this->body .= "There are no classifieds with VIN: ".trim($vins[$i]).'<br>';

//
            	if($category)
            	{
                	$parent = 0;
                	$cats = Array();
                	$k = 0;
                	$cats[$k] = $category;
                	$k ++;

                	$cat_query = "SELECT parent_id FROM geodesic_categories WHERE category_id = ".$category;
                	$cat_result = $db->Execute($cat_query);
                	if($cat_result)
                	{
                    	if($cat_result->RecordCount() == 1)
                    	{
                        	$cat = $cat_result->FetchNextObject();
                        	$parent = $cat->PARENT_ID;
                        	if($parent) $cats[$k] = $parent;
                        	$k ++;
                    	}
                	}

                	while($parent != 0)
                	{
                    	$cat_query = "SELECT parent_id FROM geodesic_categories WHERE category_id = ".$parent;
                    	$cat_result = $db->Execute($cat_query);
                    	if($cat_result)
                    	{
                        	if($cat_result->RecordCount() == 1)
                        	{
                            	$cat = $cat_result->FetchNextObject();
                            	$parent = $cat->PARENT_ID;
                            	if($parent) $cats[$k] = $parent;
                            	if($geobu_debug) echo "Parent $j: $parent<br>";
                            	$k ++;
                        	}
                    	}
                	}
                	if(count($cats) > 0)
                	{
                    	foreach($cats as $key => $value)
                    	{
                        	$cat_query = "UPDATE geodesic_categories SET category_count = category_count - 1 WHERE category_id=".$value." LIMIT 1";
                        	$cat_result = $db->Execute($cat_query);
                        	if(!$cat_result)
                        	{
                            	return false;
                        	}
                    	}
                	}
            	}
        	}
		}
        unset($vins);

		// turn off cheapo auctions while they're classifieds
		$query = "
			UPDATE geodesic_classifieds
			SET live = 0
			WHERE (minimum_bid <= $cpPriceDelta
					OR starting_bid <= $cpPriceDelta
					OR reserve_price <= $cpPriceDelta
				)
				AND item_type = 1
				AND live = 1
				$user_id";

		$this->body .= "<center>$query</center>\n";
		$resultSubtraction = $db->Execute($query);
		if(!$resultSubtraction)
		{
			$this->body .= "<center><font class=\"menu_txt2\">Can't convert classifieds to auctions. 6</center>\n";
			$this->body .= "<center>$query</center>\n";
			return(0);
		}

        if($amount)
        {
        	$query = "UPDATE geodesic_classifieds
                  	SET minimum_bid = reserve_price - $amount,
                  	current_bid = reserve_price - $amount,
                  	starting_bid =  reserve_price - $amount
                  	WHERE reserve_price > $amount
					$user_id
                  	AND item_type = 1 AND live = 1";

        	$resultSubtraction = $db->Execute($query);
        	if(!$resultSubtraction)
        	{
        		$this->body .= "<center><font class=\"menu_txt2\">Can't convert classifieds to auctions. 4</center>\n";
            	return(0);
        	}

        	$query = "UPDATE geodesic_classifieds
                  	SET current_bid = minimum_bid
                  	WHERE reserve_price <= $amount
					$user_id
                  	AND item_type = 1 AND live = 1";

        	$resultSubtraction = $db->Execute($query);
        	if(!$resultSubtraction)
        	{
            	$this->body .= "<center><font class=\"menu_txt2\">Can't convert classifieds to auctions. 5</center>\n";
            	return(0);
        	}
        }

        $query = "SELECT * FROM geodesic_classifieds WHERE live=1 $user_id AND item_type=1";
        $resultClassifieds = $db->Execute($query);

        if($resultClassifieds)
        {
        	while($res = $resultClassifieds->FetchNextObject())
            {
            	$minimum_bid = $res->MINIMUM_BID;
            	$starting_bid = $res->STARTING_BID;
            	$auction_type = $res->AUCTION_TYPE;
            	$id = $res->ID;

            	$queryStr = '';

                /*
            	if(empty($minimum_bid) && empty($starting_bid))
            	{
            		$queryStr .= ', minimum_bid=1.00';
                	$queryStr .= ', starting_bid=1.00';
            	}
				elseif(empty($minimum_bid) && !empty($starting_bid))
            	{
                    $reserve_price = $starting_bid + 1495;
                    $queryStr .= ', minimum_bid=\''.$starting_bid.'\'';
                    $queryStr .= ', reserve_price=\''.$reserve_price.'\'';
            	}
            	elseif(!empty($minimum_bid) && empty($starting_bid))
            	{
                    $reserve_price = $minimum_bid + 1495;
                	$queryStr .= ', starting_bid=\''.$minimum_bid.'\'';
                    $queryStr .= ', reserve_price=\''.$reserve_price.'\'';
            	}
                */

            	$query =
        		"UPDATE geodesic_classifieds
        		SET item_type=2,
            	auction_type=1 "
            	.$queryStr.
        		" WHERE live=1 AND item_type=1
				$user_id
				AND id=$id
            	LIMIT 1";

        		$result = $db->Execute($query);
        		//$recordCount = $result->RecordCount();

       	 		if($result)
        		{
            		$this->body .= "<center><font class=\"menu_txt2\">Classified ".$id." has been converted<br> </center></font>";
            	}
        		else
        		{
            		$this->body .= "<center><font class=\"menu_txt2\">Can't convert classifieds to auctions. 2</center>\n";
                	return(0);
                }
            }
            $this->body .= "<center><br><strong><font class=\"menu_txt2\">Classifieds have been converted to auctions.</font></strong></center><br><hr size=1 color=#0077cc align=center width=300>\n";
        }
        else
        {
            $this->body .= "<center><font class=\"menu_txt2\">Can't convert classifieds to auctions. 1</center>\n";
        }

        //Added BCS-IT, 03.05.2006
        $query = "SELECT optional_field_16 FROM geodesic_classifieds
        WHERE 1 = 1
		$user_id
        AND minimum_bid <= '1.00' AND item_type = 2 AND live=1";

        $result = $db->Execute($query);
        if(!$result)
        {
        	echo "MySql error: ".__LINE__;
            echo "Query is ".$query;
        	return(0);
        }

        $i = 0;
        while($res = $result->FetchNextObject())
        {
        	$vins[$i] = $res->OPTIONAL_FIELD_16;
            $i ++;
        }

        for($i = 0; $i < count($vins); $i ++)
        {
            $vins[$i] = trim($vins[$i]);
            if(!empty($vins[$i]))
            {
                $query = "SELECT id, category FROM geodesic_classifieds
                         WHERE optional_field_16 = '".trim($vins[$i])."'
						 $user_id
						 AND item_type = 2 AND live=1 LIMIT 1";

                $result = $db->Execute($query);
                if($result)
                {
                    $recordCount = $result->RecordCount();
                    $catresult = $result->FetchNextObject();
                    $category = $catresult->CATEGORY;
                }
                else {echo "Query is ".$query; return(0);}

                if($recordCount)
                {
                    $query = "UPDATE geodesic_classifieds
                             SET live = 0
                             WHERE optional_field_16 = '".trim($vins[$i])."'
							 $user_id
							 AND item_type = 2 LIMIT 1";

                    $result = $db->Execute($query);
                    if($result)
                    {
                        //$this->body .= "<span class=medium_font><font color=000000>Item (VIN: <strong>".trim($vins[$i])."</strong>) has been updated: live = 0</span><br><br>";
                        $j ++;
                    }
                    else {echo "Query is ".$query; return(0);}
                }
                else $this->body .= "There are no classifieds with VIN: ".trim($vins[$i]).'<br>';

//
                if($category)
                {
                    $parent = 0;
                    $cats = Array();
                    $k = 0;
                    $cats[$k] = $category;
                    $k ++;

                    $cat_query = "SELECT parent_id FROM geodesic_categories WHERE category_id = ".$category;
                    $cat_result = $db->Execute($cat_query);
                    if($cat_result)
                    {
                        if($cat_result->RecordCount() == 1)
                        {
                            $cat = $cat_result->FetchNextObject();
                            $parent = $cat->PARENT_ID;
                            if($parent) $cats[$k] = $parent;
                            $k ++;
                        }
                    }

                    while($parent != 0)
                    {
                        $cat_query = "SELECT parent_id FROM geodesic_categories WHERE category_id = ".$parent;
                        $cat_result = $db->Execute($cat_query);
                        if($cat_result)
                        {
                            if($cat_result->RecordCount() == 1)
                            {
                                $cat = $cat_result->FetchNextObject();
                                $parent = $cat->PARENT_ID;
                                if($parent) $cats[$k] = $parent;
                                if($geobu_debug) echo "Parent $j: $parent<br>";
                                $k ++;
                            }
                        }
                    }
                    if(count($cats) > 0)
                    {
                        foreach($cats as $key => $value)
                        {
                            $cat_query = "UPDATE geodesic_categories SET category_count = category_count - 1 WHERE category_id=".$value." LIMIT 1";
                            $cat_result = $db->Execute($cat_query);
                            if(!$cat_result)
                            {
                            	echo "Query is ".$cat_query;
                                return false;
                            }
                        }
                    }
                }
            }
        }

        $query = "UPDATE geodesic_classifieds
        SET live = 0
        WHERE 1 = 1
		$user_id
        AND minimum_bid <= '1.00' AND item_type = 2";

        $result = $db->Execute($query);
        if(!$result)
        {
            echo "MySql error: ".__LINE__;
            return(0);
        }

        return(1);
    }

    function auctions_to_classifieds($db)
    {
        $this->title = 'Listings admin';
        $this->description = 'Convert users auctions to classifieds.';

        $query =
        "UPDATE geodesic_classifieds
        SET item_type=1
        WHERE live=1 AND item_type=2 AND high_bidder = 0";

        $result = $db->Execute($query);
        //$recordCount = $result->RecordCount();

        if($result)
        {
            $this->body .= "<center><font class=\"menu_txt2\">Auctions have been converted to classifieds.</center>\n";
        }
        else
        {
            $this->body .= "<center><font class=\"menu_txt2\">Can't convert auctions to classifieds.</center>\n";
        }

        return(1);
    }

    function show_delete_inventory($db)
    {
        $this->title = 'Listings Admin > Delete Inventory';
        $this->description = 'Delete inventory in preparation for new sale.';

		$this->body .= "
		Delete Inventory?
			<a href='index.php?a=302&b=7'>Yes</a>
			<a href='index.php?a=302'>No</a>
		";

        return true;
    }

    function do_delete_inventory($db)
    {
        $this->title = 'Listings Admin > Delete Inventory';
        $this->description = 'Delete inventory in preparation for new sale.';
		$return					= false;

        $query					= array(
			/* clear old auctions */
			'TRUNCATE TABLE geodesic_ad_filter;'
			, 'TRUNCATE TABLE geodesic_ad_filter_categories;'
			, 'TRUNCATE TABLE geodesic_auctions_autobids;'
			, 'TRUNCATE TABLE geodesic_auctions_bids;'
			, 'TRUNCATE TABLE geodesic_auctions_feedbacks;'
			, 'TRUNCATE TABLE geodesic_categories;'
			, 'TRUNCATE TABLE geodesic_classifieds;'
			, 'TRUNCATE TABLE geodesic_classifieds_categories_languages;'
			, 'TRUNCATE TABLE geodesic_classifieds_expired;'
			, 'TRUNCATE TABLE geodesic_classifieds_images;'
			, 'TRUNCATE TABLE geodesic_classifieds_images_urls;'
			, 'TRUNCATE TABLE geodesic_classifieds_votes;'
			, 'TRUNCATE TABLE geodesic_favorites;'
			, 'TRUNCATE TABLE geodesic_storefront_subscriptions;'
			, 'TRUNCATE TABLE geodesic_user_communications;'
		);

		foreach ( $query as $key => $value )
		{
        	$result				= $db->Execute($value);
		}

        if($result)
        {
            $this->body			.= "<p>Inventory deleted</p>";
        	$return				= true;
        }
        else
        {
            $this->body			.= "<p>Inventory not deleted</p>";
        }

        $this->body				.= "
			<p><a href='index.php?a=302'>Return to Listings Admin</a></p>
		";

		return $return;
    }

    function show_missing_images($db)
    {
        $this->title = 'Listings Admin > Hide Inventory Sans Images';
        $this->description = 'Hide New Inventory Sans Images.';

		$this->body .= "
		Hide no-image inventory?
			<a href='index.php?a=302&b=9'>Yes</a>
			<a href='index.php?a=302'>No</a>
		";

        return true;
    }

    function do_missing_images($db)
    {
        $this->title = 'Listings Admin > Hide Inventory Sans Images';
        $this->description = 'Hide Inventory Sans Images.';
		$return					= false;

        $query					= "
			UPDATE geodesic_classifieds
			SET live = 0
			WHERE 1 = 1
				AND image = 0
		";

        $result					= $db->Execute($query);

		$this->body				.= "<p>Inventory without images hidden</p>";
		$this->body				.= "<p><a href=index.php?a=70>Reset all category counts</a></p>";
		$return					= true;

        $this->body				.= "
			<p><a href='index.php?a=302'>Return to Listings Admin</a></p>
		";

		return $return;
    }

    //BCS-IT 11.05.2006
    //List of vins of sold vehicles set live=0 and category_count -1
    function sold_classifieds($db)
    {
        $this->title = 'Listings admin';
        $this->description = 'Convert user classifieds to auctions.';

    	$j = 0;
		$vins = explode(",", $_REQUEST['sold']);
        for($i = 0; $i < count($vins); $i ++)
        {
            $query = "SELECT id, category FROM geodesic_classifieds
                     WHERE optional_field_16 = '".trim($vins[$i])."' AND item_type = 1 AND live=1 LIMIT 1";

            $result = $db->Execute($query);
            if($result)
            {
            	$recordCount = $result->RecordCount();
            	$catresult = $result->FetchNextObject();
            	$category = $catresult->CATEGORY;
            }
            else return(0);

            if($recordCount)
            {
            	$query = "UPDATE geodesic_classifieds
            	         SET live = 0
                         WHERE optional_field_16 = '".trim($vins[$i])."' AND item_type = 1 LIMIT 1";

            	$result = $db->Execute($query);
            	if($result)
            	{
					$this->body .= "<span class=medium_font><font color=000000>Item (VIN: <strong>".trim($vins[$i])."</strong>) has been updated: live = 0</span><br>";
            		$j ++;
            	}
                else return(0);
            }
            else $this->body .= "There are no classifieds with VIN: ".trim($vins[$i]).'<br>';

//
        	if($category)
        	{
            	$parent = 0;
            	$cats = Array();
            	$k = 0;
            	$cats[$k] = $category;
            	$k ++;

            	$cat_query = "SELECT parent_id FROM geodesic_categories WHERE category_id = ".$category;
            	$cat_result = $db->Execute($cat_query);
            	if($cat_result)
            	{
                	if($cat_result->RecordCount() == 1)
                	{
                    	$cat = $cat_result->FetchNextObject();
                    	$parent = $cat->PARENT_ID;
                    	if($parent) $cats[$k] = $parent;
                    	$k ++;
                	}
            	}

           		while($parent != 0)
            	{
                	$cat_query = "SELECT parent_id FROM geodesic_categories WHERE category_id = ".$parent;
                	$cat_result = $db->Execute($cat_query);
                	if($cat_result)
                	{
                    	if($cat_result->RecordCount() == 1)
                    	{
                    		$cat = $cat_result->FetchNextObject();
                        	$parent = $cat->PARENT_ID;
                        	if($parent) $cats[$k] = $parent;
                        	if($geobu_debug) echo "Parent $j: $parent<br>";
                        	$k ++;
                    	}
                	}
            	}
            	if(count($cats) > 0)
            	{
                	foreach($cats as $key => $value)
                	{
                    	$cat_query = "UPDATE geodesic_categories SET category_count = category_count - 1 WHERE category_id=".$value." LIMIT 1";
                    	$cat_result = $db->Execute($cat_query);
                    	if(!$cat_result)
                    	{
                        	return false;
                    	}
                	}
            	}
        	}
//
        }
        return(1);
    }

    function listings_home($db)
    {
        $this->title = 'Listings admin';
        $this->description = 'Convert users auctions to classifieds and vicy.';

        $this->body .= "<strong>Listings admin</strong><hr size=1 color=#ff9900 width = 100% align=left>";
        $this->body .= "<table width=50% align=center cellspacing=0 cellpadding=3>\n<tr>\n\t<td align=left>\n\t<ul>";
        $this->body .= "<li type=square><a href=index.php?a=302&b=2><span class=medium_font><font color=000000>Convert classifieds to auctions</span></a></li>\n";
        $this->body .= "<li type=square><a href=index.php?a=302&b=4><span class=medium_font><font color=000000>Convert auctions to classifieds</span></a></li>\n";
        $this->body .= "<li type=square><a href=index.php?a=302&b=6><span
		class=medium_font><font color=000000>Delete Inventory (Prepare for Sale)</span></a></li>\n";
        $this->body .= "</ul>\n</td\n></tr>\n</table>\n";

        return(1);
    }

    function show_new_end_date($db)
    {
        $this->title = 'Auction Close Update';
        $this->description = 'Auction Close Update.';

        $h = ($_REQUEST['h']) ? $_REQUEST['h']:10;
        $m = $_REQUEST['m'];

        $hfield = '<select name=h>';
        for($i = 0; $i <= 23; $i ++)
        {
         	if($i < 13)
                {
        	if($i == 12)
                {
                	if($i == $h) $hfield .= "<option selected value=".$i.">Noon</option>";
            		else $hfield .= "<option value=".$i.">Noon</option>";
                }
                elseif($i == 0)
                {
                        if($i == $h) $hfield .= "<option selected value=".$i.">Midnight</option>";
                        else $hfield .= "<option value=".$i.">Midnight</option>";
                }
		else
                {
                    if($i == $h) $hfield .= "<option selected value=".$i.">".$i." AM </option>";
                    else $hfield .= "<option value=".$i.">".$i." AM</option>";
                }
            }
            else
            {
               	if($i == $h) $hfield .= "<option selected value=".$i.">".($i-12)." PM</option>";
              	else $hfield .= "<option value=".$i.">".($i-12)." PM</option>";
            }
        }
        $hfield .= '</select>';

        $mfield = '<select name=m>';
        $minutes = Array('00', '15', '30', '45');
        for($i = 0; $i < count($minutes); $i ++)
        {
        	if($i == $m) $mfield .= "<option selected value=".$minutes[$i].">:".$minutes[$i]."</option>";
        	else $mfield .= "<option value=".$minutes[$i].">:".$minutes[$i]."</option>";
        }
        $mfield .= '</select>';

        $this->body = "
        <style type='text/css'>
            /* Date */
            h2 {font-family: helvetica,arial,verdana,tahoma; font-size:22px; color:#0077cc;}
            td.first {background-color:#fafafa; align:right; font-family: arial, helvetica, verdana; font-size:12px; padding: 10px;}
            td.second {background-color:#fffaf5; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 10px;}
            td.middle {background-color:#ecf4fc; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 2px;}
            td.middle1 {background-color:#ffff99; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 2px;}
            .small_text {font-family: helvetica,arial,verdana,tahoma; font-size:10px; color:#666666}
            table.show  {border:0px; bgcolor:#cccccc; background-color:#cccccc; align:center; font-family: arial, helvetica, verdana; font-size:12px; width:80%;}
            .dateBtn    {background-image:url(./images/dayselect.gif); width:34px; background-repeat:no-repeat; background-position:middle center;}
            .calFont {font-family: helvetica,arial,verdana,tahoma; font-size:12px;}
            </style>
            <script language='JavaScript' src='./calendar.js'></script>
            <script language='JavaScript'>
            function RefreshDates ()
            {
                var d = new Date();
                d.setTime(document.fls.fromMs.value);
                document.fls.DateFromValueDay.value = d.getDate();
                document.fls.DateFromValueMonth.value = d.getMonth()+1;
                document.fls.DateFromValueYear.value = d.getFullYear();
                d.setTime(document.fls.toMs.value);
                document.fls.DateToValueDay.value = d.getDate();
                document.fls.DateToValueMonth.value = d.getMonth()+1;
                document.fls.DateToValueYear.value = d.getFullYear();
                return (true);
            }
            function SetInitialDate ()
            {
                currField = document.fls.from;
                currHiddenField = document.fls.fromMs;
                setDate(d, m, y);
                wCoord = (screen.availWidth/2)-120;
                hCoord = (screen.availWidth/2)-250;
            }

        </script>
        <div id='cal1' style='position: absolute; top: 200; left: 10; z-index: 666; visibility: hidden;'></div>

        <p><p><hr size=1 color=#ff9900 align=center width=100%>
        <form name=newtime action=\"index.php?a=304&b=2\" method=post>
        <table cellpadding=5 cellspacing=0>
        <tr>
        <td><strong><span class=medium_font><font color=000000>New time: </span></strong></td>
        <td>".$hfield."</td>
        <td>".$mfield."</td>
        <td class=second><input type='button' value='&nbsp;' width='' onMouseDown=\"showCalendar(this, 'cal1', 'newAnc', 'newdatetime');\" class='dateBtn'></td>
        </tr>
		<tr>
        <td class=middle><spacer type='block' width='1' height='1' /></td>
        <td class=middle><a name='newAnc'><img src='i/d-t.gif' width='1' height='1' /></a></td>
        <td class=middle colspan=2></td>
        </tr>

        <tr>
        <td><strong><span class=medium_font><font color=000000>New date: </span></strong></td>
        <td colspan=3><input name=newdatetime value=\"\"></td>
        </tr>
        <tr>
        <td><strong><span class=medium_font><font color=000000>Set New End date and time: </span></strong></td>
        <td colspan=3><input type=submit value=\">> Go >>\" style=\"border:1px solid #454545 bgcolor=#fffaf5\"></td>
        </tr>
        </table>
        </form>";

        return true;
    }

    function process_new_end_date($db)
    {
        $this->title = 'Auction Close Update';
        $this->description = 'Auction Close Update.';

        $h = $_REQUEST['h'];
        $m = $_REQUEST['m'];
        $newdatetime = $_REQUEST['newdatetime'];

        $dat = explode(" ", $newdatetime);
        $months = Array('January' => '1', 'February' => '2', 'March' => '3', 'April' => '4', 'May' => '5', 'June' => '6', 'July' => '7', 'August' => '8', 'September' => '9', 'October' => '10', 'November' => '11', 'December' => '12');
        $month = $months[$dat[0]];
        $day = $dat[1];
        $year = $dat[2];

        $datetime = mktime($h, $m, 0, $month, $day, $year);

        $query = "UPDATE geodesic_classifieds
				 SET
                 end_time = ".$datetime." /* time view ends */
				 , ends = ".$datetime." /* time bidding ends */
				 , live = 1 /* make live */
                 WHERE live = 1";
        $result = $db->Execute($query);

        if($result)
        {
        	$this->body .= "<span class=medium_font>New End date and time (UNIX timestamp): ".$datetime."<br>";
        	$this->body .= "New End date and time: ".date("F d Y h A : i", $datetime).'</span>';
            $this->body .= "<hr size=1 color=#ff9900 width=80% align=center><br><strong><span class=medium_font>End date and time have been changed successfully.</span></strong>";
            $this->body .= '<br>'.$query;
            return(1);
		}
		else return(0);
    }
}

?>
