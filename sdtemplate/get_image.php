<? //get_image.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

error_reporting  (E_ERROR | E_WARNING | E_PARSE);
include("config.php");
include_once('products.php');
include("classes/adodb.inc.php");
include_once('classes/site_class.php');

$debug_popup = 0;

if ($_COOKIE["language_id"])
	$language_id = $_COOKIE["language_id"];
else
	$language_id = 1;

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

if(!$product_configuration)
	$product_configuration = new product_configuration($db);

// Create an instance of the site class
$site = new Site($db, 0, 0, 0, $product_configuration);

if (($_REQUEST["image"]) and (is_numeric($_REQUEST["image"])))
{
	if ($size == 1)
		$sql_query = "SELECT thumb_file FROM geodesic_classifieds_images WHERE image_id = ".$_REQUEST["image"];
	else
		$sql_query = "SELECT filetype,image_file FROM geodesic_classifieds_images WHERE image_id = ".$_REQUEST["image"];
	$result = $db->Execute($sql_query);
	if (!$result)
	{
		//echo 'Error<br>';
		exit;
	}
	elseif ($result->RecordCount() == 1)
	{
		$show = $result->FetchRow();
		if ($size == 1)
		{
			header("Content-Type: image/pjpeg");
			echo $show["thumb_file"];
			exit;
		}
		else
		{
			header("Content-Type: ".$show["filetype"]);
			echo $show["image_file"];
			exit;
		}
	}
}
elseif (($_REQUEST["popupimage"]) && (is_numeric($_REQUEST["popupimage"])))
{
	$sql_query = "SELECT popup_image_template_id,popup_image_extra_width,popup_image_extra_height,
		maximum_full_image_height, maximum_full_image_width
		FROM geodesic_classifieds_ad_configuration";
	$ad_configuration_result = $db->Execute($sql_query);
	if ($debug_popup)
		echo $sql_query."<br>\n";
	if (!$ad_configuration_result)
	{
		echo $sql_query."<bR>\n";
		exit;
	}
	elseif ($ad_configuration_result->RecordCount() == 1)
	{
		$ad_configuration = $ad_configuration_result->FetchRow();
		$sql_query = "SELECT * FROM geodesic_classifieds_images_urls WHERE image_id = ".$_REQUEST["popupimage"];
		if ($debug_popup)
			echo $sql_query."<br>\n";
		$image_result = $db->Execute($sql_query);
		if (!$image_result)
		{
			echo $sql_query."<bR>\n";
			exit;
		}
		elseif ($image_result->RecordCount() == 1)
		{
			$show_image = $image_result->FetchRow();
			if ((strlen(trim($show_image["image_url"])) > 0) && ($ad_configuration["popup_image_template_id"]))
			{
				//get template
				//place the image within the template
				$sql_query = "select template_code from geodesic_templates where template_id = ".$ad_configuration["popup_image_template_id"];
				if ($debug_popup)
					echo $sql_query."<br>\n";
				$template_result = $db->Execute($sql_query);
				if (!$template_result)
				{
					echo $sql_query."<bR>\n";
					exit;
				}
				elseif ($template_result->RecordCount() ==1)
				{
					$show_template = $template_result->FetchRow();
					$template =  stripslashes($show_template["template_code"]);
					if ($debug_popup)
					{
						echo $image_width." is image width before<br>\n";
						echo $image_height." is image height before<br>\n";
						echo $ad_configuration["maximum_full_image_width"]." is MAXIMUM_FULL_IMAGE_WIDTH<br>\n";
						echo $ad_configuration["maximum_full_image_height"]." is MAXIMUM_FULL_IMAGE_HEIGHT<br>\n";
						echo $show_image["original_image_width"]." is ORIGINAL_IMAGE_WIDTH<br>\n";
					}

					//$replace = $site->display_image($db, $show_image["image_url"], $image_width, $image_height, $show_image['mime_type']);
                    $solt = rand(1,10000);
                    $replace = "<img src=imagewm.php?image=".$_REQUEST['popupimage']."&st=$solt border=0>";

                    $template = str_replace("<<DISPLAY_IMAGE>>",$replace,$template);
					if ($debug_popup)
						echo $show_image["display_order"]." is this images display_order value<Br>\n";

					$sql_query = "select text_id,text from geodesic_pages_messages_languages
						where page_id = 157 and language_id = ".$language_id;
					if ($debug_popup)
						echo $sql_query."<br>\n";
					$result = $db->Execute($sql_query);
					if (!$result)
					{
						echo "<img src=".$show_image["image_url"]." border=0>";
					}
					elseif ($result->RecordCount() > 0)
					{
						//take the database message result and push the contents into an array
						while ($show = $result->FetchRow())
						{
							$messages[$show["text_id"]] = $show["text"];
							//echo $show["text_id"]." - ".$show["text"]."<br>\n";
						}
					}
					if ($show_image["display_order"] == 1)
					{
						//this is the first image so there is no so replace previous link with nothing
						$template = str_replace("<<PREVIOUS_IMAGE_LINK>>","",$template);

						$sql_query = "select * from geodesic_classifieds_images_urls where
							classified_id = ".$show_image["classified_id"]."
							and display_order > ".$show_image["display_order"]." order by display_order asc limit 1";
						$next_image_result = $db->Execute($sql_query);
						if ($debug_popup)
							echo $sql_query."<br>\n";
						if (!$next_image_result)
						{
							echo "<img src=".$show_image["image_url"]." border=0>";
						}
						elseif ($next_image_result->RecordCount() == 1)
						{
							//get text for popup image
							$next_image = $next_image_result->FetchRow();


							//build link to next image
							$next_image_link = "<a href=get_image.php?popupimage=".$next_image["image_id"].">".
								urldecode($messages[2412])."</a>";
							//get link to next image if there is one
							$template = str_replace("<<NEXT_IMAGE_LINK>>",$next_image_link,$template);
						}
						else
						{
							//cannot find next image so replace next image link with nothing
							//possibly only one image attached to this ad
							$template = str_replace("<<NEXT_IMAGE_LINK>>","",$template);
						}
					}
					else
					{
						//get link to previous image if there is one
						$sql_query = "select * from geodesic_classifieds_images_urls where
							classified_id = ".$show_image["classified_id"]."
							and display_order > ".$show_image["display_order"]." order by display_order asc limit 1";
						$next_image_result = $db->Execute($sql_query);
						if ($debug_popup)
							echo $sql_query."<br>\n";
						if (!$next_image_result)
						{
							$template = str_replace("<<NEXT_IMAGE_LINK>>","",$template);
						}
						elseif ($next_image_result->RecordCount() == 1)
						{
							$next_image = $next_image_result->FetchRow();
							$next_image_link = "<a href=get_image.php?popupimage=".$next_image["image_id"].">".
								urldecode($messages[2412])."</a>";
							$template = str_replace("<<NEXT_IMAGE_LINK>>",$next_image_link,$template);
						}
						else
							$template = str_replace("<<NEXT_IMAGE_LINK>>","",$template);

						//get link to next image if there is one
						$sql_query = "select * from geodesic_classifieds_images_urls where
							classified_id = ".$show_image["classified_id"]."
							and display_order < ".$show_image["display_order"]." order by display_order desc limit 1";
						$previous_image_result = $db->Execute($sql_query);
						if ($debug_popup)
							echo $sql_query."<br>\n";
						if (!$previous_image_result)
						{
							$template = str_replace("<<PREVIOUS_IMAGE_LINK>>","",$template);
						}
						elseif ($previous_image_result->RecordCount() == 1)
						{
							$previous_image = $previous_image_result->FetchRow();
							$previous_image_link = "<a href=get_image.php?popupimage=".$previous_image["image_id"].">".
								urldecode($messages[2411])."</a>";
							$template = str_replace("<<PREVIOUS_IMAGE_LINK>>",$previous_image_link,$template);
						}
						else
							$template = str_replace("<<PREVIOUS_IMAGE_LINK>>","",$template);

					}
					echo $template;
					exit;
				}
				else
				{
					echo $site->display_image($db, $show_image["image_url"], 0, 0, $show_image['mime_type']);
					//echo "<img src=".$show_image["image_url"]." border=0>";
					exit;
				}
			}
			elseif (strlen(trim($show_image["image_url"])) > 0)
			{
				//display image only
				echo $site->display_image($db, $show_image["image_url"], 0, 0, $show_image['mime_type']);
				//echo "<img src=".$show_image["image_url"]." border=0>";
				exit;
			}
			else
			{
				//no image to display
			}
		}
		else
		{
			//check uploaded image type???
			$sql_query = "SELECT * FROM geodesic_classifieds_images WHERE image_id = ".$_REQUEST["popupimage"];
			if ($debug_popup)
				echo $sql_query."<br>\n";
			$image_result = $db->Execute($sql_query);
			if (!$image_result)
			{
				echo $sql_query."<bR>\n";
				exit;
			}
			elseif ($image_result->RecordCount() == 1)
			{

			}
			else
			{
				//no image to display
			}
		}
	}
	else
		exit;
}
?>