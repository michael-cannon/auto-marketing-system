<?php
class Admin_store_management extends Admin_store
{
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function Admin_store_management($db, $product_configuration)
	{
		$this->Admin_store($db, $product_configuration);

		// Set the admin_icon variable for the admin icon
		$this->admin_icon = "admin_images/menu_storefront.gif";
	} //end of function Admin_store_management()
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function adminStoreTemplates($db)
	{
		//echo $sql_query." is the query<br>\n";
		$sql_query = "select * from ".$this->templates_table." order by name";
		$templates_result = $db->Execute($sql_query);
		if (!$templates_result)
		{
			//echo $db->ErrorMsg()." is the error<br>\n";
			//echo $sql_query." is the busted query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}
		$this->body .= "
		<script type='text/javascript'>
		var defaultTemplate = 0;
		function displayDefault(templateId)
		{
			if(document.getElementById('generic_'+templateId).checked)
			{
				document.getElementById('default_'+templateId).style.display='none';
			}
			else
			{
				document.getElementById('default_'+templateId).style.display='';
			}
		}
		function disableGeneric(templateId)
		{
			if(document.getElementById('generic_'+defaultTemplate))
				document.getElementById('generic_'+defaultTemplate).disabled=false;
			document.getElementById('generic_'+templateId).disabled=true;
			defaultTemplate = templateId;
		}
		</script>";
		$this->body .= "<form method=post action='index.php?a=201&b=1'>\n";
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 class=row_color1>\n";
		$this->title = "Storefront Management > Storefront Templates";
		$this->description = "Specify which templates can be used by storefront merchants and which one is the default template.";

		$this->body .= "<tr class=row_color_black>
							<td colspan=5 bgcolor=#000066 class=large_font_light>Set Storefront Templates</td>
						</tr>";
		if ($templates_result->RecordCount() > 0)
		{
			$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light>Name </td>\n\t";
			$this->body .= "<td class=medium_font_light>Description </td>\n\t";
			$this->body .= "<td class=medium_font_light>Generic Template </td>\n\t";
			$this->body .= "<td class=medium_font_light>Storefront Template </td>\n\t";
			$this->body .= "<td class=medium_font_light>Default Template </td>\n</tr>\n";

			$this->row_count = 0;
			while ($show_templates = $templates_result->FetchRow())
			{
				$display = $show_templates["storefront_template"]==1 ? "" : "none ";
				$checked = $show_templates["storefront_template"]==1 ? " checked" : "";
				$checkedDefault = $show_templates["storefront_template_default"]==1 ? " checked" : "";
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t".stripslashes($show_templates["name"])." </td>\n\t";
				$this->body .= "<td class=medium_font>\n\t".stripslashes($show_templates["description"])." </td>\n\t";
				$this->body .= "<td style='text-align:right;'><input type=radio name='templateType[".$show_templates["template_id"]."]' id='generic_".$show_templates["template_id"]."' value='0' onClick='javascript:displayDefault(\"".$show_templates["template_id"]."\")' checked title='Generic Template'></td><td style='text-align:center;'><input type=radio name='templateType[".$show_templates["template_id"]."]' id='storefront_".$show_templates["template_id"]."' value='1' onClick='javascript:displayDefault(\"".$show_templates["template_id"]."\")' title='Storefront Template' $checked></td><td><input style='display:$display;' type=radio name=storefrontTemplateDefault value=".$show_templates["template_id"]." id='default_".$show_templates["template_id"]."' onClick='javascript:disableGeneric(\"".$show_templates["template_id"]."\")' title='Default Storefront Template' $checkedDefault></td>\n</tr>\n";
				if($show_templates["storefront_template_default"]==1)
				{
					$this->body .= "
					<script type='text/javascript'>
					disableGeneric('".$show_templates["template_id"]."')
					</script>";
				}
				$this->row_count++;
			}
			$this->body .= "<tr><td class=".$this->get_row_color()." style='text-align:right;' colspan=5><input type=submit name=templateSubmit value=submit></td></tr>\n\t";
		}
		else
		{
			$this->body .= "<tr>\n\t<td colspan class=medium_font>There are no templates currently in the database. </td>\n</tr>\n";
		}
		$this->body .= "</table>\n";

		return true;
	} //end of function adminStoreTemplates()
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function adminStoreTemplatesUpdate($db,$templateTypes,$defaultStoreTemplate=0)
	{
		foreach($templateTypes as $templateId => $templateType)
		{
			$this->sql_query = "update $this->templates_table set
			storefront_template = '$templateType' where
			template_id = '$templateId'";
			$updateResultSet = $db->Execute($this->sql_query);
			if(!$updateResultSet)
				return false;
			if($templateType==1)
			{
				$this->adminStoreSetTemplateModules($db, $templateId);
			}
		}
		$this->sql_query = "update $this->templates_table set
		storefront_template_default = '0' where
		storefront_template_default = '1'";
		$updateResultSet = $db->Execute($this->sql_query);
		if(!$updateResultSet)
			return false;
		$this->sql_query = "update $this->templates_table set
		storefront_template_default = '1' where
		template_id = '$defaultStoreTemplate'";
		$updateResultSet = $db->Execute($this->sql_query);
		if(!$updateResultSet)
			return false;
		return true;
	} //end of function adminStoreTemplatesUpdate()
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function adminStoreSetTemplateModules($db,$templateId)
	{
		//If called from classauctions, grab variables
		$this->storefront_template_modules_table = (!$this->storefront_template_modules_table) ? Admin_store::get("storefront_template_modules_table") :$this->storefront_template_modules_table;
		$this->templates_table = (!$this->templates_table) ? Admin_store::get("templates_table") : $this->templates_table;
		$this->pages_table = (!$this->pages_table) ? Admin_store::get("pages_table") : $this->pages_table;
			
		$this->sql_query = "delete from $this->storefront_template_modules_table where template_id = $templateId";
		$updateTemplateModules = $db->Execute($this->sql_query);
		
		$this->sql_query = "select template_code from $this->templates_table where template_id = $templateId limit 1";
		$templateResults = $db->Execute($this->sql_query);
		$templateResults = $templateResults->FetchRow();
		
		$this->sql_query = "select * from ".$this->pages_table." where module = 1";
		$module_result = $db->Execute($this->sql_query);
		
		while($moduleArray = $module_result->FetchRow())
		{
			if(strstr($templateResults["template_code"], $moduleArray["module_replace_tag"]))
			{
				$this->sql_query = "insert into $this->storefront_template_modules_table 
				(module_id, template_id, connection_time)
				values
				('".$moduleArray["page_id"]."', '".$templateId."', '".time()."')";
				$updateTemplateModules = $db->Execute($this->sql_query);
			}
		}
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function adminUseStorefrontLink($db)
	{
		//echo $sql_query." is the query<br>\n";
		$this->sql_query = "select * from ".$this->classified_categories_table." where parent_id = '0' order by display_order";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//echo $db->ErrorMsg()." is the error<br>\n";
			//echo $sql_query." is the busted query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}
		$this->body .= "
		<script language='JavaScript1.2' type='text/javascript'>
		var categoryElements = new Array();
		function displaySubcategories(categoryId)
		{
			categoryElements = document.getElementById('category_'+categoryId);
			if(categoryElements.style.display == 'none')
				categoryElements.style.display = '';
			else
				categoryElements.style.display = 'none';
		}
		</script>";
		$this->body .= "<form method=post action='index.php?a=201&b=2'>\n";
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 class=row_color1 width=100%>\n";
		$this->title = "Storefront Management > Display Storefront Link";
		$this->description = "Change the storefront link column during the browsing process.  These switches will affect the category browsing process.  If a store owner has placed a listing in a category with this setting turned on, the store's link will be displayed in the storefront column.";
		$this->body .= "<tr bgcolor=#000066>
							<td colspan=3 class=large_font_light>Display Storefront Link</td>
						</tr>";
		$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light width=100%>Name </td>\n\t";
		$this->body .= "<td class=medium_font_light style='text-align:center;width:160px;' nowrap>Display Link </td>\n\t";
		$this->body .= "<td class=medium_font_light style='text-align:center;width:120px;' nowrap>Apply to<br>Subcategories </td>\n</tr><tr><td colspan=3>\n";

		$displayDefault = ($this->configuration_data["display_storefront_link"]==1) ? "checked" : "";
		$this->body .= "<div class='".$this->get_row_color(1)." medium_font' name='category_$categoryId' style='display:table;width:100%;'>\n";

		$this->body .= "<div style='text-align:center;float:left;clear:left;'><b>Site Default</b></div>
						<div style='width:280px;float:right;clear:right;'>
						<div style='text-align:center;width:160px;float:left;'>
							<input type=radio name='site_default' value='0' title='Do not display the storefront link during the browsing process' checked>&nbsp;&nbsp;no
							<input type=radio name='site_default' value='1' title='Display the storefront link during the browsing process' $displayDefault>&nbsp;&nbsp;yes</div>
						<div style='text-align:center;width:120px;float:right;'><input type=checkbox name='applyToSubCategories[0]' id='applyToSubCategories_0' value='1' title=\"Apply this to all the category's subcategories\"></div>\n
						</div>";

		$this->body .= "</div>\n";


		if ($result->RecordCount() > 0)
		{
			$this->buildCategories($db, $result, 0);
		}
		else
		{
			$this->body .= "There are no categories currently in the database.\n";
		}
		$this->body .= "</td></tr><tr><td class=".$this->get_row_color()." style='text-align:right;' colspan=3><input type=submit name=displayLinkSubmit value=submit></td></tr>\n\t";
		$this->body .= "</table>\n";

		return true;
	} //end of function adminUseStorefrontLink()
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function buildCategories($db, $resultSet, $categoryId=0, $catDepth=0)
	{
		$row_count[$categoryId] = 0;
		$catDepth ++;
		$isSubcatDisplay = ($catDepth > 1) ? 'none' : '';
		$this->body .= "<div class=medium_font id='category_$categoryId' style='display:$isSubcatDisplay;width:100%;'>\n";
		while ($show_categories = $resultSet->FetchRow())
		{
			$displaysLink = ($show_categories["display_storefront_link"]==1) ? 'checked' : '';
			$this->sql_query = "select * from ".$this->classified_categories_table." where parent_id = ".$show_categories["category_id"]." order by display_order";
			$result = $db->Execute($this->sql_query);
			$subCategoryCount = $result->RecordCount();

			$noSubCategories = ($subCategoryCount == 0) ? 'none' : '';
			$this->body .= "<div class=".$this->get_row_color($row_count[$categoryId])."  style='width:100%;display:table;'>";
			$this->body .= "\n\t<div style='text-align:center;float:left;clear:left;'>";
			for($lcv=0;$lcv<($catDepth*2);$lcv++)
				$this->body .= "&nbsp;";

			if($subCategoryCount > 0)
				$this->body .= "<a href='javascript: displaySubcategories(\"".$show_categories["category_id"]."\")' class=menu_txt2 style='font-weight:bold;'>";
			$this->body .= "\n\t".stripslashes($show_categories["category_name"]);
			if($subCategoryCount > 0)
				$this->body .= "</a>";

			$this->body .= "</div>\n\t";
			$this->body .= "
			<div style='width:280px;float:right;clear:right'>
			<div style='text-align:center;width:160px;float:left;'>
				<input type=radio name='displayStorefrontLink[".$show_categories["category_id"]."]' id='displayStorefrontLink_".$show_categories["category_id"]."' value='0' title='Do not display the storefront link during the browsing process' checked>&nbsp;&nbsp;no
				<input type=radio name='displayStorefrontLink[".$show_categories["category_id"]."]' id='displayStorefrontLink_".$show_categories["category_id"]."' value='1' title='Display the storefront link during the browsing process' $displaysLink>&nbsp;&nbsp;yes</div>
			<div style='text-align:center;width:120px;float:right;'><input type=checkbox name='applyToSubCategories[".$show_categories["category_id"]."]' id='applyToSubCategories_".$show_categories["category_id"]."' value='1' title=\"Apply this to all the category's subcategories\" style='display:$noSubCategories;'></div>\n
			</div>";
			
			$this->body .= "</div>";
			$row_count[$categoryId]++;

			if ($subCategoryCount > 0)
			{
				$this->buildCategories($db, $result, $show_categories["category_id"], $catDepth);
			}
		}
		$this->body .= "</div>\n";
		$catDepth--;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
function adminUseStorefrontLinkUpdate($db,$siteDefault,$useLinkInCategory,$useLinkInSubCategory=0)
	{
		$this->sql_query = "update $this->site_configuration_table set
		display_storefront_link = $siteDefault";
		$updateResultSet = $db->Execute($this->sql_query);
		if(!$updateResultSet)
			return false;
		else
			$this->configuration_data["display_storefront_link"] = $siteDefault;
		foreach($useLinkInCategory as $categoryId => $categorySetting)
		{
			$this->sql_query = "update $this->classified_categories_table set
			display_storefront_link = '$categorySetting' where
			category_id = '$categoryId'";
			$updateResultSet = $db->Execute($this->sql_query);
			if(!$updateResultSet)
				return false;
		}
		if($useLinkInSubCategory)
		{
			foreach($useLinkInSubCategory as $categoryId => $categorySettings)
			{
				$this->adminUseStorefrontLinkUpdateSubCategories($db,$categoryId);
			}
		}
		return true;
	} //end of function adminUseStorefrontLinkUpdate()
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
function adminUseStorefrontLinkUpdateSubCategories($db,$parentId)
	{
		if($parentId===0){
			$this->sql_query = "update ".$this->classified_categories_table." 
			set display_storefront_link = ".$this->configuration_data["display_storefront_link"]."
			where parent_id = 0";
		}else{
			$this->sql_query = "update ".$this->classified_categories_table." as child, ".$this->classified_categories_table." as parent
			 set child.display_storefront_link = parent.display_storefront_link
			 where child.parent_id = parent.category_id and parent.category_id = '$parentId'";
		}
		$resultSet = $db->Execute($this->sql_query);
		if($resultSet)
		{
			$this->sql_query = "select * from ".$this->classified_categories_table." where parent_id = ".$parentId." order by display_order";
			$resultSet = $db->Execute($this->sql_query);
			while($show_categories = $resultSet->FetchRow())
			{
				$this->adminUseStorefrontLinkUpdateSubCategories($db,$show_categories["category_id"]);
			}
		}
	} //end of function adminStoreTemplatesUpdate()
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function adminStorefrontFieldsToUse($db)
	{
		if ($this->is_class_auctions() || $this->is_auctions())
		{
			$config_title = "Listing Configuration";
			$item_name = "listing";
			$price_name = "Price (Classifieds Only)";
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
			$price_name = "Price";
		}
		// Listings header
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "
			Text[1] = [\"use field\", \"Turning this field off here by unchecking the checkbox will turn it off in the \\\"place ".$item_name."\\\" and \\\"search\\\" processes as well as remove it from the browsing results.\"]\n
			Text[2] = [\"editable field\", \"Checking this checkbox will allow the field to be editable once the ".$item_name." has been placed. If no the seller can only change the value while placing the new ".$item_name.". THIS IS A SITE WIDE SETTING AND NOT CHANGEABLE ON A CATEGORY BY CATEGORY BASIS.\"]\n
			Text[3] = [\"require field\", \"Checking this checkbox will require this field within the ".$item_name." place an ".$item_name." process. The fields that do not have this choice are required if used\"]\n
			Text[4] = [\"display field\", \"Checking this checkbox will display this field as a column while browsing the ".$item_name."s.\"]\n
			Text[5] = [\"field length\", \"This value will determine maximum amount of characters or numbers a user can place into this field while placing their ".$item_name.". The maximum number of characters that can be placed in any field is 256.\"]\n
			Text[6] = [\"photo icon/thumbnail (while browsing)\", \"Checking this checkbox will display the photo column next to the title column (at the left side of the browsing results). You can choose below whether or not to display the photo icon referenced below or the first image of the ".$item_name." in the selection below.\"]\n
			Text[7] = [\"PayPal \\\"Buy Now\\\"\", \"\"]\n
			Text[8] = [\"entry data (while browsing)\", \"\"]\n
			Text[9] = [\"payment types\", \"\"]\n
			Text[10] = [\"time left\", \"\"]\n
			Text[11] = [\"number of bids\", \"\"]\n
			Text[12] = [\"editable start bidding time field\", \"\"]\n
			Text[13] = [\"automatic line breaks on text areas\", \"Choosing \\\"yes\\\" will set the wrap attribute for any text area field for the place an ".$item_name." process. For example if a user clicks the return/enter key on their keyboard while typing their description the carriage return will be stored in the database and show in the display. Will help if you want your users to be able to create lists in their description or any other field that is set as a text area. DO NOT USE THIS IN CONJUNCTION WITH RICH TEXT EDITOR.\"]\n
			Text[14] = [\"display the description below the title\", \"If you choose to display the description while browsing you can choose to display the description in its own column or below the title.\"]\n
			Text[15] = [\"length of description to display (while browsing)\", \"Number of characters to display when users browse ".$item_name."s\"]\n
			Text[16] = [\"editable category specific questions\", \"Choosing \\\"yes\\\" will allow the seller to edit the category specific fields attached to their ".$item_name." once there ".$item_name." has been placed. If no is checked the seller will only be able to change the category specific questions attached to their ".$item_name." while placing their ".$item_name.". THIS IS A SITE WIDE SETTING AND NOT CHANGEABLE ON A CATEGORY BY CATEGORY BASIS\"]\n
			Text[17] = [\"entry date display configuration\", \"Choose which format you wish to display your entry date by choosing one of the configurations below.\"]\n
			Text[18] = [\"price (Classifieds Only)\", \"These settings apply to classifieds only.\"]\n

			Text[19] = [\"other box\", \"\"]\n
			Text[20] = [\"number only\", \"\"]\n
			Text[21] = [\"type\", \"\"]\n
			";

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$sql_query = "select * from ".$this->storefront_display_table;
		$site_result = $db->Execute($sql_query);
		if (!$site_result)
		{
			$this->site_error($sql_query,$db->ErrorMsg());
			return false;
		}
		if($site_result->RecordCount()==0)
		{
			$sql_query = "INSERT INTO `geodesic_storefront_display` ( `display_business_type` , `use_site_default` , `display_photo_icon` , `display_price` , `display_browsing_zip_field` , `display_browsing_city_field` , `display_browsing_state_field` , `display_browsing_country_field` , `display_entry_date` , `display_optional_field_1` , `display_optional_field_2` , `display_optional_field_3` , `display_optional_field_4` , `display_optional_field_5` , `display_optional_field_6` , `display_optional_field_7` , `display_optional_field_8` , `display_optional_field_9` , `display_optional_field_10` , `display_optional_field_11` , `display_optional_field_12` , `display_optional_field_13` , `display_optional_field_14` , `display_optional_field_15` , `display_optional_field_16` , `display_optional_field_17` , `display_optional_field_18` , `display_optional_field_19` , `display_optional_field_20` , `display_ad_description` , `display_ad_description_where` , `display_all_of_description` , `display_ad_title` , `display_number_bids` , `display_time_left` )
							VALUES (
							'0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '0'
							)";
			$site_result = $db->Execute($sql_query);
			if (!$site_result)
			{
				$this->site_error($sql_query,$db->ErrorMsg());
				return false;
			}else{
				$sql_query = "select * from ".$this->storefront_display_table;
				$site_result = $db->Execute($sql_query);
			}
		}
		if ($site_result->RecordCount() == 1)
		{
			$site_configuration = $site_result->FetchRow();

			$this->title = "Storefront Management > Storefront Fields to Display";
			$this->description = "Set the fields to be displayed during the browsing process of a user's storefront.";

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
				</script>
				<form name=fields_to_use action=index.php?a=201&b=3 method=post>
				<table cellpadding=3 cellspacing=2 border=0 width=100% align=center class=row_color1>\n";
			if ($this->ad_configuration_message)
				$this->body .= "
					<tr>
						<td colspan=2 class=medium_error_font>".$this->ad_configuration_message."</td>
					</tr>";

			// Block of checkboxes for major settings
			$this->body .= "
					<tr class=row_color_black>
						<td colspan=2 class=large_font_light bgcolor=#000066>Fields to Display</td>
					</tr>
					<tr class=row_color_black>
						<td align=left class=medium_font_light><b>field</b></td>
						<td align=center class=medium_font_light><b>display</b>".$this->show_tooltip(4,1)."</td>
					</tr>";
			$this->row_count=0;
			if ($this->is_class_auctions() || $this->is_auctions())
			{
				//display photo column
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=left valign=top class=medium_font>
									Photo Icon/Thumbnail (while browsing)".$this->show_tooltip(6,1)."
								</td>
								<td align=center valign=top class=medium_font>
									<input id=display type=checkbox name=c[display_photo_icon] value=1 "
									.(($site_configuration['display_photo_icon']==1) ? "checked" : "").">
								</td>
							</tr>";$this->row_count++;
			}

			// Title Field
			$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=left valign=top class=medium_font>Title</td>
								<td align=center valign=top class=medium_font>
									<input id=display type=checkbox name=c[display_ad_title] value=1 "
									.(($site_configuration['display_ad_title']==1) ? "checked" : "").">
								</td>
							</tr>";$this->row_count++;

			// Description Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=left valign=top class=medium_font>Description</td>
								<td valign=top align=center class=medium_font>
									<input id=display type=checkbox name=c[display_ad_description] value=1 "
									.(($site_configuration['display_ad_description']==1) ? "checked" : "").">
								</td>
							</tr>";$this->row_count++;

			if ($this->is_class_auctions() || $this->is_classifieds())
			{
				// Price Field
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=left valign=top class=medium_font>".$price_name.$this->show_tooltip(18,1)."</td>
								<td valign=top align=center class=medium_font>
									<input id=display type=checkbox name=c[display_price] value=1 "
									.(($site_configuration['display_price']==1) ? "checked" : "").">
								</td>
							</tr>";$this->row_count++;
			}

			// Country Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=left valign=top class=medium_font>Country</td>
								<td valign=top align=center class=medium_font>
									<input id=display type=checkbox name=c[display_browsing_country_field] value=1 "
									.(($site_configuration['display_browsing_country_field']==1) ? "checked" : "").">
								</td>
							</tr>";$this->row_count++;

			// State Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=left valign=top class=medium_font>State</td>
								<td valign=top align=center class=medium_font>
									<input id=display type=checkbox name=c[display_browsing_state_field] value=1 "
									.(($site_configuration['display_browsing_state_field']==1) ? "checked" : "").">
								</td>
							</tr>";$this->row_count++;

			// City Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=left valign=top class=medium_font>City</td>
								<td valign=top align=center class=medium_font>
									<input id=display type=checkbox name=c[display_browsing_city_field] value=1 "
									.(($site_configuration['display_browsing_city_field']==1) ? "checked" : "").">
								</td>
							</tr>";$this->row_count++;

			// Zip Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=left valign=top class=medium_font>Zip</td>
								<td valign=top align=center class=medium_font>
									<input id=display type=checkbox name=c[display_browsing_zip_field] value=1 "
									.(($site_configuration['display_browsing_zip_field']==1) ? "checked" : "").">
								</td>
							</tr>";$this->row_count++;

			//display date posted column
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=left valign=top class=medium_font>Entry Date (while browsing) ".$this->show_tooltip(8,1)."</td>
								<td align=center valign=top class=medium_font>
									<input id=display type=checkbox name=c[display_entry_date] value=1 "
									.(($site_configuration['display_entry_date']==1) ? "checked" : "").">
								</td>
							</tr>";$this->row_count++;

			//display date posted column
			$this->body .= "<tr class=".$this->get_row_color().">
									<td align=left valign=top class=medium_font>Business Type</td>
								<td align=center valign=top class=medium_font>
									<input id=display type=checkbox name=c[display_business_type] value=1 "
									.(($site_configuration['display_business_type']==1) ? "checked" : "").">
								</td>
							</tr>";$this->row_count++;

			if ($this->is_class_auctions() || $this->is_auctions())
			{
				//display price column
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=left valign=top class=medium_font>Time Left (while browsing)".$this->show_tooltip(10,1)."</td>
								<td align=center valign=top class=medium_font>
									<input id=display type=checkbox name=c[display_time_left] value=1 "
									.(($site_configuration['display_time_left']==1) ? "checked" : "").">
								</td>
							</tr>";$this->row_count++;

				//display number of bids left column
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=left valign=top class=medium_font>Number of Bids (while browsing)".$this->show_tooltip(11,1)."</td>
								<td align=center valign=top class=medium_font>
									<input id=display type=checkbox name=c[display_number_bids] value=1 "
									.(($site_configuration['display_number_bids']==1) ? "checked" : "").">
								</td>
							</tr>";$this->row_count++;
			}

			$this->row_count=0;
			//Optional Fields
			for($i = 1; $i < 21; $i++)
			{
				$this->body .= "	<tr class=".$this->get_row_color().">
										<td align=left valign=top class=medium_font>optional field $i</td>
										<td align=center valign=top class=medium_font>
											<input id=display type=checkbox name=c[display_optional_field_".$i."] value=1 ".
											(($site_configuration['display_optional_field_'.$i] == 1) ? "checked" : "").">
										</td>
									</tr>";$this->row_count++;
			}

			$this->body .= "<tr class=row_color_black>
								<td>&nbsp;</td>
								<td align=center><input id=display_all onclick=\"javascript:check_all(document.fields_to_use,'display');\" type=checkbox></td>
								</tr>";


			$this->body .= "<tr>
								<td colspan=2 align=center class=medium_font>
									<input type=submit value=\"Save\" name=submitFieldsToUse>
									<input onclick=\"reset()\" type=\"button\" value=\"reset form\">
								</td>
							</tr>
					</form>
				</table>";
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

	function adminStorefrontFieldsToUseUpdate($db,$site_config_info=0)
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
			"display_business_type",
			"display_time_left",
			"display_number_bids"
			);
		for($i=1;$i<21;$i++)
		{
			array_push($site_fields,"display_optional_field_".$i);
		}
		$this->sql_query = "update ".$this->storefront_display_table." set ";
		foreach ($site_fields as $value)
		{
			$this->sql_query .= $value." = \"".($site_config_info[$value] ? $site_config_info[$value] : 0)."\", ";
		}
		$this->sql_query = rtrim($this->sql_query, ' ,');//strip off comma
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			echo $this->sql_query."<br>\n";
			return false;
		}
		return true;
	} // end of function adminStorefrontFieldsToUseUpdate

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_subscription_periods($db)
	{
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center width=100% class=row_color1>\n";
		$this->title = "Storefront Management > Edit Subscription Periods";
		$this->description = "Below are your storefront subscription choices.  Delete choices by clicking the delete link next to the
			appropriate choice.  Add new choices by clicking the add new choice link at the bottom.";

		$this->body .= "<tr bgcolor=000066>\n\t<td colspan=5 class=large_font_light align=center>\n\t<b>Storefront Subscription Choices</b>\n\t</td>\n</tr>\n";
		$this->sql_query = "select * from ".$this->storefront_subscriptions_choices_table." order by value";
		//echo $this->sql_query." is query1<br>";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->body .= "<tr class=row_color_black>\n\t
				<td class=medium_font_light>\n\t<b>length of period</b> \n\t</td>\n\t
				<td class=medium_font_light align=center>\n\t<b>cost</b> \n\t</td>\n\t
				<td class=medium_font_light>\n\t&nbsp; \n\t</td>\n\t
				<td class=medium_font_light>\n\t&nbsp; \n\t</td>\n\t
				</tr>\n";
			$this->row_count = 0;
			while ($show_subscriptions = $result->FetchRow())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t
					<td class=medium_font>\n\t".$show_subscriptions["display_value"]." \n\t</td>\n\t
					<td class=medium_font align=center>\n\t".sprintf("%0.2f",$show_subscriptions["amount"])." \n\t</td>\n\t
					<td align=center width=100>\n\t<a href=index.php?a=201&b=4&c=edit&h=".$show_subscriptions["period_id"]."><span class=medium_font>\n\t<img src=\"admin_images/btn_admin_edit.gif\" border=0></span></a>\n\t</td>\n\t
					<td align=center width=100>\n\t<a href=index.php?a=201&b=4&c=delete&h=".$show_subscriptions["period_id"]."><span class=medium_font>\n\t<img src=\"admin_images/btn_admin_delete.gif\" border=0></span></a>\n\t</td>\n\t
					</tr>\n";
				$this->row_count++;
			}
		}
		else
		{
			//none...allow to add
			$this->body .= "<tr>\n\t<td colspan=4 class=medium_font align=center>\n\t<br><br><b>There are currently no Subscription Periods for your Storefronts.</b><br><br> </td>\n</tr>\n";
		}
		$this->body .= "<tr>\n\t<td colspan=4 align=center>\n\t<a href=index.php?a=201&b=4&c=add>
			<span class=medium_font>\n\t<b>add new subscription period choice</b></span></a></td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;
	} //end of function display_subscription_periods

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function subscription_period_form($db, $periodId=0)
	{
			if($periodId)
			{
				$this->sql_query = "select * from ".$this->storefront_subscriptions_choices_table." where period_id = $periodId";
				$result = $db->Execute($this->sql_query);
				if(!$result)
					return false;
				$show_subscriptions = $result->FetchRow();
				$this->title = "Storefront Management > Edit A Subscription Period";
				$this->description = "Use this form to edit a Subscription Period for your Storefronts.
				Enter the specifics below and then click the \"edit choice\" button at the bottom.";
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=201&b=4&c=edit&h=$periodId method=post>\n";
				$title = "Edit Subscription Choice";
			}else{
				$this->title = "Storefront Management > Add A Subscription Period";
				$this->description = "Use this form to enter a new Subscription Period for your Storefronts.
					Enter the specifics below and then click the \"enter choice\" button at the bottom.";
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=201&b=4&c=add method=post>\n";
				$title = "Add Subscription Choice";
			}
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";

			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=5 class=large_font_light align=center>\n\t<b><b>$title</b></b>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1>
				<td align=right width=50% class=medium_font>\n\tdisplay value: \n\t</td>\n\t
				<td class=medium_font>\n\t<input type=text name=d[display_value] value='".$show_subscriptions["display_value"]."'> ie 30 days \n\t</td>\n\t
				</tr>\n";
			$this->body .= "<tr class=row_color2>
				<td align=right width=50% class=medium_font>\n\tnumber of days of subscription period: \n\t</td>\n\t
				<td class=medium_font>\n\t<select name=d[value]>";
			for ($i=1;$i < 1826;$i++)
			{
				$selected = ($show_subscriptions["value"]==$i) ? 'selected' : '';
				$this->body .= "<option $selected>".$i."</option>\n\t";
			}
			$this->body .= "</select> \n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color1>
				<td align=right width=50% class=medium_font>\n\tamount to charge for period: \n\t</td>\n\t
				<td class=medium_font>\n\t<select name=d[period_dollars]>";
			for ($i=0;$i < 1001;$i++)
			{
				$amount = explode('.', $show_subscriptions["amount"]);
				$selected = ($amount[0]==$i) ? 'selected' : '';
				$this->body .= "<option $selected>".$i."</option>\n\t";
			}
			$this->body .= "</select>\n<select name=d[period_cents]>";
			for ($i=0;$i < 100;$i++)
			{
				$amount = explode('.', $show_subscriptions["amount"]);
				$selected = ($amount[1]==$i) ? 'selected' : '';
				$this->body .= "<option $selected>".sprintf("%02d",$i)."</option>\n\t";
			}
			$this->body .= "</select> \n\t</td>\n</tr>\n";
			if($periodId){
				if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 align=center><input type=button value=\"cancel\" onClick=\"javascript: window.location='index.php?a=201&b=4';\">&nbsp;<input type=submit name=editChoice value=\"edit choice\">\n\t</td>\n</tr>\n";
			}else{
				if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 align=center><input type=button value=\"cancel\" onClick=\"javascript: window.location='index.php?a=201&b=4';\">&nbsp;<input type=submit name=addNewChoice value=\"enter choice\">\n\t</td>\n</tr>\n";
			}
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
	} //end of subscription_period_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_subscription_period($db,$subscription_info=0,$periodId=0)
	{
		if ($subscription_info)
		{
			if($periodId){
				$this->sql_query = "update ".$this->storefront_subscriptions_choices_table." set
				display_value = \"".$subscription_info["display_value"]."\",
				value = ".$subscription_info["value"].",
				amount = ".$subscription_info["period_dollars"].".".$subscription_info["period_cents"]."
				where period_id = $periodId";
			}else{
				$this->sql_query = "insert into ".$this->storefront_subscriptions_choices_table."
				(display_value,value,amount)
				values
				(\"".$subscription_info["display_value"]."\",".$subscription_info["value"].",".$subscription_info["period_dollars"].".".$subscription_info["period_cents"].")";

			}
			$insert_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$insert_result)
			{
				$this->error_message = $this->internal_error_message;
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
	} //end of function insert_subscription_period

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_subscription_period($db,$subscription_period_id=0)
	{
		if ($subscription_period_id)
		{
			$this->sql_query = "delete from ".$this->storefront_subscriptions_choices_table." where period_id = ".$subscription_period_id;
			$delete_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$delete_result)
			{
				$this->error_message = $this->internal_error_message;
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
	} //end of function insert_subscription_period

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function storefront_home($db)
	{
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center width=100% class=row_color1>\n";
		$this->title = "Storefront Management";
		$this->description = "Use these tools below to manage your storefront settings.";

		$this->body .= "<tr bgcolor=000066>\n\t<td class=large_font_light align=center>\n\t<b>Storefront Management Tools</b>\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=2 class='medium_font row_color1'>&bull;<a href='index.php?a=201&b=1' class=menu_txt2>Storefront Templates</a>\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=2 class='medium_font row_color2'>&bull;<a href='index.php?a=201&b=2' class=menu_txt2>Display Storefront Link</a>\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=2 class='medium_font row_color1'>&bull;<a href='index.php?a=201&b=3' class=menu_txt2>Fields to Display</a>\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=2 class='medium_font row_color2'>&bull;<a href='index.php?a=201&b=4' class=menu_txt2>Storefront Subscription Choices</a>\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=2 class='medium_font row_color1'>&bull;<a href='index.php?a=28&z=6#storefrontUrl' class=menu_txt2>Storefront Url</a>\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;
	} //end of function display_subscription_periods

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

//STOREFRONT CODE
}
?>