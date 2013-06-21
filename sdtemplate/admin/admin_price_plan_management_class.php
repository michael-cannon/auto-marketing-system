<?// admin_price_plan_management_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
\**************************************************************************/

class Price_plan_management extends Admin_site {

	var $debug_price_plan = 0;
	var $last_high_variable = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Price_plan_management($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_price_plan_list($db)
	{
		$this->sql_query = "SELECT price_plan_id FROM ".$this->price_plan_table." WHERE applies_to=2 ORDER BY price_plan_id ASC";
		$count_result = $db->Execute($this->sql_query);
		if (!$count_result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		if ($count_result->RecordCount() > 0)
			$primary_auc = $count_result->FetchRow();
		$primary_auc_id = ($count_result->RecordCount() > 0) ? $primary_auc["price_plan_id"] : 0;

		$this->sql_query = "select * from ".$this->price_plan_table;
		if($this->is_auctions())
			$this->sql_query .= " where applies_to = 2";
		elseif($this->is_classifieds())
			$this->sql_query .= " where applies_to = 1";
		$this->sql_query .= " order by applies_to, name";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			$this->body .= "
				<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Pricing > Price Plans Home";
			$this->description = "Create and delete price plans through this
				admin tool.  You can organize your price plans on a grouping basis.  Each group within your site will have a price plan
				attached to them.  This will be the pricing plan they use to list items on your site.  Pricing plans can be very inventive
				so explore all your options.";
			$this->body .= "
					<tr>
						<td align=center>
							<table cellpadding=3 cellspacing=1 border=0 class=row_color2 width=100%>
								<tr bgcolor=000066>
									<td align=center colspan=".(($this->is_class_auctions()) ? "7" : "6")." class=large_font_light>Current Price Plans </td>
								</tr>
								<tr class=row_color_red align=center>
									<td align=left class=medium_font_light width=40%><b>Price Plan Name</b></td>
									<td nowrap class=medium_font_light><b># of Users</b></td>";

			if($this->is_class_auctions())
				$this->body .= "
									<td class=medium_font_light><b>Applies to</b></td>";
			$this->body .= "
									<td class=medium_font_light><b>Type</b></td>
									<td colspan=2 class=medium_font_light width=30%><b>Actions</b></td>
								</tr>";

			$this->row_count = 0;
			$error=0;
			while ($show = $result->FetchRow())
			{
				$this->sql_query = "SELECT * FROM ".$this->classified_subscription_choices_table." WHERE price_plan_id = ".$show["price_plan_id"]."";
				$sub_choice_result = $db->Execute($this->sql_query);
				if ($plan_count_result===false)
					return false;
				if ($show["type_of_billing"]==2 && $sub_choice_result->RecordCount()==0)
				{
					$error++;
					$medium_font = "medium_error_font";
					if ($error==1)
						$this->description .= "<br><b>You currently have one or more subscription price plan(s) (<span class=medium_error_font>listed in
							red</span>) that needs subscription periods in order to be available to the groups.</b>";
				}
				else
					$medium_font = "medium_font";

				if(($this->is_class_auctions() || $this->is_classifieds()) && $show["applies_to"]==1)
				{
					$this->sql_query = "select count(*) as price_plan_total from ".$this->user_groups_price_plans_table." where price_plan_id = ".$show["price_plan_id"]." and id!=1";
					$plan_count_result = $db->Execute($this->sql_query);
					if (!$plan_count_result)
					{
						//echo $this->sql_query."<br>\n";
						return false;
					}
					elseif ($plan_count_result->RecordCount() == 1)
						$show_plan_count = $plan_count_result->FetchRow();
				}
				if(($this->is_class_auctions() || $this->is_auctions()) && $show["applies_to"]==2)
				{
					$this->sql_query = "select count(*) as price_plan_total from ".$this->user_groups_price_plans_table." where auction_price_plan_id = ".$show["price_plan_id"]." and id!=1";
					$plan_count_result = $db->Execute($this->sql_query);
					if (!$plan_count_result)
					{
						//echo $this->sql_query."<br>\n";
						return false;
					}
					elseif ($plan_count_result->RecordCount() == 1)
						$show_plan_count = $plan_count_result->FetchRow();
				}

				$this->body .= "<tr align=center class=".$this->get_row_color().">
									<td align=left class=$medium_font><b>".$show["name"]."</b><br>".$show["description"]."</td>";

				$this->body .= "<td class=medium_font>".$show_plan_count["price_plan_total"]." </td>";

				if($this->is_class_auctions())
					$this->body .= "
									<td class=medium_font>".(($show["applies_to"] == 1) ? "Classifieds" : "Auctions")."</td>";
				$this->body .= "
									<td class=medium_font>".($show["type_of_billing"] == 1 ? "Fee-based" : "Subscription")."</td>
									<td class=medium_font width=10%><a href=index.php?a=37&b=3&g=".$show["price_plan_id"]."><img src=admin_images/btn_admin_edit.gif alt=edit border=0></a></td>";
				if ($show["price_plan_id"]!=1 && $show["price_plan_id"]!=$primary_auc_id)
					$this->body .= "
									<td width=10%><a href=index.php?a=37&b=2&c=".$show["price_plan_id"]."><span class=medium_font>\n\t<img src=admin_images/btn_admin_delete.gif alt=delete border=0></a></span></td>";
				else
					$this->body .= "
									<td class=medium_font width=10%>&nbsp; </td>";
				$this->body .= "
								</tr>";
				$this->row_count++;
			}
			$this->body .= "
							</table>
						</td>
					</tr>
					<tr>
						<td align=center><a href=index.php?a=37&b=1><span class=medium_font>\n\t<b>add new price plan</b></span></a></td>
					</tr>
				</table>
				</form>";
			return true;
		}

	} //end of function display_price_plan_list

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_price_plan($db,$price_plan_info=0)
	{
		if ($price_plan_info)
		{
			if ((strlen(trim($price_plan_info["name"])) > 0) && ($price_plan_info["type_of_billing"]))
			{
				$this->sql_query = "insert into ".$this->price_plan_table."
					(name,description,type_of_billing, max_ads_allowed, applies_to)
					values
					(\"".$price_plan_info["name"]."\",\"".$price_plan_info["description"]."\",".$price_plan_info["type_of_billing"].", 1000, ".$price_plan_info["applies_to"].")";
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>";
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
			elseif (strlen(trim($price_plan_info["description"])) == 0)
			{
				echo "the description for the price plan is empty....please fill one in<BR>";
			}
			elseif (strlen(trim($price_plan_info["name"])) == 0)
			{
				echo "the name for the price plan is empty....please fill one in<BR>";
			}
			else
				return true;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function insert_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_price_plan($db,$price_plan_id=0)
	{
		$this->sql_query = "SELECT price_plan_id FROM ".$this->price_plan_table." WHERE applies_to=2 ORDER BY price_plan_id ASC";
		$count_result = $db->Execute($this->sql_query);
		if (!$count_result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		if ($count_result->RecordCount() > 0)
			$primary_auc = $count_result->FetchRow();
		$primary_auc_id = ($count_result->RecordCount() > 0) ? $primary_auc["price_plan_id"] : 0;
		if ($price_plan_id && $price_plan_id!=1 && $price_plan_id!=$primary_auc_id)
		{
			$this->sql_query = "delete from ".$this->price_plan_table."
				where price_plan_id = ".$price_plan_id;
			//echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			else
			{
				$this->sql_query = "delete from ".$this->classified_price_plans_categories_table."
					where price_plan_id = ".$price_plan_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}

				$this->sql_query = "delete from ".$this->price_plans_increments_table."
					where price_plan_id = ".$price_plan_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}

				$this->sql_query = "delete from ".$this->price_plans_extras_table."
					where price_plan_id = ".$price_plan_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}

				$this->sql_query = "delete from ".$this->classified_price_plan_lengths_table."
					where price_plan_id = ".$price_plan_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}

				$this->sql_query = "delete from ".$this->attached_price_plans."
					where price_plan_id = ".$price_plan_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}

				return true;
			}
		}
		elseif ($price_plan_id == 1)
		{
			return true;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function delete_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function move_to_price_plan($db,$price_plan_from=0,$price_plan_to=0)
	{
		if (($price_plan_from) && ($price_plan_from != $price_plan_to))
		{
			if ($price_plan_to)
			{
				$this->sql_query = "select * from ".$this->price_plan_table." where price_plan_id = ".$price_plan_to;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$this->sql_query = "update ".$this->user_groups_price_plans_table." set
						price_plan_id = ".$price_plan_to."
						where price_plan_id = ".$price_plan_from;
					$result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$result)
					{
						$this->error_message = $this->internal_error_message;
						return false;
					}

					$this->sql_query = "update ".$this->classified_groups_table." set
						price_plan_id = ".$price_plan_to."
						where price_plan_id = ".$price_plan_from;
					$result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$result)
					{
						$this->error_message = $this->internal_error_message;
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
	} //end of function move_to_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function price_plan_home($db,$price_plan_id=0)
	{
		if ($price_plan_id)
		{
			$price_plan_name = $this->get_price_plan_name($db,$price_plan_id);
			$price_plan = $this->get_price_plan($db,$price_plan_id);
			if ($price_plan_name)
			{
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
				$this->title = "Pricing > Price Plans > Edit";
				$this->description = "Edit the <b>".$price_plan_name."</b> price plan details through this
					admin tool.  Click on the aspect of this price plan you wish to edit by clicking on the appropriate link below.";

				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=5 class=large_font_light align=center>\n\t<b>Price Plan: ".$price_plan_name."</b>\n\t</td>\n</tr>\n";
				// Name and description
				$this->body .= "<tr class=row_color1>\n\t<td width=10%>&nbsp;</td>\n\t<td>\n\t<br><a href=index.php?a=37&b=3&f=5&g=".$price_plan_id."><span class=medium_font>\n\t
				<b>name and description</b></span></a><br><span class=small_font>how it appears in the price plan list</span>\n\t</td>\n</tr>\n";

				// Expiration of plan
				$this->body .= "<tr class=row_color2>\n\t<td width=10%>&nbsp;</td>\n\t<td>\n\t<a href=index.php?a=37&b=3&f=1&g=".$price_plan_id."><span class=medium_font>\n\t
					<b>expiration of plan</b></span></a><br><span class=small_font>if and when this price plan expires</span>\n\t</td>\n</tr>\n";

				// Cost specifics
				$this->body .= "<tr class=row_color1>\n\t<td width=10%>&nbsp;</td>\n\t<td>\n\t<a href=index.php?a=37&b=3&f=3&g=".$price_plan_id."><span class=medium_font>\n\t
					<b>cost specifics</b></span></a><br><span class=small_font>cost structure for this price plan whether subscription or fee based.</span>\n\t</td>\n</tr>\n";

				if(!$this->is_class_auctions())
				{
					// Registration specifics
					$this->body .= "<tr class=row_color2>\n\t<td width=10%>&nbsp;</td>\n\t<td>\n\t<a href=index.php?a=37&b=3&f=4&g=".$price_plan_id."><span class=medium_font>\n\t
						<b>registration specifics</b></span></a><br><span class=small_font>any special procedures available when a user registers</span>\n\t</td>\n</tr>\n";
				}

				// Separator bar
				$this->body .= "<tr class=row_color_black>\n\t<td colspan=3>\n\t</td>\n</tr>\n";

				if ($price_plan["type_of_billing"] == 2)
				{
					$this->body .= "<tr class=row_color1>\n\t<td width=10%>&nbsp;</td>\n\t<td>\n\t<a href=index.php?a=37&b=3&f=8&g=".$price_plan_id."><span class=medium_font>\n\t
						<b>subscription periods</b></span></a><br><span class=small_font>choose availabe subscription periods and associated subscription fees</span>\n\t</td>\n</tr>\n";
				}
				else
				{
					// Category specific Costs
					$this->body .= "<tr class=row_color2>\n\t<td width=10%>&nbsp;</td>\n\t<td>\n\t<a href=index.php?a=37&b=5&x=".$price_plan_id."><span class=medium_font>\n\t
					<b>category specific costs</b></span></a><br><span class=small_font>add a category specific cost structure for this fee-based price plan.</span>\n\t</td>\n</tr>\n";
				}

				$this->body .= "</table>\n";
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
	} //end of function price_plan_home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function price_plan_expiration_form($db,$price_plan_id=0)
	{
		if ($price_plan_id)
		{
			$price_plan_name = $this->get_price_plan_name($db,$price_plan_id);
			if ($price_plan_name)
			{
				$this->sql_query = "select * from ".$this->price_plan_table." where price_plan_id = ".$price_plan_id;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show_price_plan = $result->FetchRow();
				}
				else
				{
					return false;
				}

				$fixed_price_plan_expiration = $this->get_price_plan_expiration($db,$price_plan_id);
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=3&f=1&g=".$price_plan_id." method=post>\n";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
				$this->title = "Pricing > Price Plans > Edit Expiration";
				$this->description = "Edit a price plan details through this
					admin tool.  Make your changes then click the \"save\" button at the bottom. <br>";
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=5 class=large_font_light align=center>\n\t<b>Price Plan: ".$price_plan_name."</b>\n\t</td>\n</tr>\n";

				//expire on fixed date
				$this->body .= "<tr class=row_color2>\n\t<td align=right width=10% valign=top>\n\t<input type=radio name=d[expiration_type] ";
				if ($show_price_plan["expiration_type"] == 1)
					$this->body .= "checked";
				$this->body .= " value=1>\n\t</td>\n\t";
				$this->body .= "<td width=50% class=medium_font><b>\n\tExpire by fixed date</b><br>Choose this option to expire this price plan on a fixed
					date of your choosing.  Upon that fixed date this price plan will revert into a price plan you choose to the right.  This
					changes the price plan for all users of this price plan at once no matter what group they are in.<br>";

				$this->date_dropdown($fixed_price_plan_expiration,"d[fixed_expire_date]");

				$this->body .= " \n\t</td>\n\t";
				$this->body .= "<td width=40% rowspan=3 valign=center align=center class=row_color_red><span class=large_font_light>\n\t
					Choose price plan to replace expired plan with here:</span><br>\n\t
					<br><select name=d[price_plan_expires_into]>\n\t\t";
				$this->sql_query = "select name,price_plan_id from ".$this->price_plan_table." order by name";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}
				elseif ($result->RecordCount() > 0)
				{
					while ($show = $result->FetchRow())
					{
						if ($show["price_plan_id"] != $show_price_plan["price_plan_id"])
						{
							$this->body .= "<option value=".$show["price_plan_id"];
							if ($show_price_plan["price_plan_expires_into"] == $show["price_plan_id"])
								$this->body .= " selected";
							$this->body .= ">".$show["name"]."</option>\n\t\t";

						}
					}
				}
				$this->body .= "</select></td>\n</tr>\n";

				//expire on length of time from registration
				$this->body .= "<tr>\n\t<td align=right width=10% valign=top>\n\t<input type=radio name=d[expiration_type] ";
				if ($show_price_plan["expiration_type"] == 2)
					$this->body .= "checked";
				$this->body .= " value=2>\n\t</td>\n\t";
				$this->body .= "<td width=50% class=medium_font><b>\n\tExpire by time period from registration</b><br>Choose this option to expire this price plan on a fixed
					period of time from the date of registration.  Upon that dynamic date this price plan will revert into a price plan you choose to the right.
					This means that users will be moved from this price plan at different times because the expiration is set from the
					date they register.  Using this option may leave various users within the same group on different price plans. Changing the
					date in this option will not affect the current expirations already set for users currently in this price plan.  This
					will only affect future users joining this price plan.  If you are changing from one of the other <br>";
				$this->body .= "days from registration to expire";
				$this->subscription_period_dropdown($db,$show_price_plan["expiration_from_registration"],"d[expiration_from_registration]");
				$this->body .= " \n\t</td>\n\t</tr>\n";

				//never expire price plan
				$this->body .= "<tr class=row_color2>\n\t<td align=right valign=top>\n\t<input type=radio name=d[expiration_type] ";
				if ($show_price_plan["expiration_type"] == 0)
					$this->body .= "checked";
				$this->body .= " value=0>\n\t</td>\n\t";
				$this->body .= "<td class=medium_font><b>\n\tNever Expire</b><br>Choose this option to never expire this price plan. If there is an expiration
					currently on this price plan and this option is chosen the expirations on this price plan will be removed.  This
					will remove the expiration no matter the type of expiration previously set.  The fixed expiration date will be
					removed if that was the expiration type.  All \"dynamic\" expiration dates set from registration will be removed
					also.  This will not affect those users who were on this price plan and they have already expired into another
					price plan.  This affects all users and groups currently on this price plan.   \n\t</td>\n\t</tr>\n\t";
				$this->body .= "</td>\n</tr>\n";
				if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=3 class=medium_font align=center>\n\t<input type=submit name=submit value=\"Save\"> \n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td colspan=3>\n\t<a href=index.php?a=37&b=3&g=".$price_plan_id."><span class=medium_font>\n\t
					<b>back to ".$price_plan_name." Details</b></span></a>\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td colspan=3>\n\t<a href=index.php?a=37><span class=medium_font>\n\t
					<b>back to Price Plan Home</b></span></a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
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
	} //end of function price_plan_home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_price_plan_expiration($db,$price_plan_id=0,$price_plan_info=0)
	{
		if (($price_plan_info) && ($price_plan_id))
		{
			//echo $price_plan_info["expiration_type"]." is expiration type in update<br>\n";
			switch ($price_plan_info["expiration_type"])
			{
				case 0:
					//remove expirations
					$this->sql_query = "update ".$this->price_plan_table." set
						expiration_type = 0,
						expiration_from_registration = 0,
						price_plan_expires_into = 0
						where price_plan_id = ".$price_plan_id;
					$result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$result)
					{
						$this->error_message = $this->internal_error_message;
						return false;
					}
					$this->sql_query = "delete from ".$this->classified_expirations_table."
						where type_id = ".$price_plan_id." and type = 2";
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
					break;

				case 1:
					//fixed date expiration
					//remove current expirations
					$this->sql_query = "update ".$this->price_plan_table." set
						expiration_type = 1,
						expiration_from_registration = 0,
						price_plan_expires_into = ".$price_plan_info["price_plan_expires_into"]."
						where price_plan_id = ".$price_plan_id;
					$result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$result)
					{
						$this->error_message = $this->internal_error_message;
						return false;
					}
					$this->sql_query = "select * from ".$this->classified_expirations_table." where type_id = ".$price_plan_id." and type = 2";
					$current_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$current_result)
					{
						$this->error_message = $this->internal_error_message;
						return false;
					}
					elseif ($current_result->RecordCount() < 2)
					{
						//there is either no or one present expiration
						$this->sql_query = "delete from ".$this->classified_expirations_table."
							where type_id = ".$price_plan_id." and type = 2";
						$result = $db->Execute($this->sql_query);
						//echo $this->sql_query."<br>\n";
						if (!$result)
						{
							$this->error_message = $this->internal_error_message;
							return false;
						}
						else
						{
							$expiration_date = mktime(0,0,0,$price_plan_info["fixed_expire_date"]["month"],$price_plan_info["fixed_expire_date"]["day"],$price_plan_info["fixed_expire_date"]["year"]);
							$this->sql_query = "insert into ".$this->classified_expirations_table."
								(type,expires,type_id)
								values
								(2,".$expiration_date.",".$price_plan_id.")";
							$insert_result = $db->Execute($this->sql_query);
							//echo $this->sql_query."<br>\n";
							if (!$insert_result)
							{
								//oops
								//put the expiration returned in the current result back (if there was one)
								//since a new one could not be added
								if ($current_result->RecordCount() == 1)
								{
									$show_current = $current_result->FetchRow();
									$this->sql_query = "insert into ".$this->classified_expirations_table."
										(type,expires,type_id)
										values
										(2,".$show_current["expires"].",".$price_plan_id.")";
									$insert_result = $db->Execute($this->sql_query);
									//echo $this->sql_query."<br>\n";
								}
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
						return false;
					}
					break;

				case 2:
					//dynamic expiration from registration
					//first remove the fixed expiration
					$this->sql_query = "delete from ".$this->classified_expirations_table."
						where type_id = ".$price_plan_id." and type = 2 and user_id = 0";
					$result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$result)
					{
						$this->error_message = $this->internal_error_message;
						return false;
					}
					$this->sql_query = "update ".$this->price_plan_table." set
						expiration_type = 2,
						expiration_from_registration = ".$price_plan_info["expiration_from_registration"].",
						price_plan_expires_into = ".$price_plan_info["price_plan_expires_into"]."
						where price_plan_id = ".$price_plan_id;
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
					break;
			} // end of switch

		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_price_plan_expiration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function price_plan_form($db,$price_plan_id=0)
	{
		if ($price_plan_id)
		{
			//edit this price plan form
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=3&f=5&g=".$price_plan_id." method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Pricing > Price Plans > Edit Name and Description";
			$this->description = "Edit a price plan details through this admin tool.  Make your changes then click the \"save\" button at the bottom.";
			$this->sql_query = "select * from ".$this->price_plan_table." where price_plan_id = ".$price_plan_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_price_plan = $result->FetchRow();
			}
			else
			{
				return false;
			}
		}
		else
		{
			//insert new price plan form
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=1 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Pricing > Price Plans > New Price Plan";
			$this->description = "Create a new price plan through this
				admin tool.  The type of price plan set below cannot be changed once saved below.  But if necessary you can always
				move one price plan into another at any time.
				Insert the details below then click the \"save\" button at the bottom.";
		}


		$this->body .= "<tr>\n\t<td align=right width=30% class=medium_font>\n\tPrice Plan: \n\t</td>\n\t";
		$this->body .= "<td width=70%><input type=text name=d[name] size=30 maxsize=30 value=\"".$show_price_plan["name"]."\">\n\t</td>\n</tr>\n";
		if($this->is_class_auctions())
		{
			$this->body .= "
				<tr>
					<td align=right width=30% class=medium_font>Applies to:</td>
					<td width=70% class=medium_font>
						<input type=radio name=d[applies_to] value=1".(($show_price_plan["applies_to"] == 1) ? " checked" : "")." class=medium_font> Classifieds<br>
						<input type=radio name=d[applies_to] value=2".(($show_price_plan["applies_to"] == 2) ? " checked" : "")." class=medium_font> Auctions
					</td>
				</tr>";
		}
		elseif($this->is_auctions())
		{
			$this->body .= "<input type=\"hidden\" name=\"d[applies_to]\" value=2>";
		}
		elseif($this->is_classifieds())
		{
			$this->body .= "<input type=\"hidden\" name=\"d[applies_to]\" value=1>";
		}
		$this->body .= "<tr>\n\t<td align=right valign=top class=medium_font>\n\tPlan Description: \n\t</td>\n\t";
		$this->body .= "<td><textarea name=d[description] rows=3 cols=30>".$this->special_chars($show_price_plan["description"])."</textarea>\n\t</td>\n</tr>\n";

		if (!$price_plan_id)
		{
			$this->body .= "<tr>\n\t<td colspan=2 class=medium_font>\n\t<b>Price Plan Type:</b> \n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td colspan=2>\n\t<table width=100%>\n\t";
			$this->body .= "<tr>\n\t<td width=5% align=right valign=top class=medium_font>\n\t<input type=radio name=d[type_of_billing] value=1 checked>\n\t</td>\n\t";
			$this->body .= "<td width=95% class=medium_font>\n\t<b>fee based</b><br>This type of price plan charges the user for each listing they
				place on your site. Extra charge items and features are then added onto that price per listing. \n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td width=5% align=right valign=top class=medium_font>\n\t<input type=radio name=d[type_of_billing] value=2>\n\t</td>\n\t";
			$this->body .= "<td class=medium_font>\n\t<b>subscription based</b><br>This type of price plan charges the user for a time
				period to place listings.  They can place as many as they want up to the limit you set.  You can set the length of the time period and
				price per time period to charge. Extra charge items and features are then added at the time of listing an item if they
				wish to have them. \n\t</td>\n</tr>\n";
			$this->body .= "</table></td>\n</tr>\n";
		}

		if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 align=center>\n\t<input type=submit name=save_group value=\"Save\">\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=2>\n\t<a href=index.php?a=37&b=3&g=".$price_plan_id."><span class=medium_font>\n\t
			<b>back to Price Plan Home</b></span></a>\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;
	} //end of function price_plan_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_price_plan_form($db,$price_plan_id)
	{
		$delete_price_plan_name = $this->get_price_plan_name($db,$price_plan_id);
		if (!$price_plan_id || $price_plan_id == 1 || !$delete_price_plan_name)
			return false;

		$current_price_plan = $this->get_price_plan($db,$price_plan_id);
		$this->title = "Pricing > Price Plans > Delete <b>".$delete_price_plan_name."</b>";

		$this->sql_query = "SELECT name,price_plan_id,applies_to,type_of_billing FROM ".$this->price_plan_table." WHERE
			price_plan_id != ".$price_plan_id." AND
			applies_to = ".$current_price_plan["applies_to"]." AND
			type_of_billing = ".$current_price_plan["type_of_billing"]." ORDER BY name";
		$price_plan_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if ($price_plan_result === false)
			return false;


		if (!$this->admin_demo())
			$this->body .= "
				<form action=index.php?a=37&b=2&c=".$price_plan_id." method=post>\n";
		$this->body .= "
			<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";

		$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where price_plan_id = ".$price_plan_id;
		$user_price_plan_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if ($user_price_plan_result === false)
			return false;
		elseif ($user_price_plan_result->RecordCount() == 0)
		{
			$this->body .= "
				<tr>
					<td class=medium_font align=center>
						There are currently no users attached to this price plan.
					</td>
				</tr>";
		}

		$this->sql_query = "select * from ".$this->classified_groups_table." where price_plan_id = ".$price_plan_id;
		$group_result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if ($group_result === false)
			return false;
		elseif ($group_result->RecordCount() == 0)
		{
			$this->body .= "
				<tr>
					<td class=medium_font align=center>
						There are currently no groups attached to this price plan.
					</td>
				</tr>";
		}
		$delete_ok=1;
		if ($group_result->RecordCount()>0 || $user_price_plan_result->RecordCount()>0)
		{
			if ($group_result->RecordCount()>0)
			{
				$this->body .= "
				<tr>
					<td class=medium_error_font align=center>
						There are currently groups attached to this price plan.
					</td>
				</tr>";
			}

			if ($user_price_plan_result->RecordCount()>0)
			{
				$this->body .= "
				<tr>
					<td class=medium_error_font align=center>
						There are currently users attached to this price plan.
					</td>
				</tr>";
			}
			if ($current_price_plan["type_of_billing"]==2)
			{
				//PREVENT DELETION of subsription with users/groups attached.
				$this->body .= "
					<tr>
						<td align=center class=medium_error_font>
							Subscription price plans cannot be deleted with users and/or groups attached.
							To delete, remove all attached groups and/or users.
						</td>
					</tr>";
				$delete_ok=0;
			}
			else
			{
				$this->body .= "
					<tr>
						<td class=medium_font>
							This price plan is currently attached to a group or user(s).
							To delete this price plan you must choose which price plan to replace this one with.
							Every group/user must be have a price plan.  In the dropdown list of price plans below
							choose the price plans you will replace	this one with. Once you have made a choice the
							changes will be made by clicking the \"delete\" button at the bottom.
						</td>
					</tr>
					<tr>
						<td class=medium_font align=center>
							<b>Choose the price plan you wish to replace the <b>".$delete_price_plan_name."</b> price plan with:</b>
						</td>
					</tr>
					<tr>
						<td align=center class=medium_font>
							Replace <b>".$delete_price_plan_name."</b> with:
							<select name=d>";
				while ($show = $price_plan_result->FetchRow())
				{
					if ($show["price_plan_id"] != $price_plan_id)
						$this->body .= "
								<option value=".$show["price_plan_id"].">".$show["name"]."</option>";
				}
				$this->body .= "
							</select>
						</td>
					</tr>";
			}
		}
		if (!$this->admin_demo() && $delete_ok)
		{
			$this->body .= "
				<tr>
					<td class=medium_font_light align=center>
						<input type=submit name=f value=\"Delete\">
					</td>
				</tr>";
		}
		$this->body .= "
				<tr>
					<td class=medium_font_light align=center>
						<a href=index.php?a=37><span class=medium_font>back to Price Plans Home</span></a>
					</td>
				</tr>";
		$this->body .= "
			</table>
			</form>";
		return true;
	} //end of function delete_price_plan_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_price_plan_name_and_description($db,$price_plan_id=0,$price_plan_info=0)
	{
		if (($price_plan_info) && ($price_plan_id))
		{
			$this->sql_query = "update ".$this->price_plan_table." set
				name = \"".$price_plan_info["name"]."\",
				description = \"".$price_plan_info["description"]."\",
				applies_to = \"".$price_plan_info["applies_to"]."\"
				where price_plan_id = ".$price_plan_id;
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
	} //end of function update_price_plan_name_and_description

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function price_plan_specifics_form($db,$price_plan_id=0)
	{
		if ($this->debug_price_plan)
		{
			echo "<BR>TOP OF PRICE_PLAN_SPECIFICS_FORM<br>\n";
			echo $price_plan_id." is the price_plan_id<bR>\n";
		}
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"charge per listing\", \"You have three choices of how to charge to for listing items: 1) a flat fee for all listings, 2) a fee based on the price a user enters into the price field, and 3) a fee based on the length of time the user wishes to run the listing. Choose the method you want by clicking the button next to your choice at right. If you choose the \\\"price based\\\" method make sure to create a bracketing system that will produce the appropriate charge you wish.\"]\n
			Text[2] = [\"instant cash renewals\", \"Choosing \\\"yes\\\" will renew listings instantly when paying for renewals with cash. If \\\"no\\\" the renewals will be placed on hold until the administrator approves them.\"]\n
			Text[3] = [\"instant check renewals\", \"Choosing \\\"yes\\\" will renew listings instantly when paying for renewals with check. If \\\"no\\\" the renewals will be placed on hold until the administrator approves them.\"]\n
			Text[4] = [\"instant money order renewals\", \"Choosing \\\"yes\\\" will renew listings instantly when paying for renewals with money order. If \\\"no\\\" the renewals will be placed on hold until the administrator approves them.\"]\n
			Text[5] = [\"allow credits to be used for listing renewals\", \"Choosing \\\"yes\\\" will allow the user to use credits to renew their listings. The rate will be one credit used per renewal.\"]\n
			Text[6] = [\"number of free pics\", \"This is the number of pictures a seller can post for free before they get charged for each additional picture at the price indicated in the next field.\"]\n";

		//".$this->show_tooltip(6,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		if ($price_plan_id)
		{
			$price_plan_name = $this->get_price_plan_name($db,$price_plan_id);
			$this->row_count = 0;
			if ($price_plan_name)
			{
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=3&f=3&g=".$price_plan_id." method=post>\n";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
				$this->title .= "Pricing > Price Plans > Edit Cost Specifics";
				$this->description = "Edit <b>".$price_plan_name."</b> price plan detailed charges through this form.  Make your changes then click the \"save\" button at the bottom.
					<br><b>Be mindful of the effects certain choices could have on people currently on this pricing plan";
				$this->sql_query = "select * from ".$this->price_plan_table." where price_plan_id = ".$price_plan_id;
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show_price_plan = $result->FetchRow();
				}
				else
				{
					return false;
				}

				if ($show_price_plan["type_of_billing"] == 1)
				{
					//charge for ----
					//charge per listing
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right width=30% rowspan=3 valign=top class=large_font>\n\t<b>charge per listing:</b>".$this->show_tooltip(1,1)."<input type=hidden h[type_of_billing] value=1></td>";
					$this->body .= "<td width=10% valign=top align=right>\n\t";
					$this->body .= "<input type=radio name=h[charge_per_ad_type] value=0 ";
					if ($show_price_plan["charge_per_ad_type"] == 0)
						$this->body .= "checked";
					$this->body .= "></td><td width=60% valign=top class=medium_font>\n\t<b>Charge same price for every listing</b> <br>";
					$this->charge_select_box($show_price_plan["charge_per_ad"],"h[charge_per_ad]");
					$this->body .= "</td>\n\t</tr>\n";

					//charge based on price field
					$this->body .= "<tr class=".$this->get_row_color()."><td valign=top align=right>";
					$this->body .= "<input type=radio name=h[charge_per_ad_type] value=1 ";
					if ($show_price_plan["charge_per_ad_type"] == 1)
						$this->body .= "checked";
					$this->body .= "></td><td width=60% valign=top class=medium_font>\n\t<b>Charge per listing based on price field of listing</b> <br>";

					$this->sql_query = "select * from ".$this->price_plans_increments_table." where price_plan_id = ".$price_plan_id." and category_id = 0 order by low asc";
					$result = $db->Execute($this->sql_query);
					//echo $this->sql_query." is the bracket display query<br>\n";
					if (!$result)
					{
						return false;
					}
					elseif ($result->RecordCount() > 0)
					{
						$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=100% class=row_color2>\n";
						$this->body .= "<tr bgcolor=000066>\n\t<td class=medium_font_light>\n\t<b>Price field low:</b></td>\n\t
							<td>&nbsp;</td>\n\t
							<td class=medium_font_light>\n\t<b>Price field high:</b></td>\n\t
							<td class=medium_font_light>\n\t<b>Listing fee assessed:</b></td>\n
							<td class=medium_font_light>\n\t<b>Renewal fee assessed:</b></td>\n</tr>\n";
						$old_count = $this->row_count;
						$this->row_count = 0;
						while ($show = $result->FetchRow())
						{
							$this->body .= "<tr class=".$this->get_row_color().">\n\t
								<td class=medium_font>\n\t".$show["low"]." ";
							$this->body .= "\n\t</td>\n\t
								<td class=medium_font>\n\tto \n\t</td>\n\t
								<td class=medium_font>\n\t";
							if ($show["high"] == 100000000)
								$this->body .= "and up";
							else
								$this->body .= $show["high"]."\n\t</td>\n\t";
							$this->body .= "<td class=medium_font>\n\t".$show["charge"]."\n\t</td>\n";
							$this->body .= "<td class=medium_font>\n\t".$show["renewal_charge"]."\n\t</td>\n</tr>\n";
							$this->row_count++;
						} //end of while
						$this->row_count = $old_count;
						$this->body .= "</table>\n";
					}
					else
						$this->body .= "<span class=medium_font>There are currently no charge per price range increments</span><br>";
					$this->body .= "<a href=index.php?a=37&b=6&e=".$price_plan_id."><span class=medium_font>\n\t<b>edit \"price range\" increments</b></span></a>";
					$this->body .= "</td>\n\t</tr>\n";

					//charge based on length of listing
					$this->body .= "<tr class=".$this->get_row_color()."><td valign=top align=right>";
					$this->body .= "<input type=radio name=h[charge_per_ad_type] value=2 ";
					if ($show_price_plan["charge_per_ad_type"] == 2)
						$this->body .= "checked";
					$this->body .= "></td><td width=60% valign=top class=medium_font>\n\t<b>Charge per listing based on time length of listing</b> <br>";

					$this->sql_query = "select * from ".$this->classified_price_plan_lengths_table." where price_plan_id = ".$price_plan_id." and category_id = 0 order by length_of_ad asc";
					$result = $db->Execute($this->sql_query);
					//echo $this->sql_query." is the bracket display query<br>\n";
					if (!$result)
					{
						return false;
					}
					elseif ($result->RecordCount() > 0)
					{
						$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=100% class=row_color2>\n";
						$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light>\n\tlength of listing (displayed)</td>\n\t
							<td class=medium_font_light>\n\tlength of listing (days) </td>\n\t
							<td class=medium_font_light>\n\tcharge to place an listing with this length </td>
							<td class=medium_font_light>\n\tcharge to renew an listing with this length </td>\n</tr>\n";
						$old_count = $this->row_count;
						$this->row_count = 0;
						while ($show = $result->FetchRow())
						{
							$this->body .= "<tr class=".$this->get_row_color().">\n\t
								<td class=medium_font>\n\t".$show["display_length_of_ad"]."\n\t</td>\n\t
								<td class=medium_font>\n\t".$show["length_of_ad"]."\n\t</td>\n\t
								<td class=medium_font class=medium_font>\n\t".$show["length_charge"]."\n\t</td>
								<td class=medium_font class=medium_font>\n\t".$show["renewal_charge"]."\n\t</td>\n</tr>\n";
							$this->row_count++;
						} //end of while
						$this->row_count = $old_count;
						$this->body .= "</table>\n";
					}
					else
						$this->body .= "<span class=medium_font>There are currently no charge per length range increments</span><br>";
					$this->body .= "<a href=index.php?a=37&b=7&c=".$price_plan_id."><span class=medium_font>\n\t<b>edit \"price plan\" length choices</b></span></a>";
					$this->body .= "</td>\n\t</tr>\n";
					$this->row_count++;

					//listing renewal cost
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right colspan=2 class=medium_font>\n\tlisting renewal cost: \n\t</td>\n\t";
					$this->body .= "<td>\n\t";
					$this->charge_select_box($show_price_plan["ad_renewal_cost"],"h[ad_renewal_cost]");
					$this->body .= "</td>\n\t</tr>\n";
					$this->row_count++;

					//echo $show_price_plan["num_free_pics"]." is the # of free pics<br>\n";
					//instant cash renewal
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right colspan=2 class=medium_font>\n\tinstant cash renewals:".$this->show_tooltip(2,1)."</td>\n\t";
					$this->body .= "<td colspan=2 class=medium_font>\n\t";
					$this->body .= "<input type=radio name=h[instant_cash_renewals] ";
					if ($show_price_plan["instant_cash_renewals"] == 1) $this->body .= "checked";
					$this->body .= " value=1>yes<Br>
						<input type=radio name=h[instant_cash_renewals] ";
					if ($show_price_plan["instant_cash_renewals"] == 0) $this->body .= "checked";
					$this->body .= " value=0>no";
					$this->body .= "</td>\n\t</tr>\n";
					$this->row_count++;

					//instant check renewal
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right colspan=2 class=medium_font>\n\tinstant check renewals:".$this->show_tooltip(3,1)."</td>\n\t";
					$this->body .= "<td colspan=2 class=medium_font>\n\t";
					$this->body .= "<input type=radio name=h[instant_check_renewals] ";
					if ($show_price_plan["instant_check_renewals"] == 1) $this->body .= "checked";
					$this->body .= " value=1>yes<Br>
						<input type=radio name=h[instant_check_renewals] ";
					if ($show_price_plan["instant_check_renewals"] == 0) $this->body .= "checked";
					$this->body .= " value=0>no";
					$this->body .= "</td>\n\t</tr>\n";
					$this->row_count++;

					//instant money order renewal
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right colspan=2 class=medium_font>\n\tinstant money order renewals:".$this->show_tooltip(4,1)."</td>\n\t";
					$this->body .= "<td colspan=2 class=medium_font>\n\t";
					$this->body .= "<input type=radio name=h[instant_money_order_renewals] ";
					if ($show_price_plan["instant_money_order_renewals"] == 1) $this->body .= "checked";
					$this->body .= " value=1>yes<Br>
						<input type=radio name=h[instant_money_order_renewals] ";
					if ($show_price_plan["instant_money_order_renewals"] == 0) $this->body .= "checked";
					$this->body .= " value=0>no";
					$this->body .= "</td>\n\t</tr>\n";
					$this->row_count++;

					//allow credits for renewal
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right colspan=2 class=medium_font>\n\tallow credits to be used for listing renewals:".$this->show_tooltip(5,1)."</td>\n\t";
					$this->body .= "<td colspan=2 class=medium_font>\n\t";
					$this->body .= "<input type=radio name=h[allow_credits_for_renewals] ";
					if ($show_price_plan["allow_credits_for_renewals"] == 1) $this->body .= "checked";
					$this->body .= " value=1>yes<Br>
						<input type=radio name=h[allow_credits_for_renewals] ";
					if ($show_price_plan["allow_credits_for_renewals"] == 0) $this->body .= "checked";
					$this->body .= " value=0>no";
					$this->body .= "</td>\n\t</tr>\n";
					$this->row_count++;
				}
				elseif ($show_price_plan["type_of_billing"] == 2)
				{
					//subscription billing period
					$this->body .= "<tr>\n\t<td align=right width=50% colspan=2 class=medium_font>\n\tclick on this link to
						edit subscription billing periods and associated charges
						<input type=hidden h[type_of_billing] value=2>\n\t</td>\n\t";
					$this->body .= "<td>\n\t<a href=index.php?a=37&b=3&f=8&g=".$price_plan_id."><span class=medium_font>
						subscription periods</span></a></td>\n</tr>\n";

					/*echo "<tr class=row_color1>\n\t<td align=right width=30% class=medium_font>\n\tsubscription billing period and charge
						can be set in registration period link in price plan admin <input type=hidden h[type_of_billing] value=2>\n\t</td>\n\t";
					echo "<td width=30%>\n\t";*/
					//$this->subscription_period_dropdown($db,$show_price_plan["subscription_billing_period"],"h[subscription_billing_period]");
					//echo "&nbsp;</td>\n\t</tr>\n";

					//subscription billing charge per period
					//echo "<tr class=row_color2>\n\t<td align=right class=medium_font>\n\tcharge per period \n\t</td>\n\t";
					//echo "<td>\n\t";
					//$this->charge_select_box($show_price_plan["subscription_billing_charge_per_period"],"h[subscription_billing_charge_per_period]");
					//echo "</td>\n\t</tr>\n";

					//free subscription period from registration
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td colspan=2 align=right class=medium_font>\n\tfree subscription period upon registration \n\t</td>\n\t";
					$this->body .= "<td>\n\t";
					$this->subscription_period_dropdown($db,$show_price_plan["free_subscription_period_upon_registration"],"h[free_subscription_period_upon_registration]");
					$this->body .= "</td>\n\t</tr>\n";
					$this->row_count++;

					//expire listings when subscription expires
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td colspan=2 align=right valign=top class=medium_font>\n\texpire listings when subscription expires\n\t</td>\n\t";
					$this->body .= "<td class=medium_font>\n\t<input type=radio name=h[ad_and_subscription_expiration] ";
					if ($show_price_plan["ad_and_subscription_expiration"] == 1)
						$this->body .= "checked ";
					$this->body .= "value=1>yes<Br><input type=radio name=h[ad_and_subscription_expiration] ";
					if ($show_price_plan["ad_and_subscription_expiration"] == 0)
						$this->body .= "checked ";
					$this->body .= "value=0>no ";
					$this->body .= "</td>\n\t</tr>\n";
					$this->row_count++;
				}

				$this->sql_query = "select * from ".$this->classified_payment_choices_table." where payment_choice_id = 7";
				$site_balance_result = $db->Execute($this->sql_query);
				if (!$site_balance_result)
				{
					return false;
				}
				else
				{
					$show_site_balance = $site_balance_result->FetchRow();
				}
				if (($show_site_balance["accepted"]) && (!$this->configuration_data["positive_balances_only"]))
				{
					// Max invoice limit
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font colspan=2>\n\tmax invoice limit:</font>\n\t</td>\n\t";
					$this->body .= "<td>\n\t";
					$this->body .= $this->charge_select_box($show_price_plan["invoice_max"], "h[invoice_max]");
					$this->body .= "</td>\n\t</tr>\n";
					$this->row_count++;
				}

				$this->sql_query = "select * from ".$this->site_configuration_table;
				$result = $db->Execute($this->sql_query);
				$site_config = $result->FetchRow();

				$this->body .= "<tr class=row_color_red>\n\t<td colspan=4 class=medium_font_light>\n\t<b>Add the price plan
					extra fields to the entire price plan below.  Note these will be in effect for the categories without specific
					listing extras assigned to them.</b> </td></tr>";

				for($i = 1; $i < 6; $i++)
				{
					$suffix = ($i > 1) ? "_$i" : "";
					$level_suffix = ($i > 1) ? "_level_$i" : "";
					if($site_config["use_featured_feature$suffix"])
					{
						// featured listings
						$this->body .= "<tr class=row_color_red>\n\t<td colspan=4 class=medium_font_light>\n\tFeatured Listings Level $i</td></tr>\n";
						$this->body .= "<tr>\n\t<td align=right colspan=2 valign=top class=medium_font>\n\tuse the featured listings field level $i: </td>\n\t";
						$this->body .= "<td colspan=4 valign=top class=medium_font>\n\t<input type=radio name=h[use_featured_ads] value=1 ";
						if ($show_price_plan["use_featured_ads$level_suffix"] == 1)
							$this->body .= "checked";
						$this->body .= "> yes<br><input type=radio name=h[use_featured_ads$level_suffix] value=0 ";
						if ($show_price_plan["use_featured_ads$level_suffix"] == 0)
							$this->body .= "checked";
						$this->body .= "> no\n\t</td>\n</tr>\n";

						$this->body .= "<tr  class=row_color1>\n\t<td align=right colspan=2 class=medium_font>\n\tfeatured listing price level $i \n\t</td>\n\t";
						$this->body .= "<td >\n\t";
						$this->charge_select_box($show_price_plan["featured_ad_price$suffix"],"h[featured_ad_price]");
						$this->body .= "</td>\n\t</tr>\n";
					}
				}

				if($site_config["use_bolding_feature"])
				{
					// bolding
					$this->body .= "<tr class=row_color_red>\n\t<td colspan=4 class=medium_font_light>\n\tBolding</td></tr>\n";
					$this->body .= "<tr>\n\t<td align=right colspan=2 valign=top class=medium_font>\n\tuse bolding: </td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=h[use_bolding] value=1 ";
					if ($show_price_plan["use_bolding"] == 1)
						$this->body .= "checked";
					$this->body .= "> yes<br><input type=radio name=h[use_bolding] value=0 ";
					if ($show_price_plan["use_bolding"] == 0)
						$this->body .= "checked";
					$this->body .= "> no\n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color1>\n\t<td align=right colspan=2 class=medium_font>\n\tbolding price \n\t</td>\n\t";
					$this->body .= "<td >\n\t";
					$this->charge_select_box($show_price_plan["bolding_price"],"h[bolding_price]");
					$this->body .= "</td>\n\t</tr>\n";
				}

				if($site_config["use_better_placement_feature"])
				{
					// better placement
					$this->body .= "<tr class=row_color_red>\n\t<td colspan=4 class=medium_font_light>\n\tBetter Placement</td></tr>\n";
					$this->body .= "<tr>\n\t<td align=right colspan=2 valign=top class=medium_font>\n\tuse better placement: </td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=h[use_better_placement] value=1 ";
					if ($show_price_plan["use_better_placement"] == 1)
						$this->body .= "checked";
					$this->body .= "> yes<br><input type=radio name=h[use_better_placement] value=0 ";
					if ($show_price_plan["use_better_placement"] == 0)
						$this->body .= "checked";
					$this->body .= "> no\n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color1>\n\t<td align=right colspan=2 class=medium_font>\n\tbetter placement charge \n\t</td>\n\t";
					$this->body .= "<td>\n\t";
					$this->charge_select_box($show_price_plan["better_placement_charge"],"h[better_placement_charge]");
					$this->body .= "</td>\n\t</tr>\n";
				}

				if($site_config["use_attention_getters"])
				{
					// attention getters
					$this->body .= "<tr class=row_color_red>\n\t<td colspan=4 class=medium_font_light>\n\tAttention Getters</td></tr>\n";
					$this->body .= "<tr>\n\t<td align=right colspan=2 valign=top class=medium_font>\n\tuse attention getters: </td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=h[use_attention_getters] value=1 ";
					if ($show_price_plan["use_attention_getters"] == 1)
						$this->body .= "checked";
					$this->body .= "> yes<br><input type=radio name=h[use_attention_getters] value=0 ";
					if ($show_price_plan["use_attention_getters"] == 0)
						$this->body .= "checked";
					$this->body .= "> no\n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color1>\n\t<td align=right colspan=2 class=medium_font>\n\tattention getter price \n\t</td>\n\t";
					$this->body .= "<td>\n\t";
					$this->charge_select_box($show_price_plan["attention_getter_price"],"h[attention_getter_price]");
					$this->body .= "</td>\n\t</tr>\n";
				}

				// Reset color
				$this->row_count = 1;

				// Charge per picture
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right colspan=2 class=medium_font>\n\tcharge per picture \n\t</td>\n\t";
				$this->body .= "<td>\n\t";
				$this->charge_select_box($show_price_plan["charge_per_picture"],"h[charge_per_picture]");
				$this->body .= "</td>\n\t</tr>\n";
				$this->row_count++;

				// num free pics
				// get max number of photos
				$this->sql_query = "select maximum_photos from ".$this->ad_configuration_table;
				//echo $this->sql_query.'<br>';
				$result = $db->Execute($this->sql_query);
				if($result)
					$show_configuration = $result->FetchRow();
				else
					$this->body .= "Error couldnt get maximum number of pictures<br>";

				if ($this->debug_price_plan)
				{
					echo $show_configuration["maximum_photos"]." is maximum_photos<BR>\n";
				}
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right colspan=2 class=medium_font>\n\tnumber of free pics:".".$this->show_tooltip(6,1)."."</td>\n\t";
				$this->body .= "<td colspan=2 class=medium_font>\n\t<select name=h[num_free_pics]>\n\t\t";
				for ($i=0;$i <= $show_configuration["maximum_photos"];$i++)
				{
					$this->body .= "<option ";
					if ($show_price_plan["num_free_pics"] == $i)
						$this->body .= "selected";
					$this->body .= ">".$i."</option>\n\t\t";
				}
				$this->body .= "</select></font></td>\n\t</tr>\n";
				$this->row_count++;

				//max listings allowed
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=right colspan=2 class=medium_font>\n\tmax listings allowed at one time \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t<select name=h[max_ads_allowed]>\n\t\t";
				for ($i=0;$i < 101;$i++)
				{
					$this->body .= "<option ";
					if ($show_price_plan["max_ads_allowed"] == $i)
						$this->body .= "selected";
					$this->body .= ">".$i."</option>\n\t\t";
				}
				for ($i=110;$i <= 999;($i=$i+10))
				{
					$this->body .= "<option ";
					if ($show_price_plan["max_ads_allowed"] == $i)
						$this->body .= "selected";
					$this->body .= ">".$i."</option>\n\t\t";
				}
				for ($i=1000;$i <= 10000;$i = $i + 1000)
				{
					$this->body .= "<option ";
					if ($show_price_plan["max_ads_allowed"] == $i)
						$this->body .= "selected";
					$this->body .= ">".$i."</option>\n\t\t";
				}
				$this->body .= "</select> </td>\n\t</tr>\n";
				$this->row_count++;

				if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2>\n\t<input type=submit name=save_group value=\"Save\">\n\t</td>\n</tr>\n";
				if ($show_price_plan["type_of_billing"] == 1)
					$this->body .= "<tr>\n\t<td colspan=2>\n\t<a href=index.php?a=37&b=5&x=".$price_plan_id."><span class=medium_font>\n\t
						<b>add a category specific price plan</b></span></a>\n\t</td>\n</tr>\n";

				$this->body .= "<tr>\n\t<td colspan=2>\n\t<a href=index.php?a=37&b=3&g=".$price_plan_id."><span class=medium_font>\n\t
					back to <b>".$price_plan_name."</b> Details</span></a>\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td colspan=2>\n\t<a href=index.php?a=37><span class=medium_font>\n\t
					price plan list</span></a>\n\t</td>\n</tr>\n";
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
	} //end of function price_plan_specifics_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_price_plan_specifics($db,$price_plan_id=0,$price_plan_info=0)
	{
		if ($price_plan_id && $price_plan_info)
		{
			$this->sql_query = "select * from ".$this->price_plan_table." where price_plan_id = ".$price_plan_id;
			$type_result = $db->Execute($this->sql_query);
			if (!$type_result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($type_result->RecordCount() == 1)
			{
				$show_type = $type_result->FetchRow();
				$this->sql_query = "update ".$this->price_plan_table." set ";
				if ($show_type["type_of_billing"] == 1)
				{
					$this->sql_query .= "
						charge_per_ad_type = '".$price_plan_info["charge_per_ad_type"]."',
						charge_per_ad = '".$price_plan_info["charge_per_ad"][0].".".sprintf("%02d",$price_plan_info["charge_per_ad"][1])."',
						instant_cash_renewals = '".$price_plan_info["instant_cash_renewals"]."',
						instant_check_renewals = '".$price_plan_info["instant_check_renewals"]."',
						instant_money_order_renewals = '".$price_plan_info["instant_money_order_renewals"]."',
						allow_credits_for_renewals = '".$price_plan_info["allow_credits_for_renewals"]."',
						ad_renewal_cost = '".$price_plan_info["ad_renewal_cost"][0].".".sprintf("%02d",$price_plan_info["ad_renewal_cost"][1])."', ";
				}
				elseif ($show_type["type_of_billing"] == 2)
				{
					$this->sql_query .= "
						subscription_billing_period = \"".$price_plan_info["subscription_billing_period"]."\",
						subscription_billing_charge_per_period = \"".$price_plan_info["subscription_billing_charge_per_period"][0].".".sprintf("%02d",$price_plan_info["subscription_billing_charge_per_period"][1])."\",
						free_subscription_period_upon_registration = \"".$price_plan_info["free_subscription_period_upon_registration"]."\",
						ad_and_subscription_expiration = \"".$price_plan_info["ad_and_subscription_expiration"]."\", ";
				}
				else
				{
					return false;
				}

				//build price from form data

				if(strlen(trim($price_plan_info["use_featured_ads"])) >0)
				{
					if(ereg("^[0-9]*$", $price_plan_info["featured_ad_price"][0]) == 0)
						$price_plan_info["featured_ad_price"][0] = 0;
					$this->sql_query .= "use_featured_ads = ".$price_plan_info["use_featured_ads"].",
										featured_ad_price = ".$price_plan_info["featured_ad_price"][0].".".sprintf("%02d",$price_plan_info["featured_ad_price"][1]).", ";
				}
				if(strlen(trim($price_plan_info["use_featured_ads_level_2"])) >0)
				{
					if(ereg("^[0-9]*$", $price_plan_info["featured_ad_price_2"][0]) == 0)
						$price_plan_info["featured_ad_price_2"][0] = 0;
					$this->sql_query .= "use_featured_ads_level_2 = ".$price_plan_info["use_featured_ads_level_2"].",
										featured_ad_price_2 = ".$price_plan_info["featured_ad_price_2"][0].".".sprintf("%02d",$price_plan_info["featured_ad_price_2"][1]).", ";
				}
				if(strlen(trim($price_plan_info["use_featured_ads_level_3"])) >0)
				{
					if(ereg("^[0-9]*$", $price_plan_info["featured_ad_price_3"][0]) == 0)
						$price_plan_info["featured_ad_price_3"][0] = 0;
					$this->sql_query .= "use_featured_ads_level_3 = ".$price_plan_info["use_featured_ads_level_3"].",
										featured_ad_price_3 = ".$price_plan_info["featured_ad_price_3"][0].".".sprintf("%02d",$price_plan_info["featured_ad_price_3"][1]).", ";
				}
				if(strlen(trim($price_plan_info["use_featured_ads_level_4"])) >0)
				{
					if(ereg("^[0-9]*$", $price_plan_info["featured_ad_price_4"][0]) == 0)
						$price_plan_info["featured_ad_price_4"][0] = 0;
					$this->sql_query .= "use_featured_ads_level_4 = ".$price_plan_info["use_featured_ads_level_4"].",
										featured_ad_price_4 = ".$price_plan_info["featured_ad_price_4"][0].".".sprintf("%02d",$price_plan_info["featured_ad_price_4"][1]).", ";
				}
				if(strlen(trim($price_plan_info["use_featured_ads_level_5"])) >0)
				{
					if(ereg("^[0-9]*$", $price_plan_info["featured_ad_price_5"][0]) == 0)
						$price_plan_info["featured_ad_price_5"][0] = 0;
					$this->sql_query .= "use_featured_ads_level_5 = ".$price_plan_info["use_featured_ads_level_5"].",
										featured_ad_price_5 = ".$price_plan_info["featured_ad_price_5"][0].".".sprintf("%02d",$price_plan_info["featured_ad_price_5"][1]).", ";
				}
				if(strlen(trim($price_plan_info["use_attention_getters"])) >0)
				{
					if(ereg("^[0-9]*$", $price_plan_info["attention_getter_price"][0]) == 0)
						$price_plan_info["attention_getter_price"][0] = 0;
					$this->sql_query .= "use_attention_getters = ".$price_plan_info["use_attention_getters"].",
										attention_getter_price = ".$price_plan_info["attention_getter_price"][0].".".sprintf("%02d",$price_plan_info["attention_getter_price"][1]).", ";
				}
				if(strlen(trim($price_plan_info["use_better_placement"])) >0)
				{
					if(ereg("^[0-9]*$", $price_plan_info["better_placement_charge"][0]) == 0)
						$price_plan_info["better_placement_charge"][0] = 0;
					$this->sql_query .= "use_better_placement = ".$price_plan_info["use_better_placement"].",
										better_placement_charge = ".$price_plan_info["better_placement_charge"][0].".".sprintf("%02d",$price_plan_info["better_placement_charge"][1]).", ";
				}
				if(strlen(trim($price_plan_info["use_bolding"])) >0)
				{
					if(ereg("^[0-9]*$", $price_plan_info["bolding_price"][0]) == 0)
						$price_plan_info["bolding_price"][0] = 0;
					$this->sql_query .= "use_bolding = ".$price_plan_info["use_bolding"].",
										bolding_price = ".$price_plan_info["bolding_price"][0].".".sprintf("%02d",$price_plan_info["bolding_price"][1]).", ";
				}

				if(ereg("^[0-9]*$", $price_plan_info["charge_per_picture"][0]) == 0)
						$price_plan_info["charge_per_picture"][0] = 0;

				$this->sql_query .=	"charge_per_picture = ".$price_plan_info["charge_per_picture"][0].".".sprintf("%02d",$price_plan_info["charge_per_picture"][1]).",
					max_ads_allowed = ".$price_plan_info["max_ads_allowed"].",
					num_free_pics = ".$price_plan_info["num_free_pics"].",
					invoice_max = ".$price_plan_info["invoice_max"][0].".".sprintf("%02d",$price_plan_info["invoice_max"][1])."
					where price_plan_id = ".$price_plan_id;

				//echo $this->sql_query."<br>";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}
				else
				{
					if ($price_plan_info["charge_per_ad_type"] == 1)
					{
						//set use_price_field to 1
						$this->sql_query = "update ".$this->ad_configuration_table." set use_price_field = 1";
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
						{
							$this->error_message = $this->internal_error_message;
							return false;
						}
					}
					return true;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_price_plan_specifics

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_price_plan_expiration($db,$price_plan_id=0)
	{
		if ($price_plan_id)
		{
			$this->sql_query = "select * from ".$this->classified_expirations_table." where type_id = ".$price_plan_id." and type = 2";
			$current_expiration_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$current_expiration_result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($current_expiration_result->RecordCount() == 1)
			{
				$show = $current_expiration_result->FetchRow();
				return $show["expires"];
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return false;
		}
	} //end of function get_price_plan_expiration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function move_price_plan_form($db,$price_plan_id=0)
	{
		if ($price_plan_id)
		{
			$this->sql_query = "select name,price_plan_id from ".$this->price_plan_table." where price_plan_id != ".$price_plan_id." order by name";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$price_plan_name = $this->get_price_plan_name($db,$price_plan_id);
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=4&g=".$price_plan_id." method=post>\n";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
				$this->title .= "Pricing > Price Plans > Move Users \n\t</td>\n</tr>\n";
				$this->description .= "Move users from the ".$price_plan_name." price plan to
					another price plan using this admin tool.  Choose the price plan you wish to move the users to from the dropdown list below.
					The operation cannot be reversed.  You will have to move users back individually once this action is taken.
					Once your choice is made click the \"move users\" button at the bottom.";
				$this->body .= "<tr>\n\t<td align=center class=medium_font>\n\tmove members of the <b>".$price_plan_name."</b> price plan to the \n\t";
				$this->body .= "<select name=h>\n\t\t";

				while ($show = $result->FetchRow())
				{
					if ($show["price_plan_id"] != $price_plan_id)
					{
						$this->body .= "<option value=".$show["price_plan_id"].">".$show["name"]."</option>\n\t\t";
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
				$this->title .= "Pricing > Price Plans > Move Users";
				$this->description .= "<tr class=row_color_red>\n\t<td class=medium_font_light>\n\tThere are no other price plans
					to move users to. \n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
			}
		}
		else
		{
			return false;
		}

	} //end of function move_price_plan_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function browse_categories($db,$category=0,$price_plan_id=0)
	{
		if ($price_plan_id)
		{
			$price_plan_name = $this->get_price_plan_name(&$db,$price_plan_id);
			$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100% class=row_color1>\n";
			$this->title = "Pricing > Price Plans > Category Specific Pricing";
			$this->description = "Change, add and remove Category Specific Pricing for this Price Plan.
			A category that does not have Category Specific pricing attached to it will display \"Base Price Plan\".
			To delete Category Specific Pricing simply click the \"delete\" button if it exists. When Category Specific
			Pricing is initiated for a category, that pricing will be inherited to all of that category's subcategories
			as well. Therefore, the \"category pricing status\" column will always display either \"Base Price Plan\"
			or the Parent Category's specific pricing.";
			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=3 align=center>\n\t".$this->very_large_font_tag_light."Price Plan: <b>".$price_plan_name."</b></font>\n\t</td>\n</tr>\n";

			if ($category)
			{
				$sql_query = "select * from ".$this->classified_categories_table." where category_id = ".$category;
				$result = $db->Execute($sql_query);
				//echo $sql_query." is the query<br>\n";
				if (!$result)
				{
					//echo $sql_query." is the query<br>\n";
					$this->error_message = $this->messages[5501];
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show = $result->FetchRow();
					$parent_id = $show["parent_id"];
					$category_name = $this->get_category_name($db,$category);
					$description = $this->get_category_description($db,$category);
				}
				else
				{
					//category does not exist
					$this->error_message = $this->messages["5500"];
					return false;
				}
			}
			else
			{
				$parent_id = 0;
				$category_name = "Main";
				$description = "home of all main categories";
				$category = 0;
			}

			$sql_query = "select * from ".$this->classified_categories_table." where parent_id = ".$category." order by display_order";
			$result = $db->Execute($sql_query);
			//echo $sql_query." is the query<br>\n";
			if (!$result)
			{
				//echo $sql_query." is the query<br>\n";
				$this->error_message = $this->messages[5501];
				return false;
			}
			else
			{
				if ($category)
				{
					$category_tree = $this->get_category_tree($db,$category);
					reset ($this->category_tree_array);
					if ($category_tree)
					{

						//category tree
						$this->body .= "<tr>\n\t<td colspan=5 class=medium_font>\n\t<b>";
						$this->body .= "current category : </b> <a href=index.php?a=37&b=5&x=".$price_plan_id.">Main</a> >";
						if (is_array($this->category_tree_array))
						{
							$i = 0;
							//$categories = array_reverse($this->category_tree_array);
							$i = count($this->category_tree_array);
							while ($i > 0 )
							{
								//display all the categories
								$i--;
								if ($i == 0)
									$this->body .= "<span class=medium_font><b>".$this->category_tree_array[$i]["category_name"]."</b></span>";
								else
									$this->body .= "<a href=index.php?a=37&b=5&d=".$this->category_tree_array[$i]["category_id"]."&x=".$price_plan_id."><span class=medium_font>\n\t".$this->category_tree_array[$i]["category_name"]."</span></a> > ";
							}
						}
						else
						{
							$this->body .= $category_tree;
						}
						$this->body .= "\n\t</td>\n</tr>\n";
					}
				}
				else
				{
					$this->body .= "<tr>\n\t<td colspan=4 class=medium_font>\n\t<b>Category : </b>Main \n\t</td>\n</tr>\n";
				}

				if ($result->RecordCount() > 0)
				{
					//echo $result->RecordCount()." is the record count<br>\n";
					//display the sub categories of this category
					$this->body .= "<tr class=row_color_black>\n\t<td colspan=5 class=medium_font_light>\n\t".$this->messages[3505]." Subcategories of <b>".$category_name."</b> \n\t</td>\n</tr>\n";
					$this->body .= "<tr class=row_color_black>\n\t<td align=center class=medium_font_light>\n\t<b>category name</b>\n\t</td>\n\t";
					$this->body .= "<td align=center class=medium_font_light>\n\t<b>edit category pricing</b>\n\t</td>\n\t";
					$this->body .= "<td align=center class=medium_font_light>\n\t<b>category pricing status</b>\n\t</td>\n</tr>";
					$this->row_count = 0;
					while ($show_sub_categories = $result->FetchRow())
					{
						//check for subcategories to the current category
						$subcategory_name = $this->get_category_name($db,$show_sub_categories["category_id"]);
						$sql_query = "select * from ".$this->classified_categories_table." where parent_id = ".$show_sub_categories["category_id"]." order by display_order";
						$test_sub_result = $db->Execute($sql_query);
						//echo $sql_query." is the query<br>\n";
						if (!$test_sub_result)
						{
							//echo $sql_query." is the query<br>\n";
							$this->error_message = $this->messages[5501];
							return false;
						}
						if ($test_sub_result->RecordCount() > 0)
							$this->body .= "<tr class=".$this->get_row_color().">\n\t<td>\n\t<a href=index.php?a=37&b=5&d=".$show_sub_categories["category_id"]."&x=".$price_plan_id."><span class=medium_font>\n\t".$subcategory_name."</span></a></td>\n\t";
						else
							$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t".
								$subcategory_name."</td>\n\t";

						//see if there is a price plan attached
						$sql_query = "select * from ".$this->classified_price_plans_categories_table."
							where category_id = ".$show_sub_categories["category_id"]." and price_plan_id = ".$price_plan_id;

						$test_current_result = $db->Execute($sql_query);
						//echo $sql_query." is the query<br>\n";
						if (!$test_current_result)
						{
							//echo $sql_query." is the query<br>\n";
							$this->error_message = $this->messages[5501];
							return false;
						}
						if ($test_current_result->RecordCount() == 1)
						{
							$current_sub_category = $test_current_result->FetchRow();
							$this->body .= "<td align=center>\n\t<a href=index.php?a=37&b=5&d=".$show_sub_categories["category_id"]."&e=1&x=".$price_plan_id."&y=".$current_sub_category["category_price_plan_id"]."><span class=medium_font>\n\tedit</span></a>\n\t</td>\n\t";
							$this->body .= "<td align=center><a href=index.php?a=37&b=5&e=2&d=".$show_sub_categories["category_id"]."&x=".$price_plan_id."&y=".$current_sub_category["category_price_plan_id"]."><span class=medium_font>\n\tdelete</span></a></td>\n\t";
						}
						else
						{
							$this->body .= "<td align=center>\n\t<a href=index.php?a=37&b=5&d=".$show_sub_categories["category_id"]."&e=3&x=".$price_plan_id."><span class=medium_font>\n\tclick to add</span></a>\n\t</td>\n\t";
							$this->body .= "<td align=center class=medium_font>\n\t";
							//check for parent price plan
							$parent_category_price_plan_id = $this->get_parent_price_plan($db,$show_sub_categories["category_id"],$price_plan_id);
							if ($parent_category_price_plan_id)
							{
								//show category name or delete if it is the current
								$category_name = $this->get_category_name($db,$parent_category_price_plan_id);
								$this->body .= $category_name." Price Plan";
							}
							else
							{
								$this->body .= "Base Price Plan";
							}

							$this->body .= " </td>\n\t";
						}
						$this->body .= "</tr>\n";
						$this->row_count++;
					}
				}
			}
			$this->body .= "<tr>\n\t<td colspan=5>\n\t<a href=index.php?a=37&b=3&g=".$price_plan_id." class=medium_font>\n\t
				<b>back to ".$price_plan_name." Details</b></a>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			return true;
		}
		else
			return false;

	} //end of function browse_categories

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_parent_price_plan($db,$category_id=0,$price_plan_id=0)
	{
		if (($category_id) && ($price_plan_id))
		{
			$i = 0;
			$category_next = $category_id;
			do
			{
				$this->sql_query = "select category_id,parent_id from ".$this->classified_categories_table."
					where category_id = ".$category_next;
				$category_result =  $db->Execute($this->sql_query);

				//$category = array();

				//echo $this->sql_query." is the query<br>\n";
				if (!$category_result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[3501];
					return false;
				}
				elseif ($category_result->RecordCount() == 1)
				{
					$show_category = $category_result->FetchRow();
					$this->sql_query = "select * from ".$this->classified_price_plans_categories_table."
						where category_id = ".$show_category["category_id"]." and price_plan_id = ".$price_plan_id;
					$price_plan_result =  $db->Execute($this->sql_query);
					if ($price_plan_result->RecordCount() == 1)
					{
						return $show_category["category_id"];
					}
					$category_next = $show_category["parent_id"];
				}
				else
				{
					//echo "wrong return<Br>\n";
					return false;
				}

			} while ( $show_category["parent_id"] != 0 );
			return 0;
		}
		else
			return false;
	} //end of function get_parent_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function category_specific_price_plan_form($db,$category_price_plan_id,$category_id=0,$price_plan_id=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"charge per listing\", \"You have three choices of how to charge to place an listing. One flat charge for all listings, a charge based on the price a user enters into the price field, or a charge based upon the length of the listing. If you choose the \\\"price based\\\" method make sure to create a bracketing system that will produce the appropriate charge you wish.\"]\n
			Text[2] = [\"instant cash renewals\", \"Choosing \\\"yes\\\" will renew listings instantly when paying for renewals with cash. If \\\"no\\\" the renewals will be placed on hold until the administrator approves them.\"]\n
			Text[3] = [\"instant check renewals\", \"Choosing \\\"yes\\\" will renew listings instantly when paying for renewals with check. If \\\"no\\\" the renewals will be placed on hold until the administrator approves them.\"]\n
			Text[4] = [\"instant money order renewals\", \"Choosing \\\"yes\\\" will renew listings instantly when paying for renewals with money order. If \\\"no\\\" the renewals will be placed on hold until the administrator approves them.\"]\n
			Text[5] = [\"allow credits to be used for listing renewals\", \"Choosing \\\"yes\\\" will allow the user to use credits to renew their listings. The rate will be one credit used per renewal.\"]\n
			Text[6] = [\"number of free pics\", \"This is the number of pictures a seller can post for free before they get charged for each additional picture at the price indicated in the next field.\"]\n";

		//".$this->show_tooltip(1,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";
		if ($price_plan_id)
		{
			$base_price_plan = $this->get_price_plan($db,$price_plan_id);
			$price_plan_name = $this->get_price_plan_name($db,$price_plan_id);

			if($category_id)
			{
				// set category specific variables
				$category_name = $this->get_category_name($db,$category_id);

				$this->sql_query = "select * from {$this->classified_categories_table} where category_id = ".$category_id;
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}
				elseif ($result->RecordCount() == 1)
					$show_parent_id = $result->FetchRow();

				$parent_name = $this->get_category_name($db,$show_parent_id["parent_id"]);
				if($category_price_plan_id)
				{
					$this->sql_query = "select * from ".$this->classified_price_plans_categories_table." where category_price_plan_id = ".$category_price_plan_id;
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						$this->error_message = $this->internal_error_message;
						return false;
					}
					elseif ($result->RecordCount() == 1)
					{
						$show_price_plan = $result->FetchRow();
						//echo "using category specific<br>\n";
					}
					else
						return false;
					if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=5&e=1&d=".$show_parent_id["parent_id"]."&x=".$price_plan_id."&y=".$category_price_plan_id." method=post>\n";
					$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center width=100% class=row_color1>\n";
					$this->title = "Pricing > Price Plans > Category Specific Pricing > Edit";
					$this->description = "Edit <b>".$price_plan_name."</b>
						price plans charges attached to the <b>".$category_name."</b> category through this form.  The charges set below are used for the current category and its
						subcategories unless a subcategory overrides these charges with its own category specific price plan.
						Once you are through making the changes you want click the \"save changes\" button.
						<br><b>Be mindful of the effects certain
						choices could have on people currently on this pricing plan</b>";
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=5 class=large_font_light align=center>\n\t<b>Price Plan: ".$price_plan_name."</b>\n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color_red>\n\t<td colspan=5 class=large_font_light align=center>\n\t
				<span class=large_font_light> Category: ".$category_name."</span>\n\t</td>\n</tr>\n";

				}
				else
				{
					//add new one
					$this->title = "Pricing > Price Plans > Category Specific Pricing";
					$this->description .= "Use the form below to add category specific pricing
						under the <b>".$price_plan_name."</b> price plan and attached to the <b>".$category_name."</b>.
						The charges set below are used for the current category and its subcategories unless a subcategory overrides these charges
						with its own pricing.<br><b>Be mindful of the effects certain
						choices could have on people currently on this pricing plan";
					if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=5&e=3&d=".$category_id."&x=".$price_plan_id." method=post>\n";
					$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
					$this->body .= "<tr bgcolor=000066>\n\t<td colspan=4 class=large_font_light align=center>\n\t<b>Price Plan: ".$price_plan_name."</b>\n\t</td>\n</tr>\n";
					$this->body .= "<tr class=row_color_red>\n\t<td colspan=4 class=large_font_light align=center>\n\t
						<span class=large_font_light> Category: ".$category_name."</span>\n\t</td>\n</tr>\n";


				}
			}
			else
			{
				// set global price plan variables
				$this->sql_query = "select * from ".$this->price_plan_table." where price_plan_id = ".$price_plan_id;
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show_price_plan = $result->FetchRow();
					//echo "using category specific<br>\n";
				}
				else
					return false;
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=3&f=3&g=".$price_plan_id." method=post>\n";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
				$this->title .= "Pricing > Price Plans > Edit Cost Specifics";
				$this->description = "Edit <b>".$price_plan_name."</b> price plan detailed charges through this form.  Make your changes then click the \"save\" button at the bottom.
					<br><b>Be mindful of the effects certain choices could have on people currently on this Price Plan.";
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=5 class=large_font_light align=center>\n\t<b>Price Plan: ".$price_plan_name."</b>\n\t</td>\n</tr>\n";

				$this->sql_query = "select * from ".$this->price_plan_table." where price_plan_id = ".$price_plan_id;
			}

			//echo $this->sql_query."<br>\n";

			if ($price_plan_name)
			{
				//charge for ----

				if ($base_price_plan["type_of_billing"] == 1)
				{
					//charge for ----
					//charge per listing
					$this->body .= "
						<tr class=row_color2>";
					if($base_price_plan["applies_to"] == 1)
					{
						$this->body .= "<td align=right width=30% rowspan=3 valign=top class=large_font><b>charge per listing:".$this->show_tooltip(1,1)."</b></td>
							<td width=10% valign=top align=right>\n\t";
						$this->body .= "<input type=radio name=h[charge_per_ad_type] value=0 ";
						if ($show_price_plan["charge_per_ad_type"] == 0)
							$this->body .= "checked";
						$this->body .= "></td><td width=60% valign=top class=medium_font>\n\t<b>Charge same price for every listing</b> <br>";
						$this->charge_select_box($show_price_plan["charge_per_ad"],"h[charge_per_ad]");
						$this->body .= "</td>\n\t</tr>\n";

						//charge based on price field
						$this->body .= "<tr class=row_color2><td valign=top align=right>";
						$this->body .= "<input type=radio name=h[charge_per_ad_type] value=1 ";
						if ($show_price_plan["charge_per_ad_type"] == 1)
							$this->body .= "checked";
						$this->body .= "></td><td width=60% valign=top class=medium_font>\n\t<b>Charge per listing based on price field of listing</b> <br>";

						$this->sql_query = "select * from ".$this->price_plans_increments_table." where price_plan_id = ".$price_plan_id." and category_id = ".$category_id." order by low asc";
						$result = $db->Execute($this->sql_query);
						//echo $this->sql_query." is the bracket display query<br>\n";
						if (!$result)
							return false;
						elseif ($result->RecordCount() > 0)
						{
							$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=100% class=row_color2>\n";
							$this->body .= "<tr bgcolor=000066>\n\t<td class=medium_font_light>\n\t<b>Price field low:</b></td>\n\t
								<td>&nbsp;</td>\n\t
							<td class=medium_font_light>\n\t<b>Price field high:</b></td>\n\t
							<td class=medium_font_light align=center>\n\t<b>Listing fee assessed:</b></td>\n
							<td class=medium_font_light align=center>\n\t<b>Renewal fee assessed:</b></td>\n</tr>\n";
							$this->row_count = 0;
							while ($show = $result->FetchRow())
							{
								$this->body .= "<tr class=".$this->get_row_color().">\n\t
									<td class=medium_font>\n\t".$show["low"]." ";
								$this->body .= "\n\t</td>\n\t
									<td class=medium_font>\n\tto \n\t</td>\n\t
									<td class=medium_font>\n\t";
								if ($show["high"] == 100000000)
									$this->body .= "and up";
								else
									$this->body .= $show["high"]."\n\t</td>\n\t";
								$this->body .= "<td class=medium_font align=center>\n\t".$show["charge"]."\n\t</td>\n";
								$this->body .= "<td class=medium_font align=center>\n\t".$show["renewal_charge"]."\n\t</td>\n</tr>\n";
								$this->row_count++;
							} //end of while
							$this->body .= "</table>\n";
						}
						else
							$this->body .= "<span class=medium_font><b>There are currently no charge per price range increments.</b></span><br>";
						$this->body .= "<a href=index.php?a=37&b=6&e=".$price_plan_id."&f=".$category_id."><span class=medium_font>\n\t<b>edit \"price range\" increments</b></span></a>";
						$this->body .= "</td>\n\t</tr>\n";

						//charge based on length of listing
						$this->body .= "<tr class=row_color2><td valign=top align=right>";
						$this->body .= "<input type=radio name=h[charge_per_ad_type] value=2 ";
						if ($show_price_plan["charge_per_ad_type"] == 2)
							$this->body .= "checked";
						$this->body .= "></td><td width=60% valign=top class=medium_font>\n\t<b>Charge per listing based on its length</b> <br>";

						$this->sql_query = "select * from ".$this->classified_price_plan_lengths_table." where price_plan_id = ".$price_plan_id." and category_id = ".$category_id." order by length_of_ad asc";
						$result = $db->Execute($this->sql_query);
						//echo $this->sql_query." is the bracket display query<br>\n";
						if (!$result)
							return false;
						elseif ($result->RecordCount() > 0)
						{
							$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=100% class=row_color2>\n";
							$this->body .= "<tr bgcolor=000066>\n\t<td class=medium_font_light align=center>\n\t<b>Length of listing<br>(displayed)</b></td>\n\t
								<td class=medium_font_light align=center>\n\t<b>Length of listing<br>(days)</b></td>\n\t
									<td class=medium_font_light align=center>\n\t<b>Listing fee assessed:</b></td>\n\t
								<td class=medium_font_light align=center>\n\t<b>Renewal fee assessed:</b></td>\n</tr>\n";
							$this->row_count = 0;
							while ($show = $result->FetchRow())
							{
								$this->body .= "<tr class=".$this->get_row_color().">\n\t
									<td class=medium_font>\n\t".$show["display_length_of_ad"]."\n\t</td>\n\t
									<td class=medium_font>\n\t".$show["length_of_ad"]."\n\t</td>\n\t
									<td class=medium_font class=medium_font align=center>\n\t".$show["length_charge"]."\n\t</td>
									<td class=medium_font class=medium_font align=center>\n\t".$show["renewal_charge"]."\n\t</td>\n</tr>\n";
								$this->row_count++;
							} //end of while
							$this->body .= "</table>\n";
						}
						else
							$this->body .= "<span class=medium_font>There are currently no charge per length range increments</span><br>";
						$this->body .= "<a href=index.php?a=37&b=7&c=".$price_plan_id."&e=".$category_id."><span class=medium_font>\n\t<b>edit \"listing length\" choices</b></span></a>";
						$this->body .= "</td>";
					}
					else
					{
						$this->body .= "<td align=right width=50% valign=top colspan=2 class=large_font><b>charge per listing:".$this->show_tooltip(1,1)."</td>
							<td><input type=hidden name=h[charge_per_ad_type] value=1>";
						$this->charge_select_box($show_price_plan["charge_per_ad"],"h[charge_per_ad]");
						$this->body .= "</td>";
					}
					$this->body .= "</tr>";

					//listing renewal cost
					$this->body .= "<tr class=row_color1>\n\t<td align=right colspan=2 class=medium_font>\n\t<b>listing renewal cost: </b>\n\t</td>\n\t";
					$this->body .= "<td>\n\t";
					$this->charge_select_box($show_price_plan["ad_renewal_cost"],"h[ad_renewal_cost]");
					$this->body .= "</td>\n\t</tr>\n";

					//instant cash renewal
					if(!$category_id)
					{
						$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=right colspan=2 class=medium_font><b>instant cash renewals: </b>".$this->show_tooltip(2,1)."</td>
								<td colspan=2 class=medium_font>";
						if ($show_price_plan["instant_cash_renewals"])
							$this->body .= "
									<input type=radio name=h[instant_cash_renewals] value=1 checked>yes<br>
									<input type=radio name=h[instant_cash_renewals] value=0>no";
						else
							$this->body .= "
									<input type=radio name=h[instant_cash_renewals] value=1>yes<br>
									<input type=radio name=h[instant_cash_renewals] value=0 checked>no";
						$this->body .= "
								</td>
							</tr>";$this->row_count++;
						$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=right colspan=2 class=medium_font><b>instant check renewals: </b>".$this->show_tooltip(3,1)."</td>
								<td colspan=2 class=medium_font>";
						if ($show_price_plan["instant_check_renewals"])
							$this->body .= "
									<input type=radio name=h[instant_check_renewals] value=1 checked>yes<br>
									<input type=radio name=h[instant_check_renewals] value=0>no";
						else
							$this->body .= "
									<input type=radio name=h[instant_check_renewals] value=1>yes<br>
									<input type=radio name=h[instant_check_renewals] value=0 checked>no";
						$this->body .= "
								</td>
							</tr>";$this->row_count++;
						$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=right colspan=2 class=medium_font><b>instant money order renewals: </b>".$this->show_tooltip(4,1)."</td>
								<td colspan=2 class=medium_font>";
						if ($show_price_plan["instant_money_order_renewals"])
							$this->body .= "
									<input type=radio name=h[instant_money_order_renewals] value=1 checked>yes<br>
									<input type=radio name=h[instant_money_order_renewals] value=0>no";
						else
							$this->body .= "
									<input type=radio name=h[instant_money_order_renewals] value=1>yes<br>
									<input type=radio name=h[instant_money_order_renewals] value=0 checked>no";
						$this->body .= "
								</td>
							</tr>";$this->row_count++;
						$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=right colspan=2 class=medium_font><b>allow credits to be used for renewals: </b>".$this->show_tooltip(5,1)."</td>
								<td colspan=2 class=medium_font>";
						if ($show_price_plan["allow_credits_for_renewals"])
							$this->body .= "
									<input type=radio name=h[allow_credits_for_renewals] value=1 checked>yes<Br>
									<input type=radio name=h[allow_credits_for_renewals] value=0>no";
						else
							$this->body .= "
									<input type=radio name=h[allow_credits_for_renewals] value=1>yes<Br>
									<input type=radio name=h[allow_credits_for_renewals] value=0 checked>no";
						$this->body .= "
								</td>
							</tr>";$this->row_count++;
						$this->body .= "<tr class=".$this->get_row_color().">
								<td align=right colspan=2 class=medium_font><b>charge per picture: </b></td>
								<td>";
						$this->charge_select_box($show_price_plan["charge_per_picture"],"h[charge_per_picture]");
						$this->body .= "</td>
							</tr>";$this->row_count++;
					}

				}
				elseif ($base_price_plan["type_of_billing"] == 2)
				{
					//expire ads when subscription expires
					$this->body .= "
							<tr class=".$this->get_row_color().">
								<td colspan=2 align=right valign=top class=medium_font>
									expire ads when subscription expires
								</td>
								<td class=medium_font>
									<input type=radio name=h[ad_and_subscription_expiration] value=1
									".(($show_price_plan['ad_and_subscription_expiration']==1) ? " checked" : "").">
									yes<Br>
									<input type=radio name=h[ad_and_subscription_expiration] value=0
									".(($show_price_plan['ad_and_subscription_expiration']==0) ? " checked" : "").">
									no
								</td>
							</tr>";$this->row_count++;
				}

				// max # of listings
				if(!$category_id)
				{
					$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right class=medium_font colspan=2><b>max listings allowed at one time:</b></td>
							<td colspan=2 class=medium_font>
								<select name=h[max_ads_allowed]>";
					for ($i=0;$i < 101;$i++)
					{
						if ($show_price_plan["max_ads_allowed"] == $i)
							$this->body .= "<option selected>".$i."</option>\n";
						else
							$this->body .= "<option>".$i."</option>\n";
					}
					for ($i=110;$i <= 999;($i=$i+10))
					{
						if ($show_price_plan["max_ads_allowed"] == $i)
							$this->body .= "<option selected>".$i."</option>\n";
						else
							$this->body .= "<option>".$i."</option>\n";
					}
					for ($i=1000;$i <= 10000;$i = $i + 1000)
					{
						if ($show_price_plan["max_ads_allowed"] == $i)
							$this->body .= "<option selected>".$i."</option>\n";
						else
							$this->body .= "<option>".$i."</option>\n";
					}
					$this->body .= "
								</select>
							</td>
						</tr>";$this->row_count++;

					if($base_price_plan["applies_to"] == 2)
					{
					// buy now only
						$this->body .= "
						<tr class=".$this->get_row_color().">
							<td class='medium_font' align='right' colspan=2>
								all auctions placed are buy now auctions only:
								<a href='javascript:void(0)' onmouseover='stm(Text[7],Style[1])' onmouseout='mig_hide(1)'><img src='admin_images/help.gif' border='0'></a><br>
								NOTE: ENABLING THIS OPTION WILL DISABLE DUTCH AUCTIONS.
							</td>
							<td colspan='2' class='medium_font'>";
						if($base_price_plan["buy_now_only"])
							$this->body .= "
								<input name='h[buy_now_only]' value='1' type='radio' checked> yes<br>
								<input name='h[buy_now_only]' value='0' type='radio'> no";
						else
							$this->body .= "
								<input name='h[buy_now_only]' value='1' type='radio'> yes<br>
								<input name='h[buy_now_only]' value='0' type='radio' checked> no";
						$this->body .= "
							</td>
						</tr>";$this->row_count++;
						$this->body .= "
						<tr class=".$this->get_row_color().">
							<td valign=top align=right class=medium_font>Charge a percentage of final bid at end of auction</td>
							<td class=medium_font>";
						if ($show_price_plan["charge_percentage_at_auction_end"] == 1)
							$this->body .= "
								<input type=radio name=h[charge_percentage_at_auction_end] value=1 checked> Yes<br>
								<input type=radio name=h[charge_percentage_at_auction_end] value=0> No";
						else
							$this->body .= "
								<input type=radio name=h[charge_percentage_at_auction_end] value=1> Yes<br>
								<input type=radio name=h[charge_percentage_at_auction_end] value=0 checked> No";

						$this->body .= "
							</td>
							<td width=60% valign=top class=medium_font>";

						$this->sql_query = "select * from geodesic_auctions_final_fee_price_increments where price_plan_id = ".$price_plan_id." order by low asc";
						$result = $db->Execute($this->sql_query);
						if (!$result)
							return false;
						elseif ($result->RecordCount() > 0)
						{
							$this->body .= "
								<table cellpadding=3 cellspacing=1 border=0 width=100% class=row_color2>
									<tr class=row_color_black>
										<td class=medium_font_light>low end of increment bracket</td>
										<td>&nbsp;</td>
										<td class=medium_font_light>high end</td>
										<td class=medium_font_light>final fee charge percentage</td>
									</tr>";
							$this->row_count = 0;
							while ($show = $result->FetchRow())
							{
								$this->body .= "
									<tr class=".$this->get_row_color().">
										<td class=medium_font>".$show["low"]."</td>
										<td class=medium_font>to</td>
										<td class=medium_font>";
								if ($show["high"] == 100000000)
									$this->body .= "and up";
								else
									$this->body .= $show["high"];
								$this->body .= "</td>
										<td class=medium_font>".$show["charge"]." %</td>
									</tr>";
								$this->row_count++;
							}
							$this->body .= "
								</table>";
						}
						else
							$this->body .= "
								<b class=medium_error_font>There are currently no final fee charge per final price range increments. YOU MUST HAVE AT LEAST ONE FINAL FEE INCREMENT ATTACHED TO WORK.  YOU MAY EXPERIENCE ERRORS IN YOUR SELL PROCESS WITHOUT ONE.</b><br>";
						if(!$category_id)
							$this->body .= "
								<a href=index.php?a=37&b=8&e=".$price_plan_id."&price_plan=".$price_plan_id." class=medium_font><img src=admin_images/btn_admin_edit.gif border=0 alt=edit></a>";
						$this->body .= "
								</td>
							</tr>
							<tr class=row_color2>
								<td align=right class=medium_font colspan=2>roll final fee charges into future auction placement:".$this->show_tooltip(6,1)."</td>
								<td class=medium_font>";
						if ($show_price_plan["roll_final_fee_into_future"] == 1)
							$this->body .= "
									<input type=radio name=h[roll_final_fee_into_future] value=1 checked> Yes
									<input type=radio name=h[roll_final_fee_into_future] value=0> No";
						else
							$this->body .= "
									<input type=radio name=h[roll_final_fee_into_future] value=1>Yes
									<input type=radio name=h[roll_final_fee_into_future] value=0 checked> No";
						$this->body .= "
								</td>
							</tr>";
					}

					$this->sql_query = "select maximum_photos from ".$this->ad_configuration_table;
					//echo $this->sql_query.'<br>';
					$result = $db->Execute($this->sql_query);
					if($result)
						$show_configuration = $result->FetchRow();
					else
						$this->body .= "Error couldnt get maximum number of pictures<br>";

					if ($this->debug_price_plan)
					{
						echo $show_configuration["maximum_photos"]." is maximum_photos<BR>\n";
					}

					$this->body .= "<tr class=row_color1>\n\t<td align=right colspan=2 class=medium_font>\n\t<b>number of free pics:</b>".$this->show_tooltip(6,1)."</td>\n\t";
					$this->body .= "<td colspan=2 class=medium_font>\n\t<select name=h[num_free_pics]>\n\t\t";
					for ($i=0;$i <= $show_configuration["maximum_photos"];$i++)
					{
						$this->body .= "<option ";
						if ($show_price_plan["num_free_pics"] == $i)
							$this->body .= "selected";
						$this->body .= ">".$i."</option>\n\t\t";
					}
					$this->body .= "</select></font></td>\n\t</tr>\n";
					$this->row_count++;
				}



				// Get site configuration data
				$this->sql_query = "select * from ".$this->site_configuration_table;
				$result = $db->Execute($this->sql_query);

				if($result)
					$site_config = $result->FetchRow();

				if(!$category_id)
					$this->body .= "<tr bgcolor=000066>\n\t<td colspan=4 class=medium_font_light align=center>\n\t<b>LISTING EXTRAS</b> </td></tr>";
				for($i = 1; $i < 6; $i++)
				{
					$suffix = ($i == 1) ? "" : "_$i";
					$level_suffix = ($i == 1) ? "" : "_level_$i";
					if($site_config["use_featured_feature$suffix"])
					{
						// featured listings
						$this->body .= "<tr class=row_color_red>\n\t<td colspan=4 class=medium_font_light>\n\t<b>Featured Listings Level $i</b></td></tr>\n";
						$this->body .= "<tr>\n\t<td width=50% align=right colspan=2 valign=top class=medium_font>\n\t<b>use: </b></td>\n\t";
						$this->body .= "<td colspan=4 valign=top class=medium_font>\n\t<input type=radio name=h[use_featured_ads$level_suffix] value=1 ";
						if ($show_price_plan["use_featured_ads$level_suffix"] == 1)
							$this->body .= "checked";
						$this->body .= "> yes<br><input type=radio name=h[use_featured_ads$level_suffix] value=0 ";
						if ($show_price_plan["use_featured_ads$level_suffix"] == 0)
							$this->body .= "checked";
						$this->body .= "> no\n\t</td>\n</tr>\n";

						$this->body .= "
							<tr class=row_color2>
								<td align=right colspan=2 class=medium_font><b>fee: </b></td>
								<td>";
						$this->charge_select_box($show_price_plan["featured_ad_price$suffix"],"h[featured_ad_price$suffix]");
						$this->body .= "</td>
							</tr>";
					}
				}

				if($site_config["use_bolding_feature"])
				{
					// bolding
					$this->body .= "<tr class=row_color_red>\n\t<td colspan=4 class=medium_font_light>\n\t<b>Bolding</b></td></tr>\n";
					$this->body .= "<tr>\n\t<td align=right colspan=2 valign=top class=medium_font>\n\t<b>use: </b></td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=h[use_bolding] value=1 ";
					if ($show_price_plan["use_bolding"] == 1)
						$this->body .= "checked";
					$this->body .= "> yes<br><input type=radio name=h[use_bolding] value=0 ";
					if ($show_price_plan["use_bolding"] == 0)
						$this->body .= "checked";
					$this->body .= "> no\n\t</td>\n</tr>\n";

					$this->body .= "
						<tr class=row_color2>
							<td align=right colspan=2 class=medium_font><b>fee: </b></td>
							<td>";
					$this->charge_select_box($show_price_plan["bolding_price"],"h[bolding_price]");
					$this->body .= "</td>
						</tr>";
				}

				if($site_config["use_better_placement_feature"])
				{
					// better placement
					$this->body .= "<tr class=row_color_red>\n\t<td colspan=4 class=medium_font_light>\n\t<b>Better Placement</b></td></tr>\n";
					$this->body .= "<tr>\n\t<td align=right colspan=2 valign=top class=medium_font>\n\t<b>use: </b></td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=h[use_better_placement] value=1 ";
					if ($show_price_plan["use_better_placement"] == 1)
						$this->body .= "checked";
					$this->body .= "> yes<br><input type=radio name=h[use_better_placement] value=0 ";
					if ($show_price_plan["use_better_placement"] == 0)
						$this->body .= "checked";
					$this->body .= "> no\n\t</td>\n</tr>\n";

					$this->body .= "
						<tr class=row_color2>
							<td align=right colspan=2 class=medium_font><b>fee: </b></td>
							<td>";
					$this->charge_select_box($show_price_plan["better_placement_charge"],"h[better_placement_charge]");
					$this->body .= "</td>
						</tr>";
				}

				if($site_config["use_attention_getters"])
				{
					// attention getters
					$this->body .= "<tr class=row_color_red>\n\t<td colspan=4 class=medium_font_light>\n\t<b>Attention Getters</b></td></tr>\n";
					$this->body .= "<tr>\n\t<td align=right colspan=2 valign=top class=medium_font>\n\t<b>use: </b></td>\n\t";
					$this->body .= "<td valign=top class=medium_font>\n\t<input type=radio name=h[use_attention_getters] value=1 ";
					if ($show_price_plan["use_attention_getters"] == 1)
						$this->body .= "checked";
					$this->body .= "> yes<br><input type=radio name=h[use_attention_getters] value=0 ";
					if ($show_price_plan["use_attention_getters"] == 0)
						$this->body .= "checked";
					$this->body .= "> no\n\t</td>\n</tr>\n";

					$this->body .= "
						<tr class=row_color2>
							<td align=right colspan=2 class=medium_font><b>fee: </b></td>
							<td>";
					$this->charge_select_box($show_price_plan["attention_getter_price"],"h[attention_getter_price]");
					$this->body .= "</td>
						</tr>";
				}
				if (!$this->admin_demo())
				{
					$this->body .= "
								<tr>
									<td colspan=4 align=center><input type=submit name=save_group value=\"save\"></td>
								</tr>";
				}

				if ($category_id)
				{
					$this->body .= "
							<tr>
								<td colspan=4>
									<a href=index.php?a=37&b=5&x=".$price_plan_id."&d=".$show_parent_id["parent_id"].">
										<span class=medium_font><b>back to ".$category_name." - ".$price_plan_name." Details</b></span>
									</a>
								</td>
							</tr>";
				}
				elseif ($show_price_plan["type_of_billing"] == 1)
				{
					$this->body .= "
							<tr>
								<td colspan=2>
									<a href=index.php?a=37&b=5&x=".$price_plan_id.">
										<span class=medium_font></span>
									</a>
								</td>
							</tr>";
				}

				$this->body .= "
							<tr>
								<td colspan=4>
									<a href=index.php?a=37&b=3&g=".$price_plan_id.">
										<span class=medium_font><b>back to ".$price_plan_name." Details</b></span>
									</a>
								</td>
							</tr>
							<tr>
								<td colspan=4><a href=index.php?a=37><span class=medium_font><b>back to Price Plan Home</b></span></a></td>
							</tr>
						</table>
					</form>";
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
	} //end of function category_specific_price_plan_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_increments($db, $price_plan_id=0, $increment_number=0)
	{
		$this->function_name = "delete_increments";

		if(!($price_plan_id) && !($increment_number))
			return false;

		$sql_query = "select * from geodesic_auctions_final_fee_price_increments where price_plan_id = ".$price_plan_id." order by low asc";
		$result = $db->Execute($sql_query);
		if (!$result)
			return false;
		elseif($result->RecordCount() < $increment_number)
			return false;
		else
		{
			// Find the final fee element to delete
			// since it goes by indices it finds it from the record number
			$i = 0;
			do
			{
				$i++;
				$show = $result->FetchRow();
			} while($i != $increment_number);

			// Since we found it, delete the correct increment
			$this->sql_query = "delete from geodesic_auctions_final_fee_price_increments where price_plan_id = ".$price_plan_id." and low = ".$show->LOW."
				 and high = ".$show->HIGH." and charge = ".$show->CHARGE;
			$result = $db->Execute($this->sql_query);
			if(!$result)
				return false;
		}

		return true;
	} //end of function delete_increments

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_category_specific_price_plan($db,$category_id=0,$price_plan_id=0,$info=0)
	{
		if (($category_id) && ($price_plan_id) && ($info))
		{
			//make sure one does not already exist
			$this->sql_query = "select * from ".$this->classified_price_plans_categories_table." where price_plan_id = ".$price_plan_id." and category_id = ".$category_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() == 0)
			{
				//none there...insert
				$this->first_sql_query = "insert into ".$this->classified_price_plans_categories_table."
					(category_id,price_plan_id,charge_per_ad_type,
					ad_renewal_cost";




				$this->second_sql_query = ") values
					(".$category_id.",".$price_plan_id.",".$info["charge_per_ad_type"].",
					".$info["ad_renewal_cost"][0].".".sprintf("%02d",$info["ad_renewal_cost"][1]);

				if (strlen(trim($info["use_featured_ads"])) > 0)
				{
					$this->first_sql_query .= ",use_featured_ads";
					$this->second_sql_query .= ",".$info["use_featured_ads"];
				}

				if (strlen(trim($info["use_featured_ads_level_2"])) > 0)
				{
					$this->first_sql_query .= ",use_featured_ads_level_2";
					$this->second_sql_query .= ",".$info["use_featured_ads_level_2"];
				}

				if (strlen(trim($info["use_featured_ads_level_3"])) > 0)
				{
					$this->first_sql_query .= ",use_featured_ads_level_3";
					$this->second_sql_query .= ",".$info["use_featured_ads_level_3"];
				}

				if (strlen(trim($info["use_featured_ads_level_4"])) > 0)
				{
					$this->first_sql_query .= ",use_featured_ads_level_4";
					$this->second_sql_query .= ",".$info["use_featured_ads_level_4"];
				}

				if (strlen(trim($info["use_featured_ads_level_5"])) > 0)
				{
					$this->first_sql_query .= ",use_featured_ads_level_5";
					$this->second_sql_query .= ",".$info["use_featured_ads_level_5"];
				}

				if (strlen(trim($info["use_bolding"])) > 0)
				{
					$this->first_sql_query .= ",use_bolding";
					$this->second_sql_query .= ",".$info["use_bolding"];
				}

				if (strlen(trim($info["use_better_placement"])) > 0)
				{
					$this->first_sql_query .= ",use_better_placement";
					$this->second_sql_query .= ",".$info["use_better_placement"];
				}

				if (strlen(trim($info["use_attention_getters"])) > 0)
				{
					$this->first_sql_query .= ",use_attention_getters";
					$this->second_sql_query .= ",".$info["use_attention_getters"];
				}
				$this->sql_query = $this->first_sql_query.$this->second_sql_query.")";
				//echo $this->sql_query."<br>\n";
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
				return false;
			}
		}
		else
		{
			return false;
		}
	} //end of function insert_category_specific_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_category_specific_price_plan($db,$category_price_plan_id=0,$info=0, $category_specific = 1)
	{
		if ($category_price_plan_id && $info)
		{
			if($category_specific)
			{
				$table = "geodesic_classifieds_price_plans_categories";
				$this->sql_query = "select * from geodesic_classifieds_price_plans_categories as cat,{$this->price_plan_table} as gen where cat.category_price_plan_id = $category_price_plan_id and cat.price_plan_id = gen.price_plan_id";
			}
			else
			{
				$table = $this->price_plan_table;
				$this->sql_query = "select * from {$this->price_plan_table} where price_plan_id = $category_price_plan_id";
			}
			$type_result = $db->Execute($this->sql_query);
			if (!$type_result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			if($type_result->RecordCount() == 1)
				$show_type = $type_result->FetchRow();
			else
				return false;


			$this->sql_query = "update $table set ";
			if(@$show_type["type_of_billing"] == 1)
			{
				if(!$category_specific)
				{
					$this->sql_query .= "
						instant_cash_renewals = ".$info["instant_cash_renewals"].",
						instant_check_renewals = ".$info["instant_check_renewals"].",
						instant_money_order_renewals = ".$info["instant_money_order_renewals"].",
						allow_credits_for_renewals = ".$info["allow_credits_for_renewals"].", ";
				}
				$this->sql_query .= "
					charge_per_ad_type = ".$info["charge_per_ad_type"].",
					charge_per_ad = ".$info["charge_per_ad"][0].".".sprintf("%02d",$info["charge_per_ad"][1]).",
					ad_renewal_cost = ".$info["ad_renewal_cost"][0].".".sprintf("%02d",$info["ad_renewal_cost"][1]).", ";
			}
			else
			{
				$this->sql_query .= "
					subscription_billing_period = \"".$info["subscription_billing_period"]."\",
					subscription_billing_charge_per_period = \"".$info["subscription_billing_charge_per_period"][0].".".sprintf("%02d",$info["subscription_billing_charge_per_period"][1])."\",
					free_subscription_period_upon_registration = \"".$info["free_subscription_period_upon_registration"]."\",
					ad_and_subscription_expiration = \"".$info["ad_and_subscription_expiration"]."\", ";
			}

			if(!$category_specific)
			{
				$this->sql_query .= "
					num_free_pics = '".$info["num_free_pics"]."',
					max_ads_allowed = '".$info["max_ads_allowed"]."',
					charge_percentage_at_auction_end = '".$info["charge_percentage_at_auction_end"]."',
					roll_final_fee_into_future = '".$info["roll_final_fee_into_future"]."', ";
			}

			$this->sql_query .= "
					charge_per_picture = \"".$info["charge_per_picture"][0].".".sprintf("%02d",$info["charge_per_picture"][1])."\", ";

			if($show_type["applies_to"] == 2)
				$this->sql_query .= "buy_now_only = ".(($info["buy_now_only"]) ? $info["buy_now_only"] : 0).", ";
			// featured listings
			for($i = 1; $i < 6; $i++)
			{
				$suffix = ($i == 1) ? "" : "_$i";
				$level_suffix = ($i == 1) ? "" : "_level_$i";

				$this->sql_query .=	"use_featured_ads$level_suffix = ".(($info["use_featured_ads$level_suffix"]) ? $info["use_featured_ads$level"] : "0").", ";
				$this->sql_query .= "featured_ad_price$suffix = ".$info["featured_ad_price$suffix"][0].".".sprintf("%02d",$info["featured_ad_price$suffix"][1]).", ";
			}

			$this->sql_query .=	"use_bolding = ".(($info["use_bolding"]) ? $info["use_bolding"] : "0").", ";
			$this->sql_query .= "bolding_price = ".$info["bolding_price"][0].".".sprintf("%02d",$info["bolding_price"][1]).", ";

			$this->sql_query .=	"use_better_placement = ".(($info["use_better_placement"]) ? $info["use_better_placement"] : "0").", ";
			$this->sql_query .= "better_placement_charge = ".$info["better_placement_charge"][0].".".sprintf("%02d",$info["better_placement_charge"][1]).", ";

			$this->sql_query .=	"use_attention_getters = ".(($info["use_attention_getters"]) ? $info["use_attention_getters"] : "0").", ";
			$this->sql_query .= "attention_getter_price = ".$info["attention_getter_price"][0].".".sprintf("%02d",$info["attention_getter_price"][1]).", ";

			$this->sql_query = rtrim($this->sql_query, ", ");
     		$this->sql_query .= " where ".(($category_specific) ? "category_price_plan_id" : "price_plan_id")." = ".$category_price_plan_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			else
				return true;
		}
		else
			return false;
	} //end of function update_category_specific_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_category_specific_price_plan($db,$category_price_plan_id=0)
	{
		if ($category_price_plan_id)
		{
			$this->sql_query = "delete from ".$this->classified_price_plans_categories_table."
				where category_price_plan_id = ".$category_price_plan_id;
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
			return false;
		}
	} //end of function delete_category_specific_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_subscription_periods($db,$price_plan_id=0)
	{
		//echo $price_plan_id." is price plan id<br>";
		$price_plan_name = $this->get_price_plan_name($db,$price_plan_id);
		//echo $price_plan_name." is price plan name<br>";
		if (!$price_plan_id || !$price_plan_name)
			return false;
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center width=100% class=row_color1>\n";
		$this->title = "Pricing > Price Plans > Edit Subscription Periods";
		$this->description = "Below are the current
			subscription choices offered to users on this price plan.  Delete choices by clicking the delete link next to the
			appropriate choice.  Add new choices by clicking the add new choice link at the bottom.";

		$this->body .= "<tr bgcolor=000066>\n\t<td colspan=5 class=large_font_light align=center>\n\t<b>Price Plan: ".$price_plan_name."</b>\n\t</td>\n</tr>\n";
		$this->sql_query = "select * from ".$this->classified_subscription_choices_table." where price_plan_id = ".$price_plan_id." order by value";
		//echo $this->sql_query." is query1<br>";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->body .= "<tr class=row_color_black>\n\t
				<td class=medium_font_light>\n\t<b>length of period</b> \n\t</td>\n\t
				<td class=medium_font_light>\n\tlength in days\n\t</td>\n\t
				<td class=medium_font_light align=center>\n\t<b>cost</b> \n\t</td>\n\t
				<td class=medium_font_light>\n\t&nbsp; \n\t</td>\n\t
				</tr>\n";
			$this->row_count = 0;
			while ($show_subscriptions = $result->FetchRow())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t
					<td class=medium_font>\n\t".$show_subscriptions["display_value"]." \n\t</td>\n\t
					<td class=medium_font>\n\t".$show_subscriptions["value"]." days\n\t</td>\n\t
					<td class=medium_font align=center>\n\t".sprintf("%0.2f",$show_subscriptions["amount"])." \n\t</td>\n\t
					<td align=center width=100>\n\t<a href=index.php?a=37&b=3&f=6&g=".$price_plan_id."&h=".$show_subscriptions["period_id"]."><span class=medium_font>\n\t<img src=\"admin_images/btn_admin_delete.gif\" border=0></span></a>\n\t</td>\n\t
					</tr>\n";
				$this->row_count++;
			}
		}
		else
		{
			//none...allow to add
			$this->body .= "<tr>\n\t<td colspan=3 class=medium_font align=center>\n\t<br><br><b>There are currently no Subscription Periods for this Price Plan.</b><br><br> </td>\n</tr>\n";
		}
		$this->body .= "<tr>\n\t<td align=center>\n\t<a href=index.php?a=37&b=3&f=7&g=".$price_plan_id.">
			<span class=medium_font>\n\t<b>add new Subscription Period choice</b></span></a></td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td align=center>\n\t<a href=index.php?a=37&b=3&f=3&g=".$price_plan_id.">
			<span class=medium_font>\n\t<b>back to Cost Specifics</b></span></a></td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;
	} //end of function display_subscription_periods

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function subscription_period_form($db,$price_plan_id=0)
	{
		$price_plan_name = $this->get_price_plan_name($db,$price_plan_id);
		if (($price_plan_id) && ($price_plan_name))
		{
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=3&f=7&g=".$price_plan_id." method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Pricing > Price Plans > Edit Subscription Periods > New Subscription Choice";
			$this->description = "Use this form to enter a new Subscription Period for this Price Plan.
				Enter the specifics below and then click the \"enter choice\" button at the bottom.";

			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=5 class=large_font_light align=center>\n\t<b>Price Plan: ".$price_plan_name."</b>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color1>
				<td align=right width=50% class=medium_font>\n\tdisplay value: \n\t</td>\n\t
				<td class=medium_font>\n\t<input type=text name=d[display_value]> ie 30 days \n\t</td>\n\t
				</tr>\n";
			$this->body .= "<tr class=row_color2>
				<td align=right width=50% class=medium_font>\n\tnumber of days of subscription period: \n\t</td>\n\t
				<td class=medium_font>\n\t<select name=d[value]>";
			for ($i=1;$i < 1826;$i++)
			{
				$this->body .= "<option>".$i."</option>\n\t";
			}
			$this->body .= "</select> \n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color1>
				<td align=right width=50% class=medium_font>\n\tamount to charge for period: \n\t</td>\n\t
				<td class=medium_font>\n\t<select name=d[period_dollars]>";
			for ($i=0;$i < 5001;$i++)
			{
				$this->body .= "<option>".$i."</option>\n\t";
			}
			$this->body .= "</select><select name=d[period_cents]>";
			for ($i=0;$i < 100;$i++)
			{
				$this->body .= "<option>".sprintf("%02d",$i)."</option>\n\t";
			}
			$this->body .= "</select> \n\t</td>\n</tr>\n";
			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2 align=center><input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}
		else
		{
			return false;
		}
	} //end of subscription_period_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_subscription_period($db,$price_plan_id=0,$subscription_info=0)
	{
		if (($price_plan_id) && ($subscription_info))
		{
			$this->sql_query = "insert into ".$this->classified_subscription_choices_table."
				(price_plan_id,display_value,value,amount)
				values
				(".$price_plan_id.",\"".$subscription_info["display_value"]."\",".$subscription_info["value"].",".$subscription_info["period_dollars"].".".$subscription_info["period_cents"].")";
			$insert_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$insert_result)
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
			return false;
		}
	} //end of function insert_subscription_period

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_subscription_period($db,$subscription_period_id=0)
	{
		if ($subscription_period_id)
		{
			$this->sql_query = "delete from ".$this->classified_subscription_choices_table." where period_id = ".$subscription_period_id;
			$delete_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$delete_result)
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
			return false;
		}
	} //end of function insert_subscription_period

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_credit_periods($db,$price_plan_id=0)
	{
		//echo "hello from display<br>\n";
		$price_plan_name = $this->get_price_plan_name($db,$price_plan_id);
		if (($price_plan_id) && ($price_plan_name))
		{
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Price Plan Credit Choices Management";
			$this->description = "Below are the current
				choice choices offered to users on this price plan.  Delete choices by clicking the delete link next to the
				appropriate choice.  Add new choices by clicking the add new choice link at the bottom.";

			$this->sql_query = "select * from ".$this->credit_choices." where price_plan_id = ".$price_plan_id." order by value";
			$result = $db->Execute($this->sql_query);

			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$this->body .= "<tr class=row_color_black>\n\t
					<td class=medium_font_light>\n\tnumber of credits \n\t</td>\n\t
					<td class=medium_font_light>\n\tcost \n\t</td>\n\t
					<td class=medium_font_light>\n\t&nbsp; \n\t</td>\n\t
					</tr>\n";
				$this->row_count = 0;
				while ($show_credits = $result->FetchRow())
				{
					$this->body .= "<tr class=".$this->get_row_color().">\n\t
						<td class=medium_font>\n\t".$show_credits["display_value"]." \n\t</td>\n\t
						<td class=medium_font>\n\t".sprintf("%0.2f",$show_credits["amount"])." \n\t</td>\n\t
						<td>\n\t<a href=index.php?a=37&b=3&f=9&g=".$price_plan_id."&h=".$show_credits["credit_id"]."><span class=medium_font>\n\tdelete</span></a>\n\t</td>\n\t
						</tr>\n";
					$this->row_count++;
				}
			}
			else
			{
				//none...allow to add
				$this->body .= "<tr>\n\t<td colspan=3 class=medium_font>\n\tthere are no credit plans to choose from </td>\n</tr>\n";
			}
			$this->body .= "<tr>\n\t<td align=center><a href=index.php?a=37&b=3&f=10&g=".$price_plan_id."><span class=medium_font>\n\tadd a new credit choice</span></a></td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}
		else
		{
			return false;
		}

	} //end of function display_credit_periods

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function credit_period_form($db,$price_plan_id=0)
	{
		$price_plan_name = $this->get_price_plan_name($db,$price_plan_id);
		if (($price_plan_id) && ($price_plan_name))
		{
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=3&f=10&g=".$price_plan_id." method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Pricing > Price Plans > Add New Credit Choice";
			$this->description = "Use this form to enter a new credit choice.
				Enter the specifics below and then click the \"enter choice\" button at the bottom.";

			$this->body .= "<tr class=row_color1>
				<td align=right width=50% class=medium_font>\n\tdisplay value: \n\t</td>\n\t
				<td class=medium_font>\n\t<input type=text name=d[display_value]> ie 30 days \n\t</td>\n\t
				</tr>\n";
			$this->body .= "<tr class=row_color2>
				<td align=right width=50% class=medium_font>\n\tnumber of credits: \n\t</td>\n\t
				<td class=medium_font>\n\t<select name=d[value]>";
			for ($i=1;$i < 1826;$i++)
			{
				$this->body .= "<option>".$i."</option>\n\t";
			}
			$this->body .= "</select> \n\t</td>\n</tr>\n";

			$this->body .= "<tr class=row_color1>
				<td align=right width=50% class=medium_font>\n\tamount to charge for above credits: \n\t</td>\n\t
				<td class=medium_font>\n\t<select name=d[credit_dollars]>";
			for ($i=1;$i < 1001;$i++)
			{
				$this->body .= "<option>".$i."</option>\n\t";
			}
			$this->body .= "</select><select name=d[credit_cents]>";
			for ($i=0;$i < 100;$i++)
			{
				$this->body .= "<option>".sprintf("%02d",$i)."</option>\n\t";
			}
			$this->body .= "</select> \n\t</td>\n</tr>\n";
			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2><input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}
		else
		{
			return false;
		}
	} //end of credit_period_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_credit_period($db,$price_plan_id=0,$credit_info=0)
	{
		if (($price_plan_id) && ($credit_info))
		{
			$this->sql_query = "insert into ".$this->credit_choices."
				(price_plan_id,display_value,value,amount)
				values
				(".$price_plan_id.",\"".$credit_info["display_value"]."\",".$credit_info["value"].",".$credit_info["credit_dollars"].".".$credit_info["credit_cents"].")";
			$insert_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$insert_result)
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
			return false;
		}
	} //end of function insert_subscription_period

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_credit_period($db,$credit_period_id=0)
	{
		if ($credit_period_id)
		{
			$this->sql_query = "delete from ".$this->credit_choices." where credit_id = ".$credit_period_id;
			$delete_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$delete_result)
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
			return false;
		}
	} //end of function delete_credit_period

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%


	function add_length($db,$price_plan_id=0,$new_length_info=0,$category_id=0)
	{
		//echo $price_plan_id." is price_plan_id<br>\n";
		//echo $new_length_info." is new_length_info<br>\n";
		//echo $category_id." is category_id<br>\n";
		if (!$category_id)
			$category_id = 0;
		if (($new_length_info) && ($price_plan_id))
		{
			//check length_of_ad to see if int
			//check length_charge to see if double or int
			if (ereg("[0-9]+", $new_length_info["length_of_ad"]))
			{
				$this->sql_query = "select * from  ".$this->classified_price_plan_lengths_table."
					where length_of_ad = ".$new_length_info["length_of_ad"]." and price_plan_id = ".$price_plan_id." and category_id = ".$category_id;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<Br>";
				if (!$result)
				{
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($result->RecordCount() == 0 )
				{
					$this->sql_query = "insert into ".$this->classified_price_plan_lengths_table."
						(price_plan_id,category_id,length_of_ad,display_length_of_ad,length_charge,renewal_charge)
						values
						(".$price_plan_id.",".$category_id.",".$new_length_info["length_of_ad"].",\"".$new_length_info["display_length_of_ad"]."\",".$new_length_info["length_charge"].",".$new_length_info["renewal_charge"].")";
					$insert_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<Br>";
					if (!$insert_result)
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
				$this->ad_configuration_message = "Please only enter numbers";
				return true;
			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function add_length

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_length($db,$length_id=0)
	{
		if ($length_id)
		{
			$this->sql_query = "delete from  ".$this->classified_price_plan_lengths_table." where length_id = ".$length_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<Br>";
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
	} //end of function delete_length

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function increments_form($db,$price_plan_id=0,$category_id=0)
	{
		if ($price_plan_id)
		{
			$price_plan_name = $this->get_price_plan_name($db,$price_plan_id);
			$current_price_plan = $this->get_price_plan($db,$price_plan_id);
			if ($category_id)
				$category_name = $this->get_category_name($db,$category_id);
			else
				$category_name = "all categories (default)";
			if (!$category_id)
				$category_id = 0;
			$sql_query = "select * from ".$this->price_plans_increments_table." where price_plan_id = ".$price_plan_id." and category_id = ".$category_id." order by low asc";
			$result = $db->Execute($sql_query);
			//echo $sql_query." is the bracket display query<br>\n";
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() > 0)
			{
				$price_plan_name = $this->get_price_plan_name($db,$price_plan_id);
				$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=100% class=row_color1>\n";
				$this->title = "Pricing > Price Plans > Category Specific Pricing > Price Range Increments";
				$this->description = "If you have decided to charge users of this Price Plan based upon their listing's \"price field\", you may use
				the table below to edit the fees that will be assessed per listing. Starting with 0.01 create a price field bracketing system and charge
				a fee based upon that field. Each price bracket must have a \"Price field low\" and \"Price field high\" (or be checked 'and up').";
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=5 class=large_font_light align=center>\n\t<b>Price Plan: ".$price_plan_name."</b>\n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color_red>\n\t<td colspan=5 class=large_font_light align=center>\n\t
						<span class=large_font_light> Category: ".$category_name."</span>\n\t</td>\n</tr>\n";

				$this->body .= "<tr bgcolor=000066>\n\t<td class=medium_font_light>\n\t<b>Price field low:</b></td>\n\t
					<td>&nbsp;</td>\n\t
					<td class=medium_font_light>\n\t<b>Price field high:</b></td>\n\t
					<td class=medium_font_light align=center>\n\t<b>Listing fee assessed</b></td>
					<td class=medium_font_light align=center>\n\t<b>Renewal fee assessed</b></td>\n</tr>\n";
				$this->row_count = 0;
				while ($show = $result->FetchRow())
				{
					$this->body .= "<tr class=".$this->get_row_color().">\n\t
						<td class=medium_font>\n\t".$show["low"]." ";
					$this->body .= "\n\t</td>\n\t
						<td class=medium_font>\n\tto \n\t</td>\n\t
						<td class=medium_font>\n\t";
					if ($show["high"] == 100000000)
						$this->body .= "and up";
					else
						$this->body .= $show["high"]."\n\t</td>\n\t";
					$this->body .= "<td class=medium_font align=center>\n\t".$show["charge"]."\n\t</td>\n";
					$this->body .= "<td class=medium_font align=center>\n\t".$show["renewal_charge"]."\n\t</td>\n</tr>\n";
					$this->row_count++;
				} //end of while
				$this->body .= "</table>\n";
			}
			else
			{
				//there are no brackets to display
				$this->body .= "<table cellpadding=3 cellspacing=1 border=0>\n";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100% class=row_color1>\n";
				$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=very_large_font_light>\n\t<b>Price Based Increments Brackets</b> \n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color_red>\n\t<td colspan=4 class=very_large_font_light>\n\t<b>Price Plan: ".$price_plan_name."<br>Category: ";
				if ($category_id)
					$this->body .= $category_name." and subcategories";
				else
					$this->body .= $category_name;
				$this->body .= "</b> \n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color_red>\n\t<td colspan=2 class=medium_font_light>\n\t
					You can charge different amounts to place listings using the price field value through this admin tool.  Starting with 0
					create a bracketing system and charge differently for each bracket you create.  Once you start a new bracket you must
					finish it or there will be \"holes\" where a price could not be charged to place a listing. \n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
			}

			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=6&e=".$price_plan_id."&f=".$category_id." method=post>\n";

			$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=100% class=row_color2>\n";
			$this->body .= "<tr bgcolor=000066>\n\t<td class=medium_font_light>\n\t<b>Price field low:</b></td>\n\t
				<td class=medium_font_light>\n\t \n\t</td>\n\t
				<td class=medium_font_light>\n\t<b>Price field high:</b></td>\n\t
				<td class=medium_font_light align=center>\n\t<b>Listing fee assessed</b></td>\n
				<td class=medium_font_light align=center>\n\t<b>Renewal fee assessed</b></td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td class=medium_font>\n\t";
			if ($this->debug_price_plan)
			{
				echo $this->last_high_variable." is last_high_variable<bR>\n";
			}
			if ($this->last_high_variable == 0)
				$last_high_variable_to_use = 0;
			else
				$last_high_variable_to_use = $this->last_high_variable + .01;
			$this->body .= $last_high_variable_to_use."<input type=hidden name=d[new_low] value=\"".$last_high_variable_to_use."\"></td>\n\t";
			$this->body .= "<td class=medium_font>\n\tto</td>\n\t";
			$this->body .= "<td class=medium_font>\n\t<select name=d[new_high]>\n\t\t";
			$this->body .= "<option value=100000000>and up</option>\n\t\t";
			//if ($this->last_high_variable == 0)
			//{
				for ($i = $this->last_high_variable + .25; $i <= 100;$i = $i + .25)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
				for ($i = 101; $i < 500;$i++)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
				for ($i = 501; $i < 10000;$i = $i + 5)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
				for ($i = 10001; $i < 100000;$i = $i + 1000)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
				for ($i = 100001; $i < 1000000; $i = $i + 10000)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
			//}
			//else
			//{
			//	for ($i = $this->last_high_variable + 1; $i < 5000;$i++)
			//	{
			//		echo "<option>".$i."</option>\n\t\t";
			//	}
			//}
			$this->body .= "</select>\n\t</td>\n\t";
			$this->body .= "<td align=center>\n\t<input type=text name=d[new_increment] value=\"0\">\n\t</td>\n";
			$this->body .= "<td align=center>\n\t<input type=text name=d[new_renewal_charge] value=\"0\">\n\t</td>\n</tr>\n";
			if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=5 align=center>\n\t<input type=submit value=\"Save Increment\">\n\t</td>\n</tr>\n";
			if ($category_id)
			{
				$this->sql_query = "select * from ".$this->classified_price_plans_categories_table." where price_plan_id = ".$price_plan_id." and category_id = ".$category_id;
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show_this = $result->FetchRow();
					$this->body .= "<tr>\n\t<td colspan=5>\n\t<a href=index.php?a=37&b=5&e=1&x=".$price_plan_id."&d=".$category_id."&y=".$show_this["category_price_plan_id"]."><span class=medium_font>\n\t".$price_plan_name." price plan - ".$category_name." category home</span></a>\n\t</td>\n</tr>\n";
				}
			}
			$this->body .= "<tr>\n\t<td colspan=5>\n\t<a href=index.php?a=37&b=5&x=".$price_plan_id."&d=".$show_parent_id["parent_id"]." class=medium_font>\n\t
				<b>back to Category Specific Pricing</b></a>\n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td colspan=5>\n\t<a href=index.php?a=37&b=3&g=".$price_plan_id." class=medium_font>\n\t
				<b>back to ".$price_plan_name." Details</b></a>\n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td colspan=5>\n\t<a href=index.php?a=37 class=medium_font>\n\t
					<b>back to Price Plans Home</b></a>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= $this->hidden_bracket_variables;
			$this->body .= "</form>\n";
			$this->hidden_bracket_variables = "";
			return true;
		}
		else
			return false;
	} //end of function increments_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%


	function final_fees_increments_form($db,$price_plan_id=0)
	{
		$this->function_name = "increments_form";

		$sql_query = "select * from geodesic_auctions_final_fee_price_increments where price_plan_id = ".$price_plan_id." order by low asc";
		$result = $db->Execute($sql_query);
		if($this->configuration_data->DEBUG_ADMIN)
		{
			$this->debug_display($db, $this->filename, $this->function_name, "final_fee_table", "delete final fee data");
		}
		if (!$result)
		{
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100%  class=row_color1>\n";
			$this->title .= "Final Fee % Brackets";
			$this->description .= "You can charge different amounts at the end of your auctions based upon the final price of the items sold.  Set the
					brackets that will determine the final fee percentage charged.  Each bracket will have its own percentage to charge for
					an auction based upon the final selling price.  Starting with 0
					create a bracketing system and charge differently for each bracket you create.  Once you start a new bracket you must
					finish it or there will be \"holes\" where a final fee could not be charged at the end of the auction.";
			$this->body .= "<tr  class=row_color_black>\n\t<td class=medium_font_light>start of price range</td>\n\t
				<td>&nbsp;</td>\n\t
				<td class=medium_font_light>end of price range</font></td>\n\t
				<td class=medium_font_light>percentage charge at auction end</font></td>\n";
			//$this->body .= "<td align=center class=medium_font_light>\n\tDelete?</td>\n";
			$this->body .= "</tr>\n";
			$this->row_count = 0;
			while ($show = $result->FetchNextObject())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t
					<td class=medium_font>\n\t".$show->LOW." ";
				$this->body .= "\n\t</td>\n\t
					<td class=medium_font>\n\tto</font>\n\t</td>\n\t
					<td class=medium_font>\n\t";
				if ($show->HIGH == 100000000)
					$this->body .= "and up";
				else
					$this->body .= $show->HIGH."\n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t".$show->CHARGE." %\n\t</td>\n";
				//$this->body .= "<td align=center>\n\t<a href=\"index.php?a=37&b=8&e=".$price_plan_id."&f=".($this->row_count+1)."\"><img src=\"admin_images/btn_admin_delete.gif\" border=0></a>";
				$this->body .= "</tr>\n";
				$this->row_count++;
			} //end of while
			$this->body .= "</table>\n";
		}
		else
		{
			//there are no brackets to display
			$this->body .= "<table cellpadding=3 cellspacing=1 border=0>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100%  class=row_color1>\n";
			$this->title .= "Pricing > Price Plans > Edit Final Fee % Brackets";
			$this->description .= "You can charge different amounts at the end of your auctions based upon the final price of the items sold.  Set the
					brackets that will determine the final fee percentage charged.  Each bracket will have its own percentage to charge for
					an auction based upon the final selling price.  Starting with 0
					create a bracketing system and charge differently for each bracket you create.  Once you start a new bracket you must
					finish it or there will be \"holes\" where a final fee could not be charged at the end of the auction.";
			$this->body .= "<tr>\n\t<td align=center>\nYou currently have no brackets set up</td>\n\t</tr>\n";
			$this->body .= "</table>\n";
		}

		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=8&e=".$price_plan_id." method=post>\n";

		// Inserting new brackets form
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=100%  class=row_color2>\n";
		$this->body .= "<tr  class=row_color_red>\n\t<td colspan=4 class=very_large_font_light>\n\tFinal Fee % Brackets (Insert)\n\t</td>\n</tr>\n";
		//$this->body .= "<tr  class=row_color_red>\n\t<td colspan=4 class=medium_font_light>Use this form to change the
		//	current price based increment brackets.  Once you start to enter a new set of increment brackets the old ones will be deleted.
		//	You must then complete the complete the new brackets before you quit or there will be no charge for some values of price.\n\t</td>\n</tr>\n";

		$this->body .= "<tr  class=row_color_black>\n\t<td class=medium_font_light>start of bracket</td>\n\t
			<td class=medium_font_light>to</font>\n\t</td>\n\t
			<td class=medium_font_light>end of bracket</td>\n\t<td class=medium_font_light>percentage charge</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td class=medium_font>\n\t";
		$this->body .= "<input type=text size=5 name=d[new_low] value=\"".($this->last_high_variable + .01)."\"></td>\n\t";
		$this->body .= "<td class=medium_font>to</td>\n\t";
		$this->body .= "<td class=medium_font><input type=text size=5 name=d[new_high]>\n\t\t<input type=checkbox name=d[and_up] value=1> check for \"and up\"";

		$this->body .= "\n\t</td>\n\t";
		$this->body .= "<td>\n\t<input type=text size=4 name=d[new_increment] value=\"0\">%\n\t</td>\n</tr>\n";
		if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td align=center colspan=4>\n\t<input type=submit value=\"save increment\">\n\t</td>\n</tr>\n";

		// If redirected from price plan give back link
		if($_REQUEST["price_plan"])
			$this->body .= "<tr><td align=center colspan=4><a href=index.php?a=37&b=3&f=3&g=".$_REQUEST["price_plan"].">Return to Price Plan Cost Specifics</a></td></tr>\n\t";

		$this->body .= "</table>\n";
		$this->body .= $this->hidden_bracket_variables;
		$this->body .= "</form>\n";

		$this->hidden_bracket_variables = "";
		return true;
	} //end of function increments_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_final_fee_increments($db,$new_info=0,$info=0,$price_plan_id=0)
	{
		//echo $info." is the entered variables<br>\n";
		//echo count($info)." is the count of entered variable<Br>\n";
		if ($price_plan_id)
		{
			$this->function_name = "update_increments";

			$sql_query = "delete from geodesic_auctions_final_fee_price_increments where price_plan_id = ".$price_plan_id;
			$result = $db->Execute($sql_query);
			if($this->configuration_data->DEBUG_ADMIN)
			{
				$this->debug_display($db, $this->filename, $this->function_name, "final_fee_table", "delete final fee data");
			}
			if (!$result)
			{
				$this->database_error($db->ErrorMsg(),$sql_query);
				return false;
			}

			$this->hidden_bracket_variables = "";

			if ((is_array($info)) && ($info != 0))
			{
				while (list($key,$value) = each($info))
				{
					$sql_query = "insert into geodesic_auctions_final_fee_price_increments
						(price_plan_id,low,high,charge)
						values
						(".$price_plan_id.",".$info[$key]["lower"].",".$info[$key]["higher"].",".$info[$key]["increment"].")";
					if ($new_info["and_up"] != 1)
					{
						$this->hidden_bracket_variables .= "<input type=hidden name=c[".$key."][lower] value=".$info[$key]["lower"].">\n\t";
						$this->hidden_bracket_variables .= "<input type=hidden name=c[".$key."][higher] value=".$info[$key]["higher"].">\n\t";
						$this->hidden_bracket_variables .= "<input type=hidden name=c[".$key."][increment] value=".$info[$key]["increment"].">\n\t";
					}
					$result = $db->Execute($sql_query);
					if($this->configuration_data->DEBUG_ADMIN)
					{
						$this->debug_display($db, $this->filename, $this->function_name, "final_fee_table", "insert new final fee data");
					}
					if (!$result)
					{
						$this->database_error($db->ErrorMsg(),$sql_query);
						return false;
					}
				} //end of while
			}
			//echo $new_info["and_up"]." is and_up<br>\n";
			if ((is_array($new_info)) && ($new_info["and_up"] != 1))
			{
				$sql_query = "insert into geodesic_auctions_final_fee_price_increments
					(price_plan_id,low,high,charge)
					values
					(".$price_plan_id.",".$new_info["new_low"].",".$new_info["new_high"].",".$new_info["new_increment"].")";
				$result = $db->Execute($sql_query);
				if($this->configuration_data->DEBUG_ADMIN)
				{
					$this->debug_display($db, $this->filename, $this->function_name, "final_fee_table", "insert new final fee data");
				}
				if (!$result)
				{
					return false;
				}
				if ($new_info["and_up"] != 1)
				{
					$this->last_high_variable = $new_info["new_high"];
					if ($info == 0)
						$new_key = 0;
					else
						$new_key = count($info);
					$this->hidden_bracket_variables .= "<input type=hidden name=c[".$new_key."][lower] value=".$new_info["new_low"].">\n\t";
					$this->hidden_bracket_variables .= "<input type=hidden name=c[".$new_key."][higher] value=".$new_info["new_high"].">\n\t";
					$this->hidden_bracket_variables .= "<input type=hidden name=c[".$new_key."][increment] value=".$new_info["new_increment"].">\n\t";
				}
				else
					$this->last_high_variable = 0;

			}
			elseif ($new_info["and_up"] == 1)
			{
				$sql_query = "insert into geodesic_auctions_final_fee_price_increments
					(price_plan_id,low,high,charge)
					values
					(".$price_plan_id.",".$new_info["new_low"].",100000000,".$new_info["new_increment"].")";
				$result = $db->Execute($sql_query);
				if($this->configuration_data->DEBUG_ADMIN)
				{
					$this->debug_display($db, $this->filename, $this->function_name, "final_fee_table", "insert new final fee data");
				}
				if (!$result)
				{
					return false;
				}
				$this->last_high_variable = 0;
			}
			return true;
		}
		else
			return false;

	} //end of function update_increments

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_increments($db,$new_info=0,$info=0,$price_plan_id=0,$category_id=0)
	{
		//echo $info." is the entered variables<br>\n";
		//echo count($info)." is the count of entered variable<Br>\n";
		if ($price_plan_id)
		{
			$sql_query = "delete from ".$this->price_plans_increments_table." where price_plan_id = ".$price_plan_id." and category_id=".$category_id;
			$result = $db->Execute($sql_query);
			//echo $sql_query." is the fonts_and_colors query<br>\n";
			if (!$result)
			{
				$this->database_error($db->ErrorMsg(),$sql_query);
				return false;
			}

			$this->hidden_bracket_variables = "";

			if ((is_array($info)) && ($info != 0))
			{
				while (list($key,$value) = each($info))
				{
					$sql_query = "insert into ".$this->price_plans_increments_table."
						(price_plan_id,category_id,low,high,charge,renewal_charge)
						values
						(".$price_plan_id.",".$category_id.",".$info[$key]["lower"].",".$info[$key]["higher"].",".$info[$key]["increment"].",".$info[$key]["renewal_charge"].")";
					if ($new_info["new_high"] != 100000000)
					{
						$this->hidden_bracket_variables .= "<input type=hidden name=c[".$key."][lower] value=".$info[$key]["lower"].">\n\t";
						$this->hidden_bracket_variables .= "<input type=hidden name=c[".$key."][higher] value=".$info[$key]["higher"].">\n\t";
						$this->hidden_bracket_variables .= "<input type=hidden name=c[".$key."][increment] value=".$info[$key]["increment"].">\n\t";
						$this->hidden_bracket_variables .= "<input type=hidden name=c[".$key."][renewal_charge] value=".$info[$key]["renewal_charge"].">\n\t";
					}
					$result = $db->Execute($sql_query);
					//echo $sql_query." is the entered variables query<br>\n";
					if (!$result)
					{
						//echo $sql_query." is the entered variables query<br>\n";
						return false;
					}
				} //end of while
			}

			if (is_array($new_info))
			{
				$sql_query = "insert into ".$this->price_plans_increments_table."
					(price_plan_id,category_id,low,high,charge,renewal_charge)
					values
					(".$price_plan_id.",".$category_id.",".$new_info["new_low"].",".$new_info["new_high"].",".$new_info["new_increment"].",".$new_info["new_renewal_charge"].")";
				$result = $db->Execute($sql_query);
				//echo $sql_query." is the new entries query<br>\n";
				if (!$result)
				{
					return false;
				}
				if ($new_info["new_high"] != 100000000)
				{
					$this->last_high_variable = $new_info["new_high"];
					if ($info == 0)
						$new_key = 0;
					else
						$new_key = count($info);
					$this->hidden_bracket_variables .= "<input type=hidden name=c[".$new_key."][lower] value=".$new_info["new_low"].">\n\t";
					$this->hidden_bracket_variables .= "<input type=hidden name=c[".$new_key."][higher] value=".$new_info["new_high"].">\n\t";
					$this->hidden_bracket_variables .= "<input type=hidden name=c[".$new_key."][increment] value=".$new_info["new_increment"].">\n\t";
					$this->hidden_bracket_variables .= "<input type=hidden name=c[".$new_key."][renewal_charge] value=".$new_info["new_renewal_charge"].">\n\t";
				}
				else
					$this->last_high_variable = 0;

			}
			return true;
		}
		else
			return false;

	} //end of function update_increments

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function lengths_form($db,$price_plan_id=0,$category_id=0)
	{
		if ($price_plan_id)
		{
			if (!$category_id)
			{
				$category_id = 0;
				$category_name = "all categories (default)";
			}
			else
				$category_name = $this->get_category_name($db,$category_id);

			$name = $this->get_price_plan_name($db,$price_plan_id);
			$sql_query = "select * from ".$this->classified_price_plan_lengths_table." where price_plan_id = ".$price_plan_id." and category_id = ".$category_id." order by length_of_ad asc";
			$length_result = $db->Execute($sql_query);
			if (!$length_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=7&c=".$price_plan_id."&e=".$category_id." method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center class=row_color1>\n";
			$this->title = "Pricing > Price Plans > Category Specific Pricing > Listing Length Choices";
			//$this->body .= "<tr class=row_color_red>\n\t<td colspan=5 class=large_font_light>\n\t<b>Price Plan: ".$name." <br>Category: ".$category_name."</b> \n\t</td>\n</tr>\n";
			$this->description = "Control the choices your users have for the length of days their
				listings are displayed in this form.  This only affects users within this price plan.  Delete the values you do not want by clicking the delete link next to them.
				Add a value by using the short form at the bottom and clicking \"add value\".  The values will always appear in numerical order.";
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=5 class=large_font_light align=center>\n\t<b>Price Plan: ".$name."</b>\n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color_red>\n\t<td colspan=5 class=large_font_light align=center>\n\t
				<span class=large_font_light> Category: ".$category_name."</span>\n\t</td>\n</tr>\n";
			$this->body .= "<tr bgcolor=000066>\n\t<td align=center class=medium_font_light>\n\t<b>Length of Listing (displayed)</b>\n\t</td>\n\t";
			$this->body .= "<td align=center class=medium_font_light>\n\t<b>Length of Listing (# of days)</b>\n\t</td>\n\t";
			$this->body .= "<td align=center class=medium_font_light>\n\t<b>Listing fee assessed</b>\n\t</td>\n\t";
			$this->body .= "<td align=center class=medium_font_light>\n\t<b>Renewal fee assessed</b>\n\t</td>\n\t";
			$this->body .= "<td align=center class=medium_font_light\n\t&nbsp; \n\t</td>\n</tr>\n";
			$this->row_count = 0;
			while ($show_lengths = $length_result->FetchRow())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t".$show_lengths["display_length_of_ad"]." \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t".$show_lengths["length_of_ad"]." \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t".$show_lengths["length_charge"]." \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t".$show_lengths["renewal_charge"]." \n\t</td>\n\t";
				$this->body .= "<td align=center>\n\t<a href=index.php?a=37&b=7&c=".$price_plan_id."&x=".$show_lengths["length_id"]."><span class=medium_font>\n\tdelete</span></a>\n\t</td>\n</tr>\n";
				$this->row_count++;
			}
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td>\n\t<input type=text name=d[display_length_of_ad]>\n\t</td>\n\t";
			$this->body .= "<td  class=medium_font>\n\t<input type=text name=d[length_of_ad]> \n\t</td>\n\t";
			$this->body .= "<td  class=medium_font>\n\t<select name=d[length_charge]>";
				for ($i = 0; $i <= 100;$i = $i + .25)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
				for ($i = 101; $i < 500;$i++)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
				for ($i = 501; $i < 10000;$i = $i + 5)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
			$this->body .= "</select> \n\t</td>\n\t";
			$this->body .= "<td  class=medium_font>\n\t<select name=d[renewal_charge]>";
				for ($i = 0; $i <= 100;$i = $i + .25)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
				for ($i = 101; $i < 500;$i++)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
				for ($i = 501; $i < 10000;$i = $i + 5)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
			$this->body .= "</select> \n\t</td>\n\t";
			if (!$this->admin_demo()) $this->body .= "<td >\n\t<input type=submit name=submit value=\"Save\">\n\t</td>\n";
			$this->body .= "</tr>\n";
			$this->body .= "<tr>\n\t<td colspan=5>\n\t<a href=index.php?a=37&b=5&x=".$price_plan_id."&d=".$show_parent_id["parent_id"]." class=medium_font>\n\t
				<b>back to Category Specific Pricing</b></a>\n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td colspan=5>\n\t<a href=index.php?a=37&b=3&g=".$price_plan_id." class=medium_font>\n\t
				<b>back to ".$name." Details</b></a>\n\t</td>\n</tr>\n";
			$this->body .= "<tr>\n\t<td colspan=5>\n\t<a href=index.php?a=37 class=medium_font>\n\t
					<b>back to Price Plan Home</b> </a>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}
		else
		{
			return false;
		}

	} //end of function lengths_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function add_category_specific_length($db,$price_plan_id=0,$new_length_info=0,$category_id=0)
	{
		//echo $price_plan_id." is price_plan_id<br>\n";
		//echo $new_length_info." is new_length_info<br>\n";
		//echo $category_id." is category_id<br>\n";
		if (!$category_id)
			$category_id = 0;
		if (($new_length_info) && ($price_plan_id))
		{
			//check length_of_ad to see if int
			//check length_charge to see if double or int
			if (ereg("[0-9]+", $new_length_info["length_of_ad"]))
			{
				$this->sql_query = "select * from  ".$this->classified_price_plan_lengths_table."
					where length_of_ad = ".$new_length_info["length_of_ad"]." and price_plan_id = ".$price_plan_id." and category_id = ".$category_id;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<Br>";
				if (!$result)
				{
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($result->RecordCount() == 0 )
				{
					$this->sql_query = "insert into ".$this->classified_price_plan_lengths_table."
						(price_plan_id,category_id,length_of_ad,display_length_of_ad,length_charge,renewal_charge)
						values
						(".$price_plan_id.",".$category_id.",".$new_length_info["length_of_ad"].",\"".$new_length_info["display_length_of_ad"]."\",".$new_length_info["length_charge"].",".$new_length_info["renewal_charge"].")";
					$insert_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<Br>";
					if (!$insert_result)
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
				$this->ad_configuration_message = "Please only enter numbers";
				return true;
			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function add_length

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function category_specific_delete_length($db,$length_id=0)
	{
		if ($length_id)
		{
			$this->sql_query = "delete from  ".$this->classified_price_plan_lengths_table." where length_id = ".$length_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<Br>";
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
	} //end of function category_specific_delete_length

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function category_specific_lengths_form($db,$price_plan_id=0,$category_id=0)
	{
		if ($price_plan_id)
		{
			if (!$category_id)
			{
				$category_id = 0;
				$category_name = "all categories (default)";
			}
			else
				$category_name = $this->get_category_name($db,$category_id);

			$name = $this->get_price_plan_name($db,$price_plan_id);
			$sql_query = "select * from ".$this->classified_price_plan_lengths_table." where price_plan_id = ".$price_plan_id." and category_id = ".$category_id." order by length_of_ad asc";
			$length_result = $db->Execute($sql_query);
			if (!$length_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=7&c=".$price_plan_id."&e=".$category_id." method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=5 class=very_large_font_light>\n\t<b>Length of Listing within ".$name." Price Plan</b> \n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=5 class=large_font_light>\n\t<b>Price Plan: ".$name." <br>Category: ".$category_name."</b> \n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=5 class=medium_font_light>\n\tControl the choices your users have for the length of days their
				listings are displayed in this form.  This only affects users within this price plan.  Delete the values you do not want by clicking the delete link next to them.
				Add a value by using the short form at the bottom and clicking \"add value\".  The values will always appear in numerical order.
				 \n\t</td>\n</tr>\n";

			$this->body .= "<tr>\n\t<td align=center class=medium_font>\n\tLength of Listing (displayed) \n\t</td>\n\t";
			$this->body .= "<td align=center class=medium_font>\n\tLength of Listing (# of days) \n\t</td>\n\t";
			$this->body .= "<td align=center class=medium_font>\n\tCharge \n\t</td>\n\t";
			$this->body .= "<td align=center class=medium_font>\n\tRenewal Charge \n\t</td>\n\t";
			$this->body .= "<td align=center class=medium_font>\n\t&nbsp; \n\t</td>\n</tr>\n";
			$this->row_count = 0;
			while ($show_lengths = $length_result->FetchRow())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t".$show_lengths["display_length_of_ad"]." \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t".$show_lengths["length_of_ad"]." \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t".$show_lengths["length_charge"]." \n\t</td>\n\t";
				$this->body .= "<td class=medium_font>\n\t".$show_lengths["renewal_charge"]." \n\t</td>\n\t";
				$this->body .= "<td >\n\t<a href=index.php?a=37&b=7&c=".$price_plan_id."&x=".$show_lengths["length_id"]."><span class=medium_font>\n\tdelete</span></a>\n\t</td>\n</tr>\n";
				$this->row_count++;
			}
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td>\n\t<input type=text name=d[display_length_of_ad]>\n\t</td>\n\t";
			$this->body .= "<td  class=medium_font>\n\t<input type=text name=d[length_of_ad]> \n\t</td>\n\t";
			$this->body .= "<td  class=medium_font>\n\t<select name=d[length_charge]>";
				for ($i = 0; $i <= 100;$i = $i + .25)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
				for ($i = 101; $i < 500;$i++)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
				for ($i = 501; $i < 10000;$i = $i + 5)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
			$this->body .= "</select> \n\t</td>\n\t";
			$this->body .= "<td  class=medium_font>\n\t<select name=d[renewal_charge]>";
				for ($i = 0; $i <= 100;$i = $i + .25)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
				for ($i = 101; $i < 500;$i++)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
				for ($i = 501; $i < 10000;$i = $i + 5)
				{
					$this->body .= "<option>".$i."</option>\n\t\t";
				}
			$this->body .= "</select> \n\t</td>\n\t";
			if (!$this->admin_demo())$this->body .= "<td >\n\t<input type=submit name=submit value=\"Save\">\n\t</td>\n";
			$this->body .= "</tr>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=5>\n\t<a href=index.php?a=37&b=5&x=".$price_plan_id."&d=".$show_parent_id["parent_id"]." class=medium_font_light>\n\t
				back to <b>".$category_name."</b> - <b>".$price_plan_name."</b> price plan home </a>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=5>\n\t<a href=index.php?a=37&b=3&g=".$price_plan_id." class=medium_font_light>\n\t
				back to <b>".$price_plan_name."</b> price plan home </a>\n\t</td>\n</tr>\n";
			$this->body .= "<tr class=row_color_red>\n\t<td colspan=5>\n\t<a href=index.php?a=37 class=medium_font_light>\n\t
					price plan list </a>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}
		else
		{
			return false;
		}

	} //end of function category_specific_lengths_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_discount_code_list($db)
	{
		$this->sql_query = "select * from ".$this->classified_discount_codes_table." order by name";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
		else
		{
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Pricing > Discount Codes";
			$this->description = "Create and edit discount codes that work for your listing placement
				procedures through this admin tool.  These codes will allow you to give percentage discounts on services at your site.  These codes can
				be given to clients and they can then enter the discount codes into the transaction details page and the discounts
				appearing on the transaction approval page.  <BR>  If a user id is attached to a discount code a credit will be
				deducted from that users credits and applied to the clients listing cost (the client who uses the attached discount code
				within the listing process).  The regular discount percentage will then apply to the clients costs of extra features
				above the cost of the listing.";
			if ($result->RecordCount() > 0)
			{
				$this->body .= "<tr>\n\t<td align=center>\n\t<table cellpadding=3 cellspacing=1 border=0 class=row_color2 width=100%>\n";
				$this->body .= "<tr bgcolor=000066>\n\t\t<td class=medium_font_light align=center colspan=8>\n\t<b>Current Discount Codes</b>\n\t\t</td></tr>\n\t\t";
				$this->body .= "<tr class=row_color_red>\n\t\t<td class=medium_font_light>\n\t<b>name</b>\n\t\t</td>\n\t\t";
				$this->body .= "<td class=medium_font_light>\n\t<b>code</b>\n\t\t</td>\n\t\t";
				$this->body .= "<td class=medium_font_light>\n\t<b>listings using</b> \n\t\t</td>\n\t\t";
				$this->body .= "<td class=medium_font_light align=center>\n\t<b>active?</b>\n\t\t</td>\n\t\t";
				$this->body .= "<td class=medium_font_light align=center>\n\t<b>user id?</b>\n\t\t</td>\n\t\t";
				$this->body .= "<td class=medium_font_light align=center>\n\t\n\t\t</td>\n\t\t";
				$this->body .= "<td class=medium_font_light align=center>\n\t\n\t\t</td>\n\t\t";
				$this->body .= "<td class=medium_font_light align=center>\n\t\n\t\t</td>\n\t</tr>\n\t";

				$this->row_count = 0;
				while ($show = $result->FetchRow())
				{
					$this->body .= "<tr class=".$this->get_row_color().">\n\t\t<td class=medium_font>\n\t<b>".urldecode($show["name"])."</b> <br>";
					$this->body .= "<span class=small_font>".urldecode($show["description"])."</span></td>\n\t";
					$this->body .= "<td class=medium_font>\n\t".urldecode($show["discount_code"])."</td>\n\t";
					$this->sql_query = "select count(*) as total_ads from ".$this->classifieds_table."
						where live = 1 and discount_id = ".$show["discount_id"];
					$count_result = $db->Execute($this->sql_query);
					if (!$count_result)
					{
						$this->error_message = $this->internal_error_message;
						return false;
					}
					elseif ($count_result->RecordCount() == 1)
					{
						$show_count = $count_result->FetchRow();
					}
					$this->body .= "<td class=medium_font align=center>\n\t".$show_count["total_ads"]."</td>\n\t";
					$this->body .= "<td class=medium_font align=center>\n\t";
					if ($show["active"] == 1)
						$this->body .= "active";
					else
						$this->body .= "inactive";
					$this->body .= " </td>\n\t";
					$this->body .= "<td class=medium_font align=center>\n\t";
					if ($show["user_id"])
						$this->body .= $show["user_id"];
					else
						$this->body .= "none";
					$this->body .= " </td>\n\t";
					$this->body .= "<td align=center>\n\t\t<a href=index.php?a=75&b=2&c=".$show["discount_id"]."><span class=medium_font>\n\t
						<img src=admin_images/btn_admin_edit.gif alt=edit border=0></a></span></td>\n\t";
					$this->body .= "<td align=center>\n\t\t<a href=index.php?a=75&b=3&c=".$show["discount_id"]."><span class=medium_font>\n\t
						<img src=admin_images/btn_admin_delete.gif alt=delete border=0></a></span></td>\n\t";
					$this->body .= "<td align=center>\n\t\t<a href=index.php?a=75&b=4&c=".$show["discount_id"]."><span class=medium_font>\n\t
						<img src=admin_images/btn_admin_view.gif alt=view border=0></a></span></td>\n\t";
					$this->body .= "</tr>\n\t";
					$this->row_count++;
				}
				$this->body .= "</table>\n";
				$this->body .= "</td>\n</tr>\n";
			}


			$this->body .= "<tr>\n\t<td align=center>\n\t<a href=index.php?a=75&b=1><span class=medium_font>\n\t<b>add new Discount Code</b></span></a>\n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
			$this->body .= "</form>\n";
			return true;
		}

	} //end of function display_discount_code_list

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_discount_code($db,$discount_code_info=0)
	{
		if ($discount_code_info)
		{
			if ((strlen(trim($discount_code_info["name"])) > 0) &&
				(strlen(trim($discount_code_info["discount_code"])) > 0) &&
				($discount_code_info["discount_percentage"] > 0))
			{
				if (!$discount_code_info["user_id"])
					$discount_code_info["user_id"] = 0;
				$this->sql_query = "insert into ".$this->classified_discount_codes_table."
					(name,description,discount_email,discount_code,discount_percentage,active,user_id)
					values
					(\"".urlencode($discount_code_info["name"])."\",\"".urlencode($discount_code_info["description"])."\",
					\"".urlencode($discount_code_info["discount_email"])."\",
					\"".urlencode($discount_code_info["discount_code"])."\",\"".$discount_code_info["discount_percentage"]."\",
					\"".$discount_code_info["active"]."\",".$discount_code_info["user_id"].")";
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>";
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
				return true;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function insert_discount_code

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_discount_code($db,$discount_id=0,$discount_code_info=0)
	{
		if (($discount_id) && ($discount_code_info))
		{
			if ((strlen(trim($discount_code_info["name"])) > 0) &&
				(strlen(trim($discount_code_info["discount_code"])) > 0) &&
				($discount_code_info["discount_percentage"] > 0))
			{
				$this->sql_query = "update ".$this->classified_discount_codes_table." set
					name = \"".urlencode($discount_code_info["name"])."\",
					description = \"".urlencode($discount_code_info["description"])."\",
					discount_code = \"".urlencode($discount_code_info["discount_code"])."\",
					discount_email = \"".urlencode($discount_code_info["discount_email"])."\",
					active = \"".$discount_code_info["active"]."\",
					user_id = \"".$discount_code_info["user_id"]."\",
					discount_percentage = \"".$discount_code_info["discount_percentage"]."\"
					where discount_id = ".$discount_id;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>";
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
				return true;
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_discount_code

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_discount_code($db,$discount_id=0)
	{
		if ($discount_id)
		{
			$this->sql_query = "delete from ".$this->classified_discount_codes_table."
				where discount_id = ".$discount_id;
			//echo $this->sql_query."<br>\n";
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
	} //end of function delete_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function edit_discount_code($db,$discount_id=0)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"discount code email\", \"If you enter an email into this field it will be used within the Authorize.net AIM payment gateway as the email address that will receive the email receipt sent by Authorize.net to the buyer.\"]\n
			Text[2] = [\"discount code\", \"Enter an alphanumeric code that the seller can enter while listing an item.\"]\n
			Text[3] = [\"user id to deduct credits from\", \"If this is left blank, no credit deductions will take place.\"]\n";

		//".$this->show_tooltip(1,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		if ($discount_id)
		{
			//edit this price plan form
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=75&b=2&c=".$discount_id." method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Classified Discount Code Management > Edit Discount Code";
			$this->description = "Edit a discount code details through this admin tool.  Make your changes then click the \"save\" button at the bottom.";
			$this->sql_query = "select * from ".$this->classified_discount_codes_table." where discount_id = ".$discount_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show_discount = $result->FetchRow();
			}
			else
			{
				return false;
			}
		}
		else
		{
			//insert new discount code form
			if (!$this->admin_demo())$this->body .= "<form action=index.php?a=75&b=1 method=post>\n";
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
			$this->title = "Pricing > Discount Codes > New Discount Code";
			$this->description = "Create a new discount code
				through this admin tool. As the implies,\"discount code\" are alphanumeric codes that the seller can enter while listing an item, to receive a percentage discount off of the total cost of that listing.  They are not Price Plan specific, and
				the discount code field will not display on the Final Transaction page of the \"Listing\" process, unless there you have entered at least one discount code into the system. (See User Manual for more details)";
		}


		$this->body .= "<tr>\n\t<td align=right width=30% class=medium_font>\n\tdiscount code name: \n\t</td>\n\t";
		$this->body .= "<td width=70%><input type=text name=d[name] size=30 maxsize=30 value=\"".urldecode($show_discount["name"])."\">\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td align=right valign=top class=medium_font>\n\tdiscount code description: \n\t</td>\n\t";
		$this->body .= "<td><textarea name=d[description] rows=3 cols=30>".$this->special_chars(urldecode($show_discount["description"]))."</textarea>\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td align=right width=30% class=medium_font>\n\tdiscount code email:".$this->show_tooltip(1,1)."</td>\n\t";
		$this->body .= "<td width=70%><input type=text name=d[discount_email] size=30 maxsize=30 value=\"".urldecode($show_discount["discount_email"])."\">\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td align=right width=30% class=medium_font>\n\tdiscount code:".$this->show_tooltip(2,1)."</td>\n\t";
		$this->body .= "<td width=70%><input type=text name=d[discount_code] size=30 maxsize=30 value=\"".urldecode($show_discount["discount_code"])."\">\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td align=right width=30% class=medium_font>\n\tdiscount percentage: \n\t</td>\n\t";
		$this->body .= "<td width=70%><input type=text name=d[discount_percentage] size=7 maxsize=7 value=\"".$show_discount["discount_percentage"]."\">%\n\t</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td align=right width=30% class=medium_font>\n\tactive/in use: \n\t</td>\n\t";
		$this->body .= "<td width=70% class=medium_font>\n\t<input type=radio name=d[active] value=1 ";
		if ($show_discount["active"] == 1)
			$this->body .= "checked";
		$this->body .= "> active<br><input type=radio name=d[active] value=0 ";
		if ($show_discount["active"] == 0)
			$this->body .= "checked";
		$this->body .= "> inactive<br>\n\t</td>\n";
		$this->body .= "<tr>\n\t<td align=right width=30% class=medium_font>\n\tuser id to deduct credits from:".$this->show_tooltip(3,1)."</td>\n\t";
		$this->body .= "<td width=70%><input type=text name=d[user_id] size=7 maxsize=7 value=\"".$show_discount["user_id"]."\">\n\t</td>\n</tr>\n";
		$this->body .= "</tr>\n";

		if (!$this->admin_demo())
		{
			$this->body .= "<tr>\n\t<td colspan=2 align=center>\n\t<input type=submit name=save_discount value=\"";
			if ($discount_id)
				$this->body .= "save";
			else
				$this->body .= "insert new discount code";
			$this->body .= "\">\n\t</td>\n</tr>\n";
		}
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;
	} //end of function edit_discount_code

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_discount_code_ads($db,$discount_id=0,$page=0)
	{
		if ($discount_id)
		{
			//get discount code info
			$this->sql_query = "select * from ".$this->classified_discount_codes_table." where discount_id = ".$discount_id;
			$code_result = $db->Execute($this->sql_query);
			if (!$code_result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($code_result->RecordCount() == 1)
			{
				$show_code = $code_result->FetchRow();
			}
			else
			{
				return false;
			}

			//get total count of ads using discount
			$this->sql_query = "select count(*) as total_discounted_ads from ".$this->classifieds_table." where live = 1 and discount_id = ".$discount_id;
			$count_result = $db->Execute($this->sql_query);
			if (!$count_result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($count_result->RecordCount() == 1)
			{
				$show_total = $count_result->FetchRow();
			}
			else
			{
				return false;
			}

			//get sum of discounts given for all listings
			$this->sql_query = "select sum(discount_amount) as total_discounted_ads from ".$this->classifieds_table." where live = 1 and discount_id = ".$discount_id;
			$sum_result = $db->Execute($this->sql_query);
			if (!$sum_result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($sum_result->RecordCount() == 1)
			{
				$show_sum = $sum_result->FetchRow();
			}
			else
			{
				return false;
			}

			if ($show_total["total_discounted_ads"] > 25)
			{
				if ($page)
				{
					if ($page > 1)
					{
						$limit = ((($page - 1) * 25)+ 1);
						$limit_phrase = " limit ".($limit).",25 ";
						//$limit_phrase = $limit;
					}
					else
						$limit_phrase = " limit 0,25";
				}
				else
				{
					$page = 1;
					$limit_phrase = " limit 0,25";
				}

				//get current page sum of discounts
				$this->sql_query = "select sum(discount_amount) as total_discounted_ads from ".$this->classifieds_table." where live = 1 and discount_id = ".$discount_id;
				$sum_result = $db->Execute($this->sql_query);
				if (!$sum_result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}
				elseif ($sum_result->RecordCount() == 1)
				{
					$show_sum = $sum_result->FetchRow();
				}
				else
				{
					return false;
				}
			}

			$this->sql_query = "select *from ".$this->classifieds_table." where live = 1 and discount_id = ".$discount_id." order by date asc ".$limit_phrase;
			$ad_result = $db->Execute($this->sql_query);
			if (!$ad_result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($ad_result->RecordCount() > 0)
			{
				$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>\n";
				$this->title = "Pricing > Discount Codes > Show Listings for Discount Code";
				$this->body .= "<tr>\n\t<td colspan=100% class=medium_font>\n\t
					<b>discount code name:</b> ".urldecode($show_code["name"])."<br>
					<b>discount code description:</b> ".urldecode($show_code["description"])."<br>
					<b>discount code:</b> ".$show_code["discount_code"]."<br><br></td>\n</tr>\n";
				$this->body .= "<tr bgcolor=000066><td colspan=8 align=center class=large_font_light><b> Discount Code: ".$show_code["discount_code"]."</b></td>\n</tr> \n\t\t";
				$this->body .= "<tr class=row_color_black>\n\t\t";
				if($this->is_class_auctions())
				$this->body .= "<td class=medium_font_light><b>listing type </b></td>\n\t\t";
				$this->body .= "<td class=medium_font_light align=center><b>listing ID </b></td>\n\t\t";
				$this->body .= "<td class=medium_font_light><b>seller </b></td>\n\t\t";
				$this->body .= "<td class=medium_font_light><b>listing title </b></td>\n\t\t";
				$this->body .= "<td class=medium_font_light align=center><b>% discount </b></td>\n\t\t";
				$this->body .= "<td class=medium_font_light align=center><b>discount total </b></td>\n\t\t";
				$this->body .= "<td class=medium_font_light align=center><b>total </b></td>\n\t\t";
				$this->body .= "<td class=medium_font_light><b>date </b></td>\n\t</tr>\n\t";

				$this->row_count = 0;
				while($show_ads = $ad_result->FetchRow())
				{
					$user_data = $this->get_user_data($db,$show_ads["seller"]);
					$this->body .= "<tr class=".$this->get_row_color().">\n\t\t";
					if($this->is_class_auctions())
					{
						if($show_ads['item_type'] == 1)
							$this->body .= "<td class=medium_font>\n\tClassified Ad</td>\n\t\t";
						elseif($show_ads['item_type'] == 2)
							$this->body .= "<td class=medium_font>\n\tAuction</td>\n\t\t";
					}
					$this->body .= "<td class=medium_font align=center>\n\t".$show_ads["id"]."</td>\n\t\t";
					$this->body .= "<td class=medium_font>\n\t".$user_data["username"]."(".$show_ads["seller"].")</td>\n\t\t";
					$this->body .= "<td class=medium_font>".urldecode($show_ads["title"])."</td>\n\t\t";
					$this->body .= "<td class=medium_font align=center>\n\t".$show_ads["discount_percentage"]."</td>\n\t\t";
					$this->body .= "<td class=medium_font align=center>\n\t".$show_ads["discount_amount"]."</td>\n\t\t";
					$this->body .= "<td class=medium_font align=center>\n\t".$show_ads["total"]."</td>\n\t\t";
					$this->body .= "<td class=medium_font>\n\t".date("M j,Y",$show_ads["date"])."</td>\n\t</tr>\n\t";
					$this->row_count++;
				}
				$total_count = ($ad_result->RecordCount() - 1);
				//echo $total_count." is the total count<bR>\n";
				$counter = 1;
				$number_of_times = 0;
				if ($total_count > 25)
				{
					$this->body .= "<tr>\n\t<td colspan=7>\n\t";
					while ($number_of_times < 6)
					{
						$this->body .= "<a href=index.php?a=19&b[limit]=".$counter."&b[order_by]=".$limit_by."><span class=medium_font>\n\t".$counter."-".($counter + 25)."</span></a> | ";

						$counter = $counter + 25;
						$number_of_times++;
					}
					if ($number_of_times == 6)
					{
						$this->body .= "<a href=index.php?a=19&b[limit]=".($total_count - 25)."&b[order_by]=".$limit_by." class=medium_font>\n\t";
					}
					$this->body .= "</td>\n</tr>\n</table>\n";
				}
				$this->body .= "<tr>\n\t\t<td colspan=8 align=center><a href=index.php?a=75 class=medium_font>\n\t<br><br><b>back to Discount Code List</b> </a></td>\n\t</tr>\n\t";
				$this->body .= "</table>\n";
				return true;
			}
			else
			{
				$this->title .= "Pricing > Discount Codes > Listings using this Discount Code";
				$this->body .= "<span class=medium_font><b>There are no listings using this discount code.</b></span><bR>\n";
				return true;
			}

			return true;
		}
		else
		{
			return false;
		}
	} //end of function display_discount_code_ads

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function price_plan_registration_freebies_form($db,$price_plan_id=0)
	{
		if ($price_plan_id && !$this->is_class_auctions())
		{
			$price_plan_name = $this->get_price_plan_name($db,$price_plan_id);
			$this->get_configuration_data($db);

			if ($price_plan_name)
			{
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=37&b=3&f=4&g=".$price_plan_id." method=post>\n";
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center width=100% class=row_color1>\n";
				$this->title .= "Pricing > Price Plans > Edit Registration";
				$this->description = "Edit ".$price_plan_name."
					price plans detailed registration promotions through this form.  The options you see in this form are affected by the
					choice you made for the type of the <b>".$price_plan_name."</b> plan you made in the price plan type form (subscription or
					fee based).  Make sure you have chosen the option in that form that you want.  If not create a new price plan
					with the correct choice and discard this price plan (if you don't need it).  Make your changes then click the \"save\" button at the bottom.
					<br><b>Be mindful of the effects certain
					choices could have on people currently on this pricing plan</b>
					<br><b>Changes in expirations below will not affect credits already issued by your site.
					</b> \n\t</td>\n</tr>\n";
				$this->sql_query = "select * from ".$this->price_plan_table." where price_plan_id = ".$price_plan_id;
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					$this->error_message = $this->internal_error_message;
					return false;
				}
				elseif ($result->RecordCount() == 1)
				{
					$show_price_plan = $result->FetchRow();
				}
				else
				{
					return false;
				}

				if ($show_price_plan["type_of_billing"] == 1)
				{
					//fee based price plans
					//credits upon registration
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font colspan=2>\n\tcredits issued upon registration\n\t
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

					//credit expiration type
					$this->body .= "<tr class=".$this->get_row_color().">\n\t<td colspan=2 class=medium_font>\n\t<b>how do the credits expire</b>\n\t</td>\n</tr>\n";
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

					if($this->configuration_data["positive_balances_only"])
					{
						$this->body .= "<tr class=".$this->get_row_color().">\n\t<td colspan=2 class=medium_font>\n\tinitial site balance\n\t
							<select name=h[initial_site_balance] class=medium_font>\n\t\t";
						for ($i=0;$i < 1000;$i++)
						{
							$this->body .= "<option ";
							if ($show_price_plan["initial_site_balance"] == $i)
								$this->body .= "selected";
							$this->body .= ">".$i."</option>\n\t\t";
						}
						$this->body .= "</select></td>\n\t</tr>\n";
						$this->row_count++;
					}
				}
				elseif ($show_price_plan["type_of_billing"] == 2)
				{
					//subscription based price plans
					//free subscription period from registration
					$this->body .= "<tr>\n\t<td align=right class=medium_font>\n\tfree subscription period upon registration \n\t</td>\n\t";
					$this->body .= "<td>\n\t";
					$this->subscription_period_dropdown($db,$show_price_plan["free_subscription_period_upon_registration"],"h[free_subscription_period_upon_registration]");
					$this->body .= "</td>\n\t</tr>\n";
				}
				if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2>\n\t<input type=submit name=save_group value=\"Save\">\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td colspan=2>\n\t<a href=index.php?a=37&b=3&g=".$price_plan_id."><span class=medium_font>\n\t
					back to <b>".$price_plan_name."</b> price plan home</span></a>\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td colspan=2>\n\t<a href=index.php?a=37><span class=medium_font>\n\t
					price plan list</span></a>\n\t</td>\n</tr>\n";
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

	function update_price_plan_registration_freebies($db,$price_plan_id=0,$price_plan_info=0)
	{
		if (($price_plan_info) && ($price_plan_id) && (!$this->is_class_auctions()))
		{
			$this->sql_query = "select type_of_billing from ".$this->price_plan_table." where price_plan_id = ".$price_plan_id;
			$type_result = $db->Execute($this->sql_query);
			if (!$type_result)
			{
				$this->error_message = $this->internal_error_message;
				return false;
			}
			elseif ($type_result->RecordCount() == 1)
			{
				$show_type = $type_result->FetchRow();
				$this->sql_query = "update ".$this->price_plan_table." set ";
				if ($show_type["type_of_billing"] == 1)
				{
					$expiration_date = mktime(0,0,0,$price_plan_info["credits_expire_date"]["month"],$price_plan_info["credits_expire_date"]["day"],$price_plan_info["credits_expire_date"]["year"]);
					$this->sql_query .= "credits_upon_registration = \"".$price_plan_info["credits_upon_registration"]."\",
						credits_expire_type = \"".$price_plan_info["credits_expire_type"]."\",
						credits_expire_date = ".$expiration_date.",
						credits_expire_period = \"".$price_plan_info["credits_expire_period"]."\",
						initial_site_balance = \"".$price_plan_info["initial_site_balance"]."\"";
				}
				elseif ($show_type["type_of_billing"] == 2)
				{
					//get how the credits expire
					$this->sql_query .= "free_subscription_period_upon_registration = \"".$price_plan_info["free_subscription_period_upon_registration"]."\"";
				}
				else
				{
					return false;
				}
				$this->sql_query .= " where price_plan_id = ".$price_plan_id;
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
				return false;
			}
		}
		else
		{
			$this->error_message = $this->internal_error_message;
			return false;
		}
	} //end of function update_price_plan_registration_freebies

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Price_plan_management
?>
