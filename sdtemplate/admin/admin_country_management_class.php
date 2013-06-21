<?// admin_country_management_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Country_management extends Admin_site {

	var $internal_error_message = "There was an internal error";
	var $data_error_message = "Not enough data to complete request";
	var $page_text_error_message = "No text connected to this page";
	var $no_pages_message = "No pages to list";

	var $text_management_title_message = "Geographic Setup > Country Management Admin";
	var $text_management_instruction_message = "You can control the countries that this application displays in it's country dropdown
		boxes throughoutt the site within this administration tool.  Add new countries by using the form supplied or delete a specific country
		using the delete button next to the country you wish to delete.  The countries will appear in alphabetical order.";

	var $country_error;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Country_management($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_country_list($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "
			Text[1] = [\"display order\", \"Control the display order of all your dropdown boxes here.  Countries will be displayed according to their \\\"display order\\\" in ascending order.\"]\n
			Text[2] = [\"tax\", \"Values can range from 0.0001 to 99999.9999\"]\n
			Text[3] = [\"tax type\", \"Decide whether to charge a flat tax vs. a percentage based on the <b>user's registered country.</b> <br>For example, if you wish to charge $1.25 tax for every purchase, regardless of the amount of purchase, then choose \\\"flat tax\\\" and set tax = 1.25 <br>If however, you wish to charge a 1.25% tax based on the amount of purchase, then choose \\\"rate\\\" and set tax = 1.25 <br><b>WARNING: The tax will be calculated using the same type of currency as the sub-total at the time of purchase. </b>\"]\n
			";
		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$sql_query = "select * from ".$this->countries_table." order by display_order, name";
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		$this->title = "Geographic Setup > Countries";
		$this->description = "You can control the Countries that this application displays in the Country dropdown boxes
			that are displayed at various locations throughout your site. Add or delete Countries by using the form below. The
			Countries will appear in alphabetical order.<br><br>
			<b>Note: Entries are required in both the country and abbreviation fields. </b>";
		if (!$this->admin_demo())
			$this->body .= "<form action=index.php?a=22 method=post>\n";
		$this->body .= "
			<script type=\"text/javascript\">
				function validate(field)
				{
					re = /([^0-9.])/;
					if (field.value.match(re))
					{
						alert('You must enter a numeric value only.');
						field.value=\"\";
						field.focus();
					}
				}
				function display_percent(field,percent_id)
				{
					if (field.value == 1)
						document.getElementById(percent_id).style.display = 'none';
					else
						document.getElementById(percent_id).style.display = '';
				}
			</script>
			<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>
				<tr>
					<td align=center>
						<table cellpadding=3 cellspacing=1 border=0 class=row_color2 width=100%>
							<tr bgcolor=000066>
								<td colspan=100% class=large_font_light align=center>
									<b>New Country Form</b>
								</td>
							</tr>
							<tr class=row_color_black>
								<td class=medium_font_light width=30%>
									<b>country</b>
								</td>
								<td class=medium_font_light width=10%>
									<b>abbrev.</b>
								</td>
								<td class=medium_font_light width=15%>
									<nobr><b>display order</b>".$this->show_tooltip(1,1)."</nobr>
								</td>
								<td class=medium_font_light width=15%>
									<nobr><b>tax</b>".$this->show_tooltip(2,1)."</nobr>
								</td>
								<td class=medium_font_light width=20%>
									<nobr><b>tax type</b>".$this->show_tooltip(3,1)."</nobr>
								</td>
								<td width=10%>&nbsp;</td>
							</tr>
							";
		if ($this->country_error)
			$this->body .= "<tr>
								<td colspan=100% class=medium_error_font align=center>
									".$this->country_error."
								</td>
							</tr>";
		$this->body .= "
							<tr class=row_color2>
								<td class=medium_font>
									<input type=text name=b[name] size=30 maxsize=255>
								</td>
								<td>
									<input type=text name=b[abbreviation] size=5 maxsize=255>
								</td>
								<td>
									<input onkeyup=\"javascript:validate(this)\" type=text name=b[order] size=5>
								</td>
								<td>
									<nobr><input onkeyup=\"javascript:validate(this)\" type=text name=b[tax] size=8 maxlength=10><b id=percent>%</b></nobr>
								</td>
								<td class=medium_font>
									<nobr><input onclick=\"javascript:display_percent(this,'percent')\" type=radio name=b[tax_type] value=0 checked><b>rate</b> (ie 8.25 %)</nobr><br>
									<nobr><input onclick=\"javascript:display_percent(this,'percent')\" type=radio name=b[tax_type] value=1><b>flat tax</b></nobr>
								</td>";
		if (!$this->admin_demo())
			$this->body .= "	<td align=center>
									<input type=submit name=save_country value=\"Save\">
								</td>
							</tr>
							<tr bgcolor=000066>
								<td colspan=100% class=large_font_light align=center>
									<nobr><b>Current Country List</b></nobr>
								</td>
							</tr>
							<tr class=row_color_black>
								<td class=medium_font_light>
									<b>country</b>
								</td>
								<td class=medium_font_light>
									<b>abbrev.</b>
								</td>
								<td class=medium_font_light>
									<nobr><b>display order</b>".$this->show_tooltip(1,1)."</nobr>
								</td>
								<td class=medium_font_light>
									<nobr><b>tax</b>".$this->show_tooltip(2,1)."</nobr>
								</td>
								<td class=medium_font_light>
									<nobr><b>tax type</b>".$this->show_tooltip(3,1)."</nobr>
								</td>
								<td>&nbsp;</td>
							</tr>";

		if ($result->RecordCount() > 0)
		{
			$this->row_count = 0;
			$count = 0;
			while ($show = $result->FetchRow())
			{
				$this->display_this_country($show, $count, $result);
				$this->row_count++;
				$count++;
			}
		}
		else
		{
			$this->body .= "<tr>
								<td colspan=100% class=medium_error_font>
									there are no countries to display
								</td>
							</tr>";
		}
		$this->body .= "</table>
					</td>
				</tr>
				<tr colspan=100% align=center>
					<td>
						<input type=reset value=\"Reset\">";
		if (!$this->admin_demo())
			$this->body .= "
						<input type=submit name=save_orders value=\"Save\">";
		$this->body .= "
					</td>
				</tr>
			</table>";
		return true;
	} //end of function display_country_list

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_this_country($text, $count, $result=0)
	{
		$display_tax_rate = (strstr($text["tax"],'.')) ? rtrim(rtrim($text["tax"],0),'.') : $text["tax"];
		$display_tax_value = sprintf("%01.2f",$text["tax"]);
		$this->body .= "
			<tr class=".$this->get_row_color().">
				<td class=medium_font>
					<b>".$text["name"]."</b>
				</td>
				<td class=medium_font>
					<b>".$text["abbreviation"]."</b>
				</td>
				<td>
					<input onkeyup=\"javascript:validate(this)\" type=text name='d[order".$count."]' value='".$text["display_order"]."' size=5>

				</td>
				<td>
					<nobr><input onkeyup=\"javascript:validate(this)\" type=text name='d[tax".$count."]' value='".(($text['tax_type'] == 0) ? $display_tax_rate : $display_tax_value)."' size=8 maxlength=10>
					<b id=percent".$count." style=display:"
					.(($text['tax_type'] == 1) ? 'none' : '').">%</b></nobr>
				</td>
				<td class=medium_font valign=top>
					<nobr><input onclick=\"javascript:display_percent(this,'percent".$count."')\" type=radio name='d[tax_type".$count."]' value=0 "
					.(($text['tax_type'] == 0) ? 'checked' : '').">
					<b>rate</b> (ie 8.25 %)</nobr><br>
					<nobr><input onclick=\"javascript:display_percent(this,'percent".$count."')\" type=radio name='d[tax_type".$count."]' value=1 "
					.(($text['tax_type'] == 1) ? 'checked' : '').">
					<b>flat tax</b></nobr>
				</td>
				<td align=center>
					<a href=index.php?a=22&c=".$text["country_id"].">
						<span class=medium_font>
							<img src='admin_images/btn_admin_delete.gif' alt=delete border=0>

						</span>
					</a>
				</td>
				<input type=hidden name=d[id".$count."]"." value=".$text["country_id"].">
			</tr>";

	} //end of function display_this_country

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_country($db,$insert_country_info="",$update_country_info="")
	{
		if($update_country_info)
		{
			$sql_query = "SELECT * FROM ".$this->countries_table." ORDER BY display_order,name";
			$result = $db->Execute($sql_query);
			if (!$result)
			{
				return false;
			}
			else
			{
				for($i=0; $i < $result->RecordCount(); $i++)
				{
					$tax = ($update_country_info["tax".$i]) ? $update_country_info["tax".$i] : 0;
					$sql_query = "UPDATE ".$this->countries_table." SET
						display_order = ".$update_country_info["order".$i].",
						tax = $tax,
						tax_type = ".$update_country_info["tax_type".$i]." WHERE
						country_id = ".$update_country_info["id".$i];
					$update = $db->Execute($sql_query);
					if (!$result)
					{
						return false;
					}
				}
			}
		}
		if ($insert_country_info)
		{
			if ((strlen(trim($insert_country_info["name"])) > 0) && (strlen(trim($insert_country_info["abbreviation"])) > 0))
			{
				$sql_query = "SELECT * FROM ".$this->countries_table." WHERE abbreviation = \"".$insert_country_info["abbreviation"]."\"";
				$abbreviation_result = $db->Execute($sql_query);
				if (!$abbreviation_result)
				{
					return false;
				}
				elseif ($abbreviation_result->RecordCount() == 0)
				{
					$sql_query = "INSERT INTO ".$this->countries_table."
						(name,abbreviation, display_order,tax,tax_type)
						VALUES(
							'".$insert_country_info['name']."',
							'".$insert_country_info['abbreviation']."',
							'".$insert_country_info['order']."',
							'".$insert_country_info['tax']."',
							'".$insert_country_info['tax_type']."')";
					$result = $db->Execute($sql_query);
					if (!$result)
					{
						return false;
					}
				}
			}
		}
		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_country($db,$country_id=0)
	{
		if (!$country_id)
			return false;
		$sql_query = "DELETE FROM ".$this->countries_table."
			WHERE country_id = ".$country_id;
		$result = $db->Execute($sql_query);
		if (!$result)
			return false;
		return true;
	} //end of function delete_country

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Country_management


?>