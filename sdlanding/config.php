<?php

/**
 * Movingiron templates configuration
 *
 * @author Michael Cannon <michael@peimic.com>
 * @version $Id: config.php,v 1.1.1.1 2010/04/15 09:42:35 peimic.comprock Exp $
 */

// BEGIN DEALER LANDING DOMAIN EDITING
// Don't edit the `define( 'MI_WHATEVER',`
// Just edit the define parts on the right.

// Landing domain
define( 'MI_SALE_DOMAIN',		'@@MI_SALE_DOMAIN@@' );

// Sale theme text
// Private Sale - private
// Fleet Liquidation - fleet
// Repo Sale - repo
define( 'MI_SALE_TYPE',			'private' );

// Dealer details
// Dealer name
define( 'MI_DEALER_NAME',		"@@DEALER_NAME@@" );

// Dealer city
define( 'MI_DEALER_CITY',		'@@MI_DEALER_CITY@@' );

// Sale dates
define( 'MI_SALE_DATES',		'@@MI_SALE_DATES@@' );

// Dealer street addres
define( 'MI_DEALER_STREET',		'@@MI_DEALER_STREET@@' );

// Dealer city, state zip
define( 'MI_DEALER_CSZ',		'@@MI_DEALER_CITY@@, @@MI_DEALER_STATE@@ @@MI_DEALER_ZIP@@' );

// Number of vehicles to be sold
define( 'MI_DEALER_INV_COUNT',	'@@MI_DEALER_INV_COUNT@@' );

// Videos, google map
// Welcome video
define( 'MI_VIDEO_WELCOME',		'' );

// Registration video
define( 'MI_VIDEO_REGISTER',	'' );
// define( 'MI_VIDEO_REGISTER',	'RegisterBelowVideo/RegisterBelow.swf' );

// Google map image name
// Leave blank if none
define( 'MI_DEALER_MAP_IMAGE',	'' );

// Person to get sale leads.
// Can be multiple emails when separated by commas
define( 'MI_SALE_EMAIL',		'@@MI_SALE_EMAIL@@' );

// Classified/Acution site
// Leave blank '' if no auction
define( 'MI_AUCTION_URL',		'@@MI_AUCTION_URL@@' );
// END DEALER LANDING DOMAIN EDITING


// DON'T EDIT BELOW THIS LINE
define( 'MI_DEALER_NAME_LOC',	MI_DEALER_NAME . ' in ' . MI_DEALER_CITY );

define( 'MI_OWNER_NAME',		'@@OWNER_NAME@@' );
define( 'MI_COPYRIGHT',			'Copyright &copy; ' . date('Y') );

$address						= MI_DEALER_STREET . ' ' . MI_DEALER_CSZ;
$address						= preg_replace( "#\s+#", '+', $address );
$address						= preg_replace( "#,#", '', $address );
define( 'MI_DEALER_MAP_URL',	'http://maps.google.com/maps?f=q&hl=en&q='
									. $address
								);

define( 'MI_THANK_YOU',			MI_SALE_DOMAIN . 'thank-you.php' );

// File log of emails
define( 'MI_LEADS_FILE_LOG',	'true' );

// Database log file of emails
define( 'MI_LEADS_DB_LOG',		true );

// Leads db
define( 'MI_LEADS_HOST',		'@@API_HOST@@' );
define( 'MI_LEADS_DB',			'@@API_DATABASE@@' );
define( 'MI_LEADS_USER',		'@@API_USERNAME@@' );
define( 'MI_LEADS_PW',			'@@API_PASSWORD@@' );

// Sale theme text
if ( 'private' == MI_SALE_TYPE )
{
	// Sale theme CSS
	define( 'MI_SALE_CSS' ,		'styles-baseline.css' );
	define( 'MI_SALE_TEXT' ,	'@@MI_SALE_TEXT@@' );
	define( 'MI_SALE_FORM' ,	'Invitation' );
	define( 'MI_SALE_LOGO' ,	'' );
}

else if ( 'fleet' == MI_SALE_TYPE )
{
	define( 'MI_SALE_CSS' ,		'styles-baseline.css' );
	define( 'MI_SALE_TEXT',		'Fleet Liquidation' );
	define( 'MI_SALE_FORM' ,	'Invitation' );
	define( 'MI_SALE_LOGO',		'' );
}

else if ( 'repo' == MI_SALE_TYPE )
{
	define( 'MI_SALE_CSS' ,		'styles-baseline.css' );
	define( 'MI_SALE_TEXT',		'Repo Sale' );
	define( 'MI_SALE_FORM' ,	'Application' );
	define( 'MI_SALE_LOGO',		'<center><img src="images/wholesalers.gif" width="240" height="100" alt="USA Wholesalers" /></center>' );
}

else 
{
	define( 'MI_SALE_CSS' ,		'' );
	define( 'MI_SALE_TEXT',		'Set MI_SALE_TYPE to private, fleet, or repo in config.php' );
	define( 'MI_SALE_LOGO',		'' );
}

// cb_cogs code libary
include_once( 'includes/cb_cogs/cb_cogs.config.php' );

session_start();
session_cache_expire("1440");

if ( ! isset( $_SESSION[ 'referrer' ] ) )
{
	$_SESSION[ 'referrer' ]		= $_SERVER[ 'HTTP_REFERER' ];
}

// counter and visitor tracking
include_once( 'includes/counter.php' );

?>