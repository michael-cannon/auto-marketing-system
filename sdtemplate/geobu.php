<?php
/**
 * AMS bulk uploader helper
 *
 * @author Michael Cannon <michael@peimic.com>
 * @version $Id: geobu.php,v 1.1.1.1 2010/04/15 09:42:44 peimic.comprock Exp $
 */

$file_name = 'geobu.php';
require_once("./functions.php");

if(!isset($_POST['geobu']))
{
	// data file upload form
	require_once("./text_3.php");
}

if($geobu_debug >= 1) echo __LINE__." : ".__FILE__." : ".__FUNCTION__."<br/>\n";

if(isset($_POST['geobu']) && 4 == $_POST['geobu'])
{
	if(empty($_REQUEST['datestart']) || empty($_REQUEST['dateconvert']) || empty($_REQUEST['dateend']))
	{
		// data file upload form
		require_once("./text_3.php");
		exit();
	}
	else
	{
		require_once("./text_4.php");
	}
}
?>