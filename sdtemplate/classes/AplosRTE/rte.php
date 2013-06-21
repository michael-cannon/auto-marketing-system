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

class rteEditor
{
    var $rteContent;
    var $rteFieldname;
    var $rteImage;
    var $rteTheme;
    var $rteURL;

    function rteEditor( $rteURL, $rteImage = false, $rteTheme = 'default' )
    {
        $this->rteImage = $rteImage;
        $this->rteTheme = $rteTheme;
        $this->rteURL   = $rteURL;

        $rteEditor = $this->rteFunctions() . "\n" .
                     '<script type="text/javascript" language="javascript">' . "\n" .
                     $this->rteInitialize() . "\n" .
                     $this->openLink() . "\n" .
                     $this->submitForm() . "\n" .
                     'window.onsubmit = function(){ submitForm(); }' . "\n" . 
                     '</script>' . "\n";

        echo $rteEditor;
    }
    
    function initRTE( $fieldname, $content, $width, $height, $buttons = true, $readonly = false, $css )
    {
        $this->rteFieldname = $fieldname;
        $this->rteContent   = $content;
        
        if ( $readonly == false || $readonly == '' )
            $readonly = 0;

        if ( $buttons == false || $buttons == '' )
            $buttons = 0;

        $initRTE = '<table cellpadding="0" cellspacing="0" border="0" summary="editor" width="' . $width . '">' . "\n" .
                   '<tr>' . "\n" .
                   '<td>' . "\n" .
                   '<script type="text/javascript" language="javascript">' . "\n" .
                   'writeRichText( \'' . $this->rteFieldname . '\', \'' . $this->safeCode() . '\', ' . $width . ', ' . $height . ', ' . $buttons . ', ' . $readonly . ' );' . "\n" .
                   '</script></td>' . "\n" .
                   '</tr>' . "\n" .
                   '</table>' . "\n";

        return $initRTE;
    }
    
    function rteFieldname( $fieldname )
    {
        return $fieldname;
    }
    
    function rteInitialize()
    {
        return 'initRTE( \'' . $this->rteURL . '\', \'\', \'' . $this->rteTheme . '\', \'' . $this->rteImage . '\' );';
    }

    function submitForm()
    {
        return 'function submitForm() { updateRTEs(); return true; }';
    }

    function openLink()
    {
        return 'function openLink( URI ) { win = window.open( URI, \'secWin\', \'\' ); win.focus; }';
    }

    function rteFunctions()
    {
        return '<script type="text/javascript" language="javascript" src="' . $this->rteURL . 'rteEngine_source.js"></script>';
    }

    function safeCode()
    {
        $safeCode = trim( $this->rteContent );

        //convert all types of single quotes
        $safeCode = str_replace( chr( 145 ), chr( 39 ), $safeCode );
        $safeCode = str_replace( chr( 146 ), chr( 39 ), $safeCode );
        $safeCode = str_replace( "'", '&#39;', $safeCode );

        //convert all types of double quotes
        $safeCode = str_replace( chr( 147 ), chr( 34 ), $safeCode );
        $safeCode = str_replace( chr( 148 ), chr( 34 ), $safeCode );
        
        //replace carriage returns & line feeds
        $safeCode = str_replace( chr( 10 ), ' ', $safeCode );
        $safeCode = str_replace( chr( 13 ), ' ', $safeCode );
       
        return $safeCode;
    }
}

?>

