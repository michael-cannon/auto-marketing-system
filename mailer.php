<?php

require_once( '/home/movingir/local/cb_cogs/cb_cogs.config.php' );
require_once( CB_COGS_DIR . 'cb_html.php' );

$db_host = 'localhost';
$db_username = 'movingir_statist';
$db_password = 'vejocrud';
$db_name = 'movingir_statistic';

$printable = 1;

require_once("./classes/db_class.php");

$conn = new DB($db_host, $db_username, $db_password, $db_name);
$conn->open();

$templateFile					= 'templates/email-prestige.txt';

$template						= file( $templateFile );

$from							= "From: Tyler Stokes <tyler@movingiron.com>";
$bcc							= "Bcc: Tyler Stokes <tyler@movingiron.com>";
// $bcc							= "Bcc: M C <michael@movingiron.com>";
$mailHeader						= $from . "\n" . $bcc;
$to								= '';
$subject						= array_shift( $template );
$body							= implode( '', $template );

// cbDebug( 'subject', $subject );
// cbDebug( 'body', $body );

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
{
	$mailTo						= $data[ 'email' ];
	$mailBody					= ( $data[ 'firstname' ] )
									? $data[ 'firstname' ]
										. ",\n\n"
										. $body
									: $body;
	
	$search						= array(
									'###USERNAME###'
									, '###PASSWORD###'
									, '###URL###'
								);

	$replace					= array(
									$data[ 'username' ]
									, $data[ 'password' ]
									, $data[ 'url' ]
								);

	$mailBody					= str_replace( $search, $replace, $mailBody );
	// $mailTo						= 'michael@peimic.com';

	if ( ! mail( $mailTo, $subject, $mailBody, $mailHeader ) )
	{
		echo "<h1>Mail Not Sent</h1>";
		echo cbMail2html( $mailTo, $subject, $mailBody, $mailHeader );
		cbDebug( 'data', $data );
	}
}

mysql_free_result( $result );
?>
