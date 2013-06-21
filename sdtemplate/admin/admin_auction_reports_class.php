<?php
class Admin_auction_reports extends Admin_site
{
    function Admin_auction_reports($db, $product_configuration=0)
    {
        //constructor
        $this->Admin_site($db, $product_configuration);

        // Set the admin_icon variable for the admin icon
        $this->admin_icon = "admin_images/menu_pages.gif";
    }

    function show_current_bids($db)
    {
        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, closed auctions, email report groups.';

		$query =
        "SELECT u.firstname, u.lastname, u.phone, u.email, u.city, u.state, u.zip, c.id, c.title, c.price, c.optional_field_18, c.optional_field_16, c.current_bid, c.reserve_price, c.buy_now
        FROM geodesic_classifieds c LEFT JOIN geodesic_userdata u
        ON c.high_bidder=u.id
        WHERE c.live=1 AND c.high_bidder != 0 AND c.item_type = 2
        ORDER BY c.id";

		$result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            $this->body .= "<span class=medium_font><a href=index.php?a=300&b=1&c=2>Download Current Auctions Report</a></span><p>";
        	$this->body .= '<table width = 100% cellpadding=3 cellspacing=1 class=row_color_red>';
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left colspan=14 class=medium_font_light><strong>Current Auctions Report</strong></td>";
            $this->body .= "</tr>";
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left colspan=6 class=medium_font_light><strong>Bidder contact information</strong></td>";
            $this->body .= "<td align=left colspan=4 class=medium_font_light><strong>Item details</strong></td>";
            $this->body .= "<td align=left colspan=4 class=medium_font_light><strong>Price details</strong></td>";
            $this->body .= "</tr>";
        	$this->body .= "<tr class=row_color_red height=30>";
            $this->body .= "<td align=left class=small_font_light><strong>Name</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Phone</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Email</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>City</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>State</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Zip</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Id</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Name</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Stock number</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>VIN number</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Reserve price</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Buy it now</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Current bid</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Reserve price met</strong></td>";
            $this->body .= "</tr>";

        	while ($row = $result->FetchRow())
			{
            	if($i%2) $bgcolor = '#CCCCFF';
            	else $bgcolor = 'FFFFCC';

                $username = $row['firstname'] . ' ' . $row['lastname'];
                $username = trim($username);
                if (empty($username)) $username = 'No buyer';
                $phone = $row['phone'];
                $email = $row['email'];
                $city = $row['city'];
                $state = $row['state'];
                $zip = $row['zip'];
                $id = $row['id'];
                $title = $row['title'];
                $price = $row['price'];
                $stockNumber = $row['optional_field_18'];
                $vin = $row['optional_field_16'];
                $current_bid = $row['current_bid'];
                $reserve_price = $row['reserve_price'];
                $buy_now = $row['buy_now'];
                $reserve_price_met = (($current_bid >= $reserve_price) || ($price >= $reserve_price)) ? "<font color=#FF0000><strong>X</strong></font>":'';

            	$this->body .= "<tr>\n";
            	$this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($username)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($phone)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($email)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($city)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($state)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($zip)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($id)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($title)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($stockNumber)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($vin)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($reserve_price)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($buy_now)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($current_bid)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($reserve_price_met)."</td>\n";
            	$this->body .= "</tr>\n";

            	$i ++;
        	}
            $this->body .= '</table>';
        }
        else
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no &laquo;auctions&raquo; with bids.</strong></span>\n";
        }

    	return(1);
    }

    function download_current_bids($db)
    {
        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, closed auctions, email report groups.';

        $query =
        "SELECT u.firstname, u.lastname, u.phone, u.email, u.city, u.state, u.zip, c.id, c.title, c.price, c.optional_field_18, c.optional_field_16, c.current_bid, c.reserve_price, c.buy_now
        FROM geodesic_classifieds c LEFT JOIN geodesic_userdata u
        ON c.high_bidder=u.id
        WHERE c.live=1 AND c.high_bidder != 0  AND c.item_type = 2
        ORDER BY c.id";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
			$solt = date(Ymd);
			$filename = 'ams-current-bids-' . $solt . '.csv';
            $filePath = '../scripts/' . $filename;

			if(!$file = fopen($filePath, "w"))
			{
    			Echo "<br>Can't open file $filename<br>";
    			exit;
			}

            while ($row = $result->FetchRow())
            {
                $username = urldecode($row['firstname']) . ' ' . urldecode($row['lastname']);
                $username = trim($username);
                if (empty($username)) $username = 'No buyer';
                $phone = urldecode($row['phone']);
                $email = urldecode($row['email']);
                $city = urldecode($row['city']);
                $state = urldecode($row['state']);
                $zip = urldecode($row['zip']);
                $id = $row['id'];
                $title = urldecode($row['title']);
                $price = $row['price'];
                $stockNumber = urldecode($row['optional_field_18']);
                $vin = urldecode($row['optional_field_16']);
                $current_bid = $row['current_bid'];
                $reserve_price = $row['reserve_price'];
                $buy_now = $row['buy_now'];
                $reserve_price_met = (($current_bid >= $reserve_price) || ($price >= $reserve_price)) ? "X\n":"\n";

                $text = "$username, ";
                $text .= "$phone, ";
                $text .= "$email, ";
                $text .= "$city, ";
                $text .= "$state, ";
                $text .= "$zip, ";
                $text .= "$id, ";
                $text .= "$title, ";
                $text .= "$stockNumber, ";
                $text .= "$vin, ";
                $text .= "$reserve_price, ";
                $text .= "$buy_now, ";
                $text .= "$current_bid, ";
                $text .= "$reserve_price_met\n";

                fwrite($file, $text, 1024);
            }
			fclose($file);

			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			header("Content-Disposition: attachment; filename=$filename");

			readfile($filePath);
			$pathToFile = $_SERVER["DOCUMENT_ROOT"] . '/scripts/' . $filename;
			unlink($pathToFile);
            return(1);
        }
        else
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no &laquo;auctions&raquo; with bids.</strong></span>";
        	return(0);
        }
	}

    function show_items_with_no_bids($db)
    {
        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, closed auctions, email report groups.';

        $query =
        "SELECT c.id, c.title, c.optional_field_18, c.optional_field_16
        FROM geodesic_classifieds c
        WHERE c.live=1 AND c.high_bidder=0 AND c.item_type = 2
        ORDER BY c.id";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
        	$this->body .= "<span class=medium_font><a href=index.php?a=300&b=2&c=2>Download \"No Bid Auctions\" report</a></span><p>";
        	$this->body .= "<table width = 100% cellpadding=3 cellspacing=1 bgcolor=#cccccc>";
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left colspan=5 class=medium_font_light><strong>No Bid Auctions</strong></td>";
            $this->body .= "</tr>";
            $this->body .= "<tr class=row_color_red>";
            $this->body .= "<td align=left class=small_font_light><strong>Auction Id</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Title</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Stock number</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>VIN number</strong></td>";
            $this->body .= "</tr>";

            while ($row = $result->FetchRow())
            {
                if($i%2) $bgcolor = '#CCCCFF';
                else $bgcolor = 'FFFFCC';

                $id = $row['id'];
                $title = $row['title'];
                $stockNumber = $row['optional_field_18'];
                $vin = $row['optional_field_16'];

                $this->body .= "<tr>";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($id)."</td>";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($title)."</td>";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($stockNumber)."</td>";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($vin)."</td>";
                $this->body .= "</tr>";

                $i ++;
            }
            $this->body .= '</table>';
        }
        else
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no &laquo;auctions&raquo; with no bids.</strong></span>";
        }

        return(1);
    }

    function download_items_with_no_bids($db)
    {
        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, closed auctions, email report groups.';

        $query =
        "SELECT c.id, c.title, c.optional_field_18, c.optional_field_16
        FROM geodesic_classifieds c
        WHERE c.live=1 AND c.high_bidder=0 AND c.item_type = 2
        ORDER BY c.id";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            $solt = date(Ymd);
            $filename = 'ams-items_with_no_bids-' . $solt . '.csv';
            $filePath = '../scripts/' . $filename;

            if(!$file = fopen($filePath, "w"))
            {
                Echo "<br>Can't open file $filename<br>";
                exit;
            }

            while ($row = $result->FetchRow())
            {
            	$id = $row['id'];
                $title = urldecode($row['title']);
                $stockNumber = urldecode($row['optional_field_18']);
                $vin = urldecode($row['optional_field_16']);

                $text = "$id, ";
                $text .= "$title, ";
                $text .= "$stockNumber, ";
                $text .= "$vin\n";

                fwrite($file, $text, 1024);
            }
            fclose($file);

            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-Disposition: attachment; filename=$filename");

            readfile($filePath);
            $pathToFile = $_SERVER["DOCUMENT_ROOT"] . '/scripts/' . $filename;
            unlink($pathToFile);
        	return(1);
        }
        else
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no &laquo;auctions&raquo; with no bids.</strong></span>";
        	return(0);
        }
    }

    function show_completed_auctions($db)
    {
        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, closed auctions, email report groups.';

        $query =
        "SELECT u.firstname, u.lastname, u.phone, u.email, u.city, u.state, u.zip, c.id, c.title, c.price, c.optional_field_18, c.optional_field_16, c.current_bid, c.reserve_price, c.buy_now
        FROM geodesic_classifieds c LEFT JOIN geodesic_userdata u
        ON c.high_bidder=u.id
        WHERE c.live=0 AND c.item_type = 2
        ORDER BY c.id";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            $this->body .= "<span class=medium_font><a href=index.php?a=300&b=3&c=2>Download Completed Auctions Report</a></span><p>";
            $this->body .= '<table width = 100% cellpadding=3 cellspacing=1 class=row_color_red>';
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left colspan=14 class=medium_font_light><strong>Completed Auctions Report</strong></td>";
            $this->body .= "</tr>";
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left colspan=6 class=medium_font_light><strong>Bidder contact information</strong></td>";
            $this->body .= "<td align=left colspan=4 class=medium_font_light><strong>Item details</strong></td>";
            $this->body .= "<td align=left colspan=4 class=medium_font_light><strong>Price</strong></td>";
            $this->body .= "</tr>";
            $this->body .= "<tr class=row_color_red height=30>";
            $this->body .= "<td align=left class=small_font_light><strong>Name</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Phone</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Email</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>City</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>State</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Zip</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Id</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Name</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Stock number</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>VIN number</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Reserve price</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Buy it now</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Final bid</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Reserve price met</strong></td>";
            $this->body .= "</tr>";

            while ($row = $result->FetchRow())
            {
                if($i%2) $bgcolor = '#CCCCFF';
                else $bgcolor = 'FFFFCC';

                $username = $row['firstname'] . ' ' . $row['lastname'];
                $username = trim($username);
                if (empty($username)) $username = 'No buyer';
                $phone = $row['phone'];
                $email = $row['email'];
                $city = $row['city'];
                $state = $row['state'];
                $zip = $row['zip'];
                $id = $row['id'];
                $title = $row['title'];
                $price = $row['price'];
                $stockNumber = $row['optional_field_18'];
                $vin = $row['optional_field_16'];
                $current_bid = $row['current_bid'];
                $reserve_price = $row['reserve_price'];
                $buy_now = $row['buy_now'];
                $reserve_price_met = (($current_bid >= $reserve_price) || ($price >= $reserve_price)) ? "<font color=#FF0000><strong>X</strong></font>":" ";

                $this->body .= "<tr>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($username)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($phone)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($email)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($city)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($state)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($zip)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($id)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($title)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($stockNumber)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($vin)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($reserve_price)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($buy_now)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($current_bid)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($reserve_price_met)."</td>\n";
                $this->body .= "</tr>\n";

				$i ++;
            }
            $this->body .= '</table>';
        }
        else
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no completed &laquo;auctions&raquo;.</strong></span>";
        }

        return(1);
    }

    function download_completed_auctions($db)
    {
        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, closed auctions, email report groups.';

        $query =
        "SELECT u.firstname, u.lastname, u.phone, u.email, u.city, u.state, u.zip, c.id, c.title, c.price, c.optional_field_18, c.optional_field_16, c.current_bid, c.reserve_price, c.buy_now
        FROM geodesic_classifieds c LEFT JOIN geodesic_userdata u
        ON c.high_bidder=u.id
        WHERE c.live=0 AND c.item_type = 2
        ORDER BY c.id";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            $solt = date(Ymd);
            $filename = 'ams-current-bids-' . $solt . '.csv';
            $filePath = '../scripts/' . $filename;

            if(!$file = fopen($filePath, "w"))
            {
                Echo "<br>Can't open file $filename<br>";
                exit;
            }

            while ($row = $result->FetchRow())
            {
                $username = $row['firstname'] . ' ' . $row['lastname'];
                $username = trim($username);
                if (empty($username)) $username = 'No buyer';
                $phone = urldecode($row['phone']);
                $email = urldecode($row['email']);
                $city = urldecode($row['city']);
                $state = urldecode($row['state']);
                $zip = urldecode($row['zip']);
                $id = $row['id'];
                $title = urldecode($row['title']);
                $price = $row['price'];
                $stockNumber = urldecode($row['optional_field_18']);
                $vin = urldecode($row['optional_field_16']);
                $current_bid = $row['current_bid'];
                $reserve_price = $row['reserve_price'];
                $buy_now = $row['buy_now'];
                $reserve_price_met = (($current_bid >= $reserve_price) || ($price >= $reserve_price)) ? "X ":" ";

                $text = "$username, ";
                $text .= "$phone, ";
                $text .= "$email, ";
                $text .= "$city, ";
                $text .= "$state, ";
                $text .= "$zip, ";
                $text .= "$id, ";
                $text .= "$title, ";
                $text .= "$stockNumber, ";
                $text .= "$vin, ";
                $text .= "$reserve_price, ";
                $text .= "$buy_now, ";
                $text .= "$current_bid, ";
                $text .= "$reserve_price_met\n";

                fwrite($file, $text, 1024);
            }
            fclose($file);

            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-Disposition: attachment; filename=$filename");

            readfile($filePath);
            $pathToFile = $_SERVER["DOCUMENT_ROOT"] . '/scripts/' . $filename;
            unlink($pathToFile);
            return(1);
        }
        else
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no completed &laquo;auctions&raquo;.</strong></span>";
        	return(0);
        }
    }

    function show_email_report_1($db)
    {
        $emails = array();

        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, closed auctions, email report groups.';

        $query =
		"SELECT u.email
		FROM geodesic_userdata u, geodesic_auctions_bids b, geodesic_classifieds c
		WHERE u.id = b.bidder AND b.auction_id = c.id AND c.live=1 AND c.item_type = 2
		GROUP BY u.id
		LIMIT 0, 1000000";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            while ($row = $result->FetchRow())
            {
            	$email = urldecode($row['email']);
    			$emails[$email] = 1;
            }
        }

        if($recordCount > 0)
        {
            $this->body .= "<span class=medium_font><a href=index.php?a=300&b=4&c=2>Download \"active bidders\"</a></span><p>";
            $this->body .= '<table width = 100% cellpadding=3 cellspacing=1 class=row_color_red>';
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left colspan=12 class=medium_font_light><strong>Email group reports</strong></td>";
            $this->body .= "</tr>";
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left class=medium_font_light><strong>Active bidders</strong></td>";
            $this->body .= "</tr>";
            $this->body .= "<tr class=row_color_red height=30>";
            $this->body .= "<td align=left class=small_font_light><strong>Email</strong></td>";
            $this->body .= "</tr>";

            $i = 0;
            foreach($emails as $email => $value)
            {
                if($i%2) $bgcolor = '#CCCCFF';
                else $bgcolor = 'FFFFCC';

                $this->body .= "<tr>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($email)."</td>\n";
                $this->body .= "</tr>\n";

                $i ++;
            }
            $this->body .= '</table>';
        }

		if(!$this->body)
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no Active users.</strong></span>";
        }

        return(1);
    }

    function show_email_report_2($db)
    {
        $emails = array();
        $activeUsers = array();
        $inactiveUsers = array();

        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, closed auctions, email report groups.';

        $query = "SELECT u.email FROM geodesic_userdata u";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            while ($row = $result->FetchRow())
            {
                $email = urldecode($row['email']);

                $emails[$email] = 0;
            }
        }

        $query =
        "SELECT u.email
        FROM geodesic_userdata u, geodesic_auctions_bids b, geodesic_classifieds c
        WHERE u.id = b.bidder AND b.auction_id = c.id AND c.live=1 AND c.item_type = 2
        GROUP BY u.id
        LIMIT 0, 1000000";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            while ($row = $result->FetchRow())
            {
                $email = urldecode($row['email']);
                if(!empty($email)) $emails[$email] = 1;
            }
        }

        foreach($emails as $email => $value)
        {
            if($value) $activeUsers[$email] = 1;
            else $inactiveUsers[$email] = 1;
        }

        if(count($inactiveUsers) > 0)
        {
            $this->body .= "<p><span class=medium_font><a href=index.php?a=300&b=5&c=2>Download \"Inctive bidders\"</a></span><p>";
            $this->body .= '<table width = 100% cellpadding=3 cellspacing=1 class=row_color_red>';
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left colspan=12 class=medium_font_light><strong>Email group reports</strong></td>";
            $this->body .= "</tr>";
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left class=medium_font_light><strong>Inctive bidders</strong></td>";
            $this->body .= "</tr>";
            $this->body .= "<tr class=row_color_red height=30>";
            $this->body .= "<td align=left class=small_font_light><strong>Email</strong></td>";
            $this->body .= "</tr>";

            $i = 0;
            foreach($inactiveUsers as $email => $value)
            {
                if($i%2) $bgcolor = '#CCCCFF';
                else $bgcolor = 'FFFFCC';

                $this->body .= "<tr>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($email)."</td>\n";
                $this->body .= "</tr>\n";
                $i ++;
            }
            $this->body .= '</table>';
        }

        if(!$this->body)
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no Inactive users.</strong></span>";
        }

        return(1);
    }

    function show_email_report_3($db)
    {
        $emails = array();

        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, closed auctions, email report groups.';

        $query = "SELECT u.email
                 FROM geodesic_userdata u, geodesic_classifieds c
			     WHERE u.id = c.high_bidder AND c.live=1 AND c.item_type = 2
			     GROUP by u.id";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            $this->body .= "<span class=medium_font><a href=index.php?a=300&b=4&c=2>Download \"high-bidders\"</a></span><p>";
            $this->body .= '<table width = 100% cellpadding=3 cellspacing=1 class=row_color_red>';
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left colspan=12 class=medium_font_light><strong>Email group reports</strong></td>";
            $this->body .= "</tr>";
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left class=medium_font_light><strong>Winning-bidders</strong></td>";
            $this->body .= "</tr>";
            $this->body .= "<tr class=row_color_red height=30>";
            $this->body .= "<td align=left class=small_font_light><strong>Email</strong></td>";
            $this->body .= "</tr>";

            $i = 0;
            while ($row = $result->FetchRow())
            {
                if($i%2) $bgcolor = '#CCCCFF';
                else $bgcolor = 'FFFFCC';

                $email = $row['email'];

                $this->body .= "<tr>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($email)."</td>\n";
                $this->body .= "</tr>\n";

                $i ++;
            }
            $this->body .= '</table>';
        }

        if(!$this->body)
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no Winning-bidders.</strong></span>";
        }

        return(1);
    }

    function show_email_report_4($db)
    {
        $emails = array();

        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, closed auctions, email report groups.';

        $query = "SELECT u.email
			     FROM geodesic_userdata u, geodesic_auctions_bids b, geodesic_classifieds c
			     WHERE u.id = b.bidder AND b.auction_id = c.id AND c.live=1 AND u.id != c.high_bidder AND c.item_type = 2
			     GROUP BY u.id
			     LIMIT 0, 1000000";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            $this->body .= "<span class=medium_font><a href=index.php?a=300&b=4&c=2>Download \"losing-bidders\"</a></span><p>";
            $this->body .= '<table width = 100% cellpadding=3 cellspacing=1 class=row_color_red>';
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left colspan=12 class=medium_font_light><strong>Email group reports</strong></td>";
            $this->body .= "</tr>";
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left class=medium_font_light><strong>Losing-bidders</strong></td>";
            $this->body .= "</tr>";
            $this->body .= "<tr class=row_color_red height=30>";
            $this->body .= "<td align=left class=small_font_light><strong>Email</strong></td>";
            $this->body .= "</tr>";

            $i = 0;
            while ($row = $result->FetchRow())
            {
                if($i%2) $bgcolor = '#CCCCFF';
                else $bgcolor = 'FFFFCC';

                $email = $row['email'];

                $this->body .= "<tr>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($email)."</td>\n";
                $this->body .= "</tr>\n";

                $i ++;
            }
            $this->body .= '</table>';
        }

        if(!$this->body)
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no Losing-bidders users.</strong></span>";
        }

        return(1);
    }

    function download_email_report_1($db)
    {
        $emails = array();
        $activeUsers = array();
        $inactiveUsers = array();

        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, closed auctions, email report groups.';

        $query = "SELECT u.email FROM geodesic_userdata u";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            while ($row = $result->FetchRow())
            {
                $email = urldecode($row['email']);

                $emails[$email] = 0;
            }
        }

        $query =
        "SELECT u.email
        FROM geodesic_userdata u, geodesic_auctions_bids b, geodesic_classifieds c
        WHERE u.id = b.bidder and b.auction_id = c.id and c.live=1 AND c.item_type = 2
        GROUP BY u.id
        LIMIT 0, 1000000";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            while ($row = $result->FetchRow())
            {
                $email = urldecode($row['email']);
                if(!empty($email)) $emails[$email] = 1;
            }
        }

        foreach($emails as $email => $value)
        {
            if($value) $activeUsers[$email] = 1;
            else $inactiveUsers[$email] = 1;
        }

        if(count($activeUsers) > 0)
        {
            $solt = date(Ymd);
            $filename = 'ams-emails_active_users-' . $solt . '.csv';
            $filePath = '../scripts/' . $filename;

            if(!$file = fopen($filePath, "w"))
            {
                Echo "<br>Can't open file $filename<br>";
                exit;
            }

            foreach($activeUsers as $email => $value)
            {
                $email = "$email\n";

                fwrite($file, $email, 1024);
            }
            fclose($file);

            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-Disposition: attachment; filename=$filename");

            readfile($filePath);
            $pathToFile = $_SERVER["DOCUMENT_ROOT"] . '/scripts/' . $filename;
            unlink($pathToFile);
            return(1);
        }
        else
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no active users.</strong></span>";
            return(0);
        }
    }

    function download_email_report_2($db)
    {
        $emails = array();
        $activeUsers = array();
        $inactiveUsers = array();

        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, closed auctions, email report groups.';

        $query = "SELECT u.email FROM geodesic_userdata u";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            while ($row = $result->FetchRow())
            {
                $email = urldecode($row['email']);

                $emails[$email] = 0;
            }
        }

        $query =
        "SELECT u.email
        FROM geodesic_userdata u, geodesic_auctions_bids b, geodesic_classifieds c
        WHERE u.id = b.bidder and b.auction_id = c.id and c.live=1 AND c.item_type = 2
        GROUP BY u.id
        LIMIT 0, 1000000";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            while ($row = $result->FetchRow())
            {
                $email = urldecode($row['email']);
                if(!empty($email)) $emails[$email] = 1;
            }
        }

        foreach($emails as $email => $value)
        {
            if($value) $activeUsers[$email] = 1;
            else $inactiveUsers[$email] = 1;
        }

        if(count($inactiveUsers) > 0)
        {
            $solt = date(Ymd);
            $filename = 'ams-emails_inactive_users-' . $solt . '.csv';
            $filePath = '../scripts/' . $filename;

            if(!$file = fopen($filePath, "w"))
            {
                Echo "<br>Can't open file $filename<br>";
                exit;
            }

            foreach($inactiveUsers as $email => $value)
            {
                $email = "$email\n";

                fwrite($file, $email, 1024);
            }
            fclose($file);

            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-Disposition: attachment; filename=$filename");

            readfile($filePath);
            $pathToFile = $_SERVER["DOCUMENT_ROOT"] . '/scripts/' . $filename;
            unlink($pathToFile);
            return(1);
        }
        else
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no inactive users.</strong></span>";
            return(0);
        }
    }

    function download_email_report_3($db)
    {
        $emails = array();

        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, closed auctions, email report groups.';

        $query = "SELECT u.email
                 FROM geodesic_userdata u, geodesic_classifieds c
                 WHERE u.id = c.high_bidder and c.live=1 AND c.item_type = 2
                 GROUP by u.id";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            $solt = date(Ymd);
            $filename = 'ams-emails_high_bidders-' . $solt . '.csv';
            $filePath = '../scripts/' . $filename;

            if(!$file = fopen($filePath, "w"))
            {
                Echo "<br>Can't open file $filename<br>";
                exit;
            }

            while ($row = $result->FetchRow())
            {
            	$email = $row['email']."\n";
            	$email = urldecode($email);

                fwrite($file, $email, 1024);
            }
            fclose($file);

            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-Disposition: attachment; filename=$filename");

            readfile($filePath);
            $pathToFile = $_SERVER["DOCUMENT_ROOT"] . '/scripts/' . $filename;
            unlink($pathToFile);
            return(1);
        }
        else
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no Winning-bidders users.</strong></span>";
            return(0);
        }
    }

    function download_email_report_4($db)
    {
        $emails = array();

        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, closed auctions, email report groups.';

        $query = "SELECT u.email
                 FROM geodesic_userdata u, geodesic_auctions_bids b, geodesic_classifieds c
                 WHERE u.id = b.bidder AND b.auction_id = c.id AND c.live=1 AND u.id != c.high_bidder AND c.item_type = 2
                 GROUP BY u.id
                 LIMIT 0, 1000000";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if( $recordCount > 0)
        {
            $solt = date(Ymd);
            $filename = 'ams-emails_losing_bidders-' . $solt . '.csv';
            $filePath = '../scripts/' . $filename;

            if(!$file = fopen($filePath, "w"))
            {
                Echo "<br>Can't open file $filename<br>";
                exit;
            }

            while ($row = $result->FetchRow())
            {
                $email = $row['email']."\n";
                $email = urldecode($email);

                fwrite($file, $email, 1024);
            }
            fclose($file);

            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-Disposition: attachment; filename=$filename");

            readfile($filePath);
            $pathToFile = $_SERVER["DOCUMENT_ROOT"] . '/scripts/' . $filename;
            unlink($pathToFile);
            return(1);
        }
        else
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no Losing-bidders.</strong></span>";
            return(0);
        }
    }

    function listing_home($db)
    {
        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of bids, items with no bids, completed auctions, email report groups.';

        $this->body .= "<strong>Auction Reports</strong><hr size=1 color=#ff9900 width = 100% align=left>";
        $this->body .= "<table width=50% align=center cellspacing=0 cellpadding=3>\n<tr>\n\t<td align=left>\n\t<ul>";
        $this->body .= "<li type=square><a href=index.php?a=300&b=1&c=1><span class=medium_font><font color=000000>Show Current Auctions Report</span></a></li>\n";
        $this->body .= "<li type=square><a href=index.php?a=300&b=2&c=1><span class=medium_font><font color=000000>Show \"No Bid Auctions\" Report</span></a></li>\n";
        $this->body .= "<li type=square><a href=index.php?a=300&b=3&c=1><span class=medium_font><font color=000000>Show Closed Auctions Report</span></a></li>\n";
        $this->body .= "<br><span class=medium_font><font color=000000><strong>Email groups reports</strong></span>\n";
        $this->body .= "<li type=square><a href=index.php?a=300&b=4&c=1><span class=medium_font><font color=000000>Show \"Active bidders\"</span></a></li>\n";
        $this->body .= "<li type=square><a href=index.php?a=300&b=5&c=1><span class=medium_font><font color=000000>Show \"Inctive bidders\"</span></a></li>\n";
        $this->body .= "<li type=square><a href=index.php?a=300&b=6&c=1><span class=medium_font><font color=000000>Show \"Winning-bidders\"</span></a></li>\n";
        $this->body .= "<li type=square><a href=index.php?a=300&b=7&c=1><span class=medium_font><font color=000000>Show \"Losing-bidders\"</span></a></li>\n";
        $this->body .= "</ul>\n</td\n></tr>\n</table>\n";

        $this->body .= "<table width=50% align=center cellspacing=0 cellpadding=3>\n<tr>\n\t<td align=left>\n\t<ul>";
        $this->body .= "<li type=square><a href=index.php?a=300&b=1&c=2><span class=medium_font><font color=000000>Download Current Auctions Report</span></a></li>\n";
        $this->body .= "<li type=square><a href=index.php?a=300&b=2&c=2><span class=medium_font><font color=000000>Download \"No Bid Auctions\" Report</span></a></li>\n";
        $this->body .= "<li type=square><a href=index.php?a=300&b=3&c=2><span class=medium_font><font color=000000>Download Closed Auctions Report</span></a></li>\n";
        $this->body .= "<br><span class=medium_font><font color=000000><strong>Download email reports</strong></span>\n";
        $this->body .= "<li type=square><a href=index.php?a=300&b=4&c=2><span class=medium_font><font color=000000>Download \"Active bidders\"</span></a></li>\n";
        $this->body .= "<li type=square><a href=index.php?a=300&b=5&c=2><span class=medium_font><font color=000000>Download \"Inctive bidders\"</span></a></li>\n";
        $this->body .= "<li type=square><a href=index.php?a=300&b=6&c=2><span class=medium_font><font color=000000>Download \"Winning-bidders\"</span></a></li>\n";
        $this->body .= "<li type=square><a href=index.php?a=300&b=7&c=2><span class=medium_font><font color=000000>Download \"Losing-bidders\"</span></a></li>\n";
        $this->body .= "</ul>\n</td\n></tr>\n</table>\n";

        return(1);
    }
}

?>