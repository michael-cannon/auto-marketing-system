<?php
/**
 * Site creator processor
 *
 * @author Michael Cannon <michael@peimic.com>
 * $Id: createSite.php,v 1.1.1.1 2010/04/15 09:42:34 peimic.comprock Exp $
 */
require_once( 'config.php' );

// skip cpanel calls. used for testing
$skipCpanel				= false;
$skipCleanup			= false;

set_time_limit(3600);

$debug							= 0;

// print_r($_REQUEST);
if(empty($_REQUEST['name']) || empty($_REQUEST['address']) || empty($_REQUEST['city']) || empty($_REQUEST['state']) || empty($_REQUEST['zip']) || empty($_REQUEST['site']) || empty($_REQUEST['miurl']))
{
    $error = 16;
	cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );

    $reqStr = '';
    foreach($_REQUEST as $key => $value)
    {
        $reqStr .= "$key=$value&";
    }
    header("location: $cpUrl/createsd.php?$reqStr");
    exit();
}

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n";

if($log = fopen('sitecreate.log', "a"))
{
	$logStr .= date("Y M d H:i:s")."\t".$_SERVER['HTTP_HOST']."\t".$_SERVER['SCRIPT_NAME'];
    foreach($_REQUEST as $key => $value)
    {
        $logStr .= "\t".$key.':'.$value;
    }
    $logStr .= "\n";

   if(fwrite($log, $logStr, 4096) === FALSE)
   {
       echo "Can't add string to file ($log)";
       exit;
   }
}
else {echo "Can't open file $log"; exit();}

$error = 0;
$errorStr = '';
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

$name							= cbRequest('name');
$address						= cbRequest('address');
$city							= cbRequest('city');
$state							= cbRequest('state');
$zip							= cbRequest('zip');
$miurl							= cbRequest('miurl');
$sitename						= cbRequest('site');
$landing						= cbRequest('landing');
$email							= cbRequest('email', $cpEmail);
$start							= cbRequest('start');
$convert						= cbRequest('convert');
$end							= cbRequest('end');
$special						= cbRequest('special');
$invcount						= cbRequest('invcount', $cpInvCount);
$bkgColor						= cbRequest('bkgColor', $cpBkgColor);
$saleText						= cbRequest('saleText', $cpSaleText);
$headerImage					= cbRequest('headerImage');
$pocFirst						= cbRequest('pocFirst', $cpFirst);
$pocLast						= cbRequest('pocLast', $cpLast);
$pocPhone						= cbRequest('pocPhone', $cpPhone);
$dateStart						= cbRequest( 'start', strtotime( 'now' ) );
$dateEnd						= cbRequest( 'end', strtotime( 'next Sunday' ) );
$dates							= date( 'l F jS', strtotime( $dateStart )  )
									. ' to '
									. date( 'l F jS', strtotime( $dateEnd )  );
$year							= date( 'Y' );


// determine the full landing URL
$landingParse = parse_url( $landing );
$landing						= isset( $landingParse[ 'host'] )
									? $landingParse[ 'host']
									: $landingParse[ 'path'];
$landing						= 'http://' . trim( $landing ) . '/';

$parts = explode(".", $miurl);
$subdomain = str_replace("http://", "", $parts[0]);
$subdomain = strtolower(trim($subdomain));

$dealerName = $name;

$prefix = $db_prefix;
if(strlen($subdomain) > 6) $str = substr($subdomain, 0, 6);
else $str = $subdomain;

if(strlen($subdomain) > 4) $str1 = substr($subdomain, 0, 4);
else $str1 = $subdomain;

$dbName = $str;
$userName = $str1;

$passStr = substr(md5(rand()), 0, 5);
$pocpassword = substr(md5(rand()), 0, 8);
$userPassword = $passStr.$str;

$months = Array("January" => 1,
				"February" => 2,
				"March" => 3,
				"April" => 4,
				"May" => 5,
				"June" => 6,
				"July" => 7,
				"August" => 8,
				"September" => 9,
				"October" => 10,
				"November" => 11,
				"December" => 12);

$stArr = explode(" ", $start, 3);
$cvArr = explode(" ", $convert, 3);
$enArr = explode(" ", $end, 3);

$start = date("F d Y", mktime(0, 0, 0, $months[$stArr[0]], $stArr[1], $stArr[2]));
$convert = date("F d Y", mktime(0, 0, 0, $months[$cvArr[0]], $cvArr[1], $cvArr[2]));
$end = date("F d Y", mktime(0, 0, 0, $months[$enArr[0]], $enArr[1], $enArr[2]));

$errors = Array(1 => 'Can\'t create subdomain.',
                2 => 'Can\'t create database.',
                3 => 'Can\'t create user.',
                4 => 'Can\'t add user to database.',
                5 => 'Can\'t copy files from Templates folder to new Subdomain folder.',
                6 => 'Can\'t import data to the DataBase.',
                7 => 'Can\'t open config.php file.',
                8 => 'Can\'t open config.php file.',
                9 => 'Can\'t write to config.php file',
                10 => 'Can\'t connect to database.',
                11 => 'Can\'t select database.',
                12 => 'Can\'t execute query.',
                13 => 'Can\'t add new site database parameters to API.',
                14 => 'Can\'t create GeoSystem user. Bad URL or no license?',
                15 => 'Can\'t create directory. This directory is already exists.',
                16 => '* Indicates required fields.',
                17 => 'Can\'t change email data on geodesic_user_data.',
                18 => 'Can\'t open directory: /home/mi_user/public_html/cp/sdtemplate/',
                19 => 'File size is bigger then MAX_FILE_SIZE',
                20 => 'Upload file error',
                21 => 'Upload file error',
                22 => 'Upload file error',
                23 => 'File haven\'t been uploaded',
                24 => 'Can\'t move uploaded file',
                25 => 'Can\'t open uploaded file',
                26 => 'Can\'t edit uploaded file',
                27 => 'Can\'t create FTP account for video files',
                28 => 'Can\'t create FTP account for data files'
                , 29 => 'Can\'t insert inventory data'
                , 30 => 'Can\'t insert sites data'
                , 31 => 'Can\'t insert landingsites data'
                , 32 => 'Can\'t delete old landingsite'
                , 100 => 'Can\'t connect to api database.'
                , 110 => 'Can\'t select api database.'
                , 111 => 'Can\'t add base site database parameters to API.'
                , 112 => 'Can\'t open directory: /home/mi_user/public_html/cp/sdlanding/'
                , 113 => 'Can\'t open landing/config.php file.'
                , 114 => 'Can\'t write to landing/config.php file'
                , 115 => 'Can\'t create email domain foward'
                , 116 => 'Can\'t open geostyle.css file.'
                , 116 => 'Can\'t write to geostyle.css file.'
                );

require_once("./create_site_class.php");
$site = new siteCreate($subdomain, $userName, $userPassword, $dbName, $prefix, $cpConfig);

//Generate subdomain name
$salt = 1;
$sdm = $subdomain;

while(is_dir($_SERVER['DOCUMENT_ROOT'].'/'.$subdomain))
{
    $subdomain = $sdm.$salt;
    $salt ++;
}

//Generate database name
if( ! $db = mysql_connect($db_host, $cpConfig['mi_login'], $cpConfig['db_password']))
{
	echo "mysql_connect(".$db_host.", ". $cpConfig['mi_login'].", ". $cpConfig['db_password']."))";
    echo "<br />Can't connect to database. ";
    die( "MySql errno: ".mysql_errno().". MySql error: ".mysql_error() );
}

$result = mysql_query("SHOW databases");
if($result)
{
	$databases = Array();
	while($row = mysql_fetch_array($result))
    {
    	$databases[$row['Database']] = 1;
    }
}
else
{
	echo "Can't execut query. MySql errno: ".mysql_errno().". MySql error: ".mysql_error();
    exit();
}

$salt = 1;
$wholeDbName = $prefix.$dbName;

while($databases[$wholeDbName])
{
	$dbName = $str.$salt;
    $wholeDbName = $prefix.$dbName;
    $salt ++;
}
mysql_close($db);
//Generate database name

//Generate dbUserName
$res = 0;
$wholeUserName = $prefix.$userName;
$site->show_db_users($wholeUserName, $res);
$i = 1;

while($res && $i < 3)
{
	$userName = $str1.$i;
    $wholeUserName = $prefix.$userName;
	$site->show_db_users($wholeUserName, $res);
    $i ++;
}
//Generate dbUserName

if($debug) echo "<h1>$subdomain<br />$userName<br />$userPassword<br />dbName:".$dbName."<br /></h1>";

$site->subdomain = $subdomain;
$site->userName = $userName;
$site->userPassword = $userPassword;
$site->dbName = $dbName;

$subdomainPath					= $cpConfig['webdirectory']
									. $subdomain;

if( ! $error)
{
	if(is_dir($subdomainPath))
	{
		$error = 15;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
	}
}

// copy auction files, do before create_subdomain to help overcome fileperms
if( ! $error)
{
	if( ! is_dir("sdtemplate/") )
    {
    	$error = 18;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
    }

    if( ! $error)
    {
		$exec					= 'cp -r sdtemplate ' . $subdomainPath;
		shell_exec( $exec );
    	if($debug) echo "Directory $subdomainPath has been created successfully.<br />";
    }
}

// copy landing domains
$landingPath					= $subdomainPath . '/landing';

if( ! $error)
{
	if( ! is_dir("sdlanding/") )
    {
    	$error = 112;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
    }

    if( ! $error)
    {
		$exec					= 'cp -r sdlanding ' . $landingPath;
		shell_exec( $exec );
    	if($debug) echo "Directory $landingPath has been created successfully.<br />";
    }
}

if( ! $skipCpanel && ! $error)
{
    if( ! $site->create_subdomain())
    {
		$skipCpanel			= true;
        $error					= 1;
    }
    if($debug) echo "Subdomain $subdomain has been created successfully.<br />";
}
else
{
	$skipCpanel				= true;
}

if( ! $skipCpanel && ! $error)
{
    if( ! $site->create_db())
    {
        $error = 2;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
    }
    if($debug) echo "DB $dbName has been created successfully.<br />";
}

if( ! $skipCpanel && ! $error)
{
    if( ! $site->create_user())
    {
        $error = 3;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
    }
    if($debug) echo "User $userName:$userPassword has been created successfully.<br />";
}

if( ! $skipCpanel && ! $error)
{
    if( ! $site->add_user_to_db())
    {
        $error = 4;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
    }
    if($debug) echo "User has been added to Db successfully.<br />";
}


// MLC 20080208 auction site config
$configFileSource				= $subdomainPath . '/config.php';

if( ! $error)
{
	$mysql_host					= $db_host;
	$mysql_username				= $prefix.$userName;
	$mysql_password				= $userPassword;
	$mysql_dbname				= $prefix.$dbName;

	$api_database				= $cpConfig[ 'api_db_name' ];
	$api_db_username			= $cpConfig[ 'api_db_username' ];
	$api_db_host				= $cpConfig[ 'api_db_host' ];
	$api_db_password			= $cpConfig[ 'api_db_password' ];

	$fileRes					= fopen( $configFileSource, 'r' );
    $configFile					= fread( $fileRes
									, filesize( $configFileSource)
								);
	fclose ( $fileRes );

	$fileRes					= fopen( $configFileSource, 'w+' );

    if( ! $configFile)
    {
        $error = 7;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
    }
    else
    {
		// main db
        $keys = Array("@@USERNAME@@", "@@PASSWORD@@", "@@DATABASE@@");
        $values = Array($mysql_username, $mysql_password, $mysql_dbname);
        $configFile = str_replace($keys, $values, $configFile);

		// api db
        $keys = Array("@@API_USERNAME@@"
			, "@@API_PASSWORD@@"
			, "@@API_HOST@@"
			, "@@API_DATABASE@@");
        $values = Array($api_db_username
			, $api_db_password
			, $api_db_host
			, $api_database);
        $configFile = str_replace($keys, $values, $configFile);

		// cb_cogs helper
        $keys = Array(
			"@@MI_USER@@"
			, "@@cpPriceDelta@@"
			, '@@SHOW_VIDEO_DEFAULT@@'
			, '@@VIDEO_ACCOUNT@@'
			, '@@VIDEO_SITE@@'
			, '@@VIDEO_TOKEN@@'
		);
        $values = Array(
			$cpConfig['mi_login']
			, $cpPriceDelta
			, $show_video_default
			, $video_account
			, $video_site
			, $video_token
		);
        $configFile = str_replace($keys, $values, $configFile);

        if( ! fwrite( $fileRes, $configFile ) )
        {
        	$error = 9;
			cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
        }

		fclose( $fileRes );
    	
		require_once( $configFileSource );
    }
}

// Set header image in CSS
$cssFileSource				= $subdomainPath . '/geostyle.css';

if( ! $error)
{
	$fileRes					= fopen( $cssFileSource, 'r' );
    $cssFile					= fread( $fileRes
									, filesize( $cssFileSource)
								);
	fclose ( $fileRes );

	$fileRes					= fopen( $cssFileSource, 'w+' );

    if( ! $cssFile)
    {
        $error = 116;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
    }

    else
    {
		$image = getimagesize('sdtemplate/user_images/'.$headerImage);
		$headerHeight = $image[1];
        $keys = Array(
			"@@MI_HEADER_LOGO@@"
			, '@@MI_HEADER_HEIGHT@@'
			, '@@MI_BKG_COLOR@@'
		);
        $values = Array(
			$headerImage
			, $headerHeight
			, $bkgColor
		);
        $cssFile = str_replace($keys, $values, $cssFile);

        if( ! fwrite( $fileRes, $cssFile ) )
        {
        	$error = 117;
			cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
        }

		fclose( $fileRes );
    }
}

$subdomainEmail					= $subdomain . $cpEmailBase;
$configFileSource				= $landingPath . '/config.php';
$auctionUrl						= "http://$subdomain{$cpConfig['postfix']}/";

if( ! $error)
{
	$fileRes					= fopen( $configFileSource, 'r' );
    $configFile					= fread( $fileRes
									, filesize( $configFileSource)
								);
	fclose ( $fileRes );

	$fileRes					= fopen( $configFileSource, 'w+' );

    if( ! $configFile)
    {
        $error = 113;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
    }
    else
    {
		// main db
        $keys = Array('@@MI_SALE_DOMAIN@@'
			, '@@DEALER_NAME@@'
			, '@@OWNER_NAME@@'
			, '@@MI_DEALER_STREET@@'
			, '@@MI_DEALER_CITY@@'
			, '@@MI_DEALER_STATE@@'
			, '@@MI_DEALER_ZIP@@'
			, '@@MI_DEALER_INV_COUNT@@'
			, '@@MI_SALE_EMAIL@@'
			, '@@MI_SALE_DATES@@'
			, '@@MI_AUCTION_URL@@'
			, '@@MI_USER@@'
			, '@@MI_SALE_TEXT@@'
		);
        $values = Array($landing
			, $dealerName
			, $cpSiteName
			, $address
			, $city
			, $state
			, $zip
			, number_format( $invcount )
			, $subdomainEmail
			, $dates
			, $auctionUrl
			, $cpConfig['mi_login']
			, $saleText
		);
        $configFile = str_replace($keys, $values, $configFile);

		// api db
        $keys = Array("@@API_USERNAME@@"
			, "@@API_PASSWORD@@"
			, "@@API_HOST@@"
			, "@@API_DATABASE@@");
        $values = Array($api_db_username
			, $api_db_password
			, $db_host_remote
			, $api_database);
        $configFile = str_replace($keys, $values, $configFile);

        if( ! fwrite( $fileRes, $configFile ) )
        {
        	$error = 114;
			cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
        }

		fclose( $fileRes );
    }
}

//Dumping data from .sql archive and tables
if( ! $error)
{
    $execStr = "cat < sdtemplate/sd_all_dump.sql | mysql --user=".$mysql_username." --host=localhost -D ".$mysql_dbname." --password=".$mysql_password;
    if($debug) echo "<br /><br />Mysql Dump is ".htmlspecialchars($execStr);
    shell_exec($execStr);
    $execStr = "mysqldump --add-drop-table --add-locks ".$api_database." geodesic_userdata geodesic_user_groups_price_plans geodesic_logins --user=".$api_db_username." --host=".$api_db_host." --password=".$api_db_password." | mysql --user=".$mysql_username." --host=".$mysql_host." -D ".$mysql_dbname." --password=".$mysql_password;
    shell_exec($execStr);
    if($debug) echo "<br /><br />Mysqldump string is ".htmlspecialchars($execStr);
    if($debug) echo "<br />";
}

if( ! $error)
{
    $db = mysql_connect($mysql_host, $mysql_username, $mysql_password);
    if( ! $db)
    {
        $error = 10;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
    }
    if( !mysql_select_db($mysql_dbname, $db))
    {
        $error = 11;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
    }
}

if( ! $error && $db)
{
	$postfix					= $cpConfig['postfix'];

	// let the site work when subdomain fails, mainly for testing
	$siteUrl					= ( ! $skipCpanel )
									? $auctionUrl
									: 'http://' . $cpConfig['mi_host'] . '/'
										.  $subdomain . '/';

    $query						= "
		UPDATE geodesic_templates
		SET
			template_code = replace(template_code, 'http://@@SUBDOMAIN@@', '{$siteUrl}')
			, template_code = replace(template_code, '@@COMPANY_NAME@@', '$cpSiteName')
			, template_code = replace(template_code, '@@YEAR@@', '$year')
	";
    $result = mysql_query($query);
    if( ! $result)
    {
        $error = 12;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
		cbDebug( 'query', $query );	
    	if($debug) echo "<br />$query";
    }
}

if( ! $error && $db)
{
    $query						= <<<EOD
		UPDATE geodesic_pages
		SET
			module_logged_in_html = replace(module_logged_in_html, "@@DEALER_NAME@@", "$dealerName")
    		, module_logged_out_html = replace( module_logged_out_html, "@@DEALER_NAME@@", "$dealerName")
    		, extra_page_text = replace( extra_page_text, "@@DEALER_NAME@@", "$dealerName")
			, module_logged_in_html = replace(module_logged_in_html, "@@AUCTION_URL@@", "$auctionUrl")
    		, module_logged_out_html = replace( module_logged_out_html, "@@AUCTION_URL@@", "$auctionUrl")
    		, extra_page_text = replace( extra_page_text, "@@AUCTION_URL@@", "$auctionUrl")
			, module_logged_in_html = replace(module_logged_in_html, "@@COMPANY_PHONE@@", "$cpPhone")
    		, module_logged_out_html = replace( module_logged_out_html, "@@COMPANY_PHONE@@", "$cpPhone")
    		, extra_page_text = replace( extra_page_text, "@@COMPANY_PHONE@@", "$cpPhone")
			, module_logged_in_html = replace(module_logged_in_html, "@@POC_PHONE@@", "$pocPhone")
    		, module_logged_out_html = replace( module_logged_out_html, "@@POC_PHONE@@", "$pocPhone")
    		, extra_page_text = replace( extra_page_text, "@@POC_PHONE@@", "$pocPhone")
			, module_logged_in_html = replace(module_logged_in_html, "@@COMPANY_NAME@@", "$cpSiteName")
    		, module_logged_out_html = replace( module_logged_out_html, "@@COMPANY_NAME@@", "$cpSiteName")
    		, extra_page_text = replace( extra_page_text, "@@COMPANY_NAME@@", "$cpSiteName")
			, module_logged_in_html = replace(module_logged_in_html, "@@COMPANY_URL@@", "$cpSiteName")
    		, module_logged_out_html = replace( module_logged_out_html, "@@COMPANY_URL@@", "$cpSiteName")
    		, extra_page_text = replace( extra_page_text, "@@COMPANY_URL@@", "$cpSiteName")
			, module_logged_in_html = replace(module_logged_in_html, "@@POC_EMAIL@@", "$subdomainEmail")
    		, module_logged_out_html = replace( module_logged_out_html, "@@POC_EMAIL@@", "$subdomainEmail")
    		, extra_page_text = replace( extra_page_text, "@@POC_EMAIL@@", "$subdomainEmail")
			, module_logged_in_html = replace(module_logged_in_html, "@@COMPANY_ADDRESS@@", "$cpAddress")
    		, module_logged_out_html = replace( module_logged_out_html, "@@COMPANY_ADDRESS@@", "$cpAddress")
    		, extra_page_text = replace( extra_page_text, "@@COMPANY_ADDRESS@@", "$cpAddress")
			, module_logged_in_html = replace(module_logged_in_html, "@@COMPANY_CITY@@", "$cpCity")
    		, module_logged_out_html = replace( module_logged_out_html, "@@COMPANY_CITY@@", "$cpCity")
    		, extra_page_text = replace( extra_page_text, "@@COMPANY_CITY@@", "$cpCity")
			, module_logged_in_html = replace(module_logged_in_html, "@@COMPANY_STATE@@", "$cpState")
    		, module_logged_out_html = replace( module_logged_out_html, "@@COMPANY_STATE@@", "$cpState")
    		, extra_page_text = replace( extra_page_text, "@@COMPANY_STATE@@", "$cpState")
			, module_logged_in_html = replace(module_logged_in_html, "@@COMPANY_ZIP@@", "$cpZip")
    		, module_logged_out_html = replace( module_logged_out_html, "@@COMPANY_ZIP@@", "$cpZip")
    		, extra_page_text = replace( extra_page_text, "@@COMPANY_ZIP@@", "$cpZip")
			, module_logged_in_html = replace(module_logged_in_html, "@@YEAR@@", "$year")
    		, module_logged_out_html = replace( module_logged_out_html, "@@YEAR@@", "$year")
    		, extra_page_text = replace( extra_page_text, "@@YEAR@@", "$year")
EOD;
    $result = mysql_query($query);
    if( ! $result)
    {
        $error = 12;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );
		cbDebug( 'query', $query );	
    	if($debug) echo "<br />$query";
    }
}

if( ! $error && $db)
{
    $query						= "
		UPDATE geodesic_classifieds_configuration
		SET
		classifieds_url = '{$auctionUrl}'
		, affiliate_url = '{$auctionUrl}aff.php'
		, registration_url = '{$auctionUrl}register.php'
		, storefront_url = '{$auctionUrl}stores.php'
		, license = '{$cpConfig['license']}'
		, registration_admin_email = '{$subdomainEmail}'
		, site_email = '{$subdomainEmail}'
		, site_name = '{$cpConfig['siteName']}'
		, display_optional_field_8 = 0
		, display_optional_field_9 = 0
		, display_optional_field_10 = 0
		, display_optional_field_12 = 0
		, display_optional_field_14 = 0
		, display_optional_field_15 = 0
		, display_optional_field_17 = 0
		, use_optional_field_8 = 0
		, use_optional_field_9 = 0
		, use_optional_field_10 = 0
		, use_optional_field_12 = 0
		, use_optional_field_14 = 0
		, use_optional_field_15 = 0
		, use_optional_field_17 = 0
";

    $result = mysql_query($query);
    if( ! $result)
    {
        $error = 12;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    	if($debug) echo "<br />$query";
    }
}

if( ! $error && $db)
{
    $query						= "
		UPDATE geodesic_registration_configuration
		SET
		/* turn off auction pass code */
		use_registration_optional_1_field = 0
		, require_registration_optional_1_field = 0
";

    $result = mysql_query($query);
    if( ! $result)
    {
        $error = 12;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    	if($debug) echo "<br />$query";
    }
}

if( ! $error && $db)
{
    $query						= <<<EOD
		UPDATE `geodesic_pages_messages_languages`
		SET
    		text = replace( text, "@@DEALER_NAME@@", "$dealerName" )
    		, text = replace(text, "@@COMPANY_NAME@@", "$cpSiteName")
    		, text = replace(text, "@@COMPANY_URL@@", "{$cpConfig["mi_host"]}")
			, text = replace(text, "@@AUCTION_URL@@", "$auctionUrl")
			, text = replace(text, "@@MI_DEALER_CITY@@", "$city")
			, text = replace(text, "@@MI_DEALER_STATE@@", "$state")
			, text = replace(text, "@@MI_SALE_DATES@@", "$dates")
EOD;

    $result = mysql_query($query);
    if( ! $result)
    {
        $error = 12;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    	if($debug) echo "<br />$query";
    }
}

if( ! $error && $db)
{
    $query						= "
		UPDATE `geodesic_classifieds_ad_configuration`
		SET
    		title_module_text = '".urlencode("$name - ")."'
			, image_upload_path = '" 
				. $subdomainPath
				. "/user_images/'"
		;

    $result = mysql_query($query);
    if( ! $result)
    {
        $error = 12;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    	if($debug) echo "<br />$query";
    }
}

if( ! $error && $db)
{
    $query = "UPDATE `geodesic_pages_messages_languages` SET text ='".urlencode(stripslashes("Congratulations!!!  Nice Bidding - You are the high bidder in this auction.

Below is a link to view the auction after it has closed. Please contact the seller using the information below to exchange payment information.

National and Local financing is available and will take only minutes for approval.

Keep this email for your records and the link back to the auction.

$cpSiteName
$auctionUrl

$pocFirst $pocLast
Onsite Re-Marketing Manager
$pocPhone
$subdomainEmail
")).
             "' WHERE text_id = 102767";

    $result = mysql_query($query);
    if( ! $result)
    {
        $error = 12;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    	if($debug) echo "<br />$query";
    }
}

if( ! $error && $db)
{
    $query = "UPDATE `geodesic_pages_messages_languages` SET text ='".urlencode(stripslashes("Congratulations! You have won this auction with your buy now bid.
The seller has also been notified of your bid. Please contact the seller below to complete this transaction.

$cpSiteName
$auctionUrl

$pocFirst $pocLast
Onsite Re-Marketing Manager
$pocPhone
$subdomainEmail
")).
"' WHERE text_id = 102493";

    $result = mysql_query($query);
    if( ! $result)
    {
        $error = 12;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    	if($debug) echo "<br />$query";
    }
}

if( ! $error && $db)
{
    $query = "UPDATE `geodesic_pages_messages_languages` SET text ='".urlencode(stripslashes("Your registration is successful. Now you will have Priority Access to Bank Vehicles when you arrive on site at $dealerName.

Please ask for me, $pocFirst $pocLast. I will find the car you are looking for and make sure you are thousands under wholesale. Please note that more vehicles are arriving daily at the dealership.

Visit $auctionUrl to view current inventory.

$pocFirst $pocLast
Onsite Re-Marketing Manager
$pocPhone
$subdomainEmail
")).
             "' WHERE text_id = 677";

    $result = mysql_query($query);
    if( ! $result)
    {
        $error = 12;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    	if($debug) echo "<br />$query";
    }
}

if( ! $error && $db)
{
    $query = "UPDATE `geodesic_pages_messages_languages` SET text ='".urlencode(stripslashes("<div align=\"center\">
    <table align=center border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"77%\" id=\"table1\">
        <tr>
            <td width=\"27%\">&nbsp;</td>
            <td width=\"69%\">&nbsp;</td>
            <td width=\"4%\">&nbsp;</td>
        </tr>
        <tr>
            <td width=\"27%\">&nbsp;</td>
            <td width=\"69%\">
            <p align=\"center\">
            <font face=\"Arial\" style=\"font-size: 9pt; font-weight: 700\">The
            Authorized Agent Hosting Event is: $dealerName, </font></td>
            <td width=\"4%\">&nbsp;</td>
        </tr>
        <tr>
            <td width=\"27%\">
            <p align=\"right\">
            </td>
            <td width=\"69%\">
            <p align=\"center\"><font face=\"Arial\">Your registration is now
            complete. <a href=\"index.php\">CLICK HERE</a> to start browsing..</font></td>
            <td width=\"4%\">&nbsp;</td>
        </tr>
        <tr>
            <td colspan=\"3\">&nbsp;</td>
        </tr>
    </table>
    <font face=\"Arial\"><br />
    <br />
    .</font></div>
    </body>
    ")).
    "' WHERE text_id = 325";

    $result = mysql_query($query);
    if( ! $result)
    {
        $error = 12;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    	if($debug) echo "<br />$query";
    }
}

// MLC 20080109 don't rerun base API setup
if( false && ! $skipCpanel && ! $error)
{
    if( ! $site->add_subdomain_to_api( false ))
    {
        $error = 111;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
    }
    if($debug) echo "Site {$cpConfig['baseSite']} has been added to API successfully.<br />";
}

if( ! $skipCpanel && ! $error)
{
    if( ! $site->add_subdomain_to_api())
    {
        $error = 13;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
    }
    if($debug) echo "Site $auctionUrl has been added to API successfully.<br />";
}

if( ! $skipCpanel && ! $error && $db)
{
	$strName = $name;
    $strName = preg_replace("#[\W]#", "", $strName);
    if(strlen($strName) > 12) $strName = substr($strName, 0, 12);

    $i = 0;
    $gdUserName = $strName;
    $query = "SELECT count( id ) AS count FROM geodesic_userdata WHERE username = '".$gdUserName."'";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result);
    $count = $row['count'];
    if($debug) echo $i.": ".$query."<br />";
    if($debug) echo "<br />Count: ".$count;
    if($debug) echo "<br />User: ".$strName;
    if($debug) echo "<br />dsUserName: ".$gdUserName;

	while($count)
    {
    	if(strlen($strName) > 10) $strName = substr($strName, 0, 10);
    	$gdUserName = $strName.$i;
    	$query = "SELECT count( id ) AS count FROM geodesic_userdata WHERE username = '".$gdUserName."'";
    	$result = mysql_query($query);
    	$row = mysql_fetch_array($result);
    	$count = $row['count'];
		if($debug) echo $i.": ".$query."<br />";
		if($debug) echo "<br />Count: ".$count;
		if($debug) echo "<br />User: ".$strName;
		if($debug) echo "<br />dsUserName: ".$gdUserName;
    	$i++;
    }

    if($debug) echo "<hr>Geodesic User Name is ".$gdUserName."<hr>";

    $c['username'] = $gdUserName;
	$c['company_name'] = $name;
    $c['country'] = "United States";
    $c['firstname'] = $pocFirst;
    $c['lastname'] = $pocLast;
    $c['address'] = $address;
    $c['city'] = $city;
    $c['state'] = $state;
    $c['zip'] = $zip;
    $c['phone'] = $pocPhone;
    $c['phoneext'] = '';
    $c['phone_2'] = $pocPhone;
    $c['phoneext'] = '';
    $c['email'] = $subdomainEmail;
    $c['email_verifier'] = $subdomainEmail;
    $c['optional_field_1'] = '';
    $c['optional_field_8'] = 'Yes';
    $c['password'] = $pocpassword;
    $c['password_confirm'] = $pocpassword;
    $c['agreement'] = 'yes';
    $c['group_id'] = '2';

    if( ! $site->create_geoclass_user($c))
    {
        $error = 14;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
    }
}

if( ! $error && $db)
{
    $query = "UPDATE `geodesic_userdata` SET email='".$pocemail."'
	WHERE username = '".$c['username']."'";

    $result = mysql_query($query);
    if( ! $result)
    {
        $error = 17;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    }
}

if( ! $error && $db)
{
    $query = "SELECT id FROM `geodesic_userdata`
	WHERE username = '".$c['username']."'";

    $result = mysql_query($query);
    if( ! $result)
    {
        $error = 17;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    }
    $row = mysql_fetch_array($result);
    $user_id = $row['id'];
}

if( ! $error && $db)
{
    $query = "UPDATE `geodesic_user_groups_price_plans` SET
    group_id='2'
    WHERE id = $user_id
    LIMIT 1";

    $result = mysql_query($query);
    if( ! $result)
    {
        $error = 17;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    }
}

if( ! $error)
{
	$api_db = mysql_connect($api_db_host, $api_db_username, $api_db_password);
	if( ! $api_db)
	{
		$error = 100;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
	}
	if( !mysql_select_db($api_database, $api_db))
	{
		$error = 110;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
	}
}

if( ! $error && $api_db)
{
    $query = "INSERT INTO `geodesic_inventory` (id, pocfirstname, poclastname, pocphone, pocemail, date, subdomain) VALUES ('', '$pocFirst', '$pocLast', '$pocPhone', '$pocemail', NOW(), '$auctionUrl')";
    if($debug) echo "Sql query: $query<br/>";

    $result = mysql_query($query, $api_db);
    if( ! $result)
    {
        $error = 29;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    }
}

// sites
if( ! $error && $api_db)
{
	// MLC 20070815 todo add convert date
    $query = <<<EOD
	INSERT INTO `sites` (
		subdomain
		, dealername
		, dealeraddress
		, dealercity
		, dealerstate
		, dealerpostcode
		, dealerwebsite
		, emailsendregistrationto
		, inventorypocname
		, inventorypocemail
		, inventorypocphone
		, datecreate
		, live
		, datestart
		, dateend
		, dealerusername
		, dealerpassword
		, landingdomain
	)
    VALUES (
		"$subdomain"
		, "$name"
		, "$address"
		, "$city"
		, "$state"
		, "$zip"
		, "$sitename"
		, "$email"
		, "$pocFirst+$pocLast"
		, "$pocemail"
		, "$pocPhone"
		, NOW()
		, 1
		, "$start"
		, "$end"
		, "{$c['username']}"
		, "{$c['password']}"
		, "$landing"
	)
EOD;
    if($debug) echo "Sql query: $query<br/>";

    $result = mysql_query($query, $api_db);
    if( ! $result)
    {
        $error = 30;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    }
}

// landingsites
if( ! $error && $api_db)
{
	// MLC 20071104 delete old landingsite
    $query = "UPDATE `landingsites`
		SET deleted = 1
		WHERE domain = '$landing'
	";
    if($debug) echo "Sql query: $query<br/>";

    $result = mysql_query($query, $api_db);
    if( ! $result)
    {
        $error = 32;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    }

    $query = "INSERT INTO `landingsites` (
		title
		, domain
		, datecreate
		, datemodify
		, host
		, live
		, deleted
	)
    VALUES (
		'$saleText'
		, '$landing'
		, UNIX_TIMESTAMP()
		, UNIX_TIMESTAMP()
		, '$landing'
		, 1
		, 0
	)";
    if($debug) echo "Sql query: $query<br/>";

    $result = mysql_query($query, $api_db);
    if( ! $result)
    {
        $error = 31;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'query', $query );	
    }
}

// Create landing archive for Ladair
if( ! $error )
{
	$exec					= 'cd ' . $subdomainPath . '; '
								. 'zip -r landing.zip landing/; ';
	shell_exec( $exec );
}

//Create video and datafeed ftp accounts
if( false && ! $skipCpanel && ! $error)
{
    if( ! $site->create_ftp_account('uploads', $pocpassword))
    {
        $error = 27;
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
    }
    if($debug) echo "FTP account for video and data files has been created successfully.<br />";
}

// Create email forwards
if ( ! $error )
{
	// ensure cp admin is in the loop
	if ( ! stristr( $email, $cpEmail ) )
	{
		$email					.= ',' . $cpEmail;
	}

	$emailBase					= str_replace( '@', '', $cpEmailBase );

	$emails						= preg_replace( '/[;, ]/', ',', $email );
	$emails						= explode( ',', $emails );
	$emails						= array_unique( $emails );

	foreach( $emails as $key => $value )
	{
		if( ! $site->create_email_forward( $subdomain, $emailBase, $value ) )
		{
			$error = 115;
			cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
			break;
		}

		if ( $debug )
		{
			echo "$subdomain @ $emailBase email forward to $value done<br />";
		}
	}
}

$str = '';

$path = "sdtemplate/templates/templateAdmin.htm";
$template = implode("", file("$path"));

$separator						= "\n";

// email headers
$headers						= "From: $subdomainEmail\r\n"
									. "Cc: $adminEmail\r\n"
									. "Bcc: $bccEmail\r\n";

if($error)
{
	cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
	cbDebug( 'error', $error );	
	cbDebug( 'site->answers', $site->answers );	
	cbDebug( 'site->errors', $site->errors );	

	// MLC true for cleanup
	if ( ! $skipCpanel && ! $skipCleanup )
	{
		$site->del_api($api_db);
		$site->del_api_users($api_db);
		$site->del_db();
		$site->del_user();
		$site->del_subdomain();
		$site->del_subdomain_folder();
		$site->delete_email_forward($subdomain, $cpEmailBase, $cpEmail);
	}
	else
	{
		cbDebug( 'File ' . __FILE__, 'Line ' . __LINE__ );	
		cbDebug( 'exiting', 'error clean up' );	
		cbDebug( 'needed clean up', '
		$site->del_api($api_db);
		$site->del_api_users($api_db);
		$site->del_db();
		$site->del_user();
		$site->del_subdomain();
		$site->del_subdomain_folder();
		$site->delete_email_forward($subdomain, $cpEmailBase, $cpEmail);
		' );	
		exit;
	}

    $str .= "<hr color=#ff0000>".$errors[$error];
    foreach($site->errors as $key => $value)
    {
        $str .= "<hr size=1 color=#ff9900>$key => $value";
        $message .= "$key => $value.".$separator.$separator;
    }

    $str .= "<hr size=1 color=#ff9900>You're site will be created soon message";

    if(empty($email)) $email = $cpEmail;

    $mailto = $email;
    $subject = "$cpSiteName: Failed submissions. Marketing site request for ".$name;

    $message .= "Error: ".$errors[$error].".".$separator.$separator.$separator;

    $message .= "User information:".$separator."===================================".$separator;
    $message .= "Name of dealership: ".$name.$separator;
    $message .= "Dealership address: ".$address.$separator;
    $message .= "City: ".$city.$separator;
    $message .= "State: ".$state.$separator;
    $message .= "Zip code: ".$zip.$separator;
    $message .= "Dealership website: ".$sitename.$separator;
    $message .= "$cpSiteName URL to use: ".$miurl.$separator;
    $message .= "Landing page: ".$landing.$separator;
    $message .= "Email to send registrations to: ".$email.$separator;
    $message .= "Date to start classifieds: ".$start.$separator;
    $message .= "Date to convert to auctions: ".$convert.$separator;
    $message .= "Date to end auctions: ".$end.$separator;
    $message .= "Dealer Account POC name: ".$pocFirst." ".$pocLast.$separator;
    $message .= "Dealer Account POC email: ".$pocemail.$separator;
    $message .= "Dealer Account POC phone: ".$pocPhone.$separator.$separator;

    $message .= "MySql User Name: ".$mysql_username.$separator;
    $message .= "MySql User Password: ".$userPassword.$separator;
    $message .= "MySql DB Name: ".$mysql_dbname.$separator.$separator;

    $message .= "Special instructions: ".$special.$separator.$separator;
    $message .= "Header: ".$headerImage.$separator;

    mail($mailto, $subject, $message, $headers);
}

else
{
	$userLink = $auctionUrl;
    $adminLink = $auctionUrl."admin/";
    $str =  "
		Landing files archive for <a href='".$landing."' target='_blank'>".$landing."</a> are at <a href='{$userLink}landing.zip' target='_blank'>{$userLink}landing.zip</a>
        <hr size=1 color=#ff9900 />
        Site <a href=".$userLink." target=_blank>$userLink</a> has been created successfully.
		<hr size=1 color=#ff9900 />
	    Dealer login <a href='".$userLink."index.php?a=10&b[username]=".$c['username']."&b[password]=".$c['password']."' target=_blank>$userLink</a>
        <br /><b>Dealer User Name:<b> ".$c['username']."
        <br /><b>Dealer User Password:<b> ".$c['password']."
		<br /><br />Admin section: <a href='".$adminLink."index.php?b[username]=".$cpConfig['gcaadmin']."&b[password]=".$cpConfig['gcaadminpw']."' target='_blank'>{$userLink}admin/</a>
        <br /><b>Admin User Name:<b> ".$cpConfig['gcaadmin']."
        <br /><b>Admin User Password:<b> ".$cpConfig['gcaadminpw']."
		<hr size=1 color=#ff9900 />
        <br /><b>MySql User Name:<b> $mysql_username
        <br /><b>MySql User Password:<b> $userPassword
        <br /><b>MySql DB Name:<b> $mysql_dbname
	";
    $i = 1;
    foreach($site->answers as $key => $value)
    {
        $str .= "<hr size=1 color=#ff9900 /><h1>$i </h1>$value";
        $i ++;
    }

    if(empty($email)) $email = $cpEmail;

    $separator = "\n";
    $mailto = $email;
    $subject = "$cpSiteName: Site Creating. Marketing site request for ".$name;

    $message = "Site $userLink has been created successfully.".$separator;
    $message .= "Dealer User Name: ".$c['username'].$separator;
    $message .= "Dealer User Password: ".$c['password'].$separator.$separator;

    $message .= "Admin section: $adminLink".$separator;
    $message .= "Admin User Name: miadmin".$separator;
    $message .= "Admin User Password: H0iB6k".$separator.$separator;

    $message .= "Landing files archive {$userLink}landing.zip".$separator.$separator;

if ( false ) {
    $message .= "FTP account for video and data files".$separator;
    $message .= "Host: {$cpConfig['mi_host']}".$separator;
    $message .= "Login: ".$subdomain."_u".$separator;
    $message .= "Password: ".$pocpassword.$separator.$separator;
}

    $message .= "User information:".$separator."===================================".$separator;
    $message .= "Name of dealership: ".$name.$separator;
    $message .= "Dealership address: ".$address.$separator;
    $message .= "City: ".$city.$separator;
    $message .= "State: ".$state.$separator;
    $message .= "Zip code: ".$zip.$separator;
    $message .= "Dealership website: ".$sitename.$separator;
    $message .= "$cpSiteName URL to use: ".$miurl.$separator;
    $message .= "Landing page: ".$landing.$separator;
    $message .= "Email to send registrations to: ".$email.$separator;
    $message .= "Date to start classifieds: ".$start.$separator;
    $message .= "Date to convert to auctions: ".$convert.$separator;
    $message .= "Date to end auctions: ".$end.$separator;
    $message .= "Dealer Account POC name: ".$pocFirst." ".$pocLast.$separator;
    $message .= "Dealer Account POC email: ".$pocemail.$separator;
    $message .= "Dealer Account POC phone: ".$pocPhone.$separator.$separator;

    $message .= "MySql User Name: ".$mysql_username.$separator;
    $message .= "MySql User Password: ".$userPassword.$separator;
    $message .= "MySql DB Name: ".$mysql_dbname.$separator.$separator;

    $message .= "Special instructions: ".$special.$separator.$separator;
    $message .= "Header: ".$headerImage.$separator;

    $mailto = $designerEmail;
    mail($mailto, $subject, $message, $headers);

	//Send mail to video people
    $mailto = $videoEmail;
    $subject = $cpSiteName . ': FTP account for data and video files has been
	created: '.$subdomain.'_u@' . $cpConfig['mi_host'];
    $message = "FTP account for video and data files".$separator;
    $message .= "Host: {$cpConfig['mi_host']}".$separator;
    $message .= "Login: ".$subdomain."_u".$separator;
    $message .= "Password: ".$pocpassword.$separator.$separator;

    // mail($mailto, $subject, $message, $headers);

    //Send mail header design request
    $nmailto = $designerEmail;
    $nsubject = $cpSiteName . ': Header Request for '.$userLink;
    $nmessage = "Please create a header for the $userLink} website. The dealers own website is at ".$sitename."\nAdmin URL: {$userLink}admin/\n\nU: miadmin\nP: H0iB6k";

    // mail($nmailto, $nsubject, $nmessage, $headers);
}

$template = str_replace("@IMG_TEMPLATE_PATH@", "http://www{$cpConfig['postfix']}/sdtemplate/templates/img", $template);
$template = str_replace("@SITE_TITLE@", "http://www.{$cpConfig['postfix']} - Inventory Site Created.", $template);
$template = str_replace("@TITLE@", "http://www{$cpConfig['postfix']} - Inventory Site Created.", $template);
$template = str_replace("@TEXT@", $str, $template);

echo $template;
?>
