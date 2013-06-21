<? //admin_css_management.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class css_management extends Admin_site
{
	function css_management($db, $product_configuration=0)
	{
		$this->Admin_Site($db, $product_configuration);
	}

	function show_all_css_forms($db)
	{
		$function_name = "show_all_css_forms";

		//$this->sql_query = "select distinct element_id, element, page_id from ".$this->pages_fonts_table;
		$this->sql_query = "select distinct element from ".$this->pages_fonts_table;
		$font_result = $db->Execute($this->sql_query);

		// Error checking
		if(!$font_result)
			return false;

		$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
		if ($this->ad_configuration_message)
			$this->body .= "<tr>\n\t<td colspan=2 class=medium_error_font>".$this->ad_configuration_message."</font>\n\t</td>\n</tr>\n";
		$this->title = "CSS Management";
		$this->description = "You can select the CSS tags below that you wish to edit by clicking on the edit button.  If you know the CSS tags you are looking for either search below or press Control-F when the page is done loading and type in the name and press enter.  To set all fonts to one specification click <a href=\"index.php?a=108&b=5\">here</a> or to one color click <a href=\"index.php?a=108&b=6\">here</a>.  To toggle whether to use the site's built in CSS or to use your own files then check the site configuration <a href=\"index.php?a=28&z=6\"> here</a>.  WARNING: This page may take a while to load even on fast internet connections!!!";

		// Display Search
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=108&b=1 method=post>\n";
		$this->body .= "<tr class=row_color1>\n\t";
		$this->body .= "<td align=center ccolspan=2 class=medium_font>\n\tSearch for CSS tags by name:<br>";
		$this->body .= "<input type=text name=z[search]></font>\n\t";
		$this->body .= "<input type=submit value=\"Save\">\n\t</td>\n\t</tr>\n";
		$this->body .= "</form>";

		while($font = $font_result->FetchRow())
		{
			// Alternate background color using Sayle's (almost) patented method
			if(!$mod)
				$this->body .= "<tr class=row_color2>\n\t";
			else
				$this->body .= "<tr class=row_color1>\n\t";

			$this->body .= "<td align=center width=50% valign=middle class=medium_font>\n\t".$font["element"]."</font>\n\t\n\t</td>\n\t";
			$this->body .= "<td align=center valign=middle class=medium_font>\n\t<a href=\"index.php?a=108&b=2&c=".$font["element"]."\"><img src=\"admin_images/btn_admin_edit.gif\" border=0></a>\n</tr>";

			// Do the alternation
			$mod = !$mod;
		}

		$this->body .= "</table>";

		return true;
	}

	function check_page($db, $css)
	{
		$function_name = "check_page";

		// Check for duplicates
		$this->sql_query = "select f.element, f.element_id, f.page_id, p.name, p.description from ".$this->pages_fonts_table." as f, ".$this->pages_table." as p where f.element = \"".$css."\" and f.page_id = p.page_id group by f.page_id order by p.page_id asc";
		$font_result = $db->Execute($this->sql_query);

		if(!$font_result)
			return false;

		$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
		if ($this->ad_configuration_message)
			$this->body .= "<tr>\n\t<td colspan=2 class=medium_error_font>".$this->ad_configuration_message."</font>\n\t</td>\n</tr>\n";
		$this->title = "CSS Management > Edit";
		$this->description = "The CSS tag you clicked on is attached to multiple pages.  Below please click on the individual one that you wish to edit.";

		while($font = $font_result->FetchRow())
		{
			// Alternate background color using Sayle's (almost) patented method
			if(!$mod)
				$this->body .= "<tr class=row_color2>\n\t";
			else
				$this->body .= "<tr class=row_color1>\n\t";

				$this->body .= "<td align=center width=50% valign=middle>\n\t<a href=\"index.php?a=44&z=3&b=".$font["page_id"]."\"><span class=medium_font>".$font["name"]."</a></span>\n\t<br><span class=small_font>".$font["description"]."</span>\n\t</td>\n\t";
			$this->body .= "<td align=center width=50% valign=middle class=medium_font>\n\t".$font["element"]."</td>\n\t";
			$this->body .= "<td align=center valign=middle class=medium_font>\n\t<a href=\"index.php?a=38&b=".$font["page_id"]."&c=".$font["element_id"]."\"><img src=\"admin_images/btn_admin_edit.gif\" border=0></a>\n</tr>\n";

			// Do the alternation
			$mod = !$mod;
		}
		// Display the edit all button if more than 1 record
		if($font_result->RecordCount() > 1)
			$this->body .= "<tr>\n\t<td class=medium_font align=center colspan=3>\n\t<a href=\"index.php?a=108&b=3&c=".$css."\"><br><br><b>Edit All Instances</b></a></td>\n\t</tr>\n";
		$this->body .= "</table>";

		return true;
	}

	function edit_all($db, $css)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"decoration\", \"This removes or places (underline) special decoration that the browser may place on text - for example links may be a different color and underlined by the browser where it leaves normal text alone.  Choosing \\\"none\\\" in this field will make the browser remove the special decoration from the link text.\"]\n
			Text[2] = [\"background color\", \"This places a background color around the text, table data box or table row (most cases) that the text appears on. Many combinations of colors can be achieved with this control.\"]\n
			Text[3] = [\"background image\", \"This places a background image around the text, table data box or table row (most cases) where this text appears.\"]\n
			Text[4] = [\"horizontal text align\", \"This is the horizontal alignment of the text within the area it appears.\"]\n
			Text[5] = [\"vertical text align\", \"This is the vertical alignment of the text within the area it appears.\"]\n";

		//".$this->show_tooltip(5,1)."

		// Set style for tooltip
		//$this->body .= "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$function_name = "edit_all";

		// If all is select we need a different query
		if(!strcmp($css, "font_all") || !strcmp($css, "color_all"))
			$this->sql_query = "select page_id, element_id from ".$this->pages_fonts_table;
		else
			$this->sql_query = "select page_id, element_id from ".$this->pages_fonts_table." where element = \"".$css."\"";
		$result = $db->Execute($this->sql_query);

		if(!$result)
			return false;

		$page = $this->get_page($db,$page_id);
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=108&b=4&c=".$css." method=post>\n";
		$this->body .= "<table cellpadding=2 cellspacing=0 border=0 class=row_color1>\n";
		$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=very_large_font_light><b>".$this->text_management_title_message."</b></td>\n</tr>\n";
		$this->title = "CSS Element Edit";
		$this->description = "Edit the CSS tags in this form.  Below are all the elements of the
			tags you can edit.  Many of the css tags modify the table row or table data tag that the text is in.  This can allow you
			to modify the background colors of that element when possible.";
		if(strcmp($css, "font_all") != 0 && strcmp($css, "color_all") != 0)
		{
			$this->body .= "<tr class=row_color2><td align=right class=medium_font>color chart:</font></td><td>";
			$this->body .= "<a href=\"javascript:win('colorcodes.html','500','400')\"><span class=medium_font><img src=admin_images/btn_admin_colors.gif alt=colors border=0></span></a>";
			$this->body .= "</td>\n</tr>\n";
		}
		if(strcmp($css, "color_all") != 0 && !strcmp($css, "font_all"))
		{
			$this->body .= "<tr class=row_color2><td align=right class=medium_font>font family:</td><td>";
			$this->display_font_type_select($db,"z[font_family]",$text["font_family"]);
			$this->body .= "</td>\n</tr>\n";
		}
		if(strcmp($css, "font_all") != 0 && strcmp($css, "color_all") != 0)
		{
			$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\tsize:</td>\n\t<td>\n\t";
			$this->body .= $this->display_font_size_select($db,"z[font_size]",$text["font_size"]);
			$this->body .= "</td>\n</tr>\n";
			$this->body .= "<tr class=row_color2>\n\t<td align=right class=medium_font>\n\tstyle:</td>\n\t<td>\n\t";
			$this->body .= $this->display_font_style_select($db,"z[font_style]",$text["font_style"]);
			$this->body .= "</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\tweight:";
			$this->body .= "</td>\n\t<td valign=top>\n\t";
			$this->body .= $this->display_font_weight_select($db,"z[font_weight]",$text["font_weight"]);
			$this->body .= "</td>\n</tr>\n";
		}

		if(strcmp($css, "font_all") != 0 && !strcmp($css, "color_all"))
		{
			$this->body .= "<tr class=row_color2>\n\t<td align=right class=medium_font>\n\ttext color:";
			$this->body .= "</td>\n\t<td valign=top>\n\t
				<input type=text name=z[color] value=\"".$text["color"]."\"></td>\n</tr>\n";
		}

		if(strcmp($css, "font_all") != 0 && strcmp($css, "color_all") != 0)
		{
			$this->body .= "<tr class=row_color2>\n\t<td align=right width=45% class=medium_font>\n\tdecoration:".$this->show_tooltip(1,1);
			$this->body .= "</td>\n\t<td valign=top>\n\t";
			$this->body .= $this->display_font_decoration_select($db,"z[text_decoration]",$text["text_decoration"]);
			$this->body .= "</td>\n</tr>\n";

			$this->body .= "<tr class=row_color1>\n\t<td align=right width=45% class=medium_font>\n\tbackground color:".$this->show_tooltip(2,1);
			$this->body .= "</td>\n\t<td valign=top>\n\t";
			$this->body .= "<input type=text name=z[background_color] value=\"".$text["background_color"]."\">";
			$this->body .= "</td>\n</tr>\n";

			$this->body .= "<tr class=row_color2>\n\t<td align=right width=45% class=medium_font>\n\tbackground image:".$this->show_tooltip(3,1);
			$this->body .= "</td>\n\t<td valign=top>\n\t";
			$this->body .= "<input type=text name=z[background_image] value=\"".$text["background_image"]."\">";
			$this->body .= "</td>\n</tr>\n";

			$this->body .= "<tr class=row_color1>\n\t<td align=right width=45% class=medium_font>\n\thorizontal text align:".$this->show_tooltip(4,1);
			$this->body .= "</td>\n\t<td valign=top>\n\t";
			$this->body .= $this->display_text_align_select($db,"z[text_align]",$text["text_align"]);
			$this->body .= "</td>\n</tr>\n";

			$this->body .= "<tr class=row_color2>\n\t<td align=right width=45% class=medium_font>\n\tvertical text align:".$this->show_tooltip(5,1);
			$this->body .= "</td>\n\t<td valign=top>\n\t";
			$this->body .= $this->display_text_vertical_align_select($db,"z[text_vertical_align]",$text["text_vertical_align"]);
			$this->body .= "</td>\n</tr>\n";
		}
		if (!$this->admin_demo())
			$this->body .= "<tr>\n\t<td align=center colspan=2><input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";

		$this->body .= "</table>\n</form>\n";

		return true;
	}

	function update_all($db, $css, $text_info)
	{
		$function_name = "update_all";

		// Once again if all is $css we need a different query
		if(!strcmp($css, "font_all"))
		{
			$this->sql_query = "update ".$this->pages_fonts_table." set
					font_family = \"".$text_info["font_family"]."\"";
		}
		elseif(!strcmp($css, "color_all"))
		{
			$this->sql_query = "update ".$this->pages_fonts_table." set
					color = \"".$text_info["color"]."\"";
		}
		else
		{
			$this->sql_query = "update ".$this->pages_fonts_table." set
					font_family = \"".$text_info["font_family"]."\",
					font_size = \"".$text_info["font_size"]."\",
					font_style = \"".$text_info["font_style"]."\",
					font_weight = \"".$text_info["font_weight"]."\",
					color = \"".$text_info["color"]."\",
					text_decoration = \"".$text_info["text_decoration"]."\",
					background_color = \"".$text_info["background_color"]."\",
					text_align = \"".$text_info["text_align"]."\",
					text_vertical_align = \"".$text_info["text_vertical_align"]."\",
					text_transform = \"".$text_info["text_transform"]."\",
					background_image = \"".$text_info["background_image"]."\"
					where element = \"".$css."\"";
		}

		$result = $db->Execute($this->sql_query);
		if (!$result)
			return false;
		else
		{
			return true;
		}
	}

	function search($db, $css)
	{
		$function_name = "search";

		if(!$this->check_page($db, $css["search"]))
			$this->body .= "No results returned.  Please check and make sure you entered the css tag properly.";
	}
}
?>
