<? //search_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
\**************************************************************************/

class Search_classifieds extends Site
{
	var $category_name;
	var $criteria;
	var $search_criteria;
	var $started;
	var $where_clause;
	var $search_sql_query;
	var $search_page_results;
	var $optional_fields;
	var $browse_type;
	var $original_search_term;

	var $debug=0;
	var $debug_search=0;
	var $debug_display_results = 0;
	var $testing=0;
	var $test_name="";
	var $canadian_zip = 0;
	var $total_returned = 0;
	var $search_text;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Search_classifieds ($db,$language_id,$auth,$category_id=0,$filter_id=0,$state_filter=0,$zip_filter=0,$zip_filter_distance=0,$product_configuration=0)
	{
		$this->Site($db,8,$language_id,$auth,$product_configuration);
		$this->site_category = $category_id;

		$this->filter_id = $filter_id;
		$this->state_filter = $state_filter;
		$this->zip_filter = $zip_filter;
		$this->zip_filter_distance = $zip_filter_distance;

	} //end of function Search

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function search_form ($db,$search=0)
	{
		$this->page_id = 44;
		$this->get_text($db);
		$this->category_name = $this->get_category_name($db,$this->site_category);
		$this->get_ad_configuration($db);
		$this->get_category_configuration($db,$this->site_category,0);

		$this->CountOptionalFields($db);

		if (!$this->configuration_data['use_search_form'])
		{
			if (!$this->category_configuration->USE_SITE_DEFAULT)
			{
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
				$this->field_configuration_data->USE_OPTIONAL_FIELD_21 = $this->configuration_data['use_optional_field_21'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_22 = $this->configuration_data['use_optional_field_22'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_23 = $this->configuration_data['use_optional_field_23'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_24 = $this->configuration_data['use_optional_field_24'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_25 = $this->configuration_data['use_optional_field_25'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_26 = $this->configuration_data['use_optional_field_26'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_27 = $this->configuration_data['use_optional_field_27'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_28 = $this->configuration_data['use_optional_field_28'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_29 = $this->configuration_data['use_optional_field_29'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_30 = $this->configuration_data['use_optional_field_30'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_31 = $this->configuration_data['use_optional_field_31'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_32 = $this->configuration_data['use_optional_field_32'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_33 = $this->configuration_data['use_optional_field_33'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_34 = $this->configuration_data['use_optional_field_34'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_35 = $this->configuration_data['use_optional_field_35'];
			}
			else
			{
				$this->field_configuration_data = $this->category_configuration;
			}

			$this->body .="<form name='site_search_form' action=";
			if ($affiliate_id)
				$this->body .= $this->configuration_data['affiliate_url']."?a=19";
			else
				$this->body .= $this->configuration_data['classifieds_url']."?a=19";
			$this->body .=" method=post>\n";
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
			$this->body .="<tr class=search_page_title>\n\t<td colspan=2>\n\t".urldecode($this->messages[571])."\n\t</td>\n</tr>\n";
			$this->body .="<tr class=search_page_instructions>\n\t<td colspan=2>\n\t".urldecode($this->messages[572])."\n\t</td>\n</tr>\n";
			$this->body .= "<tr><td colspan=3><hr width=70%></td></tr>";
			if ((strlen(trim($this->search_sql_query)) > 0) && (!$this->started))
				$this->body .="<tr class=search_criteria_error_message>\n\t<td colspan=2 align=center>\n\t".urldecode($this->messages[589])."\n\t</td>\n</tr>\n";

			//get category dropdown and checkbox
			$this->withAjax=false;
			$this->body .="
				<tr>
					<td class=search_field_section_labels style='width:40%;'>".urldecode($this->messages[1442])."</td>
					<input type='hidden' value='0' name='change'>
					<td class=search_field_labels>";
			if ($this->site_category)
			{
				if ($this->category_name)
					$this->get_category_dropdown($db,"\"c\" onChange='sendReq(\"displaySearchQuestions\", this.value);'",$this->site_category, 0,"search_data_values");
					//$this->body .= $this->category_name->CATEGORY_NAME." and<input type=hidden name=c value=".$this->site_category." class=search_data_values>";
				else
					$this->get_category_dropdown($db,"\"c\" onChange='sendReq(\"displaySearchQuestions\", this.value);'",0, 0,"search_data_values");
			}
			else
				$this->get_category_dropdown($db,"\"c\" onChange='sendReq(\"displaySearchQuestions\", this.value);'",0,0, "search_data_values");
			$this->body .=" ".urldecode($this->messages[579])."<input type=checkbox name=b[subcategories_also] value=1 checked class=search_field_labels>".$this->display_help_link(585)."</td>\n</tr>\n";
			$this->body .= "<tr><td colspan=3><hr width=70%></td></tr>";
			$this->body .="<tr>\n\t<td class=search_field_section_labels>\n\t".urldecode($this->messages[573])."</td>\n\t";
			$this->body .="<td width=75% class=search_data_values>\n\t
				<input type=text name=b[search_text] size=30 maxsize=50  class=search_data_values>".$this->display_help_link(574)
				."</td>\n</tr>\n";

			$this->body .="<tr>\n\t<td class=search_field_section_labels>&nbsp;</td>\n\t
				<td class=search_field_labels>\n\t <input type=radio name=b[whole_word] value=1  class=search_data_values>".urldecode($this->messages[580])."<br>
				<input type=radio name=b[whole_word] value=0 ";
			// Special case for joe edwards
			include('config.php');
			if($joe_edwards)
				$this->body .= "";
			else
				$this->body .= " checked ";
			$this->body .= "class=search_data_values>".urldecode($this->messages[581])."<br>";
			$this->body .= "<input type=radio name=b[whole_word] value=2 ";
			if($joe_edwards)
				$this->body .= "checked";
			$this->body .= " class=search_data_values>".urldecode($this->messages[1437])."\n\t</td>\n</tr>\n";

			if ($this->is_class_auctions())
			{
				$this->body .= "<tr><td colspan=3><hr width=70%></td></tr>";
				//classauctions switch here
				$this->body .= "<tr><td class=search_field_section_labels>&nbsp;</td>\n\t
					<td class=search_field_labels>\n\t<input type=radio name=b[classified_auction_search] value=0 class=search_data_values>".urldecode($this->messages[200021])." <bR>
					<input type=radio name=b[classified_auction_search] value=1 class=search_data_values>".urldecode($this->messages[200023])." <bR>
					<input type=radio name=b[classified_auction_search] value=2 class=search_data_values>".urldecode($this->messages[200022])." </td></tr>";
			}
			$this->body .= "<tr><td colspan=3><hr width=70%></td></tr>";

			if ($this->field_configuration_data->USE_PRICE_FIELD)
			{
				//display price limits to search for
				$this->body .="<tr>\n\t<td class=search_field_section_labels>\n\t".urldecode($this->messages[788])."\n\t</td>\n\t";
				$this->body .="<td class=search_data_values>\n\t
					<table cellpadding=1 >
					<tr class=range_labels><td class=search_data_values>".urldecode($this->messages[1440])."</td><td class=search_data_values><input name=b[by_price_lower] size=15 maxsize=15 class=search_data_values>\n\t\t</td></tr>
					<tr class=range_labels><td class=search_data_values>".urldecode($this->messages[1441])."</td><td class=search_data_values><input name=b[by_price_higher] size=15 maxsize=15 class=search_data_values>\n\t\t</td></tr></table>";
				$this->body .="</td>\n</tr>\n";
			}

			$this->sql_query = "SELECT use_registration_business_type_field FROM ".$this->registration_configuration_table;
			//echo $this->sql_query." is the query<bR>\n";
			$result = $db->Execute($this->sql_query) or die();
			$registration_configuration = $result->FetchNextObject();

			if ($registration_configuration->USE_REGISTRATION_BUSINESS_TYPE_FIELD)
			{
				$this->body .="<tr>\n\t<td  class=search_field_section_labels>\n\t".urldecode($this->messages[1439])."\n\t</td>\n\t
					<td class=search_data_values>\n\t<input type=radio name=b[by_business_type] value=1 class=search_data_values> ".urldecode($this->messages[790])."<br>
					<input type=radio name=b[by_business_type] value=2 class=search_data_values> ".urldecode($this->messages[789])."<br>
					<input type=radio name=b[by_business_type] value=0 checked class=search_data_values> ".urldecode($this->messages[791])."\n\t</td>\n</tr>\n";
			}
			if ($this->field_configuration_data->USE_STATE_FIELD)
			{
				$this->sql_query = "select distinct(location_state) from ".$this->classifieds_table." where live = 1 and location_state != \"\" order by location_state";
				//$this->body .=$this->sql_query."<br>\n";
				$state_result = $db->Execute($this->sql_query);
				if (!$state_result)
				{
					return false;
				}
				elseif ($state_result->RecordCount() > 0)
				{
					$this->body .="<tr>\n\t<td class=search_field_section_labels>\n\t".urldecode($this->messages[582])."\n\t</td>\n\t";
					$this->body .="<td class=search_data_values>\n\t"."<select name=b[by_state] class=search_data_values>";
					$this->body .="<option value=0></option>\n\t\t";
					while ($show_state = $state_result->FetchNextObject())
					{
						$this->body .="<option value=\"".$show_state->LOCATION_STATE."\" ";
						if (($this->state_filter) && ($this->state_filter == $show_state->LOCATION_STATE))
							$this->body .= "selected";
						$this->body .= ">".urldecode($this->get_state_name($db,$show_state->LOCATION_STATE))."</option>\n\t\t";
					}
					$this->body .="</select>".$this->display_help_link(586)."</td>\n</tr>\n";
				}
			}
			/*Begin Commenting out
			if (($this->field_configuration_data->USE_ZIP_FIELD) && (!$this->configuration_data['use_zip_distance_calculator']))
			{
				$this->sql_query = "select distinct(location_zip) from ".$this->classifieds_table." where live = 1 and location_zip != \"\" order by location_zip";
				//$this->body .=$this->sql_query."<br>\n";
				$zip_result = $db->Execute($this->sql_query);
				if (!$zip_result)
				{
					return false;
				}
				elseif ($zip_result->RecordCount() > 0)
				{
					$this->body .="<tr>\n\t<td class=search_field_section_labels>\n\t".urldecode($this->messages[577])."\n\t</td>\n\t";
					$this->body .="<td class=search_data_values>\n\t"."<select name=b[by_zip] class=search_data_values>";
					$this->body .="<option value=0></option>\n\t\t";
					while ($show_zip = $zip_result->FetchNextObject())
					{
						$this->body .="<option value=\"".$show_zip->LOCATION_ZIP."\" ";
						if (($this->zip_filter) && ($this->zip_filter == $show_zip->LOCATION_ZIP))
							$this->body .= "selected";
						$this->body .= ">".urldecode($show_zip->LOCATION_ZIP)."</option>\n\t\t";
					}
					$this->body .="</select>".$this->display_help_link(588)."</td>\n</tr>\n";
				}
			}
			End Commenting out*/

			if ($this->configuration_data['use_zip_distance_calculator'])
			{
				$this->body .= "
					<tr>
						<td class=search_field_section_labels>".urldecode($this->messages[1949])."</td>
						<td class=search_data_values><input type=text name=b[by_zip_code] class=search_data_values ";
				if ($this->configuration_data['use_zip_distance_calculator'] == 2 || $this->configuration_data['use_zip_distance_calculator'] == 3)
				{
					if ((strlen(trim($this->zip_filter)) != 0) && ($this->zip_filter != "0"))
						$this->body .= "value='".$this->zip_filter."'";
				}
				else
				{
					if (strlen(trim($this->zip_filter)) != 0 && $this->zip_filter != 0 && $this->zip_filter != "0")
						$this->body .= "value=\"".$this->zip_filter."\"";
				}
				$this->body .= ">
							<select name=b[by_zip_code_distance] class=search_data_values>
								<option value=0>".urldecode($this->messages[1950])."</option>
								<option value=5 ";
				if ($this->zip_filter_distance && $this->zip_filter_distance == 5) $this->body .= "selected";
				$this->body .= ">5</option>";
				$this->body .= "<option value=10 ";
				if ($this->zip_filter_distance && $this->zip_filter_distance == 10) $this->body .= "selected";
				$this->body .= ">10</option>";
				$this->body .= "<option value=15 ";
				if ($this->zip_filter_distance && $this->zip_filter_distance == 15) $this->body .= "selected";
				$this->body .= ">15</option>";
				$this->body .= "<option value=20 ";
				if ($this->zip_filter_distance && $this->zip_filter_distance == 20) $this->body .= "selected";
				$this->body .= ">20</option>";
				$this->body .= "<option value=25 ";
				if ($this->zip_filter_distance && $this->zip_filter_distance == 25) $this->body .= "selected";
				$this->body .= ">25</option>";
				$this->body .= "<option value=30 ";
				if ($this->zip_filter_distance && $this->zip_filter_distance == 30) $this->body .= "selected";
				$this->body .= ">30</option>";
				$this->body .= "<option value=40 ";
				if ($this->zip_filter_distance && $this->zip_filter_distance == 40) $this->body .= "selected";
				$this->body .= ">40</option>";
				$this->body .= "<option value=50 ";
				if ($this->zip_filter_distance && $this->zip_filter_distance == 50) $this->body .= "selected";
				$this->body .= ">50</option>";
				$this->body .= "<option value=75 ";
				if ($this->zip_filter_distance && $this->zip_filter_distance == 75) $this->body .= "selected";
				$this->body .= ">75</option>";
				$this->body .= "<option value=100 ";
				if ($this->zip_filter_distance && $this->zip_filter_distance == 100) $this->body .= "selected";
				$this->body .= ">100</option>";
				$this->body .= "<option value=200 ";
				if ($this->zip_filter_distance && $this->zip_filter_distance == 200) $this->body .= "selected";
				$this->body .= ">200</option>";
				$this->body .= "<option value=300 ";
				if ($this->zip_filter_distance && $this->zip_filter_distance == 300) $this->body .= "selected";
				$this->body .= ">300</option>";
				$this->body .= "<option value=400 ";
				if ($this->zip_filter_distance && $this->zip_filter_distance == 400) $this->body .= "selected";
				$this->body .= ">400</option>";
				$this->body .= "<option value=500 ";
				if ($this->zip_filter_distance && $this->zip_filter_distance == 500) $this->body .= "selected";
				$this->body .= ">500</option>";
				$this->body .= "</select>";
				$this->body .= $this->display_help_link(1951)."</td>\n</tr>\n";
			}
			$this->body .= "<tr><td colspan=3><hr width=70%></td></tr>";
			/*Begin Commenting out
			if ($this->ad_configuration_data->USE_COUNTRY_FIELD)
			{
				$this->sql_query = "select distinct(location_country) from ".$this->classifieds_table." where location_country != \"\"";
				$country_result = $db->Execute($this->sql_query);
				if (!$country_result)
				{
					return false;
				}
				elseif ($country_result->RecordCount() > 0)
				{
					//get unique countries
					$this->body .="<tr>\n\t<td class=search_field_section_labels>\n\t".urldecode($this->messages[578])."\n\t</td>\n\t";
					$this->body .="<td>\n\t<select name=b[by_country]>\n\t\t";
					$this->body .="<option value=0></option>\n\t\t";
					while ($show_country = $country_result->FetchNextObject())
					{
						if (strlen(trim($show_country->LOCATION_COUNTRY)) > 0)
							$this->body .="<option>".$show_country->LOCATION_COUNTRY."</option>\n\t\t";
					}
					$this->body .="</select>".$this->display_help_link(587)."</td>\n</tr>\n";
				}

			}
			End Commenting out*/

				$this->body .= "
					<tr><td colspan='2' align='center' id='catQuestions'>&nbsp;";
				$this->body .= "
						<table cellpadding=2 cellspacing=1 border=0 width=100%>";
				// Display all optional fields
				if(!$this->optional_fields)
					$this->CountOptionalFields($db);
				$field_vars = get_object_vars($this->field_configuration_data);
				$ad_vars = get_object_vars($this->ad_configuration_data);

				for($i = 1; $i < $this->optional_fields+1; $i++)
				{
					// the following is impossible to make sense of
					//filter display use may change in the future
					if (($field_vars['USE_OPTIONAL_FIELD_'.$i]) && (!$this->configuration_data["optional_".$i."_filter_association"]))
					{
						// Special case because optional field 1 has a different number than rest of scheme
						if($i == 1)
							$this->body .="<tr>\n\t<td class=search_field_section_labels style='width:40%;'>".urldecode($this->messages[1457])."</td>\n\t";
						elseif($i <= 10)
							$this->body .="<tr>\n\t<td class=search_field_section_labels style='width:40%;'>".urldecode($this->messages[(1458+($i-1))])."</td>\n\t";
						elseif($i <= 20)
							$this->body .="<tr>\n\t<td class=search_field_section_labels style='width:40%;'>".urldecode($this->messages[(1933+($i-11))])."</td>\n\t";
						elseif($i <= 35)
							$this->body .="<tr>\n\t<td class=search_field_section_labels style='width:40%;'>".urldecode($this->messages[(2778+($i-21))])."</td>\n\t";
						$this->body .="<td class=search_data_values>\n\t";

						if ($this->configuration_data["optional_".$i."_filter_association"])
						{
							$this->body .= "&nbsp;";
						}
						elseif ($ad_vars["OPTIONAL_".$i."_NUMBER_ONLY"])
						{
							//if numbers only - produce a upper and lower limit
							$this->body .= "<table cellpadding=1 >
								<tr class=range_labels><td class=search_data_values>".urldecode($this->messages[1440])."</td><td class=search_data_values><input name=b[by_optional_".$i."_lower] size=15 maxsize=15 class=search_data_values>\n\t\t</td></tr>
								<tr class=range_labels><td class=search_data_values>".urldecode($this->messages[1441])."</td><td class=search_data_values><input name=b[by_optional_".$i."_higher] size=15 maxsize=15 class=search_data_values>\n\t\t</td></tr></table>";
						}
						elseif (!($ad_vars["OPTIONAL_".$i."_FIELD_TYPE"]))
						{
							//check to see if numbers only
							if ($ad_vars["OPTIONAL_".$i."_NUMBER_ONLY"])
							{
								//if numbers only - produce a upper and lower limit
								$this->body .= "<table cellpadding=1 >
								<tr class=range_labels><td class=search_data_values>".urldecode($this->messages[1440])."</td><td class=search_data_values><input name=b[by_optional_".$i."_lower] size=15 maxsize=15 class=search_data_values>\n\t\t</td></tr>
								<tr class=range_labels><td class=search_data_values>".urldecode($this->messages[1441])."</td><td class=search_data_values><input name=b[by_optional_".$i."_higher] size=15 maxsize=15 class=search_data_values>\n\t\t</td></tr></table>";
							}
							else
								$this->body .= "<input type=text name=b[optional_field_".$i."] id=optional_field_".$i." class=search_data_values>\n\t";
						}
						else
						{
							$this->sql_query = "select * from ".$this->sell_choices_table." where type_id = ".$ad_vars["OPTIONAL_".$i."_FIELD_TYPE"]." order by display_order,value";
							$type_result = $db->Execute($this->sql_query);
							if (!$type_result)
							{
								if ($this->debug_search)
								{
									echo $db->ErrorMsg()."<br>";
									echo $this->sql_query."<bR>\n";
								}
							}
							if ($type_result->RecordCount() > 0)
							{
								$this->body .= "<select name=b[optional_field_".$i."] id=optional_field_".$i." class=search_data_values>";
								$this->body .= "<option value=0></option>";
								$matched = 0;
								while ($show_dropdown = $type_result->FetchNextObject())
								{
									$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
									if ($this->classified_variables["optional_field_".$i] == $show_dropdown->VALUE)
									{
										$this->body .= "selected";
										$matched = 1;
									}
									$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
								}
								$this->body .= "</select>";
							}
							else
							//blank text box
							$this->body .= "<input type=text name=b[optional_field_".$i."] id=optional_field_".$i." class=search_data_values>\n\t";
						}
						if (($ad_vars["OPTIONAL_".$i."_OTHER_BOX"]) && ($ad_vars["OPTIONAL_".$i."_FIELD_TYPE"]) && (!$this->configuration_data["optional_".$i."_filter_association"]))
							$this->body .= " ".urldecode($this->messages[1458])." <input type=text name=b[optional_field_".$i."_other] class=search_data_values>\n\t";
						$this->body .="</td>\n</tr>\n";
					}
				}
				$this->body .= "</table>";
				$this->body .= "</td></tr>";
			/*if ($this->site_category)
			{
				//get category questions if there are any
				$this->get_category_questions($db,$this->site_category);

				$this->display_search_questions($db);
				if (strlen(trim($this->criteria)) > 0)
				{
					$this->body .= "<tr class=search_page_instructions >\n\t<td colspan=2>".urldecode($this->messages[1614]).$this->category_name->CATEGORY_NAME."</td>\n</tr>\n";
					$this->body .= $this->criteria;
				}
			}
			else
			{
				//this is the main category

			}*/
			$this->body .="<tr class=search_button>\n\t<td colspan=2 align=center>\n\t<input type=submit value=\"".urldecode($this->messages[584])."\" name=b[search] class=search_button>\n\t</td>\n</tr>\n";
			//$this->body .="<tr>\n\t<td colspan=2 class=start_new_search_link>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=19 class=start_new_search_link>".urldecode($this->messages[591])."</a>\n\t</td>\n</tr>\n";
			$this->body .="</table>\n";
			$this->body .="</form>\n";
		}
		$this->display_page($db);
		return true;

	} //end of function search_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
/*
	function display_search_questions($db)
	{
		if ($this->debug_search)
		{
			echo "<BR>TOP OF DISPLAY_SEARCH_QUESTIONS<Br>\n";
			echo count($this->category_questions)." is the count of this->category_questions<br>\n";
		}
		//$this->body .=count($this->category_questions)." is the count of category questions<br>\n";
		$criteria = 0;
		$to_display = 0;
		if (count($this->category_questions) > 0)
		{
			//$category_questions = array_reverse($category_questions);  //puts question in order of general to specific
			reset($this->category_questions);
			foreach ($this->category_questions as $key => $value)
			{
				//spit out the questions
				if ($this->debug_search)
				{
					echo $this->category_choices[$key]." is category choices ".$key." <br>\n\t";
				}
				//get the list of choices for this question
				if ($this->category_choices[$key] == "check")
				{
					$this->criteria .= "<tr>\n\t<td class=search_field_section_labels>".$this->category_questions[$key]."\n\t</td>\n\t";
					$this->criteria .= "<td class=search_data_values>\n\t";
					//this is a blank field
					$this->criteria .= "<input class=search_data_values type=checkbox name=b[question_value][".$key."] value=\"".$this->category_questions[$key]."\">\n\t";
					$to_display++;
				}
				elseif ($this->category_choices[$key] != "none")
				{
					$this->sql_query = "SELECT * FROM ".$this->sell_choices_table." WHERE type_id = \"".$this->category_choices[$key]."\" ORDER BY display_order,value";
					if ($this->debug_search) echo $this->sql_query."<br>\n";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						return false;
					}
					elseif ($result->RecordCount() > 0)
					{
						$this->criteria .= "<tr>\n\t<td class=search_field_section_labels>".$this->category_questions[$key]."\n\t</td>\n\t";
						$this->criteria .= "<td class=search_data_values>\n\t";
						$this->criteria .= "<select name=b[question_value][".$key."] class=search_data_values>\n\t\t";
						$this->criteria .= "<option></option>\n\t\t";
						while ($show_choices = $result->FetchNextObject())
						{
							//put choices in options of this select statement
							$this->criteria .= "<option ";
							if ($this->category_values[$key] == $show_choices->VALUE)
								$this->criteria .= "selected";
							$this->criteria .= ">".$show_choices->VALUE."</option>\n\t\t";
						}
						$this->criteria .= "</select>\n\t";
						$to_display++;
					}
				} //end of if $category_questions[$i]["choices"] != "none"
//					Taken out because people just wanted to use top box
//				else
//				{
//					$this->criteria .= "<tr>\n\t<td class=search_field_section_labels>".$this->category_questions[$key]."\n\t</td>\n\t";
//					$this->criteria .= "<td class=search_data_values><input type=text name=b[question_value][".$key."] class=search_data_values></td>\n\t</tr>\n\t";
//				}



				//$this->body .= "<a href=\"message.php\" onmouseover=\"window.status='explanation to ".$category_questions[$key]."';  return true;\" onmouseout=\"window.status=''; return true;\" onClick='enterWindow=window.open(\"message.php?msg=".urlencode($category_explanation[$key])."&msgheader=Explanation\",\"Explanation to ".$category_questions[$key]."\",\"width=300,height=150,top=50,left=100,resizeable=no\"); return false'>explanation</a>";
				$this->criteria .= "</td>\n</tr>\n";

			} // end of while
		} //end of if (count($category_questions) > 0)
		if ($to_display == 0)
			$this->criteria = "";
		return;
	} //end of function display_search_questions

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
*/
	function Search($db, $search_criteria = 0, $change = 0, $browse_type=0)
	{
		if ($this->debug_search)
		{
			echo "<br>TOP OF SEARCH<bR>\n";
			echo $search_criteria." is search_criteria<BR>\n";
			echo $search_criteria["whole_word"]." is search_criteria[whole_word]<Br>\n";
			echo $search_criteria["search_text"]." is the search text<BR>\n";
			echo count($search_criteria["question_value"])." is the count of search_criteria[question_value]<bR>\n";

		}
		$this->page_id = 44;
		$this->get_text($db);

		$this->search_text = $search_criteria["search_text"];

		// Check if user changed category
		if($change == 1)
		{
			if ($this->debug_search) echo "category changed ...displaying form<BR>\n";
			return false;
		}

		if($search_criteria)
		{
			if ($this->debug_search)
			{
				echo "inside of if (search_criteria)<BR>\n";
				echo $this->search_criteria["page"]." is the b[page] value<BR>\n";
			}
			$this->original_search_term = $search_criteria["search_text"];
			$this->search_criteria = $search_criteria;
			$this->get_ad_configuration($db);
			$this->site_category = $_REQUEST['c'];
			if (strlen(trim($this->site_category)) == 0)
				$this->site_category = 0;
			if ($this->debug_search) echo "this->site_category set to ".$this->site_category."<bR>\n";

			$this->search_text = urlencode(trim($this->search_criteria["search_text"]));
			$this->search_criteria["search_text"] = urlencode(trim($this->search_criteria["search_text"]));

			if ($this->debug_search)
			{
				echo $this->search_criteria["search_text"]." is search_criteria[search_text]<br>\n";
				echo $this->search_text." is search_text<br>\n";
			}

			$this->get_category_configuration($db,$this->site_category,0);
			$this->get_category_questions($db, $this->site_category);

			if (!$this->category_configuration->USE_SITE_DEFAULT)
			{
				if ($this->debug_search) echo "using site settings<br>\n";
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
				$this->field_configuration_data->USE_OPTIONAL_FIELD_21 = $this->configuration_data['use_optional_field_21'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_22 = $this->configuration_data['use_optional_field_22'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_23 = $this->configuration_data['use_optional_field_23'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_24 = $this->configuration_data['use_optional_field_24'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_25 = $this->configuration_data['use_optional_field_25'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_26 = $this->configuration_data['use_optional_field_26'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_27 = $this->configuration_data['use_optional_field_27'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_28 = $this->configuration_data['use_optional_field_28'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_29 = $this->configuration_data['use_optional_field_29'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_30 = $this->configuration_data['use_optional_field_30'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_31 = $this->configuration_data['use_optional_field_31'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_32 = $this->configuration_data['use_optional_field_32'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_33 = $this->configuration_data['use_optional_field_33'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_34 = $this->configuration_data['use_optional_field_34'];
				$this->field_configuration_data->USE_OPTIONAL_FIELD_35 = $this->configuration_data['use_optional_field_35'];
			}
			else
			{
				if ($this->debug_search) echo "using category settings<br>\n";
				$this->field_configuration_data = $this->category_configuration;
			}

			// Start out query
			$this->sql_query = "SELECT * FROM ".$this->classifieds_table." WHERE ";

			// Search ID only and exit if only searching for ad id
			if($this->search_criteria["whole_word"] == 2)
			{
				if($this->debug_search)
				{
					echo "searching as if search term were an ad id<BR>\n";
					echo "Searching for id ".$this->search_criteria["search_text"]."<br>";
				}
				$this->where_clause = "id = ".$this->search_criteria["search_text"]." and live = 1";

				// Build the query to run
				$this->sql_query .= $this->where_clause;

				if($this->testing && $this->test_name == "TestIDSearch")
				{
					// TestIDSearch()
					return $this->sql_query;
				}
				// Get actual results
				$result = $db->Execute($this->sql_query);
				if(!$result)
				{
					if($this->debug_search)
					{
						echo $db->ErrorMsg()." is the error<br>\n";
						echo $this->sql_query.'<br>';
					}
					return  false;
				}
				else
				{
					// Send user to correct ad
					if($result->RecordCount() == 1)
					{
						if($this->testing == 1)
						{
							return $result;
						}

						// Send user to correct ad
						$returned_result = $result->FetchNextObject();
						if ($this->debug_search) echo "redirecting to id ".$returned_result->ID." in the id search<BR>\n";
						header("Location: ".$this->configuration_data['classifieds_url']."?a=2&b=".$returned_result->ID);
						exit;
					}
					else
					{
						// No results returned
						if ($this->debug_search) echo "no id results from search of id ".$returned_result->ID." in the id search<BR>\n";
						$this->body .="<tr class=search_page_instructions>\n\t<td colspan=4>\n\t".urldecode($this->messages[592])."\n\t</td>\n</tr>\n";
						return false;
					}

					return true;
				}
			}

			// Generate whole or partial word match
			if(strlen(trim($this->search_criteria["search_text"])) > 0)
			{
				if ($this->debug_search)
				{
					echo "<br>TOP OF SEARCH_TEXT > 0<br>\n";
					echo " search_text contained text<BR>\n";
					echo "about to search for this search_text - ".$this->search_criteria["search_text"]."<bR>\n";
				}
				// Notes:
				// 0 is partial
				// 1 is whole
				/*
				if($this->search_criteria["whole_word"] == 0)
				{
					// Partial word match
					$this->search_criteria["search_text"] = "%".$this->search_criteria["search_text"]."%";
				}
				elseif ($this->search_criteria["whole_word"] == 1)
				{
					$this->search_criteria["search_text"] = "%+".$this->search_criteria["search_text"]."+%";
				}
				elseif($this->search_criteria["whole_word"] == 2)
				{
					// Shouldnt get here so error out
					// if whole_word is 2 then it should have been caught above
					if ($this->debug_search) echo "whole word did not match<br>\n";
					return false;
				}

				if($this->testing && ($this->test_name == "TestWholeWordMatch" || $this->test_name == "TestPartialWordMatch"))
				{
					// Test*WordMatch()
					return $this->search_criteria["search_text"];
				}
				if($this->debug_search)
				{
					echo $this->search_criteria["search_text"]." is the search text to be placed in search queries<Br>";
				}
				*/

				// Search Title
				if(strlen($this->where_clause) > 0)
				{
					$this->where_clause .= " OR ";
					if ($this->debug_search) echo "added OR because where clause already populated - ".$this->where_clause."<BR>\n";
				}

				//check to see if search term can be broken into pieces
				// check for ',' - ' or ' - ' and '
				//comma is %2C
				if (stristr($this->search_criteria["search_text"],"%2C"))
				{
					$and_or_condition = 0;
					$all_search_terms = explode("%2C",$this->search_criteria["search_text"]);
				}
				elseif (stristr($this->search_criteria["search_text"],"+or+"))
				{
					$and_or_condition = 0;
					$all_search_terms = explode("+or+",$this->search_criteria["search_text"]);
				}
				elseif (stristr($this->search_criteria["search_text"],"+and+"))
				{
					$and_or_condition = 1;
					$all_search_terms = explode("+and+",$this->search_criteria["search_text"]);
				}
				else
				{
					$and_or_condition = 0;
					$all_search_terms = array();
					$all_search_terms[0] = $this->search_criteria["search_text"];
				}


				if ($this->debug_search)
				{
					echo $and_or_condition." is the and_or_condition<br>ALL KEY TERMS TO SEARCH<br>\n";
					foreach ($all_search_terms as $key => $value)
					{
						echo $value."<br>\n";
					}
					echo "END OF SEARCH TERMS<Br>\n";
				}

				$this->where_clause .= " (";

				if($this->search_criteria["whole_word"] == 1)
				{
					if ($this->debug_search) echo "searching whole word = 1<bR>\n";
					// word only
					reset($all_search_terms);
					$started_through_all_search_terms = 0;
					$this->where_clause .= "(";
					foreach ($all_search_terms as $key => $value)
					{
						if ($started_through_all_search_terms)
						{
							//add the or,and
							if ($and_or_condition)
							{
								$this->where_clause .= " AND ";
							}
							else
							{
								$this->where_clause .= " OR ";
							}
						}
						$this->where_clause .= "((title like \"".$value."\")";
						$this->where_clause .= " OR ";

						// Word at beginning of phrase
						$this->where_clause .= "(title like \"".$value."+%\")";
						$this->where_clause .= " OR ";

						// Word at end of phrase
						$this->where_clause .= "(title like \"%".$value."\")";
						$this->where_clause .= " OR ";

						// Word in of phrase
						$this->where_clause .= "(title like \"%".$value."%\")";
						$this->where_clause .= " OR ";

						$this->where_clause .= "(description like \"".$value."\")";
						$this->where_clause .= " OR ";

						// Word at beginning of phrase
						$this->where_clause .= "(description like \"".$value."+%\")";
						$this->where_clause .= " OR ";

						// Word at end of phrase
						$this->where_clause .= "(description like \"%".$value."\")";
						$this->where_clause .= " OR ";

						// Word in of phrase
						$this->where_clause .= "(description like \"%".$value."%\")";
						$this->where_clause .= " OR ";

						$this->where_clause .= "(search_text like \"".$value."\")";
						$this->where_clause .= " OR ";

						// Word at beginning of phrase
						$this->where_clause .= "(search_text like \"".$value."+%\")";
						$this->where_clause .= " OR ";

						// Word at end of phrase
						$this->where_clause .= "(search_text like \"%".$value."\")";
						$this->where_clause .= " OR ";

						// Word in of phrase
						$this->where_clause .= "(search_text like \"%".$value."%\"))";
						$started_through_all_search_terms = 1;
					}
					$this->where_clause .= ")";
				}
				else
				{
					if ($this->debug_search) echo "not searching whole word not equal 1<bR>\n";
					reset($all_search_terms);
					$started_through_all_search_terms = 0;
					$this->where_clause .= "(";
					foreach ($all_search_terms as $key => $value)
					{
						if ($started_through_all_search_terms)
						{
							//add the or,and
							if ($and_or_condition)
							{
								$this->where_clause .= " AND ";
							}
							else
							{
								$this->where_clause .= " OR ";
							}
						}
						$this->where_clause .= "((title like \"%".$value."%\")";
						$this->where_clause .= " OR ";
						$this->where_clause .= "(title like \"".$value."%\")";
						$this->where_clause .= " OR ";
						$this->where_clause .= "(title like \"%".$value."\")";
						$this->where_clause .= " OR ";
						$this->where_clause .= "(description like \"%".$value."%\")";
						$this->where_clause .= " OR ";
						$this->where_clause .= "(description like \"".$value."%\")";
						$this->where_clause .= " OR ";
						$this->where_clause .= "(description like \"%".$value."\")";
						$this->where_clause .= " OR ";
						$this->where_clause .= "(search_text like \"%".$value."%\")";
						$this->where_clause .= " OR ";
						$this->where_clause .= "(search_text like \"".$value."%\")";
						$this->where_clause .= " OR ";
						$this->where_clause .= "(search_text like \"%".$value."\"))";
						$started_through_all_search_terms = 1;
					}
					$this->where_clause .= ")";
				}

				if ($this->debug_search)
				{
					echo $this->where_clause." <bR>is the where clause after adding title,description and search_text clause<BR>\n";
				}

				// Find all optional fields
				if(!$this->optional_fields)
				{
					$this->CountOptionalFields($db);
				}

				if($this->optional_fields > 0)
				{
					if(strlen($this->where_clause) > 0)
					{
						$this->where_clause .= " OR ";
					}

					$this->where_clause .= "(";

					// Search all optional fields
					for($i = 1; $i <= $this->optional_fields; $i++)
					{
						// Put ORs in the right place
						if(($i != 1) && ($i != $this->optional_fields+1))
						{
							$this->where_clause .= " OR ";
						}
						reset($all_search_terms);
						$started_through_all_search_terms = 0;
						$this->where_clause .= "(";
						foreach ($all_search_terms as $key => $value)
						{
							if ($started_through_all_search_terms)
							{
								//add the or,and
								if ($and_or_condition)
								{
									$this->where_clause .= " AND ";
								}
								else
								{
									$this->where_clause .= " OR ";
								}
							}
							$this->where_clause .= "(optional_field_".$i." like \"%".$value."%\")";
							$started_through_all_search_terms = 1;
						}
						$this->where_clause .= ")";
					}

					$this->where_clause .= ")";

					if($this->testing && $this->test_name == "TestGlobalOptionalFields")
					{
						// TestGlobalOptionalFields
						return $this->sql_query.$this->where_clause;
					}
				}
				$this->where_clause .= ") ";
			}

			if (($this->search_criteria["classified_auction_search"]) && ($this->is_class_auctions()))
			{
				//default is to search both....if classified_auction_search is empty do not limit the search to a type

				if ($this->search_criteria["classified_auction_search"] == 1)
				{
					//search only classifieds
					if(strlen($this->where_clause) > 0)
					{
						$this->where_clause .= " AND ";
					}
					$this->where_clause .= "(item_type = 1)";
				}
				elseif ($this->search_criteria["classified_auction_search"] == 2)
				{
					//search only auctions
					if(strlen($this->where_clause) > 0)
					{
						$this->where_clause .= " AND ";
					}
					$this->where_clause .= "(item_type = 2)";
				}
			}

			if ($this->debug_search)
			{
				echo $this->where_clause."<BR>is where_clause after search_text > 0<br>\n";
				echo "<BR>ABOUT TO DO CATEGORY SPECIFIC<Br>\n";
				echo $this->site_category." is site_category before category specific<br>";
				reset ($search_criteria["question_value"]);
				foreach($search_criteria["question_value"] as $key => $value)
				{
					echo $key." is key to ".$value."<br>";
				}
			}

			// Search specific category-specific questions based on input values
			if($this->site_category > 0 && isset($search_criteria["question_value"]))
			{
				if ($this->debug_search) echo "going through question_value<BR>\n";
				$category_question_list = array();

				//foreach($question_value as $key => $value)
				reset ($search_criteria["question_value"]);
				foreach($search_criteria["question_value"] as $key => $value)
				{
					if (strlen(trim($value)) > 0)
					{
						$this->sql_query = "select distinct(classified_id) from ".$this->classified_extra_table." where question_id = ".$key." and value like \"".urlencode($value)."\"";
						$result = $db->Execute($this->sql_query);
						if ($this->debug_search) echo $this->sql_query." <br>\n";
						if(!$result)
						{
							if ($this->debug_search) echo $this->sql_query." <br>\n";
							return false;
						}

						//if($result->RecordCount() == 0)
						//{
						//	if ($this->debug_search) echo "nothing returned in query of ".$key." and ".$value."<BR>\n";
						//	continue;
						//}
						//else
						//{
							$single_key = $key;
							if ($this->debug_search) echo "single_key set to: ".$single_key."<bR>\n";
							$category_question_list[$key] = array();
							if ($this->debug_search) echo $result->RecordCount()." is the count of distinct ids returned<BR>\n";
							// If a value exists lets put it in the array
							if ( $result->RecordCount() > 0)
							{
								while ($show_id = $result->FetchNextObject())
								{
									array_push($category_question_list[$key], $show_id->CLASSIFIED_ID);
									if ($this->debug_search)
									{
										echo "pushing ".$show_id->CLASSIFIED_ID." onto key - ".$key."<bR>\n";
									}
								}
							}
							else
							{
								array_push($category_question_list[$key], "0");
							}
							//	$id = $result->FetchNextObject();
							//	$category_question_list[] = $id->CLASSIFIED_ID;
						//}
					}
				}
				if (count($category_question_list) > 1)
				{
					if ($this->debug_search)
					{
						reset($category_question_list);
						foreach ($category_question_list as $key => $value)
						{
							if (is_array($value))
								echo $key."- is an array<BR>\n";
							else
								echo $key." - is not an array<BR>\n";
							//echo count($value)." is the count of key - ".$key."<br>\n";

						}
						var_dump($category_question_list);
					}
					$field_items = call_user_func_array('array_intersect', $category_question_list);
					if (!$field_items)
					{
						if ($this->debug_search)
						{
							echo "there were no intersecting ad ids for category specific questions<bR>\n";
						}
						$field_items = array();
						$field_items[$single_key] = "0";
					}
				}
				elseif (count($category_question_list) == 1)
				{
					if ($this->debug_search)
					{
						echo "category_question_list has only one value - ".$single_key."<bR>\n";
					}
					$field_items = $category_question_list[$single_key];
				}
				else
				{
					//this is an and condition
					//if no results from one condition then there are no results
					//from the search
					if ($this->debug_search)
					{
						echo "pushing 0 onto optional_field[".$i."]<br>\n";
					}
					$category_question_list = array();
					array_push($category_question_list[$single_key],"0");
				}

				if ($this->debug_search)
				{
					echo "<br><br>".count($field_items)." is the count of field_items<br>\n";
					echo $single_key." is single_key<br>\n";
					var_dump($field_items);
					echo "<bR><BR>";
				}

				// Append the search category questions onto the entire query
				if(sizeof($field_items) > 0)
				{
					reset($field_items);
					if(strlen($this->where_clause) > 0)
					{
						$this->where_clause .= " AND ";
					}

					if ($this->debug_search)
					{
						echo $this->where_clause." is the where_clause before field_items build where statement<BR>\n";
					}

					$this->where_clause .= "( id in (";
					$category_specific_where_started = 0;
					foreach($field_items as $key => $value)
					{
						if ($category_specific_where_started)
							$this->where_clause .= ", ";
						$this->where_clause .= $value;
						$category_specific_where_started = 1;
					}
					$this->where_clause .= ")";
					$this->where_clause .= ")";
				}
				if($this->testing && $this->test_name == "TestGlobalCategorySpecificQuestions")
				{
					// TestGlobalCategorySpecificQuestion
					return $this->sql_query.$this->where_clause;
				}

				if ($this->debug_search)
				{
					echo $this->where_clause." is the where_clause after field_items build where statement<BR>\n";
				}

				// Append it back onto the where clause
				if($where_statement)
					$this->where_clause .= $where_statement;

				if($this->debug_search)
					echo $this->where_clause.'<Br>';

				if($this->testing && $this->test_name == "TestSpecificCategoryQuestions")
				{
					return $this->sql_query.$this->where_clause;
				}

			}

			if($this->debug_search)
			{
				echo $this->where_clause.' is the where_clause at the end of category specific<br>';
				echo "<br>ABOUT TO SEARCH SITE WIDE OPTIONAL FIELDS<BR>\n";
			}

			// Search for specific optional field data
			// Build array to hold items for optional fields
			$field_items = array();

			// Query strings for optional fields
			$opt_sql_query = "select id from ".$this->classifieds_table." where ";
			$opt_where_clause = "";

			// Find all optional fields
			if(!$this->optional_fields)
			{
				$this->CountOptionalFields($db);
			}

			if($this->optional_fields > 0)
			{
				$optional_field = array();

				for($i = 1; $i <= $this->optional_fields; $i++)
				{
					$opt_where_clause = "";

					if($this->search_criteria["by_optional_".$i."_lower"] != "")
					{
						// If no upper limit set to "infinity"
						if(!$this->search_criteria["by_optional_".$i."_higher"])
						{
							$this->search_criteria["by_optional_".$i."_higher"] = 99999999;
						}

						if(strlen($opt_where_clause) > 1)
						{
							$opt_where_clause .= " AND ";
						}

						$opt_where_clause .= "(optional_field_".$i." <= ".$this->search_criteria["by_optional_".$i."_higher"]." AND optional_field_".$i." >= ".$this->search_criteria["by_optional_".$i."_lower"].")";
					}
					elseif($this->search_criteria["by_optional_".$i."_higher"] != "")
					{
						// If no lower limit set to zero
						if($this->search_criteria["by_optional_".$i."_lower"] == "")
						{
							$this->search_criteria["by_optional_".$i."_lower"] = 0;
						}

						if(strlen($opt_where_clause) > 1)
						{
							$opt_where_clause .= " AND ";
						}

						$opt_where_clause .= "(optional_field_".$i." <= ".$this->search_criteria["by_optional_".$i."_higher"]." AND optional_field_".$i." >= ".$this->search_criteria["by_optional_".$i."_lower"].")";
					}
					elseif($this->search_criteria["optional_field_".$i])
					{
						// Text field or dropdown
						if(strlen($opt_where_clause) > 1)
						{
							$opt_where_clause .= " AND ";
						}

						//$opt_where_clause .= "(optional_field_".$i." like \"%".urlencode($this->search_criteria["optional_field_".$i])."%\")";
						$opt_where_clause .= "(optional_field_".$i." = \"".urlencode($this->search_criteria["optional_field_".$i])."\")";
					}
					else
					{
						// This optional field wasnt used so skip it
						continue;
					}

					// Find all ids that match
					$result = $db->Execute($opt_sql_query.$opt_where_clause);
					if($this->debug_search)
					{
						echo $opt_sql_query.$opt_where_clause.' is the optional site wide query<Br>';
						echo  $result->RecordCount()." is recordcount<bR>\n";
					}
					if(!$result)
					{
						$this->error_message = "Error in optional fields query";
						return false;
					}
					elseif ($result->RecordCount() > 0)
					{
						$optional_field[$i] = array();
						$single_key = $i;
						while($record = $result->FetchNextObject())
						{
							array_push($optional_field[$i],$record->ID);
							//$optional_field[] = $record->ID;
						}
					}
					else
					{
						//this is an and condition
						//if no results from one condition then there are no results
						//from the search
						if ($this->debug_search)
						{
							echo "pushing 0 onto optional_field[".$i."]<br>\n";
						}
						$optional_field[$i] = array();
						array_push($optional_field[$i],"0");
						$single_key = $i;
					}

					// Put this array into main one only if it exists
					if(sizeof($optional_field) > 0)
					{
						$field_items[] = $optional_field;
						if ($this->debug_search)
						{
							echo count($field_items)." is the count of field_items for optional fields<bR>\n";
						}
					}


				}

				// Build where statement for this segment
				$where_statement = "";
				if(sizeof($optional_field) > 1)
				{
					// Now we want to intersect all fields and get a list of ids
					if ($this->debug_search)
					{
						echo "more than one optional question filled out<BR>\n";
						echo is_array($optional_field)." is field_items is_array<BR>\n";
						echo count($optional_field)." is count of field_items<BR>\n";
						var_dump($optional_field);
						reset($optional_field);
						foreach($optional_field as $key => $value)
						{
							echo "<br>displaying: ".$key."<bR>\n";
							if (is_array($value))
							{
								reset($value);
								foreach ($value as $key2 => $value2)
								{
									echo "&nbsp;&nbsp;&nbsp;&nbsp;".$key2." is the key to ".$value2."<br>\n";
								}
							}
						}
					}
					$where_items = call_user_func_array('array_intersect', $optional_field);
				}
				elseif(sizeof($optional_field) == 1)
				{
					// If only 1 array in list then we got our list
					if ($this->debug_search)
					{
						echo "only one optional question filled out<BR>\n";
						echo $single_key." is single_key<bR>\n";

					}
					$where_items = $optional_field[$single_key];

					if($this->debug_search)
					{
						echo $where_items." is where_items<BR>\n";
						//var_dump($field_items);
						echo ' is a var dump of the field_items array<Br>';
					}
				}

				if (is_array($where_items))
				{
					reset($where_items);
					$where_statement = "(id in (";
					foreach($where_items as $key => $value)
					{
						$where_statement .= $value.", ";
					}
					$where_statement = rtrim($where_statement, ", ");
					$where_statement .= "))";

					if($this->debug_search)
						echo $where_statement.' is the where statement segment for optional fields<br>';

					if((strlen($this->where_clause) > 0) && (strlen($where_statement) > 0))
					{
						$this->where_clause .= " AND ";
					}

					// Append it back onto the where clause
					if($where_statement)
						$this->where_clause .= $where_statement;

					if($this->testing && $this->test_name == "TestSpecificOptionalFields")
					{
						return $this->sql_query.$this->where_clause;
					}
				}
			}

			if ($this->debug_search)
			{
				echo $this->where_clause."<Br> is the where_clause after site wide optional fields<br>";
			}

			// Do price range checking
			if($this->search_criteria["by_price_lower"] || $this->search_criteria["by_price_higher"])
			{
				if(!$this->search_criteria["by_price_lower"])
				{
					// lower price doesnt exist so default it to 0
					$this->search_criteria["by_price_lower"] = 0.0;
				}

				if(!$this->search_criteria["by_price_higher"])
				{
					// highest price doesnt exist so set it to "infinity"
					$this->search_criteria["by_price_higher"] = 99999999.99;
				}

				if(strlen($this->where_clause) > 0)
				{
					$this->where_clause .= " AND ";
				}

				$this->where_clause .= "(price >= ".$this->search_criteria["by_price_lower"]." AND price <= ".$this->search_criteria["by_price_higher"].")";
			}

			if ($this->debug_search)
			{
				echo $this->where_clause."<Br> is the where_clause after by price<br>";
			}

			if (($this->search_criteria["by_zip_code"]) && ($this->search_criteria["by_zip_code_distance"]) && ($this->configuration_data['use_zip_distance_calculator']))
			{
				if(strlen($this->where_clause) > 0)
				{
					$this->where_clause .= " AND ";
				}
				//if (($this->configuration_data['use_zip_distance_calculator'] == 3) && (!ereg("^[0-9]{1,}$",trim($this->search_criteria["by_zip_code"]))))
				if ($this->configuration_data['use_zip_distance_calculator'] == 3)
				{
					//this zip code search option uses the full US zip code list and an abbreviated Canadian postal code list
					//the Canadian postal code is abbreviated by only using the first three characters of the postal code
					//and averaging the longitude and latitudes values for all postal codes sharing the same first three
					//characters of that postal code.  This compresses the postal codes significantly.

					if ($this->debug_search)
					{
						echo "USE_ZIP_DISTANCE_CALCULATOR == 3<br>\n";
						echo $this->search_criteria["by_zip_code_distance"]." is by_zip_code_distance<br>\n";
					}

					if (strlen(trim($this->search_criteria["by_zip_code_distance"])) > 0)
						$use_zip_code_distance = $this->search_criteria["by_zip_code_distance"];
					elseif (strlen(trim($this->zip_filter_distance)) > 0)
						$use_zip_code_distance = $this->zip_filter_distance;

					//check to see if canadian zip code - canadian postal codes always start with a letter
					if (!ereg("^[0-9]{1,}$",trim($this->search_criteria["by_zip_code"])))
					{
						//this is a canadian postal code
						//get the first 3 characters from the zip code entered and search the database
						$this->canadian_zip = 1;

						if ($this->debug_search) echo "the first character of the zip code is a letter<BR>\n";

						$postcode_list = array();
						if (strlen(trim($this->search_criteria["by_zip_code"])) > 0)
							$use_zip_code = substr($this->search_criteria["by_zip_code"], 0, 3 );
						elseif (strlen(trim($this->zip_filter)) > 0)
							$use_zip_code = substr($this->zip_filter, 0, 3 );

					}
					else
					{
						$this->canadian_zip = 0;
						$use_zip_code = $this->search_criteria["by_zip_code"];
					}

					$this->sql_query = "select * from ".$this->postal_code_table." where zipcode = \"".$use_zip_code."\" limit 1";
					if ($this->debug_search) echo $this->sql_query."<br>\n";
					$zip_result = $db->Execute($this->sql_query);
					if ($this->debug_search) echo $zip_result->RecordCount()." is recordcount<bR>\n";
					if (!$zip_result)
					{
						if ($this->debug_search)
						{
							echo $this->sql_query."<br>\n";
							echo $db->ErrorMsg()." is the error<br>";
							echo "no zip codes in database - needs upgrade - contact Geodesic Solutions for this upgrade or support if you obtained it and are seeing this message<br>";
						}
						return false;
					}
					elseif ($zip_result->RecordCount() == 1)
					{
						$show_zip_data = $zip_result->FetchNextObject();

						if ($this->debug_search)
						{
							echo "data for postal code: ".$use_zip_code."<br>\n";
							echo "distance: ".$this->search_criteria["by_zip_code_distance"]."<bR>\n";
							echo "longitude: ".$show_zip_data->LONGITUDE."<br>\n";
							echo "latitude: ".$show_zip_data->LATITUDE."<br>\n";
						}

						//get the minimum and maximum longitude and latitude
						$this->RadiusAssistant($show_zip_data->LATITUDE, $show_zip_data->LONGITUDE, $this->search_criteria["by_zip_code_distance"]);
						$this->search_zip_latitude = $show_zip_data->LATITUDE;
						$this->search_zip_longitude = $show_zip_data->LONGITUDE;

						if ($this->debug_search)
						{
							echo $this->max_latitude." is max_latitude<br>\n";
							echo $this->min_latitude." is min_latitude<br>\n";
							echo $this->max_longitude." is max_longitude<br>\n";
							echo $this->min_longitude." is min_longitude<br>\n";
						}

						//get the zip codes within distance using min and max longitude and latitude
						$this->sql_query = "select distinct(zipcode) from ".$this->postal_code_table." where
							((latitude >= ".$this->min_latitude." and
							latitude <= ".$this->max_latitude.") and
							(longitude >= ".$this->min_longitude." and
							longitude <= ".$this->max_longitude."))";
						if ($this->debug_search) echo $this->sql_query."<br>\n";
						$range_zip_result = $db->Execute($this->sql_query);
						if (!$range_zip_result)
						{
							if ($this->debug_search) echo $this->sql_query."<br>\n";
							return false;
						}
						elseif ($range_zip_result->RecordCount() > 0)
						{
							$zip_in_statement .= "( ";
							$zip_count = 0;
							while ($show_zip_in = $range_zip_result->FetchNextObject())
							{
								if (!ereg("^[0-9]{1,}$",trim($show_zip_in->ZIPCODE)))
								{
									if ($zip_count == 0)
										$zip_in_statement .= " location_zip like '".$show_zip_in->ZIPCODE."%'";
									else
										$zip_in_statement .= " or  location_zip like '".$show_zip_in->ZIPCODE."%'";
								}
								else
								{
									if ($zip_count == 0)
										$zip_in_statement .= " location_zip like '".$show_zip_in->ZIPCODE."'";
									else
										$zip_in_statement .= " or  location_zip like '".$show_zip_in->ZIPCODE."'";
								}
								$zip_count++;
							}
							$zip_in_statement .= ")";

							if ($this->debug_search)
							{
								echo $zip_in_statement." is the zip_in_statement in USE_ZIP_DISTANCE_CALCULATOR == 3 after looping through<bR>\n";
							}
						}
						else
						{
							//no results
							$zip_in_statement .= "location_zip in ()";
							if ($this->debug_search)
							{
								echo $zip_in_statement." is the zip_in_statement in USE_ZIP_DISTANCE_CALCULATOR == 3 - results = 0<bR>\n";
							}
						}
						if ($this->started)
							$this->where_clause .= " and ".$zip_in_statement."";
						else
							$this->where_clause .= " ".$zip_in_statement."";
						$this->started = 1;
					}
					else
					{
						//there was an error selecting the zip/postal code
						if ($this->debug_search)
						{
							echo "<br>THERE WAS AN ERROR SELECTING THE ZIP/POSTAL CODE IN USE_ZIP_DISTANCE_CALCULATOR = 3<br>\n";
							echo $use_zip_code." is the zip code attempted<Br>\n";
						}
					}

				}
				elseif ($this->configuration_data['use_zip_distance_calculator'] == 2)
				{
					$postcode_list = array();
					if (strlen(trim($this->search_criteria["by_zip_code"])) > 0)
						$use_zip_code = $this->search_criteria["by_zip_code"];
					elseif (strlen(trim($this->zip_filter)) > 0)
						$use_zip_code = $this->zip_filter;

					if (strlen(trim($this->search_criteria["by_zip_code_distance"])) > 0)
						$use_zip_code_distance = $this->search_criteria["by_zip_code_distance"];
					elseif (strlen(trim($this->zip_filter_distance)) > 0)
						$use_zip_code_distance = $this->zip_filter_distance;


					$postcode = trim($options['postcode_from']);
					$this->sql_query = "SELECT postcode, x, y FROM cgi_postcodes WHERE postcode = '" . trim($use_zip_code) . "'";
					if ($this->debug_search) echo $this->sql_query."<bR>\n";
					$postcode_data_result = $db->Execute($this->sql_query);
					$result = mysql_query($sql);
					if($postcode_data_result->RecordCount() <> 1)
					{
						if ($this->debug_search) echo $this->sql_query."<br>\n";
						return false;
					}
					while ($row = $postcode_data_result->FetchNextObject())
					{
						$user_x = $row->X;
						$user_y = $row->Y;
					}
					//$_radius = $use_zip_code_distance + 5;
					$_radius = $use_zip_code_distance;
					$_radius = $_radius / 0.621;
					$_radius = sprintf("%.0f", $_radius * 1000);
					$_radius = $_radius / 2;
					$_lowerx = $user_x - $_radius;
					$_lowery = $user_y - $_radius;
					$_upperx = $user_x + $_radius;
					$_uppery = $user_y + $_radius;
					$this->sql_query = "SELECT x, y, postcode FROM cgi_postcodes WHERE (x >= $_lowerx and y >= $_lowery) AND (x <= $_upperx and y <= $_uppery)";
					if ($this->debug_search) echo $this->sql_query."<bR>\n";
					$postcode_result = $db->Execute($this->sql_query);
					if((!$postcode_result) || ($postcode_result->RecordCount() == 0))
					{
						if ($this->debug_search) echo $this->sql_query."<bR>\n";
						return false;
					}
					$zip_in_statement .= "location_zip in (";
					$zip_count = 0;
					while ($row = $postcode_result->FetchNextObject())
					{
						$distance = sqrt((($row->X -$user_x) * ($row->X -$user_x)) + (($row->Y - $user_y) * ($row->Y - $user_y)));
						$km = sprintf("%.2f", $distance / 1000);
						$miles = sprintf("%.2f", $km * 0.621);
						$this->uk_postcodes[urlencode($row->POSTCODE)] = $miles;
						if ($this->debug_search) echo $row->POSTCODE." has a distance of: ".$miles."<br>";
						if ($zip_count == 0)
						{
							$zip_in_statement .= "'".urlencode($row->POSTCODE)."'";
						}
						else
						{
							$zip_in_statement .= ",'".urlencode($row->POSTCODE)."'";
						}
						$zip_count++;
					}
					$zip_in_statement .= ")";
					if ($this->debug_search) echo $zip_in_statement." is zip in statement within site_class<bR>\n";

					// Put the zip in statement into the where clause
					if(strlen($this->where_clause) > 0)
					{
						$this->where_clause = "(".$this->where_clause.") AND ".$zip_in_statement;
					}
					else
					{
						$this->where_clause = $zip_in_statement;
					}
				}
				elseif ($this->configuration_data['use_zip_distance_calculator'] == 1)
				{
					//get the longitude and latitude of the zip code entered
					$this->sql_query = "select * from ".$this->postal_code_table." where zipcode = \"".$this->search_criteria["by_zip_code"]."\" limit 1";
					if ($this->debug_search) echo $this->sql_query."<br>\n";
					$zip_result = $db->Execute($this->sql_query);
					if ($this->debug_search) echo $zip_result->RecordCount()." is recordcount<bR>\n";
					if (!$zip_result)
					{
						if ($this->debug_search)
						{
							echo $this->sql_query."<br>\n";
							echo $db->ErrorMsg()." is the error<br>";
							echo "no zip codes in database - needs upgrade - contact Geodesic Solutions for this upgrade or support if you obtained it and are seeing this message<br>";
						}
						return false;
					}
					elseif ($zip_result->RecordCount() == 1)
					{
						//zip code data found...continue
						$show_zip_data = $zip_result->FetchNextObject();

						//get the minimum and maximum longitude and latitude
						$this->RadiusAssistant($show_zip_data->LATITUDE, $show_zip_data->LONGITUDE, $this->search_criteria["by_zip_code_distance"]);

						$this->search_zip_latitude = $show_zip_data->LATITUDE;
						$this->search_zip_longitude = $show_zip_data->LONGITUDE;

						//get the zip codes within distance using min and max longitude and latitude
						$this->sql_query = "select distinct(zipcode) from ".$this->postal_code_table." where
							((latitude >= ".$this->min_latitude." and
							latitude <= ".$this->max_latitude.") and
							(longitude >= ".$this->min_longitude." and
							longitude <= ".$this->max_longitude."))";
						if ($this->debug_search) echo $this->sql_query."<br>\n";
						$range_zip_result = $db->Execute($this->sql_query);
						if (!$range_zip_result)
						{
							if ($this->debug_search) echo $this->sql_query."<br>\n";
							return false;
						}
						elseif ($range_zip_result->RecordCount() > 0)
						{
							$zip_in_statement .= "location_zip in (";
							$zip_count = 0;
							while ($show_zip_in = $range_zip_result->FetchNextObject())
							{
								if ($zip_count == 0)
									$zip_in_statement .= "'".$show_zip_in->ZIPCODE."'";
								else
									$zip_in_statement .= ",'".$show_zip_in->ZIPCODE."'";
								$zip_count++;
							}
							$zip_in_statement .= ")";
						}
						else
						{
							//no results
							$zip_in_statement .= "location_zip in ()";
						}

						// Put the zip in statement into the where clause
						if(strlen($this->where_clause) > 0)
						{
							$this->where_clause = "(".$this->where_clause.") AND ".$zip_in_statement;
						}
						else
						{
							$this->where_clause = $zip_in_statement;
						}

						if($this->testing && $this->test_name == "TestZipFilter")
						{
							$this->sql_query = "SELECT * FROM ".$this->classifieds_table." WHERE ";
							if ($this->debug_search) echo $this->where_clause."<Br>";
							return $this->sql_query.$this->where_clause;
						}
					}
				}

			}
			elseif ($this->search_criteria["by_zip_code"])
			{
				if(strlen($this->where_clause) > 0)
					$this->where_clause = "(".$this->where_clause.") AND ( location_zip like \"%".$this->search_criteria["by_zip_code"]."%\")";
				else
					$this->where_clause = "( location_zip like \"%".$this->search_criteria["by_zip_code"]."%\")";
			}

			if ($this->filter_id)
			{
				if ($this->debug_search)
				{
					echo "about to add filter_id filtering<br>";
				}
				//add filter association to end of sql_query
				if(strlen($this->where_clause) > 0)
				{
					$this->where_clause .= " AND ";
				}

				$filter_in_statement = $this->get_sql_filter_in_statement($db);
				$this->where_clause .= "( filter_id ".$filter_in_statement." ) ";
				if ($this->debug_search)
				{
					echo $this->where_clause." is where clause after adding filter_id filter<BR><BR>";
				}
			}


			if ($this->debug_search)
			{
				echo $this->where_clause."<Br> is the where_clause after zip code<br>";
			}

			if (($this->search_criteria["by_country"]) && ($this->search_criteria["by_country"] != "none"))
			{
				if(strlen($this->where_clause) > 0)
				{
					$this->where_clause = "(".$this->where_clause.") AND ( location_country like \"".$this->search_criteria["by_country"]."\")";
				}
				else
				{
					$this->where_clause = "( location_country like \"".$this->search_criteria["by_country"]."\")";
				}

				if($this->testing && $this->test_name == "TestByCountry")
				{
					$this->sql_query = "SELECT * FROM ".$this->classifieds_table." WHERE ";
					//echo $this->where_clause."<Br>";
					return $this->sql_query.$this->where_clause;
				}
			}

			if ($this->debug_search)
			{
				echo $this->search_criteria["by_state"]." is by_state<BR>\n";
			}
			if (($this->state_filter) && ((!$this->search_criteria["by_state"]) || ($this->search_criteria["by_state"] == "none")))
			{
				if ($this->debug_search)
				{
					echo "adding state filter<BR>\n";
				}
				//add state to end of sql_query
				if(strlen($this->where_clause) > 0)
				{
					$this->where_clause .= " AND ";
				}
				$this->where_clause .= " ( location_state LIKE \"%".trim($this->state_filter)."%\" ) ";

				if ($this->debug_search)
				{
					echo $this->where_clause." is where clause after adding state filter<BR><BR>";
				}

			}
			elseif (($this->search_criteria["by_state"]) && ($this->search_criteria["by_state"] != "none"))
			{
				if ($this->debug_search)
				{
					echo "adding by_state<BR>\n";
				}

				if (strlen($this->where_clause) > 0)
				{
					$this->where_clause = "(".$this->where_clause.") AND ( location_state like \"%".$this->search_criteria["by_state"]."%\")";
				}
				else
				{
					$this->where_clause = "( location_state like \"%".$this->search_criteria["by_state"]."%\")";
				}

				if($this->testing && $this->test_name == "TestByState")
				{
					$this->sql_query = "SELECT * FROM ".$this->classifieds_table." WHERE ";
					//echo $this->where_clause."<Br>";
					return $this->sql_query.$this->where_clause;
				}
				if ($this->debug_search)
				{
					echo $this->where_clause." is the where_clause after adding by_state<BR>\n";
				}
			}

			if (($this->search_criteria["by_city"]) && ($this->search_criteria["by_city"] != "none"))
			{
				if (strlen($this->where_clause) > 0)
				{
					$this->where_clause = "(".$this->where_clause.") AND ( location_city like \"".$this->search_criteria["by_city"]."\")";
				}
				else
				{
					$this->where_clause = "( location_city like \"".$this->search_criteria["by_city"]."\")";
				}

				if($this->testing && $this->test_name == "TestByCity")
				{
					$this->sql_query = "SELECT * FROM ".$this->classifieds_table." WHERE ";
					//echo $this->where_clause."<Br>";
					return $this->sql_query.$this->where_clause;
				}
			}

			// Do check for business type
			if($this->search_criteria["by_business_type"])
			{
				if(strlen($this->where_clause) > 0)
				{
					if($this->search_criteria["by_business_type"] == 1)
					{
						// Individual
						$this->where_clause = "(".$this->where_clause." AND (business_type = 1))";
					}
					else
					{
						// Business
						$this->where_clause = "(".$this->where_clause." AND (business_type = 2))";
					}
				}
				else
				{
					if($this->search_criteria["by_business_type"] == 1)
					{
						// Individual
						$this->where_clause = "(business_type = 1)";
					}
					else
					{
						// Business
						$this->where_clause = "(business_type = 2)";
					}
				}
			}

			// Put in category selecting
			if($this->site_category != 0)
			{
				if($this->search_criteria["subcategories_also"])
				{
					$this->sql_query = "select in_statement from ".$this->categories_table." where category_id = ".$this->site_category;
					$result = $db->Execute($this->sql_query);
					if(!$result)
					{
						return false;
					}
					else
					{
						$in_statement = $result->FetchNextObject();

						if(strlen($this->where_clause) > 0)
						{
							$this->where_clause = "(".$this->where_clause.") AND category ".$in_statement->IN_STATEMENT;
						}
						else
						{
							$this->where_clause = "category ".$in_statement->IN_STATEMENT;
						}

						if($this->debug_search) echo $this->sql_query.$this->where_clause.'<Br>';

						if($this->testing && $this->test_name == "TestCategoryWithSubs")
						{
							return $this->sql_query.$this->where_clause;
						}
					}
				}
				else
				{
					if(strlen($this->where_clause) > 0)
					{
						$this->where_clause = "(".$this->where_clause.") AND category = ".$this->site_category;
					}
					else
					{
						$this->where_clause = "category = ".$this->site_category;
					}

					if($this->testing && $this->test_name == "TestCategory")
					{
						return $this->sql_query.$this->where_clause;
					}
				}
			}







			// Create order by clause
			switch ($browse_type)
			{
				case 0: //nothing
					$order_by = "order by better_placement desc,date desc ";
					break;
				case 1: //price desc
					$order_by = " order by price desc, current_bid desc, better_placement desc ";
					break;
				case 2: //price asc
					$order_by = " order by price asc, current_bid asc, better_placement desc ";
					break;
				case 3: //date earliest to latest
					$order_by = " order by date asc, better_placement desc ";
					break;
				case 4: //date latest to earliest
					$order_by = " order by date desc, better_placement desc ";
					break;
				case 5: //title asc
					$order_by = " order by title asc, better_placement desc ";
					break;
				case 6: //title desc
					$order_by = " order by title desc, better_placement desc ";
					break;
				case 7: //city asc
					$order_by = " order by location_city asc, better_placement desc ";
					break;
				case 8: //city desc
					$order_by = " order by location_city desc, better_placement desc ";
					break;
				case 9: //state asc
					$order_by = " order by location_state asc, better_placement desc ";
					break;
				case 10: //state desc
					$order_by = " order by location_state desc, better_placement desc ";
					break;
				case 11: //country asc
					$order_by = " order by location_country asc, better_placement desc ";
					break;
				case 12: //country desc
					$order_by = " order by location_country desc, better_placement desc ";
					break;
				case 13: //zip asc
					$order_by = " order by location_zip asc, better_placement desc ";
					break;
				case 14: //zip desc
					$order_by = " order by location_zip desc, better_placement desc ";
					break;
				case 15: //optional field 1 asc
					$order_by = " order by optional_field_1 asc, better_placement desc ";
					break;
				case 16: //optional field 1 desc
					$order_by = " order by optional_field_1 desc, better_placement desc ";
					break;
				case 17: //optional field 2 asc
					$order_by = " order by optional_field_2 asc, better_placement desc ";
					break;
				case 18: //optional field 2 desc
					$order_by = " order by optional_field_2 desc, better_placement desc ";
					break;
				case 19: //optional field 3 asc
					$order_by = " order by optional_field_3 asc, better_placement desc ";
					break;
				case 20: //optional field 3 desc
					$order_by = " order by optional_field_3 desc, better_placement desc ";
					break;
				case 21: //optional field 4 asc
					$order_by = " order by optional_field_4 asc, better_placement desc ";
					break;
				case 22: //optional field 4 desc
					$order_by = " order by optional_field_4 desc, better_placement desc ";
					break;
				case 23: //optional field 5 asc
					$order_by = " order by optional_field_5 asc, better_placement desc ";
					break;
				case 24: //optional field 5 desc
					$order_by = " order by optional_field_5 desc, better_placement desc ";
					break;
				case 25: //optional field 6 asc
					$order_by = " order by optional_field_6 asc, better_placement desc ";
					break;
				case 26: //optional field 6 desc
					$order_by = " order by optional_field_6 desc, better_placement desc ";
					break;
				case 27: //optional field 7 asc
					$order_by = " order by optional_field_7 asc, better_placement desc ";
					break;
				case 28: //optional field 7 desc
					$order_by = " order by optional_field_7 desc, better_placement desc ";
					break;
				case 29: //optional field 8 asc
					$order_by = " order by optional_field_8 asc, better_placement desc ";
					break;
				case 30: //optional field 8 desc
					$order_by = " order by optional_field_8 desc, better_placement desc ";
					break;
				case 31: //optional field 9 asc
					$order_by = " order by optional_field_9 asc, better_placement desc ";
					break;
				case 32: //optional field 9 desc
					$order_by = " order by optional_field_9 desc, better_placement desc ";
					break;
				case 33: //optional field 10 asc
					$order_by = " order by optional_field_10 asc, better_placement desc ";
					break;
				case 34: //optional field 10 desc
					$order_by = " order by optional_field_10 desc, better_placement desc ";
					break;
				case 35: //city asc
					$order_by = " order by location_city asc, better_placement desc ";
					break;
				case 36: //city desc
					$order_by = " order by location_city desc, better_placement desc ";
					break;
				case 37: //state asc
					$order_by = " order by location_state asc, better_placement desc ";
					break;
				case 38: //state desc
					$order_by = " order by location_state desc, better_placement desc ";
					break;
				case 39: //country asc
					$order_by = " order by location_country asc, better_placement desc ";
					break;
				case 40: //country desc
					$order_by = " order by location_country desc, better_placement desc ";
					break;
				case 41: //zip asc
					$order_by = " order by location_zip asc, better_placement desc ";
					break;
				case 42: //zip desc
					$order_by = " order by location_zip desc, better_placement desc ";
					break;
				case 43: //business_type asc
					$order_by = " order by business_type asc, better_placement desc ";
					break;
				case 44: //business_type desc
					$order_by = " order by business_type desc, better_placement desc ";
					break;
				case 45: //optional field 11 asc
					$order_by = " order by optional_field_11 asc, better_placement desc ";
					break;
				case 46: //optional field 11 desc
					$order_by = " order by optional_field_11 desc, better_placement desc ";
					break;
				case 47: //optional field 12 asc
					$order_by = " order by optional_field_12 asc, better_placement desc ";
					break;
				case 48: //optional field 12 desc
					$order_by = " order by optional_field_12 desc, better_placement desc ";
					break;
				case 49: //optional field 13 asc
					$order_by = " order by optional_field_13 asc, better_placement desc ";
					break;
				case 50: //optional field 13 desc
					$order_by = " order by optional_field_13 desc, better_placement desc ";
					break;
				case 51: //optional field 14 asc
					$order_by = " order by optional_field_14 asc, better_placement desc ";
					break;
				case 52: //optional field 14 desc
					$order_by = " order by optional_field_14 desc, better_placement desc ";
					break;
				case 53: //optional field 15 asc
					$order_by = " order by optional_field_15 asc, better_placement desc ";
					break;
				case 54: //optional field 15 desc
					$order_by = " order by optional_field_15 desc, better_placement desc ";
					break;
				case 55: //optional field 16 asc
					$order_by = " order by optional_field_16 asc, better_placement desc ";
					break;
				case 56: //optional field 16 desc
					$order_by = " order by optional_field_16 desc, better_placement desc ";
					break;
				case 57: //optional field 17 asc
					$order_by = " order by optional_field_17 asc, better_placement desc ";
					break;
				case 58: //optional field 17 desc
					$order_by = " order by optional_field_17 desc, better_placement desc ";
					break;
				case 59: //optional field 18 asc
					$order_by = " order by optional_field_18 asc, better_placement desc ";
					break;
				case 60: //optional field 18 desc
					$order_by = " order by optional_field_18 desc, better_placement desc ";
					break;
				case 61: //optional field 19 asc
					$order_by = " order by optional_field_19 asc, better_placement desc ";
					break;
				case 62: //optional field 19 desc
					$order_by = " order by optional_field_19 desc, better_placement desc ";
					break;
				case 63: //optional field 20 asc
					$order_by = " order by optional_field_20 asc, better_placement desc ";
					break;
				case 64: //optional field 20 desc
					$order_by = " order by optional_field_20 desc, better_placement desc ";
					break;
				case 65: //classifids first
					$order_by = " order by item_type asc, better_placement desc ";
					break;
				case 66: //auctions first
					$order_by = " order by item_type desc, better_placement desc ";
					break;
				default:
					$order_by = "order by better_placement desc, date desc ";
					break;
			}
			$this->browse_type = $browse_type;

			// Perform actual search
			// If query was changed put it back to original state
			if($this->sql_query !== "SELECT * FROM ".$this->classifieds_table." WHERE ")
			{
				$this->sql_query = "SELECT * FROM ".$this->classifieds_table." WHERE ";
			}


            //It was changed on 21.03.2006 WWW.BCS-IT.COM
			if($this->where_clause) $this->count_sql_query = "SELECT count(id) as total_count FROM ".$this->classifieds_table." WHERE (".$this->where_clause.") AND live = 1";
            else $this->count_sql_query = "SELECT count(id) as total_count FROM ".$this->classifieds_table." WHERE live = 1";

            if($this->debug_search) echo $this->count_sql_query." is the count query<br>";
			$count_result = $db->Execute($this->count_sql_query);
			if($this->debug_search)
			{
				echo $this->sql_query."<br>";
				if ($count_result)
					echo $count_result->RecordCount()." is the recordcount from the count_sql_query<BR>\n";
			}
			if(!$count_result)
			{
				if($this->debug_search)
				{
					echo $db->ErrorMsg()." is the sql error <BR>\n";
					echo $this->count_sql_query."<BR>\n";
				}
				return false;
			}
			elseif($count_result->RecordCount() == 1)
			{
				$show_total = $count_result->FetchNextObject();
				$this->total_returned = $show_total->TOTAL_COUNT;
				if($this->debug_search)
				{
					echo "getting total_returned<BR>\n";
					echo $show_total->TOTAL_COUNT." is TOTAL_COUNT<bR>\n";
				}
			}
			else
			{
				$this->total_returned = 0;
			}

			if ($this->debug_search)
			{
				echo $this->total_returned." is total_returned<BR>\n";
			}

			if (($this->total_returned == 1) && ($this->search_criteria["whole_word"] == 2))
			{
				if ($this->debug_search)
				{
					echo "REDIRECTING TO ID: ".$this->search_criteria["search_text"]." as an id search<BR>\n";
				}
				header("Location: ".$this->configuration_data['classifieds_url']."?a=2&b=".$this->search_criteria["search_text"]);
				exit;
			}
			elseif ($this->total_returned != 0)
			{
                //It was changed on 21.03.2006 WWW.BCS-IT.COM
                if($this->where_clause) $this->sql_query = "SELECT * FROM ".$this->classifieds_table." WHERE (".$this->where_clause.") AND live = 1 ".$order_by;
                else $this->sql_query = "SELECT * FROM ".$this->classifieds_table." WHERE live = 1 ".$order_by;

                //$this->sql_query = "SELECT * FROM ".$this->classifieds_table." WHERE (".$this->where_clause.") AND live = 1 ";
				if ($this->search_criteria["page"])
				{
					$this->sql_query = $this->sql_query." limit ".(($this->search_criteria["page"] - 1) * $this->configuration_data['number_of_ads_to_display']).",".$this->configuration_data['number_of_ads_to_display'];
				}
				else
				{
					$this->sql_query = $this->sql_query." limit 0,".$this->configuration_data['number_of_ads_to_display'];
				}
				if($this->debug_search) echo $this->sql_query.' is the final sql_query<br>';

                $result = $db->Execute($this->sql_query);
				if(!$result)
				{
					if ($this->debug_search)
					{
						echo $db->ErrorMsg()." is the error<bR>\n";
					}
					return false;
				}
				elseif($result->RecordCount() == 1000000000)
				{

					$returned_result = $result->FetchNextObject();

					if ($this->debug_search)
					{
						echo "REDIRECTING TO ID: ".$returned_result->ID." as the only result of a search<BR>\n";
					}

					header("Location: ".$this->configuration_data['classifieds_url']."?a=2&b=".$returned_result->ID);
					exit;

				}
				elseif($result->RecordCount() > 0)
				{
					 //".$order_by;
					$this->BuildResults($db, $result);
				}
				else
				{
					// No results returned
					$this->body .="<tr class=search_page_instructions>\n\t<td colspan=4>\n\t".urldecode($this->messages[592])."\n\t</td>\n</tr>\n";
					return false;
				}
			}
			else
			{
				//no results
				return false;
			}

			return true;
		}
		else
		{
			//echo 'Getting here<br>';
			// No search criteria
			return false;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function BuildResults($db, $result = 0)
	{
		//$this->total_returned = $result->RecordCount();
		if ($this->debug_display_results)
		{
			echo "<Br>TOP OF BUILDRESULTS<br>\n";
			echo $this->total_returned." is total_returned<br>\n";
			echo $this->configuration_data['number_of_ads_to_display']." is NUMBER_OF_ADS_TO_DISPLAY<br>\n";
		}

		$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=19 method=post>\n";
		$this->body .="<table width=100% cellpadding=2 cellspacing=0 border=0>\n";
		$this->body .="<tr class=search_page_title>\n\t<td>\n\t".urldecode($this->messages[593])."\n\t</td>\n</tr>\n";

		if ($this->configuration_data['number_of_ads_to_display'] < $this->total_returned)
		{
			//display the link to the next 10
			$number_of_page_results = ceil($this->total_returned / $this->configuration_data['number_of_ads_to_display']);
			if ($this->debug_display_results)
			{
				echo $number_of_page_results." is the number_of_page_results<bR>\n";
			}
			$this->search_page_results .="<tr class=search_page_instructions>\n\t<td valign=top>".urldecode($this->messages[599])." ";
			$this->search_page_results .="(".$this->total_returned.")";
			if ($number_of_page_results < 10)
			{
				for ($i = 1;$i <= $number_of_page_results;$i++)
				{
					if (($this->search_criteria["page"] == $i) || (($this->search_criteria["page"] == 0) && ($i == 1)))
					{
						if ($this->debug_display_results)
						{
							echo "this is the page<br>\n";
							echo $this->search_criteria["page"]." is search_criteria[page]<br>\n";
							echo $i." is i<Br>\n";
						}
						$this->search_page_results .=" <font class=search_page_instructions>&lt;".$i."&gt;</font> ";
					}
					else
					{
						if ($this->debug_display_results)
						{
							echo "this is NOT the page<br>\n";
							echo $this->search_criteria["page"]." is search_criteria[page]<br>\n";
							echo $i." is i<Br>\n";
						}
						$this->search_page_results .="<a href=".$this->configuration_data['classifieds_file_name']."?a=19";
						if ($this->search_criteria["search_titles"])
							$this->search_page_results .="&b[search_titles]=1";
						if ($this->search_criteria["search_descriptions"])
							$this->search_page_results .="&b[search_descriptions]=1";
						if (strlen(trim($this->original_search_term)) > 0)
							$this->search_page_results .="&b[search_text]=".urlencode($this->original_search_term);
						if ($this->browse_type)
							$this->search_page_results .="&order=".$this->browse_type;
						if ($this->site_category)
						{
							$this->search_page_results .="&c=".$this->site_category;
							if ($this->search_criteria["question_value"])
							{
								foreach($this->search_criteria["question_value"] as $key => $value)
								{
									if (strlen(trim($value)) > 0)
										$this->search_page_results .="&b[question_value][".$key."]=".urlencode($value);
								}
							}
						}

						if (($this->search_criteria["by_business_type"]) && (strlen(trim($this->search_criteria["by_business_type"])) > 0))
							$this->search_page_results .="&b[by_business_type]=".urlencode($this->search_criteria["by_business_type"]);
						if ($this->search_criteria["by_price_lower"])
							$this->search_page_results .="&b[by_price_lower]=".urlencode($this->search_criteria["by_price_lower"]);
						if ($this->search_criteria["by_price_higher"])
							$this->search_page_results .="&b[by_price_higher]=".urlencode($this->search_criteria["by_price_higher"]);
						if ($this->search_criteria["by_zip_code"])
							$this->search_page_results .="&b[by_zip_code]=".urlencode($this->search_criteria["by_zip_code"]);
						if ($this->search_criteria["by_zip_code_distance"])
							$this->search_page_results .="&b[by_zip_code_distance]=".urlencode($this->search_criteria["by_zip_code_distance"]);
						if (($this->search_criteria["by_country"]) && ($this->search_criteria["by_country"] != "none"))
							$this->search_page_results .="&b[by_country]=".urlencode($this->search_criteria["by_country"]);
						if ($this->search_criteria["subcategories_also"])
							$this->search_page_results .="&b[subcategories_also]=1";
						if (($this->search_criteria["by_state"]) && ($this->search_criteria["by_state"] != "none"))
							$this->search_page_results .="&b[by_state]=".urlencode($this->search_criteria["by_state"]);

						// Put in optional fields
						for($f = 1; $f < 35; $f++)
						{
							if (($this->search_criteria["optional_field_".$f]) && (strlen(trim($this->search_criteria["optional_field_".$f])) > 0))
								$this->search_page_results .="&b[optional_field_".$f."]=".urlencode($this->search_criteria["optional_field_".$f]);
							if ($this->search_criteria["by_optional_".$f."_higher"])
								$this->search_page_results .="&b[by_optional_".$f."_higher]=".urlencode($this->search_criteria["by_optional_".$f."_higher"]);
							if ($this->search_criteria["by_optional_".$f."_lower"])
								$this->search_page_results .="&b[by_optional_".$f."_lower]=".urlencode($this->search_criteria["by_optional_".$f."_lower"]);
						}

						$this->search_page_results .="&b[page]=".$i;
						$this->search_page_results .=" class=search_page_instructions>[".$i."]</a> ";
					}

				}
			}
			elseif($number_of_page_results < 100)
			{
				$number_of_sections =  ceil($number_of_page_results/10);
				for ($section = 0;$section < $number_of_sections;$section++)
				{
					if ((($this->search_criteria["page"] > ($section * 10)) && ($this->search_criteria["page"] <= (($section+1) * 10)))
						|| ((strlen($this->search_criteria["page"] ) == 0) && ($section == 0)))
					{
						//display the individual pages within this section
						for ($page = (($section * 10) + 1);$page <= (($section+1) * 10);$page++)
						{
							if ($page <= $number_of_page_results)
							{
								if (($this->search_criteria["page"] == $page) || (($this->search_criteria["page"] == 0) && ($page == 1)))
								{
									$this->search_page_results .=" <font class=search_page_instructions>&lt;".$page."&gt;</font> ";
								}
								else
								{
									$this->search_page_results .="<a href=".$this->configuration_data['classifieds_file_name']."?a=19";
									if ($this->search_criteria["search_titles"])
										$this->search_page_results .="&b[search_titles]=1";
									if ($this->search_criteria["search_descriptions"])
										$this->search_page_results .="&b[search_descriptions]=1";
									if (strlen(trim($this->original_search_term)) > 0)
										$this->search_page_results .="&b[search_text]=".urlencode($this->original_search_term);
									if ($this->browse_type)
										$this->search_page_results .="&order=".$this->browse_type;
									if ($this->site_category)
									{
										$this->search_page_results .="&c=".$this->site_category;
										if ($this->search_criteria["question_value"])
										{
											foreach($this->search_criteria["question_value"] as $key => $value)
											{
												if (strlen(trim($value)) > 0)
													$this->search_page_results .="&b[question_value][".$key."]=".urlencode($value);
											}
										}
									}

									if ($this->search_criteria["by_business_type"])
										$this->search_page_results .="&b[by_business_type]=".urlencode($this->search_criteria["by_business_type"]);
									if ($this->search_criteria["by_price_lower"])
										$this->search_page_results .="&b[by_price_lower]=".urlencode($this->search_criteria["by_price_lower"]);
									if ($this->search_criteria["by_price_higher"])
										$this->search_page_results .="&b[by_price_higher]=".urlencode($this->search_criteria["by_price_higher"]);
									if ($this->search_criteria["by_zip_code"])
										$this->search_page_results .="&b[by_zip_code]=".urlencode($this->search_criteria["by_zip_code"]);
									if ($this->search_criteria["by_zip_code_distance"])
										$this->search_page_results .="&b[by_zip_code_distance]=".urlencode($this->search_criteria["by_zip_code_distance"]);

									if (($this->search_criteria["by_country"]) && ($this->search_criteria["by_country"] != "none"))
										$this->search_page_results .="&b[by_country]=".urlencode($this->search_criteria["by_country"]);
									if ($this->search_criteria["subcategories_also"])
										$this->search_page_results .="&b[subcategories_also]=1";
									if (($this->search_criteria["by_state"]) && ($this->search_criteria["by_state"] != "none"))
										$this->search_page_results .="&b[by_state]=".urlencode($this->search_criteria["by_state"]);

									// Put in optional fields
									for($f = 1; $f < 35; $f++)
									{
										if (($this->search_criteria["optional_field_".$f]) && (strlen(trim($this->search_criteria["optional_field_".$f])) > 0))
											$this->search_page_results .="&b[optional_field_".$f."]=".urlencode($this->search_criteria["optional_field_".$f]);
										if ($this->search_criteria["by_optional_".$f."_higher"])
											$this->search_page_results .="&b[by_optional_".$f."_higher]=".urlencode($this->search_criteria["by_optional_".$f."_higher"]);
										if ($this->search_criteria["by_optional_".$f."_lower"])
											$this->search_page_results .="&b[by_optional_".$f."_lower]=".urlencode($this->search_criteria["by_optional_".$f."_lower"]);
									}

									$this->search_page_results .="&b[page]=".$page;
									$this->search_page_results .=" class=search_page_instructions>[".$page."]</a> ";
								}
							}
						}
					}
					else
					{
						//display the link to the section
						$this->search_page_results .="<a href=".$this->configuration_data['classifieds_file_name']."?a=19";
						if ($this->search_criteria["search_titles"])
							$this->search_page_results .="&b[search_titles]=1";
						if ($this->search_criteria["search_descriptions"])
							$this->search_page_results .="&b[search_descriptions]=1";
						if (strlen(trim($this->original_search_term)) > 0)
							$this->search_page_results .="&b[search_text]=".urlencode($this->original_search_term);
						if ($this->browse_type)
							$this->search_page_results .="&order=".$this->browse_type;
						if ($this->site_category)
						{
							$this->search_page_results .="&c=".$this->site_category;
							if ($this->search_criteria["question_value"])
							{
								foreach($this->search_criteria["question_value"] as $key => $value)
								{
									if (strlen(trim($value)) > 0)
										$this->search_page_results .="&b[question_value][".$key."]=".urlencode($value);
								}
							}
						}

						if ($this->search_criteria["by_business_type"])
						$this->search_page_results .="&b[by_business_type]=".urlencode($this->search_criteria["by_business_type"]);
						if ($this->search_criteria["by_price_lower"])
						$this->search_page_results .="&b[by_price_lower]=".urlencode($this->search_criteria["by_price_lower"]);
						if ($this->search_criteria["by_price_higher"])
						$this->search_page_results .="&b[by_price_higher]=".urlencode($this->search_criteria["by_price_higher"]);
						if ($this->search_criteria["by_zip_code"])
						$this->search_page_results .="&b[by_zip_code]=".urlencode($this->search_criteria["by_zip_code"]);
						if ($this->search_criteria["by_zip_code_distance"])
						$this->search_page_results .="&b[by_zip_code_distance]=".urlencode($this->search_criteria["by_zip_code_distance"]);
						if (($this->search_criteria["by_country"]) && ($this->search_criteria["by_country"] != "none"))
						$this->search_page_results .="&b[by_country]=".urlencode($this->search_criteria["by_country"]);
						if ($this->search_criteria["subcategories_also"])
						$this->search_page_results .="&b[subcategories_also]=1";
						if (($this->search_criteria["by_state"]) && ($this->search_criteria["by_state"] != "none"))
						$this->search_page_results .="&b[by_state]=".urlencode($this->search_criteria["by_state"]);

						// Put in optional fields
						for($f = 1; $f < 35; $f++)
						{
							if (($this->search_criteria["optional_field_".$f]) && (strlen(trim($this->search_criteria["optional_field_".$f])) > 0))
								$this->search_page_results .="&b[optional_field_".$f."]=".urlencode($this->search_criteria["optional_field_".$f]);
							if ($this->search_criteria["by_optional_".$f."_higher"])
								$this->search_page_results .="&b[by_optional_".$f."_higher]=".urlencode($this->search_criteria["by_optional_".$f."_higher"]);
							if ($this->search_criteria["by_optional_".$f."_lower"])
								$this->search_page_results .="&b[by_optional_".$f."_lower]=".urlencode($this->search_criteria["by_optional_".$f."_lower"]);
						}

						$this->search_page_results .="&b[page]=".(($section*10)+1);
						$this->search_page_results .=" class=search_page_instructions>[".(($section*10)+1)."]</a> ";
					}
					if (($section+1) < $number_of_sections)
					$this->search_page_results .="..";
				}
			}
			else
			{
				$number_of_sections =  ceil($number_of_page_results/100);
				for ($section = 0;$section < $number_of_sections;$section++)
				{
					if ((($this->search_criteria["page"] > ($section * 100)) && ($this->search_criteria["page"] <= (($section+1) * 100)))
						|| ((strlen($this->search_criteria["page"] ) == 0) && ($section == 0)))
					{
						//display the individual pages within this section
						//display tens
						for ($page = (($section * 100) + 1);$page <= (($section+1) * 100);$page+=10)
						{
							if ((($this->search_criteria["page"] >= $page) && ($this->search_criteria["page"] <= ($page+9))))
							{
								//display ones
								for ($page_link = $page;$page_link <= ($page+9);$page_link++)
								{
									if ($page_link <= $number_of_page_results)
									{
										if (($this->search_criteria["page"] == $page_link) || (($this->search_criteria["page"] == 0) && ($page_link == 1)))
										{
											$this->search_page_results .=" <font class=search_page_instructions>&lt;".$page_link."&gt;</font> ";
										}
										else
										{
											$this->search_page_results .="<a href=".$this->configuration_data['classifieds_file_name']."?a=19";
											if ($this->search_criteria["search_titles"])
												$this->search_page_results .="&b[search_titles]=1";
											if ($this->search_criteria["search_descriptions"])
												$this->search_page_results .="&b[search_descriptions]=1";
											if (strlen(trim($this->original_search_term)) > 0)
												$this->search_page_results .="&b[search_text]=".urlencode($this->original_search_term);
											if ($this->browse_type)
												$this->search_page_results .="&order=".$this->browse_type;
											if ($this->site_category)
											{
												$this->search_page_results .="&c=".$this->site_category;
												if ($this->search_criteria["question_value"])
												{
													foreach($this->search_criteria["question_value"] as $key => $value)
													{
														if (strlen(trim($value)) > 0)
															$this->search_page_results .="&b[question_value][".$key."]=".urlencode($value);
													}
												}
											}

											if ($this->search_criteria["by_business_type"])
												$this->search_page_results .="&b[by_business_type]=".urlencode($this->search_criteria["by_business_type"]);
											if ($this->search_criteria["by_price_lower"])
												$this->search_page_results .="&b[by_price_lower]=".urlencode($this->search_criteria["by_price_lower"]);
											if ($this->search_criteria["by_price_higher"])
												$this->search_page_results .="&b[by_price_higher]=".urlencode($this->search_criteria["by_price_higher"]);
											if ($this->search_criteria["by_zip_code"])
												$this->search_page_results .="&b[by_zip_code]=".urlencode($this->search_criteria["by_zip_code"]);
											if ($this->search_criteria["by_zip_code_distance"])
												$this->search_page_results .="&b[by_zip_code_distance]=".urlencode($this->search_criteria["by_zip_code_distance"]);

											if (($this->search_criteria["by_country"]) && ($this->search_criteria["by_country"] != "none"))
												$this->search_page_results .="&b[by_country]=".urlencode($this->search_criteria["by_country"]);
											if ($this->search_criteria["subcategories_also"])
												$this->search_page_results .="&b[subcategories_also]=1";
											if (($this->search_criteria["by_state"]) && ($this->search_criteria["by_state"] != "none"))
												$this->search_page_results .="&b[by_state]=".urlencode($this->search_criteria["by_state"]);

											// Put in optional fields
											for($f = 1; $f < 35; $f++)
											{
												if (($this->search_criteria["optional_field_".$f]) && (strlen(trim($this->search_criteria["optional_field_".$f])) > 0))
													$this->search_page_results .="&b[optional_field_".$f."]=".urlencode($this->search_criteria["optional_field_".$f]);
												if ($this->search_criteria["by_optional_".$f."_higher"])
													$this->search_page_results .="&b[by_optional_".$f."_higher]=".urlencode($this->search_criteria["by_optional_".$f."_higher"]);
												if ($this->search_criteria["by_optional_".$f."_lower"])
													$this->search_page_results .="&b[by_optional_".$f."_lower]=".urlencode($this->search_criteria["by_optional_".$f."_lower"]);
											}

											$this->search_page_results .="&b[page]=".$page_link;
											$this->search_page_results .=" class=search_page_instructions>[".$page_link."]</a> ";
										}
									}
									$this->search_page_results .="..";
								}
							}else
							{
								if ($page <= $number_of_page_results)
								{
									if (($this->search_criteria["page"] == $page) || (($this->search_criteria["page"] == 0) && ($page == 1)))
									{
										$this->search_page_results .=" <font class=search_page_instructions>&lt;".$page."&gt;</font> ";
									}
									else
									{
										$this->search_page_results .="<a href=".$this->configuration_data['classifieds_file_name']."?a=19";
										if ($this->search_criteria["search_titles"])
											$this->search_page_results .="&b[search_titles]=1";
										if ($this->search_criteria["search_descriptions"])
											$this->search_page_results .="&b[search_descriptions]=1";
										if (strlen(trim($this->original_search_term)) > 0)
											$this->search_page_results .="&b[search_text]=".urlencode($this->original_search_term);
										if ($this->browse_type)
											$this->search_page_results .="&order=".$this->browse_type;
										if ($this->site_category)
										{
											$this->search_page_results .="&c=".$this->site_category;
											if ($this->search_criteria["question_value"])
											{
												foreach($this->search_criteria["question_value"] as $key => $value)
												{
													if (strlen(trim($value)) > 0)
														$this->search_page_results .="&b[question_value][".$key."]=".urlencode($value);
												}
											}
										}

										if ($this->search_criteria["by_business_type"])
											$this->search_page_results .="&b[by_business_type]=".urlencode($this->search_criteria["by_business_type"]);
										if ($this->search_criteria["by_price_lower"])
											$this->search_page_results .="&b[by_price_lower]=".urlencode($this->search_criteria["by_price_lower"]);
										if ($this->search_criteria["by_price_higher"])
											$this->search_page_results .="&b[by_price_higher]=".urlencode($this->search_criteria["by_price_higher"]);
										if ($this->search_criteria["by_zip_code"])
											$this->search_page_results .="&b[by_zip_code]=".urlencode($this->search_criteria["by_zip_code"]);
										if ($this->search_criteria["by_zip_code_distance"])
											$this->search_page_results .="&b[by_zip_code_distance]=".urlencode($this->search_criteria["by_zip_code_distance"]);

										if (($this->search_criteria["by_country"]) && ($this->search_criteria["by_country"] != "none"))
											$this->search_page_results .="&b[by_country]=".urlencode($this->search_criteria["by_country"]);
										if ($this->search_criteria["subcategories_also"])
											$this->search_page_results .="&b[subcategories_also]=1";
										if (($this->search_criteria["by_state"]) && ($this->search_criteria["by_state"] != "none"))
											$this->search_page_results .="&b[by_state]=".urlencode($this->search_criteria["by_state"]);

										// Put in optional fields
										for($f = 1; $f < 35; $f++)
										{
											if ($this->search_criteria["optional_field_".$f])
												$this->search_page_results .="&b[optional_field_".$f."]=".urlencode($this->search_criteria["optional_field_".$f]);
											if ($this->search_criteria["by_optional_".$f."_higher"])
												$this->search_page_results .="&b[by_optional_".$f."_higher]=".urlencode($this->search_criteria["by_optional_".$f."_higher"]);
											if ($this->search_criteria["by_optional_".$f."_lower"])
												$this->search_page_results .="&b[by_optional_".$f."_lower]=".urlencode($this->search_criteria["by_optional_".$f."_lower"]);
										}

										$this->search_page_results .="&b[page]=".$page;
										$this->search_page_results .=" class=search_page_instructions>[".$page."]</a> ";
									}
								}
							}
							$this->search_page_results .="..";
						}
					}
					else
					{
						//display the link to the section
						$this->search_page_results .="<a href=".$this->configuration_data['classifieds_file_name']."?a=19";
						if ($this->search_criteria["search_titles"])
							$this->search_page_results .="&b[search_titles]=1";
						if ($this->search_criteria["search_descriptions"])
							$this->search_page_results .="&b[search_descriptions]=1";
						if (strlen(trim($this->original_search_term)) > 0)
							$this->search_page_results .="&b[search_text]=".urlencode($this->original_search_term);
						if ($this->browse_type)
							$this->search_page_results .="&order=".$this->browse_type;
						if ($this->site_category)
						{
							$this->search_page_results .="&c=".$this->site_category;
							if ($this->search_criteria["question_value"])
							{
								foreach($this->search_criteria["question_value"] as $key => $value)
								{
									if (strlen(trim($value)) > 0)
										$this->search_page_results .="&b[question_value][".$key."]=".urlencode($value);
								}
							}
						}

						if ($this->search_criteria["by_business_type"])
						$this->search_page_results .="&b[by_business_type]=".urlencode($this->search_criteria["by_business_type"]);
						if ($this->search_criteria["by_price_lower"])
						$this->search_page_results .="&b[by_price_lower]=".urlencode($this->search_criteria["by_price_lower"]);
						if ($this->search_criteria["by_price_higher"])
						$this->search_page_results .="&b[by_price_higher]=".urlencode($this->search_criteria["by_price_higher"]);
						if ($this->search_criteria["by_zip_code"])
						$this->search_page_results .="&b[by_zip_code]=".urlencode($this->search_criteria["by_zip_code"]);
						if ($this->search_criteria["by_zip_code_distance"])
						$this->search_page_results .="&b[by_zip_code_distance]=".urlencode($this->search_criteria["by_zip_code_distance"]);
						if (($this->search_criteria["by_country"]) && ($this->search_criteria["by_country"] != "none"))
						$this->search_page_results .="&b[by_country]=".urlencode($this->search_criteria["by_country"]);
						if ($this->search_criteria["subcategories_also"])
						$this->search_page_results .="&b[subcategories_also]=1";
						if (($this->search_criteria["by_state"]) && ($this->search_criteria["by_state"] != "none"))
						$this->search_page_results .="&b[by_state]=".urlencode($this->search_criteria["by_state"]);

						// Put in optional fields
						for($f = 1; $f < 35; $f++)
						{
							if (($this->search_criteria["optional_field_".$f]) && (strlen(trim($this->search_criteria["optional_field_".$f])) > 0))
								$this->search_page_results .="&b[optional_field_".$f."]=".urlencode($this->search_criteria["optional_field_".$f]);
							if ($this->search_criteria["by_optional_".$f."_higher"])
								$this->search_page_results .="&b[by_optional_".$f."_higher]=".urlencode($this->search_criteria["by_optional_".$f."_higher"]);
							if ($this->search_criteria["by_optional_".$f."_lower"])
								$this->search_page_results .="&b[by_optional_".$f."_lower]=".urlencode($this->search_criteria["by_optional_".$f."_lower"]);
						}

						$this->search_page_results .="&b[page]=".(($section*100)+1);
						$this->search_page_results .=" class=search_page_instructions>[".(($section*100)+1)."]</a> ";
					}
					if (($section+1) < $number_of_sections)
					$this->search_page_results .="..";
				}
			}
			$this->search_page_results .="</td>\n</tr>\n";
		}
		$this->body .= $this->search_page_results;

		if ($this->total_returned > 0)
		{
			//$this->body .="<tr>\n\t<td>\n\t<table width=100% cellpadding=2 cellspacing=1 border=0>\n\t";
			//if ($this->configuration_data['display_photo_icon'])
			//{
			//	$this->body .="<tr class=search_page_results_title_row>\n\t<td>\n\t".urldecode($this->messages[389])."\n\t</td>\n\t";
			//}
			//$this->body .="<td>\n\t".urldecode($this->messages[306])."\n\t</td>\n\t";
			//if ($this->configuration_data['display_ad_description'])
			//{
			//	$this->body .="<td>\n\t".urldecode($this->messages[390])."\n\t</td>\n\t";
			//}
			//if ($this->configuration_data['display_entry_date'])
			//{
			//	$this->body .="<td>\n\t".urldecode($this->messages[391])."\n\t</td>\n";
			//}
			//$this->body .="</tr>\n";

			//display the results of the search
			//while ($show_classifieds = $result->FetchNextObject())
			///{
			//	$this->body .="<tr class=". $this->get_row_color(3).">\n\t\t";
			//	if ($this->configuration_data['display_photo_icon'])
			//	{
			//		if ($show_classifieds->IMAGE == 1)
			//			$this->body .="<td valign=top align=center><img src=".$this->configuration_data['photo_icon_url']."></td>\n\t";
			//		else
			//			$this->body .="<td valign=top>"."&nbsp;</td>\n\t";
			//	}
			//	$this->body .="<td valign=top>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds->ID.">".stripslashes(urldecode($show_classifieds->TITLE))."</a>\n\t\t</td>\n\t\t";
			//
			//	if ($this->configuration_data['display_ad_description'])
			///	{
			//		$this->body .="<td valign=top>";
			//		if (!$this->configuration_data['display_all_of_description'])
			//		{
			//			if (strlen(urldecode($show_classifieds->DESCRIPTION)) > $this->configuration_data['length_of_description'])
			//			{
			//				$small_string = substr(trim(stripslashes(urldecode($show_classifieds->DESCRIPTION))),0,$this->configuration_data['length_of_description']);
			//				$position = strrpos($small_string," ");
			//				$smaller_string = substr($small_string,0,$position);
			//				$this->body .=stripslashes($smaller_string."...");
			//			}
			//			else
			//				echo	stripslashes(urldecode($show_classifieds->DESCRIPTION));
			//		}
			//		else
			//			echo	stripslashes(urldecode($show_classifieds->DESCRIPTION));
			//		$this->body .="</td>\n\t";
			//	}
			//	if ($this->configuration_data['display_entry_date'])
			//	{
			//		$this->body .="<td valign=top>".date("F d-G:i",$show_classifieds->DATE)."</td>\n\t";
			//	}
			//	$this->body .="</tr>\n\t";
			//	$this->row_count++;
			//} //end of while

			if (($this->get_category_configuration($db,$this->site_category,1)) && ($this->site_category))
			{
				if (!$this->category_configuration->USE_SITE_DEFAULT)
				{
					$this->browsing_configuration = $this->configuration_data;
					if ($this->debug_search) echo "using site defaults<br>\n";
				}
				else
				{
					$this->browsing_configuration = $this->category_configuration;
					if ($this->debug_search) echo "using category specific settings<br>\n";
				}
			}
			else
			{
				if ($this->debug_search) echo "no category configuration<br>\n";
				$this->browsing_configuration = $this->configuration_data;
			}

			// Build up $_REQUEST[b] just in case it is needed
			$b_variable = "";

			//add category first if selected
			if (strlen(trim($_REQUEST["c"])) > 0)
			{
				$b_variable .= "&c=".$this->site_category;

			}
			foreach($_REQUEST["b"] as $key => $value)
			{
				if(is_array($value))
				{
					foreach($value as $new_key => $new_value)
					{
						if (strlen(trim($new_value)) > 0)
							$b_variable .= "&b[".$key."][".$new_key."]=".urlencode($new_value);
					}
				}
				else
				{
					if (strlen(trim($value)) > 0)
						$b_variable .= "&b[".$key."]=".urlencode($value);

				}
			}

			$this->body .="<tr>\n\t<td  height=20 width=100%>\n\t";
			$this->body .="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n\t";
			$this->body .="<tr class=search_page_results_title_row>\n\t\t";

			if ($this->is_class_auctions())
			{
				//display the auction or classified column header
				if ($this->browse_type == 65)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=66\" class=search_page_results_title_row>".urldecode($this->messages[200024])."</a></td>\n\t";
				elseif ($this->browse_type == 66)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[200024])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=65\" class=search_page_results_title_row>".urldecode($this->messages[200024])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_business_type"])
				$this->body .="<td nowrap>".urldecode($this->messages[1262])."</td>\n\t";

			if ($this->browsing_configuration["display_photo_icon"])
				$this->body .="<td nowrap>".urldecode($this->messages[594])."</td>\n\t";

			if ($this->browsing_configuration["display_ad_title"])
			{
				$this->body .="<td";
				if ((($this->browsing_configuration['display_ad_description'])&& ($this->configuration_data['display_ad_description_where'])) || (!$this->browsing_configuration['display_ad_description']))
					$this->body .= " width=100%";
				if($this->browse_type == 5)
					$this->body .="><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=6\" class=search_page_results_title_row>".urldecode($this->messages[595])."</a>";
				elseif($this->browse_type == 6)
					$this->body .="><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[595])."</a>";
				else
					$this->body .="><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=5\" class=search_page_results_title_row>".urldecode($this->messages[595])."</a>";

				if (($this->browsing_configuration["display_ad_description"]) && ($this->configuration_data['display_ad_description_where']))
					$this->body .="<br>".urldecode($this->messages[596]);
				$this->body .="</td>\n\t\t";
			}

			if (($this->browsing_configuration["display_ad_description"]) && (!$this->configuration_data['display_ad_description_where']))
				$this->body .="<td>".urldecode($this->messages[596])."</td>\n\t";

			if ($this->browsing_configuration["display_optional_field_1"])
			{
				if($this->browse_type == 15)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=16\" class=search_page_results_title_row>".urldecode($this->messages[1443])."</a></td>\n\t";
				elseif($this->browse_type == 16)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1443])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=15\" class=search_page_results_title_row>".urldecode($this->messages[1443])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_2"])
			{
				if($this->browse_type == 17)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=18\" class=search_page_results_title_row>".urldecode($this->messages[1444])."</a></td>\n\t";
				elseif($this->browse_type == 18)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1444])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=17\" class=search_page_results_title_row>".urldecode($this->messages[1444])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_3"])
			{
				if($this->browse_type == 19)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=20\" class=search_page_results_title_row>".urldecode($this->messages[1445])."</a></td>\n\t";
				elseif($this->browse_type == 20)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1445])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=19\" class=search_page_results_title_row>".urldecode($this->messages[1445])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_4"])
			{
				if($this->browse_type == 21)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=22\" class=search_page_results_title_row>".urldecode($this->messages[1446])."</a></td>\n\t";
				elseif($this->browse_type == 22)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1446])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=21\" class=search_page_results_title_row>".urldecode($this->messages[1446])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_5"])
			{
				if($this->browse_type == 23)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=24\" class=search_page_results_title_row>".urldecode($this->messages[1447])."</a></td>\n\t";
				elseif($this->browse_type == 24)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1447])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=23\" class=search_page_results_title_row>".urldecode($this->messages[1447])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_6"])
			{
				if($this->browse_type == 25)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=26\" class=search_page_results_title_row>".urldecode($this->messages[1448])."</a></td>\n\t";
				elseif($this->browse_type == 26)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1448])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=25\" class=search_page_results_title_row>".urldecode($this->messages[1448])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_7"])
			{
				if($this->browse_type == 27)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=28\" class=search_page_results_title_row>".urldecode($this->messages[1449])."</a></td>\n\t";
				elseif($this->browse_type == 28)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1449])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=27\" class=search_page_results_title_row>".urldecode($this->messages[1449])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_8"])
			{
				if($this->browse_type == 29)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=30\" class=search_page_results_title_row>".urldecode($this->messages[1450])."</a></td>\n\t";
				elseif($this->browse_type == 30)
					$this->body .="<td nowrap nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1450])."</a></td>\n\t";
				else
					$this->body .="<td><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=29\" class=search_page_results_title_row>".urldecode($this->messages[1450])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_9"])
			{
				if($this->browse_type == 31)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=32\" class=search_page_results_title_row>".urldecode($this->messages[1451])."</a></td>\n\t";
				elseif($this->browse_type == 32)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1451])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=31\" class=search_page_results_title_row>".urldecode($this->messages[1451])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_10"])
			{
				if($this->browse_type == 33)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=34\" class=search_page_results_title_row>".urldecode($this->messages[1452])."</a></td>\n\t";
				elseif($this->browse_type == 34)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1452])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=33\" class=search_page_results_title_row>".urldecode($this->messages[1452])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_11"])
			{
				if($this->browse_type == 45)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=46\" class=search_page_results_title_row>".urldecode($this->messages[1923])."</a></td>\n\t";
				elseif($this->browse_type == 46)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1923])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=45\" class=search_page_results_title_row>".urldecode($this->messages[1923])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_12"])
			{
				if($this->browse_type == 47)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=48\" class=search_page_results_title_row>".urldecode($this->messages[1924])."</a></td>\n\t";
				elseif($this->browse_type == 48)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1924])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=47\" class=search_page_results_title_row>".urldecode($this->messages[1924])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_13"])
			{
				if($this->browse_type == 49)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=50\" class=search_page_results_title_row>".urldecode($this->messages[1925])."</a></td>\n\t";
				elseif($this->browse_type == 50)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1925])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=49\" class=search_page_results_title_row>".urldecode($this->messages[1925])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_14"])
			{
				if($this->browse_type == 51)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=52\" class=search_page_results_title_row>".urldecode($this->messages[1926])."</a></td>\n\t";
				elseif($this->browse_type == 52)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1926])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=51\" class=search_page_results_title_row>".urldecode($this->messages[1926])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_15"])
			{
				if($this->browse_type == 53)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=54\" class=search_page_results_title_row>".urldecode($this->messages[1927])."</a></td>\n\t";
				elseif($this->browse_type == 54)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1927])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=53\" class=search_page_results_title_row>".urldecode($this->messages[1927])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_16"])
			{
				if($this->browse_type == 55)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=56\" class=search_page_results_title_row>".urldecode($this->messages[1928])."</a></td>\n\t";
				elseif($this->browse_type == 56)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1928])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=55\" class=search_page_results_title_row>".urldecode($this->messages[1928])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_17"])
			{
				if($this->browse_type == 57)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=58\" class=search_page_results_title_row>".urldecode($this->messages[1929])."</a></td>\n\t";
				elseif($this->browse_type == 58)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1929])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=57\" class=search_page_results_title_row>".urldecode($this->messages[1929])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_18"])
			{
				if($this->browse_type == 59)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=60\" class=search_page_results_title_row>".urldecode($this->messages[1930])."</a></td>\n\t";
				elseif($this->browse_type == 60)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1930])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=59\" class=search_page_results_title_row>".urldecode($this->messages[1930])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_19"])
			{
				if($this->browse_type == 61)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=62\" class=search_page_results_title_row>".urldecode($this->messages[1931])."</a></td>\n\t";
				elseif($this->browse_type == 62)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1931])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=61\" class=search_page_results_title_row>".urldecode($this->messages[1931])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_optional_field_20"])
			{
				if($this->browse_type == 63)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=64\" class=search_page_results_title_row>".urldecode($this->messages[1932])."</a></td>\n\t";
				elseif($this->browse_type == 64)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1932])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=63\" class=search_page_results_title_row>".urldecode($this->messages[1932])."</a></td>\n\t";
			}

			if ($this->browsing_configuration["display_browsing_city_field"])
			{
				if($this->browse_type == 35)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=36\" class=search_page_results_title_row>".urldecode($this->messages[1453])."</a></td>\n\t";
				elseif($this->browse_type == 36)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1453])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=35\" class=search_page_results_title_row>".urldecode($this->messages[1453])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_browsing_state_field"])
			{
				if($this->browse_type == 37)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=38\" class=search_page_results_title_row>".urldecode($this->messages[1454])."</a></td>\n\t";
				elseif($this->browse_type == 38)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1454])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=37\" class=search_page_results_title_row>".urldecode($this->messages[1454])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_browsing_country_field"])
			{
				if($this->browse_type == 39)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=40\" class=search_page_results_title_row>".urldecode($this->messages[1455])."</a></td>\n\t";
				elseif($this->browse_type == 40)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1455])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=39\" class=search_page_results_title_row>".urldecode($this->messages[1455])."</a></td>\n\t";
			}
			if ($this->browsing_configuration["display_browsing_zip_field"])
			{
				if($this->browse_type == 41)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=42\" class=search_page_results_title_row>".urldecode($this->messages[1456])."</a></td>\n\t";
				elseif($this->browse_type == 42)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[1456])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=41\" class=search_page_results_title_row>".urldecode($this->messages[1456])."</a></td>\n\t";
			}

			if ($this->browsing_configuration["display_price"])
			{
				if($this->browse_type == 2)
					$this->body .="<td nowrap ><div nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=1\" class=search_page_results_title_row>".urldecode($this->messages[597])."</a></div></td>\n\t";
				elseif($this->browse_type == 1)
					$this->body .="<td nowrap ><div nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[597])."</a></div></td>\n\t";
				else
					$this->body .="<td nowrap ><div nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=2\" class=search_page_results_title_row>".urldecode($this->messages[597])."</a></div></td>\n\t";
			}

			if ($this->browsing_configuration["display_entry_date"])
			{
				if($this->browse_type == 3)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=4\" class=search_page_results_title_row>".urldecode($this->messages[598])."</a></td>\n\t";
				elseif($this->browse_type == 4)
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=0\" class=search_page_results_title_row>".urldecode($this->messages[598])."</a></td>\n\t";
				else
					$this->body .="<td nowrap><a href=\"".$this->configuration_data['classifieds_file_name']."?a=19".$b_variable."&order=3\" class=search_page_results_title_row>".urldecode($this->messages[598])."</a></td>\n\t";
			}

			if (($this->search_criteria["by_zip_code"]) && ($this->search_criteria["by_zip_code_distance"]) && ($this->configuration_data['use_zip_distance_calculator']))
				$this->body .= "<td  nowrap>".urldecode($this->messages[1948])."</td>\n\t";

			if ($this->classified_user_id == 1)
			{
				//this is the admin
				$this->body .="<td>edit</td>\n\t";
				$this->body .="<td>delete</td>\n\t";
			}

			$this->body .="</tr>\n\t";
			$this->row_count = 0;
			while ($show_classifieds = $result->FetchNextObject())
			{
				if (($this->row_count % 2) == 0)
					$css_class_tag = "search_page_results_even_row";
				else
					$css_class_tag = "search_page_results_odd_row";
				$this->body .="<tr class=".$css_class_tag.">\n\t\t";

				if ($this->is_class_auctions())
				{
					$this->body .="<td nowrap>";
					if ($show_classifieds->ITEM_TYPE == 1)
						$this->body .= urldecode($this->messages[200026]);
					elseif ($show_classifieds->ITEM_TYPE == 2)
						$this->body .= urldecode($this->messages[200025]);
					$this->body .= "</td>\n\t";
				}

				if ($this->browsing_configuration["display_business_type"])
				{
					$this->body .="<td nowrap>";
					if ($show_classifieds->BUSINESS_TYPE == 1)
						$this->body .= urldecode($this->messages[1263]);
					elseif ($show_classifieds->BUSINESS_TYPE == 2)
						$this->body .= urldecode($this->messages[1263]);
					else
						$this->body .= "&nbsp;";
					$this->body .= "</td>\n\t";
				}

				if ($this->browsing_configuration["display_photo_icon"])
				{
					if ($this->configuration_data['photo_or_icon'] == 1)
					{
						if ($show_classifieds->IMAGE > 0)
						{
							if ($featured)
								$this->display_thumbnail($db,$show_classifieds->ID,$this->configuration_data['featured_thumbnail_max_width'],$this->configuration_data['featured_thumbnail_max_height']);
							else
								$this->display_thumbnail($db,$show_classifieds->ID);
						}
						elseif (($this->configuration_data['no_image_url']) && (!$show_classifieds->IMAGE))
						{
							if (($this->configuration_data['popup_while_browsing'])
								&& ($this->configuration_data['popup_while_browsing_width'])
								&& ($this->configuration_data['popup_while_browsing_height']))
							{
								$this->body .= "<td align=center><a href=\"";
								$this->body .= $this->configuration_data['classifieds_file_name'];
								$this->body .= "?a=2&b=".$show_classifieds->ID."\" ";
								$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
							else
							{
								$this->body .="<td align=center><a href=".$this->configuration_data['classifieds_file_name'];
								$this->body .="?a=2&b=".$show_classifieds->ID.">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
						}
						else
							$this->body .="<td>&nbsp;</td>\n\t";
					}
					else
					{
						if ($show_classifieds->IMAGE > 0)
						{
							if (($this->configuration_data['popup_while_browsing'])
								&& ($this->configuration_data['popup_while_browsing_width'])
								&& ($this->configuration_data['popup_while_browsing_height']))
							{
								$this->body .= "<td><a href=\"";
								$this->body .= $this->configuration_data['classifieds_file_name'];
								$this->body .= "?a=2&b=".$show_classifieds->ID."\" ";
								$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
								$this->body .="<img src=".$this->configuration_data['photo_icon_url']." border=0></td>";
							}
							else
							{
								$this->body .="<td><a href=".$this->configuration_data['classifieds_file_name'];
								$this->body .="?a=2&b=".$show_classifieds->ID.">";
								$this->body .="<img src=".$this->configuration_data['photo_icon_url']." border=0></td>";
							}
						}
						elseif (($this->configuration_data['no_image_url']) && (!$show_classifieds->IMAGE))
						{
							if (($this->configuration_data['popup_while_browsing'])
								&& ($this->configuration_data['popup_while_browsing_width'])
								&& ($this->configuration_data['popup_while_browsing_height']))
							{
								$this->body .= "<td align=center><a href=\"";
								$this->body .= $this->configuration_data['classifieds_file_name'];
								$this->body .= "?a=2&b=".$show_classifieds->ID."\" ";
								$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
							else
							{
								$this->body .="<td align=center><a href=".$this->configuration_data['classifieds_file_name'];
								$this->body .="?a=2&b=".$show_classifieds->ID.">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
						}
						else
						$this->body .="<td>&nbsp;</td>\n\t";
					}
				}
				if ($this->browsing_configuration["display_ad_title"])
				{
					$this->body .="<td ";
					if ((($this->browsing_configuration['display_ad_description'])&& ($this->configuration_data['display_ad_description_where'])) || (!$this->browsing_configuration['display_ad_description']))
						$this->body .= "width=100%";
					$this->body .=">";
					if ($show_classifieds->SOLD_DISPLAYED)
						$this->body .= "<img src=".$this->configuration_data['sold_image']." border=0> ";
					$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds->ID." class=".$css_class_tag.">".stripslashes(urldecode($show_classifieds->TITLE))."</a>\n\t\t";
					if ($show_classifieds->ATTENTION_GETTER)
					{
						$this->body .= "<img src=\"".$show_classifieds->ATTENTION_GETTER_URL."\" border=0 hspace=2>";
					}
					if (($this->browsing_configuration["display_ad_description"]) && ($this->configuration_data['display_ad_description_where']))
					{
						$this->body .="<br>";
						if (strlen(urldecode($show_classifieds->DESCRIPTION)) > $this->configuration_data['length_of_description'])
						{
							$small_string = substr(trim(strip_tags(stripslashes(urldecode($show_classifieds->DESCRIPTION)))),0,$this->configuration_data['length_of_description']);
							$position = strrpos($small_string," ");
							$smaller_string = substr($small_string,0,$position);
							$this->body .=$smaller_string."...";
						}
						else
							$this->body .=	stripslashes(urldecode($show_classifieds->DESCRIPTION));
					}
					$this->body .="</td>\n\t\t";
				}

				if (($this->browsing_configuration["display_ad_description"]) && (!$this->configuration_data['display_ad_description_where']))
				{
					$this->body .="<td >";
					if (!$this->configuration_data['display_all_of_description'])
					{
						if (strlen(urldecode($show_classifieds->DESCRIPTION)) > $this->configuration_data['length_of_description'])
						{
							$small_string = substr(trim(strip_tags(stripslashes(urldecode($show_classifieds->DESCRIPTION)))),0,$this->configuration_data['length_of_description']);
							$position = strrpos($small_string," ");
							$smaller_string = substr($small_string,0,$position);
							$this->body .=$smaller_string."...";
						}
						else
						$this->body .=	stripslashes(urldecode($show_classifieds->DESCRIPTION));
					}
					else
						$this->body .=	stripslashes(urldecode($show_classifieds->DESCRIPTION));
					$this->body .="</font></td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_1"])
				{
					$this->body .="<td nowrap>";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_1))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_1));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_2"])
				{
					$this->body .="<td  nowrap>";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_2))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_2));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_3"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_3))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_3));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_4"])
				{
					$this->body .="<td  nowrap>";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_4))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_4));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_5"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_5))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_5));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_6"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_6))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_6));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_7"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_7))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_7));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_8"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_8))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_8));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_9"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_9))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_9));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_10"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_10))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_10));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_11"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_11))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_11));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_12"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_12))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_12));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_13"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_13))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_13));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_14"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_14))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_14));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_15"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_15))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_15));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_16"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_16))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_16));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_17"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_17))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_17));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_18"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_18))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_18));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_19"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_19))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_19));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_optional_field_20"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->OPTIONAL_FIELD_20))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->OPTIONAL_FIELD_20));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_browsing_city_field"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->LOCATION_CITY))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_CITY));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_browsing_state_field"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->LOCATION_STATE))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_STATE));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_browsing_country_field"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->LOCATION_COUNTRY))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_COUNTRY));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_browsing_zip_field"])
				{
					$this->body .="<td nowrap >";
					if (strlen(trim(urldecode($show_classifieds->LOCATION_ZIP))) > 0)
						$this->body .=stripslashes(urldecode($show_classifieds->LOCATION_ZIP));
					else
						$this->body .=	"-";
					$this->body .="</td>\n\t";
				}


				if ($this->browsing_configuration["display_price"])
				{
					$this->body .="<td nowrap>";
					if ($show_classifieds->ITEM_TYPE == 1)
					{
						if (((strlen(trim(urldecode($show_classifieds->PRICE))) > 0)
							|| (strlen(trim(urldecode($show_classifieds->PRECURRENCY))) > 0)
							|| (strlen(trim(urldecode($show_classifieds->POSTCURRENCY))) > 0))
							&& ($show_classifieds->PRICE != 0))
						{
							if (floor($show_classifieds->PRICE) == $show_classifieds->PRICE)
							{
								$this->body .= stripslashes(urldecode($show_classifieds->PRECURRENCY)). " ".
								number_format($show_classifieds->PRICE)." ".
								stripslashes(urldecode($show_classifieds->POSTCURRENCY));
							}
							else
							{
								$this->body .= stripslashes(urldecode($show_classifieds->PRECURRENCY)). " ".
								number_format($show_classifieds->PRICE,2,".",",")." ".
								stripslashes(urldecode($show_classifieds->POSTCURRENCY));
							}
						}
						else
							$this->body .=	"-";
					}
					elseif ($show_classifieds->ITEM_TYPE == 2)
					{
						if ($this->debug_search) echo $show_auctions->MINIMUM_BID." is m and ".$show_auctions->CURRENT_BID." is c<BR>\n";
						if ($show_classifieds->CURRENT_BID > 0)
							$this->body .= stripslashes(urldecode($show_classifieds->PRECURRENCY)). " ".$this->print_number($show_classifieds->CURRENT_BID)." ".stripslashes(urldecode($show_classifieds->POSTCURRENCY));
						else
							$this->body .= stripslashes(urldecode($show_classifieds->PRECURRENCY)). " ".$this->print_number($show_classifieds->MINIMUM_BID)." ".stripslashes(urldecode($show_classifieds->POSTCURRENCY));
					}
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration["display_entry_date"])
				{
					$this->body .="<td nowrap>".date("M d-G:i",$show_classifieds->DATE)."</td>\n\t";
				}
				if (($this->search_criteria["by_zip_code"]) && ($this->search_criteria["by_zip_code_distance"]) && ($this->configuration_data['use_zip_distance_calculator']))
				{
					//echo strtoupper($show_classifieds->LOCATION_ZIP)." is upper location zip<BR>\n";
					if ($this->configuration_data['use_zip_distance_calculator'] == 2)
					{
						//Using UK Postal Codes
						$this->body .= "<td nowrap>".sprintf("%01.2f",$this->uk_postcodes[strtoupper($show_classifieds->LOCATION_ZIP)])."</td>\n\t";
					}
					elseif ($this->configuration_data['use_zip_distance_calculator'] == 1)
					{
						//Using United States Zip Codes
						//get the distance from the original zip code
						$this->sql_query = "select latitude,longitude from ".$this->postal_code_table."
							where zipcode = ".$show_classifieds->LOCATION_ZIP." limit 1";
						$zip_distance_result =  $db->Execute($this->sql_query);
						if ($this->debug_search) echo $this->sql_query." is the query<br><br>\n";
						if (!$zip_distance_result)
						{
							if ($this->debug_search) echo $this->sql_query." is the query<br>\n";
							$this->error_message = $this->messages[2052];
							return false;
						}
						elseif ($zip_distance_result->RecordCount() == 1)
						{
							$zip_distance = $zip_distance_result->FetchNextObject();
							$distance = $this->calculate_distance_from_zip($zip_distance->LATITUDE,$zip_distance->LONGITUDE);
							$this->body .= "<td nowrap>".sprintf("%01.2f",$distance)."</td>\n\t";
						}
						else
						{
							$this->body .= "<td nowrap> - </td>\n\t";
						}
					}
					elseif ($this->configuration_data['use_zip_distance_calculator'] == 3)
					{
						//Using United States Zip Codes
						//get the distance from the original zip code

						//check to see if canadian zip code - canadian postal codes always start with a letter
						if (!ereg("^[0-9]{1,}$",trim($show_classifieds->LOCATION_ZIP)))
						{
							//this is a canadian postal code
							//get the first 3 characters from the zip code entered and search the database
							$this->canadian_zip = 1;

							if ($this->debug_search) echo "the first character of the zip code is a letter<BR>\n";
							$use_zip_code = substr($show_classifieds->LOCATION_ZIP, 0, 3 );
						}
						else
						{
							$this->canadian_zip = 0;
							$use_zip_code = $show_classifieds->LOCATION_ZIP;
						}

						$this->sql_query = "select latitude,longitude
							from ".$this->postal_code_table." where zipcode = '".$use_zip_code."' limit 1";
						$zip_distance_result =  $db->Execute($this->sql_query);
						if ($this->debug_search) echo $this->sql_query." is the query in 3<br><br>\n";
						if (!$zip_distance_result)
						{
							if ($this->debug_search) echo $this->sql_query." is the query<br>\n";
							$this->error_message = $this->messages[2052];
							return false;
						}
						elseif ($zip_distance_result->RecordCount() == 1)
						{
							$zip_distance = $zip_distance_result->FetchNextObject();

							$distance = $this->calculate_distance_from_zip($zip_distance->LATITUDE,$zip_distance->LONGITUDE);
							$this->body .= "<td nowrap>".sprintf("%01.2f",$distance)."</td>\n\t";

							if ($this->debug_search)
							{
								echo $show_classifieds->LOCATION_ZIP." is the zip for this ad: ".$show_classifieds->ID."<bR>\n";
								echo $zip_distance->LATITUDE." is latitude<BR>\n";
								echo $zip_distance->LONGITUDE." is longitude<BR>\n";
								echo $this->search_zip_latitude." is search latitude<BR>\n";
								echo $this->search_zip_longitude." is search longitude<BR>\n";
								echo $distance." is the distance between<BR>\n";
							}

						}
						else
						{
							$this->body .= "<td nowrap> - </td>\n\t";
						}
					}
				}
				if ($this->classified_user_id == 1)
				{
					//this is the admin
					$this->body .="<td >\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&e=".$show_classifieds->ID."&b=5>edit</a>\n\t\t</td>\n\t";
					$this->body .="<td >\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=99&b=".$show_classifieds->ID."&c=".$category.">delete</a>\n\t\t</td>\n\t";

				}
				$this->body .="</tr>\n\t";
				$this->row_count++;
			} //end of while
			$this->body .="</table>\n\t</td>\n</tr>\n";

		}
		else
		{
			//no results to display
			$this->body .="<tr class=search_page_instructions>\n\t<td colspan=4>\n\t".urldecode($this->messages[592])."\n\t</td>\n</tr>\n";
		}
		//$this->body .="</table>\n\t</td>\n</tr>\n";

		if ($this->configuration_data['number_of_ads_to_display'] < $this->total_returned)
		$this->body .= $this->search_page_results;

		$this->body .="<tr class=search_page_bottom_links>\n\t<td>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=19 class=start_new_search_link>".urldecode($this->messages[303])."</a>\n\t</td>\n</tr>\n";
		$this->body .="<tr>\n\t<td colspan=2 class=start_new_search_link>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=19 class=start_new_search_link>".urldecode($this->messages[591])."</a>\n\t</td>\n</tr>\n";
		$this->body .="</table>\n";
		$this->body .="</form>\n";

		$this->display_page($db);
	} // end function BuildResults

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function CountOptionalFields($db)
	{
		$count = 0;

		$this->sql_query = "select * from ".$this->site_configuration_table." limit 1";
		$result = $db->Execute($this->sql_query);
		if(!$result)
		{
			return false;
		}
		$row = $result->FetchNextObject();
		$variable = get_object_vars($row);
		$keys = array_keys($variable);

		for($i = 1; in_array("USE_OPTIONAL_FIELD_".$i, $keys); $i++)
		{
			$count++;
		}

		$this->optional_fields = $count;

		//echo $this->optional_fields.' is the number of optional fields<br>';
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function test_titles_and_descriptions ($term)
	{
		if ($this->debug_search)
		{
			echo $term." is term at the top of test_titles_and_descriptions<br>\n";
			echo $this->where_clause." is the where clause at top of test_titles_and_descriptions<BR>\n";
		}

		//$term = urlencode($term);
		if ($this->search_criteria["whole_word"] == 2)
		{
			$this->where_clause .= "(id = ".$term.")";
		}
		else
		{
			if ($this->search_criteria["search_titles"])
			{
				if ($this->search_criteria["whole_word"])
				{
					$this->where_clause .= "((title like \"%+".$term."+%\") or (title like \"%+".$term."\") or (title like \"".$term."+%\")) ";
					if (ereg("^[0-9]{1,10}$", trim($term)))
						$this->where_clause .= "or (id = ".$term.")";
					//$this->where_clause .= ")";
				}
				else
				{
					$this->where_clause .= "(title like \"%".$term."%\")";
					if (ereg("^[0-9]{1,10}$", trim($term)))
						$this->where_clause .= "or (id = ".$term.")";
					//$this->where_clause .= ")";
				}

				$this->started = 1;
				if ($this->search_criteria["search_descriptions"])
				{
					$this->where_clause .= " or ";
					if ($this->search_criteria["whole_word"])
					{
						$this->where_clause .= "((description like \"%+".$term."+%\") or (search_text like \"%+".$term."+%\") or (description like \"%+".$term."\") or (search_text like \"%+".$term."\") or (description like \"".$term."+%\") or (search_text like \"".$term."+%\"))";
					}
					else
						$this->where_clause .= "((description like \"%".$term."%\") or (search_text like \"%".$term."%\"))";
				}

			}
			elseif ($this->search_criteria["search_descriptions"])
			{
				if ($this->search_criteria["whole_word"])
					$this->where_clause .= "((description like \"%+".$term."+%\") or (search_text like \"%+".$term."+%\") or (description like \"%+".$term."\") or (search_text like \"%+".$term."\") or (description like \"".$term."+%\") or (search_text like \"".$term."+%\")) or id = ".$term.")";
				else
					$this->where_clause .= "((description like \"%".$term."%\") or (search_text like \"%".$term."%\")) or id = ".$term.")";
				$this->started = 1;
			}
			else
			{
				$this->where_clause .= "(search_text like \"%".$term."%\")";
			}
		}
		if ($this->debug_search)
		{
			echo $term." is term at the bottom of test_titles_and_descriptions<br>\n";
			echo $this->where_clause." is the where clause at the end of test_titles_and_descriptions<BR>\n";
		}
		return true;

	} //end of function test_titles_and_descriptions

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function RadiusAssistant($Latitude, $Longitude, $Miles)
	{

		$EQUATOR_LAT_MILE = 69.172;
		$this->max_latitude = $Latitude + $Miles / $EQUATOR_LAT_MILE;
		$this->min_latitude = $Latitude - ($this->max_latitude - $Latitude);
		$this->max_longitude = $Longitude + $Miles / (cos($this->min_latitude * M_PI / 180) * $EQUATOR_LAT_MILE);
		$this->min_longitude = $Longitude - ($this->max_longitude - $Longitude);
	} //end of function RadiusAssistant

//##################################################################################

	function calculate_distance_from_zip($dblLat2,$dblLong2)
	{
		$EARTH_RADIUS_MILES = 3963;
		$dist = 0;
		//convert degrees to radians
		$current_latitude = $this->search_zip_latitude * M_PI / 180;
		$current_longitude = $this->search_zip_longitude * M_PI / 180;
		$dblLat2 = $dblLat2 * M_PI / 180;
		$dblLong2 = $dblLong2 * M_PI / 180;
		if ($current_latitude != $dblLat2 || $current_longitude != $dblLong2)
		{
			//the two points are not the same
			$dist =
				sin($current_latitude) * sin($dblLat2)
				+ cos($current_latitude) * cos($dblLat2)
				* cos($dblLong2 - $current_longitude);

			$dist =
				$EARTH_RADIUS_MILES
				* (-1 * atan($dist / sqrt(1 - $dist * $dist)) + M_PI / 2);
		}
		return $dist;
	} //end of function Calculate_distance_from_zip

//##################################################################################

	function filter_select($db,$filter_id=0)
	{
		//check current temp filter
		//see if there are subfilters
		if ($filter_id)
		{
			$this->sql_query = "select ".$this->filters_table.".filter_id, ".$this->filters_table.".filter_level, ".$this->filters_table.".parent_id,".$this->filters_languages_table.".filter_name
				from ".$this->filters_table.",".$this->filters_languages_table."
				where ".$this->filters_table.".filter_id = ".$this->filters_languages_table.".filter_id
				and ".$this->filters_languages_table.".language_id = ".$this->language_id."
				and ".$this->filters_table.".parent_id = ".$filter_id." order by display_order asc,".$this->filters_languages_table.".filter_name";
		}
		else
			$this->sql_query = "select ".$this->filters_table.".filter_id, ".$this->filters_table.".filter_level, ".$this->filters_table.".parent_id,".$this->filters_languages_table.".filter_name
				from ".$this->filters_table.",".$this->filters_languages_table."
				where ".$this->filters_table.".filter_id = ".$this->filters_languages_table.".filter_id
				and ".$this->filters_languages_table.".language_id = ".$this->language_id."
				and filter_level = 1 order by display_order asc,".$this->filters_languages_table.".filter_name";
		$sub_filter_result = $db->Execute($this->sql_query);
		if ($this->debug_register) echo $this->sql_query."<br>\n";
		if (!$sub_filter_result)
		{
			if ($this->debug_register) echo $this->sql_query."<br>\n";
			$this->error_message = $this->messages[105501];
			return false;
		}
		elseif ($sub_filter_result->RecordCount() > 0)
		{
			$this->page_id = 10092;
		//	echo "get_text inside filter_select function <bR>";
			$this->get_text($db);
			//display the form top
			$this->body .= "<form action=";
			if ($this->configuration_data['use_ssl_in_registration'])
				$this->body .= trim($this->configuration_data['registration_ssl_url']);
			else
				$this->body .=  trim($this->configuration_data['registration_url']);
			$this->body .= " method=post>\n";
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n\t";
			$this->body .="<tr class=section_title>\n\t\t<td>".urldecode($this->messages[101502])."</td>\n\t</tr>\n\t";
			$this->body .="<tr class=page_title>\n\t\t<td>".urldecode($this->messages[101503])."</td>\n\t</tr>\n\t";
			$this->body .="<tr class=page_description>\n\t\t<td>".urldecode($this->messages[101504])."</td>\n\t</tr>\n\t";
			$this->body .="<tr class=filter_selection>\n\t\t<td>";
			//get the parent filters to this one
			$show_level = $sub_filter_result->FetchNextObject();
			if ($show_level->PARENT_ID != 0)
			{
				//show the parent levels
				$filter_tree = $this->get_filter_level($db,$show_level->PARENT_ID);
				reset ($this->filter_level_array);
				if ($filter_tree)
				{
					foreach ($this->filter_level_array as $key => $value)
						$this->body .= $this->filter_level_array[$key]["filter_name"]." > ";
				}
			}
			$sub_filter_result->Move(0);

			//show the form to select filter
			$this->body .= "<select name=registration_filter_id class=filter_dropdown onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\">\n\t\t";
			$this->body .= "<option value=\"\">".urldecode($this->messages[101505])."</option>\n\t\t";
			while ($show_filter = $sub_filter_result->FetchNextObject())
			{
				$this->body .= "<option value=".$show_filter->FILTER_ID.">".$show_filter->FILTER_NAME."</option>\n\t\t";
			}
			$this->body .= "</select>";
			//display the form bottom
			$this->body .= "</td></tr>";
			$this->body .= "</table></form>";
			$this->display_page($db);
			exit;
		}
		else
		{
			//this is the terminal filter...set it
			$this->update_filter_id($db,$filter_id);
			$this->registration_form_1($db);
		}

	} //end of function filter_select

//########################################################################

	function update_filter_id($db,$filter_id)
	{
		$this->registration_filter_id = $filter_id;
		$this->sql_query = "update ".$this->registration_table." set
			filter_id = ".$filter_id."
			where session=\"".$this->session_id."\"";
		$registration_filter_id_result = $db->Execute($this->sql_query);
		if ($this->debug_register) echo $this->sql_query."<br>\n";
		if (!$registration_filter_id_result)
		{
			if ($this->debug_register) echo $this->sql_query."<br>\n";
			return false;
		}
	} // end of update_filter_id

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_filter_value($db,$association=0)
	{
		if ($association)
		{
			//association is the filter level this value is associated with
			$this->sql_query = "select count(distinct(filter_level)) as level_count from ".$this->filters_table;
			$level_count_result = $db->Execute($this->sql_query);
			if ($this->debug_register) echo $this->sql_query."<br>\n";
			if (!$level_count_result)
			{
				if ($this->debug_register) echo $this->sql_query."<br>\n";
				$this->error_message = $this->messages[105501];
				return false;
			}
			elseif ($level_count_result->RecordCount() == 1)
			{
				$level_count = $level_count_result->FetchNextObject();
				if ($level_count->LEVEL_COUNT == $association)
				{
					//get current filter id filter name
					$this->sql_query = "select ".$this->filters_languages_table.".filter_name
						from ".$this->filters_languages_table."
						where ".$this->filters_languages_table.".language_id = ".$this->language_id."
						and ".$this->filters_languages_table.".filter_id = ".$this->registration_filter_id;
					$filter_result =  $db->Execute($this->sql_query);
					if ($this->debug_register) echo $this->sql_query."<br>\n";
					if (!$filter_result)
					{
						if ($this->debug_register) echo $this->sql_query."<br>\n";
						$this->error_message = $this->messages[103501];
						return false;
					}
					elseif ($filter_result->RecordCount() == 1)
					{
						$show_filter_name = $filter_result->FetchNextObject();
						return $show_filter_name->FILTER_NAME;
					}
					else
						return false;

				}
				else
				{
					$filter_name = $this->get_filter_level($db,$this->registration_filter_id,$association);
					return $filter_name;
				}
			}
			else
			{
				return false;
			}
		}
		else
			return false;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_filter_level($db,$filter=0,$level_result=0)
	{
		if ($filter)
		{
			$i = 0;
			$filter_next = $filter;
			do
			{
				$this->sql_query = "select ".$this->filters_table.".filter_id,".$this->filters_table.".parent_id,
					".$this->filters_languages_table.".filter_name, ".$this->filters_table.".filter_level
					from ".$this->filters_table.",".$this->filters_languages_table."
					where ".$this->filters_table.".filter_id = ".$this->filters_languages_table.".filter_id
					and ".$this->filters_languages_table.".language_id = ".$this->language_id."
					and ".$this->filters_table.".filter_id = ".$filter_next;
				$filter_result =  $db->Execute($this->sql_query);
				if ($this->debug_register) echo $this->sql_query."<br>\n";
				if (!$filter_result)
				{
					if ($this->debug_register) echo $this->sql_query."<br>\n";
					$this->error_message = $this->messages[103501];
					return false;
				}
				elseif ($filter_result->RecordCount() == 1)
				{
					$show_filter = $filter_result->FetchNextObject();
					$this->filter_level_array[$i]["parent_id"]  = $show_filter->PARENT_ID;
					$this->filter_level_array[$i]["filter_name"] = $show_filter->FILTER_NAME;
					$this->filter_level_array[$i]["filter_id"]   = $show_filter->FILTER_ID;
					$this->filter_level_array[$i]["filter_level"]   = $show_filter->FILTER_LEVEL;
					if (($level_result) && ($level_result == $show_filter->FILTER_LEVEL))
						return $show_filter->FILTER_NAME;
					$i++;
					$filter_next = $show_filter->PARENT_ID;
				}
				else
				{
					//echo "wrong return<Br>\n";
					return false;
				}

			} while ( $show_filter->PARENT_ID != 0 );

			return $i;
		}
		else
			return false;

	} // end of function get_filter_level

//########################################################################

} //end of class Search_classifieds
?>
