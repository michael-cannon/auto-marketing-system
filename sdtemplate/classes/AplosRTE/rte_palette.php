<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Color</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script language="JavaScript" type="text/javascript">
    <!--
    
    /** Edit fieldname rte */

    function selectColor(color) {
        self.parent.setColor(color);
    }

    function InitColorPalette() {
        if (document.getElementsByTagName)
            var x = document.getElementsByTagName('TD');
        else if (document.all)
            var x = document.all.tags('TD');
        for (var i=0;i<x.length;i++) {
            x[i].onmouseover = over;
            x[i].onmouseout = out;
            x[i].onclick = click;
        }
    }

    function over() {
        this.style.border='1px dotted #fff';
    }

    function out() {
        this.style.border='1px solid #aaa';
    }

    function click() {
        selectColor(this.id);
    }
    
    function View( character )
    {
        document.getElementById( 'characterPreview' ).value = character;
        document.getElementById( 'showCharacter' ).value = character;
    }

    function ViewColor( color )
    {
        document.getElementById( 'showColor' ).style.background = color;
        document.getElementById( 'showColor' ).style.color = color;
    }

    //-->
    </script>
</head>
<body style="background: #ddd; margin: 0px; padding: 0px;" onLoad="InitColorPalette()">
<table cellpadding="0" cellspacing="1" border="1" align="center">
    <tr>
        <td onMouseOver="View('#FFFFFF');ViewColor('#ffffff');" style="border: 1px solid #aaa;" id="#FFFFFF" bgcolor="#FFFFFF"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td onMouseOver="View('#FFCCCC');ViewColor('#ffcccc');" style="border: 1px solid #aaa;" id="#FFCCCC" bgcolor="#FFCCCC"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FFCC99" bgcolor="#FFCC99"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FFFF99" bgcolor="#FFFF99"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FFFFCC" bgcolor="#FFFFCC"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#99FF99" bgcolor="#99FF99"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#99FFFF" bgcolor="#99FFFF"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#CCFFFF" bgcolor="#CCFFFF"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#CCCCFF" bgcolor="#CCCCFF"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FFCCFF" bgcolor="#FFCCFF"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
    </tr>
    <tr>
        <td style="border: 1px solid #aaa;" id="#CCCCCC" bgcolor="#CCCCCC"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FF6666" bgcolor="#FF6666"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FF9966" bgcolor="#FF9966"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FFFF66" bgcolor="#FFFF66"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FFFF33" bgcolor="#FFFF33"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#66FF99" bgcolor="#66FF99"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#33FFFF" bgcolor="#33FFFF"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#66FFFF" bgcolor="#66FFFF"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#9999FF" bgcolor="#9999FF"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FF99FF" bgcolor="#FF99FF"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
    </tr>
    <tr>
        <td style="border: 1px solid #aaa;" id="#C0C0C0" bgcolor="#C0C0C0"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FF0000" bgcolor="#FF0000"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FF9900" bgcolor="#FF9900"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FFCC66" bgcolor="#FFCC66"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FFFF00" bgcolor="#FFFF00"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#33FF33" bgcolor="#33FF33"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#66CCCC" bgcolor="#66CCCC"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#33CCFF" bgcolor="#33CCFF"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#6666CC" bgcolor="#6666CC"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#CC66CC" bgcolor="#CC66CC"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
    </tr>
    <tr>
        <td style="border: 1px solid #aaa;" id="#999999" bgcolor="#999999"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#CC0000" bgcolor="#CC0000"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FF6600" bgcolor="#FF6600"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FFCC33" bgcolor="#FFCC33"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#FFCC00" bgcolor="#FFCC00"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#33CC00" bgcolor="#33CC00"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#00CCCC" bgcolor="#00CCCC"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#3366FF" bgcolor="#3366FF"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#6633FF" bgcolor="#6633FF"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#CC33CC" bgcolor="#CC33CC"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
    </tr>
    <tr>
        <td style="border: 1px solid #aaa;" id="#666666" bgcolor="#666666"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#990000" bgcolor="#990000"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#CC6600" bgcolor="#CC6600"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#CC9933" bgcolor="#CC9933"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#999900" bgcolor="#999900"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#009900" bgcolor="#009900"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#339999" bgcolor="#339999"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#3333FF" bgcolor="#3333FF"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#6600CC" bgcolor="#6600CC"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#993399" bgcolor="#993399"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
    </tr>
    <tr>
        <td style="border: 1px solid #aaa;" id="#333333" bgcolor="#333333"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#660000" bgcolor="#660000"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#993300" bgcolor="#993300"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#996633" bgcolor="#996633"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#666600" bgcolor="#666600"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#006600" bgcolor="#006600"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#336666" bgcolor="#336666"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#000099" bgcolor="#000099"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#333399" bgcolor="#333399"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#663366" bgcolor="#663366"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
    </tr>
    <tr>
        <td style="border: 1px solid #aaa;" id="#000000" bgcolor="#000000"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#330000" bgcolor="#330000"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#663300" bgcolor="#663300"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#663333" bgcolor="#663333"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#333300" bgcolor="#333300"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#003300" bgcolor="#003300"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#003333" bgcolor="#003333"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#000066" bgcolor="#000066"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#330099" bgcolor="#330099"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
        <td style="border: 1px solid #aaa;" id="#330033" bgcolor="#330033"><img src="images/spacer.gif" width="12" height="12" alt=""></td>
    </tr>
</table>
<!--
<center>
<form action="" method="get" style="margin:0px; padding:0px" onSubmit="Set(document.getElementById('showColor').value); return false;">
    
                <div id="colorPreview"></div>
            
                <input type="text" name="showcolor" id="showColor" value="" size="10" style="border: 1px solid #aaa; font-size: 12px;color:#fff">
            
</form>


<form action="" method="get" style="margin:0px; padding:0px" onSubmit="Set(document.getElementById('showCharacter').value); return false;">
    
                <div id="characterPreview"></div>
            
                <input type="text" name="showcharacter" id="showCharacter" value="" size="10" style="border: 1px solid #aaa; background: #fff; font-size: 12px;" readonly>
           
</form>
<center>-->
</body>
</html>
