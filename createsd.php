<?php
/**
 * Show inventory site create form
 *
 * @author Michael Cannon <michael@peimic.com>
 * @version $Id: createsd.php,v 1.1.1.1 2010/04/15 09:42:34 peimic.comprock Exp $
 */

require_once( 'config.php' );
require_once( CB_COGS_DIR . 'cb_dir_file.php' );

$name							= cbRequest('name');
$address						= cbRequest('address');
$city							= cbRequest('city');
$state							= cbRequest('state');
$zip							= cbRequest('zip');
$miurl							= cbRequest('miurl');
$site							= cbRequest('site');
$landing						= cbRequest('landing');
$email							= cbRequest('email', $cpEmail);
$start							= cbRequest('start', date( 'm/d/Y', strtotime( '+1 Thursday' ) ) );
$convert						= cbRequest('convert', date( 'm/d/Y', strtotime( '+2 Saturday' ) ) );
$end							= cbRequest('end', date( 'm/d/Y', strtotime( '+2 Sunday' ) ) );
$special						= cbRequest('special');
$invcount						= cbRequest('invcount', $cpInvCount);
$bkgColor						= cbRequest('bkgColor', $cpBkgColor);
$saleText						= cbRequest('saleText', $cpSaleText);
$pocFirst						= cbRequest('pocFirst', $cpFirst);
$pocLast						= cbRequest('pocLast', $cpLast);
$pocPhone						= cbRequest('pocPhone', $cpPhone);
$headerImage					= cbRequest('headerImage', 'mi_generic_hdr.jpg');

$states							= Array(
									'AL' => 'Alabama',
									'AK' => 'Alaska',
									'AZ' => 'Arizona',
									'AR' => 'Arkansas',
									'CA' => 'California',
									'CO' => 'Colorado',
									'CT' => 'Connecticut',
									'DE' => 'Delaware',
									'DC' => 'District of Columbia',
									'FL' => 'Florida',
									'GA' => 'Georgia',
									'HI' => 'Hawaii',
									'ID' => 'Idaho',
									'IL' => 'Illinois',
									'IN' => 'Indiana',
									'IA' => 'Iowa',
									'KS' => 'Kansas',
									'KY' => 'Kentucky',
									'LA' => 'Louisiana',
									'ME' => 'Maine',
									'MD' => 'Maryland',
									'MA' => 'Massachusetts',
									'MI' => 'Michigan',
									'MN' => 'Minnesota',
									'MS' => 'Mississippi',
									'MO' => 'Missouri',
									'MT' => 'Montana',
									'NE' => 'Nebraska',
									'NV' => 'Nevada',
									'NH' => 'New Hampshire',
									'NJ' => 'New Jersey',
									'NM' => 'New Mexico',
									'NY' => 'New York',
									'NC' => 'North Carolina',
									'ND' => 'North Dakota',
									'OH' => 'Ohio',
									'OK' => 'Oklahoma',
									'OR' => 'Oregon',
									'PA' => 'Pennsylvania',
									'RI' => 'Rhode Island',
									'SC' => 'South Carolina',
									'SD' => 'South Dakota',
									'TN' => 'Tennessee',
									'TX' => 'Texas',
									'UT' => 'Utah',
									'VT' => 'Vermont',
									'VA' => 'Virginia',
									'WA' => 'Washington',
									'WV' => 'West Virginia',
									'WI' => 'Wisconsin',
									'WY' => 'Wyoming',
									'NL' => 'Not Listed'
								);

$headerLocation					= 'sdtemplate/user_images';
$headerImages					= cbGetDirListing( $headerLocation
									, "^mi_"
									, true
								);
sort( $headerImages );

// show_form
?>
<html>
    <head>
        <title><?php echo $cpTitle; ?></title>
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
            .text_red {color: #ff0000; text-weight: bold;}
       	</style>
        <script language='JavaScript' src='calendar.js'></script>
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
                var reg=/http:\/\/www\.(\w+)\.(\w+)/;
                var reg1 = /http:\/\/(\w+)\.(\w+)/;
                if(reg.test(sitename) == true)
                {
                	var arr=reg.exec(sitename);
                	document.fls.miurl.value = "http://" + arr[1] + "\<?php echo $cpConfig['postfix']; ?>";
                }
                else if(reg1.test(sitename) == true)
                {
                    var arr=reg1.exec(sitename);
                	document.fls.miurl.value = "http://" + arr[1] + "\<?php echo $cpConfig['postfix']; ?>";
                }
                else
                {
                	alert("Incorrect site name!");
                }
            }

    </script>

    </head>
	<body bgcolor='#ffffff' text='#000000' link='#425BBA' alink='#FF7F00' vlink='#425BBA' marginheight='0' marginwidth='0' topmargin='0' leftmargin='0' rightmargin='0'>
        <div id='cal1' style='position: absolute; top: 200; left: 10; z-index: 666; visibility: hidden;'></div>
        <div id='cal2' style='position: absolute; top: 200; left: 10; z-index: 666; visibility: hidden;'></div>
        <div id='cal3'  style='position: absolute; top: 200; left: 10; z-index: 666; visibility: hidden;'></div>

<form method=post target='_blank' action='createSite.php' enctype='multipart/form-data' name='fls' id='fls'>
<table width=100%>
	<tr>
    	<td height=50 valign=middle align=center>
        	<br><h2>Marketing Site Request</h2><hr size=1 color=#ff9900 align=center width=80%>
        </td>
    </tr>
    <tr>
        <td>
                 <table class=show  cellpadding=0; cellspacing=1 width=80% align=center>

                    <tr>
                        <td class=first width=50%><strong>Name of dealership<span class=text_red>*</span>:</strong></td>
                        <td class=second width=50% colspan=2><input type='text' size='40' name='name' value='<?php echo $name; ?>' /></td>
                    </tr>
                    <tr>
                        <td class=middle1></td>
                        <td class=middle1 colspan=2></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>Dealership address<span class=text_red>*</span>:</td>
                        <td class=second width=50% colspan=2><input type='text' size='40' name='address' value='<?php echo $address; ?>' /></td>
                    </tr>
                    <tr>
                        <td class=middle></td>
                        <td class=middle colspan=2></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>City<span class=text_red>*</span>:</td>
                        <td class=second width=50% colspan=2><input type='text' size='40' name='city' value='<?php echo $city; ?>' /></td>
                    </tr>
                    <tr>
                        <td class=middle></td>
                        <td class=middle colspan=2></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>State<span class=text_red>*</span>:</td>
                        <td class=second width=50% colspan=2><select name=state>
        			<option value=none></option>
<?php
        			foreach($states as $key => $value)
        			{
        				echo ($key == $state)
							? "<option selected value='$key'>$value</option>\n"
							:"<option value='$key'>$value</option>\n";
        			}
?>

        			</select></td>
                    </tr>
                    <tr>
                        <td class=middle></td>
                        <td class=middle colspan=2></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>Zip code<span class=text_red>*</span>:</td>
                        <td class=second width=50% colspan=2><input type='text' size='10' name='zip' value='<?php echo $zip; ?>' /></td>
                    </tr>
                    <tr>
                        <td class=middle></td>
                        <td class=middle colspan=2></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>Dealership POC<span class=text_red>*</span>:</td>
                        <td class=second width=50% colspan=2>
						<input type='text' size='20' name='pocFirst' value='<?php echo $pocFirst; ?>' />
						<input type='text' size='20' name='pocLast' value='<?php echo $pocLast; ?>' />
						</td>
                    </tr>
                    <tr>
                        <td class=middle></td>
                        <td class=middle colspan=2></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>Dealership POC Phone<span class=text_red>*</span>:</td>
                        <td class=second width=50% colspan=2>
						<input type='text' size='20' name='pocPhone' value='<?php echo $pocPhone; ?>' />
						</td>
                    </tr>
                    <tr>
                        <td class=middle></td>
                        <td class=middle colspan=2></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>Dealership website<span class=text_red>*</span>:<br><span class=small_text><strong>Format:</strong> http://www.dealership.com</span></td>
                        <td class=second width=50% colspan=2><input type='text' size='40' name='site' value='<?php echo $site; ?>' onChange="url_change(site, miurl)"/></td>
                    </tr>
                    <tr>
                        <td class=middle></td>
                        <td class=middle colspan=2></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>Inventory Site URL to use<span class=text_red>*</span>:<br><span class=small_text><strong>Format:</strong> http://dealership.movingiron.com</span></td>
                        <td class=second width=50% colspan=2><input type='text' size='40' name='miurl' value='<?php echo $miurl; ?>'/></td>
                    </tr>
                    <tr>
                        <td class=middle colspan=2></td>
                        <td class=middle></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>Landing Site URL to use<span
						class=text_red>*</span>:<br><span
						class=small_text><strong>Format:</strong>
						http://www.bestpublicoffer.com</span></td>
                        <td class=second width=50% colspan=2><input type='text'
						size='40' name='landing' value='<?php echo $landing; ?>'/></td>
                    </tr>
                    <tr>
                        <td class=middle colspan=2></td>
                        <td class=middle></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>Landing Site Title<span
						class=text_red>*</span>:<br><span
						class=small_text><strong>Default:</strong>
						Private Sale</span></td>
                        <td class=second width=50% colspan=2><input type='text'
						size='40' name='saleText' value='<?php echo $saleText; ?>'/></td>
                    </tr>
                    <tr>
                        <td class=middle colspan=2></td>
                        <td class=middle></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>Email to send registrations to:</td>
                        <td class=second width=50% colspan=2><input type='text' size='40' name='email' value='<?php echo $email; ?>' /></td>
                    </tr>
                    <tr>
                        <td class=middle colspan=2></td>
                        <td class=middle></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>Date to start sale:</td>
                        <td class=second width=5%><input type='text' size='15' name='start' value='<?php echo $start; ?>' class='dateInput'/></td>
                        <td class=second><input type='button' value='&nbsp;' width='' onMouseDown="showCalendar(this, 'cal1', 'startAnc', 'start');" class='dateBtn'></td>
                    </tr>
                    <tr>
                        <td class=middle><spacer type='block' width='1' height='1' /></td>
                        <td class=middle><a name='startAnc'><img src='i/d-t.gif' width='1' height='1' /></a></td>
                        <td class=middle><a name='toAnc'><img src='i/d-t.gif' width='1' height='1' /></a></td>
                    </tr>

<!--
                    <tr>
                        <td class=first width=50%>Date to convert to auctions:</td>
                        <td class=second width=5%><input type='text' size='15' name='convert' value='<?php echo $convert; ?>' class='dateInput' /></td>
                        <td class=second><input type='button' value='&nbsp;' width='' onMouseDown="showCalendar(this, 'cal2', 'convertAnc', 'convert');" class='dateBtn'></td>
                    </tr>
                    <tr>
                        <td class=middle><spacer type='block' width='1' height='1' /></td>
                        <td class=middle><a name='convertAnc'><img src='i/d-t.gif' width='1' height='1' /></a></td>
                        <td class=middle><a name='toAnc'><img src='i/d-t.gif' width='1' height='1' /></a></td>
                    </tr>
-->
                    <tr>
                        <td class=first width=50%>Date to end sale:</td>
                        <td class=second width=5%><input type='text' size='15' name='end' value='<?php echo $end; ?>' class='dateInput' /></td>
                        <td class=second><input type='button' value='&nbsp;' width='' onMouseDown="showCalendar(this, 'cal3', 'endAnc', 'end');" class='dateBtn'></td>
                    </tr>
                    <tr>
                        <td class=middle><spacer type='block' width='1' height='1' /></td>
                        <td class=middle><a name='endAnc'><img src='i/d-t.gif' width='1' height='1' /></a></td>
                        <td class=middle><a name='toAnc'><img src='i/d-t.gif' width='1' height='1' /></a></td>
                    </tr>
                    <tr>
                        <td class=first width=50%>Dealer inventory count:</td>
                        <td class=second width=50% colspan=2><input type='text'
						size='4' name='invcount' value='<?php echo $invcount; ?>' /></td>
                    </tr>
                    <tr>
                        <td class=middle colspan=2></td>
                        <td class=middle></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>Template background color:</td>
                        <td class=second width=50% colspan=2><input type='text'
						size='4' name='bkgColor' value='<?php echo $bkgColor; ?>' /></td>
                    </tr>
                    <tr>
                        <td class=middle colspan=2></td>
                        <td class=middle></td>
                    </tr>

                    <tr>
                        <td class=first>Header selection:</td>
                        <td class=second colspan=2>
<?php
foreach( $headerImages as $key => $value )
{
	$imageFilename				= basename( $value );
	$checked					= '';

	// type and value check
	if ( $imageFilename == $headerImage )
	{
		$checked				= 'checked="checked"';
	}

	$str						= '<input type="radio" name="headerImage" value="'
									. $imageFilename
									. '" '
									. $checked
									. ' />'
									. $imageFilename
									. '<br />'
									. '<img src="'
									. $value
									. '" alt="'
									. $imageFilename
									. '" />'
									. '<br />';
	echo $str;
}
?>
						</td>
                    </tr>
                    <tr>
                        <td class=middle colspan=2></td>
                        <td class=middle></td>
                    </tr>

                    <tr>
                        <td class=first width=50%>Special instructions:</td>
                        <td class=second width=50% colspan=2><textarea name='special' rows=10 cols=40%><?php echo $special; ?></textarea></td>
                    </tr>
                    <tr>
                        <td class=middle></td>
                        <td class=middle colspan=2></td>
                    </tr>

                    <tr>
                        <td class=second width=100% colspan=3 align=center><input type=submit value='>> Create Site'>
                        <p>Site creation will take about five minutes to complete.</td>
                    </tr>
                    <tr>
                        <td class=middle1></td>
                        <td class=middle1 colspan=2></td>
                    </tr>
                 </table>
        </td>
    </tr>
</table>
</form>
</html>