<?php
require_once( 'config.php' );

$subdomain = $argv[1];
$user_id = $argv[2] ? '&user_id=' . $argv[2] : '';
# MLC 20080602 ignore user id, convert all
$user_id = '';
$mailto = $adminEmail;
$from = $cpEmail;
$subject = $cpUrl . ": converting classifieds to auctions.";

$head      = "From: $from\n";
$head     .= "X-Mailer: PHPMail Tool\n";
$head     .= "Reply-To: $from\n";
$head     .= "Mime-Version: 1.0\n";
$head     .= "Content-Type:text/plain;";

if(!$subdomain)
{
	mail($mailto, $subject, "Error: subdomain undefine.", $head);
	exit();
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$subdomain.$cpConfig['postfix']."/admin/index.php");
curl_setopt($ch, CURLOPT_POST, 1 );
curl_setopt($ch, CURLOPT_POSTFIELDS, 'b[username]=miadmin&b[password]=H0iB6k');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$store = curl_exec($ch);

$url							= "http://".$subdomain.$cpConfig['postfix']."/admin/index.php";
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'a=302&b=2'.$user_id);
$url							= "http://".$subdomain.$cpConfig['postfix']."/admin/index.php?a=302&b=2".$user_id;
$content = curl_exec ($ch);

if(curl_errno($ch))
{
	curl_close($ch);
	$error = "Can't convert classifieds to auctions on site ".$subdomain.$cpConfig['postfix'];
	mail($mailto, $subject, $error, $head);
}

$answer = "Classifieds have been converted to auctions successfully on site ".$subdomain.$cpConfig['postfix'];
$answer .= "\nConvert URL {$url}.";

curl_setopt($ch, CURLOPT_URL, "http://".$subdomain.$cpConfig['postfix']."/admin/index.php");
curl_setopt($ch, CURLOPT_POSTFIELDS, 'a=70');
$content = curl_exec ($ch);

if(curl_errno($ch))
{
	curl_close($ch);
	$error = "Can't update category count on site ".$subdomain.$cpConfig['postfix'];
	mail($mailto, $subject, $error, $head);
}

curl_close($ch);

$answer .= "\n\nCategory count updated.";

mail($mailto, $subject, $answer, $head);

?>