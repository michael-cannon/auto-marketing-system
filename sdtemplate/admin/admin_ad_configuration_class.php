<?// admin_ad_configuration_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Ad_configuration extends Admin_site {

	var $internal_error_message = "There was an internal error";
	var $data_error_message = "Not enough data to complete request";
	var $page_text_error_message = "No text connected to this page";
	var $no_pages_message = "No pages to list";

	var $ad_configuration_message;
	var $ad_configuration_data;

	var $default_ad_template = "";

	var $default_extra_template = "";

	var $default_extra_template2 = "";

	var $default_checkbox_template2 = "";

	var $debug_ad = 0;
	var $debug_auction = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Ad_configuration($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_ad_configuration_form($db)
	{
		if ($this->is_class_auctions() || $this->is_auctions())
		{
			$config_title = "Listing Setup";
			$item_name = "listing";
		}
		elseif ($this->is_auctions())
		{
			$config_title = "Auction Setup";
			$item_name = "Auction";
		}
		else
		{
			$config_title = "Ad Setup";
			$item_name = "Ad";
		}
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"maximum number of photos allowed in ".$item_name."\", \"This determines the number of photos referenced by url, uploaded or a combination of both that can appear in a registrant's listing.  If you do not want to allow images at all set this value to 0.  THIS WILL BE THE TOTAL NUMBER OF PHOTOS THAT CAN BE ATTACHED TO A LISTING. ALL IMAGES UPLOADED FOR A SPECIFIC ".$item_name." WILL BE DISPLAYED ON THE FULL IMAGES PAGE FOR EVERY LISTING.\"]\n
			Text[2] = [\"maximum number of photos displayed within listing detail\", \"This determines the number of photos referenced by url, uploaded or a combination of both that can appear in the standard listing display.  IF THIS IS SET TO 0, THERE WILL BE NO LIMIT.\"]\n
			Text[3] = [\"allow images to be uploaded\", \"You can allow users to upload images to the site by clicking yes here.  If the maximum number of images above is not zero the users will be allowed to upload images to the site.\"]\n
			Text[4] = [\"upload images to database or file directory\", \"If you choose to allow user to upload images, which way do you want to save the image files?  You can save the image in the database or as image files in a directory on the server.  We recommend saving the images as files on the server.  But if server permissions or other problems prevent this you can use the database.  If you use the database method your database could possibly become very large and difficult to backup.\"]\n
			Text[5] = [\"imagecreatetruecolor (GD Library 2.0) manual switch\", \"By using the default setting you are affirming that your server has Gd Library 2.0 or above.  To find out for sure you may need to contact your host or upload a phpinfo() function to your server and verify.  If you allow uploading of images to your server you may sometimes encounter a error that stops the image upload (sometimes without any errors).  We have found that several servers report the existence of the imagecreatetruecolor command and then immediately errors out with many times no error messages.  Leaving the setting below in the default position will assume the existence of the imagecreatetruecolor command is reported correctly.  Clicking \\\"manual override\\\" will force the script to use older methods of creating the thumbnail images ie use the imagecreate command.  The imagecreatetruecolor command is more efficient at creating the thumbnail.\"]\n
			Text[6] = [\"url path to image directory if allow uploaded to a file\", \"TRAILING SLASH REQUIRED.  If you save uploaded images to a image file (and not in the database) this application needs a directory to place images when they are uploaded.  This directory must be viewable from the internet and should be the same directory as the \\\"directory path to images directory\\\" in the next field.  The images are removed after the listing they are attached to has expired.\"]\n
			Text[7] = [\"image upload type\", \"If you allow uploading of images to your server there are two ways to accomplish this depending on your servers configuration.  The first configuration (and default) is if your server allows manipulations of files within the temp directory.  This is the most common configuration. The second configuration is necessary if you are not allowed to perform operations on files within your servers tmp directory.  If you use the second configuration you will need to create a temporary directory with the appropriate rights to place image files in temporarily so the application can perform certain operations on the files.  Try the first configuration initially as this is the most common. If you need to try the second configuration you will need to enter a \\\"temp directory\\\" into the \\\"directory path to images directory\\\"( the next field).\"]\n
			Text[8] = [\"directory path to images directory\", \"TRAILING SLASH REQUIRED.  If you allow any type of uploaded file this application needs a directory to place images when they are uploaded.  This field should contain the document path to the directory on the server. This is the server path the images directory from the server root directory. The path to this admin document is listed below to help. This is necessary for the uploading and saving of a file directly on the server.... Or for the second image upload type chosen in the previous data field. The images are removed after the listing has expired or they are no longer needed.\"]\n
			Text[9] = [\"allow images to be referenced by url\", \"You can allow users to reference images by url that are placed in their listings.  If the maximum number of images above is not zero the users will be allowed to enter urls of images to place as images in a listing.\"]\n
			Text[10] = [\"maximum size of uploaded file\", \"Set the maximum size in bytes an uploaded file can be if allowed in the previous setting.\"]\n
			Text[11] = [\"maximum width of images displayed on site\", \"Set the maximum width of images displayed on your site.  If an image referenced or uploaded in an ".$item_name." has width dimensions larger than your maximum width the image appearing in the registrants ".$item_name." will be resized to have a width equal to or less than your maximum width.  If an image is resized a link will appear below the image allowing the user browsing the ".$item_name." to see the image in a popup window at the images full dimensions.  In resizing the image this application will keep the images proportions intact.\"]\n
			Text[12] = [\"maximum height of images displayed on site\", \"Set the maximum height of images displayed on your site.  If an image referenced or uploaded in an ".$item_name." has height dimensions larger than your maximum height the image appearing in the registrants ".$item_name." will be resized to have a width equal to or less than your maximum height.  If an image is resized a link will appear below the image allowing the user browsing the ".$item_name." to see the image in a popup window at the images full dimensions.  In resizing the image the this application will keep the images proportions intact.\"]\n
			Text[13] = [\"maximum width of images displayed on full-sized and pop-up image pages\", \"Set the maximum width of images displayed on your full-sized image page.  If an image referenced or uploaded in an ".$item_name." has width dimensions larger than your maximum width the image appearing on any specific listings full-sized image page the image will be resized to have a width equal to or less than your maximum width.  In resizing the image the this application will keep the images proportions intact. Click on the \\\"Edit Popup Image Box\\\" link below to change the template inside the popup window or to modify the \\\"popup image extra width/height\\\".\"]\n
			Text[14] = [\"maximum height of images displayed on full-sized and pop-up image pages\", \"Set the maximum height of images displayed on your full-sized image page.  If an image referenced or uploaded in an ".$item_name." has height dimensions larger than your maximum width the image appearing on any specific listings full-sized image page the image will be resized to have a width equal to or less than your maximum width.  In resizing the image the this application will keep the images proportions intact. Click on the \\\"Edit Popup Image Box\\\" link below to change the template inside the popup window or to modify the \\\"popup image extra width/height\\\".\"]\n
			Text[15] = [\"quality adjustment for resized images\", \"If you having image display problems you can adjust the quality on the images produced.  The new setting will only affect thumbnail images created in the future.  This does not affect current thumbnail images.\"]\n
			Text[16] = [\"number of photo columns displayed in a listing\", \"Pay attention to the maximum width you have set for images displayed on your site.  If your center columns width is 600 there will be enough space to put 2 up to 300 pixels width images or 3 up to 200 pixels width images in the listing ".$item_name." without stretching the sites design.\"]\n
			Text[17] = [\"maximum length of image description within ".$item_name." display\", \"This setting allows you to set the maximum length (in characters) of the text displayed with each image within the ".$item_name." display page. The full image text will be displayed within the full size image page.  The maximum characters allowed within a description is 256 characters.\"]\n
			Text[18] = [\"client side image uploader\", \"This switch allows for the ability of client side image compression.\"]\n
			Text[19] = [\"client side image uploader gui\", \"This setting changes the graphical user interface of the advanced uploader.\"]\n";

		//".$this->show_tooltip(1,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$sql_query = "select * from ".$this->site_configuration_table;
		$site_result = $db->Execute($sql_query);
		if (!$site_result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}

		$sql_query = "select * from ".$this->ad_configuration_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif (($result->RecordCount() == 1) && ($site_result->RecordCount() == 1))
		{
			$show_configuration = $result->FetchRow();
			$site_configuration = $site_result->FetchRow();
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=1 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1  width=100%>\n";
			if ($this->ad_configuration_message)
				$this->body .= "<tr>\n\t<td colspan=2 class=medium_error_font>\\n\t".$this->ad_configuration_message."\n\t</td>\n</tr>\n";

			$this->title = "Listing Setup > Photo Upload Settings";
			$this->description = "Control the number of photos and whether or not to allow a \"url\" field in your registrants
				listings in the top form.  Also control the maximum sizes of uploaded or referenced images displayed in your site as well as how many columns are
				used in displaying them within the site.";

			//number of maximum photos allowed
			$this->body .= "<tr class=row_color2 >\n\t<td align=right width=50% valign=top class=medium_font>\n\tmaximum number of photos allowed in a listing:".$this->show_tooltip(1,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top>\n\t<select name=b[number_of_photos]>\n\t\t";
			for ($i=0;$i<21;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_configuration["maximum_photos"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";

			//number of maximum photos displayed in ad detail
			$this->body .= "<tr class=row_color2 >\n\t<td align=right width=50% valign=top class=medium_font>\n\tmaximum number of photos displayed within listing detail:".$this->show_tooltip(2,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top>\n\t<select name=b[number_of_photos_in_detail]>\n\t\t";
			for ($i=0;$i<21;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_configuration["number_of_photos_in_detail"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";

			//allow uploading of images
			$this->body .= "<tr>\n\t<td align=right valign=top class=medium_font>\n\tallow images to be uploaded:".$this->show_tooltip(3,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=b[allow_upload_images] value=1 ";
			if ($show_configuration["allow_upload_images"] == 1)
				$this->body .= "checked";
			$this->body .= "> yes<br><input type=radio name=b[allow_upload_images] value=0 ";
			if ($show_configuration["allow_upload_images"] == 0)
				$this->body .= "checked";
			$this->body .= "> no\n\t</td>\n</tr>\n";

			//allow uploading of images to db or to server
			/*
			$this->body .= "<tr>\n\t<td align=right valign=top class=medium_font>\n\tupload images to database or file directory:".$this->show_tooltip(4,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=b[image_upload_save_type] value=1 ";
			if ($show_configuration["image_upload_save_type"] == 1)
				$this->body .= "checked";
			$this->body .= "> save images in database<br><input type=radio name=b[image_upload_save_type] value=2 ";
			if ($show_configuration["image_upload_save_type"] == 2)
				$this->body .= "checked";
			$this->body .= "> save images as file\n\t</td>\n</tr>\n";
			*/
			$this->body .= "<input type=hidden name=b[image_upload_save_type] value='".$show_configuration["image_upload_save_type"]."'>";

			$this->body .= "<tr class=row_color1 >\n\t<td align=right valign=top class=medium_font>\n\timagecreatetruecolor (GD Library 2.0) manual switch:".$this->show_tooltip(5,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=b[imagecreatetruecolor_switch] value=0 ";
			if ($show_configuration["imagecreatetruecolor_switch"] == 0)
				$this->body .= "checked";
			$this->body .= "> default (imagecreatetruecolor exists and works)<br><input type=radio name=b[imagecreatetruecolor_switch] value=1 ";
			if ($show_configuration["imagecreatetruecolor_switch"] == 1)
				$this->body .= "checked";
			$this->body .= "> manual override (imagecreatetruecolor is reported to exist but stops upload process)\n\t</td>\n</tr>\n";

			//if save to server as file what is the url
			$this->body .= "<tr class=row_color2 >\n\t<td align=right valign=top class=medium_font>\n\turl path to image directory if allow uploaded to a file:".$this->show_tooltip(6,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t
				<input type=text name=b[url_image_directory] value=\"".$show_configuration["url_image_directory"]."\" size=60>\n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color2 >\n\t<td align=right valign=top class=medium_font>\n\timage upload type:".$this->show_tooltip(7,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=b[image_upload_type] value=0 ";
			if ($show_configuration["image_upload_type"] == 0)
				$this->body .= "checked";
			$this->body .= "> option 1 (default)<br><input type=radio name=b[image_upload_type] value=1 ";
			if ($show_configuration["image_upload_type"] == 1)
				$this->body .= "checked";
			$this->body .= "> option 2\n\t</td>\n</tr>\n";

			//if uploaded -- temp dir
			$this->body .= "<tr class=row_color2 >\n\t<td align=right valign=top class=medium_font>\n\tdirectory path to images directory:".$this->show_tooltip(8,1)."<br>\n\t
				<span class=small_font>Path to this document: ".$_SERVER["DOCUMENT_ROOT"]."</span><br></td>";
			//echo $show_configuration["image_upload_path"]." is the path<Br>\n";
			$this->body .= "<td valign=top class=medium_font>\n\t
				<input type=text name=b[image_upload_path] value=\"".stripslashes($show_configuration["image_upload_path"])."\" size=60>\n\t</td>\n</tr>\n";

			//allow url referenced images
			$this->body .= "<tr>\n\t<td align=right valign=top class=medium_font>\n\tallow images to be referenced by url:".$this->show_tooltip(9,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=b[allow_url_referenced] value=1 ";
			if ($show_configuration["allow_url_referenced"] == 1)
				$this->body .= "checked";
			$this->body .= "> yes<br><input type=radio name=b[allow_url_referenced] value=0 ";
			if ($show_configuration["allow_url_referenced"] == 0)
				$this->body .= "checked";
			$this->body .= "> no\n\t</td>\n</tr>\n";

			//maximum file size of images to upload
			$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font>\n\tmaximum size of uploaded file:".$this->show_tooltip(10,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t
				<input type=text name=b[maximum_upload_size] value=\"".$show_configuration["maximum_upload_size"]."\"> bytes\n\t</td>\n</tr>\n";

			//maximum width of images displayed on site
			$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font>\n\tmaximum width of images displayed on site:".$this->show_tooltip(11,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t
				<input type=text name=b[maximum_image_width] value=\"".$show_configuration["maximum_image_width"]."\"> pixels\n\t</td>\n</tr>\n";

			//maximum height of images displayed on site
			$this->body .= "<tr class=row_color2 >\n\t<td align=right valign=top class=medium_font>\n\tmaximum height of images displayed on site:".$this->show_tooltip(12,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t
				<input type=text name=b[maximum_image_height] value=\"".$show_configuration["maximum_image_height"]."\"> pixels\n\t</td>\n</tr>\n";

			//maximum width of images displayed on full-sized image page
			$this->body .= "<tr>\n\t<td align=right valign=top class=medium_font>\n\tmaximum width of images displayed on full-sized and pop-up image pages:".$this->show_tooltip(13,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t
				<input type=text name=b[maximum_full_image_width] value=\"".$show_configuration["maximum_full_image_width"]."\"> pixels\n\t</td>\n</tr>\n";

			//maximum height of images displayed on full-sized image page
			$this->body .= "<tr>\n\t<td align=right valign=top class=medium_font>\n\tmaximum height of images displayed on full-sized and pop-up image pages:".$this->show_tooltip(14,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t
				<input type=text name=b[maximum_full_image_height] value=\"".$show_configuration["maximum_full_image_height"]."\"> pixels\n\t</td>\n</tr>\n";

			$this->body .= "<tr>\n\t<td align=center colspan=2><a href=index.php?a=23&r=19><span class=medium_font>Edit Popup Image Box</span></a></td>\n</tr>\n";

			//image quality on resized images
			$this->body .= "<tr class=row_color2>\n\t<td align=right width=50% valign=top class=medium_font>\n\tquality adjustment for resized images:".$this->show_tooltip(15,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top>\n\t<select name=b[photo_quality]>\n\t\t";
			for ($i=0;$i<101;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_configuration["photo_quality"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";

			//number of photo columns
			$this->body .= "<tr class=row_color1>\n\t<td align=right width=50% valign=top class=medium_font>\n\tnumber of photo columns displayed in a listing:".$this->show_tooltip(16,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top>\n\t<select name=b[photo_columns]>\n\t\t";
			for ($i=1;$i<9;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_configuration["photo_columns"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";

			//maximum length of image text to display
			$this->body .= "<tr class=row_color2>\n\t<td align=right width=50% valign=top class=medium_font>\n\tmaximum length of image description within listing display:".$this->show_tooltip(17,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top>\n\t<select name=b[maximum_image_description]>\n\t\t";
			for ($i=1;$i<257;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_configuration["maximum_image_description"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";

			//image link to full-size page or java popup of image clicked
			$this->body .= "<tr class=row_color1>\n\t<td align=right width=50% valign=top class=medium_font>\n\tlarger image links to full-size image page or javascript popup of image:".$this->show_tooltip(18,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=b[image_link_destination_type] value=1 ";
			if ($site_configuration["image_link_destination_type"] == 1)
				$this->body .= "checked";
			$this->body .= "> link to full-sized image page<br><input type=radio name=b[image_link_destination_type] value=0 ";
			if ($site_configuration["image_link_destination_type"] == 0)
				$this->body .= "checked";
			$this->body .= "> javascript popup of full-sized image\n\t</td>\n</tr>\n";

			if(is_dir('../classes/aurigma/'))
			{
				//use aurigma uploader
				$this->body .= "<tr class=row_color2>\n\t<td align=right width=50% valign=top class=medium_font>\n\tUse Client Side Image Uploader:".$this->show_tooltip(18,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=b[clientside_image_uploader] value=1 ";
				if ($show_configuration["clientside_image_uploader"] == 1)
					$this->body .= "checked";
				$this->body .= "> yes<br><input type=radio name=b[clientside_image_uploader] value=0 ";
				if ($show_configuration["clientside_image_uploader"] == 0)
					$this->body .= "checked";
				$this->body .= "> no\n\t</td>\n</tr>\n";
				
				//use aurigma uploader by default
				$this->body .= "<tr class=row_color1>\n\t<td align=right width=50% valign=top class=medium_font>\n\tDefault Image Uploader:".$this->show_tooltip(18,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=b[image_uploader_default] value=1 ";
				if ($show_configuration["image_uploader_default"] == 1)
					$this->body .= "checked";
				$this->body .= "> Advanced<br><input type=radio name=b[image_uploader_default] value=0 ";
				if ($show_configuration["image_uploader_default"] == 0)
					$this->body .= "checked";
				$this->body .= "> Basic\n\t</td>\n</tr>\n";
				
				//set aurigma uploader gui
				$this->body .= "<tr class=row_color2>\n\t<td align=right width=50% valign=top class=medium_font>\n\tSelect Client Side Image Uploader GUI:".$this->show_tooltip(19,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=b[clientside_image_uploader_view] value=0 ";
				if ($show_configuration["clientside_image_uploader_view"] == 0)
					$this->body .= "checked";
				$this->body .= "> Drag & Drop<br><input type=radio name=b[clientside_image_uploader_view] value=1 ";
				if ($show_configuration["clientside_image_uploader_view"] == 1)
					$this->body .= "checked";
				$this->body .= "> Checkbox\n\t</td>\n</tr>\n";
			}

			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% align=center class=row_color1 >\n";
			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=2 class=large_font_light>\n\t<b>Lead Image Dimensions</b>\n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td colspan=2 class=medium_font>\n\tYou can choose to display a larger thumbnail image of
				the \"first\" image in any listing by inserting the &lt;&lt;LEAD_PICTURE&gt;&gt; tag directly into the Listing
				Details Template.  You can determine the maximum dimensions of that image using the form below. The original proportions
				of the image will be kept.  If either of these values are 0 the lead picture will not be displayed within the Listing
				Details Template.\n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color1 ><td align=right class=medium_font>\n\tLead Picture Width</td>";
			$this->body .= "<td valign=top>\n\t<input type=text name=b[lead_picture_width] value=".$show_configuration["lead_picture_width"]."></td></tr>";

			$this->body .= "<tr class=row_color2 ><td align=right class=medium_font>\n\tLead Picture Height</td>";
			$this->body .= "<td valign=top>\n\t<input type=text name=b[lead_picture_height] value=".$show_configuration["lead_picture_height"]."></td></tr>";

			if (!$this->admin_demo())
				$this->body .= "<tr>\n\t<td colspan=2 align=center class=medium_font>\n\t<input type=submit value=\"Save\" name=submit>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";



			return true;
		}
		else
		{
			echo $sql_query." is the query<BR>\n";
			return false;
		}

	} //end of function display_ad_configuration_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_ad_configuration($db,$configuration_info=0)
	{
		if ($configuration_info)
		{
			$this->sql_query = "select maximum_image_width,maximum_image_height from ".$this->ad_configuration_table;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query."<br>\n";
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_current_sizes = $result->FetchRow();
				if (($show_current_sizes["maximum_image_width"] != $configuration_info["maximum_image_width"]) ||
					($show_current_sizes["maximum_image_height"] != $configuration_info["maximum_image_height"]))
				{
					$this->sql_query = "update ".$this->ad_configuration_table." set
						maximum_image_width = ".$configuration_info["maximum_image_width"].",
						maximum_image_height = ".$configuration_info["maximum_image_height"];
					//echo $this->sql_query."<br>\n";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						//echo $this->sql_query."<br>\n";
						return false;
					}
					else
					{
						$this->image_resize_table($db);
					}
				}
			}

			$this->sql_query = "update ".$this->ad_configuration_table." set
				maximum_photos = ".$configuration_info["number_of_photos"].",
				number_of_photos_in_detail = ".$configuration_info["number_of_photos_in_detail"].",
				imagecreatetruecolor_switch = ".$configuration_info["imagecreatetruecolor_switch"].",
				image_upload_path = \"".addslashes($configuration_info["image_upload_path"])."\",
				image_upload_type = \"".$configuration_info["image_upload_type"]."\",
				allow_upload_images = ".$configuration_info["allow_upload_images"].",
				allow_url_referenced = ".$configuration_info["allow_url_referenced"].",
				maximum_full_image_height  = ".$configuration_info["maximum_full_image_height"].",
				maximum_full_image_width  = ".$configuration_info["maximum_full_image_width"].",
				lead_picture_width = \"".$configuration_info["lead_picture_width"]."\",
				lead_picture_height = \"".$configuration_info["lead_picture_height"]."\",
				maximum_upload_size = ".$configuration_info["maximum_upload_size"].",
				maximum_image_description = ".$configuration_info["maximum_image_description"].",
				url_image_directory = \"".$configuration_info["url_image_directory"]."\",
				image_upload_save_type = ".$configuration_info["image_upload_save_type"].",
				photo_quality = ".$configuration_info["photo_quality"].",
				photo_columns = ".$configuration_info["photo_columns"];
			//echo $this->sql_query."<br>\n";
			
			
			if(is_dir('../classes/aurigma/'))
			{
				$this->sql_query .= ", clientside_image_uploader = ".$configuration_info["clientside_image_uploader"];
				$this->sql_query .= ", clientside_image_uploader_view = ".$configuration_info["clientside_image_uploader_view"];
				$this->sql_query .= ", image_uploader_default = ".$configuration_info["image_uploader_default"];
			}
			
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query."<br>\n";
				return false;
			}
			else
			{

				$this->sql_query = "update ".$this->site_configuration_table." set
					image_link_destination_type = ".$configuration_info["image_link_destination_type"];
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					//echo $this->sql_query."<br>\n";
					return false;
				}
				else
				{
					return true;
				}

			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_ad_configuration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function fields_to_use_form($db)
	{
		if ($this->is_class_auctions() || $this->is_auctions())
		{
			$config_title = "Listing Configuration";
			$item_name = "listing";
			$price_name = "<b>Price (Classifieds Only)</b>&nbsp;";
		}
		elseif ($this->is_auctions())
		{
			$config_title = "Auction Configuration";
			$item_name = "Auction";
		}
		else
		{
			$config_title = "Listing Setup";
			$item_name = "Ad";
			$price_name = "<b>Price</b>&nbsp;";
		}
		// Listings header
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "
			Text[1] = [\"use field\", \"Turning this field off here by unchecking the checkbox will turn it off in the \\\"place ".$item_name."\\\" and \\\"search\\\" processes as well as remove it from the browsing results.\"]\n
			Text[2] = [\"editable field\", \"Checking this checkbox will allow the field to be editable once the ".$item_name." has been placed. If no the seller can only change the value while placing the new ".$item_name.". THIS IS A SITE WIDE SETTING AND NOT CHANGEABLE ON A CATEGORY BY CATEGORY BASIS.\"]\n
			Text[3] = [\"require field\", \"Checking this checkbox will require this field within the ".$item_name." place an ".$item_name." process. The fields that do not have this choice are required if used\"]\n
			Text[4] = [\"display field\", \"Checking this checkbox will display this field as a sortable column while browsing the ".$item_name."s.\"]\n
			Text[5] = [\"field length\", \"This value will determine maximum amount of characters or numbers a user can place into this field while placing their ".$item_name.". The maximum number of characters that can be placed in any field is 256.\"]\n
			Text[6] = [\"photo icon/thumbnail (while browsing)\", \"Checking this checkbox will display the photo column next to the title column (at the left side of the browsing results). You can choose below whether or not to display the photo icon referenced below or the first image of the ".$item_name." in the selection below.\"]\n
			Text[7] = [\"PayPal \\\"Buy Now\\\"\", \"\"]\n
			Text[8] = [\"classified entry date (while browsing)\", \"Checking this box will display the entry date within the site's browsing tables.\"]\n
			Text[9] = [\"payment types\", \"Checking this box will display a series of 'payment types accepted' checkboxes for the seller to select from while listing their auctions.\"]\n
			Text[10] = [\"time left before auction closes (while browsing)\", \"Checking this box will display in the site's browsing tables the amount of time remaining for the auction before it will expire.\"]\n
			Text[11] = [\"number of bids\", \"Checking this box will display the current number of bids that have been placed on the auction in a column of the site's browsing tables.\"]\n
			Text[12] = [\"editable start bidding time field\", \"Checking this box will allow for the user to edit their start time for an auction.\"]\n
			Text[13] = [\"automatic line breaks on text areas\", \"Choosing \\\"yes\\\" will set the wrap attribute for any text area field for the place an ".$item_name." process. For example if a user clicks the return/enter key on their keyboard while typing their description the carriage return will be stored in the database and show in the display. Will help if you want your users to be able to create lists in their description or any other field that is set as a text area. DO NOT USE THIS IN CONJUNCTION WITH RICH TEXT EDITOR.\"]\n
			Text[14] = [\"display the description below the title\", \"If you choose to display the description while browsing you can choose to display the description in its own column or below the title.\"]\n
			Text[15] = [\"length of description to display (while browsing)\", \"Number of characters to display when users browse ".$item_name."s\"]\n
			Text[16] = [\"editable category specific questions\", \"Choosing \\\"yes\\\" will allow the seller to edit the category specific fields attached to their ".$item_name." once there ".$item_name." has been placed. If no is checked the seller will only be able to change the category specific questions attached to their ".$item_name." while placing their ".$item_name.". THIS IS A SITE WIDE SETTING AND NOT CHANGEABLE ON A CATEGORY BY CATEGORY BASIS\"]\n
			Text[17] = [\"entry date display configuration\", \"Choose which format you wish to display your entry date by choosing one of the configurations below.\"]\n
			Text[18] = [\"price (Classifieds Only)\", \"These settings apply to classifieds only.\"]\n

			Text[19] = [\"other box\", \"Checking this box will display an additional entry field for the user to enter information on this particular field.\"]\n
			Text[20] = [\"number only\", \"Checking this field will make the field 'number only', which also automatically displays as a 'high' and 'low' search field on the search page.\"]\n
			Text[21] = [\"type\", \"This field specifies the type of field that will be displayed to the seller when they are entering their information for this field.\"]\n
			Text[22] = [\"auction entry date (while browsing)\", \"Checking this box will display the entry date within the site's browsing tables.\"]\n
			Text[23] = [\"time left before classified expires (while browsing)\", \"Checking this box will display the time left before the classified expires within the site's browsing tables.\"]\n
			Text[24] = [\"optional field admin name\", \"Keep track of your Optional Site Wide Fields by giving them a name you choose.  This name will be visible throughout your admin, wherever the field is used.\"]\n
			";

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$sql_query = "select * from ".$this->ad_configuration_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}

		$sql_query = "select * from ".$this->site_configuration_table;
		$site_result = $db->Execute($sql_query);
		if (!$site_result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}

		if (($result->RecordCount() == 1) && ($site_result->RecordCount() == 1))
		{
			$show_configuration = $result->FetchRow();
			$site_configuration = $site_result->FetchRow();

			$this->title = "Listing Setup > Fields to Use";
			$this->description = "You can control the aspects of the ".$item_name."s your registrants post
				through this administration. Control the fields that are displayed in the ".$item_name.".
				Making changes here will affect the \"Listing\" and
				\"Search\" processes. Some extra information can be found in the tooltips.";

			$this->body .= "
				<script type=\"text/javascript\">
					function validate(field,max)
					{
						max=(max)?max:256;
						if (!(field.value>=0 && field.value<=max))
						{
							alert('Must be between 0 and '+max+'. Values outside this range as well as invalid characters will not be submitted.');
							field.value=\"\";
							field.focus();
						}
					}
					function check_all(elements,col)
					{
						for(x = 0; x < elements.length; x++)
						{
							if(elements[x].id == col && !elements[x].disabled)
								elements[x].checked=elements[col+'_all'].checked;
							if(elements[x].id == col+'_section' && !elements[x].disabled)
								elements[x].checked=elements[col+'_all'].checked;
						}
					}
				</script>";
			if (!$this->admin_demo())
				$this->body .= "<form name=fields_to_use action=index.php?a=23&r=4 method=post>";
			$this->body .= "<table cellpadding=0 cellspacing=0 border=0 align=center class=row_color1>\n";
			if ($this->ad_configuration_message)
				$this->body .= "
					<tr>
						<td colspan=2 class=medium_error_font>".$this->ad_configuration_message."</td>
					</tr>";

			// Block of checkboxes for major settings
			$this->body .= "
					<tr>
						<td colspan=2><table cellpadding=5 cellspacing=1 border=0 width=100%>
						<tr bgcolor=000066>
							<td class=medium_font_light colspan=6 align=center><b>\"Fields to Use\" in Listing</b></td>
							</tr>
							<tr class=row_color_black>
								<td align=center class=medium_font_light><b>field</b></td>
								<td align=center class=medium_font_light><b>use</b>".$this->show_tooltip(1,1)."</td>
								<td align=center class=medium_font_light><b>editable</b>".$this->show_tooltip(2,1)."</td>
								<td align=center class=medium_font_light><b>require</b>".$this->show_tooltip(3,1)."</td>
								<td align=center class=medium_font_light><b>display</b>".$this->show_tooltip(4,1)."</td>
								<td align=center class=medium_font_light><b>length</b>".$this->show_tooltip(5,1)."</td>
							</tr>";
			$this->row_count=0;
			if ($this->is_class_auctions() || $this->is_auctions())
			{
				//display photo column
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font>
									<b>Photo Icon/Thumbnail (while browsing)</b>&nbsp;".$this->show_tooltip(6,1)."
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align=center valign=top class=medium_font>
									<input id=display type=checkbox name=c[display_photo_icon] value=1 "
									.(($site_configuration['display_photo_icon']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;
			}

			// Title Field
			$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Title</b></td>
								<td>&nbsp;</td>
								<td valign=top align=center class=medium_font>
									<input id=editable type=checkbox name=b[editable_title_field] value=1 "
									.(($show_configuration['editable_title_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td align=center valign=top class=medium_font>
									<input id=display type=checkbox name=c[display_ad_title] value=1 "
									.(($site_configuration['display_ad_title']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top>
									<input onkeyup=validate(this) type=text name=b[title_length] size=3 maxsize=3 value="
									.$show_configuration['title_length'].">
								</td>
							</tr>";$this->row_count++;

			// Description Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Description</b></td>
								<td>&nbsp;</td>
								<td valign=top align=center class=medium_font>
									<input id=editable type=checkbox name=b[editable_description_field] value=1 "
									.(($show_configuration['editable_description_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td valign=top align=center class=medium_font>
									<input id=display type=checkbox name=c[display_ad_description] value=1 "
									.(($site_configuration['display_ad_description']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			if ($this->is_class_auctions() || $this->is_classifieds())
			{
				// Price Field
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font>".$price_name.$this->show_tooltip(18,1)."</td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_price_field] value=1 "
									.(($show_configuration['use_price_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=editable type=checkbox name=b[editable_price_field] value=1 "
									.(($show_configuration['editable_price_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td valign=top align=center class=medium_font>
									<input id=display type=checkbox name=c[display_price] value=1 "
									.(($site_configuration['display_price']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top>
									<input onkeyup=validate(this) type=text name=b[price_length] size=3 maxsize=3 value="
									.$show_configuration['price_length'].">
								</td>
							</tr>";$this->row_count++;
			}

			// Country Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Country</b></td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_country_field] value=1 "
									.(($show_configuration['use_country_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=editable type=checkbox name=b[editable_country_field] value=1 "
									.(($show_configuration['editable_country_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td valign=top align=center class=medium_font>
									<input id=display type=checkbox name=c[display_browsing_country_field] value=1 "
									.(($site_configuration['display_browsing_country_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			// State Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>State</b></td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_state_field] value=1 "
									.(($show_configuration['use_state_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=editable type=checkbox name=b[editable_state_field] value=1 "
									.(($show_configuration['editable_state_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td valign=top align=center class=medium_font>
									<input id=display type=checkbox name=c[display_browsing_state_field] value=1 "
									.(($site_configuration['display_browsing_state_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			// City Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>City</b></td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_city_field] value=1 "
									.(($show_configuration['use_city_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=editable type=checkbox name=b[editable_city_field] value=1 "
									.(($show_configuration['editable_city_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td valign=top align=center class=medium_font>
									<input id=display type=checkbox name=c[display_browsing_city_field] value=1 "
									.(($site_configuration['display_browsing_city_field']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top class=small_font>
									<input onkeyup=validate(this) type=text name=b[city_length] size=3 maxsize=3 value="
									.$show_configuration['city_length'].">
								</td>
							</tr>";$this->row_count++;

			// Zip Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Zip</b></td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_zip_field] value=1 "
									.(($show_configuration['use_zip_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=editable type=checkbox name=b[editable_zip_field] value=1 "
									.(($show_configuration['editable_zip_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td valign=top align=center class=medium_font>
									<input id=display type=checkbox name=c[display_browsing_zip_field] value=1 "
									.(($site_configuration['display_browsing_zip_field']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top class=small_font>
									<input onkeyup=validate(this) type=text name=b[zip_length] size=3 maxsize=3 value="
									.$show_configuration['zip_length'].">
								</td>
							</tr>";$this->row_count++;

			// Phone 1 Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Phone 1</b></td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_phone_1_option_field] value=1 "
									.(($show_configuration['use_phone_1_option_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=editable type=checkbox name=b[allow_phone_1_override] value=1 "
									.(($show_configuration['allow_phone_1_override']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align=center valign=top class=small_font>
									<input onkeyup=validate(this) type=text name=b[phone_1_length] size=3 maxsize=3 value="
									.$show_configuration['phone_1_length'].">
								</td>
							</tr>";$this->row_count++;

			// Phone 2 Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Phone 2</b></td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_phone_2_option_field] value=1 "
									.(($show_configuration['use_phone_2_option_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=editable type=checkbox name=b[allow_phone_2_override] value=1 "
									.(($show_configuration['allow_phone_2_override']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align=center valign=top class=small_font>
									<input onkeyup=validate(this) type=text name=b[phone_2_length] size=3 maxsize=3 value="
									.$show_configuration['phone_2_length'].">
								</td>
							</tr>";$this->row_count++;

			// Fax Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Fax</b></td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_fax_field_option] value=1 "
									.(($show_configuration['use_fax_field_option']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=editable type=checkbox name=b[allow_fax_override] value=1 "
									.(($show_configuration['allow_fax_override']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align=center valign=top class=small_font>
									<input onkeyup=validate(this) type=text name=b[fax_length] size=3 maxsize=3 value="
									.$show_configuration['fax_length'].">
								</td>
							</tr>";$this->row_count++;

			// Url Link Fields
			for ($i=1;$i<4;$i++)
			{
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>URL Link ".$i."</b></td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_url_link_".$i."] value=1 "
								.(($show_configuration["use_url_link_".$i]==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=editable type=checkbox name=b[editable_url_link_".$i."] value=1 "
								.(($show_configuration["editable_url_link_".$i]==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=c[require_url_link_".$i."] value=1 "
								.(($site_configuration["require_url_link_".$i]==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td align=center valign=top class=small_font>
									<input onkeyup=validate(this) type=text name=b[url_link_".$i."_length] size=3 maxsize=3 value="
									.$show_configuration["url_link_".$i."_length"].">
								</td>
							</tr>";$this->row_count++;
			}

			// Email Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Email</b></td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_email_option_field] value=1 "
									.(($show_configuration['use_email_option_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=editable type=checkbox name=b[use_email_override] value=1 "
									.(($show_configuration['use_email_override']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td valign=top align=center class=medium_font>
									<input id=display type=checkbox name=b[publically_expose_email] value=1 "
									.(($show_configuration['publically_expose_email']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			// Mapping Address Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Mapping Address</b></td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_mapping_address_field] value=1 "
									.(($show_configuration['use_mapping_address_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			// Mapping City Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Mapping City</b></td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_mapping_city_field] value=1 "
									.(($show_configuration['use_mapping_city_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			// Mapping State Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Mapping State</b></td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_mapping_state_field] value=1 "
									.(($show_configuration['use_mapping_state_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			// Mapping Country Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Mapping Country</b></td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_mapping_country_field] value=1 "
									.(($show_configuration['use_mapping_country_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			// Mapping Zip Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Mapping Zip</b></td>
								<td valign=top align=center class=medium_font>
									<input id=use type=checkbox name=b[use_mapping_zip_field] value=1 "
									.(($show_configuration['use_mapping_zip_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			// PayPal "Buy Now"
			if(file_exists("../classes/class.paypal.php"))
			{
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>PayPal \"Buy Now\"</b>&nbsp;".$this->show_tooltip(7,1)."</td>
								<td valign=top align=center class=medium_font>
									<input type=checkbox id=use name=b[use_buy_now] value=1 "
									.(($show_configuration['use_buy_now']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top class=medium_font>
									<input type=checkbox id=editable name=b[editable_buy_now] value=1 "
									.(($show_configuration['editable_buy_now']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top class=medium_font>
									<input type=checkbox id=require name=b[require_buy_now] value=1 "
									.(($show_configuration['require_buy_now']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;
			}

			if ($this->is_class_auctions() || $this->is_classifieds())
			{
				//display classified entry date
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Classified Entry Date (while browsing)</b>&nbsp;".$this->show_tooltip(8,1)."</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align=center valign=top class=medium_font>
									<input id=display type=checkbox name=c[display_entry_date] value=1 "
									.(($site_configuration['display_entry_date']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;
			}

			if ($this->is_class_auctions() || $this->is_auctions())
			{
				//display auction entry date
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Auction Entry Date (while browsing)</b>&nbsp;".$this->show_tooltip(22,1)."</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align=center valign=top class=medium_font>
									<input id=display type=checkbox name=c[auction_entry_date] value=1 "
									.(($site_configuration['auction_entry_date']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;
			}


			//display date posted column
			$this->body .= "<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Business Type</b></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align=center valign=top class=medium_font>
									<input id=display type=checkbox name=c[display_business_type] value=1 "
									.(($site_configuration['display_business_type']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			if ($this->is_class_auctions() || $this->is_auctions())
			{
				//payment types
				//NOTE - payment_types is a USE variable
				//NOTE - payment_types_use is a REQUIRE variable
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Payment Types</b>&nbsp;".$this->show_tooltip(9,1)."</td>
								<td valign=top align=center class=medium_font>
									<input type=checkbox id=use name=c[payment_types] value=1 "
									.(($site_configuration['payment_types']==1) ? "checked" : "").">
								<td>&nbsp;</td>
								<td align=center valign=top class=medium_font>
									<input type=checkbox id=require name=c[payment_types_use] value=1 "
									.(($site_configuration['payment_types_use']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

				//display auction time left
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font>
									<b>Time Left Before Auction Closes (while browsing)</b>&nbsp;".$this->show_tooltip(10,1)."
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align=center valign=top class=medium_font>
									<input id=display type=checkbox name=c[display_time_left] value=1 "
									.(($site_configuration['display_time_left']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;
			}
			if ($this->is_class_auctions() || $this->is_classifieds())
			{
				//display classified time left
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font>
									<b>Time Left Before Classified Expires (while browsing)</b>&nbsp;".$this->show_tooltip(23,1)."
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align=center valign=top class=medium_font>
									<input id=display type=checkbox name=c[classified_time_left] value=1 "
									.(($site_configuration['classified_time_left']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;
			}
			if ($this->is_class_auctions() || $this->is_auctions())
			{
				//display number of bids left column
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font>
									<b>Number of Bids (while browsing)</b>&nbsp;".$this->show_tooltip(11,1)."
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align=center valign=top class=medium_font>
									<input id=display type=checkbox name=c[display_number_bids] value=1 "
									.(($site_configuration['display_number_bids']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

				// editable start time field if no bids have been recorded
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font>
									<b>Editable Start Bidding Time</b>&nbsp;".$this->show_tooltip(12,1)."
								</td>
								<td>&nbsp;</td>
								<td valign=top align=center class=medium_font>
									<input id=editable type=checkbox name=b[editable_bid_start_time_field] value=1 "
									.(($show_configuration['editable_bid_start_time_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;
			}
			$this->body .= "<tr class=row_color_black>
								<td align=right class=medium_font_light><b>select all:&nbsp;&nbsp;</b></td>
								<td align=center>
									<input id=use_all onclick=\"javascript:check_all(document.fields_to_use,'use');\" type=checkbox>
								</td>
								<td align=center>
									<input id=editable_all onclick=\"javascript:check_all(document.fields_to_use,'editable');\" type=checkbox>
								</td>
								<td align=center>
									<input id=require_all onclick=\"javascript:check_all(document.fields_to_use,'require');\" type=checkbox>
								</td>
								<td align=center>
									<input id=display_all onclick=\"javascript:check_all(document.fields_to_use,'display');\" type=checkbox>
								</td>
								<td align=center>
									<input type=\"button\" onclick=\"reset()\" value=\"reset form\">
								</td>
							</tr>
						</table>
						<tr>
							<td align=center colspan=6>&nbsp;</td>
						</tr>
						<tr><td colspan=2><table cellpadding=5 cellspacing=1 border=0 width=100%>
						<tr bgcolor=000066>
							<td colspan=2 class=medium_font_light align=center><b>Additional Miscellaneous Settings</b></td>
						</tr></td></table></tr>";
			$this->row_count=1;
			//"wrap" in text area boxes
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right valign=center width=55% class=medium_font>
								<b>automatic line breaks on text areas:</b>&nbsp;".$this->show_tooltip(13,1)."
							</td>
							<td width=50% valign=top class=medium_font>
								<input type=radio name=b[textarea_wrap] value=1 "
								.(($show_configuration["textarea_wrap"]==1) ? "checked": "")."> yes<br>
								<input type=radio name=b[textarea_wrap] value=0 "
								.(($show_configuration["textarea_wrap"]==0) ? "checked": "")."> no
							</td>
						</tr>";$this->row_count++;

			//display description below category name while browsing
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right valign=center width=50% class=medium_font>
								<b>display the description below the title:</b>&nbsp;".$this->show_tooltip(14,1)."
							</td>
							<td width=50% valign=top class=medium_font>
								<input type=radio name=c[display_ad_description_where] value=1 "
								.(($site_configuration["display_ad_description_where"]==1) ? "checked": "").">	below title<br>
								<input type=radio name=c[display_ad_description_where] value=0 "
								.(($site_configuration["display_ad_description_where"]==0) ? "checked": "")."> own column
							</td>
						</tr>";$this->row_count++;

			//length of description to display if displayed
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right valign=center width=50% class=medium_font>
								<b>length of description to display (while browsing):</b>&nbsp;".$this->show_tooltip(15,1)."
							</td>
							<td width=50% valign=top class=medium_font>
								<input type=radio name=c[display_all_of_description] value=1 "
								.(($site_configuration["display_all_of_description"]==1) ? "checked": "")."> all of description<br>
								<input type=radio name=c[display_all_of_description] value=0 "
								.(($site_configuration["display_all_of_description"]==0) ? "checked": "")."> display this many characters&nbsp;&nbsp;&nbsp;
								<input onkeyup=validate(this,500) type=text name=c[length_of_description] size=3 maxsize=3 value="
								.$site_configuration['length_of_description'].">
							</td>
						</tr>";$this->row_count++;

			//edit category specific
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right valign=center width=50% class=medium_font>
								<b>editable category specific questions:</b>&nbsp;".$this->show_tooltip(16,1)."</td>
							<td width=50% valign=top class=medium_font>
								<input type=radio name=b[editable_category_specific] value=1 "
								.(($show_configuration["editable_category_specific"]==1) ? "checked": "")."> yes<br>
								<input type=radio name=b[editable_category_specific] value=0 "
								.(($show_configuration["editable_category_specific"]==0) ? "checked": "")."> no
							</td>
						</tr>";$this->row_count++;

			//entry date format
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right class=medium_font>
								<b>date display configuration:</b>&nbsp;".$this->show_tooltip(17,1)."
							</td>
							<td valign=top class=medium_font>
								<input type=radio name=c[entry_date_configuration] value=\"M j-G:i\" "
									.(($site_configuration["entry_date_configuration"]=="M j-G:i") ? "checked" : "").">Month day-hour:minute - Jan 1-12:00<br>
								<input type=radio name=c[entry_date_configuration] value=\"j M-G:i\" "
									.(($site_configuration["entry_date_configuration"]=="j M-G:i") ? "checked" : "").">day Month-hour:minute - 1 Jan 1-12:00<br>
								<input type=radio name=c[entry_date_configuration] value=\"j/n-G:i\" "
									.(($site_configuration["entry_date_configuration"]=="j/n-G:i") ? "checked" : "").">day/month-hour:minute - 1/1-12:00<br>
								<input type=radio name=c[entry_date_configuration] value=\"n/j-G:i\" "
									.(($site_configuration["entry_date_configuration"]=="n/j-G:i") ? "checked" : "").">month/day-hour:minute - 1/1-12:00<br>
								<input type=radio name=c[entry_date_configuration] value=\"M j\" "
									.(($site_configuration["entry_date_configuration"]=="M j") ? "checked" : "").">Month day- Jan 1<br>
								<input type=radio name=c[entry_date_configuration] value=\"j M\" "
									.(($site_configuration["entry_date_configuration"]=="j M") ? "checked" : "").">day Month - 1 Jan<br>
								<input type=radio name=c[entry_date_configuration] value=\"j/n\" "
									.(($site_configuration["entry_date_configuration"]=="j/n") ? "checked" : "").">day/month - 1/1<br>
								<input type=radio name=c[entry_date_configuration] value=\"n/j\" "
									.(($site_configuration["entry_date_configuration"]=="n/j") ? "checked" : "").">month/day - 1/1<br>
								<input type=radio name=c[entry_date_configuration] value=\"n/j/y\" "
									.(($site_configuration["entry_date_configuration"]=="n/j/y") ? "checked" : "").">month/day/year - 1/1/04<br>
								<input type=radio name=c[entry_date_configuration] value=\"n j, Y\" "
									.(($site_configuration["entry_date_configuration"]=="n j, Y") ? "checked" : "").">month day, year - 1 1, 2004<br>
								<input type=radio name=c[entry_date_configuration] value=\"F j, Y\" "
									.(($site_configuration["entry_date_configuration"]=="F j, Y") ? "checked" : "").">month day, year - January 1, 2004<br>
								<input type=radio name=c[entry_date_configuration] value=\"M j, Y\" "
									.(($site_configuration["entry_date_configuration"]=="M j, Y") ? "checked" : "").">month day, year - Jan 1, 2004<br>
								<input type=radio name=c[entry_date_configuration] value=\"d.m.Y H:i\" "
									.(($site_configuration["entry_date_configuration"]=="d.m.Y H:i") ? "checked" : "").">day.month.year hour:minute - 1. 1. 2004 12:00
							</td>
						</tr>";$this->row_count++;

			$this->body .= "
						<tr>
							<td colspan=2 class=large_font_light height=12>&nbsp;</td>
						</tr>
						<tr><td colspan=2><table cellpadding=5 cellspacing=1 border=0 width=100%>
						<tr bgcolor=000066>
							<td colspan=2 class=medium_font_light align=center><b>Optional Site Wide Fields</b></td>
						</tr></td></table></tr>
						<tr>
							<td colspan=2 class=medium_font>
								Optional Site Wide Fields are similar to \"category questions\" in that they can be displayed as additional fields during the
								Listing Process to help sellers better explain their listing. If you turn one of these fields on, it will automatically be
								displayed during the listing process of every category. That is, unless you turn off it's display on an individual category by
								category basis through the \"Categories Menu\". When used, Optional Site Wide Fields become searchable criteria on the Search
								Page. Additionally, you can choose the \"display\" field below which automatically makes the field a \"sortable column\" on all
								category browsing pages that \"use\" the field.<br><br>

								If using an Optional Site Wide Field, you will need to edit the text associated with that field on every page that uses the field.
								For instance, if you \"use\" Optional Field 1 (or name of your choosing) you will need to access the
								appropriate page to change the text for this field.  For a complete list of pages have these fields attached,
								please see the User Manual.  The column and field names can be set up in the text administration of each
								individual page where they appear. Each field is prebuilt to handle up to 256 characters of information.<br><br>

								The column and field names can be set up in the text administration of each individual page	where they appear.
								Each field is prebuilt to handle up to 256 characters of information.<br>
								<b>IMPORTANT: The name of each \"optional field admin name\" is only a tool for you to keep track of how you
								are using the field.  This name will ONLY be visible in your admin.  To change the name of the field
								<i>actually</i> being used,  you must go to the particular page in \"Pages Management\" where the field is used
								and edit it there.</b>
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<table width=100%>
									<tr class=row_color_black>
										<td align=center class=medium_font_light><b>optional field admin name (#)</b>".$this->show_tooltip(24,1)."</td>
										<td align=center class=medium_font_light><b>use</b>".$this->show_tooltip(1,1)."</td>
										<td align=center class=medium_font_light><b>require</b>".$this->show_tooltip(3,1)."</td>
										<td align=center class=medium_font_light><b>display</b>".$this->show_tooltip(4,1)."</td>
										<td align=center class=medium_font_light><b>other box</b>".$this->show_tooltip(19,1)."</td>
										<td align=center class=medium_font_light><b># only</b>".$this->show_tooltip(20,1)."</td>
										<td align=center class=medium_font_light><b>editable</b>".$this->show_tooltip(2,1)."</td>
										<td align=center class=medium_font_light><b>length</b>".$this->show_tooltip(5,1)."</td>
										<td align=center class=medium_font_light><b>type</b>".$this->show_tooltip(21,1)."</td>
									</tr>";

			$this->row_count=0;
			//Optional Fields
			for($i = 1; $i < 21; $i++)
			{
				$this->body .= "	<tr class=".$this->get_row_color().">
										<td valign=top align=left class=medium_font>
											<input type=text size=30 name=c[optional_field_".$i."_name] value=\"".
											$site_configuration['optional_field_'.$i.'_name']."\">($i)
										</td>
										<td valign=top align=center class=medium_font>
											<input id=optional_use type=checkbox name=c[use_optional_field_".$i."] value=1 ".
											(($site_configuration['use_optional_field_'.$i] == 1) ? "checked" : "").">
										</td>
										<td align=center valign=top class=medium_font>
											<input id=optional_require type=checkbox name=c[require_optional_field_".$i."] value=1 ".
											 (($site_configuration['require_optional_field_'.$i] == 1) ? "checked" : "").">
										</td>
										<td align=center valign=top class=medium_font>
											<input id=optional_display type=checkbox name=c[display_optional_field_".$i."] value=1 ".
											(($site_configuration['display_optional_field_'.$i] == 1) ? "checked" : "").">
										</td>
										<td valign=top align=center class=medium_font>
											<input id=optional_other_box type=checkbox name=b[optional_".$i."_other_box] value=1 ".
											(($show_configuration['optional_'.$i.'_other_box'] == 1) ? "checked" : "").">
										</td>
										<td valign=top align=center class=medium_font>
											<input id=optional_number_only type=checkbox name=b[optional_".$i."_number_only] value=1 ".
											(($show_configuration['optional_'.$i.'_number_only'] == 1) ? "checked" : "").">
										</td>
										<td valign=top align=center class=medium_font>
											<input id=optional_editable type=checkbox name=b[optional_".$i."_field_editable] value=1 ".
											(($show_configuration['optional_'.$i.'_field_editable'] == 1) ? "checked" : "").">
										</td>
										<td align=center valign=top class=small_font>
											<input onkeyup=validate(this) type=text name=b[optional_".$i."_length] size=3 maxsize=3 value=".$show_configuration['optional_'.$i.'_length'].">
										</td>
										<td align=center valign=top class=small_font>
											<select name=b[optional_".$i."_field_type]>
												<option value=0 ".(($show_configuration['optional_'.$i.'_field_type'] == 0) ? "selected" : "").">
													blank text box
												</option>";

				$this->sql_query = "select * from ".$this->sell_choices_types_table;
				if ($this->auction_debug) $this->body .= $this->sql_query." is the query<br>\n";
				$types_result = $db->Execute($this->sql_query);
				if (!$types_result)
				{
					if ($this->auction_debug) $this->body .= $db->ErrorMsg()."<br>\n";

					$this->error_message = $this->messages[5501];
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($types_result->RecordCount() > 0)
				{
					while ($show_type = $types_result->FetchRow())
					{
						//show questions as drop down box
						$this->body .= "		<option value=".$show_type['type_id'].
													(($show_type['type_id'] == $show_configuration['optional_'.$i.'_field_type']) ? " selected" : "").">"
													.$show_type['type_name']."
												</option>";
					} //end of while
				}
				$this->body .= "			</select>
										</td>
									</tr>";$this->row_count++;
			}

			$this->body .= "<tr class=row_color_black>
     							<td align=right class=medium_font_light><b>select all:&nbsp;&nbsp;</b></td>
								<td align=center><input id=optional_use_all onclick=\"javascript:check_all(document.fields_to_use,'optional_use');\" type=checkbox></td>
								<td align=center><input id=optional_require_all onclick=\"javascript:check_all(document.fields_to_use,'optional_require');\" type=checkbox></td>
								<td align=center><input id=optional_display_all onclick=\"javascript:check_all(document.fields_to_use,'optional_display');\" type=checkbox></td>
								<td align=center><input id=optional_other_box_all onclick=\"javascript:check_all(document.fields_to_use,'optional_other_box');\" type=checkbox></td>
								<td align=center><input id=optional_number_only_all onclick=\"javascript:check_all(document.fields_to_use,'optional_number_only');\" type=checkbox></td>
								<td align=center><input id=optional_editable_all onclick=\"javascript:check_all(document.fields_to_use,'optional_editable');\" type=checkbox></td>
								<td></td>
								<td></td>
							</tr>";

			if (!$this->admin_demo())
			{
				$this->body .= "<tr>
									<td colspan=9 align=center class=medium_font>
										<input type=submit value=\"Save\" name=submit>
										<input onclick=\"reset()\" type=\"button\" value=\"reset form\">
									</td>
								</tr>";
			}
			$this->body .="</table></form></table>";
			return true;
		}
		else
		{
			if ($this->debug)
			{
				echo $result->RecordCount()." is result count<br>\n";
				echo $site_result->RecordCount()." is $site_result count<br>\n";
				echo $this->sql_query." is the query<BR>\n";
			}
			return false;
		}

		return true;
	} //end of function fields_to_use_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_fields_to_use($db,$config_info,$site_config_info)
	{
		$this->function_name = "update_fields_to_use";
		if ($config_info)
		{
			$show_fields = array(
				"city_length",
				"fax_length",
				"phone_2_length",
				"phone_1_length",
				"zip_length",
				"price_length",
				"title_length",
				"editable_category_specific",
				"textarea_wrap",
				"editable_title_field",
				"editable_description_field",
				"use_price_field",
				"editable_price_field",
				"use_country_field",
				"use_country_field",
				"editable_country_field",
				"use_state_field",
				"editable_state_field",
				"use_city_field",
				"editable_city_field",
				"use_zip_field",
				"editable_zip_field",
				"use_phone_1_option_field",
				"allow_phone_1_override",
				"use_phone_2_option_field",
				"allow_phone_2_override",
				"use_fax_field_option",
				"allow_fax_override",
				"use_email_option_field",
				"use_email_override",
				"publically_expose_email",
				"use_mapping_address_field",
				"use_mapping_city_field",
				"use_mapping_state_field",
				"use_mapping_country_field",
				"use_mapping_zip_field",
				"use_buy_now",
				"editable_buy_now",
				"require_buy_now",
				"editable_bid_start_time_field"
				);

			for($i=1;$i<4;$i++)
			{
				array_push($show_fields,'use_url_link_'.$i);
				array_push($show_fields,'editable_url_link_'.$i);
				array_push($show_fields,'url_link_'.$i.'_length');
			}
			for($i=1;$i<21;$i++)
			{
				array_push($show_fields,'optional_'.$i.'_other_box');
				array_push($show_fields,'optional_'.$i.'_number_only');
				array_push($show_fields,'optional_'.$i.'_field_editable');
				array_push($show_fields,'optional_'.$i.'_length');
				array_push($show_fields,'optional_'.$i.'_field_type');
			}
			$this->sql_query = "update ".$this->ad_configuration_table." set ";
			foreach ($show_fields as $value)
				$this->sql_query .= $value." = \"".($config_info[$value] ? $config_info[$value] : 0)."\", ";
			$this->sql_query = rtrim($this->sql_query, ', ');//strip off comma
			$result = $db->Execute($this->sql_query);
			if ($this->debug) echo "<BR><BR>QUERY - ".$this->sql_query."<BR>";
			if (!$result)
			{
				//echo $this->sql_query."<br>\n";
				return false;
			}
		}
		if ($site_config_info)
		{
			$site_fields = array(
				"display_ad_description_where",
				"display_all_of_description",
				"display_photo_icon",
				"display_ad_title",
				"display_ad_description",
				"display_price",
				"display_browsing_country_field",
				"display_browsing_state_field",
				"display_browsing_city_field",
				"display_browsing_zip_field",
				"display_entry_date",
				"length_of_description",
				"auction_entry_date",
				"classified_time_left",
				"display_business_type",
				"payment_types",
				"payment_types_use",
				"display_time_left",
				"display_number_bids",
				"entry_date_configuration"
				);
			for($i=1;$i<4;$i++)
			{
				array_push($site_fields,'require_url_link_'.$i);
			}
			for($i=1;$i<21;$i++)
			{
				array_push($site_fields,'optional_field_'.$i.'_name');
				array_push($site_fields,'use_optional_field_'.$i);
				array_push($site_fields,'require_optional_field_'.$i);
				array_push($site_fields,'display_optional_field_'.$i);
			}
			$this->sql_query = "update ".$this->site_configuration_table." set ";
			foreach ($site_fields as $value)
				$this->sql_query .= $value." = \"".($site_config_info[$value] ? $site_config_info[$value] : 0)."\", ";
			$this->sql_query = rtrim($this->sql_query, ' ,');//strip off comma
			$result = $db->Execute($this->sql_query);
			if ($this->debug) echo "<BR><BR>QUERY - ".$this->sql_query."<BR>";
			if (!$result)
			{
				echo $this->sql_query."<br>\n";
				return false;
			}
		}
		return true;
	} // end of function update_fields_to_use

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function classified_length_form($db)
	{
		$sql_query = "select * from ".$this->choices_table." where type_of_choice = 1 order by numeric_value";
		$length_result = $db->Execute($sql_query);
		if (!$length_result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=2 method=post>\n";
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center class=row_color1 >\n";
		$this->title = "Listing Setup > Listing Lengths";
		$this->description = "The table below allows you to prepopulate an Listing Length dropdown box that appears to the seller during the Listing process.
		This allows them to choose how long they want the listing to stay active on your site. Note: This box will not be used for those users who fall under
		a Price Plan that is set to charge based upon the length of the listing.";

		$this->body .= "<tr bgcolor=000066>\n\t<td colspan=3 align=center class=medium_font_light>\n\t<b>Current Listing Lengths\n\t</td>\n</tr>\n";
		$this->row_count = 0;
		$this->body .= "<tr class=row_color_black>\n\t<td width=33% align=center class=medium_font_light><b>length in days (numeric only)</b>\n\t</td>\n\t";
		$this->body .= "<td width=33% align=center class=medium_font_light><b>displayed value</b>\n\t</td>\n\t";
		$this->body .= "<td width=33%>\n\t&nbsp;\n\t</td>\n</tr>\n";

		while ($show_lengths = $length_result->FetchRow())
		{
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td width=33% align=center class=medium_font>\n\t".$show_lengths["numeric_value"]."\n\t</td>\n\t";
			$this->body .= "<td width=33% align=center class=medium_font>\n\t".$show_lengths["display_value"]."\n\t</td>\n\t";
			$this->body .= "<td width=33% align=center>\n\t<a href=index.php?a=23&r=2&d=".$show_lengths["choice_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></a>\n\t</span></td>\n</tr>\n";
			$this->row_count++;
		}
		$this->body .= "<tr>\n\t<td align=center width=33%>\n\t<input type=text name=c[value] size=4 maxsize=4>\n\t</td>\n\t";
		$this->body .= "<td width=33% align=center class=medium_font>\n\t<INPUT type=text name=c[display_value]>\n\t</td>\n\t";
		if (!$this->admin_demo())
			$this->body .= "<td width=33% align=center>\n\t<input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;

	} //end of function classified_length_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function add_classified_length($db,$new_length=0)
	{
		if ($new_length)
		{
			if (ereg("[0-9]+", $new_length["value"]))
			{
				$sql_query = "select * from  ".$this->choices_table." where type_of_choice = 1 and numeric_value = ".$new_length["value"];
				$result = $db->Execute($sql_query);
				if (!$result)
				{
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($result->RecordCount() == 0 )
				{
					$sql_query = "insert into ".$this->choices_table."
						(type_of_choice,display_value,numeric_value)
						values
						(1,\"".$new_length["display_value"]."\",".$new_length["value"].")";
					$result = $db->Execute($sql_query);
					if (!$result)
					{
						$this->site_error($db->ErrorMsg());
						return false;
					}
					else
					{
						return true;
					}
				}
				else
				{
					$this->ad_configuration_message = "That value already exists";
					return true;
				}
			}
			else
			{
				$this->ad_configuration_message = "Please only enter numbers";
				return true;
			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_ad_configuration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_classified_length($db,$length_id=0)
	{
		if ($length_id)
		{
			$sql_query = "select * from  ".$this->choices_table." where type_of_choice = 1";
			$result = $db->Execute($sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() > 1)
			{
				$sql_query = "delete from  ".$this->choices_table." where choice_id = ".$length_id;
				$result = $db->Execute($sql_query);
				if (!$result)
				{
					$this->site_error($db->ErrorMsg());
					return false;
				}
				return true;
			}
			else
			{
				$this->ad_configuration_message = "Must have at least one length value at all times.  To delete the current value you
					must add another one.";
				return true;
			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function delete_classified_length

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function file_types_form($db)
	{
		//file type accepted
		$sql_query = "select * from ".$this->file_types_table;
		$type_result = $db->Execute($sql_query);
		if (!$type_result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=3 method=post>\n";
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center class=row_color2 >\n";
		$this->title = "Listing Setup > Allowed Uploads";
		$this->description = "Control the files a user can upload to your site and view through their
			listings with this admin.  Put a check next to the file types you wish to allow.";

		$this->body .= "<tr bgcolor=000066><td class=medium_font_light align=center colspan=5><b>Current Allowed Uploads</b></td></tr>\n\t";
		$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light>\n\t<b>file name</b>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light>\n\t<b>mime type</b>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light>\n\t<b>accepted</b>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light>\n\t<b>icon used</b>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light>\n\t\n\t</td>\n\t";
		$this->body .= "</tr>\n";

		while ($show_types = $type_result->FetchRow())
		{
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t".$show_types["name"]."\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\t".$show_types["mime_type"]."\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\t<input type=radio name=e[".$show_types["file_type_id"]."] value=1  class=medium_font";
			if ($show_types["accept"] == 1)
				$this->body .= " checked";
			$this->body .= ">yes <input type=radio name=e[".$show_types["file_type_id"]."] value=2  class=medium_font";
			if ($show_types["accept"] == 2)
				$this->body .= " checked";
			$this->body .= ">no\n\t</td>\n";
			$this->body .= "<td align=center>";
			if (strlen(trim($show_types["icon_to_use"])) > 0)
				$this->body .= "<img src=".trim($show_types["icon_to_use"]).">";
			else
				$this->body .= "<span class=medium_font><b>no icon</b></span>";
			$this->body .= "</td>";
			$this->body .= "<td align=center width=100><a href=index.php?a=23&r=18&b=".$show_types["file_type_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a></td>";
			$this->body .= "</tr>\n";
		}
		if (!$this->admin_demo())
			$this->body .= "<tr>\n\t<td align=center class=medium_font colspan=5>\n\t<input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=5 align=center>\n\t
			<a href=index.php?a=23&r=17><span class=medium_font><br><br><b>add New File Type</b></span></a>\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;
	} //end of function file_types_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_file_types($db,$type_info=0)
	{
		if ($type_info)
		{
			//file type accepted
			if ((is_array($type_info)) && (count($type_info)))
			{
				reset($type_info);
				foreach ($type_info as $key => $value)
				{
					$sql_query = "update ".$this->file_types_table." set
						accept = ".$value."
						where file_type_id = ".$key;
					$type_result = $db->Execute($sql_query);
					if (!$type_result)
					{
						$this->site_error($db->ErrorMsg());
						return false;
					}
				}
				return true;
			}
			else
				return false;
		}
		else
		{
			return false;
		}

	} //end of function update_file_types

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function new_file_types_form($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"file mime type\", \"This is tag that is passed to the server by the uploading computer to let the server know what kind of file is being uploaded. There are several places to identify this tag. These mime types are constantly changing so if you do not find the one you are looking for in the list perform a search for \\\"mime-types\\\" through an Internet search engine.\"]\n
			Text[2] = [\"icon to use\", \"Many file types can not be displayed within a browser or do not want to because of the size of the file. Provide the url of an icon.\"]\n
			Text[3] = [\"extension of file type\", \"Insert the file extension (ie. gif, jpg,...).\"]\n";

		//".$this->show_tooltip(1,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$this->sql_query = "select * from ".$this->ad_configuration_table;
		//echo $this->sql_query."<br>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			echo $this->sql_query."<br>\n";
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$this->ad_configuration_data = $result->FetchRow();
		}
		else
		{
			return false;
		}
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=17 method=post enctype=multipart/form-data>\n";
		$this->body .= "<input type=hidden name=MAX_FILE_SIZE value=10000000>\n";
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center class=row_color2 >\n";
		$this->title = "Listing Setup > Allowed Uploads > New File Type";
		$this->description = "Insert a new file type of files that will be allowed to upload to your site.  Fill in the type name you wish to give the
			file type, mime-type (by uploading the file that you want to allow or entering the mime-type directly) of the file and
			icon to place as a link to the type of file when viewing the listing display.  <Br><Br>Note:  The browser reports the mime-type of the
			file it is sending to the server.  Different browsers may report the mime-type of the file differently.  You may have to find different
			ways of expressing the same mime-type and enter them manually if your customers report issues.  You can request the file type
			from them and enter it into the form below but their browser may report a different mime-type than yours.";

		$this->body .= "<tr class=row_color2 >\n\t<td width=50% align=right class=medium_font>\n\tfile type name:\n\t</td>\n\t";
		$this->body .= "<td width=50%>\n\t<input type=text name=b[type_name]>\n\t</td>\n\t";

		$this->body .= "<tr class=row_color1 >\n\t<td width=50% align=right class=medium_font>\n\tfile mime type:".$this->show_tooltip(1,1)."</td>\n\t";
		$this->body .= "<td class=medium_font>\n\t<input type=text name=b[mime_type]><br><b>OR</b><Br>upload a file to pull the mime-type from<br>
		<input type=file name=c >\n\t</td>\n\t";

		$this->body .= "<tr class=row_color2 >\n\t<td width=50% align=right class=medium_font>\n\ticon to use:".$this->show_tooltip(2,1)."</td>\n\t";
		$this->body .= "<td class=medium_font>\n\t<input type=text name=b[icon_to_use]>\n\tIf you select a non-image mime type, you MUST select an icon.</td>\n\t";

		$this->body .= "<tr class=row_color1 >\n\t<td width=50% align=right class=medium_font>\n\textension of file type:".$this->show_tooltip(3,1)."</td>\n\t";
		$this->body .= "<td>\n\t<input type=text name=b[extension]>\n\t</td>\n\t";
		if (!$this->admin_demo())
			$this->body .= "<tr>\n\t<td align=center class=medium_font colspan=3>\n\t<input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;
	} // end of function new_file_types_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_new_file_type($db,$file_type_info=0,$post_file=0)
	{
		if ($file_type_info)
		{
			//echo $post_file[c]['size']." is size<bR>\n";
			//echo $post_file[c]['type']." is type<bR>\n";
			//echo $post_file[c]['tmp_name']." is tmp_name<bR>\n";
			//echo $post_file[c]['name']." is name<bR>\n";
			if ($post_file[c]['size'] > 0)
			{
				//get file type info from file
				$mime_type = trim($post_file[c]['type']);
			}
			else
			{
				//get file type info from form
				$mime_type = trim($file_type_info["mime_type"]);
			}
			if (strlen($mime_type) > 0)
			{
				$sql_query = "insert into ".$this->file_types_table."
					(name,mime_type,icon_to_use,accept,extension)
					values
					(\"".$file_type_info["type_name"]."\",\"".$mime_type."\",\"".$file_type_info["icon_to_use"]."\",1,\"".$file_type_info["extension"]."\")";
				$result = $db->Execute($sql_query);
				//echo $sql_query."<bR>\n";
				if (!$result)
				{
					//echo $sql_query."<bR>\n";
					$this->site_error($db->ErrorMsg());
					return false;
				}
				else
					return true;
			}
			else
				echo "<b>a required field was empty!!</b><br>";
				return false;
		}
		else
			return false;

	} // end of function insert_new_file_type

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_file_type($db,$type_id=0)
	{
		if ($type_id)
		{
			$sql_query = "delete from ".$this->file_types_table."
				where file_type_id = ".$type_id;
			$result = $db->Execute($sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
				return true;
		}
		else
			return false;

	} // end of function insert_new_file_type

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function ad_configuration_home($db)
	{
		if ($this->is_class_auctions() || $this->is_auctions())
		{
			$config_title = "Listing Setup";
			$item_name = "listing";
		}
		elseif ($this->is_auctions())
		{
			$config_title = "Auction Setup";
			$item_name = "Auction";
		}
		else
		{
			$config_title = "Ad Setup";
			$item_name = "Ad";
		}

		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"length in characters for description\", \"Set the maximum length in characters that will be allowed to be entered into the description.\"]\n
			Text[2] = [\"send email to user upon successful listing placement\", \"This sends an email to the user when their listing becomes live. After paypal and credit card payment acceptance and when the other forms of payment are approved in the admin.\"]\n
			Text[3] = [\"send email to Admin upon successful listing placement\", \"This sends an email to the Admin when a listing becomes live. After paypal and credit card payment acceptance and when the other forms of payment are approved in the administration.\"]\n
			Text[4] = [\"send email to Admin when manual payment type chosed\", \"This sends an email to the Admin when a listing has been placed but a manual payment type was used. See the unnapproved listings section if email received.\"]\n
			Text[5] = [\"send email to user before listing expiration\", \"This sends an email to the user when their listing is about to expire. Set the number of days prior to expiration to send the email in the dropdown box below. Set the value to 0 to not send the email at all.\"]\n
			Text[6] = [\"send email to user before subscription expiration\", \"This sends an email to the user when their subscription is about to expire. Set the number of days prior to subscription expiration to send the email in the dropdown box below. Set the value to 0 to not send the email at all.\"]\n
			Text[7] = [\"renewal period (if 0 - no renewals)\", \"This setting defines if listing renewals will be accepted and the period that the listing can be renewed from the end of the listing. If this is set to 0 no listing renewals will be accepted. If set to a positive value that will be the number of days before and after listing expiration that the listing can be renewed.\"]\n
			Text[8] = [\"period a listing can be upgraded\", \"This setting determines if and for how many days from the day a listing started a user can place extra feature upgrades on a listing. If this value is set to 0, no upgrades will be allowed on any listing. Upgrades are bolding, better placement, featured listing or the addition of an attention getter. The reason for putting a limit on the number of days to allow upgrades is that the listing is \\\"renewed\\\" when an upgrade is successfully added. This means the listing will be given its chosen duration from the date the upgrade was added.  Example: If a user places a 30 day listing and wants to come back and add an extra feature after 3 days (lets assume you set this limit to 7 days). If the user successfully purchases the the upgrade the extra features will be added to the listing and a new expiration date will be set -- 30 days from the upgrade date which makes this particular listing run 33 days. This was done to let the user take full advantage of the upgrade which is for the full duration they chose initially.\"]\n
			Text[9] = [\"place listings only in terminal categories\", \"Checking yes will only allow users to place their listings in categories that do not have subcategories to them thus making them choose the lowest categories to place their listings. Choosing no will allow users to place listings in any category.\"]\n
			Text[10] = [\"use rich text editor when placing a listing (for description field only)\", \"Checking yes will allow the use of a rich text editor for the description field only. This will allow the client to more easily enter HTML into their description. This will provide quicker, better and easier to use formatting capabilities within the description field for the HTML gifted and for the HTML challenged.  DO NOT USE THIS IN CONJUNCTION WITH 'automatic line breaks on text areas'.  Note that the Aplos Rich Text Editor is provided under the terms of the Lesser GNU Public License.  The source code is available in the AplosRTE folder of the classes directory.\"]\n
			Text[11] = [\"pop up listing display when browsing by category\", \"Checking yes will popup a new window a user clicks on listing to view it.\"]\n
			Text[12] = [\"display category order within place a listing and edit category processes\", \"You can choose to display the categories in alphabetical order while you are choosing a category within the place a listing process or within the edit category process.\"]\n
			Text[13] = [\"use voting system for listings\", \"This setting determines how you use the listings voting system. The voting system allows the user to place a recommend,ok,not recommend vote on an individual listing. According to the setting you place at right the system will:  1 - Allows each unique IP to vote once on each listing.  2 - Allows each registered user to place a vote on each listing.  3 - Allows each registered user and unique IP to place a vote on each listing.  Each vote must come with a rating, title and comment before it is logged. The comments can then be viewed by accessing a link on the listing dispay page.\"]\n
			Text[14] = [\"number of vote comments to display on a page\", \"Set the number of votes to display on a page.\"]\n
			Text[15] = [\"category column choice count:\",\"This determines the number of columns the categories will be displayed in on the choose category page within the place a listing process.\"]\n
			Text[16] = [\"Number of columns to display checkbox category specific questions in:\",\"On the detail display page and the place an ad process the category specific checkboxes will be displayed in columns if this is set above 1.  If set to 0 or 1 they will be displayed in 1 column.  Any other value will display them in that number of columns.  If you display in more than one column remove the html tr tags from the checkbox detail template as the script will automatically insert them when needed\"]\n
			Text[17] = [\"update end times when a listing is upgraded:\",\"When upgrading a listing the end time can be updated as well.  Toggle this to allow the updating or not.\"]\n
			Text[18] = [\"frequency to send expiration email after first:\",\"After the first expiration email is sent another will be sent at the frequency chosen here.\"]\n
			Text[19] = [\"Send email to admin upon expiration of listing or subscription:\",\"Toggle to send an email to the admin when a subscription or listing expires.\"]\n

			Text[20] = [\"allow standard auctions\", \"Selecting yes will allow your users to use the standard auction format for their auction.\"]\n
			Text[21] = [\"allow dutch auctions\", \"Selecting yes will allow your users to use the dutch auction format for their auction.\"]\n
			Text[22] = [\"switch for bid history link\", \"Choose whether to display/not display bid history link when the auction is live or after it has ended.\"]\n
			Text[23] = [\"switch for blacklisting of buyers\", \"Choose whether to maintain the black list of buyers or not.\"]\n
			Text[24] = [\"switch for inviting buyers\", \"Choose whether to maintain the invited list of buyers or not.\"]\n
			Text[25] = [\"display Auction Start Date\", \"Choose whether to display/not display Auction Start Date field. If \\\"no\\\" then Auction Start Date will default to current time.  If \\\"yes\\\" and 'switch for use of Auction End Date' is set to \\\"no\\\" then Duration (defaulted to minimum value set by client) will be added to Auction Start Date or current time (whichever is greater) for calculation of Auction End Date.\"]\n
			Text[26] = [\"display Auction End Date\", \"Choose whether to display/not display Auction End Date field.  If \\\"yes\\\" and the value set by client is less than or equal to current time then Duration (if set to anything other than zero/null which is the default when this switch is \\\"yes\\\") will be added to Auction Start Date or current time (whichever is greater if 'switch for use of Auction Start Date' is set to \\\"yes\\\") to create Auction End Date.  Otherwise, ANY value greater than or equal to current time will OVERRIDE ANY Duration setting.\"]\n
			Text[27] = [\"display buy now before first bid\", \"Choose whether to display buy now until the reserve price is met or to go away after first bid.\"]\n
			Text[28] = [\"switch for editing after auction has begun\", \"Choose whether to allow a user to edit an auction after it has began until the first bid or not to allow editing at all once its began.\"]\n
			Text[29] = [\"switch for viewing before the auction has begun\", \"Choose whether to allow a user to view an auction that has a start time set and before it is starts.\"]\n
			Text[30] = [\"<nobr>extend an auction by ? when a bid is made within ?</nobr>\",\"<ul><li>Set the first pull down to the amount of time to extend an auction.  <BR><b>IMPORTANT: This setting is entirely dependent upon the second pull pown menu.</b></li><li>Set the second pull down to the amount of time before an auction ends that a bidder can extend an auction.  This will happen automatically, if the bid is made within the time period set here.  <BR><b>IMPORTANT: Set the value to 0 to not check and extend.</b></li></ul>\"]\n
			Text[31] = [\"switch for only allow admin to remove a live auction:\", \"Choose whether to allow a user to delete a live auction or only allow the admin to delete a live auction.\"]\n
			";
		//".$this->show_tooltip(20,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$sql_query = "select * from ".$this->ad_configuration_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show_configuration = $result->FetchRow();

			$this->title = "Listing Setup > General Settings";
			$this->description = "Control the specific aspects and features of the ".$item_name."s displayed on your site with
				this admin.  Choose from the links below to perform specific configurations on the ".$item_name."s displayed on your site.";

			$this->row_count = 1;
			$this->body .= "
				<script type=\"text/javascript\">
					function validate(field)
					{
						if (!(field.value>=0 && field.value<=5000))
						{
							alert(\"Must be between 0 and 5000. Values outside this range as well as invalid characters will not be submitted.\")
							field.value=\"\"
							field.focus()
						}
					}
					function check_all(elements,col)
					{
						for(x = 0; x < elements.length; x++)
						{
							if(elements[x].id == col && !elements[x].disabled)
								elements[x].checked=elements[col+'_all'].checked;
							if(elements[x].id == col+'_section' && !elements[x].disabled)
								elements[x].checked=elements[col+'_all'].checked;
						}
					}
				</script>";
			if (!$this->admin_demo())
				$this->body .= "<form action=index.php?a=23&r=8 method=post>";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% align=center class=row_color1>
					<tr class=".$this->get_row_color().">
						<td align=right valign=top class=medium_font>
							<b>description length:</b>".$this->show_tooltip(1,1)."</td>
						<td valign=top class=medium_font>
							<input onkeyup=validate(this) type=text name=b[maximum_description_length] size=5 value=\"".$show_configuration["maximum_description_length"]."\">characters
						</td>
					</tr>";$this->row_count++;

			$sql_query = "select * from ".$this->site_configuration_table;
			$result = $db->Execute($sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_site_configuration = $result->FetchRow();

				$this->body .= "<tr class=".$this->get_row_color()." >\n\t<td align=right valign=top class=medium_font>\n\t<b>send email to user upon successful listing placement:</b>".$this->show_tooltip(2,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<input type=radio name=b[send_successful_placement_email] value=1 ";
				if ($show_site_configuration["send_successful_placement_email"] == 1)
						$this->body .= "checked";
				$this->body .= ">yes<br><input type=radio name=b[send_successful_placement_email] value=0 ";
				if ($show_site_configuration["send_successful_placement_email"] == 0)
						$this->body .= "checked";
				$this->body .= ">no\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>send email to Admin upon successful listing placement:</b>".$this->show_tooltip(3,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<input type=radio name=b[send_admin_placement_email] value=1 ";
				if ($show_site_configuration["send_admin_placement_email"] == 1)
					$this->body .= "checked";
				$this->body .= ">yes<br><input type=radio name=b[send_admin_placement_email] value=0 ";
				if ($show_site_configuration["send_admin_placement_email"] == 0)
					$this->body .= "checked";
				$this->body .= ">no\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>send email to Admin upon listing or subscription expiration:</b>".$this->show_tooltip(19,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<input type=radio name=b[send_admin_end_email] value=1 ";
				if ($show_site_configuration["send_admin_end_email"] == 1)
						$this->body .= "checked";
				$this->body .= ">yes<br><input type=radio name=b[send_admin_end_email] value=0 ";
				if ($show_site_configuration["send_admin_end_email"] == 0)
						$this->body .= "checked";
				$this->body .= ">no\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>send email to Admin when manual payment type chosed:</b>".$this->show_tooltip(4,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<input type=radio name=b[user_set_hold_email] value=1 ";
				if ($show_site_configuration["user_set_hold_email"] == 1)
						$this->body .= "checked";
				$this->body .= ">yes<br><input type=radio name=b[user_set_hold_email] value=0 ";
				if ($show_site_configuration["user_set_hold_email"] == 0)
						$this->body .= "checked";
				$this->body .= ">no\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>send email to user before listing expiration:</b>".$this->show_tooltip(5,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<select name=b[send_ad_expire_email]>\n\t";
				for ($i = 0; $i<61; $i++)
				{
					$this->body .= "<option value=".$i;
					if ($show_site_configuration["send_ad_expire_email"] == $i)
						$this->body .= " selected";
					$this->body .= ">".$i."</option>\n\t\t";
				}
				$this->body .= "</select> days\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>frequency to send expiration email after first:</b>".$this->show_tooltip(18,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<select name=b[send_ad_expire_frequency]>\n\t";
				$this->body .= "<option value=0";
				if ($show_site_configuration["send_ad_expire_frequency"] == 0)
					$this->body .= " selected";
				$this->body .= ">Never</option>\n\t\t";
				$this->body .= "<option value=21600";
				if ($show_site_configuration["send_ad_expire_frequency"] == 21600)
					$this->body .= " selected";
				$this->body .= ">6 hours</option>\n\t\t";
				$this->body .= "<option value=43200";
				if ($show_site_configuration["send_ad_expire_frequency"] == 43200)
					$this->body .= " selected";
				$this->body .= ">12 hours</option>\n\t\t";
				$this->body .= "<option value=86400";
				if ($show_site_configuration["send_ad_expire_frequency"] == 86400)
					$this->body .= " selected";
				$this->body .= ">1 day</option>\n\t\t";
				$this->body .= "<option value=172800";
				if ($show_site_configuration["send_ad_expire_frequency"] == 172800)
					$this->body .= " selected";
				$this->body .= ">2 days</option>\n\t\t";
				$this->body .= "<option value=259200";
				if ($show_site_configuration["send_ad_expire_frequency"] == 259200)
					$this->body .= " selected";
				$this->body .= ">3 days</option>\n\t\t";
				$this->body .= "<option value=604800";
				if ($show_site_configuration["send_ad_expire_frequency"] == 604800)
					$this->body .= " selected";
				$this->body .= ">1 week</option>\n\t\t";
				$this->body .= "<option value=1209600";
				if ($show_site_configuration["send_ad_expire_frequency"] == 1209600)
					$this->body .= " selected";
				$this->body .= ">2 week</option>\n\t\t";
				$this->body .= "<option value=1814400";
				if ($show_site_configuration["send_ad_expire_frequency"] == 1814400)
					$this->body .= " selected";
				$this->body .= ">3 week</option>\n\t\t";
				$this->body .= "<option value=2592000";
				if ($show_site_configuration["send_ad_expire_frequency"] == 2592000)
					$this->body .= " selected";
				$this->body .= ">30 days</option>\n\t\t";
				$this->body .= "</select>\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>send email to user before subscription expiration:</b>".$this->show_tooltip(6,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<select name=b[subscription_expire_period_notice]>\n\t";
				for ($i = 0; $i<31; $i++)
				{
					$this->body .= "<option value=".$i;
					if ($show_site_configuration["subscription_expire_period_notice"] == $i)
						$this->body .= " selected";
					$this->body .= ">".$i."</option>\n\t\t";
				}
				$this->body .= "</select> days\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>renewal period (if 0 - no renewals):</b>".$this->show_tooltip(7,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<select name=b[days_to_renew]>\n\t";
				for ($i = 0; $i<180; $i++)
				{
					$this->body .= "<option value=".$i;
					if ($show_site_configuration["days_to_renew"] == $i)
						$this->body .= " selected";
					$this->body .= ">".$i."</option>\n\t\t";
				}
				$this->body .= "</select> days\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>update end date of listing when upgraded:</b>".$this->show_tooltip(17,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<input type=radio name=b[upgrade_time] value=1 ";
				if ($show_site_configuration["upgrade_time"] == 1)
						$this->body .= "checked";
				$this->body .= ">yes<br><input type=radio name=b[upgrade_time] value=0 ";
				if ($show_site_configuration["upgrade_time"] == 0)
						$this->body .= "checked";
				$this->body .= ">no\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>period a listing can be upgraded:</b>".$this->show_tooltip(8,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<select name=b[days_can_upgrade]>\n\t";
				for ($i = 0; $i<31; $i++)
				{
					$this->body .= "<option value=".$i;
					if ($show_site_configuration["days_can_upgrade"] == $i)
						$this->body .= " selected";
					$this->body .= ">".$i."</option>\n\t\t";
				}
				$this->body .= "</select> days\n\t</td>\n</tr>\n";
				$this->row_count++;

				//place listings in terminal categories only
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t<b>place listings only in terminal categories:</b>".$this->show_tooltip(9,1)."</td>\n\t";
				$this->body .= "<td valign=top width=50% class=medium_font>\n\t
					<input type=radio name=b[place_ads_only_in_terminal_categories] value=1 ";
				if ($show_site_configuration["place_ads_only_in_terminal_categories"] == 1)
					$this->body .= "checked";
				$this->body .= ">yes<br><input type=radio name=b[place_ads_only_in_terminal_categories] value=0 ";
				if ($show_site_configuration["place_ads_only_in_terminal_categories"] == 0)
					$this->body .= "checked";
				$this->body .= ">no\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
					<b>use rich text editor when placing a listing (for description field only):</b>".$this->show_tooltip(10,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<input type=radio name=b[use_rte] value=1 ";
				if ($show_site_configuration["use_rte"] == 1)
						$this->body .= "checked";
				$this->body .= ">yes<br><input type=radio name=b[use_rte] value=0 ";
				if ($show_site_configuration["use_rte"] == 0)
						$this->body .= "checked";
				$this->body .= ">no\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
					<b>pop up listing display when browsing by category:</b>".$this->show_tooltip(11,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<input type=radio name=b[popup_while_browsing] value=1 ";
				if ($show_site_configuration["popup_while_browsing"] == 1)
						$this->body .= "checked";
				$this->body .= ">yes<br><input type=radio name=b[popup_while_browsing] value=0 ";
				if ($show_site_configuration["popup_while_browsing"] == 0)
						$this->body .= "checked";
				$this->body .= ">no\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
					<b>width of popup window in pixels:</b>\n\t\n\t</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<input type=text size=5 name=b[popup_while_browsing_width]
						value=".$show_site_configuration["popup_while_browsing_width"].">\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
					<b>height of popup window in pixels:</b>\n\t\n\t</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<input type=text size=5 name=b[popup_while_browsing_height]
						value=".$show_site_configuration["popup_while_browsing_height"].">\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
					<b>display category order within place a listing and edit category processes:</b>".$this->show_tooltip(12,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<input type=radio name=b[order_choose_category_by_alpha] value=1 ";
				if ($show_site_configuration["order_choose_category_by_alpha"] == 1)
						$this->body .= "checked";
				$this->body .= ">order alphabetically<br><input type=radio name=b[order_choose_category_by_alpha] value=0 ";
				if ($show_site_configuration["order_choose_category_by_alpha"] == 0)
						$this->body .= "checked";
				$this->body .= ">order by display order set within category administration\n\t</td>\n</tr>\n";
				$this->row_count++;

				// number of columns to show in place listing process
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=50% class=medium_font>\n\t<b>number of columns to display categories in place a listing process:</b>".$this->show_tooltip(15,1)."</td>\n\t";
				$this->body .= "<td width=50% valign=top class=medium_font>\n\t<select name=b[sell_category_column_count]>\n\t\t";
				for ($i=1;$i<=5;$i++)
				{
					$this->body .= "<option ";
					if ($i == $show_site_configuration["sell_category_column_count"])
					$this->body .= "selected";
					$this->body .= ">".$i."</option>\n\t\t";
				}
				$this->body .= "</select>\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top width=50% class=medium_font>\n\t
					<b>reset listing Start Date when edit listing details, images or category</b></font><br>
					".$this->small_font_tag."Checking yes will reset the start date of a listing to the current date whenever that listing
					is edited in any way.</td>\n\t";
				$this->body .= "<td valign=top width=50% class=medium_font>
					<input type=radio name=b[edit_reset_date] value=1 ";
				if ($show_site_configuration["edit_reset_date"] == 1)
					$this->body .= "checked";
				$this->body .= ">yes<br><input type=radio name=b[edit_reset_date] value=0 ";
				if ($show_site_configuration["edit_reset_date"] == 0)
					$this->body .= "checked";
				$this->body .= ">no</font>\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>how voting system is used for listings:</b>".$this->show_tooltip(13,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<select name=b[voting_system]>\n\t";
				$this->body .= "<option value=1 ";
				if ($show_site_configuration["voting_system"] == 1) $this->body .= "selected";
				$this->body .= ">IP based discrimination</OPTION>";
				$this->body .= "<option value=2 ";
				if ($show_site_configuration["voting_system"] == 2) $this->body .= "selected";
				$this->body .= ">User based</OPTION>";
				$this->body .= "<option value=3 ";
				if ($show_site_configuration["voting_system"] == 3) $this->body .= "selected";
				$this->body .= ">IP and User based</OPTION>";
				$this->body .= "</select>\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>number of vote comments to display on a page:</b>".$this->show_tooltip(14,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<select name=b[number_of_vote_comments_to_display]>\n\t";
				for ($i = 0; $i<50; $i++)
				{
					$this->body .= "<option value=".$i;
					if ($show_site_configuration["number_of_vote_comments_to_display"] == $i)
						$this->body .= " selected";
					$this->body .= ">".$i."</option>\n\t\t";
				}
				$this->body .= "</select>\n\t</td>\n</tr>\n";
				$this->row_count++;

				// Add multiple checkbox columns dropdown here
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>number of columns to display checkbox category specific questions in:</b></font>".$this->show_tooltip(16,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>\n\t
					<select name=b[checkbox_columns]>\n\t";
				for ($i = 0; $i<6; $i++)
				{
					$this->body .= "<option value=".$i;
					if ($show_site_configuration["checkbox_columns"] == $i)
						$this->body .= " selected";
					$this->body .= ">".$i."</option>\n\t\t";
				}
				$this->body .= "</select> columns</font>\n\t</td>\n</tr>\n";
				$this->row_count++;

				if ($this->is_class_auctions() || $this->is_auctions())
				{
					// Check for price plans having buy now only set
				$this->sql_query = "select buy_now_only from ".$this->price_plan_table;
					$result = $db->Execute($this->sql_query);
					if($result->RecordCount() > 0)
					{
						while($price_plan = $result->FetchRow())
						{
							if($price_plan["buy_now_only"] == 1)
							{
								// If so disable warning
								$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=center colspan=2><b><font color=red>Warning the Dutch Auctions option is disabled because one or more of your price plans
									has the buy now auctions only option set.  To enable Dutch Auctions please disable this in all of your price plans.</font></b></td></tr>";
								break;
							}
						}
					}

					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>allow standard auctions:</b>".$this->show_tooltip(20,1)."</td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t
						<input type=radio name=b[allow_standard] value=1 ";
					if ($show_site_configuration["allow_standard"] == 1)
						$this->body .= "checked";
					$this->body .= ">yes<br><input type=radio name=b[allow_standard] value=0 ";
					if ($show_site_configuration["allow_standard"] == 0)
						$this->body .= "checked";
					$this->body .= ">no</font>\n\t</td>\n</tr>\n";
					$this->row_count++;

					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>allow dutch auctions:</b>".$this->show_tooltip(21,1)."</td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t
						<input type=radio name=b[allow_dutch] value=1 ";
					if ($show_site_configuration["allow_dutch"] == 1)
						$this->body .= "checked";
					$this->body .= ">yes<br><input type=radio name=b[allow_dutch] value=0 ";
					if ($show_site_configuration["allow_dutch"] == 0)
						$this->body .= "checked";
					$this->body .= ">no</font>\n\t</td>\n</tr>\n";
					$this->row_count++;

					//Added for bid history link
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
						<b>switch for bid history link:</b>".$this->show_tooltip(22,1)."</td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t
						<input type=radio name=b[bid_history_link_live] value=1 ";
					if ($show_site_configuration["bid_history_link_live"] == 1)
						$this->body .= "checked ";
					$this->body .= "> When it goes Live<br><input type=radio name=b[bid_history_link_live] value=0 ";
					if ($show_site_configuration["bid_history_link_live"] == 0)
						$this->body .= "checked";
					$this->body .= "> After it ends</font>\n\t</td>\n</tr>\n";
					$this->row_count++;

					//Added for Black list of buyers
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
						<b>switch for blacklisting of buyers:</b>".$this->show_tooltip(23,1)."</td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t
						<input type=radio name=b[black_list_of_buyers] value=1 ";
					if ($show_site_configuration["black_list_of_buyers"] == 1)
						$this->body .= "checked ";
					$this->body .= "> Yes<br><input type=radio name=b[black_list_of_buyers] value=0 ";
					if ($show_site_configuration["black_list_of_buyers"] == 0)
						$this->body .= "checked";
					$this->body .= "> No</font>\n\t</td>\n</tr>\n";
					$this->row_count++;

					//Added for Inviting buyers
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
						<b>switch for inviting buyers:</b>".$this->show_tooltip(24,1)."</td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t
						<input type=radio name=b[invited_list_of_buyers] value=1 ";
					if ($show_site_configuration["invited_list_of_buyers"] == 1)
						$this->body .= "checked ";
					$this->body .= "> Yes<br><input type=radio name=b[invited_list_of_buyers] value=0 ";
					if ($show_site_configuration["invited_list_of_buyers"] == 0)
						$this->body .= "checked";
					$this->body .= "> No</font>\n\t</td>\n</tr>\n";
					$this->row_count++;

					//Make Start times field visible
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
						<b>switch for use of Auction Start Date:</b>".$this->show_tooltip(25,1)."</td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t
						<input type=radio name=b[user_set_auction_start_times] value=1 ";
					if ($show_site_configuration["user_set_auction_start_times"] == 1)
						$this->body .= "checked ";
					$this->body .= ">Yes<br><input type=radio name=b[user_set_auction_start_times] value=0 ";
					if ($show_site_configuration["user_set_auction_start_times"] == 0)
						$this->body .= "checked";
					$this->body .= "> No</font>\n\t</td>\n</tr>\n";
					$this->row_count++;

					//Make End times field visible
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
						<b>switch for use of Auction End Date:</b>".$this->show_tooltip(26,1)."</td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t
						<input type=radio name=b[user_set_auction_end_times] value=1 ";
					if ($show_site_configuration["user_set_auction_end_times"] == 1)
						$this->body .= "checked ";
					$this->body .= ">Yes<br><input type=radio name=b[user_set_auction_end_times] value=0 ";
					if ($show_site_configuration["user_set_auction_end_times"] == 0)
						$this->body .= "checked";
					$this->body .= "> No</font>\n\t</td>\n</tr>\n";
					$this->row_count++;

					//Make Buy now enabled at certain points
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
						<b>switch for buy now choices:</b>".$this->show_tooltip(27,1)."</td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t
						<input type=radio name=b[buy_now_reserve] value=1 ";
					if ($show_site_configuration["buy_now_reserve"] == 1)
						$this->body .= "checked ";
					$this->body .= ">Until Reserve Met<br><input type=radio name=b[buy_now_reserve] value=0 ";
					if ($show_site_configuration["buy_now_reserve"] == 0)
						$this->body .= "checked";
					$this->body .= ">Until first bid</font>\n\t</td>\n</tr>\n";
					$this->row_count++;

					// Allow editing after auction has started until first bid
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
						<b>switch for editing after auction has begun:</b>".$this->show_tooltip(28,1)."</td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t
						<input type=radio name=b[edit_begin] value=1 ";
					if ($show_site_configuration["edit_begin"] == 1)
						$this->body .= "checked ";
					$this->body .= ">Do not allow editing<br><input type=radio name=b[edit_begin] value=0 ";
					if ($show_site_configuration["edit_begin"] == 0)
						$this->body .= "checked";
					$this->body .= ">Edit until first bid</font>\n\t</td>\n</tr>\n";
					$this->row_count++;

					// Show auction before it started
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
						<b>switch for viewing auctions before they begin:</b>".$this->show_tooltip(29,1)."</td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t
						<input type=radio name=b[display_before_start] value=1 ";
					if ($show_site_configuration["display_before_start"] == 1)
						$this->body .= "checked ";
					$this->body .= ">Allow viewing<br><input type=radio name=b[display_before_start] value=0 ";
					if ($show_site_configuration["display_before_start"] == 0)
						$this->body .= "checked";
					$this->body .= ">Do not allow viewing</font>\n\t</td>\n</tr>\n";
					$this->row_count++;

					//Added for auction extension
					$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right valign=top class=medium_font>
								<b>extend an auction by:</b>
							</td>
							<td valign=top class=medium_font>
								<select name=b[auction_extension]>";
					for ($i = 0; $i<=60; $i++)
					{
						$this->body .= "
									<option value=".$i.(($show_site_configuration['auction_extension'] == $i) ? " selected" : "").">".$i."</option>";
					}
					$this->body .= "
								</select> minute(s)
							</td>
						</tr>
						<tr class=".$this->get_row_color().">
							<td align=right valign=top class=medium_font>
								<b>. . . when a bid is made within:".$this->show_tooltip(30,1)."</b>
							</td>
							<td valign=top class=medium_font>
								<select name=b[auction_extension_check]>";
					for ($i = 1; $i<=60; $i++)
					{
						$this->body .= "
									<option value=".$i.(($show_site_configuration['auction_extension_check'] == $i) ? " selected" : "").">".$i."</option>";
					}
					$this->body .= "
								</select> minute(s) of the end of an auction
							</td>
						</tr>";
					$this->row_count++;

					// Only allow admin to delete a live auction
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
						<b>switch to only allow admin to remove a live auction:</b>".$this->show_tooltip(31,1)."</td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t
						<input type=radio name=b[admin_only_removes_auctions] value=1 ";
					if ($show_site_configuration['admin_only_removes_auctions'] == 1)
						$this->body .= "checked ";
					$this->body .= ">Only allow admin to delete a live auction<br><input type=radio name=b[admin_only_removes_auctions] value=0 ";
					if ($show_site_configuration['admin_only_removes_auctions'] == 0)
						$this->body .= "checked";
					$this->body .= ">Allow a user to delete their own live auctions</font>\n\t</td>\n</tr>\n";
					$this->row_count++;
				}
			}
			if (!$this->admin_demo())
			{
				$this->body .= "<tr class=row_color2 >\n\t<td align=center valign=middle colspan=2 class=medium_font>\n\t
					<input type=submit name=submit value=Save>\n\t</td>\n</tr>\n";
			}
			$this->body .= "</table>";
		}
		return true;
	} //end of function ad_configuration_home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_max_lengths($db,$length_info=0)
	{
		if ($length_info)
		{
			$this->sql_query = "update ".$this->ad_configuration_table." set
				maximum_title_length = \"".$length_info["maximum_title_length"]."\",
				maximum_description_length = \"".$length_info["maximum_description_length"]."\"";
			$result = $db->Execute($this->sql_query);
			//echo $sql_query."<br>\n";
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
			{
				$this->sql_query = "update ".$this->site_configuration_table." set
					place_ads_only_in_terminal_categories = \"".$length_info["place_ads_only_in_terminal_categories"]."\",
					send_successful_placement_email = \"".$length_info["send_successful_placement_email"]."\",
					send_admin_placement_email = \"".$length_info["send_admin_placement_email"]."\",
					send_admin_end_email = \"".$length_info["send_admin_end_email"]."\",
					user_set_hold_email = \"".$length_info["user_set_hold_email"]."\",
					send_ad_expire_email = \"".$length_info["send_ad_expire_email"]."\",
					send_ad_expire_frequency = \"".$length_info["send_ad_expire_frequency"]."\",
					subscription_expire_period_notice = \"".$length_info["subscription_expire_period_notice"]."\",
					voting_system = \"".$length_info["voting_system"]."\",
					sell_category_column_count = \"".$length_info["sell_category_column_count"]."\",
					number_of_vote_comments_to_display = \"".$length_info["number_of_vote_comments_to_display"]."\",
					days_can_upgrade = \"".$length_info["days_can_upgrade"]."\",
					popup_while_browsing = \"".$length_info["popup_while_browsing"]."\",
					order_choose_category_by_alpha = \"".$length_info["order_choose_category_by_alpha"]."\",
					popup_while_browsing_width = \"".$length_info["popup_while_browsing_width"]."\",
					popup_while_browsing_height = \"".$length_info["popup_while_browsing_height"]."\",
					edit_reset_date = \"".$length_info["edit_reset_date"]."\",
					use_rte = \"".$length_info["use_rte"]."\",
					checkbox_columns = \"".$length_info["checkbox_columns"]."\",
					upgrade_time = \"".$length_info["upgrade_time"]."\",
					days_to_renew = \"".$length_info["days_to_renew"]."\",

					allow_standard = \"".$length_info["allow_standard"]."\",
					allow_dutch = \"".$length_info["allow_dutch"]."\",
					bid_history_link_live = \"".$length_info["bid_history_link_live"]."\",
					black_list_of_buyers = \"".$length_info["black_list_of_buyers"]."\",
					invited_list_of_buyers = \"".$length_info["invited_list_of_buyers"]."\",
					user_set_auction_start_times = \"".$length_info["user_set_auction_start_times"]."\",
					user_set_auction_end_times = \"".$length_info["user_set_auction_end_times"]."\",
					buy_now_reserve = \"".$length_info["buy_now_reserve"]."\",
					edit_begin = \"".$length_info["edit_begin"]."\",
					display_before_start = \"".$length_info["display_before_start"]."\",
					auction_extension = \"".$length_info["auction_extension"]."\",
					auction_extension_check = \"".$length_info["auction_extension_check"]."\",
					admin_only_removes_auctions = \"".$length_info['admin_only_removes_auctions']."\"
					";
				$update_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$update_result)
				{
					$this->site_error($db->ErrorMsg());
					return false;
				}
				else
				{
					return true;
				}
			}
		}
		else
		{
			return false;
		}
	} //end of function update_max_lengths

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function extra_template_form($db)
	{
		$sql_query = "select * from ".$this->ad_configuration_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show = $result->FetchRow();
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=7 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% align=center class=row_color1 >\n";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=very_large_font_light>\n\t<b>Extra Question-Data Fields Template Configuration</b>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=medium_font_light>\n\tAdd a template displaying
				your category specific data fields using this admin tool.  Add your own template in the space below or leave the textarea blank to
				choose the default template we include with
				application.  This template only controls the placement of label and data fields within the table you provide in this
				box above.  You can control the actual text placed within labels through the categories/subcats facility reached
				through a link at the left.  Please
				look closely at the default template to understand how you should also use the font css tags wrapped around
				each label and data field.  Use the css style tag admin in the <b>site configuration &gt; fonts</b> admin tool to adjust
				css display properties for the listing text and text throughout the site.  \n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color1 ><td align=right class=medium_font>\n\tDefault Listing Extra Display Template</td>";
			$sql_query = "select name,template_id from ".$this->templates_table." order by name";
			$this->body .= "<td valign=top>\n\t<select name=b[user_extra_template]>\n\t\t";
			$category_template_result = $db->Execute($sql_query);
			 if (!$category_template_result)
			 {
				$this->error_message = $this->messages[3501];
				return false;
			 }
			 elseif ($category_template_result->RecordCount() > 0)
			 {
				while ($show_template = $category_template_result->FetchRow())
				{
					$this->body .= "<option value=".$show_template["template_id"];
					if ($show_template["template_id"] == $show["user_extra_template"])
						$this->body .= " selected ";
					$this->body .= ">".$show_template["name"]."</option>\n\t";
				}
			 }
			$this->body .= "</select></td></tr>";
			if (!$this->admin_demo())
			{
				$this->body .= "<tr class=row_color2 >\n\t<td valign=top colspan=2 class=medium_font>\n\t
					<input type=submit name=submit  value=\"Save\">\n\t</td>\n</tr>\n";
			}

			$this->body .= "<tr class=row_color2 >\n\t<td valign=top colspan=2 class=medium_font>\n\t
				Below is the list of template fields possible for the listing html table.  Place the fields below within the table
				where you would like them to appear.  Next to each field is an explanation of what that field represents.
				The data will be replaced on the fly by this Application.\n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;EXTRA_QUESTION_NAME&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe name attached to the data field.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;EXTRA_QUESTION_VALUE&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe data value saved in this data field.\n\t</td>\n</tr>\n";

			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}
		else
		{
			return false;
		}

	} //end of function extra_template_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_extra_template($db,$template_info=0)
	{
		if ($template_info)
		{
			$sql_query = "update ".$this->ad_configuration_table." set
				user_extra_template = \"".$template_info["user_extra_template"]."\"";
			$result = $db->Execute($sql_query);
			//echo $sql_query."<br>\n";
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	} //end of function update_extra_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function extra_template_checkbox_form($db)
	{
		$sql_query = "select * from ".$this->ad_configuration_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show = $result->FetchRow();
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=9 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% align=center class=row_color1 >\n";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=very_large_font_light>\n\t<b>Extra Question-Checkbox Data Fields Template Configuration</b>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=medium_font_light>\n\tAdd a template displaying
				your category specific checkbox data fields using this admin tool.  Add your own template in the space below or leave the textarea blank to
				choose the default template we include with
				application.  This template only controls the placement of label and data fields within the table you provide in this
				box above.  You can control the actual text placed within labels through the categories/subcats facility reached
				through a link at the left.  Please
				look closely at the default template to understand how you should also use the font css tags wrapped around
				each label and data field.  Use the css style tag admin in the <b>site configuration &gt; fonts</b> admin tool to adjust
				css display properties for the listing text and text throughout the site.  \n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color1 ><td align=right class=medium_font>\n\tDefault Listing Checkbox Display Template</td>";
			$sql_query = "select name,template_id from ".$this->templates_table." order by name";
			$this->body .= "<td valign=top>\n\t<select name=b[user_checkbox_template]>\n\t\t";
			$category_template_result = $db->Execute($sql_query);
			 if (!$category_template_result)
			 {
				$this->error_message = $this->messages[3501];
				return false;
			 }
			 elseif ($category_template_result->RecordCount() > 0)
			 {
				while ($show_template = $category_template_result->FetchRow())
				{
					$this->body .= "<option value=".$show_template["template_id"];
					if ($show_template["template_id"] == $show["user_checkbox_template"])
						$this->body .= " selected ";
					$this->body .= ">".$show_template["name"]."</option>\n\t";
				}
			 }
			$this->body .= "</select></td></tr>";
			if (!$this->admin_demo())
			{
				$this->body .= "<tr class=row_color2 >\n\t<td valign=top colspan=2 class=medium_font>\n\t
					<input type=submit name=submit  value=\"Save\">\n\t</td>\n</tr>\n";
			}

			$this->body .= "<tr class=row_color2 >\n\t<td valign=top colspan=2 class=medium_font>\n\t
				Below is the list of template fields possible for the listing html table.  Place the fields below within the table
				where you would like them to appear.  Next to each field is an explanation of what that field represents.
				The data will be replaced on the fly by this application.\n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;EXTRA_CHECKBOX_NAME&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe name attached to the data field.\n\t</td>\n</tr>\n";

			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}
		else
		{
			return false;
		}

	} //end of function extra_template_checkbox_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_extra_checkbox_template($db,$template_info=0)
	{
		if ($template_info)
		{
			$sql_query = "update ".$this->ad_configuration_table." set
				user_checkbox_template = \"".$template_info["user_checkbox_template"]."\"";
			$result = $db->Execute($sql_query);
			//echo $sql_query."<br>\n";
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	} //end of function update_extra_checkbox_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function full_images_template_form($db)
	{
		$sql_query = "select * from ".$this->ad_configuration_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show = $result->FetchRow();
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=15 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% align=center class=row_color1 >\n";
			$this->title = "Listing Setup > Full Size Image Template";
			$this->description = "This template determines the look of each individual full size images look.  This will be displayed on the
				page where all of the full size images are displayed together along with the text that was entered with it.  The title will
				appear at the top of the page and the description will appear at the bottom.  Return to listing link will be at both top and bottom.
				The send seller a message, notify friend, add to favorites and sellers other listing links will appear at the bottom of the page.
				The template you are creating will be placed in it's own table row and the images stacked one on top of the other.";

			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				your category specific data field template:\n\t</td>\n\t";
			$sql_query = "select name,template_id from ".$this->templates_table." order by name";
			$this->body .= "<td valign=top>\n\t<select name=b[user_template]>\n\t\t";
			$category_template_result = $db->Execute($sql_query);
			 if (!$category_template_result)
			 {
				$this->error_message = $this->messages[3501];
				return false;
			 }
			 elseif ($category_template_result->RecordCount() > 0)
			 {
				while ($show_template = $category_template_result->FetchRow())
				{
					$this->body .= "<option value=".$show_template["template_id"];
					if ($show_template["template_id"] == $show["full_size_image_template"])
						$this->body .= " selected ";
					$this->body .= ">".$show_template["name"]."</option>\n\t";
				}
			 }
			$this->body .= "</select></td></tr>";
			if (!$this->admin_demo())
			{
				$this->body .= "<tr class=row_color2 >\n\t<td valign=top colspan=2 class=medium_font>\n\t
					<input type=submit name=submit  value=\"Save\">\n\t</td>\n</tr>\n";
			}

			$this->body .= "<tr class=row_color2 >\n\t<td valign=top colspan=2 class=medium_font>\n\t
				Below is the list of template fields possible for the listing html table.  Place the fields below within the table
				where you would like them to appear.  Next to each field is an explanation of what that field represents.
				The data will be replaced on the fly by the this application.\n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;FULL_SIZE_IMAGE&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe full size image.\n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;FULL_SIZE_TEXT&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe text attached to the full size image.\n\t</td>\n</tr>\n";

			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}
		else
		{
			return false;
		}

	} //end of function full_images_template_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_full_images_template($db,$template_info=0)
	{
		if ($template_info)
		{
			$sql_query = "update ".$this->ad_configuration_table." set
				full_size_image_template = \"".$template_info["user_template"]."\"";
			$result = $db->Execute($sql_query);
			//echo $sql_query."<br>\n";
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	} //end of function update_full_images_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function image_resize_table($db)
	{
		//get listing configuration data
		$this->sql_query = "select * from ".$this->ad_configuration_table;
		//echo $this->sql_query."<br>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//echo $this->sql_query."<br>\n";
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$this->ad_configuration_data = $result->FetchRow();
		}
		else
		{
			return false;
		}
		//echo $this->ad_configuration_data["maximum_image_width"]." is the width<br>\n";
		//echo $this->ad_configuration_data["maximum_image_height"]." is the height<br>\n";
		$tables = array($this->images_table,$this->images_urls_table);
		//$tables = array($this->images_urls_table);
		reset($tables);
		foreach ($tables as $value)
		{
			$this->sql_query = "select original_image_width,original_image_height, image_id from ".$value;
			//echo $this->sql_query."<br>\n";
			$image_result = $db->Execute($this->sql_query);
			if (!$image_result)
			{
				//echo $this->sql_query."<br>\n";
				return false;
			}
			elseif ($image_result->RecordCount() > 0)
			{
				while ($show_images = $image_result->FetchRow())
				{
					if (($show_images["original_image_width"] > $this->ad_configuration_data["maximum_image_width"]) && ($show_images["original_image_height"] > $this->ad_configuration_data["maximum_image_height"]))
					{
						$imageprop = ($this->ad_configuration_data["maximum_image_width"] * 100) / $show_images["original_image_width"];
						$imagevsize = ($show_images["original_image_height"] * $imageprop) / 100 ;
						$final_image_width = $this->ad_configuration_data["maximum_image_width"];
						$final_image_height = ceil($imagevsize);

						if ($final_image_height > $this->ad_configuration_data["maximum_image_height"])
						{
							$imageprop = ($this->ad_configuration_data["maximum_image_height"] * 100) / $show_images["original_image_height"];
							$imagehsize = ($show_images["original_image_width"] * $imageprop) / 100 ;
							$final_image_height = $this->ad_configuration_data["maximum_image_height"];
							$final_image_width = ceil($imagehsize);
						}
					}
					elseif ($show_images["original_image_width"] > $this->ad_configuration_data["maximum_image_width"])
					{
						$imageprop = ($this->ad_configuration_data["maximum_image_width"] * 100) / $show_images["original_image_width"];
						$imagevsize = ($show_images["original_image_height"] * $imageprop) / 100 ;
						$final_image_width = $this->ad_configuration_data["maximum_image_width"];
						$final_image_height = ceil($imagevsize);
					}
					elseif ($show_images["original_image_height"] > $this->ad_configuration_data["maximum_image_height"])
					{
						$imageprop = ($this->ad_configuration_data["maximum_image_height"] * 100) / $show_images["original_image_height"];
						$imagehsize = ($show_images["original_image_width"] * $imageprop) / 100 ;
						$final_image_height = $this->ad_configuration_data["maximum_image_height"];
						$final_image_width = ceil($imagehsize);
					}
					else
					{
						$final_image_width = $show_images["original_image_width"];
						$final_image_height = $show_images["original_image_height"];
					}

					$this->sql_query = "update ".$this->images_table." set
						image_width = ".$final_image_width.",
						image_height = ".$final_image_height."
						where image_id = ".$show_images["image_id"];
					//echo $this->sql_query."<br>\n";
					$update_result = $db->Execute($this->sql_query);
					if (!$update_result)
					{
						//echo $this->sql_query."<br>\n";
						return false;
					}
				}
			}
			else
			{
				return false;
			}
		}


	} //end of function image_resize_table

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function ad_extras_form($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"featured listings\", \"Checking \\\"yes\\\" will allow users to place one thumbnail image with their title that links to their listing on the featured listings page of the site.\"]\n
			Text[2] = [\"featured listings level 2\", \"Checking \\\"yes\\\" will allow users to place their listings where featured listing level 2 listings appear.\"]\n
			Text[3] = [\"featured listings level 3\", \"Checking \\\"yes\\\" will allow users to place their listings where featured listing level 3 listings appear.\"]\n
			Text[4] = [\"featured listings level 4\", \"Checking \\\"yes\\\" will allow users to place their listings where featured listing level 4 listings appear.\"]\n
			Text[5] = [\"featured listings level 5\", \"Checking \\\"yes\\\" will allow users to place their listings where featured listing level 5 listings appear.\"]\n
			Text[6] = [\"number of featured picture listings columns to display on featured listing pics page\", \"This determines number of columns that will be displayed on the featured listings page that only displays the featured listing pics. The pictures will be displayed at the size they would be displayed within the display of the listing itself.\"]\n
			Text[7] = [\"number of featured picture listings to display on one page\", \"This determines the number of listings that will be displayed on any single page returned. Make sure that you keep in mind the number of columns you set above. If this number is not evenly divided by the above figure the last row of the results would not be filled in completely if there were at least this many or more featured listings in the result.\"]\n
			Text[8] = [\"bolding\", \"Checking \\\"yes\\\" will allow the users placing listings to bold the title of their listings when their listings are displayed while browsing the listings. The price for this added feature will be set by the pricing plan attached to that user.\"]\n
			Text[9] = [\"better placement\", \"Checking \\\"yes\\\" will allow the users to put their listings at the top of the browsed categories when users are browsing. The \\\"better placed\\\" listings comes first in any browsed return from a category. The better placed listings themselves are arranged by date the listing was placed (from most recent to oldest).\"]\n
			Text[10] = [\"attention getters\", \"Checking \\\"yes\\\" will allow the users to purchase an attention getter to place in the front of their description within the listing browsing pages.\"]\n";

		//".$this->show_tooltip(10,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$this->sql_query = "select * from ".$this->site_configuration_table;
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show_configuration = $result->FetchRow();

			$sql_query = "select * from ".$this->ad_configuration_table;
			$result = $db->Execute($sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_ad_configuration = $result->FetchRow();
			}

			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=6 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center class=row_color2 >\n";
			$this->title = "Listing Setup > Listing Extras";
			$this->description = "Control which features your users can take advantage
				of for their listings through your site.  The price charged for each feature will be controlled by the price plan attached to that user.  Note that these simply activate
				the category specific features.  You will still have to decide which ones will go with each category.";

			//featured
			$this->body .= "<tr class=row_color2 >\n\t<td align=right valign=top width=60% class=medium_font>\n\tfeatured listings:".$this->show_tooltip(1,1)."</td>\n\t";
			$this->body .= "<td valign=top width=60% class=medium_font>\n\t
				<input type=radio name=b[use_featured_feature] value=1 ";
			if ($show_configuration["use_featured_feature"] == 1)
				$this->body .= "checked";
			$this->body .= ">yes<br><input type=radio name=b[use_featured_feature] value=0 ";
			if ($show_configuration["use_featured_feature"] == 0)
				$this->body .= "checked";
			$this->body .= ">no\n\t</td>\n</tr>\n";

			//featured level 2
			$this->body .= "<tr class=row_color1 >\n\t<td align=right valign=top width=60% class=medium_font>\n\tfeatured listings level 2:".$this->show_tooltip(2,1)."</td>\n\t";
			$this->body .= "<td valign=top width=60% class=medium_font>\n\t
				<input type=radio name=b[use_featured_feature_2] value=1 ";
			if ($show_configuration["use_featured_feature_2"] == 1)
				$this->body .= "checked";
			$this->body .= ">yes<br><input type=radio name=b[use_featured_feature_2] value=0 ";
			if ($show_configuration["use_featured_feature_2"] == 0)
				$this->body .= "checked";
			$this->body .= ">no\n\t</td>\n</tr>\n";

			//featured level 3
			$this->body .= "<tr class=row_color2 >\n\t<td align=right valign=top width=60% class=medium_font>\n\tfeatured listings level 3:".$this->show_tooltip(3,1)."</td>\n\t";
			$this->body .= "<td valign=top width=60% class=medium_font>\n\t
				<input type=radio name=b[use_featured_feature_3] value=1 ";
			if ($show_configuration["use_featured_feature_3"] == 1)
				$this->body .= "checked";
			$this->body .= ">yes<br><input type=radio name=b[use_featured_feature_3] value=0 ";
			if ($show_configuration["use_featured_feature_3"] == 0)
				$this->body .= "checked";
			$this->body .= ">no\n\t</td>\n</tr>\n";

			//featured level 4
			$this->body .= "<tr class=row_color1 >\n\t<td align=right valign=top width=60% class=medium_font>\n\tfeatured listings level 4:".$this->show_tooltip(4,1)."</td>\n\t";
			$this->body .= "<td valign=top width=60% class=medium_font>\n\t
				<input type=radio name=b[use_featured_feature_4] value=1 ";
			if ($show_configuration["use_featured_feature_4"] == 1)
				$this->body .= "checked";
			$this->body .= ">yes<br><input type=radio name=b[use_featured_feature_4] value=0 ";
			if ($show_configuration["use_featured_feature_4"] == 0)
				$this->body .= "checked";
			$this->body .= ">no\n\t</td>\n</tr>\n";

			//featured level 5
			$this->body .= "<tr class=row_color2 >\n\t<td align=right valign=top width=60% class=medium_font>\n\tfeatured listings level 5:".$this->show_tooltip(5,1)."</td>\n\t";
			$this->body .= "<td valign=top width=60% class=medium_font>\n\t
				<input type=radio name=b[use_featured_feature_5] value=1 ";
			if ($show_configuration["use_featured_feature_5"] == 1)
				$this->body .= "checked";
			$this->body .= ">yes<br><input type=radio name=b[use_featured_feature_5] value=0 ";
			if ($show_configuration["use_featured_feature_5"] == 0)
				$this->body .= "checked";
			$this->body .= ">no\n\t</td>\n</tr>\n";

			//featured listing count
			$this->body .= "<tr class=row_color1 >\n\t<td align=right valign=top width=60% class=medium_font>\n\tnumber of
				featured picture listings columns to display on featured listing pics page:".$this->show_tooltip(6,1)."<br>
				<span class=small_font>You currently have these sizes for those images:<br>
				maximum width: ".$show_ad_configuration["maximum_image_width"]." pixels<Br>
				maximum height: ".$show_ad_configuration["maximum_image_height"]." pixels</span></td>\n\t";
			$this->body .= "<td valign=top width=60% class=medium_font>\n\t
				<select name=b[featured_pic_ad_column_count]>";
			for ($i=1;$i <= 5; $i++)
			{
				$this->body .= "<option ";
				if ($show_configuration["featured_pic_ad_column_count"] == $i)
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";

			//featured listing count
			$this->body .= "<tr class=row_color2 >\n\t<td align=right valign=top width=60% class=medium_font>\n\tnumber of
				featured picture listings to display on one page:".$this->show_tooltip(7,1)."</td>\n\t";
			$this->body .= "<td valign=top width=60% class=medium_font>\n\t
				<select name=b[featured_ad_page_count]>";
			for ($i=1;$i <= 100; $i++)
			{
				$this->body .= "<option ";
				if ($show_configuration["featured_ad_page_count"] == $i)
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";

			//bolding
			$this->body .= "<tr class=row_color1 >\n\t<td align=right valign=top class=medium_font>\n\tbolding:".$this->show_tooltip(8,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t
				<input type=radio name=b[use_bolding_feature] value=1 ";
			if ($show_configuration["use_bolding_feature"] == 1)
				$this->body .= "checked";
			$this->body .= ">yes<br><input type=radio name=b[use_bolding_feature] value=0 ";
			if ($show_configuration["use_bolding_feature"] == 0)
				$this->body .= "checked";
			$this->body .= ">no\n\t</td>\n</tr>\n";

			//better placement
			$this->body .= "<tr class=row_color2 >\n\t<td align=right valign=top class=medium_font>\n\tbetter placement:".$this->show_tooltip(9,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t
				<input type=radio name=b[use_better_placement_feature] value=1 ";
			if ($show_configuration["use_better_placement_feature"] == 1)
				$this->body .= "checked";
			$this->body .= ">yes<br><input type=radio name=b[use_better_placement_feature] value=0 ";
			if ($show_configuration["use_better_placement_feature"] == 0)
				$this->body .= "checked";
			$this->body .= ">no\n\t</td>\n</tr>\n";

			//attention getters
			$this->body .= "<tr class=row_color1 >\n\t<td align=right valign=top class=medium_font>\n\tattention getters:".$this->show_tooltip(10,1)."</td>\n\t";
			$this->body .= "<td valign=top class=medium_font>\n\t
				<input type=radio name=b[use_attention_getters] value=1 ";
			if ($show_configuration["use_attention_getters"] == 1)
				$this->body .= "checked";
			$this->body .= ">yes<br><input type=radio name=b[use_attention_getters] value=0 ";
			if ($show_configuration["use_attention_getters"] == 0)
				$this->body .= "checked";
			$this->body .= ">no\n\t</td>\n</tr>\n";
			if (!$this->admin_demo())
				$this->body .= "<tr>\n\t<td align=center class=medium_font colspan=3>\n\t<input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}
		else
		{
			return false;
		}
	} //end of function ad_extras_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_ad_extras($db,$extras_info=0)
	{
		if ($extras_info)
		{
			$sql_query = "update ".$this->site_configuration_table." set
				use_better_placement_feature = ".$extras_info["use_better_placement_feature"].",
				use_bolding_feature = ".$extras_info["use_bolding_feature"].",
				featured_ad_page_count = ".$extras_info["featured_ad_page_count"].",
				featured_pic_ad_column_count = ".$extras_info["featured_pic_ad_column_count"].",
				use_featured_feature = ".$extras_info["use_featured_feature"].",
				use_featured_feature_2 = ".$extras_info["use_featured_feature_2"].",
				use_featured_feature_3 = ".$extras_info["use_featured_feature_3"].",
				use_featured_feature_4 = ".$extras_info["use_featured_feature_4"].",
				use_featured_feature_5 = ".$extras_info["use_featured_feature_5"].",
				use_attention_getters = ".$extras_info["use_attention_getters"];
			$site_configuration_table = $db->Execute($sql_query);
			//echo $sql_query."<br>\n";
			if (!$site_configuration_table)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}

	} //end of function update_ad_extras

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function attention_getters_form($db)
	{
		$sql_query = "select * from ".$this->choices_table." where type_of_choice = 10 order by display_order";
		$attention_getters_result = $db->Execute($sql_query);
		if (!$attention_getters_result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=66&z=1 method=post>\n";
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center class=row_color1 >\n";
		$this->title = "Listing Setup > Attention Getters";
		$this->description = "Below is the list of attention getters
			that can be placed within the description field of an individual users listing.  You must turn on the use of attention getters before they
			become usable and then set the price to use them on a per price plan basis within the price plan administration.";

		$this->body .= "<tr bgcolor=000066><td class=medium_font_light align=center colspan=4><b>Current Attention Getters</b></td></tr>\n\t";
		$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light>\n\t<b>name</b>\n\t</td>
			<td class=medium_font_light>\n\t<b>url of image</b>\n\t</td>
			<td class=medium_font_light>\n\t<b>image</b>\n\t</td>
			<td class=medium_font_light>\n\t&nbsp;\n\t</td>\n</tr>\n";
		$this->row_count = 0;
		if ($attention_getters_result->RecordCount() > 0)
		{
			while ($show_attention_getters = $attention_getters_result->FetchRow())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t".$show_attention_getters["display_value"]."\n\t</td>\n\t
					<td class=medium_font>\n\t".$show_attention_getters["value"]."\n\t</td>";
				$this->body .= "<td class=medium_font>\n\t<img src=../".$show_attention_getters["value"].">\n\t</td>";
				$this->body .= "<td width=100 align=center>\n\t<a href=index.php?a=66&z=2&c=".$show_attention_getters["choice_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></a>\n\t</span></td>\n</tr>\n";
				$this->row_count++;
			}
		}
		else
		{
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td colspan=4>\n\t<span class=medium_font>There are currently no attention getters.  Add them through the fields
				below</span>\n\t</td>\n</tr>\n";
			$this->row_count++;
		}
		if (!$this->admin_demo())
		{
			$this->body .= "<tr class=".$this->get_row_color()."><td><input type=text name=b[attention_getter_name]></td>\n\t
				<td colspan=2><input type=text name=b[attention_getter_url] size=50></td>\n\t
				<td><input type=submit name=submit value=\"Save\"></td>\n<tr>\n";
		}
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;

	} //end of function attention_getters_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_attention_getter($db,$info=0)
	{
		if ($info)
		{
			$sql_query = "insert into ".$this->choices_table."
				(type_of_choice,display_value,value)
				values
				(10,\"".$info["attention_getter_name"]."\",\"".$info["attention_getter_url"]."\")";
			$insert_result = $db->Execute($sql_query);
			if (!$insert_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function insert_attention_getter

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_attention_getter($db,$attention_getter_id=0)
	{
		if ($attention_getter_id)
		{
			$sql_query = "delete from  ".$this->choices_table." where choice_id = ".$attention_getter_id;
			$delete_result = $db->Execute($sql_query);
			if (!$delete_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function delete_attention_getter

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function flyer_template_form($db)
	{
		$sql_query = "select * from ".$this->ad_configuration_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show = $result->FetchRow();
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=10 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% align=center class=row_color1 >\n";
			$this->title = "Listing Setup > Flyers Setup";

			//Add a sales flyer template
			//displaying the information you select in it through the form below.  Add your template in the space below using the
			//	field replacement labels listed below.  This template only controls the placement of data fields within the table you provide
			//	in the box below.  All data placed within the template is pulled from the users and listing information.<br><br>
			$this->description = "Your sellers have the ability to print out a Flyer for each of their active listings through their \"My Account\" pages.
			By default, the software will select the first image in their listing to use as the image displayed in their Flyer. However, you can also
			set up additional image choices for the seller to choose from if they do not want to use their own image, or if they do not have any images
			to upload with their listing. Use the form below to enter new images for the user to choose from.";

			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=2 class=medium_font_light align=center>\n\t<b>Current Image Choices for Seller</b>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td colspan=2>\n\t";
			$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
			$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light><b>Image Title (displayed to seller)</b></td><td class=medium_font_light><b>Image location</b></td></tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td align=left class=medium_font>\n\tFirst Image in Listing</td><td align=left class=medium_font>\n\t[ within listing ]</td>";
			$sql_query = "select * from ".$this->choices_table." where type_of_choice = 13";
			$choices_result = $db->Execute($sql_query);
			if (!$choices_result)
			{
				return false;
			}
			elseif ($choices_result->RecordCount() >0 )
			{
				while ($show_choice = $choices_result->FetchRow())
				{
					$this->body .= "<tr class=row_color2>\n\t<td align=left class=medium_font>\n\t";
					$this->body .= $show_choice["display_value"]."</td>\n\t";
					$this->body .= "<td align=left class=medium_font>\n\t".$show_choice["value"]." <a href=index.php?a=23&r=12&b=".$show_choice["choice_id"]."&c=13><img src=admin_images/btn_admin_delete.gif alt=delete border=0>\n\t</td>\n</tr>\n";
				}
			}
			//echo "<tr class=row_color1 >\n\t<td colspan=2 class=medium_font>\n\tAdd a new image choice\n\t</td>\n</tr>\n";
			//echo "<tr class=row_color2 >\n\t<td class=medium_font>Image title</td><td class=medium_font>Image location</td></tr>\n";
			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=2 class=medium_font_light>\n\t<b>Add a New Image Choice</b>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td class=medium_font_light><b>Image Title</b></td><td class=medium_font_light><b>Image location (URL)</b></td></tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td align=left class=medium_font>\n\t<input type=text name=b[new_image_name] size=30></td><td align=left class=medium_font>\n\t<input type=text name=b[new_image_value] size=60></td></tr>\n";
			if (!$this->admin_demo())
				$this->body .= "<tr class=row_color1 >\n\t<td colspan=2 class=medium_font align=center>\n\t<input type=submit value=\"Save\">\n\t</td>\n</tr>\n";
			$this->body .= "\n\t</td>\n</tr>\n</table>";
			//echo "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
			//	your flyer template\n\t</td>\n\t";
			//echo "<td class=medium_font>\n\t<textarea name=b[flyer_template] rows=15 cols=50>".stripslashes($show["flyer_template"])."</textarea>\n\t</td>\n</tr>\n";
//
			//echo "<tr class=row_color2 >\n\t<td valign=top colspan=2 class=medium_font align=center>\n\t
			//	<input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color2 >\n\t<td valign=top colspan=2 class=medium_font>\n\t
				Displayed below are the available tags that may be be placed within the Flyer Template.  Simply insert the tag names into the template
				wherever you want each tag's data to display.\n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;IMAGE&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tPlacement of image chosen for flyer.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;TITLE&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe title of the listing.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;ADDRESS&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe address of the user placing listing.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;CITY&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe city of the user placing listing.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;STATE&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe state of the user placing listing.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;ZIP&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe zip of the user placing listing.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;PRICE&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe price of the listing item.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;PHONE_1&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tphone number 1\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;PHONE_2&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tphone number 2\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_1&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 1 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_2&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 2 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_3&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 3 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_4&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 4 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_5&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 5 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_6&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 6 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_7&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 7 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_8&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 8 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_9&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 9 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_10&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 10 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_11&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 11 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_12&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 12 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_13&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 13 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_14&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 14 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_15&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 15 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_16&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 16 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_17&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 17 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_18&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 18 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_19&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 19 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_20&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 20 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;CLASSIFIED_ID&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe listing id data.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;DESCRIPTION&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tDescription data for this listing.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;CONTACT&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tName of user who placed the listing.\n\t</td>\n</tr>\n";

			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}
		else
		{
			return false;
		}

	} //end of function flyer_template_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_flyer($db,$template_info=0)
	{
		if ($template_info)
		{
			if (strlen($template_info["new_image_name"]) > 0 )
			{
				//enter new image
				$this->sql_query = "insert into ".$this->choices_table."
					(type_of_choice,display_value,value)
					values
					(13,\"".$template_info["new_image_name"]."\",\"".$template_info["new_image_value"]."\")";
				$result = $db->Execute($this->sql_query);
				//echo $sql_query."<br>\n";
				if (!$result)
				{
					return false;
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	} //end of function update_flyer

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function sign_template_form($db)
	{
		$sql_query = "select * from ".$this->ad_configuration_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show = $result->FetchRow();
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=11 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% align=center class=row_color1 >\n";
			$this->title = "Listing Setup > Signs Setup";

			//."Add a sales flyer template
			//displaying the information you select in it through the form below.  Add your template in the space below using the
			//	field replacement labels listed below.  This template only controls the placement of label and data fields within the table you provide in the
			//	box below.  All data placed within the template is pulled from the users and listing information.<br><br>
			$this->description = "Your sellers have the ability to print out a Sign for each of their active listings through their \"My Account\" pages.
			By default, the software will select the first image in their listing to use as the image displayed in their Sign. However, you can also
			set up additional image choices for the seller to choose from if they do not want to use their own image, or if they do not have any images
			to upload with their listing. Use the form below to enter new images for the user to choose from.";

			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=2 class=medium_font_light align=center>\n\t<b>Current Image Choices for Seller</b>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td colspan=2>\n\t";
			$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
			$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light><b>Image Title (displayed to seller)</b></td><td class=medium_font_light><b>Image location</b></td></tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td class=medium_font>First Image in Listing</td><td class=medium_font>[ within listing ]</td>";
			$sql_query = "select * from ".$this->choices_table." where type_of_choice = 14";
			$choices_result = $db->Execute($sql_query);
			if (!$choices_result)
			{
				return false;
			}
			elseif ($choices_result->RecordCount() > 0 )
			{
				while ($show_choice = $choices_result->FetchRow())
				{
					$this->body .= "<tr class=row_color2 >\n\t<td align=left class=medium_font>\n\t";
					$this->body .= $show_choice["display_value"]."</td>\n\t";
					$this->body .= "<td class=medium_font align=left>\n\t".$show_choice["value"]." <a href=index.php?a=23&r=12&b=".$show_choice["choice_id"]."&c=14><img src=admin_images/btn_admin_delete.gif alt=delete border=0></a>\n\t</td>\n</tr>\n";
				}
			}
			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=2 class=medium_font_light>\n\t<b>Add a New Image Choice</b>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td class=medium_font_light><b>Image Title</b></td><td class=medium_font_light><b>Image location (URL)</b></td></tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td class=medium_font><input type=text name=b[new_image_name] size=30></td><td class=medium_font><input type=text name=b[new_image_value] size=60></td></tr>\n";
			if (!$this->admin_demo())
				$this->body .= "<tr class=row_color1 >\n\t<td colspan=2 align=center class=medium_font>\n\t<input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
			$this->body .= "\n\t</td>\n</tr>\n</table>";



			//echo "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
			//	your sign template\n\t</td>\n\t";
			//echo "<td class=medium_font>\n\t<textarea name=b[sign_template] rows=15 cols=50>".stripslashes(urldecode($show["sign_template"]))."</textarea>\n\t</td>\n</tr>\n";
//
			//echo "<tr class=row_color2 >\n\t<td valign=top colspan=2 class=medium_font>\n\t
			//	<input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
//
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top colspan=2 class=medium_font>\n\t
				Displayed below are the available tags that may be be placed within the Flyer Template.  Simply insert the tag names into the template
				wherever you want each tag's data to display.\n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;IMAGE&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tPlacement of image chosen for sign.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;TITLE&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe title of the listing.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;ADDRESS&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe address of the user placing the listing.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;CITY&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe city of the user placing the listing.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;STATE&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe state of the user placing the listing.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;ZIP&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe zip of the user placing the listing.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;PRICE&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe price of the listing.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;PHONE_1&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tphone number 1\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;PHONE_2&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tphone number 2\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_1&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 1 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_2&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 2 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_3&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 3 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_4&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 4 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_5&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 5 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_6&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 6 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_7&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 7 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_8&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 8 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_9&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 9 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_10&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 10 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_11&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 11 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_12&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 12 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_13&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 13 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_14&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 14 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_15&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 15 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_16&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 16 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_17&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 17 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_18&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 18 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_19&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 19 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;OPTIONAL_FIELD_20&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tthe optional field 20 if you use it\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;CLASSIFIED_ID&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tThe listing id data.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;DESCRIPTION&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tDescription data for this listing.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;CONTACT&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tName of user who placed the listing.\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}
		else
		{
			return false;
		}

	} //end of function sign_template_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_sign($db,$template_info=0)
	{
		if ($template_info)
		{
			if (strlen($template_info["new_image_name"]) > 0 )
			{
				//enter new image
				$this->sql_query = "insert into ".$this->choices_table."
					(type_of_choice,display_value,value)
					values
					(14,\"".$template_info["new_image_name"]."\",\"".$template_info["new_image_value"]."\")";
				$result = $db->Execute($this->sql_query);
				//echo $sql_query."<br>\n";
				if (!$result)
				{
					return false;
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	} //end of function update_flyer

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_template_image($db,$choice_id=0)
	{
		if ($choice_id)
		{
			$sql_query = "delete from ".$this->choices_table."
				where choice_id = ".$choice_id;
			$result = $db->Execute($sql_query);
			//echo $sql_query."<br>\n";
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	} //end of function update_flyer

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function currency_types_form($db)
	{
		//file type accepted
		$sql_query = "select * from ".$this->currency_types_table;
		$type_result = $db->Execute($sql_query);
		if (!$type_result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=14 method=post>\n";
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center class=row_color2 >\n";
		$this->title = "Listing Setup > Currency Types";
		$this->description = "The table below allows you to prepopulate a currency dropdown box that appears to the seller
		during the Listing process. If there are no currency types entered into this form there will be no currency type displayed.
		Please note that the currency in this dropdown box is simply for the user to choose which currency they are willing to
		accept from a willing buyer. This has NOTHING to do with the fees that you are charging the user to place their listing
		on your site.";

		$this->body .= "<tr bgcolor=000066><td class=medium_font_light align=center colspan=5><b>Current Currency Types</b></td></tr>\n\t";
		$this->body .= "<tr class=row_color_black>\n\t
			<td class=medium_font_light>\n\t<b>Currency Name</b>\n\t</td>\n\t
			<td class=medium_font_light>\n\t<b>Precurrency Symbol</b>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light>\n\t<b>PostCurrency Symbol</b>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light align=center>\n\t<b>display order</b>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light>\n\t&nbsp;\n\t</td>\n\t";
		$this->body .= "</tr>\n";

		while ($show_types = $type_result->FetchRow())
		{
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t".$show_types["type_name"]."\n\t</td>\n\t";
			$this->body .= "<td class=medium_font align=center>\n\t".$show_types["precurrency"]."\n\t</td>\n\t";
			$this->body .= "<td class=medium_font align=center>\n\t".$show_types["postcurrency"]."\n\t</td>\n\t";
			$this->body .= "<td class=medium_font align=center>\n\t".$show_types["display_order"]."\n\t</td>\n\t";
			$this->body .= "<td align=center width=100>\n\t<a href=index.php?a=23&r=14&z=".$show_types["type_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></a>\n\t</span></td>\n</tr>\n";
		}
		$this->body .= "<tr>\n\t
			<td class=medium_font_light>\n\t<input type=text name=b[type_name]>\n\t</td>\n\t
			<td class=medium_font_light>\n\t<input type=text name=b[precurrency]>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light>\n\t<input type=text name=b[postcurrency]>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font align=center>\n\t<select name=b[display_order]>\n\t\t";
			for ($i=1;$i<101;$i++)
			{
				$this->body .= "<option>".$i."</option>";
			}
			$this->body .= "</select>\n\t</td>\n\t";
		if (!$this->admin_demo())
			$this->body .= "<td align=center class=medium_font>\n\t<input type=submit name=submit value=\"Save\">\n\t</td>\n\t";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;
	} //end of function currency_types_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_currency_type($db,$type_id=0)
	{
		if ($type_id)
		{
			$sql_query = "delete from ".$this->currency_types_table."
				where type_id = ".$type_id;
			$type_result = $db->Execute($sql_query);
			if (!$type_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}

	} //end of function update_file_types

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_currency_type($db,$type_info=0)
	{
		if ($type_info)
		{
			$sql_query = "insert into ".$this->currency_types_table."
				(type_name,precurrency,postcurrency,display_order)
				values
				(\"".$type_info["type_name"]."\",\"".$type_info["precurrency"]."\",\"".$type_info["postcurrency"]."\",\"".$type_info["display_order"]."\")";
			$type_result = $db->Execute($sql_query);
			if (!$type_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}

	} //end of function update_file_types

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%


	function print_friendly_ad_template_form($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"details template\", \"This template displays the details of the listing for this page. You have the ability to insert the various tags displayed further down on this page within the Tag Verification Tool for this template.\"]\n";
		//".$this->show_tooltip(5,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$this->sql_query = "select * from ".$this->templates_table." order by name";
		$templates_result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$templates_result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}
		elseif ($templates_result->RecordCount() > 0)
		{
			$page = $this->get_page($db,69);

			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=16 method=post>\n";
			$this->body .= "<table cellpadding=2 cellspacing=0 border=0 width=100% class=row_color1>\n";
			$this->title = "Pages Management";
			$this->description = "The template(s) indicated below will be used to create your Page. Please review this software's
				User Manual for a pictorial reference of the ".$page["name"]."'s unique layout";

			$this->sql_query = "select * from ".$this->pages_languages_table;
			$language_result = $db->Execute($this->sql_query);
			$row_color = 0;
			while($show_languages = $language_result->FetchRow())
			{
				if($row_color % 2)
					$css_tag = "row_color1";
				else
					$css_tag = "row_color2";

				$this->sql_query = "select * from ".$this->templates_table." order by name";
				$templates_result = $db->Execute($this->sql_query);

				$page_template = $this->get_page_language_template($db,69,$show_languages["language_id"]);

				$this->body .= "<tr class=".$css_tag."><td align=right class=medium_font width=40%>Page Name:</td>\n\t";
				$this->body .= "<td align=left class=medium_font>".$page["name"]."\n\t</td></tr>\n";

				$this->body .= "<tr class=".$css_tag."><td align=right class=medium_font width=40%>Language:</td>\n\t";
				$this->body .= "<td class=medium_font>".$this->get_language_name($db,$show_languages["language_id"])."\n\t</td></tr>\n";

				$row_color++;
			}

			$this->sql_query = "select ad_detail_print_friendly_template,auction_detail_print_friendly_template from ". $this->ad_configuration_table;
			$result = $db->Execute($this->sql_query);
			if(!$result)
				return false;
			else
				$misc_templates = $result->FetchRow();
			if($this->is_class_auctions() || $this->is_classifieds())
			{
				$this->body .= "<tr class=row_color1 ><td align=right class=medium_font>\n\tAd Details Template:".$this->show_tooltip(1,1)."</td>";
				$sql_query = "select name,template_id, template_code from ".$this->templates_table." order by name";
				$this->body .= "<td valign=top>\n\t<select name=b[ad_detail_print_friendly_template]>\n\t\t";
				$templates_result = $db->Execute($sql_query);
				if (!$templates_result)
				{
					$this->error_message = $this->messages[3501];
					return false;
				}
				elseif ($templates_result->RecordCount() > 0)
				{
					while ($show_template = $templates_result->FetchRow())
					{
						$this->body .= "<option value=\"".$show_template["template_id"]."\"";
						if ($show_template["template_id"] == $misc_templates["ad_detail_print_friendly_template"])
						{
							$this->body .= " selected";
							$ad_detail = $show_template["name"];
							$ad_detail_code = $show_template["template_code"];
						}
						$this->body .= ">".$show_template["name"]."</option>\n\t";
					}
				}
				$this->body .= "</select></td></tr>";
			}

			if($this->is_class_auctions() || $this->is_auctions())
			{
				//
				//auctions
				//
				$this->body .= "<tr class=row_color2 ><td align=right class=medium_font>\n\tAuction Details Template:".$this->show_tooltip(1,1)."</td>";
				$sql_query = "select name,template_id, template_code from ".$this->templates_table." order by name";
				$this->body .= "<td valign=top>\n\t<select name=b[auction_detail_print_friendly_template]>\n\t\t";
				$templates_result = $db->Execute($sql_query);
				if (!$templates_result)
				{
					$this->error_message = $this->messages[3501];
					return false;
				}
				elseif ($templates_result->RecordCount() > 0)
				{
					while ($show_template = $templates_result->FetchRow())
					{
						$this->body .= "<option value=\"".$show_template["template_id"]."\"";
						if ($show_template["template_id"] == $misc_templates["auction_detail_print_friendly_template"])
						{
							$this->body .= " selected";
							$auction_detail = $show_template["name"];
							$auction_detail_code = $show_template["template_code"];
						}
						$this->body .= ">".$show_template["name"]."</option>\n\t";
					}
				}
				$this->body .= "</select></td></tr>";
			}
			// Display Submit button
			if (!$this->admin_demo()) $this->body .= "<tr><td align=center colspan=2 class=medium_font><input type=submit name=submit value=\"Save\"></td></tr>";
			$this->body .= "<tr>\n\t<td colspan=2><a href=index.php?a=44&z=3&b=69><span class=medium_font><b>back to ".$page["name"]."</b></span></a></td>\n</tr>\n";
			$this->body .= "</form>\n";

			$this->body .= "</table>";
			$this->body .= "<table>";

			// Tag verification tools
			$this->body .= "<tr><td colspan=3 class=row_color_red></td></tr>";

			$this->body .= "<tr class=".$css_tag."><td class=row_color_red colspan=3 align=center><font class=large_font_light>Tag Verification Tool</font></td></tr>\n\t";
			$this->body .= "<tr class=".$css_tag."><td class=medium_font colspan=3>The Tag Verification Tool helps you to verify the existence of template tags
				(item within brackets) that can be used within each of the templates you have chosen above. When these tags
				are used within their respective template, the tag will display information by pulling it directly from
				the database. In most cases, these tags will simply display a \"label\" name that you have defined, and
				a \"value\" name that the seller defines when placing their listing. The system automatically searches the
				templates you have assigned at the top of this page and returns a result for each tag. The search result
				will either be <font color=#33dd33 size=2 face=arial,helvetica><b>found</b></font> or
				<font color=#bb3333 size=2 face=arial,helvetica><b>not found</b></font>. If there is a tag that displays
				a <font color=#bb3333 size=2 face=arial,helvetica><b>not found</b></font> result, but you want it to
				show up within the ".$page["name"].", simply insert that tag (item within brackets) into its appropriate
				template. IMPORTANT: When placing these tags in the html of your template, you must also go to the
				LISTING SETUP > FIELDS TO USE page of this admin and set each of them to \"use\".</td></tr>\n\t";

			$this->body .= "<tr bgcolor=000066><td align=center><font class=medium_font_light align=center><b>Available Tags:</b></font></td>\n\t";
			$this->body .= "<td align=center><font class=medium_font_light><b>Ad Details<br>Template</b></font></td>";
			$this->body .= "<td align=center><font class=medium_font_light><b>Auction Details<br>Template</b></font></td>\n\t";
			$this->body .= "</tr>\n\t";

			$data = array(	"CATEGORY_TREE"	=>	"The current category as well as links to parent categories this listing is in.<bR><b>not used on affiliate side</b>",
							"TITLE"	=>	"The title of the listing.",
							"CLASSIFIED_ID_LABEL"	=>	"Label text for the listing id.",
							"CLASSIFIED_ID"	=>	"The listing id data.",
							"VIEWED_COUNT_LABEL"	=>	"Label text for the count of the number of times the listing has been viewed.",
							"VIEWED_COUNT"	=>	"The data for the number of times the listing has been viewed.",
							"SELLER_LABEL"	=>	"Label text for the sellers username field.",
							"SELLER"	=>	"Sellers username data.",
							"MEMBER_SINCE"	=>	"This is the date the seller first registered.",
							"DATE_STARTED_LABEL"	=>	"Label text for the date the listing was started.",
							"DATE_STARTED"	=>	"Data for the date the listing was started.",
							"CITY_LABEL"	=>	"Label text for the city.",
							"CITY_DATA"	=>	"City data for this listing.",
							"STATE_LABEL"	=>	"Label text for the state.",
							"STATE_DATA"	=>	"State data for this listing.",
							"COUNTRY_LABEL"	=>	"Label text for the country.",
							"COUNTRY_DATA"	=>	"Country data for this listing.",
							"ZIP_LABEL"	=>	"Label text for the zip.",
							"ZIP_DATA"	=>	"Zip data for this listing.",
							"PRICE_LABEL"	=>	"Label text for the price.",
							"PRICE"	=>	"Price and currency information for this listing.",
							"PUBLIC_EMAIL_LABEL"	=>	"Label text for the publically exposed email (if shown).",
							"PUBLIC_EMAIL"	=>	"Publically exposed email data for this listing (if shown).",
							"PHONE_LABEL"	=>	"Label text for the phone (if shown).",
							"PHONE_DATA"	=>	"Phone data for this listing (if shown).",
							"PHONE2_LABEL"	=>	"Label text for the phone 2 (if shown).",
							"PHONE2_DATA"	=>	"Phone 2 data for this listing (if shown).",
							"FAX_LABEL"	=>	"Label text for the fax label (if shown).",
							"FAX_DATA"	=>	"Fax data for this ad (if shown).",
							"URL_LINK_1"	=>	"This is the link text allowing the user to link to the url within the data field.  The text (html,image) of the link is determined within the listing display page text administration.  The link the client inserts into the field while placing an image will determine where the link goes to.",
							"URL_LINK_2"	=>	"This is the link text allowing the user to link to the url within the data field.  The text (html,image) of the link is determined within the listing display page text administration.  The link the client inserts into the field while placing an image will determine where the link goes to.",
							"URL_LINK_3"	=>	"This is the link text allowing the user to link to the url within the data field.  The text (html,image) of the link is determined within the listing display page text administration.  The link the client inserts into the field while placing an image will determine where the link goes to.",
							"DESCRIPTION_LABEL"	=>	"Label text for the description.",
							"DESCRIPTION"	=>	"Description data for this listing.",
							"EXTRA_QUESTION_BLOCK"	=>	"Where the specific block of category specific questions will be placed.  You can configure the extra question block in this same section under template questions.",
							"EXTRA_CHECKBOX_BLOCK"	=>	"Where the specific block of category specific checkbox questions will be placed.  You can configure the extra question block in this same section under template questions.",
							"IMAGE_BLOCK"	=>	"The images for this listing.  This is the block of all images attached to this listing.  You can configure the number of columns the images should be and the size of the images through the admin tool in <b>listing configuration &gt; images configure</b>.",
							"LEAD_PICTURE"	=>	"Placing this tag will display the first image attached to the listing by itself within the listing display template.",
							"NOTIFY_FRIEND_LINK"	=>	"This is the link allowing the client to send a notification to a friend about the current listing.",
							"MESSAGE_TO_SELLER_LINK"	=>	"This is the link allowing the client to send a message to the seller of the listing.",
							"FAVORITES_LINK"	=>	"This is the link text allowing the user to add the current listing to their favorites list in their user home page.<br><b>not used on affiliate side</b>",
							"SELLERS_OTHER_ADS_LINK"	=>	"This is the link text allowing the user to view the current sellers other listings.<br><b>not used on affiliate side</b>",
							"SHOW_AD_VOTE_COMMENTS_LINK"	=>	"This is the link text allowing the user to view the votes and comments attached to the current listing.<br><b>not used on affiliate side</b>",
							"VOTE_ON_AD_LINK"	=>	"This is the link text allowing the user to vote and leave comments about the current listing.<br><b>not used on affiliate side</b>",
							"FULL_IMAGES_LINK"	=>	"This is the link text allowing the user to view all images at their full size on the same page.",
							"PRINT_FRIENDLY_LINK"	=>	"This is the link text allowing the user to view the listing details page in a page that is more	print friendly.",
							"SPONSORED_BY"	=>	"This is where the sponsored by html is placed within sellers listings where that sellers group has \"sponsered by\" html has been placed.  If none of your groups use the sponsored by html fields this tag does not need to be placed within the listing display template.",
							"PREVIOUS_AD_LINK"	=>	"Link that will display in the listing allowing the user to see the previous listing within the category.",
							"NEXT_AD_LINK"	=>	"Link that will display in the listing allowing the user to see the next listing within the category.",
							"RETURN_TO_LIST_LINK"	=>	"Return user to affiliated listing list - <br><b>only used on affiliate side</b>",
							"MAPPING_LINK"	=>	"Mapquest link to create a map to the location entered for this listing.",
							);
			for($i = 1; $i < 21; $i++)
			{
				$data["OPTIONAL_FIELD_".$i."_LABEL"] = "";
				$data["OPTIONAL_FIELD_".$i] = "";
			}
			$this->display_check($data, $ad_detail_code, $auction_detail_code);

			if($this->is_class_auctions() || $this->is_auctions())
			{
				//AUCTION SPECIFIC TAGS
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=3 class=medium_font_light align=center>\n\t<b>Auction Specific Tags</b></td></tr>\n\t";

				$data = array(	"BID_HISTORY_LINK"	=>	"This is the link text allowing the user to view the bid history of the current auction.  You can turn off and on the visibility of this link while the auction is live within the admin.  The link will be visible when the auction ends",
								"PAYMENT_OPTIONS_LABEL"	=>	"This is the lavle for the payment options the seller will accept for this auction.</b></span>",
								"RESERVE"	=>	"<span class=medium_font>\n\tThis is the reserve price of the item. </span>",
								"HIGH_BIDDER_LABEL"	=>	"<span class=medium_font>\n\tLabel text for the high bidder.</span>",
								"HIGH_BIDDER"	=>	"<span class=medium_font>\n\tThis is the high bidder's user id.</span>",
								"WINNING_DUTCH_BIDDERS_LABEL"	=>	"<span class=medium_font>\n\tLabel text for the winning bidder on a dutch auction.</span>",
								"WINNING_DUTCH_BIDDERS"	=>	"This is the winning bidder's user id on a dutch auction.",
								"NUM_BIDS"	=>	"This is the number of bids on the item.",
								"NUM_BIDS_LABEL"	=>	"Label text for the number of bids.",
								"QUANTITY_LABEL"	=>	"Label for the item quantity.",
								"QUANTITY"	=>	"This is the quantity of items.",
								"AUCTION_TYPE_LABEL"	=>	"Label text for the auction type.",
								"AUCTION_TYPE_DATA"	=>	"This is the auction type.",
								"AUCTION_TYPE_HELP"	=>	"Help link for the auction types.",
								"BUY_NOW_LABEL"	=>	"Label text for buy now.",
								"BUY_NOW_DATA"	=>	"This is the buy now data.",
								"BUY_NOW_LINK"	=>	"This is the link to buy now.",
								"DATE_ENDED_LABEL"	=>	"Label text for date ended.",
								"DATE_ENDED"	=>	"This is the date ended and appears after the auction has been closed.",
								"TIME_REMAINING_LABEL"	=>	"Label text for time left.",
								"TIME_REMAINING"	=>	"This is the time left in the current auction.",
								"MINIMUM_LABEL"	=>	"Label text for minimum bid.",
								"MINIMUM_BID"	=>	"This is the minimum bid.",
								"STARTING_LABEL"	=>	"Label text for the starting bid.",
								"STARTING_BID"	=>	"This is the starting bid.",
								"SELLER_RATING_LABEL"	=>	"Label text for the seller rating.",
								"SELLER_RATING"	=>	"This is the seller rating.",
								"FEEDBACK_LINK"	=>	"This is the link to the feedback page.",
								"SELLER_NUMBER_RATES_LABEL"	=>	"Label text for the seller's feedback score.",
								"SELLER_NUMBER_RATES"	=>	"This is the seller's feedback score.",
								"SELLER_RATING_SCALE_EXPLANATION"	=>	"Explanations of seller rating scale.",
								"BID_START_DATE_LABEL"	=>	"This displays label for the date the auction will start if there is a date set for this auction.",
								"BID_START_DATE"	=>	"This displays the date the auction will start if there is a date set for this auction.",
								"MAKE_BID_LINK"	=>	"This displays the link to make a bid on the auction.  It is only displayed when the auction is live.",
								);
				$this->display_check($data, $ad_detail_code, $auction_detail_code);
			}

			//REGISTRATION TAGS
			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=3 class=medium_font_light align=center>\n\t<b>Registration Information Tags</b></td></tr>\n\t";
			$data = array(	"SELLER_FIRST_NAME"	=>	"Sellers first name pulled from sellers registration data.",
							"SELLER_LAST_NAME"	=>	"Sellers last name pulled from sellers registration data.",
							"SELLER_URL"	=>	"Sellers url pulled from sellers registration data.",
							"SELLER_ADDRESS"	=>	"Sellers address (line 1 and 2) pulled from sellers registration data.",
							"SELLER_CITY"	=>	"Sellers city pulled from sellers registration data.",
							"SELLER_STATE"	=>	"Sellers state pulled from sellers registration data.",
							"SELLER_COUNTRY"	=>	"Sellers country pulled from sellers registration data.",
							"SELLER_ZIP"	=>	"Sellers zip pulled from sellers registration data.",
							"SELLER_PHONE"	=>	"Sellers phone pulled from sellers registration data.",
							"SELLER_PHONE2"	=>	"Sellers phone2 pulled from sellers registration data.",
							"SELLER_FAX"	=>	"Sellers fax pulled from sellers registration data.",
							"SELLER_COMPANY_NAME"	=>	"Sellers company name pulled from sellers registration data."
							);

			for($i = 1; $i < 11; $i++)
			{
				$data["SELLER_OPTIONAL_".$i] = "Sellers optional ".$i." pulled from sellers registration data.";
			}

			$this->display_check($data, $ad_detail_code, $auction_detail_code);

			$this->body .= "</table>\n";
			return true;
		}
		else
		{
			return false;
		}
	} //end of function print_friendly_ad_template_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_print_friendly_ad_template($db,$template_info=0)
	{
		if ($template_info)
		{
			$this->sql_query = "update ".$this->ad_configuration_table." set
				ad_detail_print_friendly_template = \"".$template_info["ad_detail_print_friendly_template"]."\"";
			if($this->is_class_auctions())
				$this->sql_query .= ", auction_detail_print_friendly_template = \"".$template_info["auction_detail_print_friendly_template"]."\"";
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	} //end of function update_print_friendly_ad_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function picture_popup_template_form($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"popup image extra height\", \"This is the extra height in pixels you can add to the total height of the popup box containing your listing image.  Click on the \\\"Listing Image Configuration Management\\\" link below to modify the \\\"maximum width/height of images displayed on full-sized and pop-up image pages\\\".\"]\n
			Text[2] = [\"popup image extra width\", \"This is the extra width in pixels you can add to the total width of the popup box containing your listing image.  Click on the \\\"Listing Image Configuration Management\\\" link below to modify the \\\"maximum width/height of images displayed on full-sized and pop-up image pages\\\".\"]\n";

		//".$this->show_tooltip(1,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$sql_query = "select * from ".$this->ad_configuration_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show = $result->FetchRow();
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=19 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% align=center class=row_color1 >\n";
			$this->title = "Listing Setup > Popup Template";
			$this->description = "Add a template to
				the display of the popup box containing the full image from a listing using this admin tool.  Choose the template
				you wish to use from the dropdown box below.  You must insert the image tag listed below within your template
				to show the script where you wish to place the image.  Also set the extra space vertically and horizontally around the
				image you need to display your template properly.";

			$this->body .= "<tr class=row_color1 ><td align=right class=medium_font>\n\tpopup image template:</td>";
			$sql_query = "select name,template_id from ".$this->templates_table." order by name";
			$this->body .= "<td valign=top>\n\t<select name=b[popup_image_template_id]>\n\t\t";
			$category_template_result = $db->Execute($sql_query);
			 if (!$category_template_result)
			 {
				$this->error_message = $this->messages[3501];
				return false;
			 }
			 elseif ($category_template_result->RecordCount() > 0)
			 {
				while ($show_template = $category_template_result->FetchRow())
				{
					$this->body .= "<option value=".$show_template["template_id"];
					if ($show_template["template_id"] == $show["popup_image_template_id"])
						$this->body .= " selected ";
					$this->body .= ">".$show_template["name"]."</option>\n\t";
				}
			 }
			$this->body .= "</select></td></tr>";

			$this->body .= "<tr class=row_color2 ><td align=right class=medium_font>\n\tpopup image extra height:".$this->show_tooltip(1,1)."</td>";
			$this->body .= "<td valign=top>\n\t<select name=b[popup_image_extra_height]>\n\t\t";
			for ($i=0;$i < 101;$i++)
			{
				$this->body .= "<option value=".$i;
				if ($i == $show["popup_image_extra_height"])
					$this->body .= " selected ";
				$this->body .= ">".$i."</option>\n\t";
			}
			$this->body .= "</select></td></tr>";

			$this->body .= "<tr class=row_color2 ><td align=right class=medium_font>\n\tpopup image extra width:".$this->show_tooltip(2,1)."</td>";
			$this->body .= "<td valign=top>\n\t<select name=b[popup_image_extra_width]>\n\t\t";
			for ($i=0;$i < 101;$i++)
			{
				$this->body .= "<option value=".$i;
				if ($i == $show["popup_image_extra_width"])
					$this->body .= " selected ";
				$this->body .= ">".$i."</option>\n\t";
			}
			$this->body .= "</select></td></tr>";
			if (!$this->admin_demo())
			{
				$this->body .= "<tr class=row_color2 >\n\t<td valign=top colspan=2 class=medium_font>\n\t
					<input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
			}

			$this->body .= "<tr>\n\t<td colspan=2><a href=index.php?a=23&r=1 class=medium_font>Listing Image Configuration Management</a></td>\n</tr>\n";

			$this->body .= "<tr class=row_color_red >\n\t<td valign=top colspan=2 class=medium_font_light>\n\t
				Below are the template fields needed to be placed within your template.  Place the field below within the html
				template where you would like the popup image to appear and the links to the next and previous images for the
				specific listing referenced.\n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;DISPLAY_IMAGE&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tPlace this tag within your template where you wish the image displayed to be.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;PREVIOUS_IMAGE_LINK&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tPlace this tag within you template where you
				want the previous image link to appear.  If there is no previous image the link will be replaced
				with a space.\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1 >\n\t<td valign=top align=right class=medium_font>\n\t
				&lt;&lt;NEXT_IMAGE_LINK&gt;&gt;</td>\n\t";
			$this->body .= "<td class=medium_font>\n\tPlace this tag within you template where you
				want the next image link to appear.  If there is no previous image the link will be replaced
				with a space.\n\t</td>\n</tr>\n";

			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}
		else
		{
			return false;
		}

	} //end of function picture_popup_template_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_picture_popup_template($db,$template_info=0)
	{
		if ($template_info)
		{
			$sql_query = "update ".$this->ad_configuration_table." set
				popup_image_template_id = \"".$template_info["popup_image_template_id"]."\",
				popup_image_extra_width = \"".$template_info["popup_image_extra_width"]."\",
				popup_image_extra_height = \"".$template_info["popup_image_extra_height"]."\"";
			$result = $db->Execute($sql_query);
			//echo $sql_query."<br>\n";
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	} //end of function update_picture_popup_template

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function increments_form($db)
	{
		$this->sql_query = "select * from ".$this->increments_table." order by low asc";
		$result = $db->Execute($this->sql_query);
		if ($this->debug_auction) echo $this->sql_query." is the bracket display query<br>\n";
		$this->title = "Listing Setup (Auction Specific) > Bid Increments";
		$this->description = "Edit the increments a bid must increase before the next bid is accepted through the form below.
		Use this form to change the current increment brackets.<br>
			<b>Once you start a new increment system you must finish it.</b>";
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->body .= "
				<table cellpadding=3 cellspacing=1 border=0 width=450  class=row_color1>
					<tr bgcolor=000066><td class=medium_font_light align=center colspan=5><b>Current Bid Increments</b></td></tr>
					<tr class=row_color_black>
						<td class=medium_font_light><b>start of bracket</b></td>
						<td>&nbsp;</td>
						<td class=medium_font_light><b>start of bracket</b></font></td>
						<td class=medium_font_light><b>bid increment<b></td>
						<td>&nbsp;</td>
					</tr>";
			$this->row_count = 0;
			while ($show = $result->FetchRow())
			{
				$this->body .= "
					<tr class=".$this->get_row_color().">
						<td class=medium_font>".$show['low']."</td>
						<td class=medium_font>to</td>
						<td class=medium_font>";
				if ($show['high'] == 100000000)
					$this->body .= "and up</td>";
				else
					$this->body .= $show['high']."</td>";
				$this->body .= "
						<td class=medium_font>".$show['increment']."</td>
						<td>&nbsp;</td>
					</tr>";
				$this->row_count++;
			} //end of while
			$this->body .= "
				</table>\n";
		}
		else
		{
			//there are no brackets to display
			$this->body .= "
				<table cellpadding=3 cellspacing=0 border=0 width=100% class=row_color1>
					<tr  class=row_color_red>
						<td colspan=2 class=very_large_font_light><b>Auction Bid Increments Brackets</b></td>
					</tr>
					<tr class=row_color_red>
						<td colspan=2 class=medium_font_light>
							There are no increments entered into the bid increments table.
							If no bid increments are entered the default bid increment is 1.
						</td>
					</tr>
				</table>\n";
		}
		if (!$this->admin_demo())
			$this->body .= "<form action=index.php?a=23&r=20&z=1 method=post>";
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=450 class=row_color2>
					<tr bgcolor=000066>
						<td colspan=4 class=medium_font_light align=center><b>Bid Increment Bracket Form</b></td>
					</tr>
					<tr class=row_color_black>
						<td class=medium_font_light><b>start of bracket</b></td>
						<td class=medium_font_light></td>
						<td class=medium_font_light><b>end of bracket</b></td>
						<td class=medium_font_light><b>bid increment</b></td>
					</tr>
					<tr>
						<td class=medium_font>
							".($this->last_high_variable + .01)."<input type=hidden name=b[new_low] value=\"".($this->last_high_variable + .01)."\">
						</td>
						<td class=medium_font>to</td>
						<td class=medium_font>
							<select name=b[new_high]>
								<option value=100000000>and up</option>\n\t\t";
		if ($this->last_high_variable == 0)
		{
			for ($i = $this->last_high_variable + 1; $i < 500;$i = $i + 10 )
			{
				$this->body .= "<option>".$i."</option>\n\t\t";
			}
			for ($i = 600; $i <= 10000;$i = $i + 200 )
			{
				$this->body .= "<option>".$i."</option>\n\t\t";
			}
			for ($i = 15000; $i <= 50000;$i = $i + 5000 )
			{
				$this->body .= "<option>".$i."</option>\n\t\t";
			}
			for ($i = 60000; $i <= 2000000;$i = $i + 10000 )
			{
				$this->body .= "<option>".$i."</option>\n\t\t";
			}
		}
		else
		{
			for ($i = $this->last_high_variable + 2; $i < 500;$i = $i + 10)
			{
				$this->body .= "<option>".$i."</option>\n\t\t";
			}
			for ($i = 600; $i <= 10000;$i = $i + 200 )
			{
				$this->body .= "<option>".$i."</option>\n\t\t";
			}
			for ($i = 15000; $i <= 50000;$i = $i + 5000 )
			{
				$this->body .= "<option>".$i."</option>\n\t\t";
			}
			for ($i = 60000; $i <= 2000000;$i = $i + 10000 )
			{
				$this->body .= "<option>".$i."</option>\n\t\t";
			}
		}
		$this->body .= "	</select>
						</td>
						<td><input type=text name=b[new_increment] value=\"0\"></td>
					</tr>";
		if (!$this->admin_demo())
			$this->body .= "<tr><td colspan=4 class=medium_font align=center><input type=submit value=\"Save\"></td></tr>";
		$this->body .= "</table>\n";
		$this->body .= $this->hidden_bracket_variables;
		$this->body .= "</form>\n";

		$this->hidden_bracket_variables = "";
		return true;
	} //end of function increments_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_increments($db,$new_info=0,$info=0)
	{
		//echo $info." is the entered variables<br>\n";
		//echo count($info)." is the count of entered variable<Br>\n";
		$this->sql_query = "delete from ".$this->increments_table;
		$result = $db->Execute($this->sql_query);
		if ($this->debug_auction) echo $this->sql_query."<br>\n";
		if (!$result)
		{
			if ($this->debug_auction) echo $this->sql_query."<br>\n";
			$this->database_error($db->ErrorMsg(),$this->sql_query);
			return false;
		}

		$this->hidden_bracket_variables = "";

		if ((is_array($info)) && ($info != 0))
		{
			while (list($key,$value) = each($info))
			{
				$this->sql_query = "insert into ".$this->increments_table."
					(low,high,increment)
					values
					(".$info[$key]["lower"].",".$info[$key]["higher"].",".$info[$key]["increment"].")";
				if ($new_info["new_high"] != 100000000)
				{
					$this->hidden_bracket_variables .= "<input type=hidden name=c[".$key."][lower] value=".$info[$key]["lower"].">\n\t";
					$this->hidden_bracket_variables .= "<input type=hidden name=c[".$key."][higher] value=".$info[$key]["higher"].">\n\t";
					$this->hidden_bracket_variables .= "<input type=hidden name=c[".$key."][increment] value=".$info[$key]["increment"].">\n\t";
				}
				$result = $db->Execute($this->sql_query);
				if ($this->debug_auction) echo $this->sql_query."<br>\n";
				if (!$result)
				{
					if ($this->debug_auction) echo $this->sql_query."<br>\n";
					$this->database_error($db->ErrorMsg(),$this->sql_query);
					return false;
				}
			} //end of while
		}

		if (is_array($new_info))
		{
			$this->sql_query = "insert into ".$this->increments_table."
				(low,high,increment)
				values
				(".$new_info["new_low"].",".$new_info["new_high"].",".$new_info["new_increment"].")";
			$result = $db->Execute($this->sql_query);
			if ($this->debug_auction) echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug_auction) echo $this->sql_query."<br>\n";
				$this->database_error($db->ErrorMsg(),$this->sql_query);
				return false;
			}
			if ($new_info["new_high"] != 100000000)
			{
				$this->last_high_variable = $new_info["new_high"];
				if ($info == 0)
					$new_key = 0;
				else
					$new_key = count($info);
				$this->hidden_bracket_variables .= "<input type=hidden name=c[".$new_key."][lower] value=".$new_info["new_low"].">\n\t";
				$this->hidden_bracket_variables .= "<input type=hidden name=c[".$new_key."][higher] value=".$new_info["new_high"].">\n\t";
				$this->hidden_bracket_variables .= "<input type=hidden name=c[".$new_key."][increment] value=".$new_info["new_increment"].">\n\t";
			}
			else
				$this->last_high_variable = 0;

		}
		return true;

	} //end of function update_increments

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_increments($db, $low, $high)
	{
		$this->sql_query = "select * from ".$this->increments_table." where low = ".$low." and high = ".$high;
		$result = $db->Execute($this->sql_query);
		if ($this->debug_auction) echo $this->sql_query."<br>\n";
		if(!$result)
		{
			if ($this->debug_auction) echo $this->sql_query."<br>\n";
			return false;
		}
		else
		{
			$this->sql_query = "delete from ".$this->increments_table." where low = ".$low." and high = ".$high;
			$result = $db->Execute($this->sql_query);
			if ($this->debug_auction) echo $this->sql_query."<br>\n";
			if(!$result)
			{
				if ($this->debug_auction) echo $this->sql_query."<br>\n";
				return false;
			}
		}

		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function payment_types_form($db)
	{
		//file type accepted
		$this->get_configuration_data($db);
		$this->sql_query = "select payment_types, payment_types_use from ".$this->site_configuration_table;
		if ($this->debug_auction) echo $this->sql_query."<bR>\n";
		$config_result = $db->Execute($this->sql_query);
		if(!$config_result)
		{
			if ($this->debug_auction) echo $this->sql_query."<bR>\n";
			$this->site_error($db->ErrorMsg());
			return false;
		}
		$show_site_configuration = $config_result->FetchNextObject();

		$this->sql_query = "select * from ".$this->auction_payment_types_table." order by display_order, type_name";
		$type_result = $db->Execute($this->sql_query);
		if ($this->debug_auction) echo $this->sql_query."<bR>\n";
		$this->title .= "Listing Setup (Auction Specific) > Payments Types Accepted";
		$this->description .= "This field is used within the listing to let the potential
			buyer know what payment types the seller will accept as payment when the listing expires.
			The list below will display in a checkbox format for the seller to select from during the listing process.
			The choices the seller selects will then be displayed on the listing page for buyers to view.";

		if (!$type_result)
		{
			if ($this->debug_auction) echo $this->sql_query."<bR>\n";
			$this->site_error($db->ErrorMsg());
			return false;
		}
		else if($type_result->RecordCount() > 0 )
		{
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=21 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center  class=row_color2 width=450>\n";
			$this->body .= "<tr bgcolor=000066><td class=medium_font_light align=center colspan=3><b>Current Payment Types</b></td></tr>\n\t";
			$this->body .= "<tr  class=row_color_black>\n\t
				<td class=medium_font_light>\n\t<b>payment type</b></font>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font_light align=center width=120>\n\t <b>display order</b></font>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font_light align=center width=80>\n\t &nbsp;</font>\n\t</td>\n\t";
			$this->body .= "</tr>\n";

			while ($show_types = $type_result->FetchNextObject())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t".$show_types->TYPE_NAME."</font>\n\t</td>\n\t";
				$this->body .= "<td class=medium_font align=center>\n\t".$show_types->DISPLAY_ORDER."</font>\n\t</td>\n\t";
				$this->body .= "<td>\n\t<a href=index.php?a=23&r=21&z=".$show_types->TYPE_ID." class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></a>\n\t</td>\n</tr>\n";
			}
			$this->body .= "<tr  class=row_color_black>\n\t
				<td class=medium_font_light>\n\t <input type=text name=b[type_name]></font>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font align=center>\n\t<select name=b[display_order]>\n\t\t";
				for ($i=1;$i<101;$i++)
				{
					$this->body .= "<option>".$i."</option>";
				}
				$this->body .= "</select></font>\n\t</td>\n\t";
			if (!$this->admin_demo())
				$this->body .= "<td class=medium_font_light align=center>\n\t <input type=submit name=submit value=\"Save\"></font>\n\t</td>\n\t";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";

			return true;
		}
		else
		{
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=23&r=21 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center  class=row_color2>\n";
			$this->body .= "<tr  class=row_color_black>\n\t
				<td class=medium_font_light>\n\t Payment Name</font>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font_light>\n\t display order</font>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font_light>\n\t &nbsp;</font>\n\t</td>\n\t";
			$this->body .= "</tr>\n";
			$this->body .= "<tr  class=row_color_black>\n\t
				<td class=medium_font_light>\n\t <input type=text name=b[type_name]></font>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\t<select name=b[display_order]>\n\t\t";
				for ($i=1;$i<101;$i++)
				{
					$this->body .= "<option>".$i."</option>";
				}
				$this->body .= "</select></font>\n\t</td>\n\t";
			if (!$this->admin_demo())
				$this->body .= "<td class=medium_font_light>\n\t <input type=submit name=submit value=\"Save\"></font>\n\t</td>\n\t";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";

			return true;
		}
	} //end of function payment_types_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_payment_types($db, $payment_type)
	{
		if($payment_type)
		{
			$this->sql_query = "update ".$this->site_configuration_table." set payment_types = ".
				$payment_type["payment_types"].", payment_types_use = ".$payment_type["payment_types_use"];
			$result = $db->Execute($this->sql_query);
			if(!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
		}
		else return false;

		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_payment_type($db,$type_id=0)
	{
		if ($type_id)
		{
			$this->sql_query = "delete from ".$this->auction_payment_types_table."
				where type_id = ".$type_id;
			$type_result = $db->Execute($this->sql_query);
			if (!$type_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}

	} //end of function delete_payment_type

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_payment_type($db,$type_info=0)
	{
		if ($type_info)
		{
			$this->sql_query = "insert into ".$this->auction_payment_types_table."
				(type_name,display_order)
				values
				(\"".$type_info["type_name"]."\",\"".$type_info["display_order"]."\")";
			$type_result = $db->Execute($this->sql_query);
			if (!$type_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}

	} //end of function insert_payment_type

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function remove_archived_listings_form($db)
	{
		$this->title = "Remove Archived Listings Tool";
		$this->description = "Select a date below to remove all listings that expired BEFORE this date.  Note that this will delete the listings from the archive table and not the live listings table.";

		$today = getdate();
		$this->body .= "<form method=\"post\" action=\"index.php?a=23&r=23\"\">";
		$this->body .= "<table width=100%>
							<tr align=center>
								<td align=center class=\"medium_font\">Choose date to delete all before:</td>
							</tr>
							<tr align=center>
								<td align=center class=\"medium_font\">
									".$this->get_date_select("c[year]", "c[month]", "c[day]", $today['year'], $today['mon'], $today['mday'])."
								</td>
							</tr>
							<tr>
								<td align=center>&nbsp;</td>
							</tr>
							<tr>
								<td align=center><input type=\"submit\" value=\"Delete\"></td>
							</tr>
						</table>";
		$this->body .= "</form>";
		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function remove_archived_listings($db, $date)
	{
		// Find the UNIX timestamp for inputted time
		$time = gmmktime(0, 0, 0, $date['month'], $date['day'], $date['year']);

		$this->sql_query = "delete from ".$this->classifieds_expired_table." where ad_ended <= ".$time;
		$result = $db->Execute($this->sql_query);
		if(!$result)
			return false;
		else
			return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Ad_configuration
?>
