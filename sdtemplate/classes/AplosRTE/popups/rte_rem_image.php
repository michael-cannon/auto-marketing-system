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
<title>Insert Remote Image</title>
<link rel="stylesheet" type="text/css" href="rte_popup.css" />
<script language="JavaScript" type="text/javascript">
<!-- 
var rte = "<?php echo $_GET['edID']; ?>";
var modal = false;
var isIE;
var ua = navigator.userAgent.toLowerCase();
isIE = ( ( ua.indexOf( 'msie' ) != -1 ) && ( ua.indexOf( 'opera' ) == -1 ) && ( ua.indexOf( 'webtv' ) == -1 ) );

function InsertImage()
{
    window.opener.document.getElementById(rte).contentWindow.focus();
    var img_url = document.getElementById( 'i_url' );
    var img_align = document.getElementById( 'i_align' );
    var img_border = document.getElementById( 'i_border' );
    
    if( img_url.value == '' )
    {
        window.close();
    }
    else
    {
    
        if ( img_align.value == '' )
        {
            var align = '';
        }
        else
        {
            var align = ' align="' + img_align.value + '"';
        }
    
        if ( isIE )
        {
            var img = img_url.value + '" alt="' + img_url.value + '" title="' + img_url.value + '"' + align + ' border="' + img_border.value;
            window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'insertImage', false, img );
        }
        else
        {
            var img = '<img src="' + img_url.value + '" alt="' + img_url.value +'" title="' + img_url.value + '"' + align + ' border="' + img_border.value + '">';
            window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'insertHTML', false, img );
        }
    
        window.close();
    }
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
    document.getElementById( 'i_url' ).focus();
} 
//-->
</script>
</head>
<body onload="initLink();mytimer=setTimeout('focusWindow()',0);">
<form action="" method="get" onSubmit="InsertImage();">
<table align="center" summary="form">
    <tr>
        <td class="popup">URL: </td>
        <td><input type="text" id="i_url" class="popup200" onfocus="modal=true" onblur="modal=false" /></td>
    </tr>
    <tr>
        <td class="popup">Alignment: </td>
        <td>
            <select id="i_align" class="popup205" onfocus="modal=true" onblur="modal=false">
                <option value="">None</option>
                <option value="baseline">Baseline</option>
                <option value="left">Left</option>
                <option value="right">Right</option>
                <option value="top">Top</option>
            </select>
        </td>
    </tr>
    <tr>    
        <td class="popup">Border: </td>
        <td>
            <select id="i_border" class="popup205" onfocus="modal=true" onblur="modal=false">
                <option value="0">None</option>
                <option value="1">1px</option>
                <option value="2">2px</option>
                <option value="3">3px</option>
                <option value="4">4px</option>
                <option value="5">5px</option>
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
