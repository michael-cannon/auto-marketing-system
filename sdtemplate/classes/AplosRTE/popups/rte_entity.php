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
<title>Insert Entity</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="rte_popup.css" />
<script language="JavaScript" type="text/javascript">
<!--
var rte = "<?php echo $_GET['edID']; ?>";
var modal = false;
var isIE;
var ua  = navigator.userAgent.toLowerCase();
isIE = ( ( ua.indexOf( 'msie' ) != -1 ) && (ua.indexOf( 'opera' ) == -1 ) && ( ua.indexOf( 'webtv' ) == -1 ) );

function selectEntity( id,entity )
{
    window.opener.document.getElementById( rte ).contentWindow.focus();

    if ( isIE )
    {
        window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'Paste', false, entity );
    }
    else
    {
        window.opener.document.getElementById( rte ).contentWindow.document.execCommand( 'InsertHTML', false, entity );
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

function View( character )
{
    document.getElementById( 'characterPreview' ).value = character;
    document.getElementById( 'showCharacter' ).value = character;
}
//-->
</script>
</head>
<body onload="mytimer=setTimeout('focusWindow()',0);">
<form action="" method="get" style="margin:0px; padding:0px" onSubmit="Set(document.getElementById('showCharacter').value); return false;">
    <table border="0" cellspacing="0" cellpadding="4" width="100%">
        <tr>
            <td style="vertical-align: middle; width: 50%;">
                <div id="characterPreview"></div>
            </td>
            <td style="vertical-align: middle; text-align: center; white-space: nowrap;">
                <input type="text" name="showcharacter" id="showCharacter" value="" size="15" style="border: 1px solid #aaa; background: #fff; font-size: 12px;" onfocus="modal=true" onblur="modal=false">
            </td>
            <td style="width: 50%;">&nbsp;</td>
        </tr>
    </table>
</form>
<table border="0" cellspacing="0" cellpadding="2" width="100%" align="center">
<tr>
<?php

$entity = array( '&cent;','&euro;','&pound;','&curren;','&yen;','&copy;','&reg;','&trade;','&divide;','&times;',
                 '&plusmn;','&frac14;','&frac12;','&frac34;','&deg;','&sup1;','&sup2;','&sup3;','&micro;','&laquo;',
                 '&raquo;','&quot;','&lsquo;','&rsquo;','&lsaquo;','&rsaquo;','&sbquo;','&bdquo;','&ldquo;','&rdquo;',
                 '&iexcl;','&brvbar;','&sect;','&not;','&macr;','&para;','&middot;','&cedil;','&iquest;','&fnof;',
                 '&mdash;','&ndash;','&bull;','&hellip;','&permil;','&ordf;','&ordm;','&szlig;','&dagger;','&Dagger;',
                 '&eth;','&ETH;','&oslash;','&Oslash;','&thorn;','&THORN;','&oelig;','&OElig;','&scaron;','&Scaron;',
                 '&acute;','&circ;','&tilde;','&uml;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;',
                 '&aelig;','&Agrave;','&Aacute;','&Acirc;','&Atilde;','&Auml;','&Aring;','&AElig;','&ccedil;','&Ccedil;',
                 '&egrave;','&eacute;','&ecirc;','&euml;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&igrave;','&iacute;',
                 '&icirc;','&iuml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ntilde;','&Ntilde;','&ograve;','&oacute;',
                 '&ocirc;','&otilde;','&ouml;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&ugrave;','&uacute;',
                 '&ucirc;','&uuml;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;','&yacute;','&yuml;','&Yacute;','&Yuml;' );
$count = 1;

foreach ( $entity as $data )
{
    if ( $count == 11 )
    {
        echo '</tr>' . "\n" . '<tr>' . "\n";
        $count = 1; 
    }
    else
    {
        echo '<td class="character" onMouseOver="View(\'' . str_replace( '&','&amp;',$data ) . '\');this.style.border=\'1px solid #000\';" onMouseOut="this.style.border=\'1px solid #aaa\';" onClick="selectEntity(\'entity\',\'' . $data . '\')">' . $data . '</td>' . "\n";
        
        $count++;
    }
}

?>
</tr>
</table>
<p align="center"><input type="button" value="Cancel" onClick="window.close();" class="button" onfocus="modal=true" onblur="modal=false"></p>
</body>
</html>
