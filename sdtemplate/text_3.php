<?php
        $h = ($_REQUEST['h']) ? $_REQUEST['h']:10;
        $m = $_REQUEST['m'];

        $hfieldstart = '<select name=hstart>';
        $hfieldconvert = '<select name=hconvert>';
        $hfieldend = '<select name=hend>';
        for($i = 0; $i < 24; $i ++)
        {
            if($i < 13)
            {
                if($i == 12)
                {
                    if($i == $h)
                    {
                    	$hfieldstart .= "<option selected value=".$i.">Noon</option>";
                        $hfieldconvert .= "<option selected value=".$i.">Noon</option>";
                        $hfieldend .= "<option selected value=".$i.">Noon</option>";
                    }
                    else
                    {
                        $hfieldstart .= "<option value=".$i.">Noon</option>";
                        $hfieldconvert .= "<option value=".$i.">Noon</option>";
                    	$hfieldend .= "<option value=".$i.">Noon</option>";
                    }
                }
                elseif($i == 0)
                {
                    if($i == $h)
                    {
                        $hfieldstart .= "<option selected value=".$i.">Midnight</option>";
                        $hfieldconvert .= "<option selected value=".$i.">Midnight</option>";
                        $hfieldend .= "<option selected value=".$i.">Midnight</option>";
                    }
                    else
                    {
                        $hfieldstart .= "<option value=".$i.">Midnight</option>";
                        $hfieldconvert .= "<option value=".$i.">Midnight</option>";
                        $hfieldend .= "<option value=".$i.">Midnight</option>";
                    }
                }
                else
                {
                    if($i == $h)
                    {
                    	$hfieldstart .= "<option selected value=".$i.">".$i." AM </option>";
                        $hfieldconvert .= "<option selected value=".$i.">".$i." AM </option>";
                        $hfieldend .= "<option selected value=".$i.">".$i." AM </option>";
                    }
                    else
                    {
                    	$hfieldstart .= "<option value=".$i.">".$i." AM</option>";
                    	$hfieldconvert .= "<option value=".$i.">".$i." AM</option>";
                    	$hfieldend .= "<option value=".$i.">".$i." AM</option>";
                    }
                }
            }
            else
            {
                if(1)
                {
                    if($i == $h)
                    {
                    	$hfieldstart .= "<option selected value=".$i.">".($i-12)." PM</option>";
                        $hfieldconvert .= "<option selected value=".$i.">".($i-12)." PM</option>";
                        $hfieldend .= "<option selected value=".$i.">".($i-12)." PM</option>";
                    }
                    else
                    {
                    	$hfieldstart .= "<option value=".$i.">".($i-12)." PM</option>";
                    	$hfieldconvert .= "<option value=".$i.">".($i-12)." PM</option>";
                        $hfieldend .= "<option value=".$i.">".($i-12)." PM</option>";
                    }
                }
            }
        }
        $hfieldstart .= '</select>';
        $hfieldconvert .= '</select>';
        $hfieldend .= '</select>';

        $mfieldstart = '<select name=mstart>';
        $mfieldconvert = '<select name=mconvert>';
        $mfieldend = '<select name=mend>';

        $minutes = Array('00', '15', '30', '45', '59');
        $minutesv = Array('0', '15', '30', '45', '59');
        for($i = 0; $i < count($minutes); $i ++)
        {
            if($i == $m)
            {
            	$mfieldstart .= "<option selected value=".$minutesv[$i].">:".$minutes[$i]."</option>";
                $mfieldconvert .= "<option selected value=".$minutesv[$i].">:".$minutes[$i]."</option>";
                $mfieldend .= "<option selected value=".$minutesv[$i].">:".$minutes[$i]."</option>";
            }
            else
            {
            	$mfieldstart .= "<option value=".$minutesv[$i].">:".$minutes[$i]."</option>";
            	$mfieldconvert .= "<option value=".$minutesv[$i].">:".$minutes[$i]."</option>";
                $mfieldend .= "<option value=".$minutesv[$i].">:".$minutes[$i]."</option>";
            }
        }
        $mfieldstart .= '</select>';
        $mfieldconvert .= '</select>';
        $mfieldend .= '</select>';

    	echo "
        <style type='text/css'>
            /* Date */
            h2 {font-family: helvetica,arial,verdana,tahoma; font-size:22px; color:#0077cc;}
            td.first {background-color:#fafafa; align:right; font-family: arial, helvetica, verdana; font-size:12px; padding: 10px;}
            td.second {background-color:#fffaf5; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 10px;}
            td.middle {background-color:#ecf4fc; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 2px;}
            td.middle1 {background-color:#ffff99; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 2px;}
            .small_text {font-family: helvetica,arial,verdana,tahoma; font-size:10px; color:#666666}
            table.show {border:0px; bgcolor:#cccccc; background-color:#cccccc; align:center; font-family: arial, helvetica, verdana; font-size:12px; width:80%;}
            .dateBtn {background-image:url(./images/dayselect.gif); width:34px; background-repeat:no-repeat; background-position:middle center;}
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
        <div id='cal2' style='position: absolute; top: 200; left: 10; z-index: 666; visibility: hidden;'></div>
        <div id='cal3' style='position: absolute; top: 200; left: 10; z-index: 666; visibility: hidden;'></div>
    	<table cellspacing=1 align=center width = 90% class=blue><tr>
        <th>Geodesic Bulk Uploader - Data File upload</th>
        <tr><td>
        <b>Instructions:</b><br/>Select the data file to be used for the bulk upload.
        </td></tr><tr><td align='center'>";

        echo "<form target=_blank name=CSV-XML method='POST' action='".$file_name."' enctype='multipart/form-data'>
            <br/>
            <table cellpadding=0 cellspacing=1 width=90% class=blue>
            <input type='hidden' name='geobu_debug' value='".$geobu_debug."'/>\n
            <input type='hidden' name='".$geobu_cookie."' value='".$geobu_cookie_id."' />\n
            <input type='hidden' name='geobu' value='4'/>\n
            <tr>
            <td align=left valign=middle height=40>DATA FILE</td>
            <td align=left valign=middle height=40 colspan=2><input type='file' name='datafile' size='40'/></td>
            </tr>

        	<tr>
        	<td align=left width=35% height=30><span class=medium_font><font color=000000>CLASSIFIEDS START TIME: </span></td>
        	<td align=left width=65% colspan=2>".$hfieldstart.$mfieldstart."</td>
        	</tr>

        	<tr>
        	<td align=left><span class=medium_font><font color=000000>CLASSIFIEDS START DATE:* </span></td>
        	<td align=left colspan=2><input name=datestart value=\"\"><input type='button' value='&nbsp;' width='' onMouseDown=\"showCalendar(this, 'cal1', 'newAnc', 'datestart');\" class='dateBtn'></td>
        	</tr>

            <tr>
            <td align=left class=middle><spacer type='block' width='1' height='1' /></td>
            <td align=left class=middle><a name='newAnc'><img src='i/d-t.gif' width='1' height='1' /></a></td>
            <td align=left class=middle></td>
            </tr>

            <tr>
            <td align=left height=30><span class=medium_font><font color=000000>CONVERT CLASSIFIEDS TO AUCTIONS TIME: </span></td>
            <td align=left colspan=2>".$hfieldconvert.$mfieldconvert."</td>
            </tr>

            <tr>
            <td align=left><span class=medium_font><font color=000000>CONVERT CLASSIFIEDS TO AUCTIONS DATE:* </span></td>
            <td align=left colspan=2><input name=dateconvert value=\"\"><input type='button' value='&nbsp;' width='' onMouseDown=\"showCalendar(this, 'cal2', 'newAnc2', 'dateconvert');\" class='dateBtn'></td>
            </tr>

            <tr>
            <td align=left class=middle><spacer type='block' width='1' height='1' /></td>
            <td align=left class=middle><a name='newAnc2'><img src='i/d-t.gif' width='1' height='1' /></a></td>
            <td align=left class=middle></td>
            </tr>

            <tr>
            <td align=left height=30><span class=medium_font><font color=000000>AUCTIONS END TIME: </span></td>
            <td align=left colspan=2>".$hfieldend.$mfieldend."</td>
            </tr>

            <tr>
            <td align=left><span class=medium_font><font color=000000>AUCTIONS END DATE:* </span></td>
            <td align=left colspan=2><input name=dateend value=\"\"><input type='button' value='&nbsp;' width='' onMouseDown=\"showCalendar(this, 'cal3', 'newAnc3', 'dateend');\" class='dateBtn'></td>
            </tr>

            <tr>
            <td align=left class=middle><spacer type='block' width='1' height='1' /></td>
            <td align=left class=middle><a name='newAnc3'><img src='i/d-t.gif' width='1' height='1' /></a></td>
            <td align=left class=middle></td>
            </tr>

            <tr>
            <td align=left align=center valign=middle height=40>PRICE DELTA</td>
            <td align=left valign=middle height=40><input name=delta_plus value=$cpPriceDelta></td>
            <td align=left valign=middle height=40><span class=small_text>Format: 1234.56 <br>The given value is used for calculation of the Reserve Price</span></td>
            </tr>

            <tr>
            <td align=left valign=middle height=40>AUCTION TYPE</td>
            <td align=left valign=middle height=40><input name=auction_type value=1></td>
            <td align=left valign=middle height=40><span class=small_text>The given value is used for set auction type. <br>Standard auction: 1 <br>Dunish auction: 2</span></td>
            </tr>

            <tr>
            <td align=left valign=middle height=40>AUCTION QUANTITY</td>
            <td align=left valign=middle height=40><input name=auction_quantity value=1></td>
            <td align=left valign=middle height=40><span class=small_text>Quantity of the goods</span></td>

            </tr>

            <tr>
            <td align=left valign=middle height=40>PAYMENT OPTIONS</td>
            <td align=left valign=middle height=40><input name=payment_options value=\"Visa||Mastercard||Discover||American Express||Check||Money Order||PayPal||Bank Transfer\"></td>
            <td align=left valign=middle height=40><span class=small_text>This field is used within the listing to let the potential buyer know what payment types the seller will accept as payment when the listing expires.</span></td>
            </tr>

            <tr>
            <td align=left valign=middle height=40>PRICE PLAN ID</td>
            <td align=left valign=middle height=40><input name=price_plan_id value=0></td>
            <td align=left valign=middle height=40><span class=small_text></span></td>
            </tr>

            <tr>
            <td align=left valign=middle height=40>FEATURED AD</td>
            <td align=left valign=middle height=40><input name=featured_ad value=1></td>
            <td align=left valign=middle height=40><span class=small_text></span></td>
            </tr>

            <tr>
            <td align=left valign=middle height=40>ITEM TYPE</td>
            <td align=left valign=middle height=40><input name=item_type value=1></td>
            <td align=left valign=middle height=40><span class=small_text>The given value is used for set listing types <br>Classifieds: 1 <br>Auctions: 2</span></td>
            </tr>

            <tr>
            <td align=left valign=middle height=40>SKIP CRONTAB</td>
            <td align=left valign=middle height=40>No <input type=radio
			name=skipCrontab value=0 checked=checked> Yes <input type=radio name=skipCrontab value=1></td>
            <td align=left valign=middle height=40><span
			class=small_text>Default is No</span></td>
            </tr>

            <tr>
            <td align=center valign=middle height=40 colspan=3><input class=button type='submit' value='Click to upload and process the DATA file'/></td>
            </tr>
            </table>
            </form>";
?>