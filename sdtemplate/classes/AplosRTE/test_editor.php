<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Aplos RTE Editor</title>
</head>
<body style="background: #ffffff;">

<?php

include_once 'rte.php';

$editor = new rteEditor( $rtePath      = $url, //full URL to AplosRTE dir - use trailing slash
                         $imageGallery = true,
                         $rteTheme     = '' ); // default,blue,green,silver
?>
<table align="center"><tr><td>
<form action="test_editor.php" method="post" enctype="multipart/form-data">
<?php                                       

echo $editor->initRTE( $fieldname    = 'test', //fieldname
                       $content      = '<b>Default</b> content 1', //default content
                       $rteWidth     = 600,
                       $rteheight    = 200,
                       $showEditor   = true, //show toolbar
                       $readonly     = false, //textarea readonly
                       '' ); // CSS style for textarea if not Gecko or IE
                    
//Add as many editors as you need  

/** 
echo '<br>';
                                               
echo $editor->initRTE( $fieldname    = 'test2',
                       $content      = '<b>Default</b> content 2',
                       $rteWidth     = 600,
                       $rteheight    = 200,
                       $showEditor   = true,
                       $readonly     = false,
                       '' );
                    
echo '<br>';
                                               
echo $editor->initRTE( $fieldname    = 'test3',
                       $content      = '<b>Default</b> content 3',
                       $rteWidth     = 600,
                       $rteheight    = 200,
                       $showEditor   = true,
                       $readonly     = false,
                       '' );
*/
?>
<p><input type="submit" name="submit" value="Submit"></p>
</form>
<h2 style="font-family:verdana;">View Output</h2>
</td></tr><tr>
<td style="border: 1px dotted #444; text-align: left; padding: 5px;">
<?php 
if ( isset( $_POST['test'] ) || isset( $_POST['test2'] ) || isset( $_POST['test3'] ))
{
    $test = rawurldecode(stripslashes($_POST['test']));// . '<br>' .
            //rawurldecode(stripslashes($_POST['test2'])) . '<br>' .
            //rawurldecode(stripslashes($_POST['test3']));
    
    echo $test . '<br>';
    
}
?>
</td></tr></table>
</body>
</html>