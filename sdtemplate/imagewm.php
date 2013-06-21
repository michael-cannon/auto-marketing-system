<? //get_image.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
\**************************************************************************/
$wmlogo = $_SERVER["DOCUMENT_ROOT"].'/'.'images/wmlogo.gif';
$wmlogo1 = $_SERVER["DOCUMENT_ROOT"].'/'.'images/wmlogo1.gif';

error_reporting  (E_ERROR | E_WARNING | E_PARSE);
include("config.php");
include_once('products.php');
include("classes/adodb.inc.php");
include_once('classes/site_class.php');

$debug_popup = 0;

if ($_COOKIE["language_id"]) $language_id = $_COOKIE["language_id"];
else $language_id = 1;

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

if(!$product_configuration) $product_configuration = new product_configuration($db);

// Create an instance of the site class
$site = new Site($db, 0, 0, 0, $product_configuration);


$sql_query = "SELECT popup_image_template_id,popup_image_extra_width,popup_image_extra_height,
	maximum_full_image_height, maximum_full_image_width
    FROM geodesic_classifieds_ad_configuration";

$ad_configuration_result = $db->Execute($sql_query);
if ($debug_popup) echo $sql_query."<br>\n";
if (!$ad_configuration_result)
{
	echo $sql_query."<bR>\n";
    exit;
}
elseif ($ad_configuration_result->RecordCount() == 1)
{
	$ad_configuration = $ad_configuration_result->FetchRow();
}

$sql_query = "SELECT * FROM geodesic_classifieds_images_urls WHERE image_id = ".$_REQUEST["image"];

if ($debug_popup) echo $sql_query."<br>\n";
$image_result = $db->Execute($sql_query);

if (!$image_result)
{
	echo $sql_query."<bR>\n";
    exit;
}
elseif ($image_result->RecordCount() == 1)
{
	$show_image = $image_result->FetchRow();

	if (($show_image["original_image_width"] > $ad_configuration["maximum_full_image_width"]) ||
       ($show_image["original_image_height"] > $ad_configuration["maximum_full_image_height"]))
    {
    	if (($show_image["original_image_width"] > $ad_configuration["maximum_full_image_width"]) && ($show_image["original_image_height"] > $ad_configuration["maximum_full_image_height"]))
        {
        	$imageprop = ($ad_configuration["maximum_full_image_width"] * 100) / $show_image["original_image_width"];
            $imagevsize = ($show_image["original_image_height"] * $imageprop) / 100 ;
            $image_width = $ad_configuration["maximum_full_image_width"];
            $image_height = ceil($imagevsize);
            if ($image_height > $ad_configuration["maximum_full_image_height"])
            {
            	$imageprop = ($ad_configuration["maximum_full_image_height"] * 100) / $show_image["original_image_height"];
                $imagehsize = ($show_image["original_image_width"] * $imageprop) / 100 ;
                $image_height = $ad_configuration["maximum_full_image_height"];
                $image_width = ceil($imagehsize);
            }
        }
        elseif ($show_image["original_image_width"] > $ad_configuration["maximum_full_image_width"])
        {
        	$imageprop = ($ad_configuration["maximum_full_image_width"] * 100) / $show_image["original_image_width"];
            $imagevsize = ($show_image["original_image_height"] * $imageprop) / 100 ;
            $image_width = $ad_configuration["maximum_full_image_width"];
            $image_height = ceil($imagevsize);
        }
        elseif ($show_image["original_image_height"] > $ad_configuration["maximum_full_image_height"])
        {
        	$imageprop = ($ad_configuration["maximum_full_image_height"] * 100) / $show_image["original_image_height"];
            $imagehsize = ($show_image["original_image_width"] * $imageprop) / 100 ;
            $image_height = $ad_configuration["maximum_full_image_height"];
            $image_width = ceil($imagehsize);
        }
        else
        {
        	$image_width = $show_image["original_image_width"];
            $image_height = $show_image["original_image_height"];
        }
    }
    else
    {
    	$image_width = $show_image["original_image_width"];
        $image_height = $show_image["original_image_height"];
    }
    $debug_popup = 0;
    if ($debug_popup)
    {
    	echo $image_width." is image width after<br>\n";
        echo $image_height." is image height after<br>\n";
    }

	$imagePath = $_SERVER["DOCUMENT_ROOT"].'/'.$show_image['image_url'];
	$imageInfo = getImageSize($imagePath);
	$imageMimeType = $imageInfo['mime'];
	$width = $imageInfo[0];
	$height = $imageInfo[1];

	if($imageMimeType == 'image/jpeg' || $imageMimeType == 'image/jpg')
	{
    	$im = imageCreateFromJpeg($imagePath);
	}
	elseif($imageMimeType == 'image/gif')
	{
    	$im = imageCreateFromGif($imagePath);
	}
	elseif($imageMimeType == 'image/png')
	{
    	$im = imageCreateFromPng($imagePath);
	}

	$img = imageCreateFromGif($wmlogo);
	$logoInfo = getImageSize($wmlogo);
	$logoWidth = $logoInfo[0];
	$logoHeight = $logoInfo[1];

    //$img1 = imageCreateFromGif($wmlogo1);
    //$logoInfo1 = getImageSize($wmlogo1);
    //$logoWidth1 = $logoInfo1[0];
    //$logoHeight1 = $logoInfo1[1];

	//imageCopyMerge($im, $img, $width-$logoWidth-100, $height-$logoHeight-100, 0, 0, $logoWidth, $logoHeight, 50);
    $dst = imageCreateTrueColor($image_width, $image_height);
    $white = imageColorAllocate($dst, 256,256,256);
    imageFill($dst, 0,0, $white);
    imageCopyResized($dst, $im, 0,0,0,0, $image_width, $image_height, $width, $height);
    imageCopyMerge($dst, $img, $image_width-$logoWidth-5, $image_height-$logoHeight-5, 0, 0, $logoWidth, $logoHeight, 70);
    //imageCopyMerge($dst, $img1, 40, $image_height-$logoHeight1-40, 0, 0, $logoWidth1, $logoHeight1, 100);

	$contentType = 'Content-Type: ' . $imageMimeType;

    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("$contentType");
	if($imageMimeType == 'image/jpeg' || $imageMimeType == 'image/jpg')
	{
    	imageJpeg($dst);
	}
	elseif($imageMimeType == 'image/gif')
	{
    	imageGif($dst);
	}
	elseif($imageMimeType == 'image/png')
	{
    	imagePng($dst);
	}
}
?>