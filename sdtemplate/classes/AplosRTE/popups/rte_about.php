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
<title>About Aplos RTE Editor</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
<!--
body{background: #D4D0C8;padding:0px;margin:0px;}
.button{font-family:verdana,sans-serif;font-size:11px;font-weight:normal;}
fieldset,div{font-size:10px;font-family:verdana,sans-serif;}
//-->
</style>
<script type="text/javascript" language="javascript">
<!--
var modal = false;

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
<table width="100%" summary="about">
    <tr>
        <td style="padding: 3px;">
            <fieldset>
                <legend>About AplosRTE</legend>
                <div style="padding: 3px; _padding: 5px;">
                    Author: MT Jordan &lt;mtjo@aplosmedia.com>
                    Copyright: 2004<br>
                    Version: 0.0.2<br>
                    License: <a href="http://opensource.org/licenses/lgpl-license.php" target="_blank">LGPL</a><br>
                    Support: <a href="http://forum.aplosmedia.com/" target="_blank">Aplos Media Forums</a>
                </div>
            </fieldset><br />
            <fieldset>
                <legend>Acknowledgements</legend>
                <div style="padding: 3px; _padding: 5px;">
                    Based on Cross-Browser Rich Text Editor:<br />
                    <a href="http://www.kevinroth.com/rte/demo.htm" target="_blank">http://www.kevinroth.com/rte/demo.htm</a><br /><br />
                    Editor Icons courtesy of:<br />
                    <a href="http://www.fckeditor.net/" target="_blank">Frederico Caldeira Knabben</a>
                </div>
            </fieldset>
        </td>
    </tr>
</table> 
<p align="center"><input type="button" value="Close" onclick="window.close();" class="button"></p>
</body>
</html>
