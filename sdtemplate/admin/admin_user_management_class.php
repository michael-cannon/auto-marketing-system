<? //admin_user_management_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Admin_user_management extends Admin_site{


	//used to keep track of what 'order by' the previous search took place in
	//if current is different from this one the search will respond with the first 25 of the
	//returned search set
	var $order_by_switch = 0;
	var $search_group = 0;
	var $user_management_error;
	var $filter_dropdown_id_array = array();
	var $filter_dropdown_name_array = array();
	var $debug = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Admin_user_management($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);

        // Set the admin_icon variable for the admin icon
        $this->admin_icon = "admin_images/menu_storefront.gif";
	} //end of function Admin_user_management()

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_form($db)
    {
        $this->title = 'Download user emails.';
        $this->description = 'Download user emails.';

        $subdomain = $_SERVER['HTTP_HOST'];

        $from = $_REQUEST['from'];
        $to = $_REQUEST['to'];
        $b = ($_REQUEST['b']) ? $_REQUEST['b']:1;

		$this->body .= "
        <style type='text/css'>
            /* Date */
            h2 {font-family: helvetica,arial,verdana,tahoma; font-size:22px; color:#0077cc;}
            td.first {background-color:#fafafa; align:right; font-family: arial, helvetica, verdana; font-size:12px; padding: 10px;}
            td.second {background-color:#fffaf5; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 10px;}
            td.middle {background-color:#ecf4fc; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 2px;}
            td.middle1 {background-color:#ffff99; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 2px;}
            .small_text {font-family: helvetica,arial,verdana,tahoma; font-size:10px; color:#666666}

            /* Date */
            td.searchLeft        {padding:5px;}
            td.searchItem        {padding:5px 0}
            td.searchItemInd    {padding:5px 5px 5px 0}
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
            function url_change(site, miurl)
            {
                var sitename = document.fls.site.value;
                var reg=/http:\/\/www.(\w+).(\w+)/;
                var reg1 = /http:\/\/(\w+)(.[\w]+)+/;
                if(reg.test(sitename) == true)
                {
                    var arr=reg.exec(sitename);
                    document.fls.miurl.value = \"http://\" + arr[1] + \"\.movingiron.com\";
                }
                else if(reg1.test(sitename) == true)
                {
                    var arr=reg1.exec(sitename);
                    document.fls.miurl.value = \"http://\" + arr[1] + \"\.movingiron.com\";
                }
                else
                {
                    alert(\"Incorrect site name!\");
                }
            }

    	</script>
        <div id='cal1' style='position: absolute; top: 200; left: 10; z-index: 666; visibility: hidden;'></div>
        <div id='cal2' style='position: absolute; top: 200; left: 10; z-index: 666; visibility: hidden;'></div>
        <form method=post action='index.php?a=1616&b=1' name='fls' id='fls'>
			<input type=hidden name=subdomains value=$subdomain />
        <table width=100% cellpadding=0 cellcpacing=0>
                    <tr>
                        <td colspan=3 align=center><span class=medium_font><strong>Show User Emails</strong></span></td>
                    </tr>
                    <tr>
                        <td class=first width=50%>From:</td>
                        <td class=second width=5%><input type='text' size='15' name='from' value='".$from."' class='dateInput' /></td>
                        <td class=second><input type='button' value='&nbsp;' width='' onMouseDown=\"showCalendar(this, 'cal1', 'fromAnc', 'from');\" class='dateBtn'></td>
                    </tr>
                    <tr>
                        <td class=middle><spacer type='block' width='1' height='1' /></td>
                        <td class=middle><a name='fromAnc'><img src='i/d-t.gif' width='1' height='1' /></a></td>
                        <td class=middle></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>To:</td>
                        <td class=second width=5%><input type='text' size='15' name='to' value='".$to."' class='dateInput' /></td>
                        <td class=second><input type='button' value='&nbsp;' width='' onMouseDown=\"showCalendar(this, 'cal2', 'toAnc', 'to');\" class='dateBtn'></td>
                    </tr>
                    <tr>
                        <td class=middle><spacer type='block' width='1' height='1' /></td>
                        <td class=middle><a name='toAnc'><img src='i/d-t.gif' width='1' height='1' /></a></td>
                        <td class=middle></td>
                    </tr>
                    <tr height=30 valign=middle>
                        <td align=left>
                        	<span class=medium_font>Show emails: </span>
                        </td>
                        <td colspan=2 align=left>
                            <input name=b type=radio value=1 ";

        if($b == 1) $this->body .= 'checked';
        $this->body .=  ">
                        </td>
                    </tr>
                    <tr height=30 valign=middle>
                        <td align=left>
                            <span class=medium_font>Download emails:</span>
                        </td>
                        <td colspan=2 align=left><input name=b type=radio ";

    	if($b == 2) $this->body .= 'checked';
        $this->body .= "
                            value=2>
                        </td>
                    </tr>
                    <tr>
                    	<td colspan=3 align=center>
                        	<input type=submit value=\">> Go\">
                        </td>
                    </tr>
        </table></form>";
    }

    function show_user_emails($db, $b)
    {
        $this->title = 'Download user emails.';
        $this->description = 'Download user emails.';

		$monthes = Array('January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6, 'July' => 7, 'Augost' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 11);

        $to = $_REQUEST["to"];
        $from = $_REQUEST["from"];
        $subdomain = $_REQUEST['subdomains'];

        $to = explode(" ", $to);
        $to[0] = $monthes[$to[0]];

        $from = explode(" ", $from);
        $from[0] = $monthes[$from[0]];

        $to = mktime(0, 0, 0, $to[0], $to[1], $to[2]);
        $from = mktime(0, 0, 0, $from[0], $from[1], $from[2]);

    	$query = "SELECT u.email
			     FROM geodesic_userdata u, geodesic_user_groups_price_plans l
			     WHERE 1 = 1
                 AND l.group_id = 1
                 AND l.id = u.id
			     AND u.date_joined >= ".$from;
    	if($to)
        {
			$query .= " AND u.date_joined <= ".$to;
        }

        if($subdomain)
        {
            $query .= " AND u.subdomain = '".$subdomain."'";
        }

        $query .= " GROUP BY u.email";

    	$this->body .= "<hr size=1 color=#ff9900 align=center>";

		$result = $db->Execute($query);

        if(!$result)
        {
            $this->site_error($db->ErrorMsg());
            return false;
        }
        else
        {
            $recordCount = $result->RecordCount();
            if($recordCount)
            {
            	//$this->body .= "<hr>".$query."<hr>";
            	$this->body .= "<table cellpadding=5 cellspacing=1 align=center width=80% color=#cccccc>";
            	$i = 0;
            	while($res = $result->FetchNextObject())
                {
                	$emails[$i] = $res->EMAIL;
                    $this->body .= "<tr><td class=medium_font bgcolor=#fafafa align=left valign=middle>".$res->EMAIL."</td></tr>";
                    $i ++;
                }
                $this->body .= "</table>";
            }
            else
            {
            	$this->body .= "There are no user emails.";
            }
        }
        return true;
    }

    function show_user_emails_all($db, $b)
    {
        $this->title = 'Download user emails.';
        $this->description = 'Download user emails.';

        $monthes = Array('January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6, 'July' => 7, 'Augost' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 11);

        $to = $_REQUEST["to"];
        $from = $_REQUEST["from"];
        $subdomain = $_REQUEST['subdomains'];

        $to = explode(" ", $to);
        $to[0] = $monthes[$to[0]];

        $from = explode(" ", $from);
        $from[0] = $monthes[$from[0]];

        $to = mktime(0, 0, 0, $to[0], $to[1], $to[2]);
        $from = mktime(0, 0, 0, $from[0], $from[1], $from[2]);

        $query = "SELECT u.email
                 FROM geodesic_userdata u, geodesic_user_groups_price_plans l
                 WHERE 1 = 1
                 AND l.id = u.id
                 AND l.group_id = 1";

        if($subdomain)
        {
            $query .= " AND u.subdomain = '".$subdomain."'";
        }

        $query .= " GROUP BY u.email";

        $this->body .= "<hr size=1 color=#ff9900 align=center>";

        $result = $db->Execute($query);

        if(!$result)
        {
            $this->site_error($db->ErrorMsg());
            return false;
        }
        else
        {
            $recordCount = $result->RecordCount();
            if($recordCount)
            {
            	//$this->body .= "<hr>".$query."<hr>";
                $this->body .= "<table cellpadding=5 cellspacing=1 align=center width=80% color=#cccccc>";
                $i = 0;
                while($res = $result->FetchNextObject())
                {
                    $emails[$i] = $res->EMAIL;
                    $this->body .= "<tr><td class=medium_font bgcolor=#fafafa align=left valign=middle>".$res->EMAIL."</td></tr>";
                    $i ++;
                }
                $this->body .= "</table>";
            }
            else
            {
                $this->body .= "There are no user emails.";
            }
        }
        return true;
    }

    function download_user_emails($db, $b, &$res)
    {
        $this->title = 'Download user emails.';
        $this->description = 'Download user emails.';

        $monthes = Array('January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6, 'July' => 7, 'Augost' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 11);

        $to = $_REQUEST["to"];
        $from = $_REQUEST["from"];
        $subdomain = $_REQUEST["subdomains"];

        $to = explode(" ", $to);
        $to[0] = $monthes[$to[0]];

        $from = explode(" ", $from);
        $from[0] = $monthes[$from[0]];

        $to = mktime(0, 0, 0, $to[0], $to[1], $to[2]);
        $from = mktime(0, 0, 0, $from[0], $from[1], $from[2]);

        $query = "SELECT u.email
                 FROM geodesic_userdata u, geodesic_user_groups_price_plans l
                 WHERE 1 = 1
                 AND l.group_id = 1
                 AND l.id = u.id
                 AND u.date_joined >= ".$from;
        if($to)
        {
            $query .= " AND u.date_joined <= ".$to;
        }

        if($subdomain)
        {
            $query .= " AND u.subdomain = '".$subdomain."'";
        }

        $query .= " GROUP BY u.email";

        $result = $db->Execute($query);

        if(!$result)
        {
            $this->site_error($db->ErrorMsg());
            return false;
        }
        else
        {
            $recordCount = $result->RecordCount();
            if($recordCount)
            {
            	$salt = date(Ymd);
            	$filename = 'ams-user-emails-'.$subdomain.'-'.$salt.'.csv';
            	$filePath = '../scripts/' . $filename;

            	if(!$file = fopen($filePath, "w"))
            	{
                	echo "<br>Can't open file $filename<br>";
                	exit;
            	}

                $i = 0;
                while($res = $result->FetchNextObject())
                {
                    if($i == 0) $text = "$res->EMAIL\n";
                    else $text = "$res->EMAIL\n";
                	fwrite($file, $text, 1024);
                    $i ++;
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
                $res = 1;
            	return true;
            }
            else
            {
            	$this->body .= "There are no emails.";
                return true;
            }
        }
        return 1;
    }

    function download_user_emails_all($db, $b, &$res)
    {
        $this->title = 'Download user emails.';
        $this->description = 'Download user emails.';

        $monthes = Array('January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6, 'July' => 7, 'Augost' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12);

        $to = $_REQUEST["to"];
        $from = $_REQUEST["from"];
        $subdomain = $_REQUEST["subdomains"];

        $to = explode(" ", $to);
        $to[0] = $monthes[$to[0]];

        $from = explode(" ", $from);
        $from[0] = $monthes[$from[0]];

        $to = mktime(0, 0, 0, $to[0], $to[1], $to[2]);
        $from = mktime(0, 0, 0, $from[0], $from[1], $from[2]);

        $query = "SELECT u.email
                 FROM geodesic_userdata u, geodesic_user_groups_price_plans l
                 WHERE 1 = 1
                 AND l.id = u.id
                 AND l.group_id = 1";

        if($subdomain)
        {
            $query .= " AND u.subdomain = '".$subdomain."'";
        }

        $query .= " GROUP BY u.email";

        $result = $db->Execute($query);

        if(!$result)
        {
            $this->site_error($db->ErrorMsg());
            return false;
        }
        else
        {
            $recordCount = $result->RecordCount();
            if($recordCount)
            {
                $salt = date(Ymd);
                $filename = 'ams-user-emails-'.$subdomain.'-all-'.$salt.'.csv';
                $filePath = '../scripts/' . $filename;

                if(!$file = fopen($filePath, "w"))
                {
                    echo "<br>Can't open file $filename<br>";
                    exit;
                }

                $i = 0;
                while($res = $result->FetchNextObject())
                {
                    if($i == 0) $text = "$res->EMAIL\n";
                    else $text = "$res->EMAIL\n";
                    fwrite($file, $text, 1024);
                    $i ++;
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
                $res = 1;
                return true;
            }
            else
            {
                $this->body .= "There are no emails.";
                return true;
            }
        }
    }

	function list_users($db,$list_info=0)
	{
		//list_info will contain
		//order by = list_info[order_by]
		//limit = list_info[limit]
		//search_group = list_info[search_group]
		//only prints 25 users at a time
		//shows username,first name, last name, #current listings, locked, edit, remove
		$this->search_group = $list_info[search_group];

		if ($this->search_group)
			$sql_query = "select count(*) as total_users from ".$this->userdata_table.",".$this->user_groups_price_plans_table.
				" where ".$this->userdata_table.".id = ".$this->user_groups_price_plans_table.".id
				and ".$this->user_groups_price_plans_table.".group_id = ".$this->search_group;
		else
			$sql_query = "select count(*) as total_users from ".$this->userdata_table.",".$this->user_groups_price_plans_table.
				" where ".$this->userdata_table.".id = ".$this->user_groups_price_plans_table.".id and ".$this->userdata_table.".id != 1 ";


		$result = $db->Execute($sql_query);
		if ($debug) echo $sql_query." is the query<br>\n";
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		$show_total = $result->FetchRow();
		$total_users = $show_total["total_users"];

		$sql_query_base = "select * from ".$this->userdata_table.",".$this->user_groups_price_plans_table.
				" where ".$this->userdata_table.".id = ".$this->user_groups_price_plans_table.".id and ".$this->userdata_table.".id != 1 ";

		if ($this->search_group)
			$sql_query_base .= "and ".$this->user_groups_price_plans_table.".group_id = ".$this->search_group;

		if ($list_info["page"])
		{
			$page = $list_info["page"];
			if ($list_info["page"] > 1)
			{
				$limit = ((($list_info["page"] - 1) * 25));
				$limit_phrase = ($limit).",25 ";
				//$limit_phrase = $limit;
			}
			else
				$limit_phrase = " 0,25";
		}
		else
		{
			$page = 1;
			$limit_phrase = " 0,25";
		}

		switch ($list_info["order_by"])
		{
			case 1:
				$order_by_phrase = " order by ".$this->userdata_table.".username,".$this->userdata_table.".lastname,".$this->userdata_table.".firstname ";
				$order_by_message = " (ordered by username) ";
				$this->order_by_switch = 1;
				break;
			case 2:
				$order_by_phrase = " order by ".$this->userdata_table.".lastname,".$this->userdata_table.".firstname,".$this->userdata_table.".username ";
				$order_by_message = " (ordered by last name) ";
				$this->order_by_switch = 2;
				break;
			case 3:
				$order_by_phrase = " order by ".$this->userdata_table.".date_joined desc,".$this->userdata_table.".username,".$this->userdata_table.".lastname,".$this->userdata_table.".firstname ";
				$order_by_message = " (ordered by date joined -- latest to earliest) ";
				$this->order_by_switch = 3;
				break;
			case 4:
				$order_by_phrase = " order by ".$this->userdata_table.".date_joined,".$this->userdata_table.".username,".$this->userdata_table.".lastname,".$this->userdata_table.".firstname ";
				$order_by_message = " (ordered by date joined -- earliest to latest)";
				$this->order_by_switch = 4;
				break;
			default:
				$order_by_phrase = " order by ".$this->userdata_table.".username,".$this->userdata_table.".lastname,".$this->userdata_table.".firstname ";
				$order_by_message = " (ordered by username) ";
				$this->order_by_switch = 1;

		} //end of switch

		if ($this->search_group)
			$order_by_message .= "from ".$this->get_group_name($db,$this->search_group)." group";
		else
			$order_by_message .= "from all groups";
		$sql_query = $sql_query_base.$order_by_phrase." limit ".$limit_phrase;

		$result = $db->Execute($sql_query);
		if ($debug) echo $sql_query." is the query<br>\n";
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
			$this->title = "Users / User Groups > List Users";
			$this->body .= "<tr>\n\t<td colspan=6>\n\t";
			$this->list_user_order_by_box($db);
			$this->body .= "</td>\n</tr>\n";
			$this->body .= "<tr bgcolor=000066>\n\t\t<td colspan=6 class=medium_font_light>\n\t<b>Registered User List -
				".$order_by_message."</b></td>\n\t</tr>\n\t";
			$this->body .= "<tr class=row_color_black>\n\t\t<td class=medium_font_light>\n\t<b>username</b></td>\n\t\t";
			$this->body .= "<td class=medium_font_light>\n\t<b>lastname</b></td>\n\t\t";
			$this->body .= "<td class=medium_font_light>\n\t<b>firstname</b></td>\n\t\t";
			$this->body .= "<td class=medium_font_light align=center>\n\t<b>status</b></td>\n\t\t";
			$this->body .= "<td class=medium_font_light align=center width=100>\n\t&nbsp;</td>\n\t\t";
			$this->body .= "<td class=medium_font_light align=center width=100>\n\t&nbsp;</td>\n\t</tr>\n\t";


			$this->row_count = 0;
			while ($show = $result->FetchRow())
			{
				if ($show["level"] != 1)
					$this->show_user_line($db,$show);
				$this->row_count++;
			}
			$this->show_range_of_users($db,$sql_query,$list_info["order_by"]);

			if ($total_users > 25)
			{
				$this->body .= "<tr class=row_color_black>\n\t\t<td colspan=6 class=small_font_light>";
				$number_of_page_results = ceil($total_users / 25);
				for ($i = 1;$i <= $number_of_page_results;$i++)
				{
					if ($page == $i)
					{
						$this->body .= " <b><span class=small_font_light>\n\t".$i."</span></b> ";
					}
					else
					{
						$this->body .= "<a href=index.php?a=19&b[page]=".$i;
						if ($list_info["order_by"])
							$this->body .= "&b[order_by]=".$list_info["order_by"];
						if ($list_info["search_group"])
							$this->body .= "&b[search_group]=".$list_info["search_group"];
						$this->body .= " class=small_font_light>\n\t".$i."</font></a> ";
					}
				}
				$this->body .= "</font></td>\n\t</tr>\n\t";
			}
			$this->body .= "<tr>\n\t\t<td colspan=6 align=center><a href=index.php?a=69><span class=medium_font><b>Add New User</b></span></a></td>\n\t</tr>\n\t";
			$this->body .= "</table>\n";
			return true;
		}
		else
		{
			//no users in the database
			$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
			$this->title = "List Users";
			$this->body .= "<tr>\n\t<td colspan=6>\n\t";
			$this->list_user_order_by_box($db);
			$this->body .= "</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td class=medium_font align=center><b>No users are currently in the database.</b></font></td>\n\t</tr>\n\t";
			$this->body .= "</table>\n";
			return true;
		}

	} //end of list_user

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function list_user_order_by_box($db)
	{
		$this->body .= "<form action=index.php?a=19 method=post>\n";
		$this->body .= "<table width=600 cellpadding=5 cellspacing=1 bordercolor=999999 border=1 align=center>\n";
		$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light align=center>\n\t<b>Sort Users by:</b></font>\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td class=medium_font align=center>\n\t<br><input type=radio name=b[order_by]";
		if ($this->order_by_switch == 1)
			$this->body .= " checked";
		$this->body .= " value=1><b>username</b>\n\t<input type=radio name=b[order_by]";
		if ($this->order_by_switch == 2)
			$this->body .= " checked";
		$this->body .= " value=2><b>last name</b>\n\t<input type=radio name=b[order_by]";
		if ($this->order_by_switch == 3)
			$this->body .= " checked";
		$this->body .= " value=3><b>date joined (latest first)</b>\n\t<input type=radio name=b[order_by]";
		if ($this->order_by_switch == 4)
			$this->body .= " checked";
		$this->body .= " value=4><b>date joined (earliest first)<br><br></b>\n\t\n\t";

		$this->body .= $this->group_dropdown($db);

		$this->body .= "<input type=submit name=search value=\"Sort\">\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
	} //end of function list_user_order_by_box

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_user_line($db,$show)
	{
		$current_status = $this->get_current_status($db,$show["id"]);
		$this->body .= "<tr class=".$this->get_row_color().">\n\t\t<td class=small_font>".$show["username"]."</font>
			<input type=hidden name=b[".$show["id"]."] value=".$show["username"].">\n\t\t</td>\n\t\t";
		$this->body .= "<td class=small_font>".stripslashes($show["lastname"])."</font>\n\t\t</td>\n\t\t";
		$this->body .= "<td class=small_font>".stripslashes($show["firstname"])."</font>\n\t\t</td>\n\t\t";
		$this->body .= "<td class=small_font align=center>";
		if ($current_status == 1)
			$this->body .= "active";
		else
			$this->body .= "suspended";
		$this->body .= "</font>\n\t\t</td>\n\t\t";
		$this->body .= "<td class=small_font align=center width=100><a href=index.php?a=17&b=".$show["id"]."><img src=admin_images/btn_admin_view.gif alt=view border=0></a>\n\t\t</td>\n\t\t";
		$this->body .= "<td class=small_font align=center width=100><a href=index.php?a=34&b=".$show["id"]."><img src=admin_images/btn_admin_remove.gif alt=remove border=0></a>\n\t\t</td>\n\t</tr>\n\t";

	} //end of function show_user_line

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function edit_user_form($db,$user_id=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"password\", \"Edit the user's password here. All of the same requirements to the user apply here.\"]\n";

		//".$this->show_tooltip(2,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";
		$this->sql_query = "SELECT * FROM ".$this->site_configuration_table;
		$result = $db->Execute($this->sql_query);
		if ($debug) echo $sql_query." is the query<br>\n";
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		else
		{
			$this->configuration_data = $result->FetchRow();
		}

		$this->sql_query = "SELECT * FROM ".$this->registration_configuration_table;

		$result = $db->Execute($this->sql_query);
		if ($debug) echo $sql_query." is the query<br>\n";
		if (!$result)
		{
			return false;
		}
		else
		{
			$this->registration_configuration = $result->FetchRow();
		}
		if ($user_id)
		{
			$user_data = $this->get_user_data($db,$user_id);
			//highlight_string(print_r($user_data,1));
			$sql_query = "select * from ".$this->logins_table."	where id = ".$user_id;
			$password_result = $db->Execute($sql_query);
			if (!$password_result)
			{
				return false;
			}
			elseif ($password_result->RecordCount() == 1)
			{
				$show_password = $password_result->FetchRow();
				if ($user_data)
				{
					//display the form to edit the userdata
					if (!$this->admin_demo())$this->body .= "<form action=index.php?a=18&b=".$user_id." method=post>\n";
					$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center>\n";
					$this->title = "Users / User Groups > List Users > Userdata Display > Edit";
					$this->description = "Displayed below is this user's data.  Make any
						necessary changes and click the \"Save\" button.";

					if ($this->user_management_error)
					{
						$this->body .= "<tr class=row_color2>\n\t<td colspan=2 class=medium_error_font><b>".
							$this->user_management_error."</b></font>\n\t</td>\n</tr>\n";

					}

					$this->body .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light align=center>\n\t<b>PERSONAL</b></font>\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=row_color1>\n\t<td align=right class=medium_font width=50%><b>user id: </b></font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t".$user_data["id"]."</font>\n\t</td>\n</tr>\n";

					$current_status = $this->get_current_status($db,$user_id);
					$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font><b>current status: </b></font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t <input type=radio name=c[status] value=1 ";
					if ($current_status == 1)
						$this->body .= " checked";
					$this->body .= "> active <br><input type=radio name=c[status] value=2 ";
					if ($current_status == 2)
						$this->body .= " checked";
					$this->body .= "> suspended </font>\n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color1>\n\t<td align=right class=medium_font><b>username: </b></font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=text name=c[username] value=\"".$user_data["username"]."\"></font>\n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color1>\n\t<td align=right class=medium_font><b>password: </b>".$this->show_tooltip(1,1)."</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=password name=c[password] value=\"".$show_password["password"]."\">\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=row_color1>\n\t<td align=right class=medium_font><b.password verifier: </b></font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=password name=c[password_verifier] value=\"".$show_password["password"]."\">\n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color1>\n\t<td align=right class=medium_font><b>first name: </b></font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=text name=c[firstname] value=\"".stripslashes($user_data["firstname"])."\"></font>\n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color1>\n\t<td align=right class=medium_font><b>last name: </b></font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=text name=c[lastname] value=\"".stripslashes($user_data["lastname"])."\"></font>\n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color1>\n\t<td align=right class=medium_font><b>date registered: </b></font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t";
					if ($user_data["date_joined"] == 0)
						$this->body .= " not available ";
					else
						$this->body .= date("M d,Y G:i - l",$user_data["date_joined"])."</font>\n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light align=center>\n\t<b>BUSINESS STATUS</b></font>\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=row_color2>\n\t<td align=right class=medium_font><b>company name</font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=text name=c[company_name] value=\"".$user_data["company_name"]."\"></font>\n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color2>\n\t<td align=right class=medium_font><b>business type</font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=radio name=c[business_type] value=1 ";
					if ($user_data["business_type"] == 1)
						$this->body .= " checked";
					$this->body .= "> individual <br><input type=radio name=c[business_type] value=2 ";
					if ($user_data["business_type"] == 2)
						$this->body .= " checked";
					$this->body .= "> business </font>\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=row_color2>\n\t<td align=right class=medium_font><b>url:</font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=text name=c[url] value=\"".$user_data["url"]."\"></font>\n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light align=center>\n\t<b>ADDRESS</b></font>\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font><b>address: </b></font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=text name=c[address] value=\"".$user_data["address"]."\">\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font><b>address line 2: </b></font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=text name=c[address_2] value=\"".$user_data["address_2"]."\">\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font><b>city: </b></font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=text name=c[city] value=\"".$user_data["city"]."\">\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font><b>state: </b></font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t";
					$this->body .= $this->show_state_dropdown($db,$user_data["state"],"c[state]");
					$this->body .= "\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font><b>zip:</font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=text name=c[zip] value=\"".$user_data["zip"]."\">\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font><b>country:\n\t</td>\n\t";

					$this->body .= "<td class=medium_font>\n\t";
					$this->body .= $this->show_country_dropdown($db,$user_data["country"],"c[country]");
					$this->body .= "\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light align=center>\n\t<b>CONTACT</b></font>\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=row_color2>\n\t<td align=right class=medium_font><b>email: </b></font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=text name=c[email] value=\"".$user_data["email"]."\"></font>\n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font><b>phone:</font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=text name=c[phone] value=\"".$user_data["phone"]."\"></td\n</tr>\n";

					$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font><b>phone 2:</font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=text name=c[phone2] value=\"".$user_data["phone2"]."\"></td\n</tr>\n";

					$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font><b>fax:</font>\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=text name=c[fax] value=\"".$user_data["fax"]."\"></td\n</tr>\n";

					$sql_query = "select * from ".$this->registration_configuration_table;
					$result = $db->Execute($sql_query);
					if (!$result)
					{
						$this->site_error($db->ErrorMsg());
						return false;
					}
					elseif ($result->RecordCount() == 1)
					{
						$registration_configuration = $result->FetchRow();
						for($i = 1; $i < 11; $i++)
						{
							if ($this->registration_configuration['use_registration_optional_'.$i.'_field'])
							{
								$this->body .= "
									<tr class=row_color2>
										<td align=right class=medium_font>
											<b>".$registration_configuration['registration_optional_'.$i.'_field_name'].": </b>
										</td>
										<td class=medium_font>";
								if (($this->configuration_data["use_filters"]) && ($this->registration_configuration['registration_optional_'.$i.'_filter_association']))
								{
									$this->get_filter_dropdown($db,"c[optional_field_".$i."]",$user_data['optional_field_'.$i],$this->registration_configuration['registration_optional_'.$i.'_filter_association']);
									$this->body .= "choose a value with an * (asterisk) after it";
								}
								else
								{
									$this->body .= "<input type=text name=c[optional_field_".$i."] value='".$user_data['optional_field_'.$i]."' size=30 maxsize=70> ";
									if ($this->registration_configuration['require_registration_optional_'.$i.'_field'])
										$this->body .= "(required) ";
								}
								$this->body .= "</td>\n\t</tr>\n\t";
							}
						}
					}

					$this->body .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light align=center>\n\t<b>ACCOUNT</b></font>\n\t</td>\n</tr>\n";
					$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$user_id;
					$user_group_result = $db->Execute($this->sql_query);
					if ($debug) echo $sql_query." is the query<br>\n";
					if (!$user_group_result)
					{
						//echo $this->sql_query."<br>\n";
						//do nothing
					}
					elseif ($user_group_result->RecordCount() == 1)
					{
						$show_user_stuff = $user_group_result->FetchRow();
						$group_name = $this->get_group_name($db,$show_user_stuff["group_id"]);
						if ($group_name)
						{
							//change group
							$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font><b>User Group: </b></font>\n\t</td>\n\t";
							$this->body .= "<td class=medium_font>\n\t<select name=c[group]>";
							$this->sql_query = "select * from ".$this->classified_groups_table;
							$all_groups_result = $db->Execute($this->sql_query);
							if ($debug) echo $sql_query." is the query<br>\n";
							if (!$all_groups_result)
							{
								//echo $this->sql_query."<br>\n";
								//do nothing
							}
							elseif ($all_groups_result->RecordCount() > 0)
							{
								while ($show_groups = $all_groups_result->FetchRow())
								{
									$this->body .= "<option value=".$show_groups["group_id"];
									if ($show_groups["group_id"] == $show_user_stuff["group_id"])
									{
										$this->body .= " selected";
										$this->body .= ">".$this->get_group_name($db,$show_groups["group_id"])." (current)</option>\n\t\t";
									}
									else
										$this->body .= ">".$this->get_group_name($db,$show_groups["group_id"])."</option>\n\t\t";
								}
							}
							$this->body .= "</select><br>changing this users group will delete this users current credits and subscriptions and also
								moves the user into the price plan attached to the new group.  The credits or subscriptions can then be
								manually added back.</td\n</tr>\n";
						}

						//change expiration or credits
						if ($this->is_class_auctions() || $this->is_auctions())
							$auction_price_plan = $this->get_price_plan($db,$show_user_stuff["auction_price_plan_id"]);
						if ($this->is_class_auctions() || $this->is_classifieds())
							$classified_price_plan = $this->get_price_plan($db,$show_user_stuff["price_plan_id"]);
						if ($auction_price_plan || $classified_price_plan)
						{
							//current price plan
							if ($this->is_class_auctions() || $this->is_auctions())
								$this->body .= "
									<tr class=row_color1>
										<td width=30% align=right class=medium_font><b>auction</b> price plan attached to:</td>
										<td><a href=index.php?a=37&b=3&g=".$show_user_stuff["auction_price_plan_id"]."><span class=medium_font>".$auction_price_plan["name"]."</a></td>
									</tr>";
							if ($this->is_class_auctions() || $this->is_classifieds())
								$this->body .= "
									<tr class=row_color1>
										<td width=30% align=right class=medium_font><b>classified</b> price plan attached to:</td>
										<td><a href=index.php?a=37&b=3&g=".$show_user_stuff["price_plan_id"]."><span class=medium_font>".$classified_price_plan["name"]."</a></td>
									</tr>";
							if ($auction_price_plan["type_of_billing"]==1 || $classified_price_plan["type_of_billing"]==1)
							{
								//charged per listing -- check for credits
								$this->body .= "
											<tr class=row_color1>
												<td align=right class=medium_font><b>credits: </b></td>";
								$this->body .= "<td class=medium_font>";
								$this->sql_query = "select * from ".$this->user_credits_table." where user_id = ".$user_id;
								$get_credits_result = $db->Execute($this->sql_query);
								if ($debug) echo $sql_query." is the query<br>\n";
								if (!$get_credits_result)
								{
									return false;
								}
								elseif ($get_credits_result->RecordCount() > 0)
								{
									while ($show_credits = $get_credits_result->FetchRow())
									{
										$this->body .= $show_credits["credit_count"]." expire(s) on ".date("M d, Y H:i:s", $show_credits["credits_expire"]).
										" <a href=index.php?a=47&z=1&b=".$user_id."&c=".$show_credits["credits_id"].">delete</a><br>";
									}
								}
								else
									$this->body .= "0";
								$this->body .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href=index.php?a=47&z=2&b=".$user_id.">add credits</a><br>";
								$this->body .= "</td>\n</tr>\n";
							}
							elseif ($auction_price_plan["type_of_billing"]==2 || $classified_price_plan["type_of_billing"]==2)
							{
								//charge by subscription -- display when expire
								$this->body .= "<tr class=row_color1>\n\t<td align=right class=medium_font>subscription expires:</td>\n\t";
								$this->body .= "<td class=medium_font>\n\t";
								$this->sql_query = "select * from ".$this->classified_user_subscriptions_table." where user_id = ".$user_id;
								//echo $this->sql_query."<br>\n";
								$get_subscription_result = $db->Execute($this->sql_query);
								if ($debug) echo $sql_query." is the query<br>\n";
								if (!$get_subscription_result)
								{
									return false;
								}
								elseif ($get_subscription_result->RecordCount() == 1)
								{
									$show_subscription = $get_subscription_result->FetchRow();
									$this->body .= "expires on ".date("M d, Y H:i:s", $show_subscription["subscription_expire"]);
									$this->body .= " - - <a href=index.php?a=47&z=4&b=".$user_id.">delete subscription</a>";
								}
								else
									$this->body .= "expired";
								$this->body .= "<br><a href=index.php?a=47&z=3&b=".$user_id."&c=".$show_subscription["subscription_id"].">change expiration</a>";
								$this->body .= "</td></tr>\n";
							}
						}
					}
					if (!$this->admin_demo())
					{
						$this->body .= "<tr class=row_color1>\n\t<td align=center colspan=2 class=medium_font>
							<input type=submit name=z value=\"Save\"></font></a>
							\n\t</td>\n</tr>\n";
					}
					$this->body .= "</table>\n</form>";
					return true;
				}
				else
				{
					//no user exists
					return false;
				}
			}
		}
		else
		{
			//no user_id
			return false;
		}

	} //end of function edit_user_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_credits($db,$user_id=0,$credits_id=0)
	{
		if (($user_id) && ($credits_id))
		{
			$sql_query = "delete from ".$this->user_credits_table."
				where user_id = ".$user_id." and credits_id = ".$credits_id;
			$delete_result = $db->Execute($sql_query);
			if ($debug) echo $sql_query." is the query<br>\n";

			if (!$delete_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
				return true;
		}
		else
		{
			return false;
		}

	} //end of function delete_credits

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_subscription($db,$user_id=0)
	{
		if ($user_id)
		{
			$this->sql_query = "delete from ".$this->classified_user_subscriptions_table."
				where user_id = ".$user_id;
			$delete_result = $db->Execute($this->sql_query);
			if ($debug) echo $sql_query." is the query<br>\n";
			if (!$delete_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
				return true;
		}
		else
		{
			return false;
		}

	} //end of function delete_subscription

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function change_subscription_form($db,$user_id=0,$subscription_id=0)
	{
		if ($user_id)
		{
			if ($subscription_id)
			{
				$this->sql_query = "select * from ".$this->classified_user_subscriptions_table." where user_id = ".$user_id;
				//echo $this->sql_query."<br>\n";
				$get_subscription_result = $db->Execute($this->sql_query);
				if ($debug) echo $sql_query." is the query<br>\n";
				if (!$get_subscription_result)
				{
					return false;
				}
				elseif ($get_subscription_result->RecordCount() == 1)
				{
					$show = $get_subscription_result->FetchRow();
					$current_expiration = $show["subscription_expire"];
					//echo $current_expiration."<br>\n";
				}
				else
					$current_expiration = $this->shifted_time();
			}
			else
				$current_expiration = $this->shifted_time();
			$user = $this->get_user_data($db,$user_id);

			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=47&z=3&b=".$user_id."&c=".$subscription_id." method=post>\n";
			$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Edit This Users Subscription Expiration";
			$this->description = "Below is this users data current subscription expiration.
				Make any necessary changes you need and click the \"save changes\" button.";
			$this->body .= "<tr class=row_color1>\n\t<td colspan=2 class=medium_font>\n\tSubscription Expiration for ".$user["firstname"]." ".$user["lastname"]." <BR>
				USERNAME: ".$user["username"]."</font>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2>\n\t<td class=medium_font>Date To Expire:</font></td>\n";
			$this->body .= "<td class=medium_font>";
			$this->body .= $this->get_date_select("d[year]","d[month]","d[day]",date("Y",$current_expiration),date("n",$current_expiration),date("j",$current_expiration));
			$this->body .= "<br>expiration will be moved to expire at the end of the day you set here.</td>\n</tr>\n";
			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 align=center><input type=submit name=submit value=\"Save\"></td></tr>\n";
			$this->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center><br><a href=index.php?a=17&b=".$user_id.">back to user info</a></td></tr>\n";
			$this->body .= "</table>\n";
			return true;
		}
		else
		{
			return false;
		}

	} //end of function change_subscription_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function add_credits_form($db,$user_id=0)
	{
		$user = $this->get_user_data($db,$user_id);
		if (($user_id) && ($user))
		{
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=47&z=2&b=".$user_id." method=post>\n";
			$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Users / User Groups > List Users > Userdata > Add Credits";
			$this->description = "Add credits to this user
				through the form below.  Add the number of credits and the date the credits should expire. Then click the \"save\" button.";
			$this->body .= "<tr class=row_color1>\n\t<td colspan=2 class=medium_font>\n\t<b>Add credits to: </b> ".$user["firstname"]." ".$user["lastname"]." <BR>
				<b>Username: </b>".$user["username"]."</font>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2>\n\t<td class=medium_font><b>Date To Expire:</b></font></td>\n";
			$this->body .= "<td class=medium_font>";
			$this->body .= $this->get_date_select("c[year]","c[month]","c[day]",date("Y"),date("n"),date("j"));
			$this->body .= "</td>\n</tr>\n";

			$this->body .= "<tr class=row_color2>\n\t<td class=medium_font><b>Number of Credits:</b></font></td>\n";
			$this->body .= "<td class=medium_font><select name=c[credits]>\n";
			for ($i=1;$i< 1000;$i++)
			{
				$this->body .= "<option>".$i."</option>\n";
			}
			$this->body .= "</select></td>\n</tr>\n";

			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 align=center><input type=submit name=submit value=\"Save\"></td></tr>\n";
			$this->body .= "<tr>\n\t<td colspan=2 class=medium_font><a href=index.php?a=17&b=".$user_id."><b>back to user info</b></a></td></tr>\n";
			$this->body .= "</table>\n";
			return true;
		}
		else
		{
			return false;
		}

	} //end of function add_credits_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function add_credits($db,$user_id=0,$credits_info=0)
	{
		if (($user_id) && ($credits_info))
		{
			$timestamp = mktime(23,59,59,$credits_info["month"],$credits_info["day"],$credits_info["year"]);
			 //set to end of day
			$sql_query = "insert into ".$this->user_credits_table."
				(user_id,credit_count,credits_expire)
				values
				(".$user_id.",".$credits_info["credits"].",".$timestamp.")";
			$insert_credits_result = $db->Execute($sql_query);
			if ($debug) echo $sql_query." is the query<br>\n";

			if (!$insert_credits_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
				return true;
		}
		else
		{
			return false;
		}

	} //end of function add_credits

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_subscription($db,$user_id=0,$subscription_id=0,$subscription_info=0)
	{

		if (($user_id) && ($subscription_id) && ($subscription_info))
		{
			//update subscription
			$timestamp = mktime(23,59,59,$subscription_info["month"],$subscription_info["day"],$subscription_info["year"]);
			 //set to end of day
			$sql_query = "update ".$this->classified_user_subscriptions_table." set
				subscription_expire = \"".$timestamp."\"
				where user_id = ".$user_id." and subscription_id = ".$subscription_id;
			$update_time_result = $db->Execute($sql_query);
			if ($debug) echo $sql_query." is the query<br>\n";

			if (!$update_time_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
				return true;
		}
		elseif (($user_id) && ($subscription_info))
		{
			//insert new subscription
			$sql_query = "delete from ".$this->classified_user_subscriptions_table."
				where user_id = ".$user_id;
			$delete_subscription_result = $db->Execute($sql_query);
			if ($debug) echo $sql_query." is the query<br>\n";

			if (!$delete_subscription_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			$timestamp = mktime(23,59,59,$subscription_info["month"],$subscription_info["day"],$subscription_info["year"]);
			$sql_query = "insert into ".$this->classified_user_subscriptions_table."
				(user_id,subscription_expire)
				values
				(".$user_id.",".$timestamp.")";
			$insert_expiration_result = $db->Execute($sql_query);
			if ($debug) echo $sql_query." is the query<br>\n";

			if (!$insert_expiration_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}

	} //end of function delete_credits

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_user_info($db,$user_id=0,$user_info=0)
	{
		if ($user_id && $user_info)
		{
			$sql_query = "select * from ".$this->logins_table."
				where id = ".$user_id;
			$result = $db->Execute($sql_query);
			if ($debug) echo $sql_query." is the query<br>\n";

			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_username = $result->FetchRow();
				if ($user_info["username"] != $show_username["username"])
				{
					//check if username already exists for another user
					$sql_query = "select * from ".$this->logins_table."
						where username = \"".$user_info["username"]."\" and id != ".$user_id;
					$username_result = $db->Execute($sql_query);
					if ($debug) echo $sql_query." is the query<br>\n";

					if (!$username_result)
					{
						$this->site_error($db->ErrorMsg());
						return false;
					}
					elseif ($username_result->RecordCount() > 0)
					{
						//this username already exists
						$this->user_management_error = "Cannot change username to requested username.
							That username already exists in the database.";
					}
					else
					{
						//this username does not exist in the database
						//change to the new username in the logins and userdata table
						$sql_query = "update ".$this->logins_table." set
							username = \"".$user_info["username"]."\"
							where id = ".$user_id;
						$username_result = $db->Execute($sql_query);
						if ($debug) echo $sql_query." is the query<br>\n";

						if (!$username_result)
						{
							$this->site_error($db->ErrorMsg());
							return false;
						}
						else
						{
							$sql_query = "update ".$this->userdata_table." set
								username = \"".$user_info["username"]."\"
								where id = ".$user_id;
							$username_result = $db->Execute($sql_query);
							if ($debug) echo $sql_query." is the query<br>\n";

							if (!$username_result)
							{
								$this->site_error($db->ErrorMsg());
								return false;
							}
						}
					}
				}

				/*if ((strlen(trim($user_info["password"])) > 4) && (strlen(trim($user_info["password"])) < 15))
				{
					if ($user_info["password"] == $user_info["password_verifier"])
					{
						$sql_query = "update ".$this->logins_table." set
							password = \"".$user_info["password"]."\"
							where id = ".$user_id;

						$result = $db->Execute($sql_query);
						if ($debug) echo $sql_query." is the query<br>\n";
						if (!$result)
						{
							$this->site_error($db->ErrorMsg());
							return false;
						}
					}
					else
					{
						$this->user_management_error = "Password and Password Verifier did not match";
					}
				}*/
				if ((strlen(trim($user_info["password"])) >= 6) && (strlen(trim($user_info["password"])) <= 12))
				{
					// check for alpha-numeric password
					//if((!eregi('[^0-9]', $user_info["password"])) && (!eregi('[^a-z]', $user_info["password"]))  && (!eregi('[^A-Z]', $user_info["password"])))
					if (ereg('[^A-Za-z0-9]', $user_info["password"]))
					{
						$this->user_management_error = "Password cannot contain special characters - only letters and numbers";
					}
					elseif ($user_info["password"] == $user_info["password_verifier"])
					{
						$sql_query = "update ".$this->logins_table." set
							password = \"".$user_info["password"]."\"
							where id = ".$user_id;
						$result = $db->Execute($sql_query);
						if ($this->debug_password) echo $sql_query." is the query<br>\n";
						if($this->configuration_data["debug_admin"])
						{
							$this->debug_display($db, $this->filename, $this->function_name, "logins_table", "update logins data");
						}
						if (!$result)
						{
							$this->site_error($db->ErrorMsg());
							return false;
						}
					}
					else
					{
						$this->user_management_error = "Password and Password Verifier did not match";
					}
				}
				else
				{
					$this->user_management_error = "Password must be at least 6 characters but not more than 12";
				}

				$sql_query = "update ".$this->userdata_table." set
					firstname = \"".$user_info["firstname"]."\",
					lastname = \"".$user_info["lastname"]."\",
					company_name = \"".$user_info["company_name"]."\",
					business_type = \"".$user_info["business_type"]."\",
					url = \"".$user_info["url"]."\",
					address = \"".$user_info["address"]."\",
					address_2 = \"".$user_info["address_2"]."\",
					city = \"".$user_info["city"]."\",
					state = \"".$user_info["state"]."\",
					zip = \"".$user_info["zip"]."\",
					country = \"".$user_info["country"]."\",
					email = \"".$user_info["email"]."\",
					phone = \"".$user_info["phone"]."\",
					phone2 = \"".$user_info["phone2"]."\",
					fax = \"".$user_info["fax"]."\",
					optional_field_1 = \"".$user_info["optional_field_1"]."\",
					optional_field_2 = \"".$user_info["optional_field_2"]."\",
					optional_field_3 = \"".$user_info["optional_field_3"]."\",
					optional_field_4 = \"".$user_info["optional_field_4"]."\",
					optional_field_5 = \"".$user_info["optional_field_5"]."\",
					optional_field_6 = \"".$user_info["optional_field_6"]."\",
					optional_field_7 = \"".$user_info["optional_field_7"]."\",
					optional_field_8 = \"".$user_info["optional_field_8"]."\",
					optional_field_9 = \"".$user_info["optional_field_9"]."\",
					optional_field_10 = \"".$user_info["optional_field_10"]."\"
					where id = ".$user_id;

				$result = $db->Execute($sql_query);
				if ($debug) echo $sql_query." is the query<br>\n";
				if (!$result)
				{
					$this->site_error($db->ErrorMsg());
					return false;
				}
				else
				{
					$sql_query = "update ".$this->logins_table." set
						status = ".$user_info["status"]."
						where id = ".$user_id;

					$result = $db->Execute($sql_query);
					if ($debug) echo $sql_query." is the query<br>\n";
					if (!$result)
					{
						$this->site_error($db->ErrorMsg());
						return false;
					}
				}

				//get the current group
				$sql_query = "select * from ".$this->user_groups_price_plans_table."
					where id = ".$user_id;
				$group_result = $db->Execute($sql_query);
				if ($debug) echo $sql_query." is the query<br>\n";

				if (!$group_result)
				{
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($group_result->RecordCount() == 1)
				{
					$show_group = $group_result->FetchRow();
					if ($user_info["group"] != $show_group["group_id"])
					{
						//get price plan attached to this group
						$this->sql_query = "select * from ".$this->classified_groups_table." where group_id = ".$user_info["group"];
						$result = $db->Execute($this->sql_query);
						if ($debug) echo $sql_query." is the query<br>\n";
						//echo $this->sql_query."<br>\n";
						if (!$result)
						{
							$this->error_message = $this->internal_error_message;
							$this->site_error($db->ErrorMsg());
						}
						elseif ($result->RecordCount() == 1)
						{
							$show_group = $result->FetchRow();
							if ($this->is_class_auctions() || $this->is_classifieds())
							{
								$sql_query = "update ".$this->user_groups_price_plans_table." set
									group_id = ".$user_info["group"].",
									price_plan_id = ".$show_group["price_plan_id"]."
									where id = ".$user_id;
								$update_group_result = $db->Execute($sql_query);
								if ($debug) echo $sql_query." is the query<br>\n";
								if (!$update_group_result)
								{
									$this->site_error($db->ErrorMsg());
									return false;
								}
							}
							if ($this->is_class_auctions() || $this->is_auctions())
							{
								$sql_query = "update ".$this->user_groups_price_plans_table." set
									group_id = ".$user_info["group"].",
									auction_price_plan_id = ".$show_group["auction_price_plan_id"]."
									where id = ".$user_id;
								$update_group_result = $db->Execute($sql_query);
								if ($debug) echo $sql_query." is the query<br>\n";
								if (!$update_group_result)
								{
									$this->site_error($db->ErrorMsg());
									return false;
								}
							}

							$sql_query = "delete from ".$this->user_credits_table."
								where user_id = ".$user_id;
							$delete_credits_result = $db->Execute($sql_query);
							if ($debug) echo $sql_query." is the query<br>\n";

							if (!$delete_credits_result)
							{
								$this->site_error($db->ErrorMsg());
								return false;
							}
							$sql_query = "delete from ".$this->classified_user_subscriptions_table."
								where user_id = ".$user_id;
							$delete_subscriptions_result = $db->Execute($sql_query);
							if ($debug) echo $sql_query." is the query<br>\n";
							if (!$delete_subscriptions_result)
							{
								$this->site_error($db->ErrorMsg());
								return false;
							}
							return true;
						}
						else
						{
							return "no name";
						}


					}
				}

				//api reference
				if ($this->configuration_data["use_api"])
				{
					$user_info["old_username"] = $show_username["username"];
					include("../config.php");
					$user_info["db_host"] = $db_host;
					$user_info["db_name"] = $database;
                    			$apiUpdateUserFile = $_SERVER['DOCUMENT_ROOT'].'/'.'classes/api_update_user.php';
					include_once($apiUpdateUserFile);
					$api_update_user = new API_update_user($user_info);
					$api_update_user->api_update_user_info();
				}

				return true;
			}
			else
			{
				//user does not exist in the logins table
				return false;
			}

		}
		else
		{
			//not enough data to save changes
			return false;
		}

	} //end of function update_user_info

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function lock_unlock_user($db,$user_id)
	{

	} //end of function update_user_info

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function remove_user_verify($db,$user_id)
	{
		if ($user_id != 1)
		{
			//verify that you want to remove this user and all of their listings
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=34&b=".$user_id." method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";

			$this->title = "Verify User Removal";
			$this->description = "Please verify the removal of this user.
				This will remove the user information, their listings present and past and their login information.  If you want to remove the
				user click the \remove user\" button.  Otherwise follow one of the links at the left.<br><br>
				<b>IMPORTANT: If you are using an api to connect different Geodesic Installations you must go to each installation
				individually to delete a user.";
			$this->body .= "<tr>\n\t<td>\n\t";
			$this->body .= "<tr bgcolor=000066>\n\t<td class=large_font_light align=center>\n\t\n\t</td>\n</tr>\n";
			//$this->body .= "<tr>\n<td>\n\t";
			$this->display_user_data($db,$user_id);
			$this->body .= "</td>\n</tr>\n";
			$this->get_configuration_data($db);
			//if ($this->configuration_data["use_api"])
			//	echo "<tr >\n\t<td class=medium_font>\n\t<input type=checkbox name=x value=1>remove user from api connected installations also</font>\n\t</td>\n</tr>\n";
			if (!$this->admin_demo()) $this->body .= "<tr >\n\t<td align=center class=medium_font>\n\t<input type=submit name=z value=\"Remove User\"></font>\n\t</td>\n</tr>\n";
			//echo "</table>\n";
		}
		else
		{
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";

			$this->body .= "<tr class=row_color_red>\n\t<td class=very_large_font_light>\n\t<b>Verify User Removal</b></font>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td class=medium_font_light>\n\tCannot remove the admin.</font>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
		}
		return true;

	} //end of function remove_user_verify

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function remove_user($db,$user_id)
	{
		if ($user_id != 1)
		{
			//must remove from userdata, login, as well as all listings data

			//delete userdata history
			$this->sql_query = "delete from ".$this->userdata_history_table."
				where id = ".$user_id;

			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			//communications message_to
			$this->sql_query = "delete from ".$this->user_communications_table."
				where message_to = ".$user_id;

			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			$this->remove_ad_filters($db,$user_id);

			//delete expired
			$this->sql_query = "delete from ".$this->classifieds_expired_table."
				where seller = ".$user_id;

			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			//get current listings
			$this->sql_query = "select * from ".$this->classifieds_table."
				where seller = ".$user_id;

			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				while ($show = $result->FetchRow())
				{
					//delete image url
					$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$show["id"];
					$get_url_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$get_url_result)
					{
						//echo $this->sql_query."<br>\n";
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}
					elseif ($get_url_result->RecordCount())
					{
						while ($show_url = $get_url_result->FetchRow())
						{
							if ($show_url["full_filename"])
								unlink($show_url["file_path"].$show_url["full_filename"]);
							if ($show_url["thumb_filename"])
								unlink($show_url["file_path"].$show_url["thumb_filename"]);
						}
						$this->sql_query = "delete from ".$this->images_urls_table." where classified_id = ".$show["id"];
						$delete_url_result = $db->Execute($this->sql_query);
						//echo $this->sql_query."<br>\n";
						if (!$delete_url_result)
						{
							//echo $this->sql_query."<br>\n";
							$this->error_message = urldecode($this->messages[81]);
							return false;
						}
					}

					//delete images
					$this->sql_query = "delete from ".$this->images_table."
						where classified_id = ".$show["id"];

					$delete_result = $db->Execute($this->sql_query);
					if (!$delete_result)
					{
						$this->site_error($db->ErrorMsg());
						return false;
					}

					//delete invoices
					$this->sql_query = "delete from ".$this->invoices_table."
						where user_id = ".$user_id;
					//echo $this->sql_query." is the query<br>\n";
					$delete_invoice_result = $db->Execute($this->sql_query);

					//delete balance transactions
					$this->sql_query = "delete from ".$this->balance_transactions."
						where user_id = ".$user_id;
					//echo $this->sql_query." is the query<br>\n";
					$delet_balance_transactions_result = $db->Execute($this->sql_query);

					//delete listings extra questions
					$this->sql_query = "delete from ".$this->classified_extra_table."
						where classified_id = ".$show["id"];

					$delete_result = $db->Execute($this->sql_query);
					if (!$delete_result)
					{
						$this->site_error($db->ErrorMsg());
						return false;
					}

					//delete listings
					$this->sql_query = "delete from ".$this->classifieds_table."
						where id = ".$show["id"];

					$delete_result = $db->Execute($this->sql_query);
					if (!$delete_result)
					{
						$this->site_error($db->ErrorMsg());
						return false;
					}

					$this->update_category_count($db,$show["category"]);
				}
			}



			//delete group information
			$this->sql_query = "delete from ".$this->user_groups_price_plans_table."
				where id = ".$user_id;

			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			//delete login information
			$this->sql_query = "delete from ".$this->logins_table."
				where id = ".$user_id;

			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			//delete userdata
			$this->sql_query = "delete from ".$this->userdata_table."
				where id = ".$user_id;

			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			//delete from subscriptions expiration
			$this->sql_query = "delete from ".$this->classified_user_subscriptions_table."
				where user_id = ".$user_id;

			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			//delete user credits
			$this->sql_query = "delete from ".$this->user_credits_table."
				where user_id = ".$user_id;

			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			//delete user listing filters
			$this->sql_query = "select * from ".$this->classified_ad_filter_table."
				where user_id = ".$user_id;

			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$show_filter = $result->FetchRow();
				$this->sql_query = "delete from ".$this->classified_ad_filter_categories_table."
					where filter_id = ".$show_filter["filter_id"];

				$filter_result = $db->Execute($this->sql_query);
				if (!$filter_result)
				{
					$this->site_error($db->ErrorMsg());
					return false;
				}
			}

			$this->sql_query = "delete from ".$this->classified_ad_filter_table."
				where user_id = ".$user_id;

			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			$this->sql_query = "delete from ".$this->sessions_table." where user_id = ".$user_id;
			$result = $db->Execute($this->sql_query);
			if(!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			//delete user's bids
			$this->sql_query = "DELETE FROM ".$this->bid_table." WHERE bidder = ".$user_id;
			$result = $db->Execute($this->sql_query);
			if(!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}

			//delete user's autobids
			$this->sql_query = "DELETE FROM ".$this->autobid_table." WHERE bidder = ".$user_id;
			$result = $db->Execute($this->sql_query);
			if(!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			//delete user's feedbacks
			$this->sql_query = "DELETE FROM ".$this->feedbacks_table." WHERE
				rated_user_id = ".$user_id." OR rater_user_id = ".$user_id;
			$result = $db->Execute($this->sql_query);
			if(!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
		}
		return true;
	} //end of function remove_user

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function remove_ad_filters($db,$user_id)
	{
		if ($user_id)
		{
			$this->sql_query = "select * from ".$this->classified_ad_filter_table." where user_id = ".$user_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$filter_result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($filter_result->RecordCount() > 0)
			{
				while ($show = $filter_result->FetchRow())
				{
					$this->sql_query = "delete from ".$this->classified_ad_filter_categories_table." where filter_id = ".$show["filter_id"];
					$categories_filters_result = $db->Execute($this->sql_query);
					if (!$categories_filters_result)
					{
						$this->error_message = $this->internal_error_message;
						return false;
					}
				}
			}

			$this->sql_query = "delete from ".$this->classified_ad_filter_table." where user_id = ".$user_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			return true;
		}
		else
			return false;

	} //end of function remove_ad_filters

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function user_search_box()
	{
		$this->body .= "<form action=index.php?a=16 method=post>\n";
		$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center>\n";
		$this->body .= "<tr>\n\t<td colspan=2>\n\t".$this->medium_font."Search for user by</font>\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td>\n\t".$this->medium_font."by username<input type=radio name=b[username_by] value=1></font><br>\n\t
			".$this->medium_font."by first or last name<input type=radio name=b[search_user_by] value=2></font><br>\n\t</td>\n\t";
		$this->body .= "<td>".$this->medium_font."<input type=text size=30 maxsize=30 name=b[search_by_text]></font>\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
	} //end of function user_search_box

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function search_users($db,$search_info=0)
	{
		if ($this->debug)
		{
			echo "<bR>TOP OF SEARCH_USERS<bR>";
			echo  $search_info["search_group"]." is search_info[search_group]<bR>\n";
			echo  $search_info["search_type"]." is search_info[search_type]<bR>\n";
			echo  $search_info["field_type"]." is search_info[field_type]<bR>\n";
			echo  $search_info["before_or_after"]." is search_info[before_or_after]<bR>\n";
			echo  $search_info["begin_month"]." is search_info[begin_month]<bR>\n";
			echo  $search_info["begin_day"]." is search_info[begin_day]<bR>\n";
			echo  $search_info["begin_year"]." is search_info[begin_year]<bR>\n";
		}

		if ($search_info)
		{
			$this->search_group = $search_info["search_group"];
			$this->sql_query = "";
			switch ($search_info["search_type"])
			{
				case 1:
					//search by text
					$this->sql_query = "select * from ".$this->userdata_table.", ".$this->logins_table.",".$this->user_groups_price_plans_table."
						where ".$this->userdata_table.".id = ".$this->logins_table.".id and ".$this->user_groups_price_plans_table.".id = ".$this->logins_table.".id and ";
					switch ($search_info["field_type"])
					{
						case 1:
							$this->sql_query .= $this->logins_table.".username ";
							break;
						case 2:
							$this->sql_query .= $this->userdata_table.".lastname ";
							break;
						case 3:
							$this->sql_query .= $this->userdata_table.".firstname ";
							break;
						case 4:
							$this->sql_query .= $this->userdata_table.".email ";
							break;
						case 5:
							$this->sql_query .= $this->userdata_table.".company_name ";
							break;
						case 6:
							$this->sql_query .= $this->userdata_table.".url ";
							break;
						case 7:
							$this->sql_query .= $this->userdata_table.".city ";
							break;
						case 8:
							$this->sql_query .= $this->userdata_table.".phone ";
							break;
						default:
							$this->sql_query .= $this->logins_table.".username ";
					} //end of switch
					$this->sql_query .= "like \"%".$search_info["text_to_search"]."%\"";
					break;

				case 2:
					//display suspended users
					$this->sql_query = "select * from ".$this->userdata_table.", ".$this->logins_table.",".$this->user_groups_price_plans_table."
						where ".$this->userdata_table.".id = ".$this->logins_table.".id and ".$this->user_groups_price_plans_table.".id = ".$this->logins_table.".id and status=2 ";
					switch ($search_info["field_type"])
					{
						case 1:
							$this->sql_query .= "order by ".$this->logins_table.".username,lastname,firstname ";
							break;
						case 2:
							$this->sql_query .= "order by lastname,firstname ";
							break;
						case 3:
							$this->sql_query .= "order by firstname,lastname ";
							break;
						case 4:
							$this->sql_query .= "order by email ";
							break;
						case 5:
							$this->sql_query .= "order by company_name,lastname,firstname ";
							break;
						case 6:
							$this->sql_query .= "order by url,lastname,firstname ";
							break;
						default:
							$this->sql_query .= "order by ".$this->logins_table.".username,lastname,firstname ";
					} //end of switch
					break;

				case 3:
					//joined before or after a date
					$pivot_date = mktime(0,0,0,$search_info["search_month"],$search_info["search_day"],$search_info["search_year"]);
					$this->sql_query = "select * from ".$this->userdata_table.", ".$this->logins_table.",".$this->user_groups_price_plans_table."
						where ".$this->userdata_table.".id = ".$this->logins_table.".id and ".$this->user_groups_price_plans_table.".id = ".$this->logins_table.".id
						and date_joined ";
					//$this->sql_query = "select * from ".$this->userdata_table." left join ".$this->user_groups_price_plans_table." using (id) where date_joined ";
					if ($search_info["before_or_after"] == 1)
						$this->sql_query .= " < ";
					else
						$this->sql_query .= " > ";
					$this->sql_query .= $pivot_date;
					break;

				case 4:
					//joined between dates
					//check if first date is less than second date
					$begin_date = mktime(0,0,0,$search_info["begin_month"],$search_info["begin_day"],$search_info["begin_year"]);
					$end_date = mktime(0,0,0,$search_info["end_month"],$search_info["end_day"],$search_info["end_year"]);
					if ($begin_date < $end_date)
					{
						//do the search
						$this->sql_query = "select * from ".$this->userdata_table.", ".$this->logins_table.",".$this->user_groups_price_plans_table."
							where ".$this->userdata_table.".id = ".$this->logins_table.".id and ".$this->user_groups_price_plans_table.".id = ".$this->logins_table.".id and
							date_joined < ".$end_date." and date_joined > ".$begin_date;
						//$this->sql_query = "select * from ".$this->userdata_table." left join ".$this->user_groups_price_plans_table." using (id) where date_joined > ".$begin_date." and date_joined < ".$end_date;
					}
					elseif (($begin_date == $end_date) || ($begin_date > $end_date))
					{
						//wrong search data
						$this->body .= "<table width=100% cellpadding=5 cellspacing=1><tr>\n\t<td class=medium_font align=center>\n\t
							<b>Please enter your dates again...</b></td></tr></table>";
						return true;
						exit;
					}

					break;
			} //end of switch

			if ($this->search_group)
			{
				$this->sql_query .= " and group_id = ".$this->search_group;
			}

			if ($this->debug)
			{
				echo $this->sql_query." is the search user query<bR>\n";
			}

			if (strlen(trim($this->sql_query)) == 0)
			{
				//no query to search with

				return false;
			}
			else
			{
				//echo $this->sql_query.'<br>';
				$this->display_search_results($db,urlencode($this->sql_query));
				return true;
			}
		}
		else
		{
			//no search info to search by
			return false;
		}
	} //end of function user_search_box

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_search_results($db,$sql_query=0)
	{
		if ($sql_query)
		{
			$sql_query = urldecode($sql_query);
			$result = $db->Execute($sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=25&x=1 method=post>\n";
				$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center class=row_color1 width=100%>\n";
				$this->title = "Users / User Groups > Search Users > Search Results";
				$this->description = "Send a message to the group of
					users in your results by clicking the \"send a message to these users\" button below.  Don't worry if there are extra users
					you do not wish to send the message to. You will be able to de-select individual users from the recipients list in the messaging admin.";
				$this->body .= "<tr class=row_color2>\n\t\t<td colspan=6 class=large_font align=center>Search Results</font></td>\n\t</tr>\n\t";
				$this->body .= "<tr class=row_color_black>\n\t\t<td class=small_font_light>\n\tusername</font></td>\n\t\t";
				$this->body .= "<td class=small_font_light>\n\tlastname</font></td>\n\t\t";
				$this->body .= "<td class=small_font_light>\n\tfirstname</font></td>\n\t\t";
				$this->body .= "<td class=small_font_light>\n\tstatus</font></td>\n\t\t";
				$this->body .= "<td class=small_font_light>\n\t&nbsp;</font></td>\n\t\t";
				$this->body .= "<td class=small_font_light>\n\t&nbsp;</font></td>\n\t</tr>\n\t";
				$this->row_count = 0;
				while ($show = $result->FetchRow())
				{
					if ($show["level"] != 1)
						$this->show_user_line($db,$show);
					$this->row_count++;
				}
				if (!$this->admin_demo()) $this->body .= "<tr>\n\t\t<td colspan=6 class=medium_font align=center><input type=submit name=submit value=\"send a message to these users\"></font></td>\n\t</tr>\n\t";
				$this->body .= "<tr class=row_color_red>\n\t<td colspan=6 class=medium_font_light>\n\t</font>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n</form>\n";
				return true;
			}
			else
			{
				//no users in the database
				$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center class=row_color1 width=100%>\n";
				$this->title .= "Users / User Groups > Search Users > Search Results";
				$this->description .= "Send a message to the group of users in your results by clicking the \"send a message to these users\" button below.
				Don't worry if there are extra users you do not wish to send the message to. You will be able to de-select individual users from the recipients list in
				the messaging admin.";
				$this->body .= "<tr class=row_color2>\n\t\t<td colspan=6 class=large_font align=center>Search Results</font></td>\n\t</tr>\n\t";
				$this->body .= "<tr>\n\t<td colspan=6 class=medium_font align=center><b>No users matched your search. Please redefine your search.</b></font></td>\n\t</tr>\n\t";
				$this->body .= "</table>\n";
			}
		}
		else
		{
			//no query to search with
			return false;
		}
	}//end of display_search_results

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function advanced_user_search($db)
	{
		//$title = "";
		//$description = "";
		//$template = file_get_contents("template.html");

		$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
		$this->title = "Users / User Groups > Search Users";
		//$title .= "Users / User Groups > Search Users";
		/*$description .= "Find the user or group of users by any of the means below.  From the results you can edit or remove users.
			Once you make a search you can then send a message to the resulting users by clicking the \"send a message\" button at the
			bottom of the form.";*/
		$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
		/*$this->body .= "<tr class=row_color1>\n\t<td class=medium_font>\n\tfields</font><br>";
		$this->body .= "<tr>\n\t<td class=small_font>\n\t*/
		$this->body .= "<tr class=row_color2>\n\t<td class=large_font>\n\tSearch Users...</font><br></td></tr>";
		$this->body .= "<tr class=row_color1>\n\t<td class=large_font align=center>\n\t...by text field</font><br>";
		$this->body .= "<form action=index.php?a=16 method=post>\n\t";
		$this->body .= "<input type=hidden name=b[search_type] value=1>";
		$this->body .= "<table cellpadding=1 cellspacing=0 border=0 width=100%>\n\t";
		$this->body .= "<tr>\n\t<td class=small_font align=center>\n\t<span class=medium_font></span><br>
			<input type=radio name=b[field_type] value=1> username
			<input type=radio name=b[field_type] value=2> lastname
			<input type=radio name=b[field_type] value=3> firstname
			<input type=radio name=b[field_type] value=4> email address
			<input type=radio name=b[field_type] value=5> company name
			<input type=radio name=b[field_type] value=6> url
			<input type=radio name=b[field_type] value=7> city
			<input type=radio name=b[field_type] value=8> phone contacts
			<br>";
		$this->body .= $this->group_dropdown($db);
		$this->body .= "</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td class=medium_font align=center>\n\t<b>search for this text: </b><input type=text name=b[text_to_search] size=30 maxsize=30>
			<input type=submit name=search value=\"Search\"></td>\n</tr>\n";
		$this->body .= "</table>\n\t</form>\n\t</td>\n</tr>\n";

		$this->body .= "<tr class=row_color2>\n\t<td class=large_font align=center>\n\t...by suspended status</font><br>";
		$this->body .= "<form action=index.php?a=16 method=post>\n\t";
		$this->body .= "<input type=hidden name=b[search_type] value=2>";
		$this->body .= "<table cellpadding=1 cellspacing=0 border=0 width=100%>\n\t";
		$this->body .= "<tr>\n\t<td class=small_font align=center>\n\t<span class=medium_font align=center><b>order by:</b></span><br>
			<input type=radio name=b[field_type] value=1> username
			<input type=radio name=b[field_type] value=2> lastname
			<input type=radio name=b[field_type] value=3> firstname
			<input type=radio name=b[field_type] value=4> email address
			<input type=radio name=b[field_type] value=5> company name
			<input type=radio name=b[field_type] value=6> url";
		$this->body .= "</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td class=small_font align=center>\n\t<input type=submit name=search value=\"Search\">
			</td>\n</tr>\n";
		$this->body .= "</table>\n\t</form>\n\t</td>\n</tr>\n";

		$this->body .= "<tr class=row_color1>\n\t<td class=large_font align=center>\n\t...by joined (registration) date</font><br>";
		$this->body .= "<form action=index.php?a=16 method=post>\n\t";
		$this->body .= "<input type=hidden name=b[search_type] value=3>";
		$this->body .= "<table cellpadding=1 cellspacing=0 border=0 width=100%>\n\t";
		$this->body .= "<tr>\n\t<td class=small_font align=center>\n\t<input type=radio name=b[before_or_after] value=1>registered before
			<input type=radio name=b[before_or_after] value=2>registered after</font><br>
			</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td class=small_font align=center>\n\t";
		$this->body .= $this->get_date_select("b[search_year]","b[search_month]","b[search_day]");
		$this->body .= "<br>";
		$this->body .= $this->group_dropdown($db);
		$this->body .= "</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td class=small_font align=center>\n\t<input type=submit name=save value=\"Search\">
			</td>\n</tr>\n";
		$this->body .= "</table>\n\t</form>\n\t</td>\n</tr>\n";

		$this->body .= "<tr class=row_color2>\n\t<td class=large_font align=center>\n\t...by joined (registration) date range</font><br>";
		$this->body .= "<form action=index.php?a=16 method=post>\n\t";
		$this->body .= "<input type=hidden name=b[search_type] value=4>";
		$this->body .= "<table cellpadding=1 cellspacing=0 border=0 width=100%>\n\t";
		$this->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center>\n\t<b>specify a date range:</b><br><br>
			</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td class=medium_font>\n\t<b>from: </b>";
		$this->body .= $this->get_date_select("b[begin_year]","b[begin_month]","b[begin_day]");
		$this->body .= "</td>\n\t";
		$this->body .= "<td class=medium_font>\n\t<b>to: </b>";
		$this->body .= $this->get_date_select("b[end_year]","b[end_month]","b[end_day]");
		$this->body .= "</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=2 align=center>\n\t<br>";
		$this->body .= $this->group_dropdown($db);
		$this->body .= "<span class=small_font><input type=submit name=search value=\"Search\"></span></td>\n</tr>\n";
		$this->body .= "</table>\n\t</form>\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
	} //end of function advanced_user_search

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function send_user_email($db,$user_id)
	{
		if ($this->get_user_info($db,$user_id))
		{
			$user = $this->get_user_data($db,$user_id);
			if ($user)
			{
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a= method=post>\n\t";
				$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center>\n\t";
				$this->body .= "<tr>\n\t\t<td colspan=2 class=large_font>\n\tsend a message to <b>".$user["username"]."</b>
					\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "<tr>\n\t\t<td align=right width=30% class=medium_font>subject</font>\n\t\t</td>\n\t\t";
				$this->body .= "<td>\n\t\t<input type=text name=b[subject] size=50 maxsize=50>\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "<tr>\n\t\t<td colspan=2>\n\t\t<textarea name=b[message] cols=50 rows=10>\n\t\t</td>\n\t</tr>\n\t";
				if (!$this->admin_demo()) $this->body .= "<tr>\n\t\t<td colspan=2>\n\t\t<input type=submit name=submit value=\"Send\">\n\t\t</td>\n\t</tr>\n\t";
				$this->body .= "</table>\n";
			}
			else
			{
				//no user data returned
				return false;
			}
		}
		else
		{
			//no user returned
			return false;
		}
	} //end of function send_user_email

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_range_of_users($db,$sql_query=0,$limit_by)
	{
		if ($sql_query)
		{
			$result = $db->Execute($sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$total_count = ($result->RecordCount() - 1);
				//echo $total_count." is the total count<bR>\n";
				$counter = 1;
				$number_of_times = 0;
				if ($total_count > 25)
				{

					$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
					$this->body .= "<tr>\n\t<td>\n\t";
					while ($number_of_times < 6)
					{
						$this->body .= "<a href=index.php?a=19&b[limit]=".$counter."&b[order_by]=".$limit_by."><span class=medium_font>".$counter."-".($counter + 25)."</span></a> | ";

						$counter = $counter + 25;
						$number_of_times++;
					}
					if ($number_of_times == 6)
					{
						$this->body .= "<a href=index.php?a=19&b[limit]=".($total_count - 25)."&b[order_by]=".$limit_by;
					}
					$this->body .= "</td>\n</tr>\n</table>\n";
				}
			}
			return true;
		}

	} //end of function show_range_of_users

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function group_dropdown($db)
	{
		$this->function_name = "group_dropdown";
		$body = "";
		$sql_query = "select * from ".$this->classified_groups_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() > 1)
		{
			$body .= "<span class=medium_font><b>by User Group:</b>&nbsp&nbsp<select name=b[search_group]>\n\t\t";
			$body .= "<option value=0>all groups</option></span>\n\t\t";
			while ($show = $result->FetchRow())
			{
				$body .= "<option value=".$show["group_id"];
				if ($this->search_group == $show["group_id"])
					$body .= " selected";
				$body .= ">".$show["name"]."</option>\n\t\t";
			}
			$body .= "</select>\n\t";
		}
		elseif ($result->RecordCount() == 1)
		{
			$body .= "<input type=hidden name=b[search_group] value=0>\n\t";
		}

		return  $body;

	} //end of function group_dropdown

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_new_user_form($db)
	{
        $this->page_id = 15;
        $this->get_text($db);

		$this->sql_query = "SELECT * FROM ".$this->site_configuration_table;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		else
		{
			$this->configuration_data = $result->FetchRow();
		}

		$this->sql_query = "SELECT * FROM ".$this->registration_configuration_table;

		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		else
		{
			$this->registration_configuration = $result->FetchRow();
		}

		$this->body .= "<table cellpadding=5 cellspacing=0 border=0 width=100%>\n";
		$this->title .= "Users > User Groups > Add New User";
		$this->description .= "Add a new user manually through the form below.";
		if (!$this->admin_demo())$this->body .= "<tr>\n\t<td>\n\t<form action=index.php?a=69&z=1 method=post>\n\t";
		$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100%>\n\t";

		if ($this->registration_configuration["use_registration_firstname_field"])
		{
			$this->body .= "<tr>\n\t\t<td align=right class=medium_font><b>first name: <b>";
			if ($this->registration_configuration["require_registration_firstname_field"])
				$this->body .= "*";
			$this->body .= "</td>\n\t\t";
			$this->body .= "<td>\n\t\t
				<input type=text name=c[firstname] value=\"".$this->classified_variables["firstname"]."\" size=30 maxsize=50> *\n\t\t</td>\n\t\t";
			$this->body .= "<td>&nbsp;";
			if (isset($this->error[firstname]))
				$this->body .= "<font color=#880000 size=1 face=arial>firstname required</font>";
			$this->body .= "</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_lastname_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>last name: <b>";
			if ($this->registration_configuration["require_registration_lastname_field"])
				$this->body .= "*";
			$this->body .= "</td>\n\t\t";
			$this->body .= "<td>\n\t
				<input type=text name=c[lastname] value=\"".$this->classified_variables["lastname"]."\" size=30 maxsize=50> *\n\t\t</td>\n\t\t";
			$this->body .= "<td>&nbsp;";
			if (isset($this->error[lastname]))
				$this->body .= "<font color=#880000 size=1 face=arial face=arial>lastname required</font>";
			$this->body .= "</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_company_name_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>company name: <b>";
			if ($this->registration_configuration["require_registration_company_name_field"])
				$this->body .= "*";
			$this->body .= "</td>\n\t\t\t";
			$this->body .= "<td>\n\t\t
			 	<input type=text name=c[company_name] value=\"".$this->classified_variables["company_name"]."\" size=30 maxsize=50>
				</td>\n\t\t<td>&nbsp;";
			if (isset($this->error[company_name]))
				$this->body .= "<font color=#880000 size=1 face=arial>company error</font>";
			$this->body .= "&nbsp;</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_business_type_field"])
		{
			$this->body .= "<tr class=data_field_labels><td align=right class=medium_font><b>business type: <b>";
			if ($this->registration_configuration["require_registration_business_type_field"])
				$this->body .= "*";
			$this->body .= "</td>\n\t";
			$this->body .= "<td class=medium_font>\n\t\t
				<input type=radio name=c[business_type] value=1";
			 if ($this->registered_variables[business_type] == 1) $this->body .= " checked";
			$this->body .= " <span class=medium_font>individual</span><br>
			 	<input type=radio name=c[business_type] value=2 ";
			if ($this->registered_variables[business_type] == 2) $this->body .= " checked";
			$this->body .= " <span class=medium_font>business</span>";
			$this->body .=  "\n\t\t</td><td>&nbsp;";
			if (isset($this->error[business_type]))
				$this->body .= "<font class=error_message>please choose a business type</font>";
			$this->body .=  "</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_address_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td valign=top align=right class=medium_font><b>address: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t<input type=text name=c[address] value=\"";
			$this->body .= $this->classified_variables["address"];
			$this->body .= "\" size=30 maxsize=50> ";
			if ($this->registration_configuration["require_registration_address_field"])
				$this->body .= "*";
			$this->body .= "\n\t\t</td>\n\t\t";
			$this->body .= "<td>&nbsp;";

			if (isset($this->error[address]))
				$this->body .= "<font color=#880000 size=1 face=arial>address required</font>";
			$this->body .= "</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_address2_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td valign=top align=right class=medium_font><b>address line 2: <b>";
			if ($this->registration_configuration["require_registration_address2_field"])
				$this->body .= "*";
			$this->body .= "</td>\n\t\t";
			$this->body .= "<td colspan=2>\n\t\t
				<input type=text name=c[address_2] value=\"".$this->classified_variables["address_2"]."\" size=30 maxsize=50>\n\t\t</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_city_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right align=right class=medium_font><b>city: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t
				<input type=text name=c[city] value=\"".$this->classified_variables["city"]."\" size=30 maxsize=50> ";
			if ($this->registration_configuration["require_registration_city_field"])
				$this->body .= "*";
			$this->body .= "\n\t\t</td>\n\t\t";
			$this->body .= "<td>&nbsp;";

			if (isset($this->error[city]))
				$this->body .= "<font color=#880000 size=1 face=arial>city required</font>";
			$this->body .= "</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_state_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>state: <b></td>\n\t\t";
			$this->body .= "<td>";
			$this->body .= $this->show_state_dropdown($db,$this->classified_variables["state"],"c[state]");
			if ($this->registration_configuration["require_registration_state_field"])
				$this->body .= "*";
			$this->body .= "</td>\n\t\t";
			$this->body .= "<td>&nbsp;";

			if (isset($this->error[state]))
				$this->body .= "<font color=#880000 size=1 face=arial>state required</font>";
			$this->body .= "</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_zip_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>zip: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t
				<input type=text name=c[zip] value=\"".$this->classified_variables["zip"]."\" size=30 maxsize=50> ";
			if ($this->registration_configuration["require_registration_zip_field"])
				$this->body .= "*";
			$this->body .= "\n\t\t</td>\n\t\t";
			$this->body .= "<td>&nbsp;";
			if (isset($this->error[zip]))
				$this->body .= "<font color=#880000 size=1 face=arial>zip required</font>";
			$this->body .= "</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_country_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>country: <b></td>\n\t\t";
			$this->body .= "<td>";
			$this->body .= $this->show_country_dropdown($db,$this->classified_variables["country"],"c[country]");
			if ($this->registration_configuration["require_registration_country_field"])
				$this->body .= "*";
			$this->body .= "</td>\n\t<td>&nbsp;";

			if (isset($this->error[country]))
				$this->body .= "<font color=#880000 size=1 face=arial>country required</font>";
			$this->body .= "</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_phone_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t<td align=right class=medium_font><b>phone 1: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t
				<input type=text name=c[phone] value=\"".$this->classified_variables["phone"]."\" size=30 maxsize=50> ";
			if ($this->registration_configuration["require_registration_phone_field"])
				$this->body .= "*";
			$this->body .= "\n\t\t</td>\n\t\t";
			$this->body .= "<td>&nbsp;";

			if (isset($this->error[phone]))
				$this->body .= "<font color=#880000 size=1 face=arial>first contact number required</font>";
			$this->body .= "</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_phone2_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>phone 2: <b>\n\t\t</td>\n\t\t";
			$this->body .= "<td>\n\t\t
				<input type=text name=c[phone_2] value=\"".$this->classified_variables["phone_2"]."\" size=30 maxsize=50> ";
			if ($this->registration_configuration["require_registration_phone2_field"])
				$this->body .= "*";
			$this->body .= "\n\t\t</td>\n\t";
			$this->body .= "<td>&nbsp;";

			if (isset($this->error[phone_2]))
			$this->body .= "<font color=#880000 size=1 face=arial>second contact number required</font>";
			$this->body .= "</td></tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_fax_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>fax: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t
				<input type=text name=c[fax] value=\"".$this->classified_variables["fax"]."\" size=30 maxsize=50> ";
			if ($this->registration_configuration["require_registration_fax_field"])
				$this->body .= "*";
			$this->body .= "\n\t\t</td>\n\t";
			$this->body .= "<td>&nbsp;";

			if (isset($this->error[fax]))
			$this->body .= "<font color=#880000 size=1 face=arial>fax required</font>";
			$this->body .= "</td></tr>\n\t";
		}

		$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>email address: <b></td>\n\t\t";
		$this->body .= "<td>\n\t\t
			<input type=text name=c[email] value=\"".$this->classified_variables["email"]."\" size=30 maxsize=50> *\n\t\t</td>\n\t\t";
		$this->body .= "<td>&nbsp;";
		if (isset($this->error[email]))
			$this->body .= "<font color=#880000 size=1 face=arial>".$this->error[email]."</font>";
		$this->body .= "</td>\n\t</tr>\n\t";

		if ($this->registration_configuration["use_registration_email2_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>email address 2: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t
				<input type=text name=c[email2] value=\"".$this->classified_variables["email2"]."\" size=30 maxsize=50> *\n\t\t</td>\n\t\t";
			$this->body .= "<td>&nbsp;";
			if (isset($this->error[email]))
				$this->body .= "<font color=#880000 size=1 face=arial>".$this->error[email2]."</font>";
			$this->body .= "</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_url_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>url: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t
				<input type=text name=c[url] value=\"".$this->classified_variables["url"]."\" size=30 maxsize=70> ";
			if ($this->registration_configuration["require_registration_url_field"])
				$this->body .= "*";
			$this->body .= "\n\t\t</td>\n\t";
			$this->body .= "<td>&nbsp;";
			if (isset($this->error[url]))
				$this->body .= "<font color=#880000 size=1 face=arial><span class=medium_font>url required</span>";
			$this->body .= "</td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_optional_1_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>".urldecode($this->messages[1220])."<b></td>\n\t\t";
			$this->body .= "<td>\n\t\t";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_1_filter_association"]))
			{
				$this->get_filter_dropdown($db,"c[optional_field_1]",$this->classified_variables["optional_field_1"],$this->registration_configuration["registration_optional_1_filter_association"]);
			}
			else
			{
				$this->body .= "<input type=text name=c[optional_field_1] value=\"".$this->classified_variables["optional_field_1"]."\" size=30 maxsize=70> ";
			}
			if ($this->registration_configuration["require_registration_optional_1_field"])
				$this->body .= "<span class=medium_font>(required) ";
			$this->body .= "\n\t\t<span class=medium_font>choose a value with an * (asterisk) after it";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_1_filter_association"]))
				$this->body .= "</td>\n\t";
			$this->body .= "<td>&nbsp;";
			if (isset($this->error[optional_field_1]))
				$this->body .= "<font color=#880000 size=1 face=arial>optional field 1 required</font>";
			$this->body .= "</span></td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_optional_2_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>optional field 2: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_2_filter_association"]))
			{
				$this->get_filter_dropdown($db,"c[optional_field_2]",$this->classified_variables["optional_field_2"],$this->registration_configuration["registration_optional_2_filter_association"]);
			}
			else
			{
				$this->body .= "<input type=text name=c[optional_field_2] value=\"".$this->classified_variables["optional_field_2"]."\" size=30 maxsize=70> ";
			}
			if ($this->registration_configuration["require_registration_optional_2_field"])
				$this->body .= "<span class=medium_font>(required) ";
			$this->body .= "\n\t\t<span class=medium_font>choose a value with an * (asterisk) after it";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_2_filter_association"]))
				$this->body .= "</td>\n\t";
			if (isset($this->error[optional_field_2]))
				$this->body .= "<font color=#880000 size=1 face=arial>optional field 2 required</font>";
			$this->body .= "</span></td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_optional_3_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>optional field 3: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_3_filter_association"]))
			{
				$this->get_filter_dropdown($db,"c[optional_field_3]",$this->classified_variables["optional_field_3"],$this->registration_configuration["registration_optional_3_filter_association"]);
			}
			else
			{
				$this->body .= "<input type=text name=c[optional_field_3] value=\"".$this->classified_variables["optional_field_3"]."\" size=30 maxsize=70> ";
			}
			if ($this->registration_configuration["require_registration_optional_3_field"])
				$this->body .= "<span class=medium_font>(required) ";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_3_filter_association"]))
				$this->body .= "\n\t\t<span class=medium_font>choose a value with an * (asterisk) after it";
			$this->body .= "</td>\n\t";
			if (isset($this->error[optional_field_3]))
				$this->body .= "<font color=#880000 size=1 face=arial>optional field 3 required</font>";
			$this->body .= "</span></td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_optional_4_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>optional field 4: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_4_filter_association"]))
			{
				$this->get_filter_dropdown($db,"c[optional_field_4]",$this->classified_variables["optional_field_4"],$this->registration_configuration["registration_optional_4_filter_association"]);
			}
			else
			{
				$this->body .= "<input type=text name=c[optional_field_4] value=\"".$this->classified_variables["optional_field_4"]."\" size=30 maxsize=70> ";
			}
			if ($this->registration_configuration["require_registration_optional_4_field"])
				$this->body .= "<span class=medium_font>(required) ";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_4_filter_association"]))
				$this->body .= "\n\t\t<span class=medium_font>choose a value with an * (asterisk) after it";
			$this->body .= "</td>\n\t";
			if (isset($this->error[optional_field_4]))
				$this->body .= "<font color=#880000 size=1 face=arial>optional field 4 required</font>";
			$this->body .= "</span></td>\n\t</tr>\n\t";
		}

						if ($this->registration_configuration["use_registration_optional_5_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>optional field 5: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_5_filter_association"]))
			{
				$this->get_filter_dropdown($db,"c[optional_field_5]",$this->classified_variables["optional_field_5"],$this->registration_configuration["registration_optional_5_filter_association"]);
			}
			else
			{
				$this->body .= "<input type=text name=c[optional_field_5] value=\"".$this->classified_variables["optional_field_5"]."\" size=30 maxsize=70> ";
			}
			if ($this->registration_configuration["require_registration_optional_5_field"])
				$this->body .= "<span class=medium_font>(required) ";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_5_filter_association"]))
				$this->body .= "\n\t\t<span class=medium_font>choose a value with an * (asterisk) after it";
			$this->body .= "</td>\n\t";
			if (isset($this->error[optional_field_5]))
				$this->body .= "<font color=#880000 size=1 face=arial>optional field 5 required</font>";
			$this->body .= "</span></td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_optional_6_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>optional field 6: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_6_filter_association"]))
			{
				$this->get_filter_dropdown($db,"c[optional_field_6]",$this->classified_variables["optional_field_6"],$this->registration_configuration["registration_optional_6_filter_association"]);
			}
			else
			{
				$this->body .= "<input type=text name=c[optional_field_6] value=\"".$this->classified_variables["optional_field_6"]."\" size=30 maxsize=70> ";
			}
			if ($this->registration_configuration["require_registration_optional_6_field"])
				$this->body .= "<span class=medium_font>(required) ";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_6_filter_association"]))
				$this->body .= "\n\t\t<span class=medium_font>choose a value with an * (asterisk) after it";
			$this->body .= "</td>\n\t";
			if (isset($this->error[optional_field_6]))
				$this->body .= "<font color=#880000 size=1 face=arial>optional field 6 required</font>";
			$this->body .= "</span></td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_optional_7_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>optional field 7: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_7_filter_association"]))
			{
				$this->get_filter_dropdown($db,"c[optional_field_7]",$this->classified_variables["optional_field_7"],$this->registration_configuration["registration_optional_7_filter_association"]);
			}
			else
			{
				$this->body .= "<input type=text name=c[optional_field_7] value=\"".$this->classified_variables["optional_field_7"]."\" size=30 maxsize=70> ";
			}
			if ($this->registration_configuration["require_registration_optional_7_field"])
				$this->body .= "<span class=medium_font>(required) ";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_7_filter_association"]))
				$this->body .= "\n\t\t<span class=medium_font>choose a value with an * (asterisk) after it";
			$this->body .= "</td>\n\t";
			if (isset($this->error[optional_field_7]))
				$this->body .= "<font color=#880000 size=1 face=arial>optional field 7 required</font>";
			$this->body .= "</span></td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_optional_8_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>".urldecode($this->messages[1234])."<b></td>\n\t\t";
			$this->body .= "<td>\n\t\t";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_8_filter_association"]))
			{
				$this->get_filter_dropdown($db,"c[optional_field_8]",$this->classified_variables["optional_field_8"],$this->registration_configuration["registration_optional_8_filter_association"]);
			}
            //New code, 04.05.2006 BCS-IT
            else
            {
                if (!$this->registration_configuration["registration_optional_8_field_type"])
                {
                    $this->body .= "<input type=text name=c[optional_field_8] value=\"".$this->registered_variables["optional_field_8"]."\" ";
                    if ($this->registration_configuration["optional_8_maxlength"] > 30)
                        $this->body .= "size=30 maxlength=".$this->registration_configuration["optional_8_maxlength"]." class=data_field_values>";
                    else
                        $this->body .= "size=".$this->registration_configuration["optional_8_maxlength"]." maxlength=".$this->registration_configuration["optional_8_maxlength"]." class=data_field_values>";
                }
                elseif($this->registration_configuration["registration_optional_8_field_type"] == 1)
                {
                    $this->body .= "<textarea name=c[optional_field_8] ";
                    $this->body .= "rows=8 cols=50 class=data_field_values>";
                    $this->body .= $this->registered_variables["optional_field_8"]."</textarea>";
                }
                else
                {
                    $this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration["registration_optional_8_field_type"]." order by display_order, value";
                    $type_result = $db->Execute($this->sql_query);
                    if (!$type_result)
                    {
                        return false;
                    }
                    elseif ($type_result->RecordCount() > 0)
                    {
                        $this->body .= "<select name=c[optional_field_8] class=data_field_values>";
                        $matched = 0;
                        while ($show_dropdown = $type_result->FetchNextObject())
                        {
                            $this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
                            if ($this->registered_variables["optional_field_8"] == $show_dropdown->VALUE)
                            {
                                $this->body .= "selected";
                                $matched = 1;
                            }
                            $this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
                        }
                        $this->body .= "</select>";
                    }
                    else
                        //blank text box
                        $this->body .= "<input type=text name=c[optional_field_1] value=\"".$this->registered_variables["optional_field_8"]."\"  size=".$this->registration_configuration["optional_8_maxlength"]." maxlength=".$this->registration_configuration["optional_8_maxlength"]." class=data_field_values>";
                }
                if (($this->registration_configuration["registration_optional_8_order_box"]) && ($this->registration_configuration["registration_optional_8_field_type"]))
                {
                    $this->body .= " ".urldecode($this->messages[1261])." <input type=text name=c[optional_field_8_other] value=\"";
                    if (!$matched)
                        $this->body .= $this->registered_variables["optional_field_8"];
                    $this->body .= "\" size=15 maxlength=".$this->registration_configuration["optional_8_maxlength"]." class=data_field_values>\n\t";
                }
            }
            //New code, 04.05.2006 BCS-IT


            //04.05.2006 Old code - BCS-IT
            /*
            else
			{
				$this->body .= "<input type=text name=c[optional_field_8] value=\"".$this->classified_variables["optional_field_8"]."\" size=30 maxsize=70> ";
			}
            */
            //04.05.2006 Old code - BCS-IT

			if ($this->registration_configuration["require_registration_optional_8_field"])
				$this->body .= "<span class=medium_font>(required) ";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_8_filter_association"]))
				$this->body .= "\n\t\t<span class=medium_font>choose a value with an * (asterisk) after it";
			$this->body .= "</td>\n\t";
			if (isset($this->error[optional_field_8]))
				$this->body .= "<font color=#880000 size=1 face=arial>optional field 8 required</font>";
			$this->body .= "</span></td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_optional_9_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>optional field 9: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_9_filter_association"]))
			{
				$this->get_filter_dropdown($db,"c[optional_field_9]",$this->classified_variables["optional_field_9"],$this->registration_configuration["registration_optional_9_filter_association"]);
			}
			else
			{
				$this->body .= "<input type=text name=c[optional_field_9] value=\"".$this->classified_variables["optional_field_9"]."\" size=30 maxsize=70> ";
			}
			if ($this->registration_configuration["require_registration_optional_9_field"])
				$this->body .= "<span class=medium_font>(required) ";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_9_filter_association"]))
				$this->body .= "\n\t\t<span class=medium_font>choose a value with an * (asterisk) after it";
			$this->body .= "</td>\n\t";
			if (isset($this->error[optional_field_9]))
				$this->body .= "<font color=#880000 size=1 face=arial>optional field 9 required</font>";
			$this->body .= "</span></td>\n\t</tr>\n\t";
		}

		if ($this->registration_configuration["use_registration_optional_10_field"])
		{
			$this->body .= "<tr class=registration_field_label>\n\t\t<td align=right class=medium_font><b>optional field 10: <b></td>\n\t\t";
			$this->body .= "<td>\n\t\t";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_10_filter_association"]))
			{
				$this->get_filter_dropdown($db,"c[optional_field_10]",$this->classified_variables["optional_field_10"],$this->registration_configuration["registration_optional_10_filter_association"]);
			}
			else
			{
				$this->body .= "<input type=text name=c[optional_field_10] value=\"".$this->classified_variables["optional_field_10"]."\" size=30 maxsize=70> ";
			}
			if ($this->registration_configuration["require_registration_optional_10_field"])
				$this->body .= "<span class=medium_font>(required) ";
			if (($this->configuration_data["use_filters"]) && ($this->registration_configuration["registration_optional_10_filter_association"]))
				$this->body .= "\n\t\t<span class=medium_font>choose a value with an * (asterisk) after it";
			$this->body .= "</td>\n\t";
			if (isset($this->error[optional_field_10]))
				$this->body .= "<font color=#880000 size=1 face=arial>optional field 10 required</font>";
			$this->body .= "</span></td>\n\t</tr>\n\t";
		}

		$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100%>\n\t";
		$this->body .= "<tr>\n\t\t<td align=right valign=top class=registration_field_label><span class=medium_font><b>username: <b></span></td>\n\t\t";
		$this->body .= "<td valign=top>\n\t\t
			<input type=text name=c[username] value=\"".$this->classified_variables["username"]."\"";
		$this->body .= " size=15 maxsize=15> *\n\t\t</td>\n\t\t";
		$this->body .= "<td>&nbsp;";
		if (isset($this->error[username]))
		   	$this->body .= "<font color=#880000 size=1 face=arial>".urldecode($this->error[username])."</font>";
		else
		   	$this->body .= "<font color=#000000 size=1 face=arial></font>";
		$this->body .= "</td>\n\t</tr>\n\t";

		$this->body .= "<tr>\n\t\t<td align=right valign=top class=registration_field_label><span class=medium_font><b>password: <b></span></td>\n\t\t";
		$this->body .= "<td valign=top>\n\t\t
			<input type=password name=c[password] size=15 maxsize=15> *\n\t\t</td>\n\t\t";
		$this->body .= "<td>&nbsp;";
		if (isset($this->error[password]))
			$this->body .= "<font color=#880000 size=1 face=arial>".urldecode($this->error[password])."</font>";
		$this->body .= "</td>\n\t</tr>\n\t";

		$this->body .= "<tr>\n\t\t<td align=right valign=top class=registration_field_label><span class=medium_font><b>password verifier: <b></span></td>\n\t\t";
		$this->body .= "<td valign=top>\n\t\t
			<input type=password name=c[password_confirm] size=15 maxsize=15> *\n\t\t</td>\n\t\t";
		$this->body .= "<td>";
		if ($this->error[repeat_password])
			$this->body .= "<font color=#880000 size=1 face=arial>your password verifier did not match the password field</font>";
		$this->body .= "</td>\n\t</tr>\n\t";
		$this->body .= "<tr>\n\t\t<td colspan=3 align=center>\n\t\t";
		$this->sql_query = "select name,group_id from ".$this->classified_groups_table." order by name";
		$group_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$group_result)
		{
			//echo "no price plan result<bR>\n";
			$this->error_message = $this->internal_error_message;
			return false;
		}
		elseif ($group_result->RecordCount() > 0)
		{
			$this->body .= "<select name=c[group_id]>\n\t\t";
			while ($show = $group_result->FetchRow())
			{
				$this->body .= "<option value=".$show["group_id"].">".$show["name"]."</option>\n\t\t";
			}
			$this->body .= "</select> <span class=medium_font> <b>User Group</b></span>\n\t</font>\n\t</td>\n</tr>\n";
		}
		if (!$this->admin_demo()) $this->body .= "<tr>\n\t\t<td colspan=3 align=center>\n\t\t<input type=submit name=submit value=\"Save\">\n\t\t</td>\n\t</tr>\n\t";
		$this->body .= "</table>\n\t</form>\n\t</td>\n</tr>\n</table>\n";
		return true;
	} //end of function insert_new_user_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_info($db)
	{
		$this->sql_query = "SELECT * FROM ".$this->site_configuration_table;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		else
		{
			$this->configuration_data = $result->FetchRow();
		}

		$this->sql_query = "SELECT * FROM ".$this->registration_configuration_table;

		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		else
		{
			$this->registration_configuration = $result->FetchRow();
		}

		//$this->error = array();
		$this->error_found = 0;
		if ($this->classified_user_id == 0)
		{
			//echo "checking user info<br>\n";
			if ($this->registration_configuration["use_company_name_field"])
			{
				if ($this->configuration_data["require_company_name_field"])
				{
					if (strlen(trim($this->classified_variables[company_name])) == 0) {
						$this->error[company_name] = "missing company name";
						$this->error_found++;
					}
				}
			}

			if (strlen(trim($this->classified_variables[firstname])) == 0) {
				$this->error[firstname] = "please fill in the firstname";
				$this->error_found++;
			}

			if (strlen(trim($this->classified_variables[lastname])) == 0 ) {
				$this->error[lastname] ="please fill in the lastname";
				$this->error_found++;
			}

			if ($this->registration_configuration["require_address_field"])
			{
				if (strlen(trim($this->classified_variables[address]))== 0 ) {
					$this->error[address] ="please fill in the lastname";
					$this->error_found++;
				}
			}

			if (strlen(trim($this->classified_variables[email])) > 0)
			{
				if (eregi("^([a-z0-9]+)(([a-z0-9._-]+))*[@]([a-z0-9]+)([._-]([a-z0-9]+))*[.]([a-z0-9]){2}([a-z0-9])?$", $this->classified_variables[email]))
				{
					$this->sql_query = "select id from ".$this->userdata_table." where email = \"".$this->classified_variables[email]."\"";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						//echo $this->sql_query." is the id check query<br>\n";
						$this->error["registration"] =urldecode($this->messages[230]);
						return false;
					}
					elseif ($result->RecordCount() > 0)
					{
						//email already in use
						$this->error[email] = "email address already exists";
						$this->error_found++;
					}
				}
				else
				{
					$this->error[email] = "please re-enter the email address";
					$this->error_found++;
				}
			}
			else
			{
				$this->error[email] = "please enter an email address";
				$this->error_found++;
			}
			//$this->error[email] = "does not check now - remove before release";

			if ($this->registration_configuration["require_city_field"])
			{
				if (strlen(trim($this->classified_variables[city])) == 0 )
				{
					$this->error[city] = "please fill in the city";
					$this->error_found++;
				}
			}

			if ($this->registration_configuration["use_state_field"])
			{
				if ($this->configuration_data["require_state_field"])
				{
					if ($this->classified_variables[state] == "none" )
					{
						$this->error[state] = "please fill in the state";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["use_country_field"])
			{
				if ($this->configuration_data["require_country_field"])
				{
					if ($this->classified_variables[country] == "none" )
					{
						$this->error[country] = "please fill in the country";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["use_zip_field"])
			{
				if ($this->configuration_data["require_zip_field"])
				{
					if (strlen(trim($this->classified_variables[zip])) == 0 )
					{
						$this->error[zip] = "please fill in the zip";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["use_phone_field"])
			{
				if ($this->configuration_data["require_phone_field"])
				{
					if (strlen(trim($this->classified_variables[phone])) == 0 )
					{
						$this->error[phone] = "please fill in the first contact field";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["use_phone2_field"])
			{
				if ($this->configuration_data["require_phone2_field"])
				{
					if (strlen(trim($this->classified_variables[phone_2])) == 0 )
					{
						$this->error[phone_2] = "please fill in the second contact field";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["require_fax_field"])
			{
				if (strlen(trim($this->classified_variables[fax])) == 0 )
				{
					$this->error[fax] ="please fill in the fax";
					$this->error_found++;
				}
			}

			if ($this->registration_configuration["use_url_field"])
			{
				if ($this->configuration_data["require_url_field"])
				{
					if (strlen(trim($this->classified_variables[url])) == 0 )
					{
						$this->error[url] = "please fill in the url";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["use_registration_optional_1_field"])
			{
				if ($this->registration_configuration["require_registration_optional_1_field"])
				{
					if (strlen(trim($this->classified_variables[optional_field_1])) == 0 )
					{
						$this->error[optional_field_1] = "please fill in the optional field 1";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["use_registration_optional_2_field"])
			{
				if ($this->registration_configuration["require_registration_optional_2_field"])
				{
					if (strlen(trim($this->classified_variables[optional_field_2])) == 0 )
					{
						$this->error[optional_field_2] = "please fill in the optional field 2";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["use_registration_optional_3_field"])
			{
				if ($this->registration_configuration["require_registration_optional_3_field"])
				{
					if (strlen(trim($this->classified_variables[optional_field_3])) == 0 )
					{
						$this->error[optional_field_3] = "please fill in the optional field 3";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["use_registration_optional_4_field"])
			{
				if ($this->registration_configuration["require_registration_optional_4_field"])
				{
					if (strlen(trim($this->classified_variables[optional_field_4])) == 0 )
					{
						$this->error[optional_field_4] = "please fill in the optional field 4";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["use_registration_optional_5_field"])
			{
				if ($this->registration_configuration["require_registration_optional_5_field"])
				{
					if (strlen(trim($this->classified_variables[optional_field_5])) == 0 )
					{
						$this->error[optional_field_5] = "please fill in the optional field 5";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["use_registration_optional_6_field"])
			{
				if ($this->registration_configuration["require_registration_optional_6_field"])
				{
					if (strlen(trim($this->classified_variables[optional_field_6])) == 0 )
					{
						$this->error[optional_field_6] = "please fill in the optional field 6";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["use_registration_optional_7_field"])
			{
				if ($this->registration_configuration["require_registration_optional_7_field"])
				{
					if (strlen(trim($this->classified_variables[optional_field_7])) == 0 )
					{
						$this->error[optional_field_7] = "please fill in the optional field 7";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["use_registration_optional_8_field"])
			{
				if ($this->registration_configuration["require_registration_optional_8_field"])
				{
					if (strlen(trim($this->classified_variables[optional_field_8])) == 0 )
					{
						$this->error[optional_field_8] = "please fill in the optional field 8";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["use_registration_optional_9_field"])
			{
				if ($this->registration_configuration["require_registration_optional_9_field"])
				{
					if (strlen(trim($this->classified_variables[optional_field_9])) == 0 )
					{
						$this->error[optional_field_9] = "please fill in the optional field 9";
						$this->error_found++;
					}
				}
			}

			if ($this->registration_configuration["use_registration_optional_10_field"])
			{
				if ($this->registration_configuration["require_registration_optional_10_field"])
				{
					if (strlen(trim($this->classified_variables[optional_field_10])) == 0 )
					{
						$this->error[optional_field_10] = "please fill in the optional field 10";
						$this->error_found++;
					}
				}
			}

			$this->check_username($db);
			$this->check_password();
		}
		//echo $this->error_found." is error_found<bR>\n";
		if ($this->error_found > 0)
			return false;
		else
			return true;
	} //end of function check_info($info)

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_username($db)
	{
		//echo "hello from check_username<br>\n";
		$this->classified_variables["username"] = trim($this->classified_variables["username"]);
		$this->error[username] = "";
		$username_length = strlen($this->classified_variables["username"]);
		if (($username_length == 0 ) || ($username_length > 12) || ($username_length < 6) || (!ereg("^[[:alnum:]_-]+$", $this->classified_variables["username"])))
		{
			$this->error[username] = "username length must be at least 6 characters and less than 12 characters with no spaces. Numbers, letters, _ and - are acceptable.";
			$this->error_found++;
		 }
		else
		{
			$this->sql_query = "select id from ".$this->logins_table." where username = \"".$this->classified_variables["username"]."\"";
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				$this->error["registration"] =urldecode($this->messages[230]);
				return false;
			}

			if ($result->RecordCount() > 0)
			{
				$this->error[username] = "username already exists";
				$this->error_found++;
			}
			else
			{
				$this->sql_query = "select * from ".$this->confirm_table." where username = \"".$this->classified_variables["username"]."\"";
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					$this->error["registration"] =urldecode($this->messages[230]);
					return false;
				}
				if ($result->RecordCount() > 0)
				{
					$this->error[username] = "username currently in the registration confirmation queue";
					$this->error_found++;
				}
			}
		 }
		 return true;
	} //end of function check_username($username)

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_form_variables ($info)
	{
		//get the variables from the form and save them
		if (is_array($info))
		{
			reset ($info);
			foreach ($info as $key => $value)
			//while (list($key,$value) = each($info))
			{
				if ($value != "none")
					$this->classified_variables[$key] = $value;
				//echo $key." is the key and this is the value - ".$this->classified_variables[$key]."<br>\n";
			}
		}
	} //end of function get_sell_form_variables ($info)

//#####################################################################

	function check_password()
	{
		$this->classified_variables["password"] = trim($this->classified_variables["password"]);
		$this->classified_variables["password_confirm"] = trim($this->classified_variables["password_confirm"]);
		$password_length = strlen($this->classified_variables["password"]);
		if ((($password_length == 0 ) || ($password_length >12) || ($password_length < 6)) && (!ereg("^[[:alnum:]_-]+$", $this->classified_variables["password"])))	{
			$this->error[password] = "please fill in a password";
			$this->error_found++;
		}

		if ($this->classified_variables["password_confirm"] != $this->classified_variables["password"] ) {
			$this->error[repeat_password] = "your password confirmation did not match the password you entered";
			$this->error_found++;
		}
		return true;
	} //end of function check_password


//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_new_user ($db)
	{
		$this->sql_query = "insert into ".$this->logins_table." (username, password,status)
			values
			(\"".$this->classified_variables["username"]."\", \"".$this->classified_variables["password"]."\",1)";

		$login_result = $db->Execute($this->sql_query);
		if (!$login_result)
		{
			$this->site_error($db->ErrorMsg());

			$this->error["confirm"] =urldecode($this->messages[229]);
			return false;
		}
		else
		{
			$user_id = $db->Insert_ID();
			//insert login data into the login table
			$this->sql_query = "insert into ".$this->userdata_table."
				(id,username,email,newsletter,level,company_name,business_type,firstname,lastname,address,address_2,
				zip,city,state,country,phone,phone2,fax,url,date_joined,communication_type,rate_sum,rate_num,
				optional_field_1,optional_field_2,optional_field_3,optional_field_4,optional_field_5,
				optional_field_6,optional_field_7,optional_field_8,optional_field_9,optional_field_10)
				values
				(".$user_id.",'".$this->classified_variables["username"]."','".$this->classified_variables["email"]."',
				'0', 0,'".addslashes($this->classified_variables["company_name"])."',
				'0','".addslashes($this->classified_variables["firstname"])."','".addslashes($this->classified_variables["lastname"])."',
				'".addslashes($this->classified_variables["address"])."','".addslashes($this->classified_variables["address_2"])."','".addslashes($this->classified_variables["zip"])."',
				'".addslashes($this->classified_variables["city"])."','".$this->classified_variables["state"]."','".$this->classified_variables["country"]."',
				'".addslashes($this->classified_variables["phone"])."','".addslashes($this->classified_variables["phone_2"])."','".addslashes($this->classified_variables["fax"])."','".addslashes($this->classified_variables["url"])."',".$this->shifted_time().",1,0,0,
				'".addslashes($this->classified_variables["optional_field_1"])."','".addslashes($this->classified_variables["optional_field_2"])."',
				'".addslashes($this->classified_variables["optional_field_3"])."','".addslashes($this->classified_variables["optional_field_4"])."',
				'".addslashes($this->classified_variables["optional_field_5"])."','".addslashes($this->classified_variables["optional_field_6"])."',
				'".addslashes($this->classified_variables["optional_field_7"])."','".addslashes($this->classified_variables["optional_field_8"])."',
				'".addslashes($this->classified_variables["optional_field_9"])."','".addslashes($this->classified_variables["optional_field_10"])."')";

			$userdata_result = $db->Execute($this->sql_query);
			if (!$userdata_result)
			{
				$this->site_error($db->ErrorMsg());

				$this->error["confirm"] =urldecode($this->messages[229]);
				return false;
		  	}
		  	else
		  	{
		  		//insert into users_group_price_plans table
		  		if ($this->is_class_auctions())
		  		{
		  			$class_price_plan = $this->get_price_plan_from_group($db,$this->classified_variables["group_id"]);
		  			$auction_price_plan = $this->get_price_plan_from_group($db,$this->classified_variables["group_id"],1);
		  			$this->sql_query = "insert into ".$this->user_groups_price_plans_table."
						(id,group_id,price_plan_id,auction_price_plan_id)
						values
						(".$user_id.","
			  			.$this->classified_variables["group_id"].","
			  			.$class_price_plan["price_plan_id"].","
			  			.$auction_price_plan["price_plan_id"].")";
		  		}
		  		elseif ($this->is_auctions())
		  		{
		  			$auction_price_plan = $this->get_price_plan_from_group($db,$this->classified_variables["group_id"],1);
		  			$this->sql_query = "insert into ".$this->user_groups_price_plans_table."
					(id,group_id,auction_price_plan_id)
					values
					(".$user_id.",".$this->classified_variables["group_id"].",".$auction_price_plan["auction_price_plan_id"].")";
		  		}
		  		elseif ($this->is_classifieds())
		  		{
		  			$class_price_plan = $this->get_price_plan_from_group($db,$this->classified_variables["group_id"]);
		  			$this->sql_query = "insert into ".$this->user_groups_price_plans_table."
					(id,group_id,price_plan_id)
					values
					(".$user_id.",".$this->classified_variables["group_id"].",".$class_price_plan["price_plan_id"].")";
		  		}
		  		else return false;
		  		$group_result = $db->Execute($this->sql_query);
				if (!$group_result)
				{
					$this->sql_query;
					$this->site_error($db->ErrorMsg());
					$this->error["confirm"] =urldecode($this->messages[229]);
					return false;
				}
			}
		}
		$this->new_user_id = $user_id;
		return true;
	} //end of function insert_new_user

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%


	function get_price_plan_from_group($db,$group_id=0,$item=0)
	{
		if (!$group_id)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		$this->sql_query = "select * from ".$this->classified_groups_table." where group_id = ".$group_id;
		$group_price_plan_result = $db->Execute($this->sql_query);

		//echo $this->sql_query." is get_price_plan query<br>\n";
		if (!$group_price_plan_result)
		{
			$this->error_message = $this->internal_error_message;
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($group_price_plan_result->RecordCount() == 1)
		{
			$show_group_price_plan = $group_price_plan_result->FetchRow();
			if ($item)
			{
				//GET AUCTION PRICE PLAN
				$this->sql_query = "select * from ".$this->price_plan_table." where price_plan_id = ".$show_group_price_plan["auction_price_plan_id"];
				$auction_price_plan_result = $db->Execute($this->sql_query);

				//$this->sql_query." is get_price_plan query<br>\n";
				if (!$auction_price_plan_result)
				{
					$this->error_message = $this->internal_error_message;
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($auction_price_plan_result->RecordCount()==1)
				{
					$show_price_plan = $auction_price_plan_result->FetchRow();
				}
				else
				{
					return false;
				}
			}
			else
			{
				//GET CLASSIFIED PRICE PLAN
				$this->sql_query = "select * from ".$this->price_plan_table." where price_plan_id = ".$show_group_price_plan["price_plan_id"];

				$price_plan_result = $db->Execute($this->sql_query);

				//$this->sql_query." is get_price_plan query<br>\n";
				if (!$price_plan_result)
				{
					$this->error_message = $this->internal_error_message;
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($price_plan_result->RecordCount()==1)
				{
					$show_price_plan = $price_plan_result->FetchRow();
				}
				else
				{
					return false;
				}
			}
			return $show_price_plan;
		}
		else
		{
			//just display the user_id
			return false;
		}
	} //end of function get_price_plan_from_group

//########################################################################

	function restart_classified_form($db,$classified_id=0)
	{
		if ($classified_id)
		{

			$sql_query = "select * from ".$this->classifieds_table." where id = ".$classified_id;
			$result = $db->Execute($sql_query);
			//echo $sql_query." is the bracket display query<br>\n";
			if (!$result)
			{
				echo $sql_query." is the bracket display query<br>\n";
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
				if ($show["live"] == 0 )
				{
					//echo $info." is the entered variables<br>\n";
					//echo count($info)." is the count of entered variable<Br>\n";
					if (!$this->admin_demo())$this->body .= "<form action=index.php?a=54&b=".$classified_id." method=post>\n\t";
					$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% class=row_color1>\n\t";
					$this->title = "Restart Listing Form";
					$this->description = "Choose a new ending date for this listing through the form below.  The start date will automatically be set to the current time.";
					$this->body .= "<tr class=row_color2>\n\t<td class=medium_font>\n\tnew start day - ";
					$current_time = $this->shifted_time();
					$this->body .= date("M d, Y H:i:s", $current_time);
					$this->body .= " (now)</font></td>\n\t</tr>";
					$this->body .= "<tr><td class=medium_font>\n\tnew ending day - ";
					$this->get_fine_date_select("c[end_year]","c[end_month]","c[end_day]","c[end_hour]","c[end_minute]",
						date("Y",$current_time),date("n",$current_time),date("j",$current_time),date("G",$current_time),date("i",$current_time));

					$this->body .= "</td>\n</tr>\n";
					if ($show['item_type']==2)
					{
						//$this->body .= "<tr class=row_color2><td class=medium_font>\n\tRemove Current Bids Price:&nbsp;<br>";
						$this->body .= "<tr><td><input type=hidden name=c[remove_current_bids] value=1></td>\n</tr>\n";
						//$this->body .= "yes<br><input type=radio name=c[remove_current_bids] checked value=0>no</td>\n</tr>\n";

						$this->body .= "<tr class=row_color2><td class=medium_font>\n\tStarting Bid:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=text name=c[starting_bid] value=".$show->STARTING_BID.">";
						$this->body .= "</td>\n</tr>\n";
						$this->body .= "<tr class=row_color1><td class=medium_font>\n\tReserve Price:&nbsp;&nbsp;<input type=text name=c[reserve_price] value=".$show->RESERVE_PRICE.">";
						$this->body .= "</td>\n</tr>\n";
						$this->body .= "<tr class=row_color2><td class=medium_font>\n\tBuy Now Price:&nbsp;<input type=text name=c[buy_now] value=".$show->BUY_NOW.">";
						$this->body .= "</td>\n</tr>\n";

						$this->body .= "<tr class=row_color2><td class=medium_font>\n\t<input type=checkbox checked name=c[reset_viewed_count] value=1> reset viewed count: ";
						$this->body .= "</td>\n</tr>\n";
					}

					$this->body .= "<tr class=row_color2><td class=medium_font>\n\t<input type=checkbox name=c[featured_ad] value=1> make featured listing: ";
					$this->body .= "</td>\n</tr>\n";
					$this->body .= "<tr class=row_color2><td class=medium_font>\n\t<input type=checkbox name=c[featured_ad_2] value=1> make featured listing level 2: ";
					$this->body .= "</td>\n</tr>\n";
					$this->body .= "<tr class=row_color2><td class=medium_font>\n\t<input type=checkbox name=c[featured_ad_3] value=1> make featured listing level 3: ";
					$this->body .= "</td>\n</tr>\n";
					$this->body .= "<tr class=row_color2><td class=medium_font>\n\t<input type=checkbox name=c[featured_ad_4] value=1> make featured listing level 4: ";
					$this->body .= "</td>\n</tr>\n";
					$this->body .= "<tr class=row_color2><td class=medium_font>\n\t<input type=checkbox name=c[featured_ad_5] value=1> make featured listing level 5: ";
					$this->body .= "</td>\n</tr>\n";
					$this->body .= "<tr><td class=medium_font>\n\t<input type=checkbox name=c[better_placement] value=1> make better_placement listing: ";
					$this->body .= "</td>\n</tr>\n";
					$this->body .= "<tr class=row_color2><td class=medium_font>\n\t <input type=checkbox name=c[bolding] value=1> make bolded listing:";
					$this->body .= "</td>\n</tr>\n";
					$this->body .= "<tr><td class=medium_font>\n\t<input type=checkbox name=c[attention_getter] value=1> attach attention getter to listing: ";
					$this->body .= "<table width=100% align=center>\n";
					$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 10";
					//echo $this->sql_query."<br>\n";
					$attention_getters_result = $db->Execute($this->sql_query);
					if (!$attention_getters_result)
					{
						echo $this->sql_query."<br>\n";
						$this->setup_error = $this->messages[453];
						return false;
					}
					elseif ($attention_getters_result->RecordCount() > 0)
					{
						//attention getters
						while ($show_attention_getter = $attention_getters_result->FetchRow())
						{
							$this->body .= "<tr>\n\t<td valign=middle class=cost_data_values ><input type=radio name=c[attention_getter_choice] value=".$show_attention_getter["choice_id"];
							$this->body .= "><img src=\"../".$show_attention_getter["value"]."\" border=0 hspace=2></td>\n\t</tr>\n";
						}

						$this->body .= "</table></td>\n\t</tr>\n\t";
					}

					$this->body .= "</td>\n</tr>\n";

					if (!$this->admin_demo()) $this->body .= "<tr class=row_color2>\n\t<td class=small_font>\n\t<input type=submit name=search value=\"Save\"></td>\n</tr>\n";
					$this->body .= "</table>\n\t</form>";

					/*
					echo "<form action=index.php?a=16&b[search_type]=5 method=post>\n\t";
					echo "<table cellpadding=3 cellspacing=0 border=0 width=100% class=row_color1>\n\t";
					echo "<tr class=row_color2>\n\t<td class=medium_font>\n\tSearch for new listing</td>\n</tr>\n";
					echo "<tr>\n\t<td class=small_font>\n\tid to search for <input type=text name=b[classified_id]></font></td>\n</tr>\n";
					echo "<tr class=row_color2>\n\t<td class=small_font>\n\t<input type=submit name=search value=\"Save\"></td>\n</tr>\n";
					echo "</table>\n\t</form>";
					*/
					return true;
				}
				else
				{
					//this auction is currently live --- do not restart
					if (!$this->admin_demo())$this->body .= "<form action=index.php?a=54&b=".$classified_id." method=post>\n\t";
					$this->body .= "<table cellpadding=1 cellspacing=0 border=0 width=100%>\n\t";
					$this->title = "Users / User Groups > UserData > Extend/Upgrade Listing";
					$this->description = "This listing is currently live and cannot be restarted.";

					$this->body .= "<tr><td class=medium_font>\n\tnew ending day - ";
					$this->get_fine_date_select("c[end_year]","c[end_month]","c[end_day]","c[end_hour]","c[end_minute]",
						date("Y",$show["ends"]),date("n",$show["ends"]),date("j",$show["ends"]),date("G",$show["ends"]),date("i",$show["ends"]));

					$this->body .= "</td>\n</tr>\n";

					if($show['item_type'] ==2)
					{
						//$this->body .= "<tr class=row_color2><td class=medium_font>\n\tRemove Current Bids Price:&nbsp;<br>";
						$this->body .= "<tr><td><input type=hidden name=c[remove_current_bids] value=1></td>\n</tr>\n";
						//$this->body .= "yes<br><input type=radio name=c[remove_current_bids] checked value=0>no</td>\n</tr>\n";

						$this->body .= "<tr class=row_color1><td class=medium_font>\n\tstarting bid  <input type=text name=c[starting_bid] value=\"".$show['starting_bid']."\"></td>\n</tr>\n";
						$this->body .= "<tr class=row_color1><td class=medium_font>\n\treserve price <input type=text name=c[reserve_price] value=\"".$show['reserve_price']."\"></td>\n</tr>\n";
						$this->body .= "<tr class=row_color1><td class=medium_font>\n\tbuy now <input type=text name=c[buy_now] value=\"".$show['buy_now']."\"></td>\n</tr>\n";
						//$this->body .= "<tr class=row_color1><td class=medium_font>\n\tcurrent bid <input type=text name=c[current_bid] value=\"".$show['current_bid']."\"></td>\n</tr>\n";
					}

					$this->body .= "<tr class=row_color2><td class=medium_font>\n\t<input type=checkbox ";
					if ($show["featured_ad"])
						$this->body .= "checked";
					$this->body .= " name=c[featured_ad] value=1> make featured listing: ";
					if ($show["featured_ad"])
						$this->body .= " <b>currently has this feature</b>";
					$this->body .= "</td>\n</tr>\n";

					$this->body .= "<tr class=row_color2><td class=medium_font>\n\t<input type=checkbox ";
					if ($show["featured_ad_2"])
						$this->body .= "checked";
					$this->body .= " name=c[featured_ad_2] value=1> make featured listing level 2: ";
					if ($show["featured_ad_2"])
						$this->body .= " <b>currently has this feature</b>";
					$this->body .= "</td>\n</tr>\n";

					$this->body .= "<tr class=row_color2><td class=medium_font>\n\t<input type=checkbox ";
					if ($show["featured_ad_3"])
						$this->body .= "checked";
					$this->body .= " name=c[featured_ad_3] value=1> make featured listing level 3: ";
					if ($show["featured_ad_3"])
						$this->body .= " <b>currently has this feature</b>";
					$this->body .= "</td>\n</tr>\n";

					$this->body .= "<tr class=row_color2><td class=medium_font>\n\t<input type=checkbox ";
					if ($show["featured_ad_4"])
						$this->body .= "checked";
					$this->body .= " name=c[featured_ad_4] value=1> make featured listing level 4: ";
					if ($show["featured_ad_4"])
						$this->body .= " <b>currently has this feature</b>";
					$this->body .= "</td>\n</tr>\n";
					$this->body .= "<tr class=row_color2><td class=medium_font>\n\t<input type=checkbox ";
					if ($show["featured_ad_5"])
						$this->body .= "checked";
					$this->body .= " name=c[featured_ad_5] value=1> make featured listing level 5: ";
					if ($show["featured_ad_5"])
						$this->body .= " <b>currently has this feature</b>";
					$this->body .= "</td>\n</tr>\n";

					$this->body .= "<tr><td class=medium_font>\n\t<input type=checkbox ";
					if ($show["better_placement"])
						$this->body .= "checked";
					$this->body .= " name=c[better_placement] value=1> make better placement listing: ";
					if ($show["better_placement"])
						$this->body .= " <b>currently has this feature</b>";
					$this->body .= "</td>\n</tr>\n";
					$this->body .= "<tr class=row_color2><td class=medium_font>\n\t <input type=checkbox ";
					if ($show["bolding"])
						$this->body .= "checked";
					$this->body .= " name=c[bolding] value=1> make bolded listing:";
					if ($show["bolding"])
						$this->body .= " <b>currently has this feature</b>";
					$this->body .= "</td>\n</tr>\n";
					$this->body .= "<tr><td class=medium_font>\n\t<input type=checkbox ";
					if ($show["attention_getter"])
						$this->body .= "checked";
					$this->body .= " name=c[attention_getter] value=1> attach attention getter to listing: ";
					if ($show["attention_getter"])
						$this->body .= " <b>currently has this feature</b>";
					$this->body .= "<table width=100% align=center>\n";
					$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 10";
					//echo $this->sql_query."<br>\n";
					$attention_getters_result = $db->Execute($this->sql_query);
					if (!$attention_getters_result)
					{
						echo $this->sql_query."<br>\n";
						$this->setup_error = $this->messages[453];
						return false;
					}
					elseif ($attention_getters_result->RecordCount() > 0)
					{
						//attention getters
						while ($show_attention_getter = $attention_getters_result->FetchRow())
						{
							//echo $show["attention_getter"]." - ".$show["attention_getter_url"]." - ".$show_attention_getter["value"]." - ".strcmp($show["attention_getter_url"],$show_attention_getter["value"])."<bR>\n";
							$this->body .= "<tr>\n\t<td valign=middle class=cost_data_values ><input type=radio name=c[attention_getter_choice] value=".$show_attention_getter["choice_id"];
							if (strcmp($show["attention_getter_url"],$show_attention_getter["value"]) == 0)
								$this->body .= " checked";
							$this->body .= "><img src=\"../".$show_attention_getter["value"]."\" border=0 hspace=2></td>\n\t</tr>\n";
						}

						$this->body .= "</table></td>\n\t</tr>\n\t";
					}

					$this->body .= "<input type=hidden name=user_id value=".$show['seller'].">\n\t";
					if (!$this->admin_demo()) $this->body .= "<tr class=row_color2>\n\t<td class=small_font align=center>\n\t<input type=submit name=search value=\"Save\"></td>\n</tr>\n";
					$this->body .= "</table></form>\n\t";

					/*
					echo "<form action=index.php?a=16&b[search_type]=5 method=post>\n\t";
					echo "<table cellpadding=1 cellspacing=0 border=0 width=100%>\n\t";
					echo "<tr>\n\t<td class=small_font>\n\tid to search for <input type=text name=b[classified_id]></font></td>\n</tr>\n";
					echo "<tr>\n\t<td class=small_font align=center>\n\t<input type=submit name=search value=\"Save\"></td>\n</tr>\n";
					echo "</table>\n\t</form>";
					*/
					return true;
				}
			}
			else
				return false;
		}
		else
			return false;

	} //end of function restart_classified_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function restart_classified($db,$classified_id=0,$classified_info=0)
	{
		if (($classified_id) && ($classified_info))
		{
			$sql_query = "select * from ".$this->classifieds_table." where id = ".$classified_id;
			$result = $db->Execute($sql_query);
			//echo $sql_query." is the bracket display query<br>\n";
			if (!$result)
			{
				echo $sql_query." is the bracket display query<br>\n";
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
			}
			$end_date = mktime($classified_info["end_hour"],$classified_info["end_minute"],0,$classified_info["end_month"],$classified_info["end_day"],$classified_info["end_year"]);
//////////////////////classified
			if ($show['item_type']==1)
			{
				if ($show['live'])
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						ends = ".$end_date.",
						featured_ad = 0,
						featured_ad_2 = 0,
						featured_ad_3 = 0,
						featured_ad_4 = 0,
						featured_ad_5 = 0,
						better_placement = 0,
						bolding = 0,
						attention_getter = 0,
						attention_getter_url = \"\",
						live = 1
						where id = ".$classified_id;
				}
				else
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						date =  ".$this->shifted_time().",
						ends = ".$end_date.",
						live = 1
						where id = ".$classified_id;
				}
				$result = $db->Execute($this->sql_query);

				if (!$result)
				{
					echo $this->sql_query." is the query<br>\n";
					return false;
				}

				if ($classified_info["featured_ad"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad = 1
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if ($classified_info["featured_ad_2"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_2 = 1
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if ($classified_info["featured_ad_3"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_3 = 1
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if ($classified_info["featured_ad_4"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_4 = 1
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if ($classified_info["featured_ad_5"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_5 = 1
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if ($classified_info["better_placement"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						better_placement = 1
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if ($classified_info["bolding"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						bolding = 1
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if (($classified_info["attention_getter"] == 1) && ($classified_info["attention_getter_choice"]))
				{
					$this->sql_query = "select * from ".$this->choices_table." where choice_id = ".$classified_info["attention_getter_choice"];
					$attention_getter_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$attention_getter_result)
					{
						return false;
					}
					elseif ($attention_getter_result->RecordCount() == 1)
					{
						$show_attention_getter = $attention_getter_result->FetchRow();
						$attention_getter_url = $show_attention_getter['value'];
					}
					else
					{
						$site->renew_upgrade_variables["attention_getter_upgrade"] = 0;
						$attention_getter_url = "";
					}

					$this->sql_query = "update ".$this->classifieds_table." set
						attention_getter = 1,
						attention_getter_url = \"".$attention_getter_url."\"
						where id = ".$classified_id;
					$update_attention_getter_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$update_attention_getter_result)
					{
						return false;
					}
				}

				//get category count
				if (!$show['live'])
				{
					$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);
					//echo $this->sql_query." is the entered variables query<br>\n";
					if (!$result)
					{
						$this->database_error($db->ErrorMsg(),$sql_query);
						return false;
					}
					elseif ($result->RecordCount() == 1)
					{
						$show = $result->FetchRow();
						$this->update_category_count($db,$show['category']);
					}
				}

				return true;
			}

///////////////////////auction
			if ($show['item_type']==2)
			{
				if ($show["live"])
				{
					/*if ($classified_info["current_bid"] == 0.00)
					{
						if ($this->debug_user) echo "minimum bid is set to starting bid<BR>\n";
						$minimum_bid = $classified_info["starting_bid"];
					}
					else
					{
						if ($this->debug_user) echo "minimum bid is set to starting bid<BR>\n";
						$minimum_bid = $classified_info["current_bid"];
					}*/
					$this->sql_query = "update ".$this->classifieds_table." set
						ends = ".$end_date.",
						featured_ad = 0,
						featured_ad_2 = 0,
						featured_ad_3 = 0,
						featured_ad_4 = 0,
						featured_ad_5 = 0,
						better_placement = 0,
						bolding = 0,
						attention_getter = 0,
						attention_getter_url = \"\",
						starting_bid = \"".$classified_info["starting_bid"]."\",
						reserve_price = \"".$classified_info["reserve_price"]."\",
						buy_now =  \"".$classified_info["buy_now"]."\",
						live = 1
						where id = ".$classified_id;
				}
				else
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						date = ".$this->shifted_time().",
						ends = ".$end_date.",
						starting_bid = ".$classified_info["starting_bid"].",
						minimum_bid = ".$classified_info["starting_bid"].",
						current_bid = 0,
						reserve_price = ".$classified_info["reserve_price"].",
						buy_now =  ".$classified_info["buy_now"].",
						live = 1
						where id = ".$classified_id;
				}
				$result = $db->Execute($this->sql_query);

				if (!$result)
				{
					echo $this->sql_query." is the query<br>\n";
					return false;
				}

				if ($classified_info["remove_current_bids"] == 1 && !$show["live"])
				{
					// Remove current bids
					$this->sql_query = "delete from ".$this->bid_table." where auction_id = ".$classified_id;
					$result = $db->Execute($this->sql_query);
					if ($this->debug_user) echo $this->sql_query."<br>\n";
					if (!$result)
					{
						if ($this->debug_user) echo $this->sql_query."<br>\n";
						return false;
					}
				}

				// Remove final price, reset min bid and current_bid
				$this->sql_query = "update ".$this->classifieds_table." set
					final_price = 0.00
					where id = ".$classified_id;
				$result = $db->Execute($this->sql_query);
				if ($this->debug_user) echo $this->sql_query."<br>\n";
				if (!$result)
				{
					if ($this->debug_user) echo $this->sql_query."<br>\n";
					return false;
				}

				if ($classified_info["reset_viewed_count"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						viewed = 0,
						responded = 0,
						forwarded = 0
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if ($classified_info["featured_ad"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad = 1
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if ($classified_info["featured_ad_2"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_2 = 1
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if ($classified_info["featured_ad_3"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_3 = 1
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if ($classified_info["featured_ad_4"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_4 = 1
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if ($classified_info["featured_ad_5"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_5 = 1
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if ($classified_info["better_placement"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						better_placement = 1
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if ($classified_info["bolding"] == 1)
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						bolding = 1
						where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);

					if (!$result)
					{
						echo $this->sql_query." is the query<br>\n";
						return false;
					}
				}

				if (($classified_info["attention_getter"] == 1) && ($classified_info["attention_getter_choice"]))
				{
					$this->sql_query = "select * from ".$this->choices_table." where choice_id = ".$classified_info["attention_getter_choice"];
					$attention_getter_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$attention_getter_result)
					{
						return false;
					}
					elseif ($attention_getter_result->RecordCount() == 1)
					{
						$show_attention_getter = $attention_getter_result->FetchRow();
						$attention_getter_url = $show_attention_getter["value"];
					}
					else
					{
						$site->renew_upgrade_variables["attention_getter_upgrade"] = 0;
						$attention_getter_url = "";
					}

					$this->sql_query = "update ".$this->classifieds_table." set
						attention_getter = 1,
						attention_getter_url = \"".$attention_getter_url."\"
						where id = ".$classified_id;
					$update_attention_getter_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$update_attention_getter_result)
					{
						return false;
					}
				}

				//get category count
				if (!$show["live"])
				{
					$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$classified_id;
					$result = $db->Execute($this->sql_query);
					//echo $this->sql_query." is the entered variables query<br>\n";
					if (!$result)
					{
						$this->database_error($db->ErrorMsg(),$sql_query);
						return false;
					}
					elseif ($result->RecordCount() == 1)
					{
						$show = $result->FetchRow();
						$this->update_category_count($db,$show["category"]);
					}
				}

				return true;
			}
		}
		else
			return false;

	} //end of function restart_classified

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function increase_image_count($db,$classified_id=0)
	{
		if ($classified_id)
		{

			$sql_query = "select image from ".$this->classifieds_table." where id = ".$classified_id;
			$result = $db->Execute($sql_query);
			//echo $sql_query." is the bracket display query<br>\n";
			if (!$result)
			{
				echo $sql_query." is the bracket display query<br>\n";
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
			}
			$new_image_count = ($show["image"] + 1);

			$this->sql_query = "update ".$this->classifieds_table." set
				image = ".$new_image_count."
				where id = ".$classified_id;
			$result = $db->Execute($this->sql_query);

			if (!$result)
			{
				echo $this->sql_query." is the query<br>\n";
				return false;
			}

			return true;
		}
		else
			return false;

	} //end of function increase_image_count

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%


	function get_filter_dropdown($db,$name,$filter_id=0,$filter_level=0)
	{
		empty($this->filter_dropdown_id_array);
		empty($this->filter_dropdown_name_array);
		$this->filter_dropdown_name_array = array();
		$this->filter_dropdown_id_array = array();

		$this->get_subfilters_for_dropdown($db,0,$filter_level);

		//build the select statement
		//array_reverse($this->filter_dropdown_name_array);
		//array_reverse($this->filter_dropdown_id_array);


		$this->body .= "<select name=".$name.">\n\t\t";
		foreach($this->filter_dropdown_name_array as $key => $value)
		{
			$this->body .= "<option ";
			if ($this->filter_dropdown_id_array[$key] == $filter_id)
				$this->body .= "selected";
			$this->body .= " value=".$this->filter_dropdown_id_array[$key].">".urldecode($this->filter_dropdown_name_array[$key])."</option>\n\t\t";
		}
		$this->body .= "</select>\n\t";

     	return true;

	} //end of function get_filter_dropdown

//##################################################################################

	function get_subfilters_for_dropdown($db,$filter_id=0,$filter_level=0)
	{
		//echo $filter_level." is filter_level in sub<br>\n";
		$this->sql_query = "select * from ".$this->classified_filters_table."
			where ".$this->classified_filters_table.".parent_id = ".$filter_id."
			order by display_order,filter_name";
		$filter_result =  $db->Execute($this->sql_query);

		if (!$filter_result)
		{

			$this->error_message = $this->messages[2052];
			return false;
		}
		elseif ($filter_result->RecordCount() > 0)
		{
			$this->stage++;
			while ($show_filter = $filter_result->FetchRow())
			{
				$pre_stage = "";
				for ($i=1;$i<=$this->stage;$i++)
				{
					$pre_stage .= "&nbsp;&nbsp;&nbsp;";
				}
				//echo $show_filter["filter_level"]." is the filters level<BR>\n";
				if ($filter_id != 0)
				{
					if ($show_filter["filter_level"] == $filter_level)
					{
						array_push($this->filter_dropdown_name_array, $pre_stage.$show_filter["filter_name"]."*");
						array_push($this->filter_dropdown_id_array,$show_filter["filter_id"]);
					}
					else
					{
						array_push($this->filter_dropdown_name_array, $pre_stage.$show_filter["filter_name"]);
						array_push($this->filter_dropdown_id_array,$show_filter["filter_id"]);
					}
				}
				else
				{
					if ($show_filter["filter_level"] == $filter_level)
					{
						array_push($this->filter_dropdown_name_array, $show_filter["filter_name"]."*");
						array_push($this->filter_dropdown_id_array,$show_filter["filter_id"]);
					}
					else
					{
						array_push($this->filter_dropdown_name_array, $show_filter["filter_name"]);
						array_push($this->filter_dropdown_id_array,$show_filter["filter_id"]);
					}
				}
				$this->get_subfilters_for_dropdown($db,$show_filter["filter_id"],$filter_level);
			}
			$this->stage--;
		}
		return;
	} //end of function get_subfilters_for_dropdown

//##################################################################################

	function edit_account_balance($db,$user_id=0)
	{
		if ($user_id)
		{
			$user_data = $this->get_user_data($db,$user_id);
			if ($user_data)
			{
				//display the form to edit the users balance
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=76&b=".$user_id." method=post>\n";
				$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center>\n";
				$this->title = "Edit This Users Balance";
				$this->description = "Below is the users balance that you selected to edit.  Make the
					changes to the current balance you need and click the \"save changes\" button.  The balance you enter below will
					become the users new balance.";

				$this->body .= "<tr>\n\t<td align=right class=medium_font>current balance: </td>\n\t";
				if ($user_data["account_balance"] == "")
					$balance = 0;
				else
					$balance = $user_data["account_balance"];
				$this->body .= "<td class=medium_font><input type=text name=c value=".$balance." ></td></tr>";

				if (!$this->admin_demo()) $this->body .= "<tr><td colspan=2 align=center><input type=submit value=\"Save\"></td></tr>";
				$this->body .= "</table></form>";
				return true;
			}
			return false;
		}
		else
		{
			return false;
		}
	} //end of function edit_account_balance

//##################################################################################

	function update_account_balance($db,$user_id=0,$amount=0)
	{
		if ($user_id)
		{
			$this->sql_query = "update ".$this->userdata_table." set
				account_balance = ".$amount."
				where id = ".$user_id;
			$balance_result =  $db->Execute($this->sql_query);

			if (!$balance_result)
			{

				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	} //end of function update_account_balance

//##################################################################################

} //end of class Admin_user_management

?>