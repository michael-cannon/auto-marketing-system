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
<title>Insert Image</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script language="JavaScript" type="text/javascript">
<!--
var modal = false;

function AddImage( rte, imagePath, file )
{
    var isIE;
    var ua  = navigator.userAgent.toLowerCase();
    isIE = ( ( ua.indexOf( 'msie' ) != -1 ) && (ua.indexOf( 'opera' ) == -1 ) && ( ua.indexOf( 'webtv' ) == -1 ) );
    var img_align = document.getElementById( 'i_align' );
    var img_border = document.getElementById( 'i_border' );
           
    window.opener.document.getElementById( rte ).contentWindow.focus();
    
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
        var img = imagePath + file + '" alt="' + file + '" title="' + file + '"' + align + ' border="' + img_border.value;
        window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'insertImage', false, img );
    }
    else
    {
        var img = '<img src="' + imagePath + file + '" alt="' + file +'" title="' + file + '"' + align + ' border="' + img_border.value + '">';
        window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'insertHTML', false, img );
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
<body style="margin: 0px; padding: 0px; margin-left: 3px; background: #D4D0C8;" onload="mytimer=setTimeout('focusWindow()',0);">
<div style="font-family: verdana,sans-serif; font-size: 11px; padding: 5px;">Choose image options then click desired image to insert into editor</div>
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div style="width: 530px; height: 160px; overflow: auto; background: #fff; border: 1px solid #aaa;">
                <table width="100%">
                    <tr>
                        <?php

                        if ( !function_exists( 'gd_info' ) )
                        {
                            echo '<td><p style="text-align: center; font-family: verdana,sans-serif; font-size: 11px;">GD Image Library not found.</p></td>';
                        }
                        else
                        {

                            /** Edit path/to/image folder, URL/to/image folder and Fieldname used for rteEditor Iframe/Textarea - INCLUDE trailing slash! */

                            $list_img = new imgDir( 'c:/www/images/', 'http://localhost/images/', $_GET['edID'] );
                        }
                        
                        ?>
                    </tr>
                </table>
            </div>
        </td>
        <td style="width: 100px; white-space: nowrap; padding: 5px; font-family: verdana,sans-serif; font-size: 11px; vertical-align: top;">
            <form action="" method="post">
                <fieldset>
                    <legend style="font-weight: bold; font-family: verdana,sans-serif; font-size: 11px;">Options</legend>
                    <div style="padding: 3px;">
                    <table>
                        <tr>
                            <td style="font-family: verdana,sans-serif; font-size: 11px;">Alignment</td>
                        </tr>
                        <tr>
                            <td>
                                <select id="i_align" style="font-family: verdana,sans-serif; font-size: 11px;" onfocus="modal=true" onblur="modal=false">
                                    <option value="">None</option>
                                    <option value="baseline">Baseline</option>
                                    <option value="left">Left</option>
                                    <option value="right">Right</option>
                                    <option value="top">Top</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family: verdana,sans-serif; font-size: 11px; padding-top: 10px;">Border Size</td>
                        </tr>
                        <tr>
                            <td>
                                <select id="i_border" style="font-family: verdana,sans-serif; font-size: 11px;" onfocus="modal=true" onblur="modal=false">
                                    <option value="0">None&nbsp;&nbsp;&nbsp;&nbsp;</option>
                                    <option value="1">1px</option>
                                    <option value="2">2px</option>
                                    <option value="3">3px</option>
                                    <option value="4">4px</option>
                                    <option value="5">5px</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    </div>
                </fieldset>    
            </form>
            <center><input type="button" value="Cancel" onclick="window.close();" style="font-size: 11px; font-family: verdana; margin-top: 10px;" onfocus="modal=true" onblur="modal=false"></center>
        </td>
    </tr>
</table>
</body>
</html>

<?php

class imgDir
{
    var $imgDir;
    var $imgURL;
    var $imgField;

    function imgDir( $dir, $url, $field )
    {
        $this->imgDir   = $dir;
        $this->imgURL   = $url;
        $this->imgField = $field;

        return $this->listDir();
    }

     function listdir()
    {
        if ( is_dir( $this->imgDir ) )
        {
            $dirhandle = opendir( $this->imgDir );
            
            $count = 0;

            while ( ( $file = readdir( $dirhandle ) ) !== false )
            {
                if ( preg_match( '/gif/i', $file ) >= 1  || preg_match( '/jpg/i', $file ) >= 1  || preg_match( '/jpeg/i', $file ) >= 1  || preg_match( '/png/i', $file ) >= 1 )
                {
                    $currentfile = $file;
                    $img_array = getimagesize( $this->imgURL . $currentfile );
                    $img_size = filesize( $this->imgDir . $currentfile );

                    echo '<td onfocus="modal=true" onblur="modal=false" style="text-align: center; font-size: 9px; font-family: verdana; cursor: pointer;" title="Click to insert image"><img src="../rte_thumb.php?image=' . $currentfile . '&img_path=' . $this->imgDir . '" onClick="AddImage(\'' . $this->imgField . '\',\'' . $this->imgURL . '\',\''. $currentfile . '\')" /><br />' . 
                         $img_array[0] . 'x' . $img_array[1] . ' px<br />' . 
                         $img_size . ' bytes</td>' . "\n";
                         
                    $count++;
                }
            }
            
            if ( $count == 0 )
            {
                echo '<td style="text-align: center; font-size: 11px; font-family: verdana; padding-top: 40px;">No images available for preview.</td>';
            }
        }
        else
        {
            echo '<td style="text-align: center; font-size: 11px; font-family: verdana; padding-top: 40px;">Error: Unable to open image folder.<br /><br />Check the dir path and URL arguments.</td>';
        }
    }
}

?>