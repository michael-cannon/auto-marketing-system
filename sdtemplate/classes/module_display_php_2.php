<? //module_display_php_2.php	
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

//eval php code
ob_start();
eval(stripslashes($show_module['php_code']));
$contents = ob_get_contents();
ob_end_clean();
$this->body = $contents;
?>