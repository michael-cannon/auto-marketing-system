<?php
/**
 * Return list of active sellers from current sales
 *
 * @author Michael Cannon <michael@peimic.com>
 * @version $Id: sellers.php,v 1.1.1.1 2010/04/15 09:42:45 peimic.comprock Exp $
 */

include_once("config.php");
include_once("classes/adodb.inc.php");

$db								= & ADONewConnection('mysql');

if( ! $db->Connect($db_host, $db_username, $db_password, $database) )
{
	die( "Could not connect to database" );
}

@$db->SetFetchMode( ADODB_FETCH_ASSOC );

$query							= "
	/* Grab active sellers from ads */
	SELECT c.seller
		, u.company_name
		, u.city
		, u.state
	FROM geodesic_classifieds c
	LEFT JOIN geodesic_userdata u ON c.seller = u.id
	WHERE 1 = 1
		AND c.live = 1
	GROUP BY c.seller
	;
								";

$result							= $db->GetArray( $query );

$db->Close();

$return							= ( 0 < count( $result ) )
									? serialize( $result )
									: '';

echo $return;
?>