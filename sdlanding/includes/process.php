<?php

/**
 * Form mail caller
 *
 * @author Michael Cannon <michael@peimic.com>
 * @version $Id: process.php,v 1.1.1.1 2010/04/15 09:42:35 peimic.comprock Exp $
 */

include_once( '../config.php' );

// Formmail bits
$_REQUEST[ "to" ]                = MI_SALE_EMAIL;
// $_REQUEST[ "to" ]                = 'michael@peimic.com';
$_REQUEST[ "from_email" ]        = MI_SALE_EMAIL;
$_REQUEST[ "subject" ]            = MI_DEALER_NAME_LOC;
$_REQUEST[ "thankyou" ]            = MI_THANK_YOU;
$_REQUEST[ "log_email" ]        = MI_LEADS_FILE_LOG;
$_REQUEST[ "referrer" ]            = $_SESSION[ 'referrer' ];
$_REQUEST[ "remote_address" ]    = ( isset(
                                        $_SERVER[ 'HTTP_X_FORWARDED_FOR' ]
                                    ) )
                                    ? $_SERVER[ 'HTTP_X_FORWARDED_FOR' ]
                                    : $_SERVER[ 'REMOTE_ADDR' ];

// MLC 20070914 spam stop via privacy check
if ( isset( $_REQUEST[ 'Privacy_Notice' ] )
	&& isset( $_REQUEST[ 'Privacy' ] )
	&& substr( $_REQUEST[ 'Privacy' ], 0, 10 ) != 'I hereby a'
)
{
	exit();
}

// Prep lead data for database entry
foreach ( $_REQUEST AS $key => $value )
{
    // grab form pieces, clean them up, format, and append
    $value                        = cbCleanStr($value);

    $_REQUEST[ $key ]            = $value;
}

if ( MI_LEADS_DB_LOG )
{
    $db                            = mysql_connect( MI_LEADS_HOST, MI_LEADS_USER
                                        , MI_LEADS_PW
                                    );
    mysql_select_db( MI_LEADS_DB );
    $subdomain                    = MI_SALE_DOMAIN;
    $now                        = time();

    $submitted                    = mysql_real_escape_string( cbPrintString(
                                    $_REQUEST ) );

    $insert =
        "INSERT INTO leads_userdata
        SET email = \"".$_REQUEST[ 'Email' ]."\"
            , firstname = \"".ucwords(strtolower(trim($_REQUEST[ 'First_Name' ])))."\"
            , lastname = \"".ucwords(strtolower(trim($_REQUEST[ 'Last_Name' ])))."\"
            , address = \"".$_REQUEST[ 'Address' ]."\"
            , address_2 = \"".$_REQUEST[ 'Address_2' ]."\"
            , city = \"".$_REQUEST[ 'City' ]."\"
            , state = \"".$_REQUEST[ 'State' ]."\"
            , zip = \"".$_REQUEST[ 'Zip' ]."\"
            , phone = \"".$_REQUEST[ 'Home_Phone' ]."\"
            , phone2 = \"".$_REQUEST[ 'Work_Phone' ]."\"
            , phoneext_2 = \"".$_REQUEST[ 'Work_Phone_Ext' ]."\"
            , referrer = \"".$_SESSION[ 'referrer' ]."\"
            , subdomain = \"".$subdomain."\"
            , date_joined = \"".$now."\"
            , remote_addr = \"".$_REQUEST[ 'remote_address' ]."\"
            , submitted = \"".$submitted."\"
            , session_id = \"".$_REQUEST[ 'PHPSESSID' ]."\"
            ON DUPLICATE KEY UPDATE
            email = \"".$_REQUEST[ 'Email' ]."\"
            , address = \"".$_REQUEST[ 'Address' ]."\"
            , address_2 = \"".$_REQUEST[ 'Address_2' ]."\"
            , city = \"".$_REQUEST[ 'City' ]."\"
            , state = \"".$_REQUEST[ 'State' ]."\"
            , zip = \"".$_REQUEST[ 'Zip' ]."\"
            , phone = \"".$_REQUEST[ 'Home_Phone' ]."\"
            , phone2 = \"".$_REQUEST[ 'Work_Phone' ]."\"
            , phoneext_2 = \"".$_REQUEST[ 'Work_Phone_Ext' ]."\"
            , referrer = \"".$_SESSION[ 'referrer' ]."\"
            , date_joined = \"".$now."\"
            , remote_addr = \"".$_REQUEST[ 'remote_address' ]."\"
            , submitted = \"".$submitted."\"
    ";

    @mysql_query( $insert );
    mysql_close( $db );
}

include_once( CB_COGS_DIR . 'cb_formmail.php' );

?>
