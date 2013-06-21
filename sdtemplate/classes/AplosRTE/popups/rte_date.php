<?php

/**
 * @author    MT Jordan <mtjo@aplosmedia.com>
 * @version   0.0.3
 * @link      <http://aplosmedia.com/products/rteeditor>
 * @copyright 2004 AplosMedia
 * @license   LGPL <http://opensource.org/licenses/lgpl-license.php>
 * 
 * This library is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public 
 * License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software 
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA.
 */
 
 ?>
 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Insert Date</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="rte_popup.css" />
<script language="JavaScript" type="text/javascript">
<!--
var rte = "<?php echo $_GET['edID']; ?>";
var modal = false;
var isIE;
var ua  = navigator.userAgent.toLowerCase();
isIE = ( ( ua.indexOf( 'msie' ) != -1 ) && (ua.indexOf( 'opera' ) == -1 ) && ( ua.indexOf( 'webtv' ) == -1 ) );

function selectDate( id,date )
{
    window.opener.document.getElementById( rte ).contentWindow.focus();

    if ( isIE )
    {
        window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'Paste', false, date );
    }
    else
    {
        window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'InsertHTML', false, date );
    }

    window.close();
}

function focusWindow()
{
    if( !modal )
    {
        window.focus();
    }

    mytimer = setTimeout( 'focusWindow()',0 );
}
//-->
</script>
</head>
<body onload="mytimer=setTimeout('focusWindow()',0);">
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
<?php

$date = array( 'F d, Y','d F, Y','M d, Y','d M, Y','m/d/y','m/d/Y','d/m/y','d/m/Y','m.d.y','m.d.Y','d.m.y','d.m.Y','m-d-y','m-d-y','d-m-y','d-m-Y','l F d, Y','r' );

foreach( $date as $data )
{
    echo '<tr>' . "\n" .
         '<td class="date" onmouseover="this.style.background=\'#000077\';this.style.color=\'#fff\'" onmouseout="this.style.background=\'#fff\';this.style.color=\'#000\'" onClick="selectDate(\'date\',\'' . date( $data ) . '\')">' . date( $data ) . '</td>' . "\n" .
         '</tr>' . "\n";
}

?>
</table>
<p align="center"><input type="button" value="Cancel" onclick="window.close();" class="button" onfocus="modal=true" onblur="modal=false"></p>
</body>
</html>
