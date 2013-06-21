<!--
	HTML Color Editor (c) 2000 by Sebastian Weber <webersebastian@yahoo.de>

	This is a completely JavaScript- and HTML-based color editor
	for inclusion in Webpages.

	The Color Editor Window should be opened via a JavaScript Call
	like:
		window.open('ColorEditor.html','colorchoser',
		'height=250,width=390,dependent=yes,directories=no,location=no,
		menubar=no,resizable=yes,scrollbars=no,status=no,toolbar=no');

	The opening window MUST contain a JS-Function 
		setColor(rgbcolor);
	This Function will be called whenever the user presses the "OK" or
	"Apply"-Button in the Color Editor Window.

	Furthermore the opening window MAY contain a variable called
	oldColor which must be set to the value of the old Color before
	opening the Color Editor window.

	Tested with Netscape Navigator v4.7 and MSIE v5.5
	The set of custom colors is stored in a Cookie which virtually
	never expires.

	Have Phun !
-->
<html>
<head>
<title>Color</title>
<script language="javascript">
<!--
// Microsoft or Netscape ?
	var micro=false;
	if (navigator.appName.substr(0,5)=="Micro") micro=true;	
// Old Color and Current Color
	if (window.opener && window.opener.oldColor)
		oldcol=window.opener.oldColor;
	else oldcol="FFFFFF";
	curcol=oldcol;
// Predefined Colors
	pcoc = getCookie("predefcolors");
	if (!pcoc) pcoc="FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF,"+
					"FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF,FFFFFF";
	precol= new Array(16);
	precol=pcoc.split(",",16);
// Predefined Colors Cursor
	cursorImg=new Image ();
	cursorImg.src="cursor.gif";
	blankImg=new Image ();
	blankImg.src="blank.gif";
	cursorPos=1;
// Other Stuff
	hexchars="0123456789ABCDEF";
// Funktions
	function setCookie(name, value, expire) {
	   document.cookie = name + "=" + escape(value)
	   + ((expire == null) ? "" : ("; expires=" + expire.toGMTString()))
	}

	function transferColor () {
		today= new Date();
		expires= new Date ()
		expires.setTime(today.getTime() + 1000*60*60*24*365)
		setCookie("predefcolors",precol.join(","),expires);
		if (window.opener)
			window.opener.setColor(curcol);
	}
	
	function getCookie(Name) {
	   var search = Name + "="
	   if (document.cookie.length > 0) { // if there are any cookies
	      offset = document.cookie.indexOf(search) 
	      if (offset != -1) { // if cookie exists 
	         offset += search.length 
	         // set index of beginning of value
	         end = document.cookie.indexOf(";", offset) 
	         // set index of end of cookie value
	         if (end == -1) 
	            end = document.cookie.length
	         return unescape(document.cookie.substring(offset, end))
	      } 
	   }
	}

	function fromhex(inval) {
		out=0;
		for (a=inval.length-1;a>=0;a--) 
			out+=Math.pow(16,inval.length-a-1)*hexchars.indexOf(inval.charAt(a));	
		return out;
	}

	function tohex(inval) {
		out=hexchars.charAt(inval/16);
		out+=hexchars.charAt(inval%16);
		return out;
	}

	function setPreColors () {
		for (a=1;a<=16;a++) {
			if (!micro) eval ("document.precell"+a+".bgColor=precol["+(a-1)+"]");
			else eval ("document.all('precell"+a+"').bgColor=precol["+(a-1)+"]");
		}
	}
	
	function definePreColor () {
		precol[cursorPos-1]=curcol;
		setPreColors();
		setCursor(cursorPos+1>16?1:cursorPos+1);
	}

	function preset (what) {
		set (precol[what-1]);
		setCursor(what);
	}

	function setCursor(what) {
		if (!micro) {
			cursorPos=what;
			eval ("document.cursorLayer.pageX=document.precell"+cursorPos+".pageX");
			eval ("document.cursorLayer.pageY=document.precell"+cursorPos+".pageY-1");
		} else {
			eval ("document.preimg"+cursorPos+".src=blankImg.src");
			cursorPos=what;
			eval ("document.preimg"+cursorPos+".src=cursorImg.src");
		}
	}

	function set(color) {
		color=color.toUpperCase();
		if (!micro) document.thecell.bgColor=color;
		else document.all("thecell").bgColor=color;
		document.theform.rtext.value=(fromhex(color.substr(0,2)));
		document.theform.gtext.value=(fromhex(color.substr(2,2)));
		document.theform.btext.value=(fromhex(color.substr(4,2)));
		document.htmlform.htmlcolor.value=color;
		curcol=color;
		setCursor(cursorPos);
	}

	function setOldColor () {
		if (!micro) document.theoldcell.bgColor=oldcol;
		else document.all("theoldcell").bgColor=oldcol;
	}

	function setFromRGB () {
		r=document.theform.rtext.value;
		g=document.theform.gtext.value;
		b=document.theform.btext.value;
		if (r>255||r<0||g>255||g<0||g>255||g<0) {set(curcol);return;}
		set(tohex(r)+tohex(g)+tohex(b));
	}

	function setFromHTML () {
		inval=document.htmlform.htmlcolor.value.toUpperCase();
		if (inval.length!=6) {set(curcol);return;}
		for (a=0;a<6;a++) 
			if (hexchars.indexOf(inval.charAt(a))==-1) {
				set(curcol);return;
			}
		set (inval);
	}

// --> </script>
<style type="text/css">
td {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
input {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
</style>
</head>
<body bgcolor="#bfbfbf" onLoad="setPreColors();setCursor(cursorPos);set(curcol);setOldColor();">
<table border=0 cellspacing=2 cellpadding=0 width=100%>
<tr><td align=center>Basic Colors</td>
	<td align=center>Current Color</td>
	<td align=center>Old Color</td>
</tr>	
<tr><td align=center>
<!-- ************** BASIC COLORS PALETTE ******************** -->
<table border=1 cellpadding=0 cellspacing=0>
<tr height=14>
	<td height=14 width=14 bgcolor="#ff0000"><a
	href="javascript:set('ff0000')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#400000"><a
	href="javascript:set('400000')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#800000"><a
	href="javascript:set('800000')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#c00000"><a
	href="javascript:set('c00000')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#ff4040"><a
	href="javascript:set('ff4040')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#ff8080"><a
	href="javascript:set('ff8080')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#ffc0c0"><a
	href="javascript:set('ffc0c0')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#000000"><a
	href="javascript:set('000000')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
</tr>
<tr height=14>
	<td height=14 width=14 bgcolor="#ffff00"><a
	href="javascript:set('ffff00')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#404000"><a
	href="javascript:set('404000')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#808000"><a
	href="javascript:set('808000')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#c0c000"><a
	href="javascript:set('c0c000')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#ffff40"><a
	href="javascript:set('ffff40')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#ffff80"><a
	href="javascript:set('ffff80')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#ffffc0"><a
	href="javascript:set('ffffc0')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#202020"><a
	href="javascript:set('202020')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
</tr>
<tr height=14>
	<td height=14 width=14 bgcolor="#00ff00"><a
	href="javascript:set('00ff00')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#004000"><a
	href="javascript:set('004000')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#008000"><a
	href="javascript:set('008000')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#00c000"><a
	href="javascript:set('00c000')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#40ff40"><a
	href="javascript:set('40ff40')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#80ff80"><a
	href="javascript:set('80ff80')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#c0ffc0"><a
	href="javascript:set('c0ffc0')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#404040"><a
	href="javascript:set('404040')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
</tr>
<tr height=14>
	<td height=14 width=14 bgcolor="#00ffff"><a
	href="javascript:set('00ff00')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#004040"><a
	href="javascript:set('004040')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#008080"><a
	href="javascript:set('008080')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#00c0c0"><a
	href="javascript:set('00c0c0')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#40ffff"><a
	href="javascript:set('40ffff')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#80ffff"><a
	href="javascript:set('80ffff')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#c0ffff"><a
	href="javascript:set('c0ffff')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#808080"><a
	href="javascript:set('808080')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
</tr>
<tr height=14>
	<td height=14 width=14 bgcolor="#0000ff"><a
	href="javascript:set('0000ff')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#000040"><a
	href="javascript:set('000040')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#000080"><a
	href="javascript:set('000080')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#0000c0"><a
	href="javascript:set('0000c0')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#4040ff"><a
	href="javascript:set('4040ff')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#8080ff"><a
	href="javascript:set('8080ff')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#c0c0ff"><a
	href="javascript:set('c0c0ff')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#c0c0c0"><a
	href="javascript:set('c0c0c0')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
</tr>
<tr height=14>
	<td height=14 width=14 bgcolor="#ff00ff"><a
	href="javascript:set('ff00ff')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#400040"><a
	href="javascript:set('400040')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#800080"><a
	href="javascript:set('800080')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#c000c0"><a
	href="javascript:set('c000c0')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#ff40ff"><a
	href="javascript:set('ff40ff')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#ff80ff"><a
	href="javascript:set('ff80ff')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#ffc0ff"><a
	href="javascript:set('ffc0ff')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
	<td height=14 width=14 bgcolor="#ffffff"><a
	href="javascript:set('ffffff')"><img src="blank.gif" width=14
	height=14 border=0></a></td>
</tr>
</table>
</td>
<td valign=top>
<!-- ************** RGB INPUT ******************** -->
<table border=0 cellpadding=0 cellspacing=0>
<tr><form name="theform"><td valign=center>
	<input type="text" size=3 maxlength=3 name="rtext" value=255
	onChange="setFromRGB()"> R<br>
	<input type="text" size=3 maxlength=3 name="gtext" value=255
	onChange="setFromRGB()"> G<br>
	<input type="text" size=3 maxlength=3 name="btext" value=255
	onChange="setFromRGB()"> B
	</td></form>
	<td>
<!-- ************** Display New Color ******************** -->
<table border=0 cellspacing=0 cellpadding=0>
	<tr height=10><td width=75 height=10 colspan=3></td></tr>
	<tr height=53>
		<td width=10 height=53> </td>
		<td width=55 height=53 id="thecell" bgcolor="#ffffff"><table
		border=1 cellspacing=0 cellpadding=0><tr><td><ilayer
		below name="thecell" bgcolor="#ffffff"><img src="blank.gif"
		width=55 height=53 border=0></ilayer></td></tr></table></td>
		<td width=10 height=53> </td></tr>
	<tr height=10><td width=75 height=10 colspan=3></td></tr>
</table>
	</td></tr>
<tr>
	<td colspan=2><form name="htmlform">
	<input type=text size=6 maxlength=6 name="htmlcolor" value="FFFFFF"
	onChange="setFromHTML()"> HTML</form></td>
</tr>
</table>
</td>
<td valign=top>
<!-- ************** Display Old Color ******************** -->
<table border=0 cellspacing=0 cellpadding=0>
	<tr height=10><td width=75 height=10 colspan=3></td></tr>
	<tr height=53>
		<td width=10 height=53> </td>
		<td width=55 height=53 id="theoldcell" bgcolor="#ffffff"><table
		border=1 cellspacing=0 cellpadding=0><tr><td><ilayer
		below name="theoldcell" bgcolor="#ffffff"><img src="blank.gif"
		width=55 height=53 border=0></ilayer></td></tr></table></td>
		<td width=10 height=53> </td></tr>
	<tr height=10><td width=75 height=10 colspan=3></td></tr>
</table>
</td>
</tr>
<tr height=5><td height=5 colspan=3></td></tr>
<tr><td align=center>Custom Colors</td></tr>
<tr><td align="center">
<!-- ************** Custom Colors ******************** -->
<table border=1 cellpadding=0 cellspacing=0>
<tr height=14>
	<td width=14 height=14 id="precell1" bgcolor="#ffffff"
	><ilayer below name="precell1" bgcolor="#ffffff"><a
	href="javascript:preset(1)"><img src="blank.gif" width=14
	name="preimg1" height=14 border=0></a></ilayer></td>
	<td width=14 height=14 id="precell2" bgcolor="#ffffff"
	><ilayer below name="precell2" bgcolor="#ffffff"><a
	href="javascript:preset(2)"><img src="blank.gif" width=14
	name="preimg2" height=14 border=0></a></ilayer></td>
	<td width=14 height=14 id="precell3" bgcolor="#ffffff"
	><ilayer below name="precell3" bgcolor="#ffffff"><a
	href="javascript:preset(3)"><img src="blank.gif" width=14
	name="preimg3" height=14 border=0></a></ilayer></td>
	<td width=14 height=14 id="precell4" bgcolor="#ffffff"
	><ilayer below name="precell4" bgcolor="#ffffff"><a
	href="javascript:preset(4)"><img src="blank.gif" width=14
	name="preimg4" height=14 border=0></a></ilayer></td>
	<td width=14 height=14 id="precell5" bgcolor="#ffffff"
	><ilayer below name="precell5" bgcolor="#ffffff"><a
	href="javascript:preset(5)"><img src="blank.gif" width=14
	name="preimg5" height=14 border=0></a></ilayer></td>
	<td width=14 height=14 id="precell6" bgcolor="#ffffff"
	><ilayer below name="precell6" bgcolor="#ffffff"><a
	href="javascript:preset(6)"><img src="blank.gif" width=14
	name="preimg6" height=14 border=0></a></ilayer></td>
	<td width=14 height=14 id="precell7" bgcolor="#ffffff"
	><ilayer below name="precell7" bgcolor="#ffffff"><a
	href="javascript:preset(7)"><img src="blank.gif" width=14
	name="preimg7" height=14 border=0></a></ilayer></td>
	<td width=14 height=14 id="precell8" bgcolor="#ffffff"
	><ilayer below name="precell8" bgcolor="#ffffff"><a
	href="javascript:preset(8)"><img src="blank.gif" width=14
	name="preimg8" height=14 border=0></a></ilayer></td>
</tr>
<tr height=14>
	<td width=14 height=14 id="precell9" bgcolor="#ffffff"
	><ilayer below name="precell9" bgcolor="#ffffff"><a
	href="javascript:preset(9)"><img src="blank.gif" width=14
	name="preimg9" height=14 border=0></a></ilayer></td>
	<td width=14 height=14 id="precell10" bgcolor="#ffffff"
	><ilayer below name="precell10" bgcolor="#ffffff"><a
	href="javascript:preset(10)"><img src="blank.gif" width=14
	name="preimg10" height=14 border=0></a></ilayer></td>
	<td width=14 height=14 id="precell11" bgcolor="#ffffff"
	><ilayer below name="precell11" bgcolor="#ffffff"><a
	href="javascript:preset(11)"><img src="blank.gif" width=14
	name="preimg11" height=14 border=0></a></ilayer></td>
	<td width=14 height=14 id="precell12" bgcolor="#ffffff"
	><ilayer below name="precell12" bgcolor="#ffffff"><a
	href="javascript:preset(12)"><img src="blank.gif" width=14
	name="preimg12" height=14 border=0></a></ilayer></td>
	<td width=14 height=14 id="precell13" bgcolor="#ffffff"
	><ilayer below name="precell13" bgcolor="#ffffff"><a
	href="javascript:preset(13)"><img src="blank.gif" width=14
	name="preimg13" height=14 border=0></a></ilayer></td>
	<td width=14 height=14 id="precell14" bgcolor="#ffffff"
	><ilayer below name="precell14" bgcolor="#ffffff"><a
	href="javascript:preset(14)"><img src="blank.gif" width=14
	name="preimg14" height=14 border=0></a></ilayer></td>
	<td width=14 height=14 id="precell15" bgcolor="#ffffff"
	><ilayer below name="precell15" bgcolor="#ffffff"><a
	href="javascript:preset(15)"><img src="blank.gif" width=14
	name="preimg15" height=14 border=0></a></ilayer></td>
	<td width=14 height=14 id="precell16" bgcolor="#ffffff"
	><ilayer below name="precell16" bgcolor="#ffffff"><a
	href="javascript:preset(16)"><img src="blank.gif" width=14
	name="preimg16" height=14 border=0></a></ilayer></td>
</tr>
</table>
</td>
<td colspan=2 rowspan=2 align=center valign=bottom><form><input type="button" 
value="&nbsp;&nbsp;&nbsp;OK&nbsp;&nbsp;&nbsp;"
onClick="transferColor();window.close()">
<input type="button" value="Apply"
onClick="transferColor();">
<input type="button" value="Cancel"
onClick="window.close()">
</form></td>
</tr>
</tr>
<tr><td align=center><form><input type="button"
	value="Add Custom" onClick="definePreColor()"></form></td></tr>
</table>
<!-- *************** define the Cursor-Layer (Netcape only)************* -->

<layer above name="cursorLayer" width=14 visibility=show>
<script language="javascript">
 if (!micro) document.write("<a href='javascript:preset(cursorPos)'><img border=0 src='cursor.gif'></a>");
// --></script>
</layer>
</body>
</html>

