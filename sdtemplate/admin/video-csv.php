<?php
/**
 * Create CSV file for sending to video merical agency.
 *
 * @author Michael Cannon <michael@peimic.com>
 * @version $Id: video-csv.php,v 1.1.1.1 2010/04/15 09:42:47 peimic.comprock Exp $
 */
include_once("../config.php");
include_once("../classes/adodb.inc.php");
include_once("../classes/site_class.php");
include_once("../classes/register_class.php");

// Peimic code helpers
include_once( '/home/movingir/local/cb_cogs/cb_cogs.config.php' );

// download helper
require_once( CB_COGS_DIR . 'cb_html.php' );

// site domain
$domain						= "http://".$_SERVER["HTTP_HOST"]."/";
$defaultImage					= "user_images/MissingImage.jpg";

error_reporting  (E_ERROR | E_WARNING | E_PARSE);

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

$query = '
	select
		id
		, optional_field_16 vin
		, optional_field_2 year
		, optional_field_13 color
		, title
		, description
		, "' . $domain . $defaultImage . '" image0
	from geodesic_classifieds
	where 1 = 1
		and live = 1
';
// $query .= ' LIMIT 2';

// file to write to
$filenameTmp					= '/tmp/' . uniqid( time() ) . '.csv';
$filelink						= fopen( $filenameTmp, 'w+' );

$result = $db->Execute($query);
if($result)
{
	$vehicleHeader			= array(
								'vin'
								, 'title'
								, 'description'
								, 'image'
							);
	$vehicleCsv				= cbMkCsvString( $vehicleHeader );

	fwrite( $filelink, $vehicleCsv );

	while ( ! $result->EOF )
    {
		$vehicleData		= $result->GetRowAssoc( false );

    	$id					= $vehicleData['id'];
		unset( $vehicleData[ 'id' ] );
    	$year					= $vehicleData['year'];
		unset( $vehicleData[ 'year' ] );
    	$color					= $vehicleData['color'];
		unset( $vehicleData[ 'color' ] );

		$vehicleData[ 'description' ]	= rawurldecode(
											$vehicleData[ 'description' ]
										);
		$vehicleData[ 'description' ]	= urldecode(
											$vehicleData[ 'description' ]
										);

		// check that title includes vehicle color
        $color = eregi_replace("([\+]+)", "+", $color);
		if ( ! preg_match("#$color#i", $vehicleData[ 'title' ] ) )
		{
			$vehicleData[ 'title' ]	= preg_replace( "#(\b$year\b)#"
										, "$1 $color"
										, $vehicleData[ 'title' ]
									);
		}

		$vehicleData[ 'title' ]			= rawurldecode(
											$vehicleData[ 'title' ]
										);
		$vehicleData[ 'title' ]			= urldecode(
											$vehicleData[ 'title' ]
										);

        $sql = "
			SELECT CONCAT('$domain', image_url) image_url
			FROM geodesic_classifieds_images_urls
			WHERE classified_id = $id
			ORDER BY display_order
            		";

        $imageResult = $db->Execute($sql);

        if ( $imageResult )
		{
			while ( ! $imageResult->EOF )
			{
				$vehicleData[]	= $imageResult->fields[ 'image_url' ];

				$imageResult->MoveNext();
			}
		}

		$vehicleDataCsv			= cbMkCsvString( $vehicleData );
		fwrite( $filelink, $vehicleDataCsv );

		$result->MoveNext();
    }

	// display 
	// make filename with today's date and vehicle list
	$today							= date( 'Y-m-d' );
	$filename						= 'ams_' . $today . '.csv';

	// read newly created file back for sending to user
	$vehicleCsv						= file_get_contents( $filenameTmp );

	fclose( $filelink );
	unlink( $filenameTmp );

	// download members list
	cbBrowserDownload( $filename, $vehicleCsv );
	// cbPrint( $vehicleCsv );
}

else
{
	echo 'No vehicles found';
}
?>