<?

class ajax {
	var $db;
	var $debug_ajax = 0;
	var $ad_configuration_data;
	var $configuration_data;
	
	function ajax(&$db) {
		$this->db = $db;
	}
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function getCategoryQuestions($category_id=0)
	{
		if ($this->debug_ajax)
		{
			echo "<BR>TOP OF GETCATEGORYQUESTIONS<BR>\n";
		}
		//get sell questions specific to this category
		if($category_id == 0)
		{
			if ($this->debug_ajax) 
				echo "no category_id<Br>\n";
			return false;
		}
		//get the questions for this category
		$query = "select name,choices,question_id from geodesic_classifieds_sell_questions where category_id = '$category_id' order by display_order";
		if ($this->debug_ajax) echo $query."<Br>\n";
		$result = $this->db->Execute($query) or die("An error has occurred".__LINE__);
		if (!$result)
		{
			if ($this->debug_ajax) echo $query."<Br>\n";
			return false;
		}
		if ($result->RecordCount() > 0)
		{
			while ($get_questions = $result->FetchNextObject())
			{
				$category["questions"][$get_questions->QUESTION_ID] = $get_questions->NAME;
				$category["choices"][$get_questions->QUESTION_ID] = $get_questions->CHOICES;
			}
		}
		else 
		{
			if ($this->debug_ajax) echo "no questions attached to ".$category_id."<Br>\n";
		}
		if ($this->debug_ajax) 
		{
			highlight_string(print_r($category));
			echo "<Br><br>is category returned<Br>\n";
		}
		return $category;
	} //end of function get_category_questions
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function get_category_configuration($category_id=0)
	{
		//Get Category Configuration
		if($category_id != 0)
		{
			$this->sql_query = "select * from geodesic_categories where category_id = ".$category_id;
			$result = $this->db->Execute($this->sql_query);
			if (!$result)
				return false;
			elseif($result->RecordCount() == 1)
				$this->category_configuration = $result->FetchNextObject();
		}
			
		if($this->category_configuration->USE_SITE_DEFAULT==0||$category_id==0)
		{
			$this->sql_query = "SELECT * FROM geodesic_classifieds_configuration";
			$result = $this->db->Execute($this->sql_query);
			if (!$result)
				return false;
			elseif($result->RecordCount() == 1)
				$this->category_configuration = $result->FetchNextObject();
		}
		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_ad_configuration()
	{
		$this->sql_query = "select * from geodesic_classifieds_ad_configuration";
		$result = $this->db->Execute($this->sql_query);
		if (!$result)
			return false;
		elseif ($result->RecordCount() == 1)
		{
			$this->ad_configuration_data = $result->FetchNextObject();
			return true;
		}

	} //function get_ad_configuration
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_site_configuration($db)
	{
		$sql_query = "SELECT * FROM geodesic_classifieds_configuration";
		$result = $this->db->Execute($sql_query);
		if (!$result)
		{
			return false;
		}
		else
		{
			$this->configuration_data = $result->FetchRow();
		}
		return true;

	} //function get_ad_configuration
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function CountOptionalFields()
	{
		$count = 0;

		$this->sql_query = "select * from geodesic_classifieds_configuration limit 1";
		$result = $this->db->Execute($this->sql_query);
		if(!$result)
			return false;
		$row = $result->FetchNextObject();
		$variable = get_object_vars($row);
		$keys = array_keys($variable);

		for($i = 1; in_array("USE_OPTIONAL_FIELD_".$i, $keys); $i++)
			$count++;

		$this->optional_fields = $count;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_text()
	{
		$this->sql_query = "select text_id,text from geodesic_pages_messages_languages where page_id = ".$this->page_id;
		//echo $this->sql_query."<br>\n";
		$result = $this->db->Execute($this->sql_query);
		if (!$result)
		{
			//echo "bad get_text query<bR>\n";
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			//take the database message result and push the contents into an array
			while ($show = $result->FetchNextObject())
			{
				$this->messages[$show->TEXT_ID] = $show->TEXT;
				//echo $show->TEXT_ID." - ".$show->TEXT."<br>\n";
			}
		}
	} // end of function get_text

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function displaySearchQuestions($categoryID)
	{
		if ($this->debug_ajax)
		{
			echo "<br>TOP OF  DISPLAYSEARCHQUESTIONS<BR>\n";
		}
		$category = $this->getCategoryQuestions($categoryID);
		$to_display = 1;
		$criteria = "
					<table cellpadding=2 cellspacing=1 border=0 width=100%>";
		
		// Display all optional fields
		$this->page_id = 44;
		$this->get_text($db);
		$this->get_category_configuration($categoryID);
		$this->get_site_configuration($db);
		$this->get_ad_configuration($db);
		if(!$this->optional_fields)
			$this->CountOptionalFields($db);
		$field_vars = get_object_vars($this->category_configuration);
		$ad_vars = get_object_vars($this->ad_configuration_data);
		for($i = 1; $i < $this->optional_fields+1; $i++)
		{
			// the following is impossible to make sense of
			//if($field_vars['USE_OPTIONAL_FIELD_'.$i])
			if (($field_vars['USE_OPTIONAL_FIELD_'.$i]) && (!$this->configuration_data["optional_".$i."_filter_association"]))
			{
				// Special case because optional field 1 has a different number than rest of scheme
				if($i == 1)
					$criteria .="<tr>\n\t<td class=search_field_section_labels style='width:40%;'>".urldecode($this->messages[1457])."</td>\n\t";
				elseif($i <= 10)
					$criteria .="<tr>\n\t<td class=search_field_section_labels style='width:40%;'>".urldecode($this->messages[(1458+($i-1))])."</td>\n\t";
				elseif($i <= 20)
					$criteria .="<tr>\n\t<td class=search_field_section_labels style='width:40%;'>".urldecode($this->messages[(1933+($i-11))])."</td>\n\t";
				elseif($i <= 35)
					$criteria .="<tr>\n\t<td class=search_field_section_labels style='width:40%;'>".urldecode($this->messages[(2778+($i-21))])."</td>\n\t";
				$criteria .="<td class=search_data_values>\n\t";

				if ($ad_vars["OPTIONAL_".$i."_NUMBER_ONLY"])
				{
					//if numbers only - produce a upper and lower limit
					$criteria .= "<table cellpadding=1 >
						<tr class=range_labels><td>".urldecode($this->messages[1440])."</td><td><input name=b[by_optional_".$i."_lower] size=15 maxsize=15 class=range_labels>\n\t\t</td></tr>
						<tr class=range_labels><td>".urldecode($this->messages[1441])."</td><td><input name=b[by_optional_".$i."_higher] size=15 maxsize=15 class=range_labels>\n\t\t</td></tr></table>";
				}
				elseif (!($ad_vars["OPTIONAL_".$i."_FIELD_TYPE"]))
				{
					//check to see if numbers only
					if ($ad_vars["OPTIONAL_".$i."_NUMBER_ONLY"])
					{
						//if numbers only - produce a upper and lower limit
						$criteria .= "<table cellpadding=1 >
						<tr class=range_labels><td>".urldecode($this->messages[1440])."</td><td><input name=b[by_optional_".$i."_lower] size=15 maxsize=15 class=search_data_values>\n\t\t</td></tr>
						<tr class=range_labels><td>".urldecode($this->messages[1441])."</td><td><input name=b[by_optional_".$i."_higher] size=15 maxsize=15 class=search_data_values>\n\t\t</td></tr></table>";
					}
					else
						$criteria .= "<input type=text name=b[optional_field_".$i."] id=optional_field_".$i." class=search_data_values>\n\t";
				}
				else
				{
					$query = "select * from ".$this->sell_choices_table." where type_id = ".$ad_vars["OPTIONAL_".$i."_FIELD_TYPE"]." order by display_order,value";
					if ($this->debug_ajax) echo $query."<Br>\n";
					$type_result = $this->db->Execute($this->sql_query) or die();
					if ($type_result->RecordCount() > 0)
					{
						$criteria .= "<select name=b[optional_field_".$i."] id=optional_field_".$i." class=search_data_values>";
						$criteria .= "<option value=0></option>";
						$matched = 0;
						while ($show_dropdown = $type_result->FetchNextObject())
						{
							$criteria .= "<option value=\"".$show_dropdown->VALUE."\" ";
							if ($this->classified_variables["optional_field_".$i] == $show_dropdown->VALUE)
							{
								$criteria .= "selected";
								$matched = 1;
							}
							$criteria .= ">".$show_dropdown->VALUE."</option>\n\t\t";
						}
						$criteria .= "</select>";
					}
					else
					//blank text box
					$criteria .= "<input type=text name=b[optional_field_".$i."] id=optional_field_".$i." class=search_data_values>\n\t";
				}
				if (($ad_vars["OPTIONAL_".$i."_OTHER_BOX"]) && ($ad_vars["OPTIONAL_".$i."_FIELD_TYPE"]))
					$criteria .= " ".urldecode($this->messages[1458])." <input type=text name=b[optional_field_".$i."_other] class=search_data_values>\n\t";
				$criteria .="</td>\n</tr>\n";
				$to_display++;
			}
		}
		
		//spit out the questions
		if(is_array($category))
		{
			foreach ($category["questions"] as $key => $value)
			{
				//get the list of choices for this question
				if ($category["choices"][$key] == "check")
				{
					$criteria .= "
							<tr>
								<td class=search_field_section_labels style='width:40%;'>".$category["questions"][$key]."</td>
								<td class=search_data_values>
									<input class=search_data_values type=checkbox name=b[question_value][".$key."] value=\"".$category["questions"][$key]."\">
								</td>
							</tr>";
					$to_display++;
				}
				elseif ($category["choices"][$key] == "none")
				{
					$criteria .= "
							<tr>
								<td class=search_field_section_labels style='width:40%;'>".$category["questions"][$key]."</td>
								<td class=search_data_values>
									<input class=search_data_values type=text name=b[question_value][".$key."]>
								</td>
							</tr>";
					$to_display++;
				}
				elseif ($category["choices"][$key] == "textarea")
				{
					$criteria .= "
							<tr>
								<td class=search_field_section_labels style='width:40%;'>".$category["questions"][$key]."</td>
								<td class=search_data_values>
									<textarea name=b[question_value][".$key."]></textarea>
								</td>
							</tr>";
					$to_display++;
				}
				elseif ($category["choices"][$key] != "none")
				{
					$query = "select * from geodesic_classifieds_sell_question_choices where type_id = '{$category["choices"][$key]}' order by display_order,value";
					if ($this->debug_ajax) echo $query."<Br>\n";
					$result = $this->db->Execute($query) or die("An error has occurred ".__LINE__);
					
					if ($result->RecordCount() > 0)
					{
						$criteria .= "
							<tr>
								<td class=search_field_section_labels style='width:40%;'>{$category["questions"][$key]}</td>
								<td class=search_data_values>
									<select name=b[question_value][$key] class=search_data_values>
										<option></option>";
						while ($row = $result->FetchRow())
							$criteria .= "
										<option>{$row["value"]}</option>";
						$criteria .= "
									</select>
								</td>
							</tr>";
						$to_display++;
					}
				}
			}
		}
		$criteria .= "
					</table>";
		echo ($to_display == 0) ? "catQuestions| " : "catQuestions|$criteria";
		
		exit;
	}
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function close() {
		include_once("classes/site_class.php");
		include_once("classes/browse_ads.php");
		$browse = new Browse_ads($this->db,1,1);
		$browse->classified_close($this->db);
		$this->db->Close();
		exit;
	}
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

}

function initAjax(&$db) {
	header('Cache-Control: no-cache');
	header('Expires: -1');
	header('Pragma: no-cache');
	
	if(isset($_GET["action"])) 
	{
		$ajax = new ajax($db);
		if ($ajax->debug_ajax)
		{
			echo "initiating ajax class<bR>\n";
			echo $_GET["action"]." is action<bR>\n";
			echo $_GET["b"]." is b<bR>\n";
		}
		if(isset($_GET['b']))
			echo $ajax->{$_GET["action"]}($_GET["b"]);	
		else 
			echo $ajax->{$_GET["action"]}();
	}
}
?>