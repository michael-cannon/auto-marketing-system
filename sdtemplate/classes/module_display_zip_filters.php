<? //module_display_zip_filters.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

//if ($this->configuration_data['use_zip_filters'])
//{
	$this->get_css($db,$show_module['page_id']);
	$this->get_text($db,$show_module['page_id']);

	//setup dropdown labels
	//echo $this->zip_filter	." is zip filter in module<BR>\n";
	//echo strlen(trim($this->zip_filter))." is strlen zip filter<br>\n";
	//echo strlen(trim($this->zip_filter))." is strlen zip filter<br>\n";
	//if ($this->zip_filter == 0) echo "zip filter is equal to 0<br>\n";
	//if  ($this->zip_filter == "0") echo "zip filter is equal to quote 0<br>\n";
	//echo $this->configuration_data['use_zip_distance_calculator']." is USE_ZIP_DISTANCE_CALCULATOR<br>\n";
	if (($this->configuration_data['use_zip_distance_calculator'] == 2) || ($this->configuration_data['use_zip_distance_calculator'] == 3))
	{
		if ((strlen(trim($this->zip_filter)) == 0) || ($this->zip_filter == "0"))
			$local_zip_filter = urldecode($this->messages[2312]);
		else
			$local_zip_filter = $this->zip_filter;
	}
	else
	{
		if ((strlen(trim($this->zip_filter)) == 0) || ($this->zip_filter == 0) || ($this->zip_filter == "0"))
			$local_zip_filter = urldecode($this->messages[2312]);
		else
			$local_zip_filter = $this->zip_filter;
	}
	//echo $local_zip_filter." is local_zip_filter in module<BR>\n";
	$this->body = "<table cellpadding=3 cellspacing=1 border=0 width=100%>";
	$this->body .= "<form name=zip_filter_form_1 action=".$this->configuration_data['classifieds_file_name']."?".$_SERVER["QUERY_STRING"]." method=post>\n";	

	$this->body .= "<tr><td class=zip_filter_text>".urldecode($this->messages[2305]);
	if (($this->configuration_data['use_zip_distance_calculator'] == 2) || ($this->configuration_data['use_zip_distance_calculator'] == 3))
		$this->body .= "<input class=zip_filter_input_box type=text size=8 maxlength=8 value=\"".$local_zip_filter."\" onFocus=\"if(this.value=='".$local_zip_filter."')this.value='';\" name=set_zip_filter>\n\t\t";
	else
		$this->body .= "<input class=zip_filter_input_box type=text size=5 maxlength=5 value=\"".$local_zip_filter."\" onFocus=\"if(this.value=='".$local_zip_filter."')this.value='';\" name=set_zip_filter>\n\t\t";
	$this->body .= "\n\t</td>";

	$distance_array = array(5,10,15,20,25,30,40,50,75,100,200,300,400,500);
	$this->body .= "<td><select class=zip_filter_dropdown name=set_zip_filter_distance>\n\t\t";
	$this->body .= "<option value=\"\">".urldecode($this->messages[2306])."</option>\n\t\t";
	reset($distance_array);
	foreach ($distance_array as $distance)
	{
		if ($this->zip_filter_distance == $distance)
			$this->body .= "<option selected value=\"".$distance."\">".$distance." ".urldecode($this->messages[2307])."</option>\n\t\t";
		else
			$this->body .= "<option value=\"".$distance."\">".$distance." ".urldecode($this->messages[2307])."</option>\n\t\t";
	}
	$this->body .= "</select>\n\t</td>";
	$this->body .= "<td>";
	$this->body .= "<input type=submit name=submit_zip_filter value=\"".urldecode($this->messages[2308])."\" class=zip_filter_buttons >";
	$this->body .= "<input type=submit name=clear_zip_filter value=\"".urldecode($this->messages[2309])."\" class=zip_filter_buttons >";
	$this->body .= "\n\t</td></tr>";
	$this->body .= "</form></table>\n";
//}
?>