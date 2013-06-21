<?// admin_html_allowed_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class HTML_allowed extends Admin_site {

	var $internal_error_message = "There was an internal error";
	var $data_error_message = "Not enough data to complete request";
	var $page_text_error_message = "No text connected to this page";
	var $no_pages_message = "No pages to list";

	var $html_allowed_message;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function HTML_allowed($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_html_allowed_list($db)
	{
		$sql_query = "select * from ".$this->html_allowed_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=24 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Site Setup > Allowed HTML";
			$this->description = "Use this administration tool to declare HTML tags you do not wish to
				appear in areas of your site that registrants enter text.  The tags declared in this list by using a check will be removed
				from text that registrants enter into the new listings they place.  The HTML tag will
				simply be removed with no replacement.  The tags with * are strongly recommended to exclude as malicious code
				could be entered using some of them while others could affect the formatting of your pages with malformed html.";
			if ($this->html_allowed_message)
				$this->body .= "<tr>\n\t<td class=medium_error_font>\n\t".$this->html_allowed_message."</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td align=center>\n\t<table cellpadding=2 cellspacing=1 border=0>\n\t";
			$this->body .= "<tr class=row_color_black>\n\t<td align=right class=medium_font_light>\n\ttag</td>\n\t";
			$this->body .= "<td class=medium_font_light>\n\tdisallowed</td>\n\t";
			$this->body .= "<td class=medium_font_light>\n\tallowed</td>\n</tr>";
			$this->row_count = 0;
			while ($show = $result->FetchRow())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>\n\t".$show["tag_name"];
				if ($show["strongly_recommended"] == 1)
					$this->body .= "<span class=medium_error_font>*</span>";
				$this->body .= "</td>\n\t";
				$this->body .= "<td align=center class=medium_font>\n\t<input type=radio name=b[".$show["tag_id"]."] value=1 ";
				if ($show["tag_status"] == 1)
					$this->body .= "checked";
				$this->body .= ">";

				$this->body .= "\n\t</td>\n\t";
				$this->body .= "<td align=center class=medium_font>\n\t<input type=radio name=b[".$show["tag_id"]."] value=0 ";
				if ($show["tag_status"] == 0)
					$this->body .= "checked";
				$this->body .= "></td>\n</tr>\n";
				$this->row_count++;
			}
			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td align=center class=medium_font colspan=3>\n\t<input type=submit name=save_html_disallowed value=\"Save\">\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}

	} //end of function display_html_allowed_list

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_html_allowed_list($db,$allowed_info=0)
	{
		if ($allowed_info)
		{
			$sql_query = "select * from ".$this->html_allowed_table;
			$html_result = $db->Execute($sql_query);
			//echo $sql_query." is the query<bR>\n";
			if (!$html_result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($html_result->RecordCount() > 0)
			{
				//echo $html_result->RecordCount()." is result count<br>\n";
				while ($show = $html_result->FetchRow())
				{
					//echo $allowed_info[$show["tag_id"]]." is allowed<bR>\n";
					//echo  $show["tag_status"]."<br><br>\n";
					if ($allowed_info[$show["tag_id"]] != $show["tag_status"])
					{
						$sql_query = "update ".$this->html_allowed_table." set
							tag_status = ".$allowed_info[$show["tag_id"]]."
							where tag_id = ".$show["tag_id"];
						//echo $sql_query." is the query<bR>\n";
						$update_result = $db->Execute($sql_query);
						if (!$update_result)
						{
							$this->site_error($db->ErrorMsg());
							return false;
						}
					}
				}
				return true;
			}
			else
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_html_allowed_list

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class HTML_allowed


?>