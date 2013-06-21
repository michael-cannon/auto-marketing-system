<?php
class Admin_classified_reports extends Admin_site
{
    function Admin_classified_reports($db, $product_configuration=0)
    {
        //constructor
        $this->Admin_site($db, $product_configuration);

        // Set the admin_icon variable for the admin icon
        $this->admin_icon = "admin_images/menu_pages.gif";
    }

    function listing_home($db)
    {
        $this->title = 'Classified reports';
        $this->description = 'The table below displays a current list of classifieds.';

        $this->body .= "<strong>Classified Reports</strong><hr size=1 color=#ff9900 align=left width=100%>";
        $this->body .= "<table width=50% align=center cellspacing=0 cellpadding=3>\n<tr>\n\t<td align=left>\n\t<ul>";
        $this->body .= "<li type=square><a href=index.php?a=301&b=1&c=1><span class=medium_font><font color=000000>Show Closed Classified Report</span></a></li>\n";
        $this->body .= "<li type=square><a href=index.php?a=301&b=2&c=1><span class=medium_font><font color=000000>Show Open Classified Report</span></a></li></ul>\n";
        $this->body .= "<ul><li type=square><a href=index.php?a=301&b=1&c=2><span class=medium_font><font color=000000>Download Closed Classified Report</span></a></li>\n";
        $this->body .= "<li type=square><a href=index.php?a=301&b=2&c=2><span class=medium_font><font color=000000>Download Open Classified Report</span></a></li>\n";
        $this->body .= "</ul>\n</td\n></tr>\n</table>\n";

        return(1);
    }

    function show_closed_classifieds_report($db)
    {
        $this->title = 'Listing of closed classifieds';
        $this->description = 'The table below displays a current list of closed classifieds.';

        $query =
        "SELECT c.id, c.title, c.price, c.optional_field_18, c.optional_field_16
        FROM geodesic_classifieds c
        WHERE c.live=0 AND c.item_type = 1
        ORDER BY c.id";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            $this->body .= "<span class=medium_font><a href=index.php?a=300&b=21&c=2>Download Closed Classifieds Report</a></span><p>";
            $this->body .= '<table width = 100% cellpadding=3 cellspacing=1 class=row_color_red>';
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left colspan=5 class=medium_font_light><strong>Closed Classifieds Report</strong></td>";
            $this->body .= "</tr>";
            $this->body .= "<tr class=row_color_red height=30>";
            $this->body .= "<td align=left class=small_font_light><strong>Id</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Title</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Stock Number</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>VIN number</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Price</strong></td>";
            $this->body .= "</tr>";

            while ($row = $result->FetchRow())
            {
                if($i%2) $bgcolor = '#CCCCFF';
                else $bgcolor = 'FFFFCC';

                $id = $row['id'];
                $title = $row['title'];
                $price = $row['price'];
                $stockNumber = $row['optional_field_18'];
                $vin = $row['optional_field_16'];

                $this->body .= "<tr>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($id)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($title)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($stockNumber)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($vin)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($price)."</td>\n";

                $this->body .= "</tr>\n";

                $i ++;
            }
            $this->body .= '</table>';
        }
        else
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no closed classifieds.</strong></span>\n";
        }

        return(1);
    }

    function show_open_classifieds_report($db)
    {
        $this->title = 'Listing';
        $this->description = 'The table below displays a current list of open classifieds.';

        $query =
        "SELECT c.id, c.title, c.price, c.optional_field_18, c.optional_field_16
        FROM geodesic_classifieds c
        WHERE c.live=1 AND c.item_type = 1
        ORDER BY c.id";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            $this->body .= "<span class=medium_font><a href=index.php?a=301&b=1&c=2>Download Open Classifieds Report</a></span><p>";
            $this->body .= '<table width = 100% cellpadding=3 cellspacing=1 class=row_color_red>';
            $this->body .= "<tr bgcolor=#000099>";
            $this->body .= "<td align=left colspan=5 class=medium_font_light><strong>Open Classifieds Report</strong></td>";
            $this->body .= "</tr>";
            $this->body .= "<tr class=row_color_red height=30>";
            $this->body .= "<td align=left class=small_font_light><strong>Id</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Title</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Stock Number</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>VIN number</strong></td>";
            $this->body .= "<td align=left class=small_font_light><strong>Price</strong></td>";
            $this->body .= "</tr>";

            while ($row = $result->FetchRow())
            {
                if($i%2) $bgcolor = '#CCCCFF';
                else $bgcolor = 'FFFFCC';

                $id = $row['id'];
                $title = $row['title'];
                $price = $row['price'];
                $stockNumber = $row['optional_field_18'];
                $vin = $row['optional_field_16'];

                $this->body .= "<tr>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($id)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($title)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($stockNumber)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($vin)."</td>\n";
                $this->body .= "<td bgcolor=$bgcolor align=left class=small_font>".urldecode($price)."</td>\n";

                $this->body .= "</tr>\n";

                $i ++;
            }
            $this->body .= '</table>';
        }
        else
        {
            $this->body .= "<span class=medium_font><strong>Sorry! There are no open classifieds.</strong></span>\n";
        }

        return(1);
    }

    function download_closed_classifieds_report($db)
    {
        $this->title = 'Listing of closed classifieds';
        $this->description = 'The table below displays a current list of closed classifieds.';

        $query =
        "SELECT c.id, c.title, c.price, c.optional_field_18, c.optional_field_16
        FROM geodesic_classifieds c
        WHERE c.live=0 AND c.item_type = 1
        ORDER BY c.id";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            $solt = date(Ymd);
            $filename = 'ams-closed-classifieds-' . $solt . '.csv';
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
                $price = $row['price'];
                $stockNumber = urldecode($row['optional_field_18']);
                $vin = urldecode($row['optional_field_16']);

                $text .= "$id, ";
                $text .= "$title, ";
                $text .= "$price, ";
                $text .= "$vin, ";
                $text .= "$stockNumber";

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
            $this->body .= "<span class=medium_font><strong>Sorry! There are no closed classifieds.</strong></span>";
            return(0);
        }
    }

    function download_open_classifieds_report($db)
    {
        $this->title = 'Listing of open classifieds';
        $this->description = 'The table below displays a current list of open classifieds.';

        $query =
        "SELECT c.id, c.title, c.price, c.optional_field_18, c.optional_field_16
        FROM geodesic_classifieds c
        WHERE c.live=1 AND c.item_type = 1
        ORDER BY c.id";

        $result = $db->Execute($query);
        $recordCount = $result->RecordCount();

        if($recordCount > 0)
        {
            $solt = date(Ymd);
            $filename = 'ams-open-classifieds-' . $solt . '.csv';
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
                $price = $row['price'];
                $stockNumber = urldecode($row['optional_field_18']);
                $vin = urldecode($row['optional_field_16']);

                $text .= "$id, ";
                $text .= "$title, ";
                $text .= "$price, ";
                $text .= "$vin, ";
                $text .= "$stockNumber";

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
            $this->body .= "<span class=medium_font><strong>Sorry! There are no open classifieds.</strong></span>";
            return(0);
        }
    }
}

?>