<?
if(file_exists('admin_site_class.php'))
	include_once('admin_site_class.php');
else
	include_once('../admin_site_class.php');

class bulk_uploader extends Admin_site
{
	var $db;
	var $uploader_body;
	var $debug_bulk = 0;
	var $category_id;
	var $page_number;
	var $filename;
	var $top_dropdown = array(	"All Fields",
								"general" => "General Fields",
								"mapping" => "Mapping Fields",
								"optional" => "Optional Fields",
								"images" => "Images"
							 );
	var $bottom_dropdown = array(	"Title" => array("general", "title"),
									"User ID" => array("general", "user_id"),
									"Description" => array("general", "description"),
									"Country" => array("general", "location_country"),
									"State" => array("general", "location_state"),
									"City" => array("general", "location_city"),
									"Zip" => array("general", "location_zip"),
									"Phone 1" => array("general", "phone"),
									"Phone 2" => array("general", "phone2"),
									"Fax" => array("general", "fax"),
									"URL Link 1" => array("general", "url_link_1"),
									"URL Link 2" => array("general", "url_link_2"),
									"URL Link 3" => array("general", "url_link_3"),
									"Email" => array("general", "email"),
									"Business Type" => array("general", "business_type"),
									"Mapping Address" => array("mapping", "mapping_address"),
									"Mapping City" => array("mapping", "mapping_city"),
									"Mapping State" => array("mapping", "mapping_state"),
									"Mapping Country" => array("mapping", "mapping_country"),
									"Mapping Zip" => array("mapping", "mapping_zip"),
									"Payment Types" => array("general", "payment_options")
								);

	function bulk_uploader($db, $category_id, $product_configuration, $listing_type=0)
	{
		$this->Admin_Site($db, $product_configuration);
		$this->db = $db;
		$this->get_configuration_data($db);

		// Set the icon for the admin
		$this->admin_icon = "admin_images/bulk_upload.gif";

		// Add optional fields to dropdown
		for($i = 1; $i < 21; $i++)
		{
			$optional_field_name = $this->configuration_data['optional_field_'.$i.'_name'];
			$this->bottom_dropdown[$optional_field_name] = array("optional", "optional_field_".$i);
		}

		// Add the image urls to the dropdown
		for($i = 1; $i < 21; $i++)
		{
			$this->bottom_dropdown["Image URL ".$i] = array("images", "image_".$i);
		}

		$this->update_bottom_dropdown_list($category_id, $listing_type);
		
		$this->filename = $_SERVER['DOCUMENT_ROOT'].str_replace("index.php", "", $_SERVER['PHP_SELF'])."bulk_uploader/uploads/bulk_upload_file.txt";
	}

	function update_bottom_dropdown_list($category_id, $listing_type)
	{
		if($category_id)
		{
			$this->category_id = $category_id;

			// Populate category-specific questions
			$this->sql_query = "select question_id, name from ".$this->questions_table." where category_id = ".$this->category_id;
			$result = $this->run_query();
			if(!$result)
			{
				// Error
				echo "ERROR!!!<br>";
				exit(1);
			}

			if($result->RecordCount() > 0)
			{
				$this->top_dropdown["category"] = "Category Specific Questions";

				while($question = $result->FetchRow())
				{
					$this->bottom_dropdown[$question["name"]] = array("category", $question["question_id"]);
				}
			}
		}

		// Check products and add to dropdown from that
		if($this->is_class_auctions())
		{
			// Will only use $listing_type when in the preview iframe
			if($listing_type == 1)
			{
				$this->top_dropdown["classified"] = "Classified Specific Fields";
				$this->bottom_dropdown["Price"] = array("classified", "price");
			}
			elseif($listing_type == 2)
			{
				$this->top_dropdown["auction"] = "Auction Specific Fields";
				$this->bottom_dropdown["Bid Start Date"] = array("auction", "start_time");
				$this->bottom_dropdown["Buy Now"] = array("auction", "buy_now");
				$this->bottom_dropdown["Reserve Price"] = array("auction", "reserve_price");
				$this->bottom_dropdown["Minimum Bid"] = array("auction", "minimum_bid");
			}
			else
			{
				$this->top_dropdown["auction"] = "Auction Specific Fields";
				$this->top_dropdown["classified"] = "Classified Specific Fields";

				$this->bottom_dropdown["Bid Start Date"] = array("auction", "start_time");
				$this->bottom_dropdown["Buy Now"] = array("auction", "buy_now");
				$this->bottom_dropdown["Reserve Price"] = array("auction", "reserve_price");
				$this->bottom_dropdown["Minimum Bid"] = array("auction", "minimum_bid");

				$this->bottom_dropdown["Price"] = array("classified", "price");
			}
		}
		elseif($this->is_auctions())
		{
			$this->top_dropdown["auction"] = "Auction Specific Fields";
			$this->bottom_dropdown["Bid Start Date"] = array("auction", "start_time");
			$this->bottom_dropdown["Buy Now"] = array("auction", "buy_now");
			$this->bottom_dropdown["Reserve Price"] = array("auction", "reserve_price");
			$this->bottom_dropdown["Minimum Bid"] = array("auction", "minimum_bid");
		}
		elseif($this->is_classified())
		{
			$this->top_dropdown["classified"] = "Classified Specific Fields";
			$this->bottom_dropdown["Price"] = array("classified", "price");
		}
	}

	function header()
	{
		$this->extra_header_html = "<script language=\"javascript\" src=\"bulk_uploader/parse_form.js\"></script>\n";
		$this->extra_header_html .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"bulk_uploader/bulk.css\">\n";
		$this->additional_body_tag_attributes = "onLoad=onLoadActions(".$this->page_number.")";
	}

	function undo_block()
	{
		$title = "Undo a Bulk Upload";
		$label = "Listing IDs";
		$time_label = "Inserted at time";
		$undo_label = "Undo?";
		$no_logs = "There are no previous bulk uploads that have not expired.";
		$body = "";

		// First clean out the log table
		$this->clean_profile_log();

		// Build the body
		$body .= "<table class=\"undo_top_table\">\n";

		$body .= "<tr class=\"undo_title_row\">\n";
		$body .= "<td class=\"undo_title_cell\"><input name=\"action_choice\" id=\"undo_checkbox\" type=\"radio\" onClick=\"showHideField('undo_hidden_row', 'bulk_uploader_hidden');\">".$title."</td>\n";
		$body .= "</tr>\n";

		$body .= "<tr class=\"undo_hidden_row\" id=\"undo_hidden_row\"><td>";
		$body .= "<table class=\"undo_table\">";
		$body .= "<tr class=\"undo_label_row\">\n";
		$body .= "<td class=\"undo_label_cell\">".$label."</td>\n";
		$body .= "<td class=\"undo_time_label_cell\">".$time_label."</td>\n";
		$body .= "<td class=\"undo_delete_label_cell\">".$undo_label."</td>\n";
		$body .= "</tr>\n";

		$this->sql_query = "select * from geodesic_bulk_uploader_log where user_id_list like \"\"";
		$result = $this->run_query();
		if(!$result)
		{
			$body .= "<tr class=\"error_row\"><td>Error getting log of previous bulk uploads</td></tr>";
			$body .= "</table>";
			$body .= "</td></tr>";
			$body .= "</table>";
			$this->uploader_body = str_replace("<<UNDO_BLOCK>>", $body, $this->uploader_body);
			return false;
		}

		if($result->RecordCount() == 0)
		{
			$body .= "<tr class=\"undo_none_row\"><td class=\"undo_none_cell\">".$no_logs."</td></tr>";
			$body .= "</table>";
			$body .= "</td></tr>";
			$body .= "</table>";
			$this->uploader_body = str_replace("<<UNDO_BLOCK>>", $body, $this->uploader_body);
			return true;
		}

		// Show rows
		$i = 0;
		while($element = $result->FetchRow())
		{
			if($i % 2)
				$body .= "<tr class=\"undo_odd_row\">";
			else
				$body .= "<tr class=\"undo_even_row\">";

			$body .= "<td class=\"undo_cell\">".substr($element['listing_id_list'], 0, 15)."...</td>";
			$body .= "<td class=\"undo_time_cell\">".date("F j, Y g:i:s", $element['insert_time'])."</td>";
			$body .= "<td class=\"undo_delete_cell\">";
			$body .= "<a href=\"index.php?a=200&action=delete_log&log_id=".$element['log_id']."\" onClick=\"return confirm('Are you sure you want to delete all listings that were inserted by the bulk upload session?');\">Undo</a>";
			$body .= "</td>";

			$body .= "</tr>";

			$i++;
		}

		$body .= "</table>";
		$body .= "</td></tr>";
		$body .= "</table>";

		$this->uploader_body = str_replace("<<UNDO_BLOCK>>", $body, $this->uploader_body);

		return true;
	}

	function clean_profile_log()
	{
		$delete_ids = array();

		$this->sql_query = "select * from geodesic_bulk_uploader_log";
		$result = $this->run_query();

		// Check for none, if none then lets bail
		if($result->RecordCount() == 0)
			return;

		// Get the duration from the listings
		while($log_id = $result->FetchRow())
		{
			$this->sql_query = "select duration from ".$this->classifieds_table." where id in (".$log_id['listing_id_list'].") limit 1";
			$duration_result = $this->run_query();

			$duration = $duration_result->FetchRow();

			// Check if they have expired or not
			// 60 seconds * 60 minutes * 24 hours
			$time = $duration['duration']*86400+$log_id['insert_time'];
			if(time() < $time)
				$delete_ids .= $log_id['log_id'].', ';
		}

		// Clean out the expired ids
		$delete_ids = rtrim($delete_ids, ' ,');
		$this->sql_query = "delete from geodesic_bulk_uploader_log where log_id in (".$delete_ids.")";
		$result = $this->run_query();
	}

	function delete_log($log_id)
	{
		$this->sql_query = "select listing_id_list from geodesic_bulk_uploader_log where log_id = ".$log_id;
		$result = $this->run_query();
		if(!$result)
			return false;

		if($result->RecordCount() == 1)
			$list = $result->FetchRow();
		else
			return false;

		$listing_ids = $list['listing_id_list'];

		// Delete from listings table
		$this->sql_query = "delete from ".$this->classifieds_table." where id in (".$listing_ids.")";
		$result = $this->run_query();
		if(!$result)
			return false;

		// Delete from listing extras
		$this->sql_query = "delete from ".$this->classified_extra_table." where classified_id in (".$listing_ids.")";
		$result = $this->run_query();
		if(!$result)
			return false;

		// Delete from image urls
		$this->sql_query = "delete from ".$this->images_urls_table." where classified_id in (".$listing_ids.")";
		$result = $this->run_query();
		if(!$result)
			return false;

		// Delete from log table
		$this->sql_query = "delete from geodesic_bulk_uploader_log where log_id = ".$log_id;
		$result = $this->run_query();
		if(!$result)
			return false;

		// We be done so return
		return true;
	}

	function profile_block()
	{
//		$title = "Load Profile";
//		$label = "Select Profile to Load:";
//
//		// Get the Profiles and their names
//		$this->sql_query = "select profile_id, profile_name from geodesic_bulk_uploader_profiles";
//		$result = $this->run_query();
//		if(!$result)
//		{
//			echo 'Error getting profiles<br>';
//			$this->db->Close();
//			exit;
//		}
//
//		$body .= "	<table class=\"step1_label\">
//						<tr><td>Step 1 (Optional): Choose a Saved Profile&nbsp;".$this->show_tooltip(1,1)."</td></tr>
//					</table>";
//
//		$body .= "<div id=\"profile_div\">";
//		$body .= "<table class=\"profile_table\">";
//		$body .= "<tr class=\"profile_row\">\n\t";
//		$body .= "<td class=\"profile_label\">".$label."&nbsp;";
//		$body .= "<select class=\"profile_select\" id=\"c[profile_name]\" name=\"c[profile_name]\" onClick=\"LoadProfile()\">\n\t";
//		$body .= "<option value=\"0\">None</option>";
//		while($profile = $result->FetchRow())
//			$body .= "<option value=\"".$profile['profile_id']."\">".$profile['profile_name']."</option>\n\t";
//		$body .= "</select>";
//		$body .= "</td>\n</tr>\n";
//		$body .= "</table>";
//		$body .= "</div>";

		$this->uploader_body = str_replace("<<CHOOSE_PROFILE_BLOCK>>", $body, $this->uploader_body);
	}

	function category_block()
	{
		$label = "Please choose the category to insert the listings into.";

		$body .= "	<table class=\"step2_label\">
						<tr><td>Step 2: Choose a Category&nbsp;".$this->show_tooltip(3,1)."</td></tr>
					</table>";

		$body .= "<div id=\"category_div\">";
		$body .= "<table class=\"category_table\">\n\t";
		$body .= "<tr class=\"category_row\">\n\t";
		$body .= "<td class=\"category_label\">".$label."&nbsp;";
		$body .= $this->get_category_dropdown_uploader($this->db)."</td>\n";
		$body .= "</tr>";
		$body .= "</table>";
		$body .= "</div>";

		$this->uploader_body = str_replace("<<CHOOSE_CATEGORY_BLOCK>>", $body, $this->uploader_body);
	}

	function file_block()
	{
		// Step label
		if($this->is_class_auctions())
			$body .= "	<table class=\"step5_label\">
							<tr><td>Step 5: Upload your file&nbsp;".$this->show_tooltip(7,1)."</td></tr>
						</table>";
		else
			$body .= "	<table class=\"step4_label\">
							<tr><td>Step 4: Upload your file&nbsp;".$this->show_tooltip(7,1)."</td></tr>
						</table>";

		$body .= "<div id=\"file_block_div\">";
		$body .= "<div id=\"file_warning\" class=\"file_warning_text\">";
		$body .= "After completing the above fields please press the button below to select a file to upload.<br>";
		$body .= "<input type=\"button\" class=\"file_warning_button\" value=\"Select File to Upload\" onClick=\"getFileBlockCode()\">";
		$body .= "<input type=\"reset\" class=\"reset\" value=\"Reset\">";
		$body .= "</div>";
		$body .= "</div>";

		$this->uploader_body = str_replace("<<CHOOSE_FILE_BLOCK>>", $body, $this->uploader_body);
	}

	function getFileBlockCode()
	{
		$body = "";
		$label = "Upload File:";
		$browse_label = "Browse";
		$compression_label = "Compression:";

		$body .= "<table align=\"center\" class=\"file_table\" id=\"file_block\">\n\t";
		$body .= "<tr class=\"file_row1\">\n\t";
		$body .= "<td class=\"file_label\">".$label."</td>\n";
		$body .= "<td class=\"file_browse\"><input id=\"file_name\" name=\"file_name\" type=\"file\"></td>\n";
		$body .= "</tr>";

		$body .= "<tr class=\"file_row3\">\n\t";
		$body .= "<td class=\"file_compression\" colspan=100%>\n\t";
		$body .= $compression_label."&nbsp;";
		$body .= "<input type=radio id=\"c[compression]\" name=\"c[compression]\" value=\"autodetect\" checked>AutoDetect&nbsp;&nbsp;";
		$body .= "<input type=radio id=\"c[compression]\" name=\"c[compression]\" value=\"none\">None&nbsp;&nbsp;";
		if(function_exists("zip_read"))
			$body .= "<input type=radio id=\"c[compression]\" name=\"c[compression]\" value=\"zip\">\"zipped\"&nbsp;&nbsp;";
		if(function_exists("gzopen"))
			$body .= "<input type=radio id=\"c[compression]\" name=\"c[compression]\" value=\"gzip\">\"gzipped\"&nbsp;&nbsp;";
		if(function_exists("bzopen"))
			$body .= "<input type=radio id=\"c[compression]\" name=\"c[compression]\" value=\"bz2\">\"bz2\"&nbsp;&nbsp;";
		$body .= "</td>\n";
		$body .= "</tr>";

		// Submit button
		$body .= "<tr class=\"file_entry_submit_row\">\n\t";
		$body .= "<td class=\"file_entry_submit\" colspan=100%>\n\t";
		$body .= "<input type=submit value=\"Upload\" class=\"file_entry_submit_button\">";
		$body .= "</td>";
		$body .= "</tr>";

		$body .= "</table>";

		echo $body;
	}

	function type_block()
	{
		// If not classauctions then lets not display the dropdown
		if(!$this->is_class_auctions())
		{
			$this->uploader_body = str_replace("<<CHOOSE_TYPE_BLOCK>>", "", $this->uploader_body);
			return;
		}

		// Step Label
		$body .= "	<table class=\"step3_label\">
						<tr><td>Step 3: Choose a Listing Type&nbsp;".$this->show_tooltip(4,1)."</td></tr>
					</table>";

		$label = "Please choose the type of listing that you are bulk uploading:";

		$body .= "<div id=\"type_table_div\">";
		$body .= "<table class=\"type_table\">\n\t";
		$body .= "<tr class=\"type_row\">\n\t";
		$body .= "<td class=\"type_label\">".$label."&nbsp;";
		$body .= "<select id=\"c[listing_type]\" id=\"c[listing_type]\" name=\"c[listing_type]\" class=\"type_dropdown_select\">\n\t";
		$body .= "<option value=\"-1\">Choose a Type</option>\n";
		$body .= "<option value=\"2\">Auction</option>\n";
		$body .= "<option value=\"1\">Classified Ad</option>\n";
		$body .= "</select>";
		$body .= "</td>\n";
		$body .= "</tr>";
		$body .= "</table>";
		$body .= "</div>";

		$this->uploader_body = str_replace("<<CHOOSE_TYPE_BLOCK>>", $body, $this->uploader_body);
	}

	function user_block()
	{
		$user_id_label = "Please enter the user id of the user doing the bulk uploading:";
		$username_label = "Please enter the username of the user doing the bulk uploading:";

		$body .="	<table class=\"step1_label\">
						<tr><td>Step 1: Choose a User&nbsp;".$this->show_tooltip(2,1)."</td></tr>
					</table>";
		$body .= "<div id=\"user_table_div\">";
		$body .= "<table class=\"user_table\">\n\t";
		$body .= "<tr class=\"user_id_row\">\n\t";
		$body .= "<td class=\"user_id_label\">".$user_id_label."&nbsp;";
		$body .= "<input id=\"c[user_id]\" name=\"c[user_id]\" type=\"text\" size=\"5\" class=\"user_id_input_select\" onkeyup=\"verifyUserID(this.value)\" onblur=\"sendUserId(this.value.toString())\">\n\t";
		$body .= "</td>\n";
		$body .= "</tr>";
		$body .= "<tr class=\"user_or_row\">\n\t";
		$body .= "<td colpsan=2>OR</td>";
		$body .= "</tr>";
		$body .= "<tr class=\"username_row\">\n\t";
		$body .= "<td class=\"username_label\">".$username_label."&nbsp;";
		$body .= "<input id=\"c[username]\" name=\"c[username]\" type=\"text\" size=\"15\" maxlength=\"25\" class=\"username_input_select\" onblur=\"sendUsername(this.value.toString())\">\n\t";
		$body .= "</td>\n";
		$body .= "</tr>";

		// Error message code
		$body .= "<tr class=\"user_error_row\">\n\t";
		$body .= "<td class=\"user_error\" colspan=100%>\n\t";
		$body .= "<div id=\"user_error\"></div>";
		$body .= "</td>";
		$body .= "</tr>";

		$body .= "</table>";
		$body .= "</div>";

		$this->uploader_body = str_replace("<<CHOOSE_USER_BLOCK>>", $body, $this->uploader_body);
	}

	function listing_features_block()
	{
		$body = "";
		$label = "Please select listing pre-values used for all listings.";

		// Step label
		if($this->is_class_auctions())
			$body .= "	<table class=\"step7_label\">
							<tr><td>Step 7 (Optional): Choose and set Prevalues for Fields&nbsp;".$this->show_tooltip(9,1)."</td></tr>
						</table>";
		else
			$body .= "	<table class=\"step6_label\">
							<tr><td>Step 6 (Optional): Choose and set Prevalues for Fields&nbsp;".$this->show_tooltip(9,1)."</td></tr>
						</table>";

		$body .= "<div id=\"listing_features_div\">";
		$body .= "<table class=\"listing_features_table\">\n\t";
		$body .= "<tr class=\"listing_features_header_row\">\n\t";
		$body .= "<td class=\"listing_features_check\">";
		$body .= "<input id=\"c[listing_features_checkbox]\" name=\"c[listing_features_checkbox]\" type=checkbox onClick=\"ShowListingFeatures()\">Set prevalues on specific fields";
		$body .= "</td>\n";
		$body .= "</tr>";
		$body .= "<td>";

		$body .= "<table id=\"listing_features_table\" class=\"listing_features_table\" cellspacing=\"0\" cellpadding=\"5\">\n\t";
		$body .= "<tr class=\"listing_features_label_row\">\n\t";
		$body .= "<td class=\"listing_features_label\" colspan=\"100%\">".$label."</td>\n";
		$body .= "</tr>";
		for($i = 1; $i < 6; $i++)
		{
			$body .= "<tr class=\"listing_features_row".$i."\">";

			$body .= "<td class=\"listing_features_cell_1\">\n";
			$body .= "<table class=\"listing_features_table_left\">";
			$body .= "<tr><td>";
			$body .= $this->display_dual_dropdowns("c[listing_features_top".$i."1]", "c[listing_features_bottom".$i."1]", "listing_features".$i."1", "header_row");
			$body .= "</td><td>\n";
			$body .= "<input size=15 id=\"c[listing_features_input".$i."1]\" name=\"c[listing_features_input".$i."1]\">";
			$body .= "</td></tr>";
			$body .= "</table>";
			$body .= "</td>\n";

			$body .= "<td class=\"listing_features_cell_2\">\n";
			$body .= "<table class=\"listing_features_table_right\">";
			$body .= "<tr><td>";
			$body .= $this->display_dual_dropdowns("c[listing_features_top".$i."2]", "c[listing_features_bottom".$i."2]", "listing_features".$i."2", "header_row");
			$body .= "</td><td>\n";
			$body .= "<input size=15 id=\"c[listing_features_input".$i."2]\" name=\"c[listing_features_input".$i."2]\">";
			$body .= "</td></tr>\n";
			$body .= "</table>";
			$body .= "</td>\n";

			$body .= "</tr>";
		}

		$body .= "</table>";

		$body .= "</td>\n</tr>\n";
		$body .= "</table>";
		$body .= "</div>";

		$this->uploader_body = str_replace("<<LISTING_FEATURES_BLOCK>>", $body, $this->uploader_body);
	}

	function display_hidden_fields($fields)
	{
		$body = "";
		foreach($fields as $key => $value)
		{
			// We want to put listing type at end in case of not class auctions
			if($key == "listing_type")
				continue;
			if($key=="delimeter"||$key=="encapsulation")
			{
				$body .= "<input type=hidden id=\"".$key."\" name=\"".$key."\" value=\"".urlencode(stripslashes($value))."\">";
			}else
			$body .= "<input type=hidden id=\"".$key."\" name=\"".$key."\" value=\"".$value."\">";
		}

		// Now put in the listing_type
		if($this->is_class_auctions())
			$body .= "<input type=hidden id=\"listing_type\" name=\"listing_type\" value=\"".$fields['listing_type']."\">";
		elseif($this->is_auctions())
			$body .= "<input type=hidden id=\"listing_type\" name=\"listing_type\" value=\"2\">";
		elseif($this->is_classifieds())
			$body .= "<input type=hidden id=\"listing_type\" name=\"listing_type\" value=\"1\">";
			
		$this->uploader_body = str_replace("<<HIDDEN_FIELDS>>", $body, $this->uploader_body);
	}

	function build_title_block()
	{
		$body = "";
		$label = "Build Title from fields?";
		$class_auctions = "Please select a listing type.";

		// Step label
		if($this->is_class_auctions())
			$body .= "	<table class=\"step8_label\">
							<tr><td>Step 8 (Optional): Choose the Fields that will Compose the Title&nbsp;".$this->show_tooltip(10,1)."</td></tr>
						</table>";
		else
			$body .= "	<table class=\"step7_label\">
							<tr><td>Step 7 (Optional): Choose the Fields that will Compose the Title&nbsp;".$this->show_tooltip(10,1)."</td></tr>
						</table>";

		$body .= "<div id=\"build_title_div\">";
		$body .= "<table class=\"build_title_table\">\n\t";
		$body .= "<tr class=\"build_title_row\">\n\t";
		$body .= "<td class=\"build_title\"><input type=\"checkbox\" id=\"c[build_title]\" name=\"c[build_title]\" value=\"build_title\" onClick=\"MakeFieldsAppear('c[build_title]', 'bottom_dropdowns')\">".$label."</td>\n</tr>";
		$body .= "<tr class=\"build_title_parts_row\">\n\t";
		$body .= "<td class=\"build_title_parts\">";
		$body .= "<div id=\"bottom_dropdowns\">";
		$body .= "<table><tr>";
		$body .= "<td>";
		$body .= $this->display_dual_dropdowns("top_select0", "bottom_select0", "bottom_div0", "header_row");
		$body .= "</td>";
		$body .= "<td>";
		$body .= $this->display_dual_dropdowns("top_select1", "bottom_select1", "bottom_div1", "header_row");
		$body .= "</td>";
		$body .= "<td>";
		$body .= $this->display_dual_dropdowns("top_select2", "bottom_select2", "bottom_div2", "header_row");
		$body .= "</td>";
		$body .= "<td>";
		$body .= $this->display_dual_dropdowns("top_select3", "bottom_select3", "bottom_div3", "header_row");
		$body .= "</td>";
		$body .= "</table>";
		$body .= "</div>";
		$body .= "</td>\n</tr>\n";
		$body .= "</table>";
		$body .= "</div>";

		$this->uploader_body = str_replace("<<BUILD_TITLE_BLOCK>>", $body, $this->uploader_body);
	}

	function submit_button()
	{
		$body = "";
		$submit_label = "Submit";
		$reset_label = "Reset";

		// Step label
		if($this->is_class_auctions())
			$body .= "	<table class=\"step11_label\">
							<tr><td>Step 11: Submit and Upload your Listings&nbsp;".$this->show_tooltip(13,1)."</td></tr>
						</table>";
		else
			$body .= "	<table class=\"step10_label\">
							<tr><td>Step 10: Submit and Upload your Listings&nbsp;".$this->show_tooltip(13,1)."</td></tr>
						</table>";

		$body .= "<div id=\"submit_table_div\">";
		$body .= "<table class=\"submit_table\">\n\t";
		$body .= "<tr class=\"submit_row\">\n\t";
		$body .= "<td class=\"submit_label\"><input type=\"button\" value=\"".$submit_label."\" class=\"submit\" onClick=\"return Submit();\"></td>\n";
		$body .= "<td class=\"reset_label\"><input type=\"button\" value=\"".$reset_label."\" class=\"reset\" onClick=\"Reset()\"></td>\n";
		$body .= "</td>\n";
		$body .= "</tr>";
		$body .= "<tr><td>";
		$body .= "<br><a href=\"index.php?a=200\"><< Back</a>";
		$body .= "</td></tr>";
		$body .= "</table>";
		$body .= "</div>";

		$this->uploader_body = str_replace("<<SUBMIT_BUTTON>>", $body, $this->uploader_body);
	}

	function save_profile_button()
	{
//		$body = "";
//		$save_new_label = "Save to New Profile";
//		$save_old_label = "Save Changes to Profile";
//
//		// Step label
//		if($this->is_class_auctions())
//			$body .= "	<table class=\"step11_label\">
//							<tr><td>Step 11 (Optional): Save Selections to New Profile or Existing Profile&nbsp;".$this->show_tooltip(12,1)."</td></tr>
//						</table>";
//		else
//			$body .= "	<table class=\"step10_label\">
//							<tr><td>Step 10 (Optional): Save Selections to New Profile or Existing Profile&nbsp;".$this->show_tooltip(12,1)."</td></tr>
//						</table>";
//
//		$body .= "<table id=\"save_table\" class=\"save_table\">";
//		$body .= "<tr class=\"save_row\">";
//		$body .= "<td class=\"save_new_cell\">";
//		$body .= "<input type=\"button\" value=\"".$save_new_label."\" class=\"save_new_button\" onClick=\"save_new_profile()\">";
//		$body .= "</td>";
//		$body .= "<td class=\"save_changes_cell\">";
//		$body .= "<input type=\"button\" value=\"".$save_old_label."\" class=\"save_old_button\" onClick=\"save_profile_changes()\">";
//		$body .= "</td>";
//		$body .= "</tr>";
//		$body .= "</table>";

		$this->uploader_body = str_replace("<<SAVE_PROFILE_BUTTON>>", $body, $this->uploader_body);
	}

	function file_parts()
	{
		$delimeter = "Delimeter:";
		$encapsulaton = "Encapsulation (Optional):";

		// Step label
		if($this->is_class_auctions())
			$body .= "	<table class=\"step4_label\">
							<tr><td>Step 4: Input a Delimeter and an Encapsulation String&nbsp;".$this->show_tooltip(6,1)."</td></tr>
						</table>";
		else
			$body .= "	<table class=\"step3_label\">
							<tr><td>Step 3: Input a Delimeter and an Encapsulation String (Optional)&nbsp;".$this->show_tooltip(6,1)."</td></tr>
						</table>";

		$body .= "<div id=\"file_parts_div\">";
		$body .= "<table class=\"file_parts_table\">";
		$body .= "<tr class=\"file_parts_row\">\n\t";
		$body .= "<td class=\"file_delimiter_label\">".$delimeter."&nbsp;<input type=\"text\" id=\"c[delimeter]\" name=\"c[delimeter]\" size=\"5\">&nbsp;".$encapsulaton."&nbsp;<input type=\"text\" id=\"c[encapsulation]\" name=\"c[encapsulation]\" size=\"5\"></td>\n";
		$body .= "</tr>";
		$body .= "</table>";
		$body .= "</div>";

		$this->uploader_body = str_replace("<<FILE_PARTS>>", $body, $this->uploader_body);
	}

	function duration_block()
	{
		$body = "";
		$text = "Please select below whether to have your listings use a fixed duration or a start and end date.";

		// Step label
		if($this->is_class_auctions())
			$body .= "	<table class=\"step6_label\">
							<tr><td>Step 6: Choose the Listing Duration&nbsp;".$this->show_tooltip(8,1)."</td></tr>
						</table>";
		else
			$body .= "	<table class=\"step5_label\">
							<tr><td>Step 5: Choose the Listing Duration&nbsp;".$this->show_tooltip(8,1)."</td></tr>
						</table>";

		$body .= "<table>\n";
		$body .= "<tr class=\"duration_text_row\"><td>".$text."</td></tr>";
		$body .= "</table>\n";
		$body .= "<table class=\"duration_table\" id=\"duration_table\">";
		$body .= "<tr class=\"duration_fixed_row\">\n\t";
		$body .= "<td><input type=\"radio\" id=\"duration_fixed\" name=\"duration_type\" onClick=\"ShowFixedDuration()\" value=\"duration_fixed\">Fixed Duration</td>\n";
		$body .= "</tr>\n";
		$body .= "<tr class=\"duration_fixed_hidden_row\">\n\t";
		$body .= "<td id=\"duration_fixed_hidden\"></td>\n";
		$body .= "</tr>\n";
		$body .= "<tr class=\"duration_variable_row\">\n\t";
		$body .= "<td><input type=\"radio\" id=\"duration_variable\" name=\"duration_type\" onClick=\"ShowVariableDuration()\" value=\"duration_variable\">Choose Start and End Time</td>\n";
		$body .= "</tr>\n";
		$body .= "<tr class=\"duration_variable_hidden_row\">\n\t";
		$body .= "<td id=\"duration_variable_hidden\"></td>\n";
		$body .= "</tr>\n";
		$body .= "</table>";

		$this->uploader_body = str_replace("<<DURATION_BLOCK>>", $body, $this->uploader_body);
	}

	function preview_block()
	{
		// Step label
		if($this->is_class_auctions())
			$body .= "	<table class=\"step9_label\">
							<tr><td>Step 9: Select Column Values to go with Data&nbsp;".$this->show_tooltip(11,1)."</td></tr>
						</table>";
		else
			$body .= "	<table class=\"step8_label\">
							<tr><td>Step 8: Select Column Values to go with Data&nbsp;".$this->show_tooltip(11,1)."</td></tr>
						</table>";

		$body .= "<iframe class=\"preview_iframe\" id=\"preview_block_iframe\" frameBorder=\"0\" height=\"0\" width=\"0\">";
		$body .= "</iframe>";
		$body .= "<div id=\"preview_button\"></div>";

		$this->uploader_body = str_replace("<<PREVIEW_BLOCK>>", $body, $this->uploader_body);
	}

	function upgrade_block()
	{
		// Step label
		if($this->is_class_auctions())
			$body .= "	<table class=\"step10_label\">
							<tr><td>Step 10 (Optional): Listing Upgrades&nbsp;".$this->show_tooltip(14,1)."</td></tr>
						</table>";
		else
			$body .= "	<table class=\"step9_label\">
							<tr><td>Step 9 (Optional): Upload your file&nbsp;".$this->show_tooltip(14,1)."</td></tr>
						</table>";

		$body .= "<table>\n";
		$body .= "<tr class=\"duration_text_row\"><td>These upgrade options will effect all of the uploaded data.</td></tr>";
		$body .= "</table>\n";
		$body .= "<table class=\"duration_table\" id=\"duration_table\">";
		$body .= "<tr class=\"duration_fixed_row\">\n\t";
		$body .= "<td><input type=\"checkbox\" id=\"bolding\" name=\"bolding\" /><label for=\"bolding\">Bolding</label></td>\n";
		$body .= "</tr>\n";
		$body .= "<tr class=\"duration_fixed_row\">\n\t";
		$body .= "<td><input type=\"checkbox\" id=\"better_placement\" name=\"better_placement\" /><label for=\"better_placement\">Better Placement</label></td>\n";
		$body .= "</tr>\n";
		$body .= "<tr class=\"duration_fixed_row\">\n\t";
		$body .= "<td><input type=\"checkbox\" id=\"featured_ad\" name=\"featured_ad\" /><label for=\"featured_ad\">Featured Listing</label></td>\n";
		$body .= "</tr>\n";
		$attentionGetters = $this->getAttentionGetters();
		if(count($attentionGetters)>0) {
			$select = "<select id=\"attention_getter_url\" name=\"attention_getter_url\" onChange=\"javascript: document.getElementById('attention_getter').checked = true;\">";
			foreach ($attentionGetters as $key => $value) {
				$select .= "<option value=\"$value\">$key</option>";
			}
			$select .= "</select>";
			$body .= "<tr class=\"duration_fixed_row\">\n\t";
			$body .= "<td><input type=\"checkbox\" id=\"attention_getter\" name=\"attention_getter\" onClick=\"javascript: document.getElementById('attention_getter_url').focus();\"/><label for=\"attention_getter\">Attention Getter</label> $select</td>\n";
			$body .= "</tr>\n";
		}
		$body .= "</table>";

		$this->uploader_body = str_replace("<<LISTING_UPGRADES>>", $body, $this->uploader_body);
	}

	function display_uploader($page_number=1, $values=0)
	{
		$this->title = "Bulk Uploader Utility";
		if($page_number == 1)
			$this->description = "If you wish to undo a previous bulk upload session select \"Undo a Bulk Upload\" below.<br>
									If you wish to perform a bulk upload session select \"Perform a Bulk Upload\" below and follow each steps instructions.";
		else
			$this->description = "";
		$this->page_number = $page_number;

		// Build the header
		$this->header();

		// Build page
		if($page_number == 1)
		{
			// Open the template file
			if(file_exists("bulk_uploader/bulk_uploader.html"))
				$this->uploader_body = file_get_contents("bulk_uploader/bulk_uploader.html");
			else
				$this->uploader_body = file_get_contents("bulk_uploader.html");

			// Actually populate it
			$this->undo_block();
			$this->profile_block();
			$this->user_block();
			$this->type_block();
			$this->category_block();
			$this->file_parts();
			$this->file_block();
		}
		elseif($page_number == 2)
		{
			// Open the template file
			if(file_exists("bulk_uploader/bulk_uploader_2.html"))
				$this->uploader_body = file_get_contents("bulk_uploader/bulk_uploader_2.html");
			else
				$this->uploader_body = file_get_contents("bulk_uploader_2.html");

			$this->display_hidden_fields($values);
			$this->build_title_block();
			$this->listing_features_block();
			$this->duration_block();
			$this->preview_block();
			$this->submit_button();
			$this->upgrade_block();
			$this->save_profile_button();
		}

		$this->body = $this->uploader_body;
	}

	function upload_file($choices, $files)
	{
		if($choices['compression'] !== 'autodetect')
		{
			switch($choices['compression'])
			{
				case 'none':
					$this->write_file($files['file_name']['tmp_name'], 'none', $choices);
					break;
				case 'zip':
					$this->write_file($files['file_name']['tmp_name'], 'zip', $choices);
					break;
				case 'gzip':
					$this->write_file($files['file_name']['tmp_name'], 'gzip', $choices);
					break;
				case 'bz2':
					$this->write_file($files['file_name']['tmp_name'], 'bz2', $choices);
					break;
			}
		}
		else
		{
			// Autodetect
			switch($files['file_name']['type'])
			{
				case 'application/x-gzip':
				case 'application/gzip':
				case 'application/gzipped':
				case 'application/gzip-compressed':
					$this->write_file($files['file_name']['tmp_name'], 'gzip', $choices);
					break;
				case 'application/x-zip':
				case 'application/zip':
				case 'application/x-zip-compressed':
				case 'multipart/x-zip':
					$this->write_file($files['file_name']['tmp_name'], 'zip', $choices);
					break;
				case 'application/bzip2':
				case 'application/x-bz2':
				case 'application/x-bzip':
					$this->write_file($files['file_name']['tmp_name'], 'bz2', $choices);
					break;
				default:
					// Treat as text
					$this->write_file($files['file_name']['tmp_name'], 'none', $choices);
					break;
			}
		}
	}

	function write_file($file, $compression, $choices)
	{
		if($compression !== "none")
		{
			// Compressed
			switch($compression)
			{
				case 'gzip':
					if(function_exists('readgzfile'))
						$text = readgzfile($file);
					else
						return false;
					break;

				case 'bz2':
					if(function_exists('bzopen') && function_exists('bzread') &&
						function_exists('bzdecompress') && function_exists('bzclose'))
						{
							$bz_file = bzopen($file, 'r');
							$bz_string = bzread($bz_file);
							$text = bzdecompress($bz_string);
							bzclose($bz_file);
						}
					else
						return false;
					break;

				case 'zip':
					if(function_exists('zip_open') && function_exists('zip_read') &&
						function_exists('zip_entry_read') && function_exists('zip_entry_open') &&
						function_exists('zip_entry_close') && function_exists('zip_close'))
						{
							$zip_file = zip_open($file);
							$zip_entry = zip_read($zip_file);
							if(!zip_entry_open($zip_file, $zip_entry))
								return false;
							$text = zip_entry_read($zip_entry);
							zip_entry_close($zip_entry);
							zip_close($zip_file);
						}
					else
						return false;
					break;

				default:
					// Some unknown file type
					return false;
			}
		}
		else
		{
			if(move_uploaded_file($file, $this->filename) === false)
				return false;
			else
				return true;
		}

		//echo $_REQUEST['file_name'].' is the filename<Br>';
	}

	function save_iframe_variables($variables, $prevalue, $duration)
	{
		$this->sql_query = "select bulk_upload_text from ".$this->sessions_table." where classified_session like \"".$_COOKIE['admin_classified_session']."\" and user_id = 1";
		$result = $this->run_query();
		if(!$result)
			return false;

		$error = true;
		$this->sql_query = "update ".$this->sessions_table." set bulk_upload_text = \"";
		foreach($variables as $value)
		{
			// If we run into a element thats used then lets tell the error to not occur
			if($error && ($value != -1))
			{
				$error = false;
			}

			$sql_value .= $value."|";
		}
		$sql_value = rtrim($sql_value, " |");

		// If no parts are used then lets send an error
		if($error)
		{
			echo '<error>Please use at least one field from the file</error>';
			return false;
		}

		// Now lets append a ^ in there so that we can separate the different fields out
		if($prevalue)
		{
			$sql_value .= "^pre|";
			for($i = 1; $i < 6; $i++)
			{
				$sql_value .= $prevalue["listing_features_top".$i."1"]."|";
				$sql_value .= $prevalue["listing_features_bottom".$i."1"]."|";
				$sql_value .= $prevalue["listing_features_input".$i."1"]."|";
				$sql_value .= $prevalue["listing_features_top".$i."2"]."|";
				$sql_value .= $prevalue["listing_features_bottom".$i."2"]."|";
				$sql_value .= $prevalue["listing_features_input".$i."2"]."|";
			}
		}

		// Append another ^ in there so that we can tell the differences
		if($duration)
		{
			$sql_value .= "^dur|";
			if($duration['duration_value'])
			{
				// Fixed Duration
				$sql_value .= $duration['duration_value']."|";
			}
			else
			{
				// Start and End Time specified
				$sql_value .= $duration['start_day']."|";
				$sql_value .= $duration['start_month']."|";
				$sql_value .= $duration['start_year']."|";
				$sql_value .= $duration['start_hour']."|";
				$sql_value .= $duration['start_minute']."|";
				$sql_value .= $duration['end_day']."|";
				$sql_value .= $duration['end_month']."|";
				$sql_value .= $duration['end_year']."|";
				$sql_value .= $duration['end_hour']."|";
				$sql_value .= $duration['end_minute']."|";
			}
		}

		$sql_value = rtrim($sql_value, " |");

		$this->sql_query .= $sql_value."\" where classified_session like \"".$_COOKIE['admin_classified_session']."\" and user_id = 1";
		$result = $this->run_query();
		if(!$result)
		{
			echo '<error>Unable to save data.</error>';
			return false;
		}

		// echo out the unused XML request
		echo "<response></response>";
	}

	function update_results($old_size, $new_size)
	{
		// difference will never be negative because of how it is being called from the js
		// Shave one off of both so that the indices will work in a loop
		$difference = $new_size - $old_size;

		$handle = fopen(urldecode($this->filename), 'r');
		if(!$handle)
		{
			echo '<root><error>Error opening the data file.</error></root>';
			fclose($handle);
			return false;
		}

		$xml = "<root><size>".($difference+1)."</size>";

		// Read lines until reaches $old_size-1
		
		$delimeter = urldecode($_REQUEST['delimeter']);
		$encapsulation = stripslashes(urldecode($_REQUEST['encapsulation']));

		// Add two make up for the two shaved off
		for($i = 0; $i < ($difference+1); $i++)
		{
			$xml .= "<line".$i.">";

			// Add the line number to it
			$xml .= urlencode('<td>'.($i+$old_size).'</td>');

			// Do the actual handing and putting into XML
			if($encapsulation)
				$line = fgetcsv($handle,1000,$delimeter,$encapsulation);
			else
				$line = fgetcsv($handle,1000,$delimeter);
			foreach($line as $key => $value)
				$xml .= urlencode('<td>'.$value.'</td>');
			$xml .= "</line".$i.">";
		}

		// Close the XML
		$xml .= "</root>";

		// "Javascriptize" the urlencode to handle the unescape function
		$xml = str_replace("+", "%20", $xml);

		// Close the file
		fclose($handle);

		// Output the XML
		echo $xml;
	}

	function run_queries($start, $user_id, $title=0, $category_id=0, $listing_type=1)
	{
		// Check if we are in the first round of queries
		if($start == 0)
		{
			// Lets clean out the database value
			$this->sql_query = "update ".$this->sessions_table." set bulk_upload_listing_id_list = \"\" where classified_session = ".$_COOKIE['admin_classified_session'];
			$result = $this->run_query();

			$id_list = "";
		}
		else
		{
			// Lets get the current list out of the database
			$this->sql_query = "select bulk_upload_listing_id_list from ".$this->sessions_table." where classified_session like \"".$_COOKIE['admin_classified_session']."\"";
			$result = $this->run_query();

			if(!$result)
			{
				echo '<root><error>Error getting the id log.</error></root>';
				return false;
			}

			$list = $result->FetchRow();
			$id_list = $list['bulk_upload_listing_id_list'];
		}

		// Grab the parts string from the sessions table
		$this->sql_query = "select bulk_upload_text from ".$this->sessions_table." where classified_session like \"".$_COOKIE['admin_classified_session']."\" and user_id = 1";
		$result = $this->run_query();
		if(!$result)
		{
			echo '<root><error>Getting the bulk upload text failed.</error></root>';
			return false;
		}
		else
			$text = $result->FetchRow();

		// Split the text into two parts
		$text = explode("^", $text['bulk_upload_text']);
		$parts = explode("|", $text[0]);

		// Check for prevalues and duration values
		if(sizeof($text) == 3)
		{
			$prevalues = explode("|", $text[1]);
			$duration = explode("|", $text[2]);
		}
		elseif(sizeof($text) == 2)
		{
			$duration = explode("|", $text[1]);
		}

		// Go ahead and take off the beginning of duration and prevalues
		array_shift($duration);
		if(is_array($prevalues))
			array_shift($prevalues);

		$handle = fopen($this->filename, "r");
		if(!$handle)
		{
			echo "<root><error>Error opening the data file.</error></root>";
			return false;
		}

		
		if($start !== 0)
		{
			// Read lines until reaches $start-1
			if($_GET["delimeter"])$delimeter = urldecode($_GET["delimeter"]);
			if($_GET["encapsulation"])$encapsulation = urldecode($_GET["encapsulation"]);
			for($i = 0; $i < $start; $i++)
				fgetcsv($handle,1000,$delimeter,$encapsulation);
		}

		// Create the insert statement
		$this->sql_query = "insert into ".$this->classifieds_table;
		$first_part = "";

		foreach($parts as $key => $value)
		{
			// Check for not used
			if($value == -1)
				continue;
			// Check for category specific fields
			elseif(strpos($value, "category_spec") !== false)
				continue;
			// Check for images
			elseif(strpos($value, "image_") !== false)
				continue;
			// Check for title field
			elseif($value == "title")
				continue;
			elseif($value == "user_id")
				continue;
			else
				$first_part .= $value.", ";
		}

		// Add the misc. stuff to the end of it
		// The order is title, seller, category, item_type, live
		$first_part .= "title, seller, category, item_type, live, ";

		if(is_array($prevalues))
		{
			if(sizeof($prevalues) % 2)
			{
				// prevalues array is an odd size so lets shrink it by one
				// since this only happens when the last element is empty
				array_pop($prevalues);
			}

			for($i = 1; $i < sizeof($prevalues)/3; $i += 3)
			{
				if(($prevalues[$i] != -1) && $prevalues[$i])
				{
					if($prevalues[$i] == "title")
						continue;
					// Check for category specific fields
					elseif(strpos($prevalues[$i], "category_spec") !== false)
						continue;
					elseif(strpos($prevalues[$i], "image_") !== false)
						continue;
					elseif(in_array($prevalues[$i], $parts) !== false)
						continue;
					else
						$first_part .= $prevalues[$i].", ";
				}
			}
		}
		
		// Figure out the duration
		$first_part .= "date, duration, ends, ";
		
		// Listing Upgades
		if(isset($_REQUEST["bolding"])) {
			$first_part .= "bolding, ";
		}
		if(isset($_REQUEST["better_placement"])) {
			$first_part .= "better_placement, ";
		}
		if(isset($_REQUEST["featured_ad"])) {
			$first_part .= "featured_ad, ";
		}
		if(isset($_REQUEST["attention_getter"])) {
			$first_part .= "attention_getter, attention_getter_url, ";
		}
		
		//turn off dutch auction
		if($listing_type==2){
			$first_part .= "auction_type, ";
		}

		$delimeter = urldecode($_REQUEST['delimeter']);
		$encapsulation = stripslashes(urldecode($_REQUEST['encapsulation']));
		
		// WARNING: the first part variable should not be modified in the below loop
		for($i = 0; $i < 51; $i++)
		{
			$second_part = "";
			
			if($encapsulation)
				$line = fgetcsv($handle,1000,$delimeter,$encapsulation);
			else
				$line = fgetcsv($handle,1000,$delimeter);

			// Check for EOF
			if($line === false)
			{
				if(feof($handle))
				{
					$complete = 1;
					break;
				}
				else
					continue;
			}

			if(array_is_empty($line))
				// There is am empty line in the file
				continue;

			// Build the insertion parts
			foreach($line as $key => $value)
			{
				// Cache this value so that it wont be constantly looked up
				$field = $parts[$key];
				$value = trim(trim($value),$encapsulation);
				if($field == -1)
					// Skip it if the user does not want it used
					continue;
				elseif($field == "title")
				{
					// Get the title key either in prevalue or in the line
					if(($prevalue_key = array_search($field, $prevalues)) !== false)
					{
						$title_value = $prevalues[$prevalue_key+1];
					}
					else
					{
						// Skip the title in case its custom built
						$title_value = $value;
					}
					continue;
				}
				elseif($field == "user_id")
				{
					$newUserId = $value;
					continue;
				}
				elseif(strpos($field, "image") !== false)
				{
					if(!is_array($image_field))
						$image_field = array();
					if(($prevalue_key = array_search($field, $prevalues)) !== false)
					{
						// Store the value in an array for lookup later
						$image_field[] = trim(trim($prevalues[$prevalue_key+1]),$encapsulation);
					}
					else
					{
						// Store the value in an array for lookup later
						if(in_array($value, $image_field))
							continue;
						else
							$image_field[] = $value;
					}

					// Skip it
					continue;
				}
				elseif(strpos($field, "category_spec") !== false)
				{
					if(($prevalue_key = array_search($field, $prevalues)) !== false)
					{
						// Store the value in an array for lookup later
						$category_questions[$field] = trim(trim($prevalues[$prevalue_key+1]),$encapsulation);
					}
					else
					{
						// Store the value in an array for lookup later
						$category_questions[$field] = $value;
					}

					// Skip it
					continue;
				}
				else
					$second_part .= "\"".$value."\", ";
			}

			// Deal with the title
			if(is_array($title))
			{
				$built_title = "";
				// bottom_select0
				for($i = 0; $i < 4; $i++)
				{
					// If not used skip it
					if($title['bottom_select'.$i] == -1)
						continue;

					$key = array_search($title['bottom_select'.$i], $parts);
					if($key === false)
					{
						// Skip this one because the user did not specify
						// where it is in the iframe table.
						continue;
					}

					$built_title .= trim(trim($line[$key]),$encapsulation)." ";
				}

				// Trim and append the new title
				$built_title = rtrim($built_title);

				// Check for empty title
				if(!strlen($built_title) && $title_value)
					$built_title = $title_value;

				$second_part .= "\"".urlencode($built_title)."\", ";
			}elseif(isset($title_value))
			{
				$second_part .= "\"".urlencode($title_value)."\", ";
			}else
				$second_part .= "\"".urlencode($title_value)."\", ";
			
			// Add the seller onto the query
			if($newUserId) {
				$second_part .= "\"".$newUserId."\", ";
			} else {
				$second_part .= "\"".$user_id."\", ";				
			}

			// Add the category id onto the query
			$second_part .= "\"".$category_id."\", ";

			// Add the listing_type or item_type onto the end
			$second_part .= $listing_type." ,";

			// Add the live tag
			$second_part .= "1, ";
			// Lastly lets append the final prevalues in there
			if(is_array($prevalues))
			{
				$backup = $i;
				$size = sizeof($prevalues)/3;
				for($i = 1; $i < $size; $i += 3)
				{
					if(($prevalues[$i] != -1) && $prevalues[$i])
					{
						if($prevalues[$i] == "title")
							continue;
						// Check for category specific fields
						elseif(strpos($prevalues[$i], "category_spec") !== false)
						{
							if(($prevalue_key = array_search($field, $prevalues)) !== false)
							{
								// Store the value in an array for lookup later
								$category_questions[$prevalues[$i]] = $prevalues[$i+1];
							}
							continue;
						}
						elseif(in_array($prevalues[$i], $parts) !== false)
							continue;
						else
							$second_part .= "\"".($prevalues[$i+1])."\", ";
					}
				}
				$i = $backup;
			}

			// Do the durations
			// fixed Duration
			if(sizeof($duration) == 1)
			{
				$new_time = time()+(60*60*24);
				$second_part .= time().", ".$duration[0].", ".$new_time.", ";
			}
			else
			{
				// set start and end time
				$start_time = mktime($duration[3], $duration[4], 0, $duration[1], $duration[0], $duration[2]);
				$end_time = mktime($duration[8], $duration[9], 0, $duration[6], $duration[5], $duration[7]);

				$second_part .= $start_time.", 0, ".$end_time.", ";
			}
			
			// Listing Upgrades
			if(isset($_REQUEST["bolding"])) {
				$second_part .= "1, ";
			}
			if(isset($_REQUEST["better_placement"])) {
				$second_part .= "1, ";
			}
			if(isset($_REQUEST["featured_ad"])) {
				$second_part .= "1, ";
			}
			if(isset($_REQUEST["attention_getter"])) {
				$second_part .= "1, \"".$_REQUEST["attention_getter_url"]."\", ";
			}
			
			// Turn off dutch auction
			if($listing_type==2){
				$second_part .= "1, ";
			}
			
			// Clean up the first and second parts
			$first_part = trim($first_part, " ,");
			$second_part = trim($second_part, " ,");

			// Build the query
			$sql_query = $this->sql_query." (".$first_part.") values (".$second_part.")";

			// Run the query
			$result = $this->run_query($sql_query);
			if(!$result)
			{

				fclose($handle);
				echo '<root><error>Error inserting the data.  Query is: '.$sql_query.'</error></root>';
				return false;
			}
			else
				// This is the listing id
				$insert_id = $this->db->Insert_ID();

			// Add the listing id onto the id_list string
			$id_list .= ",".$insert_id;

			// Now insert the image URLs if they exist
			if(is_array($image_field))
			{
				$sql_query = "insert into ".$this->images_urls_table."
						(image_url,classified_id, date_entered,image_width,image_height,original_image_width,original_image_height,display_order)
						values ";
				foreach($image_field as $value)
				{

					$image_dimensions = @getimagesize($value);
					if($image_dimensions)
					{
						$part_two = $sql_query." (\"".$value."\", ".$insert_id.", ".time().", ".$image_dimensions[0].", ".$image_dimensions[1].", ".$image_dimensions[0].", ".$image_dimensions[1].", 0)";
						$result = $this->run_query($part_two);
						if(!$result)
						{
							echo '<root><error>Error inserting the image URL.  Query is: '.$part_two.'</error></root>';
							return false;
						}else{
							$setImageStatus = "update ".$this->classifieds_table." set image = 1 where id = ".$insert_id;
							$result = $this->run_query($setImageStatus);
						}
					}
				}
				$image_field = array();
			}

			// Insert the category specific questions
			if(is_array($category_questions))
			{
				foreach($category_questions as $key => $value)
				{
					$checkbox = 0;
					if($value){
						$key = str_replace("category_spec", "", $key);
						$sql_query = "select * from ".$this->classified_questions_table." where question_id = '$key' limit 1";
						$result = $this->run_query($sql_query);
						$resultInfo = $result->FetchRow();
						if($resultInfo["choices"]=="check"){
							$value = $resultInfo["name"];
							$checkbox = 1;
						}
						$sql_query = "insert into ".$this->classified_extra_table." (classified_id, name, question_id, value, checkbox) values ";
						$sql_query .= "(".$insert_id.", \"".$resultInfo["name"]."\", \"".$key."\", \"".$value."\", $checkbox)";
						$result = $this->run_query($sql_query);
						if(!$result)
						{
							fclose($handle);
							echo '<root><error>Error inserting the extras.  Query is: '.$sql_query.'</error></root>';
							return false;
						}
					}
				}
			}
		}

		// Update the log
		$this->sql_query = "update ".$this->sessions_table." set bulk_upload_listing_id_list = \"".trim($id_list, " ,")."\" where classified_session = \"".$_COOKIE['admin_classified_session']."\"";
		$result = $this->run_query();

		// Check for being complete
		if($complete)
		{
			fclose($handle);

			// Put the final log into the log table
			$this->sql_query = "insert into geodesic_bulk_uploader_log (listing_id_list, insert_time) values (\"".trim($id_list, " ,")."\", \"".time()."\")";
			$result = $this->run_query();

			// Go ahead and send the XML to the javascript
			if(!$result)
				echo '<root><error>Error inserting into log</error></root>';
			else
				echo '<root><complete>yes</complete></root>';
			return true;
		}

		// Update the start counter
		$start += 50;

		fclose($handle);

		// Output the xml
		$xml = "<root>";
		$xml .= "<start>".$start."</start>";
		$xml .= "<user_id>".$user_id."</user_id>";
		$xml .= "<title>";
		if(is_array($title))
		{
			for($i = 0; $i < 4; $i++)
				$title_field .= "title[bottom_select".$i."]=".$title['bottom_select'.$i]."&";
			$title_field = rtrim($title_field, " &");

			// Javascriptize the urlencode
			$title_field = str_replace("+", "%20", urlencode($title_field));

			// Append the title field onto the xml
			$xml .= $title_field;
		}
		$xml .= "</title>";
		$xml .= "<category_id>".$category_id."</category_id>";
		$xml .= "<listing_type>".$listing_type."</listing_type>";
		$xml .= "<filename>".$this->filename."</filename>";
		$xml .= "</root>";

		echo $xml;

		return true;
	}

	function send_username($user_id)
	{
		$this->sql_query = "select username from ".$this->logins_table." where id = ".$user_id;
		$result = $this->run_query();
		if(!$result)
			return;

		if($result->RecordCount() == 1)
			$username = $result->FetchRow();
		else
		{
			echo "<root><error>Please enter a valid user id.</error></root>";
			return;
		}
		echo '<root><username>'.$username['username'].'</username></root>';
		return;
	}

	function send_user_id($username)
	{
		$this->sql_query = "select id from ".$this->logins_table." where username like \"".$username."\"";
		$result = $this->run_query();
		if(!$result)
			return;

		if($result->RecordCount() == 1)
			$id = $result->FetchRow();
		else
		{
			echo '<root><error>Please enter a valid username.</error></root>';
			return;
		}
		echo '<root><user_id>'.$id['id'].'</user_id></root>';
		return;
	}

	function display_dual_dropdowns($top_name, $bottom_name, $div_name, $class_name=0)
	{
		// If category id hasnt been set then lets set it
		if(!$this->category_id)
			$this->category_id = $_REQUEST['c']['category_id'];

		$top_dropdown .= "<select id=\"".$top_name."\" name=\"".$top_name."\" onClick=\"SendDropDownChange('".$top_name."', '".$bottom_name."', '".$div_name."')\"";
		if($class_name)
			$top_dropdown .= " class=\"".$class_name."\"";
		$top_dropdown .= ">";
		$top_dropdown .= "<option value=\"-1\">Field Not Used</option>";
		foreach ($this->top_dropdown as $key => $value)
		{
			$top_dropdown .= "<option value=\"".$key."\">".$value."</option>\n";
		}
		$top_dropdown .= "</select>";
		$body = $top_dropdown;

		$bottom_dropdown = "<div id=\"".$div_name."\">";
		$bottom_dropdown .= "<select id=\"".$bottom_name."\" name=\"".$bottom_name."\"";
		if($class_name)
			$bottom_dropdown .= " class=\"".$class_name."\"";
		$bottom_dropdown .= ">";
		$bottom_dropdown .= "<option value=\"-1\">Field Not Used</option>";
		$bottom_dropdown .= "</select>";
		$bottom_dropdown .= "</div>";
		$body .= $bottom_dropdown;

		return $body;
	}

	function update_dropdown($top_selection, $bottom_name, $div_name, $second_value=0)
	{
		$body = "<?xml version=\"1.0\" ?>";
		$body .= "<root>";
		$body .= "<div_name>\"".$div_name."\"</div_name>";
		$body .= "<code>";
		$select = "<select id=\"".$bottom_name."\" name=\"".$bottom_name."\">";
		$select .= $this->get_bottom_dropdown($top_selection, $second_value);
		$select .= "</select>";
		$select = urlencode($select);
		// Change +s to %20s for javascripts unescape uses %20s
		$body .= str_replace("+", "%20", $select);

		$body .= "</code>";

		if($second_value)
			$body .= "<second_value>".$second_value."</second_value>";

		$body .= "</root>";
		echo $body;
	}

	function get_bottom_dropdown($top_selection, $second_value)
	{
		foreach ($this->bottom_dropdown as $key => $value)
		{
			// Check for ignore fields
			if($top_selection === "-1")
			{
				$select .= "<option value=\"-1\">Field Not Used</option>\n";
				break;
			}
			elseif(($top_selection == $value[0]))
			{
				// Check for category specific questions
				if($value[0] == "category")
					$select .= "<option value=\"category_spec".$value[1]."\"";
				else
					$select .= "<option value=\"".$value[1]."\"";
			}
			// Check for the "All Fields" selection
			elseif($top_selection === "0")
				$select .= "<option value=\"".$value[1]."\"";

			if($second_value && ($second_value == $value[1]))
					$select .= " selected";

			$select .= ">".$key."</option>\n";
		}

		return $select;
	}

	function add_listing_to_dropdown($listing_type)
	{
		if($listing_type == 2)
		{
			$this->bottom_dropdown["Auction Entry Date"] = "auction";
			$this->bottom_dropdown["Buy Now"] = "auction";
			$this->bottom_dropdown["Reserve Price"] = "auction";
			$this->bottom_dropdown["Minimum Bid"] = "auction";
		}
		elseif($listing_type == 1)
		{
			$this->bottom_dropdown["Classified Entry Date"] = "classified";
			$this->bottom_dropdown["Price"] = "classified";
		}
	}

	function save_new_profile($form_values)
	{
		$this->sql_query = "insert into geodesic_bulk_uploader_profiles ";
		$first_part = "(";
		$second_part = " values (";

		foreach($form_values as $key => $value)
		{
			$first_part .= $key.", ";
			$second_part .= "\"".addslashes($value)."\", ";
		}

		$first_part = rtrim($first_part, " ,");
		$second_part = rtrim($second_part, " ,");

		$first_part .= ") ";
		$second_part .= ")";

		$this->sql_query .= $first_part.$second_part;
		$result = $this->run_query();

		if(!$result)
			echo '<error>Error inserting profile.  Please try again.</error>';
		else
			echo '<root></root>';
	}

	function save_profile_changes($form_values)
	{
		$this->sql_query = "update geodesic_bulk_uploader_profiles set ";

		foreach($form_values as $key => $value)
		{
			if($key == "profile_id")
				continue;
			$this->sql_query .= $key." = \"".$value."\", ";
		}

		$this->sql_query = rtrim($this->sql_query, " ,");

		$this->sql_query .= " where profile_id = ".$form_values['profile_id'];
		$result = $this->run_query();

		if(!$result)
		{
			echo '<error>Error saving profile.  Please try again.</error>';
		}
		else
			echo '<root></root>';
	}

	function load_profile($profile_id)
	{
		$this->sql_query = "select * from geodesic_bulk_uploader_profiles where profile_id = ".$profile_id;
		$result = $this->run_query();
		if(!$result)
		{
			echo '<error>Error getting the profile data</error>';
			return false;
		}
		else
			$profile = $result->GetRowAssoc(false);

		$xml = "<root>";
		foreach($profile as $key => $value)
		{
			// Skip the profile name and profile description and the display_size
			if($key == "profile_name" || $key == "profile_description" || $key == "display_size")
				continue;

			// listing_features_top11
			// row column
			// Check for title fields
			if($key == "title_fields")
			{
				if($value)
				{
					$title = explode("|", $value);
					$title = stripslashes($title);

					// This is an unrolled loop for ease of reading
					$xml .= "<title>";
					$xml .= "<top_select0>".$title[0]."</top_select0>";
					$xml .= "<bottom_select0>".$title[1]."</bottom_select0>";
					$xml .= "<top_select1>".$title[2]."</top_select1>";
					$xml .= "<bottom_select1>".$title[3]."</bottom_select1>";
					$xml .= "<top_select2>".$title[4]."</top_select2>";
					$xml .= "<bottom_select2>".$title[5]."</bottom_select2>";
					$xml .= "<top_select3>".$title[6]."</top_select3>";
					$xml .= "<bottom_select3>".$title[7]."</bottom_select3>";
					$xml .= "</title>";
				}

				continue;
			}

			if($key == "prevalues")
			{
				if($value)
				{
					$prevalues = explode("|", $value);
					$prevalues = stripslashes($prevalues);

					// c[listing_features_top11]', 'c[listing_features_bottom11]
					$xml .= "<prevalues>";
					$xml .= "<listing_features_top11>".$prevalues[0]."</listing_features_top11>";
					$xml .= "<listing_features_bottom11>".$prevalues[1]."</listing_features_bottom11>";
					$xml .= "<listing_features_input11>".$prevalues[2]."</listing_features_input11>";
					$xml .= "<listing_features_top12>".$prevalues[3]."</listing_features_top12>";
					$xml .= "<listing_features_bottom12>".$prevalues[4]."</listing_features_bottom12>";
					$xml .= "<listing_features_input12>".$prevalues[5]."</listing_features_input12>";

					$xml .= "<listing_features_top21>".$prevalues[6]."</listing_features_top21>";
					$xml .= "<listing_features_bottom21>".$prevalues[7]."</listing_features_bottom21>";
					$xml .= "<listing_features_input21>".$prevalues[8]."</listing_features_input21>";
					$xml .= "<listing_features_top22>".$prevalues[9]."</listing_features_top22>";
					$xml .= "<listing_features_bottom22>".$prevalues[10]."</listing_features_bottom22>";
					$xml .= "<listing_features_input22>".$prevalues[11]."</listing_features_input22>";

					$xml .= "<listing_features_top31>".$prevalues[12]."</listing_features_top31>";
					$xml .= "<listing_features_bottom31>".$prevalues[13]."</listing_features_bottom31>";
					$xml .= "<listing_features_input31>".$prevalues[14]."</listing_features_input31>";
					$xml .= "<listing_features_top32>".$prevalues[15]."</listing_features_top32>";
					$xml .= "<listing_features_bottom32>".$prevalues[16]."</listing_features_bottom32>";
					$xml .= "<listing_features_input32>".$prevalues[17]."</listing_features_input32>";

					$xml .= "<listing_features_top41>".$prevalues[18]."</listing_features_top41>";
					$xml .= "<listing_features_bottom41>".$prevalues[19]."</listing_features_bottom41>";
					$xml .= "<listing_features_input41>".$prevalues[20]."</listing_features_input41>";
					$xml .= "<listing_features_top42>".$prevalues[21]."</listing_features_top42>";
					$xml .= "<listing_features_bottom42>".$prevalues[22]."</listing_features_bottom42>";
					$xml .= "<listing_features_input42>".$prevalues[23]."</listing_features_input42>";

					$xml .= "<listing_features_top51>".$prevalues[24]."</listing_features_top51>";
					$xml .= "<listing_features_bottom51>".$prevalues[25]."</listing_features_bottom51>";
					$xml .= "<listing_features_input51>".$prevalues[26]."</listing_features_input51>";
					$xml .= "<listing_features_top52>".$prevalues[27]."</listing_features_top52>";
					$xml .= "<listing_features_bottom52>".$prevalues[28]."</listing_features_bottom52>";
					$xml .= "<listing_features_input52>".$prevalues[29]."</listing_features_input52>";
					$xml .= "</prevalues>";
				}

				continue;
			}

			// TODO put in the break up of fields for:
			// prevalues, title_fields, and field_mappings
			$xml .= "<".$key.">".stripslashes($value)."</".$key.">";
		}

		// Add the display size to the list
		$xml .= "<display_size>".$profile['display_size']."</display_size>";

		// Add the username to the list also
		$this->sql_query = "select username from geodesic_logins where id = ".$profile['user_id'];
		$result = $this->run_query();
		if(!$result)
		{
			echo '<error>Error getting the username from the database</error>';
			return false;
		}
		else
			$name = $result->FetchRow();

		$xml .= "<username>".$name['username']."</username>";

		echo $xml."</root>";
	}

	function run_query($sql_query=0)
	{
		if($sql_query)
			return $this->db->Execute($sql_query);
		else
			return $this->db->Execute($this->sql_query);
	}

	function get_category_dropdown_uploader($db)
	{
		$this->get_subcategories_for_dropdown($db,0,0);

		$body .="<select id=\"c[category_id]\" name=\"c[category_id]\" class=\"category_select\">\n\t\t";
		foreach($this->category_dropdown_name_array as $key => $value)
		{
			$body .="<option ";
			if ($this->category_dropdown_id_array[$key] == $category_id)
				$body .="selected";
			$body .=" value=\"".$this->category_dropdown_id_array[$key]."\">".urldecode($this->category_dropdown_name_array[$key])."</option>\n\t\t";
		}
		$body .="</select>\n\t";

     	return $body;
	} //end of function get_category_dropdown
	
	function getAttentionGetters()
	{
		$sql_query = "select * from ".$this->choices_table." where type_of_choice = 10";
		$result = $this->run_query($sql_query);
		$attentionGetters = array();
		while ($resultRow = $result->FetchRow()) {
			$attentionGetters[$resultRow["display_value"]] = $resultRow["value"];
		}
		return $attentionGetters;
	}
}

// This part is only for servers not running PHP 5
if(!function_exists('file_put_contents'))
{
	define('FILE_APPEND', 1);
	// define('LOCK_EX', 2); - constant defined in PHP, is 2 as well

	function file_put_contents($filename, $data, $flags = 0, $f = FALSE)
	{
		if(($f===FALSE) && (($flags%2)==1))
			$f=fopen($filename, 'a');
		elseif($f===FALSE)
			$f=fopen($filename, 'w');
		if(round($flags/2)==1)
			while(!flock($f, LOCK_EX)) { /* lock */ }
		if(is_array($data))
			$data=implode('', $data);
		fwrite($f, $data);
		if(round($flags/2)==1)
			flock($f, LOCK_UN);
		fclose($f);
	}
}

// Check for previous inclusion
if(!function_exists("array_is_empty"))
{
	// Checks if an array is empty or not
	// returns true or false
	function array_is_empty($array)
	{
		if(!is_array($array))
			return false;

		$empty = true;
		foreach($array as $value)
		{
			if($value)
			{
				$empty = false;
				break;
			}
		}

		return $empty;
	}
}
?>