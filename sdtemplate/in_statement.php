<?php

/**
 * Update in_statement for categories.
 *
 * @author Michael Cannon <michael@peimic.com>
 * @version $Id: in_statement.php,v 1.1.1.1 2010/04/15 09:42:44 peimic.comprock Exp $
 */

// load config or db helper from GCA, GeoClassAuctions
include_once('config.php');
include_once('classes/adodb.inc.php');
include_once('classes/site_class.php');

$db								= & ADONewConnection( 'mysql' );

if ( ! $db->Connect($db_host, $db_username, $db_password, $database ) )
{
	echo "could not connect to database";
	exit;
}

// Lookup categories with parent_id = 0
$select							= "
	SELECT category_id
	FROM geodesic_categories
	WHERE 1 = 1
		AND parent_id = 0
";

// query and get resource
$result							= $db->GetAll( $select );
// cbDebug( 'result', $result );

// for each load up categories by parent
foreach ( $result as $key => $value )
{
	$value						= $value[ 'category_id' ];
	$newIn						= array( $value );

	// second level categories
	$select						= "
		SELECT category_id
		FROM geodesic_categories
		WHERE 1 = 1
			AND parent_id = $value
	";

	$subResult					= $db->GetAll( $select );

	foreach ( $subResult as $subKey => $subValue )
	{
		$subValue				= $subValue[ 'category_id' ];
		// create array containing parent and sub-category ids
		$newIn[]				= $subValue;
		$subIn					= array( $subValue );

		// third level categories
		$select					= "
			SELECT category_id
			FROM geodesic_categories
			WHERE 1 = 1
				AND parent_id = $subValue
		";

		$subberResult			= $db->GetAll( $select );

		foreach ( $subberResult as $subberKey => $subberValue )
		{
			$subberValue				= $subberValue[ 'category_id' ];
			$newIn[]					= $subberValue;
			$subIn[]					= $subberValue;
		}

		// sort
		sort( $subIn );

		// implode to create csv of parent and sub cats
		$subIn							= implode( ',', $subIn );

		// create in_statement
		// update sql
		$update							= "
			update geodesic_categories
			set in_statement = 'in ( $subIn )'
			where category_id = $subValue
		";

		// $subUpdate					= $db->Execute( $update );
		cbDebug( 'sub update', $update );
	}

	// sort
	sort( $newIn );

	// implode to create csv of parent and sub cats
	$newIn						= implode( ',', $newIn );

	// create in_statement
	// update sql
	$update						= "
		update geodesic_categories
		set in_statement = 'in ( $newIn )'
		where category_id = $value
	";

	// $subUpdate					= $db->Execute( $update );
	cbDebug( 'update', $update );
}

?>
