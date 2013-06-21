<?php

require_once( '/home/bigdeale/local/cb_cogs/cb_cogs.config.php' );
// require_once( '/Users/michael/Sites/local/cb_cogs/cb_cogs.config.php' );
require_once( CB_COGS_DIR . 'cb_html.php' );

/*
$db_host = 'localhost';
$db_username = 'movingir_statist';
$db_password = 'vejocrud';
$db_name = 'movingir_statistic';

require_once("./classes/db_class.php");

$conn = new DB($db_host, $db_username, $db_password, $db_name);
$conn->open();
*/

$templateFile					= 'templates/email-winner.txt';

$template						= file( $templateFile );

$from							= "From: nissanofmckinney@bigdealer-sales.com ";
$bcc							= "Bcc: DInboden@aol.com,acqal.cto@gmail.com";
$mailHeader						= $from . "\n" . $bcc;
$to								= '';
$subject						= array_shift( $template );
$body							= implode( '', $template );

// cbDebug( 'subject', $subject );
// cbDebug( 'body', $body );

/*
$select							= <<<EOD
	SELECT
		u.email
		, u.firstname
		, u.username
		, l.password
		, "http://prestigeford.movingiron.com/" url
	FROM
		geodesic_userdata u
		LEFT JOIN geodesic_logins l ON u.id = l.id
	WHERE
		1 = 1
		AND l.id IS NOT NULL
		AND u.referrer LIKE "%reposale.info%"
		AND u.date_joined >= UNIX_TIMESTAMP("2006-09-15 00:00:00")
EOD;

$result							= mysql_query( $select, $conn->conn );

while ( $result && $data = mysql_fetch_assoc( $result ) )
	*/
$users = array();
$users[] = array(
	'username' => 'bendersw'
	, 'email' => 'bendersw@yahoo.com'
	, 'url' => 'sale.com'
);
$users[] = array(
	'username' => 'marijan71'
	, 'email' => 'marijan71@tx.rr.com'
	, 'url' => 'sale.com'
);
$users[] = array(
	'username' => 'catsbid'
	, 'email' => 'bdt4004@tx.rr.com'
	, 'url' => 'sale.com'
);
$users[] = array(
	'username' => 'chrislemens'
	, 'email' => 'chrislemens@yahoo.com'
	, 'url' => 'sale.com'
);
foreach( $users as $data ) {
	$mailTo						= $data[ 'email' ];
	$mailBody					= $body;
	
	$search						= array(
									'###USERNAME###'
									, '###URL###'
								);

	$replace					= array(
									$data[ 'username' ]
									, $data[ 'url' ]
								);

	$mailBody					= str_replace( $search, $replace, $mailBody );
	// $mailTo						= 'michael@peimic.com';

	if ( ! mail( $mailTo, $subject, $mailBody, $mailHeader ) ) {
	 	echo "<h1>Mail Not Sent</h1>";
		cbMail2html( $mailTo, $subject, $mailBody, $mailHeader );
		cbDebug( 'data', $data );	
	}
}

// mysql_free_result( $result );
?>
