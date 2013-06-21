<?// admin_group_management_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
\**************************************************************************/

class Group_management extends Admin_site {

	var $debug_groups = 0;
	var $new_group_error = "";
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Group_management($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_group_list($db)
	{
		$this->sql_query = "select * from ".$this->classified_groups_table." order by name";
		$result = $db->Execute($this->sql_query);
		if ($result===false)
			return false;
		$this->title = "Users / User Groups";
		$this->description = "Setting up User Groups allows you to charge different fees to different users for listings on your site. This is
			because each User Group can have its own specific Price Plan(s) attached to it. Upon registration every user is automatically placed
			into the User Group	marked below as \"default\". That is, unless they enter a \"registration (promotional) code\" during registration,
			that is attached to a User Group other than \"default\". Every user within the same User Group pays the same amount for listing
			placements and renewals based upon the Price Plan that you have attached to that User Group. The \"move\" link below allows you to
			move entire groups of users from one User Group to another.";
		if (!$this->admin_demo()) $this->body .= "<form action=index.php?a=36&b=3 method=post>";
		$this->body .= "
			<table cellpadding=3 cellspacing=1 border=0 align=center width=100% class=row_color1>
				<tr bgcolor=000066>
					<td align=center colspan=".(($this->is_class_auctions()) ? "8" : "7")." class=large_font_light>Current User Groups</td>
				</tr>
				<tr class=row_color_red>
					<td class=medium_font_light><b>User Group Name</b></td>
					<td class=medium_font_light nowrap><b># of users</b></td>";
		if ($this->is_class_auctions() || $this->is_classifieds())
			$this->body .= "
					<td align=center class=medium_font_light width=15%><b>Classifieds<br>Price Plan</b></td>";
		if ($this->is_class_auctions() || $this->is_auctions())
			$this->body .= "
					<td align=center class=medium_font_light width=15%><b>Auctions<br>Price Plan</b></td>";
		$this->body .= "
					<td class=medium_font_light><b>default</b></td>
					<td colspan=3 class=medium_font_light>&nbsp;</td>
				</tr>";

		$this->row_count = 0;
		while ($show = $result->FetchRow())
		{
			$this->sql_query = "select count(*) as group_total from ".$this->user_groups_price_plans_table." where group_id = ".$show["group_id"]." and id!=1";
			$group_count_result = $db->Execute($this->sql_query);
			if (!$group_count_result)
			{
				//echo $this->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($group_count_result->RecordCount() == 1)
				$show_group_count = $group_count_result->FetchRow();
			$this->body .= "
				<tr class=".$this->get_row_color().">
					<td class=medium_font><b>".$show["name"]."</b><br>".$show["description"]."</td>
					<td align=center class=medium_font>".$show_group_count["group_total"]."</td>";
			if ($this->is_class_auctions() || $this->is_classifieds())
			{
				$this->body .= "
					<td align=center class=medium_font><a href=index.php?a=37&b=3&g=".$show["price_plan_id"].">
						<span class=medium_font>".$this->get_price_plan_name($db,$show["price_plan_id"])."</span></a>
					</td>";
			}
			if ($this->is_class_auctions() || $this->is_auctions())
			{
				$this->body .= "
						<td align=center class=medium_font>";
			}
			if ($show["auction_price_plan_id"])
			{
				$this->body .= "
							<a href=index.php?a=37&b=3&g=".$show["auction_price_plan_id"].">
							<span class=medium_font>".$this->get_price_plan_name($db,$show["auction_price_plan_id"])."</span></a>";
			}
			else
			{
				$this->body .= "
							<span class=medium_error_font>no default</span>";
			}
			$this->body .= "
						</td>
						<td align=center class=medium_font><input type=radio name=e value=".$show["group_id"]." ";
			if ($show["default_group"] == 1)
				$this->body .= "checked";
			$this->body .= ">
						</td>
						<td align=center>
							<a href=index.php?a=36&b=5&g=".$show["group_id"].">
							<span class=medium_font><img src=admin_images/btn_admin_move.gif alt=move border=0></span></a>
						</td>
						<td align=center>
							<a href=index.php?a=36&b=4&c=".$show["group_id"].">
							<span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></a>
						</td>";
			$this->body .= "
						<td align=center>";
			if ($show["group_id"] != 1)
			{
				$this->body .= "
							<a href=index.php?a=36&b=2&c=".$show["group_id"].">
							<span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a>";
			}
			else
			{
				$this->body .= "&nbsp;";
			}
			$this->body .= "
						</td>
					</tr>";
			$this->row_count++;
		}
		$this->body .= "
					<tr>";
		if (!$this->admin_demo()) $this->body .= "<td class=medium_font align=center colspan=7><input type=submit name=submit value=\"change default\"></td>";
		$this->body .= "	</tr>
					<tr>
						<td align=center colspan=7><a href=index.php?a=36&b=1><span class=medium_font><b>add new User Group</b></span></a></td>
					</tr>
				</table>
			</form>\n";

		return true;
	} //end of function display_group_list

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_group($db,$group_info=0)
	{
		if (!$group_info)
		{
			if ($this->debug_groups)
				$this->new_group_error .= "<br>admin_group_management.php LINE ".__LINE__."<br>";
			return false;
		}

		if (!$this->check_registration_code($db,0,$group_info["registration_code"]))
		{
			$this->new_group_error = "That registration code already exists, please try again.";
			if ($this->debug_groups)
				$this->new_group_error .= "<br>admin_group_management.php LINE ".__LINE__."<br>";
			return true;
		}

		if ($this->is_class_auctions() || $this->is_auctions())
			$new_auc_price_plan_id = $group_info["price_plan_type"] == 1 ? $group_info["new_auc_price_plan_fee"] : $group_info["new_auc_price_plan_sub"];
		if ($this->is_class_auctions() || $this->is_classifieds())
			$new_class_price_plan_id = $group_info["price_plan_type"] == 1 ? $group_info["new_class_price_plan_fee"] : $group_info["new_class_price_plan_sub"];


		$num_products = ($this->is_class_auctions()) ? 2 : 1;
		for ($i=$num_products;$i>0;$i--)
		{
			if ($this->is_class_auctions())
			{
				//initialize variables and then switch 2nd time through the loop
				$price_plan_column = ($i==2) ? "price_plan_id" : "auction_price_plan_id";
				$other_price_plan_column = ($i==2) ? "auction_price_plan_id" : "price_plan_id";
			}
			else
			{
				//only gonna do this loop once, so we need to specify which product type we're using
				if ($this->is_auctions())
					$price_plan_column = "auction_price_plan_id";
				elseif ($this->is_classifieds())
					$price_plan_column = "price_plan_id";
				$other_price_plan_column = "";
			}
			if ($this->is_class_auctions())
			{
				if (!$group_info["price_plan_type"])
					return false;
				if($group_info["price_plan_type"]==2)
				{
					if (!$other_price_plan_column)
						return false;
					$new_id = "";
					if ($price_plan_column=="auction_price_plan_id" && $group_info["sub_period_choice"]==1)
					{
						$this->sql_query = "SELECT * FROM $this->classified_subscription_choices_table
							WHERE
								price_plan_id = $new_auc_price_plan_id";
						$new_id = $new_auc_price_plan_id;
						$other_id = $new_class_price_plan_id;
					}
					elseif ($price_plan_column=="price_plan_id" && $group_info["sub_period_choice"]==2)
					{
						$this->sql_query = "SELECT * FROM $this->classified_subscription_choices_table
							WHERE
								price_plan_id = $new_class_price_plan_id";
						$new_id = $new_class_price_plan_id;
						$other_id = $new_auc_price_plan_id;
					}
					if (strlen($new_id)>0)
					{
						$new_sub_result = $db->Execute($this->sql_query);
						if($new_sub_result === false)
							return false;
						$this->sql_query = "DELETE FROM $this->classified_subscription_choices_table WHERE
							price_plan_id = $other_id";
						if($db->Execute($this->sql_query) === false)
							return false;

						if ($new_sub_result->RecordCount() > 0)
						{
							while ($new_subscription = $new_sub_result->FetchRow())
							{
								$this->sql_query = "INSERT INTO $this->classified_subscription_choices_table
								(display_value,value,amount,price_plan_id)
								VALUES
								(
									\"{$new_subscription["display_value"]}\",
									\"{$new_subscription["value"]}\",
									\"{$new_subscription["amount"]}\",
									\"{$other_id}\"
								)";
								if($db->Execute($this->sql_query) === false)
									return false;
							}
						}
					}
				}
			}
		}

		if ((strlen(trim($group_info["name"])) > 0) && (strlen(trim($group_info["description"])) > 0))
		{
			$group_info["affiliate"] = ($group_info["affiliate"]) ? $group_info["affiliate"] : 0;
			$this->sql_query = "INSERT INTO ".$this->classified_groups_table."
				(
					name,
					description,
					price_plan_id,
					auction_price_plan_id,
					registration_code,
					registration_splash_code,
					place_an_ad_splash_code,
					sponsored_by_code,affiliate
				)
				VALUES
				(
					\"".$group_info["name"]."\",
					\"".$group_info["description"]."\",
					\"".$new_class_price_plan_id."\",
					\"".$new_auc_price_plan_id."\",
					\"".$group_info["registration_code"]."\",
					\"".trim(addslashes($group_info["registration_splash_code"]))."\",
					\"".trim(addslashes($group_info["place_an_ad_splash_code"]))."\",
					\"".trim(addslashes($group_info["sponsored_by_code"]))."\",
					\"".$group_info["affiliate"]."\"
				)";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				if ($this->debug_groups)
					$this->new_group_error .= "<br>admin_group_management.php LINE ".__LINE__."<br>";
				return false;
			}
			$group_id = $db->Insert_Id();

			$this->sql_query = "select language_id from ".$this->pages_languages_table;
			$language_result = $db->Execute($this->sql_query);
			if (!$language_result)
			{
				if ($this->debug_groups)
					$this->new_group_error .= "<br>admin_group_management.php LINE ".__LINE__."<br>";
				return false;
			}
			elseif ($language_result->RecordCount() > 0)
			{
				while ($show = $language_result->FetchRow())
				{
					$this->sql_query = "INSERT INTO ".$this->affiliate_templates_table."
						(group_id,language_id,template_id,secondary_template_id,ad_display_template_id,
						extra_question_template_id,checkbox_question_template_id,auctions_display_template_id,
						auctions_extra_question_template_id,auctions_checkbox_question_template_id)	VALUES(
						".$group_id.",
						\"".$show["language_id"]."\",
						\"".$group_info[$show["language_id"]]["template_id"]."\",
						\"".$group_info[$show["language_id"]]["secondary_template_id"]."\",
						\"".$group_info[$show["language_id"]]["ad_display_template_id"]."\",
						\"".$group_info[$show["language_id"]]["extra_question_template_id"]."\",
						\"".$group_info[$show["language_id"]]["checkbox_question_template_id"]."\",
						\"".$group_info[$show["language_id"]]["auctions_display_template_id"]."\",
						\"".$group_info[$show["language_id"]]["auctions_extra_question_template_id"]."\",
						\"".$group_info[$show["language_id"]]["auctions_checkbox_question_template_id"]."\")
						";
					$insert_result = $db->Execute($this->sql_query);
					if (!$insert_result)
					{
						if ($this->debug_groups)
							$this->new_group_error .= "<br>admin_group_management.php LINE ".__LINE__."<br>";
						return false;
					}
				}
			}

			//STOREFRONT CODE
			if(is_file('storefront/admin_store.php'))
			{
				include_once('storefront/admin_store.php');
				$group_info["storefront"] = ($group_info["storefront"]) ? 1 : 0;
				$this->sql_query = "update ".$this->classified_groups_table." set
					storefront = \"".$group_info["storefront"]."\"
					where group_id = ".$group_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					echo $this->sql_query."<br>\n";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				if($group_info["storefront_periods"])
				{
					foreach($group_info["storefront_periods"] as $period_id)
					{
						$this->sql_query = "insert into ".Admin_store::get("storefront_group_subscriptions_choices_table")."
						(group_id, choice_id)
						values
						($group_id, $period_id)";
						$result = $db->Execute($this->sql_query);
						if (!$result)
						{
							echo $this->sql_query."<br>\n";
							$this->error_message = $this->internal_error_message;
							return false;
						}
					}
				}
			}
			//STOREFRONT CODE
			$this->new_group_error = "Your group has been added.";
			return true;
		}
		else
		{
			if ($this->debug_groups)
				$this->new_group_error .= "<br>admin_group_management.php LINE ".__LINE__."<br>";
			return false;
		}
	} //end of function insert_group

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_group($db,$group_id=0)
	{
		if ($group_id)
		{
			if ($group_id == 1)
			{
				$this->body .= "<font size=4 color=red>cannot delete this group <br>
					<font size=2 color=red>This is the group users are placed in if errors occur among the group structure ";
				return true;
			}
			$this->sql_query = "select * from ".$this->classified_groups_table."
				where group_id = ".$group_id;
			$group_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$group_result)
			{
				//echo $this->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($group_result->RecordCount== 1)
			{
				$show_group = $group_result->FetchRow();
				if ($show_group["default_group"] == 1)
				{
					$this->body .= "<font size=4 color=red>cannot delete default group <br>
						<font size=2 color=red>make a different group the default group and try deleting again ";
					return true;
				}
			}

			$this->sql_query = "delete from ".$this->classified_groups_table."
				where group_id = ".$group_id;
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}

			$this->sql_query = "delete from ".$this->affiliate_templates_table."
				where group_id = ".$group_id;
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				//echo $this->sql_query."<br>\n";
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
	} //end of function delete_group

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function set_default_group($db,$group_id=0)
	{
		if ($group_id)
		{
			$this->sql_query = "update ".$this->classified_groups_table." set
				default_group = 0";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			else
			{
				$this->sql_query = "update ".$this->classified_groups_table." set
					default_group = 1
					where group_id = ".$group_id;
				$result = $db->Execute($this->sql_query);
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
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function set_default_group

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function move_to_group($db,$group_from=0,$group_to=0)
	{
		if (($group_from) && ($group_to) && ($group_from != $group_to))
		{
			$this->sql_query = "select price_plan_id from ".$this->classified_groups_table." where group_id = ".$group_to;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query.'<br>';
			if(!$result)
			{
				if ($this->debug_groups) echo $this->sql_query."<br>\n";
				$this->error_message = $this->internal_error_message;
				return false;
			}
			else
			{
				$show_group = $result->FetchRow();
			}

			$this->sql_query = "update ".$this->user_groups_price_plans_table." set
				group_id = ".$group_to.",
				price_plan_id = ".$show_group["price_plan_id"]."
				where group_id = ".$group_from;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
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
	} //end of function move_to_group

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%


	function move_group_price_plan_form($db,$group_id)
	{
		if (!$group_id)
			return false;
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"move current group members to new price plan also\", \"There are currently live members in this group. Choose \\\"yes\\\" from the selection to move the current users to the new price plan you choose above. Choose \\\"no\\\" to leave the current group members on the price plans they currently have.\"]\n";

		//".$this->show_tooltip(1,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$this->sql_query = "SELECT * FROM ".$this->classified_groups_table." WHERE group_id = $group_id";

		$group_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$group_result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		elseif ($group_result->RecordCount() == 1)
		{
			$show_group = $group_result->FetchRow();
			$this->title = "Users / User Groups > Edit Details > Edit Price Plan";
			$this->description = "Use the form below to specify a new Price Plan for this User Group.";

			$this->body .= "
					<table cellpadding=0 cellspacing=0 border=0 align=center width=100%>
			             <tr>
							<td colspan=100%>
								<table valign=center cellspacing=0 cellpadding=3 width=100%>
									<tr bgcolor=000066>
										<td colspan=100% class=large_font_light align=center><b>User Group: ".$show_group["name"]."</b></td>
									</tr>
							    </table>
							</td>
					     </tr>
					</table>";

			//get all price plans for dropdown boxes
			$this->sql_query = "SELECT * FROM $this->price_plan_table ORDER BY name";

			$price_plan_result = $db->Execute($this->sql_query);
			if (!$price_plan_result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($price_plan_result->RecordCount() > 0)
			{
				//ECHO "<BR>RECORDS = ".$price_plan_result->RecordCount();
				while ($show = $price_plan_result->FetchRow())
				{
					//ECHO "<BR>APPLIES TO = ".$show["applies_to"];
					if ($show["type_of_billing"] == 1)
					{
						if (($this->is_class_auctions() || $this->is_auctions()) && $show["applies_to"]==2)
						{
							if ($show["price_plan_id"]==$show_group["auction_price_plan_id"])
							{
								$auc_fee_based_options .= "
									<option value=".$show["price_plan_id"]." selected><b>".$show["name"]." - (current)</b></option>";
								$selected_price_plan_type = 0;
							}
							else
								$auc_fee_based_options .= "
									<option value=".$show["price_plan_id"].">".$show["name"]."</option>";
						}
						if (($this->is_class_auctions() || $this->is_classifieds()) && $show["applies_to"]==1)
						{
							if ($show["price_plan_id"]==$show_group["price_plan_id"])
							{
								$class_fee_based_options .= "
									<option value=".$show["price_plan_id"]." selected><b>".$show["name"]." - (current)</b></option>";
								$selected_price_plan_type = 0;
							}
							else
								$class_fee_based_options .= "
									<option value=".$show["price_plan_id"].">".$show["name"]."</option>";
						}
					}
					elseif ($show["type_of_billing"] == 2)
					{
						$this->sql_query = "
							SELECT * FROM
								$this->classified_subscription_choices_table
							WHERE
								price_plan_id = ".$show["price_plan_id"];
						$sub_result = $db->Execute($this->sql_query);
						if (!$sub_result)
						{
							$this->error_message = $this->internal_error_message;
							return false;
						}
						elseif ($sub_result->RecordCount() > 0)
						{
							if (($this->is_class_auctions() || $this->is_auctions()) && $show["applies_to"]==2)
							{
								if ($show["price_plan_id"]==$show_group["auction_price_plan_id"])
								{
									$auc_sub_based_options .= "
										<option value=".$show["price_plan_id"]." selected><b>".$show["name"]." - (current)</b></option>";
									$selected_price_plan_type = 1;
								}
								else
									$auc_sub_based_options .= "
										<option value=".$show["price_plan_id"].">".$show["name"]."</option>";
							}
							if (($this->is_class_auctions() || $this->is_classifieds()) && $show["applies_to"]==1)
							{
								if ($show["price_plan_id"]==$show_group["price_plan_id"])
								{
									$class_sub_based_options .= "
										<option value=".$show["price_plan_id"]." selected><b>".$show["name"]." - (current)</b></option>";
									$selected_price_plan_type = 1;
								}
								else
									$class_sub_based_options .= "
										<option value=".$show["price_plan_id"].">".$show["name"]."</option>";
							}
						}
					}
				}

				$this->additional_body_tag_attributes = " onload='javascript:show_types();'";
				$this->additional_header_html .= "
					<script type=text/javascript>
						function show_types(field)
						{
							if (document.getElementById('fee').style.display == 'none')
							{
								if (!field || field==document.price_plans.elements[0])
								{
									document.getElementById('fee').style.display = '';
									document.getElementById('sub').style.display = 'none';
									document.price_plans.elements[0].checked = true;
								}
							}
							else
							{
								if (!field || field==document.price_plans.elements[1])
								{
									document.getElementById('fee').style.display = 'none';
									document.getElementById('sub').style.display = '';
									document.price_plans.elements[1].checked = true;
								}
							}
						}
						function check_sub_period_choice()
						{
							if (document.getElementById('sub_plan_type').checked &&
								!document.getElementById('choice_1').checked &&
								!document.getElementById('choice_2').checked)
							{
								alert('You must choose which price plan\'s subscription periods will overwrite the other.');
								return false;
							}
							else
							{
								return true;
							}
						}
					</script>";
				$this->body .= "
					<form onSubmit='return check_sub_period_choice()' action=index.php?a=36&b=6&g=".$group_id." method=post name=price_plans>
					<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>
						<tr>
							<td align=right class=medium_font width=50%><b>Price Plan Type:</b></td>
							<td class=medium_font>
								<input type=radio name=k[price_plan_type] value=1 onclick='javascript:show_types(this);'".(!$selected_price_plan_type ? " checked" : "").">Fee-based<br>
								<input id=sub_plan_type type=radio name=k[price_plan_type] value=2 onclick='javascript:show_types(this);'".($selected_price_plan_type ? " checked" : "").">Subscription
							</td>
						</tr>
						<tr id=fee ".($selected_price_plan_type ? "" : "style='display:none;'").">
							<td colspan=100%>
								<table align=center border=0>";
				if($this->is_auctions() || $this->is_class_auctions())
				{
					$this->body .= "
										<tr>
											<td align=right valign=center class=medium_font>
												<b>auction price plan:</b>
											</td>
											<td align=left valign=center class=medium_font>
												<select name=k[new_auc_price_plan_fee]>
													$auc_fee_based_options
												</select>
											</td>
										</tr>";
				}
				if($this->is_classifieds() || $this->is_class_auctions())
				{
					$this->body .= "
										<tr>
											<td align=right valign=center class=medium_font>
												<b>classified price plan:</b>
											</td>
											<td align=left valign=center class=medium_font>
												<select name=k[new_class_price_plan_fee]>
													$class_fee_based_options
												</select>
											</td>
										</tr>";
				}
				$this->body .= "
								</table>
							</td>
						</tr>
						<tr id=sub ".($selected_price_plan_type ? "style='display:none;'" : "").">
							<td colspan=100%>
								<table align=center border=0>";
				if($this->is_auctions() || $this->is_class_auctions())
				{
					if (strlen($auc_sub_based_options)>0)
						$this->body .= "
										<tr>
											<td align=right valign=center class=medium_font>
												<b>auction price plan:</b>
											</td>
											<td align=left valign=center class=medium_font>
												<select name=k[new_auc_price_plan_sub]>
													$auc_sub_based_options
												</select>
											</td>
											<td>
												<input onclick=\"javascript:alert('NOTE: This will permanently overwrite subscription periods belonging to the classified subscription chosen in the pull down box below');\"
													id=choice_1 name=k[sub_period_choice] type=radio value=1>
											</td>
											<td align=left valign=center class=medium_font>
												<b>use this price plan's subscription periods</b>
											</td>
										</tr>";
					else
						$this->body .= "
										<tr>
											<td align=left valign=center class=medium_error_font>
												You currently do not have an auction subscription price plan that has at least one subcription period.
											</td>
										</tr>";
				}
				if($this->is_classifieds() || $this->is_class_auctions())
				{
					if (strlen($class_sub_based_options)>0)
						$this->body .= "
										<tr>
											<td align=right valign=center class=medium_font>
												<b>classified price plan:</b>
											</td>
											<td align=left valign=center class=medium_font>
												<select name=k[new_class_price_plan_sub]>
													$class_sub_based_options
												</select>
											</td>
											<td>
												<input onclick=\"javascript:alert('NOTE: This will permanently overwrite subscription periods belonging to the auction subscription chosen in the pull down box above');\"
													id=choice_2 name=k[sub_period_choice] type=radio value=2>
											</td>
											<td align=left valign=center class=medium_font>
												<b>use this price plan's subscription periods</b>
											</td>
										</tr>";
					else
						$this->body .= "
										<tr>
											<td align=left valign=center class=medium_error_font>
												You currently do not have a classified subscription price plan that has at least one subcription period.
											</td>
										</tr>";
				}

				$this->body .= "</table>
							</td>
						</tr>";
				if (!$this->admin_demo()) $this->body .= "
						<tr>
							<td align=center colspan=2><input type=submit value=\"Save\"></td>
						</tr>";
				$this->body .= "
					</table>
					</form>";
			}
			$this->body .= "
					<a href=index.php?a=36&b=4&c=$group_id><span class=medium_font><b>back to Edit Details for <b>".$show_group["name"]."</b></span></a>";
			return true;
		}
		else return false;

	} //end of function move_group_price_plan_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function move_group_price_plan($db,$group_id,$group_info)
	{
		if (!$group_info || !$group_id)
			return false;

		if ($this->is_class_auctions() || $this->is_auctions())
			$new_auc_price_plan_id = $group_info["price_plan_type"] == 1 ? $group_info["new_auc_price_plan_fee"] : $group_info["new_auc_price_plan_sub"];
		if ($this->is_class_auctions() || $this->is_classifieds())
			$new_class_price_plan_id = $group_info["price_plan_type"] == 1 ? $group_info["new_class_price_plan_fee"] : $group_info["new_class_price_plan_sub"];

		// grab group data
		$this->sql_query = "SELECT * FROM ".$this->classified_groups_table." WHERE group_id = $group_id";
		if ($this->debug_groups) echo $this->sql_query."<br>\n";
		$group_result = $db->Execute($this->sql_query);
		if ($group_result === false)
			return false;
		if ($group_result->RecordCount() != 1)
			return false;

		//the group exists
		$show_group = $group_result->FetchRow();
		//set loop counter
		$num_products = ($this->is_class_auctions()) ? 2 : 1;
		for ($i=$num_products;$i>0;$i--)
		{
			if ($this->is_class_auctions())
			{
				//initialize variables and then switch 2nd time through the loop
				$price_plan_column = ($i==2) ? "price_plan_id" : "auction_price_plan_id";
				$other_price_plan_column = ($i==2) ? "auction_price_plan_id" : "price_plan_id";
			}
			else
			{
				//only gonna do this loop once, so we need to specify which product type we're using
				if ($this->is_auctions())
					$price_plan_column = "auction_price_plan_id";
				elseif ($this->is_classifieds())
					$price_plan_column = "price_plan_id";
				$other_price_plan_column = "";
			}
			if ($this->is_class_auctions())
			{
				if (!$group_info["price_plan_type"])
					return false;
				if($group_info["price_plan_type"]==2)
				{
					if (!$other_price_plan_column)
						return false;
					$new_id = "";
					if ($price_plan_column=="auction_price_plan_id" && $group_info["sub_period_choice"]==1)
					{
						$this->sql_query = "SELECT * FROM $this->classified_subscription_choices_table
							WHERE
								price_plan_id = $new_auc_price_plan_id";
						$new_id = $new_auc_price_plan_id;
						$other_id = $new_class_price_plan_id;
					}
					elseif ($price_plan_column=="price_plan_id" && $group_info["sub_period_choice"]==2)
					{
						$this->sql_query = "SELECT * FROM $this->classified_subscription_choices_table
							WHERE
								price_plan_id = $new_class_price_plan_id";
						$new_id = $new_class_price_plan_id;
						$other_id = $new_auc_price_plan_id;
					}
					if (strlen($new_id)>0)
					{
						$new_sub_result = $db->Execute($this->sql_query);
						if($new_sub_result === false)
							return false;
						$this->sql_query = "DELETE FROM $this->classified_subscription_choices_table WHERE
							price_plan_id = $other_id";
						if($db->Execute($this->sql_query) === false)
							return false;

						if ($new_sub_result->RecordCount() > 0)
						{
							while ($new_subscription = $new_sub_result->FetchRow())
							{
								$this->sql_query = "INSERT INTO $this->classified_subscription_choices_table
								(display_value,value,amount,price_plan_id)
								VALUES
								(
									\"{$new_subscription["display_value"]}\",
									\"{$new_subscription["value"]}\",
									\"{$new_subscription["amount"]}\",
									\"{$other_id}\"
								)";
								if($db->Execute($this->sql_query) === false)
									return false;
							}
						}
					}
				}
			}
			if ($price_plan_column == "price_plan_id")
			{
				$this->sql_query = "UPDATE $this->classified_groups_table
					SET
						price_plan_id = $new_class_price_plan_id
					WHERE
						group_id = $group_id";

				$update_group_result = $db->Execute($this->sql_query);
				if ($update_group_result === false)
					return false;

				//move the current users to the new price plan
				$this->sql_query = "UPDATE $this->user_groups_price_plans_table
					SET
						price_plan_id = $new_class_price_plan_id
					WHERE group_id = $group_id";
			}
			elseif ($price_plan_column == "auction_price_plan_id")
			{
				$this->sql_query = "UPDATE $this->classified_groups_table
					SET
						auction_price_plan_id = $new_auc_price_plan_id
					WHERE
						group_id = $group_id";

				$update_group_result = $db->Execute($this->sql_query);
				if ($update_group_result === false)
					return false;

				//move the current users to the new price plan
				$this->sql_query = "UPDATE $this->user_groups_price_plans_table
					SET
						auction_price_plan_id = $new_auc_price_plan_id
					WHERE group_id = $group_id";
			}
			$update_current_result = $db->Execute($this->sql_query);
			if ($update_current_result === false)
				return false;
		}
		return true;
	} //end of function update_group_info

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function group_form($db,$group_id=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "
			Text[1] = [\"affiliate privileges\", \"When checked this will give affiliate privileges to users in this group.  Affiliate privileges allow users of this group to have access to the affiliate page.  This is a special page where (if linked to properly) a page will be displayed with only their own listings.  There will be no direct link to the main site unless you place one within the template.  The search within this section will only return listings from this user.  To find the direct link to the affiliate's site for this user you must find the user you wish to send the link to through the list users or search users part of the admin and display that users information.  The link to the affiliate section will be with the other group information for that user.\"]\n
			Text[2] = [\"Affiliate URL Page\", \"Choose a template used on this group affiliate site within the home page using this language.  If no template is chosen, the default template set in the PAGES > BROWSING PAGES > BROWSE CATEGORIES will be used.  Enter all templates through the template administration.\"]\n
			Text[3] = [\"Affiliate URL Browsing Page\", \"Choose a template used on this group affiliate site within the secondary browsing page using this language.  If no template is chosen for this category the default template set in the PAGES > BROWSING PAGES > BROWSE CATEGORIES will be used.  Enter all templates through the template administration.\"]\n
			Text[4] = [\"Listing Display Details\", \"Choose a template used on this group affiliate site within ad display page using this language.  If no template is chosen for this category, the default template set in the PAGES > BROWSING PAGES > AD DISPLAY PAGE will be used.  Enter all templates through the template administration.\"]\n
			Text[5] = [\"display listing extra question template\", \"Choose a template used on this group affiliate site within the listing display template to display the extra category specific questions using this language.  If no template is chosen for this category the default template set in the PAGES > BROWSING PAGES > EXTRA QUESTIONS will be used.  Enter all templates through the template administration.\"]\n
			Text[6] = [\"display listing checkbox template\", \"Choose a template used on this group affiliate site within the listing display template to display the checkbox based category specific questions using this language.  If no template is chosen for this category the default template set in the PAGES > BROWSING PAGES > EXTRA CHECKBOXES will be used.  Enter all templates through the template administration.\"]\n
			Text[7] = [\"group splash page at registration\", \"Cut and paste html you wish to display after a user has registered into this group.  This html will be displayed within the registration page template and will appear once this user has entered the registration code for this group.  This will not display if the user is \\\"defaulted\\\" into this group by not entering a registration code.\"]\n
			Text[8] = [\"group splash page before placing an listing\", \"Cut and paste html you wish to display to a user of this group when they click the link to place a new ad.  This html will be displayed within the \\\"choose category\\\" page template before the user chooses a category to place their listing in.\"]\n
			Text[9] = [\"sponsored by html placed within listing display\", \"Cut and paste html you wish to display within the listing display page of sellers belonging to to this group. This can be any message you wish to attach to users listings placed by sellers within this group.  You must place the <&lt;SPONSORED_BY>&gt; tag within the listing display template for this code to display.  On sellers that do not have sponsored by html attached to their group nothing will appear.\"]\n
			";
		//STOREFRONT CODE
		$this->body .= "
			Text[100] = [\"storefront privileges\", \"Allow users to purchase storefront subscriptions.\"]\n
			Text[101] = [\"storefront subscriptions\", \"These subscription periods can be added and edited through the storefront tool.\"]\n
			";
		//STOREFRONT CODE

		//".$this->show_tooltip(9,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		if ($group_id)
		{
			$this->sql_query = "select * from ".$this->classified_groups_table." where group_id = ".$group_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchRow();
			}
			else
			{
				return false;
			}

			if ($show)
			{
				$this->title = "Users / User Groups > Edit Details";
				$this->description = "Edit this User Group's name and description through this admin tool.  Make your changes then click the \"save\" button at the bottom of the form.";
				$this->row_count = 0;
				$this->body .= "
						<table cellpadding=3 cellspacing=0 border=0 align=center width=100%>
				             <tr>
								<td colspan=100%>
									<table valign=center cellspacing=0 cellpadding=3 width=100%>
										<tr bgcolor=000066>
											<td colspan=100% class=large_font_light align=center><b>User Group: ".$show["name"]."</b></td>
										</tr>
								    </table>
								</td>
						     </tr>
						</table>";
				if (!$this->admin_demo())$this->body .= "<form name=fields_to_use action=index.php?a=36&b=4&c=".$group_id." method=post>";
						$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>
							<tr class=".$this->get_row_color().">
								<td align=right width=50% class=medium_font><b>Group Name:</b></td>
								<td width=50%><input type=text name=d[name] size=30 maxsize=30 value=\"".$show["name"]."\"></td>
							</tr>";$this->row_count++;
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=right valign=top class=medium_font><b>Group Description:</b></td>
								<td><textarea name=d[description] rows=3 cols=30>".$this->special_chars($show["description"])."</textarea></td>
							</tr>";$this->row_count++;
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=right width=50% class=medium_font><b>Registration Code:</b></td>
								<td width=50%>
									<input type=text name=d[registration_code] size=30 maxsize=30 value=\"".$show["registration_code"]."\">
								</td>
							</tr>";$this->row_count++;
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=right valign=top class=medium_font>
									<b>Questions attached to this Group:</b>
								</td>
								<td class=medium_font>";
				$this->row_count++;
				$this->sql_query = "select * from ".$this->sell_questions_table." where group_id = ".$group_id;
				$question_result = $db->Execute($this->sql_query);
				if (!$question_result)
				{
					if ($this->debug_groups) echo "<br><font color=red>LINE ".__LINE__."<BR>ERROR IN QUERY - ".$this->sql_query."</font><BR>";
					$this->error_message = $this->internal_error_message;
					return false;
				}
				elseif ($question_result->RecordCount() > 0)
				{
					while ($show_group = $question_result->FetchRow())
					{
						$this->body .= $show_group["name"]."<br>\n";
					}
				}
				else
				{
					$this->body .= "none";
				}

				$this->body .= "<a href=index.php?a=60&b=".$group_id."><span class=medium_font><b><br>Edit / Add Group Questions</b></span></a><br><br></td></tr>";

				$default_auction_price_plan = $this->get_price_plan($db, $show["auction_price_plan_id"]);
				$default_class_price_plan = $this->get_price_plan($db, $show["price_plan_id"]);
				$this->body .= "
							<tr>
								<td colspan=100%>
									<table valign=center cellspacing=0 cellpadding=0 width=100%>
										<tr bgcolor=000066>
											<td colspan=100% class=medium_font_light align=center><b>Price Plans Attached to \"".$show["name"]."\"</b></td>
										</tr>
										<tr>";
				if ($this->is_class_auctions() || $this->is_classifieds())
				{
					$this->body .= "		<td valign=top width=50%>
												<table cellpadding=0 width=100%>
													<tr class=row_color_red>
														<td align=center class=medium_font_light colspan=2><b>Classifieds</b></td>
													</tr>
													<tr class=row_color2>
														<td align=left class=medium_font width=90%><b>Default</b></td>
														<td>
															<a href=index.php?a=36&b=6&g=".$group_id.">
																<span class=medium_font>
																	<img src=admin_images/btn_admin_edit.gif alt=edit border=0>
																</span>
															</a>
														</td>
													</tr>
													<tr class=row_color1>
														<td colspan=2 align=left valign=top class=medium_font>
															".$default_class_price_plan["name"]."
														</td>
													</tr>
													<tr style='display:".(($default_class_price_plan["type_of_billing"]==2) ? "none" : "")."' class=row_color2>
														<td align=left class=medium_font width=90%><b>Additional</b></td>
														<td>
															<a href=index.php?a=36&b=7&g=".$group_id."&t=1>
																<span class=medium_font>
																	<img src=admin_images/btn_admin_edit.gif alt=edit border=0>
																</span>
															</a>
														</td>
													</tr>";
					$this->sql_query  = "SELECT price_plan_id FROM ".$this->attached_price_plans." WHERE
						group_id = ".$group_id." AND
						applies_to = 1";
					$addition_class_price_plan_result = $db->Execute($this->sql_query);
					if (!$addition_class_price_plan_result)
					{
						if ($this->debug_groups) echo "<br><font color=red>LINE ".__LINE__."<BR>ERROR IN QUERY - ".$this->sql_query."</font><BR>";
						$this->error_message = $this->internal_error_message;
						return false;
					}
					else
					{
						if ($addition_class_price_plan_result->RecordCount() > 0)
						{
							//display extra attached price plans
							while ($show_price_plan = $addition_class_price_plan_result->FetchRow())
							{
								$price_plan_name = $this->get_price_plan_name($db, $show_price_plan["price_plan_id"]);
								$this->body .= "	<tr style='display:".(($default_class_price_plan["type_of_billing"]==2) ? "none" : "")."' class=row_color1>
														<td align=left valign=top class=medium_font colspan=2>
															".$price_plan_name."
														</td>
													</tr>";
							}
						}
						else $this->body .= "		<tr style='display:".(($default_class_price_plan["type_of_billing"]==2) ? "none" : "")."' class=row_color1>
														<td align=left valign=top class=medium_error_font colspan=2>
															none
														</td>
													</tr>";
					}
					$this->body .= "			</table>
											</td>";
				}
				if ($this->is_class_auctions() || $this->is_auctions())
				{
					$this->body .= "		<td valign=top width=50%>
												<table cellpadding=0 width=100%>
													<tr class=row_color_red>
														<td align=center class=medium_font_light colspan=2><b>Auctions</b></td>
													</tr>
													<tr class=row_color2>
														<td align=left class=medium_font width=90%><b>Default</b></td>
														<td>
															<a href=index.php?a=36&b=6&g=".$group_id.">
																<span class=medium_font>
																	<img src=admin_images/btn_admin_edit.gif alt=edit border=0>
																</span>
															</a>
														</td>
													</tr>
													<tr class=row_color1>
														<td colspan=2 align=left valign=top class=medium_font>
															".$default_auction_price_plan["name"]."
														</td>
													</tr>
													<tr style='display:".(($default_auction_price_plan["type_of_billing"]==2) ? "none" : "")."' class=row_color2>
														<td align=left class=medium_font width=90%><b>Additional</b></td>
														<td>
															<a href=index.php?a=36&b=7&g=".$group_id."&t=2>
																<span class=medium_font>
																	<img src=admin_images/btn_admin_edit.gif alt=edit border=0>
																</span>
															</a>
														</td>
													</tr>";
					$this->sql_query  = "SELECT price_plan_id FROM ".$this->attached_price_plans." WHERE
						group_id = ".$group_id." AND
						applies_to = 2";
					$addition_auction_price_plan_result = $db->Execute($this->sql_query);
					if (!$addition_auction_price_plan_result)
					{
						if ($this->debug_groups) echo "<br><font color=red>LINE ".__LINE__."<BR>ERROR IN QUERY - ".$this->sql_query."</font><BR>";
						$this->error_message = $this->internal_error_message;
						return false;
					}
					else
					{
						if ($addition_auction_price_plan_result->RecordCount() > 0)
						{
							//display extra attached price plans
							while ($show_price_plan = $addition_auction_price_plan_result->FetchRow())
							{
								$price_plan_name = $this->get_price_plan_name($db, $show_price_plan["price_plan_id"]);
								$this->body .= "	<tr style='display:".(($default_auction_price_plan["type_of_billing"]==2) ? "none" : "")."' class=row_color1>
														<td align=left valign=top class=medium_font colspan=2>
															".$price_plan_name."
														</td>
													</tr>";
							}
						}
						else $this->body .= "		<tr style='display:".(($default_auction_price_plan["type_of_billing"]==2) ? "none" : "")."' class=row_color1>
														<td align=left valign=top class=medium_error_font colspan=2>
															none
														</td>
													</tr>";
					}
					$this->body .= "			</table>
											</td>";
				}
				$this->body .= "		</table>
									</td>
								</tr>";

				$this->additional_body_tag_attributes .= " onload='javascript:hide_affiliate_section(".$show["affiliate"].");'";
				$this->additional_header_html .= "
						<script type=\"text/javascript\">
							function hide_affiliate_section(refresh_value)
							{
								if (refresh_value==0 || refresh_value==1)
									check = refresh_value;
								else
									check = (document.getElementById('cbox').checked == true) ? 1 : 0;
								if (check==1)
								{
									document.getElementById('cbox').checked = true;
									document.getElementById('aff').style.display = '';

								}
								else
								{
									document.getElementById('cbox').checked = false;
									document.getElementById('aff').style.display = 'none';
								}
							}
						</script>";

				$this->body .= "<tr class=".$this->get_row_color().">
									<td colspan=100% align=center>
										<a class=medium_font href=index.php?a=36&b=10&g=".$group_id."><b>Group Price Plan Registration Specifics</b><br><br></a>
									</td>
								</tr>
								<tr bgcolor=000066>
									<td colspan=100% class=medium_font_light align=center><b>Spash Pages HTML / 'Sponsored By' HTML</b></td>
								</tr>
								<tr class=row_color1>
									<td align=center valign=top class=medium_font colspan=100%>
										<b>group splash page at registration:".$this->show_tooltip(7,1)."
									</td>
								</tr>
								<tr>
									<td class=medium_font colspan=100%>
										<textarea name=d[registration_splash_code] style=\"width:100%\" cols=50 rows=20>"
											.$this->special_chars(stripslashes($show["registration_splash_code"])).
										"</textarea>
									</td>
								</tr>
								<tr class=row_color1>
									<td align=center valign=top class=medium_font colspan=100%>
										<b>group splash page before placing a listing:".$this->show_tooltip(8,1)."
									</td>
								</tr>
								<tr>
									<td class=medium_font colspan=100%>
										<textarea name=d[place_an_ad_splash_code]  style=\"width:100%\" cols=50 rows=20>"
											.$this->special_chars(stripslashes($show["place_an_ad_splash_code"]))."
										</textarea>
									</td>
								</tr>
								<tr class=row_color1>
									<td align=center valign=top class=medium_font colspan=100%>
										<b>sponsored by html placed within listing display:".$this->show_tooltip(9,1)."
									</td>
								</tr>
								<tr>
									<td class=medium_font colspan=100%>
										<textarea name=d[sponsored_by_code]  style=\"width:100%\" cols=50 rows=20>"
											.$this->special_chars(stripslashes($show["sponsored_by_code"]))."
										</textarea><br><br>
									</td>
								</tr>
								<tr bgcolor=000066>
									<td colspan=100% class=medium_font_light align=center><b>Affiliate Privileges</b></td>
								</tr>
								<tr>
									<td colspan=2 class=medium_font>
										The users that have	affiliate privileges can place their own html within the affiliate side templates.
										The users with affiliate privileges can place html into the	affiliate html field where they edit their
										personal registration information inside of the user management facility on the client side.  This
										affiliate html will appear in the templates you choose below where you place the tag
										&lt;AFFILIATE_INFO&gt; tag within that template.<Br><br>The templates specified below will be used
										within this User Group's affiliates section.  When affiliates link to their affiliate page through the
										Affiliate URL link found in their \"My Account\" pages, the templates below will be used.
									</td>
								</tr>
								<tr class=".$this->get_row_color().">
									<td align=right width=50% class=medium_font>
										<b>Use Affiliate Privileges?: </b>".$this->show_tooltip(1,1)."
									</td>
									<td width=50% valign=top class=medium_font>
										<input id=cbox onclick=\"javascript:hide_affiliate_section();\" type=checkbox name=d[affiliate] value=1 "
											.(($show["affiliate"]==1) ? "checked" : "" ).">
									</td>
								</tr>
								<tr align=center id=aff style='display:".(($show["affiliate"]) ? "" : "none").";'>
									<td colspan=100%>
										<table>";

				$this->sql_query = "select language_id from ".$this->pages_languages_table;
				$language_result = $db->Execute($this->sql_query);
				 if (!$language_result)
				 {
					if ($this->debug_groups) echo "<br><font color=red>LINE ".__LINE__."<BR>ERROR IN QUERY - ".$this->sql_query."</font><BR>";
					$this->error_message = $this->messages[3501];
					return false;
				 }
				 elseif ($language_result->RecordCount() > 0)
				 {

				 	while ($show_language = $language_result->FetchRow())
				 	{
						$this->sql_query = "select * from ".$this->affiliate_templates_table." where group_id = ".$group_id." and language_id = ".$show_language["language_id"];
						$group_language_result = $db->Execute($this->sql_query);
						 if (!$group_language_result)
						 {
							if ($this->debug_groups) echo "<br><font color=red>LINE ".__LINE__."<BR>ERROR IN QUERY - ".$this->sql_query."</font><BR>";
							$this->error_message = $this->messages[3501];
							return false;
						 }
						 elseif ($group_language_result->RecordCount() == 1)
						 {
						 	$show_current = $group_language_result->FetchRow();
						 }
				 		$this->body .= "<tr class=row_color_black>\n\t<td colspan=2 class=medium_font_light>\n\t<b>Affiliate Section Templates for :
				 		".$this->get_language_name($db,$show_language["language_id"])."</b>\n\t</td>\n\t";


				 		$this->row_count = 0;

						$this->row_count++;

						$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
							<b>Affiliate URL Page:</b>&nbsp;".$this->show_tooltip(2,1)."</td>\n\t";
						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=d[".$show_language["language_id"]."][template_id]>\n\t\t";
						$this->body .= "<option value=0>none</option>\n\t";
						$category_template_result = $db->Execute($this->sql_query);
						 if (!$category_template_result)
						 {
						 	if ($this->debug_groups) echo "<br><font color=red>LINE ".__LINE__."<BR>ERROR IN QUERY - ".$this->sql_query."</font><BR>";
							$this->error_message = $this->messages[3501];
							return false;
						 }
						 elseif ($category_template_result->RecordCount() > 0)
						 {
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "<option ";
								if ($show_template["template_id"] == $show_current["template_id"])
									$this->body .= "selected ";
								$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
							}
						 }
						$this->body .= "</select>\n\t </td>\n</tr>\n";
						$this->row_count++;

						$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
							<b>Affiliate URL Browsing Page:</b>&nbsp;".$this->show_tooltip(3,1)."</td>\n\t";
						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$this->body .= "<td valign=top>\n\t<select name=d[".$show_language["language_id"]."][secondary_template_id]>\n\t\t";
						$this->body .= "<option value=0>none</option>\n\t";
						$category_template_result = $db->Execute($this->sql_query);
						 if (!$category_template_result)
						 {
						 	if ($this->debug_groups) echo "<br><font color=red>LINE ".__LINE__."<BR>ERROR IN QUERY - ".$this->sql_query."</font><BR>";
							$this->error_message = $this->messages[3501];
							return false;
						 }
						 elseif ($category_template_result->RecordCount() > 0)
						 {
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "<option ";
								if ($show_template["template_id"] == $show_current["secondary_template_id"])
									$this->body .= "selected ";
								$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
							}
						 }
						$this->body .= "</select>\n\t </td>\n</tr>\n";
						$this->row_count++;


						if($this->is_classifieds()||$this->is_class_auctions())
						{
							$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
								<b>Ad - Listing Display Details:</b>&nbsp;".$this->show_tooltip(4,1)."</td>\n\t";
							$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
							$this->body .= "<td valign=top>\n\t<select name=d[".$show_language["language_id"]."][ad_display_template_id]>\n\t\t";
							$this->body .= "<option value=0>none</option>\n\t";
							$category_template_result = $db->Execute($this->sql_query);
							 if (!$category_template_result)
							 {
								$this->error_message = $this->messages[3501];
								return false;
							 }
							 elseif ($category_template_result->RecordCount() > 0)
							 {
								while ($show_template = $category_template_result->FetchRow())
								{
									$this->body .= "<option ";
									if ($show_template["template_id"] == $show_current["ad_display_template_id"])
										$this->body .= "selected ";
									$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
								}
							 }
							$this->body .= "</select>\n\t </td>\n</tr>\n";
							$this->row_count++;

							$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
								<b>Ad - Extra Questions:</b>&nbsp;".$this->show_tooltip(5,1)."</td>\n\t";
							$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
							$this->body .= "<td valign=top>\n\t<select name=d[".$show_language["language_id"]."][extra_question_template_id]>\n\t\t";
							$this->body .= "<option value=0>none</option>\n\t";
							$category_template_result = $db->Execute($this->sql_query);
							 if (!$category_template_result)
							 {
								$this->error_message = $this->messages[3501];
								return false;
							 }
							 elseif ($category_template_result->RecordCount() > 0)
							 {
								while ($show_template = $category_template_result->FetchRow())
								{
									$this->body .= "<option ";
									if ($show_template["template_id"] == $show_current["extra_question_template_id"])
										$this->body .= "selected ";
									$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
								}
							 }
							$this->body .= "</select>\n\t </td>\n</tr>\n";
							$this->row_count++;

							$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
								<b>Ad - Checkboxes:</b>&nbsp;".$this->show_tooltip(6,1)."</td>\n\t";
							$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
							$this->body .= "<td valign=top>\n\t<select name=d[".$show_language["language_id"]."][checkbox_question_template_id]>\n\t\t";
							$this->body .= "<option value=0>none</option>\n\t";
							$category_template_result = $db->Execute($this->sql_query);
							 if (!$category_template_result)
							 {
								$this->error_message = $this->messages[3501];
								return false;
							 }
							 elseif ($category_template_result->RecordCount() > 0)
							 {
								while ($show_template = $category_template_result->FetchRow())
								{
									$this->body .= "<option ";
									if ($show_template["template_id"] == $show_current["checkbox_question_template_id"])
										$this->body .= "selected ";
									$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
								}
							 }
							$this->body .= "</select>\n\t </td>\n</tr>\n";
							$this->row_count++;
					 	}

						//
						// AUCTIONS
						//

						if($this->is_auctions()||$this->is_class_auctions())
						{
							$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
								<b>Auction - Listing Display Details:</b>&nbsp;".$this->show_tooltip(4,1)."</td>\n\t";
							$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
							$this->body .= "<td valign=top>\n\t<select name=d[".$show_language["language_id"]."][auctions_display_template_id]>\n\t\t";
							$this->body .= "<option value=0>none</option>\n\t";
							$category_template_result = $db->Execute($this->sql_query);
							 if (!$category_template_result)
							 {
								$this->error_message = $this->messages[3501];
								return false;
							 }
							 elseif ($category_template_result->RecordCount() > 0)
							 {
								while ($show_template = $category_template_result->FetchRow())
								{
									$this->body .= "<option ";
									if ($show_template["template_id"] == $show_current["auctions_display_template_id"])
										$this->body .= "selected ";
									$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
								}
							 }
							$this->body .= "</select>\n\t </td>\n</tr>\n";
							$this->row_count++;

							$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
								<b>Auction - Extra Questions:</b>&nbsp;".$this->show_tooltip(5,1)."</td>\n\t";
							$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
							$this->body .= "<td valign=top>\n\t<select name=d[".$show_language["language_id"]."][auctions_extra_question_template_id]>\n\t\t";
							$this->body .= "<option value=0>none</option>\n\t";
							$category_template_result = $db->Execute($this->sql_query);
							 if (!$category_template_result)
							 {
								$this->error_message = $this->messages[3501];
								return false;
							 }
							 elseif ($category_template_result->RecordCount() > 0)
							 {
								while ($show_template = $category_template_result->FetchRow())
								{
									$this->body .= "<option ";
									if ($show_template["template_id"] == $show_current["auctions_extra_question_template_id"])
										$this->body .= "selected ";
									$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
								}
							 }
							$this->body .= "</select>\n\t </td>\n</tr>\n";
							$this->row_count++;

							$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right valign=top class=medium_font>\n\t
								<b>Auction - Checkboxes:</b>&nbsp;".$this->show_tooltip(6,1)."</td>\n\t";
							$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
							$this->body .= "<td valign=top>\n\t<select name=d[".$show_language["language_id"]."][auctions_checkbox_question_template_id]>\n\t\t";
							$this->body .= "<option value=0>none</option>\n\t";
							$category_template_result = $db->Execute($this->sql_query);
							 if (!$category_template_result)
							 {
								$this->error_message = $this->messages[3501];
								return false;
							 }
							 elseif ($category_template_result->RecordCount() > 0)
							 {
								while ($show_template = $category_template_result->FetchRow())
								{
									$this->body .= "<option ";
									if ($show_template["template_id"] == $show_current["auctions_checkbox_question_template_id"])
										$this->body .= "selected ";
									$this->body .= "value=".$show_template["template_id"].">".$show_template["name"]."</option>\n\t";
								}
							 }
							$this->body .= "</select>\n\t </td>\n</tr>\n";
							$this->row_count++;
					 	}
						//
						// AUCTIONS
						//
					}
				}

				$this->body .= "</table></td></tr>";//end id=aff

				//STOREFRONT CODE
				if(is_file('storefront/admin_store.php'))
				{
					include_once('storefront/admin_store.php');
					$this->body .= "
					<tr bgcolor=000066>
						<td colspan=2 class=medium_font_light align=center><b>Storefront Privileges</b></td>
					</tr>
					<tr>
						<td colspan=2 class=medium_font>
							The users that have Storefront privileges can be charged a subsrciption for having storefront
							capabilities. You can allow them choose from a variety of different subscription lengths depending
							upon the lengths you have set up within the Storefront Menu.
						</td>
					</tr>
					<tr class=".$this->get_row_color().">
						<td align=right width=50% class=medium_font>
							<b>Use Storefront Privileges?: </b>".$this->show_tooltip(100,1)."
						</td>
						<td width=50% valign=top class=medium_font>
							<input type=checkbox name=d[storefront] value=1 "
								.(($show["storefront"]==1) ? "checked" : "" ).">
						</td>
					</tr>";
					$this->row_count++;
					$this->body .= "<tr class=".$this->get_row_color().">
						<td align=right width=50% class=medium_font>
							<b>Attach Storefront Subscription Periods: </b>".$this->show_tooltip(101,1)."
						</td>
						<td align=center class=medium_font>
							&nbsp;
						</td>
					</tr>";

					$this->sql_query = "select choice_id from ".Admin_store::get("storefront_group_subscriptions_choices_table")." where group_id = $group_id";

					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						$this->error_message = $this->internal_error_message;
						return false;
					}
					$groupChoices = Array();
					while($groupSubscriptionChoices = $result->FetchRow())
					{
						array_push($groupChoices, $groupSubscriptionChoices["choice_id"]);
					}

					$this->sql_query = "select * from ".Admin_store::get('storefront_subscriptions_choices_table')." order by value";

					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						$this->error_message = $this->internal_error_message;
						return false;
					}
					while($subscriptionPeriods = $result->FetchRow())
					{
						$this->row_count++;
						$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right width=50% class=medium_font>
								".$subscriptionPeriods["display_value"]." - ".$subscriptionPeriods["amount"]."
							</td>
							<td width=50% valign=top class=medium_font>
								<input type=checkbox name=d[storefront_periods][".$subscriptionPeriods["period_id"]."] value=".$subscriptionPeriods["period_id"]." "
									.((in_array($subscriptionPeriods["period_id"],$groupChoices)) ? "checked" : "" ).">
							</td>
						</tr>";
					}
					//STOREFRONT CODE
				}
				if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 align=center>\n\t<input type=submit name=save_group value=\"save\">\n\t</td>\n</tr>\n";

				$this->body .= "</table>\n";
				$this->body .= "</form>\n";
				return true;
			}
			else
			{
				if ($this->debug_groups) echo "<br><font color=red>LINE ".__LINE__."<BR>ERROR IN QUERY - ".$this->sql_query."</font><BR>";
				return false;
			}

		}
		else
		{
			if ($this->debug_groups) echo "<br><font color=red>LINE ".__LINE__."<BR>ERROR - No Group Id</font><BR>";
			return false;
		}
	} //end of function group_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function new_group_form($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "
			Text[1] = [\"affiliate privileges\", \"When checked this will give affiliate privileges to users in this group.  Affiliate privileges allow users of this group to have access to the affiliate page.  This is a special page where (if linked to properly) a page will be displayed with only their own listings.  There will be no direct link to the main site unless you place one within the template.  The search within this section will only return listings from this user.  To find the direct link to the affiliate's site for this user you must find the user you wish to send the link to through the list users or search users part of the admin and display that users information.  The link to the affiliate section will be with the other group information for that user.\"]\n
			Text[2] = [\"Affiliate URL Page\", \"Choose a template used on this group affiliate site within the home page using this language.  If no template is chosen, the default template set in the PAGES > BROWSING PAGES > BROWSE CATEGORIES will be used.  Enter all templates through the template administration.\"]\n
			Text[3] = [\"Affiliate URL Browsing Page\", \"Choose a template used on this group affiliate site within the secondary browsing page using this language.  If no template is chosen for this category the default template set in the PAGES > BROWSING PAGES > BROWSE CATEGORIES will be used.  Enter all templates through the template administration.\"]\n
			Text[4] = [\"Listing Display Details\", \"Choose a template used on this group affiliate site within ad display page using this language.  If no template is chosen for this category, the default template set in the PAGES > BROWSING PAGES > AD DISPLAY PAGE will be used.  Enter all templates through the template administration.\"]\n
			Text[5] = [\"display listing extra question template\", \"Choose a template used on this group affiliate site within the listing display template to display the extra category specific questions using this language.  If no template is chosen for this category the default template set in the PAGES > BROWSING PAGES > EXTRA QUESTIONS will be used.  Enter all templates through the template administration.\"]\n
			Text[6] = [\"display listing checkbox template\", \"Choose a template used on this group affiliate site within the listing display template to display the checkbox based category specific questions using this language.  If no template is chosen for this category the default template set in the PAGES > BROWSING PAGES > EXTRA CHECKBOXES will be used.  Enter all templates through the template administration.\"]\n
			Text[7] = [\"group splash page at registration\", \"Cut and paste html you wish to display after a user has registered into this group.  This html will be displayed within the registration page template and will appear once this user has entered the registration code for this group.  This will not display if the user is \\\"defaulted\\\" into this group by not entering a registration code.\"]\n
			Text[8] = [\"group splash page before placing an listing\", \"Cut and paste html you wish to display to a user of this group when they click the link to place a new ad.  This html will be displayed within the \\\"choose category\\\" page template before the user chooses a category to place their listing in.\"]\n
			Text[9] = [\"sponsored by html placed within listing display\", \"Cut and paste html you wish to display within the listing display page of sellers belonging to to this group. This can be any message you wish to attach to users listings placed by sellers within this group.  You must place the <&lt;SPONSORED_BY>&gt; tag within the listing display template for this code to display.  On sellers that do not have sponsored by html attached to their group nothing will appear.\"]\n";

		//".$this->show_tooltip(5,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$this->title = "Users / User Groups > Add New User Group";
		$this->description = "Use the form below to create a new User Group. Be sure to click the \"save\" button at the bottom of the form.";

		//get all fee-based price plans for dropdown boxes
		$this->sql_query = "SELECT DISTINCT p.applies_to,p.name,p.price_plan_id,p.type_of_billing
			FROM ".$this->price_plan_table." AS p, geodesic_classifieds_subscription_choices AS s
			WHERE
				p.price_plan_id = s.price_plan_id or p.type_of_billing=1
			ORDER BY p.name";

		$price_plan_result = $db->Execute($this->sql_query);
		if (!$price_plan_result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		elseif ($price_plan_result->RecordCount() > 0)
		{
			while ($show = $price_plan_result->FetchRow())
			{
				if (($this->is_class_auctions() || $this->is_auctions()) && $show["applies_to"]==2)
				{
					$auc_fee_based_options .= "
						<option value=".$show["price_plan_id"].">".$show["name"]."</option>";
				}
				if (($this->is_class_auctions() || $this->is_classifieds()) && $show["applies_to"]==1)
				{
					$class_fee_based_options .= "
						<option value=".$show["price_plan_id"].">".$show["name"]."</option>";
				}
			}
			
			$this->sql_query = "SELECT DISTINCT p.applies_to,p.name,p.price_plan_id,p.type_of_billing
				FROM ".$this->price_plan_table." AS p, geodesic_classifieds_subscription_choices AS s
				WHERE
					p.price_plan_id = s.price_plan_id or p.type_of_billing=2
				ORDER BY p.name";
	
			$price_plan_result = $db->Execute($this->sql_query);
			if (!$price_plan_result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($price_plan_result->RecordCount() > 0)
			{
				while ($show = $price_plan_result->FetchRow())
				{
					$this->sql_query = "SELECT * FROM $this->classified_subscription_choices_table
						WHERE price_plan_id = ".$show["price_plan_id"];
					//echo $this->sql_query."<br>\n";
					$sub_result = $db->Execute($this->sql_query);
					if (!$sub_result)
					{
						$this->error_message = $this->internal_error_message;
						return false;
					}
					elseif ($sub_result->RecordCount() > 0)
					{
						//echo "sub_result had some count<br>\n";
						if (($this->is_class_auctions() || $this->is_auctions()) && $show["applies_to"]==2)
						{
							$auc_sub_based_options .= "
								<option value=".$show["price_plan_id"].">".$show["name"]."</option>";
						}
						if (($this->is_class_auctions() || $this->is_classifieds()) && $show["applies_to"]==1)
						{
							$class_sub_based_options .= "
								<option value=".$show["price_plan_id"].">".$show["name"]."</option>";
						}
					}
					else 
					{
						//echo "sub_result had no count<br>\n";
						if (($this->is_class_auctions() || $this->is_auctions()) && $show["applies_to"]==2)
						{
							$auc_sub_based_options_error .= "
								<br><font color=red>you must add subscription lengths to your auction subscription based price plans</font>";
						}
						if (($this->is_class_auctions() || $this->is_classifieds()) && $show["applies_to"]==1)
						{
							$class_sub_based_options_error .= "
								<br><font color=red>you must add subscription lengths to your classified subscription based price plans</font>";
						}						
					}
				}
			}			

			$this->additional_body_tag_attributes .= " onload='javascript:show_types();'";
			$this->additional_header_html .= "
				<script type=text/javascript>
					function show_types(field)
					{
						if (document.getElementById('fee').style.display == 'none')
						{
							if (!field || field==document.price_plans.elements[3])
							{
								document.getElementById('fee').style.display = '';
								document.getElementById('sub').style.display = 'none';
								document.price_plans.elements[3].checked = true;
							}
						}
						else
						{
							if (!field || field==document.price_plans.elements[4])
							{
								document.getElementById('fee').style.display = 'none';
								document.getElementById('sub').style.display = '';
								document.price_plans.elements[4].checked = true;
							}
						}
					}
					function check_sub_period_choice()
					{
						if (document.getElementById('sub_plan_type').checked &&
							!document.getElementById('choice_1').checked &&
							!document.getElementById('choice_2').checked)
						{
							alert('You must choose which price plan\'s subscription periods will overwrite the other.');
							return false;
						}
						else
						{
							return true;
						}
					}
				</script>";
			if (!$this->admin_demo())
				$this->body .= "
				<form onSubmit='return check_sub_period_choice()' action=index.php?a=36&b=1 method=post name=price_plans>";
			$this->body .= "
				<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>
					<tr>
						<td align=right width=50% class=medium_font>
							<b>Group Name:</b>
						</td>
						<td width=50%>
							<input type=text name=d[name] size=30 maxsize=30>
						</td>
					</tr>
					<tr>
						<td align=right valign=top class=medium_font>
							<b>Group Description:</b>
						</td>
						<td>
							<textarea name=d[description] rows=3 cols=30></textarea>
						</td>
					</tr>
					<tr>
						<td align=right width=50% class=medium_font>
							<b>Registration Code:</b>
						</td>
						<td width=50%>
							<input type=text name=d[registration_code] size=30 maxsize=30>
						</td>
					</tr>
					<tr>
						<td align=right class=medium_font width=50%><b>Price Plan Type:</b></td>
						<td class=medium_font>
							<input type=radio name=d[price_plan_type] value=1 onclick='javascript:show_types(this);' checked>Fee-based<br>
							<input id=sub_plan_type type=radio name=d[price_plan_type] value=2 onclick='javascript:show_types(this);'>Subscription
						</td>
					</tr>
					<tr id=fee style='display:none;'>
						<td colspan=100%>
							<table align=center border=0>";
			if($this->is_auctions() || $this->is_class_auctions())
			{
				$this->body .= "
									<tr>
										<td align=right valign=center class=medium_font>
											<b>auction price plan:</b>
										</td>
										<td align=left valign=center class=medium_font>
											<select name=d[new_auc_price_plan_fee]>
												$auc_fee_based_options
											</select>
										</td>
									</tr>";
			}
			if($this->is_classifieds() || $this->is_class_auctions())
			{
				$this->body .= "
									<tr>
										<td align=right valign=center class=medium_font>
											<b>classified price plan:</b>
										</td>
										<td align=left valign=center class=medium_font>
											<select name=d[new_class_price_plan_fee]>
												$class_fee_based_options
											</select>
										</td>
									</tr>";
			}
			$this->body .= "
							</table>
						</td>
					</tr>
					<tr id=sub style='display:none;'>
						<td colspan=100%>
							<table align=center border=0>";
			if($this->is_auctions() || $this->is_class_auctions())
			{
				$this->body .= "
									<tr>
										<td align=right valign=center class=medium_font>
											<b>auction price plan:</b>".$auc_sub_based_options_error."
										</td>
										<td align=left valign=center class=medium_font>
											<select name=d[new_auc_price_plan_sub]>
												$auc_sub_based_options
											</select>
										</td>
										<td>
											<input onclick=\"javascript:alert('NOTE: This will permanently overwrite subscription periods belonging to the classified subscription chosen in the pull down box below');\"
												id=choice_1 name=d[sub_period_choice] type=radio value=1>
										</td>
										<td align=left valign=center class=medium_font>
											<b>use this price plan's subscription periods</b>
										</td>
									</tr>";
			}
			if($this->is_classifieds() || $this->is_class_auctions())
			{
				$this->body .= "
									<tr>
										<td align=right valign=center class=medium_font>
											<b>classified price plan:</b>".$class_sub_based_options_error."
										</td>
										<td align=left valign=center class=medium_font>
											<select name=d[new_class_price_plan_sub]>
												$class_sub_based_options
											</select>
										</td>
										<td>
											<input onclick=\"javascript:alert('NOTE: This will permanently overwrite subscription periods belonging to the auction subscription chosen in the pull down box above');\"
												id=choice_2 name=d[sub_period_choice] type=radio value=2>
										</td>
										<td align=left valign=center class=medium_font>
											<b>use this price plan's subscription periods</b>
										</td>
									</tr>";
			}
			$this->body .= "
							</table>
						</td>
					</tr>";
		}
		else
		{
			$this->body .= "
					<tr>
						<td class=medium_error_font>You do not have any listing price plans to attach.</td>
					</tr>";

		}
		$this->additional_body_tag_attributes .= " onload='javascript:hide_affiliate_section(".$show["affiliate"].");'";
		$this->additional_header_html .= "
				<script type=\"text/javascript\">
					function hide_affiliate_section(refresh_value)
					{
						if (refresh_value==0 || refresh_value==1)
							check = refresh_value;
						else
							check = (document.getElementById('cbox').checked == true) ? 1 : 0;
						if (check==1)
						{
							document.getElementById('cbox').checked = true;
							document.getElementById('aff').style.display = '';

						}
						else
						{
							document.getElementById('cbox').checked = false;
							document.getElementById('aff').style.display = 'none';
						}
					}
				</script>";

		$this->body .= "
					<tr bgcolor=000066>
						<td colspan=100% class=medium_font_light align=center><b>Spash Pages HTML / 'Sponsored By' HTML</b></td>
					</tr>
					<tr class=row_color1>
						<td align=center valign=top class=medium_font colspan=100%>
							<b>group splash page at registration:".$this->show_tooltip(7,1)."
						</td>
					</tr>
					<tr>
						<td class=medium_font colspan=100%>
							<textarea name=d[registration_splash_code] style=\"width:100%\" cols=50 rows=20></textarea>
						</td>
					</tr>
					<tr class=row_color1>
						<td align=center valign=top class=medium_font colspan=100%>
							<b>group splash page before placing a listing:".$this->show_tooltip(8,1)."
						</td>
					</tr>
					<tr>
						<td class=medium_font colspan=100%>
							<textarea name=d[place_an_ad_splash_code]  style=\"width:100%\" cols=50 rows=20></textarea>
						</td>
					</tr>
					<tr class=row_color1>
						<td align=center valign=top class=medium_font colspan=100%>
							<b>sponsored by html placed within listing display:".$this->show_tooltip(9,1)."
						</td>
					</tr>
					<tr>
						<td class=medium_font colspan=100%>
							<textarea name=d[sponsored_by_code]  style=\"width:100%\" cols=50 rows=20></textarea><br><br>
						</td>
					</tr>
					<tr bgcolor=000066>
						<td colspan=100% class=medium_font_light align=center><b>Affiliate Privileges</b></td>
					</tr>
					<tr>
						<td colspan=2 class=medium_font>
							The users that have	affiliate privileges can place their own html within the affiliate side templates.
							The users with affiliate privileges can place html into the	affiliate html field where they edit their
							personal registration information inside of the user management facility on the client side.  This
							affiliate html will appear in the templates you choose below where you place the tag
							&lt;AFFILIATE_INFO&gt; tag within that template.<Br><br>The templates specified below will be used
							within this User Group's affiliates section.  When affiliates link to their affiliate page through the
							Affiliate URL link found in their \"My Account\" pages, the templates below will be used.
						</td>
					</tr>
					<tr class=".$this->get_row_color().">
						<td align=right width=50% class=medium_font>
							<b>Use Affiliate Privileges?: </b>".$this->show_tooltip(1,1)."
						</td>
						<td width=50% valign=top class=medium_font>
							<input id=cbox onclick=\"javascript:hide_affiliate_section();\" type=checkbox name=d[affiliate] value=1 "
								.(($show["affiliate"]==1) ? "checked" : "" ).">
						</td>
					</tr>
					<tr align=center id=aff style='display:".(($show["affiliate"]) ? "" : "none").";'>
						<td colspan=100%>
							<table>";

		$this->sql_query = "select language_id from ".$this->pages_languages_table;
		$language_result = $db->Execute($this->sql_query);
		 if (!$language_result)
		 {
			if ($this->debug_groups) echo "<br><font color=red>LINE ".__LINE__."<BR>ERROR IN QUERY - ".$this->sql_query."</font><BR>";
			$this->error_message = $this->messages[3501];
			return false;
		 }
		 elseif ($language_result->RecordCount() > 0)
		 {

		 	while ($show_language = $language_result->FetchRow())
		 	{
				$this->sql_query = "select * from ".$this->affiliate_templates_table." where language_id = ".$show_language["language_id"];
				$group_language_result = $db->Execute($this->sql_query);
				if (!$group_language_result)
				{
					if ($this->debug_groups) echo "<br><font color=red>LINE ".__LINE__."<BR>ERROR IN QUERY - ".$this->sql_query."</font><BR>";
					$this->error_message = $this->messages[3501];
					return false;
				}
				elseif ($group_language_result->RecordCount() > 0)
				{
					$show_current = $group_language_result->FetchRow();
					$this->body .= "
								<tr class=row_color_black>
									<td colspan=2 class=medium_font_light>
										<b>Affiliate Section Templates for :".$this->get_language_name($db,$show_language["language_id"])."</b>
									</td>";
			 		$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
					$category_template_result = $db->Execute($this->sql_query);
					 if (!$category_template_result)
					 {
					 	if ($this->debug_groups) echo "<br><font color=red>LINE ".__LINE__."<BR>ERROR IN QUERY - ".$this->sql_query."</font><BR>";
						$this->error_message = $this->messages[3501];
						return false;
					 }
					 elseif ($category_template_result->RecordCount() > 0)
					 {
			 			$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right valign=top class=medium_font>
										<b>Affiliate URL Page:</b>&nbsp;".$this->show_tooltip(2,1)."
									</td>
									<td valign=top>
										<select name=d[".$show_language["language_id"]."][template_id]>
											<option value=0>none</option>";
						while ($show_template = $category_template_result->FetchRow())
						{
							$this->body .= "
										<option value=".$show_template["template_id"].">".$show_template["name"]."</option>";
						}
					 	$this->body .= "
										</select>
									</td>
								</tr>";$this->row_count++;
					}

					$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
					$category_template_result = $db->Execute($this->sql_query);
					 if (!$category_template_result)
					 {
					 	if ($this->debug_groups) echo "<br><font color=red>LINE ".__LINE__."<BR>ERROR IN QUERY - ".$this->sql_query."</font><BR>";
						$this->error_message = $this->messages[3501];
						return false;
					 }
					 elseif ($category_template_result->RecordCount() > 0)
					 {
						$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right valign=top class=medium_font>
										<b>Affiliate URL Browsing Page:</b>&nbsp;".$this->show_tooltip(3,1)."
									</td>
									<td valign=top>
										<select name=d[".$show_language["language_id"]."][secondary_template_id]>
											<option value=0>none</option>";
						while ($show_template = $category_template_result->FetchRow())
						{
							$this->body .= "
										<option value=".$show_template["template_id"].">".$show_template["name"]."</option>";
						}
					 	$this->body .= "
						 				</select>
									</td>
								</tr>";$this->row_count++;
					}

					if($this->is_classifieds()||$this->is_class_auctions())
					{
						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$category_template_result = $db->Execute($this->sql_query);
						 if (!$category_template_result)
						 {
							$this->error_message = $this->messages[3501];
							return false;
						 }
						 elseif ($category_template_result->RecordCount() > 0)
						 {
							$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right valign=top class=medium_font>
										<b>Ad - Listing Display Details:</b>&nbsp;".$this->show_tooltip(4,1)."
									</td>
									<td valign=top>
										<select name=d[".$show_language["language_id"]."][ad_display_template_id]>
											<option value=0>none</option>";
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "
										<option value=".$show_template["template_id"].">".$show_template["name"]."</option>";
							}
					 		$this->body .= "
										</select>
									</td>
								</tr>";$this->row_count++;
					 	}

						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$category_template_result = $db->Execute($this->sql_query);
						 if (!$category_template_result)
						 {
							$this->error_message = $this->messages[3501];
							return false;
						 }
						 elseif ($category_template_result->RecordCount() > 0)
						 {
							$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right valign=top class=medium_font>
										<b>Ad - Extra Questions:</b>&nbsp;".$this->show_tooltip(5,1)."
									</td>
									<td valign=top>
										<select name=d[".$show_language["language_id"]."][extra_question_template_id]>
											<option value=0>none</option>";
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "
										<option value=".$show_template["template_id"].">".$show_template["name"]."</option>";
							}

							$this->body .= "
										</select>
									</td>
								</tr>";$this->row_count++;
						}

						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$category_template_result = $db->Execute($this->sql_query);
						if (!$category_template_result)
						{
							$this->error_message = $this->messages[3501];
							return false;
						}
						elseif ($category_template_result->RecordCount() > 0)
						{
							$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right valign=top class=medium_font>
										<b>Ad - Checkboxes:</b>&nbsp;".$this->show_tooltip(6,1)."
									</td>
									<td valign=top>
										<select name=d[".$show_language["language_id"]."][checkbox_question_template_id]>
											<option value=0>none</option>";
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "
										<option value=".$show_template["template_id"].">".$show_template["name"]."</option>";
							}
							$this->body .= "
										</select>
									</td>
								</tr>";$this->row_count++;
						}
			 		}

			 		if($this->is_auctions()||$this->is_class_auctions())
					{
						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$category_template_result = $db->Execute($this->sql_query);
						if (!$category_template_result)
						{
							$this->error_message = $this->messages[3501];
							return false;
						}
						elseif ($category_template_result->RecordCount() > 0)
						{
							$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right valign=top class=medium_font>
										<b>Auction - Listing Display Details:</b>&nbsp;".$this->show_tooltip(4,1)."
									</td>
									<td valign=top>
										<select name=d[".$show_language["language_id"]."][auctions_display_template_id]>
											<option value=0>none</option>";
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "
										<option value=".$show_template["template_id"].">".$show_template["name"]."</option>";
							}

							$this->body .= "
										</select>
									</td>
								</tr>";$this->row_count++;
						}
						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$category_template_result = $db->Execute($this->sql_query);
						if (!$category_template_result)
						{
							$this->error_message = $this->messages[3501];
							return false;
						}
						elseif ($category_template_result->RecordCount() > 0)
						{
							$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right valign=top class=medium_font>
										<b>Auction - Extra Questions:</b>&nbsp;".$this->show_tooltip(5,1)."
									</td>
									<td valign=top>
										<select name=d[".$show_language["language_id"]."][auctions_extra_question_template_id]>
											<option value=0>none</option>";
							while ($show_template = $category_template_result->FetchRow())
							{
								$this->body .= "
										<option value=".$show_template["template_id"].">".$show_template["name"]."</option>";
							}

							$this->body .= "
										</select>
									</td>
								</tr>";$this->row_count++;
						}
						$this->sql_query = "select name,template_id from ".$this->templates_table." order by name";
						$category_template_result = $db->Execute($this->sql_query);
						if (!$category_template_result)
						{
							$this->error_message = $this->messages[3501];
							return false;
						}
						elseif ($category_template_result->RecordCount() > 0)
						{
							$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right valign=top class=medium_font>
										<b>Auction - Checkboxes:</b>&nbsp;".$this->show_tooltip(6,1)."
									</td>
									<td valign=top>
										<select name=d[".$show_language["language_id"]."][auctions_checkbox_question_template_id]>
											<option value=0>none</option>";
								while ($show_template = $category_template_result->FetchRow())
								{
									$this->body .= "
										<option value=".$show_template["template_id"].">".$show_template["name"]."</option>";
								}

							$this->body .= "
										</select>
									</td>
								</tr>";$this->row_count++;
						}
				 	}
				}
			}
		}

		$this->body .= "	</table>
						</td>
					</tr>";//end id=aff




				//STOREFRONT CODE
				if(is_file('storefront/admin_store.php'))
				{
					include_once('storefront/admin_store.php');
					$this->body .= "
					<tr bgcolor=000066>
						<td colspan=2 class=medium_font_light align=center><b>Storefront Privileges</b></td>
					</tr>
					<tr>
						<td colspan=2 class=medium_font>
							Description of storefront privileges
						</td>
					</tr>
					<tr class=".$this->get_row_color().">
						<td align=right width=50% class=medium_font>
							<b>Use Storefront Privileges?: </b>".$this->show_tooltip(100,1)."
						</td>
						<td width=50% valign=top class=medium_font>
							<input type=checkbox name=d[storefront] value=1 "
								.(($show["storefront"]==1) ? "checked" : "" ).">
						</td>
					</tr>";
					$this->row_count++;
					$this->body .= "<tr class=".$this->get_row_color().">
						<td align=right width=%50 class=medium_font>
							<b>Attach Storefront Subscription Periods: </b>".$this->show_tooltip(101,1)."
						</td>
						<td align=center class=medium_font>
							&nbsp;
						</td>
					</tr>";

					$this->sql_query = "select * from ".Admin_store::get('storefront_subscriptions_choices_table')." order by value";

					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						$this->error_message = $this->internal_error_message;
						return false;
					}
					while($subscriptionPeriods = $result->FetchRow())
					{
						$this->row_count++;
						$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right width=50% class=medium_font>
								".$subscriptionPeriods["display_value"]." - ".$subscriptionPeriods["amount"]."
							</td>
							<td width=50% valign=top class=medium_font>
								<input type=checkbox name=d[storefront_periods][".$subscriptionPeriods["period_id"]."] value=".$subscriptionPeriods["period_id"].">
							</td>
						</tr>";
					}
				}
				//STOREFRONT CODE

		$this->body .= "
					<tr>
						<td colspan=2 align=center>";
		if (!$this->admin_demo()) $this->body .= "<input type=submit name=save_group value=\"save\">";

		$this->body .= "</td>
					</tr>
				</table>
				</form>";
		return true;

	} //end of function new_group_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function move_group_form($db,$group_id=0)
	{
		if ($group_id)
		{
			$this->sql_query = "select name,group_id from ".$this->classified_groups_table." where group_id != ".$group_id." order by name";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$group_name = $this->get_group_name($db,$group_id);
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=36&b=5&g=".$group_id." method=post>\n";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
				$this->title .= "Users / User Groups > Move Users";
				$this->description .= "Move users from this User Group to
					another User Group using this admin tool.  Choose the User Group you wish to move the users into by selecting it from the dropdown list below.
					This operation cannot be reversed.  If you change your mind, you will have to move users back individually once this action is taken.";
		$this->body .= "<tr bgcolor=000066>\n\t<td class=large_font_light align=center><b>User Group: ".$group_name."</b>\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td align=center class=medium_font>Move users of this User Group to:  \n\t";
				$this->body .= "<select name=h>\n\t\t";

				while ($show = $result->FetchRow())
				{
					if ($show["group_id"] != $group_id)
					{
						$this->body .= "<option value=".$show["group_id"].">".$show["name"]."</option>\n\t\t";
					}
				}
				$this->body .= "</select>\n\t \n\t</td>\n</tr>\n";
				if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td align=center><input type=submit value=\"Save\"></td>\n</tr>\n";
				$this->body .= "</table>\n";
				$this->body .= "</form>\n";
				return true;
			}
			else
			{
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
				$this->title .= "Users / User Groups > Move Users";
				$this->body .= "<tr>\n\t<td class=medium_font align=center><b>There are no other User Groups to move users to.</b>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
				return true;
			}
		}
		else
		{
			return false;
		}

	} //end of function move_group_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_group_form($db,$group_id)
	{
		if (($group_id) && ($group_id != 1))
		{
			$this->sql_query = "select name,group_id from ".$this->classified_groups_table." order by name";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$delete_group_name = $this->get_group_name($db,$group_id);
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=36&b=2&c=".$group_id." method=post>\n";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
				$this->title = "Users / User Groups > Group Management > Delete";
				$this->description = "To delete a user group you must choose
					which group to move that groups users to.  Every user must be a part of a group.  In the dropdown list of users groups below choose
					the group the new users will be a part of. Once you have made a choice the changes will be made by clicking the \"delete\"
					button at the bottom.";
				$this->body .= "<tr bgcolor=000066>\n\t<td class=large_font_light align=center>\n\t<b>User Group: ".$delete_group_name."</b>\n\t</td>\n</tr>\n";

				$this->body .= "<tr>\n\t<td class=medium_font>\n\tSelect a new User Group to move this User Group's existing users to.
					 <br><br><br>\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td align=center class=medium_font>\n\tMove users to:
					<select name=d>\n\t\t";
				while ($show = $result->FetchRow())
				{
					if ($show["group_id"] != $group_id)
						$this->body .= "<option value=".$show["group_id"].">".$show["name"]."</option>\n\t\t";
				}
				$this->body .= "</select> \n\t</td>\n</tr>\n";
				if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td class=medium_font_light align=center><input type=submit name=submit value=\"Save\"> \n\t</td>\n</tr>\n";
				$this->body .= "</table>\n</form>\n";
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}

	} //end of function delete_group_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_group_info($db,$group_id=0,$group_info=0)
	{
		if (($group_info) && ($group_id))
		{
			$this->sql_query = "select * from ".$this->pages_languages_table;
			$language_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$language_result)
			{
				//echo $this->sql_query." is the query<br>\n";
				$this->error_message = $this->messages[3500];
				return false;
			}
			elseif ($language_result->RecordCount() > 0)
			{

				if ($this->check_registration_code($db,$group_id,$group_info["registration_code"]))
				{
					$group_info["affiliate"] = ($group_info["affiliate"]) ? 1 : 0;
					$this->sql_query = "update ".$this->classified_groups_table." set
						name = \"".$group_info["name"]."\",
						description = \"".$group_info["description"]."\",
						registration_splash_code = \"".trim(addslashes($group_info["registration_splash_code"]))."\",
						place_an_ad_splash_code = \"".trim(addslashes($group_info["place_an_ad_splash_code"]))."\",
						sponsored_by_code = \"".trim(addslashes($group_info["sponsored_by_code"]))."\",
						affiliate = \"".$group_info["affiliate"]."\",
						registration_code = \"".$group_info["registration_code"]."\"
						where group_id = ".$group_id;
					//echo $this->sql_query."<br>\n";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						//echo $this->sql_query."<br>\n";
						$this->error_message = $this->internal_error_message;
						return false;
					}

					//STOREFRONT CODE
					if(is_file('storefront/admin_store.php'))
					{
						include_once('storefront/admin_store.php');
						$group_info["storefront"] = ($group_info["storefront"]) ? 1 : 0;
						$this->sql_query = "update ".$this->classified_groups_table." set
							storefront = \"".$group_info["storefront"]."\"
							where group_id = ".$group_id;
						//echo $this->sql_query."<br>\n";
						$result = $db->Execute($this->sql_query);
						if (!$result)
						{
							echo $this->sql_query."<br>\n";
							$this->error_message = $this->internal_error_message;
							return false;
						}
						$this->sql_query = "delete from ".Admin_store::get("storefront_group_subscriptions_choices_table")."
						where group_id = $group_id";
						$result = $db->Execute($this->sql_query);
						if (!$result)
						{
							echo $this->sql_query."<br>\n";
							$this->error_message = $this->internal_error_message;
							return false;
						}
						if($group_info["storefront_periods"])
						{
							foreach($group_info["storefront_periods"] as $period_id)
							{
								$this->sql_query = "insert into ".Admin_store::get("storefront_group_subscriptions_choices_table")."
								(group_id, choice_id)
								values
								($group_id, $period_id)";
								$result = $db->Execute($this->sql_query);
								if (!$result)
								{
									echo $this->sql_query."<br>\n";
									$this->error_message = $this->internal_error_message;
									return false;
								}
							}
						}
					}
					//STOREFRONT CODE
				}
				else
				{
					$this->body .= "<span class=medium_error_font>That registration code already exists, please try again</span><br>\n";
				}

				while ($show = $language_result->FetchRow())
				{
					$this->sql_query = "update ".$this->affiliate_templates_table." set
						template_id = \"".$group_info[$show["language_id"]]["template_id"]."\",
						secondary_template_id = \"".$group_info[$show["language_id"]]["secondary_template_id"]."\",
						extra_question_template_id = \"".$group_info[$show["language_id"]]["extra_question_template_id"]."\",
						checkbox_question_template_id = \"".$group_info[$show["language_id"]]["checkbox_question_template_id"]."\",
						ad_display_template_id = \"".$group_info[$show["language_id"]]["ad_display_template_id"]."\",
						auctions_extra_question_template_id = \"".$group_info[$show["language_id"]]["auctions_extra_question_template_id"]."\",
						auctions_checkbox_question_template_id = \"".$group_info[$show["language_id"]]["auctions_checkbox_question_template_id"]."\",
						auctions_display_template_id = \"".$group_info[$show["language_id"]]["auctions_display_template_id"]."\"
						where group_id = ".$group_id." and language_id = ".$show["language_id"];
					$result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[3500];
						return false;
					}


					//echo $show["language_id"]." is the language_id at end<br>\n";
				}
				return true;
			}
			else
				return false;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_group_info

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_registration_code($db,$group_id,$registration_code)
	{
		if (strlen(trim($registration_code)) > 0)
		{
			$this->sql_query = "select * from ".$this->classified_groups_table." where group_id != ".$group_id." and registration_code = \"".$registration_code."\"";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return true;
		}
	} //end of function check_registration_code

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function group_multiple_price_plan_form($db,$group_id,$product_type)
	{
		if ($group_id && $product_type)
		{
			$item_name = ($product_type == 1) ? "Classified Ad" : "Auction";

			$sql_query = "select * from ".$this->classified_groups_table." where group_id = ".$group_id;
			$group_result = $db->Execute($sql_query);
			if ((!$group_result) && ($group_result->RecordCount() != 1))
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			$group_data = $group_result->FetchRow();

			$sql_query = "select * from ".$this->attached_price_plans." where group_id = ".$group_id." and applies_to = ".$product_type;
			$attached_result = $db->Execute($sql_query);
			if (!$attached_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			$group_name = $this->get_group_name($db,$group_id);
			$this->title = "Users / User Groups > Additional Price Plans - Listing Type: $item_name";
			$this->description = "The price plans listed below will become the only choices within the listing process when the user
				<b>in this User Group</b> is listing <b>$item_name(s)</b>.  If no additional price plans (beyond the one originally attached)
				are added below the price plan originally attached to this group will remain the default price plan.  However, if there
				<b>\"are\"</b> additional Price Plan choices specified below, when a user lists an item these \"Additional Price Plans\" will
				become choices for the user to select from for that $item_name. In this case, <b>\"only\"</b> the price plans listed below will
				become choices.  Therefore, if you still want the \"default\" User Group Price Plan to apply, you should also attach the default
				Price Plan below.<br><br><b>IMPORTANT:</b> The text that you enter within the <b>\"Name\"</b> and <b>\"Description\"</b> fields
				below is the text that your users will see as Price Plan choices to select from during the listing process.";
			if ($this->is_class_auctions())
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=36&b=8&g=".$group_id."&t=".$product_type." method=post>";
					$this->body .= "<table width=100% cellpadding=3 cellspacing=1 border=0 align=center class=row_color1>
						<tr bgcolor=000066>
							<td colspan=4 align=center class=large_font_light>
								<b>User Group: ".$group_name."</b>
							</td>
						</tr>
						<tr class=row_color_black>
							<td align=center class=medium_font_light>
								<b>Additional Price Plan(s)</b>
							</td>
							<td align=center class=medium_font_light>
								<b>Name</b>
							</td>
							<td align=center class=medium_font_light>
								<b>Description</b>
							</td>
							<td>
								&nbsp;
							</td>
						</tr>\n";

			if ($attached_result->RecordCount() > 0)
			{
				$this->row_count = 0;
				$in_already_listed = "";
				while ($show_attached = $attached_result->FetchRow())
				{
					$price_plan_name = $this->get_price_plan_name($db,$show_attached["price_plan_id"]);
					if (strlen($in_already_listed) > 0)
						$in_already_listed .= ",".$show_attached["price_plan_id"];
					else
						$in_already_listed = $show_attached["price_plan_id"];
					$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=center class=medium_font>
								".$price_plan_name."
							</td>
							<td align=center class=medium_font>
								".$show_attached["name"]."
							</td>
							<td align=center class=medium_font>
								".$show_attached["description"]."
							</td>
							<td align=center width=100>
								<a href=index.php?a=36&b=9&g=".$group_id."&p=".$show_attached["price_plan_id"]."&t=".$product_type.">
									<span class=medium_font>
										<img src=admin_images/btn_admin_delete.gif alt=delete border=0>
									</span>
								</a>
							</td>
						</tr>";
					$this->row_count++;
				}
			}
			else
			{
					$this->body .= "
						<tr>
							<td align=center colspan=4 class=medium_font>
								<br><br><b>No additional Price Plans are currently attached to this User Group.<br>Therefore, this User Group's \"default\" Price Plan will apply. Use the fields below<br> to enter an 'Additional Price Plan'.</b><br><br>
							</td>";
			}

			$in_already_listed = ($in_already_listed) ? $in_already_listed : "NULL";
			//get list of price plans
			$sql_query = "SELECT * FROM $this->price_plan_table
				WHERE
					type_of_billing != 2 AND
					applies_to = $product_type";
			if ($in_already_listed != "NULL")
				$sql_query .= " AND price_plan_id NOT IN(".$in_already_listed.")";
			$price_plan_result = $db->Execute($sql_query);
			if (!$price_plan_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($price_plan_result->RecordCount() > 0)
			{
				$this->body .= "
						<tr>
							<td align=center>
								<select name=p[price_plan_id]>";
				while ($price_plan = $price_plan_result->FetchRow())
				{
					$this->body .= "<option value=".$price_plan["price_plan_id"].">
										".$price_plan["name"]."
									</option>";
				}
				$this->body .= "</select>
							</td>
							<td align=center class=medium_font>
								<input type=text name=p[name]>
							</td>
							<td align=center class=medium_font>
								<textarea name=p[description]></textarea>
							</td>
							<td align=center>";
				if (!$this->admin_demo())	$this->body .= "<input type=submit name=submit value=\"attach plan\">";
				$this->body .= "</td>
						</tr>";
			}
			$this->body .= "
					</table>
				</form>
				<tr><td class=medium_font><a href=index.php?a=36&b=4&c=$group_id><b>back to Edit Details for this User Group</b></a></td></tr>";

			return true;
		}
	} //end of function group_multiple_price_plan_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function add_attached_price_plan($db,$group_id,$price_plan,$product_type)
	{
		if ($group_id && $price_plan && $product_type)
		{
			$sql_query = "select * from  ".$this->attached_price_plans." where group_id = ".$group_id." and price_plan_id = ".$price_plan["price_plan_id"];
			$result = $db->Execute($sql_query);
			//echo $sql_query."<br>\n";
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 0 )
			{
				$sql_query = "insert into ".$this->attached_price_plans."
					(group_id,price_plan_id,name,description,applies_to)
					values
					(".$group_id.",".$price_plan["price_plan_id"].",\"".$price_plan["name"]."\",\"".$price_plan["description"]."\",\"".$product_type."\")";
				$result = $db->Execute($sql_query);
				//echo $sql_query."<br>\n";
				if (!$result)
				{
					$this->site_error($db->ErrorMsg());
					return false;
				}
				else
				{
					return true;
				}
			}
			else
			{
				$this->ad_configuration_message = "That value already exists";
				return true;
			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_ad_configuration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_attached_price_plan($db,$group_id,$price_plan_id)
	{
		if ($group_id &&  $price_plan_id)
		{
			$sql_query = "delete from  ".$this->attached_price_plans." where group_id = ".$group_id." and price_plan_id = ".$price_plan_id;
			$result = $db->Execute($sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function delete_attached_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function price_plan_registration_freebies_form($db,$group_id=0)
	{
		if ($group_id)
		{
			$group = $this->get_group($db, $group_id);
			if($this->is_classifieds() || $this->is_class_auctions())
			{
				$show_price_plan = $this->get_price_plan($db, $group['price_plan_id']);
			}
			elseif($this->is_auctions())
			{
				$show_price_plan = $this->get_price_plan($db, $group['auction_price_plan_id']);
			}

			if ($show_price_plan)
			{
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=36&b=10&g=".$group_id." method=post>\n";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
				$this->title .= "Users / User Groups > ".$group['name']." > Registration Specifics";
				$this->description = "Edit detailed registration promotions for the group through this form.  The options you see in this form are affected by the
					choice you made for the type of the price plans you made in the price plan type form (subscription or
					fee based).  Make sure you have chosen the option in that form that you want.  If not create a new price plan
					with the correct choice and discard this price plan (if you don't need it).  Make your changes then click the \"save\" button at the bottom.
					<br><b>Be mindful of the effects certain
					choices could have on people currently on this pricing plan</b>
					<br><b>Changes in expirations below will not affect credits already issued by your site.
					</b> \n\t</td>\n</tr>\n";

				if (($show_price_plan["type_of_billing"] == 1) || $different)
				{
					//fee based price plans
					//credits upon registration
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font colspan=2 align=center>\n\tCredits issued upon registration\n\t
						<select name=h[credits_upon_registration]>\n\t\t";
					for ($i=0;$i < 1000;$i++)
					{
						$this->body .= "<option ";
						if ($show_price_plan["credits_upon_registration"] == $i)
							$this->body .= "selected";
						$this->body .= ">".$i."</option>\n\t\t";
					}
					$this->body .= "</select></td>\n\t</tr>\n";
					$this->row_count++;

					if($this->configuration_data['positive_balances_only'] && $this->configuration_data['use_account_balance'])
					{
						// Show Initial Site Balance
						$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font colspan=2>\n\tInitial Site Balance\n\t";
						$this->charge_select_box($show_price_plan["initial_site_balance"],"h[initial_site_balance]");
						$this->body .= "</td>\n\t</tr>\n";
						$this->row_count++;
					}

					//credit expiration type
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td colspan=2 class=medium_font>\n\t<b>How do these credits expire?</b>\n\t</td>\n</tr>\n";
					$this->body .= "<tr>\n\t<td colspan=2>\n\t
						<input type=radio name=h[credits_expire_type] ";
					if ($show_price_plan["credits_expire_type"] == 1)
						$this->body .= "checked";
					$this->body .= " value=1>\n\t
						\n\t<span class=medium_font>time period from registration</span>\n\t
						\n\t";
					$this->subscription_period_dropdown($db,$show_price_plan["credits_expire_period"],"h[credits_expire_period]");
					$this->body .= "</td>\n\t</tr>\n";
					$this->row_count++;

					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font colspan=2>\n\t<input type=radio name=h[credits_expire_type] ";
					if ($show_price_plan["credits_expire_type"] == 2)
						$this->body .= "checked";
					$this->body .= " value=2>\n\t
						\n\tfixed expiration date\n\t
						\n\t";
					$this->date_dropdown($show_price_plan["credits_expire_date"],"h[credits_expire_date]");
					$this->body .= "</td>\n\t</tr>\n";
					$this->row_count++;
				}
				elseif ($show_price_plan["type_of_billing"] == 2)
				{
					//subscription based price plans
					//free subscription period from registration
					$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\tfree subscription period upon registration \n\t</td>\n\t";
					$this->body .= "<td>\n\t";
					$this->subscription_period_dropdown($db, $show_price_plan["free_subscription_period_upon_registration"],"h[free_subscription_period_upon_registration]");
					$this->body .= "</td>\n\t</tr>\n";
				}
				if (!$this->admin_demo())$this->body .= "<tr>\n\t<td colspan=2 align=center>\n\t<input type=submit name=save_group value=\"Save\">\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td colspan=2>\n\t<a href=index.php?a=36><span class=medium_font>\n\t
					<b>back to User Groups Home</b></span></a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
				$this->body .= "</form>\n";
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	} //end of function price_plan_registration_freebies_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_price_plan_registration_freebies($db,$group_id=0,$group_info=0)
	{
		if (($group_id) && ($group_info))
		{
			$group = $this->get_group($db, $group_id);
			if(!$group)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}

			$this->sql_query = "update ".$this->price_plan_table." set ";
			$expiration_date = mktime(0,0,0,$group_info["credits_expire_date"]["month"],$group_info["credits_expire_date"]["day"],$price_plan_info["credits_expire_date"]["year"]);
			$this->sql_query .= "credits_upon_registration = \"".$group_info["credits_upon_registration"]."\",
					credits_expire_type = \"".$group_info["credits_expire_type"]."\",
					credits_expire_date = ".$expiration_date.",
					credits_expire_period = \"".$group_info["credits_expire_period"]."\",
					initial_site_balance = ".$group_info["initial_site_balance"][0].".".sprintf("%02d", $group_info["initial_site_balance"][1]).",
					free_subscription_period_upon_registration = \"".$group_info["free_subscription_period_upon_registration"]."\"";

			if($this->is_class_auctions())
				$this->sql_query .= " where price_plan_id in (".$group['price_plan_id'].", ".$group['auction_price_plan_id'].")";
			elseif($this->is_auctions())
				$this->sql_query .= " where price_plan_id = ".$group['auction_price_plan_id'];
			elseif($this->is_classifieds())
				$this->sql_query .= " where price_plan_id = ".$group['price_plan_id'];

			//echo $this->sql_query.'<Br>';
			$result = $db->Execute($this->sql_query);
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
	} //end of function update_price_plan_registration_freebies

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function compare_price_plan_types($db,$auction_price_plan_id=0,$classified_price_plan_id=0)
	{
		$this->sql_query = "SELECT type_of_billing FROM ".$this->price_plan_table." WHERE price_plan_id = ".$auction_price_plan_id;
		if ($this->debug_groups) echo "<BR>QUERY(LINE ".__LINE__.") - ".$this->sql_query;
		$auc_result = $db->Execute($this->sql_query);
		if (!$auc_result)
		{
			if ($this->debug_groups)
				$this->new_group_error .= "<br>admin_group_management.php LINE ".__LINE__."<br>";
			$this->error_message = $this->internal_error_message;
			return false;
		}
		$this->sql_query = "SELECT type_of_billing FROM ".$this->price_plan_table." WHERE price_plan_id = ".$classified_price_plan_id;
		if ($this->debug_groups) echo "<BR>QUERY(LINE ".__LINE__.") - ".$this->sql_query;
		$class_result = $db->Execute($this->sql_query);
		if (!$class_result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		if ($auc_result->RecordCount()==1 && $class_result->RecordCount()==1)
		{
			$auc = $auc_result->FetchRow();
			$class = $class_result->FetchRow();
			if ($auc["type_of_billing"]==$class["type_of_billing"])
				return true;
			else
			{
				if ($this->debug_groups)
					$this->new_group_error .= "<br>admin_group_management.php LINE ".__LINE__."<br>";
				return false;
			}
		}
		else
		{
			if ($this->debug_groups)
				$this->new_group_error .= "<br>admin_group_management.php LINE ".__LINE__."<br>";
			$this->error_message = $this->internal_error_message;
			return false;
		}
	}//end of function compare_price_plan_types

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Group_management


?>
