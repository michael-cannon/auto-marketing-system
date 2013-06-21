<? //module_display_state_filters.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

//if ($this->configuration_data['use_state_filters'])
//{
	$this->sql_query = "select * from ".$this->states_table." order by display_order, name";
	$state_result =  &$db->Execute($this->sql_query);
	if (!$state_result)
	{
		return false;
	}
	$this->get_css($db,$show_module['page_id']);
	$this->get_text($db,$show_module['page_id']);

	//setup dropdown labels
	//echo $this->state_filter." is state filter in state module<bR>\n";
	$this->body = "";
	$this->body .= "<form name=state_filter_form_1 action=".$this->configuration_data['classifieds_file_name']."?".$_SERVER["QUERY_STRING"]." method=post>";
	$this->body .= "<table cellpadding=0 cellspacing=0 border=0 width=100% class=state_filter_text>";
	$this->body .= "<tr><td height=15>".urldecode($this->messages[2311]);
	$this->body .= "<select class=state_filter_dropdown name=set_state_filter onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\">";
	$this->body .= "<option ";
	if (($this->state_filter == 0) || ($this->state_filter == "0") || ($this->state_filter == ""))
		$this->body .= " selected ";
	$this->body .= " value=\"clear state\">".urldecode($this->messages[2310])."</option>";
	if (strlen(trim($this->state_filter)) > 0)
		$this->body .= "<option value=\"clear state\">".urldecode($this->messages[2304])."</option>";
	while ($show_state = $state_result->FetchRow())
	{
		if (($this->state_filter == $show_state["abbreviation"]) && (strlen(trim($this->state_filter)) > 0))
			$this->body .= "<option selected value=\"".$show_state["abbreviation"]."\">".$show_state["name"]."</option>";
		else
			$this->body .= "<option value=\"".$show_state["abbreviation"]."\">".$show_state["name"]."</option>";
	}
	if (strlen(trim($this->state_filter)) > 0)
		$this->body .= "<option value=\"clear state\">".urldecode($this->messages[2310])."</option>";
	$this->body .= "</select>";
	$this->body .= "</td></tr>";
	$this->body .= "</table>";
	$this->body .= "</form>";
//}
?>