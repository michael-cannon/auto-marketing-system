<?// admin_text_management_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Text_badwords_management extends Admin_site {

	var $internal_error_message = "There was an internal error";
	var $data_error_message = "Not enough data to complete request";
	var $page_text_error_message = "No text connected to this page";
	var $no_pages_message = "No pages to list";

	var $text_management_title_message = "Site Setup > Badwords";
	var $text_management_instruction_message = "You can control the badwords the software searches for in user
		entered text through this administration tool.  Enter a new badword and what text to possibly replace it with
		in the space next to it.  If the badword replacement is left blank the badword will be removed from user entered
		text with no replacement. To remove a badword
		from the list just click delete next to it.";

	var $badword_error;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Text_badwords_management($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_badword_list($db)
	{
		$sql_query = "select * from ".$this->badwords_table." order by badword_id";
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=15 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Site Setup > Badwords";
			$this->description = "Use this administration tool to declare words you do not wish to
				appear in areas of your site that registrants enter text.  The words declared in this list will be removed
				from text that registrants enter into the new listings they place.  The text you declare on the left will be
				replaced with the text you declare on the right.  If there is no replacement text the badword will simply be
				removed.<br><br>
				<b>ORDER IS IMPORTANT:</b><br>
				Pay attention to the order of the words from top to bottom in the list below.  The filter starts at the top of the list then moves to
				the bottom replacing text each row.  There could be a problem if one word is a subword of another in the bottom of the list.  The
				word lower in the list would never be replaced.<br><br>
				<b>FOR EXAMPLE:</b><Br>If two words were in the list: \"dummy\" replaced
				with \"stupid\" and \"dummyhead\" replaced with \"lalala\".  If
				\"dummy\" were above \"dummyhead\" in the list \"dummyhead\" would never match because by the time the filter got to filtering
				\"dummyhead\" the \"dummy\" part of the word would have already been replaced.  Most instances this would not be a problem
				but in some instances it might.  Make sure that words or word snippet appearing within other (longer) words appear later or after
				their longer counterparts.";

			$this->body .= "<tr>\n\t<td align=center>\n\t<table cellpadding=3 cellspacing=1 border=0 class=row_color2>\n";
			$this->body .= "<tr>\n\t<td colspan=3 class=large_font align=center>\n\t<b>New Badword Form</b> \n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_black>\n\t<td colspan=3>\n\t \n\t</td>\n</tr>\n";
			if ($this->badword_error)
				$this->body .= "<tr>\n\t<td colspan=3 class=medium_error_font>\n\t".$this->badword_error." \n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td class=medium_font>\n\t<b>badword</b>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\t<b>replace with</b>\n\t</td>\n\t";
			$this->body .= "<td>\n\t&nbsp;\n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td>\n\t<input type=text name=b[badword] size=30 maxsize=30>\n\t</td>\n\t";
			$this->body .= "<td>\n\t<input type=text name=b[badword_replacement] size=30 maxsize=30>\n\t</td>\n\t";
			if (!$this->admin_demo()) $this->body .= "<td>\n\t<input type=submit name=save_badword value=\"Save\">\n\t</td>\n";
			$this->body .= "</tr>\n";
			$this->body .= "</table>\n\t</td>\n</tr>\n";

			$this->body .= "<tr>\n<td>\n\t";
			$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=50% align=center>\n";
			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=3 class=large_font_light align=center>\n\tcurrent badword list \n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td class=medium_font_light>\n\t<b>badword</b>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font_light>\n\t<b>replace with</b>\n\t</td>\n\t";
			$this->body .= "<td>\n\t&nbsp;\n\t</td>\n</tr>\n";

			if ($result->RecordCount() > 0)
			{
				$this->row_count = 0;
				while ($show = $result->FetchRow())
				{
					$this->display_this_badword($show);
					$this->row_count++;
				}
			}
			else
			{
				$this->body .= "<tr>\n\t<td colspan=3 class=medium_error_font align=center>There are currently no badwords.</td>\n</tr>\n";
			}
			$this->body .= "</table>\n</td>\n</tr>\n";

			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}

	} //end of function display_page_messages

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_this_badword($text)
	{

		$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t<b>";
		$this->body .= $text["badword"]."</b> \n\t</td>\n\t";
		$this->body .= "<td class=medium_font>\n\t".$text["badword_replacement"]." \n\t</td>\n\t";
		$this->body .= "<td align=center width=100>\n\t<a href=index.php?a=15&c=".$text["badword_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a>\n\t</td>\n</tr>\n";

	} //end of function display_text_message

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_badword($db,$badword_info=0)
	{
		if ($badword_info)
		{
			$sql_query = "select * from ".$this->badwords_table." where badword = \"".$badword_info["badword"]."\"";
			$result = $db->Execute($sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 0)
			{

				$sql_query = "insert into ".$this->badwords_table."
					(badword,badword_replacement)
					values
					(\"".$badword_info["badword"]."\",\"".$badword_info["badword_replacement"]."\")";
				$result = $db->Execute($sql_query);
				if (!$result)
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
				$this->badword_error = "That word already exists in the badword list";
				return true;
			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function insert_badword

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_badword($db,$badword_id=0)
	{
		if ($badword_id)
		{
			$sql_query = "delete from ".$this->badwords_table."
				where badword_id = ".$badword_id;
			$result = $db->Execute($sql_query);
			if (!$result)
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
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function delete_badword

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function badword_management_error()
	{
		$this->body .= "<table cellpadding=5 cellspacing=1 border=0>\n";
		$this->body .= "<tr>\n\t<td>There was an error</td>\n</tr>\n";
		if ($this->error_message)
			$this->body .= "<tr>\n\t<td>".$this->error_message."</td>\n</tr>\n";
		$this->body .= "</table>\n";
	} //end of function badword_management_error

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Text_management


?>