<?//admin_categories_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
\**************************************************************************/

class Admin_categories extends Admin_site{

	var $current_category;
	var $in_statement;

	var $messages = array();
	var $debug_categories = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Admin_categories ($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);

		$this->messages[3500] = "Not enough information to display the category";
		$this->messages[3501] = "Internal browse error!";
		$this->messages[3502] = "no category id";
		$this->messages[3503] = "cannot update the main category";
		$this->messages[3504] = "Category ";
		$this->messages[3505] = "Subcategories of ";
		$this->messages[3506] = "There are currently no subcategories to display for this Category.";
		$this->messages[3507] = "subcategories exist";
		$this->messages[3508] = "<img src=admin_images/btn_admin_delete.gif alt=delete border=0>";
		$this->messages[3509] = "cannot delete the Main category";
		$this->messages[3510] = "There was an error processing your request";
	} //end of function Admin_categories

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_category_form ($db,$category=0,$type=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"category order\", \"Category order determines the order the categories when this categories parent category is displayed.\"]\n
			Text[2] = [\"url of category image\", \"This is the url of the image icon this category uses when this category's name is being displayed.  If no image is referenced no image will be displayed.\"]\n
			Text[3] = [\"edit the category specific fields used/displayed within category\", \"Click here to edit which fields you wish to use and display within this category (and subcategories if you choose to).\"]\n
			Text[4] = [\"edit listing lengths for this category\", \"Click here to edit the lengths that will appear for this category.  If you do not enter any lengths for this category or any of its direct parent categories the site defaults set within  LISTING SETUP > LENGTH OF LISTING will be used.\"]\n
			Text[5] = [\"edit this category's name, description and templates\", \"Click here to edit the listing used for this category.\"]\n
			Text[6] = [\"category description\", \"This description will appear below the category name while browsing the listings if you choose to display the category discriptions set in the SITE SETUP > BROWSING page.\"]\n
			Text[7] = [\"Listing types allowed in this category\", \"These are the allowed types of listings that can be placed in this category\"]\n";

		//".$this->show_tooltip(6,1)."

		// Set style for tooltip
		//$this->body .= "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		if ($category)
		{
			//edit this category after getting current info
			$show_category_name = $this->get_category_name($db,$category);
			$this->sql_query = "select * from ".$this->classified_categories_table." where category_id = ".$category;
			$result = $db->Execute($this->sql_query);
			 if (!$result)
			 {
				//$this->body .= $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3501];
				return false;
			 }
			 elseif ($result->RecordCount() == 1)
			 {
			 	$show_category = $result->FetchRow();
			 }
			 else
			 {
			 	return false;
			 }

			if (!$type)
			{
				//edit
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=6&c=".$category." method=post>\n";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% class=".$this->get_row_color().">\n";
				$this->title .= "Categories Setup > Edit";
				$this->description .= "Edit this category's name, description, display order and image associated with it within this form.";

				$this->row_count = 0;
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\tcategory order:".$this->show_tooltip(1,1)."</td>\n\t";
				$this->body .= "<td valign=top>\n\t<select name=d[display_order]>\n\t\t";
				$this->row_count++;

				for ($i=1;$i<500;$i++)
				{
					$this->body .= "<option ";
					if ($i == $show_category["display_order"])
						$this->body .= "selected";
					$this->body .= ">".$i."</option>\n\t\t";
				}
				$this->body .= "</select>\n\t</td>\n</tr>\n";

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\turl of category image:".$this->show_tooltip(2,1)."</td>\n\t";
				$this->body .= "<td valign=top>\n\t<input type=text name=d[category_image] value=\"".$show_category["category_image"]."\" size=30 maxsize=100>\n\t</td>\n</tr>\n";
				$this->row_count++;

				if($this->is_class_auctions())
				{
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\tListing types allowed in this category:".$this->show_tooltip(7,1)."</td>\n\t";
					$this->body .= "<td valign=top class=medium_font>";
					$this->body .= "<input type=\"radio\" name=\"d[listing_types_allowed]\" value=\"0\" ";
					if(($show_category['listing_types_allowed'] == 0))
						$this->body .= "checked";
					$this->body .= ">Classified Ads and Auctions<Br>";
					$this->body .= "<input type=\"radio\" name=\"d[listing_types_allowed]\" value=\"1\" ";
					if($show_category['listing_types_allowed'] == 1)
						$this->body .= "checked";
					$this->body .= ">Classified Ads only<Br>";
					$this->body .= "<input type=\"radio\" name=\"d[listing_types_allowed]\" value=\"2\" ";
					if($show_category['listing_types_allowed'] == 2)
						$this->body .= "checked";
					$this->body .= ">Auctions only";
					$this->body .= "</td></tr>";
					$this->row_count++;
				}

				//$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\turl of request category image<br>
				//	<span class=small_font>this is the url of the image icon this category uses when this categorys name is being displayed.  If
				//	no image is referenced no image will be displayed.</span></td>\n\t";
				//$this->body .= "<td valign=top>\n\t<input type=text name=d[request_category_image] value=\"".$show_category["request_category_image"]."\" size=30 maxsize=100>\n\t</td>\n</tr>\n";

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\tedit the category specific fields used/displayed within category:".$this->show_tooltip(3,1)."</td>\n\t";
				$this->body .= "<td><a href=index.php?a=6&c=".$category."&z=1><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></a></span></td></tr>\n";
				$this->row_count++;

				//$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>click to edit the request category specific fields used and displayed
				//	within  category</a><br>
				//	<span class=small_font>click here to edit which request fields you wish to use and display within this category (and
				//	subcategories if you choose to).</span></td>\n\t";
				//$this->body .= "<td><a href=index.php?a=6&c=".$category."&z=1&t=2><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></a></td></tr>\n";


				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\tedit listing lengths for this category:".$this->show_tooltip(4,1)."</td>\n\t";
				$this->body .= "<td><a href=index.php?a=6&c=".$category."&z=2><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></a></span></td></tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>edit this category's name, description and templates:".$this->show_tooltip(5,1)."</td>\n\t";
				$this->body .= "<td><a href=index.php?a=6&c=".$category."&z=3><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></a></span></td></tr>\n";
				$this->row_count++;

				//$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>click to edit request templates for this category</a><br>
				//	<span class=small_font>click here to edit the listings used for this category.</span></td>\n\t";
				//$this->body .= "<td><a href=index.php?a=6&c=".$category."&z=3&t=2><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></a></td></tr>\n";
				if (!$this->admin_demo())
					$this->body .= "<tr>\n\t<td colspan=2 align=center><input type=submit value=\"Save\"></td>\n</tr>\n";

				$this->body .= "</table>\n</form>\n";
			}
			else
			{
				//add
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=2&c=".$category." method=post>\n";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% class=".$this->get_row_color().">\n";
				$this->title .= "Categories Setup > New Category";
				$this->description .= "	Insert a new Subcategory into the <b>".$show_category_name."</b> Category.";

				//$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\tcategory name \n\t</td>\n\t";
				//$this->body .= "<td valign=top>\n\t<input type=text name=b[category_name]>\n\t</td>\n</tr>\n";


				//$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\tcategory description:".$this->show_tooltip(6,1)."</td>\n\t";
				//$this->body .= "<td valign=top>\n\t<textarea name=b[description] cols=30 rows=3></textarea>\n\t</td>\n</tr>\n";

				$this->row_count = 0;
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>category order:</b>".$this->show_tooltip(1,1)."</td>\n\t";
				$this->body .= "<td>\n\t<select name=b[display_order]>\n\t\t";
				for ($i=1;$i<500;$i++)
				{
					$this->body .= "<option ";
					if ($i == $show_category["display_order"])
						$this->body .= "selected";
					$this->body .= ">".$i."</option>\n\t\t";
				}
				$this->body .= "</select>\n\t</td>\n</tr>\n";
				$this->row_count++;

				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>url of category image:</b>".$this->show_tooltip(2,1)."</td>\n\t";
				$this->body .= "<td valign=top>\n\t<input type=text name=b[category_image] size=30 maxsize=100>\n\t</td>\n</tr>\n";
				$this->row_count++;

				if($this->is_class_auctions())
				{
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\tListing types allowed in this category:".$this->show_tooltip(7,1)."</td>\n\t";
					$this->body .= "<td valign=top class=medium_font>";
					$this->body .= "<input type=\"radio\" name=\"b[listing_types_allowed]\" value=\"0\" ";
					if(($show_category['listing_types_allowed'] == 0))
						$this->body .= "checked";
					$this->body .= ">Classified Ads and Auctions<Br>";
					$this->body .= "<input type=\"radio\" name=\"b[listing_types_allowed]\" value=\"1\" ";
					if($show_category['listing_types_allowed'] == 1)
						$this->body .= "checked";
					$this->body .= ">Classified Ads only<Br>";
					$this->body .= "<input type=\"radio\" name=\"b[listing_types_allowed]\" value=\"2\" ";
					if($show_category['listing_types_allowed'] == 2)
						$this->body .= "checked";
					$this->body .= ">Auctions only";
					$this->body .= "</td></tr>";
					$this->row_count++;
				}

				$this->sql_query = "select distinct(language_id) from ".$this->pages_languages_table." order by language_id asc";
				$language_result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is the query<br>\n";
				if (!$language_result)
				 {
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[3501];
					return false;
				 }
				 elseif ($language_result->RecordCount() > 0)
				 {
				 	while ($show = $language_result->FetchRow())
				 	{
				 		$this->body .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light>\n\tcategory name and description for: <b>".$this->get_language_name($db,$show["language_id"])."</b> language \n\t</td>\n\t";
						$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>category name:</b> \n\t</td>\n\t";
						$this->body .= "<td valign=top>\n\t<input type=text name=b[".$show["language_id"]."][category_name] value=\"".$show_category_name."\">\n\t</td>\n</tr>\n";
						$this->row_count++;

						$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t<b>category description:</b>".$this->show_tooltip(6,1)."</span></td>\n\t";
						$this->body .= "<td valign=top>\n\t<textarea name=b[".$show["language_id"]."][description] cols=30 rows=3></textarea>\n\t</td>\n</tr>\n";
						$this->row_count++;
					}
				}

				if (!$this->admin_demo())
					$this->body .= "<tr>\n\t<td colspan=2 align=center><input type=submit value=\"Save\"></td>\n</tr>\n";

				$this->body .= "</table>\n</form>\n";
			}
		}
		else
		{
			//this is the main category
			//you can only add a category to the main category
			//there is no edit of the main category
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=2&c=0 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=100% class=".$this->get_row_color().">\n";
			$this->title .= "Categories Setup > New Category";
			$this->description .= "Insert a new <b>Main</b> category.";

			//echo "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\tcategory name \n\t</td>\n\t";
			//echo "<td>\n\t<input type=text name=b[category_name]>\n\t</td>\n</tr>\n";

			//echo "<tr>\n\t<td align=right valign=top class=medium_font>\n\tcategory description:".$this->show_tooltip(6,1)."</td>\n\t";
			//echo "<td>\n\t<textarea name=b[description]></textarea>\n\t</td>\n</tr>\n";

			$this->row_count = 0;
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\tcategory order:".$this->show_tooltip(1,1)."</td>\n\t";
			$this->body .= "<td>\n\t<select name=b[display_order]>\n\t\t";
			for ($i=1;$i<500;$i++)
			{
				$this->body .= "<option ";
				if ($i == $show_category["display_order"])
					$this->body .= "selected";
				$this->body .= ">".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";
			$this->row_count++;

			$this->body .= "<tr ".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\turl of category image:".$this->show_tooltip(2,1)."</td>\n\t";
			$this->body .= "<td valign=top>\n\t<input type=text name=b[category_image] value=\"".$show_category["category_image"]."\" size=30 maxsize=100>\n\t</td>\n</tr>\n";
			$this->row_count++;

			if($this->is_class_auctions())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\tListing types allowed in this category:".$this->show_tooltip(7,1)."</td>\n\t";
				$this->body .= "<td valign=top class=medium_font>";
				$this->body .= "<input type=\"radio\" name=\"b[listing_types_allowed]\" value=\"0\" ";
				if(($show_category['listing_types_allowed'] == 0))
					$this->body .= "checked";
				$this->body .= ">Classified Ads and Auctions<Br>";
				$this->body .= "<input type=\"radio\" name=\"b[listing_types_allowed]\" value=\"1\" ";
				if($show_category['listing_types_allowed'] == 1)
					$this->body .= "checked";
				$this->body .= ">Classified Ads only<Br>";
				$this->body .= "<input type=\"radio\" name=\"b[listing_types_allowed]\" value=\"2\" ";
				if($show_category['listing_types_allowed'] == 2)
					$this->body .= "checked";
				$this->body .= ">Auctions only";
				$this->body .= "</td></tr>";
				$this->row_count++;
			}

			$this->sql_query = "select distinct(language_id) from ".$this->pages_languages_table." order by language_id asc";
			$language_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			 if (!$language_result)
			 {
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3501];
				return false;
			 }
			 elseif ($language_result->RecordCount() > 0)
			 {
			 	while ($show = $language_result->FetchRow())
			 	{
			 		$this->body .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light>\n\tcategory name and description for: <b>".$this->get_language_name($db,$show["language_id"])."</b> language</td>\n\t";
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\tcategory name:</td>\n\t";
					$this->body .= "<td valign=top>\n\t<input type=text name=b[".$show["language_id"]."][category_name] value=\"".$show_category_name."\">\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\tcategory description:".$this->show_tooltip(6,1)."</td>\n\t";
					$this->body .= "<td valign=top>\n\t<textarea name=b[".$show["language_id"]."][description] cols=30 rows=3></textarea>\n\t</td>\n</tr>\n";
					$this->row_count++;
				}
			}


			if (!$this->admin_demo())
				$this->body .= "<tr>\n\t<td align=center colspan=2><input type=submit value=\"Save\"></td>\n</tr>\n";

			$this->body .= "</table>\n</form>\n";
		}
		return true;

	} //end of function display_category_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function category_templates_form ($db,$category=0,$type=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"category description\", \"This description will appear below the category name while browsing the listings if you choose to display the category descriptions set in the site configuration > browsing page.\"]\n
			Text[2] = [\"category home template\", \"Choose a template used for this category's home page. If no template is chosen for this category the default template set in the PAGES > BROWSING > BROWSE CATEGORIES will be used. Enter all templates through the template administration.\"]\n
			Text[3] = [\"category secondary template\", \"Choose a template used for this category's subsequent browsing page. This template will be used for this categorys pages 2 and up in its browsing results. If no template is chosen for this category the default template set in the PAGES > BROWSING > BROWSE CATEGORIES will be used. Enter all templates through the template administration.\"]\n
			Text[4] = [\"category listing page template\", \"Choose a template used for this category and languages subsequent browsing page. This template will be used for this categorys pages 2 and up in its browsing results. If no template is chosen for this category the default template set in the PAGES > BROWSING > LISTING DISPLAY PAGE will be used. Enter all templates through the template administration.\"]\n
			Text[5] = [\"category listing detail template (body of listing itself not the page that surrounds it)\", \"Choose a template used for this category's listing detail within the listing display template referenced in the previous. If no template is chosen for this category the default listing display template for the site will be used. Enter templates used for the category's listing detail display through the template administration.\"]\n
			Text[6] = [\"category listing detail extra question template (body of listing itself not the page that surrounds it)\", \"Choose a template used for this category's extra questions (category specific questions you generate and attach to a category). If no extra question template is chosen for this category the default extra question template for the site will be used. Enter templates used for the category specific purpose through the template administration.\"]\n
			Text[7] = [\"category listing detail checkbox question template (body of listing itself not the page that surrounds it)\", \"Choose a template used for this category's checkbox questions (category specific questions you generate and attach to a category). If no checkbox question template is chosen for this category the default site template for checkbox questions will be used. Enter templates used for the category's listing detail display through the template administration.\"]\n
			Text[8] = [\"full size image template (connected to each listing itself not the page that surrounds it)\", \"Choose a template used for this category's full size image page. If no full size image template is chosen for this category the default full size image template for the site will be used. Enter templates used for the category specific purpose through the template administration.\"]\n
			Text[9] = [\"print friendly listing display template (connected to each listing details itself not the page that surrounds it)\", \"Choose a template used for this category's print friendly page. If no print friendly template is chosen for this category the default print friendly template for the site will be used. Enter templates used for the category specific purpose print friendly display through the template administration.\"]\n
			Text[10] = [\"print friendly listing display template (connected to each listing details itself not the page that surrounds it)\", \"Choose a template used for this category's print friendly page (category specific questions you generate and attach to a category). If no print friendly template is chosen for this category the default print friendly template for the site will be used. Enter templates used for the category specific purpose print friendly display through the template administration.\"]\n";

		//".$this->show_tooltip(1,1)."

		// Set style for tooltip
		//$this->body .= "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		if (($category) && ($type))
		{
			$this->sql_query = "select * from ".$this->classified_categories_languages_table." where category_id = ".$category;
			//echo $this->sql_query."<br>\n";
			$language_result = $db->Execute($this->sql_query);
			 if (!$language_result)
			 {
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3501];
				return false;
			 }
			 elseif ($language_result->RecordCount() > 0)
			 {
			 	if (!$this->admin_demo())$this->body .= "<form action=index.php?a=6&c=".$category."&t=".$type."&z=3 method=post>\n";
		 		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=100%>\n";
		 		$this->title = "Categories Setup > Edit > Name, Description, and Templates";
			 	while ($show = $language_result->FetchRow())
			 	{
			 		$this->body .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light>\n\tcategory name and description for: <b>".$this->get_language_name($db,$show["language_id"])."</b> language \n\t</td>\n\t";
					$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font>\n\tcategory name: \n\t</td>\n\t";
					$this->body .= "<td valign=top>\n\t<input type=text name=b[".$show["language_id"]."][category_name] value=\"".urldecode(stripslashes($show["category_name"]))."\">\n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font>\n\tcategory description:".$this->show_tooltip(1,1)."</td>\n\t";
					$this->body .= "<td valign=top>\n\t<textarea name=b[".$show["language_id"]."][description] cols=30 rows=3>".urldecode(stripslashes($this->special_chars($show["description"])))."</textarea>\n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font>\n\t
						category home template:".$this->show_tooltip(2,1)."</td>\n\t";
					$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
					//echo $this->sql_query."<br>\n";
					$this->body .= "<td valign=top>\n\t<select name=b[".$show["language_id"]."][template_id]>\n\t\t";
					$this->body .= "<option value=0>none</option>\n\t";
					$category_template_result = $db->Execute($this->sql_query);
					 if (!$category_template_result)
					 {
						$this->error_message = $this->messages[3501];
						return false;
					 }
					 elseif ($category_template_result->RecordCount() > 0)
					 {
						while ($show_template = $category_template_result->FetchRow())
						{
							$this->body .= "<option ";
							if ($show_template["template_id"] == $show["template_id"])
								$this->body .= "selected ";
							$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
						}
					 }
					$this->body .= "</select>\n\t<br><input type=checkbox checked name=b[".$show["language_id"]."][template_id_subcats_also] value=1><span class=medium_font>subcategories also</span></td>\n</tr>\n";

					$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font>\n\t
						category secondary template:".$this->show_tooltip(3,1)."</td>\n\t";
					$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
					$this->body .= "<td valign=top>\n\t<select name=b[".$show["language_id"]."][secondary_template_id]>\n\t\t";
					$this->body .= "<option value=0>none</option>\n\t";
					$category_template_result = $db->Execute($this->sql_query);
					 if (!$category_template_result)
					 {
						$this->error_message = $this->messages[3501];
						return false;
					 }
					 elseif ($category_template_result->RecordCount() > 0)
					 {
						while ($show_template = $category_template_result->FetchRow())
						{
							$this->body .= "<option ";
							if ($show_template["template_id"] == $show["secondary_template_id"])
								$this->body .= "selected ";
							$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
						}
					 }
					$this->body .= "</select>\n\t<br><input type=checkbox checked name=b[".$show["language_id"]."][secondary_template_id_subcats_also] value=1><span class=medium_font>subcategories also</span></td>\n</tr>\n";

					$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font>\n\t
						category display listing page template:".$this->show_tooltip(4,1)."</td>\n\t";
					$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
					$this->body .= "<td valign=top>\n\t<select name=b[".$show["language_id"]."][ad_display_template_id]>\n\t\t";
					$this->body .= "<option value=0>none</option>\n\t";
					$category_template_result = $db->Execute($this->sql_query);
					 if (!$category_template_result)
					 {
						$this->error_message = $this->messages[3501];
						return false;
					 }
					 elseif ($category_template_result->RecordCount() > 0)
					 {
						while ($show_template = $category_template_result->FetchRow())
						{
							$this->body .= "<option ";
							if ($show_template["template_id"] == $show["ad_display_template_id"])
								$this->body .= "selected ";
							$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
						}
					 }
					$this->body .= "</select><br><input type=checkbox checked name=b[".$show["language_id"]."][ad_display_template_id_subcats_also] value=1><span class=medium_font>subcategories also</span></td>\n</tr>\n";

					if($this->is_classifieds()||$this->is_class_auctions())
					{
						$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font>\n\t
							category listing detail template:".$this->show_tooltip(5,1)."</td>\n\t";
						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=b[".$show["language_id"]."][ad_detail_display_template_id]>\n\t\t";
						$this->body .= "<option value=0>none</option>\n\t";
						$category_template_result = $db->Execute($this->sql_query);
						 if (!$category_template_result)
						 {
							$this->error_message = $this->messages[3501];
							return false;
						 }
						 elseif ($category_template_result->RecordCount() > 0)
						 {
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "<option ";
								if ($show_template["template_id"] == $show["ad_detail_display_template_id"])
									$this->body .= "selected ";
								$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
							}
						 }
						$this->body .= "</select><br><input type=checkbox checked name=b[".$show["language_id"]."][ad_detail_display_template_id_also] value=1><span class=medium_font>subcategories also</span></td>\n</tr>\n";

						$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font>\n\t
							category listing detail extra question template:".$this->show_tooltip(6,1)."</td>\n\t";
						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=b[".$show["language_id"]."][ad_detail_extra_display_template_id]>\n\t\t";
						$this->body .= "<option value=0>none</option>\n\t";
						$category_template_result = $db->Execute($this->sql_query);
						 if (!$category_template_result)
						 {
							$this->error_message = $this->messages[3501];
							return false;
						 }
						 elseif ($category_template_result->RecordCount() > 0)
						 {
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "<option ";
								if ($show_template["template_id"] == $show["ad_detail_extra_display_template_id"])
									$this->body .= "selected ";
								$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
							}
						 }
						$this->body .= "</select><br><input type=checkbox checked name=b[".$show["language_id"]."][ad_display_detail_extra_template_id_subcats_also] value=1><span class=medium_font>subcategories also</span></td>\n</tr>\n";

						$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font>\n\t
							category listing detail checkbox question template:".$this->show_tooltip(7,1)."</td>\n\t";
						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=b[".$show["language_id"]."][ad_detail_checkbox_display_template_id]>\n\t\t";
						$this->body .= "<option value=0>none</option>\n\t";
						$category_template_result = $db->Execute($this->sql_query);
						 if (!$category_template_result)
						 {
							$this->error_message = $this->messages[3501];
							return false;
						 }
						 elseif ($category_template_result->RecordCount() > 0)
						 {
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "<option ";
								if ($show_template["template_id"] == $show["ad_detail_checkbox_display_template_id"])
									$this->body .= "selected ";
								$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
							}
						 }
						$this->body .= "</select><br><input type=checkbox checked name=b[".$show["language_id"]."][ad_display_detail_checkbox_template_id_subcats_also] value=1><span class=medium_font>subcategories also</span></td>\n</tr>\n";
				 	}
					//
					//AUCTIONS
					//

					if($this->is_auctions()||$this->is_class_auctions())
					{
						$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font>\n\t
							category auction detail template:".$this->show_tooltip(5,1)."</td>\n\t";
						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=b[".$show["language_id"]."][auction_detail_display_template_id]>\n\t\t";
						$this->body .= "<option value=0>none</option>\n\t";
						$category_template_result = $db->Execute($this->sql_query);
						 if (!$category_template_result)
						 {
							$this->error_message = $this->messages[3501];
							return false;
						 }
						 elseif ($category_template_result->RecordCount() > 0)
						 {
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "<option ";
								if ($show_template["template_id"] == $show["auction_detail_display_template_id"])
									$this->body .= "selected ";
								$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
							}
						 }
						$this->body .= "</select><br><input type=checkbox checked name=b[".$show["language_id"]."][auction_detail_display_template_id_also] value=1><span class=medium_font>subcategories also</span></td>\n</tr>\n";

						$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font>\n\t
							category auction detail extra question template:".$this->show_tooltip(6,1)."</td>\n\t";
						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=b[".$show["language_id"]."][auction_detail_extra_display_template_id]>\n\t\t";
						$this->body .= "<option value=0>none</option>\n\t";
						$category_template_result = $db->Execute($this->sql_query);
						 if (!$category_template_result)
						 {
							$this->error_message = $this->messages[3501];
							return false;
						 }
						 elseif ($category_template_result->RecordCount() > 0)
						 {
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "<option ";
								if ($show_template["template_id"] == $show["auction_detail_extra_display_template_id"])
									$this->body .= "selected ";
								$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
							}
						 }
						$this->body .= "</select><br><input type=checkbox checked name=b[".$show["language_id"]."][auction_display_detail_extra_template_id_subcats_also] value=1><span class=medium_font>subcategories also</span></td>\n</tr>\n";

						$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font>\n\t
							category auction detail checkbox question template:".$this->show_tooltip(7,1)."</td>\n\t";
						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=b[".$show["language_id"]."][auction_detail_checkbox_display_template_id]>\n\t\t";
						$this->body .= "<option value=0>none</option>\n\t";
						$category_template_result = $db->Execute($this->sql_query);
						 if (!$category_template_result)
						 {
							$this->error_message = $this->messages[3501];
							return false;
						 }
						 elseif ($category_template_result->RecordCount() > 0)
						 {
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "<option ";
								if ($show_template["template_id"] == $show["auction_detail_checkbox_display_template_id"])
									$this->body .= "selected ";
								$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
							}
						 }
						$this->body .= "</select><br><input type=checkbox checked name=b[".$show["language_id"]."][auction_display_detail_checkbox_template_id_subcats_also] value=1><span class=medium_font>subcategories also</span></td>\n</tr>\n";
					}

					//
					// AUCTIONS
					//

					$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top class=medium_font>\n\t
						full size image template:".$this->show_tooltip(8,1)."</td>\n\t";
					$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
					$this->body .= "<td valign=top>\n\t<select name=b[".$show["language_id"]."][ad_detail_full_image_display_template_id]>\n\t\t";
					$this->body .= "<option value=0>none</option>\n\t";
					$category_template_result = $db->Execute($this->sql_query);
					 if (!$category_template_result)
					 {
						$this->error_message = $this->messages[3501];
						return false;
					 }
					 elseif ($category_template_result->RecordCount() > 0)
					 {
						while ($show_template = $category_template_result->FetchRow())
						{
							$this->body .= "<option ";
							if ($show_template["template_id"] == $show["ad_detail_full_image_display_template_id"])
								$this->body .= "selected ";
							$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
						}
					 }
					$this->body .= "</select><br><input type=checkbox checked name=b[".$show["language_id"]."][ad_display_detail_full_image_template_id_subcats_also] value=1><span class=medium_font>subcategories also</span></td>\n</tr>\n";
					//echo $show["ad_detail_full_image_display_template_id"]." is full image<bR>\n";

					$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
					$category_template_result = $db->Execute($this->sql_query);
					 if (!$category_template_result)
					 {
						$this->error_message = $this->messages[3501];
						return false;
					 }
					 elseif ($category_template_result->RecordCount() > 0)
					 {
						if($this->is_class_auctions() || $this->is_auctions())
						{
						 	$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font>\n\t
								print friendly listing display template:".$this->show_tooltip(9,1)."</td>\n\t";
							$this->body .= "<td valign=top>\n\t<select name=b[".$show["language_id"]."][ad_detail_print_friendly_template]>\n\t\t";
							$this->body .= "<option value=0>none</option>\n\t";
						 	while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "<option ";
								if ($show_template["template_id"] == $show["ad_detail_print_friendly_template"])
									$this->body .= "selected ";
								$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
							}
							$this->body .= "</select><br><input type=checkbox checked name=b[".$show["language_id"]."][ad_display_detail_print_friendly_template_id_subcats_also] value=1><span class=medium_font>subcategories also</span></td>\n</tr>\n";
						}

						if($this->is_class_auctions() || $this->is_auctions())
						{
							$category_template_result->Move(0);
							$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font>\n\t
								print friendly auction display template:".$this->show_tooltip(10,1)."</td>\n\t";
							$this->body .= "<td valign=top>\n\t<select name=b[".$show["language_id"]."][auction_detail_print_friendly_template]>\n\t\t";
							$this->body .= "<option value=0>none</option>\n\t";
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "<option ";
								if ($show_template["template_id"] == $show["auction_detail_print_friendly_template"])
									$this->body .= "selected ";
								$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
							}
							$this->body .= "</select><br><input type=checkbox checked name=b[".$show["language_id"]."][auction_display_detail_print_friendly_template_id_subcats_also] value=1><span class=medium_font>subcategories also</span></td>\n</tr>\n";
						}
					 }
					//echo $show["ad_detail_print_friendly_template"]." is print friendly<bR>\n";
				}
				if (!$this->admin_demo())
					$this->body .= "<tr><td align=center colspan=2><input type=submit value=\"Save\"></td></tr>";
				$this->body .= "</table>\n</form>";
				return true;
			}
			else
				return false;
		}
		else
			return false;
	} //end of function category_templates_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_sql_in_statement($db,$category_id)
	{
		if ($category_id)
		{
			$this->subcategory_array = array();
			//echo "empty subcategory_array for ".$category_id."<BR>\n";
			$this->get_sql_in_array($db,$category_id);
			if (count($this->subcategory_array) > 0)
			{
				$this->in_statement = "";
				$this->in_statement .= " in (";
				while (list($key,$value) = each($this->subcategory_array))
				{
					if ($key == 0)
						$this->in_statement .= $value;
					else
						$this->in_statement .= ",".$value;
				}
				$this->in_statement .= ")";
				return $this->in_statement;
			}
			else
			{
				return false;
			}
		}
		else
		{
			//category_id is missing
			return false;
		}

	} //end of get_sql_in_statement

//####################################################################################

	function get_sql_in_array($db,$category_id)
	{
		if ($category_id)
		{
			//get the count for this category
			$count = 0;

			//$this->subcategory_array = array();
			$this->sql_query = "select category_id from ".$this->classified_categories_table." where parent_id = ".$category_id;
			if ($this->debug_categories) echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				$this->error_message = $this->messages[2524];
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				while ($show_category = $result->FetchRow())
				{
					$this->get_sql_in_array($db,$show_category["category_id"]);
				}
			}
			//echo "pushing - ".$category_id."<br>";
			array_push ($this->subcategory_array, $category_id);

			return true;
		}
		else
		{
			//category_id is missing
			return false;
		}

	} //end of get_sql_in_array

//##################################################################################

	function insert_category($db,$info,$parent_category=0)
	{
		$this->sql_query = "select * from ".$this->pages_languages_table;
		$language_result = $db->Execute($this->sql_query);
		if ($this->debug_categories) echo $this->sql_query."<br>\n";
		if (!$language_result)
		{
			if ($this->debug_categories) echo $this->sql_query."<br>\n";
			$this->error_message = $this->messages[3500];
			return false;
		}
		elseif ($language_result->RecordCount() > 0)
		{
			$this->sql_query = "insert into ".$this->classified_categories_table."
				(parent_id,category_name,description,display_order,category_image,listing_types_allowed)
				values
				(".$parent_category.",\"".$info[1]["category_name"]."\",\"".$info[1]["description"]."\",".$info["display_order"].",\"".$info["category_image"]."\", ".$info["listing_types_allowed"].")";
			$result = $db->Execute($this->sql_query);
			if ($this->debug_categories)
				echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
			$category_id = $db->Insert_ID();

			$category_in_statement = "";
			$category_in_statement = $this->get_sql_in_statement($db,$category_id);

			$this->sql_query = "update ".$this->classified_categories_table." set
				in_statement = \"".$category_in_statement."\" where category_id = ".$category_id;
			$result = $db->Execute($this->sql_query);
			if ($this->debug_categories) echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}

			while ($show = $language_result->FetchRow())
			{
				$this->sql_query = "insert into ".$this->classified_categories_languages_table."
					(category_id,template_id,secondary_template_id,ad_display_template_id,category_name,description,language_id)
					values
					(".$category_id.",\"".$info[$show["language_id"]]["template_id"]."\",
					\"".$info[$show["language_id"]]["secondary_template_id"]."\",\"".$info[$show["language_id"]]["ad_display_template_id"]."\",
					\"".addslashes(urlencode($info[$show["language_id"]]["category_name"]))."\",\"".addslashes(urlencode($info[$show["language_id"]]["description"]))."\",".$show["language_id"].")";
				$result = $db->Execute($this->sql_query);
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				if (!$result)
				{
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					$this->error_message = $this->messages[3500];
					return false;
				}
				//echo $show["language_id"]." is the language_id at end<br>\n";
			}
			//get sql in statement for this category

			//reset sql in statement for categories above
			if ($parent_category)
			{
				//check if parent is main category
				$current_parent_category = $parent_category;
				while ($current_parent_category != 0)
				{
					$parent_in_statement = "";
					$parent_in_statement = $this->get_sql_in_statement($db,$current_parent_category);

					$this->sql_query = "update ".$this->classified_categories_table." set
						in_statement = \"".$parent_in_statement."\" where category_id = ".$current_parent_category;
					//echo $this->sql_query."<br>\n";
					$result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					if (!$result)
					{
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						return false;
					}

					$this->sql_query = "SELECT parent_id FROM ".$this->classified_categories_table." WHERE category_id = ".$current_parent_category;
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						return false;
					}
					elseif ($result->RecordCount() == 1)
					{
						$show_category = $result->FetchRow();
						$current_parent_category = $show_category["parent_id"];
					}
					else
					{
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						return false;
					}

				} //end of while
			}
		}

		return $category_id;

	} // end of function insert_category

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_category_check($db,$category=0)
	{
		if ($category)
		{
			$category_name = $this->get_category_name($db,$category);
			$this->sql_query = "select * from ".$this->classified_categories_table." where category_id = ".$category;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
				if ($show["parent_id"])
				{
					$parent_name = $this->get_category_name($db,$show["parent_id"]);

					$this->sql_query = "select * from ".$this->classified_categories_table." where category_id = ".$show["parent_id"];
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						$this->error_message = $this->messages[3500];
						return false;
					}
					elseif ($result->RecordCount() == 1)
					{
						$show_parent = $result->FetchRow();

						$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100% class=row_color1>\n";
						$this->title .= "Categories Setup > Delete Category";
						$this->description .= "Verify deletion of a category and choose what to do with listings within it.";
						$this->body .= "<tr>\n\t<td class=medium_font>Are you sure you want to
							delete the <b>".$category_name."</b> Category?  If so, choose whether or not you want
							to move this category's existing listings to it's Parent Category<b> ( ".
							$parent_name.")</b> or delete them along with the category.
							 \n\t</td>\n</tr>\n";
						$this->body .= "<tr>\n\t<td align=center><a href=index.php?a=4&b=".$category."&c=move>
							<span class=medium_font><br><br><font color=000000>Move all listings to the ".
							$parent_name." Category</font></span></a>\n\t</td>\n</tr>\n";
						$this->body .= "<tr>\n\t<td class=medium_font align=center><b>--OR--</b>\n\t</td>\n</tr>\n";
						$this->body .= "<tr>\n\t<td align=center><a href=index.php?a=4&b=".$category."&c=delete>
							<span class=medium_font align=center><font color=000000>Delete all listings and the <b>".$category_name."</b> Category</font>
							</span></a>\n\t</td>\n</tr>\n";
						$this->body .= "<tr>\n\t<td class=medium_error_font align=center>\n\t<b><br><br>Note: All category specific questions will be removed
							from the database.</b></td>\n</tr>\n";
						$this->body .= "</table>\n";
					}
					else
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[3500];
						return false;
					}
				}
				else
				{
					//delete a main category
					$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100% class=row_color1>\n";
					$this->title .= "Categories Setup > Delete a Main Category";
					$this->description .= "Verify deletion of a category and choose what to do with listings within it. \n\t</td>\n</tr>\n";
					$this->body .= "\n<tr>\n\t<td class=medium_font>Are you sure you want to
						delete the <b>".$category_name."</b> category?  If so click the link below. If you choose to delete this
						category the only option is to delete the listings and subcategories currently within it as well.<br><br> \n\t</td>\n</tr>\n";
					$this->body .= "<tr>\n\t<td align=center><a href=index.php?a=4&b=".$category."&c=delete><span class=medium_font>
						Delete all listings in the <b>".$category_name."</b> category along with its subcategories.<br><br></span></a>\n\t</td>\n</tr>\n";
					$this->body .= "<tr>\n\t<td class=medium_error_font align=center>\n\t<b>Note: All category specific questions will be removed
						from the database. </b></td>\n</tr>\n";

					$this->body .= "</table>\n";
				}
				return true;
			}
			else
			{
				$this->error_message = $this->messages[3500];
				return false;
			}
		}
		else
		{
			$this->error_message = $this->messages[3509];
			return false;
		}
	} // end of function delete_category_check

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_category($db,$category=0,$type_of_delete=0)
	{
		if ($this->debug_categories) echo "hello from delete category<br>\n";
		if ($category)
		{
			$this->sql_query = "select parent_id from ".$this->classified_categories_table."
				where category_id = ".$category;
			$result = $db->Execute($this->sql_query);
			if ($this->debug_categories) echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_parent = $result->FetchRow();
				if ($type_of_delete)
				{

					switch ($type_of_delete)
					{
						case "move":
							$this->sql_query = "update ".$this->classifieds_table." set
								category = ".$show_parent["parent_id"]."
								where category = ".$category;
							$update_result = $db->Execute($this->sql_query);
							if ($this->debug_categories) echo $this->sql_query."<br>\n";
							if (!$update_result)
							{
								if ($this->debug_categories) echo $this->sql_query."<br>\n";
								$this->error_message = $this->messages[3500];
								return false;
							}
							break;
						case "delete":
							$this->sql_query = "select * from ".$this->classifieds_table." where category = ".$category;
							$delete_category_result = $db->Execute($this->sql_query);
							if ($this->debug_categories) echo $this->sql_query."<br>\n";
							if (!$delete_category_result)
							{
								if ($this->debug_categories) echo $this->sql_query."<br>\n";
								$this->error_message = $this->messages[3500];
								return false;
							}

							if ($delete_category_result->RecordCount() > 0)
							{
								while ($show = $delete_category_result->FetchRow())
								{
									//delete the images
									$this->sql_query = "delete from ".$this->images_table." where classified_id = ".$show["id"];
									$delete_image_result = $db->Execute($this->sql_query);
									if ($this->debug_categories) echo $this->sql_query."<br>\n";
									if (!$delete_image_result)
									{
										if ($this->debug_categories) echo $this->sql_query."<br>\n";
										$this->error_message = urldecode($this->messages[81]);
										return false;
									}

									//delete from auctions extra questions
									$this->sql_query = "delete from ".$this->classified_extra_table." where classified_id = ".$show["id"];
									$remove_extra_result = $db->Execute($this->sql_query);
									if ($this->debug_categories) echo $this->sql_query."<br>\n";
									if (!$remove_extra_result)
									{
										if ($this->debug_categories) echo $this->sql_query."<br>\n";
										$this->error_message = urldecode($this->messages[81]);
										return false;
									}

									//delete url images
									//get image urls to
									$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$show["id"];
									$get_url_result = $db->Execute($this->sql_query);
									if ($this->debug_categories) echo $this->sql_query."<br>\n";
									if (!$get_url_result)
									{
										if ($this->debug_categories) echo $this->sql_query."<br>\n";
										$this->error_message = urldecode($this->messages[81]);
										return false;
									}
									elseif ($get_url_result->RecordCount())
									{
										while ($show_url = $get_url_result->FetchRow())
										{
											if ($show_url["full_filename"])
												unlink($show_url["file_path"].$show_url["full_filename"]);
											if ($show_url["thumb_filename"])
												unlink($show_url["file_path"].$show_url["thumb_filename"]);
										}
										$this->sql_query = "delete from ".$this->images_urls_table." where classified_id = ".$show["id"];
										$delete_url_result = $db->Execute($this->sql_query);
										if ($this->debug_categories) echo $this->sql_query."<br>\n";
										if (!$delete_url_result)
										{
											if ($this->debug_categories) echo $this->sql_query."<br>\n";
											$this->error_message = urldecode($this->messages[81]);
											return false;
										}
									}

									//delete from classifieds table
									$this->sql_query = "delete from ".$this->classifieds_table." where id = ".$show["id"];
									$remove_result = $db->Execute($this->sql_query);
									if ($this->debug_categories) echo $this->sql_query."<br>\n";
									if (!$remove_result)
									{
										if ($this->debug_categories) echo $this->sql_query."<br>\n";
										$this->error_message = urldecode($this->messages[81]);
										return false;
									}
								}
							}

							break;
						default:
							//do nothing

					} //end of switch
				}

				$this->update_category_count($db,$show_parent["parent_id"]);

				$in_statement = "";
				$in_statement = $this->get_sql_in_statement($db,$category);

				$this->sql_query = "select filter_id,user_id,category_id,sub_category_check from ".$this->classified_ad_filter_table."
					where category_id ".$in_statement;
				$select_filter_result = $db->Execute($this->sql_query);
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				if (!$select_filter_result)
				{
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					$this->error_message = $this->messages[3500];
					return false;
				}
				elseif ($select_filter_result->RecordCount() > 0)
				{
					while ($show = $select_filter_result->FetchRow())
					{
						$this->sql_query = "delete from ".$this->classified_ad_filter_categories_table."
							where filter_id = ".$show["filter_id"];
						$delete_filter_result = $db->Execute($this->sql_query);
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						if (!$delete_filter_result)
						{
							if ($this->debug_categories) echo $this->sql_query."<br>\n";
							$this->error_message = $this->messages[3500];
							return false;
						}
					}
				}


				$this->sql_query = "delete from ".$this->classified_ad_filter_table."
					where category_id ".$in_statement;
				$delete_category_result = $db->Execute($this->sql_query);
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				if (!$delete_category_result)
				{
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					$this->error_message = $this->messages[3500];
					return false;
				}

				$this->sql_query = "delete from ".$this->classified_ad_filter_categories_table."
					where category_id ".$in_statement;
				$delete_filter_result = $db->Execute($this->sql_query);
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				if (!$delete_filter_result)
				{
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					$this->error_message = $this->messages[3500];
					return false;
				}

				$this->sql_query = "delete from ".$this->classified_categories_languages_table."
					where category_id = ".$category;
				$delete_category_result = $db->Execute($this->sql_query);
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				if (!$delete_category_result)
				{
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					$this->error_message = $this->messages[3500];
					return false;
				}

				$this->sql_query = "delete from ".$this->classified_price_plans_categories_table."
					where category_id = ".$category;
				$delete_category_result = $db->Execute($this->sql_query);
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				if (!$delete_category_result)
				{
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					$this->error_message = $this->messages[3500];
					return false;
				}

				$this->sql_query = "delete from ".$this->classified_categories_table."
					where category_id = ".$category;
				$delete_category_result = $db->Execute($this->sql_query);
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				if (!$delete_category_result)
				{
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					$this->error_message = $this->messages[3500];
					return false;
				}

				$this->sql_query = "delete from ".$this->sell_questions_table."
					where category_id = ".$category;
				$delete_question_result = $db->Execute($this->sql_query);
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				if (!$delete_question_result)
				{
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					$this->error_message = $this->messages[3500];
					return false;
				}

			}
			else
			{
				$this->error_message = $this->messages[3500];
				return false;
			}
			return true;
		}
		else
		{
			$this->error_message = $this->messages[3509];
			return false;
		}
	} // end of function delete_category

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_category($db,$category,$info2=0)
	{
		if (($category) && ($info2))
		{
			$in_statement = $this->get_sql_in_statement($db,$category);
			$this->sql_query = "update ".$this->classified_categories_table." set
				display_order = ".$info2["display_order"].",
				category_image = \"".$info2["category_image"]."\",
				in_statement = \"".$in_statement."\",
				listing_types_allowed = \"".$info2['listing_types_allowed']."\" 
				where category_id = ".$category;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}



			return true;
		}
		else
		{
			$this->error_message = $this->messages[3503];
			return false;
		}
	} // end of function update_category

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_category_templates($db,$category=0,$info=0)
	{
		if (($category) && ($info))
		{
			foreach ($info as $key => $value)
			{
				$this->sql_query = "update ".$this->classified_categories_languages_table." set
					category_name = \"".addslashes(urlencode($info[$key]["category_name"]))."\",
					secondary_template_id = \"".$info[$key]["secondary_template_id"]."\",
					ad_display_template_id = \"".$info[$key]["ad_display_template_id"]."\",
					ad_detail_display_template_id = \"".$info[$key]["ad_detail_display_template_id"]."\",
					ad_detail_extra_display_template_id  = \"".$info[$key]["ad_detail_extra_display_template_id"]."\",
					ad_detail_checkbox_display_template_id = \"".$info[$key]["ad_detail_checkbox_display_template_id"]."\",
					auction_detail_display_template_id = \"".$info[$key]["auction_detail_display_template_id"]."\",
					auction_detail_extra_display_template_id  = \"".$info[$key]["auction_detail_extra_display_template_id"]."\",
					auction_detail_checkbox_display_template_id = \"".$info[$key]["auction_detail_checkbox_display_template_id"]."\",
					ad_detail_full_image_display_template_id = \"".$info[$key]["ad_detail_full_image_display_template_id"]."\",
					ad_detail_print_friendly_template  = \"".$info[$key]["ad_detail_print_friendly_template"]."\",
					auction_detail_print_friendly_template  = \"".$info[$key]["auction_detail_print_friendly_template"]."\",
					template_id = \"".$info[$key]["template_id"]."\",
					description = \"".addslashes(urlencode($info[$key]["description"]))."\"
					where category_id = ".$category." and language_id = ".$key;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is the query<br>\n";
				if (!$result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[3500];
					return false;
				}

				// Attach modules in templates
				if($info[$key]["secondary_template_id"])
				{
					$this->AttachEmbeddedModules($db, $info[$key]["secondary_template_id"], 1);
				}
				if($info[$key]["ad_display_template_id"])
				{
					$this->AttachEmbeddedModules($db, $info[$key]["ad_display_template_id"], 1);
				}
				if($info[$key]["ad_detail_display_template_id"])
				{
					$this->AttachEmbeddedModules($db, $info[$key]["ad_detail_display_template_id"], 1);
				}
				if($info[$key]["ad_detail_extra_display_template_id"])
				{
					$this->AttachEmbeddedModules($db, $info[$key]["ad_detail_extra_display_template_id"], 1);
				}
				if($info[$key]["ad_detail_checkbox_display_template_id"])
				{
					$this->AttachEmbeddedModules($db, $info[$key]["ad_detail_checkbox_display_template_id"], 1);
				}
				if($info[$key]["ad_detail_full_image_display_template_id"])
				{
					$this->AttachEmbeddedModules($db, $info[$key]["ad_detail_full_image_display_template_id"], 1);
				}
				if($info[$key]["ad_detail_print_friendly_template"])
				{
					$this->AttachEmbeddedModules($db, $info[$key]["ad_detail_print_friendly_template"], 1);
				}
				if($info[$key]["auction_detail_print_friendly_template"])
				{
					$this->AttachEmbeddedModules($db, $info[$key]["auction_detail_print_friendly_template"], 1);
				}
				if($info[$key]["template_id"])
				{
					$this->AttachEmbeddedModules($db, $info[$key]["template_id"], 1);
				}

				if ($info[$key]["template_id_subcats_also"] == 1)
				{
					//set subcategories template also
					$this->set_template_for_subcategories ($db,$category,$key,1,$info[$key]["template_id"]);
				}

				if ($info[$key]["secondary_template_id_subcats_also"] == 1)
				{
					//set subcategories secondary template also
					$this->set_template_for_subcategories ($db,$category,$key,2,$info[$key]["secondary_template_id"]);
				}

				if ($info[$key]["ad_display_template_id_subcats_also"] == 1)
				{
					//set subcategories listing display template also
					$this->set_template_for_subcategories ($db,$category,$key,3,$info[$key]["ad_display_template_id"]);
				}
				if ($info[$key]["ad_detail_display_template_id_also"] == 1)
				{
					//set subcategories listing detail display template also
					$this->set_template_for_subcategories ($db,$category,$key,4,$info[$key]["ad_detail_display_template_id"]);
				}
				if ($info[$key]["ad_display_detail_extra_template_id_subcats_also"] == 1)
				{
					//set subcategories listing detail extra question template also
					$this->set_template_for_subcategories ($db,$category,$key,5,$info[$key]["ad_detail_extra_display_template_id"]);
				}
				if ($info[$key]["ad_display_detail_checkbox_template_id_subcats_also"] == 1)
				{
					//set subcategories listing detail checkbox template also
					$this->set_template_for_subcategories ($db,$category,$key,6,$info[$key]["ad_detail_checkbox_display_template_id"]);
				}
				if ($info[$key]["auction_detail_display_template_id_also"] == 1)
				{
					//set subcategories listing detail display template also
					$this->set_template_for_subcategories ($db,$category,$key,9,$info[$key]["auction_detail_display_template_id"]);
				}
				if ($info[$key]["auction_display_detail_extra_template_id_subcats_also"] == 1)
				{
					//set subcategories listing detail extra question template also
					$this->set_template_for_subcategories ($db,$category,$key,10,$info[$key]["auction_detail_extra_display_template_id"]);
				}
				if ($info[$key]["auction_display_detail_checkbox_template_id_subcats_also"] == 1)
				{
					//set subcategories listing detail checkbox template also
					$this->set_template_for_subcategories ($db,$category,$key,11,$info[$key]["auction_detail_checkbox_display_template_id"]);
				}
				if ($info[$key]["ad_display_detail_full_image_template_id_subcats_also"] == 1)
				{
					//set subcategories listing detail checkbox template also
					$this->set_template_for_subcategories ($db,$category,$key,7,$info[$key]["ad_detail_full_image_display_template_id"]);
				}
				if ($info[$key]["ad_display_detail_print_friendly_template_id_subcats_also"] == 1)
				{
					//set subcategories listing detail checkbox template also
					$this->set_template_for_subcategories ($db,$category,$key,8,$info[$key]["ad_detail_print_friendly_template"]);
				}
				if ($info[$key]["auction_display_detail_print_friendly_template_id_subcats_also"] == 1)
				{
					//set subcategories listing detail checkbox template also
					$this->set_template_for_subcategories ($db,$category,$key,12,$info[$key]["auction_detail_print_friendly_template"]);
				}

			}
			return true;
		}
		else
			return false;
	} //end of function update_category_templates

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function set_template_for_subcategories ($db,$category_id,$language_id,$type,$template_id)
	{
		$this->sql_query = "update ".$this->classified_categories_languages_table." set ";

		switch ($type)
		{
			case 1:{$this->sql_query .= "template_id = \"".$template_id."\""; break;}
			case 2:{$this->sql_query .= "secondary_template_id = \"".$template_id."\""; break;}
			case 3:{$this->sql_query .= "ad_display_template_id = \"".$template_id."\""; break;}
			case 4:{$this->sql_query .= "ad_detail_display_template_id = \"".$template_id."\""; break;}
			case 5:{$this->sql_query .= "ad_detail_extra_display_template_id = \"".$template_id."\""; break;}
			case 6:{$this->sql_query .= "ad_detail_checkbox_display_template_id = \"".$template_id."\""; break;}
			case 7:{$this->sql_query .= "ad_detail_full_image_display_template_id = \"".$template_id."\""; break;}
			case 8:{$this->sql_query .= "ad_detail_print_friendly_template = \"".$template_id."\""; break;}
			case 9:{$this->sql_query .= "auction_detail_display_template_id = \"".$template_id."\""; break;}
			case 10:{$this->sql_query .= "auction_detail_extra_display_template_id = \"".$template_id."\""; break;}
			case 11:{$this->sql_query .= "auction_detail_checkbox_display_template_id = \"".$template_id."\""; break;}
			case 12:{$this->sql_query .= "auction_detail_print_friendly_template = \"".$template_id."\""; break;}
			default: return false;
		}
		$this->sql_query .= " where category_id = ".$category_id." and language_id = ".$language_id;
		//echo $this->sql_query."<br>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[3500];
			return false;
		}
		else
		{
			$this->sql_query = "select in_statement from ".$this->classified_categories_table." where category_id = ".$category_id;
			//echo $this->sql_query."<br>\n";
			$in_category_result = $db->Execute($this->sql_query);
			if (!$in_category_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
			elseif ($in_category_result->RecordCount() == 1)
			{
				$show_in_statement = $in_category_result->FetchRow();
				$this->sql_query = "update ".$this->classified_categories_languages_table." set ";
				switch ($type)
				{
					case 1:{$this->sql_query .= "template_id = \"".$template_id."\""; break;}
					case 2:{$this->sql_query .= "secondary_template_id = \"".$template_id."\""; break;}
					case 3:{$this->sql_query .= "ad_display_template_id = \"".$template_id."\""; break;}
					case 4:{$this->sql_query .= "ad_detail_display_template_id = \"".$template_id."\""; break;}
					case 5:{$this->sql_query .= "ad_detail_extra_display_template_id = \"".$template_id."\""; break;}
					case 6:{$this->sql_query .= "ad_detail_checkbox_display_template_id = \"".$template_id."\""; break;}
					case 7:{$this->sql_query .= "ad_detail_full_image_display_template_id = \"".$template_id."\""; break;}
					case 8:{$this->sql_query .= "ad_detail_print_friendly_template = \"".$template_id."\""; break;}
					case 12:{$this->sql_query .= "auction_detail_print_friendly_template = \"".$template_id."\""; break;}
					default: return false;
				}
				$this->sql_query .= " where category_id  ".$show_in_statement["in_statement"]." and language_id = ".$language_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[3500];
					return false;
				}
			}
			else
				return false;

			//$this->sql_query = "select category_id from ".$this->classified_categories_table." where parent_id = ".$category_id;
			////echo $this->sql_query."<br>\n";
			//$subcategory_result = $db->Execute($this->sql_query);
			//if (!$subcategory_result)
			//{
			//	//echo $this->sql_query." is the query<br>\n";
			//	$this->error_message = $this->messages[3500];
			//	return false;
			//}
			//elseif ($subcategory_result->RecordCount() > 0)
			//{
			//	while ($show_subcategory = $subcategory_result->FetchRow())
			//	{
			//		$this->set_template_for_subcategories ($db,$show_subcategory["category_id"],$language_id,$type,$template_id);
			//	}
			//}
			return true;
		}
	} //end of function set_template_for_subcategories

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function home ()
	{
		echo "<table align=center width=100%>\n";
		echo "<tr>\n\t<td><a href=index.php?a=7>browse and edit categories</a>\n\t</td>\n</tr>\n";
		echo "</table>\n";
	} //end of function home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_current_category($db,$category=0)
	{
		//echo $category." is category in the top of display_current_category<br>\n";
		$this->body .= "<table cellpadding=3 cellspacing=0 border=1 width=100%>\n<tr>\n\t<td colspan=5>\n\t";
		$this->sql_query = "select * from ".$this->classified_categories_table." where category_id = ".$category;
		$result = $db->Execute($this->sql_query);
		 if (!$result)
		 {
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[3501];
			return false;
		 }
		 elseif ($result->RecordCount() == 1)
		 {
			$show_this_category = $result->FetchRow();
			$current_category_name = $this->get_category_name($db,$category);

			 $this->sql_query = "select * from ".$this->classified_categories_table." where parent_id = ".$category." order by display_order asc";
			 $subcategory_result = $db->Execute($this->sql_query);
			// echo $this->sql_query." is the query<br>\n";
			 if (!$subcategory_result)
			 {
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			 }

			 if ($category == 0)
			 {
				//parent is main
				//echo "categories under the Main category";
				$this->body .= "Main categories";
			 }
			 else
			 {
				$this->body .= "categories under the ".$current_category_name." category --- #".$show_this_category["category_id"];
			 }
			$this->body .= "</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td>category (category id#)<br>click to enter</td>\n\t<td>description</td>\n\t<td>display order</td>\n\t<td>click to edit</td>\n\t<td>click to delete</td>\n</tr>\n";
			while ($show = $subcategory_result->FetchRow())
			{
				$subcategory_name = $this->get_category_name($db,$show["category_id"]);
				$subcategory_description = $this->get_category_description($db,$show["category_id"]);
				//show each subcategory
				$this->body .= "<tr>\n\t<td><a href=index.php?a=7&b=".$show["category_id"].">".$subcategory_name." (".$show["category_id"].")</a></td>\n\t";
				$this->body .= "<td>".$subcategory_description."&nbsp;</td>\n\t";
				$this->body .= "<td>".$show["display_order"]."</td>\n\t";
				$this->body .= "<td><a href=index.php?a=5&b=".$show["category_id"]."><img src=admin_images/btn_admin_edit.gif alt=edit border=0></a></td>\n\t";
				$this->body .= "<td><a href=index.php?a=4&b=".$show["category_id"]."><img src=admin_images/btn_admin_delete.gif alt=delete border=0></a></td>\n";
				$this->body .= "</tr>\n";
			}
			$this->body .= "</table>\n";
			$this->body .= "<br><br><a href=index.php?a=1&b=".$category.">add a sub category to this category</a>";
			$this->body .= "<br><br><a href=index.php?a=70>reset all category counts</a> - could take a little time depending on the number of categories";
			$this->body .= "<br><br><a href=index.php?a=72>copy subcategories</a>";
			$this->body .= "<br><br><a href=index.php?a=73>copy category specific questions to another category</a>";
			if ($category != 0)
				$this->body .= "<br><br><a href=index.php?a=7&b=0>back to Main category</a>";
		}
		else
		{
			//category does not exist
			$this->error_message = $this->messages[3500];
			return false;
		}
		return true;
	} //end of function display_current_category

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function browse($db,$category=0)
	{
		$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100% class=row_color1>\n";
		$this->title = "Categories Setup";
		$this->description = "The table below displays a current list of categories available on your site. To view a category's subcategories,
		click on the Category Name. To edit a category's details, click Edit Category. You also have the option to enter edit category specific
		questions, which can be done through the Edit Category Questions links. If you choose to delete a category you will be given the option
		of deleting the listings within that category or moving those listings to the next category up in the heirarchy. You can only delete a
		category if there are no subcategories below it. \n\t";
		//browse the listings in this category that are open

		if ($category)
		{
			$current_category_name = $this->get_category_name($db,$category);
		}
		else
		{
			$parent_id = 0;
			$current_category_name = "Main";
		}

		$this->sql_query = "select * from ".$this->classified_categories_table." where parent_id = ".$category." order by display_order";
		$result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}
		else
		{
			if ($category)
			{
				$category_tree = $this->get_category_tree($db,$category);
				reset ($this->category_tree_array);
				if ($category_tree)
				{

					//category tree
					$this->body .= "<tr>\n\t<td colspan=5 class=medium_font><b>";
					$this->body .= $this->messages[3504]." : </b> <a href=index.php?a=7><font color=000000>Main</font></a> > ";
					if (is_array($this->category_tree_array))
					{
						$i = 0;
						//$categories = array_reverse($this->category_tree_array);
						$i = count($this->category_tree_array);
						while ($i > 0 )
						{
							//display all the categories
							$i--;
							if ($i == 0)
								$this->body .= "<span class=medium_font><b>".$this->category_tree_array[$i]["category_name"]."</b></span>";
							else
								$this->body .= "<a href=index.php?a=7&b=".$this->category_tree_array[$i]["category_id"]."><span class=medium_font>".$this->category_tree_array[$i]["category_name"]."</span></a> > ";
						}
					}
					else
					{
						$this->body .= $category_tree;
					}
					$this->body .= "\n\t</td>\n</tr>\n";
				}
			}
			else
			{
				$this->body .= "<tr>\n\t<td colspan=4 class=medium_font><b>".$this->messages[3504].": </b>Main</td>\n</tr>\n";
			}


			if ($result->RecordCount() > 0)
			{
				//echo $result->RecordCount()." is the record count<br>\n";
				//display the sub categories of this category
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=5 class=medium_font_light>\n\t".$this->messages[3505]." <b>".$current_category_name."</b> \n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color_black>\n\t<td align=center class=medium_font_light><b>category name</b> (id#)\n\t</td>\n\t";
				$this->body .= "<td align=center class=medium_font_light>\n\t<b>edit category</b>\n\t</td>\n\t";
				$this->body .= "<td align=center class=medium_font_light>\n\t<b>delete category</b>\n\t</td>\n\t";
				$this->body .= "<td align=center class=medium_font_light>\n\t<b>enter category</b>\n\t</td>\n\t";
				$this->body .= "<td align=center class=medium_font_light>\n\t<b>edit category questions</b>\n\t</td>\n</tr>";
				$this->row_count = 0;
				while ($show_sub_categories = $result->FetchRow())
				{
					$subcategory_name = $this->get_category_name($db,$show_sub_categories["category_id"]);
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td>\n\t<a href=index.php?a=7&b=".$show_sub_categories["category_id"]."><span class=medium_font><font color=000000>".$subcategory_name."(".$show_sub_categories["category_id"].")</font></span></a></td>\n\t";
					$this->body .= "<td align=center>\n\t<a href=index.php?a=5&b=".$show_sub_categories["category_id"]."><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></a>\n\t</span>\n\t";
					$this->sql_query = "select * from ".$this->classified_categories_table." where parent_id = ".$show_sub_categories["category_id"]." order by display_order";
					$subcategory_result = $db->Execute($this->sql_query);
					//echo $this->sql_query." is the query<br>\n";
					if (!$subcategory_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[5501];
						return false;
					}
					elseif ($subcategory_result->RecordCount() == 0)
					{
						$this->body .= "<td align=center>\n\t
							<a href=index.php?a=4&b=".$show_sub_categories["category_id"]."><span class=medium_font_light>".$this->messages[3508]."</span>\n\t</td>\n";
						$this->body .= "<td align=center>---</td>\n\t";
					}
					else
					{
						//echo "<td>\n\t".$this->messages[3507]."\n\t</td>\n";
						$this->body .= "<td align=center>\n\t---\n\t</td>\n";
						$this->body .= "<td align=center><a href=index.php?a=7&b=".$show_sub_categories["category_id"]."><span class=medium_font><img src=admin_images/btn_admin_enter.gif alt=enter border=0></span></a></td>\n\t";
					}
					$this->body .= "<td align=center><a href=index.php?a=8&b=".$show_sub_categories["category_id"]."><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></a></td>\n\t";
					$this->body .= "</tr>\n";
					$this->row_count++;
				}
			}
			else
			{
				//no sub categories to this category
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=5 class=medium_font_light>\n\t".$this->messages[3505]." <b>".$current_category_name."</b> \n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td colspan=5 class=medium_font align=center>\n\t<b><br><br>".$this->messages[3506]."<br><br><br></b>\n\t</td>\n</tr>\n";
			}
		}
		$this->body .= "<tr bgcolor=000066>\n\t<td colspan=5 class=medium_font_light align=center>\n\t<b>Miscellaneous Controls</b></td></tr>\n\t";
		$this->body .= "<tr>\n\t<td colspan=5>\n\t<a href=index.php?a=1&b=".$category."><span class=medium_font>
			<font color=000000><b>add a Subcategory to the ".$current_category_name." category</b></font></span></a>\n\t";
		$this->body .= "<br><br><span class=medium_font><a href=index.php?a=70><span class=medium_font><font color=000000><b>reset all category counts</b></font></span></a> - could take a little time depending on the number of categories</span>";
		$this->body .= "<br><br><a href=index.php?a=72><span class=medium_font><font color=000000><b>copy subcategories</b></font></span></a>";
		$this->body .= "<br><br><a href=index.php?a=73><span class=medium_font><font color=000000><b>copy category specific questions to another category</b></font></span></a>";
		//$this->body .= "<br><br><a href=index.php?a=32&e=".$category."><span class=medium_font><font color=000000><b>add New Pre-Valued Dropdown</b></font></span></a>\n\t";

		$this->body .= "</td>\n</tr>\n";
		$this->body .= "</table>\n";
		return true;
	} //end of function browse

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function category_error()
	{
		$this->body .= "<table cellpadding=5 cellspacing=1 border=0 width=100%>\n";
		$this->body .= "<tr>\n\t<td>".$this->messages[3510]."</td>\n</tr>\n";
		if ($this->error_message)
			$this->body .= "<tr>\n\t<td>".$this->error_messages."</td>\n</tr>\n";
		$this->body .= "</table>\n";
	} //end of function category_error

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function migrate_languages_categories($db)
	{
		$this->sql_query = "select * from ".$this->pages_languages_table." where language_id != 1";
		$result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br><br>\n";
		if (!$result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}
		while ($show_language = $result->FetchRow())
		{
			$this->sql_query = "select * from ".$this->classified_categories_table;
			$category_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br><br>\n";
			if (!$category_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			while ($show_category = $category_result->FetchRow())
			{
				$this->sql_query = "insert into ".$this->classified_categories_languages_table."
					(category_id,category_name,description,language_id)
					values
					(".$show_category["category_id"].",\"".addslashes(urlencode($show_category["category_name"]))."\",\"".addslashes(urlencode($show_category["description"]))."\",".$show_language["language_id"].")";
				$insert_result = $db->Execute($this->sql_query);
				//echo $this->sql_query." is the query<br>\n";
				if (!$insert_result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[5501];
					return false;
				}
			}
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function category_fields_to_use_form($db,$category_id=0)
	{
		if ($this->is_class_auctions() || $this->is_auctions())
			$price_name = "<b>Price (Classifieds Only)</b>&nbsp;";
		elseif ($this->is_classifieds())
			$price_name = "<b>Price</b>&nbsp;";
		// Listings header
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "
			Text[1] = [\"use site default settings\", \"Checking this will use the site wide settings that can be found in \\\"Listing Setup > Fields To Use\\\".  YOU MUST CLICK \\\"Save\\\" FOR THIS TO TAKE AFFECT\"]\n
			Text[2] = [\"use field\", \"Turning this field off here by unchecking the checkbox will turn it off in the \\\"place a listing\\\" and \\\"search\\\" processes as well as remove it from the browsing results.\"]\n
			Text[3] = [\"display field\", \"Checking this checkbox will display this field as a column while browsing the listings.\"]\n
			Text[4] = [\"photo icon/thumbnail (while browsing)\", \"Checking this checkbox will display the photo column next to the title column (at the left side of the browsing results). You can choose below whether or not to display the photo icon referenced below or the first image of the listing in the selection below.\"]\n
			Text[5] = [\"price (Classifieds Only)\", \"These settings apply to classifieds only.\"]\n
			Text[6] = [\"Entry Date (while browsing)\", \"Checking this box will display the entry date within the site's browsing tables.\"]\n

			Text[7] = [\"payment types\", \"Checking this box will display a series of 'payment types accepted' checkboxes for the seller to select from while listing their auctions.\"]\n
			Text[8] = [\"time left\", \"Checking this box will display in the site's browsing tables the amount of time remaining for the auction before it will expire.\"]\n
			Text[9] = [\"number of bids\", \"Checking this box will display the current number of bids that have been placed on the auction in a column of the site's browsing tables.\"]\n
			Text[10] = [\"length of description to display (while browsing)\", \"Number of characters to display when users browse listings\"]\n
			Text[11] = [\"move settings to subcategories also\", \"Check this box if you would like to force these settings onto this category's subcategories as well.\"]\n
			";

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		if ($category_id)
		{
			$this->sql_query = "select * from ".$this->classified_categories_table." where category_id = ".$category_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
		}
		else
			return false;

		if ($result->RecordCount() == 1)
		{
			$show_configuration = $result->FetchRow();
			$category_name = $this->get_category_name($db,$category_id);
			$this->title .= "Categories Setup > Edit > Category Specific Fields";
			$this->description .= "You can control the aspects of the fields used
				within this category (<b>".$category_name."</b>) and its subcategories.  Making changes here will affect the \"place a listing\" and
				\"search\" processes.";


			$this->additional_body_tag_attributes = " onload='javascript:hide_options(".$show_configuration['use_site_default'].");'";
			$this->additional_header_html .= "
				<script type=\"text/javascript\">
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
					function hide_options(use_site_default)
					{
						if (use_site_default==0 || use_site_default==1)
							check = use_site_default;
						else
							check = (document.getElementById('cbox').checked == true) ? 0 : 1;
						if (check==0)
						{
							document.getElementById('cbox').checked = true;
							document.getElementById('aff').style.display = 'none';

						}
						else
						{
							document.getElementById('cbox').checked = false;
							document.getElementById('aff').style.display = '';
						}
					}
					function validate(field)
					{
						if (!(field.value>=0 && field.value<=500))
						{
							alert(\"Must be between 0 and 500. Values outside this range as well as invalid characters will not be submitted.\")
							field.value=\"\"
							field.focus()
						}
					}
				</script>";
			//use_site_default=0 MEANS **USE SITE DEFAULT** (NEGATIVE LOGIC)
			if (!$this->admin_demo())
				$this->body .="<form name=fields_to_use action=index.php?a=6&z=1&c=".$category_id." method=post>";
			$this->body .="<tr>
						<td align=center valign=top class=medium_font colspan=2>
							<input id=cbox onclick=\"javascript:hide_options();\" type=checkbox name=b[use_site_default] value=1 "
							.(($show_configuration['use_site_default']==0) ? "checked" : "").">
							&nbsp;&nbsp;<b>Use Site Default Settings</b>&nbsp;".$this->show_tooltip(1,1)."
						</td>
						<td>&nbsp;<br><br></td>
					</tr>
					<tr id=aff style='display:".(($show["use_site_default"]) ? "" : "").";'>
						<td colspan=100%>
							<table cellpadding=3 cellspacing=1 border=0 align=center class=row_color1>
								<tr class=row_color_black>
									<td align=center class=medium_font_light width=70%><b>field</b></td>
									<td align=center class=medium_font_light width=15%><b>use</b>&nbsp;".$this->show_tooltip(2,1)."</td>
									<td align=center class=medium_font_light width=15%><b>display</b>&nbsp;".$this->show_tooltip(3,1)."</td>
								</tr>";

			if ($this->debug_categories)
			{
				echo $show_configuration['default_display_order_while_browsing_category']." is DEFAULT_DISPLAY_ORDER_WHILE_BROWSING_CATEGORY<br>\n";
			}
			
			$this->body .= "<tr class=row_color2>\n\t<td align=right width=50% class=medium_font>\n\tdefault order of ads while browsing:this category and it's subcategories".$this->show_tooltip(37,1)."</td>\n\t";
			$this->body .= "<td width=50% valign=top class=medium_font>\n\t<select name=b[default_display_order_while_browsing_category]>\n\t\t";
			$order_by_array = array();
			$order_by_array[0] = "default site wide setting or no setting";
			$order_by_array[1] = "price ascending";
			$order_by_array[2] = "price descending";
			//$order_by_array[3] = "placement date ascending";
			$order_by_array[4] = "placement date descending";
			$order_by_array[5] = "title ascending (alphabetical)";
			$order_by_array[6] = "title descending";
			$order_by_array[7] = "city ascending (alphabetical)";
			$order_by_array[8] = "city descending";
			$order_by_array[9] = "state ascending";
			$order_by_array[10] = "state descending";
			$order_by_array[11] = "country ascending";
			$order_by_array[12] = "country descending";
			$order_by_array[13] = "zip ascending";
			$order_by_array[14] = "zip descending";
			$order_by_array[15] = "optional field 1 ascending";
			$order_by_array[16] = "optional field 1 descending";
			$order_by_array[17] = "optional field 2 ascending";
			$order_by_array[18] = "optional field 2 descending";
			$order_by_array[19] = "optional field 3 ascending";
			$order_by_array[20] = "optional field 3 descending";
			$order_by_array[21] = "optional field 4 ascending";
			$order_by_array[22] = "optional field 4 descending";
			$order_by_array[23] = "optional field 5 ascending";
			$order_by_array[24] = "optional field 5 descending";
			$order_by_array[25] = "optional field 6 ascending";
			$order_by_array[26] = "optional field 6 descending";
			$order_by_array[27] = "optional field 7 ascending";
			$order_by_array[28] = "optional field 7 descending";
			$order_by_array[29] = "optional field 8 ascending";
			$order_by_array[30] = "optional field 8 descending";
			$order_by_array[31] = "optional field 9 ascending";
			$order_by_array[32] = "optional field 9 descending";
			$order_by_array[33] = "optional field 10 ascending";
			$order_by_array[34] = "optional field 10 descending";
		
			$order_by_array[45] = "optional field 11 ascending";
			$order_by_array[46] = "optional field 11 descending";
			$order_by_array[47] = "optional field 12 ascending";
			$order_by_array[48] = "optional field 12 descending";
			$order_by_array[49] = "optional field 13 ascending";
			$order_by_array[50] = "optional field 13 descending";
			$order_by_array[51] = "optional field 14 ascending";
			$order_by_array[52] = "optional field 14 descending";
			$order_by_array[53] = "optional field 15 ascending";
			$order_by_array[54] = "optional field 15 descending";
			$order_by_array[55] = "optional field 16 ascending";
			$order_by_array[56] = "optional field 16 descending";
			$order_by_array[57] = "optional field 17 ascending";
			$order_by_array[58] = "optional field 17 descending";
			$order_by_array[59] = "optional field 18 ascending";
			$order_by_array[60] = "optional field 18 descending";
			$order_by_array[61] = "optional field 19 ascending";
			$order_by_array[62] = "optional field 19 descending";
			$order_by_array[63] = "optional field 20 ascending";
			$order_by_array[64] = "optional field 20 descending";
			$order_by_array[43] = "business type ascending";
			$order_by_array[44] = "business type descending";
		
			reset ($order_by_array);
			foreach ($order_by_array as $key => $value)
			{
				$this->body .= "<option value=\"".$key."\" ";
				if ($key == $show_configuration['default_display_order_while_browsing_category'])
					$this->body .= "selected";
				$this->body .= ">".$value."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t</td>\n</tr>\n";

			$this->row_count=0;
			//display photo column
			if ($this->is_class_auctions() || $this->is_auctions())
			{
				//display photo column
				$this->body .= "<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font>
										<b>Photo Icon/Thumbnail (while browsing)</b>&nbsp;".$this->show_tooltip(4,1)."
									</td>
									<td>&nbsp;</td>
									<td align=center valign=top class=medium_font>
										<input id=display type=checkbox name=b[display_photo_icon] value=1 "
										.(($show_configuration['display_photo_icon']==1) ? "checked" : "")."></td>
								</tr>";$this->row_count++;
			}
			// Title Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Title</b></td>
									<td>&nbsp;</td>
									<td align=center valign=top class=medium_font>
										<input id=display type=checkbox name=b[display_ad_title] value=1 "
										.(($show_configuration['display_ad_title']==1) ? "checked" : "").">
									</td>
								</tr>";$this->row_count++;

			// Description Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Description</b></td>
									<td>&nbsp;</td>
									<td valign=top align=center class=medium_font>
										<input id=display type=checkbox name=b[display_ad_description] value=1 "
										.(($show_configuration['display_ad_description']==1) ? "checked" : "").">
									</td>
								</tr>";$this->row_count++;

			if ($this->is_class_auctions() || $this->is_classifieds())
			{
				// Price Field
				$this->body .= "<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font>".$price_name.$this->show_tooltip(5,1)."</td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_price_field] value=1 "
										.(($show_configuration['use_price_field']==1) ? "checked" : "").">
									</td>
									<td valign=top align=center class=medium_font>
										<input id=display type=checkbox name=b[display_price] value=1 "
										.(($show_configuration['display_price']==1) ? "checked" : "").">
									</td>
								</tr>";$this->row_count++;
			}
			// Country Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Country</b></td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_country_field] value=1 "
										.(($show_configuration['use_country_field']==1) ? "checked" : "").">
									</td>
									<td valign=top align=center class=medium_font>
										<input id=display type=checkbox name=b[display_browsing_country_field] value=1 "
										.(($show_configuration['display_browsing_country_field']==1) ? "checked" : "").">
									</td>
								</tr>";$this->row_count++;

			// State Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>State</b></td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_state_field] value=1 "
										.(($show_configuration['use_state_field']==1) ? "checked" : "").">
									</td>
									<td valign=top align=center class=medium_font>
										<input id=display type=checkbox name=b[display_browsing_state_field] value=1 "
										.(($show_configuration['display_browsing_state_field']==1) ? "checked" : "").">
									</td>
								</tr>";$this->row_count++;

			// City Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>City</b></td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_city_field] value=1 "
										.(($show_configuration['use_city_field']==1) ? "checked" : "").">
									</td>
									<td valign=top align=center class=medium_font>
										<input id=display type=checkbox name=b[display_browsing_city_field] value=1 "
										.(($show_configuration['display_browsing_city_field']==1) ? "checked" : "").">
									</td>
								</tr>";$this->row_count++;

			// Zip Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Zip</b></td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_zip_field] value=1 "
										.(($show_configuration['use_zip_field']==1) ? "checked" : "").">
									</td>
									<td valign=top align=center class=medium_font>
										<input id=display type=checkbox name=b[display_browsing_zip_field] value=1 "
										.(($show_configuration['display_browsing_zip_field']==1) ? "checked" : "").">
									</td>
								</tr>";$this->row_count++;

			// Phone 1 Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Phone 1</b></td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_phone_1_option_field] value=1 "
										.(($show_configuration['use_phone_1_option_field']==1) ? "checked" : "").">
									</td>
									<td>&nbsp;</td>
								</tr>";$this->row_count++;

			// Phone 2 Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Phone 2</b></td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_phone_2_option_field] value=1 "
										.(($show_configuration['use_phone_2_option_field']==1) ? "checked" : "").">
									</td>
									<td>&nbsp;</td>
								</tr>";$this->row_count++;

			// Fax Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Fax</b></td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_fax_field_option] value=1 "
										.(($show_configuration['use_fax_field_option']==1) ? "checked" : "").">
									</td>
									<td>&nbsp;</td>
								</tr>";$this->row_count++;

			//Url Link Fields
			for ($field=1;$field<4;$field++)
			{
				$this->body .= "<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>URL Link ".$field."</b></td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_url_link_".$field."] value=1 "
										.(($show_configuration["use_url_link_".$field]==1) ? "checked" : "").">
									</td>
									<td>&nbsp;</td>
								</tr>";$this->row_count++;
			}

			// Email Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Email</b></td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_email_option_field] value=1 "
										.(($show_configuration['use_email_option_field']==1) ? "checked" : "").">
									</td>
									<td valign=top align=center class=medium_font>
										<input id=display type=checkbox name=b[publically_expose_email] value=1 "
										.(($show_configuration['publically_expose_email']==1) ? "checked" : "").">
									</td>
								</tr>";$this->row_count++;

			// Mapping Address Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Mapping Address</b></td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_mapping_address_field] value=1 "
										.(($show_configuration['use_mapping_address_field']==1) ? "checked" : "").">
									</td>
									<td>&nbsp;</td>
								</tr>";$this->row_count++;

			// Mapping City Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Mapping City</b></td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_mapping_city_field] value=1 "
										.(($show_configuration['use_mapping_city_field']==1) ? "checked" : "").">
									</td>
										<td>&nbsp;</td>
								</tr>";$this->row_count++;

			// Mapping State Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Mapping State</b></td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_mapping_state_field] value=1 "
										.(($show_configuration['use_mapping_state_field']==1) ? "checked" : "").">
									</td>
									<td>&nbsp;</td>
								</tr>";$this->row_count++;

			// Mapping Country Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Mapping Country</b></td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_mapping_country_field] value=1 "
										.(($show_configuration['use_mapping_country_field']==1) ? "checked" : "").">
									</td>
									<td>&nbsp;</td>
								</tr>";$this->row_count++;

			// Mapping Zip Field
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Mapping Zip</b></td>
									<td valign=top align=center class=medium_font>
										<input id=use type=checkbox name=b[use_mapping_zip_field] value=1 "
										.(($show_configuration['use_mapping_zip_field']==1) ? "checked" : "").">
									</td>
									<td>&nbsp;</td>
								</tr>";$this->row_count++;

			// PayPal "Buy Now"
			if(file_exists("../classes/class.paypal.php"))
			{
				$this->body .= "<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>PayPal \"Buy Now\"</b>&nbsp;".$this->show_tooltip(7,1)."</td>
									<td valign=top align=center class=medium_font>
										<input type=checkbox id=use name=b[use_buy_now] value=1 "
										.(($show_configuration['use_buy_now']==1) ? "checked" : "").">
									</td>
									<td>&nbsp;</td>
								</tr>";$this->row_count++;
			}

			//display date posted column
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font>
										<b>Entry Date (while browsing)</b>&nbsp;".$this->show_tooltip(6,1)."</td>
									<td>&nbsp;</td>
									<td align=center valign=top class=medium_font>
										<input id=display type=checkbox name=b[display_entry_date] value=1 "
										.(($show_configuration['display_entry_date']==1) ? "checked" : "").">
									</td>
								</tr>";$this->row_count++;

			//display date posted column
			$this->body .= "	<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Business Type</b></td>
									<td>&nbsp;</td>
									<td align=center valign=top class=medium_font>
										<input id=display type=checkbox name=b[display_business_type] value=1 "
										.(($show_configuration['display_business_type']==1) ? "checked" : "").">
									</td>
								</tr>";$this->row_count++;

			if ($this->is_class_auctions() || $this->is_auctions())
			{
				//payment types
				//NOTE - payment_types is a USE variable
				//NOTE - payment_types_use is a REQUIRE variable
				$this->body .= "<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Payment Types</b>&nbsp;".$this->show_tooltip(7,1)."</td>
									<td valign=top align=center class=medium_font>
										<input type=checkbox id=use name=b[payment_types] value=1 "
										.(($show_configuration['payment_types']==1) ? "checked" : "").">
									</td>
									<td>&nbsp;</td>
								</tr>";$this->row_count++;

				//display price column
				$this->body .= "<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Time Left (while browsing)</b>&nbsp;".$this->show_tooltip(8,1)."</td>
									<td>&nbsp;</td>
									<td align=center valign=top class=medium_font>
										<input id=display type=checkbox name=b[display_time_left] value=1 "
										.(($show_configuration['display_time_left']==1) ? "checked" : "").">
									</td>
								</tr>";$this->row_count++;

				//display number of bids left column
				$this->body .= "<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>Number of Bids (while browsing)</b>&nbsp;".$this->show_tooltip(9,1)."</td>
									<td>&nbsp;</td>
									<td align=center valign=top class=medium_font>
										<input id=display type=checkbox name=b[display_number_bids] value=1 "
										.(($show_configuration['display_number_bids']==1) ? "checked" : "").">
									</td>
								</tr>";$this->row_count++;
			}
			$this->body .= "	<tr class=row_color_black>
									<td class=medium_font_light align=right><b>select all:&nbsp;&nbsp;</b></td>
									<td align=center>
										<input id=use_all onclick=\"javascript:check_all(document.fields_to_use,'use');\" type=checkbox>
									</td>
									<td align=center>
										<input id=display_all onclick=\"javascript:check_all(document.fields_to_use,'display');\" type=checkbox>
									</td>
								</tr>";
			if (!$this->admin_demo())
			{
					$this->body .= "	<tr>
									<td colspan=100% align=center>
										<input type=submit value=\"Save\" name=submit>
										<input type=\"button\" onclick=\"reset()\" value=\"reset form\">
									</td>
								</tr>";
			}
					$this->body .= "	<tr bgcolor=000066>
									<td align=left class=large_font_light colspan=100%>
										<b>Miscellaneous Settings</b>
									</td>
								</tr>
								<tr>
									<td colspan=100%>
										<table width=100% cellpadding=0 cellspacing=0 border=0 align=center>
											<tr colspan=100% class=row_color2>
												<td align=right width=50% class=medium_font>
													length of description to display while browsing:&nbsp;".$this->show_tooltip(10,1)."
													<input type=radio name=b[display_all_of_description] value=1 "
													.(($show_configuration['display_all_of_description']==1) ? "checked" : "").">
												</td>
												<td align=left class=medium_font width=50%>
													all of description
												<td>
											<tr class=row_color2>
												<td align=right width=50% class=medium_font>
													<input type=radio name=b[display_all_of_description] value=0 "
													.(($show_configuration['display_all_of_description']==0) ? "checked" : "").">
												</td>
												<td align=left class=medium_font width=50%>
													display this many characters&nbsp;&nbsp;&nbsp;
													<input onkeyup=validate(this) type=text name=b[length_of_description] size=3 maxsize=3 value="
													.$show_configuration['length_of_description'].">
												</td>
											</tr>
										</table>
									</td>
								</tr>";
			$this->row_count=1;
			$this->body .= "	<tr bgcolor=000066>
									<td colspan=100% class=large_font_light>Optional Site Wide Fields for this Category</td>
								</tr>
								<tr>
									<td class=medium_font colspan=100%>
										The fields below give you the ability to control the use and display of the software's \"Optional Site Wide Fields\"
										on a category by category basis. If you make changes here, these changes will override the site-wide settings as
										specified under the LISTING SETUP > FIELDS TO USE menu.
									</td>
								</tr>
								<tr class=row_color_black>
									<td align=center class=medium_font_light><b>optional field admin name(#)</b></td>
									<td align=center class=medium_font_light><b>use</b>".$this->show_tooltip(2,1)."</td>
									<td align=center class=medium_font_light><b>display</b>".$this->show_tooltip(3,1)."</td>
								</tr>";

			//Optional Fields
			$this->row_count=0;
			for($i = 1; $i < 21; $i++)
			{
				$this->body .= "<tr class=".$this->get_row_color().">
									<td align=center valign=top class=medium_font><b>".$this->configuration_data['optional_field_'.$i.'_name']."&nbsp;($i)</b></td>
									<td valign=top align=center class=medium_font>
										<input id=optional_use type=checkbox name=b[use_optional_field_".$i."] value=1 "
										.(($show_configuration["use_optional_field_".$i]==1) ? "checked" : "").">
									</td>
									<td align=center valign=top class=medium_font>
										<input id=optional_display type=checkbox name=b[display_optional_field_".$i."] value=1 "
										.(($show_configuration["display_optional_field_".$i]==1) ? "checked" : "").">
									</td>
								</tr>";$this->row_count++;
			}
			$this->body .= "	<tr class=row_color_black>
									<td class=medium_font_light align=right><b>select all:&nbsp;&nbsp;</b></td>
									<td align=center><input id=optional_use_all onclick=\"javascript:check_all(document.fields_to_use,'optional_use');\" type=checkbox></td>
									<td align=center><input id=optional_display_all onclick=\"javascript:check_all(document.fields_to_use,'optional_display');\" type=checkbox></td>
								</tr>
							</table>
							<table>
								<tr class=row_color1>
									<td width=50% align=right class=medium_font>
										move settings to subcategories also:".$this->show_tooltip(11,1)."</td>
									<td width=50% align=left valign=top class=medium_font>
										<input type=checkbox name=b[subcategories_also] value=1 checked> yes (recommended to save confusion)
									</td>
								</tr>
							</table>
						</td>
					</tr>";
				if (!$this->admin_demo())
				{
					$this->body .= "	<tr>
									<td colspan=100% align=center class=medium_font>
										<input type=submit value=\"Save\" name=submit>
										<input onclick=\"reset()\" type=\"button\" value=\"reset form\">
									</td>
								</tr>";
				}
				$this->body .= "</form>";
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

	function update_fields_to_use($db,$category_id=0,$configuration_info=0)
	{
		$fields = array(
			"use_site_default",
			"display_photo_icon",
			"display_ad_title",
			"display_business_type",
			"display_ad_description",
			"display_all_of_description",
			"length_of_description",
			"display_price",
			"use_price_field",
			"display_browsing_zip_field",
			"use_zip_field",
			"display_browsing_city_field",
			"use_city_field",
			"display_browsing_state_field",
			"use_state_field",
			"display_browsing_country_field",
			"use_country_field",
			"display_entry_date",
			"use_email_option_field",
			"publically_expose_email",
			"use_phone_1_option_field",
			"use_phone_2_option_field",
			"use_fax_field_option",
			"use_mapping_address_field",
			"use_mapping_city_field",
			"use_mapping_state_field",
			"use_mapping_country_field",
			"use_mapping_zip_field",
			"use_url_link_1",
			"use_url_link_2",
			"use_url_link_3",
			"display_number_bids",
			"display_time_left",
			"use_buy_now",
			"payment_types",
			"editable_bid_start_time_field",
			"default_display_order_while_browsing_category"
			);
		for ($i=1;$i<21;$i++)
		{
			array_push($fields,"use_optional_field_".$i);
			array_push($fields,"display_optional_field_".$i);
		}
		$sql_query = "update ".$this->classified_categories_table." set ";
		foreach ($fields as $value)
		{
			if ($value=="use_site_default")
			{
				//use_site_default=0 MEANS **USE SITE DEFAULT** (NEGATIVE LOGIC)
				$sql_query .= $value." = ".($configuration_info[$value] ? 0 : 1).", ";
			}
			else
				$sql_query .= $value." = ".($configuration_info[$value] ? $configuration_info[$value] : 0).", ";
		}
		$sql_query = substr($sql_query,0,-2);//strip off comma
		$sql_query .= " where category_id ";
		if ($category_id && $configuration_info)
		{
			//run category query only here
			$a_sql_query = $sql_query ." = ".$category_id;
			$result = $db->Execute($a_sql_query);
			if (!$result)
			{
				//echo $sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
		}
		if ($configuration_info["subcategories_also"])
		{
			//run subcategory query here
			$this->get_sql_in_statement($db,$category_id);
			$a_sql_query = $sql_query.$this->in_statement;
			$result = $db->Execute($a_sql_query);
			if (!$result)
			{
				//echo $sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
		}

		//reset the order by setting in the site configuration
		$this->sql_query = "select * from ".$this->classified_categories_table." where default_display_order_while_browsing_category > 0";
		$result = $db->Execute($this->sql_query);
		if ($this->debug_categories) echo  $this->sql_query."<br>\n";
		if (!$result)
		{
			if ($this->debug_categories)
			{
				echo  $this->sql_query."<br>\n";
				echo $db->ErrorMsg()."<br>\n";
			}
			return false;
		}
		else
		{
			$this->sql_query = "select default_display_order_while_browsing from ".$this->site_configuration_table;
			$order_result = $db->Execute($this->sql_query);
			if ($this->debug_categories) echo  $this->sql_query."<br>\n";
			if (!$order_result)
			{
				if ($this->debug_categories)
				{
					echo  $this->sql_query."<br>\n";
					echo $db->ErrorMsg()."<br>\n";
				}
				return false;
			}
			elseif ($order_result->RecordCount() == 1)
			{
				$show_display_order = $order_result->FetchRow();

				if ($result->RecordCount() > 0)
				{
					//make sure the site wide setting is 1000...category specific setting
					$this->sql_query = "update ".$this->site_configuration_table." set
								default_display_order_while_browsing =1000";
					$update_order_result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo  $this->sql_query."<br>\n";
					if (!$update_order_result)
					{
						if ($this->debug_categories)
						{
							echo  $this->sql_query."<br>\n";
							echo $db->ErrorMsg()."<br>\n";
						}
						return false;
					}
				}
				else
				{
					//make sure the site wide setting is not 1000...if it
					if ($show_display_order["default_display_order_while_browsing"] == 1000)
					{
						//change from category specific setting to 0...no ordering as the category specific setting does not exist anymore
						$this->sql_query = "update ".$this->site_configuration_table." set
									default_display_order_while_browsing =0";
						$update_order_result = $db->Execute($this->sql_query);
						if ($this->debug_categories) echo  $this->sql_query."<br>\n";
						if (!$update_order_result)
						{
							if ($this->debug_categories)
							{
								echo  $this->sql_query."<br>\n";
								echo $db->ErrorMsg()."<br>\n";
							}
							return false;
						}
					}

				}
			}
		}
		return true;

	} // end of function update_fields_to_use

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function add_category_specific_length($db,$new_length_info=0,$category_id=0)
	{
		if ($this->debug_categories)
		{
			echo "<BR>ADD_CATEGORY_SPECIFIC_LENGTHS<br>\n";
			echo $new_length_info["display_length_of_ad"]." is new_length_info - display_length_of_ad<br>\n";
			echo $new_length_info["length_of_ad"]." is new_length_info - length_of_ad<br>\n";
			echo $category_id." is category_id<br>\n";
		}
		if (($category_id) && ($new_length_info))
		{
			//check length_of_ad to see if int
			//check length_charge to see if double or int
			if (ereg("[0-9]+", $new_length_info["length_of_ad"]))
			{
				$this->sql_query = "select * from  ".$this->classified_price_plan_lengths_table."
					where length_of_ad = ".$new_length_info["length_of_ad"]." and price_plan_id = 0 and category_id = ".$category_id;
				$result = $db->Execute($this->sql_query);
				if ($this->debug_categories) echo $this->sql_query."<Br>";
				if (!$result)
				{
					if ($this->debug_categories)
					{
						echo $this->sql_query."<Br>";
						echo $db->ErrorMsg()."<br>\n";
					}
					return false;
				}
				elseif ($result->RecordCount() == 0 )
				{
					$this->sql_query = "insert into ".$this->classified_price_plan_lengths_table."
						(price_plan_id,category_id,length_of_ad,display_length_of_ad,length_charge,renewal_charge)
						values
						(0,".$category_id.",".$new_length_info["length_of_ad"].",\"".$new_length_info["display_length_of_ad"]."\",0,0)";
					$insert_result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo $this->sql_query."<Br>";
					if (!$insert_result)
					{
						if ($this->debug_categories)
						{
							echo $this->sql_query."<Br>";
							echo $db->ErrorMsg()."<br>\n";
						}
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
	} //end of function add_category_specific_length

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function category_specific_delete_length($db,$length_id=0)
	{
		if ($length_id)
		{
			$this->sql_query = "delete from  ".$this->classified_price_plan_lengths_table." where length_id = ".$length_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<Br>";
			if (!$result)
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
	} //end of function delete_length

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function category_specific_lengths_form($db,$category_id=0)
	{
		if ($category_id)
		{
			$category_name = $this->get_category_name($db,$category_id);

			$this->sql_query = "select * from ".$this->classified_price_plan_lengths_table." where price_plan_id = 0 and category_id = ".$category_id." order by length_of_ad asc";
			$length_result = $db->Execute($this->sql_query);
			if (!$length_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=6&z=2&c=".$category_id." method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center>\n";
			$this->title = "Categories Setup > Edit > Base Length of Listing - ".$category_name."";
			$this->description = "Control the choices your users have for the length of days their
				listings are displayed in this form.  This only affects users within this category (".$category_name.") and are the default lengths of listings for this category
				and its subcategories (unless a subcategory has its own lengths set).  Delete the values you do not want by clicking the delete link next
				to them. Add a value by using the short form at the bottom and clicking \"add value\".  The values will always appear in numerical order.
				If you do not want to set listings lengths for this category (and subcategories) do not enter any.  If there are not any lengths
				set for this category or any of its direct parent categories the site defaults set within
				LISTING SETUP > LENGTH OF LISTINGS will be used for this category.";

			$this->body .= "<tr>\n\t<td align=center class=medium_font>\n\t<b>Length of Listing</b><br>(displayed) \n\t</td>\n\t";
			$this->body .= "<td align=center class=medium_font>\n\t<b>Length of Listing</b><br>(# of days) \n\t</td>\n\t";
			$this->body .= "<td align=center class=medium_font>\n\t&nbsp; \n\t</td>\n</tr>\n";
			$this->row_count = 0;
			while ($show_lengths = $length_result->FetchRow())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font align=center>".$show_lengths["display_length_of_ad"]."</td>\n\t";
				$this->body .= "<td class=medium_font align=center>".$show_lengths["length_of_ad"]."</td>\n\t";
				$this->body .= "<td>\n\t<a href=index.php?a=6&c=".$category_id."&z=2&d=".$show_lengths["length_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a>\n\t</td>\n</tr>\n";
				$this->row_count++;
			}
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td>\n\t<input type=text name=b[display_length_of_ad]>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font><input type=text name=b[length_of_ad]></td>\n\t";
			if (!$this->admin_demo())
				$this->body .= "<td >\n\t<input type=submit name=submit value=\"Save\">\n\t</td>\n";
			$this->body .= "</tr>\n";
			$this->body .= "<tr>\n\t<td colspan=5>\n\t<a href=index.php?a=7&b=".$category_id."><span class=medium_font>
				<b>back to ".$category_name." Main</b></span></a>\n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td colspan=5>\n\t<a href=index.php?a=5&b=".$category_id."><span class=medium_font>
					<b>back to ".$category_name." category edit page</b></span></a>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}
		else
		{
			return false;
		}

	} //end of function lengths_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function reset_all_category_counts($db)
	{
		$this->sql_query = "select * from ".$this->classified_categories_table;
		$category_result = $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br><br>\n";
		if (!$category_result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[5501];
			return false;
		}
		if ($category_result->RecordCount() > 0)
		{
			while ($show_category = $category_result->FetchRow())
			{
				$this->update_category_count($db,$show_category["category_id"]);
			}
		}
		return true;
	} //end of function reset_all_category_counts

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function duplicate_category_structure($db,$start_category,$target_category=0,$copy_questions_too=0,$recursive=0)
	{
		if (($start_category) && ($target_category))
		{
			$this->sql_query = "select * from ".$this->classified_categories_table." where parent_id = ".$start_category;
			$start_result = $db->Execute($this->sql_query);
			if ($this->debug_categories) echo $this->sql_query."<br>\n";
			if (!$start_result)
			{
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
			elseif ($start_result->RecordCount() > 0)
			{
				//there are some categories to copy - do it
				while ($starting_subcategory = $start_result->FetchRow())
				{
					$this->sql_query = "insert into ".$this->classified_categories_table."
						(parent_id,category_name,description,display_order,category_image)
						values
						(".$target_category.",\"".$starting_subcategory["category_name"]."\",\"".$starting_subcategory["description"]."\",
						".$starting_subcategory["display_order"].",\"".$starting_subcategory["category_image"]."\")";
					$result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					if (!$result)
					{
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						$this->error_message = $this->messages[3500];
						return false;
					}
					$category_id = $db->Insert_ID();

					if ($copy_questions_too == 1)
					{
						$this->sql_query = "select * from ".$this->sell_questions_table." where category_id = ".$starting_subcategory["category_id"];
						$category_question_result = $db->Execute($this->sql_query);
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						if (!$category_question_result)
						{
							if ($this->debug_categories) echo $this->sql_query."<br>\n";
							$this->error_message = $this->messages[5501];
							return false;
						}
						elseif ($category_question_result->RecordCount() > 0)
						{
							while ($show_category_question = $category_question_result->FetchRow())
							{
								$this->sql_query = "insert into ".$this->sell_questions_table."
									(category_id, name, explanation, choices, other_input, display_order)
									values
									(".$category_id.", \"".addslashes($show_category_question["name"])."\",
									\"".addslashes($show_category_question["explanation"])."\", \"".$show_category_question["choices"]."\",
									".$show_category_question["other_input"].",".$show_category_question["display_order"]." )";

								if ($this->debug_categories) echo $this->sql_query."<br>\n";
								$result = $db->Execute($this->sql_query);
								if (!$result)
								{
									if ($this->debug_categories) echo $this->sql_query."<br>\n";
									$this->error_message = $this->messages[5501];
									return false;
								}
							}
						}
					}

					$this->sql_query = "select distinct(language_id) from ".$this->pages_languages_table;
					$language_result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					if (!$language_result)
					 {
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						$this->error_message = $this->messages[3501];
						return false;
					 }
					 elseif ($language_result->RecordCount() > 0)
					 {
						while ($show = $language_result->FetchRow())
						{
							$this->sql_query = "select * from ".$this->classified_categories_languages_table."
								where category_id = ".$starting_subcategory["category_id"]."
								and language_id = ".$show["language_id"];
							$category_language_result = $db->Execute($this->sql_query);
							if ($this->debug_categories) echo $this->sql_query."<br>\n";
							if (!$category_language_result)
							{
								if ($this->debug_categories) echo $this->sql_query."<br>\n";
								$this->error_message = $this->messages[3500];
								return false;
							}
							elseif ($category_language_result->RecordCount() == 1)
							{
								$show_category_language = $category_language_result->FetchRow();
								$this->sql_query = "insert into ".$this->classified_categories_languages_table."
									(category_id,template_id,secondary_template_id,ad_display_template_id,category_name,description,language_id)
									values
									(".$category_id.",\"".$show_category_language["template_id"]."\",
									\"".$show_category_language["secondary_template_id"]."\",\"".$show_category_language["ad_display_template_id"]."\",
									\"".addslashes(urlencode($show_category_language["category_name"]))."\",\"".addslashes(urlencode($show_category_language["description"]))."\",".$show["language_id"].")";
								$insert_category_language_result = $db->Execute($this->sql_query);
								if ($this->debug_categories) echo $this->sql_query."<br>\n";
								if (!$insert_category_language_result)
								{
									if ($this->debug_categories) echo $this->sql_query."<br>\n";
									$this->error_message = $this->messages[3500];
									return false;
								}
							}
						}
					}

					if ($recursive)
					{
						//do a recursive call to all subcategories to copy them also
						$this->duplicate_category_structure($db,$starting_subcategory["category_id"],$category_id,$copy_questions_too,$recursive);
					}

					$category_in_statement = "";
					$category_in_statement = $this->get_sql_in_statement($db,$category_id);

					$this->sql_query = "update ".$this->classified_categories_table." set
						in_statement = \"".$category_in_statement."\" where category_id = ".$category_id;
					$result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					if (!$result)
					{
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						$this->error_message = $this->messages[3500];
						return false;
					}

					//get sql in statement for this category
				} // end of while

				//reset sql in statement for categories above
				if ($target_category)
				{
					//check if parent is main category
					$current_parent_category = $target_category;
					while ($current_parent_category != 0)
					{
						$parent_in_statement = "";
						$parent_in_statement = $this->get_sql_in_statement($db,$current_parent_category);
						$this->sql_query = "update ".$this->classified_categories_table." set
							in_statement = \"".$parent_in_statement."\" where category_id = ".$current_parent_category;
						//echo $this->sql_query."<br>\n";
						$result = $db->Execute($this->sql_query);
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						if (!$result)
						{
							if ($this->debug_categories) echo $this->sql_query."<br>\n";
							return false;
						}

						$this->sql_query = "SELECT parent_id FROM ".$this->classified_categories_table." WHERE category_id = ".$current_parent_category;
						$result = $db->Execute($this->sql_query);
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						if (!$result)
						{
							if ($this->debug_categories) echo $this->sql_query."<br>\n";
							return false;
						}
						elseif ($result->RecordCount() == 1)
						{
							$show_category = $result->FetchRow();
							$current_parent_category = $show_category["parent_id"];
						}
						else
						{
							//$this->body .=$this->sql_query." is the query where count is not 1<br>\n";
							return false;
						}
					} //end of while
				}
				return $target_category;
			}
			else
			{
				if ($this->debug_categories) echo "hello from returning true<br>\n";
				return true;
			}
		}
		else
			return false;

	} // end of function duplicate_category_structure

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function duplicate_structure_form($db)
	{
		$this->get_configuration_data($db);
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"copy immediate subcategories of this category\", \"This is the category whose subcategories you wish to copy to another category.\"]\n
			Text[2] = [\"category you want the subcategories copied to\", \"This is the category you wish to copy the subcategories to.\"]\n
			Text[3] = [\"copy category specific questions also\", \"Checking \\\"yes\\\" will copy the category specific fields/questions of each subcategory over to the new subcategories.\"]\n
			Text[4] = [\"copy all sub categories recursively\", \"Checking \\\"yes\\\" will copy the subcategories as well as all of their subcategories also...recursively.\"]\n";

		//".$this->show_tooltip(1,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=72 method=post>\n";
		$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% class=row_color1>\n";
		$this->title = "Categories Setup > Copy Subcategories";
		$this->description = "This form will allow you to copy a category's subcategories to another category.  Please carefully select the appropriate settings below
		before saving your selection.";

		$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font width=50%>\n\tcopy immediate subcategories from:".$this->show_tooltip(1,1)."</td>\n\t";
		$this->body .= "<td valign=top width=50%>\n\t";
		$this->get_category_dropdown($db,"b",0,1,$this->configuration_data["levels_of_categories_displayed_admin"]);
		$this->body .= $this->dropdown_body;
		$this->body .= "</td></tr>\n";

		$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font>\n\tcopy immediate subcategories to:".$this->show_tooltip(2,1)."</td>\n\t";
		$this->body .= "<td valign=top>\n\t";
		$this->get_category_dropdown($db,"c",0,1,$this->configuration_data["levels_of_categories_displayed_admin"]);
		$this->body .= $this->dropdown_body;
		$this->body .= "</td></tr>\n";

		$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font>\n\tinclude category specific questions also:".$this->show_tooltip(3,1)."</td>\n\t";
		$this->body .= "<td valign=top class=medium_font>\n\tyes<input type=radio name=d value=1 class=medium_font><br>
			no<input type=radio name=d value=0 checked class=medium_font></td></tr>\n";

		$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font>\n\tinclude subcategory subcategories also recursively:".$this->show_tooltip(4,1)."</td>\n\t";
		$this->body .= "<td valign=top class=medium_font>\n\tyes<input type=radio name=e value=1 class=medium_font><br>
			no<input type=radio name=e value=0 checked class=medium_font><bR>PLEASE NOTE THAT IF THERE ARE HUNDREDS OR THOUSANDS OF
			SUBCATEGORIES TO COPY YOU MAY REACH THE MAXIMUM EXECUTION TIME FOR PHP ON YOUR SERVER...STOPPING THE PROCEDURE
			BEFORE IT IS FINISHED.</td></tr>\n";
		if (!$this->admin_demo())
		{
			$this->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center>
				<input type=submit value=\"Save\"> \n\t</td>\n</tr>\n";
		}
		$this->body .= "</table></form>";
		return true;

	} //end of function duplicate_structure_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function duplicate_category_questions($db,$start_category,$target_category=0)
	{
		if (($start_category) && ($target_category))
		{
			$this->sql_query = "select * from ".$this->sell_questions_table." where category_id = ".$start_category;
			$category_question_result = $db->Execute($this->sql_query);
			if ($this->debug_categories) echo $this->sql_query."<br>\n";
			if (!$category_question_result)
			{
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($category_question_result->RecordCount() > 0)
			{
				while ($show_category_question = $category_question_result->FetchRow())
				{
					$this->sql_query = "insert into ".$this->sell_questions_table."
						(category_id, name, explanation, choices, other_input, display_order)
						values
						(".$target_category.", \"".addslashes($show_category_question["name"])."\",
						\"".addslashes($show_category_question["explanation"])."\", \"".$show_category_question["choices"]."\",
						".$show_category_question["other_input"].",".$show_category_question["display_order"]." )";
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						$this->error_message = $this->messages[5501];
						return false;
					}
				}
				return true;
			}
			else
				return true;
		}
		else
			return false;

	} // end of function duplicate_category_questions

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function duplicate_questions_form($db)
	{
		$this->get_configuration_data($db);
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"Copy Category Questions\", \"This is the category FROM which whose questions you wish to copy to another category.\"]\n
			Text[2] = [\"Copy Category Questions\", \"This is the category you wish to copy the above category's questions TO.\"]\n";

		//".$this->show_tooltip(2,1)."

		// Set style for tooltip
		//$this->body .= "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=73 method=post>\n";
		$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% class=row_color1>\n";
		$this->title .= "Categories Setup - Copy Category Questions";
		$this->description .= "This form will allow you to copy a category's category speicific questions to another category on the site.";

		$this->body .= "<tr class=row_color1>\n\t<td width=50% align=right valign=top class=medium_font>\n\t<b>copy questions from:</b>".$this->show_tooltip(1,1)."</td>\n\t";
		$this->body .= "<td valign=top>\n\t";
		$this->get_category_dropdown($db,"b",0,1,$this->configuration_data["levels_of_categories_displayed_admin"]);
		$this->body .= $this->dropdown_body;
		$this->body .= "</td></tr>\n";

		$this->body .= "<tr class=row_color1>\n\t<td align=right valign=top class=medium_font>\n\t<b>copy questions to:</b>".$this->show_tooltip(2,1)."</td>\n\t";
		$this->body .= "<td valign=top>\n\t";
		$this->get_category_dropdown($db,"c",0,1,$this->configuration_data["levels_of_categories_displayed_admin"]);
		$this->body .= $this->dropdown_body;
		$this->body .= "</td></tr>\n";
		if (!$this->admin_demo())
		{
			$this->body .= "<tr>\n\t<td colspan=2 align=center><br>
				<input type=submit value=\"Save\"> \n\t</td>\n</tr>\n";
		}
		$this->body .= "</table></form>";
		return true;

	} //end of function duplicate_questions_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_subcategory_check($db,$category=0)
	{
		if ($category)
		{
			$category_name = $this->get_category_name($db,$category);
			$this->sql_query = "select * from ".$this->classified_categories_table." where category_id = ".$category;
			$result = $db->Execute($this->sql_query);
			if ($this->debug_categories) echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
				if ($show["parent_id"])
				{
					$this->sql_query = "select * from ".$this->classified_categories_languages_table." where category_id = ".$show["parent_id"]." and language_id = 1";
					$result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					if (!$result)
					{
						if ($this->debug_categories)
						{
							echo $this->sql_query."<br>\n";
							echo $db->ErrorMsg()." is the error<Br>\n";
						}
						$this->error_message = $this->messages[3500];
						return false;
					}
					elseif ($result->RecordCount() == 1)
					{
						$show_parent = $result->FetchRow();

						$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100% class=row_color1>\n";
						$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=very_large_font_light><b>Categories Setup</b> \n\t</td>\n</tr>\n";
						$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=medium_font_light>
							Verify deletion of a subcategories. \n\t</td>\n</tr>\n";
						$this->body .= "<tr>\n\t<td class=medium_font>Are you sure you want to
							delete the <b>".$category_name."</b> category. \n\t</td>\n</tr>\n";
						$this->body .= "<tr>\n\t<td><a href=index.php?a=105&b=".$category."&c=move>
							<span class=medium_font>Move the listings in the subcategories of the <b>".$category_name["category_name"]."</b> category to the ".
							$category_name." category.</span></a>\n\t</td>\n</tr>\n";
						$this->body .= "<tr>\n\t<td class=medium_error_font>\n\t<b>All category specific questions will be removed
							from the database. </b> \n\t</td>\n</tr>\n";
						$this->body .= "</table>\n";
					}
					else
					{
						if ($this->debug_categories)
						{
							echo $this->sql_query."<br>\n";
							echo "the recordcount for above was: ".$result->RecordCount()."<bR>\n";
						}
						$this->error_message = $this->messages[3500];
						return false;
					}
				}
				else
				{
					//delete a main category
					$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100% class=row_color1>\n";
						$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=very_large_font_light><b>Categories Setup</b> \n\t</td>\n</tr>\n";
						$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=medium_font_light>
							Verify deletion of a category. \n\t</td>\n</tr>\n";
					$this->body .= "\n<tr>\n\t<td class=medium_font>Are you sure you want to
						delete the <b>".$category_name."</b> category.  If so click the link below.  If you choose to delete the
						category the only option is to delete the listings currently within it also. \n\t</td>\n</tr>\n";
					$this->body .= "<tr>\n\t<td><a href=index.php?a=105&b=".$category."&c=delete><span class=medium_font>
						Delete the listings in the <b>".$category_name."</b> category along with the subcategories.</span></a>\n\t</td>\n</tr>\n";
					$this->body .= "<tr>\n\t<td class=medium_error_font>\n\t<b>All category specific questions will be removed
						from the database. </b> \n\t</td>\n</tr>\n";

					$this->body .= "</table>\n";
				}
				return true;
			}
			else
			{
				$this->error_message = $this->messages[3500];
				return false;
			}
		}
		else
		{
			$this->error_message = $this->messages[3509];
			return false;
		}
	} // end of function delete_subcategory_check

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_subcategories($db,$category=0,$type_of_delete=0)
	{
		if ($this->debug_categories)
		{
			echo "<BR>TOP OF DELETE_SUBCATEGORIES<bR>\n";
		}
		if ($category)
		{
			$this->sql_query = "select * from ".$this->classified_categories_table." where parent_id = ".$category;
			$parent_result = $db->Execute($this->sql_query);
			if ($this->debug_categories) echo $this->sql_query."<br>\n";
			if (!$parent_result)
			{
				if ($this->debug_categories) echo $this->sql_query."<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
			elseif ($parent_result->RecordCount() > 0)
			{
				while ($show_subcategory = $parent_result->FetchRow())
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						category = ".$category."
						where category = ".$show_subcategory["category_id"];
					$update_result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					if (!$update_result)
					{
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						$this->error_message = $this->messages[3500];
						return false;
					}

					$in_statement = "";
					$in_statement = $this->get_sql_in_statement($db,$show_subcategory["category_id"]);

					$this->sql_query = "select filter_id,user_id,category_id,sub_category_check from ".$this->classified_ad_filter_table."
						where category_id ".$in_statement;
					$select_filter_result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					if (!$select_filter_result)
					{
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						$this->error_message = $this->messages[3500];
						return false;
					}
					elseif ($select_filter_result->RecordCount() > 0)
					{
						while ($show = $select_filter_result->FetchRow())
						{
							$this->sql_query = "delete from ".$this->classified_ad_filter_categories_table."
								where filter_id = ".$show["filter_id"];
							$delete_filter_result = $db->Execute($this->sql_query);
							if ($this->debug_categories) echo $this->sql_query."<br>\n";
							if (!$delete_filter_result)
							{
								if ($this->debug_categories) echo $this->sql_query."<br>\n";
								$this->error_message = $this->messages[3500];
								return false;
							}
						}
					}


					$this->sql_query = "delete from ".$this->classified_ad_filter_table."
						where category_id ".$in_statement;
					$delete_category_result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					if (!$delete_category_result)
					{
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						$this->error_message = $this->messages[3500];
						return false;
					}

					$this->sql_query = "delete from ".$this->classified_ad_filter_categories_table."
						where category_id ".$in_statement;
					$delete_filter_result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					if (!$delete_filter_result)
					{
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						$this->error_message = $this->messages[3500];
						return false;
					}

					$this->sql_query = "delete from ".$this->classified_categories_languages_table."
						where category_id  ".$in_statement;
					$delete_category_result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					if (!$delete_category_result)
					{
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						$this->error_message = $this->messages[3500];
						return false;
					}

					$this->sql_query = "delete from ".$this->classified_price_plans_categories_table."
						where category_id  ".$in_statement;
					$delete_category_result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					if (!$delete_category_result)
					{
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						$this->error_message = $this->messages[3500];
						return false;
					}

					$this->sql_query = "delete from ".$this->classified_categories_table."
						where category_id  ".$in_statement;
					$delete_category_result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					if (!$delete_category_result)
					{
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						$this->error_message = $this->messages[3500];
						return false;
					}

					$this->sql_query = "delete from ".$this->sell_questions_table."
						where category_id  ".$in_statement;
					$delete_question_result = $db->Execute($this->sql_query);
					if ($this->debug_categories) echo $this->sql_query."<br>\n";
					if (!$delete_question_result)
					{
						if ($this->debug_categories) echo $this->sql_query."<br>\n";
						$this->error_message = $this->messages[3500];
						return false;
					}
				}
			}
			else
			{
				$this->body .= "there were no subcategory<BR>\n";
			}
			return true;
		}
		else
		{
			$this->error_message = $this->messages[3509];
			return false;
		}
	} // end of function delete_subcategories

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%


} // end of class Admin_categories

?>
