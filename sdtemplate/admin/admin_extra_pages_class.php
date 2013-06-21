<?//admin_extra_pages_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Admin_extra_pages extends Admin_site{

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Admin_extra_pages($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function add_edit_extra_page_configuration_form($db,$page_id=0)
	{
		if ($page_id)
		{
			$this->sql_query = "select * from ".$this->extra_pages_table." where page_id = ".$page_id." order by page_title";
			$result = &$db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=33&b=1&c=".$page_id." method=post>\n";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
				$this->body .= "<tr class=row_color_red> \n\t<td colspan=2 class=very_lage_font_light>\n\t<b>Extra Page Management</b> \n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color_red> \n\t<td colspan=2 class=medium_font_light>\n\tEdit the extra page through this
					form.  Make the changes you need and click the submit button at the bottom. \n\t</td>\n</tr>\n";
			}
			else
			{
				return false;
			}
		}
		else
		{
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=33&b=3 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
			$this->body .= "<tr class=row_color_red> \n\t<td colspan=2 class=very_lage_font_light>\n\t<b>Extra Page Management</b> \n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_red> \n\t<td colspan=2 class=medium_font_light>\n\tAdd the extra page through the
				form below.  Fill in the a title for the page and then paste your HTML into the page content textarea below.  Then click the
				submit button at the bottom to save the page.  The link to the page will appear in the extra page list afterwards. \n\t</td>\n</tr>\n";
		}


		if ($this->site_configuration_message)
			$this->body .= "<tr>\n\t<td colspan=2 class=medium_error_font>\n\t".$this->site_configuration_message." \n\t</td>\n</tr>\n";

		$this->body .= "<tr class=row_color2>\n\t<td align=right class=medium_font>\n\textra page title: \n\t</td>\n\t";
		$this->body .= "<td valign=top class=medium_font>\n\t<input type=text name=d[page_title] value=\"".stripslashes(urldecode($show["page_title"]))."\"> \n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td valign=top colspan=2 class=medium_font>\n\tpage content<br>
			<textarea name=d[page_content] cols=50 rows=30>".$this->special_chars(stripslashes(urldecode($show["page_content"])))."</textarea> \n\t</td>\n</tr>\n";
		if (!$this->admin_demo())
			$this->body .= "<tr>\n\t<td colspan=2 class=medium_font>\n\t<input type=submit name=submit value=\"Save\"> \n\t</td>\n</tr>\n";
		$this->body .= "</table>\n</form>\n";
		return true;

	} //end of function add_extra_page_configuration_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function extra_page_home($db)
	{
		$this->sql_query = "select * from ".$this->site_configuration_table;
		//echo $this->sql_query."<br>\n";
		$result = &$db->Execute($this->sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show_configuration = $result->FetchRow();
		}
		else
		{
			return false;
		}

		$this->sql_query = "select * from ".$this->extra_pages_table;
		//echo $this->sql_query."<br>\n";
		$result = &$db->Execute($this->sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		else
		{
			//show list of pages to edit or delete
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
			$this->title = "Extra Page Management";
			$this->description = "Below is the list of extra
				pages you currently have within the database.  To add a new extra page click the add extra page link at the bottom.  To edit an existing
				page click the edit button next to the adjacent page.  To delete the existing page click the delete button next to the adjacent page.
				The link to display the extra page within your site is in the link column below.";

			if ($result->RecordCount() > 0)
			{
				$this->body .= "<tr>\n\t<td>\n\t";
				$this->body .= " <table cellpadding=2 cellspacing=1 border=0 align=center class=row_color1 width=100%>\n";
				$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light>\n\tpage title \n\t</td>\n\t";
				$this->body .= "<td class=medium_font_light>\n\tlink to page \n\t</td>\n\t";
				$this->body .= "<td class=medium_font_light>\n\t&nbsp; \n\t</td>\n\t";
				$this->body .= "<td class=medium_font_light>\n\t&nbsp; \n\t</td>\n</tr>\n";
				$this->row_count = 0;
				while ($show = $result->FetchRow())
				{
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t".urldecode($show["page_title"])." \n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t".$show_configuration["classifieds_url"]."?a=22&b=".$show["page_id"]." \n\t</td>\n\t";
					$this->body .= "<td>\n\t<a href=index.php?a=33&b=1&c=".$show["page_id"]."><span class=medium_font>edit </a>\n\t</td>\n\t";
					$this->body .= "<td>\n\t<a href=index.php?a=33&b=2&c=".$show["page_id"]."><span class=medium_font>delete </a></td>\n</tr>\n";
					$this->row_count++;
				}
				$this->body .= "</table>\n";
				$this->body .= "</td>\n</tr>\n";
			}
			$this->body .= "<tr>\n\t<td>\n\t<a href=index.php?a=33&b=3><span class=medium_font>click to add a new extra page </a>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			return true;
		}

	} //end of function extra_page_home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_extra_page($db,$page_id=0)
	{
		if ($page_id)
		{
			$this->sql_query = "delete from ".$this->extra_pages_table." where page_id = ".$page_id;
			$result = &$db->Execute($this->sql_query);
			if (!$result)
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

	} //end of function delete_extra_page

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_extra_page($db,$page_id=0,$page_info=0)
	{
		if (($page_id) && ($page_info))
		{
			$this->sql_query = "update ".$this->extra_pages_table." set
				page_title = \"".urlencode($page_info["page_title"])."\",
				page_content = \"".urlencode($page_info["page_content"])."\"
				where page_id = ".$page_id;
			$result = &$db->Execute($this->sql_query);
			if (!$result)
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

	} //end of function delete_extra_page

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_extra_page($db,$page_info=0)
	{
		if ($page_info)
		{
			$this->sql_query = "insert into ".$this->extra_pages_table."
				(page_title,page_content)
				values
				(\"".urlencode($page_info["page_title"])."\",\"".urlencode($page_info["page_content"])."\")";
			$result = &$db->Execute($this->sql_query);
			if (!$result)
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

	} //end of function delete_extra_page

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of Admin_extra_pages