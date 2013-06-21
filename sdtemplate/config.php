<? //config.php
// MLC debug tools
require_once( '/home/@@MI_USER@@/local/cb_cogs/cb_cogs.config.php' );
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

/*
 Welcome to the Geodesic Software Configuration File. This file is the link
 between the php files that run the functions of the software and your
 database which stores all of the information.

We have organized the file in sections. Please follow the below carefully to
ensure a successful installation
*/


// STEP 1 ################### MySQL Database Settings #######################
/*
The information below is for the main installation. For configuring an instalation with the API
see further below.
*/

$db_host = "localhost";//location of sql host - usually localhost
$db_username = "@@USERNAME@@";//username used to connect to database
$db_password = "@@PASSWORD@@";//password used to connect to database
$database = "@@DATABASE@@";//name of database

//########################## MySQL Database Settings #######################

// STEP 2 ################### Database Connection Type #######################
/*
OPTIONAL
Uncomment the below line($persistent_connections = 1) if you want to use
persistent database connections.
Some hosts do not allow this so only do so if you are sure that you want
to use this type of connection. To uncomment, remove the two slashes.
*/

//$persistent_connections = true;
$persistent_connections = false;

############################# Database Connection Type #######################

// STEP 3 ################### API MySQL Database Settings #######################
/*
ONLY COMPLETE IF USING THE API.
The information below is for the installation of the API. For configuring an instalation with the API
see further below.
*/

$api_db_host = "@@API_HOST@@";//location of sql host - usually localhost
$api_db_username = "@@API_USERNAME@@";//username used to connect to database
$api_db_password = "@@API_PASSWORD@@";//password used to connect to database
$api_database = "@@API_DATABASE@@";//name of database

//########################## API MySQL Database Settings #######################

// Show video object via videomerical API
define('SHOW_VIDEO_DEFAULT', @@SHOW_VIDEO_DEFAULT@@);
$video_account					= '@@VIDEO_ACCOUNT@@';
$video_site						= '@@VIDEO_SITE@@';
$video_token					= '@@VIDEO_TOKEN@@';

$cpPriceDelta					= '@@cpPriceDelta@@';

//////////////////////// DO NOT EDIT BELOW THIS LINE ////////////////////////

$product_type = 4;

/////////////////////////////////////////////////////////////////////////////////////////////////////
/*
The following are controls for beta testing features.  Do not edit these controls without direction
from Geodesic Support.  Changing these values could affect the stability of your installation.
Proceed with caution.
*/

//this controls whether or not a client browsing your site should have a subscription
//to view an ads details.  If set to 1 and the client does not have a subscription
//they will not be able to view an ad details
define ("MUST_HAVE_SUBSCRIPTION_TO_VIEW_AD_DETAIL",0);

//this controls the default users communication
//configuration setting at the time of registration.  The default configuration
//setting is "1".  This is the public communication configuration for all new registrants at the time
//of registration.  To change to the completely private setting at time of registration for all new clients
//change this to 3.  The client can always change their configuration after they have finished
//registration.  The only possible setting for this are 1 or 3.
define("DEFAULT_COMMUNICATION_SETTING",1);

//if set to 1 this will allow any bidder to bid against themselves even if they are the current
//high bidder.  The current default controls will not allow the current high bidder to bid against
//themselves.  If this is set to 0 the default code will not allow the current high bidder to
//bid against themselves...searching for the hidden reserve price.
define("ALLOW_BIDDING_AGAINST_SELF", 0);

// If set to 1 this will allow the user to copy a current or newly expired listing into a brand
// new listing.  It will pop them directly to the approval page.
define("ALLOW_COPYING_NEW_LISTING", 0);

?>