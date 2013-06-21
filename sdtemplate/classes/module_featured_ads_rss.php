<? //module_featured_ads_rss.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

include_once("../config.php");
include_once("adodb.inc.php");
include_once("site_class.php");
error_reporting  (E_ERROR | E_WARNING | E_PARSE);
if (isset ($HTTP_SERVER_VARS))
{
	$_SERVER = $HTTP_SERVER_VARS;
}
$db = &ADONewConnection('mysql');
//$db = &ADONewConnection('access');
//$db = &ADONewConnection('ado');
//$db = &ADONewConnection('ado_mssql');
//$db = &ADONewConnection('borland_ibase');
//$db = &ADONewConnection('csv');
//$db = &ADONewConnection('db2');
//$db = &ADONewConnection('fbsql');
//$db = &ADONewConnection('firebird');
//$db = &ADONewConnection('ibase');
//$db = &ADONewConnection('informix');
//$db = &ADONewConnection('mssql');
//$db = &ADONewConnection('mysqlt');
//$db = &ADONewConnection('oci8');
//$db = &ADONewConnection('oci8po');
//$db = &ADONewConnection('odbc');
//$db = &ADONewConnection('odbc_mssql');
//$db = &ADONewConnection('odbc_oracle');
//$db = &ADONewConnection('oracle');
//$db = &ADONewConnection('postgres7');
//$db = &ADONewConnection('postgress');
//$db = &ADONewConnection('proxy');
//$db = &ADONewConnection('sqlanywhere');
//$db = &ADONewConnection('sybase');
//$db = &ADONewConnection('vfp');

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

// if you want to display a specific category set category_id below to that
//categories id...otherwise leave its value 0 to display featured ads from all categories
//the choice of featured is completely random
$category_id = 0;

//set the number of featured ads you want to display within the feed here
//it has been defaulted to 10
$number_of_ads_to_display = 10;

//set 0 next to all fields you DO NOT want to display
//set 1 next to all field you do want to display
//the fields will appear in the order below.
$show_title = 1;
$show_description = 1;
$show_optional_field_1 = 0;
$show_optional_field_2 = 0;
$show_optional_field_3 = 0;
$show_optional_field_4 = 0;
$show_optional_field_5 = 0;
$show_optional_field_6 = 0;
$show_optional_field_7 = 0;
$show_optional_field_8 = 0;
$show_optional_field_9 = 0;
$show_optional_field_10 = 0;
$show_optional_field_11 = 0;
$show_optional_field_12 = 0;
$show_optional_field_13 = 0;
$show_optional_field_14 = 0;
$show_optional_field_15 = 0;
$show_optional_field_16 = 0;
$show_optional_field_17 = 0;
$show_optional_field_18 = 0;
$show_optional_field_19 = 0;
$show_optional_field_20 = 0;
$show_city = 1;
$show_state = 1;
$show_country = 1;
$show_zip = 0;
$show_price = 0;
$show_entry_date = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

$site = new Site($db,2,1,0);

if (!$site->in_statement)
{
	if ($site->configuration_data['display_sub_category_ads'])
		$site->get_sql_in_statement($db,$category_id);
	else
		$site->in_statement = " in (".$category_id.") ";
}

$seed = rand();
$site->sql_query = "select * from geodesic_classifieds where featured_ad = 1 and live = 1";
if ($category_id)
	$site->sql_query .= " and category ".$site->in_statement;
$site->sql_query .= " order by rand(".$seed.") limit ".$number_of_ads_to_display;
$featured_result = $db->Execute($site->sql_query);
//echo $site->sql_query."<br>\n";
if ($featured_result->RecordCount() > 0)
{
	$body = "<\?xml version=\"1.0\"\?>\n<rss>";
 	//$number_of_ads_to_display;
	//$result->Move(0);
	//$site->display_featured_1_result($db,$featured_result);
	$site->row_count = 0;
	while ($show_classifieds = $featured_result->FetchRow())
	{
			$body .="<item>\n\t\t";
			if ($show_title)
			{
				$body .="<title>";
				$body .= "<a href=".$site->configuration_data['classifieds_url']."?a=2&b=".$show_classifieds['id'].">";
				$body .= stripslashes(urldecode($show_classifieds['title']))."</a>\n\t\t";
				$body .="</title>\n\t\t";
			}
			if ($show_description)
			{
				$body .="<description>".stripslashes(urldecode($show_classifieds['description']))."</description>\n\t";
			}

			if ($show_optional_field_1)
			{
				$body .="<optional_field_1>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_1']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_1']));
				else
					$body .=	"-";
				$body .="</optional_field_1>\n\t";
			}

			if ($show_optional_field_2)
			{
				$body .="<optional_field_2>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_2']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_2']));
				else
					$body .=	"-";
				$body .="</optional_field_2>\n\t";
			}

			if ($show_optional_field_3)
			{
				$body .="<optional_field_3>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_3']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_3']));
				else
					$body .=	"-";
				$body .="</optional_field_3>\n\t";
			}

			if ($show_optional_field_4)
			{
				$body .="<optional_field_4>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_4']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_4']));
				else
					$body .=	"-";
				$body .="</optional_field_4>\n\t";
			}

			if ($show_optional_field_5)
			{
				$body .="<optional_field_5>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_5']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_5']));
				else
					$body .=	"-";
				$body .="</optional_field_5>\n\t";
			}

			if ($show_optional_field_6)
			{
				$body .="<optional_field_6>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_6']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_6']));
				else
					$body .=	"-";
				$body .="</optional_field_6>\n\t";
			}

			if ($show_optional_field_7)
			{
				$body .="<optional_field_7>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_7']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_7']));
				else
					$body .=	"-";
				$body .="</optional_field_7>\n\t";
			}

			if ($show_optional_field_8)
			{
				$body .="<optional_field_8>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_8']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_8']));
				else
					$body .=	"-";
				$body .="</optional_field_8>\n\t";
			}

			if ($show_optional_field_9)
			{
				$body .="<optional_field_9>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_9']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_9']));
				else
					$body .=	"-";
				$body .="</optional_field_9>\n\t";
			}

			if ($show_optional_field_10)
			{
				$body .="<optional_field_10>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_10']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_10']));
				else
					$body .=	"-";
				$body .="</optional_field_10>\n\t";
			}

			if ($show_optional_field_11)
			{
				$body .="<optional_field_11>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_11']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_11']));
				else
					$body .=	"-";
				$body .="</optional_field_11>\n\t";
			}

			if ($show_optional_field_12)
			{
				$body .="<optional_field_12>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_12']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_12']));
				else
					$body .=	"-";
				$body .="</optional_field_12>\n\t";
			}

			if ($show_optional_field_13)
			{
				$body .="<optional_field_13>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_13']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_13']));
				else
					$body .=	"-";
				$body .="</optional_field_13>\n\t";
			}

			if ($show_optional_field_14)
			{
				$body .="<optional_field_14>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_14']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_14']));
				else
					$body .=	"-";
				$body .="</optional_field_14>\n\t";
			}

			if ($show_optional_field_15)
			{
				$body .="<optional_field_15>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_15']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_15']));
				else
					$body .=	"-";
				$body .="</optional_field_15>\n\t";
			}

			if ($show_optional_field_16)
			{
				$body .="<optional_field_16>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_16']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_16']));
				else
					$body .=	"-";
				$body .="</optional_field_16>\n\t";
			}

			if ($show_optional_field_17)
			{
				$body .="<optional_field_17>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_17']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_17']));
				else
					$body .=	"-";
				$body .="</optional_field_17>\n\t";
			}

			if ($show_optional_field_18)
			{
				$body .="<optional_field_18>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_18']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_18']));
				else
					$body .=	"-";
				$body .="</optional_field_18>\n\t";
			}

			if ($show_optional_field_19)
			{
				$body .="<optional_field_19>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_19']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_19']));
				else
					$body .=	"-";
				$body .="</optional_field_19>\n\t";
			}

			if ($show_optional_field_20)
			{
				$body .="<optional_field_20>";
				if (strlen(trim(urldecode($show_classifieds['optional_field_20']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['optional_field_20']));
				else
					$body .=	"-";
				$body .="</optional_field_20>\n\t";
			}

			if ($show_city)
			{
				$body .="<city>";
				if (strlen(trim(urldecode($show_classifieds['location_city']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['location_city']));
				else
					$body .=	"-";
				$body .="</city>\n\t";
			}

			if ($show_state)
			{
				$body .="<state>";
				if (strlen(trim(urldecode($show_classifieds['location_state']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['location_state']));
				else
					$body .=	"-";
				$body .="</state>\n\t";
			}

			if ($show_country)
			{
				$body .="<country>";
				if (strlen(trim(urldecode($show_classifieds['location_country']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['location_country']));
				else
					$body .=	"-";
				$body .="</country>\n\t";
			}

			if ($show_zip)
			{
				$body .="<zip>";
				if (strlen(trim(urldecode($show_classifieds['location_zip']))) > 0)
					$body .=stripslashes(urldecode($show_classifieds['location_zip']));
				else
					$body .=	"-";
				$body .="</zip>\n\t";
			}


			if ($show_price)
			{
				$body .="<price>";
				if (($show_classifieds['price'] != 0)
					|| (strlen(trim(urldecode($show_classifieds['precurrency']))) > 0)
					|| (strlen(trim(urldecode($show_classifieds['postcurrency']))) > 0))
				{
					if (floor($show_classifieds['price']) == $show_classifieds['price'])
					{
						$body .= stripslashes(urldecode($show_classifieds['precurrency'])). " ".
							number_format($show_classifieds['price'])." ".
							stripslashes(urldecode($show_classifieds['postcurrency']));
					}
					else
					{
						$body .= stripslashes(urldecode($show_classifieds['precurrency'])). " ".
							number_format($show_classifieds['price'],2,".",",")." ".
							stripslashes(urldecode($show_classifieds['postcurrency']));
					}
				}
				else
					$body .=	"-";
				$body .="</price>\n\t";
			}

			if ($show_entry_date)
			{
				$body .="<entry_date>".date(trim($site->configuration_data['entry_date_configuration']),$show_classifieds['date'])."</entry_date>\n\t";
			}
			$body .="</item>\n\t";
			$site->row_count++;
		} //end of while
	echo $body."</rss>";
	exit;
}
else
{
	echo "no feed currently";
}
?>
</rss>