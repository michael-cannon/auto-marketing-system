/**
 * Cross-Browser Rich Text Editor
 * http://www.kevinroth.com/rte/demo.htm
 * Written by Kevin Roth (kevin@NOSPAMkevinroth.com - remove NOSPAM)
 */

//init variables
var isRichText = false;
var rng;
var currentRTE;
var allRTEs = "";
var imgGallery = true;
var frameWidth;

var isIE;
var isGecko;
var isSafari;
var isKonqueror;

var includesPath;
var imagesPath;
var cssFile;
var rteCSS;
var rte_theme;

function initRTE( incPath, css, rte_css, imgPop )
{
    //set browser vars
    var ua = navigator.userAgent.toLowerCase();
    isIE = ( ( ua.indexOf("msie") != -1) && (ua.indexOf("opera") == -1) && (ua.indexOf("webtv") == -1));
    isGecko = (ua.indexOf("gecko") != -1);
    isSafari = (ua.indexOf("safari") != -1);
    isKonqueror = (ua.indexOf("konqueror") != -1);

    //check to see if designMode mode is available
    if (document.getElementById && document.designMode && !isSafari && !isKonqueror)
    {
        isRichText = true;
    }

    if (!isIE) document.captureEvents(Event.MOUSEOVER | Event.MOUSEOUT | Event.MOUSEDOWN | Event.MOUSEUP);
    document.onmouseover = raiseButton;
    document.onmouseout  = normalButton;
    document.onmousedown = lowerButton;
    document.onmouseup   = raiseButton;

    //set paths vars
    imagesPath   = incPath + 'images/';
    includesPath = incPath;
    cssFile      = css;
    rte_css      = rte_css.toLowerCase();

    if ( rte_css == '' && ( rte_css != 'blue' || rte_css != 'silver' || rte_css == 'default' ) )
    {
        rteCSS = 'style/rte_default.css';
        rte_theme = 'default';
    }
    else
    {
        rteCSS = 'style/rte_' + rte_css + '.css';
        rte_theme = rte_css;
    }

    if (isRichText) document.writeln('<style type="text/css">@import "' + includesPath + rteCSS + '";</style>');

    //for testing standard textarea, uncomment the following line

    //isRichText = false;

    if ( !imgPop )
        imgGallery = false;
}

function writeRichText(rte, html, width, height, buttons, readOnly)
{
    if ( isRichText && buttons )
    {
        if ( allRTEs.length > 0 )
            allRTEs += ";";
            allRTEs += rte;
            writeRTE(rte, html, width, height, readOnly);
    }
    else
    {
        writeDefault(rte, html, width, height, readOnly);
    }
    
}

function writeDefault(rte, html, width, height, readOnly) {
    if (!readOnly) {
        document.writeln('<textarea class="rteTextarea" name="' + rte + '" id="' + rte + '" style="width: ' + width + 'px; height: ' + height + 'px;">' + html + '</textarea>');
    } else {
        document.writeln('<textarea class="rteTextarea" name="' + rte + '" id="' + rte + '" style="width: ' + width + 'px; height: ' + height + 'px;" readonly="readonly">' + html + '</textarea>');
    }
}

function raiseButton(e) {
    if (isIE) {
        var el = window.event.srcElement;
    } else {
        var el= e.target;
    }

    className = el.className;
    if (className == 'rteImage' || className == 'rteImageLowered') {
        el.className = 'rteImageRaised';
    }
}

function normalButton(e) {
    if (isIE) {
        var el = window.event.srcElement;
    } else {
        var el= e.target;
    }

    className = el.className;
    if (className == 'rteImageRaised' || className == 'rteImageLowered') {
        el.className = 'rteImage';
    }
}

function lowerButton(e) {
    if (isIE) {
        var el = window.event.srcElement;
    } else {
        var el= e.target;
    }

    className = el.className;
    if (className == 'rteImage' || className == 'rteImageRaised') {
        el.className = 'rteImageLowered';
    }
}

function writeRTE(rte, html, width, height, readOnly)
{
    //var set_button_mode;

    var buttons = true;

    if (readOnly)
    {
        buttons = false;
    }

   var tablewidth = '100%';
   
   if ( buttons ) {
        document.writeln('<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr><td><img src="' + imagesPath + 'gui_images/' + rte_theme + '/' + rte_theme + '_bg_left.gif" width="5" height="81"></td><td style="width: 100%; background: url(' + imagesPath + 'gui_images/' + rte_theme + '/' + rte_theme + '_bg.png) repeat-x; padding-top: 4px;">');
        document.writeln('<table border="0" class="rteBack" cellpadding="0" cellspacing="0" id="Buttons2_' + rte + '" width="' + tablewidth + '">');
        document.writeln('  <tr>');
        document.writeln('      <td><img src="' + includesPath + 'spacer.gif" width="1" height="1" border="0" alt=""></td>');

        if ( !isGecko )
        {
            document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_cut.gif" width="20" height="20" alt="Cut" title="Cut" onClick="FormatText(\'' + rte + '\', \'cut\')"></td>');
            document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_copy.gif" width="20" height="20" alt="Copy" title="Copy" onClick="FormatText(\'' + rte + '\', \'copy\')"></td>');
            document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_paste.gif" width="20" height="20" alt="Paste" title="Paste" onClick="FormatText(\'' + rte + '\', \'paste\')"></td>');
        }
        else
        {
            document.writeln('      <td><img class="imageOff" src="' + imagesPath + 'rte_cut.gif" width="20" height="20" alt="Cut Function not available in Gecko browsers. Use Ctrl + X" title="Cut Function not available in Gecko browsers. Use Ctrl + X"></td>');
            document.writeln('      <td><img class="imageOff" src="' + imagesPath + 'rte_copy.gif" width="20" height="20" alt="Copy Function not available in Gecko browsers. Use Ctrl + C" title="Copy Function not available in Gecko browsers. Use Ctrl + C"></td>');
            document.writeln('      <td><img class="imageOff" src="' + imagesPath + 'rte_paste.gif" width="20" height="20" alt="Paste Function not available in Gecko browsers. Use Ctrl + V" title="Paste Function not available in Gecko browsers. Use Ctrl + V"></td>');
        }

        document.writeln('      <td><img class="rteVertSep" src="' + imagesPath + 'spacer.gif" width="3" height="20" border="0" alt=""></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_undo.gif" width="20" height="20" alt="Undo" title="Undo" onClick="FormatText(\'' + rte + '\', \'undo\')"></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_redo.gif" width="20" height="20" alt="Redo" title="Redo" onClick="FormatText(\'' + rte + '\', \'redo\')"></td>');
        document.writeln('      <td><img class="rteVertSep" src="' + imagesPath + 'spacer.gif" width="3" height="20" border="0" alt=""></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_selectall.gif" width="20" height="20" alt="Select All" title="Select All" onClick="FormatText(\'' + rte + '\', \'selectall\', \'\')"></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_removeformat.gif" width="20" height="20" alt="Remove Formatting" title="Remove Formatting" onClick="FormatText(\'' + rte + '\', \'removeformat\', \'\')"></td>');
        document.writeln('      <td><img class="rteVertSep" src="' + imagesPath + 'spacer.gif" width="3" height="20" border="0" alt=""></td>');
        //document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_hyperlink.gif" width="20" height="20" alt="Insert Link" title="Insert Link" onClick="popup( \'' + includesPath + 'popups/rte_link.php?css=' + rte_theme + '&edID=' + rte + '\',\'insert_link\',300,120,\'no\')"></td>');
        //document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_anchor.gif" width="20" height="20" alt="Insert Bookmark" title="Insert Bookmark" onClick="popup( \'' + includesPath + 'popups/rte_bookmark.php?edID=' + rte + '\',\'insert_bookmark\',300,80,\'no\')"></td>');
        //document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_email.gif" width="20" height="20" alt="Insert Email Link" title="Insert Email Link" onClick="popup( \'' + includesPath + 'popups/rte_email.php?edID=' + rte + '\',\'insert_email\',300,100,\'no\')"></td>');
        //document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_unlink.gif" width="20" height="20" alt="Remove Link" title="Remove Link" onClick="FormatText(\'' + rte + '\', \'unlink\')"></td>');
        //document.writeln('      <td><img class="rteVertSep" src="' + imagesPath + 'spacer.gif" width="3" height="20" border="0" alt=""></td>');

        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_hr.gif" width="20" height="20" alt="Horizontal Rule" title="Horizontal Rule" onClick="FormatText(\'' + rte + '\', \'inserthorizontalrule\', \'\')"></td>');
        document.writeln('      <td width="100%"></td>');
        document.writeln('  </tr>');
        document.writeln('</table>');
        document.writeln('<table border="0" class="rteBack" cellpadding="0" cellspacing="0" id="Buttons3_' + rte + '" width="' + tablewidth + '">');
        document.writeln('  <tr>');
        document.writeln('      <td><img src="' + imagesPath + 'spacer.gif" width="1" height="1" border="0" alt=""></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_bold.gif" width="20" height="20" alt="Bold" title="Bold" onClick="FormatText(\'' + rte + '\', \'bold\', \'\');"></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_italic.gif" width="20" height="20" alt="Italic" title="Italic" onClick="FormatText(\'' + rte + '\', \'italic\', \'\');"></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_underline.gif" width="20" height="20" alt="Underline" title="Underline" onClick="FormatText(\'' + rte + '\', \'underline\', \'\');"></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_strike.gif" width="20" height="20" alt="Strike Through" title="Strike Through" onClick="FormatText(\'' + rte + '\', \'strikethrough\', \'\');"></td>');
        document.writeln('      <td><img class="rteVertSep" src="' + imagesPath + 'spacer.gif" width="3" height="20" border="0" alt=""></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_sup.gif" width="20" height="20" alt="Superscript" title="Superscript" onClick="FormatText(\'' + rte + '\', \'superscript\', \'\');"></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_sub.gif" width="20" height="20" alt="Subscript" title="Subscript" onClick="FormatText(\'' + rte + '\', \'subscript\', \'\');"></td>');
        document.writeln('      <td><img class="rteVertSep" src="' + imagesPath + 'spacer.gif" width="3" height="20" border="0" alt=""></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_leftjustify.gif" width="20" height="20" alt="Align Left" title="Align Left" onClick="FormatText(\'' + rte + '\', \'justifyleft\', \'\')"></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_center.gif" width="20" height="20" alt="Center" title="Center" onClick="FormatText(\'' + rte + '\', \'justifycenter\', \'\')"></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_rightjustify.gif" width="20" height="20" alt="Align Right" title="Align Right" onClick="FormatText(\'' + rte + '\', \'justifyright\', \'\')"></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_justify.gif" width="20" height="20" alt="Justify Full" title="Justify Full" onclick="FormatText(\'' + rte + '\', \'justifyfull\', \'\')"></td>');
        document.writeln('      <td><img class="rteVertSep" src="' + imagesPath + 'spacer.gif" width="3" height="20" border="0" alt=""></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_numbered_list.gif" width="20" height="20" alt="Ordered List" title="Ordered List" onClick="FormatText(\'' + rte + '\', \'insertorderedlist\', \'\')"></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_list.gif" width="20" height="20" alt="Unordered List" title="Unordered List" onClick="FormatText(\'' + rte + '\', \'insertunorderedlist\', \'\')"></td>');
        document.writeln('      <td><img class="rteVertSep" src="' + imagesPath + 'spacer.gif" width="3" height="20" border="0" alt=""></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_outdent.gif" width="20" height="20" alt="Outdent" title="Outdent" onClick="FormatText(\'' + rte + '\', \'outdent\', \'\')"></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_indent.gif" width="20" height="20" alt="Indent" title="Indent" onClick="FormatText(\'' + rte + '\', \'indent\', \'\')"></td>');
        /**document.writeln('      <td><img class="rteVertSep" src="' + imagesPath + 'spacer.gif" width="3" height="20" border="0" alt=""></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_table.gif" width="20" height="20" alt="Insert Table - Not yet functional" title="Insert Table - Not yet functional" onClick="popup( \'' + includesPath + 'popups/rte_table.php?edID=' + rte + '\',\'insert_table\',350,210,\'no\')"></td>');
        document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_table_border.gif" width="20" height="20" alt="Toggle Table Border - Not yet functional" title="Toggle Table Border - Not yet functional"  onClick="setBorder(\'' + rte + '\')"></td>');*/
        document.writeln('      <td width="100%"></td>');
        document.writeln('  </tr>');
        document.writeln('</table>');
        document.writeln('<table class="rteBack" cellpadding=2 cellspacing=0 id="Buttons1_' + rte + '" width="' + tablewidth + '">');
        document.writeln('  <tr>');
        document.writeln('      <td>');
        document.writeln('          <select class="rteSelect" id="formatblock_' + rte + '" onchange="Select(\'' + rte + '\', this.id);">');
        document.writeln('              <option value="">Font Style</option>');
        document.writeln('              <option value="<p>">Paragraph</option>');
        document.writeln('              <option value="<h1>">Heading 1 <h1></option>');
        document.writeln('              <option value="<h2>">Heading 2 <h2></option>');
        document.writeln('              <option value="<h3>">Heading 3 <h3></option>');
        document.writeln('              <option value="<h4>">Heading 4 <h4></option>');
        document.writeln('              <option value="<h5>">Heading 5 <h5></option>');
        document.writeln('              <option value="<h6>">Heading 6 <h6></option>');
        document.writeln('              <option value="<address>">Address <ADDR></option>');
        document.writeln('              <option value="<pre>">Formatted <pre></option>');
        document.writeln('          </select>');
        document.writeln('      </td>');
        document.writeln('      <td>');
        document.writeln('          <select class="rteSelect" id="fontname_' + rte + '" onchange="Select(\'' + rte + '\', this.id)">');
        document.writeln('              <option value="Font" selected>Font Family</option>');
        document.writeln('              <option value="Arial, Helvetica, sans-serif">Arial</option>');
        document.writeln('              <option value="Comic Sans MS, Arial, Helvetica, sans-serif">Comic Sans</option>');
        document.writeln('              <option value="Courier New, Courier, mono">Courier New</option>');
        document.writeln('              <option value="Georgia, Times New Roman, serif">Georgia</option>');
        document.writeln('              <option value="Helvetica, Verdana, Arial, sans-serif">Helvetica</option>');
        document.writeln('              <option value="Tahoma, Arial, Helvetica, sans-serif">Tahoma</option>');
        document.writeln('              <option value="Times New Roman, Times, serif">Times New Roman</option>');
        document.writeln('              <option value="Trebuchet MS, Arial, Helvetica, sans-serif">Trebuchet</option>');
        document.writeln('              <option value="Verdana, Arial, Helvetica, sans-serif">Verdana</option>');
        document.writeln('          </select>');
        document.writeln('      </td>');
        document.writeln('      <td>');
        document.writeln('          <select class="rteSelect" unselectable="on" id="fontsize_' + rte + '" onchange="Select(\'' + rte + '\', this.id);">');
        document.writeln('              <option value="Size">Font Size</option>');
        document.writeln('              <option value="1">xx-small</option>');
        document.writeln('              <option value="2">x-small</option>');
        document.writeln('              <option value="3">small</option>');
        document.writeln('              <option value="4">medium</option>');
        document.writeln('              <option value="5">large</option>');
        document.writeln('              <option value="6">x-large</option>');
        document.writeln('              <option value="7">xx-large</option>');
        document.writeln('          </select>');
        document.writeln('      </td>');
        document.writeln('      <td><img src="' + includesPath + 'spacer.gif" width="2" height="1" border="0" alt=""></td>');
        document.writeln('      <td><img class="rteVertSep" src="' + imagesPath + 'spacer.gif" width="3" height="20" border="0" alt=""></td>');
        document.writeln('      <td><div id="forecolor_' + rte + '"><img class="rteImage" src="' + imagesPath + 'rte_textcolor.gif" width="20" height="20" alt="Text Color" title="Text Color" onClick="FormatText(\'' + rte + '\', \'forecolor\', \'\')""></div></td>');
        document.writeln('      <td><div id="hilitecolor_' + rte + '"><img class="rteImage" src="' + imagesPath + 'rte_bgcolor.gif" width="20" height="20" alt="Highlight Color" title="Highlight Color" onClick="FormatText(\'' + rte + '\', \'hilitecolor\', \'\')""></div></td>');
        document.writeln('      <td><img class="rteVertSep" src="' + imagesPath + 'spacer.gif" width="3" height="20" border="0" alt=""></td>');
        //document.writeln('      <td><img class="rteImage" src="' + imagesPath + 'rte_help.gif" width="20" height="20" alt="About Aplos RTE Editor" title="About Aplos RTE Editor" onClick="popup( \'' + includesPath + 'popups/rte_about.php\', \'about\',300,250,\'no\')"></td>');
        document.writeln('      <td width="100%">');
        document.writeln('      </td>');
        document.writeln('  </tr>');
        document.writeln('</table></td><td><img src="' + imagesPath + 'gui_images/' + rte_theme + '/' + rte_theme + '_bg_right.gif" width="5" height="81"></td></tr></table>');
    }
    document.writeln('<iframe id="' + rte + '" name="' + rte + '" style="width:' + width + 'px; height:' + height + 'px;" frameborder="yes"></iframe>');
    if (!readOnly )document.writeln('<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr><td valign="top"><img src="' + imagesPath + 'gui_images/' + rte_theme + '/' + rte_theme + '_bg_left2.gif" width="5" height="30"></td><td class="rteSource" valign="top"><label for="chkSrc' + rte + '" class="rteImage" style="color: #000; font-family: verdana,sans-serif; font-size: 10px; font-weight: bold; padding: 3px;" title="Toggle HTML Source and Design View">Toggle View</label><input type="checkbox" id="chkSrc' + rte + '" onclick="toggleHTMLSrc(\'' + rte + '\');" style="-moz-opacity:0; opacity: 0: visible; filter:alpha(opacity=0);" /></td><td valign="top"><img src="' + imagesPath + 'gui_images/' + rte_theme + '/' + rte_theme + '_bg_right2.gif" width="5" height="30"></td></tr></table>');
    document.writeln('<iframe width="155" height="109" id="cp' + rte + '" src="' + includesPath + 'rte_palette.php" marginwidth="0" marginheight="0" scrolling="no" style="visibility: hidden; display: none; position: absolute; _margin-top: 0px;" frameborder="0"></iframe>');
    document.writeln('<input type="hidden" id="hdn' + rte + '" name="' + rte + '" value="">');
    document.getElementById('hdn' + rte).value = html;
    enableDesignMode(rte, html, readOnly);
    if (isGecko) {
        setTimeout("enableDesignMode('" + rte + "', '" + html + "', " + readOnly + ");", 10);
    } 
}

function enableDesignMode(rte, html, readOnly) {
    var frameHtml = "<html id=\"" + rte + "\">\n";
    frameHtml += "<head>\n";
    //to reference your stylesheet, set href property below to your stylesheet path and uncomment
    if (cssFile.length > 0) {
        frameHtml += "<link media=\"all\" type=\"text/css\" href=\"" + cssFile + "\" rel=\"stylesheet\">\n";
    } else {
        frameHtml += "<style>\n";
        frameHtml += "body {\n";
        frameHtml += "  background: #FFFFFF;\n";
        frameHtml += "  margin: 0px;\n";
        frameHtml += "  padding: 0px;\n";
        frameHtml += "}\n";
        frameHtml += "</style>\n";
    }
    frameHtml += "</head>\n";
    frameHtml += "<body>\n";
    frameHtml += html + "\n";
    frameHtml += "</body>\n";
    frameHtml += "</html>";

    if (document.all) {
        var oRTE = frames[rte].document;
        oRTE.open();
        oRTE.write(frameHtml);
        oRTE.close();
        if (!readOnly) oRTE.designMode = "On";
    } else {
        try {
            if (!readOnly) document.getElementById(rte).contentDocument.designMode = "on";
            try {
                var oRTE = document.getElementById(rte).contentWindow.document;
                oRTE.open();
                oRTE.write(frameHtml);
                oRTE.close();
                if (isGecko && !readOnly) {
                    //attach a keyboard handler for gecko browsers to make keyboard shortcuts work
                    oRTE.addEventListener("keypress", kb_handler, true);
                }
            } catch (e) {
                alert("Error preloading content.");
            }
        } catch (e) {
            //gecko may take some time to enable design mode.
            //Keep looping until able to set.
            if (isGecko) {
                setTimeout("enableDesignMode('" + rte + "', '" + html + "', " + readOnly + ");", 10);
            } else {
                return false;
            }
        }
    }
}

function updateRTEs() {
    var vRTEs = allRTEs.split(";");
    for (var i = 0; i < vRTEs.length; i++) {
        updateRTE(vRTEs[i]);
    }
}

function updateRTE(rte)
{
    if (!isRichText) return;

    //set message value
    var oHdnMessage = document.getElementById('hdn' + rte);
    var oRTE = document.getElementById(rte);
    var readOnly = false;

    //check for readOnly mode
    if (document.all)
    {
        if (frames[rte].document.designMode != "On")
            readOnly = true;
    }
    else
    {
        if (document.getElementById(rte).contentDocument.designMode != "on")
            readOnly = true;
    }

    if (isRichText && !readOnly)
    {
        //if viewing source, switch back to design view
        if (document.getElementById("chkSrc" + rte).checked)
        {
            document.getElementById("chkSrc" + rte).checked = false;
            toggleHTMLSrc(rte);
        }

        if (oHdnMessage.value == null)
        {
            oHdnMessage.value = "";
        }

        if (document.all)
        {
            oHdnMessage.value = frames[rte].document.body.innerHTML;
            //oHdnMessage.value = oHdnMessage.value.replace("%20", " ");
            //oHdnMessage.value = oHdnMessage.value.replace("%22", "\"");
        }
        else
        {
            oHdnMessage.value = oRTE.contentWindow.document.body.innerHTML;
            //oHdnMessage.value = oHdnMessage.value.replace("%20", " ");
            //oHdnMessage.value = oHdnMessage.value.replace("%22", "\"");
        }

        //if there is no content (other than formatting) set value to nothing
        if (stripHTML(oHdnMessage.value.replace("&nbsp;", " ")) == ""
            && oHdnMessage.value.toLowerCase().search("<hr") == -1
            && oHdnMessage.value.toLowerCase().search("<img") == -1) oHdnMessage.value = "";
        //fix for gecko
        if (escape(oHdnMessage.value) == "%3Cbr%3E%0D%0A%0D%0A%0D%0A") oHdnMessage.value = "";
    }
}

function toggleHTMLSrc(rte)
{
    //contributed by Bob Hutzel (thanks Bob!)
    var oRTE;

    if (document.all)
    {
        oRTE = frames[rte].document;
    }
    else
    {
        oRTE = document.getElementById(rte).contentWindow.document;
    }

    if (document.getElementById("chkSrc" + rte).checked)
    {
        //set_button_mode.value = false;
        document.getElementById("Buttons1_" + rte).style.visibility = "hidden";
        document.getElementById("Buttons2_" + rte).style.visibility = "hidden";
        document.getElementById("Buttons3_" + rte).style.visibility = "hidden";
                        
        if (document.all)
        {
            oRTE.body.innerText = oRTE.body.innerHTML;
        }
        else
        {
            var htmlSrc = oRTE.createTextNode(oRTE.body.innerHTML);
            oRTE.body.innerHTML = "";
            oRTE.body.appendChild(htmlSrc);
        }
    }
    else
    {
        document.getElementById("Buttons1_" + rte).style.visibility = "visible";
        document.getElementById("Buttons2_" + rte).style.visibility = "visible";
        document.getElementById("Buttons3_" + rte).style.visibility = "visible";
                
        if (document.all)
        {
            //fix for IE
            var output = escape(oRTE.body.innerText);
            output = output.replace("%3CP%3E%0D%0A%3CHR%3E", "%3CHR%3E");
            output = output.replace("%3CHR%3E%0D%0A%3C/P%3E", "%3CHR%3E");

            oRTE.body.innerHTML = unescape(output);
        }
        else
        {
            var htmlSrc = oRTE.body.ownerDocument.createRange();
            htmlSrc.selectNodeContents(oRTE.body);
            oRTE.body.innerHTML = htmlSrc.toString();
        }
    }
}

/** This function is experimental and verrrrry buggy */

var isBorder = false;

function setBorder(rte) {
    var oRTE;
    if (document.all) {
        oRTE = frames[rte];

        //get current selected range
        var selection = oRTE.document.selection;
        if (selection != null) {
            rng = selection.createRange();
        }
    } else {
        oRTE = document.getElementById(rte).contentWindow;

        //get currently selected range
        var selection = oRTE.getSelection();
        rng = selection.getRangeAt(selection.rangeCount - 1).cloneRange();
    }
    
    var table  = oRTE.document.getElementsByTagName( 'table' ); 
    var border = new Array();
    
    for( var i = 0; i < table.length; i++ ) 
    {  
        border[i] = table[i].getAttribute( 'border' ); 
    } 
 
    if( isBorder == false ) 
    { 
        // toggle <table border=0> to dashed border 
    
        for( var i = 0; i < table.length; i++ ) 
        {  
            if ( border[i] == 0 )
            {
                oRTE.focus();
                var td = table[i].getElementsByTagName( 'td' ); 
                table[i].setAttribute( 'cellSpacing',3 ); 
                
                for( var i = 0; i < td.length; i++ ) 
                {  
                    td[i].style.border = '1px dashed #bbb'; 
                }
                oRTE.focus();
            }
        } 

        isBorder = true;
    } 
    else 
    { 
         // toggle border to <table border=0>
          
        for( var i = 0; i < table.length; i++ ) 
        {  
            if ( border[i] == 0 )
            {
                oRTE.focus();
                var td = table[i].getElementsByTagName( 'td' ); 
                table[i].setAttribute( 'border', border[i] ); 
                                
                for( var i = 0; i < td.length; i++ ) 
                {  
                    td[i].style.border = ''; 
                }
                oRTE.focus();
            }
        } 
     
        isBorder = false; 
     } 
}

//Function to add table
function AddTable(rte, tableHTML) {
	var rte = currentRTE;
	var oRTE;
	if (document.all) {
		oRTE = frames[rte];
	} else {
		oRTE = document.getElementById(rte).contentWindow;
	}
	
	var parentCommand = parent.command;
	if (document.all) {
		//retrieve selected range
		var sel = oRTE.document.selection;
		if (sel != null) {
			var newRng = sel.createRange();
			newRng = rng;
			newRng.select();
		}
	} else {
		//oRTE.focus();
	}
		if ((tableHTML != null) && (tableHTML != "")) {
		newRng.pasteHTML(tableHTML);
	}
	document.getElementById('cp2' + rte).style.visibility = "hidden";
	document.getElementById('cp2' + rte).style.display = "none";
}

//Function to format text in the text box
function FormatText(rte, command, option)
{
    var oRTE;

    if (document.all)
    {
        oRTE = frames[rte];

        //get current selected range
        var selection = oRTE.document.selection;
        if (selection != null)
        {
            rng = selection.createRange();
        }
    }
    else
    {
        oRTE = document.getElementById(rte).contentWindow;

        //get currently selected range
        var selection = oRTE.getSelection();
        rng = selection.getRangeAt(selection.rangeCount - 1).cloneRange();
    }

    try {
        if ( ( command == "forecolor") || ( command == "hilitecolor" ) )
        {
            //save current values
            parent.command = command;
            currentRTE = rte;

            //position and show color palette
            buttonElement = document.getElementById( command + '_' + rte );

            // Ernst de Moor: Fix the amount of digging parents up, in case the RTE editor itself is displayed in a div.
            document.getElementById('cp' + rte).style.left = getOffsetLeft(buttonElement, 8) + "px";
            document.getElementById('cp' + rte).style.top = (getOffsetTop(buttonElement, 8) + buttonElement.offsetHeight + 4) + "px";

            if (document.getElementById('cp' + rte).style.visibility == "hidden")
            {
                document.getElementById('cp' + rte).style.visibility = "visible";
                document.getElementById('cp' + rte).style.display = "inline";
            }
            else
            {
                document.getElementById('cp' + rte).style.visibility = "hidden";
                document.getElementById('cp' + rte).style.display = "none";
            }
        }
        else
        {
            oRTE.focus();
            if (isGecko)
            {
                oRTE.document.execCommand("useCSS",false,true);
            }
            oRTE.document.execCommand(command, false, option);
            oRTE.focus();
        }
    }
    catch (e)
    {
        alert(e);
    }
}

//Function to set color
function setColor( color )
{
    var rte = currentRTE;
    var oRTE;

    if ( document.all )
    {
        oRTE = frames[rte];
    }
    else
    {
        oRTE = document.getElementById(rte).contentWindow;
    }

    var parentCommand = parent.command;

    if ( document.all )
    {
        //retrieve selected range
        var sel = oRTE.document.selection;

        if ( parentCommand == "hilitecolor" )
        {
            parentCommand = "backcolor";
        }

        if (sel != null)
        {
            var newRng = sel.createRange();
            newRng = rng;
            newRng.select();
        }
    }

    oRTE.focus();
    oRTE.document.execCommand( parentCommand, false, color );
    oRTE.focus();
    document.getElementById( 'cp' + rte ).style.visibility = "hidden";
    document.getElementById( 'cp' + rte ).style.display = "none";
}

//Function to set color
function setEntity( entity )
{
    var entity = entity;
    oRTE.focus();
    oRTE.document.execCommand( insertHTML, false, entity );
    oRTE.focus();

}

//Function to add image
function AddImage(rte) {
    var oRTE;
    if (document.all) {
        oRTE = frames[rte];

        //get current selected range
        var selection = oRTE.document.selection;
        if (selection != null) {
            rng = selection.createRange();
        }
    } else {
        oRTE = document.getElementById(rte).contentWindow;

        //get currently selected range
        var selection = oRTE.getSelection();
        rng = selection.getRangeAt(selection.rangeCount - 1).cloneRange();
    }

    imagePath = prompt('Enter Image URL:', 'http://');
    if ((imagePath != null) && (imagePath != "") && (imagePath != "http://")) {
        oRTE.focus();
        oRTE.document.execCommand('InsertImage', false, imagePath);
        oRTE.focus();
    }
}

//function to perform spell check
function checkspell() {
    try {
        var tmpis = new ActiveXObject("ieSpell.ieSpellExtension");
        tmpis.CheckAllLinkedDocuments(document);
    }
    catch(exception) {
        if(exception.number==-2146827859) {
            if (confirm("ieSpell not detected.  Click Ok to go to download page."))
                window.open("http://www.iespell.com/download.php","DownLoad");
        } else {
            alert("Error Loading ieSpell: Exception " + exception.number);
        }
    }
}

// Ernst de Moor: Fix the amount of digging parents up, in case the RTE editor itself is displayed in a div.
function getOffsetTop(elm, parents_up) {
    var mOffsetTop = elm.offsetTop;
    var mOffsetParent = elm.offsetParent;

    if(!parents_up) {
        parents_up = 10000; // arbitrary big number
    }
    while(parents_up>0 && mOffsetParent) {
        mOffsetTop += mOffsetParent.offsetTop;
        mOffsetParent = mOffsetParent.offsetParent;
        parents_up--;
    }

    return mOffsetTop;
}

// Ernst de Moor: Fix the amount of digging parents up, in case the RTE editor itself is displayed in a div.
function getOffsetLeft(elm, parents_up) {
    var mOffsetLeft = elm.offsetLeft;
    var mOffsetParent = elm.offsetParent;

    if(!parents_up) {
        parents_up = 10000; // arbitrary big number
    }
    while(parents_up>0 && mOffsetParent) {
        mOffsetLeft += mOffsetParent.offsetLeft;
        mOffsetParent = mOffsetParent.offsetParent;
        parents_up--;
    }

    return mOffsetLeft;
}

function Select(rte, selectname) {
    var oRTE;
    if (document.all) {
        oRTE = frames[rte];

        //get current selected range
        var selection = oRTE.document.selection;
        if (selection != null) {
            rng = selection.createRange();
        }
    } else {
        oRTE = document.getElementById(rte).contentWindow;

        //get currently selected range
        var selection = oRTE.getSelection();
        rng = selection.getRangeAt(selection.rangeCount - 1).cloneRange();
    }

    var idx = document.getElementById(selectname).selectedIndex;
    // First one is always a label
    if (idx != 0) {
        var selected = document.getElementById(selectname).options[idx].value;
        var cmd = selectname.replace('_' + rte, '');
        oRTE.focus();
        oRTE.document.execCommand(cmd, false, selected);
        oRTE.focus();
        document.getElementById(selectname).selectedIndex = 0;
    }
}


function kb_handler(evt) {
    var rte = evt.target.id;

    //contributed by Anti Veeranna (thanks Anti!)
    if (evt.ctrlKey) {
        var key = String.fromCharCode(evt.charCode).toLowerCase();
        var cmd = '';
        switch (key) {
            case 'b': cmd = "bold"; break;
            case 'i': cmd = "italic"; break;
            case 'u': cmd = "underline"; break;
        };

        if (cmd) {

            FormatText(rte, cmd, true);
            //evt.target.ownerDocument.execCommand(cmd, false, true);
            // stop the event bubble
            evt.preventDefault();
            evt.stopPropagation();
        }
    }
}

function docChanged (evt) {
    alert('changed');
}

function stripHTML(oldString) {
    var newString = oldString.replace(/(<([^>]+)>)/ig,"");

    //replace carriage returns and line feeds
   newString = newString.replace(/\r\n/g," ");
   newString = newString.replace(/\n/g," ");
   newString = newString.replace(/\r/g," ");

    //trim string
    newString = trim(newString);

    return newString;
}

function trim(inputString) {
   // Removes leading and trailing spaces from the passed string. Also removes
   // consecutive spaces and replaces it with one space. If something besides
   // a string is passed in (null, custom object, etc.) then return the input.
   if (typeof inputString != "string") return inputString;
   var retValue = inputString;
   var ch = retValue.substring(0, 1);

   while (ch == " ") { // Check for spaces at the beginning of the string
      retValue = retValue.substring(1, retValue.length);
      ch = retValue.substring(0, 1);
   }
   ch = retValue.substring(retValue.length-1, retValue.length);

   while (ch == " ") { // Check for spaces at the end of the string
      retValue = retValue.substring(0, retValue.length-1);
      ch = retValue.substring(retValue.length-1, retValue.length);
   }

    // Note that there are two spaces in the string - look for multiple spaces within the string
   while (retValue.indexOf("  ") != -1) {
        // Again, there are two spaces in each of the strings
      retValue = retValue.substring(0, retValue.indexOf("  ")) + retValue.substring(retValue.indexOf("  ")+1, retValue.length);
   }
   return retValue; // Return the trimmed string back to the user
}

function deleteText( rte )
{
    var oRTE;

    if ( document.all )
    {
        oRTE = window.frames[rte].document;
    }
    else
    {
        oRTE = document.getElementById( rte ).contentWindow.document;
    }

    oRTE.body.innerHTML = "";

    if ( document.getElementById( "cleartext" + rte ) )
    {
        if ( document.all )
        {
            oRTE.body.innerHTML = "";
        }
        else
        {
            var htmlSrc = oRTE.createTextNode( "" );
            oRTE.body.innerHTML = "";
            oRTE.body.appendChild( htmlSrc );
        }
    }
}

function popup( page, popupname, popwidth, popheight, popscroll )
{
    if ( isIE )
    {
        popheight = popheight + 20;
    }
    
    var top         = ( window.screen.height - popheight ) / 2 ;
    var left        = ( window.screen.width - popwidth ) / 2;
    var windowprops = "width=" + popwidth + ",height=" + popheight + ",left=" + left + ",top=" + top + ",location=no,menubar=no,status=no,toolbar=no,scrollbars=" + popscroll + ",resizable=yes";

    newWindow = window.open( page, popupname, windowprops );
}

function openLink( URI )
{
    win = window.open( URI, "secWin", "" );
    win.focus;
}
