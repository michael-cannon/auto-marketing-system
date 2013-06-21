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
 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Insert Bookmark</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="rte_popup.css" />
<script language="JavaScript" type="text/javascript">
<!-- 
var rte = "<?php echo $_GET['edID']; ?>";
var modal = false;
var isIE;
var ua = navigator.userAgent.toLowerCase();
isIE = ( ( ua.indexOf( 'msie' ) != -1 ) && ( ua.indexOf( 'opera' ) == -1 ) && ( ua.indexOf( 'webtv' ) == -1 ) );

function InsertBookmark()
{
    window.opener.document.getElementById( rte ).contentWindow.focus();
    var bookmark_name = document.getElementById( 'b_name' );
    
    if( bookmark_name.value == '' )
    {
        window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'unLink',false,null );
    }
    else if( isIE )
    {
        window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'createBookmark',false,bookmark_name.value );
    }
    else
    {
        var link = window.opener.document.getElementById( rte ).contentWindow.getSelection();
        var param = '<a name="' + bookmark_name.value + '">' + link + '</a>'; 
        window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'insertHTML',false,param );
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

function initLink()
{
    document.getElementById( 'b_name' ).focus();
} 
//-->
</script>
</head>
<body onload="initLink();mytimer=setTimeout('focusWindow()',0);">
<form action="" method="get" onSubmit="InsertBookmark();">
<table align="center" summary="form">
    <tr>
        <td class="popup">Bookmark Name: </td>
        <td><input type="text" id="b_name" class="popup150" onfocus="modal=true" onblur="modal=false" /></td>
    </tr>
</table><br />
<div style="text-align: center;">
    <input type="submit" value="Insert" class="button" onfocus="modal=true" onblur="modal=false" />&nbsp;
    <input type="button" value="Cancel" class="button" onclick="window.close();" onfocus="modal=true" onblur="modal=false" />
</div>
</form>
</body>
</html>
