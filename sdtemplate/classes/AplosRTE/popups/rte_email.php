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
<title>Insert Email Link</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="rte_popup.css" />
<script language="JavaScript" type="text/javascript">
<!--
var rte = "<?php echo $_GET['edID']; ?>";
var modal = false;
var isIE;
var ua  = navigator.userAgent.toLowerCase();
isIE = ( ( ua.indexOf( 'msie' ) != -1 ) && (ua.indexOf( 'opera' ) == -1 ) && ( ua.indexOf( 'webtv' ) == -1 ) );

function InsertLink()
{
    window.opener.document.getElementById( rte ).contentWindow.focus();
    var email_url = document.getElementById( 'e_url' );
    var email_tooltip = document.getElementById( 'e_tooltip' );
    
    if ( !isIE )
    {
        var link = window.opener.document.getElementById( rte ).contentWindow.getSelection();
           
        if ( email_tooltip.value == '' )
        {
            var param = 'mailto:' + email_url.value + '" title="' + email_url.value + '"'; 
        }
        else
        {
            var param = 'mailto:' + email_url.value + '" title="' + email_tooltip.value + '"'; 
        }
    }
    else
    {
        if ( email_tooltip.value == '' )
        {
            var param = 'mailto:' + email_url.value + '%22 title=%22' + email_url.value; 
        }
        else
        {
            var param = 'mailto:' + email_url.value + '%22 title=%22' + email_tooltip.value; 
        }
        
    }   
        
    if ( email_url.value == '' )
    {
        window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'unLink', false, null );
    }
    else
    {
        window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'createLink', false, param );
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

function initEmail()
{
    document.getElementById( 'e_url' ).focus();
}
//-->
</script>
</head>
<body onload="initEmail();mytimer=setTimeout('focusWindow()',0);">
<form action="" method="get" onSubmit="InsertLink();" style="padding-top: 10px; text-align: center;">
<table align="center" summary="form">
    <tr>
        <td class="popup">Email: </td>
        <td><input type="text" id="e_url" style="width: 200px;" onfocus="modal=true" onblur="modal=false"></td>
    </tr>
    <tr>
        <td class="popup">Tooltip: </td>
        <td><input type="text" id="e_tooltip" style="width: 200px;" onfocus="modal=true" onblur="modal=false"></td>
    </tr>
</table><br />
<div style="text-align: center;"><input type="submit" value="Insert" class="button" onfocus="modal=true" onblur="modal=false" /> <input type="button" value="Cancel" onclick="window.close();" class="button" onfocus="modal=true" onblur="modal=false" /></div>
</form>
</body>
</html>
