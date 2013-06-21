<? //show_help.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

error_reporting  (E_ERROR | E_WARNING | E_PARSE);
include("config.php");
include("classes/adodb.inc.php");
include("classes/site_class.php");

if ($_REQUEST["a"])
{
	$db = &ADONewConnection('mysql');

	if($persistent_connections)
	{
		//echo " Persistent Connection <bR>";
		if (!$db->PConnect($db_host, $db_username, $db_password, $database))
		{
			echo "could not connect to database";
			exit;
		}
	}
	else
	{
		//echo " No Persistent Connection <bR>";
		if (!$db->Connect($db_host, $db_username, $db_password, $database))
		{
			echo "could not connect to database";
			exit;
		}
	}

	$site = new Site($db,100,$language_id,0);

	if ($_REQUEST["b"])
	{
		if ($b == "1")
		{
			$sql_query = "select explanation from ".$site->sell_choices_types_table." where type_id = ".$_REQUEST["a"];
			//echo $sql_query." is the query<br>\n";
			$explanation_result = $db->Execute($sql_query);
			if (!$explanation_result)
			{
				//echo $sql_query." is the query<br>\n";
				return false;
			}
			elseif ($explanation_result->RecordCount() == 1)
			{
				//echo $sql_query." is the query<br>\n";
				$show_explanation = $explanation_result->FetchRow();
				//echo $explanation_result->RecordCount()." is the count<br>\n";
				echo $site->medium_font_tag.urldecode($show_explanation["explanation"])."</font>";
				//echo "this is working";
				return true;
			}
			else
			{
				//echo "no help for that-1";
			}
		}
	}
	elseif ($_REQUEST["c"])
	{
		//display the help question explanation
		$sql_query = "select explanation from geodesic_classifieds_sell_questions where question_id = ".$_REQUEST["c"];
		$result = $db->Execute($sql_query);
		//echo $sql_query." is the query<br>\n";
		if (!$result)
		{
			//echo $sql_query." is the query<br>\n";
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show = $result->FetchRow();
			echo $site->medium_font_tag.stripslashes($show["explanation"])."</font>";
		}
		else
		{
			echo "no help for that";
		}
	}
	else
	{
		$language_id = $HTTP_COOKIE_VARS["language_id"];
		//echo $language_id." is the language id<br>\n";

		//get the help message
		$sql_query = "select * from geodesic_pages_messages_languages where text_id = ".$_REQUEST["a"]." and language_id = ".$_REQUEST["l"];
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			//echo $sql_query." is the query<br>\n";
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show = $result->FetchRow();
			echo $site->medium_font_tag.urldecode($show["text"])."</font>";
		}
		else
		{
			//echo "no help for that";
		}
	}

}
