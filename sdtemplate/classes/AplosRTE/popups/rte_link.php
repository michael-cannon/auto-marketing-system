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
<title>Insert Link</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="rte_popup.css" />
<script language="JavaScript" type="text/javascript">
<!-- 
var rte = "<?php echo $_GET['edID']; ?>";
var modal = false;
var isIE;
var ua = navigator.userAgent.toLowerCase();
isIE = ( ( ua.indexOf( 'msie' ) != -1 ) && (ua.indexOf( 'opera' ) == -1 ) && ( ua.indexOf( 'webtv' ) == -1 ) );

function InsertLink()
{
    //window.opener.document.getElementById(rte).contentWindow.focus();
    var link_url = document.getElementById( 'l_url' );
    var link_target = document.getElementById( 'l_target' );
    var link_tooltip = document.getElementById( 'l_tooltip' );
        
    if( !isIE )
    {
        var link = window.opener.document.getElementById( rte ).contentWindow.getSelection();
        
        if( link_target.value == '' )
        {
            var param = link_url.value;
        }
        else
        {
            var param = link_url.value + '" target="' + link_target.value + '"';
        }
        
        if( link_tooltip.value == '' )
        {
            var param = param + '" title="' + link_url.value + '"';
        }
        else
        {
            var param = param + '" title="' + link_tooltip.value + '"';
        }
        
        if( link_url.value == '' )
        {
            window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'unLink',false,null );
        }
        else
        {
            window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'createLink',false,param );
        }
    }
    else
    {
        if( link_target.value == '' )
        {
            var param = link_url.value;
        }
        else
        {
            var param = link_url.value + '%22 target=%22' + link_target.value;
        }
        
        if( link_tooltip.value == '' )
        {
            var param=param + '%22 title=%22' + link_url.value;
        }
        else
        {
            var param = param + '%22 title=%22' + link_tooltip.value;
        }
        
        if( link_url.value == '' )
        {
            window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'unLink',false,null );
        }
        else
        {
            window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'createLink',false,param );
        }
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
    document.getElementById( 'l_url' ).focus();
} 
//-->
</script>
</head>
<body onload="initLink();mytimer=setTimeout('focusWindow()',0);">
<form action="" method="get" onSubmit="InsertLink();">
<table align="center" summary="form">
    <tr>
        <td class="popup">URL: </td>
        <td><input type="text" id="l_url" class="popup200" onfocus="modal=true" onblur="modal=false" /></td>
    </tr>
    <tr>
        <td class="popup">Tooltip: </td>
        <td><input type="text" id="l_tooltip" class="popup200" onfocus="modal=true" onblur="modal=false" /></td>
    </tr>
    <tr>
        <td class="popup">Target: </td>
        <td>
            <select name="target" id="l_target" class="popup205" onfocus="modal=true" onblur="modal=false">
                <option value="">None</option>
                <option value="_blank">New Window (_blank)</option>
                <option value="_self">Same Frame (_self)</option>
                <option value="_top">Top Frame (_top)</option>
            </select>
        </td>
    </tr>
</table>
<p>
    <input type="submit" value="Insert" class="button" onfocus="modal=true" onblur="modal=false" />&nbsp;
    <input type="button" value="Cancel" class="button" onclick="window.close();" onfocus="modal=true" onblur="modal=false" />
</p>
</form>
</body>
</html>
