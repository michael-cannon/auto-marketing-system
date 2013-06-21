<? //module_display_search_box_1.php	
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$this->get_css($db,$show_module['page_id']);
$this->get_text($db,$show_module['page_id']);


$this->body = "<table width=100% cellspacing=1 cellpadding=3 border=0>";
$this->body .= "<form action=".$this->configuration_data['classifieds_file_name']."?a=19 method=post>\n";
$this->body .= "<tr>\n<td class=search_box_1_input><input type=text name=b[search_text] class=search_box_1_input></td>";
$this->body .= "<td class=search_box_1_input>";
$this->get_category_dropdown($db,"c",$this->site_category,0,"search_box_1_input");
$this->body .= "</td>";
if ($show_module['display_category_description'])
{
	$this->get_ad_configuration($db);
	$this->get_category_configuration($db,$this->site_category,0);
		
	if (!$this->category_configuration->USE_SITE_DEFAULT)
	{
		//echo "using site settings<br>\n";
		$this->field_configuration_data = $this->ad_configuration_data;
		$this->field_configuration_data->USE_OPTIONAL_FIELD_1 = $this->configuration_data['use_optional_field_1'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_2 = $this->configuration_data['use_optional_field_2'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_3 = $this->configuration_data['use_optional_field_3'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_4 = $this->configuration_data['use_optional_field_4'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_5 = $this->configuration_data['use_optional_field_5'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_6 = $this->configuration_data['use_optional_field_6'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_7 = $this->configuration_data['use_optional_field_7'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_8 = $this->configuration_data['use_optional_field_8'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_9 = $this->configuration_data['use_optional_field_9'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_10 = $this->configuration_data['use_optional_field_10'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_11 = $this->configuration_data['use_optional_field_11'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_12 = $this->configuration_data['use_optional_field_12'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_13 = $this->configuration_data['use_optional_field_13'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_14 = $this->configuration_data['use_optional_field_14'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_15 = $this->configuration_data['use_optional_field_15'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_16 = $this->configuration_data['use_optional_field_16'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_17 = $this->configuration_data['use_optional_field_17'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_18 = $this->configuration_data['use_optional_field_18'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_19 = $this->configuration_data['use_optional_field_19'];
		$this->field_configuration_data->USE_OPTIONAL_FIELD_20 = $this->configuration_data['use_optional_field_20'];			
	}
	else
	{
		//echo "using category settings<br>\n";
		$this->field_configuration_data = $this->category_configuration;
	}

	$this->body .= "<td class=search_box_1_input><select name=b[search_by_field] class=search_box_1_input>\n";
	$this->body .= "<option value=all_fields>".urldecode($this->messages[1867])."</option>\n";	
	$this->body .= "<option value=title_only>".urldecode($this->messages[1868])."</option>\n";	
	$this->body .= "<option value=description_only>".urldecode($this->messages[1869])."</option>\n";	
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_1)	
		$this->body .= "<option value=optional_field_1>".urldecode($this->messages[1870])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_2)	
		$this->body .= "<option value=optional_field_2>".urldecode($this->messages[1871])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_3)	
		$this->body .= "<option value=optional_field_3>".urldecode($this->messages[1872])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_4)	
		$this->body .= "<option value=optional_field_4>".urldecode($this->messages[1873])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_5)	
		$this->body .= "<option value=optional_field_5>".urldecode($this->messages[1874])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_6)	
		$this->body .= "<option value=optional_field_6>".urldecode($this->messages[1875])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_7)	
		$this->body .= "<option value=optional_field_7>".urldecode($this->messages[1876])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_8)	
		$this->body .= "<option value=optional_field_8>".urldecode($this->messages[1877])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_9)	
		$this->body .= "<option value=optional_field_9>".urldecode($this->messages[1878])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_10)	
		$this->body .= "<option value=optional_field_10>".urldecode($this->messages[1879])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_11)	
		$this->body .= "<option value=optional_field_11>".urldecode($this->messages[1880])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_12)	
		$this->body .= "<option value=optional_field_12>".urldecode($this->messages[1881])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_13)	
		$this->body .= "<option value=optional_field_13>".urldecode($this->messages[1882])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_14)	
		$this->body .= "<option value=optional_field_14>".urldecode($this->messages[1883])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_15)	
		$this->body .= "<option value=optional_field_15>".urldecode($this->messages[1884])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_16)	
		$this->body .= "<option value=optional_field_16>".urldecode($this->messages[1885])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_17)	
		$this->body .= "<option value=optional_field_17>".urldecode($this->messages[1886])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_18)	
		$this->body .= "<option value=optional_field_18>".urldecode($this->messages[1887])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_19)	
		$this->body .= "<option value=optional_field_19>".urldecode($this->messages[1888])."</option>\n";
	if ($this->field_configuration_data->USE_OPTIONAL_FIELD_20)	
		$this->body .= "<option value=optional_field_20>".urldecode($this->messages[1889])."</option>\n";		
	$this->body .= "</select></td>";
}
$this->body .= "<td class=search_box_1_input><input type=hidden name=b[search_descriptions] value=1>
	<input type=hidden name=b[subcategories_also] value=1>
	<input type=hidden name=b[search_titles] value=1>
	<input type=submit value=\"".urldecode($this->messages[1627])."\" class=search_box_1_submit></td></tr>";
$this->body .= "</form>\n</table>";
?>