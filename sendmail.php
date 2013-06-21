<?php
/**
 * Sendmail program
 * Automotive Marketing System
 *
 * @author Michael Cannon <michael@peimic.com>
 * @version $Id: sendmail.php,v 1.1.1.1 2010/04/15 09:42:34 peimic.comprock Exp $
 */

require_once('config.php');
require_once('classes/db_class.php');
require_once('classes/sendmail_class.php');

$conn = new DB($db_host, $db_username, $db_password, $db_name);
$conn->open();

$conn1 = new DB($db_host, $db_username, $db_password, $db_name);
$conn1->open();

$sendmail = new Sendmail($conn, $conn1, $cpConfig);
$sendmail->debug = 0;

if ( ! $sendmail->check_accounts() ) {
	if ( true ) {
		echo "Leads emails not sent";
		echo "<br>error ".$sendmail->error;
		echo "<br>errno ".$sendmail->errno;
	}
} else {
	if ( false ) {
		echo "Leads email sent";
		echo "<br>error ".$sendmail->error;
		echo "<br>errno ".$sendmail->errno;
	}
}
?>